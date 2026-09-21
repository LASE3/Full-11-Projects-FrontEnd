<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('ADM');
require_once __DIR__ . '/gov_service.php';

$currentUser = gov_getActiveUserProfile();
$auditLogs = gov_getUnifiedAuditLogs(50);
$privilegedAccounts = gov_getPrivilegedAccounts();
$metrics = gov_getGovernanceMetrics();
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>VOSTOKPRIBOR Administration &amp; Governance Portal - System 11</title>
    <link rel="stylesheet" href="css/common.css" />
    <link rel="stylesheet" href="css/auditLogs.css" />
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
                        Ingestion Nodes Active</span></div><button
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
<a href="../api/logout.php?system=Admin%20%26%20Governance%20Portal&redirect=../Admin%20%26%20Governance%20Portal/login.php" class="top-signout-btn" title="Sign Out of Admin &amp; Governance Portal" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:4px;background:rgba(178,58,50,0.2);border:1px solid rgba(178,58,50,0.5);color:#FF8080;font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;margin-left:8px;vertical-align:middle;transition:all 0.2s;" onmouseover="this.style.background='rgba(178,58,50,0.4)';this.style.color='#FFFFFF'" onmouseout="this.style.background='rgba(178,58,50,0.2)';this.style.color='#FF8080'"><span class="material-symbols-outlined" style="font-size:15px;line-height:1;">logout</span><span>Sign Out</span></a>
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
                </a></nav>
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
                <a class="flex items-center justify-between px-space-sm py-space-xs rounded text-error hover:bg-error-container hover:text-on-error-container transition-all font-body-compact text-body-compact" data-path="emergency-lockdown" href="EmergencyLockdown.php"><div class="flex items-center gap-space-sm"><span class="material-symbols-outlined text-[18px] text-error">lock</span><span class="font-bold uppercase">Emergency Lockdown</span></div><span class="font-telemetry-micro text-[10px] px-space-2xs bg-error text-on-error font-bold rounded">DEFCON-1</span></a></nav>
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
                </a></nav>
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
                <span class="station-live-clock">UTC+6 (ALMATY TIME)</span></div>
        </div>
    </aside>
    <div class="pl-[260px]">
        <main class="relative pt-[60px] w-full min-h-screen bg-surface px-gutter-desktop py-space-lg">
            <div class="flex flex-col w-full">
                <!-- Top Command Ribbon / Breadcrumb Header -->
                <div
                    class="flex flex-col xl:flex-row xl:items-center justify-between gap-space-md pb-space-md mb-space-base">
                    <div class="flex flex-col gap-space-2xs">
                        <div
                            class="flex items-center gap-space-xs font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                            <span>ROOT</span>
                            <span class="text-outline-variant">/</span>
                            <span>GOVERNANCE</span>
                            <span class="text-outline-variant">/</span>
                            <span>SECURITY LOGS</span>
                            <span class="text-outline-variant">/</span>
                            <span class="text-primary font-bold">MONOSPACED EVENT STREAM</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-space-md">
                            <h1 class="font-headline-lg text-headline-lg uppercase text-on-surface tracking-tight">AUDIT
                                LOGS &amp; PRIVILEGED SESSION MONITORING</h1>
                            <div
                                class="flex items-center gap-space-xs px-space-sm py-space-2xs bg-secondary-container rounded font-telemetry-micro text-telemetry-micro text-on-secondary-container">
                                <span class="w-2 h-2 rounded-full bg-secondary animate-ping"></span>
                                <span class="font-bold">LIVE STREAMING (500 events/sec buffer)</span>
                            </div>
                            <div
                                class="px-space-xs py-[2px] bg-surface-container-high rounded font-security-stamp text-security-stamp text-on-surface-variant">
                                SHARD: KZ-ALM-PRIMARY-01
                            </div>
                        </div>
                    </div>
                    <!-- Quick Ledger Status Counter Strip -->
                    <div class="flex items-center gap-space-sm self-start xl:self-auto">
                        <div class="px-space-md py-space-xs bg-surface-container rounded flex flex-col items-end">
                            <span class="font-label-uppercase text-label-uppercase text-on-surface-variant">INGEST
                                RATE</span>
                            <span class="font-telemetry-data text-telemetry-data text-primary font-bold">512.4
                                evt/s</span>
                        </div>
                        <div class="px-space-md py-space-xs bg-surface-container rounded flex flex-col items-end">
                            <span class="font-label-uppercase text-label-uppercase text-on-surface-variant">UNVERIFIED
                                DELTA</span>
                            <span class="font-telemetry-data text-telemetry-data text-secondary font-bold">0
                                SHA-BLOCKS</span>
                        </div>
                        <div class="px-space-md py-space-xs bg-error-container rounded flex flex-col items-end">
                            <span class="font-label-uppercase text-label-uppercase text-on-error-container">ACTIVE
                                THREAT FLAGS</span>
                            <span class="font-telemetry-data text-telemetry-data text-error font-bold">2
                                SYSTEM-WIDE</span>
                        </div>
                    </div>
                </div>
                <!-- Primary Query Syntax & Control Deck -->
                <div
                    class="bg-surface-container-lowest rounded-lg shadow-sm p-space-md mb-space-base flex flex-col gap-space-md">
                    <!-- Search Query Bar -->
                    <div class="flex flex-col lg:flex-row items-stretch gap-space-sm">
                        <div
                            class="relative flex-1 bg-surface-container-low rounded flex items-center px-space-sm focus-within:bg-surface-container-lowest transition-all">
                            <span
                                class="material-symbols-outlined text-[18px] text-on-surface-variant mr-space-xs">terminal</span>
                            <span
                                class="font-telemetry-micro text-telemetry-micro text-secondary select-none font-bold mr-space-xs">QUERY&gt;</span>
                            <input
                                class="w-full bg-transparent font-telemetry-data text-telemetry-data text-on-surface focus:outline-none placeholder:text-outline py-space-xs"
                                id="logQueryInput"
                                placeholder="Search syntax: level:WARN target:SYS-07 user:EMP-1005..." type="text"
                                value="level:CRITICAL OR action:OVERRIDE target:SYS-*" />
                            <button class="text-on-surface-variant hover:text-error transition-colors px-space-xs"
                                onclick="document.getElementById('logQueryInput').value=''">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                            </button>
                        </div>
                        <div class="flex items-center gap-space-xs">
                            <button
                                class="h-control-height-md px-space-base bg-primary text-on-primary rounded font-headline-md text-body-default flex items-center gap-space-xs hover:bg-primary-container transition-colors"
                                id="execQueryBtn">
                                <span class="material-symbols-outlined text-[16px]">bolt</span>
                                <span>Execute Query</span>
                            </button>
                            <button
                                class="h-control-height-md px-space-md bg-surface-container text-on-surface rounded font-body-compact text-body-compact flex items-center gap-space-xs hover:bg-surface-container-high transition-colors"
                                id="streamToggleBtn">
                                <span class="material-symbols-outlined text-[16px]" id="streamIcon">pause_circle</span>
                                <span id="streamStateLabel">Pause Stream</span>
                            </button>
                        </div>
                    </div>
                    <!-- Fine Filter Badges & Protocols -->
                    <div class="flex flex-wrap items-center justify-between gap-space-md pt-space-xs">
                        <div class="flex flex-wrap items-center gap-space-xs">
                            <span
                                class="font-label-uppercase text-label-uppercase text-on-surface-variant mr-space-xs">SEVERITY:</span>
                            <button
                                class="px-space-sm py-[3px] bg-primary text-on-primary rounded font-label-uppercase text-label-uppercase">ALL
                                (14.2K)</button>
                            <button
                                class="px-space-sm py-[3px] bg-error-container text-on-error-container hover:bg-error hover:text-on-error rounded font-label-uppercase text-label-uppercase flex items-center gap-space-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-error"></span>CRITICAL (14)
                            </button>
                            <button
                                class="px-space-sm py-[3px] bg-surface-container-high text-on-surface hover:bg-surface-variant rounded font-label-uppercase text-label-uppercase flex items-center gap-space-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-container"></span>WARN (82)
                            </button>
                            <button
                                class="px-space-sm py-[3px] bg-surface-container-high text-on-surface hover:bg-surface-variant rounded font-label-uppercase text-label-uppercase flex items-center gap-space-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>INFO (1.2K)
                            </button>
                            <button
                                class="px-space-sm py-[3px] bg-surface-container-high text-on-surface hover:bg-surface-variant rounded font-label-uppercase text-label-uppercase flex items-center gap-space-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-outline"></span>AUDIT (12.9K)
                            </button>
                            <div class="h-4 w-[1px] bg-surface-variant mx-space-xs hidden md:block"></div>
                            <span
                                class="font-label-uppercase text-label-uppercase text-on-surface-variant mr-space-xs hidden md:inline">TARGET
                                NODE:</span>
                            <select
                                class="h-control-height-sm bg-surface-container-low text-on-surface font-telemetry-micro text-telemetry-micro rounded px-space-xs focus:outline-none">
                                <option value="ALL">All Nodes (SYS-01 - SYS-11)</option>
                                <option value="SYS-02">SYS-02 (Hydraulic Press Complex)</option>
                                <option value="SYS-03">SYS-03 (Precision CNC Center)</option>
                                <option value="SYS-05">SYS-05 (110kV Substation Grid)</option>
                                <option value="SYS-07">SYS-07 (ASRS Automated Storage)</option>
                                <option selected="" value="SYS-11">SYS-11 (Gov-Core Mainframe)</option>
                            </select>
                        </div>
                        <!-- Action Utility Buttons -->
                        <div class="flex items-center gap-space-xs">
                            <button
                                class="h-control-height-sm px-space-sm bg-secondary-container text-on-secondary-container rounded font-telemetry-micro text-telemetry-micro font-bold flex items-center gap-space-2xs hover:bg-secondary hover:text-on-secondary transition-colors"
                                title="Verify Merkle Tree Integrity">
                                <span class="material-symbols-outlined text-[14px]">verified_user</span>
                                <span>Cryptographic Ledger Verify</span>
                            </button>
                            <button
                                class="h-control-height-sm px-space-sm bg-surface-container text-on-surface rounded font-telemetry-micro text-telemetry-micro flex items-center gap-space-2xs hover:bg-surface-container-high transition-colors"
                                title="RFC-5424 Raw Format">
                                <span class="material-symbols-outlined text-[14px]">download</span>
                                <span>Export Syslog</span>
                            </button>
                            <button
                                class="h-control-height-sm px-space-sm bg-surface-container text-on-surface hover:text-error rounded font-telemetry-micro text-telemetry-micro flex items-center gap-space-2xs"
                                title="Flush current local terminal buffer">
                                <span class="material-symbols-outlined text-[14px]">mop</span>
                                <span>Clear Buffer</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Main Bento Grid: Live Event Stream + Operational Panels -->
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-base mb-space-base items-start">
                    <!-- Left Column (8 cols): Live Event Terminal Console -->
                    <div class="xl:col-span-8 flex flex-col gap-space-base">
                        <!-- Live Industrial Terminal Pane -->
                        <div class="bg-primary text-on-primary rounded-lg shadow-md overflow-hidden flex flex-col">
                            <!-- Terminal Header Bar -->
                            <div
                                class="h-[36px] bg-primary-container px-space-md flex items-center justify-between select-none">
                                <div class="flex items-center gap-space-sm">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-error"></span>
                                        <span class="w-2.5 h-2.5 rounded-full bg-tertiary-fixed-dim"></span>
                                        <span class="w-2.5 h-2.5 rounded-full bg-secondary-fixed"></span>
                                    </div>
                                    <span
                                        class="font-security-stamp text-security-stamp text-on-primary-container tracking-wider">CONSOLE://SYS11.STREAM.AUDIT.DAEMON.01</span>
                                </div>
                                <div class="flex items-center gap-space-md">
                                    <div
                                        class="flex items-center gap-space-xs font-telemetry-micro text-telemetry-micro text-secondary-fixed">
                                        <span class="material-symbols-outlined text-[14px]">sync</span>
                                        <span id="bufferStatus">RING-BUFFER: 8,192 LINES (0 DROPPED)</span>
                                    </div>
                                    <span
                                        class="font-telemetry-micro text-telemetry-micro text-on-primary-container">ENCODING:
                                        UTF-8 / RFC5424</span>
                                </div>
                            </div>
                            <!-- Terminal Body / Monospaced Stream Entries -->
                            <div class="p-space-sm overflow-x-auto flex flex-col gap-[2px] max-h-[580px] overflow-y-auto"
                                id="terminalStreamBox">
                                <?php foreach ($auditLogs as $idx => $al): 
                                    $isCrit = ($al['result'] === 'Failed' || strpos($al['action'], 'Override') !== false || strpos($al['action'], 'Breach') !== false || strpos($al['action'], 'Revocation') !== false);
                                    $isWarn = ($al['result'] === 'Warning');
                                    $sysLabel = !empty($al['system_id']) ? $al['system_id'] : (!empty($al['actor_system']) ? $al['actor_system'] : 'SYS-11');
                                ?>
                                <div class="flex items-start gap-space-xs font-telemetry-data text-telemetry-data py-space-2xs px-space-xs <?= $isCrit ? 'bg-error-container/20 rounded hover:bg-error-container/30' : 'hover:bg-surface-container-highest/10' ?> transition-colors">
                                    <span class="text-on-primary-container select-none font-telemetry-micro w-8 text-right shrink-0"><?= sprintf('%04d', $al['audit_id']) ?></span>
                                    <span class="text-secondary-fixed shrink-0 font-telemetry-micro"><?= htmlspecialchars($al['occurred_at']) ?></span>
                                    <?php if ($isCrit): ?>
                                        <span class="bg-error text-on-error px-space-2xs py-0 rounded font-security-stamp text-security-stamp shrink-0">CRITICAL</span>
                                    <?php elseif ($isWarn): ?>
                                        <span class="bg-tertiary-container text-tertiary-fixed px-space-2xs py-0 rounded font-security-stamp text-security-stamp shrink-0">WARN</span>
                                    <?php else: ?>
                                        <span class="bg-primary-container text-on-primary-container px-space-2xs py-0 rounded font-security-stamp text-security-stamp shrink-0">INFO</span>
                                    <?php endif; ?>
                                    <span class="text-secondary-fixed-dim font-bold shrink-0">[<?= htmlspecialchars($sysLabel) ?>]</span>
                                    <span class="text-tertiary-fixed-dim shrink-0">[<?= htmlspecialchars($al['action']) ?>]</span>
                                    <span class="text-on-primary">
                                        <strong class="text-secondary-fixed underline"><?= htmlspecialchars($al['actor_emp_id'] ?? 'SYS-AUTO') ?></strong> 
                                        (<?= htmlspecialchars($al['actor_name'] ?? 'System Service') ?>)
                                        <?= htmlspecialchars($al['action']) ?> on <?= htmlspecialchars($al['target_entity_type'] ?? 'SYS') ?>:<?= htmlspecialchars($al['target_entity_id'] ?? 'CORE') ?> 
                                        [Result: <span class="<?= ($al['result'] === 'Success') ? 'text-secondary-fixed font-bold' : 'text-error font-bold' ?>"><?= htmlspecialchars($al['result']) ?></span>, IP: <?= htmlspecialchars($al['source_ip'] ?? '10.240.0.1') ?>]
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>\n                            <!-- Terminal Footer Status & Command Input Simulator -->
                            <div
                                class="h-[32px] bg-primary-container/80 px-space-md flex items-center justify-between font-telemetry-micro text-telemetry-micro text-on-primary-container">
                                <div class="flex items-center gap-space-xs">
                                    <span class="w-2 h-2 rounded-full bg-secondary-fixed animate-pulse"></span>
                                    <span>STREAM ACTIVE • TAIL LOGS REAL-TIME</span>
                                </div>
                                <div class="flex items-center gap-space-base">
                                    <span>FILTER: PASSING 100% OF BUFFER</span>
                                    <span class="font-security-stamp text-security-stamp text-secondary-fixed">SYSLOG
                                        RFC5424 COMPLIANT</span>
                                </div>
                            </div>
                        </div>
                        <!-- Historical Event Distribution Graph (SVG Vector Data) -->
                        <div
                            class="bg-surface-container-lowest rounded-lg shadow-sm p-space-md flex flex-col gap-space-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-space-xs">
                                    <span class="material-symbols-outlined text-[18px] text-primary">analytics</span>
                                    <span class="font-title-sm text-title-sm text-on-surface uppercase">Event Velocity
                                        &amp; Anomaly Distribution (Last 60 Minutes)</span>
                                </div>
                                <span class="font-telemetry-micro text-telemetry-micro text-on-surface-variant">BUCKET:
                                    60 SEC INTERVALS</span>
                            </div>
                            <!-- Inline SVG Sparkline / Bar Graph -->
                            <div class="h-28 w-full relative">
                                <svg class="w-full h-full text-secondary" preserveaspectratio="none"
                                    viewbox="0 0 600 80">
                                    <defs>
                                        <lineargradient id="gradientAudit" x1="0" x2="0" y1="0" y2="1">
                                            <stop offset="0%" stop-color="#006972" stop-opacity="0.35"></stop>
                                            <stop offset="100%" stop-color="#006972" stop-opacity="0.0"></stop>
                                        </lineargradient>
                                    </defs>
                                    <!-- Grid Lines -->
                                    <line stroke="#E5E8EE" stroke-dasharray="3,3" stroke-width="1" x1="0" x2="600"
                                        y1="20" y2="20"></line>
                                    <line stroke="#E5E8EE" stroke-dasharray="3,3" stroke-width="1" x1="0" x2="600"
                                        y1="40" y2="40"></line>
                                    <line stroke="#E5E8EE" stroke-dasharray="3,3" stroke-width="1" x1="0" x2="600"
                                        y1="60" y2="60"></line>
                                    <!-- Area Fill -->
                                    <polygon fill="url(#gradientAudit)"
                                        points="0,65 30,62 60,60 90,55 120,68 150,50 180,48 210,54 240,40 270,30 300,32 330,22 360,45 390,50 420,28 450,15 480,24 510,38 540,18 570,22 600,12 600,80 0,80">
                                    </polygon>
                                    <!-- Stroke Path -->
                                    <polyline fill="none"
                                        points="0,65 30,62 60,60 90,55 120,68 150,50 180,48 210,54 240,40 270,30 300,32 330,22 360,45 390,50 420,28 450,15 480,24 510,38 540,18 570,22 600,12"
                                        stroke="#006972" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2.5"></polyline>
                                    <!-- Critical Spike Anomaly Pin -->
                                    <circle cx="450" cy="15" fill="#BA1A1A" r="4.5"></circle>
                                    <circle cx="600" cy="12" fill="#BA1A1A" r="4.5"></circle>
                                </svg>
                                <div
                                    class="absolute right-2 top-2 px-space-xs py-space-2xs bg-error-container text-on-error-container rounded font-telemetry-micro text-telemetry-micro font-bold flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-error animate-ping"></span>
                                    PEAK: 1,842 EVT/S @ 10:48 (OVERRIDE CALL)
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Right Column (4 cols): Privileged Sessions & Emergency Break-Glass Station -->
                    <div class="xl:col-span-4 flex flex-col gap-space-base">
                        <!-- Active Privileged Sessions Panel -->
                        <div class="bg-surface-container-lowest rounded-lg shadow-sm overflow-hidden flex flex-col">
                            <div class="px-space-md py-space-sm bg-surface-container flex items-center justify-between">
                                <div class="flex items-center gap-space-xs">
                                    <span
                                        class="material-symbols-outlined text-[18px] text-primary">admin_panel_settings</span>
                                    <span class="font-headline-md text-headline-md text-on-surface uppercase">ELEVATED
                                        SESSIONS</span>
                                </div>
                                <span
                                    class="px-space-xs py-[2px] bg-secondary-container text-on-secondary-container rounded font-security-stamp text-security-stamp font-bold">4
                                    ACTIVE</span>
                            </div>
                            <div class="p-space-md flex flex-col gap-space-md">
                                <!-- Session Card 1: Timur Akhmetov -->
                                <div
                                    class="bg-surface-container-low rounded p-space-sm flex flex-col gap-space-xs hover:bg-surface-container transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-space-xs">
                                            <span
                                                class="font-telemetry-data text-telemetry-data font-bold text-primary">#PS-8841</span>
                                            <span
                                                class="font-security-stamp text-[10px] px-space-xs bg-primary text-on-primary rounded">SSH</span>
                                        </div>
                                        <span
                                            class="flex items-center gap-1 font-telemetry-micro text-telemetry-micro text-error font-bold">
                                            <span class="w-2 h-2 rounded-full bg-error animate-pulse"></span>
                                            KEYLOG ACTIVE
                                        </span>
                                    </div>
                                    <div class="flex flex-col">
                                        <div
                                            class="flex items-center justify-between font-body-compact text-body-compact">
                                            <span class="font-bold text-on-surface">EMP-1005 (Timur Akhmetov)</span>
                                            <span class="text-on-surface-variant font-telemetry-micro">44m 12s
                                                ELAPSED</span>
                                        </div>
                                        <span
                                            class="font-telemetry-micro text-telemetry-micro text-on-surface-variant">TARGET:
                                            SYS-11 Gov Core via Bastion-01</span>
                                    </div>
                                    <div class="flex items-center justify-between pt-space-xs gap-space-xs">
                                        <button
                                            class="flex-1 h-control-height-sm bg-primary text-on-primary rounded font-body-compact text-body-compact hover:bg-primary-container transition-colors flex items-center justify-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">visibility</span>
                                            <span>Shadow View</span>
                                        </button>
                                        <button
                                            class="h-control-height-sm px-space-sm bg-error-container text-on-error-container hover:bg-error hover:text-on-error rounded font-body-compact text-body-compact transition-colors flex items-center gap-1 font-bold">
                                            <span class="material-symbols-outlined text-[14px]">cancel</span>
                                            <span>Kill Session</span>
                                        </button>
                                    </div>
                                </div>
                                <!-- Session Card 2: Leonid Volkov -->
                                <div
                                    class="bg-surface-container-low rounded p-space-sm flex flex-col gap-space-xs hover:bg-surface-container transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-space-xs">
                                            <span
                                                class="font-telemetry-data text-telemetry-data font-bold text-primary">#PS-8843</span>
                                            <span
                                                class="font-security-stamp text-[10px] px-space-xs bg-secondary text-on-secondary rounded">HTTPS
                                                MFA</span>
                                        </div>
                                        <span
                                            class="flex items-center gap-1 font-telemetry-micro text-telemetry-micro text-secondary font-bold">
                                            <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                                            KEYLOG ACTIVE
                                        </span>
                                    </div>
                                    <div class="flex flex-col">
                                        <div
                                            class="flex items-center justify-between font-body-compact text-body-compact">
                                            <span class="font-bold text-on-surface">EMP-1018 (Leonid Volkov)</span>
                                            <span class="text-on-surface-variant font-telemetry-micro">28m 05s
                                                ELAPSED</span>
                                        </div>
                                        <span
                                            class="font-telemetry-micro text-telemetry-micro text-on-surface-variant">TARGET:
                                            Policy Engine Root Console</span>
                                    </div>
                                    <div class="flex items-center justify-between pt-space-xs gap-space-xs">
                                        <button
                                            class="flex-1 h-control-height-sm bg-surface-container-high text-on-surface rounded font-body-compact text-body-compact hover:bg-surface-variant transition-colors flex items-center justify-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">visibility</span>
                                            <span>Audit Stream</span>
                                        </button>
                                        <button
                                            class="h-control-height-sm px-space-sm bg-surface-container text-on-surface hover:text-error rounded font-body-compact text-body-compact transition-colors flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">block</span>
                                            <span>Revoke MFA</span>
                                        </button>
                                    </div>
                                </div>
                                <!-- Session Card 3: Automated Daemon Tunnel -->
                                <div
                                    class="bg-surface-container-low rounded p-space-sm flex flex-col gap-space-xs hover:bg-surface-container transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-space-xs">
                                            <span
                                                class="font-telemetry-data text-telemetry-data font-bold text-primary">#PS-8849</span>
                                            <span
                                                class="font-security-stamp text-[10px] px-space-xs bg-surface-container-high text-on-surface-variant rounded">gRPC
                                                TUNNEL</span>
                                        </div>
                                        <span
                                            class="font-telemetry-micro text-telemetry-micro text-secondary font-semibold">IN
                                            COMPLIANCE</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <div
                                            class="flex items-center justify-between font-body-compact text-body-compact">
                                            <span class="font-bold text-on-surface">Maintenance Daemon #04</span>
                                            <span class="text-on-surface-variant font-telemetry-micro">03h 11m
                                                ELAPSED</span>
                                        </div>
                                        <span
                                            class="font-telemetry-micro text-telemetry-micro text-on-surface-variant">TARGET:
                                            SYS-04 Bogie Line Diagnostics</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Emergency Break-Glass Protocol Status Console -->
                        <div
                            class="bg-surface-container-lowest rounded-lg shadow-sm overflow-hidden flex flex-col relative">
                            <!-- Visual Accent Strip -->
                            <div class="h-1.5 w-full bg-error"></div>
                            <div class="p-space-md flex flex-col gap-space-md">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-space-xs">
                                        <span
                                            class="material-symbols-outlined text-error text-[22px]">e911_emergency</span>
                                        <div class="flex flex-col">
                                            <span
                                                class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight">BREAK-GLASS
                                                INTERLOCK</span>
                                            <span
                                                class="font-telemetry-micro text-telemetry-micro text-on-surface-variant">REF:
                                                DOC-2026-015 JURISDICTION CODE</span>
                                        </div>
                                    </div>
                                    <span
                                        class="px-space-xs py-space-2xs bg-error-container text-on-error-container font-security-stamp text-security-stamp font-extrabold rounded">STANDBY</span>
                                </div>
                                <!-- Dual Custody Token Verification Pod -->
                                <div class="bg-surface-container rounded p-space-sm flex flex-col gap-space-sm">
                                    <span
                                        class="font-label-uppercase text-label-uppercase text-on-surface-variant">DUAL-AUTHORIZATION
                                        PHYSICAL CUSTODIANS</span>
                                    <!-- Custodian 1: CGO Akhmetov -->
                                    <div
                                        class="flex items-center justify-between bg-surface-container-lowest p-space-xs rounded">
                                        <div class="flex items-center gap-space-xs">
                                            <span
                                                class="material-symbols-outlined text-[18px] text-secondary">vpn_key</span>
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-body-compact text-body-compact font-bold text-on-surface">EMP-1005
                                                    (CGO Akhmetov)</span>
                                                <span
                                                    class="font-telemetry-micro text-telemetry-micro text-secondary">Hardware
                                                    Key 1: INSERTED &amp; VERIFIED</span>
                                            </div>
                                        </div>
                                        <span
                                            class="material-symbols-outlined text-[18px] text-secondary">check_circle</span>
                                    </div>
                                    <!-- Custodian 2: Lead Auditor Sadykova -->
                                    <div
                                        class="flex items-center justify-between bg-surface-container-lowest p-space-xs rounded">
                                        <div class="flex items-center gap-space-xs">
                                            <span
                                                class="material-symbols-outlined text-[18px] text-outline">key_off</span>
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-body-compact text-body-compact font-bold text-on-surface">EMP-1018
                                                    (Auditor Sadykova)</span>
                                                <span
                                                    class="font-telemetry-micro text-telemetry-micro text-tertiary-container">Hardware
                                                    Key 2: PENDING RE-INSERTION</span>
                                            </div>
                                        </div>
                                        <span
                                            class="font-telemetry-micro text-telemetry-micro font-bold text-on-tertiary-container">PENDING</span>
                                    </div>
                                </div>
                                <!-- Emergency Action Warning & Interlock Trigger -->
                                <div
                                    class="p-space-xs bg-error-container/40 rounded flex items-center gap-space-xs text-on-error-container">
                                    <span
                                        class="material-symbols-outlined text-[18px] shrink-0 text-error">warning</span>
                                    <span class="font-telemetry-micro text-telemetry-micro">
                                        Initiating emergency break-glass notifies the State Security Board and issues
                                        permanent cryptographic audit markers to the vault.
                                    </span>
                                </div>
                                <button
                                    class="w-full h-control-height-md bg-error text-on-error rounded font-security-stamp text-security-stamp tracking-wider uppercase hover:opacity-95 transition-opacity flex items-center justify-center gap-space-xs">
                                    <span class="material-symbols-outlined text-[18px]">lock_open</span>
                                    <span>INITIALIZE BREAK-GLASS DIALOGUE</span>
                                </button>
                            </div>
                            <!-- Watermark / Security Tag Footer -->
                            <div
                                class="px-space-md py-space-xs bg-surface-container-low flex items-center justify-between text-on-surface-variant font-telemetry-micro text-telemetry-micro">
                                <span>ALMATY VAULT MERKLE ROOT: <code
                                        class="text-primary font-bold">d6a7...10bc</code></span>
                                <span>COMPLIANCE CLASS IV</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Bottom Ledger Verification & Forensic Search Drawer -->
                <div class="bg-surface-container-lowest rounded-lg shadow-sm p-space-md flex flex-col gap-space-md">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-sm pb-space-xs">
                        <div class="flex items-center gap-space-sm">
                            <span class="material-symbols-outlined text-[20px] text-primary">history_edu</span>
                            <div class="flex flex-col">
                                <span
                                    class="font-headline-md text-headline-md text-on-surface font-semibold">CRYPTOGRAPHIC
                                    INTEGRITY &amp; LOG EVIDENCE LEDGER</span>
                                <span
                                    class="font-telemetry-micro text-telemetry-micro text-on-surface-variant">Immutable
                                    SHA-256 block receipts confirmed across 10 redundant telemetry bridges</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-space-sm">
                            <div
                                class="px-space-sm py-space-2xs bg-secondary-container rounded font-telemetry-micro text-telemetry-micro text-on-secondary-container font-bold">
                                BLOCK HEIGHT: #4,921,802
                            </div>
                            <span class="font-telemetry-micro text-telemetry-micro text-on-surface-variant">LAST PROOF:
                                4 SEC AGO</span>
                        </div>
                    </div>
                    <!-- Forensic Table Strip -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left font-body-compact text-body-compact">
                            <thead>
                                <tr
                                    class="bg-surface-container font-label-uppercase text-label-uppercase text-on-surface-variant">
                                    <th class="py-space-xs px-space-sm">RECEIPT ID</th>
                                    <th class="py-space-xs px-space-sm">TIMESTAMP (ALMATY)</th>
                                    <th class="py-space-xs px-space-sm">SOURCE APPARATUS</th>
                                    <th class="py-space-xs px-space-sm">EVENT CATEGORY</th>
                                    <th class="py-space-xs px-space-sm">SIGNING KEY</th>
                                    <th class="py-space-xs px-space-sm">BLOCK HASH</th>
                                    <th class="py-space-xs px-space-sm text-right">PROOF</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-container">
                                <tr class="hover:bg-surface-container-low transition-colors font-telemetry-micro">
                                    <td class="py-space-xs px-space-sm text-primary font-bold">RCP-90144</td>
                                    <td class="py-space-xs px-space-sm">2026-03-31 10:48:19</td>
                                    <td class="py-space-xs px-space-sm">SYS-03 Lathe Core</td>
                                    <td class="py-space-xs px-space-sm"><span
                                            class="px-space-2xs bg-error-container text-on-error-container rounded">OVERRIDE</span>
                                    </td>
                                    <td class="py-space-xs px-space-sm text-on-surface-variant">RSA-4096:KEY-ALM-1005
                                    </td>
                                    <td class="py-space-xs px-space-sm text-outline">f8b2c419e02c918a3d...</td>
                                    <td class="py-space-xs px-space-sm text-right text-secondary font-bold">VERIFIED œ“
                                    </td>
                                </tr>
                                <tr class="hover:bg-surface-container-low transition-colors font-telemetry-micro">
                                    <td class="py-space-xs px-space-sm text-primary font-bold">RCP-90143</td>
                                    <td class="py-space-xs px-space-sm">2026-03-31 10:47:55</td>
                                    <td class="py-space-xs px-space-sm">SYS-07 Storage Robot</td>
                                    <td class="py-space-xs px-space-sm"><span
                                            class="px-space-2xs bg-surface-container-high text-on-surface rounded">POLICY-VIOLATION</span>
                                    </td>
                                    <td class="py-space-xs px-space-sm text-on-surface-variant">ED25519:GATEWAY-07</td>
                                    <td class="py-space-xs px-space-sm text-outline">43e018a11b8ca9f102...</td>
                                    <td class="py-space-xs px-space-sm text-right text-secondary font-bold">VERIFIED œ“
                                    </td>
                                </tr>
                                <tr class="hover:bg-surface-container-low transition-colors font-telemetry-micro">
                                    <td class="py-space-xs px-space-sm text-primary font-bold">RCP-90142</td>
                                    <td class="py-space-xs px-space-sm">2026-03-31 10:45:02</td>
                                    <td class="py-space-xs px-space-sm">SYS-11 Gov-Core</td>
                                    <td class="py-space-xs px-space-sm"><span
                                            class="px-space-2xs bg-secondary-container text-on-secondary-container rounded">BREAK-GLASS</span>
                                    </td>
                                    <td class="py-space-xs px-space-sm text-on-surface-variant">FIPS-140-3:YUBI-8832
                                    </td>
                                    <td class="py-space-xs px-space-sm text-outline">7718decf119a0028a1...</td>
                                    <td class="py-space-xs px-space-sm text-right text-secondary font-bold">VERIFIED œ“
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </main>
    </div>
    <script src="js/common.js"></script>
    <script src="js/auditLogs.js"></script>
</body>

</html>