<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR SQL Injection Regression Suite
 *
 * Verifies that all endpoints resist error-based and time-based SQL injection attacks.
 * Tests representative endpoints across systems using error-based and time-based payloads.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';

$baseUrl = getenv('BASE_URL') ?: 'http://127.0.0.1/Full-11-Projects-FrontEnd';
$baseUrl = rtrim($baseUrl, '/');

echo "========================================================================\n";
echo "            VOSTOKPRIBOR SQL INJECTION REGRESSION TEST                  \n";
echo "            Target Base URL: {$baseUrl}\n";
echo "========================================================================\n\n";

// Authenticate as CEO for full access
$adminCookie = createSsoCookie(['user_id' => 'EMP-1001', 'account_id' => 1, 'clearance_level' => 'L4']);

$endpoints = [
    '/IT Helpdesk/api/assets.php',
    '/IT Helpdesk/api/knowledge.php',
    '/Employee Intranet/api/policies.php',
    '/api/v1/intranet/directory.php',
    '/Online Shop B2B/api/products.php',
    '/Customer Portal/api/orders.php',
    '/CRM/api/customers.php',
    '/Finance & Billing/api/invoices.php',
];

$payloads = [
    'error_single_quote' => "' OR '1'='1",
    'error_union' => "1' UNION SELECT 1,2,3,4,5,6,7,8,9,10 -- ",
    'error_extractvalue' => "1' AND EXTRACTVALUE(1, CONCAT(0x7e, (SELECT 1), 0x7e)) -- ",
    'error_double_quote' => '" OR "1"="1',
    'time_based_sleep' => "1' AND (SELECT 1 FROM (SELECT(SLEEP(1)))a) -- ",
];

$testParams = ['search', 'q', 'id', 'category', 'status', 'type', 'filter'];

$totalTests = 0;
$passed = 0;
$failed = 0;
$defects = [];

foreach ($endpoints as $ep) {
    echo "Testing endpoint: {$ep}\n";
    foreach ($testParams as $param) {
        foreach ($payloads as $pName => $payload) {
            $totalTests++;
            $encodedEp = implode('/', array_map('rawurlencode', explode('/', ltrim($ep, '/'))));
            $url = $baseUrl . '/' . $encodedEp . '?' . urlencode($param) . '=' . urlencode($payload);

            $startTime = microtime(true);
            $ctx = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => "Cookie: vostok_sso_token={$adminCookie}\r\nUser-Agent: VostokSecurityAudit/2.0\r\n",
                    'timeout' => 3.0,
                    'ignore_errors' => true
                ]
            ]);

            $http_response_header = [];
            $resp = @file_get_contents($url, false, $ctx);
            $elapsed = microtime(true) - $startTime;

            $status = 0;
            if (!empty($http_response_header)) {
                if (preg_match('#HTTP/\S+\s+(\d+)#', $http_response_header[0], $m)) {
                    $status = (int)$m[1];
                }
            }

            // Error-based SQLi check: look for database error strings in body
            $hasSqlError = false;
            $sqlKeywords = ['SQLSTATE', 'syntax error', 'MariaDB server version', 'mysql_fetch', 'ORA-', 'pg_query'];
            foreach ($sqlKeywords as $kw) {
                if ($resp !== false && stripos($resp, $kw) !== false) {
                    $hasSqlError = true;
                    break;
                }
            }

            // Time-based SQLi check: if payload was SLEEP(1) and elapsed >= 1.0s, possible injection
            $timeDelayed = ($pName === 'time_based_sleep' && $elapsed >= 1.5);

            if ($status >= 500 || $hasSqlError || $timeDelayed) {
                $failed++;
                $reason = $status >= 500 ? "HTTP {$status} 5xx error" : ($hasSqlError ? "SQL error leaked" : "Time delay {$elapsed}s");
                $defects[] = "{$ep} [{$param}] ({$pName}): {$reason}";
                echo "  [FAIL] {$param}={$pName} (HTTP {$status}, {$elapsed}s)\n";
            } else {
                $passed++;
            }
        }
    }
}

echo "\n========================================================================\n";
echo "              SQL INJECTION REGRESSION TEST SUMMARY                     \n";
echo "========================================================================\n";
echo "  TOTAL TESTS : {$totalTests}\n";
echo "  PASSED      : {$passed}\n";
echo "  FAILED      : {$failed}\n";
echo "========================================================================\n\n";

if ($failed > 0) {
    echo "SQL INJECTION DEFECTS DETECTED:\n";
    foreach ($defects as $d) {
        echo "  - {$d}\n";
    }
    exit(1);
}

echo "ALL SQL INJECTION REGRESSION TESTS PASSED (0 VULNERABILITIES DETECTED)!\n";
exit(0);
