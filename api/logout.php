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

// Clear session
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
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

$redirect = $_GET['redirect'] ?? 'login.php';
header("Location: " . $redirect);
exit;
