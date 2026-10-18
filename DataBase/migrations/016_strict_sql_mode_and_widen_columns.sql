-- Migration 016: Strict SQL Mode Alignment & Column Widening
-- 1. Ensure tickets.source_system has DEFAULT 'IT' for strict SQL mode compatibility
-- 2. Drop 8 foreign keys referencing systems_catalog(system_id) via information_schema.REFERENTIAL_CONSTRAINTS
-- 3. Widen systems_catalog.system_id and all referencing FK columns to VARCHAR(16)
-- 4. Re-add the 8 foreign key constraints idempotently via information_schema.REFERENTIAL_CONSTRAINTS
-- Compatible and tested across MariaDB 10.4, 10.11, and MySQL 8.

SET FOREIGN_KEY_CHECKS = 0;

-- Ensure tickets source_system default
ALTER TABLE `tickets` MODIFY COLUMN `source_system` VARCHAR(50) NOT NULL DEFAULT 'IT';

-- 1. Drop the 8 foreign keys referencing systems_catalog(system_id)
SET @exist := (SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'api_access_logs' AND CONSTRAINT_NAME = 'fk_api_access_logs_system_id');
SET @sql := IF(@exist > 0, 'ALTER TABLE `api_access_logs` DROP FOREIGN KEY `fk_api_access_logs_system_id`', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'audit_logs' AND CONSTRAINT_NAME = 'fk_audit_logs_system_id');
SET @sql := IF(@exist > 0, 'ALTER TABLE `audit_logs` DROP FOREIGN KEY `fk_audit_logs_system_id`', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'authentication_events' AND CONSTRAINT_NAME = 'fk_authentication_events_system_id');
SET @sql := IF(@exist > 0, 'ALTER TABLE `authentication_events` DROP FOREIGN KEY `fk_authentication_events_system_id`', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'document_access_log' AND CONSTRAINT_NAME = 'fk_document_access_log_system_id');
SET @sql := IF(@exist > 0, 'ALTER TABLE `document_access_log` DROP FOREIGN KEY `fk_document_access_log_system_id`', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'it_assets' AND CONSTRAINT_NAME = 'fk_it_assets_system_id');
SET @sql := IF(@exist > 0, 'ALTER TABLE `it_assets` DROP FOREIGN KEY `fk_it_assets_system_id`', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'role_system_access' AND CONSTRAINT_NAME = 'fk_role_system_access_system_id');
SET @sql := IF(@exist > 0, 'ALTER TABLE `role_system_access` DROP FOREIGN KEY `fk_role_system_access_system_id`', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'security_events' AND CONSTRAINT_NAME = 'fk_security_events_source_system_id');
SET @sql := IF(@exist > 0, 'ALTER TABLE `security_events` DROP FOREIGN KEY `fk_security_events_source_system_id`', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'user_sessions' AND CONSTRAINT_NAME = 'fk_user_sessions_system_id');
SET @sql := IF(@exist > 0, 'ALTER TABLE `user_sessions` DROP FOREIGN KEY `fk_user_sessions_system_id`', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2. Modify primary referenced column in systems_catalog
ALTER TABLE `systems_catalog` MODIFY COLUMN `system_id` VARCHAR(16) NOT NULL;

-- 3. Modify referencing columns in dependent tables
ALTER TABLE `api_access_logs` MODIFY COLUMN `system_id` VARCHAR(16) DEFAULT NULL;
ALTER TABLE `audit_logs` MODIFY COLUMN `system_id` VARCHAR(16) DEFAULT NULL;
ALTER TABLE `authentication_events` MODIFY COLUMN `system_id` VARCHAR(16) DEFAULT NULL;
ALTER TABLE `document_access_log` MODIFY COLUMN `system_id` VARCHAR(16) DEFAULT NULL;
ALTER TABLE `it_assets` MODIFY COLUMN `system_id` VARCHAR(16) DEFAULT NULL;
ALTER TABLE `role_system_access` MODIFY COLUMN `system_id` VARCHAR(16) NOT NULL;
ALTER TABLE `security_events` MODIFY COLUMN `source_system_id` VARCHAR(16) DEFAULT NULL;
ALTER TABLE `user_sessions` MODIFY COLUMN `system_id` VARCHAR(16) DEFAULT NULL;

-- Other widened columns
ALTER TABLE `system_integration_logs` MODIFY COLUMN `source_system_id` VARCHAR(16) NOT NULL;
ALTER TABLE `system_integration_logs` MODIFY COLUMN `target_system_id` VARCHAR(16) NOT NULL;
ALTER TABLE `system_integrations` MODIFY COLUMN `source_system_id` VARCHAR(16) NOT NULL;
ALTER TABLE `system_integrations` MODIFY COLUMN `target_system_id` VARCHAR(16) NOT NULL;
-- Clean orphan values in referencing tables before restoring foreign keys
UPDATE `document_access_log` SET `system_id` = 'DOC' WHERE `system_id` IS NOT NULL AND `system_id` NOT IN (SELECT `system_id` FROM `systems_catalog`);

-- 4. Re-add foreign key constraints idempotently via REFERENTIAL_CONSTRAINTS
SET @exist := (SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'api_access_logs' AND CONSTRAINT_NAME = 'fk_api_access_logs_system_id');
SET @sql := IF(@exist = 0, 'ALTER TABLE `api_access_logs` ADD CONSTRAINT `fk_api_access_logs_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'audit_logs' AND CONSTRAINT_NAME = 'fk_audit_logs_system_id');
SET @sql := IF(@exist = 0, 'ALTER TABLE `audit_logs` ADD CONSTRAINT `fk_audit_logs_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'authentication_events' AND CONSTRAINT_NAME = 'fk_authentication_events_system_id');
SET @sql := IF(@exist = 0, 'ALTER TABLE `authentication_events` ADD CONSTRAINT `fk_authentication_events_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'document_access_log' AND CONSTRAINT_NAME = 'fk_document_access_log_system_id');
SET @sql := IF(@exist = 0, 'ALTER TABLE `document_access_log` ADD CONSTRAINT `fk_document_access_log_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'it_assets' AND CONSTRAINT_NAME = 'fk_it_assets_system_id');
SET @sql := IF(@exist = 0, 'ALTER TABLE `it_assets` ADD CONSTRAINT `fk_it_assets_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'role_system_access' AND CONSTRAINT_NAME = 'fk_role_system_access_system_id');
SET @sql := IF(@exist = 0, 'ALTER TABLE `role_system_access` ADD CONSTRAINT `fk_role_system_access_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'security_events' AND CONSTRAINT_NAME = 'fk_security_events_source_system_id');
SET @sql := IF(@exist = 0, 'ALTER TABLE `security_events` ADD CONSTRAINT `fk_security_events_source_system_id` FOREIGN KEY (`source_system_id`) REFERENCES `systems_catalog` (`system_id`)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'user_sessions' AND CONSTRAINT_NAME = 'fk_user_sessions_system_id');
SET @sql := IF(@exist = 0, 'ALTER TABLE `user_sessions` ADD CONSTRAINT `fk_user_sessions_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS = 1;
