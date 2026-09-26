<?php
/**
 * Class 3: Customer Portal - Invoices & Payments API
 * Location: api/v1/customer/invoices.php
 * Methods: GET
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/I18n.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$cusId = $_SESSION['cus_id'] ?? ($_GET['cus_id'] ?? 'CUS-1001');
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

        $invoice['payment_status_display'] = I18n::translate($invoice['payment_status'], $lang);

        // Fetch payment lines
        $payStmt = $pdo->prepare("SELECT * FROM payments WHERE inv_id = :iid ORDER BY payment_date DESC");
        $payStmt->execute([':iid' => $invId]);
        $invoice['payments'] = $payStmt->fetchAll(PDO::FETCH_ASSOC);

        Response::success($invoice, "Invoice details loaded");
    } else {
        $stmt = $pdo->prepare("
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
            WHERE i.cus_id = :cid
            GROUP BY i.inv_id
            ORDER BY i.inv_id DESC
        ");
        $stmt->execute([':cid' => $cusId]);
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($invoices as &$inv) {
            $inv['payment_status_display'] = I18n::translate($inv['payment_status'], $lang);
        }

        Response::success($invoices, "Customer invoices loaded");
    }
} catch (Exception $e) {
    Response::error("Failed to load invoices: " . $e->getMessage(), 500);
}
