<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';

$pdo = getDbConnection();
$pdo->exec("UPDATE employee_accounts SET must_change_password = 0 WHERE emp_id IN ('EMP-1017', 'EMP-1020')");

// 1. EMP-1020 creates a key
$userOwner = queryUserByCredentials('EMP-1020');
$tokenOwner = createSsoCookie($userOwner);

$baseUrl = 'http://127.0.0.1/Full-11-Projects-FrontEnd';

// Fetch CSRF for owner
$chCsrf = curl_init($baseUrl . '/api/csrf.php');
curl_setopt($chCsrf, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chCsrf, CURLOPT_HEADER, true);
curl_setopt($chCsrf, CURLOPT_HTTPHEADER, ['Cookie: vostok_sso_token=' . $tokenOwner]);
$rawCsrf = (string)curl_exec($chCsrf);
$headerSize = curl_getinfo($chCsrf, CURLINFO_HEADER_SIZE);
$headerStr = substr($rawCsrf, 0, $headerSize);
$bodyStr = substr($rawCsrf, $headerSize);
preg_match('/PHPSESSID=([^;]+)/', $headerStr, $m);
$phpsessidOwner = $m[1] ?? '';
$csrfDataOwner = json_decode($bodyStr, true) ?? [];
$csrfTokenOwner = $csrfDataOwner['csrf_token'] ?? '';

// Create key
$ch = curl_init($baseUrl . '/Developer/api/credentials.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Cookie: vostok_sso_token=' . $tokenOwner . '; PHPSESSID=' . $phpsessidOwner,
    'X-CSRF-Token: ' . $csrfTokenOwner
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'action' => 'create',
    'label' => 'EMP-1020 Key',
    'environment' => 'Production'
]));
$body = (string)curl_exec($ch);
$created = json_decode($body, true);
$keyId = (int)($created['data']['id'] ?? 0);
echo "Created key ID: {$keyId}\n";

// 2. EMP-1017 (non-owner) attempts to revoke the key
$userNonOwner = queryUserByCredentials('EMP-1017');
$tokenNonOwner = createSsoCookie($userNonOwner);

$chCsrf2 = curl_init($baseUrl . '/api/csrf.php');
curl_setopt($chCsrf2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chCsrf2, CURLOPT_HEADER, true);
curl_setopt($chCsrf2, CURLOPT_HTTPHEADER, ['Cookie: vostok_sso_token=' . $tokenNonOwner]);
$rawCsrf2 = (string)curl_exec($chCsrf2);
$headerSize2 = curl_getinfo($chCsrf2, CURLINFO_HEADER_SIZE);
$headerStr2 = substr($rawCsrf2, 0, $headerSize2);
$bodyStr2 = substr($rawCsrf2, $headerSize2);
preg_match('/PHPSESSID=([^;]+)/', $headerStr2, $m2);
$phpsessidNonOwner = $m2[1] ?? '';
$csrfDataNonOwner = json_decode($bodyStr2, true) ?? [];
$csrfTokenNonOwner = $csrfDataNonOwner['csrf_token'] ?? '';

// Revoke attempt by non-owner
$chRevoke = curl_init($baseUrl . '/Developer/api/credentials.php');
curl_setopt($chRevoke, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chRevoke, CURLOPT_POST, true);
curl_setopt($chRevoke, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Cookie: vostok_sso_token=' . $tokenNonOwner . '; PHPSESSID=' . $phpsessidNonOwner,
    'X-CSRF-Token: ' . $csrfTokenNonOwner
]);
curl_setopt($chRevoke, CURLOPT_POSTFIELDS, json_encode([
    'action' => 'revoke',
    'id' => $keyId
]));
$bodyRevoke = (string)curl_exec($chRevoke);
$codeRevoke = curl_getinfo($chRevoke, CURLINFO_HTTP_CODE);
echo "Non-owner revoke HTTP: {$codeRevoke}\n";
echo "Response: {$bodyRevoke}\n";

// Delete attempt by non-owner
$chDel = curl_init($baseUrl . '/Developer/api/credentials.php');
curl_setopt($chDel, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chDel, CURLOPT_POST, true);
curl_setopt($chDel, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Cookie: vostok_sso_token=' . $tokenNonOwner . '; PHPSESSID=' . $phpsessidNonOwner,
    'X-CSRF-Token: ' . $csrfTokenNonOwner
]);
curl_setopt($chDel, CURLOPT_POSTFIELDS, json_encode([
    'action' => 'delete',
    'id' => $keyId
]));
$bodyDel = (string)curl_exec($chDel);
$codeDel = curl_getinfo($chDel, CURLINFO_HTTP_CODE);
echo "Non-owner delete HTTP: {$codeDel}\n";
echo "Response: {$bodyDel}\n";

// 3. EMP-1020 (owner) revokes and deletes the key
$chRevokeOwner = curl_init($baseUrl . '/Developer/api/credentials.php');
curl_setopt($chRevokeOwner, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chRevokeOwner, CURLOPT_POST, true);
curl_setopt($chRevokeOwner, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Cookie: vostok_sso_token=' . $tokenOwner . '; PHPSESSID=' . $phpsessidOwner,
    'X-CSRF-Token: ' . $csrfTokenOwner
]);
curl_setopt($chRevokeOwner, CURLOPT_POSTFIELDS, json_encode([
    'action' => 'revoke',
    'id' => $keyId
]));
$bodyRevokeOwner = (string)curl_exec($chRevokeOwner);
$codeRevokeOwner = curl_getinfo($chRevokeOwner, CURLINFO_HTTP_CODE);
echo "Owner revoke HTTP: {$codeRevokeOwner}\n";
echo "Response: {$bodyRevokeOwner}\n";

$chDelOwner = curl_init($baseUrl . '/Developer/api/credentials.php');
curl_setopt($chDelOwner, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chDelOwner, CURLOPT_POST, true);
curl_setopt($chDelOwner, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Cookie: vostok_sso_token=' . $tokenOwner . '; PHPSESSID=' . $phpsessidOwner,
    'X-CSRF-Token: ' . $csrfTokenOwner
]);
curl_setopt($chDelOwner, CURLOPT_POSTFIELDS, json_encode([
    'action' => 'delete',
    'id' => $keyId
]));
$bodyDelOwner = (string)curl_exec($chDelOwner);
$codeDelOwner = curl_getinfo($chDelOwner, CURLINFO_HTTP_CODE);
echo "Owner delete HTTP: {$codeDelOwner}\n";
echo "Response: {$bodyDelOwner}\n";
