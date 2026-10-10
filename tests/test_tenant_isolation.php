<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }

/**
 * Phase 1.3 Automated Customer Tenant Isolation Test
 * Logged in as CUS-1001:
 * Requests for CUS-1002's inv_id, prj_id, tkt_id, doc_id
 * (with and without cus_id params) must return 403 or 404 and never leak data.
 */

require_once __DIR__ . '/../config/db.php';

$pdo = getDbConnection();
$results = [];

function testTenantAssert(string $name, bool $passed, string $details = ''): void {
    global $results;
    $results[] = ['name' => $name, 'passed' => $passed, 'details' => $details];
    $status = $passed ? "\033[32mPASS\033[0m" : "\033[31mFAIL\033[0m";
    echo "{$status} - {$name}" . ($details ? " ({$details})" : "") . PHP_EOL;
}

function runIsolatedRequest(string $scriptPath, array $get = [], array $session = [], array $post = [], ?string $jsonBody = null): array {
    $tmpDir = sys_get_temp_dir();
    $tmpInput = tempnam($tmpDir, 'vp_iso_');
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

// Fetch real entity IDs for CUS-1002
$cus1002Inv = $pdo->query("SELECT inv_id FROM invoices WHERE cus_id = 'CUS-1002' LIMIT 1")->fetchColumn() ?: 'INV-2026-002';
$cus1002Prj = $pdo->query("SELECT prj_id FROM projects WHERE cus_id = 'CUS-1002' LIMIT 1")->fetchColumn() ?: 'PRJ-2026-002';
$cus1002Tkt = $pdo->query("SELECT tkt_id FROM tickets WHERE requester_cus_id = 'CUS-1002' LIMIT 1")->fetchColumn() ?: 'TKT-2026-001';
$cus1002Doc = $pdo->query("SELECT doc_id FROM documents WHERE related_cus_id = 'CUS-1002' OR customer_ref = 'CUS-1002' LIMIT 1")->fetchColumn() ?: 'DOC-2026-002';

$cus1Session = [
    'vostok_authenticated' => true,
    'vostok_current_system' => 'CUS',
    'cus_id' => 'CUS-1001',
    'vostok_user' => [
        'account_id' => 101,
        'user_id' => 'CUS-1001',
        'cus_id' => 'CUS-1001',
        'account_type' => 'Customer',
        'clearance_level' => 'L1',
        'role_name' => 'Customer Client Account'
    ]
];

echo "=== TESTING TENANT ISOLATION (CUS-1001 requesting CUS-1002 resources) ===" . PHP_EOL;

// 1. Invoices
$invScript = __DIR__ . '/../Customer Portal/api/invoices.php';
// without cus_id
$res = runIsolatedRequest($invScript, ['id' => $cus1002Inv], $cus1Session);
$code = $res['http_code'];
$leaked = strpos($res['body'], $cus1002Inv) !== false && !strpos($res['body'], 'denied') && !strpos($res['body'], 'Not found');
testTenantAssert("Invoices: CUS-1001 accessing {$cus1002Inv} returns 403/404 without cus_id", ($code === 403 || $code === 404) && !$leaked, "Code: {$code}");

// with cus_id tampering
$res = runIsolatedRequest($invScript, ['id' => $cus1002Inv, 'cus_id' => 'CUS-1002'], $cus1Session);
$code = $res['http_code'];
$leaked = strpos($res['body'], $cus1002Inv) !== false && !strpos($res['body'], 'denied') && !strpos($res['body'], 'Not found');
testTenantAssert("Invoices: CUS-1001 accessing {$cus1002Inv} returns 403/404 with cus_id=CUS-1002", ($code === 403 || $code === 404) && !$leaked, "Code: {$code}");

// 2. Projects
$prjScript = __DIR__ . '/../Customer Portal/api/projects.php';
// without cus_id
$res = runIsolatedRequest($prjScript, ['id' => $cus1002Prj], $cus1Session);
$code = $res['http_code'];
$leaked = strpos($res['body'], $cus1002Prj) !== false && !strpos($res['body'], 'denied') && !strpos($res['body'], 'Not found');
testTenantAssert("Projects: CUS-1001 accessing {$cus1002Prj} returns 403/404 without cus_id", ($code === 403 || $code === 404) && !$leaked, "Code: {$code}");

// with cus_id tampering
$res = runIsolatedRequest($prjScript, ['id' => $cus1002Prj, 'cus_id' => 'CUS-1002'], $cus1Session);
$code = $res['http_code'];
$leaked = strpos($res['body'], $cus1002Prj) !== false && !strpos($res['body'], 'denied') && !strpos($res['body'], 'Not found');
testTenantAssert("Projects: CUS-1001 accessing {$cus1002Prj} returns 403/404 with cus_id=CUS-1002", ($code === 403 || $code === 404) && !$leaked, "Code: {$code}");

// 3. Tickets
$tktScript = __DIR__ . '/../Customer Portal/api/tickets.php';
// without cus_id
$res = runIsolatedRequest($tktScript, ['id' => $cus1002Tkt], $cus1Session);
$code = $res['http_code'];
$leaked = strpos($res['body'], $cus1002Tkt) !== false && !strpos($res['body'], 'denied') && !strpos($res['body'], 'Not found');
testTenantAssert("Tickets: CUS-1001 accessing {$cus1002Tkt} returns 403/404 without cus_id", ($code === 403 || $code === 404) && !$leaked, "Code: {$code}");

// with cus_id tampering
$res = runIsolatedRequest($tktScript, ['id' => $cus1002Tkt, 'cus_id' => 'CUS-1002'], $cus1Session);
$code = $res['http_code'];
$leaked = strpos($res['body'], $cus1002Tkt) !== false && !strpos($res['body'], 'denied') && !strpos($res['body'], 'Not found');
testTenantAssert("Tickets: CUS-1001 accessing {$cus1002Tkt} returns 403/404 with cus_id=CUS-1002", ($code === 403 || $code === 404) && !$leaked, "Code: {$code}");

// 4. Documents
$docScript = __DIR__ . '/../Customer Portal/api/documents.php';
// without cus_id
$res = runIsolatedRequest($docScript, ['id' => $cus1002Doc], $cus1Session);
$code = $res['http_code'];
$leaked = strpos($res['body'], $cus1002Doc) !== false && !strpos($res['body'], 'denied') && !strpos($res['body'], 'Not found');
testTenantAssert("Documents: CUS-1001 accessing {$cus1002Doc} returns 403/404 without cus_id", ($code === 403 || $code === 404) && !$leaked, "Code: {$code}");

// with cus_id tampering
$res = runIsolatedRequest($docScript, ['id' => $cus1002Doc, 'cus_id' => 'CUS-1002'], $cus1Session);
$code = $res['http_code'];
$leaked = strpos($res['body'], $cus1002Doc) !== false && !strpos($res['body'], 'denied') && !strpos($res['body'], 'Not found');
testTenantAssert("Documents: CUS-1001 accessing {$cus1002Doc} returns 403/404 with cus_id=CUS-1002", ($code === 403 || $code === 404) && !$leaked, "Code: {$code}");

// 5. Employee without CUSTOMER_IMPERSONATE
$empNonAdminSession = [
    'vostok_authenticated' => true,
    'vostok_current_system' => 'CUS',
    'vostok_user' => [
        'account_id' => 15,
        'user_id' => 'EMP-1015',
        'emp_id' => 'EMP-1015',
        'account_type' => 'Employee',
        'clearance_level' => 'L2',
        'role_name' => 'Supply Chain Analyst'
    ]
];
$res = runIsolatedRequest($invScript, ['cus_id' => 'CUS-1002'], $empNonAdminSession);
$code = $res['http_code'];
testTenantAssert("Impersonation: Employee without CUSTOMER_IMPERSONATE cannot view customer data", ($code === 403), "Code: {$code}");

echo PHP_EOL . "=== TENANT ISOLATION SUMMARY ===" . PHP_EOL;
$total = count($results);
$passedCount = count(array_filter($results, fn($r) => $r['passed']));
echo "Total Tests: {$total} | Passed: {$passedCount} | Failed: " . ($total - $passedCount) . PHP_EOL;

if ($passedCount === $total) {
    echo "\033[32mALL TENANT ISOLATION TESTS PASSED!\033[0m" . PHP_EOL;
} else {
    echo "\033[31mSOME TENANT ISOLATION TESTS FAILED!\033[0m" . PHP_EOL;
    exit(1);
}
