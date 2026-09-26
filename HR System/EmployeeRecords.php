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

        <!-- Right User Actions -->
        <div class="top-nav__actions">
          <div class="confidential-system-pill" title="Restricted Personnel & Security Clearance System (GOST Class 1G)">
            <span>🔒</span>
            <span>HIGHLY CONFIDENTIAL SYSTEM</span>
          </div>

          <!-- Dynamic Active User Profile -->
          <div class="top-user-profile" title="Active User: <?= htmlspecialchars($currUser['full_name']) ?> (<?= htmlspecialchars($currUser['clearance_level'] ?? 'L1') ?>)">
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
            <div class="hr-text-inverse-muted-sm" >
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
          <div class="hr-grid-4col-mb" >
            <div class="hr-card hr-card-clearance-l4" >
              <div>
                <div class="hr-caption-muted" >Level 4 · Top Secret</div>
                <div class="hr-mono-heading-18" ><?= $metrics['clearance_counts']['L4'] ?> Staff</div>
              </div>
              <span class="clearance-badge clearance-l4">L4</span>
            </div>

            <div class="hr-card hr-card-clearance-l3" >
              <div>
                <div class="hr-caption-muted" >Level 3 · Secret SCADA</div>
                <div class="hr-mono-heading-18" ><?= $metrics['clearance_counts']['L3'] ?> Staff</div>
              </div>
              <span class="clearance-badge clearance-l3">L3</span>
            </div>

            <div class="hr-card hr-card-clearance-l2" >
              <div>
                <div class="hr-caption-muted" >Level 2 · Confidential</div>
                <div class="hr-mono-heading-18" ><?= $metrics['clearance_counts']['L2'] ?> Staff</div>
              </div>
              <span class="clearance-badge clearance-l2">L2</span>
            </div>

            <div class="hr-card hr-card-clearance-l1" >
              <div>
                <div class="hr-caption-muted" >Level 1 · General</div>
                <div class="hr-mono-heading-18" ><?= $metrics['clearance_counts']['L1'] ?> Staff</div>
              </div>
              <span class="clearance-badge clearance-l1">L1</span>
            </div>
          </div>

          <!-- Main Table Container -->
          <div class="hr-card hr-panel-flush" >
            <!-- Filter & Search Toolbar -->
            <div class="hr-table-header-bar-wrap" >
              <!-- Search Bar -->
              <div class="hr-search-container" >
                <span class="hr-search-icon" >🔍</span>
                <input class="hr-search-input"
                  type="text"
                  id="employee-table-search"
                  placeholder="Filter by name, EMP-ID, or role..."
                  oninput="window.hrApp.filterEmployees()"
                   />
              </div>

              <!-- Filter Dropdowns -->
              <div class="hr-flex-gap-md" >
                <div class="hr-flex-gap-4" >
                  <span class="hr-caption-muted-115" >Department:</span>
                  <select class="hr-select-filter" id="employee-dept-filter" onchange="window.hrApp.filterEmployees()" >
                    <option value="all">All Departments (<?= count($departments) ?>)</option>
                    <?php foreach ($departments as $dept): ?>
                      <option value="<?= htmlspecialchars($dept['dept_code']) ?>"><?= htmlspecialchars($dept['dept_name']) ?> (<?= htmlspecialchars($dept['dept_code']) ?>)</option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="hr-flex-gap-4" >
                  <span class="hr-caption-muted-115" >Clearance:</span>
                  <select class="hr-select-filter" id="employee-clearance-filter" onchange="window.hrApp.filterEmployees()" >
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
                  <th class="hr-w-140" >Employee ID</th>
                  <th>Name &amp; Profile</th>
                  <th>Department</th>
                  <th>Official Title</th>
                  <th class="hr-w-170" >Clearance Level</th>
                  <th class="hr-w-110" >Status</th>
                  <th class="hr-w-110-right" >Action</th>
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
                      <span class="hr-mono-bold-plum" >
                        <?= htmlspecialchars($emp['emp_id']) ?>
                      </span>
                    </td>
                    <td>
                      <div class="hr-flex-gap-65" >
                        <div class="hr-avatar-32" >
                          <?= $rowInitials ?>
                        </div>
                        <div>
                          <div class="hr-font-semibold-navy-13" ><?= htmlspecialchars($emp['full_name']) ?></div>
                          <div class="hr-text-muted-11" ><?= htmlspecialchars($emp['email']) ?></div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="hr-font-medium-125" >
                        <?= htmlspecialchars($emp['dept_name'] ?? $emp['department_code']) ?>
                      </span>
                    </td>
                    <td>
                      <span class="hr-text-secondary-125" ><?= htmlspecialchars($emp['job_title']) ?></span>
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
                    <td class="hr-text-right"  onclick="event.stopPropagation()">
                      <div class="hr-inline-gap-xs" >
                        <button class="btn btn-outline btn-sm" onclick="window.hrApp.inspectEmployee('<?= htmlspecialchars($emp['emp_id']) ?>')">
                          Dossier 🔒
                        </button>
                        <?php if ($canManage): ?>
                          <button class="btn btn-outline btn-sm hr-btn-border-red-40" 
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
    <div class="modal-dialog hr-modal-680" >
      <div class="modal-header hr-modal-header-plum" >
        <div>
          <div class="hr-tag-rose-bold" >
            CONFIDENTIAL PERSONNEL DOSSIER · GOST R 34.10
          </div>
          <div class="hr-title-16-bold" >
            <span id="drawer-emp-name">Employee Name</span>
          </div>
        </div>
        <button class="modal-close hr-text-white"  onclick="window.hrApp.closeModal('modal-employee-detail')">✕</button>
      </div>

      <div class="modal-body hr-p-6" >
        <div class="hr-header-hero" >
          <div class="hr-avatar-64" id="drawer-emp-avatar-placeholder" >
            VP
          </div>
          <div class="hr-flex-1" >
            <div class="hr-flex-gap-sm-mb" >
              <span class="hr-mono-plum-12" id="drawer-emp-id" >EMP-xxxx</span>
              <span id="drawer-emp-status" class="status-pill status-active">Active</span>
            </div>
            <div class="hr-title-14-navy" id="drawer-emp-title" >Job Title</div>
            <div class="hr-meta-desc-muted" id="drawer-emp-dept" >Department</div>
            <div class="hr-mt-2" >
              <span id="drawer-emp-clearance" class="clearance-badge clearance-l3">🔒 Level 3 Clearance</span>
            </div>
          </div>
        </div>

        <!-- Details Grid -->
        <div class="hr-grid-details" >
          <div><strong>Email:</strong> <span class="hr-mono" id="drawer-emp-email" >email@vostokpribor.local</span></div>
          <div><strong>Account Login:</strong> <span class="hr-mono-plum" id="drawer-emp-username" >username</span></div>
          <div><strong>Hire Date:</strong> <span id="drawer-emp-hire">2026-01-01</span></div>
          <div><strong>Direct Supervisor:</strong> <span id="drawer-emp-sup">Manager</span></div>
          <div><strong>Assigned System Role:</strong> <span id="drawer-emp-role">Role</span></div>
          <div><strong>Account Status:</strong> <span id="drawer-emp-accstatus">Active</span></div>
        </div>

        <?php if ($canManage): ?>
          <!-- Management Actions (Clearance Elevation & Offboarding) -->
          <div class="hr-card-white" >
            <div class="hr-title-uppercase" >
              Executive Personnel Actions
            </div>
            <div class="hr-flex-wrap-gap" >
              <button class="btn btn-outline btn-sm" onclick="window.hrApp.elevateClearancePrompt()">
                <span>🛡️ Modify Clearance Tier</span>
              </button>
              <button class="btn btn-outline btn-sm hr-btn-confidential-action"  onclick="window.hrApp.triggerOffboardingFromDossier()">
                <span>🔒 Initiate Offboarding</span>
              </button>
              <button class="btn btn-outline btn-sm hr-btn-danger-soft"  onclick="window.hrApp.deleteEmployee(currentDossierEmpId, document.getElementById('drawer-emp-name').textContent)">
                <span>🗑️ Purge / Terminate Record</span>
              </button>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="modal-footer hr-modal-footer" >
        <button class="btn btn-outline" onclick="window.hrApp.closeModal('modal-employee-detail')">Close Dossier</button>
      </div>
    </div>
  </div>

  <?php if ($canManage): ?>
    <!-- MODAL: REGISTER NEW EMPLOYEE (LIVE DATABASE INSERT) -->
    <div id="modal-add-employee" class="modal-backdrop">
      <div class="modal-dialog hr-modal-580" >
        <div class="modal-header hr-modal-header-plum" >
          <div>
            <div class="hr-title-15-bold" >Register New Employee into VOSTOKPRIBOR</div>
            <div class="hr-text-rose" >Saves to MySQL · Provisions Account Credentials · Enters Onboarding Pipeline</div>
          </div>
          <button class="modal-close hr-text-white"  onclick="window.hrApp.closeModal('modal-add-employee')">✕</button>
        </div>
        <div class="modal-body hr-p-6" >
          <form id="form-register-employee" onsubmit="return window.hrApp.handleRegisterEmployee(event, this);">
            <div class="hr-flex-col-gap-md" >
              <div>
                <label class="hr-field-label" >Full Legal Name *</label>
                <input class="hr-form-control" type="text" name="full_name" required placeholder="e.g. Dr. Viktor Alexandrov"  />
              </div>

              <div class="hr-grid-2col-sm" >
                <div>
                  <label class="hr-field-label" >Operational Department *</label>
                  <select class="hr-form-control" name="department_code" required >
                    <?php foreach ($departments as $dept): ?>
                      <option value="<?= htmlspecialchars($dept['dept_code']) ?>">
                        <?= htmlspecialchars($dept['dept_name']) ?> (<?= htmlspecialchars($dept['dept_code']) ?>)
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div>
                  <label class="hr-field-label" >Security Clearance Tier *</label>
                  <select class="hr-form-control" name="clearance_level" required >
                    <option value="L1">Level 1 · General Public</option>
                    <option value="L2">Level 2 · Confidential</option>
                    <option value="L3" selected>Level 3 · Secret SCADA</option>
                    <option value="L4">Level 4 · Top Secret Executive</option>
                  </select>
                </div>
              </div>

              <div>
                <label class="hr-field-label" >Job Title / Engineering Function *</label>
                <input class="hr-form-control" type="text" name="job_title" required placeholder="e.g. Senior Optical Systems Physicist"  />
              </div>

              <div class="hr-grid-2col-sm" >
                <div>
                  <label class="hr-field-label" >Corporate Email (Optional)</label>
                  <input class="hr-form-control" type="email" name="email" placeholder="Auto-generated if empty"  />
                </div>

                <div>
                  <label class="hr-field-label" >Direct Manager</label>
                  <select class="hr-form-control" name="manager_emp_id" >
                    <option value="">-- No Direct Manager --</option>
                    <?php foreach ($employees as $mgr): ?>
                      <option value="<?= htmlspecialchars($mgr['emp_id']) ?>">
                        <?= htmlspecialchars($mgr['full_name']) ?> (<?= htmlspecialchars($mgr['emp_id']) ?>)
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="hr-grid-2col-sm" >
                <div>
                  <label class="hr-field-label" >Initial Account Password</label>
                  <input class="hr-form-control-mono" type="text" name="password" value="Vostok2026!"  />
                </div>
                <div>
                  <label class="hr-field-label" >Hire Date</label>
                  <input type="date" name="hire_date" value="<?= date('Y-m-d') ?>" class="hr-form-control" />
                </div>
              </div>

              <div class="hr-flex-end-gap-75" >
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
</body>

</html>