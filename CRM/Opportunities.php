<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('CRM');
$currUser = $_SESSION['vostok_user'] ?? ['full_name' => 'Mikhail Sorokin', 'role_name' => 'VP Enterprise Sales', 'clearance_level' => 'L4'];

$pdo = getDbConnection();
$dbOpps = [];
try {
    $stmt = $pdo->query("
        SELECT 
            o.opp_id,
            o.stage,
            o.estimated_value,
            o.expected_close_date,
            o.opp_title,
            o.probability_percent,
            o.is_confidential,
            c.cus_id,
            c.company_name,
            c.sector,
            e.emp_id AS sales_emp_id,
            e.full_name AS sales_representative
        FROM opportunities o
        LEFT JOIN customers c ON o.cus_id = c.cus_id
        LEFT JOIN employees e ON o.sales_emp_id = e.emp_id
        ORDER BY o.estimated_value DESC
    ");
    $dbOpps = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $dbOpps = [];
}

$oppsByStage = [
    'qualification' => [],
    'proposal' => [],
    'negotiation' => [],
    'contract' => [],
    'won' => []
];
$stageTotals = [
    'qualification' => 0.0,
    'proposal' => 0.0,
    'negotiation' => 0.0,
    'contract' => 0.0,
    'won' => 0.0
];

foreach ($dbOpps as $o) {
    $rawStage = strtolower(trim((string)($o['stage'] ?? '')));
    $matched = 'qualification';
    foreach (array_keys($oppsByStage) as $s) {
        if (str_contains($rawStage, $s)) {
            $matched = $s;
            break;
        }
    }
    $oppsByStage[$matched][] = $o;
    $stageTotals[$matched] += (float)($o['estimated_value'] ?? 0);
}

function formatCrmValue($val) {
    if ($val >= 1000000) {
        return '$' . number_format($val / 1000000, 2) . 'M';
    } elseif ($val >= 1000) {
        return '$' . number_format($val / 1000, 0) . 'K';
    }
    return '$' . number_format($val, 2);
}

function renderKanbanCardPhp($opp) {
    $id = 'OPP-2026-' . str_pad((string)$opp['opp_id'], 4, '0', STR_PAD_LEFT);
    $client = htmlspecialchars((string)($opp['company_name'] ?: ('Enterprise Account ' . ($opp['cus_id'] ?? ''))));
    $title = htmlspecialchars((string)($opp['opp_title'] ?: ($opp['sector'] ? $opp['sector'] . ' Instrumentation' : 'Industrial Automation System')));
    $val = '$' . number_format((float)($opp['estimated_value'] ?? 0), 2);
    $prob = (int)($opp['probability_percent'] ?? 50);
    $close = htmlspecialchars((string)($opp['expected_close_date'] ?? 'Q4 2026'));
    $rep = htmlspecialchars((string)($opp['sales_representative'] ?? 'Pavel Orlov'));
    $isConf = !empty($opp['is_confidential']);
    $confBadge = $isConf ? '<span class="confidential-pill" style="font-size: 9px; padding: 1px 5px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Confid.</span>' : '';

    return '
    <div class="kanban-deal-card" draggable="true" data-id="' . $id . '" onclick="window.crmApp && window.crmApp.inspectOpportunity && window.crmApp.inspectOpportunity(\'' . $id . '\')">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <span class="card-company-name">' . $client . '</span>
        ' . $confBadge . '
      </div>
      <div class="card-deal-title">' . $title . '</div>
      <div style="display: flex; align-items: baseline; justify-content: space-between;">
        <span class="card-value-badge">' . $val . '</span>
        <span style="font-size: 10px; font-family: var(--crm-font-mono); color: var(--crm-indigo); font-weight: 700;">' . $prob . '% Prob.</span>
      </div>
      <div class="card-footer-row">
        <span class="card-close-date">
          <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
          <span>' . $close . '</span>
        </span>
        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=60&auto=format&fit=crop&q=80" alt="' . $rep . '" class="card-rep-avatar" title="Rep: ' . $rep . '" />
      </div>
    </div>';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR CRM · Opportunities Kanban Board</title>
  <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
  <script>
    window.INITIAL_DB_OPPORTUNITIES = <?= json_encode($dbOpps, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
  </script>
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
                <span>DEAL FLOW PIPELINE</span>
              </div>
            </div>
          </a>
        </div>

        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search opportunities (e.g. Blast Furnace, NLMK, Talnakh)..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <div class="top-nav__actions">
          <div class="pipeline-sync-badge">
            <span class="crm-status-success" >●</span>
            <span>Sync: <strong>Active 99.98%</strong></span>
          </div>
          <button class="btn btn-primary-amber btn-sm" onclick="window.crmApp.openModal('modal-new-opportunity')">
            <span>+ New Opportunity</span>
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

            <a href="Customers.php" class="sidebar-nav-item">
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

            <!-- Opportunities (Active) -->
            <a href="Opportunities.php" class="sidebar-nav-item active">
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
                <span class="breadcrumb-current">Opportunities Kanban Board</span>
              </div>
              <h1 class="page-title">Enterprise Commercial Opportunities Kanban</h1>
              <p class="page-subtitle">Drag-and-drop industrial deals through 5 gated conversion stages with live revenue recalculation</p>
            </div>
            <div class="page-header-actions">
              <div class="filter-select crm-inline-flex-sm" >
                <span>👤 Sales Rep: All Directors</span>
              </div>
              <button class="btn btn-outline" onclick="window.crmApp.showToast('Kanban Filtered', 'Displaying all 42 opportunities across 5 gating stages.')">
                <span><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg> Refresh Board</span>
              </button>
              <button class="btn btn-primary-amber" onclick="window.crmApp.openModal('modal-new-opportunity')">
                <span>+ New Opportunity</span>
              </button>
            </div>
          </div>

          <!-- Opportunities 5-Column Kanban Board -->
          <div class="kanban-board-container">
            <!-- Column 1: Qualification -->
            <div class="kanban-column" id="kanban-col-qualification">
              <div class="kanban-col-header col-header-1">
                <span class="kanban-col-title">1. Qualification</span>
                <div class="kanban-col-metrics">
                  <span class="kanban-col-count" id="col-count-qualification"><?= count($oppsByStage['qualification']) ?></span>
                  <span class="crm-font-bold" id="col-val-qualification"><?= formatCrmValue($stageTotals['qualification']) ?></span>
                </div>
              </div>
              <div class="kanban-cards-wrapper" id="kanban-cards-qualification">
                <?php if (empty($oppsByStage['qualification'])): ?>
                  <div class="kanban-empty-part" style="padding: 24px 10px; text-align: center; color: #8892b0; font-size: 12px; font-weight: 500; font-style: italic; border: 1px dashed rgba(255,255,255,0.15); border-radius: 6px; margin: 8px 4px;">( There's no Opportunities in the moment )</div>
                <?php else: ?>
                  <?php foreach ($oppsByStage['qualification'] as $o) echo renderKanbanCardPhp($o); ?>
                <?php endif; ?>
              </div>
            </div>

            <!-- Column 2: Proposal -->
            <div class="kanban-column" id="kanban-col-proposal">
              <div class="kanban-col-header col-header-2">
                <span class="kanban-col-title">2. Proposal / Spec</span>
                <div class="kanban-col-metrics">
                  <span class="kanban-col-count" id="col-count-proposal"><?= count($oppsByStage['proposal']) ?></span>
                  <span class="crm-font-bold" id="col-val-proposal"><?= formatCrmValue($stageTotals['proposal']) ?></span>
                </div>
              </div>
              <div class="kanban-cards-wrapper" id="kanban-cards-proposal">
                <?php if (empty($oppsByStage['proposal'])): ?>
                  <div class="kanban-empty-part" style="padding: 24px 10px; text-align: center; color: #8892b0; font-size: 12px; font-weight: 500; font-style: italic; border: 1px dashed rgba(255,255,255,0.15); border-radius: 6px; margin: 8px 4px;">( There's no Opportunities in the moment )</div>
                <?php else: ?>
                  <?php foreach ($oppsByStage['proposal'] as $o) echo renderKanbanCardPhp($o); ?>
                <?php endif; ?>
              </div>
            </div>

            <!-- Column 3: Negotiation -->
            <div class="kanban-column" id="kanban-col-negotiation">
              <div class="kanban-col-header col-header-3">
                <span class="kanban-col-title">3. Negotiation</span>
                <div class="kanban-col-metrics">
                  <span class="kanban-col-count" id="col-count-negotiation"><?= count($oppsByStage['negotiation']) ?></span>
                  <span class="crm-font-bold" id="col-val-negotiation"><?= formatCrmValue($stageTotals['negotiation']) ?></span>
                </div>
              </div>
              <div class="kanban-cards-wrapper" id="kanban-cards-negotiation">
                <?php if (empty($oppsByStage['negotiation'])): ?>
                  <div class="kanban-empty-part" style="padding: 24px 10px; text-align: center; color: #8892b0; font-size: 12px; font-weight: 500; font-style: italic; border: 1px dashed rgba(255,255,255,0.15); border-radius: 6px; margin: 8px 4px;">( There's no Opportunities in the moment )</div>
                <?php else: ?>
                  <?php foreach ($oppsByStage['negotiation'] as $o) echo renderKanbanCardPhp($o); ?>
                <?php endif; ?>
              </div>
            </div>

            <!-- Column 4: Contract -->
            <div class="kanban-column" id="kanban-col-contract">
              <div class="kanban-col-header col-header-4">
                <span class="kanban-col-title">4. Contract Review</span>
                <div class="kanban-col-metrics">
                  <span class="kanban-col-count" id="col-count-contract"><?= count($oppsByStage['contract']) ?></span>
                  <span class="crm-font-bold" id="col-val-contract"><?= formatCrmValue($stageTotals['contract']) ?></span>
                </div>
              </div>
              <div class="kanban-cards-wrapper" id="kanban-cards-contract">
                <?php if (empty($oppsByStage['contract'])): ?>
                  <div class="kanban-empty-part" style="padding: 24px 10px; text-align: center; color: #8892b0; font-size: 12px; font-weight: 500; font-style: italic; border: 1px dashed rgba(255,255,255,0.15); border-radius: 6px; margin: 8px 4px;">( There's no Opportunities in the moment )</div>
                <?php else: ?>
                  <?php foreach ($oppsByStage['contract'] as $o) echo renderKanbanCardPhp($o); ?>
                <?php endif; ?>
              </div>
            </div>

            <!-- Column 5: Won/Lost -->
            <div class="kanban-column" id="kanban-col-won">
              <div class="kanban-col-header col-header-5">
                <span class="kanban-col-title">5. Won / Finalized</span>
                <div class="kanban-col-metrics">
                  <span class="kanban-col-count" id="col-count-won"><?= count($oppsByStage['won']) ?></span>
                  <span class="crm-font-bold" id="col-val-won"><?= formatCrmValue($stageTotals['won']) ?></span>
                </div>
              </div>
              <div class="kanban-cards-wrapper" id="kanban-cards-won">
                <?php if (empty($oppsByStage['won'])): ?>
                  <div class="kanban-empty-part" style="padding: 24px 10px; text-align: center; color: #8892b0; font-size: 12px; font-weight: 500; font-style: italic; border: 1px dashed rgba(255,255,255,0.15); border-radius: 6px; margin: 8px 4px;">( There's no Opportunities in the moment )</div>
                <?php else: ?>
                  <?php foreach ($oppsByStage['won'] as $o) echo renderKanbanCardPhp($o); ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Modals -->
  <!-- Modal 1: + New Opportunity -->
  <div id="modal-new-opportunity" class="modal-backdrop">
    <div class="modal-card">
      <div class="modal-header">
        <h3 class="modal-title">+ Log New Industrial Opportunity</h3>
        <button class="icon-button" onclick="window.crmApp.closeModal('modal-new-opportunity')">✕</button>
      </div>
      <form id="form-new-opportunity">
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label" for="new-opp-title">Opportunity / Equipment Scope Title</label>
            <input type="text" id="new-opp-title" class="form-input" placeholder="e.g. Blast Furnace Gas Skid Automation Phase 2" required />
          </div>

          <div class="crm-grid-2col" >
            <div class="form-group">
              <label class="form-label" for="new-opp-client">Client Enterprise Account</label>
              <select id="new-opp-client" class="form-select" required>
                <option value="Severstal Metallurgy PJSC">Severstal Metallurgy PJSC</option>
                <option value="NLMK Group Lipetsk">NLMK Group Lipetsk</option>
                <option value="Norilsk Nickel Mining">Norilsk Nickel Mining</option>
                <option value="EVRAZ Consolidated">EVRAZ Consolidated</option>
                <option value="PhosAgro Chemical">PhosAgro Chemical</option>
                <option value="Gazprom Neft Omsk">Gazprom Neft Omsk</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="new-opp-value">Deal Value (USD)</label>
              <input type="number" id="new-opp-value" class="form-input" placeholder="1850000" required />
            </div>
          </div>

          <div class="crm-grid-2col" >
            <div class="form-group">
              <label class="form-label" for="new-opp-stage">Initial Pipeline Stage</label>
              <select id="new-opp-stage" class="form-select">
                <option value="qualification">1. Qualification</option>
                <option value="proposal">2. Proposal / Spec</option>
                <option value="negotiation" selected>3. Negotiation</option>
                <option value="contract">4. Contract Gating</option>
                <option value="won">5. Won / Finalized</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="new-opp-date">Target Close Date</label>
              <input type="text" id="new-opp-date" class="form-input" placeholder="Dec 30, 2024" value="Dec 30, 2024" />
            </div>
          </div>

          <div class="form-group">
            <label class="crm-clickable-pill" >
              <input type="checkbox" id="new-opp-confidential" checked />
              <span>Apply Alert-Red <strong class="crm-text-confidential" >"Highly Confidential"</strong> Commercial Data Tag</span>
            </label>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline" onclick="window.crmApp.closeModal('modal-new-opportunity')">Cancel</button>
          <button type="submit" class="btn btn-primary-amber">Create Opportunity</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal 2: Opportunity Inspector Drawer -->
  <div id="modal-inspect-opportunity" class="modal-backdrop">
    <div class="modal-card">
      <div class="modal-header">
        <h3 class="modal-title" id="inspect-opp-title">Opportunity Details</h3>
        <button class="icon-button" onclick="window.crmApp.closeModal('modal-inspect-opportunity')">✕</button>
      </div>
      <div class="modal-body">
        <div class="crm-panel-customer" >
          <div>
            <div class="crm-caption-muted" >Enterprise Client</div>
            <div class="crm-heading-15"  id="inspect-opp-client">Client Name</div>
          </div>
          <div class="crm-text-right" >
            <div class="crm-caption-muted" >Contract Value</div>
            <div class="crm-mono-stat-lg"  id="inspect-opp-value">$0.00</div>
          </div>
        </div>

        <div class="crm-grid-2col" >
          <div class="form-group">
            <label class="form-label">Current Gating Stage</label>
            <select id="inspect-opp-stage" class="form-select">
              <option value="qualification">1. Qualification</option>
              <option value="proposal">2. Proposal / Spec</option>
              <option value="negotiation">3. Negotiation</option>
              <option value="contract">4. Contract Gating</option>
              <option value="won">5. Won / Finalized</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Expected Close Date</label>
            <div class="crm-pill-dim-mono" id="inspect-opp-date" >-</div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Assigned Sales Engineer / Rep</label>
          <div class="crm-pill-dim-text" id="inspect-opp-rep" >-</div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" onclick="window.crmApp.closeModal('modal-inspect-opportunity')">Close</button>
        <button class="btn btn-indigo" onclick="window.crmApp.showToast('Opportunity Saved', 'Commercial parameters synchronized.'); window.crmApp.closeModal('modal-inspect-opportunity');">Save Changes</button>
      </div>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js?v=<?= time() ?>"></script>
  <link rel="stylesheet" href="../assets/css/api-ui.css">
  <script src="../assets/js/api-core.js"></script>
  <script src="../assets/js/api-crm.js"></script>
  <script src="js/crm-data.js?v=<?= time() ?>"></script>
  <script src="../assets/js/notifications-hub.js" defer></script>
</body>

</html>