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
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search offboarding records..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <div class="top-nav__actions">
          <div class="confidential-system-pill" title="Restricted Personnel System">
            <span>🔒</span>
            <span>HIGHLY CONFIDENTIAL SYSTEM</span>
          </div>

          <div class="top-user-profile" title="Active Session: <?= htmlspecialchars($currUser['full_name']) ?>">
            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg, #7A284E 0%, #3D1427 100%);color:#FFFFFF;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12.5px;border:2px solid #FF8080;">
              <?= $initials ?>
            </div>
            <div class="user-details-top">
              <span class="user-name-top"><?= htmlspecialchars($currUser['full_name']) ?></span>
              <span class="user-role-top"><?= htmlspecialchars($currUser['role_name'] ?? 'Authorized User') ?> · <?= htmlspecialchars($currUser['clearance_level'] ?? 'L1') ?></span>
            </div>
          </div>

          <a href="../api/logout.php?system=HR%20System&redirect=../HR%20System/login.php" class="top-signout-btn" title="Sign Out" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:4px;background:rgba(178,58,50,0.2);border:1px solid rgba(178,58,50,0.5);color:#FF8080;font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;margin-left:8px;vertical-align:middle;">
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
            <a href="Dashboard.php" class="sidebar-nav-item"><div class="sidebar-item-left"><span class="sidebar-icon">📊</span><span>Dashboard</span></div></a>
            <a href="EmployeeRecords.php" class="sidebar-nav-item"><div class="sidebar-item-left"><span class="sidebar-icon">👥</span><span>Employee Records</span></div></a>
            <a href="OnboardingTracker.php" class="sidebar-nav-item"><div class="sidebar-item-left"><span class="sidebar-icon">⚡</span><span>Onboarding Pipeline</span></div></a>
            <a href="Offboarding.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left"><span class="sidebar-icon">🔒</span><span>Offboarding &amp; Revocation</span></div>
              <span class="sidebar-pill alert"><?= $offboardingStats['active_cases'] ?></span>
            </a>
            <a href="LeaveManagement.php" class="sidebar-nav-item"><div class="sidebar-item-left"><span class="sidebar-icon">📅</span><span>Leave Management</span></div></a>
            <a href="OrgStructure.php" class="sidebar-nav-item"><div class="sidebar-item-left"><span class="sidebar-icon">🏛️</span><span>Org Hierarchy</span></div></a>
            <a href="Training.php" class="sidebar-nav-item"><div class="sidebar-item-left"><span class="sidebar-icon">🎓</span><span>Training &amp; Certs</span></div></a>
          </nav>
        </div>
      </aside>

      <!-- MAIN CONTENT -->
      <main class="content-wrapper">
        <div class="portal-container" style="max-width: 1200px;">
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
                  <span>🔒 Initiate Offboarding / Revoke Access</span>
                </button>
              <?php endif; ?>
            </div>
          </div>

          <!-- Top Row KPI Cards - 100% Database Driven -->
          <div class="kpi-grid">
            <div class="hr-card kpi-card" style="border-top: 3px solid var(--hr-confidential);">
              <div class="kpi-header">
                <span class="kpi-title">Active Offboardings</span>
                <div class="kpi-icon-pill red">🔒</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= $offboardingStats['active_cases'] ?></span>
                <span style="font-size: 13px; color: var(--hr-text-muted);">Cases in DB</span>
              </div>
              <div class="kpi-footer">
                <span>Security Clearance Gated</span>
                <span class="kpi-trend alert"><?= $offboardingStats['active_cases'] > 0 ? 'Active Workflow' : 'Clear' ?></span>
              </div>
            </div>

            <div class="hr-card kpi-card" style="border-top: 3px solid var(--hr-amber);">
              <div class="kpi-header">
                <span class="kpi-title">Completed Revocations</span>
                <div class="kpi-icon-pill amber">⚖️</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= $offboardingStats['completed_cases'] ?></span>
                <span style="font-size: 13px; color: var(--hr-text-muted);">Finalized</span>
              </div>
              <div class="kpi-footer">
                <span>Audit Logs Logged</span>
                <span class="kpi-trend up"><?= $offboardingStats['total_steps'] ?> Total Steps</span>
              </div>
            </div>

            <div class="hr-card kpi-card" style="border-top: 3px solid var(--hr-steel-blue);">
              <div class="kpi-header">
                <span class="kpi-title">Suspended Accounts</span>
                <div class="kpi-icon-pill steel">⚡</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= $offboardingStats['suspended_accounts'] ?></span>
                <span style="font-size: 13px; color: var(--hr-text-muted);">Accounts</span>
              </div>
              <div class="kpi-footer">
                <span>employee_accounts status</span>
                <span class="kpi-trend alert">Locked Out</span>
              </div>
            </div>

            <div class="hr-card kpi-card" style="border-top: 3px solid var(--hr-plum);">
              <div class="kpi-header">
                <span class="kpi-title">Terminated Personnel</span>
                <div class="kpi-icon-pill plum">📜</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= $offboardingStats['terminated_employees'] ?></span>
                <span style="font-size: 13px; color: var(--hr-text-muted);">Records</span>
              </div>
              <div class="kpi-footer">
                <span>employees status</span>
                <span class="kpi-trend up">Archived</span>
              </div>
            </div>
          </div>

          <?php if (empty($offboardingCases)): ?>
            <div class="hr-card" style="text-align: center; padding: 3rem; margin-top: 1.5rem;">
              <h3>No Active Offboarding Cases</h3>
              <p style="color: var(--hr-text-muted); margin: 0.5rem 0 1.5rem;">All employee accounts and security tokens are in active standing in MySQL.</p>
              <?php if ($canManage): ?>
                <button class="btn btn-primary-amber" onclick="window.hrApp.openModal('modal-initiate-offboarding')">+ Initiate Personnel Offboarding</button>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <!-- Offboarding Cases List -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-top: 1.5rem;">
              <?php foreach ($offboardingCases as $case): ?>
                <div class="hr-card" style="padding: 1.5rem;">
                  <!-- Case Header -->
                  <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--hr-surface-border); padding-bottom: 1rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                      <div style="width: 48px; height: 48px; border-radius: var(--hr-radius-md); background: #B23A32; color: #FFF; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700;">
                        🔒
                      </div>
                      <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                          <h3 style="font-size: 16px; font-weight: 700; color: var(--hr-navy);"><?= htmlspecialchars($case['full_name']) ?></h3>
                          <span class="status-pill status-offboarding"><?= htmlspecialchars($case['employment_status']) ?></span>
                          <span class="clearance-badge clearance-l<?= substr($case['clearance_level'], 1) ?>">Level <?= substr($case['clearance_level'], 1) ?></span>
                        </div>
                        <div style="font-size: 12px; color: var(--hr-text-muted); margin-top: 2px;">
                          <?= htmlspecialchars($case['job_title']) ?> · <strong><?= htmlspecialchars($case['dept_name']) ?></strong> · ID: <span style="font-family: var(--hr-font-mono); color: var(--hr-plum);"><?= htmlspecialchars($case['emp_id']) ?></span>
                        </div>
                      </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 1.5rem;">
                      <div style="text-align: right;">
                        <div style="font-size: 11px; text-transform: uppercase; color: var(--hr-text-muted); font-weight: 600;">Revocation Progress</div>
                        <div style="font-size: 18px; font-weight: 700; color: var(--hr-confidential); font-family: var(--hr-font-mono);">
                          <?= $case['progress_percent'] ?>% (<?= $case['completed_steps'] ?>/<?= $case['total_steps'] ?>)
                        </div>
                        <div style="font-size: 11px; color: var(--hr-text-muted); margin-top: 2px;">
                          Account Status: <strong style="color: #B23A32;"><?= htmlspecialchars($case['account_status'] ?? 'Suspended') ?></strong>
                        </div>
                      </div>

                      <?php if ($canManage): ?>
                        <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                          <button class="btn btn-sm" style="background: #2E7D32; color: #FFF; border: 1px solid #2E7D32;"
                                  onclick="window.hrApp.completeOffboarding('<?= htmlspecialchars($case['emp_id']) ?>')"
                                  title="Complete all steps and set status to Terminated in MySQL">
                            ✓ Complete &amp; Terminate
                          </button>
                          <button class="btn btn-outline btn-sm" style="color: #B23A32; border-color: rgba(178,58,50,0.4);"
                                  onclick="window.hrApp.deleteEmployee('<?= htmlspecialchars($case['emp_id']) ?>', '<?= addslashes($case['full_name']) ?>')"
                                  title="Purge record or mark Terminated in MySQL">
                            🗑 Remove Record
                          </button>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>

                  <!-- 10 Step Checklist Grid -->
                  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.75rem;">
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
                          <div style="font-size: 11.5px; font-weight: 600; color: var(--hr-navy);">
                            <?= htmlspecialchars($stepTitles[$st['step']] ?? $st['step']) ?>
                          </div>
                          <div style="font-size: 10px; color: var(--hr-text-muted); margin-top: 2px;">
                            <?= htmlspecialchars($st['status']) ?>
                          </div>
                        </div>
                        <?php if ($canManage): ?>
                          <button class="btn btn-outline btn-sm" style="padding: 2px 6px; font-size: 10px;" 
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
          <div class="hr-card" style="padding: 0; overflow: hidden; margin-top: 2rem;">
            <div style="padding: 1rem 1.25rem; background-color: #FFFFFF; border-bottom: 1px solid var(--hr-surface-border); display: flex; align-items: center; justify-content: space-between;">
              <div>
                <h3 class="card-title">Terminated &amp; Suspended Personnel Roster</h3>
                <p style="font-size: 11.5px; color: var(--hr-text-secondary); margin-top: 2px;">
                  Archived records of personnel departed or decommissioned in MySQL
                </p>
              </div>
              <span class="status-pill status-offboarding" style="font-size: 11px;">
                <?= count($terminatedEmployees) ?> Archived Records
              </span>
            </div>

            <table class="employee-table">
              <thead>
                <tr>
                  <th style="width: 100px;">EMP ID</th>
                  <th>Employee Name</th>
                  <th>Department</th>
                  <th>Clearance</th>
                  <th>Status in MySQL</th>
                  <th>Account Status</th>
                  <th style="width: 130px; text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($terminatedEmployees)): ?>
                  <tr>
                    <td colspan="7" style="text-align:center; padding: 2rem; color: var(--hr-text-muted);">
                      No terminated or suspended personnel recorded in database.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($terminatedEmployees as $te): ?>
                    <tr>
                      <td style="font-family: var(--hr-font-mono); font-weight: 700; color: var(--hr-confidential);">
                        <?= htmlspecialchars($te['emp_id']) ?>
                      </td>
                      <td>
                        <div style="font-weight: 700; color: var(--hr-navy);"><?= htmlspecialchars($te['full_name']) ?></div>
                        <div style="font-size: 11px; color: var(--hr-text-muted);"><?= htmlspecialchars($te['job_title']) ?></div>
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
                        <span style="font-size: 11px; font-weight: 600; color: #B23A32;">
                          <?= htmlspecialchars($te['account_status'] ?? 'Locked') ?>
                        </span>
                      </td>
                      <td style="text-align: right;">
                        <?php if ($canManage): ?>
                          <button class="btn btn-outline btn-sm" style="color: #B23A32; border-color: rgba(178,58,50,0.4);"
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
    <div class="modal-card" style="max-width: 560px;">
      <div class="modal-header" style="background-color: var(--hr-navy); color: #FFFFFF; border-bottom: 3px solid var(--hr-confidential);">
        <div>
          <h3 class="modal-title" style="color: #FFFFFF;">Initiate Offboarding &amp; Revoke Access</h3>
          <p style="font-size: 11px; color: #FF8080; margin-top: 2px;">Locks SSO Credentials · Begins 10-Step Security Decommissioning</p>
        </div>
        <button class="modal-close-btn" style="color: #FFFFFF;" onclick="window.hrApp.closeModal('modal-initiate-offboarding')">&times;</button>
      </div>
      <div class="modal-body" style="padding: 1.5rem;">
        <form id="form-initiate-offboarding" onsubmit="return window.hrApp.handleInitiateOffboarding(event, this);">
          <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div>
              <label class="form-label">Select Active Employee to Offboard <span style="color:var(--hr-confidential);">*</span></label>
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
              <label class="form-label">Separation Reason <span style="color:var(--hr-confidential);">*</span></label>
              <select name="reason" class="form-select" required>
                <option value="Resignation">Voluntary Resignation</option>
                <option value="Contract Expiration">Contract Term Expiration</option>
                <option value="Security Revocation">Security Clearance Revocation / SecOps Action</option>
                <option value="Retirement">Executive Retirement</option>
                <option value="Administrative Separation">Administrative Separation / Termination</option>
              </select>
            </div>

            <div style="padding: 0.75rem; background: rgba(178,58,50,0.08); border-left: 3px solid #B23A32; border-radius: 4px; font-size: 11.5px; color: #B23A32;">
              <strong>Security Protocol Warning:</strong> Submitting will immediately switch the employee status to <code>Suspended</code>, lock login credentials in <code>employee_accounts</code>, and generate the 10-step audit revocation checklist in MySQL.
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
              <button type="button" class="btn btn-outline" onclick="window.hrApp.closeModal('modal-initiate-offboarding')">Cancel</button>
              <button type="submit" class="btn btn-plum" style="background: #B23A32; border-color: #B23A32; color: #FFF;">
                <span>Confirm &amp; Suspend Access 🔒</span>
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
