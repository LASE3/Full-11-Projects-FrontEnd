<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('Employee');
$pdo = getDbConnection();
$currUser = $_SESSION['vostok_user'] ?? ['full_name' => 'Authorized User', 'clearance_level' => 'L2'];
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
                        <span class="intra-position-absolute-top-3px-e466" id="notif-unread-count"
                            >3</span>
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
                        <div class="intra-max-height-18rem-overflow-db30" >
                            <div class="intra-dropdown-item-border"
                                >
                                <span class="dept-dot itd intra-mt-5"></span>
                                <div>
                                    <div class="intra-text-white-600-sm" >MFA Hardware
                                        Token Requisition</div>
                                    <div class="intra-meta-subtitle" >Deadline
                                        approaching for L3/L4 staff.</div>
                                    <div class="intra-mono-dept-meta"
                                        >
                                        12m ago</div>
                                </div>
                            </div>
                            <div class="intra-dropdown-item-border"
                                >
                                <span class="dept-dot hra intra-mt-5"></span>
                                <div>
                                    <div class="intra-text-white-600-sm" >Annual
                                        Performance Review Cycle</div>
                                    <div class="intra-meta-subtitle" >Self-assessment
                                        form DOC-2026-009 is ready.</div>
                                    <div class="intra-mono-dept-meta"
                                        >
                                        2h ago</div>
                                </div>
                            </div>
                            <div class="intra-padding-0-75rem-1rem-beec" >
                                <span class="dept-dot eng intra-mt-5"></span>
                                <div>
                                    <div class="intra-text-white-600-sm" >VP-900 Firmware
                                        Release v4.2</div>
                                    <div class="intra-meta-subtitle" >Testing suite
                                        deployed to Almaty Lab 1.</div>
                                    <div class="intra-mono-dept-meta"
                                        >
                                        5h ago</div>
                                </div>
                            </div>
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
                            <a href="../Online Shop B2B/index.php" class="ecosystem-item">
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
                            <a href="../HR System/index.php" class="ecosystem-item">
                                <span class="material-symbols-outlined intra-color-6e4c7c-font-size-e5aa">person_search</span>
                                <div>
                                    <div class="intra-text-white-bold-sm" >Human Resources (HR)</div>
                                    <div class="intra-mono-muted-xs" >hr.vostokpribor.local • System 06</div>
                                </div>
                            </a>
                            <a href="../Finance & Billing/index.php" class="ecosystem-item">
                                <span class="material-symbols-outlined intra-color-2e6e4e-font-size-0395">receipt_long</span>
                                <div>
                                    <div class="intra-text-white-bold-sm" >Finance & Billing</div>
                                    <div class="intra-mono-muted-xs" >finance.vostokpribor.local • System 07</div>
                                </div>
                            </a>
                            <a href="../IT Helpdesk/index.php" class="ecosystem-item">
                                <span class="material-symbols-outlined intra-color-c97a3d-font-size-d3fb">support_agent</span>
                                <div>
                                    <div class="intra-text-white-bold-sm" >IT Helpdesk & Service</div>
                                    <div class="intra-mono-muted-xs" >helpdesk.vostokpribor.local • System 08</div>
                                </div>
                            </a>
                            <a href="../File Center/index.php" class="ecosystem-item">
                                <span class="material-symbols-outlined intra-color-5a6470-font-size-48ce">folder_zip</span>
                                <div>
                                    <div class="intra-text-white-bold-sm" >File Center Hub</div>
                                    <div class="intra-mono-muted-xs" >files.vostokpribor.local • System 09</div>
                                </div>
                            </a>
                            <a href="../Developer/index.php" class="ecosystem-item">
                                <span class="material-symbols-outlined intra-color-1e8fa6-font-size-e073">terminal</span>
                                <div>
                                    <div class="intra-text-white-bold-sm" >Developer / API Portal</div>
                                    <div class="intra-mono-muted-xs" >developer.vostokpribor.local • System 10</div>
                                </div>
                            </a>
                            <a href="../Admin & Governance Portal/index.php" class="ecosystem-item">
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
                        EM
                        <span class="avatar-status-dot online"></span>
                    </div>
                    <div class="user-info-text intra-flex-col">
                        <span class="intra-color-ffffff-font-weight-b287" >Elena
                            Morozova</span>
                        <span class="intra-mono-muted-xs" >CTO • L4
                            Clear</span>
                    </div>
                </div>

            </div>


            <!-- Top Bar Sign Out -->
            <a href="../api/logout.php?system=Employee%20Intranet&redirect=../Employee%20Intranet/login.php" class="top-signout-btn" title="Sign Out of Employee Intranet" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" ><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
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
                <span class="sidebar-badge intra-margin-left-auto-background-8fba">95</span>
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
        <div class="intra-padding-1rem-border-top-dd4e"
            >
            <div class="intra-flex-center-gap-md" >
                <div class="avatar-circle intra-background-color-1a73e8-5fb8">
                    EM
                    <span class="avatar-status-dot online"></span>
                </div>
                <div class="user-info-text intra-flex-1-min-0">
                    <div class="intra-font-size-0-8125rem-863b"
                        >
                        Elena Morozova
                    </div>
                    <div class="intra-font-size-0-6875rem-50ad"
                        >
                        <span class="dept-badge itd intra-padding-0-0-3rem-188d">ITD</span>
                        <span>Chief Tech Officer</span>
                    </div>
                </div>
                <button class="intra-btn-icon-ghost" type="button"
                    
                    title="Lock Session"
                    onclick="window.showIntranetToast('Security Notice', 'Session verified under ISO-27001 Zero-Trust policy.', 'info')">
                    <span class="material-symbols-outlined intra-text-11">lock</span>
                </button>
            </div>
        </div>


        <!-- Log Out -->

    </aside>

    <!-- Left-edge hover detection strip for collapsed rail state -->
    <div id="sidebar-hover-trigger" title="Hover to expand menu"></div>
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
                     ========================================================== -->
                <div class="intra-display-flex-flex-direction-f48b" id="announcements-feed" >

                    <!-- Feed Filter Tabs -->
                    <div class="intra-display-flex-align-items-29ed"
                        >
                        <div class="intra-display-flex-gap-0-5fa1" >
                            <span class="intra-font-weight-600-font-bc25" >All
                                Updates</span>
                            <span class="intra-background-var-neutral-100-9b42"
                                >5
                                New</span>
                        </div>
                        <span class="intra-font-size-0-75rem-db0e" >
                            Live Feed • Synchronized
                        </span>
                    </div>

                    <!-- Feed Card 1: ITD Zero Trust -->
                    <article class="feed-card">
                        <div class="intra-flex-between-mb-sm"
                            >
                            <div class="intra-flex-center-gap-sm" >
                                <span class="dept-dot itd"></span>
                                <span class="dept-badge itd">ITD</span>
                                <span class="intra-text-neutral-sm" >• Information
                                    Technology</span>
                            </div>
                            <span class="badge-classification confidential">Confidential</span>
                        </div>

                        <h3 class="intra-heading-h3"
                            >
                            Zero-Trust Boundary Implementation: VPN Access Migration Completed
                        </h3>

                        <p class="intra-desc-p"
                            >
                            All remote telemetry endpoints and staff laptops have now migrated to WireGuard encrypted
                            tunnels with certificate pinning. Legacy OpenVPN profiles will be decommissioned this Friday
                            at 22:00 Almaty time. Please review DOC-2026-001 for updated routing settings.
                        </p>

                        <div class="intra-card-footer-meta"
                            >
                            <div class="intra-flex-center-gap-sm" >
                                <div class="avatar-circle intra-background-color-1a73e8-width-a329">
                                    RK</div>
                                <span class="intra-font-500-neutral" >Ruslan Kim</span>
                                <span class="intra-color-neutral-400" >•</span>
                                <span class="intra-color-neutral-500" >3 hours ago • 2 min read</span>
                            </div>
                            <div class="intra-flex-center-gap-neutral" >
                                <span class="intra-flex-center-gap-xs-pointer" 
                                    onclick="window.showIntranetToast('Feedback Logged', 'Acknowledgment registered.', 'info')">
                                    <span class="material-symbols-outlined intra-text-base">thumb_up</span> 18
                                </span>
                                <span class="intra-flex-center-gap-xs-pointer" >
                                    <span class="material-symbols-outlined intra-text-base">chat_bubble_outline</span> 4
                                </span>
                            </div>
                        </div>
                    </article>

                    <!-- Feed Card 2: ENG Calibration Firmware -->
                    <article class="feed-card">
                        <div class="intra-flex-between-mb-sm"
                            >
                            <div class="intra-flex-center-gap-sm" >
                                <span class="dept-dot eng"></span>
                                <span class="dept-badge eng">ENG</span>
                                <span class="intra-text-neutral-sm" >• Engineering & R&D</span>
                            </div>
                            <span class="badge-classification internal">Internal</span>
                        </div>

                        <h3 class="intra-heading-h3"
                            >
                            Almaty Calibration Cleanroom Lab 2 Achieves ±0.02% Precision Baseline
                        </h3>

                        <p class="intra-desc-p"
                            >
                            Through the integration of our VP-900 digital metrology suite, Cleanroom Lab 2 has
                            officially passed Kazakhstan National Standard (KazInMetr) certification. The bench testing
                            protocol for high-temperature turbine flow sensors is now available in the documentation
                            archive.
                        </p>

                        <div class="intra-card-footer-meta"
                            >
                            <div class="intra-flex-center-gap-sm" >
                                <div class="avatar-circle intra-background-color-137333-width-682f">
                                    AZ</div>
                                <span class="intra-font-500-neutral" >Arman Zhumabayev</span>
                                <span class="intra-color-neutral-400" >•</span>
                                <span class="intra-color-neutral-500" >Yesterday • 4 min read</span>
                            </div>
                            <div class="intra-flex-center-gap-neutral" >
                                <span class="intra-flex-center-gap-xs-pointer" 
                                    onclick="window.showIntranetToast('Feedback Logged', 'Acknowledgment registered.', 'info')">
                                    <span class="material-symbols-outlined intra-text-base">thumb_up</span> 32
                                </span>
                                <span class="intra-flex-center-gap-xs-pointer" >
                                    <span class="material-symbols-outlined intra-text-base">chat_bubble_outline</span> 9
                                </span>
                            </div>
                        </div>
                    </article>

                    <!-- Feed Card 3: HRA Health & Benefits -->
                    <article class="feed-card">
                        <div class="intra-flex-between-mb-sm"
                            >
                            <div class="intra-flex-center-gap-sm" >
                                <span class="dept-dot hra"></span>
                                <span class="dept-badge hra">HRA</span>
                                <span class="intra-text-neutral-sm" >• Human Resources</span>
                            </div>
                            <span class="badge-classification public">Public</span>
                        </div>

                        <h3 class="intra-heading-h3"
                            >
                            Annual Health Screening & Corporate Medical Package 2026-2027
                        </h3>

                        <p class="intra-desc-p"
                            >
                            All employees at the Almaty Central Facility and Regional Logistics Warehouses are eligible
                            for comprehensive medical checkups starting October 1. Book your preferred clinical provider
                            slot through the HR portal or visit Annex Suite 102.
                        </p>

                        <div class="intra-card-footer-meta"
                            >
                            <div class="intra-flex-center-gap-sm" >
                                <div class="avatar-circle intra-background-color-6e4c7c-width-bec3">
                                    GK</div>
                                <span class="intra-font-500-neutral" >Gulnara Kassymova</span>
                                <span class="intra-color-neutral-400" >•</span>
                                <span class="intra-color-neutral-500" >2 days ago • 1 min read</span>
                            </div>
                            <div class="intra-flex-center-gap-neutral" >
                                <span class="intra-flex-center-gap-xs-pointer" 
                                    onclick="window.showIntranetToast('Feedback Logged', 'Acknowledgment registered.', 'info')">
                                    <span class="material-symbols-outlined intra-text-base">thumb_up</span> 25
                                </span>
                                <span class="intra-flex-center-gap-xs-pointer" >
                                    <span class="material-symbols-outlined intra-text-base">chat_bubble_outline</span> 2
                                </span>
                            </div>
                        </div>
                    </article>

                    <!-- Feed Card 4: OPS Logistics Pipeline -->
                    <article class="feed-card">
                        <div class="intra-flex-between-mb-sm"
                            >
                            <div class="intra-flex-center-gap-sm" >
                                <span class="dept-dot ops"></span>
                                <span class="dept-badge ops">OPS</span>
                                <span class="intra-text-neutral-sm" >• Operations &
                                    Logistics</span>
                            </div>
                            <span class="badge-classification internal">Internal</span>
                        </div>

                        <h3 class="intra-heading-h3"
                            >
                            Cross-Border Freight Clearance Timetable: Aktau Port Corridor
                        </h3>

                        <p class="intra-desc-p"
                            >
                            Maritime container shipments carrying heavy automation actuators through Caspian Transit
                            Corridor B have resumed full maritime schedules. Customs documentation manifests must be
                            signed via System 06 (WMS) prior to dispatch.
                        </p>

                        <div class="intra-card-footer-meta"
                            >
                            <div class="intra-flex-center-gap-sm" >
                                <div class="avatar-circle intra-background-color-00796b-width-b618">
                                    SL</div>
                                <span class="intra-font-500-neutral" >Sofia Lindqvist</span>
                                <span class="intra-color-neutral-400" >•</span>
                                <span class="intra-color-neutral-500" >3 days ago • 3 min read</span>
                            </div>
                            <div class="intra-flex-center-gap-neutral" >
                                <span class="intra-flex-center-gap-xs-pointer" 
                                    onclick="window.showIntranetToast('Feedback Logged', 'Acknowledgment registered.', 'info')">
                                    <span class="material-symbols-outlined intra-text-base">thumb_up</span> 14
                                </span>
                                <span class="intra-flex-center-gap-xs-pointer" >
                                    <span class="material-symbols-outlined intra-text-base">chat_bubble_outline</span> 1
                                </span>
                            </div>
                        </div>
                    </article>

                </div>

                <!-- ==========================================================
                     RIGHT COLUMN: STACKED WIDGETS
                     ========================================================== -->
                <div class="intra-display-flex-flex-direction-a81b" >

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

                            <div class="quick-link-btn" onclick="window.downloadDocSimulation('DOC-2026-005')">
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
                                onclick="window.showIntranetToast('Conference Room Booking', 'Calendar integration active. Select room slot.', 'info')">
                                <span class="material-symbols-outlined intra-color-0e7c86-font-size-0fa5">domain</span>
                                <span class="intra-text-center-bold-xs" >Room
                                    Booking</span>
                            </div>

                            <a href="EmployeeDirectory.php" class="quick-link-btn">
                                <span class="material-symbols-outlined intra-color-6e4c7c-font-size-d7e2">schema</span>
                                <span class="intra-text-center-bold-xs" >Org
                                    Chart</span>
                            </a>

                            <div class="quick-link-btn" onclick="window.downloadDocSimulation('DOC-2026-008')">
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
    <script src="../assets/js/api-client.js"></script>
    <script src="js/intranet-data.js"></script>
</body>

</html>