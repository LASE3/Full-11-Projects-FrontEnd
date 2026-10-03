<?php
declare(strict_types=1);

/**
 * Class 2: Online Shop B2B - Real-Time Dashboard Stats & Menu Badges API
 * Location: Online Shop B2B/api/stats.php
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('SHP', []);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$cusId = $_SESSION['cus_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? ($_GET['cus_id'] ?? 'CUS-1001'));
if (!$cusId && !empty($_SESSION['vostok_user']['cus_id'])) {
    $cusId = $_SESSION['vostok_user']['cus_id'];
}
if (!$cusId) {
    $cusId = 'CUS-1001';
}

try {
    // 1. Catalog count
    $catalogCount = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

    // 2. Customer quotes count
    $stmtQ = $pdo->prepare("SELECT COUNT(*) FROM quotes WHERE cus_id = ?");
    $stmtQ->execute([$cusId]);
    $quotesCount = (int)$stmtQ->fetchColumn();

    // 3. Customer orders count & financial metrics
    $stmtO = $pdo->prepare("
        SELECT 
            COUNT(*) AS total_orders,
            COALESCE(SUM(CASE WHEN status != 'Cancelled' THEN total_amount ELSE 0 END), 0) AS total_spent,
            COUNT(CASE WHEN status IN ('Processing', 'Shipped', 'Pending') THEN 1 END) AS active_shipments
        FROM orders 
        WHERE cus_id = ?
    ");
    $stmtO->execute([$cusId]);
    $orderMetrics = $stmtO->fetch(PDO::FETCH_ASSOC);

    // 4. Customer projects count
    $stmtP = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE cus_id = ?");
    $stmtP->execute([$cusId]);
    $projectsCount = (int)$stmtP->fetchColumn();

    // 5. Customer Profile Info
    $stmtC = $pdo->prepare("
        SELECT c.*, e.full_name AS manager_name, e.email AS manager_email
        FROM customers c
        LEFT JOIN employees e ON c.account_manager_emp_id = e.emp_id
        WHERE c.cus_id = ?
    ");
    $stmtC->execute([$cusId]);
    $customer = $stmtC->fetch(PDO::FETCH_ASSOC) ?: [
        'cus_id' => $cusId,
        'company_name' => 'Enterprise Client',
        'primary_contact_name' => 'Authorized Buyer'
    ];

    $payload = [
        'cus_id'           => $cusId,
        'catalog_count'    => $catalogCount,
        'quotes_count'     => $quotesCount,
        'orders_count'     => (int)($orderMetrics['total_orders'] ?? 0),
        'projects_count'   => $projectsCount,
        'active_shipments' => (int)($orderMetrics['active_shipments'] ?? 0),
        'total_spent'      => (float)($orderMetrics['total_spent'] ?? 0),
        'customer'         => $customer,
        'user'             => $_SESSION['vostok_user'] ?? [
            'full_name' => $customer['primary_contact_name'] ?? 'Alexey R. Danilov',
            'role_name' => 'Procurement Director'
        ]
    ];

    Response::success($payload, "Dashboard statistics and badge counters loaded");
} catch (Exception $e) {
    Response::error("Failed to load statistics: " . $e->getMessage(), 500);
}
