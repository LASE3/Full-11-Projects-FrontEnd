<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR Online Shop B2B - Core Database Service Controller (Class 2)
 * Handles catalog inventory, atomic order creation, quote RFQs, customer pricing, and order states.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/AuditLogger.php';
require_once __DIR__ . '/../includes/integration_bus.php';
require_once __DIR__ . '/../includes/enterprise_flows.php';

function shop_jsonReply(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

/**
 * Resolve current customer identifier from session or request.
 */
function shop_getCurrentCustomerId(): string
{
    if (!empty($_SESSION['cus_id'])) {
        return (string)$_SESSION['cus_id'];
    }
    if (!empty($_SESSION['vostok_user']['cus_id'])) {
        return (string)$_SESSION['vostok_user']['cus_id'];
    }
    $uid = (string)($_SESSION['vostok_user']['user_id'] ?? '');
    if (str_starts_with($uid, 'CUS-')) {
        $_SESSION['cus_id'] = $uid;
        return $uid;
    }
    $accId = $_SESSION['vostok_user']['account_id'] ?? null;
    if ($accId) {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("SELECT cus_id FROM customer_accounts WHERE account_id = ?");
            $stmt->execute([$accId]);
            $cid = $stmt->fetchColumn();
            if ($cid) {
                $_SESSION['cus_id'] = (string)$cid;
                return (string)$cid;
            }
        } catch (Throwable $e) {}
    }
    if (!empty($_GET['cus_id'])) {
        return (string)$_GET['cus_id'];
    }
    return '';
}

// ============================================================================
// 1. PRODUCTS & CATALOG
// ============================================================================

function shop_getProducts(?string $cusId = null, array $filters = []): array
{
    $pdo = getDbConnection();
    $cusId = $cusId ?: shop_getCurrentCustomerId();

    $where = [];
    $params = [':cid' => $cusId];

    if (!empty($filters['search'])) {
        $where[] = "(p.product_name LIKE :s OR p.prod_id LIKE :s OR p.description LIKE :s)";
        $params[':s'] = '%' . $filters['search'] . '%';
    }

    if (!empty($filters['model']) && $filters['model'] !== 'all') {
        $where[] = "p.billing_model = :model";
        $params[':model'] = $filters['model'];
    }

    $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

    $sql = "
        SELECT 
            p.prod_id,
            p.product_name,
            p.billing_model,
            p.description,
            COALESCE(cp.special_price, 2500.00) AS effective_price,
            CASE WHEN cp.special_price IS NOT NULL THEN 1 ELSE 0 END AS has_b2b_discount,
            COALESCE(pi.quantity_on_hand, 0) AS in_stock,
            COALESCE(pi.reorder_level, 10) AS reorder_level,
            COALESCE(pi.warehouse_location, 'Main Depot Shymkent') AS warehouse_location
        FROM products p
        LEFT JOIN product_inventory pi ON p.prod_id = pi.prod_id
        LEFT JOIN customer_pricing cp ON p.prod_id = cp.prod_id AND cp.cus_id = :cid
        {$whereSql}
        ORDER BY p.prod_id ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as &$p) {
        $stock = (int)$p['in_stock'];
        $reorder = (int)$p['reorder_level'];
        $p['stock_status'] = ($stock > $reorder) ? 'InStock' : (($stock > 0) ? 'LowStock' : 'OutOfStock');
        $p['effective_price'] = (float)$p['effective_price'];
    }

    return $products;
}

function shop_getProduct(string $prodId, ?string $cusId = null): ?array
{
    $pdo = getDbConnection();
    $cusId = $cusId ?: shop_getCurrentCustomerId();

    $stmt = $pdo->prepare("
        SELECT 
            p.*,
            COALESCE(cp.special_price, 2500.00) AS effective_price,
            CASE WHEN cp.special_price IS NOT NULL THEN 1 ELSE 0 END AS has_b2b_discount,
            COALESCE(pi.quantity_on_hand, 0) AS in_stock,
            COALESCE(pi.reorder_level, 10) AS reorder_level,
            COALESCE(pi.warehouse_location, 'Main Depot Shymkent') AS warehouse_location
        FROM products p
        LEFT JOIN product_inventory pi ON p.prod_id = pi.prod_id
        LEFT JOIN customer_pricing cp ON p.prod_id = cp.prod_id AND cp.cus_id = :cid
        WHERE p.prod_id = :pid
    ");
    $stmt->execute([':pid' => $prodId, ':cid' => $cusId]);
    $prod = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$prod) return null;

    $prod['in_stock'] = (int)$prod['in_stock'];
    $prod['effective_price'] = (float)$prod['effective_price'];
    return $prod;
}

function shop_createProduct(array $data): string
{
    $pdo = getDbConnection();
    $prodId = trim($data['prod_id'] ?? '');
    if (empty($prodId)) {
        $count = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
        $prodId = sprintf("PROD-%04d", $count + 1);
    }

    $stmt = $pdo->prepare("
        INSERT INTO products (prod_id, product_name, billing_model, description)
        VALUES (:id, :name, :model, :desc)
    ");
    $stmt->execute([
        ':id'    => $prodId,
        ':name'  => trim($data['product_name'] ?? 'Industrial Device'),
        ':model' => trim($data['billing_model'] ?? 'PerUnit'),
        ':desc'  => trim($data['description'] ?? '')
    ]);

    // Initialize inventory
    $invStmt = $pdo->prepare("
        INSERT INTO product_inventory (prod_id, warehouse_location, quantity_on_hand, reorder_level)
        VALUES (:id, :wh, :qty, :reorder)
        ON DUPLICATE KEY UPDATE quantity_on_hand = :qty
    ");
    $invStmt->execute([
        ':id'      => $prodId,
        ':wh'      => trim($data['warehouse_location'] ?? 'Main Depot Shymkent'),
        ':qty'     => (int)($data['quantity_on_hand'] ?? 25),
        ':reorder' => (int)($data['reorder_level'] ?? 10)
    ]);

    return $prodId;
}

function shop_updateInventory(string $prodId, int $qty, string $mode = 'set'): bool
{
    $pdo = getDbConnection();
    if ($mode === 'adjust') {
        $stmt = $pdo->prepare("
            UPDATE product_inventory 
            SET quantity_on_hand = GREATEST(0, quantity_on_hand + :qty)
            WHERE prod_id = :id
        ");
        return $stmt->execute([':qty' => $qty, ':id' => $prodId]);
    }

    $stmt = $pdo->prepare("UPDATE product_inventory SET quantity_on_hand = :qty WHERE prod_id = :id");
    return $stmt->execute([':qty' => max(0, $qty), ':id' => $prodId]);
}

// ============================================================================
// 2. ORDERS CRUD & STATE MACHINE
// ============================================================================

function shop_getOrders(?string $cusId = null, array $filters = []): array
{
    $pdo = getDbConnection();
    $where = [];
    $params = [];

    if ($cusId !== null && $cusId !== 'all') {
        $where[] = "o.cus_id = :cid";
        $params[':cid'] = $cusId;
    }

    if (!empty($filters['status']) && $filters['status'] !== 'all') {
        $where[] = "o.status = :status";
        $params[':status'] = $filters['status'];
    }

    $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

    $sql = "
        SELECT 
            o.*,
            c.company_name,
            c.primary_contact_name,
            c.sector,
            COUNT(oi.order_item_id) AS line_item_count,
            COALESCE(SUM(oi.quantity), 0) AS total_units
        FROM orders o
        JOIN customers c ON o.cus_id = c.cus_id
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        {$whereSql}
        GROUP BY o.order_id
        ORDER BY o.order_id DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $orders;
}

function shop_getOrderDetail(int $orderId, ?string $cusId = null): ?array
{
    $pdo = getDbConnection();
    $where = "WHERE o.order_id = :oid";
    $params = [':oid' => $orderId];

    if ($cusId !== null && $cusId !== 'all') {
        $where .= " AND o.cus_id = :cid";
        $params[':cid'] = $cusId;
    }

    $stmt = $pdo->prepare("
        SELECT o.*, c.company_name, c.primary_contact_name, c.primary_contact_email, c.sector
        FROM orders o
        JOIN customers c ON o.cus_id = c.cus_id
        {$where}
    ");
    $stmt->execute($params);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) return null;

    $itemStmt = $pdo->prepare("
        SELECT oi.*, p.product_name, p.billing_model, p.description
        FROM order_items oi
        JOIN products p ON oi.prod_id = p.prod_id
        WHERE oi.order_id = :oid
        ORDER BY oi.order_item_id ASC
    ");
    $itemStmt->execute([':oid' => $orderId]);
    $order['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

    return $order;
}

function shop_createOrder(string $cusId, array $items): array
{
    $pdo = getDbConnection();
    if (empty($items)) {
        throw new InvalidArgumentException("Order must contain at least one product item.");
    }

    $pdo->beginTransaction();
    try {
        $totalAmount = 0.0;
        $processed = [];

        foreach ($items as $item) {
            $prodId = trim($item['prod_id'] ?? $item['productId'] ?? '');
            $qty = (int)($item['quantity'] ?? $item['qty'] ?? 0);

            if ($prodId === '' || $qty <= 0) {
                throw new Exception("Invalid item line or zero quantity.");
            }

            // Lock inventory row
            $invStmt = $pdo->prepare("SELECT quantity_on_hand FROM product_inventory WHERE prod_id = :pid FOR UPDATE");
            $invStmt->execute([':pid' => $prodId]);
            $stock = $invStmt->fetchColumn();

            if ($stock === false || (int)$stock < $qty) {
                throw new Exception("Insufficient stock for [{$prodId}]. Available in warehouse: " . ($stock !== false ? $stock : 0));
            }

            // Fetch pricing (custom or product catalog; NO 2500.00 fallback allowed!)
            $pStmt = $pdo->prepare("SELECT special_price FROM customer_pricing WHERE cus_id = :cid AND prod_id = :pid");
            $pStmt->execute([':cid' => $cusId, ':pid' => $prodId]);
            $special = $pStmt->fetchColumn();

            if ($special !== false && (float)$special > 0.0) {
                $unitPrice = (float)$special;
            } else {
                $baseStmt = $pdo->prepare("SELECT price FROM products WHERE prod_id = ?");
                $baseStmt->execute([$prodId]);
                $basePrice = $baseStmt->fetchColumn();
                if ($basePrice !== false && $basePrice !== null && (float)$basePrice > 0.0) {
                    $unitPrice = (float)$basePrice;
                } else {
                    throw new Exception("Product {$prodId} has no established price in products or customer_pricing. Order rejected.");
                }
            }

            $totalAmount += ($unitPrice * $qty);

            $processed[] = [
                'prod_id'    => $prodId,
                'quantity'   => $qty,
                'unit_price' => $unitPrice
            ];

            // Decrement inventory
            $upd = $pdo->prepare("UPDATE product_inventory SET quantity_on_hand = quantity_on_hand - :qty WHERE prod_id = :pid");
            $upd->execute([':qty' => $qty, ':pid' => $prodId]);
        }

        // Create Order header
        $ordStmt = $pdo->prepare("
            INSERT INTO orders (cus_id, order_date, status, total_amount)
            VALUES (:cid, NOW(), 'Processing', :tot)
        ");
        $ordStmt->execute([
            ':cid' => $cusId,
            ':tot' => $totalAmount
        ]);
        $orderId = (int)$pdo->lastInsertId();

        // Create Order items
        $insItem = $pdo->prepare("
            INSERT INTO order_items (order_id, prod_id, quantity, unit_price)
            VALUES (:oid, :pid, :qty, :prc)
        ");
        foreach ($processed as $line) {
            $insItem->execute([
                ':oid' => $orderId,
                ':pid' => $line['prod_id'],
                ':qty' => $line['quantity'],
                ':prc' => $line['unit_price']
            ]);
        }

        $pdo->commit();

        // Centralized Flow D processing across systems:
        // Creates invoice, File Center doc, CRM activity, OPS task, customer notification, and emits all events
        return vp_process_order($pdo, $orderId);
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function shop_updateOrderStatus(int $orderId, string $newStatus): bool
{
    $pdo = getDbConnection();
    $allowed = ['Cart', 'Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
    if (!in_array($newStatus, $allowed, true)) {
        throw new InvalidArgumentException("Invalid order status: {$newStatus}");
    }

    $pdo->beginTransaction();
    try {
        $curStmt = $pdo->prepare("SELECT status FROM orders WHERE order_id = :id FOR UPDATE");
        $curStmt->execute([':id' => $orderId]);
        $oldStatus = $curStmt->fetchColumn();

        if ($oldStatus === false) {
            throw new Exception("Order #{$orderId} not found.");
        }

        // If transitioning TO Cancelled from active status, restore inventory
        if ($newStatus === 'Cancelled' && $oldStatus !== 'Cancelled') {
            $itemsStmt = $pdo->prepare("SELECT prod_id, quantity FROM order_items WHERE order_id = :oid");
            $itemsStmt->execute([':oid' => $orderId]);
            $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($items as $it) {
                $restock = $pdo->prepare("UPDATE product_inventory SET quantity_on_hand = quantity_on_hand + :qty WHERE prod_id = :pid");
                $restock->execute([':qty' => (int)$it['quantity'], ':pid' => $it['prod_id']]);
            }
        }

        $upd = $pdo->prepare("UPDATE orders SET status = :st WHERE order_id = :id");
        $res = $upd->execute([':st' => $newStatus, ':id' => $orderId]);

        $pdo->commit();

        try {
            vp_emit($pdo, 'SHP_TO_CUS', 'SHP', 'CUS', 'ORDER_STATUS_CHANGED', [
                'order_id'   => $orderId,
                'new_status' => $newStatus,
                'summary'    => "Order #{$orderId} changed to {$newStatus}",
                'endpoint'   => '/orders/status'
            ], 'SYSTEM', 200);
        } catch (Throwable $e) {
            error_log("Failed to emit SHP_TO_CUS: " . $e->getMessage());
        }
        return $res;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// ============================================================================
// 3. QUOTES & RFQ
// ============================================================================

function shop_getQuotes(?string $cusId = null): array
{
    $pdo = getDbConnection();
    $where = [];
    $params = [];

    if ($cusId !== null && $cusId !== 'all') {
        $where[] = "q.cus_id = :cid";
        $params[':cid'] = $cusId;
    }

    $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

    $stmt = $pdo->prepare("
        SELECT 
            q.*,
            c.company_name,
            p.product_name,
            p.billing_model,
            (q.quantity * q.unit_price) AS total_quote_value,
            e.full_name AS prepared_by_name
        FROM quotes q
        JOIN customers c ON q.cus_id = c.cus_id
        JOIN products p ON q.prod_id = p.prod_id
        LEFT JOIN employees e ON q.created_by_emp_id = e.emp_id
        {$whereSql}
        ORDER BY q.quote_id DESC
    ");
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function shop_createQuote(array $data): int
{
    $pdo = getDbConnection();
    $cusId = trim($data['cus_id'] ?? shop_getCurrentCustomerId());
    $prodId = trim($data['prod_id'] ?? '');
    $qty = (int)($data['quantity'] ?? 1);

    if (empty($prodId) || $qty <= 0) {
        throw new InvalidArgumentException("Product ID and valid quantity required.");
    }

    $unitPrice = (float)($data['unit_price'] ?? 0.0);
    if ($unitPrice <= 0) {
        $priceStmt = $pdo->prepare("SELECT COALESCE(special_price, 2500.00) FROM customer_pricing WHERE cus_id = :cid AND prod_id = :pid");
        $priceStmt->execute([':cid' => $cusId, ':pid' => $prodId]);
        $unitPrice = (float)($priceStmt->fetchColumn() ?: 2500.00);
    }

    $mgrStmt = $pdo->prepare("SELECT account_manager_emp_id FROM customers WHERE cus_id = ?");
    $mgrStmt->execute([$cusId]);
    $empId = $mgrStmt->fetchColumn();
    if (!$empId) {
        $empId = $pdo->query("SELECT emp_id FROM employees WHERE department_code = 'SAL' AND employment_status = 'Active' LIMIT 1")->fetchColumn() ?: null;
    }

    $stmt = $pdo->prepare("
        INSERT INTO quotes (cus_id, prod_id, quantity, unit_price, created_by_emp_id, created_at)
        VALUES (:cid, :pid, :qty, :prc, :eid, NOW())
    ");
    $stmt->execute([
        ':cid' => $cusId,
        ':pid' => $prodId,
        ':qty' => $qty,
        ':prc' => $unitPrice,
        ':eid' => $empId
    ]);

    return (int)$pdo->lastInsertId();
}

// ============================================================================
// 4. DASHBOARD METRICS
// ============================================================================

function shop_getDashboardMetrics(?string $cusId = null): array
{
    $pdo = getDbConnection();
    $cusId = $cusId ?: shop_getCurrentCustomerId();

    // Single query for order aggregate metrics
    $metricsStmt = $pdo->prepare("
        SELECT
            COUNT(*) AS total_orders,
            COALESCE(SUM(CASE WHEN status != 'Cancelled' THEN total_amount ELSE 0 END), 0) AS total_spent,
            COUNT(CASE WHEN status IN ('Processing', 'Pending') THEN 1 END) AS pending_shipments
        FROM orders WHERE cus_id = ?
    ");
    $metricsStmt->execute([$cusId]);
    $metrics = $metricsStmt->fetch(PDO::FETCH_ASSOC);

    $quotesStmt = $pdo->prepare("SELECT COUNT(*) FROM quotes WHERE cus_id = ?");
    $quotesStmt->execute([$cusId]);
    $activeQuotes = (int)$quotesStmt->fetchColumn();

    $catalogTotal = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

    return [
        'customer_id'       => $cusId,
        'total_orders'      => (int)($metrics['total_orders'] ?? 0),
        'total_spent'       => (float)($metrics['total_spent'] ?? 0.0),
        'pending_shipments' => (int)($metrics['pending_shipments'] ?? 0),
        'active_quotes'     => $activeQuotes,
        'catalog_total'     => $catalogTotal
    ];
}

// ============================================================================
// 5. AJAX ACTION ROUTER
// ============================================================================

$action = $_REQUEST['action'] ?? null;
if ($action !== null && (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_GET['action']) || isset($_POST['action']) || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json'))) {
    try {
        switch ($action) {
            case 'get_products':
                shop_jsonReply(['success' => true, 'data' => shop_getProducts(null, $_GET)]);
                break;

            case 'get_product':
                $pid = trim($_GET['id'] ?? $_GET['prod_id'] ?? '');
                $prod = shop_getProduct($pid);
                if (!$prod) shop_jsonReply(['success' => false, 'error' => 'Product not found'], 404);
                shop_jsonReply(['success' => true, 'data' => $prod]);
                break;

            case 'get_orders':
                shop_jsonReply(['success' => true, 'data' => shop_getOrders(shop_getCurrentCustomerId(), $_GET)]);
                break;

            case 'get_order_detail':
                $oid = (int)($_GET['id'] ?? $_GET['order_id'] ?? 0);
                $order = shop_getOrderDetail($oid);
                if (!$order) shop_jsonReply(['success' => false, 'error' => 'Order not found'], 404);
                shop_jsonReply(['success' => true, 'data' => $order]);
                break;

            case 'create_order':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $cid = trim($input['cus_id'] ?? shop_getCurrentCustomerId());
                $items = $input['items'] ?? [];
                $res = shop_createOrder($cid, $items);
                shop_jsonReply($res, 201);
                break;

            case 'update_order_status':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $oid = (int)($input['order_id'] ?? $_REQUEST['order_id'] ?? 0);
                $status = trim((string)($input['status'] ?? ''));
                shop_updateOrderStatus($oid, $status);
                shop_jsonReply(['success' => true, 'message' => "Order #{$oid} status changed to {$status}"]);
                break;

            case 'get_quotes':
                shop_jsonReply(['success' => true, 'data' => shop_getQuotes(shop_getCurrentCustomerId())]);
                break;

            case 'create_quote':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $qid = shop_createQuote($input);
                shop_jsonReply(['success' => true, 'quote_id' => $qid, 'message' => 'Quote request submitted']);
                break;

            case 'get_dashboard':
                shop_jsonReply(['success' => true, 'data' => shop_getDashboardMetrics()]);
                break;
        }
    } catch (Throwable $e) {
        shop_jsonReply(['success' => false, 'error' => $e->getMessage()], 400);
    }
}
