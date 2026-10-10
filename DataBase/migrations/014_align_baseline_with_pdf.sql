-- Migration 014: Align baseline data with enterprise PDF specifications and real File Center storage
-- Idempotent: safe to run multiple times.

-- 1. Add storage_path and mime to documents table if not present
ALTER TABLE `documents` ADD COLUMN IF NOT EXISTS `storage_path` VARCHAR(255) NULL AFTER `file_hash`;
ALTER TABLE `documents` ADD COLUMN IF NOT EXISTS `mime` VARCHAR(100) NULL AFTER `storage_path`;

-- 2. Add CUSTOMER_IMPERSONATE permission and bind to SuperAdmin role (role_id 1)
INSERT INTO `permissions` (`permission_id`, `permission_name`, `system_name`)
VALUES (22, 'CUSTOMER_IMPERSONATE', 'Customer Portal')
ON DUPLICATE KEY UPDATE `permission_name` = VALUES(`permission_name`), `system_name` = VALUES(`system_name`);

INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, 22 WHERE NOT EXISTS (SELECT 1 FROM `role_permissions` WHERE `role_id` = 1 AND `permission_id` = 22);

-- 3. Align 20 Baseline Job Titles (EMP-1001 .. EMP-1020)
UPDATE `employees` SET `job_title` = 'Chief Executive Officer (CEO)' WHERE `emp_id` = 'EMP-1001';
UPDATE `employees` SET `job_title` = 'Chief Operating Officer (COO)' WHERE `emp_id` = 'EMP-1002';
UPDATE `employees` SET `job_title` = 'Chief Financial Officer (CFO)' WHERE `emp_id` = 'EMP-1003';
UPDATE `employees` SET `job_title` = 'Chief Technology Officer (CTO)' WHERE `emp_id` = 'EMP-1004';
UPDATE `employees` SET `job_title` = 'Chief Governance Manager' WHERE `emp_id` = 'EMP-1005';
UPDATE `employees` SET `job_title` = 'Sales Manager' WHERE `emp_id` = 'EMP-1006';
UPDATE `employees` SET `job_title` = 'Senior Account Manager' WHERE `emp_id` = 'EMP-1007';
UPDATE `employees` SET `job_title` = 'Account Manager' WHERE `emp_id` = 'EMP-1008';
UPDATE `employees` SET `job_title` = 'Strategic Sales Manager' WHERE `emp_id` = 'EMP-1009';
UPDATE `employees` SET `job_title` = 'Regional Sales Manager' WHERE `emp_id` = 'EMP-1010';
UPDATE `employees` SET `job_title` = 'Operations Manager' WHERE `emp_id` = 'EMP-1011';
UPDATE `employees` SET `job_title` = 'Logistics Manager' WHERE `emp_id` = 'EMP-1012';
UPDATE `employees` SET `job_title` = 'Procurement Manager' WHERE `emp_id` = 'EMP-1013';
UPDATE `employees` SET `job_title` = 'Warehouse Supervisor' WHERE `emp_id` = 'EMP-1014';
UPDATE `employees` SET `job_title` = 'Supply Chain Analyst' WHERE `emp_id` = 'EMP-1015';
UPDATE `employees` SET `job_title` = 'Senior Automation Engineer' WHERE `emp_id` = 'EMP-1016';
UPDATE `employees` SET `job_title` = 'Software Integration Engineer' WHERE `emp_id` = 'EMP-1017';
UPDATE `employees` SET `job_title` = 'Systems Engineer' WHERE `emp_id` = 'EMP-1018';
UPDATE `employees` SET `job_title` = 'Project Manager' WHERE `emp_id` = 'EMP-1019';
UPDATE `employees` SET `job_title` = 'Senior Developer' WHERE `emp_id` = 'EMP-1020';

-- 4. Align 15 Baseline Tickets Assignee & Statuses
-- Per PDF: All 15 assigned to EMP-1018 (Systems Engineer / Helpdesk Owner)
UPDATE `tickets` SET `assigned_emp_id` = 'EMP-1018', `status` = 'In Progress' WHERE `tkt_id` = 'TKT-2026-001';
UPDATE `tickets` SET `assigned_emp_id` = 'EMP-1018', `status` = 'Resolved' WHERE `tkt_id` = 'TKT-2026-002';
UPDATE `tickets` SET `assigned_emp_id` = 'EMP-1018', `status` = 'Investigating' WHERE `tkt_id` = 'TKT-2026-003';
UPDATE `tickets` SET `assigned_emp_id` = 'EMP-1018', `status` = 'Resolved' WHERE `tkt_id` = 'TKT-2026-004';
UPDATE `tickets` SET `assigned_emp_id` = 'EMP-1018', `status` = 'Escalated' WHERE `tkt_id` = 'TKT-2026-005';
UPDATE `tickets` SET `assigned_emp_id` = 'EMP-1018', `status` = 'Resolved' WHERE `tkt_id` = 'TKT-2026-006';
UPDATE `tickets` SET `assigned_emp_id` = 'EMP-1018', `status` = 'Resolved' WHERE `tkt_id` = 'TKT-2026-007';
UPDATE `tickets` SET `assigned_emp_id` = 'EMP-1018', `status` = 'In Progress' WHERE `tkt_id` = 'TKT-2026-008';
UPDATE `tickets` SET `assigned_emp_id` = 'EMP-1018', `status` = 'Resolved' WHERE `tkt_id` = 'TKT-2026-009';
UPDATE `tickets` SET `assigned_emp_id` = 'EMP-1018', `status` = 'Investigating' WHERE `tkt_id` = 'TKT-2026-010';
UPDATE `tickets` SET `assigned_emp_id` = 'EMP-1018', `status` = 'Escalated' WHERE `tkt_id` = 'TKT-2026-011';
UPDATE `tickets` SET `assigned_emp_id` = 'EMP-1018', `status` = 'Resolved' WHERE `tkt_id` = 'TKT-2026-012';
UPDATE `tickets` SET `assigned_emp_id` = 'EMP-1018', `status` = 'Resolved' WHERE `tkt_id` = 'TKT-2026-013';
UPDATE `tickets` SET `assigned_emp_id` = 'EMP-1018', `status` = 'In Progress' WHERE `tkt_id` = 'TKT-2026-014';
UPDATE `tickets` SET `assigned_emp_id` = 'EMP-1018', `status` = 'Investigating' WHERE `tkt_id` = 'TKT-2026-015';

-- 5. Align Products PROD-1006 .. PROD-1010
-- Move previous hardware items to PROD-1011..1015
INSERT INTO `products` (`prod_id`, `product_name`, `billing_model`, `description`, `price`)
SELECT 'PROD-1011', 'High-Temp Thermal Pyrometer', 'PerUnit', 'Non-contact infrared optical pyrometer for blast furnaces up to 1,800C with air-purge collar.', 3500.00
WHERE NOT EXISTS (SELECT 1 FROM `products` WHERE `prod_id` = 'PROD-1011');

INSERT INTO `products` (`prod_id`, `product_name`, `billing_model`, `description`, `price`)
SELECT 'PROD-1012', 'Vibration Analysis Sensor Skid', 'PerUnit', 'Tri-axial piezoelectric accelerometer package for turbine bearings with FFT vibration analytics.', 3500.00
WHERE NOT EXISTS (SELECT 1 FROM `products` WHERE `prod_id` = 'PROD-1012');

INSERT INTO `products` (`prod_id`, `product_name`, `billing_model`, `description`, `price`)
SELECT 'PROD-1013', 'Electromagnetic Flowmeter Array', 'PerUnit', 'High-accuracy conductive fluid flowmeter with Hastelloy electrodes and digital pulse output.', 3500.00
WHERE NOT EXISTS (SELECT 1 FROM `products` WHERE `prod_id` = 'PROD-1013');

INSERT INTO `products` (`prod_id`, `product_name`, `billing_model`, `description`, `price`)
SELECT 'PROD-1014', 'Differential Pressure Transmitter', 'PerUnit', 'HART protocol differential pressure cell for orifice gas flow measurement with 0.05% accuracy.', 3500.00
WHERE NOT EXISTS (SELECT 1 FROM `products` WHERE `prod_id` = 'PROD-1014');

INSERT INTO `products` (`prod_id`, `product_name`, `billing_model`, `description`, `price`)
SELECT 'PROD-1015', 'SCADA Historian & Analytics Suite', 'AnnualContract', 'Enterprise operational data historian license with OPC-UA integration and predictive telemetry.', 3500.00
WHERE NOT EXISTS (SELECT 1 FROM `products` WHERE `prod_id` = 'PROD-1015');

-- Update PROD-1006..1010 to exact PDF catalog
UPDATE `products` SET 
    `product_name` = 'Optical Inspection System', 
    `billing_model` = 'PerProject', 
    `description` = 'Turnkey inline optical automated inspection station for surface defect detection with Modbus integration.',
    `price` = 18500.00
WHERE `prod_id` = 'PROD-1006';

UPDATE `products` SET 
    `product_name` = 'Industrial Lifecycle Support', 
    `billing_model` = 'SubscriptionAnnual', 
    `description` = 'Comprehensive 24/7 telemetry support, preventative replacement cycle, and on-site engineering SLA.',
    `price` = 24000.00
WHERE `prod_id` = 'PROD-1007';

UPDATE `products` SET 
    `product_name` = 'Automation Software Integration', 
    `billing_model` = 'PerProject', 
    `description` = 'Custom PLC, SCADA, and DCS protocol integration and testing for industrial sensor enclaves.',
    `price` = 15000.00
WHERE `prod_id` = 'PROD-1008';

UPDATE `products` SET 
    `product_name` = 'Enterprise Logistics Management', 
    `billing_model` = 'SubscriptionMonthly', 
    `description` = 'Cloud-synchronized warehouse tracking and spare-parts replenishment pipeline automation.',
    `price` = 3200.00
WHERE `prod_id` = 'PROD-1009';

UPDATE `products` SET 
    `product_name` = 'Preventive Instrument Maintenance', 
    `billing_model` = 'AnnualContract', 
    `description` = 'Semi-annual calibration, optical alignment, and sensor health certification contract.',
    `price` = 8900.00
WHERE `prod_id` = 'PROD-1010';
