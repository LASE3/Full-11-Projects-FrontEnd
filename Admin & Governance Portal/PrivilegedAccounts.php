<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireAuth('ADM');
require_once __DIR__ . '/gov_service.php';

$currentUser = gov_getActiveUserProfile();
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
    <link rel="stylesheet" href="css/privilegedAccounts.css" />
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
                </a><a aria-current="page"
                    class="flex items-center justify-between px-space-sm py-space-xs rounded transition-all bg-primary-container text-on-primary font-semibold border-l-4 border-secondary-fixed"
                    data-path="privileged-accounts-monitoring" href="PrivilegedAccounts.php">
                    <div class="flex items-center gap-space-sm"><span
                            class="material-symbols-outlined text-[18px]">admin_panel_settings</span><span>Privileged
                            Accounts</span></div><span
                        class="font-telemetry-micro text-[10px] px-space-2xs bg-tertiary-container text-secondary-fixed rounded font-bold border border-secondary-fixed/40">7
                        Active</span>
                </a>
            </nav>
            <div class="px-space-md mb-space-xs"><span
                    class="font-label-uppercase text-label-uppercase text-on-primary-container tracking-wider">AUDIT
                    &amp; INTELLIGENCE</span></div>
            <nav class="flex flex-col gap-[2px] px-space-xs mb-space-md"
                data-active-classes="bg-primary-container text-on-primary font-semibold border-l-4 border-secondary-fixed">
                <a class="flex items-center justify-between px-space-sm py-space-xs rounded text-on-primary-container hover:bg-primary-container hover:text-on-primary transition-all font-body-compact text-body-compact"
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
                    class="flex items-center justify-between px-space-sm py-space-xs rounded text-on-primary-container hover:bg-primary-container hover:text-on-primary transition-all font-body-compact text-body-compact"
                    data-path="emergency-break-glass" href="Break-GlassAccess.php">
                    <div class="flex items-center gap-space-sm"><span
                            class="material-symbols-outlined text-[18px] text-error">e911_emergency</span><span
                            class="font-bold uppercase text-error">Break-Glass Access</span></div><span
                        class="material-symbols-outlined text-[16px] text-error">lock_open</span>
                </a>
                <a class="flex items-center justify-between px-space-sm py-space-xs rounded text-error hover:bg-error-container hover:text-on-error-container transition-all font-body-compact text-body-compact" data-path="emergency-lockdown" href="EmergencyLockdown.php"><div class="flex items-center gap-space-sm"><span class="material-symbols-outlined text-[18px] text-error">lock</span><span class="font-bold uppercase">Emergency Lockdown</span></div><span class="font-telemetry-micro text-[10px] px-space-2xs bg-error text-on-error font-bold rounded">DEFCON-1</span></a>
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
                <span class="station-live-clock">UTC+6 (ALMATY TIME)</span></div>
        </div>
    </aside>
    <div class="pl-[260px]">
        <main class="relative pt-[60px] w-full min-h-screen bg-surface px-gutter-desktop py-space-lg">
            <div class="flex flex-col w-full">
                <!-- Breadcrumb and Security Status Bar -->
                <div
                    class="flex flex-col md:flex-row md:items-center justify-between gap-space-xs pb-space-sm mb-space-sm bg-surface-container-low px-space-md py-space-xs rounded border border-outline-variant/30">
                    <div
                        class="flex items-center gap-space-xs text-on-surface-variant font-telemetry-micro text-telemetry-micro uppercase tracking-wider flex-wrap">
                        <span class="text-on-surface/60">ROOT</span>
                        <span class="text-outline">/</span>
                        <span class="text-on-surface/60">GOVERNANCE</span>
                        <span class="text-outline">/</span>
                        <span class="text-on-surface/60">PRIVILEGED IDENTITY</span>
                        <span class="text-outline">/</span>
                        <span class="font-bold text-primary">PRIVILEGED ACCOUNTS &amp; CREDENTIAL VAULT</span>
                    </div>
                    <div class="flex items-center gap-space-sm">
                        <div
                            class="flex items-center gap-space-xs font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                            <span class="w-2 h-2 rounded-full bg-secondary-fixed animate-ping"></span>
                            <span class="font-bold text-primary">VAULT STATUS:</span>
                            <span class="text-secondary font-semibold">SEALED (HARDWARE HSM FIPS-140-3 LEVEL 4)</span>
                        </div>
                        <div class="h-3 w-[1px] bg-outline-variant"></div>
                        <span
                            class="font-security-stamp text-[10px] px-space-xs py-[1px] bg-primary text-on-primary rounded uppercase">ZERO-STANDING-PRIVILEGE</span>
                    </div>
                </div>
                <!-- Main Header & Emergency Action Toolbar -->
                <div
                    class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md mb-space-base bg-surface-container-lowest p-space-base rounded border border-outline-variant/40">
                    <div class="flex flex-col gap-space-2xs">
                        <div class="flex items-center gap-space-sm flex-wrap">
                            <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">
                                PRIVILEGED ACCOUNTS MONITORING &amp; VAULT CONTROL</h1>
                            <span
                                class="font-security-stamp text-security-stamp bg-error/10 text-error border border-error/40 px-space-xs py-[2px] rounded uppercase font-bold tracking-wider">SEC-LEVEL
                                5 HIGH TRUST</span>
                            <span
                                class="font-telemetry-micro text-telemetry-micro bg-surface-container px-space-xs py-[2px] text-on-surface-variant rounded border border-outline-variant/50">JURISDICTION:
                                KZ-02 ALMATY MATRIX</span>
                        </div>
                        <div
                            class="flex items-center gap-space-xs font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                            <span class="text-primary font-bold">SPECIFICATION:</span>
                            <span class="font-telemetry-data text-primary-container font-semibold">SPEC:
                                PAM-GOV-2026-089 // AUTOMATED EPHEMERAL LEASE</span>
                            <span class="text-outline-variant">•</span>
                            <span>VAULT EPOCH: #98,124</span>
                            <span class="text-outline-variant">•</span>
                            <span>LEASE REVOCATION LATENCY: &lt;140ms</span>
                        </div>
                    </div>
                    <!-- Actions -->
                    <div class="flex items-center gap-space-sm flex-wrap"><button
                            class="h-control-height-md px-space-base bg-secondary-fixed hover:bg-secondary-fixed-dim text-on-secondary-fixed font-title-sm text-[13px] font-bold rounded flex items-center gap-space-xs transition-all shadow border border-secondary-fixed shadow-[0_0_12px_rgba(152,240,251,0.4)] hover:shadow-[0_0_16px_rgba(152,240,251,0.6)] cursor-pointer"
                            id="btn-request-cred"><span
                                class="material-symbols-outlined text-[18px]">vpn_key</span><span>Request Ephemeral
                                Credential</span><span
                                class="font-security-stamp text-[9px] bg-primary text-secondary-fixed px-1 rounded ml-1">MFA-REQ</span></button><button
                            class="h-control-height-md px-space-md bg-surface-container-lowest hover:bg-surface-container-high text-primary font-title-sm text-[13px] font-bold rounded border-2 border-primary/70 flex items-center gap-space-xs transition-colors cursor-pointer shadow-sm"
                            id="btn-checkin-all"><span
                                class="material-symbols-outlined text-[17px] text-primary">lock_reset</span><span>Check-in
                                All Active Leases</span></button><button
                            class="h-control-height-md px-space-md bg-error hover:bg-on-error-container text-on-error font-security-stamp text-[11px] font-bold uppercase rounded border border-error flex items-center gap-space-xs transition-all shadow-sm hover:shadow"
                            onclick="confirm('CRITICAL OVERRIDE: Sever all interactive sessions and force global bastions to DEFCON-2 lockout?')"><span
                                class="material-symbols-outlined text-[16px]">power_off</span><span>Emergency Session
                                Sever (Lockout)</span></button></div>
                </div>
                <!-- KPI Metrics Banner with Exact 4px Left Classification Strips -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md mb-space-base">
                    <!-- Metric 1: Active Leases (Red strip: Highly Confidential #B23A32) -->
                    <div
                        class="bg-surface-container-lowest p-space-md rounded border-t border-r border-b border-outline-variant/40 border-l-4 border-l-[#B23A32] shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-space-xs">
                            <span
                                class="font-label-uppercase text-label-uppercase text-on-surface-variant font-bold tracking-wider">ACTIVE
                                VAULT LEASES</span>
                            <span
                                class="font-security-stamp text-[10px] text-[#B23A32] bg-error-container/40 px-space-xs py-[1px] font-bold rounded">HIGH
                                CONFIDENTIAL</span>
                        </div>
                        <div class="flex items-baseline justify-between mb-space-xs">
                            <div class="font-telemetry-data text-[22px] font-bold text-primary tracking-tight"
                                id="kpi-active-leases-count">07 ACCOUNTS CHECKED OUT</div>
                        </div>
                        <div
                            class="text-body-compact font-body-compact text-on-surface-variant flex flex-col gap-space-2xs">
                            <span>3 Root / 2 DB Admin / 2 SCADA Bastion</span>
                            <div
                                class="flex items-center justify-between text-telemetry-micro font-telemetry-micro pt-space-xs border-t border-surface-container">
                                <span class="text-error font-semibold flex items-center gap-space-2xs">
                                    <span class="material-symbols-outlined text-[14px]">timer</span>
                                    Max lease remaining:
                                </span>
                                <span class="font-bold text-error bg-error-container/30 px-space-xs rounded">01h
                                    42m</span>
                            </div>
                        </div>
                    </div>
                    <!-- Metric 2: Pending Dual-Custody (Amber strip: Confidential #D9822B) -->
                    <div
                        class="bg-surface-container-lowest p-space-md rounded border-t border-r border-b border-outline-variant/40 border-l-4 border-l-[#D9822B] shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-space-xs">
                            <span
                                class="font-label-uppercase text-label-uppercase text-on-surface-variant font-bold tracking-wider">PENDING
                                DUAL-CUSTODY REQUESTS</span>
                            <span
                                class="font-security-stamp text-[10px] text-[#D9822B] bg-tertiary-fixed/30 px-space-xs py-[1px] font-bold rounded">CONFIDENTIAL</span>
                        </div>
                        <div class="flex items-baseline justify-between mb-space-xs">
                            <div class="font-telemetry-data text-[22px] font-bold text-[#D9822B] tracking-tight"
                                id="kpi-pending-signoff-count">03 AWAITING SIGN-OFF</div>
                        </div>
                        <div
                            class="text-body-compact font-body-compact text-on-surface-variant flex flex-col gap-space-2xs">
                            <span class="truncate">Requires CGO Akhmetov or Lead Sadykova</span>
                            <div
                                class="flex items-center justify-between text-telemetry-micro font-telemetry-micro pt-space-xs border-t border-surface-container">
                                <span class="text-on-surface-variant font-semibold flex items-center gap-space-2xs">
                                    <span class="material-symbols-outlined text-[14px]">draw</span>
                                    Hardware Quorum:
                                </span>
                                <span class="font-bold text-primary">2-of-3 FIPS Keys</span>
                            </div>
                        </div>
                    </div>
                    <!-- Metric 3: Automated Password/Key Rotation (Blue strip: Internal #3E7CB1) -->
                    <div
                        class="bg-surface-container-lowest p-space-md rounded border-t border-r border-b border-outline-variant/40 border-l-4 border-l-[#3E7CB1] shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-space-xs">
                            <span
                                class="font-label-uppercase text-label-uppercase text-on-surface-variant font-bold tracking-wider">AUTOMATED
                                KEY ROTATION</span>
                            <span
                                class="font-security-stamp text-[10px] text-[#3E7CB1] bg-primary-fixed/40 px-space-xs py-[1px] font-bold rounded">INTERNAL
                                OPS</span>
                        </div>
                        <div class="flex items-baseline justify-between mb-space-xs">
                            <div class="font-telemetry-data text-[22px] font-bold text-primary tracking-tight">99.4%
                                ROTATION SLA</div>
                        </div>
                        <div
                            class="text-body-compact font-body-compact text-on-surface-variant flex flex-col gap-space-2xs">
                            <span class="truncate">Next HSM batch in 48m (SYS-03 &amp; SYS-05)</span>
                            <div
                                class="flex items-center justify-between text-telemetry-micro font-telemetry-micro pt-space-xs border-t border-surface-container">
                                <span class="text-secondary font-semibold flex items-center gap-space-2xs">
                                    <span class="material-symbols-outlined text-[14px]">sync</span>
                                    Rotated Last 24h:
                                </span>
                                <span class="font-bold text-primary">142 Secrets</span>
                            </div>
                        </div>
                    </div>
                    <!-- Metric 4: Risk & Threat Posture (Slate strip: Public/Operational #8A94A0) -->
                    <div
                        class="bg-surface-container-lowest p-space-md rounded border-t border-r border-b border-outline-variant/40 border-l-4 border-l-[#8A94A0] shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-space-xs">
                            <span
                                class="font-label-uppercase text-label-uppercase text-on-surface-variant font-bold tracking-wider">ACTIVE
                                RISK &amp; THREAT SCORE</span>
                            <span
                                class="font-security-stamp text-[10px] text-on-surface-variant bg-surface-container px-space-xs py-[1px] font-bold rounded">OPERATIONAL</span>
                        </div>
                        <div class="flex items-baseline justify-between mb-space-xs">
                            <div class="font-telemetry-data text-[22px] font-bold text-secondary tracking-tight">LOW
                                ANOMALY POSTURE</div>
                        </div>
                        <div
                            class="text-body-compact font-body-compact text-on-surface-variant flex flex-col gap-space-2xs">
                            <span class="truncate">0 unmonitored sessions, 100% bastions live</span>
                            <div
                                class="flex items-center justify-between text-telemetry-micro font-telemetry-micro pt-space-xs border-t border-surface-container">
                                <span class="text-on-surface-variant flex items-center gap-space-2xs">
                                    <span class="material-symbols-outlined text-[14px]">videocam</span>
                                    OCR Keystream Audit:
                                </span>
                                <span class="font-bold text-secondary">ACTIVE ENFORCED</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Master / Detail Grid -->
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-base items-start">
                    <!-- LEFT COLUMN: Privileged Identity Registry & Vault Inventory (65% -> col-span-8) -->
                    <div class="xl:col-span-8 flex flex-col gap-space-md">
                        <!-- Filter Bar & Search Chassis -->
                        <div
                            class="bg-surface-container-lowest p-space-md rounded border border-outline-variant/40 shadow-sm flex flex-col gap-space-sm">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-sm">
                                <!-- Segmented Filter Tabs -->
                                <div
                                    class="flex items-center gap-[2px] bg-surface-container p-[2px] rounded border border-outline-variant/50 overflow-x-auto">
                                    <button
                                        class="px-space-sm py-[4px] text-body-compact font-body-compact font-bold bg-primary text-on-primary rounded-sm shadow-sm whitespace-nowrap">
                                        All Privileged (<?= count($privilegedAccounts) ?>)
                                    </button>
                                    <button
                                        class="px-space-sm py-[4px] text-body-compact font-body-compact font-medium text-on-surface-variant hover:text-primary hover:bg-surface-container-high rounded-sm transition-colors whitespace-nowrap">
                                        Active Leases (7)
                                    </button>
                                    <button
                                        class="px-space-sm py-[4px] text-body-compact font-body-compact font-medium text-[#D9822B] hover:bg-tertiary-fixed/20 rounded-sm transition-colors whitespace-nowrap flex items-center gap-space-2xs">
                                        <span class="w-2 h-2 rounded-full bg-[#D9822B]"></span>
                                        Pending Approval (3)
                                    </button>
                                    <button
                                        class="px-space-sm py-[4px] text-body-compact font-body-compact font-medium text-error hover:bg-error-container/30 rounded-sm transition-colors whitespace-nowrap">
                                        High-Risk Tier 0 (5)
                                    </button>
                                    <button
                                        class="px-space-sm py-[4px] text-body-compact font-body-compact font-medium text-on-surface-variant hover:text-primary hover:bg-surface-container-high rounded-sm transition-colors whitespace-nowrap">
                                        Vaulted Services (6)
                                    </button>
                                </div>
                                <!-- Quick Refresh / Density Toggle -->
                                <div
                                    class="flex items-center gap-space-xs font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                                    <span class="font-bold text-primary">POLLING:</span>
                                    <span class="font-telemetry-data text-secondary font-semibold">1,000ms</span>
                                    <button
                                        class="p-1 hover:bg-surface-container rounded text-primary transition-colors"
                                        title="Force Inventory Sync">
                                        <span class="material-symbols-outlined text-[16px]">refresh</span>
                                    </button>
                                </div>
                            </div>
                            <!-- Monospaced Search Input with Parameter Helper -->
                            <div class="flex flex-col md:flex-row items-center gap-space-sm">
                                <div class="relative flex-1 w-full">
                                    <span
                                        class="material-symbols-outlined absolute left-space-sm top-1/2 -translate-y-1/2 text-[16px] text-on-surface-variant">filter_alt</span>
                                    <input
                                        class="w-full h-control-height-md bg-surface pl-space-lg pr-space-base font-telemetry-data text-telemetry-data text-primary rounded border border-outline-variant focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                                        placeholder="Filter query syntax (e.g. host:sys-01 user:root)" type="text"
                                        value="lease_status:ACTIVE OR tier:0" />
                                </div>
                                <div
                                    class="flex items-center gap-space-xs text-telemetry-micro font-telemetry-micro text-on-surface-variant shrink-0">
                                    <span
                                        class="bg-surface-container-high px-space-xs py-[3px] rounded border border-outline-variant/50 font-bold">MATCH:
                                        7 OF 18 RECORDS</span>
                                    <button
                                        class="h-control-height-md px-space-sm bg-surface hover:bg-surface-container text-primary font-semibold rounded border border-outline-variant text-body-compact">Reset</button>
                                </div>
                            </div>
                        </div>
                        <!-- High-Density Privileged Identity Registry Table -->
                        <div
                            class="bg-surface-container-lowest rounded border border-outline-variant/40 shadow-sm overflow-hidden">
                            <div
                                class="h-[34px] px-space-base bg-surface-container-low border-b border-outline-variant/40 flex items-center justify-between">
                                <div class="flex items-center gap-space-xs">
                                    <span
                                        class="material-symbols-outlined text-[16px] text-primary">shield_person</span>
                                    <span
                                        class="font-title-sm text-[13px] font-bold text-primary uppercase tracking-wider">SECURED
                                        VAULT DIRECTORY &amp; ACTIVE LEASE REGISTRY</span>
                                </div>
                                <div
                                    class="flex items-center gap-space-md font-telemetry-micro text-telemetry-micro text-on-surface-variant">
                                    <span>EXPORT: <a class="text-secondary font-bold underline" href="#">CSV</a> / <a
                                            class="text-secondary font-bold underline" href="#">JSON-SIG</a></span>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr
                                            class="bg-surface-container border-b border-on-background/20 font-telemetry-micro text-[11px] font-bold text-on-surface-variant uppercase tracking-wider">
                                            <th class="py-space-xs px-space-sm border-r border-outline-variant/30">
                                                Account &amp; Target Host</th>
                                            <th class="py-space-xs px-space-sm border-r border-outline-variant/30">
                                                Assigned Operator</th>
                                            <th class="py-space-xs px-space-sm border-r border-outline-variant/30">Vault
                                                Clearance</th>
                                            <th class="py-space-xs px-space-sm border-r border-outline-variant/30">
                                                Access Protocol</th>
                                            <th class="py-space-xs px-space-sm border-r border-outline-variant/30">Lease
                                                / TTL Status</th>
                                            <th class="py-space-xs px-space-sm border-r border-outline-variant/30">
                                                Integrity Check</th>
                                            <th class="py-space-xs px-space-sm text-right">Console Action</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="divide-y divide-outline-variant/30 font-body-compact text-body-compact"
                                        id="vault-registry-tbody">
                                        <?php foreach ($privilegedAccounts as $idx => $pa): 
                                            $isTier0 = ($pa['clearance_level'] === 'L4');
                                            $isActiveSession = !empty($pa['session_id']);
                                        ?>
                                        <tr class="hover:bg-surface-container transition-colors <?= $isActiveSession ? 'border-l-4 border-l-secondary bg-surface-container-lowest' : 'bg-surface-container-lowest' ?>">
                                            <td class="py-space-xs px-space-sm border-r border-outline-variant/20">
                                                <div class="flex flex-col">
                                                    <div class="flex items-center gap-space-xs">
                                                        <?php if ($isActiveSession): ?>
                                                            <span class="w-2 h-2 rounded-full bg-secondary-fixed animate-pulse"></span>
                                                        <?php endif; ?>
                                                        <span class="font-telemetry-data text-telemetry-data font-bold <?= $isTier0 ? 'text-error' : 'text-primary' ?>">
                                                            <?= htmlspecialchars($pa['username']) ?>@vostok-vault
                                                        </span>
                                                    </div>
                                                    <span class="font-telemetry-micro text-[10px] text-on-surface-variant">IP: 10.240.<?= (int)$pa['account_id'] ?>.10 • <?= htmlspecialchars($pa['dept_name'] ?? 'SEC-OPS') ?></span>
                                                </div>
                                            </td>
                                            <td class="py-space-xs px-space-sm border-r border-outline-variant/20">
                                                <div class="flex flex-col">
                                                    <span class="font-semibold text-primary"><?= htmlspecialchars($pa['full_name']) ?></span>
                                                    <span class="font-telemetry-micro text-[10px] text-on-surface-variant"><?= htmlspecialchars($pa['emp_id']) ?> • <?= htmlspecialchars($pa['job_title']) ?></span>
                                                </div>
                                            </td>
                                            <td class="py-space-xs px-space-sm border-r border-outline-variant/20">
                                                <span class="font-security-stamp text-[10px] px-space-xs py-[2px] <?= $isTier0 ? 'bg-error text-on-error' : 'bg-primary text-on-primary' ?> font-bold rounded">
                                                    <?= $isTier0 ? 'TIER 0 (ROOT)' : 'TIER 1 (PRIV)' ?>
                                                </span>
                                            </td>
                                            <td class="py-space-xs px-space-sm border-r border-outline-variant/20 font-telemetry-micro text-telemetry-micro">
                                                Bastion SSH / MFA: <?= $pa['mfa_enabled'] ? '<span class="text-secondary font-bold">YES</span>' : '<span class="text-error font-bold">NO</span>' ?>
                                            </td>
                                            <td class="py-space-xs px-space-sm border-r border-outline-variant/20">
                                                <div class="flex flex-col">
                                                    <span class="font-telemetry-micro text-telemetry-micro font-bold <?= $isActiveSession ? 'text-secondary' : 'text-on-surface-variant' ?>">
                                                        <?= $isActiveSession ? 'ACTIVE LEASE' : 'STANDBY' ?>
                                                    </span>
                                                    <span class="font-telemetry-micro text-[10px] text-on-surface-variant">Last: <?= htmlspecialchars($pa['last_login'] ?? 'Never') ?></span>
                                                </div>
                                            </td>
                                            <td class="py-space-xs px-space-sm border-r border-outline-variant/20">
                                                <span class="inline-flex items-center gap-[4px] px-space-xs py-[2px] <?= ($pa['account_status'] === 'Active') ? 'bg-secondary-container/30 text-on-secondary-container' : 'bg-error-container text-on-error-container' ?> font-security-stamp text-[10px] font-bold rounded">
                                                    <span class="w-1.5 h-1.5 rounded-full <?= ($pa['account_status'] === 'Active') ? 'bg-secondary' : 'bg-error' ?>"></span>
                                                    <?= ($pa['account_status'] === 'Active') ? 'NOMINAL' : 'REVOKED' ?>
                                                </span>
                                            </td>
                                            <td class="py-space-xs px-space-sm text-right">
                                                <div class="flex items-center justify-end gap-space-2xs">
                                                    <button class="px-space-xs py-[3px] bg-surface-container text-primary hover:bg-surface-container-high border border-outline-variant rounded font-telemetry-micro text-telemetry-micro font-bold" onclick="alert('Auditing <?= htmlspecialchars($pa['emp_id']) ?> session token')">
                                                        Inspect
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Table Footer / Telemetry Summary -->
                            <div
                                class="p-space-xs px-space-base bg-surface-container-low border-t border-outline-variant/40 flex flex-col sm:flex-row items-center justify-between text-telemetry-micro font-telemetry-micro text-on-surface-variant">
                                <div class="flex items-center gap-space-sm">
                                    <span>SHOWING <?= count($privilegedAccounts) ?> PRIVILEGED IDENTITIES FROM DATABASE</span>
                                    <span class="text-outline-variant">•</span>
                                    <span>VAULT CIPHER: AES-256-GCM / PBKDF2 HARDENED</span>
                                </div>
                                <div class="flex items-center gap-space-xs">
                                    <span class="w-2 h-2 rounded-full bg-secondary"></span>
                                    <span>ALL BASTION REPLAY TAPES STREAMING TO WORM STORAGE</span>
                                </div>
                            </div>
                        </div>
                        <!-- Historical Session Telemetry & Keystroke Distribution Sparkline Card -->
                        <div
                            class="bg-surface-container-lowest p-space-md rounded border border-outline-variant/40 shadow-sm flex flex-col gap-space-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-space-xs">
                                    <span class="material-symbols-outlined text-[16px] text-primary">analytics</span>
                                    <span class="font-title-sm text-[13px] font-bold text-primary uppercase">24-Hour
                                        Privileged Activity Density &amp; Elevation Frequency</span>
                                </div>
                                <span class="font-telemetry-micro text-telemetry-micro text-on-surface-variant">HOURLY
                                    AGGREGATE // BASTIONS 01-04</span>
                            </div>
                            <!-- Inline SVG Sparkline / Activity Graph -->
                            <div
                                class="w-full h-20 bg-surface-container-low p-space-xs rounded border border-outline-variant/30 relative flex items-end">
                                <svg class="w-full h-full text-secondary" fill="none" preserveaspectratio="none"
                                    viewbox="0 0 500 80">
                                    <path
                                        d="M0,65 L25,60 L50,70 L75,45 L100,55 L125,30 L150,50 L175,20 L200,40 L225,15 L250,35 L275,10 L300,25 L325,45 L350,20 L375,15 L400,30 L425,18 L450,22 L475,8 L500,24"
                                        stroke="currentColor" stroke-width="2" vector-effect="non-scaling-stroke">
                                    </path>
                                    <path
                                        d="M0,65 L25,60 L50,70 L75,45 L100,55 L125,30 L150,50 L175,20 L200,40 L225,15 L250,35 L275,10 L300,25 L325,45 L350,20 L375,15 L400,30 L425,18 L450,22 L475,8 L500,24 L500,80 L0,80 Z"
                                        fill="currentColor" fill-opacity="0.08"></path>
                                    <!-- Overlay warning marker -->
                                    <line stroke="#B23A32" stroke-dasharray="3 3" stroke-width="1.5" x1="275" x2="275"
                                        y1="0" y2="80"></line>
                                </svg>
                                <div
                                    class="absolute top-2 left-[280px] bg-primary text-on-primary font-telemetry-micro text-[10px] px-1 rounded shadow">
                                    PEAK LEASES: 14 AT 11:00 UTC+6
                                </div>
                            </div>
                            <div
                                class="grid grid-cols-3 gap-space-sm text-telemetry-micro font-telemetry-micro pt-space-xs border-t border-surface-container">
                                <div>
                                    <span class="text-on-surface-variant block">TOTAL PRIVILEGED COMMANDS</span>
                                    <span class="font-bold text-primary font-telemetry-data text-body-compact">4,819
                                        keystrokes logged</span>
                                </div>
                                <div>
                                    <span class="text-on-surface-variant block">SUSPICIOUS STRINGS BLOCKED</span>
                                    <span class="font-bold text-error font-telemetry-data text-body-compact">0 events
                                        detected</span>
                                </div>
                                <div>
                                    <span class="text-on-surface-variant block">AVERAGE LEASE HOLD TIME</span>
                                    <span class="font-bold text-secondary font-telemetry-data text-body-compact">52m
                                        14s</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- RIGHT COLUMN: Session Inspector, Approval Desk & Receipts (35% -> col-span-4) -->
                    <div class="xl:col-span-4 flex flex-col gap-space-base">
                        <!-- Panel 1: Live Bastion Keystream Inspector (Red Left Border) -->
                        <div
                            class="bg-surface-container-lowest rounded border-t border-r border-b border-outline-variant/40 border-l-4 border-l-[#B23A32] shadow-sm overflow-hidden">
                            <!-- Header -->
                            <div
                                class="h-[36px] px-space-md bg-surface-container-low border-b border-outline-variant/30 flex items-center justify-between">
                                <div class="flex items-center gap-space-xs">
                                    <span class="w-2 h-2 rounded-full bg-error animate-ping"></span>
                                    <span
                                        class="font-title-sm text-[12px] font-bold text-primary uppercase tracking-wider">LIVE
                                        BASTION KEYSTREAM INSPECTOR</span>
                                </div>
                                <span
                                    class="font-security-stamp text-[10px] text-error bg-error-container/40 px-space-xs py-[1px] font-bold rounded">#PAM-9082</span>
                            </div>
                            <div class="p-space-md flex flex-col gap-space-sm">
                                <!-- Identity Meta -->
                                <div
                                    class="flex items-center justify-between text-telemetry-micro font-telemetry-micro bg-surface-container-low p-space-xs rounded border border-outline-variant/30">
                                    <div>
                                        <span class="text-on-surface-variant block">OPERATOR &amp; TARGET</span>
                                        <span class="font-bold text-primary font-telemetry-data">K. Nurzhanov
                                            (EMP-1001)</span>
                                        <span class="text-primary-container block">admin_grid@sys-05-balkhash</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-on-surface-variant block">SESSION UPTIME</span>
                                        <span class="font-bold text-secondary font-telemetry-data">00h 16m 12s</span>
                                        <span class="text-error font-bold block">RECORDING OCR</span>
                                    </div>
                                </div>
                                <!-- Terminal Preview Box (Dark Neobrutalist Console) -->
                                <div
                                    class="bg-[#0F2438] text-[#96EEF9] font-telemetry-micro p-space-sm rounded border border-primary text-[11px] leading-[17px] flex flex-col gap-1 font-mono shadow-inner">
                                    <div
                                        class="flex items-center justify-between border-b border-outline-variant/20 pb-1 text-on-primary-container text-[10px]">
                                        <span>PTY: /dev/pts/4 (BASTION-ALMATY-02)</span>
                                        <span>CIPHER: CHACHA20-POLY1305</span>
                                    </div>
                                    <div class="text-on-primary/60 pt-1">
                                        Last login: 2026-03-31 15:28:10 from 10.240.254.4
                                    </div>
                                    <div class="text-[#96EEF9]">
                                        [sys-05-balkhash:~$] <span class="text-white font-bold">systemctl status
                                            substation-telemetry.service</span>
                                    </div>
                                    <div class="text-on-primary/80 pl-2">
                                        — substation-telemetry.service - Vostok Balkhash Substation Relay<br />
                                          Loaded: loaded (/etc/systemd/system/substation-telemetry.service)<br />
                                          Active: active (running) since Tue 2026-03-31 08:00:14 UTC
                                    </div>
                                    <div class="text-[#96EEF9] pt-1">
                                        [sys-05-balkhash:~$] <span class="text-white font-bold">openssl verify -CAfile
                                            /etc/vostok/ca.crt cert.pem</span>
                                    </div>
                                    <div class="text-secondary-fixed pl-2">
                                        cert.pem: OK (Serial: 9F:44:1B:77:01, Expiry: 2027-01-01)
                                    </div>
                                    <div class="text-[#96EEF9] pt-1 flex items-center">
                                        <span>[sys-05-balkhash:~$] </span>
                                        <span class="w-2 h-3.5 bg-secondary-fixed animate-pulse inline-block"></span>
                                    </div>
                                </div>
                                <!-- Real-time Operator Controls -->
                                <div class="flex flex-col gap-space-xs pt-space-xs">
                                    <div class="grid grid-cols-2 gap-space-xs">
                                        <button
                                            class="h-control-height-sm px-space-xs bg-surface-container hover:bg-surface-container-high text-primary rounded font-telemetry-micro text-telemetry-micro font-bold border border-outline-variant flex items-center justify-center gap-1"
                                            onclick="alert('Attached to terminal in read-only audit shadow mode.')">
                                            <span class="material-symbols-outlined text-[14px]">visibility</span>
                                            Shadow Session (RO)
                                        </button>
                                        <button
                                            class="h-control-height-sm px-space-xs bg-surface-container hover:bg-surface-container-high text-primary rounded font-telemetry-micro text-telemetry-micro font-bold border border-outline-variant flex items-center justify-center gap-1"
                                            onclick="prompt('Enter administrative message to inject into operator console:')">
                                            <span class="material-symbols-outlined text-[14px]">campaign</span>
                                            Broadcast to Shell
                                        </button>
                                    </div>
                                    <button
                                        class="h-control-height-sm w-full bg-error hover:bg-on-error-container text-on-error rounded font-security-stamp text-[10px] uppercase font-bold tracking-wider flex items-center justify-center gap-1"
                                        onclick="confirm('CRITICAL ACTION: Immediately sever session #PAM-9082 and invalidate the ephemeral lease?')">
                                        <span class="material-symbols-outlined text-[14px]">cancel</span>
                                        Terminate &amp; Sever Leased Credentials
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Panel 2: Rapid Dual-Custody Approval Desk (Amber Left Border) -->
                        <div
                            class="bg-surface-container-lowest rounded border-t border-r border-b border-outline-variant/40 border-l-4 border-l-[#D9822B] shadow-sm overflow-hidden">
                            <!-- Header -->
                            <div
                                class="h-[36px] px-space-md bg-surface-container-low border-b border-outline-variant/30 flex items-center justify-between">
                                <div class="flex items-center gap-space-xs">
                                    <span class="material-symbols-outlined text-[16px] text-[#D9822B]">fact_check</span>
                                    <span
                                        class="font-title-sm text-[12px] font-bold text-primary uppercase tracking-wider">RAPID
                                        ELEVATION DESK (DUAL-CUSTODY)</span>
                                </div>
                                <span
                                    class="font-telemetry-micro text-[10px] text-[#D9822B] font-bold bg-tertiary-fixed/30 px-space-xs py-[1px] rounded">1
                                    OF 3 QUEUED</span>
                            </div>
                            <div class="p-space-md flex flex-col gap-space-sm">
                                <!-- Elevation Context -->
                                <div
                                    class="border border-outline-variant/30 rounded p-space-sm bg-surface-container-lowest flex flex-col gap-space-xs">
                                    <div class="flex items-center justify-between">
                                        <span
                                            class="font-telemetry-data text-telemetry-data font-bold text-primary">#REQ-8821
                                            // TIER 2 ELEVATION</span>
                                        <span
                                            class="font-security-stamp text-[10px] text-[#D9822B] bg-tertiary-fixed/40 px-space-xs rounded">P2
                                            SEVERITY</span>
                                    </div>
                                    <div
                                        class="text-body-compact font-body-compact text-on-surface flex flex-col gap-1 pt-1 border-t border-surface-container">
                                        <div><strong
                                                class="text-on-surface-variant font-telemetry-micro">APPLICANT:</strong>
                                            Amina Karimova (EMP-1002)</div>
                                        <div><strong class="text-on-surface-variant font-telemetry-micro">TARGET
                                                RESOURCE:</strong> SYS-01 Smelting &amp; Heavy Foundry — Core Hydraulics
                                            PLC Override</div>
                                        <div><strong
                                                class="text-on-surface-variant font-telemetry-micro">REASON:</strong>
                                            Emergency recalibration of thermocouple pressure manifold #4</div>
                                        <div><strong class="text-on-surface-variant font-telemetry-micro">TICKET
                                                LINK:</strong> <a
                                                class="text-secondary font-telemetry-data underline font-bold"
                                                href="#">INC-2026-4412</a></div>
                                        <div><strong class="text-on-surface-variant font-telemetry-micro">REQUESTED
                                                LEASE:</strong> 60 Minutes (Single-Use)</div>
                                    </div>
                                    <!-- Sign-off Quorum Tracker -->
                                    <div
                                        class="bg-surface-container-low p-space-xs rounded border border-outline-variant/30 flex flex-col gap-1 mt-1">
                                        <div
                                            class="flex items-center justify-between text-telemetry-micro font-telemetry-micro">
                                            <span class="font-bold text-primary">SIGN-OFF HARDWARE QUORUM:</span>
                                            <span class="text-error font-bold">1 / 2 Signatures</span>
                                        </div>
                                        <div
                                            class="text-[10px] font-telemetry-micro text-on-surface-variant flex items-center justify-between">
                                            <span>[œ“] EMP-1002 (Karimova, Applicant)</span>
                                            <span class="text-secondary font-bold">SIGNED (15:39)</span>
                                        </div>
                                        <div
                                            class="text-[10px] font-telemetry-micro text-on-surface-variant flex items-center justify-between">
                                            <span>[ ] EMP-1005 (Akhmetov) OR EMP-1018 (Sadykova)</span>
                                            <span class="text-[#D9822B] font-bold">PENDING FIPS KEY</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Dual Custody Interactive Actions -->
                                <div class="flex flex-col gap-space-xs">
                                    <button
                                        class="h-control-height-md w-full bg-[#0E7C86] hover:bg-secondary text-white rounded font-title-sm text-[13px] font-semibold flex items-center justify-center gap-space-xs shadow-sm"
                                        onclick="alert('Hardware key challenge sent to physical FIPS token. Touch token to finalize elevation.')">
                                        <span class="material-symbols-outlined text-[16px]">fingerprint</span>
                                        <span>Approve Elevation with 60m Lease</span>
                                    </button>
                                    <div class="grid grid-cols-2 gap-space-xs">
                                        <button
                                            class="h-control-height-sm bg-surface-container hover:bg-surface-container-high text-error rounded font-telemetry-micro text-telemetry-micro font-bold border border-outline-variant"
                                            onclick="confirm('Reject and record justification in vault ledger?')">
                                            Reject Request
                                        </button>
                                        <button
                                            class="h-control-height-sm bg-surface-container hover:bg-surface-container-high text-primary rounded font-telemetry-micro text-telemetry-micro font-bold border border-outline-variant"
                                            onclick="alert('Request escalated to Board Risk Committee &amp; Chief Governance Officer.')">
                                            Escalate to Board
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Panel 3: Tamper-Evident Cryptographic Ledger Receipt -->
                        <div
                            class="bg-surface-container-lowest p-space-md rounded border border-outline-variant/40 shadow-sm flex flex-col gap-space-xs">
                            <div class="flex items-center justify-between">
                                <span
                                    class="font-label-uppercase text-label-uppercase text-on-surface-variant font-bold">CRYPTOGRAPHIC
                                    VAULT RECEIPT</span>
                                <span class="material-symbols-outlined text-[16px] text-secondary">verified</span>
                            </div>
                            <div
                                class="font-telemetry-micro text-[11px] bg-surface-container-low p-space-xs rounded border border-outline-variant/30 flex flex-col gap-1 text-on-surface-variant">
                                <div class="flex justify-between">
                                    <span>MERKLE ROOT HASH:</span>
                                    <span class="font-telemetry-data text-primary font-bold">0x9F4B...7A12</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>TIMESTAMP:</span>
                                    <span class="font-telemetry-data text-primary">2026-03-31 15:44:02 UTC+6</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>SIGNER:</span>
                                    <span class="font-telemetry-data text-primary">HSM-ALMATY-CLUSTER-A</span>
                                </div>
                            </div>
                            <button
                                class="h-control-height-sm w-full bg-surface hover:bg-surface-container text-primary rounded font-telemetry-micro text-telemetry-micro font-bold border border-outline-variant flex items-center justify-center gap-1 transition-colors mt-1"
                                onclick="alert('Cryptographic ledger integrity verified. Chain valid through block #441,890.')">
                                <span class="material-symbols-outlined text-[14px]">history_edu</span>
                                Verify Vault Ledger
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div class="fixed inset-0 z-50 hidden flex items-center justify-center bg-primary/80 backdrop-blur-[2px] p-space-md"
        id="modal-request-credential">
        <div
            class="w-full max-w-xl bg-surface-container-lowest rounded border-2 border-secondary-fixed shadow-2xl flex flex-col overflow-hidden animate-in fade-in zoom-in-95">
            <div
                class="h-[44px] px-space-base bg-primary text-on-primary border-b border-secondary-fixed/40 flex items-center justify-between">
                <div class="flex items-center gap-space-xs"><span
                        class="material-symbols-outlined text-[18px] text-secondary-fixed">vpn_key</span><span
                        class="font-title-sm text-[13px] font-bold tracking-wide uppercase text-on-primary">Request
                        Ephemeral Credential Access</span><span
                        class="font-security-stamp text-[9px] bg-secondary-fixed text-on-secondary-fixed font-extrabold px-1 py-[1px] rounded">HSM
                        PROTOCOL</span></div><button
                    class="p-1 text-on-primary-container hover:text-on-primary hover:bg-primary-container rounded transition-colors"
                    id="close-modal-req"><span class="material-symbols-outlined text-[18px]">close</span></button>
            </div>
            <div class="p-space-base flex flex-col gap-space-md overflow-y-auto max-h-[80vh]">
                <div
                    class="p-space-xs px-space-sm bg-surface-container-low border border-outline-variant/50 rounded flex items-center justify-between text-telemetry-micro font-telemetry-micro text-on-surface-variant">
                    <div><span class="font-bold text-primary">POLICY:</span> ZERO-STANDING-PRIVILEGE // JIT-V4</div>
                    <div class="font-security-stamp text-[10px] text-secondary font-bold">SEALED HSM LEVEL 4</div>
                </div>
                <div class="flex flex-col gap-1"><label
                        class="font-label-uppercase text-label-uppercase text-on-surface-variant font-bold">Target Asset
                        / Host Cluster</label><select
                        class="h-control-height-md bg-surface font-telemetry-data text-telemetry-data text-primary px-space-sm rounded border border-outline-variant focus:outline-none focus:border-primary"
                        id="req-target-asset">
                        <option value="sys-01-smelt (IP: 10.240.10.2 • SMELTING FOUNDRY PLC)">sys-01-smelt (IP:
                            10.240.10.2 • Smelting Foundry PLC)</option>
                        <option value="sys-05-balkhash (IP: 10.240.44.102 • REGIONAL SUBSTATION)">sys-05-balkhash (IP:
                            10.240.44.102 • Regional Substation)</option>
                        <option value="sys-03-cnc-gateway (IP: 10.240.12.8 • INDUSTRIAL CNC)">sys-03-cnc-gateway (IP:
                            10.240.12.8 • Industrial CNC Gateway)</option>
                        <option value="sys-11-core (IP: 10.240.0.1 • CENTRAL JURISDICTION ENGINE)">sys-11-core (IP:
                            10.240.0.1 • Central Core)</option>
                        <option value="sys-07-asrs-db (IP: 10.240.31.14 • ASRS WAREHOUSE CLUSTER)">sys-07-asrs-db (IP:
                            10.240.31.14 • ASRS PostgreSQL Cluster)</option>
                    </select></div>
                <div class="flex flex-col gap-1"><label
                        class="font-label-uppercase text-label-uppercase text-on-surface-variant font-bold">Clearance
                        Level &amp; Privilege Scope</label>
                    <div class="grid grid-cols-3 gap-space-xs"><button
                            class="tier-btn p-space-xs rounded border border-error bg-error/10 text-error flex flex-col items-center gap-[2px] transition-all"
                            data-tier="0" type="button"><span class="font-security-stamp text-[11px] font-bold">TIER 0
                                (ROOT)</span><span
                                class="text-[9px] font-telemetry-micro text-error font-semibold">DUAL-CUSTODY
                                REQ</span></button><button
                            class="tier-btn p-space-xs rounded border border-outline-variant/60 hover:border-[#D9822B] flex flex-col items-center gap-[2px] transition-all"
                            data-tier="1" type="button"><span
                                class="font-security-stamp text-[11px] font-bold text-primary">TIER 1
                                (GRID/DB)</span><span
                                class="text-[9px] font-telemetry-micro text-on-surface-variant">SUPERVISED</span></button><button
                            class="tier-btn p-space-xs rounded border border-outline-variant/60 hover:border-secondary flex flex-col items-center gap-[2px] transition-all"
                            data-tier="2" type="button"><span
                                class="font-security-stamp text-[11px] font-bold text-primary">TIER 2
                                (PLC/RELAY)</span><span
                                class="text-[9px] font-telemetry-micro text-on-surface-variant">STANDARD
                                JIT</span></button></div><input id="req-selected-tier" type="hidden" value="0" />
                </div>
                <div class="flex flex-col gap-1">
                    <div class="flex items-center justify-between"><label
                            class="font-label-uppercase text-label-uppercase text-on-surface-variant font-bold">Requested
                            Duration / Ephemeral TTL</label><span
                            class="font-telemetry-data text-telemetry-micro text-secondary font-bold"
                            id="req-ttl-display">1 Hour (Recommended)</span></div>
                    <div class="grid grid-cols-4 gap-space-xs"><button
                            class="ttl-btn py-space-xs px-1 text-center font-telemetry-micro text-[11px] rounded border border-outline-variant/50 hover:bg-surface-container font-medium"
                            data-duration="15 Mins" type="button">15 Mins</button><button
                            class="ttl-btn py-space-xs px-1 text-center font-telemetry-micro text-[11px] rounded border-2 border-primary bg-primary-container text-on-primary font-bold"
                            data-duration="1 Hour" type="button">1 Hour</button><button
                            class="ttl-btn py-space-xs px-1 text-center font-telemetry-micro text-[11px] rounded border border-outline-variant/50 hover:bg-surface-container font-medium"
                            data-duration="4 Hours" type="button">4 Hours</button><button
                            class="ttl-btn py-space-xs px-1 text-center font-telemetry-micro text-[11px] rounded border border-outline-variant/50 hover:bg-surface-container font-medium"
                            data-duration="8 Hours Max" type="button">8 Hours Max</button></div><input
                        id="req-selected-ttl" type="hidden" value="1 Hour" />
                </div>
                <div class="flex flex-col gap-1"><label
                        class="font-label-uppercase text-label-uppercase text-on-surface-variant font-bold">Justification
                        / Incident Ticket Ref</label><input
                        class="h-control-height-md bg-surface font-telemetry-data text-telemetry-data text-primary px-space-sm rounded border border-outline-variant focus:outline-none focus:border-primary"
                        id="req-justification" type="text" value="INC-2026-99023 // Emergency SCADA patch" /></div>
                <div class="p-space-xs bg-error-container/30 border-l-4 border-error rounded flex items-start gap-space-xs"
                    id="tier0-notice"><span
                        class="material-symbols-outlined text-[18px] text-error shrink-0">security</span>
                    <div class="font-telemetry-micro text-[11px] text-on-error-container leading-tight"><strong
                            class="font-bold uppercase">Mandatory Dual-Custody:</strong> Tier 0 root requests enforce
                        secondary signature verification under DOC-2026-088. Approver quorum: Timur Akhmetov (EMP-1005)
                        or Leonid Volkov (EMP-1018).</div>
                </div>
            </div>
            <div
                class="p-space-base bg-surface-container-low border-t border-outline-variant/40 flex items-center justify-between">
                <button
                    class="h-control-height-md px-space-md rounded font-telemetry-micro text-telemetry-micro font-bold bg-surface hover:bg-surface-container border border-outline-variant text-on-surface-variant"
                    id="cancel-modal-req">Cancel</button><button
                    class="h-control-height-md px-space-base bg-secondary-fixed hover:bg-secondary-fixed-dim text-on-secondary-fixed font-title-sm text-[13px] font-bold rounded flex items-center gap-space-xs shadow border border-secondary-fixed"
                    id="submit-modal-req"><span
                        class="material-symbols-outlined text-[16px]">fingerprint</span><span>Submit Request (MFA
                        Attest)</span></button>
            </div>
        </div>
    </div>
    <div class="fixed inset-0 z-50 hidden flex items-center justify-center bg-primary/80 backdrop-blur-[2px] p-space-md"
        id="modal-checkin-confirm">
        <div
            class="w-full max-w-lg bg-surface-container-lowest rounded border-2 border-error shadow-2xl flex flex-col overflow-hidden animate-in fade-in zoom-in-95">
            <div class="h-[44px] px-space-base bg-error text-on-error flex items-center justify-between">
                <div class="flex items-center gap-space-xs"><span
                        class="material-symbols-outlined text-[18px]">warning</span><span
                        class="font-title-sm text-[13px] font-bold tracking-wide uppercase">Revoke &amp; Check-In All
                        Active Leases?</span></div><button
                    class="p-1 text-on-error hover:bg-on-error/20 rounded transition-colors"
                    id="close-modal-checkin"><span class="material-symbols-outlined text-[18px]">close</span></button>
            </div>
            <div class="p-space-base flex flex-col gap-space-md">
                <div
                    class="p-space-sm bg-error-container/40 border-l-4 border-error rounded flex items-start gap-space-sm">
                    <span class="material-symbols-outlined text-[22px] text-error shrink-0">lock_person</span>
                    <div class="flex flex-col gap-1 text-body-compact font-body-compact text-on-error-container">
                        <div><strong class="font-bold text-error">07 Accounts Checked Out</strong> will be immediately
                            terminated, all session tokens and SSH/TLS keyrings revoked, and master secrets safely
                            returned to the Hardware Security Module.</div><span
                            class="font-telemetry-micro text-[11px] text-error font-semibold">IMMEDIATE OPERATIONAL
                            IMPACT // GLOBAL HSM SYNC</span>
                    </div>
                </div>
                <div
                    class="font-telemetry-micro text-telemetry-micro bg-surface-container-low p-space-xs rounded border border-outline-variant/30 flex flex-col gap-1 text-on-surface-variant">
                    <div class="flex justify-between"><span>Affected Bastions:</span><span
                            class="font-bold text-primary">BASTION-01, BASTION-02, BASTION-04</span></div>
                    <div class="flex justify-between"><span>Revocation Protocol:</span><span
                            class="font-bold text-secondary">ECDSA-SECP256K1 HARDWARE ZEROIZATION</span></div>
                </div>
            </div>
            <div
                class="p-space-base bg-surface-container-low border-t border-outline-variant/40 flex items-center justify-between">
                <button
                    class="h-control-height-md px-space-md rounded font-telemetry-micro text-telemetry-micro font-bold bg-surface hover:bg-surface-container border border-outline-variant text-on-surface-variant"
                    id="cancel-modal-checkin">Cancel</button><button
                    class="h-control-height-md px-space-base bg-error hover:bg-on-error-container text-on-error font-title-sm text-[13px] font-bold rounded flex items-center gap-space-xs shadow"
                    id="confirm-mass-checkin"><span
                        class="material-symbols-outlined text-[16px]">lock_reset</span><span>Confirm Mass
                        Check-In</span></button>
            </div>
        </div>
    </div>
    <div class="fixed bottom-space-lg right-space-lg z-50 hidden transition-all duration-300 transform translate-y-4"
        id="toast-notification">
        <div
            class="flex items-center gap-space-sm px-space-base py-space-sm bg-[#0F2438] text-white rounded border border-secondary-fixed shadow-[0_8px_30px_rgba(0,0,0,0.5)] font-telemetry-micro text-telemetry-micro max-w-md">
            <span class="material-symbols-outlined text-secondary-fixed text-[22px]">verified</span>
            <div class="flex flex-col"><span class="font-bold text-secondary-fixed font-mono uppercase"
                    id="toast-title">TRANSACTION RECORDED</span><span class="text-on-primary-container text-[11px]"
                    id="toast-message">All 7 active leases successfully returned to HSM Vault [ECDSA-SECP256K1
                    CONFIRMED]</span></div><button class="p-1 text-on-primary-container hover:text-white"
                onclick="document.getElementById('toast-notification').classList.add('hidden')"><span
                    class="material-symbols-outlined text-[16px]">close</span></button>
        </div>
    </div>
    
    <script src="js/common.js"></script>
    <script src="js/privilegedAccounts.js"></script>
</body>

</html>