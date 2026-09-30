<?php

declare(strict_types=1);

/**
 * Class 5: CRM Platform - Quotes API
 * Location: CRM/api/quotes.php
 * Methods: GET, POST, DELETE
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/I18n.php';
require_once __DIR__ . '/helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$lang = strtolower(substr($_GET['lang'] ?? $_COOKIE['vp_lang'] ?? 'en', 0, 2)) === 'ar' ? 'ar' : 'en';
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$empId = $_SESSION['emp_id'] ?? ($_SESSION['vostok_user']['emp_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? 'EMP-1006'));

$rawInput = file_get_contents('php://input');
$jsonBody = [];
if ($rawInput !== false && trim($rawInput) !== '') {
    $decoded = json_decode($rawInput, true);
    if (is_array($decoded)) {
        $jsonBody = $decoded;
    }
}
$data = array_merge(is_array($_GET) ? $_GET : [], is_array($_POST) ? $_POST : [], $jsonBody);

if ($method === 'GET') {
    try {
        $stmt = $pdo->query("
            SELECT 
                q.*,
                c.company_name,
                p.product_name,
                e.full_name AS creator_name
            FROM `quotes` q
            LEFT JOIN `customers` c ON q.cus_id = c.cus_id
            LEFT JOIN `products` p ON q.prod_id = p.prod_id
            LEFT JOIN `employees` e ON q.created_by_emp_id = e.emp_id
            ORDER BY q.quote_id DESC
        ");
        $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($quotes as &$q) {
            $q['status_display'] = I18n::translate((string)$q['status'], $lang);
        }
        unset($q);

        $totalQuotesVal = (float)array_sum(array_column($quotes, 'total_amount'));

        Response::success($quotes, 'Commercial quotes loaded from DB', 200, [
            'total_quotes' => count($quotes),
            'total_value'  => $totalQuotesVal
        ]);
    } catch (Throwable $e) {
        Response::error("Failed to load quotes: " . $e->getMessage(), 500);
    }
}

if ($method === 'POST') {
    $action = strtolower(trim((string)($data['action'] ?? '')));
    if ($action === 'delete' || $action === 'delete_quote') {
        goto handle_delete_quote;
    }

    $cusId     = trim((string)($data['cus_id'] ?? 'CUS-1001'));
    $scope     = trim((string)($data['equipment_scope'] ?? 'Industrial Telemetry Equipment Batch'));
    $amount    = (float)($data['total_amount'] ?? ($data['quoted_price'] ?? 500000));
    $ref       = trim((string)($data['quote_ref'] ?? ('QUO-' . rand(9000, 9999))));
    $prodId    = trim((string)($data['prod_id'] ?? 'PROD-1001'));
    $qty       = (int)($data['quantity'] ?? 1);
    $unitPrice = $qty > 0 ? ($amount / $qty) : $amount;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO `quotes` (
                quote_ref, cus_id, prod_id, quantity, unit_price,
                total_amount, equipment_scope, valid_until, created_by_emp_id, status
            ) VALUES (
                :ref, :cid, :pid, :qty, :up,
                :tot, :scope, DATE_ADD(CURDATE(), INTERVAL 30 DAY), :eid, 'Delivered'
            )
        ");
        $stmt->execute([
            ':ref'   => $ref,
            ':cid'   => $cusId,
            ':pid'   => $prodId,
            ':qty'   => $qty,
            ':up'    => $unitPrice,
            ':tot'   => $amount,
            ':scope' => $scope,
            ':eid'   => $empId
        ]);
        $quoteId = (int)$pdo->lastInsertId();

        AuditLogger::logAction($empId, $cusId, 'CRM Platform', 'CRM', 'CREATE_QUOTE', 'quotes', (string)$quoteId, ['ref' => $ref, 'amount' => $amount], 'SUCCESS');

        $pdo->prepare("INSERT INTO `crm_activities` (activity_type, title, description, cus_id, emp_id) VALUES ('Quote', 'Commercial Quotation Issued', :desc, :cid, :eid)")
            ->execute([
                ':desc' => "Commercial quotation {$ref} ($" . number_format($amount, 2) . ") delivered to {$cusId}.",
                ':cid'  => $cusId,
                ':eid'  => $empId
            ]);

        Response::success(['quote_id' => $quoteId, 'quote_ref' => $ref], 'Quote logged to DB', 201);
    } catch (Throwable $e) {
        Response::error("Failed to create quote: " . $e->getMessage(), 500);
    }
}

if ($method === 'PUT' || $method === 'PATCH' || ($method === 'POST' && in_array(strtolower(trim((string)($data['action'] ?? ''))), ['edit', 'update', 'update_quote', 'edit_quote'], true))) {
    $quoteId = (int)($data['quote_id'] ?? ($data['id'] ?? 0));
    if ($quoteId <= 0) {
        Response::error("Quote ID required for update", 422);
    }

    try {
        $fields = [];
        $params = [':id' => $quoteId];

        if (isset($data['equipment_scope']) && trim($data['equipment_scope']) !== '') {
            $fields[] = "`equipment_scope` = :scope";
            $params[':scope'] = trim($data['equipment_scope']);
        }
        if (isset($data['total_amount'])) {
            $fields[] = "`total_amount` = :amt";
            $params[':amt'] = (float)$data['total_amount'];
        }
        if (isset($data['status'])) {
            $fields[] = "`status` = :st";
            $params[':st'] = trim($data['status']);
        }

        if (empty($fields)) {
            Response::error("No fields provided for update", 422);
        }

        $sql = "UPDATE `quotes` SET " . implode(', ', $fields) . " WHERE `quote_id` = :id";
        $pdo->prepare($sql)->execute($params);

        AuditLogger::logAction($empId, null, 'CRM Platform', 'CRM', 'UPDATE_QUOTE', 'quotes', (string)$quoteId, $params, 'SUCCESS');
        Response::success(['quote_id' => $quoteId], 'Quote updated in DB');
    } catch (Throwable $e) {
        Response::error("Failed to update quote: " . $e->getMessage(), 500);
    }
}

if ($method === 'DELETE') {
    handle_delete_quote:
    $quoteId = (int)($data['id'] ?? ($data['quote_id'] ?? 0));
    if ($quoteId <= 0) {
        Response::error("Quote ID required for deletion", 422);
    }

    try {
        $pdo->prepare("DELETE FROM `quotes` WHERE quote_id = ?")->execute([$quoteId]);
        AuditLogger::logAction($empId, null, 'CRM Platform', 'CRM', 'DELETE_QUOTE', 'quotes', (string)$quoteId, [], 'SUCCESS');
        Response::success(['quote_id' => $quoteId], 'Quote removed');
    } catch (Throwable $e) {
        Response::error("Failed to delete quote: " . $e->getMessage(), 500);
    }
}
