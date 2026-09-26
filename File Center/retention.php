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
    <title>Retention &amp; Legal Holds - VOSTOKPRIBOR File Center</title>
    <link rel="stylesheet" href="css/fc-tokens.css" />
    <link rel="stylesheet" href="css/fc-common.css" />
    <link rel="stylesheet" href="css/fc-retention.css" />
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
                <span class="material-symbols-outlined text-[14px] fc-color-accent">inventory_2</span>
                <span class="fc-font-family-var-font-eb0b" >ARCHIVE: <strong>STATUTORY RETENTION &amp; LEGAL HOLDS</strong></span>
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
                <span class="station-live-clock">17:48:00 UTC+6</span>
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
            <a class="vk-nav-item" href="upload.php">
                <div class="fc-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">upload_file</span>
                    <span>Secure Ingestion</span>
                </div>
            </a>
            <a class="vk-nav-item active" href="retention.php">
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
                <span class="fc-font-family-var-font-1ab9" >ARCHIVE GOVERNANCE</span>
            </div>
            <div class="fc-mono-muted-11" >Auto Purge: Inactive (Hold)</div>
            <div class="fc-font-family-var-font-940f" >Standard: ISO 27001 §A.8.3</div>
        </div>

        <!-- Log Out -->

    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="vk-main-layout">
        <!-- HEADER BLOCK -->
        <div class="fc-display-flex-justify-content-f610" >
            <div>
                <div class="fc-display-flex-align-items-9bb7" >
                    <span class="vk-tag vk-tag-internal">CLASSIFICATION: INTERNAL // #3E7CB1</span>
                    <span class="fc-mono-muted-12" >POLICY: RETENTION-SCHEDULE-v2</span>
                </div>
                <h1 class="fc-font-size-26px-font-5041" >
                    <span class="material-symbols-outlined fc-font-size-28px-color-a4d3">inventory_2</span>
                    Archival Governance, Retention Lifecycle &amp; Legal Holds
                </h1>
                <p class="fc-color-var-vk-neutral-5a07" >
                    Statutory preservation timelines, litigation legal holds, and immutable cold archival quotas compliant with Kazakhstani Industrial Standards.
                </p>
            </div>
            <div>
                <button class="vk-btn vk-btn-outline" id="btn-export-archival-manifest" type="button">
                    <span class="material-symbols-outlined text-[16px]">download</span> Export Archival Manifest
                </button>
            </div>
        </div>

        <!-- 3 STORAGE VOLUME ALLOCATION GAUGES -->
        <div class="retention-meter-grid">
            <div class="retention-meter-card">
                <div class="fc-flex-between-center" >
                    <span class="fc-font-family-var-font-a98c" >Hot NVMe Vault</span>
                    <span class="material-symbols-outlined text-[16px] fc-color-accent">flash_on</span>
                </div>
                <div class="fc-font-family-var-font-8611" >
                    412.4 <span class="fc-font-size-13px-font-30a9" >GB / 1.0 TB</span>
                </div>
                <div class="retention-progress-bar">
                    <div class="fc-width-41-2-height-c9b3" ></div>
                </div>
                <div class="fc-text-muted-11" >Active projects &bull; Real-time access (&lt;5ms)</div>
            </div>

            <div class="retention-meter-card">
                <div class="fc-flex-between-center" >
                    <span class="fc-font-family-var-font-a98c" >Warm Nearline Store</span>
                    <span class="material-symbols-outlined text-[16px] fc-color-secondary">storage</span>
                </div>
                <div class="fc-font-family-var-font-8611" >
                    430.2 <span class="fc-font-size-13px-font-30a9" >GB / 2.0 TB</span>
                </div>
                <div class="retention-progress-bar">
                    <div class="fc-width-21-5-height-23d4" ></div>
                </div>
                <div class="fc-text-muted-11" >Financial archives &bull; 7-year audit buffer</div>
            </div>

            <div class="retention-meter-card">
                <div class="fc-flex-between-center" >
                    <span class="fc-font-family-var-font-a98c" >Cold WORM Archive</span>
                    <span class="material-symbols-outlined text-[16px] fc-color-var-vk-primary-40d3">ac_unit</span>
                </div>
                <div class="fc-font-family-var-font-8611" >
                    1.28 <span class="fc-font-size-13px-font-30a9" >TB / 5.0 TB</span>
                </div>
                <div class="retention-progress-bar">
                    <div class="fc-width-25-6-height-a4a5" ></div>
                </div>
                <div class="fc-text-muted-11" >Tape &amp; Optical WORM &bull; Permanent holds</div>
            </div>
        </div>

        <!-- STATUTORY RETENTION POLICIES TABLE -->
        <div class="vk-card fc-margin-bottom-24px-dc2c">
            <div class="vk-card-header">
                <div>
                    <div class="vk-card-title">Statutory Retention Schedules (By Document Category)</div>
                    <div class="vk-card-subtitle">Automated lifecycle transitions enforced by Almaty Storage Controller</div>
                </div>
                <span class="vk-tag vk-tag-internal">ENFORCED BY HSM</span>
            </div>

            <div class="vk-card-body fc-p-0">
                <table class="vk-table">
                    <thead>
                        <tr>
                            <th class="fc-width-220px-d415" >Category</th>
                            <th class="fc-w-140" >Retention Scope</th>
                            <th class="fc-width-180px-21b9" >Legal / Standard Anchor</th>
                            <th>Disposition Action</th>
                            <th class="fc-width-120px-b15a" >Compliance Tier</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="vk-table-row-highly-confidential">
                            <td>
                                <div class="fc-text-primary-bold" >Corporate Charter &amp; Board Registers</div>
                                <div class="fc-text-muted-11" >DOC-2026-001, DOC-2026-015</div>
                            </td>
                            <td class="fc-font-family-var-font-d8ae" >Permanent</td>
                            <td class="fc-text-muted-12" >Kazakhstan Corporate Law §14</td>
                            <td class="fc-text-12" >WORM Immutable Archive &bull; Zero deletion permitted</td>
                            <td><span class="vk-tag vk-tag-highly-confidential">Highly Conf.</span></td>
                        </tr>

                        <tr class="vk-table-row-confidential">
                            <td>
                                <div class="fc-text-primary-bold" >SCADA Engineering &amp; Blueprints</div>
                                <div class="fc-text-muted-11" >DOC-2026-004, DOC-2026-014</div>
                            </td>
                            <td class="fc-font-family-var-font-5822" >10 Years</td>
                            <td class="fc-text-muted-12" >IEC 62443-4-2 §7.3</td>
                            <td class="fc-text-12" >Transition to Cold Archive after project closeout</td>
                            <td><span class="vk-tag vk-tag-confidential">Confidential</span></td>
                        </tr>

                        <tr class="vk-table-row-confidential">
                            <td>
                                <div class="fc-text-primary-bold" >Commercial Contracts &amp; Billing Invoices</div>
                                <div class="fc-text-muted-11" >DOC-2026-003, DOC-2026-005, DOC-2026-012</div>
                            </td>
                            <td class="fc-font-family-var-font-5822" >7 Years</td>
                            <td class="fc-text-muted-12" >Kazakhstan Tax Code §48</td>
                            <td class="fc-text-12" >Archive to Nearline &bull; Sealed against alteration</td>
                            <td><span class="vk-tag vk-tag-confidential">Confidential</span></td>
                        </tr>

                        <tr class="vk-table-row-internal">
                            <td>
                                <div class="fc-text-primary-bold" >HR Onboarding &amp; Personnel Records</div>
                                <div class="fc-text-muted-11" >DOC-2026-006, DOC-2026-013</div>
                            </td>
                            <td class="fc-font-family-var-font-5822" >5 Years</td>
                            <td class="fc-text-muted-12" >Kazakhstan Labor Code §63</td>
                            <td class="fc-text-12" >Automated purge after statutory expiration</td>
                            <td><span class="vk-tag vk-tag-internal">Internal</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ACTIVE LEGAL HOLDS & PRESERVATION ORDERS -->
        <div class="vk-card fc-mb-30">
            <div class="vk-card-header">
                <div>
                    <div class="vk-card-title">Active Legal Preservation Holds (Litigation &amp; Audit Freezes)</div>
                    <div class="vk-card-subtitle">Documents locked against automated purging or lifecycle transitions</div>
                </div>
                <span class="legal-hold-badge">
                    <span class="material-symbols-outlined text-[14px]">gavel</span> 3 ACTIVE HOLDS
                </span>
            </div>

            <div class="vk-card-body fc-p-0">
                <table class="vk-table">
                    <thead>
                        <tr>
                            <th class="fc-w-140" >DOC-ID</th>
                            <th>Target Document</th>
                            <th class="fc-width-180px-21b9" >Preservation Authority</th>
                            <th class="fc-width-160px-7534" >Enforced Since</th>
                            <th class="fc-width-160px-text-align-a6ce" >Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="vk-table-row-highly-confidential">
                            <td><code>DOC-2026-004</code></td>
                            <td>
                                <div class="fc-text-primary-bold" >PRJ-2026-002_Integration_Specification.pdf</div>
                                <div class="fc-text-muted-11" >BaltNord Process Systems SCADA bridge specification</div>
                            </td>
                            <td class="fc-text-12" >Farida Iskakova (EMP-1019)</td>
                            <td class="fc-mono-11" >2026-09-10 14:12</td>
                            <td class="fc-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-accent btn-toggle-hold active-hold" data-doc-id="DOC-2026-004" >
                                    <span class="material-symbols-outlined text-[14px]">lock</span> Hold Active
                                </button>
                            </td>
                        </tr>

                        <tr class="vk-table-row-highly-confidential">
                            <td><code>DOC-2026-007</code></td>
                            <td>
                                <div class="fc-text-primary-bold" >Employee_Access_Matrix.xlsx</div>
                                <div class="fc-text-muted-11" >Statutory access permissions &bull; Annual external audit</div>
                            </td>
                            <td class="fc-text-12" >Timur Akhmetov (EMP-1005)</td>
                            <td class="fc-mono-11" >2026-09-01 09:00</td>
                            <td class="fc-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-accent btn-toggle-hold active-hold" data-doc-id="DOC-2026-007" >
                                    <span class="material-symbols-outlined text-[14px]">lock</span> Hold Active
                                </button>
                            </td>
                        </tr>

                        <tr class="vk-table-row-highly-confidential">
                            <td><code>DOC-2026-015</code></td>
                            <td>
                                <div class="fc-text-primary-bold" >Board_Risk_Register_2026.xlsx</div>
                                <div class="fc-text-muted-11" >Board of Directors quarterly risk disclosures</div>
                            </td>
                            <td class="fc-text-12" >Viktor Sokolov (EMP-1001)</td>
                            <td class="fc-mono-11" >2026-09-05 11:30</td>
                            <td class="fc-text-right" >
                                <button class="vk-btn vk-btn-sm vk-btn-accent btn-toggle-hold active-hold" data-doc-id="DOC-2026-015" >
                                    <span class="material-symbols-outlined text-[14px]">lock</span> Hold Active
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
    <script src="js/fc-retention.js"></script>
</body>

</html>