-- Migration 005: Schema adjustments for Flows D, E, H, J
-- Database: active connection target

-- 1. Invoices creator column for separation of duties enforcement
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `created_by_emp_id` VARCHAR(10) NULL AFTER `prj_id`;

-- 2. api_credentials expires_at timestamp nullability to prevent 1067 zero-date error
ALTER TABLE `api_credentials` MODIFY COLUMN `expires_at` TIMESTAMP NULL DEFAULT NULL;

-- 3. Ensure employee_accounts supports 'Inactive' status for pending onboarding
ALTER TABLE `employee_accounts` MODIFY COLUMN `status` VARCHAR(20) DEFAULT 'Inactive';
