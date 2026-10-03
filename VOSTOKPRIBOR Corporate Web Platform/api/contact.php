<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR Corporate Web Platform - Contact & RFQ Intake API
 * Flow A: Validates input, applies rate-limiting and honeypot traps,
 * inserts CRM lead, assigns sales rep round-robin via assignment_counters,
 * creates crm_activities row, and dispatches via vp_emit('WEB_TO_CRM').
 */

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/integration_bus.php';
require_once __DIR__ . '/../../includes/AuditLogger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed. Use POST.']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: $_POST;

// Honeypot check (field named 'website' or 'hp_check' should be empty)
if (!empty($data['website']) || !empty($data['hp_check']) || !empty($data['fax'])) {
    // Silently reject bot submissions with a fake success
    echo json_encode([
        'success' => true,
        'message' => 'Inquiry successfully saved and dispatched to CRM engineering queue.'
    ]);
    exit;
}

$company  = trim((string)($data['company'] ?? $data['company_name'] ?? ''));
$contact  = trim((string)($data['contact'] ?? $data['contact_name'] ?? $data['full_name'] ?? ''));
$email    = trim((string)($data['email'] ?? ''));
$phone    = trim((string)($data['phone'] ?? ''));
$sector   = trim((string)($data['sector'] ?? ''));
$interest = trim((string)($data['interest'] ?? ''));
$budget   = trim((string)($data['budget'] ?? ''));
$notes    = trim((string)($data['notes'] ?? $data['message'] ?? ''));

// Validation
if (empty($company) || empty($contact) || empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Company, contact name, and corporate email are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid email address format.']);
    exit;
}

if (mb_strlen($company) > 150 || mb_strlen($contact) > 150 || mb_strlen($email) > 150 || mb_strlen($notes) > 5000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Input exceeds maximum allowed character length.']);
    exit;
}

$clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

try {
    $pdo = getDbConnection();

    // Per-IP rate limiting: maximum 10 RFQ inquiries per minute
    $rateStmt = $pdo->prepare("
        SELECT COUNT(*) FROM audit_logs 
        WHERE source_ip = ? AND action LIKE 'INTEGRATION_WEB_TO_CRM%' AND occurred_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)
    ");
    $rateStmt->execute([$clientIp]);
    $recentSubmissions = (int)$rateStmt->fetchColumn();

    if ($recentSubmissions >= 10) {
        http_response_code(429);
        echo json_encode(['success' => false, 'error' => 'Too many requests. Please try again later.']);
        exit;
    }

    // Map estimated budget value
    $estVal = match (strtolower($budget)) {
        'large' => 500000.00,
        'medium' => 200000.00,
        'evaluation' => 75000.00,
        default => 100000.00
    };

    // Round-robin sales rep assignment among active SAL employees using assignment_counters
    $salEmployees = $pdo->query("
        SELECT emp_id FROM employees 
        WHERE department_code = 'SAL' AND employment_status = 'Active' 
        ORDER BY emp_id ASC
    ")->fetchAll(PDO::FETCH_COLUMN);

    if (empty($salEmployees)) {
        $salesRep = 'EMP-1006';
    } else {
        $counterStmt = $pdo->query("SELECT last_index FROM assignment_counters WHERE dept = 'SAL' FOR UPDATE");
        $lastIdx = $counterStmt ? (int)$counterStmt->fetchColumn() : 0;
        $nextIdx = ($lastIdx + 1) % count($salEmployees);
        
        $updCounter = $pdo->prepare("
            INSERT INTO assignment_counters (dept, last_index) VALUES ('SAL', :idx)
            ON DUPLICATE KEY UPDATE last_index = VALUES(last_index)
        ");
        $updCounter->execute([':idx' => $nextIdx]);
        $salesRep = $salEmployees[$nextIdx];
    }

    $composedMessage = "Sector: " . ($sector ?: 'General') . 
                       " | Equipment Interest: " . ($interest ?: 'Turnkey Package') . 
                       " | Budget Scope: " . ($budget ?: 'Standard') . 
                       ($notes ? "\n\nNotes: " . $notes : "");

    // 1. Insert lead into CRM leads table
    $stmt = $pdo->prepare("
        INSERT INTO leads (full_name, email, phone, company_name, message, source_page, status, assigned_sales_emp_id, created_at, estimated_value, priority)
        VALUES (:fn, :em, :ph, :comp, :msg, 'Corporate Web Platform - Contact RFQ', 'New', :rep, NOW(), :val, 'High')
    ");
    $stmt->execute([
        ':fn'   => $contact,
        ':em'   => $email,
        ':ph'   => $phone,
        ':comp' => $company,
        ':msg'  => $composedMessage,
        ':rep'  => $salesRep,
        ':val'  => $estVal
    ]);

    $newId = (int)$pdo->lastInsertId();
    $leadCode = 'LEAD-2026-' . str_pad((string)$newId, 4, '0', STR_PAD_LEFT);

    // 2. Create crm_activities row "Lead received"
    $actStmt = $pdo->prepare("
        INSERT INTO crm_activities (activity_type, title, description, emp_id, created_at)
        VALUES ('Inquiry', 'Lead received', :desc, :emp, NOW())
    ");
    $actStmt->execute([
        ':desc' => "New RFQ lead {$leadCode} received from {$contact} ({$company}) via Corporate Web Platform",
        ':emp'  => $salesRep
    ]);

    // 3. Emit integration event WEB -> CRM via unified vp_emit()
    try {
        vp_emit(
            $pdo,
            'WEB_TO_CRM',
            'WEB',
            'CRM',
            'LEAD_RECEIVED',
            [
                'lead_id'              => $leadCode,
                'lead_db_id'           => $newId,
                'company'              => $company,
                'contact'              => $contact,
                'email'                => $email,
                'assigned_sales_rep'   => $salesRep,
                'estimated_value'      => $estVal,
                'entity_type'          => 'lead',
                'entity_id'            => $leadCode,
                'summary'              => "Commercial lead {$leadCode} ({$company}) ingested and dispatched to CRM queue",
                'notification_title'   => "New Commercial Lead: {$company}",
                'notification_message' => "Lead {$leadCode} assigned to {$salesRep} from Corporate Web RFQ intake."
            ],
            'SYSTEM',
            200
        );
    } catch (Throwable $ex) {
        error_log("Failed to emit WEB_TO_CRM event in contact.php: " . $ex->getMessage());
        // Do not fail the client response if notification/bus logging throws
    }

    echo json_encode([
        'success'   => true,
        'lead_id'   => $leadCode,
        'db_id'     => $newId,
        'company'   => $company,
        'contact'   => $contact,
        'assigned_rep' => $salesRep,
        'message'   => 'Inquiry successfully saved and dispatched to CRM engineering queue.'
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Database error: ' . $e->getMessage()
    ]);
}
