<?php

/**
 * B2B Shop Dedicated Authentication Endpoint
 * Handles login exclusively for the Online Shop B2B system (systemId = SHP).
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';

header('Content-Type: application/json; charset=utf-8');

const SYSTEM_ID       = 'SHP';
const SYSTEM_DIR      = 'Online Shop B2B';
const LOGIN_PAGE      = '../login.php';
const DEFAULT_REDIRECT = 'Dashboard.php';

$isJsonRequest = (
    (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
    (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) ||
    (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
    isset($_GET['ajax']) || isset($_POST['ajax'])
);

$inputRaw = file_get_contents('php://input');
$jsonData  = json_decode($inputRaw, true) ?: [];

$userId   = trim($jsonData['userId']   ?? $_POST['userId']   ?? $_POST['username'] ?? '');
$password =      $jsonData['password'] ?? $_POST['password'] ?? '';
$redirect = trim($jsonData['redirect'] ?? $_POST['redirect'] ?? DEFAULT_REDIRECT);

function respondError($message, $code = 401, $isJson = true)
{
    if ($isJson) {
        http_response_code($code);
        echo json_encode(['success' => false, 'message' => $message, 'system_id' => SYSTEM_ID]);
        exit;
    }
    $referer   = $_SERVER['HTTP_REFERER'] ?? LOGIN_PAGE;
    $delimiter = strpos($referer, '?') !== false ? '&' : '?';
    header('Location: ' . $referer . $delimiter . 'error=' . urlencode($message));
    exit;
}

if (empty($userId) || empty($password)) {
    respondError('Please enter your B2B account ID / email and password.', 400, $isJsonRequest);
}

$user = queryUserByCredentials($userId);
if (!$user) {
    logAuthenticationEvent('Unknown', null, SYSTEM_ID, false, "Unknown account: {$userId}");
    respondError("Account '{$userId}' was not found in the VOSTOKPRIBOR B2B directory.", 401, $isJsonRequest);
}

if (isset($user['account_status']) && strtolower($user['account_status']) !== 'active') {
    logAuthenticationEvent($user['account_type'], $user['account_id'], SYSTEM_ID, false, "Account suspended: {$userId}");
    respondError("Access denied: Account status is '{$user['account_status']}'. Contact VP B2B Commerce Administrator.", 403, $isJsonRequest);
}

if (!verifyUserPassword($user, $password)) {
    logAuthenticationEvent($user['account_type'], $user['account_id'], SYSTEM_ID, false, "Password mismatch: {$userId}");
    respondError('Invalid credentials. Please re-check your password.', 401, $isJsonRequest);
}

$authCheck = checkSystemAuthorization($user, SYSTEM_ID);
if (!$authCheck['authorized']) {
    logAuthenticationEvent($user['account_type'], $user['account_id'], SYSTEM_ID, false, "B2B auth denied: {$authCheck['reason']}");
    respondError("Authorization Denied: {$authCheck['reason']}", 403, $isJsonRequest);
}

$userSessionData = [
    'account_id'        => $user['account_id'],
    'user_id'           => $user['emp_id'] ?? $user['cus_id'],
    'emp_id'            => $user['emp_id'] ?? null,
    'cus_id'            => $user['cus_id'] ?? null,
    'username'          => $user['username'],
    'full_name'         => $user['full_name'],
    'email'             => $user['email'] ?? '',
    'role_name'         => $user['role_name'] ?? 'B2B Client',
    'department_code'   => $user['department_code'] ?? 'GEN',
    'clearance_level'   => $user['clearance_level'] ?? 'L1',
    'account_type'      => $user['account_type'],
    'authorized_system' => SYSTEM_ID,
    'login_time'        => date('Y-m-d H:i:s'),
];

foreach (array_keys($_SESSION) as $sessKey) {
    if (str_starts_with($sessKey, 'vostok_system_')) {
        unset($_SESSION[$sessKey]);
    }
}
$_SESSION['vostok_authenticated'] = true;
$_SESSION['vostok_system_' . SYSTEM_ID] = true;
$_SESSION['vostok_current_system']      = SYSTEM_ID;
$_SESSION['vostok_user']                = $userSessionData;

if ($user['account_type'] === 'Customer' && !empty($user['cus_id'])) {
    $_SESSION['cus_id'] = $user['cus_id'];
}

$jti = bin2hex(random_bytes(16));
createSsoCookie($userSessionData, $jti);
logAuthenticationEvent($user['account_type'], $user['account_id'], SYSTEM_ID, true, "B2B access granted: {$authCheck['reason']}");
registerUserSession($user['account_type'], $user['account_id'], SYSTEM_ID, $jti);

try {
    $pdo = getDbConnection();
    $tbl = ($user['account_type'] === 'Employee') ? 'employee_accounts' : 'customer_accounts';
    $upd = $pdo->prepare("UPDATE `{$tbl}` SET `last_login` = NOW() WHERE `account_id` = ?");
    $upd->execute([$user['account_id']]);
} catch (Exception $e) {
    error_log('B2B auth last_login update failed: ' . $e->getMessage());
}

if ($isJsonRequest) {
    http_response_code(200);
    echo json_encode([
        'success'              => true,
        'message'              => 'B2B Shop Access Granted: ' . $user['full_name'] . ' (' . ($user['clearance_level'] ?? 'L1') . ' Clearance)',
        'user'                 => $userSessionData,
        'redirect'             => $redirect,
        'authorization_reason' => $authCheck['reason'],
    ]);
    exit;
}

$targetUrl = (str_starts_with($redirect, 'http://') || str_starts_with($redirect, 'https://') || str_starts_with($redirect, '/'))
    ? $redirect
    : ('../' . SYSTEM_DIR . '/' . $redirect);

header('Location: ' . $targetUrl);
exit;
