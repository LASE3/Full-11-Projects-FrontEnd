<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR IT Helpdesk · Ticket Detail TICK-8819</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <div class="app-container">
    <!-- ========================================================================
         TOP NAVIGATION BAR
         ======================================================================== -->
    <header class="top-nav">
      <div class="top-nav__accent-stripe"></div>

      <div class="top-nav__content">
        <!-- Brand & System Identifier -->
        <div class="brand-section">
          <a href="Dashboard.php" class="brand-logo-container">
            <img alt="VOSTOKPRIBOR Official Mark" class="brand-logo-img" src="https://lh3.googleusercontent.com/aida/AEtjO1XMGDZ9meLzj0XkVJ6C4Xv2AoEzKtMyFqF7KQ8eMADmbywzsRZ7VF4Em6pQ7fZ8QRJExCZedCKEUUo1fN1LpEmGsQva25blyUsGhOPvX2vv2cHGkppzOp9iT33Xy2n4Nkr5e_YY_0J78vA8Q7vKUpViCouPJo13HFVvW5olf7QEFzU3EX-WCqc0SYyPXZb7E11PafOMC_KprpG6Tre6bN_DyZS-CI-J5qiCgBtikqVNYGueATNgWbi73Q" />
            <div class="brand-divider"></div>
            <div class="brand-title-group">
              <div class="brand-title-row">
                <span class="brand-name">VOSTOKPRIBOR</span>
                <span class="system-tag">IT · SYS 08</span>
              </div>
              <div class="brand-subline">
                <span class="status-dot-pulse"></span>
                <span>helpdesk.vostokpribor.local</span>
                <span style="opacity: 0.5;">|</span>
                <span>SUPPORT OPERATIONS</span>
              </div>
            </div>
          </a>
        </div>

        <!-- Global Omni Search -->
        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search ticket ID, requester, SCADA node, knowledge base..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <!-- Right System Metrics & Profile -->
        <div class="top-nav__actions">
          <div class="pipeline-sync-badge" style="display: flex; align-items: center; gap: 0.35rem; font-family: var(--hd-font-mono); font-size: 10px; color: var(--hd-text-inverse-muted); background: rgba(255,255,255,0.06); padding: 3px 8px; border-radius: var(--hd-radius-sm); border: 1px solid rgba(255,255,255,0.08);">
            <span style="color: #2ECC71;">●</span>
            <span>SLA: <strong>98.4% Compliant</strong></span>
          </div>

          <button class="icon-button" title="Incident Telemetry Notifications" onclick="window.hdApp.showToast('Critical Alert', 'SCADA Gateway Node #3 packet loss detected in Lipetsk Bay.', 'critical')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="badge-dot"></span>
          </button>

          <div class="top-user-profile" onclick="window.hdApp.showToast('Active Tech Session', 'Alexey Ivanov · Tier 3 IT Operations Engineer')">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" alt="Alexey Ivanov" class="user-avatar-top" />
            <div class="user-details-top">
              <span class="user-name-top">Alexey Ivanov</span>
              <span class="user-role-top">Lead IT Tech · Tier 3</span>
            </div>
          </div>
        </div>
      </div>
    </header>

    <div class="main-layout">
      <!-- ========================================================================
           LEFT SIDEBAR NAVIGATION
           ======================================================================== -->
      <aside class="sidebar">
        <div>
          <div class="sidebar-section-title">IT Support Operations</div>
          <nav class="sidebar-nav">
            <!-- Screen 1: Dashboard -->
            <a href="Dashboard.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                </span>
                <span>Dashboard</span>
              </div>
            </a>

            <!-- Screen 2: Ticket Queue (Active for detail view) -->
            <a href="TicketQueue.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </span>
                <span>Ticket Queue</span>
              </div>
              <span class="sidebar-badge badge-orange">34</span>
            </a>

            <!-- My Tickets -->
            <a href="MyTickets.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                <span>My Tickets</span>
              </div>
              <span class="sidebar-badge badge-red">8</span>
            </a>

            <!-- Knowledge Base -->
            <a href="KnowledgeBase.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                </span>
                <span>Knowledge Base</span>
              </div>
              <span class="sidebar-badge">142</span>
            </a>

            <!-- Asset Management -->
            <a href="AssetManagement.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                </span>
                <span>Asset Management</span>
              </div>
              <span class="sidebar-badge">1,820</span>
            </a>

            <!-- SLA Reports -->
            <a href="SLAReports.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </span>
                <span>SLA Reports</span>
              </div>
              <span class="sidebar-badge badge-green">98.4%</span>
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
            <a href="login.php" class="sidebar-nav-item sidebar-nav-item--logout" id="btn-logout" onclick="(function(){sessionStorage.clear();localStorage.clear();})()">
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
              <span>Incident Response Gateway</span>
              <span class="security-badge-status">● LIVE</span>
            </div>
            <div style="font-size: 11px; color: var(--hd-text-inverse-muted); margin-top: 2px;">
              Active Escalations: <strong>3 P1 Incidents</strong>
            </div>
          </div>
        </div>
      </aside>

      <!-- ========================================================================
           MAIN CONTENT AREA: SCREEN 3 TICKET DETAIL VIEW (70% Left / 30% Right Split)
           ======================================================================== -->
      <main class="content-wrapper">
        <div class="portal-container">
          <!-- Page Header & Contextual Back Navigation -->
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <a href="TicketQueue.php" style="color: var(--hd-text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
                  <span>← Back to Ticket Queue</span>
                </a>
                <span class="breadcrumb-separator">/</span>
                <span>TICK-8819</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Triage &amp; Incident Remediation</span>
              </div>
              <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.25rem;">
                <h1 class="page-title" style="display: flex; align-items: center; gap: 0.75rem;">
                  <span style="font-family: var(--hd-font-mono); color: var(--hd-orange);">TICK-8819</span>
                  <span>SCADA Modbus Gateway #3 Packet Drop</span>
                </h1>
              </div>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" onclick="window.hdApp.showToast('Incident Audit', 'Exported cryptographic syslog payload for TICK-8819.')">
                <span>📋 Export Syslog Trace</span>
              </button>
            </div>
          </div>

          <!-- Two-Column Layout (70% Left / 30% Right) -->
          <div class="detail-layout-grid">
            <!-- ==================================================================
                 LEFT 70%: TICKET HEADER, TELEMETRY LOGS & CONVERSATION THREAD
                 ================================================================== -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
              <!-- Ticket Header Metadata Card -->
              <div class="hd-card" style="border-top: 3.5px solid var(--hd-priority-critical);">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid var(--hd-surface-border);">
                  <div style="display: flex; align-items: center; gap: 1rem;">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM" alt="Elena Rostova" style="width: 44px; height: 44px; border-radius: var(--hd-radius-md); object-fit: cover; border: 2px solid var(--hd-orange);" />
                    <div>
                      <div style="font-size: 15px; font-weight: 700; color: var(--hd-navy);">Dr. Elena Rostova</div>
                      <div style="font-size: 12px; color: var(--hd-text-secondary);">Chief Optical Calibration Architect · Dept of Laser Pyrometry</div>
                      <div style="font-size: 11px; color: var(--hd-text-muted); font-family: var(--hd-font-mono); margin-top: 2px;">
                        Reported: Today at 08:30:14 MSK · Incident Channel: Automated PLC Alarm Hook
                      </div>
                    </div>
                  </div>

                  <!-- Priority & Status Badges -->
                  <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.4rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                      <span class="priority-badge priority-critical">CRITICAL (P1)</span>
                      <span class="status-pill status-in-progress">In Progress</span>
                    </div>
                    <span style="font-family: var(--hd-font-mono); font-size: 11px; color: var(--hd-text-muted);">Assigned to Alexey Ivanov (Tier 3)</span>
                  </div>
                </div>

                <!-- System Affected & Diagnostic Overview -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; background: var(--hd-surface-dim); padding: 0.85rem 1rem; border-radius: var(--hd-radius-md); border: 1px solid var(--hd-surface-border); margin-bottom: 1.25rem;">
                  <div>
                    <div style="font-size: 10.5px; font-weight: 700; color: var(--hd-text-muted); text-transform: uppercase;">System Affected</div>
                    <div style="font-weight: 700; color: var(--hd-navy); font-size: 13px;">SCADA Gateway #3</div>
                    <div style="font-size: 11px; color: var(--hd-text-secondary);">Lipetsk Furnace #5 Bay</div>
                  </div>
                  <div>
                    <div style="font-size: 10.5px; font-weight: 700; color: var(--hd-text-muted); text-transform: uppercase;">Interface / Node IP</div>
                    <div style="font-family: var(--hd-font-mono); font-weight: 700; color: var(--hd-navy); font-size: 12px;">10.240.48.12:502</div>
                    <div style="font-size: 11px; color: var(--hd-text-secondary);">RS-485 Modbus Serial Bus B</div>
                  </div>
                  <div>
                    <div style="font-size: 10.5px; font-weight: 700; color: var(--hd-text-muted); text-transform: uppercase;">Error Classification</div>
                    <div style="font-weight: 700; color: var(--hd-priority-critical); font-size: 13px;">CRC Error Rate: 14.8%</div>
                    <div style="font-size: 11px; color: var(--hd-text-secondary);">Frame drop during thermal ramp</div>
                  </div>
                </div>

                <!-- Problem Description -->
                <div>
                  <h4 style="font-size: 12.5px; font-weight: 700; color: var(--hd-navy); margin-bottom: 0.4rem;">Incident Summary &amp; Impact Analysis</h4>
                  <p style="font-size: 13px; color: var(--hd-text-primary); line-height: 1.6;">
                    Telemetry frame drop detected on RS-485 bus #3 connecting the high-temperature pyrometer array in Lipetsk Hot Blast Furnace #5. Packet loss is exceeding 14.8% whenever the thermal chamber ramps past 1,450°C. This is triggering safety failsafe alarms and halting the automated continuous calibration pass.
                  </p>
                </div>
              </div>

              <!-- Conversation Thread with Message Bubbles -->
              <div class="hd-card conversation-card">
                <div class="card-header-row" style="margin-bottom: 0.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--hd-surface-border);">
                  <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <h3 class="card-title">Remediation Communication Thread</h3>
                    <span style="font-size: 11px; background: var(--hd-orange-light); color: var(--hd-orange-dark); padding: 1px 6px; border-radius: 10px; font-weight: 700;">4 Entries</span>
                  </div>
                  <span style="font-size: 11px; color: var(--hd-text-muted); font-family: var(--hd-font-mono);">Encrypted Channel · ISO 27001 Audit Logged</span>
                </div>

                <!-- Chat Bubble Thread Container -->
                <div class="chat-bubble-thread" id="chat-conversation-thread">
                  <!-- Message 1: Requester (Dr. Elena Rostova) -->
                  <div class="chat-msg-row requester-msg">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM" alt="Dr. Elena" class="chat-avatar" />
                    <div class="chat-bubble">
                      <div class="chat-msg-header">
                        <strong>Dr. Elena Rostova (Requester · Optical Calibration)</strong>
                        <span>08:30 MSK</span>
                      </div>
                      <p>
                        Alexey, we are seeing recurrent timeout errors from Pyrometer Node #4B during the 1,450°C cycle. The Modbus gateway is responding with 0x0B (Gateway Target Device Failed to Respond) error codes. We cannot certify the morning optical batch until the bus latency drops below 20ms.
                      </p>
                    </div>
                  </div>

                  <!-- Message 2: IT Tech (Alexey Ivanov) -->
                  <div class="chat-msg-row tech-msg">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" alt="Alexey" class="chat-avatar" />
                    <div class="chat-bubble">
                      <div class="chat-msg-header">
                        <strong>Alexey Ivanov (Lead IT Tech · Tier 3)</strong>
                        <span>08:42 MSK</span>
                      </div>
                      <p>
                        Understood Dr. Rostova. I checked the telemetry trace on Switch <code>SW-LIP-03</code> port <code>Eth12</code>. We are observing high CRC error rates caused by electromagnetic interference from Induction Coil #2 during the peak heating cycle.
                      </p>
                      <div style="background: rgba(0,0,0,0.25); border-radius: 4px; padding: 6px 10px; margin-top: 6px; font-family: var(--hd-font-mono); font-size: 11px; color: #7EE787;">
                        Diagnostic: RS485_BUS_3_CRC_ERR = 1,420 pkts/min [ABNORMAL]
                      </div>
                    </div>
                  </div>

                  <!-- Message 3: Requester (Dr. Elena Rostova) -->
                  <div class="chat-msg-row requester-msg">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM" alt="Dr. Elena" class="chat-avatar" />
                    <div class="chat-bubble">
                      <div class="chat-msg-header">
                        <strong>Dr. Elena Rostova (Requester · Optical Calibration)</strong>
                        <span>08:50 MSK</span>
                      </div>
                      <p>
                        Can you switch the primary polling channel to the redundant optical isolator link (Channel B2) on the secondary DIN-rail multiplexer?
                      </p>
                    </div>
                  </div>

                  <!-- Message 4: IT Tech (Alexey Ivanov) -->
                  <div class="chat-msg-row tech-msg">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" alt="Alexey" class="chat-avatar" />
                    <div class="chat-bubble">
                      <div class="chat-msg-header">
                        <strong>Alexey Ivanov (Lead IT Tech · Tier 3)</strong>
                        <span>09:05 MSK</span>
                      </div>
                      <p>
                        Executing failover to Channel B2 right now. I have set the baud rate to 115200 with parity even. Awaiting test pulse verification from your calibration rig.
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Interactive Reply Box -->
                <div style="border-top: 1px solid var(--hd-surface-border); padding-top: 1rem; display: flex; flex-direction: column; gap: 0.75rem;">
                  <textarea id="chat-reply-input" placeholder="Type technical response, diagnostic command output, or resolution steps to requester..." rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid var(--hd-surface-border); border-radius: var(--hd-radius-md); font-size: 13px; line-height: 1.5; resize: vertical; background: #FFFFFF; color: var(--hd-text-primary);"></textarea>
                  <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; gap: 0.5rem;">
                      <button class="btn btn-outline btn-sm" onclick="document.getElementById('chat-reply-input').value += 'Channel B2 link validated: 0 dropped packets in 500 frame burst. '; ">
                        <span>+ Insert Link Test Result</span>
                      </button>
                      <button class="btn btn-outline btn-sm" onclick="document.getElementById('chat-reply-input').value += 'Telemetry restored within normal threshold (latency &lt; 8ms). '; ">
                        <span>+ Insert Normal Telemetry Macro</span>
                      </button>
                    </div>
                    <button class="btn btn-primary-amber" onclick="window.hdApp.sendMessage()">
                      <span>Transmit Message →</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- ==================================================================
                 RIGHT 30%: SLA COUNTDOWN TIMER, ESCALATE & RESOLUTION CONTROLS
                 ================================================================== -->
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
              <!-- SLA Countdown Timer Widget -->
              <div class="sla-timer-card">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                  <span style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: var(--hd-orange-bright);">
                    Critical SLA Target Window
                  </span>
                  <span style="font-family: var(--hd-font-mono); font-size: 11px; background: rgba(255,123,114,0.2); color: #FF7B72; padding: 1px 6px; border-radius: 4px; font-weight: 700;">
                    P1 · &lt; 2h Target
                  </span>
                </div>

                <div style="display: flex; align-items: baseline; gap: 0.5rem;">
                  <span class="sla-timer-val" id="live-sla-timer">01:42:15</span>
                  <span style="font-size: 11.5px; color: var(--hd-text-inverse-muted);">Remaining</span>
                </div>

                <!-- Progress Bar -->
                <div>
                  <div style="height: 6px; width: 100%; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden;">
                    <div style="height: 100%; width: 78%; background: linear-gradient(90deg, var(--hd-orange) 0%, #FF7B72 100%); border-radius: 3px;"></div>
                  </div>
                  <div style="display: flex; justify-content: space-between; font-size: 10px; font-family: var(--hd-font-mono); color: var(--hd-text-inverse-muted); margin-top: 4px;">
                    <span>Created 08:30 MSK</span>
                    <span>Breach at 10:30 MSK</span>
                  </div>
                </div>
              </div>

              <!-- Escalation & Resolution Actions Card -->
              <div class="hd-card" style="display: flex; flex-direction: column; gap: 1rem;">
                <h3 class="card-title" style="font-size: 13.5px;">Incident Actions &amp; Governance</h3>

                <!-- Red-Outlined Escalate to Governance Button -->
                <button class="btn btn-outline-red" style="width: 100%;" onclick="window.hdApp.showToast('Escalated to Governance', 'Incident TICK-8819 transferred to Operational Governance Board &amp; Plant Manager.', 'critical')">
                  <span>🚨 Escalate to Governance</span>
                </button>

                <div style="height: 1px; background: var(--hd-surface-border);"></div>

                <!-- Resolution Notes Textarea -->
                <div>
                  <label for="resolution-notes" style="display: block; font-size: 12px; font-weight: 600; color: var(--hd-navy); margin-bottom: 0.4rem;">
                    Resolution &amp; Root Cause Notes:
                  </label>
                  <textarea id="resolution-notes" placeholder="Enter root cause analysis, corrective action applied, and verification steps before closing..." rows="4" style="width: 100%; padding: 0.65rem; border: 1px solid var(--hd-surface-border); border-radius: var(--hd-radius-md); font-size: 12px; line-height: 1.4; resize: vertical; background: #FFFFFF; color: var(--hd-text-primary);">Switched RS-485 Modbus bus link from primary copper pair to optically-isolated Channel B2. Validated 0 frame drop during 1,480°C furnace test.</textarea>
                </div>

                <!-- Green Mark Resolved Button -->
                <button class="btn btn-green" style="width: 100%; padding: 0.75rem;" onclick="window.hdApp.showToast('Incident Resolved', 'TICK-8819 marked as RESOLVED. Audit ledger and requester notification sent.', 'green')">
                  <span>✓ Mark Resolved &amp; Close Ticket</span>
                </button>
              </div>

              <!-- Infrastructure Context Card -->
              <div class="hd-card">
                <h3 class="card-title" style="font-size: 13px; margin-bottom: 0.75rem;">Node &amp; Asset Hardware Context</h3>
                <div style="display: flex; flex-direction: column; gap: 0.6rem; font-size: 12px;">
                  <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--hd-surface-border-light); padding-bottom: 0.35rem;">
                    <span style="color: var(--hd-text-muted);">Asset Tag:</span>
                    <span style="font-family: var(--hd-font-mono); font-weight: 600; color: var(--hd-navy);">VP-GW-LIP-03</span>
                  </div>
                  <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--hd-surface-border-light); padding-bottom: 0.35rem;">
                    <span style="color: var(--hd-text-muted);">Hardware Model:</span>
                    <span style="font-weight: 600; color: var(--hd-navy);">Moxa MGate MB3170</span>
                  </div>
                  <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--hd-surface-border-light); padding-bottom: 0.35rem;">
                    <span style="color: var(--hd-text-muted);">Firmware Revision:</span>
                    <span style="font-family: var(--hd-font-mono); font-weight: 600; color: var(--hd-navy);">v4.2.1-sec-hardened</span>
                  </div>
                  <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--hd-surface-border-light); padding-bottom: 0.35rem;">
                    <span style="color: var(--hd-text-muted);">Facility Location:</span>
                    <span style="font-weight: 600; color: var(--hd-navy);">Lipetsk Plant · Bay 5</span>
                  </div>
                  <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--hd-text-muted);">Maintenance SLA:</span>
                    <span style="font-weight: 700; color: var(--hd-success);">Gold Tier 24/7 (2h SLA)</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
</body>
</html>
