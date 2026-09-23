-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: vostokpribor
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `vostokpribor`
--

/*!40000 DROP DATABASE IF EXISTS `vostokpribor`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `vostokpribor` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `vostokpribor`;

--
-- Table structure for table `access_reviews`
--

DROP TABLE IF EXISTS `access_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `access_reviews` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` varchar(10) DEFAULT NULL,
  `reviewed_by_emp_id` varchar(10) DEFAULT NULL,
  `review_date` date DEFAULT curdate(),
  `finding` text DEFAULT NULL,
  `action_taken` text DEFAULT NULL,
  PRIMARY KEY (`review_id`),
  KEY `fk_access_reviews_emp_id` (`emp_id`),
  KEY `fk_access_reviews_reviewed_by_emp_id` (`reviewed_by_emp_id`),
  CONSTRAINT `fk_access_reviews_emp_id` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`),
  CONSTRAINT `fk_access_reviews_reviewed_by_emp_id` FOREIGN KEY (`reviewed_by_emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `access_reviews`
--

LOCK TABLES `access_reviews` WRITE;
/*!40000 ALTER TABLE `access_reviews` DISABLE KEYS */;
INSERT INTO `access_reviews` VALUES (1,'EMP-1001','EMP-1005','2026-09-15','Executive clearance L4 verified across grid operations and telemetry.','Attested - All Entitlements Validated'),(2,'EMP-1002','EMP-1005','2026-09-18','Chief Automation Engineer - SCADA & PLC operational permits active.','Pending Quarterly Attestation Sign-off'),(3,'EMP-1004','EMP-1005','2026-09-14','CTO root trust boundary attestation completed.','Attested - Master Signer Approved'),(4,'EMP-1005','EMP-1001','2026-09-10','Governance Officer dual-custody audit interlock verified.','Master Signer Attestation Active'),(5,'EMP-1007','EMP-1005','2026-09-16','Warehouse logistics dispatch tokens active for Shymkent depot.','Pending Quarterly Attestation Sign-off'),(6,'EMP-1009','EMP-1005','2026-09-20','SEC-POL-44 Breach Detected: Vendor contract termination date was 14 days ago. High-privilege RSA SSH-key remains configured inside SYS-03 (CNC SCADA Gateway).','Orphan Account Isolated - Flagged Unlawful Bind'),(7,'EMP-1011','EMP-1005','2026-09-12','Procurement manager telemetry and invoice approval limits verified.','Attested - Role Validated'),(8,'EMP-1018','EMP-1005','2026-09-15','Systems Engineer L3 access to IT assets and SLA escalation queue verified.','Attested - Validated'),(9,'EMP-1020','EMP-1005','2026-09-17','Lead Developer API Sandbox key generation access verified.','Attested - Validated');
/*!40000 ALTER TABLE `access_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `posted_by_emp_id` varchar(10) DEFAULT NULL,
  `audience_dept` varchar(4) DEFAULT NULL,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`announcement_id`),
  KEY `fk_announcements_posted_by_emp_id` (`posted_by_emp_id`),
  KEY `fk_announcements_audience_dept` (`audience_dept`),
  CONSTRAINT `fk_announcements_audience_dept` FOREIGN KEY (`audience_dept`) REFERENCES `departments` (`dept_code`),
  CONSTRAINT `fk_announcements_posted_by_emp_id` FOREIGN KEY (`posted_by_emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (3,'New Industrial Optical Pyrometer Metrotec-900 Released to Production','Engineering & R&D has successfully transitioned the Metrotec-900 into high-volume assembly. Product documentation and calibration sheets are now in the File Center.','EMP-1004','ENG','2026-09-20 07:30:00'),(4,'Winter Equipment Maintenance & Calibration Schedules Published','Operations & Logistics has uploaded the Q4 winter servicing windows for field calibration teams across Karaganda, Pavlodar, and Atyrau.','EMP-1002','OPS','2026-09-21 05:15:00');
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_access_logs`
--

DROP TABLE IF EXISTS `api_access_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_access_logs` (
  `api_log_id` bigint(20) NOT NULL AUTO_INCREMENT,
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
  `success` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`api_log_id`),
  KEY `fk_api_access_logs_credential_id` (`credential_id`),
  KEY `fk_api_access_logs_partner_id` (`partner_id`),
  KEY `fk_api_access_logs_system_id` (`system_id`),
  CONSTRAINT `fk_api_access_logs_credential_id` FOREIGN KEY (`credential_id`) REFERENCES `api_credentials` (`credential_id`),
  CONSTRAINT `fk_api_access_logs_partner_id` FOREIGN KEY (`partner_id`) REFERENCES `api_partners` (`partner_id`),
  CONSTRAINT `fk_api_access_logs_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_access_logs`
--

LOCK TABLES `api_access_logs` WRITE;
/*!40000 ALTER TABLE `api_access_logs` DISABLE KEYS */;
INSERT INTO `api_access_logs` VALUES (1,1,1,'DEV','/api/v1/telemetry/scada','GET','10.240.1.20',200,'2026-09-23 08:00:00',42,'VostokClient/2.4','REQ-2026-9901',1),(2,2,2,'CRM','/api/v1/customers/lookup','POST','10.240.3.15',200,'2026-09-23 08:15:00',65,'VostokCRM/4.1','REQ-2026-9902',1),(3,3,3,'SHP','/api/v1/orders/create','POST','10.240.4.8',201,'2026-09-23 08:30:00',120,'B2BPortal/1.0','REQ-2026-9903',1),(4,4,4,'ADM','/api/v1/governance/attest','POST','10.240.0.1',200,'2026-09-23 08:45:00',38,'GovCoreDaemon/11.4','REQ-2026-9904',1);
/*!40000 ALTER TABLE `api_access_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_credentials`
--

DROP TABLE IF EXISTS `api_credentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_credentials` (
  `credential_id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `api_key_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `revoked` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`credential_id`),
  KEY `fk_api_credentials_partner_id` (`partner_id`),
  CONSTRAINT `fk_api_credentials_partner_id` FOREIGN KEY (`partner_id`) REFERENCES `api_partners` (`partner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_credentials`
--

LOCK TABLES `api_credentials` WRITE;
/*!40000 ALTER TABLE `api_credentials` DISABLE KEYS */;
INSERT INTO `api_credentials` VALUES (1,1,'$2y$12$bvGevGpqrlkisCIAr1UKaeTRl/oimOkZl6ruFAl8WkYoaB7xLL3jK','2026-01-15 07:00:00','2027-01-15 07:00:00',0),(2,2,'$2y$12$bvGevGpqrlkisCIAr1UKaeTRl/oimOkZl6ruFAl8WkYoaB7xLL3jK','2026-02-20 08:30:00','2027-02-20 08:30:00',0),(3,3,'$2y$12$bvGevGpqrlkisCIAr1UKaeTRl/oimOkZl6ruFAl8WkYoaB7xLL3jK','2026-03-10 11:00:00','2027-03-10 11:00:00',0),(4,4,'$2y$12$bvGevGpqrlkisCIAr1UKaeTRl/oimOkZl6ruFAl8WkYoaB7xLL3jK','2026-04-05 06:15:00','2027-04-05 06:15:00',0);
/*!40000 ALTER TABLE `api_credentials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_partners`
--

DROP TABLE IF EXISTS `api_partners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_partners` (
  `partner_id` int(11) NOT NULL AUTO_INCREMENT,
  `cus_id` varchar(10) DEFAULT NULL,
  `partner_name` varchar(150) DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(30) DEFAULT 'Active',
  PRIMARY KEY (`partner_id`),
  KEY `fk_api_partners_cus_id` (`cus_id`),
  CONSTRAINT `fk_api_partners_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_partners`
--

LOCK TABLES `api_partners` WRITE;
/*!40000 ALTER TABLE `api_partners` DISABLE KEYS */;
INSERT INTO `api_partners` VALUES (1,'CUS-1001','Aral Geomatics Automation Labs','2026-01-15 07:00:00','Active'),(2,'CUS-1003','Steppe Mining SCADA Engineering','2026-02-20 08:30:00','Active'),(3,'CUS-1005','Tashkent Precision Telemetry Division','2026-03-10 11:00:00','Active'),(4,'CUS-1006','Rhein Werk Metrology Interface','2026-04-05 06:15:00','Active');
/*!40000 ALTER TABLE `api_partners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `audit_id` bigint(20) NOT NULL AUTO_INCREMENT,
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
  `occurred_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`audit_id`),
  KEY `fk_audit_logs_actor_emp_id` (`actor_emp_id`),
  KEY `fk_audit_logs_actor_customer_id` (`actor_customer_id`),
  KEY `fk_audit_logs_system_id` (`system_id`),
  KEY `fk_audit_logs_device_id` (`device_id`),
  CONSTRAINT `fk_audit_logs_actor_customer_id` FOREIGN KEY (`actor_customer_id`) REFERENCES `customers` (`cus_id`),
  CONSTRAINT `fk_audit_logs_actor_emp_id` FOREIGN KEY (`actor_emp_id`) REFERENCES `employees` (`emp_id`),
  CONSTRAINT `fk_audit_logs_device_id` FOREIGN KEY (`device_id`) REFERENCES `devices` (`device_id`),
  CONSTRAINT `fk_audit_logs_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,'EMP-1005',NULL,'GOV-CORE','ADM','QUARTERLY_ATTESTATION_CYCLE_OPENED','access_matrix','DOC-2026-007','10.240.0.1',NULL,NULL,'{\"cycle\":\"2026-Q3\",\"total_attested\":17,\"posture\":\"85%\"}','SUCCESS','2026-09-21 05:30:00'),(2,'EMP-1001',NULL,'EXEC-PORTAL','ADM','DUAL_CUSTODY_SIGN_OFF','governance_policy','POL-SEC-2026.04','10.240.0.12',NULL,NULL,'{\"cosigners\":[\"EMP-1001\",\"EMP-1005\"],\"status\":\"APPROVED\"}','SUCCESS','2026-09-21 06:14:22'),(3,'EMP-1004',NULL,'DEV-ENCLAVE','DEV','HSM_ROOT_KEY_ROTATION','cryptographic_vault','KEY-FIPS-140-3','10.240.1.44',NULL,'{\"active_key\":\"0x7F2A...\",\"algorithm\":\"RSA-4096\"}','{\"active_key\":\"0x9C1B...\",\"algorithm\":\"CRYSTALS-Kyber-1024\"}','SUCCESS','2026-09-21 07:05:11'),(4,'EMP-1009',NULL,'SCADA-BRIDGE','CUS','UNAUTHORIZED_INGESTION_PROBE','api_endpoint','/api/v1/telemetry/sc','192.168.10.89',NULL,NULL,'{\"action\":\"PROBE_BLOCKED\",\"reason\":\"CONTRACT_EXPIRED\"}','BLOCKED','2026-09-21 08:22:45'),(5,'EMP-1005',NULL,'GOV-CORE','ADM','ISOLATION_TRIGGER_ACTIVATED','employee_account','EMP-1009','10.240.0.1',NULL,'{\"status\":\"Active\"}','{\"status\":\"Suspended\",\"flag\":\"UNLAWFUL_BIND\"}','QUARANTINED','2026-09-21 08:25:30'),(6,'EMP-1018',NULL,'IT-DESK','IT','BREAK_GLASS_SESSION_REQUEST','infrastructure_node','NODE-ALMATY-ALPHA','10.240.2.18',NULL,NULL,'{\"ticket_id\":\"TKT-2026-099\",\"duration\":\"2h\"}','AUTHORIZED','2026-09-21 10:40:10'),(7,'EMP-1020',NULL,'DEV-PORTAL','DEV','API_WEBHOOK_REGISTRATION','partner_sandbox','PART-004','10.240.1.20',NULL,NULL,'{\"client\":\"Aral Geomatics\",\"endpoint\":\"https://api.aral-gis.kz/vostok\"}','SUCCESS','2026-09-21 11:12:00'),(8,'EMP-1006',NULL,'SALES-CRM','CRM','SALES_CONTRACT_SIGN_OFF','contract','CTR-2026-042','10.240.3.15',NULL,'{\"status\":\"Draft\"}','{\"status\":\"Active\",\"total_value\":480000}','SUCCESS','2026-09-21 12:01:45'),(9,'EMP-1008',NULL,'B2B-SHOP','SHP','BULK_ORDER_DISPATCH_CONFIRM','order','ORD-2026-104','10.240.4.8',NULL,'{\"status\":\"Pending\"}','{\"status\":\"Dispatched\",\"tracking\":\"KZ-POST-9921\"}','SUCCESS','2026-09-21 13:30:19'),(10,'EMP-1005',NULL,'GOV-CORE','ADM','ACCESS_MATRIX_DISPOSITION_UPDATE','employee','EMP-1002','10.240.0.1',NULL,'{\"disposition\":\"Review\"}','{\"disposition\":\"Attest\"}','SUCCESS','2026-09-21 14:15:33');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `authentication_events`
--

DROP TABLE IF EXISTS `authentication_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authentication_events` (
  `event_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_type` varchar(20) NOT NULL,
  `employee_account_id` int(11) DEFAULT NULL,
  `customer_account_id` int(11) DEFAULT NULL,
  `system_id` varchar(4) DEFAULT NULL,
  `device_id` int(11) DEFAULT NULL,
  `ip_id` int(11) DEFAULT NULL,
  `event_type` varchar(30) DEFAULT NULL,
  `occurred_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `success` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`event_id`),
  KEY `fk_authentication_events_employee_account_id` (`employee_account_id`),
  KEY `fk_authentication_events_customer_account_id` (`customer_account_id`),
  KEY `fk_authentication_events_system_id` (`system_id`),
  KEY `fk_authentication_events_device_id` (`device_id`),
  KEY `fk_authentication_events_ip_id` (`ip_id`),
  CONSTRAINT `fk_authentication_events_customer_account_id` FOREIGN KEY (`customer_account_id`) REFERENCES `customer_accounts` (`account_id`),
  CONSTRAINT `fk_authentication_events_device_id` FOREIGN KEY (`device_id`) REFERENCES `devices` (`device_id`),
  CONSTRAINT `fk_authentication_events_employee_account_id` FOREIGN KEY (`employee_account_id`) REFERENCES `employee_accounts` (`account_id`),
  CONSTRAINT `fk_authentication_events_ip_id` FOREIGN KEY (`ip_id`) REFERENCES `ip_addresses` (`ip_id`),
  CONSTRAINT `fk_authentication_events_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authentication_events`
--

LOCK TABLES `authentication_events` WRITE;
/*!40000 ALTER TABLE `authentication_events` DISABLE KEYS */;
INSERT INTO `authentication_events` VALUES (1,'Employee',53,NULL,'HR',NULL,NULL,'LOGIN_SUCCESS','2026-09-21 17:32:28',1),(2,'Employee',53,NULL,'HR',NULL,NULL,'LOGIN_SUCCESS','2026-09-21 17:36:16',1);
/*!40000 ALTER TABLE `authentication_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing_cycles`
--

DROP TABLE IF EXISTS `billing_cycles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_cycles` (
  `cycle_id` int(11) NOT NULL AUTO_INCREMENT,
  `prj_id` varchar(15) NOT NULL,
  `milestone_description` varchar(200) DEFAULT NULL,
  `scheduled_date` date DEFAULT NULL,
  `invoiced` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`cycle_id`),
  KEY `fk_billing_cycles_prj_id` (`prj_id`),
  CONSTRAINT `fk_billing_cycles_prj_id` FOREIGN KEY (`prj_id`) REFERENCES `projects` (`prj_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing_cycles`
--

LOCK TABLES `billing_cycles` WRITE;
/*!40000 ALTER TABLE `billing_cycles` DISABLE KEYS */;
INSERT INTO `billing_cycles` VALUES (1,'PRJ-2026-001','Initial Telemetry Architecture Blueprint Sign-Off','2026-02-01',1),(2,'PRJ-2026-001','Hardware Staging & Optical Transducer Delivery','2026-05-15',1),(3,'PRJ-2026-001','Final SCADA Interlock Verification & Site Commissioning','2026-11-30',0),(4,'PRJ-2026-002','High-Voltage Switchgear Relays Installation','2026-04-10',1),(5,'PRJ-2026-002','State Metrology Calibration Certificate Handover','2026-09-30',0);
/*!40000 ALTER TABLE `billing_cycles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `budgets`
--

DROP TABLE IF EXISTS `budgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgets` (
  `budget_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_code` varchar(4) DEFAULT NULL,
  `fiscal_year` int(11) DEFAULT NULL,
  `allocated_amount` decimal(14,2) DEFAULT NULL,
  `spent_amount` decimal(14,2) DEFAULT 0.00,
  PRIMARY KEY (`budget_id`),
  KEY `fk_budgets_department_code` (`department_code`),
  CONSTRAINT `fk_budgets_department_code` FOREIGN KEY (`department_code`) REFERENCES `departments` (`dept_code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `budgets`
--

LOCK TABLES `budgets` WRITE;
/*!40000 ALTER TABLE `budgets` DISABLE KEYS */;
INSERT INTO `budgets` VALUES (1,'ENG',2026,3200000.00,2150000.00),(2,'OPS',2026,4500000.00,3120000.00),(3,'ITD',2026,1800000.00,1290000.00),(4,'SAL',2026,1200000.00,840000.00),(5,'FIN',2026,750000.00,480000.00),(6,'HRA',2026,600000.00,390000.00),(7,'GOV',2026,500000.00,310000.00),(8,'EXE',2026,950000.00,620000.00);
/*!40000 ALTER TABLE `budgets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `cus_id` varchar(10) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`contact_id`),
  KEY `fk_contacts_cus_id` (`cus_id`),
  CONSTRAINT `fk_contacts_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
INSERT INTO `contacts` VALUES (1,'CUS-1001','Marat Zhumabayev','Chief Metrology Inspector','m.zhuma@kaztransgas.kz','+7-7172-550101'),(2,'CUS-1001','Gulnara Sadykova','Procurement Director','g.sadyk@kaztransgas.kz','+7-7172-550102'),(3,'CUS-1002','Daulet Smagulov','SCADA Systems Architect','d.smagulov@samruk.kz','+7-7172-790201'),(4,'CUS-1003','Elena Kim','Operations Director','e.kim@kazzinc.com','+7-7232-291000'),(5,'CUS-1004','Oleg Morozov','Substation Automation Lead','o.morozov@kegoc.kz','+7-7172-690011'),(6,'CUS-1005','Yerbol Akhmetov','Drilling Equipment Lead','y.akhmetov@kazmunaigas.kz','+7-7172-786000');
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contracts`
--

DROP TABLE IF EXISTS `contracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contracts` (
  `contract_id` int(11) NOT NULL AUTO_INCREMENT,
  `cus_id` varchar(10) DEFAULT NULL,
  `prj_id` varchar(15) DEFAULT NULL,
  `opp_id` int(11) DEFAULT NULL,
  `contract_value` decimal(14,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `doc_id` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`contract_id`),
  KEY `fk_contracts_cus_id` (`cus_id`),
  KEY `fk_contracts_prj_id` (`prj_id`),
  KEY `fk_contracts_opp_id` (`opp_id`),
  KEY `fk_contracts_doc_id` (`doc_id`),
  CONSTRAINT `fk_contracts_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`),
  CONSTRAINT `fk_contracts_doc_id` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`),
  CONSTRAINT `fk_contracts_opp_id` FOREIGN KEY (`opp_id`) REFERENCES `opportunities` (`opp_id`),
  CONSTRAINT `fk_contracts_prj_id` FOREIGN KEY (`prj_id`) REFERENCES `projects` (`prj_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contracts`
--

LOCK TABLES `contracts` WRITE;
/*!40000 ALTER TABLE `contracts` DISABLE KEYS */;
INSERT INTO `contracts` VALUES (1,'CUS-1001','PRJ-2026-001',1,450000.00,'2026-01-15','2026-12-31','Active','DOC-2026-001'),(2,'CUS-1002','PRJ-2026-002',2,128000.00,'2026-02-01','2026-11-30','Active','DOC-2026-002'),(3,'CUS-1003','PRJ-2026-003',3,89000.00,'2026-03-10','2027-03-09','Active','DOC-2026-003'),(4,'CUS-1004','PRJ-2026-004',4,310000.00,'2026-04-01','2026-10-31','UnderReview','DOC-2026-004'),(5,'CUS-1005','PRJ-2026-005',5,520000.00,'2026-01-01','2026-12-31','Active','DOC-2026-005');
/*!40000 ALTER TABLE `contracts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_accounts`
--

DROP TABLE IF EXISTS `customer_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_accounts` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `cus_id` varchar(10) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `mfa_enabled` tinyint(1) DEFAULT 0,
  `status` varchar(20) DEFAULT 'Active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`account_id`),
  UNIQUE KEY `idx_cus_account_username` (`username`),
  KEY `idx_cus_id` (`cus_id`),
  CONSTRAINT `fk_customer_accounts_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_accounts`
--

LOCK TABLES `customer_accounts` WRITE;
/*!40000 ALTER TABLE `customer_accounts` DISABLE KEYS */;
INSERT INTO `customer_accounts` VALUES (1,'CUS-1001','sergei.makarov','s.makarov@aral-geomatics.kz','$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq',0,'Active',NULL,'2026-09-21 16:39:04'),(2,'CUS-1001','CLT-77210','client77210@vostokpribor.local','$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq',0,'Active',NULL,'2026-09-21 16:39:04'),(3,'CUS-1001','CUS-1001','cus1001@aral-geomatics.kz','$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq',0,'Active',NULL,'2026-09-21 16:39:04'),(4,'CUS-1002','kristaps.ozols','k.ozols@baltnord-systems.eu','$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq',0,'Active',NULL,'2026-09-21 16:39:04'),(5,'CUS-1002','SHP-VP-11','b2b-buyer11@baltnord.eu','$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq',0,'Active',NULL,'2026-09-21 16:39:04'),(6,'CUS-1002','CUS-1002','cus1002@baltnord.eu','$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq',0,'Active',NULL,'2026-09-21 16:39:04'),(7,'CUS-1003','yerlan.bektemis','y.bektemis@steppemining.kz','$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq',0,'Active',NULL,'2026-09-21 16:39:04'),(8,'CUS-1003','CUS-1003','cus1003@steppemining.kz','$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq',0,'Active',NULL,'2026-09-21 16:39:04'),(9,'CUS-1004','lukas.brandt','l.brandt@rheinwerk-inst.de','$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq',0,'Active',NULL,'2026-09-21 16:39:04'),(10,'CUS-1004','CUS-1004','cus1004@rheinwerk.de','$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq',0,'Active',NULL,'2026-09-21 16:39:04'),(11,'CUS-1005','dilshod.karim','d.karim@tashkent-precision.uz','$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq',0,'Active',NULL,'2026-09-21 16:39:04'),(12,'CUS-1005','CUS-1005','cus1005@tashkent-precision.uz','$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq',0,'Active',NULL,'2026-09-21 16:39:04'),(13,'CUS-1006','mara.kalnina','m.kalnina@daugava-optical.lv','$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq',0,'Active',NULL,'2026-09-21 16:39:04'),(14,'CUS-1007','murad.safarov','m.safarov@caspian-robotics.az','$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq',0,'Active',NULL,'2026-09-21 16:39:04'),(15,'CUS-1008','oleg.petrenko','o.petrenko@eurasia-water.ua','$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq',0,'Active',NULL,'2026-09-21 16:39:04'),(16,'CUS-1009','ainur.sadyk','a.sadyk@altai-env.kz','$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq',0,'Active',NULL,'2026-09-21 16:39:04'),(17,'CUS-1010','tomas.varga','t.varga@central-rail.hu','$2y$12$sjpWNnksb35C7D8re5tdt.qqOiKDZqw7J2lmGOVPoyRBT5bDpEzGq',0,'Active',NULL,'2026-09-21 16:39:04');
/*!40000 ALTER TABLE `customer_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_pricing`
--

DROP TABLE IF EXISTS `customer_pricing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_pricing` (
  `cus_id` varchar(10) NOT NULL,
  `prod_id` varchar(10) NOT NULL,
  `special_price` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`cus_id`,`prod_id`),
  KEY `fk_customer_pricing_prod_id` (`prod_id`),
  CONSTRAINT `fk_customer_pricing_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`),
  CONSTRAINT `fk_customer_pricing_prod_id` FOREIGN KEY (`prod_id`) REFERENCES `products` (`prod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_pricing`
--

LOCK TABLES `customer_pricing` WRITE;
/*!40000 ALTER TABLE `customer_pricing` DISABLE KEYS */;
INSERT INTO `customer_pricing` VALUES ('CUS-1001','PROD-1001',2250.00),('CUS-1001','PROD-1002',3100.00),('CUS-1002','PROD-1003',1850.00),('CUS-1003','PROD-1004',4200.00),('CUS-1004','PROD-1005',850.00),('CUS-1005','PROD-1001',2100.00);
/*!40000 ALTER TABLE `customer_pricing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `cus_id` varchar(10) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `primary_contact_name` varchar(150) DEFAULT NULL,
  `primary_contact_email` varchar(150) DEFAULT NULL,
  `account_manager_emp_id` varchar(10) DEFAULT NULL,
  `onboarded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cus_id`),
  KEY `fk_customers_account_manager_emp_id` (`account_manager_emp_id`),
  CONSTRAINT `fk_customers_account_manager_emp_id` FOREIGN KEY (`account_manager_emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES ('CUS-1001','Aral Geomatics Group','Surveying & GIS','Sergei Makarov',NULL,'EMP-1007','2026-09-19 07:35:40'),('CUS-1002','BaltNord Process Systems','Industrial Automation','Kristaps Ozols',NULL,'EMP-1010','2026-09-19 07:35:40'),('CUS-1003','Steppe Mining Technologies','Mining','Yerlan Bektemis',NULL,'EMP-1008','2026-09-19 07:35:40'),('CUS-1004','Rhein Werk Instrumentation','Industrial Measurement','Lukas Brandt',NULL,'EMP-1010','2026-09-19 07:35:40'),('CUS-1005','Tashkent Precision Controls','Manufacturing','Dilshod Karim',NULL,'EMP-1008','2026-09-19 07:35:40'),('CUS-1006','Daugava Optical Research','Optical Engineering','Mara Kalnina',NULL,'EMP-1007','2026-09-19 07:35:40'),('CUS-1007','Caspian Industrial Robotics','Robotics','Murad Safarov',NULL,'EMP-1009','2026-09-19 07:35:40'),('CUS-1008','Eurasia Water Automation','Water Infrastructure','Oleg Petrenko',NULL,'EMP-1009','2026-09-19 07:35:40'),('CUS-1009','Altai Environmental Systems','Environmental Monitoring','Ainur Sadyk',NULL,'EMP-1008','2026-09-19 07:35:40'),('CUS-1010','Central Rail Diagnostics','Railway Infrastructure','Tomas Varga',NULL,'EMP-1006','2026-09-19 07:35:40');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department_boards`
--

DROP TABLE IF EXISTS `department_boards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department_boards` (
  `board_post_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_code` varchar(4) DEFAULT NULL,
  `topic` varchar(200) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `posted_by_emp_id` varchar(10) DEFAULT NULL,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`board_post_id`),
  KEY `fk_department_boards_department_code` (`department_code`),
  KEY `fk_department_boards_posted_by_emp_id` (`posted_by_emp_id`),
  CONSTRAINT `fk_department_boards_department_code` FOREIGN KEY (`department_code`) REFERENCES `departments` (`dept_code`),
  CONSTRAINT `fk_department_boards_posted_by_emp_id` FOREIGN KEY (`posted_by_emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department_boards`
--

LOCK TABLES `department_boards` WRITE;
/*!40000 ALTER TABLE `department_boards` DISABLE KEYS */;
INSERT INTO `department_boards` VALUES (1,'ADM','Q3 Statutory Audit Protocol & Compliance Schedule','Executive briefing materials for National Accreditation Center audit are available on file server.','EMP-1005','2026-09-15 05:30:00'),(2,'ENG','SCADA Sensor Firmware v4.9.1 Rollout Notice','Optical latency patches must be applied to all Karaganda relays before end of month.','EMP-1002','2026-09-18 07:00:00'),(3,'IT','Scheduled Bastion SSH Key Rotation','All L3+ engineers are reminded to complete hardware token MFA re-enrollment.','EMP-1018','2026-09-20 11:00:00');
/*!40000 ALTER TABLE `department_boards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `dept_code` varchar(4) NOT NULL,
  `dept_name` varchar(100) NOT NULL,
  `main_function` text DEFAULT NULL,
  `employee_count_target` int(11) DEFAULT NULL,
  PRIMARY KEY (`dept_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES ('ENG','Engineering & Automation','Industrial Integration, Software, and Hardware Engineering',16),('EXE','Executive Management','Strategy, Governance, and Executive Approvals',5),('FIN','Finance & Billing','Billing, Payments, Accounting, and Reconciliation',10),('GOV','Governance, Risk & Compliance','Compliance, Audit, Risk Management, and Information Security',6),('HRA','Human Resources & Admin','Recruitment, Personnel Affairs, and Facilities',8),('ITD','Information Technology','Infrastructure, Support, Security, and System Management',14),('OPS','Operations & Logistics','Procurement, Inventory, Shipping, and Execution',20),('SAL','Sales & Commercial Affairs','Sales, Customer Management, Quotes, and Contracts',16);
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devices` (
  `device_id` int(11) NOT NULL AUTO_INCREMENT,
  `device_type` varchar(50) DEFAULT NULL,
  `hostname` varchar(150) DEFAULT NULL,
  `os_name` varchar(100) DEFAULT NULL,
  `department_code` varchar(4) DEFAULT NULL,
  `assigned_emp_id` varchar(10) DEFAULT NULL,
  `first_seen_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_seen_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(20) DEFAULT 'Active',
  PRIMARY KEY (`device_id`),
  KEY `fk_devices_department_code` (`department_code`),
  KEY `fk_devices_assigned_emp_id` (`assigned_emp_id`),
  CONSTRAINT `fk_devices_assigned_emp_id` FOREIGN KEY (`assigned_emp_id`) REFERENCES `employees` (`emp_id`),
  CONSTRAINT `fk_devices_department_code` FOREIGN KEY (`department_code`) REFERENCES `departments` (`dept_code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devices`
--

LOCK TABLES `devices` WRITE;
/*!40000 ALTER TABLE `devices` DISABLE KEYS */;
INSERT INTO `devices` VALUES (1,'Industrial Bastion Server','bastion-01.almaty.vostok.kz','Ubuntu 24.04 LTS (Kernel Hardened)','ADM','EMP-1005','2025-12-31 21:00:00','2026-09-23 09:00:00','Active'),(2,'SCADA Optical Relay Node','scada-gw-03.karaganda.vostok.kz','VxWorks 7.2 RTOS','ENG','EMP-1002','2026-01-10 05:30:00','2026-09-23 09:15:00','Active'),(3,'HSM Cryptographic Enclave','hsm-fips-140-3.almaty.vostok.kz','Thales Luna PCIe Firmware v7.8','IT','EMP-1004','2026-01-05 06:00:00','2026-09-23 09:20:00','Active'),(4,'Field Engineering Laptop','ws-field-1018.almaty.vostok.kz','Windows 11 Enterprise (Secured Core)','IT','EMP-1018','2026-02-15 07:00:00','2026-09-23 08:45:00','Active'),(5,'Warehouse Dispatch Terminal','stacker-04.asrs.ust-kam.vostok.kz','Debian 12 Industrial Embedded','PRD','EMP-1007','2026-03-01 04:00:00','2026-09-23 09:10:00','Active');
/*!40000 ALTER TABLE `devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_access_log`
--

DROP TABLE IF EXISTS `document_access_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_access_log` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` varchar(15) NOT NULL,
  `accessed_by_emp_id` varchar(10) DEFAULT NULL,
  `accessed_by_cus_id` varchar(10) DEFAULT NULL,
  `system_id` varchar(4) DEFAULT NULL,
  `device_id` int(11) DEFAULT NULL,
  `source_ip` varchar(45) DEFAULT NULL,
  `access_type` enum('View','Download','Edit','Delete') DEFAULT NULL,
  `success` tinyint(1) DEFAULT 1,
  `accessed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`access_id`),
  KEY `fk_document_access_log_doc_id` (`doc_id`),
  KEY `fk_document_access_log_accessed_by_emp_id` (`accessed_by_emp_id`),
  KEY `fk_document_access_log_accessed_by_cus_id` (`accessed_by_cus_id`),
  KEY `fk_document_access_log_system_id` (`system_id`),
  KEY `fk_document_access_log_device_id` (`device_id`),
  CONSTRAINT `fk_document_access_log_accessed_by_cus_id` FOREIGN KEY (`accessed_by_cus_id`) REFERENCES `customers` (`cus_id`),
  CONSTRAINT `fk_document_access_log_accessed_by_emp_id` FOREIGN KEY (`accessed_by_emp_id`) REFERENCES `employees` (`emp_id`),
  CONSTRAINT `fk_document_access_log_device_id` FOREIGN KEY (`device_id`) REFERENCES `devices` (`device_id`),
  CONSTRAINT `fk_document_access_log_doc_id` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`),
  CONSTRAINT `fk_document_access_log_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_access_log`
--

LOCK TABLES `document_access_log` WRITE;
/*!40000 ALTER TABLE `document_access_log` DISABLE KEYS */;
INSERT INTO `document_access_log` VALUES (1,'DOC-2026-001','EMP-1005',NULL,'ADM',1,'10.240.0.1','View',1,'2026-09-23 06:15:00'),(2,'DOC-2026-001',NULL,'CUS-1001','CUS',NULL,'195.189.12.44','Download',1,'2026-09-23 07:20:00'),(3,'DOC-2026-005','EMP-1004',NULL,'DEV',3,'10.240.1.44','View',1,'2026-09-23 08:05:00'),(4,'DOC-2026-002','EMP-1002',NULL,'ENG',2,'192.168.10.89','Edit',1,'2026-09-22 12:30:00');
/*!40000 ALTER TABLE `document_access_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_approvals`
--

DROP TABLE IF EXISTS `document_approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_approvals` (
  `approval_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` varchar(15) NOT NULL,
  `reviewer_emp_id` varchar(10) DEFAULT NULL,
  `decision` enum('Approved','Rejected','Pending') DEFAULT 'Pending',
  `decision_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`approval_id`),
  KEY `fk_document_approvals_doc_id` (`doc_id`),
  KEY `fk_document_approvals_reviewer_emp_id` (`reviewer_emp_id`),
  CONSTRAINT `fk_document_approvals_doc_id` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`),
  CONSTRAINT `fk_document_approvals_reviewer_emp_id` FOREIGN KEY (`reviewer_emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_approvals`
--

LOCK TABLES `document_approvals` WRITE;
/*!40000 ALTER TABLE `document_approvals` DISABLE KEYS */;
INSERT INTO `document_approvals` VALUES (1,'DOC-2026-001','EMP-1001','Approved','2026-01-15 13:00:00'),(2,'DOC-2026-002','EMP-1005','Approved','2026-02-02 08:30:00'),(3,'DOC-2026-004','EMP-1005','Pending','2026-09-20 06:00:00'),(4,'DOC-2026-005','EMP-1001','Approved','2026-01-02 07:00:00');
/*!40000 ALTER TABLE `document_approvals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_versions`
--

DROP TABLE IF EXISTS `document_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_versions` (
  `version_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` varchar(15) NOT NULL,
  `version_number` int(11) NOT NULL,
  `uploaded_by_emp_id` varchar(10) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`version_id`),
  KEY `fk_document_versions_doc_id` (`doc_id`),
  KEY `fk_document_versions_uploaded_by_emp_id` (`uploaded_by_emp_id`),
  CONSTRAINT `fk_document_versions_doc_id` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`),
  CONSTRAINT `fk_document_versions_uploaded_by_emp_id` FOREIGN KEY (`uploaded_by_emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_versions`
--

LOCK TABLES `document_versions` WRITE;
/*!40000 ALTER TABLE `document_versions` DISABLE KEYS */;
INSERT INTO `document_versions` VALUES (1,'DOC-2026-001',1,'EMP-1005','2026-01-10 11:00:00','/storage/docs/DOC-2026-001_v1.0.pdf'),(2,'DOC-2026-001',2,'EMP-1005','2026-01-15 13:30:00','/storage/docs/DOC-2026-001_v2.0_signed.pdf'),(3,'DOC-2026-002',1,'EMP-1002','2026-02-01 07:00:00','/storage/docs/DOC-2026-002_v1.0.pdf'),(4,'DOC-2026-005',1,'EMP-1004','2026-01-01 05:00:00','/storage/docs/DOC-2026-005_v1.0.pdf');
/*!40000 ALTER TABLE `document_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `doc_id` varchar(15) NOT NULL,
  `file_name` varchar(200) NOT NULL,
  `classification` enum('Public','Internal','Confidential','TopSecret') NOT NULL,
  `owning_system` varchar(50) DEFAULT NULL,
  `owner_emp_id` varchar(10) DEFAULT NULL,
  `related_prj_id` varchar(15) DEFAULT NULL,
  `related_cus_id` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`doc_id`),
  KEY `fk_documents_owner_emp_id` (`owner_emp_id`),
  KEY `fk_documents_related_prj_id` (`related_prj_id`),
  KEY `fk_documents_related_cus_id` (`related_cus_id`),
  CONSTRAINT `fk_documents_owner_emp_id` FOREIGN KEY (`owner_emp_id`) REFERENCES `employees` (`emp_id`),
  CONSTRAINT `fk_documents_related_cus_id` FOREIGN KEY (`related_cus_id`) REFERENCES `customers` (`cus_id`),
  CONSTRAINT `fk_documents_related_prj_id` FOREIGN KEY (`related_prj_id`) REFERENCES `projects` (`prj_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
INSERT INTO `documents` VALUES ('DOC-2026-001','Corporate_Information_Security_Policy.pdf','TopSecret','Admin & Governance','EMP-1005',NULL,NULL,'2026-09-21 16:36:10'),('DOC-2026-002','Customer_Onboarding_Standard.pdf','Confidential','CRM','EMP-1006',NULL,NULL,'2026-09-21 16:36:10'),('DOC-2026-003','PRJ-2026-001_Statement_of_Work.pdf','Confidential','File Center','EMP-1019','PRJ-2026-001','CUS-1001','2026-09-21 16:36:10'),('DOC-2026-004','PRJ-2026-002_Integration_Specification.pdf','TopSecret','File Center','EMP-1019','PRJ-2026-002','CUS-1002','2026-09-21 16:36:10'),('DOC-2026-005','INV-2026-002_Billing_Record.pdf','Confidential','Finance','EMP-1003','PRJ-2026-002','CUS-1002','2026-09-21 16:36:10'),('DOC-2026-006','Employee_Onboarding_Procedure.pdf','Confidential','HR','EMP-1005',NULL,NULL,'2026-09-21 16:36:10'),('DOC-2026-007','Employee_Access_Matrix.xlsx','TopSecret','Admin & Governance','EMP-1005',NULL,NULL,'2026-09-21 16:36:10'),('DOC-2026-008','Supplier_Evaluation_2026.pdf','Confidential','Operations','EMP-1013',NULL,NULL,'2026-09-21 16:36:10'),('DOC-2026-009','Optical_Sensor_Product_Catalog.pdf','Public','E-Commerce','EMP-1006',NULL,NULL,'2026-09-21 16:36:10'),('DOC-2026-010','API_Integration_Guide.pdf','Internal','Developer Portal','EMP-1020',NULL,NULL,'2026-09-21 16:36:10'),('DOC-2026-011','Disaster_Recovery_Plan.pdf','TopSecret','IT Helpdesk','EMP-1018',NULL,NULL,'2026-09-21 16:36:10'),('DOC-2026-012','Annual_Corporate_Budget_2026.xlsx','TopSecret','Finance','EMP-1003',NULL,NULL,'2026-09-21 16:36:10'),('DOC-2026-013','Customer_Service_Handbook.pdf','Internal','Intranet','EMP-1004',NULL,NULL,'2026-09-21 16:36:10'),('DOC-2026-014','PRJ-2026-007_Test_Report.pdf','Confidential','File Center','EMP-1019','PRJ-2026-007','CUS-1007','2026-09-21 16:36:10'),('DOC-2026-015','Board_Risk_Register_2026.xlsx','TopSecret','Admin & Governance','EMP-1005',NULL,NULL,'2026-09-21 16:36:10');
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_accounts`
--

DROP TABLE IF EXISTS `employee_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_accounts` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` varchar(10) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `mfa_enabled` tinyint(1) DEFAULT 0,
  `status` varchar(20) DEFAULT 'Active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`account_id`),
  UNIQUE KEY `idx_emp_account_username` (`username`),
  KEY `idx_emp_id` (`emp_id`),
  CONSTRAINT `fk_employee_accounts_emp_id` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_accounts`
--

LOCK TABLES `employee_accounts` WRITE;
/*!40000 ALTER TABLE `employee_accounts` DISABLE KEYS */;
INSERT INTO `employee_accounts` VALUES (1,'EMP-1001','viktor.sokolov','$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC',0,'Active',NULL,'2026-09-21 16:39:04'),(2,'EMP-1001','ADM-VP-01','$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC',0,'Active',NULL,'2026-09-21 16:39:04'),(3,'EMP-1001','EMP-1001','$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC',0,'Active',NULL,'2026-09-21 16:39:04'),(4,'EMP-1002','amina.karimova','$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC',0,'Active',NULL,'2026-09-21 16:39:04'),(5,'EMP-1002','HR-VP-201','$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC',0,'Active',NULL,'2026-09-21 16:39:04'),(6,'EMP-1002','HR-VP-104','$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC',0,'Active',NULL,'2026-09-21 16:39:04'),(7,'EMP-1002','EMP-1002','$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC',0,'Active',NULL,'2026-09-21 16:39:04'),(8,'EMP-1003','daniel.weber','$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC',0,'Active',NULL,'2026-09-21 16:39:04'),(9,'EMP-1003','FIN-VP-102','$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC',0,'Active',NULL,'2026-09-21 16:39:04'),(10,'EMP-1003','FIN-VP-502','$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC',0,'Active',NULL,'2026-09-21 16:39:04'),(11,'EMP-1003','EMP-1003','$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC',0,'Active',NULL,'2026-09-21 16:39:04'),(12,'EMP-1004','elena.morozova','$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC',0,'Active',NULL,'2026-09-21 16:39:04'),(13,'EMP-1004','EMP-1004','$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC',0,'Active',NULL,'2026-09-21 16:39:04'),(14,'EMP-1005','timur.akhmetov','$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC',0,'Active',NULL,'2026-09-21 16:39:04'),(15,'EMP-1005','EMP-1005','$2y$12$d5ZnZjG.UwfqQvfH.vDCp.1RFxhqL3iG5CWs10MtPqWaxn88qHzrC',0,'Active',NULL,'2026-09-21 16:39:04'),(16,'EMP-1006','pavel.orlov','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(17,'EMP-1006','CRM-VP-842','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(18,'EMP-1006','EMP-842','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(19,'EMP-1006','EMP-1006','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(20,'EMP-1007','sara.lindholm','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(21,'EMP-1007','EMP-1007','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(22,'EMP-1008','bekzod.rakhimov','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(23,'EMP-1008','EMP-1008','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(24,'EMP-1009','nadia.petrova','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(25,'EMP-1009','EMP-1009','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(26,'EMP-1010','markus.klein','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(27,'EMP-1010','EMP-1010','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(28,'EMP-1011','arman.tulegenov','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(29,'EMP-1011','EMP-1011','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(30,'EMP-1012','rustam.bekov','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(31,'EMP-1012','EMP-1012','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(32,'EMP-1013','ilona.vetra','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(33,'EMP-1013','EMP-1013','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(34,'EMP-1014','mikhail.antonov','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(35,'EMP-1014','EMP-1014','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(36,'EMP-1015','kamila.nurzhan','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(37,'EMP-1015','EMP-1015','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(38,'EMP-1016','erik.hansen','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(39,'EMP-1016','EMP-1016','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(40,'EMP-1017','dana.yermak','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(41,'EMP-1017','EMP-1017','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(42,'EMP-1018','leonid.volkov','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(43,'EMP-1018','IT-VP-304','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(44,'EMP-1018','EMP-1018','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(45,'EMP-1019','farida.iskakova','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(46,'EMP-1019','DOC-VP-501','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(47,'EMP-1019','CST-VP-09','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(48,'EMP-1019','EMP-VP-1019','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(49,'EMP-1019','EMP-1019','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(50,'EMP-1020','jonas.richter','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(51,'EMP-1020','DEV-VP-994','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(52,'EMP-1020','EMP-1020','$2y$12$3lgM9IJEMEqi.kwMKlBtwucD5oaUnUJAY3dt.eQlyjbXrQ6fkr/bW',0,'Active',NULL,'2026-09-21 16:39:04'),(53,'EMP-0001','admin@gmail.com','$2y$10$IvBRwu9FeDoyqTupw2q0De80dqn7k/QtkXO//kK9VYN7eTojeDenS',0,'Active','2026-09-21 17:36:16','2026-09-21 17:20:16'),(54,'EMP-0001','admin','$2y$10$IvBRwu9FeDoyqTupw2q0De80dqn7k/QtkXO//kK9VYN7eTojeDenS',0,'Active',NULL,'2026-09-21 17:20:16'),(55,'EMP-0001','EMP-0001','$2y$10$IvBRwu9FeDoyqTupw2q0De80dqn7k/QtkXO//kK9VYN7eTojeDenS',0,'Active',NULL,'2026-09-21 17:20:16'),(60,'EMP-1021','ahmad.iyad.ahmad.abunijim','$2y$10$LwAEoKt4zH6vXj4zvHp2SOu6zytn/pUGn6WEVjBv6UmSVIooswAIG',0,'Active',NULL,'2026-09-21 17:50:06'),(61,'EMP-1021','EMP-1021','$2y$10$LwAEoKt4zH6vXj4zvHp2SOu6zytn/pUGn6WEVjBv6UmSVIooswAIG',0,'Active',NULL,'2026-09-21 17:50:06');
/*!40000 ALTER TABLE `employee_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_offboarding`
--

DROP TABLE IF EXISTS `employee_offboarding`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_offboarding` (
  `offboarding_id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` varchar(10) NOT NULL,
  `step` enum('HRInitiated','StatusChanged','ITNotified','AccessRevoked','IntranetRevoked','FileCenterReviewed','CRMRevoked','HelpdeskClosed','GovernanceVerified','AuditLogged') NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`offboarding_id`),
  KEY `fk_employee_offboarding_emp_id` (`emp_id`),
  CONSTRAINT `fk_employee_offboarding_emp_id` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_offboarding`
--

LOCK TABLES `employee_offboarding` WRITE;
/*!40000 ALTER TABLE `employee_offboarding` DISABLE KEYS */;
INSERT INTO `employee_offboarding` VALUES (1,'EMP-1014','HRInitiated','Completed','2026-09-21 17:21:39'),(2,'EMP-1014','StatusChanged','Completed','2026-09-21 17:21:39'),(3,'EMP-1014','ITNotified','In Progress','2026-09-21 17:21:39'),(4,'EMP-1014','AccessRevoked','Pending','2026-09-21 17:21:39'),(5,'EMP-1014','IntranetRevoked','Pending','2026-09-21 17:21:39'),(6,'EMP-1014','FileCenterReviewed','Pending','2026-09-21 17:21:39'),(7,'EMP-1014','CRMRevoked','Pending','2026-09-21 17:21:39'),(8,'EMP-1014','HelpdeskClosed','Pending','2026-09-21 17:21:39'),(9,'EMP-1014','GovernanceVerified','Pending','2026-09-21 17:21:39'),(10,'EMP-1014','AuditLogged','Pending','2026-09-21 17:21:39');
/*!40000 ALTER TABLE `employee_offboarding` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_onboarding`
--

DROP TABLE IF EXISTS `employee_onboarding`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_onboarding` (
  `onboarding_id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` varchar(10) NOT NULL,
  `step` enum('RecordCreated','AccessRequested','IntranetGranted','SystemAccessGranted','DocumentsFiled','GovernanceReviewed') NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`onboarding_id`),
  KEY `fk_employee_onboarding_emp_id` (`emp_id`),
  CONSTRAINT `fk_employee_onboarding_emp_id` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_onboarding`
--

LOCK TABLES `employee_onboarding` WRITE;
/*!40000 ALTER TABLE `employee_onboarding` DISABLE KEYS */;
INSERT INTO `employee_onboarding` VALUES (1,'EMP-1020','RecordCreated','Completed','2026-09-21 17:21:39'),(2,'EMP-1020','AccessRequested','Completed','2026-09-21 17:21:39'),(3,'EMP-1020','IntranetGranted','Completed','2026-09-21 17:21:39'),(4,'EMP-1020','SystemAccessGranted','Completed','2026-09-21 17:36:55'),(5,'EMP-1020','DocumentsFiled','Pending','2026-09-21 17:21:39'),(6,'EMP-1020','GovernanceReviewed','Pending','2026-09-21 17:21:39'),(7,'EMP-1019','RecordCreated','Completed','2026-09-21 17:21:39'),(8,'EMP-1019','AccessRequested','Completed','2026-09-21 17:21:39'),(9,'EMP-1019','IntranetGranted','Completed','2026-09-21 17:21:39'),(10,'EMP-1019','SystemAccessGranted','Completed','2026-09-21 17:21:39'),(11,'EMP-1019','DocumentsFiled','Completed','2026-09-21 17:21:39'),(12,'EMP-1019','GovernanceReviewed','In Progress','2026-09-21 17:21:39'),(13,'EMP-1015','RecordCreated','Completed','2026-09-21 17:21:39'),(14,'EMP-1015','AccessRequested','In Progress','2026-09-21 17:21:39'),(15,'EMP-1015','IntranetGranted','Pending','2026-09-21 17:21:39'),(16,'EMP-1015','SystemAccessGranted','Pending','2026-09-21 17:21:39'),(17,'EMP-1015','DocumentsFiled','Pending','2026-09-21 17:21:39'),(18,'EMP-1015','GovernanceReviewed','Pending','2026-09-21 17:21:39'),(31,'EMP-1021','RecordCreated','Completed','2026-09-21 17:50:06'),(32,'EMP-1021','AccessRequested','In Progress','2026-09-21 17:50:06'),(33,'EMP-1021','IntranetGranted','Pending','2026-09-21 17:50:06'),(34,'EMP-1021','SystemAccessGranted','Pending','2026-09-21 17:50:06'),(35,'EMP-1021','DocumentsFiled','Pending','2026-09-21 17:50:06'),(36,'EMP-1021','GovernanceReviewed','Pending','2026-09-21 17:50:06');
/*!40000 ALTER TABLE `employee_onboarding` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_roles`
--

DROP TABLE IF EXISTS `employee_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_roles` (
  `emp_id` varchar(10) NOT NULL,
  `role_id` int(11) NOT NULL,
  `granted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `granted_by_emp_id` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`emp_id`,`role_id`),
  KEY `fk_employee_roles_role_id` (`role_id`),
  KEY `fk_employee_roles_granted_by_emp_id` (`granted_by_emp_id`),
  CONSTRAINT `fk_employee_roles_emp_id` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`),
  CONSTRAINT `fk_employee_roles_granted_by_emp_id` FOREIGN KEY (`granted_by_emp_id`) REFERENCES `employees` (`emp_id`),
  CONSTRAINT `fk_employee_roles_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_roles`
--

LOCK TABLES `employee_roles` WRITE;
/*!40000 ALTER TABLE `employee_roles` DISABLE KEYS */;
INSERT INTO `employee_roles` VALUES ('EMP-0001',1,'2026-09-21 17:20:16','EMP-0001'),('EMP-1001',1,'2026-09-21 16:36:11','EMP-1001'),('EMP-1002',7,'2026-09-21 16:36:11','EMP-1001'),('EMP-1003',6,'2026-09-21 16:36:11','EMP-1001'),('EMP-1004',4,'2026-09-21 16:36:11','EMP-1001'),('EMP-1005',2,'2026-09-21 16:36:11','EMP-1001'),('EMP-1006',3,'2026-09-21 16:36:11','EMP-1001'),('EMP-1007',3,'2026-09-21 16:36:11','EMP-1001'),('EMP-1008',3,'2026-09-21 16:36:11','EMP-1001'),('EMP-1009',3,'2026-09-21 16:36:11','EMP-1001'),('EMP-1010',3,'2026-09-21 16:36:11','EMP-1001'),('EMP-1011',8,'2026-09-21 16:36:11','EMP-1001'),('EMP-1012',8,'2026-09-21 16:36:11','EMP-1001'),('EMP-1013',8,'2026-09-21 16:36:11','EMP-1001'),('EMP-1014',8,'2026-09-21 16:36:11','EMP-1001'),('EMP-1015',8,'2026-09-21 16:36:11','EMP-1001'),('EMP-1016',4,'2026-09-21 16:36:11','EMP-1001'),('EMP-1017',4,'2026-09-21 16:36:11','EMP-1001'),('EMP-1018',5,'2026-09-21 16:36:11','EMP-1001'),('EMP-1019',4,'2026-09-21 16:36:11','EMP-1001'),('EMP-1020',4,'2026-09-21 16:36:11','EMP-1001'),('EMP-1021',7,'2026-09-21 17:50:06',NULL);
/*!40000 ALTER TABLE `employee_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `emp_id` varchar(10) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `job_title` varchar(150) DEFAULT NULL,
  `department_code` varchar(4) NOT NULL,
  `clearance_level` enum('L1','L2','L3','L4') NOT NULL,
  `email` varchar(150) NOT NULL,
  `manager_emp_id` varchar(10) DEFAULT NULL,
  `employment_status` enum('Active','OnLeave','Suspended','Terminated') DEFAULT 'Active',
  `hire_date` date DEFAULT NULL,
  PRIMARY KEY (`emp_id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_employees_department_code` (`department_code`),
  KEY `fk_employees_manager_emp_id` (`manager_emp_id`),
  CONSTRAINT `fk_employees_department_code` FOREIGN KEY (`department_code`) REFERENCES `departments` (`dept_code`),
  CONSTRAINT `fk_employees_manager_emp_id` FOREIGN KEY (`manager_emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES ('EMP-0001','System Administrator','Executive SuperAdmin','EXE','L4','admin@gmail.com',NULL,'Active','2026-09-21'),('EMP-1001','Viktor Sokolov','Chief Executive Officer (CEO)','EXE','L4','viktor.sokolov@vostokpribor.local',NULL,'Active',NULL),('EMP-1002','Amina Karimova','Chief Operating Officer (COO)','EXE','L4','amina.karimova@vostokpribor.local','EMP-1001','Active',NULL),('EMP-1003','Daniel Weber','Chief Financial Officer (CFO)','EXE','L4','daniel.weber@vostokpribor.local','EMP-1001','Active',NULL),('EMP-1004','Elena Morozova','Chief Technology Officer (CTO)','EXE','L4','elena.morozova@vostokpribor.local','EMP-1001','Active',NULL),('EMP-1005','Timur Akhmetov','Chief Governance Officer','EXE','L4','timur.akhmetov@vostokpribor.local','EMP-1001','Active',NULL),('EMP-1006','Pavel Orlov','Sales Director','SAL','L3','pavel.orlov@vostokpribor.local','EMP-1001','Active',NULL),('EMP-1007','Sara Lindholm','Senior Account Manager','SAL','L3','sara.lindholm@vostokpribor.local','EMP-1006','Active',NULL),('EMP-1008','Bekzod Rakhimov','Account Manager','SAL','L3','bekzod.rakhimov@vostokpribor.local','EMP-1006','Active',NULL),('EMP-1009','Nadia Petrova','Strategic Sales Manager','SAL','L3','nadia.petrova@vostokpribor.local','EMP-1006','Active',NULL),('EMP-1010','Markus Klein','Regional Sales Manager','SAL','L3','markus.klein@vostokpribor.local','EMP-1006','Active',NULL),('EMP-1011','Arman Tulegenov','Operations Manager','OPS','L3','arman.tulegenov@vostokpribor.local','EMP-1002','Active',NULL),('EMP-1012','Rustam Bekov','Logistics Manager','OPS','L3','rustam.bekov@vostokpribor.local','EMP-1011','Active',NULL),('EMP-1013','Ilona Vetra','Procurement Manager','OPS','L3','ilona.vetra@vostokpribor.local','EMP-1011','Active',NULL),('EMP-1014','Mikhail Antonov','Warehouse Supervisor','OPS','L2','mikhail.antonov@vostokpribor.local','EMP-1011','Active',NULL),('EMP-1015','Kamila Nurzhan','Supply Chain Analyst','OPS','L2','kamila.nurzhan@vostokpribor.local','EMP-1011','Active',NULL),('EMP-1016','Erik Hansen','Senior Automation Engineer','ENG','L3','erik.hansen@vostokpribor.local','EMP-1004','Active',NULL),('EMP-1017','Dana Yermak','Software Integration Engineer','ENG','L3','dana.yermak@vostokpribor.local','EMP-1004','Active',NULL),('EMP-1018','Leonid Volkov','Systems Engineer','ENG','L3','leonid.volkov@vostokpribor.local','EMP-1004','Active',NULL),('EMP-1019','Farida Iskakova','Project Manager','ENG','L3','farida.iskakova@vostokpribor.local','EMP-1004','Active',NULL),('EMP-1020','Jonas Richter','Senior Developer','ENG','L3','jonas.richter@vostokpribor.local','EMP-1004','Active',NULL),('EMP-1021','Ahmad Iyad Ahmad AbuNijim','Full Stack Developer','HRA','L4','nijim.ahmad077@gmail.com','EMP-0001','Active','2026-09-21');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `integration_logs`
--

DROP TABLE IF EXISTS `integration_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_logs` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) DEFAULT NULL,
  `endpoint` varchar(200) DEFAULT NULL,
  `request_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `response_status` int(11) DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `fk_integration_logs_partner_id` (`partner_id`),
  CONSTRAINT `fk_integration_logs_partner_id` FOREIGN KEY (`partner_id`) REFERENCES `api_partners` (`partner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `integration_logs`
--

LOCK TABLES `integration_logs` WRITE;
/*!40000 ALTER TABLE `integration_logs` DISABLE KEYS */;
INSERT INTO `integration_logs` VALUES (1,1,'https://api.vostokpribor.kz/telemetry/v1','2026-09-23 07:00:00',200),(2,2,'https://api.vostokpribor.kz/crm/v1/sync','2026-09-23 07:30:00',200),(3,3,'https://api.vostokpribor.kz/b2b/orders/v1','2026-09-23 08:00:00',200);
/*!40000 ALTER TABLE `integration_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `integration_requirements`
--

DROP TABLE IF EXISTS `integration_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_requirements` (
  `req_id` int(11) NOT NULL AUTO_INCREMENT,
  `cus_id` varchar(10) DEFAULT NULL,
  `prj_id` varchar(15) DEFAULT NULL,
  `reviewed_by_emp_id` varchar(10) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(30) DEFAULT 'UnderReview',
  PRIMARY KEY (`req_id`),
  KEY `fk_integration_requirements_cus_id` (`cus_id`),
  KEY `fk_integration_requirements_prj_id` (`prj_id`),
  KEY `fk_integration_requirements_reviewed_by_emp_id` (`reviewed_by_emp_id`),
  CONSTRAINT `fk_integration_requirements_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`),
  CONSTRAINT `fk_integration_requirements_prj_id` FOREIGN KEY (`prj_id`) REFERENCES `projects` (`prj_id`),
  CONSTRAINT `fk_integration_requirements_reviewed_by_emp_id` FOREIGN KEY (`reviewed_by_emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `integration_requirements`
--

LOCK TABLES `integration_requirements` WRITE;
/*!40000 ALTER TABLE `integration_requirements` DISABLE KEYS */;
INSERT INTO `integration_requirements` VALUES (1,'CUS-1001','PRJ-2026-001','EMP-1002','Bidirectional Modbus TCP to REST JSON Gateway with AES-256 GCM hardware encryption.','Approved'),(2,'CUS-1002','PRJ-2026-002','EMP-1004','Continuous real-time telemetry streaming to Samruk Energy central monitoring station.','InDevelopment'),(3,'CUS-1005','PRJ-2026-005','EMP-1018','Automated replenishment dispatch orders triggered when pressure threshold crosses safety margin.','UnderReview');
/*!40000 ALTER TABLE `integration_requirements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `internal_policies`
--

DROP TABLE IF EXISTS `internal_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `internal_policies` (
  `policy_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `doc_id` varchar(15) DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  PRIMARY KEY (`policy_id`),
  KEY `fk_internal_policies_doc_id` (`doc_id`),
  CONSTRAINT `fk_internal_policies_doc_id` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `internal_policies`
--

LOCK TABLES `internal_policies` WRITE;
/*!40000 ALTER TABLE `internal_policies` DISABLE KEYS */;
INSERT INTO `internal_policies` VALUES (1,'Enterprise Clean Desk & Credential Protection Standard','DOC-2026-001','2026-01-01'),(2,'High-Precision Measurement Instrument Calibration Standard','DOC-2026-005','2026-03-15'),(3,'Employee Travel & Overseas Per Diem Regulations','DOC-2026-006','2026-02-01'),(4,'Emergency Facility Evacuation & Industrial Safety Protocol','DOC-2026-012','2025-11-01'),(5,'Intellectual Property & Industrial Patent Filing Standard','DOC-2026-013','2026-04-10');
/*!40000 ALTER TABLE `internal_policies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `inv_id` varchar(15) NOT NULL,
  `cus_id` varchar(10) NOT NULL,
  `prj_id` varchar(15) DEFAULT NULL,
  `total_value` decimal(14,2) NOT NULL,
  `currency` char(3) DEFAULT 'EUR',
  `payment_status` enum('Paid','Pending','Overdue') NOT NULL,
  `issued_at` date DEFAULT NULL,
  `paid_at` date DEFAULT NULL,
  PRIMARY KEY (`inv_id`),
  KEY `fk_invoices_cus_id` (`cus_id`),
  KEY `fk_invoices_prj_id` (`prj_id`),
  CONSTRAINT `fk_invoices_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`),
  CONSTRAINT `fk_invoices_prj_id` FOREIGN KEY (`prj_id`) REFERENCES `projects` (`prj_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES ('INV-2026-001','CUS-1001','PRJ-2026-001',46250.00,'EUR','Paid',NULL,NULL),('INV-2026-002','CUS-1002','PRJ-2026-002',80000.00,'EUR','Pending',NULL,NULL),('INV-2026-003','CUS-1003','PRJ-2026-003',137500.00,'EUR','Paid',NULL,NULL),('INV-2026-004','CUS-1004','PRJ-2026-004',55000.00,'EUR','Pending',NULL,NULL),('INV-2026-005','CUS-1005','PRJ-2026-005',42000.00,'EUR','Paid',NULL,NULL),('INV-2026-006','CUS-1006','PRJ-2026-006',32000.00,'EUR','Pending',NULL,NULL),('INV-2026-007','CUS-1007','PRJ-2026-007',105000.00,'EUR','Paid',NULL,NULL),('INV-2026-008','CUS-1008','PRJ-2026-008',68333.00,'EUR','Pending',NULL,NULL),('INV-2026-009','CUS-1009','PRJ-2026-012',47333.00,'EUR','Paid',NULL,NULL),('INV-2026-010','CUS-1010','PRJ-2026-010',91667.00,'EUR','Pending',NULL,NULL);
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ip_addresses`
--

DROP TABLE IF EXISTS `ip_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip_addresses` (
  `ip_id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `device_id` int(11) DEFAULT NULL,
  `is_internal` tinyint(1) DEFAULT 1,
  `geo_country` varchar(100) DEFAULT NULL,
  `first_seen_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_seen_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ip_id`),
  KEY `fk_ip_addresses_device_id` (`device_id`),
  CONSTRAINT `fk_ip_addresses_device_id` FOREIGN KEY (`device_id`) REFERENCES `devices` (`device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ip_addresses`
--

LOCK TABLES `ip_addresses` WRITE;
/*!40000 ALTER TABLE `ip_addresses` DISABLE KEYS */;
INSERT INTO `ip_addresses` VALUES (1,'10.240.0.1',1,1,'Kazakhstan','2025-12-31 21:00:00','2026-09-23 09:00:00'),(2,'10.240.1.44',3,1,'Kazakhstan','2026-01-05 06:00:00','2026-09-23 09:20:00'),(3,'10.240.2.18',4,1,'Kazakhstan','2026-02-15 07:00:00','2026-09-23 08:45:00'),(4,'192.168.10.89',2,1,'Kazakhstan','2026-01-10 05:30:00','2026-09-23 09:15:00'),(5,'10.240.5.12',5,1,'Kazakhstan','2026-03-01 04:00:00','2026-09-23 09:10:00');
/*!40000 ALTER TABLE `ip_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `it_assets`
--

DROP TABLE IF EXISTS `it_assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `it_assets` (
  `asset_id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` varchar(10) DEFAULT NULL,
  `department_code` varchar(4) DEFAULT NULL,
  `system_id` varchar(4) DEFAULT NULL,
  `asset_type` varchar(100) DEFAULT NULL,
  `hostname` varchar(150) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `operating_system` varchar(100) DEFAULT NULL,
  `os_version` varchar(50) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `assigned_date` date DEFAULT NULL,
  `criticality` varchar(20) DEFAULT NULL,
  `environment` varchar(20) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Active',
  `last_seen_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`asset_id`),
  UNIQUE KEY `serial_number` (`serial_number`),
  KEY `fk_it_assets_emp_id` (`emp_id`),
  KEY `fk_it_assets_department_code` (`department_code`),
  KEY `fk_it_assets_system_id` (`system_id`),
  CONSTRAINT `fk_it_assets_department_code` FOREIGN KEY (`department_code`) REFERENCES `departments` (`dept_code`),
  CONSTRAINT `fk_it_assets_emp_id` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`),
  CONSTRAINT `fk_it_assets_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `it_assets`
--

LOCK TABLES `it_assets` WRITE;
/*!40000 ALTER TABLE `it_assets` DISABLE KEYS */;
INSERT INTO `it_assets` VALUES (1,'EMP-1005','ADM','ADM','Server','gov-core-node-01.vostok.local','10.240.0.1','RHEL 9.3','9.3','SRV-VP-2026-001','2026-01-01','Critical','Production','Active','2026-09-23 09:00:00'),(2,'EMP-1004','IT','DEV','Hardware Security Module','hsm-cluster-alpha.vostok.local','10.240.1.44','Thales LunaOS','7.8.2','HSM-FIPS-0992','2026-01-05','Critical','Production','Active','2026-09-23 09:20:00'),(3,'EMP-1002','ENG','SHP','Industrial SCADA Gateway','scada-plc-gw-01.vostok.local','192.168.10.89','VxWorks RTOS','7.2','SCADA-VP-003','2026-01-10','High','Production','Active','2026-09-23 09:15:00'),(4,'EMP-1018','IT','IT','Engineering Workstation','ws-sec-ops-1018.vostok.local','10.240.2.18','Windows 11 Pro','23H2','WS-VP-2026-018','2026-02-15','Medium','Production','Active','2026-09-23 08:45:00');
/*!40000 ALTER TABLE `it_assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_postings`
--

DROP TABLE IF EXISTS `job_postings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_postings` (
  `posting_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) DEFAULT NULL,
  `department_code` varchar(4) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 1,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`posting_id`),
  KEY `fk_job_postings_department_code` (`department_code`),
  CONSTRAINT `fk_job_postings_department_code` FOREIGN KEY (`department_code`) REFERENCES `departments` (`dept_code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_postings`
--

LOCK TABLES `job_postings` WRITE;
/*!40000 ALTER TABLE `job_postings` DISABLE KEYS */;
INSERT INTO `job_postings` VALUES (1,'Senior Industrial SCADA Engineer','ENG',1,'2026-09-01 06:00:00'),(2,'Metrology Metrologist & Pressure Calibration Specialist','ENG',1,'2026-09-05 07:00:00'),(3,'B2B Technical Sales Account Manager','SAL',1,'2026-09-10 08:00:00'),(4,'Cybersecurity & Governance Auditor (ST RK 27001)','ADM',1,'2026-09-12 11:00:00');
/*!40000 ALTER TABLE `job_postings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `knowledge_base_articles`
--

DROP TABLE IF EXISTS `knowledge_base_articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `knowledge_base_articles` (
  `kb_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `created_by_emp_id` varchar(10) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`kb_id`),
  KEY `fk_knowledge_base_articles_created_by_emp_id` (`created_by_emp_id`),
  CONSTRAINT `fk_knowledge_base_articles_created_by_emp_id` FOREIGN KEY (`created_by_emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `knowledge_base_articles`
--

LOCK TABLES `knowledge_base_articles` WRITE;
/*!40000 ALTER TABLE `knowledge_base_articles` DISABLE KEYS */;
INSERT INTO `knowledge_base_articles` VALUES (1,'Standard WireGuard VPN Tunnel Setup for Field Calibration Teams','Detailed guide for establishing secure encrypted WireGuard links from field measurement stations to Almaty Central Telemetry.','Network Security','EMP-1018','2026-08-15 07:00:00'),(2,'FIDO2 Hardware Token Enrollment & Emergency Recovery Runbook','Instructions for enrolling YubiKey FIDO2 tokens for L2/L3 operations and verifying zero-trust biometric signatures.','Identity & Access','EMP-1004','2026-09-01 11:30:00'),(3,'SCADA Modbus TCP/IP Telemetry Packet Capture Procedure','Diagnostic steps for taking packet dumps on industrial flow measurement nodes without introducing jitter or latency.','Industrial Telemetry','EMP-1017','2026-09-10 13:00:00');
/*!40000 ALTER TABLE `knowledge_base_articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leads` (
  `lead_id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`lead_id`),
  KEY `fk_leads_assigned_sales_emp_id` (`assigned_sales_emp_id`),
  KEY `fk_leads_converted_cus_id` (`converted_cus_id`),
  CONSTRAINT `fk_leads_assigned_sales_emp_id` FOREIGN KEY (`assigned_sales_emp_id`) REFERENCES `employees` (`emp_id`),
  CONSTRAINT `fk_leads_converted_cus_id` FOREIGN KEY (`converted_cus_id`) REFERENCES `customers` (`cus_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads`
--

LOCK TABLES `leads` WRITE;
/*!40000 ALTER TABLE `leads` DISABLE KEYS */;
INSERT INTO `leads` VALUES (1,'Nursultan Kadyrov','n.kadyrov@kaztransgas.kz','+7 (717) 255-8801','KazTransGas Distribution','Requesting commercial quote for 40 units of Metrotec-500 Flow Analyzers.','Corporate Web','Qualified','EMP-1007',NULL,'2026-09-21 18:13:25'),(2,'Olga Demidova','o.demidova@severstal.ru','+7 (820) 256-4422','Severstal Metallurgy PJSC','Follow-up regarding proposal for furnace pressure differential gauges.','Direct Referral','Qualified','EMP-1006',NULL,'2026-09-21 18:13:25'),(3,'Yerbol Sadykov','yerbol@bogatyr.kz','+7 (718) 722-1144','Bogatyr Coal Mining','Seeking vibration analysis telemetry sensors for open-pit excavators.','B2B Shop','New','EMP-1008',NULL,'2026-09-21 18:13:25'),(4,'Alexander Weber','a.weber@basf-caspian.de','+49 621 60-0','BASF Caspian Petrochemical','Inquiry on corrosive chemical immersion probes with ATEX Zone 0 proofing.','API Portal','Qualified','EMP-1010',NULL,'2026-09-21 18:13:25'),(5,'Marat Tazhin','m.tazhin@almatypower.kz','+7 (727) 299-3300','Almaty Energy Consortium','Grid monitoring instrumentation replacement for Substation 220kV East.','Corporate Web','New','EMP-1009',NULL,'2026-09-21 18:13:25');
/*!40000 ALTER TABLE `leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_requests`
--

DROP TABLE IF EXISTS `leave_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leave_requests` (
  `leave_id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` varchar(10) NOT NULL,
  `leave_type` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `approved_by_emp_id` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`leave_id`),
  KEY `fk_leave_requests_emp_id` (`emp_id`),
  KEY `fk_leave_requests_approved_by_emp_id` (`approved_by_emp_id`),
  CONSTRAINT `fk_leave_requests_approved_by_emp_id` FOREIGN KEY (`approved_by_emp_id`) REFERENCES `employees` (`emp_id`),
  CONSTRAINT `fk_leave_requests_emp_id` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_requests`
--

LOCK TABLES `leave_requests` WRITE;
/*!40000 ALTER TABLE `leave_requests` DISABLE KEYS */;
INSERT INTO `leave_requests` VALUES (1,'EMP-1007','Annual Leave','2026-10-01','2026-10-08','Pending',NULL),(2,'EMP-1008','Sick Leave','2026-09-22','2026-09-25','Pending',NULL),(3,'EMP-1016','Paternity Leave','2026-10-15','2026-10-30','Rejected','EMP-0001'),(4,'EMP-1012','Annual Leave','2026-08-10','2026-08-17','Approved','EMP-1002'),(5,'EMP-1009','Study Leave','2026-09-01','2026-09-05','Approved','EMP-1006'),(6,'EMP-1013','Personal Leave','2026-07-14','2026-07-16','Approved','EMP-1002'),(7,'EMP-1017','Annual Leave','2026-06-01','2026-06-12','Approved','EMP-1004');
/*!40000 ALTER TABLE `leave_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opportunities`
--

DROP TABLE IF EXISTS `opportunities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `opportunities` (
  `opp_id` int(11) NOT NULL AUTO_INCREMENT,
  `lead_id` int(11) DEFAULT NULL,
  `cus_id` varchar(10) DEFAULT NULL,
  `sales_emp_id` varchar(10) DEFAULT NULL,
  `stage` enum('Qualification','Proposal','Negotiation','Won','Lost') DEFAULT 'Qualification',
  `estimated_value` decimal(14,2) DEFAULT NULL,
  `expected_close_date` date DEFAULT NULL,
  PRIMARY KEY (`opp_id`),
  KEY `fk_opportunities_lead_id` (`lead_id`),
  KEY `fk_opportunities_cus_id` (`cus_id`),
  KEY `fk_opportunities_sales_emp_id` (`sales_emp_id`),
  CONSTRAINT `fk_opportunities_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`),
  CONSTRAINT `fk_opportunities_lead_id` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`lead_id`),
  CONSTRAINT `fk_opportunities_sales_emp_id` FOREIGN KEY (`sales_emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opportunities`
--

LOCK TABLES `opportunities` WRITE;
/*!40000 ALTER TABLE `opportunities` DISABLE KEYS */;
INSERT INTO `opportunities` VALUES (1,1,'CUS-1001','EMP-1007','Negotiation',450000.00,'2026-10-15'),(2,2,'CUS-1003','EMP-1006','Proposal',820000.00,'2026-11-01'),(3,3,'CUS-1005','EMP-1008','',1250000.00,'2026-12-20'),(4,4,'CUS-1006','EMP-1010','',390000.00,'2026-09-30'),(5,5,'CUS-1010','EMP-1009','Negotiation',640000.00,'2026-10-31');
/*!40000 ALTER TABLE `opportunities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `prod_id` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `fk_order_items_order_id` (`order_id`),
  KEY `fk_order_items_prod_id` (`prod_id`),
  CONSTRAINT `fk_order_items_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  CONSTRAINT `fk_order_items_prod_id` FOREIGN KEY (`prod_id`) REFERENCES `products` (`prod_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,'PROD-1001',5,12500.00),(2,1,'PROD-1002',10,8250.00),(3,2,'PROD-1003',8,18500.00),(4,2,'PROD-1004',15,10000.00),(5,3,'PROD-1005',20,22500.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `cus_id` varchar(10) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(30) DEFAULT 'Cart',
  `total_amount` decimal(14,2) DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `fk_orders_cus_id` (`cus_id`),
  CONSTRAINT `fk_orders_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'CUS-1001','2026-09-15 05:30:00','Processing',145000.00),(2,'CUS-1003','2026-09-18 08:20:00','Shipped',298000.00),(3,'CUS-1005','2026-09-20 11:10:00','Delivered',450000.00),(4,'CUS-1006','2026-09-10 13:45:00','Delivered',85000.00),(5,'CUS-1010','2026-09-21 06:00:00','Processing',192000.00);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `inv_id` varchar(15) NOT NULL,
  `amount` decimal(14,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `reconciled` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`payment_id`),
  KEY `fk_payments_inv_id` (`inv_id`),
  CONSTRAINT `fk_payments_inv_id` FOREIGN KEY (`inv_id`) REFERENCES `invoices` (`inv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,'INV-2026-001',125000.00,'2026-09-12','Bank Swift Transfer',1),(2,'INV-2026-002',88400.00,'2026-09-14','Corporate Wire',1),(3,'INV-2026-003',340000.00,'2026-09-17','Letter of Credit',1),(4,'INV-2026-004',54200.00,'2026-09-19','Direct Clearing',1);
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(150) DEFAULT NULL,
  `system_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'ACCESS_ADMIN_GOVERNANCE','Admin & Governance Portal'),(2,'EXECUTE_BREAK_GLASS','Admin & Governance Portal'),(3,'INITIATE_LOCKDOWN','Admin & Governance Portal'),(4,'VIEW_CRM_CUSTOMERS','CRM System'),(5,'MANAGE_CRM_DEALS','CRM System'),(6,'VIEW_CUSTOMER_DOCUMENTS','Customer Portal'),(7,'DOWNLOAD_INVOICES','Customer Portal'),(8,'ACCESS_DEV_SANDBOX','Developer Portal'),(9,'GENERATE_API_KEYS','Developer Portal'),(10,'VIEW_EMPLOYEE_DIRECTORY','Employee Intranet'),(11,'ACCESS_FILE_CENTER','File Center'),(12,'UPLOAD_TECHNICAL_DOCS','File Center'),(13,'APPROVE_BUDGETS','Finance & Billing'),(14,'PROCESS_PAYMENTS','Finance & Billing'),(15,'MANAGE_EMPLOYEES','HR System'),(16,'MANAGE_ONBOARDING','HR System'),(17,'VIEW_SUPPORT_TICKETS','IT Helpdesk'),(18,'RESOLVE_INCIDENTS','IT Helpdesk'),(19,'PLACE_B2B_ORDERS','Online Shop B2B'),(20,'VIEW_CATALOG_PRICING','Online Shop B2B'),(21,'VIEW_CORPORATE_PORTAL','Corporate Web Platform');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portal_accounts`
--

DROP TABLE IF EXISTS `portal_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portal_accounts` (
  `portal_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `cus_id` varchar(10) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `mfa_enabled` tinyint(1) DEFAULT 0,
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`portal_user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `fk_portal_accounts_cus_id` (`cus_id`),
  CONSTRAINT `fk_portal_accounts_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portal_accounts`
--

LOCK TABLES `portal_accounts` WRITE;
/*!40000 ALTER TABLE `portal_accounts` DISABLE KEYS */;
INSERT INTO `portal_accounts` VALUES (1,'CUS-1001','m.zhuma','m.zhuma@kaztransgas.kz',1,'2026-09-23 07:15:00'),(2,'CUS-1002','d.smagulov','d.smagulov@samruk.kz',1,'2026-09-23 06:30:00'),(3,'CUS-1003','e.kim','e.kim@kazzinc.com',0,'2026-09-22 11:00:00'),(4,'CUS-1004','o.morozov','o.morozov@kegoc.kz',1,'2026-09-21 13:45:00'),(5,'CUS-1005','y.akhmetov','y.akhmetov@kazmunaigas.kz',1,'2026-09-23 08:20:00');
/*!40000 ALTER TABLE `portal_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portal_notifications`
--

DROP TABLE IF EXISTS `portal_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portal_notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `portal_user_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `related_entity_type` varchar(30) DEFAULT NULL,
  `related_entity_id` varchar(15) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`notification_id`),
  KEY `fk_portal_notifications_portal_user_id` (`portal_user_id`),
  CONSTRAINT `fk_portal_notifications_portal_user_id` FOREIGN KEY (`portal_user_id`) REFERENCES `portal_accounts` (`portal_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portal_notifications`
--

LOCK TABLES `portal_notifications` WRITE;
/*!40000 ALTER TABLE `portal_notifications` DISABLE KEYS */;
INSERT INTO `portal_notifications` VALUES (1,1,'New Calibration Certificate DOC-2026-001 is now available for download.','Document','DOC-2026-001',0,'2026-09-23 05:00:00'),(2,2,'Invoice INV-2026-002 has been generated and scheduled for payment.','Invoice','INV-2026-002',1,'2026-09-22 06:00:00'),(3,5,'Your bulk order ORD-2026-104 has been dispatched via Trans-Kazakhstan freight.','Order','ORD-2026-104',0,'2026-09-23 08:00:00');
/*!40000 ALTER TABLE `portal_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portal_sessions`
--

DROP TABLE IF EXISTS `portal_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portal_sessions` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `portal_user_id` int(11) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `logout_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip_address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  KEY `fk_portal_sessions_portal_user_id` (`portal_user_id`),
  CONSTRAINT `fk_portal_sessions_portal_user_id` FOREIGN KEY (`portal_user_id`) REFERENCES `portal_accounts` (`portal_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portal_sessions`
--

LOCK TABLES `portal_sessions` WRITE;
/*!40000 ALTER TABLE `portal_sessions` DISABLE KEYS */;
INSERT INTO `portal_sessions` VALUES (1,1,'2026-09-23 07:15:00','2026-09-23 08:45:00','195.189.12.44'),(2,2,'2026-09-23 06:30:00','2026-09-23 07:15:00','212.154.200.18'),(3,5,'2026-09-23 08:20:00','2026-09-23 09:00:00','89.218.45.67');
/*!40000 ALTER TABLE `portal_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_inventory`
--

DROP TABLE IF EXISTS `product_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_inventory` (
  `prod_id` varchar(10) NOT NULL,
  `warehouse_location` varchar(100) DEFAULT NULL,
  `quantity_on_hand` int(11) DEFAULT 0,
  `reorder_level` int(11) DEFAULT 0,
  PRIMARY KEY (`prod_id`),
  CONSTRAINT `fk_product_inventory_prod_id` FOREIGN KEY (`prod_id`) REFERENCES `products` (`prod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_inventory`
--

LOCK TABLES `product_inventory` WRITE;
/*!40000 ALTER TABLE `product_inventory` DISABLE KEYS */;
INSERT INTO `product_inventory` VALUES ('PROD-1001','WH-North-A1',45,15),('PROD-1002','WH-Central-B3',57,15),('PROD-1003','WH-East-C2',69,15),('PROD-1004','WH-South-D4',81,15),('PROD-1005','WH-North-A1',93,15),('PROD-1006','WH-Central-B3',105,15),('PROD-1007','WH-East-C2',117,15),('PROD-1008','WH-South-D4',129,15),('PROD-1009','WH-North-A1',141,15),('PROD-1010','WH-Central-B3',153,15);
/*!40000 ALTER TABLE `product_inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `prod_id` varchar(10) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `billing_model` enum('PerUnit','PerProject','SubscriptionMonthly','SubscriptionAnnual','AnnualContract') NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`prod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES ('PROD-1001','Industrial Optical Sensor Package','PerUnit',NULL),('PROD-1002','Precision Geodetic Measurement Kit','PerUnit',NULL),('PROD-1003','Automated Calibration Station','PerProject',NULL),('PROD-1004','Industrial PLC Integration','PerProject',NULL),('PROD-1005','Remote Monitoring Gateway','PerUnit',NULL),('PROD-1006','Optical Inspection System','PerProject',NULL),('PROD-1007','Industrial Lifecycle Support','SubscriptionAnnual',NULL),('PROD-1008','Automation Software Integration','PerProject',NULL),('PROD-1009','Enterprise Logistics Management','SubscriptionMonthly',NULL),('PROD-1010','Preventive Instrument Maintenance','AnnualContract',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `prj_id` varchar(15) NOT NULL,
  `cus_id` varchar(10) NOT NULL,
  `project_manager_emp_id` varchar(10) DEFAULT NULL,
  `budget` decimal(14,2) DEFAULT NULL,
  `currency` char(3) DEFAULT 'EUR',
  `status` enum('Planning','Procurement','Design','Integration','Testing','Execution','ContractReview','Maintenance','Closed') DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`prj_id`),
  KEY `fk_projects_cus_id` (`cus_id`),
  KEY `fk_projects_project_manager_emp_id` (`project_manager_emp_id`),
  CONSTRAINT `fk_projects_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`),
  CONSTRAINT `fk_projects_project_manager_emp_id` FOREIGN KEY (`project_manager_emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES ('PRJ-2026-001','CUS-1001','EMP-1019',185000.00,'EUR','Execution',NULL,NULL),('PRJ-2026-002','CUS-1002','EMP-1019',240000.00,'EUR','Integration',NULL,NULL),('PRJ-2026-003','CUS-1003','EMP-1016',410000.00,'EUR','Procurement',NULL,NULL),('PRJ-2026-004','CUS-1004','EMP-1019',165000.00,'EUR','Execution',NULL,NULL),('PRJ-2026-005','CUS-1005','EMP-1017',128000.00,'EUR','Integration',NULL,NULL),('PRJ-2026-006','CUS-1006','EMP-1016',96000.00,'EUR','Design',NULL,NULL),('PRJ-2026-007','CUS-1007','EMP-1019',315000.00,'EUR','Integration',NULL,NULL),('PRJ-2026-008','CUS-1008','EMP-1017',205000.00,'EUR','Execution',NULL,NULL),('PRJ-2026-009','CUS-1001','EMP-1019',275000.00,'EUR','Design',NULL,NULL),('PRJ-2026-010','CUS-1002','EMP-1017',74000.00,'EUR','ContractReview',NULL,NULL),('PRJ-2026-011','CUS-1005','EMP-1016',188000.00,'EUR','Testing',NULL,NULL),('PRJ-2026-012','CUS-1009','EMP-1016',142000.00,'EUR','Maintenance',NULL,NULL),('PRJ-2026-013','CUS-1010','EMP-1019',112000.00,'EUR','Procurement',NULL,NULL),('PRJ-2026-014','CUS-1007','EMP-1017',260000.00,'EUR','Procurement',NULL,NULL),('PRJ-2026-015','CUS-1010','EMP-1016',151000.00,'EUR','Planning',NULL,NULL);
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quotes`
--

DROP TABLE IF EXISTS `quotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quotes` (
  `quote_id` int(11) NOT NULL AUTO_INCREMENT,
  `cus_id` varchar(10) NOT NULL,
  `prod_id` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(12,2) DEFAULT NULL,
  `created_by_emp_id` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`quote_id`),
  KEY `fk_quotes_cus_id` (`cus_id`),
  KEY `fk_quotes_prod_id` (`prod_id`),
  KEY `fk_quotes_created_by_emp_id` (`created_by_emp_id`),
  CONSTRAINT `fk_quotes_created_by_emp_id` FOREIGN KEY (`created_by_emp_id`) REFERENCES `employees` (`emp_id`),
  CONSTRAINT `fk_quotes_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`),
  CONSTRAINT `fk_quotes_prod_id` FOREIGN KEY (`prod_id`) REFERENCES `products` (`prod_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quotes`
--

LOCK TABLES `quotes` WRITE;
/*!40000 ALTER TABLE `quotes` DISABLE KEYS */;
INSERT INTO `quotes` VALUES (1,'CUS-1001','PROD-1001',25,2350.00,'EMP-1006','2026-09-18 07:00:00'),(2,'CUS-1002','PROD-1003',10,1900.00,'EMP-1006','2026-09-19 11:30:00'),(3,'CUS-1004','PROD-1002',15,3200.00,'EMP-1006','2026-09-21 08:00:00');
/*!40000 ALTER TABLE `quotes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recruitment_candidates`
--

DROP TABLE IF EXISTS `recruitment_candidates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recruitment_candidates` (
  `candidate_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(150) DEFAULT NULL,
  `applied_position` varchar(150) DEFAULT NULL,
  `department_code` varchar(4) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Applied',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`candidate_id`),
  KEY `fk_recruitment_candidates_department_code` (`department_code`),
  CONSTRAINT `fk_recruitment_candidates_department_code` FOREIGN KEY (`department_code`) REFERENCES `departments` (`dept_code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recruitment_candidates`
--

LOCK TABLES `recruitment_candidates` WRITE;
/*!40000 ALTER TABLE `recruitment_candidates` DISABLE KEYS */;
INSERT INTO `recruitment_candidates` VALUES (1,'Almas Berikov','Senior Industrial SCADA Engineer','ENG','InterviewScheduled','2026-09-15 07:30:00'),(2,'Saule Ospanova','Metrology Metrologist','ENG','UnderReview','2026-09-18 08:15:00'),(3,'Dmitry Pavlov','Cybersecurity & Governance Auditor','ADM','Shortlisted','2026-09-20 13:00:00');
/*!40000 ALTER TABLE `recruitment_candidates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_register`
--

DROP TABLE IF EXISTS `risk_register`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_register` (
  `risk_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` text DEFAULT NULL,
  `likelihood` varchar(20) DEFAULT NULL,
  `impact` varchar(20) DEFAULT NULL,
  `owner_emp_id` varchar(10) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Open',
  `review_date` date DEFAULT NULL,
  PRIMARY KEY (`risk_id`),
  KEY `fk_risk_register_owner_emp_id` (`owner_emp_id`),
  CONSTRAINT `fk_risk_register_owner_emp_id` FOREIGN KEY (`owner_emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_register`
--

LOCK TABLES `risk_register` WRITE;
/*!40000 ALTER TABLE `risk_register` DISABLE KEYS */;
INSERT INTO `risk_register` VALUES (1,'Critical semiconductor lead time volatility for precision pressure transducers','Medium','High','EMP-1011','Mitigated','2026-10-01'),(2,'Industrial SCADA optical diode firmware integrity attestation','Low','Critical','EMP-1004','UnderReview','2026-10-15'),(3,'Trans-Caspian multimodal freight transit route bottlenecks','Medium','Medium','EMP-1012','Mitigated','2026-11-01'),(4,'Harmonization between Eurasian GOST and European IEC metrology standards','Low','Medium','EMP-1005','Accepted','2026-12-01'),(5,'Cross-Site SCADA Bridge Latency Spike on SYS-01 / SYS-05 Ingestion Link','High','Critical','EMP-1005','ActionRequired','2026-10-20'),(6,'Orphaned Service Account & RSA Key in AS/RS Warehouse System (SYS-07)','Medium','High','EMP-1018','ActionRequired','2026-10-25'),(7,'HSM Cryptographic Root Key Attestation Drift on SYS-02 Key Vault','Low','Critical','EMP-1002','UnderReview','2026-11-05'),(8,'Dual-Custody Governance Quorum Failure Contingency Protocol','Low','High','EMP-1001','Accepted','2026-11-15'),(9,'B2B Procurement API Webhook Buffer Saturation on Trans-Kazakhstan Backbone','Medium','Medium','EMP-1020','Mitigated','2026-12-01'),(10,'Calibration Certificate Expiry on Ekibastuz Power Substation High-Voltage Relays','Medium','High','EMP-1007','UnderReview','2026-12-10');
/*!40000 ALTER TABLE `risk_register` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `fk_role_permissions_permission_id` (`permission_id`),
  CONSTRAINT `fk_role_permissions_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`),
  CONSTRAINT `fk_role_permissions_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10),(1,11),(1,12),(1,13),(1,14),(1,15),(1,16),(1,17),(1,18),(1,19),(1,20),(1,21),(2,4),(2,5),(2,10),(2,19),(3,8),(3,9),(3,10),(4,10),(4,13),(4,14),(5,10),(5,15),(5,16),(6,10),(6,17),(6,18),(7,1),(7,2),(7,3),(7,10),(7,11);
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_system_access`
--

DROP TABLE IF EXISTS `role_system_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_system_access` (
  `role_id` int(11) NOT NULL,
  `system_id` varchar(4) NOT NULL,
  `access_level` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`role_id`,`system_id`),
  KEY `fk_role_system_access_system_id` (`system_id`),
  CONSTRAINT `fk_role_system_access_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  CONSTRAINT `fk_role_system_access_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_system_access`
--

LOCK TABLES `role_system_access` WRITE;
/*!40000 ALTER TABLE `role_system_access` DISABLE KEYS */;
INSERT INTO `role_system_access` VALUES (1,'ADM','Full'),(1,'CRM','Full'),(1,'CUS','Full'),(1,'DEV','Full'),(1,'DOC','Full'),(1,'EMP','Full'),(1,'FIN','Full'),(1,'HR','Full'),(1,'IT','Full'),(1,'SHP','Full'),(1,'WEB','Full'),(2,'ADM','Full'),(2,'DOC','Full'),(2,'EMP','Full'),(2,'FIN','Audit'),(2,'HR','Audit'),(3,'CRM','Full'),(3,'CUS','Supervise'),(3,'DOC','ReadWrite'),(3,'EMP','Read'),(3,'SHP','Full'),(4,'DEV','Full'),(4,'DOC','ReadWrite'),(4,'EMP','Read'),(4,'IT','Full'),(4,'WEB','ReadWrite'),(5,'ADM','Telemetry'),(5,'DEV','ReadWrite'),(5,'DOC','ReadWrite'),(5,'EMP','Read'),(5,'IT','Full'),(6,'ADM','Audit'),(6,'CRM','Read'),(6,'DOC','ReadWrite'),(6,'EMP','Read'),(6,'FIN','Full'),(7,'ADM','Read'),(7,'DOC','ReadWrite'),(7,'EMP','Full'),(7,'HR','Full'),(8,'DOC','ReadWrite'),(8,'EMP','Read'),(8,'FIN','Read'),(8,'SHP','Full'),(9,'CUS','Full'),(9,'SHP','Full'),(9,'WEB','Public');
/*!40000 ALTER TABLE `role_system_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Executive SuperAdmin','Full administrative authority and governance oversight across all 11 VOSTOKPRIBOR systems'),(2,'Chief Governance Officer','Compliance, legal audits, executive risk registries and policy oversight'),(3,'Sales Director & Manager','CRM pipeline oversight, B2B quotes, enterprise client accounts and order approval'),(4,'Senior Automation & Developer','Engineering codebase, API developer portal, telemetry and system integrations'),(5,'Systems Engineer & IT Support','Infrastructure management, IT Helpdesk ticketing, device telemetry, network security'),(6,'Chief Financial Officer & Controller','Invoices, enterprise billing cycles, audits, and payment records'),(7,'HR Director & Operations','Personnel records, department assignments, onboarding, payroll compliance'),(8,'Logistics & Supply Chain Specialist','Warehouse inventory, product catalog, delivery telemetry and procurement'),(9,'Customer Client Account','Access to Customer Portal, project tracking, ticket creation, B2B purchasing');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_forecasts`
--

DROP TABLE IF EXISTS `sales_forecasts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales_forecasts` (
  `forecast_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_emp_id` varchar(10) DEFAULT NULL,
  `period` varchar(20) DEFAULT NULL,
  `forecast_amount` decimal(14,2) DEFAULT NULL,
  `actual_amount` decimal(14,2) DEFAULT NULL,
  PRIMARY KEY (`forecast_id`),
  KEY `fk_sales_forecasts_sales_emp_id` (`sales_emp_id`),
  CONSTRAINT `fk_sales_forecasts_sales_emp_id` FOREIGN KEY (`sales_emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_forecasts`
--

LOCK TABLES `sales_forecasts` WRITE;
/*!40000 ALTER TABLE `sales_forecasts` DISABLE KEYS */;
INSERT INTO `sales_forecasts` VALUES (1,'EMP-1006','2026-Q1',450000.00,482000.00),(2,'EMP-1006','2026-Q2',550000.00,530000.00),(3,'EMP-1006','2026-Q3',600000.00,615000.00),(4,'EMP-1006','2026-Q4',700000.00,NULL);
/*!40000 ALTER TABLE `sales_forecasts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `security_events`
--

DROP TABLE IF EXISTS `security_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `security_events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `reported_to_governance` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`event_id`),
  KEY `fk_security_events_source_system_id` (`source_system_id`),
  KEY `fk_security_events_source_device_id` (`source_device_id`),
  KEY `fk_security_events_source_ip_id` (`source_ip_id`),
  KEY `fk_security_events_actor_emp_id` (`actor_emp_id`),
  KEY `fk_security_events_actor_customer_id` (`actor_customer_id`),
  KEY `fk_security_events_target_device_id` (`target_device_id`),
  KEY `fk_security_events_related_tkt_id` (`related_tkt_id`),
  CONSTRAINT `fk_security_events_actor_customer_id` FOREIGN KEY (`actor_customer_id`) REFERENCES `customers` (`cus_id`),
  CONSTRAINT `fk_security_events_actor_emp_id` FOREIGN KEY (`actor_emp_id`) REFERENCES `employees` (`emp_id`),
  CONSTRAINT `fk_security_events_related_tkt_id` FOREIGN KEY (`related_tkt_id`) REFERENCES `tickets` (`tkt_id`),
  CONSTRAINT `fk_security_events_source_device_id` FOREIGN KEY (`source_device_id`) REFERENCES `devices` (`device_id`),
  CONSTRAINT `fk_security_events_source_ip_id` FOREIGN KEY (`source_ip_id`) REFERENCES `ip_addresses` (`ip_id`),
  CONSTRAINT `fk_security_events_source_system_id` FOREIGN KEY (`source_system_id`) REFERENCES `systems_catalog` (`system_id`),
  CONSTRAINT `fk_security_events_target_device_id` FOREIGN KEY (`target_device_id`) REFERENCES `devices` (`device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_events`
--

LOCK TABLES `security_events` WRITE;
/*!40000 ALTER TABLE `security_events` DISABLE KEYS */;
INSERT INTO `security_events` VALUES (1,'ORPHAN_IDENTIFIER_DETECTED','Industrial SCADA Enclave','CUS',NULL,NULL,'EMP-1009',NULL,NULL,NULL,'Vendor contract expired 14 days ago. High-privilege RSA SSH-key persists inside CNC SCADA Gateway.','Critical','2026-09-21 08:22:45','{\"key_fingerprint\":\"SHA256:7mP0w...k9Qx\",\"node\":\"CNC-03\"}','Quarantined',NULL,1),(2,'DEFCON_POSTURE_ATTESTATION','Gov Core Hub','ADM',NULL,NULL,'EMP-1005',NULL,NULL,NULL,'Normal operations DEFCON-4 posture attested by Dual-Custody signers (EMP-1001 & EMP-1005).','Low','2026-09-21 06:14:22','{\"defcon_level\":4,\"posture\":\"NORMAL_OPS\"}','Resolved',NULL,1),(3,'BREAK_GLASS_ELEVATION','Infrastructure Ops','IT',NULL,NULL,'EMP-1018',NULL,NULL,NULL,'Emergency telemetry elevation token issued for Almaty Station grid sub-station 04 maintenance.','High','2026-09-21 10:40:10','{\"authorized_by\":\"EMP-1005\",\"token_id\":\"BG-2026-018\"}','Active',NULL,1),(4,'SCADA_INGESTION_LATENCY_JITTER','Industrial Bus Relay','SHP',NULL,NULL,'EMP-1002',NULL,NULL,NULL,'Optical sensor latency jitter exceeded 45ms threshold on Ingestion Bridge 03.','Medium','2026-09-21 09:05:00','{\"node\":\"INGEST-03\",\"jitter_ms\":52}','Investigating',NULL,1);
/*!40000 ALTER TABLE `security_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `security_incidents`
--

DROP TABLE IF EXISTS `security_incidents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `security_incidents` (
  `incident_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `severity` enum('Low','Medium','High','Critical') DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Open',
  `reported_by_emp_id` varchar(10) DEFAULT NULL,
  `assigned_to_emp_id` varchar(10) DEFAULT NULL,
  `opened_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `closed_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`incident_id`),
  KEY `fk_security_incidents_reported_by_emp_id` (`reported_by_emp_id`),
  KEY `fk_security_incidents_assigned_to_emp_id` (`assigned_to_emp_id`),
  CONSTRAINT `fk_security_incidents_assigned_to_emp_id` FOREIGN KEY (`assigned_to_emp_id`) REFERENCES `employees` (`emp_id`),
  CONSTRAINT `fk_security_incidents_reported_by_emp_id` FOREIGN KEY (`reported_by_emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_incidents`
--

LOCK TABLES `security_incidents` WRITE;
/*!40000 ALTER TABLE `security_incidents` DISABLE KEYS */;
INSERT INTO `security_incidents` VALUES (1,'SCADA CNC Gateway Token Leak & Orphan Account (EMP-1009)','Vendor contract expired 14 days ago. High-privilege RSA SSH-key persisted inside CNC SCADA Gateway. Remediation desk token purge triggered.','Critical','Contained','EMP-1005','EMP-1004','2026-09-20 11:15:00','2026-09-23 12:46:47'),(2,'Ingestion Node 07 Shymkent Buffer Desync','Packet retransmission spikes on optical sensor telemetry queue between Shymkent Depot and Central Databus.','Medium','Resolved','EMP-1007','EMP-1018','2026-09-18 06:30:00','2026-09-18 08:45:00'),(3,'Emergency Break-Glass Audit: Sub-Station 04 Power Grid','Scheduled high-voltage optical relay calibration requiring temporary L5 telemetry elevation.','High','Closed','EMP-1001','EMP-1005','2026-09-14 23:00:00','2026-09-15 00:30:00');
/*!40000 ALTER TABLE `security_incidents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `security_policies`
--

DROP TABLE IF EXISTS `security_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `security_policies` (
  `policy_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` varchar(15) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  PRIMARY KEY (`policy_id`),
  KEY `fk_security_policies_doc_id` (`doc_id`),
  CONSTRAINT `fk_security_policies_doc_id` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_policies`
--

LOCK TABLES `security_policies` WRITE;
/*!40000 ALTER TABLE `security_policies` DISABLE KEYS */;
INSERT INTO `security_policies` VALUES (1,'DOC-2026-005','Cryptographic Key Rotation & HSM Policy (FIPS 140-3)','2026-01-01'),(2,'DOC-2026-006','Air-Gapped SCADA Network Isolation & Telemetry Standards','2026-02-15'),(3,'DOC-2026-012','Zero-Trust Multi-Factor Authentication & Identity Lifecycle','2026-03-01'),(4,'DOC-2026-013','Executive Break-Glass Emergency Authorization Protocol','2026-01-10'),(5,'DOC-2026-001','Statutory Data Sovereignty & Audit Retention Framework','2026-01-01');
/*!40000 ALTER TABLE `security_policies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sla_policies`
--

DROP TABLE IF EXISTS `sla_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sla_policies` (
  `sla_id` int(11) NOT NULL AUTO_INCREMENT,
  `priority` enum('Low','Medium','High','Critical') DEFAULT NULL,
  `response_time_hours` int(11) DEFAULT NULL,
  `resolution_time_hours` int(11) DEFAULT NULL,
  PRIMARY KEY (`sla_id`),
  UNIQUE KEY `priority` (`priority`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sla_policies`
--

LOCK TABLES `sla_policies` WRITE;
/*!40000 ALTER TABLE `sla_policies` DISABLE KEYS */;
INSERT INTO `sla_policies` VALUES (1,'Low',24,72),(2,'Medium',8,24),(3,'High',2,8),(4,'Critical',1,2);
/*!40000 ALTER TABLE `sla_policies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_integration_logs`
--

DROP TABLE IF EXISTS `system_integration_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_integration_logs` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `link_code` varchar(20) NOT NULL,
  `source_system_id` varchar(10) NOT NULL,
  `target_system_id` varchar(10) NOT NULL,
  `api_protocol` varchar(100) NOT NULL,
  `endpoint` varchar(255) NOT NULL,
  `payload_summary` text DEFAULT NULL,
  `direction` varchar(30) NOT NULL,
  `status_code` int(11) NOT NULL DEFAULT 200,
  `actor_id` varchar(50) DEFAULT 'SYSTEM',
  `executed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `link_code` (`link_code`),
  KEY `source_system_id` (`source_system_id`),
  KEY `target_system_id` (`target_system_id`),
  KEY `executed_at` (`executed_at`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_integration_logs`
--

LOCK TABLES `system_integration_logs` WRITE;
/*!40000 ALTER TABLE `system_integration_logs` DISABLE KEYS */;
INSERT INTO `system_integration_logs` VALUES (1,'SYS01_TO_SYS02','ADM','CRM','REST / JSON HTTPS','/api/integrations/SYS01_TO_SYS02/dispatch','Governance directive dispatched from SYS01 (ADM) to SYS02 (CRM). Action: TEST_SYNC | Payload: {\"test_mode\":true,\"system_check\":\"OK\"}','Outbound (SYS01 ?????? SYS02)',200,'admin@gmail.com','2026-09-21 15:07:57'),(2,'SYS01_TO_SYS02','ADM','CRM','REST / JSON HTTPS','/api/integrations/SYS01_TO_SYS02/dispatch','Governance directive dispatched from SYS01 (ADM) to SYS02 (CRM). Action: UNIT_TEST_DISPATCH','Outbound (SYS01 ?????? SYS02)',200,'admin@gmail.com','2026-09-21 15:08:13'),(3,'SYS02_TO_SYS03','CRM','CUS','REST / JSON HTTPS','/api/integrations/SYS02_TO_SYS03/dispatch','Customer account and SLA profile synchronized from SYS02 (CRM) to SYS03 (CUS). Action: UNIT_TEST_DISPATCH','Outbound (SYS02 ?????? SYS03)',200,'admin@gmail.com','2026-09-21 15:08:14'),(4,'SYS02_TO_SYS05','CRM','EMP','REST Event Bus / PubSub','/api/integrations/SYS02_TO_SYS05/dispatch','Sales benchmark notification published from SYS02 (CRM) to SYS05 (EMP Intranet). Action: UNIT_TEST_DISPATCH','Outbound (SYS02 ?????? SYS05)',200,'admin@gmail.com','2026-09-21 15:08:14'),(5,'SYS03_TO_SYS05','CUS','EMP','REST Webhook / JSON','/api/integrations/SYS03_TO_SYS05/dispatch','Customer support escalation routed from SYS03 (CUS) to SYS05 (EMP Intranet). Action: UNIT_TEST_DISPATCH','Outbound (SYS03 ?????? SYS05)',200,'admin@gmail.com','2026-09-21 15:08:14'),(6,'SYS05_TO_SYS06','EMP','DOC','Document Ingestion REST API','/api/integrations/SYS05_TO_SYS06/dispatch','Internal corporate document ingested from SYS05 (EMP) to SYS06 (DOC File Center). Action: UNIT_TEST_DISPATCH','Outbound (SYS05 ?????? SYS06)',200,'admin@gmail.com','2026-09-21 15:08:14'),(7,'SYS05_TO_SYS07','EMP','FIN','REST JSON RPC','/api/integrations/SYS05_TO_SYS07/dispatch','Expense claim & requisition dispatched from SYS05 (EMP) to SYS07 (FIN Finance & Billing). Action: UNIT_TEST_DISPATCH','Outbound (SYS05 ?????? SYS07)',200,'admin@gmail.com','2026-09-21 15:08:14'),(8,'SYS04_TO_SYS06','DEV','DOC','OpenAPI Auto-Sync REST','/api/integrations/SYS04_TO_SYS06/dispatch','Technical OpenAPI specification published from SYS04 (DEV) to SYS06 (DOC). Action: UNIT_TEST_DISPATCH','Outbound (SYS04 ?????? SYS06)',200,'admin@gmail.com','2026-09-21 15:08:14'),(9,'SYS04_TO_SYS08','DEV','HR','REST JSON Webhook','/api/integrations/SYS04_TO_SYS08/dispatch','Engineering candidate technical score submitted from SYS04 (DEV) to SYS08 (HR). Action: UNIT_TEST_DISPATCH','Outbound (SYS04 ?????? SYS08)',200,'admin@gmail.com','2026-09-21 15:08:14'),(10,'SYS04_TO_SYS09','DEV','IT','Syslog / REST Webhook','/api/integrations/SYS04_TO_SYS09/dispatch','Automated telemetry incident ticket generated from SYS04 (DEV) to SYS09 (IT Helpdesk). Action: UNIT_TEST_DISPATCH','Outbound (SYS04 ?????? SYS09)',200,'admin@gmail.com','2026-09-21 15:08:14'),(11,'SYS10_TO_SYS02','SHP','CRM','REST / HTTPS JSON','/api/integrations/SYS10_TO_SYS02/dispatch','B2B commercial wholesale RFQ transmitted from SYS10 (SHP) to SYS02 (CRM). Action: UNIT_TEST_DISPATCH','Outbound (SYS10 ?????? SYS02)',200,'admin@gmail.com','2026-09-21 15:08:14'),(12,'SYS10_TO_SYS05','SHP','EMP','REST Event Bus / Webhook','/api/integrations/SYS10_TO_SYS05/dispatch','Inventory threshold alert broadcast from SYS10 (SHP) to SYS05 (EMP Intranet). Action: UNIT_TEST_DISPATCH','Outbound (SYS10 ?????? SYS05)',200,'admin@gmail.com','2026-09-21 15:08:14'),(13,'SYS11_TO_ALL','WEB','ALL','Universal Gateway Router','/api/integrations/SYS11_TO_ALL/dispatch','Universal Corporate Platform announcement broadcast from SYS11 (WEB) to ALL subsystems (SYS01-SYS10). Action: UNIT_TEST_DISPATCH','Broadcast (SYS11 ?????? ALL)',200,'admin@gmail.com','2026-09-21 15:08:14');
/*!40000 ALTER TABLE `system_integration_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_integrations`
--

DROP TABLE IF EXISTS `system_integrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_integrations` (
  `integration_id` int(11) NOT NULL AUTO_INCREMENT,
  `link_code` varchar(20) NOT NULL,
  `source_system_id` varchar(10) NOT NULL,
  `target_system_id` varchar(10) NOT NULL,
  `api_protocol` varchar(100) NOT NULL,
  `authentication_method` varchar(100) NOT NULL,
  `data_exchanged` text NOT NULL,
  `direction` varchar(30) NOT NULL DEFAULT 'Outbound',
  `required_clearance` enum('L1','L2','L3','L4') NOT NULL DEFAULT 'L2',
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`integration_id`),
  UNIQUE KEY `link_code` (`link_code`),
  KEY `source_system_id` (`source_system_id`),
  KEY `target_system_id` (`target_system_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_integrations`
--

LOCK TABLES `system_integrations` WRITE;
/*!40000 ALTER TABLE `system_integrations` DISABLE KEYS */;
INSERT INTO `system_integrations` VALUES (1,'SYS01_TO_SYS02','ADM','CRM','REST / JSON HTTPS','Mutual HMAC-SHA256 & L4 SuperAdmin','Executive Governance Policies, Audit Compliance Flags, Enterprise Client Oversight Directives','Outbound (SYS01 ??? SYS02)','L3','Active','2026-09-23 12:46:33'),(2,'SYS02_TO_SYS03','CRM','CUS','REST / JSON HTTPS','SSO Session Token & API Key','Customer Accounts, SLA Tiers, Billing Terms, Project Milestones & Contracts','Outbound (SYS02 ??? SYS03)','L2','Active','2026-09-23 12:46:33'),(3,'SYS02_TO_SYS05','CRM','EMP','REST Event Bus / PubSub','SSO Session Token','Sales Performance Metrics, Department Target Announcements, Major Client Deal Wins','Outbound (SYS02 ??? SYS05)','L2','Active','2026-09-23 12:46:33'),(4,'SYS03_TO_SYS05','CUS','EMP','REST Webhook / JSON','Customer Auth Token & SSO','Customer Support Escalations, Feedback Inquiries, Internal Department Dispatch','Outbound (SYS03 ??? SYS05)','L1','Active','2026-09-23 12:46:33'),(5,'SYS05_TO_SYS06','EMP','DOC','Document Ingestion REST API','SSO Session Token','Internal Corporate Policies, Employee Form Submissions, Compliance Manuals, Archived Memos','Outbound (SYS05 ??? SYS06)','L1','Active','2026-09-23 12:46:33'),(6,'SYS05_TO_SYS07','EMP','FIN','REST JSON RPC','Clearance Gated Session (L2+)','Employee Expense Claims, Departmental Budget Requisitions, Operational Travel Invoices','Outbound (SYS05 ??? SYS07)','L2','Active','2026-09-23 12:46:33'),(7,'SYS04_TO_SYS06','DEV','DOC','OpenAPI Auto-Sync REST','Bearer API Key','API Technical Specs, SDK Documentation, Industrial Telemetry Architecture Schematics','Outbound (SYS04 ??? SYS06)','L2','Active','2026-09-23 12:46:33'),(8,'SYS04_TO_SYS08','DEV','HR','REST JSON Webhook','SecOps HMAC Token','Technical Skills Assessment, Developer Candidate Profiles, Engineering Protocol Certifications','Outbound (SYS04 ??? SYS08)','L3','Active','2026-09-23 12:46:33'),(9,'SYS04_TO_SYS09','DEV','IT','Syslog / REST Webhook','Bearer API Token','Telemetry Alerts, Infrastructure Error Logs, Continuous Deployment Incident Tickets','Outbound (SYS04 ??? SYS09)','L2','Active','2026-09-23 12:46:33'),(10,'SYS10_TO_SYS02','SHP','CRM','REST / HTTPS JSON','Storefront API Token','High-Value Purchase Leads, Commercial Accounts, Customer RFQ Inquiries','Outbound (SYS10 ??? SYS02)','L2','Active','2026-09-23 12:46:33'),(11,'SYS10_TO_SYS05','SHP','EMP','REST Event Bus / Webhook','System SSO Token','Warehouse Inventory Depletion Warnings, High-Priority B2B Order Notifications','Outbound (SYS10 ??? SYS05)','L2','Active','2026-09-23 12:46:33'),(12,'SYS11_TO_ALL','WEB','ALL','Universal Gateway Router','Universal SSO Cookie & Public Token','Public Visitor Contact Inquiries, Press Releases, SSO Launchpad Cross-System Routing','Broadcast (SYS11 ??? ALL)','L1','Active','2026-09-23 12:46:33');
/*!40000 ALTER TABLE `system_integrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `systems_catalog`
--

DROP TABLE IF EXISTS `systems_catalog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `systems_catalog` (
  `system_id` varchar(4) NOT NULL,
  `system_name` varchar(100) DEFAULT NULL,
  `fqdn` varchar(100) DEFAULT NULL,
  `criticality` varchar(30) DEFAULT NULL,
  `trust_zone` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`system_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `systems_catalog`
--

LOCK TABLES `systems_catalog` WRITE;
/*!40000 ALTER TABLE `systems_catalog` DISABLE KEYS */;
INSERT INTO `systems_catalog` VALUES ('ADM','Admin & Governance Portal','admin.vostokpribor.local','MissionCritical','Zone-Alpha'),('CRM','CRM System','crm.vostokpribor.local','High','Zone-Bravo'),('CUS','Customer Portal','customer.vostokpribor.local','High','Zone-External'),('DEV','Developer Portal','developer.vostokpribor.local','High','Zone-Bravo'),('DOC','File Center','files.vostokpribor.local','High','Zone-Bravo'),('EMP','Employee Intranet','intranet.vostokpribor.local','Medium','Zone-Internal'),('FIN','Finance & Billing','finance.vostokpribor.local','MissionCritical','Zone-Alpha'),('HR','HR System','hr.vostokpribor.local','High','Zone-Bravo'),('IT','IT Helpdesk','helpdesk.vostokpribor.local','Medium','Zone-Internal'),('SHP','Online Shop B2B','shop.vostokpribor.local','High','Zone-External'),('WEB','Corporate Web Platform','vostokpribor.local','Public','Zone-DMZ');
/*!40000 ALTER TABLE `systems_catalog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_comments`
--

DROP TABLE IF EXISTS `ticket_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `tkt_id` varchar(15) NOT NULL,
  `author_emp_id` varchar(10) DEFAULT NULL,
  `comment_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`comment_id`),
  KEY `fk_ticket_comments_tkt_id` (`tkt_id`),
  KEY `fk_ticket_comments_author_emp_id` (`author_emp_id`),
  CONSTRAINT `fk_ticket_comments_author_emp_id` FOREIGN KEY (`author_emp_id`) REFERENCES `employees` (`emp_id`),
  CONSTRAINT `fk_ticket_comments_tkt_id` FOREIGN KEY (`tkt_id`) REFERENCES `tickets` (`tkt_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_comments`
--

LOCK TABLES `ticket_comments` WRITE;
/*!40000 ALTER TABLE `ticket_comments` DISABLE KEYS */;
INSERT INTO `ticket_comments` VALUES (1,'TKT-2026-001','EMP-1018','Diagnostic trace shows optical sensor jitter resolved after transceiver cleaning.','2026-09-18 07:00:00'),(2,'TKT-2026-001','EMP-1002','Calibration values verified within 0.05% margin of error.','2026-09-18 08:30:00'),(3,'TKT-2026-002','EMP-1018','Substation relay power supply swapped; load test completed successfully.','2026-09-19 12:45:00');
/*!40000 ALTER TABLE `ticket_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_escalations`
--

DROP TABLE IF EXISTS `ticket_escalations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_escalations` (
  `escalation_id` int(11) NOT NULL AUTO_INCREMENT,
  `tkt_id` varchar(15) NOT NULL,
  `escalated_to_emp_id` varchar(10) DEFAULT NULL,
  `escalated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reason` text DEFAULT NULL,
  PRIMARY KEY (`escalation_id`),
  KEY `fk_ticket_escalations_tkt_id` (`tkt_id`),
  KEY `fk_ticket_escalations_escalated_to_emp_id` (`escalated_to_emp_id`),
  CONSTRAINT `fk_ticket_escalations_escalated_to_emp_id` FOREIGN KEY (`escalated_to_emp_id`) REFERENCES `employees` (`emp_id`),
  CONSTRAINT `fk_ticket_escalations_tkt_id` FOREIGN KEY (`tkt_id`) REFERENCES `tickets` (`tkt_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_escalations`
--

LOCK TABLES `ticket_escalations` WRITE;
/*!40000 ALTER TABLE `ticket_escalations` DISABLE KEYS */;
INSERT INTO `ticket_escalations` VALUES (1,'TKT-2026-001','EMP-1002','2026-09-18 06:30:00','Optical packet loss exceeded 5% threshold requiring Lead Automation Engineer review.'),(2,'TKT-2026-003','EMP-1005','2026-09-20 11:00:00','Privileged token anomaly requiring Governance Officer dual-custody review.');
/*!40000 ALTER TABLE `ticket_escalations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tickets` (
  `tkt_id` varchar(15) NOT NULL,
  `requester_type` enum('Customer','Employee') NOT NULL,
  `requester_cus_id` varchar(10) DEFAULT NULL,
  `requester_emp_id` varchar(10) DEFAULT NULL,
  `source_system` varchar(50) NOT NULL,
  `priority` enum('Low','Medium','High','Critical') NOT NULL,
  `assigned_emp_id` varchar(10) DEFAULT NULL,
  `status` enum('Open','InProgress','Investigating','Escalated','Resolved') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`tkt_id`),
  KEY `fk_tickets_requester_cus_id` (`requester_cus_id`),
  KEY `fk_tickets_requester_emp_id` (`requester_emp_id`),
  KEY `fk_tickets_assigned_emp_id` (`assigned_emp_id`),
  CONSTRAINT `fk_tickets_assigned_emp_id` FOREIGN KEY (`assigned_emp_id`) REFERENCES `employees` (`emp_id`),
  CONSTRAINT `fk_tickets_requester_cus_id` FOREIGN KEY (`requester_cus_id`) REFERENCES `customers` (`cus_id`),
  CONSTRAINT `fk_tickets_requester_emp_id` FOREIGN KEY (`requester_emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES ('TKT-2026-001','Customer','CUS-1002',NULL,'Customer Portal','High','EMP-1018','InProgress','2026-09-21 16:36:10','0000-00-00 00:00:00'),('TKT-2026-002','Employee',NULL,'EMP-1007','CRM','Medium','EMP-1018','Resolved','2026-09-21 16:36:10','0000-00-00 00:00:00'),('TKT-2026-003','Customer','CUS-1004',NULL,'E-Commerce','High','EMP-1018','Investigating','2026-09-21 16:36:10','0000-00-00 00:00:00'),('TKT-2026-004','Employee',NULL,'EMP-1015','Intranet','Low','EMP-1018','Resolved','2026-09-21 16:36:10','0000-00-00 00:00:00'),('TKT-2026-005','Customer','CUS-1007',NULL,'Customer Portal','Critical','EMP-1018','Escalated','2026-09-21 16:36:10','0000-00-00 00:00:00'),('TKT-2026-006','Employee',NULL,'EMP-1020','Developer Portal','Medium','EMP-1018','Resolved','2026-09-21 16:36:10','0000-00-00 00:00:00'),('TKT-2026-007','Customer','CUS-1005',NULL,'E-Commerce','Medium','EMP-1018','Resolved','2026-09-21 16:36:10','0000-00-00 00:00:00'),('TKT-2026-008','Employee',NULL,'EMP-1016','File Center','Medium','EMP-1018','InProgress','2026-09-21 16:36:10','0000-00-00 00:00:00'),('TKT-2026-009','Customer','CUS-1001',NULL,'Customer Portal','Low','EMP-1018','Resolved','2026-09-21 16:36:10','0000-00-00 00:00:00'),('TKT-2026-010','Employee',NULL,'EMP-1017','Developer Portal','High','EMP-1018','Investigating','2026-09-21 16:36:10','0000-00-00 00:00:00'),('TKT-2026-011','Customer','CUS-1008',NULL,'Customer Portal','High','EMP-1018','Escalated','2026-09-21 16:36:10','0000-00-00 00:00:00'),('TKT-2026-012','Employee',NULL,'EMP-1013','Intranet','Medium','EMP-1018','Resolved','2026-09-21 16:36:10','0000-00-00 00:00:00'),('TKT-2026-013','Customer','CUS-1009',NULL,'E-Commerce','Low','EMP-1018','Resolved','2026-09-21 16:36:10','0000-00-00 00:00:00'),('TKT-2026-014','Employee',NULL,'EMP-1019','File Center','Medium','EMP-1018','InProgress','2026-09-21 16:36:10','0000-00-00 00:00:00'),('TKT-2026-015','Customer','CUS-1010',NULL,'Customer Portal','High','EMP-1018','Investigating','2026-09-21 16:36:10','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_records`
--

DROP TABLE IF EXISTS `training_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training_records` (
  `training_id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` varchar(10) NOT NULL,
  `training_name` varchar(150) DEFAULT NULL,
  `completed_at` date DEFAULT NULL,
  `certificate_doc_id` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`training_id`),
  KEY `fk_training_records_emp_id` (`emp_id`),
  KEY `fk_training_records_certificate_doc_id` (`certificate_doc_id`),
  CONSTRAINT `fk_training_records_certificate_doc_id` FOREIGN KEY (`certificate_doc_id`) REFERENCES `documents` (`doc_id`),
  CONSTRAINT `fk_training_records_emp_id` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_records`
--

LOCK TABLES `training_records` WRITE;
/*!40000 ALTER TABLE `training_records` DISABLE KEYS */;
INSERT INTO `training_records` VALUES (2,'EMP-1016','SCADA Level 4 Security Protocol Compliance','2026-06-15',NULL),(3,'EMP-1017','Industrial IoT Cryptography & Data Pipelines','2026-07-20',NULL),(4,'EMP-1018','Cyber Defense & Perimeter Intrusion Prevention','2026-08-10',NULL),(5,'EMP-1008','Enterprise B2B Deal Structuring & Negotiation','2026-05-12',NULL),(6,'EMP-1011','Supply Chain Redundancy & ISO 9001 Audits','2026-04-18',NULL),(7,'EMP-1007','Strategic Key Account Management','2026-03-22',NULL),(8,'EMP-1013','Global Optical Equipment Procurement Standards','2026-02-14',NULL);
/*!40000 ALTER TABLE `training_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_sessions`
--

DROP TABLE IF EXISTS `user_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_sessions` (
  `session_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_type` varchar(20) NOT NULL,
  `employee_account_id` int(11) DEFAULT NULL,
  `customer_account_id` int(11) DEFAULT NULL,
  `system_id` varchar(4) DEFAULT NULL,
  `device_id` int(11) DEFAULT NULL,
  `ip_id` int(11) DEFAULT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ended_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(20) DEFAULT 'Active',
  PRIMARY KEY (`session_id`),
  KEY `fk_user_sessions_employee_account_id` (`employee_account_id`),
  KEY `fk_user_sessions_customer_account_id` (`customer_account_id`),
  KEY `fk_user_sessions_system_id` (`system_id`),
  KEY `fk_user_sessions_device_id` (`device_id`),
  KEY `fk_user_sessions_ip_id` (`ip_id`),
  CONSTRAINT `fk_user_sessions_customer_account_id` FOREIGN KEY (`customer_account_id`) REFERENCES `customer_accounts` (`account_id`),
  CONSTRAINT `fk_user_sessions_device_id` FOREIGN KEY (`device_id`) REFERENCES `devices` (`device_id`),
  CONSTRAINT `fk_user_sessions_employee_account_id` FOREIGN KEY (`employee_account_id`) REFERENCES `employee_accounts` (`account_id`),
  CONSTRAINT `fk_user_sessions_ip_id` FOREIGN KEY (`ip_id`) REFERENCES `ip_addresses` (`ip_id`),
  CONSTRAINT `fk_user_sessions_system_id` FOREIGN KEY (`system_id`) REFERENCES `systems_catalog` (`system_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_sessions`
--

LOCK TABLES `user_sessions` WRITE;
/*!40000 ALTER TABLE `user_sessions` DISABLE KEYS */;
INSERT INTO `user_sessions` VALUES (1,'Employee',53,NULL,'HR',NULL,NULL,'2026-09-21 14:32:28','2026-09-21 22:32:28','Active'),(2,'Employee',53,NULL,'HR',NULL,NULL,'2026-09-21 14:36:16','2026-09-21 22:36:16','Active');
/*!40000 ALTER TABLE `user_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'vostokpribor'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-23 16:03:49
