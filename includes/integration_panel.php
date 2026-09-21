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

function renderSystemIntegrationView($systemCode, $pageMode = 'standalone') {
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
    <div class="vostok-integration-container" id="vostok-integration-root">
        <style>
            .vostok-integration-container {
                font-family: 'IBM Plex Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                color: #E2E8F0;
            }
            .vostok-int-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 1rem;
                padding: 1.25rem 1.5rem;
                background: linear-gradient(135deg, #0b1929 0%, #0f2b48 100%);
                border: 1px solid rgba(0, 229, 255, 0.2);
                border-radius: 8px;
                margin-bottom: 1.5rem;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            }
            .vostok-int-title {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }
            .vostok-int-badge {
                background: rgba(0, 229, 255, 0.15);
                color: #00E5FF;
                border: 1px solid rgba(0, 229, 255, 0.3);
                font-family: 'JetBrains Mono', monospace;
                font-size: 0.75rem;
                font-weight: 700;
                padding: 0.25rem 0.6rem;
                border-radius: 4px;
                letter-spacing: 0.05em;
            }
            .vostok-int-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
                gap: 1.25rem;
                margin-bottom: 2rem;
            }
            .vostok-card {
                background: #0d1e30;
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 8px;
                padding: 1.25rem;
                transition: transform 0.2s, border-color 0.2s;
                position: relative;
                overflow: hidden;
            }
            .vostok-card:hover {
                transform: translateY(-2px);
                border-color: rgba(0, 229, 255, 0.4);
            }
            .vostok-card.outbound {
                border-left: 4px solid #00E5FF;
            }
            .vostok-card.inbound {
                border-left: 4px solid #38BDF8;
            }
            .vostok-card.broadcast {
                border-left: 4px solid #F59E0B;
            }
            .vostok-meta-row {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
                margin-bottom: 0.75rem;
                padding-bottom: 0.75rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }
            .vostok-meta-label {
                font-size: 0.7rem;
                font-family: 'JetBrains Mono', monospace;
                text-transform: uppercase;
                color: #94A3B8;
                letter-spacing: 0.06em;
            }
            .vostok-meta-value {
                font-size: 0.85rem;
                color: #F8FAFC;
                font-weight: 500;
                line-height: 1.4;
            }
            .vostok-sync-btn {
                background: linear-gradient(135deg, #0284C7 0%, #0369A1 100%);
                color: #FFFFFF;
                border: 1px solid rgba(255, 255, 255, 0.2);
                padding: 0.5rem 0.85rem;
                border-radius: 4px;
                font-size: 0.75rem;
                font-weight: 600;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                transition: all 0.2s;
                margin-top: 0.5rem;
                width: 100%;
                justify-content: center;
            }
            .vostok-sync-btn:hover {
                background: linear-gradient(135deg, #0369A1 0%, #0284C7 100%);
                box-shadow: 0 0 12px rgba(2, 132, 199, 0.5);
            }
            .vostok-sync-btn:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }
            .vostok-logs-table-wrap {
                background: #0d1e30;
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 8px;
                overflow: hidden;
            }
            .vostok-logs-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.8rem;
                text-align: left;
            }
            .vostok-logs-table th {
                background: rgba(0, 0, 0, 0.3);
                color: #94A3B8;
                font-family: 'JetBrains Mono', monospace;
                font-size: 0.7rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                padding: 0.75rem 1rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            }
            .vostok-logs-table td {
                padding: 0.75rem 1rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.04);
                color: #CBD5E1;
            }
            .vostok-logs-table tr:hover td {
                background: rgba(255, 255, 255, 0.02);
            }
            .vostok-status-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.3rem;
                padding: 0.2rem 0.5rem;
                border-radius: 4px;
                font-size: 0.7rem;
                font-family: 'JetBrains Mono', monospace;
                font-weight: 600;
            }
            .vostok-status-pill.success {
                background: rgba(16, 185, 129, 0.15);
                color: #34D399;
                border: 1px solid rgba(16, 185, 129, 0.3);
            }
            .vostok-status-pill.error {
                background: rgba(239, 68, 68, 0.15);
                color: #F87171;
                border: 1px solid rgba(239, 68, 68, 0.3);
            }
        </style>

        <!-- Top Header -->
        <div class="vostok-int-header">
            <div>
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                    <span class="vostok-int-badge"><?= htmlspecialchars($systemCode) ?></span>
                    <span style="color: #64748B; font-family: 'JetBrains Mono', monospace; font-size: 0.75rem;">CONNECTED SYSTEM</span>
                </div>
                <h2 style="margin: 0; font-size: 1.35rem; color: #FFFFFF; font-weight: 700;">
                    <?= htmlspecialchars($sysName) ?> &mdash; Inter-System Integrations
                </h2>
                <div style="font-size: 0.8rem; color: #94A3B8; margin-top: 0.25rem;">
                    Universal Data Bus &bull; Topology: Strict 12-Channel Interconnect Architecture
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span class="vostok-status-pill success">
                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #34D399;"></span>
                    <?= count($integrations) ?> Channel<?= count($integrations) !== 1 ? 's' : '' ?> Active
                </span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #94A3B8; background: rgba(0,0,0,0.3); padding: 0.35rem 0.6rem; border-radius: 4px; border: 1px solid rgba(255,255,255,0.06);">
                    Clearance: <strong style="color: <?= $isSuperAdmin ? '#F59E0B' : '#38BDF8' ?>;"><?= htmlspecialchars($userClearance) ?><?= $isSuperAdmin ? ' (SuperAdmin)' : '' ?></strong>
                </span>
            </div>
        </div>

        <!-- 6 Mandatory Items Grid: API/Protocol, Authentication, Data Exchanged, Direction, Permissions, Logs -->
        <h3 style="font-size: 1rem; color: #FFFFFF; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>Configured Integration Links</span>
            <span style="font-size: 0.75rem; font-family: 'JetBrains Mono', monospace; color: #64748B;">(API, Protocol, Auth, Data, Direction, Clearance)</span>
        </h3>

        <div class="vostok-int-grid">
            <?php if (empty($integrations)): ?>
                <div style="grid-column: 1 / -1; padding: 2rem; text-align: center; background: #0d1e30; border-radius: 8px;">
                    <p style="color: #94A3B8; margin: 0;">No active integration channels assigned to this subsystem.</p>
                </div>
            <?php else: ?>
                <?php foreach ($integrations as $int): 
                    $isOutbound = ($int['source_system_id'] === $sysId);
                    $isBroadcast = ($int['target_system_id'] === 'ALL');
                    $cardType = $isBroadcast ? 'broadcast' : ($isOutbound ? 'outbound' : 'inbound');
                ?>
                    <div class="vostok-card <?= $cardType ?>" id="card-<?= htmlspecialchars($int['link_code']) ?>">
                        <!-- Header with Direction & Link Code -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                            <div>
                                <span style="font-family: 'JetBrains Mono', monospace; font-weight: 700; font-size: 0.85rem; color: #00E5FF;">
                                    <?= htmlspecialchars($int['link_code']) ?>
                                </span>
                                <div style="font-size: 0.8rem; font-weight: 600; color: #F1F5F9; margin-top: 2px;">
                                    <?= htmlspecialchars($int['source_system_id']) ?> &rarr; <?= htmlspecialchars($int['target_system_id']) ?>
                                </div>
                            </div>
                            <span style="font-size: 0.65rem; font-family: 'JetBrains Mono', monospace; padding: 0.2rem 0.5rem; border-radius: 4px; background: rgba(255,255,255,0.06); color: #94A3B8;">
                                <?= htmlspecialchars($int['direction']) ?>
                            </span>
                        </div>

                        <!-- 1. API / Protocol -->
                        <div class="vostok-meta-row">
                            <span class="vostok-meta-label">1. API / Protocol</span>
                            <span class="vostok-meta-value" style="color: #38BDF8;">
                                <?= htmlspecialchars($int['api_protocol']) ?>
                            </span>
                        </div>

                        <!-- 2. Authentication -->
                        <div class="vostok-meta-row">
                            <span class="vostok-meta-label">2. Authentication</span>
                            <span class="vostok-meta-value" style="color: #A7F3D0;">
                                <?= htmlspecialchars($int['authentication_method']) ?>
                            </span>
                        </div>

                        <!-- 3. Data Exchanged -->
                        <div class="vostok-meta-row">
                            <span class="vostok-meta-label">3. Data Exchanged</span>
                            <span class="vostok-meta-value" style="color: #E2E8F0; font-size: 0.8rem;">
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
                            <span class="vostok-meta-value" style="display: flex; align-items: center; justify-content: space-between;">
                                <span>Required: <strong><?= htmlspecialchars($int['required_clearance']) ?></strong></span>
                                <?php if ($isSuperAdmin || $userClearance >= $int['required_clearance']): ?>
                                    <span style="color: #34D399; font-size: 0.7rem; font-family: 'JetBrains Mono', monospace;">&#10003; AUTHORIZED</span>
                                <?php else: ?>
                                    <span style="color: #F87171; font-size: 0.7rem; font-family: 'JetBrains Mono', monospace;">&#10007; RESTRICTED</span>
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
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <h3 style="font-size: 1rem; color: #FFFFFF; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>Integration Audit Logs (system_integration_logs)</span>
            </h3>
            <button type="button" 
                    onclick="window.refreshVostokLogs('<?= htmlspecialchars($systemCode) ?>')" 
                    style="background: transparent; border: 1px solid rgba(255,255,255,0.1); color: #94A3B8; font-size: 0.75rem; padding: 0.25rem 0.6rem; border-radius: 4px; cursor: pointer;">
                &#8635; Refresh Logs
            </button>
        </div>

        <div class="vostok-logs-table-wrap">
            <div style="overflow-x: auto;">
                <table class="vostok-logs-table" id="vostok-logs-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Log ID</th>
                            <th>Link Code</th>
                            <th>Route</th>
                            <th>Protocol</th>
                            <th>Status</th>
                            <th>Actor</th>
                            <th>Payload Summary</th>
                            <th style="width: 160px;">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody id="vostok-logs-tbody">
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 2rem; color: #64748B;">
                                    No integration events recorded yet for this system.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): 
                                $isSuccess = ($log['status_code'] >= 200 && $log['status_code'] < 300);
                            ?>
                                <tr>
                                    <td style="font-family: 'JetBrains Mono', monospace; font-weight: 600; color: #00E5FF;">
                                        #<?= htmlspecialchars($log['log_id']) ?>
                                    </td>
                                    <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #E2E8F0;">
                                        <?= htmlspecialchars($log['link_code']) ?>
                                    </td>
                                    <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem;">
                                        <?= htmlspecialchars($log['source_system_id']) ?> &rarr; <?= htmlspecialchars($log['target_system_id']) ?>
                                    </td>
                                    <td style="font-size: 0.75rem; color: #94A3B8;">
                                        <?= htmlspecialchars($log['api_protocol']) ?>
                                    </td>
                                    <td>
                                        <span class="vostok-status-pill <?= $isSuccess ? 'success' : 'error' ?>">
                                            <?= htmlspecialchars($log['status_code']) ?> <?= $isSuccess ? 'OK' : 'ERR' ?>
                                        </span>
                                    </td>
                                    <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #CBD5E1;">
                                        <?= htmlspecialchars($log['actor_id']) ?>
                                    </td>
                                    <td style="font-size: 0.75rem; color: #94A3B8; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($log['payload_summary']) ?>">
                                        <?= htmlspecialchars($log['payload_summary']) ?>
                                    </td>
                                    <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748B;">
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
    <script>
    (function() {
        window.triggerVostokIntegration = function(linkCode, btnElement) {
            if (!confirm('Execute live integration exchange for link ' + linkCode + '?')) {
                return;
            }

            if (btnElement) {
                btnElement.disabled = true;
                btnElement.innerHTML = '<span>&#8987; Dispatching...</span>';
            }

            fetch('../api/integration_router.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    link_code: linkCode,
                    action: 'UI_INTERACTIVE_DISPATCH',
                    payload: { client_time: new Date().toISOString(), trigger: 'WEB_CONSOLE' }
                })
            })
            .then(res => res.json())
            .then(data => {
                if (btnElement) {
                    btnElement.disabled = false;
                    btnElement.innerHTML = '<span>&#9889; Dispatch Test Exchange</span>';
                }

                if (data.success) {
                    alert('SUCCESS: Integration ' + linkCode + ' executed!\nLog ID #' + data.log_id + '\nStatus: ' + data.status_code + ' OK\nProtocol: ' + data.protocol);
                    window.refreshVostokLogs('<?= htmlspecialchars($systemCode) ?>');
                } else {
                    alert('ERROR: ' + (data.error || 'Failed to dispatch integration.'));
                }
            })
            .catch(err => {
                if (btnElement) {
                    btnElement.disabled = false;
                    btnElement.innerHTML = '<span>&#9889; Dispatch Test Exchange</span>';
                }
                alert('Network Error: ' + err.message);
            });
        };

        window.refreshVostokLogs = function(sysCode) {
            fetch('../api/integration_router.php?system=' + encodeURIComponent(sysCode) + '&include_logs=1')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.logs) {
                    const tbody = document.getElementById('vostok-logs-tbody');
                    if (!tbody) return;

                    if (data.logs.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 2rem; color: #64748B;">No integration events recorded yet for this system.</td></tr>';
                        return;
                    }

                    tbody.innerHTML = data.logs.map(log => {
                        const isSuccess = log.status_code >= 200 && log.status_code < 300;
                        const statusClass = isSuccess ? 'success' : 'error';
                        const statusText = isSuccess ? log.status_code + ' OK' : log.status_code + ' ERR';
                        return `
                            <tr>
                                <td style="font-family: 'JetBrains Mono', monospace; font-weight: 600; color: #00E5FF;">#${log.log_id}</td>
                                <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #E2E8F0;">${log.link_code}</td>
                                <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem;">${log.source_system_id} &rarr; ${log.target_system_id}</td>
                                <td style="font-size: 0.75rem; color: #94A3B8;">${log.api_protocol}</td>
                                <td>
                                    <span class="vostok-status-pill ${statusClass}">${statusText}</span>
                                </td>
                                <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #CBD5E1;">${log.actor_id}</td>
                                <td style="font-size: 0.75rem; color: #94A3B8; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${log.payload_summary || ''}">
                                    ${log.payload_summary || ''}
                                </td>
                                <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748B;">${log.executed_at}</td>
                            </tr>
                        `;
                    }).join('');
                }
            })
            .catch(err => console.error('Failed to refresh integration logs:', err));
        };
    })();
    </script>
    <?php
}
