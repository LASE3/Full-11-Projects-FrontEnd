-- ============================================================================
-- VOSTOKPRIBOR IT HELPDESK & SERVICE DESK SYSTEM (SYS 08)
-- COMPLETE DATABASE SCHEMA & SEED DATA FOR PHPMYADMIN
-- Target Database: vostokpribor
-- Compatible with MySQL 5.7+, MariaDB 10.3+, phpMyAdmin
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `vostokpribor` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `vostokpribor`;

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------------------
-- 1. TICKETS TABLE
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tickets` (
  `tkt_id` varchar(15) NOT NULL,
  `requester_type` enum('Customer','Employee') NOT NULL DEFAULT 'Employee',
  `requester_cus_id` varchar(10) DEFAULT NULL,
  `requester_emp_id` varchar(10) DEFAULT NULL,
  `source_system` varchar(50) NOT NULL DEFAULT 'General IT Support',
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `requester_name` varchar(150) DEFAULT 'Authorized Personnel',
  `requester_role` varchar(150) DEFAULT 'Operations Staff',
  `requester_dept` varchar(100) DEFAULT 'Operations',
  `priority` enum('Low','Medium','High','Critical') NOT NULL DEFAULT 'Medium',
  `assigned_emp_id` varchar(10) DEFAULT 'EMP-1018',
  `status` enum('Open','InProgress','Investigating','Escalated','Resolved') NOT NULL DEFAULT 'Open',
  `resolution_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL,
  `sla_deadline` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`tkt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Safe column additions if tables existed from earlier versions
ALTER TABLE `tickets` MODIFY COLUMN `resolved_at` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `it_assets` ADD COLUMN IF NOT EXISTS `notes` TEXT NULL;
ALTER TABLE `it_assets` ADD COLUMN IF NOT EXISTS `health_status` VARCHAR(50) NOT NULL DEFAULT 'Nominal (Active)';
ALTER TABLE `sla_policies` ADD COLUMN IF NOT EXISTS `description` VARCHAR(255) NULL;
ALTER TABLE `ticket_escalations` ADD COLUMN IF NOT EXISTS `status` ENUM('Pending','Acknowledged','Resolved','Escalated') NOT NULL DEFAULT 'Escalated';

-- ----------------------------------------------------------------------------
-- 2. TICKET COMMENTS / CONVERSATION LEDGER
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ticket_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `tkt_id` varchar(15) NOT NULL,
  `author_emp_id` varchar(10) DEFAULT 'EMP-1018',
  `author_name` varchar(100) DEFAULT 'Alexey Ivanov',
  `author_role` varchar(100) DEFAULT 'Tier 3 SCADA Engineer',
  `author_type` enum('tech','requester','system') NOT NULL DEFAULT 'tech',
  `comment_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`comment_id`),
  KEY `idx_cmt_tkt_id` (`tkt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. INDUSTRIAL IT ASSETS & EDGE HARDWARE REGISTRY
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `it_assets` (
  `asset_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_tag` varchar(50) DEFAULT NULL,
  `emp_id` varchar(10) DEFAULT NULL,
  `department_code` varchar(4) DEFAULT NULL,
  `system_id` varchar(4) DEFAULT NULL,
  `asset_type` varchar(100) DEFAULT 'Industrial Gateway',
  `device_model` varchar(150) DEFAULT NULL,
  `hostname` varchar(150) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `mac_address` varchar(50) DEFAULT NULL,
  `location` varchar(150) DEFAULT 'Central Datacenter',
  `operating_system` varchar(100) DEFAULT 'Linux',
  `os_version` varchar(50) DEFAULT NULL,
  `firmware_version` varchar(50) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `assigned_date` date DEFAULT NULL,
  `criticality` varchar(20) DEFAULT 'High',
  `environment` varchar(20) DEFAULT 'Production',
  `status` varchar(30) DEFAULT 'Active',
  `last_seen_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `health_status` varchar(50) NOT NULL DEFAULT 'Nominal (Active)',
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`asset_id`),
  UNIQUE KEY `idx_asset_tag_unique` (`asset_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. KNOWLEDGE BASE ARTICLES & SOP RUNBOOKS
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `knowledge_base_articles` (
  `kb_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_code` varchar(50) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `category` varchar(100) DEFAULT 'General Support',
  `tags` varchar(255) DEFAULT NULL,
  `created_by_emp_id` varchar(10) DEFAULT 'EMP-1018',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `views_count` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`kb_id`),
  UNIQUE KEY `idx_art_code` (`article_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. SLA POLICIES TABLE
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sla_policies` (
  `sla_id` int(11) NOT NULL AUTO_INCREMENT,
  `priority` enum('Low','Medium','High','Critical') DEFAULT NULL,
  `response_time_hours` int(11) DEFAULT NULL,
  `resolution_time_hours` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sla_id`),
  UNIQUE KEY `idx_sla_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. TICKET ESCALATIONS TABLE
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ticket_escalations` (
  `escalation_id` int(11) NOT NULL AUTO_INCREMENT,
  `tkt_id` varchar(15) NOT NULL,
  `escalated_to_emp_id` varchar(10) DEFAULT 'EMP-1005',
  `escalated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reason` text DEFAULT NULL,
  `status` enum('Pending','Acknowledged','Resolved','Escalated') NOT NULL DEFAULT 'Escalated',
  PRIMARY KEY (`escalation_id`),
  KEY `idx_esc_tkt` (`tkt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- SEED DATA
-- ============================================================================

-- Seed: Tickets
INSERT INTO `tickets` (`tkt_id`, `requester_type`, `source_system`, `title`, `description`, `requester_name`, `requester_role`, `requester_dept`, `priority`, `assigned_emp_id`, `status`, `sla_deadline`)
VALUES
('TICK-8819', 'Employee', 'SCADA Modbus Gateway #3 (Lipetsk Bay)', 'SCADA Modbus Gateway #3 Packet Drop', 'Telemetry frame drop on RS-485 bus #3 connecting high-temp pyrometer array. Packet loss exceeding 14.8% during hot blast cycle.', 'Dr. Elena Rostova', 'Chief Optical Calibration Architect', 'Optical Calibration Bay', 'Critical', 'EMP-1018', 'InProgress', DATE_ADD(NOW(), INTERVAL 2 HOUR)),
('TICK-8820', 'Employee', 'Cleanroom Biometric Scanner Bay B', 'Cleanroom Airlock RFID Interlock Rejecting Level 3 Badges', 'Class 4 Cleanroom airlock interlock rejecting authenticated Level 3 smartcard RFID credentials.', 'Dr. Mikhail Abramov', 'Principal Semiconductor Physicist', 'R&D Sensor Fab', 'Critical', 'EMP-1018', 'InProgress', DATE_ADD(NOW(), INTERVAL 1 HOUR)),
('TICK-8821', 'Employee', 'FAT Triangulation Laser Calibration Server', 'Laser Triangulation Calibration Server Matrix Overflow', 'Automated calibration routine crashing on 64-bit floating point matrix overflow during high-speed profile tests.', 'Viktor Morozov', 'Lead SCADA Gateway Specialist', 'FAT Calibration Bay', 'High', 'EMP-1004', 'Open', DATE_ADD(NOW(), INTERVAL 4 HOUR)),
('TICK-8822', 'Employee', 'ISO 9001 Electronic Certificate Signer', 'ISO 9001 PKI Smartcard Token Renewal Required', 'Cryptographic smartcard PKI token renewal required for electronic FAT test report signing.', 'Anna Belova', 'Head of Quality Assurance', 'Quality Assurance', 'Medium', 'EMP-1002', 'InProgress', DATE_ADD(NOW(), INTERVAL 8 HOUR)),
('TICK-8823', 'Employee', 'ERP Procurement Signing Authority Module', 'ERP Procurement Delegation Authority During Annual Leave', 'Request for secondary approval delegation during scheduled annual factory shutdown.', 'Svetlana Petrova', 'Strategic Component Buyer', 'Procurement & Logistics', 'Low', 'EMP-1002', 'Open', DATE_ADD(NOW(), INTERVAL 24 HOUR))
ON DUPLICATE KEY UPDATE 
  `title` = VALUES(`title`),
  `description` = VALUES(`description`),
  `priority` = VALUES(`priority`),
  `status` = VALUES(`status`),
  `assigned_emp_id` = VALUES(`assigned_emp_id`);

-- Seed: Ticket Comments
INSERT INTO `ticket_comments` (`tkt_id`, `author_name`, `author_role`, `author_type`, `author_emp_id`, `comment_text`, `created_at`)
VALUES
('TICK-8819', 'Dr. Elena Rostova', 'Chief Optical Calibration Architect', 'requester', 'EMP-1001', 'Initial telemetry diagnostic shows CRC errors spiking every 4.2 seconds on Moxa MB3170 RS-485 bus #3. Induction furnace EMI suspected.', DATE_SUB(NOW(), INTERVAL 90 MINUTE)),
('TICK-8819', 'Alexey Ivanov', 'Lead IT Tech · Tier 3', 'tech', 'EMP-1018', 'Acknowledged. Oscilloscope attached to Channel B. Investigating shield ground potential differences between cleanroom floor and furnace transformer vault.', DATE_SUB(NOW(), INTERVAL 45 MINUTE)),
('TICK-8819', 'Alexey Ivanov', 'Lead IT Tech · Tier 3', 'tech', 'EMP-1018', 'Ground loop identified: 34V AC ripple on copper shield. Switched pyrometer bus lines to optically isolated transceiver module. Retesting frame drop rate now.', DATE_SUB(NOW(), INTERVAL 15 MINUTE));

-- Seed: IT Hardware Assets
INSERT INTO `it_assets` (`asset_tag`, `device_model`, `asset_type`, `location`, `ip_address`, `mac_address`, `firmware_version`, `health_status`, `notes`)
VALUES
('VP-GW-LIP-03', 'Moxa MGate MB3170', 'Modbus TCP/IP to RS-485 Gateway', 'Lipetsk Furnace #5 Bay', '10.240.48.12', '00:90:E8:21:4B:03', 'v4.2.1-sec', 'Degraded (14% Loss)', 'Monitors high-temp pyrometer array for furnace #5 blast cycle.'),
('VP-BIO-FAB-02', 'Suprema BioEntry W2', 'Cleanroom RFID/Biometric Airlock', 'Nanofabrication Bay B', '10.240.90.05', '00:17:7D:6F:12:88', 'v2.18.0', 'Interlock Fault', 'Controls ISO Class 4 cleanroom personnel airlock gating.'),
('VP-SRV-FAT-01', 'Advantech MIC-7700', 'Industrial Rugged Xeon Server', 'FAT Acceptance Bay #2', '10.240.12.80', '00:0B:AB:44:90:E1', 'Ubuntu 22.04 RT', 'Online (Active)', 'Real-time laser triangulation coordinate calculation server.'),
('VP-CAL-OPTI-04', 'Mikron M390 Ultra-Precision', 'Optical Pyrometer Standard', 'Optical Standards Metrology Bay', '10.240.12.92', '00:80:A3:99:C2:01', 'v1.44-cal', 'Online (Active)', 'National standard trace optical reference.')
ON DUPLICATE KEY UPDATE 
  `device_model` = VALUES(`device_model`),
  `health_status` = VALUES(`health_status`);

-- Seed: Knowledge Base Articles
INSERT INTO `knowledge_base_articles` (`article_code`, `title`, `category`, `summary`, `content`, `tags`, `views_count`)
VALUES
('KB-4091', 'RS-485 Modbus Serial EMI Troubleshooting', 'SCADA NETWORKING', 'Step-by-step failover procedure for Moxa MB3170 gateways when induction furnace interference degrades copper bus signal-to-noise ratio.', '1. Check line impedance using 120-ohm termination resistor switches at both bus ends.\n2. Measure ground potential offset across terminal SG and chassis ground; maximum allowable is < 7V RMS.\n3. If noise exceeds 200mV peak-to-peak, activate optical isolation mode on port DIP switch #3.\n4. Run Modbus poll diagnostics: sudo mbpoll -m rtu -a 1 -b 19200 -d 8 -s 1 -p none /dev/ttyUSB0\n5. Verify 0 CRC errors over 10,000 continuous query cycles before returning bus to production.', 'SCADA, RS485, Moxa, EMI, Pyrometer', 284),
('KB-3184', 'Cleanroom ISO Class 4 Airlock Interlock Recovery', 'ACCESS CONTROL', 'Emergency bypass protocols, RFID reader recalibration, and pressure differential sensor zeroing for cleanroom bays A through D.', '1. Inspect airlock differential pressure sensor: reading must stay between 25Pa and 40Pa relative to gowning buffer.\n2. In case of badge rejection, verify Suprema BioEntry W2 controller status in Wiegand mode 34-bit.\n3. Cycle airlock logic interlock PLC (Siemens S7-1200) via key switch SW-2 in cleanroom electrical closet.\n4. Emergency bypass override: Turn safety lock key clockwise 90 degrees; this logs an unmaskable security audit event in SYS 07.\n5. Re-authenticate Level 3 badge to confirm automated magnetic strike clearance.', 'Cleanroom, RFID, Airlock, Interlock, Suprema', 196),
('KB-2015', 'Hardware Security Module (HSM) Token Renewal', 'PKI INFRASTRUCTURE', 'Cryptographic key issuance and CSR generation for automated test sign-off compliant with GOST R 34.12-2015 algorithms.', '1. Connect authorized cryptographic token (JaCarta-2 GOST / Rutoken EDS 3.0) to workstation USB port.\n2. Launch CSP CryptoPro 5.0 administration utility.\n3. Generate container CSR: cryptcp -creatrqst -dn "CN=Alexey Ivanov, OU=IT Ops, O=VOSTOKPRIBOR" -cont "FAT-SIGN-2026" req.req\n4. Submit request to internal Sub-CA: https://ca.vostokpribor.local/certsrv\n5. Install issued X.509 certificate into token container and verify signature on test PDF protocol.', 'PKI, HSM, GOST, Security, Certificate', 142)
ON DUPLICATE KEY UPDATE 
  `title` = VALUES(`title`),
  `content` = VALUES(`content`);

-- Seed: SLA Policies
INSERT INTO `sla_policies` (`priority`, `response_time_hours`, `resolution_time_hours`, `description`)
VALUES
('Critical', 1, 2, 'Production-halting incidents, furnace telemetry losses, and cleanroom airlock failures.'),
('High', 2, 4, 'Acceptance test rig malfunctions, automated QA server faults with workaround available.'),
('Medium', 4, 8, 'Internal tooling degradation, secondary sensor calibrations, and certificate sign-offs.'),
('Low', 12, 24, 'Routine access provisioning, equipment relocation requests, and documentation reviews.')
ON DUPLICATE KEY UPDATE 
  `response_time_hours` = VALUES(`response_time_hours`),
  `resolution_time_hours` = VALUES(`resolution_time_hours`);

-- Seed: Escalations
INSERT INTO `ticket_escalations` (`tkt_id`, `escalated_to_emp_id`, `reason`, `status`)
VALUES
('TICK-8819', 'EMP-1005', 'Telemetry instability on blast furnace #5 exceeding standard 30-minute threshold', 'Escalated');

SET FOREIGN_KEY_CHECKS = 1;
