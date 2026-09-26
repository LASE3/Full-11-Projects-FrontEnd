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
    <title>Secure Document Ingestion - VOSTOKPRIBOR File Center</title>
    <link rel="stylesheet" href="css/fc-tokens.css" />
    <link rel="stylesheet" href="css/fc-common.css" />
    <link rel="stylesheet" href="css/fc-upload.css" />
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
                <span class="material-symbols-outlined text-[14px] fc-color-accent">upload_file</span>
                <span class="fc-font-family-var-font-eb0b" >INGESTION: <strong>SECURE TAXONOMY ENCLAVE</strong></span>
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
                <span class="station-live-clock">17:46:00 UTC+6</span>
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
            <a class="vk-nav-item" href="approvals.php">
                <div class="fc-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">assignment_turned_in</span>
                    <span>Signoff &amp; Approvals</span>
                </div>
                <span class="vk-tag vk-tag-highly-confidential fc-font-size-9px-padding-4279">1 ACTION</span>
            </a>
            <a class="vk-nav-item active" href="upload.php">
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
                <span class="fc-font-family-var-font-1ab9" >INGESTION ENCLAVE</span>
            </div>
            <div class="fc-mono-muted-11" >Auto Virus Scan: Active</div>
            <div class="fc-font-family-var-font-940f" >SHA-256 Engine: Online</div>
        </div>

        <!-- Log Out -->

    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="vk-main-layout">
        <div class="upload-container">
            <!-- HEADER BLOCK -->
            <div class="fc-display-flex-justify-content-f610" >
                <div>
                    <div class="fc-display-flex-align-items-9bb7" >
                        <span class="vk-tag fc-background-var-vk-sys-9005">
                            SYSTEM 09 // INTAKE ENCLAVE
                        </span>
                        <span class="fc-mono-muted-12" >NEXT ID: DOC-2026-016</span>
                    </div>
                    <h1 class="fc-font-size-26px-font-5041" >
                        <span class="material-symbols-outlined fc-font-size-28px-color-a4d3">upload_file</span>
                        Secure Document Ingestion &amp; Taxonomy Tagging
                    </h1>
                    <p class="fc-color-var-vk-neutral-5a07" >
                        Upload engineering specifications, bilateral customer contracts, and audit reports into the encrypted VOSTOKPRIBOR document vault.
                    </p>
                </div>
                <div>
                    <button class="vk-btn vk-btn-outline" id="btn-preset-baltnord-report" type="button">
                        <span class="material-symbols-outlined text-[16px]">dataset</span> Autofill BaltNord Test Spec
                    </button>
                </div>
            </div>

            <!-- DROPZONE -->
            <div class="dropzone-box" id="upload-dropzone">
                <input class="fc-display-none-224b" type="file" id="file-input-hidden"  />
                <span class="material-symbols-outlined dropzone-icon">cloud_upload</span>
                <div class="fc-font-family-var-font-9412" >
                    Drag and drop file here, or browse local disk
                </div>
                <div class="fc-font-size-12px-color-f108" >
                    Supported formats: PDF, XLSX, DOCX, DWG &bull; Maximum file size: 100 MB &bull; AES-256 encrypted at rest
                </div>
                <div class="fc-display-none-margin-top-6ce4" id="selected-file-name" ></div>
            </div>

            <!-- METADATA FORM -->
            <form id="doc-upload-form" class="vk-card fc-mb-30">
                <div class="vk-card-header">
                    <div>
                        <div class="vk-card-title">Document Metadata &amp; Governance Taxonomy</div>
                        <div class="vk-card-subtitle">Mandatory custodial attributes required per ISO 27001 document governance policy</div>
                    </div>
                    <span class="vk-tag fc-background-rgba-14-124-0f4c">
                        HSM ANCHORING
                    </span>
                </div>

                <div class="vk-card-body">
                    <input type="hidden" id="meta-classification" value="internal" />

                    <!-- ROW 1: TITLE & DEPT -->
                    <div class="form-grid-2col">
                        <div class="form-group">
                            <label class="form-label" for="meta-doc-title">
                                Document Title / Filename <span class="form-label-required">*</span>
                            </label>
                            <input class="form-input" id="meta-doc-title" type="text" placeholder="e.g. PRJ-2026-002_SCADA_Validation_Report.pdf" required />
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="meta-department">
                                Originating Department <span class="form-label-required">*</span>
                            </label>
                            <select class="form-select" id="meta-department" required>
                                <option value="ENG">ENG — Engineering &amp; Automation (16 Staff)</option>
                                <option value="EXE">EXE — Executive Management (5 Staff)</option>
                                <option value="OPS">OPS — Operations &amp; Logistics (20 Staff)</option>
                                <option value="SAL">SAL — Sales &amp; Commercial (16 Staff)</option>
                                <option value="FIN">FIN — Finance &amp; Accounting (8 Staff)</option>
                                <option value="HR">HR — Human Resources &amp; Training (5 Staff)</option>
                            </select>
                        </div>
                    </div>

                    <!-- ROW 2: PROJECT & CUSTOMER -->
                    <div class="form-grid-2col">
                        <div class="form-group">
                            <label class="form-label" for="meta-project-ref">
                                Associated Project Reference
                            </label>
                            <input class="form-input" id="meta-project-ref" type="text" placeholder="e.g. PRJ-2026-002 (BaltNord Process Systems)" />
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="meta-customer-ref">
                                Associated Customer Enclave
                            </label>
                            <input class="form-input" id="meta-customer-ref" type="text" placeholder="e.g. CUS-1002 (BaltNord Process Systems)" />
                        </div>
                    </div>

                    <!-- CLASSIFICATION SELECTOR -->
                    <div class="form-group">
                        <label class="form-label">
                            Data Classification Sensitivity Tier <span class="form-label-required">*</span>
                        </label>
                        <div class="classification-selector-grid">
                            <div class="class-option-card class-opt-public" data-class-val="public">
                                <span class="material-symbols-outlined text-[20px] fc-color-var-vk-class-8bc0">public</span>
                                <div class="fc-font-weight-600-font-3c8f" >Public</div>
                                <div class="fc-font-size-10px-color-eefa" >Catalogs, brochures</div>
                            </div>

                            <div class="class-option-card class-opt-internal selected" data-class-val="internal">
                                <span class="material-symbols-outlined text-[20px] fc-color-var-vk-class-5979">corporate_fare</span>
                                <div class="fc-font-weight-600-font-3c8f" >Internal</div>
                                <div class="fc-font-size-10px-color-eefa" >Handbooks, guides</div>
                            </div>

                            <div class="class-option-card class-opt-confidential" data-class-val="confidential">
                                <span class="material-symbols-outlined text-[20px] fc-color-var-vk-class-7bf4">shield</span>
                                <div class="fc-font-weight-600-font-3c8f" >Confidential</div>
                                <div class="fc-font-size-10px-color-eefa" >Contracts, SOWs</div>
                            </div>

                            <div class="class-option-card class-opt-highly-confidential" data-class-val="highly-confidential">
                                <span class="material-symbols-outlined text-[20px] fc-color-var-vk-class-66a0">lock</span>
                                <div class="fc-font-weight-600-font-3c8f" >Highly Conf.</div>
                                <div class="fc-font-size-10px-color-eefa" >Security, specs</div>
                            </div>
                        </div>
                    </div>

                    <!-- ROW 3: RETENTION & CUSTODIAN -->
                    <div class="form-grid-2col">
                        <div class="form-group">
                            <label class="form-label" for="meta-retention">
                                Statutory Retention Policy <span class="form-label-required">*</span>
                            </label>
                            <select class="form-select" id="meta-retention" required>
                                <option value="3y">3 Years (Operational drafts &amp; transient tickets)</option>
                                <option value="5y">5 Years (HR procedures &amp; personnel records)</option>
                                <option value="7y" selected>7 Years (Commercial contracts &amp; invoices)</option>
                                <option value="10y">10 Years (Industrial SCADA &amp; engineering blueprints)</option>
                                <option value="permanent">Permanent / Legal Preservation Hold</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="meta-custodian">
                                Lead Document Custodian <span class="form-label-required">*</span>
                            </label>
                            <input class="form-input" id="meta-custodian" type="text" value="Farida Iskakova (EMP-1019)" required />
                        </div>
                    </div>

                    <!-- DESCRIPTION -->
                    <div class="form-group">
                        <label class="form-label" for="meta-description">
                            Document Abstract / Scope Description
                        </label>
                        <textarea class="form-textarea" id="meta-description" rows="3" placeholder="Brief summary of document purpose and engineering context..."></textarea>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <div class="fc-display-flex-justify-content-ef47" >
                        <button class="vk-btn vk-btn-outline" type="reset">Reset</button>
                        <button class="vk-btn vk-btn-primary" type="submit">
                            <span class="material-symbols-outlined text-[16px]">security</span>
                            Ingest &amp; Register in Hardware Vault
                        </button>
                    </div>
                </div>
            </form>

            <!-- INGESTION SUCCESS CARD -->
            <div id="upload-success-card" class="vk-card fc-display-none-margin-bottom-366d">
                <div class="vk-card-header fc-background-rgba-14-124-0a52">
                    <div class="fc-display-flex-align-items-1c20" >
                        <span class="material-symbols-outlined text-[28px] fc-color-secondary">check_circle</span>
                        <div>
                            <div class="vk-card-title">Document Successfully Registered &amp; Encrypted</div>
                            <div class="vk-card-subtitle">Assigned to Almaty Central hardware storage enclave</div>
                        </div>
                    </div>
                    <span class="vk-tag vk-tag-internal">INGESTION VERIFIED</span>
                </div>

                <div class="vk-card-body">
                    <div class="fc-display-grid-grid-template-17e2" >
                        <div>
                            <div class="fc-font-size-11px-font-b2cd" >ASSIGNED DOC-ID</div>
                            <div class="fc-font-family-var-font-df85" id="disp-new-doc-id" >DOC-2026-016</div>
                        </div>
                        <div>
                            <div class="fc-font-size-11px-font-b2cd" >TITLE</div>
                            <div class="fc-font-weight-600-font-d257" id="disp-new-doc-title" >PRJ-2026-002_SCADA_Validation_Report.pdf</div>
                        </div>
                        <div>
                            <div class="fc-font-size-11px-font-b2cd" >DEPARTMENT</div>
                            <div class="fc-font-family-var-font-1f15" id="disp-new-doc-dept" >ENG</div>
                        </div>
                        <div>
                            <div class="fc-font-size-11px-font-b2cd" >CLASSIFICATION</div>
                            <div class="fc-font-family-var-font-eaee" id="disp-new-doc-class" >CONFIDENTIAL</div>
                        </div>
                    </div>

                    <div class="fc-margin-bottom-16px-d327" >
                        <div class="fc-font-family-var-font-e3ef" >SHA-256 CHECK INTEGRITY SEAL</div>
                        <div class="fc-font-family-var-font-4bf8"  id="disp-new-doc-hash">
                            9a3f2b4c810d7e5e6c1a89b034298fc1c149afbf4c8996fb92427ae41e4649b9
                        </div>
                    </div>

                    <div class="fc-display-flex-gap-12px-800a" >
                        <a class="vk-btn vk-btn-primary" href="Dashboard.php">
                            <span class="material-symbols-outlined text-[16px]">folder_open</span> View in Repository
                        </a>
                        <a class="vk-btn vk-btn-outline" href="audit.php">
                            <span class="material-symbols-outlined text-[16px]">fingerprint</span> Verify in Ledger
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

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

    <!-- Universal Command Palette Modal -->
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
    <script src="js/fc-upload.js"></script>
</body>

</html>