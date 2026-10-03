<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR Centralized Integration Router & API Gateway
 * Manages communication between all 11 enterprise systems using the
 * locked matrix defined in system_integrations and dispatches via vp_emit().
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/integration_bus.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = getDbConnection();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// -----------------------------------------------------------------------------
// Authentication & Security Context
// -----------------------------------------------------------------------------
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$bearerToken = '';
if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
    $bearerToken = trim($matches[1]);
}

$currentUser = null;
if (!empty($_SESSION['vostok_user'])) {
    $currentUser = $_SESSION['vostok_user'];
} elseif (!empty($bearerToken)) {
    $tokenData = verifySsoCookie($bearerToken);
    if ($tokenData) {
        $currentUser = $tokenData;
    }
}

$userClearance = $currentUser['clearance_level'] ?? 'L1';
$actorId = $currentUser['emp_id'] ?? ($currentUser['user_id'] ?? 'SYSTEM');
$isSuperAdmin = isSuperAdmin($currentUser);

// -----------------------------------------------------------------------------
// GET: Fetch Integrations and Logs
// -----------------------------------------------------------------------------
if ($method === 'GET') {
    $system = trim($_GET['system'] ?? '');
    $linkCode = trim($_GET['link_code'] ?? '');
    $includeLogs = isset($_GET['include_logs']) ? (bool)$_GET['include_logs'] : true;

    try {
        if (!empty($linkCode)) {
            $stmt = $pdo->prepare("SELECT * FROM system_integrations WHERE link_code = ? LIMIT 1");
            $stmt->execute([$linkCode]);
            $integration = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$integration) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => "Integration link '{$linkCode}' not found in catalog"]);
                exit;
            }

            $logs = [];
            if ($includeLogs) {
                $logStmt = $pdo->prepare("SELECT * FROM system_integration_logs WHERE link_code = ? ORDER BY log_id DESC LIMIT 50");
                $logStmt->execute([$linkCode]);
                $logs = $logStmt->fetchAll(PDO::FETCH_ASSOC);
            }

            echo json_encode([
                'success' => true,
                'integration' => $integration,
                'logs' => $logs
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Filter by canonical system code (WEB, SHP, CUS, EMP, CRM, HR, FIN, IT, DOC, DEV, ADM)
        $whereSql = "1=1";
        $params = [];

        if (!empty($system)) {
            $sysCode = canonicalSystemCode($system);
            if ($sysCode === 'OPS') {
                $sysCode = 'EMP';
            }
            $whereSql = "(source_system_id = :s1 OR target_system_id = :s2)";
            $params[':s1'] = $sysCode;
            $params[':s2'] = $sysCode;
        }

        $stmt = $pdo->prepare("SELECT * FROM system_integrations WHERE {$whereSql} ORDER BY link_code ASC");
        $stmt->execute($params);
        $integrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $logs = [];
        if ($includeLogs) {
            $logWhere = "1=1";
            $logParams = [];
            if (!empty($system)) {
                $logWhere = "(source_system_id = :ls1 OR target_system_id = :ls2)";
                $logParams[':ls1'] = $sysCode;
                $logParams[':ls2'] = $sysCode;
            }
            $logStmt = $pdo->prepare("SELECT * FROM system_integration_logs WHERE {$logWhere} ORDER BY log_id DESC LIMIT 50");
            $logStmt->execute($logParams);
            $logs = $logStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // System matrix statistics
        $statsStmt = $pdo->query("
            SELECT 
                COUNT(*) as total_links,
                SUM(CASE WHEN status = 'Active' THEN 1 ELSE 0 END) as active_links,
                COUNT(DISTINCT source_system_id) as active_sources,
                COUNT(DISTINCT target_system_id) as active_targets
            FROM system_integrations
        ");
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'count' => count($integrations),
            'system_filter' => $system ? canonicalSystemCode($system) : 'ALL',
            'stats' => $stats,
            'integrations' => $integrations,
            'recent_logs' => $logs
        ], JSON_UNESCAPED_UNICODE);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// -----------------------------------------------------------------------------
// POST: Execute Inter-System Integration Event
// -----------------------------------------------------------------------------
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

    $linkCode = trim((string)($input['link_code'] ?? ''));
    $action   = trim((string)($input['action'] ?? ($input['event'] ?? 'SYNC_EVENT')));
    $payload  = (array)($input['payload'] ?? []);
    $customEndpoint = trim((string)($input['endpoint'] ?? ''));

    if (empty($linkCode)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing required parameter: link_code']);
        exit;
    }

    try {
        // 1. Fetch integration link definition from system_integrations table
        $stmt = $pdo->prepare("SELECT * FROM system_integrations WHERE link_code = ? LIMIT 1");
        $stmt->execute([$linkCode]);
        $link = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$link) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => "Integration link '{$linkCode}' is not registered in system_integrations matrix"]);
            exit;
        }

        // 2. Clearance hierarchy validation (L1 < L2 < L3 < L4)
        $clearanceRank = ['L1' => 1, 'L2' => 2, 'L3' => 3, 'L4' => 4];
        $requiredRank = $clearanceRank[$link['required_clearance']] ?? 2;
        $userRank = $clearanceRank[$userClearance] ?? 1;

        if (!$isSuperAdmin && $userRank < $requiredRank) {
            // Permission denied: emit security event and unauthorized log
            $emitPayload = array_merge($payload, [
                'severity'    => 'High',
                'governance'  => true,
                'description' => "UNAUTHORIZED INTEGRATION PROBE: User '{$actorId}' with clearance {$userClearance} attempted access requiring {$link['required_clearance']}"
            ]);
            vp_emit($pdo, $linkCode, $link['source_system_id'], $link['target_system_id'], 'UNAUTHORIZED_ACCESS_ATTEMPT', $emitPayload, $actorId, 403);

            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error' => "Access Denied: Requires clearance {$link['required_clearance']}. Current clearance: {$userClearance}",
                'link_code' => $linkCode
            ]);
            exit;
        }

        // 3. Prepare payload & summary
        $endpoint = $customEndpoint ?: "/api/integrations/{$linkCode}/dispatch";
        $summary = "Integration directive dispatched from {$link['source_system_id']} to {$link['target_system_id']}. Action: {$action}";
        if (!empty($payload['summary'])) {
            $summary = (string)$payload['summary'];
        } elseif (!empty($payload['description'])) {
            $summary = (string)$payload['description'];
        }

        $emitPayload = array_merge($payload, [
            'endpoint'     => $endpoint,
            'api_protocol' => $link['api_protocol'],
            'summary'      => $summary,
            'direction'    => $link['direction'],
            'entity_type'  => $payload['entity_type'] ?? 'system_integration',
            'entity_id'    => $payload['entity_id'] ?? $linkCode
        ]);

        // 4. Dispatch through centralized Universal Integration Event Bus (vp_emit)
        $statusCode = 200;
        $logId = vp_emit(
            $pdo,
            $linkCode,
            $link['source_system_id'],
            $link['target_system_id'],
            $action,
            $emitPayload,
            $actorId,
            $statusCode
        );

        echo json_encode([
            'success'        => true,
            'message'        => "Integration transaction executed and logged successfully via vp_emit()",
            'link_code'      => $linkCode,
            'log_id'         => $logId,
            'source'         => $link['source_system_id'],
            'target'         => $link['target_system_id'],
            'protocol'       => $link['api_protocol'],
            'authentication' => $link['authentication_method'],
            'direction'      => $link['direction'],
            'status_code'    => $statusCode,
            'actor_id'       => $actorId,
            'executed_at'    => date('Y-m-d H:i:s'),
            'data'           => ['status' => 'COMMITTED', 'action' => $action]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}
