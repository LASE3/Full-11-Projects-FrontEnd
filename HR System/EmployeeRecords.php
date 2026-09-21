<?php
/**
 * VOSTOKPRIBOR HR System - Employee Records & Clearance Registry
 * Database-driven employee ledger with live filtering, real-time modal dossier, and registration.
 */
require_once __DIR__ . '/hr_service.php';
requireAuth('HR');

$currUser    = hr_getCurrentUser();
$metrics     = hr_getDashboardMetrics();
$departments = hr_getDepartments();
$employees   = hr_getEmployees();
$canManage   = hr_canManageHR();

$nameParts = explode(' ', trim($currUser['full_name']));
$initials  = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR HR System · Employee Records &amp; Clearance Registry</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body>

  <div class="app-container">
    <!-- TOP NAVIGATION BAR -->
    <header class="top-nav">
      <div class="top-nav__accent-stripe"></div>

      <div class="top-nav__content">
        <!-- Brand -->
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

        <!-- Global Omni Search -->
        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search employee records, EMP-ID, clearance level, department..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <!-- Right User Actions -->
        <div class="top-nav__actions">
          <div class="confidential-system-pill" title="Restricted Personnel & Security Clearance System (GOST Class 1G)">
            <span>🔒</span>
            <span>HIGHLY CONFIDENTIAL SYSTEM</span>
          </div>

          <!-- Dynamic Active User Profile -->
          <div class="top-user-profile" title="Active User: <?= htmlspecialchars($currUser['full_name']) ?> (<?= htmlspecialchars($currUser['clearance_level'] ?? 'L1') ?>)">
            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg, #7A284E 0%, #3D1427 100%);color:#FFFFFF;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12.5px;border:2px solid #FF8080;box-shadow:0 0 8px rgba(255,128,128,0.3);">
              <?= $initials ?>
            </div>
            <div class="user-details-top">
              <span class="user-name-top"><?= htmlspecialchars($currUser['full_name']) ?></span>
              <span class="user-role-top"><?= htmlspecialchars($currUser['role_name'] ?? 'Authorized User') ?> · <?= htmlspecialchars($currUser['clearance_level'] ?? 'L1') ?></span>
            </div>
          </div>

          <!-- Sign Out -->
          <a href="../api/logout.php?system=HR%20System&redirect=../HR%20System/login.php" class="top-signout-btn" title="Sign Out of HR System" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:4px;background:rgba(178,58,50,0.2);border:1px solid rgba(178,58,50,0.5);color:#FF8080;font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;margin-left:8px;vertical-align:middle;transition:all 0.2s;">
            <span class="material-symbols-outlined" style="font-size:15px;line-height:1;">logout</span>
            <span>Sign Out</span>
          </a>
        </div>
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
                <span class="sidebar-icon">📊</span>
                <span>Dashboard</span>
              </div>
            </a>
            <a href="EmployeeRecords.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">👥</span>
                <span>Employee Records</span>
              </div>
              <span class="sidebar-pill"><?= count($employees) ?></span>
            </a>
            <a href="OnboardingTracker.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">⚡</span>
                <span>Onboarding Pipeline</span>
              </div>
              <?php if ($metrics['active_onboarding'] > 0): ?>
                <span class="sidebar-pill amber"><?= $metrics['active_onboarding'] ?></span>
              <?php endif; ?>
            </a>
            <a href="Offboarding.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">🔒</span>
                <span>Offboarding &amp; Revocation</span>
              </div>
              <?php if ($metrics['active_offboarding'] > 0): ?>
                <span class="sidebar-pill alert"><?= $metrics['active_offboarding'] ?></span>
              <?php endif; ?>
            </a>
            <a href="LeaveManagement.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">📅</span>
                <span>Leave Management</span>
              </div>
              <?php if ($metrics['pending_leaves'] > 0): ?>
                <span class="sidebar-pill alert"><?= $metrics['pending_leaves'] ?></span>
              <?php endif; ?>
            </a>
            <a href="OrgStructure.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">🏛️</span>
                <span>Org Hierarchy</span>
              </div>
            </a>
            <a href="Training.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">🎓</span>
                <span>Training &amp; Certs</span>
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
              Active Level 4 Clearances: <strong><?= $metrics['clearance_counts']['L4'] ?> Vetted</strong>
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
                <a href="Dashboard.php">HR System</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Employee Records &amp; Clearance Registry</span>
              </div>
              <h1 class="page-title">Employee Directory &amp; Security Dossiers</h1>
              <p class="page-subtitle">Centralized database-driven human capital records with 4-tier security clearance classification and audit vault</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.hrApp.showToast('Ledger Audit', 'Rostrud compliance hash verified for <?= count($employees) ?> live personnel files.')">
                <span>📑 Verify Security Hashes</span>
              </button>
              <?php if ($canManage): ?>
                <button class="btn btn-primary-amber" onclick="window.hrApp.openModal('modal-add-employee')">
                  <span>+ Register New Employee</span>
                </button>
              <?php endif; ?>
            </div>
          </div>

          <!-- 4-Tier Security Clearance Quick Summary Strip (LIVE SQL DATA) -->
          <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
            <div class="hr-card" style="padding: 0.85rem 1rem; border-left: 3px solid var(--hr-clearance-l4); display: flex; align-items: center; justify-content: space-between;">
              <div>
                <div style="font-size: 11px; color: var(--hr-text-muted); font-weight: 600; text-transform: uppercase;">Level 4 · Top Secret</div>
                <div style="font-size: 18px; font-weight: 700; color: var(--hr-navy); font-family: var(--hr-font-mono);"><?= $metrics['clearance_counts']['L4'] ?> Staff</div>
              </div>
              <span class="clearance-badge clearance-l4">L4</span>
            </div>

            <div class="hr-card" style="padding: 0.85rem 1rem; border-left: 3px solid var(--hr-clearance-l3); display: flex; align-items: center; justify-content: space-between;">
              <div>
                <div style="font-size: 11px; color: var(--hr-text-muted); font-weight: 600; text-transform: uppercase;">Level 3 · Secret SCADA</div>
                <div style="font-size: 18px; font-weight: 700; color: var(--hr-navy); font-family: var(--hr-font-mono);"><?= $metrics['clearance_counts']['L3'] ?> Staff</div>
              </div>
              <span class="clearance-badge clearance-l3">L3</span>
            </div>

            <div class="hr-card" style="padding: 0.85rem 1rem; border-left: 3px solid var(--hr-clearance-l2); display: flex; align-items: center; justify-content: space-between;">
              <div>
                <div style="font-size: 11px; color: var(--hr-text-muted); font-weight: 600; text-transform: uppercase;">Level 2 · Confidential</div>
                <div style="font-size: 18px; font-weight: 700; color: var(--hr-navy); font-family: var(--hr-font-mono);"><?= $metrics['clearance_counts']['L2'] ?> Staff</div>
              </div>
              <span class="clearance-badge clearance-l2">L2</span>
            </div>

            <div class="hr-card" style="padding: 0.85rem 1rem; border-left: 3px solid var(--hr-clearance-l1); display: flex; align-items: center; justify-content: space-between;">
              <div>
                <div style="font-size: 11px; color: var(--hr-text-muted); font-weight: 600; text-transform: uppercase;">Level 1 · General</div>
                <div style="font-size: 18px; font-weight: 700; color: var(--hr-navy); font-family: var(--hr-font-mono);"><?= $metrics['clearance_counts']['L1'] ?> Staff</div>
              </div>
              <span class="clearance-badge clearance-l1">L1</span>
            </div>
          </div>

          <!-- Main Table Container -->
          <div class="hr-card" style="padding: 0; overflow: hidden;">
            <!-- Filter & Search Toolbar -->
            <div style="padding: 1rem 1.25rem; background-color: #FFFFFF; border-bottom: 1px solid var(--hr-surface-border); display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
              <!-- Search Bar -->
              <div style="position: relative; flex: 1; max-width: 380px;">
                <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--hr-text-muted); font-size: 13px;">🔍</span>
                <input 
                  type="text" 
                  id="employee-table-search" 
                  placeholder="Filter by name, EMP-ID, or role..." 
                  oninput="window.hrApp.filterEmployees()"
                  style="width: 100%; padding: 0.5rem 0.75rem 0.5rem 2.2rem; border: 1px solid var(--hr-surface-border); border-radius: var(--hr-radius-md); font-size: 12.5px;" 
                />
              </div>

              <!-- Filter Dropdowns -->
              <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.4rem;">
                  <span style="font-size: 11.5px; color: var(--hr-text-muted); font-weight: 600;">Department:</span>
                  <select id="employee-dept-filter" onchange="window.hrApp.filterEmployees()" style="padding: 0.45rem 0.75rem; border: 1px solid var(--hr-surface-border); border-radius: var(--hr-radius-md); font-size: 12px; background: #FFFFFF;">
                    <option value="all">All Departments (<?= count($departments) ?>)</option>
                    <?php foreach ($departments as $dept): ?>
                      <option value="<?= htmlspecialchars($dept['dept_code']) ?>"><?= htmlspecialchars($dept['dept_name']) ?> (<?= htmlspecialchars($dept['dept_code']) ?>)</option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div style="display: flex; align-items: center; gap: 0.4rem;">
                  <span style="font-size: 11.5px; color: var(--hr-text-muted); font-weight: 600;">Clearance:</span>
                  <select id="employee-clearance-filter" onchange="window.hrApp.filterEmployees()" style="padding: 0.45rem 0.75rem; border: 1px solid var(--hr-surface-border); border-radius: var(--hr-radius-md); font-size: 12px; background: #FFFFFF;">
                    <option value="all">All Clearance Tiers</option>
                    <option value="4">Level 4 · Top Secret</option>
                    <option value="3">Level 3 · Secret SCADA</option>
                    <option value="2">Level 2 · Confidential</option>
                    <option value="1">Level 1 · General</option>
                  </select>
                </div>

                <button class="btn btn-outline btn-sm" onclick="document.getElementById('employee-table-search').value=''; document.getElementById('employee-dept-filter').value='all'; document.getElementById('employee-clearance-filter').value='all'; window.hrApp.filterEmployees();">
                  Reset Filters
                </button>
              </div>
            </div>

            <!-- Searchable / Filterable Table (LIVE DATABASE ROWS) -->
            <table class="employee-table">
              <thead>
                <tr>
                  <th style="width: 140px;">Employee ID</th>
                  <th>Name &amp; Profile</th>
                  <th>Department</th>
                  <th>Official Title</th>
                  <th style="width: 170px;">Clearance Level</th>
                  <th style="width: 110px;">Status</th>
                  <th style="width: 110px; text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody class="employee-table-body">
                <?php foreach ($employees as $emp): 
                  $cNum = substr($emp['clearance_level'], 1);
                  $nameParts = explode(' ', trim($emp['full_name']));
                  $rowInitials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
                  
                  $statusPillClass = 'status-active';
                  if ($emp['employment_status'] === 'Suspended') $statusPillClass = 'status-offboarding';
                  if ($emp['employment_status'] === 'OnLeave') $statusPillClass = 'status-leave';
                  if ($emp['employment_status'] === 'Terminated') $statusPillClass = 'status-offboarding';
                ?>
                  <tr class="employee-row" 
                      data-id="<?= htmlspecialchars($emp['emp_id']) ?>" 
                      data-name="<?= htmlspecialchars($emp['full_name']) ?>" 
                      data-dept="<?= htmlspecialchars($emp['department_code']) ?>" 
                      data-clearance="<?= $cNum ?>" 
                      onclick="window.hrApp.inspectEmployee('<?= htmlspecialchars($emp['emp_id']) ?>')">
                    <td>
                      <span style="font-family: var(--hr-font-mono); font-weight: 700; color: var(--hr-plum); font-size: 12px;">
                        <?= htmlspecialchars($emp['emp_id']) ?>
                      </span>
                    </td>
                    <td>
                      <div style="display: flex; align-items: center; gap: 0.65rem;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #1B3B5C; color: #FFF; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 11px; flex-shrink: 0; border: 1px solid var(--hr-plum);">
                          <?= $rowInitials ?>
                        </div>
                        <div>
                          <div style="font-weight: 600; color: var(--hr-navy); font-size: 13px;"><?= htmlspecialchars($emp['full_name']) ?></div>
                          <div style="font-size: 11px; color: var(--hr-text-muted);"><?= htmlspecialchars($emp['email']) ?></div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span style="font-size: 12.5px; color: var(--hr-text-primary); font-weight: 500;">
                        <?= htmlspecialchars($emp['dept_name'] ?? $emp['department_code']) ?>
                      </span>
                    </td>
                    <td>
                      <span style="font-size: 12.5px; color: var(--hr-text-secondary);"><?= htmlspecialchars($emp['job_title']) ?></span>
                    </td>
                    <td>
                      <span class="clearance-badge clearance-l<?= $cNum ?>">
                        <span>🔒</span>
                        <span>Level <?= $cNum ?> · <?= $cNum == '4' ? 'Top Secret' : ($cNum == '3' ? 'Secret SCADA' : ($cNum == '2' ? 'Confidential' : 'General')) ?></span>
                      </span>
                    </td>
                    <td>
                      <span class="status-pill <?= $statusPillClass ?>"><?= htmlspecialchars($emp['employment_status']) ?></span>
                    </td>
                    <td style="text-align: right;" onclick="event.stopPropagation()">
                      <div style="display: inline-flex; gap: 0.35rem;">
                        <button class="btn btn-outline btn-sm" onclick="window.hrApp.inspectEmployee('<?= htmlspecialchars($emp['emp_id']) ?>')">
                          Dossier 🔒
                        </button>
                        <?php if ($canManage): ?>
                          <button class="btn btn-outline btn-sm" style="color: #B23A32; border-color: rgba(178,58,50,0.4);" 
                                  title="Remove or terminate employee record"
                                  onclick="window.hrApp.deleteEmployee('<?= htmlspecialchars($emp['emp_id']) ?>', '<?= addslashes($emp['full_name']) ?>')">
                            🗑
                          </button>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- MODAL: EMPLOYEE RECORD DOSSIER (Detail View) -->
  <div id="modal-employee-detail" class="modal-backdrop">
    <div class="modal-dialog" style="max-width: 680px;">
      <div class="modal-header" style="background-color: var(--hr-navy); color: #FFFFFF; border-bottom: 3px solid var(--hr-plum);">
        <div>
          <div style="font-size: 10.5px; color: #FF8080; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
            CONFIDENTIAL PERSONNEL DOSSIER · GOST R 34.10
          </div>
          <div style="font-size: 16px; font-weight: 700; margin-top: 2px;">
            <span id="drawer-emp-name">Employee Name</span>
          </div>
        </div>
        <button class="modal-close" style="color: #FFFFFF;" onclick="window.hrApp.closeModal('modal-employee-detail')">✕</button>
      </div>

      <div class="modal-body" style="padding: 1.5rem;">
        <div style="display: flex; gap: 1.25rem; align-items: flex-start; margin-bottom: 1.25rem; padding-bottom: 1.25rem; border-bottom: 1px solid var(--hr-surface-border);">
          <div id="drawer-emp-avatar-placeholder" style="width: 64px; height: 64px; border-radius: var(--hr-radius-md); background: #0F2438; color: #FFF; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 700; border: 2px solid var(--hr-plum); flex-shrink: 0;">
            VP
          </div>
          <div style="flex: 1;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
              <span id="drawer-emp-id" style="font-family: var(--hr-font-mono); font-size: 12px; font-weight: 700; color: var(--hr-plum);">EMP-xxxx</span>
              <span id="drawer-emp-status" class="status-pill status-active">Active</span>
            </div>
            <div id="drawer-emp-title" style="font-weight: 600; font-size: 14px; color: var(--hr-navy);">Job Title</div>
            <div id="drawer-emp-dept" style="font-size: 12px; color: var(--hr-text-muted); margin-top: 2px;">Department</div>
            <div style="margin-top: 0.5rem;">
              <span id="drawer-emp-clearance" class="clearance-badge clearance-l3">🔒 Level 3 Clearance</span>
            </div>
          </div>
        </div>

        <!-- Details Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem; font-size: 12px; background: var(--hr-surface-dim); padding: 1rem; border-radius: var(--hr-radius-md); border: 1px solid var(--hr-surface-border); margin-bottom: 1.25rem;">
          <div><strong>Email:</strong> <span id="drawer-emp-email" style="font-family: var(--hr-font-mono);">email@vostokpribor.local</span></div>
          <div><strong>Account Login:</strong> <span id="drawer-emp-username" style="font-family: var(--hr-font-mono); color: var(--hr-plum);">username</span></div>
          <div><strong>Hire Date:</strong> <span id="drawer-emp-hire">2026-01-01</span></div>
          <div><strong>Direct Supervisor:</strong> <span id="drawer-emp-sup">Manager</span></div>
          <div><strong>Assigned System Role:</strong> <span id="drawer-emp-role">Role</span></div>
          <div><strong>Account Status:</strong> <span id="drawer-emp-accstatus">Active</span></div>
        </div>

        <?php if ($canManage): ?>
          <!-- Management Actions (Clearance Elevation & Offboarding) -->
          <div style="background: #FFF; border: 1px solid var(--hr-surface-border); border-radius: var(--hr-radius-md); padding: 1rem;">
            <div style="font-size: 11.5px; font-weight: 700; color: var(--hr-navy); text-transform: uppercase; margin-bottom: 0.5rem;">
              Executive Personnel Actions
            </div>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
              <button class="btn btn-outline btn-sm" onclick="window.hrApp.elevateClearancePrompt()">
                <span>🛡️ Modify Clearance Tier</span>
              </button>
              <button class="btn btn-outline btn-sm" style="color: var(--hr-confidential); border-color: var(--hr-confidential);" onclick="window.hrApp.triggerOffboardingFromDossier()">
                <span>🔒 Initiate Offboarding</span>
              </button>
              <button class="btn btn-outline btn-sm" style="color: #B23A32; border-color: rgba(178,58,50,0.5);" onclick="window.hrApp.deleteEmployee(currentDossierEmpId, document.getElementById('drawer-emp-name').textContent)">
                <span>🗑️ Purge / Terminate Record</span>
              </button>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="modal-footer" style="padding: 1rem 1.5rem; background: var(--hr-surface-dim); border-top: 1px solid var(--hr-surface-border); display: flex; align-items: center; justify-content: flex-end;">
        <button class="btn btn-outline" onclick="window.hrApp.closeModal('modal-employee-detail')">Close Dossier</button>
      </div>
    </div>
  </div>

  <?php if ($canManage): ?>
  <!-- MODAL: REGISTER NEW EMPLOYEE (LIVE DATABASE INSERT) -->
  <div id="modal-add-employee" class="modal-backdrop">
    <div class="modal-dialog" style="max-width: 580px;">
      <div class="modal-header" style="background-color: var(--hr-navy); color: #FFFFFF; border-bottom: 3px solid var(--hr-plum);">
        <div>
          <div style="font-weight: 700; font-size: 15px;">Register New Employee into VOSTOKPRIBOR</div>
          <div style="font-size: 11px; color: #FF8080;">Saves to MySQL · Provisions Account Credentials · Enters Onboarding Pipeline</div>
        </div>
        <button class="modal-close" style="color: #FFFFFF;" onclick="window.hrApp.closeModal('modal-add-employee')">✕</button>
      </div>
      <div class="modal-body" style="padding: 1.5rem;">
        <form id="form-register-employee" onsubmit="return window.hrApp.handleRegisterEmployee(event, this);">
          <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div>
              <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--hr-navy); margin-bottom: 0.25rem;">Full Legal Name *</label>
              <input type="text" name="full_name" required placeholder="e.g. Dr. Viktor Alexandrov" style="width: 100%; padding: 0.55rem; border: 1px solid var(--hr-surface-border); border-radius: var(--hr-radius-md);" />
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
              <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--hr-navy); margin-bottom: 0.25rem;">Operational Department *</label>
                <select name="department_code" required style="width: 100%; padding: 0.55rem; border: 1px solid var(--hr-surface-border); border-radius: var(--hr-radius-md);">
                  <?php foreach ($departments as $dept): ?>
                    <option value="<?= htmlspecialchars($dept['dept_code']) ?>">
                      <?= htmlspecialchars($dept['dept_name']) ?> (<?= htmlspecialchars($dept['dept_code']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--hr-navy); margin-bottom: 0.25rem;">Security Clearance Tier *</label>
                <select name="clearance_level" required style="width: 100%; padding: 0.55rem; border: 1px solid var(--hr-surface-border); border-radius: var(--hr-radius-md);">
                  <option value="L1">Level 1 · General Public</option>
                  <option value="L2">Level 2 · Confidential</option>
                  <option value="L3" selected>Level 3 · Secret SCADA</option>
                  <option value="L4">Level 4 · Top Secret Executive</option>
                </select>
              </div>
            </div>

            <div>
              <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--hr-navy); margin-bottom: 0.25rem;">Job Title / Engineering Function *</label>
              <input type="text" name="job_title" required placeholder="e.g. Senior Optical Systems Physicist" style="width: 100%; padding: 0.55rem; border: 1px solid var(--hr-surface-border); border-radius: var(--hr-radius-md);" />
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
              <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--hr-navy); margin-bottom: 0.25rem;">Corporate Email (Optional)</label>
                <input type="email" name="email" placeholder="Auto-generated if empty" style="width: 100%; padding: 0.55rem; border: 1px solid var(--hr-surface-border); border-radius: var(--hr-radius-md);" />
              </div>

              <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--hr-navy); margin-bottom: 0.25rem;">Direct Manager</label>
                <select name="manager_emp_id" style="width: 100%; padding: 0.55rem; border: 1px solid var(--hr-surface-border); border-radius: var(--hr-radius-md);">
                  <option value="">-- No Direct Manager --</option>
                  <?php foreach ($employees as $mgr): ?>
                    <option value="<?= htmlspecialchars($mgr['emp_id']) ?>">
                      <?= htmlspecialchars($mgr['full_name']) ?> (<?= htmlspecialchars($mgr['emp_id']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
              <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--hr-navy); margin-bottom: 0.25rem;">Initial Account Password</label>
                <input type="text" name="password" value="Vostok2026!" style="width: 100%; padding: 0.55rem; border: 1px solid var(--hr-surface-border); border-radius: var(--hr-radius-md); font-family: var(--hr-font-mono);" />
              </div>
              <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--hr-navy); margin-bottom: 0.25rem;">Hire Date</label>
                <input type="date" name="hire_date" value="<?= date('Y-m-d') ?>" style="width: 100%; padding: 0.55rem; border: 1px solid var(--hr-surface-border); border-radius: var(--hr-radius-md);" />
              </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 0.75rem;">
              <button type="button" class="btn btn-outline" onclick="window.hrApp.closeModal('modal-add-employee')">Cancel</button>
              <button type="submit" id="btn-submit-employee" class="btn btn-primary-amber">
                <span>Register &amp; Initialize Onboarding →</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
  <script>
    // Check if openAdd is passed in URL query
    if (new URLSearchParams(window.location.search).get('openAdd') === '1') {
      window.hrApp.openModal('modal-add-employee');
    }
  </script>
</body>
</html>
