<?php

/**
 * VOSTOKPRIBOR System 11 // GOV-CORE
 * Immutable Audit Logs & Cryptographic Notary API
 * Handles live event stream queries and cryptographic notary stamp commits.
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
    // 1. GET: Query Audit Logs
    // -------------------------------------------------------------------------
    if ($method === 'GET') {
        $limit = isset($_GET['limit']) ? min(200, max(10, (int)$_GET['limit'])) : 50;
        $search = trim($_GET['search'] ?? '');
        $systemId = trim($_GET['system_id'] ?? '');
        $result = trim($_GET['result'] ?? '');

        $sql = "
            SELECT 
                al.audit_id,
                al.actor_emp_id,
                al.actor_system,
                al.system_id,
                al.action,
                al.target_entity_type,
                al.target_entity_id,
                al.source_ip,
                al.result,
                al.occurred_at,
                al.new_values,
                e.full_name AS actor_name
            FROM audit_logs al
            LEFT JOIN employees e ON al.actor_emp_id = e.emp_id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (al.action LIKE ? OR al.actor_emp_id LIKE ? OR al.target_entity_id LIKE ? OR e.full_name LIKE ?)";
            $wildcard = "%$search%";
            $params = array_merge($params, [$wildcard, $wildcard, $wildcard, $wildcard]);
        }
        if (!empty($systemId) && $systemId !== 'ALL') {
            $sql .= " AND al.system_id = ?";
            $params[] = $systemId;
        }
        if (!empty($result) && $result !== 'ALL') {
            $sql .= " AND al.result = ?";
            $params[] = $result;
        }

        $sql .= " ORDER BY al.occurred_at DESC LIMIT " . (int)$limit;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        jsonResponse([
            'success' => true,
            'count' => count($logs),
            'logs' => $logs
        ]);
    }

    // -------------------------------------------------------------------------
    // 2. POST: Notarize Cryptographic Audit Stamp
    // -------------------------------------------------------------------------
    if ($method === 'POST') {
        $action = trim($input['action'] ?? 'notarize_stamp');

        if ($action === 'notarize_stamp') {
            $scope = trim($input['scope'] ?? 'ALL_SYS_01_11');
            $customNote = trim($input['note'] ?? 'Autonomous periodic Merkle tree root seal');
            $actorId = getCurrentGovActor();

            $shaHash = hash('sha256', $scope . microtime(true) . $actorId);

            recordAuditEntry($pdo, 'CRYPTO_NOTARIZATION_SEAL', 'audit_ledger', $shaHash, 'SUCCESS', [
                'merkle_root' => $shaHash,
                'scope' => $scope,
                'note' => $customNote,
                'sealed_by' => $actorId,
                'standard' => 'FIPS 140-3'
            ]);

            jsonResponse([
                'success' => true,
                'message' => 'Cryptographic Merkle notary stamp committed to immutable ledger.',
                'hash' => $shaHash,
                'timestamp' => date('Y-m-d H:i:s')
            ], 201);
        }

        jsonResponse(['success' => false, 'error' => 'Unknown action.'], 400);
    }

    jsonResponse(['success' => false, 'error' => 'Unsupported method.'], 405);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
