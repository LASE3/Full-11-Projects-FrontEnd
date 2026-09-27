<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('Developer');
$pdo = getDbConnection();
require_once __DIR__ . '/api/db_helper.php';
ensureDeveloperTables($pdo);
$currUser = $_SESSION['vostok_user'] ?? ['full_name' => 'Authorized User', 'clearance_level' => 'L2'];

// Fetch all dynamic endpoints from database
$stmt = $pdo->query("SELECT * FROM `developer_endpoints` ORDER BY `id` ASC");
$endpoints = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <button class="vk-btn vk-btn-accent" id="btnOpenCreateEndpoint">
                    <span class="material-symbols-outlined text-[16px]">add_circle</span> Register New Endpoint
                </button>
                <a class="vk-btn vk-btn-outline" href="guides.php">
                    <span class="material-symbols-outlined text-[16px]">menu_book</span> Quickstart Guide
                </a>
                <a class="vk-btn vk-btn-outline" href="sandbox.php">
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

        <!-- ENDPOINTS SECTION (100% Dynamic from Database) -->
        <div class="endpoint-container" id="endpointCardsContainer">
            <?php if (empty($endpoints)): ?>
                <div class="vk-card tag-internal" style="padding: 40px; text-align: center;">
                    <span class="material-symbols-outlined" style="font-size: 48px; color: var(--vk-neutral-400);">terminal</span>
                    <h3 style="margin-top: 12px; color: var(--vk-neutral-800);">No API Endpoints Registered</h3>
                    <p style="color: var(--vk-neutral-500); margin-top: 6px;">Register your first industrial endpoint to populate developer documentation.</p>
                    <button class="vk-btn vk-btn-accent" style="margin-top: 16px;" onclick="document.getElementById('endpointModal').style.display='flex'">
                        <span class="material-symbols-outlined text-[16px]">add_circle</span> Register Endpoint
                    </button>
                </div>
            <?php else: ?>
                <?php foreach ($endpoints as $ep): 
                    $methodClass = 'endpoint-badge-' . strtolower($ep['method']);
                    $cardClass = str_contains(strtolower($ep['classification']), 'confidential') ? 'tag-confidential' : 'tag-internal';
                    $badgeClass = str_contains(strtolower($ep['classification']), 'confidential') ? 'badge-confidential' : 'badge-internal';
                    $params = !empty($ep['parameters_json']) ? json_decode($ep['parameters_json'], true) : [];
                    $epJson = htmlspecialchars(json_encode($ep), ENT_QUOTES, 'UTF-8');
                ?>
                <div class="endpoint-card <?= $cardClass ?>" id="<?= htmlspecialchars($ep['endpoint_slug']) ?>" data-endpoint-id="<?= $ep['id'] ?>">
                    <div class="endpoint-header">
                        <div class="dev-display-flex-align-items-1c20" style="gap: 10px;">
                            <span class="<?= $methodClass ?>"><?= htmlspecialchars($ep['method']) ?></span>
                            <span class="endpoint-path"><?= htmlspecialchars($ep['path']) ?></span>
                            <span class="badge-classification <?= $badgeClass ?>"><?= htmlspecialchars($ep['classification']) ?></span>
                        </div>
                        <div class="dev-flex-center-gap-10" style="gap: 8px;">
                            <span class="dev-mono-muted-11">Rate Limit: <?= htmlspecialchars($ep['rate_limit']) ?></span>
                            
                            <button class="vk-btn vk-btn-outline btn-try-sandbox" 
                                data-method="<?= htmlspecialchars($ep['method']) ?>"
                                data-url="<?= htmlspecialchars($ep['path']) ?>"
                                title="Run this endpoint in the interactive test sandbox">
                                <span class="material-symbols-outlined text-[14px]">tune</span> Test
                            </button>

                            <button class="btn-crud-action btn-crud-edit btn-edit-endpoint" 
                                data-endpoint='<?= $epJson ?>'
                                title="Edit this endpoint specification">
                                <span class="material-symbols-outlined text-[14px]">edit</span> Edit
                            </button>

                            <button class="btn-crud-action btn-crud-delete btn-delete-endpoint" 
                                data-id="<?= $ep['id'] ?>"
                                data-title="<?= htmlspecialchars($ep['title']) ?>"
                                title="Remove this endpoint from database">
                                <span class="material-symbols-outlined text-[14px]">delete</span>
                            </button>
                        </div>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-docs-col">
                            <div class="dev-font-weight-600-font-94c0">
                                <?= htmlspecialchars($ep['title']) ?>
                            </div>
                            <p class="dev-font-size-13px-color-78fc">
                                <?= nl2br(htmlspecialchars($ep['description'])) ?>
                            </p>

                            <?php if (!empty($params)): ?>
                            <div class="dev-margin-top-16px-font-dc1f">Parameters</div>
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
                                    <?php foreach ($params as $param): ?>
                                    <tr>
                                        <td class="param-name"><?= htmlspecialchars($param['name'] ?? '') ?></td>
                                        <td class="param-type"><?= htmlspecialchars($param['type'] ?? 'string') ?></td>
                                        <td>
                                            <?php if (!empty($param['required'])): ?>
                                                <span class="param-required">REQUIRED</span>
                                            <?php else: ?>
                                                <span class="dev-font-size-10px-color-f312">Optional</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($param['description'] ?? '') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php endif; ?>
                        </div>
                        <div class="endpoint-code-col">
                            <div class="code-tabs-nav">
                                <div class="dev-display-flex-gap-6px-4a5d">
                                    <button class="code-tab-btn active" data-endpoint="<?= htmlspecialchars($ep['endpoint_slug']) ?>" data-lang="curl">cURL</button>
                                    <button class="code-tab-btn" data-endpoint="<?= htmlspecialchars($ep['endpoint_slug']) ?>" data-lang="python">Python</button>
                                    <button class="code-tab-btn" data-endpoint="<?= htmlspecialchars($ep['endpoint_slug']) ?>" data-lang="node">Node.js</button>
                                    <button class="code-tab-btn" data-endpoint="<?= htmlspecialchars($ep['endpoint_slug']) ?>" data-lang="go">Go</button>
                                </div>
                                <button class="vk-btn-outline copy-code-btn dev-padding-2px-8px-font-bd20">
                                    <span class="material-symbols-outlined text-[14px]">content_copy</span> Copy
                                </button>
                            </div>
                            <pre class="code-block-box"><code id="code-<?= htmlspecialchars($ep['endpoint_slug']) ?>"><?= htmlspecialchars($ep['curl_snippet'] ?: '') ?></code></pre>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- REGISTER / EDIT ENDPOINT MODAL (Universal Database CRUD) -->
    <div class="vk-modal-overlay" id="endpointModal">
        <div class="vk-modal-dialog" style="max-width: 680px;">
            <div class="vk-modal-header">
                <div class="dev-flex-center-gap-8">
                    <span class="material-symbols-outlined text-[20px] dev-color-accent">terminal</span>
                    <h3 id="endpointModalTitle" class="dev-font-size-15px-font-29ad">Register New API Endpoint</h3>
                </div>
                <button class="dev-background-transparent-border-none-aba8" id="btnCloseEndpointModal" style="color: #94a3b8; cursor: pointer;">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
            <form id="endpointForm" class="vk-modal-body">
                <input type="hidden" id="epId" name="id" value="" />
                <input type="hidden" id="epSlug" name="endpoint_slug" value="" />

                <div class="crud-form-group">
                    <label class="crud-form-label" for="epTitle">Endpoint Title / Equipment Name *</label>
                    <input type="text" id="epTitle" class="crud-form-input" placeholder="e.g. PROD-1005 Laser Vibrometer High-Speed Stream" required />
                </div>

                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 12px;">
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="epMethod">HTTP Method *</label>
                        <select id="epMethod" class="crud-form-select">
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="epPath">URI Path *</label>
                        <input type="text" id="epPath" class="crud-form-input" placeholder="/v1/sensors/vibrometer/stream" required />
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="epClassification">Classification</label>
                        <select id="epClassification" class="crud-form-select">
                            <option value="Internal • PROD-1001">Internal</option>
                            <option value="Confidential • PROD-1004">Confidential</option>
                            <option value="Public API">Public API</option>
                            <option value="Restricted • L3">Restricted L3</option>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="epRateLimit">Rate Limit</label>
                        <input type="text" id="epRateLimit" class="crud-form-input" value="10k/min" placeholder="10k/min" />
                    </div>
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="epTargetHardware">Target Hardware</label>
                        <input type="text" id="epTargetHardware" class="crud-form-input" value="PROD-1001" placeholder="PROD-1001" />
                    </div>
                </div>

                <div class="crud-form-group">
                    <label class="crud-form-label" for="epDescription">Specification Description *</label>
                    <textarea id="epDescription" class="crud-form-textarea" placeholder="Detailed description of telemetric attributes, operational ranges, and safety boundaries..." required></textarea>
                </div>

                <div class="crud-form-group">
                    <label class="crud-form-label" for="epParamsJson">Query / Body Parameters (JSON Array format)</label>
                    <textarea id="epParamsJson" class="crud-form-textarea" style="font-family: var(--font-mono); font-size: 11px;" placeholder='[{"name":"device_id","type":"string","required":true,"description":"Hardware Serial ID"}]'></textarea>
                </div>

                <div class="crud-form-group">
                    <label class="crud-form-label" for="epCurl">cURL Code Snippet (Optional - Auto-generated if left blank)</label>
                    <textarea id="epCurl" class="crud-form-textarea" style="font-family: var(--font-mono); font-size: 11px;" placeholder="curl -X GET ..."></textarea>
                </div>
            </form>
            <div class="vk-modal-footer">
                <button type="button" class="vk-btn vk-btn-outline" id="btnCancelEndpointModal">Cancel</button>
                <button type="button" class="vk-btn vk-btn-accent" id="btnSaveEndpoint">
                    <span class="material-symbols-outlined text-[16px]">save</span> Save to Database
                </button>
            </div>
        </div>
    </div>

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