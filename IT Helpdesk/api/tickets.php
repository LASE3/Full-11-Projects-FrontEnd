<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR IT Helpdesk - Tickets API Endpoint
 * Full CRUD, SLA Deadlines, Resolution & Escalation
 */

require_once __DIR__ . '/db_helper.php';
require_once __DIR__ . '/../../includes/auth_guard.php';

$user = requireApiAuth();

$pdo = getItDb();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$payload = getRequestPayload();

// ---------------------------------------------------------------------
// 1. GET: Fetch Ticket List or Single Ticket Detail
// ---------------------------------------------------------------------
if ($method === 'GET') {
    $tktId = trim((string)($payload['id'] ?? ''));

    if ($tktId !== '') {
        $stmt = $pdo->prepare("
            SELECT 
                t.*,
                COALESCE(e.full_name, t.assigned_emp_id, 'Unassigned') AS assigned_tech_name,
                e.job_title AS assigned_tech_role,
                e.email AS assigned_tech_email
            FROM tickets t
            LEFT JOIN employees e ON t.assigned_emp_id = e.emp_id
            WHERE t.tkt_id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $tktId]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticket) {
            sendJsonError("Ticket [{$tktId}] not found in database.", 404);
        }

        if (($user['account_type'] ?? '') === 'Customer') {
            $ownCusId = $user['cus_id'] ?? $user['user_id'];
            if ($ticket['requester_cus_id'] !== $ownCusId) {
                sendJsonError("Forbidden: Unauthorized ticket access.", 403);
            }
        }

        // Fetch comments for this ticket
        $cmtStmt = $pdo->prepare("
            SELECT 
                c.*,
                COALESCE(e.full_name, c.author_name, 'IT Support') AS display_author,
                COALESCE(e.job_title, c.author_role, 'Support Engineer') AS display_role
            FROM ticket_comments c
            LEFT JOIN employees e ON c.author_emp_id = e.emp_id
            WHERE c.tkt_id = :id
            ORDER BY c.created_at ASC, c.comment_id ASC
        ");
        $cmtStmt->execute([':id' => $tktId]);
        $ticket['comments'] = $cmtStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch escalations if any
        $escStmt = $pdo->prepare("
            SELECT es.*, e.full_name AS escalated_to_name
            FROM ticket_escalations es
            LEFT JOIN employees e ON es.escalated_to_emp_id = e.emp_id
            WHERE es.tkt_id = :id
            ORDER BY es.escalated_at DESC
        ");
        $escStmt->execute([':id' => $tktId]);
        $ticket['escalations'] = $escStmt->fetchAll(PDO::FETCH_ASSOC);

        sendJsonSuccess($ticket, "Ticket [{$tktId}] retrieved.");
    }

    // List tickets with filters
    $where = [];
    $params = [];

    if (($user['account_type'] ?? '') === 'Customer') {
        $where[] = "t.requester_cus_id = :own_cus_id";
        $params[':own_cus_id'] = $user['cus_id'] ?? $user['user_id'];
    }

    $status = trim((string)($payload['status'] ?? ''));
    if ($status !== '' && $status !== 'all') {
        $where[] = "t.status = :status";
        $params[':status'] = $status;
    }

    $priority = trim((string)($payload['priority'] ?? ''));
    if ($priority !== '' && $priority !== 'all') {
        $where[] = "t.priority = :priority";
        $params[':priority'] = $priority;
    }

    $system = trim((string)($payload['system'] ?? ''));
    if ($system !== '' && $system !== 'all') {
        $where[] = "(t.source_system LIKE :system OR t.title LIKE :system)";
        $params[':system'] = "%{$system}%";
    }

    $tech = trim((string)($payload['tech'] ?? ''));
    if ($tech !== '' && $tech !== 'all') {
        if ($tech === 'Unassigned') {
            $where[] = "(t.assigned_emp_id IS NULL OR t.assigned_emp_id = '')";
        } else {
            $where[] = "(e.full_name LIKE :tech OR t.assigned_emp_id LIKE :tech)";
            $params[':tech'] = "%{$tech}%";
        }
    }

    $search = trim((string)($payload['search'] ?? ''));
    if ($search !== '') {
        $where[] = "(t.tkt_id LIKE :s OR t.title LIKE :s OR t.description LIKE :s OR t.requester_name LIKE :s OR t.source_system LIKE :s)";
        $params[':s'] = "%{$search}%";
    }

    $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

    $sql = "
        SELECT 
            t.*,
            COALESCE(e.full_name, t.assigned_emp_id, 'Unassigned') AS assigned_tech_name,
            e.job_title AS assigned_tech_role
        FROM tickets t
        LEFT JOIN employees e ON t.assigned_emp_id = e.emp_id
        {$whereSql}
        ORDER BY 
            CASE t.priority 
                WHEN 'Critical' THEN 1 
                WHEN 'High' THEN 2 
                WHEN 'Medium' THEN 3 
                WHEN 'Low' THEN 4 
                ELSE 5 
            END ASC,
            t.created_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendJsonSuccess($tickets, "Loaded " . count($tickets) . " tickets from database.");
}

// ---------------------------------------------------------------------
// 2. POST: Create, Update, Resolve, Escalate, Bulk-Assign, Delete
// ---------------------------------------------------------------------
$action = trim((string)($_GET['action'] ?? ($payload['action'] ?? '')));

// ACTION: CREATE TICKET
if ($action === 'create') {
    $title = trim((string)($payload['title'] ?? ''));
    if ($title === '') {
        sendJsonError("Ticket title is required.");
    }

    require_once __DIR__ . '/../../includes/enterprise_flows.php';
    $actorId = $user['emp_id'] ?? ($user['cus_id'] ?? ($user['user_id'] ?? 'EMP-1004'));

    try {
        $tktId = vp_create_ticket($pdo, $payload, $actorId);

        $description = trim((string)($payload['description'] ?? ''));
        if ($description !== '') {
            $cmtStmt = $pdo->prepare("
                INSERT INTO ticket_comments 
                (tkt_id, author_name, author_role, author_type, comment_text, created_at)
                VALUES 
                (:tid, :authName, :authRole, 'requester', :txt, NOW())
            ");
            $cmtStmt->execute([
                ':tid'      => $tktId,
                ':authName' => $payload['requester_name'] ?? 'Authorized Personnel',
                ':authRole' => $payload['requester_role'] ?? 'Specialist',
                ':txt'      => $description,
            ]);
        }

        sendJsonSuccess(['tkt_id' => $tktId], "Ticket [{$tktId}] created successfully.");
    } catch (Throwable $e) {
        sendJsonError("Failed to create ticket: " . $e->getMessage(), 500);
    }
}

// ACTION: UPDATE TICKET
if ($action === 'update') {
    $tktId = trim((string)($payload['tkt_id'] ?? ($payload['ticket_id'] ?? ($payload['id'] ?? ''))));
    if ($tktId === '') {
        sendJsonError("Ticket ID is required for update.");
    }

    $title = trim((string)($payload['title'] ?? ''));
    $system = trim((string)($payload['source_system'] ?? ($payload['system'] ?? '')));
    $priority = trim((string)($payload['priority'] ?? ''));
    $status = trim((string)($payload['status'] ?? ''));
    $assignedEmpId = trim((string)($payload['assigned_emp_id'] ?? ''));
    $description = trim((string)($payload['description'] ?? ''));
    $requesterName = trim((string)($payload['requester_name'] ?? ''));
    $requesterRole = trim((string)($payload['requester_role'] ?? ''));
    $resolutionNotes = trim((string)($payload['resolution_notes'] ?? ''));

    $fields = [];
    $params = [':id' => $tktId];

    if ($title !== '') { $fields[] = "title = :title"; $params[':title'] = $title; }
    if ($system !== '') { $fields[] = "source_system = :sys"; $params[':sys'] = $system; }
    if ($priority !== '') { $fields[] = "priority = :prio"; $params[':prio'] = $priority; }
    if ($status !== '') { $fields[] = "status = :status"; $params[':status'] = $status; }
    if (isset($payload['assigned_emp_id'])) { $fields[] = "assigned_emp_id = :assigned"; $params[':assigned'] = $assignedEmpId ?: null; }
    if ($description !== '') { $fields[] = "description = :desc"; $params[':desc'] = $description; }
    if ($requesterName !== '') { $fields[] = "requester_name = :reqName"; $params[':reqName'] = $requesterName; }
    if ($requesterRole !== '') { $fields[] = "requester_role = :reqRole"; $params[':reqRole'] = $requesterRole; }
    if ($resolutionNotes !== '') { $fields[] = "resolution_notes = :resNotes"; $params[':resNotes'] = $resolutionNotes; }

    if ($status === 'Resolved') {
        $fields[] = "resolved_at = NOW()";
    }

    if (empty($fields)) {
        sendJsonError("No fields specified for update.");
    }

    $sql = "UPDATE tickets SET " . implode(", ", $fields) . " WHERE tkt_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    sendJsonSuccess(['tkt_id' => $tktId], "Ticket [{$tktId}] updated successfully.");
}

// ACTION: RESOLVE TICKET
if ($action === 'resolve') {
    $tktId = trim((string)($payload['tkt_id'] ?? ($payload['ticket_id'] ?? ($payload['id'] ?? ''))));
    $notes = trim((string)($payload['resolution_notes'] ?? 'Incident remediation completed successfully.'));

    if ($tktId === '') {
        sendJsonError("Ticket ID is required.");
    }

    $stmt = $pdo->prepare("
        UPDATE tickets 
        SET status = 'Resolved', 
            resolved_at = NOW(), 
            resolution_notes = :notes 
        WHERE tkt_id = :id
    ");
    $stmt->execute([':id' => $tktId, ':notes' => $notes]);

    // Insert resolution comment
    $currUser = getItCurrentUser();
    $authEmpId = !empty($payload['author_emp_id']) ? (string)$payload['author_emp_id'] : ($currUser['emp_id'] ?? 'EMP-1018');
    $authName = !empty($payload['author_name']) ? (string)$payload['author_name'] : ($currUser['full_name'] ?? 'Alexey Ivanov');
    $authRole = !empty($payload['author_role']) ? (string)$payload['author_role'] : ($currUser['role_display'] ?? 'Lead IT Tech · Tier 3');

    $cmtStmt = $pdo->prepare("
        INSERT INTO ticket_comments 
        (tkt_id, author_emp_id, author_name, author_role, author_type, comment_text, created_at)
        VALUES 
        (:tid, :emp, :name, :role, 'tech', :txt, NOW())
    ");
    $cmtStmt->execute([
        ':tid'  => $tktId,
        ':emp'  => $authEmpId,
        ':name' => $authName,
        ':role' => $authRole,
        ':txt'  => "RESOLVED: " . $notes,
    ]);

    // Check if this was a provisioning ticket and activate account
    $tCheck = $pdo->prepare("SELECT title FROM tickets WHERE tkt_id = :id");
    $tCheck->execute([':id' => $tktId]);
    $tTitle = $tCheck->fetchColumn();
    if ($tTitle && preg_match('/Provision access for (EMP-\d+)/i', (string)$tTitle, $m)) {
        $targetEmp = $m[1];
        $pdo->prepare("UPDATE employees SET employment_status = 'Active' WHERE emp_id = ? AND employment_status = 'Inactive'")->execute([$targetEmp]);
        $pdo->prepare("UPDATE employee_accounts SET status = 'Active' WHERE emp_id = ? AND status = 'Inactive'")->execute([$targetEmp]);
        $pdo->prepare("UPDATE employee_onboarding SET status = 'Completed', completed_at = NOW() WHERE emp_id = ? AND step = 'SystemAccessGranted'")->execute([$targetEmp]);
    }

    sendJsonSuccess(['tkt_id' => $tktId], "Ticket [{$tktId}] marked as Resolved.");
}

// ACTION: ESCALATE TICKET
if ($action === 'escalate') {
    $tktId = trim((string)($payload['tkt_id'] ?? ($payload['ticket_id'] ?? ($payload['id'] ?? ''))));
    $reason = trim((string)($payload['reason'] ?? 'Incident escalated to Operational Governance Board and Plant Engineering Manager.'));
    $escalateTo = trim((string)($payload['escalated_to'] ?? 'EMP-1005')); // Lead Governance

    if ($tktId === '') {
        sendJsonError("Ticket ID is required.");
    }

    // Update status to Escalated
    $stmt = $pdo->prepare("UPDATE tickets SET status = 'Escalated', priority = 'Critical' WHERE tkt_id = :id");
    $stmt->execute([':id' => $tktId]);

    // Insert into ticket_escalations
    $escStmt = $pdo->prepare("
        INSERT INTO ticket_escalations (tkt_id, escalated_to_emp_id, escalated_at, reason)
        VALUES (:tid, :toEmp, NOW(), :reason)
    ");
    $escStmt->execute([
        ':tid'    => $tktId,
        ':toEmp'  => $escalateTo,
        ':reason' => $reason,
    ]);

    // Insert comment
    $currUser = getItCurrentUser();
    $authEmpId = !empty($payload['author_emp_id']) ? (string)$payload['author_emp_id'] : ($currUser['emp_id'] ?? 'EMP-1018');
    $authName = !empty($payload['author_name']) ? (string)$payload['author_name'] : ($currUser['full_name'] ?? 'Alexey Ivanov');
    $authRole = !empty($payload['author_role']) ? (string)$payload['author_role'] : ($currUser['role_display'] ?? 'Lead IT Tech · Tier 3');

    $cmtStmt = $pdo->prepare("
        INSERT INTO ticket_comments 
        (tkt_id, author_emp_id, author_name, author_role, author_type, comment_text, created_at)
        VALUES 
        (:tid, :emp, :name, :role, 'system', :txt, NOW())
    ");
    $cmtStmt->execute([
        ':tid'  => $tktId,
        ':emp'  => $authEmpId,
        ':name' => $authName,
        ':role' => $authRole,
        ':txt'  => "🚨 ESCALATION: " . $reason,
    ]);

    sendJsonSuccess(['tkt_id' => $tktId], "Ticket [{$tktId}] successfully escalated to Governance.");
}

// ACTION: BULK ASSIGN
if ($action === 'bulk_assign') {
    // Assign unassigned tickets round-robin to active shift engineers
    $activeEngineers = ['EMP-1018', 'EMP-1004', 'EMP-1002'];
    $stmt = $pdo->query("SELECT tkt_id FROM tickets WHERE assigned_emp_id IS NULL OR assigned_emp_id = ''");
    $unassigned = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $assignedCount = 0;
    foreach ($unassigned as $idx => $tid) {
        $assignedTech = $activeEngineers[$idx % count($activeEngineers)];
        $update = $pdo->prepare("UPDATE tickets SET assigned_emp_id = :tech, status = 'InProgress' WHERE tkt_id = :tid");
        $update->execute([':tech' => $assignedTech, ':tid' => $tid]);
        $assignedCount++;
    }

    sendJsonSuccess(['assigned_count' => $assignedCount], "Bulk assigned {$assignedCount} pending tickets to active shift engineers.");
}

// ACTION: DELETE TICKET
if ($action === 'delete') {
    $tktId = trim((string)($payload['tkt_id'] ?? ($payload['ticket_id'] ?? ($payload['id'] ?? ''))));
    if ($tktId === '') {
        sendJsonError("Ticket ID is required for delete.");
    }

    // Delete comments first
    $pdo->prepare("DELETE FROM ticket_comments WHERE tkt_id = :id")->execute([':id' => $tktId]);
    // Delete escalations
    $pdo->prepare("DELETE FROM ticket_escalations WHERE tkt_id = :id")->execute([':id' => $tktId]);
    // Delete ticket
    $pdo->prepare("DELETE FROM tickets WHERE tkt_id = :id")->execute([':id' => $tktId]);

    sendJsonSuccess(['tkt_id' => $tktId], "Ticket [{$tktId}] deleted from database.");
}

sendJsonError("Invalid or unsupported action: {$action}");
