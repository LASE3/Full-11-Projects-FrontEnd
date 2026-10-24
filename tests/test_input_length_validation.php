<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Input Length Validation & Audit Logger Truncation Test
 *
 * Verifies:
 * 1. Over-long values (> limits) in ID/code fields return HTTP 422 with a clear error message.
 * 2. AuditLogger truncates long values defensively so audit logging can never crash a request.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/AuditLogger.php';
require_once __DIR__ . '/../includes/helpers/Validator.php';
require_once __DIR__ . '/../Admin & Governance Portal/api/db_helper.php';

$pdo = getDbConnection();
$passed = 0;
$total = 0;

function assertTest(string $desc, bool $condition, string $detail = ''): void {
    global $passed, $total;
    $total++;
    if ($condition) {
        $passed++;
        echo "  [PASS] {$desc}\n";
    } else {
        echo "  [FAIL] {$desc}: {$detail}\n";
    }
}

echo "========================================================================\n";
echo "       INPUT LENGTH VALIDATION & AUDIT LOGGER TRUNCATION TEST           \n";
echo "========================================================================\n\n";

// 1. Validator unit test
$overlongEmp = str_repeat('E', 50);
$overlongCus = str_repeat('C', 50);
$overlongTarget = str_repeat('T', 100);

$errors = Validator::validateLengths([
    'emp_id'           => $overlongEmp,
    'cus_id'           => $overlongCus,
    'target_entity_id' => $overlongTarget,
    'normal_field'     => 'valid_value'
]);

assertTest("Validator detects overlong emp_id", isset($errors['emp_id']), $errors['emp_id'] ?? 'Not found');
assertTest("Validator detects overlong cus_id", isset($errors['cus_id']), $errors['cus_id'] ?? 'Not found');
assertTest("Validator detects overlong target_entity_id", isset($errors['target_entity_id']), $errors['target_entity_id'] ?? 'Not found');
assertTest("Validator ignores valid fields", !isset($errors['normal_field']));

// 2. AuditLogger defensive truncation
$long120 = str_repeat('Z', 120);
$auditError = null;
try {
    AuditLogger::logAction($long120, $long120, 'System Name Exceeding Normal Limit Length Test', 'ADM_EXTRA_LONG', 'TEST_ACTION', 'target_entity_type_super_long', $long120);
} catch (Throwable $e) {
    $auditError = $e->getMessage();
}
assertTest("AuditLogger::logAction handles 120-char values without crash", $auditError === null, (string)$auditError);

// 3. db_helper recordAuditEntry defensive truncation
$govError = null;
try {
    $res = recordAuditEntry($pdo, 'ACTION_LONG', 'TYPE_LONG', $long120, 'SUCCESS', ['test' => true], 'ADM');
} catch (Throwable $e) {
    $govError = $e->getMessage();
}
assertTest("recordAuditEntry handles 120-char values without crash", $govError === null, (string)$govError);

// 4. HTTP API Test with overlong query parameter
$baseUrl = getenv('BASE_URL') ?: 'http://127.0.0.1/Full-11-Projects-FrontEnd';
require_once __DIR__ . '/../includes/auth_guard.php';
$user = queryUserByCredentials('EMP-1001');
$token = createSsoCookie($user);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "{$baseUrl}/IT%20Helpdesk/api/assets.php?asset_id=" . urlencode($long120));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, "vostok_sso_token={$token}");
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$json = json_decode((string)$resp, true);
assertTest("HTTP request with overlong asset_id returns HTTP 422", $code === 422, "Got HTTP {$code}: {$resp}");
assertTest("422 response includes clear error message", !empty($json['message']) && str_contains($json['message'], 'asset_id'), "Response: {$resp}");

echo "\n========================================================================\n";
echo "SUMMARY: {$passed} / {$total} tests passed.\n";
echo "========================================================================\n";
exit($passed === $total ? 0 : 1);
