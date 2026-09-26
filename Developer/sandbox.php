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
    <title>Interactive API Sandbox &amp; Testing Console - VOSTOKPRIBOR Developer Portal</title>
    <link rel="stylesheet" href="css/dev-tokens.css" />
    <link rel="stylesheet" href="css/dev-common.css" />
    <link rel="stylesheet" href="css/dev-sandbox.css" />
</head>

<body>
    <!-- TOP NAVIGATION BAR -->
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
                <span class="material-symbols-outlined text-[14px] dev-color-accent">code_blocks</span>
                <span class="dev-font-family-var-font-eb0b" >SANDBOX: <strong>LIVE SIMULATION RUNNER</strong></span>
            </div>
        </div>

        <div class="dev-flex-center-gap-16" >
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
                    <div class="dev-font-size-12px-font-2ab2" >Kristaps Ozols</div>
                    <div class="dev-font-family-var-font-b636" >CUS-1002 • BaltNord</div>
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

            <div class="vk-sidebar-header dev-mt-16">Organization</div>
            <a class="vk-nav-item" href="partner-registration.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">verified_user</span>
                    <span>Partner Registration</span>
                </div>
                <span class="dev-font-family-var-font-296e" >NDA</span>
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

        <div class="dev-padding-16px-border-top-156b" >
            <div class="dev-font-family-var-font-6447" >Mock Ingestion Engine</div>
            <div class="dev-text-muted" >Target: Almaty Mock Cluster</div>
            <div class="dev-color-var-vk-secondary-9fd9" >Latency: &lt;35ms Simulated</div>
        </div>

        <!-- Log Out -->

    </aside>

    <!-- MAIN CONTENT -->
    <main class="vk-app-body">
        <div class="dev-mb-20" >
            <div class="dev-display-flex-align-items-81c3" >
                <span class="dev-mono-muted-11" >ROOT / SYSTEM 10 / TESTING CONSOLE</span>
                <span class="badge-classification badge-internal">Sandbox Mock Environment</span>
            </div>
            <h1 class="dev-font-size-26px-font-2295" >
                Interactive API Request Runner &amp; Simulator
            </h1>
            <p class="dev-color-var-vk-neutral-56fa" >
                Directly dispatch test requests to VOSTOKPRIBOR sandbox nodes without impacting production SCADA telemetry or ERP records.
            </p>
        </div>

        <!-- Quick Endpoint Presets -->
        <div class="dev-display-flex-align-items-7bb4" >
            <span class="dev-font-family-var-font-0e94" >Quick Presets:</span>
            <button class="vk-btn vk-btn-outline btn-preset" data-method="GET" data-url="/v1/sensors/optical/telemetry?device_id=PROD-1001-KZ" data-body="">
                <span class="endpoint-badge-get dev-padding-1px-4px-font-650b">GET</span> PROD-1001 Optical Telemetry
            </button>
            <button class="vk-btn vk-btn-outline btn-preset" data-method="GET" data-url="/v1/devices/geodetic/measurements?unit=PROD-1002" data-body="">
                <span class="endpoint-badge-get dev-padding-1px-4px-font-650b">GET</span> PROD-1002 Geodetic Vectors
            </button>
            <button class="vk-btn vk-btn-outline btn-preset" data-method="POST" data-url="/v1/scada/ingest/frames" data-body='{"facility_id":"ALMATY-CENTRAL-01","protocol":"MODBUS-TCP","plc_register":"40001","payload_hex":"0A2B4C"}'>
                <span class="endpoint-badge-post dev-padding-1px-4px-font-650b">POST</span> PROD-1004 SCADA Frame
            </button>
            <button class="vk-btn vk-btn-outline btn-preset" data-method="POST" data-url="/v1/b2b/orders/create" data-body='{"customer_id":"CUS-1002","items":[{"prod_id":"PROD-1001","qty":4}]}'>
                <span class="endpoint-badge-post dev-padding-1px-4px-font-650b">POST</span> B2B Order Create
            </button>
        </div>

        <!-- Sandbox Layout Grid -->
        <div class="sandbox-container">
            <!-- Left: Request Builder -->
            <div class="vk-card tag-internal">
                <div class="vk-card-header">
                    <div class="dev-font-weight-700-font-2e8f" >HTTP Request Builder</div>
                    <span class="badge-classification badge-internal">Sandbox Mode</span>
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
                        <label class="dev-font-size-11px-font-a64a" >Headers (Auto-injected)</label>
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
    </main>

        <footer class="vk-footer">
        <div class="vk-footer-bottom dev-border-top-none-padding-efbd">
            <div>© 2026 VOSTOKPRIBOR Global Logistics &amp; Supply JSC. System 10: Developer &amp; API Portal.</div>
            <div>Testing Sandbox Node: almaty-sbx-01.local • Isolated RAM Partition</div>
        </div>
    </footer>

    <script src="js/dev-common.js"></script>
    <script src="js/dev-sandbox.js"></script>
</body>

</html>