<?php

/**
 * Class 2: Online Shop B2B - Orders API
 * Location: Online Shop B2B/api/orders.php
 * Methods: GET, POST, PUT, DELETE
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$user = vp_api_guard('SHP', []);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/I18n.php';
require_once __DIR__ . '/helpers/AuditLogger.php';
require_once __DIR__ . '/../../includes/enterprise_flows.php';

$pdo = getDbConnection();
$isSA = isSuperAdmin($user);

// Derive customer ID or allow SuperAdmin override
if ($isSA) {
    $cusId = $_GET['cus_id'] ?? $_POST['cus_id'] ?? ($_SESSION['cus_id'] ?? null);
} else {
    if (($user['account_type'] ?? '') === 'Customer') {
        $cusId = $user['cus_id'] ?? $user['user_id'];
    } else {
        $cusId = $_SESSION['cus_id'] ?? null;
    }
    if (!$cusId) {
        Response::error("Customer session required. Please log in.", 401);
    }
}

$lang   = $_GET['lang'] ?? 'en';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Handle GET: Retrieve orders
if ($method === 'GET') {
    try {
        $orderId = $_GET['order_id'] ?? null;
        if ($orderId) {
            // Check ownership
            $chkStmt = $pdo->prepare("SELECT cus_id FROM orders WHERE order_id = :oid");
            $chkStmt->execute([':oid' => $orderId]);
            $owner = $chkStmt->fetchColumn();

            if ($owner === false) {
                Response::error("Order not found.", 404);
            }

            if (!$isSA && ($user['account_type'] ?? '') === 'Customer' && $owner !== $cusId) {
                Response::error("Forbidden: You do not have permission to view this order.", 403);
            }

            $stmt = $pdo->prepare("
                SELECT o.*, c.company_name, c.primary_contact_name
                FROM orders o
                JOIN customers c ON o.cus_id = c.cus_id
                WHERE o.order_id = :oid
            ");
            $stmt->execute([':oid' => $orderId]);
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
            // All orders for this customer (or all orders for SuperAdmin without cusId filter)
            if ($isSA && empty($cusId)) {
                $stmt = $pdo->query("
                    SELECT 
                        o.order_id,
                        o.cus_id,
                        c.company_name,
                        o.order_date,
                        o.status,
                        o.total_amount,
                        COUNT(oi.order_item_id) AS total_items
                    FROM orders o
                    LEFT JOIN customers c ON o.cus_id = c.cus_id
                    LEFT JOIN order_items oi ON o.order_id = oi.order_id
                    GROUP BY o.order_id
                    ORDER BY o.order_date DESC
                ");
            } else {
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
            }
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
    vp_enforce_csrf([]);
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true) ?: $_POST;

    $targetCusId = $cusId ?: ($input['cus_id'] ?? 'CUS-1001');

    $items = $input['items'] ?? [];
    if (empty($items) || !is_array($items)) {
        Response::error("Order must contain at least one product item.", 422);
    }

    try {
        $pdo->beginTransaction();

        $totalOrderAmount = 0.0;
        $processedLines = [];

        foreach ($items as $item) {
            $prodId = trim($item['prod_id'] ?? $item['product_id'] ?? $item['productId'] ?? '');
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

            // Check pricing in customer_pricing first, then products table
            $priceStmt = $pdo->prepare("SELECT special_price FROM customer_pricing WHERE cus_id = :cid AND prod_id = :pid");
            $priceStmt->execute([':cid' => $targetCusId, ':pid' => $prodId]);
            $customPrice = $priceStmt->fetchColumn();

            if ($customPrice !== false && $customPrice !== null) {
                $unitPrice = (float)$customPrice;
            } else {
                $basePriceStmt = $pdo->prepare("SELECT price, product_name FROM products WHERE prod_id = :pid");
                $basePriceStmt->execute([':pid' => $prodId]);
                $baseRow = $basePriceStmt->fetch(PDO::FETCH_ASSOC);
                if (!$baseRow) {
                    throw new Exception("Product price not configured for item: " . htmlspecialchars($prodId));
                }
                $unitPrice = (float)$baseRow['price'];
                $productName = $baseRow['product_name'];
            }

            $lineTotal = $unitPrice * $qty;
            $totalOrderAmount += $lineTotal;

            $processedLines[] = [
                'prod_id'      => $prodId,
                'product_name' => $productName ?? null,
                'quantity'     => $qty,
                'unit_price'   => $unitPrice
            ];

            // Decrement inventory
            $updInv = $pdo->prepare("UPDATE product_inventory SET quantity_on_hand = quantity_on_hand - :qty WHERE prod_id = :pid");
            $updInv->execute([':qty' => $qty, ':pid' => $prodId]);
        }

        // Insert Order Header
        $orderStmt = $pdo->prepare("INSERT INTO orders (cus_id, order_date, status, total_amount) VALUES (:cid, NOW(), 'Processing', :tot)");
        $orderStmt->execute([':cid' => $targetCusId, ':tot' => $totalOrderAmount]);
        $newOrderId = (int)$pdo->lastInsertId();

        // Insert Order Items with snapshot product_name
        $itemInsert = $pdo->prepare("INSERT INTO order_items (order_id, prod_id, product_name, quantity, unit_price) VALUES (:oid, :pid, :pname, :qty, :prc)");
        foreach ($processedLines as $line) {
            $itemInsert->execute([
                ':oid'   => $newOrderId,
                ':pid'   => $line['prod_id'],
                ':pname' => $line['product_name'],
                ':qty'   => $line['quantity'],
                ':prc'   => $line['unit_price']
            ]);
        }

        // Audit Log
        AuditLogger::logAction(
            $user['emp_id'] ?? null,
            $targetCusId,
            'Online Shop B2B',
            'SHP',
            'PLACE_B2B_PURCHASE_ORDER',
            'orders',
            (string)$newOrderId,
            ['total_amount' => $totalOrderAmount, 'items_count' => count($processedLines)],
            'SUCCESS'
        );

        // Call common order processing function if defined (Phase 3 Flow D)
        if (function_exists('vp_process_order')) {
            vp_process_order($pdo, $newOrderId);
        }

        $pdo->commit();

        Response::success([
            'order_id'       => $newOrderId,
            'total_amount'   => $totalOrderAmount,
            'status'         => 'Processing',
            'status_display' => I18n::translate('Processing', $lang)
        ], "Order successfully created and inventory committed.", 201);
    } catch (Exception $e) {
        $pdo->rollBack();
        Response::error("Failed to commit order: " . $e->getMessage(), 400);
    }
}

// Handle PUT: Update order (SuperAdmin override)
if ($method === 'PUT') {
    vp_enforce_csrf([]);
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true) ?: $_POST;
    $orderId = (int)($input['order_id'] ?? $_GET['order_id'] ?? 0);
    $newStatus = trim((string)($input['status'] ?? ''));

    if (!$orderId || !$newStatus) {
        Response::error("order_id and status required", 422);
    }

    $chk = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
    $chk->execute([$orderId]);
    $oldOrder = $chk->fetch(PDO::FETCH_ASSOC);
    if (!$oldOrder) Response::error("Order not found", 404);

    if (!$isSA && ($user['account_type'] ?? '') === 'Customer') {
        Response::error("Forbidden", 403);
    }

    $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?")->execute([$newStatus, $orderId]);

    AuditLogger::logAction(
        $user['emp_id'] ?? 'EMP-0001',
        $oldOrder['cus_id'],
        'Online Shop B2B',
        'SHP',
        'ORDER_STATUS_UPDATED',
        'orders',
        (string)$orderId,
        ['status' => $newStatus],
        'SUCCESS',
        $oldOrder
    );

    Response::success(['order_id' => $orderId, 'status' => $newStatus], "Order updated");
}

// Handle DELETE: Cancel order (SuperAdmin override)
if ($method === 'DELETE') {
    vp_enforce_csrf([]);
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true) ?: [];
    $orderId = (int)($input['order_id'] ?? $_GET['order_id'] ?? 0);
    $reason = trim((string)($input['reason'] ?? 'Cancelled by Admin'));

    if (!$orderId) Response::error("order_id required", 422);

    $chk = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
    $chk->execute([$orderId]);
    $oldOrder = $chk->fetch(PDO::FETCH_ASSOC);
    if (!$oldOrder) Response::error("Order not found", 404);

    if (!$isSA && ($user['account_type'] ?? '') === 'Customer' && $oldOrder['cus_id'] !== $cusId) {
        Response::error("Forbidden", 403);
    }

    $pdo->beginTransaction();
    $pdo->prepare("UPDATE orders SET status = 'Cancelled' WHERE order_id = ?")->execute([$orderId]);

    // Restore inventory
    $items = $pdo->prepare("SELECT prod_id, quantity FROM order_items WHERE order_id = ?");
    $items->execute([$orderId]);
    $restoreStmt = $pdo->prepare("UPDATE product_inventory SET quantity_on_hand = quantity_on_hand + ? WHERE prod_id = ?");
    foreach ($items->fetchAll(PDO::FETCH_ASSOC) as $it) {
        $restoreStmt->execute([$it['quantity'], $it['prod_id']]);
    }
    $pdo->commit();

    AuditLogger::logAction(
        $user['emp_id'] ?? 'EMP-0001',
        $oldOrder['cus_id'],
        'Online Shop B2B',
        'SHP',
        'ORDER_CANCELLED',
        'orders',
        (string)$orderId,
        ['status' => 'Cancelled', 'reason' => $reason],
        'SUCCESS',
        $oldOrder
    );

    Response::success(['order_id' => $orderId, 'status' => 'Cancelled'], "Order successfully cancelled and inventory restored.");
}
