-- Migration 015: Per-User Data Isolation, Audit & Hardening
-- Idempotent: safe to run multiple times.

-- 1. Ensure leave_requests has a reason column
ALTER TABLE `leave_requests` ADD COLUMN IF NOT EXISTS `reason` VARCHAR(255) NULL AFTER `end_date`;

-- 2. Ensure portal_notifications has account_id column
ALTER TABLE `portal_notifications` ADD COLUMN IF NOT EXISTS `account_id` INT(11) NULL AFTER `notification_id`;
UPDATE `portal_notifications` SET `account_id` = `portal_user_id` WHERE `account_id` IS NULL AND `portal_user_id` IS NOT NULL;

-- 3. Ensure developer_api_keys has user scoping columns
ALTER TABLE `developer_api_keys` ADD COLUMN IF NOT EXISTS `created_by_account_id` INT(11) NULL AFTER `partner_id`;
ALTER TABLE `developer_api_keys` ADD COLUMN IF NOT EXISTS `created_by_user_id` VARCHAR(50) NULL AFTER `created_by_account_id`;

-- 4. User preferences table (scoped by account_id)
CREATE TABLE IF NOT EXISTS `user_preferences` (
    `pref_id` INT AUTO_INCREMENT PRIMARY KEY,
    `account_id` INT NOT NULL,
    `account_type` VARCHAR(20) NOT NULL DEFAULT 'Employee',
    `preference_key` VARCHAR(100) NOT NULL,
    `preference_value` TEXT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_user_pref` (`account_id`, `account_type`, `preference_key`),
    KEY `idx_pref_account` (`account_id`, `account_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Saved filters table (scoped by account_id)
CREATE TABLE IF NOT EXISTS `saved_filters` (
    `filter_id` INT AUTO_INCREMENT PRIMARY KEY,
    `account_id` INT NOT NULL,
    `account_type` VARCHAR(20) NOT NULL DEFAULT 'Employee',
    `system_code` VARCHAR(10) NOT NULL,
    `filter_name` VARCHAR(100) NOT NULL,
    `filter_json` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_filter_user` (`account_id`, `account_type`, `system_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Restrict Operations staff (Role 8) from Finance (FIN is Top Secret: CFO/FIN/EXE only)
DELETE FROM `role_system_access` WHERE `role_id` = 8 AND `system_id` = 'FIN';

-- 7. Performance & isolation indexes on scoping columns (idempotent helper)
SET @exist := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'customers' AND index_name = 'idx_cus_acct_mgr');
SET @sql := IF(@exist = 0, 'CREATE INDEX idx_cus_acct_mgr ON customers (account_manager_emp_id)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'contracts' AND index_name = 'idx_contracts_cus');
SET @sql := IF(@exist = 0, 'CREATE INDEX idx_contracts_cus ON contracts (cus_id)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'invoices' AND index_name = 'idx_invoices_cus');
SET @sql := IF(@exist = 0, 'CREATE INDEX idx_invoices_cus ON invoices (cus_id)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'tickets' AND index_name = 'idx_tickets_req_cus');
SET @sql := IF(@exist = 0, 'CREATE INDEX idx_tickets_req_cus ON tickets (requester_cus_id)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'leave_requests' AND index_name = 'idx_leave_emp');
SET @sql := IF(@exist = 0, 'CREATE INDEX idx_leave_emp ON leave_requests (emp_id)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

