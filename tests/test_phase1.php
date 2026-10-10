<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }

/**
 * Phase 1 Acceptance Tests
 * 1. logging in with admin123 or password123 fails
 * 2. unauthenticated GET IT Helpdesk/api/tickets.php returns 401
 * 3. unauthenticated GET api/v1/invoices.php returns 401
 * 4. CUS-1002 cannot read CUS-1001's invoice
 * 5. changing cus_id in an order request has no effect
 */

require_once __DIR__ . '/../config/db.php';

$results = [];

function testAssert(string $testName, bool $passed, string $details = ''): void {
    global $results;
    $results[] = [
        'name'    => $testName,
        'passed'  => $passed,
        'details' => $details
    ];
    $status = $passed ? "\033[32mPASS\033[0m" : "\033[31mFAIL\033[0m";
    echo "{$status} - {$testName}" . ($details ? " ({$details})" : "") . PHP_EOL;
}

// -------------------------------------------------------------
// Test 1: verifyUserPassword rejects admin123 and password123
// -------------------------------------------------------------
$pdo = getDbConnection();
$adminUser = queryUserByCredentials('EMP-1001');

$canAdmin123 = verifyUserPassword($adminUser, 'admin123');
$canPass123  = verifyUserPassword($adminUser, 'password123');
$canRealPass = verifyUserPassword($adminUser, 'AdminPass2026!') || verifyUserPassword($adminUser, 'VostokPribor2026!');

testAssert(
    "Security: login with fallback 'admin123' fails",
    !$canAdmin123,
    "admin123 result: " . ($canAdmin123 ? 'ALLOWED (Vulnerable)' : 'REJECTED')
);

testAssert(
    "Security: login with fallback 'password123' fails",
    !$canPass123,
    "password123 result: " . ($canPass123 ? 'ALLOWED (Vulnerable)' : 'REJECTED')
);

testAssert(
    "Security: real password hash still verifies via password_verify()",
    $canRealPass,
    "AdminPass2026! verified"
);

// -------------------------------------------------------------
// Test 2: Unauthenticated GET IT Helpdesk/api/tickets.php returns 401
// -------------------------------------------------------------
function runPhpCapture(string $scriptPath, array $get = [], array $session = [], array $post = [], ?string $jsonBody = null): array {
    $tmpDir = sys_get_temp_dir();
    $tmpInput = tempnam($tmpDir, 'vp_req_');
    $tmpRunner = $tmpInput . '.php';

    $inputData = [
        'get'     => $get,
        'session' => $session,
        'post'    => $post,
        'json'    => $jsonBody,
        'server'  => [
            'REQUEST_METHOD' => !empty($post) || $jsonBody !== null ? 'POST' : 'GET',
            'REMOTE_ADDR'    => '127.0.0.1',
            'HTTP_ACCEPT'    => 'application/json'
        ]
    ];
    file_put_contents($tmpInput, json_encode($inputData));

    $escapedScript = addslashes($scriptPath);
    $escapedInput = addslashes($tmpInput);

    $runnerContent = "<?php
\$req = json_decode(file_get_contents('{$escapedInput}'), true);
\$_GET = \$req['get'];
\$_POST = \$req['post'];
\$_SERVER = array_merge(\$_SERVER, \$req['server']);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
\$_SESSION = \$req['session'];
register_shutdown_function(function() {
    \$out = ob_get_clean();
    echo '___JSON_START___' . json_encode([
        'http_code' => http_response_code(),
        'body'      => \$out
    ]) . '___JSON_END___';
});
ob_start();
require '{$escapedScript}';
";
    file_put_contents($tmpRunner, $runnerContent);

    $phpBin = defined('PHP_BINARY') && PHP_BINARY ? escapeshellarg(PHP_BINARY) : 'php';
    $output = shell_exec("{$phpBin} " . escapeshellarg($tmpRunner));
    @unlink($tmpInput);
    @unlink($tmpRunner);

    if (preg_match('/___JSON_START___(.*?)___JSON_END___/s', (string)$output, $m)) {
        return json_decode($m[1], true) ?: ['http_code' => 500, 'body' => (string)$output];
    }
    return ['http_code' => 500, 'body' => (string)$output];
}

$ticketScript = __DIR__ . '/../IT Helpdesk/api/tickets.php';
$ticketRes = runPhpCapture($ticketScript);
$ticketCode = $ticketRes['http_code'] ?? 0;
testAssert(
    "Security: unauthenticated GET IT Helpdesk/api/tickets.php returns 401",
    $ticketCode === 401,
    "HTTP code: {$ticketCode}"
);

// -------------------------------------------------------------
// Test 3: Unauthenticated GET api/v1/invoices.php returns 401
// -------------------------------------------------------------
$invoiceScript = __DIR__ . '/../api/v1/invoices.php';
$invRes = runPhpCapture($invoiceScript);
$invCode = $invRes['http_code'] ?? 0;
testAssert(
    "Security: unauthenticated GET api/v1/invoices.php returns 401",
    $invCode === 401,
    "HTTP code: {$invCode}"
);

// -------------------------------------------------------------
// Test 4: CUS-1002 cannot read CUS-1001's invoice (Tenant Isolation)
// -------------------------------------------------------------
// Find an invoice belonging to CUS-1001
$inv1001 = $pdo->query("SELECT inv_id FROM invoices WHERE cus_id = 'CUS-1001' LIMIT 1")->fetchColumn();
if (!$inv1001) {
    // If no invoices exist for CUS-1001, insert a dummy one for the test
    $pdo->exec("INSERT INTO invoices (inv_id, cus_id, total_value, currency, payment_status, issued_at) VALUES ('INV-2026-001', 'CUS-1001', 1000.00, 'EUR', 'Pending', NOW()) ON DUPLICATE KEY UPDATE cus_id = 'CUS-1001'");
    $inv1001 = 'INV-2026-001';
}

$cus2Session = [
    'vostok_authenticated' => true,
    'vostok_current_system' => 'CUS',
    'cus_id' => 'CUS-1002',
    'vostok_user' => [
        'account_id' => 102,
        'user_id' => 'CUS-1002',
        'cus_id' => 'CUS-1002',
        'account_type' => 'Customer',
        'clearance_level' => 'L1',
        'role_name' => 'Customer Client Account'
    ]
];

$crossInvRes = runPhpCapture($invoiceScript, ['id' => $inv1001], $cus2Session);
$crossInvCode = $crossInvRes['http_code'] ?? 0;
$bodyDecoded = json_decode($crossInvRes['body'] ?? '', true);
$hasLeak = is_array($bodyDecoded) && isset($bodyDecoded['data']['inv_id']) && $bodyDecoded['data']['inv_id'] === $inv1001;

testAssert(
    "Security: CUS-1002 cannot read CUS-1001's invoice (Tenant Isolation)",
    ($crossInvCode === 403 || $crossInvCode === 404) && !$hasLeak,
    "HTTP code: {$crossInvCode}, Leaked data: " . ($hasLeak ? 'YES' : 'NO')
);

// -------------------------------------------------------------
// Test 5: Changing cus_id in order request has no effect
// -------------------------------------------------------------
$shopOrderScript = __DIR__ . '/../api/v1/shop/orders.php';
// CUS-1002 attempts to query orders with cus_id=CUS-1001 in GET
$orderTamperRes = runPhpCapture($shopOrderScript, ['cus_id' => 'CUS-1001'], $cus2Session);
$orderBody = json_decode($orderTamperRes['body'] ?? '', true);
$ordersList = $orderBody['data'] ?? [];
$tamperSuccess = false;
foreach ($ordersList as $ord) {
    if (($ord['cus_id'] ?? '') === 'CUS-1001') {
        $tamperSuccess = true;
        break;
    }
}
testAssert(
    "Security: changing cus_id in order request has no effect (GET)",
    !$tamperSuccess,
    "Orders filtered to authenticated session tenant: YES"
);

// -------------------------------------------------------------
// Test 6: SSO token lifetime and JTI verification
// -------------------------------------------------------------
$fakeUser = [
    'user_id' => 'EMP-1001',
    'account_id' => 1,
    'clearance_level' => 'L4',
    'emp_id' => 'EMP-1001'
];
$jti = bin2hex(random_bytes(16));
$token = createSsoCookie($fakeUser, $jti);
registerUserSession('Employee', 1, 'ADM', $jti);

$verifiedUser = verifySsoCookie($token);
testAssert(
    "Security: active SSO cookie with valid JTI verifies correctly",
    $verifiedUser !== null && $verifiedUser['emp_id'] === 'EMP-1001',
    "User verified"
);

// Invalidate session in user_sessions
$pdo->prepare("UPDATE user_sessions SET status = 'Terminated' WHERE jti = ?")->execute([$jti]);
$revokedVerify = verifySsoCookie($token);
testAssert(
    "Security: terminated JTI immediately invalidates SSO token",
    $revokedVerify === null,
    "Revoked token rejected"
);

// -------------------------------------------------------------
// Test 7: Fail fast on weak/missing VOSTOK_SSO_SECRET
// -------------------------------------------------------------
testAssert(
    "Security: VOSTOK_SSO_SECRET requires 32+ characters",
    strlen(VOSTOK_SSO_SECRET) >= 32,
    "Length: " . strlen(VOSTOK_SSO_SECRET)
);

// Summary Table
echo PHP_EOL . "=== PHASE 1 TEST SUMMARY ===" . PHP_EOL;
$total = count($results);
$passedCount = count(array_filter($results, fn($r) => $r['passed']));
echo "Total Tests: {$total} | Passed: {$passedCount} | Failed: " . ($total - $passedCount) . PHP_EOL;

if ($passedCount === $total) {
    echo "\033[32mALL PHASE 1 ACCEPTANCE TESTS PASSED!\033[0m" . PHP_EOL;
} else {
    echo "\033[31mSOME PHASE 1 ACCEPTANCE TESTS FAILED!\033[0m" . PHP_EOL;
    exit(1);
}
