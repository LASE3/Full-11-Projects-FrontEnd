-- ==============================================================================
-- VOSTOKPRIBOR SYSTEM 11 // GOV-CORE
-- ADMINISTRATION & GOVERNANCE PORTAL SCHEMA & SEED SCRIPT
-- Compatible with MySQL 5.7+, MariaDB 10.3+, and phpMyAdmin
-- Database: vostokpribor
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `vostokpribor` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `vostokpribor`;

-- ==============================================================================
-- 1. SECURITY POLICIES TABLE (security_policies)
-- ==============================================================================
CREATE TABLE IF NOT EXISTS `security_policies` (
    `policy_id` INT AUTO_INCREMENT PRIMARY KEY,
    `doc_id` VARCHAR(15) NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `effective_date` DATE DEFAULT NULL,
    `severity` VARCHAR(20) DEFAULT 'High',
    `enforcement_mode` VARCHAR(30) DEFAULT 'MANDATORY',
    `description` TEXT,
    `system_id` VARCHAR(50) DEFAULT 'SYS-01..11',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_doc_id` (`doc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ensure all required columns exist if the table was previously created
ALTER TABLE `security_policies` ADD COLUMN IF NOT EXISTS `doc_id` VARCHAR(15) NOT NULL;
ALTER TABLE `security_policies` ADD COLUMN IF NOT EXISTS `title` VARCHAR(200) NOT NULL;
ALTER TABLE `security_policies` ADD COLUMN IF NOT EXISTS `effective_date` DATE DEFAULT NULL;
ALTER TABLE `security_policies` ADD COLUMN IF NOT EXISTS `severity` VARCHAR(20) DEFAULT 'High';
ALTER TABLE `security_policies` ADD COLUMN IF NOT EXISTS `enforcement_mode` VARCHAR(30) DEFAULT 'MANDATORY';
ALTER TABLE `security_policies` ADD COLUMN IF NOT EXISTS `description` TEXT;
ALTER TABLE `security_policies` ADD COLUMN IF NOT EXISTS `system_id` VARCHAR(50) DEFAULT 'SYS-01..11';

-- Populate Enterprise Security Policies (if not already seeded)
INSERT IGNORE INTO `security_policies` (`policy_id`, `doc_id`, `title`, `effective_date`, `severity`, `enforcement_mode`, `description`, `system_id`) VALUES
(1, 'DOC-2026-001', 'Zero-Trust Telemetry Relay Ingestion Boundary', '2026-01-10', 'Critical', 'ACTIVE', 'Mandates cryptographic verification of all incoming edge telemetry via mutual TLS and TPM chip attestation.', 'SYS-11'),
(2, 'DOC-2026-002', 'Dual-Custody Break-Glass Emergency Execution', '2026-01-15', 'Critical', 'ACTIVE', 'Requires two independent level-5 hardware tokens to override system air-gap or initiate emergency purge procedures.', 'SYS-11'),
(3, 'DOC-2026-003', 'Orphaned Access Token Revocation SLA (4 Hours)', '2026-02-01', 'High', 'ACTIVE', 'Strict requirement that inactive or revoked operator credentials be purged from memory rings within 4 hours of status change.', 'SYS-02'),
(4, 'DOC-2026-004', 'Automated 90-Day SCADA Cryptographic Key Rotation', '2026-02-10', 'High', 'ACTIVE', 'Automated rotation pipeline for PLC, RTU, and field gateway symmetrical credentials with fallback fail-safe lock.', 'SYS-08'),
(5, 'DOC-2026-005', 'High-Frequency OT Ring-Buffer Write-Once Immutable Retention', '2026-02-15', 'Medium', 'ACTIVE', 'All raw operational event telemetry must be committed to tamper-evident WORM disk banks before execution queues.', 'SYS-11'),
(6, 'DOC-2026-006', 'Defcon Enclave Galvanic Isolation Interlock', '2026-02-20', 'Critical', 'ENFORCED', 'Physical galvanic disconnection protocols for substation and furnace actuators during industrial emergency states.', 'SYS-05'),
(7, 'DOC-2026-007', 'Statutory FIPS 140-3 Hardware Key Attestation', '2026-03-01', 'High', 'ACTIVE', 'All privileged administrative console interactions require compliant physical HSM tokens with biometric pin challenge.', 'SYS-10'),
(8, 'DOC-2026-008', 'SCADA Network Unidirectional Diode Boundary Verification', '2026-03-05', 'Critical', 'ACTIVE', 'Physical hardware data diodes must ensure strictly one-way data egress from operational safety rings.', 'SYS-08'),
(9, 'DOC-2026-009', 'Continuous Merkle Root Proof Sovereign State Uplink', '2026-03-10', 'Medium', 'ACTIVE', 'Periodic anchoring of tamper-evident Merkle hash roots with KZ-CERT national cybersecurity registry.', 'SYS-11'),
(10, 'DOC-2026-010', 'Privileged Keystroke & Shadow Session Forensic Mirroring', '2026-03-15', 'Medium', 'ACTIVE', 'Full session recording and optical OCR audit capture for all Tier 0/1 interactive shells across bastions.', 'SYS-10');

-- ==============================================================================
-- 2. BOARD RISK REGISTER TABLE (risk_register)
-- ==============================================================================
CREATE TABLE IF NOT EXISTS `risk_register` (
    `risk_id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NULL,
    `threat_vector` TEXT NULL,
    `system_target` VARCHAR(100) DEFAULT 'SYS-01 Production Enclave',
    `description` TEXT,
    `likelihood` VARCHAR(20) DEFAULT 'Moderate',
    `impact` VARCHAR(20) DEFAULT 'High',
    `owner_emp_id` VARCHAR(10) DEFAULT 'EMP-1005',
    `status` VARCHAR(30) DEFAULT 'Mitigating',
    `review_date` DATE DEFAULT NULL,
    KEY `idx_owner` (`owner_emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `risk_register` ADD COLUMN IF NOT EXISTS `title` VARCHAR(255) NULL;
ALTER TABLE `risk_register` ADD COLUMN IF NOT EXISTS `threat_vector` TEXT NULL;
ALTER TABLE `risk_register` ADD COLUMN IF NOT EXISTS `system_target` VARCHAR(100) DEFAULT 'SYS-01 Production Enclave';
ALTER TABLE `risk_register` ADD COLUMN IF NOT EXISTS `description` TEXT;
ALTER TABLE `risk_register` ADD COLUMN IF NOT EXISTS `likelihood` VARCHAR(20) DEFAULT 'Moderate';
ALTER TABLE `risk_register` ADD COLUMN IF NOT EXISTS `impact` VARCHAR(20) DEFAULT 'High';
ALTER TABLE `risk_register` ADD COLUMN IF NOT EXISTS `owner_emp_id` VARCHAR(10) DEFAULT 'EMP-1005';
ALTER TABLE `risk_register` ADD COLUMN IF NOT EXISTS `status` VARCHAR(30) DEFAULT 'Mitigating';
ALTER TABLE `risk_register` ADD COLUMN IF NOT EXISTS `review_date` DATE DEFAULT NULL;

-- Populate Board Risk Register Seed Data (if empty)
INSERT IGNORE INTO `risk_register` (`risk_id`, `title`, `threat_vector`, `system_target`, `description`, `likelihood`, `impact`, `owner_emp_id`, `status`, `review_date`) VALUES
(1, 'Orphaned Access Token Privilege Escalation', 'Unrevoked active credentials from terminated contractor personnel', 'SYS-02 HRIS & SYS-11 Gov-Core', 'Contractor Maksim Sokolov account was terminated in HRIS but credential tokens remain valid in telemetry ingress gateway.', 'Moderate', 'Critical', 'EMP-1042', 'Mitigating', '2026-04-15'),
(2, 'Smelting Furnace Telemetry Desynchronization', 'Sensor spoofing or packet drop on Modbus TCP telemetry', 'SYS-01 Smelting & Heavy Foundry', 'Intermittent packet loss on telemetry line 4 induces delayed emergency cutoff trigger for blast furnace arc.', 'Low', 'Critical', 'EMP-1088', 'Mitigating', '2026-04-20'),
(3, 'Substation 110kV SCADA Relay Manipulation', 'Unauthorized control sequence injection into power grid', 'SYS-05 110kV Substation Grid', 'Remote substation interconnect lacks secondary physical lockout key, exposing grid actuators to lateral compromise.', 'Low', 'Critical', 'EMP-1005', 'Mitigating', '2026-05-01'),
(4, 'Cryptographic Root Key Compromise', 'Physical theft or unauthorized extraction of HSM master key', 'SYS-11 Gov-Core & Security Vault', 'Potential cryptographic root exposure during off-site backup synchronizations without Shamir 3-of-5 split quorum.', 'Low', 'Critical', 'EMP-1005', 'Mitigating', '2026-05-15'),
(5, 'ASRS Crane Ingestion Buffer Overflow', 'Denial of service via massive sensor telemetry flood', 'SYS-07 Automated Storage (ASRS)', 'High-bay crane telemetry buffer lacks local ring-buffer rate-limiting, risking logistics stall during batch processing.', 'Moderate', 'Moderate', 'EMP-1088', 'Mitigating', '2026-05-20'),
(6, 'Statutory ST RK 27001 Audit Non-Conformity', 'Failure to provide continuous Merkle chain verification logs', 'All Industrial & Gov Systems', 'State regulatory inspection scheduled for Q3 requires notarized cryptographic ledger verification for all Tier 0 nodes.', 'Low', 'High', 'EMP-1005', 'Mitigating', '2026-06-01');

-- ==============================================================================
-- 3. COMPLIANCE CONTROLS TABLE (compliance_controls)
-- ==============================================================================
CREATE TABLE IF NOT EXISTS `compliance_controls` (
    `control_id` INT AUTO_INCREMENT PRIMARY KEY,
    `control_code` VARCHAR(30) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `system_id` VARCHAR(50) DEFAULT 'SYS-01..10',
    `framework` VARCHAR(50) DEFAULT 'KAZ-CERT DIR-44',
    `custodian_emp_id` VARCHAR(10) DEFAULT 'EMP-1005',
    `status` ENUM('COMPLIANT','DEVIATION','REMEDIATION') DEFAULT 'COMPLIANT',
    `evidence_ref` VARCHAR(50) DEFAULT 'DOC-2026-015',
    `last_reviewed` DATE DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `compliance_controls` (`control_id`, `control_code`, `title`, `description`, `system_id`, `framework`, `custodian_emp_id`, `status`, `evidence_ref`, `last_reviewed`) VALUES
(1, 'CTRL-ISO-9.2', 'Dual-Custody Break-Glass Protocol Enforcement', 'Mandatory quorum authorization for Level 5 cryptographic override across state enclaves.', 'SYS-11 Gov-Core', 'ST RK 27001 §9', 'EMP-1005', 'COMPLIANT', 'DOC-2026-002', '2026-03-20'),
(2, 'CTRL-KAZ-3.1', 'Almaty Grid SCADA Bridge mTLS Rotation', 'Automated 90-day cryptographic cert exchange with regional SCADA and power monitoring nodes.', 'SYS-08 SCADA', 'KAZ-CERT 2025', 'EMP-1088', 'REMEDIATION', 'DOC-2026-004', '2026-03-22'),
(3, 'CTRL-ISO-9.4', 'Orphaned Access Token Revocation SLA', 'Revocation of privileged tokens within 4h of HRIS termination event across corporate active directories.', 'SYS-02 HRIS', 'ST RK 27001 §9.4', 'EMP-1042', 'DEVIATION', 'NC-2026-009', '2026-03-24'),
(4, 'CTRL-CERT-04', 'National Cryptographic Root Key Splitting', 'Shamir secret sharing (3-of-5) of state root attestation key anchored to local hardware security modules.', 'SYS-11 Gov-Core', 'KAZ-CERT Order 145', 'EMP-1005', 'COMPLIANT', 'DOC-2026-007', '2026-03-25'),
(5, 'CTRL-SCADA-01', 'Industrial Network Boundary Segmentation', 'Strict unidirectional data diode air-gap for OT telemetry feeds preventing reverse ingress into safety PLCs.', 'SYS-08 SCADA', 'IEC 62443-3-3', 'EMP-1088', 'COMPLIANT', 'DOC-2026-008', '2026-03-25'),
(6, 'CTRL-SEC-15', 'Privileged Session Recording & Keystroke Logging', 'Tamper-evident recording and optical OCR stream mirroring of all SSH/RDP privileged sessions.', 'SYS-10 Bastion', 'ST RK 27001 §8.2', 'EMP-1042', 'COMPLIANT', 'DOC-2026-010', '2026-03-26');

-- ==============================================================================
-- 4. RE-CERTIFICATION WINDOWS TABLE (recertification_windows)
-- ==============================================================================
CREATE TABLE IF NOT EXISTS `recertification_windows` (
    `window_id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `status` ENUM('Active','Scheduled','Completed') DEFAULT 'Active',
    `scopes` VARCHAR(255) DEFAULT 'ALL',
    `created_by_emp_id` VARCHAR(10) DEFAULT 'EMP-1005',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `recertification_windows` (`window_id`, `title`, `start_date`, `end_date`, `status`, `scopes`, `created_by_emp_id`) VALUES
(1, 'Q2-2026 Statutory Re-Certification Window', '2026-04-01', '2026-04-30', 'Scheduled', 'ALL DEPARTMENTS & INDUSTRIAL NODES', 'EMP-1005'),
(2, 'Q1-2026 Emergency Cryptographic Audit Window', '2026-01-15', '2026-02-15', 'Completed', 'SYS-11 GOV-CORE & HSM NODES', 'EMP-1005');

-- ==============================================================================
-- 5. SYSTEMS CATALOG TABLE (systems_catalog - EMERGENCY DEFCON MATRIX)
-- ==============================================================================
CREATE TABLE IF NOT EXISTS `systems_catalog` (
    `system_id` VARCHAR(4) NOT NULL PRIMARY KEY,
    `system_name` VARCHAR(100) DEFAULT NULL,
    `fqdn` VARCHAR(100) DEFAULT NULL,
    `criticality` VARCHAR(30) DEFAULT 'HIGH',
    `trust_zone` VARCHAR(30) DEFAULT 'PROD',
    `status` VARCHAR(20) NOT NULL DEFAULT 'OPERATIONAL',
    `isolation_reason` VARCHAR(255) DEFAULT NULL,
    `isolated_at` DATETIME DEFAULT NULL,
    `isolated_by` VARCHAR(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `systems_catalog` ADD COLUMN IF NOT EXISTS `status` VARCHAR(20) NOT NULL DEFAULT 'OPERATIONAL';
ALTER TABLE `systems_catalog` ADD COLUMN IF NOT EXISTS `isolation_reason` VARCHAR(255) NULL;
ALTER TABLE `systems_catalog` ADD COLUMN IF NOT EXISTS `isolated_at` DATETIME NULL;
ALTER TABLE `systems_catalog` ADD COLUMN IF NOT EXISTS `isolated_by` VARCHAR(10) NULL;

-- Populate all 11 Industrial Systems
INSERT INTO `systems_catalog` (`system_id`, `system_name`, `criticality`, `trust_zone`, `status`) VALUES
('SYS-01', 'Smelting & Heavy Foundry', 'CRITICAL', 'ZONE-OT-HEAVY', 'OPERATIONAL'),
('SYS-02', 'High-Tonnage Hydraulic Press Complex', 'CRITICAL', 'ZONE-OT-PRESS', 'OPERATIONAL'),
('SYS-03', 'Precision CNC Machining Center', 'HIGH', 'ZONE-OT-CNC', 'OPERATIONAL'),
('SYS-04', 'Robotic Assembly & Ultrasonic Welding', 'HIGH', 'ZONE-OT-ROBOTICS', 'OPERATIONAL'),
('SYS-05', '110kV Substation & Power Distribution', 'CRITICAL', 'ZONE-OT-POWER', 'OPERATIONAL'),
('SYS-06', 'Industrial Water Treatment & Cooling', 'MEDIUM', 'ZONE-OT-UTILITIES', 'OPERATIONAL'),
('SYS-07', 'Automated Storage & High-Bay Logistics (ASRS)', 'MEDIUM', 'ZONE-LOGISTICS', 'OPERATIONAL'),
('SYS-08', 'Enterprise SCADA & Regional Grid Interconnect', 'CRITICAL', 'ZONE-SCADA-WAN', 'OPERATIONAL'),
('SYS-09', 'Environmental Emissions & Scrubbing Array', 'MEDIUM', 'ZONE-ECO-SAFETY', 'OPERATIONAL'),
('SYS-10', 'Perimeter Defense & Physical Security Grid', 'CRITICAL', 'ZONE-PHYS-SEC', 'OPERATIONAL'),
('SYS-11', 'Governance Core, PKI Root & Audit Vault', 'CRITICAL', 'ZONE-GOV-CORE', 'OPERATIONAL')
ON DUPLICATE KEY UPDATE `system_name` = VALUES(`system_name`);

-- ==============================================================================
-- 6. SECURITY INCIDENTS TABLE (security_incidents)
-- ==============================================================================
CREATE TABLE IF NOT EXISTS `security_incidents` (
    `incident_id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT,
    `severity` ENUM('Low','Medium','High','Critical') DEFAULT 'High',
    `status` VARCHAR(30) DEFAULT 'OPEN',
    `reported_by_emp_id` VARCHAR(10) DEFAULT 'EMP-1005',
    `assigned_to_emp_id` VARCHAR(10) DEFAULT 'EMP-1042',
    `opened_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `closed_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `security_incidents` (`incident_id`, `title`, `description`, `severity`, `status`, `reported_by_emp_id`, `assigned_to_emp_id`) VALUES
(1, 'Orphaned Account Key Retained in Telemetry Ingress', 'Maksim Sokolov (Contractor EMP-3044) revoked in HRIS, but active keys retain RW on Sys-03 Telemetry Ingestion Bridge.', 'Critical', 'OPEN', 'EMP-1005', 'EMP-1042'),
(2, 'SCADA mTLS Certificate Expiration Pending', 'Sys-08 Karaganda SCADA Bridge client certificate invalid in 17h 40m. Automatic rotation pipeline stalled at Step 3/4.', 'High', 'OPEN', 'EMP-1005', 'EMP-1088');

-- ==============================================================================
-- 7. ACCESS REVIEWS TABLE (access_reviews)
-- ==============================================================================
CREATE TABLE IF NOT EXISTS `access_reviews` (
    `review_id` INT AUTO_INCREMENT PRIMARY KEY,
    `emp_id` VARCHAR(10) DEFAULT NULL,
    `reviewed_by_emp_id` VARCHAR(10) DEFAULT NULL,
    `review_date` DATE DEFAULT NULL,
    `finding` TEXT,
    `action_taken` TEXT,
    KEY `idx_emp` (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `access_reviews` (`review_id`, `emp_id`, `reviewed_by_emp_id`, `review_date`, `finding`, `action_taken`) VALUES
(1, 'EMP-1002', 'EMP-1005', '2026-03-01', 'Quarterly Privileged Audit: Lead CNC Programmer privileges verified.', 'Attested - Validated'),
(2, 'EMP-1004', 'EMP-1005', '2026-03-05', 'Quarterly Privileged Audit: SecOps Lead permissions confirmed.', 'Attested - Validated'),
(3, 'EMP-1009', 'EMP-1005', '2026-03-10', 'Contractor tenure expired 14 days ago. SSH key lingering in SYS-03.', 'Breach Flagged - Revocation Pending');

-- ==============================================================================
-- 8. AUDIT LOGS TABLE (audit_logs - IMMUTABLE LEDGER)
-- ==============================================================================
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `audit_id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `actor_emp_id` VARCHAR(10) DEFAULT NULL,
    `actor_customer_id` VARCHAR(10) DEFAULT NULL,
    `actor_system` VARCHAR(50) DEFAULT NULL,
    `system_id` VARCHAR(4) DEFAULT 'ADM',
    `action` VARCHAR(200) NOT NULL,
    `target_entity_type` VARCHAR(50) DEFAULT NULL,
    `target_entity_id` VARCHAR(20) DEFAULT NULL,
    `source_ip` VARCHAR(45) DEFAULT NULL,
    `device_id` INT DEFAULT NULL,
    `old_values` LONGTEXT DEFAULT NULL,
    `new_values` LONGTEXT DEFAULT NULL,
    `result` VARCHAR(20) DEFAULT 'SUCCESS',
    `occurred_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- 9. RE-ENABLE CHECKS & VERIFICATION
-- ==============================================================================
SET FOREIGN_KEY_CHECKS = 1;

SELECT 'VOSTOKPRIBOR SYSTEM 11 SCHEMA & SEED VERIFIED' AS `status`,
       (SELECT COUNT(*) FROM security_policies) AS `policies_count`,
       (SELECT COUNT(*) FROM risk_register) AS `risks_count`,
       (SELECT COUNT(*) FROM compliance_controls) AS `compliance_controls_count`,
       (SELECT COUNT(*) FROM recertification_windows) AS `recert_windows_count`,
       (SELECT COUNT(*) FROM systems_catalog) AS `systems_count`,
       (SELECT COUNT(*) FROM security_incidents) AS `incidents_count`;
