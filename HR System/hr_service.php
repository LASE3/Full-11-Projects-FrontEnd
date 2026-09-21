<?php
/**
 * VOSTOKPRIBOR HR System - Core Backend Data Service
 * Provides secure, database-driven operations for employee lifecycle management,
 * clearance-based access verification, onboarding/offboarding workflows, and telemetry.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';

/**
 * Get active user from session or fallback
 */
function hr_getCurrentUser() {
    global $currentUser;
    if (!empty($_SESSION['vostok_user'])) {
        return $_SESSION['vostok_user'];
    }
    return $currentUser;
}

/**
 * Check if the active user has management clearance in HR (L4 or HR department / Executive)
 */
function hr_canManageHR() {
    $u = hr_getCurrentUser();
    $clearance = $u['clearance_level'] ?? 'L1';
    $dept = $u['department_code'] ?? '';
    
    // Level 4 (Executive) has full management rights
    if ($clearance === 'L4') return true;
    
    // HR Department (HRA, HR) with Level 3 or higher
    if (in_array($dept, ['HRA', 'HR', 'EXE']) && in_array($clearance, ['L3', 'L4'])) {
        return true;
    }

    return false;
}

/**
 * Retrieve high-level KPI metrics for the HR Dashboard
 */
function hr_getDashboardMetrics() {
    $pdo = getDbConnection();

    // 1. Total Active Headcount
    $stmt = $pdo->query("SELECT COUNT(*) FROM employees WHERE employment_status = 'Active' AND emp_id != 'EMP-0001' AND email != 'admin@gmail.com'");
    $activeHeadcount = (int) $stmt->fetchColumn();

    // 2. Active Onboarding Pipelines
    $stmt = $pdo->query("
        SELECT COUNT(DISTINCT emp_id) 
        FROM employee_onboarding 
        WHERE status != 'Completed'
    ");
    $activeOnboarding = (int) $stmt->fetchColumn();

    // 3. Active Offboarding Cases
    $stmt = $pdo->query("
        SELECT COUNT(DISTINCT emp_id) 
        FROM employee_offboarding 
        WHERE status != 'Completed'
    ");
    $activeOffboarding = (int) $stmt->fetchColumn();

    // 4. Pending Leave Requests
    $stmt = $pdo->query("SELECT COUNT(*) FROM leave_requests WHERE status = 'Pending'");
    $pendingLeaves = (int) $stmt->fetchColumn();

    // 5. Clearance Tier Counts
    $clearanceCounts = ['L4' => 0, 'L3' => 0, 'L2' => 0, 'L1' => 0];
    $stmt = $pdo->query("SELECT clearance_level, COUNT(*) AS cnt FROM employees WHERE employment_status = 'Active' AND emp_id != 'EMP-0001' AND email != 'admin@gmail.com' GROUP BY clearance_level");
    while ($row = $stmt->fetch()) {
        if (isset($clearanceCounts[$row['clearance_level']])) {
            $clearanceCounts[$row['clearance_level']] = (int) $row['cnt'];
        }
    }

    // 6. Department Distribution
    $deptDistribution = [];
    $stmt = $pdo->query("
        SELECT d.dept_code, d.dept_name, d.employee_count_target, COUNT(e.emp_id) AS current_count
        FROM departments d
        LEFT JOIN employees e ON d.dept_code = e.department_code AND e.employment_status = 'Active' AND e.emp_id != 'EMP-0001' AND e.email != 'admin@gmail.com'
        GROUP BY d.dept_code, d.dept_name, d.employee_count_target
        ORDER BY current_count DESC
    ");
    while ($row = $stmt->fetch()) {
        $deptDistribution[] = [
            'code' => $row['dept_code'],
            'name' => $row['dept_name'],
            'current' => (int) $row['current_count'],
            'target' => (int) ($row['employee_count_target'] ?: 10)
        ];
    }

    // 7. Recent Activity Feed
    $recentActivity = [];
    // Recent employees
    $stmt = $pdo->query("SELECT emp_id, full_name, job_title, hire_date, clearance_level FROM employees WHERE emp_id != 'EMP-0001' AND email != 'admin@gmail.com' ORDER BY emp_id DESC LIMIT 5");
    while ($row = $stmt->fetch()) {
        $recentActivity[] = [
            'type' => 'NEW_HIRE',
            'title' => "Personnel File Created: {$row['full_name']}",
            'subtitle' => "{$row['job_title']} · Level {$row['clearance_level']}",
            'time' => $row['hire_date'] ? date('M d, Y', strtotime($row['hire_date'])) : 'Recently'
        ];
    }

    return [
        'active_headcount'   => $activeHeadcount,
        'active_onboarding'  => $activeOnboarding,
        'active_offboarding' => $activeOffboarding,
        'pending_leaves'     => $pendingLeaves,
        'clearance_counts'   => $clearanceCounts,
        'dept_distribution'  => $deptDistribution,
        'recent_activity'    => $recentActivity
    ];
}

/**
 * Retrieve list of employees with optional filters
 */
function hr_getEmployees($search = '', $dept = '', $clearance = '', $status = '') {
    $pdo = getDbConnection();
    
    $sql = "
        SELECT 
            e.emp_id,
            e.full_name,
            e.job_title,
            e.department_code,
            d.dept_name,
            e.clearance_level,
            e.email,
            e.employment_status,
            e.hire_date,
            e.manager_emp_id,
            m.full_name AS manager_name,
            ea.username,
            ea.status AS account_status,
            ea.last_login
        FROM employees e
        LEFT JOIN departments d ON e.department_code = d.dept_code
        LEFT JOIN employees m ON e.manager_emp_id = m.emp_id
        LEFT JOIN employee_accounts ea ON e.emp_id = ea.emp_id
        WHERE 1=1 AND e.emp_id != 'EMP-0001' AND e.email != 'admin@gmail.com'
    ";

    $params = [];

    if (!empty($search)) {
        $sql .= " AND (e.emp_id LIKE :s1 OR e.full_name LIKE :s2 OR e.email LIKE :s3 OR e.job_title LIKE :s4)";
        $params[':s1'] = "%{$search}%";
        $params[':s2'] = "%{$search}%";
        $params[':s3'] = "%{$search}%";
        $params[':s4'] = "%{$search}%";
    }

    if (!empty($dept)) {
        $sql .= " AND e.department_code = :dept";
        $params[':dept'] = $dept;
    }

    if (!empty($clearance)) {
        $sql .= " AND e.clearance_level = :clr";
        $params[':clr'] = $clearance;
    }

    if (!empty($status)) {
        $sql .= " AND e.employment_status = :stat";
        $params[':stat'] = $status;
    }

    // Group by emp_id to avoid duplicate rows from multiple aliases
    $sql .= " GROUP BY e.emp_id ORDER BY e.emp_id ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get full dossier for a single employee
 */
function hr_getEmployeeById($empId) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("
        SELECT 
            e.*,
            d.dept_name,
            m.full_name AS manager_name,
            ea.username,
            ea.status AS account_status,
            ea.last_login,
            r.role_name
        FROM employees e
        LEFT JOIN departments d ON e.department_code = d.dept_code
        LEFT JOIN employees m ON e.manager_emp_id = m.emp_id
        LEFT JOIN employee_accounts ea ON e.emp_id = ea.emp_id
        LEFT JOIN employee_roles er ON e.emp_id = er.emp_id
        LEFT JOIN roles r ON er.role_id = r.role_id
        WHERE e.emp_id = ?
        LIMIT 1
    ");
    $stmt->execute([$empId]);
    return $stmt->fetch();
}

/**
 * Fetch all departments
 */
function hr_getDepartments() {
    $pdo = getDbConnection();
    return $pdo->query("SELECT * FROM departments ORDER BY dept_name ASC")->fetchAll();
}

/**
 * Create a new employee atomically across tables
 */
function hr_createEmployee($data) {
    $pdo = getDbConnection();
    $pdo->beginTransaction();

    try {
        // 1. Determine next EMP ID if not provided
        $empId = trim($data['emp_id'] ?? '');
        if (empty($empId)) {
            $stmtMax = $pdo->query("
                SELECT emp_id FROM employees 
                WHERE emp_id LIKE 'EMP-%' 
                ORDER BY CAST(SUBSTRING(emp_id, 5) AS UNSIGNED) DESC 
                LIMIT 1
            ");
            $lastId = $stmtMax->fetchColumn();
            if ($lastId && preg_match('/EMP-(\d+)/', $lastId, $m)) {
                $nextNum = (int)$m[1] + 1;
                $empId = 'EMP-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
            } else {
                $empId = 'EMP-1021';
            }
        }

        $fullName    = trim($data['full_name'] ?? '');
        $jobTitle    = trim($data['job_title'] ?? 'Staff Specialist');
        $deptCode    = trim($data['department_code'] ?? 'HRA');
        $clearance   = trim($data['clearance_level'] ?? 'L2');
        $email       = trim($data['email'] ?? strtolower(str_replace(' ', '.', $fullName)) . '@vostokpribor.local');
        $managerId   = !empty($data['manager_emp_id']) ? $data['manager_emp_id'] : null;
        $hireDate    = !empty($data['hire_date']) ? $data['hire_date'] : date('Y-m-d');
        $password    = !empty($data['password']) ? $data['password'] : 'Vostok2026!';
        $passHash    = password_hash($password, PASSWORD_BCRYPT);
        $username    = trim($data['username'] ?? '') ?: strtolower(str_replace(' ', '.', $fullName));

        // Insert into employees
        $stmtEmp = $pdo->prepare("
            INSERT INTO employees (emp_id, full_name, job_title, department_code, clearance_level, email, manager_emp_id, employment_status, hire_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Active', ?)
        ");
        $stmtEmp->execute([$empId, $fullName, $jobTitle, $deptCode, $clearance, $email, $managerId, $hireDate]);

        // Insert into employee_accounts
        $stmtAcc = $pdo->prepare("
            INSERT INTO employee_accounts (emp_id, username, password_hash, status, created_at)
            VALUES (?, ?, ?, 'Active', NOW())
        ");
        $stmtAcc->execute([$empId, $username, $passHash]);

        // Insert primary ID alias
        if ($username !== $empId) {
            $stmtAcc->execute([$empId, $empId, $passHash]);
        }

        // Assign role based on department
        $deptRoles = [
            'EXE' => 1, 'GOV' => 2, 'SAL' => 3, 'ENG' => 4,
            'ITD' => 5, 'FIN' => 6, 'HRA' => 7, 'OPS' => 8
        ];
        $roleId = $deptRoles[$deptCode] ?? 7;
        $stmtRole = $pdo->prepare("INSERT INTO employee_roles (emp_id, role_id, granted_at) VALUES (?, ?, NOW())");
        $stmtRole->execute([$empId, $roleId]);

        // Initialize Onboarding Pipeline
        $onboardingSteps = [
            ['RecordCreated', 'Completed'],
            ['AccessRequested', 'In Progress'],
            ['IntranetGranted', 'Pending'],
            ['SystemAccessGranted', 'Pending'],
            ['DocumentsFiled', 'Pending'],
            ['GovernanceReviewed', 'Pending']
        ];
        $stmtOnboard = $pdo->prepare("INSERT INTO employee_onboarding (emp_id, step, status, completed_at) VALUES (?, ?, ?, NOW())");
        foreach ($onboardingSteps as $step) {
            $stmtOnboard->execute([$empId, $step[0], $step[1]]);
        }

        $pdo->commit();
        return [
            'success'  => true,
            'emp_id'   => $empId,
            'username' => $username,
            'message'  => "Employee {$fullName} ({$empId}) registered successfully."
        ];

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("hr_createEmployee failed: " . $e->getMessage());
        return ['success' => false, 'message' => "Database error: " . $e->getMessage()];
    }
}

/**
 * Update employee metadata and clearance
 */
function hr_updateEmployee($empId, $data) {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare("
            UPDATE employees SET
                full_name = ?,
                job_title = ?,
                department_code = ?,
                clearance_level = ?,
                email = ?,
                employment_status = ?
            WHERE emp_id = ?
        ");
        $stmt->execute([
            $data['full_name'],
            $data['job_title'],
            $data['department_code'],
            $data['clearance_level'],
            $data['email'],
            $data['employment_status'],
            $empId
        ]);

        // If status changed to suspended/terminated, lock account
        if (in_array($data['employment_status'], ['Suspended', 'Terminated'])) {
            $pdo->prepare("UPDATE employee_accounts SET status = ? WHERE emp_id = ?")->execute([$data['employment_status'], $empId]);
        } elseif ($data['employment_status'] === 'Active') {
            $pdo->prepare("UPDATE employee_accounts SET status = 'Active' WHERE emp_id = ?")->execute([$empId]);
        }

        return ['success' => true, 'message' => "Employee {$empId} updated."];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Fetch candidates currently in the Onboarding Pipeline
 */
function hr_getOnboardingCandidates() {
    $pdo = getDbConnection();
    $stmt = $pdo->query("
        SELECT 
            e.emp_id,
            e.full_name,
            e.job_title,
            e.department_code,
            d.dept_name,
            e.clearance_level,
            e.hire_date
        FROM employee_onboarding o
        JOIN employees e ON o.emp_id = e.emp_id
        JOIN departments d ON e.department_code = d.dept_code
        GROUP BY e.emp_id
        ORDER BY e.emp_id DESC
    ");
    $candidates = $stmt->fetchAll();

    foreach ($candidates as &$cand) {
        $stmtSteps = $pdo->prepare("
            SELECT step, status, completed_at 
            FROM employee_onboarding 
            WHERE emp_id = ? 
            ORDER BY onboarding_id ASC
        ");
        $stmtSteps->execute([$cand['emp_id']]);
        $cand['steps'] = $stmtSteps->fetchAll();

        // Calculate progress percentage
        $total = count($cand['steps']);
        $completed = 0;
        foreach ($cand['steps'] as $s) {
            if ($s['status'] === 'Completed') $completed++;
        }
        $cand['progress_percent'] = $total > 0 ? round(($completed / $total) * 100) : 0;
        $cand['completed_steps'] = $completed;
        $cand['total_steps'] = $total;
    }

    return $candidates;
}

/**
 * Advance an onboarding step status in MySQL
 */
function hr_advanceOnboardingStep($empId, $step, $newStatus) {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare("
            UPDATE employee_onboarding 
            SET status = ?, completed_at = NOW() 
            WHERE emp_id = ? AND step = ?
        ");
        $stmt->execute([$newStatus, $empId, $step]);
        return ['success' => true, 'message' => "Step {$step} updated to {$newStatus}."];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Fetch offboarding cases and steps
 */
function hr_getOffboardingCases() {
    $pdo = getDbConnection();
    $stmt = $pdo->query("
        SELECT 
            e.emp_id,
            e.full_name,
            e.job_title,
            e.department_code,
            d.dept_name,
            e.clearance_level,
            e.employment_status,
            ea.status AS account_status
        FROM employee_offboarding o
        JOIN employees e ON o.emp_id = e.emp_id
        JOIN departments d ON e.department_code = d.dept_code
        LEFT JOIN employee_accounts ea ON e.emp_id = ea.emp_id
        GROUP BY e.emp_id
        ORDER BY e.emp_id DESC
    ");
    $cases = $stmt->fetchAll();

    foreach ($cases as &$case) {
        $stmtSteps = $pdo->prepare("
            SELECT offboarding_id, step, status, completed_at 
            FROM employee_offboarding 
            WHERE emp_id = ? 
            ORDER BY offboarding_id ASC
        ");
        $stmtSteps->execute([$case['emp_id']]);
        $case['steps'] = $stmtSteps->fetchAll();

        $total = count($case['steps']);
        $completed = 0;
        foreach ($case['steps'] as $s) {
            if ($s['status'] === 'Completed') $completed++;
        }
        $case['progress_percent'] = $total > 0 ? round(($completed / $total) * 100) : 0;
        $case['completed_steps'] = $completed;
        $case['total_steps'] = $total;
    }

    return $cases;
}

/**
 * Initiate offboarding for an employee
 */
function hr_initiateOffboarding($empId, $reason = 'Resignation') {
    $pdo = getDbConnection();
    $pdo->beginTransaction();

    try {
        // 1. Set employment status to Suspended
        $pdo->prepare("UPDATE employees SET employment_status = 'Suspended' WHERE emp_id = ?")->execute([$empId]);
        
        // 2. Lock employee account
        $pdo->prepare("UPDATE employee_accounts SET status = 'Suspended' WHERE emp_id = ?")->execute([$empId]);

        // 3. Clear existing offboarding steps if any
        $pdo->prepare("DELETE FROM employee_offboarding WHERE emp_id = ?")->execute([$empId]);

        // 4. Populate standard 10-step offboarding checklist
        $steps = [
            'HRInitiated'        => 'Completed',
            'StatusChanged'      => 'Completed',
            'ITNotified'         => 'In Progress',
            'AccessRevoked'      => 'Pending',
            'IntranetRevoked'    => 'Pending',
            'FileCenterReviewed' => 'Pending',
            'CRMRevoked'         => 'Pending',
            'HelpdeskClosed'     => 'Pending',
            'GovernanceVerified' => 'Pending',
            'AuditLogged'        => 'Pending'
        ];

        $stmt = $pdo->prepare("INSERT INTO employee_offboarding (emp_id, step, status, completed_at) VALUES (?, ?, ?, NOW())");
        foreach ($steps as $step => $stat) {
            $stmt->execute([$empId, $step, $stat]);
        }

        $pdo->commit();
        return ['success' => true, 'message' => "Offboarding initiated for employee {$empId}. Access suspended."];

    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Advance offboarding step
 */
function hr_advanceOffboardingStep($empId, $step, $newStatus) {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare("
            UPDATE employee_offboarding 
            SET status = ?, completed_at = NOW() 
            WHERE emp_id = ? AND step = ?
        ");
        $stmt->execute([$newStatus, $empId, $step]);
        return ['success' => true, 'message' => "Offboarding step {$step} updated to {$newStatus}."];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Fetch Leave Requests
 */
function hr_getLeaveRequests($status = '') {
    $pdo = getDbConnection();
    $sql = "
        SELECT 
            lr.leave_id,
            lr.emp_id,
            e.full_name,
            e.job_title,
            e.department_code,
            d.dept_name,
            e.clearance_level,
            lr.leave_type,
            lr.start_date,
            lr.end_date,
            lr.status,
            lr.approved_by_emp_id,
            app.full_name AS approver_name
        FROM leave_requests lr
        JOIN employees e ON lr.emp_id = e.emp_id
        LEFT JOIN departments d ON e.department_code = d.dept_code
        LEFT JOIN employees app ON lr.approved_by_emp_id = app.emp_id
        WHERE 1=1
    ";

    $params = [];
    if (!empty($status)) {
        $sql .= " AND lr.status = :status";
        $params[':status'] = $status;
    }

    $sql .= " ORDER BY lr.leave_id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Process (Approve / Reject) Leave Request
 */
function hr_processLeaveRequest($leaveId, $decision, $approverEmpId = null) {
    $pdo = getDbConnection();
    try {
        if (!$approverEmpId) {
            $user = hr_getCurrentUser();
            $approverEmpId = $user['user_id'] ?? 'EMP-0001';
        }

        $stmt = $pdo->prepare("
            UPDATE leave_requests 
            SET status = ?, approved_by_emp_id = ? 
            WHERE leave_id = ?
        ");
        $stmt->execute([$decision, $approverEmpId, $leaveId]);
        return ['success' => true, 'message' => "Leave request #{$leaveId} marked as {$decision}."];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Create a new leave request
 */
function hr_createLeaveRequest($empId, $leaveType, $startDate, $endDate) {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare("
            INSERT INTO leave_requests (emp_id, leave_type, start_date, end_date, status)
            VALUES (?, ?, ?, ?, 'Pending')
        ");
        $stmt->execute([$empId, $leaveType, $startDate, $endDate]);
        return ['success' => true, 'message' => "Leave request submitted."];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Fetch Training Records
 */
function hr_getTrainingRecords() {
    $pdo = getDbConnection();
    return $pdo->query("
        SELECT 
            tr.training_id,
            tr.emp_id,
            e.full_name,
            e.job_title,
            e.department_code,
            d.dept_name,
            tr.training_name,
            tr.completed_at,
            tr.certificate_doc_id
        FROM training_records tr
        JOIN employees e ON tr.emp_id = e.emp_id
        LEFT JOIN departments d ON e.department_code = d.dept_code
        ORDER BY tr.completed_at DESC
    ")->fetchAll();
}

/**
 * Log new training record
 */
function hr_createTrainingRecord($empId, $trainingName, $completedAt) {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare("
            INSERT INTO training_records (emp_id, training_name, completed_at)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$empId, $trainingName, $completedAt]);
        return ['success' => true, 'message' => "Training certification logged."];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Get Organizational Hierarchy Tree
 */
function hr_getOrgStructure() {
    $pdo = getDbConnection();
    $employees = $pdo->query("
        SELECT 
            e.emp_id,
            e.full_name,
            e.job_title,
            e.department_code,
            d.dept_name,
            e.clearance_level,
            e.email,
            e.manager_emp_id,
            e.employment_status
        FROM employees e
        LEFT JOIN departments d ON e.department_code = d.dept_code
        WHERE e.employment_status = 'Active'
        ORDER BY e.clearance_level DESC, e.emp_id ASC
    ")->fetchAll();

    return $employees;
}

/**
 * Fetch Offboarding Statistics dynamically from database
 */
function hr_getOffboardingStats() {
    $pdo = getDbConnection();

    $stmt1 = $pdo->query("
        SELECT COUNT(DISTINCT emp_id) 
        FROM employee_offboarding 
        WHERE status != 'Completed'
    ");
    $activeCases = (int) $stmt1->fetchColumn();

    $stmt2 = $pdo->query("
        SELECT COUNT(DISTINCT o.emp_id) 
        FROM employee_offboarding o
        WHERE o.emp_id NOT IN (
            SELECT DISTINCT emp_id FROM employee_offboarding WHERE status != 'Completed'
        )
    ");
    $completedCases = (int) $stmt2->fetchColumn();

    $stmt3 = $pdo->query("SELECT COUNT(*) FROM employee_accounts WHERE status = 'Suspended'");
    $suspendedAccounts = (int) $stmt3->fetchColumn();

    $stmt4 = $pdo->query("SELECT COUNT(*) FROM employees WHERE employment_status IN ('Terminated', 'Suspended')");
    $terminatedEmployees = (int) $stmt4->fetchColumn();

    $stmt5 = $pdo->query("SELECT COUNT(*) FROM employee_offboarding");
    $totalSteps = (int) $stmt5->fetchColumn();

    return [
        'active_cases'        => $activeCases,
        'completed_cases'     => $completedCases,
        'suspended_accounts'  => $suspendedAccounts,
        'terminated_employees'=> $terminatedEmployees,
        'total_steps'         => $totalSteps
    ];
}

/**
 * Fetch Onboarding Statistics dynamically from database
 */
function hr_getOnboardingStats() {
    $pdo = getDbConnection();

    $stmt1 = $pdo->query("
        SELECT COUNT(DISTINCT emp_id) 
        FROM employee_onboarding 
        WHERE status != 'Completed'
    ");
    $activeCount = (int) $stmt1->fetchColumn();

    $stmt2 = $pdo->query("
        SELECT COUNT(DISTINCT o.emp_id) 
        FROM employee_onboarding o
        WHERE o.emp_id NOT IN (
            SELECT DISTINCT emp_id FROM employee_onboarding WHERE status != 'Completed'
        )
    ");
    $completedCount = (int) $stmt2->fetchColumn();

    $stmt3 = $pdo->query("SELECT COUNT(*) FROM employee_onboarding");
    $totalSteps = (int) $stmt3->fetchColumn();

    $stmt4 = $pdo->query("SELECT COUNT(*) FROM employee_onboarding WHERE status = 'Completed'");
    $completedSteps = (int) $stmt4->fetchColumn();

    return [
        'active_count'    => $activeCount,
        'completed_count' => $completedCount,
        'total_steps'     => $totalSteps,
        'completed_steps' => $completedSteps,
        'total_candidates'=> $activeCount + $completedCount
    ];
}

/**
 * Complete offboarding and finalize employee termination
 */
function hr_completeOffboarding($empId) {
    $pdo = getDbConnection();
    $pdo->beginTransaction();
    try {
        $pdo->prepare("UPDATE employee_offboarding SET status = 'Completed', completed_at = NOW() WHERE emp_id = ?")->execute([$empId]);
        $pdo->prepare("UPDATE employees SET employment_status = 'Terminated' WHERE emp_id = ?")->execute([$empId]);
        $pdo->prepare("UPDATE employee_accounts SET status = 'Suspended' WHERE emp_id = ?")->execute([$empId]);

        $pdo->commit();
        return ['success' => true, 'message' => "Offboarding completed. Employee {$empId} status set to Terminated and credentials locked."];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Delete or permanently decommission employee record
 */
function hr_deleteEmployee($empId) {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare("SELECT full_name FROM employees WHERE emp_id = ?");
        $stmt->execute([$empId]);
        $emp = $stmt->fetch();
        if (!$emp) {
            return ['success' => false, 'message' => "Employee {$empId} not found."];
        }

        $pdo->beginTransaction();

        $pdo->prepare("UPDATE employees SET manager_emp_id = NULL WHERE manager_emp_id = ?")->execute([$empId]);
        $pdo->prepare("DELETE FROM employee_roles WHERE emp_id = ? OR granted_by_emp_id = ?")->execute([$empId, $empId]);
        $pdo->prepare("DELETE FROM employee_accounts WHERE emp_id = ?")->execute([$empId]);
        $pdo->prepare("DELETE FROM employee_onboarding WHERE emp_id = ?")->execute([$empId]);
        $pdo->prepare("DELETE FROM employee_offboarding WHERE emp_id = ?")->execute([$empId]);
        $pdo->prepare("DELETE FROM training_records WHERE emp_id = ?")->execute([$empId]);
        $pdo->prepare("DELETE FROM leave_requests WHERE emp_id = ? OR approved_by_emp_id = ?")->execute([$empId, $empId]);

        $pdo->prepare("DELETE FROM employees WHERE emp_id = ?")->execute([$empId]);

        $pdo->commit();
        return ['success' => true, 'message' => "Employee {$emp['full_name']} ({$empId}) removed from database."];

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        if (strpos($e->getMessage(), '1451') !== false || strpos($e->getMessage(), 'foreign key') !== false) {
            $pdo->prepare("UPDATE employees SET employment_status = 'Terminated' WHERE emp_id = ?")->execute([$empId]);
            $pdo->prepare("UPDATE employee_accounts SET status = 'Suspended' WHERE emp_id = ?")->execute([$empId]);
            return [
                'success' => true,
                'message' => "Employee {$empId} has historical operational references (audit logs, documents, tickets) and cannot be hard-deleted. Employee has been permanently Terminated and all accounts suspended in MySQL."
            ];
        }
        return ['success' => false, 'message' => "Database error: " . $e->getMessage()];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Enroll existing active employee into onboarding pipeline
 */
function hr_enrollOnboarding($empId) {
    $pdo = getDbConnection();
    try {
        $check = $pdo->prepare("SELECT COUNT(*) FROM employee_onboarding WHERE emp_id = ?");
        $check->execute([$empId]);
        if ((int)$check->fetchColumn() > 0) {
            return ['success' => false, 'message' => "Employee {$empId} is already in the onboarding pipeline."];
        }

        $onboardingSteps = [
            ['RecordCreated', 'Completed'],
            ['AccessRequested', 'In Progress'],
            ['IntranetGranted', 'Pending'],
            ['SystemAccessGranted', 'Pending'],
            ['DocumentsFiled', 'Pending'],
            ['GovernanceReviewed', 'Pending']
        ];
        $stmt = $pdo->prepare("INSERT INTO employee_onboarding (emp_id, step, status, completed_at) VALUES (?, ?, ?, NOW())");
        foreach ($onboardingSteps as $step) {
            $stmt->execute([$empId, $step[0], $step[1]]);
        }
        return ['success' => true, 'message' => "Employee {$empId} enrolled into onboarding pipeline."];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Fetch Terminated / Suspended employees for offboarding archive
 */
function hr_getTerminatedEmployees() {
    $pdo = getDbConnection();
    $stmt = $pdo->query("
        SELECT 
            e.emp_id,
            e.full_name,
            e.job_title,
            e.department_code,
            d.dept_name,
            e.clearance_level,
            e.employment_status,
            ea.status AS account_status,
            e.hire_date
        FROM employees e
        LEFT JOIN departments d ON e.department_code = d.dept_code
        LEFT JOIN employee_accounts ea ON e.emp_id = ea.emp_id
        WHERE e.employment_status IN ('Terminated', 'Suspended')
        GROUP BY e.emp_id
        ORDER BY e.emp_id DESC
    ");
    return $stmt->fetchAll();
}

/**
 * Fetch active employees who are not yet in onboarding pipeline
 */
function hr_getUnonboardedEmployees() {
    $pdo = getDbConnection();
    $stmt = $pdo->query("
        SELECT 
            e.emp_id,
            e.full_name,
            e.job_title,
            e.department_code,
            d.dept_name,
            e.clearance_level
        FROM employees e
        LEFT JOIN departments d ON e.department_code = d.dept_code
        WHERE e.emp_id NOT IN (SELECT DISTINCT emp_id FROM employee_onboarding)
          AND e.employment_status = 'Active'
        ORDER BY e.emp_id DESC
    ");
    return $stmt->fetchAll();
}

