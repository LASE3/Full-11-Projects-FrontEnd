<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR Finance · Payments &amp; Bank Reconciliation</title>
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

          <button class="icon-button" onclick="window.finApp.showToast('Reconciliation Notice', '6 bank transactions pending ledger pairing.')">
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
            <a href="PaymentsReconciliation.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span>
                <span>Payments &amp; Reconciliation</span>
              </div>
              <span class="sidebar-badge badge-red">6</span>
            </a>
            <a href="ProjectBilling.php" class="sidebar-nav-item">
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
            <a href="../api/logout.php?system=Finance%20%26%20Billing&redirect=../Finance%20%26%20Billing/login.php" class="sidebar-nav-item sidebar-nav-item--logout" id="btn-logout" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" onclick="(function(){sessionStorage.clear();localStorage.clear();})()">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                  </svg>
                </span>
                <span>Log Out</span>
              </div>
            </a>
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

      <!-- MAIN CONTENT -->
      <main class="content-wrapper">
        <div class="portal-container">
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <a href="Dashboard.php">Finance &amp; Billing</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Payments &amp; Bank Reconciliation</span>
              </div>
              <h1 class="page-title">Electronic Payment Clearing &amp; Bank Telemetry</h1>
              <p class="page-subtitle">Automatic clearing house, SPFS interbank telemetry, and manual transaction pairing queue</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.finApp.showToast('Bank Feeds Refreshed', 'Connected to Sberbank, VTB, and Gazprombank electronic clearing gateways.')">
                <span>🔄 Sync Bank Feeds</span>
              </button>
              <button class="btn btn-primary-amber" onclick="window.finApp.showToast('Auto-Reconciliation', 'Matched 4 standard telemetry wires based on invoice references.', 'green')">
                <span>⚡ Run Auto-Match Engine</span>
              </button>
            </div>
          </div>

          <!-- Unmatched Payments Desk -->
          <div class="fin-card" style="padding: 0; overflow: hidden; margin-bottom: 1.5rem;">
            <div style="padding: 1rem 1.25rem; background: #FFFFFF; border-bottom: 1px solid var(--fin-surface-border); display: flex; align-items: center; justify-content: space-between;">
              <h3 class="card-title">Pending Unmatched Transactions Desk (6)</h3>
              <span class="confidential-system-pill" style="font-size: 9.5px; padding: 2px 6px;">UNMATCHED POOL: $942,300.00</span>
            </div>

            <table class="fin-table">
              <thead>
                <tr>
                  <th>Transaction ID</th>
                  <th>Originating Account</th>
                  <th>Payment Type &amp; Remittance Memo</th>
                  <th>Ingested Amount</th>
                  <th>Received Timestamp</th>
                  <th style="text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr id="tx-row-TX-SPFS-9101" class="fin-table-row">
                  <td><span style="font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-green);">TX-SPFS-9101</span></td>
                  <td><strong>Severstal Metallurgy PJSC</strong> (Main Treasury)</td>
                  <td>SPFS Direct Wire · <code>Ref: PO-SEV-88219</code></td>
                  <td><strong style="font-family: var(--fin-font-mono); font-size: 13px; color: var(--fin-navy);">$420,000.00</strong></td>
                  <td>Nov 09, 2024 · 14:22 MSK</td>
                  <td style="text-align: right;">
                    <button class="btn btn-primary-amber btn-sm" onclick="window.finApp.reconcilePayment('TX-SPFS-9101')">Reconcile ⚡</button>
                  </td>
                </tr>

                <tr id="tx-row-TX-SWIFT-9102" class="fin-table-row">
                  <td><span style="font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-green);">TX-SWIFT-9102</span></td>
                  <td><strong>NLMK Group Lipetsk</strong> (Treasury Escrow)</td>
                  <td>Bank Wire (Gazprombank) · <code>Ref: NLMK-FAT-INV-8842</code></td>
                  <td><strong style="font-family: var(--fin-font-mono); font-size: 13px; color: var(--fin-navy);">$185,000.00</strong></td>
                  <td>Nov 08, 2024 · 10:15 MSK</td>
                  <td style="text-align: right;">
                    <button class="btn btn-primary-amber btn-sm" onclick="window.finApp.reconcilePayment('TX-SWIFT-9102')">Reconcile ⚡</button>
                  </td>
                </tr>

                <tr id="tx-row-TX-SPFS-9103" class="fin-table-row">
                  <td><span style="font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-green);">TX-SPFS-9103</span></td>
                  <td><strong>Norilsk Nickel Mining</strong> (Arctic Div)</td>
                  <td>SPFS Direct Wire · <code>Ref: NN-ARC-TEL-99</code></td>
                  <td><strong style="font-family: var(--fin-font-mono); font-size: 13px; color: var(--fin-navy);">$337,300.00</strong></td>
                  <td>Nov 06, 2024 · 17:40 MSK</td>
                  <td style="text-align: right;">
                    <button class="btn btn-primary-amber btn-sm" onclick="window.finApp.reconcilePayment('TX-SPFS-9103')">Reconcile ⚡</button>
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
