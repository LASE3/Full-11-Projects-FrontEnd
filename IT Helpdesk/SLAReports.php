<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('IT');
require_once __DIR__ . '/api/db_helper.php';
$pdo = getItDb();
$currUser = $_SESSION['vostok_user'] ?? ['full_name' => 'Alexey Ivanov', 'clearance_level' => 'L2'];

// Dynamic calculations
$totalStmt = $pdo->query("SELECT COUNT(*) FROM tickets");
$totalTickets = (int)$totalStmt->fetchColumn();

$critStmt = $pdo->query("SELECT COUNT(*) FROM tickets WHERE priority = 'Critical'");
$critCount = (int)$critStmt->fetchColumn();

$resolvedStmt = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'Resolved'");
$resolvedCount = (int)$resolvedStmt->fetchColumn();

$withinSlaStmt = $pdo->query("SELECT COUNT(*) FROM tickets WHERE sla_status = 'Within SLA' OR sla_status IS NULL");
$withinSlaCount = (int)$withinSlaStmt->fetchColumn();
$slaPct = $totalTickets > 0 ? round(($withinSlaCount / $totalTickets) * 100, 1) : 98.4;

// SLA Policies
$slaStmt = $pdo->query("SELECT * FROM sla_policies ORDER BY FIELD(priority_level, 'Critical', 'High', 'Medium', 'Low')");
$policies = $slaStmt->fetchAll(PDO::FETCH_ASSOC);

// Global Badges
$openCountStmt = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status != 'Resolved'");
$openCount = (int)$openCountStmt->fetchColumn();

$myTicketsStmt = $pdo->query("SELECT COUNT(*) FROM tickets WHERE (assigned_to LIKE '%Alexey%' OR assigned_emp_id = 'EMP-1018') AND status != 'Resolved'");
$myTicketsCount = (int)$myTicketsStmt->fetchColumn();

$assetCountStmt = $pdo->query("SELECT COUNT(*) FROM it_assets");
$assetCount = (int)$assetCountStmt->fetchColumn();

$kbCountStmt = $pdo->query("SELECT COUNT(*) FROM knowledge_base_articles");
$kbCount = (int)$kbCountStmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR IT Helpdesk · SLA Compliance &amp; MTTR Reports</title>
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
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search SLA audit, MTTR trends, resolution metrics..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <div class="top-nav__actions">
          <div class="pipeline-sync-badge hd-badge-telemetry">
            <span class="hd-status-success">●</span>
            <span>SLA: <strong><?= $slaPct ?>% Compliant</strong></span>
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
            <a href="AssetManagement.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="2" width="20" height="8" rx="2" ry="2" />
                    <rect x="2" y="14" width="20" height="8" rx="2" ry="2" />
                    <line x1="6" y1="6" x2="6.01" y2="6" />
                    <line x1="6" y1="18" x2="6.01" y2="18" />
                  </svg></span>
                <span>Asset Management</span>
              </div>
              <span class="sidebar-badge"><?= $assetCount ?></span>
            </a>
            <a href="SLAReports.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                  </svg></span>
                <span>SLA Reports</span>
              </div>
              <span class="sidebar-badge badge-green"><?= $slaPct ?>%</span>
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
              <span>SLA Target Engine</span>
              <span class="security-badge-status">● LIVE</span>
            </div>
            <div class="hd-text-inverse-muted-sm">
              SLA Standard: <strong>GOST R 9001:2015</strong>
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
                <span>Governance &amp; Auditing</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">SLA Performance Metrics</span>
              </div>
              <h1 class="page-title">Operational Service Level Agreements &amp; MTTR Analytics</h1>
              <p class="page-subtitle">Historical resolution pacing, tier performance, and plant facility uptime compliance</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.hdApp.showToast('Audit Export', 'Exporting SLA report (PDF format)...')">
                <span>📑 Export PDF Report</span>
              </button>
            </div>
          </div>

          <!-- KPI Summary (Dynamic from DB) -->
          <div class="kpi-grid">
            <div class="hd-card kpi-card">
              <div class="kpi-header">
                <span>Total Live Incidents</span>
                <div class="kpi-icon-pill orange">📊</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value"><?= $totalTickets ?></span>
              </div>
              <div class="kpi-footer">
                <span>Database Tracked</span>
                <span class="kpi-trend up">▲ <?= $slaPct ?>% On Time</span>
              </div>
            </div>

            <div class="hd-card kpi-card">
              <div class="kpi-header">
                <span>P1 Mean Time to Resolve</span>
                <div class="kpi-icon-pill red">⏱️</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value hd-text-critical">42 min</span>
              </div>
              <div class="kpi-footer">
                <span>Target &lt; 120 min</span>
                <span class="kpi-trend up">100% Met Target</span>
              </div>
            </div>

            <div class="hd-card kpi-card">
              <div class="kpi-header">
                <span>SLA Compliance Rate</span>
                <div class="kpi-icon-pill green">✓</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value hd-text-success"><?= $slaPct ?>%</span>
              </div>
              <div class="kpi-footer">
                <span><?= $withinSlaCount ?> Met SLA Baseline</span>
                <span class="kpi-trend up">Optimal</span>
              </div>
            </div>

            <div class="hd-card kpi-card">
              <div class="kpi-header">
                <span>Plant Uptime Impact</span>
                <div class="kpi-icon-pill steel">🏭</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value">99.98%</span>
              </div>
              <div class="kpi-footer">
                <span>Zero Production Halts</span>
                <span class="kpi-trend up">Optimal</span>
              </div>
            </div>
          </div>

          <!-- SLA Policies Configuration Table (Dynamic from DB) -->
          <div class="hd-card hd-panel-flush" style="margin-top: 24px;">
            <div class="hd-table-header-flex">
              <div>
                <div class="hd-title-13-navy">Configured Incident SLA Policies &amp; Escalation Thresholds</div>
                <div class="hd-text-muted-11" style="margin-top: 2px;">Target horizons governed by VOSTOKPRIBOR Plant Operational SOP-04</div>
              </div>
            </div>

            <table class="hd-table">
              <thead>
                <tr>
                  <th class="hd-w-120">Priority Tier</th>
                  <th>First Response Target</th>
                  <th>Resolution Target</th>
                  <th>Escalation Threshold</th>
                  <th>Policy Description &amp; Scope</th>
                  <th class="hd-text-right">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($policies)): ?>
                <tr>
                  <td colspan="6" style="text-align: center; padding: 24px; color: var(--hd-text-muted);">
                    No SLA policies configured in database.
                  </td>
                </tr>
                <?php else: ?>
                  <?php foreach ($policies as $p): 
                    $prioClass = 'priority-critical';
                    if ($p['priority_level'] === 'High') $prioClass = 'priority-high';
                    if ($p['priority_level'] === 'Medium') $prioClass = 'priority-medium';
                    if ($p['priority_level'] === 'Low') $prioClass = 'priority-low';
                  ?>
                  <tr class="hd-table-row">
                    <td><span class="priority-badge <?= $prioClass ?>"><?= strtoupper(htmlspecialchars($p['priority_level'])) ?></span></td>
                    <td><strong><?= (int)$p['first_response_time_minutes'] ?> mins</strong></td>
                    <td>
                      <strong>
                        <?php 
                          $res = (int)$p['resolution_time_minutes'];
                          echo $res >= 60 ? round($res / 60, 1) . ' hours (' . $res . 'm)' : $res . ' mins';
                        ?>
                      </strong>
                    </td>
                    <td><span class="hd-mono-orange-sm"><?= (int)$p['escalation_threshold_minutes'] ?> mins</span></td>
                    <td><?= htmlspecialchars($p['description'] ?? 'Standard tier SLA policy') ?></td>
                    <td class="hd-text-right">
                      <button class="btn btn-outline btn-sm" onclick="window.hdApp.openEditSlaModal(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)">Edit Policy</button>
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

  <!-- Edit SLA Policy Modal -->
  <div id="modal-edit-sla" class="hd-modal-overlay">
    <div class="hd-modal-dialog">
      <div class="hd-modal-header">
        <h3 class="hd-modal-title">✎ Configure SLA Policy Horizons</h3>
        <button type="button" class="hd-modal-close" onclick="window.hdApp.closeModal('modal-edit-sla')">✕</button>
      </div>
      <form id="form-edit-sla" onsubmit="window.hdApp.submitEditSla(event)">
        <input type="hidden" name="policy_id" id="edit-sla-id" />
        <div class="hd-modal-body">
          <div class="hd-form-group">
            <label class="hd-form-label">Priority Tier</label>
            <input type="text" name="priority_level" id="edit-sla-prio" class="hd-form-input" readonly style="background: rgba(0,0,0,0.2);" />
          </div>
          <div class="hd-form-row">
            <div class="hd-form-group">
              <label class="hd-form-label">First Response Target (Minutes) *</label>
              <input type="number" name="first_response_time_minutes" id="edit-sla-resp" class="hd-form-input" required min="1" />
            </div>
            <div class="hd-form-group">
              <label class="hd-form-label">Resolution Target (Minutes) *</label>
              <input type="number" name="resolution_time_minutes" id="edit-sla-resol" class="hd-form-input" required min="5" />
            </div>
          </div>
          <div class="hd-form-group">
            <label class="hd-form-label">Escalation Threshold (Minutes) *</label>
            <input type="number" name="escalation_threshold_minutes" id="edit-sla-escl" class="hd-form-input" required min="1" />
          </div>
          <div class="hd-form-group">
            <label class="hd-form-label">Policy Description &amp; Scope</label>
            <textarea name="description" id="edit-sla-desc" class="hd-form-textarea"></textarea>
          </div>
        </div>
        <div class="hd-modal-footer">
          <button type="button" class="btn btn-outline" onclick="window.hdApp.closeModal('modal-edit-sla')">Cancel</button>
          <button type="submit" class="btn btn-primary-amber">Save SLA Policy</button>
        </div>
      </form>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
</body>

</html>