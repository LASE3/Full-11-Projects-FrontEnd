<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Corporate Web Platform — Public Product Catalog API (WEB_TO_SHP)
 * Public GET endpoint that queries active product catalog from the Online Shop
 * and records the integration event via vp_emit.
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/integration_bus.php';
require_once __DIR__ . '/../../includes/AuditLogger.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = getDbConnection();

    // Fetch active public products
    $stmt = $pdo->query("
        SELECT prod_id, product_name, billing_model, price, description, is_active 
        FROM products 
        WHERE is_active = 1 
        ORDER BY prod_id ASC
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Emit real cross-system integration event: WEB -> SHP
    vp_emit($pdo, 'WEB_TO_SHP', 'WEB', 'SHP', 'PUBLIC_CATALOG_QUERY', [
        'catalog_source' => 'Online Shop B2B Storefront',
        'product_count'  => count($products),
        'timestamp'      => date('c')
    ], 'PUBLIC-WEB-VISITOR');

    echo json_encode([
        'success' => true,
        'count'   => count($products),
        'data'    => $products
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Catalog service temporarily unavailable: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
