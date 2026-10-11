<?php

declare(strict_types=1);

/**
 * Class 4: Employee Intranet - Leave Requests API (v1)
 * Location: api/v1/intranet/leaves.php
 * Methods: GET, POST, PATCH
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../includes/auth_guard.php';
require_once __DIR__ . '/../../../includes/authorize.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/I18n.php';
require_once __DIR__ . '/../../helpers/AuditLogger.php';

$user = requireApiAuth('EMP');

$pdo = getDbConnection();
$empId = (string)($user['emp_id'] ?? ($user['user_id'] ?? ''));
$dept  = strtoupper(trim((string)($user['department_code'] ?? '')));
$lang   = $_GET['lang'] ?? 'en';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (empty($empId)) {
    Response::error("Authenticated employee session required.", 401);
}

if ($method === 'GET') {
    try {
        $isHrOrAdmin = isSuperAdmin($user) || ($dept === 'HRA') || (($user['clearance_level'] ?? '') === 'L4');
        $viewAll = isset($_GET['all']) && $isHrOrAdmin;

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
                    lr.reason,
                    lr.status,
                    ap.full_name AS approved_by_name
                FROM leave_requests lr
                JOIN employees e ON lr.emp_id = e.emp_id
                LEFT JOIN employees ap ON lr.approved_by_emp_id = ap.emp_id
                ORDER BY lr.leave_id DESC
            ");
            $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Employee sees own requests + managers see direct reports
            $stmt = $pdo->prepare("
                SELECT 
                    lr.leave_id,
                    lr.emp_id,
                    e.full_name AS employee_name,
                    e.department_code,
                    lr.leave_type,
                    lr.start_date,
                    lr.end_date,
                    lr.reason,
                    lr.status,
                    ap.full_name AS approved_by_name
                FROM leave_requests lr
                JOIN employees e ON lr.emp_id = e.emp_id
                LEFT JOIN employees ap ON lr.approved_by_emp_id = ap.emp_id
                WHERE lr.emp_id = :eid OR e.manager_emp_id = :mgr_eid
                ORDER BY lr.leave_id DESC
            ");
            $stmt->execute([':eid' => $empId, ':mgr_eid' => $empId]);
            $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        foreach ($leaves as &$l) {
            $l['status_display']     = I18n::translate((string)$l['status'], $lang);
            $l['leave_type_display'] = I18n::translate((string)$l['leave_type'], $lang);
        }

        Response::success($leaves, "Leave ledger loaded");
    } catch (Exception $e) {
        error_log("Failed to load leaves: " . $e->getMessage());
        Response::error("Failed to load leaves. Please try again later.", 500);
    }
}

if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $data  = json_decode($rawInput, true) ?: $_POST;
    $type  = trim((string)($data['leave_type'] ?? 'Annual Leave'));
    $start = trim((string)($data['start_date'] ?? ''));
    $end   = trim((string)($data['end_date'] ?? ''));
    $reason = trim((string)($data['reason'] ?? ''));
    // Identity strictly from session (never client body)
    $applicant = $empId;

    if (empty($type) || empty($start) || empty($end)) {
        Response::error("Leave type, start date, and end date required.", 422);
    }

    if (strtotime($end) < strtotime($start)) {
        Response::error("End date cannot precede start date.", 422);
    }

    try {
        vp_authorize($user, 'leave:create', ['emp_id' => $applicant], $pdo);

        $stmt = $pdo->prepare("
            INSERT INTO leave_requests (emp_id, leave_type, start_date, end_date, reason, status)
            VALUES (:eid, :typ, :std, :end, :rsn, 'Pending')
        ");
        $stmt->execute([
            ':eid' => $applicant,
            ':typ' => $type,
            ':std' => $start,
            ':end' => $end,
            ':rsn' => $reason ?: null
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
            ['type' => $type, 'start' => $start, 'end' => $end, 'reason' => $reason],
            'SUCCESS'
        );

        Response::success([
            'leave_id' => $newId,
            'emp_id'   => $applicant,
            'leave_type' => $type,
            'start_date' => $start,
            'end_date'   => $end,
            'reason'     => $reason,
            'status'     => 'Pending',
            'status_display' => I18n::translate('Pending', $lang)
        ], "Leave request filed successfully", 201);
    } catch (VpAuthorizationException $ae) {
        Response::error($ae->getMessage(), $ae->getCode());
    } catch (Exception $e) {
        error_log("Failed to submit leave: " . $e->getMessage());
        Response::error("Failed to submit leave. Please try again later.", 500);
    }
}

if ($method === 'PATCH') {
    // Approve / Reject decision
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?: $_POST;
    $leaveId = (int)($data['leave_id'] ?? 0);
    $decision = trim((string)($data['decision'] ?? '')); // 'Approved' or 'Rejected'
    // Approver strictly from session
    $approver = $empId;

    if ($leaveId <= 0) {
        Response::error("Valid leave ID required.", 422);
    }

    if (!in_array($decision, ['Approved', 'Rejected'], true)) {
        Response::error("Decision must be 'Approved' or 'Rejected'.", 422);
    }

    try {
        $stmt = $pdo->prepare("
            SELECT lr.*, e.manager_emp_id 
            FROM leave_requests lr
            JOIN employees e ON lr.emp_id = e.emp_id
            WHERE lr.leave_id = ?
            LIMIT 1
        ");
        $stmt->execute([$leaveId]);
        $leave = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$leave) {
            Response::error("Leave request not found.", 404);
        }

        $action = ($decision === 'Approved') ? 'leave:approve' : 'leave:reject';
        vp_authorize($user, $action, $leave, $pdo);

        $upd = $pdo->prepare("
            UPDATE leave_requests 
            SET status = :dec, approved_by_emp_id = :appr
            WHERE leave_id = :lid
        ");
        $upd->execute([
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
            ['decision' => $decision, 'applicant_emp_id' => $leave['emp_id']],
            'SUCCESS'
        );

        Response::success(['leave_id' => $leaveId, 'status' => $decision], "Leave request status updated");
    } catch (VpAuthorizationException $ae) {
        Response::error($ae->getMessage(), $ae->getCode());
    } catch (Exception $e) {
        error_log("Failed to process leave decision: " . $e->getMessage());
        Response::error("Failed to process leave decision. Please try again later.", 500);
    }
}
