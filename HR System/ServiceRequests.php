<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR HR System - Customer Service Requests Dispatch
 * Live cross-system integration with Customer Portal (SYS-03)
 */

require_once __DIR__ . '/hr_service.php';
requireAuth('HR');

$currUser  = hr_getCurrentUser();
$metrics   = hr_getDashboardMetrics();
$canManage = hr_canManageHR();
$isSuper   = isSuperAdmin($currUser);

$filter = $_GET['status'] ?? 'ALL';
$requests = hr_getServiceRequests($filter);

// Fetch qualified active engineers from employees table for assignment dropdown
$pdo = getDbConnection();
$engineers = $pdo->query("
    SELECT emp_id, full_name, job_title, department_code, clearance_level 
    FROM employees 
    WHERE employment_status = 'Active' 
      AND (department_code IN ('ENG', 'OPS', 'ITD') OR clearance_level IN ('L3', 'L4'))
      AND emp_id != 'EMP-0001'
    ORDER BY full_name ASC
")->fetchAll(PDO::FETCH_ASSOC);

$nameParts = explode(' ', trim($currUser['full_name']));
$initials  = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR HR System · Client Service Requests &amp; Field Dispatch</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <style>
    .badge-status { padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge-assigned { background: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.3); }
    .badge-review { background: rgba(245,158,11,0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); }
    .badge-submitted { background: rgba(56,189,248,0.15); color: #38bdf8; border: 1px solid rgba(56,189,248,0.3); }
  </style>
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
                <span class="hr-opacity-50">|</span>
                <span>CLIENT SERVICE DISPATCH</span>
              </div>
            </div>
          </a>
        </div>
        <div class="top-nav__actions">
          <div class="confidential-system-pill">
            <span>ðŸ› ï¸</span>
            <span>CUSTOMER PORTAL INTEGRATION ACTIVE</span>
          </div>
          <button class="icon-button notifications-btn" id="notifications-toggle-btn" title="Live Enterprise Notifications">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
              <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
          </button>
          <div class="top-user-profile">
            <div class="hr-avatar-circle-glow"><?= $initials ?></div>
            <div class="user-details-top">
              <span class="user-name-top"><?= htmlspecialchars($currUser['full_name']) ?></span>
              <span class="user-role-top"><?= htmlspecialchars($currUser['role_name'] ?? 'HR Officer') ?> · <?= htmlspecialchars($currUser['clearance_level'] ?? 'L1') ?></span>
            </div>
          </div>
          <a href="../api/logout.php?system=HR%20System&redirect=../HR%20System/login.php" class="top-signout-btn" title="Sign Out">
            <span class="material-symbols-outlined">logout</span>
            <span>Sign Out</span>
          </a>
        </div>
      </div>
    </header>

    <div class="main-layout">
      <!-- SIDEBAR -->
      <?php hr_renderSidebar('services'); ?>

      <!-- MAIN CONTENT -->
      <main class="content-wrapper">
        <div class="portal-container">
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <a href="Dashboard.php">HR System</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Customer Service Requests</span>
              </div>
              <h1 class="page-title">Client Engineering Requests &amp; Field Dispatch</h1>
              <p class="page-subtitle">Incoming technical service and calibration orders submitted through the Customer Portal (SYS-03). Assign vetted engineers and specialists from the employee registry.</p>
            </div>
            <div class="page-header-actions">
              <div class="filter-group">
                <label style="color:#94a3b8; font-size:12px; margin-right:8px;">Status:</label>
                <select onchange="location.href='ServiceRequests.php?status=' + this.value" class="hr-select-dark">
                  <option value="ALL" <?= $filter === 'ALL' ? 'selected' : '' ?>>All Requests (<?= count($requests) ?>)</option>
                  <option value="Submitted" <?= $filter === 'Submitted' ? 'selected' : '' ?>>Submitted (Unassigned)</option>
                  <option value="Personnel Assigned" <?= $filter === 'Personnel Assigned' ? 'selected' : '' ?>>Personnel Assigned</option>
                  <option value="Completed" <?= $filter === 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select>
              </div>
            </div>
          </div>

          <!-- TABLE -->
          <div class="employee-table-container">
            <table class="employee-table">
              <thead>
                <tr>
                  <th>Request ID</th>
                  <th>Client Organization</th>
                  <th>Requested Service</th>
                  <th>Priority</th>
                  <th>Target Date</th>
                  <th>Assigned Specialist</th>
                  <th>Status</th>
                  <th style="text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($requests)): ?>
                  <tr><td colspan="8" style="text-align:center; padding: 40px; color:#94a3b8;">No customer service requests match this filter.</td></tr>
                <?php else: ?>
                  <?php foreach ($requests as $r): ?>
                    <tr>
                      <td style="font-family: monospace; font-weight:700; color:#38bdf8;">
                        <?= htmlspecialchars($r['request_id']) ?>
                      </td>
                      <td>
                        <strong style="color:#f8fafc; font-size:13px;"><?= htmlspecialchars($r['company_name']) ?></strong>
                        <div style="color:#64748b; font-size:11px;"><?= htmlspecialchars($r['facility_location'] ?? 'Industrial Site') ?></div>
                      </td>
                      <td>
                        <div style="font-weight:600; color:#e2e8f0;"><?= htmlspecialchars($r['title']) ?></div>
                        <div style="color:#94a3b8; font-size:11px;"><?= htmlspecialchars($r['service_type']) ?></div>
                      </td>
                      <td>
                        <?php 
                          $pColor = match($r['priority']) {
                            'Critical' => 'color:#f87171; background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3);',
                            'High'     => 'color:#fbbf24; background:rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.3);',
                            default    => 'color:#94a3b8; background:rgba(148,163,184,0.1); border:1px solid rgba(148,163,184,0.2);'
                          };
                        ?>
                        <span style="display:inline-block; padding:2px 8px; border-radius:4px; font-size:10px; font-weight:700; font-family:monospace; <?= $pColor ?>">
                          <?= htmlspecialchars($r['priority']) ?>
                        </span>
                      </td>
                      <td style="font-family: monospace; font-size:12px; color:#cbd5e1;">
                        <?= htmlspecialchars(substr($r['requested_date'] ?? '', 0, 10)) ?>
                      </td>
                      <td>
                        <?php if (!empty($r['assigned_engineer_name'])): ?>
                          <div style="display:flex; align-items:center; gap:8px;">
                            <span style="display:inline-flex; width:24px; height:24px; border-radius:50%; background:rgba(34,197,94,0.2); color:#4ade80; align-items:center; justify-content:center; font-size:10px; font-weight:700;">
                              <?= strtoupper(substr($r['assigned_engineer_name'], 0, 2)) ?>
                            </span>
                            <div>
                              <div style="color:#4ade80; font-size:12px; font-weight:600;"><?= htmlspecialchars($r['assigned_engineer_name']) ?></div>
                              <div style="color:#64748b; font-size:10px;"><?= htmlspecialchars($r['assigned_engineer_title'] ?? 'Specialist') ?></div>
                            </div>
                          </div>
                        <?php else: ?>
                          <span style="color:#f59e0b; font-size:11px; font-style:italic;">âš ï¸ Awaiting Dispatch</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php 
                          $sClass = match($r['status']) {
                            'Personnel Assigned', 'Completed' => 'badge-assigned',
                            'Under Review', 'In Progress'     => 'badge-review',
                            default                            => 'badge-submitted'
                          };
                        ?>
                        <span class="badge-status <?= $sClass ?>">
                          <?= htmlspecialchars($r['status']) ?>
                        </span>
                      </td>
                      <td style="text-align: right;">
                        <button onclick="openAssignModal('<?= htmlspecialchars($r['request_id']) ?>', '<?= htmlspecialchars(addslashes($r['title'])) ?>', '<?= htmlspecialchars(addslashes($r['company_name'])) ?>')"
                                style="background:#2563eb; color:#fff; border:none; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">
                          <?= !empty($r['assigned_emp_id']) ? 'Reassign' : 'Assign Engineer' ?>
                        </button>
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

  <!-- Modal: Assign Specialist -->
  <div id="assign-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#131b2e; border:1px solid #334155; border-radius:12px; max-width:480px; width:100%; padding:24px; box-shadow:0 25px 50px rgba(0,0,0,0.6);">
      <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #334155; padding-bottom:12px; margin-bottom:16px;">
        <h3 style="color:#f8fafc; font-size:16px; font-weight:700; margin:0;">Assign Field Specialist</h3>
        <button onclick="document.getElementById('assign-modal').style.display='none'" style="background:transparent; border:none; color:#94a3b8; font-size:18px; cursor:pointer;">✕</button>
      </div>

      <div style="font-size:12px; color:#cbd5e1; margin-bottom:16px; background:#1e293b; padding:12px; border-radius:6px; line-height:1.5;">
        <div><strong>Request:</strong> <span id="modal-req-id" style="color:#38bdf8; font-family:monospace;"></span></div>
        <div style="margin-top:4px;"><strong>Client:</strong> <span id="modal-client-name"></span></div>
        <div style="margin-top:4px;"><strong>Task:</strong> <span id="modal-task-title"></span></div>
      </div>

      <form id="assign-form" style="display:flex; flex-direction:column; gap:12px; font-size:12px;">
        <input type="hidden" id="form-req-id" />
        <div>
          <label style="display:block; color:#94a3b8; font-weight:600; margin-bottom:6px;">Select Qualified Engineer / Specialist *</label>
          <select id="form-emp-id" required style="width:100%; background:#0f172a; color:#f8fafc; border:1px solid #334155; border-radius:6px; padding:8px 10px; font-size:12px;">
            <option value="">-- Choose Specialist from Active Registry --</option>
            <?php foreach ($engineers as $eng): ?>
              <option value="<?= htmlspecialchars($eng['emp_id']) ?>">
                <?= htmlspecialchars($eng['full_name']) ?> (<?= htmlspecialchars($eng['emp_id']) ?>) · <?= htmlspecialchars($eng['job_title']) ?> [<?= htmlspecialchars($eng['clearance_level']) ?>]
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label style="display:block; color:#94a3b8; font-weight:600; margin-bottom:6px;">HR Operations Dispatch Notes</label>
          <textarea id="form-notes" rows="3" placeholder="Add operational notes, dispatch instructions, safety clearance reminders..."
                    style="width:100%; background:#0f172a; color:#f8fafc; border:1px solid #334155; border-radius:6px; padding:8px 10px; font-size:12px; box-sizing:border-box;"></textarea>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:12px; border-top:1px solid #334155; padding-top:12px;">
          <button type="button" onclick="document.getElementById('assign-modal').style.display='none'"
                  style="background:transparent; color:#94a3b8; border:1px solid #475569; padding:8px 16px; border-radius:6px; font-size:12px; cursor:pointer;">
            Cancel
          </button>
          <button type="submit" id="btn-submit-assign"
                  style="background:#22c55e; color:#0f172a; font-weight:700; border:none; padding:8px 18px; border-radius:6px; font-size:12px; cursor:pointer;">
            Confirm Dispatch
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
  function openAssignModal(reqId, title, client) {
    document.getElementById('form-req-id').value = reqId;
    document.getElementById('modal-req-id').innerText = reqId;
    document.getElementById('modal-client-name').innerText = client;
    document.getElementById('modal-task-title').innerText = title;
    document.getElementById('assign-modal').style.display = 'flex';
  }

  document.getElementById('assign-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-submit-assign');
    btn.disabled = true;
    btn.innerText = 'Assigning...';

    const payload = {
      action: 'assign_service_request',
      request_id: document.getElementById('form-req-id').value,
      emp_id: document.getElementById('form-emp-id').value,
      notes: document.getElementById('form-notes').value
    };

    try {
      const res = await fetch('./api/hr_actions.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (data.success) {
        alert(data.message || 'Engineer assigned successfully!');
        window.location.reload();
      } else {
        alert('Error: ' + (data.message || 'Assignment failed.'));
      }
    } catch (err) {
      alert('Network error: ' + err.message);
    } finally {
      btn.disabled = false;
      btn.innerText = 'Confirm Dispatch';
    }
  });
  </script>
  <script src="../assets/js/notifications-hub.js" defer></script>
</body>
</html>

