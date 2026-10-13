<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate new token if rotate requested or return current token
if (isset($_GET['rotate']) && $_GET['rotate'] === '1') {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$token = getCsrfToken();

echo json_encode([
    'success' => true,
    'csrf_token' => $token
], JSON_UNESCAPED_SLASHES);
