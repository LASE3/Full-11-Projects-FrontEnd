<?php

/**
 * Class 3: Customer Portal - Support Tickets API
 * Location: Customer Portal/api/tickets.php
 * Methods: GET, POST
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('CUS', ['customer' => true]);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/I18n.php';
require_once __DIR__ . '/helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$cusId = $_SESSION['cus_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? ($_GET['cus_id'] ?? null));
$lang  = $_GET['lang'] ?? 'en';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (empty($cusId)) {
    Response::error("Customer session required. Please log in.", 401);
}

if ($method === 'GET') {
    $tktId = $_GET['id'] ?? null;

    try {
        if ($tktId) {
            $stmt = $pdo->prepare("
                SELECT 
                    t.*,
                    e.full_name AS assigned_engineer_name,
                    e.email AS assigned_engineer_email
                FROM tickets t
                LEFT JOIN employees e ON t.assigned_emp_id = e.emp_id
                WHERE t.tkt_id = :tid AND t.requester_cus_id = :cid
            ");
            $stmt->execute([':tid' => $tktId, ':cid' => $cusId]);
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$ticket) {
                Response::error("Ticket not found or unauthorized.", 404);
            }

            $ticket['status_display']   = I18n::translate($ticket['status'], $lang);
            $ticket['priority_display'] = I18n::translate($ticket['priority'], $lang);

            // Fetch comments
            $cmtStmt = $pdo->prepare("
                SELECT tc.*, e.full_name AS staff_author_name
                FROM ticket_comments tc
                LEFT JOIN employees e ON tc.author_emp_id = e.emp_id
                WHERE tc.tkt_id = :tid
                ORDER BY tc.created_at ASC
            ");
            $cmtStmt->execute([':tid' => $tktId]);
            $ticket['comments'] = $cmtStmt->fetchAll(PDO::FETCH_ASSOC);

            Response::success($ticket, "Ticket details loaded");
        } else {
            $stmt = $pdo->prepare("
                SELECT 
                    t.tkt_id,
                    t.source_system,
                    t.priority,
                    t.status,
                    t.created_at,
                    t.resolved_at,
                    e.full_name AS assigned_engineer_name
                FROM tickets t
                LEFT JOIN employees e ON t.assigned_emp_id = e.emp_id
                WHERE t.requester_cus_id = :cid
                ORDER BY t.created_at DESC
            ");
            $stmt->execute([':cid' => $cusId]);
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($tickets as &$t) {
                $t['status_display']   = I18n::translate($t['status'], $lang);
                $t['priority_display'] = I18n::translate($t['priority'], $lang);
            }

            Response::success($tickets, "Customer tickets list loaded");
        }
    } catch (Exception $e) {
        Response::error("Failed to load tickets: " . $e->getMessage(), 500);
    }
}

if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);

    $action = $_GET['action'] ?? 'create';

    if ($action === 'comment') {
        // Add comment to existing ticket
        $tktId = trim($data['tkt_id'] ?? '');
        $comment = trim($data['comment_text'] ?? '');

        if (empty($tktId) || empty($comment)) {
            Response::error("Ticket ID and comment text required.", 422);
        }

        // Verify ownership
        $check = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE tkt_id = :tid AND requester_cus_id = :cid");
        $check->execute([':tid' => $tktId, ':cid' => $cusId]);
        if (!$check->fetchColumn()) {
            Response::error("Ticket access denied.", 403);
        }

        $ins = $pdo->prepare("INSERT INTO ticket_comments (tkt_id, author_emp_id, comment_text, created_at) VALUES (:tid, NULL, :txt, NOW())");
        $ins->execute([':tid' => $tktId, ':txt' => $comment]);

        Response::success(['comment_id' => $pdo->lastInsertId()], "Comment posted successfully", 201);
    } else {
        // Create new ticket
        $priority = $data['priority'] ?? 'Medium';
        $message  = trim($data['message'] ?? $data['description'] ?? '');
        $system   = trim($data['source_system'] ?? 'Customer Portal');

        if (empty($message)) {
            Response::error("Ticket description cannot be empty.", 422);
        }

        require_once __DIR__ . '/../../includes/enterprise_flows.php';
        $tktId = vp_create_ticket($pdo, [
            'title'          => $data['title'] ?? 'Customer Support Ticket',
            'description'    => $message,
            'priority'       => $priority,
            'source_system'  => 'CUS',
            'cus_id'         => $cusId,
            'requester_name' => $user['full_name'] ?? 'Customer Representative',
        ], $cusId);

        // Insert first comment thread entry
        $cmtStmt = $pdo->prepare("INSERT INTO ticket_comments (tkt_id, author_emp_id, comment_text, created_at) VALUES (:tid, NULL, :txt, NOW())");
        $cmtStmt->execute([':tid' => $tktId, ':txt' => $message]);

        Response::success([
            'tkt_id'   => $tktId,
            'status'   => ($priority === 'Critical') ? 'Escalated' : 'Open',
            'priority' => $priority
        ], "Support ticket successfully created", 201);
    }
}
