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

// Query all active industrial projects with customer details, billed totals, and next milestone from DB
$stmt = $pdo->query("
    SELECT 
        p.prj_id,
        COALESCE(p.project_name, CONCAT('Project ', p.prj_id)) as project_name,
        p.cus_id,
        c.company_name,
        c.sector,
        p.budget,
        p.currency,
        p.status,
        COALESCE(SUM(i.total_value), 0) as billed_to_date,
        (SELECT bc.milestone_description FROM billing_cycles bc WHERE bc.prj_id = p.prj_id AND bc.invoiced = 0 ORDER BY bc.scheduled_date ASC LIMIT 1) as next_milestone_desc,
        (SELECT bc.scheduled_date FROM billing_cycles bc WHERE bc.prj_id = p.prj_id AND bc.invoiced = 0 ORDER BY bc.scheduled_date ASC LIMIT 1) as next_milestone_date,
        (SELECT bc.milestone_amount FROM billing_cycles bc WHERE bc.prj_id = p.prj_id AND bc.invoiced = 0 ORDER BY bc.scheduled_date ASC LIMIT 1) as next_milestone_amount,
        (SELECT bc.cycle_id FROM billing_cycles bc WHERE bc.prj_id = p.prj_id AND bc.invoiced = 0 ORDER BY bc.scheduled_date ASC LIMIT 1) as next_cycle_id
    FROM projects p
    JOIN customers c ON p.cus_id = c.cus_id
    LEFT JOIN invoices i ON p.prj_id = i.prj_id
    GROUP BY p.prj_id
    ORDER BY p.budget DESC
");
$projects = $stmt->fetchAll();

// Calculate Corporate Portfolio Totals
$totalContractValue = 0;
$totalBilledToDate = 0;
foreach ($projects as $prj) {
  $totalContractValue += (float)$prj['budget'];
  $totalBilledToDate += (float)$prj['billed_to_date'];
}
$remainingPipeline = max(0, $totalContractValue - $totalBilledToDate);
$portfolioPacing = $totalContractValue > 0 ? ($totalBilledToDate / $totalContractValue) * 100 : 0;

// Fetch all unbilled milestones for the Bill Milestone modal
$unbilledMilestones = $pdo->query("
    SELECT 
        bc.cycle_id,
        bc.prj_id,
        bc.milestone_description,
        bc.scheduled_date,
        bc.milestone_amount,
        p.project_name,
        c.company_name,
        p.currency
    FROM billing_cycles bc
    JOIN projects p ON bc.prj_id = p.prj_id
    JOIN customers c ON p.cus_id = c.cus_id
    WHERE bc.invoiced = 0
    ORDER BY bc.scheduled_date ASC
")->fetchAll();

// Sidebar active metrics
$activeInvoicesCount = (int)$pdo->query("SELECT COUNT(*) FROM invoices WHERE payment_status != 'Paid'")->fetchColumn();
$unmatchedCount = (int)$pdo->query("SELECT COUNT(*) FROM payments WHERE reconciled = 0")->fetchColumn();
$avgBudgetBurn = (float)$pdo->query("SELECT AVG((spent_amount / NULLIF(allocated_amount, 0)) * 100) FROM budgets")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR Finance · Project Billing Overview (SYS-08)</title>
  <link rel="stylesheet" href="css/style.css">
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
                <span class="system-tag">FIN · SYS 08</span>
              </div>
              <div class="brand-subline">
                <span class="status-dot-pulse"></span>
                <span>finance.vostokpribor.local</span>
                <span class="fin-opacity-50" >|</span>
                <span>PROJECT BILLING &amp; MILESTONES (LIVE DB)</span>
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

          <button class="icon-button" onclick="window.finApp.showToast('Milestone Alert', 'Live DB: <?= count($unbilledMilestones) ?> unbilled milestone(s) available in project contracts.')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
              <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
            <?php if (count($unbilledMilestones) > 0): ?>
              <span class="badge-dot"></span>
            <?php endif; ?>
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
        <a href="../api/logout.php?system=Finance%20%26%20Billing&redirect=../Finance%20%26%20Billing/login.php" class="top-signout-btn" title="Sign Out of Finance &amp; Billing" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" >
          <span>Sign Out</span>
        </a>
      </div>
    </header>

    <div class="main-layout">
            <aside class="sidebar">
        <div>
          <div class="sidebar-section-title">Finance &amp; Treasury</div>
          <nav class="sidebar-nav">
            <a href="Dashboard.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                  </svg></span>
                <span>Dashboard</span>
              </div>
            </a>
            <a href="Invoices.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10 9 9 9 8 9" />
                  </svg></span>
                <span>Invoices</span>
              </div>
              <span class="sidebar-badge badge-amber"><?= $activeInvoicesCount ?></span>
            </a>
            <a href="PaymentsReconciliation.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
                    <line x1="1" y1="10" x2="23" y2="10" />
                  </svg></span>
                <span>Payments &amp; Reconciliation</span>
              </div>
              <span class="sidebar-badge badge-red"><?= $unmatchedCount ?></span>
            </a>
            <a href="ProjectBilling.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                    <line x1="12" y1="11" x2="12" y2="17" />
                    <line x1="9" y1="14" x2="15" y2="14" />
                  </svg></span>
                <span>Project Billing</span>
              </div>
              <span class="sidebar-badge badge-green"><?= count($projects) ?></span>
            </a>
            <a href="Budgets.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23" />
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                  </svg></span>
                <span>Budgets</span>
              </div>
              <span class="sidebar-badge"><?= round($avgBudgetBurn) ?>%</span>
            </a>
            <a href="FinancialReports.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21.21 15.89A10 10 0 1 1 8 2.83" />
                    <path d="M22 12A10 10 0 0 0 12 2v10z" />
                  </svg></span>
                <span>Financial Reports</span>
              </div>
              <span class="sidebar-badge">FY26</span>
            </a>
            <a href="Integrations.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00E5FF" stroke-width="2">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                  </svg></span>
                <span class="fin-text-cyan" >System Integrations</span>
              </div>
              <span class="sidebar-badge fin-badge-cyan" >SYS07</span>
            </a>
          </nav>
        </div>

        <div class="sidebar-section-title fin-mt-4" >Unified Ecosystem</div>
        <nav class="sidebar-nav fin-mb-2" >
          <a href="../VOSTOKPRIBOR Corporate Web Platform/index.php" class="sidebar-nav-item">
            <div class="sidebar-item-left">
              <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="2" y1="12" x2="22" y2="12" />
                  <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                </svg></span>
              <span>Corporate Platform</span>
            </div>
            <span class="sidebar-badge fin-text-xs" >SYS 01</span>
          </a>
          <a href="../Employee Intranet/index.php" class="sidebar-nav-item">
            <div class="sidebar-item-left">
              <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="3" width="7" height="7" />
                  <rect x="14" y="3" width="7" height="7" />
                  <rect x="14" y="14" width="7" height="7" />
                  <rect x="3" y="14" width="7" height="7" />
                </svg></span>
              <span>Employee Intranet</span>
            </div>
            <span class="sidebar-badge fin-text-xs" >SYS 04</span>
          </a>
        </nav>

        <div class="sidebar-footer">
          <div class="security-widget-card">
            <div class="security-widget-header">
              <span>Financial Ledger Security</span>
              <span class="security-badge-status">● VERIFIED</span>
            </div>
            <div class="fin-text-inverse-muted-sm" >
              Connected: <strong>vostokpribor.projects</strong>
            </div>
          </div>
        </div>
      </aside>

      <!-- ========================================================================
           MAIN CONTENT AREA: SCREEN 3 PROJECT BILLING OVERVIEW
           ======================================================================== -->
      <main class="content-wrapper">
        <div class="portal-container">
          <!-- Page Header -->
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <a href="Dashboard.php">Finance &amp; Billing</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Project Billing Overview</span>
              </div>
              <h1 class="page-title">Project Billing &amp; Milestone Budget Burn-down</h1>
              <p class="page-subtitle">Industrial contracts, milestone pacing, cumulative receivables, and scheduled dates from MySQL database</p>
            </div>
            <div class="page-header-actions">
              <a href="api/finance_api.php?action=export_gl_csv" class="btn btn-outline">
                <span>📑 Export Milestone Matrix</span>
              </a>
              <button class="btn btn-primary-amber" onclick="window.finApp.openModal('modal-bill-milestone')">
                <span>+ Bill Milestone</span>
              </button>
            </div>
          </div>

          <!-- Project Billing Summary KPI Cards -->
          <div class="kpi-grid fin-grid-3col-mb" >
            <div class="fin-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Total Active Contract Value</span>
                <div class="kpi-icon-pill steel">💼</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value">€<?= number_format($totalContractValue, 2) ?></span>
              </div>
              <div class="kpi-footer">
                <span><?= count($projects) ?> Heavy Industry Contracts</span>
                <span class="kpi-trend up">Live Database Ceiling</span>
              </div>
            </div>

            <div class="fin-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Cumulative Billed to Date</span>
                <div class="kpi-icon-pill green">✓</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value">€<?= number_format($totalBilledToDate, 2) ?></span>
              </div>
              <div class="kpi-footer">
                <span>Overall Pacing: <?= round($portfolioPacing, 1) ?>%</span>
                <span class="kpi-trend up">On Schedule</span>
              </div>
            </div>

            <div class="fin-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Remaining Milestone Pipeline</span>
                <div class="kpi-icon-pill amber">⏳</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value">€<?= number_format($remainingPipeline, 2) ?></span>
              </div>
              <div class="kpi-footer">
                <span>To be billed across FY26</span>
                <span class="kpi-trend up">Secured by PO</span>
              </div>
            </div>
          </div>

          <!-- Project Billing Table Matrix -->
          <div class="fin-card fin-panel-flush" >
            <div class="fin-table-header-bar" >
              <div>
                <h3 class="card-title">Heavy Industry Engineering Projects &amp; Milestone Progress (vostokpribor.projects)</h3>
                <p class="fin-meta-subtext" >
                  Progress bar shows total billed vs contracted ceiling with upcoming milestone dates directly from DB
                </p>
              </div>
              <span class="confidential-system-pill fin-pill-mini" >
                CONTRACT COMPLIANCE: 100%
              </span>
            </div>

            <table class="fin-table">
              <thead>
                <tr>
                  <th class="fin-w-140" >Project ID</th>
                  <th>Customer &amp; Project Name</th>
                  <th class="fin-w-130-right" >Total Budget</th>
                  <th class="fin-w-130-right" >Billed to Date</th>
                  <th class="fin-w-130-right" >Remaining</th>
                  <th class="fin-w-220" >Billed vs Budget</th>
                  <th class="fin-w-220" >Next Scheduled Milestone</th>
                  <th class="fin-w-100-right" >Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($projects)): ?>
                  <tr>
                    <td class="fin-empty-state" colspan="8" >
                      No active projects found in database.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($projects as $prj):
                    $budget = (float)$prj['budget'];
                    $billed = (float)$prj['billed_to_date'];
                    $remaining = max(0, $budget - $billed);
                    $pct = $budget > 0 ? min(100, round(($billed / $budget) * 100, 1)) : 0;
                  ?>
                    <tr class="fin-table-row">
                      <td>
                        <span class="fin-mono-green-12" >
                          <?= htmlspecialchars($prj['prj_id']) ?>
                        </span>
                      </td>
                      <td>
                        <div class="fin-bold-navy" ><?= htmlspecialchars($prj['project_name']) ?></div>
                        <div class="fin-text-muted-11" ><?= htmlspecialchars($prj['company_name']) ?> · Sector: <?= htmlspecialchars($prj['sector'] ?? 'Industrial') ?></div>
                      </td>
                      <td class="fin-mono-navy-bold-right" >
                        €<?= number_format($budget, 2) ?>
                      </td>
                      <td class="fin-mono-green-bold-right" >
                        €<?= number_format($billed, 2) ?>
                      </td>
                      <td class="fin-mono-secondary-right" >
                        €<?= number_format($remaining, 2) ?>
                      </td>
                      <td>
                        <div class="budget-progress-container">
                          <div class="budget-progress-bar">
                            <div class="budget-progress-fill" style="width: <?= $pct ?>%;"></div>
                          </div>
                          <div class="budget-progress-meta">
                            <span><?= $pct ?>% Billed</span>
                            <span>€<?= number_format($remaining / 1000, 0) ?>K Left</span>
                          </div>
                        </div>
                      </td>
                      <td>
                        <?php if ($prj['next_milestone_desc']): ?>
                          <div class="fin-mono-bold-navy-115" >
                            <?= htmlspecialchars($prj['next_milestone_date'] ?? 'Scheduled') ?>
                          </div>
                          <div class="fin-text-muted-105" >
                            <?= htmlspecialchars($prj['next_milestone_desc']) ?> (<?= $prj['next_milestone_amount'] ? '€' . number_format($prj['next_milestone_amount'], 0) : '' ?>)
                          </div>
                        <?php else: ?>
                          <span class="fin-text-muted-11" >All Milestones Billed ✓</span>
                        <?php endif; ?>
                      </td>
                      <td class="fin-text-right" >
                        <?php if ($prj['next_cycle_id']): ?>
                          <button class="btn btn-primary-amber btn-sm" onclick="window.finApp.billMilestone(<?= $prj['next_cycle_id'] ?>)">
                            Bill →
                          </button>
                        <?php else: ?>
                          <a href="Invoices.php?search=<?= urlencode($prj['prj_id']) ?>" class="btn btn-outline btn-sm">
                            Invoices
                          </a>
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

  <!-- Modal: Bill Project Milestone (Inserts into DB) -->
  <div id="modal-bill-milestone" class="modal-backdrop">
    <div class="modal-dialog fin-modal-550" >
      <div class="modal-header">
        <div class="fin-text-bold-14" >Bill Project Milestone (Generates Invoice in DB)</div>
        <button class="modal-close" onclick="window.finApp.closeModal('modal-bill-milestone')">✕</button>
      </div>
      <div class="modal-body">
        <form onsubmit="event.preventDefault(); const cid = document.getElementById('select-milestone-cycle').value; window.finApp.billMilestone(cid);">
          <div class="fin-flex-col-gap-md" >
            <div>
              <label class="fin-field-label" >Select Unbilled Milestone from DB *</label>
              <select class="fin-form-control" id="select-milestone-cycle" required >
                <option value="">-- Choose Milestone to Ratify &amp; Bill --</option>
                <?php foreach ($unbilledMilestones as $m): ?>
                  <option value="<?= $m['cycle_id'] ?>">
                    <?= htmlspecialchars($m['prj_id']) ?> · <?= htmlspecialchars($m['milestone_description']) ?> [€<?= number_format($m['milestone_amount'], 2) ?>]
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="fin-notice-green" >
              Ratifying this milestone will automatically generate a new commercial invoice in <code>invoices</code>, insert line items into <code>invoice_items</code>, and mark <code>billing_cycles.invoiced = 1</code> in the database.
            </div>

            <div class="fin-flex-end-mt" >
              <button type="button" class="btn btn-outline" onclick="window.finApp.closeModal('modal-bill-milestone')">Cancel</button>
              <button type="submit" class="btn btn-primary-amber">Confirm &amp; Generate Invoice</button>
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