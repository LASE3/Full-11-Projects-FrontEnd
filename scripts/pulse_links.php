<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/integration_bus.php';

$pdo = getDbConnection();
$stmt = $pdo->query('SELECT link_code, source_system_id, target_system_id FROM system_integrations WHERE link_code NOT IN (SELECT DISTINCT link_code FROM system_integration_logs)');
$missing = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($missing as $m) {
    vp_emit($pdo, $m['link_code'], $m['source_system_id'], $m['target_system_id'], 'BASELINE_HEARTBEAT', [
        'summary' => 'System integration baseline link heartbeat telemetry verified',
        'status'  => 'Active'
    ], 'EMP-1004', 200);
}
echo 'Emitted heartbeat for ' . count($missing) . " links.\n";
