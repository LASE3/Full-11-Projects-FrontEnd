<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('DOC');

$pdo = getDbConnection();
$currUser = $_SESSION['vostok_user'] ?? [
    'full_name' => 'Farida Iskakova',
    'user_id' => 'EMP-1019',
    'role_name' => 'Lead Custodian',
    'clearance_level' => 'L3',
];

// Query real statistics from database
$totalDocs = (int)$pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn();

// Volume calculation from actual documents
$docsSizes = $pdo->query("SELECT file_size FROM documents")->fetchAll(PDO::FETCH_COLUMN);
$totalMB = 0;
foreach ($docsSizes as $sz) {
    if (preg_match('/([\d\.]+)\s*(MB|KB|GB)/i', (string)$sz, $m)) {
        $num = (float)$m[1];
        $unit = strtoupper($m[2]);
        if ($unit === 'GB') $totalMB += $num * 1024;
        elseif ($unit === 'MB') $totalMB += $num;
        elseif ($unit === 'KB') $totalMB += $num / 1024;
    } else {
        $totalMB += 2.0;
    }
}
$vaultGb = round(840.0 + ($totalMB / 1024.0), 1);
$vaultCapGb = 4800; // 4.8 TB cap
$vaultPct = round(($vaultGb / $vaultCapGb) * 100, 1);

// Pending approvals
$pendingApprovals = (int)$pdo->query("SELECT COUNT(*) FROM documents WHERE status IN ('In Review', 'Pending')")->fetchColumn();

// Verified SHA-256 hashes
$validHashes = (int)$pdo->query("SELECT COUNT(*) FROM documents WHERE file_hash IS NOT NULL AND CHAR_LENGTH(file_hash) = 64")->fetchColumn();
$integrityPct = $totalDocs > 0 ? round(($validHashes / $totalDocs) * 100, 1) : 100.0;

// Classification counts
$countHighlyConf = (int)$pdo->query("SELECT COUNT(*) FROM documents WHERE classification = 'TopSecret'")->fetchColumn();
$countConf = (int)$pdo->query("SELECT COUNT(*) FROM documents WHERE classification = 'Confidential'")->fetchColumn();
$countInternal = (int)$pdo->query("SELECT COUNT(*) FROM documents WHERE classification = 'Internal'")->fetchColumn();
$countPublic = (int)$pdo->query("SELECT COUNT(*) FROM documents WHERE classification = 'Public'")->fetchColumn();

// Partition counts
$folders = ['governance', 'projects', 'contracts', 'finance', 'hr', 'operations'];
$folderCounts = [];
foreach ($folders as $f) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE folder = :f");
    $stmt->execute([':f' => $f]);
    $folderCounts[$f] = (int)$stmt->fetchColumn();
}

$userClearance = $currUser['clearance_level'] ?? 'L2';
$userCusId = $currUser['cus_id'] ?? null;
$isSuperAdmin = ($userClearance === 'L4' || $userClearance === 'L5' || ($currUser['role_name'] ?? '') === 'Super Administrator' || ($currUser['username'] ?? '') === 'admin');

$allowedClassifications = ['Public'];
if ($userClearance === 'L2') {
    $allowedClassifications = ['Public', 'Internal'];
} elseif ($userClearance === 'L3') {
    $allowedClassifications = ['Public', 'Internal', 'Confidential'];
} elseif ($isSuperAdmin || in_array($userClearance, ['L4', 'L5'])) {
    $allowedClassifications = ['Public', 'Internal', 'Confidential', 'TopSecret', 'Restricted'];
}
$inClause = "'" . implode("','", $allowedClassifications) . "'";

if ($userCusId && !$isSuperAdmin) {
    $sqlDocs = "SELECT d.*, e.full_name AS custodian_name, e.job_title AS custodian_job, c.company_name AS customer_name, p.project_name AS project_name FROM documents d LEFT JOIN employees e ON d.owner_emp_id = e.emp_id LEFT JOIN customers c ON d.related_cus_id = c.cus_id LEFT JOIN projects p ON d.related_prj_id = p.prj_id WHERE (d.related_cus_id = " . $pdo->quote($userCusId) . " OR d.classification = 'Public') ORDER BY CAST(SUBSTRING(d.doc_id, 10) AS UNSIGNED) ASC, d.doc_id ASC";
} elseif (!$isSuperAdmin) {
    $sqlDocs = "SELECT d.*, e.full_name AS custodian_name, e.job_title AS custodian_job, c.company_name AS customer_name, p.project_name AS project_name FROM documents d LEFT JOIN employees e ON d.owner_emp_id = e.emp_id LEFT JOIN customers c ON d.related_cus_id = c.cus_id LEFT JOIN projects p ON d.related_prj_id = p.prj_id WHERE d.classification IN ($inClause) ORDER BY CAST(SUBSTRING(d.doc_id, 10) AS UNSIGNED) ASC, d.doc_id ASC";
} else {
    $sqlDocs = "SELECT d.*, e.full_name AS custodian_name, e.job_title AS custodian_job, c.company_name AS customer_name, p.project_name AS project_name FROM documents d LEFT JOIN employees e ON d.owner_emp_id = e.emp_id LEFT JOIN customers c ON d.related_cus_id = c.cus_id LEFT JOIN projects p ON d.related_prj_id = p.prj_id ORDER BY CAST(SUBSTRING(d.doc_id, 10) AS UNSIGNED) ASC, d.doc_id ASC";
}
$docsStmt = $pdo->query($sqlDocs);
$documents = $docsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="<?= htmlspecialchars(getCsrfToken()) ?>" />
    <title>Enterprise Document Repository - VOSTOKPRIBOR File Center</title>
    <link rel="stylesheet" href="css/fc-tokens.css" />
    <link rel="stylesheet" href="css/fc-common.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="css/fc-repo.css" />
</head>

<body>
    <!-- TOP NAVIGATION BAR -->
    <header class="vk-top-navbar">
        <div class="fc-flex-center-gap-24">
            <button class="mobile-nav-toggle" id="fc-sidebar-toggle" onclick="document.body.classList.toggle('sidebar-open')" title="Toggle Menu"><span class="material-symbols-outlined">menu</span></button>
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
                <span class="material-symbols-outlined text-[14px] fc-color-accent">folder_special</span>
                <span class="fc-font-family-var-font-eb0b">VAULT: <strong>CENTRAL DOCUMENT REPOSITORY</strong></span>
            </div>
        </div>

        <div class="fc-flex-center-gap-16">
<button class="icon-button notifications-btn" id="notifications-toggle-btn" title="Live Enterprise Notifications" style="background:transparent;border:none;color:#bdc6cf;cursor:pointer;padding:6px;display:flex;align-items:center;justify-content:center;position:relative;">
                <span class="material-symbols-outlined text-[20px]">notifications</span>
            </button>
            <button class="search-trigger-btn" type="button">
                <span class="material-symbols-outlined text-[16px]">search</span>
                <span>Search documents, DOC-IDs...</span>
                <span class="kbd-shortcut">Ctrl K</span>
            </button>
            <div class="fc-display-flex-align-items-9eca">
                <span class="material-symbols-outlined text-[14px] fc-color-secondary">schedule</span>
                <span class="station-live-clock">17:42:00 UTC+6</span>
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
    <aside class="vk-sidebar" style="background-color: #0f2438 !important; border-right: 1px solid rgba(255, 255, 255, 0.1) !important;">
        <div class="vk-sidebar-nav">
            <div class="vk-sidebar-header">Document Vault</div>
            <a class="vk-nav-item active" href="Dashboard.php">
                <div class="fc-flex-center-gap-10">
                    <span class="material-symbols-outlined text-[18px]">folder_open</span>
                    <span>Document Repository</span>
                </div>
                <span class="nav-badge" id="sidebar-total-docs"><?= $totalDocs ?></span>
            </a>
            <a class="vk-nav-item" href="approvals.php">
                <div class="fc-flex-center-gap-10">
                    <span class="material-symbols-outlined text-[18px]">assignment_turned_in</span>
                    <span>Signoff &amp; Approvals</span>
                </div>
                <?php if ($pendingApprovals > 0): ?>
                    <span class="vk-tag vk-tag-highly-confidential fc-font-size-9px-padding-4279" id="sidebar-pending-badge"><?= $pendingApprovals ?> ACTION</span>
                <?php endif; ?>
            </a>
            <a class="vk-nav-item" href="upload.php">
                <div class="fc-flex-center-gap-10">
                    <span class="material-symbols-outlined text-[18px]">upload_file</span>
                    <span>Secure Ingestion</span>
                </div>
            </a>
            <a class="vk-nav-item" href="retention.php">
                <div class="fc-flex-center-gap-10">
                    <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                    <span>Retention &amp; Holds</span>
                </div>
            </a>
            <a href="Integrations.php" class="sidebar-nav-item">
                <div class="sidebar-item-left"><span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00E5FF" stroke-width="2">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                        </svg></span><span class="fc-color-00e5ff-font-weight-fe7a">System Integrations</span></div><span class="sidebar-badge fc-background-rgba-0-229-2595">SYS06</span>
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
            <a class="vk-nav-item" href="../Employee Intranet/login.php">
                <div class="fc-flex-center-gap-10">
                    <span class="material-symbols-outlined text-[18px] fc-color-5c7290-b50c">badge</span>
                    <span>Employee Intranet</span>
                </div>
                <span class="vk-tag fc-font-size-9px-background-cfa1">SYS-04</span>
            </a>
            <a class="vk-nav-item" href="../Developer/login.php">
                <div class="fc-flex-center-gap-10">
                    <span class="material-symbols-outlined text-[18px] fc-color-1e8fa6-f90d">terminal</span>
                    <span>Developer / API Portal</span>
                </div>
                <span class="vk-tag fc-font-size-9px-background-5382">SYS-10</span>
            </a>
            <a class="vk-nav-item" href="../Admin & Governance Portal/login.php">
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
                <span class="fc-font-family-var-font-1ab9">ENCLAVE HSM ONLINE</span>
            </div>
            <div class="fc-mono-muted-11">Node: files.vostokpribor.local</div>
            <div class="fc-font-family-var-font-940f">FIPS 140-3 Hardware Sealed</div>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="vk-main-layout">
        <!-- HEADER BLOCK -->
        <div class="repo-header-toolbar">
            <div>
                <div class="fc-display-flex-align-items-9bb7">
                    <span class="vk-tag fc-background-var-vk-sys-9005">
                        SYSTEM 09 // GRAPHITE #5A6470
                    </span>
                    <span class="fc-mono-muted-12">FQDN: files.vostokpribor.local</span>
                </div>
                <h1 class="fc-font-size-26px-font-5041">
                    <span class="material-symbols-outlined fc-font-size-28px-color-a4d3">source_environment</span>
                    Central Enterprise Document Repository
                </h1>
                <p class="fc-color-var-vk-neutral-5a07">
                    Authoritative archival store, technical specifications, bilateral customer contracts, and regulatory audit records for VOSTOKPRIBOR.
                </p>
            </div>
            <div class="fc-display-flex-gap-10px-c623">
                <a class="vk-btn vk-btn-outline" href="approvals.php">
                    <span class="material-symbols-outlined text-[16px]">rule</span> Review Queue (<?= $pendingApprovals ?>)
                </a>
                <a class="vk-btn vk-btn-primary" href="upload.php">
                    <span class="material-symbols-outlined text-[16px]">upload</span> Ingest Document
                </a>
            </div>
        </div>

        <!-- 4 KPI REPOSITORY METRIC CARDS (Dynamic from Database) -->
        <div class="repo-stats-grid">
            <div class="repo-stat-card">
                <div class="repo-stat-label">
                    <span>Registered Documents</span>
                    <span class="material-symbols-outlined text-[18px] fc-color-accent">description</span>
                </div>
                <div class="repo-stat-value" id="kpi-total-docs"><?= number_format($totalDocs) ?></div>
                <div class="repo-stat-subtext fc-color-var-vk-secondary-bd5b">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> Database Root Manifest
                </div>
            </div>

            <div class="repo-stat-card">
                <div class="repo-stat-label">
                    <span>Encrypted Vault Volume</span>
                    <span class="material-symbols-outlined text-[18px] fc-color-secondary">lock</span>
                </div>
                <div class="repo-stat-value" id="kpi-vault-vol"><?= number_format($vaultGb, 1) ?> <span class="fc-font-size-14px-font-8cb8">GB</span></div>
                <div class="repo-stat-subtext">AES-256-GCM hardware envelope</div>
            </div>

            <div class="repo-stat-card">
                <div class="repo-stat-label">
                    <span>Pending Approvals</span>
                    <span class="material-symbols-outlined text-[18px] fc-color-var-vk-accent-33d2">pending_actions</span>
                </div>
                <div class="repo-stat-value fc-color-var-vk-accent-33d2" id="kpi-pending-actions"><?= $pendingApprovals ?> Action<?= $pendingApprovals === 1 ? '' : 's' ?></div>
                <div class="repo-stat-subtext"><?= $pendingApprovals > 0 ? 'Awaiting Custodial Signoff' : 'All Clear' ?></div>
            </div>

            <div class="repo-stat-card">
                <div class="repo-stat-label">
                    <span>Integrity Verification</span>
                    <span class="material-symbols-outlined text-[18px] fc-color-secondary">verified</span>
                </div>
                <div class="repo-stat-value fc-color-2e6e4e-f283" id="kpi-integrity-pct"><?= number_format($integrityPct, 1) ?>%</div>
                <div class="repo-stat-subtext">All SHA-256 anchors verified</div>
            </div>
        </div>

        <!-- FILTER & SEARCH BAR -->
        <div class="repo-filter-bar">
            <div class="filter-pills-group">
                <button class="filter-pill active" data-class-filter="all">
                    <span>All Classifications</span>
                    <span class="fc-mono-11" id="filter-count-all">(<?= $totalDocs ?>)</span>
                </button>
                <button class="filter-pill pill-highly-confidential" data-class-filter="highly-confidential">
                    <span class="material-symbols-outlined text-[14px] fc-color-var-vk-class-66a0">lock</span>
                    <span>Highly Confidential</span>
                    <span class="fc-mono-11" id="filter-count-hc">(<?= $countHighlyConf ?>)</span>
                </button>
                <button class="filter-pill pill-confidential" data-class-filter="confidential">
                    <span class="material-symbols-outlined text-[14px] fc-color-var-vk-class-7bf4">shield</span>
                    <span>Confidential</span>
                    <span class="fc-mono-11" id="filter-count-conf">(<?= $countConf ?>)</span>
                </button>
                <button class="filter-pill pill-internal" data-class-filter="internal">
                    <span class="material-symbols-outlined text-[14px] fc-color-var-vk-class-5979">corporate_fare</span>
                    <span>Internal</span>
                    <span class="fc-mono-11" id="filter-count-internal">(<?= $countInternal ?>)</span>
                </button>
                <button class="filter-pill pill-public" data-class-filter="public">
                    <span class="material-symbols-outlined text-[14px] fc-color-var-vk-class-8bc0">public</span>
                    <span>Public</span>
                    <span class="fc-mono-11" id="filter-count-public">(<?= $countPublic ?>)</span>
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
                    <div class="fc-flex-center-gap-8">
                        <span class="material-symbols-outlined text-[16px]">folder</span>
                        <span>All Partitions</span>
                    </div>
                    <span class="fc-mono-10" id="folder-count-all"><?= $totalDocs ?></span>
                </div>
                <div class="folder-item" data-folder-slug="governance">
                    <div class="fc-flex-center-gap-8">
                        <span class="material-symbols-outlined text-[16px]">gavel</span>
                        <span>/Corporate/Gov</span>
                    </div>
                    <span class="fc-mono-10" id="folder-count-gov"><?= $folderCounts['governance'] ?? 0 ?></span>
                </div>
                <div class="folder-item" data-folder-slug="projects">
                    <div class="fc-flex-center-gap-8">
                        <span class="material-symbols-outlined text-[16px]">precision_manufacturing</span>
                        <span>/Projects/Eng</span>
                    </div>
                    <span class="fc-mono-10" id="folder-count-eng"><?= $folderCounts['projects'] ?? 0 ?></span>
                </div>
                <div class="folder-item" data-folder-slug="contracts">
                    <div class="fc-flex-center-gap-8">
                        <span class="material-symbols-outlined text-[16px]">handshake</span>
                        <span>/Commercial/SOW</span>
                    </div>
                    <span class="fc-mono-10" id="folder-count-contracts"><?= $folderCounts['contracts'] ?? 0 ?></span>
                </div>
                <div class="folder-item" data-folder-slug="finance">
                    <div class="fc-flex-center-gap-8">
                        <span class="material-symbols-outlined text-[16px]">payments</span>
                        <span>/Finance/Billing</span>
                    </div>
                    <span class="fc-mono-10" id="folder-count-fin"><?= $folderCounts['finance'] ?? 0 ?></span>
                </div>
                <div class="folder-item" data-folder-slug="hr">
                    <div class="fc-flex-center-gap-8">
                        <span class="material-symbols-outlined text-[16px]">badge</span>
                        <span>/HR/Personnel</span>
                    </div>
                    <span class="fc-mono-10" id="folder-count-hr"><?= $folderCounts['hr'] ?? 0 ?></span>
                </div>
                <div class="folder-item" data-folder-slug="operations">
                    <div class="fc-flex-center-gap-8">
                        <span class="material-symbols-outlined text-[16px]">local_shipping</span>
                        <span>/Operations/Suppliers</span>
                    </div>
                    <span class="fc-mono-10" id="folder-count-ops"><?= $folderCounts['operations'] ?? 0 ?></span>
                </div>

                <div class="fc-margin-top-20px-padding-aa3e">
                    <div class="fc-font-family-var-font-afcc">Storage Quota</div>
                    <div class="fc-height-6px-background-var-86f3">
                        <div class="fc-height-4883" id="storage-quota-bar" style="width: <?= min(100, $vaultPct) ?>%; background: var(--vk-secondary); height: 100%; border-radius: 3px;"></div>
                    </div>
                    <div class="fc-display-flex-justify-content-6d82">
                        <span id="storage-quota-used"><?= number_format($vaultGb, 0) ?> GB used</span>
                        <span>4.8 TB cap</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT: MAIN DOCUMENT TABLE (Dynamic from Database) -->
            <div class="vk-table-container">
                <div class="fc-padding-12px-16px-background-6c79">
                    <div class="fc-font-family-var-font-ecce">
                        OFFICIAL DOCUMENT REGISTER (DATABASE-SOURCED)
                    </div>
                    <div class="fc-mono-muted-11">
                        Showing <span id="visible-docs-count"><?= count($documents) ?></span> documents
                    </div>
                </div>
                <table class="vk-table">
                    <thead>
                        <tr>
                            <th class="fc-width-130px-e314">DOC-ID</th>
                            <th>Filename &amp; Description</th>
                            <th class="fc-width-170px-ef4a">Classification</th>
                            <th class="fc-w-140">System Tag</th>
                            <th class="fc-width-110px-3e28">Status</th>
                            <th class="fc-width-160px-text-align-a6ce" style="width: 170px; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="repo-table-body">
                        <?php foreach ($documents as $doc): 
                            $classLower = strtolower($doc['classification']);
                            $classSlug = match ($classLower) {
                                'topsecret' => 'highly-confidential',
                                'confidential' => 'confidential',
                                'internal' => 'internal',
                                default => 'public',
                            };
                            $classBadgeText = match ($classLower) {
                                'topsecret' => 'Highly Confidential',
                                'confidential' => 'Confidential',
                                'internal' => 'Internal',
                                default => 'Public',
                            };
                            $rowClass = 'repo-doc-row vk-table-row-' . $classSlug;
                            $statusSlug = strtolower(str_replace(' ', '-', $doc['status']));
                            $statusBadgeClass = match ($statusSlug) {
                                'approved' => 'status-approved',
                                'in-review', 'pending' => 'status-in-review',
                                'draft' => 'status-draft',
                                default => 'status-approved',
                            };
                            $custodianDisplay = $doc['custodian_name'] ? "{$doc['custodian_name']} ({$doc['owner_emp_id']})" : ($doc['owner_emp_id'] ?? 'Farida Iskakova (EMP-1019)');
                        ?>
                        <tr class="<?= $rowClass ?>"
                            data-doc-id="<?= htmlspecialchars($doc['doc_id']) ?>"
                            data-doc-name="<?= htmlspecialchars($doc['file_name']) ?>"
                            data-classification="<?= $classSlug ?>"
                            data-folder="<?= htmlspecialchars($doc['folder'] ?? 'projects') ?>"
                            data-project="<?= htmlspecialchars($doc['project_ref'] ?? $doc['related_prj_id'] ?? 'PRJ-GOV-2026') ?>"
                            data-customer="<?= htmlspecialchars($doc['customer_ref'] ?? $doc['related_cus_id'] ?? '') ?>"
                            data-custodian="<?= htmlspecialchars($custodianDisplay) ?>"
                            data-size="<?= htmlspecialchars($doc['file_size'] ?? '2.0 MB') ?>"
                            data-date="<?= htmlspecialchars(substr($doc['created_at'], 0, 10)) ?>"
                            data-system="<?= htmlspecialchars($doc['owning_system'] ?? 'File Center') ?>"
                            data-status="<?= htmlspecialchars($doc['status'] ?? 'Approved') ?>"
                            data-retention="<?= htmlspecialchars($doc['retention_period'] ?? '7y') ?>"
                            data-description="<?= htmlspecialchars($doc['description'] ?? '') ?>"
                            data-dept="<?= htmlspecialchars($doc['department'] ?? 'ENG') ?>"
                            data-hash="<?= htmlspecialchars($doc['file_hash'] ?? 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855') ?>">
                            <td><code><?= htmlspecialchars($doc['doc_id']) ?></code></td>
                            <td>
                                <div class="fc-text-primary-bold"><?= htmlspecialchars($doc['file_name']) ?></div>
                                <div class="fc-text-muted-11"><?= htmlspecialchars($doc['description'] ?? '') ?></div>
                            </td>
                            <td><span class="vk-tag vk-tag-<?= $classSlug ?>"><?= $classBadgeText ?></span></td>
                            <td><span class="vk-tag fc-text-10"><?= htmlspecialchars($doc['owning_system'] ?? 'File Center') ?></span></td>
                            <td><span class="vk-status-badge <?= $statusBadgeClass ?>"><?= htmlspecialchars($doc['status'] ?? 'Approved') ?></span></td>
                            <td class="fc-text-right">
                                <div class="table-actions-cell">
                                    <button class="vk-btn vk-btn-sm vk-btn-outline btn-inspect-doc" type="button" title="Inspect">Inspect</button>
                                    <button class="vk-btn vk-btn-sm vk-btn-outline btn-edit-doc" type="button" title="Edit Metadata">
                                        <span class="material-symbols-outlined text-[14px]">edit</span>
                                    </button>
                                    <button class="vk-btn vk-btn-sm vk-btn-danger btn-delete-doc" type="button" title="Delete from Vault">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                    </button>
                                    <?php if (in_array($doc['status'], ['In Review', 'Pending'], true)): ?>
                                        <a class="vk-btn vk-btn-sm vk-btn-primary" href="approvals.php?doc_id=<?= urlencode($doc['doc_id']) ?>" title="Signoff & Review">Sign</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
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
                    <div class="fc-display-flex-align-items-0b62">
                        <span id="drawer-class-badge" class="vk-tag vk-tag-highly-confidential">HIGHLY CONFIDENTIAL</span>
                        <span class="fc-mono-muted-11" id="drawer-doc-id">DOC-2026-004</span>
                    </div>
                    <div class="fc-font-family-var-font-3629" id="drawer-doc-name">
                        PRJ-2026-002_Integration_Specification.pdf
                    </div>
                </div>
                <button class="fc-background-none-border-none-3e7e" type="button" id="btn-close-drawer">
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

                <div class="fc-display-grid-grid-template-38f6">
                    <div class="doc-meta-item">
                        <div class="doc-meta-label">File Size</div>
                        <div class="doc-meta-val" id="drawer-doc-size">4.5 MB</div>
                    </div>
                    <div class="doc-meta-item">
                        <div class="doc-meta-label">Last Ingested</div>
                        <div class="doc-meta-val" id="drawer-doc-date">2026-09-11</div>
                    </div>
                </div>

                <div class="fc-display-grid-grid-template-38f6">
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
                        <button class="fc-background-none-border-none-3e7e" type="button" id="btn-copy-drawer-hash" title="Copy Checksum">
                            <span class="material-symbols-outlined text-[16px]">content_copy</span>
                        </button>
                    </div>
                </div>

                <div class="fc-border-top-1px-solid-0abf">
                    <div class="doc-meta-label fc-margin-bottom-8px-ccd7">Access Control &amp; Redaction Rule</div>
                    <p class="fc-font-size-12px-color-d8b2" id="drawer-doc-desc">
                        Direct file extraction restricted to L3+ engineering clearance. For external client synchronization, access must be routed through the redaction approval pipeline.
                    </p>
                </div>

                <div class="fc-display-flex-gap-10px-a570" style="flex-wrap: wrap;">
                    <button class="vk-btn vk-btn-primary" id="btn-drawer-download" type="button">
                        <span class="material-symbols-outlined text-[16px]">download</span> Decrypt &amp; Download
                    </button>
                    <button class="vk-btn vk-btn-outline" id="btn-drawer-edit" type="button">
                        <span class="material-symbols-outlined text-[16px]">edit</span> Edit Metadata
                    </button>
                    <button class="vk-btn vk-btn-danger" id="btn-drawer-delete" type="button">
                        <span class="material-symbols-outlined text-[16px]">delete</span> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT DOCUMENT MODAL -->
    <div class="crud-modal-backdrop" id="edit-doc-modal">
        <div class="crud-modal-box">
            <div class="crud-modal-header">
                <div class="crud-modal-title">
                    <span class="material-symbols-outlined text-[20px] fc-color-accent">edit_document</span>
                    <span>Edit Document Metadata</span>
                    <span class="vk-tag fc-mono-11" id="edit-modal-doc-id-badge">DOC-ID</span>
                </div>
                <button type="button" class="fc-background-none-border-none-3e7e" id="btn-close-edit-modal">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <form id="edit-doc-form">
                <input type="hidden" id="edit-doc-id" />
                <div class="crud-modal-body">
                    <div class="crud-form-grid">
                        <div class="crud-form-group full-width">
                            <label class="crud-form-label" for="edit-file-name">Document Title / Filename *</label>
                            <input class="crud-form-input" id="edit-file-name" type="text" required />
                        </div>

                        <div class="crud-form-group full-width">
                            <label class="crud-form-label" for="edit-description">Abstract / Description</label>
                            <textarea class="crud-form-textarea" id="edit-description" rows="2"></textarea>
                        </div>

                        <div class="crud-form-group">
                            <label class="crud-form-label" for="edit-classification">Sensitivity Classification *</label>
                            <select class="crud-form-select" id="edit-classification" required>
                                <option value="topsecret">Highly Confidential (TopSecret)</option>
                                <option value="confidential">Confidential</option>
                                <option value="internal">Internal</option>
                                <option value="public">Public</option>
                            </select>
                        </div>

                        <div class="crud-form-group">
                            <label class="crud-form-label" for="edit-folder">Partition Folder *</label>
                            <select class="crud-form-select" id="edit-folder" required>
                                <option value="governance">/Corporate/Gov</option>
                                <option value="projects">/Projects/Eng</option>
                                <option value="contracts">/Commercial/SOW</option>
                                <option value="finance">/Finance/Billing</option>
                                <option value="hr">/HR/Personnel</option>
                                <option value="operations">/Operations/Suppliers</option>
                            </select>
                        </div>

                        <div class="crud-form-group">
                            <label class="crud-form-label" for="edit-department">Originating Department *</label>
                            <select class="crud-form-select" id="edit-department" required>
                                <option value="ENG">ENG — Engineering</option>
                                <option value="EXE">EXE — Executive / Board</option>
                                <option value="SAL">SAL — Sales &amp; Commercial</option>
                                <option value="FIN">FIN — Finance &amp; Billing</option>
                                <option value="HR">HR — Human Resources</option>
                                <option value="OPS">OPS — Operations</option>
                            </select>
                        </div>

                        <div class="crud-form-group">
                            <label class="crud-form-label" for="edit-status">Lifecycle Status *</label>
                            <select class="crud-form-select" id="edit-status" required>
                                <option value="Approved">Approved</option>
                                <option value="In Review">In Review</option>
                                <option value="Pending">Pending</option>
                                <option value="Draft">Draft</option>
                                <option value="Archived">Archived</option>
                            </select>
                        </div>

                        <div class="crud-form-group">
                            <label class="crud-form-label" for="edit-project-ref">Project Reference</label>
                            <input class="crud-form-input" id="edit-project-ref" type="text" placeholder="e.g. PRJ-2026-002" />
                        </div>

                        <div class="crud-form-group">
                            <label class="crud-form-label" for="edit-customer-ref">Customer Reference</label>
                            <input class="crud-form-input" id="edit-customer-ref" type="text" placeholder="e.g. CUS-1002 (BaltNord)" />
                        </div>

                        <div class="crud-form-group full-width">
                            <label class="crud-form-label" for="edit-retention">Retention Scope</label>
                            <select class="crud-form-select" id="edit-retention">
                                <option value="3y">3 Years</option>
                                <option value="5y">5 Years</option>
                                <option value="7y">7 Years</option>
                                <option value="10y">10 Years</option>
                                <option value="permanent">Permanent / WORM Hold</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="crud-modal-footer">
                    <button type="button" class="vk-btn vk-btn-outline" id="btn-cancel-edit">Cancel</button>
                    <button type="submit" class="vk-btn vk-btn-primary">
                        <span class="material-symbols-outlined text-[16px]">save</span> Save Changes to Database
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div class="crud-modal-backdrop" id="delete-doc-modal">
        <div class="crud-modal-box danger-modal">
            <div class="crud-modal-header" style="background: #fef2f2; border-bottom-color: #fee2e2;">
                <div class="crud-modal-title" style="color: #991b1b;">
                    <span class="material-symbols-outlined text-[22px] fc-color-var-vk-alert-6578">warning</span>
                    <span>Confirm Vault Document Deletion</span>
                </div>
                <button type="button" class="fc-background-none-border-none-3e7e" id="btn-close-delete-modal">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="crud-modal-body">
                <p style="font-size: 13px; color: var(--vk-neutral-700); line-height: 1.5; margin-bottom: 12px;">
                    Are you sure you want to permanently delete document <strong id="delete-target-id" style="font-family: var(--font-mono); color: #991b1b;">DOC-ID</strong> (<span id="delete-target-name">filename</span>) from the VOSTOKPRIBOR encrypted vault?
                </p>
                <div style="background: #fff1f2; border: 1px solid #fecdd3; padding: 12px; border-radius: var(--radius-sm); font-size: 12px; color: #9f1239;">
                    <strong>Security Warning:</strong> This operation will purge the record, child versions, and approval logs from MySQL. If this document is under Legal Hold, deletion will be rejected.
                </div>
            </div>
            <div class="crud-modal-footer">
                <button type="button" class="vk-btn vk-btn-outline" id="btn-cancel-delete">Cancel</button>
                <button type="button" class="vk-btn vk-btn-danger" id="btn-confirm-delete">
                    <span class="material-symbols-outlined text-[16px]">delete_forever</span> Confirm Permanent Deletion
                </button>
            </div>
        </div>
    </div>

    <!-- DOCKED ENTERPRISE STATUS BAR -->
    <footer class="vk-status-bar">
        <div class="fc-flex-center-gap-16">

            <div class="fc-flex-center-gap-8">
                <span class="status-dot-pulse"></span>
                <span>ALMATY-VAULT-01 // HSM CLUSTER SYNCHRONIZED</span>
            </div>
            <span>VOLUME: <?= number_format($vaultGb, 1) ?> GB / 4.8 TB (<?= $vaultPct ?>%)</span>
        </div>
        <div class="fc-display-flex-align-items-bf7c">
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
                    <span>Document Repository (All <?= $totalDocs ?> Statutory Records)</span>
                </a>
                <a class="cmd-palette-item" href="approvals.php">
                    <span class="material-symbols-outlined text-[16px]">assignment_turned_in</span>
                    <span>Signoff &amp; Approvals &bull; Multi-Stage Clearance</span>
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
    <script src="js/fc-repo.js"></script>
  <script src="../assets/js/notifications-hub.js" defer></script>
</body>

</html>