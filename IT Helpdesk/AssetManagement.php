<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('IT');
require_once __DIR__ . '/api/db_helper.php';
$pdo = getItDb();
$currUser = $_SESSION['vostok_user'] ?? ['full_name' => 'Alexey Ivanov', 'clearance_level' => 'L2'];

// Query all assets
$stmt = $pdo->query("SELECT * FROM it_assets ORDER BY created_at DESC");
$assets = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalAssets = count($assets);

// Badges
$openCountStmt = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status != 'Resolved'");
$openCount = (int)$openCountStmt->fetchColumn();

$myTicketsStmt = $pdo->query("SELECT COUNT(*) FROM tickets WHERE (assigned_to LIKE '%Alexey%' OR assigned_emp_id = 'EMP-1018') AND status != 'Resolved'");
$myTicketsCount = (int)$myTicketsStmt->fetchColumn();

$kbCountStmt = $pdo->query("SELECT COUNT(*) FROM knowledge_base_articles");
$kbCount = (int)$kbCountStmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR IT Helpdesk · Industrial Asset Inventory</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>

  <div class="app-container">
    <!-- TOP NAVIGATION BAR -->
    <header class="top-nav">
      <div class="top-nav__accent-stripe"></div>
      <div class="top-nav__content">
        <div class="brand-section">
          <a href="Dashboard.php" class="brand-logo-container">
            <img alt="VOSTOKPRIBOR Official Mark" class="brand-logo-img" src="assets/logo.svg" />
            <div class="brand-divider"></div>
            <div class="brand-title-group">
              <div class="brand-title-row">
                <span class="brand-name">VOSTOKPRIBOR</span>
                <span class="system-tag">IT · SYS 08</span>
              </div>
              <div class="brand-subline">
                <span class="status-dot-pulse"></span>
                <span>helpdesk.vostokpribor.local</span>
                <span class="hd-opacity-50">|</span>
                <span>SUPPORT OPERATIONS</span>
              </div>
            </div>
          </a>
        </div>

        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search asset tag, serial number, IP address, plant bay..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <div class="top-nav__actions">
          <div class="pipeline-sync-badge hd-badge-telemetry">
            <span class="hd-status-success">●</span>
            <span>SLA: <strong>98.4% Compliant</strong></span>
          </div>
          <button class="icon-button" title="Incident Telemetry Notifications" onclick="window.hdApp.showToast('Critical Alert', 'SCADA Gateway Node #3 packet loss detected in Lipetsk Bay.', 'critical')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
              <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
            <span class="badge-dot"></span>
          </button>
          <div class="top-user-profile" onclick="window.hdApp.showToast('Active Tech Session', '<?= htmlspecialchars($currUser['full_name']) ?> · Lead IT Engineer')">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" alt="Alexey Ivanov" class="user-avatar-top" />
            <div class="user-details-top">
              <span class="user-name-top"><?= htmlspecialchars($currUser['full_name']) ?></span>
              <span class="user-role-top">Lead IT Tech · Tier 3</span>
            </div>
          </div>
        </div>

        <!-- Top Bar Sign Out -->
        <a href="../api/logout.php?system=IT%20Helpdesk&redirect=../IT%20Helpdesk/login.php" class="top-signout-btn" title="Sign Out of IT Helpdesk" onclick="(function(){sessionStorage.clear();localStorage.clear();})()"><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
      </div>
    </header>

    <div class="main-layout">
      <aside class="sidebar">
        <div>
          <div class="sidebar-section-title">IT Support Operations</div>
          <nav class="sidebar-nav">
            <a href="Dashboard.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                  </svg></span>
                <span>Dashboard</span>
              </div>
            </a>
            <a href="TicketQueue.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                  </svg></span>
                <span>Ticket Queue</span>
              </div>
              <span class="sidebar-badge badge-orange"><?= $openCount ?></span>
            </a>
            <a href="MyTickets.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                  </svg></span>
                <span>My Tickets</span>
              </div>
              <span class="sidebar-badge badge-red"><?= $myTicketsCount ?></span>
            </a>
            <a href="KnowledgeBase.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                  </svg></span>
                <span>Knowledge Base</span>
              </div>
              <span class="sidebar-badge"><?= $kbCount ?></span>
            </a>
            <a href="AssetManagement.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="2" width="20" height="8" rx="2" ry="2" />
                    <rect x="2" y="14" width="20" height="8" rx="2" ry="2" />
                    <line x1="6" y1="6" x2="6.01" y2="6" />
                    <line x1="6" y1="18" x2="6.01" y2="18" />
                  </svg></span>
                <span>Asset Management</span>
              </div>
              <span class="sidebar-badge"><?= $totalAssets ?></span>
            </a>
            <a href="SLAReports.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                  </svg></span>
                <span>SLA Reports</span>
              </div>
              <span class="sidebar-badge badge-green">98.4%</span>
            </a>
          </nav>
        </div>

        <div class="sidebar-section-title hd-mt-4">Unified Ecosystem</div>
        <nav class="sidebar-nav hd-mb-2">
          <a href="../VOSTOKPRIBOR Corporate Web Platform/index.php" class="sidebar-nav-item">
            <div class="sidebar-item-left">
              <span class="sidebar-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="2" y1="12" x2="22" y2="12" />
                  <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                </svg>
              </span>
              <span>Corporate Platform</span>
            </div>
            <span class="sidebar-badge hd-text-xs">SYS 01</span>
          </a>
          <a href="../Employee Intranet/index.php" class="sidebar-nav-item">
            <div class="sidebar-item-left">
              <span class="sidebar-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="3" width="7" height="7" />
                  <rect x="14" y="3" width="7" height="7" />
                  <rect x="14" y="14" width="7" height="7" />
                  <rect x="3" y="14" width="7" height="7" />
                </svg>
              </span>
              <span>Employee Intranet</span>
            </div>
            <span class="sidebar-badge hd-text-xs">SYS 04</span>
          </a>
        </nav>

        <div class="sidebar-footer">
          <div class="security-widget-card">
            <div class="security-widget-header">
              <span>Industrial OT Registry</span>
              <span class="security-badge-status">● LIVE</span>
            </div>
            <div class="hd-text-inverse-muted-sm">
              Registered Devices: <strong><?= $totalAssets ?> Edge Nodes</strong>
            </div>
          </div>
        </div>
      </aside>

      <!-- MAIN CONTENT -->
      <main class="content-wrapper">
        <div class="portal-container">
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <span>IT Helpdesk</span>
                <span class="breadcrumb-separator">/</span>
                <span>Infrastructure</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Hardware &amp; Network Nodes</span>
              </div>
              <h1 class="page-title">Industrial IT Asset &amp; Edge Hardware Registry</h1>
              <p class="page-subtitle"><?= $totalAssets ?> managed edge nodes, industrial gateways, optical pyrometers, and biometric interlocks</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.hdApp.showToast('Asset Discovery', 'Triggering ARP scan on industrial OT subnets (10.240.0.0/16)...')">
                <span>🔍 Scan OT Network</span>
              </button>
              <button class="btn btn-primary-amber" onclick="window.hdApp.openCreateAssetModal()">
                <span>+ Register Device</span>
              </button>
            </div>
          </div>

          <div class="hd-card hd-panel-flush">
            <table class="hd-table">
              <thead>
                <tr>
                  <th class="hd-w-140">Asset Tag</th>
                  <th>Device Model &amp; Type</th>
                  <th>Location / Bay</th>
                  <th>IP Address &amp; MAC</th>
                  <th>Firmware</th>
                  <th>Health Status</th>
                  <th class="hd-text-right">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($assets)): ?>
                <tr>
                  <td colspan="7" style="text-align: center; padding: 24px; color: var(--hd-text-muted);">
                    No hardware assets registered in database. Click "+ Register Device" to add one.
                  </td>
                </tr>
                <?php else: ?>
                  <?php foreach ($assets as $a): 
                    $health = $a['health_status'] ?? 'Online';
                    $healthClass = 'status-pill status-in-progress';
                    if (stripos($health, 'Degraded') !== false || stripos($health, 'Fault') !== false) {
                      $healthClass = 'priority-badge priority-critical';
                    } elseif (stripos($health, 'Warning') !== false) {
                      $healthClass = 'priority-badge priority-high';
                    }
                  ?>
                  <tr class="hd-table-row">
                    <td><strong class="hd-mono-navy"><?= htmlspecialchars($a['asset_tag']) ?></strong></td>
                    <td>
                      <div class="hd-font-semibold-navy"><?= htmlspecialchars($a['device_model'] ?: $a['asset_name']) ?></div>
                      <div class="hd-text-muted-11"><?= htmlspecialchars($a['asset_type'] ?? 'Industrial Node') ?></div>
                    </td>
                    <td><?= htmlspecialchars($a['location'] ?? 'Plant Bay') ?></td>
                    <td>
                      <code class="hd-mono-navy-11"><?= htmlspecialchars($a['ip_address'] ?? 'DHCP') ?></code>
                      <?php if (!empty($a['mac_address'])): ?>
                      <div class="hd-text-muted-11" style="font-family: monospace; font-size: 10px;"><?= htmlspecialchars($a['mac_address']) ?></div>
                      <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($a['firmware_version'] ?? 'N/A') ?></td>
                    <td><span class="<?= $healthClass ?>"><?= htmlspecialchars($health) ?></span></td>
                    <td class="hd-text-right">
                      <div style="display: inline-flex; gap: 6px; align-items: center;">
                        <button class="btn btn-outline btn-sm" onclick="window.hdApp.showToast('Telemetry', 'Device <?= htmlspecialchars($a['asset_tag']) ?>: CPU 14%, Ping: 0.8ms, Memory: 32% OK.')">Inspect</button>
                        <button class="btn-crud-edit" onclick="window.hdApp.openEditAssetModal(<?= htmlspecialchars(json_encode($a), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Asset">✎</button>
                        <button class="btn-crud-delete" onclick="window.hdApp.deleteAsset(<?= (int)$a['asset_id'] ?>, '<?= htmlspecialchars($a['asset_tag'], ENT_QUOTES) ?>')" title="Delete Asset">🗑</button>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Register Device Modal -->
  <div id="modal-create-asset" class="hd-modal-overlay">
    <div class="hd-modal-dialog">
      <div class="hd-modal-header">
        <h3 class="hd-modal-title">⚡ Register Industrial IT Asset</h3>
        <button type="button" class="hd-modal-close" onclick="window.hdApp.closeModal('modal-create-asset')">✕</button>
      </div>
      <form id="form-create-asset" onsubmit="window.hdApp.submitCreateAsset(event)">
        <div class="hd-modal-body">
          <div class="hd-form-row">
            <div class="hd-form-group">
              <label class="hd-form-label">Asset Tag *</label>
              <input type="text" name="asset_tag" class="hd-form-input" required placeholder="e.g. VP-GW-LIP-04" />
            </div>
            <div class="hd-form-group">
              <label class="hd-form-label">Device Model *</label>
              <input type="text" name="device_model" class="hd-form-input" required placeholder="e.g. Moxa MGate MB3170" />
            </div>
          </div>
          <div class="hd-form-row">
            <div class="hd-form-group">
              <label class="hd-form-label">Asset Type *</label>
              <input type="text" name="asset_type" class="hd-form-input" required placeholder="e.g. Modbus Gateway, Edge Server, Biometric RFID" />
            </div>
            <div class="hd-form-group">
              <label class="hd-form-label">Location / Plant Bay *</label>
              <input type="text" name="location" class="hd-form-input" required placeholder="e.g. Lipetsk Furnace #5 Bay" />
            </div>
          </div>
          <div class="hd-form-row">
            <div class="hd-form-group">
              <label class="hd-form-label">IP Address</label>
              <input type="text" name="ip_address" class="hd-form-input" placeholder="e.g. 10.240.48.15" />
            </div>
            <div class="hd-form-group">
              <label class="hd-form-label">MAC Address</label>
              <input type="text" name="mac_address" class="hd-form-input" placeholder="e.g. 00:90:E8:4A:2B:12" />
            </div>
          </div>
          <div class="hd-form-row">
            <div class="hd-form-group">
              <label class="hd-form-label">Firmware Version</label>
              <input type="text" name="firmware_version" class="hd-form-input" placeholder="e.g. v4.2.2-sec" />
            </div>
            <div class="hd-form-group">
              <label class="hd-form-label">Health Status</label>
              <select name="health_status" class="hd-form-select">
                <option value="Online (Active)" selected>Online (Active)</option>
                <option value="Degraded (Packet Loss)">Degraded (Packet Loss)</option>
                <option value="Interlock Fault">Interlock Fault</option>
                <option value="Offline">Offline</option>
              </select>
            </div>
          </div>
          <div class="hd-form-group">
            <label class="hd-form-label">Configuration &amp; Subsystem Notes</label>
            <textarea name="notes" class="hd-form-textarea" placeholder="Enter rack details, port assignments, and calibration history..."></textarea>
          </div>
        </div>
        <div class="hd-modal-footer">
          <button type="button" class="btn btn-outline" onclick="window.hdApp.closeModal('modal-create-asset')">Cancel</button>
          <button type="submit" class="btn btn-primary-amber">Save Asset to DB</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Asset Modal -->
  <div id="modal-edit-asset" class="hd-modal-overlay">
    <div class="hd-modal-dialog">
      <div class="hd-modal-header">
        <h3 class="hd-modal-title">✎ Edit Industrial Hardware Asset</h3>
        <button type="button" class="hd-modal-close" onclick="window.hdApp.closeModal('modal-edit-asset')">✕</button>
      </div>
      <form id="form-edit-asset" onsubmit="window.hdApp.submitEditAsset(event)">
        <input type="hidden" name="asset_id" id="edit-asset-id" />
        <div class="hd-modal-body">
          <div class="hd-form-row">
            <div class="hd-form-group">
              <label class="hd-form-label">Asset Tag *</label>
              <input type="text" name="asset_tag" id="edit-asset-tag" class="hd-form-input" required />
            </div>
            <div class="hd-form-group">
              <label class="hd-form-label">Device Model *</label>
              <input type="text" name="device_model" id="edit-asset-model" class="hd-form-input" required />
            </div>
          </div>
          <div class="hd-form-row">
            <div class="hd-form-group">
              <label class="hd-form-label">Asset Type</label>
              <input type="text" name="asset_type" id="edit-asset-type" class="hd-form-input" />
            </div>
            <div class="hd-form-group">
              <label class="hd-form-label">Location / Plant Bay</label>
              <input type="text" name="location" id="edit-asset-loc" class="hd-form-input" />
            </div>
          </div>
          <div class="hd-form-row">
            <div class="hd-form-group">
              <label class="hd-form-label">IP Address</label>
              <input type="text" name="ip_address" id="edit-asset-ip" class="hd-form-input" />
            </div>
            <div class="hd-form-group">
              <label class="hd-form-label">MAC Address</label>
              <input type="text" name="mac_address" id="edit-asset-mac" class="hd-form-input" />
            </div>
          </div>
          <div class="hd-form-row">
            <div class="hd-form-group">
              <label class="hd-form-label">Firmware Version</label>
              <input type="text" name="firmware_version" id="edit-asset-fw" class="hd-form-input" />
            </div>
            <div class="hd-form-group">
              <label class="hd-form-label">Health Status</label>
              <select name="health_status" id="edit-asset-health" class="hd-form-select">
                <option value="Online (Active)">Online (Active)</option>
                <option value="Degraded (Packet Loss)">Degraded (Packet Loss)</option>
                <option value="Interlock Fault">Interlock Fault</option>
                <option value="Offline">Offline</option>
              </select>
            </div>
          </div>
          <div class="hd-form-group">
            <label class="hd-form-label">Configuration &amp; Subsystem Notes</label>
            <textarea name="notes" id="edit-asset-notes" class="hd-form-textarea"></textarea>
          </div>
        </div>
        <div class="hd-modal-footer">
          <button type="button" class="btn btn-outline" onclick="window.hdApp.closeModal('modal-edit-asset')">Cancel</button>
          <button type="submit" class="btn btn-primary-amber">Save Asset Changes</button>
        </div>
      </form>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
</body>

</html>