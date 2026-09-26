<?php

/**
 * Class 5: CRM Platform - Leads API
 * Location: api/v1/crm/leads.php
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
$salesRep = $_SESSION['emp_id'] ?? ($_GET['emp_id'] ?? 'EMP-1006');
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

            $lead['status_display'] = I18n::translate($lead['status'], $lang);
            Response::success($lead, "Lead detail loaded");
        } else {
            $sql = "
                SELECT 
                    l.lead_id, l.full_name, l.email, l.phone, l.company_name,
                    l.message, l.source_page, l.status, l.created_at,
                    e.full_name AS assigned_sales_rep,
                    c.company_name AS converted_customer_name
                FROM leads l
                LEFT JOIN employees e ON l.assigned_sales_emp_id = e.emp_id
                LEFT JOIN customers c ON l.converted_cus_id = c.cus_id
            ";

            if ($status) {
                $sql .= " WHERE l.status = :st ORDER BY l.lead_id DESC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':st' => $status]);
            } else {
                $sql .= " ORDER BY l.lead_id DESC";
                $stmt = $pdo->query($sql);
            }

            $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($leads as &$ld) {
                $ld['status_display'] = I18n::translate($ld['status'], $lang);
            }
            unset($ld);

            Response::success($leads, "Leads loaded");
        }
    } catch (Throwable $e) {
        Response::error("Failed to load leads: " . $e->getMessage(), 500);
    }
}

if ($method === 'POST') {
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
                $maxNum = $pdo->query("SELECT MAX(CAST(SUBSTRING(cus_id, 5) AS UNSIGNED)) FROM customers")->fetchColumn();
                $cusId = "CUS-" . (($maxNum ?: 1000) + 1);

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
            Response::error("Conversion failed: " . $e->getMessage(), 400);
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
            Response::error("Failed to create lead: " . $e->getMessage(), 500);
        }
    }
}
