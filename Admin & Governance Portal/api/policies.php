<?php

/**
 * VOSTOKPRIBOR System 11 // GOV-CORE
 * Enterprise Security Policies REST API
 * Handles Full CRUD operations on `security_policies` table.
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
    // 1. GET: Fetch Policies
    // -------------------------------------------------------------------------
    if ($method === 'GET') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $stmt = $pdo->prepare("SELECT * FROM security_policies WHERE policy_id = ?");
            $stmt->execute([$id]);
            $policy = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$policy) {
                jsonResponse(['success' => false, 'error' => 'Policy not found'], 404);
            }
            jsonResponse(['success' => true, 'policy' => $policy]);
        }

        $search = trim($_GET['search'] ?? '');
        $severity = trim($_GET['severity'] ?? '');

        $sql = "SELECT * FROM security_policies WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (title LIKE ? OR doc_id LIKE ? OR description LIKE ? OR system_id LIKE ?)";
            $wildcard = "%$search%";
            $params = array_merge($params, [$wildcard, $wildcard, $wildcard, $wildcard]);
        }
        if (!empty($severity) && $severity !== 'ALL') {
            $sql .= " AND severity = ?";
            $params[] = $severity;
        }

        $sql .= " ORDER BY policy_id ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $policies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        jsonResponse([
            'success' => true,
            'count' => count($policies),
            'policies' => $policies
        ]);
    }

    // -------------------------------------------------------------------------
    // 2. POST: Create, Update, Delete
    // -------------------------------------------------------------------------
    if ($method === 'POST') {
        $action = trim($input['action'] ?? 'create');

        // CREATE
        if ($action === 'create') {
            $docId = trim($input['doc_id'] ?? '');
            $title = trim($input['title'] ?? '');
            $effectiveDate = trim($input['effective_date'] ?? date('Y-m-d'));
            $severity = trim($input['severity'] ?? 'High');
            $enforcementMode = trim($input['enforcement_mode'] ?? 'MANDATORY');
            $description = trim($input['description'] ?? '');
            $systemId = trim($input['system_id'] ?? 'SYS-01..11');

            if (empty($title)) {
                jsonResponse(['success' => false, 'error' => 'Policy title is required.'], 400);
            }

            if (empty($docId)) {
                $maxId = (int)$pdo->query("SELECT MAX(policy_id) FROM security_policies")->fetchColumn();
                $docId = 'DOC-2026-' . str_pad($maxId + 1, 3, '0', STR_PAD_LEFT);
            }

            // Ensure document exists in documents table to satisfy foreign key constraint
            $chkDoc = $pdo->prepare("SELECT doc_id FROM documents WHERE doc_id = ?");
            $chkDoc->execute([$docId]);
            if (!$chkDoc->fetch()) {
                $insDoc = $pdo->prepare("
                    INSERT INTO documents 
                    (doc_id, file_name, description, classification, folder, department, file_size, status, retention_period, owning_system, owner_emp_id)
                    VALUES (?, ?, ?, 'Confidential', 'policies', 'EXE', '1.2 MB', 'Approved', '7y', 'SYS-11 GOV-CORE', ?)
                ");
                $insDoc->execute([
                    $docId,
                    preg_replace('/[^a-zA-Z0-9_-]/', '_', $title) . '.pdf',
                    $description ?: 'Statutory Security Policy Document',
                    getCurrentGovActor()
                ]);
            }

            $stmt = $pdo->prepare("
                INSERT INTO security_policies 
                (doc_id, title, effective_date, severity, enforcement_mode, description, system_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$docId, $title, $effectiveDate, $severity, $enforcementMode, $description, $systemId]);
            $newId = (int)$pdo->lastInsertId();

            recordAuditEntry($pdo, 'CREATE_SECURITY_POLICY', 'security_policies', $docId, 'SUCCESS', [
                'policy_id' => $newId,
                'doc_id' => $docId,
                'title' => $title,
                'severity' => $severity,
                'enforcement_mode' => $enforcementMode
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Statutory policy [{$docId}] created successfully.",
                'policy_id' => $newId,
                'doc_id' => $docId
            ], 201);
        }

        // UPDATE
        if ($action === 'update') {
            $policyId = (int)($input['policy_id'] ?? 0);
            if ($policyId <= 0) {
                jsonResponse(['success' => false, 'error' => 'Valid policy_id is required for update.'], 400);
            }

            $stmt = $pdo->prepare("SELECT * FROM security_policies WHERE policy_id = ?");
            $stmt->execute([$policyId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$existing) {
                jsonResponse(['success' => false, 'error' => 'Policy not found.'], 404);
            }

            $docId = trim($input['doc_id'] ?? $existing['doc_id']);
            $title = trim($input['title'] ?? $existing['title']);
            $effectiveDate = trim($input['effective_date'] ?? $existing['effective_date']);
            $severity = trim($input['severity'] ?? $existing['severity']);
            $enforcementMode = trim($input['enforcement_mode'] ?? $existing['enforcement_mode']);
            $description = trim($input['description'] ?? $existing['description']);
            $systemId = trim($input['system_id'] ?? $existing['system_id']);

            $upd = $pdo->prepare("
                UPDATE security_policies 
                SET doc_id = ?, title = ?, effective_date = ?, severity = ?, enforcement_mode = ?, description = ?, system_id = ?
                WHERE policy_id = ?
            ");
            $upd->execute([$docId, $title, $effectiveDate, $severity, $enforcementMode, $description, $systemId, $policyId]);

            recordAuditEntry($pdo, 'UPDATE_SECURITY_POLICY', 'security_policies', $docId, 'SUCCESS', [
                'policy_id' => $policyId,
                'doc_id' => $docId,
                'title' => $title,
                'severity' => $severity,
                'enforcement_mode' => $enforcementMode
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Policy [{$docId}] updated successfully.",
                'policy_id' => $policyId
            ]);
        }

        // DELETE
        if ($action === 'delete') {
            $policyId = (int)($input['policy_id'] ?? 0);
            if ($policyId <= 0) {
                jsonResponse(['success' => false, 'error' => 'Valid policy_id is required for deletion.'], 400);
            }

            $stmt = $pdo->prepare("SELECT * FROM security_policies WHERE policy_id = ?");
            $stmt->execute([$policyId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$existing) {
                jsonResponse(['success' => false, 'error' => 'Policy not found.'], 404);
            }

            $del = $pdo->prepare("DELETE FROM security_policies WHERE policy_id = ?");
            $del->execute([$policyId]);

            recordAuditEntry($pdo, 'DELETE_SECURITY_POLICY', 'security_policies', $existing['doc_id'], 'SUCCESS', [
                'deleted_policy_id' => $policyId,
                'doc_id' => $existing['doc_id'],
                'title' => $existing['title']
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Policy [{$existing['doc_id']}] revoked and purged from statutory register."
            ]);
        }

        jsonResponse(['success' => false, 'error' => 'Unknown action.'], 400);
    }

    jsonResponse(['success' => false, 'error' => 'Unsupported method.'], 405);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
