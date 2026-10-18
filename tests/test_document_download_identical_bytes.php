<?php
declare(strict_types=1);

/**
 * Regression Test: Phase 1.4 - Document Download Bytes Identical
 *
 * Verifies that:
 * 1. Document DOC-2026-003 downloaded as owner (EMP-1019)
 * 2. Document DOC-2026-003 downloaded as user with clearance (EMP-1001, L4)
 * 3. Document DOC-2026-003 downloaded as authorized customer (CUS-1001)
 * All yield HTTP 200 with 100% identical byte length and SHA-256 hash.
 * 4. Unauthorized customer (CUS-1002) is denied (HTTP 403).
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';

$pdo = getDbConnection();
$baseUrl = getenv('BASE_URL') ?: 'http://127.0.0.1/Full-11-Projects-FrontEnd';

// Ensure test accounts exist and have sessions
function createTestSession(PDO $pdo, string $id, string $type): string
{
    if ($type === 'Employee') {
        $stmt = $pdo->prepare("SELECT * FROM employees e JOIN employee_accounts ea ON e.emp_id = ea.emp_id WHERE e.emp_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $user = [
            'account_id' => (int)$row['account_id'],
            'emp_id' => $row['emp_id'],
            'user_id' => $row['emp_id'],
            'full_name' => $row['full_name'],
            'email' => $row['email'],
            'clearance_level' => $row['clearance_level'],
            'account_type' => 'Employee',
            'department_code' => $row['department_code'],
            'role_name' => 'Staff',
        ];
    } else {
        $stmt = $pdo->prepare("SELECT * FROM customers c JOIN customer_accounts ca ON c.cus_id = ca.cus_id WHERE c.cus_id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $user = [
            'account_id' => (int)$row['account_id'],
            'cus_id' => $row['cus_id'],
            'user_id' => $row['cus_id'],
            'full_name' => $row['contact_name'] ?? ($row['company_name'] ?? 'Customer Contact'),
            'email' => $row['contact_email'] ?? ($row['email'] ?? 'customer@example.com'),
            'clearance_level' => 'L1',
            'account_type' => 'Customer',
            'department_code' => 'CUS',
            'role_name' => 'CustomerUser',
        ];
    }

    return createSsoCookie($user);
}

function fetchDoc(string $baseUrl, string $endpoint, string $token, string $docId): array
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "{$baseUrl}/{$endpoint}?doc_id={$docId}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, "vostok_sso_token={$token}");
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    return ['code' => $code, 'body' => (string)$body, 'len' => strlen((string)$body), 'hash' => hash('sha256', (string)$body)];
}

$tokenOwner = createTestSession($pdo, 'EMP-1019', 'Employee');
$tokenClearance = createTestSession($pdo, 'EMP-1001', 'Employee');
$tokenCustomer = createTestSession($pdo, 'CUS-1001', 'Customer');
$tokenOtherCustomer = createTestSession($pdo, 'CUS-1002', 'Customer');

$docId = 'DOC-2026-003';

echo "Testing document download across roles for {$docId}...\n";

// 1. Owner download
$resOwner = fetchDoc($baseUrl, 'File%20Center/api/download.php', $tokenOwner, $docId);
echo "  [Owner EMP-1019] HTTP {$resOwner['code']} | Bytes: {$resOwner['len']} | Hash: " . substr($resOwner['hash'], 0, 16) . "...\n";

// 2. Clearance download
$resClearance = fetchDoc($baseUrl, 'File%20Center/api/download.php', $tokenClearance, $docId);
echo "  [Clearance EMP-1001] HTTP {$resClearance['code']} | Bytes: {$resClearance['len']} | Hash: " . substr($resClearance['hash'], 0, 16) . "...\n";

// 3. Customer download
$resCustomer = fetchDoc($baseUrl, 'Customer%20Portal/api/download.php', $tokenCustomer, $docId);
echo "  [Customer CUS-1001] HTTP {$resCustomer['code']} | Bytes: {$resCustomer['len']} | Hash: " . substr($resCustomer['hash'], 0, 16) . "...\n";

// 4. Unauthorized Customer
$resUnauthorized = fetchDoc($baseUrl, 'Customer%20Portal/api/download.php', $tokenOtherCustomer, $docId);
echo "  [Unauthorized CUS-1002] HTTP {$resUnauthorized['code']}\n";

$pass = true;
if ($resOwner['code'] !== 200) {
    echo "FAIL: Owner download was not 200\n";
    $pass = false;
}
if ($resClearance['code'] !== 200) {
    echo "FAIL: Clearance download was not 200\n";
    $pass = false;
}
if ($resCustomer['code'] !== 200) {
    echo "FAIL: Customer download was not 200\n";
    $pass = false;
}
if ($resOwner['hash'] !== $resClearance['hash'] || $resOwner['hash'] !== $resCustomer['hash']) {
    echo "FAIL: Downloaded bytes/hash mismatch across roles!\n";
    $pass = false;
}
if ($resUnauthorized['code'] !== 403 && $resUnauthorized['code'] !== 404) {
    echo "FAIL: Unauthorized customer did not receive 403/404\n";
    $pass = false;
}

if ($pass) {
    echo "\nRESULT: PASS (All document download byte hashes 100% identical, unauthorized rejected with {$resUnauthorized['code']})\n";
    exit(0);
} else {
    echo "\nRESULT: FAIL\n";
    exit(1);
}
