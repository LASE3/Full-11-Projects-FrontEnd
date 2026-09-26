<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('IT');
?>
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
            <img alt="VOSTOKPRIBOR Official Mark" class="brand-logo-img" src="assets/logo.svg" />
            <div class="brand-divider"></div>
            <div class="brand-title-group">
              <div class="brand-title-row">
                <span class="brand-name">VOSTOKPRIBOR</span>
                <span class="system-tag">IT · SYS 08</span>
              </div>
              <div class="brand-subline">
                <span class="status-dot-pulse"></span>
                <span>helpdesk.vostokpribor.local</span>
                <span class="hd-opacity-50" >|</span>
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
          <div class="pipeline-sync-badge hd-badge-telemetry" >
            <span class="hd-status-success" >●</span>
            <span>SLA: <strong>98.4% Compliant</strong></span>
          </div>

          <button class="icon-button" title="Incident Telemetry Notifications" onclick="window.hdApp.showToast('Critical Alert', 'SCADA Gateway Node #3 packet loss detected in Lipetsk Bay.', 'critical')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
              <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
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

        <!-- Top Bar Sign Out -->
        <a href="../api/logout.php?system=IT%20Helpdesk&redirect=../IT%20Helpdesk/login.php" class="top-signout-btn" title="Sign Out of IT Helpdesk" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" ><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
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

            <!-- Screen 2: Ticket Queue (Active for detail view) -->
            <a href="TicketQueue.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                  </svg>
                </span>
                <span>Ticket Queue</span>
              </div>
              <span class="sidebar-badge badge-orange">34</span>
            </a>

            <!-- My Tickets -->
            <a href="MyTickets.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                  </svg>
                </span>
                <span>My Tickets</span>
              </div>
              <span class="sidebar-badge badge-red">8</span>
            </a>

            <!-- Knowledge Base -->
            <a href="KnowledgeBase.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                  </svg>
                </span>
                <span>Knowledge Base</span>
              </div>
              <span class="sidebar-badge">142</span>
            </a>

            <!-- Asset Management -->
            <a href="AssetManagement.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="2" width="20" height="8" rx="2" ry="2" />
                    <rect x="2" y="14" width="20" height="8" rx="2" ry="2" />
                    <line x1="6" y1="6" x2="6.01" y2="6" />
                    <line x1="6" y1="18" x2="6.01" y2="18" />
                  </svg>
                </span>
                <span>Asset Management</span>
              </div>
              <span class="sidebar-badge">1,820</span>
            </a>

            <!-- SLA Reports -->
            <a href="SLAReports.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                  </svg>
                </span>
                <span>SLA Reports</span>
              </div>
              <span class="sidebar-badge badge-green">98.4%</span>
            </a>
          </nav>
        </div>


        <div class="sidebar-section-title hd-mt-4" >Unified Ecosystem</div>
        <nav class="sidebar-nav hd-mb-2" >
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
            <span class="sidebar-badge hd-text-xs" >SYS 01</span>
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
            <span class="sidebar-badge hd-text-xs" >SYS 04</span>
          </a>
        </nav>
        <!-- Log Out -->

        <div class="sidebar-footer">
          <div class="security-widget-card">
            <div class="security-widget-header">
              <span>Incident Response Gateway</span>
              <span class="security-badge-status">● LIVE</span>
            </div>
            <div class="hd-text-inverse-muted-sm" >
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
                <a class="hd-link-back" href="TicketQueue.php" >
                  <span>← Back to Ticket Queue</span>
                </a>
                <span class="breadcrumb-separator">/</span>
                <span>TICK-8819</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Triage &amp; Incident Remediation</span>
              </div>
              <div class="hd-flex-gap-md-mt" >
                <h1 class="page-title hd-flex-gap-md" >
                  <span class="hd-mono-orange" >TICK-8819</span>
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
            <div class="hd-flex-col-gap-15" >
              <!-- Ticket Header Metadata Card -->
              <div class="hd-card hd-card-ticket-critical" >
                <div class="hd-ticket-header" >
                  <div class="hd-flex-gap-1" >
                    <img class="hd-avatar-orange" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM" alt="Elena Rostova"  />
                    <div>
                      <div class="hd-title-15-navy" >Dr. Elena Rostova</div>
                      <div class="hd-text-secondary-12" >Chief Optical Calibration Architect · Dept of Laser Pyrometry</div>
                      <div class="hd-mono-muted-sm" >
                        Reported: Today at 08:30:14 MSK · Incident Channel: Automated PLC Alarm Hook
                      </div>
                    </div>
                  </div>

                  <!-- Priority & Status Badges -->
                  <div class="hd-flex-end-col" >
                    <div class="hd-flex-gap-sm" >
                      <span class="priority-badge priority-critical">CRITICAL (P1)</span>
                      <span class="status-pill status-in-progress">In Progress</span>
                    </div>
                    <span class="hd-mono-muted-11" >Assigned to Alexey Ivanov (Tier 3)</span>
                  </div>
                </div>

                <!-- System Affected & Diagnostic Overview -->
                <div class="hd-grid-meta-box" >
                  <div>
                    <div class="hd-caption-bold-105" >System Affected</div>
                    <div class="hd-bold-navy-13" >SCADA Gateway #3</div>
                    <div class="hd-text-secondary-11" >Lipetsk Furnace #5 Bay</div>
                  </div>
                  <div>
                    <div class="hd-caption-bold-105" >Interface / Node IP</div>
                    <div class="hd-mono-bold-navy-12" >10.240.48.12:502</div>
                    <div class="hd-text-secondary-11" >RS-485 Modbus Serial Bus B</div>
                  </div>
                  <div>
                    <div class="hd-caption-bold-105" >Error Classification</div>
                    <div class="hd-bold-critical-13" >CRC Error Rate: 14.8%</div>
                    <div class="hd-text-secondary-11" >Frame drop during thermal ramp</div>
                  </div>
                </div>

                <!-- Problem Description -->
                <div>
                  <h4 class="hd-title-125-navy" >Incident Summary &amp; Impact Analysis</h4>
                  <p class="hd-body-text-13" >
                    Telemetry frame drop detected on RS-485 bus #3 connecting the high-temperature pyrometer array in Lipetsk Hot Blast Furnace #5. Packet loss is exceeding 14.8% whenever the thermal chamber ramps past 1,450°C. This is triggering safety failsafe alarms and halting the automated continuous calibration pass.
                  </p>
                </div>
              </div>

              <!-- Conversation Thread with Message Bubbles -->
              <div class="hd-card conversation-card">
                <div class="card-header-row hd-divider-subtle-mb" >
                  <div class="hd-flex-gap-sm" >
                    <h3 class="card-title">Remediation Communication Thread</h3>
                    <span class="hd-badge-orange-pill" >4 Entries</span>
                  </div>
                  <span class="hd-audit-notice">Encrypted Channel · ISO 27001 Audit Logged</span>
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
                      <div class="hd-code-snippet-box" >
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
                <div class="hd-reply-box-wrap" >
                  <textarea class="hd-form-control-textarea" id="chat-reply-input" placeholder="Type technical response, diagnostic command output, or resolution steps to requester..." rows="3" ></textarea>
                  <div class="hd-flex-between-center" >
                    <div class="hd-gap-sm" >
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
            <div class="hd-flex-col-gap-125" >
              <!-- SLA Countdown Timer Widget -->
              <div class="sla-timer-card">
                <div class="hd-flex-between-center" >
                  <span class="hd-heading-orange-sm" >
                    Critical SLA Target Window
                  </span>
                  <span class="hd-badge-sla-danger" >
                    P1 · &lt; 2h Target
                  </span>
                </div>

                <div class="hd-flex-baseline-sm" >
                  <span class="sla-timer-val" id="live-sla-timer">01:42:15</span>
                  <span class="hd-text-inverse-muted-115" >Remaining</span>
                </div>

                <!-- Progress Bar -->
                <div>
                  <div class="hd-progress-track-inverse" >
                    <div class="hd-progress-fill-78" ></div>
                  </div>
                  <div class="hd-progress-meta-row" >
                    <span>Created 08:30 MSK</span>
                    <span>Breach at 10:30 MSK</span>
                  </div>
                </div>
              </div>

              <!-- Escalation & Resolution Actions Card -->
              <div class="hd-card hd-flex-col-gap-md" >
                <h3 class="card-title hd-text-135" >Incident Actions &amp; Governance</h3>

                <!-- Red-Outlined Escalate to Governance Button -->
                <button class="btn btn-outline-red w-full" onclick="window.hdApp.showToast('Escalated to Governance', 'Incident TICK-8819 transferred to Operational Governance Board &amp; Plant Manager.', 'critical')">
                  <span>🚨 Escalate to Governance</span>
                </button>

                <div class="hd-divider-line" ></div>

                <!-- Resolution Notes Textarea -->
                <div>
                  <label class="hd-field-label-12" for="resolution-notes" >
                    Resolution &amp; Root Cause Notes:
                  </label>
                  <textarea class="hd-form-control-full" id="resolution-notes" placeholder="Enter root cause analysis, corrective action applied, and verification steps before closing..." rows="4" >Switched RS-485 Modbus bus link from primary copper pair to optically-isolated Channel B2. Validated 0 frame drop during 1,480°C furnace test.</textarea>
                </div>

                <!-- Green Mark Resolved Button -->
                <button class="btn btn-green hd-p-75-full"  onclick="window.hdApp.showToast('Incident Resolved', 'TICK-8819 marked as RESOLVED. Audit ledger and requester notification sent.', 'green')">
                  <span>✓ Mark Resolved &amp; Close Ticket</span>
                </button>
              </div>

              <!-- Infrastructure Context Card -->
              <div class="hd-card">
                <h3 class="card-title hd-text-13-mb" >Node &amp; Asset Hardware Context</h3>
                <div class="hd-meta-field-list" >
                  <div class="hd-dashed-meta-row" >
                    <span class="hd-text-muted" >Asset Tag:</span>
                    <span class="hd-mono-semibold-navy" >VP-GW-LIP-03</span>
                  </div>
                  <div class="hd-dashed-meta-row" >
                    <span class="hd-text-muted" >Hardware Model:</span>
                    <span class="hd-font-semibold-navy" >Moxa MGate MB3170</span>
                  </div>
                  <div class="hd-dashed-meta-row" >
                    <span class="hd-text-muted" >Firmware Revision:</span>
                    <span class="hd-mono-semibold-navy" >v4.2.1-sec-hardened</span>
                  </div>
                  <div class="hd-dashed-meta-row" >
                    <span class="hd-text-muted" >Facility Location:</span>
                    <span class="hd-font-semibold-navy" >Lipetsk Plant · Bay 5</span>
                  </div>
                  <div class="hd-flex-between" >
                    <span class="hd-text-muted" >Maintenance SLA:</span>
                    <span class="hd-bold-success" >Gold Tier 24/7 (2h SLA)</span>
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