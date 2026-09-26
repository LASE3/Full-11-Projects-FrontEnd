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
  <title>VOSTOKPRIBOR CRM · Severstal Metallurgy PJSC</title>
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
                <span class="crm-opacity-50" >|</span>
                <span>CUSTOMER ACCOUNT DOSSIER</span>
              </div>
            </div>
          </a>
        </div>

        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search accounts, contracts, projects..." />
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
          <button class="icon-button" title="Telemetry" onclick="window.crmApp.showToast('Telemetry Alert', 'Severstal SCADA node #4 verified encryption handshake.')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
              <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
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
        <a href="../api/logout.php?system=CRM&redirect=../CRM/login.php" class="top-signout-btn" title="Sign Out of CRM" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" ><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
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
          <a href="../Employee Intranet/index.php" class="sidebar-nav-item">
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
          <!-- Breadcrumb & Top Actions -->
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <span>Enterprise CRM</span>
                <span class="breadcrumb-separator">/</span>
                <a class="crm-text-indigo" href="Customers.php" >Customers Directory</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Severstal Metallurgy PJSC (#VP-90214)</span>
              </div>
              <h1 class="page-title">Enterprise Account Dossier</h1>
              <p class="page-subtitle">Strategic Key Account · Unified Telemetry &amp; SLA Contracts Management</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.crmApp.showToast('Dossier Export', 'Severstal account dossier generated with signed audit stamps.')">
                <span>📄 Export Complete Dossier</span>
              </button>
              <button class="btn btn-primary-amber" onclick="window.crmApp.openModal('modal-new-opportunity')">
                <span>+ New Opportunity</span>
              </button>
            </div>
          </div>

          <!-- Customer Header Card (Amber-Orange #D9822B Left Border) -->
          <div class="customer-detail-header-card">
            <div class="customer-header-top-row">
              <div>
                <div class="crm-flex-gap-md" >
                  <h2 class="customer-company-title">Severstal Metallurgy PJSC</h2>
                  <span class="tier-badge strategic">Strategic Tier-1 Account</span>
                  <span class="tier-badge crm-badge-success-outline" >● SLA Active</span>
                </div>
                <div class="customer-meta-pills">
                  <span class="meta-pill">🏭 Sector: Ferrous Metallurgy &amp; Blast Furnace Operations</span>
                  <span class="meta-pill">📍 Headquarters: Cherepovets, Vologda Oblast</span>
                  <span class="meta-pill">🔏 Master Tax Code: INN 3528000597</span>
                  <span class="meta-pill crm-mono" >ID: #VP-90214</span>
                </div>
              </div>
              <div class="crm-text-right" >
                <div class="crm-caption-muted" >Total Active ARR (2024)</div>
                <div class="crm-mono-hero" >$6,850,000.00</div>
                <div class="crm-text-success-bold" >▲ +18.5% YoY Expansion</div>
              </div>
            </div>

            <!-- Customer Info 4-Col Grid -->
            <div class="customer-info-grid">
              <div class="customer-info-col">
                <span class="info-label">Lead Account Manager</span>
                <span class="info-val">Dr. Elena Rostova</span>
                <span class="crm-text-muted-sm" >Senior Sales Director</span>
              </div>
              <div class="customer-info-col">
                <span class="info-label">Customer Executive Contact</span>
                <span class="info-val">P. V. Cherepanov</span>
                <span class="crm-text-muted-sm" >VP Procurement &amp; Automation</span>
              </div>
              <div class="customer-info-col">
                <span class="info-label">Direct Secure Channel</span>
                <span class="info-val info-val-mono">+7 (8202) 53-0900</span>
                <span class="crm-text-muted-sm" >procurement@severstal.com</span>
              </div>
              <div class="customer-info-col">
                <span class="info-label">Client Health Score</span>
                <span class="info-val crm-text-success" >98% Optimal Health</span>
                <span class="crm-text-muted-sm" >Zero overdue invoices</span>
              </div>
            </div>
          </div>

          <!-- Tabbed Section Navigation -->
          <div class="customer-tabs-nav">
            <button class="customer-tab-btn active" data-tab="overview" onclick="window.crmApp.switchCustomerTab('overview')">
              <span>Overview</span>
            </button>
            <button class="customer-tab-btn" data-tab="projects" onclick="window.crmApp.switchCustomerTab('projects')">
              <span>Projects</span>
              <span class="tab-badge">3</span>
            </button>
            <button class="customer-tab-btn" data-tab="documents" onclick="window.crmApp.switchCustomerTab('documents')">
              <span>Documents</span>
              <span class="tab-badge">12</span>
            </button>
            <button class="customer-tab-btn" data-tab="contracts" onclick="window.crmApp.switchCustomerTab('contracts')">
              <span>Contracts</span>
              <span class="tab-badge">4</span>
            </button>
            <button class="customer-tab-btn" data-tab="support" onclick="window.crmApp.switchCustomerTab('support')">
              <span>Support History</span>
              <span class="tab-badge">8</span>
            </button>
          </div>

          <!-- TAB 1: OVERVIEW (Active by Default) -->
          <div id="tab-panel-overview" class="tab-content-panel active">
            <div class="overview-summary-grid">
              <!-- Left Column: Linked Engineering Projects List -->
              <div class="crm-card">
                <div class="card-header-row">
                  <div>
                    <h3 class="card-title">Linked On-Site Engineering Projects</h3>
                    <p class="crm-meta-subtext" >
                      Active telemetry deployments, FAT sign-offs, and SCADA automation milestones
                    </p>
                  </div>
                  <button class="btn btn-outline btn-sm" onclick="window.crmApp.switchCustomerTab('projects')">View All Matrix</button>
                </div>

                <div class="crm-flex-col-gap-md" >
                  <!-- Project Item 1 -->
                  <div class="crm-card-indigo-border" >
                    <div class="crm-flex-between-start-mb2" >
                      <div>
                        <div class="crm-mono-indigo-11" >PRJ-VP-7721</div>
                        <div class="crm-title-13" >Blast Furnace #5 Automation &amp; Gas Analysis</div>
                        <div class="crm-meta-subtext" >Facility: Cherepovets Ironmaking Plant #4</div>
                      </div>
                      <span class="tier-badge crm-badge-warning-bold" >Execution (72%)</span>
                    </div>
                    <div class="crm-row-divided-sm" >
                      <span>Contract Scope: <strong class="crm-mono-navy" >$1,850,000.00</strong></span>
                      <span>Target Commissioning: <strong>Nov 28, 2024</strong></span>
                    </div>
                  </div>

                  <!-- Project Item 2 -->
                  <div class="crm-card-info-border" >
                    <div class="crm-flex-between-start-mb2" >
                      <div>
                        <div class="crm-mono-info-11" >PRJ-VP-7804</div>
                        <div class="crm-title-13" >Hot Strip Mill #2 Hydraulic Pressure Sensor Telemetry</div>
                        <div class="crm-meta-subtext" >Facility: Rolling Mill Bay 2 · Cherepovets</div>
                      </div>
                      <span class="tier-badge crm-badge-info-bold" >Integration (45%)</span>
                    </div>
                    <div class="crm-row-divided-sm" >
                      <span>Contract Scope: <strong class="crm-mono-navy" >$640,000.00</strong></span>
                      <span>Target Commissioning: <strong>Dec 20, 2024</strong></span>
                    </div>
                  </div>

                  <!-- Project Item 3 -->
                  <div class="crm-card-purple-border" >
                    <div class="crm-flex-between-start-mb2" >
                      <div>
                        <div class="crm-mono-purple-11" >PRJ-VP-8902</div>
                        <div class="crm-title-13" >Continuous Casting Machine #3 Optical Thickness Gauges</div>
                        <div class="crm-meta-subtext" >Facility: Steelmaking Shop #1</div>
                      </div>
                      <span class="tier-badge crm-badge-purple-bold" >Design / FAT (18%)</span>
                    </div>
                    <div class="crm-row-divided-sm" >
                      <span>Contract Scope: <strong class="crm-mono-navy" >$410,000.00</strong></span>
                      <span>Target Commissioning: <strong>Feb 15, 2025</strong></span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Right Column: Invoices Summary & Highly Confidential Contract Value Card -->
              <div class="overview-right-col">
                <!-- 1. Invoice Status Summary Widget -->
                <div class="crm-card">
                  <div class="card-header-row">
                    <div>
                      <h3 class="card-title">Commercial Invoicing &amp; Cashflow</h3>
                      <p class="crm-meta-subtext" >FY2024 Reconciled Balances</p>
                    </div>
                    <span class="crm-mono-success-10" >
                      ✓ CLEAN RECONCILIATION
                    </span>
                  </div>

                  <div class="crm-grid-2col-gap-sm" >
                    <div class="crm-card-dim-sm" >
                      <div class="crm-caption-xs" >Total Capital Paid</div>
                      <div class="crm-mono-navy-16" >$4,812,400.00</div>
                      <div class="crm-text-secondary-10" >10 Milestones Settled</div>
                    </div>
                    <div class="crm-card-amber-3" >
                      <div class="crm-caption-xs" >Outstanding Due</div>
                      <div class="crm-mono-amber-16" >$248,600.00</div>
                      <div class="crm-text-secondary-10" >3 Pending Invoices</div>
                    </div>
                  </div>

                  <div class="crm-row-dim-mono" >
                    <span>Next Milestone Due: <strong>Nov 28, 2024</strong></span>
                    <span class="crm-bold-amber" >$114,200.00</span>
                  </div>
                </div>

                <!-- 2. "Highly Confidential" Alert-Red (#B23A32) Tagged Contract Value Card -->
                <div class="confidential-contract-card">
                  <div class="confidential-badge-banner">
                    <span class="confidential-pill">
                      <span>🔒</span>
                      <span>HIGHLY CONFIDENTIAL</span>
                    </span>
                    <span class="crm-mono-confidential-10" >
                      RESTRICTED COMMERCIAL // TIER 4
                    </span>
                  </div>

                  <div class="contract-value-display">
                    <span class="contract-tcv-label">Master Services Agreement (MSA-2024-SVST-088)</span>
                    <div class="contract-tcv-number">$14,250,000.00 USD</div>
                    <div class="crm-text-secondary-sm" >
                      Active Commitment Period: Jan 01, 2024 – Dec 31, 2026 • GOST Cryptographic Seal Attached
                    </div>
                  </div>

                  <div class="contract-metrics-row">
                    <div class="contract-metric-box">
                      <span>Annual Contract Value (2024)</span>
                      <span>$6,850,000.00</span>
                    </div>
                    <div class="contract-metric-box">
                      <span>Gross Margin Target</span>
                      <span class="crm-text-success" >42.5% Enterprise</span>
                    </div>
                    <div class="contract-metric-box">
                      <span>Signed Mutual NDA</span>
                      <span class="crm-text-indigo" >NDA-VP-SVR-90214</span>
                    </div>
                    <div class="contract-metric-box">
                      <span>Authorized Signatory</span>
                      <span class="crm-text-semibold-12" >P. V. Cherepanov</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- TAB 2: PROJECTS PANEL -->
          <div id="tab-panel-projects" class="tab-content-panel">
            <div class="crm-card">
              <h3 class="card-title crm-mb-4" >Complete Severstal Engineering Projects Matrix</h3>
              <table class="accounts-table">
                <thead>
                  <tr>
                    <th>Project ID</th>
                    <th>Equipment Scope</th>
                    <th>Facility</th>
                    <th>Status</th>
                    <th>Budget</th>
                    <th>Assigned Lead</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="account-row account-row-tagged">
                    <td><strong class="crm-mono-indigo" >PRJ-VP-7721</strong></td>
                    <td>Blast Furnace #5 Automation &amp; Gas Analysis Instrumentation</td>
                    <td>Cherepovets Plant #4</td>
                    <td><span class="tier-badge crm-badge-warning" >Execution (72%)</span></td>
                    <td><strong class="crm-mono" >$1,850,000.00</strong></td>
                    <td>Dr. Elena Rostova</td>
                  </tr>
                  <tr class="account-row account-row-tagged">
                    <td><strong class="crm-mono-indigo" >PRJ-VP-7804</strong></td>
                    <td>Hot Strip Mill Hydraulic Pressure Telemetry Retrofit</td>
                    <td>Hot Strip Mill #2</td>
                    <td><span class="tier-badge crm-badge-info" >Integration (45%)</span></td>
                    <td><strong class="crm-mono" >$640,000.00</strong></td>
                    <td>Viktor Morozov</td>
                  </tr>
                  <tr class="account-row account-row-tagged">
                    <td><strong class="crm-mono-indigo" >PRJ-VP-8902</strong></td>
                    <td>Continuous Casting Machine #3 Optical Thickness Gauges</td>
                    <td>Casting Bay #3</td>
                    <td><span class="tier-badge crm-badge-purple" >Design (18%)</span></td>
                    <td><strong class="crm-mono" >$410,000.00</strong></td>
                    <td>Anna Belova</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- TAB 3: DOCUMENTS PANEL -->
          <div id="tab-panel-documents" class="tab-content-panel">
            <div class="crm-card">
              <h3 class="card-title crm-mb-4" >Customer Technical Dossiers &amp; Compliance Certificates</h3>
              <div class="crm-grid-3col" >
                <div class="crm-card crm-card-border-indigo-3" >
                  <div class="crm-bold-navy" >CERT-2024-HPF-0994.pdf</div>
                  <div class="crm-meta-desc" >High-Pressure Flowmeter HPF-900X Calibration Certificate</div>
                  <span class="tier-badge crm-badge-success" >Rostest Certified</span>
                </div>
                <div class="crm-card crm-card-border-indigo-3" >
                  <div class="crm-bold-navy" >DWG-7721-PND-V3.dwg</div>
                  <div class="crm-meta-desc" >Blast Furnace #5 Automation Wiring Schematic &amp; P&amp;ID</div>
                  <span class="tier-badge crm-badge-info" >Approved for Construction</span>
                </div>
                <div class="crm-card crm-card-confidential-border" >
                  <div class="crm-bold-navy" >ADDENDUM-CA-402.pdf</div>
                  <div class="crm-meta-desc" >Spare Parts Consignment Agreement Q4 2024 - Q2 2025</div>
                  <span class="confidential-pill">Confidential</span>
                </div>
              </div>
            </div>
          </div>

          <!-- TAB 4: CONTRACTS PANEL -->
          <div id="tab-panel-contracts" class="tab-content-panel">
            <div class="confidential-contract-card">
              <div class="confidential-badge-banner">
                <span class="confidential-pill">🔒 Highly Confidential Legal Registry</span>
                <span class="crm-mono-bold-confidential" >4 ACTIVE CONTRACT DOCUMENTS</span>
              </div>
              <table class="accounts-table crm-bg-transparent" >
                <thead>
                  <tr>
                    <th>Contract ID</th>
                    <th>Document Description</th>
                    <th>Effective Dates</th>
                    <th>Total Value</th>
                    <th>Legal Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><strong class="crm-mono" >MSA-2024-SVST-088</strong></td>
                    <td>Master Equipment &amp; Automation Services Agreement</td>
                    <td>Jan 2024 - Dec 2026</td>
                    <td><strong class="crm-mono" >$14,250,000.00</strong></td>
                    <td><span class="tier-badge crm-badge-success" >Active (Counter-Signed)</span></td>
                  </tr>
                  <tr>
                    <td><strong class="crm-mono" >SLA-2024-TIER1</strong></td>
                    <td>24/7 Field Specialist &amp; Incident SLA Support Coverage</td>
                    <td>Jan 2024 - Dec 2025</td>
                    <td><strong class="crm-mono" >$850,000.00 / yr</strong></td>
                    <td><span class="tier-badge crm-badge-success" >Active</span></td>
                  </tr>
                  <tr>
                    <td><strong class="crm-mono" >NDA-VP-SVR-90214</strong></td>
                    <td>Bilateral Non-Disclosure Agreement (GOST R 34.10)</td>
                    <td>Dec 2023 - Dec 2028</td>
                    <td><strong class="crm-mono" >Unlimited Scope</strong></td>
                    <td><span class="tier-badge crm-badge-indigo" >Valid Electronic Seal</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- TAB 5: SUPPORT HISTORY PANEL -->
          <div id="tab-panel-support" class="tab-content-panel">
            <div class="crm-card">
              <h3 class="card-title crm-mb-4" >Service Desk &amp; Field Incident History (Severstal #VP-90214)</h3>
              <div class="activity-feed-list">
                <div class="activity-item">
                  <div class="activity-icon-container deal crm-badge-danger-soft" >⚠️</div>
                  <div class="activity-content">
                    <div class="activity-title">Incident TCK-9482 · SCADA Sensor Bank #2 Analog Loop Dropout</div>
                    <div class="activity-desc">Boris K. dispatched to Mezzanine Bay C for high-temp sensor replacement. SLA met: 7 mins to response.</div>
                    <span class="activity-timestamp">Oct 24, 10:14 AM MSK · Severity 1 (Critical) · Escalated</span>
                  </div>
                </div>
                <div class="activity-item">
                  <div class="activity-icon-container contract">✓</div>
                  <div class="activity-content">
                    <div class="activity-title">Incident TCK-9351 · Hydraulic Pressure Firmware Compatibility</div>
                    <div class="activity-desc">Denis Sokolov verified Siemens S7-400 PLC patch. Successfully validated and closed.</div>
                    <span class="activity-timestamp">Oct 21, 14:20 MSK · Resolved</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </main>
    </div>
  </div>

  <!-- Modal: + New Opportunity -->
  <div id="modal-new-opportunity" class="modal-backdrop">
    <div class="modal-card">
      <div class="modal-header">
        <h3 class="modal-title">+ Log New Industrial Opportunity for Severstal</h3>
        <button class="icon-button" onclick="window.crmApp.closeModal('modal-new-opportunity')">✕</button>
      </div>
      <form id="form-new-opportunity">
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label" for="new-opp-title">Opportunity / Equipment Scope Title</label>
            <input type="text" id="new-opp-title" class="form-input" placeholder="e.g. Continuous Casting Optical Gauge Upgrade" required />
          </div>

          <div class="crm-grid-2col" >
            <div class="form-group">
              <label class="form-label" for="new-opp-client">Client Enterprise Account</label>
              <select id="new-opp-client" class="form-select" required>
                <option value="Severstal Metallurgy PJSC" selected>Severstal Metallurgy PJSC</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="new-opp-value">Deal Value (USD)</label>
              <input type="number" id="new-opp-value" class="form-input" placeholder="410000" required />
            </div>
          </div>

          <div class="crm-grid-2col" >
            <div class="form-group">
              <label class="form-label" for="new-opp-stage">Initial Pipeline Stage</label>
              <select id="new-opp-stage" class="form-select">
                <option value="qualification">1. Qualification</option>
                <option value="proposal" selected>2. Proposal / Spec</option>
                <option value="negotiation">3. Negotiation</option>
                <option value="contract">4. Contract Gating</option>
                <option value="won">5. Won / Finalized</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="new-opp-date">Target Close Date</label>
              <input type="text" id="new-opp-date" class="form-input" placeholder="Dec 30, 2024" value="Dec 30, 2024" />
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline" onclick="window.crmApp.closeModal('modal-new-opportunity')">Cancel</button>
          <button type="submit" class="btn btn-primary-amber">Create Opportunity</button>
        </div>
      </form>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
  <link rel="stylesheet" href="../assets/css/api-ui.css">
  <script src="../assets/js/api-client.js"></script>
  <script src="js/crm-data.js"></script>
</body>

</html>