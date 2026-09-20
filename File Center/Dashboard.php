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
        <div style="display: flex; align-items: center; gap: 24px;">
            <a class="vk-brand-section" href="index.php">
                <img alt="VOSTOKPRIBOR Official Mark" class="brand-logo-img" style="height: 30px; width: 30px; object-fit: contain;" src="https://lh3.googleusercontent.com/aida/AEtjO1XMGDZ9meLzj0XkVJ6C4Xv2AoEzKtMyFqF7KQ8eMADmbywzsRZ7VF4Em6pQ7fZ8QRJExCZedCKEUUo1fN1LpEmGsQva25blyUsGhOPvX2vv2cHGkppzOp9iT33Xy2n4Nkr5e_YY_0J78vA8Q7vKUpViCouPJo13HFVvW5olf7QEFzU3EX-WCqc0SYyPXZb7E11PafOMC_KprpG6Tre6bN_DyZS-CI-J5qiCgBtikqVNYGueATNgWbi73Q" />
                <div style="display: flex; flex-direction: column;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-family: var(--font-heading); font-weight: 700; font-size: 15px; letter-spacing: -0.02em;">VOSTOKPRIBOR</span>
                        <span class="vk-system-badge">SYS-09 // FILE-CENTER</span>
                    </div>
                    <span style="font-family: var(--font-mono); font-size: 10px; color: #94A3B8;">ALMATY CENTRAL • EST. 1968 • DOCUMENT VAULT v3.8.2</span>
                </div>
            </a>
            <div style="display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.06); padding: 4px 12px; border-radius: var(--radius-sm); border: 1px solid rgba(255,255,255,0.1);">
                <span class="material-symbols-outlined text-[14px]" style="color: var(--vk-sys-accent);">folder_special</span>
                <span style="font-family: var(--font-mono); font-size: 11px; color: #E2E8F0;">VAULT: <strong>CENTRAL DOCUMENT REPOSITORY</strong></span>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 16px;">
            <button class="search-trigger-btn" type="button">
                <span class="material-symbols-outlined text-[16px]">search</span>
                <span>Search documents, DOC-IDs...</span>
                <span class="kbd-shortcut">Ctrl K</span>
            </button>
            <div style="display: flex; align-items: center; gap: 6px; font-family: var(--font-mono); font-size: 11px; color: #94A3B8; background: rgba(0,0,0,0.25); padding: 4px 10px; border-radius: var(--radius-sm);">
                <span class="material-symbols-outlined text-[14px]" style="color: var(--vk-secondary);">schedule</span>
                <span class="station-live-clock">17:42:00 UTC+6</span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; padding-left: 12px; border-left: 1px solid rgba(255,255,255,0.15);">
                <div style="text-align: right;">
                    <div style="font-size: 12px; font-weight: 600; color: #ffffff;">Farida Iskakova</div>
                    <div style="font-family: var(--font-mono); font-size: 10px; color: #CBD5E1;">EMP-1019 • Lead Custodian</div>
                </div>
                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--vk-primary); display: flex; align-items: center; justify-content: center; border: 1px solid var(--vk-sys-accent);">
                    <span class="material-symbols-outlined text-[18px]" style="color: #ffffff;">folder_managed</span>
                </div>
            </div>
        </div>
    </header>

    <!-- LEFT SIDEBAR (Authenticated System 09) -->
    <aside class="vk-sidebar">
        <div class="vk-sidebar-nav">
            <div class="vk-sidebar-header">Document Vault</div>
            <a class="vk-nav-item active" href="index.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]">folder_open</span>
                    <span>Document Repository</span>
                </div>
                <span class="nav-badge">15</span>
            </a>
            <a class="vk-nav-item" href="approvals.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]">assignment_turned_in</span>
                    <span>Signoff &amp; Approvals</span>
                </div>
                <span class="vk-tag vk-tag-highly-confidential" style="font-size: 9px; padding: 1px 5px;">1 ACTION</span>
            </a>
            <a class="vk-nav-item" href="upload.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]">upload_file</span>
                    <span>Secure Ingestion</span>
                </div>
            </a>
            <a class="vk-nav-item" href="retention.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                    <span>Retention &amp; Holds</span>
                </div>
            </a>
            <a class="vk-nav-item" href="audit.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]">fingerprint</span>
                    <span>Integrity Ledger</span>
                </div>
            </a>

                        <div class="vk-sidebar-header" style="margin-top: 20px;">System Integrations</div>
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
            <a class="vk-nav-item" href="../Developer/index.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]" style="color: #1E8FA6;">terminal</span>
                    <span>Developer / API Portal</span>
                </div>
                <span class="vk-tag" style="font-size: 9px; background: rgba(30,143,166,0.1); color: #1E8FA6; border: 1px solid #1E8FA6;">SYS-10</span>
            </a>
            <a class="vk-nav-item" href="../Admin & Governance Portal/index.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]" style="color: var(--vk-alert);">shield</span>
                    <span>Admin &amp; Governance</span>
                </div>
                <span class="vk-tag" style="font-size: 9px; background: rgba(178,58,50,0.1); color: var(--vk-alert); border: 1px solid var(--vk-alert);">SYS-11</span>
            </a>
        </div>

        <div style="padding: 16px; border-top: 1px solid var(--vk-neutral-200); background: #ffffff;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <span class="status-dot-pulse"></span>
                <span style="font-family: var(--font-mono); font-size: 11px; font-weight: 600; color: var(--vk-neutral-900);">ENCLAVE HSM ONLINE</span>
            </div>
            <div style="font-family: var(--font-mono); font-size: 11px; color: var(--vk-neutral-600);">Node: files.vostokpribor.local</div>
            <div style="font-family: var(--font-mono); font-size: 10px; color: var(--vk-neutral-600); margin-top: 4px;">FIPS 140-3 Hardware Sealed</div>
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
        <div class="repo-header-toolbar">
            <div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
                    <span class="vk-tag" style="background: var(--vk-sys-badge-bg); color: var(--vk-primary-dark); border: 1px solid var(--vk-sys-accent);">
                        SYSTEM 09 // GRAPHITE #5A6470
                    </span>
                    <span style="font-family: var(--font-mono); font-size: 12px; color: var(--vk-neutral-600);">FQDN: files.vostokpribor.local</span>
                </div>
                <h1 style="font-size: 26px; font-weight: 700; color: var(--vk-primary); margin: 0; display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined" style="font-size: 28px; color: var(--vk-sys-accent);">source_environment</span>
                    Central Enterprise Document Repository
                </h1>
                <p style="color: var(--vk-neutral-600); font-size: 14px; margin: 4px 0 0 0;">
                    Authoritative archival store, technical specifications, bilateral customer contracts, and regulatory audit records for VOSTOKPRIBOR.
                </p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
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
                    <span class="material-symbols-outlined text-[18px]" style="color: var(--vk-sys-accent);">description</span>
                </div>
                <div class="repo-stat-value">1,842</div>
                <div class="repo-stat-subtext" style="color: var(--vk-secondary); display: flex; align-items: center; gap: 4px;">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> 15 Core Baseline Manifest
                </div>
            </div>

            <div class="repo-stat-card">
                <div class="repo-stat-label">
                    <span>Encrypted Vault Volume</span>
                    <span class="material-symbols-outlined text-[18px]" style="color: var(--vk-secondary);">lock</span>
                </div>
                <div class="repo-stat-value">842.6 <span style="font-size: 14px; font-weight: 400; color: var(--vk-neutral-600);">GB</span></div>
                <div class="repo-stat-subtext">AES-256-GCM hardware envelope</div>
            </div>

            <div class="repo-stat-card">
                <div class="repo-stat-label">
                    <span>Pending Approvals</span>
                    <span class="material-symbols-outlined text-[18px]" style="color: var(--vk-accent-cta);">pending_actions</span>
                </div>
                <div class="repo-stat-value" style="color: var(--vk-accent-cta);">1 Action</div>
                <div class="repo-stat-subtext">DOC-2026-004 awaiting Farida Iskakova</div>
            </div>

            <div class="repo-stat-card">
                <div class="repo-stat-label">
                    <span>Integrity Verification</span>
                    <span class="material-symbols-outlined text-[18px]" style="color: var(--vk-secondary);">verified</span>
                </div>
                <div class="repo-stat-value" style="color: #2E6E4E;">100.0%</div>
                <div class="repo-stat-subtext">All SHA-256 anchors matched</div>
            </div>
        </div>

        <!-- FILTER & SEARCH BAR -->
        <div class="repo-filter-bar">
            <div class="filter-pills-group">
                <button class="filter-pill active" data-class-filter="all">
                    <span>All Classifications</span>
                    <span style="font-family: var(--font-mono); font-size: 11px;">(15)</span>
                </button>
                <button class="filter-pill pill-highly-confidential" data-class-filter="highly-confidential">
                    <span class="material-symbols-outlined text-[14px]" style="color: var(--vk-class-highly-confidential);">lock</span>
                    <span>Highly Confidential</span>
                    <span style="font-family: var(--font-mono); font-size: 11px;">(6)</span>
                </button>
                <button class="filter-pill pill-confidential" data-class-filter="confidential">
                    <span class="material-symbols-outlined text-[14px]" style="color: var(--vk-class-confidential);">shield</span>
                    <span>Confidential</span>
                    <span style="font-family: var(--font-mono); font-size: 11px;">(6)</span>
                </button>
                <button class="filter-pill pill-internal" data-class-filter="internal">
                    <span class="material-symbols-outlined text-[14px]" style="color: var(--vk-class-internal);">corporate_fare</span>
                    <span>Internal</span>
                    <span style="font-family: var(--font-mono); font-size: 11px;">(2)</span>
                </button>
                <button class="filter-pill pill-public" data-class-filter="public">
                    <span class="material-symbols-outlined text-[14px]" style="color: var(--vk-class-public);">public</span>
                    <span>Public</span>
                    <span style="font-family: var(--font-mono); font-size: 11px;">(1)</span>
                </button>
            </div>

            <div class="repo-search-box">
                <span class="material-symbols-outlined text-[16px]" style="color: var(--vk-neutral-600);">search</span>
                <input class="repo-search-input" id="repo-search-input" type="text" placeholder="Filter DOC-ID, name, project..." />
            </div>
        </div>

        <!-- REPOSITORY WORKSPACE: FOLDER TREE + DOCUMENT TABLE -->
        <div class="repo-workspace-grid">
            <!-- LEFT: FOLDER TREE -->
            <div class="folder-tree-card">
                <div class="folder-tree-title">
                    <span class="material-symbols-outlined text-[16px]" style="color: var(--vk-sys-accent);">account_tree</span>
                    <span>Partitions</span>
                </div>
                <div class="folder-item active" data-folder-slug="all">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="material-symbols-outlined text-[16px]">folder</span>
                        <span>All Partitions</span>
                    </div>
                    <span style="font-family: var(--font-mono); font-size: 10px;">15</span>
                </div>
                <div class="folder-item" data-folder-slug="governance">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="material-symbols-outlined text-[16px]">gavel</span>
                        <span>/Corporate/Gov</span>
                    </div>
                    <span style="font-family: var(--font-mono); font-size: 10px;">3</span>
                </div>
                <div class="folder-item" data-folder-slug="projects">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="material-symbols-outlined text-[16px]">precision_manufacturing</span>
                        <span>/Projects/Eng</span>
                    </div>
                    <span style="font-family: var(--font-mono); font-size: 10px;">4</span>
                </div>
                <div class="folder-item" data-folder-slug="contracts">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="material-symbols-outlined text-[16px]">handshake</span>
                        <span>/Commercial/SOW</span>
                    </div>
                    <span style="font-family: var(--font-mono); font-size: 10px;">2</span>
                </div>
                <div class="folder-item" data-folder-slug="finance">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="material-symbols-outlined text-[16px]">payments</span>
                        <span>/Finance/Billing</span>
                    </div>
                    <span style="font-family: var(--font-mono); font-size: 10px;">2</span>
                </div>
                <div class="folder-item" data-folder-slug="hr">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="material-symbols-outlined text-[16px]">badge</span>
                        <span>/HR/Personnel</span>
                    </div>
                    <span style="font-family: var(--font-mono); font-size: 10px;">2</span>
                </div>
                <div class="folder-item" data-folder-slug="operations">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="material-symbols-outlined text-[16px]">local_shipping</span>
                        <span>/Operations/Suppliers</span>
                    </div>
                    <span style="font-family: var(--font-mono); font-size: 10px;">2</span>
                </div>

                <div style="margin-top: 20px; padding-top: 14px; border-top: 1px solid var(--vk-neutral-200);">
                    <div style="font-family: var(--font-mono); font-size: 10px; color: var(--vk-neutral-600); text-transform: uppercase;">Storage Quota</div>
                    <div style="height: 6px; background: var(--vk-neutral-200); border-radius: 3px; overflow: hidden; margin-top: 6px;">
                        <div style="width: 17.5%; height: 100%; background: var(--vk-sys-accent);"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-family: var(--font-mono); font-size: 10px; color: var(--vk-neutral-600); margin-top: 4px;">
                        <span>842 GB used</span>
                        <span>4.8 TB cap</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT: MAIN DOCUMENT TABLE (15 Official Documents from PDF) -->
            <div class="vk-table-container">
                <div style="padding: 12px 16px; background: #F8FAFC; border-bottom: 1px solid var(--vk-neutral-200); display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-family: var(--font-heading); font-size: 13px; font-weight: 700; color: var(--vk-primary);">
                        OFFICIAL DOCUMENT REGISTER (DOC-2026-001 TO DOC-2026-015)
                    </div>
                    <div style="font-family: var(--font-mono); font-size: 11px; color: var(--vk-neutral-600);">
                        Showing <span id="visible-docs-count">15</span> documents
                    </div>
                </div>
                <table class="vk-table">
                    <thead>
                        <tr>
                            <th style="width: 130px;">DOC-ID</th>
                            <th>Filename &amp; Description</th>
                            <th style="width: 170px;">Classification</th>
                            <th style="width: 140px;">System Tag</th>
                            <th style="width: 110px;">Status</th>
                            <th style="width: 90px; text-align: right;">Action</th>
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
                                <div style="font-weight: 600; color: var(--vk-primary);">Corporate_Information_Security_Policy.pdf</div>
                                <div style="font-size: 11px; color: var(--vk-neutral-600);">Master enterprise cybersecurity charter &amp; access policy</div>
                            </td>
                            <td><span class="vk-tag vk-tag-highly-confidential">Highly Confidential</span></td>
                            <td><span class="vk-tag" style="font-size: 10px;">Admin &amp; Gov</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td style="text-align: right;">
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
                                <div style="font-weight: 600; color: var(--vk-primary);">Customer_Onboarding_Standard.pdf</div>
                                <div style="font-size: 11px; color: var(--vk-neutral-600);">Commercial account vetting protocol &amp; KYC</div>
                            </td>
                            <td><span class="vk-tag vk-tag-confidential">Confidential</span></td>
                            <td><span class="vk-tag" style="font-size: 10px;">CRM</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td style="text-align: right;">
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
                                <div style="font-weight: 600; color: var(--vk-primary);">PRJ-2026-001_Statement_of_Work.pdf</div>
                                <div style="font-size: 11px; color: var(--vk-neutral-600);">Aral Geomatics Group &bull; Optical Sensor Integration SOW</div>
                            </td>
                            <td><span class="vk-tag vk-tag-confidential">Confidential</span></td>
                            <td><span class="vk-tag" style="font-size: 10px;">File Center</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td style="text-align: right;">
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
                                <div style="font-weight: 700; color: var(--vk-alert); display: flex; align-items: center; gap: 6px;">
                                    <span>PRJ-2026-002_Integration_Specification.pdf</span>
                                    <span class="vk-tag" style="background: #FEE2E2; color: #991B1B; font-size: 9px;">ACTION NEEDED</span>
                                </div>
                                <div style="font-size: 11px; color: var(--vk-neutral-600);">BaltNord SCADA Ingestion &bull; Reviewed by Farida Iskakova</div>
                            </td>
                            <td><span class="vk-tag vk-tag-highly-confidential">Highly Confidential</span></td>
                            <td><span class="vk-tag" style="font-size: 10px;">File Center</span></td>
                            <td><span class="vk-status-badge status-in-review">In Review</span></td>
                            <td style="text-align: right;">
                                <a class="vk-btn vk-btn-sm vk-btn-primary" href="approvals.php" style="padding: 2px 8px; font-size: 11px;">Sign</a>
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
                                <div style="font-weight: 600; color: var(--vk-primary);">INV-2026-002_Billing_Record.pdf</div>
                                <div style="font-size: 11px; color: var(--vk-neutral-600);">BaltNord Milestone 1 billing attestation (€120,000)</div>
                            </td>
                            <td><span class="vk-tag vk-tag-confidential">Confidential</span></td>
                            <td><span class="vk-tag" style="font-size: 10px;">Finance</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td style="text-align: right;">
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
                                <div style="font-weight: 600; color: var(--vk-primary);">Employee_Onboarding_Procedure.pdf</div>
                                <div style="font-size: 11px; color: var(--vk-neutral-600);">Standard Operating Procedure &bull; SOP-05 HR Enrollment</div>
                            </td>
                            <td><span class="vk-tag vk-tag-confidential">Confidential</span></td>
                            <td><span class="vk-tag" style="font-size: 10px;">HR</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td style="text-align: right;">
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
                                <div style="font-weight: 600; color: var(--vk-primary);">Employee_Access_Matrix.xlsx</div>
                                <div style="font-size: 11px; color: var(--vk-neutral-600);">Complete RBAC and clearance register for 95 employees</div>
                            </td>
                            <td><span class="vk-tag vk-tag-highly-confidential">Highly Confidential</span></td>
                            <td><span class="vk-tag" style="font-size: 10px;">Admin &amp; Gov</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td style="text-align: right;">
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
                                <div style="font-weight: 600; color: var(--vk-primary);">Supplier_Evaluation_2026.pdf</div>
                                <div style="font-size: 11px; color: var(--vk-neutral-600);">Tier-1 industrial transducer vendor scorecard</div>
                            </td>
                            <td><span class="vk-tag vk-tag-confidential">Confidential</span></td>
                            <td><span class="vk-tag" style="font-size: 10px;">Operations</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td style="text-align: right;">
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
                                <div style="font-weight: 600; color: var(--vk-primary);">Optical_Sensor_Product_Catalog.pdf</div>
                                <div style="font-size: 11px; color: var(--vk-neutral-600);">Standard B2B product specifications &bull; Public distribution</div>
                            </td>
                            <td><span class="vk-tag vk-tag-public">Public</span></td>
                            <td><span class="vk-tag" style="font-size: 10px;">E-Commerce</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td style="text-align: right;">
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
                                <div style="font-weight: 600; color: var(--vk-primary);">API_Integration_Guide.pdf</div>
                                <div style="font-size: 11px; color: var(--vk-neutral-600);">REST &amp; gRPC endpoints protocol for partner systems</div>
                            </td>
                            <td><span class="vk-tag vk-tag-internal">Internal</span></td>
                            <td><span class="vk-tag" style="font-size: 10px;">Developer Portal</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td style="text-align: right;">
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
                                <div style="font-weight: 600; color: var(--vk-primary);">Disaster_Recovery_Plan.pdf</div>
                                <div style="font-size: 11px; color: var(--vk-neutral-600);">Cold site failover &amp; Almaty datastore replication runbook</div>
                            </td>
                            <td><span class="vk-tag vk-tag-highly-confidential">Highly Confidential</span></td>
                            <td><span class="vk-tag" style="font-size: 10px;">IT Helpdesk</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td style="text-align: right;">
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
                                <div style="font-weight: 600; color: var(--vk-primary);">Annual_Corporate_Budget_2026.xlsx</div>
                                <div style="font-size: 11px; color: var(--vk-neutral-600);">Capital allocation &bull; Executive board authorization only</div>
                            </td>
                            <td><span class="vk-tag vk-tag-highly-confidential">Highly Confidential</span></td>
                            <td><span class="vk-tag" style="font-size: 10px;">Finance</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td style="text-align: right;">
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
                                <div style="font-weight: 600; color: var(--vk-primary);">Customer_Service_Handbook.pdf</div>
                                <div style="font-size: 11px; color: var(--vk-neutral-600);">Operational guidelines for regional account liaisons</div>
                            </td>
                            <td><span class="vk-tag vk-tag-internal">Internal</span></td>
                            <td><span class="vk-tag" style="font-size: 10px;">Intranet</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td style="text-align: right;">
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
                                <div style="font-weight: 600; color: var(--vk-primary);">PRJ-2026-007_Test_Report.pdf</div>
                                <div style="font-size: 11px; color: var(--vk-neutral-600);">Seismic Vibration Array &bull; Acceptance Testing Certificate</div>
                            </td>
                            <td><span class="vk-tag vk-tag-confidential">Confidential</span></td>
                            <td><span class="vk-tag" style="font-size: 10px;">File Center</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td style="text-align: right;">
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
                                <div style="font-weight: 600; color: var(--vk-primary);">Board_Risk_Register_2026.xlsx</div>
                                <div style="font-size: 11px; color: var(--vk-neutral-600);">Statutory enterprise risk matrix &bull; Board of Directors</div>
                            </td>
                            <td><span class="vk-tag vk-tag-highly-confidential">Highly Confidential</span></td>
                            <td><span class="vk-tag" style="font-size: 10px;">Admin &amp; Gov</span></td>
                            <td><span class="vk-status-badge status-approved">Approved</span></td>
                            <td style="text-align: right;">
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
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <span id="drawer-class-badge" class="vk-tag vk-tag-highly-confidential">HIGHLY CONFIDENTIAL</span>
                        <span style="font-family: var(--font-mono); font-size: 11px; color: var(--vk-neutral-600);" id="drawer-doc-id">DOC-2026-004</span>
                    </div>
                    <div style="font-family: var(--font-heading); font-size: 16px; font-weight: 700; color: var(--vk-primary);" id="drawer-doc-name">
                        PRJ-2026-002_Integration_Specification.pdf
                    </div>
                </div>
                <button type="button" id="btn-close-drawer" style="background: none; border: none; cursor: pointer; color: var(--vk-neutral-600);">
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

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="doc-meta-item">
                        <div class="doc-meta-label">File Size</div>
                        <div class="doc-meta-val" id="drawer-doc-size">4.5 MB</div>
                    </div>
                    <div class="doc-meta-item">
                        <div class="doc-meta-label">Last Ingested</div>
                        <div class="doc-meta-val" id="drawer-doc-date">2026-09-11</div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
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
                        <button type="button" id="btn-copy-drawer-hash" style="background: none; border: none; cursor: pointer; color: var(--vk-neutral-600);" title="Copy Checksum">
                            <span class="material-symbols-outlined text-[16px]">content_copy</span>
                        </button>
                    </div>
                </div>

                <div style="border-top: 1px solid var(--vk-neutral-200); padding-top: 16px;">
                    <div class="doc-meta-label" style="margin-bottom: 8px;">Access Control &amp; Redaction Rule</div>
                    <p style="font-size: 12px; color: var(--vk-neutral-600); line-height: 1.6;">
                        Direct file extraction restricted to L3+ engineering clearance. For external client synchronization, access must be routed through the redaction approval pipeline.
                    </p>
                </div>

                <div style="display: flex; gap: 10px; margin-top: auto;">
                    <button class="vk-btn vk-btn-primary" id="btn-drawer-download" type="button" style="flex: 1;">
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
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="status-dot-pulse"></span>
                <span>ALMATY-VAULT-01 // HSM CLUSTER SYNCHRONIZED</span>
            </div>
            <span>VOLUME: 842.6 GB / 4.8 TB (17.5%)</span>
        </div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <span>ACTIVE SENSITIVITY ENCLAVE: FOUR-TIER RBAC</span>
            <span>IEC 62443 / ISO 27001 AUDIT COMPLIANT</span>
        </div>
    </footer>

    <!-- Universal Command Palette Modal (Ctrl + K) -->
    <div class="cmd-palette-backdrop" id="cmd-palette-modal">
        <div class="cmd-palette-box">
            <div class="cmd-palette-header">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--vk-sys-accent);">folder_managed</span>
                <input class="cmd-palette-input" id="cmd-palette-input" type="text" placeholder="Type a document ID, name, or jump to view..." />
            </div>
            <div class="cmd-palette-list" id="cmd-palette-results">
                <a class="cmd-palette-item" href="index.php">
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
