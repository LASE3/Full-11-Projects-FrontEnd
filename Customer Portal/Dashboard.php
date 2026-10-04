<?php
require_once __DIR__ . '/customer_context.php';

// Financial events from MariaDB
$finStmt = $pdo->prepare("SELECT i.inv_id, i.total_value, i.currency, i.payment_status, i.issued_at, p.project_name FROM invoices i LEFT JOIN projects p ON i.prj_id = p.prj_id WHERE " . ($isSuperAdmin ? "1=1" : "i.cus_id = :cid") . " ORDER BY i.issued_at DESC, i.inv_id DESC LIMIT 4");
if (!$isSuperAdmin) { $finStmt->bindValue(':cid', $cusId); }
$finStmt->execute();
$finEvents = $finStmt->fetchAll(PDO::FETCH_ASSOC);

// Project events from MariaDB
$prjFeedStmt = $pdo->prepare("SELECT p.prj_id, p.project_name, p.status, p.budget, p.currency, p.start_date FROM projects p WHERE " . ($isSuperAdmin ? "1=1" : "p.cus_id = :cid") . " ORDER BY p.prj_id DESC LIMIT 4");
if (!$isSuperAdmin) { $prjFeedStmt->bindValue(':cid', $cusId); }
$prjFeedStmt->execute();
$prjEvents = $prjFeedStmt->fetchAll(PDO::FETCH_ASSOC);

// Field service events from MariaDB
$tktFeedStmt = $pdo->prepare("SELECT t.tkt_id, t.title, t.priority, t.status, t.created_at, e.full_name AS tech_name FROM tickets t LEFT JOIN employees e ON t.assigned_emp_id = e.emp_id WHERE " . ($isSuperAdmin ? "1=1" : "t.requester_cus_id = :cid") . " ORDER BY t.created_at DESC LIMIT 4");
if (!$isSuperAdmin) { $tktFeedStmt->bindValue(':cid', $cusId); }
$tktFeedStmt->execute();
$tktEvents = $tktFeedStmt->fetchAll(PDO::FETCH_ASSOC);

$totalFeedEvents = count($finEvents) + count($prjEvents) + count($tktEvents);
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="<?= htmlspecialchars(getCsrfToken()) ?>" />
    <title>VOSTOKPRIBOR Portal</title>
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@400;500;600&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="css/common.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/tailwind-config.js"></script>
    <script src="js/portal.js"></script>
    <link rel="stylesheet" href="../assets/css/api-ui.css">
    <script src="../assets/js/api-core.js"></script>
    <script src="../assets/js/api-customer.js"></script>
    <script src="js/portal-data.js"></script>
    <script src="js/dashboard.js"></script>
</head>

<body class="bg-background font-body-md text-body-md text-on-background">
    <header
        class="fixed top-0 left-0 right-0 h-16 bg-primary-container z-50 flex items-center justify-between px-unit-lg border-b border-outline/20">
        <div class="flex items-center gap-unit-base">
            <button id="sidebar-toggle-btn" class="p-1.5 -ml-1 mr-1 rounded text-on-primary-container hover:text-on-primary hover:bg-surface-container-high/10 transition-colors flex items-center justify-center cursor-pointer focus:outline-none" title="Toggle Navigation Menu (Ctrl+B)">
                <span class="material-symbols-outlined text-2xl" id="sidebar-toggle-icon">menu</span>
            </button>
            <img alt="VOSTOKPRIBOR Logo" class="h-8 w-auto object-contain cursor-pointer" onclick="location.href='Dashboard.php'"
                src="assets/logo.svg" />
            <div class="h-6 w-px bg-outline/30"></div>
            <div class="flex flex-col">
                <div class="flex items-center gap-unit-xs"><span
                        class="font-headline-sm text-headline-sm text-on-primary font-semibold tracking-tight cursor-pointer" onclick="location.href='Dashboard.php'">VOSTOKPRIBOR</span><span
                        class="px-unit-xs py-0.5 rounded bg-surface-container-high/10 text-tertiary-fixed font-technical-tag text-technical-tag border border-tertiary-fixed/30">PORTAL</span>
                </div>
                <div
                    class="flex items-center gap-unit-xs text-on-primary-container font-technical-tag text-technical-tag">
                    <span class="truncate max-w-[200px]"><?= htmlspecialchars($customer['company_name'] ?? 'Authorized Client') ?></span><span class="text-outline">|</span><span
                        class="text-primary-fixed-dim font-mono"><?= htmlspecialchars($customer['tax_id'] ?? 'VP-88204-EU') ?></span>
                </div>
            </div>
        </div>
        <div class="w-64 md:w-80 lg:w-96 max-w-md mx-2 shrink-1">
            <div
                class="header-search-bar flex items-center bg-primary px-unit-md py-1.5 rounded border border-outline/30 text-on-primary-container cursor-pointer transition-colors hover:border-outline/50">
                <span class="material-symbols-outlined text-sm mr-unit-sm text-on-primary-container">search</span><input
                    class="header-search-input bg-transparent border-none outline-none font-body-sm text-body-sm text-on-primary placeholder:text-on-primary-container w-full cursor-pointer"
                    placeholder="Search projects, serial numbers, specs..." type="text" /><span
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
                        class="font-technical-tag text-technical-tag text-on-primary-container mt-0.5"><?= htmlspecialchars($currUser['role_name'] ?? ($currUser['clearance_level'] ?? 'L2')) ?></span></div>
                <div class="w-8 h-8 rounded-full bg-tertiary-fixed/20 text-tertiary-fixed border border-tertiary-fixed/50 flex items-center justify-center font-bold text-xs">
                    <?= htmlspecialchars(mb_substr($currUser['full_name'] ?? 'VP', 0, 2)) ?>
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
                <a aria-current="page"
                    class="flex items-center gap-unit-sm px-unit-base py-unit-sm transition-colors bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-on-tertiary-container font-headline-sm text-headline-sm"
                    data-path="dashboard" href="Dashboard.php"><svg class="w-4 h-4 shrink-0 text-tertiary-fixed" fill="none" stroke="currentColor"
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
                    </svg><span class="">Orders</span></a><a
                    class="flex items-center gap-unit-sm px-unit-base py-unit-sm text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary transition-colors font-headline-sm text-headline-sm font-normal"
                    data-path="projects" href="ProjectListAndDetail.php"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
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
                <section class="flex flex-col xl:flex-row xl:items-end justify-between gap-4 pb-1">
                    <div class="flex flex-col gap-unit-xs">
                        <nav
                            class="flex items-center gap-unit-xs font-technical-tag text-technical-tag text-on-surface-variant uppercase tracking-wider">
                            <span>Enterprise Portal</span>
                            <span class="text-outline-variant">/</span>
                            <span>Client Space</span>
                            <span class="text-outline-variant">/</span>
                            <span class="text-primary font-semibold">Dashboard Overview</span>
                        </nav>
                        <div class="flex flex-wrap items-baseline gap-x-unit-md gap-y-unit-xs">
                            <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight font-bold">Welcome
                                back, <?= htmlspecialchars($currUser['full_name'] ?? 'Client Representative') ?></h1>
                            <span
                                class="font-technical-tag text-technical-tag bg-surface-container-high text-primary px-unit-xs py-0.5 rounded">SYS-AUTH
                                // <?= htmlspecialchars($currUser['clearance_level'] ?? 'L2') ?></span>
                        </div>
                        <p class="font-body-sm text-body-sm text-on-surface-variant max-w-4xl">
                            Client Account <span
                                class="font-data-mono-md text-data-mono-md text-on-surface font-medium">#<?= htmlspecialchars($cusId) ?></span>
                            • Facility: <span class="font-medium text-on-surface"><?= htmlspecialchars($customer['company_name'] ?? 'Industrial Operations') ?></span> • Primary Contact: <span class="font-medium text-on-surface"><?= htmlspecialchars($customer['primary_contact_name'] ?? 'Engineering Team') ?></span> (<a
                                class="text-secondary hover:underline"
                                href="mailto:<?= htmlspecialchars($customer['primary_contact_email'] ?? 'direct.contact@vostokpribor.com') ?>"><?= htmlspecialchars($customer['primary_contact_email'] ?? 'direct.contact@vostokpribor.com') ?></a>)
                        </p>
                    </div>
                    <div class="flex items-center gap-unit-sm shrink-0">
                        <button
                            class="inline-flex items-center gap-unit-xs px-unit-md py-2 bg-surface-container-lowest text-primary text-body-sm font-body-sm font-medium rounded shadow-sm hover:bg-surface-container transition-colors cursor-pointer"
                            onclick="window.showToast('Telemetry PDF Compiled', 'Facility operational dossier exported successfully.', 'success')"
                            type="button">
                            <span class="material-symbols-outlined text-lg text-secondary">file_download</span>
                            <span>Export Telemetry PDF</span>
                        </button>
                        <button
                            class="inline-flex items-center gap-unit-xs px-unit-md py-2 bg-tertiary-fixed text-primary-container text-body-sm font-body-sm font-semibold rounded shadow-sm hover:bg-tertiary-fixed-dim transition-colors cursor-pointer"
                            onclick="window.showDispatchModal()"
                            type="button">
                            <span class="material-symbols-outlined text-lg text-primary-container">local_shipping</span>
                            <span>Request Urgent Equipment Dispatch</span>
                        </button>
                    </div>
                </section>
                <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-unit-base">
                    <a href="ProjectListAndDetail.php"
                        class="flex flex-col justify-between p-unit-base bg-surface-container-lowest rounded-xl shadow-sm relative overflow-hidden group hover:-translate-y-0.5 hover:shadow-md transition-all">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-secondary group-hover:h-1.5 transition-all"></div>
                        <div class="flex items-start justify-between">
                            <div class="flex flex-col">
                                <span
                                    class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Active
                                    Projects</span>
                                <span
                                    class="font-display-lg text-display-lg text-primary font-bold tracking-tight mt-unit-xs" id="kpi-active-projects"><?= $badgeProjects ?></span>
                            </div>
                            <div class="p-2 rounded bg-primary-container text-inverse-primary group-hover:scale-105 transition-transform">
                                <span class="material-symbols-outlined text-xl">precision_manufacturing</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-unit-md mt-unit-sm">
                            <span class="font-body-sm text-body-sm text-on-surface-variant" id="kpi-active-projects-sub"><?= $badgeProjects ?> engineering scopes active</span>
                            <span
                                class="inline-flex items-center gap-1 font-technical-tag text-technical-tag font-semibold text-primary px-1.5 py-0.5 rounded bg-surface-container-high">
                                <span class="material-symbols-outlined text-xs">trending_up</span>Live
                            </span>
                        </div>
                    </a>
                    <a href="Invoices.php"
                        class="flex flex-col justify-between p-unit-base bg-surface-container-lowest rounded-xl shadow-sm relative overflow-hidden group hover:-translate-y-0.5 hover:shadow-md transition-all">
                        <div class="absolute top-0 bottom-0 left-0 w-1 bg-tertiary-fixed-dim group-hover:w-1.5 transition-all"></div>
                        <div class="flex items-start justify-between pl-unit-xs">
                            <div class="flex flex-col">
                                <span
                                    class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Total
                                    Invoices</span>
                                <span
                                    class="font-display-lg text-display-lg text-primary font-bold tracking-tight mt-unit-xs" id="kpi-open-invoices"><?= $badgeInvoices ?></span>
                            </div>
                            <div class="p-2 rounded bg-tertiary-container text-tertiary-fixed group-hover:scale-105 transition-transform">
                                <span class="material-symbols-outlined text-xl">payments</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-unit-md mt-unit-sm pl-unit-xs">
                            <span
                                class="font-data-mono-md text-data-mono-md text-on-surface-variant font-medium" id="kpi-open-invoices-sub"><?= $badgeInvoices ?> commercial invoices</span>
                            <span
                                class="font-technical-tag text-technical-tag font-semibold text-on-tertiary-fixed-variant bg-tertiary-fixed/30 px-1.5 py-0.5 rounded">
                                Financial
                            </span>
                        </div>
                    </a>
                    <a href="SupportTicketView.php"
                        class="flex flex-col justify-between p-unit-base bg-surface-container-lowest rounded-xl shadow-sm relative overflow-hidden group hover:-translate-y-0.5 hover:shadow-md transition-all">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-error group-hover:h-1.5 transition-all"></div>
                        <div class="flex items-start justify-between">
                            <div class="flex flex-col">
                                <span
                                    class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Support
                                    Tickets</span>
                                <span
                                    class="font-display-lg text-display-lg text-primary font-bold tracking-tight mt-unit-xs" id="kpi-open-tickets"><?= $badgeTickets ?></span>
                            </div>
                            <div class="p-2 rounded bg-surface-container text-on-surface group-hover:scale-105 transition-transform">
                                <span class="material-symbols-outlined text-xl">headset_mic</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-unit-md mt-unit-sm">
                            <span class="font-body-sm text-body-sm text-on-surface-variant" id="kpi-open-tickets-sub"><?= $badgeTickets ?> logged in registry</span>
                            <span
                                class="inline-flex items-center gap-1.5 font-technical-tag text-technical-tag font-bold text-on-error bg-error px-2 py-0.5 rounded">
                                Active SLA
                            </span>
                        </div>
                    </a>
                    <a href="Documents.php"
                        class="flex flex-col justify-between p-unit-base bg-surface-container-lowest rounded-xl shadow-sm relative overflow-hidden group hover:-translate-y-0.5 hover:shadow-md transition-all">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-secondary group-hover:h-1.5 transition-all"></div>
                        <div class="flex items-start justify-between">
                            <div class="flex flex-col">
                                <span
                                    class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Technical
                                    Documents</span>
                                <span
                                    class="font-display-lg text-display-lg text-primary font-bold tracking-tight mt-unit-xs" id="kpi-approved-docs"><?= $badgeDocs ?></span>
                            </div>
                            <div class="p-2 rounded bg-surface-container text-secondary group-hover:scale-105 transition-transform">
                                <span class="material-symbols-outlined text-xl">verified</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-unit-md mt-unit-sm">
                            <span class="font-body-sm text-body-sm text-on-surface-variant" id="kpi-approved-docs-sub"><?= $badgeDocs ?> repository specs</span>
                            <span
                                class="font-technical-tag text-technical-tag font-medium text-secondary bg-secondary-fixed/50 px-1.5 py-0.5 rounded">
                                Verified
                            </span>
                        </div>
                    </a>
                </section>
                <section class="grid grid-cols-1 lg:grid-cols-12 gap-unit-base items-start">
                    <div
                        class="lg:col-span-8 flex flex-col bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden">
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between p-unit-base bg-surface-container-low gap-unit-sm">
                            <div class="flex items-center gap-unit-sm">
                                <span class="material-symbols-outlined text-xl text-secondary">dynamic_feed</span>
                                <h2 class="font-headline-sm text-headline-sm text-primary font-semibold">Recent Portal
                                    &amp; Facility Activity</h2>
                            </div>
                            <div class="flex items-center gap-1 bg-surface-container-highest p-1 rounded" id="dash-activity-filters">
                                <button
                                    class="dash-filter-btn px-2.5 py-1 text-technical-tag font-technical-tag rounded font-semibold bg-surface-container-lowest text-primary shadow-xs transition-colors cursor-pointer"
                                    data-filter="all" type="button">ALL EVENTS</button>
                                <button
                                    class="dash-filter-btn px-2.5 py-1 text-technical-tag font-technical-tag rounded font-medium text-on-surface-variant hover:text-primary transition-colors cursor-pointer"
                                    data-filter="finance" type="button">FINANCIAL</button>
                                <button
                                    class="dash-filter-btn px-2.5 py-1 text-technical-tag font-technical-tag rounded font-medium text-on-surface-variant hover:text-primary transition-colors cursor-pointer"
                                    data-filter="project" type="button">PROJECTS</button>
                                <button
                                    class="dash-filter-btn px-2.5 py-1 text-technical-tag font-technical-tag rounded font-medium text-on-surface-variant hover:text-primary transition-colors cursor-pointer"
                                    data-filter="field" type="button">FIELD SERVICE</button>
                            </div>
                        </div>
                        <div class="flex flex-col" id="dash-portal-activity-feed">
                            <?php if (empty($finEvents) && empty($prjEvents) && empty($tktEvents)): ?>
                                <div id="dash-portal-empty-feed" class="p-8 text-center text-on-surface-variant font-mono text-sm">
                                    No activity events recorded at the moment.
                                </div>
                            <?php else: ?>
                                <?php foreach ($finEvents as $fe): ?>
                                    <div class="feed-item flex flex-col md:flex-row md:items-center justify-between p-unit-base gap-unit-sm border-b border-surface-container-low hover:bg-surface-container-low transition-colors relative pl-unit-lg cursor-pointer"
                                         data-category="finance"
                                         onclick="window.location.href='Invoices.php?invoice=<?= urlencode($fe['inv_id']) ?>'">
                                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-tertiary-fixed-dim"></div>
                                        <div class="flex flex-col gap-0.5 pr-unit-md">
                                            <div class="flex items-center gap-unit-xs">
                                                <span class="font-label-caps text-label-caps text-secondary font-bold uppercase tracking-wider">Commercial Invoice</span>
                                                <span class="font-technical-tag text-technical-tag text-on-surface-variant">#<?= htmlspecialchars($fe['inv_id']) ?></span>
                                            </div>
                                            <p class="font-body-md text-body-md text-on-surface font-medium">
                                                <?= htmlspecialchars($fe['project_name'] ?? 'Equipment Procurement') ?>: <?= htmlspecialchars($fe['payment_status']) ?> ($<?= number_format((float)$fe['total_value'], 2) ?> <?= htmlspecialchars($fe['currency'] ?? 'USD') ?>).
                                            </p>
                                            <span class="font-data-mono-md text-data-mono-md text-on-surface-variant"><?= htmlspecialchars($fe['issued_at']) ?> • Financial Dept</span>
                                        </div>
                                        <div class="flex items-center gap-unit-xs shrink-0 self-end md:self-center">
                                            <a class="px-unit-sm py-1 rounded bg-tertiary-fixed text-primary-container font-technical-tag text-technical-tag font-semibold hover:bg-tertiary-fixed-dim"
                                               href="Invoices.php?invoice=<?= urlencode($fe['inv_id']) ?>">Review Invoice</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <?php foreach ($prjEvents as $pe): ?>
                                    <div class="feed-item flex flex-col md:flex-row md:items-center justify-between p-unit-base gap-unit-sm bg-surface-container-lowest hover:bg-surface-container-low transition-colors relative pl-unit-lg cursor-pointer border-b border-surface-container-low"
                                         data-category="project"
                                         onclick="window.location.href='ProjectListAndDetail.php?project=<?= urlencode($pe['prj_id']) ?>'">
                                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-primary-container"></div>
                                        <div class="flex flex-col gap-0.5 pr-unit-md">
                                            <div class="flex items-center gap-unit-xs">
                                                <span class="font-label-caps text-label-caps text-primary-container font-bold uppercase tracking-wider">Project Milestone</span>
                                                <span class="font-technical-tag text-technical-tag text-on-surface-variant"><?= htmlspecialchars($pe['prj_id']) ?></span>
                                            </div>
                                            <p class="font-body-md text-body-md text-on-surface font-medium">
                                                <?= htmlspecialchars($pe['project_name']) ?> (Status: <?= htmlspecialchars($pe['status']) ?>, Budget: $<?= number_format((float)$pe['budget'], 2) ?>).
                                            </p>
                                            <span class="font-data-mono-md text-data-mono-md text-on-surface-variant">Started <?= htmlspecialchars($pe['start_date']) ?> • Engineering Bureau</span>
                                        </div>
                                        <div class="flex items-center gap-unit-xs shrink-0 self-end md:self-center">
                                            <a href="ProjectListAndDetail.php?project=<?= urlencode($pe['prj_id']) ?>"
                                               class="inline-flex items-center gap-1 font-technical-tag text-technical-tag text-secondary bg-secondary-fixed/30 px-unit-sm py-1 rounded font-medium">
                                                <span class="material-symbols-outlined text-xs">check_circle</span> <?= htmlspecialchars($pe['status']) ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <?php foreach ($tktEvents as $te): ?>
                                    <div class="feed-item flex flex-col md:flex-row md:items-center justify-between p-unit-base gap-unit-sm bg-error-container/20 hover:bg-error-container/30 transition-colors relative pl-unit-lg cursor-pointer border-b border-surface-container-low"
                                         data-category="field"
                                         onclick="window.location.href='SupportTicketView.php?ticket=<?= urlencode($te['tkt_id']) ?>'">
                                        <div class="absolute left-0 top-0 bottom-0 w-1.5 <?= $te['priority'] === 'Critical' ? 'bg-error' : 'bg-secondary' ?>"></div>
                                        <div class="flex flex-col gap-0.5 pr-unit-md">
                                            <div class="flex items-center gap-unit-xs">
                                                <span class="font-label-caps text-label-caps text-error font-bold uppercase tracking-wider">Field Incident #<?= htmlspecialchars($te['tkt_id']) ?></span>
                                                <span class="font-technical-tag text-technical-tag bg-error text-on-error px-1.5 rounded font-bold"><?= htmlspecialchars(strtoupper($te['priority'])) ?></span>
                                            </div>
                                            <p class="font-body-md text-body-md text-primary font-medium">
                                                <?= htmlspecialchars($te['title']) ?> (Status: <?= htmlspecialchars($te['status']) ?>)
                                            </p>
                                            <span class="font-data-mono-md text-data-mono-md text-on-surface-variant">Created <?= htmlspecialchars($te['created_at']) ?> • Assigned: <?= htmlspecialchars($te['tech_name'] ?? 'Field Pool') ?></span>
                                        </div>
                                        <div class="flex items-center gap-unit-xs shrink-0 self-end md:self-center">
                                            <a class="px-unit-sm py-1 rounded bg-error text-on-error font-technical-tag text-technical-tag font-semibold"
                                               href="SupportTicketView.php?ticket=<?= urlencode($te['tkt_id']) ?>">Track Dispatch</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <div id="dash-portal-empty-filter" style="display: none;" class="p-8 text-center text-on-surface-variant font-mono text-sm">
                                    No records found in this category.
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-unit-sm bg-surface-container-low flex justify-between items-center px-unit-base">
                            <span class="font-technical-tag text-technical-tag text-on-surface-variant" id="dash-portal-events-count">Showing <?= $totalFeedEvents ?> recent system events</span>
                            <button
                                class="text-secondary hover:text-primary font-technical-tag text-technical-tag font-bold inline-flex items-center gap-1 cursor-pointer"
                                onclick="window.location.href='Documents.php'"
                                type="button">
                                <span>VIEW FULL AUDIT LOG</span>
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </button>
                        </div>
                    </div>
                    <div class="lg:col-span-4 flex flex-col gap-unit-base">
                        <div class="flex flex-col bg-surface-container-lowest rounded-xl shadow-sm p-unit-base">
                            <div class="flex items-center justify-between pb-unit-sm">
                                <div class="flex items-center gap-unit-xs">
                                    <span class="material-symbols-outlined text-lg text-primary">sensors</span>
                                    <h3 class="font-headline-sm text-headline-sm text-primary font-semibold">Telemetry
                                        &amp; Node Health</h3>
                                </div>
                                <span
                                    class="inline-flex items-center gap-1 text-technical-tag font-technical-tag font-bold text-secondary bg-secondary-fixed/40 px-1.5 py-0.5 rounded">
                                    <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span> 100% ONLINE
                                </span>
                            </div>
                            <div class="flex flex-col gap-unit-sm my-unit-xs">
                                <div class="p-unit-sm rounded bg-surface-container-low flex flex-col gap-1">
                                    <div class="flex justify-between items-center text-body-sm font-body-sm">
                                        <span class="text-on-surface-variant">Field Operational Nodes</span>
                                        <span class="font-data-mono-md text-data-mono-md font-bold text-primary">42 / 42
                                            Active</span>
                                    </div>
                                    <div class="w-full bg-surface-container-highest h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-secondary h-full rounded-full w-full"></div>
                                    </div>
                                </div>
                                <div
                                    class="p-unit-sm rounded bg-surface-container-low flex items-center justify-between">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-label-caps text-label-caps text-on-surface-variant uppercase">SCADA
                                            Gateway VP-GW-09</span>
                                        <span
                                            class="font-data-mono-md text-data-mono-md text-primary font-semibold">Cherepovets
                                            Unit #2</span>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="font-technical-tag text-technical-tag font-bold text-secondary">LATENCY
                                            4ms</span>
                                        <div class="font-data-mono-md text-data-mono-md text-on-surface-variant">Modbus
                                            TCP/IP</div>
                                    </div>
                                </div>
                                <div
                                    class="p-unit-sm rounded bg-surface-container-low flex items-center justify-between">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-label-caps text-label-caps text-on-surface-variant uppercase">Scheduled
                                            Calibration Audit</span>
                                        <span class="font-body-md text-body-md text-primary font-semibold">Nov 14,
                                            2024</span>
                                    </div>
                                    <span
                                        class="font-technical-tag text-technical-tag font-bold text-on-tertiary-fixed-variant bg-tertiary-fixed/40 px-2 py-1 rounded">
                                        IN 18 DAYS
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-unit-xs pt-unit-sm">
                                <button
                                    class="flex-1 py-1.5 text-center bg-surface-container hover:bg-surface-container-high rounded text-primary font-technical-tag text-technical-tag font-semibold transition-colors cursor-pointer"
                                    onclick="window.showToast('Ledger Exported', 'Facility CSV ledger downloaded.', 'success')"
                                    type="button">
                                    DOWNLOAD LEDGER (.CSV)
                                </button>
                                <button
                                    class="flex-1 py-1.5 text-center bg-surface-container hover:bg-surface-container-high rounded text-primary font-technical-tag text-technical-tag font-semibold transition-colors cursor-pointer"
                                    onclick="window.exportTelemetryPDF ? window.exportTelemetryPDF() : window.showToast('PDF Exported', 'Facility report compiled.', 'success')"
                                    type="button">
                                    PDF REPORT
                                </button>
                            </div>
                        </div>
                        <div class="flex flex-col bg-surface-container-lowest rounded-xl shadow-sm p-unit-base">
                            <div class="flex items-center justify-between pb-unit-sm">
                                <h3 class="font-headline-sm text-headline-sm text-primary font-semibold">Dedicated
                                    Engineering Contact</h3>
                                <span
                                    class="font-technical-tag text-technical-tag font-bold text-on-tertiary-fixed-variant bg-tertiary-fixed/30 px-1.5 py-0.5 rounded">
                                    SLA TIER 1
                                </span>
                            </div>
                            <div class="flex items-center gap-unit-md py-unit-xs">
                                <img class="w-14 h-14 rounded-full object-cover shadow-sm ring-2 ring-tertiary-fixed/50"
                                    data-alt="Professional portrait of Senior Lead Systems Engineer Viktor Morozov"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" />
                                <div class="flex flex-col">
                                    <span
                                        class="font-headline-sm text-headline-sm text-primary font-bold leading-tight">Viktor
                                        Morozov</span>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">Senior Lead Systems
                                        Engineer</span>
                                    <span class="font-technical-tag text-technical-tag text-secondary mt-0.5">ID:
                                        VP-ENG-4410 • Severstal Liaison</span>
                                </div>
                            </div>
                            <div
                                class="flex flex-col gap-1.5 py-unit-sm my-unit-xs font-data-mono-md text-data-mono-md text-on-surface-variant bg-surface-container-low p-unit-sm rounded">
                                <div class="flex items-center justify-between">
                                    <span>Direct Desk:</span>
                                    <span class="font-semibold text-primary">+7 (812) 409-22-11 ext. 4410</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Emergency Mobile:</span>
                                    <span class="font-semibold text-primary">+7 (921) 880-14-99</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>SLA Response:</span>
                                    <span class="font-semibold text-secondary">&lt; 15 min for P1 Critical</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-unit-xs pt-unit-xs">
                                <button
                                    class="flex-1 py-2 bg-primary-container hover:bg-primary text-on-primary font-technical-tag text-technical-tag font-bold rounded inline-flex items-center justify-center gap-1.5 transition-colors cursor-pointer"
                                    onclick="window.location.href='SupportTicketView.php'"
                                    type="button">
                                    <span class="material-symbols-outlined text-base">chat</span>
                                    <span>SECURE DISPATCH CHAT</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('#dash-activity-filters .dash-filter-btn');
        const items = document.querySelectorAll('#dash-portal-activity-feed .feed-item');
        const emptyNotice = document.getElementById('dash-portal-empty-filter');
        const countEl = document.getElementById('dash-portal-events-count');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                filterBtns.forEach(b => {
                    b.className = 'dash-filter-btn px-2.5 py-1 text-technical-tag font-technical-tag rounded font-medium text-on-surface-variant hover:text-primary transition-colors cursor-pointer';
                });
                this.className = 'dash-filter-btn px-2.5 py-1 text-technical-tag font-technical-tag rounded font-semibold bg-surface-container-lowest text-primary shadow-xs transition-colors cursor-pointer';

                let visible = 0;
                items.forEach(item => {
                    if (filter === 'all' || item.getAttribute('data-category') === filter) {
                        item.style.display = 'flex';
                        visible++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (emptyNotice) {
                    emptyNotice.style.display = (visible === 0 && items.length > 0) ? 'block' : 'none';
                }
                if (countEl) {
                    countEl.textContent = `Showing ${visible} recent system event${visible === 1 ? '' : 's'}`;
                }
            });
        });
    });
    </script>
</body>

</html>