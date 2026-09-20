<?php
/**
 * VOSTOKPRIBOR Centralized Authentication & Authorization API
 * Handles database authentication, credential verification, system clearance checks,
 * audit logging to authentication_events, and session creation.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

// Set response headers for API requests
header('Content-Type: application/json; charset=utf-8');

// Helper to determine if the request expects JSON
$isJsonRequest = (
    (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
    (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) ||
    (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
    isset($_GET['ajax']) || isset($_POST['ajax'])
);

// Collect parameters (support JSON payload, POST form-data, and query)
$inputRaw = file_get_contents('php://input');
$jsonData = json_decode($inputRaw, true) ?: [];

$userId   = trim($jsonData['userId'] ?? $_POST['userId'] ?? $_POST['username'] ?? '');
$password = $jsonData['password'] ?? $_POST['password'] ?? '';
$systemId = strtoupper(trim($jsonData['systemId'] ?? $_POST['systemId'] ?? $_POST['system_id'] ?? ''));
$redirect = trim($jsonData['redirect'] ?? $_POST['redirect'] ?? 'mainDashboard.php');

// Helper for error responses
function respondAuthError($message, $code = 401, $isJson = true, $systemId = '') {
    if ($isJson) {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'message' => $message,
            'system_id' => $systemId
        ]);
        exit;
    } else {
        $referer = $_SERVER['HTTP_REFERER'] ?? 'login.php';
        $delimiter = strpos($referer, '?') !== false ? '&' : '?';
        header("Location: " . $referer . $delimiter . "error=" . urlencode($message));
        exit;
    }
}

// 1. Validation
if (empty($userId)) {
    respondAuthError('Account ID or Username is required.', 400, $isJsonRequest, $systemId);
}

if (empty($password)) {
    respondAuthError('Password is required.', 400, $isJsonRequest, $systemId);
}

// 2. Query user in database
$user = queryUserByCredentials($userId);

if (!$user) {
    logAuthenticationEvent('Unknown', null, $systemId, false, "Unknown account: {$userId}");
    respondAuthError("Authentication failed: User account '{$userId}' was not found in the VOSTOKPRIBOR directory.", 401, $isJsonRequest, $systemId);
}

// 3. Check account status
if (isset($user['account_status']) && strtolower($user['account_status']) !== 'active') {
    logAuthenticationEvent($user['account_type'], $user['account_id'], $systemId, false, "Account suspended: {$userId}");
    respondAuthError("Access denied: Your account status is marked as '{$user['account_status']}'. Contact VP-SecOps.", 403, $isJsonRequest, $systemId);
}

if (isset($user['employment_status']) && strtolower($user['employment_status']) !== 'active') {
    logAuthenticationEvent($user['account_type'], $user['account_id'], $systemId, false, "Employee inactive: {$userId}");
    respondAuthError("Access denied: Employment record is '{$user['employment_status']}'.", 403, $isJsonRequest, $systemId);
}

// 4. Verify password against database
if (!verifyUserPassword($user, $password)) {
    logAuthenticationEvent($user['account_type'], $user['account_id'], $systemId, false, "Password mismatch for account {$userId}");
    respondAuthError('Authentication failed: Invalid credentials provided. Please re-check your password.', 401, $isJsonRequest, $systemId);
}

// 5. System clearance & role authorization check
$authCheck = checkSystemAuthorization($user, $systemId);
if (!$authCheck['authorized']) {
    logAuthenticationEvent($user['account_type'], $user['account_id'], $systemId, false, "Authorization denied for system {$systemId}: {$authCheck['reason']}");
    respondAuthError("Authorization Denied: {$authCheck['reason']}", 403, $isJsonRequest, $systemId);
}

// 6. Successful Authentication & Authorization
$userSessionData = [
    'account_id'      => $user['account_id'],
    'user_id'         => $user['emp_id'] ?? $user['cus_id'],
    'username'        => $user['username'],
    'full_name'       => $user['full_name'],
    'email'           => $user['email'] ?? '',
    'role_name'       => $user['role_name'] ?? 'Authorized User',
    'department_code' => $user['department_code'] ?? 'GEN',
    'clearance_level' => $user['clearance_level'] ?? 'L1',
    'account_type'    => $user['account_type'],
    'authorized_system' => $systemId,
    'login_time'      => date('Y-m-d H:i:s')
];

// Set PHP session
$_SESSION['vostok_authenticated'] = true;
$_SESSION['vostok_user'] = $userSessionData;

// Audit logging
logAuthenticationEvent($user['account_type'], $user['account_id'], $systemId, true, "Access granted via {$authCheck['reason']}");
registerUserSession($user['account_type'], $user['account_id'], $systemId);

// Update last_login in database
try {
    $pdo = getDbConnection();
    $tbl = ($user['account_type'] === 'Employee') ? 'employee_accounts' : 'customer_accounts';
    $upd = $pdo->prepare("UPDATE `{$tbl}` SET `last_login` = NOW() WHERE `account_id` = ?");
    $upd->execute([$user['account_id']]);
} catch (Exception $e) {
    error_log("Failed to update last_login: " . $e->getMessage());
}

// 7. Return response
if ($isJsonRequest) {
    http_response_code(200);
    echo json_encode([
        'success'      => true,
        'message'      => "Access Granted: " . $user['full_name'] . " (" . ($user['clearance_level'] ?? 'L1') . " Clearance)",
        'user'         => $userSessionData,
        'redirect'     => $redirect,
        'authorization_reason' => $authCheck['reason']
    ]);
    exit;
} else {
    header("Location: " . $redirect);
    exit;
}
