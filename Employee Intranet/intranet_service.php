<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR Employee Intranet - Core Database Service Controller (Class 4)
 * Handles enterprise personnel directory, announcements, leave workflows, internal policies, and IT dispatch.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/AuditLogger.php';
require_once __DIR__ . '/../includes/integration_bus.php';

function intra_jsonReply(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function intra_getCurrentUser(): array
{
    if (!empty($_SESSION['vostok_user'])) {
        return $_SESSION['vostok_user'];
    }
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->query("SELECT emp_id AS user_id, emp_id, full_name, job_title, clearance_level, department_code FROM employees WHERE employment_status = 'Active' ORDER BY emp_id ASC LIMIT 1");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) return $user;
    } catch (Throwable $e) {}
    return [
        'user_id' => '',
        'emp_id' => '',
        'full_name' => 'Staff Member',
        'job_title' => 'Specialist',
        'clearance_level' => 'L1',
        'department_code' => 'GEN'
    ];
}

// ============================================================================
// 1. DASHBOARD & COMMUNICATIONS
// ============================================================================

function intra_getDashboardMetrics(?string $empId = null, ?string $deptCode = null): array
{
    $pdo = getDbConnection();
    $user = intra_getCurrentUser();
    $empId = $empId ?: $user['emp_id'];
    $deptCode = $deptCode ?: ($user['department_code'] ?? 'ENG');

    // Total active employees
    $staffCount = (int)$pdo->query("SELECT COUNT(*) FROM employees WHERE employment_status = 'Active'")->fetchColumn();

    // Pending leaves using parameterized query
    $myPendingStmt = $pdo->prepare("SELECT COUNT(*) FROM leave_requests WHERE emp_id = ? AND status = 'Pending'");
    $myPendingStmt->execute([$empId]);
    $myPendingLeaves = (int)$myPendingStmt->fetchColumn();

    // All pending leaves (for managers/L3/L4)
    $allPendingLeaves = (int)$pdo->query("SELECT COUNT(*) FROM leave_requests WHERE status = 'Pending'")->fetchColumn();

    // Announcements
    $announcements = intra_getAnnouncements($deptCode, 5);

    // Upcoming approved leaves for coworkers in department
    $coworkerLeavesStmt = $pdo->prepare("
        SELECT lr.*, e.full_name, e.job_title
        FROM leave_requests lr
        JOIN employees e ON lr.emp_id = e.emp_id
        WHERE e.department_code = :dept AND lr.status = 'Approved' AND lr.end_date >= CURDATE()
        ORDER BY lr.start_date ASC
        LIMIT 5
    ");
    $coworkerLeavesStmt->execute([':dept' => $deptCode]);
    $coworkerLeaves = $coworkerLeavesStmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'user'               => $user,
        'staff_count'        => $staffCount,
        'my_pending_leaves'  => $myPendingLeaves,
        'all_pending_leaves' => $allPendingLeaves,
        'announcements'      => $announcements,
        'coworker_leaves'    => $coworkerLeaves
    ];
}

function intra_getAnnouncements(?string $deptCode = null, int $limit = 20): array
{
    $pdo = getDbConnection();
    $user = intra_getCurrentUser();
    $dept = $deptCode ?: ($user['department_code'] ?? 'ENG');

    $stmt = $pdo->prepare("
        SELECT 
            a.*,
            e.full_name AS posted_by_name,
            e.job_title AS posted_by_role,
            d.dept_name AS target_dept_name
        FROM announcements a
        JOIN employees e ON a.posted_by_emp_id = e.emp_id
        LEFT JOIN departments d ON a.audience_dept = d.dept_code
        WHERE a.audience_dept IS NULL OR a.audience_dept = :dept
        ORDER BY a.posted_at DESC
        LIMIT :lim
    ");
    $stmt->bindValue(':dept', $dept, PDO::PARAM_STR);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function intra_createAnnouncement(array $data, ?string $empId = null): int
{
    $pdo = getDbConnection();
    $user = intra_getCurrentUser();
    $author = $empId ?: $user['emp_id'];

    $title = trim($data['title'] ?? '');
    $body = trim($data['body'] ?? '');
    $dept = !empty($data['audience_dept']) ? trim($data['audience_dept']) : null;

    if (empty($title) || empty($body)) {
        throw new InvalidArgumentException("Title and announcement body are required.");
    }

    $stmt = $pdo->prepare("
        INSERT INTO announcements (title, body, posted_by_emp_id, audience_dept, posted_at)
        VALUES (:title, :body, :emp, :dept, NOW())
    ");
    $stmt->execute([
        ':title' => $title,
        ':body'  => $body,
        ':emp'   => $author,
        ':dept'  => $dept
    ]);

    $newId = (int)$pdo->lastInsertId();
    try {
        vp_emit($pdo, 'EMP_TO_ADM', 'EMP', 'ADM', 'ANNOUNCEMENT_PUBLISHED', [
            'announcement_id' => $newId,
            'title'           => $title,
            'summary'         => "Published announcement #{$newId} by {$author}",
            'endpoint'        => '/announcements/create'
        ], $author, 201);
    } catch (Throwable $e) {
        error_log("Failed to emit EMP_TO_ADM: " . $e->getMessage());
    }
    return $newId;
}

function intra_deleteAnnouncement(int $id): bool
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("DELETE FROM announcements WHERE announcement_id = ?");
    return $stmt->execute([$id]);
}

// ============================================================================
// 2. PERSONNEL DIRECTORY
// ============================================================================

function intra_getDirectory(array $filters = []): array
{
    $pdo = getDbConnection();
    $where = ["e.employment_status = 'Active'"];
    $params = [];

    if (!empty($filters['dept']) && $filters['dept'] !== 'ALL') {
        $where[] = "e.department_code = :dept";
        $params[':dept'] = $filters['dept'];
    }

    if (!empty($filters['clearance']) && $filters['clearance'] !== 'ALL') {
        $cMap = [
            'public'       => 'L1',
            'internal'     => 'L2',
            'confidential' => 'L3',
            'restricted'   => 'L4'
        ];
        $cVal = $cMap[strtolower($filters['clearance'])] ?? $filters['clearance'];
        $where[] = "e.clearance_level = :clr";
        $params[':clr'] = $cVal;
    }

    if (!empty($filters['search'])) {
        $where[] = "(e.full_name LIKE :s OR e.job_title LIKE :s OR e.email LIKE :s OR e.emp_id LIKE :s OR d.dept_name LIKE :s)";
        $params[':s'] = '%' . $filters['search'] . '%';
    }

    $whereSql = "WHERE " . implode(" AND ", $where);

    $sql = "
        SELECT 
            e.*,
            d.dept_name,
            m.full_name AS manager_name
        FROM employees e
        JOIN departments d ON e.department_code = d.dept_code
        LEFT JOIN employees m ON e.manager_emp_id = m.emp_id
        {$whereSql}
        ORDER BY d.dept_code ASC, e.clearance_level DESC, e.full_name ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function intra_getEmployee(string $empId): ?array
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("
        SELECT 
            e.*,
            d.dept_name,
            m.full_name AS manager_name
        FROM employees e
        JOIN departments d ON e.department_code = d.dept_code
        LEFT JOIN employees m ON e.manager_emp_id = m.emp_id
        WHERE e.emp_id = ?
    ");
    $stmt->execute([$empId]);
    $emp = $stmt->fetch(PDO::FETCH_ASSOC);
    return $emp ?: null;
}

function intra_getDepartments(): array
{
    $pdo = getDbConnection();
    return $pdo->query("SELECT * FROM departments ORDER BY dept_code ASC")->fetchAll(PDO::FETCH_ASSOC);
}

// ============================================================================
// 3. LEAVE REQUISITIONS WORKFLOW
// ============================================================================

function intra_getLeaveRequests(?string $empId = null, bool $isAdmin = false): array
{
    $pdo = getDbConnection();
    $where = [];
    $params = [];

    if (!$isAdmin && $empId !== null) {
        $where[] = "lr.emp_id = :eid";
        $params[':eid'] = $empId;
    }

    $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

    $sql = "
        SELECT 
            lr.*,
            e.full_name AS employee_name,
            e.department_code,
            e.job_title,
            ap.full_name AS approved_by_name
        FROM leave_requests lr
        JOIN employees e ON lr.emp_id = e.emp_id
        LEFT JOIN employees ap ON lr.approved_by_emp_id = ap.emp_id
        {$whereSql}
        ORDER BY lr.leave_id DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function intra_submitLeaveRequest(string $empId, array $data): int
{
    $pdo = getDbConnection();
    $type = trim($data['leave_type'] ?? 'Annual Leave');
    $start = trim($data['start_date'] ?? '');
    $end = trim($data['end_date'] ?? '');

    if (empty($start) || empty($end)) {
        throw new InvalidArgumentException("Start date and end date are required.");
    }
    if (strtotime($end) < strtotime($start)) {
        throw new InvalidArgumentException("End date cannot precede start date.");
    }

    $stmt = $pdo->prepare("
        INSERT INTO leave_requests (emp_id, leave_type, start_date, end_date, status)
        VALUES (:eid, :typ, :std, :end, 'Pending')
    ");
    $stmt->execute([
        ':eid' => $empId,
        ':typ' => $type,
        ':std' => $start,
        ':end' => $end
    ]);

    $newId = (int)$pdo->lastInsertId();
    try {
        vp_emit($pdo, 'EMP_TO_ADM', 'EMP', 'ADM', 'LEAVE_SUBMITTED', [
            'leave_id' => $newId,
            'emp_id'   => $empId,
            'summary'  => "Leave request #{$newId} submitted by {$empId}",
            'endpoint' => '/leaves/submit'
        ], $empId, 201);
    } catch (Throwable $e) {
        error_log("Failed to emit EMP_TO_ADM: " . $e->getMessage());
    }
    return $newId;
}

function intra_updateLeaveStatus(int $leaveId, string $decision, ?string $approverEmpId = null): bool
{
    $pdo = getDbConnection();
    $user = intra_getCurrentUser();
    $approver = $approverEmpId ?: $user['emp_id'];

    if (!in_array($decision, ['Approved', 'Rejected'], true)) {
        throw new InvalidArgumentException("Decision must be 'Approved' or 'Rejected'.");
    }

    $stmt = $pdo->prepare("
        UPDATE leave_requests 
        SET status = :dec, approved_by_emp_id = :appr
        WHERE leave_id = :lid
    ");
    $res = $stmt->execute([
        ':dec'  => $decision,
        ':appr' => $approver,
        ':lid'  => $leaveId
    ]);

    try {
        vp_emit($pdo, 'EMP_TO_ADM', 'EMP', 'ADM', 'LEAVE_DECIDED', [
            'leave_id' => $leaveId,
            'decision' => $decision,
            'summary'  => "Leave #{$leaveId} {$decision} by {$approver}",
            'endpoint' => '/leaves/decide'
        ], $approver, 200);
    } catch (Throwable $e) {
        error_log("Failed to emit EMP_TO_ADM: " . $e->getMessage());
    }
    return $res;
}

function intra_cancelLeaveRequest(int $leaveId, string $empId): bool
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("DELETE FROM leave_requests WHERE leave_id = :lid AND emp_id = :eid AND status = 'Pending'");
    return $stmt->execute([':lid' => $leaveId, ':eid' => $empId]);
}

// ============================================================================
// 4. POLICIES & FORMS
// ============================================================================

function intra_getPoliciesAndForms(?string $folder = null): array
{
    $pdo = getDbConnection();
    $where = [];
    $params = [];

    if ($folder !== null && $folder !== 'all') {
        $where[] = "d.folder = :fld";
        $params[':fld'] = $folder;
    }

    $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

    $sql = "
        SELECT 
            d.*,
            p.policy_id,
            p.effective_date
        FROM documents d
        LEFT JOIN internal_policies p ON d.doc_id = p.doc_id
        {$whereSql}
        ORDER BY d.created_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function intra_createQuickTicket(string $empId, string $title, string $description, string $priority = 'Medium'): string
{
    $pdo = getDbConnection();
    $user = intra_getEmployee($empId);

    $rand = rand(1000, 9999);
    $tktId = "TICK-INTRA-{$rand}";

    $slaHours = match ($priority) {
        'Critical' => 2,
        'High'     => 4,
        'Medium'   => 8,
        'Low'      => 24,
        default    => 8,
    };
    $slaDeadline = date('Y-m-d H:i:s', strtotime("+{$slaHours} hours"));

    $stmt = $pdo->prepare("
        INSERT INTO tickets 
        (tkt_id, requester_type, requester_emp_id, source_system, title, description, requester_name, requester_role, requester_dept, priority, status, sla_deadline, created_at)
        VALUES 
        (:tid, 'Employee', :eid, 'Employee Intranet Portal', :title, :desc, :rname, :rrole, :rdept, :prio, 'Open', :sla, NOW())
    ");
    $stmt->execute([
        ':tid'   => $tktId,
        ':eid'   => $empId,
        ':title' => trim($title),
        ':desc'  => trim($description),
        ':rname' => $user['full_name'] ?? 'Intranet Employee',
        ':rrole' => $user['job_title'] ?? 'Staff',
        ':rdept' => $user['department_code'] ?? 'ENG',
        ':prio'  => $priority,
        ':sla'   => $slaDeadline
    ]);

    try {
        vp_emit($pdo, 'EMP_TO_ADM', 'EMP', 'ADM', 'TICKET_DISPATCHED', [
            'tkt_id'   => $tktId,
            'emp_id'   => $empId,
            'summary'  => "Dispatched ticket {$tktId} for {$empId}",
            'endpoint' => '/tickets/create'
        ], $empId, 201);
    } catch (Throwable $e) {
        error_log("Failed to emit EMP_TO_ADM: " . $e->getMessage());
    }
    return $tktId;
}

// ============================================================================
// 5. AJAX ACTION ROUTER
// ============================================================================

$action = $_REQUEST['action'] ?? null;
if ($action !== null && (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_GET['action']) || isset($_POST['action']) || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json'))) {
    try {
        $u = intra_getCurrentUser();

        switch ($action) {
            case 'get_dashboard':
                intra_jsonReply(['success' => true, 'data' => intra_getDashboardMetrics($u['emp_id'], $u['department_code'])]);
                break;

            case 'get_announcements':
                intra_jsonReply(['success' => true, 'data' => intra_getAnnouncements($_GET['dept'] ?? null)]);
                break;

            case 'create_announcement':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $aid = intra_createAnnouncement($input, $u['emp_id']);
                intra_jsonReply(['success' => true, 'announcement_id' => $aid, 'message' => 'Announcement broadcasted']);
                break;

            case 'delete_announcement':
                $aid = (int)($_REQUEST['id'] ?? 0);
                intra_deleteAnnouncement($aid);
                intra_jsonReply(['success' => true, 'message' => 'Announcement removed']);
                break;

            case 'get_directory':
                intra_jsonReply(['success' => true, 'data' => intra_getDirectory($_GET)]);
                break;

            case 'get_employee':
                $eid = trim($_GET['id'] ?? $_GET['emp_id'] ?? '');
                $emp = intra_getEmployee($eid);
                if (!$emp) intra_jsonReply(['success' => false, 'error' => 'Employee not found'], 404);
                intra_jsonReply(['success' => true, 'data' => $emp]);
                break;

            case 'get_leaves':
                $isAdmin = in_array($u['clearance_level'], ['L3', 'L4']) || ($u['department_code'] === 'HRA');
                $viewAll = isset($_GET['all']) && $isAdmin;
                intra_jsonReply(['success' => true, 'data' => intra_getLeaveRequests($viewAll ? null : $u['emp_id'], $viewAll)]);
                break;

            case 'submit_leave':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $lid = intra_submitLeaveRequest($u['emp_id'], $input);
                intra_jsonReply(['success' => true, 'leave_id' => $lid, 'message' => 'Leave request logged with HR']);
                break;

            case 'decide_leave':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $lid = (int)($input['leave_id'] ?? 0);
                $dec = trim((string)($input['decision'] ?? 'Approved'));
                intra_updateLeaveStatus($lid, $dec, $u['emp_id']);
                intra_jsonReply(['success' => true, 'message' => "Leave #{$lid} status marked as {$dec}"]);
                break;

            case 'get_policies':
                intra_jsonReply(['success' => true, 'data' => intra_getPoliciesAndForms($_GET['folder'] ?? null)]);
                break;

            case 'get_ops_tasks':
                intra_jsonReply(['success' => true, 'data' => intra_getOpsTasks()]);
                break;

            case 'update_ops_task':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $taskId = trim((string)($input['task_id'] ?? ''));
                $taskStatus = trim((string)($input['status'] ?? 'Pending'));
                $assignedEmp = trim((string)($input['assigned_emp_id'] ?? ''));
                if ($taskId === '') {
                    intra_jsonReply(['success' => false, 'error' => 'Task ID is required'], 400);
                }
                intra_updateOpsTask($taskId, $taskStatus, $assignedEmp ?: null);
                intra_jsonReply(['success' => true, 'message' => "OPS Task {$taskId} updated to {$taskStatus}"]);
                break;

            case 'quick_ticket':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $tid = intra_createQuickTicket(
                    $u['emp_id'],
                    trim($input['title'] ?? 'Intranet Assistance Request'),
                    trim($input['description'] ?? ''),
                    trim($input['priority'] ?? 'Medium')
                );
                intra_jsonReply(['success' => true, 'tkt_id' => $tid, 'message' => 'Support request dispatched to IT Helpdesk']);
                break;
        }
    } catch (Throwable $e) {
        intra_jsonReply(['success' => false, 'error' => $e->getMessage()], 400);
    }
}

/**
 * OPS Fulfillment Queue (Flow D / Department Board)
 */
function intra_getOpsTasks(): array
{
    $pdo = getDbConnection();
    $stmt = $pdo->query("
        SELECT 
            ot.*,
            e.full_name AS assigned_emp_name
        FROM ops_tasks ot
        LEFT JOIN employees e ON ot.assigned_emp_id = e.emp_id
        ORDER BY 
            CASE ot.status 
                WHEN 'Pending' THEN 1 
                WHEN 'In Progress' THEN 2 
                WHEN 'Completed' THEN 3 
                ELSE 4 
            END ASC,
            ot.created_at DESC
    ");
    return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
}

function intra_updateOpsTask(string $taskId, string $status, ?string $assignedEmpId = null): bool
{
    $pdo = getDbConnection();
    $sql = "UPDATE ops_tasks SET status = :status";
    $params = [':status' => $status, ':id' => $taskId];
    if ($assignedEmpId !== null && $assignedEmpId !== '') {
        $sql .= ", assigned_emp_id = :assigned";
        $params[':assigned'] = $assignedEmpId;
    }
    $sql .= ", updated_at = NOW() WHERE task_id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}
