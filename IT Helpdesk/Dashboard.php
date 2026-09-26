<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('IT');
$pdo = getDbConnection();
$currUser = $_SESSION['vostok_user'] ?? ['full_name' => 'Authorized User', 'clearance_level' => 'L2'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR IT Helpdesk · Operational Support Desk</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>

  <div class="app-container">
    <!-- ========================================================================
         TOP NAVIGATION BAR (#0F2438 Deep Navy + #C97A3D Warm Orange Accent Stripe)
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
           LEFT SIDEBAR NAVIGATION (#0F2438 Dark Navy + #C97A3D Warm Orange)
           ======================================================================== -->
      <aside class="sidebar">
        <div>
          <div class="sidebar-section-title">IT Support Operations</div>
          <nav class="sidebar-nav">
            <!-- Screen 1: Dashboard (Active) -->
            <a href="Dashboard.php" class="sidebar-nav-item active">
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

            <!-- Screen 2: Ticket Queue -->
            <a href="TicketQueue.php" class="sidebar-nav-item">
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
            <a href="Integrations.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00E5FF" stroke-width="2">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                  </svg></span><span class="hd-nav-integrations" >System Integrations</span></div><span class="sidebar-badge hd-badge-integrations" >SYS09</span>
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
           MAIN CONTENT AREA: SCREEN 1 DASHBOARD
           ======================================================================== -->
      <main class="content-wrapper">
        <div class="portal-container">
          <!-- Page Header -->
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <span>IT Helpdesk</span>
                <span class="breadcrumb-separator">/</span>
                <span>Operations Central</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Triage &amp; SLA Dashboard</span>
              </div>
              <h1 class="page-title">IT Incident Command &amp; Service Desk Dashboard</h1>
              <p class="page-subtitle">Real-time incident queue telemetry, priority heat matrix, SLA breach prevention, and resolution pacing</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.hdApp.showToast('SLA Audit', 'SLA compliance report generated for Weekly Operations Review.')">
                <span>📑 Export SLA Audit</span>
              </button>
              <a href="TicketDetail.php" class="btn btn-primary-amber">
                <span>⚡ Triage Active P1 Incident</span>
              </a>
            </div>
          </div>

          <!-- Top Row 4 KPI Cards -->
          <div class="kpi-grid">
            <!-- KPI 1: Open Tickets -->
            <div class="hd-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Open Tickets</span>
                <div class="kpi-icon-pill orange">🎫</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value">34</span>
                <span class="hd-text-muted-125" >Active</span>
              </div>
              <div class="kpi-footer">
                <span>12 Unassigned in Queue</span>
                <span class="kpi-trend up">▲ +4 Today</span>
              </div>
            </div>

            <!-- KPI 2: Critical / Escalated -->
            <div class="hd-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Critical / Escalated</span>
                <div class="kpi-icon-pill red">🚨</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value hd-text-critical" >3</span>
                <span class="hd-text-crit-semibold" >Tier 1</span>
              </div>
              <div class="kpi-footer">
                <span>2 SCADA, 1 Cleanroom</span>
                <span class="kpi-trend alert">SLA &lt; 2h Target</span>
              </div>
            </div>

            <!-- KPI 3: Avg Resolution Time -->
            <div class="hd-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Avg Resolution Time</span>
                <div class="kpi-icon-pill steel">⏱️</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value">42 min</span>
              </div>
              <div class="kpi-footer">
                <span>Target SLA: &lt; 60 min</span>
                <span class="kpi-trend up">▼ -18% Faster</span>
              </div>
            </div>

            <!-- KPI 4: SLA Compliance % -->
            <div class="hd-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">SLA Compliance %</span>
                <div class="kpi-icon-pill green">✓</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value hd-text-success" >98.4%</span>
              </div>
              <div class="kpi-footer">
                <span>282 / 286 Met Target</span>
                <span class="kpi-trend up">Exceeds Target</span>
              </div>
            </div>
          </div>

          <!-- Stacked Bar Chart: Ticket Volume by Day Colored by Priority Tier -->
          <div class="hd-card hd-mb-6" >
            <div class="card-header-row">
              <div>
                <h3 class="card-title">Daily Ticket Volume by Heat-Scale Priority Tier</h3>
                <p class="hd-meta-subtext" >
                  Weekly incident distribution classified by operational severity tiers
                </p>
              </div>
              <!-- Legend -->
              <div class="hd-legend-row" >
                <div class="hd-flex-gap-xs" >
                  <span class="hd-legend-dot-crit" ></span>
                  <span class="hd-font-semibold-navy" >Critical</span>
                </div>
                <div class="hd-flex-gap-xs" >
                  <span class="hd-legend-dot-high" ></span>
                  <span class="hd-font-semibold-navy" >High</span>
                </div>
                <div class="hd-flex-gap-xs" >
                  <span class="hd-legend-dot-med" ></span>
                  <span class="hd-font-semibold-navy" >Medium</span>
                </div>
                <div class="hd-flex-gap-xs" >
                  <span class="hd-legend-dot-low" ></span>
                  <span class="hd-font-semibold-navy" >Low</span>
                </div>
              </div>
            </div>

            <!-- Stacked Chart Days -->
            <div class="stacked-chart-container">
              <div class="stacked-days-grid">
                <!-- Monday (42 tickets) -->
                <div class="stacked-col">
                  <div class="stacked-day-total">42</div>
                  <div class="stacked-bar-pillar hd-bar-h-140" >
                    <div class="seg-low hd-bar-h-40"  title="Low: 12"></div>
                    <div class="seg-med hd-bar-h-55"  title="Medium: 18"></div>
                    <div class="seg-high hd-bar-h-30"  title="High: 9"></div>
                    <div class="seg-crit hd-bar-h-15"  title="Critical: 3"></div>
                  </div>
                  <span class="stacked-day-label">Mon</span>
                </div>

                <!-- Tuesday (48 tickets) -->
                <div class="stacked-col">
                  <div class="stacked-day-total">48</div>
                  <div class="stacked-bar-pillar hd-bar-h-160" >
                    <div class="seg-low hd-bar-h-45"  title="Low: 14"></div>
                    <div class="seg-med hd-bar-h-65"  title="Medium: 21"></div>
                    <div class="seg-high hd-bar-h-35"  title="High: 10"></div>
                    <div class="seg-crit hd-bar-h-15"  title="Critical: 3"></div>
                  </div>
                  <span class="stacked-day-label">Tue</span>
                </div>

                <!-- Wednesday (56 tickets) -->
                <div class="stacked-col">
                  <div class="stacked-day-total">56</div>
                  <div class="stacked-bar-pillar hd-bar-h-185" >
                    <div class="seg-low hd-bar-h-50"  title="Low: 16"></div>
                    <div class="seg-med hd-bar-h-75"  title="Medium: 24"></div>
                    <div class="seg-high hd-bar-h-40"  title="High: 12"></div>
                    <div class="seg-crit hd-bar-h-20"  title="Critical: 4"></div>
                  </div>
                  <span class="stacked-day-label">Wed</span>
                </div>

                <!-- Thursday (52 tickets) -->
                <div class="stacked-col">
                  <div class="stacked-day-total">52</div>
                  <div class="stacked-bar-pillar hd-bar-h-172" >
                    <div class="seg-low hd-bar-h-48"  title="Low: 15"></div>
                    <div class="seg-med hd-bar-h-70"  title="Medium: 23"></div>
                    <div class="seg-high hd-bar-h-38"  title="High: 11"></div>
                    <div class="seg-crit hd-bar-h-16"  title="Critical: 3"></div>
                  </div>
                  <span class="stacked-day-label">Thu</span>
                </div>

                <!-- Friday (64 tickets - peak) -->
                <div class="stacked-col">
                  <div class="stacked-day-total hd-text-critical" >64</div>
                  <div class="stacked-bar-pillar hd-bar-h-200" >
                    <div class="seg-low hd-bar-h-55"  title="Low: 18"></div>
                    <div class="seg-med hd-bar-h-80"  title="Medium: 26"></div>
                    <div class="seg-high hd-bar-h-45"  title="High: 15"></div>
                    <div class="seg-crit hd-bar-h-20"  title="Critical: 5"></div>
                  </div>
                  <span class="stacked-day-label hd-bold-navy" >Fri</span>
                </div>

                <!-- Saturday (16 tickets) -->
                <div class="stacked-col">
                  <div class="stacked-day-total">16</div>
                  <div class="stacked-bar-pillar hd-bar-h-55" >
                    <div class="seg-low hd-bar-h-20"  title="Low: 6"></div>
                    <div class="seg-med hd-bar-h-25"  title="Medium: 8"></div>
                    <div class="seg-high hd-bar-h-10"  title="High: 2"></div>
                  </div>
                  <span class="stacked-day-label">Sat</span>
                </div>

                <!-- Sunday (8 tickets) -->
                <div class="stacked-col">
                  <div class="stacked-day-total">8</div>
                  <div class="stacked-bar-pillar hd-bar-h-30" >
                    <div class="seg-low hd-bar-h-15"  title="Low: 4"></div>
                    <div class="seg-med hd-bar-h-15"  title="Medium: 4"></div>
                  </div>
                  <span class="stacked-day-label">Sun</span>
                </div>
              </div>
            </div>
          </div>

          <!-- "Needs Attention" List Widget (Tickets Nearing SLA Breach) -->
          <div class="hd-card">
            <div class="card-header-row">
              <div>
                <h3 class="card-title">Immediate Attention Required · Nearing SLA Breach Horizon</h3>
                <p class="hd-meta-subtext" >
                  High-priority telemetry faults and access gating blockers highlighted in red/orange
                </p>
              </div>
              <a href="TicketQueue.php" class="btn btn-orange btn-sm">
                <span>View Full Queue (34) →</span>
              </a>
            </div>

            <div class="needs-attention-list">
              <!-- Item 1: Critical SCADA Node (Red) -->
              <div class="attention-item">
                <div class="hd-flex-gap-1" >
                  <div class="hd-stat-column-box" >
                    <span class="priority-badge priority-critical">CRITICAL</span>
                    <span class="hd-mono-critical-sm" >01:42:15 Left</span>
                  </div>
                  <div>
                    <div class="hd-flex-gap-sm" >
                      <a class="hd-title-link" href="TicketDetail.php" >
                        TICK-8819 · SCADA Modbus Gateway #3 Packet Drop
                      </a>
                      <span class="status-pill status-in-progress">In Progress</span>
                    </div>
                    <div class="hd-meta-subtext" >
                      Requester: <strong>Dr. Elena Rostova</strong> (Optical Calibration) · System: <strong>Lipetsk Hot Blast Furnace #5 Gateway</strong>
                    </div>
                  </div>
                </div>
                <div class="hd-flex-gap-md" >
                  <div class="hd-text-right-11" >
                    <div class="hd-text-muted" >Assigned Tech</div>
                    <div class="hd-bold-navy" >Alexey Ivanov</div>
                  </div>
                  <a href="TicketDetail.php" class="btn btn-primary-amber btn-sm">Triage →</a>
                </div>
              </div>

              <!-- Item 2: Cleanroom Biometric Scanner (Red) -->
              <div class="attention-item">
                <div class="hd-flex-gap-1" >
                  <div class="hd-stat-column-box" >
                    <span class="priority-badge priority-critical">CRITICAL</span>
                    <span class="hd-mono-critical-sm" >00:48:30 Left</span>
                  </div>
                  <div>
                    <div class="hd-flex-gap-sm" >
                      <a class="hd-title-link" href="TicketDetail.php" >
                        TICK-8820 · Cleanroom Airlock RFID Interlock Rejecting Level 3 Badges
                      </a>
                      <span class="status-pill status-in-progress">In Progress</span>
                    </div>
                    <div class="hd-meta-subtext" >
                      Requester: <strong>Dr. Mikhail Abramov</strong> (R&amp;D Sensor Fab) · System: <strong>Nanofabrication Facility Bay B</strong>
                    </div>
                  </div>
                </div>
                <div class="hd-flex-gap-md" >
                  <div class="hd-text-right-11" >
                    <div class="hd-text-muted" >Assigned Tech</div>
                    <div class="hd-bold-navy" >Alexey Ivanov</div>
                  </div>
                  <a href="TicketDetail.php" class="btn btn-primary-amber btn-sm">Triage →</a>
                </div>
              </div>

              <!-- Item 3: FAT Calibration Server (Orange) -->
              <div class="attention-item warning-tier">
                <div class="hd-flex-gap-1" >
                  <div class="hd-stat-column-box" >
                    <span class="priority-badge priority-high">HIGH</span>
                    <span class="hd-mono-orange-sm" >02:15:00 Left</span>
                  </div>
                  <div>
                    <div class="hd-flex-gap-sm" >
                      <a class="hd-title-link" href="TicketDetail.php" >
                        TICK-8821 · Laser Triangulation Calibration Server Matrix Overflow
                      </a>
                      <span class="status-pill status-open">Open</span>
                    </div>
                    <div class="hd-meta-subtext" >
                      Requester: <strong>Viktor Morozov</strong> (SCADA Specialist) · System: <strong>FAT Acceptance Testing Bay #2</strong>
                    </div>
                  </div>
                </div>
                <div class="hd-flex-gap-md" >
                  <div class="hd-text-right-11" >
                    <div class="hd-text-muted" >Assigned Tech</div>
                    <div class="hd-bold-navy" >Dmitry Popov</div>
                  </div>
                  <a href="TicketDetail.php" class="btn btn-outline btn-sm">Review →</a>
                </div>
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