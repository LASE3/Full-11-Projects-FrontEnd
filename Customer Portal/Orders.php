<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('CUS');

// Establish database connection and identify current logged-in customer
$pdo = getDbConnection();
$cusId = $_SESSION['cus_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? null);

$orders = [];
$activeOrder = null;
$kpis = [
    'total_orders' => 0,
    'total_value' => 0.00,
    'in_transit' => 0,
    'in_mfg' => 0
];

if ($cusId) {
    try {
        // Fetch order stats for active customer
        $kpiStmt = $pdo->prepare("
            SELECT 
                COUNT(*) AS total_orders,
                COALESCE(SUM(total_amount), 0) AS total_value,
                SUM(CASE WHEN status IN ('Shipped', 'In Transit') THEN 1 ELSE 0 END) AS in_transit,
                SUM(CASE WHEN status IN ('Processing', 'Manufacturing') THEN 1 ELSE 0 END) AS in_mfg
            FROM orders 
            WHERE cus_id = :cid
        ");
        $kpiStmt->execute([':cid' => $cusId]);
        $kpis = $kpiStmt->fetch(PDO::FETCH_ASSOC);

        // Fetch overall orders list with equipment and facility info
        $ordersStmt = $pdo->prepare("
            SELECT 
                o.order_id,
                o.cus_id,
                o.order_date,
                o.status,
                o.total_amount,
                GROUP_CONCAT(p.product_name SEPARATOR ', ') AS equipment_summary,
                c.company_name AS facility_name
            FROM orders o
            LEFT JOIN order_items oi ON o.order_id = oi.order_id
            LEFT JOIN products p ON oi.prod_id = p.prod_id
            LEFT JOIN customers c ON o.cus_id = c.cus_id
            WHERE o.cus_id = :cid
            GROUP BY o.order_id
            ORDER BY o.order_date DESC
        ");
        $ordersStmt->execute([':cid' => $cusId]);
        $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch latest active order for top banner tracking
        $activeOrderStmt = $pdo->prepare("
            SELECT 
                o.order_id,
                o.order_date,
                o.status,
                o.total_amount,
                GROUP_CONCAT(p.product_name SEPARATOR ', ') AS equipment_summary,
                c.company_name AS facility_name
            FROM orders o
            LEFT JOIN order_items oi ON o.order_id = oi.order_id
            LEFT JOIN products p ON oi.prod_id = p.prod_id
            LEFT JOIN customers c ON o.cus_id = c.cus_id
            WHERE o.cus_id = :cid AND o.status IN ('Processing', 'Manufacturing', 'Shipped', 'In Transit')
            GROUP BY o.order_id
            ORDER BY o.order_date DESC
            LIMIT 1
        ");
        $activeOrderStmt->execute([':cid' => $cusId]);
        $activeOrder = $activeOrderStmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Orders Retrieval Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>VOSTOKPRIBOR Portal - Orders Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@400;500;600&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="css/common.css" />
    <link rel="stylesheet" href="css/orders.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/tailwind-config.js"></script>
    <script src="js/portal.js"></script>
    <link rel="stylesheet" href="../assets/css/api-ui.css">
    <script src="../assets/js/api-core.js"></script>
    <script src="../assets/js/api-customer.js"></script>
    <script src="js/portal-data.js"></script>
    <script src="js/orders.js"></script>
</head>

<body class="bg-background font-body-md text-body-md text-on-background">
    <header class="fixed top-0 left-0 right-0 h-16 bg-primary-container z-50 flex items-center justify-between px-unit-lg shadow-sm">
        <div class="flex items-center gap-unit-base">
            <button id="sidebar-toggle-btn" class="p-1.5 -ml-1 mr-1 rounded text-on-primary-container hover:text-on-primary hover:bg-surface-container-high/10 transition-colors flex items-center justify-center cursor-pointer focus:outline-none" title="Toggle Navigation Menu (Ctrl+B)">
                <span class="material-symbols-outlined text-2xl" id="sidebar-toggle-icon">menu</span>
            </button>
            <img alt="VOSTOKPRIBOR Logo" class="h-8 w-auto object-contain cursor-pointer" onclick="location.href='Dashboard.php'" src="assets/logo.svg">
            <div class="h-6 w-px bg-on-primary-container/30"></div>
            <div class="flex flex-col">
                <div class="flex items-center gap-unit-xs">
                    <span class="font-headline-sm text-headline-sm text-on-primary font-semibold tracking-tight cursor-pointer" onclick="location.href='Dashboard.php'">VOSTOKPRIBOR</span>
                    <span class="px-unit-xs py-0.5 rounded bg-surface-container-high/10 text-tertiary-fixed font-technical-tag text-technical-tag border border-tertiary-fixed/30">PORTAL</span>
                </div>
                <div class="flex items-center gap-unit-xs text-on-primary-container font-technical-tag text-technical-tag">
                    <span>Severstal Metallurgy Plant #4</span>
                    <span class="text-on-primary-container/50">|</span>
                    <span class="text-primary-fixed-dim">VP-88204-EU</span>
                </div>
            </div>
        </div>
        <div class="w-64 md:w-80 lg:w-96 max-w-md mx-2 shrink-1">
            <div class="header-search-bar flex items-center bg-primary px-unit-md py-1.5 rounded border border-outline/30 text-on-primary-container cursor-pointer transition-colors hover:border-outline/50">
                <span class="material-symbols-outlined text-sm mr-unit-sm text-on-primary-container">search</span>
                <input class="header-search-input bg-transparent border-none outline-none font-body-sm text-body-sm text-on-primary placeholder:text-on-primary-container w-full cursor-pointer" placeholder="Search projects, serial numbers, specs..." type="text">
                <span class="font-technical-tag text-technical-tag px-1.5 py-0.5 rounded bg-surface-container-high/10 text-on-primary-container border border-outline/30">Ctrl+K</span>
            </div>
        </div>
        <div class="flex items-center gap-2 sm:gap-4 lg:gap-unit-lg shrink-0">
            <div class="hidden md:flex items-center gap-unit-xs px-unit-sm py-1 rounded bg-primary font-technical-tag text-technical-tag text-on-primary ring-1 ring-white/10">
                <span class="w-2 h-2 rounded-full bg-tertiary-fixed animate-pulse"></span>
                <span class="text-on-primary-container">Telemetry Node:</span>
                <span class="text-tertiary-fixed">Online 99.98%</span>
            </div>
            <div id="header-bell-btn" class="relative flex items-center text-on-primary-container hover:text-on-primary cursor-pointer" title="Operational Telemetry Alerts">
                <span class="material-symbols-outlined">notifications</span>
                <span id="bell-unread-dot" class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-tertiary-fixed ring-2 ring-primary-container"></span>
            </div>
            <a class="flex items-center text-on-primary-container hover:text-on-primary" href="Documents.php" title="Technical Documentation">
                <span class="material-symbols-outlined">menu_book</span>
            </a>
            <div class="h-6 w-px bg-on-primary-container/30"></div>
            <div class="flex items-center gap-unit-sm cursor-pointer" id="header-profile-btn">
                <div class="flex flex-col text-right">
                    <span class="font-headline-sm text-headline-sm text-on-primary font-medium leading-none">Alexey R. Danilov</span>
                    <span class="font-technical-tag text-technical-tag text-on-primary-container mt-0.5">Chief Instrumentation Eng.</span>
                </div>
                <img alt="Alexey R. Danilov Profile" class="w-8 h-8 rounded-full object-cover ring-1 ring-tertiary-fixed/50" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBxrM-O7aJYHYCDtkoA3WwbiOe6BxJ0vK7AcnogxwZN9MACsknTlpyGKyy-lWl2Hwn9IEZLPDCvVGrmxN2kvPEfzbJ5E4u5x6-38EP2exwXW8Dmm-7oMTzMG07_rmRLbT0xvZwQMFEwa4qJO5LcWbn58eWx3fSkVjAmSI3UWO8dCTgRg6GBgrY_MTUl-JF-JUf4K5CGPp0o4tvKoxbSqSysGT8r3j8de3w_sfk4F8p9ysiXXfbUkWPV">
            </div>
            <a href="./api/logout.php?redirect=../Customer%20Portal/login.php" class="top-signout-btn" title="Sign Out of Customer Portal" onclick="(function(){sessionStorage.clear();localStorage.clear();})()"><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
        </div>
    </header>

    <aside id="portal-sidebar" class="fixed left-0 top-16 bottom-0 w-64 bg-primary-container z-40 flex flex-col justify-between shadow-sm">
        <div class="py-unit-md">
            <div class="px-unit-base mb-unit-sm font-label-caps text-label-caps text-on-primary-container uppercase tracking-wider flex items-center justify-between">
                <span>Operational Navigation</span>
                <button id="sidebar-collapse-btn" class="text-on-primary-container hover:text-on-primary p-0.5 rounded hover:bg-surface-container-high/10 transition-colors cursor-pointer" title="Collapse Menu">
                    <span class="material-symbols-outlined text-base">chevron_left</span>
                </button>
            </div>
            <nav class="flex flex-col gap-0.5" data-active-classes="bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-on-tertiary-container">
                <a class="flex items-center gap-unit-sm px-unit-base py-unit-sm text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary transition-colors font-headline-sm text-headline-sm font-normal" data-path="dashboard" href="Dashboard.php">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <rect height="7" rx="1" width="7" x="3" y="3"></rect>
                        <rect height="7" rx="1" width="7" x="14" y="3"></rect>
                        <rect height="7" rx="1" width="7" x="14" y="14"></rect>
                        <rect height="7" rx="1" width="7" x="3" y="14"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a aria-current="page" class="flex items-center gap-unit-sm px-unit-base py-unit-sm transition-colors bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-on-tertiary-container font-headline-sm text-headline-sm" data-path="orders" href="Orders.php">
                    <svg class="w-4 h-4 shrink-0 text-tertiary-fixed" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                        <path d="M12 22V12"></path>
                    </svg>
                    <span>Orders</span>
                </a>
                <a class="flex items-center gap-unit-sm px-unit-base py-unit-sm text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary transition-colors font-headline-sm text-headline-sm font-normal" data-path="projects" href="ProjectListAndDetail.php">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <rect height="18" rx="2" width="18" x="3" y="3"></rect>
                        <path d="M3 9h18"></path>
                        <path d="M9 21V9"></path>
                    </svg>
                    <span>Projects</span>
                </a>
                <a class="flex items-center gap-unit-sm px-unit-base py-unit-sm text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary transition-colors font-headline-sm text-headline-sm font-normal" data-path="invoices" href="Invoices.php">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"></path>
                        <path d="M8 7h8"></path>
                        <path d="M8 11h8"></path>
                        <path d="M8 15h5"></path>
                    </svg>
                    <span>Invoices</span>
                </a>
                <a class="flex items-center gap-unit-sm px-unit-base py-unit-sm text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary transition-colors font-headline-sm text-headline-sm font-normal" data-path="documents" href="Documents.php">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"></path>
                    </svg>
                    <span>Documents</span>
                </a>
                <a class="flex items-center gap-unit-sm px-unit-base py-unit-sm text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary transition-colors font-headline-sm text-headline-sm font-normal" data-path="support" href="SupportTicketView.php">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                        <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path>
                    </svg>
                    <span>Support</span>
                </a>
                <a class="flex items-center gap-unit-sm px-unit-base py-unit-sm text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary transition-colors font-headline-sm text-headline-sm font-normal" data-path="account-settings" href="AccountSettings.php">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <span>Account Settings</span>
                </a>
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

                <div class="flex flex-col gap-unit-xs">
                    <div class="flex items-center gap-unit-xs font-technical-tag text-technical-tag text-on-surface-variant uppercase tracking-wider">
                        <span>Enterprise Portal</span>
                        <span class="text-outline-variant">/</span>
                        <span>Procurement &amp; Supply</span>
                        <span class="text-outline-variant">/</span>
                        <span class="text-on-surface font-semibold">Orders</span>
                    </div>
                    <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-unit-base">
                        <div>
                            <div class="flex items-center gap-unit-sm">
                                <h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Equipment Orders &amp; Procurement Ledger</h1>
                                <span class="px-2 py-0.5 rounded bg-surface-container-highest text-secondary font-technical-tag text-technical-tag font-semibold">LEDGER LIVE</span>
                            </div>
                            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                                Tracking physical shipments, manufacturing stages, and customs clearance for 
                                <span class="font-semibold text-on-surface">Severstal Metallurgy Plant #4</span>
                            </p>
                        </div>
                        <div class="flex items-center gap-unit-sm shrink-0">
                            <button class="flex items-center gap-1.5 px-unit-md py-2 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-headline-sm text-headline-sm font-medium shadow-sm transition-colors" onclick="window.showToast('Manifest Exported', 'Equipment orders CSV ledger downloaded.', 'success')" type="button">
                                <span class="material-symbols-outlined text-base text-secondary">file_download</span>
                                Export Manifest (.CSV)
                            </button>
                            <button class="flex items-center gap-1.5 px-unit-md py-2 rounded bg-tertiary-fixed-dim hover:bg-on-tertiary-container text-on-tertiary-fixed font-headline-sm text-headline-sm font-semibold shadow-sm transition-all" onclick="window.showDispatchModal()" type="button">
                                <span class="material-symbols-outlined text-base text-on-tertiary-fixed">bolt</span>
                                Request Expedited Dispatch
                            </button>
                        </div>
                    </div>
                </div>

                <!-- DYNAMIC KPI CARDS -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-unit-base">
                    <div class="bg-surface-container-lowest p-unit-base rounded shadow-sm relative overflow-hidden flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-unit-xs">
                            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Active Orders</span>
                            <span class="material-symbols-outlined text-secondary text-lg">local_shipping</span>
                        </div>
                        <div>
                            <div class="font-data-mono-lg text-display-lg text-on-surface font-bold leading-none mb-1" id="kpi-active-orders">
                                <?= (int)$kpis['total_orders'] ?> Orders
                            </div>
                            <div class="flex items-center gap-unit-xs font-technical-tag text-technical-tag text-on-surface-variant">
                                <span class="text-secondary font-semibold"><?= (int)$kpis['in_transit'] ?></span> in transit
                                <span class="text-outline-variant">•</span>
                                <span class="text-on-surface font-semibold"><?= (int)$kpis['in_mfg'] ?></span> in manufacturing
                            </div>
                        </div>
                        <div class="mt-unit-sm w-full bg-surface-container-high h-1 rounded overflow-hidden flex">
                            <div class="bg-secondary h-full w-1/3"></div>
                            <div class="bg-primary-container h-full w-2/3"></div>
                        </div>
                    </div>

                    <div class="bg-surface-container-lowest p-unit-base rounded shadow-sm relative overflow-hidden flex flex-col justify-between">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-on-tertiary-container"></div>
                        <div class="flex items-center justify-between mb-unit-xs pl-unit-xs">
                            <div class="flex items-center gap-1">
                                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Total Value on Order</span>
                                <span class="px-1 py-0.2 rounded bg-error-container text-error font-technical-tag text-technical-tag uppercase">Confidential</span>
                            </div>
                            <span class="material-symbols-outlined text-on-tertiary-container text-lg">payments</span>
                        </div>
                        <div class="pl-unit-xs">
                            <div class="font-data-mono-lg text-display-lg text-on-surface font-bold leading-none mb-1 tracking-tight" id="kpi-total-value">
                                $<?= number_format((float)$kpis['total_value'], 2) ?>
                            </div>
                            <div class="flex items-center gap-unit-xs font-technical-tag text-technical-tag text-on-surface-variant">
                                <span class="text-secondary font-semibold">USD</span> currency baseline
                                <span class="text-outline-variant">•</span>
                                <span class="text-on-tertiary-container font-semibold">Active Ledger</span>
                            </div>
                        </div>
                        <div class="mt-unit-sm w-full bg-surface-container-high h-1 rounded overflow-hidden pl-unit-xs">
                            <div class="bg-on-tertiary-container h-full w-[78%]"></div>
                        </div>
                    </div>

                    <div class="bg-surface-container-lowest p-unit-base rounded shadow-sm relative overflow-hidden flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-unit-xs">
                            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Customs Status</span>
                            <span class="material-symbols-outlined text-secondary text-lg">verified</span>
                        </div>
                        <div>
                            <div class="font-headline-lg text-display-lg text-on-surface font-bold leading-none mb-1">100% On-Track</div>
                            <div class="flex items-center gap-unit-xs font-technical-tag text-technical-tag text-on-surface-variant">
                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-tertiary-fixed-dim"></span>
                                <span>0 Active Customs Holds</span>
                                <span class="text-outline-variant">•</span>
                                <span>Pulkovo / Domodedovo</span>
                            </div>
                        </div>
                        <div class="mt-unit-sm flex items-center justify-between font-technical-tag text-technical-tag text-secondary">
                            <span>Cleared past 30d: 9 Consignments</span>
                            <span class="font-bold">Avg 18h</span>
                        </div>
                    </div>

                    <div class="bg-surface-container-lowest p-unit-base rounded shadow-sm relative overflow-hidden flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-unit-xs">
                            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Critical Spares Buffer</span>
                            <span class="material-symbols-outlined text-secondary text-lg">timer</span>
                        </div>
                        <div>
                            <div class="flex items-baseline gap-2">
                                <span class="font-data-mono-lg text-display-lg text-on-surface font-bold leading-none">4</span>
                                <span class="font-headline-md text-headline-md text-on-surface-variant font-semibold">Days to SLA</span>
                            </div>
                            <div class="flex items-center gap-unit-xs font-technical-tag text-technical-tag text-on-surface-variant mt-1">
                                <span>Scheduled air freight to Cherepovets</span>
                            </div>
                        </div>
                        <div class="mt-unit-sm w-full bg-surface-container-high h-1 rounded overflow-hidden flex">
                            <div class="bg-error h-full w-1/4"></div>
                            <div class="bg-surface-variant h-full w-3/4"></div>
                        </div>
                    </div>
                </div>

                <!-- DYNAMIC ACTIVE ORDER BANNER -->
                <div class="bg-surface-container-lowest rounded shadow-md overflow-hidden" id="active-order-banner">
                    <?php if ($activeOrder): ?>
                        <div class="bg-primary-container px-unit-lg py-unit-base text-on-primary flex flex-col lg:flex-row lg:items-center justify-between gap-unit-base">
                            <div class="flex items-center gap-unit-base">
                                <div class="w-10 h-10 rounded bg-surface-container-high/10 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-tertiary-fixed text-2xl">local_shipping</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-unit-sm">
                                        <span class="font-data-mono-lg text-data-mono-lg text-tertiary-fixed font-bold">ORD-<?= htmlspecialchars($activeOrder['order_id']) ?></span>
                                        <span class="px-2 py-0.5 rounded bg-tertiary-fixed/20 text-tertiary-fixed font-technical-tag text-technical-tag font-semibold tracking-wider uppercase"><?= htmlspecialchars($activeOrder['status']) ?></span>
                                    </div>
                                    <p class="font-headline-sm text-headline-sm text-on-primary font-medium mt-0.5">
                                        <?= htmlspecialchars($activeOrder['equipment_summary'] ?? 'Industrial Equipment Package') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-unit-sm shrink-0">
                                <button class="flex items-center gap-1.5 px-unit-md py-1.5 rounded bg-surface-container-high/10 hover:bg-surface-container-high/20 text-on-primary font-body-sm text-body-sm transition-colors" onclick="window.previewDocument('BOL-<?= htmlspecialchars($activeOrder['order_id']) ?>.PDF', 'Bill of Lading - ORD-<?= htmlspecialchars($activeOrder['order_id']) ?>', 'RZD FREIGHT EXPRESS')" type="button">
                                    <span class="material-symbols-outlined text-base">receipt_long</span>
                                    Bill of Lading
                                </button>
                                <button class="flex items-center gap-1.5 px-unit-md py-1.5 rounded bg-tertiary-fixed-dim hover:bg-on-tertiary-container text-on-tertiary-fixed font-body-sm text-body-sm font-semibold transition-colors" onclick="showLiveTelemetryModal('ORD-<?= htmlspecialchars($activeOrder['order_id']) ?>')" type="button">
                                    <span class="material-symbols-outlined text-base">my_location</span>
                                    Live Telemetry Link
                                </button>
                            </div>
                        </div>
                        <div class="p-unit-lg">
                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-unit-base mb-unit-lg">
                                <div class="flex flex-col gap-1">
                                    <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Destination Bay</span>
                                    <span class="font-headline-sm text-headline-sm text-on-surface font-semibold"><?= htmlspecialchars($activeOrder['facility_name'] ?? 'Primary Facility') ?></span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Order Date</span>
                                    <span class="font-headline-sm text-headline-sm text-on-surface font-semibold"><?= htmlspecialchars($activeOrder['order_date']) ?></span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Total Value</span>
                                    <span class="font-data-mono-md text-data-mono-md text-on-surface font-bold">$<?= number_format((float)$activeOrder['total_amount'], 2) ?></span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Current Status</span>
                                    <span class="font-headline-sm text-headline-sm text-secondary font-semibold"><?= htmlspecialchars($activeOrder['status']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="p-8 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl mb-2 text-outline">package_2</span>
                            <p class="font-medium text-body-md">No active shipments currently in transit for your account.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ORDERS LEDGER TABLE -->
                <div class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
                    <div class="p-unit-base bg-surface-container-low flex flex-col md:flex-row md:items-center justify-between gap-unit-base">
                        <div class="flex flex-wrap items-center gap-unit-sm flex-1">
                            <div class="flex items-center bg-surface-container-lowest px-unit-md py-1.5 rounded shadow-sm text-on-surface w-72">
                                <span class="material-symbols-outlined text-sm mr-unit-sm text-secondary">filter_alt</span>
                                <input class="bg-transparent border-none outline-none font-body-sm text-body-sm text-on-surface placeholder:text-outline w-full" id="tableSearch" onkeyup="filterOrders()" placeholder="Search Order ID, PO Ref, Specification..." type="text">
                            </div>
                            <select class="bg-surface-container-lowest px-unit-md py-1.5 rounded shadow-sm text-on-surface font-body-sm text-body-sm outline-none cursor-pointer" id="statusFilter" onchange="filterOrders()">
                                <option value="ALL">All Delivery Statuses</option>
                                <option value="TRANSIT">In Transit / En Route</option>
                                <option value="MFG">Manufacturing / FAT</option>
                                <option value="DELIVERED">Delivered &amp; Inspected</option>
                                <option value="CUSTOMS">Customs Pending</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-unit-xs text-on-surface-variant font-technical-tag text-technical-tag self-end md:self-auto">
                            <span>Showing <strong class="text-on-surface" id="recordCount"><?= count($orders) ?></strong> active contracts</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="ordersTable">
                            <thead>
                                <tr class="bg-surface-container font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider select-none">
                                    <th class="py-2.5 px-unit-base">Order ID</th>
                                    <th class="py-2.5 px-unit-base">Equipment &amp; Specification</th>
                                    <th class="py-2.5 px-unit-base">Destination Facility</th>
                                    <th class="py-2.5 px-unit-base">Status</th>
                                    <th class="py-2.5 px-unit-base text-right">Order Total (USD)</th>
                                    <th class="py-2.5 px-unit-base">Order Date</th>
                                    <th class="py-2.5 px-unit-base text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-0 text-on-surface font-body-md text-body-md" id="orders-table-body">
                                <?php if (!empty($orders)): ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr class="hover:bg-surface-container-low transition-colors group relative bg-surface-container-lowest">
                                            <td class="py-3 px-unit-base font-data-mono-md text-data-mono-md font-bold text-on-surface">
                                                ORD-<?= htmlspecialchars($order['order_id']) ?>
                                            </td>
                                            <td class="py-3 px-unit-base max-w-xs">
                                                <div class="font-headline-sm text-headline-sm font-semibold truncate text-on-surface">
                                                    <?= htmlspecialchars($order['equipment_summary'] ?? 'Standard Equipment Package') ?>
                                                </div>
                                            </td>
                                            <td class="py-3 px-unit-base">
                                                <div class="font-body-md text-body-md font-medium text-on-surface">
                                                    <?= htmlspecialchars($order['facility_name'] ?? 'Primary Facility') ?>
                                                </div>
                                            </td>
                                            <td class="py-3 px-unit-base">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-tertiary-fixed/30 text-on-tertiary-fixed-variant font-technical-tag text-technical-tag font-bold uppercase">
                                                    <?= htmlspecialchars($order['status']) ?>
                                                </span>
                                            </td>
                                            <td class="py-3 px-unit-base text-right font-data-mono-md text-data-mono-md font-bold text-on-surface">
                                                $<?= number_format((float)$order['total_amount'], 2) ?>
                                            </td>
                                            <td class="py-3 px-unit-base font-headline-sm text-headline-sm text-on-surface">
                                                <?= htmlspecialchars($order['order_date']) ?>
                                            </td>
                                            <td class="py-3 px-unit-base text-right">
                                                <div class="flex items-center justify-end gap-unit-xs">
                                                    <button class="px-2 py-1 rounded bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-technical-tag text-technical-tag font-medium transition-colors" onclick="showLiveTelemetryModal('ORD-<?= htmlspecialchars($order['order_id']) ?>')" type="button">Track</button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-12 text-on-surface-variant">
                                            <span class="material-symbols-outlined text-4xl block mb-2 text-outline">inbox</span>
                                            No procurement records found in database. New entries will update this ledger automatically.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>

</html>