<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR HR System · Human Capital Operations</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>

  <div class="app-container">
    <!-- ========================================================================
         TOP NAVIGATION BAR (#0F2438 Navy + Plum Accent Stripe + Confidential Badge)
         ======================================================================== -->
    <header class="top-nav">
      <!-- 4px System Identity Stripe (Plum) -->
      <div class="top-nav__accent-stripe"></div>

      <div class="top-nav__content">
        <!-- Brand & System Identifier -->
        <div class="brand-section">
          <a href="index.php" class="brand-logo-container">
            <img alt="VOSTOKPRIBOR Official Mark" class="brand-logo-img"
              src="https://lh3.googleusercontent.com/aida/AEtjO1XMGDZ9meLzj0XkVJ6C4Xv2AoEzKtMyFqF7KQ8eMADmbywzsRZ7VF4Em6pQ7fZ8QRJExCZedCKEUUo1fN1LpEmGsQva25blyUsGhOPvX2vv2cHGkppzOp9iT33Xy2n4Nkr5e_YY_0J78vA8Q7vKUpViCouPJo13HFVvW5olf7QEFzU3EX-WCqc0SYyPXZb7E11PafOMC_KprpG6Tre6bN_DyZS-CI-J5qiCgBtikqVNYGueATNgWbi73Q" />
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

        <!-- Global Omni Search -->
        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="global-omni-search"
              placeholder="Search employee records, EMP-ID, clearance level, department..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <!-- Right System Metrics, Red Confidential Badge & Profile -->
        <div class="top-nav__actions">
          <!-- Small Red "Highly Confidential System" Badge -->
          <div class="confidential-system-pill"
            title="Restricted Personnel & Security Clearance System (GOST Class 1G)">
            <span>🔒</span>
            <span>HIGHLY CONFIDENTIAL SYSTEM</span>
          </div>

          <button class="icon-button" title="Personnel Telemetry Notifications"
            onclick="window.hrApp.showToast('Security Clearance Update', 'Level 4 Clearance ratified for Dr. Elena Rostova.')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
              <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
            <span class="badge-dot"></span>
          </button>

          <div class="top-user-profile"
            onclick="window.hrApp.showToast('Active User Session', 'Valeria Zaytseva · Chief Human Capital Officer · Level 4 Authorization')">
            <img
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM"
              alt="Valeria Zaytseva" class="user-avatar-top" />
            <div class="user-details-top">
              <span class="user-name-top">Valeria Zaytseva</span>
              <span class="user-role-top">Chief HR Officer · Level 4</span>
            </div>
          </div>
        </div>
      </div>
    </header>

    <div class="main-layout">
      <!-- ========================================================================
           LEFT SIDEBAR NAVIGATION (#0F2438 Navy + Plum Accent Active & Hover)
           ======================================================================== -->
      <aside class="sidebar">
        <div>
          <div class="sidebar-section-title">Human Resources</div>
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

            <!-- Screen 2: Employee Records -->
            <a href="EmployeeRecords.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                  </svg>
                </span>
                <span>Employee Records</span>
              </div>
              <span class="sidebar-badge">1,428</span>
            </a>

            <!-- Screen 3: Recruitment & Onboarding -->
            <a href="OnboardingTracker.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <polyline points="16 11 18 13 22 9" />
                  </svg>
                </span>
                <span>Recruitment &amp; Onboarding</span>
              </div>
              <span class="sidebar-badge">12</span>
            </a>

            <!-- Leave Management -->
            <a href="LeaveManagement.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                  </svg>
                </span>
                <span>Leave Management</span>
              </div>
              <span class="sidebar-badge badge-amber">19</span>
            </a>

            <!-- Org Structure -->
            <a href="OrgStructure.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="5" r="3" />
                    <circle cx="5" cy="19" r="3" />
                    <circle cx="19" cy="19" r="3" />
                    <path d="M12 8v4" />
                    <path d="M5 16v-2a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v2" />
                  </svg>
                </span>
                <span>Org Structure</span>
              </div>
              <span class="sidebar-badge">8</span>
            </a>

            <!-- Training -->
            <a href="Training.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                  </svg>
                </span>
                <span>Training &amp; Certs</span>
              </div>
              <span class="sidebar-badge">94%</span>
            </a>

            <!-- Offboarding -->
            <a href="Offboarding.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                    <polyline points="16 17 21 12 16 7" />
                    <line x1="21" y1="12" x2="9" y2="12" />
                  </svg>
                </span>
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
            <a href="login.php" class="sidebar-nav-item sidebar-nav-item--logout" id="btn-logout"
              onclick="(function(){sessionStorage.clear();localStorage.clear();})()">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                    <polyline points="16 17 21 12 16 7" />
                    <line x1="21" y1="12" x2="9" y2="12" />
                  </svg>
                </span>
                <span>Log Out</span>
              </div>
            </a>
          </nav>
        </div>

        <!-- Sidebar Bottom Clearance & Security Widget -->
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

      <!-- ========================================================================
           MAIN CONTENT AREA
           ======================================================================== -->
      <main class="content-wrapper">
        <div class="portal-container">
          <!-- Page Header -->
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <span>HR System</span>
                <span class="breadcrumb-separator">/</span>
                <span>Human Capital Directorate</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Executive Overview</span>
              </div>
              <h1 class="page-title">Human Capital &amp; Personnel Operations Dashboard</h1>
              <p class="page-subtitle">Workforce analytics, security clearance governance, and onboarding pipeline
                across 8 engineering divisions</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline"
                onclick="window.hrApp.showToast('Personnel Export', 'Workforce census generated with Rostrud &amp; GOST metadata.')">
                <span>📥 Export Census (.CSV)</span>
              </button>
              <a href="OnboardingTracker.php" class="btn btn-primary-amber">
                <span>+ Initiate Onboarding</span>
              </a>
            </div>
          </div>

          <!-- Top Row 4 KPI Cards -->
          <div class="kpi-grid">
            <!-- Card 1: Total Headcount -->
            <div class="hr-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Total Headcount</span>
                <div class="kpi-icon-pill plum">👥</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">1,428</span>
                <span style="font-size: 13px; color: var(--hr-text-muted); font-weight: 500;">Staff</span>
              </div>
              <div class="kpi-footer">
                <span>Full-time Engineers: 1,180</span>
                <span class="kpi-trend up">▲ +3.4% YoY</span>
              </div>
            </div>

            <!-- Card 2: Open Positions -->
            <div class="hr-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Open Positions</span>
                <div class="kpi-icon-pill steel">💼</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">46</span>
                <span style="font-size: 13px; color: var(--hr-text-muted); font-weight: 500;">Requisitions</span>
              </div>
              <div class="kpi-footer">
                <span>18 Optical &amp; SCADA Roles</span>
                <span class="kpi-trend up">Priority Hiring</span>
              </div>
            </div>

            <!-- Card 3: Pending Onboarding -->
            <div class="hr-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Pending Onboarding</span>
                <div class="kpi-icon-pill amber">⚡</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">12</span>
                <span style="font-size: 13px; color: var(--hr-text-muted); font-weight: 500;">New Hires</span>
              </div>
              <div class="kpi-footer">
                <span>4 Security Gating In Progress</span>
                <span class="kpi-trend alert">Action Required</span>
              </div>
            </div>

            <!-- Card 4: Leave Requests -->
            <div class="hr-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Leave Awaiting Approval</span>
                <div class="kpi-icon-pill red">📅</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">19</span>
                <span style="font-size: 13px; color: var(--hr-text-muted); font-weight: 500;">Requests</span>
              </div>
              <div class="kpi-footer">
                <span>5 Scheduled next week</span>
                <span class="kpi-trend up">SLA: &lt;24h</span>
              </div>
            </div>
          </div>

          <!-- Horizontal Bar Chart: Headcount by Department (8 Bars) -->
          <div class="hr-card" style="margin-bottom: 1.5rem;">
            <div class="card-header-row">
              <div>
                <h3 class="card-title">Headcount Distribution by Engineering Division</h3>
                <p style="font-size: 11.5px; color: var(--hr-text-secondary); margin-top: 2px;">
                  Active staff across 8 specialized divisions and industrial research laboratories
                </p>
              </div>
              <a href="EmployeeRecords.php" class="btn btn-plum btn-sm">
                <span>View Full Employee Ledger (1,428) →</span>
              </a>
            </div>

            <div class="dept-chart-container">
              <!-- Bar 1: Optical Sensors Engineering -->
              <div class="dept-bar-row">
                <div class="dept-bar-label">
                  <span>🔬</span>
                  <span>Optical Sensors Engineering</span>
                </div>
                <div class="dept-bar-track">
                  <div class="dept-bar-fill" style="width: 100%;"></div>
                </div>
                <div class="dept-bar-val">342 Staff</div>
              </div>

              <!-- Bar 2: SCADA & Automation -->
              <div class="dept-bar-row">
                <div class="dept-bar-label">
                  <span>⚙️</span>
                  <span>SCADA &amp; Automation</span>
                </div>
                <div class="dept-bar-track">
                  <div class="dept-bar-fill" style="width: 83%;"></div>
                </div>
                <div class="dept-bar-val">284 Staff</div>
              </div>

              <!-- Bar 3: Blast Furnace Robotics -->
              <div class="dept-bar-row">
                <div class="dept-bar-label">
                  <span>🏭</span>
                  <span>Blast Furnace Robotics</span>
                </div>
                <div class="dept-bar-track">
                  <div class="dept-bar-fill" style="width: 58%;"></div>
                </div>
                <div class="dept-bar-val">198 Staff</div>
              </div>

              <!-- Bar 4: R&D Labs & Sensor Fabrication -->
              <div class="dept-bar-row">
                <div class="dept-bar-label">
                  <span>🧪</span>
                  <span>R&amp;D Labs &amp; Sensor Fab</span>
                </div>
                <div class="dept-bar-track">
                  <div class="dept-bar-fill" style="width: 48%;"></div>
                </div>
                <div class="dept-bar-val">165 Staff</div>
              </div>

              <!-- Bar 5: Quality & FAT Testing -->
              <div class="dept-bar-row">
                <div class="dept-bar-label">
                  <span>✓</span>
                  <span>Quality &amp; FAT Testing</span>
                </div>
                <div class="dept-bar-track">
                  <div class="dept-bar-fill" style="width: 41.5%;"></div>
                </div>
                <div class="dept-bar-val">142 Staff</div>
              </div>

              <!-- Bar 6: Field Operations & Metallurgy -->
              <div class="dept-bar-row">
                <div class="dept-bar-label">
                  <span>⚡</span>
                  <span>Field Operations &amp; Metallurgy</span>
                </div>
                <div class="dept-bar-track">
                  <div class="dept-bar-fill" style="width: 37.4%;"></div>
                </div>
                <div class="dept-bar-val">128 Staff</div>
              </div>

              <!-- Bar 7: Procurement & Logistics -->
              <div class="dept-bar-row">
                <div class="dept-bar-label">
                  <span>📦</span>
                  <span>Procurement &amp; Logistics</span>
                </div>
                <div class="dept-bar-track">
                  <div class="dept-bar-fill" style="width: 27.5%;"></div>
                </div>
                <div class="dept-bar-val">94 Staff</div>
              </div>

              <!-- Bar 8: Corporate & Legal Governance -->
              <div class="dept-bar-row">
                <div class="dept-bar-label">
                  <span>⚖️</span>
                  <span>Corporate &amp; Legal Governance</span>
                </div>
                <div class="dept-bar-track">
                  <div class="dept-bar-fill" style="width: 21.9%;"></div>
                </div>
                <div class="dept-bar-val">75 Staff</div>
              </div>
            </div>
          </div>

          <!-- Action Needed List Widget (IT Provisioning & Offboarding) -->
          <div class="hr-card">
            <div class="card-header-row">
              <div>
                <h3 class="card-title">Priority Actions &amp; Security Compliance Queue</h3>
                <p style="font-size: 11.5px; color: var(--hr-text-secondary); margin-top: 2px;">
                  Pending IT provisioning clearances, security credential audits, and scheduled offboarding revocations
                </p>
              </div>
              <span class="confidential-system-pill" style="font-size: 9.5px; padding: 2px 6px;">
                SECURITY SLA: MANDATORY 24H
              </span>
            </div>

            <div class="action-needed-list">
              <!-- Item 1: IT Provisioning for Onboarding Candidate -->
              <div class="action-item action-it">
                <div class="action-item-left">
                  <div class="action-badge-icon"
                    style="background: var(--hr-warning-light); color: var(--hr-amber-hover);">💻</div>
                  <div>
                    <div class="action-item-title">Pending IT SCADA Provisioning · Dr. Mikhail Abramov</div>
                    <div class="action-item-desc">Issue VPN token &amp; Intranet credentials for R&amp;D Sensor Fab Lab
                      (EMP-VP-0492 · Level 3 Clearance).</div>
                  </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                  <span
                    style="font-family: var(--hr-font-mono); font-size: 11px; color: var(--hr-amber-hover); font-weight: 700;">Stage
                    6 / 8</span>
                  <a href="OnboardingTracker.php" class="btn btn-primary-amber btn-sm">Open Tracker →</a>
                </div>
              </div>

              <!-- Item 2: Offboarding IT Revocation -->
              <div class="action-item action-offboarding">
                <div class="action-item-left">
                  <div class="action-badge-icon"
                    style="background: var(--hr-confidential-bg); color: var(--hr-confidential);">🔒</div>
                  <div>
                    <div class="action-item-title">Security Clearance Revocation &amp; IT Lock · Boris Kamenev</div>
                    <div class="action-item-desc">Scheduled Offboarding: Revoke Level 3 SCADA tokens, retrieve
                      cryptographic smartcard, archive NDA.</div>
                  </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                  <span
                    style="font-family: var(--hr-font-mono); font-size: 11px; color: var(--hr-confidential); font-weight: 700;">Final
                    Day: Nov 15</span>
                  <a href="Offboarding.php" class="btn btn-outline btn-sm">Process Revocation →</a>
                </div>
              </div>

              <!-- Item 3: Level 4 Executive Recertification -->
              <div class="action-item">
                <div class="action-item-left">
                  <div class="action-badge-icon" style="background: var(--hr-plum-light); color: var(--hr-plum);">🔏
                  </div>
                  <div>
                    <div class="action-item-title">Annual GOST Security Clearance Audit · 4 Executive Engineers</div>
                    <div class="action-item-desc">Dr. Elena Rostova, Mikhail Sorokin, Yury Vasiliev, and Valeria
                      Zaytseva biometric key renewals.</div>
                  </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                  <span
                    style="font-family: var(--hr-font-mono); font-size: 11px; color: var(--hr-plum); font-weight: 700;">Due
                    Nov 30</span>
                  <button class="btn btn-outline btn-sm"
                    onclick="window.hrApp.showToast('Audit Triggered', 'Automated security renewal notices sent.')">Review
                    Dossier</button>
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