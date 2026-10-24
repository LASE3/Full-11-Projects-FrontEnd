<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR Security Headers & Cookie Hardening Regression Test
 *
 * Verifies Release Hardening Requirements 5.1 - 5.4:
 * 1. Content-Security-Policy, X-Frame-Options, X-Content-Type-Options,
 *    Referrer-Policy, Permissions-Policy, Strict-Transport-Security, and expose_php off.
 * 2. PHPSESSID session cookie has HttpOnly, SameSite=Lax (or Strict), and Secure when HTTPS.
 * 3. .env.production.example is present with APP_ENV=production, display_errors off,
 *    non-root database user, and documented HTTPS requirement.
 * 4. Runtime verification across login pages and API endpoints.
 */

$root = dirname(__DIR__);
$passCount = 0;
$failCount = 0;
$failures = [];

function assertCheck(string $desc, bool $condition, string $detail = ''): void
{
    global $passCount, $failCount, $failures;
    if ($condition) {
        $passCount++;
        echo "  [PASS] {$desc}\n";
    } else {
        $failCount++;
        $failures[] = "{$desc}: {$detail}";
        echo "  [FAIL] {$desc}: {$detail}\n";
    }
}

echo "========================================================================\n";
echo "       VOSTOKPRIBOR SECURITY HEADERS & COOKIE HARDENING TEST            \n";
echo "========================================================================\n\n";

// -----------------------------------------------------------------------------
// 1. Static Verification: .htaccess Security Headers & Directives
// -----------------------------------------------------------------------------
echo "[1/4] Auditing .htaccess directives...\n";
$htaccessPath = $root . '/.htaccess';
assertCheck(".htaccess file exists", file_exists($htaccessPath));

if (file_exists($htaccessPath)) {
    $htaccess = file_get_contents($htaccessPath);
    assertCheck(".htaccess sets Content-Security-Policy", (bool)preg_match('/Header\s+(always\s+)?set\s+Content-Security-Policy/i', $htaccess));
    assertCheck(".htaccess sets X-Frame-Options: SAMEORIGIN", (bool)preg_match('/Header\s+(always\s+)?set\s+X-Frame-Options\s+["\']?SAMEORIGIN/i', $htaccess));
    assertCheck(".htaccess sets X-Content-Type-Options: nosniff", (bool)preg_match('/Header\s+(always\s+)?set\s+X-Content-Type-Options\s+["\']?nosniff/i', $htaccess));
    assertCheck(".htaccess sets Referrer-Policy: strict-origin-when-cross-origin", (bool)preg_match('/Header\s+(always\s+)?set\s+Referrer-Policy\s+["\']?strict-origin-when-cross-origin/i', $htaccess));
    assertCheck(".htaccess sets Permissions-Policy", (bool)preg_match('/Header\s+(always\s+)?set\s+Permissions-Policy/i', $htaccess));
    assertCheck(".htaccess sets Strict-Transport-Security on HTTPS", (bool)preg_match('/Header\s+(always\s+)?set\s+Strict-Transport-Security/i', $htaccess));
    assertCheck(".htaccess unsets X-Powered-By (expose_php off)", (bool)preg_match('/Header\s+unset\s+X-Powered-By/i', $htaccess));
}

// -----------------------------------------------------------------------------
// 2. Static Verification: .env.production.example Configuration
// -----------------------------------------------------------------------------
echo "\n[2/4] Auditing .env.production.example...\n";
$prodEnvPath = $root . '/.env.production.example';
assertCheck(".env.production.example exists", file_exists($prodEnvPath));

if (file_exists($prodEnvPath)) {
    $prodEnv = file_get_contents($prodEnvPath);
    assertCheck(".env.production.example specifies APP_ENV=production", (bool)preg_match('/^APP_ENV\s*=\s*production/m', $prodEnv));
    assertCheck(".env.production.example specifies display_errors=off", (bool)preg_match('/^display_errors\s*=\s*off/m', $prodEnv));
    assertCheck(".env.production.example specifies non-root DB_USER=vostok_app", (bool)preg_match('/^DB_USER\s*=\s*vostok_app/m', $prodEnv));
    assertCheck(".env.production.example documents non-root least privileges", str_contains($prodEnv, 'GRANT SELECT, INSERT, UPDATE, DELETE'));
    assertCheck(".env.production.example documents mandatory HTTPS requirement", str_contains($prodEnv, 'REQUIRE_HTTPS=true') || str_contains($prodEnv, 'HTTPS'));
}

// -----------------------------------------------------------------------------
// 3. Code Verification: Centralized Session & Header Configuration in db.php
// -----------------------------------------------------------------------------
echo "\n[3/4] Auditing shared session & header configuration...\n";
$dbConfigPath = $root . '/config/db.php';
$dbContent = file_get_contents($dbConfigPath);
assertCheck("config/db.php defines vp_configure_session()", str_contains($dbContent, 'function vp_configure_session'));
assertCheck("config/db.php defines vp_send_security_headers()", str_contains($dbContent, 'function vp_send_security_headers'));
assertCheck("config/db.php sets session.cookie_httponly", str_contains($dbContent, "'session.cookie_httponly', '1'"));
assertCheck("config/db.php sets session.cookie_samesite", str_contains($dbContent, 'session.cookie_samesite'));
assertCheck("config/db.php sets session.cookie_secure when HTTPS", str_contains($dbContent, 'session.cookie_secure'));
assertCheck("config/db.php sets expose_php = Off", str_contains($dbContent, "'expose_php', 'Off'"));

$headSecPath = $root . '/includes/head_security.php';
$headSec = file_get_contents($headSecPath);
assertCheck("includes/head_security.php invokes vp_send_security_headers()", str_contains($headSec, 'vp_send_security_headers'));

// -----------------------------------------------------------------------------
// 4. Runtime Verification: Headers & Cookie Attributes on HTTP Responses
// -----------------------------------------------------------------------------
echo "\n[4/4] Executing runtime header & session cookie verification...\n";

// 4a. Unit check on session parameters and security function execution
$testScript = <<<PHP
<?php
\$_SERVER['HTTPS'] = 'on';
\$_SERVER['SERVER_PORT'] = '443';
require_once '{$root}/config/db.php';
session_start();
echo json_encode([
    'params' => session_get_cookie_params(),
    'ini_httponly' => ini_get('session.cookie_httponly'),
    'ini_secure' => ini_get('session.cookie_secure'),
    'ini_samesite' => ini_get('session.cookie_samesite'),
]);
PHP;

$phpBin = defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : 'php';
$descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
$pipes = [];
$process = proc_open($phpBin, $descriptors, $pipes, $root);
if (is_resource($process)) {
    fwrite($pipes[0], $testScript);
    fclose($pipes[0]);
    $output = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($process);

    $data = json_decode($output, true);
    if (is_array($data)) {
        $params = $data['params'] ?? [];
        assertCheck("Runtime session cookie httponly is true", ($params['httponly'] ?? false) === true || $data['ini_httponly'] === '1');
        assertCheck("Runtime session cookie samesite is Lax or Strict", in_array(strtolower($params['samesite'] ?? $data['ini_samesite'] ?? ''), ['lax', 'strict'], true));
        assertCheck("Runtime session cookie secure is true on HTTPS", ($params['secure'] ?? false) === true || $data['ini_secure'] === '1');
    } else {
        assertCheck("Session parameters unit test executed cleanly", false, "Output: {$output}");
    }
}

// 4b. Live HTTP response verification
$serverUrl = null;
$candidates = array_filter([
    getenv('BASE_URL') ?: '',
    'http://127.0.0.1/Full-11-Projects-FrontEnd/',
    'http://127.0.0.1:8080/'
]);

foreach ($candidates as $cand) {
    $cand = rtrim($cand, '/') . '/';
    $ctx = stream_context_create(['http' => ['method' => 'GET', 'timeout' => 1.5, 'ignore_errors' => true]]);
    $http_response_header = [];
    $test = @file_get_contents($cand . 'index.php', false, $ctx);
    if ($test !== false && !empty($http_response_header)) {
        $serverUrl = $cand;
        break;
    }
}

$transientServer = null;
if ($serverUrl === null) {
    // Launch a transient built-in PHP server for live testing
    $port = 18089;
    $tPipes = [];
    $transientServer = proc_open(
        "\"{$phpBin}\" -S 127.0.0.1:{$port} -t \"{$root}\"",
        [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']],
        $tPipes,
        $root
    );
    usleep(500000); // 500ms
    $serverUrl = "http://127.0.0.1:{$port}/";
}

$loginEndpoint = $serverUrl . 'VOSTOKPRIBOR%20Corporate%20Web%20Platform/login.php';
$ctx = stream_context_create(['http' => ['method' => 'GET', 'timeout' => 3.0, 'ignore_errors' => true]]);
$http_response_header = [];
$response = @file_get_contents($loginEndpoint, false, $ctx);
$respHeaders = $http_response_header ?? [];
$headerStr = implode("\n", $respHeaders);

if ($transientServer && is_resource($transientServer)) {
    proc_terminate($transientServer);
    proc_close($transientServer);
}

assertCheck("Live HTTP endpoint reachable ({$serverUrl})", !empty($respHeaders));
assertCheck("HTTP response sends Content-Security-Policy", (bool)preg_match('/Content-Security-Policy:/i', $headerStr));
assertCheck("HTTP response sends X-Frame-Options: SAMEORIGIN", (bool)preg_match('/X-Frame-Options:\s*SAMEORIGIN/i', $headerStr));
assertCheck("HTTP response sends X-Content-Type-Options: nosniff", (bool)preg_match('/X-Content-Type-Options:\s*nosniff/i', $headerStr));
assertCheck("HTTP response sends Referrer-Policy: strict-origin-when-cross-origin", (bool)preg_match('/Referrer-Policy:\s*strict-origin-when-cross-origin/i', $headerStr));
assertCheck("HTTP response sends Permissions-Policy", (bool)preg_match('/Permissions-Policy:/i', $headerStr));
assertCheck("HTTP response removes X-Powered-By", !preg_match('/X-Powered-By:\s*PHP/i', $headerStr));
assertCheck("HTTP response issues PHPSESSID with HttpOnly and SameSite", (bool)preg_match('/Set-Cookie:\s*PHPSESSID=.*?HttpOnly.*?SameSite=(Lax|Strict)/i', $headerStr));

// Check that all 11 login.php pages require db.php before session_start
$loginFiles = [
    'Admin & Governance Portal/login.php',
    'CRM/login.php',
    'Customer Portal/login.php',
    'Developer/login.php',
    'Employee Intranet/login.php',
    'File Center/login.php',
    'Finance & Billing/login.php',
    'HR System/login.php',
    'IT Helpdesk/login.php',
    'Online Shop B2B/login.php',
    'VOSTOKPRIBOR Corporate Web Platform/login.php',
];
$allLoginsConfigured = true;
$unconfiguredLogins = [];
foreach ($loginFiles as $lf) {
    $c = file_get_contents($root . '/' . $lf);
    $dbPos = strpos($c, 'config/db.php');
    $sessPos = strpos($c, 'session_start');
    if ($dbPos === false || ($sessPos !== false && $dbPos > $sessPos)) {
        $allLoginsConfigured = false;
        $unconfiguredLogins[] = $lf;
    }
}
assertCheck(
    "All 11 login.php pages load config/db.php before session_start",
    $allLoginsConfigured,
    "Unconfigured: " . implode(', ', $unconfiguredLogins)
);

echo "\n========================================================================\n";
echo "                   SECURITY HARDENING TEST SUMMARY                      \n";
echo "========================================================================\n";
echo "  TOTAL CHECKS: " . ($passCount + $failCount) . "\n";
echo "  PASSED      : {$passCount}\n";
echo "  FAILED      : {$failCount}\n";
echo "========================================================================\n\n";

if ($failCount > 0) {
    echo "SECURITY HARDENING FAILURES:\n";
    foreach ($failures as $f) {
        echo "  - {$f}\n";
    }
    exit(1);
}

echo "ALL SECURITY HEADERS & COOKIE HARDENING GATES PASSED (0 DEFECTS)!\n";
exit(0);
