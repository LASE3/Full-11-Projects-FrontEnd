<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('DOC');

$pdo = getDbConnection();
$currUser = $_SESSION['vostok_user'] ?? [
    'full_name' => 'Farida Iskakova',
    'user_id' => 'EMP-1019',
    'role_name' => 'Lead Custodian',
];

// Query real policies from database
$policies = $pdo->query("SELECT * FROM document_retention_policies ORDER BY policy_id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Query active legal holds from database
$holds = $pdo->query("
    SELECT 
        d.doc_id,
        d.file_name,
        d.description,
        d.classification,
        d.is_legal_hold,
        d.legal_hold_date,
        d.legal_hold_reason,
        COALESCE(e.full_name, d.legal_hold_by_emp_id, 'Farida Iskakova') AS hold_authority,
        d.legal_hold_by_emp_id
    FROM documents d
    LEFT JOIN employees e ON d.legal_hold_by_emp_id = e.emp_id
    WHERE d.is_legal_hold = 1
    ORDER BY d.legal_hold_date DESC, d.doc_id ASC
")->fetchAll(PDO::FETCH_ASSOC);

$totalDocs = (int)$pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn();
$pendingApprovals = (int)$pdo->query("SELECT COUNT(*) FROM documents WHERE status IN ('In Review', 'Pending')")->fetchColumn();
$holdsCount = count($holds);

// Calculate dynamic storage gauges
$hotGb = round(400.0 + ($totalDocs * 0.8), 1);
$warmGb = round(420.0 + ($totalDocs * 0.7), 1);
$coldTb = round(1.2 + ($totalDocs * 0.005), 2);
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
    <!-- TOP NAVIGATION BAR -->
    <header class="vk-top-navbar">
        <div class="fc-flex-center-gap-24">
            <a class="vk-brand-section" href="Dashboard.php">
                <img alt="VOSTOKPRIBOR Official Mark" class="brand-logo-img fc-logo-img" src="assets/logo.svg" />
                <div class="fc-flex-col">
                    <div class="fc-flex-center-gap-8">
                        <span class="fc-font-family-var-font-980b">VOSTOKPRIBOR</span>
                        <span class="vk-system-badge">SYS-09 // FILE-CENTER</span>
                    </div>
                    <span class="fc-font-family-var-font-54ae">ALMATY CENTRAL • EST. 1968 • DOCUMENT VAULT v3.8.2</span>
                </div>
            </a>
            <div class="fc-display-flex-align-items-bc9f">
                <span class="material-symbols-outlined text-[14px] fc-color-accent">inventory_2</span>
                <span class="fc-font-family-var-font-eb0b">ARCHIVE: <strong>STATUTORY RETENTION &amp; LEGAL HOLDS</strong></span>
            </div>
        </div>

        <div class="fc-flex-center-gap-16">
            <button class="search-trigger-btn" type="button">
                <span class="material-symbols-outlined text-[16px]">search</span>
                <span>Search documents, DOC-IDs...</span>
                <span class="kbd-shortcut">Ctrl K</span>
            </button>
            <div class="fc-display-flex-align-items-9eca">
                <span class="material-symbols-outlined text-[14px] fc-color-secondary">schedule</span>
                <span class="station-live-clock">17:48:00 UTC+6</span>
            </div>
            <div class="fc-display-flex-align-items-20f3">
                <div class="fc-text-right">
                    <div class="fc-font-size-12px-font-2ab2"><?= htmlspecialchars($currUser['full_name'] ?? 'Farida Iskakova') ?></div>
                    <div class="fc-font-family-var-font-5c5e"><?= htmlspecialchars($currUser['user_id'] ?? 'EMP-1019') ?> • <?= htmlspecialchars($currUser['role_name'] ?? 'Lead Custodian') ?></div>
                </div>
                <div class="fc-width-32px-height-32px-0eaf">
                    <span class="material-symbols-outlined text-[18px] fc-text-white">folder_managed</span>
                </div>
            </div>

            <!-- Top Bar Sign Out -->
            <a href="../api/logout.php?system=File%20Center&redirect=../File%20Center/login.php" class="top-signout-btn" title="Sign Out of File Center" onclick="(function(){sessionStorage.clear();localStorage.clear();})()"><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
        </div>
    </header>

    <!-- LEFT SIDEBAR -->
    <aside class="vk-sidebar">
        <div class="vk-sidebar-nav">
            <div class="vk-sidebar-header">Document Vault</div>
            <a class="vk-nav-item" href="Dashboard.php">
                <div class="fc-flex-center-gap-10">
                    <span class="material-symbols-outlined text-[18px]">folder_open</span>
                    <span>Document Repository</span>
                </div>
                <span class="nav-badge"><?= $totalDocs ?></span>
            </a>
            <a class="vk-nav-item" href="approvals.php">
                <div class="fc-flex-center-gap-10">
                    <span class="material-symbols-outlined text-[18px]">assignment_turned_in</span>
                    <span>Signoff &amp; Approvals</span>
                </div>
                <?php if ($pendingApprovals > 0): ?>
                    <span class="vk-tag vk-tag-highly-confidential fc-font-size-9px-padding-4279"><?= $pendingApprovals ?> ACTION</span>
                <?php endif; ?>
            </a>
            <a class="vk-nav-item" href="upload.php">
                <div class="fc-flex-center-gap-10">
                    <span class="material-symbols-outlined text-[18px]">upload_file</span>
                    <span>Secure Ingestion</span>
                </div>
            </a>
            <a class="vk-nav-item active" href="retention.php">
                <div class="fc-flex-center-gap-10">
                    <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                    <span>Retention &amp; Holds</span>
                </div>
            </a>
            <a class="vk-nav-item" href="audit.php">
                <div class="fc-flex-center-gap-10">
                    <span class="material-symbols-outlined text-[18px]">fingerprint</span>
                    <span>Integrity Ledger</span>
                </div>
            </a>

            <div class="vk-sidebar-header fc-mt-20">System Integrations</div>
            <a class="vk-nav-item" href="../VOSTOKPRIBOR Corporate Web Platform/index.php">
                <div class="fc-flex-center-gap-10">
                    <span class="material-symbols-outlined text-[18px] fc-color-1b3a5c-1796">language</span>
                    <span>Corporate Platform</span>
                </div>
                <span class="vk-tag fc-font-size-9px-background-21b8">SYS-01</span>
            </a>
            <a class="vk-nav-item" href="../Employee Intranet/index.php">
                <div class="fc-flex-center-gap-10">
                    <span class="material-symbols-outlined text-[18px] fc-color-5c7290-b50c">badge</span>
                    <span>Employee Intranet</span>
                </div>
                <span class="vk-tag fc-font-size-9px-background-cfa1">SYS-04</span>
            </a>
            <a class="vk-nav-item" href="../Developer/index.php">
                <div class="fc-flex-center-gap-10">
                    <span class="material-symbols-outlined text-[18px] fc-color-1e8fa6-f90d">terminal</span>
                    <span>Developer / API Portal</span>
                </div>
                <span class="vk-tag fc-font-size-9px-background-5382">SYS-10</span>
            </a>
            <a class="vk-nav-item" href="../Admin & Governance Portal/index.php">
                <div class="fc-flex-center-gap-10">
                    <span class="material-symbols-outlined text-[18px] fc-color-var-vk-alert-6578">shield</span>
                    <span>Admin &amp; Governance</span>
                </div>
                <span class="vk-tag fc-font-size-9px-background-0f11">SYS-11</span>
            </a>
        </div>

        <div class="fc-padding-16px-border-top-d16d">
            <div class="fc-display-flex-align-items-81c3">
                <span class="status-dot-pulse"></span>
                <span class="fc-font-family-var-font-1ab9">RETENTION ENCLAVE</span>
            </div>
            <div class="fc-mono-muted-11">WORM Immutable Storage: Active</div>
            <div class="fc-font-family-var-font-940f">Kazakhstan Law §14 Compliant</div>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="vk-main-layout">
        <!-- HEADER BLOCK -->
        <div class="fc-display-flex-justify-content-f610" style="margin-bottom: 24px;">
            <div>
                <div class="fc-display-flex-align-items-9bb7">
                    <span class="vk-tag vk-tag-internal">CLASSIFICATION: INTERNAL // #3E7CB1</span>
                    <span class="fc-mono-muted-12">POLICY: RETENTION-SCHEDULE-v2</span>
                </div>
                <h1 class="fc-font-size-26px-font-5041">
                    <span class="material-symbols-outlined fc-font-size-28px-color-a4d3">inventory_2</span>
                    Archival Governance, Retention Lifecycle &amp; Legal Holds
                </h1>
                <p class="fc-color-var-vk-neutral-5a07">
                    Statutory preservation timelines, litigation legal holds, and immutable cold archival quotas compliant with Kazakhstani Industrial Standards.
                </p>
            </div>
            <div>
                <button class="vk-btn vk-btn-outline" id="btn-export-archival-manifest" type="button">
                    <span class="material-symbols-outlined text-[16px]">download</span> Export Archival Manifest
                </button>
            </div>
        </div>

        <!-- 3 STORAGE VOLUME ALLOCATION GAUGES (Dynamic from Database) -->
        <div class="retention-meter-grid">
            <div class="retention-meter-card">
                <div class="fc-flex-between-center">
                    <span class="fc-font-family-var-font-a98c">Hot NVMe Vault</span>
                    <span class="material-symbols-outlined text-[16px] fc-color-accent">flash_on</span>
                </div>
                <div class="fc-font-family-var-font-8611">
                    <?= number_format($hotGb, 1) ?> <span class="fc-font-size-13px-font-30a9">GB / 1.0 TB</span>
                </div>
                <div class="retention-progress-bar">
                    <div style="width: <?= round(($hotGb / 1024) * 100, 1) ?>%; background: var(--vk-secondary); height: 100%; border-radius: 2px;"></div>
                </div>
                <div class="fc-text-muted-11">Active projects &bull; Real-time access (&lt;5ms)</div>
            </div>

            <div class="retention-meter-card">
                <div class="fc-flex-between-center">
                    <span class="fc-font-family-var-font-a98c">Warm Nearline Store</span>
                    <span class="material-symbols-outlined text-[16px] fc-color-secondary">storage</span>
                </div>
                <div class="fc-font-family-var-font-8611">
                    <?= number_format($warmGb, 1) ?> <span class="fc-font-size-13px-font-30a9">GB / 2.0 TB</span>
                </div>
                <div class="retention-progress-bar">
                    <div style="width: <?= round(($warmGb / 2048) * 100, 1) ?>%; background: var(--vk-primary); height: 100%; border-radius: 2px;"></div>
                </div>
                <div class="fc-text-muted-11">Financial archives &bull; 7-year audit buffer</div>
            </div>

            <div class="retention-meter-card">
                <div class="fc-flex-between-center">
                    <span class="fc-font-family-var-font-a98c">Cold WORM Archive</span>
                    <span class="material-symbols-outlined text-[16px] fc-color-var-vk-primary-40d3">ac_unit</span>
                </div>
                <div class="fc-font-family-var-font-8611">
                    <?= number_format($coldTb, 2) ?> <span class="fc-font-size-13px-font-30a9">TB / 5.0 TB</span>
                </div>
                <div class="retention-progress-bar">
                    <div style="width: <?= round(($coldTb / 5.0) * 100, 1) ?>%; background: #00E5FF; height: 100%; border-radius: 2px;"></div>
                </div>
                <div class="fc-text-muted-11">Tape &amp; Optical WORM &bull; Permanent holds</div>
            </div>
        </div>

        <!-- STATUTORY RETENTION POLICIES TABLE (Dynamic from Database) -->
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
                            <th class="fc-width-220px-d415">Category</th>
                            <th class="fc-w-140">Retention Scope</th>
                            <th class="fc-width-180px-21b9">Legal / Standard Anchor</th>
                            <th>Disposition Action</th>
                            <th class="fc-width-120px-b15a">Compliance Tier</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($policies as $pol): 
                            $tierSlug = strtolower(str_replace([' ', '.'], ['-', ''], $pol['compliance_tier']));
                            $tierClass = match ($tierSlug) {
                                'highly-conf', 'topsecret' => 'vk-tag-highly-confidential',
                                'confidential' => 'vk-tag-confidential',
                                default => 'vk-tag-internal',
                            };
                        ?>
                        <tr>
                            <td>
                                <div class="fc-text-primary-bold"><?= htmlspecialchars($pol['category']) ?></div>
                                <div class="fc-text-muted-11"><?= htmlspecialchars($pol['target_docs'] ?? '') ?></div>
                            </td>
                            <td class="fc-font-family-var-font-d8ae"><?= htmlspecialchars($pol['retention_scope']) ?></td>
                            <td class="fc-text-muted-12"><?= htmlspecialchars($pol['legal_anchor']) ?></td>
                            <td class="fc-text-12"><?= htmlspecialchars($pol['disposition_action']) ?></td>
                            <td><span class="vk-tag <?= $tierClass ?>"><?= htmlspecialchars($pol['compliance_tier']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ACTIVE LEGAL HOLDS & PRESERVATION ORDERS (Dynamic from Database) -->
        <div class="vk-card fc-mb-30">
            <div class="vk-card-header">
                <div>
                    <div class="vk-card-title">Active Legal Preservation Holds (Litigation &amp; Audit Freezes)</div>
                    <div class="vk-card-subtitle">Documents locked against automated purging or lifecycle transitions</div>
                </div>
                <span class="legal-hold-badge">
                    <span class="material-symbols-outlined text-[14px]">gavel</span> <span id="holds-count-badge"><?= $holdsCount ?></span> ACTIVE HOLDS
                </span>
            </div>

            <div class="vk-card-body fc-p-0">
                <table class="vk-table">
                    <thead>
                        <tr>
                            <th class="fc-w-140">DOC-ID</th>
                            <th>Target Document</th>
                            <th class="fc-width-180px-21b9">Preservation Authority</th>
                            <th class="fc-width-160px-7534">Enforced Since</th>
                            <th class="fc-width-160px-text-align-a6ce">Action</th>
                        </tr>
                    </thead>
                    <tbody id="holds-table-body">
                        <?php if (empty($holds)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 24px; color: var(--vk-neutral-500);">
                                    No active legal preservation holds currently enforced in the vault.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($holds as $h): ?>
                            <tr class="vk-table-row-highly-confidential" id="hold-row-<?= htmlspecialchars($h['doc_id']) ?>">
                                <td><code><?= htmlspecialchars($h['doc_id']) ?></code></td>
                                <td>
                                    <div class="fc-text-primary-bold"><?= htmlspecialchars($h['file_name']) ?></div>
                                    <div class="fc-text-muted-11"><?= htmlspecialchars($h['legal_hold_reason'] ?? $h['description'] ?? 'Statutory preservation freeze') ?></div>
                                </td>
                                <td class="fc-text-12"><?= htmlspecialchars($h['hold_authority']) ?> (<?= htmlspecialchars($h['legal_hold_by_emp_id'] ?? 'EMP-1019') ?>)</td>
                                <td class="fc-mono-11"><?= htmlspecialchars(substr($h['legal_hold_date'] ?? '2026-09-01 09:00:00', 0, 16)) ?></td>
                                <td class="fc-text-right">
                                    <button class="vk-btn vk-btn-sm vk-btn-accent btn-toggle-hold active-hold" data-doc-id="<?= htmlspecialchars($h['doc_id']) ?>">
                                        <span class="material-symbols-outlined text-[14px]">lock</span> Hold Active
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- DOCKED ENTERPRISE STATUS BAR -->
    <footer class="vk-status-bar">
        <div class="fc-flex-center-gap-16">
            <div class="fc-flex-center-gap-8">
                <span class="status-dot-pulse"></span>
                <span>ALMATY-VAULT-01 // HSM CLUSTER SYNCHRONIZED</span>
            </div>
            <span>VOLUME: 842.6 GB / 4.8 TB (17.5%)</span>
        </div>
        <div class="fc-display-flex-align-items-bf7c">
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
                    <span>Document Repository (All <?= $totalDocs ?> Statutory Records)</span>
                </a>
                <a class="cmd-palette-item" href="approvals.php">
                    <span class="material-symbols-outlined text-[16px]">assignment_turned_in</span>
                    <span>Signoff &amp; Approvals</span>
                </a>
                <a class="cmd-palette-item" href="upload.php">
                    <span class="material-symbols-outlined text-[16px]">upload_file</span>
                    <span>Secure Ingestion Enclave</span>
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