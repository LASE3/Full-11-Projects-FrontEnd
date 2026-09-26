<?php
/**
 * Class 2: Online Shop B2B - Quotes / RFQ API
 * Location: api/v1/shop/quotes.php
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
    $input = json_decode(file_get_contents('php://input'), true);
    $targetCus = $cusId ?: ($input['cus_id'] ?? null);
    $prodId    = trim($input['prod_id'] ?? '');
    $qty       = (int)($input['quantity'] ?? 0);
    $unitPrice = (float)($input['unit_price'] ?? 0.0);
    $empId     = $_SESSION['emp_id'] ?? ($input['emp_id'] ?? 'EMP-1006');

    if (!$targetCus || !$prodId || $qty <= 0) {
        Response::error("Customer ID, Product ID, and valid quantity required.", 422);
    }

    if ($unitPrice <= 0) {
        // Fetch baseline price
        $pStmt = $pdo->prepare("SELECT COALESCE(special_price, 2500.00) FROM customer_pricing WHERE cus_id = :cid AND prod_id = :pid");
        $pStmt->execute([':cid' => $targetCus, ':pid' => $prodId]);
        $unitPrice = (float)($pStmt->fetchColumn() ?: 2500.00);
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
            $empId, $targetCus, 'Online Shop B2B', 'SHP',
            'CREATE_B2B_PRICE_QUOTE', 'quotes', (string)$newQuoteId,
            ['prod_id' => $prodId, 'quantity' => $qty, 'unit_price' => $unitPrice], 'SUCCESS'
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
