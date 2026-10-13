<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR HR System - Employee Records List API
 * Location: HR System/api/employees.php
 * Contract: q, sort, dir, page, per_page (max 100) -> {rows, total, page, per_page}
 */

require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('HR', []);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/helpers/Response.php';
require_once __DIR__ . '/../hr_service.php';

$pdo = getDbConnection();

$q = trim((string)($_GET['q'] ?? ($_GET['query'] ?? ($_GET['search'] ?? ''))));
$dept = trim((string)($_GET['dept'] ?? ($_GET['department'] ?? '')));
$clearance = trim((string)($_GET['clearance'] ?? ''));
$sortKey = trim((string)($_GET['sort'] ?? 'emp_id'));
$dir = strtolower(trim((string)($_GET['dir'] ?? 'asc'))) === 'desc' ? 'DESC' : 'ASC';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = min(100, max(1, (int)($_GET['per_page'] ?? 20)));
$offset = ($page - 1) * $perPage;

$allowedSort = [
    'emp_id'            => 'e.emp_id',
    'full_name'         => 'e.full_name',
    'department_code'   => 'e.department_code',
    'job_title'         => 'e.job_title',
    'clearance_level'   => 'e.clearance_level',
    'employment_status' => 'e.employment_status',
    'hire_date'         => 'e.hire_date',
    'email'             => 'e.email'
];
$sortCol = $allowedSort[$sortKey] ?? 'e.emp_id';

try {
    $where = ["1=1"];
    $params = [];

    if ($q !== '') {
        $where[] = "(e.full_name LIKE :q1 OR e.emp_id LIKE :q2 OR e.job_title LIKE :q3 OR e.email LIKE :q4)";
        $wildcard = "%{$q}%";
        $params[':q1'] = $wildcard;
        $params[':q2'] = $wildcard;
        $params[':q3'] = $wildcard;
        $params[':q4'] = $wildcard;
    }

    if ($dept !== '' && strtolower($dept) !== 'all') {
        $where[] = "e.department_code = :dept";
        $params[':dept'] = $dept;
    }

    if ($clearance !== '' && strtolower($clearance) !== 'all') {
        $lvl = strtoupper($clearance);
        if (is_numeric($lvl)) $lvl = 'L' . $lvl;
        $where[] = "e.clearance_level = :clr";
        $params[':clr'] = $lvl;
    }

    $whereSql = implode(' AND ', $where);

    // Count
    $cStmt = $pdo->prepare("SELECT COUNT(*) FROM employees e WHERE {$whereSql}");
    $cStmt->execute($params);
    $total = (int)$cStmt->fetchColumn();

    // Query rows
    $sql = "
        SELECT 
            e.emp_id,
            e.full_name,
            e.email,
            e.job_title,
            e.department_code,
            d.dept_name,
            e.clearance_level,
            e.employment_status,
            e.hire_date,
            '+7 (812) 555-0100' AS phone,
            NULL AS salary_rub
        FROM employees e
        LEFT JOIN departments d ON e.department_code = d.dept_code
        WHERE {$whereSql}
        ORDER BY {$sortCol} {$dir}
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mask salary unless authorized
    $canManage = hr_canManageHR();
    if (!$canManage) {
        foreach ($rows as &$r) {
            unset($r['salary_rub']);
        }
        unset($r);
    }

    Response::paginated($rows, $total, $page, $perPage, "Employee records loaded");
} catch (Exception $e) {
    error_log("Failed to list employees: " . $e->getMessage());
    Response::error("Failed to load employee records", 500);
}
