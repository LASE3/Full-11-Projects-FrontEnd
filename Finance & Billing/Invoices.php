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
$userClearance = htmlspecialchars($currUser['clearance_level'] ?? 'L4');

// Fetch all invoices joined with customer and project records from Database
$stmt = $pdo->query("
    SELECT 
        i.inv_id,
        i.cus_id,
        c.company_name,
        c.primary_contact_name,
        c.sector,
        i.prj_id,
        COALESCE(p.project_name, CONCAT('Project ', i.prj_id)) as project_name,
        i.total_value,
        i.currency,
        i.payment_status,
        i.issued_at,
        i.due_date,
        i.paid_at,
        i.payment_terms
    FROM invoices i
    JOIN customers c ON i.cus_id = c.cus_id
    LEFT JOIN projects p ON i.prj_id = p.prj_id
    ORDER BY i.issued_at DESC, i.inv_id DESC
");
$invoices = $stmt->fetchAll();

$totalCount = count($invoices);
$paidCount = 0;
$pendingCount = 0;
$overdueCount = 0;
foreach ($invoices as $inv) {
    if ($inv['payment_status'] === 'Paid') $paidCount++;
    elseif ($inv['payment_status'] === 'Pending') $pendingCount++;
    elseif ($inv['payment_status'] === 'Overdue') $overdueCount++;
}

// Fetch active customer accounts and projects for the Create Invoice modal
$customersList = $pdo->query("SELECT cus_id, company_name FROM customers ORDER BY company_name ASC")->fetchAll();
$projectsList = $pdo->query("SELECT prj_id, cus_id, COALESCE(project_name, prj_id) as project_name, budget FROM projects ORDER BY prj_id ASC")->fetchAll();

// Sidebar active metrics
$unmatchedCount = (int)$pdo->query("SELECT COUNT(*) FROM payments WHERE reconciled = 0")->fetchColumn();
$totalProjectsCount = (int)$pdo->query("SELECT COUNT(*) FROM projects WHERE status != 'Closed'")->fetchColumn();
$avgBudgetBurn = (float)$pdo->query("SELECT AVG((spent_amount / NULLIF(allocated_amount, 0)) * 100) FROM budgets")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR Finance · Invoices &amp; Accounts Receivable (SYS-08)</title>
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
                <span>INVOICES &amp; RECEIVABLES (LIVE DB)</span>
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

          <button class="icon-button" onclick="window.finApp.showToast('Billing Notification', 'Database reports <?= $overdueCount ?> overdue account(s) past Net-30 terms.')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <?php if ($overdueCount > 0): ?>
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
            <a href="Invoices.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></span>
                <span>Invoices</span>
              </div>
              <span class="sidebar-badge badge-amber"><?= $totalCount ?></span>
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
              Connected: <strong>vostokpribor.invoices</strong>
            </div>
          </div>
        </div>
      </aside>

      <!-- ========================================================================
           MAIN CONTENT AREA: SCREEN 2 INVOICES TABLE + DETAIL DRAWER
           ======================================================================== -->
      <main class="content-wrapper">
        <div class="portal-container">
          <!-- Page Header -->
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <a href="Dashboard.php">Finance &amp; Billing</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Invoices &amp; Receivables</span>
              </div>
              <h1 class="page-title">Accounts Receivable &amp; Invoice Registry</h1>
              <p class="page-subtitle">Live records from MySQL database · Search, filter, inspect line items, and ratify payments</p>
            </div>
            <div class="page-header-actions">
              <a href="api/finance_api.php?action=export_gl_csv" class="btn btn-outline">
                <span>📥 Export CSV</span>
              </a>
              <?php if ($overdueCount > 0): ?>
                <button class="btn btn-outline" onclick="window.finApp.showToast('Batch Reminders', 'Automated collection notifications dispatched for all <?= $overdueCount ?> overdue account(s).', 'amber')">
                  <span>⚡ Batch Reminders (<?= $overdueCount ?> Overdue)</span>
                </button>
              <?php endif; ?>
              <button class="btn btn-primary-amber" onclick="window.finApp.openModal('modal-create-invoice')">
                <span>+ Create Invoice</span>
              </button>
            </div>
          </div>

          <!-- Invoices Table Container -->
          <div class="fin-card" style="padding: 0; overflow: hidden;">
            <!-- Filter Toolbar -->
            <div style="padding: 1rem 1.25rem; background-color: #FFFFFF; border-bottom: 1px solid var(--fin-surface-border); display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
              <!-- Search Box -->
              <div style="position: relative; flex: 1; max-width: 380px;">
                <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--fin-text-muted); font-size: 13px;">🔍</span>
                <input 
                  type="text" 
                  id="invoice-search" 
                  placeholder="Filter by Invoice ID, customer, or project..." 
                  oninput="window.finApp.filterInvoices()"
                  style="width: 100%; padding: 0.5rem 0.75rem 0.5rem 2.2rem; border: 1px solid var(--fin-surface-border); border-radius: var(--fin-radius-md); font-size: 12.5px;" 
                />
              </div>

              <!-- Status Dropdown Filter -->
              <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.4rem;">
                  <span style="font-size: 11.5px; color: var(--fin-text-muted); font-weight: 600;">Status:</span>
                  <select id="invoice-status-filter" onchange="window.finApp.filterInvoices()" style="padding: 0.45rem 0.75rem; border: 1px solid var(--fin-surface-border); border-radius: var(--fin-radius-md); font-size: 12px; background: #FFFFFF;">
                    <option value="all">All Invoices (<?= $totalCount ?>)</option>
                    <option value="Paid">Paid (<?= $paidCount ?>)</option>
                    <option value="Pending">Pending (<?= $pendingCount ?>)</option>
                    <option value="Overdue">Overdue (<?= $overdueCount ?>)</option>
                  </select>
                </div>

                <button class="btn btn-outline btn-sm" onclick="document.getElementById('invoice-search').value=''; document.getElementById('invoice-status-filter').value='all'; window.finApp.filterInvoices();">
                  Reset
                </button>
              </div>
            </div>

            <!-- Table -->
            <table class="fin-table">
              <thead>
                <tr>
                  <th style="width: 140px;">Invoice ID</th>
                  <th>Customer Account</th>
                  <th>Project Specification</th>
                  <th style="width: 160px; text-align: right;">Total Value</th>
                  <th style="width: 130px; text-align: center;">Status</th>
                  <th style="width: 120px;">Issue Date</th>
                  <th style="width: 120px;">Due Date</th>
                  <th style="width: 110px; text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody class="invoice-table-body">
                <?php if (empty($invoices)): ?>
                  <tr>
                    <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--fin-text-muted);">
                      No invoices found in database. Click "+ Create Invoice" to issue a new commercial bill.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($invoices as $inv): ?>
                    <tr 
                      class="fin-table-row" 
                      data-id="<?= htmlspecialchars($inv['inv_id']) ?>" 
                      data-customer="<?= htmlspecialchars($inv['company_name']) ?>" 
                      data-project="<?= htmlspecialchars($inv['project_name']) ?>" 
                      data-status="<?= htmlspecialchars($inv['payment_status']) ?>" 
                      onclick="window.finApp.inspectInvoice('<?= htmlspecialchars($inv['inv_id']) ?>')"
                    >
                      <td>
                        <span style="font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-green); font-size: 12px;">
                          <?= htmlspecialchars($inv['inv_id']) ?>
                        </span>
                      </td>
                      <td>
                        <div style="font-weight: 700; color: var(--fin-navy);"><?= htmlspecialchars($inv['company_name']) ?></div>
                        <div style="font-size: 11px; color: var(--fin-text-muted);">Account #<?= htmlspecialchars($inv['cus_id']) ?> · <?= htmlspecialchars($inv['sector'] ?? 'Commercial') ?></div>
                      </td>
                      <td>
                        <span style="font-weight: 500;"><?= htmlspecialchars($inv['project_name']) ?></span>
                      </td>
                      <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 700; font-size: 13px; color: <?= $inv['payment_status'] === 'Overdue' ? 'var(--fin-confidential)' : 'var(--fin-navy)' ?>;">
                        €<?= number_format($inv['total_value'], 2) ?>
                      </td>
                      <td style="text-align: center;">
                        <?php if ($inv['payment_status'] === 'Paid'): ?>
                          <span class="status-badge-paid">✓ Paid</span>
                        <?php elseif ($inv['payment_status'] === 'Pending'): ?>
                          <span class="status-badge-pending">⚡ Pending</span>
                        <?php else: ?>
                          <span class="status-badge-overdue">⚠️ Overdue</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <span style="font-family: var(--fin-font-mono); font-size: 11.5px;">
                          <?= htmlspecialchars($inv['issued_at'] ?? 'N/A') ?>
                        </span>
                      </td>
                      <td>
                        <span style="font-family: var(--fin-font-mono); font-size: 11.5px; font-weight: <?= $inv['payment_status'] === 'Overdue' ? '700; color: var(--fin-confidential)' : 'normal' ?>;">
                          <?= htmlspecialchars($inv['due_date'] ?? 'Net-30') ?>
                        </span>
                      </td>
                      <td style="text-align: right;">
                        <button class="btn btn-outline btn-sm" onclick="event.stopPropagation(); window.finApp.inspectInvoice('<?= htmlspecialchars($inv['inv_id']) ?>')">
                          View →
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>

            <!-- Table Footer -->
            <div style="padding: 0.85rem 1.25rem; background-color: var(--fin-surface-dim); border-top: 1px solid var(--fin-surface-border); display: flex; align-items: center; justify-content: space-between; font-size: 11.5px; color: var(--fin-text-secondary);">
              <div>
                Showing <strong id="invoice-visible-count"><?= $totalCount ?></strong> of <strong><?= $totalCount ?></strong> database receivables records
              </div>
              <div style="display: flex; gap: 0.5rem; align-items: center;">
                <span style="color: var(--fin-text-muted);">Database: vostokpribor.invoices</span>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- ========================================================================
       MODAL: INVOICE DOSSIER & LINE ITEMS (POPULATED LIVE FROM DB)
       ======================================================================== -->
  <div id="modal-invoice-detail" class="modal-backdrop">
    <div class="modal-dialog" style="max-width: 720px;">
      <div class="modal-header">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
          <span style="font-size: 16px;">📑</span>
          <span style="font-weight: 700; font-size: 14px;">Invoice Dossier · <span id="modal-inv-id" style="font-family: var(--fin-font-mono); color: #72E2A6;">INV-2026-001</span></span>
        </div>
        <button class="modal-close" onclick="window.finApp.closeModal('modal-invoice-detail')">✕</button>
      </div>

      <div class="modal-body">
        <!-- Invoice Metadata Header -->
        <div style="background: var(--fin-surface-dim); padding: 1.25rem; border-radius: var(--fin-radius-md); border-left: 4px solid var(--fin-green); margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start;">
          <div>
            <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.25rem;">
              <h2 id="modal-inv-cust" style="font-size: 16px; font-weight: 700; color: var(--fin-navy);">--</h2>
              <span id="modal-inv-status" class="status-badge-paid">✓ Paid</span>
            </div>
            <div id="modal-inv-proj" style="font-size: 12.5px; color: var(--fin-text-secondary); font-weight: 500;">--</div>
            <div style="display: flex; gap: 1rem; margin-top: 0.5rem; font-size: 11px; color: var(--fin-text-muted);">
              <span>Issue Date: <strong id="modal-inv-issue" style="color: var(--fin-navy); font-family: var(--fin-font-mono);">--</strong></span>
              <span>Due Date: <strong id="modal-inv-due" style="color: var(--fin-navy); font-family: var(--fin-font-mono);">--</strong></span>
            </div>
          </div>
          <div style="text-align: right;">
            <div style="font-size: 11px; text-transform: uppercase; color: var(--fin-text-muted); font-weight: 600;">Total Invoice Amount</div>
            <div id="modal-inv-total" style="font-size: 22px; font-weight: 700; color: var(--fin-navy); font-family: var(--fin-font-mono);">€0.00</div>
          </div>
        </div>

        <!-- Line Items Table Loaded from DB -->
        <div style="margin-bottom: 1.5rem;">
          <div style="font-size: 12px; font-weight: 700; color: var(--fin-navy); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.6rem;">
            Billed Sensor Hardware &amp; Commissioning Line Items (From Database)
          </div>
          <table class="fin-table" style="border: 1px solid var(--fin-surface-border);">
            <thead>
              <tr>
                <th>Description &amp; Part Identifier</th>
                <th style="width: 70px; text-align: center;">Qty</th>
                <th style="width: 130px; text-align: right;">Unit Price</th>
                <th style="width: 140px; text-align: right;">Line Total</th>
              </tr>
            </thead>
            <tbody id="modal-inv-items-body">
              <!-- Dynamically populated from DB -->
            </tbody>
          </table>
        </div>

        <!-- Payment History Timeline Loaded from DB -->
        <div>
          <div style="font-size: 12px; font-weight: 700; color: var(--fin-navy); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.6rem;">
            Payment Settlement &amp; Clearing History (vostokpribor.payments)
          </div>
          <div id="modal-inv-timeline-body" style="display: flex; flex-direction: column; gap: 0.5rem;">
            <!-- Dynamically populated from DB -->
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="modal-footer">
        <button class="btn btn-outline" onclick="window.finApp.closeModal('modal-invoice-detail')">Close</button>
        <div style="display: flex; gap: 0.75rem;">
          <button id="btn-modal-send-reminder" class="btn btn-amber-outline">
            <span>⚡ Send Payment Reminder</span>
          </button>
          <button id="btn-modal-mark-paid" class="btn btn-green">
            <span>✓ Mark as Paid (Commit to DB)</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================
       MODAL: CREATE NEW INVOICE (SENDS LIVE TO MYSQL DATABASE)
       ======================================================================== -->
  <div id="modal-create-invoice" class="modal-backdrop">
    <div class="modal-dialog" style="max-width: 580px;">
      <div class="modal-header">
        <div style="font-weight: 700; font-size: 14px;">Generate Enterprise Commercial Invoice (DB Insert)</div>
        <button class="modal-close" onclick="window.finApp.closeModal('modal-create-invoice')">✕</button>
      </div>
      <div class="modal-body">
        <form onsubmit="event.preventDefault(); window.finApp.handleCreateInvoiceSubmit(this);">
          <div style="display: flex; flex-direction: column; gap: 1rem;">
            <!-- Customer Selector -->
            <div>
              <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--fin-navy); margin-bottom: 0.25rem;">Customer Account (vostokpribor.customers) *</label>
              <select name="cus_id" required style="width: 100%; padding: 0.55rem; border: 1px solid var(--fin-surface-border); border-radius: var(--fin-radius-md); font-size: 13px;">
                <option value="">-- Select Customer Account --</option>
                <?php foreach ($customersList as $cust): ?>
                  <option value="<?= htmlspecialchars($cust['cus_id']) ?>">
                    <?= htmlspecialchars($cust['company_name']) ?> (<?= htmlspecialchars($cust['cus_id']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Project Selector -->
            <div>
              <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--fin-navy); margin-bottom: 0.25rem;">Project Association (vostokpribor.projects) *</label>
              <select name="prj_id" required style="width: 100%; padding: 0.55rem; border: 1px solid var(--fin-surface-border); border-radius: var(--fin-radius-md); font-size: 13px;">
                <option value="">-- Select Related Project --</option>
                <?php foreach ($projectsList as $prj): ?>
                  <option value="<?= htmlspecialchars($prj['prj_id']) ?>">
                    <?= htmlspecialchars($prj['prj_id']) ?> · <?= htmlspecialchars($prj['project_name']) ?> (Budget: €<?= number_format($prj['budget'], 2) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Line Item Description -->
            <div>
              <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--fin-navy); margin-bottom: 0.25rem;">Line Item Description (invoice_items) *</label>
              <input type="text" name="line_description" required placeholder="e.g. Optical IR Pyrometer Probe Array & Commissioning" style="width: 100%; padding: 0.55rem; border: 1px solid var(--fin-surface-border); border-radius: var(--fin-radius-md); font-size: 13px;" />
            </div>

            <!-- Amount and Currency Grid -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 0.75rem;">
              <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--fin-navy); margin-bottom: 0.25rem;">Invoice Total Value *</label>
                <input type="number" name="total_value" step="0.01" min="1" required placeholder="85000.00" style="width: 100%; padding: 0.55rem; border: 1px solid var(--fin-surface-border); border-radius: var(--fin-radius-md); font-size: 13px;" />
              </div>
              <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--fin-navy); margin-bottom: 0.25rem;">Currency</label>
                <select name="currency" style="width: 100%; padding: 0.55rem; border: 1px solid var(--fin-surface-border); border-radius: var(--fin-radius-md); font-size: 13px;">
                  <option value="EUR" selected>EUR (€)</option>
                  <option value="USD">USD ($)</option>
                  <option value="RUB">RUB (₽)</option>
                </select>
              </div>
            </div>

            <!-- Payment Terms & Due Date -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
              <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--fin-navy); margin-bottom: 0.25rem;">Payment Terms *</label>
                <select name="payment_terms" style="width: 100%; padding: 0.55rem; border: 1px solid var(--fin-surface-border); border-radius: var(--fin-radius-md); font-size: 13px;">
                  <option value="Net-30">Net-30 Days</option>
                  <option value="Net-60">Net-60 Days</option>
                  <option value="Due Upon Delivery (FAT)">Due Upon Delivery (FAT)</option>
                  <option value="Advance 100%">Advance 100%</option>
                </select>
              </div>
              <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--fin-navy); margin-bottom: 0.25rem;">Due Date</label>
                <input type="date" name="due_date" style="width: 100%; padding: 0.55rem; border: 1px solid var(--fin-surface-border); border-radius: var(--fin-radius-md); font-size: 13px;" />
              </div>
            </div>

            <!-- Notes -->
            <div>
              <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--fin-navy); margin-bottom: 0.25rem;">Ledger Reference / Remittance Memo</label>
              <input type="text" name="notes" placeholder="e.g. Contract Milestones PO-2026-VP" style="width: 100%; padding: 0.55rem; border: 1px solid var(--fin-surface-border); border-radius: var(--fin-radius-md); font-size: 13px;" />
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 0.5rem;">
              <button type="button" class="btn btn-outline" onclick="window.finApp.closeModal('modal-create-invoice')">Cancel</button>
              <button type="submit" class="btn btn-primary-amber">Ratify &amp; Issue Invoice</button>
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
