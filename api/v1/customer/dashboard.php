<?php
/**
 * Class 3: Customer Portal - Dashboard Metrics API
 * Location: api/v1/customer/dashboard.php
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

try {
    // 1. Core Profile & Aggregate Metrics
    $stmt = $pdo->prepare("
        SELECT 
            c.cus_id,
            c.company_name,
            c.sector,
            c.primary_contact_name,
            c.primary_contact_email,
            e.full_name AS account_manager_name,
            e.email AS account_manager_email,
            (SELECT COUNT(*) FROM projects WHERE cus_id = c.cus_id) AS total_projects,
            (SELECT COUNT(*) FROM projects WHERE cus_id = c.cus_id AND status IN ('Execution','Integration','Testing')) AS active_projects,
            (SELECT COUNT(*) FROM invoices WHERE cus_id = c.cus_id) AS total_invoices,
            (SELECT COUNT(*) FROM invoices WHERE cus_id = c.cus_id AND payment_status = 'Pending') AS pending_invoices,
            (SELECT COALESCE(SUM(total_value), 0) FROM invoices WHERE cus_id = c.cus_id AND payment_status = 'Pending') AS pending_balance_eur,
            (SELECT COUNT(*) FROM tickets WHERE requester_cus_id = c.cus_id AND status != 'Resolved') AS open_tickets,
            (SELECT COUNT(*) FROM orders WHERE cus_id = c.cus_id) AS total_orders
        FROM customers c
        LEFT JOIN employees e ON c.account_manager_emp_id = e.emp_id
        WHERE c.cus_id = :cid
    ");
    $stmt->execute([':cid' => $cusId]);
    $dashboard = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dashboard) {
        Response::error("Customer profile not found.", 404);
    }

    // 2. Recent Active Projects (Top 3)
    $prjStmt = $pdo->prepare("
        SELECT prj_id, budget, currency, status, start_date, end_date
        FROM projects
        WHERE cus_id = :cid
        ORDER BY prj_id ASC
        LIMIT 3
    ");
    $prjStmt->execute([':cid' => $cusId]);
    $recentProjects = $prjStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($recentProjects as &$p) {
        $p['status_display'] = I18n::translate($p['status'], $lang);
    }
    $dashboard['recent_projects'] = $recentProjects;

    // 3. Recent Invoices (Top 3)
    $invStmt = $pdo->prepare("
        SELECT inv_id, prj_id, total_value, currency, payment_status, issued_at
        FROM invoices
        WHERE cus_id = :cid
        ORDER BY inv_id DESC
        LIMIT 3
    ");
    $invStmt->execute([':cid' => $cusId]);
    $recentInvoices = $invStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($recentInvoices as &$inv) {
        $inv['status_display'] = I18n::translate($inv['payment_status'], $lang);
    }
    $dashboard['recent_invoices'] = $recentInvoices;

    // 4. Recent Tickets
    $tktStmt = $pdo->prepare("
        SELECT tkt_id, priority, status, created_at
        FROM tickets
        WHERE requester_cus_id = :cid
        ORDER BY created_at DESC
        LIMIT 3
    ");
    $tktStmt->execute([':cid' => $cusId]);
    $recentTickets = $tktStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($recentTickets as &$t) {
        $t['status_display'] = I18n::translate($t['status'], $lang);
        $t['priority_display'] = I18n::translate($t['priority'], $lang);
    }
    $dashboard['recent_tickets'] = $recentTickets;

    Response::success($dashboard, "Customer dashboard metrics loaded");
} catch (Exception $e) {
    Response::error("Failed to load dashboard: " . $e->getMessage(), 500);
}
