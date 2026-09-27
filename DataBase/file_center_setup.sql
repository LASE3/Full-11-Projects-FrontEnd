-- =====================================================================
-- VOSTOKPRIBOR FILE CENTER (SYS-09) - COMPLETE DATABASE SCHEMA & DATA
-- Database: `vostokpribor`
-- Compatible with MySQL 5.7+, MySQL 8.0+, MariaDB 10.3+, and phpMyAdmin
-- Paste and execute this entire script in phpMyAdmin -> SQL tab
-- =====================================================================

USE `vostokpribor`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Ensure `documents` table has all necessary metadata columns
SET @dbname = DATABASE();

-- Add `description` column if missing
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'documents' AND COLUMN_NAME = 'description') > 0,
  "SELECT 1",
  "ALTER TABLE `documents` ADD COLUMN `description` TEXT NULL AFTER `file_name`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add `folder` column if missing
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'documents' AND COLUMN_NAME = 'folder') > 0,
  "SELECT 1",
  "ALTER TABLE `documents` ADD COLUMN `folder` VARCHAR(50) NOT NULL DEFAULT 'projects' AFTER `classification`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add `department` column if missing
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'documents' AND COLUMN_NAME = 'department') > 0,
  "SELECT 1",
  "ALTER TABLE `documents` ADD COLUMN `department` VARCHAR(10) NOT NULL DEFAULT 'ENG' AFTER `folder`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add `file_size` column if missing
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'documents' AND COLUMN_NAME = 'file_size') > 0,
  "SELECT 1",
  "ALTER TABLE `documents` ADD COLUMN `file_size` VARCHAR(20) NOT NULL DEFAULT '2.0 MB' AFTER `department`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add `file_hash` column if missing
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'documents' AND COLUMN_NAME = 'file_hash') > 0,
  "SELECT 1",
  "ALTER TABLE `documents` ADD COLUMN `file_hash` VARCHAR(64) NULL AFTER `file_size`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add `status` column if missing
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'documents' AND COLUMN_NAME = 'status') > 0,
  "SELECT 1",
  "ALTER TABLE `documents` ADD COLUMN `status` VARCHAR(30) NOT NULL DEFAULT 'Approved' AFTER `file_hash`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add `retention_period` column if missing
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'documents' AND COLUMN_NAME = 'retention_period') > 0,
  "SELECT 1",
  "ALTER TABLE `documents` ADD COLUMN `retention_period` VARCHAR(20) NOT NULL DEFAULT '7y' AFTER `status`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add `project_ref` column if missing
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'documents' AND COLUMN_NAME = 'project_ref') > 0,
  "SELECT 1",
  "ALTER TABLE `documents` ADD COLUMN `project_ref` VARCHAR(150) NULL AFTER `retention_period`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add `customer_ref` column if missing
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'documents' AND COLUMN_NAME = 'customer_ref') > 0,
  "SELECT 1",
  "ALTER TABLE `documents` ADD COLUMN `customer_ref` VARCHAR(150) NULL AFTER `project_ref`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add `is_legal_hold` column if missing
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'documents' AND COLUMN_NAME = 'is_legal_hold') > 0,
  "SELECT 1",
  "ALTER TABLE `documents` ADD COLUMN `is_legal_hold` TINYINT(1) NOT NULL DEFAULT 0 AFTER `customer_ref`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add `legal_hold_by_emp_id` column if missing
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'documents' AND COLUMN_NAME = 'legal_hold_by_emp_id') > 0,
  "SELECT 1",
  "ALTER TABLE `documents` ADD COLUMN `legal_hold_by_emp_id` VARCHAR(10) NULL AFTER `is_legal_hold`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add `legal_hold_date` column if missing
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'documents' AND COLUMN_NAME = 'legal_hold_date') > 0,
  "SELECT 1",
  "ALTER TABLE `documents` ADD COLUMN `legal_hold_date` DATETIME NULL AFTER `legal_hold_by_emp_id`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add `legal_hold_reason` column if missing
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'documents' AND COLUMN_NAME = 'legal_hold_reason') > 0,
  "SELECT 1",
  "ALTER TABLE `documents` ADD COLUMN `legal_hold_reason` TEXT NULL AFTER `legal_hold_date`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add `updated_at` column if missing
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'documents' AND COLUMN_NAME = 'updated_at') > 0,
  "SELECT 1",
  "ALTER TABLE `documents` ADD COLUMN `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;


-- 2. Modify `document_access_log` for full audit trails
ALTER TABLE `document_access_log` MODIFY COLUMN `access_type` VARCHAR(50) NOT NULL;

-- Add `notes` to `document_access_log` if missing
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'document_access_log' AND COLUMN_NAME = 'notes') > 0,
  "SELECT 1",
  "ALTER TABLE `document_access_log` ADD COLUMN `notes` TEXT NULL AFTER `access_type`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;


-- 3. Enhance `document_approvals`
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'document_approvals' AND COLUMN_NAME = 'stage') > 0,
  "SELECT 1",
  "ALTER TABLE `document_approvals` ADD COLUMN `stage` INT(11) NOT NULL DEFAULT 2 AFTER `reviewer_emp_id`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'document_approvals' AND COLUMN_NAME = 'stage_name') > 0,
  "SELECT 1",
  "ALTER TABLE `document_approvals` ADD COLUMN `stage_name` VARCHAR(100) NOT NULL DEFAULT 'Project Manager Signoff' AFTER `stage`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'document_approvals' AND COLUMN_NAME = 'token') > 0,
  "SELECT 1",
  "ALTER TABLE `document_approvals` ADD COLUMN `token` VARCHAR(100) NULL AFTER `stage_name`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'document_approvals' AND COLUMN_NAME = 'comments') > 0,
  "SELECT 1",
  "ALTER TABLE `document_approvals` ADD COLUMN `comments` TEXT NULL AFTER `token`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;


-- 4. Create `document_retention_policies` Table
CREATE TABLE IF NOT EXISTS `document_retention_policies` (
  `policy_id` INT(11) NOT NULL AUTO_INCREMENT,
  `category` VARCHAR(200) NOT NULL,
  `target_docs` VARCHAR(200) DEFAULT NULL,
  `retention_scope` VARCHAR(50) NOT NULL,
  `legal_anchor` VARCHAR(200) NOT NULL,
  `disposition_action` TEXT NOT NULL,
  `compliance_tier` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`policy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 5. Seed / Update All 15 Core Documents with Authoritative Metadata
INSERT INTO `documents` (
  `doc_id`, `file_name`, `description`, `classification`, `folder`, `department`,
  `owning_system`, `owner_emp_id`, `related_prj_id`, `related_cus_id`,
  `project_ref`, `customer_ref`,
  `file_size`, `file_hash`, `status`, `retention_period`, `is_legal_hold`,
  `legal_hold_by_emp_id`, `legal_hold_date`, `legal_hold_reason`, `created_at`
) VALUES
('DOC-2026-001', 'Corporate_Information_Security_Policy.pdf', 'Master enterprise cybersecurity charter & access policy', 'TopSecret', 'governance', 'EXE', 'Admin & Governance', 'EMP-1005', NULL, NULL, 'PRJ-GOV-2026', 'Internal Corporate', '3.8 MB', 'a89f30b9148d423985bf4f481c81c4e97a5b3992b1cf5600ea8b1990c681ea88', 'Approved', 'permanent', 0, NULL, NULL, NULL, '2026-01-15 09:00:00'),
('DOC-2026-002', 'Customer_Onboarding_Standard.pdf', 'Commercial account vetting protocol & KYC', 'Confidential', 'contracts', 'SAL', 'CRM', 'EMP-1006', NULL, NULL, 'COMM-STD-2026', 'Commercial Accounts', '1.6 MB', '7b2a9e334f590bb821034f828a1c89283e7428fb17c1817e81037894a8217e92', 'Approved', '7y', 0, NULL, NULL, NULL, '2026-02-01 10:30:00'),
('DOC-2026-003', 'PRJ-2026-001_Statement_of_Work.pdf', 'Aral Geomatics Group • Optical Sensor Integration SOW', 'Confidential', 'projects', 'ENG', 'File Center', 'EMP-1019', 'PRJ-2026-001', 'CUS-1001', 'PRJ-2026-001 (Aral Geomatics)', 'CUS-1001 (Aral Geomatics Group)', '2.1 MB', '4e1a8b928172c3d4e5f60718293a4b5c6d7e8f90123456789abcdef012345678', 'Approved', '7y', 0, NULL, NULL, NULL, '2026-02-14 14:15:00'),
('DOC-2026-004', 'PRJ-2026-002_Integration_Specification.pdf', 'BaltNord SCADA Ingestion • Reviewed by Farida Iskakova', 'TopSecret', 'projects', 'ENG', 'File Center', 'EMP-1019', 'PRJ-2026-002', 'CUS-1002', 'PRJ-2026-002 (BaltNord Process Systems)', 'CUS-1002 (BaltNord Process Systems)', '4.5 MB', '9f8e7d6c5b4a3928170192837465abcdeffedcba98765432101234567890fedc', 'In Review', '10y', 1, 'EMP-1019', '2026-09-10 14:12:00', 'BaltNord Process Systems SCADA bridge specification', '2026-09-11 11:00:00'),
('DOC-2026-005', 'INV-2026-002_Billing_Record.pdf', 'BaltNord Milestone 1 billing attestation (€120,000)', 'Confidential', 'finance', 'FIN', 'Finance', 'EMP-1003', 'PRJ-2026-002', 'CUS-1002', 'PRJ-2026-002', 'CUS-1002 (BaltNord Process Systems)', '890 KB', '1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef', 'Approved', '7y', 0, NULL, NULL, NULL, '2026-03-01 16:45:00'),
('DOC-2026-006', 'Employee_Onboarding_Procedure.pdf', 'Standard Operating Procedure • SOP-05 HR Enrollment', 'Confidential', 'hr', 'HR', 'HR', 'EMP-1013', NULL, NULL, 'HR-SOP-2026', 'Internal HR', '1.2 MB', 'abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890', 'Approved', '5y', 0, NULL, NULL, NULL, '2026-01-10 08:30:00'),
('DOC-2026-007', 'Employee_Access_Matrix.xlsx', 'Complete RBAC and clearance register for 95 employees', 'TopSecret', 'governance', 'EXE', 'Admin & Governance', 'EMP-1005', NULL, NULL, 'SEC-AUDIT-2026', 'Internal Security Audit', '1.9 MB', 'deadbeef1029384756abcdef0192837465bcaefd1234567890fedcba98765432', 'Approved', 'permanent', 1, 'EMP-1005', '2026-09-01 09:00:00', 'Statutory access permissions • Annual external audit', '2026-09-01 09:00:00'),
('DOC-2026-008', 'Supplier_Evaluation_2026.pdf', 'Tier-1 industrial transducer vendor scorecard', 'Confidential', 'operations', 'OPS', 'Operations', 'EMP-1011', NULL, NULL, 'OPS-SUP-2026', 'Supply Chain Vendors', '2.8 MB', '9876543210fedcba9876543210fedcba9876543210fedcba9876543210fedcba', 'Approved', '5y', 0, NULL, NULL, NULL, '2026-02-28 11:20:00'),
('DOC-2026-009', 'Optical_Sensor_Product_Catalog.pdf', 'Standard B2B product specifications • Public distribution', 'Public', 'operations', 'SAL', 'E-Commerce', 'EMP-1006', NULL, NULL, 'PUB-CAT-2026', 'Public Industrial B2B', '14.2 MB', '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef', 'Approved', '3y', 0, NULL, NULL, NULL, '2026-01-05 13:00:00'),
('DOC-2026-010', 'API_Integration_Guide.pdf', 'REST & gRPC endpoints protocol for partner systems', 'Internal', 'projects', 'ENG', 'Developer Portal', 'EMP-1017', NULL, NULL, 'DEV-GATEWAY-v4', 'Developer Partners', '3.1 MB', '554433221100aabbccddeeff99887766554433221100aabbccddeeff99887766', 'Approved', '3y', 0, NULL, NULL, NULL, '2026-03-12 15:10:00'),
('DOC-2026-011', 'Disaster_Recovery_Plan.pdf', 'Cold site failover & Almaty datastore replication runbook', 'TopSecret', 'governance', 'EXE', 'IT Helpdesk', 'EMP-1004', NULL, NULL, 'BCP-DR-2026', 'IT Operations', '5.2 MB', 'feefeeddccbbaa99887766554433221100feefeeddccbbaa9988776655443322', 'Approved', 'permanent', 0, NULL, NULL, NULL, '2026-02-18 10:00:00'),
('DOC-2026-012', 'Annual_Corporate_Budget_2026.xlsx', 'Capital allocation • Executive board authorization only', 'TopSecret', 'finance', 'FIN', 'Finance', 'EMP-1003', NULL, NULL, 'CORP-FIN-2026', 'Corporate Board', '4.1 MB', '99887766554433221100feefeeddccbbaa99887766554433221100feefeeddcc', 'Approved', '7y', 0, NULL, NULL, NULL, '2026-01-02 09:30:00'),
('DOC-2026-013', 'Customer_Service_Handbook.pdf', 'Operational guidelines for regional account liaisons', 'Internal', 'hr', 'HR', 'Intranet', 'EMP-1004', NULL, NULL, 'INT-TRAIN-2026', 'Internal Support', '2.4 MB', '11223344556677889900aabbccddeeff11223344556677889900aabbccddeeff', 'Approved', '5y', 0, NULL, NULL, NULL, '2026-02-10 14:00:00'),
('DOC-2026-014', 'PRJ-2026-007_Test_Report.pdf', 'Seismic Vibration Array • Acceptance Testing Certificate', 'Confidential', 'projects', 'ENG', 'File Center', 'EMP-1019', 'PRJ-2026-007', 'CUS-1007', 'PRJ-2026-007 (PetroKaz / Caspian Robotics)', 'CUS-1007 (Caspian Industrial Robotics)', '3.6 MB', '3344556677889900aabbccddeeff11223344556677889900aabbccddeeff1122', 'Approved', '10y', 0, NULL, NULL, NULL, '2026-08-20 17:00:00'),
('DOC-2026-015', 'Board_Risk_Register_2026.xlsx', 'Statutory enterprise risk matrix • Board of Directors', 'TopSecret', 'governance', 'EXE', 'Admin & Governance', 'EMP-1005', NULL, NULL, 'BOARD-RISK-2026', 'Board of Directors', '2.7 MB', 'bbccddeeff00112233445566778899aabbccddeeff00112233445566778899aa', 'Approved', 'permanent', 1, 'EMP-1001', '2026-09-05 11:30:00', 'Board of Directors quarterly risk disclosures', '2026-09-05 11:30:00')
ON DUPLICATE KEY UPDATE
  `file_name` = VALUES(`file_name`),
  `description` = VALUES(`description`),
  `classification` = VALUES(`classification`),
  `folder` = VALUES(`folder`),
  `department` = VALUES(`department`),
  `owning_system` = VALUES(`owning_system`),
  `owner_emp_id` = VALUES(`owner_emp_id`),
  `related_prj_id` = VALUES(`related_prj_id`),
  `related_cus_id` = VALUES(`related_cus_id`),
  `project_ref` = VALUES(`project_ref`),
  `customer_ref` = VALUES(`customer_ref`),
  `file_size` = VALUES(`file_size`),
  `file_hash` = VALUES(`file_hash`),
  `status` = VALUES(`status`),
  `retention_period` = VALUES(`retention_period`),
  `is_legal_hold` = VALUES(`is_legal_hold`),
  `legal_hold_by_emp_id` = VALUES(`legal_hold_by_emp_id`),
  `legal_hold_date` = VALUES(`legal_hold_date`),
  `legal_hold_reason` = VALUES(`legal_hold_reason`);


-- 6. Seed Retention Policies
TRUNCATE TABLE `document_retention_policies`;
INSERT INTO `document_retention_policies` 
(`policy_id`, `category`, `target_docs`, `retention_scope`, `legal_anchor`, `disposition_action`, `compliance_tier`) VALUES
(1, 'Corporate Charter & Board Registers', 'DOC-2026-001, DOC-2026-015', 'Permanent', 'Kazakhstan Corporate Law §14', 'WORM Immutable Archive • Zero deletion permitted', 'Highly Conf.'),
(2, 'SCADA Engineering & Blueprints', 'DOC-2026-004, DOC-2026-014', '10 Years', 'IEC 62443-4-2 §7.3', 'Transition to Cold Archive after project closeout', 'Confidential'),
(3, 'Commercial Contracts & Billing Invoices', 'DOC-2026-003, DOC-2026-005, DOC-2026-012', '7 Years', 'Kazakhstan Tax Code §48', 'Archive to Nearline • Sealed against alteration', 'Confidential'),
(4, 'HR Onboarding & Personnel Records', 'DOC-2026-006, DOC-2026-013', '5 Years', 'Kazakhstan Labor Code §63', 'Automated purge after statutory expiration', 'Internal');


-- 7. Seed Document Approvals Workflow
DELETE FROM `document_approvals` WHERE `doc_id` IN ('DOC-2026-001', 'DOC-2026-002', 'DOC-2026-004', 'DOC-2026-005');
INSERT INTO `document_approvals` 
(`approval_id`, `doc_id`, `reviewer_emp_id`, `stage`, `stage_name`, `token`, `comments`, `decision`, `decision_date`) VALUES
(1, 'DOC-2026-001', 'EMP-1001', 4, 'Governance Clearance', 'SIG-ED25519-VP-1001-2026-01-15', 'Executive board authorization completed.', 'Approved', '2026-01-15 13:00:00'),
(2, 'DOC-2026-002', 'EMP-1005', 4, 'Governance Clearance', 'SIG-ED25519-VP-1005-2026-02-02', 'Vetted and verified.', 'Approved', '2026-02-02 08:30:00'),
(3, 'DOC-2026-004', 'EMP-1019', 2, 'Project Manager Signoff', 'SIG-ED25519-VP-9021884-2026-09-11', 'Awaiting Senior PM review and digital signature.', 'Pending', '2026-09-20 06:00:00'),
(4, 'DOC-2026-005', 'EMP-1001', 4, 'Governance Clearance', 'SIG-ED25519-VP-1001-2026-01-02', 'Approved milestone billing.', 'Approved', '2026-01-02 07:00:00');


-- 8. Seed Initial Audit Logs in `document_access_log`
DELETE FROM `document_access_log` WHERE `access_id` >= 10;
INSERT INTO `document_access_log` 
(`access_id`, `doc_id`, `accessed_by_emp_id`, `accessed_by_cus_id`, `system_id`, `source_ip`, `access_type`, `notes`, `success`, `accessed_at`) VALUES
(10, 'DOC-2026-004', 'EMP-1019', NULL, 'DOC', '10.240.0.12', 'SIGN_REVIEW', 'Opened redaction inspection for BaltNord PRJ-2026-002 specification', 1, '2026-09-11 17:15:22'),
(11, 'DOC-2026-010', 'EMP-1017', NULL, 'DEV', '10.240.1.44', 'View', 'Accessed API Integration Guide for developer gateway synchronization', 1, '2026-09-11 16:42:01'),
(12, 'DOC-2026-003', 'EMP-1010', NULL, 'SAL', '10.240.0.89', 'EXPORT', 'Exported customer copy of Aral Geomatics Statement of Work', 1, '2026-09-11 14:10:44'),
(13, 'DOC-2026-007', 'EMP-1005', NULL, 'ADM', '10.240.0.1', 'LEGAL_HOLD', 'Applied statutory audit preservation lock on Employee Access Matrix', 1, '2026-09-11 11:20:18'),
(14, 'DOC-2026-004', 'EMP-1017', NULL, 'DOC', '10.240.2.15', 'INGEST_DRAFT', 'Uploaded initial revision of PRJ-2026-002_Integration_Specification.pdf', 1, '2026-09-10 14:12:05');

SET FOREIGN_KEY_CHECKS = 1;
