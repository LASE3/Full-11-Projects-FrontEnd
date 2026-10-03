<?php

/**
 * VOSTOKPRIBOR System 11 // GOV-CORE API Manifest
 */

require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('ADM', ['min_clearance' => 'L3']);

require_once __DIR__ . '/db_helper.php';

try {
    $pdo = getDbConnection();
    gov_ensureSchemaReady($pdo);

    $policyCount = (int)$pdo->query("SELECT COUNT(*) FROM security_policies")->fetchColumn();
    $riskCount = (int)$pdo->query("SELECT COUNT(*) FROM risk_register")->fetchColumn();
    $controlCount = (int)$pdo->query("SELECT COUNT(*) FROM compliance_controls")->fetchColumn();
    $auditCount = (int)$pdo->query("SELECT COUNT(*) FROM audit_logs")->fetchColumn();
    $isolatedCount = (int)$pdo->query("SELECT COUNT(*) FROM systems_catalog WHERE status = 'ISOLATED'")->fetchColumn();

    jsonResponse([
        'success' => true,
        'status' => 'ONLINE',
        'system' => 'SYSTEM 11 // GOV-CORE',
        'subsystem' => 'ADMINISTRATION & AUTONOMOUS GOVERNANCE PORTAL',
        'version' => '11.4.2-PROD',
        'timestamp' => date('c'),
        'database' => 'vostokpribor',
        'telemetry' => [
            'total_statutory_policies' => $policyCount,
            'risk_register_entries' => $riskCount,
            'compliance_controls' => $controlCount,
            'audit_records' => $auditCount,
            'systems_isolated' => $isolatedCount,
            'defcon_level' => ($isolatedCount > 0) ? 'DEFCON-1' : 'DEFCON-4'
        ],
        'endpoints' => [
            'policies' => 'api/policies.php',
            'risks' => 'api/risks.php',
            'compliance' => 'api/compliance.php',
            'privileged' => 'api/privileged.php',
            'lockdown' => 'api/lockdown.php',
            'audit' => 'api/audit.php',
            'recert' => 'api/recert.php'
        ]
    ]);
} catch (Exception $e) {
    jsonResponse([
        'status' => 'ERROR',
        'message' => $e->getMessage()
    ], 500);
}
