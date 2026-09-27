<?php

/**
 * VOSTOKPRIBOR System 11 // GOV-CORE
 * Re-Certification Window & Entitlement Review API
 */

require_once __DIR__ . '/db_helper.php';

try {
    $pdo = getDbConnection();
    gov_ensureSchemaReady($pdo);

    $method = $_SERVER['REQUEST_METHOD'];
    $input = getRequestInput();

    if ($method === 'GET') {
        $windows = $pdo->query("
            SELECT rw.*, e.full_name AS creator_name
            FROM recertification_windows rw
            LEFT JOIN employees e ON rw.created_by_emp_id = e.emp_id
            ORDER BY rw.window_id DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        jsonResponse([
            'success' => true,
            'count' => count($windows),
            'windows' => $windows
        ]);
    }

    if ($method === 'POST') {
        $title = trim($input['title'] ?? 'Quarterly Statutory Re-Certification Window');
        $startDate = trim($input['start_date'] ?? date('Y-m-d'));
        $endDate = trim($input['end_date'] ?? date('Y-m-d', strtotime('+30 days')));
        $status = trim($input['status'] ?? 'Active');
        $scopes = trim($input['scopes'] ?? 'ALL');
        $actorId = getCurrentGovActor();

        $stmt = $pdo->prepare("
            INSERT INTO recertification_windows 
            (title, start_date, end_date, status, scopes, created_by_emp_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $startDate, $endDate, $status, $scopes, $actorId]);
        $newId = (int)$pdo->lastInsertId();

        recordAuditEntry($pdo, 'LAUNCH_RECERTIFICATION_WINDOW', 'recertification_windows', (string)$newId, 'SUCCESS', [
            'title' => $title,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status
        ]);

        jsonResponse([
            'success' => true,
            'message' => "Re-Certification campaign [{$title}] launched and registered in database.",
            'window_id' => $newId
        ], 201);
    }

    jsonResponse(['success' => false, 'error' => 'Unsupported method.'], 405);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
