<?php

declare(strict_types=1);

/**
 * Customer Portal - Published Job Postings API
 * Live integration showing jobs created in HR System (SYS-06)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/integration_service.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed. Use GET.']);
    exit;
}

$pdo = getDbConnection();
$jobs = vostok_getPublishedJobPostings($pdo);

echo json_encode([
    'success'  => true,
    'postings' => $jobs,
    'count'    => count($jobs),
    'source'   => 'VOSTOKPRIBOR HR System (SYS-06)'
]);
