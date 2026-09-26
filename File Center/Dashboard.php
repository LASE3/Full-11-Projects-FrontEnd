<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('FileCenter');
$pdo = getDbConnection();
$currUser = $_SESSION['vostok_user'] ?? ['full_name' => 'Authorized User', 'clearance_level' => 'L2'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Enterprise Document Repository - VOSTOKPRIBOR File Center</title>
    <link rel="stylesheet" href="css/fc-tokens.css" />
    <link rel="stylesheet" href="css/fc-common.css" />
    <link rel="stylesheet" href="css/fc-repo.css" />
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
                <span class="material-symbols-outlined text-[14px] fc-color-accent">folder_special</span>
                <span class="fc-font-family-var-font-eb0b" >VAULT: <strong>CENTRAL DOCUMENT REPOSITORY</strong></span>
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
                <span class="station-live-clock">17:42:00 UTC+6</span>
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

    <!-- LEFT SIDEBAR (Authenticated System 09) -->
    <aside class="vk-sidebar">
        <div class="vk-sidebar-nav">
            <div class="vk-sidebar-header">Document Vault</div>
            <a class="vk-nav-item active" href="Dashboard.php">
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
            <a href="Integrations.php" class="sidebar-nav-item">
                <div class="sidebar-item-left"><span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00E5FF" stroke-width="2">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                        </svg></span><span class="fc-color-00e5ff-font-weight-fe7a" >System Integrations</span></div><span class="sidebar-badge fc-background-rgba-0-229-2595">SYS06</span>
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
                <span class="fc-font-family-var-font-1ab9" >ENCLAVE HSM ONLINE</span>
            </div>
            <div class="fc-mono-muted-11" >Node: files.vostokpribor.local</div>
            <div class="fc-font-family-var-font-940f" >FIPS 140-3 Hardware Sealed</div>
        </div>

        <!-- Log Out -->

    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="vk-main-layout">
        <!-- HEADER BLOCK -->
        <div class="repo-header-toolbar">
            <div>
                <div class="fc-display-flex-align-items-9bb7" >
                    <span class="vk-tag fc-background-var-vk-sys-9005">
                        SYSTEM 09 // GRAPHITE #5A6470
                    </span>
                    <span class="fc-mono-muted-12" >FQDN: files.vostokpribor.local</span>
                </div>
                <h1 class="fc-font-size-26px-font-5041" >
                    <span class="material-symbols-outlined fc-font-size-28px-color-a4d3">source_environment</span>
                    Central Enterprise Document Repository
                </h1>
                <p class="fc-color-var-vk-neutral-5a07" >
                    Authoritative archival store, technical specifications, bilateral customer contracts, and regulatory audit records for VOSTOKPRIBOR.
                </p>
            </div>
            <div class="fc-display-flex-gap-10px-c623" >
                <a class="vk-btn vk-btn-outline" href="approvals.php">
                    <span class="material-symbols-outlined text-[16px]">rule</span> Review Queue (1)
                </a>
                <a class="vk-btn vk-btn-primary" href="upload.php">
                    <span class="material-symbols-outlined text-[16px]">upload</span> Ingest Document
                </a>
            </div>
        </div>

        <!-- 4 KPI REPOSITORY METRIC CARDS -->
        <div class="repo-stats-grid">
            <div class="repo-stat-card">
                <div class="repo-stat-label">
                    <span>Registered Documents</span>
                    <span class="material-symbols-outlined text-[18px] fc-color-accent">description</span>
                </div>
                <div class="repo-stat-value">1,842</div>
                <div class="repo-stat-subtext fc-color-var-vk-secondary-bd5b">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> 15 Core Baseline Manifest
                </div>
            </div>

            <div class="repo-stat-card">
                <div class="repo-stat-label">
                    <span>Encrypted Vault Volume</span>
                    <span class="material-symbols-outlined text-[18px] fc-color-secondary">lock</span>
                </div>
                <div class="repo-stat-value">842.6 <span class="fc-font-size-14px-font-8cb8" >GB</span></div>
                <div class="repo-stat-subtext">AES-256-GCM hardware envelope</div>
            </div>

            <div class="repo-stat-card">
                <div class="repo-stat-label">
                    <span>Pending Approvals</span>
                    <span class="material-symbols-outlined text-[18px] fc-color-var-vk-accent-33d2">pending_actions</span>
                </div>
                <div class="repo-stat-value fc-color-var-vk-accent-33d2">1 Action</div>
                <div class="repo-stat-subtext">DOC-2026-004 awaiting Farida Iskakova</div>
            </div>

            <div class="repo-stat-card">
                <div class="repo-stat-label">
                    <span>Integrity Verification</span>
                    <span class="material-symbols-outlined text-[18px] fc-color-secondary">verified</span>
                </div>
                <div class="repo-stat-value fc-color-2e6e4e-f283">100.0%</div>
                <div class="repo-stat-subtext">All SHA-256 anchors matched</div>
            </div>
        </div>

        <!-- FILTER & SEARCH BAR -->
        <div class="repo-filter-bar">
            <div class="filter-pills-group">
                <button class="filter-pill active" data-class-filter="all">
                    <span>All Classifications</span>
                    <span class="fc-mono-11" >(15)</span>
                </button>
                <button class="filter-pill pill-highly-confidential" data-class-filter="highly-confidential">
                    <span class="material-symbols-outlined text-[14px] fc-color-var-vk-class-66a0">lock</span>
                    <span>Highly Confidential</span>
                    <span class="fc-mono-11" >(6)</span>
                </button>
                <button class="filter-pill pill-confidential" data-class-filter="confidential">
                    <span class="material-symbols-outlined text-[14px] fc-color-var-vk-class-7bf4">shield</span>
                    <span>Confidential</span>
                    <span class="fc-mono-11" >(6)</span>
                </button>
                <button class="filter-pill pill-internal" data-class-filter="internal">
                    <span class="material-symbols-outlined text-[14px] fc-color-var-vk-class-5979">corporate_fare</span>
                    <span>Internal</span>
                    <span class="fc-mono-11" >(2)</span>
                </button>
                <button class="filter-pill pill-public" data-class-filter="public">
                    <span class="material-symbols-outlined text-[14px] fc-color-var-vk-class-8bc0">public</span>
                    <span>Public</span>
                    <span class="fc-mono-11" >(1)</span>
                </button>
            </div>

            <div class="repo-search-box">
                <span class="material-symbols-outlined text-[16px] fc-color-var-vk-neutral-a8c4">search</span>
                <input class="repo-search-input" id="repo-search-input" type="text" placeholder="Filter DOC-ID, name, project..." />
            </div>
        </div>

        <!-- REPOSITORY WORKSPACE: FOLDER TREE + DOCUMENT TABLE -->
        <div class="repo-workspace-grid">
            <!-- LEFT: FOLDER TREE -->
            <div class="folder-tree-card">
                <div class="folder-tree-title">
                    <span class="material-symbols-outlined text-[16px] fc-color-accent">account_tree</span>
                    <span>Partitions</span>
                </div>
                <div class="folder-item active" data-folder-slug="all">
                    <div class="fc-flex-center-gap-8" >
                        <span class="material-symbols-outlined text-[16px]">folder</span>
                        <span>All Partitions</span>
                    </div>
                    <span class="fc-mono-10" >15</span>
                </div>
                <div class="folder-item" data-folder-slug="governance">
                    <div class="fc-flex-center-gap-8" >
                        <span class="material-symbols-outlined text-[16px]">gavel</span>
                        <span>/Corporate/Gov</span>
                    </div>
                    <span class="fc-mono-10" >3</span>
                </div>
                <div class="folder-item" data-folder-slug="projects">
                    <div class="fc-flex-center-gap-8" >
                        <span class="material-symbols-outlined text-[16px]">precision_manufacturing</span>
                        <span>/Projects/Eng</span>
                    </div>
                    <span class="fc-mono-10" >4</span>
                </div>
                <div class="folder-item" data-folder-slug="contracts">
                    <div class="fc-flex-center-gap-8" >
                        <span class="material-symbols-outlined text-[16px]">handshake</span>
                        <span>/Commercial/SOW</span>
                    </div>
                    <span class="fc-mono-10" >2</span>
                </div>
                <div class="folder-item" data-folder-slug="finance">
                    <div class="fc-flex-center-gap-8" >
                        <span class="material-symbols-outlined text-[16px]">payments</span>
                        <span>/Finance/Billing</span>
                    </div>
                    <span class="fc-mono-10" >2</span>
                </div>
                <div class="folder-item" data-folder-slug="hr">
                    <div class="fc-flex-center-gap-8" >
                        <span class="material-symbols-outlined text-[16px]">badge</span>
                        <span>/HR/Personnel</span>
                    </div>
                    <span class="fc-mono-10" >2</span>
                </div>
                <div class="folder-item" data-folder-slug="operations">
                    <div class="fc-flex-center-gap-8" >
                        <span class="material-symbols-outlined text-[16px]">local_shipping</span>
                        <span>/Operations/Suppliers</span>
                    </div>
                    <span class="fc-mono-10" >2</span>
                </div>

                <div class="fc-margin-top-20px-padding-aa3e" >
                    <div class="fc-font-family-var-font-afcc" >Storage Quota</div>
                    <div class="fc-height-6px-background-var-86f3" >
                        <div class="fc-width-17-5-height-4883" ></div>
                    </div>
                    <div class="fc-display-flex-justify-content-6d82" >
                        <span>842 GB used</span>
                        <span>4.8 TB cap</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT: MAIN DOCUMENT TABLE (15 Official Documents from PDF) -->
            <div class="vk-table-container">
                <div class="fc-padding-12px-16px-background-6c79" >
                    <div class="fc-font-family-var-font-ecce" >
                        OFFICIAL DOCUMENT REGISTER (DOC-2026-001 TO DOC-2026-015)
                    </div>
                    <div class="fc-mono-muted-11" >
                        Showing <span id="visible-docs-count">15</span> documents
                    </div>
                </div>
                <table class="vk-table">
                    <thead>
                        <tr>
                            <th class="fc-width-130px-e314" >DOC-ID</th>
                            <th>Filename &amp; Description</th>
                            <th class="fc-width-170px-ef4a" >Classification</th>
                            <th class="fc-w-140" >System Tag</th>
                            <th class="fc-width-110px-3e28" >Status</th>
                            <th class="fc-width-90px-text-align-c9fd" >Action</th>
                        </tr>
                    </thead>
                    <tbody id="repo-table-body">
                        <!-- DOC-2026-001: Highly Confidential -->
                        <tr class="repo-doc-row vk-table-row-highly-confidential"
                            data-doc-id="DOC-2026-001"
                            data-doc-name="Corporate_Information_Security_Policy.pdf"
                            data-classification="highly-confidential"
                            data-folder="governance"
                            data-project="PRJ-GOV-2026"
                            data-custodian="Timur Akhmetov (EMP-1005)"
                            data-size="3.8 MB"
                            data-date="2026-01-15"
                            data-system="Admin &amp; Governance"
                            data-status="Approved"
                            data-hash="a89f30b9148d423985bf4f481c81c4e97a5b3992b1cf5600ea8b1990c681ea88">
                            <td><code>DOC-2026-001</code></td>
                            <td>
                                <div class="fc-text-primary-bold" >Corporate_Information_Security_Policy.pdf</div>
                                <div class="fc-text-muted-11" >Master enterprise cybersecurity charter &amp; access policy</div>
                            </td>
                            <td><span class="vk-tag vk-tag-highly-confidential">Highly Confidential</span></td>
                            <td><span class="vk-tag fc-text-10">Admin &amp; Gov</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td class="fc-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-outline btn-inspect-doc">Inspect</button>
                            </td>
                        </tr>

                        <!-- DOC-2026-002: Confidential -->
                        <tr class="repo-doc-row vk-table-row-confidential"
                            data-doc-id="DOC-2026-002"
                            data-doc-name="Customer_Onboarding_Standard.pdf"
                            data-classification="confidential"
                            data-folder="contracts"
                            data-project="COMM-STD-2026"
                            data-custodian="Pavel Orlov (EMP-1006)"
                            data-size="1.6 MB"
                            data-date="2026-02-01"
                            data-system="CRM"
                            data-status="Approved"
                            data-hash="7b2a9e334f590bb821034f828a1c89283e7428fb17c1817e81037894a8217e92">
                            <td><code>DOC-2026-002</code></td>
                            <td>
                                <div class="fc-text-primary-bold" >Customer_Onboarding_Standard.pdf</div>
                                <div class="fc-text-muted-11" >Commercial account vetting protocol &amp; KYC</div>
                            </td>
                            <td><span class="vk-tag vk-tag-confidential">Confidential</span></td>
                            <td><span class="vk-tag fc-text-10">CRM</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td class="fc-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-outline btn-inspect-doc">Inspect</button>
                            </td>
                        </tr>

                        <!-- DOC-2026-003: Confidential -->
                        <tr class="repo-doc-row vk-table-row-confidential"
                            data-doc-id="DOC-2026-003"
                            data-doc-name="PRJ-2026-001_Statement_of_Work.pdf"
                            data-classification="confidential"
                            data-folder="projects"
                            data-project="PRJ-2026-001 (Aral Geomatics)"
                            data-custodian="Farida Iskakova (EMP-1019)"
                            data-size="2.1 MB"
                            data-date="2026-02-14"
                            data-system="File Center"
                            data-status="Approved"
                            data-hash="4e1a8b928172c3d4e5f60718293a4b5c6d7e8f90123456789abcdef012345678">
                            <td><code>DOC-2026-003</code></td>
                            <td>
                                <div class="fc-text-primary-bold" >PRJ-2026-001_Statement_of_Work.pdf</div>
                                <div class="fc-text-muted-11" >Aral Geomatics Group &bull; Optical Sensor Integration SOW</div>
                            </td>
                            <td><span class="vk-tag vk-tag-confidential">Confidential</span></td>
                            <td><span class="vk-tag fc-text-10">File Center</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td class="fc-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-outline btn-inspect-doc">Inspect</button>
                            </td>
                        </tr>

                        <!-- DOC-2026-004: Highly Confidential (THE PDF CORE SCENARIO!) -->
                        <tr class="repo-doc-row vk-table-row-highly-confidential"
                            data-doc-id="DOC-2026-004"
                            data-doc-name="PRJ-2026-002_Integration_Specification.pdf"
                            data-classification="highly-confidential"
                            data-folder="projects"
                            data-project="PRJ-2026-002 (BaltNord Process Systems)"
                            data-custodian="Farida Iskakova (EMP-1019)"
                            data-size="4.5 MB"
                            data-date="2026-09-11"
                            data-system="File Center"
                            data-status="In Review"
                            data-hash="9f8e7d6c5b4a3928170192837465abcdeffedcba98765432101234567890fedc">
                            <td><code>DOC-2026-004</code></td>
                            <td>
                                <div class="fc-font-weight-700-color-9c73" >
                                    <span>PRJ-2026-002_Integration_Specification.pdf</span>
                                    <span class="vk-tag fc-background-fee2e2-color-991b1b-440c">ACTION NEEDED</span>
                                </div>
                                <div class="fc-text-muted-11" >BaltNord SCADA Ingestion &bull; Reviewed by Farida Iskakova</div>
                            </td>
                            <td><span class="vk-tag vk-tag-highly-confidential">Highly Confidential</span></td>
                            <td><span class="vk-tag fc-text-10">File Center</span></td>
                            <td><span class="vk-status-badge status-in-review">In Review</span></td>
                            <td class="fc-text-right" >
                                <a class="vk-btn vk-btn-sm vk-btn-primary" href="approvals.php" >Sign</a>
                            </td>
                        </tr>

                        <!-- DOC-2026-005: Confidential -->
                        <tr class="repo-doc-row vk-table-row-confidential"
                            data-doc-id="DOC-2026-005"
                            data-doc-name="INV-2026-002_Billing_Record.pdf"
                            data-classification="confidential"
                            data-folder="finance"
                            data-project="PRJ-2026-002"
                            data-custodian="Daniel Weber (EMP-1003)"
                            data-size="890 KB"
                            data-date="2026-03-01"
                            data-system="Finance"
                            data-status="Approved"
                            data-hash="1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef">
                            <td><code>DOC-2026-005</code></td>
                            <td>
                                <div class="fc-text-primary-bold" >INV-2026-002_Billing_Record.pdf</div>
                                <div class="fc-text-muted-11" >BaltNord Milestone 1 billing attestation (€120,000)</div>
                            </td>
                            <td><span class="vk-tag vk-tag-confidential">Confidential</span></td>
                            <td><span class="vk-tag fc-text-10">Finance</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td class="fc-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-outline btn-inspect-doc">Inspect</button>
                            </td>
                        </tr>

                        <!-- DOC-2026-006: Confidential -->
                        <tr class="repo-doc-row vk-table-row-confidential"
                            data-doc-id="DOC-2026-006"
                            data-doc-name="Employee_Onboarding_Procedure.pdf"
                            data-classification="confidential"
                            data-folder="hr"
                            data-project="HR-SOP-2026"
                            data-custodian="Ilona Vetra (EMP-1013)"
                            data-size="1.2 MB"
                            data-date="2026-01-10"
                            data-system="HR"
                            data-status="Approved"
                            data-hash="abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890">
                            <td><code>DOC-2026-006</code></td>
                            <td>
                                <div class="fc-text-primary-bold" >Employee_Onboarding_Procedure.pdf</div>
                                <div class="fc-text-muted-11" >Standard Operating Procedure &bull; SOP-05 HR Enrollment</div>
                            </td>
                            <td><span class="vk-tag vk-tag-confidential">Confidential</span></td>
                            <td><span class="vk-tag fc-text-10">HR</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td class="fc-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-outline btn-inspect-doc">Inspect</button>
                            </td>
                        </tr>

                        <!-- DOC-2026-007: Highly Confidential -->
                        <tr class="repo-doc-row vk-table-row-highly-confidential"
                            data-doc-id="DOC-2026-007"
                            data-doc-name="Employee_Access_Matrix.xlsx"
                            data-classification="highly-confidential"
                            data-folder="governance"
                            data-project="SEC-AUDIT-2026"
                            data-custodian="Timur Akhmetov (EMP-1005)"
                            data-size="1.9 MB"
                            data-date="2026-09-01"
                            data-system="Admin &amp; Governance"
                            data-status="Approved"
                            data-hash="deadbeef1029384756abcdef0192837465bcaefd1234567890fedcba98765432">
                            <td><code>DOC-2026-007</code></td>
                            <td>
                                <div class="fc-text-primary-bold" >Employee_Access_Matrix.xlsx</div>
                                <div class="fc-text-muted-11" >Complete RBAC and clearance register for 95 employees</div>
                            </td>
                            <td><span class="vk-tag vk-tag-highly-confidential">Highly Confidential</span></td>
                            <td><span class="vk-tag fc-text-10">Admin &amp; Gov</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td class="fc-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-outline btn-inspect-doc">Inspect</button>
                            </td>
                        </tr>

                        <!-- DOC-2026-008: Confidential -->
                        <tr class="repo-doc-row vk-table-row-confidential"
                            data-doc-id="DOC-2026-008"
                            data-doc-name="Supplier_Evaluation_2026.pdf"
                            data-classification="confidential"
                            data-folder="operations"
                            data-project="OPS-SUP-2026"
                            data-custodian="Arman Tulegenov (EMP-1011)"
                            data-size="2.8 MB"
                            data-date="2026-02-28"
                            data-system="Operations"
                            data-status="Approved"
                            data-hash="9876543210fedcba9876543210fedcba9876543210fedcba9876543210fedcba">
                            <td><code>DOC-2026-008</code></td>
                            <td>
                                <div class="fc-text-primary-bold" >Supplier_Evaluation_2026.pdf</div>
                                <div class="fc-text-muted-11" >Tier-1 industrial transducer vendor scorecard</div>
                            </td>
                            <td><span class="vk-tag vk-tag-confidential">Confidential</span></td>
                            <td><span class="vk-tag fc-text-10">Operations</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td class="fc-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-outline btn-inspect-doc">Inspect</button>
                            </td>
                        </tr>

                        <!-- DOC-2026-009: Public -->
                        <tr class="repo-doc-row vk-table-row-public"
                            data-doc-id="DOC-2026-009"
                            data-doc-name="Optical_Sensor_Product_Catalog.pdf"
                            data-classification="public"
                            data-folder="operations"
                            data-project="PUB-CAT-2026"
                            data-custodian="Pavel Orlov (EMP-1006)"
                            data-size="14.2 MB"
                            data-date="2026-01-05"
                            data-system="E-Commerce"
                            data-status="Approved"
                            data-hash="0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef">
                            <td><code>DOC-2026-009</code></td>
                            <td>
                                <div class="fc-text-primary-bold" >Optical_Sensor_Product_Catalog.pdf</div>
                                <div class="fc-text-muted-11" >Standard B2B product specifications &bull; Public distribution</div>
                            </td>
                            <td><span class="vk-tag vk-tag-public">Public</span></td>
                            <td><span class="vk-tag fc-text-10">E-Commerce</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td class="fc-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-outline btn-inspect-doc">Inspect</button>
                            </td>
                        </tr>

                        <!-- DOC-2026-010: Internal -->
                        <tr class="repo-doc-row vk-table-row-internal"
                            data-doc-id="DOC-2026-010"
                            data-doc-name="API_Integration_Guide.pdf"
                            data-classification="internal"
                            data-folder="projects"
                            data-project="DEV-GATEWAY-v4"
                            data-custodian="Dana Yermak (EMP-1017)"
                            data-size="3.1 MB"
                            data-date="2026-03-12"
                            data-system="Developer Portal"
                            data-status="Approved"
                            data-hash="554433221100aabbccddeeff99887766554433221100aabbccddeeff99887766">
                            <td><code>DOC-2026-010</code></td>
                            <td>
                                <div class="fc-text-primary-bold" >API_Integration_Guide.pdf</div>
                                <div class="fc-text-muted-11" >REST &amp; gRPC endpoints protocol for partner systems</div>
                            </td>
                            <td><span class="vk-tag vk-tag-internal">Internal</span></td>
                            <td><span class="vk-tag fc-text-10">Developer Portal</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td class="fc-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-outline btn-inspect-doc">Inspect</button>
                            </td>
                        </tr>

                        <!-- DOC-2026-011: Highly Confidential -->
                        <tr class="repo-doc-row vk-table-row-highly-confidential"
                            data-doc-id="DOC-2026-011"
                            data-doc-name="Disaster_Recovery_Plan.pdf"
                            data-classification="highly-confidential"
                            data-folder="governance"
                            data-project="BCP-DR-2026"
                            data-custodian="Elena Morozova (EMP-1004)"
                            data-size="5.2 MB"
                            data-date="2026-02-18"
                            data-system="IT Helpdesk"
                            data-status="Approved"
                            data-hash="feefeeddccbbaa99887766554433221100feefeeddccbbaa9988776655443322">
                            <td><code>DOC-2026-011</code></td>
                            <td>
                                <div class="fc-text-primary-bold" >Disaster_Recovery_Plan.pdf</div>
                                <div class="fc-text-muted-11" >Cold site failover &amp; Almaty datastore replication runbook</div>
                            </td>
                            <td><span class="vk-tag vk-tag-highly-confidential">Highly Confidential</span></td>
                            <td><span class="vk-tag fc-text-10">IT Helpdesk</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td class="fc-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-outline btn-inspect-doc">Inspect</button>
                            </td>
                        </tr>

                        <!-- DOC-2026-012: Highly Confidential -->
                        <tr class="repo-doc-row vk-table-row-highly-confidential"
                            data-doc-id="DOC-2026-012"
                            data-doc-name="Annual_Corporate_Budget_2026.xlsx"
                            data-classification="highly-confidential"
                            data-folder="finance"
                            data-project="CORP-FIN-2026"
                            data-custodian="Daniel Weber (EMP-1003)"
                            data-size="4.1 MB"
                            data-date="2026-01-02"
                            data-system="Finance"
                            data-status="Approved"
                            data-hash="99887766554433221100feefeeddccbbaa99887766554433221100feefeeddcc">
                            <td><code>DOC-2026-012</code></td>
                            <td>
                                <div class="fc-text-primary-bold" >Annual_Corporate_Budget_2026.xlsx</div>
                                <div class="fc-text-muted-11" >Capital allocation &bull; Executive board authorization only</div>
                            </td>
                            <td><span class="vk-tag vk-tag-highly-confidential">Highly Confidential</span></td>
                            <td><span class="vk-tag fc-text-10">Finance</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td class="fc-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-outline btn-inspect-doc">Inspect</button>
                            </td>
                        </tr>

                        <!-- DOC-2026-013: Internal -->
                        <tr class="repo-doc-row vk-table-row-internal"
                            data-doc-id="DOC-2026-013"
                            data-doc-name="Customer_Service_Handbook.pdf"
                            data-classification="internal"
                            data-folder="hr"
                            data-project="INT-TRAIN-2026"
                            data-custodian="Sara Lindholm (EMP-1007)"
                            data-size="2.4 MB"
                            data-date="2026-02-10"
                            data-system="Intranet"
                            data-status="Approved"
                            data-hash="11223344556677889900aabbccddeeff11223344556677889900aabbccddeeff">
                            <td><code>DOC-2026-013</code></td>
                            <td>
                                <div class="fc-text-primary-bold" >Customer_Service_Handbook.pdf</div>
                                <div class="fc-text-muted-11" >Operational guidelines for regional account liaisons</div>
                            </td>
                            <td><span class="vk-tag vk-tag-internal">Internal</span></td>
                            <td><span class="vk-tag fc-text-10">Intranet</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td class="fc-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-outline btn-inspect-doc">Inspect</button>
                            </td>
                        </tr>

                        <!-- DOC-2026-014: Confidential -->
                        <tr class="repo-doc-row vk-table-row-confidential"
                            data-doc-id="DOC-2026-014"
                            data-doc-name="PRJ-2026-007_Test_Report.pdf"
                            data-classification="confidential"
                            data-folder="projects"
                            data-project="PRJ-2026-007 (PetroKaz)"
                            data-custodian="Farida Iskakova (EMP-1019)"
                            data-size="3.6 MB"
                            data-date="2026-08-20"
                            data-system="File Center"
                            data-status="Approved"
                            data-hash="3344556677889900aabbccddeeff11223344556677889900aabbccddeeff1122">
                            <td><code>DOC-2026-014</code></td>
                            <td>
                                <div class="fc-text-primary-bold" >PRJ-2026-007_Test_Report.pdf</div>
                                <div class="fc-text-muted-11" >Seismic Vibration Array &bull; Acceptance Testing Certificate</div>
                            </td>
                            <td><span class="vk-tag vk-tag-confidential">Confidential</span></td>
                            <td><span class="vk-tag fc-text-10">File Center</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td class="fc-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-outline btn-inspect-doc">Inspect</button>
                            </td>
                        </tr>

                        <!-- DOC-2026-015: Highly Confidential -->
                        <tr class="repo-doc-row vk-table-row-highly-confidential"
                            data-doc-id="DOC-2026-015"
                            data-doc-name="Board_Risk_Register_2026.xlsx"
                            data-classification="highly-confidential"
                            data-folder="governance"
                            data-project="BOARD-RISK-2026"
                            data-custodian="Timur Akhmetov (EMP-1005)"
                            data-size="2.7 MB"
                            data-date="2026-09-05"
                            data-system="Admin &amp; Governance"
                            data-status="Approved"
                            data-hash="bbccddeeff00112233445566778899aabbccddeeff00112233445566778899aa">
                            <td><code>DOC-2026-015</code></td>
                            <td>
                                <div class="fc-text-primary-bold" >Board_Risk_Register_2026.xlsx</div>
                                <div class="fc-text-muted-11" >Statutory enterprise risk matrix &bull; Board of Directors</div>
                            </td>
                            <td><span class="vk-tag vk-tag-highly-confidential">Highly Confidential</span></td>
                            <td><span class="vk-tag fc-text-10">Admin &amp; Gov</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td class="fc-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-outline btn-inspect-doc">Inspect</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- SLIDE-OVER DOCUMENT INSPECTION DRAWER -->
    <div class="doc-drawer-backdrop" id="doc-drawer-backdrop">
        <div class="doc-drawer" id="doc-drawer">
            <div class="doc-drawer-header">
                <div>
                    <div class="fc-display-flex-align-items-0b62" >
                        <span id="drawer-class-badge" class="vk-tag vk-tag-highly-confidential">HIGHLY CONFIDENTIAL</span>
                        <span class="fc-mono-muted-11"  id="drawer-doc-id">DOC-2026-004</span>
                    </div>
                    <div class="fc-font-family-var-font-3629"  id="drawer-doc-name">
                        PRJ-2026-002_Integration_Specification.pdf
                    </div>
                </div>
                <button class="fc-background-none-border-none-3e7e" type="button" id="btn-close-drawer" >
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <div class="doc-drawer-body">
                <div class="doc-meta-item">
                    <div class="doc-meta-label">Associated Project</div>
                    <div class="doc-meta-val" id="drawer-doc-project">PRJ-2026-002 (BaltNord Process Systems)</div>
                </div>

                <div class="doc-meta-item">
                    <div class="doc-meta-label">Designated Lead Custodian</div>
                    <div class="doc-meta-val" id="drawer-doc-custodian">Farida Iskakova (EMP-1019)</div>
                </div>

                <div class="fc-display-grid-grid-template-38f6" >
                    <div class="doc-meta-item">
                        <div class="doc-meta-label">File Size</div>
                        <div class="doc-meta-val" id="drawer-doc-size">4.5 MB</div>
                    </div>
                    <div class="doc-meta-item">
                        <div class="doc-meta-label">Last Ingested</div>
                        <div class="doc-meta-val" id="drawer-doc-date">2026-09-11</div>
                    </div>
                </div>

                <div class="fc-display-grid-grid-template-38f6" >
                    <div class="doc-meta-item">
                        <div class="doc-meta-label">Originating System</div>
                        <div class="doc-meta-val" id="drawer-doc-system">File Center</div>
                    </div>
                    <div class="doc-meta-item">
                        <div class="doc-meta-label">Lifecycle Status</div>
                        <div class="doc-meta-val" id="drawer-doc-status">In Review</div>
                    </div>
                </div>

                <div class="doc-meta-item">
                    <div class="doc-meta-label">SHA-256 Checksum (Hardware Seal)</div>
                    <div class="hash-code-block">
                        <span id="drawer-doc-hash">9f8e7d6c5b4a3928170192837465abcdeffedcba98765432101234567890fedc</span>
                        <button class="fc-background-none-border-none-3e7e" type="button" id="btn-copy-drawer-hash"  title="Copy Checksum">
                            <span class="material-symbols-outlined text-[16px]">content_copy</span>
                        </button>
                    </div>
                </div>

                <div class="fc-border-top-1px-solid-0abf" >
                    <div class="doc-meta-label fc-margin-bottom-8px-ccd7">Access Control &amp; Redaction Rule</div>
                    <p class="fc-font-size-12px-color-d8b2" >
                        Direct file extraction restricted to L3+ engineering clearance. For external client synchronization, access must be routed through the redaction approval pipeline.
                    </p>
                </div>

                <div class="fc-display-flex-gap-10px-a570" >
                    <button class="vk-btn vk-btn-primary" id="btn-drawer-download" type="button" >
                        <span class="material-symbols-outlined text-[16px]">download</span> Decrypt &amp; Download
                    </button>
                    <a class="vk-btn vk-btn-outline" href="approvals.php">
                        <span class="material-symbols-outlined text-[16px]">draw</span> Workflow
                    </a>
                </div>
            </div>
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

    <script src="js/fc-common.js"></script>
    <script src="js/fc-repo.js"></script>
</body>

</html>