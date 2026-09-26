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
    <title>Integration Guides &amp; Standards - VOSTOKPRIBOR Developer Portal</title>
    <link rel="stylesheet" href="css/dev-tokens.css" />
    <link rel="stylesheet" href="css/dev-common.css" />
    <link rel="stylesheet" href="css/dev-portal.css" />
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
                <span class="material-symbols-outlined text-[14px] dev-color-accent">architecture</span>
                <span class="dev-font-family-var-font-eb0b" >DOCS: <strong>DOC-2026-010 Specification</strong></span>
            </div>
        </div>

        <div class="dev-flex-center-gap-16" >
            <div class="dev-search-box-wrap" >
                <span class="material-symbols-outlined text-[16px] dev-position-absolute-left-10px-d4a8">search</span>
                <input class="search-trigger-input" type="text" placeholder="Search guides, RFCs, protocols (Ctrl + K)" readonly
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
            <a class="vk-nav-item active" href="guides.php">
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
            <div class="dev-font-family-var-font-6447" >Integration Engineering</div>
            <div class="dev-color-var-vk-primary-1a7b" >Dana Yermak (EMP-1017)</div>
            <div class="dev-color-var-vk-neutral-19bc" >Integration Engineer • ENG</div>
            <div class="dev-color-var-vk-primary-57e8" >Jonas Richter (EMP-1020)</div>
            <div class="dev-color-var-vk-neutral-19bc" >Lead Developer • ENG</div>
        </div>

        <!-- Log Out -->

    </aside>

    <!-- MAIN CONTENT -->
    <main class="vk-app-body">
        <div class="dev-margin-bottom-24px-dc2c" >
            <div class="dev-display-flex-align-items-81c3" >
                <span class="dev-mono-muted-11" >ROOT / SYSTEM 10 / GUIDES &amp; PROTOCOLS</span>
                <span class="badge-classification badge-internal">Internal Specification</span>
            </div>
            <h1 class="dev-font-size-26px-font-2295" >
                Enterprise Integration Guides &amp; Protocols
            </h1>
            <p class="dev-color-var-vk-neutral-1aeb" >
                Architectural blueprints and statutory integration standards for connecting client ERP networks, SCADA controllers, and logistics feeds with VOSTOKPRIBOR.
            </p>
        </div>

        <!-- STATUTORY DOCUMENT HERO CARD (DOC-2026-010) -->
        <div class="vk-card tag-internal" id="doc010" >
            <div class="vk-card-header dev-background-color-f8fafc-fb8d">
                <div class="dev-display-flex-align-items-1c20" >
                    <span class="material-symbols-outlined text-[22px] dev-color-var-vk-class-5979">description</span>
                    <div>
                        <div class="dev-font-weight-700-font-d74c" >DOC-2026-010: API_Integration_Guide.pdf</div>
                        <div class="dev-mono-muted-11" >Authoritative Baseline Specification • System 10 Reference Document</div>
                    </div>
                </div>
                <div class="dev-display-flex-gap-8px-1326" >
                    <span class="badge-classification badge-internal">INTERNAL USE</span>
                    <button class="vk-btn vk-btn-outline dev-padding-4px-10px-font-9ec7" onclick="window.showToast('SPEC DOWNLOAD', 'Exported statutory document DOC-2026-010.pdf', 'success')">
                        <span class="material-symbols-outlined text-[14px]">file_download</span> Download PDF
                    </button>
                </div>
            </div>
            <div class="vk-card-body dev-line-height-1-6-80c9">
                <div class="dev-display-grid-grid-template-0fd8" >
                    <div>
                        <h4 class="dev-font-size-13px-font-9d6e" >Document Scope</h4>
                        <p class="dev-text-muted" >
                            Defines cryptographic and telemetry contracts between client ERP systems (e.g. CUS-1002 BaltNord) and System 11 Admin &amp; Governance pipelines. Mandates HMAC SHA-256 signatures on all webhooks.
                        </p>
                    </div>
                    <div>
                        <h4 class="dev-font-size-13px-font-9d6e" >Signatory Verification</h4>
                        <p class="dev-text-muted" >
                            Managed by Lead Developer Jonas Richter (EMP-1020) and Software Integration Engineer Dana Yermak (EMP-1017). Attested under ISO 27001 &amp; ST RK IEC 62443.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- GUIDE SECTIONS -->
        <div class="dev-display-flex-flex-direction-e9a4" >
            <!-- Guide 1: Authentication -->
            <div class="vk-card tag-confidential">
                <div class="vk-card-header">
                    <div class="dev-flex-center-gap-10" >
                        <span class="material-symbols-outlined text-[20px] dev-color-var-vk-class-7bf4">lock</span>
                        <h3 class="dev-font-size-15px-font-578e" >1. Authentication &amp; Token Scoping</h3>
                    </div>
                    <span class="badge-classification badge-confidential">Confidential</span>
                </div>
                <div class="vk-card-body">
                    <p class="dev-color-var-vk-neutral-5df1" >
                        All API requests require an HTTP Authorization header containing a bearer token issued through the Partner Credentials Vault:
                    </p>
                    <div class="dev-background-color-0a1624-padding-e1fb" >
                        Authorization: Bearer vk_live_9a41c2e8f10b7a89d4e12c5
                    </div>
                    <p class="dev-color-var-vk-neutral-b40a" >
                        Production keys expire every 90 days. Systems automatically reject tokens lacking valid IP whitelisting configured in the Credentials Vault.
                    </p>
                </div>
            </div>

            <!-- Guide 2: ERP Integration -->
            <div class="vk-card tag-internal" id="erp">
                <div class="vk-card-header">
                    <div class="dev-flex-center-gap-10" >
                        <span class="material-symbols-outlined text-[20px] dev-color-var-vk-class-5979">sync_alt</span>
                        <h3 class="dev-font-size-15px-font-578e" >2. ERP Integration Standard (SAP / 1C:Enterprise / Dynamics)</h3>
                    </div>
                    <span class="badge-classification badge-internal">Internal Standard</span>
                </div>
                <div class="vk-card-body">
                    <p class="dev-color-var-vk-neutral-e351" >
                        To sync purchase orders, equipment fulfillment stages, and billing events directly with your corporate accounting software:
                    </p>
                    <ol class="dev-padding-left-20px-font-b6a3" >
                        <li><strong>Register Webhook Target:</strong> Provide your HTTPS endpoint in the Webhooks console with TLS 1.3 encryption.</li>
                        <li><strong>Order Matching:</strong> Align commercial opportunities from System 05 (CRM) with Order IDs in System 02 (E-Commerce).</li>
                        <li><strong>Invoice Reconciliation:</strong> Track payments against System 07 (Finance &amp; Billing) reference numbers (e.g. <code>INV-2026-002</code>).</li>
                    </ol>
                </div>
            </div>

            <!-- Guide 3: SCADA Ingestion -->
            <div class="vk-card tag-public">
                <div class="vk-card-header">
                    <div class="dev-flex-center-gap-10" >
                        <span class="material-symbols-outlined text-[20px] dev-color-secondary">sensors</span>
                        <h3 class="dev-font-size-15px-font-578e" >3. SCADA Real-Time Telemetry Pipeline (Modbus &amp; OPC-UA)</h3>
                    </div>
                    <span class="badge-classification badge-public">Public Spec</span>
                </div>
                <div class="vk-card-body">
                    <p class="dev-color-var-vk-neutral-5df1" >
                        Industrial equipment deployed with customer facilities transmits operational frames at up to 64,800 events per second. Use the high-speed batch endpoint <code>/v1/scada/ingest/frames</code> or connect directly to the Almaty WebSocket stream:
                    </p>
                    <div class="dev-background-color-0a1624-padding-1ab7" >
                        wss://developer.vostokpribor.local/v1/stream/scada/feed?facility=ALMATY-01
                    </div>
                </div>
            </div>
        </div>
    </main>

        <footer class="vk-footer">
        <div class="vk-footer-bottom dev-border-top-none-padding-efbd">
            <div>© 2026 VOSTOKPRIBOR Global Logistics &amp; Supply JSC. System 10: Developer &amp; API Portal.</div>
            <div>DOC-2026-010 Specification • Almaty Enclave Engineering</div>
        </div>
    </footer>

    <script src="js/dev-common.js"></script>
    <script src="js/dev-portal.js"></script>
</body>

</html>