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
  <title>VOSTOKPRIBOR CRM · Opportunities Kanban Board</title>
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
                <span>DEAL FLOW PIPELINE</span>
              </div>
            </div>
          </a>
        </div>

        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search opportunities (e.g. Blast Furnace, NLMK, Talnakh)..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <div class="top-nav__actions">
          <div class="pipeline-sync-badge">
            <span style="color: #2ECC71;">●</span>
            <span>Sync: <strong>Active 99.98%</strong></span>
          </div>
          <button class="btn btn-primary-amber btn-sm" onclick="window.crmApp.openModal('modal-new-opportunity')">
            <span>+ New Opportunity</span>
          </button>
          <button class="icon-button" title="Telemetry" onclick="window.crmApp.showToast('Deal Telemetry', 'Norilsk Nickel accepted commercial milestone pricing.')">
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

            <!-- Opportunities (Active) -->
            <a href="Opportunities.php" class="sidebar-nav-item active">
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
                <span class="breadcrumb-current">Opportunities Kanban Board</span>
              </div>
              <h1 class="page-title">Enterprise Commercial Opportunities Kanban</h1>
              <p class="page-subtitle">Drag-and-drop industrial deals through 5 gated conversion stages with live revenue recalculation</p>
            </div>
            <div class="page-header-actions">
              <div class="filter-select" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <span>👤 Sales Rep: All Directors</span>
              </div>
              <button class="btn btn-outline" onclick="window.crmApp.showToast('Kanban Filtered', 'Displaying all 42 opportunities across 5 gating stages.')">
                <span>⚡ Refresh Board</span>
              </button>
              <button class="btn btn-primary-amber" onclick="window.crmApp.openModal('modal-new-opportunity')">
                <span>+ New Opportunity</span>
              </button>
            </div>
          </div>

          <!-- Opportunities 5-Column Kanban Board -->
          <div class="kanban-board-container">
            <!-- Column 1: Qualification -->
            <div class="kanban-column" id="kanban-col-qualification">
              <div class="kanban-col-header col-header-1">
                <span class="kanban-col-title">1. Qualification</span>
                <div class="kanban-col-metrics">
                  <span class="kanban-col-count" id="col-count-qualification">2</span>
                  <span id="col-val-qualification" style="font-weight: 700;">$4.75M</span>
                </div>
              </div>
              <div class="kanban-cards-wrapper" id="kanban-cards-qualification"></div>
            </div>

            <!-- Column 2: Proposal -->
            <div class="kanban-column" id="kanban-col-proposal">
              <div class="kanban-col-header col-header-2">
                <span class="kanban-col-title">2. Proposal / Spec</span>
                <div class="kanban-col-metrics">
                  <span class="kanban-col-count" id="col-count-proposal">2</span>
                  <span id="col-val-proposal" style="font-weight: 700;">$1.33M</span>
                </div>
              </div>
              <div class="kanban-cards-wrapper" id="kanban-cards-proposal"></div>
            </div>

            <!-- Column 3: Negotiation -->
            <div class="kanban-column" id="kanban-col-negotiation">
              <div class="kanban-col-header col-header-3">
                <span class="kanban-col-title">3. Negotiation</span>
                <div class="kanban-col-metrics">
                  <span class="kanban-col-count" id="col-count-negotiation">2</span>
                  <span id="col-val-negotiation" style="font-weight: 700;">$4.25M</span>
                </div>
              </div>
              <div class="kanban-cards-wrapper" id="kanban-cards-negotiation"></div>
            </div>

            <!-- Column 4: Contract -->
            <div class="kanban-column" id="kanban-col-contract">
              <div class="kanban-col-header col-header-4">
                <span class="kanban-col-title">4. Contract Review</span>
                <div class="kanban-col-metrics">
                  <span class="kanban-col-count" id="col-count-contract">1</span>
                  <span id="col-val-contract" style="font-weight: 700;">$640K</span>
                </div>
              </div>
              <div class="kanban-cards-wrapper" id="kanban-cards-contract"></div>
            </div>

            <!-- Column 5: Won/Lost -->
            <div class="kanban-column" id="kanban-col-won">
              <div class="kanban-col-header col-header-5">
                <span class="kanban-col-title">5. Won / Finalized</span>
                <div class="kanban-col-metrics">
                  <span class="kanban-col-count" id="col-count-won">2</span>
                  <span id="col-val-won" style="font-weight: 700;">$814K</span>
                </div>
              </div>
              <div class="kanban-cards-wrapper" id="kanban-cards-won"></div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Modals -->
  <!-- Modal 1: + New Opportunity -->
  <div id="modal-new-opportunity" class="modal-backdrop">
    <div class="modal-card">
      <div class="modal-header">
        <h3 class="modal-title">+ Log New Industrial Opportunity</h3>
        <button class="icon-button" onclick="window.crmApp.closeModal('modal-new-opportunity')">✕</button>
      </div>
      <form id="form-new-opportunity">
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label" for="new-opp-title">Opportunity / Equipment Scope Title</label>
            <input type="text" id="new-opp-title" class="form-input" placeholder="e.g. Blast Furnace Gas Skid Automation Phase 2" required />
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
              <label class="form-label" for="new-opp-client">Client Enterprise Account</label>
              <select id="new-opp-client" class="form-select" required>
                <option value="Severstal Metallurgy PJSC">Severstal Metallurgy PJSC</option>
                <option value="NLMK Group Lipetsk">NLMK Group Lipetsk</option>
                <option value="Norilsk Nickel Mining">Norilsk Nickel Mining</option>
                <option value="EVRAZ Consolidated">EVRAZ Consolidated</option>
                <option value="PhosAgro Chemical">PhosAgro Chemical</option>
                <option value="Gazprom Neft Omsk">Gazprom Neft Omsk</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="new-opp-value">Deal Value (USD)</label>
              <input type="number" id="new-opp-value" class="form-input" placeholder="1850000" required />
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
              <label class="form-label" for="new-opp-stage">Initial Pipeline Stage</label>
              <select id="new-opp-stage" class="form-select">
                <option value="qualification">1. Qualification</option>
                <option value="proposal">2. Proposal / Spec</option>
                <option value="negotiation" selected>3. Negotiation</option>
                <option value="contract">4. Contract Gating</option>
                <option value="won">5. Won / Finalized</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="new-opp-date">Target Close Date</label>
              <input type="text" id="new-opp-date" class="form-input" placeholder="Dec 30, 2024" value="Dec 30, 2024" />
            </div>
          </div>

          <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 12px; cursor: pointer;">
              <input type="checkbox" id="new-opp-confidential" checked />
              <span>Apply Alert-Red <strong style="color: var(--crm-tag-confidential);">"Highly Confidential"</strong> Commercial Data Tag</span>
            </label>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline" onclick="window.crmApp.closeModal('modal-new-opportunity')">Cancel</button>
          <button type="submit" class="btn btn-primary-amber">Create Opportunity</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal 2: Opportunity Inspector Drawer -->
  <div id="modal-inspect-opportunity" class="modal-backdrop">
    <div class="modal-card">
      <div class="modal-header">
        <h3 class="modal-title" id="inspect-opp-title">Opportunity Details</h3>
        <button class="icon-button" onclick="window.crmApp.closeModal('modal-inspect-opportunity')">✕</button>
      </div>
      <div class="modal-body">
        <div style="display: flex; justify-content: space-between; align-items: baseline; background: var(--crm-surface-dim); padding: 1rem; border-radius: var(--crm-radius-md); border-left: 3px solid var(--crm-tag-customer);">
          <div>
            <div style="font-size: 11px; text-transform: uppercase; color: var(--crm-text-muted); font-weight: 600;">Enterprise Client</div>
            <div style="font-size: 15px; font-weight: 700; color: var(--crm-navy);" id="inspect-opp-client">Client Name</div>
          </div>
          <div style="text-align: right;">
            <div style="font-size: 11px; text-transform: uppercase; color: var(--crm-text-muted); font-weight: 600;">Contract Value</div>
            <div style="font-family: var(--crm-font-mono); font-size: 18px; font-weight: 700; color: var(--crm-navy);" id="inspect-opp-value">$0.00</div>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div class="form-group">
            <label class="form-label">Current Gating Stage</label>
            <select id="inspect-opp-stage" class="form-select">
              <option value="qualification">1. Qualification</option>
              <option value="proposal">2. Proposal / Spec</option>
              <option value="negotiation">3. Negotiation</option>
              <option value="contract">4. Contract Gating</option>
              <option value="won">5. Won / Finalized</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Expected Close Date</label>
            <div id="inspect-opp-date" style="padding: 0.6rem 0.85rem; background: var(--crm-surface-dim); border-radius: var(--crm-radius-md); font-family: var(--crm-font-mono); font-size: 13px;">-</div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Assigned Sales Engineer / Rep</label>
          <div id="inspect-opp-rep" style="padding: 0.6rem 0.85rem; background: var(--crm-surface-dim); border-radius: var(--crm-radius-md); font-size: 13px; font-weight: 600;">-</div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" onclick="window.crmApp.closeModal('modal-inspect-opportunity')">Close</button>
        <button class="btn btn-indigo" onclick="window.crmApp.showToast('Opportunity Saved', 'Commercial parameters synchronized.'); window.crmApp.closeModal('modal-inspect-opportunity');">Save Changes</button>
      </div>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
</body>
</html>
