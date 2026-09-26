<?php

/**
 * VOSTOKPRIBOR Logout Endpoint
 * Terminates user session in database and clears PHP session cookies.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

$sessionId = session_id();
if (!empty($sessionId)) {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("UPDATE `user_sessions` SET `status` = 'Terminated' WHERE `session_id` = ?");
        $stmt->execute([$sessionId]);
    } catch (Exception $e) {
        error_log("Logout session update error: " . $e->getMessage());
    }
}

// Clear session & SSO cookie
$_SESSION = [];
clearSsoCookie();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
session_destroy();

// Redirect or return JSON
$isJson = (isset($_GET['ajax']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false));

if ($isJson) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
    exit;
}

$systemDirs = [
    'ADM' => 'Admin & Governance Portal',
    'CRM' => 'CRM',
    'CUS' => 'Customer Portal',
    'DEV' => 'Developer',
    'EMP' => 'Employee Intranet',
    'DOC' => 'File Center',
    'FIN' => 'Finance & Billing',
    'HR'  => 'HR System',
    'IT'  => 'IT Helpdesk',
    'SHP' => 'Online Shop B2B'
];

$system = $_GET['system'] ?? '';
$redirect = $_GET['redirect'] ?? '';

if (empty($redirect) || $redirect === 'login.php') {
    if (!empty($system) && isset($systemDirs[strtoupper($system)])) {
        $redirect = '../' . rawurlencode($systemDirs[strtoupper($system)]) . '/login.php';
    } else if (!empty($system)) {
        $redirect = '../' . rawurlencode($system) . '/login.php';
    } else if (!empty($_SERVER['HTTP_REFERER'])) {
        $refererDir = dirname($_SERVER['HTTP_REFERER']);
        $redirect = $refererDir . '/login.php';
    } else {
        $redirect = '../index.php';
    }
}

header("Location: " . $redirect);
exit;
