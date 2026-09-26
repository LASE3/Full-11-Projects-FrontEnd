<?php

/**
 * VOSTOKPRIBOR HR System - Onboarding Pipeline & Security Clearance Gating
 * Database-driven tracking of sequential onboarding workflows from MySQL.
 */
require_once __DIR__ . '/hr_service.php';
requireAuth('HR');

$currUser        = hr_getCurrentUser();
$candidates      = hr_getOnboardingCandidates();
$onboardingStats = hr_getOnboardingStats();
$departments     = hr_getDepartments();
$unonboarded     = hr_getUnonboardedEmployees();
$canManage       = hr_canManageHR();

$selectedEmpId = $_GET['emp_id'] ?? ($candidates[0]['emp_id'] ?? '');
$selectedCand  = null;
foreach ($candidates as $c) {
  if ($c['emp_id'] === $selectedEmpId) {
    $selectedCand = $c;
    break;
  }
}
if (!$selectedCand && !empty($candidates)) {
  $selectedCand = $candidates[0];
  $selectedEmpId = $selectedCand['emp_id'];
}

$nameParts = explode(' ', trim($currUser['full_name']));
$initials  = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR HR System · Onboarding Pipeline</title>
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
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search candidate records..." />
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
            <a href="OnboardingTracker.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left"><span class="sidebar-icon">⚡</span><span>Onboarding Pipeline</span></div>
              <span class="sidebar-pill amber"><?= $onboardingStats['active_count'] ?></span>
            </a>
            <a href="Offboarding.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon">🔒</span><span>Offboarding &amp; Revocation</span></div>
            </a>
            <a href="LeaveManagement.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon">📅</span><span>Leave Management</span></div>
            </a>
            <a href="OrgStructure.php" class="sidebar-nav-item">
              <div class="sidebar-item-left"><span class="sidebar-icon">🏛️</span><span>Org Hierarchy</span></div>
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
                <span class="breadcrumb-current">Recruitment &amp; Onboarding Tracker</span>
              </div>
              <h1 class="page-title">Candidate Onboarding &amp; Security Clearance Gating</h1>
              <p class="page-subtitle">Track sequential provisioning, cryptographic token assignment, and compliance gating for personnel</p>
            </div>
            <div class="page-header-actions">
              <?php if ($canManage): ?>
                <button class="btn btn-primary-amber" onclick="window.hrApp.openModal('modal-add-onboarding')">
                  <span>+ New Onboarding Pipeline</span>
                </button>
              <?php endif; ?>
            </div>
          </div>

          <!-- Dynamic Database Metrics Strip -->
          <div class="kpi-grid">
            <div class="hr-card kpi-card hr-card-plum-top" >
              <div class="kpi-header">
                <span class="kpi-title">Active Pipelines</span>
                <div class="kpi-icon-pill plum">⚡</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= $onboardingStats['active_count'] ?></span>
                <span class="hr-text-muted-13" >In Progress</span>
              </div>
              <div class="kpi-footer">
                <span>Candidates in Vetting</span>
                <span class="kpi-trend up">Live</span>
              </div>
            </div>

            <div class="hr-card kpi-card hr-card-steel-top" >
              <div class="kpi-header">
                <span class="kpi-title">Completed Onboardings</span>
                <div class="kpi-icon-pill steel">✓</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= $onboardingStats['completed_count'] ?></span>
                <span class="hr-text-muted-13" >Fully Vetted</span>
              </div>
              <div class="kpi-footer">
                <span>Security Clearance Granted</span>
                <span class="kpi-trend up">Compliant</span>
              </div>
            </div>

            <div class="hr-card kpi-card hr-card-amber-top" >
              <div class="kpi-header">
                <span class="kpi-title">Milestones Recorded</span>
                <div class="kpi-icon-pill amber">📜</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= $onboardingStats['completed_steps'] ?> / <?= $onboardingStats['total_steps'] ?></span>
                <span class="hr-text-muted-13" >Completed</span>
              </div>
              <div class="kpi-footer">
                <span>Recorded in MySQL</span>
                <span class="kpi-trend up">Synced</span>
              </div>
            </div>

            <div class="hr-card kpi-card hr-card-navy-top" >
              <div class="kpi-header">
                <span class="kpi-title">Total Personnel Enrolled</span>
                <div class="kpi-icon-pill navy">👥</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= $onboardingStats['total_candidates'] ?></span>
                <span class="hr-text-muted-13" >Employees</span>
              </div>
              <div class="kpi-footer">
                <span>Directory Coverage</span>
                <span class="kpi-trend up">Active</span>
              </div>
            </div>
          </div>

          <?php if (empty($candidates)): ?>
            <div class="hr-card hr-empty-box-lg" >
              <h3>No Active Onboarding Pipelines</h3>
              <p class="hr-desc-muted-mb" >All registered employees have completed security vetting and onboarding.</p>
              <?php if ($canManage): ?>
                <button class="btn btn-primary-amber" onclick="window.hrApp.openModal('modal-add-onboarding')">+ Add Employee to Onboarding Pipeline</button>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <!-- Candidate Switcher Tabs -->
            <div class="hr-tab-bar-scrollable" >
              <?php foreach ($candidates as $cand):
                $isActiveTab = ($cand['emp_id'] === $selectedEmpId);
              ?>
                <a href="?emp_id=<?= urlencode($cand['emp_id']) ?>"
                  class="btn <?= $isActiveTab ? '' : 'btn-outline' ?>"
                  style="font-size: 12px; <?= $isActiveTab ? 'background: var(--hr-plum); color: #FFF; border: 1px solid var(--hr-plum); box-shadow: 0 2px 6px var(--hr-plum-glow);' : '' ?>">
                  <span><?= $isActiveTab ? '●' : '○' ?> <?= htmlspecialchars($cand['full_name']) ?> (<?= $cand['completed_steps'] ?>/<?= $cand['total_steps'] ?> · <?= htmlspecialchars($cand['dept_name']) ?>)</span>
                </a>
              <?php endforeach; ?>
            </div>

            <!-- Candidate Header Component -->
            <div class="onboarding-candidate-card hr-card-hero-banner" >
              <div class="hr-flex-gap-125" >
                <div class="hr-avatar-64-plum" >
                  <?= strtoupper(substr($selectedCand['full_name'], 0, 1)) ?>
                </div>
                <div>
                  <div class="hr-flex-gap-wrap-mb" >
                    <h2 class="hr-title-18-navy" ><?= htmlspecialchars($selectedCand['full_name']) ?></h2>
                    <span class="status-pill status-probation">Onboarding In Progress</span>
                    <span class="clearance-badge clearance-l<?= substr($selectedCand['clearance_level'], 1) ?>">
                      <span>🔒</span> Level <?= substr($selectedCand['clearance_level'], 1) ?> · <?= htmlspecialchars($selectedCand['clearance_level']) ?>
                    </span>
                  </div>
                  <div class="hr-desc-cand" >
                    <?= htmlspecialchars($selectedCand['job_title']) ?> · <strong class="hr-text-navy" ><?= htmlspecialchars($selectedCand['dept_name']) ?></strong>
                  </div>
                  <div class="hr-meta-row-cand" >
                    <span>EMP ID: <strong class="hr-mono-navy" ><?= htmlspecialchars($selectedCand['emp_id']) ?></strong></span>
                    <span>Hire Date: <strong class="hr-text-navy" ><?= htmlspecialchars($selectedCand['hire_date'] ?? date('Y-m-d')) ?></strong></span>
                  </div>
                </div>
              </div>

              <!-- Progress Metric Box -->
              <div class="hr-progress-summary-box" >
                <div class="hr-caption-progress" >Onboarding Completion</div>
                <div class="hr-mono-percent-plum" >
                  <?= $selectedCand['progress_percent'] ?>% · <?= $selectedCand['completed_steps'] ?>/<?= $selectedCand['total_steps'] ?>
                </div>
                <div class="hr-progress-track" >
                  <div style="width: <?= $selectedCand['progress_percent'] ?>%; height: 100%; background: linear-gradient(90deg, #7B2CBF, #9D4EDD); border-radius: 4px;"></div>
                </div>
              </div>
            </div>

            <!-- Sequential Step Checklist -->
            <div class="hr-card hr-card-body-padded" >
              <div class="card-header-row hr-mb-6" >
                <div>
                  <h3 class="card-title">Sequential Security Gating &amp; Provisioning Workflow</h3>
                  <p class="hr-meta-subtext" >
                    Click any stage button or status toggle to update MySQL database state in real-time
                  </p>
                </div>
                <span class="status-pill status-active hr-text-xs" >
                  MySQL Synced
                </span>
              </div>

              <div class="step-tracker-container">
                <?php
                $stepDescriptions = [
                  'RecordCreated'        => ['title' => 'Stage 1: Personnel Dossier Initialized', 'desc' => 'Employee record created in central MySQL directory with national identification & academic credentials.'],
                  'AccessRequested'      => ['title' => 'Stage 2: Access & Credentials Requested', 'desc' => 'SecOps notified to provision domain credentials, corporate LDAP token, and access keys.'],
                  'IntranetGranted'      => ['title' => 'Stage 3: Employee Intranet & Portal Active', 'desc' => 'Single sign-on token configured for corporate intranet, SOP manuals, and employee directory.'],
                  'SystemAccessGranted'  => ['title' => 'Stage 4: Role-Based System Access Provisioned', 'desc' => 'Granular permissions assigned across authorized enterprise applications based on role catalog.'],
                  'DocumentsFiled'       => ['title' => 'Stage 5: Classified NDAs & Contracts Executed', 'desc' => 'Employment agreement, non-disclosure addenda, and security compliance agreements signed.'],
                  'GovernanceReviewed'   => ['title' => 'Stage 6: Executive Governance & Security Signoff', 'desc' => 'Chief Governance Officer verifies clearance level, audits telemetry, and grants final clearance.']
                ];

                foreach ($selectedCand['steps'] as $idx => $step):
                  $info = $stepDescriptions[$step['step']] ?? ['title' => $step['step'], 'desc' => 'Sequential compliance gating milestone.'];
                  $isDone = ($step['status'] === 'Completed');
                  $isInProg = ($step['status'] === 'In Progress');
                  $itemClass = $isDone ? 'completed' : ($isInProg ? 'in-progress' : 'pending');
                  $iconChar = $isDone ? '✓' : ($isInProg ? '⚡' : '○');
                  $badgeClass = $isDone ? 'status-completed' : ($isInProg ? 'status-progress' : 'status-pending');
                ?>
                  <div class="step-item <?= $itemClass ?>">
                    <div class="step-node-icon"
                      onclick="window.hrApp.advanceOnboarding('<?= htmlspecialchars($selectedCand['emp_id']) ?>', '<?= htmlspecialchars($step['step']) ?>', '<?= $isDone ? 'In Progress' : 'Completed' ?>')"
                      title="Click to toggle status in MySQL">
                      <?= $iconChar ?>
                    </div>
                    <div class="step-item-card">
                      <div class="step-item-header">
                        <div>
                          <div class="step-title"><?= htmlspecialchars($info['title']) ?></div>
                          <div class="step-subtitle"><?= htmlspecialchars($info['desc']) ?></div>
                        </div>
                        <div class="hr-flex-gap-sm" >
                          <span class="step-badge-status <?= $badgeClass ?>"><?= strtoupper(htmlspecialchars($step['status'])) ?></span>
                          <?php if ($canManage): ?>
                            <button class="btn btn-outline btn-sm"
                              onclick="window.hrApp.advanceOnboarding('<?= htmlspecialchars($selectedCand['emp_id']) ?>', '<?= htmlspecialchars($step['step']) ?>', '<?= $isDone ? 'In Progress' : 'Completed' ?>')">
                              <?= $isDone ? 'Reopen Step' : 'Mark Completed ✓' ?>
                            </button>
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="step-meta-row">
                        <span>Database Status: <strong><?= htmlspecialchars($step['status']) ?></strong></span>
                        <span>•</span>
                        <span>Timestamp: <strong><?= date('M d, Y · H:i', strtotime($step['completed_at'])) ?></strong></span>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </div>

  <!-- ADD EMPLOYEE & INITIALIZE ONBOARDING MODAL -->
  <div class="modal-backdrop" id="modal-add-onboarding">
    <div class="modal-card hr-modal-640" >
      <div class="modal-header">
        <div>
          <h3 class="modal-title">New Onboarding Pipeline · Add Personnel</h3>
          <p class="hr-meta-desc-muted" >
            Provision a new hire or enroll an active employee into the security onboarding workflow
          </p>
        </div>
        <button class="modal-close-btn" onclick="window.hrApp.closeModal('modal-add-onboarding')">&times;</button>
      </div>

      <div class="modal-body hr-p-6" >
        <!-- Option Tabs -->
        <div class="hr-modal-tab-bar" >
          <button type="button" id="tab-btn-new-hire" class="btn hr-tab-btn hr-tab-active" onclick="switchOnboardTab('new')">
            Add New Employee
          </button>
          <button type="button" id="tab-btn-enroll-existing" class="btn btn-outline hr-tab-btn hr-tab-inactive" onclick="switchOnboardTab('existing')">
            Enroll Existing Staff (<?= count($unonboarded) ?>)
          </button>
        </div>

        <!-- TAB 1: ADD NEW EMPLOYEE -->
        <form id="form-add-onboard-employee" onsubmit="window.hrApp.submitAddEmployee(event)">
          <div class="hr-grid-2col-gap" >
            <div class="form-group hr-col-span-2" >
              <label class="form-label">Full Name <span class="hr-text-confidential" >*</span></label>
              <input type="text" name="full_name" class="form-input" placeholder="e.g. Elena Vasilyeva" required />
            </div>

            <div class="form-group">
              <label class="form-label">Email Address <span class="hr-text-confidential" >*</span></label>
              <input type="email" name="email" class="form-input" placeholder="e.vasilyeva@vostokpribor.local" required />
            </div>

            <div class="form-group">
              <label class="form-label">Job Title <span class="hr-text-confidential" >*</span></label>
              <input type="text" name="job_title" class="form-input" placeholder="e.g. Calibration Engineer" required />
            </div>

            <div class="form-group">
              <label class="form-label">Department <span class="hr-text-confidential" >*</span></label>
              <select name="department_code" class="form-select" required>
                <?php foreach ($departments as $d): ?>
                  <option value="<?= htmlspecialchars($d['dept_code']) ?>">
                    <?= htmlspecialchars($d['dept_name']) ?> (<?= htmlspecialchars($d['dept_code']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Security Clearance Tier <span class="hr-text-confidential" >*</span></label>
              <select name="clearance_level" class="form-select" required>
                <option value="L1">Level 1 (L1) - Basic Personnel</option>
                <option value="L2" selected>Level 2 (L2) - Operational Access</option>
                <option value="L3">Level 3 (L3) - Departmental Admin</option>
                <option value="L4">Level 4 (L4) - Executive SuperAdmin</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Hire Date</label>
              <input type="date" name="hire_date" class="form-input" value="<?= date('Y-m-d') ?>" />
            </div>

            <div class="form-group">
              <label class="form-label">Initial Password</label>
              <input type="text" name="password" class="form-input" value="Vostok2026!" />
            </div>
          </div>

          <div class="hr-flex-end-mt6" >
            <button type="button" class="btn btn-outline" onclick="window.hrApp.closeModal('modal-add-onboarding')">Cancel</button>
            <button type="submit" class="btn btn-primary-amber">Save Employee &amp; Start Onboarding</button>
          </div>
        </form>

        <!-- TAB 2: ENROLL EXISTING EMPLOYEE -->
        <div id="section-enroll-existing" class="hidden">
          <?php if (empty($unonboarded)): ?>
            <div class="hr-empty-state" >
              <p>All active employees currently registered in MySQL are already enrolled in onboarding pipelines.</p>
            </div>
          <?php else: ?>
            <div class="form-group">
              <label class="form-label">Select Active Employee</label>
              <select id="select-enroll-emp" class="form-select">
                <?php foreach ($unonboarded as $u): ?>
                  <option value="<?= htmlspecialchars($u['emp_id']) ?>">
                    <?= htmlspecialchars($u['full_name']) ?> (<?= htmlspecialchars($u['emp_id']) ?>) · <?= htmlspecialchars($u['dept_name']) ?> · Level <?= $u['clearance_level'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="hr-flex-end-mt6" >
              <button type="button" class="btn btn-outline" onclick="window.hrApp.closeModal('modal-add-onboarding')">Cancel</button>
              <button type="button" class="btn btn-primary-amber" onclick="window.hrApp.enrollOnboarding(document.getElementById('select-enroll-emp').value)">Enroll Employee into Pipeline</button>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
</body>

</html>