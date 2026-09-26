<?php

/**
 * VOSTOKPRIBOR HR System - Dashboard
 * Dynamic, database-driven Human Capital Operations overview.
 */
require_once __DIR__ . '/hr_service.php';
requireAuth('HR');

$currUser  = hr_getCurrentUser();
$metrics   = hr_getDashboardMetrics();
$canManage = hr_canManageHR();

// Calculate initials for profile badge
$nameParts = explode(' ', trim($currUser['full_name']));
$initials  = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR HR System · Human Capital Operations</title>
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
                <span class="hr-opacity-50" >|</span>
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

          <button class="icon-button" title="Pending Leave Requests" onclick="window.location.href='LeaveManagement.php'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
              <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
            <?php if ($metrics['pending_leaves'] > 0): ?>
              <span class="badge-dot hr-badge-dot-rose" ></span>
            <?php endif; ?>
          </button>

          <!-- Dynamic Active User Profile -->
          <div class="top-user-profile" title="Active Session: <?= htmlspecialchars($currUser['full_name']) ?> (Clearance: <?= htmlspecialchars($currUser['clearance_level'] ?? 'L1') ?>)">
            <div class="hr-avatar-circle-glow" >
              <?= $initials ?>
            </div>
            <div class="user-details-top">
              <span class="user-name-top"><?= htmlspecialchars($currUser['full_name']) ?></span>
              <span class="user-role-top"><?= htmlspecialchars($currUser['role_name'] ?? 'Authorized User') ?> · <?= htmlspecialchars($currUser['clearance_level'] ?? 'L1') ?></span>
            </div>
          </div>

          <!-- Sign Out -->
          <a href="../api/logout.php?system=HR%20System&redirect=../HR%20System/login.php" class="top-signout-btn" title="Sign Out of HR System" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" >
            <span class="material-symbols-outlined">logout</span>
            <span>Sign Out</span>
          </a>
        </div>
      </div>
    </header>

    <div class="main-layout">
      <!-- LEFT SIDEBAR NAVIGATION -->
      <aside class="sidebar">
        <div>
          <div class="sidebar-section-title">Human Resources</div>
          <nav class="sidebar-nav">
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
              <span class="sidebar-pill"><?= $metrics['active_headcount'] ?></span>
            </a>

            <a href="OnboardingTracker.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <line x1="19" y1="8" x2="19" y2="14" />
                    <line x1="22" y1="11" x2="16" y2="11" />
                  </svg>
                </span>
                <span>Onboarding Pipeline</span>
              </div>
              <?php if ($metrics['active_onboarding'] > 0): ?>
                <span class="sidebar-pill amber"><?= $metrics['active_onboarding'] ?></span>
              <?php endif; ?>
            </a>

            <a href="Offboarding.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <line x1="17" y1="11" x2="23" y2="11" />
                  </svg>
                </span>
                <span>Offboarding &amp; Revocation</span>
              </div>
              <?php if ($metrics['active_offboarding'] > 0): ?>
                <span class="sidebar-pill alert"><?= $metrics['active_offboarding'] ?></span>
              <?php endif; ?>
            </a>

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
              <?php if ($metrics['pending_leaves'] > 0): ?>
                <span class="sidebar-pill alert"><?= $metrics['pending_leaves'] ?></span>
              <?php endif; ?>
            </a>

            <a href="OrgStructure.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="8.5" y="14" width="7" height="7" />
                    <line x1="6.5" y1="10" x2="6.5" y2="12" />
                    <line x1="17.5" y1="10" x2="17.5" y2="12" />
                    <line x1="6.5" y1="12" x2="17.5" y2="12" />
                    <line x1="12" y1="12" x2="12" y2="14" />
                  </svg>
                </span>
                <span>Org Hierarchy</span>
              </div>
            </a>

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
            </a>
            <a href="Integrations.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00E5FF" stroke-width="2">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                  </svg></span><span class="hr-nav-integrations" >System Integrations</span></div><span class="sidebar-badge hr-badge-integrations" >SYS08</span>
            </a>
          </nav>

          <div class="sidebar-section-title hr-mt-6" >Ecosystem Gateways</div>
          <nav class="sidebar-nav">
            <a href="../Admin & Governance Portal/index.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">🛡️</span>
                <span>Admin &amp; Governance</span>
              </div>
            </a>
            <a href="../Employee Intranet/index.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">🏢</span>
                <span>Employee Intranet</span>
              </div>
            </a>
            <a href="../index.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">🌐</span>
                <span>Ecosystem Launchpad</span>
              </div>
            </a>
          </nav>
        </div>

        <!-- Clearance & Security Widget -->
        <div class="sidebar-footer">
          <div class="security-widget-card">
            <div class="security-widget-header">
              <span>Security Clearance Registry</span>
              <span class="security-badge-status">● GOST 1G</span>
            </div>
            <div class="hr-text-inverse-muted-sm" >
              Executive L4 Clearances: <strong><?= $metrics['clearance_counts']['L4'] ?> Vetted</strong>
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
                <span>HR System</span>
                <span class="breadcrumb-separator">/</span>
                <span>Human Capital Directorate</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Executive Overview</span>
              </div>
              <h1 class="page-title">Human Capital &amp; Personnel Operations Dashboard</h1>
              <p class="page-subtitle">Live workforce metrics, security clearance governance, and onboarding pipeline from MySQL database</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.hrApp.showToast('Personnel Export', 'Workforce census generated with Rostrud &amp; GOST metadata.')">
                <span>📥 Export Census (.CSV)</span>
              </button>
              <?php if ($canManage): ?>
                <a href="EmployeeRecords.php?openAdd=1" class="btn btn-primary-amber">
                  <span>+ Register Employee</span>
                </a>
              <?php endif; ?>
            </div>
          </div>

          <!-- Top Row 4 KPI Cards (LIVE DATABASE DRIVEN) -->
          <div class="kpi-grid">
            <!-- Card 1: Total Headcount -->
            <div class="hr-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Active Headcount</span>
                <div class="kpi-icon-pill plum">👥</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= number_format($metrics['active_headcount']) ?></span>
                <span class="hr-text-muted-500" >Staff</span>
              </div>
              <div class="kpi-footer">
                <span>Top Secret L4: <?= $metrics['clearance_counts']['L4'] ?></span>
                <span class="kpi-trend up">● Active Database</span>
              </div>
            </div>

            <!-- Card 2: Security Clearance Tiers -->
            <div class="hr-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Clearance Distribution</span>
                <div class="kpi-icon-pill steel">🔒</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono hr-text-20" >
                  <span class="hr-text-red" >L4: <?= $metrics['clearance_counts']['L4'] ?></span> |
                  <span class="hr-text-plum" >L3: <?= $metrics['clearance_counts']['L3'] ?></span>
                </span>
              </div>
              <div class="kpi-footer">
                <span>L2: <?= $metrics['clearance_counts']['L2'] ?> · L1: <?= $metrics['clearance_counts']['L1'] ?></span>
                <span class="kpi-trend up">SCADA Gated</span>
              </div>
            </div>

            <!-- Card 3: Pending Onboarding -->
            <div class="hr-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Active Onboarding</span>
                <div class="kpi-icon-pill amber">⚡</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= $metrics['active_onboarding'] ?></span>
                <span class="hr-text-muted-500" >Pipelines</span>
              </div>
              <div class="kpi-footer">
                <span>Sequential Stage Gating</span>
                <span class="kpi-trend <?= $metrics['active_onboarding'] > 0 ? 'alert' : 'up' ?>">
                  <?= $metrics['active_onboarding'] > 0 ? 'In Progress' : 'Clean' ?>
                </span>
              </div>
            </div>

            <!-- Card 4: Leave Requests -->
            <div class="hr-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Leave Awaiting Approval</span>
                <div class="kpi-icon-pill red">📅</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= $metrics['pending_leaves'] ?></span>
                <span class="hr-text-muted-500" >Requests</span>
              </div>
              <div class="kpi-footer">
                <span>Offboarding Cases: <?= $metrics['active_offboarding'] ?></span>
                <span class="kpi-trend <?= $metrics['pending_leaves'] > 0 ? 'alert' : 'up' ?>">SLA: &lt;24h</span>
              </div>
            </div>
          </div>

          <!-- Horizontal Bar Chart: Headcount by Department (LIVE SQL DATA) -->
          <div class="hr-card hr-mb-6" >
            <div class="card-header-row">
              <div>
                <h3 class="card-title">Live Headcount by Enterprise Department</h3>
                <p class="hr-meta-subtext" >
                  Active staff queried from MySQL across all registered operational divisions
                </p>
              </div>
              <a href="EmployeeRecords.php" class="btn btn-plum btn-sm">
                <span>View Full Employee Ledger (<?= $metrics['active_headcount'] ?>) →</span>
              </a>
            </div>

            <div class="dept-chart-container">
              <?php
              $maxStaff = 1;
              foreach ($metrics['dept_distribution'] as $dd) {
                if ($dd['current'] > $maxStaff) $maxStaff = $dd['current'];
              }
              $deptIcons = [
                'ENG' => '⚙️',
                'EXE' => '🏛️',
                'FIN' => '💳',
                'GOV' => '⚖️',
                'HRA' => '👥',
                'ITD' => '💻',
                'OPS' => '📦',
                'SAL' => '💼'
              ];
              foreach ($metrics['dept_distribution'] as $d):
                $pct = round(($d['current'] / $maxStaff) * 100);
                $icon = $deptIcons[$d['code']] ?? '🏢';
              ?>
                <div class="dept-bar-row">
                  <div class="dept-bar-label">
                    <span><?= $icon ?></span>
                    <span><?= htmlspecialchars($d['name']) ?> (<?= htmlspecialchars($d['code']) ?>)</span>
                  </div>
                  <div class="dept-bar-track">
                    <div class="dept-bar-fill" style="width: <?= max(5, $pct) ?>%;"></div>
                  </div>
                  <div class="dept-bar-val"><?= $d['current'] ?> Staff</div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Priority Actions & Security Compliance Queue (LIVE DATABASE DRIVEN) -->
          <div class="hr-card">
            <div class="card-header-row">
              <div>
                <h3 class="card-title">Priority Personnel Actions &amp; Security Compliance Queue</h3>
                <p class="hr-meta-subtext" >
                  Recent personnel records, pending onboarding clearances, and active offboarding revocations
                </p>
              </div>
              <span class="confidential-system-pill hr-pill-mini" >
                SECURITY SLA: MANDATORY 24H
              </span>
            </div>

            <div class="action-needed-list">
              <!-- Item 1: Active Onboarding Candidate -->
              <div class="action-item action-it">
                <div class="action-item-left">
                  <div class="action-badge-icon hr-badge-warning" >💻</div>
                  <div>
                    <div class="action-item-title">Onboarding Pipeline Active · <?= $metrics['active_onboarding'] ?> Candidate(s)</div>
                    <div class="action-item-desc">Sequential provisioning, cryptographic token assignment, and security clearance gating.</div>
                  </div>
                </div>
                <div class="hr-flex-gap-md" >
                  <a href="OnboardingTracker.php" class="btn btn-primary-amber btn-sm">Open Onboarding Tracker →</a>
                </div>
              </div>

              <!-- Item 2: Offboarding Status -->
              <div class="action-item action-offboarding">
                <div class="action-item-left">
                  <div class="action-badge-icon hr-badge-confidential" >🔒</div>
                  <div>
                    <div class="action-item-title">Security Clearance Revocation &amp; IT Lock · <?= $metrics['active_offboarding'] ?> Case(s)</div>
                    <div class="action-item-desc">Revoke SCADA tokens, retrieve cryptographic smartcards, and archive NDA records.</div>
                  </div>
                </div>
                <div class="hr-flex-gap-md" >
                  <a href="Offboarding.php" class="btn btn-outline btn-sm">Process Revocation →</a>
                </div>
              </div>

              <!-- Item 3: Pending Leaves -->
              <?php if ($metrics['pending_leaves'] > 0): ?>
                <div class="action-item">
                  <div class="action-item-left">
                    <div class="action-badge-icon hr-badge-plum" >📅</div>
                    <div>
                      <div class="action-item-title">Pending Leave Requests · <?= $metrics['pending_leaves'] ?> Submitted</div>
                      <div class="action-item-desc">Operational personnel requests awaiting department director approval.</div>
                    </div>
                  </div>
                  <div class="hr-flex-gap-md" >
                    <a href="LeaveManagement.php" class="btn btn-plum btn-sm">Review Leave Queue →</a>
                  </div>
                </div>
              <?php endif; ?>
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