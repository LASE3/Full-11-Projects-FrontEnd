<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Phase 4 Regression Test Suite
 * File Center Document Ownership & Developer Credential Governance
 *
 * Rules:
 *  - EMP-1017 (non-owner) attempts mutating actions against document DOC-2026-003 (owned by EMP-1019)
 *    Actions: edit, change classification, legal hold, approve, reject, archive, delete -> all 403/404, DB unchanged.
 *  - Classification lowering: non-classification-authority is refused with 403.
 *  - Developer credentials created by EMP-1020 succeeds (HTTP 200, no 500).
 *  - Non-owner (EMP-1017) attempting revoke/delete returns 403 (not 500).
 *  - Owner (EMP-1020) can revoke and delete.
 *  - Negative authorization tests across developer and intranet endpoints.
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

function callApi(string $method, string $path, array $user, array $data = []): array {
    global $baseUrl;
    $token = createSsoCookie($user);

    // CSRF fetch
    $chCsrf = curl_init($baseUrl . '/api/csrf.php');
    curl_setopt($chCsrf, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chCsrf, CURLOPT_HEADER, true);
    curl_setopt($chCsrf, CURLOPT_HTTPHEADER, ['Cookie: vostok_sso_token=' . $token]);
    $rawCsrf = (string)curl_exec($chCsrf);
    $headerSize = curl_getinfo($chCsrf, CURLINFO_HEADER_SIZE);
    $headerStr = substr($rawCsrf, 0, $headerSize);
    $bodyStr = substr($rawCsrf, $headerSize);
    preg_match('/PHPSESSID=([^;]+)/', $headerStr, $m);
    $phpsessid = $m[1] ?? '';
    $csrfData = json_decode($bodyStr, true) ?? [];
    $csrfToken = $csrfData['csrf_token'] ?? '';

    $encodedPath = str_replace(' ', '%20', $path);
    $ch = curl_init($baseUrl . $encodedPath);
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'Cookie: vostok_sso_token=' . $token . ($phpsessid ? '; PHPSESSID=' . $phpsessid : ''),
        'X-CSRF-Token: ' . $csrfToken
    ];

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if (!empty($data) || in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $raw = (string)curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    return [
        'code' => $code,
        'data' => json_decode($raw, true) ?? [],
        'raw'  => $raw
    ];
}

echo "========================================================================\n";
echo "   PHASE 4: FILE CENTER OWNERSHIP & DEVELOPER GOVERNANCE REGRESSION    \n";
echo "========================================================================\n\n";

// Unlock must_change_password for test actors
$pdo->exec("UPDATE employee_accounts SET must_change_password = 0 WHERE emp_id IN ('EMP-1001', 'EMP-1012', 'EMP-1014', 'EMP-1017', 'EMP-1019', 'EMP-1020')");

$emp1017 = queryUserByCredentials('EMP-1017'); // Dana Yermak (ENG, L3, non-owner)
$emp1019 = queryUserByCredentials('EMP-1019'); // Farida Iskakova (ENG, L3, owner of DOC-2026-003)
$emp1020 = queryUserByCredentials('EMP-1020'); // Jonas Richter (Senior Developer)
$emp1014 = queryUserByCredentials('EMP-1014'); // Low-privilege employee (OPS, L2)
$emp1001 = queryUserByCredentials('EMP-1001'); // Viktor Sokolov (CEO, SuperAdmin, L4)

// -------------------------------------------------------------------------
// Part 1: Non-owner (EMP-1017) mutating document owned by EMP-1019 (DOC-2026-003)
// -------------------------------------------------------------------------
echo "1. Testing non-owner (EMP-1017) against document owned by EMP-1019 (DOC-2026-003)...\n";
$targetDocId = 'DOC-2026-003';
$snapStmt = $pdo->prepare("SELECT * FROM documents WHERE doc_id = ?");
$snapStmt->execute([$targetDocId]);
$docBefore = $snapStmt->fetch(PDO::FETCH_ASSOC);

// 1.1 Edit (PUT)
$resEdit = callApi('PUT', '/File Center/api/documents.php', $emp1017, [
    'doc_id' => $targetDocId,
    'file_name' => 'Tampered_Statement_of_Work.pdf',
    'description' => 'Unauthorized modification by EMP-1017'
]);
assertRule("1.1 Non-owner edit refused with 403/404", in_array($resEdit['code'], [403, 404], true), "Code: {$resEdit['code']} {$resEdit['raw']}");

// 1.2 Change classification (POST action=change_classification)
$resClassPost = callApi('POST', '/File Center/api/documents.php', $emp1017, [
    'action' => 'change_classification',
    'doc_id' => $targetDocId,
    'classification' => 'Public'
]);
assertRule("1.2 Non-owner change_classification (POST) refused with 403/404", in_array($resClassPost['code'], [403, 404], true), "Code: {$resClassPost['code']} {$resClassPost['raw']}");

// 1.3 Change classification via PUT
$resClassPut = callApi('PUT', '/File Center/api/documents.php', $emp1017, [
    'doc_id' => $targetDocId,
    'classification' => 'Internal'
]);
assertRule("1.3 Non-owner change_classification (PUT) refused with 403/404", in_array($resClassPut['code'], [403, 404], true), "Code: {$resClassPut['code']} {$resClassPut['raw']}");

// 1.4 Legal hold toggle
$resHold = callApi('POST', '/File Center/api/documents.php', $emp1017, [
    'action' => 'toggle_legal_hold',
    'doc_id' => $targetDocId
]);
assertRule("1.4 Non-owner legal hold toggle refused with 403/404", in_array($resHold['code'], [403, 404], true), "Code: {$resHold['code']} {$resHold['raw']}");

// 1.5 Approve
$resApprove = callApi('POST', '/File Center/api/documents.php', $emp1017, [
    'action' => 'approve',
    'doc_id' => $targetDocId
]);
assertRule("1.5 Non-owner approve decision refused with 403/404", in_array($resApprove['code'], [403, 404], true), "Code: {$resApprove['code']} {$resApprove['raw']}");

// 1.6 Reject
$resReject = callApi('POST', '/File Center/api/documents.php', $emp1017, [
    'action' => 'reject',
    'doc_id' => $targetDocId
]);
assertRule("1.6 Non-owner reject decision refused with 403/404", in_array($resReject['code'], [403, 404], true), "Code: {$resReject['code']} {$resReject['raw']}");

// 1.7 Archive
$resArchive = callApi('POST', '/File Center/api/documents.php', $emp1017, [
    'action' => 'archive',
    'doc_id' => $targetDocId
]);
assertRule("1.7 Non-owner archive refused with 403/404", in_array($resArchive['code'], [403, 404], true), "Code: {$resArchive['code']} {$resArchive['raw']}");

// 1.8 Delete
$resDelete = callApi('DELETE', '/File Center/api/documents.php', $emp1017, [
    'doc_id' => $targetDocId
]);
assertRule("1.8 Non-owner delete refused with 403/404", in_array($resDelete['code'], [403, 404], true), "Code: {$resDelete['code']} {$resDelete['raw']}");

// Verify Database is completely untouched
$snapStmt->execute([$targetDocId]);
$docAfter = $snapStmt->fetch(PDO::FETCH_ASSOC);
assertRule("1.9 Database row for DOC-2026-003 remained 100% UNCHANGED", $docBefore === $docAfter, json_encode(array_diff_assoc($docBefore, $docAfter)));

// -------------------------------------------------------------------------
// Part 2: Classification Authority Lowering Restriction
// -------------------------------------------------------------------------
echo "\n2. Testing classification lowering restrictions...\n";
// Owner EMP-1019 attempts to lower classification from Confidential to Public
// Owner is L3 in ENG (not an L4 classification authority, not GOV/ADM)
$resOwnerLower = callApi('POST', '/File Center/api/documents.php', $emp1019, [
    'action' => 'change_classification',
    'doc_id' => $targetDocId,
    'classification' => 'Public'
]);
assertRule("2.1 Non-classification-authority owner cannot lower classification (HTTP 403)", $resOwnerLower['code'] === 403, "Code: {$resOwnerLower['code']} {$resOwnerLower['raw']}");

// Classification authority (SuperAdmin / L4) CAN lower classification
$resAdminLower = callApi('POST', '/File Center/api/documents.php', $emp1001, [
    'action' => 'change_classification',
    'doc_id' => $targetDocId,
    'classification' => 'Confidential' // Restore / maintain
]);
assertRule("2.2 Classification authority (SuperAdmin/L4) permitted", $resAdminLower['code'] === 200, "Code: {$resAdminLower['code']} {$resAdminLower['raw']}");

// -------------------------------------------------------------------------
// Part 3: Developer Credentials: Creation & Ownership Enforcement
// -------------------------------------------------------------------------
echo "\n3. Testing Developer Credentials (EMP-1020 creation & ownership)...\n";
// 3.1 Creation by EMP-1020
$resDevCreate = callApi('POST', '/Developer/api/credentials.php', $emp1020, [
    'action' => 'create',
    'label' => 'Production Enclave Gateway Key',
    'environment' => 'Production'
]);
assertRule("3.1 Developer credential creation by EMP-1020 succeeds with HTTP 200 (no 500)", $resDevCreate['code'] === 200, "Code: {$resDevCreate['code']} {$resDevCreate['raw']}");
$devKeyId = (int)($resDevCreate['data']['data']['id'] ?? 0);
assertRule("3.2 Valid generated Key ID returned", $devKeyId > 0, "Key ID: {$devKeyId}");

// 3.2 Non-owner (EMP-1017) attempts to revoke the key
$resNonOwnerRevoke = callApi('POST', '/Developer/api/credentials.php', $emp1017, [
    'action' => 'revoke',
    'id' => $devKeyId
]);
assertRule("3.3 Non-owner revoke denied with HTTP 403 (never 500)", $resNonOwnerRevoke['code'] === 403, "Code: {$resNonOwnerRevoke['code']} {$resNonOwnerRevoke['raw']}");

// 3.3 Non-owner (EMP-1017) attempts to delete the key
$resNonOwnerDelete = callApi('POST', '/Developer/api/credentials.php', $emp1017, [
    'action' => 'delete',
    'id' => $devKeyId
]);
assertRule("3.4 Non-owner delete denied with HTTP 403 (never 500)", $resNonOwnerDelete['code'] === 403, "Code: {$resNonOwnerDelete['code']} {$resNonOwnerDelete['raw']}");

// 3.4 Owner (EMP-1020) revokes the key
$resOwnerRevoke = callApi('POST', '/Developer/api/credentials.php', $emp1020, [
    'action' => 'revoke',
    'id' => $devKeyId
]);
assertRule("3.5 Owner (EMP-1020) can revoke key with HTTP 200", $resOwnerRevoke['code'] === 200, "Code: {$resOwnerRevoke['code']} {$resOwnerRevoke['raw']}");

// 3.5 Owner (EMP-1020) deletes the key
$resOwnerDelete = callApi('POST', '/Developer/api/credentials.php', $emp1020, [
    'action' => 'delete',
    'id' => $devKeyId
]);
assertRule("3.6 Owner (EMP-1020) can delete key with HTTP 200", $resOwnerDelete['code'] === 200, "Code: {$resOwnerDelete['code']} {$resOwnerDelete['raw']}");

// -------------------------------------------------------------------------
// Part 4: Negative authorization tests on newly wired endpoints
// -------------------------------------------------------------------------
echo "\n4. Negative authorization tests on newly wired endpoints...\n";

// 4.1 Developer endpoints (create by unauthorized employee EMP-1014)
$resNegEp = callApi('POST', '/Developer/api/endpoints.php', $emp1014, [
    'action' => 'create',
    'title' => 'Malicious Endpoint',
    'path' => '/v1/tamper'
]);
assertRule("4.1 Unauthorized employee denied endpoint creation (HTTP 403)", $resNegEp['code'] === 403, "Code: {$resNegEp['code']} {$resNegEp['raw']}");

// 4.2 Developer guides (create by unauthorized employee EMP-1014)
$resNegGuide = callApi('POST', '/Developer/api/guides.php', $emp1014, [
    'action' => 'create',
    'title' => 'Bogus Guide',
    'summary' => 'Bogus summary'
]);
assertRule("4.2 Unauthorized employee denied guide creation (HTTP 403)", $resNegGuide['code'] === 403, "Code: {$resNegGuide['code']} {$resNegGuide['raw']}");

// 4.3 Developer webhooks (create by unauthorized employee EMP-1014)
$resNegWh = callApi('POST', '/Developer/api/metrics.php', $emp1014, [
    'action' => 'create_webhook',
    'event_type' => 'tamper.event',
    'target_endpoint' => 'https://attacker.local'
]);
assertRule("4.3 Unauthorized employee denied webhook creation (HTTP 403)", $resNegWh['code'] === 403, "Code: {$resNegWh['code']} {$resNegWh['raw']}");

// 4.4 Intranet announcement (create by unauthorized employee EMP-1014)
$resNegAnn = callApi('POST', '/api/v1/intranet/announcements.php', $emp1014, [
    'title' => 'Fake Announcement',
    'body'  => 'Unauthorized announcement text'
]);
assertRule("4.4 Unauthorized employee denied intranet announcement publishing (HTTP 403)", $resNegAnn['code'] === 403, "Code: {$resNegAnn['code']} {$resNegAnn['raw']}");

echo "\n========================================================================\n";
echo "SUMMARY: Total: {$testsTotal} | Passed: {$testsPassed} | Failed: {$testsFailed}\n";
if ($testsFailed === 0) {
    echo "STATUS:  100% PASS - PHASE 4 REGRESSION TEST COMPLETE\n";
} else {
    echo "STATUS:  FAILURES DETECTED\n";
}
echo "========================================================================\n";

exit($testsFailed > 0 ? 1 : 0);
