<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('CRM');
$currUser = $_SESSION['vostok_user'] ?? ['full_name' => 'Mikhail Sorokin', 'role_name' => 'VP Enterprise Sales', 'clearance_level' => 'L4'];

$pdo = getDbConnection();
$custCount = (int)$pdo->query("SELECT COUNT(*) FROM `customers`")->fetchColumn();
$totalArr = (float)$pdo->query("SELECT COALESCE(SUM(contract_value), 0) FROM `contracts` WHERE status = 'Active'")->fetchColumn();
$oppCount = (int)$pdo->query("SELECT COUNT(*) FROM `opportunities` WHERE stage NOT IN ('Won', 'Lost')")->fetchColumn();
$pipelineVal = (float)$pdo->query("SELECT COALESCE(SUM(estimated_value), 0) FROM `opportunities` WHERE stage NOT IN ('Won', 'Lost')")->fetchColumn();

// Unique dynamic sectors from live database
$sectors = $pdo->query("SELECT sector, COUNT(*) as cnt FROM `customers` WHERE sector IS NOT NULL AND sector != '' GROUP BY sector ORDER BY cnt DESC")->fetchAll(PDO::FETCH_ASSOC);

// Full Customers Matrix
$customers = $pdo->query("
    SELECT 
        c.cus_id,
        c.company_name,
        c.sector,
        c.primary_contact_name,
        c.headquarters,
        c.health_score,
        c.account_tier,
        c.status,
        e.full_name AS account_manager_name,
        COALESCE((SELECT SUM(contract_value) FROM `contracts` WHERE cus_id = c.cus_id AND status = 'Active'), 0) AS total_contract_value,
        (SELECT contract_ref FROM `contracts` WHERE cus_id = c.cus_id AND status = 'Active' ORDER BY contract_value DESC LIMIT 1) AS active_contract_ref,
        (SELECT end_date FROM `contracts` WHERE cus_id = c.cus_id AND status = 'Active' ORDER BY contract_value DESC LIMIT 1) AS contract_end
    FROM `customers` c
    LEFT JOIN `employees` e ON c.account_manager_emp_id = e.emp_id
    ORDER BY total_contract_value DESC, c.company_name ASC
")->fetchAll(PDO::FETCH_ASSOC);

$firstCusId = !empty($customers) ? $customers[0]['cus_id'] : 'CUS-1001';
$firstCusName = !empty($customers) ? $customers[0]['company_name'] : 'Severstal Metallurgy PJSC';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR CRM · Enterprise Customers Directory</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>

  <div class="app-container">
    <!-- Top Navigation Bar -->
    <header class="top-nav">
      <div class="top-nav__accent-stripe"></div>
      <div class="top-nav__content">
        <div class="brand-section">
          <button class="mobile-nav-toggle" id="crm-sidebar-toggle" onclick="document.body.classList.toggle('sidebar-open')" title="Toggle Menu"><span class="material-symbols-outlined">menu</span></button>
          <a href="Dashboard.php" class="brand-logo-container">
            <img alt="VOSTOKPRIBOR Official Mark" class="brand-logo-img" src="assets/logo.svg" />
            <div class="brand-divider"></div>
            <div class="brand-title-group">
              <div class="brand-title-row">
                <span class="brand-name">VOSTOKPRIBOR</span>
                <span class="system-tag">CRM · SYS 05</span>
              </div>
              <div class="brand-subline">
                <span class="status-dot-pulse"></span>
                <span>crm.vostokpribor.local</span>
                <span class="crm-opacity-50" >|</span>
                <span>CUSTOMER DIRECTORY</span>
              </div>
            </div>
          </a>
        </div>

        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search enterprise clients (e.g. Severstal, NLMK, Norilsk)..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <div class="top-nav__actions">
          <div class="pipeline-sync-badge">
            <span class="crm-status-success" >●</span>
            <span>Sync: <strong>Active 99.98%</strong></span>
          </div>
          <button class="btn btn-primary-amber btn-sm" onclick="window.location.href='CustomerDetail.php'">
            <span>Inspect Key Client</span>
          </button>
          <button class="icon-button notifications-btn" id="notifications-toggle-btn" title="Live Sales Telemetry & Notifications">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
              <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
          </button>
          <div class="top-user-profile" onclick="window.crmApp.showToast('Active User Session', '<?= htmlspecialchars($currUser['full_name'] ?? 'Mikhail Sorokin') ?> · <?= htmlspecialchars($currUser['role_name'] ?? 'VP Enterprise Sales') ?> · <?= htmlspecialchars($currUser['clearance_level'] ?? 'L4') ?> Clearance')">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" alt="<?= htmlspecialchars($currUser['full_name'] ?? 'User') ?>" class="user-avatar-top" />
            <div class="user-details-top">
              <span class="user-name-top"><?= htmlspecialchars($currUser['full_name'] ?? 'Mikhail Sorokin') ?></span>
              <span class="user-role-top"><?= htmlspecialchars($currUser['role_name'] ?? 'VP Enterprise Sales') ?></span>
            </div>
          </div>
        </div>

        <!-- Top Bar Sign Out -->
        <a href="./api/logout.php?redirect=../CRM/login.php" class="top-signout-btn" title="Sign Out of CRM" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" ><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
      </div>
    </header>

    <div class="main-layout">
      <!-- Sidebar Navigation -->
      <aside class="sidebar">
        <div>
          <div class="sidebar-section-title">Sales Management</div>
          <nav class="sidebar-nav">
            <a href="Dashboard.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                  </svg>
                </span>
                <span>Dashboard</span>
              </div>
            </a>

            <a href="Leads.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                  </svg>
                </span>
                <span>Leads</span>
              </div>
              <span class="sidebar-badge">28</span>
            </a>

            <!-- Customers (Active) -->
            <a href="Customers.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 21h18" />
                    <path d="M5 21V7l8-4v18" />
                    <path d="M19 21V11l-6-4" />
                    <path d="M9 9h1" />
                    <path d="M9 13h1" />
                    <path d="M9 17h1" />
                  </svg>
                </span>
                <span>Customers</span>
              </div>
              <span class="sidebar-badge">14</span>
            </a>

            <a href="Opportunities.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 14 14" />
                  </svg>
                </span>
                <span>Opportunities</span>
              </div>
              <span class="sidebar-badge">42</span>
            </a>

            <a href="QuotesAndContracts.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                  </svg>
                </span>
                <span>Quotes &amp; Contracts</span>
              </div>
              <span class="sidebar-badge">19</span>
            </a>

            <a href="Projects.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="12 2 2 7 12 12 22 7 12 2" />
                    <polyline points="2 17 12 22 22 17" />
                    <polyline points="2 12 12 17 22 12" />
                  </svg>
                </span>
                <span>Projects</span>
              </div>
              <span class="sidebar-badge">14</span>
            </a>

            <a href="SalesForecast.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10" />
                    <line x1="12" y1="20" x2="12" y2="4" />
                    <line x1="6" y1="20" x2="6" y2="14" />
                  </svg>
                </span>
                <span>Sales Forecast</span>
              </div>
              <span class="sidebar-badge badge-green">+14%</span>
            </a>
          
            <!-- Inter-System Integrations (SYS02) -->
            <a href="Integrations.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00E5FF" stroke-width="2">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                  </svg>
                </span>
                <span class="crm-nav-integrations" style="color: #00E5FF; font-weight: 600;">System Integrations</span>
              </div>
              <span class="sidebar-badge crm-badge-integrations" style="background: rgba(0, 229, 255, 0.15); color: #00E5FF; border: 1px solid rgba(0, 229, 255, 0.3);">SYS02</span>
            </a>
          </nav>
        </div>


        <div class="sidebar-section-title crm-mt-4" >Unified Ecosystem</div>
        <nav class="sidebar-nav crm-mb-2" >
          <a href="../VOSTOKPRIBOR Corporate Web Platform/index.php" class="sidebar-nav-item">
            <div class="sidebar-item-left">
              <span class="sidebar-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="2" y1="12" x2="22" y2="12" />
                  <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                </svg>
              </span>
              <span>Corporate Platform</span>
            </div>
            <span class="sidebar-badge crm-text-xs" >SYS 01</span>
          </a>
          <a href="../Employee Intranet/login.php" class="sidebar-nav-item">
            <div class="sidebar-item-left">
              <span class="sidebar-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="3" width="7" height="7" />
                  <rect x="14" y="3" width="7" height="7" />
                  <rect x="14" y="14" width="7" height="7" />
                  <rect x="3" y="14" width="7" height="7" />
                </svg>
              </span>
              <span>Employee Intranet</span>
            </div>
            <span class="sidebar-badge crm-text-xs" >SYS 04</span>
          </a>
        </nav>
        <!-- Log Out -->

        <div class="sidebar-footer">
          <div class="quota-widget-card">
            <div class="quota-widget-header">
              <span>FY2024 Q4 Quota Target</span>
              <strong class="crm-text-amber" >82%</strong>
            </div>
            <div class="quota-progress-track">
              <div class="quota-progress-bar crm-w-82" ></div>
            </div>
            <div class="quota-widget-footer">
              <span class="quota-val-current">$18.45M</span>
              <span class="quota-val-target">/ $22.50M Target</span>
            </div>
          </div>
        </div>
      </aside>

      <!-- Main Content Area -->
      <main class="content-wrapper">
        <div class="portal-container">
          <!-- Page Header -->
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <span>Enterprise CRM</span>
                <span class="breadcrumb-separator">/</span>
                <span>Sales Operations</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Enterprise Customers Directory</span>
              </div>
              <h1 class="page-title">Enterprise Industrial Client Directory</h1>
              <p class="page-subtitle">Master industrial account records, active service SLAs, and commercial portfolio value</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.crmApp.showToast('Dossier Export', 'All 14 customer dossiers packaged with cryptographic seal.')">
                <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Export Client Matrix</span>
              </button>
              <a href="CustomerDetail.php" class="btn btn-primary-amber">
                <span>View Severstal Profile →</span>
              </a>
            </div>
          </div>

          <!-- KPI Strip -->
          <div class="kpi-grid">
            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Active Accounts</span>
                <div class="kpi-icon-pill indigo"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><line x1="8" y1="6" x2="8.01" y2="6"/><line x1="16" y1="6" x2="16.01" y2="6"/><line x1="12" y1="6" x2="12.01" y2="6"/><line x1="8" y1="10" x2="8.01" y2="10"/><line x1="12" y1="10" x2="12.01" y2="10"/><line x1="16" y1="10" x2="16.01" y2="10"/><line x1="8" y1="14" x2="8.01" y2="14"/><line x1="12" y1="14" x2="12.01" y2="14"/><line x1="16" y1="14" x2="16.01" y2="14"/></svg></div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">14</span>
                <span class="crm-text-muted-12" >Enterprise</span>
              </div>
              <div class="kpi-footer">
                <span>38 Industrial Sites</span>
                <span class="kpi-trend up">100% Active</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Total Portfolio ARR</span>
                <div class="kpi-icon-pill amber"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">$48.2M</span>
                <span class="crm-text-muted-12" >USD</span>
              </div>
              <div class="kpi-footer">
                <span>Avg ARR: <strong>$3.44M</strong></span>
                <span class="kpi-trend up">▲ +16.2% YoY</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Contract Retention</span>
                <div class="kpi-icon-pill success"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">99.4%</span>
                <span class="crm-text-muted-12" >Renewal</span>
              </div>
              <div class="kpi-footer">
                <span>Zero Churn (36M)</span>
                <span class="kpi-trend up">Top Tier</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Open Expansion Opps</span>
                <div class="kpi-icon-pill steel"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">42</span>
                <span class="crm-text-muted-12" >In Pipeline</span>
              </div>
              <div class="kpi-footer">
                <span>Pipeline Value: <strong>$18.45M</strong></span>
                <span class="kpi-trend amber">Active</span>
              </div>
            </div>
          </div>

          <!-- Customer Filter Toolbar -->
          <div class="page-filter-bar">
            <div class="filter-pills-group">
              <button class="filter-pill-btn active" data-sector="all">All Sectors (<?= $custCount ?>)</button>
              <?php foreach ($sectors as $sec): ?>
                <button class="filter-pill-btn" data-sector="<?= htmlspecialchars($sec['sector']) ?>"><?= htmlspecialchars($sec['sector']) ?> (<?= $sec['cnt'] ?>)</button>
              <?php endforeach; ?>
            </div>
            <div class="crm-gap-sm" >
              <select class="filter-select">
                <option>Sort by: Annual Contract Value (High to Low)</option>
                <option>Sort by: Health Score</option>
                <option>Sort by: Account ID</option>
              </select>
            </div>
          </div>

          <!-- Customer Directory Table -->
          <div class="crm-card">
            <table class="accounts-table">
              <thead>
                <tr>
                  <th>Enterprise Account &amp; ID</th>
                  <th>Industrial Sector</th>
                  <th>Key Account Director</th>
                  <th>Annual Contract (ARR)</th>
                  <th>Active MSA Terms</th>
                  <th>Health Score</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="customers-tbody">
                <?php if (empty($customers)): ?>
                  <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--crm-text-muted);">No accounts found in database.</td></tr>
                <?php else: ?>
                  <?php foreach ($customers as $c): ?>
                    <?php
                    $hs = (int)($c['health_score'] ?? 90);
                    $hsClass = $hs >= 92 ? 'crm-mono-bold-success' : ($hs >= 80 ? 'crm-mono-bold-amber' : 'crm-mono-bold-danger');
                    $tierClass = stripos((string)$c['account_tier'], 'strategic') !== false ? 'tier-badge strategic' : 'tier-badge tier-1';
                    ?>
                    <tr class="account-row account-row-tagged" data-cus-id="<?= htmlspecialchars($c['cus_id']) ?>" data-sector="<?= htmlspecialchars($c['sector']) ?>" onclick="window.location.href='CustomerDetail.php?id=<?= urlencode($c['cus_id']) ?>'">
                      <td>
                        <div class="account-name-cell">
                          <span class="account-name-title crm-text-indigo"><?= htmlspecialchars($c['company_name']) ?></span>
                          <span class="account-name-sub">Account ID: #<?= htmlspecialchars($c['cus_id']) ?> · <?= htmlspecialchars($c['headquarters'] ?: $c['sector']) ?></span>
                        </div>
                      </td>
                      <td>
                        <span class="<?= $tierClass ?>"><?= htmlspecialchars($c['sector'] ?: 'Enterprise') ?></span>
                      </td>
                      <td>
                        <strong><?= htmlspecialchars($c['account_manager_name'] ?: 'Dr. Elena Rostova') ?></strong>
                        <div class="crm-text-muted-sm">Key Account Lead</div>
                      </td>
                      <td>
                        <?php if ((float)$c['total_contract_value'] > 0): ?>
                          <strong class="crm-mono-navy-lg">$<?= number_format((float)$c['total_contract_value'], 2) ?></strong>
                          <div class="crm-text-success-11">Active Master Agreement</div>
                        <?php else: ?>
                          <strong class="crm-mono-navy-lg" style="color:var(--crm-text-muted);">$0.00</strong>
                          <div class="crm-text-muted-sm">Pending Procurement RFP</div>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if (!empty($c['active_contract_ref'])): ?>
                          <span class="crm-mono-semibold-11"><?= htmlspecialchars($c['active_contract_ref']) ?></span>
                          <div class="crm-text-muted-10"><?= !empty($c['contract_end']) ? 'Valid thru ' . htmlspecialchars($c['contract_end']) : 'Active SLA Term' ?></div>
                        <?php else: ?>
                          <span class="crm-mono-semibold-11" style="color:var(--crm-text-muted);">MSA In Negotiation</span>
                          <div class="crm-text-muted-10">Standard Enterprise Terms</div>
                        <?php endif; ?>
                      </td>
                      <td>
                        <span class="<?= $hsClass ?>"><?= $hs ?>% (<?= $hs >= 92 ? 'Optimal' : 'Good' ?>)</span>
                      </td>
                      <td style="white-space:nowrap;">
                        <a href="CustomerDetail.php?id=<?= urlencode($c['cus_id']) ?>" class="btn btn-indigo btn-sm" onclick="event.stopPropagation()">Full Profile →</a>
                        <button class="btn btn-outline btn-sm" style="color:#ef4444;border-color:rgba(239,68,68,0.3);margin-left:4px;padding:4px 8px;" title="Delete Account" onclick="event.stopPropagation(); window.crmApp.deleteCustomer('<?= htmlspecialchars(addslashes($c['cus_id'])) ?>')">✕</button>
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
  <link rel="stylesheet" href="../assets/css/api-ui.css">
  <script src="../assets/js/api-core.js"></script>
  <script src="../assets/js/api-crm.js"></script>
  <script src="js/crm-data.js"></script>
  <script src="../assets/js/notifications-hub.js" defer></script>
</body>

</html>