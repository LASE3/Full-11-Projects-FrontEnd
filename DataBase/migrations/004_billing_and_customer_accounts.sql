-- Migration 004: Adjust billing_cycles and customer_accounts for Flow B customer onboarding
-- Database: vostokpribor

USE vostokpribor;

-- Allow prj_id in billing_cycles to be NULL for customer-level billing relationships
ALTER TABLE `billing_cycles` MODIFY COLUMN `prj_id` VARCHAR(15) NULL;

-- Add cus_id column to billing_cycles if not present
ALTER TABLE `billing_cycles` ADD COLUMN IF NOT EXISTS `cus_id` VARCHAR(10) NULL AFTER `prj_id`;

-- Allow password_hash in customer_accounts to be NULL before invite activation
ALTER TABLE `customer_accounts` MODIFY COLUMN `password_hash` VARCHAR(255) NULL;

-- Add invite_token column to customer_accounts if not present
ALTER TABLE `customer_accounts` ADD COLUMN IF NOT EXISTS `invite_token` VARCHAR(64) NULL AFTER `password_hash`;
