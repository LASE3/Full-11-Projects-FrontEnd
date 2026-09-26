<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('IT');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR IT Helpdesk · Incident &amp; Ticket Queue</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>

  <div class="app-container">
    <!-- ========================================================================
         TOP NAVIGATION BAR
         ======================================================================== -->
    <header class="top-nav">
      <div class="top-nav__accent-stripe"></div>

      <div class="top-nav__content">
        <!-- Brand & System Identifier -->
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
                <span class="hd-opacity-50" >|</span>
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
          <div class="pipeline-sync-badge hd-badge-telemetry" >
            <span class="hd-status-success" >●</span>
            <span>SLA: <strong>98.4% Compliant</strong></span>
          </div>

          <button class="icon-button" title="Incident Telemetry Notifications" onclick="window.hdApp.showToast('Critical Alert', 'SCADA Gateway Node #3 packet loss detected in Lipetsk Bay.', 'critical')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
              <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
            <span class="badge-dot"></span>
          </button>

          <div class="top-user-profile" onclick="window.hdApp.showToast('Active Tech Session', 'Alexey Ivanov · Tier 3 IT Operations Engineer')">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" alt="Alexey Ivanov" class="user-avatar-top" />
            <div class="user-details-top">
              <span class="user-name-top">Alexey Ivanov</span>
              <span class="user-role-top">Lead IT Tech · Tier 3</span>
            </div>
          </div>
        </div>

        <!-- Top Bar Sign Out -->
        <a href="../api/logout.php?system=IT%20Helpdesk&redirect=../IT%20Helpdesk/login.php" class="top-signout-btn" title="Sign Out of IT Helpdesk" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" ><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
      </div>
    </header>

    <div class="main-layout">
      <!-- ========================================================================
           LEFT SIDEBAR NAVIGATION (Ticket Queue Active)
           ======================================================================== -->
      <aside class="sidebar">
        <div>
          <div class="sidebar-section-title">IT Support Operations</div>
          <nav class="sidebar-nav">
            <!-- Screen 1: Dashboard -->
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

            <!-- Screen 2: Ticket Queue (Active) -->
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
              <span class="sidebar-badge badge-orange">34</span>
            </a>

            <!-- My Tickets -->
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
              <span class="sidebar-badge badge-red">8</span>
            </a>

            <!-- Knowledge Base -->
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
              <span class="sidebar-badge">142</span>
            </a>

            <!-- Asset Management -->
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
              <span class="sidebar-badge">1,820</span>
            </a>

            <!-- SLA Reports -->
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
              <span class="sidebar-badge badge-green">98.4%</span>
            </a>
          </nav>
        </div>


        <div class="sidebar-section-title hd-mt-4" >Unified Ecosystem</div>
        <nav class="sidebar-nav hd-mb-2" >
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
            <span class="sidebar-badge hd-text-xs" >SYS 01</span>
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
            <span class="sidebar-badge hd-text-xs" >SYS 04</span>
          </a>
        </nav>
        <!-- Log Out -->

        <div class="sidebar-footer">
          <div class="security-widget-card">
            <div class="security-widget-header">
              <span>Incident Response Gateway</span>
              <span class="security-badge-status">● LIVE</span>
            </div>
            <div class="hd-text-inverse-muted-sm" >
              Active Escalations: <strong>3 P1 Incidents</strong>
            </div>
          </div>
        </div>
      </aside>

      <!-- ========================================================================
           MAIN CONTENT AREA: SCREEN 2 TICKET QUEUE TABLE
           ======================================================================== -->
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
              <button class="btn btn-outline" onclick="window.hdApp.showToast('Queue Sync', 'Ticket queue refreshed from central message broker (0 new incidents).')">
                <span>🔄 Sync Queue</span>
              </button>
              <button class="btn btn-primary-amber" onclick="window.hdApp.bulkAssign()">
                <span>⚡ Bulk Assign</span>
              </button>
            </div>
          </div>

          <!-- Queue Quick Stats Bar -->
          <div class="hd-grid-4col-mb" >
            <div class="hd-card hd-metric-card-crit" >
              <div>
                <div class="hd-caption-muted-11" >Critical P1</div>
                <div class="hd-mono-stat-crit" >3 Open</div>
              </div>
              <span class="priority-badge priority-critical">SLA &lt; 2h</span>
            </div>
            <div class="hd-card hd-metric-card-orange" >
              <div>
                <div class="hd-caption-muted-11" >High P2</div>
                <div class="hd-mono-stat-orange" >8 Open</div>
              </div>
              <span class="priority-badge priority-high">SLA &lt; 4h</span>
            </div>
            <div class="hd-card hd-metric-card-amber" >
              <div>
                <div class="hd-caption-muted-11" >Medium P3</div>
                <div class="hd-mono-stat-amber" >15 Open</div>
              </div>
              <span class="priority-badge priority-medium">SLA &lt; 8h</span>
            </div>
            <div class="hd-card hd-metric-card-low" >
              <div>
                <div class="hd-caption-muted-11" >Low P4</div>
                <div class="hd-mono-stat-secondary" >8 Open</div>
              </div>
              <span class="priority-badge priority-low">SLA &lt; 24h</span>
            </div>
          </div>

          <!-- Main Table Card with Filter Bar -->
          <div class="hd-card hd-panel-flush" >
            <!-- Filter Bar -->
            <div class="hd-toolbar-card" >
              <div class="hd-toolbar-controls" >
                <!-- Search Input in Filter Bar -->
                <div class="hd-search-box-wrap" >
                  <span class="hd-search-box-icon" >🔍</span>
                  <input class="hd-search-box-input" type="text" id="ticket-search" oninput="window.hdApp.filterTickets()" placeholder="Search ID, requester, keyword..."  />
                </div>

                <!-- Priority Dropdown -->
                <div class="hd-flex-gap-xs" >
                  <label class="hd-meta-semibold-115" for="filter-priority" >Priority:</label>
                  <select class="hd-btn-filter-select" id="filter-priority" onchange="window.hdApp.filterTickets()" >
                    <option value="all">All Priorities</option>
                    <option value="Critical">Critical (P1)</option>
                    <option value="High">High (P2)</option>
                    <option value="Medium">Medium (P3)</option>
                    <option value="Low">Low (P4)</option>
                  </select>
                </div>

                <!-- System Affected Dropdown -->
                <div class="hd-flex-gap-xs" >
                  <label class="hd-meta-semibold-115" for="filter-system" >System:</label>
                  <select class="hd-btn-filter-select" id="filter-system" onchange="window.hdApp.filterTickets()" >
                    <option value="all">All Systems</option>
                    <option value="SCADA">SCADA &amp; Gateway Nodes</option>
                    <option value="Cleanroom">Cleanroom Access Systems</option>
                    <option value="Calibration">Calibration &amp; FAT Testing</option>
                    <option value="PKI">PKI &amp; Security Tokens</option>
                    <option value="ERP">ERP Procurement &amp; Sign-off</option>
                  </select>
                </div>

                <!-- Status Dropdown -->
                <div class="hd-flex-gap-xs" >
                  <label class="hd-meta-semibold-115" for="filter-status" >Status:</label>
                  <select class="hd-btn-filter-select" id="filter-status" onchange="window.hdApp.filterTickets()" >
                    <option value="all">All Statuses</option>
                    <option value="Open">Open</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Escalated">Escalated</option>
                    <option value="Resolved">Resolved</option>
                  </select>
                </div>

                <!-- Assigned Tech Dropdown -->
                <div class="hd-flex-gap-xs" >
                  <label class="hd-meta-semibold-115" for="filter-tech" >Assigned Tech:</label>
                  <select class="hd-btn-filter-select" id="filter-tech" onchange="window.hdApp.filterTickets()" >
                    <option value="all">All Technicians</option>
                    <option value="Alexey Ivanov">Alexey Ivanov (Tier 3)</option>
                    <option value="Dmitry Popov">Dmitry Popov (Tier 2)</option>
                    <option value="Sofia Volkova">Sofia Volkova (Tier 1)</option>
                    <option value="Unassigned">Unassigned</option>
                  </select>
                </div>
              </div>

              <!-- Bulk Assign Button & Queue Count -->
              <div class="hd-flex-gap-md" >
                <span class="hd-mono-muted-115" >Showing <strong>5 of 34</strong> Active</span>
                <button class="btn btn-orange btn-sm" onclick="window.hdApp.bulkAssign()" title="Assign pending unassigned tickets to active shift engineers">
                  <span>⚡ Bulk Assign</span>
                </button>
              </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto" >
              <table class="hd-table">
                <thead>
                  <tr>
                    <th class="hd-w-120" >Ticket ID</th>
                    <th class="hd-min-w-220" >Requester</th>
                    <th class="hd-min-w-260" >System Affected</th>
                    <th class="hd-w-120" >Priority</th>
                    <th class="hd-min-w-200" >Assigned Tech</th>
                    <th class="hd-w-120" >Status</th>
                    <th class="hd-w-110-right" >Action</th>
                  </tr>
                </thead>
                <tbody class="ticket-table-body">
                  <!-- Row 1: TICK-8819 (Critical) -->
                  <tr class="hd-table-row" data-id="TICK-8819" data-requester="Dr. Elena Rostova" data-system="SCADA" data-priority="Critical" data-status="In Progress" data-tech="Alexey Ivanov" onclick="window.location.href='TicketDetail.php'">
                    <td>
                      <span class="hd-mono-bold-navy-125" >TICK-8819</span>
                      <div class="hd-mono-muted-xs" >08:30 MSK</div>
                    </td>
                    <td>
                      <div class="hd-flex-gap-65" >
                        <img class="hd-avatar-28-orange" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM" alt="Elena Rostova"  />
                        <div>
                          <div class="hd-font-semibold-navy" >Dr. Elena Rostova</div>
                          <div class="hd-text-secondary-11" >Chief Optical Calibration Architect</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="hd-font-semibold-primary" >SCADA Modbus Gateway #3</div>
                      <div class="hd-text-muted-11" >Lipetsk Hot Blast Furnace #5 Gateway · Telemetry packet drop &gt; 14.8%</div>
                    </td>
                    <td>
                      <span class="priority-badge priority-critical">CRITICAL</span>
                    </td>
                    <td>
                      <div class="hd-flex-gap-sm" >
                        <img class="hd-avatar-24" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" alt="Alexey Ivanov"  />
                        <div>
                          <div class="hd-font-semibold-navy-12" >Alexey Ivanov</div>
                          <div class="hd-mono-muted-xs" >Tier 3 SCADA Eng</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="status-pill status-in-progress">In Progress</span>
                    </td>
                    <td class="hd-text-right" >
                      <a href="TicketDetail.php" class="btn btn-outline btn-sm" onclick="event.stopPropagation();">Triage →</a>
                    </td>
                  </tr>

                  <!-- Row 2: TICK-8820 (Critical) -->
                  <tr class="hd-table-row" data-id="TICK-8820" data-requester="Dr. Mikhail Abramov" data-system="Cleanroom" data-priority="Critical" data-status="In Progress" data-tech="Alexey Ivanov" onclick="window.location.href='TicketDetail.php'">
                    <td>
                      <span class="hd-mono-bold-navy-125" >TICK-8820</span>
                      <div class="hd-mono-muted-xs" >09:12 MSK</div>
                    </td>
                    <td>
                      <div class="hd-flex-gap-65" >
                        <img class="hd-avatar-28-orange" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" alt="Mikhail Abramov"  />
                        <div>
                          <div class="hd-font-semibold-navy" >Dr. Mikhail Abramov</div>
                          <div class="hd-text-secondary-11" >Principal Semiconductor Physicist</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="hd-font-semibold-primary" >Cleanroom Biometric Scanner Bay B</div>
                      <div class="hd-text-muted-11" >Nanofabrication Facility Bay B · RFID airlock interlock rejecting Level 3 credentials</div>
                    </td>
                    <td>
                      <span class="priority-badge priority-critical">CRITICAL</span>
                    </td>
                    <td>
                      <div class="hd-flex-gap-sm" >
                        <img class="hd-avatar-24" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" alt="Alexey Ivanov"  />
                        <div>
                          <div class="hd-font-semibold-navy-12" >Alexey Ivanov</div>
                          <div class="hd-mono-muted-xs" >Tier 3 SCADA Eng</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="status-pill status-in-progress">In Progress</span>
                    </td>
                    <td class="hd-text-right" >
                      <a href="TicketDetail.php" class="btn btn-outline btn-sm" onclick="event.stopPropagation();">Triage →</a>
                    </td>
                  </tr>

                  <!-- Row 3: TICK-8821 (High) -->
                  <tr class="hd-table-row" data-id="TICK-8821" data-requester="Viktor Morozov" data-system="Calibration" data-priority="High" data-status="Open" data-tech="Dmitry Popov" onclick="window.location.href='TicketDetail.php'">
                    <td>
                      <span class="hd-mono-bold-navy-125" >TICK-8821</span>
                      <div class="hd-mono-muted-xs" >10:05 MSK</div>
                    </td>
                    <td>
                      <div class="hd-flex-gap-65" >
                        <img class="hd-avatar-28" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" alt="Viktor Morozov"  />
                        <div>
                          <div class="hd-font-semibold-navy" >Viktor Morozov</div>
                          <div class="hd-text-secondary-11" >Lead SCADA Gateway Specialist</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="hd-font-semibold-primary" >FAT Laser Calibration Server</div>
                      <div class="hd-text-muted-11" >FAT Testing Bay #2 · Floating-point matrix overflow during 1000 Hz profile pass</div>
                    </td>
                    <td>
                      <span class="priority-badge priority-high">HIGH</span>
                    </td>
                    <td>
                      <div class="hd-flex-gap-sm" >
                        <img class="hd-avatar-24" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBxrM-O7aJYHYCDtkoA3WwbiOe6BxJ0vK7AcnogxwZN9MACsknTlpyGKyy-lWl2Hwn9IEZLPDCvVGrmxN2kvPEfzbJ5E4u5x6-38EP2exwXW8Dmm-7oMTzMG07_rmRLbT0xvZwQMFEwa4qJO5LcWbn58eWx3fSkVjAmSI3UWO8dCTgRg6GBgrY_MTUl-JF-JUf4K5CGPp0o4tvKoxbSqSysGT8r3j8de3w_sfk4F8p9ysiXXfbUkWPV" alt="Dmitry Popov"  />
                        <div>
                          <div class="hd-font-semibold-navy-12" >Dmitry Popov</div>
                          <div class="hd-mono-muted-xs" >Tier 2 Infrastructure</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="status-pill status-open">Open</span>
                    </td>
                    <td class="hd-text-right" >
                      <a href="TicketDetail.php" class="btn btn-outline btn-sm" onclick="event.stopPropagation();">Triage →</a>
                    </td>
                  </tr>

                  <!-- Row 4: TICK-8822 (Medium) -->
                  <tr class="hd-table-row" data-id="TICK-8822" data-requester="Anna Belova" data-system="PKI" data-priority="Medium" data-status="In Progress" data-tech="Sofia Volkova" onclick="window.location.href='TicketDetail.php'">
                    <td>
                      <span class="hd-mono-bold-navy-125" >TICK-8822</span>
                      <div class="hd-mono-muted-xs" >Yesterday · 16:40</div>
                    </td>
                    <td>
                      <div class="hd-flex-gap-65" >
                        <img class="hd-avatar-28" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM" alt="Anna Belova"  />
                        <div>
                          <div class="hd-font-semibold-navy" >Anna Belova</div>
                          <div class="hd-text-secondary-11" >Head of Quality Assurance</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="hd-font-semibold-primary" >ISO 9001 Certificate Signer</div>
                      <div class="hd-text-muted-11" >Cryptographic smartcard PKI token renewal required for electronic FAT signing</div>
                    </td>
                    <td>
                      <span class="priority-badge priority-medium">MEDIUM</span>
                    </td>
                    <td>
                      <div class="hd-flex-gap-sm" >
                        <img class="hd-avatar-24" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM" alt="Sofia Volkova"  />
                        <div>
                          <div class="hd-font-semibold-navy-12" >Sofia Volkova</div>
                          <div class="hd-mono-muted-xs" >Tier 1 Support</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="status-pill status-in-progress">In Progress</span>
                    </td>
                    <td class="hd-text-right" >
                      <a href="TicketDetail.php" class="btn btn-outline btn-sm" onclick="event.stopPropagation();">Triage →</a>
                    </td>
                  </tr>

                  <!-- Row 5: TICK-8823 (Low) -->
                  <tr class="hd-table-row" data-id="TICK-8823" data-requester="Svetlana Petrova" data-system="ERP" data-priority="Low" data-status="Open" data-tech="Sofia Volkova" onclick="window.location.href='TicketDetail.php'">
                    <td>
                      <span class="hd-mono-bold-navy-125" >TICK-8823</span>
                      <div class="hd-mono-muted-xs" >Yesterday · 14:15</div>
                    </td>
                    <td>
                      <div class="hd-flex-gap-65" >
                        <img class="hd-avatar-28" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM" alt="Svetlana Petrova"  />
                        <div>
                          <div class="hd-font-semibold-navy" >Svetlana Petrova</div>
                          <div class="hd-text-secondary-11" >Strategic Component Buyer</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="hd-font-semibold-primary" >ERP Procurement Signing Authority</div>
                      <div class="hd-text-muted-11" >Enterprise ERP Module · Temporary delegation setup for scheduled leave window</div>
                    </td>
                    <td>
                      <span class="priority-badge priority-low">LOW</span>
                    </td>
                    <td>
                      <div class="hd-flex-gap-sm" >
                        <img class="hd-avatar-24" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM" alt="Sofia Volkova"  />
                        <div>
                          <div class="hd-font-semibold-navy-12" >Sofia Volkova</div>
                          <div class="hd-mono-muted-xs" >Tier 1 Support</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="status-pill status-open">Open</span>
                    </td>
                    <td class="hd-text-right" >
                      <a href="TicketDetail.php" class="btn btn-outline btn-sm" onclick="event.stopPropagation();">Triage →</a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Table Pagination / Footer -->
            <div class="hd-footer-pagination" >
              <div class="hd-mono" >
                Page <strong>1</strong> of <strong>7</strong> · Total <strong>34 Tickets</strong>
              </div>
              <div class="hd-gap-sm" >
                <button class="btn btn-outline btn-sm hd-pill-pad-sm"  disabled>← Previous</button>
                <button class="btn btn-outline btn-sm hd-pill-pad-sm"  onclick="window.hdApp.showToast('Pagination', 'Loaded page 2 of ticket queue.')">Next →</button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
</body>

</html>