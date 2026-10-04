-- ============================================================================
-- Migration 011: Least-Privilege DB User & Schema Hardening
-- Creates vostok_app user with SELECT/INSERT/UPDATE/DELETE privileges only.
-- Adds product_name to order_items for force-delete snapshots.
-- Adds must_change_password to customer_accounts.
-- Adds token_hash to developer_api_keys and allows token_full to be NULL.
-- Cleans up 12 legacy SYS01_TO_SYS02 integration logs.
-- ============================================================================

-- 1. Create least-privilege vostok_app user
CREATE USER IF NOT EXISTS 'vostok_app'@'localhost' IDENTIFIED BY 'VostokApp2026!Secure';
CREATE USER IF NOT EXISTS 'vostok_app'@'127.0.0.1' IDENTIFIED BY 'VostokApp2026!Secure';

GRANT SELECT, INSERT, UPDATE, DELETE ON `vostokpribor`.* TO 'vostok_app'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON `vostokpribor`.* TO 'vostok_app'@'127.0.0.1';

GRANT SELECT, INSERT, UPDATE, DELETE ON `vostokpribor_test`.* TO 'vostok_app'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON `vostokpribor_test`.* TO 'vostok_app'@'127.0.0.1';

FLUSH PRIVILEGES;

-- 2. Ensure order_items has product_name column for snapshot on force-delete
ALTER TABLE `order_items` ADD COLUMN IF NOT EXISTS `product_name` VARCHAR(150) NULL AFTER `prod_id`;

-- 3. Ensure customer_accounts has must_change_password column
ALTER TABLE `customer_accounts` ADD COLUMN IF NOT EXISTS `must_change_password` TINYINT(1) NOT NULL DEFAULT 0;

-- 4. Harden developer_api_keys: add token_hash and make token_full nullable
ALTER TABLE `developer_api_keys` ADD COLUMN IF NOT EXISTS `token_hash` VARCHAR(255) NULL AFTER `token_prefix`;
ALTER TABLE `developer_api_keys` MODIFY COLUMN `token_full` VARCHAR(255) NULL DEFAULT NULL;

-- 5. Delete legacy SYS01_TO_SYS02 style rows from system_integration_logs
DELETE FROM `system_integration_logs` WHERE `link_code` REGEXP '^SYS[0-9]{2}_TO_SYS[0-9]{2}$';
