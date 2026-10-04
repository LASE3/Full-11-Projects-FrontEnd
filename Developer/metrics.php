<?php
$currUser = $_SESSION['vostok_user'] ?? ['full_name' => 'Super Administrator', 'clearance_level' => 'L4', 'role_name' => 'System Architect'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('DEV');
$pdo = getDbConnection();
require_once __DIR__ . '/api/db_helper.php';
ensureDeveloperTables($pdo);

// 1. Calculate Webhooks
$webhooks = $pdo->query("SELECT * FROM `developer_webhooks` ORDER BY `id` DESC")->fetchAll(PDO::FETCH_ASSOC);
$totalWh = count($webhooks);
$deliveredWh = count(array_filter($webhooks, fn($w) => $w['status'] === 'Delivered'));
$webhookSla = ($totalWh > 0) ? round(($deliveredWh / $totalWh) * 100, 2) : 99.98;

// 2. Invocations & Latency
$logCount = (int)$pdo->query("SELECT COUNT(*) FROM `developer_sandbox_logs`")->fetchColumn();
$accessLogCount = (int)$pdo->query("SELECT COUNT(*) FROM `api_access_logs`")->fetchColumn();
$totalInvocations = 1428900 + $logCount + $accessLogCount;

$avgLatency = (float)$pdo->query("SELECT COALESCE(AVG(response_time_ms), 28.4) FROM `developer_sandbox_logs`")->fetchColumn();
if ($avgLatency <= 0) $avgLatency = 28.4;

// 3. Key Quota
$keyQuotas = $pdo->query("SELECT * FROM `developer_api_keys` ORDER BY `id` ASC LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Telemetry &amp; Usage Metrics - VOSTOKPRIBOR Developer Portal</title>
    <link rel="stylesheet" href="css/dev-tokens.css" />
    <link rel="stylesheet" href="css/dev-common.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="css/dev-metrics.css" />
</head>

<body>
    <!-- TOP NAVIGATION BAR (System 10 Cyan 4px stripe) -->
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
                <span class="material-symbols-outlined text-[14px] dev-color-accent">monitoring</span>
                <span class="dev-font-family-var-font-eb0b" >TELEMETRY: <strong>INGESTION &amp; DISPATCH LEDGER</strong></span>
            </div>
        </div>

        <div class="dev-flex-center-gap-16" >
<button class="icon-button notifications-btn" id="notifications-toggle-btn" title="Live Enterprise Notifications" style="background:transparent;border:none;color:#bdc6cf;cursor:pointer;padding:6px;display:flex;align-items:center;justify-content:center;position:relative;">
                <span class="material-symbols-outlined text-[20px]">notifications</span>
            </button>
            <div class="dev-search-box-wrap" >
                <span class="material-symbols-outlined text-[16px] dev-position-absolute-left-10px-d4a8">search</span>
                <input class="search-trigger-input" type="text" placeholder="Search metrics, logs (Ctrl + K)" readonly
                     />
            </div>
            <div class="dev-display-flex-align-items-9eca" >
                <span class="material-symbols-outlined text-[14px] dev-color-secondary">schedule</span>
                <span class="station-live-clock">17:25:00 UTC+6</span>
            </div>
            <div class="dev-display-flex-align-items-20f3" >
                <div class="dev-text-right" >
                    <div class="dev-font-size-12px-font-2ab2" ><?= htmlspecialchars($currUser['full_name'] ?? 'Authorized Developer') ?></div>
                    <div class="dev-font-family-var-font-b636" ><?= htmlspecialchars($currUser['emp_id'] ?? ($currUser['cus_id'] ?? 'DEV-AUTH')) ?> • <?= htmlspecialchars($currUser['role_name'] ?? ($currUser['company_name'] ?? 'Developer')) ?></div>
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
                <span class="vk-tag dev-text-10">v4.1</span>
            </a>
            <a class="vk-nav-item" href="guides.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">integration_instructions</span>
                    <span>Integration Guides</span>
                </div>
                <span class="vk-tag vk-tag-internal dev-text-10">DOC-2026</span>
            </a>

            <div class="vk-sidebar-header dev-margin-top-20px-194b">Developer Tools</div>
            <a class="vk-nav-item" href="credentials.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">key</span>
                    <span>API Credentials</span>
                </div>
            </a>
            <a class="vk-nav-item" href="sandbox.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">terminal</span>
                    <span>Interactive Sandbox</span>
                </div>
            </a>
            <a class="vk-nav-item active" href="metrics.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">monitoring</span>
                    <span>Usage &amp; Telemetry</span>
                </div>
            </a>
            <a class="vk-nav-item" href="partner-registration.php">
                <div class="dev-flex-center-gap-10" >
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

        <div class="dev-padding-16px-border-top-d16d" >
            <div class="dev-display-flex-align-items-81c3" >
                <span class="vk-status-indicator online"></span>
                <span class="dev-font-family-var-font-1ab9" >KONG CLUSTER ONLINE</span>
            </div>
            <div class="dev-mono-muted-11" >Node: gw-almaty-01 (10.240.0.12)</div>
            <div class="dev-font-family-var-font-940f" >P99 Latency: <?= round($avgLatency * 1.5, 1) ?>ms</div>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="vk-main-layout">
        <!-- HEADER BLOCK -->
        <div class="dev-display-flex-justify-content-f610" >
            <div>
                <div class="dev-display-flex-align-items-9bb7" >
                    <span class="vk-tag vk-tag-internal">CLASSIFICATION: INTERNAL // #3E7CB1</span>
                    <span class="dev-font-family-var-font-133f" >REF: TELEMETRY-SYS10-KONG</span>
                </div>
                <h1 class="dev-font-size-26px-font-5041" >
                    <span class="material-symbols-outlined dev-font-size-28px-color-a4d3">query_stats</span>
                    API Gateway Telemetry &amp; Quota Consumption
                </h1>
                <p class="dev-color-var-vk-neutral-5a07" >
                    Real-time ingestion performance, rate limit consumption across registered client applications, and outbound webhook delivery telemetry.
                </p>
            </div>
            <div class="dev-display-flex-gap-8px-b131" >
                <span class="dev-font-family-var-font-fbe9" >Timeframe:</span>
                <button class="vk-btn vk-btn-sm vk-btn-primary timeframe-btn" data-range="24h">24 Hours</button>
                <button class="vk-btn vk-btn-sm vk-btn-outline timeframe-btn" data-range="7d">7 Days</button>
                <button class="vk-btn vk-btn-sm vk-btn-outline timeframe-btn" data-range="30d">30 Days</button>
                <button class="vk-btn vk-btn-sm vk-btn-outline timeframe-btn" data-range="90d">90 Days</button>
            </div>
        </div>

        <!-- 4 KPI HUD CARDS (Dynamic from Database) -->
        <div class="metrics-kpi-grid">
            <div class="kpi-metric-card">
                <div class="kpi-title">
                    <span>Total Invocations (24h)</span>
                    <span class="material-symbols-outlined text-[16px] dev-color-accent">swap_calls</span>
                </div>
                <div class="kpi-value" id="kpiTotalInvocations"><?= number_format($totalInvocations) ?></div>
                <div class="kpi-subtext dev-color-var-vk-secondary-bd5b">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +12.4% vs previous 24h
                </div>
            </div>

            <div class="kpi-metric-card">
                <div class="kpi-title">
                    <span>Average Ingestion Latency</span>
                    <span class="material-symbols-outlined text-[16px] dev-color-secondary">timer</span>
                </div>
                <div class="kpi-value"><span id="kpiAvgLatency"><?= round($avgLatency, 1) ?></span> <span class="dev-font-size-14px-font-8cb8" >ms</span></div>
                <div class="kpi-subtext">P95: <?= round($avgLatency * 1.9, 1) ?>ms • P99: <?= round($avgLatency * 2.9, 1) ?>ms</div>
            </div>

            <div class="kpi-metric-card">
                <div class="kpi-title">
                    <span>Gateway HTTP Error Rate</span>
                    <span class="material-symbols-outlined text-[16px] dev-color-alert">warning</span>
                </div>
                <div class="kpi-value dev-color-2e6e4e-f283">0.02%</div>
                <div class="kpi-subtext">Nominal Gateway Status • 200 OK</div>
            </div>

            <div class="kpi-metric-card">
                <div class="kpi-title">
                    <span>Webhook Dispatch SLA</span>
                    <span class="material-symbols-outlined text-[16px] dev-color-secondary">outgoing_mail</span>
                </div>
                <div class="kpi-value dev-color-var-vk-primary-40d3" id="kpiWebhookSla"><?= $webhookSla ?>%</div>
                <div class="kpi-subtext" id="kpiWebhookSubtext"><?= $deliveredWh ?> delivered • <?= $totalWh - $deliveredWh ?> retry pending</div>
            </div>
        </div>

        <!-- TWO COLUMN LAYOUT: INGESTION TRAFFIC HOURLY SPIKES + QUOTA CONSUMPTION -->
        <div class="dev-display-grid-grid-template-27b0" >
            <!-- INGESTION CHART -->
            <div class="vk-card">
                <div class="vk-card-header">
                    <div>
                        <div class="vk-card-title">Hourly Ingestion Traffic Distribution</div>
                        <div class="vk-card-subtitle">Kong Gateway cluster throughput (requests / hour) over 24-hour cycle (UTC+6 Almaty)</div>
                    </div>
                    <span class="vk-tag dev-background-rgba-30-143-8091">
                        PEAK: 98,400 REQ/H @ 14:00
                    </span>
                </div>
                <div class="vk-card-body">
                    <div class="chart-mockup">
                        <div class="chart-bar-col dev-height-18-b8f3" title="00:00 - 12,400 req"></div>
                        <div class="chart-bar-col dev-height-14-b7df" title="01:00 - 9,800 req"></div>
                        <div class="chart-bar-col dev-height-11-361b" title="02:00 - 7,600 req"></div>
                        <div class="chart-bar-col dev-height-9-f127" title="03:00 - 6,200 req"></div>
                        <div class="chart-bar-col dev-height-12-d3db" title="04:00 - 8,100 req"></div>
                        <div class="chart-bar-col dev-height-22-a30f" title="05:00 - 15,300 req"></div>
                        <div class="chart-bar-col dev-height-38-d0bc" title="06:00 - 28,400 req"></div>
                        <div class="chart-bar-col dev-height-62-cdc3" title="07:00 - 45,900 req"></div>
                        <div class="chart-bar-col dev-height-85-ef64" title="08:00 - 74,100 req (Shift Start)"></div>
                        <div class="chart-bar-col dev-height-92-6c03" title="09:00 - 88,400 req"></div>
                        <div class="chart-bar-col dev-height-88-f9c8" title="10:00 - 82,100 req"></div>
                        <div class="chart-bar-col dev-height-94-ea72" title="11:00 - 91,200 req"></div>
                        <div class="chart-bar-col dev-height-78-a8d4" title="12:00 - 70,500 req"></div>
                        <div class="chart-bar-col dev-height-89-1385" title="13:00 - 86,400 req"></div>
                        <div class="chart-bar-col dev-height-100-587c" title="14:00 - 98,400 req (Daily Peak)"></div>
                        <div class="chart-bar-col dev-height-95-94f5" title="15:00 - 93,800 req"></div>
                        <div class="chart-bar-col dev-height-87-fdef" title="16:00 - 81,300 req"></div>
                        <div class="chart-bar-col dev-height-80-3018" title="17:00 - 76,000 req"></div>
                        <div class="chart-bar-col dev-height-65-3443" title="18:00 - 58,200 req"></div>
                        <div class="chart-bar-col dev-height-50-104b" title="19:00 - 41,000 req"></div>
                        <div class="chart-bar-col dev-height-39-e53d" title="20:00 - 32,800 req"></div>
                        <div class="chart-bar-col dev-height-31-2e6a" title="21:00 - 24,100 req"></div>
                        <div class="chart-bar-col dev-height-25-6d2c" title="22:00 - 18,900 req"></div>
                        <div class="chart-bar-col dev-height-20-5a55" title="23:00 - 14,200 req"></div>
                    </div>
                    <div class="dev-display-flex-justify-content-33eb" >
                        <span>00:00 UTC+6</span>
                        <span>06:00</span>
                        <span>12:00</span>
                        <span>18:00</span>
                        <span>23:59 UTC+6</span>
                    </div>
                </div>
            </div>

            <!-- QUOTA GAUGES (Dynamic from Database) -->
            <div class="vk-card">
                <div class="vk-card-header">
                    <div>
                        <div class="vk-card-title">Key Quota Consumption</div>
                        <div class="vk-card-subtitle">Daily budget per authorized partner enclave</div>
                    </div>
                </div>
                <div class="vk-card-body dev-display-flex-flex-direction-269e" id="quotaBarsContainer">
                    <?php foreach ($keyQuotas as $kq): 
                        $rateMax = $kq['rate_limit_value'] ?: 10000;
                        $ratePct = min(100, max(5, round((($kq['usage_count'] ?? 1000) / $rateMax) * 100, 1)));
                    ?>
                    <div>
                        <div class="dev-display-flex-justify-content-c0fa" >
                            <span class="dev-text-primary-bold" ><?= htmlspecialchars($kq['key_identifier']) ?> <?= htmlspecialchars($kq['label']) ?></span>
                            <span class="dev-font-family-var-font-e036" ><?= number_format($kq['usage_count'] ?? 0) ?> / <?= number_format($rateMax) ?></span>
                        </div>
                        <div class="dev-height-8px-background-var-8d56" >
                            <div style="width: <?= $ratePct ?>%; height: 100%; background: var(--vk-sys-accent); border-radius: 4px;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="dev-margin-top-6px-padding-7a3e" >
                        <strong>Policy:</strong> Standard partner quota resets daily at 00:00:00 UTC+6. Excess calls return HTTP 429 with <code>Retry-After</code> headers.
                    </div>
                </div>
            </div>
        </div>

        <!-- OUTBOUND WEBHOOK DISPATCH LEDGER (Dynamic from Database with CRUD) -->
        <div class="vk-card dev-margin-bottom-30px-9550">
            <div class="vk-card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div class="vk-card-title">Outbound Webhook Delivery Log (System 10 Gateway)</div>
                    <div class="vk-card-subtitle">Real-time status of asynchronous telemetry and order status callbacks delivered to external partner systems</div>
                </div>
                <div class="dev-flex-center-gap-8" >
                    <button class="vk-btn vk-btn-sm vk-btn-accent" id="btnOpenCreateWebhook">
                        <span class="material-symbols-outlined text-[15px]">send</span> Dispatch New Webhook
                    </button>
                    <span class="vk-tag vk-tag-internal">IEC 62443 VERIFIED</span>
                </div>
            </div>
            <div class="vk-card-body dev-padding-0-b662">
                <table class="vk-table">
                    <thead>
                        <tr>
                            <th class="dev-width-140px-1417" >Delivery ID</th>
                            <th class="dev-width-150px-c251" >Timestamp (UTC+6)</th>
                            <th class="dev-width-210px-91ca" >Event Type</th>
                            <th>Target Endpoint</th>
                            <th class="dev-width-130px-e314" >Status</th>
                            <th class="dev-width-90px-459f" >Latency</th>
                            <th class="dev-width-110px-text-align-2833" style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="webhooksTableBody">
                        <?php if (empty($webhooks)): ?>
                        <tr><td colspan="7" style="text-align: center; color: var(--vk-neutral-500); padding: 24px;">No outbound webhook delivery records found.</td></tr>
                        <?php else: ?>
                        <?php foreach ($webhooks as $wh): 
                            $isDelivered = ($wh['status'] === 'Delivered');
                            $statusBadgeClass = $isDelivered ? 'dev-background-dcfce7-color-166534-4a19' : 'dev-background-fee2e2-color-var-4d78';
                            $rowClass = str_contains(strtolower($wh['classification']), 'confidential') ? 'vk-table-row-confidential' : 'vk-table-row-internal';
                            $whJson = htmlspecialchars(json_encode($wh), ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr class="<?= $rowClass ?>" id="webhook-row-<?= $wh['id'] ?>">
                            <td><code><?= htmlspecialchars($wh['delivery_id']) ?></code></td>
                            <td class="dev-mono-11" ><?= htmlspecialchars($wh['created_at']) ?></td>
                            <td><span class="vk-tag dev-font-size-10px-font-eb29"><?= htmlspecialchars($wh['event_type']) ?></span></td>
                            <td class="dev-mono-muted-11" ><?= htmlspecialchars($wh['target_endpoint']) ?></td>
                            <td><span class="vk-tag <?= $statusBadgeClass ?> wh-status-code-badge"><?= htmlspecialchars($wh['status_code']) ?></span></td>
                            <td class="dev-mono-11" ><span class="wh-latency-val"><?= $wh['latency_ms'] ?></span> ms</td>
                            <td class="dev-text-right" style="white-space: nowrap;">
                                <?php if (!$isDelivered): ?>
                                <button class="vk-btn vk-btn-sm vk-btn-outline btn-retry-webhook dev-padding-2px-8px-font-174a" 
                                    data-id="<?= $wh['id'] ?>"
                                    data-delivery="<?= htmlspecialchars($wh['delivery_id']) ?>">
                                    <span class="material-symbols-outlined text-[14px]">refresh</span> Retry
                                </button>
                                <?php else: ?>
                                <span class="vk-tag dev-text-10">Delivered</span>
                                <?php endif; ?>

                                <button class="btn-crud-action btn-crud-edit btn-edit-webhook" 
                                    data-webhook='<?= $whJson ?>'
                                    title="Edit webhook record">
                                    <span class="material-symbols-outlined text-[13px]">edit</span>
                                </button>

                                <button class="btn-crud-action btn-crud-delete btn-delete-webhook" 
                                    data-id="<?= $wh['id'] ?>"
                                    data-delivery="<?= htmlspecialchars($wh['delivery_id']) ?>"
                                    title="Delete webhook from database">
                                    <span class="material-symbols-outlined text-[13px]">delete</span>
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

    <!-- CREATE / EDIT WEBHOOK MODAL -->
    <div class="vk-modal-overlay" id="webhookModal">
        <div class="vk-modal-dialog" style="max-width: 550px;">
            <div class="vk-modal-header">
                <div class="dev-flex-center-gap-8">
                    <span class="material-symbols-outlined text-[20px] dev-color-accent">outgoing_mail</span>
                    <h3 id="webhookModalTitle" class="dev-font-size-15px-font-29ad">Dispatch Outbound Webhook</h3>
                </div>
                <button class="dev-background-transparent-border-none-aba8" id="btnCloseWebhookModal" style="color: #94a3b8; cursor: pointer;">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
            <div class="vk-modal-body">
                <input type="hidden" id="whId" value="" />
                <div class="crud-form-group">
                    <label class="crud-form-label" for="whEventType">Event Type *</label>
                    <input class="crud-form-input" id="whEventType" type="text" placeholder="e.g. telemetry.temperature.threshold" required />
                </div>
                <div class="crud-form-group">
                    <label class="crud-form-label" for="whTargetEndpoint">Target Endpoint URL *</label>
                    <input class="crud-form-input" id="whTargetEndpoint" type="url" placeholder="https://api.baltnord.lv/v1/vostok/events" required />
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="whStatus">Delivery Status</label>
                        <select class="crud-form-select" id="whStatus">
                            <option value="Delivered">Delivered (200 OK)</option>
                            <option value="Failed">Failed (504 Timeout)</option>
                            <option value="Pending">Pending (In Queue)</option>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="whLatency">Simulated Latency (ms)</label>
                        <input class="crud-form-input" id="whLatency" type="number" value="35" />
                    </div>
                </div>
                <div class="crud-form-group">
                    <label class="crud-form-label" for="whPayload">JSON Payload</label>
                    <textarea class="crud-form-textarea" id="whPayload" placeholder='{"event":"telemetry.vibration.alert", "device":"PROD-1001-KZ"}'></textarea>
                </div>
            </div>
            <div class="vk-modal-footer">
                <button class="vk-btn vk-btn-outline" id="btnCancelWebhookModal">Cancel</button>
                <button class="vk-btn vk-btn-accent" id="btnSaveWebhook">
                    <span class="material-symbols-outlined text-[16px]">send</span> Save &amp; Dispatch
                </button>
            </div>
        </div>
    </div>

    <!-- PUBLIC-FACING / DEVELOPER FOOTER -->
    <footer class="vk-footer">
        <div class="vk-footer-grid">
            <div>
                <div class="dev-display-flex-align-items-6751" >
                    <span class="dev-font-weight-700-font-230a" >VOSTOKPRIBOR</span>
                    <span class="vk-tag dev-background-rgba-30-143-5c91">SYSTEM 10</span>
                </div>
                <p class="dev-font-size-12px-line-7c36" >
                    Industrial equipment, automation, and logistics systems manufacturer. Established in 1968 in Almaty, Kazakhstan. Developer Platform &amp; API Enclave Gateway.
                </p>
                <div class="dev-font-family-var-font-a82b" >
                    FQDN: developer.vostokpribor.local • Node IP: 10.240.0.12
                </div>
            </div>

            <div>
                <div class="dev-font-family-var-font-7870" >Developer Resources</div>
                <div class="dev-display-flex-flex-direction-e6a0" >
                    <a class="dev-link-slate-300" href="Dashboard.php" >API Reference (OpenAPI 3.1)</a>
                    <a class="dev-link-slate-300" href="guides.php" >Integration Guide (DOC-2026-010)</a>
                    <a class="dev-link-slate-300" href="credentials.php" >Partner Key Enclave</a>
                    <a class="dev-link-slate-300" href="sandbox.php" >Interactive Dispatch Console</a>
                </div>
            </div>

            <div>
                <div class="dev-font-family-var-font-7870" >Security &amp; Compliance</div>
                <div class="dev-display-flex-flex-direction-e6a0" >
                    <span class="dev-text-slate-400" >IEC 62443-4-2 Industrial Security</span>
                    <span class="dev-text-slate-400" >ISO 27001 Certified Gateway</span>
                    <span class="dev-text-slate-400" >mTLS Ed25519 Partner Clearance</span>
                    <a class="dev-color-var-vk-alert-7bac" href="../Admin & Governance Portal/login.php" >Admin Governance Enclave</a>
                </div>
            </div>

            <div>
                <div class="dev-font-family-var-font-7870" >Engineering Contacts</div>
                <div class="dev-font-size-12px-line-049d" >
                    <div><strong>Dana Yermak (EMP-1017)</strong></div>
                    <div class="dev-font-family-var-font-d009" >dana.yermak@vostokpribor.local</div>
                    <div class="dev-margin-top-6px-57a7" ><strong>Jonas Richter (EMP-1020)</strong></div>
                    <div class="dev-font-family-var-font-d009" >jonas.richter@vostokpribor.local</div>
                </div>
            </div>
        </div>
        <div class="dev-max-width-1400px-margin-79e2" >
            <span>&copy; 1968&ndash;2026 VOSTOKPRIBOR. All industrial and telemetric protocols reserved.</span>
            <span>DATA SENSITIVITY: INTERNAL (RESTRICTED TO PARTNER SYSTEMS)</span>
        </div>
    </footer>

    <!-- Universal Command Palette Modal -->
    <div class="cmd-palette-backdrop" id="cmd-palette-modal">
        <div class="cmd-palette-box">
            <div class="cmd-palette-header">
                <span class="material-symbols-outlined text-[20px] dev-color-accent">terminal</span>
                <input class="cmd-palette-input" id="cmd-palette-input" type="text" placeholder="Type a command or jump to documentation..." />
            </div>
            <div class="cmd-palette-list" id="cmd-palette-results">
                <a class="cmd-palette-item" href="Dashboard.php">
                    <span class="material-symbols-outlined text-[16px]">menu_book</span>
                    <span>API Reference &amp; Endpoints</span>
                </a>
                <a class="cmd-palette-item" href="guides.php">
                    <span class="material-symbols-outlined text-[16px]">integration_instructions</span>
                    <span>Integration Guides &amp; DOC-2026-010</span>
                </a>
                <a class="cmd-palette-item" href="credentials.php">
                    <span class="material-symbols-outlined text-[16px]">key</span>
                    <span>API Credentials Vault</span>
                </a>
                <a class="cmd-palette-item" href="sandbox.php">
                    <span class="material-symbols-outlined text-[16px]">terminal</span>
                    <span>Interactive Sandbox Console</span>
                </a>
                <a class="cmd-palette-item" href="metrics.php">
                    <span class="material-symbols-outlined text-[16px]">monitoring</span>
                    <span>Usage Metrics &amp; Telemetry</span>
                </a>
                <a class="cmd-palette-item" href="partner-registration.php">
                    <span class="material-symbols-outlined text-[16px]">how_to_reg</span>
                    <span>Enterprise Partner Registration</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Universal Toast Container -->
    <div class="vk-toast-container"></div>

    <script src="js/dev-common.js"></script>
    <script src="js/dev-metrics.js"></script>
  <script src="../assets/js/notifications-hub.js" defer></script>
</body>

</html>