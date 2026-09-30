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

header('Content-Type: application/json; charset=utf-8');

$pdo = getDbConnection();
$jobs = vostok_getPublishedJobPostings($pdo);

echo json_encode([
    'success'  => true,
    'postings' => $jobs,
    'count'    => count($jobs),
    'source'   => 'VOSTOKPRIBOR HR System (SYS-06)'
]);
