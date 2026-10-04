<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('DEV');
$pdo = getDbConnection();
require_once __DIR__ . '/api/db_helper.php';
ensureDeveloperTables($pdo);
$currUser = $_SESSION['vostok_user'] ?? ['full_name' => 'Authorized User', 'clearance_level' => 'L2'];

// Fetch all partner clearance applications dynamically from database
$applications = [];
try {
    $stmt = $pdo->query("SELECT * FROM developer_partner_applications ORDER BY id DESC");
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $applications = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Enterprise Partner Registration - VOSTOKPRIBOR Developer Portal</title>
    <link rel="stylesheet" href="css/dev-tokens.css" />
    <link rel="stylesheet" href="css/dev-common.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="css/dev-registration.css" />
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
                <span class="material-symbols-outlined text-[14px] dev-color-accent">how_to_reg</span>
                <span class="dev-font-family-var-font-eb0b" >ONBOARDING: <strong>PARTNER ENCLAVE CLEARANCE</strong></span>
            </div>
        </div>

        <div class="dev-flex-center-gap-16" >
<button class="icon-button notifications-btn" id="notifications-toggle-btn" title="Live Enterprise Notifications" style="background:transparent;border:none;color:#bdc6cf;cursor:pointer;padding:6px;display:flex;align-items:center;justify-content:center;position:relative;">
                <span class="material-symbols-outlined text-[20px]">notifications</span>
            </button>
            <div class="dev-search-box-wrap" >
                <span class="material-symbols-outlined text-[16px] dev-position-absolute-left-10px-d4a8">search</span>
                <input class="search-trigger-input" type="text" placeholder="Search guides, forms (Ctrl + K)" readonly />
            </div>
            <div class="dev-display-flex-align-items-9eca" >
                <span class="material-symbols-outlined text-[14px] dev-color-secondary">schedule</span>
                <span class="station-live-clock">17:28:00 UTC+6</span>
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
            <a href="../api/logout.php?system=Developer&redirect=../Developer/login.php" class="top-signout-btn" title="Sign Out of Developer" onclick="(function(){sessionStorage.clear();localStorage.clear();})()"><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
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
            <a class="vk-nav-item" href="metrics.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">monitoring</span>
                    <span>Usage &amp; Telemetry</span>
                </div>
            </a>
            <a class="vk-nav-item active" href="partner-registration.php">
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
                <span class="dev-font-family-var-font-1ab9" >REGISTRATION ENCLAVE</span>
            </div>
            <div class="dev-mono-muted-11" >Auto-Vetting: Active (Level 2)</div>
            <div class="dev-font-family-var-font-940f" >Approval SLA: &lt; 24 Hours</div>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="vk-main-layout">
        <div class="reg-form-container">
            <!-- HEADER BLOCK -->
            <div class="dev-display-flex-justify-content-f610" >
                <div>
                    <div class="dev-display-flex-align-items-9bb7" >
                        <span class="vk-tag vk-tag-internal">CLASSIFICATION: INTERNAL // #3E7CB1</span>
                        <span class="dev-font-family-var-font-133f" >FORM: PARTNER-ONBOARD-v2.4</span>
                    </div>
                    <h1 class="dev-font-size-26px-font-5041" >
                        <span class="material-symbols-outlined dev-font-size-28px-color-a4d3">how_to_reg</span>
                        Enterprise Partner &amp; Client Onboarding
                    </h1>
                    <p class="dev-color-var-vk-neutral-5a07" >
                        Apply for automated SCADA gateway API access, issue client certificates, and establish telemetry bridge enclaves with VOSTOKPRIBOR.
                    </p>
                </div>
                <div>
                    <button class="vk-btn vk-btn-outline" id="btn-autofill-baltnord" type="button" >
                        <span class="material-symbols-outlined text-[16px]">dataset</span> Autofill BaltNord Spec (CUS-1002)
                    </button>
                </div>
            </div>

            <!-- REGISTRATION FORM -->
            <form id="partner-registration-form" class="vk-card dev-margin-bottom-30px-9550">
                <div class="vk-card-header">
                    <div>
                        <div class="vk-card-title">Clearance Application Form</div>
                        <div class="vk-card-subtitle">All submitted credentials are encrypted via hardware HSM modules in Almaty Central Datacenter</div>
                    </div>
                    <span class="vk-tag dev-background-rgba-14-124-0f4c">
                        IEC 62443 COMPLIANT
                    </span>
                </div>

                <div class="vk-card-body">
                    <!-- SECTION 1: ORGANIZATION & CONTACT -->
                    <div class="dev-mb-20" >
                        <h3 class="dev-font-size-14px-font-5262" >
                            <span class="material-symbols-outlined text-[18px] dev-color-accent">corporate_fare</span>
                            1. Organization &amp; Integration Contact
                        </h3>

                        <div class="form-grid-2col">
                            <div class="form-group">
                                <label class="form-label" for="reg-company">
                                    Organization / Legal Entity <span class="form-label-required">*</span>
                                </label>
                                <input class="form-input" id="reg-company" type="text" placeholder="e.g. BaltNord Process Systems" required />
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="reg-partner-id">
                                    Customer / Partner ID (if known)
                                </label>
                                <input class="form-input" id="reg-partner-id" type="text" placeholder="e.g. CUS-1002 (Leave blank if new partner)" />
                            </div>
                        </div>

                        <div class="form-grid-2col">
                            <div class="form-group">
                                <label class="form-label" for="reg-contact-name">
                                    Lead Technical Contact <span class="form-label-required">*</span>
                                </label>
                                <input class="form-input" id="reg-contact-name" type="text" placeholder="e.g. Kristaps Ozols" required />
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="reg-contact-email">
                                    Engineering Work Email <span class="form-label-required">*</span>
                                </label>
                                <input class="form-input" id="reg-contact-email" type="email" placeholder="e.g. kristaps.ozols@baltnord.lv" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="reg-project-ref">
                                Project Reference / Integration Scope
                            </label>
                            <input class="form-input" id="reg-project-ref" type="text" placeholder="e.g. PRJ-2026-002 (Refinery Flow Monitoring & SCADA Bridge)" />
                        </div>
                    </div>

                    <div class="dev-border-top-1px-solid-dae1" ></div>

                    <!-- SECTION 2: ACCESS SCOPE & ENCLAVE -->
                    <div class="dev-mb-20" >
                        <h3 class="dev-font-size-14px-font-5262" >
                            <span class="material-symbols-outlined text-[18px] dev-color-accent">tune</span>
                            2. Target Environment &amp; Requested Scopes
                        </h3>

                        <div class="form-group">
                            <label class="form-label" for="reg-env">
                                Target Enclave Environment <span class="form-label-required">*</span>
                            </label>
                            <select class="form-select" id="reg-env" required>
                                <option value="sandbox">Sandbox Enclave (Mock SCADA telemetry &amp; simulated responses)</option>
                                <option value="staging">Staging Gateway (Pre-production testing with physical hardware)</option>
                                <option value="production">Production Gateway (Requires L3 Security Officer Attestation)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Requested API Permissions (Scopes) <span class="form-label-required">*</span>
                            </label>
                            <div class="scope-checkbox-grid">
                                <label class="scope-card-option selected">
                                    <input class="dev-mt-2" type="checkbox" value="telemetry:read" checked  />
                                    <div>
                                        <div class="dev-font-weight-600-font-5c2a" >telemetry:read</div>
                                        <div class="dev-font-size-11px-color-3171" >
                                             access to sensor streams (VP-1001, VP-1002, VP-1004).
                                        </div>
                                    </div>
                                </label>

                                <label class="scope-card-option selected">
                                    <input class="dev-mt-2" type="checkbox" value="orders:read_write" checked  />
                                    <div>
                                        <div class="dev-font-weight-600-font-5c2a" >orders:read_write</div>
                                        <div class="dev-font-size-11px-color-3171" >
                                            B2B spare parts orders, stock queries, and dispatch tracking.
                                        </div>
                                    </div>
                                </label>

                                <label class="scope-card-option">
                                    <input class="dev-mt-2" type="checkbox" value="actuator:write"  />
                                    <div>
                                        <div class="dev-font-weight-600-font-5ddc" >actuator:write (RESTRICTED)</div>
                                        <div class="dev-font-size-11px-color-3171" >
                                            Send control signals to industrial valves and actuators. Requires IEC 62443 L3 clearance.
                                        </div>
                                    </div>
                                </label>

                                <label class="scope-card-option">
                                    <input class="dev-mt-2" type="checkbox" value="audit:read"  />
                                    <div>
                                        <div class="dev-font-weight-600-font-5c2a" >audit:read</div>
                                        <div class="dev-font-size-11px-color-3171" >
                                            Stream security audit logs and gateway transaction history.
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="dev-border-top-1px-solid-dae1" ></div>

                    <!-- SECTION 3: PUBLIC KEY / MTLS IDENTITY -->
                    <div class="dev-mb-20" >
                        <h3 class="dev-font-size-14px-font-5262" >
                            <span class="material-symbols-outlined text-[18px] dev-color-accent">key</span>
                            3. Cryptographic Identity (Public Key / CSR)
                        </h3>

                        <div class="form-group">
                            <label class="form-label" for="reg-public-key">
                                Ed25519 Public Key or PEM Certificate Request (Optional for Sandbox)
                            </label>
                            <textarea class="form-textarea" id="reg-public-key" rows="3" placeholder="ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAA... or -----BEGIN CERTIFICATE REQUEST-----" ></textarea>
                        </div>
                    </div>

                    <div class="dev-border-top-1px-solid-dae1" ></div>

                    <!-- SECTION 4: COMPLIANCE & ATTESTATION -->
                    <div class="dev-margin-bottom-24px-dc2c" >
                        <h3 class="dev-font-size-14px-font-5262" >
                            <span class="material-symbols-outlined text-[18px] dev-color-accent">gavel</span>
                            4. Regulatory Attestation &amp; Security Compliance
                        </h3>

                        <div class="legal-box">
                            <strong>VOSTOKPRIBOR API ACCESS &amp; TELEMETRY INTERCHANGE AGREEMENT:</strong><br />
                            1. Access to the VOSTOKPRIBOR Enterprise API Gateway is subject to the security provisions defined in Statutory Document DOC-2026-010 (API_Integration_Guide.pdf).<br />
                            2. Partner agrees to store API secret tokens and mTLS private keys exclusively within compliant HSM or encrypted key storage enclaves. Hardcoding keys in client-side code is strictly prohibited.<br />
                            3. Telemetry streams originating from industrial controllers (VP-1001, VP-1002, VP-1004) are subject to Kazakhstani Industrial Cybersecurity Regulations and IEC 62443-4-2 standards.<br />
                            4. VOSTOKPRIBOR reserves the authority to revoke API tokens immediately upon detecting anomalous polling frequencies or unauthorized actuator command dispatches.
                        </div>

                        <div class="dev-display-flex-flex-direction-e4b1" >
                            <label class="dev-display-flex-align-items-ab55" >
                                <input id="reg-compliance-doc" type="checkbox" required />
                                <span>I have read and agree to comply with <strong>DOC-2026-010 (API Integration Guide)</strong>.</span>
                            </label>
                            <label class="dev-display-flex-align-items-ab55" >
                                <input id="reg-compliance-iec" type="checkbox" required />
                                <span>We affirm that all connecting integration bridges meet <strong>IEC 62443-4-2</strong> cybersecurity levels.</span>
                            </label>
                            <label class="dev-display-flex-align-items-ab55" >
                                <input id="reg-compliance-nda" type="checkbox" required />
                                <span>We accept the bilateral Non-Disclosure Agreement under Almaty Central Jurisdiction.</span>
                            </label>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <div class="dev-display-flex-justify-content-ef47" >
                        <button class="vk-btn vk-btn-outline" type="reset">Reset Form</button>
                        <button class="vk-btn vk-btn-primary" type="submit" id="btnSubmitRegistration">
                            <span class="material-symbols-outlined text-[18px]">verified_user</span>
                            Submit Application for Security Vetting
                        </button>
                    </div>
                </div>
            </form>

            <!-- REGISTRATION SUCCESS CONTAINER (Shown on submit) -->
            <div id="reg-success-container" class="vk-card dev-display-none-margin-bottom-366d">
                <div class="vk-card-header dev-background-rgba-14-124-0a52">
                    <div class="dev-display-flex-align-items-1c20" >
                        <span class="material-symbols-outlined text-[28px] dev-color-secondary">check_circle</span>
                        <div>
                            <div class="vk-card-title">Clearance Request Successfully Enqueued</div>
                            <div class="vk-card-subtitle">Your onboarding application has been transmitted to VOSTOKPRIBOR Systems Engineering</div>
                        </div>
                    </div>
                    <span class="vk-tag vk-tag-internal">STATUS: IN_REVIEW</span>
                </div>

                <div class="vk-card-body">
                    <div class="dev-display-grid-grid-template-17e2" >
                        <div>
                            <div class="dev-font-size-11px-font-b2cd" >TICKET ID</div>
                            <div class="dev-font-family-var-font-cf89" id="disp-reg-ticket" >ENCLAVE-REQ-981244</div>
                        </div>
                        <div>
                            <div class="dev-font-size-11px-font-b2cd" >ORGANIZATION</div>
                            <div class="dev-font-weight-600-font-d257" id="disp-reg-company" >BaltNord Process Systems</div>
                        </div>
                        <div>
                            <div class="dev-font-size-11px-font-b2cd" >CONTACT</div>
                            <div class="dev-font-size-13px-color-fb5d" id="disp-reg-contact" >Kristaps Ozols</div>
                        </div>
                        <div>
                            <div class="dev-font-size-11px-font-b2cd" >TARGET ENCLAVE</div>
                            <div class="dev-font-family-var-font-b2cb" id="disp-reg-env" >SANDBOX</div>
                        </div>
                    </div>

                    <p class="dev-font-size-13px-color-7e03" >
                        A security engineer from the Developer &amp; API Portal team (Lead: <strong>Jonas Richter EMP-1020</strong>; Integration: <strong>Dana Yermak EMP-1017</strong>) will inspect your certificate request and provision the initial Sandbox API credentials. You will receive an automated dispatch notification upon clearance.
                    </p>

                    <div class="dev-display-flex-gap-12px-4896" >
                        <button class="vk-btn vk-btn-outline" id="btnRegisterAnother" type="button">
                            <span class="material-symbols-outlined text-[16px]">add_box</span> Submit Another Application
                        </button>
                        <a class="vk-btn vk-btn-primary" href="credentials.php">
                            <span class="material-symbols-outlined text-[16px]">key</span> Go to API Credentials Vault
                        </a>
                        <a class="vk-btn vk-btn-outline" href="sandbox.php">
                            <span class="material-symbols-outlined text-[16px]">terminal</span> Test Endpoints in Sandbox
                        </a>
                    </div>
                </div>
            </div>

            <!-- ENCLAVE CLEARANCE APPLICATIONS MANAGEMENT LEDGER (Full Database CRUD) -->
            <div class="vk-card dev-margin-bottom-30px-9550">
                <div class="vk-card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div class="vk-card-title">Enclave Clearance Applications Ledger</div>
                        <div class="vk-card-subtitle">Real-time status of all submitted partner onboarding clearance requests (Database: `developer_partner_applications`)</div>
                    </div>
                    <span class="vk-tag vk-tag-internal">TOTAL: <?= count($applications) ?> APPLICATIONS</span>
                </div>
                <div class="vk-card-body dev-padding-0-b662">
                    <table class="vk-table">
                        <thead>
                            <tr>
                                <th class="dev-width-150px-c251">Ticket ID</th>
                                <th>Organization</th>
                                <th>Contact / Email</th>
                                <th class="dev-width-110px-459f">Environment</th>
                                <th>Requested Scopes</th>
                                <th class="dev-width-120px-e314">Status</th>
                                <th class="dev-width-150px-text-align-2833" style="text-align: right;">Vetting Actions</th>
                            </tr>
                        </thead>
                        <tbody id="applicationsTableBody">
                            <?php if (empty($applications)): ?>
                            <tr><td colspan="7" style="text-align: center; color: var(--vk-neutral-500); padding: 24px;">No partner clearance applications recorded yet.</td></tr>
                            <?php else: ?>
                            <?php foreach ($applications as $app): 
                                $status = $app['status'];
                                $statusClass = 'vk-tag-internal';
                                if ($status === 'Approved' || $status === 'Active') $statusClass = 'vk-tag-secondary';
                                elseif ($status === 'Rejected') $statusClass = 'vk-tag-alert';
                                $appJson = htmlspecialchars(json_encode($app), ENT_QUOTES, 'UTF-8');
                            ?>
                            <tr id="app-row-<?= $app['id'] ?>">
                                <td>
                                    <code><?= htmlspecialchars($app['ticket_id']) ?></code>
                                    <?php if (!empty($app['partner_id'])): ?>
                                    <div class="dev-mono-muted-11"><?= htmlspecialchars($app['partner_id']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($app['company_name']) ?></strong>
                                    <?php if (!empty($app['project_ref'])): ?>
                                    <div class="dev-font-size-11px-color-3171"><?= htmlspecialchars($app['project_ref']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div><?= htmlspecialchars($app['contact_name']) ?></div>
                                    <div class="dev-mono-muted-11"><?= htmlspecialchars($app['contact_email']) ?></div>
                                </td>
                                <td>
                                    <span class="vk-tag dev-text-10"><?= strtoupper(htmlspecialchars($app['target_environment'])) ?></span>
                                </td>
                                <td>
                                    <div class="dev-font-family-var-font-e036" style="max-width: 200px; white-space: normal; line-height: 1.3;">
                                        <?= htmlspecialchars($app['requested_scopes']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="vk-tag <?= $statusClass ?>" id="app-status-badge-<?= $app['id'] ?>">
                                        <?= htmlspecialchars($status) ?>
                                    </span>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <?php if ($status !== 'Approved'): ?>
                                    <button class="btn-crud-action btn-approve-app" data-id="<?= $app['id'] ?>" title="Approve Application" style="color: #2e6e4e;">
                                        <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                    </button>
                                    <?php endif; ?>

                                    <?php if ($status !== 'Rejected'): ?>
                                    <button class="btn-crud-action btn-reject-app" data-id="<?= $app['id'] ?>" title="Reject Application" style="color: #ef4444;">
                                        <span class="material-symbols-outlined text-[15px]">cancel</span>
                                    </button>
                                    <?php endif; ?>

                                    <button class="btn-crud-action btn-crud-edit btn-edit-app" 
                                        data-app='<?= $appJson ?>'
                                        title="Edit application details">
                                        <span class="material-symbols-outlined text-[14px]">edit</span>
                                    </button>

                                    <button class="btn-crud-action btn-crud-delete btn-delete-app" 
                                        data-id="<?= $app['id'] ?>"
                                        data-ticket="<?= htmlspecialchars($app['ticket_id']) ?>"
                                        title="Delete application from database">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <!-- EDIT APPLICATION MODAL -->
    <div class="vk-modal-overlay" id="editAppModal">
        <div class="vk-modal-dialog" style="max-width: 580px;">
            <div class="vk-modal-header">
                <div class="dev-flex-center-gap-8">
                    <span class="material-symbols-outlined text-[20px] dev-color-accent">edit_document</span>
                    <h3 class="dev-font-size-15px-font-29ad">Edit Partner Clearance Record</h3>
                </div>
                <button class="dev-background-transparent-border-none-aba8" id="btnCloseEditAppModal" style="color: #94a3b8; cursor: pointer;">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
            <div class="vk-modal-body">
                <input type="hidden" id="editAppId" value="" />
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="editAppCompany">Company Name *</label>
                        <input class="crud-form-input" id="editAppCompany" type="text" required />
                    </div>
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="editAppPartnerId">Partner ID</label>
                        <input class="crud-form-input" id="editAppPartnerId" type="text" />
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="editAppContactName">Technical Contact *</label>
                        <input class="crud-form-input" id="editAppContactName" type="text" required />
                    </div>
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="editAppContactEmail">Contact Email *</label>
                        <input class="crud-form-input" id="editAppContactEmail" type="email" required />
                    </div>
                </div>
                <div class="crud-form-group">
                    <label class="crud-form-label" for="editAppProjectRef">Project Reference</label>
                    <input class="crud-form-input" id="editAppProjectRef" type="text" />
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="editAppEnv">Target Environment</label>
                        <select class="crud-form-select" id="editAppEnv">
                            <option value="sandbox">sandbox</option>
                            <option value="staging">staging</option>
                            <option value="production">production</option>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="editAppStatus">Vetting Status</label>
                        <select class="crud-form-select" id="editAppStatus">
                            <option value="In Review">In Review</option>
                            <option value="Approved">Approved</option>
                            <option value="Active">Active</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="crud-form-group">
                    <label class="crud-form-label" for="editAppScopes">Requested Scopes</label>
                    <input class="crud-form-input" id="editAppScopes" type="text" placeholder="telemetry:read, orders:read_write" />
                </div>
                <div class="crud-form-group">
                    <label class="crud-form-label" for="editAppPublicKey">Public Key / CSR</label>
                    <textarea class="crud-form-textarea" id="editAppPublicKey" style="height: 60px;"></textarea>
                </div>
            </div>
            <div class="vk-modal-footer">
                <button class="vk-btn vk-btn-outline" id="btnCancelEditAppModal">Cancel</button>
                <button class="vk-btn vk-btn-primary" id="btnSaveEditApp">
                    <span class="material-symbols-outlined text-[16px]">save</span> Save Changes
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
    <script src="js/dev-registration.js"></script>
  <script src="../assets/js/notifications-hub.js" defer></script>
</body>

</html>