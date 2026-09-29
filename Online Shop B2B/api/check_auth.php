<?php

/**
 * B2B Shop Session Verification Endpoint
 * Confirms an active session is authorized for the B2B Shop system.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

$isAuthenticated = !empty($_SESSION['vostok_authenticated']) && $_SESSION['vostok_authenticated'] === true;
$isShopSession   = !empty($_SESSION['vostok_system_SHP']);

if (!$isAuthenticated || !$isShopSession || empty($_SESSION['vostok_user'])) {
    http_response_code(401);
    echo json_encode([
        'authenticated' => false,
        'user'          => null,
        'message'       => 'No active B2B Shop session.',
    ]);
    exit;
}

http_response_code(200);
echo json_encode([
    'authenticated' => true,
    'user'          => $_SESSION['vostok_user'],
    'message'       => 'B2B Shop session active.',
]);
