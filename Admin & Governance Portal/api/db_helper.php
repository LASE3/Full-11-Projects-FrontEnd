<?php

/**
 * VOSTOKPRIBOR System 11 // GOV-CORE API Database Helper
 * Handles database connectivity, CORS/JSON headers, audit ledger insertions, and self-healing schema adjustments.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/db.php';

// Set JSON output headers
header('Content-Type: application/json; charset=utf-8');

/**
 * Return JSON response and exit
 */
function jsonResponse($data, $status = 200)
{
    http_response_code($status);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Get unified POST / JSON input body
 */
function getRequestInput()
{
    $input = $_POST;
    $raw = file_get_contents('php://input');
    if (!empty($raw)) {
        $json = json_decode($raw, true);
        if (is_array($json)) {
            $input = array_merge($input, $json);
        }
    }
    return $input;
}

/**
 * Get current actor employee ID
 */
function getCurrentGovActor()
{
    if (!empty($_SESSION['vostok_user']['user_id'])) {
        return $_SESSION['vostok_user']['user_id'];
    }
    if (!empty($_SESSION['vostok_user']['emp_id'])) {
        return $_SESSION['vostok_user']['emp_id'];
    }
    return 'EMP-0001'; // System Administrator (Executive SuperAdmin)
}

/**
 * Record action directly into unified immutable audit_logs ledger
 */
function recordAuditEntry($pdo, $action, $targetType, $targetId, $result = 'SUCCESS', $newValues = null, $systemId = 'ADM')
{
    try {
        $actorId = getCurrentGovActor();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $vals = is_array($newValues) ? json_encode($newValues, JSON_UNESCAPED_SLASHES) : $newValues;

        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (
                actor_emp_id, actor_system, system_id, action, 
                target_entity_type, target_entity_id, source_ip, result, new_values, occurred_at
            ) VALUES (?, 'ADM', ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $actorId,
            $systemId,
            $action,
            $targetType,
            $targetId,
            $ip,
            $result,
            $vals
        ]);
        return true;
    } catch (Exception $e) {
        error_log("Failed to write to audit_logs: " . $e->getMessage());
        return false;
    }
}

/**
 * Verify and self-heal tables & columns
 */
function gov_ensureSchemaReady($pdo)
{
    static $ready = false;
    if ($ready) return;

    try {
        // 1. security_policies columns
        $pdo->exec("
            ALTER TABLE `security_policies`
              ADD COLUMN IF NOT EXISTS `severity` VARCHAR(20) DEFAULT 'High',
              ADD COLUMN IF NOT EXISTS `enforcement_mode` VARCHAR(30) DEFAULT 'MANDATORY',
              ADD COLUMN IF NOT EXISTS `description` TEXT DEFAULT NULL,
              ADD COLUMN IF NOT EXISTS `system_id` VARCHAR(50) DEFAULT 'SYS-01..11'
        ");

        // 2. systems_catalog columns
        $pdo->exec("
            ALTER TABLE `systems_catalog`
              ADD COLUMN IF NOT EXISTS `status` VARCHAR(20) NOT NULL DEFAULT 'OPERATIONAL',
              ADD COLUMN IF NOT EXISTS `isolation_reason` VARCHAR(255) NULL DEFAULT NULL,
              ADD COLUMN IF NOT EXISTS `isolated_at` DATETIME NULL DEFAULT NULL,
              ADD COLUMN IF NOT EXISTS `isolated_by` VARCHAR(10) NULL DEFAULT NULL
        ");

        // 3. risk_register columns
        $pdo->exec("
            ALTER TABLE `risk_register`
              ADD COLUMN IF NOT EXISTS `title` VARCHAR(255) NULL,
              ADD COLUMN IF NOT EXISTS `system_target` VARCHAR(100) DEFAULT 'SYS-01 Production Enclave',
              ADD COLUMN IF NOT EXISTS `threat_vector` TEXT NULL
        ");

        // 4. compliance_controls table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `compliance_controls` (
              `control_id` INT AUTO_INCREMENT PRIMARY KEY,
              `control_code` VARCHAR(30) NOT NULL UNIQUE,
              `title` VARCHAR(255) NOT NULL,
              `description` TEXT,
              `system_id` VARCHAR(50) DEFAULT 'SYS-01..10',
              `framework` VARCHAR(50) DEFAULT 'KAZ-CERT DIR-44',
              `custodian_emp_id` VARCHAR(10) DEFAULT 'EMP-1005',
              `status` ENUM('COMPLIANT', 'DEVIATION', 'REMEDIATION') DEFAULT 'COMPLIANT',
              `evidence_ref` VARCHAR(50) DEFAULT 'DOC-2026-015',
              `last_reviewed` DATE DEFAULT NULL,
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // 5. recertification_windows table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `recertification_windows` (
              `window_id` INT AUTO_INCREMENT PRIMARY KEY,
              `title` VARCHAR(150) NOT NULL,
              `start_date` DATE NOT NULL,
              `end_date` DATE NOT NULL,
              `status` ENUM('Active', 'Scheduled', 'Completed') DEFAULT 'Active',
              `scopes` VARCHAR(255) DEFAULT 'ALL',
              `created_by_emp_id` VARCHAR(10) DEFAULT 'EMP-1005',
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $ready = true;
    } catch (Exception $e) {
        error_log("Schema self-heal warning: " . $e->getMessage());
    }
}
