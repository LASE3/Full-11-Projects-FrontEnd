<?php

/**
 * VOSTOKPRIBOR HR System - Organizational Structure & Hierarchy
 * Database-driven corporate division mapping and reporting lines from MySQL.
 */
require_once __DIR__ . '/hr_service.php';
requireAuth('HR');

$currUser    = hr_getCurrentUser();
$departments = hr_getDepartments();
$employees   = hr_getOrgStructure();
$canManage   = hr_canManageHR();

// Group employees by department
$employeesByDept = [];
foreach ($departments as $d) {
  $employeesByDept[$d['dept_code']] = [];
}
foreach ($employees as $emp) {
  $dc = $emp['department_code'];
  if (!isset($employeesByDept[$dc])) {
    $employeesByDept[$dc] = [];
  }
  $employeesByDept[$dc][] = $emp;
}

$nameParts = explode(' ', trim($currUser['full_name']));
$initials  = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR HR System · Organizational Hierarchy</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
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
                <span class="hr-opacity-50" >|</span>
                <span>PERSONNEL OPERATIONS</span>
              </div>
            </div>
          </a>
        </div>

        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search division roles..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <div class="top-nav__actions">
          <div class="confidential-system-pill" title="Restricted Personnel System">
            <span>🔒</span>
            <span>HIGHLY CONFIDENTIAL SYSTEM</span>
          </div>

          <div class="top-user-profile" title="Active Session: <?= htmlspecialchars($currUser['full_name']) ?>">
            <div class="hr-avatar-circle" >
              <?= $initials ?>
            </div>
            <div class="user-details-top">
              <span class="user-name-top"><?= htmlspecialchars($currUser['full_name']) ?></span>
              <span class="user-role-top"><?= htmlspecialchars($currUser['role_name'] ?? 'Authorized User') ?> · <?= htmlspecialchars($currUser['clearance_level'] ?? 'L1') ?></span>
            </div>
          </div>

          <a href="../api/logout.php?system=HR%20System&redirect=../HR%20System/login.php" class="top-signout-btn" title="Sign Out" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" >
            <span class="material-symbols-outlined">logout</span>
            <span>Sign Out</span>
          </a>
        </div>
      </div>
    </header>

    <div class="main-layout">
            <aside class="sidebar">
        <div>
          <div class="sidebar-section-title">Human Resources</div>
          <nav class="sidebar-nav">
            <a href="Dashboard.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon">📊</span><span>Dashboard</span></div>
            </a>
            <a href="EmployeeRecords.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon">👥</span><span>Employee Records</span></div>
            </a>
            <a href="OnboardingTracker.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon">⚡</span><span>Onboarding Pipeline</span></div>
            </a>
            <a href="Offboarding.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon">🔒</span><span>Offboarding &amp; Revocation</span></div>
            </a>
            <a href="LeaveManagement.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon">📅</span><span>Leave Management</span></div>
            </a>
            <a href="OrgStructure.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left"><span class="sidebar-icon">🏛️</span><span>Org Hierarchy</span></div>
              <span class="sidebar-pill"><?= count($departments) ?></span>
            </a>
            <a href="Training.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon">🎓</span><span>Training &amp; Certs</span></div>
            </a>
          </nav>
        </div>
      </aside>

      <!-- MAIN CONTENT -->
      <main class="content-wrapper">
        <div class="portal-container hr-max-w-1200" >
          <!-- Page Header -->
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <a href="Dashboard.php">HR System</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Executive Structure &amp; Division Map</span>
              </div>
              <h1 class="page-title">Enterprise Organizational Hierarchy</h1>
              <p class="page-subtitle">Live hierarchical structure of <?= count($departments) ?> operational divisions queried from MySQL database</p>
            </div>
            <div class="page-header-actions">
              <a href="EmployeeRecords.php" class="btn btn-outline">
                <span>View Full Ledger (<?= count($employees) ?> Active) →</span>
              </a>
            </div>
          </div>

          <!-- Department Division Cards Grid -->
          <div class="hr-grid-org-cards" >
            <?php
            $deptBadges = [
              'EXE' => ['icon' => '🏛️', 'color' => '#B23A32'],
              'GOV' => ['icon' => '⚖️', 'color' => '#7A284E'],
              'SAL' => ['icon' => '💼', 'color' => '#D97706'],
              'ENG' => ['icon' => '⚙️', 'color' => '#3D7A99'],
              'ITD' => ['icon' => '💻', 'color' => '#1B3B5C'],
              'FIN' => ['icon' => '💳', 'color' => '#2E7D32'],
              'HRA' => ['icon' => '👥', 'color' => '#7A284E'],
              'OPS' => ['icon' => '📦', 'color' => '#8C5E28']
            ];

            foreach ($departments as $d):
              $code = $d['dept_code'];
              $meta = $deptBadges[$code] ?? ['icon' => '🏢', 'color' => 'var(--hr-navy)'];
              $staff = $employeesByDept[$code] ?? [];
            ?>
              <div class="hr-card" style="padding: 1.25rem; border-top: 3px solid <?= $meta['color'] ?>;">
                <div class="hr-flex-between-start-mb" >
                  <div>
                    <div class="hr-flex-gap-4" >
                      <span class="hr-text-16" ><?= $meta['icon'] ?></span>
                      <h3 class="hr-title-15-navy" ><?= htmlspecialchars($d['dept_name']) ?></h3>
                    </div>
                    <div class="hr-mono-muted-sm" >
                      CODE: <?= htmlspecialchars($code) ?> · Target: <?= htmlspecialchars($d['employee_count_target'] ?: 10) ?> Staff
                    </div>
                  </div>
                  <span class="sidebar-pill" style="background: <?= $meta['color'] ?>; color: #FFF;">
                    <?= count($staff) ?> Staff
                  </span>
                </div>

                <div class="hr-desc-org" >
                  <?= htmlspecialchars($d['main_function'] ?? 'Operational Division of VOSTOKPRIBOR Group') ?>
                </div>

                <!-- Staff in this Department -->
                <div class="hr-border-t-divided" >
                  <div class="hr-caption-bold-navy" >
                    Assigned Personnel
                  </div>
                  <div class="hr-org-member-list" >
                    <?php if (empty($staff)): ?>
                      <div class="hr-text-muted-italic" >No active personnel registered in this division.</div>
                    <?php else: ?>
                      <?php foreach ($staff as $s): ?>
                        <div class="hr-org-member-item" >
                          <div>
                            <span class="hr-semibold-navy" ><?= htmlspecialchars($s['full_name']) ?></span>
                            <span class="hr-text-muted-105" >· <?= htmlspecialchars($s['job_title']) ?></span>
                          </div>
                          <span class="clearance-badge clearance-l<?= substr($s['clearance_level'], 1) ?> hr-pill-micro" >
                            <?= htmlspecialchars($s['clearance_level']) ?>
                          </span>
                        </div>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </main>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
</body>

</html>