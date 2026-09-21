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
  <title>VOSTOKPRIBOR CRM · Industrial Engineering Projects Tracker</title>
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
                <span>PROJECTS TRACKER</span>
              </div>
            </div>
          </a>
        </div>

        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search engineering projects (e.g. PRJ-VP-7721, Blast Furnace)..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <div class="top-nav__actions">
          <div class="pipeline-sync-badge">
            <span style="color: #2ECC71;">●</span>
            <span>Sync: <strong>Active 99.98%</strong></span>
          </div>
          <button class="btn btn-primary-amber btn-sm" onclick="window.crmApp.showToast('Milestone Logged', 'Milestone FAT sign-off logged to engineering ledger.')">
            <span>+ Log Milestone</span>
          </button>
          <button class="icon-button" title="Telemetry" onclick="window.crmApp.showToast('FAT Telemetry', 'Blast Furnace #5 instrumentation array passed Phase 2 FAT test.')">
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

            <!-- Projects (Active) -->
            <a href="Projects.php" class="sidebar-nav-item active">
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
                <span>Engineering Execution</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Active Commissioning Matrix</span>
              </div>
              <h1 class="page-title">Industrial Projects &amp; Commissioning Matrix</h1>
              <p class="page-subtitle">Field telemetry deployments, FAT milestone tracking, and SCADA gateway integrations</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.crmApp.showToast('Gantt Export', 'Commissioning Gantt trajectory exported to PDF.')">
                <span>📊 Export Gantt Schedule</span>
              </button>
              <button class="btn btn-primary-amber" onclick="window.crmApp.openModal('modal-new-opportunity')">
                <span>+ New Project Opp</span>
              </button>
            </div>
          </div>

          <!-- KPI Strip -->
          <div class="kpi-grid">
            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Active Projects</span>
                <div class="kpi-icon-pill indigo">⚙️</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">14</span>
                <span style="font-size: 12px; color: var(--crm-text-muted);">Industrial</span>
              </div>
              <div class="kpi-footer">
                <span>6 Facilities on-site</span>
                <span class="kpi-trend up">100% On-Track</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Committed Budget</span>
                <div class="kpi-icon-pill steel">💼</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">$18.45M</span>
                <span style="font-size: 12px; color: var(--crm-text-muted);">USD</span>
              </div>
              <div class="kpi-footer">
                <span>Disbursed: <strong>$11.8M</strong></span>
                <span class="kpi-trend up">64% Settled</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">FAT Milestones Met</span>
                <div class="kpi-icon-pill success">✓</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">94.8%</span>
                <span style="font-size: 12px; color: var(--crm-text-muted);">FAT Schedule</span>
              </div>
              <div class="kpi-footer">
                <span>18 / 19 Milestones</span>
                <span class="kpi-trend up">Top Tier</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Commissioning Q4</span>
                <div class="kpi-icon-pill amber">🚀</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">4</span>
                <span style="font-size: 12px; color: var(--crm-text-muted);">Go-Lives</span>
              </div>
              <div class="kpi-footer">
                <span>Nov &amp; Dec 2024</span>
                <span class="kpi-trend amber">Final Stage</span>
              </div>
            </div>
          </div>

          <!-- Projects Matrix Table (Tagged with #D9822B) -->
          <div class="crm-card">
            <div class="card-header-row">
              <div>
                <h3 class="card-title">Active Field Engineering Deployments</h3>
                <p style="font-size: 11.5px; color: var(--crm-text-secondary); margin-top: 2px;">
                  All data tagged with industrial classification tags. Click "Inspect Blueprint" to review SCADA schemas.
                </p>
              </div>
            </div>

            <table class="accounts-table">
              <thead>
                <tr>
                  <th>Project ID</th>
                  <th>Enterprise Plant &amp; Scope</th>
                  <th>Deployment Progress</th>
                  <th>Total Budget</th>
                  <th>Lead Field Engineer</th>
                  <th>Target Commissioning</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <!-- Project 1 -->
                <tr class="account-row account-row-tagged">
                  <td>
                    <strong style="font-family: var(--crm-font-mono); font-size: 13px; color: var(--crm-indigo);">PRJ-VP-7721</strong>
                  </td>
                  <td>
                    <strong>Severstal Metallurgy PJSC</strong>
                    <div style="font-size: 11.5px; color: var(--crm-text-secondary);">Blast Furnace #5 Automation &amp; Gas Analysis Instrumentation (Cherepovets)</div>
                  </td>
                  <td>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                      <div class="funnel-bar-track" style="flex: 1; height: 8px;">
                        <div class="funnel-bar-fill" style="width: 72%; background: var(--crm-amber);"></div>
                      </div>
                      <span style="font-family: var(--crm-font-mono); font-weight: 700; font-size: 11px;">72%</span>
                    </div>
                  </td>
                  <td>
                    <strong style="font-family: var(--crm-font-mono); font-size: 13.5px; color: var(--crm-navy);">$1,850,000.00</strong>
                  </td>
                  <td>Dr. Elena Rostova</td>
                  <td><span style="font-family: var(--crm-font-mono); font-size: 11px; font-weight: 600;">Nov 28, 2024</span></td>
                  <td>
                    <button class="btn btn-outline btn-sm" onclick="window.location.href='CustomerDetail.php'">Inspect →</button>
                  </td>
                </tr>

                <!-- Project 2 -->
                <tr class="account-row account-row-tagged">
                  <td>
                    <strong style="font-family: var(--crm-font-mono); font-size: 13px; color: var(--crm-indigo);">PRJ-VP-7804</strong>
                  </td>
                  <td>
                    <strong>Severstal Hot Strip Mill #2</strong>
                    <div style="font-size: 11.5px; color: var(--crm-text-secondary);">Hydraulic Pressure Sensor Telemetry Retrofit</div>
                  </td>
                  <td>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                      <div class="funnel-bar-track" style="flex: 1; height: 8px;">
                        <div class="funnel-bar-fill" style="width: 45%; background: var(--crm-info);"></div>
                      </div>
                      <span style="font-family: var(--crm-font-mono); font-weight: 700; font-size: 11px;">45%</span>
                    </div>
                  </td>
                  <td>
                    <strong style="font-family: var(--crm-font-mono); font-size: 13.5px; color: var(--crm-navy);">$640,000.00</strong>
                  </td>
                  <td>Viktor Morozov</td>
                  <td><span style="font-family: var(--crm-font-mono); font-size: 11px; font-weight: 600;">Dec 20, 2024</span></td>
                  <td>
                    <button class="btn btn-outline btn-sm" onclick="window.location.href='CustomerDetail.php'">Inspect →</button>
                  </td>
                </tr>

                <!-- Project 3 -->
                <tr class="account-row account-row-tagged">
                  <td>
                    <strong style="font-family: var(--crm-font-mono); font-size: 13px; color: var(--crm-indigo);">PRJ-VP-6610</strong>
                  </td>
                  <td>
                    <strong>Norilsk Nickel Mining (Talnakh)</strong>
                    <div style="font-size: 11.5px; color: var(--crm-text-secondary);">Talnakh Concentrator Flotation Telemetry Grid</div>
                  </td>
                  <td>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                      <div class="funnel-bar-track" style="flex: 1; height: 8px;">
                        <div class="funnel-bar-fill" style="width: 80%; background: var(--crm-indigo);"></div>
                      </div>
                      <span style="font-family: var(--crm-font-mono); font-weight: 700; font-size: 11px;">80%</span>
                    </div>
                  </td>
                  <td>
                    <strong style="font-family: var(--crm-font-mono); font-size: 13.5px; color: var(--crm-navy);">$2,400,000.00</strong>
                  </td>
                  <td>Mikhail Sorokin</td>
                  <td><span style="font-family: var(--crm-font-mono); font-size: 11px; font-weight: 600;">Dec 18, 2024</span></td>
                  <td>
                    <button class="btn btn-outline btn-sm" onclick="window.crmApp.showToast('Project View', 'Opening Norilsk Nickel Talnakh Blueprint.')">Inspect →</button>
                  </td>
                </tr>

                <!-- Project 4 -->
                <tr class="account-row account-row-tagged">
                  <td>
                    <strong style="font-family: var(--crm-font-mono); font-size: 13px; color: var(--crm-indigo);">PRJ-VP-8902</strong>
                  </td>
                  <td>
                    <strong>Severstal Casting Bay #3</strong>
                    <div style="font-size: 11.5px; color: var(--crm-text-secondary);">Continuous Casting Machine #3 Optical Thickness Gauges</div>
                  </td>
                  <td>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                      <div class="funnel-bar-track" style="flex: 1; height: 8px;">
                        <div class="funnel-bar-fill" style="width: 18%; background: var(--crm-purple);"></div>
                      </div>
                      <span style="font-family: var(--crm-font-mono); font-weight: 700; font-size: 11px;">18%</span>
                    </div>
                  </td>
                  <td>
                    <strong style="font-family: var(--crm-font-mono); font-size: 13.5px; color: var(--crm-navy);">$410,000.00</strong>
                  </td>
                  <td>Anna Belova</td>
                  <td><span style="font-family: var(--crm-font-mono); font-size: 11px; font-weight: 600;">Feb 15, 2025</span></td>
                  <td>
                    <button class="btn btn-outline btn-sm" onclick="window.location.href='CustomerDetail.php'">Inspect →</button>
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
