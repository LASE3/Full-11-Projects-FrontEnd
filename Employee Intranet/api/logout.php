<?php

/**
 * Employee Intranet Logout Endpoint
 * Terminates the Intranet session and redirects to Intranet login page.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/db.php';

$sessionId = session_id();
if (!empty($sessionId)) {
    try {
        $pdo  = getDbConnection();
        $stmt = $pdo->prepare("UPDATE `user_sessions` SET `status` = 'Terminated' WHERE `session_id` = ?");
        $stmt->execute([$sessionId]);
    } catch (Exception $e) {
        error_log('Intranet logout session update error: ' . $e->getMessage());
    }
}

$_SESSION = [];
if (function_exists('clearSsoCookie')) {
    require_once __DIR__ . '/../../includes/auth_guard.php';
    clearSsoCookie();
}
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();

$isJson = (isset($_GET['ajax']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false));

if ($isJson) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'message' => 'Logged out of Employee Intranet successfully.']);
    exit;
}

$redirect = $_GET['redirect'] ?? '../Employee Intranet/login.php';
header('Location: ' . $redirect);
exit;
