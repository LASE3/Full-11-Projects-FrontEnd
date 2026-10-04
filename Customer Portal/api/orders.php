<?php
declare(strict_types=1);

/**
 * Customer Portal - Orders Management API
 * Location: Customer Portal/api/orders.php
 * Methods: GET, POST, PUT/PATCH, DELETE
 */

require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('CUS', ['customer' => true]);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/I18n.php';
require_once __DIR__ . '/helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$isSA = isSuperAdmin($_vp_user);
if ($isSA) {
    $cusId = $_GET['cus_id'] ?? ($_SESSION['cus_id'] ?? null);
} else {
    $cusId = $_SESSION['cus_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? ($_GET['cus_id'] ?? null));
    if (empty($cusId)) {
        $firstCus = $pdo->query("SELECT cus_id FROM customers WHERE status = 'Active' ORDER BY cus_id ASC LIMIT 1")->fetchColumn();
        $cusId = $firstCus ?: 'CUS-1001';
    }
}
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    if ($method === 'GET') {
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if ($orderId) {
            $chkStmt = $pdo->prepare("SELECT cus_id FROM orders WHERE order_id = :oid");
            $chkStmt->execute([':oid' => $orderId]);
            $owner = $chkStmt->fetchColumn();

            if ($owner === false) {
                Response::error("Order #{$orderId} not found.", 404);
            }

            if (!$isSA && $owner !== $cusId) {
                Response::error("Forbidden: You do not have permission to view this order.", 403);
            }

            $stmt = $pdo->prepare("
                SELECT 
                    o.*,
                    c.company_name AS facility_name,
                    c.primary_contact_name,
                    c.primary_contact_email
                FROM orders o
                LEFT JOIN customers c ON o.cus_id = c.cus_id
                WHERE o.order_id = :oid
            ");
            $stmt->execute([':oid' => $orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                Response::error("Order #{$orderId} not found or unauthorized.", 404);
            }

            // Fetch order line items
            $itemsStmt = $pdo->prepare("
                SELECT 
                    oi.*,
                    p.product_name,
                    p.sku,
                    p.category
                FROM order_items oi
                LEFT JOIN products p ON oi.prod_id = p.prod_id
                WHERE oi.order_id = :oid
            ");
            $itemsStmt->execute([':oid' => $orderId]);
            $order['items'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            Response::success($order, "Order details loaded");
        } else {
            $whereClause = "";
            $params = [];
            if ($cusId) {
                $whereClause = "WHERE o.cus_id = :cid";
                $params[':cid'] = $cusId;
            }
            $stmt = $pdo->prepare("
                SELECT 
                    o.order_id,
                    o.cus_id,
                    o.order_date,
                    o.status,
                    o.total_amount,
                    COALESCE(GROUP_CONCAT(p.product_name SEPARATOR ', '), 'Industrial Instrumentation Order') AS equipment_summary,
                    c.company_name AS facility_name,
                    COUNT(oi.order_item_id) AS total_items
                FROM orders o
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                LEFT JOIN products p ON oi.prod_id = p.prod_id
                LEFT JOIN customers c ON o.cus_id = c.cus_id
                {$whereClause}
                GROUP BY o.order_id
                ORDER BY o.order_date DESC
            ");
            $stmt->execute($params);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            Response::success($orders, "Customer orders ledger loaded");
        }
    }

    if ($method === 'POST') {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?: $_POST;

        $totalAmount = (float)($data['total_amount'] ?? 0);
        $status = trim($data['status'] ?? 'Processing');
        $items = $data['items'] ?? [];

        if ($totalAmount <= 0 && !empty($items)) {
            foreach ($items as $item) {
                $qty = (int)($item['quantity'] ?? 1);
                $price = (float)($item['unit_price'] ?? 0);
                $totalAmount += $qty * $price;
            }
        }
        if ($totalAmount <= 0) {
            $totalAmount = (float)($data['estimated_budget'] ?? 15000.00);
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO orders (cus_id, order_date, status, total_amount)
            VALUES (:cid, NOW(), :status, :total)
        ");
        $stmt->execute([
            ':cid'    => $cusId,
            ':status' => $status,
            ':total'  => $totalAmount
        ]);
        $newOrderId = (int)$pdo->lastInsertId();

        // Fetch a valid product ID
        $validProd = $pdo->query("SELECT prod_id FROM products ORDER BY prod_id ASC LIMIT 1")->fetchColumn() ?: 'PROD-1001';

        // Add line items if provided
        if (!empty($items) && is_array($items)) {
            $itemStmt = $pdo->prepare("
                INSERT INTO order_items (order_id, prod_id, quantity, unit_price)
                VALUES (:oid, :pid, :qty, :price)
            ");
            foreach ($items as $it) {
                $pid = !empty($it['prod_id']) ? $it['prod_id'] : $validProd;
                $chkP = $pdo->prepare("SELECT prod_id FROM products WHERE prod_id = ?");
                $chkP->execute([$pid]);
                if (!$chkP->fetchColumn()) $pid = $validProd;

                $itemStmt->execute([
                    ':oid'   => $newOrderId,
                    ':pid'   => $pid,
                    ':qty'   => (int)($it['quantity'] ?? 1),
                    ':price' => (float)($it['unit_price'] ?? ($totalAmount / count($items)))
                ]);
            }
        } else {
            $pid = !empty($data['prod_id']) ? trim($data['prod_id']) : $validProd;
            $chkP = $pdo->prepare("SELECT prod_id FROM products WHERE prod_id = ?");
            $chkP->execute([$pid]);
            if (!$chkP->fetchColumn()) $pid = $validProd;

            $itemStmt = $pdo->prepare("
                INSERT INTO order_items (order_id, prod_id, quantity, unit_price)
                VALUES (:oid, :pid, 1, :price)
            ");
            $itemStmt->execute([
                ':oid'   => $newOrderId,
                ':pid'   => $pid,
                ':price' => $totalAmount
            ]);
        }

        $pdo->commit();

        AuditLogger::logSecurityEvent('ORDER_CREATED', 'CUS', "Created order #{$newOrderId} for customer {$cusId}", 'Low', null, $cusId);

        require_once __DIR__ . '/../../includes/enterprise_flows.php';
        $flowRes = vp_process_order($pdo, $newOrderId);

        Response::success([
            'order_id'     => $newOrderId,
            'inv_id'       => $flowRes['inv_id'] ?? '',
            'doc_id'       => $flowRes['doc_id'] ?? '',
            'ops_task_id'  => $flowRes['ops_task_id'] ?? '',
            'status'       => $status,
            'total_amount' => $flowRes['total_amount'] ?? $totalAmount,
            'order_date'   => date('Y-m-d H:i:s')
        ], "Order #{$newOrderId} placed successfully. Invoice " . ($flowRes['inv_id'] ?? '') . " generated.", 201);
    }

    if ($method === 'PUT' || $method === 'PATCH') {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?: [];

        $orderId = isset($data['order_id']) ? (int)$data['order_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : null);
        if (!$orderId) {
            Response::error("Order ID is required.", 400);
        }

        // Verify ownership
        $chk = $pdo->prepare("SELECT order_id, cus_id, status FROM orders WHERE order_id = :oid");
        $chk->execute([':oid' => $orderId]);
        $existing = $chk->fetch(PDO::FETCH_ASSOC);

        if (!$existing) {
            Response::error("Order not found.", 404);
        }
        if (!$isSA && $existing['cus_id'] !== $cusId) {
            Response::error("Order not found or access denied.", 403);
        }

        $fields = [];
        $params = [':oid' => $orderId];

        if (isset($data['status'])) {
            $fields[] = "status = :status";
            $params[':status'] = trim($data['status']);
        }
        if (isset($data['total_amount'])) {
            $fields[] = "total_amount = :total_amount";
            $params[':total_amount'] = (float)$data['total_amount'];
        }

        if (empty($fields)) {
            Response::error("No valid fields provided for update.", 400);
        }

        $sql = "UPDATE orders SET " . implode(", ", $fields) . " WHERE order_id = :oid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        AuditLogger::logSecurityEvent('ORDER_UPDATED', 'CUS', "Updated order #{$orderId}", 'Low', null, $cusId);

        Response::success(['order_id' => $orderId], "Order #{$orderId} updated successfully");
    }

    if ($method === 'DELETE') {
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$orderId) {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            $orderId = isset($data['order_id']) ? (int)$data['order_id'] : null;
        }

        if (!$orderId) {
            Response::error("Order ID is required.", 400);
        }

        // Verify ownership
        $chk = $pdo->prepare("SELECT order_id, cus_id FROM orders WHERE order_id = :oid");
        $chk->execute([':oid' => $orderId]);
        $existing = $chk->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            Response::error("Order not found.", 404);
        }
        if (!$isSA && $existing['cus_id'] !== $cusId) {
            Response::error("Order not found or access denied.", 403);
        }

        $pdo->beginTransaction();
        $delItems = $pdo->prepare("DELETE FROM order_items WHERE order_id = :oid");
        $delItems->execute([':oid' => $orderId]);

        $delOrder = $pdo->prepare("DELETE FROM orders WHERE order_id = :oid");
        $delOrder->execute([':oid' => $orderId]);
        $pdo->commit();

        AuditLogger::logSecurityEvent('ORDER_DELETED', 'CUS', "Deleted order #{$orderId}", 'Medium', null, $cusId);

        Response::success(['order_id' => $orderId], "Order #{$orderId} deleted successfully");
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    Response::error("Orders API Error: " . $e->getMessage(), 500);
}
