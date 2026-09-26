<?php
/**
 * Class 5: CRM Platform - Customers 360 API
 * Location: api/v1/crm/customers.php
 * Methods: GET
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/I18n.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$lang  = $_GET['lang'] ?? 'en';
$cusId = $_GET['id'] ?? null;

try {
    if ($cusId) {
        // Deep Customer 360 Dossier
        $stmt = $pdo->prepare("
            SELECT 
                c.*,
                e.full_name AS account_manager_name,
                e.email AS account_manager_email,
                e.job_title AS account_manager_title
            FROM customers c
            LEFT JOIN employees e ON c.account_manager_emp_id = e.emp_id
            WHERE c.cus_id = :cid
        ");
        $stmt->execute([':cid' => $cusId]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$customer) {
            Response::error("Customer account not found.", 404);
        }

        // Linked Contacts
        $cStmt = $pdo->prepare("SELECT * FROM contacts WHERE cus_id = :cid");
        $cStmt->execute([':cid' => $cusId]);
        $customer['contacts'] = $cStmt->fetchAll(PDO::FETCH_ASSOC);

        // Linked Contracts
        $conStmt = $pdo->prepare("
            SELECT contract_id, prj_id, contract_value, start_date, end_date, status, doc_id
            FROM contracts 
            WHERE cus_id = :cid
            ORDER BY contract_id DESC
        ");
        $conStmt->execute([':cid' => $cusId]);
        $contracts = $conStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($contracts as &$con) {
            $con['status_display'] = I18n::translate($con['status'], $lang);
        }
        $customer['contracts'] = $contracts;

        // Linked Projects
        $pStmt = $pdo->prepare("
            SELECT prj_id, budget, currency, status, start_date, end_date
            FROM projects 
            WHERE cus_id = :cid
            ORDER BY prj_id ASC
        ");
        $pStmt->execute([':cid' => $cusId]);
        $projects = $pStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($projects as &$p) {
            $p['status_display'] = I18n::translate($p['status'], $lang);
        }
        $customer['projects'] = $projects;

        // Linked Quotes
        $qStmt = $pdo->prepare("
            SELECT q.quote_id, q.prod_id, p.product_name, q.quantity, q.unit_price, q.created_at
            FROM quotes q
            JOIN products p ON q.prod_id = p.prod_id
            WHERE q.cus_id = :cid
            ORDER BY q.quote_id DESC
        ");
        $qStmt->execute([':cid' => $cusId]);
        $customer['quotes'] = $qStmt->fetchAll(PDO::FETCH_ASSOC);

        Response::success($customer, "Customer 360 dossier loaded");
    } else {
        // Directory of CRM Enterprise Clients
        $stmt = $pdo->query("
            SELECT 
                c.cus_id,
                c.company_name,
                c.sector,
                c.primary_contact_name,
                c.onboarded_at,
                e.full_name AS account_manager_name,
                (SELECT COUNT(*) FROM projects WHERE cus_id = c.cus_id) AS total_projects,
                (SELECT COALESCE(SUM(contract_value), 0) FROM contracts WHERE cus_id = c.cus_id) AS total_contract_arr,
                (SELECT COUNT(*) FROM opportunities WHERE cus_id = c.cus_id AND stage != 'Lost') AS active_opportunities
            FROM customers c
            LEFT JOIN employees e ON c.account_manager_emp_id = e.emp_id
            ORDER BY total_contract_arr DESC, c.company_name ASC
        ");
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        Response::success($customers, "CRM customer accounts directory loaded", 200, ['total' => count($customers)]);
    }
} catch (Exception $e) {
    Response::error("Failed to load customer records: " . $e->getMessage(), 500);
}
