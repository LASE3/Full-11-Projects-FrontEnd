<?php

/**
 * Class 3: Customer Portal - Invoices & Payments API
 * Location: Customer Portal/api/invoices.php
 * Methods: GET
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('CUS', ['customer' => true]);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/I18n.php';
require_once __DIR__ . '/helpers/CustomerSession.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$cusId = getActiveCustomerPortalId($pdo);
$lang  = $_GET['lang'] ?? 'en';
$invId = $_GET['id'] ?? null;

try {
    if ($invId) {
        $stmt = $pdo->prepare("
            SELECT 
                i.*,
                p.budget AS project_budget,
                p.status AS project_status,
                c.company_name,
                c.primary_contact_name
            FROM invoices i
            JOIN customers c ON i.cus_id = c.cus_id
            LEFT JOIN projects p ON i.prj_id = p.prj_id
            WHERE i.inv_id = :iid AND i.cus_id = :cid
        ");
        $stmt->execute([':iid' => $invId, ':cid' => $cusId]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            Response::error("Invoice not found or unauthorized.", 404);
        }
        vp_authorize($_vp_user, 'finance:read', $invoice, $pdo);

        $invoice['payment_status_display'] = I18n::translate($invoice['payment_status'], $lang);

        // Fetch payment lines
        $payStmt = $pdo->prepare("SELECT * FROM payments WHERE inv_id = :iid ORDER BY payment_date DESC");
        $payStmt->execute([':iid' => $invId]);
        $invoice['payments'] = $payStmt->fetchAll(PDO::FETCH_ASSOC);

        Response::success($invoice, "Invoice details loaded");
    } else {
        $q = trim((string)($_GET['q'] ?? $_GET['search'] ?? ''));
        $allowedSorts = ['inv_id', 'total_value', 'currency', 'payment_status', 'issued_at', 'paid_at'];
        $sortField = in_array(strtolower((string)($_GET['sort'] ?? '')), $allowedSorts, true) ? strtolower((string)$_GET['sort']) : 'inv_id';
        $sortDir = strtoupper((string)($_GET['dir'] ?? 'DESC')) === 'ASC' ? 'ASC' : 'DESC';

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = min(100, max(1, (int)($_GET['per_page'] ?? 50)));
        $offset = ($page - 1) * $perPage;

        $whereSql = "WHERE i.cus_id = :cid";
        $params = [':cid' => $cusId];

        if ($q !== '') {
            $whereSql .= " AND (i.inv_id LIKE :q1 OR i.prj_id LIKE :q2)";
            $params[':q1'] = "%{$q}%";
            $params[':q2'] = "%{$q}%";
        }

        // Total count
        $countStmt = $pdo->prepare("SELECT COUNT(DISTINCT i.inv_id) FROM invoices i {$whereSql}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $sql = "
            SELECT 
                i.inv_id,
                i.prj_id,
                i.total_value,
                i.currency,
                i.payment_status,
                i.issued_at,
                i.paid_at,
                COALESCE(SUM(p.amount), 0) AS total_paid_amount
            FROM invoices i
            LEFT JOIN payments p ON i.inv_id = p.inv_id
            {$whereSql}
            GROUP BY i.inv_id
            ORDER BY i.{$sortField} {$sortDir}
            LIMIT {$perPage} OFFSET {$offset}
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($invoices as &$inv) {
            $inv['payment_status_display'] = I18n::translate($inv['payment_status'], $lang);
        }

        Response::paginated($invoices, $total, $page, $perPage, "Customer invoices loaded");
    }
} catch (Exception $e) {
    error_log("Failed to load invoices: " . $e->getMessage());
        Response::error("Failed to load invoices. Please try again later.", 500);
}
