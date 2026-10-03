<?php
declare(strict_types=1);
/**
 * VOSTOKPRIBOR — SuperAdmin Console
 * Location: Admin & Governance Portal/SuperAdminConsole.php
 * Access:   SuperAdmin (role_id=1, clearance L4) only
 *
 * Features:
 *  - Whitelisted table browser (read-only with pagination)
 *  - Audit logs viewer (read-only)
 *  - View-as banner (impersonate a user's perspective; no session swap)
 *  - System integration link status management
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
requireSuperAdmin('ADM');                // Only SuperAdmin (L4) may enter

require_once __DIR__ . '/gov_service.php';
require_once __DIR__ . '/../includes/AuditLogger.php';

$pdo         = getDbConnection();
$currentUser = gov_getActiveUserProfile();

// ─────────────────────────────────────────────────────────────────────────────
// Whitelisted tables (read-only browsing)
// ─────────────────────────────────────────────────────────────────────────────
const BROWSABLE_TABLES = [
    'employees', 'employee_accounts', 'employee_roles', 'departments',
    'customers', 'customer_accounts', 'projects', 'invoices', 'invoice_items',
    'tickets', 'documents', 'products', 'orders', 'order_items',
    'system_integrations', 'system_integration_logs', 'systems_catalog',
    'roles', 'role_system_access', 'developer_api_keys',
    'portal_notifications', 'security_events',
];

// ─────────────────────────────────────────────────────────────────────────────
// View-as mode
// ─────────────────────────────────────────────────────────────────────────────
$viewAsEmpId   = trim($_GET['view_as'] ?? '');
$viewAsProfile = null;
if ($viewAsEmpId) {
    $vaStmt = $pdo->prepare(
        "SELECT e.emp_id, e.full_name, e.job_title, e.clearance_level, r.role_name
         FROM employees e
         LEFT JOIN employee_roles er ON e.emp_id = er.emp_id
         LEFT JOIN roles r ON er.role_id = r.role_id
         WHERE e.emp_id = ? LIMIT 1"
    );
    $vaStmt->execute([$viewAsEmpId]);
    $viewAsProfile = $vaStmt->fetch(PDO::FETCH_ASSOC) ?: null;
    if ($viewAsProfile) {
        AuditLogger::log('ADM', 'SUPERADMIN_VIEW_AS', $viewAsEmpId,
            "Admin {$currentUser['emp_id']} activated view-as for {$viewAsEmpId}");
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Table browser
// ─────────────────────────────────────────────────────────────────────────────
$selectedTable = $_GET['table'] ?? '';
$page          = max(1, (int)($_GET['page'] ?? 1));
$perPage       = 25;
$offset        = ($page - 1) * $perPage;
$tableRows     = [];
$tableTotal    = 0;
$tableColumns  = [];
$tableError    = '';

if ($selectedTable && in_array($selectedTable, BROWSABLE_TABLES, true)) {
    try {
        $tableTotal   = (int)$pdo->query("SELECT COUNT(*) FROM `{$selectedTable}`")->fetchColumn();
        $stmt         = $pdo->query("SELECT * FROM `{$selectedTable}` LIMIT {$perPage} OFFSET {$offset}");
        $tableRows    = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tableColumns = $tableRows ? array_keys($tableRows[0]) : [];
    } catch (Throwable $e) {
        $tableError = $e->getMessage();
    }
} elseif ($selectedTable) {
    $tableError = "Table '{$selectedTable}' is not in the browsable whitelist.";
}

// ─────────────────────────────────────────────────────────────────────────────
// Audit logs viewer
// ─────────────────────────────────────────────────────────────────────────────
$auditFilter = trim($_GET['audit_system'] ?? '');
$auditPage   = max(1, (int)($_GET['apage'] ?? 1));
$auditOffset = ($auditPage - 1) * $perPage;
$auditWhere  = $auditFilter ? "WHERE system_id = " . $pdo->quote($auditFilter) : '';
$auditTotal  = (int)$pdo->query("SELECT COUNT(*) FROM audit_logs {$auditWhere}")->fetchColumn();
$auditRows   = $pdo->query(
    "SELECT log_id, system_id, action, entity_type, entity_id, actor_emp_id, created_at
     FROM audit_logs {$auditWhere} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$auditOffset}"
)->fetchAll(PDO::FETCH_ASSOC);

// ─────────────────────────────────────────────────────────────────────────────
// Integration link health summary
// ─────────────────────────────────────────────────────────────────────────────
$linkHealth = $pdo->query(
    "SELECT link_code, source_system_id, target_system_id, status
     FROM system_integrations ORDER BY status, link_code"
)->fetchAll(PDO::FETCH_ASSOC);

$activeCount   = count(array_filter($linkHealth, fn($r) => $r['status'] === 'Active'));
$notImplCount  = count(array_filter($linkHealth, fn($r) => $r['status'] === 'NotImplemented'));

$totalAuditSystems = (int)$pdo->query(
    "SELECT COUNT(DISTINCT system_id) FROM audit_logs WHERE system_id IN ('WEB','SHP','CUS','EMP','CRM','HR','FIN','IT','DOC','DEV','ADM')"
)->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>SuperAdmin Console — VOSTOKPRIBOR</title>
    <meta name="description" content="SuperAdmin read-only console: table browser, audit logs, view-as, integration link health"/>
    <style>
        :root {
            --bg: #0a0f1e; --bg2: #111827; --bg3: #1a2438;
            --accent: #6366f1; --accent2: #818cf8;
            --danger: #ef4444; --warn: #f59e0b; --ok: #10b981;
            --text: #e2e8f0; --muted: #94a3b8; --border: #1e293b;
            --radius: 8px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: var(--bg); color: var(--text); font-family: 'Segoe UI', system-ui, sans-serif; font-size: 14px; }

        /* View-As Banner */
        .view-as-banner {
            background: linear-gradient(90deg, #7c3aed, #db2777);
            color: #fff; padding: 8px 24px;
            display: flex; align-items: center; gap: 12px;
            font-weight: 600; letter-spacing: 0.05em; font-size: 13px;
        }
        .view-as-banner a { color: #fde68a; text-decoration: underline; margin-left: auto; }

        header {
            background: var(--bg2); border-bottom: 2px solid var(--accent);
            padding: 14px 28px; display: flex; align-items: center; gap: 16px;
        }
        header h1 { font-size: 18px; font-weight: 700; letter-spacing: 0.08em; color: var(--accent2); }
        header .badge {
            background: var(--accent); color: #fff;
            padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: 700;
        }
        header .user-info { margin-left: auto; color: var(--muted); font-size: 12px; }

        .layout { display: flex; min-height: calc(100vh - 56px); }
        nav {
            width: 220px; background: var(--bg2); border-right: 1px solid var(--border);
            padding: 20px 0; flex-shrink: 0;
        }
        nav a {
            display: block; padding: 10px 20px; color: var(--muted); text-decoration: none;
            font-size: 13px; transition: all 0.15s;
        }
        nav a:hover, nav a.active { background: var(--bg3); color: var(--accent2); border-left: 3px solid var(--accent); }
        nav .section-label { padding: 16px 20px 6px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: #475569; font-weight: 700; }

        main { flex: 1; padding: 28px; overflow: auto; }
        .section { display: none; }
        .section.active { display: block; }

        h2 { font-size: 16px; font-weight: 700; color: var(--accent2); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        h2 .count { font-size: 12px; background: var(--bg3); padding: 2px 8px; border-radius: 999px; color: var(--muted); }

        .card {
            background: var(--bg2); border: 1px solid var(--border); border-radius: var(--radius);
            padding: 16px; margin-bottom: 16px;
        }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; margin-bottom: 24px; }
        .stat-card {
            background: var(--bg3); border: 1px solid var(--border); border-radius: var(--radius);
            padding: 16px 20px; text-align: center;
        }
        .stat-card .val { font-size: 28px; font-weight: 800; color: var(--accent2); }
        .stat-card .lbl { font-size: 11px; color: var(--muted); margin-top: 4px; text-transform: uppercase; letter-spacing: 0.08em; }

        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background: var(--bg3); color: var(--muted); text-align: left; padding: 8px 10px; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--border); white-space: nowrap; }
        td { padding: 7px 10px; border-bottom: 1px solid var(--border); vertical-align: top; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        tr:hover td { background: var(--bg3); }

        .badge-ok   { background: rgba(16,185,129,.15); color: var(--ok);    padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        .badge-warn { background: rgba(245,158,11,.15); color: var(--warn);  padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        .badge-err  { background: rgba(239,68,68,.15);  color: var(--danger); padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }

        select, input[type=text] {
            background: var(--bg3); color: var(--text); border: 1px solid var(--border);
            padding: 7px 12px; border-radius: var(--radius); font-size: 13px;
        }
        select:focus, input[type=text]:focus { outline: 2px solid var(--accent); }

        .form-row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 16px; }
        .btn {
            background: var(--accent); color: #fff; border: none; padding: 8px 18px;
            border-radius: var(--radius); cursor: pointer; font-size: 13px; font-weight: 600; transition: opacity 0.15s;
        }
        .btn:hover { opacity: 0.85; }
        .btn-danger { background: var(--danger); }
        .btn-sm { padding: 4px 12px; font-size: 12px; }

        .pagination { display: flex; gap: 6px; margin-top: 12px; align-items: center; }
        .pagination a {
            background: var(--bg3); color: var(--text); text-decoration: none;
            padding: 5px 12px; border-radius: var(--radius); border: 1px solid var(--border); font-size: 12px;
        }
        .pagination a.current { background: var(--accent); color: #fff; border-color: var(--accent); }
        .pagination .info { color: var(--muted); font-size: 12px; }

        .error-box { background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.3); color: var(--danger); padding: 12px 16px; border-radius: var(--radius); margin-bottom: 12px; }

        .view-as-form { background: var(--bg3); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; margin-bottom: 20px; }
        .view-as-form label { display: block; margin-bottom: 6px; color: var(--muted); font-size: 12px; }
    </style>
</head>
<body>

<?php if ($viewAsProfile): ?>
<div class="view-as-banner">
    <span>👁 VIEW-AS MODE</span>
    <span><?= htmlspecialchars($viewAsProfile['emp_id']) ?> — <?= htmlspecialchars($viewAsProfile['full_name']) ?> (<?= htmlspecialchars($viewAsProfile['clearance_level'] ?? '') ?> / <?= htmlspecialchars($viewAsProfile['role_name'] ?? '') ?>)</span>
    <span>This is a read-only perspective. No session data is changed.</span>
    <a href="SuperAdminConsole.php?tab=viewas">Exit View-As</a>
</div>
<?php endif; ?>

<header>
    <h1>⚙ SuperAdmin Console</h1>
    <span class="badge">L4 ACCESS</span>
    <span style="color:var(--muted);font-size:12px;">System 11 — ADM</span>
    <div class="user-info">
        <?= htmlspecialchars($currentUser['full_name'] ?? 'Administrator') ?>
        &nbsp;|&nbsp; <?= htmlspecialchars($currentUser['clearance_level'] ?? 'L4') ?>
        &nbsp;|&nbsp; <a href="login.php" style="color:var(--danger);font-size:12px;">Logout</a>
    </div>
</header>

<div class="layout">
    <nav>
        <div class="section-label">Console</div>
        <a href="?tab=overview" id="nav-overview" class="<?= ($_GET['tab'] ?? 'overview') === 'overview' ? 'active' : '' ?>">📊 Overview</a>
        <a href="?tab=tables"   id="nav-tables"   class="<?= ($_GET['tab'] ?? '') === 'tables'   ? 'active' : '' ?>">🗃 Table Browser</a>
        <a href="?tab=audit"    id="nav-audit"    class="<?= ($_GET['tab'] ?? '') === 'audit'    ? 'active' : '' ?>">📋 Audit Logs</a>
        <a href="?tab=links"    id="nav-links"    class="<?= ($_GET['tab'] ?? '') === 'links'    ? 'active' : '' ?>">🔗 Integration Links</a>
        <a href="?tab=viewas"   id="nav-viewas"   class="<?= ($_GET['tab'] ?? '') === 'viewas'   ? 'active' : '' ?>">👁 View-As</a>
        <div class="section-label">Admin Portal</div>
        <a href="mainDashboard.php">← Back to Dashboard</a>
        <a href="PrivilegedAccounts.php">Privileged Accounts</a>
        <a href="AuditLogs.php">Full Audit Logs</a>
    </nav>

    <main>

        <?php $tab = $_GET['tab'] ?? 'overview'; ?>

        <!-- ── OVERVIEW ─────────────────────────────────────────────────── -->
        <?php if ($tab === 'overview'): ?>
        <h2>System Overview</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="val"><?= $activeCount ?></div>
                <div class="lbl">Active Links</div>
            </div>
            <div class="stat-card">
                <div class="val" style="color:var(--warn)"><?= $notImplCount ?></div>
                <div class="lbl">Not Implemented</div>
            </div>
            <div class="stat-card">
                <div class="val"><?= $auditTotal ?></div>
                <div class="lbl">Audit Events</div>
            </div>
            <div class="stat-card">
                <div class="val"><?= $totalAuditSystems ?>/11</div>
                <div class="lbl">Systems Audited</div>
            </div>
            <?php
            $empCount = (int)$pdo->query("SELECT COUNT(*) FROM employees WHERE is_system_account = 0")->fetchColumn();
            $cusCount = (int)$pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
            ?>
            <div class="stat-card">
                <div class="val"><?= $empCount ?></div>
                <div class="lbl">Employees</div>
            </div>
            <div class="stat-card">
                <div class="val"><?= $cusCount ?></div>
                <div class="lbl">Customers</div>
            </div>
        </div>

        <h2>Recent Audit Events <span class="count">Last 10</span></h2>
        <div class="card">
        <table>
            <thead><tr><th>Log ID</th><th>System</th><th>Action</th><th>Entity</th><th>Actor</th><th>Time</th></tr></thead>
            <tbody>
            <?php foreach (array_slice($auditRows, 0, 10) as $r): ?>
            <tr>
                <td><?= (int)$r['log_id'] ?></td>
                <td><?= htmlspecialchars($r['system_id'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['action'] ?? '') ?></td>
                <td><?= htmlspecialchars(($r['entity_type'] ?? '') . ' ' . ($r['entity_id'] ?? '')) ?></td>
                <td><?= htmlspecialchars($r['actor_emp_id'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['created_at'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>

        <!-- ── TABLE BROWSER ─────────────────────────────────────────────── -->
        <?php elseif ($tab === 'tables'): ?>
        <h2>Table Browser <span class="count">Read-Only</span></h2>

        <div class="card">
            <form method="GET" action="">
                <input type="hidden" name="tab" value="tables"/>
                <div class="form-row">
                    <select name="table" id="table-select" onchange="this.form.submit()">
                        <option value="">-- Select table --</option>
                        <?php foreach (BROWSABLE_TABLES as $t): ?>
                        <option value="<?= $t ?>" <?= $selectedTable === $t ? 'selected' : '' ?>><?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($selectedTable): ?>
                    <span class="info" style="color:var(--muted);font-size:12px;"><?= $tableTotal ?> rows total</span>
                    <?php endif; ?>
                </div>
            </form>

            <?php if ($tableError): ?>
            <div class="error-box"><?= htmlspecialchars($tableError) ?></div>
            <?php endif; ?>

            <?php if ($tableRows): ?>
            <div style="overflow-x:auto">
            <table>
                <thead><tr><?php foreach ($tableColumns as $c): ?><th><?= htmlspecialchars($c) ?></th><?php endforeach; ?></tr></thead>
                <tbody>
                <?php foreach ($tableRows as $row): ?>
                <tr><?php foreach ($row as $val): ?><td title="<?= htmlspecialchars((string)$val) ?>"><?= htmlspecialchars((string)$val) ?></td><?php endforeach; ?></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php $totalPages = (int)ceil($tableTotal / $perPage); ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                <a href="?tab=tables&table=<?= urlencode($selectedTable) ?>&page=<?= $page - 1 ?>">← Prev</a>
                <?php endif; ?>
                <span class="info">Page <?= $page ?> / <?= $totalPages ?></span>
                <?php if ($page < $totalPages): ?>
                <a href="?tab=tables&table=<?= urlencode($selectedTable) ?>&page=<?= $page + 1 ?>">Next →</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- ── AUDIT LOGS ────────────────────────────────────────────────── -->
        <?php elseif ($tab === 'audit'): ?>
        <h2>Audit Logs <span class="count"><?= $auditTotal ?> events</span></h2>

        <div class="card">
            <form method="GET" action="">
                <input type="hidden" name="tab" value="audit"/>
                <div class="form-row">
                    <select name="audit_system" onchange="this.form.submit()">
                        <option value="">All Systems</option>
                        <?php foreach (['WEB','SHP','CUS','EMP','CRM','HR','FIN','IT','DOC','DEV','ADM'] as $s): ?>
                        <option value="<?= $s ?>" <?= $auditFilter === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span style="color:var(--muted);font-size:12px;"><?= $auditTotal ?> events | <?= $totalAuditSystems ?>/11 systems</span>
                </div>
            </form>

            <table>
                <thead><tr><th>ID</th><th>System</th><th>Action</th><th>Entity Type</th><th>Entity ID</th><th>Actor</th><th>Time</th></tr></thead>
                <tbody>
                <?php foreach ($auditRows as $r): ?>
                <tr>
                    <td><?= (int)$r['log_id'] ?></td>
                    <td><span class="badge-ok"><?= htmlspecialchars($r['system_id'] ?? '') ?></span></td>
                    <td><?= htmlspecialchars($r['action'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['entity_type'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['entity_id'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['actor_emp_id'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['created_at'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?php $auditPages = (int)ceil($auditTotal / $perPage); ?>
            <div class="pagination">
                <?php if ($auditPage > 1): ?>
                <a href="?tab=audit&audit_system=<?= urlencode($auditFilter) ?>&apage=<?= $auditPage - 1 ?>">← Prev</a>
                <?php endif; ?>
                <span class="info">Page <?= $auditPage ?> / <?= $auditPages ?></span>
                <?php if ($auditPage < $auditPages): ?>
                <a href="?tab=audit&audit_system=<?= urlencode($auditFilter) ?>&apage=<?= $auditPage + 1 ?>">Next →</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── INTEGRATION LINKS ─────────────────────────────────────────── -->
        <?php elseif ($tab === 'links'): ?>
        <h2>Integration Links <span class="count"><?= count($linkHealth) ?> total | <?= $activeCount ?> Active | <?= $notImplCount ?> NotImplemented</span></h2>
        <div class="card">
        <table>
            <thead><tr><th>Link Code</th><th>Source</th><th>Target</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($linkHealth as $lnk): ?>
            <tr>
                <td style="font-family:monospace;font-size:12px;"><?= htmlspecialchars($lnk['link_code']) ?></td>
                <td><?= htmlspecialchars($lnk['source_system_id']) ?></td>
                <td><?= htmlspecialchars($lnk['target_system_id']) ?></td>
                <td>
                    <?php if ($lnk['status'] === 'Active'): ?>
                    <span class="badge-ok">Active</span>
                    <?php elseif ($lnk['status'] === 'NotImplemented'): ?>
                    <span class="badge-warn">NotImplemented</span>
                    <?php else: ?>
                    <span class="badge-err"><?= htmlspecialchars($lnk['status']) ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>

        <!-- ── VIEW-AS ────────────────────────────────────────────────────── -->
        <?php elseif ($tab === 'viewas'): ?>
        <h2>View-As Mode <span class="count">Read-Only Perspective</span></h2>
        <p style="color:var(--muted);margin-bottom:16px;font-size:13px;">
            View the system from another user's perspective. No session data or permissions are changed — this is display-only.
        </p>

        <div class="view-as-form">
            <label>Enter Employee ID to view as:</label>
            <form method="GET" action="" style="display:flex;gap:10px;align-items:center;">
                <input type="hidden" name="tab" value="viewas"/>
                <input type="text" name="view_as" placeholder="EMP-1001" value="<?= htmlspecialchars($viewAsEmpId) ?>" style="width:160px;"/>
                <button type="submit" class="btn">Activate View-As</button>
                <?php if ($viewAsEmpId): ?>
                <a href="?tab=viewas" class="btn btn-danger" style="text-decoration:none;">Exit</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($viewAsProfile): ?>
        <div class="card">
            <h2>Viewing as: <?= htmlspecialchars($viewAsProfile['full_name']) ?></h2>
            <table>
                <thead><tr><th>Field</th><th>Value</th></tr></thead>
                <tbody>
                    <tr><td>Employee ID</td><td><?= htmlspecialchars($viewAsProfile['emp_id']) ?></td></tr>
                    <tr><td>Full Name</td><td><?= htmlspecialchars($viewAsProfile['full_name']) ?></td></tr>
                    <tr><td>Job Title</td><td><?= htmlspecialchars($viewAsProfile['job_title']) ?></td></tr>
                    <tr><td>Clearance Level</td><td><?= htmlspecialchars($viewAsProfile['clearance_level']) ?></td></tr>
                    <tr><td>Role</td><td><?= htmlspecialchars($viewAsProfile['role_name'] ?? 'N/A') ?></td></tr>
                </tbody>
            </table>
        </div>
        <?php elseif ($viewAsEmpId): ?>
        <div class="error-box">Employee ID "<?= htmlspecialchars($viewAsEmpId) ?>" not found.</div>
        <?php endif; ?>

        <?php endif; ?>

    </main>
</div>
</body>
</html>
