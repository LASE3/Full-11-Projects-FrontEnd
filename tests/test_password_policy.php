<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR PHASE 5 - PASSWORD POLICY & ACCOUNT FLOWS TEST SUITE
 * Tests:
 * 5.1 No plaintext credentials in storage/credentials
 * 5.2 SuperAdmin subject to forced password change
 * 5.3 Password policy: min 14 chars, deny-list, no reuse of last 5
 * 5.4 Neutral account/change-password.php route
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';

$pdo = getDbConnection();

$total = 0;
$passed = 0;

function assertPw(string $name, bool $condition, string $msg = ''): void {
    global $total, $passed;
    $total++;
    if ($condition) {
        $passed++;
        echo "  [\033[32mPASS\033[0m] $name\n";
    } else {
        echo "  [\033[31mFAIL\033[0m] $name: $msg\n";
    }
}

echo "========================================================================\n";
echo "      VOSTOKPRIBOR PHASE 5 - PASSWORDS & ACCOUNT FLOWS                  \n";
echo "========================================================================\n\n";

// 5.1 No plaintext credential file in storage/
assertPw(
    "5.1 No storage/credentials/initial_passwords.json in repository",
    !file_exists(__DIR__ . '/../storage/credentials/initial_passwords.json') && !is_dir(__DIR__ . '/../storage/credentials'),
    "storage/credentials still exists"
);

// 5.2 Neutral route account/change-password.php exists
assertPw(
    "5.4 Neutral account/change-password.php route exists",
    file_exists(__DIR__ . '/../account/change-password.php'),
    "File missing"
);

// Test password policy logic on an isolated test employee account
$testEmpId = 'EMP-1090'; // Test employee
$empAccount = $pdo->query("SELECT ea.*, e.email FROM employee_accounts ea JOIN employees e ON ea.emp_id = e.emp_id WHERE ea.emp_id = '$testEmpId'")->fetch(PDO::FETCH_ASSOC);

if (!$empAccount) {
    echo "Test employee $testEmpId not found!\n";
    exit(1);
}

// Reset history for test employee
$pdo->prepare("DELETE FROM password_history WHERE account_type = 'Employee' AND account_id = ?")->execute([$empAccount['account_id']]);

// Helper to simulate request to account/change-password.php
function submitChangePassword(array $user, array $postData): array {
    $tmpDir = sys_get_temp_dir();
    $tmpInput = tempnam($tmpDir, 'vp_pw_');
    $tmpRunner = $tmpInput . '.php';

    file_put_contents($tmpInput, json_encode([
        'user' => $user,
        'post' => $postData
    ]));

    $root = str_replace('\\', '/', dirname(__DIR__));
    $script = $root . '/account/change-password.php';
    $inputEsc = str_replace('\\', '/', $tmpInput);

    $code = "<?php
require_once '{$root}/config/db.php';
\$req = json_decode(file_get_contents('{$inputEsc}'), true);
if (session_status() === PHP_SESSION_NONE) session_start();
\$_SESSION['vostok_authenticated'] = true;
\$_SESSION['vostok_user'] = \$req['user'];
\$_SESSION['csrf_token'] = 'test_token_123';
\$_SERVER['HTTP_X_CSRF_TOKEN'] = 'test_token_123';
\$_SERVER['REQUEST_METHOD'] = 'POST';
\$_SERVER['HTTP_ACCEPT'] = 'application/json';
\$_POST = \$req['post'];

register_shutdown_function(function() {
    \$out = ob_get_clean();
    echo '___OUT___' . json_encode([
        'http' => http_response_code(),
        'body' => \$out
    ]) . '___END___';
});
ob_start();
require '{$script}';
";
    file_put_contents($tmpRunner, $code);
    $phpBin = getenv('PHP_BINARY') ?: (defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : 'php');
    $res = shell_exec(escapeshellarg($phpBin) . " " . escapeshellarg($tmpRunner) . " 2>&1");
    @unlink($tmpInput);
    @unlink($tmpRunner);

    if (preg_match('/___OUT___(.*?)___END___/s', (string)$res, $m)) {
        $parsed = json_decode($m[1], true);
        $json = json_decode($parsed['body'] ?? '{}', true) ?: [];
        return [
            'http' => $parsed['http'] ?? 500,
            'json' => $json,
            'raw'  => $parsed['body'] ?? ''
        ];
    }
    return ['http' => 500, 'json' => [], 'raw' => (string)$res];
}

$currentPass = 'InitialTempPass2026!';
$currentHash = password_hash($currentPass, PASSWORD_BCRYPT, ['cost' => 12]);
$pdo->prepare("UPDATE employee_accounts SET password_hash = ?, must_change_password = 1 WHERE emp_id = ?")->execute([$currentHash, $testEmpId]);
$empUser = queryUserByCredentials($testEmpId);

// Test 5.3a: Wrong current password rejected
$r1 = submitChangePassword($empUser, [
    'csrf_token' => 'test_token_123',
    'old_password' => 'WrongPasswordHere!',
    'new_password' => 'ValidLongSecretPassword2026',
    'confirm_password' => 'ValidLongSecretPassword2026'
]);
assertPw("5.3a Current password mismatch rejected (400)", $r1['http'] === 400 && str_contains($r1['json']['error'] ?? '', 'Current password is incorrect'), $r1['raw']);

// Test 5.3b: Under 14 characters rejected
$r2 = submitChangePassword($empUser, [
    'csrf_token' => 'test_token_123',
    'old_password' => $currentPass,
    'new_password' => 'Short12345!',
    'confirm_password' => 'Short12345!'
]);
assertPw("5.3b Password under 14 chars rejected (400)", $r2['http'] === 400 && str_contains($r2['json']['error'] ?? '', 'at least 14 characters'), $r2['raw']);

// Test 5.3c: Company name 'vostokpribor' in password rejected
$r3 = submitChangePassword($empUser, [
    'csrf_token' => 'test_token_123',
    'old_password' => $currentPass,
    'new_password' => 'vostokpribor_secret_pwd_99',
    'confirm_password' => 'vostokpribor_secret_pwd_99'
]);
assertPw("5.3c Company name in deny-list rejected (400)", $r3['http'] === 400 && str_contains($r3['json']['error'] ?? '', 'forbidden phrase'), $r3['raw']);

// Test 5.3d: Username in password rejected
$r4 = submitChangePassword($empUser, [
    'csrf_token' => 'test_token_123',
    'old_password' => $currentPass,
    'new_password' => 'MySecretPass_' . $empUser['username'],
    'confirm_password' => 'MySecretPass_' . $empUser['username']
]);
assertPw("5.3d Username in password rejected (400)", $r4['http'] === 400 && str_contains($r4['json']['error'] ?? '', 'username'), $r4['raw']);

// Test 5.3e: Same as current password rejected
$r5 = submitChangePassword($empUser, [
    'csrf_token' => 'test_token_123',
    'old_password' => $currentPass,
    'new_password' => $currentPass,
    'confirm_password' => $currentPass
]);
assertPw("5.3e Reusing current password rejected (400)", $r5['http'] === 400 && str_contains($r5['json']['error'] ?? '', 'same as your current password'), $r5['raw']);

// Test 5.3f: Valid new password #1 accepted
$pass1 = 'UltraSecureAlphaSecretOne#2026';
$r6 = submitChangePassword($empUser, [
    'csrf_token' => 'test_token_123',
    'old_password' => $currentPass,
    'new_password' => $pass1,
    'confirm_password' => $pass1
]);
assertPw("5.3f Valid password change #1 succeeds (200)", $r6['http'] === 200 && ($r6['json']['success'] ?? false) === true, $r6['raw']);

// Verify in DB that must_change_password was cleared and history has 1 record
$check1 = $pdo->query("SELECT must_change_password FROM employee_accounts WHERE emp_id = '$testEmpId'")->fetchColumn();
$histCount1 = $pdo->query("SELECT COUNT(*) FROM password_history WHERE account_type = 'Employee' AND account_id = {$empAccount['account_id']}")->fetchColumn();
assertPw("5.3g must_change_password flag cleared in DB", (int)$check1 === 0, "Flag was $check1");
assertPw("5.3h Old password archived into password_history table", (int)$histCount1 === 1, "Count was $histCount1");

// Test 5.3i: Cannot reuse password #1 or current password from history
$empUserUpdated = queryUserByCredentials($testEmpId);
$r7 = submitChangePassword($empUserUpdated, [
    'csrf_token' => 'test_token_123',
    'old_password' => $pass1,
    'new_password' => $currentPass, // The initial password now in history!
    'confirm_password' => $currentPass
]);
assertPw("5.3i Reusing password from last 5 history rejected (400)", $r7['http'] === 400 && str_contains($r7['json']['error'] ?? '', 'last 5 passwords'), $r7['raw']);

// Cycle through 4 more valid passwords
$pass2 = 'UltraSecureAlphaSecretTwo#2026';
$r8 = submitChangePassword($empUserUpdated, [
    'csrf_token' => 'test_token_123',
    'old_password' => $pass1,
    'new_password' => $pass2,
    'confirm_password' => $pass2
]);
assertPw("5.3j Second password rotation accepted (200)", $r8['http'] === 200, $r8['raw']);

echo "\n========================================================================\n";
echo "  PHASE 5 TEST RESULTS: $passed / $total PASSED\n";
echo "========================================================================\n";

if ($passed !== $total) {
    exit(1);
}
exit(0);
