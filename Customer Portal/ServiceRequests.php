<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Customer Portal - Industrial Service Requests
 * Live Integration with HR System (SYS-06) for Technical Personnel Dispatch
 */

require_once __DIR__ . '/customer_context.php';

// Fetch customer service requests with assigned engineer details
$stmtReqs = $pdo->prepare("
    SELECT 
        csr.*,
        e.full_name AS assigned_engineer_name,
        e.job_title AS assigned_engineer_title,
        e.email AS assigned_engineer_email
    FROM customer_service_requests csr
    LEFT JOIN employees e ON csr.assigned_emp_id = e.emp_id
    WHERE csr.cus_id = ?
    ORDER BY csr.created_at DESC
");
$stmtReqs->execute([$cusId]);
$serviceRequests = $stmtReqs->fetchAll(PDO::FETCH_ASSOC);

$totalRequests = count($serviceRequests);
$assignedCount = 0;
$pendingCount = 0;
foreach ($serviceRequests as $sr) {
    if ($sr['status'] === 'Personnel Assigned' || $sr['status'] === 'In Progress') $assignedCount++;
    if ($sr['status'] === 'Submitted' || $sr['status'] === 'Under Review') $pendingCount++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>VOSTOKPRIBOR Portal · Request Industrial Services</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <link rel="stylesheet" href="css/common.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/tailwind-config.js"></script>
    <script src="js/portal.js"></script>
    <style>
        .badge-status { padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-assigned { background: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.3); }
        .badge-review { background: rgba(245,158,11,0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); }
        .badge-submitted { background: rgba(56,189,248,0.15); color: #38bdf8; border: 1px solid rgba(56,189,248,0.3); }
    </style>
</head>
<body class="bg-background font-body-md text-body-md text-on-background">

    <!-- Universal Dynamic Header -->
    <?php renderCustomerHeader('Search technical service requests...'); ?>

    <!-- Universal Dynamic Sidebar -->
    <?php renderCustomerSidebar('services'); ?>

    <div id="portal-main-wrapper" class="portal-content-wrapper pl-64">
        <main class="w-full min-h-screen pt-16 bg-surface px-4 sm:px-6 lg:px-8 py-6">
            <div class="portal-container flex flex-col gap-6">

                <!-- Header Banner -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-outline/20 pb-4">
                    <div>
                        <nav class="flex items-center gap-2 text-xs uppercase tracking-wider text-on-surface-variant font-mono mb-1">
                            <span>Client Operations</span>
                            <span>/</span>
                            <span class="text-tertiary-fixed font-semibold">Technical Services</span>
                        </nav>
                        <h1 class="text-2xl lg:text-3xl font-bold text-on-surface tracking-tight">
                            Request Industrial &amp; Engineering Services
                        </h1>
                        <p class="text-sm text-on-surface-variant mt-1">
                            Commission certified VOSTOKPRIBOR specialists for calibration, telemetry integration, SCADA inspection, and maintenance. Requests are directly recorded in the HR operations dispatch queue.
                        </p>
                    </div>
                    <button onclick="document.getElementById('service-request-modal').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-tertiary-fixed text-primary font-bold text-sm hover:brightness-110 transition shadow-lg shrink-0 cursor-pointer">
                        <span class="material-symbols-outlined text-lg">add_circle</span>
                        <span>New Service Request</span>
                    </button>
                </div>

                <!-- KPI Overview Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl bg-surface-container-high/40 border border-outline/20 flex items-center justify-between">
                        <div>
                            <div class="text-xs uppercase font-mono text-on-surface-variant tracking-wider">Total Requests</div>
                            <div class="text-2xl font-bold text-on-surface mt-1"><?= $totalRequests ?></div>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-tertiary-fixed/20 text-tertiary-fixed flex items-center justify-center font-bold">
                            <span class="material-symbols-outlined">build</span>
                        </div>
                    </div>
                    <div class="p-4 rounded-xl bg-surface-container-high/40 border border-outline/20 flex items-center justify-between">
                        <div>
                            <div class="text-xs uppercase font-mono text-on-surface-variant tracking-wider">Personnel Assigned</div>
                            <div class="text-2xl font-bold text-emerald-400 mt-1"><?= $assignedCount ?></div>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                            <span class="material-symbols-outlined">engineering</span>
                        </div>
                    </div>
                    <div class="p-4 rounded-xl bg-surface-container-high/40 border border-outline/20 flex items-center justify-between">
                        <div>
                            <div class="text-xs uppercase font-mono text-on-surface-variant tracking-wider">Under HR Review</div>
                            <div class="text-2xl font-bold text-amber-400 mt-1"><?= $pendingCount ?></div>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold">
                            <span class="material-symbols-outlined">pending_actions</span>
                        </div>
                    </div>
                </div>

                <!-- Service Requests Table -->
                <div class="bg-surface-container-high/20 border border-outline/20 rounded-xl overflow-hidden shadow-sm">
                    <div class="p-4 border-b border-outline/20 flex items-center justify-between bg-surface-container-high/40">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-tertiary-fixed">assignment</span>
                            <h2 class="font-bold text-on-surface text-base">Active &amp; Historical Service Dispatches</h2>
                        </div>
                        <div class="text-xs font-mono text-on-surface-variant">HR Synchronized (SYS-03 ↔ SYS-06)</div>
                    </div>

                    <?php if (empty($serviceRequests)): ?>
                        <div class="p-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-5xl text-outline-variant mb-2">build_circle</span>
                            <p class="font-medium text-on-surface">No service requests submitted yet.</p>
                            <p class="text-xs text-on-surface-variant mt-1">Submit your first industrial service request to dispatch qualified specialists to your facility.</p>
                            <button onclick="document.getElementById('service-request-modal').classList.remove('hidden')"
                                    class="mt-4 px-4 py-2 bg-tertiary-fixed text-primary text-xs font-bold rounded-lg hover:brightness-110 transition">
                                Request Engineering Service
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm border-collapse">
                                <thead>
                                    <tr class="border-b border-outline/20 bg-surface-container-high/30 text-xs font-mono uppercase text-on-surface-variant">
                                        <th class="py-3 px-4">Request Ref</th>
                                        <th class="py-3 px-4">Service Scope</th>
                                        <th class="py-3 px-4">Site Location</th>
                                        <th class="py-3 px-4">Target Date</th>
                                        <th class="py-3 px-4">Priority</th>
                                        <th class="py-3 px-4">Assigned Specialist (HR)</th>
                                        <th class="py-3 px-4 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline/10 font-body-sm">
                                    <?php foreach ($serviceRequests as $req): ?>
                                        <tr class="hover:bg-surface-container-high/30 transition-colors">
                                            <td class="py-3.5 px-4 font-mono font-bold text-tertiary-fixed">
                                                <?= htmlspecialchars($req['request_id']) ?>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-semibold text-on-surface"><?= htmlspecialchars($req['title']) ?></div>
                                                <div class="text-xs text-on-surface-variant mt-0.5"><?= htmlspecialchars($req['service_type']) ?></div>
                                            </td>
                                            <td class="py-3.5 px-4 text-on-surface-variant text-xs">
                                                <?= htmlspecialchars($req['facility_location'] ?? 'Customer Facility') ?>
                                            </td>
                                            <td class="py-3.5 px-4 font-mono text-xs text-on-surface-variant">
                                                <?= htmlspecialchars(substr($req['requested_date'] ?? '', 0, 10)) ?>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <?php 
                                                    $pColor = match($req['priority']) {
                                                        'Critical' => 'text-rose-400 bg-rose-500/10 border-rose-500/30',
                                                        'High'     => 'text-amber-400 bg-amber-500/10 border-amber-500/30',
                                                        default    => 'text-slate-300 bg-slate-500/10 border-slate-500/30'
                                                    };
                                                ?>
                                                <span class="px-2 py-0.5 text-[11px] font-mono font-bold rounded border <?= $pColor ?>">
                                                    <?= htmlspecialchars($req['priority']) ?>
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <?php if (!empty($req['assigned_engineer_name'])): ?>
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-[10px]">
                                                            <?= strtoupper(substr($req['assigned_engineer_name'], 0, 2)) ?>
                                                        </div>
                                                        <div>
                                                            <div class="font-medium text-emerald-300 text-xs"><?= htmlspecialchars($req['assigned_engineer_name']) ?></div>
                                                            <div class="text-[10px] text-on-surface-variant font-mono"><?= htmlspecialchars($req['assigned_engineer_title'] ?? 'Specialist') ?></div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-xs text-on-surface-variant/70 italic flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[14px]">hourglass_empty</span>
                                                        Pending HR assignment
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3.5 px-4 text-right">
                                                <?php 
                                                    $sClass = match($req['status']) {
                                                        'Personnel Assigned', 'Completed' => 'badge-assigned',
                                                        'Under Review', 'In Progress'     => 'badge-review',
                                                        default                            => 'badge-submitted'
                                                    };
                                                ?>
                                                <span class="badge-status <?= $sClass ?>">
                                                    <?= htmlspecialchars($req['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </div>

    <!-- Modal: Submit Service Request -->
    <div id="service-request-modal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-primary border border-outline/30 rounded-2xl max-w-lg w-full p-6 shadow-2xl relative">
            <div class="flex items-center justify-between border-b border-outline/20 pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-tertiary-fixed">engineering</span>
                    <h3 class="text-lg font-bold text-on-primary">Request Industrial Engineering Service</h3>
                </div>
                <button onclick="document.getElementById('service-request-modal').classList.add('hidden')" class="text-on-primary-container hover:text-on-primary">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="service-request-form" class="flex flex-col gap-4 text-xs font-body-default">
                <div>
                    <label class="block text-on-primary-container font-semibold mb-1">Service Type *</label>
                    <select id="sr-type" class="w-full bg-surface-container-high/60 border border-outline/30 rounded-lg px-3 py-2 text-on-primary focus:outline-none focus:border-tertiary-fixed">
                        <option value="Calibration & Metrology">Calibration &amp; Metrology (Pressure, Flow, Temp)</option>
                        <option value="Telemetry & SCADA Integration">Telemetry &amp; SCADA Gateway Commissioning</option>
                        <option value="On-Site Engineering Inspection">On-Site Field Engineering &amp; FAT Inspection</option>
                        <option value="Custom Hardware Solution">Custom Sensor Hardware &amp; Transducer Adaptation</option>
                        <option value="Preventative Maintenance">Preventative Diagnostic &amp; Scheduled Maintenance</option>
                    </select>
                </div>

                <div>
                    <label class="block text-on-primary-container font-semibold mb-1">Title / Brief Objective *</label>
                    <input id="sr-title" type="text" placeholder="e.g. Recalibration of Unit 3 Flow Transmitters" required
                           class="w-full bg-surface-container-high/60 border border-outline/30 rounded-lg px-3 py-2 text-on-primary focus:outline-none focus:border-tertiary-fixed" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-on-primary-container font-semibold mb-1">Priority</label>
                        <select id="sr-priority" class="w-full bg-surface-container-high/60 border border-outline/30 rounded-lg px-3 py-2 text-on-primary focus:outline-none focus:border-tertiary-fixed">
                            <option value="Standard">Standard (7-14 Days)</option>
                            <option value="High">High (3-5 Days)</option>
                            <option value="Critical">Critical (Within 48h)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-on-primary-container font-semibold mb-1">Preferred Date</label>
                        <input id="sr-date" type="date" value="<?= date('Y-m-d', strtotime('+7 days')) ?>"
                               class="w-full bg-surface-container-high/60 border border-outline/30 rounded-lg px-3 py-2 text-on-primary focus:outline-none focus:border-tertiary-fixed" />
                    </div>
                </div>

                <div>
                    <label class="block text-on-primary-container font-semibold mb-1">Facility / Site Location</label>
                    <input id="sr-location" type="text" placeholder="e.g. Atyrau Refinery, Processing Complex Unit 4"
                           class="w-full bg-surface-container-high/60 border border-outline/30 rounded-lg px-3 py-2 text-on-primary focus:outline-none focus:border-tertiary-fixed" />
                </div>

                <div>
                    <label class="block text-on-primary-container font-semibold mb-1">Scope &amp; Equipment Specifications</label>
                    <textarea id="sr-desc" rows="3" placeholder="Provide equipment model numbers, serial numbers, target parameters, and safety requirements..."
                              class="w-full bg-surface-container-high/60 border border-outline/30 rounded-lg px-3 py-2 text-on-primary focus:outline-none focus:border-tertiary-fixed"></textarea>
                </div>

                <div id="sr-alert" class="hidden p-2.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/40 text-xs"></div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-outline/20">
                    <button type="button" onclick="document.getElementById('service-request-modal').classList.add('hidden')"
                            class="px-4 py-2 rounded-lg bg-surface-container-high/40 text-on-primary hover:bg-surface-container-high/60 transition">
                        Cancel
                    </button>
                    <button type="submit" id="sr-submit-btn"
                            class="px-5 py-2 rounded-lg bg-tertiary-fixed text-primary font-bold hover:brightness-110 transition shadow">
                        Submit &amp; Route to HR
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.getElementById('service-request-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('sr-submit-btn');
        const alertBox = document.getElementById('sr-alert');
        alertBox.classList.add('hidden');
        submitBtn.disabled = true;
        submitBtn.innerText = 'Submitting...';

        const payload = {
            service_type: document.getElementById('sr-type').value,
            title: document.getElementById('sr-title').value,
            priority: document.getElementById('sr-priority').value,
            requested_date: document.getElementById('sr-date').value,
            facility_location: document.getElementById('sr-location').value,
            description: document.getElementById('sr-desc').value
        };

        try {
            const res = await fetch('./api/services.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                alert('Service Request ' + data.request_id + ' successfully submitted and queued in HR operations!');
                window.location.reload();
            } else {
                alertBox.innerText = data.message || 'Submission failed.';
                alertBox.classList.remove('hidden');
            }
        } catch (err) {
            alertBox.innerText = 'Network connection error: ' + err.message;
            alertBox.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Submit & Route to HR';
        }
    });
    </script>
</body>
</html>
