<?php

/**
 * Class 2: Online Shop B2B - Quotes / RFQ API
 * Location: Online Shop B2B/api/quotes.php
 * Methods: GET, POST
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('SHP', []);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/I18n.php';
require_once __DIR__ . '/helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$isCustomer = (($user['account_type'] ?? '') === 'Customer');
if ($isCustomer) {
    $cusId = (string)($user['cus_id'] ?? ($user['user_id'] ?? ''));
} else {
    $canImpersonate = hasEmployeePermission($pdo, $user, 'CUSTOMER_IMPERSONATE');
    $cusId = $canImpersonate ? (string)($_SESSION['impersonate_cus_id'] ?? ($_GET['cus_id'] ?? '')) : '';
}
$lang  = $_GET['lang'] ?? 'en';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    if (!$cusId) {
        Response::error("Customer ID required to retrieve quotes.", 401);
    }

    try {
        $stmt = $pdo->prepare("
            SELECT 
                q.quote_id,
                q.cus_id,
                q.prod_id,
                p.product_name,
                q.quantity,
                q.unit_price,
                (q.quantity * q.unit_price) AS total_quote_value,
                q.created_at,
                e.full_name AS prepared_by_rep
            FROM quotes q
            JOIN products p ON q.prod_id = p.prod_id
            LEFT JOIN employees e ON q.created_by_emp_id = e.emp_id
            WHERE q.cus_id = :cid
            ORDER BY q.quote_id DESC
        ");
        $stmt->execute([':cid' => $cusId]);
        Response::success($stmt->fetchAll(PDO::FETCH_ASSOC), "Customer quotes loaded");
    } catch (Exception $e) {
        Response::error("Failed to retrieve quotes: " . $e->getMessage(), 500);
    }
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $targetCus = $isCustomer ? $cusId : (trim($input['cus_id'] ?? $cusId));
    $prodId    = trim($input['prod_id'] ?? '');
    $qty       = (int)($input['quantity'] ?? 0);
    $unitPrice = (float)($input['unit_price'] ?? 0.0);
    $empId     = trim((string)($input['emp_id'] ?? ($user['emp_id'] ?? '')));

    if (!$targetCus) {
        Response::error("Customer ID is required.", 422);
    }
    $cChk = $pdo->prepare("SELECT account_manager_emp_id FROM customers WHERE cus_id = ?");
    $cChk->execute([$targetCus]);
    $mgrEmpId = $cChk->fetchColumn();
    if ($mgrEmpId === false) {
        Response::error("Customer #{$targetCus} does not exist.", 422);
    }

    if (!$prodId) {
        Response::error("Product ID is required.", 422);
    }
    $pChk = $pdo->prepare("SELECT price FROM products WHERE prod_id = ?");
    $pChk->execute([$prodId]);
    $catPrice = $pChk->fetchColumn();
    if ($catPrice === false) {
        Response::error("Product #{$prodId} does not exist.", 422);
    }

    if ($qty <= 0) {
        Response::error("Valid quantity > 0 is required.", 422);
    }

    if (!$empId) {
        $empId = $mgrEmpId ?: 'EMP-1006';
    } else {
        $eChk = $pdo->prepare("SELECT 1 FROM employees WHERE emp_id = ?");
        $eChk->execute([$empId]);
        if (!$eChk->fetchColumn()) {
            Response::error("Employee #{$empId} does not exist.", 422);
        }
    }

    if ($unitPrice <= 0) {
        $cpStmt = $pdo->prepare("SELECT special_price FROM customer_pricing WHERE cus_id = :cid AND prod_id = :pid");
        $cpStmt->execute([':cid' => $targetCus, ':pid' => $prodId]);
        $spPrice = $cpStmt->fetchColumn();
        $unitPrice = ($spPrice !== false && (float)$spPrice > 0) ? (float)$spPrice : (float)$catPrice;
    }
    if ($unitPrice <= 0) {
        Response::error("Unit price must be greater than zero.", 422);
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO quotes (cus_id, prod_id, quantity, unit_price, created_by_emp_id, created_at)
            VALUES (:cid, :pid, :qty, :prc, :eid, NOW())
        ");
        $stmt->execute([
            ':cid' => $targetCus,
            ':pid' => $prodId,
            ':qty' => $qty,
            ':prc' => $unitPrice,
            ':eid' => $empId
        ]);
        $newQuoteId = $pdo->lastInsertId();

        AuditLogger::logAction(
            $empId,
            $targetCus,
            'Online Shop B2B',
            'SHP',
            'CREATE_B2B_PRICE_QUOTE',
            'quotes',
            (string)$newQuoteId,
            ['prod_id' => $prodId, 'quantity' => $qty, 'unit_price' => $unitPrice],
            'SUCCESS'
        );

        Response::success([
            'quote_id'          => (int)$newQuoteId,
            'cus_id'            => $targetCus,
            'prod_id'           => $prodId,
            'quantity'          => $qty,
            'unit_price'        => $unitPrice,
            'total_quote_value' => $qty * $unitPrice
        ], "Quote successfully issued", 201);
    } catch (Exception $e) {
        Response::error("Failed to generate quote: " . $e->getMessage(), 500);
    }
}
