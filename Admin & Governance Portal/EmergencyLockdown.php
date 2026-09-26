<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('ADM');
require_once __DIR__ . '/gov_service.php';

$currentUser = gov_getActiveUserProfile();
$systemsLockdown = gov_getSystemsLockdownMatrix();
$metrics = gov_getGovernanceMetrics();
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>VOSTOKPRIBOR Administration &amp; Governance Portal - System 11</title>
    <link rel="stylesheet" href="css/common.css" />
    <link rel="stylesheet" href="css/emergencyLockdown.css" />
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
                                class="font-telemetry-micro text-telemetry-micro font-bold text-on-primary">Timur
                                Akhmetov</span><span
                                class="font-label-uppercase text-label-uppercase text-secondary-fixed bg-secondary-container/20 px-space-2xs rounded">EMP-1005</span>
                        </div><span class="font-telemetry-micro text-[10px] text-on-primary-container">CLEARANCE: LEVEL
                            5 (ALMATY CENTRAL)</span>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center"><span
                            class="material-symbols-outlined text-on-primary text-[18px]">person</span></div>
                </div>
            </div>

            <!-- Top Bar Sign Out -->
            <a href="../api/logout.php?system=Admin%20%26%20Governance%20Portal&redirect=../Admin%20%26%20Governance%20Portal/login.php" class="top-signout-btn" title="Sign Out of Admin &amp; Governance Portal" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" ><span class="material-symbols-outlined">logout</span><span>Sign Out</span></a>
        </div>
    </header>
    <aside
        class="fixed left-0 top-[60px] h-[calc(100vh-60px)] w-[260px] bg-primary z-40 flex flex-col justify-between border-r border-outline/30 select-none overflow-y-auto">
        <div class="py-space-md">
            <div class="px-space-md mb-space-xs"><span
                    class="font-label-uppercase text-label-uppercase text-on-primary-container tracking-wider">CORE
                    GOVERNANCE</span></div>
            <nav class="flex flex-col gap-[2px] px-space-xs mb-space-md"
                data-active-classes="bg-primary-container text-on-primary font-semibold border-l-4 border-secondary-fixed">
                <a class="flex items-center justify-between px-space-sm py-space-xs rounded text-on-primary-container hover:bg-primary-container hover:text-on-primary transition-all font-body-compact text-body-compact"
                    data-path="dashboard" href="mainDashborde.php">
                    <div class="flex items-center gap-space-sm"><span
                            class="material-symbols-outlined text-[18px]">dashboard</span><span>Main Dashboard</span>
                    </div><span
                        class="font-telemetry-micro text-[10px] px-space-2xs bg-secondary-container/20 text-secondary-fixed rounded">KPI
                        &amp; Threat</span>
                </a><a
                    class="flex items-center justify-between px-space-sm py-space-xs rounded text-on-primary-container hover:bg-primary-container hover:text-on-primary transition-all font-body-compact text-body-compact"
                    data-path="access-matrix-and-role-review" href="accessMatrix.php">
                    <div class="flex items-center gap-space-sm"><span
                            class="material-symbols-outlined text-[18px]">grid_view</span><span>Access Matrix</span>
                    </div><span
                        class="font-telemetry-micro text-[10px] px-space-2xs bg-error-container text-on-error-container rounded font-bold">2
                        Orphaned</span>
                </a><a
                    class="flex items-center justify-between px-space-sm py-space-xs rounded text-on-primary-container hover:bg-primary-container hover:text-on-primary transition-all font-body-compact text-body-compact"
                    data-path="privileged-accounts-monitoring" href="PrivilegedAccounts.php">
                    <div class="flex items-center gap-space-sm"><span
                            class="material-symbols-outlined text-[18px]">admin_panel_settings</span><span>Privileged
                            Accounts</span></div><span
                        class="font-telemetry-micro text-[10px] px-space-2xs bg-surface-variant/20 text-on-primary-container rounded">7
                        Active</span>
                </a>
            </nav>
            <div class="px-space-md mb-space-xs"><span
                    class="font-label-uppercase text-label-uppercase text-on-primary-container tracking-wider">AUDIT
                    &amp; INTELLIGENCE</span></div>
            <nav class="flex flex-col gap-[2px] px-space-xs mb-space-md"
                data-active-classes="bg-primary-container text-on-primary font-semibold border-l-4 border-secondary-fixed">
                <a aria-current="page"
                    class="flex items-center justify-between px-space-sm py-space-xs rounded transition-all bg-primary-container text-on-primary font-semibold border-l-4 border-secondary-fixed"
                    data-path="audit-logs-and-event-streams" href="AuditLogs.php">
                    <div class="flex items-center gap-space-sm"><span
                            class="material-symbols-outlined text-[18px]">terminal</span><span>Audit Logs</span></div>
                    <span
                        class="font-telemetry-micro text-[10px] px-space-2xs bg-secondary-fixed text-on-secondary-fixed font-bold rounded animate-pulse">LIVE</span>
                </a><a
                    class="flex items-center justify-between px-space-sm py-space-xs rounded text-on-primary-container hover:bg-primary-container hover:text-on-primary transition-all font-body-compact text-body-compact"
                    data-path="ingestion-bridges" href="IngestionBridges.php">
                    <div class="flex items-center gap-space-sm"><span
                            class="material-symbols-outlined text-[18px]">cable</span><span>Ingestion Bridges</span>
                    </div><span class="font-telemetry-micro text-[10px] text-on-primary-container">01-10</span>
                </a><a
                    class="flex items-center justify-between px-space-sm py-space-xs rounded text-error hover:bg-error-container hover:text-on-error-container transition-all font-body-compact text-body-compact"
                    data-path="emergency-break-glass" href="Break-GlassAccess.php">
                    <div class="flex items-center gap-space-sm"><span
                            class="material-symbols-outlined text-[18px] text-error">e911_emergency</span><span
                            class="font-bold uppercase">Break-Glass Access</span></div><span
                        class="material-symbols-outlined text-[16px] text-error">lock_open</span>
                </a>
                <a class="flex items-center justify-between px-space-sm py-space-xs rounded text-error hover:bg-error-container hover:text-on-error-container transition-all font-body-compact text-body-compact" data-path="emergency-lockdown" href="EmergencyLockdown.php">
                    <div class="flex items-center gap-space-sm"><span class="material-symbols-outlined text-[18px] text-error">lock</span><span class="font-bold uppercase">Emergency Lockdown</span></div><span class="font-telemetry-micro text-[10px] px-space-2xs bg-error text-on-error font-bold rounded">DEFCON-1</span>
                </a>
            </nav>
            <div class="px-space-md mb-space-xs"><span
                    class="font-label-uppercase text-label-uppercase text-on-primary-container tracking-wider">REGULATORY
                    &amp; RISK</span></div>
            <nav class="flex flex-col gap-[2px] px-space-xs mb-space-md"
                data-active-classes="bg-primary-container text-on-primary font-semibold border-l-4 border-secondary-fixed">
                <a class="flex items-center justify-between px-space-sm py-space-xs rounded text-on-primary-container hover:bg-primary-container hover:text-on-primary transition-all font-body-compact text-body-compact"
                    data-path="enterprise-security-policies" href="SecurityPolicies.php">
                    <div class="flex items-center gap-space-sm"><span
                            class="material-symbols-outlined text-[18px]">policy</span><span>Security Policies</span>
                    </div>
                </a><a
                    class="flex items-center justify-between px-space-sm py-space-xs rounded text-on-primary-container hover:bg-primary-container hover:text-on-primary transition-all font-body-compact text-body-compact"
                    data-path="board-risk-register" href="BoardRiskRegister.php">
                    <div class="flex items-center gap-space-sm"><span
                            class="material-symbols-outlined text-[18px]">balance</span><span>Board Risk Register</span>
                    </div><span
                        class="font-telemetry-micro text-[9px] px-space-2xs bg-primary-container text-on-primary-container rounded">2026-015</span>
                </a><a
                    class="flex items-center justify-between px-space-sm py-space-xs rounded text-on-primary-container hover:bg-primary-container hover:text-on-primary transition-all font-body-compact text-body-compact"
                    data-path="compliance-and-incident-oversight" href="ComplianceOversight.php">
                    <div class="flex items-center gap-space-sm"><span
                            class="material-symbols-outlined text-[18px]">gavel</span><span>Compliance Oversight</span>
                    </div>
                </a>
            </nav>
        </div>

        <div class="p-space-md bg-primary-container/40 border-t border-outline/20 flex flex-col gap-space-2xs">
            <div class="flex items-center justify-between"><span
                    class="font-security-stamp text-[10px] text-secondary-fixed-dim uppercase tracking-wider">SEC-OPS
                    FACILITY</span>
                <div class="w-1.5 h-1.5 rounded-full bg-secondary-fixed"></div>
            </div>
            <div class="font-telemetry-micro text-telemetry-micro text-on-primary-container">ALMATY STATION • EST. 1968
            </div>
            <div
                class="font-telemetry-data text-telemetry-data text-on-primary font-semibold tracking-wider pt-space-2xs">
                <span class="station-live-clock">UTC+6 (ALMATY TIME)</span>
            </div>
        </div>
    </aside>
    <div class="pl-[260px]">
        <main class="relative pt-[60px] w-full min-h-screen bg-surface px-gutter-desktop py-space-lg">
            <div class="flex flex-col w-full">
                <!-- BREADCRUMB & METADATA BAR -->
                <div class="flex flex-wrap items-center justify-between gap-space-sm pb-space-sm">
                    <div
                        class="flex items-center gap-space-xs font-telemetry-micro text-telemetry-micro text-on-surface-variant uppercase tracking-wider">
                        <span class="hover:text-primary cursor-pointer">ROOT</span>
                        <span>/</span>
                        <span class="hover:text-primary cursor-pointer">GOVERNANCE</span>
                        <span>/</span>
                        <span class="text-on-surface font-semibold">INCIDENT ESCALATION</span>
                        <span>//</span>
                        <span
                            class="text-error font-bold tracking-widest bg-error-container text-on-error-container px-space-xs py-[1px] rounded">
                            DOC-2026-LOCK-01 (ACTIVE RESPONSE)
                        </span>
                    </div>
                    <div class="flex items-center gap-space-md font-security-stamp text-security-stamp">
                        <div class="flex items-center gap-space-xs text-on-surface-variant">
                            <span class="material-symbols-outlined text-[14px] text-tertiary">lock_clock</span>
                            <span>ISOLATION BUFFER: <span
                                    class="text-on-surface font-telemetry-data text-telemetry-data">00:04:18.91</span></span>
                        </div>
                        <div class="w-[1px] h-3 bg-outline-variant/60"></div>
                        <div class="flex items-center gap-space-xs text-on-surface-variant">
                            <span class="material-symbols-outlined text-[14px] text-secondary">satellite_alt</span>
                            <span>STATION ALMATY-01 // COLD-RUNNER</span>
                        </div>
                    </div>
                </div>
                <!-- EMERGENCY LOCKDOWN MASTER BANNER -->
                <div
                    class="relative overflow-hidden bg-primary text-on-primary rounded-lg shadow-md p-space-md mb-space-base">
                    <!-- Hazard diagonal background accent line (pure CSS pattern) -->
                    <div
                        class="absolute inset-0 opacity-10 pointer-events-none bg-[radial-gradient(#96eef9_1px,transparent_1px)] [background-size:16px_16px]">
                    </div>
                    <div
                        class="relative flex flex-col xl:flex-row items-start xl:items-center justify-between gap-space-md">
                        <!-- Title & Live Flare -->
                        <div class="flex items-start gap-space-md">
                            <div
                                class="relative flex items-center justify-center w-12 h-12 rounded bg-error/20 text-error flex-shrink-0 mt-space-2xs">
                                <span class="material-symbols-outlined text-[28px] animate-pulse">crisis_alert</span>
                                <span
                                    class="absolute -top-1 -right-1 w-3 h-3 bg-error rounded-full animate-ping"></span>
                            </div>
                            <div class="flex flex-col">
                                <div class="flex flex-wrap items-center gap-space-xs mb-space-2xs">
                                    <span
                                        class="font-security-stamp text-[10px] bg-error text-on-error px-space-xs py-[2px] rounded uppercase tracking-widest font-extrabold flex items-center gap-1">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full bg-surface-container-lowest animate-ping"></span>
                                        DEFCON-1 ARMED - ACTIVE ISOLATION PROTOCOL
                                    </span>
                                    <span
                                        class="font-telemetry-micro text-telemetry-micro text-secondary-fixed bg-primary-container px-space-xs py-[2px] rounded">
                                        INTERLOCK CHASSIS: DUAL-HSM ACTIVE
                                    </span>
                                    <span class="font-telemetry-micro text-telemetry-micro text-on-primary-container">
                                        CLASSIFICATION: KZ-STATE-SECRET // SEVERE DISRUPTION
                                    </span>
                                </div>
                                <h1 class="font-headline-lg text-headline-lg tracking-tight text-on-primary font-bold">
                                    EMERGENCY LOCKDOWN &amp; SYSTEM DEFENSIVE QUARANTINE
                                </h1>
                                <p
                                    class="font-body-compact text-body-compact text-on-primary-container max-w-3xl mt-[2px]">
                                    Executing sovereign air-gap isolation across all connected telemetry bridges.
                                    Upstream SCADA, remote actuator bus relays, and external Bastion proxies are forced
                                    into unmodifiable immutable WORM write-block buffers.
                                </p>
                            </div>
                        </div>
                        <!-- Action Buttons Bar -->
                        <div class="flex flex-wrap items-center gap-space-xs xl:self-center flex-shrink-0">
                            <button
                                class="h-control-height-md px-space-base bg-error text-on-error hover:bg-on-error-container transition-all font-body-default text-body-default font-semibold rounded flex items-center gap-space-xs shadow-sm"
                                id="btnAbortLockdown">
                                <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                                <span>Abort Lockdown (Dual HSM)</span>
                            </button>
                            <button
                                class="h-control-height-md px-space-base bg-primary-container text-on-primary hover:bg-on-primary-fixed-variant transition-all font-body-default text-body-default font-semibold rounded flex items-center gap-space-xs shadow-sm"
                                id="btnExportDossier">
                                <span class="material-symbols-outlined text-[18px]">file_download</span>
                                <span>Export Incident Dossier</span>
                            </button>
                            <button
                                class="h-control-height-md px-space-base bg-secondary text-on-secondary hover:bg-on-secondary-container transition-all font-body-default text-body-default font-semibold rounded flex items-center gap-space-xs shadow-sm"
                                id="btnDispatchAlert">
                                <span class="material-symbols-outlined text-[18px]">cell_tower</span>
                                <span>Broadcast Sec-Ops Alert</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- TOP INCIDENT & DEFENSE METRICS ROW (4 Classification Strips) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-space-md mb-space-base">
                    <!-- Card 1: DEFCON Posture (Alert Red strip) -->
                    <div
                        class="relative bg-surface-container-lowest rounded shadow-sm p-space-md flex flex-col justify-between overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-error"></div>
                        <div class="pl-space-xs">
                            <div class="flex items-center justify-between mb-space-xs">
                                <span class="font-security-stamp text-security-stamp text-error uppercase">DEFCON
                                    POSTURE</span>
                                <span
                                    class="font-label-uppercase text-label-uppercase px-space-xs py-[2px] bg-error-container text-on-error-container rounded font-bold">STATE
                                    CRITICAL</span>
                            </div>
                            <div class="flex items-baseline gap-space-xs my-space-xs">
                                <span
                                    class="font-display-lg text-display-lg text-error font-extrabold tracking-tight">DEFCON-1</span>
                                <span
                                    class="font-telemetry-micro text-telemetry-micro text-on-surface-variant uppercase font-medium">/
                                    LEVEL 1 MAX</span>
                            </div>
                            <div class="font-body-compact text-body-compact text-on-surface-variant">
                                Maximum telemetry quarantine engaged. Mechanical acoustic siren active at Almaty
                                Station.
                            </div>
                        </div>
                        <div
                            class="mt-space-sm pt-space-xs bg-surface-container-low rounded px-space-xs py-space-2xs flex items-center justify-between text-on-surface font-telemetry-micro text-telemetry-micro">
                            <span class="text-on-surface-variant">ALARM HARNESS</span>
                            <span class="text-error font-semibold flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-error animate-ping"></span>
                                ACOUSTIC_LATCHED
                            </span>
                        </div>
                    </div>
                    <!-- Card 2: Hardware Quorum / Dual Custody (Signal Amber strip) -->
                    <div
                        class="relative bg-surface-container-lowest rounded shadow-sm p-space-md flex flex-col justify-between overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-on-tertiary-container"></div>
                        <div class="pl-space-xs">
                            <div class="flex items-center justify-between mb-space-xs">
                                <span class="font-security-stamp text-security-stamp text-tertiary uppercase">HARDWARE
                                    QUORUM</span>
                                <span
                                    class="font-label-uppercase text-label-uppercase px-space-xs py-[2px] bg-tertiary-fixed text-on-tertiary-fixed-variant rounded font-bold">FIPS-140-3
                                    LOCK</span>
                            </div>
                            <div class="flex items-baseline gap-space-xs my-space-xs">
                                <span
                                    class="font-display-lg text-display-lg text-on-surface font-extrabold tracking-tight">1
                                    <span class="text-outline-variant font-normal text-headline-lg">/ 2</span></span>
                                <span
                                    class="font-telemetry-micro text-telemetry-micro text-tertiary font-bold uppercase">KEYS
                                    ENGAGED</span>
                            </div>
                            <div class="font-body-compact text-body-compact text-on-surface-variant">
                                Akhmetov (EMP-1005) <span class="text-secondary font-semibold">[VERIFIED]</span>.
                                Sadykova (EMP-1018) <span class="text-error font-semibold">[AWAITING PIN]</span>.
                            </div>
                        </div>
                        <div
                            class="mt-space-sm pt-space-xs bg-surface-container-low rounded px-space-xs py-space-2xs flex items-center justify-between text-on-surface font-telemetry-micro text-telemetry-micro">
                            <span class="text-on-surface-variant">QUORUM RATIO</span>
                            <span class="text-tertiary font-semibold font-telemetry-data">50.0% OF COLD-KEY</span>
                        </div>
                    </div>
                    <!-- Card 3: Quarantine Reach (Internal Blue strip) -->
                    <div
                        class="relative bg-surface-container-lowest rounded shadow-sm p-space-md flex flex-col justify-between overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary"></div>
                        <div class="pl-space-xs">
                            <div class="flex items-center justify-between mb-space-xs">
                                <span class="font-security-stamp text-security-stamp text-primary uppercase">QUARANTINE
                                    REACH</span>
                                <span
                                    class="font-label-uppercase text-label-uppercase px-space-xs py-[2px] bg-primary-fixed text-on-primary-fixed rounded font-bold">INGESTION
                                    MATRIX</span>
                            </div>
                            <div class="flex items-baseline gap-space-xs my-space-xs">
                                <span
                                    class="font-display-lg text-display-lg text-primary font-extrabold tracking-tight">10
                                    <span class="text-outline-variant font-normal text-headline-lg">/ 10</span></span>
                                <span
                                    class="font-telemetry-micro text-telemetry-micro text-secondary font-bold uppercase">BRIDGES
                                    SECURED</span>
                            </div>
                            <div class="font-body-compact text-body-compact text-on-surface-variant">
                                10 nodes shifted into Inbound-Only Zero-Write WORM buffer. Outbound actuator lines
                                severed.
                            </div>
                        </div>
                        <div
                            class="mt-space-sm pt-space-xs bg-surface-container-low rounded px-space-xs py-space-2xs flex items-center justify-between text-on-surface font-telemetry-micro text-telemetry-micro">
                            <span class="text-on-surface-variant">BUFFER WRITE-PROTECT</span>
                            <span class="text-secondary font-semibold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-secondary">check_circle</span>
                                WORM HARDWARE ACTIVE
                            </span>
                        </div>
                    </div>
                    <!-- Card 4: State Reporting Dispatch (Precision Teal strip) -->
                    <div
                        class="relative bg-surface-container-lowest rounded shadow-sm p-space-md flex flex-col justify-between overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-secondary"></div>
                        <div class="pl-space-xs">
                            <div class="flex items-center justify-between mb-space-xs">
                                <span class="font-security-stamp text-security-stamp text-secondary uppercase">STATE
                                    DISPATCH</span>
                                <span
                                    class="font-label-uppercase text-label-uppercase px-space-xs py-[2px] bg-secondary-container text-on-secondary-container rounded font-bold">KZ-CERT
                                    SYNC</span>
                            </div>
                            <div class="flex items-baseline gap-space-xs my-space-xs">
                                <span
                                    class="font-display-lg text-display-lg text-secondary font-extrabold tracking-tight">KZ-CERT</span>
                                <span
                                    class="font-telemetry-micro text-telemetry-micro text-on-surface-variant uppercase font-medium">/
                                    DISPATCH-01</span>
                            </div>
                            <div class="font-body-compact text-body-compact text-on-surface-variant">
                                Signed ECDSA telemetric packet queued for sovereign regulatory incident registry.
                            </div>
                        </div>
                        <div
                            class="mt-space-sm pt-space-xs bg-surface-container-low rounded px-space-xs py-space-2xs flex items-center justify-between text-on-surface font-telemetry-micro text-telemetry-micro">
                            <span class="text-on-surface-variant">PAYLOAD HASH</span>
                            <span class="text-on-surface font-telemetry-data font-semibold">9A2F...4B8C</span>
                        </div>
                    </div>
                </div>
                <!-- MAIN OPERATIONAL GRID (70 / 30 LAYOUT) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-base mb-space-base items-start">
                    <!-- LEFT COLUMN: INDUSTRIAL NODE ISOLATION MATRIX (8 cols) -->
                    <div class="lg:col-span-8 flex flex-col gap-space-base">
                        <!-- Panel 1: Industrial Node Isolation Matrix (SYS-01 to SYS-10) -->
                        <div class="bg-surface-container-lowest rounded shadow-sm overflow-hidden">
                            <div
                                class="bg-surface-container px-space-base py-space-sm flex flex-wrap items-center justify-between gap-space-xs">
                                <div class="flex items-center gap-space-sm">
                                    <span class="material-symbols-outlined text-[20px] text-primary">hub</span>
                                    <h2 class="font-title-sm text-title-sm font-semibold text-on-surface">
                                        Industrial Node Isolation Matrix (SYS-01 to SYS-10)
                                    </h2>
                                    <span
                                        class="font-telemetry-micro text-[10px] bg-primary-container text-on-primary px-space-xs py-[1px] rounded font-mono">
                                        ZERO-WRITE WORM
                                    </span>
                                </div>
                                <div class="flex items-center gap-space-xs">
                                    <button
                                        class="h-control-height-sm px-space-sm bg-primary text-on-primary hover:bg-primary-container transition-all font-body-compact text-body-compact font-semibold rounded flex items-center gap-1 shadow-sm"
                                        id="btnIsolateAll">
                                        <span class="material-symbols-outlined text-[14px]">shield</span>
                                        <span>Enforce Full Air-Gap (All 10)</span>
                                    </button>
                                </div>
                            </div>
                            <!-- Density Table of Nodes -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr
                                            class="bg-surface-container-low text-on-surface-variant font-label-uppercase text-label-uppercase">
                                            <th class="py-space-xs px-space-base text-left">NODE ID &amp; FACILITY</th>
                                            <th class="py-space-xs px-space-sm text-center">TELEMETRY STREAM</th>
                                            <th class="py-space-xs px-space-sm text-left">FAIL-SAFE MODE</th>
                                            <th class="py-space-xs px-space-sm text-right">BUFFER DRAIN</th>
                                            <th class="py-space-xs px-space-sm text-right">HOLD LATENCY</th>
                                            <th class="py-space-xs px-space-base text-right">ISOLATION SWITCH</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="font-body-compact text-body-compact divide-y divide-surface-container-low">
                                        <?php foreach ($systemsLockdown as $s):
                                            $sysId = $s['system_id'];
                                            $sysName = $s['system_name'];
                                            $sysCode = $s['system_code'] ?? $s['fqdn'] ?? $s['system_id'];
                                        ?>
                                            <tr class="hover:bg-surface-container-low/70 transition-colors">
                                                <td class="py-space-xs px-space-base">
                                                    <div class="flex items-center gap-space-xs">
                                                        <span class="font-telemetry-data text-telemetry-data font-bold text-primary"><?= htmlspecialchars($sysId) ?></span>
                                                        <span class="text-on-surface font-medium"><?= htmlspecialchars($sysName) ?></span>
                                                    </div>
                                                    <span class="font-telemetry-micro text-[10px] text-on-surface-variant"><?= htmlspecialchars($sysCode) ?> // ENCLAVE AIR-GAP PROTOCOL</span>
                                                </td>
                                                <td class="py-space-xs px-space-sm text-center">
                                                    <span class="inline-flex items-center gap-1 px-space-xs py-[2px] bg-secondary-container/50 text-on-secondary-container font-telemetry-micro text-[10px] font-bold rounded">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                                                        INBOUND-ONLY
                                                    </span>
                                                </td>
                                                <td class="py-space-xs px-space-sm">
                                                    <span class="font-telemetry-micro text-telemetry-micro font-semibold text-on-surface">Immutable Read-Only</span>
                                                </td>
                                                <td class="py-space-xs px-space-sm text-right font-telemetry-data text-telemetry-data">
                                                    0.00 MB/s Out
                                                </td>
                                                <td class="py-space-xs px-space-sm text-right font-telemetry-data text-telemetry-data text-on-surface-variant">
                                                    0.8 ms
                                                </td>
                                                <td class="py-space-xs px-space-base text-right">
                                                    <span class="px-space-xs py-[2px] bg-primary-container text-on-primary-container font-security-stamp text-[9px] rounded uppercase font-bold">LOCKED</span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Panel 2: Automated Defensive Measures Engaged -->
                        <div class="bg-surface-container-lowest rounded shadow-sm p-space-base">
                            <div
                                class="flex items-center justify-between mb-space-sm pb-space-xs border-b border-surface-container-high">
                                <div class="flex items-center gap-space-xs">
                                    <span
                                        class="material-symbols-outlined text-[18px] text-secondary">fitbit_jumping_jacks</span>
                                    <h3 class="font-title-sm text-title-sm font-semibold text-on-surface">
                                        Automated Defensive Interlocks Triggered
                                    </h3>
                                </div>
                                <span class="font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                                    EXECUTION PIPELINE: <span class="text-primary font-bold">IMMUTABLE_STAGE_3</span>
                                </span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-space-sm">
                                <!-- Defense Item 1 -->
                                <div class="p-space-sm bg-surface-container-low rounded flex items-start gap-space-sm">
                                    <div
                                        class="w-5 h-5 rounded bg-secondary text-on-secondary flex items-center justify-center flex-shrink-0 mt-[2px]">
                                        <span class="material-symbols-outlined text-[14px]">check</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-body-compact text-body-compact font-semibold text-on-surface">
                                            Contractor &amp; Tier-3/4 Credentials Revocation
                                        </span>
                                        <p
                                            class="font-telemetry-micro text-telemetry-micro text-on-surface-variant mt-[2px]">
                                            142 active SSH &amp; Kerberos tokens invalidated across all proxy gateways.
                                        </p>
                                    </div>
                                </div>
                                <!-- Defense Item 2 -->
                                <div class="p-space-sm bg-surface-container-low rounded flex items-start gap-space-sm">
                                    <div
                                        class="w-5 h-5 rounded bg-secondary text-on-secondary flex items-center justify-center flex-shrink-0 mt-[2px]">
                                        <span class="material-symbols-outlined text-[14px]">check</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-body-compact text-body-compact font-semibold text-on-surface">
                                            Bastion SSH Shadow-Mode Enforcement
                                        </span>
                                        <p
                                            class="font-telemetry-micro text-telemetry-micro text-on-surface-variant mt-[2px]">
                                            All interactive administrative shells converted into read-only mirrored log
                                            sinks.
                                        </p>
                                    </div>
                                </div>
                                <!-- Defense Item 3 -->
                                <div class="p-space-sm bg-surface-container-low rounded flex items-start gap-space-sm">
                                    <div
                                        class="w-5 h-5 rounded bg-secondary text-on-secondary flex items-center justify-center flex-shrink-0 mt-[2px]">
                                        <span class="material-symbols-outlined text-[14px]">check</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-body-compact text-body-compact font-semibold text-on-surface">
                                            Tamper-Evident ECDSA Ledger Freeze
                                        </span>
                                        <p
                                            class="font-telemetry-micro text-telemetry-micro text-on-surface-variant mt-[2px]">
                                            Cryptographic Merkel tree locked to Root Block #4,891,012 with sovereign
                                            hardware signature.
                                        </p>
                                    </div>
                                </div>
                                <!-- Defense Item 4 -->
                                <div class="p-space-sm bg-error-container/40 rounded flex items-start gap-space-sm">
                                    <div
                                        class="w-5 h-5 rounded bg-error text-on-error flex items-center justify-center flex-shrink-0 mt-[2px]">
                                        <span class="material-symbols-outlined text-[14px]">priority_high</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-body-compact text-body-compact font-bold text-on-surface">
                                            Remote Firmware Flashing Relays Air-Gapped
                                        </span>
                                        <p
                                            class="font-telemetry-micro text-telemetry-micro text-error font-medium mt-[2px]">
                                            Physical galvanic decoupling verified on SYS-01 through SYS-10 logic boards.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Real-Time Syslog Stream Console -->
                        <div class="bg-surface-container-lowest rounded shadow-sm overflow-hidden">
                            <div
                                class="bg-primary text-on-primary px-space-base py-space-xs flex items-center justify-between">
                                <div class="flex items-center gap-space-xs">
                                    <span
                                        class="material-symbols-outlined text-[16px] text-secondary-fixed">terminal</span>
                                    <span
                                        class="font-security-stamp text-security-stamp uppercase tracking-wider text-on-primary">
                                        QUARANTINE_SYSLOG_STREAM // HIGH-VELOCITY AUDIT
                                    </span>
                                </div>
                                <div class="flex items-center gap-space-sm font-telemetry-micro text-telemetry-micro">
                                    <span class="text-secondary-fixed flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-secondary-fixed animate-ping"></span>
                                        BUFFER FLUSH: 1.4 kEV/s
                                    </span>
                                </div>
                            </div>
                            <div class="bg-inverse-surface p-space-sm font-telemetry-micro text-telemetry-micro text-inverse-on-surface font-mono h-48 overflow-y-auto space-y-1"
                                id="syslogConsole">
                                <div class="text-outline-variant font-mono">[15:43:58.112 UTC+6] SEC-CORE: SYSTEM
                                    DEFCON-1 THRESHOLD BREACHED (ANOMALOUS PAYLOAD INGESTED FROM SYS-03)</div>
                                <div class="text-tertiary-fixed-dim font-mono">[15:44:00.201 UTC+6] HARDWARE-INTERLOCK:
                                    Physical Key Slot A verified (EMP-1005 Timur Akhmetov, SHA-256 OK)</div>
                                <div class="text-secondary-fixed-dim font-mono">[15:44:02.890 UTC+6] WORM-CONTROLLER:
                                    Actuator relays disconnected on Karaganda, Almaty, and Ekibastuz</div>
                                <div class="text-error-container font-mono font-bold">[15:44:03.014 UTC+6] ALERT-RELAY:
                                    Galvano-magnetic cut tripped on Automated CNC Lathes (SYS-03) [PHYSICAL AIR-GAP
                                    COMPLETE]</div>
                                <div class="text-primary-fixed font-mono">[15:44:04.450 UTC+6] DISPATCH-KZ-CERT:
                                    Sovereign telemetry envelope sealed with token 0x7E3A09... Queued for uplink</div>
                                <div class="text-inverse-on-surface font-mono">[15:44:06.120 UTC+6] BASTION: Inactive
                                    sessions purged (142 keys evicted from memory pools)</div>
                                <div class="text-secondary-fixed font-mono">[15:44:08.980 UTC+6] BUFFER-HOLD: Immutable
                                    stream anchored to local NVRAM cache (Holding 4.8 GB zero-loss telemetry)</div>
                            </div>
                        </div>
                    </div>
                    <!-- RIGHT COLUMN: DUAL-CUSTODY INTERLOCK & AUTHORIZATION CONSOLE (4 cols) -->
                    <div class="lg:col-span-4 flex flex-col gap-space-base">
                        <!-- Panel: Physical Hardware Custodian Panel (Dual-Custody) -->
                        <div
                            class="bg-surface-container-lowest rounded shadow-sm p-space-base relative overflow-hidden">
                            <div
                                class="flex items-center justify-between mb-space-md pb-space-xs border-b border-surface-container-high">
                                <div class="flex items-center gap-space-xs">
                                    <span class="material-symbols-outlined text-[18px] text-tertiary">vpn_key</span>
                                    <h3 class="font-title-sm text-title-sm font-semibold text-on-surface">
                                        Dual-Custody HSM Interlock
                                    </h3>
                                </div>
                                <span
                                    class="font-security-stamp text-[9px] bg-tertiary-container text-on-tertiary px-space-xs py-[2px] rounded uppercase">
                                    2-MAN RULE
                                </span>
                            </div>
                            <p class="font-body-compact text-body-compact text-on-surface-variant mb-space-md">
                                Universal de-escalation or irreversible sovereign payload dispatch mandates concurrent
                                physical hardware authentication from both designated security officers.
                            </p>
                            <!-- Slot A: Timur Akhmetov (LATCHED) -->
                            <div
                                class="p-space-sm bg-surface-container-low rounded mb-space-sm relative overflow-hidden">
                                <div
                                    class="absolute top-0 right-0 px-space-xs py-[2px] bg-secondary text-on-secondary font-security-stamp text-[9px] rounded-bl">
                                    LATCHED &amp; SIGNED
                                </div>
                                <div class="flex items-center gap-space-sm mb-space-xs">
                                    <div
                                        class="w-8 h-8 rounded bg-primary text-on-primary flex items-center justify-center font-bold text-title-sm">
                                        A
                                    </div>
                                    <div class="flex flex-col">
                                        <span
                                            class="font-body-compact text-body-compact font-bold text-on-surface">Timur
                                            Akhmetov</span>
                                        <span class="font-telemetry-micro text-[10px] text-on-surface-variant">EMP-1005
                                            • CGO / LEVEL 5 CLEARANCE</span>
                                    </div>
                                </div>
                                <div
                                    class="bg-surface-container px-space-xs py-space-2xs rounded flex flex-col gap-[2px] font-telemetry-micro text-telemetry-micro">
                                    <div class="flex items-center justify-between">
                                        <span class="text-on-surface-variant">HARDWARE KEY:</span>
                                        <span class="font-mono text-on-surface font-semibold">YubiHSM2-ALM-01 (SLOT
                                            #1)</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-on-surface-variant">VERIFIED:</span>
                                        <span class="font-mono text-secondary font-semibold">15:44:02 UTC+6 (ECDSA
                                            P-384)</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Slot B: Leonid Volkov (PENDING PIN CHALLENGE) -->
                            <div
                                class="p-space-sm bg-surface-container-high rounded mb-space-md relative overflow-hidden">
                                <div
                                    class="absolute top-0 right-0 px-space-xs py-[2px] bg-tertiary text-on-tertiary font-security-stamp text-[9px] rounded-bl animate-pulse">
                                    ACTION REQUIRED
                                </div>
                                <div class="flex items-center gap-space-sm mb-space-xs">
                                    <div
                                        class="w-8 h-8 rounded bg-surface-container-highest text-on-surface flex items-center justify-center font-bold text-title-sm">
                                        B
                                    </div>
                                    <div class="flex flex-col">
                                        <span
                                            class="font-body-compact text-body-compact font-bold text-on-surface">Dinara
                                            Sadykova</span>
                                        <span class="font-telemetry-micro text-[10px] text-on-surface-variant">EMP-1018
                                            • SECOPS LEAD / LEVEL 4</span>
                                    </div>
                                </div>
                                <div class="bg-surface-container-lowest p-space-sm rounded mb-space-sm shadow-sm">
                                    <label
                                        class="block font-label-uppercase text-label-uppercase text-on-surface-variant mb-[4px]">
                                        FIPS-140-3 HARDWARE TOKEN PIN OR OTP
                                    </label>
                                    <div class="flex items-center gap-space-xs">
                                        <input
                                            class="h-control-height-sm w-full bg-surface-container-low px-space-sm font-telemetry-data text-telemetry-data text-on-surface rounded border-0 focus:outline-none focus:bg-surface-container-lowest"
                                            id="secondaryPinInput" maxlength="12" placeholder="••••••••"
                                            type="password" />
                                        <button
                                            class="h-control-height-sm px-space-md bg-primary text-on-primary hover:bg-primary-container font-body-compact text-body-compact font-semibold rounded flex-shrink-0 transition-all"
                                            id="btnVerifySecondaryPin">
                                            Submit Key
                                        </button>
                                    </div>
                                    <span class="font-telemetry-micro text-[10px] text-on-surface-variant mt-1 block"
                                        id="pinFeedback">
                                        Waiting for physical security dongle insertion on Port USB-SEC-02
                                    </span>
                                </div>
                                <button
                                    class="w-full h-control-height-md bg-secondary text-on-secondary hover:bg-on-secondary-container transition-all font-body-default text-body-default font-semibold rounded shadow-sm flex items-center justify-center gap-space-xs"
                                    id="btnAuthorizeLockAll">
                                    <span class="material-symbols-outlined text-[18px]">verified_user</span>
                                    <span>Authorize &amp; Lock All Upstream Nodes</span>
                                </button>
                            </div>
                            <!-- Emergency Rollback Protocol Notice -->
                            <div class="p-space-sm bg-surface-container-low rounded text-on-surface">
                                <div class="flex items-center gap-space-xs mb-space-2xs">
                                    <span class="material-symbols-outlined text-[16px] text-primary">restart_alt</span>
                                    <span class="font-title-sm text-title-sm font-bold">DEFCON-4 Rollback
                                        Instructions</span>
                                </div>
                                <p class="font-body-compact text-body-compact text-on-surface-variant mb-space-xs">
                                    To restore normal SCADA duplex communication, both keys must trigger the dual
                                    cryptographic purge code within a 120-second window:
                                </p>
                                <div
                                    class="bg-surface-container-highest px-space-xs py-space-2xs rounded font-telemetry-micro text-[10px] font-mono text-on-surface select-all">
                                    CHALLENGE: 0x9B11-ROLLBACK-DEESCALATE-DEFCON4-CONFIRM
                                </div>
                            </div>
                        </div>
                        <!-- Facility Environmental & Radio Telemetry Pod -->
                        <div class="bg-surface-container-lowest rounded shadow-sm p-space-base">
                            <div
                                class="flex items-center justify-between mb-space-sm pb-space-xs border-b border-surface-container-high">
                                <span
                                    class="font-security-stamp text-security-stamp text-on-surface uppercase tracking-wider">
                                    FACILITY SURVEILLANCE &amp; RF STATUS
                                </span>
                                <span class="font-telemetry-micro text-[10px] text-secondary font-bold">MONITORED</span>
                            </div>
                            <div class="space-y-space-xs font-telemetry-micro text-telemetry-micro">
                                <div
                                    class="flex items-center justify-between py-space-2xs border-b border-surface-container-low">
                                    <span class="text-on-surface-variant">RF Microwave Link (Medeu Repeater):</span>
                                    <span class="text-secondary font-semibold">ATTENUATED (-22 dBm)</span>
                                </div>
                                <div
                                    class="flex items-center justify-between py-space-2xs border-b border-surface-container-low">
                                    <span class="text-on-surface-variant">Auxiliary Diesel Turbine Generator:</span>
                                    <span class="text-on-surface font-semibold">SYNCHRONIZED (400V @ 50Hz)</span>
                                </div>
                                <div
                                    class="flex items-center justify-between py-space-2xs border-b border-surface-container-low">
                                    <span class="text-on-surface-variant">Faraday Cage Ground Potential:</span>
                                    <span class="text-secondary font-semibold">&lt; 0.04 Î© (OPTIMAL)</span>
                                </div>
                                <div class="flex items-center justify-between py-space-2xs">
                                    <span class="text-on-surface-variant">Station Physical Access Gate:</span>
                                    <span class="text-error font-semibold uppercase">LOCKED DOWN (MAG-BOLT)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- CRYPTOGRAPHIC TAMPER-EVIDENT FOOTER -->
                <div
                    class="bg-surface-container-lowest rounded shadow-sm p-space-md flex flex-col md:flex-row items-start md:items-center justify-between gap-space-md text-on-surface-variant">
                    <div class="flex flex-col gap-[2px]">
                        <div
                            class="flex items-center gap-space-xs font-security-stamp text-security-stamp text-on-surface uppercase tracking-wider">
                            <span class="material-symbols-outlined text-[16px] text-secondary">verified</span>
                            <span>MERKLE ROOT ANCHOR: #DOC-2026-LOCKDOWN-SHA256</span>
                        </div>
                        <div class="font-telemetry-micro text-telemetry-micro font-mono text-outline">
                            LEDGER_BLOCK_SIG: 78f99c0d12e86a0149bb88f331980072de451aa9c277bf82e661009bf3
                        </div>
                    </div>
                    <div class="flex items-center gap-space-lg flex-shrink-0">
                        <div class="flex flex-col text-left md:text-right font-telemetry-micro text-telemetry-micro">
                            <span class="text-on-surface font-semibold">HARDWARE SECURITY MODULE FIPS-140-3 LEVEL
                                4</span>
                            <span class="text-on-surface-variant">STATION 11 AUDIT TIME: 2026-03-29 15:44:18.82
                                UTC+6</span>
                        </div>
                        <div class="h-8 w-[1px] bg-surface-container-high hidden md:block"></div>
                        <div
                            class="flex items-center gap-space-xs font-label-uppercase text-label-uppercase bg-surface-container px-space-sm py-space-xs rounded text-on-surface font-bold">
                            <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                            <span>AUDIT CHAIN INTACT</span>
                        </div>
                    </div>
                </div>
                <!-- Simple Modal / Toast for Abort / Action Micro-Interactions -->
                <div class="fixed inset-0 bg-primary/70 backdrop-blur-[2px] z-50 flex items-center justify-center p-space-base hidden"
                    id="modalOverlay">
                    <div
                        class="bg-surface-container-lowest rounded max-w-md w-full p-space-base shadow-xl border-l-4 border-error">
                        <div
                            class="flex items-center justify-between mb-space-sm pb-space-xs border-b border-surface-container-high">
                            <div
                                class="flex items-center gap-space-xs text-error font-bold font-title-sm text-title-sm">
                                <span class="material-symbols-outlined text-[20px]">warning</span>
                                <span id="modalTitle">Critical Action Interlock</span>
                            </div>
                            <button class="text-on-surface-variant hover:text-on-surface" id="modalClose">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>
                        </div>
                        <p class="font-body-compact text-body-compact text-on-surface-variant mb-space-base"
                            id="modalBody">
                            Executing this command requires physical dual cryptographic hardware cancellation.
                        </p>
                        <div class="flex items-center justify-end gap-space-xs">
                            <button
                                class="h-control-height-sm px-space-base bg-surface-container text-on-surface font-body-compact text-body-compact font-semibold rounded hover:bg-surface-container-high transition-colors"
                                id="modalBtnCancel">
                                Dismiss
                            </button>
                            <button
                                class="h-control-height-sm px-space-base bg-error text-on-error font-body-compact text-body-compact font-semibold rounded hover:bg-on-error-container transition-colors"
                                id="modalBtnConfirm">
                                Confirm Action
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
    <script src="js/common.js"></script>
    <script src="js/emergencyLockdown.js"></script>
</body>

</html>