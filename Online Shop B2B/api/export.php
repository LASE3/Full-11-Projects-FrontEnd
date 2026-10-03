<?php
declare(strict_types=1);

/**
 * Class 2: Online Shop B2B - Live Database CSV & JSON Data Export Engine
 * Location: Online Shop B2B/api/export.php
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

$type   = strtolower(trim((string)($_GET['type'] ?? 'orders')));
$format = strtolower(trim((string)($_GET['format'] ?? 'csv')));
$timestamp = date('Y-m-d_His');
$filename  = "vostok_shop_{$type}_{$timestamp}";

try {
    $rows = [];

    switch ($type) {
        case 'orders':
            $stmt = $pdo->prepare("
                SELECT 
                    o.order_id AS 'Order ID',
                    o.cus_id AS 'Customer ID',
                    c.company_name AS 'Enterprise Customer',
                    o.order_date AS 'Order Date',
                    o.status AS 'Order Status',
                    o.total_amount AS 'Total Amount (EUR)',
                    COUNT(oi.order_item_id) AS 'Line Items Count',
                    COALESCE(SUM(oi.quantity), 0) AS 'Total Units'
                FROM orders o
                LEFT JOIN customers c ON o.cus_id = c.cus_id
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                WHERE o.cus_id = :cid
                GROUP BY o.order_id
                ORDER BY o.order_id DESC
            ");
            $stmt->execute([':cid' => $cusId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filename = "orders_export_{$cusId}_{$timestamp}";
            break;

        case 'quotes':
        case 'rfq':
            $stmt = $pdo->prepare("
                SELECT 
                    q.quote_id AS 'Quote ID',
                    COALESCE(q.quote_ref, CONCAT('QUO-', q.quote_id)) AS 'Quote Reference',
                    q.cus_id AS 'Customer ID',
                    c.company_name AS 'Enterprise Customer',
                    q.prod_id AS 'Product ID',
                    p.product_name AS 'Equipment Name',
                    q.quantity AS 'Quantity',
                    q.unit_price AS 'Unit Price (EUR)',
                    COALESCE(q.total_amount, q.quantity * q.unit_price) AS 'Total Estimated Value (EUR)',
                    COALESCE(q.status, 'Under Review') AS 'Status',
                    q.valid_until AS 'Target Deadline',
                    q.created_at AS 'Date Requested',
                    e.full_name AS 'Commercial Manager'
                FROM quotes q
                JOIN customers c ON q.cus_id = c.cus_id
                JOIN products p ON q.prod_id = p.prod_id
                LEFT JOIN employees e ON q.created_by_emp_id = e.emp_id
                WHERE q.cus_id = :cid
                ORDER BY q.quote_id DESC
            ");
            $stmt->execute([':cid' => $cusId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filename = "quotes_export_{$cusId}_{$timestamp}";
            break;

        case 'products':
        case 'catalog':
            $stmt = $pdo->prepare("
                SELECT 
                    p.prod_id AS 'Product SKU',
                    p.product_name AS 'Product Name',
                    p.billing_model AS 'Billing Model',
                    p.description AS 'Technical Description',
                    COALESCE(cp.special_price, 2500.00) AS 'Enterprise Price (EUR)',
                    COALESCE(pi.quantity_on_hand, 0) AS 'In Stock Quantity',
                    COALESCE(pi.warehouse_location, 'Main Depot Shymkent') AS 'Warehouse Location'
                FROM products p
                LEFT JOIN product_inventory pi ON p.prod_id = pi.prod_id
                LEFT JOIN customer_pricing cp ON p.prod_id = cp.prod_id AND cp.cus_id = :cid
                ORDER BY p.prod_id ASC
            ");
            $stmt->execute([':cid' => $cusId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filename = "catalog_export_{$timestamp}";
            break;

        default:
            Response::error("Unknown export dataset type: '{$type}'.", 400);
    }

    if (empty($rows)) {
        // Return empty CSV or JSON
        $rows = [['Message' => "No records found for {$type}"]];
    }

    if ($format === 'json') {
        header('Content-Type: application/json; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}.json\"");
        header('Cache-Control: no-cache, no-store, must-revalidate');
        echo json_encode([
            'export_type'  => $type,
            'customer_id'  => $cusId,
            'record_count' => count($rows),
            'generated_at' => date('Y-m-d H:i:s'),
            'records'      => $rows
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Default: CSV export
    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"{$filename}.csv\"");
    header('Cache-Control: no-cache, no-store, must-revalidate');

    // UTF-8 BOM for Excel
    echo "\xEF\xBB\xBF";

    $out = fopen('php://output', 'w');
    $headers = array_keys($rows[0]);
    fputcsv($out, $headers, ',', '"', '\\');

    foreach ($rows as $row) {
        fputcsv($out, array_values($row), ',', '"', '\\');
    }
    fclose($out);
    exit;

} catch (Exception $e) {
    Response::error("Export failed: " . $e->getMessage(), 500);
}
