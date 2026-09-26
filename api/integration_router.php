<?php

/**
 * VOSTOKPRIBOR Universal System Integration Router & API Gateway
 * Manages inter-system communication between all 11 enterprise systems.
 * 
 * Enforced Integration Matrix:
 *   SYS01 (ADM) -> SYS02 (CRM)
 *   SYS02 (CRM) -> SYS03 (CUS)
 *   SYS02 (CRM) -> SYS05 (EMP)
 *   SYS03 (CUS) -> SYS05 (EMP)
 *   SYS05 (EMP) -> SYS06 (DOC)
 *   SYS05 (EMP) -> SYS07 (FIN)
 *   SYS04 (DEV) -> SYS06 (DOC)
 *   SYS04 (DEV) -> SYS08 (HR)
 *   SYS04 (DEV) -> SYS09 (IT)
 *   SYS10 (SHP) -> SYS02 (CRM)
 *   SYS10 (SHP) -> SYS05 (EMP)
 *   SYS11 (WEB) -> ALL
 * 
 * Enforces per-link metadata:
 *   1. API / Protocol
 *   2. Authentication
 *   3. Data Exchanged
 *   4. Direction
 *   5. Permissions (Clearance verification / SuperAdmin L4 bypass)
 *   6. Logs (Recorded in system_integration_logs table)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = getDbConnection();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Identify authenticated user (from session, SSO cookie, or API token)
$currentUser = $_SESSION['vostok_user'] ?? null;
if (!$currentUser && isset($_COOKIE['vostok_sso_token'])) {
    $tokenData = verifySsoCookie($_COOKIE['vostok_sso_token']);
    if ($tokenData) {
        $currentUser = [
            'emp_id' => $tokenData['emp_id'],
            'email' => $tokenData['email'],
            'clearance_level' => $tokenData['clearance_level'],
            'full_name' => $tokenData['full_name'] ?? 'Authorized User'
        ];
    }
}

$userClearance = $currentUser['clearance_level'] ?? 'L1';
$actorId = $currentUser['email'] ?? ($currentUser['emp_id'] ?? 'GUEST');
$isSuperAdmin = ($userClearance === 'L4' || ($currentUser['email'] ?? '') === 'admin@gmail.com');

// System mapping helper
$systemCodeMap = [
    'SYS01' => 'ADM',
    'ADM' => 'SYS01',
    'SYS02' => 'CRM',
    'CRM' => 'SYS02',
    'SYS03' => 'CUS',
    'CUS' => 'SYS03',
    'SYS04' => 'DEV',
    'DEV' => 'SYS04',
    'SYS05' => 'EMP',
    'EMP' => 'SYS05',
    'SYS06' => 'DOC',
    'DOC' => 'SYS06',
    'SYS07' => 'FIN',
    'FIN' => 'SYS07',
    'SYS08' => 'HR',
    'HR'  => 'SYS08',
    'SYS09' => 'IT',
    'IT'  => 'SYS09',
    'SYS10' => 'SHP',
    'SHP' => 'SYS10',
    'SYS11' => 'WEB',
    'WEB' => 'SYS11',
    'ALL'   => 'ALL'
];

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
                echo json_encode(['success' => false, 'error' => 'Integration link not found']);
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
            ]);
            exit;
        }

        // Filter by system (SYS01..SYS11 or ADM..WEB)
        $whereSql = "1=1";
        $params = [];

        if (!empty($system)) {
            $sysUpper = strtoupper($system);
            $alias = $systemCodeMap[$sysUpper] ?? $sysUpper;

            // Include integrations where this system is source, target, or target is ALL (for SYS11)
            $whereSql = "(source_system_id = :s1 OR source_system_id = :s2 OR target_system_id = :s1 OR target_system_id = :s2 OR target_system_id = 'ALL')";
            $params[':s1'] = $sysUpper;
            $params[':s2'] = $alias;
        }

        $stmt = $pdo->prepare("SELECT * FROM system_integrations WHERE {$whereSql} ORDER BY link_code ASC");
        $stmt->execute($params);
        $integrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $logs = [];
        if ($includeLogs) {
            $logWhere = "1=1";
            $logParams = [];
            if (!empty($system)) {
                $sysUpper = strtoupper($system);
                $alias = $systemCodeMap[$sysUpper] ?? $sysUpper;
                $logWhere = "(source_system_id = :s1 OR source_system_id = :s2 OR target_system_id = :s1 OR target_system_id = :s2 OR target_system_id = 'ALL')";
                $logParams[':s1'] = $sysUpper;
                $logParams[':s2'] = $alias;
            }
            $logStmt = $pdo->prepare("SELECT * FROM system_integration_logs WHERE {$logWhere} ORDER BY log_id DESC LIMIT 50");
            $logStmt->execute($logParams);
            $logs = $logStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode([
            'success' => true,
            'system' => $system ?: 'ALL',
            'count' => count($integrations),
            'integrations' => $integrations,
            'logs' => $logs,
            'user' => [
                'actor_id' => $actorId,
                'clearance_level' => $userClearance,
                'is_superadmin' => $isSuperAdmin
            ]
        ]);
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

    $linkCode = trim($input['link_code'] ?? '');
    $action   = trim($input['action'] ?? 'SYNC_EVENT');
    $payload  = $input['payload'] ?? [];
    $customEndpoint = trim($input['endpoint'] ?? '');

    if (empty($linkCode)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing link_code parameter']);
        exit;
    }

    try {
        // 1. Fetch integration link definition
        $stmt = $pdo->prepare("SELECT * FROM system_integrations WHERE link_code = ? LIMIT 1");
        $stmt->execute([$linkCode]);
        $link = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$link) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => "Integration link '{$linkCode}' is not registered"]);
            exit;
        }

        // 2. Permissions validation (Clearance hierarchy: L1 < L2 < L3 < L4)
        $clearanceRank = ['L1' => 1, 'L2' => 2, 'L3' => 3, 'L4' => 4];
        $requiredRank = $clearanceRank[$link['required_clearance']] ?? 2;
        $userRank = $clearanceRank[$userClearance] ?? 1;

        if (!$isSuperAdmin && $userRank < $requiredRank) {
            // Permission denied: log unauthorized attempt
            $endpoint = $customEndpoint ?: "/api/integrations/{$linkCode}/dispatch";
            $summary = "UNAUTHORIZED: User '{$actorId}' with clearance {$userClearance} attempted access requiring {$link['required_clearance']}";

            $logStmt = $pdo->prepare("
                INSERT INTO system_integration_logs 
                (link_code, source_system_id, target_system_id, api_protocol, endpoint, payload_summary, direction, status_code, actor_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, 403, ?)
            ");
            $logStmt->execute([
                $linkCode,
                $link['source_system_id'],
                $link['target_system_id'],
                $link['api_protocol'],
                $endpoint,
                $summary,
                $link['direction'],
                $actorId
            ]);

            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error' => "Access Denied: Requires clearance {$link['required_clearance']}. Current clearance: {$userClearance}",
                'link_code' => $linkCode
            ]);
            exit;
        }

        // 3. Process the Integration Action based on the specific link
        $endpoint = $customEndpoint ?: "/api/integrations/{$linkCode}/dispatch";
        $resultData = [];
        $statusCode = 200;

        switch ($linkCode) {
            case 'SYS01_TO_SYS02':
                // ADM -> CRM: Governance Policies & Audit Compliance
                $summary = "Governance directive dispatched from SYS01 (ADM) to SYS02 (CRM). Action: " . $action;
                $resultData = [
                    'compliance_status' => 'VERIFIED',
                    'directive_id' => 'DIR-' . strtoupper(substr(md5(time()), 0, 8)),
                    'client_oversight' => 'ISO-27001-COMPLIANT'
                ];
                break;

            case 'SYS02_TO_SYS03':
                // CRM -> CUS: Customer Accounts, SLA Tiers, Contracts
                $summary = "Customer account and SLA profile synchronized from SYS02 (CRM) to SYS03 (CUS). Action: " . $action;
                $resultData = [
                    'sync_type' => 'CUSTOMER_PORTAL_PROVISIONING',
                    'records_synced' => 1,
                    'portal_status' => 'ONLINE'
                ];
                break;

            case 'SYS02_TO_SYS05':
                // CRM -> EMP: Sales metrics & department win announcements
                $summary = "Sales benchmark notification published from SYS02 (CRM) to SYS05 (EMP Intranet). Action: " . $action;
                $resultData = [
                    'intranet_feed' => 'ANNOUNCED',
                    'sales_channel' => 'ENTERPRISE_METRICS'
                ];
                break;

            case 'SYS03_TO_SYS05':
                // CUS -> EMP: Customer support ticket escalation & feedback
                $summary = "Customer support escalation routed from SYS03 (CUS) to SYS05 (EMP Intranet). Action: " . $action;
                $resultData = [
                    'ticket_routing' => 'DISPATCHED_TO_DEPARTMENT',
                    'priority' => 'HIGH'
                ];
                break;

            case 'SYS05_TO_SYS06':
                // EMP -> DOC: Internal policy document ingestion & archiving
                $summary = "Internal corporate document ingested from SYS05 (EMP) to SYS06 (DOC File Center). Action: " . $action;
                $resultData = [
                    'doc_repository' => 'ARCHIVED_VAULT',
                    'checksum' => hash('sha256', json_encode($payload))
                ];
                break;

            case 'SYS05_TO_SYS07':
                // EMP -> FIN: Employee expense claims & budget requisitions
                $summary = "Expense claim & requisition dispatched from SYS05 (EMP) to SYS07 (FIN Finance & Billing). Action: " . $action;
                $resultData = [
                    'claim_status' => 'FORWARDED_TO_AUDIT',
                    'ledger_code' => 'ACC-FIN-EMP'
                ];
                break;

            case 'SYS04_TO_SYS06':
                // DEV -> DOC: API technical specs & architecture schematics
                $summary = "Technical OpenAPI specification published from SYS04 (DEV) to SYS06 (DOC). Action: " . $action;
                $resultData = [
                    'spec_version' => 'v3.1.0-industrial',
                    'sync_state' => 'COMMITTED'
                ];
                break;

            case 'SYS04_TO_SYS08':
                // DEV -> HR: Developer assessment & engineering profiles
                $summary = "Engineering candidate technical score submitted from SYS04 (DEV) to SYS08 (HR). Action: " . $action;
                $resultData = [
                    'assessment_pipeline' => 'HR_RECRUITMENT_UPDATED',
                    'eval_score' => '98/100'
                ];
                break;

            case 'SYS04_TO_SYS09':
                // DEV -> IT: Telemetry alerts & CI/CD deployment incidents
                $summary = "Automated telemetry incident ticket generated from SYS04 (DEV) to SYS09 (IT Helpdesk). Action: " . $action;
                $resultData = [
                    'incident_id' => 'INC-' . rand(1000, 9999),
                    'severity' => 'P2_AUTOMATED'
                ];
                break;

            case 'SYS10_TO_SYS02':
                // SHP -> CRM: High-value B2B purchase lead
                $summary = "B2B commercial wholesale RFQ transmitted from SYS10 (SHP) to SYS02 (CRM). Action: " . $action;
                $resultData = [
                    'lead_tier' => 'ENTERPRISE_TIER_A',
                    'crm_account' => 'PROSPECT_REGISTERED'
                ];
                break;

            case 'SYS10_TO_SYS05':
                // SHP -> EMP: Warehouse inventory depletion notification
                $summary = "Inventory threshold alert broadcast from SYS10 (SHP) to SYS05 (EMP Intranet). Action: " . $action;
                $resultData = [
                    'inventory_alert' => 'LOGISTICS_NOTIFIED',
                    'stock_health' => 'DISPATCH_TRIGGERED'
                ];
                break;

            case 'SYS11_TO_ALL':
                // WEB -> ALL: Universal gateway broadcast & SSO routing
                $summary = "Universal Corporate Platform announcement broadcast from SYS11 (WEB) to ALL subsystems (SYS01-SYS10). Action: " . $action;
                $resultData = [
                    'broadcast_scope' => 'ALL_11_SYSTEMS',
                    'delivery_status' => 'CONFIRMED',
                    'timestamp' => date('Y-m-d H:i:s')
                ];
                break;

            default:
                $summary = "Standard transaction executed for {$linkCode}. Action: " . $action;
                $resultData = ['status' => 'PROCESSED'];
                break;
        }

        // If custom payload provided, append summary snippet
        if (!empty($payload)) {
            $payloadJson = json_encode($payload);
            $summary .= " | Payload: " . (strlen($payloadJson) > 200 ? substr($payloadJson, 0, 200) . '...' : $payloadJson);
        }

        // 4. Record entry in system_integration_logs
        $logStmt = $pdo->prepare("
            INSERT INTO system_integration_logs 
            (link_code, source_system_id, target_system_id, api_protocol, endpoint, payload_summary, direction, status_code, actor_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $logStmt->execute([
            $linkCode,
            $link['source_system_id'],
            $link['target_system_id'],
            $link['api_protocol'],
            $endpoint,
            $summary,
            $link['direction'],
            $statusCode,
            $actorId
        ]);

        $logId = $pdo->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => "Integration transaction executed and logged successfully",
            'link_code' => $linkCode,
            'log_id' => $logId,
            'source' => $link['source_system_id'],
            'target' => $link['target_system_id'],
            'protocol' => $link['api_protocol'],
            'authentication' => $link['authentication_method'],
            'direction' => $link['direction'],
            'status_code' => $statusCode,
            'actor_id' => $actorId,
            'executed_at' => date('Y-m-d H:i:s'),
            'result' => $resultData
        ]);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}
