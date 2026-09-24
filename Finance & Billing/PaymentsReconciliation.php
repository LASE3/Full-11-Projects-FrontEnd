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

// 1. Fetch Pending Unmatched Payments from Database
$unmatchedStmt = $pdo->query("
    SELECT 
        p.payment_id,
        COALESCE(p.tx_reference, CONCAT('TX-WIRE-', p.payment_id)) as tx_reference,
        COALESCE(c.company_name, p.sender_name, 'External Treasury Clearing') as remitter,
        COALESCE(p.method, 'SPFS Direct Clearance') as method,
        COALESCE(p.remittance_memo, CONCAT('Ref: PO-WIRE-', p.payment_id)) as remittance_memo,
        p.amount,
        COALESCE(p.payment_date, CURDATE()) as payment_date,
        COALESCE(p.bank_gateway, 'Central Settlement Grid') as bank_gateway,
        p.inv_id
    FROM payments p
    LEFT JOIN invoices i ON p.inv_id = i.inv_id
    LEFT JOIN customers c ON i.cus_id = c.cus_id
    WHERE p.reconciled = 0
    ORDER BY p.payment_date DESC, p.payment_id DESC
");
$unmatchedWires = $unmatchedStmt->fetchAll();

$unmatchedCount = count($unmatchedWires);
$unmatchedTotal = 0;
foreach ($unmatchedWires as $w) {
    $unmatchedTotal += (float)$w['amount'];
}

// 2. Fetch Reconciled Payment Archive from Database
$reconciledStmt = $pdo->query("
    SELECT 
        p.payment_id,
        COALESCE(p.tx_reference, CONCAT('TX-REC-', p.payment_id)) as tx_reference,
        COALESCE(c.company_name, p.sender_name, 'Verified Client Account') as remitter,
        COALESCE(p.method, 'Bank Wire') as method,
        p.remittance_memo,
        p.amount,
        p.payment_date,
        p.inv_id
    FROM payments p
    LEFT JOIN invoices i ON p.inv_id = i.inv_id
    LEFT JOIN customers c ON i.cus_id = c.cus_id
    WHERE p.reconciled = 1
    ORDER BY p.payment_date DESC, p.payment_id DESC
    LIMIT 20
");
$reconciledWires = $reconciledStmt->fetchAll();

// 3. Fetch Pending Invoices for Manual Pairing Options
$pendingInvoices = $pdo->query("
    SELECT i.inv_id, i.total_value, c.company_name 
    FROM invoices i 
    JOIN customers c ON i.cus_id = c.cus_id 
    WHERE i.payment_status != 'Paid'
    ORDER BY i.issued_at DESC
")->fetchAll();

// Sidebar active metrics
$activeInvoicesCount = (int)$pdo->query("SELECT COUNT(*) FROM invoices WHERE payment_status != 'Paid'")->fetchColumn();
$totalProjectsCount = (int)$pdo->query("SELECT COUNT(*) FROM projects WHERE status != 'Closed'")->fetchColumn();
$avgBudgetBurn = (float)$pdo->query("SELECT AVG((spent_amount / NULLIF(allocated_amount, 0)) * 100) FROM budgets")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR Finance · Payments &amp; Bank Reconciliation (SYS-08)</title>
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
                <span style="opacity: 0.5;">|</span>
                <span>PAYMENT RECONCILIATION DESK (LIVE DB)</span>
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

          <button class="icon-button" onclick="window.finApp.showToast('Reconciliation Notice', 'Database: <?= $unmatchedCount ?> bank wire(s) pending ledger pairing.')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <?php if ($unmatchedCount > 0): ?>
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
            <a href="PaymentsReconciliation.php" class="sidebar-nav-item active">
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
            <a href="Budgets.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>
                <span>Budgets</span>
              </div>
              <span class="sidebar-badge"><?= round($avgBudgetBurn) ?>%</span>
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
              Connected: <strong>vostokpribor.payments</strong>
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
                <span class="breadcrumb-current">Payments &amp; Bank Reconciliation</span>
              </div>
              <h1 class="page-title">Electronic Payment Clearing &amp; Bank Telemetry</h1>
              <p class="page-subtitle">Automatic clearing house, SPFS interbank telemetry, and manual transaction pairing queue from MySQL database</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.finApp.syncBankFeeds()">
                <span>🔄 Ingest Bank Telemetry Wire</span>
              </button>
              <button class="btn btn-primary-amber" onclick="window.finApp.runAutoMatch()">
                <span>⚡ Run Auto-Match Engine</span>
              </button>
            </div>
          </div>

          <!-- Unmatched Payments Desk -->
          <div class="fin-card" style="padding: 0; overflow: hidden; margin-bottom: 2rem;">
            <div style="padding: 1rem 1.25rem; background: #FFFFFF; border-bottom: 1px solid var(--fin-surface-border); display: flex; align-items: center; justify-content: space-between;">
              <h3 class="card-title">Pending Unmatched Transactions Desk (<?= $unmatchedCount ?>)</h3>
              <span class="confidential-system-pill" style="font-size: 9.5px; padding: 2px 6px;">
                UNMATCHED POOL: €<?= number_format($unmatchedTotal, 2) ?>
              </span>
            </div>

            <table class="fin-table">
              <thead>
                <tr>
                  <th>Transaction ID</th>
                  <th>Originating Account</th>
                  <th>Payment Type &amp; Remittance Memo</th>
                  <th>Ingested Amount</th>
                  <th>Received Timestamp</th>
                  <th style="text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody id="unmatched-desk-body">
                <?php if (empty($unmatchedWires)): ?>
                  <tr>
                    <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--fin-text-muted);">
                      ✓ All electronic bank wires are matched and reconciled in the database! Click "Ingest Bank Telemetry Wire" to simulate a new settlement.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($unmatchedWires as $wire): ?>
                    <tr id="tx-row-<?= $wire['payment_id'] ?>" class="fin-table-row">
                      <td>
                        <span style="font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-green);">
                          <?= htmlspecialchars($wire['tx_reference']) ?>
                        </span>
                      </td>
                      <td>
                        <strong><?= htmlspecialchars($wire['remitter']) ?></strong>
                        <div style="font-size: 11px; color: var(--fin-text-muted);"><?= htmlspecialchars($wire['bank_gateway']) ?></div>
                      </td>
                      <td>
                        <?= htmlspecialchars($wire['method']) ?> · <code><?= htmlspecialchars($wire['remittance_memo']) ?></code>
                      </td>
                      <td>
                        <strong style="font-family: var(--fin-font-mono); font-size: 13px; color: var(--fin-navy);">
                          €<?= number_format($wire['amount'], 2) ?>
                        </strong>
                      </td>
                      <td>
                        <span style="font-family: var(--fin-font-mono); font-size: 11.5px;">
                          <?= htmlspecialchars($wire['payment_date']) ?>
                        </span>
                      </td>
                      <td style="text-align: right;">
                        <button class="btn btn-primary-amber btn-sm" onclick="window.finApp.reconcilePayment(<?= $wire['payment_id'] ?>, '<?= $wire['inv_id'] ?? '' ?>')">
                          Reconcile ⚡
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Reconciled Ledger Archive -->
          <div class="fin-card" style="padding: 0; overflow: hidden;">
            <div style="padding: 1rem 1.25rem; background: #FFFFFF; border-bottom: 1px solid var(--fin-surface-border); display: flex; align-items: center; justify-content: space-between;">
              <div>
                <h3 class="card-title">Reconciled Settlement Archive (vostokpribor.payments)</h3>
                <p style="font-size: 11.5px; color: var(--fin-text-secondary); margin-top: 2px;">
                  Fully cleared electronic transactions paired to general ledger invoice receivables
                </p>
              </div>
              <span class="security-badge-status">● AUDITED</span>
            </div>

            <table class="fin-table">
              <thead>
                <tr>
                  <th>Settlement Ref</th>
                  <th>Remitting Customer Account</th>
                  <th>Method &amp; Clearing Details</th>
                  <th>Reconciled Invoice</th>
                  <th>Settled Value</th>
                  <th>Clearing Date</th>
                  <th style="text-align: right;">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($reconciledWires)): ?>
                  <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--fin-text-muted);">
                      No reconciled payments found in archive.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($reconciledWires as $rec): ?>
                    <tr class="fin-table-row">
                      <td>
                        <span style="font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-navy); font-size: 12px;">
                          <?= htmlspecialchars($rec['tx_reference']) ?>
                        </span>
                      </td>
                      <td>
                        <div style="font-weight: 600; color: var(--fin-navy);"><?= htmlspecialchars($rec['remitter']) ?></div>
                      </td>
                      <td>
                        <span style="font-size: 12px; color: var(--fin-text-secondary);"><?= htmlspecialchars($rec['method']) ?></span>
                        <?php if ($rec['remittance_memo']): ?>
                          <div style="font-family: var(--fin-font-mono); font-size: 10.5px; color: var(--fin-text-muted);"><?= htmlspecialchars($rec['remittance_memo']) ?></div>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if ($rec['inv_id']): ?>
                          <a href="Invoices.php?search=<?= urlencode($rec['inv_id']) ?>" style="font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-green); text-decoration: none;">
                            <?= htmlspecialchars($rec['inv_id']) ?> ↗
                          </a>
                        <?php else: ?>
                          <span style="color: var(--fin-text-muted); font-size: 11px;">General Balance</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <span style="font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-navy);">
                          €<?= number_format($rec['amount'], 2) ?>
                        </span>
                      </td>
                      <td>
                        <span style="font-family: var(--fin-font-mono); font-size: 11.5px;">
                          <?= htmlspecialchars($rec['payment_date']) ?>
                        </span>
                      </td>
                      <td style="text-align: right;">
                        <span class="status-badge-paid">✓ Cleared</span>
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

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
</body>
</html>
