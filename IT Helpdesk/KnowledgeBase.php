<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('IT');
require_once __DIR__ . '/api/db_helper.php';
$pdo = getItDb();
$currUser = $_SESSION['vostok_user'] ?? ['full_name' => 'Alexey Ivanov', 'clearance_level' => 'L2'];

// Query KB articles
$stmt = $pdo->query("SELECT * FROM knowledge_base_articles ORDER BY updated_at DESC");
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalArticles = count($articles);

// Global Dynamic Badges
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
  <title>VOSTOKPRIBOR IT Helpdesk · Knowledge Base &amp; SOP Library</title>
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
            <input type="text" class="search-input" id="global-omni-search" placeholder="Search knowledge base articles, troubleshooting SOPs, PLC codes..." />
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
          <div class="top-user-profile" onclick="window.hdApp.showToast('Active Tech Session', '<?= htmlspecialchars($currUser['full_name']) ?> · Lead IT Engineer')">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" alt="Alexey Ivanov" class="user-avatar-top" />
            <div class="user-details-top">
              <span class="user-name-top"><?= htmlspecialchars($currUser['full_name']) ?></span>
              <span class="user-role-top">Lead IT Tech · Tier 3</span>
            </div>
          </div>
        </div>

        <!-- Top Bar Sign Out -->
        <a href="../api/logout.php?system=IT%20Helpdesk&redirect=../IT%20Helpdesk/login.php" class="top-signout-btn" title="Sign Out of IT Helpdesk" onclick="(function(){sessionStorage.clear();localStorage.clear();})()"><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
      </div>
    </header>

    <div class="main-layout">
      <aside class="sidebar">
        <div>
          <div class="sidebar-section-title">IT Support Operations</div>
          <nav class="sidebar-nav">
            <a href="Dashboard.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                  </svg></span>
                <span>Dashboard</span>
              </div>
            </a>
            <a href="TicketQueue.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                  </svg></span>
                <span>Ticket Queue</span>
              </div>
              <span class="sidebar-badge badge-orange"><?= $openCount ?></span>
            </a>
            <a href="MyTickets.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                  </svg></span>
                <span>My Tickets</span>
              </div>
              <span class="sidebar-badge badge-red"><?= $myTicketsCount ?></span>
            </a>
            <a href="KnowledgeBase.php" class="sidebar-nav-item active">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                  </svg></span>
                <span>Knowledge Base</span>
              </div>
              <span class="sidebar-badge"><?= $totalArticles ?></span>
            </a>
            <a href="AssetManagement.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="2" width="20" height="8" rx="2" ry="2" />
                    <rect x="2" y="14" width="20" height="8" rx="2" ry="2" />
                    <line x1="6" y1="6" x2="6.01" y2="6" />
                    <line x1="6" y1="18" x2="6.01" y2="18" />
                  </svg></span>
                <span>Asset Management</span>
              </div>
              <span class="sidebar-badge"><?= $assetCount ?></span>
            </a>
            <a href="SLAReports.php" class="sidebar-nav-item">
              <div class="sidebar-item-left">
                <span class="sidebar-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                  </svg></span>
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
                  <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
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
              <span>SOP Knowledge Library</span>
              <span class="security-badge-status">● LIVE</span>
            </div>
            <div class="hd-text-inverse-muted-sm">
              Standard Runbooks: <strong><?= $totalArticles ?> Articles</strong>
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
                <span>IT Helpdesk</span>
                <span class="breadcrumb-separator">/</span>
                <span>Operations Central</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Standard Operating Procedures</span>
              </div>
              <h1 class="page-title">Technical Knowledge Base &amp; Field Runbooks</h1>
              <p class="page-subtitle"><?= $totalArticles ?> verified technical articles, PLC debugging guides, cleanroom protocols, and hardware pinouts</p>
            </div>
            <div class="page-header-actions">
              <button class="btn btn-primary-amber" onclick="window.hdApp.openCreateKbModal()">
                <span>+ Create Article</span>
              </button>
            </div>
          </div>

          <!-- Knowledge Cards Grid (Dynamic from DB) -->
          <div class="hd-grid-kb">
            <?php if (empty($articles)): ?>
            <div style="grid-column: 1 / -1; padding: 32px; text-align: center; color: var(--hd-text-muted); background: var(--hd-navy-surface); border-radius: 8px;">
              No knowledge base articles found. Click "+ Create Article" to add one.
            </div>
            <?php else: ?>
              <?php foreach ($articles as $index => $art): 
                $cardStyles = ['hd-card-kb-orange', 'hd-card-kb-steel', 'hd-card-kb-success'];
                $catStyles = ['hd-kb-cat-orange', 'hd-kb-cat-steel', 'hd-kb-cat-success'];
                $styleIdx = $index % 3;
                $cardClass = $cardStyles[$styleIdx];
                $catClass = $catStyles[$styleIdx];
              ?>
              <div class="hd-card <?= $cardClass ?>" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                  <div class="<?= $catClass ?>">
                    <?= htmlspecialchars((string)($art['article_code'] ?? ('KB-' . $art['kb_id']))) ?> · <?= strtoupper(htmlspecialchars((string)($art['category'] ?? 'GENERAL'))) ?>
                  </div>
                  <h3 class="hd-kb-title" style="margin-top: 6px;"><?= htmlspecialchars((string)($art['title'] ?? 'Technical SOP')) ?></h3>
                  <p class="hd-kb-desc">
                    <?= htmlspecialchars((string)($art['summary'] ?: substr((string)($art['content'] ?? ''), 0, 160) . '...')) ?>
                  </p>
                </div>
                <div>
                  <?php if (!empty($art['tags'])): ?>
                  <div style="margin-bottom: 12px; display: flex; gap: 4px; flex-wrap: wrap;">
                    <?php foreach (explode(',', (string)$art['tags']) as $tag): ?>
                      <span style="font-size: 10px; background: rgba(255,255,255,0.06); padding: 2px 6px; border-radius: 4px; color: var(--hd-text-muted);"><?= htmlspecialchars(trim($tag)) ?></span>
                    <?php endforeach; ?>
                  </div>
                  <?php endif; ?>
                  <div class="hd-kb-footer-meta" style="padding-top: 10px; border-top: 1px solid var(--hd-navy-border); display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 11px; color: var(--hd-text-muted);">Views: <?= (int)($art['views_count'] ?? 0) ?></span>
                    <div style="display: flex; gap: 6px; align-items: center;">
                      <button class="btn btn-outline btn-sm" onclick="window.hdApp.viewKbArticle(<?= htmlspecialchars(json_encode($art), ENT_QUOTES, 'UTF-8') ?>)">Read SOP →</button>
                      <button class="btn-crud-edit" onclick="window.hdApp.openEditKbModal(<?= htmlspecialchars(json_encode($art), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Article">✎</button>
                      <button class="btn-crud-delete" onclick="window.hdApp.deleteKbArticle(<?= (int)$art['kb_id'] ?>, '<?= htmlspecialchars((string)($art['article_code'] ?? ('KB-' . $art['kb_id'])), ENT_QUOTES) ?>')" title="Delete Article">🗑</button>
                    </div>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Create Article Modal -->
  <div id="modal-create-kb" class="hd-modal-overlay">
    <div class="hd-modal-dialog">
      <div class="hd-modal-header">
        <h3 class="hd-modal-title">⚡ Author New SOP Runbook</h3>
        <button type="button" class="hd-modal-close" onclick="window.hdApp.closeModal('modal-create-kb')">✕</button>
      </div>
      <form id="form-create-kb" onsubmit="window.hdApp.submitCreateKb(event)">
        <div class="hd-modal-body">
          <div class="hd-form-row">
            <div class="hd-form-group">
              <label class="hd-form-label">Article Code</label>
              <input type="text" name="article_code" class="hd-form-input" placeholder="e.g. KB-5020 (auto-generated if blank)" />
            </div>
            <div class="hd-form-group">
              <label class="hd-form-label">Category *</label>
              <input type="text" name="category" class="hd-form-input" required placeholder="e.g. SCADA NETWORKING, ACCESS CONTROL, PKI" />
            </div>
          </div>
          <div class="hd-form-group">
            <label class="hd-form-label">Title *</label>
            <input type="text" name="title" class="hd-form-input" required placeholder="e.g. Moxa MB3170 RS-485 Termination Resistor Balancing" />
          </div>
          <div class="hd-form-group">
            <label class="hd-form-label">Executive Summary</label>
            <input type="text" name="summary" class="hd-form-input" placeholder="Brief summary of symptoms, failure mode, and resolution step..." />
          </div>
          <div class="hd-form-group">
            <label class="hd-form-label">Full SOP Runbook Content *</label>
            <textarea name="content" class="hd-form-textarea" rows="6" required placeholder="Enter numbered step-by-step remediation procedures, jumper settings, and verify commands..."></textarea>
          </div>
          <div class="hd-form-group">
            <label class="hd-form-label">Tags (comma-separated)</label>
            <input type="text" name="tags" class="hd-form-input" placeholder="e.g. SCADA, RS485, Moxa, Hardware" />
          </div>
        </div>
        <div class="hd-modal-footer">
          <button type="button" class="btn btn-outline" onclick="window.hdApp.closeModal('modal-create-kb')">Cancel</button>
          <button type="submit" class="btn btn-primary-amber">Publish Runbook to DB</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Article Modal -->
  <div id="modal-edit-kb" class="hd-modal-overlay">
    <div class="hd-modal-dialog">
      <div class="hd-modal-header">
        <h3 class="hd-modal-title">✎ Edit Knowledge Base SOP</h3>
        <button type="button" class="hd-modal-close" onclick="window.hdApp.closeModal('modal-edit-kb')">✕</button>
      </div>
      <form id="form-edit-kb" onsubmit="window.hdApp.submitEditKb(event)">
        <input type="hidden" name="article_id" id="edit-kb-id" />
        <div class="hd-modal-body">
          <div class="hd-form-row">
            <div class="hd-form-group">
              <label class="hd-form-label">Article Code *</label>
              <input type="text" name="article_code" id="edit-kb-code" class="hd-form-input" required />
            </div>
            <div class="hd-form-group">
              <label class="hd-form-label">Category *</label>
              <input type="text" name="category" id="edit-kb-cat" class="hd-form-input" required />
            </div>
          </div>
          <div class="hd-form-group">
            <label class="hd-form-label">Title *</label>
            <input type="text" name="title" id="edit-kb-title" class="hd-form-input" required />
          </div>
          <div class="hd-form-group">
            <label class="hd-form-label">Executive Summary</label>
            <input type="text" name="summary" id="edit-kb-summary" class="hd-form-input" />
          </div>
          <div class="hd-form-group">
            <label class="hd-form-label">Full SOP Runbook Content *</label>
            <textarea name="content" id="edit-kb-content" class="hd-form-textarea" rows="6" required></textarea>
          </div>
          <div class="hd-form-group">
            <label class="hd-form-label">Tags</label>
            <input type="text" name="tags" id="edit-kb-tags" class="hd-form-input" />
          </div>
        </div>
        <div class="hd-modal-footer">
          <button type="button" class="btn btn-outline" onclick="window.hdApp.closeModal('modal-edit-kb')">Cancel</button>
          <button type="submit" class="btn btn-primary-amber">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Read SOP Runbook Modal -->
  <div id="modal-view-kb" class="hd-modal-overlay">
    <div class="hd-modal-dialog" style="max-width: 680px;">
      <div class="hd-modal-header">
        <div>
          <span id="view-kb-code" style="font-size: 11px; font-weight: 700; color: var(--hd-accent); letter-spacing: 0.5px;"></span>
          <h3 id="view-kb-title" class="hd-modal-title" style="margin-top: 4px;"></h3>
        </div>
        <button type="button" class="hd-modal-close" onclick="window.hdApp.closeModal('modal-view-kb')">✕</button>
      </div>
      <div class="hd-modal-body">
        <div id="view-kb-summary" style="font-style: italic; color: var(--hd-text-muted); margin-bottom: 16px; padding: 12px; background: rgba(0,0,0,0.2); border-left: 3px solid var(--hd-accent); border-radius: 4px;"></div>
        <div style="font-weight: 700; font-size: 13px; color: #fff; margin-bottom: 8px;">Runbook Procedure &amp; Technical Steps:</div>
        <pre id="view-kb-content" style="white-space: pre-wrap; font-family: monospace; font-size: 12.5px; background: #071524; padding: 16px; border-radius: 6px; border: 1px solid var(--hd-navy-border); color: #E0E8F0; line-height: 1.6;"></pre>
      </div>
      <div class="hd-modal-footer">
        <button type="button" class="btn btn-primary-amber" onclick="window.hdApp.closeModal('modal-view-kb')">Close Runbook</button>
      </div>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="js/app.js"></script>
</body>

</html>