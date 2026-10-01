<?php

/**
 * VOSTOKPRIBOR HR System - Training & Certifications
 * Database-driven technical qualifications and security accreditations from MySQL.
 */
require_once __DIR__ . '/hr_service.php';
requireAuth('HR');

$currUser        = hr_getCurrentUser();
$trainingRecords = hr_getTrainingRecords();
$activeEmployees = hr_getEmployees('', '', '', 'Active');
$canManage       = hr_canManageHR();

$nameParts = explode(' ', trim($currUser['full_name']));
$initials  = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR HR System · Training &amp; Certifications</title>
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
          <button class="mobile-nav-toggle" id="hr-sidebar-toggle" onclick="document.body.classList.toggle('sidebar-open')" title="Toggle Menu"><span class="material-symbols-outlined">menu</span></button>
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
            <span class="search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search training records..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <div class="top-nav__actions">
          <div class="confidential-system-pill" title="Restricted Personnel System">
            <span><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
            <span>HIGHLY CONFIDENTIAL SYSTEM</span>
          </div>

          <button class="icon-button notifications-btn" id="notifications-toggle-btn" title="Live Enterprise Notifications">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
              <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
          </button>
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
              <div class="sidebar-item-left"><span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span><span>Employee Records</span></div>
            </a>
            <a href="OnboardingTracker.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></span><span>Onboarding Pipeline</span></div>
            </a>
            <a href="Offboarding.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span><span>Offboarding &amp; Revocation</span></div>
            </a>
            <a href="LeaveManagement.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span><span>Leave Management</span></div>
            </a>
            <a href="OrgStructure.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="2" y1="20" x2="22" y2="20"/><line x1="4" y1="10" x2="4" y2="20"/><line x1="9" y1="10" x2="9" y2="20"/><line x1="15" y1="10" x2="15" y2="20"/><line x1="20" y1="10" x2="20" y2="20"/><polygon points="12 2 2 7 22 7 12 2"/></svg>️</span><span>Org Hierarchy</span></div>
            </a>
            <a href="Training.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left"><span class="sidebar-icon">🎓</span><span>Training &amp; Certs</span></div>
              <span class="sidebar-pill"><?= count($trainingRecords) ?></span>
            </a>
          
            <a href="Integrations.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00E5FF" stroke-width="2">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                  </svg></span><span class="hr-nav-integrations" style="color: #00E5FF; font-weight: 600;">System Integrations</span></div><span class="sidebar-badge hr-badge-integrations" style="background: rgba(0, 229, 255, 0.15); color: #00E5FF; border: 1px solid rgba(0, 229, 255, 0.3);">SYS08</span>
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
                <span class="breadcrumb-current">Technical Academy &amp; Certifications</span>
              </div>
              <h1 class="page-title">Workforce Certifications &amp; Security Protocols</h1>
              <p class="page-subtitle">Verified technical qualifications, safety accreditations, and industrial compliance records</p>
            </div>
            <div class="page-header-actions">
              <?php if ($canManage): ?>
                <button class="btn btn-primary-amber" onclick="window.hrApp.openModal('modal-create-training')">
                  <span>+ Log Training Accreditation</span>
                </button>
              <?php endif; ?>
            </div>
          </div>

          <!-- Training Records Table -->
          <div class="hr-card hr-panel-flush-mt-15" >
            <div class="hr-table-header-simple" >
              <h3 class="card-title">Live Technical Training &amp; Protocol Certifications</h3>
              <p class="hr-meta-subtext" >
                Queried directly from MySQL <code>training_records</code> table
              </p>
            </div>

            <table class="employee-table">
              <thead>
                <tr>
                  <th class="hr-w-110" >Record ID</th>
                  <th>Employee</th>
                  <th>Division</th>
                  <th>Accreditation / Course Title</th>
                  <th>Completion Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($trainingRecords)): ?>
                  <tr>
                    <td class="hr-empty-state" colspan="6" >No training records logged.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($trainingRecords as $tr): ?>
                    <tr>
                      <td class="hr-mono-bold-plum" >
                        TR-<?= str_pad($tr['training_id'], 4, '0', STR_PAD_LEFT) ?>
                      </td>
                      <td>
                        <div class="hr-bold-navy" ><?= htmlspecialchars($tr['full_name']) ?></div>
                        <div class="hr-mono-muted-11" ><?= htmlspecialchars($tr['emp_id']) ?></div>
                      </td>
                      <td><?= htmlspecialchars($tr['dept_name'] ?? $tr['department_code']) ?></td>
                      <td>
                        <div class="hr-semibold-navy" ><?= htmlspecialchars($tr['training_name']) ?></div>
                        <div class="hr-text-muted-11" >Industrial Qualification Standard</div>
                      </td>
                      <td>
                        <span class="hr-mono-semibold" >
                          <?= date('M d, Y', strtotime($tr['completed_at'])) ?>
                        </span>
                      </td>
                      <td>
                        <span class="status-pill status-active">Verified ✓</span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

  <?php if ($canManage): ?>
    <!-- MODAL: LOG TRAINING ACCREDITATION -->
    <div id="modal-create-training" class="modal-backdrop">
      <div class="modal-dialog hr-modal-500" >
        <div class="modal-header hr-modal-header-plum" >
          <div class="hr-title-14-bold" >Log Technical Training Certification</div>
          <button class="modal-close hr-text-white"  onclick="window.hrApp.closeModal('modal-create-training')">✕</button>
        </div>
        <div class="modal-body hr-p-6" >
          <form id="form-create-training" onsubmit="return window.hrApp.handleCreateTraining(event, this);">
            <div class="hr-flex-col-gap-md" >
              <div>
                <label class="hr-field-label" >Employee *</label>
                <select class="hr-form-control" name="emp_id" required >
                  <?php foreach ($activeEmployees as $ae): ?>
                    <option value="<?= htmlspecialchars($ae['emp_id']) ?>">
                      <?= htmlspecialchars($ae['full_name']) ?> (<?= htmlspecialchars($ae['emp_id']) ?> · <?= htmlspecialchars($ae['job_title']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div>
                <label class="hr-field-label" >Training / Certification Title *</label>
                <input class="hr-form-control" type="text" name="training_name" required placeholder="e.g. SCADA Cryptography & GOST R 34.10 Protocols"  />
              </div>

              <div>
                <label class="hr-field-label" >Date of Completion *</label>
                <input type="date" name="completed_at" required value="<?= date('Y-m-d') ?>" class="hr-form-control" />
              </div>

              <div class="hr-flex-end-gap-sm" >
                <button type="button" class="btn btn-outline" onclick="window.hrApp.closeModal('modal-create-training')">Cancel</button>
                <button type="submit" class="btn btn-primary-amber">Record Certification →</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
  <script src="../assets/js/notifications-hub.js" defer></script>
</body>

</html>