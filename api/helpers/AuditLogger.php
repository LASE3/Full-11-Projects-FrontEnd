<?php

/**
 * VOSTOKPRIBOR Audit & Security Logger
 * Location: api/helpers/AuditLogger.php
 */
require_once __DIR__ . '/../../config/db.php';

class AuditLogger
{
    public static function logAction($empId, $cusId, $systemName, $systemCode, $action, $entityType, $entityId, $newValues = null, $result = 'SUCCESS')
    {
        try {
            $pdo = getDbConnection();
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

            $stmt = $pdo->prepare("
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
            $stmt->execute([
                ':emp'      => $empId,
                ':cus'      => $cusId,
                ':sys_name' => $systemName,
                ':sys_id'   => $systemCode,
                ':action'   => $action,
                ':etype'    => $entityType,
                ':eid'      => (string)$entityId,
                ':ip'       => $ip,
                ':vals'     => $newValues ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null,
                ':result'   => $result
            ]);
        } catch (Exception $e) {
            error_log("Audit logger error: " . $e->getMessage());
        }
    }

    public static function logSecurityEvent($eventType, $systemCode, $description, $severity = 'Medium', $actorEmp = null, $actorCus = null)
    {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("
                INSERT INTO security_events (
                    event_type, source_system_id, actor_emp_id, actor_customer_id,
                    description, severity, status, event_time
                ) VALUES (
                    :type, :sys, :emp, :cus,
                    :desc, :sev, 'New', NOW()
                )
            ");
            $stmt->execute([
                ':type' => $eventType,
                ':sys'  => $systemCode,
                ':emp'  => $actorEmp,
                ':cus'  => $actorCus,
                ':desc' => $description,
                ':sev'  => $severity
            ]);
        } catch (Exception $e) {
            error_log("Security event logging error: " . $e->getMessage());
        }
    }
}
