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
        <div style="display: flex; align-items: center; gap: 24px;">
            <a class="vk-brand-section" href="index.php">
                <img alt="VOSTOKPRIBOR Official Mark" class="brand-logo-img" style="height: 30px; width: 30px; object-fit: contain;" src="https://lh3.googleusercontent.com/aida/AEtjO1XMGDZ9meLzj0XkVJ6C4Xv2AoEzKtMyFqF7KQ8eMADmbywzsRZ7VF4Em6pQ7fZ8QRJExCZedCKEUUo1fN1LpEmGsQva25blyUsGhOPvX2vv2cHGkppzOp9iT33Xy2n4Nkr5e_YY_0J78vA8Q7vKUpViCouPJo13HFVvW5olf7QEFzU3EX-WCqc0SYyPXZb7E11PafOMC_KprpG6Tre6bN_DyZS-CI-J5qiCgBtikqVNYGueATNgWbi73Q" />
                <div style="display: flex; flex-direction: column;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-family: var(--font-heading); font-weight: 700; font-size: 15px; letter-spacing: -0.02em;">VOSTOKPRIBOR</span>
                        <span class="vk-system-badge">SYS-10 // DEV-PORTAL</span>
                    </div>
                    <span style="font-family: var(--font-mono); font-size: 10px; color: #94A3B8;">ALMATY CENTRAL • EST. 1968 • API GATEWAY v4.12.0</span>
                </div>
            </a>
            <div style="display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.06); padding: 4px 12px; border-radius: var(--radius-sm); border: 1px solid rgba(255,255,255,0.1);">
                <span class="material-symbols-outlined text-[14px]" style="color: var(--vk-sys-accent);">monitoring</span>
                <span style="font-family: var(--font-mono); font-size: 11px; color: #E2E8F0;">TELEMETRY: <strong>INGESTION &amp; DISPATCH LEDGER</strong></span>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="position: relative; width: 280px;">
                <span class="material-symbols-outlined text-[16px]" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #64748B;">search</span>
                <input class="search-trigger-input" type="text" placeholder="Search metrics, logs (Ctrl + K)" readonly
                    style="width: 100%; height: 32px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: var(--radius-sm); padding-left: 32px; padding-right: 12px; font-family: var(--font-mono); font-size: 11px; color: #ffffff; cursor: pointer;" />
            </div>
            <div style="display: flex; align-items: center; gap: 6px; font-family: var(--font-mono); font-size: 11px; color: #94A3B8; background: rgba(0,0,0,0.25); padding: 4px 10px; border-radius: var(--radius-sm);">
                <span class="material-symbols-outlined text-[14px]" style="color: var(--vk-secondary);">schedule</span>
                <span class="station-live-clock">17:25:00 UTC+6</span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; padding-left: 12px; border-left: 1px solid rgba(255,255,255,0.15);">
                <div style="text-align: right;">
                    <div style="font-size: 12px; font-weight: 600; color: #ffffff;">Dana Yermak</div>
                    <div style="font-family: var(--font-mono); font-size: 10px; color: var(--vk-sys-accent);">EMP-1017 • Integration Eng</div>
                </div>
                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--vk-primary); display: flex; align-items: center; justify-content: center; border: 1px solid var(--vk-sys-accent);">
                    <span class="material-symbols-outlined text-[18px]" style="color: #ffffff;">person</span>
                </div>
            </div>
        </div>
    </header>

    <!-- LEFT SIDEBAR -->
    <aside class="vk-sidebar">
        <div class="vk-sidebar-nav">
            <div class="vk-sidebar-header">Core Documentation</div>
            <a class="vk-nav-item" href="index.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]">menu_book</span>
                    <span>API Reference</span>
                </div>
                <span class="vk-tag" style="font-size: 10px;">v4.1</span>
            </a>
            <a class="vk-nav-item" href="guides.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]">integration_instructions</span>
                    <span>Integration Guides</span>
                </div>
                <span class="vk-tag vk-tag-internal" style="font-size: 10px;">DOC-2026</span>
            </a>

            <div class="vk-sidebar-header" style="margin-top: 20px;">Developer Tools</div>
            <a class="vk-nav-item" href="credentials.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]">key</span>
                    <span>API Credentials</span>
                </div>
            </a>
            <a class="vk-nav-item" href="sandbox.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]">terminal</span>
                    <span>Interactive Sandbox</span>
                </div>
            </a>
            <a class="vk-nav-item active" href="metrics.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]">monitoring</span>
                    <span>Usage &amp; Telemetry</span>
                </div>
            </a>
            <a class="vk-nav-item" href="partner-registration.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                    <span>Partner Registration</span>
                </div>
            </a>

                        <div class="vk-sidebar-header" style="margin-top: 16px;">Unified Ecosystem</div>
            <a class="vk-nav-item" href="../VOSTOKPRIBOR Corporate Web Platform/index.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]" style="color: #1B3A5C;">language</span>
                    <span>Corporate Platform</span>
                </div>
                <span class="vk-tag" style="font-size: 9px; background: rgba(27,58,92,0.1); color: #1B3A5C; border: 1px solid #1B3A5C;">SYS-01</span>
            </a>
            <a class="vk-nav-item" href="../Employee Intranet/index.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]" style="color: #5C7290;">badge</span>
                    <span>Employee Intranet</span>
                </div>
                <span class="vk-tag" style="font-size: 9px; background: rgba(92,114,144,0.1); color: #5C7290; border: 1px solid #5C7290;">SYS-04</span>
            </a>
            <a class="vk-nav-item" href="../File Center/index.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]" style="color: #5A6470;">folder_zip</span>
                    <span>File Center</span>
                </div>
                <span class="vk-tag" style="font-size: 9px; background: rgba(90,100,112,0.1); color: #5A6470; border: 1px solid #5A6470;">SYS-09</span>
            </a>
            <a class="vk-nav-item" href="../Admin & Governance Portal/index.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]" style="color: var(--vk-alert);">shield</span>
                    <span>Admin &amp; Governance</span>
                </div>
                <span class="vk-tag vk-tag-confidential" style="font-size: 10px;">SYS-11</span>
            </a>
        </div>

        <div style="padding: 16px; border-top: 1px solid var(--vk-neutral-200); background: #ffffff;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <span class="vk-status-indicator online"></span>
                <span style="font-family: var(--font-mono); font-size: 11px; font-weight: 600; color: var(--vk-neutral-900);">KONG CLUSTER ONLINE</span>
            </div>
            <div style="font-family: var(--font-mono); font-size: 11px; color: var(--vk-neutral-600);">Node: gw-almaty-01 (10.240.0.12)</div>
            <div style="font-family: var(--font-mono); font-size: 10px; color: var(--vk-neutral-600); margin-top: 4px;">P99 Latency: 42.1ms</div>
        </div>
    
            <!-- Log Out -->
            <a href="login.php" class="sidebar-nav-item sidebar-nav-item--logout" id="btn-logout" onclick="(function(){sessionStorage.clear();localStorage.clear();})()">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                  </svg>
                </span>
                <span>Log Out</span>
              </div>
            </a>
      </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="vk-main-layout">
        <!-- HEADER BLOCK -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--vk-neutral-200);">
            <div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
                    <span class="vk-tag vk-tag-internal">CLASSIFICATION: INTERNAL // #3E7CB1</span>
                    <span style="font-family: var(--font-mono); font-size: 12px; color: var(--vk-neutral-600);">REF: TELEMETRY-SYS10-KONG</span>
                </div>
                <h1 style="font-size: 26px; font-weight: 700; color: var(--vk-primary); margin: 0; display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined" style="font-size: 28px; color: var(--vk-sys-accent);">query_stats</span>
                    API Gateway Telemetry &amp; Quota Consumption
                </h1>
                <p style="color: var(--vk-neutral-600); font-size: 14px; margin: 4px 0 0 0;">
                    Real-time ingestion performance, rate limit consumption across registered client applications, and outbound webhook delivery telemetry.
                </p>
            </div>
            <div style="display: flex; gap: 8px; align-items: center;">
                <span style="font-family: var(--font-mono); font-size: 12px; color: var(--vk-neutral-600); margin-right: 4px;">Timeframe:</span>
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
                    <span class="material-symbols-outlined text-[16px]" style="color: var(--vk-sys-accent);">swap_calls</span>
                </div>
                <div class="kpi-value">1,428,910</div>
                <div class="kpi-subtext" style="color: var(--vk-secondary); display: flex; align-items: center; gap: 4px;">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +12.4% vs previous 24h
                </div>
            </div>

            <div class="kpi-metric-card">
                <div class="kpi-title">
                    <span>Average Ingestion Latency</span>
                    <span class="material-symbols-outlined text-[16px]" style="color: var(--vk-secondary);">timer</span>
                </div>
                <div class="kpi-value">28.4 <span style="font-size: 14px; font-weight: 400; color: var(--vk-neutral-600);">ms</span></div>
                <div class="kpi-subtext">P95: 54.2ms • P99: 84.1ms</div>
            </div>

            <div class="kpi-metric-card">
                <div class="kpi-title">
                    <span>Gateway HTTP Error Rate</span>
                    <span class="material-symbols-outlined text-[16px]" style="color: var(--vk-alert);">warning</span>
                </div>
                <div class="kpi-value" style="color: #2E6E4E;">0.02%</div>
                <div class="kpi-subtext">28 errors / 1.42M requests</div>
            </div>

            <div class="kpi-metric-card">
                <div class="kpi-title">
                    <span>Webhook Dispatch SLA</span>
                    <span class="material-symbols-outlined text-[16px]" style="color: var(--vk-secondary);">outgoing_mail</span>
                </div>
                <div class="kpi-value" style="color: var(--vk-primary);">99.98%</div>
                <div class="kpi-subtext">6,410 delivered • 1 retry pending</div>
            </div>
        </div>

        <!-- TWO COLUMN LAYOUT: INGESTION TRAFFIC HOURLY SPIKES + QUOTA CONSUMPTION -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px;">
            <!-- INGESTION CHART -->
            <div class="vk-card">
                <div class="vk-card-header">
                    <div>
                        <div class="vk-card-title">Hourly Ingestion Traffic Distribution</div>
                        <div class="vk-card-subtitle">Kong Gateway cluster throughput (requests / hour) over 24-hour cycle (UTC+6 Almaty)</div>
                    </div>
                    <span class="vk-tag" style="background: rgba(30,143,166,0.1); color: var(--vk-sys-accent); border: 1px solid rgba(30,143,166,0.3);">
                        PEAK: 98,400 REQ/H @ 14:00
                    </span>
                </div>
                <div class="vk-card-body">
                    <div class="chart-mockup">
                        <div class="chart-bar-col" style="height: 18%;" title="00:00 - 12,400 req"></div>
                        <div class="chart-bar-col" style="height: 14%;" title="01:00 - 9,800 req"></div>
                        <div class="chart-bar-col" style="height: 11%;" title="02:00 - 7,600 req"></div>
                        <div class="chart-bar-col" style="height: 9%;" title="03:00 - 6,200 req"></div>
                        <div class="chart-bar-col" style="height: 12%;" title="04:00 - 8,100 req"></div>
                        <div class="chart-bar-col" style="height: 22%;" title="05:00 - 15,300 req"></div>
                        <div class="chart-bar-col" style="height: 38%;" title="06:00 - 28,400 req"></div>
                        <div class="chart-bar-col" style="height: 62%;" title="07:00 - 45,900 req"></div>
                        <div class="chart-bar-col" style="height: 85%;" title="08:00 - 74,100 req (Shift Start)"></div>
                        <div class="chart-bar-col" style="height: 92%;" title="09:00 - 88,400 req"></div>
                        <div class="chart-bar-col" style="height: 88%;" title="10:00 - 82,100 req"></div>
                        <div class="chart-bar-col" style="height: 94%;" title="11:00 - 91,200 req"></div>
                        <div class="chart-bar-col" style="height: 78%;" title="12:00 - 70,500 req"></div>
                        <div class="chart-bar-col" style="height: 89%;" title="13:00 - 86,400 req"></div>
                        <div class="chart-bar-col" style="height: 100%;" title="14:00 - 98,400 req (Daily Peak)"></div>
                        <div class="chart-bar-col" style="height: 95%;" title="15:00 - 93,800 req"></div>
                        <div class="chart-bar-col" style="height: 87%;" title="16:00 - 81,300 req"></div>
                        <div class="chart-bar-col" style="height: 80%;" title="17:00 - 76,000 req"></div>
                        <div class="chart-bar-col" style="height: 65%;" title="18:00 - 58,200 req"></div>
                        <div class="chart-bar-col" style="height: 50%;" title="19:00 - 41,000 req"></div>
                        <div class="chart-bar-col" style="height: 39%;" title="20:00 - 32,800 req"></div>
                        <div class="chart-bar-col" style="height: 31%;" title="21:00 - 24,100 req"></div>
                        <div class="chart-bar-col" style="height: 25%;" title="22:00 - 18,900 req"></div>
                        <div class="chart-bar-col" style="height: 20%;" title="23:00 - 14,200 req"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-family: var(--font-mono); font-size: 10px; color: var(--vk-neutral-600); margin-top: 8px; border-top: 1px dashed var(--vk-neutral-200); padding-top: 6px;">
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
                <div class="vk-card-body" style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                            <span style="font-weight: 600; color: var(--vk-primary);">KEY-9842 BaltNord (PRJ-2026-002)</span>
                            <span style="font-family: var(--font-mono); color: var(--vk-neutral-600);">42,890 / 100k</span>
                        </div>
                        <div style="height: 8px; background: var(--vk-neutral-200); border-radius: 4px; overflow: hidden;">
                            <div style="width: 42.8%; height: 100%; background: var(--vk-sys-accent);"></div>
                        </div>
                    </div>

                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                            <span style="font-weight: 600; color: var(--vk-primary);">KEY-4419 IoT Sensor Pipeline</span>
                            <span style="font-family: var(--font-mono); color: var(--vk-neutral-600);">382,100 / 500k</span>
                        </div>
                        <div style="height: 8px; background: var(--vk-neutral-200); border-radius: 4px; overflow: hidden;">
                            <div style="width: 76.4%; height: 100%; background: var(--vk-accent);"></div>
                        </div>
                    </div>

                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                            <span style="font-weight: 600; color: var(--vk-primary);">KEY-1108 Almaty Logistics Inbound</span>
                            <span style="font-family: var(--font-mono); color: var(--vk-neutral-600);">14,350 / 50k</span>
                        </div>
                        <div style="height: 8px; background: var(--vk-neutral-200); border-radius: 4px; overflow: hidden;">
                            <div style="width: 28.7%; height: 100%; background: #2E6E4E;"></div>
                        </div>
                    </div>

                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                            <span style="font-weight: 600; color: var(--vk-primary);">KEY-7703 Internal Automated CI/CD</span>
                            <span style="font-family: var(--font-mono); color: var(--vk-neutral-600);">8,920 / 25k</span>
                        </div>
                        <div style="height: 8px; background: var(--vk-neutral-200); border-radius: 4px; overflow: hidden;">
                            <div style="width: 35.6%; height: 100%; background: var(--vk-secondary);"></div>
                        </div>
                    </div>

                    <div style="margin-top: 6px; padding: 10px; background: var(--vk-neutral-50); border: 1px solid var(--vk-neutral-200); border-radius: var(--radius-sm); font-size: 11px; color: var(--vk-neutral-600);">
                        <strong>Policy:</strong> Standard partner quota resets daily at 00:00:00 UTC+6. Excess calls return HTTP 429 with <code>Retry-After</code> headers.
                    </div>
                </div>
            </div>
        </div>

        <!-- OUTBOUND WEBHOOK DISPATCH LEDGER -->
        <div class="vk-card" style="margin-bottom: 30px;">
            <div class="vk-card-header">
                <div>
                    <div class="vk-card-title">Outbound Webhook Delivery Log (System 10 Gateway)</div>
                    <div class="vk-card-subtitle">Real-time status of asynchronous telemetry and order status callbacks delivered to external partner systems</div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="vk-tag vk-tag-internal">IEC 62443 VERIFIED</span>
                </div>
            </div>
            <div class="vk-card-body" style="padding: 0;">
                <table class="vk-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;">Delivery ID</th>
                            <th style="width: 150px;">Timestamp (UTC+6)</th>
                            <th style="width: 210px;">Event Type</th>
                            <th>Target Endpoint</th>
                            <th style="width: 130px;">Status</th>
                            <th style="width: 90px;">Latency</th>
                            <th style="width: 110px; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="vk-table-row-internal">
                            <td><code>WH-2026-9081</code></td>
                            <td style="font-family: var(--font-mono); font-size: 11px;">2026-09-11 16:42:10</td>
                            <td><span class="vk-tag" style="font-size: 10px; font-family: var(--font-mono); background: #E0F2FE; color: #0369A1;">telemetry.vibration.alert</span></td>
                            <td style="font-family: var(--font-mono); font-size: 11px; color: var(--vk-neutral-600);">https://api.baltnord.lv/v1/vostok/events</td>
                            <td><span class="vk-tag" style="background: #DCFCE7; color: #166534;">200 OK</span></td>
                            <td style="font-family: var(--font-mono); font-size: 11px;">42 ms</td>
                            <td style="text-align: right;"><span class="vk-tag" style="font-size: 10px;">Delivered</span></td>
                        </tr>
                        <tr class="vk-table-row-internal">
                            <td><code>WH-2026-9080</code></td>
                            <td style="font-family: var(--font-mono); font-size: 11px;">2026-09-11 15:18:22</td>
                            <td><span class="vk-tag" style="font-size: 10px; font-family: var(--font-mono); background: #FEF3C7; color: #92400E;">order.status.dispatched</span></td>
                            <td style="font-family: var(--font-mono); font-size: 11px; color: var(--vk-neutral-600);">https://api.baltnord.lv/v1/vostok/orders</td>
                            <td><span class="vk-tag" style="background: #DCFCE7; color: #166534;">200 OK</span></td>
                            <td style="font-family: var(--font-mono); font-size: 11px;">38 ms</td>
                            <td style="text-align: right;"><span class="vk-tag" style="font-size: 10px;">Delivered</span></td>
                        </tr>
                        <tr class="vk-table-row-confidential">
                            <td><code>WH-2026-9079</code></td>
                            <td style="font-family: var(--font-mono); font-size: 11px;">2026-09-11 14:05:01</td>
                            <td><span class="vk-tag" style="font-size: 10px; font-family: var(--font-mono); background: #FEE2E2; color: #991B1B;">scada.emergency.trip</span></td>
                            <td style="font-family: var(--font-mono); font-size: 11px; color: var(--vk-neutral-600);">https://gateway.almaty-logistics.kz/wh</td>
                            <td><span class="vk-tag" style="background: #DCFCE7; color: #166534;">200 OK</span></td>
                            <td style="font-family: var(--font-mono); font-size: 11px;">18 ms</td>
                            <td style="text-align: right;"><span class="vk-tag" style="font-size: 10px;">Delivered</span></td>
                        </tr>
                        <tr class="vk-table-row-confidential">
                            <td><code>WH-2026-9078</code></td>
                            <td style="font-family: var(--font-mono); font-size: 11px;">2026-09-11 12:30:15</td>
                            <td><span class="vk-tag" style="font-size: 10px; font-family: var(--font-mono); background: #FEF3C7; color: #92400E;">telemetry.pressure.warning</span></td>
                            <td style="font-family: var(--font-mono); font-size: 11px; color: var(--vk-neutral-600);">https://api.baltnord.lv/v1/vostok/events</td>
                            <td><span class="vk-tag" style="background: #FEE2E2; color: var(--vk-alert);">504 TIMEOUT</span></td>
                            <td style="font-family: var(--font-mono); font-size: 11px;">3002 ms</td>
                            <td style="text-align: right;">
                                <button class="vk-btn vk-btn-sm vk-btn-outline btn-retry-webhook" style="padding: 2px 8px; font-size: 11px;">
                                    <span class="material-symbols-outlined text-[14px]">refresh</span> Retry
                                </button>
                            </td>
                        </tr>
                        <tr class="vk-table-row-internal">
                            <td><code>WH-2026-9077</code></td>
                            <td style="font-family: var(--font-mono); font-size: 11px;">2026-09-11 10:15:44</td>
                            <td><span class="vk-tag" style="font-size: 10px; font-family: var(--font-mono); background: #E0E7FF; color: #3730A3;">catalog.price_index.updated</span></td>
                            <td style="font-family: var(--font-mono); font-size: 11px; color: var(--vk-neutral-600);">https://b2b.vostokpribor.local/sync</td>
                            <td><span class="vk-tag" style="background: #DCFCE7; color: #166534;">200 OK</span></td>
                            <td style="font-family: var(--font-mono); font-size: 11px;">24 ms</td>
                            <td style="text-align: right;"><span class="vk-tag" style="font-size: 10px;">Delivered</span></td>
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
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                    <span style="font-weight: 700; font-size: 14px; color: #ffffff; letter-spacing: 0.04em;">VOSTOKPRIBOR</span>
                    <span class="vk-tag" style="background: rgba(30,143,166,0.2); color: var(--vk-sys-accent); border: 1px solid var(--vk-sys-accent); font-size: 10px;">SYSTEM 10</span>
                </div>
                <p style="font-size: 12px; line-height: 1.6; margin: 0 0 12px 0; color: #94A3B8;">
                    Industrial equipment, automation, and logistics systems manufacturer. Established in 1968 in Almaty, Kazakhstan. Developer Platform &amp; API Enclave Gateway.
                </p>
                <div style="font-family: var(--font-mono); font-size: 11px; color: #64748B;">
                    FQDN: developer.vostokpribor.local • Node IP: 10.240.0.12
                </div>
            </div>

            <div>
                <div style="font-family: var(--font-mono); font-size: 11px; text-transform: uppercase; color: #94A3B8; margin-bottom: 8px; letter-spacing: 0.05em;">Developer Resources</div>
                <div style="display: flex; flex-direction: column; gap: 6px; font-size: 12px;">
                    <a href="index.php" style="color: #CBD5E1; text-decoration: none;">API Reference (OpenAPI 3.1)</a>
                    <a href="guides.php" style="color: #CBD5E1; text-decoration: none;">Integration Guide (DOC-2026-010)</a>
                    <a href="credentials.php" style="color: #CBD5E1; text-decoration: none;">Partner Key Enclave</a>
                    <a href="sandbox.php" style="color: #CBD5E1; text-decoration: none;">Interactive Dispatch Console</a>
                </div>
            </div>

            <div>
                <div style="font-family: var(--font-mono); font-size: 11px; text-transform: uppercase; color: #94A3B8; margin-bottom: 8px; letter-spacing: 0.05em;">Security &amp; Compliance</div>
                <div style="display: flex; flex-direction: column; gap: 6px; font-size: 12px;">
                    <span style="color: #94A3B8;">IEC 62443-4-2 Industrial Security</span>
                    <span style="color: #94A3B8;">ISO 27001 Certified Gateway</span>
                    <span style="color: #94A3B8;">mTLS Ed25519 Partner Clearance</span>
                    <a href="../Admin & Governance Portal/index.php" style="color: var(--vk-alert); text-decoration: none;">Admin Governance Enclave</a>
                </div>
            </div>

            <div>
                <div style="font-family: var(--font-mono); font-size: 11px; text-transform: uppercase; color: #94A3B8; margin-bottom: 8px; letter-spacing: 0.05em;">Engineering Contacts</div>
                <div style="font-size: 12px; line-height: 1.6; color: #94A3B8;">
                    <div><strong>Dana Yermak (EMP-1017)</strong></div>
                    <div style="font-family: var(--font-mono); font-size: 11px; color: var(--vk-sys-accent);">dana.yermak@vostokpribor.local</div>
                    <div style="margin-top: 6px;"><strong>Jonas Richter (EMP-1020)</strong></div>
                    <div style="font-family: var(--font-mono); font-size: 11px; color: var(--vk-sys-accent);">jonas.richter@vostokpribor.local</div>
                </div>
            </div>
        </div>
        <div style="max-width: 1400px; margin: 20px auto 0 auto; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.08); font-family: var(--font-mono); font-size: 11px; color: #64748B; display: flex; justify-content: space-between;">
            <span>&copy; 1968&ndash;2026 VOSTOKPRIBOR. All industrial and telemetric protocols reserved.</span>
            <span>DATA SENSITIVITY: INTERNAL (RESTRICTED TO PARTNER SYSTEMS)</span>
        </div>
    </footer>

    <!-- Universal Command Palette Modal -->
    <div class="cmd-palette-backdrop" id="cmd-palette-modal">
        <div class="cmd-palette-box">
            <div class="cmd-palette-header">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--vk-sys-accent);">terminal</span>
                <input class="cmd-palette-input" id="cmd-palette-input" type="text" placeholder="Type a command or jump to documentation..." />
            </div>
            <div class="cmd-palette-list" id="cmd-palette-results">
                <a class="cmd-palette-item" href="index.php">
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
