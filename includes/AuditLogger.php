<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR Centralized Audit & Security Logger
 * Single canonical shared logger across all 11 enterprise systems:
 * WEB, SHP, CUS, EMP, CRM, HR, FIN, IT, DOC, DEV, ADM
 */

require_once __DIR__ . '/../config/db.php';

class AuditLogger
{
    private static function sanitizeEmpId(PDO $pdo, ?string $empId): ?string
    {
        if (empty($empId) || !is_string($empId)) {
            return null;
        }
        try {
            $stmt = $pdo->prepare("SELECT 1 FROM employees WHERE emp_id = ? LIMIT 1");
            $stmt->execute([$empId]);
            return $stmt->fetchColumn() ? $empId : null;
        } catch (Throwable) {
            return null;
        }
    }

    private static function sanitizeCusId(PDO $pdo, ?string $cusId): ?string
    {
        if (empty($cusId) || !is_string($cusId)) {
            return null;
        }
        try {
            $stmt = $pdo->prepare("SELECT 1 FROM customers WHERE cus_id = ? LIMIT 1");
            $stmt->execute([$cusId]);
            return $stmt->fetchColumn() ? $cusId : null;
        } catch (Throwable) {
            return null;
        }
    }

    public static function sanitizeSysId($code): string
    {
        $code = strtoupper(trim((string)$code));
        $map = [
            'CRM' => 'CRM', 'CUSTOMER' => 'CUS', 'CUS' => 'CUS', 'PORTAL' => 'CUS',
            'SHOP' => 'SHP', 'SHP' => 'SHP', 'B2B' => 'SHP',
            'INTRANET' => 'EMP', 'EMP' => 'EMP', 'EMPLOYEE' => 'EMP', 'OPS' => 'EMP',
            'ADMIN' => 'ADM', 'ADM' => 'ADM', 'GOV' => 'ADM',
            'FIN' => 'FIN', 'FINANCE' => 'FIN', 'BILLING' => 'FIN',
            'HR' => 'HR', 'IT' => 'IT', 'HELPDESK' => 'IT',
            'DEV' => 'DEV', 'DEVELOPER' => 'DEV',
            'DOC' => 'DOC', 'FILE' => 'DOC', 'FILES' => 'DOC',
            'WEB' => 'WEB', 'CORP' => 'WEB'
        ];
        return $map[$code] ?? substr($code, 0, 4);
    }

    public static function log(
        PDO $pdo,
        string $systemCode,
        string $action,
        string $entityType,
        string|int|null $entityId = null,
        ?string $empId = null,
        mixed $newValues = null,
        ?string $actorEmpId = null
    ): void {
        self::logAction($actorEmpId ?: $empId, null, $systemCode, $systemCode, $action, $entityType, $entityId, $newValues);
    }

    public static function logAction(
        ?string $empId,
        ?string $cusId,
        string $systemName,
        string $systemCode,
        string $action,
        string $entityType,
        string|int|null $entityId = null,
        mixed $newValues = null,
        string $result = 'SUCCESS',
        mixed $oldValues = null
    ): void {
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
                    source_ip, old_values, new_values, result, occurred_at
                ) VALUES (
                    :emp, :cus, :sys_name, :sys_id,
                    :action, :etype, :eid,
                    :ip, :old_vals, :vals, :result, NOW()
                )
            ");
            $stmt->execute([
                ':emp'      => $validEmp,
                ':cus'      => $validCus,
                ':sys_name' => substr((string)$systemName, 0, 50),
                ':sys_id'   => $validSys,
                ':action'   => substr((string)$action, 0, 200),
                ':etype'    => substr((string)$entityType, 0, 50),
                ':eid'      => $entityId !== null ? substr((string)$entityId, 0, 20) : null,
                ':ip'       => substr((string)$ip, 0, 45),
                ':old_vals' => $oldValues !== null ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
                ':vals'     => $newValues !== null ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null,
                ':result'   => substr((string)$result, 0, 20)
            ]);
        } catch (Throwable $e) {
            error_log("Audit logger error: " . $e->getMessage());
        }
    }

    public static function logSecurityEvent(
        string $eventType,
        string $systemCode,
        string $description,
        string $severity = 'Medium',
        ?string $actorEmp = null,
        ?string $actorCus = null
    ): void {
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
