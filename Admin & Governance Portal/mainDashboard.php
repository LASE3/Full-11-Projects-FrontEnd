<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('ADM');
require_once __DIR__ . '/gov_service.php';

$currentUser = gov_getActiveUserProfile();
$metrics = gov_getGovernanceMetrics();
$telemetryGrid = gov_getTelemetryGridData();
$anomaly = gov_getCriticalAnomaly();
$sidebarBadges = gov_getSidebarBadges();
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>VOSTOKPRIBOR Administration &amp; Governance Portal - System 11</title>
    <link rel="stylesheet" href="css/common.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="js/tailwind-config.js"></script>
</head>

<body class="bg-surface font-body-default text-on-surface antialiased">
    <header class="fixed top-0 left-0 right-0 z-50 bg-primary text-on-primary border-b-4 border-error">
        <div class="h-[60px] w-full px-gutter-desktop flex items-center justify-between gap-space-md">
            <div class="flex items-center gap-space-md">
                <button class="mobile-nav-toggle" id="gov-sidebar-toggle" onclick="document.body.classList.toggle('sidebar-open')" title="Toggle Menu"><span class="material-symbols-outlined text-[20px]">menu</span></button>
                <img alt="VOSTOKPRIBOR System 11 Logo"
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
                </div><button id="notifications-toggle-btn" class="relative p-space-xs text-on-primary hover:text-secondary-fixed transition-colors notifications-btn" title="Live Enterprise Notifications">
                    <span class="material-symbols-outlined text-[20px]">notifications</span>
                </button>
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
    <?= gov_renderSidebar('mainDashboard.php') ?>
    <div class="pl-[260px]">
        <main class="relative pt-[60px] w-full min-h-screen bg-surface px-gutter-desktop py-space-lg">
            <div class="flex flex-col w-full gap-space-md">
                <!-- Breadcrumbs & Document Specification Banner -->
                <div
                    class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-sm bg-surface-container-lowest px-space-base py-space-sm shadow-sm">
                    <div class="flex flex-col gap-space-2xs">
                        <div
                            class="flex items-center gap-space-xs font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                            <span>ROOT</span>
                            <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                            <span>GOVERNANCE</span>
                            <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                            <span>EXECUTIVE OVERSIGHT</span>
                            <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                            <span class="text-primary font-bold">MAIN DASHBOARD</span>
                        </div>
                        <div class="flex items-center gap-space-sm flex-wrap">
                            <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">CORE GOVERNANCE DASHBOARD &amp; ATTESTATION MATRIX</h1>
                            <span
                                class="px-space-xs py-[2px] bg-primary text-on-primary font-security-stamp text-security-stamp uppercase tracking-wider">SEC-LEVEL
                                5 EYES ONLY</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-space-sm">
                        <div class="flex flex-col text-right">
                            <span class="font-label-uppercase text-label-uppercase text-on-surface-variant">STATUTORY
                                RECORD SPECIFICATION</span>
                            <div class="flex items-center gap-space-xs justify-end">
                                <span
                                    class="font-telemetry-micro text-telemetry-micro font-bold text-error bg-error-container/30 px-space-xs py-[1px] text-on-error-container">DOC-2026-007</span>
                                <span class="font-telemetry-micro text-telemetry-micro text-on-surface">MANDATORY
                                    QUARTERLY PRIVILEGE ATTESTATION</span>
                            </div>
                        </div>
                        <div class="w-2 h-8 bg-error"></div>
                    </div>
                </div>
                <!-- Compliance Overview & Metrics Sub-header -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">
                    <!-- Stat 1: Attestation Progress -->
                    <div class="bg-surface-container-lowest p-space-base shadow-sm relative overflow-hidden flex flex-col justify-between"
                        id="attestationPostureCard">
                        <div class="absolute left-0 top-0 bottom-0 w-[4px] bg-on-tertiary-container transition-colors duration-300"
                            id="attestationStripe"></div>
                        <div class="flex items-start justify-between">
                            <div class="flex flex-col">
                                <span
                                    class="font-label-uppercase text-label-uppercase text-on-surface-variant">ATTESTATION
                                    CYCLE POSTURE</span>
                                <span class="font-headline-md text-headline-md font-bold text-on-surface mt-space-2xs transition-all duration-300" id="attestationPercentage"><?= $metrics['percentage'] ?>% COMPLETE</span>
                            </div>
                            <div class="w-10 h-10 bg-tertiary-fixed/30 flex items-center justify-center text-on-tertiary-fixed-variant transition-all duration-300"
                                id="attestationIconWrapper">
                                <span class="material-symbols-outlined text-[24px]"
                                    id="attestationIcon">verified_user</span>
                            </div>
                        </div>
                        <div class="mt-space-md flex flex-col gap-space-xs">
                            <div class="w-full bg-surface-container-high h-2 flex overflow-hidden">
                                <div class="bg-on-tertiary-container h-full transition-all duration-500" id="attestationBarApproved" style="width: <?= $metrics['percentage'] ?>%;"></div>
                                <div class="bg-error h-full transition-all duration-500" id="attestationBarPending" style="width: <?= 100 - $metrics['percentage'] ?>%;"></div>
                            </div>
                            <div class="flex justify-between font-telemetry-micro text-telemetry-micro text-on-surface-variant"
                                id="attestationTextRow">
                                <span class="font-bold text-on-surface" id="attestationCountText"><?= $metrics['attested_count'] ?> of <?= $metrics['total_employees'] ?> Attested</span>
                                <span class="text-error font-bold" id="attestationActionText"><?= $metrics['pending_count'] ?> Action Items Required</span>
                            </div>
                        </div>
                    </div>
                    <!-- Stat 2: Violations Flagged -->
                    <div
                        class="bg-surface-container-lowest p-space-base shadow-sm relative overflow-hidden flex flex-col justify-between">
                        <div class="absolute left-0 top-0 bottom-0 w-[4px] bg-error"></div>
                        <div class="flex items-start justify-between">
                            <div class="flex flex-col">
                                <span
                                    class="font-label-uppercase text-label-uppercase text-on-surface-variant">ISOLATION
                                    TRIGGER</span>
                                <span class="font-headline-md text-headline-md font-bold text-error mt-space-2xs"><?= sprintf('%02d', $metrics['orphaned_count']) ?> ORPHANED ACCOUNTS</span>
                            </div>
                            <div
                                class="w-10 h-10 bg-error-container/40 flex items-center justify-center text-error animate-pulse">
                                <span class="material-symbols-outlined text-[24px]">warning</span>
                            </div>
                        </div>
                        <div
                            class="mt-space-md flex items-center gap-space-xs text-on-surface font-body-compact text-body-compact">
                            <span class="material-symbols-outlined text-[16px] text-error">link_off</span>
                            <span>Credentials active without verified corporate department sponsor</span>
                        </div>
                    </div>
                    <!-- Stat 3: Governing Authority -->
                    <div
                        class="bg-surface-container-lowest p-space-base shadow-sm relative overflow-hidden flex flex-col justify-between">
                        <div class="absolute left-0 top-0 bottom-0 w-[4px] bg-secondary"></div>
                        <div class="flex items-start justify-between">
                            <div class="flex flex-col">
                                <span
                                    class="font-label-uppercase text-label-uppercase text-on-surface-variant">GOVERNING
                                    SIGN-OFF JURISDICTION</span>
                                <span
                                    class="font-headline-md text-headline-md font-bold text-primary mt-space-2xs">DUAL-CUSTODY
                                    AUDIT</span>
                            </div>
                            <div
                                class="w-10 h-10 bg-secondary-container/30 flex items-center justify-center text-secondary">
                                <span class="material-symbols-outlined text-[24px]">gavel</span>
                            </div>
                        </div>
                        <div class="mt-space-md flex flex-col gap-space-2xs font-telemetry-micro text-telemetry-micro">
                            <div class="flex justify-between items-center text-on-surface">
                                <span class="font-bold">EMP-1005 (T. Akhmetov)</span>
                                <span class="px-space-xs py-[1px] bg-primary-container text-on-primary-container">CGO
                                    AUTHORIZED</span>
                            </div>
                            <div class="flex justify-between items-center text-on-surface-variant">
                                <span class="font-bold">EMP-1018 (Leonid Volkov)</span>
                                <span class="px-space-xs py-[1px] bg-surface-container-high text-on-surface">SECOPS
                                    LEAD</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Search Filter & Control Action Toolbar -->
                <div
                    class="bg-surface-container-lowest p-space-md shadow-sm flex flex-col xl:flex-row xl:items-center justify-between gap-space-md">
                    <div class="flex flex-col md:flex-row md:items-center gap-space-sm flex-1">
                        <!-- Search Input Container -->
                        <div class="relative flex-1">
                            <span
                                class="material-symbols-outlined absolute left-space-sm top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant">search</span>
                            <input
                                class="w-full h-control-height-md bg-surface-container-low pl-[36px] pr-space-base font-telemetry-data text-telemetry-data text-on-surface focus:outline-none focus:bg-surface-container-lowest shadow-inner"
                                id="matrixFilterInput"
                                placeholder="Filter by EMP-XXXX, Operator Name, System (SYS-05), or Clearance..."
                                type="text" />
                        </div>
                        <?php
$chipAllCount = count($telemetryGrid);
$chipOrphCount = count(array_filter($telemetryGrid, fn($e) => $e['category'] === 'ORPHANED'));
$chipElevCount = count(array_filter($telemetryGrid, fn($e) => $e['category'] === 'ELEVATED'));
$chipPendCount = count(array_filter($telemetryGrid, fn($e) => $e['category'] === 'PENDING'));
$chipRevCount = count(array_filter($telemetryGrid, fn($e) => $e['category'] === 'REVOKED'));
?>
                        <!-- Filter Segmented Chips -->
                        <div class="flex items-center gap-space-2xs flex-wrap" id="filterChipGroup">
                            <button
                                class="px-space-sm h-control-height-sm bg-primary text-on-primary font-telemetry-micro text-telemetry-micro font-bold flex items-center gap-space-xs filter-btn active-filter cursor-pointer"
                                data-filter="ALL">
                                <span>All Identifiers</span>
                                <span class="bg-primary-container px-space-xs text-on-primary-container"><?= $chipAllCount ?></span>
                            </button>
                            <button
                                class="px-space-sm h-control-height-sm bg-surface-container-high hover:bg-error-container text-on-surface hover:text-on-error-container font-telemetry-micro text-telemetry-micro flex items-center gap-space-xs filter-btn cursor-pointer"
                                data-filter="ORPHANED">
                                <span>Orphaned</span>
                                <span class="bg-error text-on-error px-space-xs font-bold"><?= $chipOrphCount ?></span>
                            </button>
                            <button
                                class="px-space-sm h-control-height-sm bg-surface-container-high hover:bg-surface-variant text-on-surface font-telemetry-micro text-telemetry-micro flex items-center gap-space-xs filter-btn cursor-pointer"
                                data-filter="ELEVATED">
                                <span>Elevated L5</span>
                                <span class="bg-surface-variant px-space-xs"><?= $chipElevCount ?></span>
                            </button>
                            <button
                                class="px-space-sm h-control-height-sm bg-surface-container-high hover:bg-surface-variant text-on-surface font-telemetry-micro text-telemetry-micro flex items-center gap-space-xs filter-btn cursor-pointer"
                                data-filter="PENDING">
                                <span>Pending Review</span>
                                <span
                                    class="bg-on-tertiary-container text-surface-container-lowest px-space-xs font-bold"
                                    id="pendingChipBadge"><?= $chipPendCount ?></span>
                            </button>
                            <button
                                class="px-space-sm h-control-height-sm bg-surface-container-high hover:bg-surface-variant text-on-surface font-telemetry-micro text-telemetry-micro flex items-center gap-space-xs filter-btn cursor-pointer"
                                data-filter="REVOKED">
                                <span>Revoked</span>
                                <span class="bg-surface-variant px-space-xs"><?= $chipRevCount ?></span>
                            </button>
                        </div>
                    </div>
                    <!-- Right Side Governance Operations Actions -->
                    <div class="flex items-center gap-space-xs flex-wrap">
                        <button
                            class="h-control-height-md px-space-base bg-primary text-on-primary font-body-compact text-body-compact font-bold flex items-center gap-space-xs hover:bg-primary-container transition-colors shadow-sm focus:ring-2 focus:ring-secondary-fixed/50"
                            id="signOffLedgerBtn">
                            <span class="material-symbols-outlined text-[16px] text-secondary-fixed">draw</span>
                            <span>Bulk Attestation Sign-off</span>
                        </button>
                        <button
                            class="h-control-height-md px-space-sm bg-surface-container-high hover:bg-surface-variant text-on-surface font-body-compact text-body-compact flex items-center gap-space-xs transition-colors focus:ring-2 focus:ring-outline"
                            id="exportMatrixCsvBtn">
                            <span class="material-symbols-outlined text-[16px]"
                                id="exportBtnIcon">sim_card_download</span>
                            <span id="exportBtnText">Export Matrix (CSV)</span>
                        </button>
                        <button
                            class="h-control-height-md px-space-sm bg-surface-container-high hover:bg-surface-variant text-on-surface font-body-compact text-body-compact flex items-center gap-space-xs transition-colors focus:ring-2 focus:ring-outline"
                            id="reCertWindowBtn">
                            <span class="material-symbols-outlined text-[16px]">schedule</span>
                            <span id="reCertBtnText">Re-Cert Window</span>
                            <span
                                class="hidden px-space-xs py-[1px] bg-secondary-fixed text-on-secondary-fixed text-[10px] font-bold font-telemetry-micro rounded"
                                id="reCertActiveBadge">ACTIVE</span>
                        </button>
                    </div>
                </div>
                <!-- Main Split Canvas: High Density Matrix + Context Remediation Drawer -->
                <div class="flex flex-col xl:flex-row gap-space-md items-start">
                    <!-- High-Density Matrix Table Panel (Occupies Fluid Main Body) -->
                    <div
                        class="w-full xl:flex-1 bg-surface-container-lowest shadow-sm relative overflow-hidden flex flex-col">
                        <!-- 4px Hard Security Stripe for Table Wrapper -->
                        <div class="absolute left-0 top-0 bottom-0 w-[4px] bg-error"></div>
                        <!-- Table Subheader Bar -->
                        <div
                            class="h-[38px] bg-surface-container-low pl-space-base pr-space-md flex items-center justify-between">
                            <div class="flex items-center gap-space-sm">
                                <span
                                    class="font-security-stamp text-security-stamp uppercase tracking-widest text-primary">TELEMETRY
                                    GRID: IDENTITY &amp; JURISDICTION BINDINGS</span>
                                <span
                                    class="font-telemetry-micro text-telemetry-micro text-on-surface-variant">POL-SEC-2026.04
                                    VALIDATED</span>
                            </div>
                            <div
                                class="flex items-center gap-space-md font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                                <span>SYS NODES: 11 ONLINE</span>
                                <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                            </div>
                        </div>
                        <!-- Matrix Table Wrapper -->
                        <div class="overflow-x-auto w-full">
                            <table class="w-full text-left border-collapse" id="matrixMasterTable">
                                <thead>
                                    <tr
                                        class="bg-surface-container font-label-uppercase text-label-uppercase text-on-surface-variant select-none">
                                        <th class="py-space-xs px-space-sm font-bold pl-space-base">EMP ID</th>
                                        <th class="py-space-xs px-space-sm font-bold">OPERATOR &amp; ASSIGNED POST</th>
                                        <th class="py-space-xs px-space-sm font-bold">OPERATIONAL NODE</th>
                                        <th class="py-space-xs px-space-sm font-bold text-center">LEVEL</th>
                                        <th class="py-space-xs px-space-sm font-bold">SYSTEM ENTITLEMENT PERMITS (01-11)
                                        </th>
                                        <th class="py-space-xs px-space-sm font-bold text-center">ROLE STATUS</th>
                                        <th class="py-space-xs px-space-sm font-bold text-right pr-space-base">
                                            GOVERNANCE DISPOSITION</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="font-body-compact text-body-compact divide-y divide-surface-container-high"
                                    id="matrixTableBody">
                                    <?php foreach ($telemetryGrid as $emp): ?>
                                        <tr class="hover:bg-surface-container-low transition-colors cursor-pointer matrix-row"
                                            data-cat="<?= htmlspecialchars($emp['category']) ?>"
                                            data-emp="<?= htmlspecialchars($emp['emp_id']) ?>"
                                            data-name="<?= htmlspecialchars($emp['full_name']) ?>"
                                            data-dept="<?= htmlspecialchars($emp['dept_name'] ?? $emp['department_code'] ?? '') ?>">
                                            <td class="py-space-xs px-space-sm pl-space-base">
                                                <span class="font-telemetry-data text-telemetry-data font-bold text-primary"><?= htmlspecialchars($emp['emp_id']) ?></span>
                                            </td>
                                            <td class="py-space-xs px-space-sm">
                                                <div class="font-bold text-on-surface"><?= htmlspecialchars($emp['full_name']) ?></div>
                                                <div class="font-telemetry-micro text-telemetry-micro text-on-surface-variant"><?= htmlspecialchars($emp['job_title']) ?></div>
                                            </td>
                                            <td class="py-space-xs px-space-sm font-telemetry-micro text-telemetry-micro text-on-surface">
                                                <?= htmlspecialchars($emp['dept_name'] ?? $emp['department_code'] ?? 'GENERAL') ?>
                                            </td>
                                            <td class="py-space-xs px-space-sm text-center">
                                                <span class="font-security-stamp text-security-stamp px-space-xs py-[2px] <?= ($emp['clearance_level'] === 'L4' || $emp['clearance_level'] === 'L5') ? 'bg-primary text-on-primary' : 'bg-surface-container-high text-on-surface' ?> font-bold">
                                                    <?= htmlspecialchars($emp['clearance_level']) ?>
                                                </span>
                                            </td>
                                            <td class="py-space-xs px-space-sm">
                                                <div class="flex items-center gap-[2px] font-telemetry-micro">
                                                    <?php foreach ($emp['permits'] as $num => $hasPermit): ?>
                                                        <?php if ($hasPermit): ?>
                                                            <span class="px-1 bg-primary text-on-primary font-bold" title="SYS-<?= $num ?> Authorized"><?= $num ?></span>
                                                        <?php else: ?>
                                                            <span class="px-1 bg-surface-container-high text-on-surface-variant">--</span>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </div>
                                            </td>
                                            <td class="py-space-xs px-space-sm text-center">
                                                <span class="status-badge inline-flex items-center gap-space-2xs px-space-xs py-[1px] font-telemetry-micro font-bold">
                                                    <?= $emp['role_status_html'] ?>
                                                </span>
                                            </td>
                                            <td class="py-space-xs px-space-sm text-right pr-space-base">
                                                <?= $emp['disposition_btn'] ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Table Footing Status & Metric Ledger -->
                        <div
                            class="bg-surface-container-low p-space-sm pl-space-base flex flex-col sm:flex-row sm:items-center justify-between gap-space-xs font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                            <div class="flex items-center gap-space-md">
                                <span>SHOWING: <?= count($telemetryGrid) ?> REGISTERED IDENTITIES</span>
                                <span>LEDGER PAGINATION: 1/2</span>
                            </div>
                            <div class="flex items-center gap-space-sm">
                                <span>LAST RECORD SYNC: 42 SECONDS AGO</span>
                                <span class="material-symbols-outlined text-[14px] text-secondary">sync</span>
                            </div>
                        </div>
                    </div>
                    <!-- Interactive Detail & Remediation Drawer / Modal Preview Panel -->
                    <div class="w-full xl:w-[380px] bg-surface-container-lowest shadow-sm flex flex-col relative overflow-hidden"
                        id="remediationPanel">
                        <!-- Top Alert Ribbon -->
                        <div class="h-[4px] bg-error w-full"></div>
                        <div class="p-space-base flex flex-col gap-space-md">
                            <!-- Panel Head -->
                            <div class="flex items-start justify-between">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-space-xs">
                                        <span class="material-symbols-outlined text-error text-[18px]">security</span>
                                        <span
                                            class="font-security-stamp text-security-stamp uppercase tracking-widest text-error">CRITICAL
                                            ANOMALY</span>
                                    </div>
                                    <span
                                        class="font-headline-md text-headline-md font-bold text-on-surface">REMEDIATION
                                        DESK</span>
                                </div>
                                <span
                                    class="px-space-xs py-[2px] bg-error text-on-error font-telemetry-micro font-bold">ISOLATION
                                    READY</span>
                            </div>
                            <!-- Selected Identity Identification Card -->
                            <div class="bg-surface-container-low p-space-md flex flex-col gap-space-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-telemetry-data text-telemetry-data font-bold text-primary"
                                        id="drawerEmpId"><?= htmlspecialchars($anomaly['actor_emp_id'] ?? 'EMP-1009') ?></span>
                                    <span
                                        class="px-space-xs py-[1px] bg-error-container text-on-error-container font-label-uppercase text-[10px] font-bold"
                                        id="drawerStatusTag">ORPHAN IDENTIFIER</span>
                                </div>
                                <div class="font-title-sm text-title-sm text-on-surface font-bold" id="drawerEmpName">
                                    <?= htmlspecialchars($anomaly['full_name'] ?? 'Maksim Sokolov') ?></div>
                                <div class="font-body-compact text-body-compact text-on-surface-variant"
                                    id="drawerEmpRole"><?= htmlspecialchars($anomaly['job_title'] ?? 'Contractor - SCADA Telemetry Unit') ?></div>
                            </div>
                            <!-- Policy Infraction Details -->
                            <div class="flex flex-col gap-space-xs">
                                <span
                                    class="font-label-uppercase text-label-uppercase text-on-surface-variant">INFRACTION
                                    SPECIFICATION</span>
                                <div
                                    class="bg-error-container/30 p-space-sm text-on-error-container font-body-compact text-body-compact">
                                    <div class="flex items-start gap-space-xs">
                                        <span
                                            class="material-symbols-outlined text-[16px] text-error flex-shrink-0 mt-[2px]">error</span>
                                        <div>
                                            <strong class="font-bold">SEC-POL-44 Breach Detected:</strong>
                                            Vendor contract termination date was 14 days ago. High-privilege RSA SSH-key
                                            remains configured inside
                                            <span
                                                class="font-telemetry-micro font-bold bg-error text-on-error px-1">SYS-03</span>
                                            (CNC SCADA Gateway).
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- In-Depth Key & Access Bindings -->
                            <div class="flex flex-col gap-space-2xs font-telemetry-micro text-telemetry-micro">
                                <span
                                    class="font-label-uppercase text-label-uppercase text-on-surface-variant">ASSOCIATED
                                    PRIVILEGE CREDENTIALS</span>
                                <div class="flex justify-between py-[3px] bg-surface-container-low px-space-xs">
                                    <span class="text-on-surface-variant">Key Fingerprint:</span>
                                    <span class="font-bold text-on-surface">SHA256:7mP0w...k9Qx</span>
                                </div>
                                <div class="flex justify-between py-[3px] bg-surface-container-low px-space-xs">
                                    <span class="text-on-surface-variant">Last Auth Activity:</span>
                                    <span class="font-bold text-error">Today, 03:14 UTC+6</span>
                                </div>
                                <div class="flex justify-between py-[3px] bg-surface-container-low px-space-xs">
                                    <span class="text-on-surface-variant">Department Sponsor:</span>
                                    <span class="font-bold text-error">NONE (EXPIRED DEPT-08)</span>
                                </div>
                            </div>
                            <!-- Immediate Governance Enforcement Actions -->
                            <div class="flex flex-col gap-space-xs pt-space-xs">
                                <button
                                    class="w-full h-control-height-lg bg-error text-on-error font-body-compact text-body-compact font-bold uppercase tracking-wider flex items-center justify-center gap-space-xs hover:bg-on-error-container transition-colors shadow-sm"
                                    id="severCredentialsBtn">
                                    <span class="material-symbols-outlined text-[18px]">power_off</span>
                                    <span>Sever Credentials &amp; Purge</span>
                                </button>
                                <div class="grid grid-cols-2 gap-space-xs">
                                    <button id="drawerAuditLogBtn"
                                        class="h-control-height-md bg-surface-container-high hover:bg-surface-variant text-on-surface font-body-compact text-body-compact flex items-center justify-center gap-space-xs cursor-pointer">
                                        <span class="material-symbols-outlined text-[16px]">history</span>
                                        <span>Audit Log</span>
                                    </button>
                                    <button id="drawerTempHoldBtn"
                                        class="h-control-height-md bg-surface-container-high hover:bg-surface-variant text-on-surface font-body-compact text-body-compact flex items-center justify-center gap-space-xs cursor-pointer">
                                        <span class="material-symbols-outlined text-[16px]">lock_clock</span>
                                        <span>Temp Hold</span>
                                    </button>
                                </div>
                            </div>
                            <!-- Attestation Sign-off Checklist for this record -->
                            <div
                                class="p-space-sm bg-surface-container flex flex-col gap-space-xs font-telemetry-micro text-telemetry-micro">
                                <div class="flex items-center gap-space-xs">
                                    <input class="w-3.5 h-3.5 accent-primary cursor-pointer" id="operatorAckCheck"
                                        type="checkbox" />
                                    <label class="cursor-pointer text-on-surface" for="operatorAckCheck">Operator ACK:
                                        Mandatory revocation under DOC-2026-007</label>
                                </div>
                                <div class="text-[10px] text-on-surface-variant pl-space-base">
                                    Requires EMP-1005 cryptographic signature key before database write.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Attestation Sign-Off & Cryptographic Anchor Sticky Bar -->
                <div
                    class="bg-primary text-on-primary p-space-md shadow-md flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
                    <div class="flex items-center gap-space-md">
                        <div
                            class="w-10 h-10 bg-secondary flex items-center justify-center text-on-secondary flex-shrink-0">
                            <span class="material-symbols-outlined text-[24px]">vpn_key</span>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-space-sm">
                                <span
                                    class="font-security-stamp text-security-stamp uppercase tracking-widest text-secondary-fixed">LEDGER
                                    CRYPTOGRAPHIC ANCHOR</span>
                                <span
                                    class="px-space-xs py-[1px] bg-primary-container text-on-primary-container font-telemetry-micro text-[10px]">ECDSA-SECP256K1</span>
                            </div>
                            <div class="font-telemetry-data text-telemetry-micro text-on-primary tracking-wide">
                                SHA-256 (e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855)
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-space-base flex-wrap">
                        <div class="flex flex-col text-right">
                            <div class="font-telemetry-micro text-telemetry-micro text-secondary-fixed font-bold">
                                SIGNED BY EMP-1005 (T. AKHMETOV)
                            </div>
                            <div class="font-telemetry-micro text-[10px] text-on-primary-container">
                                RECORD TIMESTAMP: 2026-03-31 09:14:02 UTC+6 (ALMATY CENTRAL)
                            </div>
                        </div>
                        <button id="verifyLedgerIntegrityBtn"
                            class="h-control-height-md px-space-base bg-secondary hover:bg-secondary/90 text-on-secondary font-body-compact text-body-compact font-bold flex items-center gap-space-xs shadow-sm cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">verified</span>
                            <span>Verify Ledger Integrity</span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- ================= MODALS, DRAWERS & TOAST SYSTEM ================= -->
            <!-- 1. Bulk Attestation Sign-off Modal Backdrop -->
            <div class="fixed inset-0 z-50 bg-[#001020]/80 backdrop-blur-sm hidden items-center justify-center p-4"
                id="bulkAttestModalBackdrop">
                <div
                    class="w-full max-w-xl bg-[#0F172A] border border-[#1E293B] shadow-2xl overflow-hidden relative text-slate-100 flex flex-col font-body-default animate-in fade-in zoom-in-95 duration-200">
                    <!-- Hard Security Bar -->
                    <div class="h-1 w-full bg-gradient-to-r from-[#00F0FF] via-[#2563EB] to-[#00F0FF]"></div>
                    <!-- Modal Header -->
                    <div class="p-space-base border-b border-[#1E293B] flex items-center justify-between bg-[#0b1220]">
                        <div class="flex items-center gap-space-sm">
                            <div
                                class="w-8 h-8 rounded bg-[#1e293b] border border-[#334155] flex items-center justify-center text-[#00F0FF]">
                                <span class="material-symbols-outlined text-[18px]">verified_user</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-headline-md text-headline-md font-bold text-white tracking-tight">
                                        BULK ATTESTATION SIGN-OFF</h3>
                                    <span
                                        class="px-1.5 py-0.5 bg-[#1E293B] text-[#00F0FF] border border-[#00F0FF]/30 font-telemetry-micro text-[10px] uppercase">DOC-2026-007</span>
                                </div>
                                <p class="font-telemetry-micro text-[11px] text-slate-400">Statutory Quarterly Privilege
                                    Affirmation Interlock</p>
                            </div>
                        </div>
                        <button class="text-slate-400 hover:text-white p-1 transition-colors" id="closeBulkModalBtn"
                            type="button">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                    <!-- Modal Body -->
                    <div class="p-space-base flex flex-col gap-space-md">
                        <!-- Summary Scope Box -->
                        <div class="bg-[#131E35] border border-[#1E293B] p-space-sm flex flex-col gap-space-xs">
                            <div
                                class="flex items-center justify-between text-slate-300 font-label-uppercase text-[10px]">
                                <span class="text-[#00F0FF] font-bold">ATTESTATION SCOPE SUMMARY</span>
                                <span class="font-telemetry-micro text-amber-400 font-semibold">3 IDENTITIES
                                    ELIGIBLE</span>
                            </div>
                            <p class="font-body-compact text-[12px] text-slate-300 leading-snug">
                                Attesting validates clearance scope conformity under DOC-2026-007 for <span
                                    class="font-bold text-white">3 Pending Review Items</span> (out of 20 matrix
                                identities).
                            </p>
                            <!-- Identities List Table Strip -->
                            <div
                                class="mt-1 border border-[#1E293B] divide-y divide-[#1E293B] bg-[#0A101D] font-telemetry-micro text-[11px]">
                                <?php
                                $pendingList = array_filter($telemetryGrid, function ($e) {
                                    return $e['category'] === 'PENDING' || $e['category'] === 'ELEVATED';
                                });
                                if (empty($pendingList)) {
                                    $pendingList = array_slice($telemetryGrid, 0, 3);
                                }
                                foreach (array_slice($pendingList, 0, 3) as $pe):
                                ?>
                                    <div class="p-1.5 flex items-center justify-between text-slate-300">
                                        <span class="font-bold text-[#00F0FF]"><?= htmlspecialchars($pe['emp_id']) ?></span>
                                        <span><?= htmlspecialchars($pe['full_name']) ?> (<?= htmlspecialchars($pe['job_title']) ?>)</span>
                                        <span class="text-slate-400 font-mono"><?= htmlspecialchars($pe['clearance_level']) ?></span>
                                        <span class="text-amber-400 font-bold"><?= htmlspecialchars($pe['category']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <!-- Security PIN & Affirmation -->
                        <div class="flex flex-col gap-space-sm bg-[#0B1324] p-space-sm border border-[#1E293B]">
                            <label
                                class="font-label-uppercase text-[10px] text-slate-300 tracking-wider flex items-center justify-between"
                                for="attestPinInput">
                                <span>OPERATOR CLEARANCE PIN (6-DIGIT EMP-1005)</span>
                                <span class="text-[#00F0FF] text-[10px] font-mono">ECDSA HARDWARE TOKEN</span>
                            </label>
                            <div class="relative">
                                <input autocomplete="off"
                                    class="w-full bg-[#131E35] border border-[#2B3B55] px-3 py-2 text-white font-mono text-center tracking-[0.4em] text-lg rounded focus:border-[#00F0FF] focus:outline-none focus:ring-1 focus:ring-[#00F0FF]"
                                    id="attestPinInput" maxlength="6" placeholder="••••••" type="password"
                                    value="482910" />
                            </div>
                            <label class="flex items-start gap-2.5 mt-1 cursor-pointer select-none">
                                <input checked=""
                                    class="mt-0.5 w-4 h-4 rounded border-[#334155] bg-[#1E293B] text-[#2563EB] focus:ring-0 focus:ring-offset-0 cursor-pointer"
                                    id="complianceAffirmCheck" type="checkbox" />
                                <span class="font-body-compact text-[11px] text-slate-300 leading-tight">
                                    I hereby attest on corporate record that all reviewed privileges conform to
                                    statutory security directives DOC-2026-007 and ISO/IEC-27001 Annex A.9.
                                </span>
                            </label>
                        </div>
                        <!-- Signing progress feedback (hidden initially) -->
                        <div class="hidden flex-col gap-1.5 p-space-xs bg-[#0b1426] border border-[#2563EB]/40 rounded"
                            id="signingProgressContainer">
                            <div class="flex justify-between font-telemetry-micro text-[11px]">
                                <span class="text-[#00F0FF] font-bold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px] animate-spin">refresh</span>
                                    <span id="signingStepText">Hashing Ledger ECDSA-SECP256K1...</span>
                                </span>
                                <span class="text-slate-300 font-mono" id="signingProgressPercent">45%</span>
                            </div>
                            <div class="w-full h-1.5 bg-[#1E293B] rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-[#2563EB] to-[#00F0FF] transition-all duration-300 w-[45%]"
                                    id="signingProgressBar"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Footer Actions -->
                    <div
                        class="p-space-base bg-[#090F1C] border-t border-[#1E293B] flex items-center justify-end gap-space-sm">
                        <button
                            class="px-4 py-2 bg-[#1E293B] hover:bg-[#334155] text-slate-300 hover:text-white font-body-compact text-body-compact font-medium transition-colors"
                            id="cancelBulkModalBtn" type="button">
                            Abort
                        </button>
                        <button
                            class="px-5 py-2 bg-gradient-to-r from-[#2563EB] to-[#0094B3] hover:brightness-110 text-white font-body-compact text-body-compact font-bold flex items-center gap-2 shadow-lg shadow-blue-500/20 transition-all disabled:opacity-50"
                            id="executeBulkSignBtn" type="button">
                            <span class="material-symbols-outlined text-[16px]">draw</span>
                            <span>Confirm &amp; Sign Ledger</span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- 2. Re-Certification Window Slide-over Drawer Backdrop & Panel -->
            <div class="fixed inset-0 z-50 bg-[#001020]/70 backdrop-blur-xs hidden" id="reCertDrawerBackdrop">
                <div class="fixed top-0 right-0 bottom-0 w-full max-w-md bg-[#0F172A] border-l border-[#1E293B] shadow-2xl flex flex-col text-slate-100 transform transition-transform duration-300 translate-x-full"
                    id="reCertDrawerPanel">
                    <!-- Drawer Security Ribbon -->
                    <div class="h-1 w-full bg-[#00F0FF]"></div>
                    <!-- Drawer Header -->
                    <div class="p-space-base border-b border-[#1E293B] bg-[#0b1220] flex items-center justify-between">
                        <div class="flex items-center gap-space-sm">
                            <div
                                class="w-8 h-8 rounded bg-[#1e293b] border border-[#334155] flex items-center justify-center text-[#00F0FF]">
                                <span class="material-symbols-outlined text-[18px]">event_repeat</span>
                            </div>
                            <div>
                                <h3 class="font-headline-md text-headline-md font-bold text-white tracking-tight">
                                    RE-CERTIFICATION WINDOW</h3>
                                <p class="font-telemetry-micro text-[11px] text-slate-400">Quarterly Policy Review Cycle
                                    Parameters</p>
                            </div>
                        </div>
                        <button class="text-slate-400 hover:text-white p-1 transition-colors" id="closeReCertDrawerBtn"
                            type="button">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                    <!-- Drawer Body -->
                    <div class="flex-1 overflow-y-auto p-space-base flex flex-col gap-space-md">
                        <!-- Window Status Toggle -->
                        <div
                            class="bg-[#131E35] border border-[#1E293B] p-space-base flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="font-label-uppercase text-[10px] text-slate-400">GOVERNANCE
                                    ENFORCEMENT</span>
                                <span class="font-bold text-white text-[14px]" id="drawerStatusText">Window State:
                                    Active</span>
                                <span class="font-telemetry-micro text-[11px] text-[#00F0FF]" id="drawerStatusSub">All
                                    L2-L5 authorizations enforced</span>
                            </div>
                            <button
                                class="w-12 h-6 bg-[#2563EB] rounded-full p-0.5 transition-colors relative focus:outline-none"
                                id="windowStatusToggle" role="switch" type="button">
                                <div class="w-5 h-5 bg-white rounded-full transition-transform translate-x-6"
                                    id="toggleKnob"></div>
                            </button>
                        </div>
                        <!-- Date Pickers (Prefilled Q2-2026) -->
                        <div class="flex flex-col gap-space-xs">
                            <span class="font-label-uppercase text-[10px] text-slate-300 tracking-wider">REVIEW DURATION
                                (Q2-2026 STATUTORY)</span>
                            <div class="grid grid-cols-2 gap-space-sm font-telemetry-micro">
                                <div class="flex flex-col gap-1">
                                    <span class="text-[10px] text-slate-400">Start Date</span>
                                    <input
                                        class="bg-[#131E35] border border-[#2B3B55] px-2.5 py-1.5 text-white text-[12px] rounded focus:border-[#00F0FF] focus:outline-none"
                                        id="recertStartDate" type="date" value="2026-04-01" />
                                </div>
                                <div class="flex flex-col gap-1">
                                    <span class="text-[10px] text-slate-400">End Date</span>
                                    <input
                                        class="bg-[#131E35] border border-[#2B3B55] px-2.5 py-1.5 text-white text-[12px] rounded focus:border-[#00F0FF] focus:outline-none"
                                        id="recertEndDate" type="date" value="2026-04-30" />
                                </div>
                            </div>
                        </div>
                        <!-- Scope Multi-Select Jurisdictions -->
                        <div class="flex flex-col gap-space-xs">
                            <span class="font-label-uppercase text-[10px] text-slate-300 tracking-wider">MANDATORY AUDIT
                                JURISDICTIONS</span>
                            <div class="flex flex-wrap gap-1.5" id="scopeTagGroup">
                                <button
                                    class="scope-chip px-2.5 py-1 bg-[#2563EB] text-white border border-[#3B82F6] font-telemetry-micro text-[11px] rounded flex items-center gap-1"
                                    type="button">
                                    <span class="material-symbols-outlined text-[12px]">check</span> Almaty Central
                                </button>
                                <button
                                    class="scope-chip px-2.5 py-1 bg-[#2563EB] text-white border border-[#3B82F6] font-telemetry-micro text-[11px] rounded flex items-center gap-1"
                                    type="button">
                                    <span class="material-symbols-outlined text-[12px]">check</span> Karaganda Plant
                                </button>
                                <button
                                    class="scope-chip px-2.5 py-1 bg-[#2563EB] text-white border border-[#3B82F6] font-telemetry-micro text-[11px] rounded flex items-center gap-1"
                                    type="button">
                                    <span class="material-symbols-outlined text-[12px]">check</span> Ust-Kamenogorsk
                                </button>
                                <button
                                    class="scope-chip px-2.5 py-1 bg-[#2563EB] text-white border border-[#3B82F6] font-telemetry-micro text-[11px] rounded flex items-center gap-1"
                                    type="button">
                                    <span class="material-symbols-outlined text-[12px]">check</span> SYS-01 to SYS-11
                                </button>
                                <button
                                    class="scope-chip px-2.5 py-1 bg-[#1E293B] text-slate-300 border border-[#334155] font-telemetry-micro text-[11px] rounded flex items-center gap-1"
                                    type="button">
                                    <span>+ Khorgos Dry Port</span>
                                </button>
                            </div>
                        </div>
                        <!-- Automated Reminders Option -->
                        <div class="bg-[#131E35] border border-[#1E293B] p-space-sm flex flex-col gap-space-xs">
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input checked=""
                                    class="mt-0.5 w-4 h-4 rounded border-[#334155] bg-[#1E293B] text-[#00F0FF] focus:ring-0 cursor-pointer"
                                    id="recertSlaNoticeCheck" type="checkbox" />
                                <span class="font-body-compact text-[12px] text-slate-200">
                                    Send automated SLA warnings to department sponsors (<strong
                                        class="text-[#00F0FF]">EMP-1005 &amp; EMP-1018</strong>)
                                </span>
                            </label>
                            <p class="pl-6 font-telemetry-micro text-[10px] text-slate-400">
                                Escalations trigger at T-7 days, T-48h, and T-12h prior to re-certification lockdown.
                            </p>
                        </div>
                        <!-- Audit Metadata Info -->
                        <div
                            class="p-space-sm bg-[#0B1324] border border-[#1E293B] text-[11px] font-telemetry-micro text-slate-400 flex flex-col gap-1">
                            <div class="flex justify-between">
                                <span>Authority Hash:</span>
                                <span class="text-slate-200">SHA256:4ca495...7852</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Compliance Mandate:</span>
                                <span class="text-amber-400">SEC-GOV-2026.Q2</span>
                            </div>
                        </div>
                    </div>
                    <!-- Drawer Footer Actions -->
                    <div
                        class="p-space-base bg-[#090F1C] border-t border-[#1E293B] flex items-center justify-end gap-space-sm">
                        <button
                            class="px-4 py-2 bg-[#1E293B] hover:bg-[#334155] text-slate-300 hover:text-white font-body-compact text-body-compact font-medium transition-colors"
                            id="cancelReCertDrawerBtn" type="button">
                            Cancel
                        </button>
                        <button
                            class="px-5 py-2 bg-[#00F0FF] hover:bg-[#00d6e6] text-[#002444] font-body-compact text-body-compact font-bold flex items-center gap-1.5 shadow-md transition-all"
                            id="saveReCertWindowBtn" type="button">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            <span>Save &amp; Launch Window</span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- 3. Global Cyber Toast Container -->
            <div class="fixed bottom-6 right-6 z-50 flex flex-col gap-2 pointer-events-none" id="toastHub"></div>

        </main>
    </div>
    <script src="js/common.js"></script>
    <script src="js/dashboard.js"></script>
  <script src="../assets/js/notifications-hub.js" defer></script>
</body>

</html>