<?php

/**
 * VOSTOKPRIBOR Audit & Security Logger
 */
require_once __DIR__ . '/../../config/db.php';

class AuditLogger
{
    private static function sanitizeEmpId($pdo, $empId)
    {
        if (empty($empId) || !is_string($empId)) return null;
        try {
            $stmt = $pdo->prepare("SELECT 1 FROM employees WHERE emp_id = ?");
            $stmt->execute([$empId]);
            return $stmt->fetchColumn() ? $empId : null;
        } catch (Throwable) {
            return null;
        }
    }

    private static function sanitizeCusId($pdo, $cusId)
    {
        if (empty($cusId) || !is_string($cusId)) return null;
        try {
            $stmt = $pdo->prepare("SELECT 1 FROM customers WHERE cus_id = ?");
            $stmt->execute([$cusId]);
            return $stmt->fetchColumn() ? $cusId : null;
        } catch (Throwable) {
            return null;
        }
    }

    private static function sanitizeSysId($code)
    {
        $code = strtoupper(trim((string)$code));
        $map = [
            'CRM' => 'CRM', 'CUSTOMER' => 'CUS', 'CUS' => 'CUS', 'PORTAL' => 'CUS',
            'SHOP' => 'SHP', 'SHP' => 'SHP', 'B2B' => 'SHP',
            'INTRANET' => 'EMP', 'EMP' => 'EMP', 'EMPLOYEE' => 'EMP',
            'ADMIN' => 'ADM', 'ADM' => 'ADM', 'GOV' => 'ADM',
            'FIN' => 'FIN', 'FINANCE' => 'FIN',
            'HR' => 'HR', 'IT' => 'IT', 'DEV' => 'DEV', 'DOC' => 'DOC', 'WEB' => 'WEB'
        ];
        return $map[$code] ?? substr($code, 0, 4);
    }

    public static function logAction($empId, $cusId, $systemName, $systemCode, $action, $entityType, $entityId, $newValues = null, $result = 'SUCCESS')
    {
        try {
            $pdo = getDbConnection();
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

            $validEmp = self::sanitizeEmpId($pdo, $empId);
            $validCus = self::sanitizeCusId($pdo, $cusId);
            $validSys = self::sanitizeSysId($systemCode);

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
                ':emp'      => $validEmp,
                ':cus'      => $validCus,
                ':sys_name' => substr((string)$systemName, 0, 50),
                ':sys_id'   => $validSys,
                ':action'   => substr((string)$action, 0, 200),
                ':etype'    => substr((string)$entityType, 0, 50),
                ':eid'      => substr((string)$entityId, 0, 20),
                ':ip'       => substr((string)$ip, 0, 45),
                ':vals'     => $newValues ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null,
                ':result'   => substr((string)$result, 0, 20)
            ]);
        } catch (Throwable $e) {
            error_log("Audit logger error: " . $e->getMessage());
        }
    }

    public static function logSecurityEvent($eventType, $systemCode, $description, $severity = 'Medium', $actorEmp = null, $actorCus = null)
    {
        try {
            $pdo = getDbConnection();

            $validEmp = self::sanitizeEmpId($pdo, $actorEmp);
            $validCus = self::sanitizeCusId($pdo, $actorCus);
            $validSys = self::sanitizeSysId($systemCode);

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
                ':type' => substr((string)$eventType, 0, 50),
                ':sys'  => $validSys,
                ':emp'  => $validEmp,
                ':cus'  => $validCus,
                ':desc' => $description,
                ':sev'  => in_array($severity, ['Low', 'Medium', 'High', 'Critical'], true) ? $severity : 'Medium'
            ]);
        } catch (Throwable $e) {
            error_log("Security event logging error: " . $e->getMessage());
        }
    }
}