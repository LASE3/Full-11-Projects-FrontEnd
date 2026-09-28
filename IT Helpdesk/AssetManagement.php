<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('IT');
require_once __DIR__ . '/api/db_helper.php';
$pdo = getItDb();
$currUser = getItCurrentUser();

// Query all assets
$stmt = $pdo->query("SELECT * FROM it_assets ORDER BY last_seen_at DESC");
$assets = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalAssets = count($assets);

// Dynamic Sidebar Counts
$sbStats = getItSidebarStats($pdo);
$openCount = $sbStats['open_count'];
$myTicketsCount = $sbStats['my_tickets_count'];
$kbCount = $sbStats['kb_count'];
$slaPct = $sbStats['sla_pct'];
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
          <div class="top-user-profile" onclick="window.hdApp.showToast('Active Tech Session', '<?= addslashes(htmlspecialchars($currUser['full_name'])) ?> · <?= addslashes(htmlspecialchars($currUser['role_display'])) ?>')">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" alt="<?= htmlspecialchars($currUser['full_name']) ?>" class="user-avatar-top" />
            <div class="user-details-top">
              <span class="user-name-top"><?= htmlspecialchars($currUser['full_name']) ?></span>
              <span class="user-role-top"><?= htmlspecialchars($currUser['role_display']) ?></span>
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
              <span class="sidebar-badge badge-green"><?= $slaPct ?>%</span>
            </a>

            <a href="Integrations.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00E5FF" stroke-width="2">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                  </svg>
                </span>
                <span class="hd-nav-integrations">System Integrations</span>
              </div>
              <span class="sidebar-badge hd-badge-integrations">SYS09</span>
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
                      <td><strong class="hd-mono-navy"><?= htmlspecialchars((string)($a['asset_tag'] ?? ('VP-NODE-' . $a['asset_id']))) ?></strong></td>
                      <td>
                        <div class="hd-font-semibold-navy"><?= htmlspecialchars((string)($a['device_model'] ?: ($a['asset_type'] ?? 'Unknown Device'))) ?></div>
                        <div class="hd-text-muted-11"><?= htmlspecialchars((string)($a['asset_type'] ?? 'Industrial Node')) ?></div>
                      </td>
                      <td><?= htmlspecialchars((string)($a['location'] ?? 'Plant Bay')) ?></td>
                      <td>
                        <code class="hd-mono-navy-11"><?= htmlspecialchars((string)($a['ip_address'] ?? 'DHCP')) ?></code>
                        <?php if (!empty($a['mac_address'])): ?>
                          <div class="hd-text-muted-11" style="font-family: monospace; font-size: 10px;"><?= htmlspecialchars((string)$a['mac_address']) ?></div>
                        <?php endif; ?>
                      </td>
                      <td><?= htmlspecialchars((string)($a['firmware_version'] ?? 'N/A')) ?></td>
                      <td><span class="<?= $healthClass ?>"><?= htmlspecialchars((string)$health) ?></span></td>
                      <td class="hd-text-right">
                        <div style="display: inline-flex; gap: 6px; align-items: center;">
                          <button class="btn btn-outline btn-sm" onclick="window.hdApp.openInspectModal(<?= htmlspecialchars(json_encode($a), ENT_QUOTES, 'UTF-8') ?>)">Inspect</button>
                          <button class="btn-crud-edit" onclick="window.hdApp.openEditAssetModal(<?= htmlspecialchars(json_encode($a), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Asset">✎</button>
                          <button class="btn-crud-delete" onclick="window.hdApp.deleteAsset(<?= (int)$a['asset_id'] ?>, '<?= htmlspecialchars((string)($a['asset_tag'] ?? ('Node #' . $a['asset_id'])), ENT_QUOTES) ?>')" title="Delete Asset">🗑</button>
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
        <input type="hidden" name="action" value="create" />
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
                <option value="Nominal">Nominal</option>
                <option value="Calibrated (Nominal)">Calibrated (Nominal)</option>
                <option value="Degraded (14% Loss)">Degraded (14% Loss)</option>
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
        <input type="hidden" name="action" value="update" />
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
                <option value="Nominal">Nominal</option>
                <option value="Calibrated (Nominal)">Calibrated (Nominal)</option>
                <option value="Degraded (14% Loss)">Degraded (14% Loss)</option>
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


  <!-- Inspect Asset Modal -->
  <div id="modal-inspect-asset" class="hd-modal-overlay">
    <div class="hd-modal-dialog" style="max-width:680px;">
      <div class="hd-modal-header">
        <h3 class="hd-modal-title">🔍 Asset Inspection Report</h3>
        <button type="button" class="hd-modal-close" onclick="window.hdApp.closeModal('modal-inspect-asset')">✕</button>
      </div>
      <div class="hd-modal-body" id="inspect-modal-body" style="padding:20px;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
          <div>
            <div style="font-size:11px;color:var(--hd-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Asset Tag</div>
            <div id="ins-tag" style="font-family:monospace;font-weight:700;font-size:15px;color:var(--hd-navy);"></div>
          </div>
          <div>
            <div style="font-size:11px;color:var(--hd-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Device Model</div>
            <div id="ins-model" style="font-weight:600;"></div>
          </div>
          <div>
            <div style="font-size:11px;color:var(--hd-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Asset Type</div>
            <div id="ins-type"></div>
          </div>
          <div>
            <div style="font-size:11px;color:var(--hd-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Location / Plant Bay</div>
            <div id="ins-loc"></div>
          </div>
          <div>
            <div style="font-size:11px;color:var(--hd-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">IP Address</div>
            <div id="ins-ip" style="font-family:monospace;"></div>
          </div>
          <div>
            <div style="font-size:11px;color:var(--hd-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">MAC Address</div>
            <div id="ins-mac" style="font-family:monospace;font-size:12px;"></div>
          </div>
          <div>
            <div style="font-size:11px;color:var(--hd-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Firmware Version</div>
            <div id="ins-fw" style="font-family:monospace;"></div>
          </div>
          <div>
            <div style="font-size:11px;color:var(--hd-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">OS / Version</div>
            <div id="ins-os"></div>
          </div>
          <div>
            <div style="font-size:11px;color:var(--hd-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Serial Number</div>
            <div id="ins-serial" style="font-family:monospace;font-size:12px;"></div>
          </div>
          <div>
            <div style="font-size:11px;color:var(--hd-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Hostname</div>
            <div id="ins-host" style="font-family:monospace;"></div>
          </div>
          <div>
            <div style="font-size:11px;color:var(--hd-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Criticality</div>
            <div id="ins-crit"></div>
          </div>
          <div>
            <div style="font-size:11px;color:var(--hd-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Environment</div>
            <div id="ins-env"></div>
          </div>
          <div>
            <div style="font-size:11px;color:var(--hd-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Health Status</div>
            <div id="ins-health"></div>
          </div>
          <div>
            <div style="font-size:11px;color:var(--hd-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Status</div>
            <div id="ins-status"></div>
          </div>
          <div>
            <div style="font-size:11px;color:var(--hd-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Assigned Date</div>
            <div id="ins-date"></div>
          </div>
          <div>
            <div style="font-size:11px;color:var(--hd-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Last Seen</div>
            <div id="ins-seen" style="font-family:monospace;font-size:12px;"></div>
          </div>
        </div>
        <div style="margin-top:16px;">
          <div style="font-size:11px;color:var(--hd-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Configuration &amp; Notes</div>
          <div id="ins-notes" style="background:var(--hd-bg-muted,#f5f6fa);border-radius:6px;padding:10px 14px;font-size:13px;line-height:1.6;min-height:40px;"></div>
        </div>
      </div>
      <div class="hd-modal-footer">
        <button type="button" class="btn btn-outline" onclick="window.hdApp.closeModal('modal-inspect-asset')">Close</button>
        <button type="button" class="btn btn-primary-amber" onclick="window.hdApp.closeModal('modal-inspect-asset');window.hdApp.openEditAssetModal(window.hdApp._inspectAsset)">Edit Asset</button>
      </div>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
  <script>
    window.hdApp.openInspectModal = function(asset) {
      if (!asset) return;
      window.hdApp._inspectAsset = asset;
      var f = function(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val || '—';
      };
      f('ins-tag', asset.asset_tag || ('VP-NODE-' + asset.asset_id));
      f('ins-model', asset.device_model || asset.asset_type || 'Industrial Edge Node');
      f('ins-type', asset.asset_type || 'Hardware Asset');
      f('ins-loc', asset.location || 'Central Datacenter Bay');
      f('ins-ip', asset.ip_address || 'DHCP');
      f('ins-mac', asset.mac_address || 'N/A');
      f('ins-fw', asset.firmware_version || 'N/A');
      f('ins-os', ((asset.operating_system || '') + (asset.os_version ? ' ' + asset.os_version : '')) || 'Embedded Linux');
      f('ins-serial', asset.serial_number || 'N/A');
      f('ins-host', asset.hostname || 'N/A');
      f('ins-crit', asset.criticality || 'High');
      f('ins-env', asset.environment || 'Production');
      f('ins-health', asset.health_status || 'Online (Active)');
      f('ins-status', asset.status || 'Active');
      f('ins-date', asset.assigned_date || '2026-01-01');
      f('ins-seen', asset.last_seen_at || 'Just now');
      f('ins-notes', asset.notes || 'No specialized subsystem notes recorded.');
      window.hdApp.openModal('modal-inspect-asset');
    };
  </script>
</body>

</html>