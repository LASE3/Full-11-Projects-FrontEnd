<?php

/**
 * VOSTOKPRIBOR System 11 // GOV-CORE
 * Statutory Compliance Oversight REST API
 * Handles Full CRUD on `compliance_controls`, `access_reviews`, and `security_incidents`.
 */

require_once __DIR__ . '/db_helper.php';

try {
    $pdo = getDbConnection();
    gov_ensureSchemaReady($pdo);

    $method = $_SERVER['REQUEST_METHOD'];
    $input = getRequestInput();

    // -------------------------------------------------------------------------
    // 1. GET: Fetch Controls, Reviews, or Incidents
    // -------------------------------------------------------------------------
    if ($method === 'GET') {
        $type = $_GET['type'] ?? 'controls';

        if ($type === 'controls') {
            $stmt = $pdo->query("
                SELECT cc.*, e.full_name AS custodian_name, e.job_title AS custodian_title
                FROM compliance_controls cc
                LEFT JOIN employees e ON cc.custodian_emp_id = e.emp_id
                ORDER BY cc.control_id ASC
            ");
            $controls = $stmt->fetchAll(PDO::FETCH_ASSOC);
            jsonResponse(['success' => true, 'count' => count($controls), 'controls' => $controls]);
        }

        if ($type === 'reviews') {
            $stmt = $pdo->query("
                SELECT ar.*, e.full_name AS emp_name, e.job_title, e.department_code, rev.full_name AS reviewer_name
                FROM access_reviews ar
                LEFT JOIN employees e ON ar.emp_id = e.emp_id
                LEFT JOIN employees rev ON ar.reviewed_by_emp_id = rev.emp_id
                ORDER BY ar.review_id DESC
            ");
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
            jsonResponse(['success' => true, 'count' => count($reviews), 'reviews' => $reviews]);
        }

        if ($type === 'incidents') {
            $stmt = $pdo->query("
                SELECT si.*, e.full_name AS lead_name
                FROM security_incidents si
                LEFT JOIN employees e ON si.assigned_to_emp_id = e.emp_id
                ORDER BY si.incident_id DESC
            ");
            $incidents = $stmt->fetchAll(PDO::FETCH_ASSOC);
            jsonResponse(['success' => true, 'count' => count($incidents), 'incidents' => $incidents]);
        }

        jsonResponse(['success' => false, 'error' => 'Invalid compliance resource type.'], 400);
    }

    // -------------------------------------------------------------------------
    // 2. POST: Actions
    // -------------------------------------------------------------------------
    if ($method === 'POST') {
        $action = trim($input['action'] ?? 'create_control');

        // CREATE CONTROL
        if ($action === 'create_control') {
            $code = trim($input['control_code'] ?? '');
            $title = trim($input['title'] ?? '');
            $desc = trim($input['description'] ?? '');
            $systemId = trim($input['system_id'] ?? 'SYS-01..10');
            $framework = trim($input['framework'] ?? 'KAZ-CERT DIR-44');
            $custodian = trim($input['custodian_emp_id'] ?? getCurrentGovActor());
            $status = trim($input['status'] ?? 'COMPLIANT');
            $evidenceRef = trim($input['evidence_ref'] ?? 'DOC-2026-015');
            $lastReviewed = trim($input['last_reviewed'] ?? date('Y-m-d'));

            if (empty($title)) {
                jsonResponse(['success' => false, 'error' => 'Control title is required.'], 400);
            }

            if (empty($code)) {
                $maxId = (int)$pdo->query("SELECT MAX(control_id) FROM compliance_controls")->fetchColumn();
                $code = 'CTRL-GOV-' . str_pad($maxId + 1, 2, '0', STR_PAD_LEFT);
            }

            $stmt = $pdo->prepare("
                INSERT INTO compliance_controls 
                (control_code, title, description, system_id, framework, custodian_emp_id, status, evidence_ref, last_reviewed)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$code, $title, $desc, $systemId, $framework, $custodian, $status, $evidenceRef, $lastReviewed]);
            $newId = (int)$pdo->lastInsertId();

            recordAuditEntry($pdo, 'CREATE_COMPLIANCE_CONTROL', 'compliance_controls', $code, 'SUCCESS', [
                'control_id' => $newId,
                'control_code' => $code,
                'title' => $title,
                'status' => $status
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Compliance Control [{$code}] registered successfully.",
                'control_id' => $newId,
                'control_code' => $code
            ], 201);
        }

        // UPDATE CONTROL
        if ($action === 'update_control') {
            $controlId = (int)($input['control_id'] ?? 0);
            if ($controlId <= 0) {
                jsonResponse(['success' => false, 'error' => 'Valid control_id required.'], 400);
            }

            $stmt = $pdo->prepare("SELECT * FROM compliance_controls WHERE control_id = ?");
            $stmt->execute([$controlId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$existing) {
                jsonResponse(['success' => false, 'error' => 'Control not found.'], 404);
            }

            $code = trim($input['control_code'] ?? $existing['control_code']);
            $title = trim($input['title'] ?? $existing['title']);
            $desc = trim($input['description'] ?? $existing['description']);
            $systemId = trim($input['system_id'] ?? $existing['system_id']);
            $framework = trim($input['framework'] ?? $existing['framework']);
            $custodian = trim($input['custodian_emp_id'] ?? $existing['custodian_emp_id']);
            $status = trim($input['status'] ?? $existing['status']);
            $evidenceRef = trim($input['evidence_ref'] ?? $existing['evidence_ref']);
            $lastReviewed = trim($input['last_reviewed'] ?? date('Y-m-d'));

            $upd = $pdo->prepare("
                UPDATE compliance_controls 
                SET control_code = ?, title = ?, description = ?, system_id = ?, framework = ?, 
                    custodian_emp_id = ?, status = ?, evidence_ref = ?, last_reviewed = ?
                WHERE control_id = ?
            ");
            $upd->execute([$code, $title, $desc, $systemId, $framework, $custodian, $status, $evidenceRef, $lastReviewed, $controlId]);

            recordAuditEntry($pdo, 'UPDATE_COMPLIANCE_CONTROL', 'compliance_controls', $code, 'SUCCESS', [
                'control_id' => $controlId,
                'control_code' => $code,
                'status' => $status
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Control [{$code}] updated successfully.",
                'control_id' => $controlId
            ]);
        }

        // UPDATE CONTROL STATUS
        if ($action === 'update_control_status') {
            $controlId = (int)($input['control_id'] ?? 0);
            $newStatus = trim($input['status'] ?? 'COMPLIANT');

            if ($controlId <= 0) {
                jsonResponse(['success' => false, 'error' => 'Valid control_id required.'], 400);
            }

            $stmt = $pdo->prepare("UPDATE compliance_controls SET status = ?, last_reviewed = CURDATE() WHERE control_id = ?");
            $stmt->execute([$newStatus, $controlId]);

            recordAuditEntry($pdo, 'UPDATE_CONTROL_STATUS', 'compliance_controls', (string)$controlId, 'SUCCESS', [
                'new_status' => $newStatus
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Control compliance posture updated to [{$newStatus}]."
            ]);
        }

        // DELETE CONTROL
        if ($action === 'delete_control') {
            $controlId = (int)($input['control_id'] ?? 0);
            if ($controlId <= 0) {
                jsonResponse(['success' => false, 'error' => 'Valid control_id required.'], 400);
            }

            $stmt = $pdo->prepare("SELECT * FROM compliance_controls WHERE control_id = ?");
            $stmt->execute([$controlId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$existing) {
                jsonResponse(['success' => false, 'error' => 'Control not found.'], 404);
            }

            $del = $pdo->prepare("DELETE FROM compliance_controls WHERE control_id = ?");
            $del->execute([$controlId]);

            recordAuditEntry($pdo, 'DELETE_COMPLIANCE_CONTROL', 'compliance_controls', $existing['control_code'], 'SUCCESS', [
                'deleted_id' => $controlId,
                'code' => $existing['control_code']
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Compliance Control [{$existing['control_code']}] removed."
            ]);
        }

        // CREATE ACCESS REVIEW / SIGN-OFF
        if ($action === 'create_review') {
            $empId = trim($input['emp_id'] ?? '');
            $finding = trim($input['finding'] ?? 'Annual Access Attestation Verified');
            $actionTaken = trim($input['action_taken'] ?? 'Attested - Validated');
            $reviewer = getCurrentGovActor();

            if (empty($empId)) {
                jsonResponse(['success' => false, 'error' => 'Employee ID is required.'], 400);
            }

            $stmt = $pdo->prepare("
                INSERT INTO access_reviews (emp_id, reviewed_by_emp_id, review_date, finding, action_taken)
                VALUES (?, ?, CURDATE(), ?, ?)
            ");
            $stmt->execute([$empId, $reviewer, $finding, $actionTaken]);
            $newId = (int)$pdo->lastInsertId();

            recordAuditEntry($pdo, 'SUBMIT_ACCESS_REVIEW', 'access_reviews', (string)$newId, 'SUCCESS', [
                'emp_id' => $empId,
                'reviewer' => $reviewer,
                'action_taken' => $actionTaken
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Access review logged for [{$empId}].",
                'review_id' => $newId
            ], 201);
        }

        // CREATE INCIDENT
        if ($action === 'create_incident') {
            $title = trim($input['title'] ?? '');
            $desc = trim($input['description'] ?? '');
            $severity = trim($input['severity'] ?? 'Medium');
            $status = trim($input['status'] ?? 'Open');
            $assignedTo = trim($input['assigned_to_emp_id'] ?? 'EMP-1004');
            $reporter = getCurrentGovActor();

            if (empty($title)) {
                jsonResponse(['success' => false, 'error' => 'Incident title is required.'], 400);
            }

            $stmt = $pdo->prepare("
                INSERT INTO security_incidents 
                (title, description, severity, status, reported_by_emp_id, assigned_to_emp_id, opened_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$title, $desc, $severity, $status, $reporter, $assignedTo]);
            $newId = (int)$pdo->lastInsertId();

            recordAuditEntry($pdo, 'CREATE_SECURITY_INCIDENT', 'security_incidents', (string)$newId, 'SUCCESS', [
                'title' => $title,
                'severity' => $severity,
                'status' => $status
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Security incident [#{$newId}] filed and dispatched to SecOps.",
                'incident_id' => $newId
            ], 201);
        }

        jsonResponse(['success' => false, 'error' => 'Unknown action.'], 400);
    }

    jsonResponse(['success' => false, 'error' => 'Unsupported method.'], 405);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
