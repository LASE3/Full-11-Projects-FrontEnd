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
                <span class="hd-opacity-50" >|</span>
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
              <span class="sidebar-badge badge-orange">34</span>
            </a>
            <a href="MyTickets.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                  </svg></span>
                <span>My Tickets</span>
              </div>
              <span class="sidebar-badge badge-red">8</span>
            </a>
            <a href="KnowledgeBase.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                  </svg></span>
                <span>Knowledge Base</span>
              </div>
              <span class="sidebar-badge">142</span>
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
              <span class="sidebar-badge">1,820</span>
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
              <p class="page-subtitle">1,820 managed edge nodes, industrial gateways, optical pyrometers, and biometric interlocks</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.hdApp.showToast('Asset Discovery', 'Triggering ARP scan on industrial OT subnets (10.240.0.0/16)...')">
                <span>🔍 Scan OT Network</span>
              </button>
              <button class="btn btn-primary-amber" onclick="window.hdApp.showToast('Add Asset', 'Registering new hardware device to IT asset database.')">
                <span>+ Register Device</span>
              </button>
            </div>
          </div>

          <div class="hd-card hd-panel-flush" >
            <table class="hd-table">
              <thead>
                <tr>
                  <th class="hd-w-140" >Asset Tag</th>
                  <th>Device Model &amp; Type</th>
                  <th>Location / Bay</th>
                  <th>IP Address &amp; MAC</th>
                  <th>Firmware</th>
                  <th>Health Status</th>
                  <th class="hd-text-right" >Action</th>
                </tr>
              </thead>
              <tbody>
                <tr class="hd-table-row">
                  <td><strong class="hd-mono-navy" >VP-GW-LIP-03</strong></td>
                  <td>
                    <div class="hd-font-semibold-navy" >Moxa MGate MB3170</div>
                    <div class="hd-text-muted-11" >Modbus TCP/IP to RS-485 Gateway</div>
                  </td>
                  <td>Lipetsk Furnace #5 Bay</td>
                  <td><code class="hd-mono-navy-11" >10.240.48.12</code></td>
                  <td>v4.2.1-sec</td>
                  <td><span class="priority-badge priority-critical">Degraded (14% Loss)</span></td>
                  <td class="hd-text-right" ><a href="TicketDetail.php" class="btn btn-outline btn-sm">Triage →</a></td>
                </tr>

                <tr class="hd-table-row">
                  <td><strong class="hd-mono-navy" >VP-BIO-FAB-02</strong></td>
                  <td>
                    <div class="hd-font-semibold-navy" >Suprema BioEntry W2</div>
                    <div class="hd-text-muted-11" >Cleanroom RFID/Biometric Airlock</div>
                  </td>
                  <td>Nanofabrication Bay B</td>
                  <td><code class="hd-mono-navy-11" >10.240.90.05</code></td>
                  <td>v2.18.0</td>
                  <td><span class="priority-badge priority-critical">Interlock Fault</span></td>
                  <td class="hd-text-right" ><a href="TicketDetail.php" class="btn btn-outline btn-sm">Triage →</a></td>
                </tr>

                <tr class="hd-table-row">
                  <td><strong class="hd-mono-navy" >VP-SRV-FAT-01</strong></td>
                  <td>
                    <div class="hd-font-semibold-navy" >Advantech MIC-7700</div>
                    <div class="hd-text-muted-11" >Industrial Rugged Xeon Server</div>
                  </td>
                  <td>FAT Acceptance Bay #2</td>
                  <td><code class="hd-mono-navy-11" >10.240.12.80</code></td>
                  <td>Ubuntu 22.04 RT</td>
                  <td><span class="status-pill status-in-progress">Online (Active)</span></td>
                  <td class="hd-text-right" ><button class="btn btn-outline btn-sm" onclick="window.hdApp.showToast('Telemetry', 'Advantech MIC-7700 CPU Load: 18%, Temp: 44°C.')">Inspect</button></td>
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