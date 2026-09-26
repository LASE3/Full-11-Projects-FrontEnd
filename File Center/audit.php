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
    <title>Integrity Ledger &amp; Cryptographic Audit - VOSTOKPRIBOR File Center</title>
    <link rel="stylesheet" href="css/fc-tokens.css" />
    <link rel="stylesheet" href="css/fc-common.css" />
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
                <span class="material-symbols-outlined text-[14px] fc-color-secondary">fingerprint</span>
                <span class="fc-font-family-var-font-eb0b" >INTEGRITY: <strong>HARDWARE HSM AUDIT LEDGER</strong></span>
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
                <span class="station-live-clock">17:50:00 UTC+6</span>
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
            <a class="vk-nav-item" href="retention.php">
                <div class="fc-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                    <span>Retention &amp; Holds</span>
                </div>
            </a>
            <a class="vk-nav-item active" href="audit.php">
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
                <span class="fc-font-family-var-font-1ab9" >HSM INTEGRITY ENGINE</span>
            </div>
            <div class="fc-mono-muted-11" >Hash Ledger: Zero Errors</div>
            <div class="fc-font-family-var-font-940f" >Last Audit Cycle: 100% PASS</div>
        </div>

        <!-- Log Out -->

    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="vk-main-layout">
        <!-- HEADER BLOCK -->
        <div class="fc-display-flex-justify-content-f610" >
            <div>
                <div class="fc-display-flex-align-items-9bb7" >
                    <span class="vk-tag vk-tag-internal">CRYPTOGRAPHIC ASSURANCE // FIPS 140-3</span>
                    <span class="fc-mono-muted-12" >AUDIT-CYCLE: 2026-Q3</span>
                </div>
                <h1 class="fc-font-size-26px-font-5041" >
                    <span class="material-symbols-outlined fc-font-size-28px-color-c410">fingerprint</span>
                    Cryptographic Integrity &amp; Document Audit Ledger
                </h1>
                <p class="fc-color-var-vk-neutral-5a07" >
                    Bitwise verification of stored documents against the hardware HSM hash ledger and immutable custodial transaction trail.
                </p>
            </div>
        </div>

        <!-- TWO COLUMN LAYOUT: CRYPTOGRAPHIC VERIFIER + ACCESS LEDGER -->
        <div class="fc-display-grid-grid-template-687b" >
            <!-- SHA-256 VERIFIER TOOL -->
            <div class="vk-card">
                <div class="vk-card-header">
                    <div>
                        <div class="vk-card-title">Real-Time Cryptographic Seal Verifier</div>
                        <div class="vk-card-subtitle">Query the Almaty Central HSM to detect bitwise corruption or tampering</div>
                    </div>
                    <span class="vk-tag fc-background-rgba-14-124-0f4c">
                        SHA-256 CHECK
                    </span>
                </div>

                <div class="vk-card-body">
                    <div class="fc-display-flex-flex-direction-f4b1" >
                        <div>
                            <label class="fc-font-size-12px-font-8504" >
                                Select Registered Document from Manifest
                            </label>
                            <select class="vk-btn vk-btn-outline" id="verify-select-doc" >
                                <option value="" data-expected-hash="">-- Choose a document to auto-populate checksum --</option>
                                <option value="DOC-2026-004" data-expected-hash="9f8e7d6c5b4a3928170192837465abcdeffedcba98765432101234567890fedc">
                                    DOC-2026-004: PRJ-2026-002_Integration_Specification.pdf (BaltNord)
                                </option>
                                <option value="DOC-2026-001" data-expected-hash="a89f30b9148d423985bf4f481c81c4e97a5b3992b1cf5600ea8b1990c681ea88">
                                    DOC-2026-001: Corporate_Information_Security_Policy.pdf
                                </option>
                                <option value="DOC-2026-007" data-expected-hash="deadbeef1029384756abcdef0192837465bcaefd1234567890fedcba98765432">
                                    DOC-2026-007: Employee_Access_Matrix.xlsx
                                </option>
                                <option value="DOC-2026-015" data-expected-hash="bbccddeeff00112233445566778899aabbccddeeff00112233445566778899aa">
                                    DOC-2026-015: Board_Risk_Register_2026.xlsx
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="fc-font-size-12px-font-8504" >
                                Target SHA-256 Digest (64 Hex Characters)
                            </label>
                            <input class="vk-btn vk-btn-outline" id="verify-input-hash" type="text" placeholder="Paste 64-character SHA-256 hex string..."  />
                        </div>

                        <div class="fc-display-flex-justify-content-9d73" >
                            <button class="vk-btn vk-btn-primary" id="btn-run-hash-verification" type="button">
                                <span class="material-symbols-outlined text-[16px]">security</span> Verify Cryptographic Seal
                            </button>
                        </div>

                        <!-- VERIFICATION RESULT (Initially hidden) -->
                        <div class="fc-display-none-padding-14px-1e95" id="verify-result-box" >
                            <div class="fc-display-flex-align-items-3b8a" >
                                <span class="material-symbols-outlined text-[18px]">verified</span>
                                BITWISE INTEGRITY CONFIRMED // ZERO TAMPERING DETECTED
                            </div>
                            <div class="fc-font-family-var-font-94fe" >
                                Verified Digest: <span id="verify-disp-hash"></span>
                            </div>
                            <div class="fc-font-family-var-font-7b91" >
                                Timestamp: <span id="verify-disp-timestamp"></span> &bull; Validated by: HSM-NODE-ALMATY-01
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECURITY ASSURANCE METRICS -->
            <div class="vk-card">
                <div class="vk-card-header">
                    <div>
                        <div class="vk-card-title">Enclave Compliance Telemetry</div>
                        <div class="vk-card-subtitle">Automated cryptographic monitoring parameters</div>
                    </div>
                </div>

                <div class="vk-card-body fc-display-flex-flex-direction-269e">
                    <div>
                        <div class="fc-display-flex-justify-content-c0fa" >
                            <span class="fc-text-primary-bold" >FIPS 140-3 Hardware Key Security</span>
                            <span class="fc-font-family-var-font-90a6" >ACTIVE (LEVEL 3)</span>
                        </div>
                        <div class="fc-text-muted-11" >Master signing key resides in hardware enclave in Almaty Datacenter.</div>
                    </div>

                    <div class="fc-border-top-1px-solid-8a57" >
                        <div class="fc-display-flex-justify-content-c0fa" >
                            <span class="fc-text-primary-bold" >Automated Daily Hash Scrub</span>
                            <span class="fc-font-family-var-font-1a77" >PASSED (02:00 UTC+6)</span>
                        </div>
                        <div class="fc-text-muted-11" >1,842 files re-verified against root merkle tree. 0 mismatches.</div>
                    </div>

                    <div class="fc-border-top-1px-solid-8a57" >
                        <div class="fc-display-flex-justify-content-c0fa" >
                            <span class="fc-text-primary-bold" >Disaster Recovery Replication</span>
                            <span class="fc-font-family-var-font-90a6" >SYNCHRONIZED</span>
                        </div>
                        <div class="fc-text-muted-11" >Encrypted mirror synchronized to Astana secondary bunker.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CUSTODIAL AUDIT LOG TABLE -->
        <div class="vk-card fc-mb-30">
            <div class="vk-card-header">
                <div>
                    <div class="vk-card-title">Immutable Document Transaction Trail</div>
                    <div class="vk-card-subtitle">Complete chronological record of document access, signoffs, and exports</div>
                </div>
                <div class="fc-width-240px-1e8d" >
                    <input class="vk-btn vk-btn-outline" id="audit-search-input" type="text" placeholder="Filter log by user, DOC-ID..."  />
                </div>
            </div>

            <div class="vk-card-body fc-p-0">
                <table class="vk-table">
                    <thead>
                        <tr>
                            <th class="fc-width-150px-c251" >Timestamp (UTC+6)</th>
                            <th class="fc-width-180px-21b9" >Actor / User</th>
                            <th class="fc-width-130px-e314" >Action</th>
                            <th class="fc-w-140" >Target DOC-ID</th>
                            <th>Transaction Scope / Notes</th>
                            <th class="fc-width-100px-text-align-41b3" >Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="audit-log-row vk-table-row-highly-confidential">
                            <td class="fc-mono-11" >2026-09-11 17:15:22</td>
                            <td>
                                <div class="fc-font-semibold" >Farida Iskakova</div>
                                <div class="fc-font-family-var-font-36a4" >EMP-1019 &bull; ENG</div>
                            </td>
                            <td><span class="vk-tag fc-background-e0e7ff-color-3730a3-4a03">SIGN_REVIEW</span></td>
                            <td><code>DOC-2026-004</code></td>
                            <td class="fc-text-12" >Opened redaction inspection for BaltNord PRJ-2026-002 specification</td>
                            <td class="fc-text-right" ><span class="vk-status-badge status-approved">VERIFIED</span></td>
                        </tr>

                        <tr class="audit-log-row vk-table-row-internal">
                            <td class="fc-mono-11" >2026-09-11 16:42:01</td>
                            <td>
                                <div class="fc-font-semibold" >Dana Yermak</div>
                                <div class="fc-font-family-var-font-36a4" >EMP-1017 &bull; ENG</div>
                            </td>
                            <td><span class="vk-tag fc-background-f1f5f9-color-475569-9fb0">VIEW</span></td>
                            <td><code>DOC-2026-010</code></td>
                            <td class="fc-text-12" >Accessed API Integration Guide for developer gateway synchronization</td>
                            <td class="fc-text-right" ><span class="vk-status-badge status-approved">SUCCESS</span></td>
                        </tr>

                        <tr class="audit-log-row vk-table-row-confidential">
                            <td class="fc-mono-11" >2026-09-11 14:10:44</td>
                            <td>
                                <div class="fc-font-semibold" >Markus Klein</div>
                                <div class="fc-font-family-var-font-36a4" >EMP-1010 &bull; SAL</div>
                            </td>
                            <td><span class="vk-tag fc-background-fef3c7-color-92400e-d4d7">EXPORT</span></td>
                            <td><code>DOC-2026-003</code></td>
                            <td class="fc-text-12" >Exported customer copy of Aral Geomatics Statement of Work</td>
                            <td class="fc-text-right" ><span class="vk-status-badge status-approved">SUCCESS</span></td>
                        </tr>

                        <tr class="audit-log-row vk-table-row-highly-confidential">
                            <td class="fc-mono-11" >2026-09-11 11:20:18</td>
                            <td>
                                <div class="fc-font-semibold" >Timur Akhmetov</div>
                                <div class="fc-font-family-var-font-36a4" >EMP-1005 &bull; EXE</div>
                            </td>
                            <td><span class="vk-tag fc-background-fee2e2-color-991b1b-7752">LEGAL_HOLD</span></td>
                            <td><code>DOC-2026-007</code></td>
                            <td class="fc-text-12" >Applied statutory audit preservation lock on Employee Access Matrix</td>
                            <td class="fc-text-right" ><span class="vk-status-badge status-approved">ENFORCED</span></td>
                        </tr>

                        <tr class="audit-log-row vk-table-row-highly-confidential">
                            <td class="fc-mono-11" >2026-09-10 14:12:05</td>
                            <td>
                                <div class="fc-font-semibold" >Dana Yermak</div>
                                <div class="fc-font-family-var-font-36a4" >EMP-1017 &bull; ENG</div>
                            </td>
                            <td><span class="vk-tag fc-background-dcfce7-color-166534-4a19">INGEST_DRAFT</span></td>
                            <td><code>DOC-2026-004</code></td>
                            <td class="fc-text-12" >Uploaded initial revision of PRJ-2026-002_Integration_Specification.pdf</td>
                            <td class="fc-text-right" ><span class="vk-status-badge status-approved">ENQUEUED</span></td>
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
    <script src="js/fc-audit.js"></script>
</body>

</html>