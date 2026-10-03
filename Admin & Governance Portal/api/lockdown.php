<?php

/**
 * VOSTOKPRIBOR System 11 // GOV-CORE
 * Emergency DEFCON-1 & Systems Lockdown REST API
 * Handles individual and global node isolation switches on `systems_catalog`.
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
    // 1. GET: Fetch Systems Isolation State
    // -------------------------------------------------------------------------
    if ($method === 'GET') {
        $systems = $pdo->query("SELECT * FROM systems_catalog ORDER BY system_id ASC")->fetchAll(PDO::FETCH_ASSOC);
        $isolatedCount = (int)$pdo->query("SELECT COUNT(*) FROM systems_catalog WHERE status = 'ISOLATED'")->fetchColumn();

        jsonResponse([
            'success' => true,
            'defcon_level' => ($isolatedCount > 0) ? 'DEFCON-1' : 'DEFCON-4',
            'isolated_nodes_count' => $isolatedCount,
            'total_nodes' => count($systems),
            'systems' => $systems
        ]);
    }

    // -------------------------------------------------------------------------
    // 2. POST: Lockdown & Air-Gap Actions
    // -------------------------------------------------------------------------
    if ($method === 'POST') {
        $action = trim($input['action'] ?? 'toggle_system');
        $actorId = getCurrentGovActor();

        // TOGGLE INDIVIDUAL SYSTEM
        if ($action === 'toggle_system') {
            $sysId = trim($input['system_id'] ?? '');
            if (empty($sysId)) {
                jsonResponse(['success' => false, 'error' => 'system_id is required.'], 400);
            }

            $stmt = $pdo->prepare("SELECT * FROM systems_catalog WHERE system_id = ?");
            $stmt->execute([$sysId]);
            $sys = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$sys) {
                jsonResponse(['success' => false, 'error' => 'System not found in catalog.'], 404);
            }

            $currentStatus = $sys['status'] ?? 'OPERATIONAL';
            $targetStatus = ($currentStatus === 'OPERATIONAL') ? 'ISOLATED' : 'OPERATIONAL';
            $reason = trim($input['reason'] ?? ($targetStatus === 'ISOLATED' ? 'Manual Air-Gap Interlock Triggered' : 'Operational Link Re-Established'));

            $isolatedAt = ($targetStatus === 'ISOLATED') ? date('Y-m-d H:i:s') : null;
            $isolatedBy = ($targetStatus === 'ISOLATED') ? $actorId : null;

            $upd = $pdo->prepare("
                UPDATE systems_catalog 
                SET status = ?, isolation_reason = ?, isolated_at = ?, isolated_by = ?
                WHERE system_id = ?
            ");
            $upd->execute([$targetStatus, $reason, $isolatedAt, $isolatedBy, $sysId]);

            recordAuditEntry($pdo, 'TOGGLE_NODE_ISOLATION', 'systems_catalog', $sysId, 'SUCCESS', [
                'system_id' => $sysId,
                'new_status' => $targetStatus,
                'reason' => $reason,
                'actor' => $actorId
            ]);

            jsonResponse([
                'success' => true,
                'message' => "Node [{$sysId}] posture switched to [{$targetStatus}].",
                'system_id' => $sysId,
                'status' => $targetStatus
            ]);
        }

        // FULL AIR-GAP (ALL SYSTEMS) - DEFCON-1
        if ($action === 'isolate_all') {
            $reason = trim($input['reason'] ?? 'DEFCON-1 EMERGENCY: Autonomous Full Air-Gap Protocol Enacted');

            $upd = $pdo->prepare("
                UPDATE systems_catalog 
                SET status = 'ISOLATED', isolation_reason = ?, isolated_at = NOW(), isolated_by = ?
            ");
            $upd->execute([$reason, $actorId]);

            recordAuditEntry($pdo, 'DEFCON_1_ENFORCE_FULL_AIRGAP', 'systems_catalog', 'ALL_SYSTEMS', 'CRITICAL', [
                'action' => 'ISOLATE_ALL_NODES',
                'reason' => $reason,
                'enforced_by' => $actorId
            ]);

            jsonResponse([
                'success' => true,
                'message' => 'DEFCON-1 ENGAGED: Full physical air-gap enforced across all industrial systems.',
                'defcon_level' => 'DEFCON-1'
            ]);
        }

        // RESTORE ALL TO OPERATIONAL
        if ($action === 'restore_all') {
            $upd = $pdo->prepare("
                UPDATE systems_catalog 
                SET status = 'OPERATIONAL', isolation_reason = NULL, isolated_at = NULL, isolated_by = NULL
            ");
            $upd->execute();

            recordAuditEntry($pdo, 'DEFCON_RESTORE_NORMAL_OPS', 'systems_catalog', 'ALL_SYSTEMS', 'SUCCESS', [
                'action' => 'RESTORE_ALL_OPERATIONAL',
                'authorized_by' => $actorId
            ]);

            jsonResponse([
                'success' => true,
                'message' => 'DEFCON-4 RESTORED: All industrial systems returned to nominal operations.',
                'defcon_level' => 'DEFCON-4'
            ]);
        }

        jsonResponse(['success' => false, 'error' => 'Unknown action.'], 400);
    }

    jsonResponse(['success' => false, 'error' => 'Unsupported method.'], 405);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
