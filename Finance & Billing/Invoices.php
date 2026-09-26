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
                <span class="fin-opacity-50" >|</span>
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
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
              <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
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
            <a href="Invoices.php" class="sidebar-nav-item active">
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
              <span class="sidebar-badge badge-amber"><?= $totalCount ?></span>
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
          <div class="fin-card fin-panel-flush" >
            <!-- Filter Toolbar -->
            <div class="fin-table-header-bar-wrap" >
              <!-- Search Box -->
              <div class="fin-search-container" >
                <span class="fin-search-icon" >🔍</span>
                <input class="fin-search-input"
                  type="text"
                  id="invoice-search"
                  placeholder="Filter by Invoice ID, customer, or project..."
                  oninput="window.finApp.filterInvoices()"
                   />
              </div>

              <!-- Status Dropdown Filter -->
              <div class="fin-flex-gap-md" >
                <div class="fin-flex-gap-4" >
                  <span class="fin-text-muted-bold-115" >Status:</span>
                  <select class="fin-select-filter" id="invoice-status-filter" onchange="window.finApp.filterInvoices()" >
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

                        <table class="fin-table">
              <thead>
                <tr>
                  <th class="fin-w-140" >Invoice ID</th>
                  <th>Customer Account</th>
                  <th>Project Specification</th>
                  <th class="fin-w-160-right" >Total Value</th>
                  <th class="fin-w-130-center" >Status</th>
                  <th class="fin-w-120" >Issue Date</th>
                  <th class="fin-w-120" >Due Date</th>
                  <th class="fin-w-110-right" >Action</th>
                </tr>
              </thead>
              <tbody class="invoice-table-body">
                <?php if (empty($invoices)): ?>
                  <tr>
                    <td class="fin-empty-state-lg" colspan="8" >
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
                      onclick="window.finApp.inspectInvoice('<?= htmlspecialchars($inv['inv_id']) ?>')">
                      <td>
                        <span class="fin-mono-green-12" >
                          <?= htmlspecialchars($inv['inv_id']) ?>
                        </span>
                      </td>
                      <td>
                        <div class="fin-bold-navy" ><?= htmlspecialchars($inv['company_name']) ?></div>
                        <div class="fin-text-muted-11" >Account #<?= htmlspecialchars($inv['cus_id']) ?> · <?= htmlspecialchars($inv['sector'] ?? 'Commercial') ?></div>
                      </td>
                      <td>
                        <span class="fin-font-medium" ><?= htmlspecialchars($inv['project_name']) ?></span>
                      </td>
                      <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 700; font-size: 13px; color: <?= $inv['payment_status'] === 'Overdue' ? 'var(--fin-confidential)' : 'var(--fin-navy)' ?>;">
                        €<?= number_format($inv['total_value'], 2) ?>
                      </td>
                      <td class="fin-text-center" >
                        <?php if ($inv['payment_status'] === 'Paid'): ?>
                          <span class="status-badge-paid">✓ Paid</span>
                        <?php elseif ($inv['payment_status'] === 'Pending'): ?>
                          <span class="status-badge-pending">⚡ Pending</span>
                        <?php else: ?>
                          <span class="status-badge-overdue">⚠️ Overdue</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <span class="fin-mono-115" >
                          <?= htmlspecialchars($inv['issued_at'] ?? 'N/A') ?>
                        </span>
                      </td>
                      <td>
                        <span style="font-family: var(--fin-font-mono); font-size: 11.5px; font-weight: <?= $inv['payment_status'] === 'Overdue' ? '700; color: var(--fin-confidential)' : 'normal' ?>;">
                          <?= htmlspecialchars($inv['due_date'] ?? 'Net-30') ?>
                        </span>
                      </td>
                      <td class="fin-text-right" >
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
            <div class="fin-footer-pagination" >
              <div>
                Showing <strong id="invoice-visible-count"><?= $totalCount ?></strong> of <strong><?= $totalCount ?></strong> database receivables records
              </div>
              <div class="fin-flex-gap-sm" >
                <span class="fin-text-muted" >Database: vostokpribor.invoices</span>
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
    <div class="modal-dialog fin-modal-720" >
      <div class="modal-header">
        <div class="fin-flex-gap-sm" >
          <span class="fin-text-16" >📑</span>
          <span class="fin-text-bold-14" >Invoice Dossier · <span class="fin-mono-teal" id="modal-inv-id" >INV-2026-001</span></span>
        </div>
        <button class="modal-close" onclick="window.finApp.closeModal('modal-invoice-detail')">✕</button>
      </div>

      <div class="modal-body">
        <!-- Invoice Metadata Header -->
        <div class="fin-banner-invoice" >
          <div>
            <div class="fin-flex-gap-6-mb" >
              <h2 class="fin-title-16" id="modal-inv-cust" >--</h2>
              <span id="modal-inv-status" class="status-badge-paid">✓ Paid</span>
            </div>
            <div class="fin-desc-125" id="modal-inv-proj" >--</div>
            <div class="fin-meta-row-sm" >
              <span>Issue Date: <strong class="fin-mono-navy" id="modal-inv-issue" >--</strong></span>
              <span>Due Date: <strong class="fin-mono-navy" id="modal-inv-due" >--</strong></span>
            </div>
          </div>
          <div class="fin-text-right" >
            <div class="fin-caption-muted-11" >Total Invoice Amount</div>
            <div class="fin-heading-22" id="modal-inv-total" >€0.00</div>
          </div>
        </div>

        <!-- Line Items Table Loaded from DB -->
        <div class="fin-mb-6" >
          <div class="fin-section-title-sm" >
            Billed Sensor Hardware &amp; Commissioning Line Items (From Database)
          </div>
          <table class="fin-table fin-card-divided" >
            <thead>
              <tr>
                <th>Description &amp; Part Identifier</th>
                <th class="fin-w-70-center" >Qty</th>
                <th class="fin-w-130-right" >Unit Price</th>
                <th class="fin-w-140-right" >Line Total</th>
              </tr>
            </thead>
            <tbody id="modal-inv-items-body">
              <!-- Dynamically populated from DB -->
            </tbody>
          </table>
        </div>

        <!-- Payment History Timeline Loaded from DB -->
        <div>
          <div class="fin-section-title-sm" >
            Payment Settlement &amp; Clearing History (vostokpribor.payments)
          </div>
          <div class="fin-flex-col-gap-sm" id="modal-inv-timeline-body" >
            <!-- Dynamically populated from DB -->
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="modal-footer">
        <button class="btn btn-outline" onclick="window.finApp.closeModal('modal-invoice-detail')">Close</button>
        <div class="fin-gap-md" >
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
    <div class="modal-dialog fin-modal-580" >
      <div class="modal-header">
        <div class="fin-text-bold-14" >Generate Enterprise Commercial Invoice (DB Insert)</div>
        <button class="modal-close" onclick="window.finApp.closeModal('modal-create-invoice')">✕</button>
      </div>
      <div class="modal-body">
        <form onsubmit="event.preventDefault(); window.finApp.handleCreateInvoiceSubmit(this);">
          <div class="fin-flex-col-gap-md" >
            <!-- Customer Selector -->
            <div>
              <label class="fin-field-label" >Customer Account (vostokpribor.customers) *</label>
              <select class="fin-form-control" name="cus_id" required >
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
              <label class="fin-field-label" >Project Association (vostokpribor.projects) *</label>
              <select class="fin-form-control" name="prj_id" required >
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
              <label class="fin-field-label" >Line Item Description (invoice_items) *</label>
              <input class="fin-form-control" type="text" name="line_description" required placeholder="e.g. Optical IR Pyrometer Probe Array & Commissioning"  />
            </div>

            <!-- Amount and Currency Grid -->
            <div class="fin-grid-2-1col-sm" >
              <div>
                <label class="fin-field-label" >Invoice Total Value *</label>
                <input class="fin-form-control" type="number" name="total_value" step="0.01" min="1" required placeholder="85000.00"  />
              </div>
              <div>
                <label class="fin-field-label" >Currency</label>
                <select class="fin-form-control" name="currency" >
                  <option value="EUR" selected>EUR (€)</option>
                  <option value="USD">USD ($)</option>
                  <option value="RUB">RUB (₽)</option>
                </select>
              </div>
            </div>

            <!-- Payment Terms & Due Date -->
            <div class="fin-grid-2col-sm" >
              <div>
                <label class="fin-field-label" >Payment Terms *</label>
                <select class="fin-form-control" name="payment_terms" >
                  <option value="Net-30">Net-30 Days</option>
                  <option value="Net-60">Net-60 Days</option>
                  <option value="Due Upon Delivery (FAT)">Due Upon Delivery (FAT)</option>
                  <option value="Advance 100%">Advance 100%</option>
                </select>
              </div>
              <div>
                <label class="fin-field-label" >Due Date</label>
                <input class="fin-form-control" type="date" name="due_date"  />
              </div>
            </div>

            <!-- Notes -->
            <div>
              <label class="fin-field-label" >Ledger Reference / Remittance Memo</label>
              <input class="fin-form-control" type="text" name="notes" placeholder="e.g. Contract Milestones PO-2026-VP"  />
            </div>

            <div class="fin-flex-end-mt" >
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