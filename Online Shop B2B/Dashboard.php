<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (empty($_SESSION['vostok_authenticated']) || empty($_SESSION['vostok_system_SHP'])) {
  header("Location: login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR | B2B Industrial E-Commerce Platform (shop.vostokpribor.local)</title>

  <script>
    (function() {
      const isAuthenticated = localStorage.getItem('vostok_authenticated');

      if (!isAuthenticated || isAuthenticated !== 'true') {
        window.location.href = 'login.php';
      }
    })();
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap"
    rel="stylesheet">

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
              <span style="opacity: 0.5;">|</span>
              <span>ENTERPRISE B2B</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Navigation Links -->
      <nav class="main-navigation">
        <a class="nav-link active" data-screen="catalog" onclick="window.shopApp.navigateTo('catalog')">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="7" />
            <rect x="14" y="3" width="7" height="7" />
            <rect x="14" y="14" width="7" height="7" />
            <rect x="3" y="14" width="7" height="7" />
          </svg>
          <span>Catalog</span>
        </a>

        <a class="nav-link" data-screen="quotes" onclick="window.shopApp.openRfqDrawer()">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
            <polyline points="14 2 14 8 20 8" />
            <line x1="16" y1="13" x2="8" y2="13" />
            <line x1="16" y1="17" x2="8" y2="17" />
          </svg>
          <span>My Quotes</span>
          <span class="nav-badge-count" id="nav-quotes-count">2</span>
        </a>

        <a class="nav-link" data-screen="tracking" onclick="window.shopApp.navigateTo('tracking')">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="1" y="3" width="15" height="13" />
            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
            <circle cx="5.5" cy="18.5" r="2.5" />
            <circle cx="18.5" cy="18.5" r="2.5" />
          </svg>
          <span>Order Tracking</span>
        </a>

        <a class="nav-link" data-screen="orders" onclick="window.shopApp.navigateTo('tracking')">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="8" y1="6" x2="21" y2="6" />
            <line x1="8" y1="12" x2="21" y2="12" />
            <line x1="8" y1="18" x2="21" y2="18" />
            <line x1="3" y1="6" x2="3.01" y2="6" />
            <line x1="3" y1="12" x2="3.01" y2="12" />
            <line x1="3" y1="18" x2="3.01" y2="18" />
          </svg>
          <span>Orders</span>
        </a>
        <a class="nav-link" href="Integrations.php" style="color: #00E5FF; border: 1px solid rgba(0,229,255,0.3); border-radius: 4px; padding: 0.35rem 0.6rem; margin-left: 0.5rem;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#00E5FF" stroke-width="2">
            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
          </svg>
          <span style="font-weight: 600;">Integrations</span>
          <span class="nav-badge-count" style="background: rgba(0,229,255,0.2); color: #00E5FF;">SYS10</span>
        </a>
      </nav>

      <!-- Right Header Actions (Customer Context, Cart, RFQ Button) -->
      <div class="header-actions">
        <!-- Account / Customer Context Switcher -->
        <div class="customer-context-selector" onclick="window.shopApp.toggleCustomerDropdown(event)">
          <div class="customer-avatar" id="header-customer-avatar">1005</div>
          <div class="customer-info">
            <span class="customer-code" id="header-customer-code">CUS-1005</span>
            <span class="customer-name" id="header-customer-name">Tashkent Precision Controls</span>
          </div>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            style="color: var(--text-on-dark-muted);">
            <polyline points="6 9 12 15 18 9" />
          </svg>

          <!-- Dropdown Account Switcher Menu -->
          <div class="customer-dropdown-menu" id="customer-dropdown-menu">
            <div class="dropdown-header-label">Switch Enterprise Account</div>

            <div class="customer-option-item selected" data-customer-id="CUS-1005"
              onclick="window.shopApp.selectCustomer('CUS-1005')">
              <div>
                <strong style="font-size: 12px; color: var(--text-primary); display: block;">Tashkent Precision
                  Controls</strong>
                <span style="font-family: var(--font-mono); font-size: 10.5px; color: var(--text-secondary);">CUS-1005 ·
                  Manufacturing</span>
              </div>
              <span
                style="font-family: var(--font-mono); font-size: 10px; color: var(--accent-teal); font-weight: 600;">Tier
                A (-12%)</span>
            </div>

            <div class="customer-option-item" data-customer-id="CUS-1001"
              onclick="window.shopApp.selectCustomer('CUS-1001')">
              <div>
                <strong style="font-size: 12px; color: var(--text-primary); display: block;">Aral Geomatics
                  Group</strong>
                <span style="font-family: var(--font-mono); font-size: 10.5px; color: var(--text-secondary);">CUS-1001 ·
                  Geomatics & GIS</span>
              </div>
              <span
                style="font-family: var(--font-mono); font-size: 10px; color: var(--accent-teal); font-weight: 600;">Tier
                A (-12%)</span>
            </div>

            <div class="customer-option-item" data-customer-id="CUS-1002"
              onclick="window.shopApp.selectCustomer('CUS-1002')">
              <div>
                <strong style="font-size: 12px; color: var(--text-primary); display: block;">BaltNord Process
                  Systems</strong>
                <span style="font-family: var(--font-mono); font-size: 10.5px; color: var(--text-secondary);">CUS-1002 ·
                  Process Automation</span>
              </div>
              <span
                style="font-family: var(--font-mono); font-size: 10px; color: var(--accent-teal); font-weight: 600;">Partner
                (-15%)</span>
            </div>

            <div class="customer-option-item" data-customer-id="CUS-1003"
              onclick="window.shopApp.selectCustomer('CUS-1003')">
              <div>
                <strong style="font-size: 12px; color: var(--text-primary); display: block;">Steppe Mining
                  Technologies</strong>
                <span style="font-family: var(--font-mono); font-size: 10.5px; color: var(--text-secondary);">CUS-1003 ·
                  Mining</span>
              </div>
              <span
                style="font-family: var(--font-mono); font-size: 10px; color: var(--accent-teal); font-weight: 600;">Tier
                B (-8%)</span>
            </div>

            <div class="customer-option-item" data-customer-id="CUS-1007"
              onclick="window.shopApp.selectCustomer('CUS-1007')">
              <div>
                <strong style="font-size: 12px; color: var(--text-primary); display: block;">Caspian Industrial
                  Robotics</strong>
                <span style="font-family: var(--font-mono); font-size: 10.5px; color: var(--text-secondary);">CUS-1007 ·
                  Robotics</span>
              </div>
              <span
                style="font-family: var(--font-mono); font-size: 10px; color: var(--accent-teal); font-weight: 600;">Partner
                (-15%)</span>
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
          <span class="cart-count-badge" id="header-cart-badge">6</span>
        </div>

        <!-- Quick RFQ CTA Button -->
        <button class="rfq-quick-btn" onclick="window.shopApp.openRfqDrawer()">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M12 5v14M5 12h14" />
          </svg>
          <span>RFQ Builder</span>
        </button>
      </div>

      <!-- Top Bar Sign Out -->
      <a href="../api/logout.php?system=Online%20Shop%20B2B&redirect=../Online%20Shop%20B2B/login.php" class="top-signout-btn" title="Sign Out of Online Shop B2B" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:4px;background:rgba(178,58,50,0.2);border:1px solid rgba(178,58,50,0.5);color:#FF8080;font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;margin-left:8px;vertical-align:middle;transition:all 0.2s;" onmouseover="this.style.background='rgba(178,58,50,0.4)';this.style.color='#FFFFFF'" onmouseout="this.style.background='rgba(178,58,50,0.2)';this.style.color='#FF8080'"><span class="material-symbols-outlined" style="font-size:15px;line-height:1;">Sign Out</span></a>
    </div>
  </header>

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
          <strong style="color: var(--status-green);">Active (99.98%)</strong>
        </div>
        <div class="telemetry-tag">
          <span>Account Manager:</span>
          <strong>EMP-1008 (Bekzod Rakhimov)</strong>
        </div>
        <div class="telemetry-tag">
          <span>Security Classification:</span>
          <strong style="color: var(--confidential-tag);">L1 Public / L3 Pricing</strong>
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
              <span class="filter-count-badge">5</span>
            </div>
            <div class="filter-option-list">
              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="category"
                  data-filter-value="Optical Sensors">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Optical Sensors</span>
                <span class="filter-item-qty">(2)</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="category"
                  data-filter-value="Measurement Kits">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Measurement Kits</span>
                <span class="filter-item-qty">(1)</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="category"
                  data-filter-value="PLC Integration">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">PLC Integration</span>
                <span class="filter-item-qty">(2)</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="category"
                  data-filter-value="Monitoring Gateways">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Monitoring Gateways</span>
                <span class="filter-item-qty">(3)</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="category"
                  data-filter-value="Calibration Stations">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Calibration Stations</span>
                <span class="filter-item-qty">(2)</span>
              </label>
            </div>
          </div>

          <!-- Industry Sector Filter -->
          <div class="filter-group">
            <div class="filter-title">
              <span>Target Sector</span>
              <span class="filter-count-badge">6</span>
            </div>
            <div class="filter-option-list">
              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="sector"
                  data-filter-value="Geomatics & GIS">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Geomatics & GIS</span>
                <span class="filter-item-qty">(3)</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="sector"
                  data-filter-value="Process Automation">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Process Automation</span>
                <span class="filter-item-qty">(4)</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="sector"
                  data-filter-value="Mining">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Mining</span>
                <span class="filter-item-qty">(3)</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="sector"
                  data-filter-value="Industrial Metrology">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Industrial Metrology</span>
                <span class="filter-item-qty">(2)</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="sector"
                  data-filter-value="Robotics">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Robotics</span>
                <span class="filter-item-qty">(3)</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="sector"
                  data-filter-value="Water Infrastructure">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Water Infrastructure</span>
                <span class="filter-item-qty">(2)</span>
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
                <span class="filter-item-qty">(6)</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="availability"
                  data-filter-value="lead-time">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Lead Time &lt; 3 Wks</span>
                <span class="filter-item-qty">(2)</span>
              </label>

              <label class="filter-checkbox-label">
                <input type="checkbox" class="filter-checkbox-input" data-filter-type="availability"
                  data-filter-value="custom">
                <span class="custom-checkbox"></span>
                <span class="filter-label-text">Custom Engineering Order</span>
                <span class="filter-item-qty">(2)</span>
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
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                style="color: var(--text-tertiary);">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
              </svg>
              <input type="text" id="catalog-search-input"
                placeholder="Search by SKU, sensor type, protocol (e.g. Modbus, 4K)..."
                oninput="window.shopApp.setSearchTerm(this.value)">
            </div>

            <div class="toolbar-meta">
              <span class="results-count" id="catalog-results-count">Showing <strong>10</strong> industrial equipment
                packages</span>

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
            <div class="kpi-value">8 Active</div>
            <span class="kpi-trend positive">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <polyline points="18 15 12 9 6 15" />
              </svg>
              100% on schedule
            </span>
          </div>

          <div class="kpi-card">
            <span class="kpi-title">Total Pipeline Value</span>
            <div class="kpi-value">€596,083</div>
            <span class="kpi-trend neutral">
              <span>Across PRJ-2026-001 to 015</span>
            </span>
          </div>

          <div class="kpi-card">
            <span class="kpi-title">In-Transit Freight</span>
            <div class="kpi-value">3 Shipments</div>
            <span class="kpi-trend positive">
              <span>Rail & Air Freight Corridor</span>
            </span>
          </div>

          <div class="kpi-card">
            <span class="kpi-title">Pending Quotes (RFQ)</span>
            <div class="kpi-value">2 Open RFQs</div>
            <span class="kpi-trend neutral">
              <span>SAL Review by EMP-1008</span>
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
                  <th style="text-align: right;">Action</th>
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
        style="background: var(--bg-surface-alt); padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-light); font-size: 11.5px;">
        <div><strong>Corporate Account:</strong> <span id="rfq-client-name">Tashkent Precision Controls
            (CUS-1005)</span></div>
        <div><strong>Assigned Sales Mgr:</strong> EMP-1008 (Bekzod Rakhimov)</div>
      </div>

      <div
        style="font-size: 12px; font-weight: 700; color: var(--secondary-brand); text-transform: uppercase; letter-spacing: 0.5px;">
        Staged Equipment Packages
      </div>

      <div class="drawer-items-list" id="rfq-items-list">
        <!-- Dynamically populated -->
      </div>

      <div style="display: flex; flex-direction: column; gap: 8px;">
        <label style="font-size: 12px; font-weight: 600; color: var(--text-primary);">Associate with Project
          (Optional):</label>
        <select
          style="padding: 8px; border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); font-size: 12px; background: #FFFFFF;">
          <option value="PRJ-2026-005">PRJ-2026-005: Precision Automation Upgrade (€128,000)</option>
          <option value="PRJ-2026-013">PRJ-2026-013: Contract Review Staging (€112,000)</option>
          <option value="NEW">Create New Project Statement of Work</option>
        </select>
      </div>

      <div style="display: flex; flex-direction: column; gap: 8px;">
        <label style="font-size: 12px; font-weight: 600; color: var(--text-primary);">Target Delivery Deadline:</label>
        <input type="date" value="2026-10-15"
          style="padding: 8px; border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); font-size: 12px;">
      </div>
    </div>

    <div class="drawer-footer">
      <div style="display: flex; align-items: baseline; justify-content: space-between;">
        <span style="font-size: 12px; color: var(--text-secondary); font-weight: 600;">Estimated Indicative
          Value:</span>
        <span class="price-unit-large" id="rfq-estimated-total" style="font-size: 20px;">€53,000</span>
      </div>

      <button class="btn-primary-amber" style="width: 100%; height: 42px;" onclick="window.shopApp.submitOfficialRfq()">
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
        style="font-size: 12px; font-weight: 700; color: var(--secondary-brand); text-transform: uppercase; letter-spacing: 0.5px;">
        Procurement Line Items
      </div>

      <div class="drawer-items-list" id="cart-items-list">
        <!-- Dynamically populated -->
      </div>

      <div
        style="display: flex; flex-direction: column; gap: 8px; border-top: 1px solid var(--border-light); padding-top: 14px;">
        <label style="font-size: 12px; font-weight: 600; color: var(--text-primary);">Corporate PO Number
          (Required):</label>
        <input type="text" id="po-number-input" value="PO-TPC-2026-8801"
          style="padding: 8px; border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); font-family: var(--font-mono); font-size: 12px;">
      </div>

      <div style="display: flex; flex-direction: column; gap: 8px;">
        <label style="font-size: 12px; font-weight: 600; color: var(--text-primary);">Payment Terms:</label>
        <select
          style="padding: 8px; border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); font-size: 12px; background: #FFFFFF;">
          <option value="net30">Net 30 Days (Pre-Approved Tier A Credit)</option>
          <option value="net60">Net 60 Days (Letter of Credit Required)</option>
          <option value="advance">100% Advance Wire (Immediate Dispatch Priority)</option>
        </select>
      </div>
    </div>

    <div class="drawer-footer">
      <div style="display: flex; align-items: baseline; justify-content: space-between;">
        <span style="font-size: 12px; color: var(--text-secondary); font-weight: 600;">Net Subtotal (excl. VAT):</span>
        <span class="price-value" id="cart-subtotal" style="font-size: 16px;">€19,300</span>
      </div>

      <div
        style="display: flex; align-items: baseline; justify-content: space-between; border-top: 1px solid var(--border-light); padding-top: 8px;">
        <span style="font-size: 13px; color: var(--text-primary); font-weight: 700;">Total Purchase Commitment:</span>
        <span class="price-unit-large" id="cart-total" style="font-size: 22px;">€19,300</span>
      </div>

      <button class="btn-primary-amber" style="width: 100%; height: 42px;" onclick="window.shopApp.submitDirectPO()">
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
        <div style="display: flex; align-items: center; gap: 8px;">
          <strong style="color: #FFFFFF;">VOSTOKPRIBOR JSC</strong>
          <span>· System 02: B2B Industrial E-Commerce Platform</span>
        </div>
        <span style="opacity: 0.4;">|</span>
        <span>Almaty, Kazakhstan (Est. 1968)</span>
      </div>

      <div class="footer-links">
        <span>FQDN: <code>shop.vostokpribor.local</code></span>
        <span style="opacity: 0.4;">·</span>
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
</body>

</html>