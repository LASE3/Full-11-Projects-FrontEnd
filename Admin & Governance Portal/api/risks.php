<?php

/**
 * VOSTOKPRIBOR System 11 // GOV-CORE
 * Board Risk Register REST API
 * Handles Full CRUD operations on `risk_register` table.
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
    // 1. GET: Fetch Risks
    // -------------------------------------------------------------------------
    if ($method === 'GET') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $stmt = $pdo->prepare("
                SELECT rr.*, e.full_name AS owner_name, e.job_title AS owner_title
                FROM risk_register rr
                LEFT JOIN employees e ON rr.owner_emp_id = e.emp_id
                WHERE rr.risk_id = ?
            ");
            $stmt->execute([$id]);
            $risk = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$risk) {
                jsonResponse(['success' => false, 'error' => 'Risk filing not found'], 404);
            }
            jsonResponse(['success' => true, 'risk' => $risk]);
        }

        $search = trim($_GET['search'] ?? '');
        $impact = trim($_GET['impact'] ?? '');
        $status = trim($_GET['status'] ?? '');

        $sql = "
            SELECT rr.*, e.full_name AS owner_name, e.job_title AS owner_title
            FROM risk_register rr
            LEFT JOIN employees e ON rr.owner_emp_id = e.emp_id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (rr.description LIKE ? OR rr.system_target LIKE ? OR rr.owner_emp_id LIKE ? OR e.full_name LIKE ?)";
            $wildcard = "%$search%";
            $params = array_merge($params, [$wildcard, $wildcard, $wildcard, $wildcard]);
        }
        if (!empty($impact) && $impact !== 'ALL') {
            $sql .= " AND rr.impact = ?";
            $params[] = $impact;
        }
        if (!empty($status) && $status !== 'ALL') {
            $sql .= " AND rr.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY CASE WHEN rr.impact = 'Critical' THEN 1 WHEN rr.impact = 'High' THEN 2 ELSE 3 END, rr.risk_id ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $risks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        jsonResponse([
            'success' => true,
            'count' => count($risks),
            'risks' => $risks
        ]);
    }

    // -------------------------------------------------------------------------
    // 2. POST: Create, Update, Delete, Update Status
    // -------------------------------------------------------------------------
    if ($method === 'POST') {
        $action = trim($input['action'] ?? 'create');

        // CREATE
        if ($action === 'create') {
            $desc = trim($input['description'] ?? '');
            $title = trim($input['title'] ?? '');
            if (empty($title) && !empty($desc)) {
                $title = mb_substr($desc, 0, 80);
            }
            if (empty($desc) && !empty($title)) {
                $desc = $title;
            }
            $likelihood = trim($input['likelihood'] ?? 'Moderate');
            $impact = trim($input['impact'] ?? 'High');
            $ownerEmpId = trim($input['owner_emp_id'] ?? getCurrentGovActor());
            $status = trim($input['status'] ?? 'UnderReview');
            $reviewDate = trim($input['review_date'] ?? date('Y-m-d', strtotime('+30 days')));
            $systemTarget = trim($input['system_target'] ?? 'SYS-01 Production Enclave');
            $threatVector = trim($input['threat_vector'] ?? '');

            if (empty($desc)) {
                jsonResponse(['success' => false, 'error' => 'Risk description is mandatory.'], 400);
            }

            $stmt = $pdo->prepare("
                INSERT INTO risk_register 
                (title, description, likelihood, impact, owner_emp_id, status, review_date, system_target, threat_vector)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$title, $desc, $likelihood, $impact, $ownerEmpId, $status, $reviewDate, $systemTarget, $threatVector]);
            $newId = (int)$pdo->lastInsertId();
            $riskCode = 'RR-2026-' . str_pad($newId, 3, '0', STR_PAD_LEFT);

            recordAuditEntry($pdo, 'CREATE_BOARD_RISK_FILING', 'risk_register', (string)$newId, 'SUCCESS', [
                'risk_code' => $riskCode,
                'title' => $title,
                'impact' => $impact,
                'likelihood' => $likelihood,
                'owner' => $ownerEmpId,
                'system_target' => $systemTarget
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Risk filing [{$riskCode}] officially registered in Board Risk Register.",
                'risk_id' => $newId,
                'risk_code' => $riskCode
            ], 201);
        }

        // UPDATE
        if ($action === 'update') {
            $riskId = (int)($input['risk_id'] ?? 0);
            if ($riskId <= 0) {
                jsonResponse(['success' => false, 'error' => 'Valid risk_id required.'], 400);
            }

            $stmt = $pdo->prepare("SELECT * FROM risk_register WHERE risk_id = ?");
            $stmt->execute([$riskId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$existing) {
                jsonResponse(['success' => false, 'error' => 'Risk record not found.'], 404);
            }

            $desc = trim($input['description'] ?? $existing['description']);
            $title = trim($input['title'] ?? ($existing['title'] ?? ''));
            if (empty($title) && !empty($desc)) {
                $title = mb_substr($desc, 0, 80);
            }
            $likelihood = trim($input['likelihood'] ?? $existing['likelihood']);
            $impact = trim($input['impact'] ?? $existing['impact']);
            $ownerEmpId = trim($input['owner_emp_id'] ?? $existing['owner_emp_id']);
            $status = trim($input['status'] ?? $existing['status']);
            $reviewDate = trim($input['review_date'] ?? $existing['review_date']);
            $systemTarget = trim($input['system_target'] ?? ($existing['system_target'] ?? 'SYS-01 Production Enclave'));
            $threatVector = trim($input['threat_vector'] ?? ($existing['threat_vector'] ?? ''));

            $upd = $pdo->prepare("
                UPDATE risk_register 
                SET title = ?, description = ?, likelihood = ?, impact = ?, owner_emp_id = ?, status = ?, 
                    review_date = ?, system_target = ?, threat_vector = ?
                WHERE risk_id = ?
            ");
            $upd->execute([$title, $desc, $likelihood, $impact, $ownerEmpId, $status, $reviewDate, $systemTarget, $threatVector, $riskId]);

            $riskCode = 'RR-2026-' . str_pad($riskId, 3, '0', STR_PAD_LEFT);
            recordAuditEntry($pdo, 'UPDATE_BOARD_RISK_FILING', 'risk_register', (string)$riskId, 'SUCCESS', [
                'risk_code' => $riskCode,
                'impact' => $impact,
                'status' => $status,
                'owner' => $ownerEmpId
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Risk filing [{$riskCode}] updated successfully.",
                'risk_id' => $riskId
            ]);
        }

        // UPDATE STATUS
        if ($action === 'update_status') {
            $riskId = (int)($input['risk_id'] ?? 0);
            $newStatus = trim($input['status'] ?? 'Mitigated');

            if ($riskId <= 0) {
                jsonResponse(['success' => false, 'error' => 'Valid risk_id required.'], 400);
            }

            $stmt = $pdo->prepare("UPDATE risk_register SET status = ? WHERE risk_id = ?");
            $stmt->execute([$newStatus, $riskId]);

            $riskCode = 'RR-2026-' . str_pad($riskId, 3, '0', STR_PAD_LEFT);
            recordAuditEntry($pdo, 'UPDATE_RISK_STATUS', 'risk_register', (string)$riskId, 'SUCCESS', [
                'risk_code' => $riskCode,
                'new_status' => $newStatus
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Risk [{$riskCode}] stance updated to [{$newStatus}]."
            ]);
        }

        // DELETE
        if ($action === 'delete') {
            $riskId = (int)($input['risk_id'] ?? 0);
            if ($riskId <= 0) {
                jsonResponse(['success' => false, 'error' => 'Valid risk_id required.'], 400);
            }

            $stmt = $pdo->prepare("SELECT * FROM risk_register WHERE risk_id = ?");
            $stmt->execute([$riskId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$existing) {
                jsonResponse(['success' => false, 'error' => 'Risk record not found.'], 404);
            }

            $del = $pdo->prepare("DELETE FROM risk_register WHERE risk_id = ?");
            $del->execute([$riskId]);

            $riskCode = 'RR-2026-' . str_pad($riskId, 3, '0', STR_PAD_LEFT);
            recordAuditEntry($pdo, 'DELETE_BOARD_RISK', 'risk_register', (string)$riskId, 'SUCCESS', [
                'risk_code' => $riskCode,
                'description' => $existing['description']
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Risk filing [{$riskCode}] retired and removed from register."
            ]);
        }

        jsonResponse(['success' => false, 'error' => 'Unknown action.'], 400);
    }

    jsonResponse(['success' => false, 'error' => 'Unsupported method.'], 405);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
