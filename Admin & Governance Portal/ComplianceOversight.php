<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('ADM');
require_once __DIR__ . '/gov_service.php';

$currentUser = gov_getActiveUserProfile();
$compliance = gov_getComplianceOversight();
$metrics = gov_getGovernanceMetrics();
$controls = gov_getComplianceControls();

$reviews = $compliance['reviews'];
$incidents = $compliance['incidents'];

$totalControls = count($controls);
$compliantControls = count(array_filter($controls, fn($c) => $c['status'] === 'COMPLIANT'));
$deviationControls = count(array_filter($controls, fn($c) => $c['status'] === 'DEVIATION'));
$remediationControls = count(array_filter($controls, fn($c) => $c['status'] === 'REMEDIATION'));

$isoCount = count(array_filter($controls, fn($c) => stripos($c['framework'] ?? '', '27001') !== false || stripos($c['framework'] ?? '', 'ISO') !== false));
$kazCount = count(array_filter($controls, fn($c) => stripos($c['framework'] ?? '', 'KAZ') !== false || stripos($c['framework'] ?? '', 'ST RK') !== false));
$scadaCount = count(array_filter($controls, fn($c) => stripos($c['framework'] ?? '', 'SCADA') !== false || stripos($c['framework'] ?? '', 'IEC') !== false || stripos($c['framework'] ?? '', 'GOST') !== false || (stripos($c['framework'] ?? '', '27001') === false && stripos($c['framework'] ?? '', 'KAZ') === false)));

$totalReviews = count($reviews);
$compliantReviews = count(array_filter($reviews, fn($r) => stripos($r['action_taken'], 'Attested') !== false || stripos($r['action_taken'], 'Validated') !== false));
$deviationReviews = count(array_filter($reviews, fn($r) => stripos($r['finding'] ?? '', 'Breach') !== false || stripos($r['action_taken'] ?? '', 'Orphan') !== false || stripos($r['finding'] ?? '', 'Unlawful') !== false));
$remediationReviews = max(0, $totalReviews - $compliantReviews - $deviationReviews);
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>VOSTOKPRIBOR Administration &amp; Governance Portal - System 11</title>
    <link rel="stylesheet" href="css/common.css" />
    <link rel="stylesheet" href="css/complianceOversight.css" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="js/tailwind-config.js"></script>
</head>

<body class="bg-surface font-body-default text-on-surface antialiased">
    <header class="fixed top-0 left-0 right-0 z-50 bg-primary text-on-primary border-b-4 border-error">
        <div class="h-[60px] w-full px-gutter-desktop flex items-center justify-between gap-space-md">
            <div class="flex items-center gap-space-md"><img alt="VOSTOKPRIBOR System 11 Logo"
                    class="h-8 w-auto object-contain"
                    src="assets/logo.svg" />
                <div class="h-6 w-[1px] bg-outline-variant/40"></div>
                <div class="flex flex-col">
                    <div class="flex items-center gap-space-xs"><span
                            class="font-security-stamp text-security-stamp uppercase tracking-widest text-on-primary">SYSTEM
                            11 // GOV-CORE</span><span
                            class="px-space-xs py-[1px] bg-primary-container font-telemetry-micro text-telemetry-micro text-on-primary-container rounded">v11.4.2-PROD</span>
                    </div><span class="font-telemetry-micro text-telemetry-micro text-on-primary-container">AUTONOMOUS
                        TELEMETRY &amp; JURISDICTION MATRIX</span>
                </div>
            </div>
            <div class="hidden xl:flex items-center gap-space-md flex-1 max-w-2xl justify-center">
                <div
                    class="flex items-center gap-space-xs px-space-sm py-space-2xs bg-primary-container rounded border border-outline/30">
                    <div class="w-2 h-2 rounded-full bg-secondary-fixed animate-pulse"></div><span
                        class="font-security-stamp text-security-stamp text-secondary-fixed">CYBER-RANGE-PROD</span><span
                        class="text-outline-variant font-telemetry-micro text-telemetry-micro">|</span><span
                        class="font-telemetry-micro text-telemetry-micro text-on-primary-container font-medium">DEFCON-4
                        / NORMAL OPS</span>
                </div>
                <div class="relative flex-1 max-w-md"><span
                        class="material-symbols-outlined absolute left-space-sm top-1/2 -translate-y-1/2 text-[16px] text-on-primary-container">search</span><input
                        class="w-full h-control-height-sm bg-primary-container/80 pl-space-lg pr-space-sm font-telemetry-micro text-telemetry-micro text-on-primary placeholder:text-on-primary-container/70 rounded border border-outline/40 focus:outline-none focus:border-secondary-fixed-dim"
                        placeholder="Search access matrices, EMP ID, audit hashes (Ctrl + K)" readonly="" type="text" />
                </div>
            </div>
            <div class="flex items-center gap-space-md">
                <div
                    class="hidden lg:flex items-center gap-space-xs px-space-sm py-space-2xs bg-surface-container-lowest/10 rounded font-telemetry-micro text-telemetry-micro text-on-primary">
                    <span class="material-symbols-outlined text-[14px] text-secondary-fixed">hub</span><span>10/10
                        Ingestion Nodes Active</span>
                </div><button
                    class="relative p-space-xs text-on-primary hover:text-secondary-fixed transition-colors"><span
                        class="material-symbols-outlined text-[20px]">notifications</span><span
                        class="absolute top-0 right-0 w-4 h-4 bg-error text-on-error font-telemetry-micro text-[10px] leading-4 text-center font-bold rounded-full">3</span></button>
                <div class="h-6 w-[1px] bg-outline-variant/40"></div>
                <div class="flex items-center gap-space-sm">
                    <div class="flex flex-col text-right">
                        <div class="flex items-center justify-end gap-space-xs"><span
                                class="font-telemetry-micro text-telemetry-micro font-bold text-on-primary"><?= htmlspecialchars((string)($currentUser['full_name'] ?? 'System Administrator')) ?></span><span
                                class="font-label-uppercase text-label-uppercase text-secondary-fixed bg-secondary-container/20 px-space-2xs rounded"><?= htmlspecialchars((string)($currentUser['user_id'] ?? ($currentUser['emp_id'] ?? 'EMP-0001'))) ?></span>
                        </div><span class="font-telemetry-micro text-[10px] text-on-primary-container">CLEARANCE: LEVEL <?= htmlspecialchars(substr((string)($currentUser['clearance_level'] ?? 'L4'), 1) ?: '4') ?> (<?= htmlspecialchars(strtoupper((string)($currentUser['role_name'] ?? 'EXECUTIVE ADMIN'))) ?>)</span>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center"><span
                            class="material-symbols-outlined text-on-primary text-[18px]">person</span></div>
                </div>
            </div>

            <!-- Top Bar Sign Out -->
            <a href="../api/logout.php?system=Admin%20%26%20Governance%20Portal&redirect=../Admin%20%26%20Governance%20Portal/login.php" class="top-signout-btn" title="Sign Out of Admin &amp; Governance Portal" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" ><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
        </div>
    </header>
    <?= gov_renderSidebar('ComplianceOversight.php') ?>
    <div class="pl-[260px]">
        <main class="relative pt-[60px] w-full min-h-screen bg-surface px-gutter-desktop py-space-lg">
            <div class="flex flex-col w-full">

                <!-- BREADCRUMBS & CONSOLE RUNBAR -->
                <div
                    class="flex flex-col md:flex-row md:items-center justify-between gap-space-sm pb-space-sm mb-space-base border-b border-surface-variant">
                    <div
                        class="flex items-center gap-space-xs font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                        <span class="hover:text-primary transition-colors cursor-pointer">VOSTOKPRIBOR-ROOT</span>
                        <span>/</span>
                        <span class="hover:text-primary transition-colors cursor-pointer">GOVERNANCE-TIER</span>
                        <span>/</span>
                        <span class="hover:text-primary transition-colors cursor-pointer">REGULATORY &amp; RISK</span>
                        <span>/</span>
                        <span class="text-primary font-bold">COMPLIANCE OVERSIGHT [SYS-11]</span>
                    </div>
                    <div class="flex items-center gap-space-sm">
                        <div
                            class="flex items-center gap-space-xs px-space-xs py-[2px] bg-surface-container rounded border border-outline-variant/50">
                            <span class="w-1.5 h-1.5 bg-secondary-fixed-dim rounded-full animate-ping"></span>
                            <span
                                class="font-telemetry-micro text-telemetry-micro text-on-surface font-semibold">NOTARIZED
                                DRIFT: 0.00%</span>
                        </div>
                        <div class="font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                            NODE: <span class="font-bold text-on-surface">ALMATY-VAULT-01</span> [UTC+6]
                        </div>
                    </div>
                </div>
                <!-- TITLE BAR & TOP-LEVEL ACTIONS -->
                <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-space-md mb-space-lg">
                    <div class="flex flex-col gap-space-xs max-w-4xl">
                        <div class="flex flex-wrap items-center gap-space-xs">
                            <span
                                class="px-space-xs py-[1px] bg-primary text-on-primary font-security-stamp text-security-stamp tracking-widest rounded-none border border-primary">
                                STATUTORY JURISDICTION: KAZ-CERT / ST RK ISO/IEC 27001
                            </span>
                            <span
                                class="px-space-xs py-[1px] bg-error-container text-on-error-container font-security-stamp text-security-stamp tracking-widest rounded-none border border-error/40">
                                CLEARANCE: LEVEL 5 RESTRICTED
                            </span>
                            <span
                                class="px-space-xs py-[1px] bg-surface-container-high text-on-surface-variant font-telemetry-micro text-telemetry-micro tracking-wider">
                                AUDIT CADENCE: CONTINUOUS TELEMETRY PROOF (T+0s)
                            </span>
                        </div>
                        <h1
                            class="font-headline-lg text-headline-lg text-primary tracking-tight font-bold uppercase mt-space-2xs">
                            Compliance Oversight &amp; Statutory Audit Register
                        </h1>
                        <p class="font-body-default text-body-default text-on-surface-variant">
                            Autonomous statutory telemetry validation for critical industrial telemetry gateways,
                            cryo-scada networks, and state cryptographic custody standards under Republic of Kazakhstan
                            Cyber-Shield directives.
                        </p>
                    </div>
                    <!-- ACTION BUTTON GROUP -->
                    <div class="flex flex-wrap items-center gap-space-xs self-start xl:self-end">
                        <button
                            class="h-control-height-md px-space-base bg-primary hover:bg-primary-container text-on-primary text-body-compact font-semibold flex items-center gap-space-xs transition-colors shadow-sm cursor-pointer"
                            id="reverifyBtn">
                            <span class="material-symbols-outlined text-[16px] text-secondary-fixed">verified</span>
                            <span>Re-Verify All Frameworks (SHA-256)</span>
                        </button>
                        <button id="btnExportComplianceLedger"
                            class="h-control-height-md px-space-md bg-surface-container-lowest hover:bg-surface-container border border-outline-variant text-on-surface text-body-compact font-medium flex items-center gap-space-xs transition-colors cursor-pointer">
                            <span
                                class="material-symbols-outlined text-[16px] text-on-surface-variant">file_download</span>
                            <span>Export Ledger (PDF/XBRL)</span>
                        </button>
                        <button id="btnFileEmergencyDeviation"
                            class="h-control-height-md px-space-md bg-error hover:bg-on-error-container text-on-error text-body-compact font-semibold flex items-center gap-space-xs transition-colors shadow-sm cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">warning</span>
                            <span class="font-label-uppercase tracking-wider">File Emergency Deviation</span>
                        </button>
                    </div>
                </div>
                <!-- STATUTORY POSTURE SUMMARY KPI ROW (4-CARD GRID WITH CLASSIFICATION STRIPS) -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-space-md mb-space-lg">
                    <!-- Card 1: Internal (#3E7CB1) -->
                    <div
                        class="bg-surface-container-lowest border border-outline-variant/60 relative p-space-md flex flex-col justify-between shadow-sm overflow-hidden before:content-[''] before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-[#3E7CB1]">
                        <div class="flex items-start justify-between pl-space-xs">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-space-2xs">
                                    <span
                                        class="font-security-stamp text-[10px] uppercase text-[#3E7CB1] tracking-wider font-bold">INTERNAL
                                        AUDIT</span>
                                    <span class="text-outline-variant text-[10px]">•</span>
                                    <span
                                        class="font-telemetry-micro text-[10px] text-on-surface-variant">ST-RK-27001</span>
                                </div>
                                <span class="font-title-sm text-title-sm text-primary font-bold mt-space-2xs">Overall
                                    Statutory Adherence</span>
                            </div>
                            <span class="material-symbols-outlined text-[#3E7CB1] text-[20px]">verified_user</span>
                        </div>
                        <div class="pl-space-xs pt-space-md pb-space-xs flex items-baseline gap-space-xs">
                            <span class="font-telemetry-data text-[28px] leading-7 font-bold text-primary">96.8%</span>
                            <span class="font-telemetry-micro text-telemetry-micro text-secondary font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">trending_up</span> +0.4% Target: 95.0%</span>
                        </div>
                        <div
                            class="pl-space-xs pt-space-xs border-t border-surface-variant/80 flex items-center justify-between font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                            <span>28/29 Mandated Compliant</span><span class="text-tertiary font-semibold ml-2 px-1.5 py-0.5 bg-tertiary-fixed/20 rounded">1 In Remediation</span>
                        </div>
                    </div>
                    <!-- Card 2: Confidential (#D9822B) -->
                    <div
                        class="bg-surface-container-lowest border border-outline-variant/60 relative p-space-md flex flex-col justify-between shadow-sm overflow-hidden before:content-[''] before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-[#D9822B]">
                        <div class="flex items-start justify-between pl-space-xs">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-space-2xs">
                                    <span
                                        class="font-security-stamp text-[10px] uppercase text-[#D9822B] tracking-wider font-bold">CONFIDENTIAL</span>
                                    <span class="text-outline-variant text-[10px]">•</span>
                                    <span
                                        class="font-telemetry-micro text-[10px] text-on-surface-variant">SCHEDULE-Q2</span>
                                </div>
                                <span class="font-title-sm text-title-sm text-primary font-bold mt-space-2xs">Active
                                    Audit Engagements</span>
                            </div>
                            <span
                                class="material-symbols-outlined text-[#D9822B] text-[20px]">assignment_turned_in</span>
                        </div>
                        <div class="pl-space-xs pt-space-md pb-space-xs flex items-baseline gap-space-xs">
                            <span class="font-telemetry-data text-[28px] leading-7 font-bold text-primary">3</span>
                            <span class="font-telemetry-micro text-telemetry-micro text-on-surface-variant">Formal
                                Bodies On-Prem/Live</span>
                        </div>
                        <div
                            class="pl-space-xs pt-space-xs border-t border-surface-variant/80 flex items-center justify-between font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                            <span class="truncate">ISO 27001 • NIS DIR-44B • KAZ-CERT</span>
                            <span class="font-bold text-on-surface">SYNCD</span>
                        </div>
                    </div>
                    <!-- Card 3: Highly Confidential (#B23A32) -->
                    <div
                        class="bg-surface-container-lowest border border-outline-variant/60 relative p-space-md flex flex-col justify-between shadow-sm overflow-hidden before:content-[''] before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-[#B23A32]">
                        <div class="flex items-start justify-between pl-space-xs">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-space-2xs">
                                    <span
                                        class="font-security-stamp text-[10px] uppercase text-[#B23A32] tracking-wider font-bold">HIGHLY
                                        CONFIDENTIAL</span>
                                    <span class="text-outline-variant text-[10px]">•</span>
                                    <span class="font-telemetry-micro text-[10px] text-error font-bold">ACTION
                                        REQ</span>
                                </div>
                                <span class="font-title-sm text-title-sm text-primary font-bold mt-space-2xs">Open
                                    Non-Conformities</span>
                            </div>
                            <span class="material-symbols-outlined text-[#B23A32] text-[20px]">rule_folder</span>
                        </div>
                        <div class="pl-space-xs pt-space-md pb-space-xs flex items-baseline gap-space-xs">
                            <span class="font-telemetry-data text-[28px] leading-7 font-bold text-error">2</span>
                            <span class="font-telemetry-micro text-telemetry-micro text-error font-medium">Pending
                                Remediation</span>
                        </div>
                        <div
                            class="pl-space-xs pt-space-xs border-t border-surface-variant/80 flex items-center justify-between font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                            <span class="text-error font-semibold">NC-2026-004 / NC-2026-009</span>
                            <span>SLA &lt; 24h</span>
                        </div>
                    </div>
                    <!-- Card 4: Public/Slate (#8A94A0) -->
                    <div
                        class="bg-surface-container-lowest border border-outline-variant/60 relative p-space-md flex flex-col justify-between shadow-sm overflow-hidden before:content-[''] before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-[#8A94A0]">
                        <div class="flex items-start justify-between pl-space-xs">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-space-2xs">
                                    <span
                                        class="font-security-stamp text-[10px] uppercase text-[#8A94A0] tracking-wider font-bold">PUBLIC
                                        MERKLE PROOF</span>
                                    <span class="text-outline-variant text-[10px]">•</span>
                                    <span class="font-telemetry-micro text-[10px] text-secondary">IMMUTABLE</span>
                                </div>
                                <span class="font-title-sm text-title-sm text-primary font-bold mt-space-2xs">Continuous
                                    Merkle Delta</span>
                            </div>
                            <span class="material-symbols-outlined text-[#8A94A0] text-[20px]">fingerprint</span>
                        </div>
                        <div class="pl-space-xs pt-space-md pb-space-xs flex items-baseline gap-space-xs">
                            <span class="font-telemetry-data text-[28px] leading-7 font-bold text-secondary">0
                                DRIFT</span>
                            <span class="font-telemetry-micro text-telemetry-micro text-on-surface-variant">100%
                                Cryptographic</span>
                        </div>
                        <div
                            class="pl-space-xs pt-space-xs border-t border-surface-variant/80 flex items-center justify-between font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                            <span>Root: 4m ago (EMP-1005)</span>
                            <span class="font-telemetry-data text-[10px] text-primary">#4,921,802</span>
                        </div>
                    </div>
                </div>
                <!-- MAIN TWO-COLUMN SPLIT WORKBENCH -->
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-md mb-space-base items-start">
                    <!-- LEFT 2/3 COLUMN: REGULATORY CONTROLS MATRIX TABLE & CADENCE -->
                    <div class="xl:col-span-8 flex flex-col gap-space-md">
                        <!-- CONTROLS CONTAINER -->
                        <div class="bg-surface-container-lowest border border-outline-variant shadow-sm flex flex-col">
                            <!-- FRAMEWORK TABS & CONTROLS HEADER -->
                            <div
                                class="p-space-md bg-surface-container-low border-b border-outline-variant flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
                                <!-- TABS -->
                                <div class="flex items-center gap-space-2xs overflow-x-auto select-none" id="frameworkTabs">
                                    <button onclick="filterFramework('all')" data-tab="all"
                                        class="tab-btn px-space-md py-space-xs bg-primary text-on-primary font-telemetry-micro text-telemetry-micro font-bold uppercase rounded-none border border-primary transition-colors flex items-center gap-space-xs whitespace-nowrap cursor-pointer">
                                        <span>All Frameworks</span>
                                        <span class="px-space-2xs bg-primary-container text-on-primary rounded text-[10px]"><?= $totalControls ?></span>
                                    </button>
                                    <button onclick="filterFramework('27001')" data-tab="27001"
                                        class="tab-btn px-space-md py-space-xs bg-surface-container hover:bg-surface-container-high text-on-surface-variant font-telemetry-micro text-telemetry-micro font-medium uppercase rounded-none border border-outline-variant/60 transition-colors flex items-center gap-space-xs whitespace-nowrap cursor-pointer">
                                        <span>ISO/IEC 27001</span>
                                        <span class="text-outline text-[10px]"><?= $isoCount ?></span>
                                    </button>
                                    <button onclick="filterFramework('KAZ')" data-tab="KAZ"
                                        class="tab-btn px-space-md py-space-xs bg-surface-container hover:bg-surface-container-high text-on-surface-variant font-telemetry-micro text-telemetry-micro font-medium uppercase rounded-none border border-outline-variant/60 transition-colors flex items-center gap-space-xs whitespace-nowrap cursor-pointer">
                                        <span>KAZ-CERT Directive</span>
                                        <span class="text-outline text-[10px]"><?= $kazCount ?></span>
                                    </button>
                                    <button onclick="filterFramework('SCADA')" data-tab="SCADA"
                                        class="tab-btn px-space-md py-space-xs bg-surface-container hover:bg-surface-container-high text-on-surface-variant font-telemetry-micro text-telemetry-micro font-medium uppercase rounded-none border border-outline-variant/60 transition-colors flex items-center gap-space-xs whitespace-nowrap cursor-pointer">
                                        <span>SCADA / Metrology</span>
                                        <span class="text-outline text-[10px]"><?= $scadaCount ?></span>
                                    </button>
                                </div>
                                <!-- SEARCH & SEVERITY DROPDOWN & NEW CONTROL BUTTON -->
                                <div class="flex items-center gap-space-xs flex-wrap">
                                    <div class="relative">
                                        <span
                                            class="material-symbols-outlined absolute left-space-xs top-1/2 -translate-y-1/2 text-[14px] text-outline">search</span>
                                        <input id="controlSearchInput"
                                            class="w-44 lg:w-52 h-control-height-sm pl-space-lg pr-space-xs bg-surface-container-lowest border border-outline-variant font-telemetry-micro text-telemetry-micro text-on-surface placeholder:text-outline focus:outline-none focus:border-primary"
                                            placeholder="Filter Control ID or Hash..." type="text" oninput="filterControlsTable()" />
                                    </div>
                                    <select id="controlStatusFilter" onchange="filterControlsTable()"
                                        class="h-control-height-sm px-space-xs bg-surface-container-lowest border border-outline-variant font-telemetry-micro text-telemetry-micro text-on-surface focus:outline-none focus:border-primary cursor-pointer">
                                        <option value="ALL">Status: All (<?= $totalControls ?>)</option>
                                        <option value="COMPLIANT">Compliant (<?= $compliantControls ?>)</option>
                                        <option value="REMEDIATION">Remediation (<?= $remediationControls ?>)</option>
                                        <option value="DEVIATION">Deviation (<?= $deviationControls ?>)</option>
                                    </select>
                                    <button onclick="openNewControlModal()"
                                        class="h-control-height-sm px-space-sm bg-primary text-on-primary hover:bg-primary/90 font-telemetry-micro text-telemetry-micro font-bold flex items-center gap-space-2xs uppercase tracking-wider transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-[14px]">add_box</span>
                                        <span>Register Control</span>
                                    </button>
                                </div>
                            </div>
                            <!-- HIGH DENSITY DATA TABLE -->
                            <div class="overflow-x-auto w-full">
                                <table class="w-full text-left border-collapse min-w-[780px]">
                                    <thead>
                                        <tr
                                            class="bg-surface-container-low border-b border-primary/40 font-security-stamp text-security-stamp text-on-surface-variant uppercase tracking-wider select-none">
                                            <th class="py-space-xs px-space-sm border-r border-outline-variant/40 w-32">
                                                Control ID</th>
                                            <th class="py-space-xs px-space-sm border-r border-outline-variant/40">
                                                Statutory Requirement &amp; Directive</th>
                                            <th class="py-space-xs px-space-sm border-r border-outline-variant/40 w-36">
                                                Target Asset</th>
                                            <th class="py-space-xs px-space-sm border-r border-outline-variant/40 w-36">
                                                Custodian</th>
                                            <th class="py-space-xs px-space-sm border-r border-outline-variant/40 w-36">
                                                Proof Hash / Token</th>
                                            <th
                                                class="py-space-xs px-space-sm border-r border-outline-variant/40 w-32 text-center">
                                                Status</th>
                                            <th class="py-space-xs px-space-sm w-32 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-body-compact text-body-compact divide-y divide-outline-variant/40" id="controlsTableBody">
                                        <?php if (empty($controls)): ?>
                                        <tr>
                                            <td colspan="7" class="py-space-md px-space-sm text-center text-outline font-telemetry-micro">
                                                No statutory compliance controls registered in database. Click "Register Control" to add one.
                                            </td>
                                        </tr>
                                        <?php else: foreach ($controls as $c): 
                                            $st = strtoupper($c['status'] ?? 'COMPLIANT');
                                            $statusBadge = 'bg-secondary-container/30 text-on-secondary-container border-secondary/30 text-secondary';
                                            $dotColor = 'bg-secondary';
                                            if ($st === 'DEVIATION') {
                                                $statusBadge = 'bg-error-container/40 text-on-error-container border-error/40 text-error';
                                                $dotColor = 'bg-error';
                                            } elseif ($st === 'REMEDIATION') {
                                                $statusBadge = 'bg-tertiary-container/40 text-on-tertiary-container border-tertiary/40 text-tertiary';
                                                $dotColor = 'bg-tertiary';
                                            }
                                            $cId = htmlspecialchars($c['control_id'] ?? '');
                                            $cFramework = htmlspecialchars($c['framework'] ?? 'ST RK 27001');
                                            $cTitle = htmlspecialchars($c['title'] ?? '');
                                            $cDesc = htmlspecialchars($c['description'] ?? '');
                                            $cAsset = htmlspecialchars($c['target_asset'] ?? 'SYS-11 Gov-Core');
                                            $cCustName = htmlspecialchars($c['custodian_name'] ?? 'Custodian');
                                            $cCustId = htmlspecialchars($c['custodian_emp_id'] ?? 'EMP-1005');
                                            $cHash = htmlspecialchars($c['proof_hash'] ?? 'SHA256:4f8a...9c1');
                                            $cType = htmlspecialchars($c['verification_type'] ?? 'HSM-VERIFIED');
                                            $cJson = htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8');
                                        ?>
                                        <tr class="hover:bg-surface-container-low transition-colors group control-row" data-control-id="<?= $cId ?>" data-framework="<?= $cFramework ?>" data-status="<?= $st ?>" data-json='<?= $cJson ?>'>
                                            <td class="py-space-xs px-space-sm border-r border-outline-variant/40 font-telemetry-data text-telemetry-micro text-primary font-bold">
                                                <?= $cId ?>
                                                <span class="block text-[9px] text-outline font-normal"><?= $cFramework ?></span>
                                            </td>
                                            <td class="py-space-xs px-space-sm border-r border-outline-variant/40">
                                                <div class="font-semibold text-on-surface"><?= $cTitle ?></div>
                                                <div class="text-[11px] text-on-surface-variant"><?= $cDesc ?></div>
                                            </td>
                                            <td class="py-space-xs px-space-sm border-r border-outline-variant/40 font-telemetry-micro text-telemetry-micro text-on-surface">
                                                <span class="px-space-2xs py-[1px] bg-surface-container font-medium border border-outline-variant/50"><?= $cAsset ?></span>
                                            </td>
                                            <td class="py-space-xs px-space-sm border-r border-outline-variant/40">
                                                <div class="font-medium text-on-surface text-[11px]"><?= $cCustName ?></div>
                                                <div class="font-telemetry-micro text-[10px] text-outline"><?= $cCustId ?></div>
                                            </td>
                                            <td class="py-space-xs px-space-sm border-r border-outline-variant/40 font-telemetry-micro text-telemetry-micro">
                                                <span class="text-secondary font-semibold hover:underline cursor-pointer" title="<?= $cHash ?>"><?= strlen($cHash) > 16 ? substr($cHash, 0, 16) . '...' : $cHash ?></span>
                                                <span class="block text-[9px] text-outline"><?= $cType ?></span>
                                            </td>
                                            <td class="py-space-xs px-space-sm border-r border-outline-variant/40 text-center">
                                                <button onclick="cycleControlStatus('<?= $cId ?>', '<?= $st ?>')" title="Click to cycle status (Compliant -> Remediation -> Deviation)"
                                                    class="inline-flex items-center gap-space-2xs px-space-xs py-[2px] <?= $statusBadge ?> font-label-uppercase text-label-uppercase font-bold border cursor-pointer hover:opacity-80 transition-opacity">
                                                    <span class="w-1.5 h-1.5 <?= $dotColor ?> rounded-full"></span>
                                                    <?= $st ?>
                                                </button>
                                            </td>
                                            <td class="py-space-xs px-space-sm text-right">
                                                <div class="inline-flex items-center gap-1 justify-end">
                                                    <button onclick="editControl('<?= $cId ?>')" title="Edit Control"
                                                        class="px-space-xs py-[2px] bg-surface-container hover:bg-primary hover:text-on-primary border border-outline-variant font-telemetry-micro text-[10px] font-semibold transition-colors cursor-pointer">
                                                        Edit
                                                    </button>
                                                    <button onclick="deleteControl('<?= $cId ?>')" title="Delete Control"
                                                        class="px-space-xs py-[2px] bg-surface-container hover:bg-error hover:text-on-error border border-outline-variant font-telemetry-micro text-[10px] font-semibold transition-colors cursor-pointer">
                                                        Del
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- TABLE FOOTER PAGINATION & TELEMETRY SYNC -->
                            <div class="p-space-sm bg-surface-container-low border-t border-outline-variant flex items-center justify-between text-telemetry-micro font-telemetry-micro text-on-surface-variant flex-wrap gap-2">
                                <div class="flex items-center gap-space-sm flex-wrap">
                                    <span id="footerCountSpan">SHOWING <?= $totalControls ?> OF <?= $totalControls ?> REGULATORY CONTROLS</span>
                                    <span class="text-outline-variant">|</span>
                                    <span class="text-secondary font-bold flex items-center gap-space-2xs">
                                        <span class="w-2 h-2 bg-secondary rounded-full"></span>
                                        <?= $compliantControls ?> COMPLIANT
                                    </span>
                                    <span class="text-tertiary font-semibold"><?= $remediationControls ?> REMEDIATION</span>
                                    <span class="text-error font-bold"><?= $deviationControls ?> DEVIATION</span>
                                </div>
                                <div class="flex items-center gap-space-xs">
                                    <span class="px-space-xs font-bold text-primary">DATABASE LIVE SYNC</span>
                                </div>
                            </div>
                            </div>
                        </div>
                        <!-- LIVE COMPLIANCE STREAM TICKER -->
                        <div
                            class="bg-primary text-on-primary p-space-sm border border-primary-container shadow-sm flex items-center gap-space-md">
                            <div
                                class="flex items-center gap-space-xs px-space-xs py-[2px] bg-error text-on-error font-security-stamp text-security-stamp tracking-wider whitespace-nowrap">
                                <span class="w-2 h-2 bg-on-error rounded-full animate-pulse"></span>
                                <span>LIVE AUDIT STREAM</span>
                            </div>
                            <div
                                class="font-telemetry-micro text-telemetry-micro text-on-primary-container overflow-hidden whitespace-nowrap text-ellipsis flex-1">
                                <span class="text-secondary-fixed font-semibold">15:44:02 UTC+6</span> — Control <span
                                    class="text-on-primary font-bold">CTRL-CERT-04</span> verified against Almaty Vault
                                Merkle tree #4,921,802... Nominal block attestation completed. Operator: EMP-1005. Hash
                                integrity validated.
                            </div>
                            <div
                                class="font-telemetry-micro text-[10px] text-secondary-fixed-dim whitespace-nowrap hidden md:block">
                                POLL: CONTINUOUS (1000ms)
                            </div>
                        </div>
                    </div>
                    <!-- RIGHT 1/3 COLUMN: AUDIT TIMELINES, DEVIATION CARDS & CRYPTO SEAL -->
                    <div class="xl:col-span-4 flex flex-col gap-space-md">
                        <!-- STATUTORY AUDIT SCHEDULE & DEADLINES PANEL -->
                        <div class="bg-surface-container-lowest border border-outline-variant shadow-sm flex flex-col">
                            <div
                                class="p-space-md bg-surface-container-low border-b border-outline-variant flex items-center justify-between">
                                <div class="flex items-center gap-space-xs">
                                    <span
                                        class="material-symbols-outlined text-primary text-[18px]">calendar_clock</span>
                                    <span
                                        class="font-headline-md text-[14px] text-primary font-bold uppercase tracking-tight">Statutory
                                        Audit Deadlines</span>
                                </div>
                                <span
                                    class="px-space-xs py-[1px] bg-surface-container text-on-surface-variant font-security-stamp text-[10px]">Q2-2026</span>
                            </div>
                            <div class="p-space-md flex flex-col gap-space-sm">
                                <!-- Event 1 -->
                                <div
                                    class="p-space-sm bg-surface-container-low border-l-4 border-error flex flex-col gap-space-2xs">
                                    <div class="flex items-center justify-between">
                                        <span
                                            class="font-security-stamp text-[10px] text-error font-bold tracking-wider uppercase">IN
                                            3 DAYS</span>
                                        <span class="font-telemetry-micro text-[10px] text-on-surface-variant">19 APR
                                            2026</span>
                                    </div>
                                    <div class="font-title-sm text-[13px] text-on-surface font-bold">
                                        Board Risk &amp; Governance Statutory Briefing
                                    </div>
                                    <div class="font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                                        Comprehensive report submission to Audit Committee on privileged identities
                                        &amp; orphan accounts.
                                    </div>
                                    <div
                                        class="flex items-center justify-between pt-space-2xs mt-space-2xs border-t border-outline-variant/30 text-[10px] font-telemetry-micro">
                                        <span class="text-error font-semibold">Requirement: Zero Open Level 1
                                            Non-Conformities</span>
                                        <span class="text-primary font-bold cursor-pointer hover:underline">Prepare
                                            Dossier →</span>
                                    </div>
                                </div>
                                <!-- Event 2 -->
                                <div
                                    class="p-space-sm bg-surface-container-low border-l-4 border-tertiary flex flex-col gap-space-2xs">
                                    <div class="flex items-center justify-between">
                                        <span
                                            class="font-security-stamp text-[10px] text-tertiary font-bold tracking-wider uppercase">IN
                                            14 DAYS</span>
                                        <span class="font-telemetry-micro text-[10px] text-on-surface-variant">30 APR
                                            2026</span>
                                    </div>
                                    <div class="font-title-sm text-[13px] text-on-surface font-bold">
                                        State Security Inspectorate (KAZ-CERT) Q2 On-Prem
                                    </div>
                                    <div class="font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                                        Physical &amp; network cryptographic verification of telemetry nodes in Almaty
                                        &amp; Karaganda stations.
                                    </div>
                                    <div
                                        class="flex items-center justify-between pt-space-2xs mt-space-2xs border-t border-outline-variant/30 text-[10px] font-telemetry-micro">
                                        <span class="text-on-surface-variant">Lead Inspector: Bulat Kasymov (State
                                            CIS)</span>
                                        <span class="text-primary font-bold cursor-pointer hover:underline">Check
                                            Checklist →</span>
                                    </div>
                                </div>
                                <!-- Event 3 -->
                                <div
                                    class="p-space-sm bg-surface-container-low border-l-4 border-secondary flex flex-col gap-space-2xs">
                                    <div class="flex items-center justify-between">
                                        <span
                                            class="font-security-stamp text-[10px] text-secondary font-bold tracking-wider uppercase">IN
                                            42 DAYS</span>
                                        <span class="font-telemetry-micro text-[10px] text-on-surface-variant">28 MAY
                                            2026</span>
                                    </div>
                                    <div class="font-title-sm text-[13px] text-on-surface font-bold">
                                        ST RK ISO/IEC 27001:2023 Surveillance Audit
                                    </div>
                                    <div class="font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                                        Formal surveillance cycle with National Center of Accreditation (NCA).
                                    </div>
                                    <div
                                        class="flex items-center justify-between pt-space-2xs mt-space-2xs border-t border-outline-variant/30 text-[10px] font-telemetry-micro">
                                        <span class="text-secondary font-semibold">Continuous Merkle logs accepted as
                                            primary evidence</span>
                                        <span class="text-primary font-bold cursor-pointer hover:underline">View Scope
                                            →</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ACTIVE REMEDIATION DESK / CRITICAL DEVIATIONS -->
                        <div class="bg-surface-container-lowest border border-outline-variant shadow-sm flex flex-col">
                            <div class="p-space-md bg-surface-container-low border-b border-outline-variant flex items-center justify-between">
                                <div class="flex items-center gap-space-xs">
                                    <span class="material-symbols-outlined text-error text-[18px]">report_problem</span>
                                    <span class="font-headline-md text-[14px] text-primary font-bold uppercase tracking-tight">Active Non-Conformity Desk</span>
                                </div>
                                <div class="flex items-center gap-space-xs">
                                    <span class="px-space-xs py-[1px] bg-error-container text-on-error-container font-label-uppercase text-label-uppercase font-bold">
                                        <?= count($incidents) ?> RECORDED
                                    </span>
                                    <button onclick="openNewIncidentModal()" title="Report Incident / Deviation"
                                        class="px-space-xs py-[2px] bg-error text-on-error hover:bg-error/90 font-telemetry-micro text-[10px] font-bold uppercase tracking-wider transition-colors cursor-pointer flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">add</span>
                                        <span>Report</span>
                                    </button>
                                </div>
                            </div>
                            <div class="p-space-md flex flex-col gap-space-md" id="incidentsDeskList">
                                <?php if (empty($incidents)): ?>
                                    <div class="p-space-sm bg-surface-container-low border border-outline-variant text-center text-outline font-telemetry-micro">
                                        No active deviations or non-conformity incidents recorded.
                                    </div>
                                <?php else: foreach ($incidents as $inc):
                                    $incCode = htmlspecialchars($inc['incident_code'] ?? ('NC-' . $inc['incident_id']));
                                    $incTitle = htmlspecialchars($inc['title'] ?? '');
                                    $incDesc = htmlspecialchars($inc['description'] ?? '');
                                    $incSev = strtoupper($inc['severity'] ?? 'HIGH');
                                    $incStatus = strtoupper($inc['status'] ?? 'OPEN');
                                    $leadName = htmlspecialchars($inc['lead_name'] ?? ($inc['assigned_to_emp_id'] ?? 'Unassigned'));
                                    $borderSev = ($incSev === 'CRITICAL' || $incSev === 'HIGH') ? 'border-error/40' : 'border-tertiary/40';
                                    $badgeSev = ($incSev === 'CRITICAL' || $incSev === 'HIGH') ? 'bg-error text-on-error' : 'bg-tertiary-fixed text-on-tertiary-fixed';
                                ?>
                                <div class="border <?= $borderSev ?> bg-surface-container-low p-space-sm flex flex-col gap-space-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="font-telemetry-micro text-[11px] text-error font-bold"><?= $incCode ?> // <?= $incTitle ?></span>
                                        <span class="px-space-xs py-[1px] <?= $badgeSev ?> font-security-stamp text-[9px] font-bold"><?= $incStatus ?></span>
                                    </div>
                                    <div class="font-body-compact text-body-compact text-on-surface font-semibold">
                                        <?= $incDesc ?>
                                    </div>
                                    <div class="font-telemetry-micro text-[10px] text-on-surface-variant flex items-center gap-space-xs">
                                        <span class="material-symbols-outlined text-[12px] text-error">assignment_ind</span>
                                        <span>Lead: <?= $leadName ?> • Severity: <?= $incSev ?></span>
                                    </div>
                                    <div class="pt-space-xs mt-space-2xs border-t border-outline-variant/40 flex items-center justify-between">
                                        <button onclick="window.location.href='PrivilegedAccounts.php'" class="h-control-height-sm px-space-sm bg-primary hover:bg-primary-container text-on-primary font-body-compact text-[11px] font-semibold flex items-center gap-space-xs transition-colors cursor-pointer">
                                            <span class="material-symbols-outlined text-[14px]">link</span>
                                            <span>Cross-Reference Matrix</span>
                                        </button>
                                        <?php if ($incStatus !== 'RESOLVED' && $incStatus !== 'CLOSED'): ?>
                                        <button onclick="resolveIncident(<?= (int)$inc['incident_id'] ?>)" class="h-control-height-sm px-space-xs text-error hover:bg-error-container font-telemetry-micro text-[10px] font-bold transition-colors cursor-pointer">
                                            Resolve Now
                                        </button>
                                        <?php else: ?>
                                        <span class="text-secondary font-bold text-[10px] font-telemetry-micro">RESOLVED</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                        <!-- AUDITOR VERIFICATION CERTIFICATE BADGE & DIGITAL STAMP -->
                        <div
                            class="bg-surface-container-lowest border-2 border-primary shadow-md p-space-md flex flex-col gap-space-sm relative overflow-hidden">
                            <!-- WATERMARK STAMP -->
                            <div class="absolute -right-6 -bottom-6 select-none pointer-events-none opacity-5">
                                <span class="material-symbols-outlined text-[160px] text-primary">security</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-primary/20 pb-space-xs">
                                <div class="flex items-center gap-space-xs">
                                    <span class="w-2.5 h-2.5 bg-secondary rounded-none"></span>
                                    <span
                                        class="font-security-stamp text-security-stamp text-primary uppercase font-bold tracking-widest">
                                        TAMPER-EVIDENT ATTESTATION SEAL
                                    </span>
                                </div>
                                <span class="font-telemetry-micro text-[10px] text-secondary font-bold">STATE-LEVEL
                                    VERIFIED</span>
                            </div>
                            <div
                                class="flex flex-col gap-space-2xs font-telemetry-micro text-telemetry-micro text-on-surface">
                                <div class="flex justify-between">
                                    <span class="text-on-surface-variant">Attesting Authority:</span>
                                    <span class="font-bold text-primary">VOSTOKPRIBOR GOV-CORE // SYSTEM 11</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-on-surface-variant">Signatory:</span>
                                    <span class="font-bold text-primary">Timur Akhmetov (EMP-1005, CGO)</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-on-surface-variant">Hardware Token:</span>
                                    <span class="font-telemetry-data text-on-surface">YubiHSM2-ALMATY-01</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-on-surface-variant">Algorithm:</span>
                                    <span class="font-telemetry-data text-on-surface">ECDSA-SECP256K1 / SHA-256</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-on-surface-variant">Ledger Anchor Block:</span>
                                    <span class="font-telemetry-data text-secondary font-bold">#4,921,802</span>
                                </div>
                            </div>
                            <div
                                class="p-space-xs bg-surface-container font-telemetry-data text-[10px] text-on-surface-variant break-all border border-outline-variant/60 select-all">
                                d6a78bc4149afbf4c8996fb92427ae41e4649b934ca495991b7852b855e94b29
                            </div>
                            <div
                                class="flex items-center justify-between pt-space-xs border-t border-outline-variant/40">
                                <div
                                    class="flex items-center gap-space-2xs text-[10px] font-telemetry-micro text-secondary font-semibold">
                                    <span class="material-symbols-outlined text-[14px]">lock</span>
                                    <span>Cryptographic Chain Valid</span>
                                </div>
                                <button
                                    class="text-[11px] font-telemetry-micro text-primary font-bold hover:underline cursor-pointer">
                                    Download Public Key (PEM)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- INTERACTIVE CLIENT MICRO-LOGIC -->

            </div>
        </main>
    </div>
    <script src="js/common.js"></script>
    <script src="js/complianceOversight.js"></script>

    <!-- MODAL: REGISTER / EDIT STATUTORY CONTROL -->
    <div id="controlModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest border-2 border-primary w-full max-w-xl shadow-2xl overflow-hidden flex flex-col">
            <div class="p-space-md bg-surface-container-low border-b border-outline-variant flex items-center justify-between">
                <div class="flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-primary text-[20px]">policy</span>
                    <h3 id="controlModalTitle" class="font-headline-md text-[14px] text-primary font-bold uppercase tracking-wider">Register Statutory Control</h3>
                </div>
                <button type="button" onclick="closeControlModal()" class="text-on-surface-variant hover:text-error text-xl font-bold p-1 leading-none">&times;</button>
            </div>
            <form id="controlForm" onsubmit="saveControlForm(event)" class="p-space-md flex flex-col gap-space-sm text-telemetry-micro font-telemetry-micro">
                <input type="hidden" id="ctrl_is_edit" value="0">
                <div class="grid grid-cols-2 gap-space-sm">
                    <div>
                        <label class="block text-outline font-semibold uppercase mb-1">Control ID *</label>
                        <input type="text" id="ctrl_id" required class="w-full h-control-height-sm px-space-xs bg-surface-container-lowest border border-outline-variant font-telemetry-data text-on-surface" placeholder="e.g. CTRL-ISO-9.5">
                    </div>
                    <div>
                        <label class="block text-outline font-semibold uppercase mb-1">Regulatory Framework *</label>
                        <input type="text" id="ctrl_framework" required class="w-full h-control-height-sm px-space-xs bg-surface-container-lowest border border-outline-variant text-on-surface" placeholder="e.g. ST RK 27001 §9.5">
                    </div>
                </div>
                <div>
                    <label class="block text-outline font-semibold uppercase mb-1">Title / Statutory Requirement *</label>
                    <input type="text" id="ctrl_title" required class="w-full h-control-height-sm px-space-xs bg-surface-container-lowest border border-outline-variant text-on-surface" placeholder="Requirement title...">
                </div>
                <div>
                    <label class="block text-outline font-semibold uppercase mb-1">Directive Description</label>
                    <textarea id="ctrl_description" rows="2" class="w-full p-space-xs bg-surface-container-lowest border border-outline-variant text-on-surface" placeholder="Detailed enforcement requirements..."></textarea>
                </div>
                <div class="grid grid-cols-2 gap-space-sm">
                    <div>
                        <label class="block text-outline font-semibold uppercase mb-1">Target Asset</label>
                        <input type="text" id="ctrl_target_asset" class="w-full h-control-height-sm px-space-xs bg-surface-container-lowest border border-outline-variant text-on-surface" placeholder="e.g. SYS-11 Gov-Core">
                    </div>
                    <div>
                        <label class="block text-outline font-semibold uppercase mb-1">Custodian Employee ID</label>
                        <input type="text" id="ctrl_custodian_emp_id" class="w-full h-control-height-sm px-space-xs bg-surface-container-lowest border border-outline-variant font-telemetry-data text-on-surface" placeholder="EMP-1005" value="EMP-1005">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-space-sm">
                    <div>
                        <label class="block text-outline font-semibold uppercase mb-1">Status</label>
                        <select id="ctrl_status" class="w-full h-control-height-sm px-space-xs bg-surface-container-lowest border border-outline-variant text-on-surface">
                            <option value="COMPLIANT">COMPLIANT</option>
                            <option value="REMEDIATION">REMEDIATION</option>
                            <option value="DEVIATION">DEVIATION</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-outline font-semibold uppercase mb-1">Verification Type</label>
                        <input type="text" id="ctrl_verification_type" class="w-full h-control-height-sm px-space-xs bg-surface-container-lowest border border-outline-variant text-on-surface" placeholder="HSM-VERIFIED" value="HSM-VERIFIED">
                    </div>
                </div>
                <div>
                    <label class="block text-outline font-semibold uppercase mb-1">Proof Hash / Token</label>
                    <input type="text" id="ctrl_proof_hash" class="w-full h-control-height-sm px-space-xs bg-surface-container-lowest border border-outline-variant font-telemetry-data text-on-surface" placeholder="SHA256:...">
                </div>
                <div class="flex items-center justify-end gap-space-xs pt-space-sm border-t border-outline-variant">
                    <button type="button" onclick="closeControlModal()" class="px-space-md py-space-xs bg-surface-container hover:bg-surface-container-high text-on-surface border border-outline-variant uppercase font-bold cursor-pointer">Cancel</button>
                    <button type="submit" id="ctrlSubmitBtn" class="px-space-md py-space-xs bg-primary hover:bg-primary/90 text-on-primary border border-primary uppercase font-bold cursor-pointer">Commit Control</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: REPORT INCIDENT / DEVIATION -->
    <div id="incidentModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest border-2 border-error w-full max-w-xl shadow-2xl overflow-hidden flex flex-col">
            <div class="p-space-md bg-surface-container-low border-b border-outline-variant flex items-center justify-between">
                <div class="flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-error text-[20px]">report</span>
                    <h3 class="font-headline-md text-[14px] text-error font-bold uppercase tracking-wider">Report Non-Conformity Incident</h3>
                </div>
                <button type="button" onclick="closeIncidentModal()" class="text-on-surface-variant hover:text-error text-xl font-bold p-1 leading-none">&times;</button>
            </div>
            <form id="incidentForm" onsubmit="saveIncidentForm(event)" class="p-space-md flex flex-col gap-space-sm text-telemetry-micro font-telemetry-micro">
                <div class="grid grid-cols-2 gap-space-sm">
                    <div>
                        <label class="block text-outline font-semibold uppercase mb-1">Incident Code *</label>
                        <input type="text" id="inc_code" required class="w-full h-control-height-sm px-space-xs bg-surface-container-lowest border border-outline-variant font-telemetry-data text-on-surface" placeholder="NC-2026-010">
                    </div>
                    <div>
                        <label class="block text-outline font-semibold uppercase mb-1">Severity *</label>
                        <select id="inc_severity" class="w-full h-control-height-sm px-space-xs bg-surface-container-lowest border border-outline-variant text-on-surface">
                            <option value="Critical">Critical</option>
                            <option value="High" selected>High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-outline font-semibold uppercase mb-1">Title *</label>
                    <input type="text" id="inc_title" required class="w-full h-control-height-sm px-space-xs bg-surface-container-lowest border border-outline-variant text-on-surface" placeholder="e.g. Unlawful Token Persistence">
                </div>
                <div>
                    <label class="block text-outline font-semibold uppercase mb-1">Incident Details *</label>
                    <textarea id="inc_description" rows="3" required class="w-full p-space-xs bg-surface-container-lowest border border-outline-variant text-on-surface" placeholder="Detailed non-conformity findings and breach telemetry..."></textarea>
                </div>
                <div>
                    <label class="block text-outline font-semibold uppercase mb-1">Assigned Investigator (EMP ID)</label>
                    <input type="text" id="inc_assigned_to" class="w-full h-control-height-sm px-space-xs bg-surface-container-lowest border border-outline-variant font-telemetry-data text-on-surface" placeholder="EMP-1042" value="EMP-1042">
                </div>
                <div class="flex items-center justify-end gap-space-xs pt-space-sm border-t border-outline-variant">
                    <button type="button" onclick="closeIncidentModal()" class="px-space-md py-space-xs bg-surface-container hover:bg-surface-container-high text-on-surface border border-outline-variant uppercase font-bold cursor-pointer">Cancel</button>
                    <button type="submit" class="px-space-md py-space-xs bg-error hover:bg-error/90 text-on-error border border-error uppercase font-bold cursor-pointer">File Incident</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>