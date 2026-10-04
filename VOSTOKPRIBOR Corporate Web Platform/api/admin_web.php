<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Corporate Web Platform — Minimal Admin Editor API
 * Handles CRUD operations for News/Announcements, Products, and Job Postings.
 * Requires clearance L3+ or SuperAdmin. Audited via AuditLogger.
 */

require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('WEB', ['min_clearance' => 'L3', 'require_csrf' => true]);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/AuditLogger.php';

$pdo = getDbConnection();

// Support JSON input as well as form POST
$inputRaw = file_get_contents('php://input');
$body = [];
if (!empty($inputRaw)) {
    $decoded = json_decode($inputRaw, true);
    if (is_array($decoded)) {
        $body = $decoded;
    }
}
$req = array_merge($_GET, $_POST, $body);

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$section = trim((string)($req['section'] ?? 'announcements'));
$action = trim((string)($req['action'] ?? ($method === 'GET' ? 'list' : '')));

$actorId = $_vp_user['emp_id'] ?? ($_vp_user['user_id'] ?? 'EMP-0001');

function jsonRes(mixed $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // -------------------------------------------------------------
    // SECTION 1: ANNOUNCEMENTS / NEWS
    // -------------------------------------------------------------
    if ($section === 'announcements' || $section === 'news') {
        if ($method === 'GET' || $action === 'list') {
            $stmt = $pdo->query("SELECT * FROM announcements ORDER BY posted_at DESC LIMIT 50");
            jsonRes(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        }

        if ($action === 'create' || ($method === 'POST' && empty($action))) {
            $title = trim((string)($req['title'] ?? ''));
            $content = trim((string)($req['body'] ?? ($req['content'] ?? '')));
            $dept = trim((string)($req['audience_dept'] ?? 'ALL')) ?: 'ALL';

            if (empty($title) || empty($content)) {
                jsonRes(['success' => false, 'error' => 'Title and announcement content are required.'], 400);
            }

            $stmt = $pdo->prepare("INSERT INTO announcements (title, body, posted_by_emp_id, audience_dept, posted_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$title, $content, $actorId, $dept]);
            $newId = (int)$pdo->lastInsertId();

            AuditLogger::logAction($actorId, null, 'Corporate Web Platform', 'WEB', 'CREATE_ANNOUNCEMENT', 'announcements', (string)$newId, ['title' => $title, 'audience_dept' => $dept]);

            jsonRes(['success' => true, 'message' => 'Announcement published.', 'id' => $newId], 201);
        }

        if ($action === 'update' || $method === 'PUT') {
            $id = (int)($req['announcement_id'] ?? ($req['id'] ?? 0));
            $title = trim((string)($req['title'] ?? ''));
            $content = trim((string)($req['body'] ?? ($req['content'] ?? '')));

            if ($id <= 0 || empty($title) || empty($content)) {
                jsonRes(['success' => false, 'error' => 'Valid ID, title, and body are required.'], 400);
            }

            $chk = $pdo->prepare("SELECT * FROM announcements WHERE announcement_id = ?");
            $chk->execute([$id]);
            $old = $chk->fetch(PDO::FETCH_ASSOC);
            if (!$old) jsonRes(['success' => false, 'error' => 'Announcement not found.'], 404);

            $stmt = $pdo->prepare("UPDATE announcements SET title = ?, body = ? WHERE announcement_id = ?");
            $stmt->execute([$title, $content, $id]);

            AuditLogger::logAction($actorId, null, 'Corporate Web Platform', 'WEB', 'UPDATE_ANNOUNCEMENT', 'announcements', (string)$id, ['title' => $title], 'SUCCESS', ['title' => $old['title']]);

            jsonRes(['success' => true, 'message' => 'Announcement updated.', 'id' => $id]);
        }

        if ($action === 'delete' || $method === 'DELETE') {
            $id = (int)($req['announcement_id'] ?? ($req['id'] ?? 0));
            if ($id <= 0) jsonRes(['success' => false, 'error' => 'Valid announcement ID required.'], 400);

            $chk = $pdo->prepare("SELECT * FROM announcements WHERE announcement_id = ?");
            $chk->execute([$id]);
            $old = $chk->fetch(PDO::FETCH_ASSOC);
            if (!$old) jsonRes(['success' => false, 'error' => 'Announcement not found.'], 404);

            $stmt = $pdo->prepare("DELETE FROM announcements WHERE announcement_id = ?");
            $stmt->execute([$id]);

            AuditLogger::logAction($actorId, null, 'Corporate Web Platform', 'WEB', 'DELETE_ANNOUNCEMENT', 'announcements', (string)$id, null, 'SUCCESS', ['title' => $old['title']]);

            jsonRes(['success' => true, 'message' => 'Announcement deleted.', 'id' => $id]);
        }
    }

    // -------------------------------------------------------------
    // SECTION 2: PRODUCTS
    // -------------------------------------------------------------
    if ($section === 'products') {
        if ($method === 'GET' || $action === 'list') {
            $stmt = $pdo->query("SELECT * FROM products ORDER BY prod_id ASC LIMIT 100");
            jsonRes(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        }

        if ($action === 'create' || ($method === 'POST' && empty($action))) {
            $prodId = trim((string)($req['prod_id'] ?? ''));
            $name   = trim((string)($req['product_name'] ?? ''));
            $model  = trim((string)($req['billing_model'] ?? 'PerUnit'));
            $price  = (float)($req['price'] ?? 0);
            $desc   = trim((string)($req['description'] ?? ''));

            if (empty($name) || $price <= 0) {
                jsonRes(['success' => false, 'error' => 'Product name and price > 0 are required.'], 400);
            }

            if (empty($prodId)) {
                $maxNum = $pdo->query("SELECT MAX(CAST(SUBSTRING(prod_id, 6) AS UNSIGNED)) FROM products WHERE prod_id LIKE 'PROD-%'")->fetchColumn();
                $prodId = sprintf('PROD-%04d', ($maxNum ? (int)$maxNum : 1000) + 1);
            }

            $stmt = $pdo->prepare("INSERT INTO products (prod_id, product_name, billing_model, price, description, is_active) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->execute([$prodId, $name, $model, $price, $desc]);

            AuditLogger::logAction($actorId, null, 'Corporate Web Platform', 'WEB', 'CREATE_PRODUCT', 'products', $prodId, ['product_name' => $name, 'price' => $price]);

            jsonRes(['success' => true, 'message' => "Product {$prodId} created.", 'prod_id' => $prodId], 201);
        }

        if ($action === 'update' || $method === 'PUT') {
            $prodId = trim((string)($req['prod_id'] ?? ''));
            if (empty($prodId)) jsonRes(['success' => false, 'error' => 'Product ID required.'], 400);

            $chk = $pdo->prepare("SELECT * FROM products WHERE prod_id = ?");
            $chk->execute([$prodId]);
            $old = $chk->fetch(PDO::FETCH_ASSOC);
            if (!$old) jsonRes(['success' => false, 'error' => 'Product not found.'], 404);

            $name  = trim((string)($req['product_name'] ?? $old['product_name']));
            $model = trim((string)($req['billing_model'] ?? $old['billing_model']));
            $price = isset($req['price']) ? (float)$req['price'] : (float)$old['price'];
            $desc  = trim((string)($req['description'] ?? $old['description']));

            $stmt = $pdo->prepare("UPDATE products SET product_name = ?, billing_model = ?, price = ?, description = ? WHERE prod_id = ?");
            $stmt->execute([$name, $model, $price, $desc, $prodId]);

            AuditLogger::logAction($actorId, null, 'Corporate Web Platform', 'WEB', 'UPDATE_PRODUCT', 'products', $prodId, ['product_name' => $name, 'price' => $price], 'SUCCESS', ['price' => $old['price']]);

            jsonRes(['success' => true, 'message' => "Product {$prodId} updated.", 'prod_id' => $prodId]);
        }

        if ($action === 'delete' || $method === 'DELETE') {
            $prodId = trim((string)($req['prod_id'] ?? ''));
            if (empty($prodId)) jsonRes(['success' => false, 'error' => 'Product ID required.'], 400);

            $chk = $pdo->prepare("SELECT * FROM products WHERE prod_id = ?");
            $chk->execute([$prodId]);
            $old = $chk->fetch(PDO::FETCH_ASSOC);
            if (!$old) jsonRes(['success' => false, 'error' => 'Product not found.'], 404);

            // Soft-delete
            $stmt = $pdo->prepare("UPDATE products SET is_active = 0 WHERE prod_id = ?");
            $stmt->execute([$prodId]);

            AuditLogger::logAction($actorId, null, 'Corporate Web Platform', 'WEB', 'DELETE_PRODUCT', 'products', $prodId, ['is_active' => 0], 'SUCCESS', ['is_active' => $old['is_active']]);

            jsonRes(['success' => true, 'message' => "Product {$prodId} soft-deleted.", 'prod_id' => $prodId]);
        }
    }

    // -------------------------------------------------------------
    // SECTION 3: JOB POSTINGS
    // -------------------------------------------------------------
    if ($section === 'job_postings' || $section === 'jobs') {
        if ($method === 'GET' || $action === 'list') {
            $stmt = $pdo->query("SELECT * FROM job_postings ORDER BY posted_at DESC LIMIT 50");
            jsonRes(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        }

        if ($action === 'create' || ($method === 'POST' && empty($action))) {
            $title = trim((string)($req['title'] ?? ''));
            $dept  = trim((string)($req['department_code'] ?? 'ENG')) ?: 'ENG';
            $pub   = isset($req['is_published']) ? (int)(bool)$req['is_published'] : 1;

            if (empty($title)) {
                jsonRes(['success' => false, 'error' => 'Job title is required.'], 400);
            }

            $stmt = $pdo->prepare("INSERT INTO job_postings (title, department_code, is_published, posted_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$title, $dept, $pub]);
            $newId = (int)$pdo->lastInsertId();

            AuditLogger::logAction($actorId, null, 'Corporate Web Platform', 'WEB', 'CREATE_JOB_POSTING', 'job_postings', (string)$newId, ['title' => $title, 'department_code' => $dept]);

            jsonRes(['success' => true, 'message' => "Job posting #{$newId} published.", 'id' => $newId], 201);
        }

        if ($action === 'update' || $method === 'PUT') {
            $id = (int)($req['posting_id'] ?? ($req['id'] ?? 0));
            if ($id <= 0) jsonRes(['success' => false, 'error' => 'Job posting ID required.'], 400);

            $chk = $pdo->prepare("SELECT * FROM job_postings WHERE posting_id = ?");
            $chk->execute([$id]);
            $old = $chk->fetch(PDO::FETCH_ASSOC);
            if (!$old) jsonRes(['success' => false, 'error' => 'Job posting not found.'], 404);

            $title = trim((string)($req['title'] ?? $old['title']));
            $dept  = trim((string)($req['department_code'] ?? $old['department_code']));
            $pub   = isset($req['is_published']) ? (int)(bool)$req['is_published'] : (int)$old['is_published'];

            $stmt = $pdo->prepare("UPDATE job_postings SET title = ?, department_code = ?, is_published = ? WHERE posting_id = ?");
            $stmt->execute([$title, $dept, $pub, $id]);

            AuditLogger::logAction($actorId, null, 'Corporate Web Platform', 'WEB', 'UPDATE_JOB_POSTING', 'job_postings', (string)$id, ['title' => $title, 'is_published' => $pub], 'SUCCESS', ['title' => $old['title']]);

            jsonRes(['success' => true, 'message' => "Job posting #{$id} updated.", 'id' => $id]);
        }

        if ($action === 'delete' || $method === 'DELETE') {
            $id = (int)($req['posting_id'] ?? ($req['id'] ?? 0));
            if ($id <= 0) jsonRes(['success' => false, 'error' => 'Job posting ID required.'], 400);

            $chk = $pdo->prepare("SELECT * FROM job_postings WHERE posting_id = ?");
            $chk->execute([$id]);
            $old = $chk->fetch(PDO::FETCH_ASSOC);
            if (!$old) jsonRes(['success' => false, 'error' => 'Job posting not found.'], 404);

            $stmt = $pdo->prepare("DELETE FROM job_postings WHERE posting_id = ?");
            $stmt->execute([$id]);

            AuditLogger::logAction($actorId, null, 'Corporate Web Platform', 'WEB', 'DELETE_JOB_POSTING', 'job_postings', (string)$id, null, 'SUCCESS', ['title' => $old['title']]);

            jsonRes(['success' => true, 'message' => "Job posting #{$id} deleted.", 'id' => $id]);
        }
    }

    jsonRes(['success' => false, 'error' => "Unknown section '{$section}' or action '{$action}'."], 400);
} catch (Throwable $e) {
    jsonRes(['success' => false, 'error' => 'Operation failed: ' . $e->getMessage()], 500);
}
