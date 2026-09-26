<?php
/**
 * Class 2: Online Shop B2B - Orders API
 * Location: api/v1/shop/orders.php
 * Methods: GET, POST
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/I18n.php';
require_once __DIR__ . '/../../helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$cusId = $_SESSION['cus_id'] ?? ($_GET['cus_id'] ?? null);
$lang  = $_GET['lang'] ?? 'en';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Handle GET: Retrieve orders
if ($method === 'GET') {
    if (!$cusId) {
        Response::error("Customer authentication or cus_id parameter required.", 401);
    }

    try {
        $orderId = $_GET['order_id'] ?? null;
        if ($orderId) {
            // Specific order with item lines
            $stmt = $pdo->prepare("
                SELECT o.*, c.company_name, c.primary_contact_name
                FROM orders o
                JOIN customers c ON o.cus_id = c.cus_id
                WHERE o.order_id = :oid AND o.cus_id = :cid
            ");
            $stmt->execute([':oid' => $orderId, ':cid' => $cusId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                Response::error("Order not found.", 404);
            }

            $order['status_display'] = I18n::translate($order['status'], $lang);

            $itemStmt = $pdo->prepare("
                SELECT oi.*, p.product_name, p.billing_model
                FROM order_items oi
                JOIN products p ON oi.prod_id = p.prod_id
                WHERE oi.order_id = :oid
            ");
            $itemStmt->execute([':oid' => $orderId]);
            $order['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

            Response::success($order, "Order details retrieved");
        } else {
            // All orders for this customer
            $stmt = $pdo->prepare("
                SELECT 
                    o.order_id,
                    o.order_date,
                    o.status,
                    o.total_amount,
                    COUNT(oi.order_item_id) AS total_items
                FROM orders o
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                WHERE o.cus_id = :cid
                GROUP BY o.order_id
                ORDER BY o.order_date DESC
            ");
            $stmt->execute([':cid' => $cusId]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($orders as &$ord) {
                $ord['status_display'] = I18n::translate($ord['status'], $lang);
            }

            Response::success($orders, "Orders retrieved successfully");
        }
    } catch (Exception $e) {
        Response::error("Failed to load orders: " . $e->getMessage(), 500);
    }
}

// Handle POST: Create new order
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    $targetCusId = $cusId ?: ($input['cus_id'] ?? null);
    if (!$targetCusId) {
        Response::error("Valid cus_id required to place an order.", 400);
    }

    $items = $input['items'] ?? [];
    if (empty($items) || !is_array($items)) {
        Response::error("Order must contain at least one product item.", 422);
    }

    try {
        $pdo->beginTransaction();

        $totalOrderAmount = 0.0;
        $processedLines = [];

        foreach ($items as $item) {
            $prodId = trim($item['prod_id'] ?? '');
            $qty = (int)($item['quantity'] ?? 0);

            if (empty($prodId) || $qty <= 0) {
                throw new Exception("Invalid product or quantity for item: " . htmlspecialchars($prodId));
            }

            // Check stock with FOR UPDATE lock
            $invStmt = $pdo->prepare("SELECT quantity_on_hand FROM product_inventory WHERE prod_id = :pid FOR UPDATE");
            $invStmt->execute([':pid' => $prodId]);
            $stock = $invStmt->fetchColumn();

            if ($stock === false || $stock < $qty) {
                throw new Exception("Insufficient stock for product ID: {$prodId}. Available: " . ($stock !== false ? $stock : 0));
            }

            // Check pricing (custom or default 2500.00)
            $priceStmt = $pdo->prepare("SELECT special_price FROM customer_pricing WHERE cus_id = :cid AND prod_id = :pid");
            $priceStmt->execute([':cid' => $targetCusId, ':pid' => $prodId]);
            $customPrice = $priceStmt->fetchColumn();

            $unitPrice = $customPrice !== false ? (float)$customPrice : 2500.00;
            $lineTotal = $unitPrice * $qty;
            $totalOrderAmount += $lineTotal;

            $processedLines[] = [
                'prod_id'    => $prodId,
                'quantity'   => $qty,
                'unit_price' => $unitPrice
            ];

            // Decrement inventory
            $updInv = $pdo->prepare("UPDATE product_inventory SET quantity_on_hand = quantity_on_hand - :qty WHERE prod_id = :pid");
            $updInv->execute([':qty' => $qty, ':pid' => $prodId]);
        }

        // Insert Order Header
        $orderStmt = $pdo->prepare("INSERT INTO orders (cus_id, order_date, status, total_amount) VALUES (:cid, NOW(), 'Processing', :tot)");
        $orderStmt->execute([':cid' => $targetCusId, ':tot' => $totalOrderAmount]);
        $newOrderId = (int)$pdo->lastInsertId();

        // Insert Order Items
        $itemInsert = $pdo->prepare("INSERT INTO order_items (order_id, prod_id, quantity, unit_price) VALUES (:oid, :pid, :qty, :prc)");
        foreach ($processedLines as $line) {
            $itemInsert->execute([
                ':oid' => $newOrderId,
                ':pid' => $line['prod_id'],
                ':qty' => $line['quantity'],
                ':prc' => $line['unit_price']
            ]);
        }

        // Audit Log
        AuditLogger::logAction(
            null, $targetCusId, 'Online Shop B2B', 'SHP',
            'PLACE_B2B_PURCHASE_ORDER', 'orders', (string)$newOrderId,
            ['total_amount' => $totalOrderAmount, 'items_count' => count($processedLines)], 'SUCCESS'
        );

        $pdo->commit();

        Response::success([
            'order_id'     => $newOrderId,
            'total_amount' => $totalOrderAmount,
            'status'       => 'Processing',
            'status_display' => I18n::translate('Processing', $lang)
        ], "Order successfully created and inventory committed.", 201);

    } catch (Exception $e) {
        $pdo->rollBack();
        Response::error("Failed to commit order: " . $e->getMessage(), 400);
    }
}
