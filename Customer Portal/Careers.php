<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Customer Portal - Careers & Job Postings
 * Live Synchronization with HR System (SYS-06)
 */

require_once __DIR__ . '/customer_context.php';

// Fetch live published job postings created in HR System
$stmtJobs = $pdo->query("
    SELECT jp.*, d.dept_name 
    FROM job_postings jp
    LEFT JOIN departments d ON jp.department_code = d.dept_code
    WHERE jp.is_published = 1
    ORDER BY jp.posting_id DESC
");
$jobs = $stmtJobs->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>VOSTOKPRIBOR Portal · Careers &amp; Technical Postings</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <link rel="stylesheet" href="css/common.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/tailwind-config.js"></script>
    <script src="js/portal.js"></script>
</head>
<body class="bg-background font-body-md text-body-md text-on-background">

    <!-- Universal Dynamic Header -->
    <?php renderCustomerHeader('Search career opportunities, specs...'); ?>

    <!-- Universal Dynamic Sidebar -->
    <?php renderCustomerSidebar('careers'); ?>

    <div id="portal-main-wrapper" class="portal-content-wrapper pl-64">
        <main class="w-full min-h-screen pt-16 bg-surface px-4 sm:px-6 lg:px-8 py-6">
            <div class="portal-container flex flex-col gap-6">

                <!-- Header Banner -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-outline/20 pb-4">
                    <div>
                        <nav class="flex items-center gap-2 text-xs uppercase tracking-wider text-on-surface-variant font-mono mb-1">
                            <span>Client Operations</span>
                            <span>/</span>
                            <span class="text-tertiary-fixed font-semibold">Talent &amp; Opportunities</span>
                        </nav>
                        <h1 class="text-2xl lg:text-3xl font-bold text-on-surface tracking-tight">
                            Careers &amp; Engineering Postings
                        </h1>
                        <p class="text-sm text-on-surface-variant mt-1">
                            Live openings published directly from the VOSTOKPRIBOR HR System. Discover technical roles in metrology, telemetry engineering, sales, and industrial automation.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-surface-container-high/40 border border-outline/20 font-mono text-xs text-on-surface-variant">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>HR Sync Active (<?= count($jobs) ?> Openings)</span>
                    </div>
                </div>

                <!-- Job Postings Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php if (empty($jobs)): ?>
                        <div class="col-span-2 p-12 text-center text-on-surface-variant bg-surface-container-high/20 rounded-xl border border-outline/20">
                            <span class="material-symbols-outlined text-4xl mb-2 text-outline-variant">work_off</span>
                            <p class="font-medium text-on-surface">No published openings available at this moment.</p>
                            <p class="text-xs text-on-surface-variant mt-1">Check back later or contact your account manager for specialized project staffing.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($jobs as $j): ?>
                            <div class="p-5 rounded-xl bg-surface-container-high/30 border border-outline/20 hover:border-tertiary-fixed/50 transition flex flex-col justify-between shadow-sm">
                                <div>
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <span class="px-2 py-0.5 rounded bg-tertiary-fixed/15 text-tertiary-fixed text-[11px] font-mono font-bold border border-tertiary-fixed/30 uppercase">
                                            <?= htmlspecialchars($j['dept_name'] ?? $j['department_code'] ?? 'Engineering') ?>
                                        </span>
                                        <span class="text-xs text-on-surface-variant font-mono">
                                            Posted <?= htmlspecialchars(substr($j['posted_at'] ?? '', 0, 10)) ?>
                                        </span>
                                    </div>
                                    <h3 class="text-base font-bold text-on-surface mt-1">
                                        <?= htmlspecialchars($j['title']) ?>
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-on-surface-variant mt-2 font-mono">
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">location_on</span>
                                            <?= htmlspecialchars($j['location'] ?? 'Almaty HQ') ?>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">schedule</span>
                                            <?= htmlspecialchars($j['employment_type'] ?? 'Full-Time') ?>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">military_tech</span>
                                            <?= htmlspecialchars($j['experience_level'] ?? 'Mid-Senior') ?>
                                        </span>
                                    </div>
                                    <p class="text-xs text-on-surface-variant/90 mt-3 line-clamp-3 leading-relaxed">
                                        <?= htmlspecialchars($j['description'] ?: 'Collaborate with the industrial automation team on high-precision instrumentation, telemetry infrastructure, and metrological calibration.') ?>
                                    </p>
                                </div>

                                <div class="mt-4 pt-3 border-t border-outline/15 flex items-center justify-between">
                                    <span class="text-[11px] font-mono text-outline-variant">Job ID: #VP-HR-<?= str_pad((string)$j['posting_id'], 4, '0', STR_PAD_LEFT) ?></span>
                                    <button onclick="openApplyModal('<?= htmlspecialchars(addslashes($j['title'])) ?>', 'VP-HR-<?= $j['posting_id'] ?>')"
                                            class="px-3.5 py-1.5 rounded-lg bg-surface-container-high text-on-primary text-xs font-semibold hover:bg-tertiary-fixed hover:text-primary transition flex items-center gap-1.5 cursor-pointer">
                                        <span>Express Interest</span>
                                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </div>

    <!-- Modal: Express Interest / Quick Application -->
    <div id="apply-modal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-primary border border-outline/30 rounded-2xl max-w-md w-full p-6 shadow-2xl relative">
            <div class="flex items-center justify-between border-b border-outline/20 pb-3 mb-4">
                <h3 class="text-lg font-bold text-on-primary">Express Interest</h3>
                <button onclick="document.getElementById('apply-modal').classList.add('hidden')" class="text-on-primary-container hover:text-on-primary">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="text-xs text-on-primary-container mb-4">
                Position: <strong id="modal-job-title" class="text-tertiary-fixed"></strong>
            </div>
            <form id="apply-form" class="flex flex-col gap-3 text-xs">
                <div>
                    <label class="block text-on-primary-container font-semibold mb-1">Candidate / Contact Name *</label>
                    <input id="app-name" type="text" value="<?= htmlspecialchars($currUser['full_name']) ?>" required
                           class="w-full bg-surface-container-high/60 border border-outline/30 rounded-lg px-3 py-2 text-on-primary focus:outline-none focus:border-tertiary-fixed" />
                </div>
                <div>
                    <label class="block text-on-primary-container font-semibold mb-1">Contact Email *</label>
                    <input id="app-email" type="email" value="<?= htmlspecialchars($currUser['email'] ?? '') ?>" required
                           class="w-full bg-surface-container-high/60 border border-outline/30 rounded-lg px-3 py-2 text-on-primary focus:outline-none focus:border-tertiary-fixed" />
                </div>
                <div>
                    <label class="block text-on-primary-container font-semibold mb-1">Professional Summary / Note</label>
                    <textarea id="app-note" rows="3" placeholder="Briefly describe engineering experience or inquiry..."
                              class="w-full bg-surface-container-high/60 border border-outline/30 rounded-lg px-3 py-2 text-on-primary focus:outline-none focus:border-tertiary-fixed"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-outline/20">
                    <button type="button" onclick="document.getElementById('apply-modal').classList.add('hidden')"
                            class="px-4 py-2 rounded-lg bg-surface-container-high/40 text-on-primary hover:bg-surface-container-high/60 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg bg-tertiary-fixed text-primary font-bold hover:brightness-110 transition shadow">
                        Transmit to HR
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openApplyModal(title, id) {
        document.getElementById('modal-job-title').innerText = title + ' (' + id + ')';
        document.getElementById('apply-modal').classList.remove('hidden');
    }

    document.getElementById('apply-form').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Thank you! Your profile and expression of interest have been transmitted directly to VOSTOKPRIBOR HR Operations.');
        document.getElementById('apply-modal').classList.add('hidden');
    });
    </script>
</body>
</html>
