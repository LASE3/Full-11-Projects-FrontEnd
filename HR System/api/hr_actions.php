<?php

/**
 * VOSTOKPRIBOR HR System - Action Dispatcher API
 * Handles asynchronous AJAX actions for HR management operations:
 * - Register new employee
 * - Update employee dossier / clearance level
 * - Advance onboarding step
 * - Initiate & advance offboarding workflow
 * - Approve or reject leave requests
 */

require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('HR', []);

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../hr_service.php';

// Ensure user is authenticated to HR system
if (empty($_SESSION['vostok_authenticated']) || (empty($_SESSION['vostok_system_HR']) && !isSuperAdmin($_vp_user))) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Please log in to HR System.']);
    exit;
}

// Ensure clearance for state-modifying actions
if (!hr_canManageHR()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: Insufficient clearance to modify personnel records.']);
    exit;
}

// Collect action and payload
$inputRaw = file_get_contents('php://input');
$json = json_decode($inputRaw, true) ?: [];

$action = $_POST['action'] ?? $json['action'] ?? $_GET['action'] ?? '';
$payload = array_merge($_POST, $json);

switch ($action) {
    case 'add_employee':
        if (empty($payload['full_name'])) {
            echo json_encode(['success' => false, 'message' => 'Employee full name is required.']);
            exit;
        }
        $res = hr_createEmployee($payload);
        echo json_encode($res);
        break;

    case 'update_employee':
        $empId = trim($payload['emp_id'] ?? '');
        if (empty($empId)) {
            echo json_encode(['success' => false, 'message' => 'Employee ID is required.']);
            exit;
        }
        $res = hr_updateEmployee($empId, $payload);
        echo json_encode($res);
        break;

    case 'get_employee_dossier':
        $empId = trim($payload['emp_id'] ?? $_GET['emp_id'] ?? '');
        if (empty($empId)) {
            echo json_encode(['success' => false, 'message' => 'Employee ID is required.']);
            exit;
        }
        $emp = hr_getEmployeeById($empId);
        if ($emp) {
            echo json_encode(['success' => true, 'employee' => $emp]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Employee not found.']);
        }
        break;

    case 'advance_onboarding':
        $empId = trim($payload['emp_id'] ?? '');
        $step = trim($payload['step'] ?? '');
        $status = trim($payload['status'] ?? 'Completed');
        if (empty($empId) || empty($step)) {
            echo json_encode(['success' => false, 'message' => 'Employee ID and step name are required.']);
            exit;
        }
        $res = hr_advanceOnboardingStep($empId, $step, $status);
        echo json_encode($res);
        break;

    case 'initiate_offboarding':
        $empId = trim($payload['emp_id'] ?? '');
        $reason = trim($payload['reason'] ?? 'Personnel Separation');
        if (empty($empId)) {
            echo json_encode(['success' => false, 'message' => 'Employee ID is required.']);
            exit;
        }
        $res = hr_initiateOffboarding($empId, $reason);
        echo json_encode($res);
        break;

    case 'advance_offboarding':
        $empId = trim($payload['emp_id'] ?? '');
        $step = trim($payload['step'] ?? '');
        $status = trim($payload['status'] ?? 'Completed');
        if (empty($empId) || empty($step)) {
            echo json_encode(['success' => false, 'message' => 'Employee ID and step name are required.']);
            exit;
        }
        $res = hr_advanceOffboardingStep($empId, $step, $status);
        echo json_encode($res);
        break;

    case 'process_leave':
        $leaveId = (int) ($payload['leave_id'] ?? 0);
        $decision = trim($payload['decision'] ?? 'Approved');
        if ($leaveId <= 0 || !in_array($decision, ['Approved', 'Rejected'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid leave ID or decision.']);
            exit;
        }
        $currUser = hr_getCurrentUser();
        $res = hr_processLeaveRequest($leaveId, $decision, $currUser['user_id'] ?? 'EMP-0001');
        echo json_encode($res);
        break;

    case 'create_leave':
        $empId = trim($payload['emp_id'] ?? '');
        $leaveType = trim($payload['leave_type'] ?? 'Annual Leave');
        $startDate = trim($payload['start_date'] ?? '');
        $endDate = trim($payload['end_date'] ?? '');
        if (empty($empId) || empty($startDate) || empty($endDate)) {
            echo json_encode(['success' => false, 'message' => 'Employee, start date, and end date are required.']);
            exit;
        }
        $res = hr_createLeaveRequest($empId, $leaveType, $startDate, $endDate);
        echo json_encode($res);
        break;

    case 'create_training':
        $empId = trim($payload['emp_id'] ?? '');
        $trainingName = trim($payload['training_name'] ?? '');
        $completedAt = trim($payload['completed_at'] ?? date('Y-m-d'));
        if (empty($empId) || empty($trainingName)) {
            echo json_encode(['success' => false, 'message' => 'Employee and training course title are required.']);
            exit;
        }
        $res = hr_createTrainingRecord($empId, $trainingName, $completedAt);
        echo json_encode($res);
        break;

    case 'complete_offboarding':
        $empId = trim($payload['emp_id'] ?? '');
        if (empty($empId)) {
            echo json_encode(['success' => false, 'message' => 'Employee ID is required.']);
            exit;
        }
        $res = hr_completeOffboarding($empId);
        echo json_encode($res);
        break;

    case 'delete_employee':
        $empId = trim($payload['emp_id'] ?? '');
        if (empty($empId)) {
            echo json_encode(['success' => false, 'message' => 'Employee ID is required.']);
            exit;
        }
        $res = hr_deleteEmployee($empId);
        echo json_encode($res);
        break;

    case 'enroll_onboarding':
        $empId = trim($payload['emp_id'] ?? '');
        if (empty($empId)) {
            echo json_encode(['success' => false, 'message' => 'Employee ID is required.']);
            exit;
        }
        $res = hr_enrollOnboarding($empId);
        echo json_encode($res);
        break;

    case 'create_job_posting':
        $title = trim($payload['title'] ?? '');
        $deptCode = trim($payload['department_code'] ?? '');
        $isPublished = isset($payload['is_published']) ? (int)$payload['is_published'] : 1;
        if (empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Job title is required.']);
            exit;
        }
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("INSERT INTO job_postings (title, department_code, is_published, posted_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$title, $deptCode ?: null, $isPublished]);
        $newId = (int)$pdo->lastInsertId();
        
        $stmt = $pdo->prepare("SELECT jp.*, d.dept_name FROM job_postings jp LEFT JOIN departments d ON jp.department_code = d.dept_code WHERE jp.posting_id = ?");
        $stmt->execute([$newId]);
        $posting = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'message' => 'Job opening created and published successfully.',
            'posting_id' => $newId,
            'data' => $posting
        ]);
        break;

    case 'toggle_job_posting':
        $postingId = (int)($payload['posting_id'] ?? 0);
        $isPublished = (int)($payload['is_published'] ?? 0);
        if ($postingId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Valid posting ID is required.']);
            exit;
        }
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("UPDATE job_postings SET is_published = ? WHERE posting_id = ?");
        $stmt->execute([$isPublished, $postingId]);
        echo json_encode([
            'success' => true,
            'message' => 'Job posting publication status updated.',
            'posting_id' => $postingId,
            'is_published' => $isPublished
        ]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => "Unrecognized action '{$action}'."]);
        break;
}
