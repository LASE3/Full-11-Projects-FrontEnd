<?php

/**
 * VOSTOKPRIBOR HR System - Leave Management & Approvals
 * Database-driven statutory and technical absence tracking connected to MySQL.
 */
require_once __DIR__ . '/hr_service.php';
requireAuth('HR');

$currUser        = hr_getCurrentUser();
$leaveRequests   = hr_getLeaveRequests();
$activeEmployees = hr_getEmployees('', '', '', 'Active');
$canManage       = hr_canManageHR();

$pendingCount = 0;
$approvedCount = 0;
foreach ($leaveRequests as $lr) {
  if ($lr['status'] === 'Pending') $pendingCount++;
  if ($lr['status'] === 'Approved') $approvedCount++;
}

$nameParts = explode(' ', trim($currUser['full_name']));
$initials  = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR HR System · Leave Management &amp; Approvals</title>
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
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search leave records..." />
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
            <a href="LeaveManagement.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left"><span class="sidebar-icon">📅</span><span>Leave Management</span></div>
              <span class="sidebar-pill <?= $pendingCount > 0 ? 'alert' : '' ?>"><?= count($leaveRequests) ?></span>
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
                <span class="breadcrumb-current">Leave Approvals &amp; Coverage Schedule</span>
              </div>
              <h1 class="page-title">Leave Approvals &amp; Engineering Absence Schedule</h1>
              <p class="page-subtitle">Statutory annual leave, medical absence, and technical sabbatical workflows recorded in MySQL</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-primary-amber" onclick="window.hrApp.openModal('modal-create-leave')">
                <span>+ Submit Leave Request</span>
              </button>
            </div>
          </div>

          <!-- Leave KPI Strip -->
          <div class="kpi-grid hr-grid-3col" >
            <div class="hr-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Awaiting Approval</span>
                <div class="kpi-icon-pill amber">⚡</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= $pendingCount ?></span>
                <span class="hr-text-muted-13" >Requests</span>
              </div>
              <div class="kpi-footer">
                <span>Requires Clearance Sign-off</span>
                <span class="kpi-trend <?= $pendingCount > 0 ? 'alert' : 'up' ?>">SLA &lt; 24h</span>
              </div>
            </div>

            <div class="hr-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Approved Leaves</span>
                <div class="kpi-icon-pill steel">🏖️</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= $approvedCount ?></span>
                <span class="hr-text-muted-13" >Authorized</span>
              </div>
              <div class="kpi-footer">
                <span>Coverage Verified</span>
                <span class="kpi-trend up">Normal Operations</span>
              </div>
            </div>

            <div class="hr-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Total Processed</span>
                <div class="kpi-icon-pill plum">📊</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= count($leaveRequests) ?></span>
                <span class="hr-text-muted-13" >Records</span>
              </div>
              <div class="kpi-footer">
                <span>Stored in leave_requests</span>
                <span class="kpi-trend up">Compliant</span>
              </div>
            </div>
          </div>

          <!-- Leave Request Approvals Queue Table -->
          <div class="hr-card hr-panel-flush-mt-15" >
            <div class="hr-table-header-bar" >
              <div>
                <h3 class="card-title">Live Leave Records &amp; Absence Requests</h3>
                <p class="hr-meta-subtext" >
                  Real-time database queries with one-click approval and audit attribution
                </p>
              </div>
            </div>

            <table class="employee-table">
              <thead>
                <tr>
                  <th class="hr-w-100" >Req ID</th>
                  <th>Employee</th>
                  <th>Division</th>
                  <th>Leave Type</th>
                  <th>Schedule</th>
                  <th>Clearance</th>
                  <th>Status</th>
                  <th class="hr-w-180-right" >Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($leaveRequests)): ?>
                  <tr>
                    <td class="hr-empty-state" colspan="8" >No leave requests recorded.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($leaveRequests as $lr):
                    $isPending = ($lr['status'] === 'Pending');
                    $isApp = ($lr['status'] === 'Approved');
                    $statusClass = $isApp ? 'status-active' : ($isPending ? 'status-probation' : 'status-offboarding');
                  ?>
                    <tr id="leave-row-<?= $lr['leave_id'] ?>">
                      <td class="hr-mono-bold-plum" >
                        #<?= $lr['leave_id'] ?>
                      </td>
                      <td>
                        <div class="hr-bold-navy" ><?= htmlspecialchars($lr['full_name']) ?></div>
                        <div class="hr-mono-muted-11" ><?= htmlspecialchars($lr['emp_id']) ?></div>
                      </td>
                      <td><?= htmlspecialchars($lr['dept_name'] ?? $lr['department_code']) ?></td>
                      <td><strong><?= htmlspecialchars($lr['leave_type']) ?></strong></td>
                      <td>
                        <strong><?= date('M d, Y', strtotime($lr['start_date'])) ?></strong> – <strong><?= date('M d, Y', strtotime($lr['end_date'])) ?></strong>
                      </td>
                      <td>
                        <span class="clearance-badge clearance-l<?= substr($lr['clearance_level'], 1) ?>">
                          <?= htmlspecialchars($lr['clearance_level']) ?>
                        </span>
                      </td>
                      <td>
                        <span class="status-pill <?= $statusClass ?>" id="leave-status-badge-<?= $lr['leave_id'] ?>">
                          <?= htmlspecialchars($lr['status']) ?>
                        </span>
                      </td>
                      <td class="hr-text-right" >
                        <?php if ($canManage && $isPending): ?>
                          <div class="hr-flex-end-gap-xs" >
                            <button class="btn btn-primary-amber btn-sm" onclick="window.hrApp.processLeave(<?= $lr['leave_id'] ?>, 'Approved')">
                              Approve ✓
                            </button>
                            <button class="btn btn-outline btn-sm hr-btn-border-red"  onclick="window.hrApp.processLeave(<?= $lr['leave_id'] ?>, 'Rejected')">
                              Reject ✕
                            </button>
                          </div>
                        <?php else: ?>
                          <span class="hr-text-muted-11" >
                            <?= $lr['approver_name'] ? 'By ' . htmlspecialchars($lr['approver_name']) : 'Processed' ?>
                          </span>
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

  <!-- MODAL: SUBMIT LEAVE REQUEST -->
  <div id="modal-create-leave" class="modal-backdrop">
    <div class="modal-dialog hr-modal-500" >
      <div class="modal-header hr-modal-header-plum" >
        <div class="hr-title-14-bold" >File Official Leave Application</div>
        <button class="modal-close hr-text-white"  onclick="window.hrApp.closeModal('modal-create-leave')">✕</button>
      </div>
      <div class="modal-body hr-p-6" >
        <form id="form-create-leave" onsubmit="return window.hrApp.handleCreateLeave(event, this);">
          <div class="hr-flex-col-gap-md" >
            <div>
              <label class="hr-field-label" >Employee *</label>
              <select class="hr-form-control" name="emp_id" required >
                <?php foreach ($activeEmployees as $ae): ?>
                  <option value="<?= htmlspecialchars($ae['emp_id']) ?>">
                    <?= htmlspecialchars($ae['full_name']) ?> (<?= htmlspecialchars($ae['emp_id']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label class="hr-field-label" >Leave Category *</label>
              <select class="hr-form-control" name="leave_type" required >
                <option value="Annual Leave">Statutory Annual Paid Leave (28 days)</option>
                <option value="Sick Leave">Certified Medical / Sick Leave</option>
                <option value="Technical Sabbatical">Technical Research Sabbatical</option>
                <option value="Paternity Leave">Paternity / Family Leave</option>
                <option value="Personal Leave">Personal Unpaid Leave</option>
              </select>
            </div>

            <div class="hr-grid-2col-sm" >
              <div>
                <label class="hr-field-label" >Start Date *</label>
                <input type="date" name="start_date" required value="<?= date('Y-m-d', strtotime('+7 days')) ?>" class="hr-form-control" />
              </div>
              <div>
                <label class="hr-field-label" >End Date *</label>
                <input type="date" name="end_date" required value="<?= date('Y-m-d', strtotime('+14 days')) ?>" class="hr-form-control" />
              </div>
            </div>

            <div class="hr-flex-end-gap-sm" >
              <button type="button" class="btn btn-outline" onclick="window.hrApp.closeModal('modal-create-leave')">Cancel</button>
              <button type="submit" class="btn btn-primary-amber">Submit Application →</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
</body>

</html>