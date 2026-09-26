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
  <title>VOSTOKPRIBOR CRM · Quotes &amp; Contracts Matrix</title>
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
                <span>CONTRACTS REGISTRY</span>
              </div>
            </div>
          </a>
        </div>

        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search master contracts, NDAs, quotes (e.g. MSA-2024, Severstal)..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <div class="top-nav__actions">
          <div class="pipeline-sync-badge">
            <span class="crm-status-success" >●</span>
            <span>Sync: <strong>Active 99.98%</strong></span>
          </div>
          <button class="btn btn-primary-amber btn-sm" onclick="window.crmApp.openModal('modal-new-quote')">
            <span>+ Create Quote</span>
          </button>
          <button class="icon-button" title="Telemetry" onclick="window.crmApp.showToast('Contract Sealed', 'MSA-2024-SVST-088 encrypted EDS seal verified by GOST key.')">
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

            <!-- Quotes & Contracts (Active) -->
            <a href="QuotesAndContracts.php" class="sidebar-nav-item active">
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
                <span>Commercial Legal Governance</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Master Contracts &amp; Quotes</span>
              </div>
              <h1 class="page-title">Commercial Contracts &amp; Formal Quotes Registry</h1>
              <p class="page-subtitle">Highly Confidential Master Services Agreements (MSA), Support SLAs, and Engineering Quotations</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.crmApp.showToast('Audit Export', 'Legal audit trail exported with cryptographic integrity verification.')">
                <span>📑 Export Legal Audit</span>
              </button>
              <button class="btn btn-primary-amber" onclick="window.crmApp.openModal('modal-new-quote')">
                <span>+ Create Quote</span>
              </button>
            </div>
          </div>

          <!-- KPI Strip -->
          <div class="kpi-grid">
            <div class="crm-card kpi-card crm-border-t-confidential" >
              <div class="kpi-header">
                <span class="kpi-title">Governed Contract Value</span>
                <div class="kpi-icon-pill crm-badge-confidential" >🔒</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">$32.4M</span>
                <span class="crm-text-muted-12" >TCV</span>
              </div>
              <div class="kpi-footer">
                <span>19 Master Contracts</span>
                <span class="kpi-trend up">100% Active</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">EDS Signature Compliance</span>
                <div class="kpi-icon-pill success">🔏</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">100%</span>
                <span class="crm-text-muted-12" >GOST R 34.10</span>
              </div>
              <div class="kpi-footer">
                <span>All Electronic Seals Valid</span>
                <span class="kpi-trend up">Verified</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Pending Quotes</span>
                <div class="kpi-icon-pill amber">⚡</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">8</span>
                <span class="crm-text-muted-12" >Active Quotes</span>
              </div>
              <div class="kpi-footer">
                <span>Value: <strong>$6.12M</strong></span>
                <span class="kpi-trend amber">Under Review</span>
              </div>
            </div>

            <div class="crm-card kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Renewals in Q1 2025</span>
                <div class="kpi-icon-pill indigo">📅</div>
              </div>
              <div class="kpi-value-row">
                <span class="kpi-value kpi-value-mono">4</span>
                <span class="crm-text-muted-12" >Contracts</span>
              </div>
              <div class="kpi-footer">
                <span>Value: <strong>$8.9M</strong></span>
                <span class="kpi-trend up">In Gating</span>
              </div>
            </div>
          </div>

          <!-- Section 1: "Highly Confidential" Alert-Red (#B23A32) Tagged Master Contracts Registry -->
          <div class="confidential-contract-card crm-mb-6" >
            <div class="confidential-badge-banner">
              <div class="crm-flex-gap-md" >
                <span class="confidential-pill">
                  <span>🔒</span>
                  <span>HIGHLY CONFIDENTIAL LEGAL REGISTRY</span>
                </span>
                <span class="crm-text-secondary-12" >Tier-4 Commercial Governance (GOST Electronic Signature Verified)</span>
              </div>
              <span class="crm-mono-bold-confidential" >
                19 ACTIVE MASTER AGREEMENTS
              </span>
            </div>

            <table class="accounts-table crm-bg-transparent" >
              <thead>
                <tr>
                  <th>Contract ID &amp; Document</th>
                  <th>Enterprise Counterparty</th>
                  <th>Contract Period</th>
                  <th>Total Contract Value (TCV)</th>
                  <th>EDS Seal Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <strong class="crm-mono-confidential" >MSA-2024-SVST-088</strong>
                    <div class="crm-text-navy-11" >Master Automation Equipment &amp; SCADA Services Agreement</div>
                  </td>
                  <td>
                    <strong>Severstal Metallurgy PJSC</strong>
                    <div class="crm-text-muted-sm" >Cherepovets Plant #4</div>
                  </td>
                  <td>Jan 01, 2024 – Dec 31, 2026</td>
                  <td><strong class="crm-mono-navy-lg" >$14,250,000.00</strong></td>
                  <td><span class="tier-badge crm-badge-success" >✓ Counter-Signed</span></td>
                  <td>
                    <button class="btn btn-outline btn-sm" onclick="window.crmApp.showToast('Secure Download', 'Decrypted document MSA-2024-SVST-088.pdf downloaded.')">Download 🔒</button>
                  </td>
                </tr>

                <tr>
                  <td>
                    <strong class="crm-mono-confidential" >MSA-2023-NN-014</strong>
                    <div class="crm-text-navy-11" >Talnakh Concentrator Multi-Year Telemetry &amp; Field Sensor MSA</div>
                  </td>
                  <td>
                    <strong>Norilsk Nickel Mining</strong>
                    <div class="crm-text-muted-sm" >Polar Division</div>
                  </td>
                  <td>Nov 15, 2023 – Nov 14, 2025</td>
                  <td><strong class="crm-mono-navy-lg" >$8,400,000.00</strong></td>
                  <td><span class="tier-badge crm-badge-success" >✓ Counter-Signed</span></td>
                  <td>
                    <button class="btn btn-outline btn-sm" onclick="window.crmApp.showToast('Secure Download', 'Decrypted document MSA-2023-NN-014.pdf downloaded.')">Download 🔒</button>
                  </td>
                </tr>

                <tr>
                  <td>
                    <strong class="crm-mono-confidential" >SLA-2024-TIER1</strong>
                    <div class="crm-text-navy-11" >24/7 Field Engineering Specialist &amp; Incident SLA Support Coverage</div>
                  </td>
                  <td>
                    <strong>Severstal Metallurgy PJSC</strong>
                    <div class="crm-text-muted-sm" >Enterprise Wide</div>
                  </td>
                  <td>Jan 01, 2024 – Dec 31, 2025</td>
                  <td><strong class="crm-mono-navy-lg" >$850,000.00 / yr</strong></td>
                  <td><span class="tier-badge crm-badge-success" >✓ Active SLA</span></td>
                  <td>
                    <button class="btn btn-outline btn-sm" onclick="window.crmApp.showToast('Secure Download', 'Decrypted document SLA-2024-TIER1.pdf downloaded.')">Download 🔒</button>
                  </td>
                </tr>

                <tr>
                  <td>
                    <strong class="crm-mono-confidential" >NDA-VP-SVR-90214</strong>
                    <div class="crm-text-navy-11" >Bilateral Non-Disclosure Agreement for Industrial Proprietary Telemetry</div>
                  </td>
                  <td>
                    <strong>Severstal Metallurgy PJSC</strong>
                    <div class="crm-text-muted-sm" >Legal Dept.</div>
                  </td>
                  <td>Dec 01, 2023 – Dec 01, 2028</td>
                  <td><strong class="crm-mono-navy-lg" >Unlimited Scope</strong></td>
                  <td><span class="tier-badge crm-badge-indigo" >✓ Electronic Seal</span></td>
                  <td>
                    <button class="btn btn-outline btn-sm" onclick="window.crmApp.showToast('Secure Download', 'Decrypted document NDA-VP-SVR-90214.pdf downloaded.')">Download 🔒</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Section 2: Active Engineering Quotations Ledger -->
          <div class="crm-card">
            <div class="card-header-row">
              <div>
                <h3 class="card-title">Commercial Quotations (Pending &amp; Delivered)</h3>
                <p class="crm-meta-subtext" >
                  Engineering quotes marked with amber-orange left border classification tags
                </p>
              </div>
            </div>

            <table class="accounts-table">
              <thead>
                <tr>
                  <th>Quote Number</th>
                  <th>Account / Facility</th>
                  <th>Equipment Scope Breakdown</th>
                  <th>Quoted Price</th>
                  <th>Validity Period</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr class="account-row account-row-tagged">
                  <td><strong class="crm-mono-indigo" >QUO-2024-9912</strong></td>
                  <td><strong>NLMK Group Lipetsk</strong></td>
                  <td>16x High-Pressure Flowmeters HPF-900X with Hastelloy C Flanges</td>
                  <td><strong class="crm-mono" >$396,400.00</strong></td>
                  <td>Valid thru Dec 15, 2024</td>
                  <td><span class="tier-badge tier-1">Delivered</span></td>
                  <td><button class="btn btn-outline btn-sm" onclick="window.crmApp.showToast('Quote View', 'Opening Quote Spec QUO-2024-9912.')">View Spec →</button></td>
                </tr>
                <tr class="account-row account-row-tagged">
                  <td><strong class="crm-mono-indigo" >QUO-2024-9918</strong></td>
                  <td><strong>Norilsk Nickel Mining</strong></td>
                  <td>Flotation Sensor Array &amp; SCADA Gateway Hardware</td>
                  <td><strong class="crm-mono" >$2,400,000.00</strong></td>
                  <td>Valid thru Jan 10, 2025</td>
                  <td><span class="tier-badge strategic">In Negotiation</span></td>
                  <td><button class="btn btn-outline btn-sm" onclick="window.crmApp.showToast('Quote View', 'Opening Quote Spec QUO-2024-9918.')">View Spec →</button></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal: + Create New Quote -->
  <div id="modal-new-quote" class="modal-backdrop">
    <div class="modal-card">
      <div class="modal-header">
        <h3 class="modal-title">+ Generate Engineering Quotation</h3>
        <button class="icon-button" onclick="window.crmApp.closeModal('modal-new-quote')">✕</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Client Enterprise Account</label>
          <select class="form-select">
            <option>Severstal Metallurgy PJSC</option>
            <option>NLMK Group Lipetsk</option>
            <option>Norilsk Nickel Mining</option>
            <option>EVRAZ Consolidated</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Equipment Specification Item</label>
          <input type="text" class="form-input" placeholder="e.g. Optical Thickness Sensor Gauges Batch" required />
        </div>
        <div class="crm-grid-2col" >
          <div class="form-group">
            <label class="form-label">Total Quotation Value (USD)</label>
            <input type="number" class="form-input" placeholder="450000" required />
          </div>
          <div class="form-group">
            <label class="form-label">Quote Expiry Date</label>
            <input type="text" class="form-input" value="Jan 30, 2025" />
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" onclick="window.crmApp.closeModal('modal-new-quote')">Cancel</button>
        <button class="btn btn-primary-amber" onclick="window.crmApp.showToast('Quote Generated', 'Formal quotation generated with generated ID #QUO-2024-9925.'); window.crmApp.closeModal('modal-new-quote');">Generate Quote</button>
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