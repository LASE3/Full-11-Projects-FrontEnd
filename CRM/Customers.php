<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('CRM');
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
                <span style="opacity: 0.5;">|</span>
                <span>CUSTOMER DIRECTORY</span>
              </div>
            </div>
          </a>
        </div>

        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search enterprise clients (e.g. Severstal, NLMK, Norilsk)..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <div class="top-nav__actions">
          <div class="pipeline-sync-badge">
            <span style="color: #2ECC71;">●</span>
            <span>Sync: <strong>Active 99.98%</strong></span>
          </div>
          <button class="btn btn-primary-amber btn-sm" onclick="window.location.href='CustomerDetail.php'">
            <span>Inspect Key Client</span>
          </button>
          <button class="icon-button" title="Telemetry" onclick="window.crmApp.showToast('Account Updates', 'Severstal telemetry nodes reporting 100% FAT uptime.')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="badge-dot"></span>
          </button>
          <div class="top-user-profile" onclick="window.crmApp.showToast('User Clearance', 'Mikhail Sorokin · Level 4 Clearance')">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" alt="Mikhail Sorokin" class="user-avatar-top" />
            <div class="user-details-top">
              <span class="user-name-top">Mikhail Sorokin</span>
              <span class="user-role-top">VP Enterprise Sales</span>
            </div>
          </div>
        </div>
      
<!-- Top Bar Sign Out -->
<a href="../api/logout.php?system=CRM&redirect=../CRM/login.php" class="top-signout-btn" title="Sign Out of CRM" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:4px;background:rgba(178,58,50,0.2);border:1px solid rgba(178,58,50,0.5);color:#FF8080;font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;margin-left:8px;vertical-align:middle;transition:all 0.2s;" onmouseover="this.style.background='rgba(178,58,50,0.4)';this.style.color='#FFFFFF'" onmouseout="this.style.background='rgba(178,58,50,0.2)';this.style.color='#FF8080'"><span class="material-symbols-outlined" style="font-size:15px;line-height:1;">logout</span><span>Sign Out</span></a>
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
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                </span>
                <span>Dashboard</span>
              </div>
            </a>

            <a href="Leads.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </span>
                <span>Leads</span>
              </div>
              <span class="sidebar-badge">28</span>
            </a>

            <!-- Customers (Active) -->
            <a href="Customers.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9h1"/><path d="M9 13h1"/><path d="M9 17h1"/></svg>
                </span>
                <span>Customers</span>
              </div>
              <span class="sidebar-badge">14</span>
            </a>

            <a href="Opportunities.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
                </span>
                <span>Opportunities</span>
              </div>
              <span class="sidebar-badge">42</span>
            </a>

            <a href="QuotesAndContracts.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </span>
                <span>Quotes &amp; Contracts</span>
              </div>
              <span class="sidebar-badge">19</span>
            </a>

            <a href="Projects.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                </span>
                <span>Projects</span>
              </div>
              <span class="sidebar-badge">14</span>
            </a>

            <a href="SalesForecast.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                </span>
                <span>Sales Forecast</span>
              </div>
              <span class="sidebar-badge badge-green">+14%</span>
            </a>
          </nav>
        </div>

        
                      <div class="sidebar-section-title" style="margin-top: 1rem;">Unified Ecosystem</div>
          <nav class="sidebar-nav" style="margin-bottom: 0.5rem;">
            <a href="../VOSTOKPRIBOR Corporate Web Platform/index.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                </span>
                <span>Corporate Platform</span>
              </div>
              <span class="sidebar-badge" style="font-size: 10px;">SYS 01</span>
            </a>
            <a href="../Employee Intranet/index.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                </span>
                <span>Employee Intranet</span>
              </div>
              <span class="sidebar-badge" style="font-size: 10px;">SYS 04</span>
            </a>
          </nav>
            <!-- Log Out -->
            
        <div class="sidebar-footer">
          <div class="quota-widget-card">
            <div class="quota-widget-header">
              <span>FY2024 Q4 Quota Target</span>
              <strong style="color: var(--crm-amber);">82%</strong>
            </div>
            <div class="quota-progress-track">
              <div class="quota-progress-bar" style="width: 82%;"></div>
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
                <span>📥 Export Client Matrix</span>
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
                <div class="kpi-icon-pill indigo">🏢</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">14</span>
                <span style="font-size: 12px; color: var(--crm-text-muted);">Enterprise</span>
              </div>
              <div class="kpi-footer">
                <span>38 Industrial Sites</span>
                <span class="kpi-trend up">100% Active</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Total Portfolio ARR</span>
                <div class="kpi-icon-pill amber">💼</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">$48.2M</span>
                <span style="font-size: 12px; color: var(--crm-text-muted);">USD</span>
              </div>
              <div class="kpi-footer">
                <span>Avg ARR: <strong>$3.44M</strong></span>
                <span class="kpi-trend up">▲ +16.2% YoY</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Contract Retention</span>
                <div class="kpi-icon-pill success">🛡️</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">99.4%</span>
                <span style="font-size: 12px; color: var(--crm-text-muted);">Renewal</span>
              </div>
              <div class="kpi-footer">
                <span>Zero Churn (36M)</span>
                <span class="kpi-trend up">Top Tier</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Open Expansion Opps</span>
                <div class="kpi-icon-pill steel">📈</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">42</span>
                <span style="font-size: 12px; color: var(--crm-text-muted);">In Pipeline</span>
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
              <button class="filter-pill-btn active">All Sectors (14)</button>
              <button class="filter-pill-btn">Ferrous Metallurgy (6)</button>
              <button class="filter-pill-btn">Non-Ferrous &amp; Mining (3)</button>
              <button class="filter-pill-btn">Chemical &amp; Agro (3)</button>
              <button class="filter-pill-btn">Oil &amp; Gas (2)</button>
            </div>
            <div style="display: flex; gap: 0.5rem;">
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
              <tbody>
                <!-- Customer 1 -->
                <tr class="account-row account-row-tagged" onclick="window.location.href='CustomerDetail.php'">
                  <td>
                    <div class="account-name-cell">
                      <span class="account-name-title" style="color: var(--crm-indigo);">Severstal Metallurgy PJSC</span>
                      <span class="account-name-sub">Account ID: #VP-90214 · Cherepovets Metallurgical Plant</span>
                    </div>
                  </td>
                  <td>
                    <span class="tier-badge strategic">Ferrous Metallurgy</span>
                  </td>
                  <td>
                    <strong>Dr. Elena Rostova</strong>
                    <div style="font-size: 11px; color: var(--crm-text-muted);">Senior Sales Director</div>
                  </td>
                  <td>
                    <strong style="font-family: var(--crm-font-mono); font-size: 14px; color: var(--crm-navy);">$6,850,000.00</strong>
                    <div style="font-size: 11px; color: var(--crm-success);">+18.5% YoY Expansion</div>
                  </td>
                  <td>
                    <span style="font-family: var(--crm-font-mono); font-size: 11px; font-weight: 600;">MSA-2024-SVST-088</span>
                    <div style="font-size: 10px; color: var(--crm-text-muted);">Valid thru Dec 2026</div>
                  </td>
                  <td>
                    <span style="font-family: var(--crm-font-mono); font-weight: 700; color: var(--crm-success);">98% (Optimal)</span>
                  </td>
                  <td>
                    <a href="CustomerDetail.php" class="btn btn-indigo btn-sm">Full Profile →</a>
                  </td>
                </tr>

                <!-- Customer 2 -->
                <tr class="account-row account-row-tagged" onclick="window.location.href='CustomerDetail.php'">
                  <td>
                    <div class="account-name-cell">
                      <span class="account-name-title" style="color: var(--crm-indigo);">Norilsk Nickel Mining</span>
                      <span class="account-name-sub">Account ID: #VP-66102 · Talnakh Concentrator Division</span>
                    </div>
                  </td>
                  <td>
                    <span class="tier-badge strategic">Non-Ferrous Mining</span>
                  </td>
                  <td>
                    <strong>Mikhail Sorokin</strong>
                    <div style="font-size: 11px; color: var(--crm-text-muted);">VP Enterprise Sales</div>
                  </td>
                  <td>
                    <strong style="font-family: var(--crm-font-mono); font-size: 14px; color: var(--crm-navy);">$8,400,000.00</strong>
                    <div style="font-size: 11px; color: var(--crm-success);">+24.0% YoY Expansion</div>
                  </td>
                  <td>
                    <span style="font-family: var(--crm-font-mono); font-size: 11px; font-weight: 600;">MSA-2023-NN-014</span>
                    <div style="font-size: 10px; color: var(--crm-text-muted);">Valid thru Nov 2025</div>
                  </td>
                  <td>
                    <span style="font-family: var(--crm-font-mono); font-weight: 700; color: var(--crm-success);">95% (Optimal)</span>
                  </td>
                  <td>
                    <a href="CustomerDetail.php" class="btn btn-indigo btn-sm">Full Profile →</a>
                  </td>
                </tr>

                <!-- Customer 3 -->
                <tr class="account-row account-row-tagged" onclick="window.location.href='CustomerDetail.php'">
                  <td>
                    <div class="account-name-cell">
                      <span class="account-name-title" style="color: var(--crm-indigo);">NLMK Group Lipetsk</span>
                      <span class="account-name-sub">Account ID: #VP-88412 · Blast Furnace &amp; Strip Mill</span>
                    </div>
                  </td>
                  <td>
                    <span class="tier-badge tier-1">Ferrous Metallurgy</span>
                  </td>
                  <td>
                    <strong>Viktor Morozov</strong>
                    <div style="font-size: 11px; color: var(--crm-text-muted);">Key Account Lead</div>
                  </td>
                  <td>
                    <strong style="font-family: var(--crm-font-mono); font-size: 14px; color: var(--crm-navy);">$4,200,000.00</strong>
                    <div style="font-size: 11px; color: var(--crm-text-muted);">Stable Baseline</div>
                  </td>
                  <td>
                    <span style="font-family: var(--crm-font-mono); font-size: 11px; font-weight: 600;">MSA-2024-NLMK-90</span>
                    <div style="font-size: 10px; color: var(--crm-text-muted);">Valid thru Oct 2026</div>
                  </td>
                  <td>
                    <span style="font-family: var(--crm-font-mono); font-weight: 700; color: var(--crm-success);">92% (Good)</span>
                  </td>
                  <td>
                    <a href="CustomerDetail.php" class="btn btn-indigo btn-sm">Full Profile →</a>
                  </td>
                </tr>

                <!-- Customer 4 -->
                <tr class="account-row account-row-tagged" onclick="window.location.href='CustomerDetail.php'">
                  <td>
                    <div class="account-name-cell">
                      <span class="account-name-title" style="color: var(--crm-indigo);">EVRAZ Consolidated</span>
                      <span class="account-name-sub">Account ID: #VP-77190 · Nizhny Tagil Plant</span>
                    </div>
                  </td>
                  <td>
                    <span class="tier-badge tier-2">Heavy Metallurgy</span>
                  </td>
                  <td>
                    <strong>Denis Sokolov</strong>
                    <div style="font-size: 11px; color: var(--crm-text-muted);">Technical Sales Eng.</div>
                  </td>
                  <td>
                    <strong style="font-family: var(--crm-font-mono); font-size: 14px; color: var(--crm-navy);">$2,100,000.00</strong>
                    <div style="font-size: 11px; color: var(--crm-success);">+8.0% YoY</div>
                  </td>
                  <td>
                    <span style="font-family: var(--crm-font-mono); font-size: 11px; font-weight: 600;">MSA-2022-EVR-05</span>
                    <div style="font-size: 10px; color: var(--crm-text-muted);">Valid thru Jan 2025</div>
                  </td>
                  <td>
                    <span style="font-family: var(--crm-font-mono); font-weight: 700; color: var(--crm-amber-hover);">88% (Normal)</span>
                  </td>
                  <td>
                    <a href="CustomerDetail.php" class="btn btn-indigo btn-sm">Full Profile →</a>
                  </td>
                </tr>
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
