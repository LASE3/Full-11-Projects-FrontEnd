<?php
declare(strict_types=1);
/**
 * Online Shop B2B — SuperAdmin Products API
 * Location: Online Shop B2B/api/admin_products.php
 * Methods: GET (list/single/orders), POST (create/upload), PUT (update), DELETE (soft/force)
 * Access:  L3+ clearance minimum (SuperAdmin or Logistics role)
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('SHP', ['min_clearance' => 'L4']);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/AuditLogger.php';
require_once __DIR__ . '/helpers/Response.php';

$pdo    = getDbConnection();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? ($_POST['action'] ?? 'list');

// Helper: next PROD-xxxx id
function nextProdId(PDO $pdo): string {
    return vp_next_id($pdo, 'products', 'PROD-', 4);
}

try {
    // ── GET: list all products (admin view with stock, soft-deleted flag) ───
    if ($method === 'GET' && $action === 'list') {
        $stmt = $pdo->query("
            SELECT p.prod_id, p.product_name, p.description, p.billing_model,
                   p.price, p.is_active, p.image_url,
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
        $sql = "SELECT o.order_id, o.cus_id, o.status, o.total_amount, o.order_date,
                       GROUP_CONCAT(oi.prod_id ORDER BY oi.prod_id SEPARATOR ', ') AS products
                FROM orders o
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                " . ($cusFilter ? "WHERE o.cus_id = :cus_id" : "") . "
                GROUP BY o.order_id ORDER BY o.order_date DESC LIMIT 200";
        $stmt = $pdo->prepare($sql);
        if ($cusFilter) $stmt->execute([':cus_id' => $cusFilter]);
        else $stmt->execute();
        Response::success($stmt->fetchAll(PDO::FETCH_ASSOC), 'Order list', 200);
    }

    // ── POST action=upload: image upload ────────────────────────────────────
    elseif ($method === 'POST' && ($action === 'upload' || isset($_FILES['image']) || isset($_FILES['file']))) {
        vp_enforce_csrf([]);

        $file = $_FILES['image'] ?? $_FILES['file'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            Response::error('No valid file uploaded or upload error occurred', 400);
        }

        // Max 2 MB
        if ($file['size'] > 2 * 1024 * 1024) {
            Response::error('Image file too large. Maximum allowed size is 2 MB.', 400);
        }

        // Check mime type via finfo
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/jpg'  => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($allowedMimes[$mime])) {
            Response::error('Invalid file format. Only JPG, PNG, and WebP images are accepted.', 400);
        }

        $ext = $allowedMimes[$mime];
        $uploadDir = dirname(__DIR__, 2) . '/uploads/products';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Ensure .htaccess inside uploads prevents script execution
        $uploadHtaccess = dirname(__DIR__, 2) . '/uploads/.htaccess';
        if (!file_exists($uploadHtaccess)) {
            file_put_contents($uploadHtaccess, "Options -Indexes -ExecCGI\nSetHandler default-handler\n<FilesMatch \"\\.(php|phtml|php3|php4|php5|php7|phps|cgi|pl|exe)$\">\n    Require all denied\n</FilesMatch>\n");
        }

        $randomName = 'prod_' . bin2hex(random_bytes(10)) . '.' . $ext;
        $destPath = $uploadDir . '/' . $randomName;
        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            Response::error('Failed to move uploaded file', 500);
        }

        $relUrl = 'uploads/products/' . $randomName;
        $prodId = trim($_POST['prod_id'] ?? $_GET['prod_id'] ?? '');

        $oldVal = null;
        if ($prodId) {
            $stmt = $pdo->prepare("SELECT prod_id, product_name, image_url FROM products WHERE prod_id = ?");
            $stmt->execute([$prodId]);
            $oldVal = $stmt->fetch(PDO::FETCH_ASSOC);

            $pdo->prepare("UPDATE products SET image_url = ? WHERE prod_id = ?")->execute([$relUrl, $prodId]);
        }

        $actorId = $_vp_user['emp_id'] ?? 'EMP-0001';
        AuditLogger::logAction(
            $actorId,
            null,
            'Online Shop B2B',
            'SHP',
            'PRODUCT_IMAGE_UPLOADED',
            'products',
            $prodId ?: null,
            ['image_url' => $relUrl, 'prod_id' => $prodId],
            'SUCCESS',
            $oldVal
        );

        Response::success([
            'image_url' => $relUrl,
            'prod_id'   => $prodId,
            'filename'  => $randomName
        ], 'Image uploaded successfully', 200);
    }

    // ── POST: create product ────────────────────────────────────────────────
    elseif ($method === 'POST') {
        vp_enforce_csrf([]);

        $body = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $name  = trim($body['product_name'] ?? '');
        $desc  = trim($body['description'] ?? '');
        $model = trim($body['billing_model'] ?? 'PerUnit');
        $price = (float)($body['price'] ?? 0.00);
        $stock = (int)($body['stock'] ?? 0);
        $img   = trim($body['image_url'] ?? '');

        if (!$name) Response::error('product_name required', 422);

        $prodId = nextProdId($pdo);
        $pdo->prepare(
            "INSERT INTO products (prod_id, product_name, description, billing_model, price, is_active, image_url)
             VALUES (?, ?, ?, ?, ?, 1, ?)"
        )->execute([$prodId, $name, $desc, $model, $price, $img ?: null]);

        // Seed inventory row
        $pdo->prepare(
            "INSERT INTO product_inventory (prod_id, quantity_on_hand, reorder_level, warehouse_location)
             VALUES (?, ?, 15, 'Main Depot')
             ON DUPLICATE KEY UPDATE quantity_on_hand = VALUES(quantity_on_hand)"
        )->execute([$prodId, $stock]);

        $newValues = [
            'prod_id'       => $prodId,
            'product_name'  => $name,
            'description'   => $desc,
            'billing_model' => $model,
            'price'         => $price,
            'stock'         => $stock,
            'is_active'     => 1,
            'image_url'     => $img
        ];

        $actorId = $_vp_user['emp_id'] ?? 'EMP-0001';
        AuditLogger::logAction(
            $actorId,
            null,
            'Online Shop B2B',
            'SHP',
            'PRODUCT_CREATED',
            'products',
            $prodId,
            $newValues,
            'SUCCESS',
            null
        );

        Response::success(['prod_id' => $prodId], 'Product created', 201);
    }

    // ── PUT: update product ─────────────────────────────────────────────────
    elseif ($method === 'PUT') {
        vp_enforce_csrf([]);

        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $prodId = trim($body['prod_id'] ?? $_GET['prod_id'] ?? '');
        if (!$prodId) Response::error('prod_id required', 422);

        // Fetch old product snapshot
        $oldStmt = $pdo->prepare("
            SELECT p.*, COALESCE(pi.quantity_on_hand, 0) AS stock
            FROM products p
            LEFT JOIN product_inventory pi ON p.prod_id = pi.prod_id
            WHERE p.prod_id = ?
        ");
        $oldStmt->execute([$prodId]);
        $oldProduct = $oldStmt->fetch(PDO::FETCH_ASSOC);
        if (!$oldProduct) Response::error('Product not found', 404);

        $fields = [];
        $params = [];
        foreach (['product_name','description','billing_model','price','image_url'] as $f) {
            if (isset($body[$f])) { $fields[] = "{$f} = ?"; $params[] = $body[$f]; }
        }
        if (isset($body['is_active'])) { $fields[] = 'is_active = ?'; $params[] = (int)$body['is_active']; }
        if (empty($fields) && !isset($body['stock'])) Response::error('Nothing to update', 422);

        if (!empty($fields)) {
            $params[] = $prodId;
            $pdo->prepare("UPDATE products SET " . implode(', ', $fields) . " WHERE prod_id = ?")->execute($params);
        }

        if (isset($body['stock'])) {
            $pdo->prepare(
                "INSERT INTO product_inventory (prod_id, quantity_on_hand, reorder_level, warehouse_location)
                 VALUES (?, ?, 15, 'Main Depot')
                 ON DUPLICATE KEY UPDATE quantity_on_hand = ?"
            )->execute([$prodId, (int)$body['stock'], (int)$body['stock']]);
        }

        // Fetch updated snapshot
        $oldStmt->execute([$prodId]);
        $newProduct = $oldStmt->fetch(PDO::FETCH_ASSOC);

        $actorId = $_vp_user['emp_id'] ?? 'EMP-0001';
        AuditLogger::logAction(
            $actorId,
            null,
            'Online Shop B2B',
            'SHP',
            'PRODUCT_UPDATED',
            'products',
            $prodId,
            $newProduct,
            'SUCCESS',
            $oldProduct
        );

        Response::success(['prod_id' => $prodId], 'Product updated', 200);
    }

    // ── DELETE: soft or force delete ────────────────────────────────────────
    elseif ($method === 'DELETE') {
        vp_enforce_csrf([]);

        $rawBody = json_decode((string)file_get_contents('php://input'), true) ?: [];
        $prodId  = trim($_GET['prod_id'] ?? $rawBody['prod_id'] ?? '');
        $force   = !empty($_GET['force']) || !empty($rawBody['force']);
        $confirm = trim($_GET['confirm'] ?? $rawBody['confirm'] ?? '');

        if (!$prodId) Response::error('prod_id required', 422);

        // Safety: prevent deleting baseline PROD-1001..1010
        if (preg_match('/^PROD-100[1-9]$|^PROD-1010$/', $prodId)) {
            Response::error('Cannot delete baseline product ' . $prodId, 403);
        }

        // Snapshot existing product
        $oldStmt = $pdo->prepare("
            SELECT p.*, COALESCE(pi.quantity_on_hand, 0) AS stock
            FROM products p
            LEFT JOIN product_inventory pi ON p.prod_id = pi.prod_id
            WHERE p.prod_id = ?
        ");
        $oldStmt->execute([$prodId]);
        $oldProduct = $oldStmt->fetch(PDO::FETCH_ASSOC);
        if (!$oldProduct) Response::error('Product not found', 404);

        $actorId = $_vp_user['emp_id'] ?? 'EMP-0001';

        // Check if product appears in order_items or quotes
        $chkOrder = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE prod_id = ?");
        $chkOrder->execute([$prodId]);
        $orderCount = (int)$chkOrder->fetchColumn();

        $chkQuote = $pdo->prepare("SELECT COUNT(*) FROM quotes WHERE prod_id = ?");
        $chkQuote->execute([$prodId]);
        $quoteCount = (int)$chkQuote->fetchColumn();

        if ($orderCount > 0 || $quoteCount > 0) {
            // Snapshot product name and price into order_items before deactivating
            $snap = $pdo->prepare("
                UPDATE order_items
                SET product_name = COALESCE(product_name, ?),
                    unit_price   = COALESCE(unit_price, ?)
                WHERE prod_id = ?
            ");
            $snap->execute([$oldProduct['product_name'], $oldProduct['price'], $prodId]);

            // Must soft-delete (is_active = 0)
            $pdo->prepare("UPDATE products SET is_active = 0 WHERE prod_id = ?")->execute([$prodId]);

            AuditLogger::logAction(
                $actorId,
                null,
                'Online Shop B2B',
                'SHP',
                'PRODUCT_SOFT_DELETED',
                'products',
                $prodId,
                ['is_active' => 0, 'reason' => 'Referenced in order_items/quotes; preserved historical snapshot'],
                'SUCCESS',
                $oldProduct
            );

            Response::success([
                'prod_id'      => $prodId,
                'action'       => 'soft_delete',
                'orders_count' => $orderCount,
                'quotes_count' => $quoteCount
            ], 'Product appears in order_items/quotes: snapshotted name/price to order_items and performed soft delete (is_active=0)', 200);
        }

        // Product does not appear in order_items or quotes
        if ($force) {
            // Require typed confirmation field
            $expectedConfirm = "DELETE {$prodId}";
            if ($confirm !== $expectedConfirm) {
                Response::error("Typed confirmation required: confirm={$expectedConfirm}", 400);
            }

            $pdo->prepare("DELETE FROM product_inventory WHERE prod_id = ?")->execute([$prodId]);
            $pdo->prepare("DELETE FROM products WHERE prod_id = ?")->execute([$prodId]);

            AuditLogger::logAction(
                $actorId,
                null,
                'Online Shop B2B',
                'SHP',
                'PRODUCT_FORCE_DELETED',
                'products',
                $prodId,
                ['deleted' => true, 'confirmation' => $confirm],
                'SUCCESS',
                $oldProduct
            );

            Response::success(['prod_id' => $prodId, 'action' => 'force_delete'], 'Product permanently deleted', 200);
        } else {
            // Standard soft delete
            $pdo->prepare("UPDATE products SET is_active = 0 WHERE prod_id = ?")->execute([$prodId]);

            AuditLogger::logAction(
                $actorId,
                null,
                'Online Shop B2B',
                'SHP',
                'PRODUCT_SOFT_DELETED',
                'products',
                $prodId,
                ['is_active' => 0],
                'SUCCESS',
                $oldProduct
            );

            Response::success(['prod_id' => $prodId, 'action' => 'soft_delete'], 'Product deactivated (soft delete)', 200);
        }
    }

    else {
        Response::error('Method not allowed', 405);
    }

} catch (Throwable $e) {
    Response::error('Server error: ' . $e->getMessage(), 500);
}
