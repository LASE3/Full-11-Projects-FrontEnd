<?php

/**
 * Class 2: Online Shop B2B - Quotes / RFQ API
 * Location: Online Shop B2B/api/quotes.php
 * Methods: GET, POST
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$user = vp_api_guard('SHP', []);

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
    } catch (Throwable $e) {
        error_log("Shop quotes GET exception: " . $e->getMessage());
        Response::error("Unable to load quotes at this time.", 500);
    }
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $targetCus = $isCustomer ? $cusId : (trim((string)($input['cus_id'] ?? $cusId)));
    $empId = trim((string)($input['emp_id'] ?? ''));
    if (!$empId && (($user['account_type'] ?? '') === 'Employee')) {
        $empId = $user['emp_id'] ?? '';
    }

    if (!$targetCus) {
        Response::error("Customer ID is required.", 422);
    }
    $cChk = $pdo->prepare("SELECT account_manager_emp_id FROM customers WHERE cus_id = ?");
    $cChk->execute([$targetCus]);
    $mgrEmpId = $cChk->fetchColumn();
    if ($mgrEmpId === false) {
        Response::error("Customer #{$targetCus} does not exist.", 422);
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

    // Determine items list: support both multi-item ('items' array) and single product ('prod_id')
    $items = [];
    if (!empty($input['items']) && is_array($input['items'])) {
        $items = $input['items'];
    } elseif (!empty($input['prod_id'])) {
        $items[] = [
            'prod_id'    => $input['prod_id'],
            'quantity'   => $input['quantity'] ?? 1,
            'unit_price' => $input['unit_price'] ?? 0.0,
        ];
    } else {
        Response::error("Product ID or items list is required.", 422);
    }

    try {
        $pdo->beginTransaction();
        $createdQuotes = [];
        $totalQuoteValue = 0.0;

        foreach ($items as $idx => $item) {
            $prodId    = trim((string)($item['prod_id'] ?? ''));
            $qty       = (int)($item['quantity'] ?? 0);
            $unitPrice = (float)($item['unit_price'] ?? 0.0);

            if (!$prodId) {
                if ($pdo->inTransaction()) { $pdo->rollBack(); }
                Response::error("Item " . ($idx + 1) . ": Product ID is required.", 422);
            }
            $pChk = $pdo->prepare("SELECT price FROM products WHERE prod_id = ?");
            $pChk->execute([$prodId]);
            $catPrice = $pChk->fetchColumn();
            if ($catPrice === false) {
                if ($pdo->inTransaction()) { $pdo->rollBack(); }
                Response::error("Item " . ($idx + 1) . ": Product #{$prodId} does not exist.", 422);
            }

            if ($qty <= 0) {
                if ($pdo->inTransaction()) { $pdo->rollBack(); }
                Response::error("Item " . ($idx + 1) . ": Valid quantity > 0 is required.", 422);
            }

            if ($unitPrice <= 0) {
                $cpStmt = $pdo->prepare("SELECT special_price FROM customer_pricing WHERE cus_id = :cid AND prod_id = :pid");
                $cpStmt->execute([':cid' => $targetCus, ':pid' => $prodId]);
                $spPrice = $cpStmt->fetchColumn();
                $unitPrice = ($spPrice !== false && (float)$spPrice > 0) ? (float)$spPrice : (float)$catPrice;
            }
            if ($unitPrice <= 0) {
                if ($pdo->inTransaction()) { $pdo->rollBack(); }
                Response::error("Item " . ($idx + 1) . ": Unit price must be greater than zero.", 422);
            }

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
            $newQuoteId = (int)$pdo->lastInsertId();
            $lineTotal = $qty * $unitPrice;
            $totalQuoteValue += $lineTotal;

            $createdQuotes[] = [
                'quote_id'          => $newQuoteId,
                'cus_id'            => $targetCus,
                'prod_id'           => $prodId,
                'quantity'          => $qty,
                'unit_price'        => $unitPrice,
                'total_quote_value' => $lineTotal,
            ];

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
        }

        $pdo->commit();

        if (count($createdQuotes) === 1) {
            Response::success($createdQuotes[0], "Quote successfully issued", 201);
        } else {
            Response::success([
                'quotes'            => $createdQuotes,
                'total_quotes'      => count($createdQuotes),
                'total_quote_value' => $totalQuoteValue,
            ], "Quotes successfully issued", 201);
        }
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Shop quote creation error: " . $e->getMessage());
        Response::error("Failed to generate quote. Please review item details and try again.", 500);
    }
}
