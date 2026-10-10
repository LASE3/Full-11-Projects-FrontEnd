<?php

declare(strict_types=1);

/**
 * Class 5: CRM Platform - Customers 360 API
 * Location: CRM/api/customers.php
 * Methods: GET, POST, PUT, PATCH, DELETE
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('CRM', []);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/I18n.php';
require_once __DIR__ . '/helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$lang = $_GET['lang'] ?? 'en';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$empId = $_SESSION['emp_id'] ?? ($_SESSION['vostok_user']['emp_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? null));

if ($method === 'GET') {
    $cusId = $_GET['id'] ?? ($_GET['cus_id'] ?? null);

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
            $cStmt = $pdo->prepare("SELECT * FROM contacts WHERE cus_id = :cid ORDER BY contact_id ASC");
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
                SELECT prj_id, project_name, budget, currency, status, start_date, end_date
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

            // Linked Opportunities
            $oppStmt = $pdo->prepare("SELECT * FROM opportunities WHERE cus_id = :cid ORDER BY estimated_value DESC");
            $oppStmt->execute([':cid' => $cusId]);
            $customer['opportunities'] = $oppStmt->fetchAll(PDO::FETCH_ASSOC);

            Response::success($customer, "Customer 360 dossier loaded");
        } else {
            // Directory of CRM Enterprise Clients
            $sectorParam = trim($_GET['sector'] ?? '');
            if (!empty($sectorParam) && strtolower($sectorParam) !== 'all') {
                $stmt = $pdo->prepare("
                    SELECT 
                        c.cus_id,
                        c.company_name,
                        c.sector,
                        c.headquarters,
                        c.health_score,
                        c.account_tier,
                        c.primary_contact_name,
                        c.primary_contact_email,
                        c.onboarded_at,
                        e.full_name AS account_manager_name,
                        (SELECT COUNT(*) FROM projects WHERE cus_id = c.cus_id) AS total_projects,
                        (SELECT COALESCE(SUM(contract_value), 0) FROM contracts WHERE cus_id = c.cus_id AND status = 'Active') AS total_contract_arr,
                        (SELECT contract_ref FROM contracts WHERE cus_id = c.cus_id AND status = 'Active' ORDER BY contract_value DESC LIMIT 1) AS active_contract_ref,
                        (SELECT end_date FROM contracts WHERE cus_id = c.cus_id AND status = 'Active' ORDER BY contract_value DESC LIMIT 1) AS contract_end,
                        (SELECT COUNT(*) FROM opportunities WHERE cus_id = c.cus_id AND stage != 'Lost') AS active_opportunities
                    FROM customers c
                    LEFT JOIN employees e ON c.account_manager_emp_id = e.emp_id
                    WHERE LOWER(c.sector) LIKE LOWER(:sector)
                    ORDER BY total_contract_arr DESC, c.company_name ASC
                ");
                $stmt->execute([':sector' => '%' . $sectorParam . '%']);
            } else {
                $stmt = $pdo->query("
                    SELECT 
                        c.cus_id,
                        c.company_name,
                        c.sector,
                        c.headquarters,
                        c.health_score,
                        c.account_tier,
                        c.primary_contact_name,
                        c.primary_contact_email,
                        c.onboarded_at,
                        e.full_name AS account_manager_name,
                        (SELECT COUNT(*) FROM projects WHERE cus_id = c.cus_id) AS total_projects,
                        (SELECT COALESCE(SUM(contract_value), 0) FROM contracts WHERE cus_id = c.cus_id AND status = 'Active') AS total_contract_arr,
                        (SELECT contract_ref FROM contracts WHERE cus_id = c.cus_id AND status = 'Active' ORDER BY contract_value DESC LIMIT 1) AS active_contract_ref,
                        (SELECT end_date FROM contracts WHERE cus_id = c.cus_id AND status = 'Active' ORDER BY contract_value DESC LIMIT 1) AS contract_end,
                        (SELECT COUNT(*) FROM opportunities WHERE cus_id = c.cus_id AND stage != 'Lost') AS active_opportunities
                    FROM customers c
                    LEFT JOIN employees e ON c.account_manager_emp_id = e.emp_id
                    ORDER BY total_contract_arr DESC, c.company_name ASC
                ");
            }
            $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            Response::success($customers, "CRM customer accounts directory loaded", 200, ['total' => count($customers)]);
        }
    } catch (Throwable $e) {
        Response::error("Failed to load customer records: " . $e->getMessage(), 500);
    }
}

if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?: $_POST;

    $companyName = trim($data['company_name'] ?? '');
    $sector = trim($data['sector'] ?? 'Industrial Technology');
    $contactName = trim($data['primary_contact_name'] ?? '');
    $contactEmail = trim($data['primary_contact_email'] ?? '');
    $mgrEmpId = trim($data['account_manager_emp_id'] ?? '');
    if (empty($mgrEmpId)) {
        $mgrEmpId = $empId ?: ($pdo->query("SELECT emp_id FROM employees WHERE department_code = 'SAL' AND employment_status = 'Active' LIMIT 1")->fetchColumn() ?: null);
    }

    if (empty($companyName)) {
        Response::error("Company name is required.", 422);
    }

    try {
        $newCusId = vp_next_id($pdo, 'customers', 'CUS-', 4);

        $stmt = $pdo->prepare("
            INSERT INTO customers (cus_id, company_name, sector, primary_contact_name, primary_contact_email, account_manager_emp_id, onboarded_at)
            VALUES (:id, :comp, :sec, :cname, :cemail, :mgr, NOW())
        ");
        $stmt->execute([
            ':id'     => $newCusId,
            ':comp'   => $companyName,
            ':sec'    => $sector,
            ':cname'  => $contactName,
            ':cemail' => $contactEmail,
            ':mgr'    => $mgrEmpId
        ]);

        AuditLogger::logAction(
            $empId,
            $newCusId,
            'CRM Platform',
            'CRM',
            'CREATE_CUSTOMER_ACCOUNT',
            'customers',
            $newCusId,
            ['company_name' => $companyName, 'sector' => $sector],
            'SUCCESS'
        );

        Response::success(['cus_id' => $newCusId, 'company_name' => $companyName], "Customer account created", 201);
    } catch (Throwable $e) {
        Response::error("Failed to create customer: " . $e->getMessage(), 500);
    }
}

if ($method === 'PUT' || $method === 'PATCH') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?: $_POST;
    $cusId = trim($data['cus_id'] ?? ($_GET['id'] ?? ''));

    if (empty($cusId)) {
        Response::error("Customer ID required.", 422);
    }

    $fields = [];
    $params = [':cid' => $cusId];

    $allowed = ['company_name', 'sector', 'primary_contact_name', 'primary_contact_email', 'account_manager_emp_id'];
    foreach ($allowed as $col) {
        if (array_key_exists($col, $data)) {
            $fields[] = "`$col` = :$col";
            $params[":$col"] = trim((string)$data[$col]);
        }
    }

    if (empty($fields)) {
        Response::error("No updatable fields provided.", 422);
    }

    try {
        $sql = "UPDATE customers SET " . implode(", ", $fields) . " WHERE cus_id = :cid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        AuditLogger::logAction(
            $empId,
            $cusId,
            'CRM Platform',
            'CRM',
            'UPDATE_CUSTOMER_ACCOUNT',
            'customers',
            $cusId,
            $data,
            'SUCCESS'
        );

        Response::success(['cus_id' => $cusId], "Customer account updated");
    } catch (Throwable $e) {
        Response::error("Failed to update customer: " . $e->getMessage(), 500);
    }
}

if ($method === 'DELETE') {
    $cusId = trim($_GET['id'] ?? $_GET['cus_id'] ?? '');
    if (empty($cusId)) {
        Response::error("Customer ID required for deletion.", 422);
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM customers WHERE cus_id = ?");
        $stmt->execute([$cusId]);

        AuditLogger::logAction(
            $empId,
            $cusId,
            'CRM Platform',
            'CRM',
            'DELETE_CUSTOMER_ACCOUNT',
            'customers',
            $cusId,
            [],
            'SUCCESS'
        );

        Response::success(['cus_id' => $cusId], "Customer account removed");
    } catch (Throwable $e) {
        Response::error("Failed to delete customer: " . $e->getMessage(), 500);
    }
}
