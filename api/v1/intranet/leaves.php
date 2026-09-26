<?php

/**
 * Class 4: Employee Intranet - Leave Requests API
 * Location: api/v1/intranet/leaves.php
 * Methods: GET, POST, PATCH
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/I18n.php';
require_once __DIR__ . '/../../helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$empId  = $_SESSION['emp_id'] ?? ($_GET['emp_id'] ?? 'EMP-1007');
$lang   = $_GET['lang'] ?? 'en';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    try {
        $viewAll = isset($_GET['all']) && in_array($_SESSION['clearance_level'] ?? 'L1', ['L3', 'L4']);

        if ($viewAll) {
            $stmt = $pdo->query("
                SELECT 
                    lr.leave_id,
                    lr.emp_id,
                    e.full_name AS employee_name,
                    e.department_code,
                    lr.leave_type,
                    lr.start_date,
                    lr.end_date,
                    lr.status,
                    ap.full_name AS approved_by_name
                FROM leave_requests lr
                JOIN employees e ON lr.emp_id = e.emp_id
                LEFT JOIN employees ap ON lr.approved_by_emp_id = ap.emp_id
                ORDER BY lr.leave_id DESC
            ");
            $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $pdo->prepare("
                SELECT 
                    lr.leave_id,
                    lr.emp_id,
                    lr.leave_type,
                    lr.start_date,
                    lr.end_date,
                    lr.status,
                    ap.full_name AS approved_by_name
                FROM leave_requests lr
                LEFT JOIN employees ap ON lr.approved_by_emp_id = ap.emp_id
                WHERE lr.emp_id = :eid
                ORDER BY lr.leave_id DESC
            ");
            $stmt->execute([':eid' => $empId]);
            $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        foreach ($leaves as &$l) {
            $l['status_display'] = I18n::translate($l['status'], $lang);
            $l['leave_type_display'] = I18n::translate($l['leave_type'], $lang);
        }

        Response::success($leaves, "Leave ledger loaded");
    } catch (Exception $e) {
        Response::error("Failed to load leaves: " . $e->getMessage(), 500);
    }
}

if ($method === 'POST') {
    $data  = json_decode(file_get_contents('php://input'), true);
    $type  = trim($data['leave_type'] ?? 'Annual Leave');
    $start = trim($data['start_date'] ?? '');
    $end   = trim($data['end_date'] ?? '');
    $applicant = $empId ?: ($data['emp_id'] ?? 'EMP-1018');

    if (empty($type) || empty($start) || empty($end)) {
        Response::error("Leave type, start date, and end date required.", 422);
    }

    if (strtotime($end) < strtotime($start)) {
        Response::error("End date cannot precede start date.", 422);
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO leave_requests (emp_id, leave_type, start_date, end_date, status)
            VALUES (:eid, :typ, :std, :end, 'Pending')
        ");
        $stmt->execute([
            ':eid' => $applicant,
            ':typ' => $type,
            ':std' => $start,
            ':end' => $end
        ]);
        $newId = (int)$pdo->lastInsertId();

        AuditLogger::logAction(
            $applicant,
            null,
            'Employee Intranet',
            'EMP',
            'SUBMIT_LEAVE_REQUEST',
            'leave_requests',
            (string)$newId,
            ['type' => $type, 'start' => $start, 'end' => $end],
            'SUCCESS'
        );

        Response::success([
            'leave_id' => $newId,
            'status'   => 'Pending',
            'status_display' => I18n::translate('Pending', $lang)
        ], "Leave request filed successfully", 201);
    } catch (Exception $e) {
        Response::error("Failed to submit leave: " . $e->getMessage(), 500);
    }
}

if ($method === 'PATCH') {
    // Approve / Reject decision
    $data = json_decode(file_get_contents('php://input'), true);
    $leaveId = (int)($data['leave_id'] ?? 0);
    $decision = trim($data['decision'] ?? ''); // 'Approved' or 'Rejected'
    $approver = $empId ?: ($data['approved_by_emp_id'] ?? 'EMP-1002');

    if (!in_array($decision, ['Approved', 'Rejected'], true)) {
        Response::error("Decision must be 'Approved' or 'Rejected'.", 422);
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE leave_requests 
            SET status = :dec, approved_by_emp_id = :appr
            WHERE leave_id = :lid
        ");
        $stmt->execute([
            ':dec'  => $decision,
            ':appr' => $approver,
            ':lid'  => $leaveId
        ]);

        AuditLogger::logAction(
            $approver,
            null,
            'Employee Intranet',
            'EMP',
            "LEAVE_REQUEST_" . strtoupper($decision),
            'leave_requests',
            (string)$leaveId,
            ['decision' => $decision],
            'SUCCESS'
        );

        Response::success(['leave_id' => $leaveId, 'status' => $decision], "Leave request status updated");
    } catch (Exception $e) {
        Response::error("Failed to process leave decision: " . $e->getMessage(), 500);
    }
}
