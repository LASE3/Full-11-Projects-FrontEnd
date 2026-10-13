<?php

declare(strict_types=1);

/**
 * Class 4: Employee Intranet - Staff Directory API
 * Location: Employee Intranet/api/directory.php
 * Methods: GET
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('EMP', []);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/I18n.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$lang = $_GET['lang'] ?? 'en';
$deptFilter = trim($_GET['dept'] ?? '');
$searchQuery = trim($_GET['q'] ?? ($_GET['query'] ?? ''));
$sortKey = trim($_GET['sort'] ?? '');
$dir = strtolower(trim($_GET['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = min(100, max(1, (int)($_GET['per_page'] ?? 20)));
$offset = ($page - 1) * $perPage;

$allowedSort = [
    'emp_id'          => 'e.emp_id',
    'full_name'       => 'e.full_name',
    'job_title'       => 'e.job_title',
    'department_code' => 'e.department_code',
    'clearance_level' => 'e.clearance_level',
    'hire_date'       => 'e.hire_date',
    'email'           => 'e.email'
];
$orderClause = isset($allowedSort[$sortKey]) 
    ? "{$allowedSort[$sortKey]} {$dir}" 
    : "d.dept_code ASC, e.clearance_level DESC, e.full_name ASC";

$callerDept = strtoupper(trim((string)($_vp_user['department_code'] ?? '')));
$callerClearance = strtoupper(trim((string)($_vp_user['clearance_level'] ?? 'L1')));
$isHrOrAdmin = isSuperAdmin($_vp_user) || ($callerDept === 'HRA') || ($callerClearance === 'L4');

try {
    $whereConditions = ["e.employment_status = 'Active'"];
    $params = [];

    if (!empty($deptFilter) && strtoupper($deptFilter) !== 'ALL') {
        $whereConditions[] = "e.department_code = :dept";
        $params[':dept'] = $deptFilter;
    }

    if (!empty($searchQuery)) {
        $whereConditions[] = "(e.full_name LIKE :q1 OR e.job_title LIKE :q2 OR e.emp_id LIKE :q3 OR e.email LIKE :q4)";
        $wildcard = "%{$searchQuery}%";
        $params[':q1'] = $wildcard;
        $params[':q2'] = $wildcard;
        $params[':q3'] = $wildcard;
        $params[':q4'] = $wildcard;
    }

    $whereSql = implode(' AND ', $whereConditions);

    // Total count for pagination contract
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM employees e JOIN departments d ON e.department_code = d.dept_code WHERE {$whereSql}");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    $sql = "
        SELECT 
            e.emp_id,
            e.full_name,
            e.job_title,
            e.department_code,
            d.dept_name,
            e.clearance_level,
            e.email,
            e.employment_status,
            e.hire_date,
            m.full_name AS manager_name
        FROM employees e
        JOIN departments d ON e.department_code = d.dept_code
        LEFT JOIN employees m ON e.manager_emp_id = m.emp_id
        WHERE {$whereSql}
        ORDER BY {$orderClause}
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $directory = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($directory as &$emp) {
        $emp['department_display'] = I18n::translate((string)$emp['department_code'], $lang);
        $emp['phone'] = '+7 (812) 555-0100'; // Default VOSTOKPRIBOR PBX
        $emp['photo'] = null; // Dynamically generated initials avatar in UI

        // Data Isolation: Mask sensitive fields for non-HR / non-admin users
        if (!$isHrOrAdmin) {
            unset(
                $emp['clearance_level'],
                $emp['hire_date'],
                $emp['employment_status'],
                $emp['status_display'],
                $emp['manager_name']
            );
        } else {
            $emp['status_display'] = I18n::translate((string)$emp['employment_status'], $lang);
        }
    }
    unset($emp);

    Response::paginated($directory, $total, $page, $perPage, "Staff directory loaded");
} catch (Exception $e) {
    error_log("Failed to load directory: " . $e->getMessage());
    Response::error("Failed to load directory. Please try again later.", 500);
}
