<?php
$currUser = $_SESSION['vostok_user'] ?? ['full_name' => 'Super Administrator', 'clearance_level' => 'L4', 'role_name' => 'System Architect'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('DEV');
$pdo = getDbConnection();
require_once __DIR__ . '/api/db_helper.php';
ensureDeveloperTables($pdo);

// Fetch presets and recent execution logs from database
$presets = $pdo->query("SELECT * FROM `developer_sandbox_presets` ORDER BY `id` ASC")->fetchAll(PDO::FETCH_ASSOC);
$recentLogs = $pdo->query("SELECT * FROM `developer_sandbox_logs` ORDER BY `id` DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Interactive API Sandbox &amp; Testing Console - VOSTOKPRIBOR Developer Portal</title>
    <link rel="stylesheet" href="css/dev-tokens.css" />
    <link rel="stylesheet" href="css/dev-common.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="css/dev-sandbox.css" />
</head>

<body>
    <!-- TOP NAVIGATION BAR -->
    <header class="vk-top-navbar">
        <div class="dev-flex-center-gap-24" >
            <button class="mobile-nav-toggle" id="dev-sidebar-toggle" onclick="document.body.classList.toggle('sidebar-open')" title="Toggle Menu"><span class="material-symbols-outlined">menu</span></button>
            <a class="vk-brand-section" href="Dashboard.php">
                <img alt="VOSTOKPRIBOR Official Mark" class="brand-logo-img dev-logo-img" src="assets/logo.svg" />
                <div class="dev-flex-col" >
                    <div class="dev-flex-center-gap-8" >
                        <span class="dev-font-family-var-font-980b" >VOSTOKPRIBOR</span>
                        <span class="vk-system-badge">SYS-10 // DEV-PORTAL</span>
                    </div>
                    <span class="dev-font-family-var-font-54ae" >ALMATY CENTRAL • EST. 1968 • API GATEWAY v4.12.0</span>
                </div>
            </a>
            <div class="dev-display-flex-align-items-bc9f" >
                <span class="material-symbols-outlined text-[14px] dev-color-accent">code_blocks</span>
                <span class="dev-font-family-var-font-eb0b" >SANDBOX: <strong>LIVE SIMULATION RUNNER</strong></span>
            </div>
        </div>

        <div class="dev-flex-center-gap-16" >
<button class="icon-button notifications-btn" id="notifications-toggle-btn" title="Live Enterprise Notifications" style="background:transparent;border:none;color:#bdc6cf;cursor:pointer;padding:6px;display:flex;align-items:center;justify-content:center;position:relative;">
                <span class="material-symbols-outlined text-[20px]">notifications</span>
            </button>
            <div class="dev-search-box-wrap" >
                <span class="material-symbols-outlined text-[16px] dev-position-absolute-left-10px-d4a8">search</span>
                <input class="search-trigger-input" type="text" placeholder="Search sandbox presets (Ctrl + K)" readonly
                     />
            </div>
            <div class="dev-display-flex-align-items-9eca" >
                <span class="material-symbols-outlined text-[14px] dev-color-secondary">schedule</span>
                <span class="station-live-clock">17:15:00 UTC+6</span>
            </div>
            <div class="dev-display-flex-align-items-20f3" >
                <div class="dev-text-right" >
                    <div class="dev-font-size-12px-font-2ab2" ><?= htmlspecialchars($currUser['full_name'] ?? 'Authorized Developer') ?></div>
                    <div class="dev-font-family-var-font-b636" ><?= htmlspecialchars($currUser['cus_id'] ?? ($currUser['emp_id'] ?? 'DEV-AUTH')) ?> • <?= htmlspecialchars($currUser['company_name'] ?? ($currUser['role_name'] ?? 'Developer')) ?></div>
                </div>
                <div class="dev-width-32px-height-32px-0eaf" >
                    <span class="material-symbols-outlined text-[18px] dev-text-white">person</span>
                </div>
            </div>

            <!-- Top Bar Sign Out -->
            <a href="../api/logout.php?system=Developer&redirect=../Developer/login.php" class="top-signout-btn" title="Sign Out of Developer" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" ><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
        </div>
    </header>

    <!-- LEFT SIDEBAR -->
    <aside class="vk-sidebar" style="background-color: #0f2438 !important; border-right: 1px solid rgba(255, 255, 255, 0.1) !important; scrollbar-width: none !important; -ms-overflow-style: none !important;">
        <div class="vk-sidebar-nav">
            <div class="vk-sidebar-header">Core Documentation</div>
            <a class="vk-nav-item" href="Dashboard.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">menu_book</span>
                    <span>API Reference</span>
                </div>
                <span class="dev-font-family-var-font-36a4" >REST</span>
            </a>
            <a class="vk-nav-item" href="guides.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">architecture</span>
                    <span>Integration Guides</span>
                </div>
                <span class="badge-classification badge-internal dev-font-size-8px-c136">DOC-010</span>
            </a>

            <div class="vk-sidebar-header dev-mt-16">Partner Enclave</div>
            <a class="vk-nav-item" href="credentials.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">key</span>
                    <span>API Credentials</span>
                </div>
                <span class="dev-font-family-var-font-36a4" >Vault</span>
            </a>
            <a class="vk-nav-item active" href="sandbox.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">code_blocks</span>
                    <span>Testing Sandbox</span>
                </div>
                <span class="vk-status-badge status-sandbox dev-padding-1px-6px-font-7734">LIVE</span>
            </a>
            <a class="vk-nav-item" href="metrics.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">monitoring</span>
                    <span>Usage &amp; Telemetry</span>
                </div>
                <span class="dev-font-family-var-font-7016" >99.98%</span>
            </a>
            <a class="vk-nav-item" href="partner-registration.php">
                <div class="dev-flex-center-gap-10">
                    <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                    <span>Partner Registration</span>
                </div>
            </a>

                        <a href="Integrations.php" class="sidebar-nav-item">
                <div class="sidebar-item-left"><span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00E5FF" stroke-width="2">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                        </svg></span><span class="dev-color-00e5ff-font-weight-fe7a" style="color:#00E5FF; font-weight:600;">System Integrations</span></div><span class="sidebar-badge" style="background:rgba(0, 229, 255, 0.15); color:#00E5FF; border:1px solid rgba(0, 229, 255, 0.3); font-size:9px; padding:2px 6px; border-radius:4px;">SYS04</span>
            </a>
            <div class="vk-sidebar-header dev-mt-16">Unified Ecosystem</div>
            <a class="vk-nav-item" href="../VOSTOKPRIBOR Corporate Web Platform/index.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px] dev-color-1b3a5c-1796">language</span>
                    <span>Corporate Platform</span>
                </div>
                <span class="vk-tag dev-font-size-9px-background-21b8">SYS-01</span>
            </a>
            <a class="vk-nav-item" href="../Employee Intranet/login.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px] dev-color-5c7290-b50c">badge</span>
                    <span>Employee Intranet</span>
                </div>
                <span class="vk-tag dev-font-size-9px-background-cfa1">SYS-04</span>
            </a>
            <a class="vk-nav-item" href="../File Center/login.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px] dev-color-5a6470-9f56">folder_zip</span>
                    <span>File Center</span>
                </div>
                <span class="vk-tag dev-font-size-9px-background-7efe">SYS-09</span>
            </a>
            <a class="vk-nav-item" href="../Admin & Governance Portal/login.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px] dev-color-alert">shield</span>
                    <span>Admin &amp; Governance</span>
                </div>
                <span class="vk-tag vk-tag-confidential dev-text-10">SYS-11</span>
            </a>
        </div>

        <div class="dev-padding-16px-border-top-156b" >
            <div class="dev-font-family-var-font-6447" >Mock Ingestion Engine</div>
            <div class="dev-text-muted" >Target: Almaty Mock Cluster</div>
            <div class="dev-color-var-vk-secondary-9fd9" >Latency: &lt;35ms Live Dispatched</div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="vk-app-body">
        <div class="dev-mb-20" >
            <div class="dev-display-flex-align-items-81c3" >
                <span class="dev-mono-muted-11" >ROOT / SYSTEM 10 / TESTING CONSOLE</span>
                <span class="badge-classification badge-internal">Sandbox Gateway Node</span>
            </div>
            <h1 class="dev-font-size-26px-font-2295" >
                Interactive API Request Runner &amp; Simulator
            </h1>
            <p class="dev-color-var-vk-neutral-56fa" >
                Directly dispatch test requests to VOSTOKPRIBOR sandbox nodes connected to the real system database without impacting production SCADA telemetry.
            </p>
        </div>

        <!-- Quick Endpoint Presets (100% Dynamic from Database) -->
        <div class="dev-display-flex-align-items-7bb4" style="flex-wrap: wrap; gap: 8px;">
            <span class="dev-font-family-var-font-0e94" >Quick Presets:</span>
            <?php foreach ($presets as $preset): 
                $badgeStyle = strtoupper($preset['method']) === 'POST' ? 'endpoint-badge-post' : (strtoupper($preset['method']) === 'PUT' ? 'endpoint-badge-put' : (strtoupper($preset['method']) === 'DELETE' ? 'endpoint-badge-delete' : 'endpoint-badge-get'));
            ?>
            <div style="display: inline-flex; align-items: center; border: 1px solid var(--vk-neutral-200); border-radius: 6px; overflow: hidden; background: #fff;">
                <button class="vk-btn vk-btn-outline btn-preset" style="border: none; border-radius: 0; padding: 5px 10px;"
                    data-method="<?= htmlspecialchars($preset['method']) ?>" 
                    data-url="<?= htmlspecialchars($preset['url']) ?>" 
                    data-body="<?= htmlspecialchars($preset['sample_body'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <span class="<?= $badgeStyle ?> dev-padding-1px-4px-font-650b"><?= htmlspecialchars($preset['method']) ?></span> 
                    <?= htmlspecialchars($preset['title']) ?>
                </button>
                <button class="btn-delete-preset" data-id="<?= $preset['id'] ?>" title="Delete preset" style="background: transparent; border: none; border-left: 1px solid #e2e8f0; padding: 6px; cursor: pointer; color: #94a3b8;">
                    <span class="material-symbols-outlined text-[13px]">close</span>
                </button>
            </div>
            <?php endforeach; ?>

            <button class="vk-btn vk-btn-outline" id="btnOpenSavePreset" style="margin-left: auto; border-style: dashed;">
                <span class="material-symbols-outlined text-[14px]">bookmark_add</span> Save Current as Preset
            </button>
        </div>

        <!-- Sandbox Layout Grid -->
        <div class="sandbox-container">
            <!-- Left: Request Builder -->
            <div class="vk-card tag-internal">
                <div class="vk-card-header">
                    <div class="dev-font-weight-700-font-2e8f" >HTTP Request Builder</div>
                    <span class="badge-classification badge-internal">Database-Connected Live Sandbox</span>
                </div>
                <div class="vk-card-body dev-display-flex-flex-direction-269e">
                    <!-- URL Bar -->
                    <div class="request-bar">
                        <select class="method-select" id="sandboxMethod">
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                        <input class="url-input" id="sandboxUrl" type="text" value="/v1/sensors/optical/telemetry?device_id=PROD-1001-KZ" />
                        <button class="vk-btn vk-btn-accent send-btn" id="btnSendSandbox">
                            <span class="material-symbols-outlined text-[16px]">send</span> Send
                        </button>
                    </div>

                    <!-- Authorization Token Preview -->
                    <div>
                        <label class="dev-font-size-11px-font-a64a" >Headers (Auto-injected by Gateway)</label>
                        <div class="dev-background-f8fafc-border-1px-0d68" >
                            Authorization: Bearer vk_test_3f7b99c1e04a88bc92d110f<br />
                            Accept: application/json<br />
                            X-Client-Enclave: CUS-1002-BALTNORD
                        </div>
                    </div>

                    <!-- JSON Body Builder -->
                    <div>
                        <div class="dev-display-flex-justify-content-2127" >
                            <label class="dev-font-size-11px-font-a64a" >Request Body (JSON)</label>
                            <button class="vk-btn-outline dev-padding-2px-6px-font-ed80" onclick="document.getElementById('sandboxBody').value='{}'">Clear</button>
                        </div>
                        <textarea class="console-editor-box" id="sandboxBody" placeholder="Enter JSON payload for POST/PUT requests..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Right: Response Inspector -->
            <div class="response-pane">
                <div class="response-header">
                    <div class="dev-flex-center-gap-10" >
                        <span id="responseStatusCode" class="vk-status-badge status-active">200 OK</span>
                        <span class="dev-text-slate-400" >Time: <strong class="dev-color-38bdf8-bbf8" id="responseTime" >24ms</strong></span>
                        <span class="dev-text-slate-400" >Size: <strong class="dev-color-e2e8f0-2bd6" id="responseSize" >842 B</strong></span>
                    </div>
                    <button class="vk-btn-outline dev-padding-2px-8px-font-1311" onclick="window.copyText(document.getElementById('responseBodyDisplay').textContent, 'Response JSON copied')">
                        <span class="material-symbols-outlined text-[13px]">content_copy</span> Copy JSON
                    </button>
                </div>
                <div class="response-body-scroll">
                    <pre id="responseBodyDisplay">{
  "device_id": "PROD-1001-KZ",
  "sensor_series": "Industrial Optical Sensor Package",
  "calibration_epoch": 1789128000,
  "station": "ALMATY-CENTRAL",
  "telemetry": {
    "spectral_resolution_nm": 0.04,
    "focal_plane_temp_c": 18.2,
    "dispersion_coefficient": 1.0024,
    "optical_throughput_percent": 99.82,
    "snr_db": 68.4
  },
  "status": "NOMINAL_OPERATIONAL",
  "jurisdiction_merkle_root": "0x4a8c911f...c892"
}</pre>
                </div>
            </div>
        </div>

        <!-- RECENT EXECUTION HISTORY (From database table developer_sandbox_logs) -->
        <div class="vk-card" style="margin-top: 24px;">
            <div class="vk-card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-outlined text-[18px] dev-color-accent">history</span>
                    <div class="vk-card-title">Recent Sandbox Dispatches (Database Ledger)</div>
                </div>
                <button class="vk-btn vk-btn-outline" id="btnClearSandboxLogs" style="font-size: 11px; padding: 3px 8px;">
                    <span class="material-symbols-outlined text-[13px]">delete_sweep</span> Clear History
                </button>
            </div>
            <div class="vk-card-body" style="padding: 0;">
                <table class="vk-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Method</th>
                            <th>Request URL</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 90px;">Latency</th>
                            <th style="width: 90px;">Payload</th>
                            <th style="width: 140px;">Timestamp</th>
                            <th style="width: 90px; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="sandboxLogsBody">
                        <?php if (empty($recentLogs)): ?>
                        <tr><td colspan="7" style="text-align: center; color: var(--vk-neutral-500); padding: 20px;">No requests dispatched yet.</td></tr>
                        <?php else: ?>
                        <?php foreach ($recentLogs as $log): 
                            $statusStyle = ($log['status_code'] >= 200 && $log['status_code'] < 300) ? 'status-active' : 'status-revoked';
                        ?>
                        <tr>
                            <td><span class="endpoint-badge-<?= strtolower($log['method']) ?>"><?= htmlspecialchars($log['method']) ?></span></td>
                            <td><code style="font-family: var(--font-mono); font-size: 12px;"><?= htmlspecialchars($log['url']) ?></code></td>
                            <td><span class="vk-status-badge <?= $statusStyle ?>"><?= $log['status_code'] ?></span></td>
                            <td style="font-family: var(--font-mono); font-size: 12px;"><?= $log['response_time_ms'] ?> ms</td>
                            <td style="font-family: var(--font-mono); font-size: 11px; color: var(--vk-neutral-500);"><?= htmlspecialchars($log['response_size']) ?></td>
                            <td style="font-size: 11px; color: var(--vk-neutral-500);"><?= htmlspecialchars($log['executed_at']) ?></td>
                            <td style="text-align: right;">
                                <button class="btn-crud-action btn-rerun-log" 
                                    data-method="<?= htmlspecialchars($log['method']) ?>" 
                                    data-url="<?= htmlspecialchars($log['url']) ?>" 
                                    data-body="<?= htmlspecialchars($log['request_body'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    title="Load and replay this request">
                                    <span class="material-symbols-outlined text-[13px]">replay</span>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- SAVE PRESET MODAL DIALOG -->
    <div class="vk-modal-overlay" id="savePresetModal">
        <div class="vk-modal-dialog" style="max-width: 500px;">
            <div class="vk-modal-header">
                <div class="dev-flex-center-gap-8">
                    <span class="material-symbols-outlined text-[20px] dev-color-accent">bookmark_add</span>
                    <h3 class="dev-font-size-15px-font-29ad">Save Endpoint Preset</h3>
                </div>
                <button class="dev-background-transparent-border-none-aba8" id="btnClosePresetModal" style="color: #94a3b8; cursor: pointer;">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
            <div class="vk-modal-body">
                <div class="crud-form-group">
                    <label class="crud-form-label" for="presetTitleInput">Preset Title *</label>
                    <input class="crud-form-input" id="presetTitleInput" type="text" placeholder="e.g. SCADA Emergency Trip Simulation" required />
                </div>
                <div class="crud-form-group">
                    <label class="crud-form-label" for="presetDescInput">Short Description</label>
                    <input class="crud-form-input" id="presetDescInput" type="text" placeholder="Optional notes about this test payload" />
                </div>
            </div>
            <div class="vk-modal-footer">
                <button class="vk-btn vk-btn-outline" id="btnCancelPresetModal">Cancel</button>
                <button class="vk-btn vk-btn-accent" id="btnConfirmSavePreset">Save Preset to Database</button>
            </div>
        </div>
    </div>

        <footer class="vk-footer">
        <div class="vk-footer-bottom dev-border-top-none-padding-efbd">
            <div>© 2026 VOSTOKPRIBOR Global Logistics &amp; Supply JSC. System 10: Developer &amp; API Portal.</div>
            <div>Testing Sandbox Node: almaty-sbx-01.local • Isolated RAM Partition</div>
        </div>
    </footer>

    <script src="js/dev-common.js"></script>
    <script src="js/dev-sandbox.js"></script>
  <script src="../assets/js/notifications-hub.js" defer></script>
</body>

</html>