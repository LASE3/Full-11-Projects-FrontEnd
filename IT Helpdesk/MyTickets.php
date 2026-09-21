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
                <span style="opacity: 0.5;">|</span>
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
          <div class="pipeline-sync-badge" style="display: flex; align-items: center; gap: 0.35rem; font-family: var(--hd-font-mono); font-size: 10px; color: var(--hd-text-inverse-muted); background: rgba(255,255,255,0.06); padding: 3px 8px; border-radius: var(--hd-radius-sm); border: 1px solid rgba(255,255,255,0.08);">
            <span style="color: #2ECC71;">●</span>
            <span>SLA: <strong>98.4% Compliant</strong></span>
          </div>
          <button class="icon-button" title="Incident Telemetry Notifications" onclick="window.hdApp.showToast('Critical Alert', 'SCADA Gateway Node #3 packet loss detected in Lipetsk Bay.', 'critical')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
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
<a href="../api/logout.php?system=IT%20Helpdesk&redirect=../IT%20Helpdesk/login.php" class="top-signout-btn" title="Sign Out of IT Helpdesk" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:4px;background:rgba(178,58,50,0.2);border:1px solid rgba(178,58,50,0.5);color:#FF8080;font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;margin-left:8px;vertical-align:middle;transition:all 0.2s;" onmouseover="this.style.background='rgba(178,58,50,0.4)';this.style.color='#FFFFFF'" onmouseout="this.style.background='rgba(178,58,50,0.2)';this.style.color='#FF8080'"><span class="material-symbols-outlined" style="font-size:15px;line-height:1;">logout</span><span>Sign Out</span></a>
</div>
    </header>

    <div class="main-layout">
      <!-- SIDEBAR -->
      <aside class="sidebar">
        <div>
          <div class="sidebar-section-title">IT Support Operations</div>
          <nav class="sidebar-nav">
            <a href="Dashboard.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
                <span>Dashboard</span>
              </div>
            </a>
            <a href="TicketQueue.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></span>
                <span>Ticket Queue</span>
              </div>
              <span class="sidebar-badge badge-orange">34</span>
            </a>
            <a href="MyTickets.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                <span>My Tickets</span>
              </div>
              <span class="sidebar-badge badge-red">8</span>
            </a>
            <a href="KnowledgeBase.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span>
                <span>Knowledge Base</span>
              </div>
              <span class="sidebar-badge">142</span>
            </a>
            <a href="AssetManagement.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg></span>
                <span>Asset Management</span>
              </div>
              <span class="sidebar-badge">1,820</span>
            </a>
            <a href="SLAReports.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                <span>SLA Reports</span>
              </div>
              <span class="sidebar-badge badge-green">98.4%</span>
            </a>
          </nav>
        </div>
        
                      <div class="sidebar-section-title" style="margin-top: 1rem;">Unified Ecosystem</div>
          <nav class="sidebar-nav" style="margin-bottom: 0.5rem;">
            <a href="../VOSTOKPRIBOR Corporate Web Platform/index.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                </span>
                <span>Corporate Platform</span>
              </div>
              <span class="sidebar-badge" style="font-size: 10px;">SYS 01</span>
            </a>
            <a href="../Employee Intranet/index.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                </span>
                <span>Employee Intranet</span>
              </div>
              <span class="sidebar-badge" style="font-size: 10px;">SYS 04</span>
            </a>
          </nav>
            <!-- Log Out -->
            
        <div class="sidebar-footer">
          <div class="security-widget-card">
            <div class="security-widget-header">
              <span>Incident Response Gateway</span>
              <span class="security-badge-status">● LIVE</span>
            </div>
            <div style="font-size: 11px; color: var(--hd-text-inverse-muted); margin-top: 2px;">
              Active Escalations: <strong>3 P1 Incidents</strong>
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
                <span class="breadcrumb-current">Assigned to Alexey Ivanov</span>
              </div>
              <h1 class="page-title">My Active Incident &amp; Task Worklist</h1>
              <p class="page-subtitle">Personal queue of 8 assigned incidents with SLA breach horizon tracking</p>
            </div>
            <div class="page-header-actions">
              <a href="TicketDetail.php" class="btn btn-primary-amber">
                <span>⚡ Resume P1 Modbus Incident</span>
              </a>
            </div>
          </div>

          <div class="hd-card" style="padding: 0; overflow: hidden;">
            <div style="padding: 1rem 1.25rem; background: var(--hd-surface); border-bottom: 1px solid var(--hd-surface-border); display: flex; justify-content: space-between; align-items: center;">
              <div style="font-size: 13px; font-weight: 700; color: var(--hd-navy);">
                Assigned Incidents (8) · Sorted by SLA Urgency
              </div>
              <span class="priority-badge priority-critical">2 P1 Critical Requiring Action</span>
            </div>

            <table class="hd-table">
              <thead>
                <tr>
                  <th style="width: 120px;">Ticket ID</th>
                  <th>Requester &amp; Facility</th>
                  <th>System &amp; Error Description</th>
                  <th>Priority</th>
                  <th>SLA Horizon</th>
                  <th>Status</th>
                  <th style="text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr class="hd-table-row" onclick="window.location.href='TicketDetail.php'">
                  <td><strong style="font-family: var(--hd-font-mono); color: var(--hd-navy);">TICK-8819</strong></td>
                  <td>
                    <div style="font-weight: 600; color: var(--hd-navy);">Dr. Elena Rostova</div>
                    <div style="font-size: 11px; color: var(--hd-text-muted);">Optical Calibration · Lipetsk Bay</div>
                  </td>
                  <td>
                    <div style="font-weight: 600;">SCADA Modbus Gateway #3</div>
                    <div style="font-size: 11px; color: var(--hd-text-muted);">Frame drops during thermal ramp &gt; 1,450°C</div>
                  </td>
                  <td><span class="priority-badge priority-critical">CRITICAL</span></td>
                  <td><strong style="font-family: var(--hd-font-mono); color: var(--hd-priority-critical);">01:42:15</strong></td>
                  <td><span class="status-pill status-in-progress">In Progress</span></td>
                  <td style="text-align: right;"><a href="TicketDetail.php" class="btn btn-primary-amber btn-sm">Triage →</a></td>
                </tr>

                <tr class="hd-table-row" onclick="window.location.href='TicketDetail.php'">
                  <td><strong style="font-family: var(--hd-font-mono); color: var(--hd-navy);">TICK-8820</strong></td>
                  <td>
                    <div style="font-weight: 600; color: var(--hd-navy);">Dr. Mikhail Abramov</div>
                    <div style="font-size: 11px; color: var(--hd-text-muted);">R&amp;D Sensor Fab · Bay B</div>
                  </td>
                  <td>
                    <div style="font-weight: 600;">Cleanroom Biometric Scanner</div>
                    <div style="font-size: 11px; color: var(--hd-text-muted);">Airlock interlock rejecting Level 3 badges</div>
                  </td>
                  <td><span class="priority-badge priority-critical">CRITICAL</span></td>
                  <td><strong style="font-family: var(--hd-font-mono); color: var(--hd-priority-critical);">00:48:30</strong></td>
                  <td><span class="status-pill status-in-progress">In Progress</span></td>
                  <td style="text-align: right;"><a href="TicketDetail.php" class="btn btn-primary-amber btn-sm">Triage →</a></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
</body>
</html>
