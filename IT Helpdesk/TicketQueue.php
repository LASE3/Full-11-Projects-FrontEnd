<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('IT');
require_once __DIR__ . '/api/db_helper.php';

$pdo = getItDb();
$currUser = getItCurrentUser();

// Query all tickets with assigned employee details
$stmt = $pdo->query("
    SELECT 
        t.*,
        COALESCE(e.full_name, t.assigned_emp_id, 'Unassigned') AS assigned_tech_name,
        e.job_title AS assigned_tech_role
    FROM tickets t
    LEFT JOIN employees e ON t.assigned_emp_id = e.emp_id
    ORDER BY 
        CASE t.priority 
            WHEN 'Critical' THEN 1 
            WHEN 'High' THEN 2 
            WHEN 'Medium' THEN 3 
            WHEN 'Low' THEN 4 
            ELSE 5 
        END ASC,
        t.created_at DESC
");
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Compute quick stats
$critCount = 0;
$highCount = 0;
$medCount = 0;
$lowCount = 0;
$unassignedCount = 0;
$openCount = 0;
foreach ($tickets as $t) {
  if ($t['status'] !== 'Resolved') {
    $openCount++;
    match ($t['priority']) {
      'Critical' => $critCount++,
      'High' => $highCount++,
      'Medium' => $medCount++,
      'Low' => $lowCount++,
      default => null
    };
  }
  if (empty($t['assigned_emp_id'])) {
    $unassignedCount++;
  }
}

// Dynamic Sidebar Counts
$sbStats = getItSidebarStats($pdo);
$myTicketsCount = $sbStats['my_tickets_count'];
$kbCount = $sbStats['kb_count'];
$assetCount = $sbStats['asset_count'];
$slaPct = $sbStats['sla_pct'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR IT Helpdesk · Incident &amp; Ticket Queue</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>

<body>

  <div class="app-container">
    <!-- TOP NAVIGATION BAR -->
    <header class="top-nav">
      <div class="top-nav__accent-stripe"></div>

      <div class="top-nav__content">
        <!-- Brand & System Identifier -->
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

        <!-- Global Omni Search -->
        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search ticket ID, requester, SCADA node, knowledge base..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <!-- Right System Metrics & Profile -->
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
      <!-- LEFT SIDEBAR NAVIGATION -->
      <aside class="sidebar">
        <div>
          <div class="sidebar-section-title">IT Support Operations</div>
          <nav class="sidebar-nav">
            <a href="Dashboard.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                  </svg>
                </span>
                <span>Dashboard</span>
              </div>
            </a>

            <a href="TicketQueue.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                  </svg>
                </span>
                <span>Ticket Queue</span>
              </div>
              <span class="sidebar-badge badge-orange" id="sidebar-queue-count"><?= $openCount ?></span>
            </a>

            <a href="MyTickets.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                  </svg>
                </span>
                <span>My Tickets</span>
              </div>
              <span class="sidebar-badge badge-red"><?= $myTicketsCount ?></span>
            </a>

            <a href="KnowledgeBase.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                  </svg>
                </span>
                <span>Knowledge Base</span>
              </div>
              <span class="sidebar-badge"><?= $kbCount ?></span>
            </a>

            <a href="AssetManagement.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="2" width="20" height="8" rx="2" ry="2" />
                    <rect x="2" y="14" width="20" height="8" rx="2" ry="2" />
                    <line x1="6" y1="6" x2="6.01" y2="6" />
                    <line x1="6" y1="18" x2="6.01" y2="18" />
                  </svg>
                </span>
                <span>Asset Management</span>
              </div>
              <span class="sidebar-badge"><?= $assetCount ?></span>
            </a>

            <a href="SLAReports.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                  </svg>
                </span>
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
              Active Escalations: <strong><?= $critCount ?> P1 Incidents</strong>
            </div>
          </div>
        </div>
      </aside>

      <!-- MAIN CONTENT AREA -->
      <main class="content-wrapper">
        <div class="portal-container">
          <!-- Page Header -->
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <span>IT Helpdesk</span>
                <span class="breadcrumb-separator">/</span>
                <span>Incident Queue</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Operational Triage Matrix</span>
              </div>
              <h1 class="page-title">Enterprise Support &amp; Incident Triage Queue</h1>
              <p class="page-subtitle">Real-time ticketing queue with multi-field classification filters, heat-scale priorities, and bulk dispatch controls</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" id="btn-sync-queue">
                <span>🔄 Sync Queue</span>
              </button>
              <button class="btn btn-primary-amber" id="btn-open-create-ticket">
                <span>+ Create Ticket</span>
              </button>
            </div>
          </div>

          <!-- Queue Quick Stats Bar (Dynamic from Database) -->
          <div class="hd-grid-4col-mb">
            <div class="hd-card hd-metric-card-crit">
              <div>
                <div class="hd-caption-muted-11">Critical P1</div>
                <div class="hd-mono-stat-crit" id="stat-crit-count"><?= $critCount ?> Open</div>
              </div>
              <span class="priority-badge priority-critical">SLA &lt; 2h</span>
            </div>
            <div class="hd-card hd-metric-card-orange">
              <div>
                <div class="hd-caption-muted-11">High P2</div>
                <div class="hd-mono-stat-orange" id="stat-high-count"><?= $highCount ?> Open</div>
              </div>
              <span class="priority-badge priority-high">SLA &lt; 4h</span>
            </div>
            <div class="hd-card hd-metric-card-amber">
              <div>
                <div class="hd-caption-muted-11">Medium P3</div>
                <div class="hd-mono-stat-amber" id="stat-med-count"><?= $medCount ?> Open</div>
              </div>
              <span class="priority-badge priority-medium">SLA &lt; 8h</span>
            </div>
            <div class="hd-card hd-metric-card-low">
              <div>
                <div class="hd-caption-muted-11">Low P4</div>
                <div class="hd-mono-stat-secondary" id="stat-low-count"><?= $lowCount ?> Open</div>
              </div>
              <span class="priority-badge priority-low">SLA &lt; 24h</span>
            </div>
          </div>

          <!-- Main Table Card with Filter Bar -->
          <div class="hd-card hd-panel-flush">
            <!-- Filter Bar -->
            <div class="hd-toolbar-card">
              <div class="hd-toolbar-controls">
                <!-- Search Input in Filter Bar -->
                <div class="hd-search-box-wrap">
                  <span class="hd-search-box-icon">🔍</span>
                  <input class="hd-search-box-input" type="text" id="ticket-search" placeholder="Search ID, requester, title, system..." />
                </div>

                <!-- Priority Dropdown -->
                <div class="hd-flex-gap-xs">
                  <label class="hd-meta-semibold-115" for="filter-priority">Priority:</label>
                  <select class="hd-btn-filter-select" id="filter-priority">
                    <option value="all">All Priorities</option>
                    <option value="Critical">Critical (P1)</option>
                    <option value="High">High (P2)</option>
                    <option value="Medium">Medium (P3)</option>
                    <option value="Low">Low (P4)</option>
                  </select>
                </div>

                <!-- System Affected Dropdown -->
                <div class="hd-flex-gap-xs">
                  <label class="hd-meta-semibold-115" for="filter-system">System:</label>
                  <select class="hd-btn-filter-select" id="filter-system">
                    <option value="all">All Systems</option>
                    <option value="SCADA">SCADA &amp; Gateway Nodes</option>
                    <option value="Cleanroom">Cleanroom Access Systems</option>
                    <option value="Calibration">Calibration &amp; FAT Testing</option>
                    <option value="PKI">PKI &amp; Security Tokens</option>
                    <option value="ERP">ERP Procurement</option>
                  </select>
                </div>

                <!-- Status Dropdown -->
                <div class="hd-flex-gap-xs">
                  <label class="hd-meta-semibold-115" for="filter-status">Status:</label>
                  <select class="hd-btn-filter-select" id="filter-status">
                    <option value="all">All Statuses</option>
                    <option value="Open">Open</option>
                    <option value="InProgress">In Progress</option>
                    <option value="Escalated">Escalated</option>
                    <option value="Resolved">Resolved</option>
                  </select>
                </div>

                <!-- Assigned Tech Dropdown -->
                <div class="hd-flex-gap-xs">
                  <label class="hd-meta-semibold-115" for="filter-tech">Assigned Tech:</label>
                  <select class="hd-btn-filter-select" id="filter-tech">
                    <option value="all">All Technicians</option>
                    <option value="Alexey Ivanov">Alexey Ivanov (Tier 3)</option>
                    <option value="Dmitry Popov">Dmitry Popov (Tier 2)</option>
                    <option value="Sofia Volkova">Sofia Volkova (Tier 1)</option>
                    <option value="Unassigned">Unassigned</option>
                  </select>
                </div>
              </div>

              <!-- Bulk Assign Button & Queue Count -->
              <div class="hd-flex-gap-md">
                <span class="hd-mono-muted-115">Total <strong><?= count($tickets) ?></strong> in Database</span>
                <button class="btn btn-orange btn-sm" id="btn-bulk-assign" title="Assign pending unassigned tickets to active shift engineers">
                  <span>⚡ Bulk Assign</span>
                </button>
              </div>
            </div>

            <!-- Data Table (100% Dynamic from Database with CRUD) -->
            <div class="overflow-x-auto">
              <table class="hd-table">
                <thead>
                  <tr>
                    <th class="hd-w-120">Ticket ID</th>
                    <th class="hd-min-w-200">Requester</th>
                    <th class="hd-min-w-260">System Affected / Title</th>
                    <th class="hd-w-120">Priority</th>
                    <th class="hd-min-w-180">Assigned Tech</th>
                    <th class="hd-w-120">Status</th>
                    <th style="width: 140px; text-align: right;">Action</th>
                  </tr>
                </thead>
                <tbody class="ticket-table-body" id="tickets-table-body">
                  <?php if (empty($tickets)): ?>
                    <tr>
                      <td colspan="7" style="text-align: center; padding: 30px; color: var(--hd-text-muted);">No incidents recorded in database.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($tickets as $t):
                      $prioClass = match ($t['priority']) {
                        'Critical' => 'priority-critical',
                        'High' => 'priority-high',
                        'Medium' => 'priority-medium',
                        'Low' => 'priority-low',
                        default => 'priority-medium'
                      };
                      $statusClass = match ($t['status']) {
                        'Open' => 'status-open',
                        'InProgress' => 'status-in-progress',
                        'Escalated' => 'priority-critical',
                        'Resolved' => 'badge-green',
                        default => 'status-open'
                      };
                      $tJson = htmlspecialchars(json_encode($t), ENT_QUOTES, 'UTF-8');
                    ?>
                      <tr class="hd-table-row" id="tkt-row-<?= htmlspecialchars($t['tkt_id']) ?>"
                        data-id="<?= htmlspecialchars($t['tkt_id']) ?>"
                        data-requester="<?= htmlspecialchars($t['requester_name'] ?? 'Authorized Staff') ?>"
                        data-system="<?= htmlspecialchars($t['source_system']) ?>"
                        data-priority="<?= htmlspecialchars($t['priority']) ?>"
                        data-status="<?= htmlspecialchars($t['status']) ?>"
                        data-tech="<?= htmlspecialchars($t['assigned_tech_name']) ?>">
                        <td>
                          <span class="hd-mono-bold-navy-125"><?= htmlspecialchars($t['tkt_id']) ?></span>
                          <div class="hd-mono-muted-xs"><?= date('H:i MSK', strtotime($t['created_at'])) ?></div>
                        </td>
                        <td>
                          <div class="hd-flex-gap-65">
                            <div class="hd-stat-column-box" style="width: 32px; height: 32px; border-radius: 50%; background: #0f2438; color: #fff; font-size: 11px; font-weight: bold;">
                              <?= strtoupper(substr($t['requester_name'] ?: 'EP', 0, 2)) ?>
                            </div>
                            <div>
                              <div class="hd-font-semibold-navy"><?= htmlspecialchars($t['requester_name'] ?: 'Authorized Staff') ?></div>
                              <div class="hd-text-secondary-11"><?= htmlspecialchars($t['requester_role'] ?: 'Operations') ?></div>
                            </div>
                          </div>
                        </td>
                        <td>
                          <div class="hd-font-semibold-primary"><?= htmlspecialchars($t['title'] ?: $t['source_system']) ?></div>
                          <div class="hd-text-muted-11"><?= htmlspecialchars($t['source_system']) ?> &bull; <?= htmlspecialchars(substr($t['description'] ?: 'No details provided.', 0, 75)) ?>...</div>
                        </td>
                        <td>
                          <span class="priority-badge <?= $prioClass ?>"><?= strtoupper(htmlspecialchars($t['priority'])) ?></span>
                        </td>
                        <td>
                          <div class="hd-font-semibold-navy-12"><?= htmlspecialchars($t['assigned_tech_name']) ?></div>
                          <div class="hd-mono-muted-xs"><?= htmlspecialchars($t['assigned_tech_role'] ?: 'Support') ?></div>
                        </td>
                        <td>
                          <span class="status-pill <?= $statusClass ?>"><?= htmlspecialchars($t['status']) ?></span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                          <a href="TicketDetail.php?id=<?= urlencode($t['tkt_id']) ?>" class="btn btn-outline btn-sm" style="padding: 3px 8px;">Triage &rarr;</a>
                          <button class="btn-crud-action btn-crud-edit btn-edit-ticket" data-ticket='<?= $tJson ?>' title="Edit Ticket">
                            <span class="material-symbols-outlined">edit</span>
                          </button>
                          <button class="btn-crud-action btn-crud-delete btn-delete-ticket" data-id="<?= htmlspecialchars($t['tkt_id']) ?>" title="Delete Ticket">
                            <span class="material-symbols-outlined">delete</span>
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

            <!-- Table Pagination / Footer -->
            <div class="hd-footer-pagination">
              <div class="hd-mono">
                Total <strong><?= count($tickets) ?> Incidents</strong> Active in Database
              </div>
              <div class="hd-gap-sm">
                <button class="btn btn-outline btn-sm hd-pill-pad-sm" onclick="window.hdApp.showToast('Queue Sync', 'All tickets up to date.')">Refresh View</button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- CREATE / EDIT TICKET MODAL -->
  <div class="hd-modal-overlay" id="ticketModal">
    <div class="hd-modal-dialog">
      <div class="hd-modal-header">
        <h3 id="ticketModalTitle">
          <span class="material-symbols-outlined">confirmation_number</span>
          Create New Support Incident
        </h3>
        <button class="hd-modal-close" id="btnCloseTicketModal">&times;</button>
      </div>
      <div class="hd-modal-body">
        <input type="hidden" id="modalTktAction" value="create">
        <input type="hidden" id="modalTktId" value="">

        <div class="hd-form-group">
          <label for="modalTktTitle">Incident Title *</label>
          <input class="hd-form-input" id="modalTktTitle" type="text" placeholder="e.g. SCADA Gateway Modbus Telemetry Packet Drop" required>
        </div>

        <div class="hd-form-grid-2">
          <div class="hd-form-group">
            <label for="modalTktSystem">System Affected *</label>
            <input class="hd-form-input" id="modalTktSystem" type="text" placeholder="e.g. SCADA Modbus Gateway #3" required>
          </div>
          <div class="hd-form-group">
            <label for="modalTktPriority">Priority Level</label>
            <select class="hd-form-select" id="modalTktPriority">
              <option value="Critical">Critical (P1 &lt; 2h SLA)</option>
              <option value="High">High (P2 &lt; 4h SLA)</option>
              <option value="Medium" selected>Medium (P3 &lt; 8h SLA)</option>
              <option value="Low">Low (P4 &lt; 24h SLA)</option>
            </select>
          </div>
        </div>

        <div class="hd-form-grid-2">
          <div class="hd-form-group">
            <label for="modalTktStatus">Incident Status</label>
            <select class="hd-form-select" id="modalTktStatus">
              <option value="Open">Open</option>
              <option value="InProgress">In Progress</option>
              <option value="Escalated">Escalated</option>
              <option value="Resolved">Resolved</option>
            </select>
          </div>
          <div class="hd-form-group">
            <label for="modalTktTech">Assign Technician</label>
            <select class="hd-form-select" id="modalTktTech">
              <option value="EMP-1018">Alexey Ivanov (Lead Tier 3)</option>
              <option value="EMP-1004">Dmitry Popov (Tier 2)</option>
              <option value="EMP-1002">Sofia Volkova (Tier 1)</option>
              <option value="">Unassigned</option>
            </select>
          </div>
        </div>

        <div class="hd-form-grid-2">
          <div class="hd-form-group">
            <label for="modalTktReqName">Requester Full Name</label>
            <input class="hd-form-input" id="modalTktReqName" type="text" placeholder="e.g. Dr. Elena Rostova" value="Authorized Personnel">
          </div>
          <div class="hd-form-group">
            <label for="modalTktReqRole">Requester Department / Role</label>
            <input class="hd-form-input" id="modalTktReqRole" type="text" placeholder="e.g. Chief Optical Calibration Architect" value="Operations Staff">
          </div>
        </div>

        <div class="hd-form-group">
          <label for="modalTktDesc">Problem Description &amp; Symptoms</label>
          <textarea class="hd-form-textarea" id="modalTktDesc" rows="3" placeholder="Describe telemetry symptoms, error codes, hardware registers, or impacted processes..."></textarea>
        </div>
      </div>
      <div class="hd-modal-footer">
        <button class="btn btn-outline" id="btnCancelTicketModal">Cancel</button>
        <button class="btn btn-primary-amber" id="btnSaveTicket">
          <span>Save Incident</span>
        </button>
      </div>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
  <script src="../assets/js/notifications-hub.js" defer></script>
</body>

</html>