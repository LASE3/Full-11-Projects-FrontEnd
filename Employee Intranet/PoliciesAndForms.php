<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('EMP');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOSTOKPRIBOR Intranet • Policies & Forms Library</title>
    <meta name="description" content="VOSTOKPRIBOR Enterprise Classified Policy & Operational Forms Repository">

    <!-- Fonts: IBM Plex Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <!-- Unified Stylesheet -->
    <link rel="stylesheet" href="css/intranet.css">
</head>

<body>

    <!-- ======================================================================
         TOP BAR: Deep Navy with 4px Slate Blue Accent Stripe
         ====================================================================== -->
    <header class="intranet-header" id="intranet-header">
        <div class="intra-height-100-display-flex-01f3" >

            <!-- Left Brand & Sidebar Toggle -->
            <div class="intra-display-flex-align-items-ee8a" >
                <button class="intra-background-rgba-255-255-928c" type="button" id="sidebar-toggle-btn"  title="Toggle Sidebar (Ctrl+B)">
                    <span class="material-symbols-outlined intra-text-xl">menu</span>
                </button>

                <a class="intra-display-flex-align-items-a3ac" href="Dashboard.php" >
                    <img class="intra-height-32px-width-auto-7934" alt="VOSTOKPRIBOR Official Mark"  src="assets/logo.svg" />
                    <div class="intra-height-24px-width-1px-c7a3" ></div>
                    <div class="intra-flex-col" >
                        <div class="intra-flex-center-gap-sm" >
                            <span class="intra-color-ffffff-font-weight-5ca4" >VOSTOKPRIBOR</span>
                            <span class="intra-background-color-var-system-2816" >INTRANET</span>
                        </div>
                        <span class="intra-font-family-var-font-efd2" >intranet.vostokpribor.local • Est. 1968</span>
                    </div>
                </a>
            </div>

            <!-- Center Search Bar (Command Palette Launcher Ctrl+K) -->
            <div class="intra-flex-1-max-width-5505" >
                <div class="header-search-bar intra-display-flex-align-items-6d41">
                    <span class="material-symbols-outlined intra-color-939fa8-font-size-e00b">search</span>
                    <input class="intra-background-transparent-border-none-05e6" type="text" id="header-search-input" placeholder="Search intranet, personnel, policies, or forms... (Ctrl+K)"  readonly>
                    <span class="intra-background-rgba-255-255-5858" >⌘K</span>
                </div>
            </div>

            <!-- Right Actions & Profile -->
            <div class="intra-flex-center-gap-md" >
                <!-- Notifications Bell -->
                <div class="intra-pos-relative" >
                    <button class="intra-background-rgba-255-255-87b8" type="button" id="notifications-toggle-btn" >
                        <span class="material-symbols-outlined intra-text-xl">notifications</span>
                        <span class="intra-position-absolute-top-3px-e466" id="notif-unread-count" >3</span>
                    </button>

                    <!-- Notifications Dropdown Popover -->
                    <div class="notifications-popover" id="notifications-popover" >
                        <div class="intra-padding-0-75rem-1rem-82b8" >
                            <span class="intra-text-white-600-sm" >System Notifications</span>
                            <button class="intra-background-transparent-border-none-4d5e" type="button" id="clear-notifications-btn" >Mark All Read</button>
                        </div>
                        <div class="intra-max-height-18rem-overflow-db30" >
                            <div class="intra-dropdown-item-border" >
                                <span class="dept-dot itd intra-mt-5"></span>
                                <div>
                                    <div class="intra-text-white-600-sm" >Policy Revision Released</div>
                                    <div class="intra-meta-subtitle" >DOC-2026-007 Cryptographic Matrix v3.0 approved.</div>
                                    <div class="intra-mono-dept-meta" >1h ago</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ecosystem Switcher (11 Systems) -->
                <div class="intra-pos-relative" >
                    <button class="intra-background-rgba-255-255-57e6" type="button" id="ecosystem-toggle-btn"  title="VOSTOKPRIBOR Ecosystem Switcher">
                        <span class="material-symbols-outlined intra-text-xl">apps</span>
                    </button>

                    <!-- 11-System Ecosystem Dropdown Menu -->
                    <div class="ecosystem-dropdown" id="ecosystem-dropdown" >
                        <div class="intra-padding-0-75rem-1rem-4249" >
                            <div>
                                <span class="intra-font-size-0-8125rem-54a0" >VOSTOKPRIBOR ECOSYSTEM</span>
                                <div class="intra-font-size-0-6875rem-e9c1" >11 Unified Systems</div>
                            </div>
                            <span class="badge-classification internal">Enterprise SSO</span>
                        </div>
                        <div class="intra-padding-0-5rem-max-ea3f" >
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
                <div class="intra-display-flex-align-items-f8dd" >
                    <div class="avatar-circle intra-background-color-1a73e8-width-13e2">
                        EM
                        <span class="avatar-status-dot online"></span>
                    </div>
                    <div class="user-info-text intra-flex-col">
                        <span class="intra-color-ffffff-font-weight-b287" >Elena Morozova</span>
                        <span class="intra-mono-muted-xs" >CTO • L4 Clear</span>
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

        <div class="intra-padding-top-1rem-display-6c18" >

            <div class="sidebar-section-title intra-padding-0-1rem-0-35ef">
                COMMUNICATIONS & HUB
            </div>

            <!-- News & Announcements -->
            <a href="Dashboard.php" class="sidebar-link">
                <span class="material-symbols-outlined intra-text-xl">campaign</span>
                <span class="sidebar-label">News & Announcements</span>
            </a>

            <!-- Employee Directory -->
            <a href="EmployeeDirectory.php" class="sidebar-link">
                <span class="material-symbols-outlined intra-text-xl">badge</span>
                <span class="sidebar-label">Employee Directory</span>
                <span class="sidebar-badge intra-margin-left-auto-background-8fba">95</span>
            </a>

            <!-- Policies & Forms (Active) -->
            <a href="PoliciesAndForms.php" class="sidebar-link active">
                <span class="material-symbols-outlined intra-text-xl">policy</span>
                <span class="sidebar-label">Policies & Forms</span>
            </a>

            <div class="sidebar-section-title intra-padding-1rem-1rem-0-0f82">
                EMPLOYEE SERVICES
            </div>

            <!-- Helpdesk / IT Support -->
            <a href="javascript:void(0)" class="sidebar-link" onclick="window.openQuickRequestModal('it')">
                <span class="material-symbols-outlined intra-text-xl">support_agent</span>
                <span class="sidebar-label">Helpdesk / IT Support</span>
            </a>

            <!-- Company Calendar -->
            <a href="javascript:void(0)" class="sidebar-link" onclick="window.showIntranetToast('Calendar Sync Active', 'Connected to Almaty Exchange CalDAV server.', 'info')">
                <span class="material-symbols-outlined intra-text-xl">event</span>
                <span class="sidebar-label">Company Calendar</span>
            </a>

            <!-- Room Booking -->
            <a href="javascript:void(0)" class="sidebar-link" onclick="window.showIntranetToast('Resource Scheduler', 'Conference Rooms Alpha & Beta available today.', 'info')">
                <span class="material-symbols-outlined intra-text-xl">meeting_room</span>
                <span class="sidebar-label">Room Booking</span>
            </a>

        </div>

        <!-- Sidebar Footer: User Card -->
        <div class="intra-padding-1rem-border-top-dd4e" >
            <div class="intra-flex-center-gap-md" >
                <div class="avatar-circle intra-background-color-1a73e8-5fb8">
                    EM
                    <span class="avatar-status-dot online"></span>
                </div>
                <div class="user-info-text intra-flex-1-min-0">
                    <div class="intra-font-size-0-8125rem-863b" >
                        Elena Morozova
                    </div>
                    <div class="intra-font-size-0-6875rem-50ad" >
                        <span class="dept-badge itd intra-padding-0-0-3rem-188d">ITD</span>
                        <span>Chief Tech Officer</span>
                    </div>
                </div>
                <button class="intra-btn-icon-ghost" type="button"  title="Lock Session" onclick="window.showIntranetToast('Security Notice', 'Session verified under ISO-27001 Zero-Trust policy.', 'info')">
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
            <div class="intra-display-flex-align-items-feef" >
                <div>
                    <div class="intra-display-flex-align-items-f015" >
                        <span>INTRANET</span>
                        <span>/</span>
                        <span class="intra-color-var-system-accent-dff5" >POLICIES & FORMS</span>
                    </div>
                    <h1 class="intra-margin-0-font-size-d321" >
                        Policies & Forms Document Repository
                    </h1>
                </div>

                <div class="intra-flex-center-gap-md" >
                    <!-- Authorized Role Upload Document Button (Signal Amber CTA) -->
                    <button type="button" class="btn btn-primary" onclick="window.showIntranetToast('Document Submission', 'Upload portal active for L3/L4 clearance holders.', 'info')" >
                        <span class="material-symbols-outlined intra-text-11">upload_file</span>
                        Upload New Policy
                    </button>
                </div>
            </div>

            <!-- ==============================================================
                 TWO-COLUMN POLICY ARCHIVE: Categories (Left) + Table (Right)
                 ============================================================== -->
            <div class="policy-layout-grid intra-display-grid-grid-template-4561">

                <!-- Left Category Filter Column -->
                <div class="intranet-card intra-padding-1rem-d8d6">
                    <div class="intra-font-size-0-75rem-2356" >
                        DOCUMENT CATEGORIES
                    </div>

                    <div class="intra-display-flex-flex-direction-1622" >
                        <div class="policy-category-item active" data-category="all">
                            <span>All Documents</span>
                            <span class="intra-mono-neutral-xs" >15</span>
                        </div>
                        <div class="policy-category-item" data-category="security">
                            <span>Security & Cyber</span>
                            <span class="intra-mono-neutral-xs" >4</span>
                        </div>
                        <div class="policy-category-item" data-category="hr">
                            <span>HR Policies</span>
                            <span class="intra-mono-neutral-xs" >3</span>
                        </div>
                        <div class="policy-category-item" data-category="it">
                            <span>IT Standards</span>
                            <span class="intra-mono-neutral-xs" >2</span>
                        </div>
                        <div class="policy-category-item" data-category="expense">
                            <span>Expense & Finance</span>
                            <span class="intra-mono-neutral-xs" >2</span>
                        </div>
                        <div class="policy-category-item" data-category="templates">
                            <span>Templates & Ops</span>
                            <span class="intra-mono-neutral-xs" >4</span>
                        </div>
                    </div>

                    <div class="intra-margin-top-1-5rem-1e34" >
                        <div class="intra-font-weight-600-color-3da0" >
                            <span class="material-symbols-outlined intra-font-size-1rem-color-b9d6">verified_user</span>
                            Audit Integrity
                        </div>
                        <p class="intra-margin-0-color-var-0d6c" >
                            All documents are cryptographically signed under ISO-27001 standard.
                        </p>
                    </div>
                </div>

                <!-- Right Table Column -->
                <div class="intranet-card intra-p-125">

                    <!-- Search & Counter Header -->
                    <div class="intra-display-flex-align-items-45d4" >
                        <div class="intra-position-relative-flex-1-c6bc" >
                            <span class="material-symbols-outlined intra-position-absolute-left-0-1c5c">search</span>
                            <input type="text" id="policy-search-input" class="form-input" placeholder="Filter documents by ID, title, author, or keyword..." >
                        </div>
                        <span class="badge-classification internal" id="policies-count-label">
                            Loading Documents...
                        </span>
                    </div>

                    <!-- Classified Document Data Table -->
                    <div class="intra-overflow-x-auto-b561" >
                        <table class="table-classified intra-width-100-border-collapse-fe41">
                            <thead>
                                <tr class="intra-border-bottom-2px-solid-4f22" >
                                    <th class="intra-padding-0-75rem-1rem-94a6" >DOC ID</th>
                                    <th class="intra-p-75" >TITLE & AUTHOR</th>
                                    <th class="intra-p-75" >CATEGORY</th>
                                    <th class="intra-p-75" >CLASSIFICATION</th>
                                    <th class="intra-p-75" >UPDATED</th>
                                    <th class="intra-p-75" >SIZE</th>
                                    <th class="intra-padding-0-75rem-1rem-ab26" >ACTION</th>
                                </tr>
                            </thead>
                            <tbody id="policies-table-body">
                                <!-- Populated dynamically by intranet.js -->
                            </tbody>
                        </table>
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
            <div class="intra-background-color-var-brand-3290" >
                <span class="material-symbols-outlined intra-color-var-system-accent-06f9">search</span>
                <input class="intra-background-transparent-border-none-01d8" type="text" id="command-palette-input" placeholder="Type a name, department, role, or policy..." >
                <button class="intra-btn-icon-ghost" type="button" id="close-command-palette-btn" >
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="intra-max-height-24rem-overflow-0250" id="command-palette-results" ></div>
            <div class="intra-padding-0-5rem-1rem-acd8" >
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