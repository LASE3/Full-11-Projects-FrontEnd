<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR Customer Portal - Core Database Service Controller (Class 3)
 * Handles customer profile context, project milestones, invoices & live payment reconciliation,
 * support tickets with threaded comments, and engineering documents.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/AuditLogger.php';
require_once __DIR__ . '/../includes/integration_bus.php';
require_once __DIR__ . '/../includes/enterprise_flows.php';

function cus_jsonReply(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

/**
 * Resolve active customer ID from session or query first customer from DB.
 */
function cus_getCurrentCustomerId(): string
{
    $pdo = getDbConnection();
    $user = $_SESSION['vostok_user'] ?? [];
    if (($user['account_type'] ?? '') === 'Customer') {
        $cid = (string)($user['cus_id'] ?? ($user['user_id'] ?? ''));
        $_SESSION['cus_id'] = $cid;
        return $cid;
    }

    // Employee
    if (!hasEmployeePermission($pdo, $user, 'CUSTOMER_IMPERSONATE')) {
        return '';
    }

    if (!empty($_GET['switch_cus_id']) || !empty($_POST['switch_cus_id'])) {
        $req = trim((string)($_GET['switch_cus_id'] ?? $_POST['switch_cus_id']));
        $chk = $pdo->prepare("SELECT cus_id FROM customers WHERE cus_id = ? LIMIT 1");
        $chk->execute([$req]);
        $valid = $chk->fetchColumn();
        if ($valid) {
            AuditLogger::logAction(
                $user['emp_id'] ?? 'EMP-0001',
                (string)$valid,
                'Customer Portal',
                'CUS',
                'CUSTOMER_IMPERSONATE_SWITCH',
                'customers',
                (string)$valid,
                ['switched_to' => (string)$valid]
            );
            $_SESSION['impersonate_cus_id'] = (string)$valid;
            $_SESSION['cus_id'] = (string)$valid;
            return (string)$valid;
        }
    }

    return (string)($_SESSION['impersonate_cus_id'] ?? '');
}

function cus_getCustomerContext(?string $cusId = null): array
{
    $pdo = getDbConnection();
    $cusId = $cusId ?: cus_getCurrentCustomerId();

    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            e.full_name AS account_manager_name,
            e.email AS account_manager_email,
            e.job_title AS account_manager_title,
            ca.username,
            ca.email AS account_email,
            ca.status AS account_status
        FROM customers c
        LEFT JOIN employees e ON c.account_manager_emp_id = e.emp_id
        LEFT JOIN customer_accounts ca ON c.cus_id = ca.cus_id
        WHERE c.cus_id = ?
        LIMIT 1
    ");
    $stmt->execute([$cusId]);
    $ctx = $stmt->fetch(PDO::FETCH_ASSOC);

    return $ctx ?: [
        'cus_id'               => $cusId,
        'company_name'         => 'Aral Geomatics Group',
        'sector'               => 'Geomatics & GIS',
        'primary_contact_name' => 'Sergei Makarov',
        'primary_contact_email'=> 's.makarov@aral-geo.kz',
        'account_manager_name' => 'Arman Zhumabayev'
    ];
}

// ============================================================================
// 1. DASHBOARD AGGREGATE METRICS
// ============================================================================

function cus_getDashboardMetrics(?string $cusId = null): array
{
    $pdo = getDbConnection();
    $cusId = $cusId ?: cus_getCurrentCustomerId();

    // Single aggregate query for all project/invoice/ticket/order metrics
    $mStmt = $pdo->prepare("
        SELECT
            (SELECT COUNT(*) FROM projects WHERE cus_id = c.cus_id
             AND status IN ('Planning','Procurement','Design','Integration','Testing','Execution')) AS active_projects,
            (SELECT COUNT(*) FROM projects WHERE cus_id = c.cus_id) AS total_projects,
            (SELECT COUNT(*) FROM invoices WHERE cus_id = c.cus_id AND payment_status = 'Pending') AS pending_invoices,
            (SELECT COUNT(*) FROM invoices WHERE cus_id = c.cus_id) AS total_invoices,
            (SELECT COALESCE(SUM(total_value), 0) FROM invoices WHERE cus_id = c.cus_id AND payment_status = 'Pending') AS pending_balance,
            (SELECT COUNT(*) FROM tickets WHERE requester_cus_id = c.cus_id AND status != 'Resolved') AS open_tickets,
            (SELECT COUNT(*) FROM orders WHERE cus_id = c.cus_id) AS total_orders
        FROM customers c WHERE c.cus_id = ?
    ");
    $mStmt->execute([$cusId]);
    $m = $mStmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // Recent Projects
    $prjStmt = $pdo->prepare("
        SELECT prj_id, project_name, budget, currency, status, start_date, end_date
        FROM projects WHERE cus_id = ? ORDER BY prj_id DESC LIMIT 3
    ");
    $prjStmt->execute([$cusId]);
    $recentPrj = $prjStmt->fetchAll(PDO::FETCH_ASSOC);

    // Recent Invoices
    $invStmt = $pdo->prepare("
        SELECT inv_id, prj_id, total_value, currency, payment_status, issued_at, due_date
        FROM invoices WHERE cus_id = ? ORDER BY issued_at DESC, inv_id DESC LIMIT 4
    ");
    $invStmt->execute([$cusId]);
    $recentInv = $invStmt->fetchAll(PDO::FETCH_ASSOC);

    // Recent Tickets
    $tktStmt = $pdo->prepare("
        SELECT tkt_id, title, priority, status, created_at
        FROM tickets WHERE requester_cus_id = ? ORDER BY created_at DESC LIMIT 4
    ");
    $tktStmt->execute([$cusId]);
    $recentTkt = $tktStmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'customer'         => cus_getCustomerContext($cusId),
        'active_projects'  => (int)($m['active_projects'] ?? 0),
        'total_projects'   => (int)($m['total_projects'] ?? 0),
        'pending_invoices' => (int)($m['pending_invoices'] ?? 0),
        'total_invoices'   => (int)($m['total_invoices'] ?? 0),
        'pending_balance'  => (float)($m['pending_balance'] ?? 0.0),
        'open_tickets'     => (int)($m['open_tickets'] ?? 0),
        'total_orders'     => (int)($m['total_orders'] ?? 0),
        'recent_projects'  => $recentPrj,
        'recent_invoices'  => $recentInv,
        'recent_tickets'   => $recentTkt
    ];
}

// ============================================================================
// 2. PROJECTS & MILESTONES (ANTI-IDOR PROTECTED)
// ============================================================================

function cus_getProjects(?string $cusId = null): array
{
    $pdo = getDbConnection();
    $cusId = $cusId ?: cus_getCurrentCustomerId();

    $stmt = $pdo->prepare("
        SELECT 
            p.*,
            e.full_name AS project_manager_name,
            e.email AS project_manager_email,
            (SELECT COUNT(*) FROM billing_cycles WHERE prj_id = p.prj_id) AS total_milestones,
            (SELECT COUNT(*) FROM billing_cycles WHERE prj_id = p.prj_id AND invoiced = 1) AS completed_milestones
        FROM projects p
        LEFT JOIN employees e ON p.project_manager_emp_id = e.emp_id
        WHERE p.cus_id = ?
        ORDER BY p.prj_id ASC
    ");
    $stmt->execute([$cusId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function cus_getProjectDetail(string $prjId, ?string $cusId = null): ?array
{
    $pdo = getDbConnection();
    $cusId = $cusId ?: cus_getCurrentCustomerId();

    $stmt = $pdo->prepare("
        SELECT 
            p.*,
            e.full_name AS project_manager_name,
            e.email AS project_manager_email,
            e.job_title AS project_manager_title
        FROM projects p
        LEFT JOIN employees e ON p.project_manager_emp_id = e.emp_id
        WHERE p.prj_id = :pid AND p.cus_id = :cid
    ");
    $stmt->execute([':pid' => $prjId, ':cid' => $cusId]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$project) return null;

    // Milestones (billing_cycles)
    $mStmt = $pdo->prepare("
        SELECT * FROM billing_cycles 
        WHERE prj_id = :pid 
        ORDER BY scheduled_date ASC
    ");
    $mStmt->execute([':pid' => $prjId]);
    $project['milestones'] = $mStmt->fetchAll(PDO::FETCH_ASSOC);

    // Linked Invoices
    $iStmt = $pdo->prepare("
        SELECT * FROM invoices 
        WHERE prj_id = :pid AND cus_id = :cid
        ORDER BY issued_at DESC
    ");
    $iStmt->execute([':pid' => $prjId, ':cid' => $cusId]);
    $project['invoices'] = $iStmt->fetchAll(PDO::FETCH_ASSOC);

    // Documents
    $dStmt = $pdo->prepare("
        SELECT * FROM documents 
        WHERE related_prj_id = :pid
        ORDER BY created_at DESC
    ");
    $dStmt->execute([':pid' => $prjId]);
    $project['documents'] = $dStmt->fetchAll(PDO::FETCH_ASSOC);

    return $project;
}

function cus_updateMilestoneStatus(int $cycleId, string $prjId, string $cusId, bool $invoiced): bool
{
    $pdo = getDbConnection();
    // Validate project ownership
    $check = $pdo->prepare("SELECT cus_id FROM projects WHERE prj_id = ?");
    $check->execute([$prjId]);
    if ($check->fetchColumn() !== $cusId) {
        throw new Exception("Unauthorized access to project.");
    }

    $stmt = $pdo->prepare("UPDATE billing_cycles SET invoiced = :inv WHERE cycle_id = :cid AND prj_id = :pid");
    return $stmt->execute([':inv' => $invoiced ? 1 : 0, ':cid' => $cycleId, ':pid' => $prjId]);
}

// ============================================================================
// 3. INVOICES & PAYMENTS RECONCILIATION
// ============================================================================

function cus_getInvoices(?string $cusId = null, array $filters = []): array
{
    $pdo = getDbConnection();
    $cusId = $cusId ?: cus_getCurrentCustomerId();

    $where = ["i.cus_id = :cid"];
    $params = [':cid' => $cusId];

    if (!empty($filters['status']) && $filters['status'] !== 'all') {
        $where[] = "i.payment_status = :status";
        $params[':status'] = $filters['status'];
    }

    if (!empty($filters['search'])) {
        $where[] = "(i.inv_id LIKE :s OR i.prj_id LIKE :s OR p.project_name LIKE :s)";
        $params[':s'] = '%' . $filters['search'] . '%';
    }

    $whereSql = "WHERE " . implode(" AND ", $where);

    $sql = "
        SELECT 
            i.*,
            p.project_name,
            COALESCE(SUM(pay.amount), 0) AS total_paid
        FROM invoices i
        LEFT JOIN projects p ON i.prj_id = p.prj_id
        LEFT JOIN payments pay ON i.inv_id = pay.inv_id
        {$whereSql}
        GROUP BY i.inv_id
        ORDER BY i.issued_at DESC, i.inv_id DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function cus_getInvoiceDetail(string $invId, ?string $cusId = null): ?array
{
    $pdo = getDbConnection();
    $cusId = $cusId ?: cus_getCurrentCustomerId();

    $stmt = $pdo->prepare("
        SELECT 
            i.*,
            c.company_name,
            c.primary_contact_name,
            c.primary_contact_email,
            p.project_name,
            p.budget AS project_budget
        FROM invoices i
        JOIN customers c ON i.cus_id = c.cus_id
        LEFT JOIN projects p ON i.prj_id = p.prj_id
        WHERE i.inv_id = :iid AND i.cus_id = :cid
    ");
    $stmt->execute([':iid' => $invId, ':cid' => $cusId]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$invoice) return null;

    // Payments
    $payStmt = $pdo->prepare("SELECT * FROM payments WHERE inv_id = ? ORDER BY payment_date DESC");
    $payStmt->execute([$invId]);
    $invoice['payments'] = $payStmt->fetchAll(PDO::FETCH_ASSOC);

    // Items
    $itemStmt = $pdo->prepare("SELECT * FROM invoice_items WHERE inv_id = ? ORDER BY item_id ASC");
    $itemStmt->execute([$invId]);
    $invoice['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

    return $invoice;
}

function cus_recordPayment(string $invId, string $cusId, float $amount, ?string $txRef = null, string $method = 'Corporate Wire Transfer'): array
{
    $pdo = getDbConnection();
    if ($amount <= 0) {
        throw new InvalidArgumentException("Payment amount must be greater than zero.");
    }

    // Verify invoice ownership
    $invStmt = $pdo->prepare("SELECT * FROM invoices WHERE inv_id = :iid AND cus_id = :cid");
    $invStmt->execute([':iid' => $invId, ':cid' => $cusId]);
    $inv = $invStmt->fetch(PDO::FETCH_ASSOC);

    if (!$inv) {
        throw new Exception("Invoice [{$invId}] not found or unauthorized.");
    }

    return vp_reconcile_payment($pdo, $invId, $amount, $method, $cusId);
}

// ============================================================================
// 4. SUPPORT TICKETS & THREADED COMMENTS
// ============================================================================

function cus_getTickets(?string $cusId = null, array $filters = []): array
{
    $pdo = getDbConnection();
    $cusId = $cusId ?: cus_getCurrentCustomerId();

    $where = ["t.requester_cus_id = :cid"];
    $params = [':cid' => $cusId];

    if (!empty($filters['status']) && $filters['status'] !== 'all') {
        $where[] = "t.status = :status";
        $params[':status'] = $filters['status'];
    }

    if (!empty($filters['priority']) && $filters['priority'] !== 'all') {
        $where[] = "t.priority = :priority";
        $params[':priority'] = $filters['priority'];
    }

    $whereSql = "WHERE " . implode(" AND ", $where);

    $stmt = $pdo->prepare("
        SELECT 
            t.*,
            e.full_name AS assigned_engineer_name,
            e.email AS assigned_engineer_email,
            (SELECT COUNT(*) FROM ticket_comments WHERE tkt_id = t.tkt_id) AS comment_count
        FROM tickets t
        LEFT JOIN employees e ON t.assigned_emp_id = e.emp_id
        {$whereSql}
        ORDER BY t.created_at DESC
    ");
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function cus_getTicketDetail(string $tktId, ?string $cusId = null): ?array
{
    $pdo = getDbConnection();
    $cusId = $cusId ?: cus_getCurrentCustomerId();

    if (!empty($cusId)) {
        $stmt = $pdo->prepare("
            SELECT 
                t.*,
                e.full_name AS assigned_engineer_name,
                e.email AS assigned_engineer_email,
                e.job_title AS assigned_engineer_role
            FROM tickets t
            LEFT JOIN employees e ON t.assigned_emp_id = e.emp_id
            WHERE t.tkt_id = :tid AND t.requester_cus_id = :cid
        ");
        $stmt->execute([':tid' => $tktId, ':cid' => $cusId]);
    } else {
        $stmt = $pdo->prepare("
            SELECT 
                t.*,
                e.full_name AS assigned_engineer_name,
                e.email AS assigned_engineer_email,
                e.job_title AS assigned_engineer_role
            FROM tickets t
            LEFT JOIN employees e ON t.assigned_emp_id = e.emp_id
            WHERE t.tkt_id = :tid
        ");
        $stmt->execute([':tid' => $tktId]);
    }
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$ticket) return null;

    $cStmt = $pdo->prepare("
        SELECT 
            c.*,
            COALESCE(e.full_name, c.author_name) AS display_author
        FROM ticket_comments c
        LEFT JOIN employees e ON c.author_emp_id = e.emp_id
        WHERE c.tkt_id = ?
        ORDER BY c.created_at ASC
    ");
    $cStmt->execute([$tktId]);
    $ticket['comments'] = $cStmt->fetchAll(PDO::FETCH_ASSOC);

    return $ticket;
}

function cus_createTicket(string $cusId, array $data): string
{
    $pdo = getDbConnection();
    require_once __DIR__ . '/../includes/enterprise_flows.php';
    $data['cus_id'] = $cusId;
    $data['requester_cus_id'] = $cusId;
    $data['source_system'] = 'CUS';
    return vp_create_ticket($pdo, $data, $cusId);
}

function cus_addTicketComment(string $tktId, string $cusId, string $commentText, string $authorName = 'Customer Rep'): bool
{
    $pdo = getDbConnection();
    // Verify ownership
    $chk = $pdo->prepare("SELECT 1 FROM tickets WHERE tkt_id = :tid AND requester_cus_id = :cid");
    $chk->execute([':tid' => $tktId, ':cid' => $cusId]);
    if (!$chk->fetchColumn()) {
        throw new Exception("Unauthorized ticket thread.");
    }

    $stmt = $pdo->prepare("
        INSERT INTO ticket_comments (tkt_id, author_name, author_type, comment_text, created_at)
        VALUES (:tid, :author, 'requester', :txt, NOW())
    ");
    return $stmt->execute([
        ':tid'    => $tktId,
        ':author' => $authorName,
        ':txt'    => trim($commentText)
    ]);
}

function cus_updateTicketStatus(string $tktId, string $cusId, string $newStatus): bool
{
    $pdo = getDbConnection();
    $allowed = ['Open', 'InProgress', 'Investigating', 'Escalated', 'Resolved'];
    if (!in_array($newStatus, $allowed, true)) {
        throw new InvalidArgumentException("Invalid ticket status: {$newStatus}");
    }

    $chk = $pdo->prepare("SELECT 1 FROM tickets WHERE tkt_id = :tid AND requester_cus_id = :cid");
    $chk->execute([':tid' => $tktId, ':cid' => $cusId]);
    if (!$chk->fetchColumn()) {
        throw new Exception("Unauthorized ticket action.");
    }

    $resolvedAt = ($newStatus === 'Resolved') ? date('Y-m-d H:i:s') : null;

    $stmt = $pdo->prepare("UPDATE tickets SET status = :st, resolved_at = :ra WHERE tkt_id = :tid");
    return $stmt->execute([':st' => $newStatus, ':ra' => $resolvedAt, ':tid' => $tktId]);
}

// ============================================================================
// 5. DOCUMENTS & ACCOUNT SETTINGS
// ============================================================================

function cus_getDocuments(?string $cusId = null): array
{
    $pdo = getDbConnection();
    $cusId = $cusId ?: cus_getCurrentCustomerId();

    $stmt = $pdo->prepare("
        SELECT d.*, p.project_name
        FROM documents d
        LEFT JOIN projects p ON d.related_prj_id = p.prj_id
        WHERE d.related_cus_id = :cid OR d.project_ref IN (SELECT prj_id FROM projects WHERE cus_id = :cid2)
        ORDER BY d.created_at DESC
    ");
    $stmt->execute([':cid' => $cusId, ':cid2' => $cusId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function cus_updateAccountSettings(string $cusId, array $data): bool
{
    $pdo = getDbConnection();
    $fields = [];
    $params = [':cid' => $cusId];

    if (!empty($data['primary_contact_name'])) {
        $fields[] = "primary_contact_name = :pcn";
        $params[':pcn'] = trim($data['primary_contact_name']);
    }
    if (!empty($data['primary_contact_email'])) {
        $fields[] = "primary_contact_email = :pce";
        $params[':pce'] = trim($data['primary_contact_email']);
    }

    if (!empty($fields)) {
        $sql = "UPDATE customers SET " . implode(", ", $fields) . " WHERE cus_id = :cid";
        $pdo->prepare($sql)->execute($params);
    }

    // Update password if requested
    if (!empty($data['new_password'])) {
        $hash = password_hash($data['new_password'], PASSWORD_BCRYPT);
        $pUpd = $pdo->prepare("UPDATE customer_accounts SET password_hash = :hash WHERE cus_id = :cid");
        $pUpd->execute([':hash' => $hash, ':cid' => $cusId]);
    }

    return true;
}

// ============================================================================
// 6. AJAX ACTION ROUTER
// ============================================================================

$action = $_REQUEST['action'] ?? null;
if ($action !== null && (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_GET['action']) || isset($_POST['action']) || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json'))) {
    try {
        $cid = cus_getCurrentCustomerId();

        switch ($action) {
            case 'get_dashboard':
                cus_jsonReply(['success' => true, 'data' => cus_getDashboardMetrics($cid)]);
                break;

            case 'get_projects':
                cus_jsonReply(['success' => true, 'data' => cus_getProjects($cid)]);
                break;

            case 'get_project_detail':
                $pid = trim($_GET['id'] ?? $_GET['prj_id'] ?? '');
                $prj = cus_getProjectDetail($pid, $cid);
                if (!$prj) cus_jsonReply(['success' => false, 'error' => 'Project not found'], 404);
                cus_jsonReply(['success' => true, 'data' => $prj]);
                break;

            case 'update_milestone':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $cycleId = (int)($input['cycle_id'] ?? 0);
                $pid = trim($input['prj_id'] ?? '');
                $inv = (bool)($input['invoiced'] ?? true);
                cus_updateMilestoneStatus($cycleId, $pid, $cid, $inv);
                cus_jsonReply(['success' => true, 'message' => 'Milestone status updated']);
                break;

            case 'get_invoices':
                cus_jsonReply(['success' => true, 'data' => cus_getInvoices($cid, $_GET)]);
                break;

            case 'get_invoice_detail':
                $iid = trim($_GET['id'] ?? $_GET['inv_id'] ?? '');
                $inv = cus_getInvoiceDetail($iid, $cid);
                if (!$inv) cus_jsonReply(['success' => false, 'error' => 'Invoice not found'], 404);
                cus_jsonReply(['success' => true, 'data' => $inv]);
                break;

            case 'record_payment':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $iid = trim($input['inv_id'] ?? '');
                $amount = (float)($input['amount'] ?? 0);
                $method = trim($input['method'] ?? 'Corporate Wire Transfer');
                $res = cus_recordPayment($iid, $cid, $amount, null, $method);
                cus_jsonReply(array_merge(['success' => true, 'message' => 'Payment posted and invoice updated'], $res));
                break;

            case 'get_tickets':
                cus_jsonReply(['success' => true, 'data' => cus_getTickets($cid, $_GET)]);
                break;

            case 'get_ticket_detail':
                $tid = trim($_GET['id'] ?? $_GET['tkt_id'] ?? '');
                $tkt = cus_getTicketDetail($tid, $cid);
                if (!$tkt) cus_jsonReply(['success' => false, 'error' => 'Ticket not found'], 404);
                cus_jsonReply(['success' => true, 'data' => $tkt]);
                break;

            case 'create_ticket':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $tid = cus_createTicket($cid, $input);
                cus_jsonReply(['success' => true, 'tkt_id' => $tid, 'message' => 'Support ticket lodged successfully']);
                break;

            case 'add_comment':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $tid = trim($input['tkt_id'] ?? '');
                $comment = trim($input['comment_text'] ?? '');
                cus_addTicketComment($tid, $cid, $comment);
                cus_jsonReply(['success' => true, 'message' => 'Comment appended to ticket']);
                break;

            case 'update_ticket_status':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $tid = trim($input['tkt_id'] ?? '');
                $st = trim($input['status'] ?? '');
                cus_updateTicketStatus($tid, $cid, $st);
                cus_jsonReply(['success' => true, 'message' => "Ticket status set to {$st}"]);
                break;

            case 'update_account':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                cus_updateAccountSettings($cid, $input);
                cus_jsonReply(['success' => true, 'message' => 'Account settings updated successfully']);
                break;
        }
    } catch (Throwable $e) {
        cus_jsonReply(['success' => false, 'error' => $e->getMessage()], 400);
    }
}
