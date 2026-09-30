-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2026 at 02:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vostokpribor`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_reviews`
--

CREATE TABLE `access_reviews` (
  `review_id` int(11) NOT NULL,
  `emp_id` varchar(10) DEFAULT NULL,
  `reviewed_by_emp_id` varchar(10) DEFAULT NULL,
  `review_date` date DEFAULT curdate(),
  `finding` text DEFAULT NULL,
  `action_taken` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `access_reviews`
--

INSERT INTO `access_reviews` (`review_id`, `emp_id`, `reviewed_by_emp_id`, `review_date`, `finding`, `action_taken`) VALUES
(1, 'EMP-1001', 'EMP-1005', '2026-09-15', 'Executive clearance L4 verified across grid operations and telemetry.', 'Attested - All Entitlements Validated'),
(2, 'EMP-1002', 'EMP-1005', '2026-09-18', 'Chief Automation Engineer - SCADA & PLC operational permits active.', 'Pending Quarterly Attestation Sign-off'),
(3, 'EMP-1004', 'EMP-1005', '2026-09-14', 'CTO root trust boundary attestation completed.', 'Attested - Master Signer Approved'),
(4, 'EMP-1005', 'EMP-1001', '2026-09-10', 'Governance Officer dual-custody audit interlock verified.', 'Master Signer Attestation Active'),
(5, 'EMP-1007', 'EMP-1005', '2026-09-16', 'Warehouse logistics dispatch tokens active for Shymkent depot.', 'Pending Quarterly Attestation Sign-off'),
(6, 'EMP-1009', 'EMP-1005', '2026-09-20', 'SEC-POL-44 Breach Detected: Vendor contract termination date was 14 days ago. High-privilege RSA SSH-key remains configured inside SYS-03 (CNC SCADA Gateway).', 'Orphan Account Isolated - Flagged Unlawful Bind'),
(7, 'EMP-1011', 'EMP-1005', '2026-09-12', 'Procurement manager telemetry and invoice approval limits verified.', 'Attested - Role Validated'),
(8, 'EMP-1018', 'EMP-1005', '2026-09-15', 'Systems Engineer L3 access to IT assets and SLA escalation queue verified.', 'Attested - Validated'),
(9, 'EMP-1020', 'EMP-1005', '2026-09-17', 'Lead Developer API Sandbox key generation access verified.', 'Attested - Validated');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `posted_by_emp_id` varchar(10) DEFAULT NULL,
  `audience_dept` varchar(4) DEFAULT NULL,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcement_id`, `title`, `body`, `posted_by_emp_id`, `audience_dept`, `posted_at`) VALUES
(3, 'New Industrial Optical Pyrometer Metrotec-900 Released to Production', 'Engineering & R&D has successfully transitioned the Metrotec-900 into high-volume assembly. Product documentation and calibration sheets are now in the File Center.', 'EMP-1004', 'ENG', '2026-09-20 07:30:00'),
(4, 'Winter Equipment Maintenance & Calibration Schedules Published', 'Operations & Logistics has uploaded the Q4 winter servicing windows for field calibration teams across Karaganda, Pavlodar, and Atyrau.', 'EMP-1002', 'OPS', '2026-09-21 05:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `api_access_logs`
--

CREATE TABLE `api_access_logs` (
  `api_log_id` bigint(20) NOT NULL,
  `credential_id` int(11) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `system_id` varchar(4) DEFAULT NULL,
  `endpoint` varchar(200) DEFAULT NULL,
  `http_method` varchar(10) DEFAULT NULL,
  `source_ip` varchar(45) DEFAULT NULL,
  `status_code` int(11) DEFAULT NULL,
  `request_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `response_time_ms` int(11) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `request_id` varchar(64) DEFAULT NULL,
  `success` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `api_access_logs`
--

INSERT INTO `api_access_logs` (`api_log_id`, `credential_id`, `partner_id`, `system_id`, `endpoint`, `http_method`, `source_ip`, `status_code`, `request_time`, `response_time_ms`, `user_agent`, `request_id`, `success`) VALUES
(1, 1, 1, 'DEV', '/api/v1/telemetry/scada', 'GET', '10.240.1.20', 200, '2026-09-23 08:00:00', 42, 'VostokClient/2.4', 'REQ-2026-9901', 1),
(2, 2, 2, 'CRM', '/api/v1/customers/lookup', 'POST', '10.240.3.15', 200, '2026-09-23 08:15:00', 65, 'VostokCRM/4.1', 'REQ-2026-9902', 1),
(3, 3, 3, 'SHP', '/api/v1/orders/create', 'POST', '10.240.4.8', 201, '2026-09-23 08:30:00', 120, 'B2BPortal/1.0', 'REQ-2026-9903', 1),
(4, 4, 4, 'ADM', '/api/v1/governance/attest', 'POST', '10.240.0.1', 200, '2026-09-23 08:45:00', 38, 'GovCoreDaemon/11.4', 'REQ-2026-9904', 1);

-- --------------------------------------------------------

--
-- Table structure for table `api_credentials`
--

CREATE TABLE `api_credentials` (
  `credential_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `api_key_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `revoked` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `api_credentials`
--

INSERT INTO `api_credentials` (`credential_id`, `partner_id`, `api_key_hash`, `created_at`, `expires_at`, `revoked`) VALUES
(1, 1, '$2y$12$bvGevGpqrlkisCIAr1UKaeTRl/oimOkZl6ruFAl8WkYoaB7xLL3jK', '2026-01-15 07:00:00', '2027-01-15 07:00:00', 0),
(2, 2, '$2y$12$bvGevGpqrlkisCIAr1UKaeTRl/oimOkZl6ruFAl8WkYoaB7xLL3jK', '2026-02-20 08:30:00', '2027-02-20 08:30:00', 0),
(3, 3, '$2y$12$bvGevGpqrlkisCIAr1UKaeTRl/oimOkZl6ruFAl8WkYoaB7xLL3jK', '2026-03-10 11:00:00', '2027-03-10 11:00:00', 0),
(4, 4, '$2y$12$bvGevGpqrlkisCIAr1UKaeTRl/oimOkZl6ruFAl8WkYoaB7xLL3jK', '2026-04-05 06:15:00', '2027-04-05 06:15:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `api_partners`
--

CREATE TABLE `api_partners` (
  `partner_id` int(11) NOT NULL,
  `cus_id` varchar(10) DEFAULT NULL,
  `partner_name` varchar(150) DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(30) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `api_partners`
--

INSERT INTO `api_partners` (`partner_id`, `cus_id`, `partner_name`, `registered_at`, `status`) VALUES
(1, 'CUS-1001', 'Aral Geomatics Automation Labs', '2026-01-15 07:00:00', 'Active'),
(2, 'CUS-1003', 'Steppe Mining SCADA Engineering', '2026-02-20 08:30:00', 'Active'),
(3, 'CUS-1005', 'Tashkent Precision Telemetry Division', '2026-03-10 11:00:00', 'Active'),
(4, 'CUS-1006', 'Rhein Werk Metrology Interface', '2026-04-05 06:15:00', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `audit_id` bigint(20) NOT NULL,
  `actor_emp_id` varchar(10) DEFAULT NULL,
  `actor_customer_id` varchar(10) DEFAULT NULL,
  `actor_system` varchar(50) DEFAULT NULL,
  `system_id` varchar(4) DEFAULT NULL,
  `action` varchar(200) DEFAULT NULL,
  `target_entity_type` varchar(50) DEFAULT NULL,
  `target_entity_id` varchar(20) DEFAULT NULL,
  `source_ip` varchar(45) DEFAULT NULL,
  `device_id` int(11) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `result` varchar(20) DEFAULT NULL,
  `occurred_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`audit_id`, `actor_emp_id`, `actor_customer_id`, `actor_system`, `system_id`, `action`, `target_entity_type`, `target_entity_id`, `source_ip`, `device_id`, `old_values`, `new_values`, `result`, `occurred_at`) VALUES
(1, 'EMP-1005', NULL, 'GOV-CORE', 'ADM', 'QUARTERLY_ATTESTATION_CYCLE_OPENED', 'access_matrix', 'DOC-2026-007', '10.240.0.1', NULL, NULL, '{\"cycle\":\"2026-Q3\",\"total_attested\":17,\"posture\":\"85%\"}', 'SUCCESS', '2026-09-21 05:30:00'),
(2, 'EMP-1001', NULL, 'EXEC-PORTAL', 'ADM', 'DUAL_CUSTODY_SIGN_OFF', 'governance_policy', 'POL-SEC-2026.04', '10.240.0.12', NULL, NULL, '{\"cosigners\":[\"EMP-1001\",\"EMP-1005\"],\"status\":\"APPROVED\"}', 'SUCCESS', '2026-09-21 06:14:22'),
(3, 'EMP-1004', NULL, 'DEV-ENCLAVE', 'DEV', 'HSM_ROOT_KEY_ROTATION', 'cryptographic_vault', 'KEY-FIPS-140-3', '10.240.1.44', NULL, '{\"active_key\":\"0x7F2A...\",\"algorithm\":\"RSA-4096\"}', '{\"active_key\":\"0x9C1B...\",\"algorithm\":\"CRYSTALS-Kyber-1024\"}', 'SUCCESS', '2026-09-21 07:05:11'),
(4, 'EMP-1009', NULL, 'SCADA-BRIDGE', 'CUS', 'UNAUTHORIZED_INGESTION_PROBE', 'api_endpoint', '/api/v1/telemetry/sc', '192.168.10.89', NULL, NULL, '{\"action\":\"PROBE_BLOCKED\",\"reason\":\"CONTRACT_EXPIRED\"}', 'BLOCKED', '2026-09-21 08:22:45'),
(5, 'EMP-1005', NULL, 'GOV-CORE', 'ADM', 'ISOLATION_TRIGGER_ACTIVATED', 'employee_account', 'EMP-1009', '10.240.0.1', NULL, '{\"status\":\"Active\"}', '{\"status\":\"Suspended\",\"flag\":\"UNLAWFUL_BIND\"}', 'QUARANTINED', '2026-09-21 08:25:30'),
(6, 'EMP-1018', NULL, 'IT-DESK', 'IT', 'BREAK_GLASS_SESSION_REQUEST', 'infrastructure_node', 'NODE-ALMATY-ALPHA', '10.240.2.18', NULL, NULL, '{\"ticket_id\":\"TKT-2026-099\",\"duration\":\"2h\"}', 'AUTHORIZED', '2026-09-21 10:40:10'),
(7, 'EMP-1020', NULL, 'DEV-PORTAL', 'DEV', 'API_WEBHOOK_REGISTRATION', 'partner_sandbox', 'PART-004', '10.240.1.20', NULL, NULL, '{\"client\":\"Aral Geomatics\",\"endpoint\":\"https://api.aral-gis.kz/vostok\"}', 'SUCCESS', '2026-09-21 11:12:00'),
(8, 'EMP-1006', NULL, 'SALES-CRM', 'CRM', 'SALES_CONTRACT_SIGN_OFF', 'contract', 'CTR-2026-042', '10.240.3.15', NULL, '{\"status\":\"Draft\"}', '{\"status\":\"Active\",\"total_value\":480000}', 'SUCCESS', '2026-09-21 12:01:45'),
(9, 'EMP-1008', NULL, 'B2B-SHOP', 'SHP', 'BULK_ORDER_DISPATCH_CONFIRM', 'order', 'ORD-2026-104', '10.240.4.8', NULL, '{\"status\":\"Pending\"}', '{\"status\":\"Dispatched\",\"tracking\":\"KZ-POST-9921\"}', 'SUCCESS', '2026-09-21 13:30:19'),
(10, 'EMP-1005', NULL, 'GOV-CORE', 'ADM', 'ACCESS_MATRIX_DISPOSITION_UPDATE', 'employee', 'EMP-1002', '10.240.0.1', NULL, '{\"disposition\":\"Review\"}', '{\"disposition\":\"Attest\"}', 'SUCCESS', '2026-09-21 14:15:33'),
(11, 'EMP-0001', NULL, 'ADM', 'ADM', 'CREATE_BOARD_RISK_FILING', 'risk_register', '11', '::1', NULL, NULL, '{\"risk_code\":\"RR-2026-011\",\"title\":\"Test Automated Threat Vector\",\"impact\":\"Low\",\"likelihood\":\"Low\",\"owner\":\"EMP-0001\",\"system_target\":\"SYS-01 Production Enclave\"}', 'SUCCESS', '2026-09-28 16:44:28'),
(12, 'EMP-0001', NULL, 'ADM', 'ADM', 'DELETE_BOARD_RISK', 'risk_register', '11', '::1', NULL, NULL, '{\"risk_code\":\"RR-2026-011\",\"description\":\"Temporary test risk entry for automated verification.\"}', 'SUCCESS', '2026-09-28 16:44:28'),
(13, 'EMP-0001', NULL, 'ADM', 'ADM', 'CREATE_COMPLIANCE_CONTROL', 'compliance_controls', 'CTRL-TEST-606', '::1', NULL, NULL, '{\"control_id\":7,\"control_code\":\"CTRL-TEST-606\",\"title\":\"Test Control Rule\",\"status\":\"COMPLIANT\"}', 'SUCCESS', '2026-09-28 16:44:28'),
(14, 'EMP-0001', NULL, 'ADM', 'ADM', 'DELETE_COMPLIANCE_CONTROL', 'compliance_controls', 'CTRL-TEST-606', '::1', NULL, NULL, '{\"deleted_id\":7,\"code\":\"CTRL-TEST-606\"}', 'SUCCESS', '2026-09-28 16:44:28'),
(15, 'EMP-0001', NULL, 'ADM', 'ADM', 'CRYPTO_NOTARIZATION_SEAL', 'audit_ledger', 'd8f2b9ace0603ae214df', '::1', NULL, NULL, '{\"merkle_root\":\"d8f2b9ace0603ae214df7480a9d344be41ecd9fe3041848ac1eaf4259293340e\",\"scope\":\"TEST_AUTOMATED_VERIFY\",\"note\":\"System verification test run\",\"sealed_by\":\"EMP-0001\",\"standard\":\"FIPS 140-3\"}', 'SUCCESS', '2026-09-28 16:44:28'),
(16, 'EMP-0001', NULL, 'ADM', 'ADM', 'LAUNCH_RECERTIFICATION_WINDOW', 'recertification_windows', '3', '::1', NULL, NULL, '{\"title\":\"Temporary Test Recert Campaign 964\",\"start_date\":\"2026-09-28\",\"end_date\":\"2026-10-05\",\"status\":\"Pending\"}', 'SUCCESS', '2026-09-28 16:44:28'),
(17, 'EMP-0001', NULL, 'ADM', 'ADM', 'CREATE_SECURITY_POLICY', 'security_policies', 'DOC-TEST-870', '::1', NULL, NULL, '{\"policy_id\":12,\"doc_id\":\"DOC-TEST-870\",\"title\":\"Test Automated Verification Policy\",\"severity\":\"Low\",\"enforcement_mode\":\"ADVISORY\"}', 'SUCCESS', '2026-09-28 16:46:08'),
(18, 'EMP-0001', NULL, 'ADM', 'ADM', 'DELETE_SECURITY_POLICY', 'security_policies', 'DOC-TEST-870', '::1', NULL, NULL, '{\"deleted_policy_id\":12,\"doc_id\":\"DOC-TEST-870\",\"title\":\"Test Automated Verification Policy\"}', 'SUCCESS', '2026-09-28 16:46:08'),
(19, 'EMP-0001', NULL, 'ADM', 'ADM', 'CREATE_BOARD_RISK_FILING', 'risk_register', '12', '::1', NULL, NULL, '{\"risk_code\":\"RR-2026-012\",\"title\":\"Test Automated Threat Vector\",\"impact\":\"Low\",\"likelihood\":\"Low\",\"owner\":\"EMP-0001\",\"system_target\":\"SYS-01 Production Enclave\"}', 'SUCCESS', '2026-09-28 16:46:08'),
(20, 'EMP-0001', NULL, 'ADM', 'ADM', 'DELETE_BOARD_RISK', 'risk_register', '12', '::1', NULL, NULL, '{\"risk_code\":\"RR-2026-012\",\"description\":\"Temporary test risk entry for automated verification.\"}', 'SUCCESS', '2026-09-28 16:46:09'),
(21, 'EMP-0001', NULL, 'ADM', 'ADM', 'CREATE_COMPLIANCE_CONTROL', 'compliance_controls', 'CTRL-TEST-431', '::1', NULL, NULL, '{\"control_id\":8,\"control_code\":\"CTRL-TEST-431\",\"title\":\"Test Control Rule\",\"status\":\"COMPLIANT\"}', 'SUCCESS', '2026-09-28 16:46:09'),
(22, 'EMP-0001', NULL, 'ADM', 'ADM', 'DELETE_COMPLIANCE_CONTROL', 'compliance_controls', 'CTRL-TEST-431', '::1', NULL, NULL, '{\"deleted_id\":8,\"code\":\"CTRL-TEST-431\"}', 'SUCCESS', '2026-09-28 16:46:09'),
(23, 'EMP-0001', NULL, 'ADM', 'ADM', 'CRYPTO_NOTARIZATION_SEAL', 'audit_ledger', '3c40b372a9f19d0c959d', '::1', NULL, NULL, '{\"merkle_root\":\"3c40b372a9f19d0c959d275bd40ac3e63f0a9662f8a35b49bdcdcf5e6acd15e6\",\"scope\":\"TEST_AUTOMATED_VERIFY\",\"note\":\"System verification test run\",\"sealed_by\":\"EMP-0001\",\"standard\":\"FIPS 140-3\"}', 'SUCCESS', '2026-09-28 16:46:09'),
(24, 'EMP-0001', NULL, 'ADM', 'ADM', 'LAUNCH_RECERTIFICATION_WINDOW', 'recertification_windows', '4', '::1', NULL, NULL, '{\"title\":\"Temporary Test Recert Campaign 693\",\"start_date\":\"2026-09-28\",\"end_date\":\"2026-10-05\",\"status\":\"Pending\"}', 'SUCCESS', '2026-09-28 16:46:09'),
(25, 'EMP-0001', NULL, 'ADM', 'ADM', 'TOGGLE_NODE_ISOLATION', 'systems_catalog', 'ADM', '::1', NULL, NULL, '{\"system_id\":\"ADM\",\"new_status\":\"ISOLATED\",\"reason\":\"Manual Air-Gap Interlock Triggered\",\"actor\":\"EMP-0001\"}', 'SUCCESS', '2026-09-28 18:41:39'),
(26, 'EMP-0001', NULL, 'ADM', 'ADM', 'TOGGLE_NODE_ISOLATION', 'systems_catalog', 'CRM', '::1', NULL, NULL, '{\"system_id\":\"CRM\",\"new_status\":\"ISOLATED\",\"reason\":\"Manual Air-Gap Interlock Triggered\",\"actor\":\"EMP-0001\"}', 'SUCCESS', '2026-09-28 19:26:37'),
(27, 'EMP-0001', NULL, 'ADM', 'ADM', 'TOGGLE_NODE_ISOLATION', 'systems_catalog', 'CRM', '::1', NULL, NULL, '{\"system_id\":\"CRM\",\"new_status\":\"OPERATIONAL\",\"reason\":\"Operational Link Re-Established\",\"actor\":\"EMP-0001\"}', 'SUCCESS', '2026-09-28 19:26:40'),
(28, 'EMP-0001', NULL, 'ADM', 'ADM', 'TOGGLE_NODE_ISOLATION', 'systems_catalog', 'ADM', '::1', NULL, NULL, '{\"system_id\":\"ADM\",\"new_status\":\"OPERATIONAL\",\"reason\":\"Operational Link Re-Established\",\"actor\":\"EMP-0001\"}', 'SUCCESS', '2026-09-28 19:26:42'),
(29, 'EMP-1006', 'CUS-1011', 'CRM Platform', 'CRM', 'CREATE_CUSTOMER_ACCOUNT', 'customers', 'CUS-1011', '127.0.0.1', NULL, NULL, '{\"company_name\":\"API Test Industrial Corp\",\"sector\":\"Aerospace Defense\",\"tier\":\"Tier-1 Enterprise\"}', 'SUCCESS', '2026-09-29 16:55:23'),
(30, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'UPDATE_CUSTOMER_ACCOUNT', 'customers', 'CUS-1001', '127.0.0.1', NULL, NULL, '{\"action\":\"update_customer\",\"cus_id\":\"CUS-1001\",\"sector\":\"Heavy Industry & Metallurgy\"}', 'SUCCESS', '2026-09-29 16:55:23'),
(31, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'CREATE_LEAD', 'leads', '7', '127.0.0.1', NULL, NULL, '{\"company_name\":\"Ural Test Turbine LLC\",\"full_name\":\"Viktor Turbinevich\"}', 'SUCCESS', '2026-09-29 16:55:23'),
(32, 'EMP-1006', 'CUS-1008', 'CRM Platform', 'CRM', 'CONVERT_COMMERCIAL_LEAD', 'opportunities', '12', '127.0.0.1', NULL, NULL, '{\"lead_id\":2,\"cus_id\":\"CUS-1008\",\"deal_value\":600000}', 'SUCCESS', '2026-09-29 16:55:23'),
(33, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'CREATE_OPPORTUNITY', 'opportunities', '13', '127.0.0.1', NULL, NULL, '{\"title\":\"API Automated Opp Validation\",\"value\":320000,\"stage\":\"Proposal\"}', 'SUCCESS', '2026-09-29 16:55:23'),
(34, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'UPDATE_OPPORTUNITY_STAGE', 'opportunities', '1', '127.0.0.1', NULL, NULL, '{\"new_stage\":\"Negotiation\",\"probability\":80}', 'SUCCESS', '2026-09-29 16:55:23'),
(35, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'CREATE_CONTRACT', 'contracts', '8', '127.0.0.1', NULL, NULL, '{\"ref\":\"MSA-2026-082D19\",\"value\":1250000}', 'SUCCESS', '2026-09-29 16:55:23'),
(36, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'CREATE_QUOTE', 'quotes', '5', '127.0.0.1', NULL, NULL, '{\"ref\":\"QUO-9088\",\"amount\":420000}', 'SUCCESS', '2026-09-29 16:55:23'),
(37, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'SAVE_FORECAST', 'sales_forecasts', '6', '127.0.0.1', NULL, NULL, '{\"period\":\"2027-Q1\",\"forecast\":25000000}', 'SUCCESS', '2026-09-29 16:55:24'),
(38, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'LOG_COMMERCIAL_ACTIVITY', 'crm_activities', '19', '127.0.0.1', NULL, NULL, '{\"title\":\"Executive Strategic Review\",\"type\":\"Meeting\"}', 'SUCCESS', '2026-09-29 16:55:24'),
(39, 'EMP-1006', 'CUS-1012', 'CRM Platform', 'CRM', 'CREATE_CUSTOMER_ACCOUNT', 'customers', 'CUS-1012', '127.0.0.1', NULL, NULL, '{\"company_name\":\"Temporary Deletion Test Corp\",\"sector\":\"Chemical Refining\",\"tier\":\"Tier-1 Enterprise\"}', 'SUCCESS', '2026-09-29 16:58:08'),
(40, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'CREATE_LEAD', 'leads', '8', '127.0.0.1', NULL, NULL, '{\"company_name\":\"Temporary Deletion Lead\",\"full_name\":\"Temp Tester\"}', 'SUCCESS', '2026-09-29 16:58:08'),
(41, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'DELETE_LEAD', 'leads', '8', '127.0.0.1', NULL, NULL, NULL, 'SUCCESS', '2026-09-29 16:58:08'),
(42, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'CREATE_OPPORTUNITY', 'opportunities', '14', '127.0.0.1', NULL, NULL, '{\"title\":\"Temporary Deletion Opp\",\"value\":150000,\"stage\":\"Proposal\"}', 'SUCCESS', '2026-09-29 16:58:09'),
(43, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'DELETE_OPPORTUNITY', 'opportunities', '14', '127.0.0.1', NULL, NULL, NULL, 'SUCCESS', '2026-09-29 16:58:09'),
(44, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'CREATE_CUSTOMER_ACCOUNT', 'customers', 'CUS-1013', '127.0.0.1', NULL, NULL, '{\"company_name\":\"Temporary Deletion Test Corp\",\"sector\":\"Chemical Refining\",\"tier\":\"Tier-1 Enterprise\"}', 'SUCCESS', '2026-09-29 16:58:24'),
(45, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'DELETE_CUSTOMER_ACCOUNT', 'customers', 'CUS-1013', '127.0.0.1', NULL, NULL, NULL, 'SUCCESS', '2026-09-29 16:58:24'),
(46, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'CREATE_LEAD', 'leads', '9', '127.0.0.1', NULL, NULL, '{\"company_name\":\"Temporary Deletion Lead\",\"full_name\":\"Temp Tester\"}', 'SUCCESS', '2026-09-29 16:58:24'),
(47, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'DELETE_LEAD', 'leads', '9', '127.0.0.1', NULL, NULL, NULL, 'SUCCESS', '2026-09-29 16:58:24'),
(48, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'CREATE_OPPORTUNITY', 'opportunities', '15', '127.0.0.1', NULL, NULL, '{\"title\":\"Temporary Deletion Opp\",\"value\":150000,\"stage\":\"Proposal\"}', 'SUCCESS', '2026-09-29 16:58:24'),
(49, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'DELETE_OPPORTUNITY', 'opportunities', '15', '127.0.0.1', NULL, NULL, NULL, 'SUCCESS', '2026-09-29 16:58:24'),
(50, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'CREATE_CUSTOMER_ACCOUNT', 'customers', 'CUS-1013', '127.0.0.1', NULL, NULL, '{\"company_name\":\"API Test Industrial Corp\",\"sector\":\"Aerospace Defense\",\"tier\":\"Tier-1 Enterprise\"}', 'SUCCESS', '2026-09-29 16:59:57'),
(51, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'UPDATE_CUSTOMER_ACCOUNT', 'customers', 'CUS-1001', '127.0.0.1', NULL, NULL, '{\"action\":\"update_customer\",\"cus_id\":\"CUS-1001\",\"sector\":\"Heavy Industry & Metallurgy\"}', 'SUCCESS', '2026-09-29 16:59:57'),
(52, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'CREATE_LEAD', 'leads', '10', '127.0.0.1', NULL, NULL, '{\"company_name\":\"Ural Test Turbine LLC\",\"full_name\":\"Viktor Turbinevich\"}', 'SUCCESS', '2026-09-29 16:59:57'),
(53, 'EMP-1006', 'CUS-1008', 'CRM Platform', 'CRM', 'CONVERT_COMMERCIAL_LEAD', 'opportunities', '16', '127.0.0.1', NULL, NULL, '{\"lead_id\":2,\"cus_id\":\"CUS-1008\",\"deal_value\":600000}', 'SUCCESS', '2026-09-29 16:59:57'),
(54, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'CREATE_OPPORTUNITY', 'opportunities', '17', '127.0.0.1', NULL, NULL, '{\"title\":\"API Automated Opp Validation\",\"value\":320000,\"stage\":\"Proposal\"}', 'SUCCESS', '2026-09-29 16:59:58'),
(55, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'UPDATE_OPPORTUNITY_STAGE', 'opportunities', '1', '127.0.0.1', NULL, NULL, '{\"new_stage\":\"Negotiation\",\"probability\":80}', 'SUCCESS', '2026-09-29 16:59:58'),
(56, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'CREATE_CONTRACT', 'contracts', '9', '127.0.0.1', NULL, NULL, '{\"ref\":\"MSA-2026-98FC21\",\"value\":1250000}', 'SUCCESS', '2026-09-29 16:59:58'),
(57, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'CREATE_QUOTE', 'quotes', '6', '127.0.0.1', NULL, NULL, '{\"ref\":\"QUO-9800\",\"amount\":420000}', 'SUCCESS', '2026-09-29 16:59:58'),
(58, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'SAVE_FORECAST', 'sales_forecasts', '7', '127.0.0.1', NULL, NULL, '{\"period\":\"2027-Q1\",\"forecast\":25000000}', 'SUCCESS', '2026-09-29 16:59:58'),
(59, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'LOG_COMMERCIAL_ACTIVITY', 'crm_activities', '35', '127.0.0.1', NULL, NULL, '{\"title\":\"Executive Strategic Review\",\"type\":\"Meeting\"}', 'SUCCESS', '2026-09-29 16:59:58'),
(60, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'CREATE_CUSTOMER_ACCOUNT', 'customers', 'CUS-1014', '127.0.0.1', NULL, NULL, '{\"company_name\":\"Direct API Test Corp\",\"sector\":\"Chemical Refining\",\"tier\":\"Tier-1 Enterprise\"}', 'SUCCESS', '2026-09-29 17:08:32'),
(61, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'CREATE_LEAD', 'leads', '11', '127.0.0.1', NULL, NULL, '{\"company\":\"Direct Lead Facility\",\"name\":\"Alexey Leadman\"}', 'SUCCESS', '2026-09-29 17:08:32'),
(62, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'CONVERT_COMMERCIAL_LEAD', 'opportunities', '18', '127.0.0.1', NULL, NULL, '{\"lead_id\":3,\"cus_id\":\"CUS-1009\",\"deal_value\":500000}', 'SUCCESS', '2026-09-29 17:08:32'),
(63, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'CREATE_OPPORTUNITY', 'opportunities', '19', '127.0.0.1', NULL, NULL, '{\"title\":\"Direct Opp Validation\",\"value\":280000,\"stage\":\"Proposal\"}', 'SUCCESS', '2026-09-29 17:08:32'),
(64, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'UPDATE_OPPORTUNITY_STAGE', 'opportunities', '2', '127.0.0.1', NULL, NULL, '{\"new_stage\":\"Negotiation\",\"probability\":80}', 'SUCCESS', '2026-09-29 17:08:33'),
(65, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'CREATE_CONTRACT', 'contracts', '10', '127.0.0.1', NULL, NULL, '{\"ref\":\"MSA-2026-9A84B0\",\"value\":750000}', 'SUCCESS', '2026-09-29 17:08:33'),
(66, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'CREATE_QUOTE', 'quotes', '7', '127.0.0.1', NULL, NULL, '{\"ref\":\"QUO-9005\",\"amount\":350000}', 'SUCCESS', '2026-09-29 17:08:33'),
(67, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'SAVE_FORECAST', 'sales_forecasts', '8', '127.0.0.1', NULL, NULL, '{\"period\":\"2027-Q2\",\"forecast\":28000000}', 'SUCCESS', '2026-09-29 17:08:33'),
(68, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'LOG_COMMERCIAL_ACTIVITY', 'crm_activities', '43', '127.0.0.1', NULL, NULL, '{\"title\":\"Direct Commercial Review Meeting\",\"type\":\"Meeting\"}', 'SUCCESS', '2026-09-29 17:08:33'),
(69, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'DELETE_CUSTOMER_ACCOUNT', 'customers', 'CUS-9999', '127.0.0.1', NULL, NULL, NULL, 'SUCCESS', '2026-09-29 17:08:57'),
(70, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'DELETE_LEAD', 'leads', '99999', '127.0.0.1', NULL, NULL, NULL, 'SUCCESS', '2026-09-29 17:08:57'),
(71, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'DELETE_OPPORTUNITY', 'opportunities', '99999', '127.0.0.1', NULL, NULL, NULL, 'SUCCESS', '2026-09-29 17:08:57'),
(72, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'DELETE_CONTRACT', 'contracts', '99999', '127.0.0.1', NULL, NULL, NULL, 'SUCCESS', '2026-09-29 17:08:57'),
(73, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'DELETE_CUSTOMER_ACCOUNT', 'customers', 'CUS-9999', '127.0.0.1', NULL, NULL, NULL, 'SUCCESS', '2026-09-29 17:09:09'),
(74, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'DELETE_LEAD', 'leads', '99999', '127.0.0.1', NULL, NULL, NULL, 'SUCCESS', '2026-09-29 17:09:09'),
(75, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'DELETE_OPPORTUNITY', 'opportunities', '99999', '127.0.0.1', NULL, NULL, NULL, 'SUCCESS', '2026-09-29 17:09:09'),
(76, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'DELETE_CONTRACT', 'contracts', '99999', '127.0.0.1', NULL, NULL, NULL, 'SUCCESS', '2026-09-29 17:09:09'),
(77, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'DELETE_QUOTE', 'quotes', '99999', '127.0.0.1', NULL, NULL, NULL, 'SUCCESS', '2026-09-29 17:09:09'),
(78, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'CREATE_CUSTOMER_ACCOUNT', 'customers', 'CUS-1015', '127.0.0.1', NULL, NULL, '{\"company_name\":\"Direct API Test Corp\",\"sector\":\"Chemical Refining\",\"tier\":\"Tier-1 Enterprise\"}', 'SUCCESS', '2026-09-29 17:09:16'),
(79, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'CREATE_LEAD', 'leads', '100000', '127.0.0.1', NULL, NULL, '{\"company\":\"Direct Lead Facility\",\"name\":\"Alexey Leadman\"}', 'SUCCESS', '2026-09-29 17:09:16'),
(80, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'CREATE_OPPORTUNITY', 'opportunities', '100000', '127.0.0.1', NULL, NULL, '{\"title\":\"Direct Opp Validation\",\"value\":280000,\"stage\":\"Proposal\"}', 'SUCCESS', '2026-09-29 17:09:16'),
(81, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'UPDATE_OPPORTUNITY_STAGE', 'opportunities', '2', '127.0.0.1', NULL, NULL, '{\"new_stage\":\"Negotiation\",\"probability\":80}', 'SUCCESS', '2026-09-29 17:09:16'),
(82, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'CREATE_CONTRACT', 'contracts', '100000', '127.0.0.1', NULL, NULL, '{\"ref\":\"MSA-2026-640619\",\"value\":750000}', 'SUCCESS', '2026-09-29 17:09:17'),
(83, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'CREATE_QUOTE', 'quotes', '100000', '127.0.0.1', NULL, NULL, '{\"ref\":\"QUO-9936\",\"amount\":350000}', 'SUCCESS', '2026-09-29 17:09:17'),
(84, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'SAVE_FORECAST', 'sales_forecasts', '9', '127.0.0.1', NULL, NULL, '{\"period\":\"2027-Q2\",\"forecast\":28000000}', 'SUCCESS', '2026-09-29 17:09:17'),
(85, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'LOG_COMMERCIAL_ACTIVITY', 'crm_activities', '50', '127.0.0.1', NULL, NULL, '{\"title\":\"Direct Commercial Review Meeting\",\"type\":\"Meeting\"}', 'SUCCESS', '2026-09-29 17:09:17'),
(86, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'CONVERT_COMMERCIAL_LEAD', 'opportunities', '100001', '127.0.0.1', NULL, NULL, '{\"lead_id\":100001,\"cus_id\":\"CUS-1016\",\"deal_value\":400000}', 'SUCCESS', '2026-09-29 17:09:27'),
(87, 'EMP-1006', 'CUS-1001', 'CRM Platform', 'CRM', 'UPLOAD_DOCUMENT', 'documents', 'CRM_20260929_193254_', '127.0.0.1', NULL, NULL, '{\"original_name\":\"test_sample.txt\",\"size_bytes\":41,\"file_path\":\"uploads\\/CRM_20260929_193254_f6496e60_test_sample.txt\",\"mime_type\":\"text\\/plain\"}', 'SUCCESS', '2026-09-29 17:32:54'),
(88, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'EXPORT_DATA', 'leads', '', '127.0.0.1', NULL, NULL, '{\"type\":\"leads\",\"count\":11,\"format\":\"csv\"}', 'SUCCESS', '2026-09-29 17:38:43'),
(89, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'EXPORT_DATA', 'customers', '', '127.0.0.1', NULL, NULL, '{\"type\":\"customers\",\"count\":16,\"format\":\"csv\"}', 'SUCCESS', '2026-09-29 17:38:59'),
(90, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'EXPORT_DATA', 'opportunities', '', '127.0.0.1', NULL, NULL, '{\"type\":\"opportunities\",\"count\":18,\"format\":\"csv\"}', 'SUCCESS', '2026-09-29 17:38:59'),
(91, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'EXPORT_DATA', 'contracts', '', '127.0.0.1', NULL, NULL, '{\"type\":\"contracts\",\"count\":11,\"format\":\"csv\"}', 'SUCCESS', '2026-09-29 17:38:59'),
(92, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'EXPORT_DATA', 'quotes', '', '127.0.0.1', NULL, NULL, '{\"type\":\"quotes\",\"count\":8,\"format\":\"csv\"}', 'SUCCESS', '2026-09-29 17:38:59'),
(93, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'EXPORT_DATA', 'projects', '', '127.0.0.1', NULL, NULL, '{\"type\":\"projects\",\"count\":6,\"format\":\"csv\"}', 'SUCCESS', '2026-09-29 17:38:59'),
(94, 'EMP-1006', NULL, 'CRM Platform', 'CRM', 'EXPORT_DATA', 'forecast', '', '127.0.0.1', NULL, NULL, '{\"type\":\"forecast\",\"count\":9,\"format\":\"csv\"}', 'SUCCESS', '2026-09-29 17:38:59'),
(95, 'EMP-1004', NULL, 'Employee Intranet', 'EMP', 'EXPORT_DATA', 'directory', '', '::1', NULL, NULL, '{\"format\":\"json\",\"row_count\":22}', 'SUCCESS', '2026-09-29 18:10:46'),
(96, 'EMP-1004', NULL, 'Employee Intranet', 'EMP', 'EXPORT_DATA', 'policies', '', '::1', NULL, NULL, '{\"format\":\"json\",\"row_count\":16}', 'SUCCESS', '2026-09-29 18:10:46'),
(97, 'EMP-1004', NULL, 'Employee Intranet', 'EMP', 'EXPORT_DATA', 'announcements', '', '::1', NULL, NULL, '{\"format\":\"json\",\"row_count\":2}', 'SUCCESS', '2026-09-29 18:10:46'),
(98, 'EMP-1004', NULL, 'Employee Intranet', 'EMP', 'EXPORT_DATA', 'leaves', '', '::1', NULL, NULL, '{\"format\":\"json\",\"row_count\":8}', 'SUCCESS', '2026-09-29 18:10:46'),
(99, 'EMP-1004', NULL, 'Employee Intranet', 'EMP', 'EXPORT_DATA', 'directory', '', '::1', NULL, NULL, '{\"format\":\"csv\",\"row_count\":22}', 'SUCCESS', '2026-09-29 18:10:46'),
(100, 'EMP-1004', NULL, 'Employee Intranet', 'EMP', 'UPLOAD_DOCUMENT', 'documents', 'DOC-2026-016', '::1', NULL, NULL, '{\"file_name\":\"Test_Intranet_Standard.pdf\",\"sha256\":\"c6c821101225d0d4d30b231705f70478a38e281936b6cb32f512f3fa38c789ce\",\"size\":\"50 B\"}', 'SUCCESS', '2026-09-29 18:10:53'),
(101, 'EMP-1004', NULL, 'Employee Intranet', 'EMP', 'DELETE_DOCUMENT', 'documents', 'DOC-2026-016', '::1', NULL, NULL, NULL, 'SUCCESS', '2026-09-29 18:11:00'),
(102, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'PLACE_B2B_PURCHASE_ORDER', 'orders', '11', '::1', NULL, NULL, '{\"total_amount\":10400,\"items_count\":1}', 'SUCCESS', '2026-09-29 23:38:10'),
(103, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'UPDATE_ORDER_STATUS', 'orders', '11', '::1', NULL, NULL, '{\"old_status\":\"Processing\",\"new_status\":\"Shipped\"}', 'SUCCESS', '2026-09-29 23:38:10'),
(104, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'DELETE_ORDER', 'orders', '11', '::1', NULL, NULL, '{\"deleted_order\":{\"order_id\":11,\"cus_id\":\"CUS-1001\",\"order_date\":\"2026-09-30 02:38:10\",\"status\":\"Shipped\",\"total_amount\":\"10400.00\"}}', 'SUCCESS', '2026-09-29 23:38:10'),
(105, 'EMP-1006', 'CUS-1001', 'Online Shop B2B', 'SHP', 'CREATE_B2B_PRICE_QUOTE', 'quotes', '100001', '::1', NULL, NULL, '{\"prod_id\":\"PROD-1002\",\"quantity\":5,\"unit_price\":3400,\"total_value\":17000}', 'SUCCESS', '2026-09-29 23:38:10'),
(106, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'UPDATE_QUOTE', 'quotes', '100001', '::1', NULL, NULL, '{\"quote_id\":100001,\"quantity\":8,\"status\":\"Under Review\"}', 'SUCCESS', '2026-09-29 23:38:10'),
(107, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'DELETE_QUOTE', 'quotes', '100001', '::1', NULL, NULL, '{\"deleted_quote\":{\"quote_id\":100001,\"cus_id\":\"CUS-1001\",\"prod_id\":\"PROD-1002\",\"quantity\":8,\"unit_price\":\"3400.00\",\"created_by_emp_id\":\"EMP-1006\",\"created_at\":\"2026-09-30 02:38:10\",\"quote_ref\":\"QUO-8296\",\"equipment_scope\":\"Test Optical Calibration Rig for Blast Furnace\",\"valid_until\":\"2026-10-30\",\"total_amount\":\"27200.00\",\"status\":\"Under Review\"}}', 'SUCCESS', '2026-09-29 23:38:10'),
(108, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'UPLOAD_PROCUREMENT_DOCUMENT', 'documents', 'DOC-SHP-2000', '::1', NULL, NULL, '{\"file_name\":\"Purchase_Order_Severstal_2026.pdf\",\"order_id\":\"1\",\"file_size\":42}', 'SUCCESS', '2026-09-29 23:38:10'),
(109, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'PLACE_B2B_PURCHASE_ORDER', 'orders', '12', '::1', NULL, NULL, '{\"total_amount\":10400,\"items_count\":1}', 'SUCCESS', '2026-09-29 23:38:35'),
(110, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'UPDATE_ORDER_STATUS', 'orders', '12', '::1', NULL, NULL, '{\"old_status\":\"Processing\",\"new_status\":\"Shipped\"}', 'SUCCESS', '2026-09-29 23:38:35'),
(111, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'DELETE_ORDER', 'orders', '12', '::1', NULL, NULL, '{\"deleted_order\":{\"order_id\":12,\"cus_id\":\"CUS-1001\",\"order_date\":\"2026-09-30 02:38:35\",\"status\":\"Shipped\",\"total_amount\":\"10400.00\"}}', 'SUCCESS', '2026-09-29 23:38:35'),
(112, 'EMP-1006', 'CUS-1001', 'Online Shop B2B', 'SHP', 'CREATE_B2B_PRICE_QUOTE', 'quotes', '100002', '::1', NULL, NULL, '{\"prod_id\":\"PROD-1002\",\"quantity\":5,\"unit_price\":3400,\"total_value\":17000}', 'SUCCESS', '2026-09-29 23:38:35'),
(113, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'UPDATE_QUOTE', 'quotes', '100002', '::1', NULL, NULL, '{\"quote_id\":100002,\"quantity\":8,\"status\":\"Under Review\"}', 'SUCCESS', '2026-09-29 23:38:35'),
(114, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'DELETE_QUOTE', 'quotes', '100002', '::1', NULL, NULL, '{\"deleted_quote\":{\"quote_id\":100002,\"cus_id\":\"CUS-1001\",\"prod_id\":\"PROD-1002\",\"quantity\":8,\"unit_price\":\"3400.00\",\"created_by_emp_id\":\"EMP-1006\",\"created_at\":\"2026-09-30 02:38:35\",\"quote_ref\":\"QUO-5590\",\"equipment_scope\":\"Test Optical Calibration Rig for Blast Furnace\",\"valid_until\":\"2026-10-30\",\"total_amount\":\"27200.00\",\"status\":\"Under Review\"}}', 'SUCCESS', '2026-09-29 23:38:35'),
(115, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'UPLOAD_PROCUREMENT_DOCUMENT', 'documents', 'DOC-SHP-7875', '::1', NULL, NULL, '{\"file_name\":\"Purchase_Order_Severstal_2026.pdf\",\"order_id\":\"1\",\"file_size\":42}', 'SUCCESS', '2026-09-29 23:38:35'),
(116, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'PLACE_B2B_PURCHASE_ORDER', 'orders', '13', '::1', NULL, NULL, '{\"total_amount\":10400,\"items_count\":1}', 'SUCCESS', '2026-09-29 23:49:40'),
(117, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'UPDATE_ORDER_STATUS', 'orders', '13', '::1', NULL, NULL, '{\"old_status\":\"Processing\",\"new_status\":\"Shipped\"}', 'SUCCESS', '2026-09-29 23:49:41'),
(118, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'DELETE_ORDER', 'orders', '13', '::1', NULL, NULL, '{\"deleted_order\":{\"order_id\":13,\"cus_id\":\"CUS-1001\",\"order_date\":\"2026-09-30 02:49:40\",\"status\":\"Shipped\",\"total_amount\":\"10400.00\"}}', 'SUCCESS', '2026-09-29 23:49:41'),
(119, 'EMP-1006', 'CUS-1001', 'Online Shop B2B', 'SHP', 'CREATE_B2B_PRICE_QUOTE', 'quotes', '100003', '::1', NULL, NULL, '{\"prod_id\":\"PROD-1002\",\"quantity\":5,\"unit_price\":3400,\"total_value\":17000}', 'SUCCESS', '2026-09-29 23:49:41'),
(120, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'UPDATE_QUOTE', 'quotes', '100003', '::1', NULL, NULL, '{\"quote_id\":100003,\"quantity\":8,\"status\":\"Under Review\"}', 'SUCCESS', '2026-09-29 23:49:41'),
(121, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'DELETE_QUOTE', 'quotes', '100003', '::1', NULL, NULL, '{\"deleted_quote\":{\"quote_id\":100003,\"cus_id\":\"CUS-1001\",\"prod_id\":\"PROD-1002\",\"quantity\":8,\"unit_price\":\"3400.00\",\"created_by_emp_id\":\"EMP-1006\",\"created_at\":\"2026-09-30 02:49:41\",\"quote_ref\":\"QUO-6255\",\"equipment_scope\":\"Test Optical Calibration Rig for Blast Furnace\",\"valid_until\":\"2026-10-30\",\"total_amount\":\"27200.00\",\"status\":\"Under Review\"}}', 'SUCCESS', '2026-09-29 23:49:41'),
(122, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'UPLOAD_PROCUREMENT_DOCUMENT', 'documents', 'DOC-SHP-7443', '::1', NULL, NULL, '{\"file_name\":\"Purchase_Order_Severstal_2026.pdf\",\"order_id\":\"1\",\"file_size\":42}', 'SUCCESS', '2026-09-29 23:49:41'),
(123, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'PLACE_B2B_PURCHASE_ORDER', 'orders', '14', '::1', NULL, NULL, '{\"total_amount\":10400,\"items_count\":1}', 'SUCCESS', '2026-09-29 23:58:50'),
(124, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'UPDATE_ORDER_STATUS', 'orders', '14', '::1', NULL, NULL, '{\"old_status\":\"Processing\",\"new_status\":\"Shipped\"}', 'SUCCESS', '2026-09-29 23:58:50'),
(125, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'DELETE_ORDER', 'orders', '14', '::1', NULL, NULL, '{\"deleted_order\":{\"order_id\":14,\"cus_id\":\"CUS-1001\",\"order_date\":\"2026-09-30 02:58:50\",\"status\":\"Shipped\",\"total_amount\":\"10400.00\"}}', 'SUCCESS', '2026-09-29 23:58:50'),
(126, 'EMP-1006', 'CUS-1001', 'Online Shop B2B', 'SHP', 'CREATE_B2B_PRICE_QUOTE', 'quotes', '100004', '::1', NULL, NULL, '{\"prod_id\":\"PROD-1002\",\"quantity\":5,\"unit_price\":3400,\"total_value\":17000}', 'SUCCESS', '2026-09-29 23:58:50'),
(127, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'UPDATE_QUOTE', 'quotes', '100004', '::1', NULL, NULL, '{\"quote_id\":100004,\"quantity\":8,\"status\":\"Under Review\"}', 'SUCCESS', '2026-09-29 23:58:51'),
(128, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'DELETE_QUOTE', 'quotes', '100004', '::1', NULL, NULL, '{\"deleted_quote\":{\"quote_id\":100004,\"cus_id\":\"CUS-1001\",\"prod_id\":\"PROD-1002\",\"quantity\":8,\"unit_price\":\"3400.00\",\"created_by_emp_id\":\"EMP-1006\",\"created_at\":\"2026-09-30 02:58:50\",\"quote_ref\":\"QUO-7549\",\"equipment_scope\":\"Test Optical Calibration Rig for Blast Furnace\",\"valid_until\":\"2026-10-30\",\"total_amount\":\"27200.00\",\"status\":\"Under Review\"}}', 'SUCCESS', '2026-09-29 23:58:51'),
(129, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'UPLOAD_PROCUREMENT_DOCUMENT', 'documents', 'DOC-SHP-9267', '::1', NULL, NULL, '{\"file_name\":\"Purchase_Order_Severstal_2026.pdf\",\"order_id\":\"1\",\"file_size\":42}', 'SUCCESS', '2026-09-29 23:58:51'),
(130, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'PLACE_B2B_PURCHASE_ORDER', 'orders', '15', '::1', NULL, NULL, '{\"total_amount\":10400,\"items_count\":1}', 'SUCCESS', '2026-09-30 00:03:37'),
(131, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'UPDATE_ORDER_STATUS', 'orders', '15', '::1', NULL, NULL, '{\"old_status\":\"Processing\",\"new_status\":\"Shipped\"}', 'SUCCESS', '2026-09-30 00:03:37'),
(132, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'DELETE_ORDER', 'orders', '15', '::1', NULL, NULL, '{\"deleted_order\":{\"order_id\":15,\"cus_id\":\"CUS-1001\",\"order_date\":\"2026-09-30 03:03:37\",\"status\":\"Shipped\",\"total_amount\":\"10400.00\"}}', 'SUCCESS', '2026-09-30 00:03:37'),
(133, 'EMP-1006', 'CUS-1001', 'Online Shop B2B', 'SHP', 'CREATE_B2B_PRICE_QUOTE', 'quotes', '100005', '::1', NULL, NULL, '{\"prod_id\":\"PROD-1002\",\"quantity\":5,\"unit_price\":3400,\"total_value\":17000}', 'SUCCESS', '2026-09-30 00:03:37'),
(134, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'UPDATE_QUOTE', 'quotes', '100005', '::1', NULL, NULL, '{\"quote_id\":100005,\"quantity\":8,\"status\":\"Under Review\"}', 'SUCCESS', '2026-09-30 00:03:37'),
(135, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'DELETE_QUOTE', 'quotes', '100005', '::1', NULL, NULL, '{\"deleted_quote\":{\"quote_id\":100005,\"cus_id\":\"CUS-1001\",\"prod_id\":\"PROD-1002\",\"quantity\":8,\"unit_price\":\"3400.00\",\"created_by_emp_id\":\"EMP-1006\",\"created_at\":\"2026-09-30 03:03:37\",\"quote_ref\":\"QUO-7625\",\"equipment_scope\":\"Test Optical Calibration Rig for Blast Furnace\",\"valid_until\":\"2026-10-30\",\"total_amount\":\"27200.00\",\"status\":\"Under Review\"}}', 'SUCCESS', '2026-09-30 00:03:37'),
(136, NULL, 'CUS-1001', 'Online Shop B2B', 'SHP', 'UPLOAD_PROCUREMENT_DOCUMENT', 'documents', 'DOC-SHP-1112', '::1', NULL, NULL, '{\"file_name\":\"Purchase_Order_Severstal_2026.pdf\",\"order_id\":\"1\",\"file_size\":42}', 'SUCCESS', '2026-09-30 00:03:37'),
(137, 'EMP-0001', NULL, 'HR System', 'HR', 'APPOINT_EQUAL_MANAGER', 'employees', 'EMP-1008', NULL, NULL, NULL, '{\"job_title\":\"Executive Co-Director\",\"clearance_level\":\"L4\",\"role_id\":1}', 'SUCCESS', '2026-09-30 00:34:14');

-- --------------------------------------------------------

--
-- Table structure for table `authentication_events`
--

CREATE TABLE `authentication_events` (
  `event_id` bigint(20) NOT NULL,
  `account_type` varchar(20) NOT NULL,
  `employee_account_id` int(11) DEFAULT NULL,
  `customer_account_id` int(11) DEFAULT NULL,
  `system_id` varchar(4) DEFAULT NULL,
  `device_id` int(11) DEFAULT NULL,
  `ip_id` int(11) DEFAULT NULL,
  `event_type` varchar(30) DEFAULT NULL,
  `occurred_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `success` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `authentication_events`
--

INSERT INTO `authentication_events` (`event_id`, `account_type`, `employee_account_id`, `customer_account_id`, `system_id`, `device_id`, `ip_id`, `event_type`, `occurred_at`, `success`) VALUES
(1, 'Employee', 53, NULL, 'HR', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-21 17:32:28', 1),
(2, 'Employee', 53, NULL, 'HR', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-21 17:36:16', 1),
(3, 'Employee', 53, NULL, 'DOC', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-24 15:11:24', 1),
(4, 'Employee', 53, NULL, 'DOC', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-24 15:11:39', 1),
(5, 'Employee', 53, NULL, 'FIN', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-24 15:12:00', 1),
(6, 'Employee', 53, NULL, 'FIN', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-24 15:13:52', 1),
(7, 'Employee', 53, NULL, 'IT', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-27 17:01:06', 1),
(8, 'Employee', 53, NULL, 'IT', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 14:24:51', 1),
(9, 'Employee', 53, NULL, 'IT', NULL, NULL, 'LOGIN_FAILURE', '2026-09-28 14:26:01', 0),
(10, 'Employee', 53, NULL, 'IT', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 14:26:06', 1),
(11, 'Employee', 53, NULL, 'SHP', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 15:29:07', 1),
(12, 'Employee', 53, NULL, 'CUS', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 15:29:43', 1),
(13, 'Employee', 53, NULL, 'HR', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 15:31:18', 1),
(14, 'Employee', 68, NULL, 'FIN', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 15:38:49', 1),
(15, 'Employee', 53, NULL, 'HR', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 16:14:48', 1),
(16, 'Employee', 53, NULL, 'ADM', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 16:28:16', 1),
(17, 'Employee', 53, NULL, 'SHP', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 17:08:03', 1),
(18, 'Employee', 53, NULL, 'SHP', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 17:08:21', 1),
(19, 'Employee', 53, NULL, 'ADM', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 18:39:48', 1),
(20, 'Employee', 53, NULL, 'DOC', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 19:57:18', 1),
(21, 'Employee', 53, NULL, 'DEV', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 19:59:33', 1),
(22, 'Employee', 53, NULL, 'HR', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 20:09:16', 1),
(23, 'Employee', 53, NULL, 'FIN', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 20:16:26', 1),
(24, 'Employee', 53, NULL, 'IT', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 20:17:28', 1),
(25, 'Employee', 53, NULL, 'DOC', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 20:18:39', 1),
(26, 'Employee', 53, NULL, 'ADM', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 20:20:03', 1),
(27, 'Employee', 53, NULL, 'FIN', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 20:20:21', 1),
(28, 'Employee', 53, NULL, 'DEV', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 20:22:30', 1),
(29, 'Employee', 53, NULL, 'SHP', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 20:51:15', 1),
(30, 'Employee', 53, NULL, 'CUS', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 20:51:31', 1),
(31, 'Employee', 53, NULL, 'HR', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 20:51:56', 1),
(32, 'Employee', 53, NULL, 'DOC', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 20:52:57', 1),
(33, 'Employee', 53, NULL, 'CRM', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-28 20:53:19', 1),
(34, 'Unknown', NULL, NULL, 'CRM', NULL, NULL, 'LOGIN_FAILURE', '2026-09-29 16:55:34', 0),
(35, 'Employee', 54, NULL, 'CRM', NULL, NULL, 'LOGIN_FAILURE', '2026-09-29 16:55:34', 0),
(36, 'Unknown', NULL, NULL, 'CRM', NULL, NULL, 'LOGIN_FAILURE', '2026-09-29 16:57:58', 0),
(37, 'Employee', 17, NULL, 'CRM', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-29 16:57:58', 1),
(38, 'Unknown', NULL, NULL, 'CRM', NULL, NULL, 'LOGIN_FAILURE', '2026-09-29 17:00:01', 0),
(39, 'Employee', 17, NULL, 'CRM', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-29 17:00:01', 1),
(40, 'Employee', 17, NULL, 'CRM', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-29 17:08:45', 1),
(41, 'Employee', 53, NULL, 'CRM', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-29 17:10:21', 1),
(42, 'Employee', 17, NULL, 'CRM', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-29 17:49:58', 1),
(43, 'Employee', 53, NULL, 'EMP', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-29 17:53:51', 1),
(44, 'Employee', 1, NULL, 'EMP', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-29 18:28:28', 1),
(45, 'Customer', NULL, 2, 'CUS', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-29 22:42:37', 1),
(46, 'Customer', NULL, 2, 'CUS', NULL, NULL, 'LOGIN_FAILURE', '2026-09-29 23:14:46', 0),
(47, 'Customer', NULL, 2, 'CUS', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-29 23:15:28', 1),
(48, 'Customer', NULL, 2, 'CUS', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-29 23:16:03', 1),
(49, 'Employee', 53, NULL, 'CUS', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-29 23:27:25', 1),
(50, 'Customer', NULL, 2, 'SHP', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-29 23:38:10', 1),
(51, 'Customer', NULL, 2, 'SHP', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-29 23:38:34', 1),
(52, 'Employee', 53, NULL, 'SHP', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-29 23:39:54', 1),
(53, 'Customer', NULL, 2, 'SHP', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-29 23:49:40', 1),
(54, 'Employee', 53, NULL, 'SHP', NULL, NULL, 'LOGIN_FAILURE', '2026-09-29 23:51:30', 0),
(55, 'Employee', 53, NULL, 'SHP', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-29 23:51:34', 1),
(56, 'Customer', NULL, 2, 'SHP', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-29 23:58:50', 1),
(57, 'Customer', NULL, 2, 'SHP', NULL, NULL, 'LOGIN_SUCCESS', '2026-09-30 00:03:37', 1);

-- --------------------------------------------------------

--
-- Table structure for table `billing_cycles`
--

CREATE TABLE `billing_cycles` (
  `cycle_id` int(11) NOT NULL,
  `prj_id` varchar(15) NOT NULL,
  `milestone_description` varchar(200) DEFAULT NULL,
  `milestone_amount` decimal(14,2) DEFAULT 0.00,
  `scheduled_date` date DEFAULT NULL,
  `invoiced` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `billing_cycles`
--

INSERT INTO `billing_cycles` (`cycle_id`, `prj_id`, `milestone_description`, `milestone_amount`, `scheduled_date`, `invoiced`) VALUES
(1, 'PRJ-2026-001', 'Initial Telemetry Architecture Blueprint Sign-Off', 0.00, '2026-02-01', 1),
(2, 'PRJ-2026-001', 'Hardware Staging & Optical Transducer Delivery', 0.00, '2026-05-15', 1),
(3, 'PRJ-2026-001', 'Final SCADA Interlock Verification & Site Commissioning', 0.00, '2026-11-30', 0),
(4, 'PRJ-2026-002', 'High-Voltage Switchgear Relays Installation', 0.00, '2026-04-10', 1),
(5, 'PRJ-2026-002', 'State Metrology Calibration Certificate Handover', 0.00, '2026-09-30', 0);

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `budget_id` int(11) NOT NULL,
  `department_code` varchar(4) DEFAULT NULL,
  `fiscal_year` int(11) DEFAULT NULL,
  `allocated_amount` decimal(14,2) DEFAULT NULL,
  `spent_amount` decimal(14,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`budget_id`, `department_code`, `fiscal_year`, `allocated_amount`, `spent_amount`) VALUES
(1, 'ENG', 2026, 3200000.00, 2150000.00),
(2, 'OPS', 2026, 4500000.00, 3120000.00),
(3, 'ITD', 2026, 1800000.00, 1290000.00),
(4, 'SAL', 2026, 1200000.00, 840000.00),
(5, 'FIN', 2026, 750000.00, 480000.00),
(6, 'HRA', 2026, 600000.00, 390000.00),
(7, 'GOV', 2026, 500000.00, 310000.00),
(8, 'EXE', 2026, 950000.00, 620000.00);

-- --------------------------------------------------------

--
-- Table structure for table `compliance_controls`
--

CREATE TABLE `compliance_controls` (
  `control_id` int(11) NOT NULL,
  `control_code` varchar(30) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `system_id` varchar(50) DEFAULT 'SYS-01..10',
  `framework` varchar(50) DEFAULT 'KAZ-CERT DIR-44',
  `custodian_emp_id` varchar(10) DEFAULT 'EMP-1005',
  `status` enum('COMPLIANT','DEVIATION','REMEDIATION') DEFAULT 'COMPLIANT',
  `evidence_ref` varchar(50) DEFAULT 'DOC-2026-015',
  `last_reviewed` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `compliance_controls`
--

INSERT INTO `compliance_controls` (`control_id`, `control_code`, `title`, `description`, `system_id`, `framework`, `custodian_emp_id`, `status`, `evidence_ref`, `last_reviewed`, `created_at`, `updated_at`) VALUES
(1, 'CTRL-CERT-01', 'Multi-Factor Authentication for Privileged IAM', 'Hardware FIDO2/WebAuthn tokens enforced for clearance level L3 and L4 across all bastions.', 'SYS-11 ADM', 'KAZ-CERT DIR-44', 'EMP-1005', 'COMPLIANT', 'DOC-2026-012', '2026-09-20', '2026-09-27 16:00:18', '2026-09-27 16:00:18'),
(2, 'CTRL-GOV-07', 'Orphaned Privileged Identifier Revocation SLA (<24h)', 'Immediate automatic suspension and key invalidation upon employee contract de-boarding.', 'SYS-03 SCADA', 'ST RK 27001-2026', 'EMP-1018', 'DEVIATION', 'NC-2026-009', '2026-09-22', '2026-09-27 16:00:18', '2026-09-27 16:00:18'),
(3, 'CTRL-SCADA-11', 'SCADA Gateway Mutual TLS Certificate Rotation', 'High-speed automated mTLS certificate rotation on optical sensor telemetry databus.', 'SYS-08 Bridge', 'IEC 62443-3-3', 'EMP-1002', 'REMEDIATION', 'TLS-CRT-7718', '2026-09-21', '2026-09-27 16:00:18', '2026-09-27 16:00:18'),
(4, 'CTRL-CERT-04', 'Cryptographic Audit Immutability across Sys 01-10', 'Continuous streaming SHA-256 Merkle tree notarization to Almaty Station Master Ledger.', 'SYS-01..11', 'KAZ-CERT DIR-44', 'EMP-1005', 'COMPLIANT', 'DOC-2026-015', '2026-09-23', '2026-09-27 16:00:18', '2026-09-27 16:00:18'),
(5, 'CTRL-NET-09', 'Air-Gapped SCADA Network Isolation & Boundary Verification', 'Dual hardware interlock physically isolating furnace SCADA relays from external IP routing.', 'SYS-01 Smelter', 'GOV-SEC-44', 'EMP-1004', 'COMPLIANT', 'DOC-2026-006', '2026-09-19', '2026-09-27 16:00:18', '2026-09-27 16:00:18'),
(6, 'CTRL-SEC-15', 'Executive Break-Glass Emergency Audit & Dual-Custody', 'Cryptographic dual-key authorization with time-limited ephemeral root leases.', 'SYS-11 ADM', 'ST RK 27001-2026', 'EMP-1001', 'COMPLIANT', 'DOC-2026-013', '2026-09-18', '2026-09-27 16:00:18', '2026-09-27 16:00:18');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `contact_id` int(11) NOT NULL,
  `cus_id` varchar(10) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`contact_id`, `cus_id`, `full_name`, `role`, `email`, `phone`) VALUES
(1, 'CUS-1001', 'Marat Zhumabayev', 'Chief Metrology Inspector', 'm.zhuma@kaztransgas.kz', '+7-7172-550101'),
(2, 'CUS-1001', 'Gulnara Sadykova', 'Procurement Director', 'g.sadyk@kaztransgas.kz', '+7-7172-550102'),
(3, 'CUS-1002', 'Daulet Smagulov', 'SCADA Systems Architect', 'd.smagulov@samruk.kz', '+7-7172-790201'),
(4, 'CUS-1003', 'Elena Kim', 'Operations Director', 'e.kim@kazzinc.com', '+7-7232-291000'),
(5, 'CUS-1004', 'Oleg Morozov', 'Substation Automation Lead', 'o.morozov@kegoc.kz', '+7-7172-690011'),
(6, 'CUS-1005', 'Yerbol Akhmetov', 'Drilling Equipment Lead', 'y.akhmetov@kazmunaigas.kz', '+7-7172-786000'),
(7, 'CUS-1011', 'Major Test Contact', 'Primary Representative', 'contact@testcorp.ru', '+7 (495) 999-0011'),
(8, 'CUS-1013', 'Major Test Contact', 'Primary Representative', 'contact@testcorp.ru', '+7 (495) 999-0011'),
(9, 'CUS-1014', 'Igor Chemist', 'Primary Representative', 'igor@directtest.ru', ''),
(10, 'CUS-1015', 'Igor Chemist', 'Primary Representative', 'igor@directtest.ru', ''),
(11, 'CUS-1016', 'Dynamic Lead', 'Procurement Contact', 'dyn@test.ru', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `contract_id` int(11) NOT NULL,
  `cus_id` varchar(10) DEFAULT NULL,
  `prj_id` varchar(15) DEFAULT NULL,
  `opp_id` int(11) DEFAULT NULL,
  `contract_value` decimal(14,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `doc_id` varchar(15) DEFAULT NULL,
  `contract_ref` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `contract_type` varchar(50) DEFAULT 'MSA',
  `eds_status` varchar(50) DEFAULT 'Counter-Signed',
  `confidentiality_level` varchar(50) DEFAULT 'Restricted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contracts`
--

INSERT INTO `contracts` (`contract_id`, `cus_id`, `prj_id`, `opp_id`, `contract_value`, `start_date`, `end_date`, `status`, `doc_id`, `contract_ref`, `title`, `contract_type`, `eds_status`, `confidentiality_level`) VALUES
(1, 'CUS-1001', 'PRJ-VP-7721', NULL, 14250000.00, '2024-01-01', '2026-12-31', 'Active', NULL, 'MSA-2024-SVST-088', 'Master Automation Equipment & SCADA Services Agreement', 'MSA', 'Counter-Signed', 'Restricted'),
(2, 'CUS-1003', 'PRJ-VP-6610', NULL, 8400000.00, '2023-11-15', '2025-11-14', 'Active', NULL, 'MSA-2023-NN-014', 'Talnakh Concentrator Multi-Year Telemetry & Field Sensor MSA', 'MSA', 'Counter-Signed', 'Restricted'),
(3, 'CUS-1002', 'PRJ-VP-5520', NULL, 4200000.00, '2024-02-01', '2026-10-31', 'Active', NULL, 'MSA-2024-NLMK-090', 'Blast Furnace & Strip Mill Telemetry Integration Agreement', 'MSA', 'Counter-Signed', 'Restricted'),
(4, 'CUS-1001', 'PRJ-VP-7721', NULL, 850000.00, '2024-01-01', '2025-12-31', 'Active', NULL, 'SLA-2024-TIER1', '24/7 Field Engineering Specialist & Incident SLA Support Coverage', 'SLA', 'Active SLA', 'Standard'),
(5, 'CUS-1001', NULL, NULL, 0.00, '2023-12-01', '2028-12-01', 'Active', NULL, 'NDA-VP-SVR-90214', 'Bilateral Non-Disclosure Agreement for Industrial Proprietary Telemetry', 'NDA', 'Electronic Seal', 'Strict'),
(6, 'CUS-1004', 'PRJ-VP-4411', NULL, 2100000.00, '2024-03-01', '2026-03-01', 'Active', NULL, 'MSA-2024-EVRAZ-033', 'Rail Mill Flaw Detection & Laser Profiler Deployment MSA', 'MSA', 'Counter-Signed', 'Restricted'),
(7, 'CUS-1005', NULL, NULL, 1950000.00, '2024-04-15', '2026-04-15', 'Active', NULL, 'MSA-2024-PHOS-019', 'Fertilizer Complex Flowmeter & Telemetry Master Contract', 'MSA', 'Counter-Signed', 'Standard'),
(8, 'CUS-1001', NULL, NULL, 1250000.00, '2026-09-29', '2028-09-29', 'Active', NULL, 'MSA-2026-082D19', 'Automated Integration Testing MSA', 'MSA', 'Counter-Signed', 'Restricted'),
(9, 'CUS-1001', NULL, NULL, 1250000.00, '2026-09-29', '2028-09-29', 'Active', NULL, 'MSA-2026-98FC21', 'Automated Integration Testing MSA', 'MSA', 'Counter-Signed', 'Restricted'),
(10, 'CUS-1001', NULL, NULL, 750000.00, '2026-09-29', '2028-09-29', 'Active', NULL, 'MSA-2026-9A84B0', 'Direct Test SLA Agreement', 'MSA', 'Counter-Signed', 'Restricted'),
(100000, 'CUS-1001', NULL, NULL, 750000.00, '2026-09-29', '2028-09-29', 'Active', NULL, 'MSA-2026-640619', 'Direct Test SLA Agreement', 'MSA', 'Counter-Signed', 'Restricted');

-- --------------------------------------------------------

--
-- Table structure for table `crm_activities`
--

CREATE TABLE `crm_activities` (
  `activity_id` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL DEFAULT 'Meeting',
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `cus_id` varchar(10) DEFAULT NULL,
  `opp_id` int(11) DEFAULT NULL,
  `emp_id` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_activities`
--

INSERT INTO `crm_activities` (`activity_id`, `activity_type`, `title`, `description`, `cus_id`, `opp_id`, `emp_id`, `created_at`) VALUES
(1, 'Contract', 'Master Contract MSA-2024-SVST Counter-Signed', 'P. V. Cherepanov (Severstal VP Proc.) ratified the 3-Year Automation SLA.', 'CUS-1001', 5, 'EMP-1006', '2026-09-29 19:25:06'),
(2, 'Opportunity', 'Opportunity Advanced to Negotiation', 'Talnakh Concentrator Flotation Telemetry ($2.40M) passed Phase 2 FAT review.', 'CUS-1003', 6, 'EMP-1007', '2026-09-29 19:25:06'),
(3, 'Quote', 'Engineering Quotation Delivered (#QUO-9912)', 'Spec sheet for 16x High-Pressure Flowmeters HPF-900X dispatched to NLMK Lipetsk.', 'CUS-1002', 3, 'EMP-1010', '2026-09-29 19:25:06'),
(4, 'Meeting', 'On-Site Technical Audit Completed', 'Dr. Elena Rostova completed sensor calibration walkthrough at Cherepovets Blast Furnace #5.', 'CUS-1001', 5, 'EMP-1006', '2026-09-29 19:25:06'),
(7, 'Contract', 'Master Contract MSA-2024-SVST Counter-Signed', 'P. V. Cherepanov (Severstal VP Proc.) ratified the 3-Year Automation SLA.', 'CUS-1001', 5, 'EMP-1006', '2026-09-29 16:52:51'),
(8, 'Opportunity', 'Opportunity Advanced to Negotiation', 'Talnakh Concentrator Flotation Telemetry ($2.40M) passed Phase 2 FAT review.', 'CUS-1003', 6, 'EMP-1007', '2026-09-29 16:52:51'),
(9, 'Quote', 'Engineering Quotation Delivered (#QUO-9912)', 'Spec sheet for 16x High-Pressure Flowmeters HPF-900X dispatched to NLMK Lipetsk.', 'CUS-1002', 3, 'EMP-1010', '2026-09-29 16:52:51'),
(10, 'Meeting', 'On-Site Technical Audit Completed', 'Dr. Elena Rostova completed sensor calibration walkthrough at Cherepovets Blast Furnace #5.', 'CUS-1001', 5, 'EMP-1006', '2026-09-29 16:52:51'),
(11, 'Account', 'Account Record Saved', 'Enterprise account API Test Industrial Corp (CUS-1011) registered in system database.', 'CUS-1011', NULL, 'EMP-1006', '2026-09-29 16:55:23'),
(12, 'Account', 'Account Updated', 'Account CUS-1001 updated with new commercial specifications.', 'CUS-1001', NULL, 'EMP-1006', '2026-09-29 16:55:23'),
(13, 'Lead', 'Inbound Lead Registered', 'New lead #7 (Ural Test Turbine LLC - Viktor Turbinevich) logged into database.', NULL, NULL, 'EMP-1006', '2026-09-29 16:55:23'),
(14, 'Conversion', 'Lead Converted to Customer Account', 'Lead #2 converted into Account Magnitogorsk Iron & Steel Works (CUS-1008) & Opportunity #12.', 'CUS-1008', 12, 'EMP-1006', '2026-09-29 16:55:23'),
(15, 'Opportunity', 'New Opportunity Created', 'Logged new opportunity #13: API Automated Opp Validation ($320,000.00) in Proposal stage.', 'CUS-1001', 13, 'EMP-1006', '2026-09-29 16:55:23'),
(16, 'Opportunity', 'Opportunity Stage Updated', 'Opportunity #1 transitioned to pipeline stage: Negotiation.', NULL, 1, 'EMP-1006', '2026-09-29 16:55:23'),
(17, 'Contract', 'Contract Ratified & Executed', 'New contract MSA-2026-082D19 (Automated Integration Testing MSA) valued at $1,250,000.00 executed in DB.', 'CUS-1001', NULL, 'EMP-1006', '2026-09-29 16:55:23'),
(18, 'Quote', 'Commercial Quotation Issued', 'Commercial quotation QUO-9088 ($420,000.00) delivered to CUS-1001.', 'CUS-1001', NULL, 'EMP-1006', '2026-09-29 16:55:23'),
(19, 'Meeting', 'Executive Strategic Review', 'Automated test suite verification note', 'CUS-1001', NULL, 'EMP-1006', '2026-09-29 16:55:24'),
(20, 'Account', 'Account Record Saved', 'Enterprise account Temporary Deletion Test Corp (CUS-1012) registered in system database.', 'CUS-1012', NULL, 'EMP-1006', '2026-09-29 16:58:08'),
(21, 'Lead', 'Inbound Lead Registered', 'New lead #8 (Temporary Deletion Lead - Temp Tester) logged into database.', NULL, NULL, 'EMP-1006', '2026-09-29 16:58:08'),
(22, 'Opportunity', 'New Opportunity Created', 'Logged new opportunity #14: Temporary Deletion Opp ($150,000.00) in Proposal stage.', 'CUS-1001', 14, 'EMP-1006', '2026-09-29 16:58:09'),
(24, 'Account', 'Account Removed', 'Customer account CUS-1013 was removed from the ledger.', NULL, NULL, 'EMP-1006', '2026-09-29 16:58:24'),
(25, 'Lead', 'Inbound Lead Registered', 'New lead #9 (Temporary Deletion Lead - Temp Tester) logged into database.', NULL, NULL, 'EMP-1006', '2026-09-29 16:58:24'),
(26, 'Opportunity', 'New Opportunity Created', 'Logged new opportunity #15: Temporary Deletion Opp ($150,000.00) in Proposal stage.', 'CUS-1001', 15, 'EMP-1006', '2026-09-29 16:58:24'),
(27, 'Account', 'Account Record Saved', 'Enterprise account API Test Industrial Corp (CUS-1013) registered in system database.', 'CUS-1013', NULL, 'EMP-1006', '2026-09-29 16:59:57'),
(28, 'Account', 'Account Updated', 'Account CUS-1001 updated with new commercial specifications.', 'CUS-1001', NULL, 'EMP-1006', '2026-09-29 16:59:57'),
(29, 'Lead', 'Inbound Lead Registered', 'New lead #10 (Ural Test Turbine LLC - Viktor Turbinevich) logged into database.', NULL, NULL, 'EMP-1006', '2026-09-29 16:59:57'),
(30, 'Conversion', 'Lead Converted to Customer Account', 'Lead #2 converted into Account Magnitogorsk Iron & Steel Works (CUS-1008) & Opportunity #16.', 'CUS-1008', 16, 'EMP-1006', '2026-09-29 16:59:57'),
(31, 'Opportunity', 'New Opportunity Created', 'Logged new opportunity #17: API Automated Opp Validation ($320,000.00) in Proposal stage.', 'CUS-1001', 17, 'EMP-1006', '2026-09-29 16:59:58'),
(32, 'Opportunity', 'Opportunity Stage Updated', 'Opportunity #1 transitioned to pipeline stage: Negotiation.', NULL, 1, 'EMP-1006', '2026-09-29 16:59:58'),
(33, 'Contract', 'Contract Ratified & Executed', 'New contract MSA-2026-98FC21 (Automated Integration Testing MSA) valued at $1,250,000.00 executed in DB.', 'CUS-1001', NULL, 'EMP-1006', '2026-09-29 16:59:58'),
(34, 'Quote', 'Commercial Quotation Issued', 'Commercial quotation QUO-9800 ($420,000.00) delivered to CUS-1001.', 'CUS-1001', NULL, 'EMP-1006', '2026-09-29 16:59:58'),
(35, 'Meeting', 'Executive Strategic Review', 'Automated test suite verification note', 'CUS-1001', NULL, 'EMP-1006', '2026-09-29 16:59:58'),
(36, 'Account', 'Account Record Saved', 'Enterprise account Direct API Test Corp (CUS-1014) registered in system database.', 'CUS-1014', NULL, 'EMP-1006', '2026-09-29 17:08:32'),
(37, 'Lead', 'Inbound Lead Registered', 'New lead #11 (Direct Lead Facility - Alexey Leadman) logged into database.', NULL, NULL, 'EMP-1006', '2026-09-29 17:08:32'),
(38, 'Conversion', 'Lead Converted to Customer Account', 'Lead #3 converted into Account Uralchem Mineral Fertilizers (CUS-1009) & Opportunity #18.', 'CUS-1009', 18, 'EMP-1006', '2026-09-29 17:08:32'),
(39, 'Opportunity', 'New Opportunity Created', 'Logged new opportunity #19: Direct Opp Validation ($280,000.00) in Proposal stage.', 'CUS-1001', 19, 'EMP-1006', '2026-09-29 17:08:32'),
(40, 'Opportunity', 'Opportunity Stage Updated', 'Opportunity #2 transitioned to pipeline stage: Negotiation.', NULL, 2, 'EMP-1006', '2026-09-29 17:08:33'),
(41, 'Contract', 'Contract Ratified & Executed', 'New contract MSA-2026-9A84B0 (Direct Test SLA Agreement) valued at $750,000.00 executed in DB.', 'CUS-1001', NULL, 'EMP-1006', '2026-09-29 17:08:33'),
(42, 'Quote', 'Commercial Quotation Issued', 'Commercial quotation QUO-9005 ($350,000.00) delivered to CUS-1001.', 'CUS-1001', NULL, 'EMP-1006', '2026-09-29 17:08:33'),
(43, 'Meeting', 'Direct Commercial Review Meeting', 'Tested direct endpoint logging', 'CUS-1001', NULL, 'EMP-1006', '2026-09-29 17:08:33'),
(44, 'Account', 'Account Record Saved', 'Enterprise account Direct API Test Corp (CUS-1015) registered in system database.', 'CUS-1015', NULL, 'EMP-1006', '2026-09-29 17:09:16'),
(45, 'Lead', 'Inbound Lead Registered', 'New lead #100000 (Direct Lead Facility - Alexey Leadman) logged into database.', NULL, NULL, 'EMP-1006', '2026-09-29 17:09:16'),
(46, 'Opportunity', 'New Opportunity Created', 'Logged new opportunity #100000: Direct Opp Validation ($280,000.00) in Proposal stage.', 'CUS-1001', 100000, 'EMP-1006', '2026-09-29 17:09:16'),
(47, 'Opportunity', 'Opportunity Stage Updated', 'Opportunity #2 transitioned to pipeline stage: Negotiation.', NULL, 2, 'EMP-1006', '2026-09-29 17:09:16'),
(48, 'Contract', 'Contract Ratified & Executed', 'New contract MSA-2026-640619 (Direct Test SLA Agreement) valued at $750,000.00 executed in DB.', 'CUS-1001', NULL, 'EMP-1006', '2026-09-29 17:09:17'),
(49, 'Quote', 'Commercial Quotation Issued', 'Commercial quotation QUO-9936 ($350,000.00) delivered to CUS-1001.', 'CUS-1001', NULL, 'EMP-1006', '2026-09-29 17:09:17'),
(50, 'Meeting', 'Direct Commercial Review Meeting', 'Tested direct endpoint logging', 'CUS-1001', NULL, 'EMP-1006', '2026-09-29 17:09:17'),
(51, 'Conversion', 'Lead Converted to Customer Account', 'Lead #100001 converted into Account Dynamic Test Co (CUS-1016) & Opportunity #100001.', 'CUS-1016', 100001, 'EMP-1006', '2026-09-29 17:09:27'),
(52, 'Document', 'Document Uploaded', 'Uploaded document: test_sample.txt (0 KB)', 'CUS-1001', NULL, 'EMP-1006', '2026-09-29 17:32:54');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `cus_id` varchar(10) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `primary_contact_name` varchar(150) DEFAULT NULL,
  `primary_contact_email` varchar(150) DEFAULT NULL,
  `account_manager_emp_id` varchar(10) DEFAULT NULL,
  `onboarded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(50) DEFAULT NULL,
  `headquarters` varchar(255) DEFAULT NULL,
  `tax_id` varchar(50) DEFAULT NULL,
  `health_score` int(11) DEFAULT 95,
  `account_tier` varchar(50) DEFAULT 'Enterprise',
  `status` varchar(50) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`cus_id`, `company_name`, `sector`, `primary_contact_name`, `primary_contact_email`, `account_manager_emp_id`, `onboarded_at`, `phone`, `headquarters`, `tax_id`, `health_score`, `account_tier`, `status`) VALUES
('CUS-1001', 'Severstal Metallurgy PJSC', 'Heavy Industry & Metallurgy', 'P. V. Cherepanov', 'procurement@severstal.com', 'EMP-1006', '2026-09-19 10:35:40', '+7 (8202) 53-88-00', 'Mira Street 30, Cherepovets, Vologda Region, 162600, Russian Federation', 'INN 3528000597', 94, 'Strategic Tier-1', 'Active'),
('CUS-1002', 'Novolipetsk Steel (NLMK)', 'Ferrous Metallurgy', 'Alexei Voronov', 'automation@nlmk.com', 'EMP-1010', '2026-09-19 10:35:40', '+7 (4742) 44-4009', 'Lipetsk, Lipetsk Oblast', 'INN 4823006703', 91, 'Tier-1 Enterprise', 'Active'),
('CUS-1003', 'Norilsk Nickel Mining', 'Non-Ferrous Mining', 'Dmitry Kiselev', 'telemetry@nornickel.ru', 'EMP-1007', '2026-09-19 10:35:40', '+7 (3919) 25-1100', 'Talnakh / Norilsk, Krasnoyarsk Krai', 'INN 8401005730', 95, 'Strategic Tier-1', 'Active'),
('CUS-1004', 'EVRAZ Consolidated', 'Ferrous Metallurgy', 'Maxim Gusev', 'm.gusev@evraz.com', 'EMP-1008', '2026-09-19 10:35:40', '+7 (3435) 49-7000', 'Nizhny Tagil, Sverdlovsk Oblast', 'INN 7715047418', 88, 'Tier-2 Enterprise', 'Active'),
('CUS-1005', 'PhosAgro Chemical', 'Chemical & Agro', 'Sergey Belyakov', 's.belyakov@phosagro.ru', 'EMP-1006', '2026-09-19 10:35:40', '+7 (8153) 15-5000', 'Apatity, Murmansk Oblast', 'INN 7725542282', 96, 'Tier-2 Enterprise', 'Active'),
('CUS-1006', 'Gazprom Neft Omsk', 'Oil & Gas', 'Roman Danilov', 'danilov.ra@omsk.gazprom-neft.ru', 'EMP-1007', '2026-09-19 10:35:40', '+7 (3812) 69-0000', 'Omsk, Omsk Oblast', 'INN 5504036333', 90, 'Strategic Tier-1', 'Active'),
('CUS-1007', 'Chelyabinsk Pipe Plant', 'Ferrous Metallurgy', 'Dr. Andrei Vasiliev', 'a.vasiliev@chelpipe.ru', 'EMP-1010', '2026-09-19 10:35:40', '+7 (351) 259-8801', 'Chelyabinsk, Chelyabinsk Oblast', 'INN 7451010323', 94, 'Tier-1 Enterprise', 'Active'),
('CUS-1008', 'Magnitogorsk Iron & Steel Works', 'Ferrous Metallurgy', 'Valery Semenov', 'v.semenov@mmk.ru', 'EMP-1008', '2026-09-19 10:35:40', '+7 (3519) 24-0012', 'Magnitogorsk, Chelyabinsk Oblast', 'INN 7414003638', 91, 'Strategic Tier-1', 'Active'),
('CUS-1009', 'Uralchem Mineral Fertilizers', 'Chemical & Agro', 'Irina Kondratieva', 'i.kondratieva@uralchem.com', 'EMP-1006', '2026-09-19 10:35:40', '+7 (342) 219-4400', 'Perm, Perm Krai', 'INN 7703647595', 89, 'Tier-2 Enterprise', 'Active'),
('CUS-1010', 'Central Rail Diagnostics', 'Railway Infrastructure', 'Konstantin Titov', 'titov@rail-diagnostics.ru', 'EMP-1007', '2026-09-19 10:35:40', '+7 (499) 262-9901', 'Moscow, Central District', 'INN 7708503727', 85, 'Tier-2 Enterprise', 'Active'),
('CUS-1011', 'API Test Industrial Corp', 'Aerospace Defense', 'Major Test Contact', 'contact@testcorp.ru', 'EMP-1006', '2026-09-29 16:55:23', '+7 (495) 999-0011', 'Moscow, RU', '', 95, 'Tier-1 Enterprise', 'Active'),
('CUS-1012', 'Temporary Deletion Test Corp', 'Chemical Refining', '', '', 'EMP-1006', '2026-09-29 16:58:08', '', 'Moscow, RU', '', 95, 'Tier-1 Enterprise', 'Active'),
('CUS-1013', 'API Test Industrial Corp', 'Aerospace Defense', 'Major Test Contact', 'contact@testcorp.ru', 'EMP-1006', '2026-09-29 16:59:57', '+7 (495) 999-0011', 'Moscow, RU', '', 95, 'Tier-1 Enterprise', 'Active'),
('CUS-1014', 'Direct API Test Corp', 'Chemical Refining', 'Igor Chemist', 'igor@directtest.ru', 'EMP-1006', '2026-09-29 17:08:32', '', 'Moscow, RU', '', 95, 'Tier-1 Enterprise', 'Active'),
('CUS-1015', 'Direct API Test Corp', 'Chemical Refining', 'Igor Chemist', 'igor@directtest.ru', 'EMP-1006', '2026-09-29 17:09:16', '', 'Moscow, RU', '', 95, 'Tier-1 Enterprise', 'Active'),
('CUS-1016', 'Dynamic Test Co', 'Industrial Automation', 'Dynamic Lead', 'dyn@test.ru', 'EMP-1006', '2026-09-29 17:09:27', NULL, NULL, NULL, 90, 'Tier-1 Enterprise', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `customer_accounts`
--

CREATE TABLE `customer_accounts` (
  `account_id` int(11) NOT NULL,
  `cus_id` varchar(10) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `mfa_enabled` tinyint(1) DEFAULT 0,
  `status` varchar(20) DEFAULT 'Active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_accounts`
--

INSERT INTO `customer_accounts` (`account_id`, `cus_id`, `username`, `email`, `password_hash`, `mfa_enabled`, `status`, `last_login`, `created_at`) VALUES
(1, 'CUS-1001', 'sergei.makarov', 's.makarov@aral-geomatics.kz', '$2y$10$eE0m1VvQ11a0u.J0PZ0XxeE/y/eG5zS8vM/oU29Vq4kY1Q1qG9r7m', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(2, 'CUS-1001', 'CLT-77210', 'client77210@vostokpribor.local', '$2y$10$eE0m1VvQ11a0u.J0PZ0XxeE/y/eG5zS8vM/oU29Vq4kY1Q1qG9r7m', 0, 'Active', '2026-09-30 00:03:37', '2026-09-21 16:39:04'),
(3, 'CUS-1001', 'CUS-1001', 'cus1001@aral-geomatics.kz', '$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(4, 'CUS-1002', 'kristaps.ozols', 'k.ozols@baltnord-systems.eu', '$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(5, 'CUS-1002', 'SHP-VP-11', 'b2b-buyer11@baltnord.eu', '$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(6, 'CUS-1002', 'CUS-1002', 'cus1002@baltnord.eu', '$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(7, 'CUS-1003', 'yerlan.bektemis', 'y.bektemis@steppemining.kz', '$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(8, 'CUS-1003', 'CUS-1003', 'cus1003@steppemining.kz', '$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(9, 'CUS-1004', 'lukas.brandt', 'l.brandt@rheinwerk-inst.de', '$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(10, 'CUS-1004', 'CUS-1004', 'cus1004@rheinwerk.de', '$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(11, 'CUS-1005', 'dilshod.karim', 'd.karim@tashkent-precision.uz', '$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(12, 'CUS-1005', 'CUS-1005', 'cus1005@tashkent-precision.uz', '$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(13, 'CUS-1006', 'mara.kalnina', 'm.kalnina@daugava-optical.lv', '$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(14, 'CUS-1007', 'murad.safarov', 'm.safarov@caspian-robotics.az', '$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(15, 'CUS-1008', 'oleg.petrenko', 'o.petrenko@eurasia-water.ua', '$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(16, 'CUS-1009', 'ainur.sadyk', 'a.sadyk@altai-env.kz', '$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(17, 'CUS-1010', 'tomas.varga', 't.varga@central-rail.hu', '$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq', 0, 'Active', NULL, '2026-09-21 16:39:04');

-- --------------------------------------------------------

--
-- Table structure for table `customer_pricing`
--

CREATE TABLE `customer_pricing` (
  `cus_id` varchar(10) NOT NULL,
  `prod_id` varchar(10) NOT NULL,
  `special_price` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_pricing`
--

INSERT INTO `customer_pricing` (`cus_id`, `prod_id`, `special_price`) VALUES
('CUS-1001', 'PROD-1001', 4850.00),
('CUS-1001', 'PROD-1002', 3200.00),
('CUS-1001', 'PROD-1003', 45000.00),
('CUS-1001', 'PROD-1004', 18500.00),
('CUS-1001', 'PROD-1005', 2100.00),
('CUS-1002', 'PROD-1001', 4950.00),
('CUS-1002', 'PROD-1003', 1850.00),
('CUS-1003', 'PROD-1004', 4200.00),
('CUS-1004', 'PROD-1005', 850.00),
('CUS-1005', 'PROD-1001', 2100.00);

-- --------------------------------------------------------

--
-- Table structure for table `customer_service_requests`
--

CREATE TABLE `customer_service_requests` (
  `request_id` varchar(20) NOT NULL,
  `cus_id` varchar(10) NOT NULL,
  `service_type` varchar(100) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `facility_location` varchar(200) DEFAULT NULL,
  `priority` enum('Standard','High','Critical') DEFAULT 'Standard',
  `requested_date` date DEFAULT NULL,
  `status` enum('Submitted','Under Review','Personnel Assigned','In Progress','Completed','Cancelled') DEFAULT 'Submitted',
  `assigned_emp_id` varchar(10) DEFAULT NULL,
  `assigned_at` datetime DEFAULT NULL,
  `hr_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_service_requests`
--

INSERT INTO `customer_service_requests` (`request_id`, `cus_id`, `service_type`, `title`, `description`, `facility_location`, `priority`, `requested_date`, `status`, `assigned_emp_id`, `assigned_at`, `hr_notes`, `created_at`, `updated_at`) VALUES
('SRV-2026-001', 'CUS-1001', 'Calibration & Metrology', 'High-Pressure Turbine Sensor On-Site Recalibration', 'Annual recalibration for 14 VP-SPT-900 pressure transmitters in Bay 3.', 'Atyrau Refining Facility, Unit 4', 'High', '2026-10-15', 'Personnel Assigned', 'EMP-1004', '2026-09-28 10:30:00', 'Assigned Senior Metrologist Elena Morozova for dispatch.', '2026-09-27 11:20:00', '2026-09-30 00:23:28'),
('SRV-2026-002', 'CUS-1002', 'Telemetry & SCADA Integration', 'Modbus TCP Telemetry Gateway Firmware Commissioning', 'Integrate new fiber optic telemetry gateways with remote monitoring station.', 'Aktau Maritime Terminal, Berth 2', 'Critical', '2026-10-08', 'Under Review', NULL, NULL, 'Pending HR engineer assignment based on offshore clearance.', '2026-09-29 06:15:00', '2026-09-30 00:23:28'),
('SRV-2026-003', 'CUS-1003', 'Preventative Maintenance', 'Biannual Flow Meter Diagnostic & Seal Inspection', 'Routine preventative diagnostic on ultrasonic flow measurement lines.', 'Karaganda Metallurgy Complex', 'Standard', '2026-10-22', 'Submitted', NULL, NULL, 'Queued for operational schedule review.', '2026-09-29 13:40:00', '2026-09-30 00:23:28'),
('SRV-2026-004', 'CUS-1001', 'Calibration & Metrology', 'Test Automated Pressure Calibration Request', 'Automated test suite service verification.', 'Test Atyrau Facility Bay 2', 'Critical', '2026-10-05', 'Submitted', NULL, NULL, NULL, '2026-09-30 00:32:16', '2026-09-30 00:32:16');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `dept_code` varchar(4) NOT NULL,
  `dept_name` varchar(100) NOT NULL,
  `main_function` text DEFAULT NULL,
  `employee_count_target` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`dept_code`, `dept_name`, `main_function`, `employee_count_target`) VALUES
('ENG', 'Engineering & Design', 'Product R&D, CAD schematics, and mechanical validation', 16),
('EXE', 'Executive Leadership', 'Corporate strategy, capital allocation, and compliance oversight', 5),
('FIN', 'Finance & Billing', 'Billing, Payments, Accounting, and Reconciliation', 10),
('GOV', 'Governance, Risk & Compliance', 'Compliance, Audit, Risk Management, and Information Security', 6),
('HRA', 'Human Resources & Admin', 'Recruitment, Personnel Affairs, and Facilities', 8),
('IT', 'Information Technology', 'Infrastructure, cybersecurity, ERP operations, and API integrations', 15),
('ITD', 'Information Technology', 'Infrastructure, Support, Security, and System Management', 14),
('LOG', 'Supply Chain & Logistics', 'Procurement, inventory tracking, and warehouse dispatch', 30),
('OPS', 'Operations & Logistics', 'Procurement, Inventory, Shipping, and Execution', 20),
('QA', 'Quality Assurance & Testing', 'ISO calibration, endurance testing, and defect inspection', 20),
('SAL', 'Sales & Enterprise Relations', 'Global pipeline, enterprise contract negotiation, and customer success', 16);

-- --------------------------------------------------------

--
-- Table structure for table `department_boards`
--

CREATE TABLE `department_boards` (
  `board_post_id` int(11) NOT NULL,
  `department_code` varchar(4) DEFAULT NULL,
  `topic` varchar(200) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `posted_by_emp_id` varchar(10) DEFAULT NULL,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `department_boards`
--

INSERT INTO `department_boards` (`board_post_id`, `department_code`, `topic`, `content`, `posted_by_emp_id`, `posted_at`) VALUES
(1, 'ADM', 'Q3 Statutory Audit Protocol & Compliance Schedule', 'Executive briefing materials for National Accreditation Center audit are available on file server.', 'EMP-1005', '2026-09-15 05:30:00'),
(2, 'ENG', 'SCADA Sensor Firmware v4.9.1 Rollout Notice', 'Optical latency patches must be applied to all Karaganda relays before end of month.', 'EMP-1002', '2026-09-18 07:00:00'),
(3, 'IT', 'Scheduled Bastion SSH Key Rotation', 'All L3+ engineers are reminded to complete hardware token MFA re-enrollment.', 'EMP-1018', '2026-09-20 11:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `developer_api_keys`
--

CREATE TABLE `developer_api_keys` (
  `id` int(11) NOT NULL,
  `key_identifier` varchar(50) NOT NULL,
  `label` varchar(150) NOT NULL,
  `partner_id` varchar(50) NOT NULL DEFAULT 'CUS-1002',
  `partner_name` varchar(150) NOT NULL DEFAULT 'BaltNord Process Systems',
  `token_prefix` varchar(32) NOT NULL,
  `token_full` varchar(255) NOT NULL,
  `environment` enum('Production','Sandbox','Staging') NOT NULL DEFAULT 'Production',
  `rate_limit` varchar(50) NOT NULL DEFAULT '10,000 req/min',
  `rate_limit_value` int(11) NOT NULL DEFAULT 10000,
  `classification` varchar(50) NOT NULL DEFAULT 'Confidential',
  `scopes` varchar(255) NOT NULL DEFAULT 'telemetry:read,scada:ingest',
  `status` enum('Active','Revoked','Suspended') NOT NULL DEFAULT 'Active',
  `usage_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `developer_api_keys`
--

INSERT INTO `developer_api_keys` (`id`, `key_identifier`, `label`, `partner_id`, `partner_name`, `token_prefix`, `token_full`, `environment`, `rate_limit`, `rate_limit_value`, `classification`, `scopes`, `status`, `usage_count`, `created_at`, `expires_at`) VALUES
(1, 'KEY-9842', 'BaltNord Primary ERP Sync', 'CUS-1002', 'BaltNord Process Systems', 'vk_live_9a41c2e8', 'vk_live_9a41c2e8f10b7a89d4e12c5b38af1009', 'Production', '10,000 req/min', 10000, 'Confidential', 'telemetry:read,scada:ingest,orders:write', 'Active', 42890, '2026-09-27 15:51:21', NULL),
(2, 'KEY-4109', 'BaltNord QA / Sandbox Ingestion', 'CUS-1002', 'BaltNord Process Systems', 'vk_test_3f7b99c1', 'vk_test_3f7b99c1e04a88bc92d110fc6e7a2014', 'Sandbox', '2,500 req/min', 2500, 'Internal QA', 'telemetry:read,scada:ingest', 'Active', 12450, '2026-09-27 15:51:21', NULL),
(3, 'KEY-4419', 'IoT Sensor Continuous Telemetry Pipeline', 'CUS-1001', 'Aral Geomatics Automation Labs', 'vk_live_7e810a9c', 'vk_live_7e810a9cf29188e7b4119d45e99aa871', 'Production', '50,000 req/min', 50000, 'Confidential', 'telemetry:read', 'Active', 382100, '2026-09-27 15:51:21', NULL),
(4, 'KEY-1108', 'Almaty Logistics Inbound Feeder', 'CUS-1003', 'Steppe Mining SCADA Engineering', 'vk_live_2b9044cc', 'vk_live_2b9044cc4e1178a9c2288019aa673199', 'Production', '10,000 req/min', 10000, 'Internal', 'scada:ingest', 'Active', 14350, '2026-09-27 15:51:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `developer_endpoints`
--

CREATE TABLE `developer_endpoints` (
  `id` int(11) NOT NULL,
  `endpoint_slug` varchar(64) NOT NULL,
  `method` varchar(10) NOT NULL DEFAULT 'GET',
  `path` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `classification` varchar(64) NOT NULL DEFAULT 'Internal',
  `rate_limit` varchar(50) NOT NULL DEFAULT '10k/min',
  `target_hardware` varchar(50) NOT NULL DEFAULT 'PROD-1001',
  `parameters_json` longtext DEFAULT NULL,
  `curl_snippet` text DEFAULT NULL,
  `python_snippet` text DEFAULT NULL,
  `node_snippet` text DEFAULT NULL,
  `go_snippet` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `developer_endpoints`
--

INSERT INTO `developer_endpoints` (`id`, `endpoint_slug`, `method`, `path`, `title`, `description`, `classification`, `rate_limit`, `target_hardware`, `parameters_json`, `curl_snippet`, `python_snippet`, `node_snippet`, `go_snippet`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'endpoint-optical', 'GET', '/v1/sensors/optical/telemetry', 'PROD-1001 Optical Sensor Package Telemetry', 'Retrieves high-frequency telemetry streams from field-deployed optical inspection and sensor apparatus (PROD-1001), including spectral resolution peak, focal plane operating temperature, and signal-to-noise ratio.', 'Internal • PROD-1001', '10k/min', 'PROD-1001', '[{\"name\":\"device_id\",\"type\":\"string\",\"required\":true,\"description\":\"Assigned hardware serial or ID (e.g. PROD-1001-KZ)\"},{\"name\":\"sample_window_sec\",\"type\":\"integer\",\"required\":false,\"description\":\"Window for rolling average (1 to 60, default: 5)\"}]', 'curl -X GET \"https://developer.vostokpribor.local/v1/sensors/optical/telemetry?device_id=PROD-1001-KZ\" \\\n  -H \"Authorization: Bearer vk_live_9a41c2e8f10b\" \\\n  -H \"Accept: application/json\"', 'import requests\n\nurl = \"https://developer.vostokpribor.local/v1/sensors/optical/telemetry\"\nheaders = {\n    \"Authorization\": \"Bearer vk_live_9a41c2e8f10b\",\n    \"Accept\": \"application/json\"\n}\nparams = {\"device_id\": \"PROD-1001-KZ\"}\n\nresponse = requests.get(url, headers=headers, params=params)\ndata = response.json()\nprint(\"Wavelength Peak (nm):\", data[\"spectral_resolution_nm\"])', 'const fetch = require(\"node-fetch\");\n\nasync function getOpticalTelemetry() {\n  const url = new URL(\"https://developer.vostokpribor.local/v1/sensors/optical/telemetry\");\n  url.searchParams.set(\"device_id\", \"PROD-1001-KZ\");\n\n  const res = await fetch(url, {\n    headers: {\n      \"Authorization\": \"Bearer vk_live_9a41c2e8f10b\",\n      \"Accept\": \"application/json\"\n    }\n  });\n  const data = await res.json();\n  console.log(data);\n}\ngetOpticalTelemetry();', 'package main\n\nimport (\n    \"fmt\"\n    \"net/http\"\n    \"io\"\n)\n\nfunc main() {\n    url := \"https://developer.vostokpribor.local/v1/sensors/optical/telemetry?device_id=PROD-1001-KZ\"\n    req, _ := http.NewRequest(\"GET\", url, nil)\n    req.Header.Set(\"Authorization\", \"Bearer vk_live_9a41c2e8f10b\")\n\n    client := &http.Client{}\n    resp, err := client.Do(req)\n    if err != nil { panic(err) }\n    defer resp.Body.Close()\n\n    body, _ := io.ReadAll(resp.Body)\n    fmt.Println(string(body))\n}', 1, '2026-09-27 15:51:21', '2026-09-27 15:51:21'),
(2, 'endpoint-geodetic', 'GET', '/v1/devices/geodetic/measurements', 'PROD-1002 Precision Geodetic Measurement Kit Vectors', 'Provides distance vectors, laser interferometer precision readings, and atmospheric refraction indices for geodetic surveying instrumentation deployed with CUS-1001 (Aral Geomatics) and CUS-1002 (BaltNord).', 'Internal • PROD-1002', '5k/min', 'PROD-1002', '[{\"name\":\"unit\",\"type\":\"string\",\"required\":true,\"description\":\"Hardware device identifier (e.g. PROD-1002)\"},{\"name\":\"include_raw\",\"type\":\"boolean\",\"required\":false,\"description\":\"Flag to append raw interferometer telemetry frames\"}]', 'curl -X GET \"https://developer.vostokpribor.local/v1/devices/geodetic/measurements?unit=PROD-1002\" \\\n  -H \"Authorization: Bearer vk_live_9a41c2e8f10b\"', 'import requests\n\nurl = \"https://developer.vostokpribor.local/v1/devices/geodetic/measurements\"\nheaders = {\"Authorization\": \"Bearer vk_live_9a41c2e8f10b\"}\nparams = {\"unit\": \"PROD-1002\"}\n\nresp = requests.get(url, headers=headers, params=params)\nprint(\"Calibration Status:\", resp.json()[\"calibration_valid\"])', 'const res = await fetch(\"https://developer.vostokpribor.local/v1/devices/geodetic/measurements?unit=PROD-1002\", {\n  headers: { \"Authorization\": \"Bearer vk_live_9a41c2e8f10b\" }\n});\nconsole.log(await res.json());', 'package main\n\nimport \"net/http\"\n\nfunc main() {\n  req, _ := http.NewRequest(\"GET\", \"https://developer.vostokpribor.local/v1/devices/geodetic/measurements?unit=PROD-1002\", nil)\n  req.Header.Set(\"Authorization\", \"Bearer vk_live_9a41c2e8f10b\")\n}', 1, '2026-09-27 15:51:21', '2026-09-27 15:51:21'),
(3, 'endpoint-scada', 'POST', '/v1/scada/ingest/frames', 'PROD-1004 SCADA High-Speed Ingestion Bridge', 'Ingests Modbus-TCP, OPC-UA, and telemetry frames directly into System 11 Ingestion Bridges with microsecond timestamp validation.', 'Confidential • PROD-1004', '50k/min', 'PROD-1004', '[{\"name\":\"facility_id\",\"type\":\"string\",\"required\":true,\"description\":\"Enclave node code (e.g. ALMATY-CENTRAL-01)\"},{\"name\":\"protocol\",\"type\":\"string\",\"required\":true,\"description\":\"MODBUS-TCP, OPC-UA, or PROFINET\"},{\"name\":\"payload_hex\",\"type\":\"hex-string\",\"required\":true,\"description\":\"Raw industrial telemetry frame\"}]', 'curl -X POST \"https://developer.vostokpribor.local/v1/scada/ingest/frames\" \\\n  -H \"Authorization: Bearer vk_live_9a41c2e8f10b\" \\\n  -H \"Content-Type: application/json\" \\\n  -d \'{\n    \"facility_id\": \"ALMATY-CENTRAL-01\",\n    \"protocol\": \"MODBUS-TCP\",\n    \"plc_register\": \"40001\",\n    \"payload_hex\": \"0A2B4C\"\n  }\'', 'import requests\n\npayload = {\n    \"facility_id\": \"ALMATY-CENTRAL-01\",\n    \"protocol\": \"MODBUS-TCP\",\n    \"plc_register\": \"40001\",\n    \"payload_hex\": \"0A2B4C\"\n}\nheaders = {\"Authorization\": \"Bearer vk_live_9a41c2e8f10b\"}\nresp = requests.post(\"https://developer.vostokpribor.local/v1/scada/ingest/frames\", json=payload, headers=headers)\nprint(\"Ingestion Receipt:\", resp.json()[\"frame_ack\"])', 'const payload = {\n  facility_id: \"ALMATY-CENTRAL-01\",\n  protocol: \"MODBUS-TCP\",\n  plc_register: \"40001\",\n  payload_hex: \"0A2B4C\"\n};\nconst res = await fetch(\"https://developer.vostokpribor.local/v1/scada/ingest/frames\", {\n  method: \"POST\",\n  headers: { \"Authorization\": \"Bearer vk_live_9a41c2e8f10b\", \"Content-Type\": \"application/json\" },\n  body: JSON.stringify(payload)\n});', 'package main\n\nimport \"net/http\"\n\nfunc main() {\n  // SCADA high-speed frame dispatch in Go\n  http.Post(\"https://developer.vostokpribor.local/v1/scada/ingest/frames\", \"application/json\", nil)\n}', 1, '2026-09-27 15:51:21', '2026-09-27 15:51:21');

-- --------------------------------------------------------

--
-- Table structure for table `developer_guides`
--

CREATE TABLE `developer_guides` (
  `id` int(11) NOT NULL,
  `guide_code` varchar(50) NOT NULL,
  `section_number` int(11) NOT NULL DEFAULT 1,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'Security',
  `classification` varchar(50) NOT NULL DEFAULT 'Confidential',
  `icon` varchar(50) NOT NULL DEFAULT 'lock',
  `summary` text NOT NULL,
  `code_snippet` text DEFAULT NULL,
  `footer_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `developer_guides`
--

INSERT INTO `developer_guides` (`id`, `guide_code`, `section_number`, `title`, `category`, `classification`, `icon`, `summary`, `code_snippet`, `footer_note`, `created_at`, `updated_at`) VALUES
(1, 'DOC-2026-010', 0, 'DOC-2026-010: API_Integration_Guide.pdf', 'Statutory Specification', 'Internal Use', 'description', 'Authoritative Baseline Specification • System 10 Reference Document.\nDefines cryptographic and telemetry contracts between client ERP systems (e.g. CUS-1002 BaltNord) and System 11 Admin & Governance pipelines. Mandates HMAC SHA-256 signatures on all webhooks.', 'SHA256: e8b94109ca82d90f23b7a1884c9820f121d5a7114b09e20a39c12b7a90f14d82\nSPEC_REVISION: 2026.4 // IEC 62443 L3 ATTESTED', 'Signatory Verification: Lead Developer Jonas Richter (EMP-1020) and Integration Engineer Dana Yermak (EMP-1017). Attested under ISO 27001 & ST RK IEC 62443.', '2026-09-27 15:51:21', '2026-09-27 15:51:21'),
(2, 'DOC-010-AUTH', 1, '1. Authentication & Token Scoping', 'Security', 'Confidential', 'lock', 'All API requests require an HTTP Authorization header containing a bearer token issued through the Partner Credentials Vault. Tokens must be signed with ed25519 or mTLS client certificates.', 'Authorization: Bearer vk_live_9a41c2e8f10b7a89d4e12c5', 'Production keys expire every 90 days. Systems automatically reject tokens lacking valid IP whitelisting configured in the Credentials Vault.', '2026-09-27 15:51:21', '2026-09-27 15:51:21'),
(3, 'DOC-010-ERP', 2, '2. ERP Integration Standard (SAP / 1C:Enterprise / Dynamics)', 'ERP Standard', 'Internal Standard', 'sync_alt', 'Architectural standard to synchronize purchase orders, equipment fulfillment stages, and billing events directly with corporate accounting software.\n1. Register Webhook Target: Provide your HTTPS endpoint in the Webhooks console with TLS 1.3 encryption.\n2. Order Matching: Align commercial opportunities from System 05 (CRM) with Order IDs in System 02 (E-Commerce).\n3. Invoice Reconciliation: Track payments against System 07 (Finance & Billing) reference numbers (e.g. INV-2026-002).', 'POST /v1/b2b/orders/create\n{\n  \"customer_id\": \"CUS-1002\",\n  \"invoice_ref\": \"INV-2026-002\",\n  \"erp_system\": \"SAP-S4HANA\"\n}', 'Mandates TLS 1.3 encryption and bidirectional payload validation against JSON schema DOC-2026-010.', '2026-09-27 15:51:21', '2026-09-27 15:51:21'),
(4, 'DOC-010-SCADA', 3, '3. SCADA Real-Time Telemetry Pipeline (Modbus & OPC-UA)', 'SCADA Telemetry', 'Public Spec', 'sensors', 'Industrial equipment deployed with customer facilities transmits operational frames at up to 64,800 events per second. Use the high-speed batch endpoint /v1/scada/ingest/frames or connect directly to the Almaty WebSocket stream.', 'wss://developer.vostokpribor.local/v1/stream/scada/feed?facility=ALMATY-01', 'Conforms to Republic Heavy Automation Standards ST RK IEC 62443-4-2.', '2026-09-27 15:51:21', '2026-09-27 15:51:21');

-- --------------------------------------------------------

--
-- Table structure for table `developer_partner_applications`
--

CREATE TABLE `developer_partner_applications` (
  `id` int(11) NOT NULL,
  `ticket_id` varchar(50) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `partner_id` varchar(50) DEFAULT NULL,
  `contact_name` varchar(100) NOT NULL,
  `contact_email` varchar(150) NOT NULL,
  `project_ref` varchar(255) DEFAULT NULL,
  `target_environment` enum('sandbox','staging','production') NOT NULL DEFAULT 'sandbox',
  `requested_scopes` text NOT NULL,
  `public_key` text DEFAULT NULL,
  `compliance_doc` tinyint(1) NOT NULL DEFAULT 1,
  `compliance_iec` tinyint(1) NOT NULL DEFAULT 1,
  `compliance_nda` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('In Review','Approved','Rejected') NOT NULL DEFAULT 'In Review',
  `assigned_engineer` varchar(100) NOT NULL DEFAULT 'Jonas Richter (EMP-1020)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `developer_partner_applications`
--

INSERT INTO `developer_partner_applications` (`id`, `ticket_id`, `company_name`, `partner_id`, `contact_name`, `contact_email`, `project_ref`, `target_environment`, `requested_scopes`, `public_key`, `compliance_doc`, `compliance_iec`, `compliance_nda`, `status`, `assigned_engineer`, `created_at`) VALUES
(1, 'ENCLAVE-REQ-981244', 'BaltNord Process Systems', 'CUS-1002', 'Kristaps Ozols', 'kristaps.ozols@baltnord.lv', 'PRJ-2026-002 (Refinery Flow Monitoring & SCADA Bridge)', 'sandbox', 'telemetry:read, orders:read_write', 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIBmQ8eBaltNordScadaGatewayKey2026', 1, 1, 1, 'In Review', 'Jonas Richter (EMP-1020)', '2026-09-11 06:12:00'),
(2, 'ENCLAVE-REQ-842109', 'Aral Geomatics Automation Labs', 'CUS-1001', 'Bauyrzhan Nurgaliyev', 'b.nurgaliyev@aral-geomatics.kz', 'PRJ-2026-001 (Geodetic Interferometer Grid Sync)', 'production', 'telemetry:read, actuator:write, audit:read', 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAICa5f82k09AralLabsKey2026', 1, 1, 1, 'Approved', 'Dana Yermak (EMP-1017)', '2026-09-08 11:30:00'),
(3, 'ENCLAVE-REQ-710293', 'Steppe Mining SCADA Engineering', 'CUS-1003', 'Aigul Sadykova', 'sadykova@steppemining.kz', 'PRJ-2026-003 (Autonomous Conveyor PLC Ingestion)', 'production', 'telemetry:read, orders:read_write', 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIK77d939SteppeKey2026', 1, 1, 1, 'Approved', 'Jonas Richter (EMP-1020)', '2026-09-05 08:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `developer_sandbox_logs`
--

CREATE TABLE `developer_sandbox_logs` (
  `id` int(11) NOT NULL,
  `method` varchar(10) NOT NULL,
  `url` varchar(500) NOT NULL,
  `request_body` longtext DEFAULT NULL,
  `status_code` int(11) NOT NULL DEFAULT 200,
  `response_time_ms` int(11) NOT NULL DEFAULT 25,
  `response_size` varchar(30) NOT NULL DEFAULT '512 B',
  `response_body` longtext DEFAULT NULL,
  `executed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `developer_sandbox_logs`
--

INSERT INTO `developer_sandbox_logs` (`id`, `method`, `url`, `request_body`, `status_code`, `response_time_ms`, `response_size`, `response_body`, `executed_at`) VALUES
(1, 'GET', '/v1/sensors/optical/telemetry?device_id=PROD-1001-KZ', NULL, 200, 24, '842 B', '{\n  \"device_id\": \"PROD-1001-KZ\",\n  \"sensor_series\": \"Industrial Optical Sensor Package\",\n  \"calibration_epoch\": 1789128000,\n  \"station\": \"ALMATY-CENTRAL\",\n  \"telemetry\": {\n    \"spectral_resolution_nm\": 0.04,\n    \"focal_plane_temp_c\": 18.2,\n    \"dispersion_coefficient\": 1.0024,\n    \"optical_throughput_percent\": 99.82,\n    \"snr_db\": 68.4\n  },\n  \"status\": \"NOMINAL_OPERATIONAL\",\n  \"jurisdiction_merkle_root\": \"0x4a8c911f...c892\"\n}', '2026-09-27 15:51:21'),
(2, 'GET', '/v1/devices/geodetic/measurements?unit=PROD-1002', NULL, 200, 31, '710 B', '{\n  \"unit_id\": \"PROD-1002-UST-04\",\n  \"apparatus\": \"Precision Geodetic Measurement Kit\",\n  \"laser_interferometer\": \"STABLE\",\n  \"azimuth_arcsec\": 142.8812,\n  \"zenith_angle_deg\": 44.1029,\n  \"distance_vector_meters\": 1840.4502,\n  \"refraction_index\": 1.000277,\n  \"calibration_valid\": true\n}', '2026-09-27 15:51:21'),
(3, 'POST', '/v1/scada/ingest/frames', '{\"facility_id\":\"ALMATY-CENTRAL-01\",\"protocol\":\"MODBUS-TCP\",\"plc_register\":\"40001\",\"payload_hex\":\"0A2B4C\"}', 201, 18, '412 B', '{\n  \"frame_ack\": \"ACK-SCADA-89102\",\n  \"facility_id\": \"ALMATY-CENTRAL-01\",\n  \"protocol\": \"MODBUS-TCP\",\n  \"buffered_lines\": 1,\n  \"ring_buffer_utilization\": \"14%\",\n  \"audit_escrow_timestamp\": 1789128842\n}', '2026-09-27 15:51:21');

-- --------------------------------------------------------

--
-- Table structure for table `developer_sandbox_presets`
--

CREATE TABLE `developer_sandbox_presets` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `method` varchar(10) NOT NULL DEFAULT 'GET',
  `url` varchar(255) NOT NULL,
  `sample_body` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `developer_sandbox_presets`
--

INSERT INTO `developer_sandbox_presets` (`id`, `title`, `method`, `url`, `sample_body`, `description`, `created_at`) VALUES
(1, 'PROD-1001 Optical Telemetry', 'GET', '/v1/sensors/optical/telemetry?device_id=PROD-1001-KZ', '', 'Fetch real-time spectral resolution, focal temp, and SNR ratio', '2026-09-27 15:51:21'),
(2, 'PROD-1002 Geodetic Vectors', 'GET', '/v1/devices/geodetic/measurements?unit=PROD-1002', '', 'Interferometer calibration vector and atmospheric refraction indices', '2026-09-27 15:51:21'),
(3, 'PROD-1004 SCADA Frame', 'POST', '/v1/scada/ingest/frames', '{\n  \"facility_id\": \"ALMATY-CENTRAL-01\",\n  \"protocol\": \"MODBUS-TCP\",\n  \"plc_register\": \"40001\",\n  \"payload_hex\": \"0A2B4C\"\n}', 'Direct Modbus/OPC-UA industrial telemetry frame dispatch', '2026-09-27 15:51:21'),
(4, 'B2B Order Create', 'POST', '/v1/b2b/orders/create', '{\n  \"customer_id\": \"CUS-1002\",\n  \"items\": [\n    {\"prod_id\": \"PROD-1001\", \"qty\": 4},\n    {\"prod_id\": \"PROD-1004\", \"qty\": 2}\n  ]\n}', 'B2B equipment procurement order generation with invoice ref', '2026-09-27 15:51:21');

-- --------------------------------------------------------

--
-- Table structure for table `developer_webhooks`
--

CREATE TABLE `developer_webhooks` (
  `id` int(11) NOT NULL,
  `delivery_id` varchar(50) NOT NULL,
  `event_type` varchar(100) NOT NULL,
  `target_endpoint` varchar(255) NOT NULL,
  `status_code` varchar(50) NOT NULL DEFAULT '200 OK',
  `latency_ms` int(11) NOT NULL DEFAULT 35,
  `status` enum('Delivered','Failed','Pending') NOT NULL DEFAULT 'Delivered',
  `classification` varchar(50) NOT NULL DEFAULT 'Internal',
  `payload` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `developer_webhooks`
--

INSERT INTO `developer_webhooks` (`id`, `delivery_id`, `event_type`, `target_endpoint`, `status_code`, `latency_ms`, `status`, `classification`, `payload`, `created_at`) VALUES
(1, 'WH-2026-9081', 'telemetry.vibration.alert', 'https://api.baltnord.lv/v1/vostok/events', '200 OK', 42, 'Delivered', 'Internal', '{\"event\": \"vibration_threshold_exceeded\", \"device\": \"PROD-1001-KZ\", \"amplitude_g\": 4.12}', '2026-09-11 13:42:10'),
(2, 'WH-2026-9080', 'order.status.dispatched', 'https://api.baltnord.lv/v1/vostok/orders', '200 OK', 38, 'Delivered', 'Internal', '{\"order_id\": \"ORD-2026-9904\", \"status\": \"DISPATCHED\", \"carrier\": \"Trans-Caspian Freight\"}', '2026-09-11 12:18:22'),
(3, 'WH-2026-9079', 'scada.emergency.trip', 'https://gateway.almaty-logistics.kz/wh', '200 OK', 18, 'Delivered', 'Confidential', '{\"facility\": \"ALMATY-CENTRAL-01\", \"circuit\": \"FEEDER-04\", \"trip_reason\": \"THERMAL_OVERLOAD\"}', '2026-09-11 11:05:01'),
(4, 'WH-2026-9078', 'telemetry.pressure.warning', 'https://api.baltnord.lv/v1/vostok/events', '504 TIMEOUT', 3002, 'Failed', 'Confidential', '{\"sensor\": \"BARO-991\", \"reading_kpa\": 1042.8}', '2026-09-11 09:30:15'),
(5, 'WH-2026-9077', 'catalog.price_index.updated', 'https://b2b.vostokpribor.local/sync', '200 OK', 24, 'Delivered', 'Internal', '{\"catalog_version\": \"2026.4\", \"items_updated\": 142}', '2026-09-11 07:15:44');

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `device_id` int(11) NOT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `hostname` varchar(150) DEFAULT NULL,
  `os_name` varchar(100) DEFAULT NULL,
  `department_code` varchar(4) DEFAULT NULL,
  `assigned_emp_id` varchar(10) DEFAULT NULL,
  `first_seen_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_seen_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`device_id`, `device_type`, `hostname`, `os_name`, `department_code`, `assigned_emp_id`, `first_seen_at`, `last_seen_at`, `status`) VALUES
(1, 'Industrial Bastion Server', 'bastion-01.almaty.vostok.kz', 'Ubuntu 24.04 LTS (Kernel Hardened)', 'ADM', 'EMP-1005', '2025-12-31 21:00:00', '2026-09-23 09:00:00', 'Active'),
(2, 'SCADA Optical Relay Node', 'scada-gw-03.karaganda.vostok.kz', 'VxWorks 7.2 RTOS', 'ENG', 'EMP-1002', '2026-01-10 05:30:00', '2026-09-23 09:15:00', 'Active'),
(3, 'HSM Cryptographic Enclave', 'hsm-fips-140-3.almaty.vostok.kz', 'Thales Luna PCIe Firmware v7.8', 'IT', 'EMP-1004', '2026-01-05 06:00:00', '2026-09-23 09:20:00', 'Active'),
(4, 'Field Engineering Laptop', 'ws-field-1018.almaty.vostok.kz', 'Windows 11 Enterprise (Secured Core)', 'IT', 'EMP-1018', '2026-02-15 07:00:00', '2026-09-23 08:45:00', 'Active'),
(5, 'Warehouse Dispatch Terminal', 'stacker-04.asrs.ust-kam.vostok.kz', 'Debian 12 Industrial Embedded', 'PRD', 'EMP-1007', '2026-03-01 04:00:00', '2026-09-23 09:10:00', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `doc_id` varchar(15) NOT NULL,
  `file_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `classification` enum('Public','Internal','Confidential','TopSecret') NOT NULL,
  `folder` varchar(50) NOT NULL DEFAULT 'projects',
  `department` varchar(10) NOT NULL DEFAULT 'ENG',
  `file_size` varchar(20) NOT NULL DEFAULT '2.0 MB',
  `file_hash` varchar(64) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Approved',
  `retention_period` varchar(20) NOT NULL DEFAULT '7y',
  `project_ref` varchar(150) DEFAULT NULL,
  `customer_ref` varchar(150) DEFAULT NULL,
  `is_legal_hold` tinyint(1) NOT NULL DEFAULT 0,
  `legal_hold_by_emp_id` varchar(10) DEFAULT NULL,
  `legal_hold_date` datetime DEFAULT NULL,
  `legal_hold_reason` text DEFAULT NULL,
  `owning_system` varchar(50) DEFAULT NULL,
  `owner_emp_id` varchar(10) DEFAULT NULL,
  `related_prj_id` varchar(15) DEFAULT NULL,
  `related_cus_id` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`doc_id`, `file_name`, `description`, `classification`, `folder`, `department`, `file_size`, `file_hash`, `status`, `retention_period`, `project_ref`, `customer_ref`, `is_legal_hold`, `legal_hold_by_emp_id`, `legal_hold_date`, `legal_hold_reason`, `owning_system`, `owner_emp_id`, `related_prj_id`, `related_cus_id`, `created_at`, `updated_at`) VALUES
('DOC-2026-001', 'Corporate_Information_Security_Policy.pdf', 'Master enterprise cybersecurity charter & access policy', 'TopSecret', 'governance', 'EXE', '3.8 MB', 'a89f30b9148d423985bf4f481c81c4e97a5b3992b1cf5600ea8b1990c681ea88', 'Approved', 'permanent', 'PRJ-GOV-2026', 'Internal Corporate', 0, NULL, NULL, NULL, 'Admin & Governance', 'EMP-1005', NULL, NULL, '2026-09-21 16:36:10', '2026-09-27 15:51:35'),
('DOC-2026-002', 'Customer_Onboarding_Standard.pdf', 'Commercial account vetting protocol & KYC', 'Confidential', 'contracts', 'SAL', '1.6 MB', '7b2a9e334f590bb821034f828a1c89283e7428fb17c1817e81037894a8217e92', 'Approved', '7y', 'COMM-STD-2026', 'Commercial Accounts', 0, NULL, NULL, NULL, 'CRM', 'EMP-1006', NULL, NULL, '2026-09-21 16:36:10', '2026-09-27 15:51:35'),
('DOC-2026-003', 'PRJ-2026-001_Statement_of_Work.pdf', 'Aral Geomatics Group • Optical Sensor Integration SOW', 'Confidential', 'projects', 'ENG', '2.1 MB', '4e1a8b928172c3d4e5f60718293a4b5c6d7e8f90123456789abcdef012345678', 'Approved', '7y', 'PRJ-2026-001 (Aral Geomatics)', 'CUS-1001 (Aral Geomatics Group)', 0, NULL, NULL, NULL, 'File Center', 'EMP-1019', 'PRJ-2026-001', 'CUS-1001', '2026-09-21 16:36:10', '2026-09-27 15:51:35'),
('DOC-2026-004', 'PRJ-2026-002_Integration_Specification.pdf', 'BaltNord SCADA Ingestion • Reviewed by Farida Iskakova', 'TopSecret', 'projects', 'ENG', '4.5 MB', '9f8e7d6c5b4a3928170192837465abcdeffedcba98765432101234567890fedc', 'In Review', '10y', 'PRJ-2026-002 (BaltNord Process Systems)', 'CUS-1002 (BaltNord Process Systems)', 1, 'EMP-0001', '2026-09-28 22:58:18', 'Preservation order: Litigation and statutory audit freeze', 'File Center', 'EMP-1019', 'PRJ-2026-002', 'CUS-1002', '2026-09-21 16:36:10', '2026-09-28 19:58:18'),
('DOC-2026-005', 'INV-2026-002_Billing_Record.pdf', 'BaltNord Milestone 1 billing attestation (€120,000)', 'Confidential', 'finance', 'FIN', '890 KB', '1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef', 'Approved', '7y', 'PRJ-2026-002', 'CUS-1002 (BaltNord Process Systems)', 0, NULL, NULL, NULL, 'Finance', 'EMP-1003', 'PRJ-2026-002', 'CUS-1002', '2026-09-21 16:36:10', '2026-09-27 15:51:35'),
('DOC-2026-006', 'Employee_Onboarding_Procedure.pdf', 'Standard Operating Procedure • SOP-05 HR Enrollment', 'Confidential', 'hr', 'HR', '1.2 MB', 'abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890', 'Approved', '5y', 'HR-SOP-2026', 'Internal HR', 0, NULL, NULL, NULL, 'HR', 'EMP-1013', NULL, NULL, '2026-09-21 16:36:10', '2026-09-27 15:51:35'),
('DOC-2026-007', 'Employee_Access_Matrix.xlsx', 'Complete RBAC and clearance register for 95 employees', 'TopSecret', 'governance', 'EXE', '1.9 MB', 'deadbeef1029384756abcdef0192837465bcaefd1234567890fedcba98765432', 'Approved', 'permanent', 'SEC-AUDIT-2026', 'Internal Security Audit', 1, 'EMP-1005', '2026-09-01 09:00:00', 'Statutory access permissions • Annual external audit', 'Admin & Governance', 'EMP-1005', NULL, NULL, '2026-09-21 16:36:10', '2026-09-27 15:51:35'),
('DOC-2026-008', 'Supplier_Evaluation_2026.pdf', 'Tier-1 industrial transducer vendor scorecard', 'Confidential', 'operations', 'OPS', '2.8 MB', '9876543210fedcba9876543210fedcba9876543210fedcba9876543210fedcba', 'Approved', '5y', 'OPS-SUP-2026', 'Supply Chain Vendors', 0, NULL, NULL, NULL, 'Operations', 'EMP-1011', NULL, NULL, '2026-09-21 16:36:10', '2026-09-27 15:51:35'),
('DOC-2026-009', 'Optical_Sensor_Product_Catalog.pdf', 'Standard B2B product specifications • Public distribution', 'Public', 'operations', 'SAL', '14.2 MB', '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef', 'Approved', '3y', 'PUB-CAT-2026', 'Public Industrial B2B', 0, NULL, NULL, NULL, 'E-Commerce', 'EMP-1006', NULL, NULL, '2026-09-21 16:36:10', '2026-09-27 15:51:35'),
('DOC-2026-010', 'API_Integration_Guide.pdf', 'REST & gRPC endpoints protocol for partner systems', 'Internal', 'projects', 'ENG', '3.1 MB', '554433221100aabbccddeeff99887766554433221100aabbccddeeff99887766', 'Approved', '3y', 'DEV-GATEWAY-v4', 'Developer Partners', 0, NULL, NULL, NULL, 'Developer Portal', 'EMP-1017', NULL, NULL, '2026-09-21 16:36:10', '2026-09-27 15:51:35'),
('DOC-2026-011', 'Disaster_Recovery_Plan.pdf', 'Cold site failover & Almaty datastore replication runbook', 'TopSecret', 'governance', 'EXE', '5.2 MB', 'feefeeddccbbaa99887766554433221100feefeeddccbbaa9988776655443322', 'Approved', 'permanent', 'BCP-DR-2026', 'IT Operations', 0, NULL, NULL, NULL, 'IT Helpdesk', 'EMP-1004', NULL, NULL, '2026-09-21 16:36:10', '2026-09-27 15:51:35'),
('DOC-2026-012', 'Annual_Corporate_Budget_2026.xlsx', 'Capital allocation • Executive board authorization only', 'TopSecret', 'finance', 'FIN', '4.1 MB', '99887766554433221100feefeeddccbbaa99887766554433221100feefeeddcc', 'Approved', '7y', 'CORP-FIN-2026', 'Corporate Board', 0, NULL, NULL, NULL, 'Finance', 'EMP-1003', NULL, NULL, '2026-09-21 16:36:10', '2026-09-27 15:51:35'),
('DOC-2026-013', 'Customer_Service_Handbook.pdf', 'Operational guidelines for regional account liaisons', 'Internal', 'hr', 'HR', '2.4 MB', '11223344556677889900aabbccddeeff11223344556677889900aabbccddeeff', 'Approved', '5y', 'INT-TRAIN-2026', 'Internal Support', 0, NULL, NULL, NULL, 'Intranet', 'EMP-1004', NULL, NULL, '2026-09-21 16:36:10', '2026-09-27 15:51:35'),
('DOC-2026-014', 'PRJ-2026-007_Test_Report.pdf', 'Seismic Vibration Array • Acceptance Testing Certificate', 'Confidential', 'projects', 'ENG', '3.6 MB', '3344556677889900aabbccddeeff11223344556677889900aabbccddeeff1122', 'Approved', '10y', 'PRJ-2026-007 (PetroKaz / Caspian Robotics)', 'CUS-1007 (Caspian Industrial Robotics)', 0, NULL, NULL, NULL, 'File Center', 'EMP-1019', 'PRJ-2026-007', 'CUS-1007', '2026-09-21 16:36:10', '2026-09-27 15:51:35'),
('DOC-2026-015', 'Board_Risk_Register_2026.xlsx', 'Statutory enterprise risk matrix • Board of Directors', 'TopSecret', 'governance', 'EXE', '2.7 MB', 'bbccddeeff00112233445566778899aabbccddeeff00112233445566778899aa', 'Approved', 'permanent', 'BOARD-RISK-2026', 'Board of Directors', 1, 'EMP-1001', '2026-09-05 11:30:00', 'Board of Directors quarterly risk disclosures', 'Admin & Governance', 'EMP-1005', NULL, NULL, '2026-09-21 16:36:10', '2026-09-27 15:51:35'),
('DOC-CAL-8812-99', 'Calibration-Cert-VP900.pdf', 'GOST & ISO 17025 Factory Calibration Certificate for VP-900', 'Internal', 'Certificates', 'QA', '1258291', NULL, 'Approved', '7y', 'PRJ-VP-7721', 'CUS-1001', 0, NULL, NULL, NULL, NULL, NULL, 'PRJ-VP-7721', 'CUS-1001', '2026-09-29 23:26:57', '2026-09-29 23:26:57'),
('DOC-FAT-7721-02', 'FAT-Acceptance-Protocol.pdf', 'Factory Acceptance Test Protocol signed by Severstal QA', 'Confidential', 'Protocols', 'QA', '2149580', NULL, 'Approved', '7y', 'PRJ-VP-7721', 'CUS-1001', 0, NULL, NULL, NULL, NULL, NULL, 'PRJ-VP-7721', 'CUS-1001', '2026-09-29 23:26:57', '2026-09-29 23:26:57'),
('DOC-SHP-1112', 'Purchase_Order_Severstal_2026.pdf', 'Signed Corporate PO Document (Order #1)', 'Confidential', 'Orders & Procurement', 'SAL', '42', '6906bc39c0b377ba9871eccce043510688663a6b146206ec705d4b3cfea88839', 'Approved', '5 Years', NULL, 'CUS-1001', 0, NULL, NULL, NULL, 'Online Shop B2B (SYS-02)', NULL, NULL, 'CUS-1001', '2026-09-30 00:03:37', '2026-09-30 00:03:37'),
('DOC-SHP-2000', 'Purchase_Order_Severstal_2026.pdf', 'Signed Corporate PO Document (Order #1)', 'Confidential', 'Orders & Procurement', 'SAL', '42', '6906bc39c0b377ba9871eccce043510688663a6b146206ec705d4b3cfea88839', 'Approved', '5 Years', NULL, 'CUS-1001', 0, NULL, NULL, NULL, 'Online Shop B2B (SYS-02)', NULL, NULL, 'CUS-1001', '2026-09-29 23:38:10', '2026-09-29 23:38:10'),
('DOC-SHP-7443', 'Purchase_Order_Severstal_2026.pdf', 'Signed Corporate PO Document (Order #1)', 'Confidential', 'Orders & Procurement', 'SAL', '42', '6906bc39c0b377ba9871eccce043510688663a6b146206ec705d4b3cfea88839', 'Approved', '5 Years', NULL, 'CUS-1001', 0, NULL, NULL, NULL, 'Online Shop B2B (SYS-02)', NULL, NULL, 'CUS-1001', '2026-09-29 23:49:41', '2026-09-29 23:49:41'),
('DOC-SHP-7875', 'Purchase_Order_Severstal_2026.pdf', 'Signed Corporate PO Document (Order #1)', 'Confidential', 'Orders & Procurement', 'SAL', '42', '6906bc39c0b377ba9871eccce043510688663a6b146206ec705d4b3cfea88839', 'Approved', '5 Years', NULL, 'CUS-1001', 0, NULL, NULL, NULL, 'Online Shop B2B (SYS-02)', NULL, NULL, 'CUS-1001', '2026-09-29 23:38:35', '2026-09-29 23:38:35'),
('DOC-SHP-9267', 'Purchase_Order_Severstal_2026.pdf', 'Signed Corporate PO Document (Order #1)', 'Confidential', 'Orders & Procurement', 'SAL', '42', '6906bc39c0b377ba9871eccce043510688663a6b146206ec705d4b3cfea88839', 'Approved', '5 Years', NULL, 'CUS-1001', 0, NULL, NULL, NULL, 'Online Shop B2B (SYS-02)', NULL, NULL, 'CUS-1001', '2026-09-29 23:58:51', '2026-09-29 23:58:51'),
('DOC-TEST-870', 'Test_Automated_Verification_Policy.pdf', 'Temporary test policy for automated method verification.', 'Confidential', 'policies', 'EXE', '1.2 MB', NULL, 'Approved', '7y', NULL, NULL, 0, NULL, NULL, NULL, 'SYS-11 GOV-CORE', 'EMP-0001', NULL, NULL, '2026-09-28 16:46:08', '2026-09-28 16:46:08'),
('DOC-WD-7721-04', 'BF5-Wiring-Diagram-RevC.pdf', 'Complete wiring schematic for Blast Furnace #5 telemetry racks', 'Confidential', 'Technical Schematics', 'Engineerin', '4404019', NULL, 'Approved', '7y', 'PRJ-VP-7721', 'CUS-1001', 0, NULL, NULL, NULL, NULL, NULL, 'PRJ-VP-7721', 'CUS-1001', '2026-09-29 23:26:57', '2026-09-29 23:26:57');

-- --------------------------------------------------------

--
-- Table structure for table `document_access_log`
--

CREATE TABLE `document_access_log` (
  `access_id` int(11) NOT NULL,
  `doc_id` varchar(15) NOT NULL,
  `accessed_by_emp_id` varchar(10) DEFAULT NULL,
  `accessed_by_cus_id` varchar(10) DEFAULT NULL,
  `system_id` varchar(4) DEFAULT NULL,
  `device_id` int(11) DEFAULT NULL,
  `source_ip` varchar(45) DEFAULT NULL,
  `access_type` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `success` tinyint(1) DEFAULT 1,
  `accessed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_access_log`
--

INSERT INTO `document_access_log` (`access_id`, `doc_id`, `accessed_by_emp_id`, `accessed_by_cus_id`, `system_id`, `device_id`, `source_ip`, `access_type`, `notes`, `success`, `accessed_at`) VALUES
(1, 'DOC-2026-001', 'EMP-1005', NULL, 'ADM', 1, '10.240.0.1', 'View', NULL, 1, '2026-09-23 06:15:00'),
(2, 'DOC-2026-001', NULL, 'CUS-1001', 'CUS', NULL, '195.189.12.44', 'Download', NULL, 1, '2026-09-23 07:20:00'),
(3, 'DOC-2026-005', 'EMP-1004', NULL, 'DEV', 3, '10.240.1.44', 'View', NULL, 1, '2026-09-23 08:05:00'),
(4, 'DOC-2026-002', 'EMP-1002', NULL, 'ENG', 2, '192.168.10.89', 'Edit', NULL, 1, '2026-09-22 12:30:00'),
(10, 'DOC-2026-004', 'EMP-1019', NULL, 'DOC', NULL, '10.240.0.12', 'SIGN_REVIEW', 'Opened redaction inspection for BaltNord PRJ-2026-002 specification', 1, '2026-09-11 14:15:22'),
(11, 'DOC-2026-010', 'EMP-1017', NULL, 'DEV', NULL, '10.240.1.44', 'View', 'Accessed API Integration Guide for developer gateway synchronization', 1, '2026-09-11 13:42:01'),
(12, 'DOC-2026-003', 'EMP-1010', NULL, 'SAL', NULL, '10.240.0.89', 'EXPORT', 'Exported customer copy of Aral Geomatics Statement of Work', 1, '2026-09-11 11:10:44'),
(13, 'DOC-2026-007', 'EMP-1005', NULL, 'ADM', NULL, '10.240.0.1', 'LEGAL_HOLD', 'Applied statutory audit preservation lock on Employee Access Matrix', 1, '2026-09-11 08:20:18'),
(14, 'DOC-2026-004', 'EMP-1017', NULL, 'DOC', NULL, '10.240.2.15', 'INGEST_DRAFT', 'Uploaded initial revision of PRJ-2026-002_Integration_Specification.pdf', 1, '2026-09-10 11:12:05'),
(15, 'DOC-2026-004', 'EMP-0001', NULL, 'DOC', NULL, '::1', 'LEGAL_HOLD', 'Released legal preservation hold by System Administrator (EMP-0001). Returned to statutory lifecycle.', 1, '2026-09-28 19:58:16'),
(16, 'DOC-2026-004', 'EMP-0001', NULL, 'DOC', NULL, '::1', 'LEGAL_HOLD', 'Enforced legal preservation hold by System Administrator (EMP-0001). Purge suspended.', 1, '2026-09-28 19:58:18');

-- --------------------------------------------------------

--
-- Table structure for table `document_approvals`
--

CREATE TABLE `document_approvals` (
  `approval_id` int(11) NOT NULL,
  `doc_id` varchar(15) NOT NULL,
  `reviewer_emp_id` varchar(10) DEFAULT NULL,
  `stage` int(11) NOT NULL DEFAULT 2,
  `stage_name` varchar(100) NOT NULL DEFAULT 'Project Manager Signoff',
  `token` varchar(100) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `decision` enum('Approved','Rejected','Pending') DEFAULT 'Pending',
  `decision_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_approvals`
--

INSERT INTO `document_approvals` (`approval_id`, `doc_id`, `reviewer_emp_id`, `stage`, `stage_name`, `token`, `comments`, `decision`, `decision_date`) VALUES
(1, 'DOC-2026-001', 'EMP-1001', 4, 'Governance Clearance', 'SIG-ED25519-VP-1001-2026-01-15', 'Executive board authorization completed.', 'Approved', '2026-01-15 10:00:00'),
(2, 'DOC-2026-002', 'EMP-1005', 4, 'Governance Clearance', 'SIG-ED25519-VP-1005-2026-02-02', 'Vetted and verified.', 'Approved', '2026-02-02 05:30:00'),
(3, 'DOC-2026-004', 'EMP-1019', 2, 'Project Manager Signoff', 'SIG-ED25519-VP-9021884-2026-09-11', 'Awaiting Senior PM review and digital signature.', 'Pending', '2026-09-20 03:00:00'),
(4, 'DOC-2026-005', 'EMP-1001', 4, 'Governance Clearance', 'SIG-ED25519-VP-1001-2026-01-02', 'Approved milestone billing.', 'Approved', '2026-01-02 04:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `document_retention_policies`
--

CREATE TABLE `document_retention_policies` (
  `policy_id` int(11) NOT NULL,
  `category` varchar(200) NOT NULL,
  `target_docs` varchar(200) DEFAULT NULL,
  `retention_scope` varchar(50) NOT NULL,
  `legal_anchor` varchar(200) NOT NULL,
  `disposition_action` text NOT NULL,
  `compliance_tier` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_retention_policies`
--

INSERT INTO `document_retention_policies` (`policy_id`, `category`, `target_docs`, `retention_scope`, `legal_anchor`, `disposition_action`, `compliance_tier`, `created_at`) VALUES
(1, 'Corporate Charter & Board Registers', 'DOC-2026-001, DOC-2026-015', 'Permanent', 'Kazakhstan Corporate Law §14', 'WORM Immutable Archive • Zero deletion permitted', 'Highly Conf.', '2026-09-27 15:51:35'),
(2, 'SCADA Engineering & Blueprints', 'DOC-2026-004, DOC-2026-014', '10 Years', 'IEC 62443-4-2 §7.3', 'Transition to Cold Archive after project closeout', 'Confidential', '2026-09-27 15:51:35'),
(3, 'Commercial Contracts & Billing Invoices', 'DOC-2026-003, DOC-2026-005, DOC-2026-012', '7 Years', 'Kazakhstan Tax Code §48', 'Archive to Nearline • Sealed against alteration', 'Confidential', '2026-09-27 15:51:35'),
(4, 'HR Onboarding & Personnel Records', 'DOC-2026-006, DOC-2026-013', '5 Years', 'Kazakhstan Labor Code §63', 'Automated purge after statutory expiration', 'Internal', '2026-09-27 15:51:35');

-- --------------------------------------------------------

--
-- Table structure for table `document_versions`
--

CREATE TABLE `document_versions` (
  `version_id` int(11) NOT NULL,
  `doc_id` varchar(15) NOT NULL,
  `version_number` int(11) NOT NULL,
  `uploaded_by_emp_id` varchar(10) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_versions`
--

INSERT INTO `document_versions` (`version_id`, `doc_id`, `version_number`, `uploaded_by_emp_id`, `uploaded_at`, `file_path`) VALUES
(1, 'DOC-2026-001', 1, 'EMP-1005', '2026-01-10 11:00:00', '/storage/docs/DOC-2026-001_v1.0.pdf'),
(2, 'DOC-2026-001', 2, 'EMP-1005', '2026-01-15 13:30:00', '/storage/docs/DOC-2026-001_v2.0_signed.pdf'),
(3, 'DOC-2026-002', 1, 'EMP-1002', '2026-02-01 07:00:00', '/storage/docs/DOC-2026-002_v1.0.pdf'),
(4, 'DOC-2026-005', 1, 'EMP-1004', '2026-01-01 05:00:00', '/storage/docs/DOC-2026-005_v1.0.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `emp_id` varchar(10) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `job_title` varchar(150) DEFAULT NULL,
  `department_code` varchar(4) NOT NULL,
  `clearance_level` enum('L1','L2','L3','L4') NOT NULL,
  `email` varchar(150) NOT NULL,
  `manager_emp_id` varchar(10) DEFAULT NULL,
  `employment_status` enum('Active','OnLeave','Suspended','Terminated') DEFAULT 'Active',
  `hire_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`emp_id`, `full_name`, `job_title`, `department_code`, `clearance_level`, `email`, `manager_emp_id`, `employment_status`, `hire_date`) VALUES
('EMP-0001', 'System Administrator', 'Executive SuperAdmin', 'EXE', 'L4', 'admin@gmail.com', NULL, 'Active', '2026-09-21'),
('EMP-1001', 'Viktor Sokolov', 'Chief Executive Officer (CEO)', 'EXE', 'L4', 'viktor.sokolov@vostokpribor.local', NULL, 'Active', NULL),
('EMP-1002', 'Amina Karimova', 'Chief Operating Officer (COO)', 'EXE', 'L4', 'amina.karimova@vostokpribor.local', 'EMP-1001', 'Active', NULL),
('EMP-1003', 'Daniel Weber', 'Chief Financial Officer (CFO)', 'EXE', 'L4', 'daniel.weber@vostokpribor.local', 'EMP-1001', 'Active', NULL),
('EMP-1004', 'Elena Morozova', 'Chief Technology Officer (CTO)', 'EXE', 'L4', 'elena.morozova@vostokpribor.local', 'EMP-1001', 'Active', NULL),
('EMP-1005', 'Timur Akhmetov', 'Chief Governance Officer', 'EXE', 'L4', 'timur.akhmetov@vostokpribor.local', 'EMP-1001', 'Active', NULL),
('EMP-1006', 'Pavel Orlov', 'Sales Director', 'SAL', 'L3', 'pavel.orlov@vostokpribor.local', 'EMP-1001', 'Active', NULL),
('EMP-1007', 'Sara Lindholm', 'Senior Account Manager', 'SAL', 'L3', 'sara.lindholm@vostokpribor.local', 'EMP-1006', 'Active', NULL),
('EMP-1008', 'Bekzod Rakhimov', 'Account Manager', 'SAL', 'L3', 'bekzod.rakhimov@vostokpribor.local', 'EMP-1006', 'Active', NULL),
('EMP-1009', 'Nadia Petrova', 'Strategic Sales Manager', 'SAL', 'L3', 'nadia.petrova@vostokpribor.local', 'EMP-1006', 'Active', NULL),
('EMP-1010', 'Markus Klein', 'Regional Sales Manager', 'SAL', 'L3', 'markus.klein@vostokpribor.local', 'EMP-1006', 'Active', NULL),
('EMP-1011', 'Arman Tulegenov', 'Operations Manager', 'OPS', 'L3', 'arman.tulegenov@vostokpribor.local', 'EMP-1002', 'Active', NULL),
('EMP-1012', 'Rustam Bekov', 'Logistics Manager', 'OPS', 'L3', 'rustam.bekov@vostokpribor.local', 'EMP-1011', 'Active', NULL),
('EMP-1013', 'Ilona Vetra', 'Procurement Manager', 'OPS', 'L3', 'ilona.vetra@vostokpribor.local', 'EMP-1011', 'Active', NULL),
('EMP-1014', 'Mikhail Antonov', 'Warehouse Supervisor', 'OPS', 'L2', 'mikhail.antonov@vostokpribor.local', 'EMP-1011', 'Active', NULL),
('EMP-1015', 'Kamila Nurzhan', 'Supply Chain Analyst', 'OPS', 'L2', 'kamila.nurzhan@vostokpribor.local', 'EMP-1011', 'Active', NULL),
('EMP-1016', 'Erik Hansen', 'Senior Automation Engineer', 'ENG', 'L3', 'erik.hansen@vostokpribor.local', 'EMP-1004', 'Active', NULL),
('EMP-1017', 'Dana Yermak', 'Software Integration Engineer', 'ENG', 'L3', 'dana.yermak@vostokpribor.local', 'EMP-1004', 'Active', NULL),
('EMP-1018', 'Leonid Volkov', 'Senior Systems Engineer', 'ENG', 'L3', 'leonid.volkov@vostokpribor.local', 'EMP-1004', 'Active', NULL),
('EMP-1019', 'Farida Iskakova', 'Project Manager', 'ENG', 'L3', 'farida.iskakova@vostokpribor.local', 'EMP-1004', 'Active', NULL),
('EMP-1020', 'Jonas Richter', 'Senior Developer', 'ENG', 'L3', 'jonas.richter@vostokpribor.local', 'EMP-1004', 'Active', NULL),
('EMP-1021', 'Ahmad Iyad Ahmad AbuNijim', 'Full Stack Developer', 'ITD', 'L4', 'nijim.ahmad077@gmail.com', NULL, 'Active', '2026-09-28');

-- --------------------------------------------------------

--
-- Table structure for table `employee_accounts`
--

CREATE TABLE `employee_accounts` (
  `account_id` int(11) NOT NULL,
  `emp_id` varchar(10) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `mfa_enabled` tinyint(1) DEFAULT 0,
  `status` varchar(20) DEFAULT 'Active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_accounts`
--

INSERT INTO `employee_accounts` (`account_id`, `emp_id`, `username`, `password_hash`, `mfa_enabled`, `status`, `last_login`, `created_at`) VALUES
(1, 'EMP-1001', 'viktor.sokolov', '$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC', 0, 'Active', '2026-09-29 18:28:28', '2026-09-21 16:39:04'),
(2, 'EMP-1001', 'ADM-VP-01', '$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(3, 'EMP-1001', 'EMP-1001', '$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(4, 'EMP-1002', 'amina.karimova', '$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(5, 'EMP-1002', 'HR-VP-201', '$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(6, 'EMP-1002', 'HR-VP-104', '$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(7, 'EMP-1002', 'EMP-1002', '$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(8, 'EMP-1003', 'daniel.weber', '$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(9, 'EMP-1003', 'FIN-VP-102', '$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(10, 'EMP-1003', 'FIN-VP-502', '$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(11, 'EMP-1003', 'EMP-1003', '$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(12, 'EMP-1004', 'elena.morozova', '$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(13, 'EMP-1004', 'EMP-1004', '$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(14, 'EMP-1005', 'timur.akhmetov', '$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(15, 'EMP-1005', 'EMP-1005', '$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(16, 'EMP-1006', 'pavel.orlov', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(17, 'EMP-1006', 'CRM-VP-842', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', '2026-09-29 17:49:58', '2026-09-21 16:39:04'),
(18, 'EMP-1006', 'EMP-842', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(19, 'EMP-1006', 'EMP-1006', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(20, 'EMP-1007', 'sara.lindholm', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(21, 'EMP-1007', 'EMP-1007', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(22, 'EMP-1008', 'bekzod.rakhimov', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(23, 'EMP-1008', 'EMP-1008', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(24, 'EMP-1009', 'nadia.petrova', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(25, 'EMP-1009', 'EMP-1009', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(26, 'EMP-1010', 'markus.klein', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(27, 'EMP-1010', 'EMP-1010', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(28, 'EMP-1011', 'arman.tulegenov', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(29, 'EMP-1011', 'EMP-1011', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(30, 'EMP-1012', 'rustam.bekov', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(31, 'EMP-1012', 'EMP-1012', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(32, 'EMP-1013', 'ilona.vetra', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(33, 'EMP-1013', 'EMP-1013', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(34, 'EMP-1014', 'mikhail.antonov', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(35, 'EMP-1014', 'EMP-1014', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(36, 'EMP-1015', 'kamila.nurzhan', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(37, 'EMP-1015', 'EMP-1015', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(38, 'EMP-1016', 'erik.hansen', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(39, 'EMP-1016', 'EMP-1016', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(40, 'EMP-1017', 'dana.yermak', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(41, 'EMP-1017', 'EMP-1017', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(42, 'EMP-1018', 'leonid.volkov', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(43, 'EMP-1018', 'IT-VP-304', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(44, 'EMP-1018', 'EMP-1018', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(45, 'EMP-1019', 'farida.iskakova', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(46, 'EMP-1019', 'DOC-VP-501', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(47, 'EMP-1019', 'CST-VP-09', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(48, 'EMP-1019', 'EMP-VP-1019', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(49, 'EMP-1019', 'EMP-1019', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(50, 'EMP-1020', 'jonas.richter', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(51, 'EMP-1020', 'DEV-VP-994', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(52, 'EMP-1020', 'EMP-1020', '$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW', 0, 'Active', NULL, '2026-09-21 16:39:04'),
(53, 'EMP-0001', 'admin@gmail.com', '$2y$12$4ATG0UAGywcH52t34qqEY.sQAxwyzlVVgOh4vFUQtLPWIGkz0HPxS', 0, 'Active', '2026-09-29 23:51:34', '2026-09-21 17:20:16'),
(54, 'EMP-0001', 'admin', '$2y$12$4ATG0UAGywcH52t34qqEY.sQAxwyzlVVgOh4vFUQtLPWIGkz0HPxS', 0, 'Active', NULL, '2026-09-21 17:20:16'),
(55, 'EMP-0001', 'EMP-0001', '$2y$12$4ATG0UAGywcH52t34qqEY.sQAxwyzlVVgOh4vFUQtLPWIGkz0HPxS', 0, 'Active', NULL, '2026-09-21 17:20:16'),
(68, 'EMP-1021', 'ahmad.iyad.ahmad.abunijim', '$2y$10$Mai8ZmEnlNE0ViwXAek6luBrNXhzykQ5Ovoj3cImmutpRprdQ/xva', 0, 'Active', '2026-09-28 15:38:49', '2026-09-28 15:38:04'),
(69, 'EMP-1021', 'EMP-1021', '$2y$10$Mai8ZmEnlNE0ViwXAek6luBrNXhzykQ5Ovoj3cImmutpRprdQ/xva', 0, 'Active', NULL, '2026-09-28 15:38:04');

-- --------------------------------------------------------

--
-- Table structure for table `employee_offboarding`
--

CREATE TABLE `employee_offboarding` (
  `offboarding_id` int(11) NOT NULL,
  `emp_id` varchar(10) NOT NULL,
  `step` enum('HRInitiated','StatusChanged','ITNotified','AccessRevoked','IntranetRevoked','FileCenterReviewed','CRMRevoked','HelpdeskClosed','GovernanceVerified','AuditLogged') NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_offboarding`
--

INSERT INTO `employee_offboarding` (`offboarding_id`, `emp_id`, `step`, `status`, `completed_at`) VALUES
(1, 'EMP-1014', 'HRInitiated', 'Completed', '2026-09-21 17:21:39'),
(2, 'EMP-1014', 'StatusChanged', 'Completed', '2026-09-21 17:21:39'),
(3, 'EMP-1014', 'ITNotified', 'In Progress', '2026-09-21 17:21:39'),
(4, 'EMP-1014', 'AccessRevoked', 'Pending', '2026-09-21 17:21:39'),
(5, 'EMP-1014', 'IntranetRevoked', 'Pending', '2026-09-21 17:21:39'),
(6, 'EMP-1014', 'FileCenterReviewed', 'Pending', '2026-09-21 17:21:39'),
(7, 'EMP-1014', 'CRMRevoked', 'Pending', '2026-09-21 17:21:39'),
(8, 'EMP-1014', 'HelpdeskClosed', 'Pending', '2026-09-21 17:21:39'),
(9, 'EMP-1014', 'GovernanceVerified', 'Pending', '2026-09-21 17:21:39'),
(10, 'EMP-1014', 'AuditLogged', 'Pending', '2026-09-21 17:21:39');

-- --------------------------------------------------------

--
-- Table structure for table `employee_onboarding`
--

CREATE TABLE `employee_onboarding` (
  `onboarding_id` int(11) NOT NULL,
  `emp_id` varchar(10) NOT NULL,
  `step` enum('RecordCreated','AccessRequested','IntranetGranted','SystemAccessGranted','DocumentsFiled','GovernanceReviewed') NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_onboarding`
--

INSERT INTO `employee_onboarding` (`onboarding_id`, `emp_id`, `step`, `status`, `completed_at`) VALUES
(1, 'EMP-1020', 'RecordCreated', 'Completed', '2026-09-21 17:21:39'),
(2, 'EMP-1020', 'AccessRequested', 'Completed', '2026-09-21 17:21:39'),
(3, 'EMP-1020', 'IntranetGranted', 'Completed', '2026-09-21 17:21:39'),
(4, 'EMP-1020', 'SystemAccessGranted', 'Completed', '2026-09-21 17:36:55'),
(5, 'EMP-1020', 'DocumentsFiled', 'Pending', '2026-09-21 17:21:39'),
(6, 'EMP-1020', 'GovernanceReviewed', 'Pending', '2026-09-21 17:21:39'),
(7, 'EMP-1019', 'RecordCreated', 'Completed', '2026-09-21 17:21:39'),
(8, 'EMP-1019', 'AccessRequested', 'Completed', '2026-09-21 17:21:39'),
(9, 'EMP-1019', 'IntranetGranted', 'Completed', '2026-09-21 17:21:39'),
(10, 'EMP-1019', 'SystemAccessGranted', 'Completed', '2026-09-21 17:21:39'),
(11, 'EMP-1019', 'DocumentsFiled', 'Completed', '2026-09-21 17:21:39'),
(12, 'EMP-1019', 'GovernanceReviewed', 'In Progress', '2026-09-21 17:21:39'),
(13, 'EMP-1015', 'RecordCreated', 'Completed', '2026-09-21 17:21:39'),
(14, 'EMP-1015', 'AccessRequested', 'In Progress', '2026-09-21 17:21:39'),
(15, 'EMP-1015', 'IntranetGranted', 'Pending', '2026-09-21 17:21:39'),
(16, 'EMP-1015', 'SystemAccessGranted', 'Pending', '2026-09-21 17:21:39'),
(17, 'EMP-1015', 'DocumentsFiled', 'Pending', '2026-09-21 17:21:39'),
(18, 'EMP-1015', 'GovernanceReviewed', 'Pending', '2026-09-21 17:21:39'),
(37, 'EMP-1021', 'RecordCreated', 'Completed', '2026-09-28 15:38:04'),
(38, 'EMP-1021', 'AccessRequested', 'In Progress', '2026-09-28 15:38:04'),
(39, 'EMP-1021', 'IntranetGranted', 'Pending', '2026-09-28 15:38:04'),
(40, 'EMP-1021', 'SystemAccessGranted', 'Pending', '2026-09-28 15:38:04'),
(41, 'EMP-1021', 'DocumentsFiled', 'Pending', '2026-09-28 15:38:04'),
(42, 'EMP-1021', 'GovernanceReviewed', 'Pending', '2026-09-28 15:38:04');

-- --------------------------------------------------------

--
-- Table structure for table `employee_roles`
--

CREATE TABLE `employee_roles` (
  `emp_id` varchar(10) NOT NULL,
  `role_id` int(11) NOT NULL,
  `granted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `granted_by_emp_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_roles`
--

INSERT INTO `employee_roles` (`emp_id`, `role_id`, `granted_at`, `granted_by_emp_id`) VALUES
('EMP-0001', 1, '2026-09-21 17:20:16', 'EMP-0001'),
('EMP-1001', 1, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1002', 7, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1003', 6, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1004', 4, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1005', 2, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1006', 3, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1007', 3, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1008', 4, '2026-09-30 00:34:14', NULL),
('EMP-1009', 3, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1010', 3, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1011', 8, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1012', 8, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1013', 8, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1014', 8, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1015', 8, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1016', 4, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1017', 4, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1018', 5, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1019', 4, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1020', 4, '2026-09-21 16:36:11', 'EMP-1001'),
('EMP-1021', 5, '2026-09-28 15:38:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `financial_reports`
--

CREATE TABLE `financial_reports` (
  `report_id` int(11) NOT NULL,
  `report_code` varchar(50) NOT NULL,
  `report_type` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `fiscal_period` varchar(50) NOT NULL,
  `summary_metrics` text DEFAULT NULL,
  `net_income` decimal(14,2) DEFAULT NULL,
  `ebitda_margin` decimal(5,2) DEFAULT NULL,
  `total_assets` decimal(14,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Audited & Signed',
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `signed_by` varchar(100) DEFAULT 'Mikhail Sorokin (CFC)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `financial_reports`
--

INSERT INTO `financial_reports` (`report_id`, `report_code`, `report_type`, `title`, `fiscal_period`, `summary_metrics`, `net_income`, `ebitda_margin`, `total_assets`, `status`, `generated_at`, `signed_by`) VALUES
(1, 'REP-PL-Q3-2026', 'Statement of Operations', 'Income Statement (P&L) · Q3 2026', 'Q3 2026', 'Net Operating Income: €4,850,200 (EBITDA margin 26.0%). Gross revenues on heavy instrumentation telemetry tracking +14.2% YoY.', 4850200.00, 26.00, 48200000.00, 'Audited & Signed', '2026-09-24 15:10:47', 'Mikhail Sorokin (Chief Financial Controller)'),
(2, 'REP-BS-Q3-2026', 'Balance Sheet', 'Quarterly Statement of Financial Position', 'Q3 2026', 'Total Consolidated Assets: €48.2M · Current Cash & Receivables: €14.6M. Capital reserves verified compliant with IFRS 9.', 3920000.00, 24.50, 48200000.00, 'Audited & Signed', '2026-09-24 15:10:47', 'Elena V. Orlova (Audit Committee Chair)'),
(3, 'REP-TAX-2026', 'Statutory Audit', 'RAS & Tax Compliance Dossier', 'FY 2026', 'Federal Tax Service (FNS) & VAT Settlement verification complete. Tax clearance certificate active through Q4 2026.', 1250000.00, 22.00, 48200000.00, 'Statutory Approved', '2026-09-24 15:10:47', 'State Metrology & Tax Board');

-- --------------------------------------------------------

--
-- Table structure for table `integration_logs`
--

CREATE TABLE `integration_logs` (
  `log_id` bigint(20) NOT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `endpoint` varchar(200) DEFAULT NULL,
  `request_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `response_status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `integration_logs`
--

INSERT INTO `integration_logs` (`log_id`, `partner_id`, `endpoint`, `request_time`, `response_status`) VALUES
(1, 1, 'https://api.vostokpribor.kz/telemetry/v1', '2026-09-23 07:00:00', 200),
(2, 2, 'https://api.vostokpribor.kz/crm/v1/sync', '2026-09-23 07:30:00', 200),
(3, 3, 'https://api.vostokpribor.kz/b2b/orders/v1', '2026-09-23 08:00:00', 200);

-- --------------------------------------------------------

--
-- Table structure for table `integration_requirements`
--

CREATE TABLE `integration_requirements` (
  `req_id` int(11) NOT NULL,
  `cus_id` varchar(10) DEFAULT NULL,
  `prj_id` varchar(15) DEFAULT NULL,
  `reviewed_by_emp_id` varchar(10) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(30) DEFAULT 'UnderReview'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `integration_requirements`
--

INSERT INTO `integration_requirements` (`req_id`, `cus_id`, `prj_id`, `reviewed_by_emp_id`, `description`, `status`) VALUES
(1, 'CUS-1001', 'PRJ-2026-001', 'EMP-1002', 'Bidirectional Modbus TCP to REST JSON Gateway with AES-256 GCM hardware encryption.', 'Approved'),
(2, 'CUS-1002', 'PRJ-2026-002', 'EMP-1004', 'Continuous real-time telemetry streaming to Samruk Energy central monitoring station.', 'InDevelopment'),
(3, 'CUS-1005', 'PRJ-2026-005', 'EMP-1018', 'Automated replenishment dispatch orders triggered when pressure threshold crosses safety margin.', 'UnderReview');

-- --------------------------------------------------------

--
-- Table structure for table `internal_policies`
--

CREATE TABLE `internal_policies` (
  `policy_id` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `doc_id` varchar(15) DEFAULT NULL,
  `effective_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `internal_policies`
--

INSERT INTO `internal_policies` (`policy_id`, `title`, `doc_id`, `effective_date`) VALUES
(1, 'Enterprise Clean Desk & Credential Protection Standard', 'DOC-2026-001', '2026-01-01'),
(2, 'High-Precision Measurement Instrument Calibration Standard', 'DOC-2026-005', '2026-03-15'),
(3, 'Employee Travel & Overseas Per Diem Regulations', 'DOC-2026-006', '2026-02-01'),
(4, 'Emergency Facility Evacuation & Industrial Safety Protocol', 'DOC-2026-012', '2025-11-01'),
(5, 'Intellectual Property & Industrial Patent Filing Standard', 'DOC-2026-013', '2026-04-10');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `inv_id` varchar(15) NOT NULL,
  `cus_id` varchar(10) NOT NULL,
  `prj_id` varchar(15) DEFAULT NULL,
  `total_value` decimal(14,2) NOT NULL,
  `currency` char(3) DEFAULT 'EUR',
  `payment_status` enum('Paid','Pending','Overdue') NOT NULL,
  `issued_at` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `payment_terms` varchar(50) DEFAULT 'Net-30',
  `notes` text DEFAULT NULL,
  `paid_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`inv_id`, `cus_id`, `prj_id`, `total_value`, `currency`, `payment_status`, `issued_at`, `due_date`, `payment_terms`, `notes`, `paid_at`) VALUES
('INV-2024-3802', 'CUS-1001', 'PRJ-VP-6840', 84000.00, 'USD', 'Paid', '2024-06-01', '2024-07-01', 'Net 30', 'Shop #2 Engineering Blueprint and Sensor Rigging', '2024-06-25'),
('INV-2024-4411', 'CUS-1001', 'PRJ-VP-7721', 256800.00, 'USD', 'Paid', '2024-07-15', '2024-08-15', 'Net 30', 'Initial Hardware Dispatch & Factory Acceptance Test', '2024-08-10'),
('INV-2024-5890', 'CUS-1001', 'PRJ-VP-7721', 128400.00, 'USD', 'Overdue', '2024-10-01', '2024-10-31', 'Net 30', 'Milestone 2 Delivery - Skid Transducer Assemblies', NULL),
('INV-2026-001', 'CUS-1001', 'PRJ-2026-001', 46250.00, 'EUR', 'Paid', NULL, NULL, 'Net-30', NULL, NULL),
('INV-2026-002', 'CUS-1002', 'PRJ-2026-002', 80000.00, 'EUR', 'Pending', NULL, NULL, 'Net-30', NULL, NULL),
('INV-2026-003', 'CUS-1003', 'PRJ-2026-003', 137500.00, 'EUR', 'Paid', NULL, NULL, 'Net-30', NULL, NULL),
('INV-2026-004', 'CUS-1004', 'PRJ-2026-004', 55000.00, 'EUR', 'Pending', NULL, NULL, 'Net-30', NULL, NULL),
('INV-2026-005', 'CUS-1005', 'PRJ-2026-005', 42000.00, 'EUR', 'Paid', NULL, NULL, 'Net-30', NULL, NULL),
('INV-2026-006', 'CUS-1006', 'PRJ-2026-006', 32000.00, 'EUR', 'Pending', NULL, NULL, 'Net-30', NULL, NULL),
('INV-2026-007', 'CUS-1007', 'PRJ-2026-007', 105000.00, 'EUR', 'Paid', NULL, NULL, 'Net-30', NULL, NULL),
('INV-2026-008', 'CUS-1008', 'PRJ-2026-008', 68333.00, 'EUR', 'Pending', NULL, NULL, 'Net-30', NULL, NULL),
('INV-2026-009', 'CUS-1009', 'PRJ-2026-012', 47333.00, 'EUR', 'Paid', NULL, NULL, 'Net-30', NULL, NULL),
('INV-2026-010', 'CUS-1010', 'PRJ-2026-010', 91667.00, 'EUR', 'Pending', NULL, NULL, 'Net-30', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `item_id` int(11) NOT NULL,
  `inv_id` varchar(15) NOT NULL,
  `description` varchar(255) NOT NULL,
  `part_number` varchar(100) DEFAULT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(14,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ip_addresses`
--

CREATE TABLE `ip_addresses` (
  `ip_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `device_id` int(11) DEFAULT NULL,
  `is_internal` tinyint(1) DEFAULT 1,
  `geo_country` varchar(100) DEFAULT NULL,
  `first_seen_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_seen_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ip_addresses`
--

INSERT INTO `ip_addresses` (`ip_id`, `ip_address`, `device_id`, `is_internal`, `geo_country`, `first_seen_at`, `last_seen_at`) VALUES
(1, '10.240.0.1', 1, 1, 'Kazakhstan', '2025-12-31 21:00:00', '2026-09-23 09:00:00'),
(2, '10.240.1.44', 3, 1, 'Kazakhstan', '2026-01-05 06:00:00', '2026-09-23 09:20:00'),
(3, '10.240.2.18', 4, 1, 'Kazakhstan', '2026-02-15 07:00:00', '2026-09-23 08:45:00'),
(4, '192.168.10.89', 2, 1, 'Kazakhstan', '2026-01-10 05:30:00', '2026-09-23 09:15:00'),
(5, '10.240.5.12', 5, 1, 'Kazakhstan', '2026-03-01 04:00:00', '2026-09-23 09:10:00');

-- --------------------------------------------------------

--
-- Table structure for table `it_assets`
--

CREATE TABLE `it_assets` (
  `asset_id` int(11) NOT NULL,
  `asset_tag` varchar(50) DEFAULT NULL,
  `emp_id` varchar(10) DEFAULT NULL,
  `department_code` varchar(4) DEFAULT NULL,
  `system_id` varchar(4) DEFAULT NULL,
  `asset_type` varchar(100) DEFAULT NULL,
  `device_model` varchar(150) DEFAULT NULL,
  `hostname` varchar(150) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `mac_address` varchar(50) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `operating_system` varchar(100) DEFAULT NULL,
  `os_version` varchar(50) DEFAULT NULL,
  `firmware_version` varchar(50) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `assigned_date` date DEFAULT NULL,
  `criticality` varchar(20) DEFAULT NULL,
  `environment` varchar(20) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Active',
  `last_seen_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `health_status` varchar(50) NOT NULL DEFAULT 'Nominal',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `it_assets`
--

INSERT INTO `it_assets` (`asset_id`, `asset_tag`, `emp_id`, `department_code`, `system_id`, `asset_type`, `device_model`, `hostname`, `ip_address`, `mac_address`, `location`, `operating_system`, `os_version`, `firmware_version`, `serial_number`, `assigned_date`, `criticality`, `environment`, `status`, `last_seen_at`, `health_status`, `notes`) VALUES
(1, 'VP-SRV-GOV-01', 'EMP-1005', 'ADM', 'ADM', 'Server', 'HPE ProLiant DL380 Gen10', 'gov-core-node-01.vostok.local', '10.240.0.1', '00:1E:67:D2:AA:01', 'Central Datacenter Bay 1', 'RHEL 9.3', '9.3', 'iLO 5 v2.60', 'SRV-VP-2026-001', '2026-01-01', 'Critical', 'Production', 'Active', '2026-09-27 17:43:34', 'Nominal', 'Core governance compliance and enterprise directory node.'),
(2, 'VP-HSM-SEC-01', 'EMP-1004', 'IT', 'DEV', 'Hardware Security Module', 'Thales Luna PCIe HSM 700', 'hsm-cluster-alpha.vostok.local', '10.240.1.44', '00:0F:B5:3C:99:44', 'Secure Key Vault Bay A', 'Thales LunaOS', '7.8.2', 'v7.8.2-FIPS', 'HSM-FIPS-0992', '2026-01-05', 'Critical', 'Production', 'Active', '2026-09-27 17:43:34', 'Nominal', 'FIPS 140-2 Level 3 Hardware Security Module cluster.'),
(3, 'VP-GW-SCA-01', 'EMP-1002', 'ENG', 'SHP', 'Industrial SCADA Gateway', 'Siemens Simatic IPC227E', 'scada-plc-gw-01.vostok.local', '192.168.10.89', '00:1B:1B:4F:10:89', 'Furnace SCADA Control Bay', 'VxWorks RTOS', '7.2', 'v2.4.1-RTOS', 'SCADA-VP-003', '2026-01-10', 'High', 'Production', 'Active', '2026-09-27 17:43:34', 'Nominal', 'Industrial SCADA edge gateway for blast furnace telemetry.'),
(4, 'VP-WS-OPS-18', 'EMP-1018', 'IT', 'IT', 'Engineering Workstation', 'Dell Precision 5820 Tower', 'ws-sec-ops-1018.vostok.local', '10.240.2.18', 'D4:5D:64:8E:22:18', 'IT Security Operations Bay', 'Windows 11 Pro', '23H2', 'BIOS 2.14.0', 'WS-VP-2026-018', '2026-02-15', 'Medium', 'Production', 'Active', '2026-09-27 17:43:34', 'Nominal', 'Lead security engineering workstation.'),
(5, 'VP-GW-LIP-01', NULL, NULL, NULL, 'Modbus TCP/IP to RS-485 Gateway', 'Moxa MGate MB3170', NULL, '10.240.48.10', '00:90:E8:4A:2B:10', 'Lipetsk Furnace #1 Bay', 'Embedded RTOS', NULL, 'v4.2.1-sec', 'SN-2AE07103', '2026-09-27', 'Critical', NULL, 'Active', '2026-09-27 17:43:34', 'Online (Active)', 'Monitors high-temp pyrometer array for furnace #1.'),
(6, 'VP-BIO-FAB-01', NULL, NULL, NULL, 'Cleanroom RFID/Biometric Airlock', 'Suprema BioEntry W2', NULL, '10.240.90.01', '00:17:7D:6F:12:01', 'Nanofabrication Bay A', 'Linux 3.18', NULL, 'v2.17.4', 'SN-62D2A5A6', '2026-09-27', 'Critical', NULL, 'Active', '2026-09-27 17:43:34', 'Online (Active)', 'Cleanroom airlock biometric ingress scanner.'),
(7, 'VP-SRV-FAT-01', NULL, NULL, NULL, 'Industrial Rugged Xeon Server', 'Advantech MIC-7700', NULL, '10.240.12.80', '00:0B:AB:44:9D:E1', 'FAT Acceptance Bay #1', 'Ubuntu 22.04 RT', NULL, 'Ubuntu 22.04 RT', 'SN-804A4627', '2026-09-27', 'High', NULL, 'Active', '2026-09-27 17:43:34', 'Online (Active)', 'Real-time laser triangulation coordinate calculation server.'),
(8, 'VP-CAL-OPTI-01', NULL, NULL, NULL, 'Optical Spectrum Analyzer Rig', 'Yokogawa AQ6370D', NULL, '10.240.12.92', '00:80:A1:99:C7:01', 'Optical Standards Metrology Bay', 'Windows Embedded Standard', NULL, 'v1.44-cal', 'SN-900BCE36', '2026-09-27', 'Medium', NULL, 'Active', '2026-09-27 17:43:34', 'Calibrated (Nominal)', 'National standard trace optical reference spectrometer.'),
(9, 'VP-GW-LIP-02', NULL, NULL, NULL, 'Modbus TCP/IP to RS-485 Gateway', 'Moxa MGate MB3170', NULL, '10.240.48.11', '00:90:E8:4A:2B:11', 'Lipetsk Furnace #2 Bay', NULL, NULL, 'v4.2.2-sec', NULL, NULL, NULL, NULL, 'Active', '2026-09-27 17:43:34', 'Online (Active)', 'Monitors high-temp pyrometer array for furnace #2.'),
(10, 'VP-BIO-FAB-02', NULL, NULL, NULL, 'Cleanroom RFID/Biometric Airlock', 'Suprema BioEntry W2', NULL, '10.240.90.02', '00:17:7D:6F:12:02', 'Nanofabrication Bay B', NULL, NULL, 'v2.18.0', NULL, NULL, NULL, NULL, 'Active', '2026-09-27 17:43:34', 'Interlock Fault', 'Controls ISO Class 4 cleanroom personnel airlock gating.'),
(11, 'VP-SRV-FAT-02', NULL, NULL, NULL, 'Industrial Rugged Xeon Server', 'Advantech MIC-7700', NULL, '10.240.12.81', '00:0B:AB:44:9D:E2', 'FAT Acceptance Bay #2', NULL, NULL, 'Ubuntu 22.04 RT', NULL, NULL, NULL, NULL, 'Active', '2026-09-27 17:43:34', 'Online (Active)', 'Factory Acceptance Testing rig telemetry aggregation.'),
(12, 'VP-CAL-OPTI-02', NULL, NULL, NULL, 'Optical Pyrometer Standard', 'Mikron M390 Ultra-Precision', NULL, '10.240.33.15', '00:80:A1:99:C7:02', 'Almaty Sensor Lab A', NULL, NULL, 'v3.02', NULL, NULL, NULL, NULL, 'Active', '2026-09-27 17:43:34', 'Calibrated (Nominal)', 'Blackbody calibration source for pyrometric sensors.'),
(13, 'VP-GW-LIP-03', NULL, NULL, NULL, 'Modbus TCP/IP to RS-485 Gateway', 'Moxa MGate MB3170', NULL, '10.240.48.12', '00:90:E8:4A:2B:12', 'Lipetsk Furnace #3 Bay', NULL, NULL, 'v4.2.1-sec', NULL, NULL, NULL, NULL, 'Active', '2026-09-27 17:43:34', 'Degraded (14% Loss)', 'Monitors high-temp pyrometer array for furnace #3 blast cycle.'),
(14, 'VP-BIO-FAB-03', NULL, NULL, NULL, 'Cleanroom RFID/Biometric Airlock', 'Suprema FaceStation 2', NULL, '10.240.90.03', '00:17:7D:6F:12:03', 'Nanofabrication Bay C', NULL, NULL, 'v1.4.0', NULL, NULL, NULL, NULL, 'Active', '2026-09-27 17:43:34', 'Online (Active)', 'Facial recognition terminal for cleanroom gowning chamber.'),
(15, 'VP-SRV-EDGE-01', NULL, NULL, NULL, 'Industrial Rugged Xeon Server', 'Cisco Catalyst IE3400', NULL, '10.240.15.10', '70:B3:17:00:15:10', 'Industrial Edge Switch Bay 4', NULL, NULL, 'IOS-XE 17.6', NULL, NULL, NULL, NULL, 'Active', '2026-09-27 17:43:34', 'Online (Active)', 'Hardened industrial switch for SCADA subnet isolation.'),
(16, 'VP-CAL-TEMP-01', NULL, NULL, NULL, 'Optical Pyrometer Standard', 'Fluke 9144 Field Metrology', NULL, '10.240.35.22', '00:1C:17:55:35:22', 'Thermal Calibration Bay', NULL, NULL, 'v2.10', NULL, NULL, NULL, NULL, 'Active', '2026-09-27 17:43:34', 'Online (Active)', 'Precision temperature well for RTD and thermocouple verification.'),
(17, 'VP-GW-LIP-04', NULL, NULL, NULL, 'Modbus TCP/IP to RS-485 Gateway', 'Moxa MGate MB3170', NULL, '10.240.48.14', '00:90:E8:4A:2B:14', 'Lipetsk Furnace #4 Bay', NULL, NULL, 'v4.2.2-sec', NULL, NULL, NULL, NULL, 'Active', '2026-09-27 17:43:34', 'Online (Active)', 'Monitors high-temp pyrometer array for furnace #4.'),
(18, 'VP-BIO-FAB-04', NULL, NULL, NULL, 'Cleanroom RFID/Biometric Airlock', 'Suprema BioEntry Plus', NULL, '10.240.90.04', '00:17:7D:6F:12:04', 'Chemical Synthesis Airlock D', NULL, NULL, 'v2.15.1', NULL, NULL, NULL, NULL, 'Active', '2026-09-27 17:43:34', 'Online (Active)', 'Chemical isolation room RFID interlock reader.'),
(19, 'VP-SRV-FAT-03', NULL, NULL, NULL, 'Industrial Rugged Xeon Server', 'Advantech MIC-7700', NULL, '10.240.12.83', '00:0B:AB:44:9D:E3', 'FAT Acceptance Bay #3', NULL, NULL, 'Ubuntu 22.04 RT', NULL, NULL, NULL, NULL, 'Active', '2026-09-27 17:43:34', 'Online (Active)', 'Hardware-in-the-loop validation server.'),
(20, 'VP-CAL-OPTI-04', NULL, NULL, NULL, 'Optical Pyrometer Standard', 'Yokogawa AQ6370E', NULL, '10.240.33.18', '00:80:A1:99:C7:04', 'Optical Spectrum Lab #2', NULL, NULL, 'v1.12', NULL, NULL, NULL, NULL, 'Active', '2026-09-27 17:43:34', 'Calibrated (Nominal)', 'Next-gen optical spectrum analyzer rig.');

-- --------------------------------------------------------

--
-- Table structure for table `job_postings`
--

CREATE TABLE `job_postings` (
  `posting_id` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `department_code` varchar(4) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 1,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `location` varchar(100) DEFAULT 'Almaty Industrial Complex (HQ)',
  `employment_type` varchar(50) DEFAULT 'Full-Time',
  `experience_level` varchar(50) DEFAULT 'Mid-Senior Level',
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_postings`
--

INSERT INTO `job_postings` (`posting_id`, `title`, `department_code`, `is_published`, `posted_at`, `location`, `employment_type`, `experience_level`, `description`) VALUES
(1, 'Senior Industrial SCADA Engineer', 'ENG', 1, '2026-09-01 06:00:00', 'Almaty Instrumentation Complex', 'Full-Time', 'Senior (5+ yrs)', 'Lead the engineering, deployment, and testing of next-generation SCADA and distributed telemetry systems across high-criticality industrial operations.'),
(2, 'Metrology Metrologist & Pressure Calibration Specialist', 'ENG', 1, '2026-09-05 07:00:00', 'Atyrau Metrology Lab', 'Full-Time', 'Specialist (3+ yrs)', 'Perform precision calibration, hydrostatic pressure verification, and sensor validation according to national metrology standards (ST RK).'),
(3, 'B2B Technical Sales Account Manager', 'SAL', 1, '2026-09-10 08:00:00', 'Astana Corporate Office', 'Full-Time', 'Mid-Level (2+ yrs)', 'Manage enterprise client accounts, prepare technical RFQs, and deliver specialized industrial equipment solutions to heavy industry partners.'),
(4, 'Cybersecurity & Governance Auditor (ST RK 27001)', 'ADM', 1, '2026-09-12 11:00:00', 'Almaty HQ / Hybrid', 'Full-Time', 'Senior Lead', 'Execute internal cyber governance audits, manage ST RK 27001 compliance frameworks, and oversee security policies across industrial automation assets.');

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_base_articles`
--

CREATE TABLE `knowledge_base_articles` (
  `kb_id` int(11) NOT NULL,
  `article_code` varchar(50) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `created_by_emp_id` varchar(10) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `views_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `knowledge_base_articles`
--

INSERT INTO `knowledge_base_articles` (`kb_id`, `article_code`, `title`, `summary`, `content`, `category`, `tags`, `created_by_emp_id`, `updated_at`, `views_count`) VALUES
(1, 'KB-1020', 'Standard WireGuard VPN Tunnel Setup for Field Calibration Teams', NULL, 'Detailed guide for establishing secure encrypted WireGuard links from field measurement stations to Almaty Central Telemetry.', 'Network Security', NULL, 'EMP-1018', '2026-08-15 07:00:00', 0),
(2, 'KB-1021', 'FIDO2 Hardware Token Enrollment & Emergency Recovery Runbook', NULL, 'Instructions for enrolling YubiKey FIDO2 tokens for L2/L3 operations and verifying zero-trust biometric signatures.', 'Identity & Access', NULL, 'EMP-1004', '2026-09-01 11:30:00', 0),
(3, 'KB-1022', 'SCADA Modbus TCP/IP Telemetry Packet Capture Procedure', NULL, 'Diagnostic steps for taking packet dumps on industrial flow measurement nodes without introducing jitter or latency.', 'Industrial Telemetry', NULL, 'EMP-1017', '2026-09-10 13:00:00', 0),
(4, 'KB-4091', 'RS-485 Modbus Serial EMI Troubleshooting', 'Step-by-step failover procedure for Moxa MB3170 gateways when induction furnace interference degrades copper bus signal-to-noise ratio.', '1. Verify CRC error count via switch port stats or gateway diagnostic page (http://10.240.48.12).\n2. If packet loss exceeds 5%, decouple primary copper pair at terminal block TB-3.\n3. Enable optically-isolated Channel B2 on secondary DIN-rail multiplexer.\n4. Set baud rate to 115200, parity Even, stop bit 1.\n5. Verify zero frame drop during thermal ramp cycles exceeding 1,400°C.', 'SCADA NETWORKING', 'Modbus, RS-485, Moxa, EMI, SCADA', 'EMP-1018', '2026-09-27 15:36:28', 0),
(5, 'KB-3184', 'Cleanroom ISO Class 4 Airlock Interlock Recovery', 'Emergency bypass protocols, RFID reader recalibration, and pressure differential sensor zeroing for cleanroom bays A through D.', '1. Check differential pressure sensors across Airlock Bay B. Ensure pressure is maintained at +25 Pa relative to corridor.\n2. In event of smartcard rejection loop, trigger supervisor manual key override on control panel CP-02.\n3. Restart Suprema BioEntry daemon via SSH: systemctl restart bioentry-agent.\n4. Re-sync smartcard badge whitelist from Employee Intranet Database (SYS 04).', 'ACCESS CONTROL', 'Cleanroom, RFID, Suprema, Interlock, Access', 'EMP-1018', '2026-09-27 15:36:28', 0),
(6, 'KB-2015', 'Hardware Security Module (HSM) Token Renewal', 'Cryptographic key issuance and CSR generation for automated test sign-off compliant with GOST R 34.12-2015 algorithms.', '1. Insert master operator YubiKey into security vault terminal.\n2. Execute PKI enrollment tool: pkcs11-tool --module /usr/lib/libthales.so --login --keypairgen.\n3. Export Certificate Signing Request (CSR) to File Center (SYS 09).\n4. Request L3 Security Officer attestation signature from Admin Governance (SYS 11).', 'PKI INFRASTRUCTURE', 'PKI, HSM, Tokens, GOST, Cryptography', 'EMP-1018', '2026-09-27 15:36:28', 0),
(7, 'KB-4091', 'RS-485 Modbus Serial EMI Troubleshooting', 'Step-by-step failover procedure for Moxa MB3170 gateways when induction furnace interference degrades copper bus signal-to-noise ratio.', '1. Check line impedance using 120-ohm termination resistor switches at both bus ends.\n2. Measure ground potential offset across terminal SG and chassis ground; maximum allowable is < 7V RMS.\n3. If noise exceeds 200mV peak-to-peak, activate optical isolation mode on port DIP switch #3.\n4. Run Modbus poll diagnostics: sudo mbpoll -m rtu -a 1 -b 19200 -d 8 -s 1 -p none /dev/ttyUSB0\n5. Verify 0 CRC errors over 10,000 continuous query cycles before returning bus to production.', 'SCADA NETWORKING', 'SCADA, RS485, Moxa, EMI, Pyrometer', NULL, '2026-09-27 15:44:58', 284),
(8, 'KB-3184', 'Cleanroom ISO Class 4 Airlock Interlock Recovery', 'Emergency bypass protocols, RFID reader recalibration, and pressure differential sensor zeroing for cleanroom bays A through D.', '1. Inspect airlock differential pressure sensor: reading must stay between 25Pa and 40Pa relative to gowning buffer.\n2. In case of badge rejection, verify Suprema BioEntry W2 controller status in Wiegand mode 34-bit.\n3. Cycle airlock logic interlock PLC (Siemens S7-1200) via key switch SW-2 in cleanroom electrical closet.\n4. Emergency bypass override: Turn safety lock key clockwise 90 degrees; this logs an unmaskable security audit event in SYS 07.\n5. Re-authenticate Level 3 badge to confirm automated magnetic strike clearance.', 'ACCESS CONTROL', 'Cleanroom, RFID, Airlock, Interlock, Suprema', NULL, '2026-09-27 15:44:58', 196),
(9, 'KB-2015', 'Hardware Security Module (HSM) Token Renewal', 'Cryptographic key issuance and CSR generation for automated test sign-off compliant with GOST R 34.12-2015 algorithms.', '1. Connect authorized cryptographic token (JaCarta-2 GOST / Rutoken EDS 3.0) to workstation USB port.\n2. Launch CSP CryptoPro 5.0 administration utility.\n3. Generate container CSR: cryptcp -creatrqst -dn \"CN=Alexey Ivanov, OU=IT Ops, O=VOSTOKPRIBOR\" -cont \"FAT-SIGN-2026\" req.req\n4. Submit request to internal Sub-CA: https://ca.vostokpribor.local/certsrv\n5. Install issued X.509 certificate into token container and verify signature on test PDF protocol.', 'PKI INFRASTRUCTURE', 'PKI, HSM, GOST, Security, Certificate', NULL, '2026-09-27 15:44:58', 142),
(10, 'KB-4091', 'RS-485 Modbus Serial EMI Troubleshooting', 'Step-by-step failover procedure for Moxa MB3170 gateways when induction furnace interference degrades copper bus signal-to-noise ratio.', '1. Check line impedance using 120-ohm termination resistor switches at both bus ends.\n2. Measure ground potential offset across terminal SG and chassis ground; maximum allowable is < 7V RMS.\n3. If noise exceeds 200mV peak-to-peak, activate optical isolation mode on port DIP switch #3.\n4. Run Modbus poll diagnostics: sudo mbpoll -m rtu -a 1 -b 19200 -d 8 -s 1 -p none /dev/ttyUSB0\n5. Verify 0 CRC errors over 10,000 continuous query cycles before returning bus to production.', 'SCADA NETWORKING', 'SCADA, RS485, Moxa, EMI, Pyrometer', NULL, '2026-09-27 15:46:26', 284),
(11, 'KB-3184', 'Cleanroom ISO Class 4 Airlock Interlock Recovery', 'Emergency bypass protocols, RFID reader recalibration, and pressure differential sensor zeroing for cleanroom bays A through D.', '1. Inspect airlock differential pressure sensor: reading must stay between 25Pa and 40Pa relative to gowning buffer.\n2. In case of badge rejection, verify Suprema BioEntry W2 controller status in Wiegand mode 34-bit.\n3. Cycle airlock logic interlock PLC (Siemens S7-1200) via key switch SW-2 in cleanroom electrical closet.\n4. Emergency bypass override: Turn safety lock key clockwise 90 degrees; this logs an unmaskable security audit event in SYS 07.\n5. Re-authenticate Level 3 badge to confirm automated magnetic strike clearance.', 'ACCESS CONTROL', 'Cleanroom, RFID, Airlock, Interlock, Suprema', NULL, '2026-09-27 15:46:26', 196),
(12, 'KB-2015', 'Hardware Security Module (HSM) Token Renewal', 'Cryptographic key issuance and CSR generation for automated test sign-off compliant with GOST R 34.12-2015 algorithms.', '1. Connect authorized cryptographic token (JaCarta-2 GOST / Rutoken EDS 3.0) to workstation USB port.\n2. Launch CSP CryptoPro 5.0 administration utility.\n3. Generate container CSR: cryptcp -creatrqst -dn \"CN=Alexey Ivanov, OU=IT Ops, O=VOSTOKPRIBOR\" -cont \"FAT-SIGN-2026\" req.req\n4. Submit request to internal Sub-CA: https://ca.vostokpribor.local/certsrv\n5. Install issued X.509 certificate into token container and verify signature on test PDF protocol.', 'PKI INFRASTRUCTURE', 'PKI, HSM, GOST, Security, Certificate', NULL, '2026-09-27 15:46:26', 142),
(13, 'KB-4091', 'RS-485 Modbus Serial EMI Troubleshooting', 'Step-by-step failover procedure for Moxa MB3170 gateways when induction furnace interference degrades copper bus signal-to-noise ratio.', '1. Check line impedance using 120-ohm termination resistor switches at both bus ends.\n2. Measure ground potential offset across terminal SG and chassis ground; maximum allowable is < 7V RMS.\n3. If noise exceeds 200mV peak-to-peak, activate optical isolation mode on port DIP switch #3.\n4. Run Modbus poll diagnostics: sudo mbpoll -m rtu -a 1 -b 19200 -d 8 -s 1 -p none /dev/ttyUSB0\n5. Verify 0 CRC errors over 10,000 continuous query cycles before returning bus to production.', 'SCADA NETWORKING', 'SCADA, RS485, Moxa, EMI, Pyrometer', NULL, '2026-09-27 15:46:48', 284),
(14, 'KB-3184', 'Cleanroom ISO Class 4 Airlock Interlock Recovery', 'Emergency bypass protocols, RFID reader recalibration, and pressure differential sensor zeroing for cleanroom bays A through D.', '1. Inspect airlock differential pressure sensor: reading must stay between 25Pa and 40Pa relative to gowning buffer.\n2. In case of badge rejection, verify Suprema BioEntry W2 controller status in Wiegand mode 34-bit.\n3. Cycle airlock logic interlock PLC (Siemens S7-1200) via key switch SW-2 in cleanroom electrical closet.\n4. Emergency bypass override: Turn safety lock key clockwise 90 degrees; this logs an unmaskable security audit event in SYS 07.\n5. Re-authenticate Level 3 badge to confirm automated magnetic strike clearance.', 'ACCESS CONTROL', 'Cleanroom, RFID, Airlock, Interlock, Suprema', NULL, '2026-09-27 15:46:48', 196),
(15, 'KB-2015', 'Hardware Security Module (HSM) Token Renewal', 'Cryptographic key issuance and CSR generation for automated test sign-off compliant with GOST R 34.12-2015 algorithms.', '1. Connect authorized cryptographic token (JaCarta-2 GOST / Rutoken EDS 3.0) to workstation USB port.\n2. Launch CSP CryptoPro 5.0 administration utility.\n3. Generate container CSR: cryptcp -creatrqst -dn \"CN=Alexey Ivanov, OU=IT Ops, O=VOSTOKPRIBOR\" -cont \"FAT-SIGN-2026\" req.req\n4. Submit request to internal Sub-CA: https://ca.vostokpribor.local/certsrv\n5. Install issued X.509 certificate into token container and verify signature on test PDF protocol.', 'PKI INFRASTRUCTURE', 'PKI, HSM, GOST, Security, Certificate', NULL, '2026-09-27 15:46:48', 142),
(16, 'KB-4091', 'RS-485 Modbus Serial EMI Troubleshooting', 'Step-by-step failover procedure for Moxa MB3170 gateways when induction furnace interference degrades copper bus signal-to-noise ratio.', '1. Check line impedance using 120-ohm termination resistor switches at both bus ends.\n2. Measure ground potential offset across terminal SG and chassis ground; maximum allowable is < 7V RMS.\n3. If noise exceeds 200mV peak-to-peak, activate optical isolation mode on port DIP switch #3.\n4. Run Modbus poll diagnostics: sudo mbpoll -m rtu -a 1 -b 19200 -d 8 -s 1 -p none /dev/ttyUSB0\n5. Verify 0 CRC errors over 10,000 continuous query cycles before returning bus to production.', 'SCADA NETWORKING', 'SCADA, RS485, Moxa, EMI, Pyrometer', NULL, '2026-09-27 15:47:20', 284),
(17, 'KB-3184', 'Cleanroom ISO Class 4 Airlock Interlock Recovery', 'Emergency bypass protocols, RFID reader recalibration, and pressure differential sensor zeroing for cleanroom bays A through D.', '1. Inspect airlock differential pressure sensor: reading must stay between 25Pa and 40Pa relative to gowning buffer.\n2. In case of badge rejection, verify Suprema BioEntry W2 controller status in Wiegand mode 34-bit.\n3. Cycle airlock logic interlock PLC (Siemens S7-1200) via key switch SW-2 in cleanroom electrical closet.\n4. Emergency bypass override: Turn safety lock key clockwise 90 degrees; this logs an unmaskable security audit event in SYS 07.\n5. Re-authenticate Level 3 badge to confirm automated magnetic strike clearance.', 'ACCESS CONTROL', 'Cleanroom, RFID, Airlock, Interlock, Suprema', NULL, '2026-09-27 15:47:20', 196),
(18, 'KB-2015', 'Hardware Security Module (HSM) Token Renewal', 'Cryptographic key issuance and CSR generation for automated test sign-off compliant with GOST R 34.12-2015 algorithms.', '1. Connect authorized cryptographic token (JaCarta-2 GOST / Rutoken EDS 3.0) to workstation USB port.\n2. Launch CSP CryptoPro 5.0 administration utility.\n3. Generate container CSR: cryptcp -creatrqst -dn \"CN=Alexey Ivanov, OU=IT Ops, O=VOSTOKPRIBOR\" -cont \"FAT-SIGN-2026\" req.req\n4. Submit request to internal Sub-CA: https://ca.vostokpribor.local/certsrv\n5. Install issued X.509 certificate into token container and verify signature on test PDF protocol.', 'PKI INFRASTRUCTURE', 'PKI, HSM, GOST, Security, Certificate', NULL, '2026-09-27 15:47:20', 142),
(19, 'KB-4091', 'RS-485 Modbus Serial EMI Troubleshooting', 'Step-by-step failover procedure for Moxa MB3170 gateways when induction furnace interference degrades copper bus signal-to-noise ratio.', '1. Check line impedance using 120-ohm termination resistor switches at both bus ends.\n2. Measure ground potential offset across terminal SG and chassis ground; maximum allowable is < 7V RMS.\n3. If noise exceeds 200mV peak-to-peak, activate optical isolation mode on port DIP switch #3.\n4. Run Modbus poll diagnostics: sudo mbpoll -m rtu -a 1 -b 19200 -d 8 -s 1 -p none /dev/ttyUSB0\n5. Verify 0 CRC errors over 10,000 continuous query cycles before returning bus to production.', 'SCADA NETWORKING', 'SCADA, RS485, Moxa, EMI, Pyrometer', NULL, '2026-09-27 15:51:00', 284),
(20, 'KB-3184', 'Cleanroom ISO Class 4 Airlock Interlock Recovery', 'Emergency bypass protocols, RFID reader recalibration, and pressure differential sensor zeroing for cleanroom bays A through D.', '1. Inspect airlock differential pressure sensor: reading must stay between 25Pa and 40Pa relative to gowning buffer.\n2. In case of badge rejection, verify Suprema BioEntry W2 controller status in Wiegand mode 34-bit.\n3. Cycle airlock logic interlock PLC (Siemens S7-1200) via key switch SW-2 in cleanroom electrical closet.\n4. Emergency bypass override: Turn safety lock key clockwise 90 degrees; this logs an unmaskable security audit event in SYS 07.\n5. Re-authenticate Level 3 badge to confirm automated magnetic strike clearance.', 'ACCESS CONTROL', 'Cleanroom, RFID, Airlock, Interlock, Suprema', NULL, '2026-09-27 15:51:00', 196),
(21, 'KB-2015', 'Hardware Security Module (HSM) Token Renewal', 'Cryptographic key issuance and CSR generation for automated test sign-off compliant with GOST R 34.12-2015 algorithms.', '1. Connect authorized cryptographic token (JaCarta-2 GOST / Rutoken EDS 3.0) to workstation USB port.\n2. Launch CSP CryptoPro 5.0 administration utility.\n3. Generate container CSR: cryptcp -creatrqst -dn \"CN=Alexey Ivanov, OU=IT Ops, O=VOSTOKPRIBOR\" -cont \"FAT-SIGN-2026\" req.req\n4. Submit request to internal Sub-CA: https://ca.vostokpribor.local/certsrv\n5. Install issued X.509 certificate into token container and verify signature on test PDF protocol.', 'PKI INFRASTRUCTURE', 'PKI, HSM, GOST, Security, Certificate', NULL, '2026-09-27 15:51:00', 142);

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `lead_id` int(11) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `company_name` varchar(150) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `source_page` varchar(100) DEFAULT NULL,
  `status` enum('New','Qualified','Converted','Rejected') DEFAULT 'New',
  `assigned_sales_emp_id` varchar(10) DEFAULT NULL,
  `converted_cus_id` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `estimated_value` decimal(14,2) DEFAULT 0.00,
  `lead_score` int(11) DEFAULT 80,
  `equipment_scope` varchar(255) DEFAULT NULL,
  `priority` varchar(50) DEFAULT 'Normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`lead_id`, `full_name`, `email`, `phone`, `company_name`, `message`, `source_page`, `status`, `assigned_sales_emp_id`, `converted_cus_id`, `created_at`, `estimated_value`, `lead_score`, `equipment_scope`, `priority`) VALUES
(1, 'Dr. Andrei Vasiliev', 'a.vasiliev@chelpipe.ru', '+7 (351) 259-8801', 'Chelyabinsk Pipe Plant', 'Seeking seamless casing ultrasonic flaw detection skid for pipe rolling mill #8.', 'Web RFQ', 'Qualified', 'EMP-1010', NULL, '2026-09-29 19:25:06', 1450000.00, 94, 'Seamless Casing Ultrasonic Flaw Detection Skid', 'High'),
(2, 'Valery Semenov', 'v.semenov@mmk.ru', '+7 (3519) 24-0012', 'Magnitogorsk Iron & Steel Works', 'Multi-channel gas optical spectrometry matrix required for converter shop #2.', 'Direct Inbound', 'Converted', 'EMP-1008', 'CUS-1008', '2026-09-29 19:25:06', 2280000.00, 91, 'Multi-Channel Gas Optical Spectrometry Matrix', 'High'),
(3, 'Irina Kondratieva', 'i.kondratieva@uralchem.com', '+7 (342) 219-4400', 'Uralchem Mineral Fertilizers', 'Engineering inquiry for ammonia synthesis high-temp pressure sensor telemetry.', 'Direct Referral', 'Converted', 'EMP-1006', 'CUS-1009', '2026-09-29 19:25:06', 890000.00, 86, 'Ammonia Synthesis High-Temp Pressure Sensors', 'Normal'),
(4, 'Nursultan Kadyrov', 'n.kadyrov@kaztransgas.kz', '+7 (717) 255-8801', 'KazTransGas Distribution', 'Requesting commercial quote for 40 units of Metrotec-500 Flow Analyzers.', 'Corporate Web', 'Qualified', 'EMP-1007', NULL, '2026-09-29 19:25:06', 450000.00, 80, '40x Metrotec-500 Flow Analyzers', 'Normal'),
(5, 'Olga Demidova', 'o.demidova@severstal.ru', '+7 (820) 256-4422', 'Severstal Metallurgy PJSC', 'Follow-up regarding proposal for furnace pressure differential gauges.', 'Direct Referral', 'Converted', 'EMP-1006', NULL, '2026-09-29 19:25:06', 640000.00, 95, 'Hydraulic Pressure Differential Gauges', 'High'),
(6, 'Yerbol Sadykov', 'yerbol@bogatyr.kz', '+7 (718) 722-1144', 'Bogatyr Coal Mining', 'Seeking vibration analysis telemetry sensors for open-pit excavators.', 'B2B Shop', 'New', 'EMP-1008', NULL, '2026-09-29 19:25:06', 320000.00, 74, 'Open-Pit Excavator Vibration Sensors', 'Normal'),
(7, 'Viktor Turbinevich', 'viktor@uralturbine.ru', '+7 343 555-8899', 'Ural Test Turbine LLC', 'Direct technical inquiry logged.', 'CRM Manual Entry', 'Qualified', 'EMP-1006', NULL, '2026-09-29 16:55:23', 450000.00, 85, 'Gas Turbine Sensors', 'High'),
(10, 'Viktor Turbinevich', 'viktor@uralturbine.ru', '+7 343 555-8899', 'Ural Test Turbine LLC', 'Direct technical inquiry logged.', 'CRM Manual Entry', 'Qualified', 'EMP-1006', NULL, '2026-09-29 16:59:57', 450000.00, 85, 'Gas Turbine Sensors', 'High'),
(11, 'Alexey Leadman', 'alexey@directfacility.ru', '', 'Direct Lead Facility', 'Direct technical inquiry logged.', 'CRM Manual Entry', 'Qualified', 'EMP-1006', NULL, '2026-09-29 17:08:32', 380000.00, 85, 'Instrumentation Telemetry Skid', 'High'),
(100000, 'Alexey Leadman', 'alexey@directfacility.ru', '', 'Direct Lead Facility', 'Direct technical inquiry logged.', 'CRM Manual Entry', 'Qualified', 'EMP-1006', NULL, '2026-09-29 17:09:16', 380000.00, 85, 'Instrumentation Telemetry Skid', 'High'),
(100001, 'Dynamic Lead', 'dyn@test.ru', NULL, 'Dynamic Test Co', NULL, NULL, 'Converted', NULL, 'CUS-1016', '2026-09-29 17:09:27', 0.00, 80, NULL, 'Normal');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `leave_id` int(11) NOT NULL,
  `emp_id` varchar(10) NOT NULL,
  `leave_type` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `approved_by_emp_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`leave_id`, `emp_id`, `leave_type`, `start_date`, `end_date`, `status`, `approved_by_emp_id`) VALUES
(1, 'EMP-1007', 'Annual Leave', '2026-10-01', '2026-10-08', 'Pending', NULL),
(2, 'EMP-1008', 'Sick Leave', '2026-09-22', '2026-09-25', 'Rejected', 'EMP-0001'),
(3, 'EMP-1016', 'Paternity Leave', '2026-10-15', '2026-10-30', 'Rejected', 'EMP-0001'),
(4, 'EMP-1012', 'Annual Leave', '2026-08-10', '2026-08-17', 'Approved', 'EMP-1002'),
(5, 'EMP-1009', 'Study Leave', '2026-09-01', '2026-09-05', 'Approved', 'EMP-1006'),
(6, 'EMP-1013', 'Personal Leave', '2026-07-14', '2026-07-16', 'Approved', 'EMP-1002'),
(7, 'EMP-1017', 'Annual Leave', '2026-06-01', '2026-06-12', 'Approved', 'EMP-1004'),
(8, 'EMP-1021', 'Technical Sabbatical', '2026-10-05', '2026-10-12', 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `opportunities`
--

CREATE TABLE `opportunities` (
  `opp_id` int(11) NOT NULL,
  `lead_id` int(11) DEFAULT NULL,
  `cus_id` varchar(10) DEFAULT NULL,
  `sales_emp_id` varchar(10) DEFAULT NULL,
  `stage` varchar(50) NOT NULL DEFAULT 'Qualification',
  `estimated_value` decimal(14,2) DEFAULT NULL,
  `expected_close_date` date DEFAULT NULL,
  `opp_title` varchar(255) DEFAULT NULL,
  `probability_percent` int(11) DEFAULT 50,
  `is_confidential` tinyint(1) DEFAULT 1,
  `priority` varchar(20) DEFAULT 'high'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `opportunities`
--

INSERT INTO `opportunities` (`opp_id`, `lead_id`, `cus_id`, `sales_emp_id`, `stage`, `estimated_value`, `expected_close_date`, `opp_title`, `probability_percent`, `is_confidential`, `priority`) VALUES
(1, NULL, 'CUS-1004', 'EMP-1008', 'Negotiation', 1650000.00, '2026-11-15', 'Rail Mill Laser Profiler & Flaw Detection Array', 80, 1, 'high'),
(2, NULL, 'CUS-1006', 'EMP-1007', 'Negotiation', 3100000.00, '2026-12-10', 'Refinery Catalytic Cracking Gas Analysis Skid', 80, 1, 'high'),
(3, NULL, 'CUS-1002', 'EMP-1010', 'Proposal', 920000.00, '2026-11-20', 'Coke Oven Battery Temperature Profiling & IR Cameras', 60, 0, 'medium'),
(4, NULL, 'CUS-1001', 'EMP-1006', 'Proposal', 410000.00, '2026-12-05', 'Continuous Casting Machine #3 Optical Thickness Gauges', 65, 0, 'medium'),
(5, NULL, 'CUS-1001', 'EMP-1006', 'Negotiation', 1850000.00, '2026-11-28', 'Blast Furnace #5 Automation & Gas Analysis', 85, 1, 'high'),
(6, NULL, 'CUS-1003', 'EMP-1007', 'Negotiation', 2400000.00, '2026-12-18', 'Talnakh Concentrator Flotation Telemetry Grid', 80, 1, 'high'),
(7, NULL, 'CUS-1001', 'EMP-1006', 'Contract', 640000.00, '2026-11-25', 'Hydraulic Pressure Sensor Telemetry Retrofit', 95, 0, 'high'),
(8, NULL, 'CUS-1007', 'EMP-1010', 'Contract', 1450000.00, '2026-12-15', 'Seamless Casing Ultrasonic Flaw Detection Skid', 90, 1, 'high'),
(9, NULL, 'CUS-1005', 'EMP-1006', 'Won', 418200.00, '2026-10-12', 'High-Pressure Flowmeter HPF-900X Replacement Batch', 100, 0, 'low'),
(10, NULL, 'CUS-1002', 'EMP-1010', 'Won', 396400.00, '2026-10-08', 'Turbine Bearing Vibration Transducers (x16)', 100, 0, 'medium'),
(12, 2, 'CUS-1008', 'EMP-1008', 'Proposal', 600000.00, '2026-11-13', 'Multi-Channel Gas Optical Spectrometry Matrix', 60, 1, 'high'),
(13, NULL, 'CUS-1001', 'EMP-1006', 'Proposal', 320000.00, '2026-11-28', 'API Automated Opp Validation', 40, 0, 'high'),
(16, 2, 'CUS-1008', 'EMP-1008', 'Proposal', 600000.00, '2026-11-13', 'Multi-Channel Gas Optical Spectrometry Matrix', 60, 1, 'high'),
(17, NULL, 'CUS-1001', 'EMP-1006', 'Proposal', 320000.00, '2026-11-28', 'API Automated Opp Validation', 40, 0, 'high'),
(18, 3, 'CUS-1009', 'EMP-1006', 'Proposal', 500000.00, '2026-11-13', 'Ammonia Synthesis High-Temp Pressure Sensors', 60, 1, 'high'),
(19, NULL, 'CUS-1001', 'EMP-1006', 'Proposal', 280000.00, '2026-11-13', 'Direct Opp Validation', 60, 0, 'high'),
(100000, NULL, 'CUS-1001', 'EMP-1006', 'Proposal', 280000.00, '2026-11-13', 'Direct Opp Validation', 60, 0, 'high'),
(100001, 100001, 'CUS-1016', 'EMP-1006', 'Proposal', 400000.00, '2026-11-13', 'Dynamic Test Co Expansion', 60, 1, 'high');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `cus_id` varchar(10) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(30) DEFAULT 'Cart',
  `total_amount` decimal(14,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `cus_id`, `order_date`, `status`, `total_amount`) VALUES
(1, 'CUS-1001', '2026-09-15 05:30:00', 'Shipped', 48500.00),
(2, 'CUS-1003', '2026-09-18 08:20:00', 'Processing', 45000.00),
(3, 'CUS-1005', '2026-09-20 11:10:00', 'Processing', 31500.00),
(4, 'CUS-1006', '2026-09-10 13:45:00', 'Delivered', 85000.00),
(5, 'CUS-1010', '2026-09-21 06:00:00', 'Processing', 192000.00),
(8, 'CUS-1001', '2026-09-29 23:14:46', 'Processing', 45000.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `prod_id` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `prod_id`, `quantity`, `unit_price`) VALUES
(1, 1, 'PROD-1001', 10, 4850.00),
(2, 1, 'PROD-1002', 1, 45000.00),
(3, 2, 'PROD-1003', 8, 18500.00),
(4, 2, 'PROD-1004', 15, 10000.00),
(5, 3, 'PROD-1005', 20, 22500.00),
(8, 8, 'PROD-1001', 1, 45000.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `tx_reference` varchar(100) DEFAULT NULL,
  `sender_name` varchar(150) DEFAULT NULL,
  `inv_id` varchar(15) DEFAULT NULL,
  `amount` decimal(14,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `remittance_memo` varchar(255) DEFAULT NULL,
  `bank_gateway` varchar(100) DEFAULT 'SPFS / Central Clearing',
  `reconciled` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `tx_reference`, `sender_name`, `inv_id`, `amount`, `payment_date`, `method`, `remittance_memo`, `bank_gateway`, `reconciled`) VALUES
(1, NULL, NULL, 'INV-2026-001', 125000.00, '2026-09-12', 'Bank Swift Transfer', NULL, 'SPFS / Central Clearing', 1),
(2, NULL, NULL, 'INV-2026-002', 88400.00, '2026-09-14', 'Corporate Wire', NULL, 'SPFS / Central Clearing', 1),
(3, NULL, NULL, 'INV-2026-003', 340000.00, '2026-09-17', 'Letter of Credit', NULL, 'SPFS / Central Clearing', 1),
(4, NULL, NULL, 'INV-2026-004', 54200.00, '2026-09-19', 'Direct Clearing', NULL, 'SPFS / Central Clearing', 1),
(5, 'TX-SPFS-9457', 'Tashkent Precision Controls', NULL, 68000.00, '2026-09-24', 'SPFS Direct Clearance', 'Ref: WIRE-INVOICE-979', 'Sberbank / Central Clearing Node', 1);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_name` varchar(150) DEFAULT NULL,
  `system_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `permission_name`, `system_name`) VALUES
(1, 'ACCESS_ADMIN_GOVERNANCE', 'Admin & Governance Portal'),
(2, 'EXECUTE_BREAK_GLASS', 'Admin & Governance Portal'),
(3, 'INITIATE_LOCKDOWN', 'Admin & Governance Portal'),
(4, 'VIEW_CRM_CUSTOMERS', 'CRM System'),
(5, 'MANAGE_CRM_DEALS', 'CRM System'),
(6, 'VIEW_CUSTOMER_DOCUMENTS', 'Customer Portal'),
(7, 'DOWNLOAD_INVOICES', 'Customer Portal'),
(8, 'ACCESS_DEV_SANDBOX', 'Developer Portal'),
(9, 'GENERATE_API_KEYS', 'Developer Portal'),
(10, 'VIEW_EMPLOYEE_DIRECTORY', 'Employee Intranet'),
(11, 'ACCESS_FILE_CENTER', 'File Center'),
(12, 'UPLOAD_TECHNICAL_DOCS', 'File Center'),
(13, 'APPROVE_BUDGETS', 'Finance & Billing'),
(14, 'PROCESS_PAYMENTS', 'Finance & Billing'),
(15, 'MANAGE_EMPLOYEES', 'HR System'),
(16, 'MANAGE_ONBOARDING', 'HR System'),
(17, 'VIEW_SUPPORT_TICKETS', 'IT Helpdesk'),
(18, 'RESOLVE_INCIDENTS', 'IT Helpdesk'),
(19, 'PLACE_B2B_ORDERS', 'Online Shop B2B'),
(20, 'VIEW_CATALOG_PRICING', 'Online Shop B2B'),
(21, 'VIEW_CORPORATE_PORTAL', 'Corporate Web Platform');

-- --------------------------------------------------------

--
-- Table structure for table `portal_accounts`
--

CREATE TABLE `portal_accounts` (
  `portal_user_id` int(11) NOT NULL,
  `cus_id` varchar(10) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `mfa_enabled` tinyint(1) DEFAULT 0,
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `portal_accounts`
--

INSERT INTO `portal_accounts` (`portal_user_id`, `cus_id`, `username`, `email`, `mfa_enabled`, `last_login`) VALUES
(1, 'CUS-1001', 'm.zhuma', 'm.zhuma@kaztransgas.kz', 1, '2026-09-23 07:15:00'),
(2, 'CUS-1002', 'd.smagulov', 'd.smagulov@samruk.kz', 1, '2026-09-23 06:30:00'),
(3, 'CUS-1003', 'e.kim', 'e.kim@kazzinc.com', 0, '2026-09-22 11:00:00'),
(4, 'CUS-1004', 'o.morozov', 'o.morozov@kegoc.kz', 1, '2026-09-21 13:45:00'),
(5, 'CUS-1005', 'y.akhmetov', 'y.akhmetov@kazmunaigas.kz', 1, '2026-09-23 08:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `portal_notifications`
--

CREATE TABLE `portal_notifications` (
  `notification_id` int(11) NOT NULL,
  `portal_user_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `related_entity_type` varchar(30) DEFAULT NULL,
  `related_entity_id` varchar(15) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `portal_notifications`
--

INSERT INTO `portal_notifications` (`notification_id`, `portal_user_id`, `message`, `related_entity_type`, `related_entity_id`, `is_read`, `created_at`) VALUES
(1, 1, 'New Calibration Certificate DOC-2026-001 is now available for download.', 'Document', 'DOC-2026-001', 0, '2026-09-23 05:00:00'),
(2, 2, 'Invoice INV-2026-002 has been generated and scheduled for payment.', 'Invoice', 'INV-2026-002', 1, '2026-09-22 06:00:00'),
(3, 5, 'Your bulk order ORD-2026-104 has been dispatched via Trans-Kazakhstan freight.', 'Order', 'ORD-2026-104', 0, '2026-09-23 08:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `portal_sessions`
--

CREATE TABLE `portal_sessions` (
  `session_id` int(11) NOT NULL,
  `portal_user_id` int(11) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `logout_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `portal_sessions`
--

INSERT INTO `portal_sessions` (`session_id`, `portal_user_id`, `login_time`, `logout_time`, `ip_address`) VALUES
(1, 1, '2026-09-23 07:15:00', '2026-09-23 08:45:00', '195.189.12.44'),
(2, 2, '2026-09-23 06:30:00', '2026-09-23 07:15:00', '212.154.200.18'),
(3, 5, '2026-09-23 08:20:00', '2026-09-23 09:00:00', '89.218.45.67');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `prod_id` varchar(10) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `billing_model` enum('PerUnit','PerProject','SubscriptionMonthly','SubscriptionAnnual','AnnualContract') NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`prod_id`, `product_name`, `billing_model`, `description`) VALUES
('PROD-1001', 'Industrial Optical Sensor Package', 'PerUnit', 'Multi-spectral 4K CMOS industrial inspection sensor with sapphire window, Modbus RTU / 4-20mA loop.'),
('PROD-1002', 'Precision Geodetic Measurement Kit', 'PerUnit', 'Sub-millimeter GNSS-RTK baseline receiver with dual constellation tracking and IP68 enclosure.'),
('PROD-1003', 'Automated Calibration Station', 'PerProject', 'Turnkey multi-axis automated pressure and thermal calibration bench with ISO 17025 certificate generator.'),
('PROD-1004', 'Industrial PLC Integration', 'PerProject', 'Fail-safe SIL-3 PLC telemetry integration skid with redundant Profinet and optical bypass.'),
('PROD-1005', 'Remote Monitoring Gateway', 'PerUnit', 'Edge computing telemetry hub with 4G/LTE failover, MQTT broker, and local SQLite buffer.'),
('PROD-1006', 'High-Temp Thermal Pyrometer', 'PerUnit', 'Non-contact infrared optical pyrometer for blast furnaces up to 1,800°C with air-purge collar.'),
('PROD-1007', 'Vibration Analysis Sensor Skid', 'PerUnit', 'Tri-axial piezoelectric accelerometer package for turbine bearings with FFT vibration analytics.'),
('PROD-1008', 'Electromagnetic Flowmeter Array', 'PerUnit', 'High-accuracy conductive fluid flowmeter with Hastelloy electrodes and digital pulse output.'),
('PROD-1009', 'Differential Pressure Transmitter', 'PerUnit', 'HART protocol differential pressure cell for orifice gas flow measurement with 0.05% accuracy.'),
('PROD-1010', 'SCADA Historian & Analytics Suite', 'AnnualContract', 'Enterprise operational data historian license with OPC-UA integration and predictive telemetry.');

-- --------------------------------------------------------

--
-- Table structure for table `product_inventory`
--

CREATE TABLE `product_inventory` (
  `prod_id` varchar(10) NOT NULL,
  `warehouse_location` varchar(100) DEFAULT NULL,
  `quantity_on_hand` int(11) DEFAULT 0,
  `reorder_level` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_inventory`
--

INSERT INTO `product_inventory` (`prod_id`, `warehouse_location`, `quantity_on_hand`, `reorder_level`) VALUES
('PROD-1001', 'WH-North-A1 (Shymkent Central)', 45, 15),
('PROD-1002', 'WH-Central-B3 (Almaty Hub)', 57, 15),
('PROD-1003', 'WH-East-C2 (Pavlodar Depot)', 69, 15),
('PROD-1004', 'WH-South-D4 (Karaganda Depot)', 81, 15),
('PROD-1005', 'WH-North-A1 (Shymkent Central)', 93, 15),
('PROD-1006', 'WH-Central-B3 (Almaty Hub)', 34, 15),
('PROD-1007', 'WH-North-A1 (Shymkent Central)', 28, 15),
('PROD-1008', 'WH-East-C2 (Pavlodar Depot)', 50, 15),
('PROD-1009', 'WH-South-D4 (Karaganda Depot)', 42, 15),
('PROD-1010', 'Digital Cloud License Repository', 500, 15);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `prj_id` varchar(15) NOT NULL,
  `project_name` varchar(150) DEFAULT NULL,
  `cus_id` varchar(10) NOT NULL,
  `project_manager_emp_id` varchar(10) DEFAULT NULL,
  `budget` decimal(14,2) DEFAULT NULL,
  `currency` char(3) DEFAULT 'EUR',
  `status` enum('Planning','Procurement','Design','Integration','Testing','Execution','ContractReview','Maintenance','Closed') DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `progress_percent` int(11) DEFAULT 0,
  `facility_location` varchar(255) DEFAULT NULL,
  `scope_summary` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`prj_id`, `project_name`, `cus_id`, `project_manager_emp_id`, `budget`, `currency`, `status`, `start_date`, `end_date`, `progress_percent`, `facility_location`, `scope_summary`) VALUES
('PRJ-VP-4411', 'Rail Mill Laser Profiler & Flaw Detection Array', 'CUS-1004', 'EMP-1008', 1650000.00, 'USD', 'Procurement', '2024-04-01', '2027-01-15', 35, 'Rail & Structural Mill · Nizhny Tagil', 'High-speed eddy-current flaw detector array for continuous railway rail profiles.'),
('PRJ-VP-5510', 'Continuous Casting Machine #3 Flow Control', 'CUS-1001', 'EMP-1018', 890000.00, 'USD', '', '2023-09-01', '2024-04-30', 100, 'Cherepovets Works - CCM3', 'Hydraulic actuator servo-valves overhaul and dual-redundant flow sensors.'),
('PRJ-VP-5520', 'Coke Oven Battery Temperature Profiling & IR Cameras', 'CUS-1002', 'EMP-1010', 920000.00, 'USD', 'Integration', '2024-02-15', '2026-11-20', 60, 'Coke Plant Shop #3 · Lipetsk', 'Continuous thermal imaging array with automatic hot-spot flame detection alerting.'),
('PRJ-VP-6610', 'Talnakh Concentrator Flotation Telemetry Grid', 'CUS-1003', 'EMP-1007', 2400000.00, 'USD', 'Execution', '2024-02-10', '2026-12-18', 80, 'Talnakh Concentrator Division · Norilsk', 'Sub-zero froth height acoustic telemetry and slurry density radiometric densitometer network.'),
('PRJ-VP-6840', 'Oxygen Converter Shop #2 Telemetry', 'CUS-1001', 'EMP-1004', 1240000.00, 'USD', '', '2024-08-15', '2025-03-31', 40, 'Cherepovets Works - BOF2', 'High-temp optical pyrometry loop integration and SIL-3 safety interlock calibration.'),
('PRJ-VP-7721', 'Blast Furnace #5 SCADA Modernization', 'CUS-1001', 'EMP-1006', 1850000.00, 'USD', 'Execution', '2024-01-15', '2026-11-28', 65, 'Cherepovets Ironmaking Plant #4', 'Blast furnace off-gas telemetry, CO/CO2 infrared optical analyzers, and high-temp probe arrays.'),
('PRJ-VP-7804', 'Hot Strip Mill #2 Hydraulic Pressure Sensor Telemetry Retrofit', 'CUS-1001', 'EMP-1010', 640000.00, 'USD', 'Integration', '2024-03-01', '2026-12-20', 45, 'Rolling Mill Bay 2 · Cherepovets', 'Retrofit 48 high-pressure hydraulic transducers with real-time SCADA Modbus TCP gateway.'),
('PRJ-VP-8902', 'Continuous Casting Machine #3 Optical Thickness Gauges', 'CUS-1001', 'EMP-1006', 410000.00, 'USD', 'Design', '2024-05-01', '2027-02-15', 18, 'Steelmaking Shop #1 · Cherepovets', 'Triangulation laser profilometer pair for slab hot-geometry dimensional verification.');

-- --------------------------------------------------------

--
-- Table structure for table `quotes`
--

CREATE TABLE `quotes` (
  `quote_id` int(11) NOT NULL,
  `cus_id` varchar(10) NOT NULL,
  `prod_id` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(12,2) DEFAULT NULL,
  `created_by_emp_id` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `quote_ref` varchar(50) DEFAULT NULL,
  `equipment_scope` varchar(255) DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `total_amount` decimal(14,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending Review'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quotes`
--

INSERT INTO `quotes` (`quote_id`, `cus_id`, `prod_id`, `quantity`, `unit_price`, `created_by_emp_id`, `created_at`, `quote_ref`, `equipment_scope`, `valid_until`, `total_amount`, `status`) VALUES
(1, 'CUS-1002', 'PROD-1001', 2, 26137.50, 'EMP-1010', '2026-09-29 19:25:06', 'QUO-9912', '16x High-Pressure Flowmeters HPF-900X with Industrial Hart Protocol', '2026-11-30', 680000.00, 'Approved'),
(2, 'CUS-1001', 'PROD-1002', 1, 340000.00, 'EMP-1006', '2026-09-29 19:25:06', 'QUO-9918', 'Blast Furnace #5 Optical Spectrometry Skid & Calibration Module', '2026-12-15', 420000.00, 'Delivered'),
(3, 'CUS-1003', 'PROD-1003', 3, 31000.00, 'EMP-1007', '2026-09-29 19:25:06', 'QUO-9924', 'Talnakh Flotation Telemetry Grid Transducers & Sub-Zero Junction Skids', '2026-12-31', 55500.00, 'Under Review'),
(4, 'CUS-1007', 'PROD-1001', 1, 1450000.00, 'EMP-1010', '2026-09-29 19:25:06', 'QUO-9931', 'Seamless Casing Ultrasonic Flaw Detection Station with Automated Feeder', '2026-11-25', 1450000.00, 'Pending Review'),
(5, 'CUS-1001', 'PROD-1001', 1, 420000.00, 'EMP-1006', '2026-09-29 16:55:23', 'QUO-9088', 'Sensor Automation Modules', '2026-10-29', 420000.00, 'Delivered'),
(6, 'CUS-1001', 'PROD-1001', 1, 420000.00, 'EMP-1006', '2026-09-29 16:59:58', 'QUO-9800', 'Sensor Automation Modules', '2026-10-29', 420000.00, 'Delivered'),
(7, 'CUS-1001', 'PROD-1001', 1, 350000.00, 'EMP-1006', '2026-09-29 17:08:33', 'QUO-9005', 'Telemetry Line Modules', '2026-10-29', 350000.00, 'Delivered'),
(100000, 'CUS-1001', 'PROD-1001', 1, 350000.00, 'EMP-1006', '2026-09-29 17:09:17', 'QUO-9936', 'Telemetry Line Modules', '2026-10-29', 350000.00, 'Delivered');

-- --------------------------------------------------------

--
-- Table structure for table `recertification_windows`
--

CREATE TABLE `recertification_windows` (
  `window_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('Active','Scheduled','Completed') DEFAULT 'Active',
  `scopes` varchar(255) DEFAULT 'ALL',
  `created_by_emp_id` varchar(10) DEFAULT 'EMP-1005',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recertification_windows`
--

INSERT INTO `recertification_windows` (`window_id`, `title`, `start_date`, `end_date`, `status`, `scopes`, `created_by_emp_id`, `created_at`) VALUES
(1, 'Q1-2026 Quarterly Privilege & Entitlement Review', '2026-04-01', '2026-04-30', 'Active', 'ALL', 'EMP-1005', '2026-09-27 16:00:18'),
(2, 'Q1-2026 Emergency Cryptographic Audit Window', '2026-01-15', '2026-02-15', 'Completed', 'SYS-11 GOV-CORE & HSM NODES', 'EMP-1005', '2026-09-27 16:33:28');

-- --------------------------------------------------------

--
-- Table structure for table `recruitment_candidates`
--

CREATE TABLE `recruitment_candidates` (
  `candidate_id` int(11) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `applied_position` varchar(150) DEFAULT NULL,
  `department_code` varchar(4) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Applied',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recruitment_candidates`
--

INSERT INTO `recruitment_candidates` (`candidate_id`, `full_name`, `applied_position`, `department_code`, `status`, `applied_at`) VALUES
(1, 'Almas Berikov', 'Senior Industrial SCADA Engineer', 'ENG', 'InterviewScheduled', '2026-09-15 07:30:00'),
(2, 'Saule Ospanova', 'Metrology Metrologist', 'ENG', 'UnderReview', '2026-09-18 08:15:00'),
(3, 'Dmitry Pavlov', 'Cybersecurity & Governance Auditor', 'ADM', 'Shortlisted', '2026-09-20 13:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `risk_register`
--

CREATE TABLE `risk_register` (
  `risk_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `likelihood` varchar(20) DEFAULT NULL,
  `impact` varchar(20) DEFAULT NULL,
  `owner_emp_id` varchar(10) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Open',
  `review_date` date DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `system_target` varchar(100) DEFAULT 'SYS-01 Production Enclave',
  `threat_vector` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `risk_register`
--

INSERT INTO `risk_register` (`risk_id`, `description`, `likelihood`, `impact`, `owner_emp_id`, `status`, `review_date`, `title`, `system_target`, `threat_vector`) VALUES
(1, 'Critical semiconductor lead time volatility for precision pressure transducers', 'Medium', 'High', 'EMP-1011', 'Mitigated', '2026-10-01', NULL, 'SYS-01 Production Enclave', NULL),
(2, 'Industrial SCADA optical diode firmware integrity attestation', 'Low', 'Critical', 'EMP-1004', 'UnderReview', '2026-10-15', NULL, 'SYS-01 Production Enclave', NULL),
(3, 'Trans-Caspian multimodal freight transit route bottlenecks', 'Medium', 'Medium', 'EMP-1012', 'Mitigated', '2026-11-01', NULL, 'SYS-01 Production Enclave', NULL),
(4, 'Harmonization between Eurasian GOST and European IEC metrology standards', 'Low', 'Medium', 'EMP-1005', 'Accepted', '2026-12-01', NULL, 'SYS-01 Production Enclave', NULL),
(5, 'Cross-Site SCADA Bridge Latency Spike on SYS-01 / SYS-05 Ingestion Link', 'High', 'Critical', 'EMP-1005', 'ActionRequired', '2026-10-20', NULL, 'SYS-01 Production Enclave', NULL),
(6, 'Orphaned Service Account & RSA Key in AS/RS Warehouse System (SYS-07)', 'Medium', 'High', 'EMP-1018', 'ActionRequired', '2026-10-25', NULL, 'SYS-01 Production Enclave', NULL),
(7, 'HSM Cryptographic Root Key Attestation Drift on SYS-02 Key Vault', 'Low', 'Critical', 'EMP-1002', 'UnderReview', '2026-11-05', NULL, 'SYS-01 Production Enclave', NULL),
(8, 'Dual-Custody Governance Quorum Failure Contingency Protocol', 'Low', 'High', 'EMP-1001', 'Accepted', '2026-11-15', NULL, 'SYS-01 Production Enclave', NULL),
(9, 'B2B Procurement API Webhook Buffer Saturation on Trans-Kazakhstan Backbone', 'Medium', 'Medium', 'EMP-1020', 'Mitigated', '2026-12-01', NULL, 'SYS-01 Production Enclave', NULL),
(10, 'Calibration Certificate Expiry on Ekibastuz Power Substation High-Voltage Relays', 'Medium', 'High', 'EMP-1007', 'UnderReview', '2026-12-10', NULL, 'SYS-01 Production Enclave', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'Executive SuperAdmin', 'Full administrative authority and governance oversight across all 11 VOSTOKPRIBOR systems'),
(2, 'Chief Governance Officer', 'Compliance, legal audits, executive risk registries and policy oversight'),
(3, 'Sales Director & Manager', 'CRM pipeline oversight, B2B quotes, enterprise client accounts and order approval'),
(4, 'Senior Automation & Developer', 'Engineering codebase, API developer portal, telemetry and system integrations'),
(5, 'Systems Engineer & IT Support', 'Infrastructure management, IT Helpdesk ticketing, device telemetry, network security'),
(6, 'Chief Financial Officer & Controller', 'Invoices, enterprise billing cycles, audits, and payment records'),
(7, 'HR Director & Operations', 'Personnel records, department assignments, onboarding, payroll compliance'),
(8, 'Logistics & Supply Chain Specialist', 'Warehouse inventory, product catalog, delivery telemetry and procurement'),
(9, 'Customer Client Account', 'Access to Customer Portal, project tracking, ticket creation, B2B purchasing');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(2, 4),
(2, 5),
(2, 10),
(2, 19),
(3, 8),
(3, 9),
(3, 10),
(4, 10),
(4, 13),
(4, 14),
(5, 10),
(5, 15),
(5, 16),
(6, 10),
(6, 17),
(6, 18),
(7, 1),
(7, 2),
(7, 3),
(7, 10),
(7, 11);

-- --------------------------------------------------------

--
-- Table structure for table `role_system_access`
--

CREATE TABLE `role_system_access` (
  `role_id` int(11) NOT NULL,
  `system_id` varchar(4) NOT NULL,
  `access_level` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_system_access`
--

INSERT INTO `role_system_access` (`role_id`, `system_id`, `access_level`) VALUES
(1, 'ADM', 'Full'),
(1, 'CRM', 'Full'),
(1, 'CUS', 'Full'),
(1, 'DEV', 'Full'),
(1, 'DOC', 'Full'),
(1, 'EMP', 'Full'),
(1, 'FIN', 'Full'),
(1, 'HR', 'Full'),
(1, 'IT', 'Full'),
(1, 'SHP', 'Full'),
(1, 'WEB', 'Full'),
(2, 'ADM', 'Full'),
(2, 'DOC', 'Full'),
(2, 'EMP', 'Full'),
(2, 'FIN', 'Audit'),
(2, 'HR', 'Audit'),
(3, 'CRM', 'Full'),
(3, 'CUS', 'Supervise'),
(3, 'DOC', 'ReadWrite'),
(3, 'EMP', 'Read'),
(3, 'SHP', 'Full'),
(4, 'DEV', 'Full'),
(4, 'DOC', 'ReadWrite'),
(4, 'EMP', 'Read'),
(4, 'IT', 'Full'),
(4, 'WEB', 'ReadWrite'),
(5, 'ADM', 'Telemetry'),
(5, 'DEV', 'ReadWrite'),
(5, 'DOC', 'ReadWrite'),
(5, 'EMP', 'Read'),
(5, 'IT', 'Full'),
(6, 'ADM', 'Audit'),
(6, 'CRM', 'Read'),
(6, 'DOC', 'ReadWrite'),
(6, 'EMP', 'Read'),
(6, 'FIN', 'Full'),
(7, 'ADM', 'Read'),
(7, 'DOC', 'ReadWrite'),
(7, 'EMP', 'Full'),
(7, 'HR', 'Full'),
(8, 'DOC', 'ReadWrite'),
(8, 'EMP', 'Read'),
(8, 'FIN', 'Read'),
(8, 'SHP', 'Full'),
(9, 'CUS', 'Full'),
(9, 'SHP', 'Full'),
(9, 'WEB', 'Public');

-- --------------------------------------------------------

--
-- Table structure for table `sales_forecasts`
--

CREATE TABLE `sales_forecasts` (
  `forecast_id` int(11) NOT NULL,
  `sales_emp_id` varchar(10) DEFAULT NULL,
  `period` varchar(20) DEFAULT NULL,
  `forecast_amount` decimal(14,2) DEFAULT NULL,
  `actual_amount` decimal(14,2) DEFAULT NULL,
  `target_quota` decimal(14,2) DEFAULT NULL,
  `weighted_amount` decimal(14,2) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_forecasts`
--

INSERT INTO `sales_forecasts` (`forecast_id`, `sales_emp_id`, `period`, `forecast_amount`, `actual_amount`, `target_quota`, `weighted_amount`, `notes`) VALUES
(1, 'EMP-1006', '2026-Q1', 16200000.00, 16200000.00, 15000000.00, 16200000.00, '108% of Plan (Settled)'),
(2, 'EMP-1007', '2026-Q2', 18100000.00, 18100000.00, 17500000.00, 18100000.00, '102% of Plan (Settled)'),
(3, 'EMP-1006', '2026-Q3', 19750000.00, 19750000.00, 19000000.00, 19750000.00, '104% of Plan (Settled)'),
(4, 'EMP-1007', '2026-Q4', 22500000.00, 18450000.00, 22500000.00, 12800000.00, 'Active Q4 Pipeline (82% Gated)'),
(5, 'EMP-1006', '2027-Q1', 25100000.00, 0.00, 24000000.00, 14500000.00, 'Forward Qualified Backlog'),
(6, 'EMP-1006', '2027-Q1', 25000000.00, 0.00, 22500000.00, 12800000.00, 'Updated via CRM Simulation'),
(7, 'EMP-1006', '2027-Q1', 25000000.00, 0.00, 22500000.00, 12800000.00, 'Updated via CRM Simulation'),
(8, 'EMP-1006', '2027-Q2', 28000000.00, 0.00, 22500000.00, 12800000.00, 'Updated via CRM API'),
(9, 'EMP-1006', '2027-Q2', 28000000.00, 0.00, 22500000.00, 12800000.00, 'Updated via CRM API');

-- --------------------------------------------------------

--
-- Table structure for table `security_events`
--

CREATE TABLE `security_events` (
  `event_id` int(11) NOT NULL,
  `event_type` varchar(50) DEFAULT NULL,
  `source_system` varchar(50) DEFAULT NULL,
  `source_system_id` varchar(4) DEFAULT NULL,
  `source_device_id` int(11) DEFAULT NULL,
  `source_ip_id` int(11) DEFAULT NULL,
  `actor_emp_id` varchar(10) DEFAULT NULL,
  `actor_customer_id` varchar(10) DEFAULT NULL,
  `target_account_id` int(11) DEFAULT NULL,
  `target_device_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `severity` enum('Low','Medium','High','Critical') DEFAULT NULL,
  `event_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `raw_event` text DEFAULT NULL,
  `status` varchar(30) DEFAULT 'New',
  `related_tkt_id` varchar(15) DEFAULT NULL,
  `reported_to_governance` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `security_events`
--

INSERT INTO `security_events` (`event_id`, `event_type`, `source_system`, `source_system_id`, `source_device_id`, `source_ip_id`, `actor_emp_id`, `actor_customer_id`, `target_account_id`, `target_device_id`, `description`, `severity`, `event_time`, `raw_event`, `status`, `related_tkt_id`, `reported_to_governance`) VALUES
(1, 'ORPHAN_IDENTIFIER_DETECTED', 'Industrial SCADA Enclave', 'CUS', NULL, NULL, 'EMP-1009', NULL, NULL, NULL, 'Vendor contract expired 14 days ago. High-privilege RSA SSH-key persists inside CNC SCADA Gateway.', 'Critical', '2026-09-21 08:22:45', '{\"key_fingerprint\":\"SHA256:7mP0w...k9Qx\",\"node\":\"CNC-03\"}', 'Quarantined', NULL, 1),
(2, 'DEFCON_POSTURE_ATTESTATION', 'Gov Core Hub', 'ADM', NULL, NULL, 'EMP-1005', NULL, NULL, NULL, 'Normal operations DEFCON-4 posture attested by Dual-Custody signers (EMP-1001 & EMP-1005).', 'Low', '2026-09-21 06:14:22', '{\"defcon_level\":4,\"posture\":\"NORMAL_OPS\"}', 'Resolved', NULL, 1),
(3, 'BREAK_GLASS_ELEVATION', 'Infrastructure Ops', 'IT', NULL, NULL, 'EMP-1018', NULL, NULL, NULL, 'Emergency telemetry elevation token issued for Almaty Station grid sub-station 04 maintenance.', 'High', '2026-09-21 10:40:10', '{\"authorized_by\":\"EMP-1005\",\"token_id\":\"BG-2026-018\"}', 'Active', NULL, 1),
(4, 'SCADA_INGESTION_LATENCY_JITTER', 'Industrial Bus Relay', 'SHP', NULL, NULL, 'EMP-1002', NULL, NULL, NULL, 'Optical sensor latency jitter exceeded 45ms threshold on Ingestion Bridge 03.', 'Medium', '2026-09-21 09:05:00', '{\"node\":\"INGEST-03\",\"jitter_ms\":52}', 'Investigating', NULL, 1),
(5, 'PROJECT_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created project PRJ-VP-9751 (Auto Test Blast Furnace Profiler)', 'Low', '2026-09-29 22:49:48', NULL, 'New', NULL, 0),
(6, 'PROJECT_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Updated project PRJ-VP-9751', 'Low', '2026-09-29 22:49:48', NULL, 'New', NULL, 0),
(7, 'PROJECT_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted project PRJ-VP-9751', 'Medium', '2026-09-29 22:49:48', NULL, 'New', NULL, 0),
(8, 'INVOICE_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created invoice INV-2026-618', 'Low', '2026-09-29 22:49:48', NULL, 'New', NULL, 0),
(9, 'INVOICE_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Updated invoice INV-2026-618', 'Low', '2026-09-29 22:49:48', NULL, 'New', NULL, 0),
(10, 'INVOICE_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted invoice INV-2026-618', 'Medium', '2026-09-29 22:49:48', NULL, 'New', NULL, 0),
(11, 'TICKET_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created ticket TCK-2026-2308: Sensor Telemetry Calibration Drift Test', 'Low', '2026-09-29 22:49:48', NULL, 'New', NULL, 0),
(12, 'TICKET_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Updated ticket TCK-2026-2308', 'Low', '2026-09-29 22:49:48', NULL, 'New', NULL, 0),
(13, 'TICKET_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted ticket TCK-2026-2308', 'Medium', '2026-09-29 22:49:48', NULL, 'New', NULL, 0),
(14, 'FILE_UPLOADED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Uploaded test_calibration_cert.pdf as DOC-2026-853 (SHA256: e7fb6de90cc6b4dafc94251d2afbe91fe822ea67b3d054e01546f61506a3dccc)', 'Low', '2026-09-29 22:49:48', NULL, 'New', NULL, 0),
(15, 'DOC_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Updated document DOC-2026-853', 'Low', '2026-09-29 22:49:48', NULL, 'New', NULL, 0),
(16, 'DOC_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted document DOC-2026-853', 'Medium', '2026-09-29 22:49:48', NULL, 'New', NULL, 0),
(17, 'PROJECT_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created project PRJ-VP-9321 (Auto Test Blast Furnace Profiler)', 'Low', '2026-09-29 22:50:44', NULL, 'New', NULL, 0),
(18, 'PROJECT_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Updated project PRJ-VP-9321', 'Low', '2026-09-29 22:50:44', NULL, 'New', NULL, 0),
(19, 'PROJECT_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted project PRJ-VP-9321', 'Medium', '2026-09-29 22:50:44', NULL, 'New', NULL, 0),
(20, 'ORDER_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created order #7 for customer CUS-1001', 'Low', '2026-09-29 22:50:44', NULL, 'New', NULL, 0),
(21, 'ORDER_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Updated order #7', 'Low', '2026-09-29 22:50:44', NULL, 'New', NULL, 0),
(22, 'ORDER_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted order #7', 'Medium', '2026-09-29 22:50:44', NULL, 'New', NULL, 0),
(23, 'INVOICE_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created invoice INV-2026-210', 'Low', '2026-09-29 22:50:44', NULL, 'New', NULL, 0),
(24, 'INVOICE_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Updated invoice INV-2026-210', 'Low', '2026-09-29 22:50:44', NULL, 'New', NULL, 0),
(25, 'INVOICE_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted invoice INV-2026-210', 'Medium', '2026-09-29 22:50:44', NULL, 'New', NULL, 0),
(26, 'TICKET_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created ticket TCK-2026-5198: Sensor Telemetry Calibration Drift Test', 'Low', '2026-09-29 22:50:44', NULL, 'New', NULL, 0),
(27, 'TICKET_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Updated ticket TCK-2026-5198', 'Low', '2026-09-29 22:50:44', NULL, 'New', NULL, 0),
(28, 'TICKET_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted ticket TCK-2026-5198', 'Medium', '2026-09-29 22:50:44', NULL, 'New', NULL, 0),
(29, 'FILE_UPLOADED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Uploaded test_calibration_cert.pdf as DOC-2026-392 (SHA256: e7fb6de90cc6b4dafc94251d2afbe91fe822ea67b3d054e01546f61506a3dccc)', 'Low', '2026-09-29 22:50:44', NULL, 'New', NULL, 0),
(30, 'DOC_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Updated document DOC-2026-392', 'Low', '2026-09-29 22:50:44', NULL, 'New', NULL, 0),
(31, 'DOC_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted document DOC-2026-392', 'Medium', '2026-09-29 22:50:44', NULL, 'New', NULL, 0),
(32, 'PROJECT_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created project PRJ-TEST-371 (E2E Automated Pipeline Commissioning Unit)', 'Low', '2026-09-29 23:14:46', NULL, 'New', NULL, 0),
(33, 'PROJECT_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted project PRJ-TEST-371', 'Medium', '2026-09-29 23:14:46', NULL, 'New', NULL, 0),
(34, 'ORDER_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created order #8 for customer CUS-1001', 'Low', '2026-09-29 23:14:46', NULL, 'New', NULL, 0),
(35, 'INVOICE_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created invoice INV-TEST-368', 'Low', '2026-09-29 23:14:46', NULL, 'New', NULL, 0),
(36, 'INVOICE_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Updated invoice INV-TEST-368', 'Low', '2026-09-29 23:14:46', NULL, 'New', NULL, 0),
(37, 'INVOICE_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted invoice INV-TEST-368', 'Medium', '2026-09-29 23:14:46', NULL, 'New', NULL, 0),
(38, 'TICKET_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created ticket TCK-2026-4845: E2E Test Pyrometer Signal Dropout', 'Low', '2026-09-29 23:14:46', NULL, 'New', NULL, 0),
(39, 'TICKET_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted ticket TCK-2026-4845', 'Medium', '2026-09-29 23:14:46', NULL, 'New', NULL, 0),
(40, 'ACCOUNT_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Customer CUS-1001 updated profile details', 'Low', '2026-09-29 23:14:46', NULL, 'New', NULL, 0),
(41, 'PROJECT_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created project PRJ-TEST-631 (E2E Automated Pipeline Commissioning Unit)', 'Low', '2026-09-29 23:15:28', NULL, 'New', NULL, 0),
(42, 'PROJECT_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Updated project PRJ-TEST-631', 'Low', '2026-09-29 23:15:28', NULL, 'New', NULL, 0),
(43, 'PROJECT_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted project PRJ-TEST-631', 'Medium', '2026-09-29 23:15:28', NULL, 'New', NULL, 0),
(44, 'ORDER_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created order #9 for customer CUS-1001', 'Low', '2026-09-29 23:15:28', NULL, 'New', NULL, 0),
(45, 'ORDER_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Updated order #9', 'Low', '2026-09-29 23:15:28', NULL, 'New', NULL, 0),
(46, 'ORDER_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted order #9', 'Medium', '2026-09-29 23:15:28', NULL, 'New', NULL, 0),
(47, 'INVOICE_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created invoice INV-TEST-328', 'Low', '2026-09-29 23:15:29', NULL, 'New', NULL, 0),
(48, 'INVOICE_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Updated invoice INV-TEST-328', 'Low', '2026-09-29 23:15:29', NULL, 'New', NULL, 0),
(49, 'INVOICE_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted invoice INV-TEST-328', 'Medium', '2026-09-29 23:15:29', NULL, 'New', NULL, 0),
(50, 'TICKET_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created ticket TCK-2026-7126: E2E Test Pyrometer Signal Dropout', 'Low', '2026-09-29 23:15:29', NULL, 'New', NULL, 0),
(51, 'TICKET_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted ticket TCK-2026-7126', 'Medium', '2026-09-29 23:15:29', NULL, 'New', NULL, 0),
(52, 'ACCOUNT_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Customer CUS-1001 updated profile details', 'Low', '2026-09-29 23:15:29', NULL, 'New', NULL, 0),
(53, 'PROJECT_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created project PRJ-TEST-263 (E2E Automated Pipeline Commissioning Unit)', 'Low', '2026-09-29 23:16:03', NULL, 'New', NULL, 0),
(54, 'PROJECT_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Updated project PRJ-TEST-263', 'Low', '2026-09-29 23:16:03', NULL, 'New', NULL, 0),
(55, 'PROJECT_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted project PRJ-TEST-263', 'Medium', '2026-09-29 23:16:03', NULL, 'New', NULL, 0),
(56, 'ORDER_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created order #10 for customer CUS-1001', 'Low', '2026-09-29 23:16:03', NULL, 'New', NULL, 0),
(57, 'ORDER_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Updated order #10', 'Low', '2026-09-29 23:16:03', NULL, 'New', NULL, 0),
(58, 'ORDER_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted order #10', 'Medium', '2026-09-29 23:16:03', NULL, 'New', NULL, 0),
(59, 'INVOICE_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created invoice INV-TEST-130', 'Low', '2026-09-29 23:16:03', NULL, 'New', NULL, 0),
(60, 'INVOICE_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Updated invoice INV-TEST-130', 'Low', '2026-09-29 23:16:03', NULL, 'New', NULL, 0),
(61, 'INVOICE_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted invoice INV-TEST-130', 'Medium', '2026-09-29 23:16:03', NULL, 'New', NULL, 0),
(62, 'TICKET_CREATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Created ticket TCK-2026-8953: E2E Test Pyrometer Signal Dropout', 'Low', '2026-09-29 23:16:03', NULL, 'New', NULL, 0),
(63, 'TICKET_DELETED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Deleted ticket TCK-2026-8953', 'Medium', '2026-09-29 23:16:03', NULL, 'New', NULL, 0),
(64, 'ACCOUNT_UPDATED', NULL, 'CUS', NULL, NULL, NULL, 'CUS-1001', NULL, NULL, 'Customer CUS-1001 updated profile details', 'Low', '2026-09-29 23:16:03', NULL, 'New', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `security_incidents`
--

CREATE TABLE `security_incidents` (
  `incident_id` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `severity` enum('Low','Medium','High','Critical') DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Open',
  `reported_by_emp_id` varchar(10) DEFAULT NULL,
  `assigned_to_emp_id` varchar(10) DEFAULT NULL,
  `opened_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `closed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `security_incidents`
--

INSERT INTO `security_incidents` (`incident_id`, `title`, `description`, `severity`, `status`, `reported_by_emp_id`, `assigned_to_emp_id`, `opened_at`, `closed_at`) VALUES
(1, 'SCADA CNC Gateway Token Leak & Orphan Account (EMP-1009)', 'Vendor contract expired 14 days ago. High-privilege RSA SSH-key persisted inside CNC SCADA Gateway. Remediation desk token purge triggered.', 'Critical', 'Contained', 'EMP-1005', 'EMP-1004', '2026-09-20 11:15:00', '2026-09-23 12:46:47'),
(2, 'Ingestion Node 07 Shymkent Buffer Desync', 'Packet retransmission spikes on optical sensor telemetry queue between Shymkent Depot and Central Databus.', 'Medium', 'Resolved', 'EMP-1007', 'EMP-1018', '2026-09-18 06:30:00', '2026-09-18 08:45:00'),
(3, 'Emergency Break-Glass Audit: Sub-Station 04 Power Grid', 'Scheduled high-voltage optical relay calibration requiring temporary L5 telemetry elevation.', 'High', 'Closed', 'EMP-1001', 'EMP-1005', '2026-09-14 23:00:00', '2026-09-15 00:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `security_policies`
--

CREATE TABLE `security_policies` (
  `policy_id` int(11) NOT NULL,
  `doc_id` varchar(15) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `severity` varchar(20) DEFAULT 'High',
  `enforcement_mode` varchar(30) DEFAULT 'MANDATORY',
  `description` text DEFAULT NULL,
  `system_id` varchar(50) DEFAULT 'SYS-01..11'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `security_policies`
--

INSERT INTO `security_policies` (`policy_id`, `doc_id`, `title`, `effective_date`, `severity`, `enforcement_mode`, `description`, `system_id`) VALUES
(1, 'DOC-2026-005', 'Cryptographic Key Rotation & HSM Policy (FIPS 140-3)', '2026-01-01', 'Critical', 'MANDATORY', 'Enforces 90-day HSM cryptographic key rotation and FIPS 140-3 boundary checks.', 'SYS-01..11'),
(2, 'DOC-2026-006', 'Air-Gapped SCADA Network Isolation & Telemetry Standards', '2026-02-15', 'Critical', 'HARD-BLOCK', 'Mandatory physical air-gap for industrial telemetry and PLC actuation relays.', 'SYS-03,08'),
(3, 'DOC-2026-012', 'Zero-Trust Multi-Factor Authentication & Identity Lifecycle', '2026-03-01', 'High', 'MANDATORY', 'Hardware token MFA enforcement across Tier-0 and Tier-1 access boundaries.', 'SYS-11'),
(4, 'DOC-2026-013', 'Executive Break-Glass Emergency Authorization Protocol', '2026-01-10', 'High', 'AUDIT-LOG', 'Dual-custody authorization requirements for root break-glass events.', 'SYS-01..11'),
(5, 'DOC-2026-001', 'Statutory Data Sovereignty & Audit Retention Framework', '2026-01-01', 'High', 'MANDATORY', 'Kazakhstan sovereign data residency and 5-year cryptographic audit retention.', 'SYS-01..11'),
(6, 'DOC-2026-006', 'Defcon Enclave Galvanic Isolation Interlock', '2026-02-20', 'Critical', 'ENFORCED', 'Physical galvanic disconnection protocols for substation and furnace actuators during industrial emergency states.', 'SYS-05'),
(7, 'DOC-2026-007', 'Statutory FIPS 140-3 Hardware Key Attestation', '2026-03-01', 'High', 'ACTIVE', 'All privileged administrative console interactions require compliant physical HSM tokens with biometric pin challenge.', 'SYS-10'),
(8, 'DOC-2026-008', 'SCADA Network Unidirectional Diode Boundary Verification', '2026-03-05', 'Critical', 'ACTIVE', 'Physical hardware data diodes must ensure strictly one-way data egress from operational safety rings.', 'SYS-08'),
(9, 'DOC-2026-009', 'Continuous Merkle Root Proof Sovereign State Uplink', '2026-03-10', 'Medium', 'ACTIVE', 'Periodic anchoring of tamper-evident Merkle hash roots with KZ-CERT national cybersecurity registry.', 'SYS-11'),
(10, 'DOC-2026-010', 'Privileged Keystroke & Shadow Session Forensic Mirroring', '2026-03-15', 'Medium', 'ACTIVE', 'Full session recording and optical OCR audit capture for all Tier 0/1 interactive shells across bastions.', 'SYS-10');

-- --------------------------------------------------------

--
-- Table structure for table `sla_policies`
--

CREATE TABLE `sla_policies` (
  `sla_id` int(11) NOT NULL,
  `priority` enum('Low','Medium','High','Critical') DEFAULT NULL,
  `response_time_hours` int(11) DEFAULT NULL,
  `resolution_time_hours` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `first_response_time_minutes` int(11) DEFAULT NULL,
  `resolution_time_minutes` int(11) DEFAULT NULL,
  `escalation_threshold_minutes` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sla_policies`
--

INSERT INTO `sla_policies` (`sla_id`, `priority`, `response_time_hours`, `resolution_time_hours`, `description`, `first_response_time_minutes`, `resolution_time_minutes`, `escalation_threshold_minutes`) VALUES
(1, 'Low', 12, 24, 'Updated Low priority policy test', 720, 1440, 360),
(2, 'Medium', 4, 8, 'General infrastructure inquiries and batch integration delays', 240, 480, 120),
(3, 'High', 2, 4, 'Degraded hardware throughput or optical pyrometer miscalibration', 120, 240, 60),
(4, 'Critical', 1, 2, 'Emergency operational shutdown, SCADA rupture or security breach', 60, 120, 30);

-- --------------------------------------------------------

--
-- Table structure for table `systems_catalog`
--

CREATE TABLE `systems_catalog` (
  `system_id` varchar(4) NOT NULL,
  `system_name` varchar(100) DEFAULT NULL,
  `fqdn` varchar(100) DEFAULT NULL,
  `criticality` varchar(30) DEFAULT NULL,
  `trust_zone` varchar(30) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'OPERATIONAL',
  `isolation_reason` varchar(255) DEFAULT NULL,
  `isolated_at` datetime DEFAULT NULL,
  `isolated_by` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `systems_catalog`
--

INSERT INTO `systems_catalog` (`system_id`, `system_name`, `fqdn`, `criticality`, `trust_zone`, `status`, `isolation_reason`, `isolated_at`, `isolated_by`) VALUES
('ADM', 'Admin & Governance Portal', 'admin.vostokpribor.local', 'MissionCritical', 'Zone-Alpha', 'OPERATIONAL', 'Operational Link Re-Established', NULL, NULL),
('CRM', 'CRM System', 'crm.vostokpribor.local', 'High', 'Zone-Bravo', 'OPERATIONAL', 'Operational Link Re-Established', NULL, NULL),
('CUS', 'Customer Portal', 'customer.vostokpribor.local', 'High', 'Zone-External', 'OPERATIONAL', NULL, NULL, NULL),
('DEV', 'Developer Portal', 'developer.vostokpribor.local', 'High', 'Zone-Bravo', 'OPERATIONAL', NULL, NULL, NULL),
('DOC', 'File Center', 'files.vostokpribor.local', 'High', 'Zone-Bravo', 'OPERATIONAL', NULL, NULL, NULL),
('EMP', 'Employee Intranet', 'intranet.vostokpribor.local', 'Medium', 'Zone-Internal', 'OPERATIONAL', NULL, NULL, NULL),
('FIN', 'Finance & Billing', 'finance.vostokpribor.local', 'MissionCritical', 'Zone-Alpha', 'OPERATIONAL', NULL, NULL, NULL),
('HR', 'HR System', 'hr.vostokpribor.local', 'High', 'Zone-Bravo', 'OPERATIONAL', NULL, NULL, NULL),
('IT', 'IT Helpdesk', 'helpdesk.vostokpribor.local', 'Medium', 'Zone-Internal', 'OPERATIONAL', NULL, NULL, NULL),
('SHP', 'Online Shop B2B', 'shop.vostokpribor.local', 'High', 'Zone-External', 'OPERATIONAL', NULL, NULL, NULL),
('SYS-', 'Governance Core, PKI Root & Audit Vault', NULL, NULL, NULL, 'OPERATIONAL', NULL, NULL, NULL),
('WEB', 'Corporate Web Platform', 'vostokpribor.local', 'Public', 'Zone-DMZ', 'OPERATIONAL', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `system_integrations`
--

CREATE TABLE `system_integrations` (
  `integration_id` int(11) NOT NULL,
  `link_code` varchar(20) NOT NULL,
  `source_system_id` varchar(10) NOT NULL,
  `target_system_id` varchar(10) NOT NULL,
  `api_protocol` varchar(100) NOT NULL,
  `authentication_method` varchar(100) NOT NULL,
  `data_exchanged` text NOT NULL,
  `direction` varchar(30) NOT NULL DEFAULT 'Outbound',
  `required_clearance` enum('L1','L2','L3','L4') NOT NULL DEFAULT 'L2',
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_integrations`
--

INSERT INTO `system_integrations` (`integration_id`, `link_code`, `source_system_id`, `target_system_id`, `api_protocol`, `authentication_method`, `data_exchanged`, `direction`, `required_clearance`, `status`, `created_at`) VALUES
(1, 'SYS01_TO_SYS02', 'ADM', 'CRM', 'REST / JSON HTTPS', 'Mutual HMAC-SHA256 & L4 SuperAdmin', 'Executive Governance Policies, Audit Compliance Flags, Enterprise Client Oversight Directives', 'Outbound (SYS01 ??? SYS02)', 'L3', 'Active', '2026-09-23 12:46:33'),
(2, 'SYS02_TO_SYS03', 'CRM', 'CUS', 'REST / JSON HTTPS', 'SSO Session Token & API Key', 'Customer Accounts, SLA Tiers, Billing Terms, Project Milestones & Contracts', 'Outbound (SYS02 ??? SYS03)', 'L2', 'Active', '2026-09-23 12:46:33'),
(3, 'SYS02_TO_SYS05', 'CRM', 'EMP', 'REST Event Bus / PubSub', 'SSO Session Token', 'Sales Performance Metrics, Department Target Announcements, Major Client Deal Wins', 'Outbound (SYS02 ??? SYS05)', 'L2', 'Active', '2026-09-23 12:46:33'),
(4, 'SYS03_TO_SYS05', 'CUS', 'EMP', 'REST Webhook / JSON', 'Customer Auth Token & SSO', 'Customer Support Escalations, Feedback Inquiries, Internal Department Dispatch', 'Outbound (SYS03 ??? SYS05)', 'L1', 'Active', '2026-09-23 12:46:33'),
(5, 'SYS05_TO_SYS06', 'EMP', 'DOC', 'Document Ingestion REST API', 'SSO Session Token', 'Internal Corporate Policies, Employee Form Submissions, Compliance Manuals, Archived Memos', 'Outbound (SYS05 ??? SYS06)', 'L1', 'Active', '2026-09-23 12:46:33'),
(6, 'SYS05_TO_SYS07', 'EMP', 'FIN', 'REST JSON RPC', 'Clearance Gated Session (L2+)', 'Employee Expense Claims, Departmental Budget Requisitions, Operational Travel Invoices', 'Outbound (SYS05 ??? SYS07)', 'L2', 'Active', '2026-09-23 12:46:33'),
(7, 'SYS04_TO_SYS06', 'DEV', 'DOC', 'OpenAPI Auto-Sync REST', 'Bearer API Key', 'API Technical Specs, SDK Documentation, Industrial Telemetry Architecture Schematics', 'Outbound (SYS04 ??? SYS06)', 'L2', 'Active', '2026-09-23 12:46:33'),
(8, 'SYS04_TO_SYS08', 'DEV', 'HR', 'REST JSON Webhook', 'SecOps HMAC Token', 'Technical Skills Assessment, Developer Candidate Profiles, Engineering Protocol Certifications', 'Outbound (SYS04 ??? SYS08)', 'L3', 'Active', '2026-09-23 12:46:33'),
(9, 'SYS04_TO_SYS09', 'DEV', 'IT', 'Syslog / REST Webhook', 'Bearer API Token', 'Telemetry Alerts, Infrastructure Error Logs, Continuous Deployment Incident Tickets', 'Outbound (SYS04 ??? SYS09)', 'L2', 'Active', '2026-09-23 12:46:33'),
(10, 'SYS10_TO_SYS02', 'SHP', 'CRM', 'REST / HTTPS JSON', 'Storefront API Token', 'High-Value Purchase Leads, Commercial Accounts, Customer RFQ Inquiries', 'Outbound (SYS10 ??? SYS02)', 'L2', 'Active', '2026-09-23 12:46:33'),
(11, 'SYS10_TO_SYS05', 'SHP', 'EMP', 'REST Event Bus / Webhook', 'System SSO Token', 'Warehouse Inventory Depletion Warnings, High-Priority B2B Order Notifications', 'Outbound (SYS10 ??? SYS05)', 'L2', 'Active', '2026-09-23 12:46:33'),
(12, 'SYS11_TO_ALL', 'WEB', 'ALL', 'Universal Gateway Router', 'Universal SSO Cookie & Public Token', 'Public Visitor Contact Inquiries, Press Releases, SSO Launchpad Cross-System Routing', 'Broadcast (SYS11 ??? ALL)', 'L1', 'Active', '2026-09-23 12:46:33');

-- --------------------------------------------------------

--
-- Table structure for table `system_integration_logs`
--

CREATE TABLE `system_integration_logs` (
  `log_id` bigint(20) NOT NULL,
  `link_code` varchar(20) NOT NULL,
  `source_system_id` varchar(10) NOT NULL,
  `target_system_id` varchar(10) NOT NULL,
  `api_protocol` varchar(100) NOT NULL,
  `endpoint` varchar(255) NOT NULL,
  `payload_summary` text DEFAULT NULL,
  `direction` varchar(30) NOT NULL,
  `status_code` int(11) NOT NULL DEFAULT 200,
  `actor_id` varchar(50) DEFAULT 'SYSTEM',
  `executed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_integration_logs`
--

INSERT INTO `system_integration_logs` (`log_id`, `link_code`, `source_system_id`, `target_system_id`, `api_protocol`, `endpoint`, `payload_summary`, `direction`, `status_code`, `actor_id`, `executed_at`) VALUES
(1, 'SYS01_TO_SYS02', 'ADM', 'CRM', 'REST / JSON HTTPS', '/api/integrations/SYS01_TO_SYS02/dispatch', 'Governance directive dispatched from SYS01 (ADM) to SYS02 (CRM). Action: TEST_SYNC | Payload: {\"test_mode\":true,\"system_check\":\"OK\"}', 'Outbound (SYS01 ?????? SYS02)', 200, 'admin@gmail.com', '2026-09-21 15:07:57'),
(2, 'SYS01_TO_SYS02', 'ADM', 'CRM', 'REST / JSON HTTPS', '/api/integrations/SYS01_TO_SYS02/dispatch', 'Governance directive dispatched from SYS01 (ADM) to SYS02 (CRM). Action: UNIT_TEST_DISPATCH', 'Outbound (SYS01 ?????? SYS02)', 200, 'admin@gmail.com', '2026-09-21 15:08:13'),
(3, 'SYS02_TO_SYS03', 'CRM', 'CUS', 'REST / JSON HTTPS', '/api/integrations/SYS02_TO_SYS03/dispatch', 'Customer account and SLA profile synchronized from SYS02 (CRM) to SYS03 (CUS). Action: UNIT_TEST_DISPATCH', 'Outbound (SYS02 ?????? SYS03)', 200, 'admin@gmail.com', '2026-09-21 15:08:14'),
(4, 'SYS02_TO_SYS05', 'CRM', 'EMP', 'REST Event Bus / PubSub', '/api/integrations/SYS02_TO_SYS05/dispatch', 'Sales benchmark notification published from SYS02 (CRM) to SYS05 (EMP Intranet). Action: UNIT_TEST_DISPATCH', 'Outbound (SYS02 ?????? SYS05)', 200, 'admin@gmail.com', '2026-09-21 15:08:14'),
(5, 'SYS03_TO_SYS05', 'CUS', 'EMP', 'REST Webhook / JSON', '/api/integrations/SYS03_TO_SYS05/dispatch', 'Customer support escalation routed from SYS03 (CUS) to SYS05 (EMP Intranet). Action: UNIT_TEST_DISPATCH', 'Outbound (SYS03 ?????? SYS05)', 200, 'admin@gmail.com', '2026-09-21 15:08:14'),
(6, 'SYS05_TO_SYS06', 'EMP', 'DOC', 'Document Ingestion REST API', '/api/integrations/SYS05_TO_SYS06/dispatch', 'Internal corporate document ingested from SYS05 (EMP) to SYS06 (DOC File Center). Action: UNIT_TEST_DISPATCH', 'Outbound (SYS05 ?????? SYS06)', 200, 'admin@gmail.com', '2026-09-21 15:08:14'),
(7, 'SYS05_TO_SYS07', 'EMP', 'FIN', 'REST JSON RPC', '/api/integrations/SYS05_TO_SYS07/dispatch', 'Expense claim & requisition dispatched from SYS05 (EMP) to SYS07 (FIN Finance & Billing). Action: UNIT_TEST_DISPATCH', 'Outbound (SYS05 ?????? SYS07)', 200, 'admin@gmail.com', '2026-09-21 15:08:14'),
(8, 'SYS04_TO_SYS06', 'DEV', 'DOC', 'OpenAPI Auto-Sync REST', '/api/integrations/SYS04_TO_SYS06/dispatch', 'Technical OpenAPI specification published from SYS04 (DEV) to SYS06 (DOC). Action: UNIT_TEST_DISPATCH', 'Outbound (SYS04 ?????? SYS06)', 200, 'admin@gmail.com', '2026-09-21 15:08:14'),
(9, 'SYS04_TO_SYS08', 'DEV', 'HR', 'REST JSON Webhook', '/api/integrations/SYS04_TO_SYS08/dispatch', 'Engineering candidate technical score submitted from SYS04 (DEV) to SYS08 (HR). Action: UNIT_TEST_DISPATCH', 'Outbound (SYS04 ?????? SYS08)', 200, 'admin@gmail.com', '2026-09-21 15:08:14'),
(10, 'SYS04_TO_SYS09', 'DEV', 'IT', 'Syslog / REST Webhook', '/api/integrations/SYS04_TO_SYS09/dispatch', 'Automated telemetry incident ticket generated from SYS04 (DEV) to SYS09 (IT Helpdesk). Action: UNIT_TEST_DISPATCH', 'Outbound (SYS04 ?????? SYS09)', 200, 'admin@gmail.com', '2026-09-21 15:08:14'),
(11, 'SYS10_TO_SYS02', 'SHP', 'CRM', 'REST / HTTPS JSON', '/api/integrations/SYS10_TO_SYS02/dispatch', 'B2B commercial wholesale RFQ transmitted from SYS10 (SHP) to SYS02 (CRM). Action: UNIT_TEST_DISPATCH', 'Outbound (SYS10 ?????? SYS02)', 200, 'admin@gmail.com', '2026-09-21 15:08:14'),
(12, 'SYS10_TO_SYS05', 'SHP', 'EMP', 'REST Event Bus / Webhook', '/api/integrations/SYS10_TO_SYS05/dispatch', 'Inventory threshold alert broadcast from SYS10 (SHP) to SYS05 (EMP Intranet). Action: UNIT_TEST_DISPATCH', 'Outbound (SYS10 ?????? SYS05)', 200, 'admin@gmail.com', '2026-09-21 15:08:14'),
(13, 'SYS11_TO_ALL', 'WEB', 'ALL', 'Universal Gateway Router', '/api/integrations/SYS11_TO_ALL/dispatch', 'Universal Corporate Platform announcement broadcast from SYS11 (WEB) to ALL subsystems (SYS01-SYS10). Action: UNIT_TEST_DISPATCH', 'Broadcast (SYS11 ?????? ALL)', 200, 'admin@gmail.com', '2026-09-21 15:08:14'),
(14, 'INT-SHP-FIN-01', 'SHP', 'FIN', 'REST / JSON HTTPS', '/api/invoices/create', 'Store System automatically generated invoice INV-2026-011 for Order #99999 value $4850', 'OUTBOUND', 201, 'CUS-1001', '2026-09-30 00:32:16'),
(15, 'INT-FIN-DOC-01', 'FIN', 'DOC', 'REST / JSON HTTPS', '/api/documents/ingest', 'Invoice file Invoice-INV-2026-011-Order-99999.html ingested into File Center vault as DOC-2026-016 (SHA256: 160134c3f922…)', 'INBOUND', 201, 'FIN-SYSTEM', '2026-09-30 00:32:16'),
(16, 'INT-CUS-HR-01', 'CUS', 'HR', 'REST / JSON HTTPS', '/api/service-requests/submit', 'Customer CUS-1001 submitted service request SRV-2026-004 (Calibration & Metrology)', 'DISPATCH', 201, 'CUS-1001', '2026-09-30 00:32:16'),
(17, 'INT-SHP-FIN-01', 'SHP', 'FIN', 'REST / JSON HTTPS', '/api/invoices/create', 'Store System automatically generated invoice INV-2026-011 for Order #99999 value $4850', 'OUTBOUND', 201, 'CUS-1001', '2026-09-30 00:34:13'),
(18, 'INT-FIN-DOC-01', 'FIN', 'DOC', 'REST / JSON HTTPS', '/api/documents/ingest', 'Invoice file Invoice-INV-2026-011-Order-99999.html ingested into File Center vault as DOC-2026-016 (SHA256: 09728563b61d…)', 'INBOUND', 201, 'FIN-SYSTEM', '2026-09-30 00:34:13'),
(19, 'INT-HR-ADM-01', 'HR', 'ADM', 'REST / JSON HTTPS', '/api/managers/appoint', 'Super-Administrator (admin@gmail.com) appointed Bekzod Rakhimov (EMP-1008) as Executive Co-Director with equal L4 Executive clearance.', 'INTERNAL', 200, 'EMP-0001', '2026-09-30 00:34:14'),
(20, 'INT-CUS-HR-01', 'CUS', 'HR', 'REST / JSON HTTPS', '/api/service-requests/submit', 'Customer CUS-1001 submitted service request SRV-2026-005 (Calibration & Metrology)', 'DISPATCH', 201, 'CUS-1001', '2026-09-30 00:34:14'),
(21, 'INT-HR-CUS-01', 'HR', 'CUS', 'REST / JSON HTTPS', '/api/service-requests/assign', 'Assigned Elena Morozova (EMP-1004) to customer service request SRV-2026-005', 'DISPATCH', 200, 'HR-DISPATCH', '2026-09-30 00:34:14'),
(22, 'INT-HR-CUS-02', 'HR', 'CUS', 'REST / JSON HTTPS', '/api/careers/post', 'HR System published new job opening: Test Senior Instrumentation Specialist (ENG)', 'BROADCAST', 201, 'HR-OPERATIONS', '2026-09-30 00:34:14');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `tkt_id` varchar(15) NOT NULL,
  `requester_type` enum('Customer','Employee') NOT NULL,
  `requester_cus_id` varchar(10) DEFAULT NULL,
  `requester_emp_id` varchar(10) DEFAULT NULL,
  `source_system` varchar(50) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `requester_name` varchar(150) DEFAULT NULL,
  `requester_role` varchar(150) DEFAULT NULL,
  `requester_dept` varchar(100) DEFAULT NULL,
  `priority` enum('Low','Medium','High','Critical') NOT NULL,
  `assigned_emp_id` varchar(10) DEFAULT NULL,
  `status` enum('Open','InProgress','Investigating','Escalated','Resolved') NOT NULL,
  `resolution_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL,
  `sla_deadline` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`tkt_id`, `requester_type`, `requester_cus_id`, `requester_emp_id`, `source_system`, `title`, `description`, `requester_name`, `requester_role`, `requester_dept`, `priority`, `assigned_emp_id`, `status`, `resolution_notes`, `created_at`, `resolved_at`, `sla_deadline`) VALUES
('TCK-9340', 'Customer', 'CUS-1001', NULL, 'Hydraulic Pressure Array HA-12', 'Hydraulic Pressure Sensor Pulse Noise Filter Parameter Optimization', 'Noise filter dampening coefficient adjusted from 0.45 to 0.82. Pressure pulse spikes suppressed to within 0.5% tolerance.', 'Alexey R. Danilov', 'Chief Instrumentation Eng.', NULL, 'Low', 'EMP-1001', 'Resolved', NULL, '2026-09-17 20:09:03', NULL, NULL),
('TCK-9399', 'Customer', 'CUS-1001', NULL, 'Laser Profiler CCM-3', 'Modbus TCP Gateway Time Sync Drift (NTP Desync)', 'Annual preventive alignment check for CCM-3 laser profiler. Optical cleaning completed, calibration verified with standard block.', 'Alexey R. Danilov', 'Chief Instrumentation Eng.', NULL, 'Low', 'EMP-1003', 'Resolved', NULL, '2026-09-24 20:09:03', NULL, NULL),
('TCK-9460', 'Customer', 'CUS-1001', NULL, 'Optical Pyrometers (VP-OP-402)', 'Calibration Certificate Request for Batch VP-900-Q3', 'Ahead of the scheduled 1,400°C heat run at 14:00, pyrometer OP-402 on Strand #2 showed a slight +2.4°C baseline zero-drift during cold-reference check. Requesting remote verification of emissivity coefficient tables.', 'Alexey R. Danilov', 'Chief Instrumentation Eng.', NULL, 'Medium', 'EMP-1002', 'Open', NULL, '2026-09-28 20:09:03', NULL, NULL),
('TCK-9482', 'Customer', 'CUS-1001', NULL, 'SCADA Telemetry Gateway VP-GW-09', 'Sensor Bank #2 Analog Loop Dropout during high-temp cycle', 'At 10:11 AM during the planned furnace temperature ramp-up to 1,350°C, sensor channel GC-02 on Skid #4 stopped transmitting 4-20mA signals to the main SCADA hub. Error code 0x7E (Analog Loop Open). We have switched to secondary backup, but need immediate root-cause diagnostic.', 'Alexey R. Danilov', 'Chief Instrumentation Eng.', NULL, 'Critical', 'EMP-1001', 'InProgress', NULL, '2026-09-29 18:09:03', NULL, NULL),
('TICK-8819', 'Employee', NULL, NULL, 'SCADA Modbus Gateway #3', 'SCADA Modbus Gateway #3 Packet Drop', 'Telemetry frame drop on RS-485 bus #3 connecting high-temp pyrometer array. Packet loss exceeding 14.8% during hot blast cycle.', 'Dr. Elena Rostova', 'Chief Optical Calibration Architect', 'Laser Pyrometry', 'Critical', 'EMP-1018', 'InProgress', NULL, '2026-09-27 05:30:14', NULL, '2026-09-27 14:18:27'),
('TICK-8820', 'Employee', NULL, NULL, 'Cleanroom Biometric Scanner Bay B', 'Cleanroom Airlock RFID Interlock Rejecting Level 3 Badges', 'Class 4 Cleanroom airlock interlock rejecting authenticated Level 3 smartcard RFID credentials.', 'Dr. Mikhail Abramov', 'Principal Semiconductor Physicist', 'Nanofabrication Facility Bay B', 'Critical', 'EMP-1018', 'InProgress', NULL, '2026-09-27 06:12:00', NULL, '2026-09-27 13:24:27'),
('TICK-8821', 'Employee', NULL, NULL, 'FAT Calibration Server Bay #2', 'Laser Triangulation Calibration Server Matrix Overflow', 'Automated calibration routine crashing on 64-bit floating point matrix overflow during high-speed profile tests.', 'Viktor Morozov', 'Lead SCADA Gateway Specialist', 'FAT Acceptance Testing Bay #2', 'High', 'EMP-1004', 'Open', NULL, '2026-09-27 07:05:00', NULL, '2026-09-27 14:51:27'),
('TICK-8822', 'Employee', NULL, NULL, 'PKI & Security Token Infrastructure', 'ISO 9001 PKI Smartcard Token Renewal Required', 'Cryptographic smartcard PKI token renewal required for electronic FAT test report signing.', 'Anna Belova', 'Head of Quality Assurance', 'QA Certification', 'Medium', 'EMP-1002', 'InProgress', NULL, '2026-09-26 13:40:00', NULL, '2026-09-27 18:06:27'),
('TICK-8823', 'Employee', NULL, NULL, 'ERP Procurement Module', 'ERP Procurement Delegation Authority During Annual Leave', 'Request for secondary approval delegation during scheduled annual factory shutdown.', 'Svetlana Petrova', 'Strategic Component Buyer', 'Procurement & Logistics', 'Low', 'EMP-1002', 'Open', NULL, '2026-09-26 11:15:00', NULL, '2026-09-28 07:21:27'),
('TKT-2026-001', 'Customer', 'CUS-1002', NULL, 'Customer Portal', 'Customer Portal B2B API Latency Degraded', 'High packet loss observed on external B2B partner gateway during order sync.', 'TechTrans Logistics', 'External Partner', 'External Logistics', 'High', 'EMP-1018', 'InProgress', NULL, '2026-09-21 16:36:10', '0000-00-00 00:00:00', NULL),
('TKT-2026-002', 'Employee', NULL, 'EMP-1007', 'CRM', 'CRM Contact Synchronization Timeout on Russian Railways Sync', 'Automated nightly sync job failed with connection timeout after 60s.', 'Dmitry Volkov', 'Sales Director', 'Sales & Marketing', 'Medium', 'EMP-1018', 'Resolved', NULL, '2026-09-21 16:36:10', '0000-00-00 00:00:00', NULL),
('TKT-2026-003', 'Customer', 'CUS-1004', NULL, 'E-Commerce', 'B2B Shop Checkout Cart Lock during Batch Invoicing', 'Database row lock on shop orders table during high concurrency checkout.', 'Industrial Alloys Corp', 'Procurement Officer', 'External Partner', 'High', 'EMP-1018', 'Investigating', NULL, '2026-09-21 16:36:10', '0000-00-00 00:00:00', NULL),
('TKT-2026-004', 'Employee', NULL, 'EMP-1015', 'Intranet', 'Corporate Intranet Knowledge Base Search Index Rebuild Required', 'Search query Elasticsearch cluster indexing lag causing missing runbooks.', 'Marina Kuznetsova', 'HR Specialist', 'Human Resources', 'Low', 'EMP-1018', 'Resolved', NULL, '2026-09-21 16:36:10', '0000-00-00 00:00:00', NULL),
('TKT-2026-005', 'Customer', 'CUS-1007', NULL, 'Customer Portal', 'Customer Portal TLS Handshake Error on Gateway 02', 'Certbot auto-renewal renewal chain mismatch on second reverse proxy.', 'Ural Mining Metallurgical', 'Systems Lead', 'External Partner', 'Critical', 'EMP-1018', 'Escalated', NULL, '2026-09-21 16:36:10', '0000-00-00 00:00:00', NULL),
('TKT-2026-006', 'Employee', NULL, 'EMP-1020', 'Developer Portal', 'API Gateway OAuth2 Token Revocation Endpoint Intermittent 502', 'Redis session token cache cluster high memory pressure triggered failover.', 'Alexey Smirnov', 'DevOps Engineer', 'Engineering', 'Medium', 'EMP-1018', 'Resolved', NULL, '2026-09-21 16:36:10', '0000-00-00 00:00:00', NULL),
('TKT-2026-007', 'Customer', 'CUS-1005', NULL, 'E-Commerce', 'ERP 1C Billing Data Reconciliation Discrepancy Q1', 'Ledger balances mismatch between export XML and internal relational store.', 'Tatiana Romanova', 'Senior Accountant', 'Finance & Accounting', 'Medium', 'EMP-1018', 'Resolved', NULL, '2026-09-21 16:36:10', '0000-00-00 00:00:00', NULL),
('TKT-2026-008', 'Employee', NULL, 'EMP-1016', 'File Center', 'Shop Catalog Price Cache Invalidation Delay', 'Updated catalog pricing not reflecting on frontend storefront within SLA window.', 'Pavel Sidorov', 'Procurement Specialist', 'Procurement', 'Medium', 'EMP-1018', 'InProgress', NULL, '2026-09-21 16:36:10', '0000-00-00 00:00:00', NULL),
('TKT-2026-009', 'Customer', 'CUS-1001', NULL, 'Customer Portal', 'Single Sign-On Session Timeout Too Short on Mobile Intranet', 'Engineers on manufacturing floor prompted for PIN re-entry every 5 minutes.', 'Olga Ivanova', 'Floor Supervisor', 'Operations', 'Low', 'EMP-1018', 'Resolved', NULL, '2026-09-21 16:36:10', '0000-00-00 00:00:00', NULL),
('TKT-2026-010', 'Employee', NULL, 'EMP-1017', 'Developer Portal', 'Lead Routing Rule Failover for Export Contracts', 'Export trade lead inquiries being incorrectly routed to domestic queue.', 'Sergey Popov', 'Commercial Director', 'Sales & Marketing', 'High', 'EMP-1018', 'Investigating', NULL, '2026-09-21 16:36:10', '0000-00-00 00:00:00', NULL),
('TKT-2026-011', 'Customer', 'CUS-1008', NULL, 'Customer Portal', 'Admin Portal Audit Log Archive Retention Rule Execution', 'Monthly cold storage archive job stalled at 88% due to disk quota.', 'Nikolay Vasilyev', 'Security Officer', 'Security & Governance', 'High', 'EMP-1018', 'Escalated', NULL, '2026-09-21 16:36:10', '0000-00-00 00:00:00', NULL),
('TKT-2026-012', 'Employee', NULL, 'EMP-1013', 'Intranet', 'Backup Storage Appliance LUN Snapshot Pool Warning (82% full)', 'Automated weekly block-level snapshot pool nearing warning threshold.', 'Andrey Fedorov', 'Storage Admin', 'IT Infrastructure', 'Medium', 'EMP-1018', 'Resolved', NULL, '2026-09-21 16:36:10', '0000-00-00 00:00:00', NULL),
('TKT-2026-013', 'Customer', 'CUS-1009', NULL, 'E-Commerce', 'Employee Onboarding Workflow Automated Provisioning Stuck', 'LDAP provisioner queue failed to generate home directory for new chemist.', 'Elena Vlasova', 'HR Operations', 'Human Resources', 'Low', 'EMP-1018', 'Resolved', NULL, '2026-09-21 16:36:10', '0000-00-00 00:00:00', NULL),
('TKT-2026-014', 'Employee', NULL, 'EMP-1019', 'File Center', 'Invoice PDF Generator Font Rendering Exception', 'Cyrillic glyph missing in Headless Chrome PDF generation microservice.', 'Altai Precision Tools', 'B2B Client Representative', 'External Partner', 'Medium', 'EMP-1018', 'InProgress', NULL, '2026-09-21 16:36:10', '0000-00-00 00:00:00', NULL),
('TKT-2026-015', 'Customer', 'CUS-1010', NULL, 'Customer Portal', 'Customer Order Status Tracking Webhook Delivery Failure', 'HTTP 429 rate limit exceeded from severstal webhook receiver endpoint.', 'Severstal Components', 'Supply Chain Tech', 'External Partner', 'High', 'EMP-1018', 'Investigating', NULL, '2026-09-21 16:36:10', '0000-00-00 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ticket_comments`
--

CREATE TABLE `ticket_comments` (
  `comment_id` int(11) NOT NULL,
  `tkt_id` varchar(15) NOT NULL,
  `author_emp_id` varchar(10) DEFAULT NULL,
  `author_name` varchar(100) DEFAULT NULL,
  `author_role` varchar(100) DEFAULT NULL,
  `author_type` enum('tech','requester','system') NOT NULL DEFAULT 'tech',
  `comment_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_comments`
--

INSERT INTO `ticket_comments` (`comment_id`, `tkt_id`, `author_emp_id`, `author_name`, `author_role`, `author_type`, `comment_text`, `created_at`) VALUES
(1, 'TKT-2026-001', 'EMP-1018', NULL, NULL, 'tech', 'Channel GC-02 dropped out again during test cycle #4. Telemetry logs attached.', '2026-09-18 07:00:00'),
(2, 'TKT-2026-001', 'EMP-1002', NULL, NULL, 'tech', 'Investigating optical isolator feedback loop. Remote diagnostics session scheduled for 14:00.', '2026-09-18 08:30:00'),
(3, 'TKT-2026-002', 'EMP-1018', NULL, NULL, 'tech', 'Substation relay power supply swapped; load test completed successfully.', '2026-09-19 12:45:00'),
(4, 'TICK-8819', NULL, 'Dr. Elena Rostova', 'Chief Optical Calibration Architect', 'requester', 'Alexey, we are seeing recurrent timeout errors from Pyrometer Node #4B during the 1,450°C cycle. The Modbus gateway is responding with 0x0B (Gateway Target Device Failed to Respond) error codes. We cannot certify the morning optical batch until the bus latency drops below 20ms.', '2026-09-27 05:30:14'),
(5, 'TICK-8819', 'EMP-1018', 'Alexey Ivanov', 'Lead IT Tech · Tier 3', 'tech', 'Understood Dr. Rostova. I checked the telemetry trace on Switch SW-LIP-03 port Eth12. We are observing high CRC error rates caused by electromagnetic interference from Induction Coil #2 during the peak heating cycle.\nDiagnostic: RS485_BUS_3_CRC_ERR = 1,420 pkts/min [ABNORMAL]', '2026-09-27 05:42:00'),
(6, 'TICK-8819', NULL, 'Dr. Elena Rostova', 'Chief Optical Calibration Architect', 'requester', 'Can you switch the primary polling channel to the redundant optical isolator link (Channel B2) on the secondary DIN-rail multiplexer?', '2026-09-27 05:50:00'),
(7, 'TICK-8819', 'EMP-1018', 'Alexey Ivanov', 'Lead IT Tech · Tier 3', 'tech', 'Executing failover to Channel B2 right now. I have set the baud rate to 115200 with parity even. Awaiting test pulse verification from your calibration rig.', '2026-09-27 06:05:00'),
(8, 'TICK-8819', 'EMP-1001', 'Dr. Elena Rostova', 'Chief Optical Calibration Architect', 'requester', 'Initial telemetry diagnostic shows CRC errors spiking every 4.2 seconds on Moxa MB3170 RS-485 bus #3. Induction furnace EMI suspected.', '2026-09-27 14:16:26'),
(9, 'TICK-8819', 'EMP-1018', 'Alexey Ivanov', 'Lead IT Tech ┬À Tier 3', 'tech', 'Acknowledged. Oscilloscope attached to Channel B. Investigating shield ground potential differences between cleanroom floor and furnace transformer vault.', '2026-09-27 15:01:26'),
(10, 'TICK-8819', 'EMP-1018', 'Alexey Ivanov', 'Lead IT Tech ┬À Tier 3', 'tech', 'Ground loop identified: 34V AC ripple on copper shield. Switched pyrometer bus lines to optically isolated transceiver module. Retesting frame drop rate now.', '2026-09-27 15:31:26'),
(11, 'TICK-8819', 'EMP-1001', 'Dr. Elena Rostova', 'Chief Optical Calibration Architect', 'requester', 'Initial telemetry diagnostic shows CRC errors spiking every 4.2 seconds on Moxa MB3170 RS-485 bus #3. Induction furnace EMI suspected.', '2026-09-27 14:16:48'),
(12, 'TICK-8819', 'EMP-1018', 'Alexey Ivanov', 'Lead IT Tech ┬À Tier 3', 'tech', 'Acknowledged. Oscilloscope attached to Channel B. Investigating shield ground potential differences between cleanroom floor and furnace transformer vault.', '2026-09-27 15:01:48'),
(13, 'TICK-8819', 'EMP-1018', 'Alexey Ivanov', 'Lead IT Tech ┬À Tier 3', 'tech', 'Ground loop identified: 34V AC ripple on copper shield. Switched pyrometer bus lines to optically isolated transceiver module. Retesting frame drop rate now.', '2026-09-27 15:31:48'),
(14, 'TICK-8819', 'EMP-1001', 'Dr. Elena Rostova', 'Chief Optical Calibration Architect', 'requester', 'Initial telemetry diagnostic shows CRC errors spiking every 4.2 seconds on Moxa MB3170 RS-485 bus #3. Induction furnace EMI suspected.', '2026-09-27 14:17:20'),
(15, 'TICK-8819', 'EMP-1018', 'Alexey Ivanov', 'Lead IT Tech ┬À Tier 3', 'tech', 'Acknowledged. Oscilloscope attached to Channel B. Investigating shield ground potential differences between cleanroom floor and furnace transformer vault.', '2026-09-27 15:02:20'),
(16, 'TICK-8819', 'EMP-1018', 'Alexey Ivanov', 'Lead IT Tech ┬À Tier 3', 'tech', 'Ground loop identified: 34V AC ripple on copper shield. Switched pyrometer bus lines to optically isolated transceiver module. Retesting frame drop rate now.', '2026-09-27 15:32:20'),
(17, 'TICK-8819', 'EMP-1001', 'Dr. Elena Rostova', 'Chief Optical Calibration Architect', 'requester', 'Initial telemetry diagnostic shows CRC errors spiking every 4.2 seconds on Moxa MB3170 RS-485 bus #3. Induction furnace EMI suspected.', '2026-09-27 14:21:00'),
(18, 'TICK-8819', 'EMP-1018', 'Alexey Ivanov', 'Lead IT Tech · Tier 3', 'tech', 'Acknowledged. Oscilloscope attached to Channel B. Investigating shield ground potential differences between cleanroom floor and furnace transformer vault.', '2026-09-27 15:06:00'),
(19, 'TICK-8819', 'EMP-1018', 'Alexey Ivanov', 'Lead IT Tech · Tier 3', 'tech', 'Ground loop identified: 34V AC ripple on copper shield. Switched pyrometer bus lines to optically isolated transceiver module. Retesting frame drop rate now.', '2026-09-27 15:36:00'),
(23, 'TCK-9482', NULL, 'Alexey R. Danilov', 'Client - Chief Instrumentation Eng.', 'requester', 'At 10:11 AM during the planned furnace temperature ramp-up to 1,350°C, sensor channel GC-02 on Skid #4 stopped transmitting 4-20mA signals to the main SCADA hub. Error code 0x7E (Analog Loop Open). We have switched to secondary backup, but need immediate root-cause diagnostic.', '2026-09-29 18:19:03'),
(24, 'TCK-9482', NULL, 'System Automated Dispatch', 'Automated Dispatch Node', 'system', 'Automated Incident Classification: Severity 1 (Critical Production Impact). SLA countdown initiated (15 min SLA window). Assigned to Industrial Automation Escalation Pool. Dispatched high-priority SMS alert to On-call Specialist: Denis Sokolov.', '2026-09-29 18:24:03'),
(25, 'TCK-9482', NULL, 'Denis Sokolov', 'Tier-3 Automation Engineer', 'tech', 'Alexey, good morning. I have reviewed the telemetry dump. The loop resistance spiked to infinity indicating either an open terminal at junction box JB-104 or a thermal overload on the galvanic isolator card. Our on-site field tech Boris K. has been dispatched to Blast Furnace #5 control room with a replacement isolator module. In the meantime, I am running a remote diagnostic script on Gateway VP-GW-09.', '2026-09-29 18:39:03'),
(26, 'TCK-9482', NULL, 'Denis Sokolov', 'Tier-3 Automation Engineer', 'tech', 'Update: Remote handshake re-established with the transmitter microcontroller. Boris is now at the cabinet verifying the terminal screws and power rails. Stand by for live calibration test.', '2026-09-29 19:09:03');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_escalations`
--

CREATE TABLE `ticket_escalations` (
  `escalation_id` int(11) NOT NULL,
  `tkt_id` varchar(15) NOT NULL,
  `escalated_to_emp_id` varchar(10) DEFAULT NULL,
  `escalated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reason` text DEFAULT NULL,
  `status` enum('Pending','Acknowledged','Resolved','Escalated') NOT NULL DEFAULT 'Escalated'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_escalations`
--

INSERT INTO `ticket_escalations` (`escalation_id`, `tkt_id`, `escalated_to_emp_id`, `escalated_at`, `reason`, `status`) VALUES
(1, 'TKT-2026-001', 'EMP-1002', '2026-09-18 06:30:00', 'Optical packet loss exceeded 5% threshold requiring Lead Automation Engineer review.', 'Escalated'),
(2, 'TKT-2026-003', 'EMP-1005', '2026-09-20 11:00:00', 'Privileged token anomaly requiring Governance Officer dual-custody review.', 'Escalated'),
(3, 'TICK-8819', 'EMP-1005', '2026-09-27 15:46:48', 'Telemetry instability on blast furnace #5 exceeding standard 30-minute threshold', 'Escalated'),
(4, 'TICK-8819', 'EMP-1005', '2026-09-27 15:47:20', 'Telemetry instability on blast furnace #5 exceeding standard 30-minute threshold', 'Escalated'),
(5, 'TICK-8819', 'EMP-1005', '2026-09-27 15:51:00', 'Telemetry instability on blast furnace #5 exceeding standard 30-minute threshold', 'Escalated');

-- --------------------------------------------------------

--
-- Table structure for table `training_records`
--

CREATE TABLE `training_records` (
  `training_id` int(11) NOT NULL,
  `emp_id` varchar(10) NOT NULL,
  `training_name` varchar(150) DEFAULT NULL,
  `completed_at` date DEFAULT NULL,
  `certificate_doc_id` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `training_records`
--

INSERT INTO `training_records` (`training_id`, `emp_id`, `training_name`, `completed_at`, `certificate_doc_id`) VALUES
(2, 'EMP-1016', 'SCADA Level 4 Security Protocol Compliance', '2026-06-15', NULL),
(3, 'EMP-1017', 'Industrial IoT Cryptography & Data Pipelines', '2026-07-20', NULL),
(4, 'EMP-1018', 'Cyber Defense & Perimeter Intrusion Prevention', '2026-08-10', NULL),
(5, 'EMP-1008', 'Enterprise B2B Deal Structuring & Negotiation', '2026-05-12', NULL),
(6, 'EMP-1011', 'Supply Chain Redundancy & ISO 9001 Audits', '2026-04-18', NULL),
(7, 'EMP-1007', 'Strategic Key Account Management', '2026-03-22', NULL),
(8, 'EMP-1013', 'Global Optical Equipment Procurement Standards', '2026-02-14', NULL),
(9, 'EMP-1021', 'Full stack developer', '2026-09-28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `session_id` bigint(20) NOT NULL,
  `account_type` varchar(20) NOT NULL,
  `employee_account_id` int(11) DEFAULT NULL,
  `customer_account_id` int(11) DEFAULT NULL,
  `system_id` varchar(4) DEFAULT NULL,
  `device_id` int(11) DEFAULT NULL,
  `ip_id` int(11) DEFAULT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ended_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`session_id`, `account_type`, `employee_account_id`, `customer_account_id`, `system_id`, `device_id`, `ip_id`, `started_at`, `ended_at`, `status`) VALUES
(1, 'Employee', 53, NULL, 'HR', NULL, NULL, '2026-09-21 14:32:28', '2026-09-21 22:32:28', 'Active'),
(2, 'Employee', 53, NULL, 'HR', NULL, NULL, '2026-09-21 14:36:16', '2026-09-21 22:36:16', 'Terminated'),
(3, 'Employee', 53, NULL, 'DOC', NULL, NULL, '2026-09-24 15:11:24', '2026-09-24 23:11:24', 'Active'),
(4, 'Employee', 53, NULL, 'DOC', NULL, NULL, '2026-09-24 15:11:39', '2026-09-24 23:11:39', 'Active'),
(5, 'Employee', 53, NULL, 'FIN', NULL, NULL, '2026-09-24 15:12:00', '2026-09-24 23:12:00', 'Active'),
(6, 'Employee', 53, NULL, 'FIN', NULL, NULL, '2026-09-24 15:13:52', '2026-09-24 23:13:52', 'Active'),
(7, 'Employee', 53, NULL, 'IT', NULL, NULL, '2026-09-27 17:01:06', '2026-09-28 01:01:06', 'Active'),
(8, 'Employee', 53, NULL, 'IT', NULL, NULL, '2026-09-28 14:24:51', '2026-09-28 22:24:51', 'Active'),
(9, 'Employee', 53, NULL, 'IT', NULL, NULL, '2026-09-28 14:26:06', '2026-09-28 22:26:06', 'Active'),
(10, 'Employee', 53, NULL, 'SHP', NULL, NULL, '2026-09-28 15:29:07', '2026-09-28 23:29:07', 'Active'),
(11, 'Employee', 53, NULL, 'CUS', NULL, NULL, '2026-09-28 15:29:43', '2026-09-28 23:29:43', 'Active'),
(12, 'Employee', 53, NULL, 'HR', NULL, NULL, '2026-09-28 15:31:18', '2026-09-28 23:31:18', 'Active'),
(13, 'Employee', 68, NULL, 'FIN', NULL, NULL, '2026-09-28 15:38:49', '2026-09-28 23:38:49', 'Active'),
(14, 'Employee', 53, NULL, 'HR', NULL, NULL, '2026-09-28 16:14:48', '2026-09-29 00:14:48', 'Active'),
(15, 'Employee', 53, NULL, 'ADM', NULL, NULL, '2026-09-28 16:28:16', '2026-09-29 00:28:16', 'Active'),
(16, 'Employee', 53, NULL, 'SHP', NULL, NULL, '2026-09-28 17:08:03', '2026-09-29 01:08:03', 'Active'),
(17, 'Employee', 53, NULL, 'SHP', NULL, NULL, '2026-09-28 17:08:21', '2026-09-29 01:08:21', 'Active'),
(18, 'Employee', 53, NULL, 'ADM', NULL, NULL, '2026-09-28 18:39:48', '2026-09-29 02:39:48', 'Active'),
(19, 'Employee', 53, NULL, 'DOC', NULL, NULL, '2026-09-28 19:57:18', '2026-09-29 03:57:18', 'Active'),
(20, 'Employee', 53, NULL, 'DEV', NULL, NULL, '2026-09-28 19:59:33', '2026-09-29 03:59:33', 'Active'),
(21, 'Employee', 53, NULL, 'HR', NULL, NULL, '2026-09-28 20:09:16', '2026-09-29 04:09:16', 'Active'),
(22, 'Employee', 53, NULL, 'FIN', NULL, NULL, '2026-09-28 20:16:26', '2026-09-29 04:16:26', 'Active'),
(23, 'Employee', 53, NULL, 'IT', NULL, NULL, '2026-09-28 20:17:28', '2026-09-29 04:17:28', 'Active'),
(24, 'Employee', 53, NULL, 'DOC', NULL, NULL, '2026-09-28 20:18:39', '2026-09-29 04:18:39', 'Active'),
(25, 'Employee', 53, NULL, 'ADM', NULL, NULL, '2026-09-28 20:20:03', '2026-09-29 04:20:03', 'Active'),
(26, 'Employee', 53, NULL, 'FIN', NULL, NULL, '2026-09-28 20:20:21', '2026-09-29 04:20:21', 'Active'),
(27, 'Employee', 53, NULL, 'DEV', NULL, NULL, '2026-09-28 20:22:30', '2026-09-29 04:22:30', 'Active'),
(28, 'Employee', 53, NULL, 'SHP', NULL, NULL, '2026-09-28 20:51:15', '2026-09-29 04:51:15', 'Active'),
(29, 'Employee', 53, NULL, 'CUS', NULL, NULL, '2026-09-28 20:51:31', '2026-09-29 04:51:31', 'Active'),
(30, 'Employee', 53, NULL, 'HR', NULL, NULL, '2026-09-28 20:51:56', '2026-09-29 04:51:56', 'Active'),
(31, 'Employee', 53, NULL, 'DOC', NULL, NULL, '2026-09-28 20:52:57', '2026-09-29 04:52:57', 'Active'),
(32, 'Employee', 53, NULL, 'CRM', NULL, NULL, '2026-09-28 20:53:19', '2026-09-29 04:53:19', 'Active'),
(33, 'Employee', 17, NULL, 'CRM', NULL, NULL, '2026-09-29 16:57:58', '2026-09-30 00:57:58', 'Active'),
(34, 'Employee', 17, NULL, 'CRM', NULL, NULL, '2026-09-29 17:00:01', '2026-09-30 01:00:01', 'Active'),
(35, 'Employee', 17, NULL, 'CRM', NULL, NULL, '2026-09-29 17:08:45', '2026-09-30 01:08:45', 'Active'),
(36, 'Employee', 53, NULL, 'CRM', NULL, NULL, '2026-09-29 17:10:21', '2026-09-30 01:10:21', 'Active'),
(37, 'Employee', 17, NULL, 'CRM', NULL, NULL, '2026-09-29 17:49:58', '2026-09-30 01:49:58', 'Active'),
(38, 'Employee', 53, NULL, 'EMP', NULL, NULL, '2026-09-29 17:53:51', '2026-09-30 01:53:51', 'Active'),
(39, 'Employee', 1, NULL, 'EMP', NULL, NULL, '2026-09-29 18:28:28', '2026-09-30 02:28:28', 'Active'),
(40, 'Customer', NULL, 2, 'CUS', NULL, NULL, '2026-09-29 22:42:37', '2026-09-30 06:42:37', 'Active'),
(41, 'Customer', NULL, 2, 'CUS', NULL, NULL, '2026-09-29 23:15:28', '2026-09-30 07:15:28', 'Active'),
(42, 'Customer', NULL, 2, 'CUS', NULL, NULL, '2026-09-29 23:16:03', '2026-09-30 07:16:03', 'Active'),
(43, 'Employee', 53, NULL, 'CUS', NULL, NULL, '2026-09-29 23:27:25', '2026-09-30 07:27:25', 'Active'),
(44, 'Customer', NULL, 2, 'SHP', NULL, NULL, '2026-09-29 23:38:10', '2026-09-30 07:38:10', 'Active'),
(45, 'Customer', NULL, 2, 'SHP', NULL, NULL, '2026-09-29 23:38:34', '2026-09-30 07:38:34', 'Active'),
(46, 'Employee', 53, NULL, 'SHP', NULL, NULL, '2026-09-29 23:39:54', '2026-09-30 07:39:54', 'Active'),
(47, 'Customer', NULL, 2, 'SHP', NULL, NULL, '2026-09-29 23:49:40', '2026-09-30 07:49:40', 'Active'),
(48, 'Employee', 53, NULL, 'SHP', NULL, NULL, '2026-09-29 23:51:34', '2026-09-30 07:51:34', 'Active'),
(49, 'Customer', NULL, 2, 'SHP', NULL, NULL, '2026-09-29 23:58:50', '2026-09-30 07:58:50', 'Active'),
(50, 'Customer', NULL, 2, 'SHP', NULL, NULL, '2026-09-30 00:03:37', '2026-09-30 08:03:37', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_reviews`
--
ALTER TABLE `access_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `fk_access_reviews_emp_id` (`emp_id`),
  ADD KEY `fk_access_reviews_reviewed_by_emp_id` (`reviewed_by_emp_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `fk_announcements_posted_by_emp_id` (`posted_by_emp_id`),
  ADD KEY `fk_announcements_audience_dept` (`audience_dept`);

--
-- Indexes for table `api_access_logs`
--
ALTER TABLE `api_access_logs`
  ADD PRIMARY KEY (`api_log_id`),
  ADD KEY `fk_api_access_logs_credential_id` (`credential_id`),
  ADD KEY `fk_api_access_logs_partner_id` (`partner_id`),
  ADD KEY `fk_api_access_logs_system_id` (`system_id`);

--
-- Indexes for table `api_credentials`
--
ALTER TABLE `api_credentials`
  ADD PRIMARY KEY (`credential_id`),
  ADD KEY `fk_api_credentials_partner_id` (`partner_id`);

--
-- Indexes for table `api_partners`
--
ALTER TABLE `api_partners`
  ADD PRIMARY KEY (`partner_id`),
  ADD KEY `fk_api_partners_cus_id` (`cus_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `fk_audit_logs_actor_emp_id` (`actor_emp_id`),
  ADD KEY `fk_audit_logs_actor_customer_id` (`actor_customer_id`),
  ADD KEY `fk_audit_logs_system_id` (`system_id`),
  ADD KEY `fk_audit_logs_device_id` (`device_id`);

--
-- Indexes for table `authentication_events`
--
ALTER TABLE `authentication_events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `fk_authentication_events_employee_account_id` (`employee_account_id`),
  ADD KEY `fk_authentication_events_customer_account_id` (`customer_account_id`),
  ADD KEY `fk_authentication_events_system_id` (`system_id`),
  ADD KEY `fk_authentication_events_device_id` (`device_id`),
  ADD KEY `fk_authentication_events_ip_id` (`ip_id`);

--
-- Indexes for table `billing_cycles`
--
ALTER TABLE `billing_cycles`
  ADD PRIMARY KEY (`cycle_id`),
  ADD KEY `fk_billing_cycles_prj_id` (`prj_id`);

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`budget_id`),
  ADD KEY `fk_budgets_department_code` (`department_code`);

--
-- Indexes for table `compliance_controls`
--
ALTER TABLE `compliance_controls`
  ADD PRIMARY KEY (`control_id`),
  ADD UNIQUE KEY `control_code` (`control_code`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`contact_id`),
  ADD KEY `fk_contacts_cus_id` (`cus_id`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`contract_id`),
  ADD KEY `fk_contracts_cus_id` (`cus_id`),
  ADD KEY `fk_contracts_prj_id` (`prj_id`),
  ADD KEY `fk_contracts_opp_id` (`opp_id`),
  ADD KEY `fk_contracts_doc_id` (`doc_id`);

--
-- Indexes for table `crm_activities`
--
ALTER TABLE `crm_activities`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `idx_crm_act_cus` (`cus_id`),
  ADD KEY `idx_crm_act_created` (`created_at`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`cus_id`),
  ADD KEY `fk_customers_account_manager_emp_id` (`account_manager_emp_id`);

--
-- Indexes for table `customer_accounts`
--
ALTER TABLE `customer_accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `idx_cus_account_username` (`username`),
  ADD KEY `idx_cus_id` (`cus_id`);

--
-- Indexes for table `customer_pricing`
--
ALTER TABLE `customer_pricing`
  ADD PRIMARY KEY (`cus_id`,`prod_id`),
  ADD KEY `fk_customer_pricing_prod_id` (`prod_id`);

--
-- Indexes for table `customer_service_requests`
--
ALTER TABLE `customer_service_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `idx_csr_cus_id` (`cus_id`),
  ADD KEY `idx_csr_assigned_emp` (`assigned_emp_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`dept_code`);

--
-- Indexes for table `department_boards`
--
ALTER TABLE `department_boards`
  ADD PRIMARY KEY (`board_post_id`),
  ADD KEY `fk_department_boards_department_code` (`department_code`),
  ADD KEY `fk_department_boards_posted_by_emp_id` (`posted_by_emp_id`);

--
-- Indexes for table `developer_api_keys`
--
ALTER TABLE `developer_api_keys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_identifier` (`key_identifier`);

--
-- Indexes for table `developer_endpoints`
--
ALTER TABLE `developer_endpoints`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `endpoint_slug` (`endpoint_slug`);

--
-- Indexes for table `developer_guides`
--
ALTER TABLE `developer_guides`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `guide_code` (`guide_code`);

--
-- Indexes for table `developer_partner_applications`
--
ALTER TABLE `developer_partner_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_id` (`ticket_id`);

--
-- Indexes for table `developer_sandbox_logs`
--
ALTER TABLE `developer_sandbox_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `developer_sandbox_presets`
--
ALTER TABLE `developer_sandbox_presets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `developer_webhooks`
--
ALTER TABLE `developer_webhooks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `delivery_id` (`delivery_id`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`device_id`),
  ADD KEY `fk_devices_department_code` (`department_code`),
  ADD KEY `fk_devices_assigned_emp_id` (`assigned_emp_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `fk_documents_owner_emp_id` (`owner_emp_id`),
  ADD KEY `fk_documents_related_prj_id` (`related_prj_id`),
  ADD KEY `fk_documents_related_cus_id` (`related_cus_id`);

--
-- Indexes for table `document_access_log`
--
ALTER TABLE `document_access_log`
  ADD PRIMARY KEY (`access_id`),
  ADD KEY `fk_document_access_log_doc_id` (`doc_id`),
  ADD KEY `fk_document_access_log_accessed_by_emp_id` (`accessed_by_emp_id`),
  ADD KEY `fk_document_access_log_accessed_by_cus_id` (`accessed_by_cus_id`),
  ADD KEY `fk_document_access_log_system_id` (`system_id`),
  ADD KEY `fk_document_access_log_device_id` (`device_id`);

--
-- Indexes for table `document_approvals`
--
ALTER TABLE `document_approvals`
  ADD PRIMARY KEY (`approval_id`),
  ADD KEY `fk_document_approvals_doc_id` (`doc_id`),
  ADD KEY `fk_document_approvals_reviewer_emp_id` (`reviewer_emp_id`);

--
-- Indexes for table `document_retention_policies`
--
ALTER TABLE `document_retention_policies`
  ADD PRIMARY KEY (`policy_id`);

--
-- Indexes for table `document_versions`
--
ALTER TABLE `document_versions`
  ADD PRIMARY KEY (`version_id`),
  ADD KEY `fk_document_versions_doc_id` (`doc_id`),
  ADD KEY `fk_document_versions_uploaded_by_emp_id` (`uploaded_by_emp_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`emp_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_employees_department_code` (`department_code`),
  ADD KEY `fk_employees_manager_emp_id` (`manager_emp_id`);

--
-- Indexes for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `idx_emp_account_username` (`username`),
  ADD KEY `idx_emp_id` (`emp_id`);

--
-- Indexes for table `employee_offboarding`
--
ALTER TABLE `employee_offboarding`
  ADD PRIMARY KEY (`offboarding_id`),
  ADD KEY `fk_employee_offboarding_emp_id` (`emp_id`);

--
-- Indexes for table `employee_onboarding`
--
ALTER TABLE `employee_onboarding`
  ADD PRIMARY KEY (`onboarding_id`),
  ADD KEY `fk_employee_onboarding_emp_id` (`emp_id`);

--
-- Indexes for table `employee_roles`
--
ALTER TABLE `employee_roles`
  ADD PRIMARY KEY (`emp_id`,`role_id`),
  ADD KEY `fk_employee_roles_role_id` (`role_id`),
  ADD KEY `fk_employee_roles_granted_by_emp_id` (`granted_by_emp_id`);

--
-- Indexes for table `financial_reports`
--
ALTER TABLE `financial_reports`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `integration_logs`
--
ALTER TABLE `integration_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_integration_logs_partner_id` (`partner_id`);

--
-- Indexes for table `integration_requirements`
--
ALTER TABLE `integration_requirements`
  ADD PRIMARY KEY (`req_id`),
  ADD KEY `fk_integration_requirements_cus_id` (`cus_id`),
  ADD KEY `fk_integration_requirements_prj_id` (`prj_id`),
  ADD KEY `fk_integration_requirements_reviewed_by_emp_id` (`reviewed_by_emp_id`);

--
-- Indexes for table `internal_policies`
--
ALTER TABLE `internal_policies`
  ADD PRIMARY KEY (`policy_id`),
  ADD KEY `fk_internal_policies_doc_id` (`doc_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`inv_id`),
  ADD KEY `fk_invoices_cus_id` (`cus_id`),
  ADD KEY `fk_invoices_prj_id` (`prj_id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `fk_invoice_items_inv` (`inv_id`);

--
-- Indexes for table `ip_addresses`
--
ALTER TABLE `ip_addresses`
  ADD PRIMARY KEY (`ip_id`),
  ADD KEY `fk_ip_addresses_device_id` (`device_id`);

--
-- Indexes for table `it_assets`
--
ALTER TABLE `it_assets`
  ADD PRIMARY KEY (`asset_id`),
  ADD UNIQUE KEY `serial_number` (`serial_number`),
  ADD KEY `fk_it_assets_emp_id` (`emp_id`),
  ADD KEY `fk_it_assets_department_code` (`department_code`),
  ADD KEY `fk_it_assets_system_id` (`system_id`);

--
-- Indexes for table `job_postings`
--
ALTER TABLE `job_postings`
  ADD PRIMARY KEY (`posting_id`),
  ADD KEY `fk_job_postings_department_code` (`department_code`);

--
-- Indexes for table `knowledge_base_articles`
--
ALTER TABLE `knowledge_base_articles`
  ADD PRIMARY KEY (`kb_id`),
  ADD KEY `fk_knowledge_base_articles_created_by_emp_id` (`created_by_emp_id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`lead_id`),
  ADD KEY `fk_leads_assigned_sales_emp_id` (`assigned_sales_emp_id`),
  ADD KEY `fk_leads_converted_cus_id` (`converted_cus_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`leave_id`),
  ADD KEY `fk_leave_requests_emp_id` (`emp_id`),
  ADD KEY `fk_leave_requests_approved_by_emp_id` (`approved_by_emp_id`);

--
-- Indexes for table `opportunities`
--
ALTER TABLE `opportunities`
  ADD PRIMARY KEY (`opp_id`),
  ADD KEY `fk_opportunities_lead_id` (`lead_id`),
  ADD KEY `fk_opportunities_cus_id` (`cus_id`),
  ADD KEY `fk_opportunities_sales_emp_id` (`sales_emp_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_orders_cus_id` (`cus_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `fk_order_items_order_id` (`order_id`),
  ADD KEY `fk_order_items_prod_id` (`prod_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_payments_inv_id` (`inv_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`);

--
-- Indexes for table `portal_accounts`
--
ALTER TABLE `portal_accounts`
  ADD PRIMARY KEY (`portal_user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_portal_accounts_cus_id` (`cus_id`);

--
-- Indexes for table `portal_notifications`
--
ALTER TABLE `portal_notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `fk_portal_notifications_portal_user_id` (`portal_user_id`);

--
-- Indexes for table `portal_sessions`
--
ALTER TABLE `portal_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `fk_portal_sessions_portal_user_id` (`portal_user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`prod_id`);

--
-- Indexes for table `product_inventory`
--
ALTER TABLE `product_inventory`
  ADD PRIMARY KEY (`prod_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`prj_id`),
  ADD KEY `fk_projects_cus_id` (`cus_id`),
  ADD KEY `fk_projects_project_manager_emp_id` (`project_manager_emp_id`);

--
-- Indexes for table `quotes`
--
ALTER TABLE `quotes`
  ADD PRIMARY KEY (`quote_id`),
  ADD KEY `fk_quotes_cus_id` (`cus_id`),
  ADD KEY `fk_quotes_prod_id` (`prod_id`),
  ADD KEY `fk_quotes_created_by_emp_id` (`created_by_emp_id`);

--
-- Indexes for table `recertification_windows`
--
ALTER TABLE `recertification_windows`
  ADD PRIMARY KEY (`window_id`);

--
-- Indexes for table `recruitment_candidates`
--
ALTER TABLE `recruitment_candidates`
  ADD PRIMARY KEY (`candidate_id`),
  ADD KEY `fk_recruitment_candidates_department_code` (`department_code`);

--
-- Indexes for table `risk_register`
--
ALTER TABLE `risk_register`
  ADD PRIMARY KEY (`risk_id`),
  ADD KEY `fk_risk_register_owner_emp_id` (`owner_emp_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `fk_role_permissions_permission_id` (`permission_id`);

--
-- Indexes for table `role_system_access`
--
ALTER TABLE `role_system_access`
  ADD PRIMARY KEY (`role_id`,`system_id`),
  ADD KEY `fk_role_system_access_system_id` (`system_id`);

--
-- Indexes for table `sales_forecasts`
--
ALTER TABLE `sales_forecasts`
  ADD PRIMARY KEY (`forecast_id`),
  ADD KEY `fk_sales_forecasts_sales_emp_id` (`sales_emp_id`);

--
-- Indexes for table `security_events`
--
ALTER TABLE `security_events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `fk_security_events_source_system_id` (`source_system_id`),
  ADD KEY `fk_security_events_source_device_id` (`source_device_id`),
  ADD KEY `fk_security_events_source_ip_id` (`source_ip_id`),
  ADD KEY `fk_security_events_actor_emp_id` (`actor_emp_id`),
  ADD KEY `fk_security_events_actor_customer_id` (`actor_customer_id`),
  ADD KEY `fk_security_events_target_device_id` (`target_device_id`),
  ADD KEY `fk_security_events_related_tkt_id` (`related_tkt_id`);

--
-- Indexes for table `security_incidents`
--
ALTER TABLE `security_incidents`
  ADD PRIMARY KEY (`incident_id`),
  ADD KEY `fk_security_incidents_reported_by_emp_id` (`reported_by_emp_id`),
  ADD KEY `fk_security_incidents_assigned_to_emp_id` (`assigned_to_emp_id`);

--
-- Indexes for table `security_policies`
--
ALTER TABLE `security_policies`
  ADD PRIMARY KEY (`policy_id`),
  ADD KEY `fk_security_policies_doc_id` (`doc_id`);

--
-- Indexes for table `sla_policies`
--
ALTER TABLE `sla_policies`
  ADD PRIMARY KEY (`sla_id`),
  ADD UNIQUE KEY `priority` (`priority`);

--
-- Indexes for table `systems_catalog`
--
ALTER TABLE `systems_catalog`
  ADD PRIMARY KEY (`system_id`);

--
-- Indexes for table `system_integrations`
--
ALTER TABLE `system_integrations`
  ADD PRIMARY KEY (`integration_id`),
  ADD UNIQUE KEY `link_code` (`link_code`),
  ADD KEY `source_system_id` (`source_system_id`),
  ADD KEY `target_system_id` (`target_system_id`);

--
-- Indexes for table `system_integration_logs`
--
ALTER TABLE `system_integration_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `link_code` (`link_code`),
  ADD KEY `source_system_id` (`source_system_id`),
  ADD KEY `target_system_id` (`target_system_id`),
  ADD KEY `executed_at` (`executed_at`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`tkt_id`),
  ADD KEY `fk_tickets_requester_cus_id` (`requester_cus_id`),
  ADD KEY `fk_tickets_requester_emp_id` (`requester_emp_id`),
  ADD KEY `fk_tickets_assigned_emp_id` (`assigned_emp_id`);

--
-- Indexes for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `fk_ticket_comments_tkt_id` (`tkt_id`),
  ADD KEY `fk_ticket_comments_author_emp_id` (`author_emp_id`);

--
-- Indexes for table `ticket_escalations`
--
ALTER TABLE `ticket_escalations`
  ADD PRIMARY KEY (`escalation_id`),
  ADD KEY `fk_ticket_escalations_tkt_id` (`tkt_id`),
  ADD KEY `fk_ticket_escalations_escalated_to_emp_id` (`escalated_to_emp_id`);

--
-- Indexes for table `training_records`
--
ALTER TABLE `training_records`
  ADD PRIMARY KEY (`training_id`),
  ADD KEY `fk_training_records_emp_id` (`emp_id`),
  ADD KEY `fk_training_records_certificate_doc_id` (`certificate_doc_id`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `fk_user_sessions_employee_account_id` (`employee_account_id`),
  ADD KEY `fk_user_sessions_customer_account_id` (`customer_account_id`),
  ADD KEY `fk_user_sessions_system_id` (`system_id`),
  ADD KEY `fk_user_sessions_device_id` (`device_id`),
  ADD KEY `fk_user_sessions_ip_id` (`ip_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_reviews`
--
ALTER TABLE `access_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `api_access_logs`
--
ALTER TABLE `api_access_logs`
  MODIFY `api_log_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `api_credentials`
--
ALTER TABLE `api_credentials`
  MODIFY `credential_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `api_partners`
--
ALTER TABLE `api_partners`
  MODIFY `partner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `audit_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `authentication_events`
--
ALTER TABLE `authentication_events`
  MODIFY `event_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `billing_cycles`
--
ALTER TABLE `billing_cycles`
  MODIFY `cycle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `budget_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `compliance_controls`
--
ALTER TABLE `compliance_controls`
  MODIFY `control_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `contract_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100001;

--
-- AUTO_INCREMENT for table `crm_activities`
--
ALTER TABLE `crm_activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `customer_accounts`
--
ALTER TABLE `customer_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `department_boards`
--
ALTER TABLE `department_boards`
  MODIFY `board_post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `developer_api_keys`
--
ALTER TABLE `developer_api_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `developer_endpoints`
--
ALTER TABLE `developer_endpoints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `developer_guides`
--
ALTER TABLE `developer_guides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `developer_partner_applications`
--
ALTER TABLE `developer_partner_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `developer_sandbox_logs`
--
ALTER TABLE `developer_sandbox_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `developer_sandbox_presets`
--
ALTER TABLE `developer_sandbox_presets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `developer_webhooks`
--
ALTER TABLE `developer_webhooks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `device_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `document_access_log`
--
ALTER TABLE `document_access_log`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `document_approvals`
--
ALTER TABLE `document_approvals`
  MODIFY `approval_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `document_retention_policies`
--
ALTER TABLE `document_retention_policies`
  MODIFY `policy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `document_versions`
--
ALTER TABLE `document_versions`
  MODIFY `version_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `employee_offboarding`
--
ALTER TABLE `employee_offboarding`
  MODIFY `offboarding_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `employee_onboarding`
--
ALTER TABLE `employee_onboarding`
  MODIFY `onboarding_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `financial_reports`
--
ALTER TABLE `financial_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `integration_logs`
--
ALTER TABLE `integration_logs`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `integration_requirements`
--
ALTER TABLE `integration_requirements`
  MODIFY `req_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `internal_policies`
--
ALTER TABLE `internal_policies`
  MODIFY `policy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ip_addresses`
--
ALTER TABLE `ip_addresses`
  MODIFY `ip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `it_assets`
--
ALTER TABLE `it_assets`
  MODIFY `asset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `job_postings`
--
ALTER TABLE `job_postings`
  MODIFY `posting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `knowledge_base_articles`
--
ALTER TABLE `knowledge_base_articles`
  MODIFY `kb_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `lead_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100002;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `leave_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `opportunities`
--
ALTER TABLE `opportunities`
  MODIFY `opp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100002;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `portal_accounts`
--
ALTER TABLE `portal_accounts`
  MODIFY `portal_user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `portal_notifications`
--
ALTER TABLE `portal_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `portal_sessions`
--
ALTER TABLE `portal_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quotes`
--
ALTER TABLE `quotes`
  MODIFY `quote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100006;

--
-- AUTO_INCREMENT for table `recertification_windows`
--
ALTER TABLE `recertification_windows`
  MODIFY `window_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `recruitment_candidates`
--
ALTER TABLE `recruitment_candidates`
  MODIFY `candidate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `risk_register`
--
ALTER TABLE `risk_register`
  MODIFY `risk_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `sales_forecasts`
--
ALTER TABLE `sales_forecasts`
  MODIFY `forecast_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `security_events`
--
ALTER TABLE `security_events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `security_incidents`
--
ALTER TABLE `security_incidents`
  MODIFY `incident_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `security_policies`
--
ALTER TABLE `security_policies`
  MODIFY `policy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `sla_policies`
--
ALTER TABLE `sla_policies`
  MODIFY `sla_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `system_integrations`
--
ALTER TABLE `system_integrations`
  MODIFY `integration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `system_integration_logs`
--
ALTER TABLE `system_integration_logs`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `ticket_escalations`
--
ALTER TABLE `ticket_escalations`
  MODIFY `escalation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `training_records`
--
ALTER TABLE `training_records`
  MODIFY `training_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `session_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `access_reviews`
--
ALTER TABLE `access_reviews`
  ADD CONSTRAINT `fk_access_reviews_emp_id` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_access_reviews_reviewed_by_emp_id` FOREIGN KEY (`reviewed_by_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `fk_announcements_audience_dept` FOREIGN KEY (`audience_dept`) REFERENCES `departments` (`dept_code`),
  ADD CONSTRAINT `fk_announcements_posted_by_emp_id` FOREIGN KEY (`posted_by_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `api_access_logs`
--
ALTER TABLE `api_access_logs`
  ADD CONSTRAINT `fk_api_access_logs_credential_id` FOREIGN KEY (`credential_id`) REFERENCES `api_credentials` (`credential_id`),
  ADD CONSTRAINT `fk_api_access_logs_partner_id` FOREIGN KEY (`partner_id`) REFERENCES `api_partners` (`partner_id`),
  ADD CONSTRAINT `fk_api_access_logs_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`);

--
-- Constraints for table `api_credentials`
--
ALTER TABLE `api_credentials`
  ADD CONSTRAINT `fk_api_credentials_partner_id` FOREIGN KEY (`partner_id`) REFERENCES `api_partners` (`partner_id`);

--
-- Constraints for table `api_partners`
--
ALTER TABLE `api_partners`
  ADD CONSTRAINT `fk_api_partners_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`);

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_logs_actor_customer_id` FOREIGN KEY (`actor_customer_id`) REFERENCES `customers` (`cus_id`),
  ADD CONSTRAINT `fk_audit_logs_actor_emp_id` FOREIGN KEY (`actor_emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_audit_logs_device_id` FOREIGN KEY (`device_id`) REFERENCES `devices` (`device_id`),
  ADD CONSTRAINT `fk_audit_logs_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`);

--
-- Constraints for table `authentication_events`
--
ALTER TABLE `authentication_events`
  ADD CONSTRAINT `fk_authentication_events_customer_account_id` FOREIGN KEY (`customer_account_id`) REFERENCES `customer_accounts` (`account_id`),
  ADD CONSTRAINT `fk_authentication_events_device_id` FOREIGN KEY (`device_id`) REFERENCES `devices` (`device_id`),
  ADD CONSTRAINT `fk_authentication_events_employee_account_id` FOREIGN KEY (`employee_account_id`) REFERENCES `employee_accounts` (`account_id`),
  ADD CONSTRAINT `fk_authentication_events_ip_id` FOREIGN KEY (`ip_id`) REFERENCES `ip_addresses` (`ip_id`),
  ADD CONSTRAINT `fk_authentication_events_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`);

--
-- Constraints for table `billing_cycles`
--
ALTER TABLE `billing_cycles`
  ADD CONSTRAINT `fk_billing_cycles_prj_id` FOREIGN KEY (`prj_id`) REFERENCES `projects` (`prj_id`);

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `fk_budgets_department_code` FOREIGN KEY (`department_code`) REFERENCES `departments` (`dept_code`);

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `fk_contacts_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`);

--
-- Constraints for table `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `fk_contracts_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`),
  ADD CONSTRAINT `fk_contracts_doc_id` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`),
  ADD CONSTRAINT `fk_contracts_opp_id` FOREIGN KEY (`opp_id`) REFERENCES `opportunities` (`opp_id`),
  ADD CONSTRAINT `fk_contracts_prj_id` FOREIGN KEY (`prj_id`) REFERENCES `projects` (`prj_id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `fk_customers_account_manager_emp_id` FOREIGN KEY (`account_manager_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `customer_accounts`
--
ALTER TABLE `customer_accounts`
  ADD CONSTRAINT `fk_customer_accounts_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`);

--
-- Constraints for table `customer_pricing`
--
ALTER TABLE `customer_pricing`
  ADD CONSTRAINT `fk_customer_pricing_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`),
  ADD CONSTRAINT `fk_customer_pricing_prod_id` FOREIGN KEY (`prod_id`) REFERENCES `products` (`prod_id`);

--
-- Constraints for table `department_boards`
--
ALTER TABLE `department_boards`
  ADD CONSTRAINT `fk_department_boards_department_code` FOREIGN KEY (`department_code`) REFERENCES `departments` (`dept_code`),
  ADD CONSTRAINT `fk_department_boards_posted_by_emp_id` FOREIGN KEY (`posted_by_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `fk_devices_assigned_emp_id` FOREIGN KEY (`assigned_emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_devices_department_code` FOREIGN KEY (`department_code`) REFERENCES `departments` (`dept_code`);

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `fk_documents_owner_emp_id` FOREIGN KEY (`owner_emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_documents_related_cus_id` FOREIGN KEY (`related_cus_id`) REFERENCES `customers` (`cus_id`),
  ADD CONSTRAINT `fk_documents_related_prj_id` FOREIGN KEY (`related_prj_id`) REFERENCES `projects` (`prj_id`);

--
-- Constraints for table `document_access_log`
--
ALTER TABLE `document_access_log`
  ADD CONSTRAINT `fk_document_access_log_accessed_by_cus_id` FOREIGN KEY (`accessed_by_cus_id`) REFERENCES `customers` (`cus_id`),
  ADD CONSTRAINT `fk_document_access_log_accessed_by_emp_id` FOREIGN KEY (`accessed_by_emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_document_access_log_device_id` FOREIGN KEY (`device_id`) REFERENCES `devices` (`device_id`),
  ADD CONSTRAINT `fk_document_access_log_doc_id` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`),
  ADD CONSTRAINT `fk_document_access_log_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`);

--
-- Constraints for table `document_approvals`
--
ALTER TABLE `document_approvals`
  ADD CONSTRAINT `fk_document_approvals_doc_id` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`),
  ADD CONSTRAINT `fk_document_approvals_reviewer_emp_id` FOREIGN KEY (`reviewer_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `document_versions`
--
ALTER TABLE `document_versions`
  ADD CONSTRAINT `fk_document_versions_doc_id` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`),
  ADD CONSTRAINT `fk_document_versions_uploaded_by_emp_id` FOREIGN KEY (`uploaded_by_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_employees_department_code` FOREIGN KEY (`department_code`) REFERENCES `departments` (`dept_code`),
  ADD CONSTRAINT `fk_employees_manager_emp_id` FOREIGN KEY (`manager_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  ADD CONSTRAINT `fk_employee_accounts_emp_id` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `employee_offboarding`
--
ALTER TABLE `employee_offboarding`
  ADD CONSTRAINT `fk_employee_offboarding_emp_id` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `employee_onboarding`
--
ALTER TABLE `employee_onboarding`
  ADD CONSTRAINT `fk_employee_onboarding_emp_id` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `employee_roles`
--
ALTER TABLE `employee_roles`
  ADD CONSTRAINT `fk_employee_roles_emp_id` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_employee_roles_granted_by_emp_id` FOREIGN KEY (`granted_by_emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_employee_roles_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `integration_logs`
--
ALTER TABLE `integration_logs`
  ADD CONSTRAINT `fk_integration_logs_partner_id` FOREIGN KEY (`partner_id`) REFERENCES `api_partners` (`partner_id`);

--
-- Constraints for table `integration_requirements`
--
ALTER TABLE `integration_requirements`
  ADD CONSTRAINT `fk_integration_requirements_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`),
  ADD CONSTRAINT `fk_integration_requirements_prj_id` FOREIGN KEY (`prj_id`) REFERENCES `projects` (`prj_id`),
  ADD CONSTRAINT `fk_integration_requirements_reviewed_by_emp_id` FOREIGN KEY (`reviewed_by_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `internal_policies`
--
ALTER TABLE `internal_policies`
  ADD CONSTRAINT `fk_internal_policies_doc_id` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_invoices_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`),
  ADD CONSTRAINT `fk_invoices_prj_id` FOREIGN KEY (`prj_id`) REFERENCES `projects` (`prj_id`);

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `fk_invoice_items_inv` FOREIGN KEY (`inv_id`) REFERENCES `invoices` (`inv_id`) ON DELETE CASCADE;

--
-- Constraints for table `ip_addresses`
--
ALTER TABLE `ip_addresses`
  ADD CONSTRAINT `fk_ip_addresses_device_id` FOREIGN KEY (`device_id`) REFERENCES `devices` (`device_id`);

--
-- Constraints for table `it_assets`
--
ALTER TABLE `it_assets`
  ADD CONSTRAINT `fk_it_assets_department_code` FOREIGN KEY (`department_code`) REFERENCES `departments` (`dept_code`),
  ADD CONSTRAINT `fk_it_assets_emp_id` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_it_assets_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`);

--
-- Constraints for table `job_postings`
--
ALTER TABLE `job_postings`
  ADD CONSTRAINT `fk_job_postings_department_code` FOREIGN KEY (`department_code`) REFERENCES `departments` (`dept_code`);

--
-- Constraints for table `knowledge_base_articles`
--
ALTER TABLE `knowledge_base_articles`
  ADD CONSTRAINT `fk_knowledge_base_articles_created_by_emp_id` FOREIGN KEY (`created_by_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `fk_leads_assigned_sales_emp_id` FOREIGN KEY (`assigned_sales_emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_leads_converted_cus_id` FOREIGN KEY (`converted_cus_id`) REFERENCES `customers` (`cus_id`);

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `fk_leave_requests_approved_by_emp_id` FOREIGN KEY (`approved_by_emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_leave_requests_emp_id` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `opportunities`
--
ALTER TABLE `opportunities`
  ADD CONSTRAINT `fk_opportunities_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`),
  ADD CONSTRAINT `fk_opportunities_lead_id` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`lead_id`),
  ADD CONSTRAINT `fk_opportunities_sales_emp_id` FOREIGN KEY (`sales_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `fk_order_items_prod_id` FOREIGN KEY (`prod_id`) REFERENCES `products` (`prod_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_inv_id` FOREIGN KEY (`inv_id`) REFERENCES `invoices` (`inv_id`);

--
-- Constraints for table `portal_accounts`
--
ALTER TABLE `portal_accounts`
  ADD CONSTRAINT `fk_portal_accounts_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`);

--
-- Constraints for table `portal_notifications`
--
ALTER TABLE `portal_notifications`
  ADD CONSTRAINT `fk_portal_notifications_portal_user_id` FOREIGN KEY (`portal_user_id`) REFERENCES `portal_accounts` (`portal_user_id`);

--
-- Constraints for table `portal_sessions`
--
ALTER TABLE `portal_sessions`
  ADD CONSTRAINT `fk_portal_sessions_portal_user_id` FOREIGN KEY (`portal_user_id`) REFERENCES `portal_accounts` (`portal_user_id`);

--
-- Constraints for table `product_inventory`
--
ALTER TABLE `product_inventory`
  ADD CONSTRAINT `fk_product_inventory_prod_id` FOREIGN KEY (`prod_id`) REFERENCES `products` (`prod_id`);

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_projects_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`),
  ADD CONSTRAINT `fk_projects_project_manager_emp_id` FOREIGN KEY (`project_manager_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `quotes`
--
ALTER TABLE `quotes`
  ADD CONSTRAINT `fk_quotes_created_by_emp_id` FOREIGN KEY (`created_by_emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_quotes_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`),
  ADD CONSTRAINT `fk_quotes_prod_id` FOREIGN KEY (`prod_id`) REFERENCES `products` (`prod_id`);

--
-- Constraints for table `recruitment_candidates`
--
ALTER TABLE `recruitment_candidates`
  ADD CONSTRAINT `fk_recruitment_candidates_department_code` FOREIGN KEY (`department_code`) REFERENCES `departments` (`dept_code`);

--
-- Constraints for table `risk_register`
--
ALTER TABLE `risk_register`
  ADD CONSTRAINT `fk_risk_register_owner_emp_id` FOREIGN KEY (`owner_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_role_permissions_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`),
  ADD CONSTRAINT `fk_role_permissions_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `role_system_access`
--
ALTER TABLE `role_system_access`
  ADD CONSTRAINT `fk_role_system_access_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `fk_role_system_access_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`);

--
-- Constraints for table `sales_forecasts`
--
ALTER TABLE `sales_forecasts`
  ADD CONSTRAINT `fk_sales_forecasts_sales_emp_id` FOREIGN KEY (`sales_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `security_events`
--
ALTER TABLE `security_events`
  ADD CONSTRAINT `fk_security_events_actor_customer_id` FOREIGN KEY (`actor_customer_id`) REFERENCES `customers` (`cus_id`),
  ADD CONSTRAINT `fk_security_events_actor_emp_id` FOREIGN KEY (`actor_emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_security_events_related_tkt_id` FOREIGN KEY (`related_tkt_id`) REFERENCES `tickets` (`tkt_id`),
  ADD CONSTRAINT `fk_security_events_source_device_id` FOREIGN KEY (`source_device_id`) REFERENCES `devices` (`device_id`),
  ADD CONSTRAINT `fk_security_events_source_ip_id` FOREIGN KEY (`source_ip_id`) REFERENCES `ip_addresses` (`ip_id`),
  ADD CONSTRAINT `fk_security_events_source_system_id` FOREIGN KEY (`source_system_id`) REFERENCES `systems_catalog` (`system_id`),
  ADD CONSTRAINT `fk_security_events_target_device_id` FOREIGN KEY (`target_device_id`) REFERENCES `devices` (`device_id`);

--
-- Constraints for table `security_incidents`
--
ALTER TABLE `security_incidents`
  ADD CONSTRAINT `fk_security_incidents_assigned_to_emp_id` FOREIGN KEY (`assigned_to_emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_security_incidents_reported_by_emp_id` FOREIGN KEY (`reported_by_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `security_policies`
--
ALTER TABLE `security_policies`
  ADD CONSTRAINT `fk_security_policies_doc_id` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`);

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `fk_tickets_assigned_emp_id` FOREIGN KEY (`assigned_emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_tickets_requester_cus_id` FOREIGN KEY (`requester_cus_id`) REFERENCES `customers` (`cus_id`),
  ADD CONSTRAINT `fk_tickets_requester_emp_id` FOREIGN KEY (`requester_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  ADD CONSTRAINT `fk_ticket_comments_author_emp_id` FOREIGN KEY (`author_emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_ticket_comments_tkt_id` FOREIGN KEY (`tkt_id`) REFERENCES `tickets` (`tkt_id`);

--
-- Constraints for table `ticket_escalations`
--
ALTER TABLE `ticket_escalations`
  ADD CONSTRAINT `fk_ticket_escalations_escalated_to_emp_id` FOREIGN KEY (`escalated_to_emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_ticket_escalations_tkt_id` FOREIGN KEY (`tkt_id`) REFERENCES `tickets` (`tkt_id`);

--
-- Constraints for table `training_records`
--
ALTER TABLE `training_records`
  ADD CONSTRAINT `fk_training_records_certificate_doc_id` FOREIGN KEY (`certificate_doc_id`) REFERENCES `documents` (`doc_id`),
  ADD CONSTRAINT `fk_training_records_emp_id` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `fk_user_sessions_customer_account_id` FOREIGN KEY (`customer_account_id`) REFERENCES `customer_accounts` (`account_id`),
  ADD CONSTRAINT `fk_user_sessions_device_id` FOREIGN KEY (`device_id`) REFERENCES `devices` (`device_id`),
  ADD CONSTRAINT `fk_user_sessions_employee_account_id` FOREIGN KEY (`employee_account_id`) REFERENCES `employee_accounts` (`account_id`),
  ADD CONSTRAINT `fk_user_sessions_ip_id` FOREIGN KEY (`ip_id`) REFERENCES `ip_addresses` (`ip_id`),
  ADD CONSTRAINT `fk_user_sessions_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
