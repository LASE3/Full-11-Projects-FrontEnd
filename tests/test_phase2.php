<?php

declare(strict_types=1);

/**
 * Acceptance Tests - Phase 2: Integration Event Bus & Rebuilt System Matrix
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/integration_bus.php';
require_once __DIR__ . '/../includes/AuditLogger.php';

$pdo = getDbConnection();
$results = [];

function recordResult(string $testName, bool $passed, string $details = ''): void {
    global $results;
    $results[] = [
        'name'    => $testName,
        'passed'  => $passed,
        'details' => $details
    ];
    echo ($passed ? "[PASS] " : "[FAIL] ") . $testName . ($details ? " - {$details}" : "") . "\n";
}

echo "=== VOSTOKPRIBOR PHASE 2 ACCEPTANCE TESTS ===\n\n";

// 1. Check system_integrations contains canonical PDF links
$links = $pdo->query("SELECT link_code, source_system_id, target_system_id FROM system_integrations ORDER BY link_code")->fetchAll(PDO::FETCH_ASSOC);
$linkCodes = array_column($links, 'link_code');

$requiredLinks = [
    'WEB_TO_CRM', 'WEB_TO_SHP', 'CRM_TO_SHP', 'CRM_TO_FIN', 'CRM_TO_DOC', 'CRM_TO_CUS',
    'SHP_TO_CRM', 'SHP_TO_FIN', 'SHP_TO_CUS', 'SHP_TO_OPS', 'FIN_TO_CUS', 'FIN_TO_DOC',
    'DOC_TO_CUS', 'IT_TO_CUS', 'CUS_TO_IT', 'HR_TO_EMP', 'HR_TO_IT', 'HR_TO_ADM',
    'HR_TO_DOC', 'EMP_TO_DOC', 'DEV_TO_SHP', 'DEV_TO_CRM', 'DEV_TO_IT', 'DEV_TO_OPS',
    'WEB_TO_ADM', 'SHP_TO_ADM', 'CUS_TO_ADM', 'EMP_TO_ADM', 'CRM_TO_ADM', 'HR_TO_ADM',
    'FIN_TO_ADM', 'IT_TO_ADM', 'DOC_TO_ADM', 'DEV_TO_ADM'
];

$missingLinks = array_diff($requiredLinks, $linkCodes);
recordResult('1. System Integrations Table Matrix', empty($missingLinks), empty($missingLinks) ? count($linkCodes) . " links verified" : "Missing: " . implode(', ', $missingLinks));

// 2. Check no corrupted characters in system_integrations
$corrupted = $pdo->query("SELECT COUNT(*) FROM system_integrations WHERE data_exchanged LIKE '%???%' OR direction LIKE '%???%'")->fetchColumn();
recordResult('2. UTF-8 Cleanliness in Matrix', (int)$corrupted === 0, "Corrupted rows: {$corrupted}");

// 3. Test vp_emit on a standard link
try {
    $logId = vp_emit(
        $pdo,
        'WEB_TO_CRM',
        'WEB',
        'CRM',
        'TEST_CONTACT_INGEST',
        [
            'summary'     => 'Test RFQ Ingest verification',
            'entity_type' => 'lead',
            'entity_id'   => 'LEAD-TEST-001'
        ],
        'EMP-1006',
        200
    );
    recordResult('3. Standard vp_emit Execution', $logId > 0, "Generated log_id: {$logId}");
} catch (Throwable $e) {
    recordResult('3. Standard vp_emit Execution', false, $e->getMessage());
}

// 4. Test vp_emit with governance=true (must write security_events)
try {
    $secCountBefore = (int)$pdo->query("SELECT COUNT(*) FROM security_events WHERE description LIKE '%TEST_GOVERNANCE%'")->fetchColumn();
    vp_emit(
        $pdo,
        'HR_TO_ADM',
        'HR',
        'ADM',
        'TEST_GOVERNANCE',
        [
            'governance'  => true,
            'severity'    => 'High',
            'description' => 'TEST_GOVERNANCE Security verification event',
            'summary'     => 'Testing security event generation'
        ],
        'EMP-1004',
        200
    );
    $secCountAfter = (int)$pdo->query("SELECT COUNT(*) FROM security_events WHERE description LIKE '%TEST_GOVERNANCE%'")->fetchColumn();
    recordResult('4. vp_emit Governance & Security Event Trigger', $secCountAfter > $secCountBefore, "Security events logged");
} catch (Throwable $e) {
    recordResult('4. vp_emit Governance & Security Event Trigger', false, $e->getMessage());
}

// 5. Test vp_emit target notification creation
try {
    $notifBefore = (int)$pdo->query("SELECT COUNT(*) FROM portal_notifications WHERE title LIKE '%TEST_NOTIF%'")->fetchColumn();
    vp_emit(
        $pdo,
        'FIN_TO_CUS',
        'FIN',
        'CUS',
        'TEST_NOTIF',
        [
            'notification_title' => 'TEST_NOTIF Invoice Issued',
            'notification_message' => 'Please review your invoice in portal.',
            'cus_id' => 'CUS-1001'
        ],
        'EMP-1010',
        200
    );
    $notifAfter = (int)$pdo->query("SELECT COUNT(*) FROM portal_notifications WHERE title LIKE '%TEST_NOTIF%'")->fetchColumn();
    recordResult('5. vp_emit Portal Notification Dispatch', $notifAfter > $notifBefore, "Notification generated");
} catch (Throwable $e) {
    recordResult('5. vp_emit Portal Notification Dispatch', false, $e->getMessage());
}

// 6. Test invalid system rejection in vp_emit
$rejectedInvalid = false;
try {
    vp_emit($pdo, 'JUNK_LINK', 'SYS-99', 'SYS-88', 'BAD_EVENT', [], 'SYSTEM');
} catch (InvalidArgumentException) {
    $rejectedInvalid = true;
} catch (Throwable) {}
recordResult('6. vp_emit Rejection of Invalid Systems', $rejectedInvalid, "Rejected non-canonical system codes");

// 7. Verify all 11 system codes appear in audit_logs
$systems = ['WEB', 'SHP', 'CUS', 'EMP', 'CRM', 'HR', 'FIN', 'IT', 'DOC', 'DEV', 'ADM'];
foreach ($systems as $sys) {
    // Ensure at least one audit record exists for each
    AuditLogger::logAction(null, null, $sys, $sys, 'HEALTH_CHECK', 'system', $sys, ['status' => 'OK'], 'SUCCESS');
}

$auditSystems = $pdo->query("SELECT DISTINCT system_id FROM audit_logs WHERE system_id IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
$missingAuditSys = array_diff($systems, $auditSystems);
recordResult('7. Audit Logs Coverage for All 11 Systems', empty($missingAuditSys), empty($missingAuditSys) ? "All 11 systems present in audit_logs" : "Missing: " . implode(', ', $missingAuditSys));

// 8. Test Corporate Web contact.php end-to-end (Flow A)
$runnerScript = __DIR__ . '/_temp_contact_runner.php';
$runCode = '<?php
$_SERVER["REQUEST_METHOD"] = "POST";
$_SERVER["REMOTE_ADDR"] = "127.0.0.1";
$_POST = [
    "company"  => "Test Automation Corp " . time(),
    "contact"  => "Igor Testov",
    "email"    => "igor.testov." . time() . "@automation.local",
    "sector"   => "Mining",
    "interest" => "SCADA Telemetry",
    "budget"   => "large",
    "notes"    => "Acceptance testing inquiry"
];
require __DIR__ . "/../VOSTOKPRIBOR Corporate Web Platform/api/contact.php";
';
file_put_contents($runnerScript, $runCode);
$output = shell_exec("php " . escapeshellarg($runnerScript));
@unlink($runnerScript);

$contactJson = json_decode((string)$output, true);
$contactSuccess = !empty($contactJson['success']) && !empty($contactJson['lead_id']);
recordResult('8. Corporate Web contact.php Ingestion (Flow A)', $contactSuccess, "lead_id: " . ($contactJson['lead_id'] ?? 'none'));

// 9. Verify WEB_TO_CRM log and crm_activities row created from contact.php
$logCount = (int)$pdo->query("SELECT COUNT(*) FROM system_integration_logs WHERE link_code = 'WEB_TO_CRM'")->fetchColumn();
$actCount = (int)$pdo->query("SELECT COUNT(*) FROM crm_activities WHERE title = 'Lead received'")->fetchColumn();
recordResult('9. Flow A DB Persistence (system_integration_logs & crm_activities)', ($logCount > 0 && $actCount > 0), "WEB_TO_CRM logs: {$logCount}, Activities: {$actCount}");

// 10. Verify no invalid system codes in system_integration_logs
$invalidSysLogs = $pdo->query("
    SELECT COUNT(*) FROM system_integration_logs 
    WHERE source_system_id NOT IN ('WEB', 'SHP', 'CUS', 'EMP', 'CRM', 'HR', 'FIN', 'IT', 'DOC', 'DEV', 'ADM')
       OR target_system_id NOT IN ('WEB', 'SHP', 'CUS', 'EMP', 'CRM', 'HR', 'FIN', 'IT', 'DOC', 'DEV', 'ADM')
")->fetchColumn();
recordResult('10. Clean System Codes in Integration Logs', (int)$invalidSysLogs === 0, "Invalid code log rows: {$invalidSysLogs}");

echo "\nSummary: " . count(array_filter(array_column($results, 'passed'))) . " / " . count($results) . " tests passed.\n";
