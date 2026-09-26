<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('DOC');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Document Signoff &amp; Approvals - VOSTOKPRIBOR File Center</title>
    <link rel="stylesheet" href="css/fc-tokens.css" />
    <link rel="stylesheet" href="css/fc-common.css" />
    <link rel="stylesheet" href="css/fc-approvals.css" />
</head>

<body>
    <!-- TOP NAVIGATION BAR (System 09 Graphite 4px Accent Stripe) -->
    <header class="vk-top-navbar">
        <div class="fc-flex-center-gap-24" >
            <a class="vk-brand-section" href="Dashboard.php">
                <img alt="VOSTOKPRIBOR Official Mark" class="brand-logo-img fc-logo-img" src="assets/logo.svg" />
                <div class="fc-flex-col" >
                    <div class="fc-flex-center-gap-8" >
                        <span class="fc-font-family-var-font-980b" >VOSTOKPRIBOR</span>
                        <span class="vk-system-badge">SYS-09 // FILE-CENTER</span>
                    </div>
                    <span class="fc-font-family-var-font-54ae" >ALMATY CENTRAL • EST. 1968 • DOCUMENT VAULT v3.8.2</span>
                </div>
            </a>
            <div class="fc-display-flex-align-items-bc9f" >
                <span class="material-symbols-outlined text-[14px] fc-color-var-vk-accent-33d2">assignment_turned_in</span>
                <span class="fc-font-family-var-font-eb0b" >WORKFLOW: <strong>MULTI-STAGE CLEARANCE PIPELINE</strong></span>
            </div>
        </div>

        <div class="fc-flex-center-gap-16" >
            <button class="search-trigger-btn" type="button">
                <span class="material-symbols-outlined text-[16px]">search</span>
                <span>Search documents, DOC-IDs...</span>
                <span class="kbd-shortcut">Ctrl K</span>
            </button>
            <div class="fc-display-flex-align-items-9eca" >
                <span class="material-symbols-outlined text-[14px] fc-color-secondary">schedule</span>
                <span class="station-live-clock">17:44:00 UTC+6</span>
            </div>
            <div class="fc-display-flex-align-items-20f3" >
                <div class="fc-text-right" >
                    <div class="fc-font-size-12px-font-2ab2" >Farida Iskakova</div>
                    <div class="fc-font-family-var-font-5c5e" >EMP-1019 • Lead Custodian</div>
                </div>
                <div class="fc-width-32px-height-32px-0eaf" >
                    <span class="material-symbols-outlined text-[18px] fc-text-white">folder_managed</span>
                </div>
            </div>

            <!-- Top Bar Sign Out -->
            <a href="../api/logout.php?system=File%20Center&redirect=../File%20Center/login.php" class="top-signout-btn" title="Sign Out of File Center" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" ><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
        </div>
    </header>

    <!-- LEFT SIDEBAR -->
    <aside class="vk-sidebar">
        <div class="vk-sidebar-nav">
            <div class="vk-sidebar-header">Document Vault</div>
            <a class="vk-nav-item" href="Dashboard.php">
                <div class="fc-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">folder_open</span>
                    <span>Document Repository</span>
                </div>
                <span class="nav-badge">15</span>
            </a>
            <a class="vk-nav-item active" href="approvals.php">
                <div class="fc-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">assignment_turned_in</span>
                    <span>Signoff &amp; Approvals</span>
                </div>
                <span class="vk-tag vk-tag-highly-confidential fc-font-size-9px-padding-4279">1 ACTION</span>
            </a>
            <a class="vk-nav-item" href="upload.php">
                <div class="fc-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">upload_file</span>
                    <span>Secure Ingestion</span>
                </div>
            </a>
            <a class="vk-nav-item" href="retention.php">
                <div class="fc-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                    <span>Retention &amp; Holds</span>
                </div>
            </a>
            <a class="vk-nav-item" href="audit.php">
                <div class="fc-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">fingerprint</span>
                    <span>Integrity Ledger</span>
                </div>
            </a>

            <div class="vk-sidebar-header fc-mt-20">System Integrations</div>
            <a class="vk-nav-item" href="../VOSTOKPRIBOR Corporate Web Platform/index.php">
                <div class="fc-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px] fc-color-1b3a5c-1796">language</span>
                    <span>Corporate Platform</span>
                </div>
                <span class="vk-tag fc-font-size-9px-background-21b8">SYS-01</span>
            </a>
            <a class="vk-nav-item" href="../Employee Intranet/index.php">
                <div class="fc-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px] fc-color-5c7290-b50c">badge</span>
                    <span>Employee Intranet</span>
                </div>
                <span class="vk-tag fc-font-size-9px-background-cfa1">SYS-04</span>
            </a>
            <a class="vk-nav-item" href="../Developer/index.php">
                <div class="fc-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px] fc-color-1e8fa6-f90d">terminal</span>
                    <span>Developer / API Portal</span>
                </div>
                <span class="vk-tag fc-font-size-9px-background-5382">SYS-10</span>
            </a>
            <a class="vk-nav-item" href="../Admin & Governance Portal/index.php">
                <div class="fc-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px] fc-color-var-vk-alert-6578">shield</span>
                    <span>Admin &amp; Governance</span>
                </div>
                <span class="vk-tag fc-font-size-9px-background-0f11">SYS-11</span>
            </a>
        </div>

        <div class="fc-padding-16px-border-top-d16d" >
            <div class="fc-display-flex-align-items-81c3" >
                <span class="status-dot-pulse"></span>
                <span class="fc-font-family-var-font-1ab9" >WORKFLOW PIPELINE</span>
            </div>
            <div class="fc-mono-muted-11" >Custodian: Farida Iskakova</div>
            <div class="fc-font-family-var-font-940f" >Clearance: Level 3 (ENG)</div>
        </div>

        <!-- Log Out -->

    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="vk-main-layout">
        <!-- HEADER BLOCK -->
        <div class="fc-display-flex-justify-content-f610" >
            <div>
                <div class="fc-display-flex-align-items-9bb7" >
                    <span class="vk-tag vk-tag-highly-confidential">ACTION REQUIRED // L3 APPROVAL</span>
                    <span class="fc-mono-muted-12" >REF: SCENARIO-PDF-SYS09</span>
                </div>
                <h1 class="fc-font-size-26px-font-5041" >
                    <span class="material-symbols-outlined fc-font-size-28px-color-cee8">draw</span>
                    Document Signoff &amp; Clearance Pipeline
                </h1>
                <p class="fc-color-var-vk-neutral-5a07" >
                    Formal multi-stakeholder review pipeline for high-sensitivity engineering deliverables, project integrations, and bilateral client releases.
                </p>
            </div>
            <div>
                <a class="vk-btn vk-btn-outline" href="Dashboard.php">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span> Back to Repository
                </a>
            </div>
        </div>

        <!-- STEPPER CARD -->
        <div class="approval-pipeline-card">
            <div class="fc-flex-between-center" >
                <div class="fc-font-family-var-font-bfb2" >
                    Four-Stage Approval &amp; Release Protocol (SOP-02 §4)
                </div>
                <span id="doc-approval-status-badge" class="vk-status-badge status-in-review">AWAITING PM APPROVAL</span>
            </div>

            <div class="approval-stepper">
                <div class="stepper-progress-bar">
                    <div class="stepper-progress-fill"></div>
                </div>

                <!-- Step 1 -->
                <div class="approval-step completed">
                    <div class="step-circle">
                        <span class="material-symbols-outlined text-[18px]">done</span>
                    </div>
                    <div class="step-label">1. Author Submission</div>
                    <div class="step-subtext">Dana Yermak (EMP-1017)</div>
                    <div class="fc-font-family-var-font-49e1" >2026-09-10 14:12</div>
                </div>

                <!-- Step 2 -->
                <div class="approval-step active" id="step-pm-approval">
                    <div class="step-circle">2</div>
                    <div class="step-label">2. Project Manager Signoff</div>
                    <div class="step-subtext">Farida Iskakova (EMP-1019)</div>
                    <div class="fc-font-family-var-font-c0ef" >ACTIVE REVIEW</div>
                </div>

                <!-- Step 3 -->
                <div class="approval-step" id="step-gov-clearance">
                    <div class="step-circle">3</div>
                    <div class="step-label">3. Governance Clearance</div>
                    <div class="step-subtext">Timur Akhmetov (EMP-1005)</div>
                    <div class="fc-font-family-var-font-2ead" >L4 Attestation</div>
                </div>

                <!-- Step 4 -->
                <div class="approval-step" id="step-client-dispatch">
                    <div class="step-circle">4</div>
                    <div class="step-label">4. Portal Synchronization</div>
                    <div class="step-subtext">Customer Portal (CUS-1002)</div>
                    <div class="fc-font-family-var-font-2ead" >Automated Bridge</div>
                </div>
            </div>
        </div>

        <!-- REVIEW HERO CARD (PDF Scenario: DOC-2026-004) -->
        <div class="doc-review-hero">
            <div class="fc-display-flex-justify-content-fe2b" >
                <div>
                    <div class="fc-display-flex-align-items-0b62" >
                        <span class="vk-tag vk-tag-highly-confidential">HIGHLY CONFIDENTIAL</span>
                        <span class="fc-mono-muted-12" >DOC-2026-004</span>
                        <span class="vk-tag fc-background-rgba-90-100-00fd">PRJ-2026-002</span>
                    </div>
                    <h2 class="fc-font-size-20px-font-fa92" >
                        PRJ-2026-002_Integration_Specification.pdf
                    </h2>
                    <div class="fc-font-size-13px-color-a50d" >
                        Target: <strong>BaltNord Process Systems (CUS-1002)</strong> &bull; Technical Lead: <strong>Kristaps Ozols</strong>
                    </div>
                </div>

                <div class="fc-display-flex-gap-8px-1326" >
                    <button class="vk-btn vk-btn-outline" id="btn-toggle-redaction" type="button">
                        <span class="material-symbols-outlined text-[16px]">visibility_off</span> Preview Redacted (Customer Portal)
                    </button>
                    <button class="vk-btn vk-btn-primary" id="btn-open-sign-modal" type="button">
                        <span class="material-symbols-outlined text-[16px]">draw</span> Approve &amp; Apply Digital Signature
                    </button>
                </div>
            </div>

            <!-- METADATA GRID -->
            <div class="review-meta-grid">
                <div>
                    <div class="fc-mono-muted-11" >DOCUMENT OWNER</div>
                    <div class="fc-font-weight-600-font-d257" >Farida Iskakova (EMP-1019)</div>
                </div>
                <div>
                    <div class="fc-mono-muted-11" >PRIMARY AUTHOR</div>
                    <div class="fc-font-weight-600-font-d257" >Dana Yermak (EMP-1017)</div>
                </div>
                <div>
                    <div class="fc-mono-muted-11" >CONTRACT VALUE</div>
                    <div class="fc-font-family-var-font-6339" >&euro;240,000</div>
                </div>
                <div>
                    <div class="fc-mono-muted-11" >HARDWARE ANCHOR</div>
                    <div class="fc-font-family-var-font-991b" >SHA-256 VERIFIED</div>
                </div>
            </div>

            <!-- REDACTION & SPEC PREVIEW -->
            <div class="fc-mt-20" >
                <div class="fc-display-flex-justify-content-f209" >
                    <span class="fc-font-family-var-font-6272" >
                        Document Executive Abstract &amp; Redaction Inspection
                    </span>
                    <span class="fc-mono-muted-11" >Version: 2.1-RELEASE-CANDIDATE</span>
                </div>

                <div class="redaction-preview-box" id="spec-preview-content">
                    <strong>1. SYSTEM INTEGRATION TOPOLOGY // PRJ-2026-002:</strong><br />
                    The Baltic processing plant operated by BaltNord Process Systems (CUS-1002) requires continuous real-time telemetry streaming from VP-1001 (Vibration Sensor Array) and VP-1002 (Multi-Stage Hydrocarbon Pressure Sensors).<br /><br />
                    <strong>2. PROTOCOL ADAPTER &amp; ENCLAVE SPECIFICATION:</strong><br />
                    • Ingestion Transport: Mutual TLS (mTLS v1.3) with Ed25519 client certificates.<br />
                    • Telemetry Ingestion Node: <span class="redact-target">10.240.0.12 (gw-almaty-01.vostokpribor.local)</span> via port 8443.<br />
                    • Sensor Modbus Register Offset: <span class="redact-target">0x40001 to 0x40032 [INTERNAL CONTROLLER MEMORY MAPPING]</span>.<br />
                    • Maximum Polling Throttle: 10,000 requests/minute per Tier-1 partner agreement.<br /><br />
                    <strong>3. CUSTODIAL STATEMENT &amp; CLEARANCE:</strong><br />
                    Farida Iskakova (EMP-1019) verifies that proprietary sensor calibration constants (<span class="redact-target">CAL-MATRIX-9921-SOVIET-VIB</span>) have been isolated and will NOT be transmitted over external API payloads. Customer Portal distribution is approved subject to redaction verification.
                </div>
            </div>
        </div>
    </main>

    <!-- ELECTRONIC SIGNOFF MODAL -->
    <div class="signoff-modal-backdrop" id="signoff-modal-backdrop">
        <div class="signoff-modal-box">
            <div class="fc-padding-16px-20px-background-b5da" >
                <div class="fc-font-family-var-font-7e7e" >
                    <span class="material-symbols-outlined text-[18px] fc-color-secondary">verified_user</span>
                    Electronic Digital Signoff Enclave
                </div>
                <span class="vk-tag fc-background-rgba-255-255-7939">FIPS 140-3</span>
            </div>

            <form class="fc-padding-20px-32c1" id="electronic-signature-form" >
                <p class="fc-font-size-13px-color-5cf7" >
                    You are signing <strong>DOC-2026-004</strong> (PRJ-2026-002_Integration_Specification.pdf) as Senior Project Manager &amp; Lead Custodian. Your cryptographic key will be anchored to the document in the Almaty Central HSM.
                </p>

                <div class="signature-stamp-box">
                    <div class="fc-font-family-var-font-5822" >
                        SIGNATORY: FARIDA ISKAKOVA (EMP-1019)
                    </div>
                    <div class="fc-font-family-var-font-d501" >
                        ROLE: PROJECT MANAGER // ENGINEERING (L3 CLEARANCE)
                    </div>
                    <div class="fc-font-family-var-font-fb04" >
                        TOKEN: SIG-ED25519-VP-9021884-2026-09-11
                    </div>
                </div>

                <div class="fc-display-flex-flex-direction-10e5" >
                    <label class="fc-display-flex-align-items-5fea" >
                        <input type="checkbox" required checked />
                        <span>I certify that all technical specifications for PRJ-2026-002 meet contractual commitments.</span>
                    </label>
                    <label class="fc-display-flex-align-items-5fea" >
                        <input type="checkbox" required checked />
                        <span>I approve client release and route to Timur Akhmetov (EMP-1005) for governance release.</span>
                    </label>
                </div>

                <div class="fc-display-flex-justify-content-4707" >
                    <button class="vk-btn vk-btn-outline" id="btn-cancel-sign" type="button">Cancel</button>
                    <button class="vk-btn vk-btn-primary" type="submit">
                        <span class="material-symbols-outlined text-[16px]">draw</span> Affix Signature &amp; Release
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DOCKED ENTERPRISE STATUS BAR -->
    <footer class="vk-status-bar">
        <div class="fc-flex-center-gap-16" >
            <div class="fc-flex-center-gap-8" >
                <span class="status-dot-pulse"></span>
                <span>ALMATY-VAULT-01 // HSM CLUSTER SYNCHRONIZED</span>
            </div>
            <span>VOLUME: 842.6 GB / 4.8 TB (17.5%)</span>
        </div>
        <div class="fc-display-flex-align-items-bf7c" >
            <span>ACTIVE SENSITIVITY ENCLAVE: FOUR-TIER RBAC</span>
            <span>IEC 62443 / ISO 27001 AUDIT COMPLIANT</span>
        </div>
    </footer>

    <!-- Universal Command Palette Modal (Ctrl + K) -->
    <div class="cmd-palette-backdrop" id="cmd-palette-modal">
        <div class="cmd-palette-box">
            <div class="cmd-palette-header">
                <span class="material-symbols-outlined text-[20px] fc-color-accent">folder_managed</span>
                <input class="cmd-palette-input" id="cmd-palette-input" type="text" placeholder="Type a document ID, name, or jump to view..." />
            </div>
            <div class="cmd-palette-list" id="cmd-palette-results">
                <a class="cmd-palette-item" href="Dashboard.php">
                    <span class="material-symbols-outlined text-[16px]">folder_open</span>
                    <span>Document Repository (All 15 Statutory Records)</span>
                </a>
                <a class="cmd-palette-item" href="approvals.php">
                    <span class="material-symbols-outlined text-[16px]">assignment_turned_in</span>
                    <span>Signoff &amp; Approvals &bull; DOC-2026-004 BaltNord</span>
                </a>
                <a class="cmd-palette-item" href="upload.php">
                    <span class="material-symbols-outlined text-[16px]">upload_file</span>
                    <span>Secure Ingestion Enclave (DOC-2026-016)</span>
                </a>
                <a class="cmd-palette-item" href="retention.php">
                    <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                    <span>Retention Governance &amp; Legal Holds</span>
                </a>
                <a class="cmd-palette-item" href="audit.php">
                    <span class="material-symbols-outlined text-[16px]">fingerprint</span>
                    <span>Cryptographic Integrity &amp; SHA-256 Ledger</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Universal Toast Container -->
    <div class="vk-toast-container"></div>

    <script src="js/fc-common.js"></script>
    <script src="js/fc-approvals.js"></script>
</body>

</html>