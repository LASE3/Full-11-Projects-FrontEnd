<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR API Runtime Smoke Test & Fatal Error Scanner
 * Specification: Phase 1.1, Phase 6.5
 *
 * Discovers every API endpoint in the repository:
 *   - [system]/api/*.php
 *   - root api/*.php
 *   - api/v1/**\/*.php
 *
 * Executes requests as 4 personas:
 *   1. Unauthenticated (no session)
 *   2. CEO / SuperAdmin (EMP-1001, Viktor Sokolov, L4)
 *   3. Low-privilege Employee (EMP-1014, Mikhail Antonov, L2 OPS)
 *   4. Customer (CUS-1001, Sergei Makarov, Aral Geomatics Group)
 *
 * Detects:
 *   - Any PHP fatal error
 *   - Any Uncaught Error / Uncaught Exception
 *   - Call to undefined function / method
 *   - Exposed Stack trace text
 *   - Undefined variable / array key warnings leaking into output
 *   - Any 5xx server error
 *   - New entries in PHP error log during execution
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';

$baseUrl = getenv('BASE_URL') ?: 'http://127.0.0.1/Full-11-Projects-FrontEnd';
$rootDir = dirname(__DIR__);

echo "========================================================================\n";
echo "           VOSTOKPRIBOR COMPREHENSIVE API RUNTIME SWEEP                 \n";
echo "           Base URL: {$baseUrl}\n";
echo "========================================================================\n\n";

// 1. Discover all API endpoint files
$apiFiles = [];
$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$exemptFiles = [
    'db.php',
    'db_helper.php',
    'AuditLogger.php',
    'I18n.php',
    'Response.php',
    'Validator.php',
    'CustomerSession.php',
];

foreach ($it as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $path = str_replace('\\', '/', $file->getPathname());

    // Skip vendors, node_modules, tests, storage, scripts, git
    if (preg_match('#/(vendor|node_modules|tests|storage|scripts|\.git|\.gemini)/#', $path)) {
        continue;
    }

    // Match */api/*.php, api/*.php, and api/v1/**
    if (preg_match('#(/api/|/api/v1/)#', $path)) {
        $base = basename($path);
        if (in_array($base, $exemptFiles, true)) {
            continue;
        }
        if (str_contains($path, '/helpers/')) {
            continue;
        }
        $relPath = str_replace('\\', '/', substr($path, strlen(realpath($rootDir)) + 1));
        $apiFiles[] = $relPath;
    }
}

sort($apiFiles, SORT_NATURAL);
echo "Discovered " . count($apiFiles) . " API endpoints across all systems.\n\n";

// 2. Prepare representative user credentials & SSO tokens
$pdo = getDbConnection();

// Ensure test users have must_change_password = 0 for test execution
$pdo->exec("UPDATE employee_accounts SET must_change_password = 0 WHERE emp_id IN ('EMP-1001', 'EMP-1014')");
$pdo->exec("UPDATE customer_accounts SET must_change_password = 0 WHERE cus_id = 'CUS-1001'");

$personas = [
    'Unauthenticated' => null,
    'CEO (EMP-1001, L4)' => 'EMP-1001',
    'Employee (EMP-1014, L2)' => 'EMP-1014',
    'Customer (CUS-1001)' => 'CUS-1001',
];

$tokens = [];
foreach ($personas as $name => $userId) {
    if ($userId === null) {
        $tokens[$name] = null;
        continue;
    }
    $u = queryUserByCredentials($userId);
    if (!$u) {
        fwrite(STDERR, "Error: User {$userId} not found for persona '{$name}'\n");
        exit(1);
    }
    $tokens[$name] = createSsoCookie($u);
}

// 3. Track error log baseline size
$logPaths = array_unique(array_filter([
    getenv('PHP_ERROR_LOG') ?: null,
    ini_get('error_log') ?: null,
    $rootDir . '/error_log',
    DIRECTORY_SEPARATOR === '\\' ? 'c:/xampp/apache/logs/error.log' : '/var/log/apache2/error.log',
    DIRECTORY_SEPARATOR === '\\' ? 'c:/xampp/php/logs/php_error_log' : '/var/log/php_errors.log',
]));
$initialLogSizes = [];
foreach ($logPaths as $lp) {
    if (file_exists($lp)) {
        $initialLogSizes[$lp] = filesize($lp);
    }
}

// 4. HTTP request runner via cURL
function callApi(string $url, ?string $token, string $method = 'GET', array $postData = []): array {
    $ch = curl_init();
    $fullUrl = $url;
    
    $headers = [
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest',
    ];

    if ($token !== null) {
        curl_setopt($ch, CURLOPT_COOKIE, "vostok_sso_token={$token}");
    }

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        $csrf = 'vostok_test_csrf_token_smoke';
        // Pass dummy CSRF and payload
        $postData['csrf_token'] = $csrf;
        $headers[] = 'X-CSRF-Token: ' . $csrf;
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    }

    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

    $body = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);

    return [
        'code' => $httpCode,
        'body' => (string)$body,
        'curl_error' => $curlErr,
    ];
}

// 5. Sweep every endpoint as each persona
$failures = [];
$totalCalls = 0;
$passedCalls = 0;

$fatalPatterns = [
    'Fatal error'                => 'PHP Fatal Error',
    'Uncaught Error'             => 'Uncaught PHP Error',
    'Uncaught Exception'         => 'Uncaught Exception',
    'Call to undefined function' => 'Call to undefined function',
    'Stack trace:'               => 'Stack trace exposed to client',
    'Notice: Undefined'          => 'PHP Notice: Undefined variable/index',
    'Warning: Undefined'         => 'PHP Warning: Undefined variable/index',
];

foreach ($personas as $personaName => $userId) {
    echo "--- Testing as: {$personaName} ---\n";
    $token = $tokens[$personaName];

    foreach ($apiFiles as $relPath) {
        $totalCalls++;
        // Construct URL
        $urlParts = explode('/', $relPath);
        $encodedParts = array_map('rawurlencode', $urlParts);
        $url = $baseUrl . '/' . implode('/', $encodedParts);

        // Call with GET
        $res = callApi($url, $token, 'GET');
        $code = $res['code'];
        $body = $res['body'];

        $failed = false;
        $reason = '';

        if (!empty($res['curl_error'])) {
            $failed = true;
            $reason = "cURL error: " . $res['curl_error'];
        } elseif ($code >= 500) {
            $failed = true;
            $reason = "HTTP {$code} Internal Server Error";
        } else {
            // Check body for fatal error strings
            foreach ($fatalPatterns as $needle => $label) {
                if (stripos($body, $needle) !== false) {
                    $failed = true;
                    // Extract snippet
                    $snip = substr(strip_tags($body), 0, 150);
                    $reason = "{$label}: {$snip}";
                    break;
                }
            }
        }

        if ($failed) {
            $failures[] = [
                'persona'  => $personaName,
                'endpoint' => $relPath,
                'method'   => 'GET',
                'code'     => $code,
                'reason'   => $reason,
            ];
            echo "  [FAIL] [{$personaName}] GET {$relPath} (HTTP {$code}): {$reason}\n";
        } else {
            $passedCalls++;
        }
    }
}

// 6. Check for new fatal errors in error logs
echo "\nChecking PHP error logs for newly logged fatals during sweep...\n";
$newFatalLogs = [];
foreach ($logPaths as $lp) {
    if (file_exists($lp)) {
        $initSize = $initialLogSizes[$lp] ?? 0;
        $currSize = filesize($lp);
        if ($currSize > $initSize) {
            $fp = fopen($lp, 'r');
            fseek($fp, $initSize);
            $newContent = fread($fp, $currSize - $initSize);
            fclose($fp);
            if (preg_match_all('/PHP Fatal error:.*$/m', $newContent, $m)) {
                foreach ($m[0] as $logLine) {
                    $newFatalLogs[] = "{$lp}: " . trim($logLine);
                }
            }
        }
    }
}

// 7. Results summary
echo "\n========================================================================\n";
echo "                      API RUNTIME SWEEP SUMMARY                         \n";
echo "========================================================================\n";
echo "  Total API Requests : {$totalCalls}\n";
echo "  Passed (No Crash)  : {$passedCalls}\n";
echo "  Failed (Crash/5xx) : " . count($failures) . "\n";
echo "  New Fatals in Log  : " . count($newFatalLogs) . "\n";
echo "========================================================================\n";

if (!empty($failures)) {
    echo "\nSUMMARY OF FAILING ENDPOINTS:\n";
    $uniqueFails = [];
    foreach ($failures as $f) {
        $key = "{$f['method']} {$f['endpoint']}";
        $uniqueFails[$key][] = "{$f['persona']} -> {$f['reason']}";
    }
    foreach ($uniqueFails as $ep => $errs) {
        echo "  - {$ep}:\n";
        foreach ($errs as $err) {
            echo "      * {$err}\n";
        }
    }
}

if (!empty($newFatalLogs)) {
    echo "\nNEW FATAL ERRORS LOGGED DURING SWEEP:\n";
    foreach ($newFatalLogs as $nfl) {
        echo "  - {$nfl}\n";
    }
}

$exitCode = (count($failures) === 0 && count($newFatalLogs) === 0) ? 0 : 1;
if ($exitCode === 0) {
    echo "\n0 FATALS / 0 5xx - ZERO ERRORS DETECTED\n";
}
echo "\nSweep complete. Status: " . ($exitCode === 0 ? "PASS" : "FAIL") . "\n";
exit($exitCode);
