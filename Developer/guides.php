<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('DEV');
$pdo = getDbConnection();
require_once __DIR__ . '/api/db_helper.php';
ensureDeveloperTables($pdo);
$currUser = $_SESSION['vostok_user'] ?? ['full_name' => 'Authorized User', 'clearance_level' => 'L2'];

// Fetch all guides dynamically from database
$guides = [];
try {
    $stmt = $pdo->query("SELECT * FROM developer_guides ORDER BY section_number ASC, id ASC");
    $guides = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $guides = [];
}

// Separate statutory document hero from section guides
$statutoryDoc = null;
$sectionGuides = [];
foreach ($guides as $g) {
    if ($g['guide_code'] === 'DOC-2026-010' || $g['category'] === 'Statutory Specification') {
        $statutoryDoc = $g;
    } else {
        $sectionGuides[] = $g;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Integration Guides &amp; Standards - VOSTOKPRIBOR Developer Portal</title>
    <link rel="stylesheet" href="css/dev-tokens.css" />
    <link rel="stylesheet" href="css/dev-common.css" />
    <link rel="stylesheet" href="css/dev-portal.css" />
</head>

<body>
    <!-- TOP NAVIGATION BAR -->
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
                <span class="material-symbols-outlined text-[14px] dev-color-accent">architecture</span>
                <span class="dev-font-family-var-font-eb0b" >DOCS: <strong>DOC-2026-010 Specification</strong></span>
            </div>
        </div>

        <div class="dev-flex-center-gap-16" >
<button class="icon-button notifications-btn" id="notifications-toggle-btn" title="Live Enterprise Notifications" style="background:transparent;border:none;color:#bdc6cf;cursor:pointer;padding:6px;display:flex;align-items:center;justify-content:center;position:relative;">
                <span class="material-symbols-outlined text-[20px]">notifications</span>
            </button>
            <div class="dev-search-box-wrap" >
                <span class="material-symbols-outlined text-[16px] dev-position-absolute-left-10px-d4a8">search</span>
                <input class="search-trigger-input" type="text" placeholder="Search guides, RFCs, protocols (Ctrl + K)" readonly />
            </div>
            <div class="dev-display-flex-align-items-9eca" >
                <span class="material-symbols-outlined text-[14px] dev-color-secondary">schedule</span>
                <span class="station-live-clock">17:15:00 UTC+6</span>
            </div>
            <div class="dev-display-flex-align-items-20f3" >
                <div class="dev-text-right" >
                    <div class="dev-font-size-12px-font-2ab2" >Kristaps Ozols</div>
                    <div class="dev-font-family-var-font-b636" >CUS-1002 • BaltNord</div>
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
    <aside class="vk-sidebar">
        <div class="vk-sidebar-nav">
            <div class="vk-sidebar-header">Core Documentation</div>
            <a class="vk-nav-item" href="Dashboard.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">menu_book</span>
                    <span>API Reference</span>
                </div>
                <span class="dev-font-family-var-font-36a4" >REST</span>
            </a>
            <a class="vk-nav-item active" href="guides.php">
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
                <span class="dev-font-family-var-font-7016" >99.98%</span>
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

        <div class="dev-padding-16px-border-top-156b" >
            <div class="dev-font-family-var-font-6447" >Integration Engineering</div>
            <div class="dev-color-var-vk-primary-1a7b" >Dana Yermak (EMP-1017)</div>
            <div class="dev-color-var-vk-neutral-19bc" >Integration Engineer • ENG</div>
            <div class="dev-color-var-vk-primary-57e8" >Jonas Richter (EMP-1020)</div>
            <div class="dev-color-var-vk-neutral-19bc" >Lead Developer • ENG</div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="vk-app-body">
        <div class="dev-margin-bottom-24px-dc2c" style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div class="dev-display-flex-align-items-81c3" >
                    <span class="dev-mono-muted-11" >ROOT / SYSTEM 10 / GUIDES &amp; PROTOCOLS</span>
                    <span class="badge-classification badge-internal">Internal Specification</span>
                </div>
                <h1 class="dev-font-size-26px-font-2295" >
                    Enterprise Integration Guides &amp; Protocols
                </h1>
                <p class="dev-color-var-vk-neutral-1aeb" >
                    Architectural blueprints and statutory integration standards for connecting client ERP networks, SCADA controllers, and logistics feeds with VOSTOKPRIBOR.
                </p>
            </div>
            <div>
                <button class="vk-btn vk-btn-primary" id="btnOpenCreateGuide">
                    <span class="material-symbols-outlined text-[16px]">add</span> Add Guide / Protocol
                </button>
            </div>
        </div>

        <!-- STATUTORY DOCUMENT HERO CARD (DOC-2026-010) -->
        <?php if ($statutoryDoc): 
            $statJson = htmlspecialchars(json_encode($statutoryDoc), ENT_QUOTES, 'UTF-8');
        ?>
        <div class="vk-card tag-internal dev-margin-bottom-24px-dc2c" id="doc010">
            <div class="vk-card-header dev-background-color-f8fafc-fb8d" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="dev-display-flex-align-items-1c20" >
                    <span class="material-symbols-outlined text-[22px] dev-color-var-vk-class-5979"><?= htmlspecialchars($statutoryDoc['icon'] ?: 'description') ?></span>
                    <div>
                        <div class="dev-font-weight-700-font-d74c" ><?= htmlspecialchars($statutoryDoc['title']) ?></div>
                        <div class="dev-mono-muted-11" ><?= htmlspecialchars($statutoryDoc['guide_code']) ?> • <?= htmlspecialchars($statutoryDoc['category']) ?></div>
                    </div>
                </div>
                <div class="dev-display-flex-gap-8px-1326" >
                    <span class="badge-classification badge-internal"><?= htmlspecialchars($statutoryDoc['classification']) ?></span>
                    <button class="vk-btn vk-btn-outline dev-padding-4px-10px-font-9ec7" onclick="window.showToast('SPEC DOWNLOAD', 'Exported statutory document DOC-2026-010.pdf', 'success')">
                        <span class="material-symbols-outlined text-[14px]">file_download</span> Download PDF
                    </button>
                    <button class="btn-crud-action btn-crud-edit btn-edit-guide" data-guide='<?= $statJson ?>' title="Edit Statutory Specification">
                        <span class="material-symbols-outlined text-[14px]">edit</span>
                    </button>
                </div>
            </div>
            <div class="vk-card-body dev-line-height-1-6-80c9">
                <p class="dev-text-muted dev-mb-16">
                    <?= nl2br(htmlspecialchars($statutoryDoc['summary'])) ?>
                </p>
                <?php if (!empty($statutoryDoc['code_snippet'])): ?>
                <div class="dev-background-color-0a1624-padding-e1fb dev-mb-16" style="border-radius: 6px; font-family: monospace; font-size: 13px; color: #38bdf8;">
                    <?= htmlspecialchars($statutoryDoc['code_snippet']) ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($statutoryDoc['footer_note'])): ?>
                <div class="dev-font-size-12px-color-fb5d">
                    <strong>Attestation Note:</strong> <?= htmlspecialchars($statutoryDoc['footer_note']) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- SECTION GUIDES (Dynamic from Database with CRUD) -->
        <div class="dev-display-flex-flex-direction-e9a4" id="guidesContainer">
            <?php if (empty($sectionGuides)): ?>
            <div class="vk-card dev-padding-24px dev-text-center dev-text-muted">
                No integration guides configured yet. Click "Add Guide / Protocol" to register one.
            </div>
            <?php else: ?>
            <?php foreach ($sectionGuides as $guide): 
                $badgeClass = 'badge-internal';
                $cardTag = 'tag-internal';
                $clsLower = strtolower($guide['classification']);
                if (str_contains($clsLower, 'confidential')) {
                    $badgeClass = 'badge-confidential';
                    $cardTag = 'tag-confidential';
                } elseif (str_contains($clsLower, 'public')) {
                    $badgeClass = 'badge-public';
                    $cardTag = 'tag-public';
                }
                $gJson = htmlspecialchars(json_encode($guide), ENT_QUOTES, 'UTF-8');
            ?>
            <div class="vk-card <?= $cardTag ?>" id="guide-card-<?= $guide['id'] ?>">
                <div class="vk-card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="dev-flex-center-gap-10" >
                        <span class="material-symbols-outlined text-[20px] dev-color-accent"><?= htmlspecialchars($guide['icon'] ?: 'menu_book') ?></span>
                        <h3 class="dev-font-size-15px-font-578e" >
                            <?= $guide['section_number'] ?>. <?= htmlspecialchars($guide['title']) ?>
                        </h3>
                    </div>
                    <div class="dev-flex-center-gap-8">
                        <span class="badge-classification <?= $badgeClass ?>"><?= htmlspecialchars($guide['classification']) ?></span>
                        <button class="btn-crud-action btn-crud-edit btn-edit-guide" data-guide='<?= $gJson ?>' title="Edit Guide">
                            <span class="material-symbols-outlined text-[14px]">edit</span>
                        </button>
                        <button class="btn-crud-action btn-crud-delete btn-delete-guide" data-id="<?= $guide['id'] ?>" data-title="<?= htmlspecialchars($guide['title']) ?>" title="Delete Guide">
                            <span class="material-symbols-outlined text-[14px]">delete</span>
                        </button>
                    </div>
                </div>
                <div class="vk-card-body">
                    <p class="dev-color-var-vk-neutral-5df1 dev-mb-16">
                        <?= nl2br(htmlspecialchars($guide['summary'])) ?>
                    </p>

                    <?php if (!empty($guide['code_snippet'])): ?>
                    <div class="dev-background-color-0a1624-padding-e1fb dev-mb-16" style="border-radius: 6px; font-family: monospace; font-size: 13px; color: #38bdf8; overflow-x: auto; white-space: pre-wrap;">
<?= htmlspecialchars($guide['code_snippet']) ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($guide['footer_note'])): ?>
                    <p class="dev-color-var-vk-neutral-b40a dev-font-size-12px">
                        <?= htmlspecialchars($guide['footer_note']) ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- CREATE / EDIT GUIDE MODAL -->
    <div class="vk-modal-overlay" id="guideModal">
        <div class="vk-modal-dialog" style="max-width: 600px;">
            <div class="vk-modal-header">
                <div class="dev-flex-center-gap-8">
                    <span class="material-symbols-outlined text-[20px] dev-color-accent">integration_instructions</span>
                    <h3 id="guideModalTitle" class="dev-font-size-15px-font-29ad">Register New Integration Guide</h3>
                </div>
                <button class="dev-background-transparent-border-none-aba8" id="btnCloseGuideModal" style="color: #94a3b8; cursor: pointer;">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
            <div class="vk-modal-body">
                <input type="hidden" id="guideId" value="" />
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="guideCode">Guide Code *</label>
                        <input class="crud-form-input" id="guideCode" type="text" placeholder="e.g. GUIDE-004" required />
                    </div>
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="guideSection">Section Number</label>
                        <input class="crud-form-input" id="guideSection" type="number" value="4" />
                    </div>
                </div>
                <div class="crud-form-group">
                    <label class="crud-form-label" for="guideTitle">Guide Title *</label>
                    <input class="crud-form-input" id="guideTitle" type="text" placeholder="e.g. Modbus TCP Gateway Bridging Protocol" required />
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="guideCategory">Category</label>
                        <input class="crud-form-input" id="guideCategory" type="text" value="Protocol Specification" />
                    </div>
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="guideClassification">Classification</label>
                        <select class="crud-form-select" id="guideClassification">
                            <option value="Internal Standard">Internal Standard</option>
                            <option value="Confidential">Confidential</option>
                            <option value="Public Spec">Public Spec</option>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="guideIcon">Material Icon</label>
                        <input class="crud-form-input" id="guideIcon" type="text" value="menu_book" placeholder="e.g. lock, sync_alt" />
                    </div>
                </div>
                <div class="crud-form-group">
                    <label class="crud-form-label" for="guideSummary">Summary &amp; Implementation Details *</label>
                    <textarea class="crud-form-textarea" id="guideSummary" style="height: 100px;" placeholder="Full architectural details and usage recommendations..." required></textarea>
                </div>
                <div class="crud-form-group">
                    <label class="crud-form-label" for="guideSnippet">Code / Endpoint Snippet</label>
                    <textarea class="crud-form-textarea" id="guideSnippet" style="height: 60px; font-family: monospace;" placeholder="Authorization: Bearer vk_live_... or https://..."></textarea>
                </div>
                <div class="crud-form-group">
                    <label class="crud-form-label" for="guideFooterNote">Footer / Attestation Note</label>
                    <input class="crud-form-input" id="guideFooterNote" type="text" placeholder="e.g. Attested under ISO 27001 & ST RK IEC 62443." />
                </div>
            </div>
            <div class="vk-modal-footer">
                <button class="vk-btn vk-btn-outline" id="btnCancelGuideModal">Cancel</button>
                <button class="vk-btn vk-btn-primary" id="btnSaveGuide">
                    <span class="material-symbols-outlined text-[16px]">save</span> Save Guide
                </button>
            </div>
        </div>
    </div>

    <!-- PUBLIC-FACING / DEVELOPER FOOTER -->
    <footer class="vk-footer">
        <div class="vk-footer-bottom dev-border-top-none-padding-efbd">
            <div>&copy; 2026 VOSTOKPRIBOR Global Logistics &amp; Supply JSC. System 10: Developer &amp; API Portal.</div>
            <div>DOC-2026-010 Specification • Almaty Enclave Engineering</div>
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
    <script src="js/dev-guides.js"></script>
  <script src="../assets/js/notifications-hub.js" defer></script>
</body>

</html>