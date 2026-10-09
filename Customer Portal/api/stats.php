<?php
declare(strict_types=1);

/**
 * Customer Portal - Live Sidebar Badge Statistics API
 * Location: Customer Portal/api/stats.php
 * Returns real-time counts for active customer orders, projects, invoices, documents, and tickets.
 */

require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('CUS', ['customer' => true]);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/CustomerSession.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$cusId = getActiveCustomerPortalId($pdo);

try {
    // 1. Orders count
    $stmtOrders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE cus_id = :cid");
    $stmtOrders->execute([':cid' => $cusId]);
    $ordersCount = (int)$stmtOrders->fetchColumn();

    // 2. Projects count
    $stmtProjects = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE cus_id = :cid");
    $stmtProjects->execute([':cid' => $cusId]);
    $projectsCount = (int)$stmtProjects->fetchColumn();

    // 3. Invoices count
    $stmtInvoices = $pdo->prepare("SELECT COUNT(*) FROM invoices WHERE cus_id = :cid");
    $stmtInvoices->execute([':cid' => $cusId]);
    $invoicesCount = (int)$stmtInvoices->fetchColumn();

    // 4. Documents count (directly linked or via customer's projects)
    $stmtDocs = $pdo->prepare("
        SELECT COUNT(*) FROM documents 
        WHERE related_cus_id = :cid 
           OR related_prj_id IN (SELECT prj_id FROM projects WHERE cus_id = :cid2)
    ");
    $stmtDocs->execute([':cid' => $cusId, ':cid2' => $cusId]);
    $documentsCount = (int)$stmtDocs->fetchColumn();

    // 5. Support tickets count
    $stmtTickets = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE requester_cus_id = :cid");
    $stmtTickets->execute([':cid' => $cusId]);
    $ticketsCount = (int)$stmtTickets->fetchColumn();

    Response::success([
        'cus_id'     => $cusId,
        'orders'     => $ordersCount,
        'projects'   => $projectsCount,
        'invoices'   => $invoicesCount,
        'documents'  => $documentsCount,
        'tickets'    => $ticketsCount,
        'timestamp'  => date('Y-m-d H:i:s')
    ], 'Customer portal statistics loaded');
} catch (Exception $e) {
    Response::error("Failed to load statistics: " . $e->getMessage(), 500);
}
