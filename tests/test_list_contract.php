<?php

declare(strict_types=1);

/**
 * Regression Test: Phase 3 Server-Side List Contract
 * Tests all 8 required big table endpoints for:
 * q, sort, dir, page, per_page (max 100) -> {rows, total, page, per_page}
 */

require_once __DIR__ . '/../config/db.php';

$pdo = getDbConnection();
$baseUrl = 'http://127.0.0.1/Full-11-Projects-FrontEnd';

// Get cookie for employee (EMP-1001 viktor.sokolov)
function getEmployeeCookie(string $systemId): string {
    $ch = curl_init('http://127.0.0.1/Full-11-Projects-FrontEnd/api/auth.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'userId'   => 'viktor.sokolov',
        'password' => 'Vp-Emp!4458b06ff8eb',
        'systemId' => $systemId
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', (string)$response, $matches);
    return implode('; ', $matches[1] ?? []);
}

$adminCusCookie = getEmployeeCookie('CUS');

$tests = [
    [
        'name' => '1. Intranet Directory',
        'url'  => "$baseUrl/Employee%20Intranet/api/directory.php?q=Viktor&page=1&per_page=5&sort=emp_id&dir=ASC",
        'cookie' => getEmployeeCookie('EMP'),
    ],
    [
        'name' => '2. HR Employee Records',
        'url'  => "$baseUrl/HR%20System/api/employees.php?q=Sokolov&page=1&per_page=5&sort=emp_id&dir=ASC",
        'cookie' => getEmployeeCookie('HR'),
    ],
    [
        'name' => '3. Finance Invoices',
        'url'  => "$baseUrl/Finance%20&%20Billing/api/finance_api.php?action=list_invoices&q=INV&page=1&per_page=5&sort=inv_id&dir=ASC",
        'cookie' => getEmployeeCookie('FIN'),
    ],
    [
        'name' => '4. IT Ticket Queue',
        'url'  => "$baseUrl/IT%20Helpdesk/api/tickets.php?q=TKT&page=1&per_page=5&sort=tkt_id&dir=ASC",
        'cookie' => getEmployeeCookie('IT'),
    ],
    [
        'name' => '5. CRM Customers',
        'url'  => "$baseUrl/CRM/api/customers.php?q=CUS&page=1&per_page=5&sort=cus_id&dir=ASC",
        'cookie' => getEmployeeCookie('CRM'),
    ],
    [
        'name' => '6. File Center Repository',
        'url'  => "$baseUrl/File%20Center/api/documents.php?q=DOC&page=1&per_page=5&sort=doc_id&dir=ASC",
        'cookie' => getEmployeeCookie('DOC'),
    ],
    [
        'name' => '7. Admin Audit Logs',
        'url'  => "$baseUrl/Admin%20&%20Governance%20Portal/api/audit.php?q=SYS&page=1&per_page=5&sort=occurred_at&dir=DESC",
        'cookie' => getEmployeeCookie('ADM'),
    ],
    [
        'name' => '8. Customer Portal Invoices',
        'url'  => "$baseUrl/Customer%20Portal/api/invoices.php?q=INV&page=1&per_page=5&sort=inv_id&dir=DESC",
        'cookie' => $adminCusCookie,
    ],
    [
        'name' => '9. Customer Portal Documents',
        'url'  => "$baseUrl/Customer%20Portal/api/documents.php?q=DOC&page=1&per_page=5&sort=created_at&dir=DESC",
        'cookie' => $adminCusCookie,
    ],
];

echo "=== Running Phase 3 Server-Side List Contract Tests ===\n\n";

$failed = 0;
foreach ($tests as $t) {
    $ch = curl_init($t['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if (!empty($t['cookie'])) {
        curl_setopt($ch, CURLOPT_COOKIE, $t['cookie']);
    }
    $res = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $json = json_decode((string)$res, true);
    $hasRows = isset($json['rows']) && is_array($json['rows']);
    $hasTotal = isset($json['total']);
    $hasPage = isset($json['page']);
    $hasPerPage = isset($json['per_page']);

    if ($httpCode === 200 && $hasRows && $hasTotal && $hasPage && $hasPerPage) {
        echo "[PASS] {$t['name']} - HTTP 200, rows=" . count($json['rows']) . ", total={$json['total']}, page={$json['page']}, per_page={$json['per_page']}\n";
    } else {
        echo "[FAIL] {$t['name']} - HTTP {$httpCode}\n";
        echo "Response: " . substr((string)$res, 0, 200) . "\n";
        $failed++;
    }
}

echo "\nSummary: " . (count($tests) - $failed) . "/" . count($tests) . " passed.\n";
if ($failed > 0) {
    exit(1);
}
echo "ALL LIST CONTRACT TESTS PASSED!\n";
