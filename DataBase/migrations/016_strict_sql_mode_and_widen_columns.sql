-- Migration 016: Strict SQL Mode Alignment & Column Widening
-- 1. Ensure tickets.source_system has DEFAULT 'IT' for strict SQL mode compatibility
-- 2. Widen systems_catalog, security_events, audit_logs, and system_integration_logs system_id columns to VARCHAR(16)
-- Idempotent execution

SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `tickets` MODIFY COLUMN `source_system` VARCHAR(50) NOT NULL DEFAULT 'IT';
ALTER TABLE `systems_catalog` MODIFY COLUMN `system_id` VARCHAR(16) NOT NULL;
ALTER TABLE `security_events` MODIFY COLUMN `source_system_id` VARCHAR(16) DEFAULT NULL;
ALTER TABLE `audit_logs` MODIFY COLUMN `system_id` VARCHAR(16) DEFAULT NULL;
ALTER TABLE `role_system_access` MODIFY COLUMN `system_id` VARCHAR(16) NOT NULL;
ALTER TABLE `system_integration_logs` MODIFY COLUMN `source_system_id` VARCHAR(16) NOT NULL;
ALTER TABLE `system_integration_logs` MODIFY COLUMN `target_system_id` VARCHAR(16) NOT NULL;
ALTER TABLE `system_integrations` MODIFY COLUMN `source_system_id` VARCHAR(16) NOT NULL;
ALTER TABLE `system_integrations` MODIFY COLUMN `target_system_id` VARCHAR(16) NOT NULL;

SET FOREIGN_KEY_CHECKS = 1;
