<?php
/**
 * Class 4: Employee Intranet - Staff Directory API
 * Location: api/v1/intranet/directory.php
 * Methods: GET
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/I18n.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$lang = $_GET['lang'] ?? 'en';
$deptFilter = trim($_GET['dept'] ?? '');
$searchQuery = trim($_GET['query'] ?? '');

try {
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
        WHERE e.employment_status = 'Active'
    ";

    $params = [];

    if (!empty($deptFilter)) {
        $sql .= " AND e.department_code = :dept";
        $params[':dept'] = $deptFilter;
    }

    if (!empty($searchQuery)) {
        $sql .= " AND (e.full_name LIKE :q OR e.job_title LIKE :q OR e.emp_id LIKE :q OR e.email LIKE :q)";
        $params[':q'] = "%{$searchQuery}%";
    }

    $sql .= " ORDER BY d.dept_code ASC, e.clearance_level DESC, e.full_name ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $directory = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($directory as &$emp) {
        $emp['department_display'] = I18n::translate($emp['department_code'], $lang);
        $emp['status_display']     = I18n::translate($emp['employment_status'], $lang);
    }

    Response::success($directory, "Staff directory loaded", 200, ['total' => count($directory)]);
} catch (Exception $e) {
    Response::error("Failed to load directory: " . $e->getMessage(), 500);
}
