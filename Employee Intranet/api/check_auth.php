<?php

/**
 * Employee Intranet Session Verification Endpoint
 * Confirms an active session is authorized for the Employee Intranet system.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

$isAuthenticated    = !empty($_SESSION['vostok_authenticated']) && $_SESSION['vostok_authenticated'] === true;
$isIntranetSession  = !empty($_SESSION['vostok_system_EMP']);

if (!$isAuthenticated || !$isIntranetSession || empty($_SESSION['vostok_user'])) {
    http_response_code(401);
    echo json_encode([
        'authenticated' => false,
        'user'          => null,
        'message'       => 'No active Employee Intranet session.',
    ]);
    exit;
}

http_response_code(200);
echo json_encode([
    'authenticated' => true,
    'user'          => $_SESSION['vostok_user'],
    'message'       => 'Employee Intranet session active.',
]);
