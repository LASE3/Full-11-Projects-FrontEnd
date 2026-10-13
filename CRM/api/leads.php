<?php

/**
 * Class 5: CRM Platform - Leads API
 * Location: CRM/api/leads.php
 * Methods: GET, POST
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
$salesRep = $_SESSION['emp_id'] ?? ($_SESSION['vostok_user']['emp_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? ($_GET['emp_id'] ?? null)));
if (!$salesRep) {
    $salesRep = $pdo->query("SELECT emp_id FROM employees WHERE department_code = 'SAL' AND employment_status = 'Active' LIMIT 1")->fetchColumn() ?: null;
}
$lang     = $_GET['lang'] ?? 'en';
$method   = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $status = $_GET['status'] ?? null;
    $leadId = $_GET['id'] ?? null;

    try {
        if ($leadId) {
            $stmt = $pdo->prepare("
                SELECT 
                    l.*,
                    e.full_name AS assigned_sales_rep,
                    e.email AS assigned_sales_email,
                    c.company_name AS converted_customer_name
                FROM leads l
                LEFT JOIN employees e ON l.assigned_sales_emp_id = e.emp_id
                LEFT JOIN customers c ON l.converted_cus_id = c.cus_id
                WHERE l.lead_id = :id
            ");
            $stmt->execute([':id' => $leadId]);
            $lead = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$lead) {
                Response::error("Lead not found.", 404);
            }
            vp_authorize($_vp_user, 'crm:read', $lead, $pdo);

            $lead['status_display'] = I18n::translate($lead['status'], $lang);
            Response::success($lead, "Lead detail loaded");
        } else {
            $q = trim($_GET['q'] ?? '');
            $page = max(1, (int)($_GET['page'] ?? 1));
            $perPage = min(100, max(1, (int)($_GET['per_page'] ?? 50)));
            $offset = ($page - 1) * $perPage;

            $sortField = trim($_GET['sort'] ?? 'lead_id');
            $dir = strtoupper(trim($_GET['dir'] ?? 'DESC')) === 'ASC' ? 'ASC' : 'DESC';

            $allowedSorts = [
                'lead_id'      => 'l.lead_id',
                'full_name'    => 'l.full_name',
                'company_name' => 'l.company_name',
                'status'       => 'l.status',
                'created_at'   => 'l.created_at'
            ];
            $orderBy = $allowedSorts[$sortField] ?? 'l.lead_id';

            $whereClauses = [];
            $params = [];

            if ($status) {
                $whereClauses[] = "l.status = :st";
                $params[':st'] = $status;
            }

            if (!empty($q)) {
                $whereClauses[] = "(l.full_name LIKE :q1 OR l.company_name LIKE :q2 OR l.email LIKE :q3 OR l.phone LIKE :q4 OR e.full_name LIKE :q5)";
                $wildcard = '%' . $q . '%';
                $params[':q1'] = $wildcard;
                $params[':q2'] = $wildcard;
                $params[':q3'] = $wildcard;
                $params[':q4'] = $wildcard;
                $params[':q5'] = $wildcard;
            }

            $whereSql = !empty($whereClauses) ? ' WHERE ' . implode(' AND ', $whereClauses) : '';

            $countSql = "SELECT COUNT(*) FROM leads l LEFT JOIN employees e ON l.assigned_sales_emp_id = e.emp_id {$whereSql}";
            $countStmt = $pdo->prepare($countSql);
            $countStmt->execute($params);
            $total = (int)$countStmt->fetchColumn();

            $sql = "
                SELECT 
                    l.lead_id, l.full_name, l.email, l.phone, l.company_name,
                    l.message, l.source_page, l.status, l.created_at,
                    e.full_name AS assigned_sales_rep,
                    c.company_name AS converted_customer_name
                FROM leads l
                LEFT JOIN employees e ON l.assigned_sales_emp_id = e.emp_id
                LEFT JOIN customers c ON l.converted_cus_id = c.cus_id
                {$whereSql}
                ORDER BY {$orderBy} {$dir}
                LIMIT {$perPage} OFFSET {$offset}
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($leads as &$ld) {
                $ld['status_display'] = I18n::translate($ld['status'], $lang);
            }
            unset($ld);

            Response::paginated($leads, $total, $page, $perPage);
        }
    } catch (Throwable $e) {
        error_log("Failed to load leads: " . $e->getMessage());
        Response::error("Failed to load leads. Please try again later.", 500);
    }
}

if ($method === 'POST') {
    vp_authorize($_vp_user, 'crm:update', $_POST, $pdo);
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    $action = $_GET['action'] ?? 'create';

    if ($action === 'convert') {
        $leadId = (int)($data['lead_id'] ?? $_GET['lead_id'] ?? 0);
        $dealValue = (float)($data['estimated_value'] ?? 350000.00);

        try {
            $pdo->beginTransaction();

            $leadStmt = $pdo->prepare("SELECT * FROM leads WHERE lead_id = :id FOR UPDATE");
            $leadStmt->execute([':id' => $leadId]);
            $lead = $leadStmt->fetch(PDO::FETCH_ASSOC);

            if (!$lead) throw new Exception("Lead #{$leadId} does not exist.");
            if ($lead['status'] === 'Converted') throw new Exception("Lead is already converted.");

            // 1. Establish Master Customer Record if not existing
            $cusId = $lead['converted_cus_id'];
            if (!$cusId) {
                $cusId = vp_next_id($pdo, 'customers', 'CUS-', 4);

                $cusStmt = $pdo->prepare("
                    INSERT INTO customers (cus_id, company_name, sector, primary_contact_name, primary_contact_email, account_manager_emp_id, onboarded_at)
                    VALUES (:id, :comp, 'Industrial Automation', :contact, :email, :mgr, NOW())
                ");
                $cusStmt->execute([
                    ':id'      => $cusId,
                    ':comp'    => $lead['company_name'] ?: $lead['full_name'],
                    ':contact' => $lead['full_name'],
                    ':email'   => $lead['email'],
                    ':mgr'     => $salesRep
                ]);

                // Create primary contact entry
                $ctStmt = $pdo->prepare("INSERT INTO contacts (cus_id, full_name, role, email, phone) VALUES (:cid, :fn, 'Procurement Contact', :em, :ph)");
                $ctStmt->execute([':cid' => $cusId, ':fn' => $lead['full_name'], ':em' => $lead['email'], ':ph' => $lead['phone']]);
            }

            // 2. Instantiate Opportunity
            $oppStmt = $pdo->prepare("
                INSERT INTO opportunities (lead_id, cus_id, sales_emp_id, stage, estimated_value, expected_close_date)
                VALUES (:lid, :cid, :sid, 'Proposal', :val, DATE_ADD(CURDATE(), INTERVAL 45 DAY))
            ");
            $oppStmt->execute([
                ':lid' => $leadId,
                ':cid' => $cusId,
                ':sid' => $salesRep,
                ':val' => $dealValue
            ]);
            $oppId = $pdo->lastInsertId();

            // 3. Mark Lead as Converted
            $updStmt = $pdo->prepare("UPDATE leads SET status = 'Converted', converted_cus_id = :cid WHERE lead_id = :lid");
            $updStmt->execute([':cid' => $cusId, ':lid' => $leadId]);

            // 4. Log Audit Event
            AuditLogger::logAction(
                $salesRep,
                null,
                'CRM Platform',
                'CRM',
                'CONVERT_COMMERCIAL_LEAD',
                'opportunities',
                (string)$oppId,
                ['cus_id' => $cusId, 'deal_value' => $dealValue],
                'SUCCESS'
            );

            $pdo->commit();

            Response::success([
                'lead_id' => $leadId,
                'cus_id'  => $cusId,
                'opp_id'  => (int)$oppId,
                'stage'   => 'Proposal',
                'stage_display' => I18n::translate('Proposal', $lang)
            ], "Lead converted to Opportunity successfully", 201);
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Conversion failed: " . $e->getMessage());
        Response::error("Conversion failed. Please try again later.", 400);
        }
    } else {
        // Create new raw lead
        $fullName = trim($data['full_name'] ?? '');
        $email    = trim($data['email'] ?? '');
        $phone    = trim($data['phone'] ?? '');
        $company  = trim($data['company_name'] ?? '');
        $message  = trim($data['message'] ?? '');
        $source   = trim($data['source_page'] ?? 'CRM Manual Entry');

        if (empty($fullName) || empty($email)) {
            Response::error("Full name and email are required for lead creation.", 422);
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO leads (full_name, email, phone, company_name, message, source_page, status, assigned_sales_emp_id, created_at)
                VALUES (:fn, :em, :ph, :comp, :msg, :src, 'New', :rep, NOW())
            ");
            $stmt->execute([
                ':fn'   => $fullName,
                ':em'   => $email,
                ':ph'   => $phone,
                ':comp' => $company,
                ':msg'  => $message,
                ':src'  => $source,
                ':rep'  => $salesRep
            ]);
            $newLeadId = $pdo->lastInsertId();

            Response::success(['lead_id' => (int)$newLeadId], "Commercial Lead logged", 201);
        } catch (Exception $e) {
            error_log("Failed to create lead: " . $e->getMessage());
        Response::error("Failed to create lead. Please try again later.", 500);
        }
    }
}
