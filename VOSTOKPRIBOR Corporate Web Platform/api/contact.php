<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR Corporate Web Platform - Contact & RFQ Intake API
 * Saves contact/RFQ submissions directly to the database and dispatches them to CRM.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed. Use POST.']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: $_POST;

$company = trim((string)($data['company'] ?? $data['company_name'] ?? ''));
$contact = trim((string)($data['contact'] ?? $data['contact_name'] ?? $data['full_name'] ?? ''));
$email   = trim((string)($data['email'] ?? ''));
$sector  = trim((string)($data['sector'] ?? ''));
$interest = trim((string)($data['interest'] ?? ''));
$budget  = trim((string)($data['budget'] ?? ''));
$notes   = trim((string)($data['notes'] ?? $data['message'] ?? ''));

if (empty($company) || empty($contact) || empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Company, contact name, and corporate email are required.']);
    exit;
}

try {
    $pdo = getDbConnection();

    // Map estimated budget value
    $estVal = match (strtolower($budget)) {
        'large' => 500000.00,
        'medium' => 200000.00,
        'evaluation' => 75000.00,
        default => 100000.00
    };

    // Find default sales rep in CRM
    $salesRep = $pdo->query("SELECT emp_id FROM employees WHERE department_code = 'SAL' AND employment_status = 'Active' LIMIT 1")->fetchColumn() ?: 'EMP-1006';

    $composedMessage = "Sector: " . ($sector ?: 'General') . 
                       " | Equipment Interest: " . ($interest ?: 'Turnkey Package') . 
                       " | Budget Scope: " . ($budget ?: 'Standard') . 
                       ($notes ? "\n\nNotes: " . $notes : "");

    // 1. Insert lead into CRM leads table
    $stmt = $pdo->prepare("
        INSERT INTO leads (full_name, email, phone, company_name, message, source_page, status, assigned_sales_emp_id, created_at, estimated_value, priority)
        VALUES (:fn, :em, '', :comp, :msg, 'Corporate Web Platform - Contact RFQ', 'New', :rep, NOW(), :val, 'High')
    ");
    $stmt->execute([
        ':fn'   => $contact,
        ':em'   => $email,
        ':comp' => $company,
        ':msg'  => $composedMessage,
        ':rep'  => $salesRep,
        ':val'  => $estVal
    ]);

    $newId = (int)$pdo->lastInsertId();
    $leadCode = 'LEAD-2026-' . str_pad((string)$newId, 4, '0', STR_PAD_LEFT);

    // 2. Log system integration event (WEB -> CRM)
    try {
        $logStmt = $pdo->prepare("
            INSERT INTO system_integration_logs (link_code, source_system_id, target_system_id, payload_summary, status, response_time_ms, created_at)
            VALUES ('SYS11_TO_SYS02', 'WEB', 'CRM', :summary, 'SUCCESS', 45, NOW())
        ");
        $logStmt->execute([
            ':summary' => "Commercial lead {$leadCode} ({$company}) ingested and dispatched to CRM queue"
        ]);
    } catch (Throwable $e) {
        // Table or columns might vary, ignore integration logging failure
    }

    echo json_encode([
        'success'   => true,
        'lead_id'   => $leadCode,
        'db_id'     => $newId,
        'company'   => $company,
        'contact'   => $contact,
        'message'   => 'Inquiry successfully saved and dispatched to CRM engineering queue.'
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Database error: ' . $e->getMessage()
    ]);
}
