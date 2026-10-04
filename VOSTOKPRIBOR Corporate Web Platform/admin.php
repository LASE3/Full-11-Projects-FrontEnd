<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Corporate Web Platform — Content Administration Portal
 * Minimal editor for News / Announcements, Products, and Job Postings.
 */

require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../config/db.php';

$currUser = requireAuth('WEB', 'login.php');
if (!isSuperAdmin($currUser) && (($currUser['clearance_level'] ?? 'L1') < 'L3')) {
    http_response_code(403);
    echo "<h1>403 Forbidden</h1><p>You need clearance L3+ or SuperAdmin privileges to access the Web Platform Content Admin.</p>";
    exit;
}

$csrfToken = getCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>" />
    <title>WEB Admin Editor • VOSTOKPRIBOR Corporate Platform</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="css/theme.css" />
    <link rel="stylesheet" href="css/corporate.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/api-core.js"></script>
    <style>
        body { font-family: 'IBM Plex Sans', sans-serif; background: #0c1524; color: #e2e8f0; }
        .glass-panel { background: rgba(18, 30, 49, 0.75); border: 1px solid rgba(255, 255, 255, 0.08); backdrop-filter: blur(12px); border-radius: 8px; }
        .tab-btn.active { background: #1e3a5f; color: #60a5fa; border-color: #3b82f6; }
    </style>
</head>
<body class="min-h-screen">
    <!-- Header -->
    <header class="bg-[#0f1d30] border-b border-white/10 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-xl font-bold tracking-wide text-white">VOSTOKPRIBOR</span>
            <span class="text-xs font-mono px-2 py-0.5 rounded bg-blue-900/60 text-blue-300 border border-blue-700">WEB-ADMIN</span>
            <span class="text-xs text-slate-400">Content Management Editor</span>
        </div>
        <div class="flex items-center gap-4 text-xs font-mono text-slate-300">
            <span>User: <strong class="text-blue-400"><?= htmlspecialchars($currUser['full_name'] ?? 'SuperAdmin') ?></strong></span>
            <span>Clearance: <strong class="text-emerald-400"><?= htmlspecialchars($currUser['clearance_level'] ?? 'L4') ?></strong></span>
            <a href="index.php" class="px-3 py-1.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-200">Public Portal &rarr;</a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <!-- Tab Navigation -->
        <div class="flex gap-2 border-b border-white/10 pb-4 mb-6">
            <button onclick="switchTab('announcements')" id="tab-announcements" class="tab-btn active px-4 py-2 rounded text-sm font-semibold border border-transparent transition">
                News & Announcements
            </button>
            <button onclick="switchTab('products')" id="tab-products" class="tab-btn px-4 py-2 rounded text-sm font-semibold border border-transparent transition">
                Product Showcase
            </button>
            <button onclick="switchTab('jobs')" id="tab-jobs" class="tab-btn px-4 py-2 rounded text-sm font-semibold border border-transparent transition">
                Job Postings & Careers
            </button>
        </div>

        <!-- Section: Announcements -->
        <section id="sec-announcements" class="glass-panel p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-bold text-white">Corporate Announcements & News</h2>
                    <p class="text-xs text-slate-400">Broadcast news and bulletins across public web channels</p>
                </div>
                <button onclick="openAnnModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded text-xs font-semibold text-white flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm">add</span> New Announcement
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-mono">
                    <thead class="bg-black/30 text-slate-400 uppercase">
                        <tr>
                            <th class="p-3">ID</th>
                            <th class="p-3">Title</th>
                            <th class="p-3">Audience</th>
                            <th class="p-3">Posted By</th>
                            <th class="p-3">Date</th>
                            <th class="p-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ann-table-body" class="divide-y divide-white/5">
                        <tr><td colspan="6" class="p-4 text-center text-slate-500">Loading announcements...</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Section: Products -->
        <section id="sec-products" class="glass-panel p-6 mb-8 hidden">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-bold text-white">Web Product Showcase</h2>
                    <p class="text-xs text-slate-400">Catalog items displayed on public product exploration pages</p>
                </div>
                <button onclick="openProdModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded text-xs font-semibold text-white flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm">add</span> New Product
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-mono">
                    <thead class="bg-black/30 text-slate-400 uppercase">
                        <tr>
                            <th class="p-3">SKU / ID</th>
                            <th class="p-3">Product Name</th>
                            <th class="p-3">Category</th>
                            <th class="p-3">Price (€)</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="prod-table-body" class="divide-y divide-white/5">
                        <tr><td colspan="6" class="p-4 text-center text-slate-500">Loading products...</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Section: Jobs -->
        <section id="sec-jobs" class="glass-panel p-6 mb-8 hidden">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-bold text-white">Career Opportunities & Job Postings</h2>
                    <p class="text-xs text-slate-400">Publish open positions to the public career portal</p>
                </div>
                <button onclick="openJobModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded text-xs font-semibold text-white flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm">add</span> New Job Posting
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-mono">
                    <thead class="bg-black/30 text-slate-400 uppercase">
                        <tr>
                            <th class="p-3">ID</th>
                            <th class="p-3">Position Title</th>
                            <th class="p-3">Department</th>
                            <th class="p-3">Published</th>
                            <th class="p-3">Date</th>
                            <th class="p-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="job-table-body" class="divide-y divide-white/5">
                        <tr><td colspan="6" class="p-4 text-center text-slate-500">Loading job postings...</td></tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Modals (Simple JavaScript-driven) -->
    <div id="modal-container" class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 hidden z-50">
        <div class="glass-panel bg-[#15233b] max-w-lg w-full p-6 shadow-2xl">
            <h3 id="modal-title" class="text-base font-bold text-white mb-4">Edit Item</h3>
            <div id="modal-body" class="space-y-4"></div>
            <div class="flex justify-end gap-3 mt-6">
                <button onclick="closeModal()" class="px-4 py-2 rounded bg-slate-700 hover:bg-slate-600 text-xs font-semibold text-white">Cancel</button>
                <button id="modal-save-btn" class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-500 text-xs font-semibold text-white">Save Changes</button>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function switchTab(name) {
            ['announcements', 'products', 'jobs'].forEach(t => {
                document.getElementById('tab-' + t).classList.toggle('active', t === name);
                document.getElementById('sec-' + t).classList.toggle('hidden', t !== name);
            });
            if (name === 'announcements') loadAnnouncements();
            if (name === 'products') loadProducts();
            if (name === 'jobs') loadJobs();
        }

        async function loadAnnouncements() {
            const res = await fetch('api/admin_web.php?section=announcements&action=list');
            const data = await res.json();
            const tb = document.getElementById('ann-table-body');
            if (!data.success || !data.data.length) {
                tb.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-slate-500">No announcements found.</td></tr>';
                return;
            }
            tb.innerHTML = data.data.map(a => `
                <tr class="hover:bg-white/5">
                    <td class="p-3 text-slate-400">#${a.announcement_id}</td>
                    <td class="p-3 font-semibold text-white">${escapeHtml(a.title)}</td>
                    <td class="p-3"><span class="px-2 py-0.5 rounded bg-blue-950 text-blue-300 border border-blue-800 text-[10px]">${a.audience_dept}</span></td>
                    <td class="p-3 text-slate-400">${a.posted_by_emp_id}</td>
                    <td class="p-3 text-slate-400">${a.posted_at ? a.posted_at.substring(0, 10) : ''}</td>
                    <td class="p-3 text-right">
                        <button onclick="deleteAnn(${a.announcement_id})" class="px-2 py-1 rounded bg-red-900/50 hover:bg-red-800 text-red-200 text-[11px]">Delete</button>
                    </td>
                </tr>
            `).join('');
        }

        async function loadProducts() {
            const res = await fetch('api/admin_web.php?section=products&action=list');
            const data = await res.json();
            const tb = document.getElementById('prod-table-body');
            if (!data.success || !data.data.length) {
                tb.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-slate-500">No products found.</td></tr>';
                return;
            }
            tb.innerHTML = data.data.map(p => `
                <tr class="hover:bg-white/5">
                    <td class="p-3 text-blue-400">${p.prod_id}</td>
                    <td class="p-3 font-semibold text-white">${escapeHtml(p.product_name)}</td>
                    <td class="p-3 text-slate-300">${p.category}</td>
                    <td class="p-3 text-emerald-400">€${Number(p.price).toFixed(2)}</td>
                    <td class="p-3">${p.is_active == 1 ? '<span class="text-emerald-400 font-bold">Active</span>' : '<span class="text-rose-400">Disabled</span>'}</td>
                    <td class="p-3 text-right">
                        <button onclick="deleteProd('${p.prod_id}')" class="px-2 py-1 rounded bg-red-900/50 hover:bg-red-800 text-red-200 text-[11px]">Delete</button>
                    </td>
                </tr>
            `).join('');
        }

        async function loadJobs() {
            const res = await fetch('api/admin_web.php?section=jobs&action=list');
            const data = await res.json();
            const tb = document.getElementById('job-table-body');
            if (!data.success || !data.data.length) {
                tb.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-slate-500">No job postings found.</td></tr>';
                return;
            }
            tb.innerHTML = data.data.map(j => `
                <tr class="hover:bg-white/5">
                    <td class="p-3 text-slate-400">#${j.posting_id}</td>
                    <td class="p-3 font-semibold text-white">${escapeHtml(j.title)}</td>
                    <td class="p-3"><span class="px-2 py-0.5 rounded bg-purple-950 text-purple-300 border border-purple-800 text-[10px]">${j.department_code}</span></td>
                    <td class="p-3">${j.is_published == 1 ? '<span class="text-emerald-400 font-bold">Published</span>' : '<span class="text-slate-500">Draft</span>'}</td>
                    <td class="p-3 text-slate-400">${j.posted_at ? j.posted_at.substring(0, 10) : ''}</td>
                    <td class="p-3 text-right">
                        <button onclick="deleteJob(${j.posting_id})" class="px-2 py-1 rounded bg-red-900/50 hover:bg-red-800 text-red-200 text-[11px]">Delete</button>
                    </td>
                </tr>
            `).join('');
        }

        function openAnnModal() {
            document.getElementById('modal-title').textContent = 'Create New Announcement';
            document.getElementById('modal-body').innerHTML = `
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Title</label>
                    <input id="modal-ann-title" type="text" class="w-full px-3 py-2 rounded bg-black/40 border border-white/10 text-white text-xs font-sans" placeholder="Announcement headline" />
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Audience Department</label>
                    <select id="modal-ann-dept" class="w-full px-3 py-2 rounded bg-black/40 border border-white/10 text-white text-xs font-sans">
                        <option value="ALL">ALL (Public Web & Intranet)</option>
                        <option value="OPS">Operations (OPS)</option>
                        <option value="ENG">Engineering (ENG)</option>
                        <option value="SAL">Sales (SAL)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Content / Message</label>
                    <textarea id="modal-ann-body" rows="4" class="w-full px-3 py-2 rounded bg-black/40 border border-white/10 text-white text-xs font-sans" placeholder="Detailed content text"></textarea>
                </div>
            `;
            document.getElementById('modal-save-btn').onclick = async () => {
                const title = document.getElementById('modal-ann-title').value.trim();
                const content = document.getElementById('modal-ann-body').value.trim();
                const dept = document.getElementById('modal-ann-dept').value;
                if (!title || !content) return alert('Title and content are required.');
                const res = await fetch('api/admin_web.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
                    body: JSON.stringify({ section: 'announcements', action: 'create', title, body: content, audience_dept: dept })
                });
                const d = await res.json();
                if (d.success) { closeModal(); loadAnnouncements(); } else alert(d.error || 'Failed to save.');
            };
            document.getElementById('modal-container').classList.remove('hidden');
        }

        function openProdModal() {
            document.getElementById('modal-title').textContent = 'Create New Product';
            document.getElementById('modal-body').innerHTML = `
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Product Name</label>
                    <input id="modal-prod-name" type="text" class="w-full px-3 py-2 rounded bg-black/40 border border-white/10 text-white text-xs font-sans" placeholder="e.g. Optical Sensor Module" />
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Category</label>
                    <input id="modal-prod-cat" type="text" class="w-full px-3 py-2 rounded bg-black/40 border border-white/10 text-white text-xs font-sans" value="Industrial Sensors" />
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Price (€)</label>
                    <input id="modal-prod-price" type="number" step="0.01" class="w-full px-3 py-2 rounded bg-black/40 border border-white/10 text-white text-xs font-sans" value="1250.00" />
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Description</label>
                    <textarea id="modal-prod-desc" rows="3" class="w-full px-3 py-2 rounded bg-black/40 border border-white/10 text-white text-xs font-sans" placeholder="Product specifications"></textarea>
                </div>
            `;
            document.getElementById('modal-save-btn').onclick = async () => {
                const name = document.getElementById('modal-prod-name').value.trim();
                const cat = document.getElementById('modal-prod-cat').value.trim();
                const price = parseFloat(document.getElementById('modal-prod-price').value);
                const desc = document.getElementById('modal-prod-desc').value.trim();
                if (!name || isNaN(price)) return alert('Name and valid price required.');
                const res = await fetch('api/admin_web.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
                    body: JSON.stringify({ section: 'products', action: 'create', product_name: name, category: cat, price, description: desc })
                });
                const d = await res.json();
                if (d.success) { closeModal(); loadProducts(); } else alert(d.error || 'Failed to save.');
            };
            document.getElementById('modal-container').classList.remove('hidden');
        }

        function openJobModal() {
            document.getElementById('modal-title').textContent = 'Create New Job Posting';
            document.getElementById('modal-body').innerHTML = `
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Position Title</label>
                    <input id="modal-job-title" type="text" class="w-full px-3 py-2 rounded bg-black/40 border border-white/10 text-white text-xs font-sans" placeholder="e.g. Senior Firmware Engineer" />
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Department</label>
                    <select id="modal-job-dept" class="w-full px-3 py-2 rounded bg-black/40 border border-white/10 text-white text-xs font-sans">
                        <option value="ENG">Engineering (ENG)</option>
                        <option value="OPS">Operations (OPS)</option>
                        <option value="IT">IT Infrastructure (IT)</option>
                        <option value="SAL">Sales & Marketing (SAL)</option>
                        <option value="HRA">Human Resources (HRA)</option>
                    </select>
                </div>
            `;
            document.getElementById('modal-save-btn').onclick = async () => {
                const title = document.getElementById('modal-job-title').value.trim();
                const dept = document.getElementById('modal-job-dept').value;
                if (!title) return alert('Title is required.');
                const res = await fetch('api/admin_web.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
                    body: JSON.stringify({ section: 'jobs', action: 'create', title, department_code: dept, is_published: 1 })
                });
                const d = await res.json();
                if (d.success) { closeModal(); loadJobs(); } else alert(d.error || 'Failed to save.');
            };
            document.getElementById('modal-container').classList.remove('hidden');
        }

        async function deleteAnn(id) {
            if (!confirm('Are you sure you want to delete announcement #' + id + '?')) return;
            const res = await fetch('api/admin_web.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
                body: JSON.stringify({ section: 'announcements', action: 'delete', id })
            });
            const d = await res.json();
            if (d.success) loadAnnouncements(); else alert(d.error || 'Delete failed.');
        }

        async function deleteProd(prodId) {
            if (!confirm('Are you sure you want to delete product ' + prodId + '?')) return;
            const res = await fetch('api/admin_web.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
                body: JSON.stringify({ section: 'products', action: 'delete', prod_id: prodId })
            });
            const d = await res.json();
            if (d.success) loadProducts(); else alert(d.error || 'Delete failed.');
        }

        async function deleteJob(id) {
            if (!confirm('Are you sure you want to delete job posting #' + id + '?')) return;
            const res = await fetch('api/admin_web.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
                body: JSON.stringify({ section: 'jobs', action: 'delete', id })
            });
            const d = await res.json();
            if (d.success) loadJobs(); else alert(d.error || 'Delete failed.');
        }

        function closeModal() {
            document.getElementById('modal-container').classList.add('hidden');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }

        // Initialize default tab
        loadAnnouncements();
    </script>
</body>
</html>
