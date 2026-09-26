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
  <title>VOSTOKPRIBOR CRM · Leads Management</title>
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
                <span>LEAD INTAKE LEDGER</span>
              </div>
            </div>
          </a>
        </div>

        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search leads by plant, engineer, or RFQ scope..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <div class="top-nav__actions">
          <div class="pipeline-sync-badge">
            <span class="crm-status-success" >●</span>
            <span>Sync: <strong>Active 99.98%</strong></span>
          </div>
          <button class="btn btn-primary-amber btn-sm" onclick="window.crmApp.openModal('modal-new-lead')">
            <span>+ Log New Lead</span>
          </button>
          <button class="icon-button" title="Telemetry" onclick="window.crmApp.showToast('Lead Telemetry', '3 inbound RFQs received from Chelyabinsk Metallurgical Plant.')">
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
      <!-- Left Sidebar Navigation -->
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

            <!-- Leads (Active) -->
            <a href="Leads.php" class="sidebar-nav-item active">
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
          <!-- Page Header -->
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <span>Enterprise CRM</span>
                <span class="breadcrumb-separator">/</span>
                <span>Sales Operations</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Inbound &amp; Outbound Leads</span>
              </div>
              <h1 class="page-title">Industrial Inbound &amp; Outbound Leads Ledger</h1>
              <p class="page-subtitle">Qualification scoring, RFQ specifications, and enterprise customer conversions</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.crmApp.showToast('Lead Export', 'Exported 28 lead records with GOST compliance tags.')">
                <span>📥 Export Leads</span>
              </button>
              <button class="btn btn-primary-amber" onclick="window.crmApp.openModal('modal-new-lead')">
                <span>+ Log New Lead</span>
              </button>
            </div>
          </div>

          <!-- KPI Summary Strip -->
          <div class="kpi-grid">
            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Active Intake Leads</span>
                <div class="kpi-icon-pill indigo">👥</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">28</span>
                <span class="crm-text-muted-13" >Total</span>
              </div>
              <div class="kpi-footer">
                <span>9 Uncontacted</span>
                <span class="kpi-trend up">▲ +14% WoW</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Qualification Ratio</span>
                <div class="kpi-icon-pill amber">⚡</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">64.2%</span>
                <span class="crm-text-muted-12" >Qualified</span>
              </div>
              <div class="kpi-footer">
                <span>Target: &gt;55%</span>
                <span class="kpi-trend up">▲ +3.8%</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Avg SLA Response Time</span>
                <div class="kpi-icon-pill steel">⏱️</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">18m</span>
                <span class="crm-text-muted-12" >MSK Time</span>
              </div>
              <div class="kpi-footer">
                <span>Target: &lt; 30 mins</span>
                <span class="kpi-trend up">✓ Met</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">High-Value RFQs (&gt;$500K)</span>
                <div class="kpi-icon-pill success">💎</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">11</span>
                <span class="crm-text-muted-12" >Priority Deals</span>
              </div>
              <div class="kpi-footer">
                <span>Est. Value: <strong>$9.4M</strong></span>
                <span class="kpi-trend amber">Gated</span>
              </div>
            </div>
          </div>

          <!-- Filter Toolbar -->
          <div class="page-filter-bar">
            <div class="filter-pills-group">
              <button class="filter-pill-btn active">All Leads (28)</button>
              <button class="filter-pill-btn">Uncontacted (9)</button>
              <button class="filter-pill-btn">Engineering Qualification (8)</button>
              <button class="filter-pill-btn">High Priority Tier-1 (11)</button>
            </div>
            <div class="crm-gap-sm" >
              <select class="filter-select">
                <option>Filter by Source: All</option>
                <option>Enterprise B2B Web RFQ</option>
                <option>Metallurgy Industrial Expo</option>
                <option>Direct Engineering Inbound</option>
              </select>
            </div>
          </div>

          <!-- Leads Data Table with Amber-Orange Left Tag (#D9822B) -->
          <div class="crm-card">
            <div class="card-header-row">
              <div>
                <h3 class="card-title">Inbound Industrial Engineering Leads</h3>
                <p class="crm-meta-subtext" >
                  Data rows marked with amber-orange left border classification stripe. Click "Convert to Customer" to initialize client account.
                </p>
              </div>
            </div>

            <table class="accounts-table">
              <thead>
                <tr>
                  <th>Lead / Contact Engineer</th>
                  <th>Enterprise Plant / Facility</th>
                  <th>Equipment Scope &amp; Target</th>
                  <th>Est. Deal Value</th>
                  <th>Lead Score</th>
                  <th>Source</th>
                  <th>Status</th>
                  <th>Primary Action</th>
                </tr>
              </thead>
              <tbody id="leads-tbody">
                <!-- Row 1 -->
                <tr class="account-row account-row-tagged">
                  <td>
                    <div class="account-name-cell">
                      <span class="account-name-title">Dr. Andrei Vasiliev</span>
                      <span class="account-name-sub">Chief Automation Engineer · +7 (351) 259-8801</span>
                    </div>
                  </td>
                  <td>
                    <strong>Chelyabinsk Pipe Plant (ChelPipe)</strong>
                    <div class="crm-text-muted-sm" >Pipe Rolling Mill #8 · Челябинск</div>
                  </td>
                  <td>
                    <span>Seamless Casing Ultrasonic Flaw Detection Skid</span>
                  </td>
                  <td>
                    <strong class="crm-mono-navy" >$1,450,000.00</strong>
                  </td>
                  <td>
                    <span class="crm-mono-bold-success" >94 / 100</span>
                  </td>
                  <td>
                    <span class="tier-badge tier-1">Web RFQ</span>
                  </td>
                  <td>
                    <span class="tier-badge strategic">High Priority</span>
                  </td>
                  <td>
                    <button class="btn btn-primary-amber btn-sm" onclick="window.crmApp.convertLeadToCustomer('Dr. Andrei Vasiliev', 'Chelyabinsk Pipe Plant (ChelPipe)', '$1,450,000.00')">
                      <span>Convert to Customer</span>
                    </button>
                  </td>
                </tr>

                <!-- Row 2 -->
                <tr class="account-row account-row-tagged">
                  <td>
                    <div class="account-name-cell">
                      <span class="account-name-title">Valery Semenov</span>
                      <span class="account-name-sub">Head of Instrumentation &amp; SCADA · +7 (3519) 24-0012</span>
                    </div>
                  </td>
                  <td>
                    <strong>Magnitogorsk Iron &amp; Steel Works (MMK)</strong>
                    <div class="crm-text-muted-sm" >Oxygen Converter Shop #2 · Магнитогорск</div>
                  </td>
                  <td>
                    <span>Multi-Channel Gas Optical Spectrometry Matrix</span>
                  </td>
                  <td>
                    <strong class="crm-mono-navy" >$2,280,000.00</strong>
                  </td>
                  <td>
                    <span class="crm-mono-bold-success" >91 / 100</span>
                  </td>
                  <td>
                    <span class="tier-badge tier-1">Direct Inbound</span>
                  </td>
                  <td>
                    <span class="tier-badge tier-1">Qualification</span>
                  </td>
                  <td>
                    <button class="btn btn-primary-amber btn-sm" onclick="window.crmApp.convertLeadToCustomer('Valery Semenov', 'Magnitogorsk Iron & Steel (MMK)', '$2,280,000.00')">
                      <span>Convert to Customer</span>
                    </button>
                  </td>
                </tr>

                <!-- Row 3 -->
                <tr class="account-row account-row-tagged">
                  <td>
                    <div class="account-name-cell">
                      <span class="account-name-title">Irina Kondratieva</span>
                      <span class="account-name-sub">Senior Process Technologist · +7 (342) 219-4400</span>
                    </div>
                  </td>
                  <td>
                    <strong>Uralchem Mineral Fertilizers</strong>
                    <div class="crm-text-muted-sm" >Ammonia Synthesis Complex · Пермь</div>
                  </td>
                  <td>
                    <span>HPF-900X High Pressure Flowmeters (x24 Unit Batch)</span>
                  </td>
                  <td>
                    <strong class="crm-mono-navy" >$620,000.00</strong>
                  </td>
                  <td>
                    <span class="crm-mono-bold-indigo" >85 / 100</span>
                  </td>
                  <td>
                    <span class="tier-badge tier-2">Expo 2024</span>
                  </td>
                  <td>
                    <span class="tier-badge tier-2">Uncontacted</span>
                  </td>
                  <td>
                    <button class="btn btn-primary-amber btn-sm" onclick="window.crmApp.convertLeadToCustomer('Irina Kondratieva', 'Uralchem Mineral Fertilizers', '$620,000.00')">
                      <span>Convert to Customer</span>
                    </button>
                  </td>
                </tr>

                <!-- Row 4 -->
                <tr class="account-row account-row-tagged">
                  <td>
                    <div class="account-name-cell">
                      <span class="account-name-title">Oleg Marchenko</span>
                      <span class="account-name-sub">Lead Maintenance Tech · +7 (3812) 69-7000</span>
                    </div>
                  </td>
                  <td>
                    <strong>Gazprom Neft Omsk Refinery</strong>
                    <div class="crm-text-muted-sm" >Catalytic Cracking Unit 43-103 · Омск</div>
                  </td>
                  <td>
                    <span>Wireless Vibration Sensor Mesh &amp; IoT Gateways</span>
                  </td>
                  <td>
                    <strong class="crm-mono-navy" >$890,000.00</strong>
                  </td>
                  <td>
                    <span class="crm-mono-bold-success" >88 / 100</span>
                  </td>
                  <td>
                    <span class="tier-badge tier-1">Web RFQ</span>
                  </td>
                  <td>
                    <span class="tier-badge tier-1">Qualification</span>
                  </td>
                  <td>
                    <button class="btn btn-primary-amber btn-sm" onclick="window.crmApp.convertLeadToCustomer('Oleg Marchenko', 'Gazprom Neft Omsk Refinery', '$890,000.00')">
                      <span>Convert to Customer</span>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal: Convert to Customer -->
  <div id="modal-convert-customer" class="modal-backdrop">
    <div class="modal-card">
      <div class="modal-header">
        <h3 class="modal-title">Convert Lead to Enterprise Customer Account</h3>
        <button class="icon-button" onclick="window.crmApp.closeModal('modal-convert-customer')">✕</button>
      </div>
      <div class="modal-body">
        <div class="crm-card-customer-border" >
          <div class="crm-caption-muted" >Enterprise Account</div>
          <div class="crm-heading-15"  id="convert-lead-company">Company Name</div>
          <div class="crm-text-secondary-subtext" >Primary Engineer: <strong id="convert-lead-name">Engineer Name</strong></div>
          <div class="crm-mono-value-navy" >Initial Deal Pipeline: <span id="convert-lead-val">$0.00</span></div>
        </div>

        <div class="form-group">
          <label class="form-label">Assign Enterprise Account Manager</label>
          <select class="form-select">
            <option>Dr. Elena Rostova (Senior Technical Sales Lead)</option>
            <option>Mikhail Sorokin (VP Enterprise Sales)</option>
            <option>Viktor Morozov (Key Account Director)</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Client Classification Tier</label>
          <select class="form-select">
            <option>Strategic Tier-1 Enterprise ($5M+ Potential)</option>
            <option>Tier-1 Enterprise ($1M - $5M Potential)</option>
            <option>Tier-2 Enterprise</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" onclick="window.crmApp.closeModal('modal-convert-customer')">Cancel</button>
        <button class="btn btn-primary-amber" onclick="window.crmApp.showToast('Account Converted', 'Customer account created with generated ID #VP-91044.'); window.crmApp.closeModal('modal-convert-customer'); setTimeout(() => window.location.href='CustomerDetail.php', 500);">Confirm Conversion</button>
      </div>
    </div>
  </div>

  <!-- Modal: + Log New Lead -->
  <div id="modal-new-lead" class="modal-backdrop">
    <div class="modal-card">
      <div class="modal-header">
        <h3 class="modal-title">+ Log New Industrial Inbound Lead</h3>
        <button class="icon-button" onclick="window.crmApp.closeModal('modal-new-lead')">✕</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Plant / Enterprise Name</label>
          <input type="text" class="form-input" placeholder="e.g. Nornickel Copper Plant #3" required />
        </div>
        <div class="crm-grid-2col" >
          <div class="form-group">
            <label class="form-label">Contact Engineer</label>
            <input type="text" class="form-input" placeholder="Dr. Alexey Smirnov" required />
          </div>
          <div class="form-group">
            <label class="form-label">Estimated Deal Scope (USD)</label>
            <input type="number" class="form-input" placeholder="850000" required />
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Equipment Specification / Scope Notes</label>
          <textarea class="form-textarea" rows="2" placeholder="Requested instrumentation scope, hazardous environment certifications..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" onclick="window.crmApp.closeModal('modal-new-lead')">Cancel</button>
        <button class="btn btn-primary-amber" onclick="window.crmApp.showToast('Lead Logged', 'New lead registered in intake ledger.'); window.crmApp.closeModal('modal-new-lead');">Save Lead</button>
      </div>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
  <link rel="stylesheet" href="../assets/css/api-ui.css">
  <script src="../assets/js/api-client.js"></script>
  <script src="js/crm-data.js"></script>
</body>

</html>