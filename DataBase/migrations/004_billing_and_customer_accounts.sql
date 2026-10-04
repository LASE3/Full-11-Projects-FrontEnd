-- Migration 004: Adjust billing_cycles and customer_accounts for Flow B customer onboarding
-- Database: active connection target

-- Allow prj_id in billing_cycles to be NULL for customer-level billing relationships
ALTER TABLE `billing_cycles` MODIFY COLUMN `prj_id` VARCHAR(15) NULL;

-- Add cus_id column to billing_cycles if not present
ALTER TABLE `billing_cycles` ADD COLUMN IF NOT EXISTS `cus_id` VARCHAR(10) NULL AFTER `prj_id`;

-- Allow password_hash in customer_accounts to be NULL before invite activation
ALTER TABLE `customer_accounts` MODIFY COLUMN `password_hash` VARCHAR(255) NULL;

-- Add invite_token column to customer_accounts if not present
ALTER TABLE `customer_accounts` ADD COLUMN IF NOT EXISTS `invite_token` VARCHAR(64) NULL AFTER `password_hash`;

-- Add customers CRM columns required for baseline and CRM operations
ALTER TABLE `customers` ADD COLUMN IF NOT EXISTS `phone` varchar(50) DEFAULT NULL;
ALTER TABLE `customers` ADD COLUMN IF NOT EXISTS `headquarters` varchar(255) DEFAULT NULL;
ALTER TABLE `customers` ADD COLUMN IF NOT EXISTS `tax_id` varchar(50) DEFAULT NULL;
ALTER TABLE `customers` ADD COLUMN IF NOT EXISTS `health_score` int(11) DEFAULT 95;
ALTER TABLE `customers` ADD COLUMN IF NOT EXISTS `account_tier` varchar(50) DEFAULT 'Tier-1 Enterprise';
ALTER TABLE `customers` ADD COLUMN IF NOT EXISTS `status` varchar(50) DEFAULT 'Active';
