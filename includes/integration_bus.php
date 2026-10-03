<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR Universal Integration Event Bus (vp_emit)
 * Centralizes all inter-system communication, integration logs,
 * audit logging, governance security events, and notification dispatch.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth_guard.php';

/**
 * Emit an inter-system integration event across the VOSTOKPRIBOR enterprise network.
 *
 * In one transaction:
 * (a) insert into system_integration_logs using only real columns
 * (b) insert an audit_logs row
 * (c) if severity >= Medium or governance=true, insert a security_events row
 * (d) insert a portal_notifications row for the target system owners
 *
 * @param PDO $pdo Active PDO connection
 * @param string $link_code Canonical link code (e.g. WEB_TO_CRM, CRM_TO_FIN)
 * @param string $source Canonical source system code (WEB, SHP, CUS, EMP, CRM, HR, FIN, IT, DOC, DEV, ADM)
 * @param string $target Canonical target system code
 * @param string $event Event name (e.g. LEAD_CREATED, ORDER_PLACED)
 * @param array<string, mixed> $payload Event data and metadata
 * @param string $actor Actor ID (e.g. EMP-1001, CUS-1001, SYSTEM)
 * @param int $status HTTP status code (default: 200)
 * @return int Insert ID from system_integration_logs
 */
function vp_emit(
    PDO $pdo,
    string $link_code,
    string $source,
    string $target,
    string $event,
    array $payload,
    string $actor,
    int $status = 200
): int {
    $srcCanonical = canonicalSystemCode($source);
    $tgtCanonical = canonicalSystemCode($target);

    // Map internal OPS subsystem references to Employee Intranet (EMP)
    if ($srcCanonical === 'OPS') $srcCanonical = 'EMP';
    if ($tgtCanonical === 'OPS') $tgtCanonical = 'EMP';

    // Validate source and target systems against systems_catalog
    static $knownSystems = null;
    if ($knownSystems === null) {
        try {
            $knownSystems = $pdo->query("SELECT system_id FROM systems_catalog")->fetchAll(PDO::FETCH_COLUMN);
        } catch (Throwable) {
            $knownSystems = ['WEB', 'SHP', 'CUS', 'EMP', 'CRM', 'HR', 'FIN', 'IT', 'DOC', 'DEV', 'ADM'];
        }
    }

    if (!in_array($srcCanonical, $knownSystems, true)) {
        throw new InvalidArgumentException("Invalid source system '{$source}' in vp_emit.");
    }
    if (!in_array($tgtCanonical, $knownSystems, true)) {
        throw new InvalidArgumentException("Invalid target system '{$target}' in vp_emit.");
    }

    $startedTx = false;
    if (!$pdo->inTransaction()) {
        $pdo->beginTransaction();
        $startedTx = true;
    }

    try {
        // (a) Insert into system_integration_logs using ONLY the real columns:
        // link_code, source_system_id, target_system_id, api_protocol, endpoint,
        // payload_summary, direction, status_code, actor_id, executed_at
        $protocol = (string)($payload['api_protocol'] ?? 'REST / JSON HTTPS');
        $endpoint = (string)($payload['endpoint'] ?? "/api/integrations/{$link_code}/dispatch");
        $direction = (string)($payload['direction'] ?? "Outbound ({$srcCanonical} -> {$tgtCanonical})");

        $summaryText = $payload['summary'] ?? ($payload['description'] ?? ("Event: {$event} | Payload: " . json_encode($payload, JSON_UNESCAPED_UNICODE)));
        if (mb_strlen($summaryText) > 65000) {
            $summaryText = mb_substr($summaryText, 0, 65000) . '...';
        }

        $stmtLog = $pdo->prepare("
            INSERT INTO system_integration_logs 
            (link_code, source_system_id, target_system_id, api_protocol, endpoint, payload_summary, direction, status_code, actor_id, executed_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $link_code,
            $srcCanonical,
            $tgtCanonical,
            $protocol,
            $endpoint,
            $summaryText,
            $direction,
            $status,
            $actor
        ]);
        $logId = (int)$pdo->lastInsertId();

        // (b) Insert an audit_logs row
        $actorEmpId = null;
        $actorCusId = null;
        if (str_starts_with($actor, 'EMP-')) {
            $actorEmpId = $actor;
        } elseif (str_starts_with($actor, 'CUS-')) {
            $actorCusId = $actor;
        }

        $stmtAudit = $pdo->prepare("
            INSERT INTO audit_logs (
                actor_emp_id, actor_customer_id, actor_system, system_id,
                action, target_entity_type, target_entity_id,
                source_ip, new_values, result, occurred_at
            ) VALUES (
                :emp, :cus, :sys_name, :sys_id,
                :action, :etype, :eid,
                :ip, :vals, :result, NOW()
            )
        ");
        $stmtAudit->execute([
            ':emp'      => $actorEmpId,
            ':cus'      => $actorCusId,
            ':sys_name' => $srcCanonical,
            ':sys_id'   => $srcCanonical,
            ':action'   => "INTEGRATION_{$link_code}: {$event}",
            ':etype'    => substr((string)($payload['entity_type'] ?? 'system_integration'), 0, 50),
            ':eid'      => substr((string)($payload['entity_id'] ?? $link_code), 0, 20),
            ':ip'       => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            ':vals'     => json_encode($payload, JSON_UNESCAPED_UNICODE),
            ':result'   => ($status < 400) ? 'SUCCESS' : 'FAILURE'
        ]);

        // (c) If severity >= Medium or event is flagged governance=true, insert a security_events row
        $severityRaw = $payload['severity'] ?? null;
        $isGovernance = !empty($payload['governance']);
        $sevNormalized = null;
        if ($severityRaw !== null) {
            $sevNormalized = ucfirst(strtolower((string)$severityRaw));
        }

        $triggersSecurity = $isGovernance || in_array($sevNormalized, ['Medium', 'High', 'Critical'], true);
        if ($triggersSecurity) {
            $finalSeverity = in_array($sevNormalized, ['Low', 'Medium', 'High', 'Critical'], true) ? $sevNormalized : 'Medium';
            $secDesc = $payload['description'] ?? "Governance Integration Event [{$event}] on link {$link_code} from {$srcCanonical} to {$tgtCanonical}";

            $stmtSec = $pdo->prepare("
                INSERT INTO security_events (
                    event_type, source_system_id, actor_emp_id, actor_customer_id,
                    description, severity, status, event_time, reported_to_governance
                ) VALUES (
                    :type, :sys, :emp, :cus,
                    :desc, :sev, 'New', NOW(), :gov
                )
            ");
            $stmtSec->execute([
                ':type' => substr("GOV_{$link_code}_{$event}", 0, 50),
                ':sys'  => $srcCanonical,
                ':emp'  => $actorEmpId,
                ':cus'  => $actorCusId,
                ':desc' => $secDesc,
                ':sev'  => $finalSeverity,
                ':gov'  => $isGovernance ? 1 : 0
            ]);
        }

        // (d) Insert a portal_notifications / intranet notification for the target system owners
        $targetUserId = 1;
        if (!empty($payload['portal_user_id'])) {
            $targetUserId = (int)$payload['portal_user_id'];
        } elseif (!empty($payload['cus_id'])) {
            try {
                $accStmt = $pdo->prepare("SELECT portal_user_id FROM portal_accounts WHERE cus_id = ? LIMIT 1");
                $accStmt->execute([$payload['cus_id']]);
                $puid = $accStmt->fetchColumn();
                if ($puid) {
                    $targetUserId = (int)$puid;
                } else {
                    $cleanCus = preg_replace('/[^a-zA-Z0-9]/', '', (string)$payload['cus_id']);
                    $paIns = $pdo->prepare("INSERT INTO portal_accounts (cus_id, username, email) VALUES (?, ?, ?)");
                    $paIns->execute([$payload['cus_id'], 'user_' . strtolower($cleanCus), 'user@' . strtolower($cleanCus) . '.local']);
                    $targetUserId = (int)$pdo->lastInsertId();
                }
            } catch (Throwable) {
                try {
                    $any = $pdo->query("SELECT portal_user_id FROM portal_accounts LIMIT 1")->fetchColumn();
                    if ($any) $targetUserId = (int)$any;
                } catch (Throwable) {}
            }
        } else {
            try {
                $any = $pdo->query("SELECT portal_user_id FROM portal_accounts LIMIT 1")->fetchColumn();
                if ($any) $targetUserId = (int)$any;
            } catch (Throwable) {}
        }

        $notifTitle = (string)($payload['notification_title'] ?? "Integration Notice: {$event}");
        $notifMsg   = (string)($payload['notification_message'] ?? ($payload['description'] ?? "System {$srcCanonical} dispatched {$event} to {$tgtCanonical}"));

        $stmtNotif = $pdo->prepare("
            INSERT INTO portal_notifications (
                portal_user_id, system_code, title, message, related_entity_type, related_entity_id, is_read, severity, created_at
            ) VALUES (
                :uid, :sys, :title, :msg, :rtype, :rid, 0, :sev, NOW()
            )
        ");
        $stmtNotif->execute([
            ':uid'   => $targetUserId,
            ':sys'   => $tgtCanonical,
            ':title' => substr($notifTitle, 0, 255),
            ':msg'   => $notifMsg,
            ':rtype' => substr((string)($payload['entity_type'] ?? 'integration'), 0, 30),
            ':rid'   => substr((string)($payload['entity_id'] ?? $link_code), 0, 15),
            ':sev'   => ($status >= 400) ? 'warning' : 'info'
        ]);

        if ($startedTx) {
            $pdo->commit();
        }

        return $logId;
    } catch (Throwable $e) {
        if ($startedTx && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("vp_emit execution error on link {$link_code}: " . $e->getMessage());
        throw $e;
    }
}
