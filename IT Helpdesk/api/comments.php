<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR IT Helpdesk - Ticket Comments & Chat API
 */

require_once __DIR__ . '/db_helper.php';

$pdo = getItDb();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$payload = getRequestPayload();

// 1. GET: Fetch comments for a ticket
if ($method === 'GET') {
    $tktId = trim((string)($payload['tkt_id'] ?? ''));
    if ($tktId === '') {
        sendJsonError("Ticket ID is required.");
    }

    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            COALESCE(e.full_name, c.author_name, 'IT Support') AS display_author,
            COALESCE(e.job_title, c.author_role, 'Support Engineer') AS display_role
        FROM ticket_comments c
        LEFT JOIN employees e ON c.author_emp_id = e.emp_id
        WHERE c.tkt_id = :id
        ORDER BY c.created_at ASC, c.comment_id ASC
    ");
    $stmt->execute([':id' => $tktId]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendJsonSuccess($comments, "Comments retrieved.");
}

// 2. POST: Add or Delete Comment
$action = trim((string)($payload['action'] ?? 'create'));

if ($action === 'create') {
    $tktId = trim((string)($payload['tkt_id'] ?? ($payload['ticket_id'] ?? ($payload['id'] ?? ''))));
    $text = trim((string)($payload['comment_text'] ?? ($payload['message'] ?? '')));
    $authorName = trim((string)($payload['author_name'] ?? 'Alexey Ivanov'));
    $authorRole = trim((string)($payload['author_role'] ?? 'Lead IT Tech · Tier 3'));
    $authorType = trim((string)($payload['author_type'] ?? 'tech')); // 'tech', 'requester', 'system'
    $authorEmpId = trim((string)($payload['author_emp_id'] ?? 'EMP-1018'));

    if ($tktId === '' || $text === '') {
        sendJsonError("Ticket ID and message text are required.");
    }

    $stmt = $pdo->prepare("
        INSERT INTO ticket_comments 
        (tkt_id, author_emp_id, author_name, author_role, author_type, comment_text, created_at)
        VALUES 
        (:tid, :emp, :name, :role, :type, :txt, NOW())
    ");
    $stmt->execute([
        ':tid'  => $tktId,
        ':emp'  => $authorEmpId ?: null,
        ':name' => $authorName,
        ':role' => $authorRole,
        ':type' => $authorType,
        ':txt'  => $text,
    ]);

    $commentId = (int)$pdo->lastInsertId();

    sendJsonSuccess([
        'comment_id'     => $commentId,
        'tkt_id'         => $tktId,
        'display_author' => $authorName,
        'display_role'   => $authorRole,
        'author_type'    => $authorType,
        'comment_text'   => $text,
        'created_at'     => date('Y-m-d H:i:s'),
        'time_formatted' => date('H:i') . ' MSK',
    ], "Comment added to ticket thread.");
}

if ($action === 'delete') {
    $commentId = (int)($payload['comment_id'] ?? 0);
    if ($commentId <= 0) {
        sendJsonError("Comment ID is required for delete.");
    }

    $stmt = $pdo->prepare("DELETE FROM ticket_comments WHERE comment_id = :id");
    $stmt->execute([':id' => $commentId]);

    sendJsonSuccess(['comment_id' => $commentId], "Comment deleted.");
}

sendJsonError("Invalid comment action: {$action}");
