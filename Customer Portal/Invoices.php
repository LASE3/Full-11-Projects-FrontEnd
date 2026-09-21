<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('CUS');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>VOSTOKPRIBOR Portal</title>
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
    <link rel="stylesheet" href="css/invoices.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script
        id="tailwind-config">tailwind.config = { darkMode: "class", theme: { extend: { colors: { "on-surface-variant": "#43474c", "on-tertiary": "#ffffff", "on-primary": "#ffffff", "secondary-fixed": "#d2e4ff", "primary-fixed-dim": "#b4c8e3", "on-secondary-fixed": "#001c38", "outline": "#74777d", "surface-bright": "#f8f9fb", "on-surface": "#191c1e", "surface": "#f8f9fb", "primary-container": "#0f2438", "secondary-container": "#b6d4fe", "on-error": "#ffffff", "on-secondary-container": "#3f5b7f", "inverse-primary": "#b4c8e3", "inverse-surface": "#2d3133", "error": "#ba1a1a", "tertiary-fixed": "#ffddb5", "on-tertiary-fixed": "#2a1800", "on-primary-fixed": "#071d30", "primary-fixed": "#d0e4ff", "on-error-container": "#93000a", "secondary-fixed-dim": "#abc9f2", "surface-container-low": "#f2f4f6", "surface-dim": "#d8dadc", "surface-container-high": "#e6e8ea", "on-secondary": "#ffffff", "secondary": "#436084", "surface-container": "#eceef0", "background": "#f8f9fb", "tertiary-fixed-dim": "#ffb956", "on-primary-fixed-variant": "#35485e", "primary": "#000e1d", "on-background": "#191c1e", "tertiary": "#150a00", "surface-variant": "#e0e3e5", "on-tertiary-container": "#bb7d16", "on-tertiary-fixed-variant": "#643f00", "error-container": "#ffdad6", "surface-container-highest": "#e0e3e5", "inverse-on-surface": "#eff1f3", "surface-tint": "#4d6077", "outline-variant": "#c4c6cd", "on-primary-container": "#788ca4", "surface-container-lowest": "#ffffff", "on-secondary-fixed-variant": "#2b486b", "tertiary-container": "#331e00" }, borderRadius: { "DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem" }, spacing: { "unit-md": "0.75rem", "unit-xs": "0.25rem", "grid-gutter": "1rem", "grid-margin": "1.5rem", "unit-xl": "2rem", "unit-sm": "0.5rem", "unit-2xs": "0.125rem", "unit-lg": "1.5rem", "unit-2xl": "3rem", "unit-base": "1rem" }, fontFamily: { "data-mono-md": ["JetBrains Mono"], "body-md": ["IBM Plex Sans"], "label-caps": ["IBM Plex Sans"], "technical-tag": ["JetBrains Mono"], "display-lg": ["IBM Plex Sans"], "data-mono-lg": ["JetBrains Mono"], "display-lg-mobile": ["IBM Plex Sans"], "headline-sm": ["IBM Plex Sans"], "headline-lg": ["IBM Plex Sans"], "headline-md": ["IBM Plex Sans"], "body-lg": ["IBM Plex Sans"], "body-sm": ["IBM Plex Sans"] }, fontSize: { "data-mono-md": ["12px", { "lineHeight": "16px", "letterSpacing": "-0.01em", "fontWeight": "500" }], "body-md": ["13px", { "lineHeight": "18px", "letterSpacing": "0em", "fontWeight": "400" }], "label-caps": ["11px", { "lineHeight": "14px", "letterSpacing": "0.06em", "fontWeight": "700" }], "technical-tag": ["10px", { "lineHeight": "12px", "letterSpacing": "0.02em", "fontWeight": "500" }], "display-lg": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "700" }], "data-mono-lg": ["14px", { "lineHeight": "20px", "letterSpacing": "-0.02em", "fontWeight": "600" }], "display-lg-mobile": ["24px", { "lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "700" }], "headline-sm": ["16px", { "lineHeight": "24px", "letterSpacing": "-0.005em", "fontWeight": "600" }], "headline-lg": ["24px", { "lineHeight": "32px", "letterSpacing": "-0.015em", "fontWeight": "600" }], "headline-md": ["20px", { "lineHeight": "28px", "letterSpacing": "-0.01em", "fontWeight": "600" }], "body-lg": ["15px", { "lineHeight": "22px", "letterSpacing": "0em", "fontWeight": "400" }], "body-sm": ["12px", { "lineHeight": "16px", "letterSpacing": "0.005em", "fontWeight": "400" }] } } } };</script>
    <script src="js/portal.js"></script>
    <script src="js/invoices.js"></script>
</head>

<body class="bg-background font-body-md text-body-md text-on-background">
    <header
        class="fixed top-0 left-0 right-0 h-16 bg-primary-container z-50 flex items-center justify-between px-unit-lg shadow-sm">
        <div class="flex items-center gap-unit-base">
            <button id="sidebar-toggle-btn" class="p-1.5 -ml-1 mr-1 rounded text-on-primary-container hover:text-on-primary hover:bg-surface-container-high/10 transition-colors flex items-center justify-center cursor-pointer focus:outline-none" title="Toggle Navigation Menu (Ctrl+B)">
                <span class="material-symbols-outlined text-2xl" id="sidebar-toggle-icon">menu</span>
            </button>
            <img alt="VOSTOKPRIBOR Logo" class="h-8 w-auto object-contain cursor-pointer" onclick="location.href='Dashboard.php'"
                src="assets/logo.svg">
            <div class="h-6 w-px bg-on-primary-container/30"></div>
            <div class="flex flex-col">
                <div class="flex items-center gap-unit-xs"><span
                        class="font-headline-sm text-headline-sm text-on-primary font-semibold tracking-tight cursor-pointer" onclick="location.href='Dashboard.php'">VOSTOKPRIBOR</span><span
                        class="px-unit-xs py-0.5 rounded bg-surface-container-high/10 text-tertiary-fixed font-technical-tag text-technical-tag border border-tertiary-fixed/30">PORTAL</span>
                </div>
                <div
                    class="flex items-center gap-unit-xs text-on-primary-container font-technical-tag text-technical-tag">
                    <span class="">Severstal Metallurgy Plant #4</span><span
                        class="text-on-primary-container/50">|</span><span
                        class="text-primary-fixed-dim">VP-88204-EU</span>
                </div>
            </div>
        </div>
        <div class="w-64 md:w-80 lg:w-96 max-w-md mx-2 shrink-1">
            <div
                class="header-search-bar flex items-center bg-primary px-unit-md py-1.5 rounded text-on-primary-container ring-1 ring-white/10 cursor-text">
                <span class="material-symbols-outlined text-sm mr-unit-sm text-on-primary-container">search</span><input
                    class="header-search-input bg-transparent border-none outline-none font-body-sm text-body-sm text-on-primary placeholder:text-on-primary-container w-full"
                    placeholder="Search projects, serial numbers, specs..." type="text"><span
                    class="font-technical-tag text-technical-tag px-1.5 py-0.5 rounded bg-surface-container-high/10 text-on-primary-container border border-white/10">Ctrl+K</span>
            </div>
        </div>
        <div class="flex items-center gap-2 sm:gap-4 lg:gap-unit-lg shrink-0">
            <div
                class="hidden md:flex items-center gap-unit-xs px-unit-sm py-1 rounded bg-primary font-technical-tag text-technical-tag text-on-primary ring-1 ring-white/10">
                <span class="w-2 h-2 rounded-full bg-tertiary-fixed animate-pulse"></span><span
                    class="text-on-primary-container">Telemetry Node:</span><span class="text-tertiary-fixed">Online
                    99.98%</span>
            </div>
            <div id="header-bell-btn" class="relative flex items-center text-on-primary-container hover:text-on-primary cursor-pointer" title="Operational Telemetry Alerts"><span
                    class="material-symbols-outlined">notifications</span><span id="bell-unread-dot"
                    class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-tertiary-fixed ring-2 ring-primary-container"></span>
            </div><a class="flex items-center text-on-primary-container hover:text-on-primary" href="Documents.php"
                title="Technical Documentation"><span class="material-symbols-outlined">menu_book</span></a>
            <div class="h-6 w-px bg-on-primary-container/30"></div>
            <div class="flex items-center gap-unit-sm cursor-pointer" id="header-profile-btn">
                <div class="flex flex-col text-right"><span
                        class="font-headline-sm text-headline-sm text-on-primary font-medium leading-none">Alexey R.
                        Danilov</span><span
                        class="font-technical-tag text-technical-tag text-on-primary-container mt-0.5">Chief
                        Instrumentation Eng.</span></div><img alt="Alexey R. Danilov Profile"
                    class="w-8 h-8 rounded-full object-cover ring-1 ring-tertiary-fixed/50"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBxrM-O7aJYHYCDtkoA3WwbiOe6BxJ0vK7AcnogxwZN9MACsknTlpyGKyy-lWl2Hwn9IEZLPDCvVGrmxN2kvPEfzbJ5E4u5x6-38EP2exwXW8Dmm-7oMTzMG07_rmRLbT0xvZwQMFEwa4qJO5LcWbn58eWx3fSkVjAmSI3UWO8dCTgRg6GBgrY_MTUl-JF-JUf4K5CGPp0o4tvKoxbSqSysGT8r3j8de3w_sfk4F8p9ysiXXfbUkWPV">
            </div>
        
<!-- Top Bar Sign Out -->
<a href="../api/logout.php?system=Customer%20Portal&redirect=../Customer%20Portal/login.php" class="top-signout-btn" title="Sign Out of Customer Portal" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:4px;background:rgba(178,58,50,0.2);border:1px solid rgba(178,58,50,0.5);color:#FF8080;font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;margin-left:8px;vertical-align:middle;transition:all 0.2s;" onmouseover="this.style.background='rgba(178,58,50,0.4)';this.style.color='#FFFFFF'" onmouseout="this.style.background='rgba(178,58,50,0.2)';this.style.color='#FF8080'"><span class="material-symbols-outlined" style="font-size:15px;line-height:1;">logout</span><span>Sign Out</span></a>
</div>
    </header>
    <aside id="portal-sidebar" class="fixed left-0 top-16 bottom-0 w-64 bg-primary-container z-40 flex flex-col justify-between shadow-sm">
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
                    </svg><span class="">Orders</span></a><a
                    class="flex items-center gap-unit-sm px-unit-base py-unit-sm text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary transition-colors font-headline-sm text-headline-sm font-normal"
                    data-path="projects" href="ProjectListAndDetail.php"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                        stroke-width="1.75" viewBox="0 0 24 24">
                        <rect height="18" rx="2" width="18" x="3" y="3"></rect>
                        <path d="M3 9h18"></path>
                        <path d="M9 21V9"></path>
                    </svg><span class="">Projects</span></a><a aria-current="page"
                    class="flex items-center gap-unit-sm px-unit-base py-unit-sm transition-colors bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-on-tertiary-container font-headline-sm text-headline-sm"
                    data-path="invoices" href="Invoices.php"><svg class="w-4 h-4 shrink-0 text-tertiary-fixed" fill="none"
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
                <!-- Breadcrumbs & Status Bar -->
                    <div class="flex flex-wrap items-center justify-between gap-unit-sm">
                        <nav
                            class="flex items-center gap-unit-xs text-on-surface-variant font-data-mono-md text-data-mono-md">
                            <span class="hover:text-on-surface cursor-pointer">Enterprise Portal</span>
                            <span class="">/</span>
                            <span class="hover:text-on-surface cursor-pointer">Commercial &amp; Billing</span>
                            <span class="">/</span>
                            <span class="text-on-surface font-semibold">Invoices</span>
                        </nav>
                        <div
                            class="flex items-center gap-unit-xs px-unit-sm py-1 rounded bg-surface-container-high text-on-surface-variant font-technical-tag text-technical-tag">
                            <span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-container animate-pulse"></span>
                            <span class="">ACCOUNT STATUS:</span>
                            <span class="text-on-surface font-semibold">CLEAN AUDIT RECONCILIATION</span>
                            <span class="text-outline-variant">|</span>
                            <span class="">CURRENCY:</span>
                            <span class="text-on-surface font-semibold">USD / EQUIV RUB</span>
                        </div>
                    </div>
                    <!-- Page Header & Action Bar -->
                    <div
                        class="flex flex-col lg:flex-row lg:items-end justify-between gap-unit-base bg-surface-container-lowest p-unit-lg rounded-lg shadow-sm">
                        <div class="flex flex-col gap-unit-xs">
                            <div class="flex items-center gap-unit-sm">
                                <span
                                    class="p-1 rounded bg-primary text-tertiary-fixed flex items-center justify-center">
                                    <span class="material-symbols-outlined text-lg">account_balance</span>
                                </span>
                                <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Billing
                                    Statements &amp; Commercial Invoices</h1>
                            </div>
                            <p class="font-body-md text-body-md text-on-surface-variant">
                                Corporate accounts payable, milestone disbursements, and VAT tax invoices for <span
                                    class="font-semibold text-on-surface">Severstal Metallurgy Plant #4</span> (Entity
                                ID: <span class="font-data-mono-md text-data-mono-md">RU-SVR-77401</span>).
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-unit-sm">
                            <button onclick="exportLedger()"
                                class="flex items-center gap-unit-xs px-unit-base py-2 rounded bg-surface-container text-on-surface font-headline-sm text-headline-sm hover:bg-surface-container-high transition-colors shadow-sm">
                                <span class="material-symbols-outlined text-base">download</span>
                                <span class="">Download Ledger (.XLSX)</span>
                            </button>
                            <button onclick="showPaymentModal()"
                                class="flex items-center gap-unit-xs px-unit-base py-2 rounded bg-tertiary-fixed-dim text-on-tertiary-fixed font-headline-sm text-headline-sm font-semibold hover:bg-tertiary-fixed transition-colors shadow-sm">
                                <span class="material-symbols-outlined text-base">payments</span>
                                <span class="">Pay Outstanding Balance</span>
                            </button>
                        </div>
                    </div>
                    <!-- KPI Metrics Bento Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-unit-base">
                        <!-- KPI 1 -->
                        <div
                            class="relative overflow-hidden bg-surface-container-lowest p-unit-base rounded-lg shadow-sm flex flex-col justify-between">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-on-tertiary-container"></div>
                            <div class="flex items-start justify-between">
                                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Total
                                    Outstanding Balance</span>
                                <span
                                    class="material-symbols-outlined text-base text-on-tertiary-container">pending_actions</span>
                            </div>
                            <div class="my-unit-sm">
                                <div
                                    class="font-data-mono-lg text-display-lg text-on-surface font-bold tracking-tight leading-none">
                                    $248,600.00</div>
                                <div class="font-technical-tag text-technical-tag text-on-surface-variant mt-1">USD
                                    CURRENCY BASE</div>
                            </div>
                            <div
                                class="flex items-center justify-between text-body-sm pt-unit-xs bg-surface-container-low/60 px-unit-sm py-1 rounded">
                                <span class="text-on-surface-variant">3 pending invoices</span>
                                <span
                                    class="font-technical-tag text-technical-tag font-semibold text-on-tertiary-container">2
                                    due &lt; 10 days</span>
                            </div>
                        </div>
                        <!-- KPI 2 -->
                        <div
                            class="relative overflow-hidden bg-surface-container-lowest p-unit-base rounded-lg shadow-sm flex flex-col justify-between">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-on-tertiary-container"></div>
                            <div class="flex items-start justify-between">
                                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Total
                                    Capital Paid (FY 2024)</span>
                                <span class="material-symbols-outlined text-base text-secondary">verified</span>
                            </div>
                            <div class="my-unit-sm">
                                <div
                                    class="font-data-mono-lg text-display-lg text-on-surface font-bold tracking-tight leading-none">
                                    $4,812,400.00</div>
                                <div class="font-technical-tag text-technical-tag text-on-surface-variant mt-1">19
                                    MILESTONES FULFILLED</div>
                            </div>
                            <div
                                class="flex items-center justify-between text-body-sm pt-unit-xs bg-surface-container-low/60 px-unit-sm py-1 rounded">
                                <span class="text-on-surface-variant">Disbursement Rate</span>
                                <span class="font-technical-tag text-technical-tag font-semibold text-secondary">95.1%
                                    SLA Compliance</span>
                            </div>
                        </div>
                        <!-- KPI 3 -->
                        <div
                            class="relative overflow-hidden bg-surface-container-lowest p-unit-base rounded-lg shadow-sm flex flex-col justify-between">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-on-tertiary-container"></div>
                            <div class="flex items-start justify-between">
                                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Under
                                    Dispute / Review</span>
                                <span class="material-symbols-outlined text-base text-outline">fact_check</span>
                            </div>
                            <div class="my-unit-sm">
                                <div
                                    class="font-data-mono-lg text-display-lg text-on-surface font-bold tracking-tight leading-none">
                                    $0.00</div>
                                <div class="font-technical-tag text-technical-tag text-on-surface-variant mt-1">ZERO
                                    FLAGGED DISCREPANCIES</div>
                            </div>
                            <div
                                class="flex items-center justify-between text-body-sm pt-unit-xs bg-surface-container-low/60 px-unit-sm py-1 rounded">
                                <span class="text-on-surface-variant">Reconciliation record</span>
                                <span class="font-technical-tag text-technical-tag font-semibold text-secondary">Passed
                                    Stage-3 Audit</span>
                            </div>
                        </div>
                        <!-- KPI 4 -->
                        <div
                            class="relative overflow-hidden bg-surface-container-lowest p-unit-base rounded-lg shadow-sm flex flex-col justify-between">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-on-tertiary-container"></div>
                            <div class="flex items-start justify-between">
                                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Next
                                    Maturity Milestone</span>
                                <span
                                    class="material-symbols-outlined text-base text-on-tertiary-container">event_upcoming</span>
                            </div>
                            <div class="my-unit-sm">
                                <div
                                    class="font-data-mono-lg text-headline-lg text-on-surface font-bold tracking-tight leading-snug">
                                    Nov 28, 2024</div>
                                <div
                                    class="font-technical-tag text-technical-tag text-on-surface-variant mt-1 font-semibold">
                                    PRJ-VP-7721 FAT SIGNOFF</div>
                            </div>
                            <div
                                class="flex items-center justify-between text-body-sm pt-unit-xs bg-surface-container-low/60 px-unit-sm py-1 rounded">
                                <span class="text-on-surface-variant">Blast Furnace #5</span>
                                <span
                                    class="font-technical-tag text-technical-tag text-on-tertiary-container font-semibold">$114,200.00
                                    DUE</span>
                            </div>
                        </div>
                    </div>
                    <!-- Active Financial Context Panel: Ledger Visualizer & Progress -->
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-unit-base">
                        <!-- SVG Disbursement Streamline -->
                        <div
                            class="xl:col-span-2 bg-surface-container-lowest p-unit-base rounded-lg shadow-sm flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-unit-sm">
                                <div class="flex items-center gap-unit-xs">
                                    <span class="material-symbols-outlined text-secondary text-base">monitoring</span>
                                    <span class="font-headline-sm text-headline-sm text-on-surface">FY 2024 Capital
                                        Disbursement Trajectory</span>
                                </div>
                                <div class="flex items-center gap-unit-sm font-technical-tag text-technical-tag">
                                    <span class="flex items-center gap-1"><span
                                            class="w-2 h-2 rounded bg-secondary"></span> Cumulative Paid</span>
                                    <span class="flex items-center gap-1"><span
                                            class="w-2 h-2 rounded bg-tertiary-fixed-dim"></span> Pending Release</span>
                                </div>
                            </div>
                            <!-- Inline Vector Chart for Milestone Capital Flow -->
                            <div class="w-full bg-surface-container-low/40 rounded p-unit-sm">
                                <svg class="w-full h-28" fill="none" preserveAspectRatio="none" viewBox="0 0 680 110">
                                    <!-- Grid Lines -->
                                    <line class="text-surface-container-highest" stroke="currentColor"
                                        stroke-dasharray="2 4" x1="0" x2="680" y1="20" y2="20"></line>
                                    <line class="text-surface-container-highest" stroke="currentColor"
                                        stroke-dasharray="2 4" x1="0" x2="680" y1="55" y2="55"></line>
                                    <line class="text-surface-container-highest" stroke="currentColor"
                                        stroke-dasharray="2 4" x1="0" x2="680" y1="90" y2="90"></line>
                                    <!-- Area Gradients -->
                                    <defs>
                                        <linearGradient id="areaPaid" x1="0" x2="0" y1="0" y2="1">
                                            <stop offset="0%" stop-color="#436084" stop-opacity="0.25"></stop>
                                            <stop offset="100%" stop-color="#436084" stop-opacity="0"></stop>
                                        </linearGradient>
                                    </defs>
                                    <!-- Cumulative Curve -->
                                    <path
                                        d="M0,95 L60,88 L140,80 L220,70 L300,58 L380,45 L460,35 L540,24 L600,20 L680,18 L680,110 L0,110 Z"
                                        fill="url(#areaPaid)"></path>
                                    <path
                                        d="M0,95 L60,88 L140,80 L220,70 L300,58 L380,45 L460,35 L540,24 L600,20 L680,18"
                                        stroke="#436084" stroke-linecap="round" stroke-width="2.5"></path>
                                    <!-- Projected Pending Line -->
                                    <path d="M540,24 L610,12 L680,8" stroke="#bb7d16" stroke-dasharray="4 3"
                                        stroke-width="2"></path>
                                    <!-- Milestone Nodes -->
                                    <circle cx="140" cy="80" fill="#436084" r="3.5"></circle>
                                    <circle cx="300" cy="58" fill="#436084" r="3.5"></circle>
                                    <circle cx="460" cy="35" fill="#436084" r="3.5"></circle>
                                    <circle cx="540" cy="24" fill="#000e1d" r="4.5"></circle>
                                    <circle cx="610" cy="12" fill="#bb7d16" r="4"></circle>
                                </svg>
                                <div
                                    class="flex justify-between font-technical-tag text-technical-tag text-on-surface-variant pt-unit-xs">
                                    <span class="">Q1-2024 (Pre-Engineering)</span>
                                    <span class="">Q2-2024 (Hardware Delivery)</span>
                                    <span class="">Q3-2024 (Telemetry Install)</span>
                                    <span class="font-semibold text-on-surface">Q4-2024 (Commissioning &amp; FAT)</span>
                                </div>
                            </div>
                        </div>
                        <!-- Compliance & Tax Digest -->
                        <div
                            class="bg-surface-container-lowest p-unit-base rounded-lg shadow-sm flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">GOST /
                                    EDI Statutory Alignment</span>
                                <span class="material-symbols-outlined text-secondary text-base">receipt_long</span>
                            </div>
                            <div class="flex flex-col gap-unit-xs my-unit-xs">
                                <div
                                    class="flex justify-between items-center bg-surface-container-low p-unit-xs rounded">
                                    <span class="text-body-sm text-on-surface">Standard VAT (20% RU GOST):</span>
                                    <span
                                        class="font-data-mono-md text-data-mono-md font-bold text-on-surface">$802,066.67
                                        USD</span>
                                </div>
                                <div
                                    class="flex justify-between items-center bg-surface-container-low p-unit-xs rounded">
                                    <span class="text-body-sm text-on-surface">Digital Act Protocol (EDI):</span>
                                    <span
                                        class="font-technical-tag text-technical-tag px-1.5 py-0.5 rounded bg-surface-container-highest text-secondary font-semibold">TORG-12
                                        SYNCHRONIZED</span>
                                </div>
                                <div
                                    class="flex justify-between items-center bg-surface-container-low p-unit-xs rounded">
                                    <span class="text-body-sm text-on-surface">Diadoc / SBIS Gate:</span>
                                    <span
                                        class="font-technical-tag text-technical-tag px-1.5 py-0.5 rounded bg-surface-container-highest text-on-surface-variant">AUTO-INGEST
                                        ENABLED</span>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between pt-unit-xs border-t-0 text-on-surface-variant font-technical-tag text-technical-tag">
                                <span class="">TAX LEDGER VALIDATION REF:</span>
                                <span class="font-data-mono-md text-data-mono-md text-on-surface">TX-2024-SVST-09</span>
                            </div>
                        </div>
                    </div>
                    <!-- Filters, Tabs & Search Controls -->
                    <div class="flex flex-col gap-unit-sm bg-surface-container-lowest p-unit-base rounded-lg shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-unit-base">
                            <!-- Status Filter Tabs -->
                            <div class="flex flex-wrap items-center gap-1 bg-surface-container p-1 rounded">
                                <button onclick="filterInvoiceStatus(this, 'all')"
                                    class="invoice-filter-tab px-unit-base py-1 rounded bg-surface-container-lowest text-on-surface font-headline-sm text-body-md font-semibold shadow-sm">
                                    All Invoices <span
                                        class="ml-1 font-technical-tag text-technical-tag text-on-surface-variant font-normal">24</span>
                                </button>
                                <button onclick="filterInvoiceStatus(this, 'pending')"
                                    class="invoice-filter-tab px-unit-base py-1 rounded text-on-surface-variant hover:text-on-surface font-body-md transition-colors">
                                    Pending Payment <span
                                        class="ml-1 px-1.5 py-0.2 rounded bg-tertiary-fixed text-on-tertiary-fixed font-technical-tag text-technical-tag font-bold">3</span>
                                </button>
                                <button onclick="filterInvoiceStatus(this, 'paid')"
                                    class="invoice-filter-tab px-unit-base py-1 rounded text-on-surface-variant hover:text-on-surface font-body-md transition-colors">
                                    Paid Archive <span
                                        class="ml-1 font-technical-tag text-technical-tag text-on-surface-variant">19</span>
                                </button>
                                <button onclick="filterInvoiceStatus(this, 'credit')"
                                    class="invoice-filter-tab px-unit-base py-1 rounded text-on-surface-variant hover:text-on-surface font-body-md transition-colors">
                                    Credit Notes &amp; Adjustments <span
                                        class="ml-1 font-technical-tag text-technical-tag text-on-surface-variant">2</span>
                                </button>
                            </div>
                            <!-- Right Tool Controls -->
                            <div class="flex items-center gap-unit-sm">
                                <div
                                    class="flex items-center bg-surface-container-low px-unit-sm py-1 rounded text-on-surface-variant">
                                    <span class="material-symbols-outlined text-base mr-1">tune</span>
                                    <span class="font-body-sm text-body-sm font-medium">Batch Actions (0)</span>
                                </div>
                                <button onclick="window.print()"
                                    class="flex items-center gap-1 bg-surface-container-low px-unit-sm py-1 rounded text-on-surface hover:bg-surface-container transition-colors font-body-sm">
                                    <span class="material-symbols-outlined text-base">print</span>
                                    <span class="">Print Ledger</span>
                                </button>
                            </div>
                        </div>
                        <!-- Advanced Filter Search Bar -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-unit-sm pt-unit-xs">
                            <div
                                class="md:col-span-5 flex items-center bg-surface-container-low px-unit-md py-1.5 rounded">
                                <span
                                    class="material-symbols-outlined text-base mr-unit-sm text-on-surface-variant">search</span>
                                <input id="invoiceSearchInput" oninput="filterInvoiceTable()"
                                    class="bg-transparent border-none outline-none font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant w-full"
                                    placeholder="Search by Invoice #, Project ID, Contract Ref..." type="text">
                            </div>
                            <div
                                class="md:col-span-3 flex items-center bg-surface-container-low px-unit-md py-1.5 rounded justify-between">
                                <div class="flex items-center gap-unit-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-base">calendar_today</span>
                                    <span class="font-body-sm text-body-sm">Fiscal Period:</span>
                                    <span class="text-on-surface font-semibold font-body-sm">FY 2024 (Full Year)</span>
                                </div>
                                <span
                                    class="material-symbols-outlined text-base text-on-surface-variant">arrow_drop_down</span>
                            </div>
                            <div
                                class="md:col-span-2 flex items-center bg-surface-container-low px-unit-md py-1.5 rounded justify-between">
                                <div class="flex items-center gap-unit-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-base">domain</span>
                                    <span class="font-body-sm text-body-sm">Unit:</span>
                                    <span class="text-on-surface font-semibold font-body-sm">Plant #4</span>
                                </div>
                                <span
                                    class="material-symbols-outlined text-base text-on-surface-variant">arrow_drop_down</span>
                            </div>
                            <div class="md:col-span-2 flex items-center justify-end">
                                <button onclick="resetInvoiceFilters()"
                                    class="w-full flex items-center justify-center gap-1 bg-surface-container hover:bg-surface-container-high py-1.5 rounded text-on-surface font-body-sm font-semibold transition-colors">
                                    <span class="material-symbols-outlined text-base">filter_alt_off</span>
                                    <span class="">Reset Filters</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Main High-Density Financial Data Table -->
                    <div class="bg-surface-container-lowest rounded-lg shadow-sm overflow-hidden flex flex-col">
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse text-left">
                                <thead>
                                    <tr
                                        class="bg-surface-container-low text-on-surface-variant font-label-caps text-label-caps uppercase tracking-wider">
                                        <th class="py-2.5 px-unit-base w-10 text-center">
                                            <input class="w-4 h-4 rounded bg-surface-container-lowest" type="checkbox">
                                        </th>
                                        <th class="py-2.5 px-unit-base">Invoice Ref #</th>
                                        <th class="py-2.5 px-unit-base">Linked Project &amp; Milestone Scope</th>
                                        <th class="py-2.5 px-unit-base">Issue / Maturity Date</th>
                                        <th class="py-2.5 px-unit-base text-right">Net Valuation</th>
                                        <th class="py-2.5 px-unit-base text-right">VAT (20% GOST)</th>
                                        <th class="py-2.5 px-unit-base text-center">Operational Status</th>
                                        <th class="py-2.5 px-unit-base">Legal Artifacts</th>
                                        <th class="py-2.5 px-unit-base text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y-0">
                                    <!-- Row 1: Confidential left border in amber-orange #D9822B -->
                                    <tr id="row-INV-2024-8819" data-status="pending"
                                        class="invoice-row relative bg-surface-container-lowest hover:bg-surface-container-low transition-colors group">
                                        <td class="relative py-3 px-unit-base text-center">
                                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-on-tertiary-container">
                                            </div>
                                            <input class="w-4 h-4 rounded bg-surface-container-lowest" type="checkbox">
                                        </td>
                                        <td class="py-3 px-unit-base">
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-data-mono-lg text-data-mono-lg font-bold text-on-surface">INV-2024-8819</span>
                                                <span
                                                    class="font-technical-tag text-technical-tag text-on-surface-variant">CTR-SVR-2024-08A</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-unit-base max-w-xs">
                                            <div class="flex flex-col">
                                                <a href="ProjectListAndDetail.php?project=PRJ-VP-7721"
                                                    class="font-headline-sm text-body-md font-semibold text-on-surface hover:text-on-tertiary-container transition-colors truncate">PRJ-VP-7721:
                                                    Blast Furnace #5 Cold Commissioning</a>
                                                <span
                                                    class="font-body-sm text-body-sm text-on-surface-variant truncate">Stage
                                                    4: Automated Pressure Transducers &amp; Gas Flue Calibrations</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-unit-base">
                                            <div class="flex flex-col font-data-mono-md text-data-mono-md">
                                                <span class="text-on-surface-variant">04 Nov 2024</span>
                                                <span class="text-on-tertiary-container font-semibold">Due 28 Nov 2024
                                                    (14d)</span>
                                            </div>
                                        </td>
                                        <td
                                            class="py-3 px-unit-base text-right font-data-mono-lg text-data-mono-lg font-bold text-on-surface">
                                            $114,200.00
                                        </td>
                                        <td
                                            class="py-3 px-unit-base text-right font-data-mono-md text-data-mono-md text-on-surface-variant">
                                            $22,840.00
                                        </td>
                                        <td class="py-3 px-unit-base text-center">
                                            <span
                                                class="inline-flex items-center gap-1 px-unit-sm py-0.5 rounded bg-tertiary-fixed text-on-tertiary-fixed font-technical-tag text-technical-tag font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-container"></span>
                                                Pending / Due in 14d
                                            </span>
                                        </td>
                                        <td class="py-3 px-unit-base">
                                            <div class="flex items-center gap-unit-xs">
                                                <a class="flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-surface-container font-technical-tag text-technical-tag text-on-surface hover:bg-surface-container-high cursor-pointer"
                                                    onclick="event.preventDefault(); window.previewDocument('INV-2024-8819', 'Commercial VAT Invoice & Stage 4 Calibrations', 'PDF Commercial Invoice')" href="#">
                                                    <span
                                                        class="material-symbols-outlined text-xs">picture_as_pdf</span>
                                                    <span class="">PDF 1.4MB</span>
                                                </a>
                                                <a class="flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-surface-container font-technical-tag text-technical-tag text-on-surface hover:bg-surface-container-high cursor-pointer"
                                                    onclick="event.preventDefault(); window.previewDocument('TORG-12-8819', 'TORG-12 Consignment Acceptance Certificate', 'TORG-12 Legal Act')" href="#">
                                                    <span class="material-symbols-outlined text-xs">code</span>
                                                    <span class="">TORG-12</span>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="py-3 px-unit-base text-right">
                                            <button onclick="showPaymentModal('INV-2024-8819', 114200)"
                                                class="px-unit-sm py-1 rounded bg-tertiary-fixed-dim text-on-tertiary-fixed hover:bg-tertiary-fixed font-label-caps text-label-caps font-semibold shadow-sm transition-colors">
                                                Review &amp; Authorize
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Row 2 -->
                                    <tr id="row-INV-2024-7019" data-status="pending"
                                        class="invoice-row relative bg-surface-container-low/30 hover:bg-surface-container-low transition-colors group">
                                        <td class="relative py-3 px-unit-base text-center">
                                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-on-tertiary-container">
                                            </div>
                                            <input class="w-4 h-4 rounded bg-surface-container-lowest" type="checkbox">
                                        </td>
                                        <td class="py-3 px-unit-base">
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-data-mono-lg text-data-mono-lg font-bold text-on-surface">INV-2024-7019</span>
                                                <span
                                                    class="font-technical-tag text-technical-tag text-on-surface-variant">CTR-SVR-2024-03B</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-unit-base max-w-xs">
                                            <div class="flex flex-col">
                                                <a href="ProjectListAndDetail.php?project=PRJ-VP-7804"
                                                    class="font-headline-sm text-body-md font-semibold text-on-surface hover:text-on-tertiary-container transition-colors truncate">PRJ-VP-7804:
                                                    Hydraulic Telemetry Phase 2</a>
                                                <span
                                                    class="font-body-sm text-body-sm text-on-surface-variant truncate">Delivery
                                                    of Ex-d Rated Explosion Proof Enclosures (x40 Units)</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-unit-base">
                                            <div class="flex flex-col font-data-mono-md text-data-mono-md">
                                                <span class="text-on-surface-variant">12 Oct 2024</span>
                                                <span class="text-on-tertiary-container font-semibold">Due 12 Nov 2024
                                                    (Scheduled)</span>
                                            </div>
                                        </td>
                                        <td
                                            class="py-3 px-unit-base text-right font-data-mono-lg text-data-mono-lg font-bold text-on-surface">
                                            $134,400.00
                                        </td>
                                        <td
                                            class="py-3 px-unit-base text-right font-data-mono-md text-data-mono-md text-on-surface-variant">
                                            $26,880.00
                                        </td>
                                        <td class="py-3 px-unit-base text-center">
                                            <span
                                                class="inline-flex items-center gap-1 px-unit-sm py-0.5 rounded bg-secondary-container text-on-secondary-container font-technical-tag text-technical-tag font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                                                Approved for Payment
                                            </span>
                                        </td>
                                        <td class="py-3 px-unit-base">
                                            <div class="flex items-center gap-unit-xs">
                                                <a class="flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-surface-container font-technical-tag text-technical-tag text-on-surface hover:bg-surface-container-high cursor-pointer"
                                                    onclick="event.preventDefault(); window.previewDocument('INV-2024-7019', 'Commercial Invoice PRJ-VP-7804 Phase 2', 'PDF Commercial Invoice')" href="#">
                                                    <span
                                                        class="material-symbols-outlined text-xs">picture_as_pdf</span>
                                                    <span class="">PDF 2.1MB</span>
                                                </a>
                                                <a class="flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-surface-container font-technical-tag text-technical-tag text-on-surface hover:bg-surface-container-high cursor-pointer"
                                                    onclick="event.preventDefault(); window.previewDocument('EDI-7019-SIG', 'Crypto-Pro EDS Signature Ledger Act', 'EDI XML Certificate')" href="#">
                                                    <span class="material-symbols-outlined text-xs">verified</span>
                                                    <span class="">EDI SIGNED</span>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="py-3 px-unit-base text-right">
                                            <button onclick="showPaymentModal('INV-2024-7019', 134400)"
                                                class="px-unit-sm py-1 rounded bg-surface-container hover:bg-surface-container-high text-on-surface font-label-caps text-label-caps font-semibold transition-colors">
                                                View Approval
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Row 3 -->
                                    <tr id="row-INV-2024-6410" data-status="paid"
                                        class="invoice-row relative bg-surface-container-lowest hover:bg-surface-container-low transition-colors group">
                                        <td class="relative py-3 px-unit-base text-center">
                                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-on-tertiary-container">
                                            </div>
                                            <input class="w-4 h-4 rounded bg-surface-container-lowest" type="checkbox">
                                        </td>
                                        <td class="py-3 px-unit-base">
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-data-mono-lg text-data-mono-lg font-bold text-on-surface">INV-2024-6410</span>
                                                <span
                                                    class="font-technical-tag text-technical-tag text-on-surface-variant">CTR-SVR-2023-99C</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-unit-base max-w-xs">
                                            <div class="flex flex-col">
                                                <a href="ProjectListAndDetail.php?project=PRJ-VP-6945"
                                                    class="font-headline-sm text-body-md font-semibold text-on-surface hover:text-on-tertiary-container transition-colors truncate">PRJ-VP-6945:
                                                    Raw Materials Conveyor Calibration</a>
                                                <span
                                                    class="font-body-sm text-body-sm text-on-surface-variant truncate">Telemetry
                                                    integration &amp; optical infrared sensor arrays</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-unit-base">
                                            <div class="flex flex-col font-data-mono-md text-data-mono-md">
                                                <span class="text-on-surface-variant">18 Sep 2024</span>
                                                <span class="text-on-surface">Paid 05 Oct 2024</span>
                                            </div>
                                        </td>
                                        <td
                                            class="py-3 px-unit-base text-right font-data-mono-lg text-data-mono-lg font-bold text-on-surface">
                                            $370,000.00
                                        </td>
                                        <td
                                            class="py-3 px-unit-base text-right font-data-mono-md text-data-mono-md text-on-surface-variant">
                                            $74,000.00
                                        </td>
                                        <td class="py-3 px-unit-base text-center">
                                            <span
                                                class="inline-flex items-center gap-1 px-unit-sm py-0.5 rounded bg-surface-container-high text-on-surface font-technical-tag text-technical-tag font-semibold">
                                                <span
                                                    class="material-symbols-outlined text-xs text-secondary">check_circle</span>
                                                Paid / Reconciled
                                            </span>
                                        </td>
                                        <td class="py-3 px-unit-base">
                                            <div class="flex items-center gap-unit-xs">
                                                <a class="flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-surface-container font-technical-tag text-technical-tag text-on-surface hover:bg-surface-container-high cursor-pointer"
                                                    onclick="event.preventDefault(); window.previewDocument('INV-2024-6410', 'Commercial Invoice PRJ-VP-6945', 'PDF Commercial Invoice')" href="#">
                                                    <span
                                                        class="material-symbols-outlined text-xs">picture_as_pdf</span>
                                                    <span class="">PDF 3.8MB</span>
                                                </a>
                                                <a class="flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-surface-container font-technical-tag text-technical-tag text-on-surface hover:bg-surface-container-high cursor-pointer"
                                                    onclick="event.preventDefault(); window.previewDocument('ACT-44-6410', 'Acceptance Act #44 - Raw Materials Conveyor', 'Acceptance Act')" href="#">
                                                    <span class="material-symbols-outlined text-xs">receipt</span>
                                                    <span class="">ACT #44</span>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="py-3 px-unit-base text-right">
                                            <button onclick="window.previewDocument('RCP-6410-REC', 'Electronic Sberbank Settlement Slip #9921', 'Bank Wire Receipt')"
                                                class="px-unit-sm py-1 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-caps text-label-caps font-semibold transition-colors">
                                                Payment Receipt
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Row 4 -->
                                    <tr id="row-INV-2024-5890" data-status="paid"
                                        class="invoice-row relative bg-surface-container-low/30 hover:bg-surface-container-low transition-colors group">
                                        <td class="relative py-3 px-unit-base text-center">
                                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-on-tertiary-container">
                                            </div>
                                            <input class="w-4 h-4 rounded bg-surface-container-lowest" type="checkbox">
                                        </td>
                                        <td class="py-3 px-unit-base">
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-data-mono-lg text-data-mono-lg font-bold text-on-surface">INV-2024-5890</span>
                                                <span
                                                    class="font-technical-tag text-technical-tag text-on-surface-variant">CTR-SVR-2023-88X</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-unit-base max-w-xs">
                                            <div class="flex flex-col">
                                                <a href="ProjectListAndDetail.php?project=PRJ-VP-6211"
                                                    class="font-headline-sm text-body-md font-semibold text-on-surface hover:text-on-tertiary-container transition-colors truncate">PRJ-VP-6211:
                                                    Slag Granulation Flow Rig Testing</a>
                                                <span
                                                    class="font-body-sm text-body-sm text-on-surface-variant truncate">Full
                                                    hardware installation, sensor calibration certificates</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-unit-base">
                                            <div class="flex flex-col font-data-mono-md text-data-mono-md">
                                                <span class="text-on-surface-variant">25 Jul 2024</span>
                                                <span class="text-on-surface">Paid 10 Aug 2024</span>
                                            </div>
                                        </td>
                                        <td
                                            class="py-3 px-unit-base text-right font-data-mono-lg text-data-mono-lg font-bold text-on-surface">
                                            $740,000.00
                                        </td>
                                        <td
                                            class="py-3 px-unit-base text-right font-data-mono-md text-data-mono-md text-on-surface-variant">
                                            $148,000.00
                                        </td>
                                        <td class="py-3 px-unit-base text-center">
                                            <span
                                                class="inline-flex items-center gap-1 px-unit-sm py-0.5 rounded bg-surface-container-high text-on-surface font-technical-tag text-technical-tag font-semibold">
                                                <span
                                                    class="material-symbols-outlined text-xs text-secondary">check_circle</span>
                                                Paid / Reconciled
                                            </span>
                                        </td>
                                        <td class="py-3 px-unit-base">
                                            <div class="flex items-center gap-unit-xs">
                                                <a class="flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-surface-container font-technical-tag text-technical-tag text-on-surface hover:bg-surface-container-high cursor-pointer"
                                                    onclick="event.preventDefault(); window.previewDocument('INV-2024-5890', 'Commercial Invoice PRJ-VP-6211', 'PDF Commercial Invoice')" href="#">
                                                    <span
                                                        class="material-symbols-outlined text-xs">picture_as_pdf</span>
                                                    <span class="">PDF 1.9MB</span>
                                                </a>
                                                <a class="flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-surface-container font-technical-tag text-technical-tag text-on-surface hover:bg-surface-container-high cursor-pointer"
                                                    onclick="event.preventDefault(); window.previewDocument('XML-ACT-5890', 'Slag Granulation Testing GOST Electronic Act', 'XML Commercial Act')" href="#">
                                                    <span class="material-symbols-outlined text-xs">code</span>
                                                    <span class="">XML ACT</span>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="py-3 px-unit-base text-right">
                                            <button onclick="window.previewDocument('RCP-5890-REC', 'Electronic Settlement Slip #5890', 'Bank Wire Receipt')"
                                                class="px-unit-sm py-1 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-caps text-label-caps font-semibold transition-colors">
                                                Payment Receipt
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Row 5 -->
                                    <tr
                                        class="relative bg-surface-container-lowest hover:bg-surface-container-low transition-colors group">
                                        <td class="relative py-3 px-unit-base text-center">
                                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-on-tertiary-container">
                                            </div>
                                            <input class="w-4 h-4 rounded bg-surface-container-lowest" type="checkbox">
                                        </td>
                                        <td class="py-3 px-unit-base">
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-data-mono-lg text-data-mono-lg font-bold text-on-surface">INV-2024-4411</span>
                                                <span
                                                    class="font-technical-tag text-technical-tag text-on-surface-variant">CTR-SVR-2023-70G</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-unit-base max-w-xs">
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-headline-sm text-body-md font-semibold text-on-surface truncate">PRJ-VP-5502:
                                                    Central Turboblower Automation Unit</span>
                                                <span
                                                    class="font-body-sm text-body-sm text-on-surface-variant truncate">Turnkey
                                                    digital instrumentation delivery and field warranty protocol</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-unit-base">
                                            <div class="flex flex-col font-data-mono-md text-data-mono-md">
                                                <span class="text-on-surface-variant">14 May 2024</span>
                                                <span class="text-on-surface">Paid 28 May 2024</span>
                                            </div>
                                        </td>
                                        <td
                                            class="py-3 px-unit-base text-right font-data-mono-lg text-data-mono-lg font-bold text-on-surface">
                                            $555,000.00
                                        </td>
                                        <td
                                            class="py-3 px-unit-base text-right font-data-mono-md text-data-mono-md text-on-surface-variant">
                                            $111,000.00
                                        </td>
                                        <td class="py-3 px-unit-base text-center">
                                            <span
                                                class="inline-flex items-center gap-1 px-unit-sm py-0.5 rounded bg-surface-container-high text-on-surface font-technical-tag text-technical-tag font-semibold">
                                                <span
                                                    class="material-symbols-outlined text-xs text-secondary">check_circle</span>
                                                Paid / Reconciled
                                            </span>
                                        </td>
                                        <td class="py-3 px-unit-base">
                                            <div class="flex items-center gap-unit-xs">
                                                <a class="flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-surface-container font-technical-tag text-technical-tag text-on-surface hover:bg-surface-container-high cursor-pointer"
                                                    onclick="event.preventDefault(); window.previewDocument('INV-2024-5100', 'Commercial VAT Invoice - Blast Furnace Spares', 'PDF Commercial Invoice')">
                                                    <span
                                                        class="material-symbols-outlined text-xs">picture_as_pdf</span>
                                                    <span class="">PDF 4.2MB</span>
                                                </a>
                                                <a class="flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-surface-container font-technical-tag text-technical-tag text-on-surface hover:bg-surface-container-high cursor-pointer"
                                                    onclick="event.preventDefault(); window.previewDocument('EDI-5100-SIG', 'Electronic Fiscal Registry Ledger', 'EDI XML Certificate')">
                                                    <span class="material-symbols-outlined text-xs">verified</span>
                                                    <span class="">EDI SIGNED</span>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="py-3 px-unit-base text-right">
                                            <button onclick="window.showToast('Payment receipt archive loaded for INV-2024-5100', 'info')"
                                                class="px-unit-sm py-1 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-caps text-label-caps font-semibold transition-colors">
                                                Payment Receipt
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination & Record Count -->
                        <div
                            class="flex flex-wrap items-center justify-between p-unit-base bg-surface-container-low text-on-surface-variant font-body-sm">
                            <div class="flex items-center gap-unit-sm">
                                <span class="">Displaying <span class="font-semibold text-on-surface">1 - 5</span> of 24
                                    corporate statements</span>
                                <span class="text-outline-variant">|</span>
                                <span class="font-technical-tag text-technical-tag">LEDGER ENCRYPTION SHA-256
                                    VERIFIED</span>
                            </div>
                            <div class="flex items-center gap-unit-xs">
                                <button
                                    class="p-1 rounded bg-surface-container-lowest text-on-surface-variant hover:text-on-surface disabled:opacity-40"
                                    disabled="">
                                    <span class="material-symbols-outlined text-base">first_page</span>
                                </button>
                                <button
                                    class="p-1 rounded bg-surface-container-lowest text-on-surface-variant hover:text-on-surface disabled:opacity-40"
                                    disabled="">
                                    <span class="material-symbols-outlined text-base">chevron_left</span>
                                </button>
                                <span
                                    class="px-unit-sm py-0.5 rounded bg-primary text-on-primary font-data-mono-md text-data-mono-md">1</span>
                                <span
                                    class="px-unit-sm py-0.5 rounded hover:bg-surface-container font-data-mono-md text-data-mono-md cursor-pointer">2</span>
                                <span
                                    class="px-unit-sm py-0.5 rounded hover:bg-surface-container font-data-mono-md text-data-mono-md cursor-pointer">3</span>
                                <span
                                    class="px-unit-sm py-0.5 rounded hover:bg-surface-container font-data-mono-md text-data-mono-md cursor-pointer">4</span>
                                <span
                                    class="px-unit-sm py-0.5 rounded hover:bg-surface-container font-data-mono-md text-data-mono-md cursor-pointer">5</span>
                                <button
                                    class="p-1 rounded bg-surface-container-lowest text-on-surface hover:text-on-surface">
                                    <span class="material-symbols-outlined text-base">chevron_right</span>
                                </button>
                                <button
                                    class="p-1 rounded bg-surface-container-lowest text-on-surface hover:text-on-surface">
                                    <span class="material-symbols-outlined text-base">last_page</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Bottom Bank Wire & Payment Routing Card -->
                    <div class="bg-surface-container-lowest rounded-lg p-unit-lg shadow-sm flex flex-col gap-unit-md">
                        <div class="flex flex-wrap items-center justify-between gap-unit-sm pb-unit-xs">
                            <div class="flex items-center gap-unit-sm">
                                <span
                                    class="p-1.5 rounded bg-primary text-tertiary-fixed flex items-center justify-center">
                                    <span class="material-symbols-outlined text-base">assured_workload</span>
                                </span>
                                <div>
                                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Official
                                        Corporate Wire Routing &amp; Escrow Directives</h3>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant">Strict compliance with
                                        Russian Federation Central Bank clearing standards &amp; cross-border settlement
                                        protocols.</p>
                                </div>
                            </div>
                            <div
                                class="flex items-center gap-unit-xs px-unit-sm py-1 rounded bg-surface-container font-technical-tag text-technical-tag text-on-surface">
                                <span class="material-symbols-outlined text-sm text-secondary">verified_user</span>
                                <span class="">AUTHORIZED PAYEE: VOSTOKPRIBOR INDUSTRIAL NPO</span>
                            </div>
                        </div>
                        <!-- Banking Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-unit-base">
                            <!-- Bank & Routing -->
                            <div class="bg-surface-container-low p-unit-base rounded flex flex-col gap-unit-xs">
                                <span
                                    class="font-label-caps text-label-caps text-on-surface-variant uppercase">Settlement
                                    Depository Bank</span>
                                <div class="font-headline-sm text-body-md font-semibold text-on-surface">PJSC Sberbank /
                                    VTB Enterprise</div>
                                <div class="font-data-mono-md text-data-mono-md text-on-surface-variant">BIC / BIK:
                                    <span class="text-on-surface font-semibold">044525225</span>
                                </div>
                                <div class="font-data-mono-md text-data-mono-md text-on-surface-variant">SWIFT: <span
                                        class="text-on-surface font-semibold">SABRRUMM</span></div>
                            </div>
                            <!-- Accounts -->
                            <div class="bg-surface-container-low p-unit-base rounded flex flex-col gap-unit-xs">
                                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Primary
                                    Clearing Account</span>
                                <div class="font-data-mono-md text-data-mono-md font-bold text-on-surface break-all">
                                    40702810938000018921</div>
                                <div class="font-label-caps text-label-caps text-on-surface-variant uppercase mt-1">
                                    Correspondent Account</div>
                                <div class="font-data-mono-md text-data-mono-md text-on-surface break-all">
                                    30101810400000000225</div>
                            </div>
                            <!-- Tax Registration & Legal -->
                            <div class="bg-surface-container-low p-unit-base rounded flex flex-col gap-unit-xs">
                                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Tax
                                    Registration &amp; Legal Identifiers</span>
                                <div class="flex justify-between items-center text-body-sm">
                                    <span class="text-on-surface-variant">Tax ID (INN):</span>
                                    <span
                                        class="font-data-mono-md text-data-mono-md font-semibold text-on-surface">7802194812</span>
                                </div>
                                <div class="flex justify-between items-center text-body-sm">
                                    <span class="text-on-surface-variant">Tax Category (KPP):</span>
                                    <span
                                        class="font-data-mono-md text-data-mono-md font-semibold text-on-surface">780201001</span>
                                </div>
                                <div class="flex justify-between items-center text-body-sm">
                                    <span class="text-on-surface-variant">OGRN Code:</span>
                                    <span
                                        class="font-data-mono-md text-data-mono-md font-semibold text-on-surface">1027801569420</span>
                                </div>
                            </div>
                            <!-- Direct Contact Officer -->
                            <div class="bg-surface-container-low p-unit-base rounded flex flex-col justify-between">
                                <div class="flex flex-col gap-unit-2xs">
                                    <span
                                        class="font-label-caps text-label-caps text-on-surface-variant uppercase">Billing
                                        &amp; Treasury Lead</span>
                                    <div class="font-headline-sm text-body-md font-bold text-on-surface">Elena V.
                                        Rostova</div>
                                    <div class="font-technical-tag text-technical-tag text-on-surface-variant">Direct
                                        Desk: Plant #4 Commercial Liaison</div>
                                </div>
                                <div class="flex flex-col gap-1 pt-unit-xs font-data-mono-md text-data-mono-md">
                                    <div class="flex items-center justify-between">
                                        <span class="text-on-surface-variant">Desk:</span>
                                        <span class="text-on-surface font-medium">+7 812 409-22-44</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-on-surface-variant">Email:</span>
                                        <span class="text-on-surface font-medium">e.rostova@vostokpribor.ru</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Instruction Note Footer -->
                        <div
                            class="flex flex-wrap items-center justify-between gap-unit-sm pt-unit-xs bg-surface-container px-unit-base py-unit-sm rounded text-body-sm text-on-surface-variant">
                            <div class="flex items-center gap-unit-xs">
                                <span class="material-symbols-outlined text-base text-on-tertiary-container">info</span>
                                <span class="">Include invoice reference number (e.g. <span
                                        class="font-data-mono-md text-data-mono-md text-on-surface font-semibold">INV-2024-8819</span>)
                                    in wire field 70 ("Payment Narrative") for instant automatic ledger
                                    recognition.</span>
                            </div>
                            <button onclick="copyWirePacket()"
                                class="font-label-caps text-label-caps uppercase text-on-surface hover:text-on-tertiary-container transition-colors flex items-center gap-1 font-semibold">
                                <span class="">Copy Wire Packet</span>
                                <span class="material-symbols-outlined text-sm">content_copy</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

</body>

</html>