<?php

/**
 * VOSTOKPRIBOR Session Verification API
 * Checks active session and confirms authorization state against the database.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/auth_guard.php';

header('Content-Type: application/json; charset=utf-8');

$csrfToken = getCsrfToken();
$isAuthenticated = !empty($_SESSION['vostok_authenticated']) && $_SESSION['vostok_authenticated'] === true;

if (!$isAuthenticated || empty($_SESSION['vostok_user'])) {
    http_response_code(401);
    echo json_encode([
        'authenticated' => false,
        'user' => null,
        'csrf_token' => $csrfToken,
        'message' => 'No active authenticated session.'
    ]);
    exit;
}

http_response_code(200);
echo json_encode([
    'authenticated' => true,
    'user' => $_SESSION['vostok_user'],
    'csrf_token' => $csrfToken,
    'message' => 'Active session valid.'
]);
