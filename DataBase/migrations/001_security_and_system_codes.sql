-- Migration 001: Security Hardening, Clean System Catalog & Canonical FQDNs
-- Idempotent schema and catalog adjustments for VOSTOKPRIBOR core

SET SQL_MODE = '';

-- 1. Ensure user_sessions supports nullable ended_at and jti tracking
ALTER TABLE `user_sessions` MODIFY COLUMN `ended_at` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `user_sessions` ADD COLUMN IF NOT EXISTS `jti` VARCHAR(64) NULL;
ALTER TABLE `user_sessions` ADD INDEX IF NOT EXISTS `idx_user_sessions_jti` (`jti`);

-- 2. Ensure authentication_events supports IP and details tracking for rate-limiting
ALTER TABLE `authentication_events` ADD COLUMN IF NOT EXISTS `ip_address` VARCHAR(45) NULL;
ALTER TABLE `authentication_events` ADD COLUMN IF NOT EXISTS `details` TEXT NULL;

-- 3. Systems Catalog: Remove junk SYS- row and enforce canonical FQDNs and system codes
DELETE FROM `systems_catalog` WHERE `system_id` = 'SYS-' OR `system_id` LIKE 'SYS%';

INSERT INTO `systems_catalog` (`system_id`, `system_name`, `fqdn`, `criticality`, `trust_zone`, `status`) VALUES
('ADM', 'Admin & Governance Portal', 'admin.vostokpribor.local', 'MissionCritical', 'Zone-Alpha', 'OPERATIONAL'),
('CRM', 'CRM System', 'crm.vostokpribor.local', 'High', 'Zone-Bravo', 'OPERATIONAL'),
('CUS', 'Customer Portal', 'portal.vostokpribor.local', 'High', 'Zone-External', 'OPERATIONAL'),
('DEV', 'Developer Portal', 'developer.vostokpribor.local', 'High', 'Zone-Bravo', 'OPERATIONAL'),
('DOC', 'File Center', 'files.vostokpribor.local', 'High', 'Zone-Bravo', 'OPERATIONAL'),
('EMP', 'Employee Intranet', 'intranet.vostokpribor.local', 'Medium', 'Zone-Internal', 'OPERATIONAL'),
('FIN', 'Finance & Billing', 'finance.vostokpribor.local', 'MissionCritical', 'Zone-Alpha', 'OPERATIONAL'),
('HR',  'HR System', 'hr.vostokpribor.local', 'High', 'Zone-Bravo', 'OPERATIONAL'),
('IT',  'IT Helpdesk', 'helpdesk.vostokpribor.local', 'Medium', 'Zone-Internal', 'OPERATIONAL'),
('SHP', 'Online Shop B2B', 'shop.vostokpribor.local', 'High', 'Zone-External', 'OPERATIONAL'),
('WEB', 'Corporate Web Platform', 'www.vostokpribor.local', 'Public', 'Zone-DMZ', 'OPERATIONAL')
ON DUPLICATE KEY UPDATE
    `system_name` = VALUES(`system_name`),
    `fqdn` = VALUES(`fqdn`),
    `criticality` = VALUES(`criticality`),
    `trust_zone` = VALUES(`trust_zone`),
    `status` = VALUES(`status`);

-- 4. Role nomenclature: SuperAdmin role & Customer Client Account role
UPDATE `roles` SET `role_name` = 'SuperAdmin', `description` = 'Executive SuperAdmin' WHERE `role_id` = 1;

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(9, 'Customer Client Account', 'Access to Customer Portal, project tracking, ticket creation, B2B purchasing')
ON DUPLICATE KEY UPDATE `role_name` = 'Customer Client Account';

-- 5. Seed role_system_access for all 11 canonical systems
INSERT INTO `role_system_access` (`role_id`, `system_id`, `access_level`) VALUES
(1, 'ADM', 'Full'),
(1, 'CRM', 'Full'),
(1, 'CUS', 'Full'),
(1, 'DEV', 'Full'),
(1, 'DOC', 'Full'),
(1, 'EMP', 'Full'),
(1, 'FIN', 'Full'),
(1, 'HR',  'Full'),
(1, 'IT',  'Full'),
(1, 'SHP', 'Full'),
(1, 'WEB', 'Full'),
(9, 'CUS', 'Full'),
(9, 'SHP', 'Full'),
(9, 'WEB', 'Public')
ON DUPLICATE KEY UPDATE `access_level` = VALUES(`access_level`);
