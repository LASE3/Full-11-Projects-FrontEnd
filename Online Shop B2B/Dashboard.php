<?php
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/shop_service.php';
requireAuth('SHP', 'login.php');

$_SESSION['vostok_system_SHP'] = true;
$_SESSION['vostok_current_system'] = 'SHP';

$currUser = $_SESSION['vostok_user'] ?? [];
$isSuperAdmin = isSuperAdmin($currUser);
$isLogistics = false;
$userRole = (string)($currUser['role_name'] ?? '');
if (stripos($userRole, 'Logistics') !== false || stripos($userRole, 'Supply Chain') !== false) {
    $isLogistics = true;
}
$canManageProducts = ($isSuperAdmin || $isLogistics);
$csrfToken = getCsrfToken();

$allCustomers = [];
try {
    $stmtAllC = getDbConnection()->query("SELECT cus_id, company_name, sector, headquarters, account_tier, account_manager_emp_id FROM customers ORDER BY cus_id ASC");
    $allCustomers = $stmtAllC->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {}

$cusId = shop_getCurrentCustomerId();
if (!$cusId && !empty($allCustomers)) {
    $cusId = $allCustomers[0]['cus_id'];
    $_SESSION['cus_id'] = $cusId;
}

$customer = null;
if ($cusId) {
    foreach ($allCustomers as $c) {
        if ($c['cus_id'] === $cusId) {
            $customer = $c;
            break;
        }
    }
    if (!$customer) {
        $stmtC = getDbConnection()->prepare("SELECT * FROM customers WHERE cus_id = ?");
        $stmtC->execute([$cusId]);
        $customer = $stmtC->fetch(PDO::FETCH_ASSOC);
    }
}
if (!$customer && !empty($allCustomers)) {
    $customer = $allCustomers[0];
    $cusId = $customer['cus_id'];
}

$kpis = shop_getDashboardMetrics($cusId);
$products = shop_getProducts($cusId);
$orders = shop_getOrders($cusId);
$projects = [];
if ($cusId) {
    $stmtP = getDbConnection()->prepare("SELECT prj_id, project_name, budget FROM projects WHERE cus_id = ? ORDER BY prj_id ASC");
    $stmtP->execute([$cusId]);
    $projects = $stmtP->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken) ?>">
  <title>VOSTOKPRIBOR | B2B Industrial E-Commerce Platform (shop.vostokpribor.local)</title>


  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

  <!-- Consolidated Stylesheet (Strict System Tokens & UI Components) -->
  <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
</head>

<body>

  <!-- ========================================================================
       TOP NAVIGATION BAR (#0F2438 Deep Navy + #0E7C86 4px Teal Accent Stripe)
       ======================================================================== -->
  <header class="top-nav">
    <!-- 4px System Identity Stripe -->
    <div class="top-nav__accent-stripe"></div>

    <div class="top-nav__content">
      <!-- Brand & System Identifier -->
      <div class="brand-section">
        <button class="mobile-nav-toggle" id="b2b-sidebar-toggle" onclick="document.body.classList.toggle('sidebar-open')" title="Toggle Sidebar Navigation" style="background:transparent;border:none;color:#fff;cursor:pointer;padding:6px;display:none;align-items:center;justify-content:center;border-radius:4px;margin-right:8px;">
          <span class="material-symbols-outlined" style="font-size:22px;">menu</span>
        </button>
        <div class="brand-logo-container" onclick="window.shopApp.navigateTo('catalog')">
          <img alt="VOSTOKPRIBOR Official Mark" class="brand-logo-img"
            src="assets/logo.svg" />
          <div class="brand-divider"></div>
          <div class="brand-title-group">
            <div class="brand-title-row">
              <span class="brand-name">VOSTOKPRIBOR</span>
              <span class="system-tag">SHOP · SYS 02</span>
            </div>
            <div class="brand-subline">
              <span class="status-dot-pulse"></span>
              <span>shop.vostokpribor.local</span>
              <span class="shop-divider-v">|</span>
              <span>ENTERPRISE B2B</span>
            </div>
          </div>
        </div>
      </div>

      <!-- (Navigation links moved to left sidebar) -->

      <!-- Right Header Actions (Customer Context, Cart, RFQ Button) -->
      <div class="header-actions">
        <!-- Authenticated User Profile Badge -->
        <div class="user-profile-badge" style="display:flex;align-items:center;gap:10px;padding:4px 10px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);border-radius:6px;" title="Current Authenticated User">
          <div style="text-align:right;">
            <div style="font-size:12px;font-weight:600;color:#fff;line-height:1.2;"><?= htmlspecialchars((string)($currUser['full_name'] ?? 'SuperAdmin')) ?></div>
            <div style="font-size:10px;font-family:'JetBrains Mono',monospace;color:#F59E0B;line-height:1.2;">
              <?= htmlspecialchars((string)($currUser['emp_id'] ?? ($currUser['user_id'] ?? 'EMP-0001'))) ?> • <?= htmlspecialchars((string)($currUser['role_name'] ?? 'SuperAdmin')) ?>
            </div>
          </div>
          <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#00E5FF22,#00E5FF44);border:1px solid #00E5FF66;display:flex;align-items:center;justify-content:center;color:#00E5FF;">
            <span class="material-symbols-outlined" style="font-size:18px;">person</span>
          </div>
        </div>

        <!-- Account / Customer Context Switcher -->
        <div class="customer-context-selector" onclick="window.shopApp && window.shopApp.toggleCustomerDropdown ? window.shopApp.toggleCustomerDropdown(event) : document.getElementById('customer-dropdown-menu').classList.toggle('show')" title="Active Enterprise Customer Persona">
          <span style="font-size:10px;font-weight:700;letter-spacing:0.04em;text-transform:uppercase;color:#F59E0B;background:rgba(245,158,11,0.15);padding:2px 6px;border-radius:3px;margin-right:2px;">CLIENT</span>
          <div class="customer-avatar" id="header-customer-avatar"><?= htmlspecialchars(strtoupper(substr($customer['company_name'] ?? 'T', 0, 1))) ?></div>
          <div class="customer-info">
            <span class="customer-code" id="header-customer-code"><?= htmlspecialchars($customer['cus_id'] ?? ($cusId ?: 'CUS-1005')) ?></span>
            <span class="customer-name" id="header-customer-name"><?= htmlspecialchars($customer['company_name'] ?? 'Tashkent Precision Controls') ?></span>
          </div>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            class="shop-text-muted-dark">
            <polyline points="6 9 12 15 18 9" />
          </svg>

          <!-- Dropdown Account Switcher Menu -->
          <div class="customer-dropdown-menu" id="customer-dropdown-menu" onclick="event.stopPropagation()">
            <div class="dropdown-header-label">
              <div style="display:flex;align-items:center;gap:6px;">
                <span class="material-symbols-outlined" style="font-size:16px;color:#00E5FF;">switch_account</span>
                <span>Switch Enterprise Account</span>
              </div>
              <span style="font-family:'JetBrains Mono',monospace;font-size:10px;background:rgba(0,229,255,0.15);color:#00E5FF;padding:2px 6px;border-radius:10px;">
                <?= count($allCustomers) ?> Accounts
              </span>
            </div>
            <div style="padding:8px 12px;border-bottom:1px solid rgba(255,255,255,0.08);background:#091827;">
              <input type="text" id="customer-dropdown-search" placeholder="Search account by name or code..."
                oninput="filterCustomerDropdownList(this.value)"
                onclick="event.stopPropagation()"
                style="width:100%;padding:6px 10px;background:#0d1e2e;border:1px solid #1b3a5c;border-radius:4px;color:#fff;font-size:12px;outline:none;" />
            </div>
            <div id="customer-dropdown-items" style="max-height:340px;overflow-y:auto;">
              <?php foreach ($allCustomers as $c): 
                $isSelected = ($c['cus_id'] === $cusId);
                $initial = strtoupper(substr($c['company_name'] ?? 'C', 0, 1));
              ?>
              <div class="customer-option-item <?= $isSelected ? 'selected' : '' ?>" 
                   data-code="<?= htmlspecialchars($c['cus_id']) ?>"
                   data-name="<?= htmlspecialchars(strtolower($c['company_name'])) ?>"
                   onclick="window.selectEnterpriseAccount('<?= htmlspecialchars($c['cus_id']) ?>', event)">
                <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;">
                  <div style="width:28px;height:28px;border-radius:50%;background:<?= $isSelected ? 'rgba(0,229,255,0.2)' : 'rgba(255,255,255,0.06)' ?>;border:1px solid <?= $isSelected ? '#00E5FF' : 'rgba(255,255,255,0.12)' ?>;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:<?= $isSelected ? '#00E5FF' : '#94a3b8' ?>;flex-shrink:0;">
                    <?= htmlspecialchars($initial) ?>
                  </div>
                  <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:6px;">
                      <span style="font-family:'JetBrains Mono',monospace;color:#00E5FF;font-size:11px;font-weight:600;"><?= htmlspecialchars($c['cus_id']) ?></span>
                      <span style="color:#ffffff;font-size:12.5px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($c['company_name']) ?></span>
                    </div>
                    <div style="font-size:11px;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                      <?= htmlspecialchars($c['sector'] ?? 'Industrial Equipment') ?> · <?= htmlspecialchars($c['headquarters'] ?? 'Kazakhstan') ?>
                    </div>
                  </div>
                </div>
                <?php if ($isSelected): ?>
                  <span style="color:#00E5FF;font-size:10px;font-weight:700;background:rgba(0,229,255,0.15);padding:2px 6px;border-radius:4px;margin-left:8px;flex-shrink:0;">ACTIVE</span>
                <?php endif; ?>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Direct Cart Icon with Amber Item-Count Badge -->
        <div class="cart-button" onclick="window.shopApp.openCartDrawer()" title="Procurement Cart">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
          </svg>
          <span class="cart-count-badge" id="header-cart-badge">0</span>
        </div>

        <!-- Live Enterprise Notifications Bell -->
        <button class="icon-button notifications-btn" id="notifications-toggle-btn" title="Live B2B Notifications & Telemetry" style="background:transparent;border:none;color:#bdc6cf;cursor:pointer;padding:6px;display:flex;align-items:center;justify-content:center;position:relative;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
          </svg>
        </button>

        <!-- Quick RFQ CTA Button -->
        <button class="rfq-quick-btn" onclick="window.shopApp.openRfqDrawer()">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M12 5v14M5 12h14" />
          </svg>
          <span>RFQ Builder</span>
        </button>
      </div>

      <!-- Top Bar Sign Out -->
      <a href="./api/logout.php?redirect=../login.php" class="top-signout-btn" title="Sign Out of Online Shop B2B" onclick="(function(){sessionStorage.clear();localStorage.clear();})()"><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
    </div>
  </header>

  <!-- ========================================================================
       LEFT SIDEBAR NAVIGATION (Hover-to-Open & Hover-to-Close)
       ======================================================================== -->
  <aside class="sidebar b2b-sidebar" id="b2b-sidebar" style="background-color: #0f2438 !important; border-right: 1px solid rgba(255, 255, 255, 0.1) !important;">
    <div class="sidebar-section-title">E-Commerce Navigation</div>
    <nav class="sidebar-nav">
      <a class="sidebar-nav-item active" data-screen="catalog" onclick="window.shopApp.navigateTo('catalog')">
        <div class="sidebar-item-left">
          <span class="sidebar-icon material-symbols-outlined">grid_view</span>
          <span class="sidebar-label">Product Catalog</span>
        </div>
      </a>

      <a class="sidebar-nav-item" data-screen="quotes" onclick="window.shopApp.openRfqDrawer()">
        <div class="sidebar-item-left">
          <span class="sidebar-icon material-symbols-outlined">request_quote</span>
          <span class="sidebar-label">My Quotes</span>
        </div>
        <span class="sidebar-badge" id="nav-quotes-count">0</span>
      </a>

      <a class="sidebar-nav-item" data-screen="tracking" onclick="window.shopApp.navigateTo('tracking')">
        <div class="sidebar-item-left">
          <span class="sidebar-icon material-symbols-outlined">local_shipping</span>
          <span class="sidebar-label">Order Tracking</span>
        </div>
      </a>

      <a class="sidebar-nav-item" data-screen="orders" onclick="window.shopApp.navigateTo('tracking')">
        <div class="sidebar-item-left">
          <span class="sidebar-icon material-symbols-outlined">receipt_long</span>
          <span class="sidebar-label">Orders &amp; Invoices</span>
        </div>
      </a>

      <a class="sidebar-nav-item" href="Integrations.php">
        <div class="sidebar-item-left">
          <span class="sidebar-icon material-symbols-outlined" style="color:#00E5FF;">hub</span>
          <span class="sidebar-label" style="color:#00E5FF;">Integrations</span>
        </div>
        <span class="sidebar-badge" style="background:rgba(0,229,255,0.2); color:#00E5FF;">SYS10</span>
      </a>

      <?php if ($canManageProducts): ?>
      <a class="sidebar-nav-item" data-screen="admin-products" onclick="window.shopApp.navigateTo('admin-products'); loadAdminProducts();" style="cursor:pointer;">
        <div class="sidebar-item-left">
          <span class="sidebar-icon material-symbols-outlined" style="color:#F59E0B;">inventory_2</span>
          <span class="sidebar-label" style="color:#F59E0B; font-weight:600;">Product Admin</span>
        </div>
        <span class="sidebar-badge" style="background:rgba(245,158,11,0.2); color:#F59E0B;">L4</span>
      </a>
      <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
      <div class="b2b-account-badge" style="background:rgba(255,255,255,0.05); padding:10px; border-radius:8px; border:1px solid rgba(255,255,255,0.1);">
        <div style="font-size:10px; font-family:'JetBrains Mono',monospace; color:#94a3b8; text-transform:uppercase;">Account Mode</div>
        <div style="font-size:12px; font-weight:600; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" id="sidebar-customer-name"><?= htmlspecialchars($customer['company_name'] ?? ($_SESSION['vostok_user']['full_name'] ?? 'Enterprise Account')) ?></div>
      </div>
    </div>
  </aside>

  <!-- ========================================================================
       SUB-HEADER BAR: Breadcrumbs & Telemetry Baseline Status
       ======================================================================== -->
  <div class="sub-header-bar">
    <div class="sub-header-content">
      <div class="breadcrumbs" id="app-breadcrumbs">
        <span class="breadcrumb-item active">Industrial B2B Catalog</span>
      </div>

      <div class="enterprise-quick-status">
        <div class="telemetry-tag">
          <span>Telemetry Node:</span>
          <strong class="shop-status-nominal">Active (99.98%)</strong>
        </div>
        <div class="telemetry-tag">
          <span>Account Manager:</span>
          <strong><?= htmlspecialchars(!empty($customer['account_manager_emp_id']) ? $customer['account_manager_emp_id'] : 'Dedicated Account Specialist') ?></strong>
        </div>
        <div class="telemetry-tag">
          <span>Security Classification:</span>
          <strong class="shop-confidential-label">L1 Public / L3 Pricing</strong>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================
       MAIN APPLICATION VIEW CONTAINER
       ======================================================================== -->
  <main class="app-container">

    <?php if ($canManageProducts): ?>
    <!-- ======================================================================
         SCREEN: PRODUCT ADMINISTRATION (SuperAdmin & Logistics Only)
         ====================================================================== -->
    <section class="screen-view" id="view-admin-products" style="padding: 24px; max-width: 1400px; margin: 0 auto; display:none;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:16px;">
        <div>
          <div style="display:flex; align-items:center; gap:10px;">
            <span class="material-symbols-outlined" style="color:#F59E0B; font-size:28px;">inventory_2</span>
            <h1 style="font-size:22px; font-weight:700; color:#fff; margin:0;">Product Catalog Administration</h1>
          </div>
          <p style="color:#94a3b8; font-size:13px; margin-top:4px;">Manage B2B industrial products, prices, stock levels, and upload technical drawings/photos.</p>
        </div>
        <div style="display:flex; gap:10px;">
          <button class="btn-primary-amber" onclick="openAddProductModal()" style="display:flex; align-items:center; gap:8px; padding:10px 16px; border-radius:6px; font-weight:600; cursor:pointer;">
            <span class="material-symbols-outlined" style="font-size:18px;">add_box</span>
            <span>Add Product</span>
          </button>
          <button onclick="loadAdminProducts()" style="background:#1e293b; color:#cbd5e1; border:1px solid #334155; padding:10px 14px; border-radius:6px; cursor:pointer; display:flex; align-items:center; gap:6px;">
            <span class="material-symbols-outlined" style="font-size:18px;">refresh</span>
            <span>Refresh</span>
          </button>
        </div>
      </div>

      <div style="background:#0F2438; border:1px solid #1B3A5C; border-radius:8px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.3);">
        <table style="width:100%; border-collapse:collapse; text-align:left; font-size:13px;" id="admin-products-table">
          <thead>
            <tr style="background:#142c44; color:#94a3b8; border-bottom:1px solid #1B3A5C; font-family:'JetBrains Mono',monospace; font-size:11px; text-transform:uppercase;">
              <th style="padding:12px 16px;">ID</th>
              <th style="padding:12px 16px;">Image</th>
              <th style="padding:12px 16px;">Product Name & Description</th>
              <th style="padding:12px 16px;">Billing Model</th>
              <th style="padding:12px 16px;">Price (€)</th>
              <th style="padding:12px 16px;">Stock</th>
              <th style="padding:12px 16px;">Status</th>
              <th style="padding:12px 16px; text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody id="admin-products-tbody">
            <tr><td colspan="8" style="padding:24px; text-align:center; color:#94a3b8;">Loading product catalog...</td></tr>
          </tbody>
        </table>
      </div>
    </section>
    <?php endif; ?>

    <!-- ======================================================================
         SCREEN 1: PRODUCT CATALOG
         ====================================================================== -->
    <section class="screen-view active" id="view-catalog">
      <div class="catalog-layout">

        <!-- Left Filter Sidebar -->
        <aside class="catalog-sidebar">
          <div class="sidebar-header">
            <div class="sidebar-title">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
              </svg>
              <span>Catalog Filters</span>
            </div>
            <button class="reset-filters-btn" onclick="window.shopApp.resetAllFilters()">Reset All</button>
          </div>

          <!-- Category Filter -->
          <div class="filter-group">
            <div class="filter-title">
              <span>Category</span>
            </div>
            <div class="filter-option-list">
              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="category"
                  data-filter-value="Optical Sensors">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Optical Sensors</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="category"
                  data-filter-value="Measurement Kits">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Measurement Kits</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="category"
                  data-filter-value="PLC Integration">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">PLC Integration</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="category"
                  data-filter-value="Monitoring Gateways">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Monitoring Gateways</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="category"
                  data-filter-value="Calibration Stations">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Calibration Stations</span>
              </label>
            </div>
          </div>

          <!-- Industry Sector Filter -->
          <div class="filter-group">
            <div class="filter-title">
              <span>Target Sector</span>
            </div>
            <div class="filter-option-list">
              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="sector"
                  data-filter-value="Geomatics & GIS">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Geomatics & GIS</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="sector"
                  data-filter-value="Process Automation">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Process Automation</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="sector"
                  data-filter-value="Mining">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Mining</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="sector"
                  data-filter-value="Industrial Metrology">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Industrial Metrology</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="sector"
                  data-filter-value="Robotics">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Robotics</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="sector"
                  data-filter-value="Water Infrastructure">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Water Infrastructure</span>
              </label>
            </div>
          </div>

          <!-- Availability Filter -->
          <div class="filter-group">
            <div class="filter-title">
              <span>Availability</span>
            </div>
            <div class="filter-option-list">
              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="availability"
                  data-filter-value="in-stock">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">In Stock (Dispatches 24h)</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="availability"
                  data-filter-value="lead-time">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Lead Time &lt; 3 Wks</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="availability"
                  data-filter-value="custom">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Custom Engineering Order</span>
              </label>
            </div>
          </div>

          <!-- Billing Model Filter -->
          <div class="filter-group">
            <div class="filter-title">
              <span>Billing Model</span>
            </div>
            <div class="filter-option-list">
              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="billing"
                  data-filter-value="Per Unit">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Per Unit</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="billing"
                  data-filter-value="Per Project">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Per Project</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="billing"
                  data-filter-value="Subscription">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Subscription</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="billing"
                  data-filter-value="Annual Contract">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Annual Contract</span>
              </label>
            </div>
          </div>

          <!-- Price Range Filter -->
          <div class="filter-group">
            <div class="filter-title">
              <span>Price Range Ceiling</span>
            </div>
            <div class="price-slider-container">
              <div class="price-inputs-row">
                <div class="price-input-box">
                  <span>Max €</span>
                  <input type="number" id="price-max-display" value="50000" min="1000" max="50000" step="1000"
                    onchange="window.shopApp.setPriceMax(this.value)">
                </div>
              </div>
              <input type="range" id="price-slider-input" class="range-slider" min="2000" max="50000" step="1000"
                value="50000" oninput="window.shopApp.setPriceMax(this.value)">
            </div>
          </div>

          <!-- Log Out -->

        </aside>

        <!-- Main Product Catalog Area -->
        <section class="catalog-main">
          <!-- Toolbar (Search, Sort, Layout Switcher) -->
          <div class="catalog-toolbar">
            <div class="toolbar-search-box">
              <svg class="shop-text-tertiary" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
              </svg>
              <input type="text" id="catalog-search-input"
                placeholder="Search by SKU, sensor type, protocol (e.g. Modbus, 4K)..."
                oninput="window.shopApp.setSearchTerm(this.value)">
            </div>

            <div class="toolbar-meta">
              <span class="results-count" id="catalog-results-count">—</span>

              <div class="sort-group">
                <label for="sort-select-input">Sort:</label>
                <select id="sort-select-input" class="sort-select" onchange="window.shopApp.setSortBy(this.value)">
                  <option value="default">Baseline Master Order</option>
                  <option value="sku">Part ID (PROD-1001 to 1010)</option>
                  <option value="price-asc">Price: Low to High</option>
                  <option value="price-desc">Price: High to Low</option>
                  <option value="availability">Availability & Stock</option>
                </select>
              </div>

              <div class="view-mode-toggle">
                <button class="view-btn active" data-mode="grid" onclick="window.shopApp.setViewMode('grid')"
                  title="Grid View">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                  </svg>
                </button>
                <button class="view-btn" data-mode="list" onclick="window.shopApp.setViewMode('list')"
                  title="List View">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="8" y1="6" x2="21" y2="6" />
                    <line x1="8" y1="12" x2="21" y2="12" />
                    <line x1="8" y1="18" x2="21" y2="18" />
                    <line x1="3" y1="6" x2="3.01" y2="6" />
                    <line x1="3" y1="12" x2="3.01" y2="12" />
                    <line x1="3" y1="18" x2="3.01" y2="18" />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Active Filter Chips Bar -->
          <div class="active-filters-bar" id="catalog-active-filters"></div>

          <!-- Product Grid Container -->
          <div class="product-grid" id="catalog-products-grid">
            <!-- Dynamically populated by app.js -->
          </div>
        </section>

      </div>
    </section>

    <!-- ======================================================================
         SCREEN 2: PRODUCT DETAIL PAGE (PDP)
         ====================================================================== -->
    <section class="screen-view" id="view-product-detail">
      <div id="pdp-dynamic-content">
        <!-- Dynamically rendered by app.js -->
      </div>
    </section>

    <!-- ======================================================================
         SCREEN 3: ORDER TRACKING DASHBOARD
         ====================================================================== -->
    <section class="screen-view" id="view-tracking">
      <div class="dashboard-layout">

        <!-- Top Horizontal Status Timeline Component -->
        <div id="tracking-timeline-container">
          <!-- Dynamically populated by app.js -->
        </div>

        <!-- KPI Metric Cards -->
        <div class="dashboard-kpi-grid">
          <div class="kpi-card">
            <span class="kpi-title">Active Platform Orders</span>
            <div class="kpi-value" id="shop-kpi-orders">—</div>
            <span class="kpi-trend positive" id="shop-kpi-orders-sub">
              Live orders
            </span>
          </div>

          <div class="kpi-card">
            <span class="kpi-title">Total Pipeline Value</span>
            <div class="kpi-value" id="shop-kpi-value">—</div>
            <span class="kpi-trend neutral" id="shop-kpi-value-sub">
              <span>Platform orders</span>
            </span>
          </div>

          <div class="kpi-card">
            <span class="kpi-title">In-Transit Freight</span>
            <div class="kpi-value" id="shop-kpi-shipments">—</div>
            <span class="kpi-trend positive" id="shop-kpi-shipments-sub">
              <span>Shipments</span>
            </span>
          </div>

          <div class="kpi-card">
            <span class="kpi-title">Pending Quotes (RFQ)</span>
            <div class="kpi-value" id="shop-kpi-rfqs">—</div>
            <span class="kpi-trend neutral" id="shop-kpi-rfqs-sub">
              <span>Commercial RFQs</span>
            </span>
          </div>
        </div>

        <!-- Confidential Orders Data Table with Amber-Orange Left-Border Tag -->
        <div class="orders-table-card">
          <div class="orders-table-header">
            <div class="table-header-left">
              <h2 class="table-title">Industrial Purchase Orders & Procurement Register</h2>
              <div class="confidential-notice-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                  <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                <span>CONFIDENTIAL BUSINESS DATA · LEVEL L3 RESTRICTED</span>
              </div>
            </div>

            <div>
              <input type="text" class="table-search-input" placeholder="Filter orders by ID, Client, Project..."
                oninput="window.shopApp.filterOrdersTable(this.value)">
            </div>
          </div>

          <div class="table-responsive-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Order ID / Invoice</th>
                  <th>Project Code</th>
                  <th>Customer Enterprise</th>
                  <th>Equipment Package</th>
                  <th>Order Value (€)</th>
                  <th>Payment / Stage</th>
                  <th>Delivery ETA</th>
                  <th class="shop-text-right">Action</th>
                </tr>
              </thead>
              <tbody id="tracking-orders-table-body">
                <!-- Rows dynamically rendered with .confidential-row (#D9822B amber-orange left border) -->
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </section>

  </main>

  <!-- ========================================================================
       MODALS & SLIDE-IN DRAWERS (RFQ Builder, Cart Drawer)
       ======================================================================== -->
  <div class="modal-overlay" id="modal-overlay" onclick="window.shopApp.closeAllModals()"></div>

  <!-- RFQ / Quotation Package Drawer -->
  <div class="drawer-container" id="rfq-drawer">
    <div class="drawer-header">
      <div class="drawer-title-group">
        <span class="drawer-title">Request for Quotation (RFQ)</span>
        <span class="drawer-subtitle">Direct Transmit to CRM (System 05) & Sales Department</span>
      </div>
      <button class="drawer-close-btn" onclick="window.shopApp.closeAllModals()">✕</button>
    </div>

    <div class="drawer-body">
      <div
        class="shop-modal-panel-inset">
        <div><strong>Corporate Account:</strong> <span id="rfq-client-name">Tashkent Precision Controls
            (CUS-1005)</span></div>
        <div><strong>Assigned Sales Mgr:</strong> EMP-1008 (Bekzod Rakhimov)</div>
      </div>

      <div
        class="shop-modal-section-title">
        Staged Equipment Packages
      </div>

      <div class="drawer-items-list" id="rfq-items-list">
        <!-- Dynamically populated -->
      </div>

      <div class="shop-form-group">
        <label class="shop-form-label">Associate with Project
          (Optional):</label>
        <select
          class="shop-form-select">
          <option value="PRJ-2026-005">PRJ-2026-005: Precision Automation Upgrade (€128,000)</option>
          <option value="PRJ-2026-013">PRJ-2026-013: Contract Review Staging (€112,000)</option>
          <option value="NEW">Create New Project Statement of Work</option>
        </select>
      </div>

      <div class="shop-form-group">
        <label class="shop-form-label">Target Delivery Deadline:</label>
        <input type="date" value="2026-10-15"
          class="shop-form-input">
      </div>
    </div>

    <div class="drawer-footer">
      <div class="shop-summary-row">
        <span class="shop-summary-label">Estimated Indicative
          Value:</span>
        <span class="price-unit-large shop-price-rfq-total" id="rfq-estimated-total">€53,000</span>
      </div>

      <button class="btn-primary-amber shop-btn-modal-action" onclick="window.shopApp.submitOfficialRfq()">
        Transmit RFQ to Commercial Sales (SAL)
      </button>
    </div>
  </div>

  <!-- Direct Procurement Cart Drawer -->
  <div class="drawer-container" id="cart-drawer">
    <div class="drawer-header">
      <div class="drawer-title-group">
        <span class="drawer-title">Direct Procurement Cart</span>
        <span class="drawer-subtitle">Integrated Purchase Order & Billing Dispatch</span>
      </div>
      <button class="drawer-close-btn" onclick="window.shopApp.closeAllModals()">✕</button>
    </div>

    <div class="drawer-body">
      <div
        class="shop-modal-section-title">
        Procurement Line Items
      </div>

      <div class="drawer-items-list" id="cart-items-list">
        <!-- Dynamically populated -->
      </div>

      <div
        class="shop-form-group-divided">
        <label class="shop-form-label">Corporate PO Number
          (Required):</label>
        <input type="text" id="po-number-input" value="PO-TPC-2026-8801"
          class="shop-form-input-mono">
      </div>

      <div class="shop-form-group">
        <label class="shop-form-label">Payment Terms:</label>
        <select
          class="shop-form-select">
          <option value="net30">Net 30 Days (Pre-Approved Tier A Credit)</option>
          <option value="net60">Net 60 Days (Letter of Credit Required)</option>
          <option value="advance">100% Advance Wire (Immediate Dispatch Priority)</option>
        </select>
      </div>
    </div>

    <div class="drawer-footer">
      <div class="shop-summary-row">
        <span class="shop-summary-label">Net Subtotal (excl. VAT):</span>
        <span class="price-value shop-price-cart-subtotal" id="cart-subtotal">€0.00</span>
      </div>

      <div
        class="shop-summary-row-total">
        <span class="shop-summary-label-total">Total Purchase Commitment:</span>
        <span class="price-unit-large shop-price-cart-total" id="cart-total">€0.00</span>
      </div>

      <button class="btn-primary-amber shop-btn-modal-action" onclick="window.shopApp.submitDirectPO()">
        Confirm Purchase Order (PO) & Generate Invoice
      </button>
    </div>
  </div>

  <!-- Toast Container -->
  <div class="toast-container" id="toast-container"></div>

  <?php if ($canManageProducts): ?>
  <!-- ========================================================================
       PRODUCT CATALOG ADMIN MODALS (SuperAdmin & Logistics)
       ======================================================================== -->
  <!-- 1. Add / Edit Product Modal -->
  <div id="admin-product-modal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:10050; width:90%; max-width:540px; background:#0F2438; border:1px solid #1B3A5C; border-radius:10px; box-shadow:0 20px 50px rgba(0,0,0,0.7); color:#e2e8f0; font-family:'IBM Plex Sans', sans-serif;">
    <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-bottom:1px solid #1B3A5C; background:#142c44;">
      <h3 id="admin-prod-modal-title" style="margin:0; font-size:16px; font-weight:700; color:#fff; display:flex; align-items:center; gap:8px;">
        <span class="material-symbols-outlined" style="color:#F59E0B; font-size:20px;">inventory_2</span>
        <span>Add Industrial Product</span>
      </h3>
      <button onclick="closeAllAdminModals()" style="background:none; border:none; color:#94a3b8; font-size:18px; cursor:pointer;">✕</button>
    </div>
    <form id="admin-prod-form" onsubmit="submitProductForm(event)" style="padding:20px;">
      <input type="hidden" id="admin-prod-id" value="">
      <div style="margin-bottom:14px;">
        <label style="display:block; font-size:12px; font-weight:600; color:#94a3b8; margin-bottom:6px; text-transform:uppercase;">Product Name *</label>
        <input type="text" id="admin-prod-name" required placeholder="e.g. VP-9000 High-Precision Flow Sensor" style="width:100%; box-sizing:border-box; background:#091827; border:1px solid #1B3A5C; color:#fff; padding:10px 12px; border-radius:6px; font-size:13px;">
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
        <div>
          <label style="display:block; font-size:12px; font-weight:600; color:#94a3b8; margin-bottom:6px; text-transform:uppercase;">Billing Model</label>
          <select id="admin-prod-model" style="width:100%; box-sizing:border-box; background:#091827; border:1px solid #1B3A5C; color:#fff; padding:10px 12px; border-radius:6px; font-size:13px;">
            <option value="PerUnit">PerUnit</option>
            <option value="Monthly">Monthly</option>
            <option value="Hourly">Hourly</option>
            <option value="Flat">Flat</option>
          </select>
        </div>
        <div>
          <label style="display:block; font-size:12px; font-weight:600; color:#94a3b8; margin-bottom:6px; text-transform:uppercase;">Price (€) *</label>
          <input type="number" step="0.01" min="0" id="admin-prod-price" required placeholder="0.00" style="width:100%; box-sizing:border-box; background:#091827; border:1px solid #1B3A5C; color:#fff; padding:10px 12px; border-radius:6px; font-size:13px;">
        </div>
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
        <div>
          <label style="display:block; font-size:12px; font-weight:600; color:#94a3b8; margin-bottom:6px; text-transform:uppercase;">Initial Stock Qty</label>
          <input type="number" step="1" min="0" id="admin-prod-stock" value="50" style="width:100%; box-sizing:border-box; background:#091827; border:1px solid #1B3A5C; color:#fff; padding:10px 12px; border-radius:6px; font-size:13px;">
        </div>
        <div>
          <label style="display:block; font-size:12px; font-weight:600; color:#94a3b8; margin-bottom:6px; text-transform:uppercase;">Catalog Status</label>
          <select id="admin-prod-active" style="width:100%; box-sizing:border-box; background:#091827; border:1px solid #1B3A5C; color:#fff; padding:10px 12px; border-radius:6px; font-size:13px;">
            <option value="1">Active in Storefront</option>
            <option value="0">Deactivated / Draft</option>
          </select>
        </div>
      </div>
      <div style="margin-bottom:14px;">
        <label style="display:block; font-size:12px; font-weight:600; color:#94a3b8; margin-bottom:6px; text-transform:uppercase;">Technical Description</label>
        <textarea id="admin-prod-desc" rows="3" placeholder="Industrial specifications, compliance standards, and operating limits..." style="width:100%; box-sizing:border-box; background:#091827; border:1px solid #1B3A5C; color:#fff; padding:10px 12px; border-radius:6px; font-size:13px; resize:vertical;"></textarea>
      </div>
      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; border-top:1px solid #1B3A5C; padding-top:16px;">
        <button type="button" onclick="closeAllAdminModals()" style="background:#1e293b; color:#cbd5e1; border:1px solid #334155; padding:9px 16px; border-radius:6px; cursor:pointer; font-weight:600;">Cancel</button>
        <button type="submit" id="admin-prod-submit-btn" style="background:#F59E0B; color:#000; border:none; padding:9px 18px; border-radius:6px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px;">
          <span class="material-symbols-outlined" style="font-size:18px;">save</span>
          <span>Save Product</span>
        </button>
      </div>
    </form>
  </div>

  <!-- 2. Technical Image Upload Modal -->
  <div id="admin-upload-modal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:10050; width:90%; max-width:480px; background:#0F2438; border:1px solid #1B3A5C; border-radius:10px; box-shadow:0 20px 50px rgba(0,0,0,0.7); color:#e2e8f0; font-family:'IBM Plex Sans', sans-serif;">
    <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-bottom:1px solid #1B3A5C; background:#142c44;">
      <h3 style="margin:0; font-size:16px; font-weight:700; color:#fff; display:flex; align-items:center; gap:8px;">
        <span class="material-symbols-outlined" style="color:#00E5FF; font-size:20px;">cloud_upload</span>
        <span>Upload Product Image</span>
      </h3>
      <button onclick="closeAllAdminModals()" style="background:none; border:none; color:#94a3b8; font-size:18px; cursor:pointer;">✕</button>
    </div>
    <form id="admin-upload-form" onsubmit="submitImageUpload(event)" style="padding:20px;">
      <input type="hidden" id="admin-upload-prod-id" value="">
      <p style="font-size:13px; color:#cbd5e1; margin-bottom:12px;">Upload a technical photograph or CAD render for <strong id="admin-upload-prod-display" style="color:#F59E0B; font-family:'JetBrains Mono',monospace;"></strong>.</p>
      <div style="background:#091827; border:2px dashed #1B3A5C; border-radius:8px; padding:24px; text-align:center; margin-bottom:14px;">
        <span class="material-symbols-outlined" style="font-size:36px; color:#64748b; margin-bottom:8px;">photo_library</span>
        <input type="file" id="admin-upload-file" accept="image/jpeg,image/png,image/webp" required style="display:block; margin:0 auto; font-size:12px; color:#94a3b8;">
        <span style="display:block; font-size:11px; color:#64748b; margin-top:8px;">Supported formats: JPG, PNG, WebP (Max: 2 MB)</span>
      </div>
      <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #1B3A5C; padding-top:16px;">
        <button type="button" onclick="closeAllAdminModals()" style="background:#1e293b; color:#cbd5e1; border:1px solid #334155; padding:9px 16px; border-radius:6px; cursor:pointer;">Cancel</button>
        <button type="submit" id="admin-upload-submit-btn" style="background:#00E5FF; color:#041421; border:none; padding:9px 18px; border-radius:6px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px;">
          <span class="material-symbols-outlined" style="font-size:18px;">upload</span>
          <span>Upload Image</span>
        </button>
      </div>
    </form>
  </div>

  <!-- 3. Force Delete Confirmation Modal -->
  <div id="admin-forcedel-modal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:10050; width:90%; max-width:480px; background:#0F2438; border:1px solid #EF4444; border-radius:10px; box-shadow:0 20px 50px rgba(0,0,0,0.7); color:#e2e8f0; font-family:'IBM Plex Sans', sans-serif;">
    <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-bottom:1px solid rgba(239,68,68,0.3); background:#271216;">
      <h3 style="margin:0; font-size:16px; font-weight:700; color:#EF4444; display:flex; align-items:center; gap:8px;">
        <span class="material-symbols-outlined" style="color:#EF4444; font-size:20px;">warning</span>
        <span>Permanent Force Delete</span>
      </h3>
      <button onclick="closeAllAdminModals()" style="background:none; border:none; color:#94a3b8; font-size:18px; cursor:pointer;">✕</button>
    </div>
    <form id="admin-forcedel-form" onsubmit="submitForceDelete(event)" style="padding:20px;">
      <input type="hidden" id="admin-forcedel-prod-id" value="">
      <p style="font-size:13px; color:#cbd5e1; line-height:1.5; margin-bottom:12px;">
        You are about to permanently purge product <strong id="admin-forcedel-prod-display" style="color:#F59E0B; font-family:'JetBrains Mono',monospace;"></strong> from the system catalog.
      </p>
      <div style="background:rgba(239,68,68,0.1); border-left:4px solid #EF4444; padding:10px 12px; border-radius:4px; font-size:12px; color:#fca5a5; margin-bottom:14px;">
        <strong>Safety Rule:</strong> Force delete will only succeed if the product has never been ordered or quoted. If referenced in orders, it is automatically soft-deleted with a historical snapshot.
      </div>
      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:12px; font-weight:600; color:#94a3b8; margin-bottom:6px;">
          Type confirmation: <code id="admin-forcedel-expected" style="color:#F59E0B;"></code>
        </label>
        <input type="text" id="admin-forcedel-input" required placeholder="DELETE PROD-xxxx" style="width:100%; box-sizing:border-box; background:#091827; border:1px solid #EF4444; color:#fff; padding:10px 12px; border-radius:6px; font-size:13px; font-family:'JetBrains Mono',monospace;">
      </div>
      <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid rgba(239,68,68,0.3); padding-top:16px;">
        <button type="button" onclick="closeAllAdminModals()" style="background:#1e293b; color:#cbd5e1; border:1px solid #334155; padding:9px 16px; border-radius:6px; cursor:pointer;">Cancel</button>
        <button type="submit" id="admin-forcedel-submit-btn" style="background:#EF4444; color:#fff; border:none; padding:9px 18px; border-radius:6px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px;">
          <span class="material-symbols-outlined" style="font-size:18px;">delete_forever</span>
          <span>Confirm Permanent Delete</span>
        </button>
      </div>
    </form>
  </div>

  <div id="admin-modal-backdrop" onclick="closeAllAdminModals()" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.7); z-index:10040; backdrop-filter:blur(2px);"></div>
  <?php endif; ?>

  <!-- ========================================================================
       FOOTER (Baseline Architecture Reference)
       ======================================================================== -->
  <footer class="app-footer">
    <div class="footer-content">
      <div class="footer-left">
        <div class="shop-footer-brand">
          <strong class="shop-footer-company">VOSTOKPRIBOR JSC</strong>
          <span>· System 02: B2B Industrial E-Commerce Platform</span>
        </div>
        <span class="shop-divider-v">|</span>
        <span>Almaty, Kazakhstan (Est. 1968)</span>
      </div>

      <div class="footer-links">
        <span>FQDN: <code>shop.vostokpribor.local</code></span>
        <span class="shop-divider-v">·</span>
        <a href="javascript:void(0)" onclick="window.shopApp.navigateTo('catalog')">Catalog</a>
        <a href="javascript:void(0)" onclick="window.shopApp.navigateTo('tracking')">Order Tracking</a>
        <a href="../VOSTOKPRIBOR Corporate Web Platform/index.php">Corporate (Sys 01)</a>
        <a href="../Customer Portal/Dashboard.php">Customer Portal (Sys 03)</a>
        <a href="../Employee Intranet/login.php">Intranet (Sys 04)</a>
        <a href="../CRM/index.php">CRM (Sys 05)</a>
        <a href="../HR System/login.php">HR (Sys 06)</a>
        <a href="../Finance & Billing/login.php">Finance (Sys 07)</a>
        <a href="../IT Helpdesk/login.php">Helpdesk (Sys 08)</a>
        <a href="../File Center/login.php">File Center (Sys 09)</a>
        <a href="../Developer/login.php">Developer (Sys 10)</a>
        <a href="../Admin & Governance Portal/login.php">Admin (Sys 11)</a>
        <a href="javascript:void(0)"
          onclick="window.shopApp.downloadDoc('DOC-2026-009', 'Full Catalog PDF')">DOC-2026-009</a>
      </div>
    </div>
  </footer>

  <!-- Consolidated JavaScript Application -->
  <script src="js/app.js?v=<?= time() ?>"></script>
  <link rel="stylesheet" href="../assets/css/api-ui.css">
  <script src="../assets/js/api-core.js"></script>
  <script src="../assets/js/api-shop.js"></script>
  <script src="js/shop-data.js?v=<?= time() ?>"></script>
  <script src="../assets/js/notifications-hub.js" defer></script>
  <script>
    // Ensure cart starts strictly empty on login
    document.addEventListener("DOMContentLoaded", function () {
      const hcb = document.getElementById("header-cart-badge");
      if (hcb) hcb.textContent = "0";
      const csub = document.getElementById("cart-subtotal");
      if (csub) csub.textContent = "€0.00";
      const ctot = document.getElementById("cart-total");
      if (ctot) ctot.textContent = "€0.00";
    });

    window.selectEnterpriseAccount = function(customerId, event) {
      if (event) {
        event.stopPropagation();
        event.preventDefault();
      }
      try {
        sessionStorage.setItem("vp_cus_id", customerId);
        localStorage.setItem("vp_cus_id", customerId);
      } catch (e) {}

      const menu = document.getElementById("customer-dropdown-menu");
      if (menu) menu.classList.remove("show");

      if (window.shopApp && typeof window.shopApp.showToast === 'function') {
        window.shopApp.showToast(`Switched active enterprise client to ${customerId}... Reloading context.`, "green");
      }
      setTimeout(() => {
        window.location.href = `Dashboard.php?cus_id=${encodeURIComponent(customerId)}`;
      }, 200);
    };

    function filterCustomerDropdownList(query) {
      const q = (query || '').toLowerCase().trim();
      const items = document.querySelectorAll('#customer-dropdown-items .customer-option-item');
      items.forEach(el => {
        const code = (el.dataset.code || '').toLowerCase();
        const name = (el.dataset.name || '').toLowerCase();
        if (!q || code.includes(q) || name.includes(q)) {
          el.style.display = 'flex';
        } else {
          el.style.display = 'none';
        }
      });
    }
  </script>

  <?php if ($canManageProducts): ?>
  <script>
    let adminProductsCache = [];

    function getAdminCsrfToken() {
      const meta = document.querySelector('meta[name="csrf-token"]');
      return meta ? meta.content : '';
    }

    function showAdminProductsScreen() {
      if (window.shopApp && typeof window.shopApp.navigateTo === 'function') {
        window.shopApp.navigateTo('admin-products');
      } else {
        document.querySelectorAll('.screen-view').forEach(el => {
          el.classList.remove('active');
          el.style.display = 'none';
        });
        const adminSec = document.getElementById('view-admin-products');
        if (adminSec) {
          adminSec.classList.add('active');
          adminSec.style.display = 'block';
        }
        document.querySelectorAll('.sidebar-nav-item').forEach(el => {
          el.classList.remove('active');
          if (el.dataset.screen === 'admin-products') el.classList.add('active');
        });
      }
    }

    async function loadAdminProducts() {
      showAdminProductsScreen();
      const tbody = document.getElementById('admin-products-tbody');
      if (!tbody) return;
      tbody.innerHTML = '<tr><td colspan="8" style="padding:24px; text-align:center; color:#94a3b8;">Loading product catalog...</td></tr>';
      try {
        const res = await fetch('api/admin_products.php?action=list');
        const json = await res.json();
        if (!json.success) throw new Error(json.message || 'Failed to load products');
        adminProductsCache = json.data || [];
        renderAdminProductsTable(adminProductsCache);
      } catch (err) {
        tbody.innerHTML = `<tr><td colspan="8" style="padding:24px; text-align:center; color:#EF4444;">Error: ${err.message}</td></tr>`;
      }
    }

    function renderAdminProductsTable(products) {
      const tbody = document.getElementById('admin-products-tbody');
      if (!tbody) return;
      if (!products.length) {
        tbody.innerHTML = '<tr><td colspan="8" style="padding:24px; text-align:center; color:#94a3b8;">No products found. Click "Add Product" to create one.</td></tr>';
        return;
      }
      tbody.innerHTML = products.map(p => {
        const isActive = parseInt(p.is_active, 10) === 1;
        const statusBadge = isActive
          ? '<span style="background:rgba(16,185,129,0.2); color:#10B981; border:1px solid rgba(16,185,129,0.3); padding:3px 8px; border-radius:4px; font-size:11px; font-weight:600;">Active</span>'
          : '<span style="background:rgba(239,68,68,0.2); color:#EF4444; border:1px solid rgba(239,68,68,0.3); padding:3px 8px; border-radius:4px; font-size:11px; font-weight:600;">Deactivated</span>';
        
        const imgSrc = p.image_url ? `../${p.image_url}` : '';
        const imgCell = imgSrc 
          ? `<img src="${imgSrc}" alt="${p.prod_id}" style="width:40px; height:40px; object-fit:cover; border-radius:4px; border:1px solid #1B3A5C;">`
          : `<div style="width:40px; height:40px; background:#142c44; border-radius:4px; border:1px solid #1B3A5C; display:flex; align-items:center; justify-content:center; color:#64748b;"><span class="material-symbols-outlined" style="font-size:20px;">image</span></div>`;

        return `
          <tr style="border-bottom:1px solid #1B3A5C; transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
            <td style="padding:12px 16px; font-family:'JetBrains Mono',monospace; font-weight:600; color:#F59E0B;">${p.prod_id}</td>
            <td style="padding:12px 16px;">${imgCell}</td>
            <td style="padding:12px 16px;">
              <div style="font-weight:600; color:#fff;">${escapeHtml(p.product_name)}</div>
              <div style="font-size:11px; color:#94a3b8; max-width:320px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${escapeHtml(p.description || '')}</div>
            </td>
            <td style="padding:12px 16px; font-family:'JetBrains Mono',monospace; color:#cbd5e1;">${p.billing_model}</td>
            <td style="padding:12px 16px; font-weight:700; color:#00E5FF;">€${parseFloat(p.price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td style="padding:12px 16px; font-family:'JetBrains Mono',monospace; color:${p.stock > 10 ? '#10B981' : '#F59E0B'};">${p.stock}</td>
            <td style="padding:12px 16px;">${statusBadge}</td>
            <td style="padding:12px 16px; text-align:right;">
              <div style="display:inline-flex; gap:6px;">
                <button onclick="openEditProductModal('${p.prod_id}')" title="Edit Product" style="background:#1e293b; color:#cbd5e1; border:1px solid #334155; padding:6px 10px; border-radius:4px; cursor:pointer; font-size:12px;">Edit</button>
                <button onclick="openUploadModal('${p.prod_id}')" title="Upload Image" style="background:#142c44; color:#00E5FF; border:1px solid #00E5FF44; padding:6px 10px; border-radius:4px; cursor:pointer; font-size:12px;">Upload</button>
                <button onclick="adminSoftDelete('${p.prod_id}')" title="Soft Delete / Deactivate" style="background:#2d1b1f; color:#F87171; border:1px solid #F8717144; padding:6px 10px; border-radius:4px; cursor:pointer; font-size:12px;">Deactivate</button>
                <button onclick="openForceDeleteModal('${p.prod_id}')" title="Force Delete" style="background:#3b1117; color:#EF4444; border:1px solid #EF444488; padding:6px 10px; border-radius:4px; cursor:pointer; font-size:12px;">Force Del</button>
              </div>
            </td>
          </tr>
        `;
      }).join('');
    }

    function escapeHtml(str) {
      if (!str) return '';
      return String(str).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    function closeAllAdminModals() {
      document.getElementById('admin-product-modal').style.display = 'none';
      document.getElementById('admin-upload-modal').style.display = 'none';
      document.getElementById('admin-forcedel-modal').style.display = 'none';
      document.getElementById('admin-modal-backdrop').style.display = 'none';
    }

    function openAddProductModal() {
      document.getElementById('admin-prod-modal-title').innerHTML = '<span class="material-symbols-outlined" style="color:#F59E0B; font-size:20px;">inventory_2</span><span>Add Industrial Product</span>';
      document.getElementById('admin-prod-id').value = '';
      document.getElementById('admin-prod-name').value = '';
      document.getElementById('admin-prod-model').value = 'PerUnit';
      document.getElementById('admin-prod-price').value = '';
      document.getElementById('admin-prod-stock').value = '50';
      document.getElementById('admin-prod-active').value = '1';
      document.getElementById('admin-prod-desc').value = '';
      document.getElementById('admin-modal-backdrop').style.display = 'block';
      document.getElementById('admin-product-modal').style.display = 'block';
    }

    function openEditProductModal(prodId) {
      const p = adminProductsCache.find(x => x.prod_id === prodId);
      if (!p) return;
      document.getElementById('admin-prod-modal-title').innerHTML = `<span class="material-symbols-outlined" style="color:#F59E0B; font-size:20px;">edit</span><span>Edit Product ${prodId}</span>`;
      document.getElementById('admin-prod-id').value = p.prod_id;
      document.getElementById('admin-prod-name').value = p.product_name;
      document.getElementById('admin-prod-model').value = p.billing_model;
      document.getElementById('admin-prod-price').value = p.price;
      document.getElementById('admin-prod-stock').value = p.stock;
      document.getElementById('admin-prod-active').value = p.is_active;
      document.getElementById('admin-prod-desc').value = p.description || '';
      document.getElementById('admin-modal-backdrop').style.display = 'block';
      document.getElementById('admin-product-modal').style.display = 'block';
    }

    async function submitProductForm(e) {
      e.preventDefault();
      const prodId = document.getElementById('admin-prod-id').value;
      const isEdit = !!prodId;
      const payload = {
        prod_id: prodId,
        product_name: document.getElementById('admin-prod-name').value,
        billing_model: document.getElementById('admin-prod-model').value,
        price: parseFloat(document.getElementById('admin-prod-price').value),
        stock: parseInt(document.getElementById('admin-prod-stock').value, 10),
        is_active: parseInt(document.getElementById('admin-prod-active').value, 10),
        description: document.getElementById('admin-prod-desc').value
      };

      const btn = document.getElementById('admin-prod-submit-btn');
      btn.disabled = true;
      btn.textContent = 'Saving...';

      try {
        const res = await fetch('api/admin_products.php', {
          method: isEdit ? 'PUT' : 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': getAdminCsrfToken()
          },
          body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (!json.success) throw new Error(json.message || json.error || 'Failed to save');
        alert(isEdit ? 'Product updated successfully.' : `Product created successfully: ${json.data.prod_id}`);
        closeAllAdminModals();
        loadAdminProducts();
      } catch (err) {
        alert(`Error: ${err.message}`);
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:18px;">save</span><span>Save Product</span>';
      }
    }

    function openUploadModal(prodId) {
      document.getElementById('admin-upload-prod-id').value = prodId;
      document.getElementById('admin-upload-prod-display').textContent = prodId;
      document.getElementById('admin-upload-file').value = '';
      document.getElementById('admin-modal-backdrop').style.display = 'block';
      document.getElementById('admin-upload-modal').style.display = 'block';
    }

    async function submitImageUpload(e) {
      e.preventDefault();
      const prodId = document.getElementById('admin-upload-prod-id').value;
      const fileInput = document.getElementById('admin-upload-file');
      if (!fileInput.files || !fileInput.files[0]) {
        alert('Please select an image file.');
        return;
      }
      const formData = new FormData();
      formData.append('image', fileInput.files[0]);
      formData.append('prod_id', prodId);
      formData.append('action', 'upload');
      formData.append('csrf_token', getAdminCsrfToken());

      const btn = document.getElementById('admin-upload-submit-btn');
      btn.disabled = true;
      btn.textContent = 'Uploading...';

      try {
        const res = await fetch('api/admin_products.php', {
          method: 'POST',
          headers: {
            'X-CSRF-Token': getAdminCsrfToken()
          },
          body: formData
        });
        const json = await res.json();
        if (!json.success) throw new Error(json.message || json.error || 'Upload failed');
        alert(`Image uploaded successfully: ${json.data.image_url}`);
        closeAllAdminModals();
        loadAdminProducts();
      } catch (err) {
        alert(`Upload Error: ${err.message}`);
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:18px;">upload</span><span>Upload Image</span>';
      }
    }

    async function adminSoftDelete(prodId) {
      if (!confirm(`Are you sure you want to deactivate (soft-delete) ${prodId}?`)) return;
      try {
        const res = await fetch('api/admin_products.php', {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': getAdminCsrfToken()
          },
          body: JSON.stringify({ prod_id: prodId, force: 0 })
        });
        const json = await res.json();
        if (!json.success) throw new Error(json.message || json.error || 'Deactivation failed');
        alert(json.message || 'Product deactivated.');
        loadAdminProducts();
      } catch (err) {
        alert(`Error: ${err.message}`);
      }
    }

    function openForceDeleteModal(prodId) {
      document.getElementById('admin-forcedel-prod-id').value = prodId;
      document.getElementById('admin-forcedel-prod-display').textContent = prodId;
      document.getElementById('admin-forcedel-expected').textContent = `DELETE ${prodId}`;
      document.getElementById('admin-forcedel-input').value = '';
      document.getElementById('admin-modal-backdrop').style.display = 'block';
      document.getElementById('admin-forcedel-modal').style.display = 'block';
    }

    async function submitForceDelete(e) {
      e.preventDefault();
      const prodId = document.getElementById('admin-forcedel-prod-id').value;
      const confirmInput = document.getElementById('admin-forcedel-input').value.trim();
      const expected = `DELETE ${prodId}`;
      if (confirmInput !== expected) {
        alert(`Typed confirmation does not match. You must type exactly: "${expected}"`);
        return;
      }

      const btn = document.getElementById('admin-forcedel-submit-btn');
      btn.disabled = true;
      btn.textContent = 'Deleting...';

      try {
        const res = await fetch('api/admin_products.php', {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': getAdminCsrfToken()
          },
          body: JSON.stringify({ prod_id: prodId, force: 1, confirm: confirmInput })
        });
        const json = await res.json();
        if (!json.success) throw new Error(json.message || json.error || 'Delete failed');
        alert(json.message || 'Product deleted.');
        closeAllAdminModals();
        loadAdminProducts();
      } catch (err) {
        alert(`Error: ${err.message}`);
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:18px;">delete_forever</span><span>Confirm Permanent Delete</span>';
      }
    }
  </script>
  <?php endif; ?>
</body>

</html>