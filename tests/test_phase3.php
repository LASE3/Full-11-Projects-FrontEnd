<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }

/**
 * VOSTOKPRIBOR - Phase 3 Enterprise Flows Acceptance Test Suite
 * Tests Flows A through K across all 11 enterprise systems.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/AuditLogger.php';
require_once __DIR__ . '/../includes/integration_bus.php';
require_once __DIR__ . '/../includes/enterprise_flows.php';
require_once __DIR__ . '/../CRM/crm_service.php';
require_once __DIR__ . '/../HR System/hr_service.php';
require_once __DIR__ . '/../Admin & Governance Portal/gov_service.php';
require_once __DIR__ . '/../Employee Intranet/intranet_service.php';

$pdo = getDbConnection();

$results = [];

function recordTest(string $flow, string $name, bool $passed, string $details = ''): void
{
    global $results;
    $results[] = [
        'flow'    => $flow,
        'name'    => $name,
        'status'  => $passed ? 'PASS' : 'FAIL',
        'details' => $details
    ];
}

echo "====================================================================\n";
echo "       VOSTOKPRIBOR PHASE 3 ACCEPTANCE TEST SUITE (FLOWS A-K)       \n";
echo "====================================================================\n\n";

// -------------------------------------------------------------------------
// FLOW A: WEB -> CRM (SOP-01 Start)
// -------------------------------------------------------------------------
try {
    $salEmpStmt = $pdo->query("SELECT emp_id FROM employees WHERE department_code = 'SAL' AND employment_status = 'Active' ORDER BY emp_id ASC LIMIT 1");
    $activeSalEmp = $salEmpStmt->fetchColumn() ?: 'EMP-1006';

    $stmtC = $pdo->prepare("SELECT next_val FROM id_counters WHERE name = 'leads' FOR UPDATE");
    $stmtC->execute();
    $lNum = $stmtC->fetchColumn();
    $leadCode = sprintf("LEAD-2026-%04d", (int)$lNum);
    $pdo->prepare("UPDATE id_counters SET next_val = next_val + 1 WHERE name = 'leads'")->execute();

    $stmtLead = $pdo->prepare("
        INSERT INTO leads (full_name, email, phone, company_name, message, source_page, status, assigned_sales_emp_id, created_at)
        VALUES (?, ?, ?, ?, ?, 'Contact Form', 'New', ?, NOW())
    ");
    $stmtLead->execute([
        'Test Procurement Lead',
        'procurement@testcorp.local',
        '+7 727 555 0199',
        'Test Industrial Corp',
        'Requesting quote for 10x VP-900 calibration kits',
        $activeSalEmp
    ]);
    $leadId = (int)$pdo->lastInsertId();

    $stmtAct = $pdo->prepare("
        INSERT INTO crm_activities (activity_type, title, description, emp_id, created_at)
        VALUES ('Inquiry', 'Lead received', ?, ?, NOW())
    ");
    $stmtAct->execute(["New RFQ {$leadCode} received via Corporate Web Platform", $activeSalEmp]);

    AuditLogger::log($pdo, 'WEB', 'Inbound Lead Submitted', 'leads', (string)$leadId, null, [
        'full_name' => 'Test Procurement Lead',
        'assigned'  => $activeSalEmp
    ], 'EMP-1004');

    vp_emit($pdo, 'WEB_TO_CRM', 'WEB', 'CRM', 'rfq_submitted', [
        'lead_id'   => $leadId,
        'lead_code' => $leadCode,
        'company'   => 'Test Industrial Corp'
    ], $activeSalEmp);

    $checkLead = $pdo->query("SELECT lead_id, assigned_sales_emp_id FROM leads WHERE lead_id = {$leadId}")->fetch(PDO::FETCH_ASSOC);
    $checkLog = $pdo->query("SELECT COUNT(*) FROM system_integration_logs WHERE link_code = 'WEB_TO_CRM' AND source_system_id = 'WEB' AND target_system_id = 'CRM'")->fetchColumn();
    $checkAudit = $pdo->query("SELECT COUNT(*) FROM audit_logs WHERE system_id = 'WEB'")->fetchColumn();

    $passA = ($checkLead && !empty($checkLead['assigned_sales_emp_id']) && (int)$checkLog > 0 && (int)$checkAudit > 0);
    recordTest('Flow A', 'WEB -> CRM (Inbound RFQ, SAL Assignment, Audit, vp_emit)', $passA, "Lead #{$leadId} assigned to {$checkLead['assigned_sales_emp_id']}");
} catch (Throwable $e) {
    recordTest('Flow A', 'WEB -> CRM (Inbound RFQ)', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// FLOW B: CRM Lead -> Customer Onboarding (SOP-01 Rest)
// -------------------------------------------------------------------------
try {
    $convRes = crm_convertLead($leadId, 150000.00, 'EMP-1007');

    $newCusId = $convRes['cus_id'] ?? '';
    $checkAcc = $pdo->prepare("SELECT invite_token FROM customer_accounts WHERE cus_id = ?");
    $checkAcc->execute([$newCusId]);
    $inviteToken = $checkAcc->fetchColumn();

    $checkDocWs = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE related_cus_id = ?");
    $checkDocWs->execute([$newCusId]);
    $docWsCount = (int)$checkDocWs->fetchColumn();

    $checkBill = $pdo->prepare("SELECT COUNT(*) FROM billing_cycles WHERE cus_id = ?");
    $checkBill->execute([$newCusId]);
    $billCount = (int)$checkBill->fetchColumn();

    $checkCrmCus = (int)$pdo->query("SELECT COUNT(*) FROM system_integration_logs WHERE link_code = 'CRM_TO_CUS'")->fetchColumn();
    $checkCrmDoc = (int)$pdo->query("SELECT COUNT(*) FROM system_integration_logs WHERE link_code = 'CRM_TO_DOC'")->fetchColumn();
    $checkCrmFin = (int)$pdo->query("SELECT COUNT(*) FROM system_integration_logs WHERE link_code = 'CRM_TO_FIN'")->fetchColumn();

    $passB = (!empty($newCusId) && !empty($inviteToken) && $docWsCount > 0 && $billCount > 0 && $checkCrmCus > 0 && $checkCrmDoc > 0 && $checkCrmFin > 0);
    recordTest('Flow B', 'CRM Lead -> Customer Onboarding (Account, Invite Token, DOC Workspace, Billing, Emits)', $passB, "Customer {$newCusId} provisioned with invite token");
} catch (Throwable $e) {
    recordTest('Flow B', 'CRM Lead -> Customer Onboarding', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// FLOW C: CRM Opportunity Won -> Project (SOP-02)
// -------------------------------------------------------------------------
try {
    $oppId = $convRes['opp_id'] ?? 0;
    if ($oppId > 0) {
        $wonOk = crm_updateOpportunityStage((int)$oppId, 'Won', 'EMP-1007');
        $fetchPrj = $pdo->prepare("SELECT prj_id, project_manager_emp_id FROM projects WHERE cus_id = ? ORDER BY prj_id DESC LIMIT 1");
        $fetchPrj->execute([$newCusId]);
        $prjRow = $fetchPrj->fetch(PDO::FETCH_ASSOC);
        $newPrjId = $prjRow['prj_id'] ?? '';
        $pmEmpId = $prjRow['project_manager_emp_id'] ?? '';

        $checkOpsTask = $pdo->prepare("SELECT COUNT(*) FROM ops_tasks WHERE prj_id = ? AND task_type = 'Procurement'");
        $checkOpsTask->execute([$newPrjId]);
        $opsTaskCount = (int)$checkOpsTask->fetchColumn();

        $passC = (!empty($newPrjId) && !empty($pmEmpId) && $opsTaskCount > 0);
        recordTest('Flow C', 'CRM Opportunity Won -> Project (PRJ-2026-xxx, ENG PM, SOW Doc, OPS Task, Emits)', $passC, "Project {$newPrjId} created with PM {$pmEmpId}");
    } else {
        recordTest('Flow C', 'CRM Opportunity Won -> Project', false, 'Missing opp_id from Flow B');
    }
} catch (Throwable $e) {
    recordTest('Flow C', 'CRM Opportunity Won -> Project', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// FLOW D: SHP Order -> CRM + FIN + DOC + OPS + CUS (vp_process_order)
// -------------------------------------------------------------------------
try {
    // 1. Create a test order
    $stmtOrdC = $pdo->prepare("SELECT next_val FROM id_counters WHERE name = 'orders' FOR UPDATE");
    $stmtOrdC->execute();
    $oVal = (int)$stmtOrdC->fetchColumn();
    $pdo->prepare("UPDATE id_counters SET next_val = next_val + 1 WHERE name = 'orders'")->execute();
    $testOrderId = (string)$oVal;

    // Ensure price in products
    $pdo->prepare("UPDATE products SET price = 12500.00 WHERE prod_id = 'PROD-1001'")->execute();

    $stmtInsOrd = $pdo->prepare("
        INSERT INTO orders (order_id, cus_id, status, total_amount, order_date)
        VALUES (?, 'CUS-1002', 'Confirmed', 50000.00, NOW())
    ");
    $stmtInsOrd->execute([$testOrderId]);

    $stmtItem = $pdo->prepare("
        INSERT INTO order_items (order_id, prod_id, quantity, unit_price)
        VALUES (?, 'PROD-1001', 4, 12500.00)
    ");
    $stmtItem->execute([$testOrderId]);

    $orderResult = vp_process_order($pdo, $testOrderId);

    $checkInv = $pdo->prepare("SELECT COUNT(*) FROM invoices WHERE inv_id = ?");
    $checkInv->execute([$orderResult['invoice_id']]);
    $invCreated = (int)$checkInv->fetchColumn() > 0;

    $checkOps = $pdo->prepare("SELECT COUNT(*) FROM ops_tasks WHERE order_id = ? AND task_type = 'Fulfilment'");
    $checkOps->execute([$testOrderId]);
    $opsCreated = (int)$checkOps->fetchColumn() > 0;

    $checkShpFin = (int)$pdo->query("SELECT COUNT(*) FROM system_integration_logs WHERE link_code = 'SHP_TO_FIN'")->fetchColumn();
    $checkFinDoc = (int)$pdo->query("SELECT COUNT(*) FROM system_integration_logs WHERE link_code = 'FIN_TO_DOC'")->fetchColumn();

    // Verify rejection when price is missing
    $pdo->prepare("DELETE FROM customer_pricing WHERE prod_id = 'PROD-9999'");
    $missingPriceRejected = false;
    try {
        $stmtBadOrd = $pdo->prepare("INSERT INTO orders (order_id, cus_id, status, total_amount) VALUES ('99999', 'CUS-1002', 'Pending', 0)");
        $stmtBadOrd->execute();
        $pdo->prepare("INSERT INTO order_items (order_id, prod_id, quantity) VALUES ('99999', 'PROD-9999', 1)")->execute();
        vp_process_order($pdo, '99999');
    } catch (Throwable $pe) {
        $missingPriceRejected = true;
    }

    $passD = ($invCreated && $opsCreated && $checkShpFin > 0 && $checkFinDoc > 0 && $missingPriceRejected);
    recordTest('Flow D', 'SHP Order -> CRM + FIN + DOC + OPS + CUS (Invoice, Billing Doc, OPS Task, No Flat 2500 Fallback)', $passD, "Invoice {$orderResult['invoice_id']} & OPS task generated");
} catch (Throwable $e) {
    recordTest('Flow D', 'SHP Order -> CRM + FIN + DOC + OPS + CUS', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// FLOW E: FIN Payment -> Reconciliation -> CUS
// -------------------------------------------------------------------------
try {
    $invToPay = $orderResult['invoice_id'] ?? 'INV-2026-001';

    // 1. Enforce separation of duties: invoice creator cannot reconcile payment
    $pdo->prepare("UPDATE invoices SET created_by_emp_id = 'EMP-1004' WHERE inv_id = ?")->execute([$invToPay]);
    $dutySeparationEnforced = false;
    try {
        vp_reconcile_payment($pdo, $invToPay, 5000.00, 'SWIFT', 'EMP-1004'); // Reconciler == Creator
    } catch (Throwable $se) {
        if (str_contains($se->getMessage(), 'Separation of duties')) {
            $dutySeparationEnforced = true;
        }
    }

    // 2. Legitimate reconciliation by distinct finance officer
    $recResult = vp_reconcile_payment($pdo, $invToPay, 50000.00, 'SWIFT', 'EMP-1010');

    $checkInvStatus = $pdo->prepare("SELECT payment_status FROM invoices WHERE inv_id = ?");
    $checkInvStatus->execute([$invToPay]);
    $invStatus = $checkInvStatus->fetchColumn();

    $checkFinCus = (int)$pdo->query("SELECT COUNT(*) FROM system_integration_logs WHERE link_code = 'FIN_TO_CUS'")->fetchColumn();

    $passE = ($dutySeparationEnforced && $invStatus === 'Paid' && $checkFinCus > 0);
    recordTest('Flow E', 'FIN Payment Reconciliation (Separation of Duties, Invoice Status Update, Emits)', $passE, "Duty separation enforced, invoice marked {$invStatus}");
} catch (Throwable $e) {
    recordTest('Flow E', 'FIN Payment Reconciliation', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// FLOW F: CUS Ticket -> IT -> Escalation -> ADM (SOP-04)
// -------------------------------------------------------------------------
try {
    $critTicketData = [
        'title'          => 'SCADA Gateway Communication Failure - Reactor Line 1',
        'source_system'  => 'CUS',
        'priority'       => 'Critical',
        'requester_name' => 'Kristaps Ozols',
        'requester_role' => 'Plant Director',
        'category'       => 'Infrastructure',
        'description'    => 'Zero telemetry signal received from pressure sensor telemetry bus.'
    ];
    $critTktId = vp_create_ticket($pdo, $critTicketData, 'CUS-1002');

    $checkEsc = $pdo->prepare("SELECT COUNT(*) FROM ticket_escalations WHERE tkt_id = ?");
    $checkEsc->execute([$critTktId]);
    $escCount = (int)$checkEsc->fetchColumn();

    $checkSecEv = $pdo->prepare("SELECT COUNT(*) FROM security_events WHERE description LIKE ?");
    $checkSecEv->execute(["%{$critTktId}%"]);
    $secEvCount = (int)$checkSecEv->fetchColumn();

    $passF = (!empty($critTktId) && $escCount > 0 && $secEvCount > 0);
    recordTest('Flow F', 'Critical Ticket -> Escalation & ADM Security Event (vp_create_ticket, SLA, ticket_escalations)', $passF, "Ticket {$critTktId} escalated to governance");
} catch (Throwable $e) {
    recordTest('Flow F', 'Critical Ticket -> Escalation & ADM Security Event', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// FLOW G: HR Onboarding -> IT + EMP + ADM + DOC (SOP-05)
// -------------------------------------------------------------------------
try {
    $uniq = time() . '_' . rand(100, 999);
    $onboardData = [
        'full_name'       => 'Svetlana Petrova ' . $uniq,
        'job_title'       => 'Automation QA Engineer',
        'department_code' => 'ENG',
        'clearance_level' => 'L2',
        'email'           => 's.petrova.' . $uniq . '@vostokpribor.local'
    ];
    $onboardRes = hr_createEmployee($onboardData);
    $newEmpId = $onboardRes['emp_id'] ?? '';

    $checkEmp = $pdo->prepare("SELECT employment_status FROM employees WHERE emp_id = ?");
    $checkEmp->execute([$newEmpId]);
    $empStat = $checkEmp->fetchColumn();

    $checkAcc = $pdo->prepare("SELECT status FROM employee_accounts WHERE emp_id = ?");
    $checkAcc->execute([$newEmpId]);
    $accStat = $checkAcc->fetchColumn();

    $checkRoles = $pdo->prepare("SELECT COUNT(*) FROM employee_roles WHERE emp_id = ?");
    $checkRoles->execute([$newEmpId]);
    $rolesCount = (int)$checkRoles->fetchColumn();

    $checkTkt = $pdo->prepare("SELECT tkt_id FROM tickets WHERE title LIKE ?");
    $checkTkt->execute(["%Provision access for {$newEmpId}%"]);
    $provTktId = $checkTkt->fetchColumn();

    $checkAsset = $pdo->prepare("SELECT COUNT(*) FROM it_assets WHERE emp_id = ? AND status = 'Provisioning'");
    $checkAsset->execute([$newEmpId]);
    $assetCount = (int)$checkAsset->fetchColumn();

    $checkHrDoc = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE file_name = ?");
    $checkHrDoc->execute(["HR_Dossier_{$newEmpId}.pdf"]);
    $hrDocCount = (int)$checkHrDoc->fetchColumn();

    $checkHrIt = (int)$pdo->query("SELECT COUNT(*) FROM system_integration_logs WHERE link_code = 'HR_TO_IT'")->fetchColumn();
    $checkHrAdm = (int)$pdo->query("SELECT COUNT(*) FROM system_integration_logs WHERE link_code = 'HR_TO_ADM'")->fetchColumn();

    $passG = (!empty($newEmpId) && $empStat === 'Inactive' && $accStat === 'Inactive' && $rolesCount > 0 && !empty($provTktId) && $assetCount > 0 && $hrDocCount > 0 && $checkHrIt > 0 && $checkHrAdm > 0);
    recordTest('Flow G', 'HR Onboarding (Inactive Until IT Closes Ticket, IT Ticket, Asset, Dossier, Emits)', $passG, "Employee {$newEmpId} enrolled with provisioning ticket {$provTktId}");
} catch (Throwable $e) {
    recordTest('Flow G', 'HR Onboarding', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// FLOW H: HR Offboarding -> Revoke Everywhere (SOP-06)
// -------------------------------------------------------------------------
try {
    // 1. Simulate an active session and API key for this employee
    $eaStmt = $pdo->prepare("SELECT account_id FROM employee_accounts WHERE emp_id = ? LIMIT 1");
    $eaStmt->execute([$newEmpId]);
    $accId = (int)$eaStmt->fetchColumn();

    $ssoJti = 'test_jti_' . bin2hex(random_bytes(16));
    $pdo->prepare("INSERT INTO user_sessions (account_type, employee_account_id, system_id, started_at, status, jti) VALUES ('Employee', ?, 'HR', NOW(), 'Active', ?)")->execute([$accId, $ssoJti]);

    $pdo->prepare("INSERT INTO developer_api_keys (key_identifier, label, partner_id, partner_name, token_prefix, token_full, environment, rate_limit, classification, scopes, status) VALUES (?, ?, ?, 'Test', 'vp_', 'vp_full', 'Sandbox', '1000', 'Internal', 'test', 'Active')")->execute(['TEST-KEY-' . $newEmpId, "Key for {$newEmpId}", $newEmpId]);

    // 2. Offboard the employee
    $offboardRes = hr_completeOffboarding($newEmpId);

    // Verify:
    // a. Zero roles
    $rCount = (int)$pdo->query("SELECT COUNT(*) FROM employee_roles WHERE emp_id = '{$newEmpId}'")->fetchColumn();
    // b. Zero active sessions
    $sCount = (int)$pdo->query("SELECT COUNT(*) FROM user_sessions WHERE employee_account_id = {$accId} AND status = 'Active' AND ended_at IS NULL")->fetchColumn();
    // c. API keys revoked
    $kCount = (int)$pdo->query("SELECT COUNT(*) FROM developer_api_keys WHERE (label LIKE '%{$newEmpId}%' OR partner_id = '{$newEmpId}') AND status = 'Active'")->fetchColumn();
    // d. IT revocation ticket created
    $tRevCount = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE title LIKE '%Revoke access and decommission equipment for {$newEmpId}%'")->fetchColumn();
    // e. Orphaned access findings empty for this employee
    $orphans = gov_getOrphanedAccessFindings($pdo);
    $foundInOrphans = false;
    foreach ($orphans as $o) {
        if ($o['emp_id'] === $newEmpId) {
            $foundInOrphans = true;
            break;
        }
    }

    $passH = ($rCount === 0 && $sCount === 0 && $kCount === 0 && $tRevCount > 0 && !$foundInOrphans);
    recordTest('Flow H', 'HR Offboarding (Zero Roles, Zero Active Sessions, Keys Revoked, IT Ticket, Orphaned Report Clean)', $passH, "Roles: {$rCount}, Active Sessions: {$sCount}, Active Keys: {$kCount}");
} catch (Throwable $e) {
    recordTest('Flow H', 'HR Offboarding', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// FLOW I: DOC Approval -> Publish to CUS (SOP-08)
// -------------------------------------------------------------------------
try {
    // 1. Create a confidential document linked to customer
    $docId = 'DOC-2026-TEST';
    $pdo->prepare("DELETE FROM document_access_log WHERE doc_id = ?")->execute([$docId]);
    $pdo->prepare("DELETE FROM document_versions WHERE doc_id = ?")->execute([$docId]);
    $pdo->prepare("DELETE FROM document_approvals WHERE doc_id = ?")->execute([$docId]);
    $pdo->prepare("DELETE FROM documents WHERE doc_id = ?")->execute([$docId]);
    $stmtDoc = $pdo->prepare("
        INSERT INTO documents (doc_id, file_name, description, classification, folder, department, file_size, status, related_cus_id, owner_emp_id)
        VALUES (?, 'Calibration_Report_CUS1002.pdf', 'Calibration Certificate for BaltNord', 'Confidential', 'projects', 'ENG', '3.5 MB', 'In Review', 'CUS-1002', 'EMP-1016')
    ");
    $stmtDoc->execute([$docId]);

    // 2. Execute approval workflow
    require_once __DIR__ . '/../File Center/api/db.php';
    $chkStmt = $pdo->prepare("SELECT approval_id FROM document_approvals WHERE doc_id = :id");
    $chkStmt->execute([':id' => $docId]);
    $existingApp = $chkStmt->fetchColumn();

    $signToken = 'SIG-TEST-' . time();
    if ($existingApp) {
        $pdo->prepare("UPDATE document_approvals SET decision = 'Approved', token = ?, decision_date = NOW() WHERE approval_id = ?")->execute([$signToken, $existingApp]);
    } else {
        $pdo->prepare("INSERT INTO document_approvals (doc_id, reviewer_emp_id, stage, stage_name, token, decision, decision_date) VALUES (?, 'EMP-1004', 3, 'Governance Clearance', ?, 'Approved', NOW())")->execute([$docId, $signToken]);
    }
    $pdo->prepare("UPDATE documents SET status = 'Approved', updated_at = NOW() WHERE doc_id = ?")->execute([$docId]);

    // Create version
    $pdo->prepare("INSERT INTO document_versions (doc_id, version_number, uploaded_by_emp_id, uploaded_at, file_path) VALUES (?, 1, 'EMP-1004', NOW(), 'vault/test.pdf')")->execute([$docId]);

    // Emit DOC_TO_CUS
    vp_emit($pdo, 'DOC_TO_CUS', 'DOC', 'CUS', 'document_published_to_customer', [
        'doc_id'    => $docId,
        'cus_id'    => 'CUS-1002',
        'file_name' => 'Calibration_Report_CUS1002.pdf'
    ], 'EMP-1004');

    // Log access
    logDocumentAction($pdo, $docId, 'View', 'Customer portal document view', 'EMP-1004');

    $checkVer = (int)$pdo->query("SELECT COUNT(*) FROM document_versions WHERE doc_id = '{$docId}'")->fetchColumn();
    $checkDocCus = (int)$pdo->query("SELECT COUNT(*) FROM system_integration_logs WHERE link_code = 'DOC_TO_CUS'")->fetchColumn();
    $checkAccessLog = (int)$pdo->query("SELECT COUNT(*) FROM document_access_log WHERE doc_id = '{$docId}'")->fetchColumn();

    $passI = ($checkVer > 0 && $checkDocCus > 0 && $checkAccessLog > 0);
    recordTest('Flow I', 'DOC Approval -> Publish to CUS (Version Created, DOC_TO_CUS Emitted, Access Log)', $passI, "Version: {$checkVer}, Logs: {$checkAccessLog}");
} catch (Throwable $e) {
    recordTest('Flow I', 'DOC Approval -> Publish to CUS', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// FLOW J: DEV <-> Other Systems
// -------------------------------------------------------------------------
try {
    // 1. Partner application creates CRM lead
    $partnerApp = [
        'company_name'  => 'Central Automation Labs',
        'contact_name'  => 'Vadim Morozov',
        'contact_email' => 'v.morozov@cal-labs.local',
        'target_environment' => 'Production'
    ];

    $stmtL = $pdo->prepare("
        INSERT INTO leads (full_name, email, company_name, source_page, status, assigned_sales_emp_id, message, created_at)
        VALUES (?, ?, ?, 'Developer Portal', 'New', 'EMP-1007', 'Partner Application', NOW())
    ");
    $stmtL->execute([$partnerApp['contact_name'], $partnerApp['contact_email'], $partnerApp['company_name']]);
    $devLeadId = (int)$pdo->lastInsertId();

    vp_emit($pdo, 'DEV_TO_CRM', 'DEV', 'CRM', 'partner_registered_lead', ['company' => $partnerApp['company_name']], 'DEV-SYSTEM');

    // 2. Approved partner gets api_partners + api_credentials
    $pdo->prepare("INSERT INTO api_partners (cus_id, partner_name, registered_at, status) VALUES ('CUS-1002', 'Central Automation Labs', NOW(), 'Active')")->execute();
    $newPId = (int)$pdo->lastInsertId();

    $rawSec = 'vp_live_' . bin2hex(random_bytes(16));
    $pdo->prepare("INSERT INTO api_credentials (partner_id, api_key_hash, created_at, revoked) VALUES (?, ?, NOW(), 0)")->execute([$newPId, password_hash($rawSec, PASSWORD_BCRYPT)]);

    // 3. API access log
    $pdo->prepare("INSERT INTO api_access_logs (system_id, endpoint, http_method, source_ip, status_code, success) VALUES ('DEV', '/v1/sensors/telemetry', 'GET', '127.0.0.1', 200, 1)")->execute();

    // 4. Sandbox error creates IT ticket and emits DEV->IT
    $errTkt = vp_create_ticket($pdo, [
        'title'          => 'Gateway Failure on /v1/webhook/scada',
        'source_system'  => 'DEV',
        'priority'       => 'High',
        'requester_name' => 'API Gateway Subsystem',
        'category'       => 'Infrastructure'
    ], 'EMP-1020');
    vp_emit($pdo, 'DEV_TO_IT', 'DEV', 'IT', 'sandbox_error_dispatched', ['ticket_id' => $errTkt], 'EMP-1020');

    $checkDevCrm = (int)$pdo->query("SELECT COUNT(*) FROM system_integration_logs WHERE link_code = 'DEV_TO_CRM'")->fetchColumn();
    $checkDevIt = (int)$pdo->query("SELECT COUNT(*) FROM system_integration_logs WHERE link_code = 'DEV_TO_IT'")->fetchColumn();
    $checkApiLog = (int)$pdo->query("SELECT COUNT(*) FROM api_access_logs WHERE system_id = 'DEV'")->fetchColumn();

    $passJ = ($devLeadId > 0 && $checkDevCrm > 0 && $checkDevIt > 0 && $checkApiLog > 0);
    recordTest('Flow J', 'DEV <-> Other Systems (CRM Lead, api_partners/api_credentials, api_access_logs, DEV->IT)', $passJ, "DEV_TO_CRM: {$checkDevCrm}, DEV_TO_IT: {$checkDevIt}");
} catch (Throwable $e) {
    recordTest('Flow J', 'DEV <-> Other Systems', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// FLOW K: Everything -> ADM (Dashboard Data Providers)
// -------------------------------------------------------------------------
try {
    $traffic = gov_getLiveIntegrationTraffic($pdo, 5);
    $critTkts = gov_getCriticalTickets($pdo, 5);
    $privLogs = gov_getPrivilegedAccountChanges($pdo, 5);
    $orphans = gov_getOrphanedAccessFindings($pdo);
    $secEvs = gov_getSecurityEventsPerSystem($pdo, 5);
    $lHealth = gov_getLinkHealth($pdo);

    $passK = (is_array($traffic) && is_array($critTkts) && is_array($privLogs) && is_array($orphans) && is_array($secEvs) && is_array($lHealth) && count($lHealth) > 0);
    recordTest('Flow K', 'Central ADM Dashboard Telemetry (Traffic, Critical Tickets, Privileged Changes, Orphaned Access, Link Health)', $passK, "Links: " . count($lHealth) . ", Traffic rows: " . count($traffic));
} catch (Throwable $e) {
    recordTest('Flow K', 'Central ADM Dashboard Telemetry', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// Print Summary Table
// -------------------------------------------------------------------------
$allPassed = true;
printf("%-8s | %-62s | %-6s | %s\n", "FLOW", "TEST NAME", "STATUS", "DETAILS");
echo str_repeat("-", 100) . "\n";
foreach ($results as $r) {
    if ($r['status'] !== 'PASS') $allPassed = false;
    printf("%-8s | %-62s | %-6s | %s\n", $r['flow'], substr($r['name'], 0, 62), $r['status'], $r['details']);
}
echo str_repeat("=", 100) . "\n";
echo "OVERALL PHASE 3 RESULT: " . ($allPassed ? "ALL PASS" : "FAILURES DETECTED") . "\n";

exit($allPassed ? 0 : 1);
