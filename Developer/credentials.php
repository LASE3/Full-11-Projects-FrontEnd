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
    <title>API Keys &amp; Partner Vault - VOSTOKPRIBOR Developer Portal</title>
    <link rel="stylesheet" href="css/dev-tokens.css" />
    <link rel="stylesheet" href="css/dev-common.css" />
    <link rel="stylesheet" href="css/dev-credentials.css" />
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
                <span class="material-symbols-outlined text-[14px] dev-color-accent">key</span>
                <span class="dev-font-family-var-font-eb0b" >VAULT: <strong>PARTNER ENCLAVE CREDENTIALS</strong></span>
            </div>
        </div>

        <div class="dev-flex-center-gap-16" >
            <div class="dev-search-box-wrap" >
                <span class="material-symbols-outlined text-[16px] dev-position-absolute-left-10px-d4a8">search</span>
                <input class="search-trigger-input" type="text" placeholder="Search keys, tokens (Ctrl + K)" readonly
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
            <a class="vk-nav-item active" href="credentials.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">key</span>
                    <span>API Credentials</span>
                </div>
                <span class="dev-font-family-var-font-829c" >Vault</span>
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
            <div class="dev-font-family-var-font-6447" >Key Rotation Enclave</div>
            <div class="dev-text-muted" >HSM Policy: FIPS 140-3</div>
            <div class="dev-color-var-vk-neutral-1ba7" >90-day automated expiry enforced</div>
        </div>

        <!-- Log Out -->

    </aside>

    <!-- MAIN CONTENT -->
    <main class="vk-app-body">
        <div class="dev-display-flex-justify-content-387e" >
            <div>
                <div class="dev-display-flex-align-items-81c3" >
                    <span class="dev-mono-muted-11" >ROOT / SYSTEM 10 / CREDENTIALS VAULT</span>
                    <span class="badge-classification badge-confidential">Confidential Keyring</span>
                </div>
                <h1 class="dev-font-size-26px-font-2295" >
                    Partner API Keys &amp; Cryptographic Tokens
                </h1>
                <p class="dev-color-var-vk-neutral-5666" >
                    Manage authenticated tokens, environment permissions, and rate-limit quotas for your integration client <strong>BaltNord Process Systems (CUS-1002)</strong>.
                </p>
            </div>
            <button class="vk-btn vk-btn-accent" id="btnOpenGenKey">
                <span class="material-symbols-outlined text-[16px]">add_circle</span> Generate New API Key
            </button>
        </div>

        <!-- Quota Overview Strip -->
        <div class="vk-card tag-confidential dev-margin-bottom-24px-dc2c">
            <div class="vk-card-body dev-display-grid-grid-template-590a">
                <div>
                    <span class="dev-font-family-var-font-d07a" >Active Keys</span>
                    <div class="dev-font-family-var-font-5f4a" >02 Active</div>
                    <div class="dev-font-size-12px-color-926b" >1 Prod • 1 Sandbox</div>
                </div>
                <div>
                    <span class="dev-font-family-var-font-d07a" >Aggregated Rate Quota</span>
                    <div class="dev-font-family-var-font-5f4a" >10,000 / min</div>
                    <div class="dev-font-size-12px-color-f7e3" >Burst allowance: 25,000</div>
                </div>
                <div>
                    <span class="dev-font-family-var-font-d07a" >Security Tier</span>
                    <div class="dev-font-family-var-font-d805" >LEVEL 3</div>
                    <div class="dev-font-size-12px-color-f7e3" >CUS-1002 Verified Partner</div>
                </div>
            </div>
        </div>

        <!-- KEYS DATA TABLE -->
        <div class="vk-table-container">
            <table class="vk-table">
                <thead>
                    <tr>
                        <th>Key Label &amp; Identifier</th>
                        <th>Token Prefix / Secret</th>
                        <th>Environment</th>
                        <th>Rate Limit</th>
                        <th>Classification</th>
                        <th class="dev-text-right" >Action</th>
                    </tr>
                </thead>
                <tbody id="keysTableBody">
                    <!-- Key Row 1 -->
                    <tr class="tag-confidential">
                        <td>
                            <div class="dev-text-primary-bold" >BaltNord Primary ERP Sync</div>
                            <div class="dev-mono-muted-11" >ID: KEY-9842 • PRJ-2026-002</div>
                        </td>
                        <td>
                            <div class="key-token-display">
                                <span>vk_live_9a41c2e8••••••••</span>
                                <button class="vk-btn-outline dev-padding-2px-6px-font-ed80" onclick="window.copyText('vk_live_9a41c2e8f10b7a89d4e12c5', 'Live token copied to clipboard')">
                                    <span class="material-symbols-outlined text-[14px]">content_copy</span>
                                </button>
                            </div>
                        </td>
                        <td>
                            <span class="vk-status-badge status-active">PRODUCTION</span>
                        </td>
                        <td>
                            <div class="dev-font-family-var-font-ef2e" >10,000 req/min</div>
                            <div class="rate-limit-bar-bg">
                                <div class="rate-limit-bar-fill dev-width-38-509e"></div>
                            </div>
                        </td>
                        <td>
                            <span class="badge-classification badge-confidential">Confidential</span>
                        </td>
                        <td class="dev-text-right" >
                            <button class="vk-btn vk-btn-outline btn-revoke-key dev-padding-4px-8px-font-3b27">
                                <span class="material-symbols-outlined text-[14px]">block</span> Revoke
                            </button>
                        </td>
                    </tr>

                    <!-- Key Row 2 -->
                    <tr class="tag-internal">
                        <td>
                            <div class="dev-text-primary-bold" >BaltNord QA / Sandbox Ingestion</div>
                            <div class="dev-mono-muted-11" >ID: KEY-4109 • Testing Pipeline</div>
                        </td>
                        <td>
                            <div class="key-token-display">
                                <span>vk_test_3f7b99c1••••••••</span>
                                <button class="vk-btn-outline dev-padding-2px-6px-font-ed80" onclick="window.copyText('vk_test_3f7b99c1e04a88bc92d110f', 'Test token copied to clipboard')">
                                    <span class="material-symbols-outlined text-[14px]">content_copy</span>
                                </button>
                            </div>
                        </td>
                        <td>
                            <span class="vk-status-badge status-sandbox">SANDBOX</span>
                        </td>
                        <td>
                            <div class="dev-font-family-var-font-ef2e" >2,500 req/min</div>
                            <div class="rate-limit-bar-bg">
                                <div class="rate-limit-bar-fill dev-width-12-background-color-2522"></div>
                            </div>
                        </td>
                        <td>
                            <span class="badge-classification badge-internal">Internal QA</span>
                        </td>
                        <td class="dev-text-right" >
                            <button class="vk-btn vk-btn-outline btn-revoke-key dev-padding-4px-8px-font-3b27">
                                <span class="material-symbols-outlined text-[14px]">block</span> Revoke
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>

    <!-- GENERATE KEY MODAL DIALOG -->
    <div class="vk-modal-overlay" id="genKeyModal" >
        <div class="vk-modal-dialog">
            <div class="dev-background-color-var-vk-ea8e" >
                <div class="dev-flex-center-gap-8" >
                    <span class="material-symbols-outlined text-[20px] dev-color-accent">key</span>
                    <h3 class="dev-font-size-15px-font-29ad" >Generate New Partner API Token</h3>
                </div>
                <button class="dev-background-transparent-border-none-aba8" id="btnCloseGenKey" >
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
            <div class="dev-padding-24px-display-flex-d31a" >
                <div>
                    <label class="dev-font-size-12px-font-8504" >Key Label / Service Name</label>
                    <input class="dev-width-100-height-36px-50c0" id="keyLabelInput" type="text" placeholder="e.g. BaltNord Warehouse PLC Bridge"
                         />
                </div>
                <div>
                    <label class="dev-font-size-12px-font-8504" >Environment Target</label>
                    <div class="dev-display-flex-gap-16px-b636" >
                        <label class="dev-display-flex-align-items-96bf" >
                            <input type="radio" name="keyEnv" value="Production" checked /> Production Enclave
                        </label>
                        <label class="dev-display-flex-align-items-96bf" >
                            <input type="radio" name="keyEnv" value="Sandbox" /> Sandbox Testing
                        </label>
                    </div>
                </div>
                <div>
                    <label class="dev-font-size-12px-font-8504" >Requested Rate Limit</label>
                    <select class="dev-width-100-height-36px-4af4" id="keyRateSelect" >
                        <option value="2,500">2,500 requests / minute (Standard)</option>
                        <option value="10,000" selected>10,000 requests / minute (Enterprise Stream)</option>
                        <option value="50,000">50,000 requests / minute (SCADA High-Frequency Batch)</option>
                    </select>
                </div>
                <div>
                    <label class="dev-font-size-12px-font-8504" >Permission Scopes</label>
                    <div class="dev-display-flex-flex-direction-625a" >
                        <label class="dev-flex-center-gap-8" >
                            <input type="checkbox" checked /> <code>telemetry:read</code> (Optical &amp; Geodetic sensors)
                        </label>
                        <label class="dev-flex-center-gap-8" >
                            <input type="checkbox" checked /> <code>scada:ingest</code> (PLC high-speed frames)
                        </label>
                        <label class="dev-flex-center-gap-8" >
                            <input type="checkbox" /> <code>orders:write</code> (B2B procurement pipeline)
                        </label>
                    </div>
                </div>
            </div>
            <div class="dev-padding-16px-20px-background-a721" >
                <button class="vk-btn vk-btn-outline" id="btnCancelGenKey">Cancel</button>
                <button class="vk-btn vk-btn-accent" id="btnSubmitGenKey">
                    <span class="material-symbols-outlined text-[16px]">vpn_key</span> Mint Key
                </button>
            </div>
        </div>
    </div>

        <footer class="vk-footer">
        <div class="vk-footer-bottom dev-border-top-none-padding-efbd">
            <div>© 2026 VOSTOKPRIBOR Global Logistics &amp; Supply JSC. System 10: Developer &amp; API Portal.</div>
            <div>Vault Security: FIPS 140-3 Hardware Token Anchored • Almaty Enclave</div>
        </div>
    </footer>

    <script src="js/dev-common.js"></script>
    <script src="js/dev-credentials.js"></script>
</body>

</html>