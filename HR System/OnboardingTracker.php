<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR HR System · Recruitment &amp; Onboarding Checklist Tracker</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <div class="app-container">
    <!-- ========================================================================
         TOP NAVIGATION BAR (#0F2438 Navy + Plum Accent Stripe + Confidential Badge)
         ======================================================================== -->
    <header class="top-nav">
      <div class="top-nav__accent-stripe"></div>

      <div class="top-nav__content">
        <!-- Brand & System Identifier -->
        <div class="brand-section">
          <a href="Dashboard.php" class="brand-logo-container">
            <img alt="VOSTOKPRIBOR Official Mark" class="brand-logo-img" src="https://lh3.googleusercontent.com/aida/AEtjO1XMGDZ9meLzj0XkVJ6C4Xv2AoEzKtMyFqF7KQ8eMADmbywzsRZ7VF4Em6pQ7fZ8QRJExCZedCKEUUo1fN1LpEmGsQva25blyUsGhOPvX2vv2cHGkppzOp9iT33Xy2n4Nkr5e_YY_0J78vA8Q7vKUpViCouPJo13HFVvW5olf7QEFzU3EX-WCqc0SYyPXZb7E11PafOMC_KprpG6Tre6bN_DyZS-CI-J5qiCgBtikqVNYGueATNgWbi73Q" />
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
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search employee records, EMP-ID, clearance level, department..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <!-- Right System Metrics, Red Confidential Badge & Profile -->
        <div class="top-nav__actions">
          <div class="confidential-system-pill" title="Restricted Personnel & Security Clearance System (GOST Class 1G)">
            <span>🔒</span>
            <span>HIGHLY CONFIDENTIAL SYSTEM</span>
          </div>

          <button class="icon-button" title="Personnel Telemetry Notifications" onclick="window.hrApp.showToast('Security Clearance Update', 'Level 4 Clearance ratified for Dr. Elena Rostova.')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="badge-dot"></span>
          </button>

          <div class="top-user-profile" onclick="window.hrApp.showToast('Active User Session', 'Valeria Zaytseva · Chief Human Capital Officer · Level 4 Authorization')">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM" alt="Valeria Zaytseva" class="user-avatar-top" />
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
           LEFT SIDEBAR NAVIGATION (#0F2438 Navy + Plum Accent)
           ======================================================================== -->
      <aside class="sidebar">
        <div>
          <div class="sidebar-section-title">Human Resources</div>
          <nav class="sidebar-nav">
            <!-- Screen 1: Dashboard -->
            <a href="Dashboard.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                </span>
                <span>Dashboard</span>
              </div>
            </a>

            <!-- Screen 2: Employee Records -->
            <a href="EmployeeRecords.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </span>
                <span>Employee Records</span>
              </div>
              <span class="sidebar-badge">1,428</span>
            </a>

            <!-- Screen 3: Recruitment & Onboarding (Active) -->
            <a href="OnboardingTracker.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
                </span>
                <span>Recruitment &amp; Onboarding</span>
              </div>
              <span class="sidebar-badge badge-amber">12</span>
            </a>

            <!-- Leave Management -->
            <a href="LeaveManagement.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </span>
                <span>Leave Management</span>
              </div>
              <span class="sidebar-badge badge-amber">19</span>
            </a>

            <!-- Org Structure -->
            <a href="OrgStructure.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="5" r="3"/><circle cx="5" cy="19" r="3"/><circle cx="19" cy="19" r="3"/><path d="M12 8v4"/><path d="M5 16v-2a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v2"/></svg>
                </span>
                <span>Org Structure</span>
              </div>
              <span class="sidebar-badge">8</span>
            </a>

            <!-- Training -->
            <a href="Training.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                </span>
                <span>Training &amp; Certs</span>
              </div>
              <span class="sidebar-badge">94%</span>
            </a>

            <!-- Offboarding -->
            <a href="Offboarding.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
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

      <!-- ========================================================================
           MAIN CONTENT AREA: SCREEN 3 ONBOARDING CHECKLIST TRACKER
           ======================================================================== -->
      <main class="content-wrapper">
        <div class="portal-container" style="max-width: 1200px;">
          <!-- Page Header -->
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <a href="Dashboard.php">HR System</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Recruitment &amp; Onboarding Tracker</span>
              </div>
              <h1 class="page-title">Candidate Onboarding &amp; Security Clearance Gating</h1>
              <p class="page-subtitle">Track sequential provisioning, cryptographic token assignment, and compliance gating for incoming engineering personnel</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.hrApp.showToast('Protocol Verification', 'Automated check completed: All 5 prior stages cryptographic signatures valid.')">
                <span>🛡️ Verify Signatures</span>
              </button>
              <button class="btn btn-primary-amber" onclick="window.hrApp.showToast('Candidate Added', 'New hire pipeline dossier initialized.')">
                <span>+ New Onboarding Pipeline</span>
              </button>
            </div>
          </div>

          <!-- Active Onboarding Candidate Switcher Tabs -->
          <div style="display: flex; gap: 0.75rem; margin-bottom: 1.25rem; overflow-x: auto; padding-bottom: 4px;">
            <button class="btn" style="background: var(--hr-plum); color: #FFF; border: 1px solid var(--hr-plum); font-size: 12px; box-shadow: 0 2px 6px var(--hr-plum-glow);">
              <span>● Dr. Mikhail Abramov (Stage 6/8 · R&amp;D Fab)</span>
            </button>
            <button class="btn btn-outline" style="font-size: 12px;" onclick="window.hrApp.showToast('Candidate Selected', 'Switching view to Ilya Morozov (Stage 4/8 - SCADA Specialist)')">
              <span>○ Ilya Morozov (Stage 4/8 · SCADA)</span>
            </button>
            <button class="btn btn-outline" style="font-size: 12px;" onclick="window.hrApp.showToast('Candidate Selected', 'Switching view to Polina Volkova (Stage 2/8 - Optical Metrology)')">
              <span>○ Polina Volkova (Stage 2/8 · Optics)</span>
            </button>
            <button class="btn btn-outline" style="font-size: 12px;" onclick="window.hrApp.showToast('Candidate Selected', 'Switching view to Alexey Smirnov (Stage 7/8 - Robotics)')">
              <span>○ Alexey Smirnov (Stage 7/8 · Robotics)</span>
            </button>
          </div>

          <!-- Candidate Header Component -->
          <div class="onboarding-candidate-card">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
              <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" alt="Candidate Photo" style="width: 64px; height: 64px; border-radius: var(--hr-radius-md); object-fit: cover; border: 2px solid var(--hr-plum);" />
              <div>
                <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap; margin-bottom: 0.25rem;">
                  <h2 style="font-size: 18px; font-weight: 700; color: var(--hr-navy);">Dr. Mikhail Abramov</h2>
                  <span class="status-pill status-probation">Onboarding In Progress</span>
                  <span class="clearance-badge clearance-l3">
                    <span>⚡</span> Level 3 · Secret SCADA
                  </span>
                </div>
                <div style="font-size: 13px; color: var(--hr-text-secondary); font-weight: 500;">
                  Principal Semiconductor Physicist · <strong style="color: var(--hr-navy);">R&amp;D Labs &amp; Sensor Fabrication</strong>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem; margin-top: 0.4rem; font-size: 11.5px; color: var(--hr-text-muted);">
                  <span>EMP ID: <strong style="color: var(--hr-navy); font-family: var(--hr-font-mono);">EMP-VP-0492</strong></span>
                  <span>Supervisor: <strong style="color: var(--hr-navy);">Dr. Elena Rostova</strong></span>
                  <span>Target Start: <strong style="color: var(--hr-navy);">Nov 18, 2024</strong></span>
                </div>
              </div>
            </div>

            <!-- Progress Metric Box -->
            <div style="text-align: right; min-width: 180px;">
              <div style="font-size: 11px; text-transform: uppercase; color: var(--hr-text-muted); font-weight: 600; letter-spacing: 0.04em;">Onboarding Completion</div>
              <div style="font-size: 24px; font-weight: 700; color: var(--hr-plum); font-family: var(--hr-font-mono);">75% · 6/8</div>
              <div style="width: 100%; height: 8px; background: var(--hr-surface-dim); border-radius: 4px; overflow: hidden; margin-top: 6px;">
                <div style="width: 75%; height: 100%; background: linear-gradient(90deg, #7B2CBF, #9D4EDD); border-radius: 4px;"></div>
              </div>
            </div>
          </div>

          <!-- Vertical Step-Tracker / Checklist Component (8 Stages) -->
          <div class="hr-card" style="padding: 1.75rem 2rem;">
            <div class="card-header-row" style="margin-bottom: 1.5rem;">
              <div>
                <h3 class="card-title">Sequential Onboarding &amp; Security Provisioning Workflow</h3>
                <p style="font-size: 11.5px; color: var(--hr-text-secondary); margin-top: 2px;">
                  Click any stage node or trigger to update verification state and log authorization timestamp
                </p>
              </div>
              <span class="confidential-system-pill" style="font-size: 9px; padding: 2px 6px;">
                GOST R 34.10 COMPLIANT
              </span>
            </div>

            <!-- Step Tracker List -->
            <div class="step-tracker-container">
              
              <!-- STAGE 1: Employee Record Created -->
              <div class="step-item completed" id="step-stage-1">
                <div class="step-node-icon" onclick="window.hrApp.toggleOnboardingStep('step-stage-1')" title="Toggle State">✓</div>
                <div class="step-item-card">
                  <div class="step-item-header">
                    <div>
                      <div class="step-title">Stage 1: Employee Record Created</div>
                      <div class="step-subtitle">Candidate dossier initialized in central personnel ledger with national passport &amp; academic verification.</div>
                    </div>
                    <span class="step-badge-status status-completed">COMPLETED</span>
                  </div>
                  <div class="step-meta-row">
                    <span>Officer: <strong>V. Zaytseva (HR Director)</strong></span>
                    <span>•</span>
                    <span>Timestamp: <strong>Oct 15, 2024 · 09:30 MSK</strong></span>
                    <span>•</span>
                    <span>Ledger Hash: <code style="font-family: var(--hr-font-mono); font-size: 10px; color: var(--hr-plum);">#8F92A1</code></span>
                  </div>
                </div>
              </div>

              <!-- STAGE 2: EMP-ID Issued -->
              <div class="step-item completed" id="step-stage-2">
                <div class="step-node-icon" onclick="window.hrApp.toggleOnboardingStep('step-stage-2')" title="Toggle State">✓</div>
                <div class="step-item-card">
                  <div class="step-item-header">
                    <div>
                      <div class="step-title">Stage 2: EMP-ID Issued</div>
                      <div class="step-subtitle">Enterprise serial identifier allocated: <strong>EMP-VP-0492</strong> registered across SCADA identity providers.</div>
                    </div>
                    <span class="step-badge-status status-completed">COMPLETED</span>
                  </div>
                  <div class="step-meta-row">
                    <span>Officer: <strong>Automated System Daemon</strong></span>
                    <span>•</span>
                    <span>Timestamp: <strong>Oct 15, 2024 · 09:31 MSK</strong></span>
                    <span>•</span>
                    <span>Allocated ID: <strong style="color: var(--hr-navy); font-family: var(--hr-font-mono);">EMP-VP-0492</strong></span>
                  </div>
                </div>
              </div>

              <!-- STAGE 3: Department Assigned -->
              <div class="step-item completed" id="step-stage-3">
                <div class="step-node-icon" onclick="window.hrApp.toggleOnboardingStep('step-stage-3')" title="Toggle State">✓</div>
                <div class="step-item-card">
                  <div class="step-item-header">
                    <div>
                      <div class="step-title">Stage 3: Department Assigned</div>
                      <div class="step-subtitle">Assigned to <strong>R&amp;D Labs &amp; Sensor Fabrication</strong> division under supervising director Dr. Elena Rostova.</div>
                    </div>
                    <span class="step-badge-status status-completed">COMPLETED</span>
                  </div>
                  <div class="step-meta-row">
                    <span>Division: <strong>R&amp;D Labs &amp; Sensor Fabrication</strong></span>
                    <span>•</span>
                    <span>Cost Center: <strong>CC-804 (Sensors R&amp;D)</strong></span>
                    <span>•</span>
                    <span>Facility Bay: <strong>Nanofabrication Facility Bay B</strong></span>
                  </div>
                </div>
              </div>

              <!-- STAGE 4: IT Access Request Sent -->
              <div class="step-item completed" id="step-stage-4">
                <div class="step-node-icon" onclick="window.hrApp.toggleOnboardingStep('step-stage-4')" title="Toggle State">✓</div>
                <div class="step-item-card">
                  <div class="step-item-header">
                    <div>
                      <div class="step-title">Stage 4: IT Access Request Sent</div>
                      <div class="step-subtitle">Formal ticket #IT-REQ-9941 generated for workstation build, cryptographic token, and smartcard badge.</div>
                    </div>
                    <span class="step-badge-status status-completed">COMPLETED</span>
                  </div>
                  <div class="step-meta-row">
                    <span>Ticket: <strong>#IT-REQ-9941</strong></span>
                    <span>•</span>
                    <span>IT Queue: <strong>Hardware &amp; Cryptographic Keys</strong></span>
                    <span>•</span>
                    <span>Assigned Engineer: <strong>Alexey Ivanov</strong></span>
                  </div>
                </div>
              </div>

              <!-- STAGE 5: Intranet Access Created -->
              <div class="step-item completed" id="step-stage-5">
                <div class="step-node-icon" onclick="window.hrApp.toggleOnboardingStep('step-stage-5')" title="Toggle State">✓</div>
                <div class="step-item-card">
                  <div class="step-item-header">
                    <div>
                      <div class="step-title">Stage 5: Intranet Access Created</div>
                      <div class="step-subtitle">Single-Sign-On LDAP mailbox (<code>m.abramov@vostokpribor.local</code>) and Employee Intranet portal access active.</div>
                    </div>
                    <span class="step-badge-status status-completed">COMPLETED</span>
                  </div>
                  <div class="step-meta-row">
                    <span>LDAP Username: <strong>m.abramov</strong></span>
                    <span>•</span>
                    <span>Intranet Role: <strong>R&amp;D Physicist</strong></span>
                    <span>•</span>
                    <span>Mailbox: <strong>Active (Quota: 50 GB)</strong></span>
                  </div>
                </div>
              </div>

              <!-- STAGE 6: System Permissions Granted (In Progress) -->
              <div class="step-item in-progress" id="step-stage-6">
                <div class="step-node-icon" onclick="window.hrApp.toggleOnboardingStep('step-stage-6')" title="Click to Complete Stage">⚡</div>
                <div class="step-item-card">
                  <div class="step-item-header">
                    <div>
                      <div class="step-title" style="color: var(--hr-amber-hover);">Stage 6: System Permissions Granted</div>
                      <div class="step-subtitle">Provisioning Level 3 SCADA telemetry access tokens, Cleanroom Bay B physical biometric lock pass.</div>
                    </div>
                    <span class="step-badge-status status-in-progress">IN PROGRESS · ACTION NEEDED</span>
                  </div>
                  <div class="step-meta-row">
                    <span>Current Gating: <strong>Security Token Serialization Pending</strong></span>
                    <span>•</span>
                    <span>SLA: <strong>&lt; 6 Hours Remaining</strong></span>
                  </div>
                  <div style="margin-top: 0.75rem; display: flex; gap: 0.5rem;">
                    <button class="btn btn-primary-amber btn-sm" onclick="window.hrApp.toggleOnboardingStep('step-stage-6')">
                      <span>✓ Authorize Level 3 SCADA Token</span>
                    </button>
                    <button class="btn btn-outline btn-sm" onclick="window.hrApp.showToast('Security Alert Sent', 'Urgent ping dispatched to Security Chief S. Kirov.')">
                      <span>⚠️ Expedite Security Review</span>
                    </button>
                  </div>
                </div>
              </div>

              <!-- STAGE 7: Documents Stored (Pending) -->
              <div class="step-item pending" id="step-stage-7">
                <div class="step-node-icon" onclick="window.hrApp.toggleOnboardingStep('step-stage-7')" title="Click to Advance State">7</div>
                <div class="step-item-card">
                  <div class="step-item-header">
                    <div>
                      <div class="step-title">Stage 7: Documents Stored</div>
                      <div class="step-subtitle">Deposit signed Labor Contract, Special NDA (GOST R 34.10), and Cleanroom Safety Pass into encrypted archive.</div>
                    </div>
                    <span class="step-badge-status status-pending">PENDING STAGE 6</span>
                  </div>
                  <div class="step-meta-row">
                    <span>Documents Awaiting Archive: <strong>Labor Agreement, GOST NDA, IP Assignment</strong></span>
                  </div>
                </div>
              </div>

              <!-- STAGE 8: Governance Review Complete (Pending) -->
              <div class="step-item pending" id="step-stage-8">
                <div class="step-node-icon" onclick="window.hrApp.toggleOnboardingStep('step-stage-8')" title="Click to Advance State">8</div>
                <div class="step-item-card">
                  <div class="step-item-header">
                    <div>
                      <div class="step-title">Stage 8: Governance Review Complete</div>
                      <div class="step-subtitle">Final compliance sign-off by Chief HR Officer &amp; Legal Directorate. Candidate converted to Active Staff status.</div>
                    </div>
                    <span class="step-badge-status status-pending">FINAL GATING</span>
                  </div>
                  <div class="step-meta-row">
                    <span>Final Approver: <strong>Valeria Zaytseva (Chief HR Officer)</strong></span>
                  </div>
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
