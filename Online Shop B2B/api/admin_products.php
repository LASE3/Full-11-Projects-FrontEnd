<?php
declare(strict_types=1);
/**
 * Online Shop B2B — SuperAdmin Products API
 * Location: Online Shop B2B/api/admin_products.php
 * Methods: GET (list/single), POST (create), PUT (update), DELETE (soft/force)
 * Access:  L3+ clearance minimum; mutation requires SuperAdmin (L4)
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('SHP', ['min_clearance' => 'L3']);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/AuditLogger.php';
require_once __DIR__ . '/helpers/Response.php';

$pdo    = getDbConnection();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

// ─────────────────────────────────────────────────────────────────────────────
// Helper: next PROD-xxxx id
// ─────────────────────────────────────────────────────────────────────────────
function nextProdId(PDO $pdo): string {
    $max = $pdo->query(
        "SELECT MAX(CAST(SUBSTRING(prod_id,6) AS UNSIGNED)) FROM products WHERE prod_id REGEXP '^PROD-[0-9]+$'"
    )->fetchColumn();
    return 'PROD-' . str_pad((string)(((int)$max) + 1), 4, '0', STR_PAD_LEFT);
}

try {
    // ── GET: list all products (admin view with stock, soft-deleted flag) ───
    if ($method === 'GET' && $action === 'list') {
        $stmt = $pdo->query("
            SELECT p.prod_id, p.product_name, p.description, p.billing_model,
                   p.price, p.is_active,
                   COALESCE(pi.quantity_on_hand, 0) AS stock,
                   COALESCE(pi.reorder_level, 15)   AS reorder_level,
                   COALESCE(pi.warehouse_location, 'Main Depot') AS warehouse_location
            FROM products p
            LEFT JOIN product_inventory pi ON p.prod_id = pi.prod_id
            ORDER BY p.prod_id ASC
        ");
        Response::success($stmt->fetchAll(PDO::FETCH_ASSOC), 'Admin product list', 200);
    }

    // ── GET: single product ─────────────────────────────────────────────────
    elseif ($method === 'GET' && $action === 'get') {
        $prodId = trim($_GET['prod_id'] ?? '');
        if (!$prodId) Response::error('prod_id required', 400);

        $stmt = $pdo->prepare("
            SELECT p.*, COALESCE(pi.quantity_on_hand, 0) AS stock
            FROM products p
            LEFT JOIN product_inventory pi ON p.prod_id = pi.prod_id
            WHERE p.prod_id = ?
        ");
        $stmt->execute([$prodId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) Response::error('Product not found', 404);
        Response::success($row, 'Product detail', 200);
    }

    // ── GET: admin order management ─────────────────────────────────────────
    elseif ($method === 'GET' && $action === 'orders') {
        $cusFilter = $_GET['cus_id'] ?? null;
        $sql = "SELECT o.order_id, o.cus_id, o.status, o.total_amount, o.created_at,
                       GROUP_CONCAT(oi.prod_id ORDER BY oi.prod_id SEPARATOR ', ') AS products
                FROM orders o
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                " . ($cusFilter ? "WHERE o.cus_id = :cus_id" : "") . "
                GROUP BY o.order_id ORDER BY o.created_at DESC LIMIT 200";
        $stmt = $pdo->prepare($sql);
        if ($cusFilter) $stmt->execute([':cus_id' => $cusFilter]);
        else $stmt->execute();
        Response::success($stmt->fetchAll(PDO::FETCH_ASSOC), 'Order list', 200);
    }

    // ── POST: create product ────────────────────────────────────────────────
    elseif ($method === 'POST') {
        // SuperAdmin L4 required for create
        if (($_vp_user['clearance_level'] ?? '') !== 'L4') {
            Response::error('SuperAdmin (L4) clearance required', 403);
        }
        vp_enforce_csrf();

        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $name     = trim($body['product_name'] ?? '');
        $desc     = trim($body['description'] ?? '');
        $model    = trim($body['billing_model'] ?? 'One-Time Purchase');
        $price    = (float)($body['price'] ?? 0.00);
        $stock    = (int)($body['stock'] ?? 0);

        if (!$name) Response::error('product_name required', 422);

        $prodId = nextProdId($pdo);
        $pdo->prepare(
            "INSERT INTO products (prod_id, product_name, description, billing_model, price, is_active)
             VALUES (?, ?, ?, ?, ?, 1)"
        )->execute([$prodId, $name, $desc, $model, $price]);

        // Seed inventory row
        $pdo->prepare(
            "INSERT INTO product_inventory (prod_id, quantity_on_hand, reorder_level, warehouse_location)
             VALUES (?, ?, 15, 'Main Depot')
             ON DUPLICATE KEY UPDATE quantity_on_hand = quantity_on_hand"
        )->execute([$prodId, $stock]);

        AuditLogger::log('SHP', 'PRODUCT_CREATED', $prodId, "Created by {$_vp_user['emp_id']}");
        Response::success(['prod_id' => $prodId], 'Product created', 201);
    }

    // ── PUT: update product ─────────────────────────────────────────────────
    elseif ($method === 'PUT') {
        if (($_vp_user['clearance_level'] ?? '') !== 'L4') {
            Response::error('SuperAdmin (L4) clearance required', 403);
        }
        vp_enforce_csrf();

        $body   = json_decode(file_get_contents('php://input'), true) ?? [];
        $prodId = trim($body['prod_id'] ?? '');
        if (!$prodId) Response::error('prod_id required', 422);

        $fields = [];
        $params = [];
        foreach (['product_name','description','billing_model','price'] as $f) {
            if (isset($body[$f])) { $fields[] = "{$f} = ?"; $params[] = $body[$f]; }
        }
        if (isset($body['is_active'])) { $fields[] = 'is_active = ?'; $params[] = (int)$body['is_active']; }
        if (empty($fields)) Response::error('Nothing to update', 422);
        $params[] = $prodId;
        $pdo->prepare("UPDATE products SET " . implode(', ', $fields) . " WHERE prod_id = ?")->execute($params);

        if (isset($body['stock'])) {
            $pdo->prepare(
                "INSERT INTO product_inventory (prod_id, quantity_on_hand, reorder_level, warehouse_location)
                 VALUES (?, ?, 15, 'Main Depot')
                 ON DUPLICATE KEY UPDATE quantity_on_hand = ?"
            )->execute([$prodId, (int)$body['stock'], (int)$body['stock']]);
        }

        AuditLogger::log('SHP', 'PRODUCT_UPDATED', $prodId, "Updated by {$_vp_user['emp_id']}");
        Response::success(['prod_id' => $prodId], 'Product updated', 200);
    }

    // ── DELETE: soft or force delete ────────────────────────────────────────
    elseif ($method === 'DELETE') {
        if (($_vp_user['clearance_level'] ?? '') !== 'L4') {
            Response::error('SuperAdmin (L4) clearance required', 403);
        }
        vp_enforce_csrf();

        $prodId = trim($_GET['prod_id'] ?? '');
        $force  = ($_GET['force'] ?? '0') === '1';
        if (!$prodId) Response::error('prod_id required', 422);

        // Safety: prevent deleting baseline PROD-1001..1010
        if (preg_match('/^PROD-100[1-9]$|^PROD-1010$/', $prodId)) {
            Response::error('Cannot delete baseline product ' . $prodId, 403);
        }

        if ($force) {
            $pdo->prepare("DELETE FROM product_inventory WHERE prod_id = ?")->execute([$prodId]);
            $pdo->prepare("DELETE FROM products WHERE prod_id = ?")->execute([$prodId]);
            AuditLogger::log('SHP', 'PRODUCT_FORCE_DELETED', $prodId, "Force-deleted by {$_vp_user['emp_id']}");
            Response::success([], 'Product permanently deleted', 200);
        } else {
            $pdo->prepare("UPDATE products SET is_active = 0 WHERE prod_id = ?")->execute([$prodId]);
            AuditLogger::log('SHP', 'PRODUCT_SOFT_DELETED', $prodId, "Soft-deleted by {$_vp_user['emp_id']}");
            Response::success(['prod_id' => $prodId], 'Product deactivated (soft delete)', 200);
        }
    }

    else {
        Response::error('Method not allowed', 405);
    }

} catch (Throwable $e) {
    Response::error('Server error: ' . $e->getMessage(), 500);
}
