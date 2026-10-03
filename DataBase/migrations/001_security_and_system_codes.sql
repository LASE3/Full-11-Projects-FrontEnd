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

-- 6. Core CRM & Projects Column Definitions (Ensures schema is complete for all baseline operations)
ALTER TABLE `opportunities` MODIFY COLUMN `stage` varchar(50) NOT NULL DEFAULT 'Qualification';
ALTER TABLE `opportunities` ADD COLUMN IF NOT EXISTS `opp_title` varchar(255) DEFAULT NULL;
ALTER TABLE `opportunities` ADD COLUMN IF NOT EXISTS `probability_percent` int(11) DEFAULT 40;
ALTER TABLE `opportunities` ADD COLUMN IF NOT EXISTS `is_confidential` tinyint(1) DEFAULT 0;
ALTER TABLE `opportunities` ADD COLUMN IF NOT EXISTS `priority` varchar(20) DEFAULT 'high';

ALTER TABLE `customers` ADD COLUMN IF NOT EXISTS `phone` varchar(50) DEFAULT NULL;
ALTER TABLE `customers` ADD COLUMN IF NOT EXISTS `headquarters` varchar(255) DEFAULT NULL;
ALTER TABLE `customers` ADD COLUMN IF NOT EXISTS `tax_id` varchar(50) DEFAULT NULL;
ALTER TABLE `customers` ADD COLUMN IF NOT EXISTS `health_score` int(11) DEFAULT 95;
ALTER TABLE `customers` ADD COLUMN IF NOT EXISTS `account_tier` varchar(50) DEFAULT 'Tier-1 Enterprise';
ALTER TABLE `customers` ADD COLUMN IF NOT EXISTS `status` varchar(50) DEFAULT 'Active';

ALTER TABLE `leads` ADD COLUMN IF NOT EXISTS `estimated_value` decimal(14,2) DEFAULT NULL;
ALTER TABLE `leads` ADD COLUMN IF NOT EXISTS `lead_score` int(11) DEFAULT 80;
ALTER TABLE `leads` ADD COLUMN IF NOT EXISTS `equipment_scope` varchar(255) DEFAULT NULL;
ALTER TABLE `leads` ADD COLUMN IF NOT EXISTS `priority` varchar(50) DEFAULT 'High';

ALTER TABLE `contracts` ADD COLUMN IF NOT EXISTS `contract_ref` varchar(50) DEFAULT NULL;
ALTER TABLE `contracts` ADD COLUMN IF NOT EXISTS `title` varchar(255) DEFAULT NULL;
ALTER TABLE `contracts` ADD COLUMN IF NOT EXISTS `contract_type` varchar(50) DEFAULT 'MSA';
ALTER TABLE `contracts` ADD COLUMN IF NOT EXISTS `eds_status` varchar(50) DEFAULT 'Counter-Signed';
ALTER TABLE `contracts` ADD COLUMN IF NOT EXISTS `confidentiality_level` varchar(50) DEFAULT 'Restricted';

ALTER TABLE `quotes` ADD COLUMN IF NOT EXISTS `quote_ref` varchar(50) DEFAULT NULL;
ALTER TABLE `quotes` ADD COLUMN IF NOT EXISTS `equipment_scope` varchar(255) DEFAULT NULL;
ALTER TABLE `quotes` ADD COLUMN IF NOT EXISTS `valid_until` date DEFAULT NULL;
ALTER TABLE `quotes` ADD COLUMN IF NOT EXISTS `total_amount` decimal(14,2) DEFAULT NULL;
ALTER TABLE `quotes` ADD COLUMN IF NOT EXISTS `status` varchar(50) DEFAULT 'Delivered';

ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `start_date` date DEFAULT NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `end_date` date DEFAULT NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `progress_percent` int(11) DEFAULT 0;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `facility_location` varchar(255) DEFAULT NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `scope_summary` text DEFAULT NULL;

-- 7. Ensure crm_activities table exists
CREATE TABLE IF NOT EXISTS `crm_activities` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_type` varchar(50) NOT NULL DEFAULT 'Note',
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `cus_id` varchar(10) DEFAULT NULL,
  `opp_id` varchar(15) DEFAULT NULL,
  `emp_id` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`activity_id`),
  KEY `idx_crm_act_cus` (`cus_id`),
  KEY `idx_crm_act_emp` (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Notifications, tickets, and SLA column alignment
ALTER TABLE `portal_notifications` ADD COLUMN IF NOT EXISTS `system_code` VARCHAR(20) DEFAULT 'ALL' AFTER `portal_user_id`;
ALTER TABLE `portal_notifications` ADD COLUMN IF NOT EXISTS `title` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `portal_notifications` ADD COLUMN IF NOT EXISTS `severity` VARCHAR(20) DEFAULT 'info';
ALTER TABLE `tickets` ADD COLUMN IF NOT EXISTS `resolution_time_minutes` INT NULL;
ALTER TABLE `sla_policies` ADD COLUMN IF NOT EXISTS `description` VARCHAR(255) NULL;
ALTER TABLE `sla_policies` ADD COLUMN IF NOT EXISTS `first_response_time_minutes` INT(11) NULL;
ALTER TABLE `sla_policies` ADD COLUMN IF NOT EXISTS `resolution_time_minutes` INT(11) NULL;
ALTER TABLE `sla_policies` ADD COLUMN IF NOT EXISTS `escalation_threshold_minutes` INT(11) NULL;
UPDATE `sla_policies` SET 
  `first_response_time_minutes` = COALESCE(`first_response_time_minutes`, `response_time_hours` * 60, 60),
  `resolution_time_minutes` = COALESCE(`resolution_time_minutes`, `resolution_time_hours` * 60, 120),
  `escalation_threshold_minutes` = COALESCE(`escalation_threshold_minutes`, `response_time_hours` * 30, 30)
WHERE `first_response_time_minutes` IS NULL OR `resolution_time_minutes` IS NULL;
ALTER TABLE `ticket_escalations` ADD COLUMN IF NOT EXISTS `status` ENUM('Pending','Acknowledged','Resolved','Escalated') NOT NULL DEFAULT 'Escalated';



