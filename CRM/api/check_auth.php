<?php

/**
 * CRM Session Verification Endpoint
 * Confirms an active session is authorized for the CRM system.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

$isAuthenticated = !empty($_SESSION['vostok_authenticated']) && $_SESSION['vostok_authenticated'] === true;
$isCrmSession    = !empty($_SESSION['vostok_system_CRM']);

if (!$isAuthenticated || !$isCrmSession || empty($_SESSION['vostok_user'])) {
    http_response_code(401);
    echo json_encode([
        'authenticated' => false,
        'user'          => null,
        'message'       => 'No active CRM session.',
    ]);
    exit;
}

http_response_code(200);
echo json_encode([
    'authenticated' => true,
    'user'          => $_SESSION['vostok_user'],
    'message'       => 'CRM session active.',
]);
