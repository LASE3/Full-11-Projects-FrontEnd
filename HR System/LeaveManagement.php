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
                <span style="opacity: 0.5;">|</span>
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
            <a href="Offboarding.php" class="sidebar-nav-item"><div class="sidebar-item-left"><span class="sidebar-icon">🔒</span><span>Offboarding &amp; Revocation</span></div></a>
            <a href="LeaveManagement.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left"><span class="sidebar-icon">📅</span><span>Leave Management</span></div>
              <span class="sidebar-pill <?= $pendingCount > 0 ? 'alert' : '' ?>"><?= count($leaveRequests) ?></span>
            </a>
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
          <div class="kpi-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="hr-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Awaiting Approval</span>
                <div class="kpi-icon-pill amber">⚡</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono"><?= $pendingCount ?></span>
                <span style="font-size: 13px; color: var(--hr-text-muted);">Requests</span>
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
                <span style="font-size: 13px; color: var(--hr-text-muted);">Authorized</span>
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
                <span style="font-size: 13px; color: var(--hr-text-muted);">Records</span>
              </div>
              <div class="kpi-footer">
                <span>Stored in leave_requests</span>
                <span class="kpi-trend up">Compliant</span>
              </div>
            </div>
          </div>

          <!-- Leave Request Approvals Queue Table -->
          <div class="hr-card" style="padding: 0; overflow: hidden; margin-top: 1.5rem;">
            <div style="padding: 1rem 1.25rem; background-color: #FFFFFF; border-bottom: 1px solid var(--hr-surface-border); display: flex; align-items: center; justify-content: space-between;">
              <div>
                <h3 class="card-title">Live Leave Records &amp; Absence Requests</h3>
                <p style="font-size: 11.5px; color: var(--hr-text-secondary); margin-top: 2px;">
                  Real-time database queries with one-click approval and audit attribution
                </p>
              </div>
            </div>

            <table class="employee-table">
              <thead>
                <tr>
                  <th style="width: 100px;">Req ID</th>
                  <th>Employee</th>
                  <th>Division</th>
                  <th>Leave Type</th>
                  <th>Schedule</th>
                  <th>Clearance</th>
                  <th>Status</th>
                  <th style="text-align: right; width: 180px;">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($leaveRequests)): ?>
                  <tr><td colspan="8" style="text-align:center; padding:2rem; color:var(--hr-text-muted);">No leave requests recorded.</td></tr>
                <?php else: ?>
                  <?php foreach ($leaveRequests as $lr): 
                    $isPending = ($lr['status'] === 'Pending');
                    $isApp = ($lr['status'] === 'Approved');
                    $statusClass = $isApp ? 'status-active' : ($isPending ? 'status-probation' : 'status-offboarding');
                  ?>
                    <tr id="leave-row-<?= $lr['leave_id'] ?>">
                      <td style="font-family: var(--hr-font-mono); font-weight: 700; color: var(--hr-plum);">
                        #<?= $lr['leave_id'] ?>
                      </td>
                      <td>
                        <div style="font-weight: 700; color: var(--hr-navy);"><?= htmlspecialchars($lr['full_name']) ?></div>
                        <div style="font-size: 11px; color: var(--hr-text-muted); font-family: var(--hr-font-mono);"><?= htmlspecialchars($lr['emp_id']) ?></div>
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
                      <td style="text-align: right;">
                        <?php if ($canManage && $isPending): ?>
                          <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                            <button class="btn btn-primary-amber btn-sm" onclick="window.hrApp.processLeave(<?= $lr['leave_id'] ?>, 'Approved')">
                              Approve ✓
                            </button>
                            <button class="btn btn-outline btn-sm" style="color:#B23A32; border-color:#B23A32;" onclick="window.hrApp.processLeave(<?= $lr['leave_id'] ?>, 'Rejected')">
                              Reject ✕
                            </button>
                          </div>
                        <?php else: ?>
                          <span style="font-size: 11px; color: var(--hr-text-muted);">
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
    <div class="modal-dialog" style="max-width: 500px;">
      <div class="modal-header" style="background-color: var(--hr-navy); color: #FFFFFF; border-bottom: 3px solid var(--hr-plum);">
        <div style="font-weight: 700; font-size: 14px;">File Official Leave Application</div>
        <button class="modal-close" style="color: #FFFFFF;" onclick="window.hrApp.closeModal('modal-create-leave')">✕</button>
      </div>
      <div class="modal-body" style="padding: 1.5rem;">
        <form id="form-create-leave" onsubmit="return window.hrApp.handleCreateLeave(event, this);">
          <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div>
              <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--hr-navy); margin-bottom: 0.25rem;">Employee *</label>
              <select name="emp_id" required style="width: 100%; padding: 0.55rem; border: 1px solid var(--hr-surface-border); border-radius: var(--hr-radius-md);">
                <?php foreach ($activeEmployees as $ae): ?>
                  <option value="<?= htmlspecialchars($ae['emp_id']) ?>">
                    <?= htmlspecialchars($ae['full_name']) ?> (<?= htmlspecialchars($ae['emp_id']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--hr-navy); margin-bottom: 0.25rem;">Leave Category *</label>
              <select name="leave_type" required style="width: 100%; padding: 0.55rem; border: 1px solid var(--hr-surface-border); border-radius: var(--hr-radius-md);">
                <option value="Annual Leave">Statutory Annual Paid Leave (28 days)</option>
                <option value="Sick Leave">Certified Medical / Sick Leave</option>
                <option value="Technical Sabbatical">Technical Research Sabbatical</option>
                <option value="Paternity Leave">Paternity / Family Leave</option>
                <option value="Personal Leave">Personal Unpaid Leave</option>
              </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
              <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--hr-navy); margin-bottom: 0.25rem;">Start Date *</label>
                <input type="date" name="start_date" required value="<?= date('Y-m-d', strtotime('+7 days')) ?>" style="width: 100%; padding: 0.55rem; border: 1px solid var(--hr-surface-border); border-radius: var(--hr-radius-md);" />
              </div>
              <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--hr-navy); margin-bottom: 0.25rem;">End Date *</label>
                <input type="date" name="end_date" required value="<?= date('Y-m-d', strtotime('+14 days')) ?>" style="width: 100%; padding: 0.55rem; border: 1px solid var(--hr-surface-border); border-radius: var(--hr-radius-md);" />
              </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 0.5rem;">
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
