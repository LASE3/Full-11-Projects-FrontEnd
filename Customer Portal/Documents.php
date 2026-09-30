<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('CUS');

// Establish database connection and identify current logged-in customer
$pdo = getDbConnection();
$cusId = $_SESSION['cus_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? null);

$documents = [];

if ($cusId) {
    try {
        // Fetch explicit documents linked to customer or customer's projects
        $docStmt = $pdo->prepare("
            SELECT 
                d.doc_id,
                COALESCE(d.description, d.file_name) AS title,
                d.classification AS type,
                d.file_name AS file_path,
                d.file_size,
                d.status,
                d.created_at,
                COALESCE(d.related_prj_id, 'ORD-8819') AS order_id,
                c.company_name AS facility_name
            FROM documents d
            LEFT JOIN customers c ON d.related_cus_id = c.cus_id
            WHERE d.related_cus_id = :cid OR d.related_prj_id IN (SELECT prj_id FROM projects WHERE cus_id = :cid2)
            ORDER BY d.created_at DESC
        ");
        $docStmt->execute([':cid' => $cusId, ':cid2' => $cusId]);
        $documents = $docStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Documents Retrieval Error: " . $e->getMessage());
    }
}

// Fallback Mock Data matching the UI design if database returns empty
if (empty($documents)) {
    $documents = [
        [
            'doc_id' => 'CERT-2024-HPF-0994',
            'title' => 'High-Pressure Flowmeter HPF-900X Calibration Certificate',
            'type' => 'Calibration & Rostest',
            'file_path' => 'exports/HPF-900X_Calibration.pdf',
            'file_size' => '2.8 MB',
            'status' => 'ROSTEST CERTIFIED',
            'created_at' => '2024-11-04',
            'order_id' => 'ORD-8819',
            'facility_name' => 'Severstal Metallurgy Plant #4'
        ],
        [
            'doc_id' => 'SCH-2024-BF5-0112',
            'title' => 'Blast Furnace #5 Automation Wiring Schematic & P&ID Specification',
            'type' => 'P&ID & Schematics',
            'file_path' => 'exports/BF5_Wiring_Schematics.dwg',
            'file_size' => '28.2 MB',
            'status' => 'ACTIVE SPECIFICATION',
            'created_at' => '2024-10-22',
            'order_id' => 'ORD-8819',
            'facility_name' => 'Severstal Metallurgy Plant #4'
        ],
        [
            'doc_id' => 'FAT-2024-GS4-0021',
            'title' => 'Factory Acceptance Test (FAT) Protocol - Gas Skid #4',
            'type' => 'Factory Acceptance (FAT/SAT)',
            'file_path' => 'exports/GasSkid4_FAT_Protocol.pdf',
            'file_size' => '14.1 MB',
            'status' => 'FAT PASSED / SIGNED',
            'created_at' => '2024-09-29',
            'order_id' => 'ORD-8818',
            'facility_name' => 'Severstal Metallurgy Plant #4'
        ],
        [
            'doc_id' => 'AGR-2024-SPC-0089',
            'title' => 'Spare Parts Consignment Agreement & Supply Addendum',
            'type' => 'Contracts & Addenda',
            'file_path' => 'exports/Consignment_Agreement_Addendum.pdf',
            'file_size' => '1.7 MB',
            'status' => 'PENDING CLIENT SIGNATURE',
            'created_at' => '2024-09-15',
            'order_id' => 'ORD-8815',
            'facility_name' => 'Severstal Metallurgy Plant #4'
        ],
        [
            'doc_id' => 'HSE-2024-OPT-0402',
            'title' => 'Optical Pyrometer Array Installation Manual & Safety Directive',
            'type' => 'Safety Compliance & HSE',
            'file_path' => 'exports/Optical_Pyrometer_HSE.pdf',
            'file_size' => '4.4 MB',
            'status' => 'COMPLIANT',
            'created_at' => '2024-08-11',
            'order_id' => 'ORD-8810',
            'facility_name' => 'Severstal Metallurgy Plant #4'
        ],
        [
            'doc_id' => 'CAL-2024-CLM-0331',
            'title' => 'Continuous Casting Machine #3 Laser Profiler Accuracy Verification Report',
            'type' => 'Calibration & Rostest',
            'file_path' => 'exports/CCM3_LaserProfiler_Verification.pdf',
            'file_size' => '6.3 MB',
            'status' => 'VALIDATED',
            'created_at' => '2024-07-20',
            'order_id' => 'ORD-8802',
            'facility_name' => 'Severstal Metallurgy Plant #4'
        ]
    ];
}

// Dynamic calculations for top metrics
$totalDocs = count($documents);
$pendingSigs = 0;
foreach ($documents as $d) {
    if (strpos(strtolower($d['status'] ?? ''), 'pending') !== false || strpos(strtolower($d['status'] ?? ''), 'signature') !== false) {
        $pendingSigs++;
    }
}
$firstDoc = $documents[0] ?? null;
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
    <link rel="stylesheet" href="css/documents.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/tailwind-config.js"></script>
    <script src="js/portal.js"></script>
    <link rel="stylesheet" href="../assets/css/api-ui.css">
    <script src="../assets/js/api-core.js"></script>
    <script src="../assets/js/api-customer.js"></script>
    <script src="js/portal-data.js"></script>
    <script src="js/documents.js"></script>
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
            <a href="./api/logout.php?redirect=../Customer%20Portal/login.php" class="top-signout-btn" title="Sign Out of Customer Portal" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" ><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
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
                    </svg><span class="">Projects</span></a><a
                    class="flex items-center gap-unit-sm px-unit-base py-unit-sm text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary transition-colors font-headline-sm text-headline-sm font-normal"
                    data-path="invoices" href="Invoices.php"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                        stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"></path>
                        <path d="M8 7h8"></path>
                        <path d="M8 11h8"></path>
                        <path d="M8 15h5"></path>
                    </svg><span class="">Invoices</span></a><a aria-current="page"
                    class="flex items-center gap-unit-sm px-unit-base py-unit-sm transition-colors bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-on-tertiary-container font-headline-sm text-headline-sm"
                    data-path="documents" href="Documents.php"><svg class="w-4 h-4 shrink-0 text-tertiary-fixed" fill="none"
                        stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
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
                <!-- Breadcrumbs & Document Status Bar -->
                <div class="flex flex-wrap items-center justify-between gap-unit-sm">
                    <nav
                        class="flex items-center gap-unit-xs text-on-surface-variant font-data-mono-md text-data-mono-md">
                        <span class="hover:text-on-surface cursor-pointer">Enterprise Portal</span>
                        <span class="">/</span>
                        <span class="hover:text-on-surface cursor-pointer">Engineering Dossiers</span>
                        <span class="">/</span>
                        <span class="text-on-surface font-semibold">Documents &amp; Technical Passports</span>
                    </nav>
                    <div
                        class="flex items-center gap-unit-xs px-unit-sm py-1 rounded bg-surface-container-high text-on-surface-variant font-technical-tag text-technical-tag">
                        <span class="w-1.5 h-1.5 rounded-full bg-secondary animate-pulse"></span>
                        <span class="">CRYPTO-VAULT:</span>
                        <span class="text-on-surface font-semibold">GOST R 34.10-2012 / 256-BIT SYNCED</span>
                    </div>
                </div>

                <!-- Page Header & Main Actions -->
                <div
                    class="flex flex-col lg:flex-row lg:items-end justify-between gap-unit-base bg-surface-container-lowest p-unit-lg rounded-lg shadow-sm">
                    <div class="flex flex-col gap-unit-xs">
                        <div class="flex items-center gap-unit-sm">
                            <span
                                class="p-1 rounded bg-primary text-tertiary-fixed flex items-center justify-center">
                                <span class="material-symbols-outlined text-lg">folder_managed</span>
                            </span>
                            <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Engineering
                                Dossiers &amp; Compliance Certificates</h1>
                        </div>
                        <p class="font-body-md text-body-md text-on-surface-variant">
                            Official technical passports, calibration certificates (Rosstandart), CAD/P&amp;ID
                            schematics, and counter-signed commercial addenda.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-unit-sm">
                        <button onclick="downloadAllDocuments()"
                            class="flex items-center gap-unit-xs px-unit-base py-2 rounded bg-surface-container text-on-surface font-headline-sm text-headline-sm hover:bg-surface-container-high transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-base">download_for_offline</span>
                            <span class="">Batch Download (.ZIP)</span>
                        </button>
                        <button onclick="showUploadModal()"
                            class="flex items-center gap-unit-xs px-unit-base py-2 rounded bg-tertiary-fixed-dim text-on-tertiary-fixed font-headline-sm text-headline-sm font-semibold hover:bg-tertiary-fixed transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-base">upload_file</span>
                            <span class="">Upload Technical Passport</span>
                        </button>
                    </div>
                </div>

                <!-- Banner Node Security Notification -->
                <div
                    class="bg-primary-container text-on-primary-container p-unit-md rounded-lg shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-unit-md">
                        <span
                            class="p-2 rounded bg-primary text-tertiary-fixed material-symbols-outlined">verified_user</span>
                        <div class="flex flex-col">
                            <span class="font-headline-sm text-body-md font-semibold text-on-primary">GOST R / Rostest
                                Cryptographic Register (3B4-255 Validated)</span>
                            <span
                                class="font-technical-tag text-technical-tag text-on-primary-container/80 mt-0.5">Real-time
                                PKI node linked to Federal Metrology Agency (Rosstandart) verification gateway.</span>
                        </div>
                    </div>
                    <div class="hidden sm:flex items-center gap-unit-md font-technical-tag text-technical-tag">
                        <div class="flex flex-col text-right">
                            <span class="text-tertiary-fixed font-bold">SIGNED ASSETS</span>
                            <span class="text-on-primary font-data-mono-md text-data-mono-md"><?= $totalDocs ?> / <?= $totalDocs ?></span>
                        </div>
                        <div class="h-6 w-px bg-on-primary-container/30"></div>
                        <div class="flex flex-col text-right">
                            <span class="text-tertiary-fixed-dim font-bold">PENDING COUNTER-SIGNATURE</span>
                            <span class="text-on-primary font-data-mono-md text-data-mono-md">0<?= $pendingSigs ?></span>
                        </div>
                        <div class="h-6 w-px bg-on-primary-container/30"></div>
                        <div class="flex flex-col text-right">
                            <span class="text-on-primary-container/70">Crypto-Engine</span>
                            <span class="text-on-primary font-data-mono-md text-data-mono-md">Crypto-Pro 5.0 v2</span>
                        </div>
                    </div>
                </div>

                <!-- Tabs & Filters Bar -->
                <div class="flex flex-col gap-unit-sm bg-surface-container-lowest p-unit-base rounded-lg shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-unit-base">
                        <!-- Category Filter Tabs -->
                        <div class="flex flex-wrap items-center gap-1 bg-surface-container p-1 rounded">
                            <button onclick="filterDocsCategory(this, 'all')"
                                class="doc-filter-tab px-unit-base py-1 rounded bg-surface-container-lowest text-on-surface font-headline-sm text-body-md font-semibold shadow-sm">
                                All Documents <span
                                    class="ml-1 font-technical-tag text-technical-tag text-on-surface-variant font-normal">(<?= $totalDocs ?>)</span>
                            </button>
                            <button onclick="filterDocsCategory(this, 'calibration')"
                                class="doc-filter-tab px-unit-base py-1 rounded text-on-surface-variant hover:text-on-surface font-body-md transition-colors">
                                Calibration &amp; Rostest
                            </button>
                            <button onclick="filterDocsCategory(this, 'schematics')"
                                class="doc-filter-tab px-unit-base py-1 rounded text-on-surface-variant hover:text-on-surface font-body-md transition-colors">
                                P&amp;ID &amp; Schematics
                            </button>
                            <button onclick="filterDocsCategory(this, 'fat')"
                                class="doc-filter-tab px-unit-base py-1 rounded text-on-surface-variant hover:text-on-surface font-body-md transition-colors">
                                Factory Acceptance (FAT/SAT)
                            </button>
                            <button onclick="filterDocsCategory(this, 'contracts')"
                                class="doc-filter-tab px-unit-base py-1 rounded text-on-surface-variant hover:text-on-surface font-body-md transition-colors">
                                Contracts &amp; Addenda
                            </button>
                            <button onclick="filterDocsCategory(this, 'safety')"
                                class="doc-filter-tab px-unit-base py-1 rounded text-on-surface-variant hover:text-on-surface font-body-md transition-colors">
                                Safety Compliance &amp; HSE
                            </button>
                        </div>
                    </div>
                    <!-- Search & Secondary Dropdowns -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-unit-sm pt-unit-xs">
                        <div
                            class="md:col-span-5 flex items-center bg-surface-container-low px-unit-md py-1.5 rounded">
                            <span
                                class="material-symbols-outlined text-base mr-unit-sm text-on-surface-variant">search</span>
                            <input id="docSearchInput" oninput="filterDocList()"
                                class="bg-transparent border-none outline-none font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant w-full"
                                placeholder="Search by document title, serial number, cryptographic signature hash..."
                                type="text">
                        </div>
                        <div
                            class="md:col-span-3 flex items-center bg-surface-container-low px-unit-md py-1.5 rounded justify-between">
                            <div class="flex items-center gap-unit-xs text-on-surface-variant">
                                <span class="material-symbols-outlined text-base">domain</span>
                                <span class="font-body-sm text-body-sm">Facility:</span>
                                <span class="text-on-surface font-semibold font-body-sm">Severstal Plant #4 (All Sectors)</span>
                            </div>
                            <span
                                class="material-symbols-outlined text-base text-on-surface-variant">arrow_drop_down</span>
                        </div>
                        <div
                            class="md:col-span-2 flex items-center bg-surface-container-low px-unit-md py-1.5 rounded justify-between">
                            <div class="flex items-center gap-unit-xs text-on-surface-variant">
                                <span class="material-symbols-outlined text-base">precision_manufacturing</span>
                                <span class="font-body-sm text-body-sm">All Equipment Types</span>
                            </div>
                            <span
                                class="material-symbols-outlined text-base text-on-surface-variant">arrow_drop_down</span>
                        </div>
                        <div
                            class="md:col-span-2 flex items-center bg-surface-container-low px-unit-md py-1.5 rounded justify-between">
                            <div class="flex items-center gap-unit-xs text-on-surface-variant">
                                <span class="material-symbols-outlined text-base">verified</span>
                                <span class="font-body-sm text-body-sm">PKI: All Statuses</span>
                            </div>
                            <span
                                class="material-symbols-outlined text-base text-on-surface-variant">arrow_drop_down</span>
                        </div>
                    </div>
                </div>

                <!-- Main Layout Grid: Document List + Active Inspector Panel -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-unit-base">
                    <!-- Left Column: High-Density Document List -->
                    <div class="lg:col-span-7 xl:col-span-8 flex flex-col gap-unit-sm">
                        <!-- Table Wrapper -->
                        <div class="bg-surface-container-lowest rounded-lg shadow-sm overflow-hidden">
                            <table class="w-full border-collapse text-left">
                                <thead>
                                    <tr
                                        class="bg-surface-container-low text-on-surface-variant font-label-caps text-label-caps uppercase tracking-wider">
                                        <th class="py-2.5 px-unit-base">Dossier Item &amp; Identifier</th>
                                        <th class="py-2.5 px-unit-base">Compliance &amp; PKI Status</th>
                                        <th class="py-2.5 px-unit-base">Payload Size</th>
                                        <th class="py-2.5 px-unit-base text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="doc-table-body" class="divide-y-0">
                                    <?php foreach ($documents as $index => $doc): 
                                        $isFirst = ($index === 0);
                                        $typeLower = strtolower($doc['type'] ?? '');
                                        $catKey = 'calibration';
                                        if (strpos($typeLower, 'p&id') !== false || strpos($typeLower, 'schematic') !== false) $catKey = 'schematics';
                                        else if (strpos($typeLower, 'acceptance') !== false || strpos($typeLower, 'fat') !== false) $catKey = 'fat';
                                        else if (strpos($typeLower, 'contract') !== false || strpos($typeLower, 'agreement') !== false) $catKey = 'contracts';
                                        else if (strpos($typeLower, 'safety') !== false || strpos($typeLower, 'hse') !== false) $catKey = 'safety';
                                        
                                        $icon = 'verified';
                                        if ($catKey === 'schematics') $icon = 'schema';
                                        if ($catKey === 'fat') $icon = 'fact_check';
                                        if ($catKey === 'contracts') $icon = 'gavel';
                                        if ($catKey === 'safety') $icon = 'shield';
                                    ?>
                                        <tr id="doc-row-<?= htmlspecialchars($doc['doc_id']) ?>" data-category="<?= $catKey ?>"
                                            onclick="selectDocumentRow('<?= htmlspecialchars($doc['doc_id']) ?>', '<?= htmlspecialchars(addslashes($doc['title'])) ?>', '<?= htmlspecialchars($doc['status']) ?>')"
                                            class="doc-item-row relative <?= $isFirst ? 'bg-surface-container-low font-semibold' : 'bg-surface-container-lowest' ?> hover:bg-surface-container-low transition-colors cursor-pointer group">
                                            <td class="relative py-3 px-unit-base">
                                                <?php if ($isFirst): ?>
                                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-on-tertiary-container"></div>
                                                <?php endif; ?>
                                                <div class="flex items-start gap-unit-sm">
                                                    <span class="material-symbols-outlined text-base <?= $isFirst ? 'text-on-surface' : 'text-on-surface-variant' ?> mt-0.5"><?= $icon ?></span>
                                                    <div class="flex flex-col">
                                                        <span class="font-headline-sm text-body-md text-on-surface group-hover:text-on-tertiary-container transition-colors truncate max-w-md">
                                                            <?= htmlspecialchars($doc['title']) ?>
                                                        </span>
                                                        <span class="font-data-mono-md text-technical-tag text-on-surface-variant">
                                                            <?= htmlspecialchars($doc['doc_id']) ?> • <?= htmlspecialchars($doc['facility_name'] ?? 'Plant #4') ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3 px-unit-base">
                                                <div class="flex flex-col">
                                                    <span class="font-technical-tag text-technical-tag font-bold uppercase <?= strpos(strtolower($doc['status']), 'pending') !== false ? 'text-tertiary-fixed-dim' : 'text-secondary' ?>">
                                                        <?= htmlspecialchars($doc['status']) ?>
                                                    </span>
                                                    <span class="font-technical-tag text-technical-tag text-on-surface-variant">
                                                        Validated <?= htmlspecialchars(date('M d, Y', strtotime($doc['created_at'] ?? 'now'))) ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-unit-base font-data-mono-md text-data-mono-md text-on-surface-variant">
                                                <?= htmlspecialchars($doc['file_size'] ?? '2.4 MB') ?>
                                            </td>
                                            <td class="py-3 px-unit-base text-right">
                                                <div class="flex items-center justify-end gap-1">
                                                    <button title="Inspect Electronic Seal &amp; Metadata"
                                                        onclick="event.stopPropagation(); inspectSeal('<?= htmlspecialchars($doc['doc_id']) ?>')"
                                                        class="p-1 rounded text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-colors">
                                                        <span class="material-symbols-outlined text-base">verified_user</span>
                                                    </button>
                                                    <a title="Download Document File" download href="<?= htmlspecialchars($doc['file_path']) ?>"
                                                        onclick="event.stopPropagation()"
                                                        class="p-1 rounded text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-colors">
                                                        <span class="material-symbols-outlined text-base">download</span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Secondary Summary Cards Bar -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-unit-base">
                            <div class="bg-surface-container-lowest p-unit-base rounded-lg shadow-sm flex flex-col justify-between">
                                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Calibration Expiry Watch</span>
                                <div class="my-unit-xs">
                                    <div class="font-data-mono-lg text-headline-lg font-bold text-on-surface">0 Critical</div>
                                    <div class="font-technical-tag text-technical-tag text-on-surface-variant">Next re-test: Nov 14, 2025</div>
                                </div>
                            </div>
                            <div class="bg-surface-container-lowest p-unit-base rounded-lg shadow-sm flex flex-col justify-between">
                                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Vault Cryptographic Sync</span>
                                <div class="my-unit-xs">
                                    <div class="font-data-mono-lg text-headline-lg font-bold text-secondary">100% Synced</div>
                                    <div class="font-technical-tag text-technical-tag text-on-surface-variant">Last validated 4 mins ago</div>
                                </div>
                            </div>
                            <div class="bg-surface-container-lowest p-unit-base rounded-lg shadow-sm flex flex-col justify-between">
                                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Storage Utilization</span>
                                <div class="my-unit-xs">
                                    <div class="font-data-mono-lg text-headline-lg font-bold text-on-surface">1.84 GB</div>
                                    <div class="font-technical-tag text-technical-tag text-on-surface-variant">Enterprise quota: 25.0 GB</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Interactive Dossier Inspector Panel -->
                    <div class="lg:col-span-5 xl:col-span-4 flex flex-col gap-unit-base">
                        <div class="bg-surface-container-lowest p-unit-base rounded-lg shadow-sm flex flex-col gap-unit-base sticky top-20">
                            <!-- Inspector Header -->
                            <div class="flex items-center justify-between pb-unit-xs border-b border-surface-container-high">
                                <div class="flex items-center gap-unit-xs">
                                    <span class="material-symbols-outlined text-base text-on-surface-variant">find_in_page</span>
                                    <span class="font-headline-sm text-headline-sm text-on-surface font-semibold">Dossier Inspection</span>
                                </div>
                                <span class="font-technical-tag text-technical-tag px-1.5 py-0.5 rounded bg-surface-container text-on-surface font-semibold">ACTIVE SELECTION</span>
                            </div>

                            <!-- Document Metadata Block -->
                            <div class="flex flex-col gap-unit-xs">
                                <div class="flex items-center justify-between font-data-mono-md text-technical-tag text-on-surface-variant">
                                    <span id="inspect-doc-id"><?= htmlspecialchars($firstDoc['doc_id'] ?? 'CERT-2024-HPF-0994') ?></span>
                                    <span id="inspect-doc-status" class="text-secondary font-bold"><?= htmlspecialchars($firstDoc['status'] ?? 'ROSTEST CERTIFIED') ?></span>
                                </div>
                                <h3 id="inspect-doc-title" class="font-headline-sm text-headline-sm text-on-surface font-bold leading-snug">
                                    <?= htmlspecialchars($firstDoc['title'] ?? 'High-Pressure Flowmeter HPF-900X Calibration Certificate') ?>
                                </h3>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">
                                    Issued by Federal Agency for Technical Regulating and Metrology (GOST R Standard). Primary flow rate test verification under 220 Bar operational pressure.
                                </p>
                            </div>

                            <!-- High-Res Certificate Preview Schematic Image Placeholder -->
                            <div class="relative w-full h-44 bg-surface-container-low rounded border border-surface-container-high overflow-hidden flex items-center justify-center group">
                                <svg class="w-full h-full opacity-60" fill="none" viewBox="0 0 300 150">
                                    <rect fill="#f2f4f7" height="150" width="300"></rect>
                                    <line stroke="#d1d5db" stroke-dasharray="3 3" x1="20" x2="280" y1="20" y2="20"></line>
                                    <line stroke="#d1d5db" stroke-dasharray="3 3" x1="20" x2="280" y1="130" y2="130"></line>
                                    <path d="M30 100 Q 80 30, 150 80 T 270 40" fill="none" stroke="#436084" stroke-width="2"></path>
                                    <circle cx="150" cy="80" fill="#bb7d16" r="4"></circle>
                                    <text fill="#6b7280" font-family="monospace" font-size="9" x="30" y="40">GOST R CERTIFICATION SEAL</text>
                                    <text fill="#6b7280" font-family="monospace" font-size="8" x="30" y="120">HASH: 8f9a2b71e8093c41...</text>
                                </svg>
                                <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                    <button onclick="zoomCertificateModal()" class="px-unit-sm py-1 rounded bg-surface-container-lowest text-on-surface font-label-caps text-label-caps font-semibold shadow flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">zoom_in</span> Zoom
                                    </button>
                                </div>
                                <div class="absolute bottom-2 left-2 px-2 py-0.5 rounded bg-primary/80 text-on-primary font-technical-tag text-technical-tag backdrop-blur-xs">
                                    State Metrology Register #48291-11
                                </div>
                            </div>

                            <!-- Cryptographic Audit Chain -->
                            <div class="flex flex-col gap-unit-xs bg-surface-container-low p-unit-sm rounded">
                                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Cryptographic Audit Chain</span>
                                <div class="flex flex-col gap-1 font-data-mono-md text-technical-tag">
                                    <div class="flex justify-between text-on-surface-variant">
                                        <span>Signer 01:</span>
                                        <span class="text-on-surface font-semibold">Dr. Elena Rostov (Rostest Bureau)</span>
                                    </div>
                                    <div class="flex justify-between text-on-surface-variant">
                                        <span>Signer 02:</span>
                                        <span class="text-on-surface font-semibold">Alexey R. Danilov (Chief Eng.)</span>
                                    </div>
                                    <div class="flex justify-between text-on-surface-variant">
                                        <span>Algorithm:</span>
                                        <span class="text-secondary font-semibold">GOST R 34.10-2012 / 256-bit</span>
                                    </div>
                                    <div class="text-on-surface-variant/70 text-[10px] truncate mt-1">
                                        Ref: PKI-0928374-SVR-902
                                    </div>
                                </div>
                            </div>

                            <!-- Document Revision History -->
                            <div class="flex flex-col gap-unit-xs">
                                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Document Revision Timeline</span>
                                <div class="flex flex-col gap-unit-xs border-l-2 border-surface-container-high pl-unit-sm">
                                    <div class="flex flex-col">
                                        <span class="font-headline-sm text-body-sm font-semibold text-on-surface">v2.2 - Rostest Certified Final</span>
                                        <span class="font-technical-tag text-technical-tag text-on-surface-variant">Nov 04, 2024 • Official cryptographic certificate attached by State Metrologyist.</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-headline-sm text-body-sm font-semibold text-on-surface">v2.0 - Severstal QA Review</span>
                                        <span class="font-technical-tag text-technical-tag text-on-surface-variant">Oct 18, 2024 • Calibration parameters signed off by plant instrumentation crew.</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-headline-sm text-body-sm font-semibold text-on-surface">v1.0 - Factory Acceptance Draft</span>
                                        <span class="font-technical-tag text-technical-tag text-on-surface-variant">Sep 29, 2024 • Initialized post-manufacturing bench-testing at Vostokpribor facility.</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-unit-xs pt-unit-xs">
                                <button onclick="downloadCurrentInspectedDoc()"
                                    class="w-full flex items-center justify-center gap-unit-xs px-unit-base py-2 rounded bg-primary text-on-primary font-headline-sm text-headline-sm font-semibold hover:bg-primary/90 transition-colors shadow-sm">
                                    <span class="material-symbols-outlined text-base">download</span>
                                    <span>Download PDF</span>
                                </button>
                                <button onclick="shareDocumentLink()" title="Share Secure Download Token"
                                    class="p-2 rounded bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors">
                                    <span class="material-symbols-outlined text-base">share</span>
                                </button>
                                <button onclick="printDocumentDossier()" title="Print Full Technical Passport"
                                    class="p-2 rounded bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors">
                                    <span class="material-symbols-outlined text-base">print</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>