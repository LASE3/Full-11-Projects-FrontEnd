<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('IT');
require_once __DIR__ . '/api/db_helper.php';
$pdo = getItDb();
$currUser = getItCurrentUser();

$userEmpId = $currUser['emp_id'] ?? null;
$userFullName = $currUser['full_name'] ?? 'Alexey Ivanov';
$isSuperAdmin = isSuperAdmin($currUser)
    || in_array($currUser['role_name'] ?? '', ['SuperAdmin', 'Super Administrator', 'Executive SuperAdmin', 'System Administrator', 'Admin', 'IT Director'])
    || in_array($currUser['clearance_level'] ?? '', ['L4', 'L5']);

if ($isSuperAdmin) {
    // Super admin can see everything
    $techFilter = $_GET['tech'] ?? 'all';
    if ($techFilter === 'all') {
        $stmt = $pdo->query("SELECT * FROM tickets ORDER BY FIELD(priority, 'Critical', 'High', 'Medium', 'Low'), created_at DESC");
        $myTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->prepare("
            SELECT t.* FROM tickets t
            LEFT JOIN employees e ON t.assigned_emp_id = e.emp_id
            WHERE t.assigned_emp_id = :empid OR e.full_name = :techname OR t.requester_name = :reqname
            ORDER BY FIELD(t.priority, 'Critical', 'High', 'Medium', 'Low'), t.created_at DESC
        ");
        $stmt->execute([':empid' => $techFilter, ':techname' => $techFilter, ':reqname' => $techFilter]);
        $myTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} else {
    // Regular user / technician: query only their tickets
    $techFilter = $userFullName;
    $stmt = $pdo->prepare("
        SELECT t.* FROM tickets t
        LEFT JOIN employees e ON t.assigned_emp_id = e.emp_id
        WHERE t.assigned_emp_id = :empid OR e.full_name = :fullname OR t.requester_name = :reqname
        ORDER BY FIELD(t.priority, 'Critical', 'High', 'Medium', 'Low'), t.created_at DESC
    ");
    $stmt->execute([
        ':empid'    => $userEmpId,
        ':fullname' => $userFullName,
        ':reqname'  => $userFullName
    ]);
    $myTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Counts
$totalCount = count($myTickets);
$critCount = 0;
foreach ($myTickets as $t) {
  if ($t['priority'] === 'Critical') {
    $critCount++;
  }
}

// Global dynamic sidebar counts
$sbStats = getItSidebarStats($pdo);
$openCount = $sbStats['open_count'];
$myTicketsCount = $sbStats['my_tickets_count'];
$assetCount = $sbStats['asset_count'];
$kbCount = $sbStats['kb_count'];
$slaPct = $sbStats['sla_pct'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR IT Helpdesk · My Assigned Tickets</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>

  <div class="app-container">
    <!-- TOP NAVIGATION BAR -->
    <header class="top-nav">
      <div class="top-nav__accent-stripe"></div>
      <div class="top-nav__content">
        <div class="brand-section">
          <button class="mobile-nav-toggle" id="hd-sidebar-toggle" onclick="document.body.classList.toggle('sidebar-open')" title="Toggle Menu"><span class="material-symbols-outlined">menu</span></button>
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
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search ticket ID, requester, SCADA node, knowledge base..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <div class="top-nav__actions">
          <div class="pipeline-sync-badge hd-badge-telemetry">
            <span class="hd-status-success">●</span>
            <span>SLA: <strong>98.4% Compliant</strong></span>
          </div>
          <button class="icon-button notifications-btn" id="notifications-toggle-btn" title="Incident Telemetry Notifications">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
              <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
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
            <a href="MyTickets.php" class="sidebar-nav-item active">
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
          <a href="../Employee Intranet/login.php" class="sidebar-nav-item">
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
              <span>Incident Response Gateway</span>
              <span class="security-badge-status">● LIVE</span>
            </div>
            <div class="hd-text-inverse-muted-sm">
              Active Assigned: <strong><?= $totalCount ?> Incidents</strong>
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
                <span>Engineer Workspace</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Assigned to <?= htmlspecialchars($techFilter === 'all' ? 'All Techs' : $techFilter) ?></span>
              </div>
              <h1 class="page-title">My Active Incident &amp; Task Worklist</h1>
              <p class="page-subtitle">Personal queue of <?= $totalCount ?> assigned incidents with real-time SLA breach horizon tracking</p>
            </div>
            <div class="page-header-actions" style="display: flex; gap: 8px;">
              <select onchange="window.location.href='MyTickets.php?tech=' + encodeURIComponent(this.value)" class="hd-form-select" style="width: auto; background: var(--hd-navy); color: #fff; border-color: rgba(255,255,255,0.2);">
                <option value="Alexey Ivanov" <?= $techFilter === 'Alexey Ivanov' ? 'selected' : '' ?>>Alexey Ivanov (Tier 3)</option>
                <option value="Dmitry Popov" <?= $techFilter === 'Dmitry Popov' ? 'selected' : '' ?>>Dmitry Popov (Tier 2)</option>
                <option value="Sofia Volkova" <?= $techFilter === 'Sofia Volkova' ? 'selected' : '' ?>>Sofia Volkova (Tier 1)</option>
                <option value="all" <?= $techFilter === 'all' ? 'selected' : '' ?>>All Assigned Techs</option>
              </select>
              <button class="btn btn-outline" onclick="window.hdApp.openCreateTicketModal()">
                <span>+ Create Incident</span>
              </button>
              <?php if (!empty($myTickets)): ?>
                <a href="TicketDetail.php?id=<?= urlencode($myTickets[0]['tkt_id']) ?>" class="btn btn-primary-amber">
                  <span>⚡ Resume Top Incident (<?= htmlspecialchars($myTickets[0]['tkt_id']) ?>)</span>
                </a>
              <?php endif; ?>
            </div>
          </div>

          <div class="hd-card hd-panel-flush">
            <div class="hd-table-header-flex">
              <div class="hd-title-13-navy">
                Assigned Incidents (<?= $totalCount ?>) · Live Database Queue
              </div>
              <?php if ($critCount > 0): ?>
                <span class="priority-badge priority-critical"><?= $critCount ?> P1 Critical Requiring Action</span>
              <?php else: ?>
                <span class="status-pill status-resolved">No Active P1 Breaches</span>
              <?php endif; ?>
            </div>

            <table class="hd-table">
              <thead>
                <tr>
                  <th class="hd-w-120">Ticket ID</th>
                  <th>Requester &amp; Facility</th>
                  <th>System &amp; Error Description</th>
                  <th>Priority</th>
                  <th>SLA Horizon</th>
                  <th>Status</th>
                  <th class="hd-text-right">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($myTickets)): ?>
                  <tr>
                    <td colspan="7" style="text-align: center; padding: 24px; color: var(--hd-text-muted);">
                      No tickets for you
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($myTickets as $t):
                    $prioClass = 'priority-critical';
                    if ($t['priority'] === 'High') $prioClass = 'priority-high';
                    if ($t['priority'] === 'Medium') $prioClass = 'priority-medium';
                    if ($t['priority'] === 'Low') $prioClass = 'priority-low';
                    $statusSlug = strtolower(str_replace(' ', '-', $t['status']));
                  ?>
                    <tr class="hd-table-row">
                      <td>
                        <a href="TicketDetail.php?id=<?= urlencode($t['tkt_id']) ?>" style="text-decoration: none;">
                          <strong class="hd-mono-navy"><?= htmlspecialchars($t['tkt_id']) ?></strong>
                        </a>
                      </td>
                      <td>
                        <div class="hd-font-semibold-navy"><?= htmlspecialchars((string)($t['requester_name'] ?? 'Authorized Personnel')) ?></div>
                        <div class="hd-text-muted-11"><?= htmlspecialchars((string)($t['requester_dept'] ?? 'Plant Operations')) ?></div>
                      </td>
                      <td>
                        <div class="hd-font-semibold"><?= htmlspecialchars((string)($t['affected_system'] ?? ($t['source_system'] ?? 'General Subsystem'))) ?></div>
                        <div class="hd-text-muted-11"><?= htmlspecialchars((string)($t['title'] ?: ($t['description'] ?? 'General Support Incident'))) ?></div>
                      </td>
                      <td><span class="priority-badge <?= $prioClass ?>"><?= strtoupper(htmlspecialchars((string)($t['priority'] ?? 'MEDIUM'))) ?></span></td>
                      <td><strong class="<?= $t['priority'] === 'Critical' ? 'hd-mono-critical' : 'hd-mono-navy' ?>"><?= htmlspecialchars((string)($t['sla_deadline'] ?? 'Active')) ?></strong></td>
                      <td><span class="status-pill status-<?= $statusSlug ?>"><?= htmlspecialchars((string)($t['status'] ?? 'Open')) ?></span></td>
                      <td class="hd-text-right">
                        <div style="display: inline-flex; gap: 6px; align-items: center;">
                          <a href="TicketDetail.php?id=<?= urlencode((string)$t['tkt_id']) ?>" class="btn btn-primary-amber btn-sm">Triage →</a>
                          <button class="btn-crud-edit" onclick="window.hdApp.openEditTicketModal(<?= htmlspecialchars(json_encode($t), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Ticket">✎</button>
                          <button class="btn-crud-delete" onclick="window.hdApp.deleteTicket('<?= htmlspecialchars((string)$t['tkt_id'], ENT_QUOTES) ?>', '<?= htmlspecialchars((string)$t['tkt_id'], ENT_QUOTES) ?>')" title="Delete Ticket">🗑</button>
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

  <!-- Create Ticket Modal -->
  <div id="modal-create-ticket" class="hd-modal-overlay">
    <div class="hd-modal-dialog">
      <div class="hd-modal-header">
        <h3 class="hd-modal-title">⚡ Register Operational Incident Ticket</h3>
        <button type="button" class="hd-modal-close" onclick="window.hdApp.closeModal('modal-create-ticket')">✕</button>
      </div>
      <form id="form-create-ticket" onsubmit="window.hdApp.submitCreateTicket(event)">
        <div class="hd-modal-body">
          <div class="hd-form-group">
            <label class="hd-form-label">Incident Title *</label>
            <input type="text" name="title" class="hd-form-input" required placeholder="e.g. Cleanroom Biometric Interlock Sensor Failure" />
          </div>
          <div class="hd-form-row">
            <div class="hd-form-group">
              <label class="hd-form-label">Priority Tier *</label>
              <select name="priority" class="hd-form-select" required>
                <option value="Critical">Critical (P1 - < 2h SLA)</option>
                <option value="High">High (P2 - < 4h SLA)</option>
                <option value="Medium" selected>Medium (P3 - < 8h SLA)</option>
                <option value="Low">Low (P4 - < 24h SLA)</option>
              </select>
            </div>
            <div class="hd-form-group">
              <label class="hd-form-label">Affected Industrial Subsystem *</label>
              <input type="text" name="affected_system" class="hd-form-input" required placeholder="e.g. Nanofabrication Bay B Airlock" />
            </div>
          </div>
          <div class="hd-form-row">
            <div class="hd-form-group">
              <label class="hd-form-label">Requester Name *</label>
              <input type="text" name="requester_name" class="hd-form-input" required value="<?= htmlspecialchars($currUser['full_name']) ?>" />
            </div>
            <div class="hd-form-group">
              <label class="hd-form-label">Requester Department</label>
              <input type="text" name="requester_dept" class="hd-form-input" placeholder="e.g. Cleanroom ISO Bay" />
            </div>
          </div>
          <div class="hd-form-row">
            <div class="hd-form-group">
              <label class="hd-form-label">Assigned Lead Tech</label>
              <select name="assigned_emp_id" class="hd-form-select">
                <option value="Alexey Ivanov" selected>Alexey Ivanov (Tier 3)</option>
                <option value="Dmitry Popov">Dmitry Popov (Tier 2)</option>
                <option value="Sofia Volkova">Sofia Volkova (Tier 1)</option>
                <option value="Unassigned">Unassigned (Pool Queue)</option>
              </select>
            </div>
            <div class="hd-form-group">
              <label class="hd-form-label">Initial Status</label>
              <select name="status" class="hd-form-select">
                <option value="Open">Open</option>
                <option value="In Progress" selected>In Progress</option>
                <option value="Escalated">Escalated</option>
              </select>
            </div>
          </div>
          <div class="hd-form-group">
            <label class="hd-form-label">Detailed Description *</label>
            <textarea name="description" class="hd-form-textarea" required placeholder="Describe error codes, sensor telemetry, and immediate production impact..."></textarea>
          </div>
        </div>
        <div class="hd-modal-footer">
          <button type="button" class="btn btn-outline" onclick="window.hdApp.closeModal('modal-create-ticket')">Cancel</button>
          <button type="submit" class="btn btn-primary-amber">Submit Ticket to DB</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Ticket Modal -->
  <div id="modal-edit-ticket" class="hd-modal-overlay">
    <div class="hd-modal-dialog">
      <div class="hd-modal-header">
        <h3 class="hd-modal-title">✎ Edit Incident Ticket</h3>
        <button type="button" class="hd-modal-close" onclick="window.hdApp.closeModal('modal-edit-ticket')">✕</button>
      </div>
      <form id="form-edit-ticket" onsubmit="window.hdApp.submitEditTicket(event)">
        <input type="hidden" name="tkt_id" id="edit-ticket-id" />
        <div class="hd-modal-body">
          <div class="hd-form-group">
            <label class="hd-form-label">Incident Title *</label>
            <input type="text" name="title" id="edit-ticket-title" class="hd-form-input" required />
          </div>
          <div class="hd-form-row">
            <div class="hd-form-group">
              <label class="hd-form-label">Priority Tier</label>
              <select name="priority" id="edit-ticket-priority" class="hd-form-select">
                <option value="Critical">Critical (P1)</option>
                <option value="High">High (P2)</option>
                <option value="Medium">Medium (P3)</option>
                <option value="Low">Low (P4)</option>
              </select>
            </div>
            <div class="hd-form-group">
              <label class="hd-form-label">Status</label>
              <select name="status" id="edit-ticket-status" class="hd-form-select">
                <option value="Open">Open</option>
                <option value="In Progress">In Progress</option>
                <option value="Escalated">Escalated</option>
                <option value="Resolved">Resolved</option>
              </select>
            </div>
          </div>
          <div class="hd-form-row">
            <div class="hd-form-group">
              <label class="hd-form-label">Affected System</label>
              <input type="text" name="affected_system" id="edit-ticket-system" class="hd-form-input" />
            </div>
            <div class="hd-form-group">
              <label class="hd-form-label">Assigned Tech</label>
              <input type="text" name="assigned_emp_id" id="edit-ticket-assigned" class="hd-form-input" />
            </div>
          </div>
          <div class="hd-form-group">
            <label class="hd-form-label">Description</label>
            <textarea name="description" id="edit-ticket-desc" class="hd-form-textarea"></textarea>
          </div>
          <div class="hd-form-group">
            <label class="hd-form-label">Resolution / Remediation Notes</label>
            <textarea name="resolution_notes" id="edit-ticket-notes" class="hd-form-textarea"></textarea>
          </div>
        </div>
        <div class="hd-modal-footer">
          <button type="button" class="btn btn-outline" onclick="window.hdApp.closeModal('modal-edit-ticket')">Cancel</button>
          <button type="submit" class="btn btn-primary-amber">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
  <script src="../assets/js/notifications-hub.js" defer></script>
</body>

</html>