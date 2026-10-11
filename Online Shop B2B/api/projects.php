<?php
declare(strict_types=1);

/**
 * Class 2: Online Shop B2B - Projects API
 * Location: Online Shop B2B/api/projects.php
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('SHP', []);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$isCustomer = (($_vp_user['account_type'] ?? '') === 'Customer');
if ($isCustomer) {
    $cusId = (string)($_vp_user['cus_id'] ?? ($_vp_user['user_id'] ?? ''));
} else {
    $canImpersonate = hasEmployeePermission($pdo, $_vp_user, 'CUSTOMER_IMPERSONATE');
    $cusId = $canImpersonate ? (string)($_SESSION['impersonate_cus_id'] ?? '') : '';
}
if (!$cusId) {
    Response::error("Customer session required.", 401);
}


try {
    $stmt = $pdo->prepare("
        SELECT 
            p.prj_id,
            p.project_name,
            p.cus_id,
            p.budget,
            p.currency,
            p.status,
            p.start_date,
            p.end_date,
            p.progress_percent,
            p.facility_location,
            p.scope_summary,
            e.full_name AS project_manager_name
        FROM projects p
        LEFT JOIN employees e ON p.project_manager_emp_id = e.emp_id
        WHERE p.cus_id = :cid
        ORDER BY p.prj_id ASC
    ");
    $stmt->execute([':cid' => $cusId]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    Response::success($projects, "Customer projects loaded successfully", 200, ['total' => count($projects)]);
} catch (Exception $e) {
    error_log("Failed to load projects: " . $e->getMessage());
        Response::error("Failed to load projects. Please try again later.", 500);
}
