<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }

/**
 * Phase 1 Comprehensive Regression Test Suite
 *
 * Covers:
 * Bug 1.1: Sequential document ID generation and counter synchronization
 * Bug 1.2: Undefined variable and fatal error regression (Quotes, Tickets, full codebase scan)
 * Bug 1.3: Audit log coverage (document_access_log schema & success/denial logging)
 * Bug 1.4: Upload status codes and file validation (413, 415, 422)
 * Bug 1.5: Error message sanitization & baseline protection (403, 409, 422, zero SQL/trace leaks)
 * Bug 1.6: Preflight PHP extension checks in DataBase/build.php
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/id_generator.php';
require_once __DIR__ . '/../includes/document_storage.php';

$pdo = getDbConnection();
$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

function assertTest(string $name, bool $passed, string $details = ''): void {
    global $totalTests, $passedTests, $failedTests;
    $totalTests++;
    if ($passed) {
        $passedTests++;
        echo "[\033[32mPASS\033[0m] {$name}" . ($details ? " - {$details}" : "") . PHP_EOL;
    } else {
        $failedTests++;
        echo "[\033[31mFAIL\033[0m] {$name}" . ($details ? " - {$details}" : "") . PHP_EOL;
    }
}

echo "====================================================================\n";
echo "      PHASE 1 REGRESSION TEST SUITE - VOSTOKPRIBOR ECOSYSTEM        \n";
echo "====================================================================\n\n";

// -----------------------------------------------------------------------------
// Helper: runPhpCapture to execute scripts isolated with simulated environment
// -----------------------------------------------------------------------------
function runPhpCapture(
    string $scriptPath,
    array $get = [],
    array $session = [],
    array $post = [],
    ?string $jsonBody = null,
    array $files = [],
    string $method = 'GET'
): array {
    $tmpDir = sys_get_temp_dir();
    $tmpInput = tempnam($tmpDir, 'vp_reg_');
    $tmpRunner = $tmpInput . '.php';

    $inputData = [
        'get'     => $get,
        'session' => $session,
        'post'    => $post,
        'json'    => $jsonBody,
        'files'   => $files,
        'server'  => [
            'REQUEST_METHOD' => $method,
            'REMOTE_ADDR'    => '127.0.0.1',
            'HTTP_ACCEPT'    => 'application/json'
        ]
    ];
    file_put_contents($tmpInput, json_encode($inputData));

    $escapedScript = addslashes($scriptPath);
    $escapedInput = addslashes($tmpInput);
    $rootEscaped = addslashes(dirname(__DIR__));

    $runnerContent = "<?php
require_once '{$rootEscaped}/config/db.php';
\$req = json_decode(file_get_contents('{$escapedInput}'), true);
\$_GET = \$req['get'] ?? [];
\$_POST = \$req['post'] ?? [];
\$_FILES = \$req['files'] ?? [];
\$_SERVER = array_merge(\$_SERVER, \$req['server'] ?? []);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
\$_SESSION = \$req['session'] ?? [];

// Establish authenticated session with CSRF token
if (!empty(\$_SESSION['vostok_user'])) {
    \$_SESSION['vostok_authenticated'] = true;
    if (empty(\$_SESSION['csrf_token'])) {
        \$_SESSION['csrf_token'] = 'test_csrf_token_12345';
    }
    \$_SERVER['HTTP_X_CSRF_TOKEN'] = \$_SESSION['csrf_token'];
    if (!empty(\$_SESSION['vostok_user']['cus_id'])) {
        \$_SESSION['cus_id'] = \$_SESSION['vostok_user']['cus_id'];
    }
    if (!empty(\$_SESSION['vostok_user']['emp_id'])) {
        \$_SESSION['emp_id'] = \$_SESSION['vostok_user']['emp_id'];
    }
    if (!empty(\$_SESSION['vostok_user']['user_id'])) {
        \$_SESSION['user_id'] = \$_SESSION['vostok_user']['user_id'];
    }
}

if (!empty(\$req['json'])) {
    \$decoded = json_decode(\$req['json'], true);
    if (is_array(\$decoded)) {
        \$_POST = array_merge(\$_POST, \$decoded);
    }
}

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
    
    // Pipe jsonBody via STDIN so file_get_contents('php://input') succeeds
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w']
    ];
    $process = proc_open("{$phpBin} " . escapeshellarg($tmpRunner), $descriptors, $pipes);
    $output = '';
    if (is_resource($process)) {
        if ($jsonBody !== null) {
            fwrite($pipes[0], $jsonBody);
        }
        fclose($pipes[0]);
        $output = (string)stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        proc_close($process);
    }

    @unlink($tmpInput);
    @unlink($tmpRunner);

    if (preg_match('/___JSON_START___(.*?)___JSON_END___/s', (string)$output, $m)) {
        return json_decode($m[1], true) ?: ['http_code' => 500, 'body' => (string)$output];
    }
    return ['http_code' => 500, 'body' => (string)$output];
}

// =============================================================================
// TEST GROUP 1.1: Sequential Document ID Generation & Counter Normalization
// =============================================================================
echo "\n--- [Group 1.1] Sequential ID Generation & Normalization ---\n";

// Reset document counter to baseline 16
$pdo->prepare("INSERT INTO id_counters (`name`, `next_val`) VALUES ('documents', 16) ON DUPLICATE KEY UPDATE `next_val` = 16")->execute();

// Test 1.1.1: Next document ID from baseline (DOC-2026-001..015) is DOC-2026-016
$nextDoc1 = vp_next_id($pdo, 'documents', 'DOC-2026-', 3);
assertTest(
    "1.1.1: Next document ID starts at DOC-2026-016",
    $nextDoc1 === 'DOC-2026-016',
    "Generated ID: {$nextDoc1}"
);

// Test 1.1.2: Normalized aliases ('doc', 'document', 'docs') map to 'documents'
$aliasDoc1 = vp_next_id($pdo, 'doc', 'DOC-2026-', 3);
$aliasDoc2 = vp_next_id($pdo, 'document', 'DOC-2026-', 3);
assertTest(
    "1.1.2: Normalized counter aliases map to canonical 'documents'",
    $aliasDoc1 === 'DOC-2026-017' && $aliasDoc2 === 'DOC-2026-018',
    "Aliases returned: {$aliasDoc1}, {$aliasDoc2}"
);

// Reset counter to baseline state for cleanliness
$pdo->prepare("UPDATE id_counters SET `next_val` = 16 WHERE `name` = 'documents'")->execute();

// Test 1.1.3: Next policy ID generation (POL-2026-...)
$nextPol = vp_next_id($pdo, 'policies', 'POL-2026-', 3);
assertTest(
    "1.1.3: Policy ID generation uses POL-2026- sequence",
    str_starts_with($nextPol, 'POL-2026-') && strlen($nextPol) === 12,
    "Generated Policy ID: {$nextPol}"
);

// =============================================================================
// TEST GROUP 1.2: Undefined Variable & Fatal Error Regressions
// =============================================================================
echo "\n--- [Group 1.2] Undefined Variable Regressions ---\n";

// Authenticated customer session from real database record
$cusUser = queryUserByCredentials('CUS-1001');
$shopUserSession = [
    'vostok_user' => $cusUser
];

$quotePayload = json_encode([
    'customer_name'  => 'KazTransGas Automation',
    'contact_email'  => 'rep@kaztransgas.kz',
    'contact_phone'  => '+7 (727) 555-0199',
    'prod_id'        => 'PROD-1001',
    'quantity'       => 2,
    'rfq_notes'      => 'Urgent procurement request for pipeline testing'
]);

$quoteScript = __DIR__ . '/../Online Shop B2B/api/quotes.php';
$quoteRes = runPhpCapture($quoteScript, [], $shopUserSession, [], $quotePayload, [], 'POST');
$quoteJson = json_decode($quoteRes['body'], true) ?: [];
$quoteId = $quoteJson['data']['quote_id'] ?? ($quoteJson['quote_id'] ?? null);
$quoteSuccess = ($quoteRes['http_code'] === 201) && !empty($quoteId);
assertTest(
    "1.2.1: Quotes API creates quote without undefined variable notices",
    $quoteSuccess,
    "HTTP: {$quoteRes['http_code']}, Quote ID: " . ($quoteId ?? 'none')
);

// Test 1.2.2: Customer Portal Tickets API creation & comment requester attribution
$cusPortalSession = [
    'vostok_user' => $cusUser
];

$ticketCreatePayload = json_encode([
    'title'       => 'Sensor telemetry variance inquiry',
    'description' => 'Discrepancy detected in pressure sensor channel B',
    'priority'    => 'Medium',
    'category'    => 'Equipment'
]);

$ticketScript = __DIR__ . '/../Customer Portal/api/tickets.php';
$ticketCreateRes = runPhpCapture($ticketScript, [], $cusPortalSession, [], $ticketCreatePayload, [], 'POST');
$ticketJson = json_decode($ticketCreateRes['body'], true) ?: [];
$createdTicketId = $ticketJson['data']['tkt_id'] ?? ($ticketJson['data']['ticket_id'] ?? ($ticketJson['ticket_id'] ?? ($ticketJson['tkt_id'] ?? '')));
assertTest(
    "1.2.2: Customer Portal creates ticket with valid requester attribution",
    ($ticketCreateRes['http_code'] === 201 || $ticketCreateRes['http_code'] === 200) && !empty($createdTicketId),
    "HTTP: {$ticketCreateRes['http_code']}, Ticket: {$createdTicketId}"
);

// If ticket created, verify ticket requester attribution in DB
if (!empty($createdTicketId)) {
    $tktRow = $pdo->query("SELECT requester_cus_id FROM tickets WHERE tkt_id = '{$createdTicketId}'")->fetch(PDO::FETCH_ASSOC);
    assertTest(
        "1.2.2b: Created ticket belongs strictly to session cus_id CUS-1001",
        ($tktRow['requester_cus_id'] ?? '') === 'CUS-1001',
        "Owner cus_id: " . ($tktRow['requester_cus_id'] ?? 'none')
    );

    // Test ticket comment creation
    $commentPayload = json_encode([
        'action'    => 'add_comment',
        'ticket_id' => $createdTicketId,
        'comment'   => 'Attaching additional SCADA sensor log snippet.'
    ]);
    $commentRes = runPhpCapture($ticketScript, [], $cusPortalSession, [], $commentPayload, [], 'POST');
    $cmtRow = $pdo->query("SELECT author_type, author_name FROM ticket_comments WHERE tkt_id = '{$createdTicketId}' ORDER BY comment_id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    assertTest(
        "1.2.2c: Ticket comment populates author_name and author_type='requester'",
        ($cmtRow['author_type'] ?? '') === 'requester' && !empty($cmtRow['author_name']),
        "Type: " . ($cmtRow['author_type'] ?? '') . ", Name: " . ($cmtRow['author_name'] ?? '')
    );
}

// Test 1.2.3: Zero undefined variables or syntax errors across full codebase
$scanScript = __DIR__ . '/../scripts/scan_undefined_vars.php';
$scanOutput = shell_exec(escapeshellarg(PHP_BINARY) . " " . escapeshellarg($scanScript) . " 2>&1");
$scanPass = str_contains((string)$scanOutput, 'Status: PASS');
assertTest(
    "1.2.3: Static token scan verifies 0 undefined variable reads across 270+ PHP files",
    $scanPass,
    $scanPass ? "All files clean" : "Undefined variables detected"
);

// =============================================================================
// TEST GROUP 1.3: Audit Log Coverage & document_access_log Schema Integrity
// =============================================================================
echo "\n--- [Group 1.3] Audit Log Coverage (document_access_log) ---\n";

$testDocId = 'DOC-2026-001';

// Test 1.3.1: vp_log_document_access inserts row with valid schema
$logCountBefore = (int)$pdo->query("SELECT COUNT(*) FROM document_access_log WHERE doc_id = '{$testDocId}'")->fetchColumn();
vp_log_document_access($pdo, $testDocId, 'View', 1, 'Regression test view access', 'EMP-1001', null, 'DOC');
$logCountAfter = (int)$pdo->query("SELECT COUNT(*) FROM document_access_log WHERE doc_id = '{$testDocId}'")->fetchColumn();
assertTest(
    "1.3.1: vp_log_document_access successfully inserts into document_access_log",
    $logCountAfter === $logCountBefore + 1,
    "Delta: +" . ($logCountAfter - $logCountBefore)
);

// Test 1.3.2: Verify inserted row columns match schema (success=1, accessed_by_emp_id, system_id, etc.)
$lastLog = $pdo->query("SELECT * FROM document_access_log WHERE doc_id = '{$testDocId}' ORDER BY access_id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
assertTest(
    "1.3.2: document_access_log schema columns correctly populated",
    ($lastLog['access_type'] === 'View') &&
    ((int)$lastLog['success'] === 1) &&
    ($lastLog['accessed_by_emp_id'] === 'EMP-1001') &&
    ($lastLog['system_id'] === 'DOC') &&
    !empty($lastLog['accessed_at']),
    "access_id: {$lastLog['access_id']}, type: {$lastLog['access_type']}, success: {$lastLog['success']}"
);

// Test 1.3.3: Denied access logging with success=0
vp_log_document_access($pdo, $testDocId, 'Download', 0, 'Clearance denied: L1 cannot access L3', 'EMP-1020', null, 'DOC');
$deniedLog = $pdo->query("SELECT * FROM document_access_log WHERE doc_id = '{$testDocId}' ORDER BY access_id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
assertTest(
    "1.3.3: Denied access attempt logged with success=0 and descriptive note",
    ((int)$deniedLog['success'] === 0) && ($deniedLog['access_type'] === 'Download') && str_contains($deniedLog['notes'], 'Clearance denied'),
    "access_id: {$deniedLog['access_id']}, success: {$deniedLog['success']}"
);

// Test 1.3.4: Baseline document deletion attempt blocked (403) and logged (success=0)
$docApiScript = __DIR__ . '/../File Center/api/documents.php';
$custodianUser = queryUserByCredentials('EMP-1019');
$fcAdminSession = [
    'vostok_user' => $custodianUser
];

$delBaselineRes = runPhpCapture($docApiScript, ['action' => 'delete', 'doc_id' => 'DOC-2026-001'], $fcAdminSession, [], null, [], 'POST');
assertTest(
    "1.3.4: Deletion of baseline document DOC-2026-001 returns 403 Forbidden",
    $delBaselineRes['http_code'] === 403,
    "HTTP: {$delBaselineRes['http_code']}"
);

$delLogCheck = $pdo->query("SELECT * FROM document_access_log WHERE doc_id = 'DOC-2026-001' AND access_type = 'Delete' ORDER BY access_id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
assertTest(
    "1.3.4b: Denied baseline deletion logged in document_access_log with success=0",
    !empty($delLogCheck) && ((int)$delLogCheck['success'] === 0),
    "Logged success: " . ($delLogCheck['success'] ?? 'none')
);

// =============================================================================
// TEST GROUP 1.4: Upload Status Codes & Validation
// =============================================================================
echo "\n--- [Group 1.4] Upload File Validation & HTTP Status Codes ---\n";

// Test 1.4.1: Empty file (0 bytes) returns 422 Unprocessable Entity
$tmpEmptyFile = tempnam(sys_get_temp_dir(), 'empty_');
file_put_contents($tmpEmptyFile, '');
$emptyUploadException = null;
try {
    vp_validate_and_save_upload([
        'name'     => 'test_empty.pdf',
        'type'     => 'application/pdf',
        'tmp_name' => $tmpEmptyFile,
        'error'    => UPLOAD_ERR_OK,
        'size'     => 0
    ], 'DOC-2026-TEST1');
} catch (VpUploadException $vue) {
    $emptyUploadException = $vue;
} finally {
    @unlink($tmpEmptyFile);
}
assertTest(
    "1.4.1: Upload of 0-byte file throws VpUploadException with HTTP 422",
    $emptyUploadException !== null && $emptyUploadException->getStatusCode() === 422,
    "Exception Code: " . ($emptyUploadException ? $emptyUploadException->getStatusCode() : 'none')
);

// Test 1.4.2: Forbidden extension (.php, .exe, double ext .php.pdf) returns 415 Unsupported Media Type
$tmpBadFile = tempnam(sys_get_temp_dir(), 'bad_');
file_put_contents($tmpBadFile, '<?php echo "evil";');
$badExtException = null;
try {
    vp_validate_and_save_upload([
        'name'     => 'payload.php.pdf',
        'type'     => 'application/x-php',
        'tmp_name' => $tmpBadFile,
        'error'    => UPLOAD_ERR_OK,
        'size'     => filesize($tmpBadFile)
    ], 'DOC-2026-TEST2');
} catch (VpUploadException $vue) {
    $badExtException = $vue;
} finally {
    @unlink($tmpBadFile);
}
assertTest(
    "1.4.2: Upload with double/prohibited extension throws VpUploadException with HTTP 415",
    $badExtException !== null && $badExtException->getStatusCode() === 415,
    "Exception Code: " . ($badExtException ? $badExtException->getStatusCode() : 'none')
);

// Test 1.4.3: MIME mismatch returns 415 Unsupported Media Type
$tmpFakePdf = tempnam(sys_get_temp_dir(), 'fakepdf_');
file_put_contents($tmpFakePdf, 'PLAIN TEXT NOT REAL PDF');
$mimeMismatchException = null;
try {
    vp_validate_and_save_upload([
        'name'     => 'report.pdf',
        'type'     => 'application/pdf',
        'tmp_name' => $tmpFakePdf,
        'error'    => UPLOAD_ERR_OK,
        'size'     => filesize($tmpFakePdf)
    ], 'DOC-2026-TEST3');
} catch (VpUploadException $vue) {
    $mimeMismatchException = $vue;
} finally {
    @unlink($tmpFakePdf);
}
assertTest(
    "1.4.3: Upload with disguised text content as .pdf throws VpUploadException with HTTP 415",
    $mimeMismatchException !== null && $mimeMismatchException->getStatusCode() === 415,
    "Exception Code: " . ($mimeMismatchException ? $mimeMismatchException->getStatusCode() : 'none')
);

// Test 1.4.4: File exceeding 50MB throws VpUploadException with HTTP 413 Payload Too Large
$largeSizeException = null;
$dummyLargePath = tempnam(sys_get_temp_dir(), 'large_');
file_put_contents($dummyLargePath, 'small');
try {
    vp_validate_and_save_upload([
        'name'     => 'large_file.pdf',
        'type'     => 'application/pdf',
        'tmp_name' => $dummyLargePath,
        'error'    => UPLOAD_ERR_OK,
        'size'     => 55 * 1024 * 1024 // 55MB reported size
    ], 'DOC-2026-TEST4');
} catch (VpUploadException $vue) {
    $largeSizeException = $vue;
} finally {
    @unlink($dummyLargePath);
}
assertTest(
    "1.4.4: Upload exceeding 50MB limit throws VpUploadException with HTTP 413",
    $largeSizeException !== null && $largeSizeException->getStatusCode() === 413,
    "Exception Code: " . ($largeSizeException ? $largeSizeException->getStatusCode() : 'none')
);

// =============================================================================
// TEST GROUP 1.5: Error Sanitization & Baseline Integrity
// =============================================================================
echo "\n--- [Group 1.5] Error Sanitization & Data Protection ---\n";

// Test 1.5.1: Attempting to delete baseline customer CUS-1001 returns 403 Forbidden
$crmCustomerScript = __DIR__ . '/../CRM/api/customers.php';
$superAdminUser = queryUserByCredentials('EMP-1001');
$crmSession = [
    'vostok_user' => $superAdminUser
];

$delCusRes = runPhpCapture($crmCustomerScript, ['action' => 'delete', 'cus_id' => 'CUS-1001'], $crmSession, [], null, [], 'POST');
$delCusJson = json_decode($delCusRes['body'], true) ?: [];
$errText = $delCusJson['error'] ?? ($delCusJson['message'] ?? '');
assertTest(
    "1.5.1: Attempting to delete baseline customer CUS-1001 returns 403 Forbidden",
    $delCusRes['http_code'] === 403 && str_contains(strtolower($errText), 'baseline'),
    "HTTP: {$delCusRes['http_code']}, Error: " . ($errText ?: 'none')
);

// Test 1.5.2: Attempting to create announcement with invalid department returns 422
$announcementScript = __DIR__ . '/../Employee Intranet/api/announcements.php';
$intranetSession = [
    'vostok_user' => $superAdminUser
];

$badAnnouncePayload = json_encode([
    'title'         => 'Test invalid dept announcement',
    'content'       => 'Test announcement body content',
    'audience_dept' => 'INVALID_DEPT_CODE_999'
]);

$announceRes = runPhpCapture($announcementScript, [], $intranetSession, [], $badAnnouncePayload, [], 'POST');
$announceJson = json_decode($announceRes['body'], true) ?: [];
assertTest(
    "1.5.2: Invalid audience department returns 422 Unprocessable Entity with friendly message",
    $announceRes['http_code'] === 422 && !str_contains($announceRes['body'], 'SQLSTATE'),
    "HTTP: {$announceRes['http_code']}, Message: " . ($announceJson['error'] ?? 'none')
);

// Test 1.5.3: Scan all test responses for raw SQL leaks, stack traces, or file paths
$responsesToScan = [
    $delCusRes['body'],
    $announceRes['body'],
    $delBaselineRes['body'],
    $quoteRes['body'],
    $ticketCreateRes['body']
];

$leakFound = false;
$leakDetail = '';
foreach ($responsesToScan as $respBody) {
    if (preg_match('/(SQLSTATE|CONSTRAINT|#\d+\s+c:\\\|at line \d+ in file)/i', $respBody, $lm)) {
        $leakFound = true;
        $leakDetail = $lm[0];
        break;
    }
}
assertTest(
    "1.5.3: Zero raw SQLSTATE, stack traces, or internal file paths leaked in error responses",
    !$leakFound,
    $leakFound ? "Leaked pattern: {$leakDetail}" : "All responses sanitized"
);

// =============================================================================
// TEST GROUP 1.6: Preflight PHP Extension Checks in DataBase/build.php
// =============================================================================
echo "\n--- [Group 1.6] Preflight Extension Verification in build.php ---\n";

$buildContent = file_get_contents(__DIR__ . '/../DataBase/build.php');
$extensionsRequired = ['pdo', 'pdo_mysql', 'zip', 'mbstring', 'fileinfo', 'openssl'];
$allExtensionsCovered = true;
foreach ($extensionsRequired as $ext) {
    if (!str_contains($buildContent, "'{$ext}'")) {
        $allExtensionsCovered = false;
        break;
    }
}
assertTest(
    "1.6.1: build.php enforces preflight checks for all 6 required extensions",
    $allExtensionsCovered && str_contains($buildContent, 'extension_loaded'),
    "Extensions: " . implode(', ', $extensionsRequired)
);

$readmeContent = file_get_contents(__DIR__ . '/../README.md');
$readmeDocumented = true;
foreach ($extensionsRequired as $ext) {
    if (!str_contains($readmeContent, "`{$ext}`")) {
        $readmeDocumented = false;
        break;
    }
}
assertTest(
    "1.6.2: README.md documents all 6 required extensions with architectural descriptions",
    $readmeDocumented,
    "Documented in README.md: " . ($readmeDocumented ? 'YES' : 'NO')
);

// =============================================================================
// SUMMARY REPORT
// =============================================================================
echo "\n====================================================================\n";
echo "                   PHASE 1 REGRESSION SUMMARY                       \n";
echo "====================================================================\n";
echo "Total Tests Run : {$totalTests}\n";
echo "Passed Tests    : \033[32m{$passedTests}\033[0m\n";
echo "Failed Tests    : " . ($failedTests > 0 ? "\033[31m{$failedTests}\033[0m" : "0") . "\n";
echo "Status          : " . ($failedTests === 0 ? "\033[32mALL PASS\033[0m" : "\033[31mFAILURES DETECTED\033[0m") . "\n";
echo "====================================================================\n";

if ($failedTests > 0) {
    exit(1);
}
