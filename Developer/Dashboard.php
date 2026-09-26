<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('Developer');
$pdo = getDbConnection();
$currUser = $_SESSION['vostok_user'] ?? ['full_name' => 'Authorized User', 'clearance_level' => 'L2'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>VOSTOKPRIBOR Developer &amp; API Portal - System 10</title>
    <link rel="stylesheet" href="css/dev-tokens.css" />
    <link rel="stylesheet" href="css/dev-common.css" />
    <link rel="stylesheet" href="css/dev-portal.css" />
</head>

<body>
    <!-- TOP NAVIGATION BAR (Deep Navy + 4px Cyan Identity Stripe) -->
    <header class="vk-top-navbar">
        <div class="dev-flex-center-gap-24" >
            <a class="vk-brand-section" href="Dashboard.php">
                <img alt="VOSTOKPRIBOR Official Mark" class="brand-logo-img dev-logo-img"
                    src="assets/logo.svg" />
                <div class="dev-flex-col" >
                    <div class="dev-flex-center-gap-8" >
                        <span class="dev-font-family-var-font-980b"
                            >VOSTOKPRIBOR</span>
                        <span class="vk-system-badge">SYS-10 // DEV-PORTAL</span>
                    </div>
                    <span class="dev-font-family-var-font-54ae" >ALMATY CENTRAL • EST.
                        1968 • API GATEWAY v4.12.0</span>
                </div>
            </a>
            <div class="dev-display-flex-align-items-bc9f"
                >
                <span class="material-symbols-outlined text-[14px] dev-color-accent">hub</span>
                <span class="dev-font-family-var-font-eb0b" >FQDN:
                    <strong>developer.vostokpribor.local</strong></span>
            </div>
        </div>

        <div class="dev-flex-center-gap-16" >
            <!-- Search Bar Trigger (Ctrl + K) -->
            <div class="dev-search-box-wrap" >
                <span class="material-symbols-outlined text-[16px] dev-position-absolute-left-10px-d4a8">search</span>
                <input class="search-trigger-input" type="text" placeholder="Search API docs, schemas (Ctrl + K)"
                    readonly
                     />
            </div>

            <!-- Almaty Live Station Time -->
            <div class="dev-display-flex-align-items-9eca"
                >
                <span class="material-symbols-outlined text-[14px] dev-color-secondary">schedule</span>
                <span class="station-live-clock">17:15:00 UTC+6</span>
            </div>

            <!-- Partner Session Persona (CUS-1002 BaltNord) -->
            <div class="dev-display-flex-align-items-20f3"
                >
                <div class="dev-text-right" >
                    <div class="dev-font-size-12px-font-2ab2" >Kristaps Ozols</div>
                    <div class="dev-font-family-var-font-b636" >CUS-1002 •
                        BaltNord</div>
                </div>
                <div class="dev-width-32px-height-32px-0eaf"
                    >
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
            <a class="vk-nav-item active" href="Dashboard.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">menu_book</span>
                    <span>API Reference</span>
                </div>
                <span class="dev-font-family-var-font-829c"
                    >REST</span>
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
            <a class="vk-nav-item" href="sandbox.php">
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
                <span class="dev-font-family-var-font-7016"
                    >99.98%</span>
            </a>
            <a href="Integrations.php" class="sidebar-nav-item">
                <div class="sidebar-item-left"><span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00E5FF" stroke-width="2">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                        </svg></span><span class="dev-color-00e5ff-font-weight-fe7a" >System Integrations</span></div><span class="sidebar-badge dev-background-rgba-0-229-2595">SYS04</span>
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

        <!-- Technical Lead Contacts (PDF Grounded: EMP-1020 & EMP-1017) -->
        <div class="dev-padding-16px-border-top-156b"
            >
            <div class="dev-font-family-var-font-6447"
                >
                Portal Custodians</div>
            <div class="dev-color-var-vk-primary-1a7b" >Jonas Richter (EMP-1020)</div>
            <div class="dev-color-var-vk-neutral-19bc" >Lead Developer • ENG</div>
            <div class="dev-color-var-vk-primary-57e8" >Dana Yermak (EMP-1017)</div>
            <div class="dev-color-var-vk-neutral-19bc" >Integration Engineer • ENG</div>
        </div>

        <!-- Log Out -->

    </aside>

    <!-- MAIN APP BODY -->
    <main class="vk-app-body">
        <!-- Page Header & Classification Bar -->
        <div class="dev-display-flex-justify-content-387e" >
            <div>
                <div class="dev-display-flex-align-items-81c3" >
                    <span class="dev-mono-muted-11" >ROOT /
                        SYSTEM 10 / API REFERENCE</span>
                    <span class="badge-classification badge-public">Public API</span>
                </div>
                <h1 class="dev-font-size-26px-font-2295" >
                    Industrial Equipment &amp; Automation API v1
                </h1>
                <p class="dev-color-var-vk-neutral-5666" >
                    Official RESTful and telemetry streaming interfaces for precision optical measurement packages,
                    geodetic kits, PLC integration, and enterprise B2B fulfillment pipelines.
                </p>
            </div>
            <div class="dev-display-flex-gap-10px-0ed0" >
                <a class="vk-btn vk-btn-outline" href="guides.php">
                    <span class="material-symbols-outlined text-[16px]">menu_book</span> Quickstart Guide
                </a>
                <a class="vk-btn vk-btn-accent" href="sandbox.php">
                    <span class="material-symbols-outlined text-[16px]">play_arrow</span> Open Sandbox
                </a>
            </div>
        </div>

        <!-- System Baseline Specs Banner (PDF Cross-Reference) -->
        <div class="vk-card tag-internal dev-margin-bottom-28px-background-8be5">
            <div class="vk-card-body dev-display-flex-align-items-8ee2">
                <div class="dev-flex-center-gap-16" >
                    <div class="dev-width-42px-height-42px-234f"
                        >
                        <span class="material-symbols-outlined text-[24px]">terminal</span>
                    </div>
                    <div>
                        <div class="dev-font-weight-700-font-2e8f" >Statutory
                            Specification DOC-2026-010 Synchronized</div>
                        <div class="dev-font-size-12px-color-6366" >
                            Conforms to Republic Heavy Automation Standards • Base URL: <code class="dev-font-family-var-font-7268"
                                >https://developer.vostokpribor.local/v1</code>
                        </div>
                    </div>
                </div>
                <div class="dev-display-flex-align-items-1c20" >
                    <span class="badge-classification badge-internal">DOC-2026-010 • INTERNAL SPEC</span>
                    <button class="vk-btn vk-btn-outline"
                        onclick="window.showToast('SPEC DOWNLOADED', 'Saved OpenAPI 3.0 YAML descriptor for System 10.', 'success')">
                        <span class="material-symbols-outlined text-[16px]">download</span> OpenAPI 3.0 JSON
                    </button>
                </div>
            </div>
        </div>

        <!-- ENDPOINTS SECTION -->
        <div class="endpoint-container">
            <!-- ENDPOINT 1: PROD-1001 Optical Sensor Package Telemetry -->
            <div class="endpoint-card tag-internal" id="endpoint-optical">
                <div class="endpoint-header">
                    <div class="dev-display-flex-align-items-1c20" >
                        <span class="endpoint-badge-get">GET</span>
                        <span class="endpoint-path">/v1/sensors/optical/telemetry</span>
                        <span class="badge-classification badge-internal">Internal • PROD-1001</span>
                    </div>
                    <div class="dev-flex-center-gap-10" >
                        <span class="dev-mono-muted-11" >Rate
                            Limit: 10k/min</span>
                        <button class="vk-btn vk-btn-outline btn-try-sandbox" data-method="GET"
                            data-url="/v1/sensors/optical/telemetry">
                            <span class="material-symbols-outlined text-[14px]">tune</span> Test in Sandbox
                        </button>
                    </div>
                </div>
                <div class="endpoint-body">
                    <div class="endpoint-docs-col">
                        <div class="dev-font-weight-600-font-94c0"
                            >
                            Fetch Industrial Optical Sensor Package Real-time Telemetry
                        </div>
                        <p class="dev-font-size-13px-color-78fc" >
                            Retrieves high-frequency telemetry streams from field-deployed optical inspection and sensor
                            apparatus (PROD-1001), including spectral resolution peak, focal plane operating
                            temperature, and signal-to-noise ratio.
                        </p>

                        <div class="dev-margin-top-16px-font-dc1f"
                            >
                            Query Parameters</div>
                        <table class="param-table">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Type</th>
                                    <th>Requirement</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="param-name">device_id</td>
                                    <td class="param-type">string</td>
                                    <td><span class="param-required">REQUIRED</span></td>
                                    <td>Assigned hardware serial or ID (e.g. <code>PROD-1001-KZ</code>)</td>
                                </tr>
                                <tr>
                                    <td class="param-name">sample_window_sec</td>
                                    <td class="param-type">integer</td>
                                    <td><span class="dev-font-size-10px-color-f312" >Optional</span>
                                    </td>
                                    <td>Window for rolling average (1 to 60, default: 5)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="endpoint-code-col">
                        <div class="code-tabs-nav">
                            <div class="dev-display-flex-gap-6px-4a5d" >
                                <button class="code-tab-btn active" data-endpoint="endpoint-optical"
                                    data-lang="curl">cURL</button>
                                <button class="code-tab-btn" data-endpoint="endpoint-optical"
                                    data-lang="python">Python</button>
                                <button class="code-tab-btn" data-endpoint="endpoint-optical"
                                    data-lang="node">Node.js</button>
                                <button class="code-tab-btn" data-endpoint="endpoint-optical" data-lang="go">Go</button>
                            </div>
                            <button class="vk-btn-outline copy-code-btn dev-padding-2px-8px-font-bd20">
                                <span class="material-symbols-outlined text-[14px]">content_copy</span> Copy
                            </button>
                        </div>
                        <pre class="code-block-box"><code>curl -X GET "https://developer.vostokpribor.local/v1/sensors/optical/telemetry?device_id=PROD-1001-KZ" \
  -H "Authorization: Bearer vk_live_9a41c2e8f10b" \
  -H "Accept: application/json"</code></pre>
                    </div>
                </div>
            </div>

            <!-- ENDPOINT 2: PROD-1002 Precision Geodetic Measurement Kit -->
            <div class="endpoint-card tag-internal" id="endpoint-geodetic">
                <div class="endpoint-header">
                    <div class="dev-display-flex-align-items-1c20" >
                        <span class="endpoint-badge-get">GET</span>
                        <span class="endpoint-path">/v1/devices/geodetic/measurements</span>
                        <span class="badge-classification badge-internal">Internal • PROD-1002</span>
                    </div>
                    <div class="dev-flex-center-gap-10" >
                        <span class="dev-mono-muted-11" >Rate
                            Limit: 5k/min</span>
                        <button class="vk-btn vk-btn-outline btn-try-sandbox" data-method="GET"
                            data-url="/v1/devices/geodetic/measurements">
                            <span class="material-symbols-outlined text-[14px]">tune</span> Test in Sandbox
                        </button>
                    </div>
                </div>
                <div class="endpoint-body">
                    <div class="endpoint-docs-col">
                        <div class="dev-font-weight-600-font-94c0"
                            >
                            Precision Geodetic Measurement Kit (PROD-1002) Calibration Vectors
                        </div>
                        <p class="dev-font-size-13px-color-78fc" >
                            Provides distance vectors, laser interferometer precision readings, and atmospheric
                            refraction indices for geodetic surveying instrumentation deployed with CUS-1001 (Aral
                            Geomatics) and CUS-1002 (BaltNord).
                        </p>

                        <div class="dev-margin-top-16px-font-1eeb"
                            >
                            Parameters</div>
                        <table class="param-table">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Type</th>
                                    <th>Requirement</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="param-name">unit</td>
                                    <td class="param-type">string</td>
                                    <td><span class="param-required">REQUIRED</span></td>
                                    <td>Hardware device identifier (e.g. <code>PROD-1002</code>)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="endpoint-code-col">
                        <div class="code-tabs-nav">
                            <div class="dev-display-flex-gap-6px-4a5d" >
                                <button class="code-tab-btn active" data-endpoint="endpoint-geodetic"
                                    data-lang="curl">cURL</button>
                                <button class="code-tab-btn" data-endpoint="endpoint-geodetic"
                                    data-lang="python">Python</button>
                                <button class="code-tab-btn" data-endpoint="endpoint-geodetic"
                                    data-lang="node">Node.js</button>
                                <button class="code-tab-btn" data-endpoint="endpoint-geodetic"
                                    data-lang="go">Go</button>
                            </div>
                            <button class="vk-btn-outline copy-code-btn dev-padding-2px-8px-font-bd20">
                                <span class="material-symbols-outlined text-[14px]">content_copy</span> Copy
                            </button>
                        </div>
                        <pre class="code-block-box"><code>curl -X GET "https://developer.vostokpribor.local/v1/devices/geodetic/measurements?unit=PROD-1002" \
  -H "Authorization: Bearer vk_live_9a41c2e8f10b"</code></pre>
                    </div>
                </div>
            </div>

            <!-- ENDPOINT 3: PROD-1004 SCADA Frame Ingestion -->
            <div class="endpoint-card tag-confidential" id="endpoint-scada">
                <div class="endpoint-header">
                    <div class="dev-display-flex-align-items-1c20" >
                        <span class="endpoint-badge-post">POST</span>
                        <span class="endpoint-path">/v1/scada/ingest/frames</span>
                        <span class="badge-classification badge-confidential">Confidential • PROD-1004</span>
                    </div>
                    <div class="dev-flex-center-gap-10" >
                        <span class="dev-mono-muted-11" >Rate
                            Limit: 50k/min</span>
                        <button class="vk-btn vk-btn-outline btn-try-sandbox" data-method="POST"
                            data-url="/v1/scada/ingest/frames"
                            data-body='{"facility_id":"ALMATY-CENTRAL-01","protocol":"MODBUS-TCP","plc_register":"40001","payload_hex":"0A2B4C"}'>
                            <span class="material-symbols-outlined text-[14px]">tune</span> Test in Sandbox
                        </button>
                    </div>
                </div>
                <div class="endpoint-body">
                    <div class="endpoint-docs-col">
                        <div class="dev-font-weight-600-font-94c0"
                            >
                            Industrial PLC Integration (PROD-1004) High-Speed Ingestion
                        </div>
                        <p class="dev-font-size-13px-color-78fc" >
                            Ingests Modbus-TCP, OPC-UA, and telemetry frames directly into System 11 Ingestion Bridges
                            with microsecond timestamp validation.
                        </p>
                        <div class="dev-margin-top-16px-font-1eeb"
                            >
                            Body Parameters (JSON)</div>
                        <table class="param-table">
                            <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Type</th>
                                    <th>Requirement</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="param-name">facility_id</td>
                                    <td class="param-type">string</td>
                                    <td><span class="param-required">REQUIRED</span></td>
                                    <td>Enclave node code (e.g. <code>ALMATY-CENTRAL-01</code>)</td>
                                </tr>
                                <tr>
                                    <td class="param-name">protocol</td>
                                    <td class="param-type">string</td>
                                    <td><span class="param-required">REQUIRED</span></td>
                                    <td><code>MODBUS-TCP</code>, <code>OPC-UA</code>, or <code>PROFINET</code></td>
                                </tr>
                                <tr>
                                    <td class="param-name">payload_hex</td>
                                    <td class="param-type">hex-string</td>
                                    <td><span class="param-required">REQUIRED</span></td>
                                    <td>Raw industrial telemetry frame</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="endpoint-code-col">
                        <div class="code-tabs-nav">
                            <div class="dev-display-flex-gap-6px-4a5d" >
                                <button class="code-tab-btn active" data-endpoint="endpoint-scada"
                                    data-lang="curl">cURL</button>
                                <button class="code-tab-btn" data-endpoint="endpoint-scada"
                                    data-lang="python">Python</button>
                                <button class="code-tab-btn" data-endpoint="endpoint-scada"
                                    data-lang="node">Node.js</button>
                                <button class="code-tab-btn" data-endpoint="endpoint-scada" data-lang="go">Go</button>
                            </div>
                            <button class="vk-btn-outline copy-code-btn dev-padding-2px-8px-font-bd20">
                                <span class="material-symbols-outlined text-[14px]">content_copy</span> Copy
                            </button>
                        </div>
                        <pre class="code-block-box"><code>curl -X POST "https://developer.vostokpribor.local/v1/scada/ingest/frames" \
  -H "Authorization: Bearer vk_live_9a41c2e8f10b" \
  -H "Content-Type: application/json" \
  -d '{
    "facility_id": "ALMATY-CENTRAL-01",
    "protocol": "MODBUS-TCP",
    "plc_register": "40001",
    "payload_hex": "0A2B4C"
  }'</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- PUBLIC/PARTNER ENTERPRISE FOOTER -->
    <footer class="vk-footer">
        <div class="vk-footer-grid">
            <div>
                <div class="dev-display-flex-align-items-6d35" >
                    <div class="vk-brand-logo-badge dev-width-28px-height-28px-5ff9">VP</div>
                    <span class="dev-font-family-var-font-deae"
                        >VOSTOKPRIBOR</span>
                </div>
                <p class="dev-color-94a3b8-font-size-fa90" >
                    Vostokpribor Global Logistics &amp; Supply JSC.<br />
                    Industrial equipment, precision measurement, and engineering automation since 1968. Almaty,
                    Kazakhstan.
                </p>
            </div>
            <div>
                <h4>Developer Enclave</h4>
                <ul>
                    <li><a href="Dashboard.php">REST API Reference</a></li>
                    <li><a href="guides.php">Integration Standards</a></li>
                    <li><a href="credentials.php">Partner API Keys</a></li>
                    <li><a href="sandbox.php">Live Sandbox Console</a></li>
                </ul>
            </div>
            <div>
                <h4>Statutory Links</h4>
                <ul>
                    <li><a href="guides.php#doc010">DOC-2026-010 Specification</a></li>
                    <li><a href="partner-registration.php">Enterprise Partner NDA</a></li>
                    <li><a href="metrics.php">SLA &amp; Gateway Uptime</a></li>
                    <li><a href="partner-registration.php">Security Attestation</a></li>
                </ul>
            </div>
            <div>
                <h4>System Jurisdiction</h4>
                <ul>
                    <li><span class="dev-text-slate-400" >Domain: local.vostokpribor</span></li>
                    <li><span class="dev-text-slate-400" >Gateway: Almaty Station Primary</span></li>
                    <li><span class="vk-status-badge status-active dev-padding-2px-8px-font-90eb">GATEWAY:
                            99.98% SLA</span></li>
                </ul>
            </div>
        </div>
        <div class="vk-footer-bottom">
            <div>© 2026 VOSTOKPRIBOR Global Logistics &amp; Supply JSC. All rights reserved. System 10: Developer &amp;
                API Portal.</div>
            <div>ST RK IEC 62443 Industrial Security • Almaty Core Enclave</div>
        </div>
    </footer>

    <!-- SHARED JS SCRIPTS -->
    <script src="js/dev-common.js"></script>
    <script src="js/dev-portal.js"></script>
</body>

</html>