<?php

/**
 * VOSTOKPRIBOR System 11 // GOV-CORE
 * Privileged Accounts & Access Matrix REST API
 * Handles privilege elevations, credential revocation, session termination, and attestation.
 */

require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('ADM', ['min_clearance' => 'L3']);

require_once __DIR__ . '/db_helper.php';

try {
    $pdo = getDbConnection();
    gov_ensureSchemaReady($pdo);

    $method = $_SERVER['REQUEST_METHOD'];
    $input = getRequestInput();

    // -------------------------------------------------------------------------
    // 1. GET: Fetch Privileged Accounts / Access Matrix
    // -------------------------------------------------------------------------
    if ($method === 'GET') {
        $view = $_GET['view'] ?? 'privileged';

        if ($view === 'privileged') {
            $sql = "
                SELECT 
                    e.emp_id, e.full_name, e.job_title, e.clearance_level, e.department_code, e.email,
                    d.dept_name,
                    ea.account_id, ea.username, ea.status AS account_status, ea.mfa_enabled, ea.last_login,
                    r.role_name,
                    us.session_id, us.started_at AS session_started
                FROM employees e
                JOIN employee_accounts ea ON e.emp_id = ea.emp_id
                LEFT JOIN departments d ON e.department_code = d.dept_code
                LEFT JOIN employee_roles er ON e.emp_id = er.emp_id
                LEFT JOIN roles r ON er.role_id = r.role_id
                LEFT JOIN user_sessions us ON ea.account_id = us.employee_account_id AND us.status = 'Active'
                WHERE (e.clearance_level IN ('L3', 'L4') OR r.role_name LIKE '%Admin%' OR r.role_name LIKE '%Officer%')
                  AND e.emp_id != 'EMP-0001'
                GROUP BY ea.account_id
                ORDER BY CASE WHEN e.clearance_level = 'L4' THEN 1 ELSE 2 END, e.emp_id ASC
            ";
            $accounts = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            jsonResponse(['success' => true, 'count' => count($accounts), 'accounts' => $accounts]);
        }

        if ($view === 'roles') {
            $roles = $pdo->query("
                SELECT r.*, COUNT(er.emp_id) AS assignee_count
                FROM roles r
                LEFT JOIN employee_roles er ON r.role_id = er.role_id
                GROUP BY r.role_id
                ORDER BY r.role_id ASC
            ")->fetchAll(PDO::FETCH_ASSOC);
            jsonResponse(['success' => true, 'count' => count($roles), 'roles' => $roles]);
        }

        jsonResponse(['success' => false, 'error' => 'Invalid view requested.'], 400);
    }

    // -------------------------------------------------------------------------
    // 2. POST: Actions
    // -------------------------------------------------------------------------
    if ($method === 'POST') {
        $action = trim($input['action'] ?? '');

        // SEVER CREDENTIALS / SUSPEND PRIVILEGED ACCOUNT
        if ($action === 'sever_credentials') {
            $empId = trim($input['emp_id'] ?? '');
            if (empty($empId)) {
                jsonResponse(['success' => false, 'error' => 'Employee ID is required.'], 400);
            }

            // Suspend account
            $stmt = $pdo->prepare("UPDATE employee_accounts SET status = 'Suspended' WHERE emp_id = ?");
            $stmt->execute([$empId]);

            // Terminate active sessions
            $stmtSess = $pdo->prepare("
                UPDATE user_sessions us
                JOIN employee_accounts ea ON us.employee_account_id = ea.account_id
                SET us.status = 'Terminated'
                WHERE ea.emp_id = ? AND us.status = 'Active'
            ");
            $stmtSess->execute([$empId]);

            // Add or update access_reviews breach finding
            $revStmt = $pdo->prepare("
                INSERT INTO access_reviews (emp_id, reviewed_by_emp_id, review_date, finding, action_taken)
                VALUES (?, ?, CURDATE(), 'Interlock Enforcement: Credentials Severed Across SYS 01-11 Nodes', 'Orphan Account Isolated - Credentials Revoked')
            ");
            $revStmt->execute([$empId, getCurrentGovActor()]);

            recordAuditEntry($pdo, 'SEVER_CREDENTIALS_INTERLOCK', 'employee_accounts', $empId, 'SUCCESS', [
                'emp_id' => $empId,
                'action' => 'SUSPEND_AND_TERMINATE_SESSIONS',
                'enforced_by' => getCurrentGovActor()
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Credentials for [{$empId}] severed and invalidated across SYS-01 through SYS-11."
            ]);
        }

        // RESTORE CREDENTIALS
        if ($action === 'restore_credentials') {
            $empId = trim($input['emp_id'] ?? '');
            if (empty($empId)) {
                jsonResponse(['success' => false, 'error' => 'Employee ID is required.'], 400);
            }

            $stmt = $pdo->prepare("UPDATE employee_accounts SET status = 'Active' WHERE emp_id = ?");
            $stmt->execute([$empId]);

            $revStmt = $pdo->prepare("
                INSERT INTO access_reviews (emp_id, reviewed_by_emp_id, review_date, finding, action_taken)
                VALUES (?, ?, CURDATE(), 'Executive Clearance Reinstatement', 'Attested - Credentials Restored')
            ");
            $revStmt->execute([$empId, getCurrentGovActor()]);

            recordAuditEntry($pdo, 'RESTORE_CREDENTIALS', 'employee_accounts', $empId, 'SUCCESS', [
                'emp_id' => $empId,
                'status' => 'Active'
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Credentials for [{$empId}] restored to Active status."
            ]);
        }

        // TERMINATE SESSION (EPHEMERAL BASTION LEASE)
        if ($action === 'terminate_session') {
            $sessionId = trim($input['session_id'] ?? '');
            $empId = trim($input['emp_id'] ?? '');

            if (!empty($sessionId)) {
                $stmt = $pdo->prepare("UPDATE user_sessions SET status = 'Terminated' WHERE session_id = ?");
                $stmt->execute([$sessionId]);
            } elseif (!empty($empId)) {
                $stmt = $pdo->prepare("
                    UPDATE user_sessions us
                    JOIN employee_accounts ea ON us.employee_account_id = ea.account_id
                    SET us.status = 'Terminated'
                    WHERE ea.emp_id = ? AND us.status = 'Active'
                ");
                $stmt->execute([$empId]);
            }

            recordAuditEntry($pdo, 'TERMINATE_BASTION_SESSION', 'user_sessions', $sessionId ?: $empId, 'SUCCESS', [
                'session_id' => $sessionId,
                'target_emp' => $empId,
                'severed_by' => getCurrentGovActor()
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Ephemeral bastion session severed and credentials invalidated."
            ]);
        }

        // PROMOTE / ELEVATE CLEARANCE
        if ($action === 'promote_account') {
            $empId = trim($input['emp_id'] ?? '');
            $newClearance = trim($input['clearance_level'] ?? 'L3');
            $roleId = isset($input['role_id']) ? (int)$input['role_id'] : 0;

            if (empty($empId)) {
                jsonResponse(['success' => false, 'error' => 'Employee ID required.'], 400);
            }

            $stmt = $pdo->prepare("UPDATE employees SET clearance_level = ? WHERE emp_id = ?");
            $stmt->execute([$newClearance, $empId]);

            if ($roleId > 0) {
                $pdo->prepare("DELETE FROM employee_roles WHERE emp_id = ?")->execute([$empId]);
                $pdo->prepare("INSERT INTO employee_roles (emp_id, role_id) VALUES (?, ?)")->execute([$empId, $roleId]);
            }

            recordAuditEntry($pdo, 'ELEVATE_CLEARANCE_LEVEL', 'employees', $empId, 'SUCCESS', [
                'new_clearance' => $newClearance,
                'role_id' => $roleId
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Employee [{$empId}] elevated to clearance [{$newClearance}]."
            ]);
        }

        // ATTEST ROLE
        if ($action === 'attest_role') {
            $empId = trim($input['emp_id'] ?? '');
            if (empty($empId)) {
                jsonResponse(['success' => false, 'error' => 'Employee ID required.'], 400);
            }

            // Check if existing review exists
            $stmtCheck = $pdo->prepare("SELECT review_id FROM access_reviews WHERE emp_id = ? ORDER BY review_id DESC LIMIT 1");
            $stmtCheck->execute([$empId]);
            $existingId = $stmtCheck->fetchColumn();

            if ($existingId) {
                $upd = $pdo->prepare("
                    UPDATE access_reviews 
                    SET reviewed_by_emp_id = ?, review_date = CURDATE(), action_taken = 'Attested - Role Validated'
                    WHERE review_id = ?
                ");
                $upd->execute([getCurrentGovActor(), $existingId]);
            } else {
                $ins = $pdo->prepare("
                    INSERT INTO access_reviews (emp_id, reviewed_by_emp_id, review_date, finding, action_taken)
                    VALUES (?, ?, CURDATE(), 'Autonomous Role Entitlement Verification', 'Attested - Role Validated')
                ");
                $ins->execute([$empId, getCurrentGovActor()]);
            }

            recordAuditEntry($pdo, 'ATTEST_ROLE_SIGN_OFF', 'access_reviews', $empId, 'SUCCESS', [
                'emp_id' => $empId,
                'signer' => getCurrentGovActor()
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Role entitlement for [{$empId}] verified and signed off."
            ]);
        }

        // PURGE ORPHAN TOKEN
        if ($action === 'purge_orphan') {
            $empId = trim($input['emp_id'] ?? '');
            if (empty($empId)) {
                jsonResponse(['success' => false, 'error' => 'Employee ID required.'], 400);
            }

            // Suspend employee account and terminate sessions
            $pdo->prepare("UPDATE employee_accounts SET status = 'Suspended' WHERE emp_id = ?")->execute([$empId]);
            $pdo->prepare("
                UPDATE user_sessions us
                JOIN employee_accounts ea ON us.employee_account_id = ea.account_id
                SET us.status = 'Terminated'
                WHERE ea.emp_id = ?
            ")->execute([$empId]);

            // Delete unauthorized role binds
            $pdo->prepare("DELETE FROM employee_roles WHERE emp_id = ?")->execute([$empId]);

            // Update review
            $pdo->prepare("
                INSERT INTO access_reviews (emp_id, reviewed_by_emp_id, review_date, finding, action_taken)
                VALUES (?, ?, CURDATE(), 'Unlawful Token Flagged: Purged by Master Signer', 'Token Purged - Binds Revoked')
            ")->execute([$empId, getCurrentGovActor()]);

            recordAuditEntry($pdo, 'PURGE_ORPHAN_TOKEN', 'employee_roles', $empId, 'SUCCESS', [
                'emp_id' => $empId,
                'purged_by' => getCurrentGovActor()
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Orphaned credentials and role bindings for [{$empId}] permanently purged."
            ]);
        }

        jsonResponse(['success' => false, 'error' => 'Unknown action.'], 400);
    }

    jsonResponse(['success' => false, 'error' => 'Unsupported method.'], 405);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
