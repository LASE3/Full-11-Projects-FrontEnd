<?php

/**
 * Class 2: Online Shop B2B - Products API
 * Location: api/v1/shop/products.php
 * Methods: GET
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/I18n.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$cusId = $_SESSION['cus_id'] ?? ($_GET['cus_id'] ?? null);
$lang  = $_GET['lang'] ?? 'en';

try {
    $stmt = $pdo->prepare("
        SELECT 
            p.prod_id,
            p.product_name,
            p.billing_model,
            p.description,
            COALESCE(cp.special_price, 2500.00) AS effective_price,
            CASE WHEN cp.special_price IS NOT NULL THEN 1 ELSE 0 END AS has_b2b_discount,
            COALESCE(pi.quantity_on_hand, 0) AS in_stock,
            COALESCE(pi.reorder_level, 15) AS reorder_level,
            COALESCE(pi.warehouse_location, 'Main Depot') AS warehouse_location
        FROM products p
        LEFT JOIN product_inventory pi ON p.prod_id = pi.prod_id
        LEFT JOIN customer_pricing cp ON p.prod_id = cp.prod_id AND cp.cus_id = :cus_id
        ORDER BY p.prod_id ASC
    ");
    $stmt->execute([':cus_id' => $cusId]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as &$item) {
        $item['billing_model_display'] = I18n::translate($item['billing_model'], $lang);
        $item['stock_status'] = ($item['in_stock'] > $item['reorder_level']) ? 'InStock' : (($item['in_stock'] > 0) ? 'LowStock' : 'OutOfStock');
        $item['stock_status_display'] = I18n::translate($item['stock_status'], $lang);
    }

    Response::success($products, "B2B Product catalog retrieved successfully", 200, ['total' => count($products)]);
} catch (Exception $e) {
    Response::error("Failed to load catalog: " . $e->getMessage(), 500);
}
