<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('EMP');
require_once __DIR__ . '/intranet_service.php';

$pdo = getDbConnection();
$currUser = $_SESSION['vostok_user'] ?? ['full_name' => 'Authorized User', 'clearance_level' => 'L2'];

// Handle direct read/write update for OPS Queue
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ops_action']) && $_POST['ops_action'] === 'update_task') {
    $tId = trim($_POST['task_id'] ?? '');
    $tStatus = trim($_POST['task_status'] ?? 'Pending');
    $tEmp = trim($_POST['assigned_emp_id'] ?? '');
    if ($tId !== '') {
        intra_updateOpsTask($tId, $tStatus, $tEmp ?: null);
        header("Location: Dashboard.php?ops_updated=1");
        exit;
    }
}
$opsTasks = intra_getOpsTasks();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOSTOKPRIBOR Intranet • Home Feed</title>
    <meta name="description" content="VOSTOKPRIBOR Enterprise Employee Communications & Operational Hub">

    <!-- Fonts: IBM Plex Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <!-- Unified Stylesheet -->
    <link rel="stylesheet" href="css/intranet.css">
</head>

<body>

    <!-- ======================================================================
         TOP BAR: Deep Navy with 4px Slate Blue Accent Stripe
         ====================================================================== -->
    <header class="intranet-header" id="intranet-header">
        <div class="intra-height-100-display-flex-01f3"
            >

            <!-- Left Brand & Sidebar Toggle -->
            <div class="intra-display-flex-align-items-ee8a" >
                <button class="intra-background-rgba-255-255-928c" type="button" id="sidebar-toggle-btn"
                    
                    title="Toggle Sidebar (Ctrl+B)">
                    <span class="material-symbols-outlined intra-text-xl">menu</span>
                </button>

                <a class="intra-display-flex-align-items-a3ac" href="Dashboard.php"
                    >
                    <img class="intra-height-32px-width-auto-7934" alt="VOSTOKPRIBOR Official Mark" 
                        src="assets/logo.svg" />
                    <div class="intra-height-24px-width-1px-c7a3" ></div>
                    <div class="intra-flex-col" >
                        <div class="intra-flex-center-gap-sm" >
                            <span class="intra-color-ffffff-font-weight-5ca4"
                                >VOSTOKPRIBOR</span>
                            <span class="intra-background-color-var-system-2816"
                                >INTRANET</span>
                        </div>
                        <span class="intra-font-family-var-font-efd2"
                            >intranet.vostokpribor.local
                            • Est. 1968</span>
                    </div>
                </a>
            </div>

            <!-- Center Search Bar (Command Palette Launcher Ctrl+K) -->
            <div class="intra-flex-1-max-width-5505" >
                <div class="header-search-bar intra-display-flex-align-items-6d41">
                    <span class="material-symbols-outlined intra-color-939fa8-font-size-e00b">search</span>
                    <input class="intra-background-transparent-border-none-05e6" type="text" id="header-search-input"
                        placeholder="Search intranet, personnel, policies, or forms... (Ctrl+K)"
                        
                        readonly>
                    <span class="intra-background-rgba-255-255-5858"
                        >⌘K</span>
                </div>
            </div>

            <!-- Right Actions & Profile -->
            <div class="intra-flex-center-gap-md" >

                <!-- Quick Submit Action -->
                <button type="button" class="btn btn-primary" onclick="window.openQuickRequestModal('leave')"
                    >
                    <span class="material-symbols-outlined intra-font-size-1rem-margin-6a85">add</span>
                    Request Leave
                </button>

                <!-- Notifications Bell -->
                <div class="intra-pos-relative" >
                    <button class="intra-background-rgba-255-255-87b8" type="button" id="notifications-toggle-btn"
                        >
                        <span class="material-symbols-outlined intra-text-xl">notifications</span>
                        <span class="intra-position-absolute-top-3px-e466" id="notif-unread-count" style="display:none;">0</span>
                    </button>

                    <!-- Notifications Dropdown Popover -->
                    <div class="notifications-popover" id="notifications-popover" >
                        <div class="intra-padding-0-75rem-1rem-82b8"
                            >
                            <span class="intra-text-white-600-sm" >System
                                Notifications</span>
                            <button class="intra-background-transparent-border-none-4d5e" type="button" id="clear-notifications-btn"
                                >Mark
                                All Read</button>
                        </div>
                        <div class="intra-max-height-18rem-overflow-db30" id="notifications-list">
                            <div style="padding:20px;text-align:center;font-size:12px;opacity:.5;">No unread notifications</div>
                        </div>
                    </div>
                </div>

                <!-- Ecosystem Switcher (11 Systems) -->
                <div class="intra-pos-relative" >
                    <button class="intra-background-rgba-255-255-57e6" type="button" id="ecosystem-toggle-btn"
                        
                        title="VOSTOKPRIBOR Ecosystem Switcher">
                        <span class="material-symbols-outlined intra-text-xl">apps</span>
                    </button>

                    <!-- 11-System Ecosystem Dropdown Menu -->
                    <div class="ecosystem-dropdown" id="ecosystem-dropdown" >
                        <div class="intra-padding-0-75rem-1rem-4249"
                            >
                            <div>
                                <span class="intra-font-size-0-8125rem-54a0"
                                    >VOSTOKPRIBOR
                                    ECOSYSTEM</span>
                                <div class="intra-font-size-0-6875rem-e9c1" >11
                                    Unified Systems</div>
                            </div>
                            <span class="badge-classification internal">Enterprise SSO</span>
                        </div>
                        <div class="intra-padding-0-5rem-max-ea3f"
                            >
                            <a href="../VOSTOKPRIBOR Corporate Web Platform/index.php" class="ecosystem-item">
                                <span class="material-symbols-outlined intra-color-1b3a5c-font-size-0b1e">language</span>
                                <div>
                                    <div class="intra-text-white-bold-sm" >Corporate Web Platform</div>
                                    <div class="intra-mono-muted-xs" >vostokpribor.local • System 01</div>
                                </div>
                            </a>
                            <a href="../Online Shop B2B/login.php" class="ecosystem-item">
                                <span class="material-symbols-outlined intra-color-0e7c86-font-size-06b1">shopping_bag</span>
                                <div>
                                    <div class="intra-text-white-bold-sm" >B2B Online Shop</div>
                                    <div class="intra-mono-muted-xs" >shop.vostokpribor.local • System 02</div>
                                </div>
                            </a>
                            <a href="../Customer Portal/Dashboard.php" class="ecosystem-item">
                                <span class="material-symbols-outlined intra-color-e8a33d-font-size-5aef">dashboard</span>
                                <div>
                                    <div class="intra-text-white-bold-sm" >Customer Portal</div>
                                    <div class="intra-mono-muted-xs" >portal.vostokpribor.local • System 03</div>
                                </div>
                            </a>
                            <a href="Dashboard.php" class="ecosystem-item intra-background-color-rgba-92-020a">
                                <span class="material-symbols-outlined intra-accent-xl">hub</span>
                                <div>
                                    <div class="intra-text-white-bold-sm" >Employee Intranet</div>
                                    <div class="intra-color-bdc6cf-font-size-99c9" >intranet.vostokpribor.local • Current (Sys 04)</div>
                                </div>
                            </a>
                            <a href="../CRM/index.php" class="ecosystem-item">
                                <span class="material-symbols-outlined intra-color-3b4c8c-font-size-67a0">groups</span>
                                <div>
                                    <div class="intra-text-white-bold-sm" >CRM Platform</div>
                                    <div class="intra-mono-muted-xs" >crm.vostokpribor.local • System 05</div>
                                </div>
                            </a>
                            <a href="../HR System/login.php" class="ecosystem-item">
                                <span class="material-symbols-outlined intra-color-6e4c7c-font-size-e5aa">person_search</span>
                                <div>
                                    <div class="intra-text-white-bold-sm" >Human Resources (HR)</div>
                                    <div class="intra-mono-muted-xs" >hr.vostokpribor.local • System 06</div>
                                </div>
                            </a>
                            <a href="../Finance & Billing/login.php" class="ecosystem-item">
                                <span class="material-symbols-outlined intra-color-2e6e4e-font-size-0395">receipt_long</span>
                                <div>
                                    <div class="intra-text-white-bold-sm" >Finance & Billing</div>
                                    <div class="intra-mono-muted-xs" >finance.vostokpribor.local • System 07</div>
                                </div>
                            </a>
                            <a href="../IT Helpdesk/login.php" class="ecosystem-item">
                                <span class="material-symbols-outlined intra-color-c97a3d-font-size-d3fb">support_agent</span>
                                <div>
                                    <div class="intra-text-white-bold-sm" >IT Helpdesk & Service</div>
                                    <div class="intra-mono-muted-xs" >helpdesk.vostokpribor.local • System 08</div>
                                </div>
                            </a>
                            <a href="../File Center/login.php" class="ecosystem-item">
                                <span class="material-symbols-outlined intra-color-5a6470-font-size-48ce">folder_zip</span>
                                <div>
                                    <div class="intra-text-white-bold-sm" >File Center Hub</div>
                                    <div class="intra-mono-muted-xs" >files.vostokpribor.local • System 09</div>
                                </div>
                            </a>
                            <a href="../Developer/login.php" class="ecosystem-item">
                                <span class="material-symbols-outlined intra-color-1e8fa6-font-size-e073">terminal</span>
                                <div>
                                    <div class="intra-text-white-bold-sm" >Developer / API Portal</div>
                                    <div class="intra-mono-muted-xs" >developer.vostokpribor.local • System 10</div>
                                </div>
                            </a>
                            <a href="../Admin & Governance Portal/login.php" class="ecosystem-item">
                                <span class="material-symbols-outlined intra-color-b23a32-font-size-0dac">security</span>
                                <div>
                                    <div class="intra-text-white-bold-sm" >Admin & Governance</div>
                                    <div class="intra-mono-muted-xs" >admin.vostokpribor.local • System 11</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Authenticated User Profile Avatar -->
                <div class="intra-display-flex-align-items-f8dd"
                    >
                    <div class="avatar-circle intra-background-color-1a73e8-width-13e2">
                        <?= htmlspecialchars(mb_substr($currUser['full_name'] ?? 'EM', 0, 2)) ?>
                        <span class="avatar-status-dot online"></span>
                    </div>
                    <div class="user-info-text intra-flex-col">
                        <span class="intra-color-ffffff-font-weight-b287" ><?= htmlspecialchars($currUser['full_name'] ?? 'Elena Morozova') ?></span>
                        <span class="intra-mono-muted-xs" ><?= htmlspecialchars($currUser['role_name'] ?? 'CTO') ?> • <?= htmlspecialchars($currUser['clearance_level'] ?? 'L4') ?> Clear</span>
                    </div>
                </div>

            </div>


            <!-- Top Bar Sign Out -->
            <a href="./api/logout.php?redirect=../Employee%20Intranet/login.php" class="top-signout-btn" title="Sign Out of Employee Intranet" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" ><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
        </div>
    </header>

    <!-- ======================================================================
         LEFT SIDEBAR: Dark Navy with Slate Blue Accent (#5C7290)
         ====================================================================== -->
    <aside class="intranet-sidebar" id="intranet-sidebar">

        <!-- Navigation Menu -->
        <div class="intra-padding-top-1rem-display-6c18" >

            <div class="sidebar-section-title intra-padding-0-1rem-0-35ef">
                COMMUNICATIONS & HUB
            </div>

            <!-- News & Announcements (Active) -->
            <a href="Dashboard.php" class="sidebar-link active">
                <span class="material-symbols-outlined intra-text-xl">campaign</span>
                <span class="sidebar-label">News & Announcements</span>
            </a>

            <!-- Employee Directory -->
            <a href="EmployeeDirectory.php" class="sidebar-link">
                <span class="material-symbols-outlined intra-text-xl">badge</span>
                <span class="sidebar-label">Employee Directory</span>
                <span class="sidebar-badge intra-margin-left-auto-background-8fba" id="intra-nav-dir-count">—</span>
            </a>

            <!-- Policies & Forms -->
            <a href="PoliciesAndForms.php" class="sidebar-link">
                <span class="material-symbols-outlined intra-text-xl">policy</span>
                <span class="sidebar-label">Policies & Forms</span>
            </a>
            <a href="Integrations.php" class="sidebar-link"><span class="material-symbols-outlined intra-font-size-1-25rem-a700">hub</span><span class="sidebar-label intra-color-00e5ff-font-weight-fe7a">System Integrations</span><span class="sidebar-badge intra-margin-left-auto-background-c33b">SYS05</span></a>

            <div class="sidebar-section-title intra-padding-1rem-1rem-0-0f82">
                EMPLOYEE SERVICES
            </div>

            <!-- Helpdesk / IT Support -->
            <a href="javascript:void(0)" class="sidebar-link" onclick="window.openQuickRequestModal('it')">
                <span class="material-symbols-outlined intra-text-xl">support_agent</span>
                <span class="sidebar-label">Helpdesk / IT Support</span>
            </a>

            <!-- Company Calendar -->
            <a href="javascript:void(0)" class="sidebar-link"
                onclick="window.showIntranetToast('Calendar Sync Active', 'Connected to Almaty Exchange CalDAV server.', 'info')">
                <span class="material-symbols-outlined intra-text-xl">event</span>
                <span class="sidebar-label">Company Calendar</span>
            </a>

            <!-- Room & Asset Booking -->
            <a href="javascript:void(0)" class="sidebar-link"
                onclick="window.showIntranetToast('Resource Scheduler', 'Conference Rooms Alpha & Beta available today.', 'info')">
                <span class="material-symbols-outlined intra-text-xl">meeting_room</span>
                <span class="sidebar-label">Room Booking</span>
            </a>

        </div>

        <!-- Sidebar Footer: User Card -->
        <div class="intra-padding-1rem-border-top-dd4e" >
            <div class="intra-flex-center-gap-md" >
                <div class="avatar-circle intra-background-color-1a73e8-5fb8">
                    <?= htmlspecialchars(mb_substr($currUser['full_name'] ?? 'EM', 0, 2)) ?>
                    <span class="avatar-status-dot online"></span>
                </div>
                <div class="user-info-text intra-flex-1-min-0">
                    <div class="intra-font-size-0-8125rem-863b" >
                        <?= htmlspecialchars($currUser['full_name'] ?? 'Elena Morozova') ?>
                    </div>
                    <div class="intra-font-size-0-6875rem-50ad" >
                        <span class="dept-badge itd intra-padding-0-0-3rem-188d"><?= htmlspecialchars($currUser['department_code'] ?? 'ITD') ?></span>
                        <span><?= htmlspecialchars($currUser['role_name'] ?? 'Chief Tech Officer') ?></span>
                    </div>
                </div>
                <button class="intra-btn-icon-ghost" type="button"  title="Lock Session" onclick="window.showIntranetToast('Security Notice', 'Session verified under ISO-27001 Zero-Trust policy.', 'info')">
                    <span class="material-symbols-outlined intra-text-11">lock</span>
                </button>
        </div>
    </aside>

    <!-- Left-edge hover detection strip for collapsed rail state -->
    <div id="sidebar-hover-trigger" title="Hover to expand menu"></div>

    <!-- ======================================================================
         MAIN CONTENT WRAPPER
         ====================================================================== -->
    <main class="intranet-content-wrapper" id="intranet-content-wrapper">
        <div class="intra-padding-1-5rem-max-53c0" >

            <!-- Page Header Breadcrumb -->
            <div class="intra-display-flex-align-items-3241" >
                <div>
                    <div class="intra-display-flex-align-items-f015"
                        >
                        <span>INTRANET</span>
                        <span>/</span>
                        <span class="intra-color-var-system-accent-dff5" >NEWS & ANNOUNCEMENTS</span>
                    </div>
                    <h1 class="intra-margin-0-font-size-d321"
                        >
                        Enterprise News & Communications
                    </h1>
                </div>

                <div class="intra-flex-center-gap-md" >
                    <button type="button" class="btn btn-secondary" onclick="window.openQuickRequestModal('it')"
                        >
                        <span class="material-symbols-outlined intra-text-base">engineering</span>
                        IT Ticket
                    </button>
                    <button type="button" class="btn btn-primary" onclick="window.openQuickRequestModal('leave')"
                        >
                        <span class="material-symbols-outlined intra-text-base">flight_takeoff</span>
                        Request Leave
                    </button>
                </div>
            </div>

            <!-- ==============================================================
                 FEATURED WIDE ANNOUNCEMENT CAROUSEL
                 ============================================================== -->
            <section class="announcement-carousel-card" id="home-announcement-carousel"
                >
                <div class="intra-position-relative-z-index-4d0f" >

                    <div class="intra-display-flex-align-items-6a3d" >
                        <span class="badge-classification internal" id="carousel-slide-badge"
                            >
                            CORPORATE STRATEGY
                        </span>
                        <span class="intra-font-family-var-font-72b4" 
                            id="carousel-slide-date">
                            September 08, 2026
                        </span>
                    </div>

                    <h2 class="intra-margin-0-0-0-7210" id="carousel-slide-title"
                        >
                        Q3 Enterprise Assembly Modernization: Plant 2 Transition
                    </h2>

                    <p class="intra-margin-0-0-1-42e5" id="carousel-slide-desc"
                        >
                        Beginning September 15, Assembly Plant 2 in Almaty will undergo planned calibration robotics
                        retrofits. Field engineering operations will reroute through Hub Alpha.
                    </p>

                    <div class="intra-display-flex-align-items-869e" >
                        <button type="button" id="carousel-slide-btn" class="btn btn-primary intra-inline-flex-gap-xs">
                            Read Directive
                            <span class="material-symbols-outlined intra-text-11">arrow_forward</span>
                        </button>
                        <span class="intra-font-size-0-8125rem-eeff"  id="carousel-slide-author">
                            Amina Karimova • Chief Operating Officer
                        </span>
                    </div>

                </div>

                <!-- Carousel Controls & Dots -->
                <div class="intra-position-absolute-bottom-1-19e7"
                    >
                    <!-- Dots -->
                    <div class="intra-display-flex-align-items-1649" >
                        <button type="button" class="carousel-dot intra-width-24px-height-8px-3a74"
                            title="Slide 1"></button>
                        <button type="button" class="carousel-dot intra-width-8px-height-8px-6638"
                            title="Slide 2"></button>
                        <button type="button" class="carousel-dot intra-width-8px-height-8px-6638"
                            title="Slide 3"></button>
                    </div>
                    <!-- Arrows -->
                    <div class="intra-display-flex-gap-0-b7c2" >
                        <button class="intra-width-32px-height-32px-3020" type="button" id="carousel-prev-btn"
                            
                            title="Previous Slide">
                            <span class="material-symbols-outlined intra-text-11">chevron_left</span>
                        </button>
                        <button class="intra-width-32px-height-32px-3020" type="button" id="carousel-next-btn"
                            
                            title="Next Slide">
                            <span class="material-symbols-outlined intra-text-11">chevron_right</span>
                        </button>
                    </div>
                </div>
            </section>

            <!-- ==============================================================
                 TWO-COLUMN LAYOUT: Feed (Left) & Widgets (Right)
                 ============================================================== -->
            <div class="intranet-grid-layout intra-display-grid-grid-template-4a9e">

                <!-- ==========================================================
                     LEFT COLUMN: ANNOUNCEMENT FEED
                     ==============================                <div class="intra-display-flex-flex-direction-f48b">

                    <!-- Feed Filter Tabs -->
                    <div class="intra-display-flex-align-items-29ed">
                        <div class="intra-display-flex-gap-0-5fa1">
                            <span class="intra-font-weight-600-font-bc25">All Updates</span>
                            <span class="intra-background-var-neutral-100-9b42" id="announcements-badge">—</span>
                        </div>
                        <span class="intra-font-size-0-75rem-db0e">
                            Live Feed • Synchronized
                        </span>
                    </div>

                    <!-- Live Dynamic Announcements Feed Container -->
                    <div class="intra-display-flex-flex-direction-f48b" id="announcements-feed">
                        <!-- Dynamically populated from MySQL database by intranet-data.js -->
                    </div>
                </div>

                <!-- ==========================================================
                     RIGHT COLUMN: STACKED WIDGETS
                     ========================================================== -->
                <div class="intra-display-flex-flex-direction-a81b" >

                    <!-- WIDGET 0: OPS FULFILLMENT QUEUE (DEPARTMENT BOARD FOR EMP-1011..1015) -->
                    <div class="intranet-card intra-p-125" id="ops-queue-panel">
                        <div class="intra-display-flex-align-items-4177" style="margin-bottom: 0.75rem;">
                            <div class="intra-flex-center-gap-sm">
                                <span class="material-symbols-outlined intra-accent-xl" style="color:#00e5ff;">local_shipping</span>
                                <h3 class="intra-margin-0-font-size-99fa">OPS Fulfillment Queue</h3>
                            </div>
                            <span class="dept-badge ops" style="font-size:0.6875rem;">03 OPS (EMP-1011..1015)</span>
                        </div>
                        <?php if (empty($opsTasks)): ?>
                            <div style="font-size:0.8125rem; color: var(--neutral-500); padding: 0.5rem 0;">No active tasks in OPS queue.</div>
                        <?php else: ?>
                            <div style="display:flex; flex-direction:column; gap:0.5rem; max-height: 340px; overflow-y: auto;">
                                <?php foreach ($opsTasks as $task): ?>
                                    <div style="padding: 0.625rem; background: var(--neutral-50); border: 1px solid var(--neutral-200); border-radius: 4px; font-size: 0.8125rem;">
                                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 4px;">
                                            <span style="font-family: var(--font-mono); font-weight:600; color: var(--system-accent);"><?= htmlspecialchars((string)$task['task_id']) ?></span>
                                            <span style="font-size: 0.6875rem; padding: 1px 6px; border-radius: 3px; font-weight:600; background: <?= $task['status'] === 'Completed' ? '#d4edda; color:#155724' : ($task['status'] === 'In Progress' ? '#cce5ff; color:#004085' : '#fff3cd; color:#856404') ?>;">
                                                <?= htmlspecialchars((string)$task['status']) ?>
                                            </span>
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--neutral-700); margin-bottom: 4px;">
                                            <strong>Type:</strong> <?= htmlspecialchars((string)$task['task_type']) ?>
                                            <?php if (!empty($task['order_id'])): ?> | <strong>Order:</strong> <?= htmlspecialchars((string)$task['order_id']) ?><?php endif; ?>
                                            <?php if (!empty($task['prj_id'])): ?> | <strong>Project:</strong> <?= htmlspecialchars((string)$task['prj_id']) ?><?php endif; ?>
                                        </div>
                                        <form method="POST" action="Dashboard.php" style="display:flex; gap: 4px; align-items:center; margin-top: 4px;">
                                            <input type="hidden" name="ops_action" value="update_task">
                                            <input type="hidden" name="task_id" value="<?= htmlspecialchars((string)$task['task_id']) ?>">
                                            <select name="assigned_emp_id" style="font-size:0.75rem; padding: 2px 4px; border: 1px solid var(--neutral-300); border-radius: 3px; flex:1;">
                                                <option value="EMP-1011" <?= ($task['assigned_emp_id'] ?? '') === 'EMP-1011' ? 'selected' : '' ?>>Amina Karimova (EMP-1011)</option>
                                                <option value="EMP-1012" <?= ($task['assigned_emp_id'] ?? '') === 'EMP-1012' ? 'selected' : '' ?>>Baurzhan Asanov (EMP-1012)</option>
                                                <option value="EMP-1013" <?= ($task['assigned_emp_id'] ?? '') === 'EMP-1013' ? 'selected' : '' ?>>Galina Voronova (EMP-1013)</option>
                                                <option value="EMP-1014" <?= ($task['assigned_emp_id'] ?? '') === 'EMP-1014' ? 'selected' : '' ?>>Dmitry Chen (EMP-1014)</option>
                                                <option value="EMP-1015" <?= ($task['assigned_emp_id'] ?? '') === 'EMP-1015' ? 'selected' : '' ?>>Saule Kadyrova (EMP-1015)</option>
                                            </select>
                                            <select name="task_status" style="font-size:0.75rem; padding: 2px 4px; border: 1px solid var(--neutral-300); border-radius: 3px;">
                                                <option value="Pending" <?= $task['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="In Progress" <?= $task['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                                <option value="Completed" <?= $task['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                                <option value="Cancelled" <?= $task['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" class="btn btn-secondary" style="font-size:0.6875rem; padding: 2px 6px; height:auto;">Update</button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- WIDGET 1: TODAY'S CALENDAR -->
                    <div class="intranet-card intra-p-125">
                        <div class="intra-display-flex-align-items-4177"
                            >
                            <div class="intra-flex-center-gap-sm" >
                                <span class="material-symbols-outlined intra-accent-xl">event</span>
                                <h3 class="intra-margin-0-font-size-99fa"
                                    >
                                    Today's Calendar</h3>
                            </div>
                            <span class="intra-mono-neutral-xs"
                                >Thu,
                                Sep 10</span>
                        </div>

                        <div class="intra-display-flex-flex-direction-84df" >

                            <!-- Event 1 -->
                            <div class="intra-padding-0-75rem-background-14c0"
                                >
                                <div class="intra-display-flex-align-items-6d7a"
                                    >
                                    <span class="intra-font-family-var-font-4519"
                                        >10:00
                                        - 11:00</span>
                                    <span class="badge-classification internal intra-font-size-0-625rem-0b73">Conf C-302</span>
                                </div>
                                <div class="intra-font-size-0-8125rem-3739"
                                    >
                                    Executive Operations Sync & Q3 Milestones
                                </div>
                                <div class="intra-display-flex-align-items-651c" >
                                    <span class="intra-text-neutral-xs" >Hybrid • WebRTC Room
                                        Alpha</span>
                                    <div class="intra-display-flex-margin-left-bc41" >
                                        <div class="avatar-circle intra-width-22px-height-22px-1368">
                                            VS</div>
                                        <div class="avatar-circle intra-width-22px-height-22px-1cfc">
                                            AK</div>
                                        <div class="avatar-circle intra-width-22px-height-22px-1845">
                                            EM</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Event 2 -->
                            <div class="intra-padding-0-75rem-background-9e63"
                                >
                                <div class="intra-display-flex-align-items-6d7a"
                                    >
                                    <span class="intra-font-family-var-font-81be"
                                        >14:30
                                        - 15:30</span>
                                    <span class="dept-badge eng intra-font-size-0-625rem-ad81">ENG LAB 1</span>
                                </div>
                                <div class="intra-font-size-0-8125rem-3739"
                                    >
                                    VP-900 Calibration Protocol Review
                                </div>
                                <div class="intra-display-flex-align-items-651c" >
                                    <span class="intra-text-neutral-xs" >In-Person Cleanroom
                                        Bay C</span>
                                    <div class="intra-display-flex-margin-left-bc41" >
                                        <div class="avatar-circle intra-width-22px-height-22px-7f85">
                                            AZ</div>
                                        <div class="avatar-circle intra-width-22px-height-22px-7f85">
                                            EH</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Event 3 -->
                            <div class="intra-padding-0-75rem-background-c8d3"
                                >
                                <div class="intra-display-flex-align-items-6d7a"
                                    >
                                    <span class="intra-font-family-var-font-2ee9"
                                        >16:00
                                        - 17:00</span>
                                    <span class="badge-classification confidential intra-font-size-0-625rem-0b73">Confidential</span>
                                </div>
                                <div class="intra-font-size-0-8125rem-3739"
                                    >
                                    Security Architecture Board (Zero-Trust)
                                </div>
                                <div class="intra-display-flex-align-items-651c" >
                                    <span class="intra-text-neutral-xs" >Virtual Conference
                                        1</span>
                                    <div class="intra-display-flex-margin-left-bc41" >
                                        <div class="avatar-circle intra-width-22px-height-22px-1845">
                                            RK</div>
                                        <div class="avatar-circle intra-width-22px-height-22px-fdc2">
                                            TA</div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- WIDGET 2: QUICK LINKS -->
                    <div class="intranet-card intra-p-125">
                        <div class="intra-display-flex-align-items-4177"
                            >
                            <div class="intra-flex-center-gap-sm" >
                                <span class="material-symbols-outlined intra-accent-xl">bolt</span>
                                <h3 class="intra-margin-0-font-size-99fa"
                                    >
                                    Quick Links</h3>
                            </div>
                            <span class="intra-mono-neutral-xs"
                                >Frequently
                                Used</span>
                        </div>

                        <div class="intra-display-grid-grid-template-38cf" >

                            <div class="quick-link-btn" onclick="window.openQuickRequestModal('expense')">
                                <span class="material-symbols-outlined intra-color-2e6e4e-font-size-b435">receipt_long</span>
                                <span class="intra-text-center-bold-xs" >Submit
                                    Expense</span>
                            </div>

                            <div class="quick-link-btn" onclick="window.openQuickRequestModal('leave')">
                                <span class="material-symbols-outlined intra-color-e8a33d-font-size-53c0">date_range</span>
                                <span class="intra-text-center-bold-xs" >Request
                                    Leave</span>
                            </div>

                            <div class="quick-link-btn" onclick="window.openQuickRequestModal('it')">
                                <span class="material-symbols-outlined intra-color-1a73e8-font-size-d946">headset_mic</span>
                                <span class="intra-text-center-bold-xs" >IT
                                    Helpdesk</span>
                            </div>

                            <div class="quick-link-btn"
                                onclick="window.openQuickRequestModal('room')">
                                <span class="material-symbols-outlined intra-color-0e7c86-font-size-0fa5">domain</span>
                                <span class="intra-text-center-bold-xs" >Room
                                    Booking</span>
                            </div>

                            <a href="EmployeeDirectory.php" class="quick-link-btn">
                                <span class="material-symbols-outlined intra-color-6e4c7c-font-size-d7e2">schema</span>
                                <span class="intra-text-center-bold-xs" >Org
                                    Chart</span>
                            </a>

                            <div class="quick-link-btn" onclick="window.openQuickRequestModal('safety')">
                                <span class="material-symbols-outlined intra-color-b23a32-font-size-2115">warning</span>
                                <span class="intra-text-center-bold-xs" >Safety
                                    Report</span>
                            </div>

                        </div>
                    </div>

                    <!-- WIDGET 3: BIRTHDAYS & ANNIVERSARIES -->
                    <div class="intranet-card intra-p-125">
                        <div class="intra-display-flex-align-items-4177"
                            >
                            <div class="intra-flex-center-gap-sm" >
                                <span class="material-symbols-outlined intra-accent-xl">cake</span>
                                <h3 class="intra-margin-0-font-size-99fa"
                                    >
                                    Milestones & Birthdays</h3>
                            </div>
                            <span class="intra-mono-neutral-xs"
                                >September
                                2026</span>
                        </div>

                        <div class="intra-display-flex-flex-direction-84df" >

                            <!-- Milestone 1 -->
                            <div class="intra-display-flex-align-items-f1c5"
                                >
                                <div class="avatar-circle intra-background-color-0e7c86-width-3efb">AK
                                </div>
                                <div class="intra-flex-1-min-0" >
                                    <div class="intra-font-size-0-8125rem-825a"
                                        >
                                        Amina Karimova
                                        <span class="dept-badge ops intra-font-size-0-625rem-ad79">OPS</span>
                                    </div>
                                    <div class="intra-text-neutral-xs" >21 Years at
                                        VOSTOKPRIBOR</div>
                                </div>
                                <span class="intra-font-family-var-font-0047"
                                    >Sep
                                    12</span>
                            </div>

                            <!-- Milestone 2 -->
                            <div class="intra-display-flex-align-items-f1c5"
                                >
                                <div class="avatar-circle intra-background-color-137333-width-b959">JR
                                </div>
                                <div class="intra-flex-1-min-0" >
                                    <div class="intra-font-size-0-8125rem-825a"
                                        >
                                        Jonas Richter
                                        <span class="dept-badge eng intra-font-size-0-625rem-ad79">ENG</span>
                                    </div>
                                    <div class="intra-text-neutral-xs" >Birthday Celebration
                                        🎂</div>
                                </div>
                                <span class="intra-font-family-var-font-0141"
                                    >Sep
                                    15</span>
                            </div>

                            <!-- Milestone 3 -->
                            <div class="intra-display-flex-align-items-f1c5"
                                >
                                <div class="avatar-circle intra-background-color-137333-width-b959">OV
                                </div>
                                <div class="intra-flex-1-min-0" >
                                    <div class="intra-font-size-0-8125rem-825a"
                                        >
                                        Olga Voronova
                                        <span class="dept-badge eng intra-font-size-0-625rem-ad79">ENG</span>
                                    </div>
                                    <div class="intra-text-neutral-xs" >9 Years at
                                        VOSTOKPRIBOR</div>
                                </div>
                                <span class="intra-font-family-var-font-0047"
                                    >Sep
                                    19</span>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>
    </main>

    <!-- ======================================================================
         COMMAND PALETTE MODAL (Ctrl+K)
         ====================================================================== -->
    <div class="intranet-modal-backdrop" id="command-palette-modal">
        <div class="intranet-modal-container intra-max-width-42rem-border-7e8a">
            <div class="intra-background-color-var-brand-3290"
                >
                <span class="material-symbols-outlined intra-color-var-system-accent-06f9">search</span>
                <input class="intra-background-transparent-border-none-01d8" type="text" id="command-palette-input" placeholder="Type a name, department, role, or policy..."
                    >
                <button class="intra-btn-icon-ghost" type="button" id="close-command-palette-btn"
                    >
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="intra-max-height-24rem-overflow-0250" id="command-palette-results" ></div>
            <div class="intra-padding-0-5rem-1rem-acd8"
                >
                <span>Navigate with mouse or keyboard</span>
                <span>ESC to dismiss</span>
            </div>
        </div>
    </div>

    <!-- Toast Notification Anchor -->
    <div id="intranet-toast-container"></div>

    <!-- Single Consolidated JavaScript Engine -->
    <script src="js/intranet.js"></script>
    <link rel="stylesheet" href="../assets/css/api-ui.css">
    <script src="../assets/js/api-core.js"></script>
    <script src="../assets/js/api-intranet.js"></script>
    <script src="js/intranet-data.js"></script>
</body>

</html>