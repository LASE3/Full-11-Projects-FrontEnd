<?php

/**
 * VOSTOKPRIBOR Session Verification API
 * Checks active session and confirms authorization state against the database.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

$isAuthenticated = !empty($_SESSION['vostok_authenticated']) && $_SESSION['vostok_authenticated'] === true;

if (!$isAuthenticated || empty($_SESSION['vostok_user'])) {
    http_response_code(401);
    echo json_encode([
        'authenticated' => false,
        'user' => null,
        'message' => 'No active authenticated session.'
    ]);
    exit;
}

http_response_code(200);
echo json_encode([
    'authenticated' => true,
    'user' => $_SESSION['vostok_user'],
    'message' => 'Active session valid.'
]);
