<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('IT');
require_once __DIR__ . '/api/db_helper.php';

$pdo = getItDb();
$tktId = trim($_GET['id'] ?? 'TICK-8819');

// Query ticket
$stmt = $pdo->prepare("
    SELECT 
        t.*,
        COALESCE(e.full_name, t.assigned_emp_id, 'Unassigned') AS assigned_tech_name,
        e.job_title AS assigned_tech_role
    FROM tickets t
    LEFT JOIN employees e ON t.assigned_emp_id = e.emp_id
    WHERE t.tkt_id = :id
");
$stmt->execute([':id' => $tktId]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    $fallbackStmt = $pdo->query("
        SELECT 
            t.*,
            COALESCE(e.full_name, t.assigned_emp_id, 'Unassigned') AS assigned_tech_name,
            e.job_title AS assigned_tech_role
        FROM tickets t
        LEFT JOIN employees e ON t.assigned_emp_id = e.emp_id
        ORDER BY t.created_at DESC 
        LIMIT 1
    ");
    $ticket = $fallbackStmt->fetch(PDO::FETCH_ASSOC);
    $tktId = $ticket['tkt_id'] ?? 'TICK-8819';
}

// Fetch comments
$cmtStmt = $pdo->prepare("
    SELECT 
        c.*,
        COALESCE(e.full_name, c.author_name, 'IT Support') AS display_author,
        COALESCE(e.job_title, c.author_role, 'Support Engineer') AS display_role
    FROM ticket_comments c
    LEFT JOIN employees e ON c.author_emp_id = e.emp_id
    WHERE c.tkt_id = :id
    ORDER BY c.created_at ASC, c.comment_id ASC
");
$cmtStmt->execute([':id' => $tktId]);
$comments = $cmtStmt->fetchAll(PDO::FETCH_ASSOC);

$prio = $ticket['priority'] ?? 'Medium';
$status = $ticket['status'] ?? 'Open';
$prioClass = match($prio) {
    'Critical' => 'priority-critical',
    'High' => 'priority-high',
    'Medium' => 'priority-medium',
    'Low' => 'priority-low',
    default => 'priority-medium'
};
$statusClass = match($status) {
    'Open' => 'status-open',
    'InProgress' => 'status-in-progress',
    'Escalated' => 'priority-critical',
    'Resolved' => 'badge-green',
    default => 'status-open'
};

// Dynamic Sidebar Counts
$sbStats = getItSidebarStats($pdo);
$openCount = $sbStats['open_count'];
$myTicketsCount = $sbStats['my_tickets_count'];
$assetCount = $sbStats['asset_count'];
$kbCount = $sbStats['kb_count'];
$slaPct = $sbStats['sla_pct'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VOSTOKPRIBOR IT Helpdesk · Ticket Detail <?= htmlspecialchars($ticket['tkt_id']) ?></title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
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
                <span class="system-tag">IT · SYS 08</span>
              </div>
              <div class="brand-subline">
                <span class="status-dot-pulse"></span>
                <span>helpdesk.vostokpribor.local</span>
                <span class="hd-opacity-50">|</span>
                <span>SUPPORT OPERATIONS</span>
              </div>
            </div>
          </a>
        </div>

        <div class="top-search-bar">
          <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search ticket ID, requester, SCADA node, knowledge base..." />
            <span class="search-kbd">Ctrl+K</span>
          </div>
        </div>

        <div class="top-nav__actions">
          <div class="pipeline-sync-badge hd-badge-telemetry">
            <span class="hd-status-success">●</span>
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
        <a href="../api/logout.php?system=IT%20Helpdesk&redirect=../IT%20Helpdesk/login.php" class="top-signout-btn" title="Sign Out of IT Helpdesk" onclick="(function(){sessionStorage.clear();localStorage.clear();})()"><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
      </div>
    </header>

    <div class="main-layout">
      <!-- LEFT SIDEBAR NAVIGATION -->
      <aside class="sidebar">
        <div>
          <div class="sidebar-section-title">IT Support Operations</div>
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
              <span class="sidebar-badge badge-orange"><?= $openCount ?></span>
            </a>

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
              <span class="sidebar-badge badge-red"><?= $myTicketsCount ?></span>
            </a>

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
              <span class="sidebar-badge"><?= $kbCount ?></span>
            </a>

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
              <span class="sidebar-badge"><?= $assetCount ?></span>
            </a>

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
              <span class="sidebar-badge badge-green"><?= $slaPct ?>%</span>
            </a>

            <a href="Integrations.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00E5FF" stroke-width="2">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                  </svg>
                </span>
                <span class="hd-nav-integrations">System Integrations</span>
              </div>
              <span class="sidebar-badge hd-badge-integrations">SYS09</span>
            </a>
          </nav>
        </div>

        <div class="sidebar-section-title hd-mt-4">Unified Ecosystem</div>
        <nav class="sidebar-nav hd-mb-2">
          <a href="../VOSTOKPRIBOR Corporate Web Platform/index.php" class="sidebar-nav-item">
            <div class="sidebar-item-left">
              <span class="sidebar-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="2" y1="12" x2="22" y2="12" />
                  <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1 4-10z" />
                </svg>
              </span>
              <span>Corporate Platform</span>
            </div>
            <span class="sidebar-badge hd-text-xs">SYS 01</span>
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
            <span class="sidebar-badge hd-text-xs">SYS 04</span>
          </a>
        </nav>

        <div class="sidebar-footer">
          <div class="security-widget-card">
            <div class="security-widget-header">
              <span>Incident Response Gateway</span>
              <span class="security-badge-status">● LIVE</span>
            </div>
            <div class="hd-text-inverse-muted-sm">
              Active Incident: <strong><?= htmlspecialchars($ticket['tkt_id']) ?></strong>
            </div>
          </div>
        </div>
      </aside>

      <!-- MAIN CONTENT AREA -->
      <main class="content-wrapper">
        <div class="portal-container">
          <!-- Page Header & Contextual Back Navigation -->
          <div class="page-header">
            <div class="page-header-info">
              <div class="breadcrumb-trail">
                <a class="hd-link-back" href="TicketQueue.php">
                  <span>← Back to Ticket Queue</span>
                </a>
                <span class="breadcrumb-separator">/</span>
                <span><?= htmlspecialchars($ticket['tkt_id']) ?></span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Triage &amp; Incident Remediation</span>
              </div>
              <div class="hd-flex-gap-md-mt">
                <h1 class="page-title hd-flex-gap-md">
                  <span class="hd-mono-orange"><?= htmlspecialchars($ticket['tkt_id']) ?></span>
                  <span><?= htmlspecialchars($ticket['title'] ?: $ticket['source_system']) ?></span>
                </h1>
              </div>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-outline" id="btn-edit-current-ticket">
                <span class="material-symbols-outlined" style="font-size: 15px; vertical-align: middle;">edit</span>
                <span>Edit Incident Details</span>
              </button>
              <button class="btn btn-outline" onclick="window.hdApp.showToast('Incident Audit', 'Exported cryptographic syslog payload for <?= htmlspecialchars($ticket['tkt_id']) ?>.')">
                <span>📋 Export Syslog Trace</span>
              </button>
            </div>
          </div>

          <!-- Two-Column Layout (70% Left / 30% Right) -->
          <div class="detail-layout-grid">
            <!-- LEFT 70%: TICKET HEADER & CONVERSATION THREAD -->
            <div class="hd-flex-col-gap-15">
              <!-- Ticket Header Metadata Card -->
              <div class="hd-card <?= ($prio === 'Critical') ? 'hd-card-ticket-critical' : '' ?>">
                <div class="hd-ticket-header">
                  <div class="hd-flex-gap-1">
                    <div class="hd-stat-column-box" style="width: 44px; height: 44px; border-radius: 50%; background: #0f2438; color: #fff; font-size: 14px; font-weight: bold;">
                      <?= strtoupper(substr($ticket['requester_name'] ?: 'EP', 0, 2)) ?>
                    </div>
                    <div>
                      <div class="hd-title-15-navy"><?= htmlspecialchars($ticket['requester_name'] ?: 'Authorized Staff') ?></div>
                      <div class="hd-text-secondary-12"><?= htmlspecialchars($ticket['requester_role'] ?: 'Operations') ?> &bull; <?= htmlspecialchars($ticket['requester_dept'] ?: 'ENG') ?></div>
                      <div class="hd-mono-muted-sm">
                        Reported: <?= date('Y-m-d H:i:s', strtotime($ticket['created_at'])) ?> &bull; System: <?= htmlspecialchars($ticket['source_system']) ?>
                      </div>
                    </div>
                  </div>

                  <!-- Priority & Status Badges -->
                  <div class="hd-flex-end-col">
                    <div class="hd-flex-gap-sm">
                      <span class="priority-badge <?= $prioClass ?>"><?= strtoupper(htmlspecialchars($prio)) ?></span>
                      <span class="status-pill <?= $statusClass ?>" id="ticket-detail-status-pill"><?= htmlspecialchars($status) ?></span>
                    </div>
                    <span class="hd-mono-muted-11">Assigned to <?= htmlspecialchars($ticket['assigned_tech_name']) ?></span>
                  </div>
                </div>

                <!-- System Affected & Diagnostic Overview -->
                <div class="hd-grid-meta-box">
                  <div>
                    <div class="hd-caption-bold-105">System Affected</div>
                    <div class="hd-bold-navy-13"><?= htmlspecialchars($ticket['source_system']) ?></div>
                    <div class="hd-text-secondary-11">Industrial Node / Gateway</div>
                  </div>
                  <div>
                    <div class="hd-caption-bold-105">Ticket ID</div>
                    <div class="hd-mono-bold-navy-12"><?= htmlspecialchars($ticket['tkt_id']) ?></div>
                    <div class="hd-text-secondary-11">Status: <?= htmlspecialchars($status) ?></div>
                  </div>
                  <div>
                    <div class="hd-caption-bold-105">SLA Target Deadline</div>
                    <div class="hd-bold-critical-13"><?= $ticket['sla_deadline'] ? date('H:i:s MSK', strtotime($ticket['sla_deadline'])) : '< 2 Hours Target' ?></div>
                    <div class="hd-text-secondary-11">Tier Response Standard</div>
                  </div>
                </div>

                <!-- Problem Description -->
                <div>
                  <h4 class="hd-title-125-navy">Incident Summary &amp; Impact Analysis</h4>
                  <p class="hd-body-text-13">
                    <?= nl2br(htmlspecialchars($ticket['description'] ?: 'No diagnostic summary provided.')) ?>
                  </p>
                </div>
              </div>

              <!-- Conversation Thread with Message Bubbles (Dynamic from Database) -->
              <div class="hd-card conversation-card">
                <div class="card-header-row hd-divider-subtle-mb">
                  <div class="hd-flex-gap-sm">
                    <h3 class="card-title">Remediation Communication Thread</h3>
                    <span class="hd-badge-orange-pill" id="thread-entry-count"><?= count($comments) ?> Entries</span>
                  </div>
                  <span class="hd-audit-notice">Encrypted Channel &bull; Database: `ticket_comments`</span>
                </div>

                <!-- Chat Bubble Thread Container -->
                <div class="chat-bubble-thread" id="chat-conversation-thread">
                  <?php if (empty($comments)): ?>
                  <div style="text-align: center; color: var(--hd-text-muted); padding: 20px;">No communication logs in this thread yet. Send a message below.</div>
                  <?php else: ?>
                  <?php foreach ($comments as $c): 
                    $isTech = ($c['author_type'] === 'tech' || !empty($c['author_emp_id']));
                    $msgRowClass = $isTech ? 'tech-msg' : 'requester-msg';
                  ?>
                  <div class="chat-msg-row <?= $msgRowClass ?>">
                    <div class="hd-stat-column-box" style="width: 32px; height: 32px; border-radius: 50%; background: <?= $isTech ? '#C97A3D' : '#0f2438' ?>; color: #fff; font-size: 11px; font-weight: bold; flex-shrink: 0;">
                      <?= strtoupper(substr($c['display_author'] ?: 'IT', 0, 2)) ?>
                    </div>
                    <div class="chat-bubble">
                      <div class="chat-msg-header">
                        <strong><?= htmlspecialchars($c['display_author']) ?> (<?= htmlspecialchars($c['display_role']) ?>)</strong>
                        <span><?= date('H:i MSK', strtotime($c['created_at'])) ?></span>
                      </div>
                      <p><?= nl2br(htmlspecialchars($c['comment_text'])) ?></p>
                    </div>
                  </div>
                  <?php endforeach; ?>
                  <?php endif; ?>
                </div>

                <!-- Interactive Reply Box -->
                <div class="hd-reply-box-wrap">
                  <textarea class="hd-form-control-textarea" id="chat-reply-input" placeholder="Type technical response, diagnostic command output, or resolution steps to requester..." rows="3"></textarea>
                  <div class="hd-flex-between-center">
                    <div class="hd-gap-sm">
                      <button class="btn btn-outline btn-sm" type="button" onclick="document.getElementById('chat-reply-input').value += 'Channel B2 link validated: 0 dropped packets in 500 frame burst. '; ">
                        <span>+ Link Test Macro</span>
                      </button>
                      <button class="btn btn-outline btn-sm" type="button" onclick="document.getElementById('chat-reply-input').value += 'Telemetry restored within normal threshold (latency < 8ms). '; ">
                        <span>+ Telemetry OK Macro</span>
                      </button>
                    </div>
                    <button class="btn btn-primary-amber" id="btn-send-message" data-tid="<?= htmlspecialchars($ticket['tkt_id']) ?>">
                      <span>Transmit Message &rarr;</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- RIGHT 30%: SLA COUNTDOWN & RESOLUTION CONTROLS -->
            <div class="hd-flex-col-gap-125">
              <!-- SLA Countdown Timer Widget -->
              <div class="sla-timer-card">
                <div class="hd-flex-between-center">
                  <span class="hd-heading-orange-sm">
                    Critical SLA Target Window
                  </span>
                  <span class="hd-badge-sla-danger">
                    <?= htmlspecialchars($prio) ?> &bull; Target
                  </span>
                </div>

                <div class="hd-flex-baseline-sm">
                  <span class="sla-timer-val" id="live-sla-timer">01:42:15</span>
                  <span class="hd-text-inverse-muted-115">Remaining</span>
                </div>

                <div>
                  <div class="hd-progress-track-inverse">
                    <div class="hd-progress-fill-78"></div>
                  </div>
                  <div class="hd-progress-meta-row">
                    <span>Created <?= date('H:i MSK', strtotime($ticket['created_at'])) ?></span>
                    <span>Deadline <?= $ticket['sla_deadline'] ? date('H:i MSK', strtotime($ticket['sla_deadline'])) : 'In Progress' ?></span>
                  </div>
                </div>
              </div>

              <!-- Escalation & Resolution Actions Card -->
              <div class="hd-card hd-flex-col-gap-md">
                <h3 class="card-title hd-text-135">Incident Actions &amp; Governance</h3>

                <!-- Red-Outlined Escalate to Governance Button -->
                <button class="btn btn-outline-red w-full" id="btn-escalate-ticket" data-tid="<?= htmlspecialchars($ticket['tkt_id']) ?>">
                  <span>🚨 Escalate to Governance</span>
                </button>

                <div class="hd-divider-line"></div>

                <!-- Resolution Notes Textarea -->
                <div>
                  <label class="hd-field-label-12" for="resolution-notes">
                    Resolution &amp; Root Cause Notes:
                  </label>
                  <textarea class="hd-form-control-full" id="resolution-notes" placeholder="Enter root cause analysis, corrective action applied, and verification steps before closing..." rows="4"><?= htmlspecialchars($ticket['resolution_notes'] ?: 'Switched RS-485 Modbus bus link from primary copper pair to optically-isolated Channel B2. Validated 0 frame drop during 1,480°C furnace test.') ?></textarea>
                </div>

                <!-- Green Mark Resolved Button -->
                <button class="btn btn-green hd-p-75-full" id="btn-resolve-ticket" data-tid="<?= htmlspecialchars($ticket['tkt_id']) ?>">
                  <span>✓ Mark Resolved &amp; Close Ticket</span>
                </button>
              </div>

              <!-- Infrastructure Context Card -->
              <div class="hd-card">
                <h3 class="card-title hd-text-13-mb">Node &amp; Asset Hardware Context</h3>
                <div class="hd-meta-field-list">
                  <div class="hd-dashed-meta-row">
                    <span class="hd-text-muted">System Ref:</span>
                    <span class="hd-mono-semibold-navy"><?= htmlspecialchars($ticket['source_system']) ?></span>
                  </div>
                  <div class="hd-dashed-meta-row">
                    <span class="hd-text-muted">Hardware Tag:</span>
                    <span class="hd-font-semibold-navy">VP-GW-LIP-03</span>
                  </div>
                  <div class="hd-dashed-meta-row">
                    <span class="hd-text-muted">Firmware Revision:</span>
                    <span class="hd-mono-semibold-navy">v4.2.1-sec-hardened</span>
                  </div>
                  <div class="hd-dashed-meta-row">
                    <span class="hd-text-muted">Assigned Tech:</span>
                    <span class="hd-font-semibold-navy"><?= htmlspecialchars($ticket['assigned_tech_name']) ?></span>
                  </div>
                  <div class="hd-flex-between">
                    <span class="hd-text-muted">Maintenance SLA:</span>
                    <span class="hd-bold-success">Gold Tier 24/7</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- EDIT TICKET MODAL -->
  <div class="hd-modal-overlay" id="editTicketModal">
    <div class="hd-modal-dialog">
      <div class="hd-modal-header">
        <h3>
          <span class="material-symbols-outlined">edit_document</span>
          Edit Incident Details: <?= htmlspecialchars($ticket['tkt_id']) ?>
        </h3>
        <button class="hd-modal-close" id="btnCloseEditTicketModal">&times;</button>
      </div>
      <div class="hd-modal-body">
        <input type="hidden" id="editTktId" value="<?= htmlspecialchars($ticket['tkt_id']) ?>">
        
        <div class="hd-form-group">
          <label for="editTktTitle">Incident Title *</label>
          <input class="hd-form-input" id="editTktTitle" type="text" value="<?= htmlspecialchars($ticket['title'] ?: $ticket['source_system']) ?>" required>
        </div>

        <div class="hd-form-grid-2">
          <div class="hd-form-group">
            <label for="editTktSystem">System Affected *</label>
            <input class="hd-form-input" id="editTktSystem" type="text" value="<?= htmlspecialchars($ticket['source_system']) ?>" required>
          </div>
          <div class="hd-form-group">
            <label for="editTktPriority">Priority Level</label>
            <select class="hd-form-select" id="editTktPriority">
              <option value="Critical" <?= ($prio === 'Critical') ? 'selected' : '' ?>>Critical</option>
              <option value="High" <?= ($prio === 'High') ? 'selected' : '' ?>>High</option>
              <option value="Medium" <?= ($prio === 'Medium') ? 'selected' : '' ?>>Medium</option>
              <option value="Low" <?= ($prio === 'Low') ? 'selected' : '' ?>>Low</option>
            </select>
          </div>
        </div>

        <div class="hd-form-grid-2">
          <div class="hd-form-group">
            <label for="editTktStatus">Incident Status</label>
            <select class="hd-form-select" id="editTktStatus">
              <option value="Open" <?= ($status === 'Open') ? 'selected' : '' ?>>Open</option>
              <option value="InProgress" <?= ($status === 'InProgress') ? 'selected' : '' ?>>In Progress</option>
              <option value="Escalated" <?= ($status === 'Escalated') ? 'selected' : '' ?>>Escalated</option>
              <option value="Resolved" <?= ($status === 'Resolved') ? 'selected' : '' ?>>Resolved</option>
            </select>
          </div>
          <div class="hd-form-group">
            <label for="editTktTech">Assigned Technician</label>
            <select class="hd-form-select" id="editTktTech">
              <option value="EMP-1018" <?= ($ticket['assigned_emp_id'] === 'EMP-1018') ? 'selected' : '' ?>>Alexey Ivanov</option>
              <option value="EMP-1004" <?= ($ticket['assigned_emp_id'] === 'EMP-1004') ? 'selected' : '' ?>>Dmitry Popov</option>
              <option value="EMP-1002" <?= ($ticket['assigned_emp_id'] === 'EMP-1002') ? 'selected' : '' ?>>Sofia Volkova</option>
              <option value="">Unassigned</option>
            </select>
          </div>
        </div>

        <div class="hd-form-group">
          <label for="editTktDesc">Problem Description</label>
          <textarea class="hd-form-textarea" id="editTktDesc" rows="3"><?= htmlspecialchars($ticket['description'] ?: '') ?></textarea>
        </div>
      </div>
      <div class="hd-modal-footer">
        <button class="btn btn-outline" id="btnCancelEditTicketModal">Cancel</button>
        <button class="btn btn-primary-amber" id="btnSaveEditTicket">
          <span>Save Changes</span>
        </button>
      </div>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
</body>

</html>