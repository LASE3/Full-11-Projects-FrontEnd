<?php

/**
 * VOSTOKPRIBOR HR System - Offboarding & Security Clearance Revocation
 * Database-driven offboarding case management, credential locking, and audit progression.
 */
require_once __DIR__ . '/hr_service.php';
requireAuth('HR');

$currUser            = hr_getCurrentUser();
$offboardingCases    = hr_getOffboardingCases();
$offboardingStats    = hr_getOffboardingStats();
$activeEmployees     = hr_getEmployees('', '', '', 'Active');
$terminatedEmployees = hr_getTerminatedEmployees();
$canManage           = hr_canManageHR();

$nameParts = explode(' ', trim($currUser['full_name']));
$initials  = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR HR System · Offboarding &amp; Security Revocation</title>
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
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search offboarding records..." />
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
            <a href="Offboarding.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left"><span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span><span>Offboarding &amp; Revocation</span></div>
              <span class="sidebar-pill alert"><?= $offboardingStats['active_cases'] ?></span>
            </a>
            <a href="LeaveManagement.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span><span>Leave Management</span></div>
            </a>
            <a href="OrgStructure.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="2" y1="20" x2="22" y2="20"/><line x1="4" y1="10" x2="4" y2="20"/><line x1="9" y1="10" x2="9" y2="20"/><line x1="15" y1="10" x2="15" y2="20"/><line x1="20" y1="10" x2="20" y2="20"/><polygon points="12 2 2 7 22 7 12 2"/></svg>️</span><span>Org Hierarchy</span></div>
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
                <span class="breadcrumb-current">Offboarding &amp; Security Revocation</span>
              </div>
              <h1 class="page-title">Offboarding Workflow &amp; Access Revocation</h1>
              <p class="page-subtitle">Security clearance revocation, hardware asset recovery, and automated SSO account de-provisioning</p>
            </div>
            <div class="page-header-actions">
              <?php if ($canManage): ?>
                <button class="btn btn-primary-amber" onclick="window.hrApp.openModal('modal-initiate-offboarding')">
                  <span><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Initiate Offboarding / Revoke Access</span>
                </button>
              <?php endif; ?>
            </div>
          </div>

          <!-- Top Row KPI Cards - 100% Database Driven -->
          <div class="kpi-grid">
            <div class="hr-card kpi-card hr-card-confidential-top" >
              <div class="kpi-header">
                <span class="kpi-title">Active Offboardings</span>
                <div class="kpi-icon-pill red"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= $offboardingStats['active_cases'] ?></span>
                <span class="hr-text-muted-13" >Cases in DB</span>
              </div>
              <div class="kpi-footer">
                <span>Security Clearance Gated</span>
                <span class="kpi-trend alert"><?= $offboardingStats['active_cases'] > 0 ? 'Active Workflow' : 'Clear' ?></span>
              </div>
            </div>

            <div class="hr-card kpi-card hr-card-amber-top" >
              <div class="kpi-header">
                <span class="kpi-title">Completed Revocations</span>
                <div class="kpi-icon-pill amber"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18"/><path d="m3 9 4-6 4 6a4 4 0 0 1-8 0Z"/><path d="m13 9 4-6 4 6a4 4 0 0 1-8 0Z"/><path d="M4 21h16"/></svg>️</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= $offboardingStats['completed_cases'] ?></span>
                <span class="hr-text-muted-13" >Finalized</span>
              </div>
              <div class="kpi-footer">
                <span>Audit Logs Logged</span>
                <span class="kpi-trend up"><?= $offboardingStats['total_steps'] ?> Total Steps</span>
              </div>
            </div>

            <div class="hr-card kpi-card hr-card-steel-top" >
              <div class="kpi-header">
                <span class="kpi-title">Suspended Accounts</span>
                <div class="kpi-icon-pill steel"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= $offboardingStats['suspended_accounts'] ?></span>
                <span class="hr-text-muted-13" >Accounts</span>
              </div>
              <div class="kpi-footer">
                <span>employee_accounts status</span>
                <span class="kpi-trend alert">Locked Out</span>
              </div>
            </div>

            <div class="hr-card kpi-card hr-card-plum-top" >
              <div class="kpi-header">
                <span class="kpi-title">Terminated Personnel</span>
                <div class="kpi-icon-pill plum">📜</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= $offboardingStats['terminated_employees'] ?></span>
                <span class="hr-text-muted-13" >Records</span>
              </div>
              <div class="kpi-footer">
                <span>employees status</span>
                <span class="kpi-trend up">Archived</span>
              </div>
            </div>
          </div>

          <?php if (empty($offboardingCases)): ?>
            <div class="hr-card hr-empty-box-lg" >
              <h3>No Active Offboarding Cases</h3>
              <p class="hr-desc-muted-mb" >All employee accounts and security tokens are in active standing in MySQL.</p>
              <?php if ($canManage): ?>
                <button class="btn btn-primary-amber" onclick="window.hrApp.openModal('modal-initiate-offboarding')">+ Initiate Personnel Offboarding</button>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <!-- Offboarding Cases List -->
            <div class="hr-flex-col-gap-15" >
              <?php foreach ($offboardingCases as $case): ?>
                <div class="hr-card hr-p-6" >
                  <!-- Case Header -->
                  <div class="hr-offboarding-hero-row" >
                    <div class="hr-flex-gap-1" >
                      <div class="hr-avatar-48-red" >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                      </div>
                      <div>
                        <div class="hr-flex-wrap-sm" >
                          <h3 class="hr-title-16-navy" ><?= htmlspecialchars($case['full_name']) ?></h3>
                          <span class="status-pill status-offboarding"><?= htmlspecialchars($case['employment_status']) ?></span>
                          <span class="clearance-badge clearance-l<?= substr($case['clearance_level'], 1) ?>">Level <?= substr($case['clearance_level'], 1) ?></span>
                        </div>
                        <div class="hr-meta-desc-muted" >
                          <?= htmlspecialchars($case['job_title']) ?> · <strong><?= htmlspecialchars($case['dept_name']) ?></strong> · ID: <span class="hr-mono-plum" ><?= htmlspecialchars($case['emp_id']) ?></span>
                        </div>
                      </div>
                    </div>

                    <div class="hr-flex-gap-15" >
                      <div class="hr-text-right" >
                        <div class="hr-caption-muted">Revocation Progress</div>
                        <div class="hr-mono-title-red" >
                          <?= $case['progress_percent'] ?>% (<?= $case['completed_steps'] ?>/<?= $case['total_steps'] ?>)
                        </div>
                        <div class="hr-meta-subtext" >
                          Account Status: <strong class="hr-text-red" ><?= htmlspecialchars($case['account_status'] ?? 'Suspended') ?></strong>
                        </div>
                      </div>

                      <?php if ($canManage): ?>
                        <div class="hr-flex-col-gap-xs" >
                          <button class="btn btn-sm hr-badge-offboarding-ok" 
                            onclick="window.hrApp.completeOffboarding('<?= htmlspecialchars($case['emp_id']) ?>')"
                            title="Complete all steps and set status to Terminated in MySQL">
                            ✓ Complete &amp; Terminate
                          </button>
                          <button class="btn btn-outline btn-sm hr-btn-border-red-40" 
                            onclick="window.hrApp.deleteEmployee('<?= htmlspecialchars($case['emp_id']) ?>', '<?= addslashes($case['full_name']) ?>')"
                            title="Purge record or mark Terminated in MySQL">
                            🗑 Remove Record
                          </button>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>

                  <!-- 10 Step Checklist Grid -->
                  <div class="hr-grid-offboarding-cards" >
                    <?php
                    $stepTitles = [
                      'HRInitiated'        => '1. HR Notification',
                      'StatusChanged'      => '2. Status Suspended',
                      'ITNotified'         => '3. IT Security Alert',
                      'AccessRevoked'      => '4. SCADA Revoked',
                      'IntranetRevoked'    => '5. Intranet Locked',
                      'FileCenterReviewed' => '6. Files Archived',
                      'CRMRevoked'         => '7. CRM Locked',
                      'HelpdeskClosed'     => '8. Tickets Reassigned',
                      'GovernanceVerified' => '9. Governance Signoff',
                      'AuditLogged'        => '10. Audit Hashed'
                    ];

                    foreach ($case['steps'] as $st):
                      $isDone = ($st['status'] === 'Completed');
                      $isInProg = ($st['status'] === 'In Progress');
                      $cardBg = $isDone ? 'rgba(46,125,50,0.08)' : ($isInProg ? 'rgba(217,119,6,0.08)' : 'var(--hr-surface-dim)');
                      $borderCol = $isDone ? '#2E7D32' : ($isInProg ? '#D97706' : 'var(--hr-surface-border)');
                    ?>
                      <div style="background: <?= $cardBg ?>; border: 1px solid <?= $borderCol ?>; border-radius: var(--hr-radius-sm); padding: 0.65rem 0.85rem; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                          <div class="hr-semibold-navy-115" >
                            <?= htmlspecialchars($stepTitles[$st['step']] ?? $st['step']) ?>
                          </div>
                          <div class="hr-meta-muted-10" >
                            <?= htmlspecialchars($st['status']) ?>
                          </div>
                        </div>
                        <?php if ($canManage): ?>
                          <button class="btn btn-outline btn-sm hr-pill-mini" 
                            onclick="window.hrApp.advanceOffboarding('<?= htmlspecialchars($case['emp_id']) ?>', '<?= htmlspecialchars($st['step']) ?>', '<?= $isDone ? 'Pending' : 'Completed' ?>')">
                            <?= $isDone ? '✓ Done' : 'Complete' ?>
                          </button>
                        <?php endif; ?>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <!-- Terminated & Suspended Personnel History -->
          <div class="hr-card hr-panel-flush-mt" >
            <div class="hr-table-header-bar" >
              <div>
                <h3 class="card-title">Terminated &amp; Suspended Personnel Roster</h3>
                <p class="hr-meta-subtext" >
                  Archived records of personnel departed or decommissioned in MySQL
                </p>
              </div>
              <span class="status-pill status-offboarding hr-text-11" >
                <?= count($terminatedEmployees) ?> Archived Records
              </span>
            </div>

            <table class="employee-table">
              <thead>
                <tr>
                  <th class="hr-w-100" >EMP ID</th>
                  <th>Employee Name</th>
                  <th>Department</th>
                  <th>Clearance</th>
                  <th>Status in MySQL</th>
                  <th>Account Status</th>
                  <th class="hr-w-130-right" >Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($terminatedEmployees)): ?>
                  <tr>
                    <td class="hr-empty-state" colspan="7" >
                      No terminated or suspended personnel recorded in database.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($terminatedEmployees as $te): ?>
                    <tr>
                      <td class="hr-mono-bold-confidential" >
                        <?= htmlspecialchars($te['emp_id']) ?>
                      </td>
                      <td>
                        <div class="hr-bold-navy" ><?= htmlspecialchars($te['full_name']) ?></div>
                        <div class="hr-text-muted-11" ><?= htmlspecialchars($te['job_title']) ?></div>
                      </td>
                      <td><?= htmlspecialchars($te['dept_name'] ?? $te['department_code']) ?></td>
                      <td>
                        <span class="clearance-badge clearance-l<?= substr($te['clearance_level'], 1) ?>">
                          Level <?= substr($te['clearance_level'], 1) ?>
                        </span>
                      </td>
                      <td>
                        <span class="status-pill status-offboarding"><?= htmlspecialchars($te['employment_status']) ?></span>
                      </td>
                      <td>
                        <span class="hr-text-red-bold-11" >
                          <?= htmlspecialchars($te['account_status'] ?? 'Locked') ?>
                        </span>
                      </td>
                      <td class="hr-text-right" >
                        <?php if ($canManage): ?>
                          <button class="btn btn-outline btn-sm hr-btn-border-red-40" 
                            onclick="window.hrApp.deleteEmployee('<?= htmlspecialchars($te['emp_id']) ?>', '<?= addslashes($te['full_name']) ?>')">
                            Purge Record
                          </button>
                        <?php endif; ?>
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
    <!-- MODAL: INITIATE OFFBOARDING / REMOVE PERSONNEL -->
    <div id="modal-initiate-offboarding" class="modal-backdrop">
      <div class="modal-card hr-modal-560" >
        <div class="modal-header hr-modal-header-confidential" >
          <div>
            <h3 class="modal-title hr-text-white" >Initiate Offboarding &amp; Revoke Access</h3>
            <p class="hr-text-rose-sm" >Locks SSO Credentials · Begins 10-Step Security Decommissioning</p>
          </div>
          <button class="modal-close-btn hr-text-white"  onclick="window.hrApp.closeModal('modal-initiate-offboarding')">&times;</button>
        </div>
        <div class="modal-body hr-p-6" >
          <form id="form-initiate-offboarding" onsubmit="return window.hrApp.handleInitiateOffboarding(event, this);">
            <div class="hr-flex-col-gap-md" >
              <div>
                <label class="form-label">Select Active Employee to Offboard <span class="hr-text-confidential" >*</span></label>
                <select name="emp_id" class="form-select" required>
                  <option value="">-- Choose active employee from MySQL --</option>
                  <?php foreach ($activeEmployees as $ae): ?>
                    <option value="<?= htmlspecialchars($ae['emp_id']) ?>">
                      <?= htmlspecialchars($ae['full_name']) ?> (<?= htmlspecialchars($ae['emp_id']) ?> · <?= htmlspecialchars($ae['job_title']) ?> · <?= htmlspecialchars($ae['dept_name']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div>
                <label class="form-label">Separation Reason <span class="hr-text-confidential" >*</span></label>
                <select name="reason" class="form-select" required>
                  <option value="Resignation">Voluntary Resignation</option>
                  <option value="Contract Expiration">Contract Term Expiration</option>
                  <option value="Security Revocation">Security Clearance Revocation / SecOps Action</option>
                  <option value="Retirement">Executive Retirement</option>
                  <option value="Administrative Separation">Administrative Separation / Termination</option>
                </select>
              </div>

              <div class="hr-notice-offboarding" >
                <strong>Security Protocol Warning:</strong> Submitting will immediately switch the employee status to <code>Suspended</code>, lock login credentials in <code>employee_accounts</code>, and generate the 10-step audit revocation checklist in MySQL.
              </div>

              <div class="hr-flex-end-gap-md" >
                <button type="button" class="btn btn-outline" onclick="window.hrApp.closeModal('modal-initiate-offboarding')">Cancel</button>
                <button type="submit" class="btn btn-plum hr-btn-danger-solid" >
                  <span>Confirm &amp; Suspend Access <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
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
  <script src="../assets/js/notifications-hub.js" defer></script>
</body>

</html>