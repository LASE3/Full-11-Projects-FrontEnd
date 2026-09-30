<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('DEV');
$pdo = getDbConnection();
require_once __DIR__ . '/api/db_helper.php';
ensureDeveloperTables($pdo);

// Fetch all keys from database
$stmt = $pdo->query("SELECT * FROM `developer_api_keys` ORDER BY `id` DESC");
$keys = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Live KPI Aggregations
$activeKeysTotal = 0;
$prodKeysCount = 0;
$sandboxKeysCount = 0;
$aggregatedQuotaSum = 0;

foreach ($keys as $k) {
    if ($k['status'] === 'Active') {
        $activeKeysTotal++;
        if ($k['environment'] === 'Production') {
            $prodKeysCount++;
        } else {
            $sandboxKeysCount++;
        }
        $aggregatedQuotaSum += (int)($k['rate_limit_value'] ?: 10000);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>API Keys &amp; Partner Vault - VOSTOKPRIBOR Developer Portal</title>
    <link rel="stylesheet" href="css/dev-tokens.css" />
    <link rel="stylesheet" href="css/dev-common.css" />
    <link rel="stylesheet" href="css/dev-credentials.css" />
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
                <span class="material-symbols-outlined text-[14px] dev-color-accent">key</span>
                <span class="dev-font-family-var-font-eb0b" >VAULT: <strong>PARTNER ENCLAVE CREDENTIALS</strong></span>
            </div>
        </div>

        <div class="dev-flex-center-gap-16" >
<button class="icon-button notifications-btn" id="notifications-toggle-btn" title="Live Enterprise Notifications" style="background:transparent;border:none;color:#bdc6cf;cursor:pointer;padding:6px;display:flex;align-items:center;justify-content:center;position:relative;">
                <span class="material-symbols-outlined text-[20px]">notifications</span>
            </button>
            <div class="dev-search-box-wrap" >
                <span class="material-symbols-outlined text-[16px] dev-position-absolute-left-10px-d4a8">search</span>
                <input class="search-trigger-input" type="text" placeholder="Search keys, tokens (Ctrl + K)" readonly
                     />
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
            <a href="../api/logout.php?system=Developer&redirect=../Developer/login.php" class="top-signout-btn" title="Sign Out of Developer" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" ><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
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
            <a class="vk-nav-item" href="guides.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">architecture</span>
                    <span>Integration Guides</span>
                </div>
                <span class="badge-classification badge-internal dev-font-size-8px-c136">DOC-010</span>
            </a>

            <div class="vk-sidebar-header dev-mt-16">Partner Enclave</div>
            <a class="vk-nav-item active" href="credentials.php">
                <div class="dev-flex-center-gap-10" >
                    <span class="material-symbols-outlined text-[18px]">key</span>
                    <span>API Credentials</span>
                </div>
                <span class="dev-font-family-var-font-829c" >Vault</span>
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
            <div class="dev-font-family-var-font-6447" >Key Rotation Enclave</div>
            <div class="dev-text-muted" >HSM Policy: FIPS 140-3</div>
            <div class="dev-color-var-vk-neutral-1ba7" >90-day automated expiry enforced</div>
        </div>

        <!-- Log Out -->

    </aside>

    <!-- MAIN CONTENT -->
    <main class="vk-app-body">
        <div class="dev-display-flex-justify-content-387e" >
            <div>
                <div class="dev-display-flex-align-items-81c3" >
                    <span class="dev-mono-muted-11" >ROOT / SYSTEM 10 / CREDENTIALS VAULT</span>
                    <span class="badge-classification badge-confidential">Confidential Keyring</span>
                </div>
                <h1 class="dev-font-size-26px-font-2295" >
                    Partner API Keys &amp; Cryptographic Tokens
                </h1>
                <p class="dev-color-var-vk-neutral-5666" >
                    Manage authenticated tokens, environment permissions, and rate-limit quotas for your integration client <strong>BaltNord Process Systems (CUS-1002)</strong>.
                </p>
            </div>
            <button class="vk-btn vk-btn-accent" id="btnOpenGenKey">
                <span class="material-symbols-outlined text-[16px]">add_circle</span> Generate New API Key
            </button>
        </div>

        <!-- Quota Overview Strip (Dynamic from Database) -->
        <div class="vk-card tag-confidential dev-margin-bottom-24px-dc2c">
            <div class="vk-card-body dev-display-grid-grid-template-590a">
                <div>
                    <span class="dev-font-family-var-font-d07a" >Active Keys</span>
                    <div class="dev-font-family-var-font-5f4a" id="statActiveKeys"><?= sprintf("%02d Active", $activeKeysTotal) ?></div>
                    <div class="dev-font-size-12px-color-926b" id="statEnvBreakdown"><?= $prodKeysCount ?> Prod • <?= $sandboxKeysCount ?> Sandbox</div>
                </div>
                <div>
                    <span class="dev-font-family-var-font-d07a" >Aggregated Rate Quota</span>
                    <div class="dev-font-family-var-font-5f4a" id="statRateQuota"><?= number_format($aggregatedQuotaSum) ?> / min</div>
                    <div class="dev-font-size-12px-color-f7e3" >Burst allowance: <?= number_format($aggregatedQuotaSum * 2) ?></div>
                </div>
                <div>
                    <span class="dev-font-family-var-font-d07a" >Security Tier</span>
                    <div class="dev-font-family-var-font-d805" >LEVEL 3</div>
                    <div class="dev-font-size-12px-color-f7e3" >CUS-1002 Verified Partner</div>
                </div>
            </div>
        </div>

        <!-- KEYS DATA TABLE (100% Dynamic from Database) -->
        <div class="vk-table-container">
            <table class="vk-table">
                <thead>
                    <tr>
                        <th>Key Label &amp; Identifier</th>
                        <th>Token Prefix / Secret</th>
                        <th>Environment</th>
                        <th>Rate Limit</th>
                        <th>Classification</th>
                        <th>Status</th>
                        <th class="dev-text-right" >Action</th>
                    </tr>
                </thead>
                <tbody id="keysTableBody">
                    <?php if (empty($keys)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: var(--vk-neutral-500);">
                            No API keys generated yet. Click "Generate New API Key" above to mint one.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($keys as $k): 
                        $isConf = str_contains(strtolower($k['classification']), 'confidential');
                        $rowClass = $isConf ? 'tag-confidential' : 'tag-internal';
                        $badgeClass = $isConf ? 'badge-confidential' : 'badge-internal';
                        $statusClass = $k['status'] === 'Active' ? ($k['environment'] === 'Sandbox' ? 'status-sandbox' : 'status-active') : 'status-revoked';
                        $fillPercent = min(100, max(5, round((($k['usage_count'] ?? 500) / max(1, $k['rate_limit_value'] ?: 10000)) * 100)));
                        $keyJson = htmlspecialchars(json_encode($k), ENT_QUOTES, 'UTF-8');
                    ?>
                    <tr class="<?= $rowClass ?>" id="key-row-<?= $k['id'] ?>" data-key-id="<?= $k['id'] ?>">
                        <td>
                            <div class="dev-text-primary-bold" ><?= htmlspecialchars($k['label']) ?></div>
                            <div class="dev-mono-muted-11" >ID: <?= htmlspecialchars($k['key_identifier']) ?> • <?= htmlspecialchars($k['partner_id']) ?></div>
                        </td>
                        <td>
                            <div class="key-token-display">
                                <span><?= htmlspecialchars($k['token_prefix']) ?>••••••••</span>
                                <button class="vk-btn-outline dev-padding-2px-6px-font-ed80" title="Copy full cryptographic token" onclick="window.copyText('<?= htmlspecialchars($k['token_full']) ?>', 'Full API token copied to clipboard')">
                                    <span class="material-symbols-outlined text-[14px]">content_copy</span>
                                </button>
                            </div>
                        </td>
                        <td>
                            <span class="vk-status-badge <?= $k['environment'] === 'Sandbox' ? 'status-sandbox' : 'status-active' ?>"><?= strtoupper(htmlspecialchars($k['environment'])) ?></span>
                        </td>
                        <td>
                            <div class="dev-font-family-var-font-ef2e" ><?= htmlspecialchars($k['rate_limit']) ?></div>
                            <div class="rate-limit-bar-bg">
                                <div class="rate-limit-bar-fill" style="width: <?= $fillPercent ?>%;"></div>
                            </div>
                        </td>
                        <td>
                            <span class="badge-classification <?= $badgeClass ?>"><?= htmlspecialchars($k['classification']) ?></span>
                        </td>
                        <td>
                            <span class="vk-status-badge <?= $statusClass ?> key-status-badge"><?= strtoupper(htmlspecialchars($k['status'])) ?></span>
                        </td>
                        <td class="dev-text-right" style="white-space: nowrap;">
                            <button class="btn-crud-action btn-crud-edit btn-edit-key" 
                                data-key='<?= $keyJson ?>' 
                                title="Edit this key configuration">
                                <span class="material-symbols-outlined text-[14px]">edit</span>
                            </button>
                            
                            <?php if ($k['status'] === 'Active'): ?>
                            <button class="btn-crud-action btn-revoke-key" 
                                data-id="<?= $k['id'] ?>" 
                                style="color: var(--vk-class-high-confidential);"
                                title="Revoke this key across all regional gateways">
                                <span class="material-symbols-outlined text-[14px]">block</span> Revoke
                            </button>
                            <?php else: ?>
                            <button class="btn-crud-action" disabled style="color: #94a3b8; opacity: 0.6;">
                                <span class="material-symbols-outlined text-[14px]">done</span> Revoked
                            </button>
                            <?php endif; ?>

                            <button class="btn-crud-action btn-crud-delete btn-delete-key" 
                                data-id="<?= $k['id'] ?>"
                                data-label="<?= htmlspecialchars($k['label']) ?>"
                                title="Permanently delete this key from database">
                                <span class="material-symbols-outlined text-[14px]">delete</span>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- GENERATE KEY MODAL DIALOG -->
    <div class="vk-modal-overlay" id="genKeyModal" >
        <div class="vk-modal-dialog">
            <div class="vk-modal-header" >
                <div class="dev-flex-center-gap-8" >
                    <span class="material-symbols-outlined text-[20px] dev-color-accent">key</span>
                    <h3 class="dev-font-size-15px-font-29ad" >Generate New Partner API Token</h3>
                </div>
                <button class="dev-background-transparent-border-none-aba8" id="btnCloseGenKey" style="color: #94a3b8; cursor: pointer;" >
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
            <div class="vk-modal-body" >
                <div class="crud-form-group">
                    <label class="crud-form-label" for="keyLabelInput">Key Label / Service Name *</label>
                    <input class="crud-form-input" id="keyLabelInput" type="text" placeholder="e.g. BaltNord Warehouse PLC Bridge" required />
                </div>
                <div class="crud-form-group">
                    <label class="crud-form-label">Environment Target</label>
                    <div style="display: flex; gap: 16px; margin-top: 4px;">
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px;">
                            <input type="radio" name="keyEnv" value="Production" checked /> Production Enclave
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px;">
                            <input type="radio" name="keyEnv" value="Sandbox" /> Sandbox Testing
                        </label>
                    </div>
                </div>
                <div class="crud-form-group">
                    <label class="crud-form-label" for="keyRateSelect">Requested Rate Limit</label>
                    <select class="crud-form-select" id="keyRateSelect" >
                        <option value="2,500">2,500 requests / minute (Standard)</option>
                        <option value="10,000" selected>10,000 requests / minute (Enterprise Stream)</option>
                        <option value="50,000">50,000 requests / minute (SCADA High-Frequency Batch)</option>
                    </select>
                </div>
                <div class="crud-form-group">
                    <label class="crud-form-label">Permission Scopes</label>
                    <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 4px;">
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                            <input type="checkbox" id="scopeTelemetry" checked /> <code>telemetry:read</code> (Optical &amp; Geodetic sensors)
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                            <input type="checkbox" id="scopeScada" checked /> <code>scada:ingest</code> (PLC high-speed frames)
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                            <input type="checkbox" id="scopeOrders" /> <code>orders:write</code> (B2B procurement pipeline)
                        </label>
                    </div>
                </div>
            </div>
            <div class="vk-modal-footer" >
                <button class="vk-btn vk-btn-outline" id="btnCancelGenKey">Cancel</button>
                <button class="vk-btn vk-btn-accent" id="btnSubmitGenKey">
                    <span class="material-symbols-outlined text-[16px]">vpn_key</span> Mint Key &amp; Save
                </button>
            </div>
        </div>
    </div>

    <!-- EDIT KEY MODAL DIALOG -->
    <div class="vk-modal-overlay" id="editKeyModal" >
        <div class="vk-modal-dialog">
            <div class="vk-modal-header" >
                <div class="dev-flex-center-gap-8" >
                    <span class="material-symbols-outlined text-[20px] dev-color-accent">edit</span>
                    <h3 class="dev-font-size-15px-font-29ad" id="editKeyModalTitle">Edit API Key Configuration</h3>
                </div>
                <button class="dev-background-transparent-border-none-aba8" id="btnCloseEditKey" style="color: #94a3b8; cursor: pointer;" >
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
            <div class="vk-modal-body" >
                <input type="hidden" id="editKeyId" value="" />
                
                <div class="crud-form-group">
                    <label class="crud-form-label" for="editKeyLabel">Key Label / Service Name *</label>
                    <input class="crud-form-input" id="editKeyLabel" type="text" required />
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="editKeyEnv">Environment</label>
                        <select class="crud-form-select" id="editKeyEnv">
                            <option value="Production">Production</option>
                            <option value="Sandbox">Sandbox</option>
                            <option value="Staging">Staging</option>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <label class="crud-form-label" for="editKeyStatus">Status</label>
                        <select class="crud-form-select" id="editKeyStatus">
                            <option value="Active">Active</option>
                            <option value="Revoked">Revoked</option>
                            <option value="Suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                <div class="crud-form-group">
                    <label class="crud-form-label" for="editKeyRateSelect">Rate Limit</label>
                    <select class="crud-form-select" id="editKeyRateSelect" >
                        <option value="2,500">2,500 req/min (Standard)</option>
                        <option value="10,000">10,000 req/min (Enterprise Stream)</option>
                        <option value="50,000">50,000 req/min (High-Frequency Batch)</option>
                    </select>
                </div>
                <div class="crud-form-group">
                    <label class="crud-form-label" for="editKeyScopes">Scopes (comma separated)</label>
                    <input class="crud-form-input" id="editKeyScopes" type="text" placeholder="telemetry:read,scada:ingest" />
                </div>
            </div>
            <div class="vk-modal-footer" >
                <button class="vk-btn vk-btn-outline" id="btnCancelEditKey">Cancel</button>
                <button class="vk-btn vk-btn-accent" id="btnSubmitEditKey">
                    <span class="material-symbols-outlined text-[16px]">save</span> Update Key in Database
                </button>
            </div>
        </div>
    </div>

        <footer class="vk-footer">
        <div class="vk-footer-bottom dev-border-top-none-padding-efbd">
            <div>© 2026 VOSTOKPRIBOR Global Logistics &amp; Supply JSC. System 10: Developer &amp; API Portal.</div>
            <div>Vault Security: FIPS 140-3 Hardware Token Anchored • Almaty Enclave</div>
        </div>
    </footer>

    <script src="js/dev-common.js"></script>
    <script src="js/dev-credentials.js"></script>
  <script src="../assets/js/notifications-hub.js" defer></script>
</body>

</html>