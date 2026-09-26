<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('DEV');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Telemetry &amp; Usage Metrics - VOSTOKPRIBOR Developer Portal</title>
    <link rel="stylesheet" href="css/dev-tokens.css" />
    <link rel="stylesheet" href="css/dev-common.css" />
    <link rel="stylesheet" href="css/dev-metrics.css" />
</head>

<body>
    <!-- TOP NAVIGATION BAR (System 10 Cyan 4px stripe) -->
    <header class="vk-top-navbar">
        <div class="dev-flex-center-gap-24" >
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
                    <div class="dev-font-size-12px-font-2ab2" >Dana Yermak</div>
                    <div class="dev-font-family-var-font-b636" >EMP-1017 • Integration Eng</div>
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
    <aside class="vk-sidebar">
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

            <div class="vk-sidebar-header dev-mt-16">Unified Ecosystem</div>
            <a class="vk-nav-item" href="../VOSTOKPRIBOR Corporate Web Platform/index.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px] dev-color-1b3a5c-1796">language</span>
                    <span>Corporate Platform</span>
                </div>
                <span class="vk-tag dev-font-size-9px-background-21b8">SYS-01</span>
            </a>
            <a class="vk-nav-item" href="../Employee Intranet/index.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px] dev-color-5c7290-b50c">badge</span>
                    <span>Employee Intranet</span>
                </div>
                <span class="vk-tag dev-font-size-9px-background-cfa1">SYS-04</span>
            </a>
            <a class="vk-nav-item" href="../File Center/index.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px] dev-color-5a6470-9f56">folder_zip</span>
                    <span>File Center</span>
                </div>
                <span class="vk-tag dev-font-size-9px-background-7efe">SYS-09</span>
            </a>
            <a class="vk-nav-item" href="../Admin & Governance Portal/index.php">
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
            <div class="dev-font-family-var-font-940f" >P99 Latency: 42.1ms</div>
        </div>

        <!-- Log Out -->

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

        <!-- 4 KPI HUD CARDS -->
        <div class="metrics-kpi-grid">
            <div class="kpi-metric-card">
                <div class="kpi-title">
                    <span>Total Invocations (24h)</span>
                    <span class="material-symbols-outlined text-[16px] dev-color-accent">swap_calls</span>
                </div>
                <div class="kpi-value">1,428,910</div>
                <div class="kpi-subtext dev-color-var-vk-secondary-bd5b">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +12.4% vs previous 24h
                </div>
            </div>

            <div class="kpi-metric-card">
                <div class="kpi-title">
                    <span>Average Ingestion Latency</span>
                    <span class="material-symbols-outlined text-[16px] dev-color-secondary">timer</span>
                </div>
                <div class="kpi-value">28.4 <span class="dev-font-size-14px-font-8cb8" >ms</span></div>
                <div class="kpi-subtext">P95: 54.2ms • P99: 84.1ms</div>
            </div>

            <div class="kpi-metric-card">
                <div class="kpi-title">
                    <span>Gateway HTTP Error Rate</span>
                    <span class="material-symbols-outlined text-[16px] dev-color-alert">warning</span>
                </div>
                <div class="kpi-value dev-color-2e6e4e-f283">0.02%</div>
                <div class="kpi-subtext">28 errors / 1.42M requests</div>
            </div>

            <div class="kpi-metric-card">
                <div class="kpi-title">
                    <span>Webhook Dispatch SLA</span>
                    <span class="material-symbols-outlined text-[16px] dev-color-secondary">outgoing_mail</span>
                </div>
                <div class="kpi-value dev-color-var-vk-primary-40d3">99.98%</div>
                <div class="kpi-subtext">6,410 delivered • 1 retry pending</div>
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

            <!-- QUOTA GAUGES -->
            <div class="vk-card">
                <div class="vk-card-header">
                    <div>
                        <div class="vk-card-title">Key Quota Consumption</div>
                        <div class="vk-card-subtitle">Daily budget per authorized partner enclave</div>
                    </div>
                </div>
                <div class="vk-card-body dev-display-flex-flex-direction-269e">
                    <div>
                        <div class="dev-display-flex-justify-content-c0fa" >
                            <span class="dev-text-primary-bold" >KEY-9842 BaltNord (PRJ-2026-002)</span>
                            <span class="dev-font-family-var-font-e036" >42,890 / 100k</span>
                        </div>
                        <div class="dev-height-8px-background-var-8d56" >
                            <div class="dev-width-42-8-height-6066" ></div>
                        </div>
                    </div>

                    <div>
                        <div class="dev-display-flex-justify-content-c0fa" >
                            <span class="dev-text-primary-bold" >KEY-4419 IoT Sensor Pipeline</span>
                            <span class="dev-font-family-var-font-e036" >382,100 / 500k</span>
                        </div>
                        <div class="dev-height-8px-background-var-8d56" >
                            <div class="dev-width-76-4-height-0ec0" ></div>
                        </div>
                    </div>

                    <div>
                        <div class="dev-display-flex-justify-content-c0fa" >
                            <span class="dev-text-primary-bold" >KEY-1108 Almaty Logistics Inbound</span>
                            <span class="dev-font-family-var-font-e036" >14,350 / 50k</span>
                        </div>
                        <div class="dev-height-8px-background-var-8d56" >
                            <div class="dev-width-28-7-height-7a47" ></div>
                        </div>
                    </div>

                    <div>
                        <div class="dev-display-flex-justify-content-c0fa" >
                            <span class="dev-text-primary-bold" >KEY-7703 Internal Automated CI/CD</span>
                            <span class="dev-font-family-var-font-e036" >8,920 / 25k</span>
                        </div>
                        <div class="dev-height-8px-background-var-8d56" >
                            <div class="dev-width-35-6-height-6aa4" ></div>
                        </div>
                    </div>

                    <div class="dev-margin-top-6px-padding-7a3e" >
                        <strong>Policy:</strong> Standard partner quota resets daily at 00:00:00 UTC+6. Excess calls return HTTP 429 with <code>Retry-After</code> headers.
                    </div>
                </div>
            </div>
        </div>

        <!-- OUTBOUND WEBHOOK DISPATCH LEDGER -->
        <div class="vk-card dev-margin-bottom-30px-9550">
            <div class="vk-card-header">
                <div>
                    <div class="vk-card-title">Outbound Webhook Delivery Log (System 10 Gateway)</div>
                    <div class="vk-card-subtitle">Real-time status of asynchronous telemetry and order status callbacks delivered to external partner systems</div>
                </div>
                <div class="dev-flex-center-gap-8" >
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
                            <th class="dev-width-110px-text-align-2833" >Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="vk-table-row-internal">
                            <td><code>WH-2026-9081</code></td>
                            <td class="dev-mono-11" >2026-09-11 16:42:10</td>
                            <td><span class="vk-tag dev-font-size-10px-font-eb29">telemetry.vibration.alert</span></td>
                            <td class="dev-mono-muted-11" >https://api.baltnord.lv/v1/vostok/events</td>
                            <td><span class="vk-tag dev-background-dcfce7-color-166534-4a19">200 OK</span></td>
                            <td class="dev-mono-11" >42 ms</td>
                            <td class="dev-text-right" ><span class="vk-tag dev-text-10">Delivered</span></td>
                        </tr>
                        <tr class="vk-table-row-internal">
                            <td><code>WH-2026-9080</code></td>
                            <td class="dev-mono-11" >2026-09-11 15:18:22</td>
                            <td><span class="vk-tag dev-font-size-10px-font-7515">order.status.dispatched</span></td>
                            <td class="dev-mono-muted-11" >https://api.baltnord.lv/v1/vostok/orders</td>
                            <td><span class="vk-tag dev-background-dcfce7-color-166534-4a19">200 OK</span></td>
                            <td class="dev-mono-11" >38 ms</td>
                            <td class="dev-text-right" ><span class="vk-tag dev-text-10">Delivered</span></td>
                        </tr>
                        <tr class="vk-table-row-confidential">
                            <td><code>WH-2026-9079</code></td>
                            <td class="dev-mono-11" >2026-09-11 14:05:01</td>
                            <td><span class="vk-tag dev-font-size-10px-font-cc63">scada.emergency.trip</span></td>
                            <td class="dev-mono-muted-11" >https://gateway.almaty-logistics.kz/wh</td>
                            <td><span class="vk-tag dev-background-dcfce7-color-166534-4a19">200 OK</span></td>
                            <td class="dev-mono-11" >18 ms</td>
                            <td class="dev-text-right" ><span class="vk-tag dev-text-10">Delivered</span></td>
                        </tr>
                        <tr class="vk-table-row-confidential">
                            <td><code>WH-2026-9078</code></td>
                            <td class="dev-mono-11" >2026-09-11 12:30:15</td>
                            <td><span class="vk-tag dev-font-size-10px-font-7515">telemetry.pressure.warning</span></td>
                            <td class="dev-mono-muted-11" >https://api.baltnord.lv/v1/vostok/events</td>
                            <td><span class="vk-tag dev-background-fee2e2-color-var-4d78">504 TIMEOUT</span></td>
                            <td class="dev-mono-11" >3002 ms</td>
                            <td class="dev-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-outline btn-retry-webhook dev-padding-2px-8px-font-174a">
                                    <span class="material-symbols-outlined text-[14px]">refresh</span> Retry
                                </button>
                            </td>
                        </tr>
                        <tr class="vk-table-row-internal">
                            <td><code>WH-2026-9077</code></td>
                            <td class="dev-mono-11" >2026-09-11 10:15:44</td>
                            <td><span class="vk-tag dev-font-size-10px-font-f9bc">catalog.price_index.updated</span></td>
                            <td class="dev-mono-muted-11" >https://b2b.vostokpribor.local/sync</td>
                            <td><span class="vk-tag dev-background-dcfce7-color-166534-4a19">200 OK</span></td>
                            <td class="dev-mono-11" >24 ms</td>
                            <td class="dev-text-right" ><span class="vk-tag dev-text-10">Delivered</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

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
                    <a class="dev-color-var-vk-alert-7bac" href="../Admin & Governance Portal/index.php" >Admin Governance Enclave</a>
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
</body>

</html>