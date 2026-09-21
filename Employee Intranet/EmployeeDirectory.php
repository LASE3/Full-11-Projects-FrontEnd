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
    <title>VOSTOKPRIBOR Intranet • Employee Directory</title>
    <meta name="description" content="VOSTOKPRIBOR Enterprise Employee Directory & Organizational Registry">
    
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
        <div style="height: 100%; display: flex; align-items: center; justify-content: space-between; padding: 0 1.25rem;">
            
            <!-- Left Brand & Sidebar Toggle -->
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button type="button" id="sidebar-toggle-btn" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); color: #FFFFFF; width: 36px; height: 36px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.15s;" title="Toggle Sidebar (Ctrl+B)">
                    <span class="material-symbols-outlined" style="font-size: 1.25rem;">menu</span>
                </button>

                <a href="Dashboard.php" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none; cursor: pointer;">
                    <img alt="VOSTOKPRIBOR Official Mark" style="height: 32px; width: auto; object-fit: contain;" src="assets/logo.svg" />
                    <div style="height: 24px; width: 1px; background: rgba(255,255,255,0.2);"></div>
                    <div style="display: flex; flex-direction: column;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="color: #FFFFFF; font-weight: 700; font-size: 0.9375rem; letter-spacing: 0.04em;">VOSTOKPRIBOR</span>
                            <span style="background-color: var(--system-accent); color: #FFFFFF; font-family: var(--font-mono); font-size: 0.625rem; font-weight: 600; padding: 0.15rem 0.4rem; border-radius: 2px; letter-spacing: 0.06em;">INTRANET</span>
                        </div>
                        <span style="font-family: var(--font-mono); font-size: 0.625rem; color: #8A94A0;">intranet.vostokpribor.local • Est. 1968</span>
                    </div>
                </a>
            </div>

            <!-- Center Search Bar (Command Palette Launcher Ctrl+K) -->
            <div style="flex: 1; max-width: 32rem; margin: 0 1.5rem;">
                <div class="header-search-bar" style="display: flex; align-items: center; padding: 0.375rem 0.75rem; gap: 0.5rem;">
                    <span class="material-symbols-outlined" style="color: #939FA8; font-size: 1.15rem;">search</span>
                    <input type="text" id="header-search-input" placeholder="Search intranet, personnel, policies, or forms... (Ctrl+K)" style="background: transparent; border: none; outline: none; color: #FFFFFF; font-size: 0.8125rem; width: 100%; font-family: var(--font-sans);" readonly>
                    <span style="background: rgba(255,255,255,0.1); color: #BDC6CF; font-family: var(--font-mono); font-size: 0.6875rem; padding: 0.1rem 0.35rem; border-radius: 2px;">⌘K</span>
                </div>
            </div>

            <!-- Right Actions & Profile -->
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <!-- Notifications Bell -->
                <div style="position: relative;">
                    <button type="button" id="notifications-toggle-btn" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); color: #FFFFFF; width: 36px; height: 36px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative;">
                        <span class="material-symbols-outlined" style="font-size: 1.25rem;">notifications</span>
                        <span id="notif-unread-count" style="position: absolute; top: -3px; right: -3px; width: 15px; height: 15px; background-color: var(--brand-accent); color: #0F2438; border-radius: 50%; font-size: 0.625rem; font-weight: 700; display: flex; align-items: center; justify-content: center;">3</span>
                    </button>

                    <!-- Notifications Dropdown Popover -->
                    <div class="notifications-popover" id="notifications-popover" style="width: 20rem; right: 0;">
                        <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-size: 0.8125rem; font-weight: 600; color: #FFFFFF;">System Notifications</span>
                            <button type="button" id="clear-notifications-btn" style="background: transparent; border: none; color: var(--system-accent); font-size: 0.75rem; cursor: pointer; text-decoration: underline;">Mark All Read</button>
                        </div>
                        <div style="max-height: 18rem; overflow-y: auto;">
                            <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; gap: 0.75rem;">
                                <span class="dept-dot itd" style="margin-top: 5px;"></span>
                                <div>
                                    <div style="font-size: 0.8125rem; font-weight: 600; color: #FFFFFF;">Directory Data Synced</div>
                                    <div style="font-size: 0.75rem; color: #939FA8; margin-top: 2px;">95 employee records synchronized with Active Directory.</div>
                                    <div style="font-size: 0.6875rem; color: #5C7290; margin-top: 4px; font-family: var(--font-mono);">Just now</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ecosystem Switcher (11 Systems) -->
                <div style="position: relative;">
                    <button type="button" id="ecosystem-toggle-btn" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); color: #FFFFFF; width: 36px; height: 36px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; cursor: pointer;" title="VOSTOKPRIBOR Ecosystem Switcher">
                        <span class="material-symbols-outlined" style="font-size: 1.25rem;">apps</span>
                    </button>

                    <!-- 11-System Ecosystem Dropdown Menu -->
                    <div class="ecosystem-dropdown" id="ecosystem-dropdown" style="width: 22rem; right: 0;">
                        <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(220,225,230,0.12); display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <span style="font-size: 0.8125rem; font-weight: 700; color: #FFFFFF; letter-spacing: 0.04em;">VOSTOKPRIBOR ECOSYSTEM</span>
                                <div style="font-size: 0.6875rem; color: #8A94A0; font-family: var(--font-mono);">11 Unified Systems</div>
                            </div>
                            <span class="badge-classification internal">Enterprise SSO</span>
                        </div>
                        <div style="padding: 0.5rem; max-height: 24rem; overflow-y: auto; display: flex; flex-direction: column; gap: 0.25rem;">
                            <a href="../VOSTOKPRIBOR Corporate Web Platform/index.php" class="ecosystem-item">
                                <span class="material-symbols-outlined" style="color: #1B3A5C; font-size: 1.25rem;">language</span>
                                <div>
                                    <div style="color: #FFFFFF; font-size: 0.8125rem; font-weight: 600;">Corporate Web Platform</div>
                                    <div style="color: #8A94A0; font-size: 0.6875rem; font-family: var(--font-mono);">vostokpribor.local • System 01</div>
                                </div>
                            </a>
                            <a href="../Online Shop B2B/index.php" class="ecosystem-item">
                                <span class="material-symbols-outlined" style="color: #0E7C86; font-size: 1.25rem;">shopping_bag</span>
                                <div>
                                    <div style="color: #FFFFFF; font-size: 0.8125rem; font-weight: 600;">B2B Online Shop</div>
                                    <div style="color: #8A94A0; font-size: 0.6875rem; font-family: var(--font-mono);">shop.vostokpribor.local • System 02</div>
                                </div>
                            </a>
                            <a href="../Customer Portal/Dashboard.php" class="ecosystem-item">
                                <span class="material-symbols-outlined" style="color: #E8A33D; font-size: 1.25rem;">dashboard</span>
                                <div>
                                    <div style="color: #FFFFFF; font-size: 0.8125rem; font-weight: 600;">Customer Portal</div>
                                    <div style="color: #8A94A0; font-size: 0.6875rem; font-family: var(--font-mono);">portal.vostokpribor.local • System 03</div>
                                </div>
                            </a>
                            <a href="Dashboard.php" class="ecosystem-item" style="background-color: rgba(92,114,144,0.25); border-left-color: var(--system-accent);">
                                <span class="material-symbols-outlined" style="color: var(--system-accent); font-size: 1.25rem;">hub</span>
                                <div>
                                    <div style="color: #FFFFFF; font-size: 0.8125rem; font-weight: 600;">Employee Intranet</div>
                                    <div style="color: #BDC6CF; font-size: 0.6875rem; font-family: var(--font-mono);">intranet.vostokpribor.local • Current (Sys 04)</div>
                                </div>
                            </a>
                            <a href="../CRM/index.php" class="ecosystem-item">
                                <span class="material-symbols-outlined" style="color: #3B4C8C; font-size: 1.25rem;">groups</span>
                                <div>
                                    <div style="color: #FFFFFF; font-size: 0.8125rem; font-weight: 600;">CRM Platform</div>
                                    <div style="color: #8A94A0; font-size: 0.6875rem; font-family: var(--font-mono);">crm.vostokpribor.local • System 05</div>
                                </div>
                            </a>
                            <a href="../HR System/index.php" class="ecosystem-item">
                                <span class="material-symbols-outlined" style="color: #6E4C7C; font-size: 1.25rem;">person_search</span>
                                <div>
                                    <div style="color: #FFFFFF; font-size: 0.8125rem; font-weight: 600;">Human Resources (HR)</div>
                                    <div style="color: #8A94A0; font-size: 0.6875rem; font-family: var(--font-mono);">hr.vostokpribor.local • System 06</div>
                                </div>
                            </a>
                            <a href="../Finance & Billing/index.php" class="ecosystem-item">
                                <span class="material-symbols-outlined" style="color: #2E6E4E; font-size: 1.25rem;">receipt_long</span>
                                <div>
                                    <div style="color: #FFFFFF; font-size: 0.8125rem; font-weight: 600;">Finance & Billing</div>
                                    <div style="color: #8A94A0; font-size: 0.6875rem; font-family: var(--font-mono);">finance.vostokpribor.local • System 07</div>
                                </div>
                            </a>
                            <a href="../IT Helpdesk/index.php" class="ecosystem-item">
                                <span class="material-symbols-outlined" style="color: #C97A3D; font-size: 1.25rem;">support_agent</span>
                                <div>
                                    <div style="color: #FFFFFF; font-size: 0.8125rem; font-weight: 600;">IT Helpdesk & Service</div>
                                    <div style="color: #8A94A0; font-size: 0.6875rem; font-family: var(--font-mono);">helpdesk.vostokpribor.local • System 08</div>
                                </div>
                            </a>
                            <a href="../File Center/index.php" class="ecosystem-item">
                                <span class="material-symbols-outlined" style="color: #5A6470; font-size: 1.25rem;">folder_zip</span>
                                <div>
                                    <div style="color: #FFFFFF; font-size: 0.8125rem; font-weight: 600;">File Center Hub</div>
                                    <div style="color: #8A94A0; font-size: 0.6875rem; font-family: var(--font-mono);">files.vostokpribor.local • System 09</div>
                                </div>
                            </a>
                            <a href="../Developer/index.php" class="ecosystem-item">
                                <span class="material-symbols-outlined" style="color: #1E8FA6; font-size: 1.25rem;">terminal</span>
                                <div>
                                    <div style="color: #FFFFFF; font-size: 0.8125rem; font-weight: 600;">Developer / API Portal</div>
                                    <div style="color: #8A94A0; font-size: 0.6875rem; font-family: var(--font-mono);">developer.vostokpribor.local • System 10</div>
                                </div>
                            </a>
                            <a href="../Admin & Governance Portal/index.php" class="ecosystem-item">
                                <span class="material-symbols-outlined" style="color: #B23A32; font-size: 1.25rem;">security</span>
                                <div>
                                    <div style="color: #FFFFFF; font-size: 0.8125rem; font-weight: 600;">Admin & Governance</div>
                                    <div style="color: #8A94A0; font-size: 0.6875rem; font-family: var(--font-mono);">admin.vostokpribor.local • System 11</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Authenticated User Profile Avatar -->
                <div style="display: flex; align-items: center; gap: 0.625rem; padding-left: 0.5rem; border-left: 1px solid rgba(255,255,255,0.12);">
                    <div class="avatar-circle" style="background-color: #1A73E8; width: 34px; height: 34px; font-size: 0.8125rem;">
                        EM
                        <span class="avatar-status-dot online"></span>
                    </div>
                    <div style="display: flex; flex-direction: column;" class="user-info-text">
                        <span style="color: #FFFFFF; font-weight: 600; font-size: 0.8125rem; line-height: 1.2;">Elena Morozova</span>
                        <span style="color: #8A94A0; font-size: 0.6875rem; font-family: var(--font-mono);">CTO • L4 Clear</span>
                    </div>
                </div>

            </div>

        
<!-- Top Bar Sign Out -->
<a href="../api/logout.php?system=Employee%20Intranet&redirect=../Employee%20Intranet/login.php" class="top-signout-btn" title="Sign Out of Employee Intranet" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:4px;background:rgba(178,58,50,0.2);border:1px solid rgba(178,58,50,0.5);color:#FF8080;font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;margin-left:8px;vertical-align:middle;transition:all 0.2s;" onmouseover="this.style.background='rgba(178,58,50,0.4)';this.style.color='#FFFFFF'" onmouseout="this.style.background='rgba(178,58,50,0.2)';this.style.color='#FF8080'"><span class="material-symbols-outlined" style="font-size:15px;line-height:1;">logout</span><span>Sign Out</span></a>
</div>
    </header>

    <!-- ======================================================================
         LEFT SIDEBAR: Dark Navy with Slate Blue Accent (#5C7290)
         ====================================================================== -->
    <aside class="intranet-sidebar" id="intranet-sidebar">
        
        <div style="padding-top: 1rem; display: flex; flex-direction: column; gap: 0.25rem;">
            
            <div style="padding: 0 1rem 0.5rem; font-size: 0.6875rem; font-family: var(--font-mono); color: #687482; font-weight: 600; text-transform: uppercase;" class="sidebar-section-title">
                COMMUNICATIONS & HUB
            </div>

            <!-- News & Announcements -->
            <a href="Dashboard.php" class="sidebar-link">
                <span class="material-symbols-outlined" style="font-size: 1.25rem;">campaign</span>
                <span class="sidebar-label">News & Announcements</span>
            </a>

            <!-- Employee Directory (Active) -->
            <a href="EmployeeDirectory.php" class="sidebar-link active">
                <span class="material-symbols-outlined" style="font-size: 1.25rem;">badge</span>
                <span class="sidebar-label">Employee Directory</span>
                <span class="sidebar-badge" style="margin-left: auto; background: var(--system-accent); color: #FFFFFF; font-family: var(--font-mono); font-size: 0.6875rem; padding: 0.1rem 0.4rem; border-radius: 999px;">95</span>
            </a>

            <!-- Policies & Forms -->
            <a href="PoliciesAndForms.php" class="sidebar-link">
                <span class="material-symbols-outlined" style="font-size: 1.25rem;">policy</span>
                <span class="sidebar-label">Policies & Forms</span>
            </a>

            <div style="padding: 1rem 1rem 0.5rem; font-size: 0.6875rem; font-family: var(--font-mono); color: #687482; font-weight: 600; text-transform: uppercase;" class="sidebar-section-title">
                EMPLOYEE SERVICES
            </div>

            <!-- Helpdesk / IT Support -->
            <a href="javascript:void(0)" class="sidebar-link" onclick="window.openQuickRequestModal('it')">
                <span class="material-symbols-outlined" style="font-size: 1.25rem;">support_agent</span>
                <span class="sidebar-label">Helpdesk / IT Support</span>
            </a>

            <!-- Company Calendar -->
            <a href="javascript:void(0)" class="sidebar-link" onclick="window.showIntranetToast('Calendar Sync Active', 'Connected to Almaty Exchange CalDAV server.', 'info')">
                <span class="material-symbols-outlined" style="font-size: 1.25rem;">event</span>
                <span class="sidebar-label">Company Calendar</span>
            </a>

            <!-- Room Booking -->
            <a href="javascript:void(0)" class="sidebar-link" onclick="window.showIntranetToast('Resource Scheduler', 'Conference Rooms Alpha & Beta available today.', 'info')">
                <span class="material-symbols-outlined" style="font-size: 1.25rem;">meeting_room</span>
                <span class="sidebar-label">Room Booking</span>
            </a>

        </div>

        <!-- Sidebar Footer: User Card -->
        <div style="padding: 1rem; border-top: 1px solid rgba(220, 225, 230, 0.12); background-color: rgba(0, 0, 0, 0.15);">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div class="avatar-circle" style="background-color: #1A73E8;">
                    EM
                    <span class="avatar-status-dot online"></span>
                </div>
                <div style="flex: 1; min-width: 0;" class="user-info-text">
                    <div style="font-size: 0.8125rem; font-weight: 600; color: #FFFFFF; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        Elena Morozova
                    </div>
                    <div style="font-size: 0.6875rem; color: #8A94A0; display: flex; align-items: center; gap: 0.25rem;">
                        <span class="dept-badge itd" style="padding: 0 0.3rem;">ITD</span>
                        <span>Chief Tech Officer</span>
                    </div>
                </div>
                <button type="button" style="background: transparent; border: none; color: #8A94A0; cursor: pointer; padding: 0;" title="Lock Session" onclick="window.showIntranetToast('Security Notice', 'Session verified under ISO-27001 Zero-Trust policy.', 'info')">
                    <span class="material-symbols-outlined" style="font-size: 1.1rem;">lock</span>
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
        <div style="padding: 1.5rem; max-width: 86rem; margin: 0 auto;">
            
            <!-- Page Header Breadcrumb -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; font-family: var(--font-mono); color: var(--neutral-600); margin-bottom: 0.25rem;">
                        <span>INTRANET</span>
                        <span>/</span>
                        <span style="color: var(--system-accent); font-weight: 600;">EMPLOYEE DIRECTORY</span>
                    </div>
                    <h1 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--neutral-900); letter-spacing: -0.02em;">
                        Personnel & Organizational Directory
                    </h1>
                </div>

                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span class="badge-classification internal" id="dir-results-count">
                        Loading Personnel...
                    </span>
                    <button type="button" class="btn btn-secondary" onclick="window.openQuickRequestModal('leave')" style="display: inline-flex; align-items: center; gap: 0.375rem;">
                        <span class="material-symbols-outlined" style="font-size: 1rem;">date_range</span>
                        Leave Requisition
                    </button>
                </div>
            </div>

            <!-- ==============================================================
                 SEARCH & FILTER TOOLBAR
                 ============================================================== -->
            <div class="intranet-card" style="padding: 1rem 1.25rem; margin-bottom: 1.5rem;">
                <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;" class="dir-filter-grid">
                    
                    <style>
                        @media (min-width: 768px) {
                            .dir-filter-grid {
                                grid-template-columns: 2fr 1.2fr 1.2fr auto !important;
                                align-items: center;
                            }
                        }
                    </style>

                    <!-- Text Search Input -->
                    <div style="position: relative;">
                        <span class="material-symbols-outlined" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--neutral-500); font-size: 1.15rem;">person_search</span>
                        <input type="text" id="dir-search-input" class="form-input" placeholder="Search by name, job title, email, room, or ID..." style="padding-left: 2.25rem;">
                    </div>

                    <!-- Department Dropdown -->
                    <div>
                        <select id="dir-dept-filter" class="form-input" style="font-size: 0.8125rem;">
                            <option value="ALL">All Departments (95)</option>
                            <option value="ENG">Engineering & R&D (ENG - 16)</option>
                            <option value="OPS">Operations & Logistics (OPS - 20)</option>
                            <option value="ITD">Information Technology (ITD - 14)</option>
                            <option value="SAL">Sales & Business Dev (SAL - 16)</option>
                            <option value="FIN">Corporate Finance (FIN - 10)</option>
                            <option value="HRA">Human Resources (HRA - 8)</option>
                            <option value="GOV">Governance & Legal (GOV - 6)</option>
                            <option value="EXE">Executive Leadership (EXE - 5)</option>
                        </select>
                    </div>

                    <!-- Security Clearance Level Dropdown -->
                    <div>
                        <select id="dir-clearance-filter" class="form-input" style="font-size: 0.8125rem;">
                            <option value="ALL">All Clearance Levels</option>
                            <option value="public">L1 Public Personnel</option>
                            <option value="internal">L2 Internal Operations</option>
                            <option value="confidential">L3 Confidential Access</option>
                            <option value="restricted">L4 Restricted Leadership</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div>
                        <button type="button" class="btn btn-secondary" style="width: 100%; white-space: nowrap;" onclick="window.resetDirectoryFilters()">
                            <span class="material-symbols-outlined" style="font-size: 1rem; margin-right: 0.25rem;">restart_alt</span>
                            Reset
                        </button>
                    </div>

                </div>
            </div>

            <!-- ==============================================================
                 EMPLOYEE CARDS RESPONSIVE GRID
                 ============================================================== -->
            <div id="employee-directory-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem;">
                <!-- Populated dynamically by intranet.js -->
            </div>

        </div>
    </main>

    <!-- ======================================================================
         COMMAND PALETTE MODAL (Ctrl+K)
         ====================================================================== -->
    <div class="intranet-modal-backdrop" id="command-palette-modal">
        <div class="intranet-modal-container" style="max-width: 42rem; border: 1px solid var(--system-accent);">
            <div style="background-color: var(--brand-primary-dark); padding: 0.875rem 1.25rem; display: flex; align-items: center; gap: 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.12);">
                <span class="material-symbols-outlined" style="color: var(--system-accent);">search</span>
                <input type="text" id="command-palette-input" placeholder="Type a name, department, role, or policy..." style="background: transparent; border: none; outline: none; color: #FFFFFF; font-size: 1rem; width: 100%; font-family: var(--font-sans);">
                <button type="button" id="close-command-palette-btn" style="background: transparent; border: none; color: #8A94A0; cursor: pointer; padding: 0;">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div id="command-palette-results" style="max-height: 24rem; overflow-y: auto; padding: 0.5rem;"></div>
            <div style="padding: 0.5rem 1rem; background: var(--neutral-100); border-top: 1px solid var(--neutral-200); display: flex; align-items: center; justify-content: space-between; font-size: 0.6875rem; color: var(--neutral-600); font-family: var(--font-mono);">
                <span>Navigate with mouse or keyboard</span>
                <span>ESC to dismiss</span>
            </div>
        </div>
    </div>

    <!-- Toast Notification Anchor -->
    <div id="intranet-toast-container"></div>

    <!-- Single Consolidated JavaScript Engine -->
    <script src="js/intranet.js"></script>
</body>
</html>
