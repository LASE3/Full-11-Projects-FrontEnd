<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('FIN');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR Finance · Project Billing Overview</title>
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
                <span>FINANCIAL OPERATIONS</span>
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

          <button class="icon-button" onclick="window.finApp.showToast('Milestone Alert', 'Next billing milestone for Severstal BF #5 approaches Nov 30.')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="badge-dot"></span>
          </button>

          <div class="top-user-profile" onclick="window.finApp.showToast('Active Financial Controller', 'Mikhail Sorokin · Chief Financial Controller')">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" alt="Controller" class="user-avatar-top" />
            <div class="user-details-top">
              <span class="user-name-top">Mikhail Sorokin</span>
              <span class="user-role-top">Chief Financial Controller</span>
            </div>
          </div>
        </div>
      
<!-- Top Bar Sign Out -->
<a href="../api/logout.php?system=Finance%20%26%20Billing&redirect=../Finance%20%26%20Billing/login.php" class="top-signout-btn" title="Sign Out of Finance &amp; Billing" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:4px;background:rgba(178,58,50,0.2);border:1px solid rgba(178,58,50,0.5);color:#FF8080;font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;margin-left:8px;vertical-align:middle;transition:all 0.2s;" onmouseover="this.style.background='rgba(178,58,50,0.4)';this.style.color='#FFFFFF'" onmouseout="this.style.background='rgba(178,58,50,0.2)';this.style.color='#FF8080'"><span class="material-symbols-outlined" style="font-size:15px;line-height:1;">logout</span><span>Sign Out</span></a>
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
              <span class="sidebar-badge badge-amber">48</span>
            </a>
            <a href="PaymentsReconciliation.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span>
                <span>Payments &amp; Reconciliation</span>
              </div>
              <span class="sidebar-badge badge-red">6</span>
            </a>
            <a href="ProjectBilling.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg></span>
                <span>Project Billing</span>
              </div>
              <span class="sidebar-badge badge-green">14</span>
            </a>
            <a href="Budgets.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>
                <span>Budgets</span>
              </div>
              <span class="sidebar-badge">91%</span>
            </a>
            <a href="FinancialReports.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg></span>
                <span>Financial Reports</span>
              </div>
              <span class="sidebar-badge">Q4</span>
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
          <div class="security-widget-card">
            <div class="security-widget-header">
              <span>Financial Ledger Security</span>
              <span class="security-badge-status">● VERIFIED</span>
            </div>
            <div style="font-size: 11px; color: var(--fin-text-inverse-muted); margin-top: 2px;">
              Bank Accounts &amp; SPFS: <strong>100% Synced</strong>
            </div>
          </div>
        </div>
      </aside>

      <!-- ========================================================================
           MAIN CONTENT AREA: SCREEN 3 PROJECT BILLING OVERVIEW
           ======================================================================== -->
      <main class="content-wrapper">
        <div class="portal-container">
          <!-- Page Header -->
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <a href="Dashboard.php">Finance &amp; Billing</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Project Billing Overview</span>
              </div>
              <h1 class="page-title">Project Billing &amp; Milestone Budget Burn-down</h1>
              <p class="page-subtitle">Track industrial contract budgets, milestone pacing, cumulative billed-to-date, and upcoming milestone dates</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.finApp.showToast('Budget Export', 'Contract burn-down ledger exported to Excel (.XLSX).')">
                <span>📑 Export Milestone Matrix</span>
              </button>
              <button class="btn btn-primary-amber" onclick="window.finApp.showToast('Milestone Billing', 'Select a project row to generate the next milestone bill.')">
                <span>+ Bill Milestone</span>
              </button>
            </div>
          </div>

          <!-- Project Billing Summary KPI Cards -->
          <div class="kpi-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 1.5rem;">
            <div class="fin-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Total Active Contract Value</span>
                <div class="kpi-icon-pill steel">💼</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value">$9,968,200.00</span>
              </div>
              <div class="kpi-footer">
                <span>5 Major Industrial Projects</span>
                <span class="kpi-trend up">14 Sub-Contracts</span>
              </div>
            </div>

            <div class="fin-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Cumulative Billed to Date</span>
                <div class="kpi-icon-pill green">✓</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value">$6,503,200.00</span>
              </div>
              <div class="kpi-footer">
                <span>Overall Pacing: 65.2%</span>
                <span class="kpi-trend up">On Schedule</span>
              </div>
            </div>

            <div class="fin-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Remaining Milestone Pipeline</span>
                <div class="kpi-icon-pill amber">⏳</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value">$3,465,000.00</span>
              </div>
              <div class="kpi-footer">
                <span>To be billed Q4 2024 / Q1 2025</span>
                <span class="kpi-trend up">Secured by PO</span>
              </div>
            </div>
          </div>

          <!-- Project Billing Table Matrix -->
          <div class="fin-card" style="padding: 0; overflow: hidden;">
            <div style="padding: 1rem 1.25rem; background-color: #FFFFFF; border-bottom: 1px solid var(--fin-surface-border); display: flex; align-items: center; justify-content: space-between;">
              <div>
                <h3 class="card-title">Active Heavy Industry Engineering Projects &amp; Milestone Progress</h3>
                <p style="font-size: 11.5px; color: var(--fin-text-secondary); margin-top: 2px;">
                  Progress bar shows total billed vs contracted ceiling with upcoming milestone dates
                </p>
              </div>
              <span class="confidential-system-pill" style="font-size: 9.5px; padding: 2px 6px;">
                CONTRACT COMPLIANCE: 100%
              </span>
            </div>

            <table class="fin-table">
              <thead>
                <tr>
                  <th style="width: 140px;">Project ID</th>
                  <th>Customer &amp; Project Name</th>
                  <th style="width: 130px; text-align: right;">Total Budget</th>
                  <th style="width: 130px; text-align: right;">Billed to Date</th>
                  <th style="width: 130px; text-align: right;">Remaining</th>
                  <th style="width: 240px;">Billed vs Budget (Progress)</th>
                  <th style="width: 200px;">Next Milestone Date</th>
                  <th style="width: 110px; text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody>
                <!-- Project 1: Severstal -->
                <tr class="fin-table-row">
                  <td>
                    <span style="font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-green); font-size: 12px;">PRJ-VP-7721</span>
                  </td>
                  <td>
                    <div style="font-weight: 700; color: var(--fin-navy);">Blast Furnace #5 Automation &amp; Gas Analysis</div>
                    <div style="font-size: 11px; color: var(--fin-text-muted);">Severstal Metallurgy PJSC · Cherepovets Bay</div>
                  </td>
                  <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-navy);">
                    $2,850,000.00
                  </td>
                  <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-green);">
                    $1,995,000.00
                  </td>
                  <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 600; color: var(--fin-text-secondary);">
                    $855,000.00
                  </td>
                  <td>
                    <!-- Horizontal Progress Bar Visualizing Billed-vs-Total in Deep Green -->
                    <div class="budget-progress-container">
                      <div class="budget-progress-bar">
                        <div class="budget-progress-fill" style="width: 70%;"></div>
                      </div>
                      <div class="budget-progress-meta">
                        <span>70.0% Billed</span>
                        <span>$855K Left</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <!-- Next Billing Milestone Date -->
                    <div style="font-family: var(--fin-font-mono); font-size: 11.5px; font-weight: 700; color: var(--fin-navy);">
                      Nov 30, 2024
                    </div>
                    <div style="font-size: 10.5px; color: var(--fin-text-muted);">
                      Milestone 4: FAT Hot Calibration ($855K)
                    </div>
                  </td>
                  <td style="text-align: right;">
                    <button class="btn btn-primary-amber btn-sm" onclick="window.finApp.showToast('Milestone Invoice', 'Generated $855,000 milestone invoice for PRJ-VP-7721.', 'green')">
                      Bill →
                    </button>
                  </td>
                </tr>

                <!-- Project 2: NLMK -->
                <tr class="fin-table-row">
                  <td>
                    <span style="font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-green); font-size: 12px;">PRJ-VP-7722</span>
                  </td>
                  <td>
                    <div style="font-weight: 700; color: var(--fin-navy);">Coke Oven Battery Temperature Profiling</div>
                    <div style="font-size: 11px; color: var(--fin-text-muted);">NLMK Group Lipetsk · Battery #3 Integration</div>
                  </td>
                  <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-navy);">
                    $1,450,000.00
                  </td>
                  <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-green);">
                    $920,000.00
                  </td>
                  <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 600; color: var(--fin-text-secondary);">
                    $530,000.00
                  </td>
                  <td>
                    <div class="budget-progress-container">
                      <div class="budget-progress-bar">
                        <div class="budget-progress-fill" style="width: 63.4%;"></div>
                      </div>
                      <div class="budget-progress-meta">
                        <span>63.4% Billed</span>
                        <span>$530K Left</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div style="font-family: var(--fin-font-mono); font-size: 11.5px; font-weight: 700; color: var(--fin-navy);">
                      Dec 15, 2024
                    </div>
                    <div style="font-size: 10.5px; color: var(--fin-text-muted);">
                      Milestone 3: Final Acceptance ($530K)
                    </div>
                  </td>
                  <td style="text-align: right;">
                    <button class="btn btn-outline btn-sm" onclick="window.finApp.showToast('Milestone Notice', 'Milestone 3 requires FAT verification sign-off prior to billing.')">
                      Pending
                    </button>
                  </td>
                </tr>

                <!-- Project 3: Norilsk Nickel -->
                <tr class="fin-table-row">
                  <td>
                    <span style="font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-green); font-size: 12px;">PRJ-VP-7723</span>
                  </td>
                  <td>
                    <div style="font-weight: 700; color: var(--fin-navy);">Talnakh Concentrator Flotation Telemetry Grid</div>
                    <div style="font-size: 11px; color: var(--fin-text-muted);">Norilsk Nickel Mining · Arctic Concentrator</div>
                  </td>
                  <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-navy);">
                    $3,600,000.00
                  </td>
                  <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-green);">
                    $2,400,000.00
                  </td>
                  <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 600; color: var(--fin-text-secondary);">
                    $1,200,000.00
                  </td>
                  <td>
                    <div class="budget-progress-container">
                      <div class="budget-progress-bar">
                        <div class="budget-progress-fill" style="width: 66.7%;"></div>
                      </div>
                      <div class="budget-progress-meta">
                        <span>66.7% Billed</span>
                        <span>$1.20M Left</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div style="font-family: var(--fin-font-mono); font-size: 11.5px; font-weight: 700; color: var(--fin-navy);">
                      Jan 20, 2025
                    </div>
                    <div style="font-size: 10.5px; color: var(--fin-text-muted);">
                      Milestone 4: Winter Cert ($1.20M)
                    </div>
                  </td>
                  <td style="text-align: right;">
                    <button class="btn btn-outline btn-sm" onclick="window.finApp.showToast('Schedule Update', 'Arctic weather telemetry confirmed for Jan 2025.')">
                      Details
                    </button>
                  </td>
                </tr>

                <!-- Project 4: EVRAZ -->
                <tr class="fin-table-row">
                  <td>
                    <span style="font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-green); font-size: 12px;">PRJ-VP-7724</span>
                  </td>
                  <td>
                    <div style="font-weight: 700; color: var(--fin-navy);">Rail Mill Laser Profiler &amp; Flaw Detection</div>
                    <div style="font-size: 11px; color: var(--fin-text-muted);">EVRAZ Consolidated · Nizhny Tagil Plant</div>
                  </td>
                  <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-navy);">
                    $1,650,000.00
                  </td>
                  <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-green);">
                    $770,000.00
                  </td>
                  <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 600; color: var(--fin-text-secondary);">
                    $880,000.00
                  </td>
                  <td>
                    <div class="budget-progress-container">
                      <div class="budget-progress-bar">
                        <div class="budget-progress-fill" style="width: 46.7%;"></div>
                      </div>
                      <div class="budget-progress-meta">
                        <span>46.7% Billed</span>
                        <span>$880K Left</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div style="font-family: var(--fin-font-mono); font-size: 11.5px; font-weight: 700; color: var(--fin-confidential);">
                      Dec 05, 2024
                    </div>
                    <div style="font-size: 10.5px; color: var(--fin-text-muted);">
                      Milestone 3: Profiler Delivery ($880K)
                    </div>
                  </td>
                  <td style="text-align: right;">
                    <button class="btn btn-outline btn-sm" onclick="window.finApp.showToast('Overdue Flag', 'Previous milestone invoice INV-2024-8844 ($385K) is overdue.')">
                      Review
                    </button>
                  </td>
                </tr>

                <!-- Project 5: PhosAgro -->
                <tr class="fin-table-row">
                  <td>
                    <span style="font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-green); font-size: 12px;">PRJ-VP-7725</span>
                  </td>
                  <td>
                    <div style="font-weight: 700; color: var(--fin-navy);">High-Pressure Flowmeter HPF-900X Replacement</div>
                    <div style="font-size: 11px; color: var(--fin-text-muted);">PhosAgro Chemical · Cherepovets Agro Complex</div>
                  </td>
                  <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-navy);">
                    $418,200.00
                  </td>
                  <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-green);">
                    $418,200.00
                  </td>
                  <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 600; color: var(--fin-text-secondary);">
                    $0.00
                  </td>
                  <td>
                    <div class="budget-progress-container">
                      <div class="budget-progress-bar">
                        <div class="budget-progress-fill" style="width: 100%;"></div>
                      </div>
                      <div class="budget-progress-meta">
                        <span>100.0% Complete</span>
                        <span>$0 Left</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div style="font-family: var(--fin-font-mono); font-size: 11.5px; font-weight: 700; color: var(--fin-green);">
                      Completed
                    </div>
                    <div style="font-size: 10.5px; color: var(--fin-text-muted);">
                      Final Supply Delivery Settled
                    </div>
                  </td>
                  <td style="text-align: right;">
                    <span class="status-badge-paid" style="font-size: 10px;">Archived ✓</span>
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
