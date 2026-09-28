<?php

/**
 * VOSTOKPRIBOR System 11 // GOV-CORE
 * Centralized Governance Data Service
 * Provides database queries for all System 11 views.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';

/**
 * Get active user session details for header
 */
function gov_getActiveUserProfile()
{
    if (!empty($_SESSION['vostok_user'])) {
        return $_SESSION['vostok_user'];
    }
    return [
        'full_name' => 'System Administrator',
        'user_id' => 'EMP-0001',
        'emp_id' => 'EMP-0001',
        'clearance_level' => 'L4',
        'role_name' => 'Executive SuperAdmin'
    ];
}

/**
 * Get Telemetry Grid (Employees, System Permits 01-11, Attestation, Status)
 */
function gov_getTelemetryGridData($filter = 'ALL', $search = '')
{
    $pdo = getDbConnection();

    // We join employees, departments, roles, employee_accounts, access_reviews
    // Hide EMP-0001 (internal master root admin account) from employee directory per user directive
    $sql = "
        SELECT 
            e.emp_id,
            e.full_name,
            e.job_title,
            e.department_code,
            e.clearance_level,
            e.employment_status,
            d.dept_name,
            ea.status AS account_status,
            ea.last_login,
            r.role_id,
            r.role_name,
            ar.review_id,
            ar.finding,
            ar.action_taken
        FROM employees e
        LEFT JOIN departments d ON e.department_code = d.dept_code
        LEFT JOIN employee_accounts ea ON e.emp_id = ea.emp_id AND ea.status = 'Active'
        LEFT JOIN employee_roles er ON e.emp_id = er.emp_id
        LEFT JOIN roles r ON er.role_id = r.role_id
        LEFT JOIN access_reviews ar ON e.emp_id = ar.emp_id
        WHERE e.emp_id != 'EMP-0001'
        GROUP BY e.emp_id
        ORDER BY 
            CASE 
                WHEN e.employment_status = 'Suspended' THEN 1
                WHEN e.clearance_level = 'L4' THEN 2
                WHEN e.clearance_level = 'L3' THEN 3
                ELSE 4
            END,
            e.emp_id ASC
    ";

    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    // Fetch role_system_access map
    $accessMap = [];
    $accessRows = $pdo->query("SELECT role_id, system_id, access_level FROM role_system_access")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($accessRows as $ar) {
        $accessMap[$ar['role_id']][$ar['system_id']] = $ar['access_level'];
    }

    $systemCodeToId = [
        '01' => 'WEB',
        '02' => 'SHP',
        '03' => 'CUS',
        '04' => 'EMP',
        '05' => 'CRM',
        '06' => 'HR',
        '07' => 'FIN',
        '08' => 'IT',
        '09' => 'DOC',
        '10' => 'DEV',
        '11' => 'ADM'
    ];

    $results = [];
    foreach ($rows as $row) {
        $roleId = $row['role_id'];
        $permits = [];
        $isL4 = ($row['clearance_level'] === 'L4');

        foreach ($systemCodeToId as $num => $sysCode) {
            $hasAccess = false;
            if ($isL4) {
                $hasAccess = true;
            } elseif ($roleId && isset($accessMap[$roleId][$sysCode])) {
                $hasAccess = true;
            }
            $permits[$num] = $hasAccess;
        }
        $row['permits'] = $permits;

        // Determine category for filtering
        $cat = 'ACTIVE';
        if ($row['employment_status'] === 'Suspended' || (isset($row['finding']) && strpos($row['finding'], 'Breach') !== false)) {
            $cat = 'ORPHANED';
        } elseif ($row['clearance_level'] === 'L4') {
            $cat = 'ELEVATED';
        } elseif (!empty($row['action_taken']) && strpos($row['action_taken'], 'Pending') !== false) {
            $cat = 'PENDING';
        } elseif ($row['employment_status'] === 'Terminated') {
            $cat = 'REVOKED';
        }
        $row['category'] = $cat;

        // Role status badge & disposition action
        if ($cat === 'ORPHANED') {
            $row['role_status_html'] = '<span class="font-label-uppercase text-[9px] bg-error text-on-error px-space-xs font-bold">FLAG: UNLAWFUL BIND</span>';
            $row['disposition_btn'] = '<button class="px-space-xs py-[2px] bg-error text-on-error font-telemetry-micro text-[10px] font-bold uppercase tracking-wider hover:bg-error-container transition-colors" onclick="purgeOrphanToken(\'' . $row['emp_id'] . '\')">Purge Token</button>';
        } elseif ($cat === 'PENDING') {
            $row['role_status_html'] = '<span class="font-label-uppercase text-[9px] bg-on-tertiary-container/20 text-on-tertiary-container px-space-xs font-bold border border-on-tertiary-container/30">PENDING SIGN-OFF</span>';
            $row['disposition_btn'] = '<button class="px-space-xs py-[2px] bg-secondary-container text-on-secondary-container font-telemetry-micro text-[10px] font-bold uppercase tracking-wider hover:bg-secondary transition-colors" onclick="attestRole(\'' . $row['emp_id'] . '\')">Attest</button>';
        } elseif ($row['clearance_level'] === 'L4') {
            $row['role_status_html'] = '<span class="font-label-uppercase text-[9px] bg-primary text-on-primary px-space-xs font-bold">MASTER SIGNER</span>';
            $row['disposition_btn'] = '<span class="font-telemetry-micro text-[10px] text-on-surface-variant font-mono">Self-Audit Interlock</span>';
        } else {
            $row['role_status_html'] = '<span class="font-label-uppercase text-[9px] bg-secondary-fixed/40 text-on-secondary-fixed font-bold px-space-xs border border-secondary-fixed/50">■ APPROVED</span>';
            $row['disposition_btn'] = '<button class="px-space-xs py-[2px] bg-surface-container-high hover:bg-surface-variant text-on-surface font-telemetry-micro text-[10px] font-bold uppercase tracking-wider" onclick="reviewRole(\'' . $row['emp_id'] . '\')">Review</button>';
        }

        $results[] = $row;
    }

    return $results;
}

/**
 * Get Attestation Summary Metrics
 */
function gov_getGovernanceMetrics()
{
    $pdo = getDbConnection();

    $totalEmployees = $pdo->query("SELECT COUNT(*) FROM employees WHERE emp_id != 'EMP-0001'")->fetchColumn();
    $orphanedCount = $pdo->query("SELECT COUNT(*) FROM employees WHERE employment_status = 'Suspended' OR employment_status = 'Terminated'")->fetchColumn();
    $attestedCount = $pdo->query("SELECT COUNT(*) FROM access_reviews WHERE action_taken LIKE '%Attested%' OR action_taken LIKE '%Validated%'")->fetchColumn();
    $pendingCount = $pdo->query("SELECT COUNT(*) FROM access_reviews WHERE action_taken LIKE '%Pending%'")->fetchColumn();
    $elevatedCount = $pdo->query("SELECT COUNT(*) FROM employees WHERE clearance_level = 'L4' AND emp_id != 'EMP-0001'")->fetchColumn();

    $percentage = ($totalEmployees > 0) ? round(($attestedCount / $totalEmployees) * 100) : 85;

    // Dual-custody audit signers from DB
    $dualSigners = $pdo->query("
        SELECT emp_id, full_name, job_title 
        FROM employees 
        WHERE clearance_level = 'L4' AND emp_id != 'EMP-0001'
        ORDER BY emp_id ASC LIMIT 2
    ")->fetchAll(PDO::FETCH_ASSOC);

    return [
        'total_employees' => (int)$totalEmployees,
        'orphaned_count' => max(1, (int)$orphanedCount),
        'attested_count' => (int)$attestedCount,
        'pending_count' => max(1, (int)$pendingCount),
        'elevated_count' => (int)$elevatedCount,
        'percentage' => $percentage,
        'dual_signers' => $dualSigners
    ];
}

/**
 * Get Critical Anomaly / Remediation Desk Record
 */
function gov_getCriticalAnomaly()
{
    $pdo = getDbConnection();

    // Look for security event with Critical severity or orphaned account
    $event = $pdo->query("
        SELECT se.*, e.full_name, e.job_title, e.department_code, d.dept_name, ea.last_login
        FROM security_events se
        LEFT JOIN employees e ON se.actor_emp_id = e.emp_id
        LEFT JOIN departments d ON e.department_code = d.dept_code
        LEFT JOIN employee_accounts ea ON e.emp_id = ea.emp_id
        WHERE se.severity = 'Critical'
        ORDER BY se.event_time DESC LIMIT 1
    ")->fetch(PDO::FETCH_ASSOC);

    if ($event) {
        return $event;
    }

    return [
        'actor_emp_id' => 'EMP-1009',
        'full_name' => 'Maksim Sokolov',
        'job_title' => 'Contractor - SCADA Telemetry Unit (External Integration)',
        'description' => 'SEC-POL-44 Breach Detected: Vendor contract termination date was 14 days ago. High-privilege RSA SSH-key remains configured inside SYS-03 (CNC SCADA Gateway).',
        'dept_name' => 'NONE (EXPIRED DEPT-08)',
        'last_login' => date('Y-m-d H:i:s', strtotime('-5 hours'))
    ];
}

/**
 * Get Role Catalog & Matrix
 */
function gov_getRolesCatalog()
{
    $pdo = getDbConnection();
    $roles = $pdo->query("
        SELECT r.*, COUNT(er.emp_id) AS assignee_count
        FROM roles r
        LEFT JOIN employee_roles er ON r.role_id = er.role_id
        GROUP BY r.role_id
        ORDER BY r.role_id ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $accessRows = $pdo->query("SELECT role_id, system_id, access_level FROM role_system_access")->fetchAll(PDO::FETCH_ASSOC);
    $map = [];
    foreach ($accessRows as $ar) {
        $map[$ar['role_id']][] = $ar['system_id'];
    }

    foreach ($roles as &$r) {
        $r['systems'] = $map[$r['role_id']] ?? [];
    }

    return $roles;
}

/**
 * Get Privileged Accounts (L3, L4, Root)
 */
function gov_getPrivilegedAccounts()
{
    $pdo = getDbConnection();
    $sql = "
        SELECT 
            e.emp_id, e.full_name, e.job_title, e.clearance_level, e.department_code, e.email,
            d.dept_name,
            ea.account_id, ea.username, ea.status AS account_status, ea.mfa_enabled, ea.last_login,
            r.role_name,
            us.session_id, us.started_at AS session_started
        FROM employees e
        JOIN employee_accounts ea ON e.emp_id = ea.emp_id
        LEFT JOIN departments d ON e.department_code = d.dept_code
        LEFT JOIN employee_roles er ON e.emp_id = er.emp_id
        LEFT JOIN roles r ON er.role_id = r.role_id
        LEFT JOIN user_sessions us ON ea.account_id = us.employee_account_id AND us.status = 'Active'
        WHERE (e.clearance_level IN ('L3', 'L4') OR r.role_name LIKE '%Admin%' OR r.role_name LIKE '%Officer%')
          AND e.emp_id != 'EMP-0001'
        GROUP BY ea.account_id
        ORDER BY 
            CASE WHEN e.clearance_level = 'L4' THEN 1 ELSE 2 END,
            e.emp_id ASC
    ";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get Unified Audit Logs
 */
function gov_getUnifiedAuditLogs($limit = 30)
{
    $pdo = getDbConnection();
    $sql = "
        SELECT 
            al.audit_id,
            al.actor_emp_id,
            al.actor_system,
            al.system_id,
            al.action,
            al.target_entity_type,
            al.target_entity_id,
            al.source_ip,
            al.result,
            al.occurred_at,
            al.new_values,
            e.full_name AS actor_name
        FROM audit_logs al
        LEFT JOIN employees e ON al.actor_emp_id = e.emp_id
        ORDER BY al.occurred_at DESC
        LIMIT " . (int)$limit;
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get Ingestion Bridges & Relays
 */
function gov_getIngestionBridges()
{
    $pdo = getDbConnection();
    return $pdo->query("
        SELECT si.*, 
               (SELECT COUNT(*) FROM system_integration_logs sil WHERE sil.link_code = si.link_code) AS transaction_count,
               (SELECT sil.executed_at FROM system_integration_logs sil WHERE sil.link_code = si.link_code ORDER BY sil.log_id DESC LIMIT 1) AS last_sync
        FROM system_integrations si
        ORDER BY si.integration_id ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get Enterprise Security Policies
 */
function gov_getSecurityPolicies()
{
    $pdo = getDbConnection();
    return $pdo->query("SELECT * FROM security_policies ORDER BY policy_id ASC")->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get Board Risk Register
 */
function gov_getRiskRegister()
{
    $pdo = getDbConnection();
    return $pdo->query("
        SELECT rr.*, e.full_name AS owner_name, e.job_title AS owner_title
        FROM risk_register rr
        LEFT JOIN employees e ON rr.owner_emp_id = e.emp_id
        ORDER BY 
            CASE WHEN rr.impact = 'Critical' THEN 1 WHEN rr.impact = 'High' THEN 2 ELSE 3 END,
            rr.risk_id ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get Systems Catalog for Emergency Lockdown Matrix
 */
function gov_getSystemsLockdownMatrix()
{
    $pdo = getDbConnection();
    return $pdo->query("SELECT * FROM systems_catalog ORDER BY system_id ASC")->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get Break-Glass Events
 */
function gov_getBreakGlassEvents()
{
    $pdo = getDbConnection();
    return $pdo->query("
        SELECT se.*, e.full_name AS actor_name 
        FROM security_events se
        LEFT JOIN employees e ON se.actor_emp_id = e.emp_id
        ORDER BY se.event_time DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get Compliance Oversight Data (Access Reviews & Incidents)
 */
function gov_getComplianceOversight()
{
    $pdo = getDbConnection();
    $reviews = $pdo->query("
        SELECT ar.*, e.full_name AS emp_name, e.job_title, e.department_code, rev.full_name AS reviewer_name
        FROM access_reviews ar
        LEFT JOIN employees e ON ar.emp_id = e.emp_id
        LEFT JOIN employees rev ON ar.reviewed_by_emp_id = rev.emp_id
        ORDER BY ar.review_id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $incidents = $pdo->query("
        SELECT si.*, e.full_name AS lead_name
        FROM security_incidents si
        LEFT JOIN employees e ON si.assigned_to_emp_id = e.emp_id
        ORDER BY si.incident_id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    return [
        'reviews' => $reviews,
        'incidents' => $incidents
    ];
}

/**
 * Get Compliance Controls Catalog
 */
function gov_getComplianceControls()
{
    $pdo = getDbConnection();
    return $pdo->query("
        SELECT cc.*, e.full_name AS custodian_name, e.job_title AS custodian_title
        FROM compliance_controls cc
        LEFT JOIN employees e ON cc.custodian_emp_id = e.emp_id
        ORDER BY cc.control_id ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get Re-Certification Windows
 */
function gov_getRecertificationWindows()
{
    $pdo = getDbConnection();
    return $pdo->query("
        SELECT rw.*, e.full_name AS creator_name
        FROM recertification_windows rw
        LEFT JOIN employees e ON rw.created_by_emp_id = e.emp_id
        ORDER BY rw.window_id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get dynamic sidebar navigation badge indicators for System 11 views
 */
function gov_getSidebarBadges()
{
    $pdo = getDbConnection();
    try {
        $orphaned = (int)$pdo->query("SELECT COUNT(*) FROM employees WHERE employment_status IN ('Suspended', 'Terminated')")->fetchColumn();
        $activeSessions = (int)$pdo->query("SELECT COUNT(DISTINCT ea.emp_id) FROM employee_accounts ea JOIN user_sessions us ON ea.account_id = us.employee_account_id WHERE us.status = 'Active'")->fetchColumn();
        if ($activeSessions === 0) {
            $activeSessions = (int)$pdo->query("SELECT COUNT(*) FROM employees WHERE clearance_level = 'L4' AND emp_id != 'EMP-0001'")->fetchColumn();
        }
        $auditCount = (int)$pdo->query("SELECT COUNT(*) FROM audit_logs")->fetchColumn();
        $bridgeCount = (int)$pdo->query("SELECT COUNT(*) FROM system_integrations")->fetchColumn();
        $isolatedCount = (int)$pdo->query("SELECT COUNT(*) FROM systems_catalog WHERE status = 'ISOLATED'")->fetchColumn();
        $breakGlassCount = (int)$pdo->query("SELECT COUNT(*) FROM security_events WHERE event_type LIKE '%Break%' OR severity IN ('Critical', 'High')")->fetchColumn();
        $policyCount = (int)$pdo->query("SELECT COUNT(*) FROM security_policies")->fetchColumn();
        $riskCount = (int)$pdo->query("SELECT COUNT(*) FROM risk_register WHERE status != 'Resolved'")->fetchColumn();
        $reviewCount = (int)$pdo->query("SELECT COUNT(*) FROM access_reviews WHERE action_taken LIKE '%Pending%' OR finding LIKE '%Breach%'")->fetchColumn();
        if ($reviewCount === 0) {
            $reviewCount = (int)$pdo->query("SELECT COUNT(*) FROM access_reviews")->fetchColumn();
        }

        return [
            'dashboard' => 'KPI & Threat',
            'access_matrix' => max(1, $orphaned) . ' Orphaned',
            'privileged' => max(1, $activeSessions) . ' Active',
            'audit_logs' => ($auditCount > 0 ? $auditCount . ' Events' : 'LIVE'),
            'bridges' => ($bridgeCount > 0 ? sprintf('%02d Nodes', $bridgeCount) : '01-10'),
            'break_glass' => max(1, $breakGlassCount) . ' Armed',
            'lockdown' => ($isolatedCount > 0) ? 'DEFCON-1' : 'DEFCON-4',
            'policies' => $policyCount . ' Active',
            'risks' => $riskCount . ' Filed',
            'compliance' => max(1, $reviewCount) . ' Reviews'
        ];
    } catch (Exception $e) {
        return [
            'dashboard' => 'KPI & Threat',
            'access_matrix' => '1 Orphaned',
            'privileged' => '7 Active',
            'audit_logs' => 'LIVE',
            'bridges' => '01-10',
            'break_glass' => '4 Armed',
            'lockdown' => 'DEFCON-4',
            'policies' => '10 Active',
            'risks' => '10 Filed',
            'compliance' => '9 Reviews'
        ];
    }
}

/**
 * Render the unified high-trust sidebar navigation with live badge counts and proper active indicators.
 */
function gov_renderSidebar($currentPage = '')
{
    if (empty($currentPage)) {
        $currentPage = basename($_SERVER['SCRIPT_NAME'] ?? 'mainDashboard.php');
    }
    $currentPage = strtolower(basename($currentPage));

    $badges = gov_getSidebarBadges();

    $sections = [
        'CORE GOVERNANCE' => [
            [
                'file' => 'maindashboard.php',
                'href' => 'mainDashboard.php',
                'path' => 'dashboard',
                'icon' => 'dashboard',
                'title' => 'Main Dashboard',
                'badge' => $badges['dashboard'],
                'badge_class' => 'font-telemetry-micro text-[10px] px-space-2xs bg-secondary-container/20 text-secondary-fixed rounded'
            ],
            [
                'file' => 'accessmatrix.php',
                'href' => 'accessMatrix.php',
                'path' => 'access-matrix-and-role-review',
                'icon' => 'grid_view',
                'title' => 'Access Matrix',
                'badge' => $badges['access_matrix'],
                'badge_class' => 'font-telemetry-micro text-[10px] px-space-2xs bg-error-container text-on-error-container rounded font-bold'
            ],
            [
                'file' => 'privilegedaccounts.php',
                'href' => 'PrivilegedAccounts.php',
                'path' => 'privileged-accounts-monitoring',
                'icon' => 'admin_panel_settings',
                'title' => 'Privileged Accounts',
                'badge' => $badges['privileged'],
                'badge_class' => 'font-telemetry-micro text-[10px] px-space-2xs bg-tertiary-container text-secondary-fixed rounded font-bold border border-secondary-fixed/40'
            ]
        ],
        'AUDIT & INTELLIGENCE' => [
            [
                'file' => 'auditlogs.php',
                'href' => 'AuditLogs.php',
                'path' => 'audit-logs-and-event-streams',
                'icon' => 'terminal',
                'title' => 'Audit Logs',
                'badge' => $badges['audit_logs'],
                'badge_class' => 'font-telemetry-micro text-[10px] px-space-2xs bg-secondary-fixed text-on-secondary-fixed font-bold rounded animate-pulse'
            ],
            [
                'file' => 'ingestionbridges.php',
                'href' => 'IngestionBridges.php',
                'path' => 'ingestion-bridges',
                'icon' => 'cable',
                'title' => 'Ingestion Bridges',
                'badge' => $badges['bridges'],
                'badge_class' => 'font-telemetry-micro text-[10px] text-on-primary-container'
            ],
            [
                'file' => 'break-glassaccess.php',
                'href' => 'Break-GlassAccess.php',
                'path' => 'emergency-break-glass',
                'icon' => 'e911_emergency',
                'title' => 'Break-Glass Access',
                'badge' => $badges['break_glass'],
                'badge_class' => 'font-telemetry-micro text-[10px] px-space-2xs bg-error-container text-on-error-container font-bold rounded border border-error/50',
                'is_break_glass' => true
            ],
            [
                'file' => 'emergencylockdown.php',
                'href' => 'EmergencyLockdown.php',
                'path' => 'emergency-lockdown',
                'icon' => 'lock',
                'title' => 'Emergency Lockdown',
                'badge' => $badges['lockdown'],
                'badge_class' => 'font-telemetry-micro text-[10px] px-space-2xs bg-error text-on-error font-bold rounded',
                'is_lockdown' => true
            ]
        ],
        'REGULATORY & RISK' => [
            [
                'file' => 'securitypolicies.php',
                'href' => 'SecurityPolicies.php',
                'path' => 'enterprise-security-policies',
                'icon' => 'policy',
                'title' => 'Security Policies',
                'badge' => $badges['policies'],
                'badge_class' => 'font-telemetry-micro text-[10px] px-space-2xs bg-primary-container text-on-primary rounded border border-outline/30'
            ],
            [
                'file' => 'boardriskregister.php',
                'href' => 'BoardRiskRegister.php',
                'path' => 'board-risk-register',
                'icon' => 'balance',
                'title' => 'Board Risk Register',
                'badge' => $badges['risks'],
                'badge_class' => 'font-telemetry-micro text-[9px] px-space-2xs bg-primary-container text-on-primary-container rounded border border-outline/30'
            ],
            [
                'file' => 'complianceoversight.php',
                'href' => 'ComplianceOversight.php',
                'path' => 'compliance-and-incident-oversight',
                'icon' => 'gavel',
                'title' => 'Compliance Oversight',
                'badge' => $badges['compliance'],
                'badge_class' => 'font-telemetry-micro text-[10px] px-space-2xs bg-secondary-container/20 text-secondary-fixed rounded'
            ]
        ]
    ];

    $html = '<aside class="fixed left-0 top-[60px] h-[calc(100vh-60px)] w-[260px] bg-primary z-40 flex flex-col justify-between border-r border-outline/30 select-none overflow-y-auto">';
    $html .= '<div class="py-space-md">';

    foreach ($sections as $sectionTitle => $items) {
        $html .= '<div class="px-space-md mb-space-xs"><span class="font-label-uppercase text-label-uppercase text-on-primary-container tracking-wider">' . htmlspecialchars($sectionTitle) . '</span></div>';
        $html .= '<nav class="flex flex-col gap-[2px] px-space-xs mb-space-md" data-active-classes="bg-primary-container text-on-primary font-semibold border-l-4 border-secondary-fixed">';
        
        foreach ($items as $item) {
            $isActive = ($currentPage === $item['file']);
            $aria = $isActive ? ' aria-current="page"' : '';
            
            if (!empty($item['is_lockdown'])) {
                if ($isActive) {
                    $linkClass = 'flex items-center justify-between px-space-sm py-space-xs rounded transition-all bg-error text-on-error font-semibold border-l-4 border-white font-body-compact text-body-compact shadow-sm';
                } else {
                    $linkClass = 'flex items-center justify-between px-space-sm py-space-xs rounded text-error hover:bg-error-container hover:text-on-error-container transition-all font-body-compact text-body-compact';
                }
                $titleClass = 'font-bold uppercase';
            } elseif (!empty($item['is_break_glass'])) {
                if ($isActive) {
                    $linkClass = 'flex items-center justify-between px-space-sm py-space-xs rounded transition-all bg-error-container text-on-error-container font-semibold border-l-4 border-error font-body-compact text-body-compact shadow-sm';
                } else {
                    $linkClass = 'flex items-center justify-between px-space-sm py-space-xs rounded text-error hover:bg-error-container hover:text-on-error-container transition-all font-body-compact text-body-compact';
                }
                $titleClass = 'font-bold uppercase text-error';
            } else {
                if ($isActive) {
                    $linkClass = 'flex items-center justify-between px-space-sm py-space-xs rounded transition-all bg-primary-container text-on-primary font-semibold border-l-4 border-secondary-fixed font-body-compact text-body-compact shadow-sm';
                } else {
                    $linkClass = 'flex items-center justify-between px-space-sm py-space-xs rounded text-on-primary-container hover:bg-primary-container hover:text-on-primary transition-all font-body-compact text-body-compact';
                }
                $titleClass = '';
            }

            $html .= '<a' . $aria . ' class="' . $linkClass . '" data-path="' . htmlspecialchars($item['path']) . '" href="' . htmlspecialchars($item['href']) . '">';
            $html .= '<div class="flex items-center gap-space-sm"><span class="material-symbols-outlined text-[18px]">' . htmlspecialchars($item['icon']) . '</span><span class="' . $titleClass . '">' . htmlspecialchars($item['title']) . '</span></div>';
            $html .= '<span class="' . $item['badge_class'] . '" data-nav-badge="' . htmlspecialchars($item['path']) . '">' . htmlspecialchars($item['badge']) . '</span>';
            $html .= '</a>';
        }
        $html .= '</nav>';
    }

    $html .= '</div>';
    $html .= '<div class="p-space-md bg-primary-container/40 border-t border-outline/20 flex flex-col gap-space-2xs">';
    $html .= '<div class="flex items-center justify-between"><span class="font-security-stamp text-[10px] text-secondary-fixed-dim uppercase tracking-wider">SEC-OPS FACILITY</span><div class="w-1.5 h-1.5 rounded-full bg-secondary-fixed"></div></div>';
    $html .= '<div class="font-telemetry-micro text-telemetry-micro text-on-primary-container">ALMATY STATION • EST. 1968</div>';
    $html .= '<div class="font-telemetry-data text-telemetry-data text-on-primary font-semibold tracking-wider pt-space-2xs"><span class="station-live-clock">UTC+6 (ALMATY TIME)</span></div>';
    $html .= '</div>';
    $html .= '</aside>';

    return $html;
}



