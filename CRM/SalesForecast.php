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
  <title>VOSTOKPRIBOR CRM · Sales Forecast &amp; Telemetry</title>
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
                <span>REVENUE FORECAST</span>
              </div>
            </div>
          </a>
        </div>

        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search revenue models, quota metrics, forecasts..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <div class="top-nav__actions">
          <div class="pipeline-sync-badge">
            <span style="color: #2ECC71;">●</span>
            <span>Sync: <strong>Active 99.98%</strong></span>
          </div>
          <button class="btn btn-primary-amber btn-sm" onclick="window.crmApp.showToast('Model Recalculated', 'Monte Carlo revenue simulation converged at $22.50M.')">
            <span>⚡ Run Forecast Model</span>
          </button>
          <button class="icon-button" title="Telemetry" onclick="window.crmApp.showToast('Forecast Telemetry', 'Q4 weighted close probability increased by +2.4%.')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="badge-dot"></span>
          </button>
          <div class="top-user-profile" onclick="window.crmApp.showToast('User Clearance', 'Mikhail Sorokin · Level 4 Authorization')">
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

            <a href="Customers.php" class="sidebar-nav-item">
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

            <!-- Sales Forecast (Active) -->
            <a href="SalesForecast.php" class="sidebar-nav-item active">
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
                <span>Executive Analytics</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Revenue &amp; Quota Forecasting</span>
              </div>
              <h1 class="page-title">Enterprise Sales Forecast &amp; Quota Modeling</h1>
              <p class="page-subtitle">Predictive pipeline velocity, rep quota pacing, and quarterly revenue projections</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.crmApp.showToast('Model Export', 'Financial model exported as spreadsheet .XLSX.')">
                <span>📥 Export Financial Model</span>
              </button>
              <button class="btn btn-primary-amber" onclick="window.crmApp.openModal('modal-new-opportunity')">
                <span>+ New Opportunity</span>
              </button>
            </div>
          </div>

          <!-- KPI Strip -->
          <div class="kpi-grid">
            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Q4 Projected Close</span>
                <div class="kpi-icon-pill success">💰</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">$22.50M</span>
                <span style="font-size: 12px; color: var(--crm-text-muted);">USD</span>
              </div>
              <div class="kpi-footer">
                <span>Target: $22.50M</span>
                <span class="kpi-trend up">▲ +14% QoQ</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Quota Pacing Rate</span>
                <div class="kpi-icon-pill amber">⚡</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">82.0%</span>
                <span style="font-size: 12px; color: var(--crm-text-muted);">Achieved</span>
              </div>
              <div class="kpi-footer">
                <span>Current: <strong>$18.45M</strong></span>
                <span class="kpi-trend up">On Schedule</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Weighted Expected Value</span>
                <div class="kpi-icon-pill indigo">📊</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">$12.80M</span>
                <span style="font-size: 12px; color: var(--crm-text-muted);">Probability</span>
              </div>
              <div class="kpi-footer">
                <span>Unweighted: $18.45M</span>
                <span class="kpi-trend up">High Confidence</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Target Gross Margin</span>
                <div class="kpi-icon-pill steel">📈</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">44.2%</span>
                <span style="font-size: 12px; color: var(--crm-text-muted);">Enterprise</span>
              </div>
              <div class="kpi-footer">
                <span>Baseline: 40.0%</span>
                <span class="kpi-trend up">▲ +4.2%</span>
              </div>
            </div>
          </div>

          <!-- Scenario Modeling & Quarterly Trajectory -->
          <div class="crm-card" style="margin-bottom: 1.5rem;">
            <div class="card-header-row">
              <div>
                <h3 class="card-title">FY2024 Quarterly Revenue Trajectory &amp; Run-Rate</h3>
                <p style="font-size: 11.5px; color: var(--crm-text-secondary); margin-top: 2px;">
                  Comparison across realized quarters (Q1-Q3) versus active Q4 forecast and Q1 2025 pipeline backlog
                </p>
              </div>
              <div class="filter-pills-group">
                <button class="filter-pill-btn" onclick="window.crmApp.showToast('Scenario Loaded', 'Conservative baseline ($19.8M) rendered.')">Conservative ($19.8M)</button>
                <button class="filter-pill-btn active" onclick="window.crmApp.showToast('Scenario Loaded', 'Base Target scenario ($22.5M) rendered.')">Base Target ($22.5M)</button>
                <button class="filter-pill-btn" onclick="window.crmApp.showToast('Scenario Loaded', 'Optimistic stretch ($26.4M) rendered.')">Optimistic ($26.4M)</button>
              </div>
            </div>

            <!-- Visual Bar Comparison -->
            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-top: 1.25rem;">
              <!-- Q1 -->
              <div style="background: var(--crm-surface-dim); padding: 1rem; border-radius: var(--crm-radius-md); border-top: 3px solid var(--crm-steel-blue);">
                <div style="font-size: 11px; text-transform: uppercase; color: var(--crm-text-muted); font-weight: 700;">Q1 2024 Actual</div>
                <div style="font-family: var(--crm-font-mono); font-size: 20px; font-weight: 700; color: var(--crm-navy); margin: 4px 0;">$16.20M</div>
                <div style="font-size: 11px; color: var(--crm-success);">108% of Plan (Settled)</div>
              </div>

              <!-- Q2 -->
              <div style="background: var(--crm-surface-dim); padding: 1rem; border-radius: var(--crm-radius-md); border-top: 3px solid var(--crm-steel-blue);">
                <div style="font-size: 11px; text-transform: uppercase; color: var(--crm-text-muted); font-weight: 700;">Q2 2024 Actual</div>
                <div style="font-family: var(--crm-font-mono); font-size: 20px; font-weight: 700; color: var(--crm-navy); margin: 4px 0;">$18.10M</div>
                <div style="font-size: 11px; color: var(--crm-success);">102% of Plan (Settled)</div>
              </div>

              <!-- Q3 -->
              <div style="background: var(--crm-surface-dim); padding: 1rem; border-radius: var(--crm-radius-md); border-top: 3px solid var(--crm-steel-blue);">
                <div style="font-size: 11px; text-transform: uppercase; color: var(--crm-text-muted); font-weight: 700;">Q3 2024 Actual</div>
                <div style="font-family: var(--crm-font-mono); font-size: 20px; font-weight: 700; color: var(--crm-navy); margin: 4px 0;">$19.75M</div>
                <div style="font-size: 11px; color: var(--crm-success);">104% of Plan (Settled)</div>
              </div>

              <!-- Q4 (Projected) -->
              <div style="background: #FFF9EE; border: 1px solid rgba(232, 163, 61, 0.3); padding: 1rem; border-radius: var(--crm-radius-md); border-top: 3px solid var(--crm-amber);">
                <div style="font-size: 11px; text-transform: uppercase; color: var(--crm-amber-hover); font-weight: 700;">Q4 2024 (Forecast)</div>
                <div style="font-family: var(--crm-font-mono); font-size: 20px; font-weight: 700; color: var(--crm-navy); margin: 4px 0;">$22.50M</div>
                <div style="font-size: 11px; color: var(--crm-amber-hover); font-weight: 600;">82% Gated ($18.45M)</div>
              </div>

              <!-- Q1 2025 (Pipeline) -->
              <div style="background: var(--crm-surface-dim); padding: 1rem; border-radius: var(--crm-radius-md); border-top: 3px solid var(--crm-indigo);">
                <div style="font-size: 11px; text-transform: uppercase; color: var(--crm-indigo); font-weight: 700;">Q1 2025 (Pipeline)</div>
                <div style="font-family: var(--crm-font-mono); font-size: 20px; font-weight: 700; color: var(--crm-navy); margin: 4px 0;">$25.10M</div>
                <div style="font-size: 11px; color: var(--crm-indigo);">Qualified Backlog</div>
              </div>
            </div>
          </div>

          <!-- Sales Rep Quota Performance Leaderboard -->
          <div class="crm-card">
            <div class="card-header-row">
              <div>
                <h3 class="card-title">Senior Enterprise Sales Directors · Quota Attainment</h3>
                <p style="font-size: 11.5px; color: var(--crm-text-secondary); margin-top: 2px;">
                  Individual quota progress tagged with industrial classification tags
                </p>
              </div>
            </div>

            <table class="accounts-table">
              <thead>
                <tr>
                  <th>Sales Director / Engineer</th>
                  <th>Key Assigned Accounts</th>
                  <th>Closed Q4 ARR</th>
                  <th>Quarterly Target</th>
                  <th>Quota Pacing</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody id="forecast-tbody">
                <tr class="account-row account-row-tagged">
                  <td>
                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                      <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;" alt="Elena" />
                      <div>
                        <strong>Dr. Elena Rostova</strong>
                        <div style="font-size: 11px; color: var(--crm-text-muted);">Lead Technical Director</div>
                      </div>
                    </div>
                  </td>
                  <td>Severstal Metallurgy PJSC, PhosAgro Chemical</td>
                  <td><strong style="font-family: var(--crm-font-mono); color: var(--crm-navy);">$6,850,000.00</strong></td>
                  <td><span style="font-family: var(--crm-font-mono); color: var(--crm-text-muted);">$6,000,000.00</span></td>
                  <td>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                      <div class="funnel-bar-track" style="flex: 1; height: 8px;">
                        <div class="funnel-bar-fill" style="width: 100%; background: var(--crm-success);"></div>
                      </div>
                      <span style="font-family: var(--crm-font-mono); font-weight: 700; color: var(--crm-success);">114%</span>
                    </div>
                  </td>
                  <td><span class="tier-badge" style="background-color: var(--crm-success-light); color: var(--crm-success);">✓ Quota Met</span></td>
                </tr>

                <tr class="account-row account-row-tagged">
                  <td>
                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                      <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;" alt="Mikhail" />
                      <div>
                        <strong>Mikhail Sorokin</strong>
                        <div style="font-size: 11px; color: var(--crm-text-muted);">VP Enterprise Sales</div>
                      </div>
                    </div>
                  </td>
                  <td>Norilsk Nickel Mining, Gazprom Neft Omsk</td>
                  <td><strong style="font-family: var(--crm-font-mono); color: var(--crm-navy);">$8,400,000.00</strong></td>
                  <td><span style="font-family: var(--crm-font-mono); color: var(--crm-text-muted);">$8,000,000.00</span></td>
                  <td>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                      <div class="funnel-bar-track" style="flex: 1; height: 8px;">
                        <div class="funnel-bar-fill" style="width: 100%; background: var(--crm-success);"></div>
                      </div>
                      <span style="font-family: var(--crm-font-mono); font-weight: 700; color: var(--crm-success);">105%</span>
                    </div>
                  </td>
                  <td><span class="tier-badge" style="background-color: var(--crm-success-light); color: var(--crm-success);">✓ Quota Met</span></td>
                </tr>

                <tr class="account-row account-row-tagged">
                  <td>
                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                      <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;" alt="Viktor" />
                      <div>
                        <strong>Viktor Morozov</strong>
                        <div style="font-size: 11px; color: var(--crm-text-muted);">Key Account Lead</div>
                      </div>
                    </div>
                  </td>
                  <td>NLMK Group Lipetsk</td>
                  <td><strong style="font-family: var(--crm-font-mono); color: var(--crm-navy);">$4,200,000.00</strong></td>
                  <td><span style="font-family: var(--crm-font-mono); color: var(--crm-text-muted);">$4,500,000.00</span></td>
                  <td>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                      <div class="funnel-bar-track" style="flex: 1; height: 8px;">
                        <div class="funnel-bar-fill" style="width: 93%; background: var(--crm-amber);"></div>
                      </div>
                      <span style="font-family: var(--crm-font-mono); font-weight: 700; color: var(--crm-amber-hover);">93.3%</span>
                    </div>
                  </td>
                  <td><span class="tier-badge strategic">Pacing Well</span></td>
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
  <link rel="stylesheet" href="../assets/css/api-ui.css">
  <script src="../assets/js/api-client.js"></script>
  <script src="js/crm-data.js"></script>
</body>
</html>
