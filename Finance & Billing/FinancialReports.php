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

// 1. Fetch Official Financial Reports from Database
$reportsStmt = $pdo->query("SELECT * FROM financial_reports ORDER BY report_id ASC");
$reports = $reportsStmt->fetchAll();

// 2. Real Live Ledger Calculations from DB
$revStmt = $pdo->query("SELECT COALESCE(SUM(total_value),0) FROM invoices WHERE payment_status = 'Paid'");
$opRevenue = (float)$revStmt->fetchColumn();

$recStmt = $pdo->query("SELECT COALESCE(SUM(total_value),0) FROM invoices WHERE payment_status != 'Paid'");
$accountsReceivable = (float)$recStmt->fetchColumn();

$expStmt = $pdo->query("SELECT COALESCE(SUM(spent_amount),0) FROM budgets");
$opExpenses = (float)$expStmt->fetchColumn();

$cashStmt = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE reconciled = 1");
$cashReserves = (float)$cashStmt->fetchColumn();

$netIncome = $opRevenue - $opExpenses;
$ebitdaMargin = ($opRevenue > 0) ? round(($netIncome / $opRevenue) * 100, 1) : 0;
$totalAssets = $cashReserves + $accountsReceivable + 35000000.00; // Cash + AR + Fixed Industrial Plant Equipment

// 3. Departmental Expended Breakdown for P&L
$deptExpenses = $pdo->query("
    SELECT d.dept_name, b.spent_amount, b.allocated_amount 
    FROM budgets b 
    JOIN departments d ON b.department_code = d.dept_code 
    ORDER BY b.spent_amount DESC
")->fetchAll();

// Sidebar active metrics
$activeInvoicesCount = (int)$pdo->query("SELECT COUNT(*) FROM invoices WHERE payment_status != 'Paid'")->fetchColumn();
$unmatchedCount = (int)$pdo->query("SELECT COUNT(*) FROM payments WHERE reconciled = 0")->fetchColumn();
$totalProjectsCount = (int)$pdo->query("SELECT COUNT(*) FROM projects WHERE status != 'Closed'")->fetchColumn();
$avgBudgetBurn = (float)$pdo->query("SELECT AVG((spent_amount / NULLIF(allocated_amount, 0)) * 100) FROM budgets")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR Finance · Financial Reports &amp; RAS/IFRS Audits (SYS-08)</title>
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
                <span class="fin-opacity-50" >|</span>
                <span>AUDIT STATEMENTS (LIVE DB)</span>
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

          <button class="icon-button" onclick="window.finApp.showToast('Audit Log', 'Annual statutory audit reports verified against vostokpribor database ledger.')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
              <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
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
            <a href="ProjectBilling.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                    <line x1="12" y1="11" x2="12" y2="17" />
                    <line x1="9" y1="14" x2="15" y2="14" />
                  </svg></span>
                <span>Project Billing</span>
              </div>
              <span class="sidebar-badge badge-green"><?= $totalProjectsCount ?></span>
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
            <a href="FinancialReports.php" class="sidebar-nav-item active">
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
              Connected: <strong>vostokpribor.financial_reports</strong>
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
                <span class="breadcrumb-current">Financial Reports &amp; Audits</span>
              </div>
              <h1 class="page-title">Executive Financial Statements &amp; Audit Reports</h1>
              <p class="page-subtitle">Balance sheets, profit &amp; loss statements, and revenue recognition compliance generated live from MySQL database</p>
            </div>
            <div class="page-header-actions">
              <a href="api/finance_api.php?action=export_gl_csv" class="btn btn-outline">
                <span>📥 Download Signed Audit (.CSV)</span>
              </a>
            </div>
          </div>

          <!-- Live Ledger Calculation Metrics -->
          <div class="kpi-grid fin-grid-4col-mb" >
            <div class="fin-card kpi-card">
              <div class="kpi-header"><span class="kpi-title">Gross Operating Revenues</span>
                <div class="kpi-icon-pill green">✓</div>
              </div>
              <div class="kpi-value-row"><span class="kpi-value">€<?= number_format($opRevenue, 2) ?></span></div>
              <div class="kpi-footer"><span>Settled Commercial Bills</span></div>
            </div>
            <div class="fin-card kpi-card">
              <div class="kpi-header"><span class="kpi-title">Operational Expenditures</span>
                <div class="kpi-icon-pill steel">📉</div>
              </div>
              <div class="kpi-value-row"><span class="kpi-value">€<?= number_format($opExpenses, 2) ?></span></div>
              <div class="kpi-footer"><span>Division CapEx &amp; OpEx</span></div>
            </div>
            <div class="fin-card kpi-card">
              <div class="kpi-header"><span class="kpi-title">Accounts Receivable (AR)</span>
                <div class="kpi-icon-pill amber">⏳</div>
              </div>
              <div class="kpi-value-row"><span class="kpi-value">€<?= number_format($accountsReceivable, 2) ?></span></div>
              <div class="kpi-footer"><span>Pending Net-30 Invoices</span></div>
            </div>
            <div class="fin-card kpi-card">
              <div class="kpi-header"><span class="kpi-title">Net Operating Income</span>
                <div class="kpi-icon-pill green">📈</div>
              </div>
              <div class="kpi-value-row"><span class="kpi-value">€<?= number_format($netIncome, 2) ?></span></div>
              <div class="kpi-footer"><span>Margin: <?= $ebitdaMargin ?>%</span></div>
            </div>
          </div>

          <!-- Reports Grid Loaded from DB -->
          <div class="fin-grid-cards" >
            <?php foreach ($reports as $r):
              $borderCol = ($r['report_type'] === 'Statement of Operations') ? 'var(--fin-green)' : (($r['report_type'] === 'Balance Sheet') ? 'var(--fin-steel-blue)' : 'var(--fin-amber)');
            ?>
              <div class="fin-card fin-report-card card" style="border-top-color: <?= $borderCol ?>;">
                <div>
                  <div class="fin-flex-between-mb" >
                    <span class="fin-report-category" style="color: <?= $borderCol ?>;"><?= htmlspecialchars($r['report_type']) ?></span>
                    <span class="fin-mono-muted-10" ><?= htmlspecialchars($r['report_code']) ?></span>
                  </div>
                  <h4 class="fin-title-14" ><?= htmlspecialchars($r['title']) ?></h4>
                  <p class="fin-desc-card" >
                    <?= htmlspecialchars($r['summary_metrics']) ?>
                  </p>
                </div>
                <div>
                  <div class="fin-footer-card-meta" >
                    Sign-off: <strong><?= htmlspecialchars($r['signed_by']) ?></strong>
                  </div>
                  <button class="btn btn-outline btn-sm" onclick="openReportModal('<?= $r['report_type'] ?>', '<?= htmlspecialchars($r['title']) ?>')">
                    View Statement →
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal: Report Dossier Statement -->
  <div id="modal-report-view" class="modal-backdrop">
    <div class="modal-dialog fin-modal-650" >
      <div class="modal-header">
        <div class="fin-text-bold-14"  id="report-modal-title">Financial Statement Dossier</div>
        <button class="modal-close" onclick="window.finApp.closeModal('modal-report-view')">✕</button>
      </div>
      <div class="modal-body" id="report-modal-content">
        <div id="report-tab-operations" class="fin-report-tab hidden">
          <div class="fin-report-banner banner-green">
            <div class="fin-report-title">Statement of Operations (P&amp;L) · Live DB Figures</div>
            <div class="fin-report-subtitle">General Ledger Receivables &amp; Division Expenditures</div>
          </div>
          <table class="fin-table fin-table-bordered">
            <tr><td><strong>Gross Operating Revenues (Paid Invoices)</strong></td><td class="fin-val-right val-bold val-green">€<?= number_format($opRevenue, 2) ?></td></tr>
            <tr><td><strong>Accounts Receivable Outstanding</strong></td><td class="fin-val-right">€<?= number_format($accountsReceivable, 2) ?></td></tr>
            <tr><td><strong>Total Division Expenditures (Budgets Expended)</strong></td><td class="fin-val-right val-red">€<?= number_format($opExpenses, 2) ?></td></tr>
            <tr class="fin-row-highlight"><td><strong class="text-navy">Net Operating Income (EBITDA <?= $ebitdaMargin ?>%)</strong></td><td class="fin-total-val">€<?= number_format($netIncome, 2) ?></td></tr>
          </table>
        </div>

        <div id="report-tab-balance" class="fin-report-tab hidden">
          <div class="fin-report-banner banner-blue">
            <div class="fin-report-title">Quarterly Balance Sheet · Statement of Financial Position</div>
            <div class="fin-report-subtitle">Asset Reserves &amp; Receivables Pacing</div>
          </div>
          <table class="fin-table fin-table-bordered">
            <tr><td><strong>Cash &amp; Reconciled Electronic Clearing</strong></td><td class="fin-val-right val-bold val-green">€<?= number_format($cashReserves, 2) ?></td></tr>
            <tr><td><strong>Accounts Receivable Asset Pool</strong></td><td class="fin-val-right">€<?= number_format($accountsReceivable, 2) ?></td></tr>
            <tr><td><strong>Fixed Sensor Fabrication &amp; Cleanroom Plant</strong></td><td class="fin-val-right">€35,000,000.00</td></tr>
            <tr class="fin-row-highlight"><td><strong class="text-navy">Total Consolidated Assets</strong></td><td class="fin-total-val">€<?= number_format($totalAssets, 2) ?></td></tr>
          </table>
        </div>

        <div id="report-tab-tax" class="fin-report-tab hidden">
          <div class="fin-report-banner banner-amber">
            <div class="fin-report-title">Statutory Audit &amp; Tax Compliance Dossier</div>
            <div class="fin-report-subtitle">Federal Tax Service (FNS) &amp; VAT Settlement Verification</div>
          </div>
          <table class="fin-table fin-table-bordered">
            <tr><td><strong>Applicable VAT (20% on Billed Milestones)</strong></td><td class="fin-val-right val-bold">€<?= number_format($opRevenue * 0.20, 2) ?></td></tr>
            <tr><td><strong>Withholding Tax Deductions</strong></td><td class="fin-val-right">€0.00 (Exempt)</td></tr>
            <tr><td><strong>Statutory Audit Status</strong></td><td class="fin-val-right val-bold val-green">Full Compliance Verified ✓</td></tr>
            <tr class="fin-row-highlight"><td><strong class="text-navy">Tax Clearance Validity</strong></td><td class="fin-total-val">Through Q4 2026</td></tr>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" onclick="window.finApp.closeModal('modal-report-view')">Close</button>
        <button class="btn btn-primary-amber" onclick="window.print()">Print Statement 🖨️</button>
      </div>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
</body>

</html>