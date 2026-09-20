<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR HR System · Leave Management &amp; Approvals</title>
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
                <span class="system-tag">HR · SYS 06</span>
              </div>
              <div class="brand-subline">
                <span class="status-dot-pulse"></span>
                <span>hr.vostokpribor.local</span>
                <span style="opacity: 0.5;">|</span>
                <span>PERSONNEL OPERATIONS</span>
              </div>
            </div>
          </a>
        </div>

        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search employee records, EMP-ID, clearance level, department..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <div class="top-nav__actions">
          <div class="confidential-system-pill" title="Restricted Personnel System">
            <span>🔒</span>
            <span>HIGHLY CONFIDENTIAL SYSTEM</span>
          </div>

          <button class="icon-button" onclick="window.hrApp.showToast('Leave Queue', '19 requests pending division director review.')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="badge-dot"></span>
          </button>

          <div class="top-user-profile" onclick="window.hrApp.showToast('Active User Session', 'Valeria Zaytseva · Chief Human Capital Officer')">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM" alt="Valeria" class="user-avatar-top" />
            <div class="user-details-top">
              <span class="user-name-top">Valeria Zaytseva</span>
              <span class="user-role-top">Chief HR Officer · Level 4</span>
            </div>
          </div>
        </div>
      
<!-- Top Bar Sign Out -->
<a href="../api/logout.php?system=HR%20System&redirect=../HR%20System/login.php" class="top-signout-btn" title="Sign Out of HR System" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:4px;background:rgba(178,58,50,0.2);border:1px solid rgba(178,58,50,0.5);color:#FF8080;font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;margin-left:8px;vertical-align:middle;transition:all 0.2s;" onmouseover="this.style.background='rgba(178,58,50,0.4)';this.style.color='#FFFFFF'" onmouseout="this.style.background='rgba(178,58,50,0.2)';this.style.color='#FF8080'"><span class="material-symbols-outlined" style="font-size:15px;line-height:1;">logout</span><span>Sign Out</span></a>
</div>
    </header>

    <div class="main-layout">
      <!-- SIDEBAR -->
      <aside class="sidebar">
        <div>
          <div class="sidebar-section-title">Human Resources</div>
          <nav class="sidebar-nav">
            <a href="Dashboard.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
                <span>Dashboard</span>
              </div>
            </a>
            <a href="EmployeeRecords.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                <span>Employee Records</span>
              </div>
              <span class="sidebar-badge">1,428</span>
            </a>
            <a href="OnboardingTracker.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg></span>
                <span>Recruitment &amp; Onboarding</span>
              </div>
              <span class="sidebar-badge">12</span>
            </a>
            <a href="LeaveManagement.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                <span>Leave Management</span>
              </div>
              <span class="sidebar-badge badge-amber">19</span>
            </a>
            <a href="OrgStructure.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="5" r="3"/><circle cx="5" cy="19" r="3"/><circle cx="19" cy="19" r="3"/><path d="M12 8v4"/><path d="M5 16v-2a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v2"/></svg></span>
                <span>Org Structure</span>
              </div>
              <span class="sidebar-badge">8</span>
            </a>
            <a href="Training.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></span>
                <span>Training &amp; Certs</span>
              </div>
              <span class="sidebar-badge">94%</span>
            </a>
            <a href="Offboarding.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></span>
                <span>Offboarding</span>
              </div>
              <span class="sidebar-badge badge-red">3</span>
            </a>

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
            <a href="../api/logout.php?system=HR%20System&redirect=../HR%20System/login.php" class="sidebar-nav-item sidebar-nav-item--logout" id="btn-logout" onclick="(function(){sessionStorage.clear();localStorage.clear();})()"
              onclick="(function(){sessionStorage.clear();localStorage.clear();})()">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </span>
                <span>Log Out</span>
              </div>
            </a>
          </nav>
        </div>

        <div class="sidebar-footer">
          <div class="security-widget-card">
            <div class="security-widget-header">
              <span>Security Clearance Registry</span>
              <span class="security-badge-status">● GOST 1G</span>
            </div>
            <div style="font-size: 11px; color: var(--hr-text-inverse-muted); margin-top: 2px;">
              Active Level 4 Clearances: <strong>24 Vetted</strong>
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
                <a href="Dashboard.php">HR System</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Leave Management</span>
              </div>
              <h1 class="page-title">Leave Approvals &amp; Engineering Coverage Schedule</h1>
              <p class="page-subtitle">Statutory annual leave, technical sabbatical, and medical absence workflows per Russian Labor Code (TK RF)</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.hrApp.showToast('Leave Schedule Export', 'Coverage matrix exported to Shift Planner.')">
                <span>📅 Export Shift Schedule</span>
              </button>
              <button class="btn btn-primary-amber" onclick="window.hrApp.showToast('Leave Filing', 'Official Leave Request form initiated.')">
                <span>+ Submit Leave Request</span>
              </button>
            </div>
          </div>

          <!-- Leave KPI Strip -->
          <div class="kpi-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="hr-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Awaiting Approval</span>
                <div class="kpi-icon-pill amber">⚡</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">19</span>
                <span style="font-size: 13px; color: var(--hr-text-muted);">Requests</span>
              </div>
              <div class="kpi-footer">
                <span>7 Optical &amp; SCADA Engineers</span>
                <span class="kpi-trend alert">SLA &lt; 24h</span>
              </div>
            </div>

            <div class="hr-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Currently On Leave</span>
                <div class="kpi-icon-pill steel">🏖️</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">38</span>
                <span style="font-size: 13px; color: var(--hr-text-muted);">Engineers</span>
              </div>
              <div class="kpi-footer">
                <span>2.6% of Total Workforce</span>
                <span class="kpi-trend up">Normal Operations</span>
              </div>
            </div>

            <div class="hr-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Statutory Days Utilized</span>
                <div class="kpi-icon-pill plum">📊</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">71.4%</span>
                <span style="font-size: 13px; color: var(--hr-text-muted);">Annual Quota</span>
              </div>
              <div class="kpi-footer">
                <span>Min. 28 Calendar Days Mandatory</span>
                <span class="kpi-trend up">Compliant</span>
              </div>
            </div>
          </div>

          <!-- Leave Request Approvals Queue Table -->
          <div class="hr-card" style="padding: 0; overflow: hidden; margin-bottom: 1.5rem;">
            <div style="padding: 1rem 1.25rem; background-color: #FFFFFF; border-bottom: 1px solid var(--hr-surface-border); display: flex; align-items: center; justify-content: space-between;">
              <div>
                <h3 class="card-title">Pending Leave Requests Awaiting Governance Sign-off</h3>
                <p style="font-size: 11.5px; color: var(--hr-text-secondary); margin-top: 2px;">
                  Verify technical coverage in cleanroom and SCADA control rooms prior to granting authorization
                </p>
              </div>
              <button class="btn btn-plum btn-sm" onclick="window.hrApp.showToast('Batch Approval', 'All 19 standard leave requests approved and logged.', 'success')">
                <span>✓ Batch Approve Compliant Requests</span>
              </button>
            </div>

            <table class="employee-table">
              <thead>
                <tr>
                  <th>Employee</th>
                  <th>Division</th>
                  <th>Leave Type</th>
                  <th>Dates &amp; Duration</th>
                  <th>Shift Coverage Plan</th>
                  <th>Clearance</th>
                  <th style="text-align: right;">Decision</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div style="font-weight: 700; color: var(--hr-navy);">Svetlana Petrova</div>
                    <div style="font-size: 11px; color: var(--hr-text-muted); font-family: var(--hr-font-mono);">EMP-VP-0518</div>
                  </td>
                  <td>Procurement &amp; Logistics</td>
                  <td><span class="status-pill status-active">Annual Paid Leave</span></td>
                  <td><strong>Nov 10 – Nov 24, 2024</strong> (14 Days)</td>
                  <td>Covered by D. Korolev (Buyer II)</td>
                  <td><span class="clearance-badge clearance-l2">L2</span></td>
                  <td style="text-align: right;">
                    <button class="btn btn-primary-amber btn-sm" onclick="this.textContent='Approved ✓'; this.classList.remove('btn-primary-amber'); this.classList.add('btn-outline'); window.hrApp.showToast('Leave Approved', 'Svetlana Petrova leave ratified.');">Approve</button>
                  </td>
                </tr>

                <tr>
                  <td>
                    <div style="font-weight: 700; color: var(--hr-navy);">Viktor Morozov</div>
                    <div style="font-size: 11px; color: var(--hr-text-muted); font-family: var(--hr-font-mono);">EMP-VP-0219</div>
                  </td>
                  <td>SCADA &amp; Automation</td>
                  <td><span class="status-pill status-probation">Technical Sabbatical</span></td>
                  <td><strong>Dec 01 – Dec 15, 2024</strong> (14 Days)</td>
                  <td>Covered by Dr. Elena Rostova</td>
                  <td><span class="clearance-badge clearance-l3">L3</span></td>
                  <td style="text-align: right;">
                    <button class="btn btn-primary-amber btn-sm" onclick="this.textContent='Approved ✓'; this.classList.remove('btn-primary-amber'); this.classList.add('btn-outline'); window.hrApp.showToast('Leave Approved', 'Viktor Morozov sabbatical approved.');">Approve</button>
                  </td>
                </tr>

                <tr>
                  <td>
                    <div style="font-weight: 700; color: var(--hr-navy);">Denis Sokolov</div>
                    <div style="font-size: 11px; color: var(--hr-text-muted); font-family: var(--hr-font-mono);">EMP-VP-0310</div>
                  </td>
                  <td>Blast Furnace Robotics</td>
                  <td><span class="status-pill status-active">Annual Paid Leave</span></td>
                  <td><strong>Nov 20 – Dec 04, 2024</strong> (14 Days)</td>
                  <td>Covered by P. Zimin (Actuator Lead)</td>
                  <td><span class="clearance-badge clearance-l3">L3</span></td>
                  <td style="text-align: right;">
                    <button class="btn btn-primary-amber btn-sm" onclick="this.textContent='Approved ✓'; this.classList.remove('btn-primary-amber'); this.classList.add('btn-outline'); window.hrApp.showToast('Leave Approved', 'Denis Sokolov leave ratified.');">Approve</button>
                  </td>
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
