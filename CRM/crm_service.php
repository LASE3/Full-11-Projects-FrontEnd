<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR CRM Platform - Core Database Service Controller (Class 5)
 * Handles all database operations, state transitions, and CRUD actions for CRM.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/AuditLogger.php';
require_once __DIR__ . '/../includes/integration_bus.php';

/**
 * Helper to respond with JSON for AJAX actions.
 */
function crm_jsonReply(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

/**
 * Get current authenticated CRM user.
 */
function crm_getCurrentUser(): array
{
    if (!empty($_SESSION['vostok_user'])) {
        return $_SESSION['vostok_user'];
    }
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->query("SELECT emp_id AS user_id, emp_id, full_name, job_title, clearance_level, department_code FROM employees WHERE department_code = 'SAL' AND employment_status = 'Active' LIMIT 1");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) return $user;
    } catch (Throwable $e) {}

    return [
        'user_id' => 'EMP-0001',
        'emp_id' => 'EMP-0001',
        'full_name' => 'System Admin',
        'job_title' => 'Administrator',
        'clearance_level' => 'L4',
        'department_code' => 'EXE'
    ];
}

// ============================================================================
// 1. DASHBOARD & PIPELINE METRICS
// ============================================================================

function crm_getDashboardMetrics(): array
{
    $pdo = getDbConnection();

    // Inbound & Outbound leads count
    $leadCount = (int)$pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
    $uncontactedLeads = (int)$pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'New'")->fetchColumn();
    $qualifiedLeads = (int)$pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'Qualified'")->fetchColumn();

    // Opportunities count & pipeline total
    $oppCount = (int)$pdo->query("SELECT COUNT(*) FROM opportunities WHERE stage != 'Lost'")->fetchColumn();
    $pipelineVal = (float)$pdo->query("SELECT COALESCE(SUM(estimated_value), 0) FROM opportunities WHERE stage != 'Lost'")->fetchColumn();
    $wonVal = (float)$pdo->query("SELECT COALESCE(SUM(estimated_value), 0) FROM opportunities WHERE stage = 'Won'")->fetchColumn();

    // Customers count & ARR
    $custCount = (int)$pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
    $totalContractARR = (float)$pdo->query("SELECT COALESCE(SUM(contract_value), 0) FROM contracts WHERE status = 'Active'")->fetchColumn();

    // Opportunity Stage breakdown
    $stagesStmt = $pdo->query("
        SELECT stage, COUNT(*) AS count, COALESCE(SUM(estimated_value), 0) AS total_val
        FROM opportunities
        GROUP BY stage
    ");
    $stageStats = [
        'Qualification' => ['count' => 0, 'val' => 0.0],
        'Proposal'      => ['count' => 0, 'val' => 0.0],
        'Negotiation'   => ['count' => 0, 'val' => 0.0],
        'Won'           => ['count' => 0, 'val' => 0.0],
        'Lost'          => ['count' => 0, 'val' => 0.0]
    ];
    while ($row = $stagesStmt->fetch(PDO::FETCH_ASSOC)) {
        if (isset($stageStats[$row['stage']])) {
            $stageStats[$row['stage']] = [
                'count' => (int)$row['count'],
                'val'   => (float)$row['total_val']
            ];
        }
    }

    // Recent deals
    $recentDealsStmt = $pdo->query("
        SELECT 
            o.opp_id,
            o.stage,
            o.estimated_value,
            o.expected_close_date,
            c.cus_id,
            c.company_name,
            c.sector,
            e.full_name AS sales_rep_name
        FROM opportunities o
        LEFT JOIN customers c ON o.cus_id = c.cus_id
        LEFT JOIN employees e ON o.sales_emp_id = e.emp_id
        ORDER BY o.opp_id DESC
        LIMIT 5
    ");
    $recentDeals = $recentDealsStmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'lead_count'         => $leadCount,
        'uncontacted_leads'  => $uncontactedLeads,
        'qualified_leads'    => $qualifiedLeads,
        'opp_count'          => $oppCount,
        'pipeline_val'       => $pipelineVal,
        'won_val'            => $wonVal,
        'cust_count'         => $custCount,
        'total_contract_arr' => $totalContractARR,
        'stage_stats'        => $stageStats,
        'recent_deals'       => $recentDeals
    ];
}

// ============================================================================
// 2. LEADS CRUD & CONVERSION
// ============================================================================

function crm_getLeads(array $filters = []): array
{
    $pdo = getDbConnection();
    $where = [];
    $params = [];

    if (!empty($filters['status']) && $filters['status'] !== 'all') {
        $where[] = "l.status = :status";
        $params[':status'] = $filters['status'];
    }

    if (!empty($filters['search'])) {
        $where[] = "(l.full_name LIKE :s OR l.company_name LIKE :s OR l.email LIKE :s OR l.message LIKE :s)";
        $params[':s'] = '%' . $filters['search'] . '%';
    }

    $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

    $stmt = $pdo->prepare("
        SELECT 
            l.*,
            e.full_name AS assigned_sales_rep,
            e.email AS assigned_sales_email,
            c.company_name AS converted_customer_name
        FROM leads l
        LEFT JOIN employees e ON l.assigned_sales_emp_id = e.emp_id
        LEFT JOIN customers c ON l.converted_cus_id = c.cus_id
        {$whereSql}
        ORDER BY l.lead_id DESC
    ");
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function crm_getLead(int $leadId): ?array
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("
        SELECT 
            l.*,
            e.full_name AS assigned_sales_rep,
            c.company_name AS converted_customer_name
        FROM leads l
        LEFT JOIN employees e ON l.assigned_sales_emp_id = e.emp_id
        LEFT JOIN customers c ON l.converted_cus_id = c.cus_id
        WHERE l.lead_id = ?
    ");
    $stmt->execute([$leadId]);
    $lead = $stmt->fetch(PDO::FETCH_ASSOC);
    return $lead ?: null;
}

function crm_createLead(array $data, ?string $salesRepEmpId = null): int
{
    $pdo = getDbConnection();
    $user = crm_getCurrentUser();
    $repId = $salesRepEmpId ?: ($data['assigned_sales_emp_id'] ?? $user['emp_id']);

    $stmt = $pdo->prepare("
        INSERT INTO leads 
        (full_name, email, phone, company_name, message, source_page, status, assigned_sales_emp_id, created_at)
        VALUES 
        (:fn, :em, :ph, :comp, :msg, :src, :st, :rep, NOW())
    ");
    $stmt->execute([
        ':fn'   => trim($data['full_name'] ?? 'Inbound Contact'),
        ':em'   => trim($data['email'] ?? ''),
        ':ph'   => trim($data['phone'] ?? ''),
        ':comp' => trim($data['company_name'] ?? ''),
        ':msg'  => trim($data['message'] ?? ''),
        ':src'  => trim($data['source_page'] ?? 'CRM Direct Entry'),
        ':st'   => trim($data['status'] ?? 'New'),
        ':rep'  => $repId
    ]);

    $newId = (int)$pdo->lastInsertId();
    try {
        vp_emit($pdo, 'CRM_TO_ADM', 'CRM', 'ADM', 'LEAD_CREATED', [
            'lead_id'  => $newId,
            'summary'  => "Created lead #{$newId}",
            'endpoint' => '/leads/create'
        ], $user['emp_id'], 201);
    } catch (Throwable $e) {
        error_log("Failed to emit CRM_TO_ADM: " . $e->getMessage());
    }
    return $newId;
}

function crm_updateLead(int $leadId, array $data): bool
{
    $pdo = getDbConnection();
    $fields = [];
    $params = [':id' => $leadId];

    $allowed = ['full_name', 'email', 'phone', 'company_name', 'message', 'source_page', 'status', 'assigned_sales_emp_id'];
    foreach ($allowed as $col) {
        if (array_key_exists($col, $data)) {
            $fields[] = "`$col` = :$col";
            $params[":$col"] = $data[$col];
        }
    }

    if (empty($fields)) {
        return false;
    }

    $sql = "UPDATE leads SET " . implode(", ", $fields) . " WHERE lead_id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

function crm_deleteLead(int $leadId): bool
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("DELETE FROM leads WHERE lead_id = ?");
    return $stmt->execute([$leadId]);
}

function crm_convertLead(int $leadId, float $dealValue, ?string $salesRep = null): array
{
    $pdo = getDbConnection();
    $user = crm_getCurrentUser();
    $salesRep = $salesRep ?: $user['emp_id'];

    $startedTx = false;
    if (!$pdo->inTransaction()) {
        $pdo->beginTransaction();
        $startedTx = true;
    }

    try {
        $leadStmt = $pdo->prepare("SELECT * FROM leads WHERE lead_id = :id FOR UPDATE");
        $leadStmt->execute([':id' => $leadId]);
        $lead = $leadStmt->fetch(PDO::FETCH_ASSOC);

        if (!$lead) {
            throw new Exception("Lead #{$leadId} does not exist.");
        }
        if ($lead['status'] === 'Converted') {
            throw new Exception("Lead #{$leadId} is already converted.");
        }

        // Establish customer
        $cusId = $lead['converted_cus_id'];
        if (!$cusId) {
            $cusId = vp_next_id($pdo, 'customers', 'CUS-', 4);

            $companyName = !empty($lead['company_name']) ? $lead['company_name'] : $lead['full_name'];

            $cusStmt = $pdo->prepare("
                INSERT INTO customers 
                (cus_id, company_name, sector, primary_contact_name, primary_contact_email, account_manager_emp_id, onboarded_at)
                VALUES 
                (:id, :comp, 'Industrial Manufacturing', :contact, :email, :mgr, NOW())
            ");
            $cusStmt->execute([
                ':id'      => $cusId,
                ':comp'    => $companyName,
                ':contact' => $lead['full_name'],
                ':email'   => $lead['email'],
                ':mgr'     => $salesRep
            ]);

            // Add contact record
            $ctStmt = $pdo->prepare("
                INSERT INTO contacts (cus_id, full_name, role, email, phone) 
                VALUES (:cid, :fn, 'Procurement Contact', :em, :ph)
            ");
            $ctStmt->execute([
                ':cid' => $cusId,
                ':fn'  => $lead['full_name'],
                ':em'  => $lead['email'],
                ':ph'  => $lead['phone']
            ]);

            // Add customer_accounts row with one-time invite token (not a password)
            $inviteToken = bin2hex(random_bytes(24));
            $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $companyName));
            if (empty($baseUsername)) $baseUsername = 'client';
            $accUsername = substr($baseUsername, 0, 15) . '_' . substr($cusId, 4);

            $accStmt = $pdo->prepare("
                INSERT INTO customer_accounts (cus_id, username, email, password_hash, invite_token, status, created_at)
                VALUES (:cid, :usr, :em, NULL, :tok, 'Invited', NOW())
                ON DUPLICATE KEY UPDATE invite_token = VALUES(invite_token)
            ");
            $accStmt->execute([
                ':cid' => $cusId,
                ':usr' => $accUsername,
                ':em'  => $lead['email'],
                ':tok' => $inviteToken
            ]);

            // Create File Center customer workspace (documents folder row with related_cus_id, owner = account manager)
            $docWsId = 'DOC-WS-' . substr($cusId, 4);
            $wsStmt = $pdo->prepare("
                INSERT INTO documents 
                (doc_id, file_name, description, classification, folder, department, file_size, status, retention_period, customer_ref, owner_emp_id, related_cus_id, created_at)
                VALUES (:did, :fname, :desc, 'Confidential', 'customer_workspace', 'SAL', '0 KB', 'Approved', '7y', :cref, :owner, :cid, NOW())
                ON DUPLICATE KEY UPDATE file_name = VALUES(file_name)
            ");
            $wsStmt->execute([
                ':did'   => $docWsId,
                ':fname' => "Workspace - {$companyName}",
                ':desc'  => "Dedicated File Center enterprise workspace for {$companyName} ({$cusId})",
                ':cref'  => $companyName,
                ':owner' => $salesRep,
                ':cid'   => $cusId
            ]);

            // Create finance relationship (billing_cycles row)
            $bcStmt = $pdo->prepare("
                INSERT INTO billing_cycles (prj_id, cus_id, milestone_description, milestone_amount, scheduled_date, invoiced)
                VALUES (NULL, :cid, :desc, 0.00, CURDATE(), 0)
            ");
            $bcStmt->execute([
                ':cid'  => $cusId,
                ':desc' => "Commercial Account Onboarding & Master Billing Setup for {$companyName}"
            ]);
        }

        // Create opportunity
        $oppStmt = $pdo->prepare("
            INSERT INTO opportunities 
            (lead_id, cus_id, sales_emp_id, stage, estimated_value, expected_close_date)
            VALUES 
            (:lid, :cid, :sid, 'Proposal', :val, DATE_ADD(CURDATE(), INTERVAL 45 DAY))
        ");
        $oppStmt->execute([
            ':lid' => $leadId,
            ':cid' => $cusId,
            ':sid' => $salesRep,
            ':val' => $dealValue
        ]);
        $oppId = (int)$pdo->lastInsertId();

        // Update lead status
        $updLead = $pdo->prepare("UPDATE leads SET status = 'Converted', converted_cus_id = :cid WHERE lead_id = :lid");
        $updLead->execute([':cid' => $cusId, ':lid' => $leadId]);

        // Emit CRM_TO_CUS, CRM_TO_DOC, CRM_TO_FIN, plus governance to ADM
        try {
            vp_emit($pdo, 'CRM_TO_CUS', 'CRM', 'CUS', 'CUSTOMER_ONBOARDED', [
                'cus_id'  => $cusId,
                'lead_id' => $leadId,
                'opp_id'  => $oppId,
                'summary' => "Customer {$cusId} provisioned from Lead #{$leadId}"
            ], $salesRep, 201);

            vp_emit($pdo, 'CRM_TO_DOC', 'CRM', 'DOC', 'WORKSPACE_PROVISIONED', [
                'cus_id'  => $cusId,
                'doc_id'  => 'DOC-WS-' . substr($cusId, 4),
                'summary' => "File Center customer workspace created for {$cusId}"
            ], $salesRep, 201);

            vp_emit($pdo, 'CRM_TO_FIN', 'CRM', 'FIN', 'BILLING_RELATIONSHIP_CREATED', [
                'cus_id'  => $cusId,
                'summary' => "Master billing schedule established for customer {$cusId}"
            ], $salesRep, 201);

            vp_emit($pdo, 'CRM_TO_ADM', 'CRM', 'ADM', 'CUSTOMER_ACCOUNT_PROVISIONED', [
                'cus_id'      => $cusId,
                'governance'  => true,
                'severity'    => 'High',
                'summary'     => "Customer account {$cusId} provisioned with invite token",
                'description' => "Customer account {$cusId} provisioned with invite token"
            ], $salesRep, 200);
        } catch (Throwable $e) {
            error_log("Failed to emit onboarding integration events: " . $e->getMessage());
        }

        if ($startedTx) {
            $pdo->commit();
        }

        return [
            'success' => true,
            'lead_id' => $leadId,
            'cus_id'  => $cusId,
            'opp_id'  => $oppId,
            'stage'   => 'Proposal'
        ];
    } catch (Exception $e) {
        if ($startedTx && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

// ============================================================================
// 3. OPPORTUNITIES CRUD & KANBAN
// ============================================================================

function crm_getOpportunities(array $filters = []): array
{
    $pdo = getDbConnection();
    $where = [];
    $params = [];

    if (!empty($filters['stage']) && $filters['stage'] !== 'all') {
        $where[] = "o.stage = :stage";
        $params[':stage'] = $filters['stage'];
    }

    if (!empty($filters['sales_emp_id'])) {
        $where[] = "o.sales_emp_id = :eid";
        $params[':eid'] = $filters['sales_emp_id'];
    }

    if (!empty($filters['search'])) {
        $where[] = "(c.company_name LIKE :s OR c.sector LIKE :s OR e.full_name LIKE :s)";
        $params[':s'] = '%' . $filters['search'] . '%';
    }

    $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

    $stmt = $pdo->prepare("
        SELECT 
            o.*,
            c.company_name,
            c.sector,
            c.primary_contact_name,
            e.full_name AS sales_representative,
            e.email AS sales_rep_email
        FROM opportunities o
        LEFT JOIN customers c ON o.cus_id = c.cus_id
        LEFT JOIN employees e ON o.sales_emp_id = e.emp_id
        {$whereSql}
        ORDER BY o.estimated_value DESC, o.opp_id DESC
    ");
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function crm_getOpportunity(int $oppId): ?array
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("
        SELECT 
            o.*,
            c.company_name,
            c.sector,
            c.primary_contact_name,
            c.primary_contact_email,
            e.full_name AS sales_representative
        FROM opportunities o
        LEFT JOIN customers c ON o.cus_id = c.cus_id
        LEFT JOIN employees e ON o.sales_emp_id = e.emp_id
        WHERE o.opp_id = ?
    ");
    $stmt->execute([$oppId]);
    $opp = $stmt->fetch(PDO::FETCH_ASSOC);
    return $opp ?: null;
}

function crm_createOpportunity(array $data, ?string $salesRep = null): int
{
    $pdo = getDbConnection();
    $user = crm_getCurrentUser();
    $salesRep = $salesRep ?: ($data['sales_emp_id'] ?? $user['emp_id']);

    $allowedStages = ['Qualification', 'Proposal', 'Negotiation', 'Won', 'Lost'];
    $stage = ucfirst(strtolower(trim((string)($data['stage'] ?? 'Qualification'))));
    if (!in_array($stage, $allowedStages, true)) {
        $stage = 'Qualification';
    }

    $cusId = trim((string)($data['cus_id'] ?? ''));
    if (empty($cusId)) {
        throw new InvalidArgumentException("Customer ID (cus_id) is required to create an opportunity.");
    }

    $stmt = $pdo->prepare("
        INSERT INTO opportunities 
        (lead_id, cus_id, sales_emp_id, stage, estimated_value, expected_close_date)
        VALUES 
        (:lid, :cid, :sid, :st, :val, :dt)
    ");
    $stmt->execute([
        ':lid' => !empty($data['lead_id']) ? (int)$data['lead_id'] : null,
        ':cid' => $cusId,
        ':sid' => $salesRep,
        ':st'  => $stage,
        ':val' => (float)($data['estimated_value'] ?? 100000.00),
        ':dt'  => !empty($data['expected_close_date']) ? $data['expected_close_date'] : date('Y-m-d', strtotime('+60 days'))
    ]);

    $newId = (int)$pdo->lastInsertId();
    try {
        vp_emit($pdo, 'CRM_TO_ADM', 'CRM', 'ADM', 'OPPORTUNITY_CREATED', [
            'opp_id'   => $newId,
            'stage'    => $stage,
            'summary'  => "Created Opp #{$newId} ({$stage})",
            'endpoint' => '/opportunities/create'
        ], $salesRep, 201);
    } catch (Throwable $e) {
        error_log("Failed to emit CRM_TO_ADM: " . $e->getMessage());
    }
    return $newId;
}

function crm_updateOpportunityStage(int $oppId, string $newStage, ?string $empId = null): bool
{
    $pdo = getDbConnection();
    $user = crm_getCurrentUser();
    $empId = $empId ?: $user['emp_id'];

    $allowedStages = ['Qualification', 'Proposal', 'Negotiation', 'Won', 'Lost'];
    $newStage = ucfirst(strtolower(trim($newStage)));
    if (!in_array($newStage, $allowedStages, true)) {
        throw new InvalidArgumentException("Invalid pipeline stage: {$newStage}. Allowed: " . implode(', ', $allowedStages));
    }

    $stmt = $pdo->prepare("UPDATE opportunities SET stage = :st WHERE opp_id = :id");
    $res = $stmt->execute([':st' => $newStage, ':id' => $oppId]);

    // Flow C: CRM opportunity won -> project (SOP-02)
    if ($newStage === 'Won') {
        try {
            $oppStmt = $pdo->prepare("SELECT o.*, c.company_name FROM opportunities o LEFT JOIN customers c ON o.cus_id = c.cus_id WHERE o.opp_id = ?");
            $oppStmt->execute([$oppId]);
            $opp = $oppStmt->fetch(PDO::FETCH_ASSOC);

            if ($opp && !empty($opp['cus_id'])) {
                // Next PRJ-2026-xxx via id_counters
                $counterStmt = $pdo->prepare("SELECT next_val FROM id_counters WHERE name = 'projects' FOR UPDATE");
                $counterStmt->execute();
                $prjNum = (int)$counterStmt->fetchColumn();
                if ($prjNum < 1) $prjNum = 16;
                $prjId = 'PRJ-2026-' . str_pad((string)$prjNum, 3, '0', STR_PAD_LEFT);
                $pdo->prepare("UPDATE id_counters SET next_val = ? WHERE name = 'projects'")->execute([$prjNum + 1]);

                // Assign project manager from ENG (EMP-1016..1020)
                $engPms = ['EMP-1016', 'EMP-1017', 'EMP-1018', 'EMP-1019', 'EMP-1020'];
                $pmEmpId = $engPms[$prjNum % count($engPms)];

                // Insert into projects
                $prjStmt = $pdo->prepare("
                    INSERT INTO projects 
                    (prj_id, project_name, cus_id, project_manager_emp_id, budget, currency, status, start_date, progress_percent, scope_summary)
                    VALUES 
                    (:pid, :pname, :cid, :pm, :bgt, 'EUR', 'Execution', CURDATE(), 5, :scope)
                ");
                $prjStmt->execute([
                    ':pid'   => $prjId,
                    ':pname' => "Engineering Project {$prjId}",
                    ':cid'   => $opp['cus_id'],
                    ':pm'    => $pmEmpId,
                    ':bgt'   => $opp['estimated_value'],
                    ':scope' => "Commissioned from Won Opportunity #{$oppId}"
                ]);

                // Create File Center project space & documents SOW stub (DOC-2026-xxx)
                $docId = 'DOC-2026-' . str_pad((string)$prjNum, 3, '0', STR_PAD_LEFT);
                $docStmt = $pdo->prepare("
                    INSERT INTO documents 
                    (doc_id, file_name, description, classification, folder, department, file_size, status, retention_period, project_ref, customer_ref, owner_emp_id, related_prj_id, related_cus_id, created_at)
                    VALUES (:did, :fname, :desc, 'Confidential', 'projects', 'ENG', '1.5 MB', 'Approved', '7y', :pref, :cref, :owner, :rpid, :rcid, NOW())
                    ON DUPLICATE KEY UPDATE file_name = VALUES(file_name)
                ");
                $docStmt->execute([
                    ':did'   => $docId,
                    ':fname' => "SOW-{$prjId}-Specification.pdf",
                    ':desc'  => "Statement of Work and technical specification stub for project {$prjId}",
                    ':pref'  => $prjId,
                    ':cref'  => $opp['company_name'] ?: $opp['cus_id'],
                    ':owner' => $pmEmpId,
                    ':rpid'  => $prjId,
                    ':rcid'  => $opp['cus_id']
                ]);

                // Create billing milestones in billing_cycles
                $bcStmt = $pdo->prepare("
                    INSERT INTO billing_cycles 
                    (prj_id, cus_id, milestone_description, milestone_amount, scheduled_date, invoiced)
                    VALUES 
                    (?, ?, ?, ?, DATE_ADD(CURDATE(), INTERVAL 30 DAY), 0),
                    (?, ?, ?, ?, DATE_ADD(CURDATE(), INTERVAL 90 DAY), 0)
                ");
                $bcStmt->execute([
                    $prjId,
                    $opp['cus_id'],
                    "Milestone 1: Engineering Blueprint & Site Sign-off ({$prjId})",
                    round($opp['estimated_value'] * 0.4, 2),
                    $prjId,
                    $opp['cus_id'],
                    "Milestone 2: Final Equipment Commissioning & Handover ({$prjId})",
                    round($opp['estimated_value'] * 0.6, 2)
                ]);

                // Create an OPS procurement task
                $taskStmt = $pdo->prepare("
                    INSERT INTO ops_tasks (task_id, order_id, prj_id, task_type, assigned_emp_id, status, created_at)
                    VALUES (:tid, NULL, :pid, 'Procurement', 'EMP-1011', 'Pending', NOW())
                ");
                $taskStmt->execute([
                    ':tid' => 'OPS-PRJ-' . substr($prjId, 9),
                    ':pid' => $prjId
                ]);

                // Emit CRM->DOC, CRM->FIN
                vp_emit($pdo, 'CRM_TO_DOC', 'CRM', 'DOC', 'PROJECT_PROVISIONED', [
                    'prj_id'  => $prjId,
                    'doc_id'  => $docId,
                    'cus_id'  => $opp['cus_id'],
                    'summary' => "Project space and SOW {$docId} created for {$prjId}"
                ], $empId, 201);

                vp_emit($pdo, 'CRM_TO_FIN', 'CRM', 'FIN', 'PROJECT_BILLING_CREATED', [
                    'prj_id'  => $prjId,
                    'cus_id'  => $opp['cus_id'],
                    'budget'  => $opp['estimated_value'],
                    'summary' => "Billing milestones generated for project {$prjId} ({$opp['estimated_value']} EUR)"
                ], $empId, 201);
            }
        } catch (Throwable $e) {
            error_log("Failed in Flow C (Opp Won -> Project): " . $e->getMessage());
        }
    }

    try {
        vp_emit($pdo, 'CRM_TO_ADM', 'CRM', 'ADM', 'OPPORTUNITY_STAGE_CHANGED', [
            'opp_id'    => $oppId,
            'new_stage' => $newStage,
            'summary'   => "Opp #{$oppId} transitioned to {$newStage}",
            'endpoint'  => '/opportunities/stage'
        ], $empId, 200);
    } catch (Throwable $e) {
        error_log("Failed to emit CRM_TO_ADM: " . $e->getMessage());
    }

    return $res;
}

function crm_updateOpportunity(int $oppId, array $data): bool
{
    $pdo = getDbConnection();
    $fields = [];
    $params = [':id' => $oppId];

    $allowed = ['stage', 'estimated_value', 'expected_close_date', 'cus_id', 'sales_emp_id'];
    foreach ($allowed as $col) {
        if (array_key_exists($col, $data)) {
            $fields[] = "`$col` = :$col";
            $params[":$col"] = $data[$col];
        }
    }

    if (empty($fields)) {
        return false;
    }

    $sql = "UPDATE opportunities SET " . implode(", ", $fields) . " WHERE opp_id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

function crm_deleteOpportunity(int $oppId): bool
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("DELETE FROM opportunities WHERE opp_id = ?");
    return $stmt->execute([$oppId]);
}

// ============================================================================
// 4. CUSTOMERS 360 & DIRECTORY
// ============================================================================

function crm_getCustomers(array $filters = []): array
{
    $pdo = getDbConnection();
    $where = [];
    $params = [];

    if (!empty($filters['sector']) && $filters['sector'] !== 'all') {
        $where[] = "c.sector = :sector";
        $params[':sector'] = $filters['sector'];
    }

    if (!empty($filters['search'])) {
        $where[] = "(c.company_name LIKE :s OR c.cus_id LIKE :s OR c.primary_contact_name LIKE :s OR c.sector LIKE :s)";
        $params[':s'] = '%' . $filters['search'] . '%';
    }

    $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            e.full_name AS account_manager_name,
            e.email AS account_manager_email,
            (SELECT COUNT(*) FROM projects WHERE cus_id = c.cus_id) AS total_projects,
            (SELECT COALESCE(SUM(contract_value), 0) FROM contracts WHERE cus_id = c.cus_id AND status = 'Active') AS total_contract_arr,
            (SELECT COUNT(*) FROM opportunities WHERE cus_id = c.cus_id AND stage != 'Lost') AS active_opportunities,
            (SELECT COUNT(*) FROM invoices WHERE cus_id = c.cus_id AND payment_status = 'Pending') AS pending_invoices
        FROM customers c
        LEFT JOIN employees e ON c.account_manager_emp_id = e.emp_id
        {$whereSql}
        ORDER BY total_contract_arr DESC, c.company_name ASC
    ");
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function crm_getCustomerDetail(string $cusId): ?array
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            e.full_name AS account_manager_name,
            e.email AS account_manager_email,
            e.job_title AS account_manager_title
        FROM customers c
        LEFT JOIN employees e ON c.account_manager_emp_id = e.emp_id
        WHERE c.cus_id = ?
    ");
    $stmt->execute([$cusId]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$customer) {
        return null;
    }

    // Contacts
    $ctStmt = $pdo->prepare("SELECT * FROM contacts WHERE cus_id = ? ORDER BY contact_id ASC");
    $ctStmt->execute([$cusId]);
    $customer['contacts'] = $ctStmt->fetchAll(PDO::FETCH_ASSOC);

    // Contracts
    $conStmt = $pdo->prepare("
        SELECT con.*, p.project_name
        FROM contracts con
        LEFT JOIN projects p ON con.prj_id = p.prj_id
        WHERE con.cus_id = ?
        ORDER BY con.start_date DESC
    ");
    $conStmt->execute([$cusId]);
    $customer['contracts'] = $conStmt->fetchAll(PDO::FETCH_ASSOC);

    // Projects
    $pStmt = $pdo->prepare("
        SELECT p.*, e.full_name AS project_manager_name
        FROM projects p
        LEFT JOIN employees e ON p.project_manager_emp_id = e.emp_id
        WHERE p.cus_id = ?
        ORDER BY p.prj_id ASC
    ");
    $pStmt->execute([$cusId]);
    $customer['projects'] = $pStmt->fetchAll(PDO::FETCH_ASSOC);

    // Quotes
    $qStmt = $pdo->prepare("
        SELECT q.*, p.product_name
        FROM quotes q
        JOIN products p ON q.prod_id = p.prod_id
        WHERE q.cus_id = ?
        ORDER BY q.quote_id DESC
    ");
    $qStmt->execute([$cusId]);
    $customer['quotes'] = $qStmt->fetchAll(PDO::FETCH_ASSOC);

    // Opportunities
    $oppStmt = $pdo->prepare("SELECT * FROM opportunities WHERE cus_id = ? ORDER BY estimated_value DESC");
    $oppStmt->execute([$cusId]);
    $customer['opportunities'] = $oppStmt->fetchAll(PDO::FETCH_ASSOC);

    return $customer;
}

function crm_createCustomer(array $data): string
{
    $pdo = getDbConnection();
    $newCusId = vp_next_id($pdo, 'customers', 'CUS-', 4);

    $user = crm_getCurrentUser();
    $mgr = !empty($data['account_manager_emp_id']) ? $data['account_manager_emp_id'] : $user['emp_id'];

    $stmt = $pdo->prepare("
        INSERT INTO customers 
        (cus_id, company_name, sector, primary_contact_name, primary_contact_email, account_manager_emp_id, onboarded_at)
        VALUES 
        (:id, :comp, :sec, :contact, :email, :mgr, NOW())
    ");
    $stmt->execute([
        ':id'      => $newCusId,
        ':comp'    => trim($data['company_name'] ?? 'New Enterprise Client'),
        ':sec'     => trim($data['sector'] ?? 'Industrial Technology'),
        ':contact' => trim($data['primary_contact_name'] ?? 'Executive Officer'),
        ':email'   => trim($data['primary_contact_email'] ?? ''),
        ':mgr'     => $mgr
    ]);

    $newCusId = $data['cus_id'] ?? ('CUS-' . (1000 + (int)$pdo->lastInsertId()));
    try {
        vp_emit($pdo, 'CRM_TO_CUS', 'CRM', 'CUS', 'CUSTOMER_CREATED', [
            'cus_id'   => $newCusId,
            'summary'  => "Created customer {$newCusId}",
            'endpoint' => '/customers/create'
        ], $user['emp_id'], 201);
    } catch (Throwable $e) {
        error_log("Failed to emit CRM_TO_CUS: " . $e->getMessage());
    }
    return $newCusId;
}

function crm_updateCustomer(string $cusId, array $data): bool
{
    $pdo = getDbConnection();
    $fields = [];
    $params = [':id' => $cusId];

    $allowed = ['company_name', 'sector', 'primary_contact_name', 'primary_contact_email', 'account_manager_emp_id'];
    foreach ($allowed as $col) {
        if (array_key_exists($col, $data)) {
            $fields[] = "`$col` = :$col";
            $params[":$col"] = $data[$col];
        }
    }

    if (empty($fields)) {
        return false;
    }

    $sql = "UPDATE customers SET " . implode(", ", $fields) . " WHERE cus_id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

function crm_deleteCustomer(string $cusId): bool
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("DELETE FROM customers WHERE cus_id = ?");
    return $stmt->execute([$cusId]);
}

// ============================================================================
// 5. SALES FORECASTS & CONTRACTS
// ============================================================================

function crm_getSalesForecasts(?string $period = null): array
{
    $pdo = getDbConnection();
    $where = $period ? "WHERE sf.period = :prd" : "";
    $stmt = $pdo->prepare("
        SELECT 
            sf.*,
            e.full_name AS sales_representative,
            e.job_title AS sales_rep_role
        FROM sales_forecasts sf
        LEFT JOIN employees e ON sf.sales_emp_id = e.emp_id
        {$where}
        ORDER BY sf.period ASC, sf.forecast_id ASC
    ");
    if ($period) {
        $stmt->execute([':prd' => $period]);
    } else {
        $stmt->execute();
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function crm_upsertSalesForecast(array $data): int
{
    $pdo = getDbConnection();
    $user = crm_getCurrentUser();
    $empId = !empty($data['sales_emp_id']) ? $data['sales_emp_id'] : $user['emp_id'];
    $period = trim($data['period'] ?? '2026-Q4');
    $forecast = (float)($data['forecast_amount'] ?? 0.0);
    $actual = isset($data['actual_amount']) ? (float)$data['actual_amount'] : null;

    if (!empty($data['forecast_id'])) {
        $stmt = $pdo->prepare("
            UPDATE sales_forecasts 
            SET sales_emp_id = :eid, period = :prd, forecast_amount = :fc, actual_amount = :act
            WHERE forecast_id = :fid
        ");
        $stmt->execute([
            ':eid' => $empId,
            ':prd' => $period,
            ':fc'  => $forecast,
            ':act' => $actual,
            ':fid' => (int)$data['forecast_id']
        ]);
        return (int)$data['forecast_id'];
    }

    $stmt = $pdo->prepare("
        INSERT INTO sales_forecasts (sales_emp_id, period, forecast_amount, actual_amount)
        VALUES (:eid, :prd, :fc, :act)
    ");
    $stmt->execute([
        ':eid' => $empId,
        ':prd' => $period,
        ':fc'  => $forecast,
        ':act' => $actual
    ]);
    return (int)$pdo->lastInsertId();
}

function crm_getQuotes(array $filters = []): array
{
    $pdo = getDbConnection();
    $stmt = $pdo->query("
        SELECT 
            q.*,
            c.company_name,
            p.product_name,
            p.billing_model,
            e.full_name AS prepared_by_name
        FROM quotes q
        JOIN customers c ON q.cus_id = c.cus_id
        JOIN products p ON q.prod_id = p.prod_id
        LEFT JOIN employees e ON q.created_by_emp_id = e.emp_id
        ORDER BY q.created_at DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function crm_getContracts(array $filters = []): array
{
    $pdo = getDbConnection();
    $stmt = $pdo->query("
        SELECT 
            con.*,
            c.company_name,
            c.sector,
            p.project_name
        FROM contracts con
        JOIN customers c ON con.cus_id = c.cus_id
        LEFT JOIN projects p ON con.prj_id = p.prj_id
        ORDER BY con.contract_id DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ============================================================================
// 6. CENTRALIZED AJAX ACTION ROUTER
// ============================================================================

$action = $_REQUEST['action'] ?? null;
if ($action !== null && (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_GET['action']) || isset($_POST['action']) || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json'))) {
    try {
        switch ($action) {
            case 'get_dashboard':
                crm_jsonReply(['success' => true, 'data' => crm_getDashboardMetrics()]);
                break;

            case 'get_leads':
                crm_jsonReply(['success' => true, 'data' => crm_getLeads($_GET)]);
                break;

            case 'get_lead':
                $lead = crm_getLead((int)($_GET['id'] ?? 0));
                if (!$lead) crm_jsonReply(['success' => false, 'error' => 'Lead not found'], 404);
                crm_jsonReply(['success' => true, 'data' => $lead]);
                break;

            case 'create_lead':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $newId = crm_createLead($input);
                crm_jsonReply(['success' => true, 'lead_id' => $newId, 'message' => 'Lead registered successfully']);
                break;

            case 'update_lead':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $leadId = (int)($input['lead_id'] ?? $_GET['id'] ?? 0);
                crm_updateLead($leadId, $input);
                crm_jsonReply(['success' => true, 'message' => 'Lead updated successfully']);
                break;

            case 'delete_lead':
                $leadId = (int)($_REQUEST['id'] ?? $_REQUEST['lead_id'] ?? 0);
                crm_deleteLead($leadId);
                crm_jsonReply(['success' => true, 'message' => 'Lead removed']);
                break;

            case 'convert_lead':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $leadId = (int)($input['lead_id'] ?? $_GET['lead_id'] ?? 0);
                $dealVal = (float)($input['estimated_value'] ?? 250000.0);
                $res = crm_convertLead($leadId, $dealVal);
                crm_jsonReply(array_merge(['success' => true, 'message' => 'Lead converted to Opportunity & Customer'], $res));
                break;

            case 'get_opportunities':
                crm_jsonReply(['success' => true, 'data' => crm_getOpportunities($_GET)]);
                break;

            case 'create_opportunity':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $newId = crm_createOpportunity($input);
                crm_jsonReply(['success' => true, 'opp_id' => $newId, 'message' => 'Opportunity logged successfully']);
                break;

            case 'update_stage':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $oppId = (int)($input['opp_id'] ?? $_GET['opp_id'] ?? 0);
                $stage = trim((string)($input['stage'] ?? ''));
                crm_updateOpportunityStage($oppId, $stage);
                crm_jsonReply(['success' => true, 'opp_id' => $oppId, 'stage' => $stage, 'message' => "Pipeline stage updated to {$stage}"]);
                break;

            case 'get_customers':
                crm_jsonReply(['success' => true, 'data' => crm_getCustomers($_GET)]);
                break;

            case 'get_customer_detail':
                $cusId = trim($_GET['id'] ?? $_GET['cus_id'] ?? '');
                $detail = crm_getCustomerDetail($cusId);
                if (!$detail) crm_jsonReply(['success' => false, 'error' => 'Customer account not found'], 404);
                crm_jsonReply(['success' => true, 'data' => $detail]);
                break;

            case 'create_customer':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $newCusId = crm_createCustomer($input);
                crm_jsonReply(['success' => true, 'cus_id' => $newCusId, 'message' => 'Customer onboarded successfully']);
                break;

            case 'upsert_forecast':
                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $fcId = crm_upsertSalesForecast($input);
                crm_jsonReply(['success' => true, 'forecast_id' => $fcId, 'message' => 'Forecast saved successfully']);
                break;
        }
    } catch (Throwable $e) {
        crm_jsonReply(['success' => false, 'error' => $e->getMessage()], 400);
    }
}
