<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Customer Password Isolation Regression Suite
 * Specification: Phase 3 (N3)
 *
 * Ground Rules:
 *  - Company with 4 accounts (CUS-1001: accounts 1, 2, 3, 18)
 *  - User B (account 2) changes their password via account/change-password.php
 *  - Users A, C, D can still log in with their old passwords
 *  - Users A, C, D cannot log in with User B's new password
 *  - User B cannot log in with old password
 *  - User B can log in with new password
 *  - Policy enforcement: min 14 chars, company name deny-list, no reuse of last 5
 *  - Old sessions for User B revoked in user_sessions; other users untouched
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';

$baseUrl = getenv('BASE_URL') ?: 'http://127.0.0.1/Full-11-Projects-FrontEnd';
$pdo = getDbConnection();

$testsTotal = 0;
$testsPassed = 0;
$testsFailed = 0;

function assertRule(string $desc, bool $condition, string $detail = ''): void {
    global $testsTotal, $testsPassed, $testsFailed;
    $testsTotal++;
    if ($condition) {
        $testsPassed++;
        echo "  [PASS] {$desc}\n";
    } else {
        $testsFailed++;
        echo "  [FAIL] {$desc} -- {$detail}\n";
    }
}

function loginApi(string $username, string $password): array {
    global $baseUrl;
    $ch = curl_init($baseUrl . '/api/auth.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'username' => $username,
        'password' => $password,
        'system'   => 'CUS'
    ]));
    $raw = (string)curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    return [
        'code' => $code,
        'data' => json_decode($raw, true) ?? [],
        'raw'  => $raw
    ];
}

function postChangePassword(array $userSessionData, string $oldPass, string $newPass, string $confPass): array {
    global $baseUrl;
    $token = createSsoCookie($userSessionData);

    // 1. Fetch CSRF token and session
    $chCsrf = curl_init($baseUrl . '/api/csrf.php');
    curl_setopt($chCsrf, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chCsrf, CURLOPT_HEADER, true);
    curl_setopt($chCsrf, CURLOPT_HTTPHEADER, [
        'Cookie: vostok_sso_token=' . $token
    ]);
    $rawCsrf = (string)curl_exec($chCsrf);
    $headerSize = curl_getinfo($chCsrf, CURLINFO_HEADER_SIZE);
    $headerStr = substr($rawCsrf, 0, $headerSize);
    $bodyStr = substr($rawCsrf, $headerSize);
    preg_match('/PHPSESSID=([^;]+)/', $headerStr, $m);
    $phpsessid = $m[1] ?? '';
    $csrfData = json_decode($bodyStr, true) ?? [];
    $csrfToken = $csrfData['csrf_token'] ?? '';

    // 2. Post change-password.php
    $ch = curl_init($baseUrl . '/account/change-password.php');
    $cookieHeader = 'vostok_sso_token=' . $token . ($phpsessid !== '' ? '; PHPSESSID=' . $phpsessid : '');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded',
        'Cookie: ' . $cookieHeader,
        'X-CSRF-Token: ' . $csrfToken
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'old_password'     => $oldPass,
        'new_password'     => $newPass,
        'confirm_password' => $confPass,
        'csrf_token'       => $csrfToken
    ]));

    $body = (string)curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    return [
        'code' => $httpCode,
        'data' => json_decode($body, true) ?? [],
        'raw'  => $body
    ];
}

echo "========================================================================\n";
echo "    PHASE 3: CUSTOMER PASSWORD ISOLATION & GOVERNANCE REGRESSION TEST    \n";
echo "========================================================================\n\n";

// Target accounts under CUS-1001:
// User A: account_id = 1  (sergei.makarov)
// User B: account_id = 2  (CLT-77210)
// User C: account_id = 3  (CUS-1001)
// User D: account_id = 18 (aralgeomaticsgr_1001)

$passA = 'AlphaPass2026!Secure';
$passB_old = 'BetaPass2026!Secure';
$passB_new = 'BetaNewPass2026!UltraSafe';
$passC = 'GammaPass2026!Secure';
$passD = 'DeltaPass2026!Secure';

// Backup original hashes to restore after test
$backupHashes = [];
$accounts = [1 => 'sergei.makarov', 2 => 'CLT-77210', 3 => 'CUS-1001', 18 => 'aralgeomaticsgr_1001'];
foreach ($accounts as $aid => $uname) {
    $stmt = $pdo->prepare("SELECT password_hash, must_change_password FROM customer_accounts WHERE account_id = ?");
    $stmt->execute([$aid]);
    $backupHashes[$aid] = $stmt->fetch(PDO::FETCH_ASSOC);
}

try {
    // Setup known passwords
    $pdo->prepare("UPDATE customer_accounts SET password_hash = ?, must_change_password = 0 WHERE account_id = 1")->execute([password_hash($passA, PASSWORD_BCRYPT)]);
    $pdo->prepare("UPDATE customer_accounts SET password_hash = ?, must_change_password = 0 WHERE account_id = 2")->execute([password_hash($passB_old, PASSWORD_BCRYPT)]);
    $pdo->prepare("UPDATE customer_accounts SET password_hash = ?, must_change_password = 0 WHERE account_id = 3")->execute([password_hash($passC, PASSWORD_BCRYPT)]);
    $pdo->prepare("UPDATE customer_accounts SET password_hash = ?, must_change_password = 0 WHERE account_id = 18")->execute([password_hash($passD, PASSWORD_BCRYPT)]);

    // Clean password history for account 2 for clean test state
    $pdo->prepare("DELETE FROM password_history WHERE account_type = 'Customer' AND account_id = 2")->execute();

    // -------------------------------------------------------------------------
    // Step 1: Pre-test authentication check for all 4 users
    // -------------------------------------------------------------------------
    echo "1. Verifying initial login for all 4 customer accounts...\n";
    $loginA = loginApi('sergei.makarov', $passA);
    $loginB = loginApi('CLT-77210', $passB_old);
    $loginC = loginApi('CUS-1001', $passC);
    $loginD = loginApi('aralgeomaticsgr_1001', $passD);

    assertRule("1.1 User A (sergei.makarov) authenticates with initial password", $loginA['code'] === 200, $loginA['raw']);
    assertRule("1.2 User B (CLT-77210) authenticates with initial password", $loginB['code'] === 200, $loginB['raw']);
    assertRule("1.3 User C (CUS-1001) authenticates with initial password", $loginC['code'] === 200, $loginC['raw']);
    assertRule("1.4 User D (aralgeomaticsgr_1001) authenticates with initial password", $loginD['code'] === 200, $loginD['raw']);

    // Build user session data for User B (CLT-77210, account_id = 2)
    $userB_session = [
        'account_id'      => 2,
        'user_id'         => 'CUS-1001',
        'cus_id'          => 'CUS-1001',
        'username'        => 'CLT-77210',
        'full_name'       => 'Client Contact 77210',
        'email'           => 'client77210@vostokpribor.local',
        'role_name'       => 'Customer Client Account',
        'department_code' => 'GEN',
        'clearance_level' => 'L1',
        'account_type'    => 'Customer',
        'must_change_password' => 0,
        'authorized_system' => 'CUS',
        'login_time'      => date('Y-m-d H:i:s')
    ];

    // Register active session for User B to test session revocation
    $oldJtiB = bin2hex(random_bytes(16));
    registerUserSession('Customer', 2, 'CUS', $oldJtiB);

    // Also register active session for User A (account 1) to verify it is NOT revoked
    $jtiA = bin2hex(random_bytes(16));
    registerUserSession('Customer', 1, 'CUS', $jtiA);

    // -------------------------------------------------------------------------
    // Step 2: Policy Enforcement Checks on account/change-password.php
    // -------------------------------------------------------------------------
    echo "\n2. Testing Password Policy Guardrails for User B...\n";

    // 2a. Too short (< 14 chars)
    $shortRes = postChangePassword($userB_session, $passB_old, 'ShortPass1!', 'ShortPass1!');
    assertRule("2.1 Refuses password < 14 chars with HTTP 400", $shortRes['code'] === 400, $shortRes['raw']);
    assertRule("2.2 Message explains 14 character minimum", str_contains(strtolower($shortRes['data']['error'] ?? ''), '14 characters'), $shortRes['raw']);

    // 2b. Contains company name ('aral')
    $companyRes = postChangePassword($userB_session, $passB_old, 'Aral_Enterprise_2026_SecureKey!', 'Aral_Enterprise_2026_SecureKey!');
    assertRule("2.3 Refuses password containing customer company name with HTTP 400", $companyRes['code'] === 400, $companyRes['raw']);
    assertRule("2.4 Message explains forbidden company name", str_contains(strtolower($companyRes['data']['error'] ?? ''), 'company name') || str_contains(strtolower($companyRes['data']['error'] ?? ''), 'forbidden'), $companyRes['raw']);

    // 2c. Contains username ('clt-77210')
    $userRes = postChangePassword($userB_session, $passB_old, 'CLT-77210_Unique_Password_2026!', 'CLT-77210_Unique_Password_2026!');
    assertRule("2.5 Refuses password containing username with HTTP 400", $userRes['code'] === 400, $userRes['raw']);

    // 2d. Same as current password
    $sameRes = postChangePassword($userB_session, $passB_old, $passB_old, $passB_old);
    assertRule("2.6 Refuses identical current password with HTTP 400", $sameRes['code'] === 400, $sameRes['raw']);

    // -------------------------------------------------------------------------
    // Step 3: User B successfully changes password
    // -------------------------------------------------------------------------
    echo "\n3. Testing Successful Password Change by User B (account_id = 2)...\n";
    $changeRes = postChangePassword($userB_session, $passB_old, $passB_new, $passB_new);
    assertRule("3.1 Password change succeeds with HTTP 200", $changeRes['code'] === 200, "Received HTTP {$changeRes['code']}: {$changeRes['raw']}");
    assertRule("3.2 Success response confirmed", !empty($changeRes['data']['success']), $changeRes['raw']);

    // Verify in DB that ONLY account_id = 2 was updated
    $dbCheck = $pdo->query("SELECT account_id, username, password_hash FROM customer_accounts WHERE cus_id = 'CUS-1001' ORDER BY account_id ASC")->fetchAll(PDO::FETCH_ASSOC);
    $accMap = [];
    foreach ($dbCheck as $row) {
        $accMap[(int)$row['account_id']] = $row;
    }

    assertRule("3.3 Account 2 password hash updated in DB to match new password", password_verify($passB_new, $accMap[2]['password_hash']));
    assertRule("3.4 Account 1 password hash in DB UNCHANGED", password_verify($passA, $accMap[1]['password_hash']));
    assertRule("3.5 Account 3 password hash in DB UNCHANGED", password_verify($passC, $accMap[3]['password_hash']));
    assertRule("3.6 Account 18 password hash in DB UNCHANGED", password_verify($passD, $accMap[18]['password_hash']));

    // -------------------------------------------------------------------------
    // Step 4: History Check (User B cannot reuse previous password)
    // -------------------------------------------------------------------------
    echo "\n4. Testing History Re-use Prevention for User B...\n";
    $userB_session_updated = $userB_session;
    $reuseRes = postChangePassword($userB_session_updated, $passB_new, $passB_old, $passB_old);
    assertRule("4.1 Refuses reuse of previous password with HTTP 400", $reuseRes['code'] === 400, $reuseRes['raw']);
    assertRule("4.2 Message explains 5 recent passwords history policy", str_contains(strtolower($reuseRes['data']['error'] ?? ''), '5 passwords'), $reuseRes['raw']);

    // -------------------------------------------------------------------------
    // Step 5: Session Revocation Isolation
    // -------------------------------------------------------------------------
    echo "\n5. Verifying Session Revocation Isolation...\n";
    $oldSessionCheckB = $pdo->prepare("SELECT status FROM user_sessions WHERE jti = ?");
    $oldSessionCheckB->execute([$oldJtiB]);
    $statusB = $oldSessionCheckB->fetchColumn();
    assertRule("5.1 User B old session revoked in user_sessions", $statusB === 'Revoked', "Status was: {$statusB}");

    $sessionCheckA = $pdo->prepare("SELECT status FROM user_sessions WHERE jti = ?");
    $sessionCheckA->execute([$jtiA]);
    $statusA = $sessionCheckA->fetchColumn();
    assertRule("5.2 User A session remains Active in user_sessions", $statusA === 'Active', "Status was: {$statusA}");

    // -------------------------------------------------------------------------
    // Step 6: Post-change Login Isolation Across All 4 Users
    // -------------------------------------------------------------------------
    echo "\n6. Verifying Cross-Account Login Isolation...\n";

    // 6a. User B old password fails
    $loginB_old = loginApi('CLT-77210', $passB_old);
    assertRule("6.1 User B old password fails authentication (HTTP 401)", $loginB_old['code'] === 401, $loginB_old['raw']);

    // 6b. User B new password succeeds
    $loginB_new = loginApi('CLT-77210', $passB_new);
    assertRule("6.2 User B new password authenticates successfully (HTTP 200)", $loginB_new['code'] === 200, $loginB_new['raw']);

    // 6c. Users A, C, D still log in with their OLD passwords
    $postLoginA = loginApi('sergei.makarov', $passA);
    $postLoginC = loginApi('CUS-1001', $passC);
    $postLoginD = loginApi('aralgeomaticsgr_1001', $passD);
    assertRule("6.3 User A still logs in with old password (HTTP 200)", $postLoginA['code'] === 200, $postLoginA['raw']);
    assertRule("6.4 User C still logs in with old password (HTTP 200)", $postLoginC['code'] === 200, $postLoginC['raw']);
    assertRule("6.5 User D still logs in with old password (HTTP 200)", $postLoginD['code'] === 200, $postLoginD['raw']);

    // 6d. Users A, C, D CANNOT log in with User B's new password
    $crossLoginA = loginApi('sergei.makarov', $passB_new);
    $crossLoginC = loginApi('CUS-1001', $passB_new);
    $crossLoginD = loginApi('aralgeomaticsgr_1001', $passB_new);
    assertRule("6.6 User A cannot log in with User B's new password (HTTP 401)", $crossLoginA['code'] === 401);
    assertRule("6.7 User C cannot log in with User B's new password (HTTP 401)", $crossLoginC['code'] === 401);
    assertRule("6.8 User D cannot log in with User B's new password (HTTP 401)", $crossLoginD['code'] === 401);

} finally {
    // Restore baseline hashes and must_change_password flags
    foreach ($backupHashes as $aid => $bData) {
        if ($bData) {
            $pdo->prepare("UPDATE customer_accounts SET password_hash = ?, must_change_password = ? WHERE account_id = ?")
                ->execute([$bData['password_hash'], $bData['must_change_password'], $aid]);
        }
    }
    // Clean test sessions
    $pdo->prepare("DELETE FROM user_sessions WHERE jti IN (?, ?)")->execute([$oldJtiB ?? '', $jtiA ?? '']);
    $pdo->prepare("DELETE FROM password_history WHERE account_type = 'Customer' AND account_id = 2")->execute();
}

echo "\n========================================================================\n";
echo "SUMMARY: Total: {$testsTotal} | Passed: {$testsPassed} | Failed: {$testsFailed}\n";
echo "STATUS:  " . ($testsFailed === 0 ? "100% PASS - CUSTOMER PASSWORD CHANGE ISOLATION VERIFIED" : "FAILURES DETECTED") . "\n";
echo "========================================================================\n";

exit($testsFailed === 0 ? 0 : 1);
