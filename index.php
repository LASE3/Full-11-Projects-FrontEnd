<?php
/**
 * VOSTOKPRIBOR Central Ecosystem Launchpad
 * Unified Command Gateway to all 11 enterprise front-end systems.
 */
$systems = [
    [
        'id' => '01',
        'code' => 'WEB',
        'dir' => 'VOSTOKPRIBOR Corporate Web Platform',
        'name' => 'Corporate Web Platform',
        'domain' => 'vostokpribor.local',
        'accent' => '#1B3A5C',
        'icon' => 'language',
        'classification' => 'Public Showcase',
        'desc' => 'Primary corporate digital front, corporate overview, equipment catalogue, global subsidiaries & RFQ portal.'
    ],
    [
        'id' => '02',
        'code' => 'SHP',
        'dir' => 'Online Shop B2B',
        'name' => 'Online Shop B2B',
        'domain' => 'shop.vostokpribor.local',
        'accent' => '#0E7C86',
        'icon' => 'shopping_cart',
        'classification' => 'Commercial B2B',
        'desc' => 'Industrial optical instrumentation storefront, bulk order matrices, inventory tracking and logistics checkout.'
    ],
    [
        'id' => '03',
        'code' => 'CUS',
        'dir' => 'Customer Portal',
        'name' => 'Customer Portal',
        'domain' => 'portal.vostokpribor.local',
        'accent' => '#E8A33D',
        'icon' => 'space_dashboard',
        'classification' => 'Client Extranet',
        'desc' => 'Contracted client telemetry, equipment orders, real-time dispatch manifests and priority ticketing.'
    ],
    [
        'id' => '04',
        'code' => 'EMP',
        'dir' => 'Employee Intranet',
        'name' => 'Employee Intranet',
        'domain' => 'intranet.vostokpribor.local',
        'accent' => '#5C7290',
        'icon' => 'badge',
        'classification' => 'Internal Core',
        'desc' => 'Corporate employee directory, enterprise announcements, internal SOP policies, and departmental forms.'
    ],
    [
        'id' => '05',
        'code' => 'CRM',
        'dir' => 'CRM',
        'name' => 'CRM Platform',
        'domain' => 'crm.vostokpribor.local',
        'accent' => '#3B4C8C',
        'icon' => 'groups',
        'classification' => 'Sales Operations',
        'desc' => 'Enterprise sales pipelines, deal negotiation tracking, quote generation, client dossier lifecycle.'
    ],
    [
        'id' => '06',
        'code' => 'HR',
        'dir' => 'HR System',
        'name' => 'HR Management System',
        'domain' => 'hr.vostokpribor.local',
        'accent' => '#6E4C7C',
        'icon' => 'person_search',
        'classification' => 'Human Capital',
        'desc' => 'Workforce directory, organizational chart hierarchy, recruitment onboarding, and leave ledger.'
    ],
    [
        'id' => '07',
        'code' => 'FIN',
        'dir' => 'Finance & Billing',
        'name' => 'Finance & Billing',
        'domain' => 'finance.vostokpribor.local',
        'accent' => '#2E6E4E',
        'icon' => 'receipt_long',
        'classification' => 'Executive Ledger',
        'desc' => 'Multi-currency invoicing, departmental budgets, cashflow reconciliation, and financial audit logs.'
    ],
    [
        'id' => '08',
        'code' => 'IT',
        'dir' => 'IT Helpdesk',
        'name' => 'IT Helpdesk & Service',
        'domain' => 'helpdesk.vostokpribor.local',
        'accent' => '#C97A3D',
        'icon' => 'support_agent',
        'classification' => 'Operational Desk',
        'desc' => 'Incident escalation queue, hardware & asset tagging, SLA monitoring, and IT self-service knowledge base.'
    ],
    [
        'id' => '09',
        'code' => 'DOC',
        'dir' => 'File Center',
        'name' => 'File Center Hub',
        'domain' => 'files.vostokpribor.local',
        'accent' => '#5A6470',
        'icon' => 'folder_zip',
        'classification' => 'Classified Dossier',
        'desc' => 'Secure cryptographic file repository, document versioning, approval workflows, and legal retention.'
    ],
    [
        'id' => '10',
        'code' => 'DEV',
        'dir' => 'Developer',
        'name' => 'Developer & API Portal',
        'domain' => 'developer.vostokpribor.local',
        'accent' => '#1E8FA6',
        'icon' => 'terminal',
        'classification' => 'API Gateway',
        'desc' => 'OpenAPI 3.1 specifications, partner sandbox keys, telemetry webhooks, and developer documentation.'
    ],
    [
        'id' => '11',
        'code' => 'ADM',
        'dir' => 'Admin & Governance Portal',
        'name' => 'Administration & Governance',
        'domain' => 'admin.vostokpribor.local',
        'accent' => '#B23A32',
        'icon' => 'security',
        'classification' => 'DEFCON-4 / Root',
        'desc' => 'Access control matrices, audit trails, emergency lockdown triggers, and enterprise governance compliance.'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOSTOKPRIBOR Ecosystem · 11 Enterprise Systems Launchpad</title>
    <meta name="description" content="Central Launchpad for the 11 VOSTOKPRIBOR Frontend Systems.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            navy: '#0B1520',
                            panel: '#111D2B',
                            card: '#162536',
                            border: '#24374E',
                            accent: '#0E7C86',
                            gold: '#E8A33D'
                        }
                    },
                    fontFamily: {
                        sans: ['IBM Plex Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0B1520;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(14, 124, 134, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(178, 58, 50, 0.06) 0%, transparent 40%),
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 32px 32px, 32px 32px;
        }
        .system-card {
            transition: all 0.25s ease;
        }
        .system-card:hover {
            transform: translateY(-3px);
            border-color: rgba(14, 124, 134, 0.6);
            box-shadow: 0 12px 24px -10px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body class="text-slate-200 font-sans min-h-screen flex flex-col antialiased">

    <!-- Top Command Header -->
    <header class="border-b border-brand-border/60 bg-brand-panel/90 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img alt="VOSTOKPRIBOR Logo" class="h-8 w-auto object-contain"
                    src="https://lh3.googleusercontent.com/aida/AEtjO1XMGDZ9meLzj0XkVJ6C4Xv2AoEzKtMyFqF7KQ8eMADmbywzsRZ7VF4Em6pQ7fZ8QRJExCZedCKEUUo1fN1LpEmGsQva25blyUsGhOPvX2vv2cHGkppzOp9iT33Xy2n4Nkr5e_YY_0J78vA8Q7vKUpViCouPJo13HFVvW5olf7QEFzU3EX-WCqc0SYyPXZb7E11PafOMC_KprpG6Tre6bN_DyZS-CI-J5qiCgBtikqVNYGueATNgWbi73Q" />
                <div class="h-6 w-px bg-brand-border"></div>
                <div>
                    <h1 class="font-bold tracking-wide text-white text-base leading-none">VOSTOKPRIBOR</h1>
                    <span class="text-[10px] font-mono text-slate-400">11-SYSTEM INTEGRATED ECOSYSTEM · v2.4</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden md:flex items-center gap-2 px-3 py-1 bg-brand-card rounded border border-brand-border text-xs font-mono">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-slate-300">11/11 Systems Ready · Instant 1-Click Login Enabled</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Title & Instructions -->
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 rounded bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[11px] font-mono font-semibold uppercase">
                    View-Inspection Mode
                </span>
                <span class="text-xs text-slate-400 font-mono">No credentials needed · 1-Click Login</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Enterprise Systems Directory</h2>
            <p class="text-slate-400 text-sm mt-1 max-w-3xl">
                Select any system below. Each system opens directly to its dedicated login view. Simply click the login button on any page to immediately authenticate and preview all dashboard views.
            </p>
        </div>

        <!-- System Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php foreach ($systems as $sys): ?>
            <div class="system-card bg-brand-card rounded-lg border border-brand-border flex flex-col justify-between overflow-hidden">
                <!-- Card Header Accent -->
                <div class="h-1 w-full" style="background-color: <?= $sys['accent'] ?>;"></div>
                
                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded flex items-center justify-center text-white shrink-0" style="background-color: <?= $sys['accent'] ?>20; border: 1px solid <?= $sys['accent'] ?>40;">
                                <span class="material-symbols-outlined text-lg" style="color: <?= $sys['accent'] ?>;"><?= $sys['icon'] ?></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-mono text-slate-400 font-medium">SYSTEM <?= $sys['id'] ?> // <?= $sys['code'] ?></span>
                                <h3 class="font-bold text-white text-base leading-snug"><?= $sys['name'] ?></h3>
                            </div>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-brand-panel border border-brand-border text-slate-300 shrink-0">
                            <?= $sys['classification'] ?>
                        </span>
                    </div>

                    <p class="text-xs text-slate-400 leading-relaxed mb-4 flex-1">
                        <?= $sys['desc'] ?>
                    </p>

                    <div class="text-[11px] font-mono text-slate-400 bg-brand-panel/60 px-3 py-1.5 rounded border border-brand-border/40 mb-4 flex items-center justify-between">
                        <span class="text-slate-400">Route:</span>
                        <span class="text-slate-200 truncate ml-2"><?= htmlspecialchars($sys['dir']) ?>/</span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 pt-2 border-t border-brand-border/40">
                        <a href="<?= rawurlencode($sys['dir']) ?>/login.php" 
                           class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded text-xs font-semibold text-white transition-colors"
                           style="background-color: <?= $sys['accent'] ?>; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                            <span class="material-symbols-outlined text-sm">login</span>
                            <span>Open Login</span>
                        </a>
                        <a href="<?= rawurlencode($sys['dir']) ?>/" 
                           class="inline-flex items-center justify-center gap-1 px-3 py-2 rounded text-xs font-mono text-slate-300 bg-brand-panel hover:bg-brand-border border border-brand-border transition-colors"
                           title="Launch system root entry (redirects to login)">
                            <span class="material-symbols-outlined text-sm">open_in_new</span>
                            <span>Run</span>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-brand-border/40 bg-brand-panel py-6 text-center text-xs text-slate-400 font-mono">
        <p>&copy; 2026 VOSTOKPRIBOR Industrial Group · All 11 Projects Unified Architecture</p>
    </footer>

</body>
</html>
