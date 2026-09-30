<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (empty($_SESSION['vostok_authenticated']) || empty($_SESSION['vostok_system_SHP'])) {
  header("Location: login.php");
  exit;
}
require_once __DIR__ . '/shop_service.php';

$cusId = shop_getCurrentCustomerId();
$customer = null;
if ($cusId) {
    $stmtC = getDbConnection()->prepare("SELECT * FROM customers WHERE cus_id = ?");
    $stmtC->execute([$cusId]);
    $customer = $stmtC->fetch(PDO::FETCH_ASSOC);
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
  <title>VOSTOKPRIBOR | B2B Industrial E-Commerce Platform (shop.vostokpribor.local)</title>


  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

  <!-- Consolidated Stylesheet (Strict System Tokens & UI Components) -->
  <link rel="stylesheet" href="css/style.css">
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
        <!-- Account / Customer Context Switcher -->
        <div class="customer-context-selector" onclick="window.shopApp.toggleCustomerDropdown(event)">
          <div class="customer-avatar" id="header-customer-avatar">—</div>
          <div class="customer-info">
            <span class="customer-code" id="header-customer-code">—</span>
            <span class="customer-name" id="header-customer-name">Enterprise Account</span>
          </div>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            class="shop-text-muted-dark">
            <polyline points="6 9 12 15 18 9" />
          </svg>

          <!-- Dropdown Account Switcher Menu -->
          <div class="customer-dropdown-menu" id="customer-dropdown-menu">
            <div class="dropdown-header-label">Switch Enterprise Account</div>
            <div id="customer-dropdown-items">
              <!-- Dynamically populated from MySQL database by shop-data.js -->
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
      <a href="./api/logout.php?redirect=../Online%20Shop%20B2B/login.php" class="top-signout-btn" title="Sign Out of Online Shop B2B" onclick="(function(){sessionStorage.clear();localStorage.clear();})()"><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
    </div>
  </header>

  <!-- ========================================================================
       LEFT SIDEBAR NAVIGATION (Hover-to-Open & Hover-to-Close)
       ======================================================================== -->
  <aside class="sidebar b2b-sidebar" id="b2b-sidebar">
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
    </nav>

    <div class="sidebar-footer">
      <div class="b2b-account-badge" style="background:rgba(255,255,255,0.05); padding:10px; border-radius:8px; border:1px solid rgba(255,255,255,0.1);">
        <div style="font-size:10px; font-family:'JetBrains Mono',monospace; color:#94a3b8; text-transform:uppercase;">Account Mode</div>
        <div style="font-size:12px; font-weight:600; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" id="sidebar-customer-name"><?= htmlspecialchars($customer['company_name'] ?? 'Enterprise Account') ?></div>
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
        <span class="price-value shop-price-cart-subtotal" id="cart-subtotal">€19,300</span>
      </div>

      <div
        class="shop-summary-row-total">
        <span class="shop-summary-label-total">Total Purchase Commitment:</span>
        <span class="price-unit-large shop-price-cart-total" id="cart-total">€19,300</span>
      </div>

      <button class="btn-primary-amber shop-btn-modal-action" onclick="window.shopApp.submitDirectPO()">
        Confirm Purchase Order (PO) & Generate Invoice
      </button>
    </div>
  </div>

  <!-- Toast Container -->
  <div class="toast-container" id="toast-container"></div>

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
        <a href="../Employee Intranet/index.php">Intranet (Sys 04)</a>
        <a href="../CRM/index.php">CRM (Sys 05)</a>
        <a href="../HR System/index.php">HR (Sys 06)</a>
        <a href="../Finance & Billing/index.php">Finance (Sys 07)</a>
        <a href="../IT Helpdesk/index.php">Helpdesk (Sys 08)</a>
        <a href="../File Center/index.php">File Center (Sys 09)</a>
        <a href="../Developer/index.php">Developer (Sys 10)</a>
        <a href="../Admin & Governance Portal/index.php">Admin (Sys 11)</a>
        <a href="javascript:void(0)"
          onclick="window.shopApp.downloadDoc('DOC-2026-009', 'Full Catalog PDF')">DOC-2026-009</a>
      </div>
    </div>
  </footer>

  <!-- Consolidated JavaScript Application -->
  <script src="js/app.js"></script>
  <link rel="stylesheet" href="../assets/css/api-ui.css">
  <script src="../assets/js/api-core.js"></script>
  <script src="../assets/js/api-shop.js"></script>
  <script src="js/shop-data.js"></script>
  <script src="../assets/js/notifications-hub.js" defer></script>
</body>

</html>