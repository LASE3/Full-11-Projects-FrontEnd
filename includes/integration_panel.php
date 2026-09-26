<?php

/**
 * VOSTOKPRIBOR Universal Integration Panel & Drawer
 * Renders the system's Integration architecture, specifications, and live logs:
 *   - API / Protocol
 *   - Authentication
 *   - Data Exchanged
 *   - Direction
 *   - Permissions
 *   - Live Logs with Trigger Action
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';

function renderSystemIntegrationView($systemCode, $pageMode = 'standalone')
{
    $pdo = getDbConnection();

    // Map system code to alias
    $codeMap = [
        'SYS01' => ['id' => 'ADM', 'name' => 'Admin & Governance Portal'],
        'SYS02' => ['id' => 'CRM', 'name' => 'CRM System'],
        'SYS03' => ['id' => 'CUS', 'name' => 'Customer Portal'],
        'SYS04' => ['id' => 'DEV', 'name' => 'Developer Portal'],
        'SYS05' => ['id' => 'EMP', 'name' => 'Employee Intranet'],
        'SYS06' => ['id' => 'DOC', 'name' => 'File Center'],
        'SYS07' => ['id' => 'FIN', 'name' => 'Finance & Billing'],
        'SYS08' => ['id' => 'HR',  'name' => 'HR System'],
        'SYS09' => ['id' => 'IT',  'name' => 'IT Helpdesk'],
        'SYS10' => ['id' => 'SHP', 'name' => 'Online Shop B2B'],
        'SYS11' => ['id' => 'WEB', 'name' => 'Corporate Web Platform']
    ];

    $sysMeta = $codeMap[$systemCode] ?? ['id' => $systemCode, 'name' => 'Enterprise Subsystem'];
    $sysId = $sysMeta['id'];
    $sysName = $sysMeta['name'];

    // Current user context
    $currentUser = $_SESSION['vostok_user'] ?? null;
    $userClearance = $currentUser['clearance_level'] ?? 'L1';
    $isSuperAdmin = ($userClearance === 'L4' || ($currentUser['email'] ?? '') === 'admin@gmail.com');

    // Query active integrations for this system
    $stmt = $pdo->prepare("
        SELECT * FROM system_integrations 
        WHERE source_system_id = :id1 OR target_system_id = :id2 OR target_system_id = 'ALL'
        ORDER BY link_code ASC
    ");
    $stmt->execute([':id1' => $sysId, ':id2' => $sysId]);
    $integrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Query recent logs for this system
    $logStmt = $pdo->prepare("
        SELECT * FROM system_integration_logs 
        WHERE source_system_id = :lid1 OR target_system_id = :lid2 OR target_system_id = 'ALL'
        ORDER BY log_id DESC LIMIT 30
    ");
    $logStmt->execute([':lid1' => $sysId, ':lid2' => $sysId]);
    $logs = $logStmt->fetchAll(PDO::FETCH_ASSOC);

    // Render HTML view
?>
    <link rel="stylesheet" href="../assets/css/integration-panel.css">
    <div class="vostok-integration-container" id="vostok-integration-root" data-system-code="<?= htmlspecialchars($systemCode) ?>">

        <!-- Top Header -->
        <div class="vostok-int-header">
            <div>
                <div class="vostok-header-badge-row">
                    <span class="vostok-int-badge"><?= htmlspecialchars($systemCode) ?></span>
                    <span class="vostok-int-connected-label">CONNECTED SYSTEM</span>
                </div>
                <h2 class="vostok-int-title">
                    <?= htmlspecialchars($sysName) ?> &mdash; Inter-System Integrations
                </h2>
                <div class="vostok-int-subtitle">
                    Universal Data Bus &bull; Topology: Strict 12-Channel Interconnect Architecture
                </div>
            </div>

            <div class="vostok-int-status-group">
                <span class="vostok-status-pill success">
                    <span class="vostok-status-dot"></span>
                    <?= count($integrations) ?> Channel<?= count($integrations) !== 1 ? 's' : '' ?> Active
                </span>
                <span class="vostok-user-badge">
                    Clearance: <strong class="<?= $isSuperAdmin ? 'text-amber-500' : 'text-sky-400' ?>"><?= htmlspecialchars($userClearance) ?><?= $isSuperAdmin ? ' (SuperAdmin)' : '' ?></strong>
                </span>
            </div>
        </div>

        <h3 class="vostok-section-title">
            <span>Configured Integration Links</span>
            <span class="vostok-int-section-subheading">(API, Protocol, Auth, Data, Direction, Clearance)</span>
        </h3>

        <div class="vostok-int-grid">
            <?php if (empty($integrations)): ?>
                <div class="vostok-empty-state">
                    <p class="text-slate-400 m-0">No active integration channels assigned to this subsystem.</p>
                </div>
            <?php else: ?>
                <?php foreach ($integrations as $int):
                    $isOutbound = ($int['source_system_id'] === $sysId);
                    $isBroadcast = ($int['target_system_id'] === 'ALL');
                    $cardType = $isBroadcast ? 'broadcast' : ($isOutbound ? 'outbound' : 'inbound');
                ?>
                    <div class="vostok-card <?= $cardType ?>" id="card-<?= htmlspecialchars($int['link_code']) ?>">
                        <!-- Header with Direction & Link Code -->
                        <div class="vostok-card-header">
                            <div>
                                <span class="vostok-card-link-code">
                                    <?= htmlspecialchars($int['link_code']) ?>
                                </span>
                                <div class="vostok-card-route">
                                    <?= htmlspecialchars($int['source_system_id']) ?> &rarr; <?= htmlspecialchars($int['target_system_id']) ?>
                                </div>
                            </div>
                            <span class="vostok-dir-badge">
                                <?= htmlspecialchars($int['direction']) ?>
                            </span>
                        </div>

                        <!-- 1. API / Protocol -->
                        <div class="vostok-meta-row">
                            <span class="vostok-meta-label">1. API / Protocol</span>
                            <span class="vostok-meta-value text-sky-400">
                                <?= htmlspecialchars($int['api_protocol']) ?>
                            </span>
                        </div>

                        <!-- 2. Authentication -->
                        <div class="vostok-meta-row">
                            <span class="vostok-meta-label">2. Authentication</span>
                            <span class="vostok-meta-value text-emerald-200">
                                <?= htmlspecialchars($int['authentication_method']) ?>
                            </span>
                        </div>

                        <!-- 3. Data Exchanged -->
                        <div class="vostok-meta-row">
                            <span class="vostok-meta-label">3. Data Exchanged</span>
                            <span class="vostok-meta-value text-slate-200 text-xs">
                                <?= htmlspecialchars($int['data_exchanged']) ?>
                            </span>
                        </div>

                        <!-- 4. Direction -->
                        <div class="vostok-meta-row">
                            <span class="vostok-meta-label">4. Direction</span>
                            <span class="vostok-meta-value">
                                <?= htmlspecialchars($int['direction']) ?>
                            </span>
                        </div>

                        <!-- 5. Permissions -->
                        <div class="vostok-meta-row">
                            <span class="vostok-meta-label">5. Permissions & Clearance</span>
                            <span class="vostok-meta-value flex items-center justify-between">
                                <span>Required: <strong><?= htmlspecialchars($int['required_clearance']) ?></strong></span>
                                <?php if ($isSuperAdmin || $userClearance >= $int['required_clearance']): ?>
                                    <span class="text-emerald-400 text-xs font-mono">&#10003; AUTHORIZED</span>
                                <?php else: ?>
                                    <span class="text-rose-400 text-xs font-mono">&#10007; RESTRICTED</span>
                                <?php endif; ?>
                            </span>
                        </div>

                        <!-- 6. Trigger / Action -->
                        <button type="button"
                            class="vostok-sync-btn"
                            onclick="window.triggerVostokIntegration('<?= htmlspecialchars($int['link_code']) ?>', this)">
                            <span>&#9889; Dispatch Test Exchange</span>
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- 6. Logs Section: Real-time Integration Logs from system_integration_logs -->
        <div class="vostok-logs-header">
            <h3 class="vostok-section-title">
                <span>Integration Audit Logs (system_integration_logs)</span>
            </h3>
            <button type="button"
                onclick="window.refreshVostokLogs('<?= htmlspecialchars($systemCode) ?>')"
                class="vostok-btn-refresh">
                &#8635; Refresh Logs
            </button>
        </div>

        <div class="vostok-logs-table-wrap">
            <div class="overflow-x-auto">
                <table class="vostok-logs-table" id="vostok-logs-table">
                    <thead>
                        <tr>
                            <th class="w-18">Log ID</th>
                            <th>Link Code</th>
                            <th>Route</th>
                            <th>Protocol</th>
                            <th>Status</th>
                            <th>Actor</th>
                            <th>Payload Summary</th>
                            <th class="w-40">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody id="vostok-logs-tbody">
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="8" class="text-center p-8 text-slate-500">
                                    No integration events recorded yet for this system.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log):
                                $isSuccess = ($log['status_code'] >= 200 && $log['status_code'] < 300);
                            ?>
                                <tr>
                                    <td class="font-mono font-semibold text-cyan-400">
                                        #<?= htmlspecialchars($log['log_id']) ?>
                                    </td>
                                    <td class="font-mono text-xs text-slate-200">
                                        <?= htmlspecialchars($log['link_code']) ?>
                                    </td>
                                    <td class="font-mono text-xs">
                                        <?= htmlspecialchars($log['source_system_id']) ?> &rarr; <?= htmlspecialchars($log['target_system_id']) ?>
                                    </td>
                                    <td class="text-xs text-slate-400">
                                        <?= htmlspecialchars($log['api_protocol']) ?>
                                    </td>
                                    <td>
                                        <span class="vostok-status-pill <?= $isSuccess ? 'success' : 'error' ?>">
                                            <?= htmlspecialchars($log['status_code']) ?> <?= $isSuccess ? 'OK' : 'ERR' ?>
                                        </span>
                                    </td>
                                    <td class="font-mono text-xs text-slate-300">
                                        <?= htmlspecialchars($log['actor_id']) ?>
                                    </td>
                                    <td class="text-xs text-slate-400 max-w-xs truncate" title="<?= htmlspecialchars($log['payload_summary']) ?>">
                                        <?= htmlspecialchars($log['payload_summary']) ?>
                                    </td>
                                    <td class="font-mono text-xs text-slate-500">
                                        <?= htmlspecialchars($log['executed_at']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Client-side Interactive Dispatcher -->
    <script src="../assets/js/integration-panel.js" defer></script>

<?php
}
