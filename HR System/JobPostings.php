<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR HR System - Job Postings & Talent Acquisition
 * Live synchronization: postings created here appear immediately on the Customer Portal
 */

require_once __DIR__ . '/hr_service.php';
requireAuth('HR');

$currUser  = hr_getCurrentUser();
$canManage = hr_canManageHR();
$isSuper   = isSuperAdmin($currUser);

$postings    = hr_getJobPostings();
$departments = hr_getDepartments();

$nameParts = explode(' ', trim($currUser['full_name']));
$initials  = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR HR System · Job Postings &amp; Recruitment Management</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body>
  <div class="app-container">
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
                <span>TALENT ACQUISITION</span>
              </div>
            </div>
          </a>
        </div>
        <div class="top-nav__actions">
          <div class="confidential-system-pill">
            <span>ðŸ“¢</span>
            <span>CUSTOMER PORTAL SYNCHRONIZATION ACTIVE</span>
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
      <?php hr_renderSidebar('careers'); ?>

      <!-- MAIN CONTENT -->
      <main class="content-wrapper">
        <div class="portal-container">
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <a href="Dashboard.php">HR System</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Job Postings &amp; Careers</span>
              </div>
              <h1 class="page-title">Published Openings &amp; Talent Acquisition</h1>
              <p class="page-subtitle">Manage open requisitions. Published postings immediately stream to the Customer Portal and external web platforms for candidate engagement.</p>
            </div>
            <div class="page-header-actions">
              <button onclick="document.getElementById('modal-create-job').style.display='flex'" class="btn btn-primary-amber">
                <span>+ Create Job Posting</span>
              </button>
            </div>
          </div>

          <!-- TABLE -->
          <div class="employee-table-container">
            <table class="employee-table">
              <thead>
                <tr>
                  <th>Posting ID</th>
                  <th>Position Title</th>
                  <th>Department</th>
                  <th>Location</th>
                  <th>Type &amp; Experience</th>
                  <th>Date Published</th>
                  <th>Status</th>
                  <th style="text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($postings)): ?>
                  <tr><td colspan="8" style="text-align:center; padding: 40px; color:#94a3b8;">No job postings found. Click "+ Create Job Posting" to add one.</td></tr>
                <?php else: ?>
                  <?php foreach ($postings as $job): ?>
                    <tr>
                      <td style="font-family: monospace; font-weight:700; color:#38bdf8;">
                        #VP-HR-<?= str_pad((string)$job['posting_id'], 4, '0', STR_PAD_LEFT) ?>
                      </td>
                      <td>
                        <strong style="color:#f8fafc; font-size:13px;"><?= htmlspecialchars($job['title']) ?></strong>
                        <div style="color:#94a3b8; font-size:11px; margin-top:2px; max-width:320px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                          <?= htmlspecialchars($job['description'] ?: 'High-precision engineering and industrial automation.') ?>
                        </div>
                      </td>
                      <td>
                        <span style="font-weight:600; color:#cbd5e1;"><?= htmlspecialchars($job['dept_name'] ?? $job['department_code'] ?? 'Engineering') ?></span>
                      </td>
                      <td style="color:#94a3b8; font-size:12px;">
                        <?= htmlspecialchars($job['location'] ?? 'Almaty HQ') ?>
                      </td>
                      <td style="font-size:12px; color:#cbd5e1;">
                        <?= htmlspecialchars($job['employment_type'] ?? 'Full-Time') ?> · <span style="color:#64748b;"><?= htmlspecialchars($job['experience_level'] ?? 'Mid-Senior') ?></span>
                      </td>
                      <td style="font-family: monospace; font-size:12px; color:#94a3b8;">
                        <?= htmlspecialchars(substr($job['posted_at'] ?? '', 0, 10)) ?>
                      </td>
                      <td>
                        <?php if ((int)$job['is_published'] === 1): ?>
                          <span style="padding:3px 8px; border-radius:9999px; font-size:10px; font-weight:700; background:rgba(34,197,94,0.15); color:#4ade80; border:1px solid rgba(34,197,94,0.3);">
                            â— LIVE ON PORTAL
                          </span>
                        <?php else: ?>
                          <span style="padding:3px 8px; border-radius:9999px; font-size:10px; font-weight:700; background:rgba(148,163,184,0.15); color:#94a3b8; border:1px solid rgba(148,163,184,0.3);">
                            â—‹ DRAFT
                          </span>
                        <?php endif; ?>
                      </td>
                      <td style="text-align: right;">
                        <button onclick="toggleJob(<?= (int)$job['posting_id'] ?>, <?= (int)$job['is_published'] === 1 ? 0 : 1 ?>)"
                                style="background:#1e293b; color:#38bdf8; border:1px solid #334155; padding:5px 10px; border-radius:6px; font-size:11px; cursor:pointer;">
                          <?= (int)$job['is_published'] === 1 ? 'Unpublish' : 'Publish Live' ?>
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

  <!-- Modal: Create Job Posting -->
  <div id="modal-create-job" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#131b2e; border:1px solid #334155; border-radius:12px; max-width:500px; width:100%; padding:24px; box-shadow:0 25px 50px rgba(0,0,0,0.6);">
      <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #334155; padding-bottom:12px; margin-bottom:16px;">
        <h3 style="color:#f8fafc; font-size:16px; font-weight:700; margin:0;">Create New Job Opening</h3>
        <button onclick="document.getElementById('modal-create-job').style.display='none'" style="background:transparent; border:none; color:#94a3b8; font-size:18px; cursor:pointer;">✕</button>
      </div>

      <form id="create-job-form" style="display:flex; flex-direction:column; gap:12px; font-size:12px;">
        <div>
          <label style="display:block; color:#94a3b8; font-weight:600; margin-bottom:4px;">Position Title *</label>
          <input id="job-title" type="text" placeholder="e.g. Metrology & Precision Sensor Specialist" required
                 style="width:100%; background:#0f172a; color:#f8fafc; border:1px solid #334155; border-radius:6px; padding:8px 10px; font-size:12px; box-sizing:border-box;" />
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
          <div>
            <label style="display:block; color:#94a3b8; font-weight:600; margin-bottom:4px;">Department</label>
            <select id="job-dept" style="width:100%; background:#0f172a; color:#f8fafc; border:1px solid #334155; border-radius:6px; padding:8px 10px; font-size:12px;">
              <?php foreach ($departments as $d): ?>
                <option value="<?= htmlspecialchars($d['dept_code']) ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label style="display:block; color:#94a3b8; font-weight:600; margin-bottom:4px;">Employment Type</label>
            <select id="job-type" style="width:100%; background:#0f172a; color:#f8fafc; border:1px solid #334155; border-radius:6px; padding:8px 10px; font-size:12px;">
              <option value="Full-Time">Full-Time</option>
              <option value="Contract / Project">Contract / Project</option>
              <option value="Part-Time">Part-Time</option>
            </select>
          </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
          <div>
            <label style="display:block; color:#94a3b8; font-weight:600; margin-bottom:4px;">Location</label>
            <input id="job-loc" type="text" value="Almaty Industrial Complex (HQ)"
                   style="width:100%; background:#0f172a; color:#f8fafc; border:1px solid #334155; border-radius:6px; padding:8px 10px; font-size:12px; box-sizing:border-box;" />
          </div>
          <div>
            <label style="display:block; color:#94a3b8; font-weight:600; margin-bottom:4px;">Experience Level</label>
            <select id="job-exp" style="width:100%; background:#0f172a; color:#f8fafc; border:1px solid #334155; border-radius:6px; padding:8px 10px; font-size:12px;">
              <option value="Mid-Senior Level">Mid-Senior Level</option>
              <option value="Senior Specialist (5+ yrs)">Senior Specialist (5+ yrs)</option>
              <option value="Lead Engineer">Lead Engineer</option>
              <option value="Junior / Entry">Junior / Entry</option>
            </select>
          </div>
        </div>

        <div>
          <label style="display:block; color:#94a3b8; font-weight:600; margin-bottom:4px;">Role Description &amp; Scope</label>
          <textarea id="job-desc" rows="3" placeholder="Describe core duties, telemetry/SCADA responsibilities, certifications..."
                    style="width:100%; background:#0f172a; color:#f8fafc; border:1px solid #334155; border-radius:6px; padding:8px 10px; font-size:12px; box-sizing:border-box;"></textarea>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:8px; border-top:1px solid #334155; padding-top:12px;">
          <button type="button" onclick="document.getElementById('modal-create-job').style.display='none'"
                  style="background:transparent; color:#94a3b8; border:1px solid #475569; padding:8px 16px; border-radius:6px; font-size:12px; cursor:pointer;">
            Cancel
          </button>
          <button type="submit" id="btn-create-job"
                  style="background:#f59e0b; color:#0f172a; font-weight:700; border:none; padding:8px 18px; border-radius:6px; font-size:12px; cursor:pointer;">
            Publish Opening
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
  document.getElementById('create-job-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-create-job');
    btn.disabled = true;
    btn.innerText = 'Publishing...';

    const payload = {
      action: 'create_job_posting',
      title: document.getElementById('job-title').value,
      department_code: document.getElementById('job-dept').value,
      employment_type: document.getElementById('job-type').value,
      location: document.getElementById('job-loc').value,
      experience_level: document.getElementById('job-exp').value,
      description: document.getElementById('job-desc').value
    };

    try {
      const res = await fetch('./api/hr_actions.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (data.success) {
        alert(data.message || 'Job opening created and published live on Customer Portal!');
        window.location.reload();
      } else {
        alert('Error: ' + (data.message || 'Creation failed.'));
      }
    } catch (err) {
      alert('Network error: ' + err.message);
    } finally {
      btn.disabled = false;
      btn.innerText = 'Publish Opening';
    }
  });

  async function toggleJob(id, pub) {
    try {
      const res = await fetch('./api/hr_actions.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'toggle_job_posting', posting_id: id, is_published: pub })
      });
      const data = await res.json();
      if (data.success) {
        window.location.reload();
      } else {
        alert(data.message || 'Action failed');
      }
    } catch (e) {
      alert(e.message);
    }
  }
  </script>
  <script src="../assets/js/notifications-hub.js" defer></script>
</body>
</html>

