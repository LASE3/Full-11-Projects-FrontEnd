<?php
require_once __DIR__ . '/customer_context.php';
require_once __DIR__ . '/customer_service.php';

$cusId = cus_getCurrentCustomerId();
$projects = cus_getProjects($cusId);
if (empty($projects)) {
    $pdo = getDbConnection();
    $projects = $pdo->query("SELECT p.*, e.full_name AS project_manager_name, e.email AS project_manager_email, (SELECT COUNT(*) FROM billing_cycles WHERE prj_id = p.prj_id) AS total_milestones, (SELECT COUNT(*) FROM billing_cycles WHERE prj_id = p.prj_id AND invoiced = 1) AS completed_milestones FROM projects p LEFT JOIN employees e ON p.project_manager_emp_id = e.emp_id ORDER BY p.prj_id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
}
$totalBudget = array_sum(array_column($projects, 'budget'));
$activeCount = count($projects);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>VOSTOKPRIBOR Portal - Projects</title>
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@400;500;600&amp;display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/common.css" />
    <link rel="stylesheet" href="css/projects.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/tailwind-config.js"></script>
    <script src="js/portal.js"></script>
    <link rel="stylesheet" href="../assets/css/api-ui.css">
    <script src="../assets/js/api-core.js"></script>
    <script src="../assets/js/api-customer.js"></script>
    <script src="js/portal-data.js"></script>
    <script src="js/projects.js"></script>
</head>

<body class="bg-background font-body-md text-body-md text-on-background">
    <header
        class="fixed top-0 left-0 right-0 h-16 bg-primary-container z-50 flex items-center justify-between px-unit-lg border-b border-outline/20">
        <div class="flex items-center gap-unit-base">
            <button id="sidebar-toggle-btn" class="p-1.5 -ml-1 mr-1 rounded text-on-primary-container hover:text-on-primary hover:bg-surface-container-high/10 transition-colors flex items-center justify-center cursor-pointer focus:outline-none" title="Toggle Navigation Menu (Ctrl+B)">
                <span class="material-symbols-outlined text-2xl" id="sidebar-toggle-icon">menu</span>
            </button>
            <img alt="VOSTOKPRIBOR Logo" class="h-8 w-auto object-contain cursor-pointer" onclick="location.href='Dashboard.php'"
                src="assets/logo.svg">
            <div class="h-6 w-px bg-outline/30"></div>
            <div class="flex flex-col">
                <div class="flex items-center gap-unit-xs"><span
                        class="font-headline-sm text-headline-sm text-on-primary font-semibold tracking-tight cursor-pointer" onclick="location.href='Dashboard.php'">VOSTOKPRIBOR</span><span
                        class="px-unit-xs py-0.5 rounded bg-surface-container-high/10 text-tertiary-fixed font-technical-tag text-technical-tag border border-tertiary-fixed/30">PORTAL</span>
                </div>
                <div
                    class="flex items-center gap-unit-xs text-on-primary-container font-technical-tag text-technical-tag">
                    <span class=""><?= htmlspecialchars($customer['company_name'] ?? 'Industrial Operations Client') ?></span><span class="text-outline">|</span><span
                        class="text-primary-fixed-dim"><?= htmlspecialchars($customer['tax_id'] ?? ($customer['code'] ?? 'VP-CORP')) ?></span>
                </div>
            </div>
        </div>
        <div class="w-64 md:w-80 lg:w-96 max-w-md mx-2 shrink-1">
            <div
                class="header-search-bar flex items-center bg-primary px-unit-md py-1.5 rounded border border-outline/30 text-on-primary-container cursor-pointer transition-colors hover:border-outline/50">
                <span class="material-symbols-outlined text-sm mr-unit-sm text-on-primary-container">search</span><input
                    class="header-search-input bg-transparent border-none outline-none font-body-sm text-body-sm text-on-primary placeholder:text-on-primary-container w-full cursor-pointer"
                    placeholder="Search projects, serial numbers, specs..." type="text"><span
                    class="font-technical-tag text-technical-tag px-1.5 py-0.5 rounded bg-surface-container-high/10 text-on-primary-container border border-outline/30">Ctrl+K</span>
            </div>
        </div>
        <div class="flex items-center gap-2 sm:gap-4 lg:gap-unit-lg shrink-0">
            <div
                class="hidden md:flex items-center gap-unit-xs px-unit-sm py-1 rounded bg-primary border border-outline/20 font-technical-tag text-technical-tag text-on-primary">
                <span class="w-2 h-2 rounded-full bg-tertiary-fixed animate-pulse"></span><span
                    class="text-on-primary-container">Telemetry Node:</span><span class="text-tertiary-fixed">Online
                    99.98%</span>
            </div>
            <div id="header-bell-btn" class="relative flex items-center text-on-primary-container hover:text-on-primary cursor-pointer" title="Operational Telemetry Alerts"><span
                    class="material-symbols-outlined">notifications</span><span id="bell-unread-dot"
                    class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-tertiary-fixed ring-2 ring-primary-container"></span>
            </div><a class="flex items-center text-on-primary-container hover:text-on-primary" href="Documents.php"
                title="Technical Documentation"><span class="material-symbols-outlined">menu_book</span></a>
            <div class="h-6 w-px bg-outline/30"></div>
            <div class="flex items-center gap-unit-sm cursor-pointer" id="header-profile-btn" onclick="location.href='AccountSettings.php'">
                <div class="flex flex-col text-right"><span
                        class="font-headline-sm text-headline-sm text-on-primary font-medium leading-none"><?= htmlspecialchars($currUser['full_name'] ?? 'Authorized User') ?></span><span
                        class="font-technical-tag text-technical-tag text-on-primary-container mt-0.5"><?= htmlspecialchars($currUser['role_name'] ?? 'Client Representative') ?></span></div>
                <div class="w-8 h-8 rounded-full bg-tertiary-fixed/30 text-tertiary-fixed border border-tertiary-fixed/50 flex items-center justify-center font-bold text-xs">
                    <?= htmlspecialchars(strtoupper(substr($currUser['full_name'] ?? 'U', 0, 2))) ?>
                </div>
            </div>

            <!-- Top Bar Sign Out -->
            <a href="./api/logout.php?redirect=../login.php" class="top-signout-btn" title="Sign Out of Customer Portal" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" ><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
        </div>
    </header>
    <aside id="portal-sidebar"
        class="fixed left-0 top-16 bottom-0 w-64 bg-primary-container z-40 flex flex-col justify-between border-r border-outline/20">
        <div class="py-unit-md">
            <div
                class="px-unit-base mb-unit-sm font-label-caps text-label-caps text-on-primary-container uppercase tracking-wider flex items-center justify-between">
                <span>Operational Navigation</span>
                <button id="sidebar-collapse-btn" class="text-on-primary-container hover:text-on-primary p-0.5 rounded hover:bg-surface-container-high/10 transition-colors cursor-pointer" title="Collapse Menu">
                    <span class="material-symbols-outlined text-base">chevron_left</span>
                </button>
            </div>
            <nav class="flex flex-col gap-0.5"
                data-active-classes="bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-on-tertiary-container">
                <a class="flex items-center gap-unit-sm px-unit-base py-unit-sm text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary transition-colors font-headline-sm text-headline-sm font-normal"
                    data-path="dashboard" href="Dashboard.php"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                        stroke-width="1.75" viewBox="0 0 24 24">
                        <rect height="7" rx="1" width="7" x="3" y="3"></rect>
                        <rect height="7" rx="1" width="7" x="14" y="3"></rect>
                        <rect height="7" rx="1" width="7" x="14" y="14"></rect>
                        <rect height="7" rx="1" width="7" x="3" y="14"></rect>
                    </svg><span class="">Dashboard</span></a><a
                    class="flex items-center gap-unit-sm px-unit-base py-unit-sm text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary transition-colors font-headline-sm text-headline-sm font-normal"
                    data-path="orders" href="Orders.php"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                        stroke-width="1.75" viewBox="0 0 24 24">
                        <path
                            d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z">
                        </path>
                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                        <path d="M12 22V12"></path>
                    </svg><span class="">Orders</span></a><a aria-current="page"
                    class="flex items-center gap-unit-sm px-unit-base py-unit-sm transition-colors bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-on-tertiary-container font-headline-sm text-headline-sm"
                    data-path="projects" href="ProjectListAndDetail.php"><svg class="w-4 h-4 shrink-0 text-tertiary-fixed" fill="none" stroke="currentColor"
                        stroke-width="1.75" viewBox="0 0 24 24">
                        <rect height="18" rx="2" width="18" x="3" y="3"></rect>
                        <path d="M3 9h18"></path>
                        <path d="M9 21V9"></path>
                    </svg><span class="">Projects</span></a><a
                    class="flex items-center gap-unit-sm px-unit-base py-unit-sm text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary transition-colors font-headline-sm text-headline-sm font-normal"
                    data-path="invoices" href="Invoices.php"><svg class="w-4 h-4 shrink-0" fill="none"
                        stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"></path>
                        <path d="M8 7h8"></path>
                        <path d="M8 11h8"></path>
                        <path d="M8 15h5"></path>
                    </svg><span class="">Invoices</span></a><a
                    class="flex items-center gap-unit-sm px-unit-base py-unit-sm text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary transition-colors font-headline-sm text-headline-sm font-normal"
                    data-path="documents" href="Documents.php"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                        stroke-width="1.75" viewBox="0 0 24 24">
                        <path
                            d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z">
                        </path>
                    </svg><span class="">Documents</span></a><a
                    class="flex items-center gap-unit-sm px-unit-base py-unit-sm text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary transition-colors font-headline-sm text-headline-sm font-normal"
                    data-path="support" href="SupportTicketView.php"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                        stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                        <path
                            d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z">
                        </path>
                    </svg><span class="">Support</span></a><a
                    class="flex items-center gap-unit-sm px-unit-base py-unit-sm text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary transition-colors font-headline-sm text-headline-sm font-normal"
                    data-path="account-settings" href="AccountSettings.php"><svg class="w-4 h-4 shrink-0" fill="none"
                        stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path
                            d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z">
                        </path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg><span class="">Account Settings</span></a>
                <a class="flex items-center gap-unit-sm px-unit-base py-unit-sm text-secondary-fixed hover:bg-surface-container-high/5 hover:text-on-primary transition-colors font-headline-sm text-headline-sm font-semibold" data-path="integrations" href="Integrations.php"><svg class="w-4 h-4 shrink-0 text-secondary-fixed" fill="none" stroke="#00E5FF" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                    </svg><span class="text-secondary-fixed">System Integrations</span><span class="ml-auto text-[10px] px-1.5 py-0.5 rounded bg-secondary-fixed/20 text-secondary-fixed font-mono">SYS03</span></a>
            </nav>
        </div>

        <div class="portal-manager-card p-3 m-3 rounded-lg bg-primary/95 border border-outline/25 shadow-sm text-xs select-none">
            <div class="flex items-center justify-between mb-1.5">
                <span class="font-label-caps text-[10px] text-tertiary-fixed uppercase font-bold tracking-wider">Assigned Manager</span>
                <span class="px-1.5 py-0.5 rounded bg-surface-container-high/15 text-on-primary-container font-mono text-[10px] border border-outline/20 font-semibold">SLA TIER 1</span>
            </div>
            <div class="flex items-center justify-between mb-0.5">
                <div>
                    <div class="font-headline-sm text-sm text-on-primary font-semibold leading-tight">Viktor Morozov</div>
                    <div class="text-[11px] text-on-primary-container/85 leading-snug">Sr. Industrial Systems Eng.</div>
                </div>
                <a href="SupportTicketView.php?ticket=TCK-9482" class="p-1 rounded bg-surface-container-high/15 text-tertiary-fixed hover:bg-surface-container-high/25 hover:text-on-primary transition-colors flex items-center justify-center shrink-0" title="Message Viktor Morozov in Support Desk">
                    <span class="material-symbols-outlined text-base">chat</span>
                </a>
            </div>
            <div class="mt-2 pt-2 border-t border-outline/20 flex flex-col gap-1.5 font-mono text-[11px]">
                <div class="flex items-center justify-between text-on-primary-container">
                    <span class="text-[10px] uppercase tracking-wider text-on-primary-container/70">Hotline:</span>
                    <a href="tel:+78124092211" class="text-on-primary font-medium hover:text-tertiary-fixed transition-colors">+7 812 409-22-11</a>
                </div>
                <div class="flex flex-col gap-0.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase tracking-wider text-on-primary-container/70">Direct:</span>
                        <span class="text-[9px] text-tertiary-fixed/80 font-normal">Encrypted SLA</span>
                    </div>
                    <a href="mailto:v.morozov@vostokpribor.ru" class="text-tertiary-fixed text-[11px] truncate block hover:underline hover:text-on-primary transition-colors" title="v.morozov@vostokpribor.ru">v.morozov@vostokpribor.ru</a>
                </div>
            </div>
        </div>
    </aside>
    <div id="portal-main-wrapper" class="portal-content-wrapper pl-64">
        <main class="w-full min-h-screen pt-16 bg-surface px-4 sm:px-6 lg:px-8 py-6">
            <div class="portal-container flex flex-col gap-6">
                <!-- Top Operational Breadcrumbs & Facility Scope -->
                <div class="flex items-center justify-between py-1">
                    <div
                        class="flex items-center gap-unit-xs font-technical-tag text-technical-tag text-on-surface-variant">
                        <span class="hover:text-primary cursor-pointer transition-colors">Enterprise Portal</span>
                        <span class="text-outline/40">/</span>
                        <span class="hover:text-primary cursor-pointer transition-colors">Industrial Solutions</span>
                        <span class="text-outline/40">/</span>
                        <span class="text-primary font-semibold">Projects &amp; Commissioning</span>
                    </div>
                    <div class="flex items-center gap-unit-md font-technical-tag text-technical-tag">
                        <div
                            class="flex items-center gap-1.5 px-unit-sm py-0.5 rounded bg-surface-container-high text-on-surface">
                            <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                            <span class="">SUPERVISORY TELEMETRY: <strong class="text-primary">NODE VP-704
                                    ACTIVE</strong></span>
                        </div>
                        <div class="text-on-surface-variant">SYNC: 14:02:18 UTC</div>
                    </div>
                </div>
                <!-- Header & Primary Controls Panel -->
                <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-unit-lg">
                    <div>
                        <div class="flex flex-wrap items-center gap-unit-sm mb-1">
                            <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Enterprise
                                Automation &amp; Equipment Projects</h1>
                            <span
                                class="px-2.5 py-0.5 rounded bg-secondary-fixed text-on-secondary-fixed-variant font-label-caps text-label-caps uppercase">
                                <?= (int)$activeCount ?> Total Active Engagements
                            </span>
                        </div>
                        <p class="font-body-md text-body-md text-on-surface-variant max-w-3xl">
                            Real-time telemetry oversight, milestone gating, compliance dossiers, and capitalized
                            project ledger for <?= htmlspecialchars($customer['company_name'] ?? 'Industrial Operations') ?> operational expansions.
                        </p>
                    </div>
                    <button
                        class="inline-flex items-center justify-center gap-unit-sm px-unit-base py-2 rounded bg-tertiary-fixed text-primary font-headline-sm text-headline-sm font-semibold hover:bg-tertiary-fixed-dim transition-colors shadow-sm shrink-0"
                        onclick="showRFQModal()"
                        type="button">
                        <span class="material-symbols-outlined text-lg">post_add</span>
                        <span class="">Request Project Scope Change / New RFQ</span>
                    </button>
                </div>
                <!-- Industrial Filter Toolbar -->
                <div
                    class="bg-surface-container-lowest rounded-xl p-unit-base shadow-sm flex flex-col lg:flex-row items-center justify-between gap-unit-md">
                    <div class="flex flex-1 w-full lg:w-auto items-center gap-unit-sm">
                        <div class="relative flex-1 max-w-md">
                            <span
                                class="material-symbols-outlined absolute left-3 top-2.5 text-outline text-lg">filter_alt</span>
                            <input
                                class="w-full bg-surface-container-low text-primary pl-9 pr-unit-base py-2 rounded text-body-sm font-body-sm placeholder:text-outline focus:bg-surface-container-lowest focus:outline-none"
                                id="projectFilterInput" onkeyup="filterProjects()"
                                placeholder="Filter by Project Code, Equipment Scope, or Director..." type="text">
                        </div>
                        <div class="flex items-center gap-unit-xs">
                            <div class="relative">
                                <select
                                    class="appearance-none bg-surface-container-low text-on-surface font-technical-tag text-technical-tag pl-3 pr-8 py-2 rounded focus:outline-none cursor-pointer"
                                    id="projectStageSelect" onchange="filterProjects()">
                                    <option value="ALL">STAGE: ALL PHASES</option>
                                    <option value="DESIGN">DESIGN</option>
                                    <option value="PROCUREMENT">PROCUREMENT</option>
                                    <option value="INTEGRATION">INTEGRATION</option>
                                    <option value="EXECUTION">EXECUTION</option>
                                    <option value="MAINTENANCE">MAINTENANCE</option>
                                </select>
                                <span
                                    class="material-symbols-outlined absolute right-2 top-2.5 text-xs text-outline pointer-events-none">expand_more</span>
                            </div>
                            <div class="relative">
                                <select
                                    class="appearance-none bg-surface-container-low text-on-surface font-technical-tag text-technical-tag pl-3 pr-8 py-2 rounded focus:outline-none cursor-pointer">
                                    <option>CALENDAR: FY 2024</option>
                                    <option>CALENDAR: FY 2023</option>
                                    <option>ALL TIMELINES</option>
                                </select>
                                <span
                                    class="material-symbols-outlined absolute right-2 top-2.5 text-xs text-outline pointer-events-none">expand_more</span>
                            </div>
                            <div class="relative">
                                <select
                                    class="appearance-none bg-surface-container-low text-on-surface font-technical-tag text-technical-tag pl-3 pr-8 py-2 rounded focus:outline-none cursor-pointer">
                                    <option selected="">FACILITY: <?= htmlspecialchars($customer['company_name'] ?? 'CHEREPOVETS PLANT #4') ?></option>
                                    <option>FACILITY: CHEREPOVETS SINTER PLANT</option>
                                    <option>FACILITY: KOLPINO SHEET MILL</option>
                                </select>
                                <span
                                    class="material-symbols-outlined absolute right-2 top-2.5 text-xs text-outline pointer-events-none">expand_more</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-unit-sm self-end lg:self-center">
                        <span class="font-technical-tag text-technical-tag text-outline">VIEWPORT: HIGH DENSITY</span>
                        <div class="flex items-center bg-surface-container-low p-0.5 rounded">
                            <button class="p-1 rounded bg-surface-container-lowest text-primary shadow-xs">
                                <span class="material-symbols-outlined text-base">table_rows</span>
                            </button>
                            <button class="p-1 rounded text-outline hover:text-primary">
                                <span class="material-symbols-outlined text-base">view_kanban</span>
                            </button>
                        </div>
                        <button
                            class="p-1.5 rounded bg-surface-container-low text-on-surface hover:bg-surface-container transition-colors"
                            onclick="exportProjectsCSV()"
                            title="Export Ledger as CSV">
                            <span class="material-symbols-outlined text-base">file_download</span>
                        </button>
                    </div>
                </div>
                <!-- Projects Master Data Grid Container -->
                <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden mb-unit-xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-surface-container-low text-on-surface-variant font-label-caps text-label-caps uppercase tracking-wider">
                                    <th class="py-3 px-unit-md w-36">Project ID</th>
                                    <th class="py-3 px-unit-md">Description &amp; Equipment Scope</th>
                                    <th class="py-3 px-unit-md w-32">Status Stage</th>
                                    <th class="py-3 px-unit-md text-right w-44">Allocated Budget</th>
                                    <th class="py-3 px-unit-md w-40">Progress</th>
                                    <th class="py-3 px-unit-md w-48">Project Manager</th>
                                    <th class="py-3 px-unit-md text-right w-28">Inspection</th>
                                </tr>
                            </thead>
                            <tbody id="projects-tbody" class="divide-y divide-surface-container-low">
                                <?php if (empty($projects)): ?>
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-on-surface-variant font-technical-tag">
                                            ( There's no Projects in the moment )
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($projects as $idx => $p): 
                                        $pId = htmlspecialchars($p['prj_id']);
                                        $pName = htmlspecialchars($p['project_name']);
                                        $pStatus = htmlspecialchars($p['status'] ?? 'Active');
                                        $pBudget = '$' . number_format((float)($p['budget'] ?? 0), 2);
                                        $pProgress = (int)($p['progress_percent'] ?? 50);
                                        $pManager = htmlspecialchars($p['project_manager_name'] ?? 'Dr. Elena Rostova');
                                        $pScope = htmlspecialchars($p['scope_summary'] ?? ($pName . ' - Automation, telemetry & sensor integration suite'));
                                        $pLocation = htmlspecialchars($p['facility_location'] ?? ($customer['company_name'] ?? 'Facility #4'));
                                        $pMilestones = cus_getProjectDetail($p['prj_id'], $cusId)['milestones'] ?? [];
                                        $isFirst = ($idx === 0);
                                    ?>
                                    <tr class="hover:bg-surface-bright/80 transition-colors group cursor-pointer" onclick="window.toggleProjectDrawer(this.querySelector('button'))">
                                        <td class="py-3.5 px-unit-md font-data-mono-md text-data-mono-md font-semibold text-primary">
                                            <div class="flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full <?= $pProgress >= 100 ? 'bg-secondary' : 'bg-tertiary-fixed-dim animate-pulse' ?>"></span>
                                                <span><?= $pId ?></span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-unit-md">
                                            <div class="font-headline-sm text-headline-sm text-primary font-medium"><?= $pName ?></div>
                                            <div class="font-technical-tag text-technical-tag text-on-surface-variant"><?= $pScope ?></div>
                                        </td>
                                        <td class="py-3.5 px-unit-md">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded <?= $pProgress >= 100 ? 'bg-secondary/20 text-secondary' : 'bg-tertiary-fixed/30 text-on-tertiary-container' ?> font-technical-tag text-technical-tag font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full <?= $pProgress >= 100 ? 'bg-secondary' : 'bg-tertiary-fixed-dim' ?>"></span>
                                                <?= $pStatus ?>
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-unit-md text-right">
                                            <div class="inline-flex items-center justify-end gap-1.5 font-data-mono-md text-data-mono-md font-semibold text-primary pl-2 project-card-highlight">
                                                <span><?= $pBudget ?></span>
                                                <span class="font-technical-tag text-technical-tag text-outline font-normal">USD</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-unit-md">
                                            <div class="flex items-center justify-between font-data-mono-md text-data-mono-md text-primary mb-1">
                                                <span class="font-semibold"><?= $pProgress ?>%</span>
                                                <span class="font-technical-tag text-technical-tag text-outline"><?= $pProgress >= 100 ? 'Complete' : 'In Flight' ?></span>
                                            </div>
                                            <div class="w-full h-1.5 bg-surface-container-high rounded-full overflow-hidden">
                                                <div class="h-full <?= $pProgress >= 100 ? 'bg-secondary' : 'bg-tertiary-fixed-dim' ?> rounded-full" style="width: <?= $pProgress ?>%"></div>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-unit-md">
                                            <div class="flex items-center gap-unit-sm">
                                                <div class="w-7 h-7 rounded-full bg-primary text-tertiary-fixed text-xs font-bold flex items-center justify-center border border-tertiary-fixed/40">
                                                    <?= htmlspecialchars(strtoupper(substr($pManager, 0, 2))) ?>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="font-body-sm text-body-sm font-medium text-primary leading-tight"><?= $pManager ?></span>
                                                    <span class="font-technical-tag text-technical-tag text-outline">Lead Systems Eng.</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-unit-md text-right">
                                            <button class="inline-flex items-center gap-0.5 font-technical-tag text-technical-tag px-2 py-1 rounded <?= $isFirst ? 'bg-primary text-on-primary font-medium' : 'bg-surface-container-low text-primary hover:bg-surface-container' ?> transition-colors" onclick="event.stopPropagation(); window.toggleProjectDrawer(this)">
                                                <span><?= $isFirst ? 'COLLAPSE' : 'VIEW' ?></span>
                                                <span class="material-symbols-outlined text-sm"><?= $isFirst ? 'keyboard_arrow_up' : 'keyboard_arrow_down' ?></span>
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- DRAWER FOR <?= $pId ?> -->
                                    <tr class="bg-surface-container-low/60" style="display: <?= $isFirst ? 'table-row' : 'none' ?>;">
                                        <td class="p-unit-lg" colspan="7">
                                            <div class="bg-surface-container-lowest rounded-xl p-unit-lg shadow-md flex flex-col gap-unit-lg">
                                                <div class="flex flex-col lg:flex-row lg:items-center justify-between pb-unit-md gap-unit-base bg-surface-container-low/40 p-unit-md rounded-lg">
                                                    <div class="flex items-start gap-unit-md">
                                                        <div class="p-2.5 rounded bg-primary text-tertiary-fixed shrink-0">
                                                            <span class="material-symbols-outlined text-2xl">precision_manufacturing</span>
                                                        </div>
                                                        <div>
                                                            <div class="flex items-center gap-unit-sm">
                                                                <span class="font-label-caps text-label-caps uppercase text-outline">Execution Blueprint</span>
                                                                <span class="font-data-mono-md text-data-mono-md font-semibold text-primary bg-surface-container px-2 py-0.5 rounded"><?= $pId ?></span>
                                                                <span class="px-2 py-0.5 rounded bg-tertiary-fixed/30 text-on-tertiary-container font-technical-tag text-technical-tag font-semibold">STAGE: <?= strtoupper($pStatus) ?></span>
                                                            </div>
                                                            <h2 class="font-headline-md text-headline-md text-primary mt-0.5"><?= $pName ?></h2>
                                                        </div>
                                                    </div>
                                                    <div class="flex flex-wrap items-center gap-unit-lg">
                                                        <div class="flex flex-col">
                                                            <span class="font-label-caps text-label-caps uppercase text-outline">Facility Scope</span>
                                                            <span class="font-data-mono-md text-data-mono-md font-semibold text-primary"><?= $pLocation ?></span>
                                                        </div>
                                                        <div class="h-8 w-px bg-outline-variant/50 hidden sm:block"></div>
                                                        <div class="flex items-center gap-unit-xs">
                                                            <button class="px-unit-sm py-1.5 rounded bg-surface-container text-primary font-technical-tag text-technical-tag font-medium hover:bg-surface-container-high transition-colors"
                                                                onclick="window.showToast('Telemetry Link Active', 'SCADA feed polling <?= $pId ?> telemetry sensors at 250ms interval.', 'info')" type="button">
                                                                Telemetry Log
                                                            </button>
                                                            <button class="px-unit-sm py-1.5 rounded bg-primary text-on-primary font-technical-tag text-technical-tag font-medium hover:bg-primary-container transition-colors"
                                                                onclick="window.location.href='SupportTicketView.php?project=<?= urlencode($pId) ?>'" type="button">
                                                                Field Ops Dispatch
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Milestones / Billing Gates -->
                                                <div>
                                                    <h3 class="font-headline-sm text-sm font-bold text-primary uppercase tracking-wider mb-2">Gate Milestones &amp; Scheduled Phases</h3>
                                                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-unit-sm">
                                                        <?php if (empty($pMilestones)): ?>
                                                            <div class="p-unit-sm rounded bg-surface-container-low text-xs text-on-surface-variant col-span-full">
                                                                Continuous execution schedule active. Real-time telemetry monitored via SCADA gateway.
                                                            </div>
                                                        <?php else: ?>
                                                            <?php foreach ($pMilestones as $mIdx => $m): ?>
                                                                <div class="p-unit-sm rounded border <?= !empty($m['invoiced']) ? 'border-secondary/40 bg-surface-container-lowest' : 'border-outline/20 bg-surface-container-low/40' ?> flex flex-col justify-between">
                                                                    <div>
                                                                        <div class="flex items-center justify-between mb-1">
                                                                            <span class="font-mono text-[10px] text-outline">GATE #<?= $mIdx + 1 ?></span>
                                                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold font-mono <?= !empty($m['invoiced']) ? 'bg-secondary/20 text-secondary' : 'bg-outline/20 text-on-surface-variant' ?>">
                                                                                <?= !empty($m['invoiced']) ? 'COMPLETED' : 'PENDING' ?>
                                                                            </span>
                                                                        </div>
                                                                        <div class="text-xs font-semibold text-primary"><?= htmlspecialchars($m['milestone_description']) ?></div>
                                                                    </div>
                                                                    <div class="mt-2 pt-1 border-t border-outline/10 flex items-center justify-between text-[11px] font-mono text-outline">
                                                                        <span>Target: <?= htmlspecialchars($m['scheduled_date']) ?></span>
                                                                        <?php if ((float)$m['milestone_amount'] > 0): ?>
                                                                            <span class="text-primary font-semibold">$<?= number_format((float)$m['milestone_amount'], 2) ?></span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <!-- Linked Technical Documents -->
                                                <div class="flex flex-col sm:flex-row items-center justify-between pt-unit-sm border-t border-outline/20 text-xs text-on-surface-variant">
                                                    <div class="flex items-center gap-2">
                                                        <span class="material-symbols-outlined text-base text-primary">description</span>
                                                        <span>Project Specification &amp; QA Blueprint: <strong>SPEC-<?= $pId ?>-R3.PDF</strong></span>
                                                    </div>
                                                    <div class="flex items-center gap-2 mt-2 sm:mt-0">
                                                        <button class="px-3 py-1.5 rounded bg-surface-container hover:bg-surface-container-high text-primary font-medium text-xs flex items-center gap-1"
                                                            onclick="window.previewDocument('SPEC-<?= $pId ?>.pdf', '<?= $pName ?> - Blueprint Dossier', 'SIGNED QA')">
                                                            <span class="material-symbols-outlined text-sm">download</span> Download PDF Spec
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Data Grid Footer / Pagination Metadata -->
                    <div
                        class="px-unit-lg py-3 bg-surface-container-low/50 flex flex-col sm:flex-row items-center justify-between gap-unit-sm font-technical-tag text-technical-tag text-on-surface-variant">
                        <div class="flex items-center gap-unit-md">
                            <span class="">SHOWING <?= count($projects) ?> COMMISSIONS</span>
                            <span class="">•</span>
                            <span class="text-primary font-semibold">TOTAL CAPITALIZATION: $<?= number_format((float)$totalBudget, 2) ?> USD</span>
                        </div>
                        <div class="flex items-center gap-unit-xs">
                            <button
                                class="px-2 py-1 rounded bg-surface-container text-outline hover:text-primary transition-colors disabled:opacity-40"
                                disabled="">
                                PREV
                            </button>
                            <span class="px-2 py-1 rounded bg-primary text-on-primary font-bold">1</span>
                            <button
                                class="px-2 py-1 rounded bg-surface-container hover:bg-surface-container-high text-primary transition-colors">2</button>
                            <button
                                class="px-2 py-1 rounded bg-surface-container hover:bg-surface-container-high text-primary transition-colors">3</button>
                            <button
                                class="px-2 py-1 rounded bg-surface-container text-primary hover:bg-surface-container-high transition-colors">
                                NEXT
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Operational Health & Support Strip -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-unit-md">
                    <div
                        class="bg-surface-container-lowest p-unit-base rounded-xl shadow-sm flex items-center gap-unit-md">
                        <div class="p-3 rounded-lg bg-surface-container-low text-primary shrink-0">
                            <span class="material-symbols-outlined text-2xl">sensors</span>
                        </div>
                        <div>
                            <div class="font-label-caps text-label-caps uppercase text-outline">SCADA Link Integrity
                            </div>
                            <div class="font-headline-sm text-headline-sm text-primary font-semibold">99.98% Telemetry
                                Uptime</div>
                            <div class="font-technical-tag text-technical-tag text-on-surface-variant mt-0.5">Polling
                                interval: 250ms via VP-Gateway</div>
                        </div>
                    </div>
                    <div
                        class="bg-surface-container-lowest p-unit-base rounded-xl shadow-sm flex items-center gap-unit-md">
                        <div class="p-3 rounded-lg bg-surface-container-low text-primary shrink-0">
                            <span class="material-symbols-outlined text-2xl">verified</span>
                        </div>
                        <div>
                            <div class="font-label-caps text-label-caps uppercase text-outline">Quality &amp; Standards
                            </div>
                            <div class="font-headline-sm text-headline-sm text-primary font-semibold">ISO 9001 / GOST R
                                Valid</div>
                            <div class="font-technical-tag text-technical-tag text-on-surface-variant mt-0.5">
                                Recertification Audit: March 2025</div>
                        </div>
                    </div>
                    <div
                        class="bg-surface-container-lowest p-unit-base rounded-xl shadow-sm flex items-center gap-unit-md">
                        <div class="p-3 rounded-lg bg-surface-container-low text-primary shrink-0">
                            <span class="material-symbols-outlined text-2xl">support_agent</span>
                        </div>
                        <div>
                            <div class="font-label-caps text-label-caps uppercase text-outline">Industrial Helpdesk
                            </div>
                            <div class="font-headline-sm text-headline-sm text-primary font-semibold">Dedicated Field
                                Response</div>
                            <div class="font-technical-tag text-technical-tag text-secondary mt-0.5">1-hr priority SLA
                                on active execution lines</div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

</body>

</html>