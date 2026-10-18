<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Maker-Checker Regression Test Suite
 * Phase 2 Verification: Creator != Approver != Payer
 *
 * Ground Rules:
 *  - FIN User A (EMP-1060) creates invoice
 *  - User A cannot approve (403)
 *  - FIN User B (EMP-1059) approves invoice
 *  - User B cannot mark paid (403)
 *  - User A cannot mark paid (403)
 *  - FIN User C (EMP-1061) marks paid (200)
 *  - Customer sees status 'Paid'
 *  - Operations staff (EMP-1014) cannot access Finance (403)
 *  - All denials return 403/404, never 500
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';

$baseUrl = getenv('BASE_URL') ?: 'http://127.0.0.1/Full-11-Projects-FrontEnd';
$pdo = getDbConnection();

$testsTotal = 0;
$testsPassed = 0;
$testsFailed = 0;

function assertCheck(string $desc, bool $condition, string $detail = ''): void {
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

function callFinanceApi(string $url, string $empId, string $method = 'GET', array $payload = []): array {
    global $baseUrl;
    $user = queryUserByCredentials($empId);
    if (!$user) {
        throw new RuntimeException("User {$empId} not found");
    }
    $token = createSsoCookie($user);
    // 1. Fetch CSRF token and session ID for this user
    $chCsrf = curl_init($baseUrl . '/api/csrf.php');
    curl_setopt($chCsrf, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chCsrf, CURLOPT_HEADER, true);
    curl_setopt($chCsrf, CURLOPT_HTTPHEADER, [
        'Cookie: vostok_sso_token=' . $token
    ]);
    $rawRes = (string)curl_exec($chCsrf);
    $headerSize = curl_getinfo($chCsrf, CURLINFO_HEADER_SIZE);
    $headerStr = substr($rawRes, 0, $headerSize);
    $bodyStr = substr($rawRes, $headerSize);
    preg_match('/PHPSESSID=([^;]+)/', $headerStr, $m);
    $phpsessid = $m[1] ?? '';
    $csrfData = json_decode($bodyStr, true) ?? [];
    $csrfToken = $csrfData['csrf_token'] ?? '';

    $ch = curl_init($url);
    $cookieHeader = 'vostok_sso_token=' . $token . ($phpsessid !== '' ? '; PHPSESSID=' . $phpsessid : '');
    $headers = [
        'Accept: application/json',
        'Cookie: ' . $cookieHeader
    ];

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $payload['csrf_token'] = $csrfToken;
        $headers[] = 'X-CSRF-Token: ' . $csrfToken;
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $body = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $data = json_decode((string)$body, true) ?? [];
    return [
        'code' => $httpCode,
        'data' => $data,
        'raw'  => (string)$body
    ];
}

echo "========================================================================\n";
echo "       PHASE 2: FINANCE MAKER-CHECKER ENFORCEMENT REGRESSION TEST       \n";
echo "========================================================================\n\n";

$financeApi = $baseUrl . '/Finance%20&%20Billing/api/finance_api.php';
$createdInvId = null;

// Ensure test personas can authenticate for API tests
$pdo->exec("UPDATE employee_accounts SET must_change_password = 0 WHERE emp_id IN ('EMP-1059', 'EMP-1060', 'EMP-1061', 'EMP-1062', 'EMP-1014')");
$pdo->exec("UPDATE customer_accounts SET must_change_password = 0 WHERE cus_id = 'CUS-1001'");

try {
    // -------------------------------------------------------------------------
    // Step 1: FIN User A (EMP-1060) creates an invoice
    // -------------------------------------------------------------------------
    echo "1. Testing Invoice Creation by FIN Staff (EMP-1060)...\n";
    $createRes = callFinanceApi($financeApi, 'EMP-1060', 'POST', [
        'action' => 'create_invoice',
        'cus_id' => 'CUS-1001',
        'prj_id' => 'PRJ-2026-001',
        'total_value' => '4500.00',
        'currency' => 'EUR',
        'payment_terms' => 'Net-30',
        'items' => json_encode([
            ['description' => 'Milestone Review', 'part_number' => 'VP-TST-01', 'qty' => 1, 'unit_price' => 4500.00]
        ])
    ]);

    assertCheck("1.1 User A create invoice returns HTTP 200", $createRes['code'] === 200, "Received HTTP {$createRes['code']}: {$createRes['raw']}");
    assertCheck("1.2 Create response has success=true", !empty($createRes['data']['success']), $createRes['raw']);
    $createdInvId = $createRes['data']['inv_id'] ?? null;
    assertCheck("1.3 Valid invoice ID generated ({$createdInvId})", !empty($createdInvId));

    // Verify in DB that created_by_emp_id = EMP-1060 and approved_by_emp_id IS NULL
    $dbCheck = $pdo->prepare("SELECT created_by_emp_id, approved_by_emp_id, paid_by_emp_id, payment_status FROM invoices WHERE inv_id = ?");
    $dbCheck->execute([$createdInvId]);
    $invRow = $dbCheck->fetch(PDO::FETCH_ASSOC);

    assertCheck("1.4 Database records created_by_emp_id = EMP-1060", ($invRow['created_by_emp_id'] ?? '') === 'EMP-1060');
    assertCheck("1.5 Newly created invoice is unapproved (approved_by_emp_id IS NULL)", empty($invRow['approved_by_emp_id']));
    assertCheck("1.6 Newly created invoice payment_status is Pending", ($invRow['payment_status'] ?? '') === 'Pending');

    // -------------------------------------------------------------------------
    // Step 2: Maker-Checker Step 2 Denial: Creator User A attempts to Approve
    // -------------------------------------------------------------------------
    echo "\n2. Testing Creator Cannot Approve (EMP-1060 attempting self-approval)...\n";
    $selfApproveRes = callFinanceApi($financeApi, 'EMP-1060', 'POST', [
        'action' => 'approve_invoice',
        'inv_id' => $createdInvId
    ]);

    assertCheck("2.1 Self-approval refused with HTTP 403", $selfApproveRes['code'] === 403, "Received HTTP {$selfApproveRes['code']}: {$selfApproveRes['raw']}");
    assertCheck("2.2 Refusal message explains duty separation violation", 
        str_contains(strtolower($selfApproveRes['data']['error'] ?? ''), 'creator cannot approve') || 
        str_contains(strtolower($selfApproveRes['data']['message'] ?? ''), 'creator cannot approve'),
        $selfApproveRes['raw']
    );

    // -------------------------------------------------------------------------
    // Step 3: Non-manager FIN User attempts to approve (must be CFO or FIN Director L3+)
    // -------------------------------------------------------------------------
    echo "\n3. Testing Approval by Non-Manager Staff (EMP-1062, L2)...\n";
    $l2ApproveRes = callFinanceApi($financeApi, 'EMP-1062', 'POST', [
        'action' => 'approve_invoice',
        'inv_id' => $createdInvId
    ]);
    assertCheck("3.1 L2 FIN staff approval refused with HTTP 403", $l2ApproveRes['code'] === 403, "Received HTTP {$l2ApproveRes['code']}: {$l2ApproveRes['raw']}");

    // -------------------------------------------------------------------------
    // Step 4: Eligible Approver (FIN User B, EMP-1059, Finance Director L3) Approves
    // -------------------------------------------------------------------------
    echo "\n4. Testing Approval by Eligible Manager User B (EMP-1059, FIN Director)...\n";
    $approveRes = callFinanceApi($financeApi, 'EMP-1059', 'POST', [
        'action' => 'approve_invoice',
        'inv_id' => $createdInvId
    ]);

    assertCheck("4.1 User B approval succeeds with HTTP 200", $approveRes['code'] === 200, "Received HTTP {$approveRes['code']}: {$approveRes['raw']}");
    assertCheck("4.2 Approval response has success=true", !empty($approveRes['data']['success']), $approveRes['raw']);

    $dbCheck->execute([$createdInvId]);
    $invRow = $dbCheck->fetch(PDO::FETCH_ASSOC);
    assertCheck("4.3 Database records approved_by_emp_id = EMP-1059", ($invRow['approved_by_emp_id'] ?? '') === 'EMP-1059');

    // -------------------------------------------------------------------------
    // Step 5: Duty Separation Denial: Approver User B attempts to Pay
    // -------------------------------------------------------------------------
    echo "\n5. Testing Approver Cannot Mark Paid (EMP-1059 attempting settlement)...\n";
    $approverPayRes = callFinanceApi($financeApi, 'EMP-1059', 'POST', [
        'action' => 'mark_paid',
        'inv_id' => $createdInvId
    ]);

    assertCheck("5.1 Approver paying refused with HTTP 403", $approverPayRes['code'] === 403, "Received HTTP {$approverPayRes['code']}: {$approverPayRes['raw']}");
    assertCheck("5.2 Refusal message explains duty separation (approver cannot mark paid)",
        str_contains(strtolower($approverPayRes['data']['error'] ?? ''), 'approver cannot') ||
        str_contains(strtolower($approverPayRes['data']['message'] ?? ''), 'approver cannot'),
        $approverPayRes['raw']
    );

    // -------------------------------------------------------------------------
    // Step 6: Duty Separation Denial: Creator User A attempts to Pay
    // -------------------------------------------------------------------------
    echo "\n6. Testing Creator Cannot Mark Paid (EMP-1060 attempting settlement)...\n";
    $creatorPayRes = callFinanceApi($financeApi, 'EMP-1060', 'POST', [
        'action' => 'mark_paid',
        'inv_id' => $createdInvId
    ]);

    assertCheck("6.1 Creator paying refused with HTTP 403", $creatorPayRes['code'] === 403, "Received HTTP {$creatorPayRes['code']}: {$creatorPayRes['raw']}");
    assertCheck("6.2 Refusal message explains duty separation (creator cannot mark paid)",
        str_contains(strtolower($creatorPayRes['data']['error'] ?? ''), 'creator cannot') ||
        str_contains(strtolower($creatorPayRes['data']['message'] ?? ''), 'creator cannot'),
        $creatorPayRes['raw']
    );

    // -------------------------------------------------------------------------
    // Step 7: Eligible Payer User C (EMP-1061, FIN Staff) Marks Invoice Paid
    // -------------------------------------------------------------------------
    echo "\n7. Testing Payment Settlement by Third Distinct FIN Staff (EMP-1061)...\n";
    $payRes = callFinanceApi($financeApi, 'EMP-1061', 'POST', [
        'action' => 'mark_paid',
        'inv_id' => $createdInvId
    ]);

    assertCheck("7.1 User C payment succeeds with HTTP 200", $payRes['code'] === 200, "Received HTTP {$payRes['code']}: {$payRes['raw']}");
    assertCheck("7.2 Payment response has success=true", !empty($payRes['data']['success']), $payRes['raw']);

    $dbCheck->execute([$createdInvId]);
    $invRow = $dbCheck->fetch(PDO::FETCH_ASSOC);
    assertCheck("7.3 Database records paid_by_emp_id = EMP-1061", ($invRow['paid_by_emp_id'] ?? '') === 'EMP-1061');
    assertCheck("7.4 Database records payment_status = Paid", ($invRow['payment_status'] ?? '') === 'Paid');

    // -------------------------------------------------------------------------
    // Step 8: Customer Views Invoice in Customer Portal
    // -------------------------------------------------------------------------
    echo "\n8. Testing Customer Portal Reflection for CUS-1001...\n";
    $cusUser = queryUserByCredentials('CUS-1001');
    $cusToken = createSsoCookie($cusUser);

    $ch = curl_init($baseUrl . '/Customer%20Portal/api/invoices.php?id=' . urlencode($createdInvId));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Cookie: vostok_sso_token=' . $cusToken
    ]);
    $cusResBody = curl_exec($ch);
    $cusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $cusData = json_decode((string)$cusResBody, true) ?? [];
    assertCheck("8.1 Customer Portal returns HTTP 200", $cusCode === 200, "Received HTTP {$cusCode}: {$cusResBody}");
    $returnedStatus = $cusData['data']['payment_status'] ?? $cusData['payment_status'] ?? '';
    assertCheck("8.2 Customer Portal reflects payment_status = Paid", $returnedStatus === 'Paid', "Status was: '{$returnedStatus}'");

    // -------------------------------------------------------------------------
    // Step 9: Operations Staff (EMP-1014) Denied Access to Finance
    // -------------------------------------------------------------------------
    echo "\n9. Testing Operations Staff (EMP-1014, OPS) Isolation from Finance...\n";
    $opsRes = callFinanceApi($financeApi, 'EMP-1014', 'GET', ['action' => 'get_invoice_detail', 'inv_id' => $createdInvId]);
    assertCheck("9.1 Operations employee access denied with HTTP 403", $opsRes['code'] === 403, "Received HTTP {$opsRes['code']}: {$opsRes['raw']}");
    assertCheck("9.2 Response contains clear 403 error message, never 500", !empty($opsRes['data']['error']), $opsRes['raw']);

} finally {
    // Clean up created invoice and line items so baseline remains locked
    if ($createdInvId) {
        $pdo->prepare("DELETE FROM invoice_items WHERE inv_id = ?")->execute([$createdInvId]);
        $pdo->prepare("DELETE FROM payments WHERE inv_id = ?")->execute([$createdInvId]);
        $pdo->prepare("DELETE FROM invoices WHERE inv_id = ?")->execute([$createdInvId]);
    }
}

echo "\n========================================================================\n";
echo "SUMMARY: Total: {$testsTotal} | Passed: {$testsPassed} | Failed: {$testsFailed}\n";
echo "STATUS:  " . ($testsFailed === 0 ? "100% PASS - ALL MAKER-CHECKER ENFORCEMENTS VERIFIED" : "FAILURES DETECTED") . "\n";
echo "========================================================================\n";

exit($testsFailed === 0 ? 0 : 1);
