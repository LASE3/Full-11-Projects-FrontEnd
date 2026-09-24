<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('Finance');

$pdo = getDbConnection();
$currUser = $_SESSION['vostok_user'] ?? [
    'full_name' => 'Mikhail Sorokin',
    'job_title' => 'Chief Financial Controller',
    'clearance_level' => 'L4'
];

$userFullName = htmlspecialchars($currUser['full_name'] ?? 'Mikhail Sorokin');
$userTitle = htmlspecialchars($currUser['job_title'] ?? $currUser['role_name'] ?? 'Chief Financial Controller');

// Query all Cost Centers from Database (budgets joined with departments)
$stmt = $pdo->query("
    SELECT 
        b.budget_id,
        b.department_code,
        b.fiscal_year,
        b.allocated_amount,
        b.spent_amount,
        d.dept_name,
        d.main_function,
        (SELECT full_name FROM employees e WHERE e.department_code = b.department_code AND e.employment_status = 'Active' ORDER BY e.clearance_level DESC LIMIT 1) as dept_head
    FROM budgets b
    JOIN departments d ON b.department_code = d.dept_code
    ORDER BY b.allocated_amount DESC
");
$budgets = $stmt->fetchAll();

// Calculate Corporate Totals
$totalAllocated = 0;
$totalSpent = 0;
foreach ($budgets as $b) {
    $totalAllocated += (float)$b['allocated_amount'];
    $totalSpent += (float)$b['spent_amount'];
}
$totalRemaining = $totalAllocated - $totalSpent;
$overallPacing = $totalAllocated > 0 ? ($totalSpent / $totalAllocated) * 100 : 0;

// Sidebar active metrics
$activeInvoicesCount = (int)$pdo->query("SELECT COUNT(*) FROM invoices WHERE payment_status != 'Paid'")->fetchColumn();
$unmatchedCount = (int)$pdo->query("SELECT COUNT(*) FROM payments WHERE reconciled = 0")->fetchColumn();
$totalProjectsCount = (int)$pdo->query("SELECT COUNT(*) FROM projects WHERE status != 'Closed'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR Finance · Engineering Division Budgets (SYS-08)</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <div class="app-container">
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
                <span class="system-tag">FIN · SYS 08</span>
              </div>
              <div class="brand-subline">
                <span class="status-dot-pulse"></span>
                <span>finance.vostokpribor.local</span>
                <span style="opacity: 0.5;">|</span>
                <span>DIVISION BUDGETS (LIVE DB)</span>
              </div>
            </div>
          </a>
        </div>

        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search invoices, transactions, account IDs, project budgets..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <div class="top-nav__actions">
          <div class="confidential-system-pill" title="Restricted Financial Records">
            <span>🔒</span>
            <span>HIGHLY CONFIDENTIAL</span>
          </div>

          <button class="icon-button" onclick="window.finApp.showToast('Budget Telemetry', 'Corporate budget burn-rate tracking at <?= round($overallPacing, 1) ?>% across <?= count($budgets) ?> operational divisions.')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="badge-dot"></span>
          </button>

          <div class="top-user-profile" onclick="window.finApp.showToast('Active Controller', '<?= $userFullName ?> · <?= $userTitle ?>')">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" alt="Controller" class="user-avatar-top" />
            <div class="user-details-top">
              <span class="user-name-top"><?= $userFullName ?></span>
              <span class="user-role-top"><?= $userTitle ?></span>
            </div>
          </div>
        </div>
      
        <!-- Top Bar Sign Out -->
        <a href="../api/logout.php?system=Finance%20%26%20Billing&redirect=../Finance%20%26%20Billing/login.php" class="top-signout-btn" title="Sign Out of Finance &amp; Billing" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:4px;background:rgba(178,58,50,0.2);border:1px solid rgba(178,58,50,0.5);color:#FF8080;font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;margin-left:8px;vertical-align:middle;transition:all 0.2s;" onmouseover="this.style.background='rgba(178,58,50,0.4)';this.style.color='#FFFFFF'" onmouseout="this.style.background='rgba(178,58,50,0.2)';this.style.color='#FF8080'">
          <span>Sign Out</span>
        </a>
      </div>
    </header>

    <div class="main-layout">
      <!-- SIDEBAR -->
      <aside class="sidebar">
        <div>
          <div class="sidebar-section-title">Finance &amp; Treasury</div>
          <nav class="sidebar-nav">
            <a href="Dashboard.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
                <span>Dashboard</span>
              </div>
            </a>
            <a href="Invoices.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></span>
                <span>Invoices</span>
              </div>
              <span class="sidebar-badge badge-amber"><?= $activeInvoicesCount ?></span>
            </a>
            <a href="PaymentsReconciliation.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span>
                <span>Payments &amp; Reconciliation</span>
              </div>
              <span class="sidebar-badge badge-red"><?= $unmatchedCount ?></span>
            </a>
            <a href="ProjectBilling.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg></span>
                <span>Project Billing</span>
              </div>
              <span class="sidebar-badge badge-green"><?= $totalProjectsCount ?></span>
            </a>
            <a href="Budgets.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>
                <span>Budgets</span>
              </div>
              <span class="sidebar-badge"><?= round($overallPacing) ?>%</span>
            </a>
            <a href="FinancialReports.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg></span>
                <span>Financial Reports</span>
              </div>
              <span class="sidebar-badge">FY26</span>
            </a>
            <a href="Integrations.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00E5FF" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></span>
                <span style="color: #00E5FF; font-weight: 600;">System Integrations</span>
              </div>
              <span class="sidebar-badge" style="background: rgba(0,229,255,0.15); color: #00E5FF;">SYS07</span>
            </a>
          </nav>
        </div>

        <div class="sidebar-section-title" style="margin-top: 1rem;">Unified Ecosystem</div>
        <nav class="sidebar-nav" style="margin-bottom: 0.5rem;">
          <a href="../VOSTOKPRIBOR Corporate Web Platform/index.php" class="sidebar-nav-item">
            <div class="sidebar-item-left">
              <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></span>
              <span>Corporate Platform</span>
            </div>
            <span class="sidebar-badge" style="font-size: 10px;">SYS 01</span>
          </a>
          <a href="../Employee Intranet/index.php" class="sidebar-nav-item">
            <div class="sidebar-item-left">
              <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
              <span>Employee Intranet</span>
            </div>
            <span class="sidebar-badge" style="font-size: 10px;">SYS 04</span>
          </a>
        </nav>

        <div class="sidebar-footer">
          <div class="security-widget-card">
            <div class="security-widget-header">
              <span>Financial Ledger Security</span>
              <span class="security-badge-status">● VERIFIED</span>
            </div>
            <div style="font-size: 11px; color: var(--fin-text-inverse-muted); margin-top: 2px;">
              Connected: <strong>vostokpribor.budgets</strong>
            </div>
          </div>
        </div>
      </aside>

      <!-- MAIN CONTENT -->
      <main class="content-wrapper">
        <div class="portal-container">
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <a href="Dashboard.php">Finance &amp; Billing</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Engineering Division Budgets</span>
              </div>
              <h1 class="page-title">CapEx &amp; OpEx Division Budget Allocation</h1>
              <p class="page-subtitle">Fiscal year 2026 departmental cost centers, laboratory capital expenditure, and burn-rate governance from MySQL database</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-primary-amber" onclick="window.finApp.openModal('modal-update-budget')">
                <span>📑 Allocate / Revise Budget</span>
              </button>
            </div>
          </div>

          <!-- Top KPI Cards -->
          <div class="kpi-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 1.5rem;">
            <div class="fin-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Total Allocated Budget (FY26)</span>
                <div class="kpi-icon-pill green">💼</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value">€<?= number_format($totalAllocated, 2) ?></span>
              </div>
              <div class="kpi-footer">
                <span><?= count($budgets) ?> Operational Divisions</span>
                <span class="kpi-trend up">Ratified by Board</span>
              </div>
            </div>

            <div class="fin-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Expended YTD</span>
                <div class="kpi-icon-pill steel">📉</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value">€<?= number_format($totalSpent, 2) ?></span>
              </div>
              <div class="kpi-footer">
                <span>Burn-Rate: <?= round($overallPacing, 1) ?>%</span>
                <span class="kpi-trend up">Within Variance Threshold</span>
              </div>
            </div>

            <div class="fin-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Remaining Corporate Ceiling</span>
                <div class="kpi-icon-pill amber">⏳</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value">€<?= number_format($totalRemaining, 2) ?></span>
              </div>
              <div class="kpi-footer">
                <span>Q3 / Q4 Available Margin</span>
                <span class="kpi-trend up">Secured by Treasury</span>
              </div>
            </div>
          </div>

          <!-- Budget Allocation Table -->
          <div class="fin-card" style="padding: 0; overflow: hidden;">
            <div style="padding: 1rem 1.25rem; background: #FFFFFF; border-bottom: 1px solid var(--fin-surface-border); display: flex; align-items: center; justify-content: space-between;">
              <h3 class="card-title">Cost Center Allocations across <?= count($budgets) ?> Company Divisions (vostokpribor.budgets)</h3>
              <span class="security-badge-status">● AUDIT SYNCHRONIZED</span>
            </div>
            <table class="fin-table">
              <thead>
                <tr>
                  <th>Cost Center</th>
                  <th>Department / Operational Division</th>
                  <th style="text-align: right;">Annual Budget</th>
                  <th style="text-align: right;">Expended YTD</th>
                  <th style="width: 220px;">Burn-Rate Pacing</th>
                  <th style="text-align: right;">Remaining</th>
                  <th style="text-align: right; width: 100px;">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($budgets)): ?>
                  <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--fin-text-muted);">
                      No budget allocations found in database.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($budgets as $b): 
                    $allocated = (float)$b['allocated_amount'];
                    $spent = (float)$b['spent_amount'];
                    $rem = $allocated - $spent;
                    $pacing = $allocated > 0 ? ($spent / $allocated) * 100 : 0;
                    $lead = $b['dept_head'] ?: 'Directorate';
                  ?>
                    <tr class="fin-table-row">
                      <td>
                        <code style="font-family: var(--fin-font-mono); color: var(--fin-green); font-weight: 700;">
                          CC-<?= htmlspecialchars($b['department_code']) ?>
                        </code>
                      </td>
                      <td>
                        <strong><?= htmlspecialchars($b['dept_name']) ?></strong>
                        <div style="font-size: 11px; color: var(--fin-text-muted);"><?= htmlspecialchars($b['main_function']) ?> · Lead: <?= htmlspecialchars($lead) ?></div>
                      </td>
                      <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-navy);">
                        €<?= number_format($allocated, 2) ?>
                      </td>
                      <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-green);">
                        €<?= number_format($spent, 2) ?>
                      </td>
                      <td>
                        <div class="budget-progress-container">
                          <div class="budget-progress-bar">
                            <div class="budget-progress-fill" style="width: <?= min(100, round($pacing, 1)) ?>%; <?= $pacing > 90 ? 'background: linear-gradient(90deg, #E8A33D, #F59E0B);' : '' ?>"></div>
                          </div>
                          <div class="budget-progress-meta">
                            <span><?= round($pacing, 1) ?>%</span>
                            <span style="<?= $pacing > 90 ? 'color: var(--fin-amber-hover);' : '' ?>"><?= $pacing > 90 ? 'CapEx High' : 'On Plan' ?></span>
                          </div>
                        </div>
                      </td>
                      <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 600; color: <?= $rem < 0 ? 'var(--fin-confidential)' : 'var(--fin-text-secondary)' ?>;">
                        €<?= number_format($rem, 2) ?>
                      </td>
                      <td style="text-align: right;">
                        <button class="btn btn-outline btn-sm" onclick="openBudgetEdit(<?= $b['budget_id'] ?>, '<?= $b['department_code'] ?>', <?= $allocated ?>, <?= $spent ?>)">
                          Edit ⚙️
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

  <!-- Modal: Allocate / Revise Budget in DB -->
  <div id="modal-update-budget" class="modal-backdrop">
    <div class="modal-dialog" style="max-width: 500px;">
      <div class="modal-header">
        <div style="font-weight: 700; font-size: 14px;">Departmental Budget Allocation (Commit to DB)</div>
        <button class="modal-close" onclick="window.finApp.closeModal('modal-update-budget')">✕</button>
      </div>
      <div class="modal-body">
        <form onsubmit="event.preventDefault(); window.finApp.handleUpdateBudgetSubmit(this);">
          <input type="hidden" name="budget_id" id="edit-budget-id" value="0" />
          <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div>
              <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--fin-navy); margin-bottom: 0.25rem;">Cost Center / Department *</label>
              <select name="department_code" id="edit-dept-code" required style="width: 100%; padding: 0.55rem; border: 1px solid var(--fin-surface-border); border-radius: var(--fin-radius-md); font-size: 13px;">
                <?php foreach ($budgets as $b): ?>
                  <option value="<?= htmlspecialchars($b['department_code']) ?>">
                    CC-<?= htmlspecialchars($b['department_code']) ?> · <?= htmlspecialchars($b['dept_name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--fin-navy); margin-bottom: 0.25rem;">Allocated Annual Budget (EUR) *</label>
              <input type="number" step="0.01" min="1" name="allocated_amount" id="edit-allocated" required placeholder="3500000.00" style="width: 100%; padding: 0.55rem; border: 1px solid var(--fin-surface-border); border-radius: var(--fin-radius-md); font-size: 13px;" />
            </div>

            <div>
              <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--fin-navy); margin-bottom: 0.25rem;">Expended Amount YTD (EUR) *</label>
              <input type="number" step="0.01" min="0" name="spent_amount" id="edit-spent" required placeholder="2100000.00" style="width: 100%; padding: 0.55rem; border: 1px solid var(--fin-surface-border); border-radius: var(--fin-radius-md); font-size: 13px;" />
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 0.5rem;">
              <button type="button" class="btn btn-outline" onclick="window.finApp.closeModal('modal-update-budget')">Cancel</button>
              <button type="submit" class="btn btn-primary-amber">Save Allocation to DB</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
  <script>
    function openBudgetEdit(id, code, allocated, spent) {
      document.getElementById('edit-budget-id').value = id;
      document.getElementById('edit-dept-code').value = code;
      document.getElementById('edit-allocated').value = allocated;
      document.getElementById('edit-spent').value = spent;
      window.finApp.openModal('modal-update-budget');
    }
  </script>
</body>
</html>
