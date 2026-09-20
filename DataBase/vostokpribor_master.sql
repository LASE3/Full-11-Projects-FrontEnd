-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: vostokpribor
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

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `vostokpribor` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `vostokpribor`;

--
-- Table structure for table `access_reviews`
--

DROP TABLE IF EXISTS `access_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `access_reviews` (
  `review_id` int(11) NOT NULL,
  `emp_id` varchar(10) DEFAULT NULL,
  `reviewed_by_emp_id` varchar(10) DEFAULT NULL,
  `review_date` date DEFAULT curdate(),
  `finding` text DEFAULT NULL,
  `action_taken` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `access_reviews`
--

LOCK TABLES `access_reviews` WRITE;
/*!40000 ALTER TABLE `access_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `access_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `posted_by_emp_id` varchar(10) DEFAULT NULL,
  `audience_dept` varchar(4) DEFAULT NULL,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_access_logs`
--

DROP TABLE IF EXISTS `api_access_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_access_logs`
--

LOCK TABLES `api_access_logs` WRITE;
/*!40000 ALTER TABLE `api_access_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `api_access_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_credentials`
--

DROP TABLE IF EXISTS `api_credentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_credentials` (
  `credential_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `api_key_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `revoked` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_credentials`
--

LOCK TABLES `api_credentials` WRITE;
/*!40000 ALTER TABLE `api_credentials` DISABLE KEYS */;
/*!40000 ALTER TABLE `api_credentials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_partners`
--

DROP TABLE IF EXISTS `api_partners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_partners` (
  `partner_id` int(11) NOT NULL,
  `cus_id` varchar(10) DEFAULT NULL,
  `partner_name` varchar(150) DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(30) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_partners`
--

LOCK TABLES `api_partners` WRITE;
/*!40000 ALTER TABLE `api_partners` DISABLE KEYS */;
/*!40000 ALTER TABLE `api_partners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
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
  `source_ip` varchar(45) DEFAULT '127.0.0.1',
  `details` text DEFAULT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authentication_events`
--

LOCK TABLES `authentication_events` WRITE;
/*!40000 ALTER TABLE `authentication_events` DISABLE KEYS */;
INSERT INTO `authentication_events` VALUES (1,'Employee',2,NULL,'ADM',NULL,NULL,'LOGIN_ATTEMPT','2026-09-20 16:07:46',1,'127.0.0.1','Access granted via Executive L4 unrestricted clearance'),(2,'Employee',47,NULL,'DEV',NULL,NULL,'LOGIN_ATTEMPT','2026-09-20 16:07:52',1,'127.0.0.1','Access granted via Authorized via role access (Full)'),(3,'Employee',47,NULL,'ADM',NULL,NULL,'LOGIN_ATTEMPT','2026-09-20 16:07:56',0,'127.0.0.1','Authorization denied for system ADM: Insufficient clearance (L3) or role permissions for system ADM.'),(4,'Customer',NULL,2,'CUS',NULL,NULL,'LOGIN_ATTEMPT','2026-09-20 16:08:00',1,'127.0.0.1','Access granted via Customer portal access granted'),(5,'Customer',NULL,2,'ADM',NULL,NULL,'LOGIN_ATTEMPT','2026-09-20 16:08:05',0,'127.0.0.1','Authorization denied for system ADM: Customer accounts are restricted from internal enterprise portals'),(6,'Employee',2,NULL,'ADM',NULL,NULL,'LOGIN_ATTEMPT','2026-09-20 16:08:08',0,'127.0.0.1','Password mismatch for account ADM-VP-01'),(7,'Employee',2,NULL,'ADM',NULL,NULL,'LOGIN_ATTEMPT','2026-09-20 16:14:32',1,'127.0.0.1','Access granted via Executive L4 unrestricted clearance'),(8,'Employee',15,NULL,'CRM',NULL,NULL,'LOGIN_ATTEMPT','2026-09-20 16:14:32',1,'127.0.0.1','Access granted via Authorized via role access (Full)'),(9,'Customer',NULL,2,'CUS',NULL,NULL,'LOGIN_ATTEMPT','2026-09-20 16:14:32',1,'127.0.0.1','Access granted via Customer portal access granted'),(10,'Employee',47,NULL,'DEV',NULL,NULL,'LOGIN_ATTEMPT','2026-09-20 16:14:32',1,'127.0.0.1','Access granted via Authorized via role access (Full)'),(11,'Employee',93,NULL,'EMP',NULL,NULL,'LOGIN_ATTEMPT','2026-09-20 16:14:33',1,'127.0.0.1','Access granted via Authorized via role access (Read)'),(12,'Employee',142,NULL,'DOC',NULL,NULL,'LOGIN_ATTEMPT','2026-09-20 16:14:33',1,'127.0.0.1','Access granted via Authorized via role access (ReadWrite)'),(13,'Employee',156,NULL,'FIN',NULL,NULL,'LOGIN_ATTEMPT','2026-09-20 16:14:33',1,'127.0.0.1','Access granted via Executive L4 unrestricted clearance'),(14,'Employee',5,NULL,'HR',NULL,NULL,'LOGIN_ATTEMPT','2026-09-20 16:14:33',1,'127.0.0.1','Access granted via Executive L4 unrestricted clearance'),(15,'Employee',41,NULL,'IT',NULL,NULL,'LOGIN_ATTEMPT','2026-09-20 16:14:33',1,'127.0.0.1','Access granted via Authorized via role access (Full)'),(16,'Customer',NULL,5,'SHP',NULL,NULL,'LOGIN_ATTEMPT','2026-09-20 16:14:33',1,'127.0.0.1','Access granted via Customer portal access granted'),(17,'Customer',NULL,5,'ADM',NULL,NULL,'LOGIN_ATTEMPT','2026-09-20 16:14:33',0,'127.0.0.1','Authorization denied for system ADM: Customer accounts are restricted from internal enterprise portals');
/*!40000 ALTER TABLE `authentication_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing_cycles`
--

DROP TABLE IF EXISTS `billing_cycles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_cycles` (
  `cycle_id` int(11) NOT NULL,
  `prj_id` varchar(15) NOT NULL,
  `milestone_description` varchar(200) DEFAULT NULL,
  `scheduled_date` date DEFAULT NULL,
  `invoiced` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing_cycles`
--

LOCK TABLES `billing_cycles` WRITE;
/*!40000 ALTER TABLE `billing_cycles` DISABLE KEYS */;
/*!40000 ALTER TABLE `billing_cycles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `budgets`
--

DROP TABLE IF EXISTS `budgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgets` (
  `budget_id` int(11) NOT NULL,
  `department_code` varchar(4) DEFAULT NULL,
  `fiscal_year` int(11) DEFAULT NULL,
  `allocated_amount` decimal(14,2) DEFAULT NULL,
  `spent_amount` decimal(14,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `budgets`
--

LOCK TABLES `budgets` WRITE;
/*!40000 ALTER TABLE `budgets` DISABLE KEYS */;
/*!40000 ALTER TABLE `budgets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts` (
  `contact_id` int(11) NOT NULL,
  `cus_id` varchar(10) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contracts`
--

DROP TABLE IF EXISTS `contracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contracts` (
  `contract_id` int(11) NOT NULL,
  `cus_id` varchar(10) DEFAULT NULL,
  `prj_id` varchar(15) DEFAULT NULL,
  `opp_id` int(11) DEFAULT NULL,
  `contract_value` decimal(14,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `doc_id` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contracts`
--

LOCK TABLES `contracts` WRITE;
/*!40000 ALTER TABLE `contracts` DISABLE KEYS */;
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
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_accounts`
--

LOCK TABLES `customer_accounts` WRITE;
/*!40000 ALTER TABLE `customer_accounts` DISABLE KEYS */;
INSERT INTO `customer_accounts` VALUES (1,'CUS-1001','sergei.makarov','s.makarov@aral-geomatics.kz','$2y$10$dUJcrR/HX8nibkq6RiE15ug5eo1h7gD/Xs/FtOhvRY7A4/x3BXlQK',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(2,'CUS-1001','CLT-77210','client77210@vostokpribor.local','$2y$10$dUJcrR/HX8nibkq6RiE15ug5eo1h7gD/Xs/FtOhvRY7A4/x3BXlQK',0,'Active','2026-09-20 16:14:32','2026-09-20 16:05:39'),(3,'CUS-1001','CUS-1001','cus1001@aral-geomatics.kz','$2y$10$dUJcrR/HX8nibkq6RiE15ug5eo1h7gD/Xs/FtOhvRY7A4/x3BXlQK',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(4,'CUS-1002','kristaps.ozols','k.ozols@baltnord-systems.eu','$2y$10$dUJcrR/HX8nibkq6RiE15ug5eo1h7gD/Xs/FtOhvRY7A4/x3BXlQK',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(5,'CUS-1002','SHP-VP-11','b2b-buyer11@baltnord.eu','$2y$10$dUJcrR/HX8nibkq6RiE15ug5eo1h7gD/Xs/FtOhvRY7A4/x3BXlQK',0,'Active','2026-09-20 16:14:33','2026-09-20 16:05:39'),(6,'CUS-1002','CUS-1002','cus1002@baltnord.eu','$2y$10$dUJcrR/HX8nibkq6RiE15ug5eo1h7gD/Xs/FtOhvRY7A4/x3BXlQK',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(7,'CUS-1003','yerlan.bektemis','y.bektemis@steppemining.kz','$2y$10$dUJcrR/HX8nibkq6RiE15ug5eo1h7gD/Xs/FtOhvRY7A4/x3BXlQK',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(8,'CUS-1003','CUS-1003','cus1003@steppemining.kz','$2y$10$dUJcrR/HX8nibkq6RiE15ug5eo1h7gD/Xs/FtOhvRY7A4/x3BXlQK',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(9,'CUS-1004','lukas.brandt','l.brandt@rheinwerk-inst.de','$2y$10$dUJcrR/HX8nibkq6RiE15ug5eo1h7gD/Xs/FtOhvRY7A4/x3BXlQK',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(10,'CUS-1004','CUS-1004','cus1004@rheinwerk.de','$2y$10$dUJcrR/HX8nibkq6RiE15ug5eo1h7gD/Xs/FtOhvRY7A4/x3BXlQK',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(11,'CUS-1005','dilshod.karim','d.karim@tashkent-precision.uz','$2y$10$dUJcrR/HX8nibkq6RiE15ug5eo1h7gD/Xs/FtOhvRY7A4/x3BXlQK',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(12,'CUS-1005','CUS-1005','cus1005@tashkent-precision.uz','$2y$10$dUJcrR/HX8nibkq6RiE15ug5eo1h7gD/Xs/FtOhvRY7A4/x3BXlQK',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(13,'CUS-1006','mara.kalnina','m.kalnina@daugava-optical.lv','$2y$10$dUJcrR/HX8nibkq6RiE15ug5eo1h7gD/Xs/FtOhvRY7A4/x3BXlQK',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(14,'CUS-1007','murad.safarov','m.safarov@caspian-robotics.az','$2y$10$dUJcrR/HX8nibkq6RiE15ug5eo1h7gD/Xs/FtOhvRY7A4/x3BXlQK',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(15,'CUS-1008','oleg.petrenko','o.petrenko@eurasia-water.ua','$2y$10$dUJcrR/HX8nibkq6RiE15ug5eo1h7gD/Xs/FtOhvRY7A4/x3BXlQK',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(16,'CUS-1009','ainur.sadyk','a.sadyk@altai-env.kz','$2y$10$dUJcrR/HX8nibkq6RiE15ug5eo1h7gD/Xs/FtOhvRY7A4/x3BXlQK',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(17,'CUS-1010','tomas.varga','t.varga@central-rail.hu','$2y$10$dUJcrR/HX8nibkq6RiE15ug5eo1h7gD/Xs/FtOhvRY7A4/x3BXlQK',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(18,'CUS-1001','sergei.makarov','s.makarov@aral-geomatics.kz','$2y$10$pN4wsT5GSj9R6J/.OYR.v.tRn5j3xjB.XvpQKl7CQ0Go/hXTgBglC',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(19,'CUS-1001','CLT-77210','client77210@vostokpribor.local','$2y$10$pN4wsT5GSj9R6J/.OYR.v.tRn5j3xjB.XvpQKl7CQ0Go/hXTgBglC',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(20,'CUS-1001','CUS-1001','cus1001@aral-geomatics.kz','$2y$10$pN4wsT5GSj9R6J/.OYR.v.tRn5j3xjB.XvpQKl7CQ0Go/hXTgBglC',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(21,'CUS-1002','kristaps.ozols','k.ozols@baltnord-systems.eu','$2y$10$pN4wsT5GSj9R6J/.OYR.v.tRn5j3xjB.XvpQKl7CQ0Go/hXTgBglC',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(22,'CUS-1002','SHP-VP-11','b2b-buyer11@baltnord.eu','$2y$10$pN4wsT5GSj9R6J/.OYR.v.tRn5j3xjB.XvpQKl7CQ0Go/hXTgBglC',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(23,'CUS-1002','CUS-1002','cus1002@baltnord.eu','$2y$10$pN4wsT5GSj9R6J/.OYR.v.tRn5j3xjB.XvpQKl7CQ0Go/hXTgBglC',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(24,'CUS-1003','yerlan.bektemis','y.bektemis@steppemining.kz','$2y$10$pN4wsT5GSj9R6J/.OYR.v.tRn5j3xjB.XvpQKl7CQ0Go/hXTgBglC',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(25,'CUS-1003','CUS-1003','cus1003@steppemining.kz','$2y$10$pN4wsT5GSj9R6J/.OYR.v.tRn5j3xjB.XvpQKl7CQ0Go/hXTgBglC',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(26,'CUS-1004','lukas.brandt','l.brandt@rheinwerk-inst.de','$2y$10$pN4wsT5GSj9R6J/.OYR.v.tRn5j3xjB.XvpQKl7CQ0Go/hXTgBglC',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(27,'CUS-1004','CUS-1004','cus1004@rheinwerk.de','$2y$10$pN4wsT5GSj9R6J/.OYR.v.tRn5j3xjB.XvpQKl7CQ0Go/hXTgBglC',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(28,'CUS-1005','dilshod.karim','d.karim@tashkent-precision.uz','$2y$10$pN4wsT5GSj9R6J/.OYR.v.tRn5j3xjB.XvpQKl7CQ0Go/hXTgBglC',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(29,'CUS-1005','CUS-1005','cus1005@tashkent-precision.uz','$2y$10$pN4wsT5GSj9R6J/.OYR.v.tRn5j3xjB.XvpQKl7CQ0Go/hXTgBglC',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(30,'CUS-1006','mara.kalnina','m.kalnina@daugava-optical.lv','$2y$10$pN4wsT5GSj9R6J/.OYR.v.tRn5j3xjB.XvpQKl7CQ0Go/hXTgBglC',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(31,'CUS-1007','murad.safarov','m.safarov@caspian-robotics.az','$2y$10$pN4wsT5GSj9R6J/.OYR.v.tRn5j3xjB.XvpQKl7CQ0Go/hXTgBglC',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(32,'CUS-1008','oleg.petrenko','o.petrenko@eurasia-water.ua','$2y$10$pN4wsT5GSj9R6J/.OYR.v.tRn5j3xjB.XvpQKl7CQ0Go/hXTgBglC',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(33,'CUS-1009','ainur.sadyk','a.sadyk@altai-env.kz','$2y$10$pN4wsT5GSj9R6J/.OYR.v.tRn5j3xjB.XvpQKl7CQ0Go/hXTgBglC',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(34,'CUS-1010','tomas.varga','t.varga@central-rail.hu','$2y$10$pN4wsT5GSj9R6J/.OYR.v.tRn5j3xjB.XvpQKl7CQ0Go/hXTgBglC',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(35,'CUS-1001','sergei.makarov','s.makarov@aral-geomatics.kz','$2y$10$FfOBA/qg9xnutLDJYWp41OVe9RLzfV4sF6l2YJQN3Yw2QWDIr3q6u',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(36,'CUS-1001','CLT-77210','client77210@vostokpribor.local','$2y$10$FfOBA/qg9xnutLDJYWp41OVe9RLzfV4sF6l2YJQN3Yw2QWDIr3q6u',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(37,'CUS-1001','CUS-1001','cus1001@aral-geomatics.kz','$2y$10$FfOBA/qg9xnutLDJYWp41OVe9RLzfV4sF6l2YJQN3Yw2QWDIr3q6u',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(38,'CUS-1002','kristaps.ozols','k.ozols@baltnord-systems.eu','$2y$10$FfOBA/qg9xnutLDJYWp41OVe9RLzfV4sF6l2YJQN3Yw2QWDIr3q6u',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(39,'CUS-1002','SHP-VP-11','b2b-buyer11@baltnord.eu','$2y$10$FfOBA/qg9xnutLDJYWp41OVe9RLzfV4sF6l2YJQN3Yw2QWDIr3q6u',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(40,'CUS-1002','CUS-1002','cus1002@baltnord.eu','$2y$10$FfOBA/qg9xnutLDJYWp41OVe9RLzfV4sF6l2YJQN3Yw2QWDIr3q6u',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(41,'CUS-1003','yerlan.bektemis','y.bektemis@steppemining.kz','$2y$10$FfOBA/qg9xnutLDJYWp41OVe9RLzfV4sF6l2YJQN3Yw2QWDIr3q6u',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(42,'CUS-1003','CUS-1003','cus1003@steppemining.kz','$2y$10$FfOBA/qg9xnutLDJYWp41OVe9RLzfV4sF6l2YJQN3Yw2QWDIr3q6u',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(43,'CUS-1004','lukas.brandt','l.brandt@rheinwerk-inst.de','$2y$10$FfOBA/qg9xnutLDJYWp41OVe9RLzfV4sF6l2YJQN3Yw2QWDIr3q6u',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(44,'CUS-1004','CUS-1004','cus1004@rheinwerk.de','$2y$10$FfOBA/qg9xnutLDJYWp41OVe9RLzfV4sF6l2YJQN3Yw2QWDIr3q6u',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(45,'CUS-1005','dilshod.karim','d.karim@tashkent-precision.uz','$2y$10$FfOBA/qg9xnutLDJYWp41OVe9RLzfV4sF6l2YJQN3Yw2QWDIr3q6u',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(46,'CUS-1005','CUS-1005','cus1005@tashkent-precision.uz','$2y$10$FfOBA/qg9xnutLDJYWp41OVe9RLzfV4sF6l2YJQN3Yw2QWDIr3q6u',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(47,'CUS-1006','mara.kalnina','m.kalnina@daugava-optical.lv','$2y$10$FfOBA/qg9xnutLDJYWp41OVe9RLzfV4sF6l2YJQN3Yw2QWDIr3q6u',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(48,'CUS-1007','murad.safarov','m.safarov@caspian-robotics.az','$2y$10$FfOBA/qg9xnutLDJYWp41OVe9RLzfV4sF6l2YJQN3Yw2QWDIr3q6u',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(49,'CUS-1008','oleg.petrenko','o.petrenko@eurasia-water.ua','$2y$10$FfOBA/qg9xnutLDJYWp41OVe9RLzfV4sF6l2YJQN3Yw2QWDIr3q6u',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(50,'CUS-1009','ainur.sadyk','a.sadyk@altai-env.kz','$2y$10$FfOBA/qg9xnutLDJYWp41OVe9RLzfV4sF6l2YJQN3Yw2QWDIr3q6u',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(51,'CUS-1010','tomas.varga','t.varga@central-rail.hu','$2y$10$FfOBA/qg9xnutLDJYWp41OVe9RLzfV4sF6l2YJQN3Yw2QWDIr3q6u',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(52,'CUS-1001','sergei.makarov','s.makarov@aral-geomatics.kz','$2y$10$Sggvi.ItHfOj6qnw2NZPBeqbQuOYWbXHR9M1XJtoAQFEwF9F1i0WC',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(53,'CUS-1001','CLT-77210','client77210@vostokpribor.local','$2y$10$Sggvi.ItHfOj6qnw2NZPBeqbQuOYWbXHR9M1XJtoAQFEwF9F1i0WC',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(54,'CUS-1001','CUS-1001','cus1001@aral-geomatics.kz','$2y$10$Sggvi.ItHfOj6qnw2NZPBeqbQuOYWbXHR9M1XJtoAQFEwF9F1i0WC',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(55,'CUS-1002','kristaps.ozols','k.ozols@baltnord-systems.eu','$2y$10$Sggvi.ItHfOj6qnw2NZPBeqbQuOYWbXHR9M1XJtoAQFEwF9F1i0WC',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(56,'CUS-1002','SHP-VP-11','b2b-buyer11@baltnord.eu','$2y$10$Sggvi.ItHfOj6qnw2NZPBeqbQuOYWbXHR9M1XJtoAQFEwF9F1i0WC',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(57,'CUS-1002','CUS-1002','cus1002@baltnord.eu','$2y$10$Sggvi.ItHfOj6qnw2NZPBeqbQuOYWbXHR9M1XJtoAQFEwF9F1i0WC',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(58,'CUS-1003','yerlan.bektemis','y.bektemis@steppemining.kz','$2y$10$Sggvi.ItHfOj6qnw2NZPBeqbQuOYWbXHR9M1XJtoAQFEwF9F1i0WC',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(59,'CUS-1003','CUS-1003','cus1003@steppemining.kz','$2y$10$Sggvi.ItHfOj6qnw2NZPBeqbQuOYWbXHR9M1XJtoAQFEwF9F1i0WC',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(60,'CUS-1004','lukas.brandt','l.brandt@rheinwerk-inst.de','$2y$10$Sggvi.ItHfOj6qnw2NZPBeqbQuOYWbXHR9M1XJtoAQFEwF9F1i0WC',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(61,'CUS-1004','CUS-1004','cus1004@rheinwerk.de','$2y$10$Sggvi.ItHfOj6qnw2NZPBeqbQuOYWbXHR9M1XJtoAQFEwF9F1i0WC',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(62,'CUS-1005','dilshod.karim','d.karim@tashkent-precision.uz','$2y$10$Sggvi.ItHfOj6qnw2NZPBeqbQuOYWbXHR9M1XJtoAQFEwF9F1i0WC',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(63,'CUS-1005','CUS-1005','cus1005@tashkent-precision.uz','$2y$10$Sggvi.ItHfOj6qnw2NZPBeqbQuOYWbXHR9M1XJtoAQFEwF9F1i0WC',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(64,'CUS-1006','mara.kalnina','m.kalnina@daugava-optical.lv','$2y$10$Sggvi.ItHfOj6qnw2NZPBeqbQuOYWbXHR9M1XJtoAQFEwF9F1i0WC',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(65,'CUS-1007','murad.safarov','m.safarov@caspian-robotics.az','$2y$10$Sggvi.ItHfOj6qnw2NZPBeqbQuOYWbXHR9M1XJtoAQFEwF9F1i0WC',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(66,'CUS-1008','oleg.petrenko','o.petrenko@eurasia-water.ua','$2y$10$Sggvi.ItHfOj6qnw2NZPBeqbQuOYWbXHR9M1XJtoAQFEwF9F1i0WC',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(67,'CUS-1009','ainur.sadyk','a.sadyk@altai-env.kz','$2y$10$Sggvi.ItHfOj6qnw2NZPBeqbQuOYWbXHR9M1XJtoAQFEwF9F1i0WC',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(68,'CUS-1010','tomas.varga','t.varga@central-rail.hu','$2y$10$Sggvi.ItHfOj6qnw2NZPBeqbQuOYWbXHR9M1XJtoAQFEwF9F1i0WC',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(69,'CUS-1001','sergei.makarov','s.makarov@aral-geomatics.kz','$2y$10$RV6Vdd8wn1t12XNmHTtCbuYnzVjxjFHG3VpOoZoAwZ5gPYf4CQKPy',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(70,'CUS-1001','CLT-77210','client77210@vostokpribor.local','$2y$10$RV6Vdd8wn1t12XNmHTtCbuYnzVjxjFHG3VpOoZoAwZ5gPYf4CQKPy',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(71,'CUS-1001','CUS-1001','cus1001@aral-geomatics.kz','$2y$10$RV6Vdd8wn1t12XNmHTtCbuYnzVjxjFHG3VpOoZoAwZ5gPYf4CQKPy',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(72,'CUS-1002','kristaps.ozols','k.ozols@baltnord-systems.eu','$2y$10$RV6Vdd8wn1t12XNmHTtCbuYnzVjxjFHG3VpOoZoAwZ5gPYf4CQKPy',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(73,'CUS-1002','SHP-VP-11','b2b-buyer11@baltnord.eu','$2y$10$RV6Vdd8wn1t12XNmHTtCbuYnzVjxjFHG3VpOoZoAwZ5gPYf4CQKPy',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(74,'CUS-1002','CUS-1002','cus1002@baltnord.eu','$2y$10$RV6Vdd8wn1t12XNmHTtCbuYnzVjxjFHG3VpOoZoAwZ5gPYf4CQKPy',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(75,'CUS-1003','yerlan.bektemis','y.bektemis@steppemining.kz','$2y$10$RV6Vdd8wn1t12XNmHTtCbuYnzVjxjFHG3VpOoZoAwZ5gPYf4CQKPy',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(76,'CUS-1003','CUS-1003','cus1003@steppemining.kz','$2y$10$RV6Vdd8wn1t12XNmHTtCbuYnzVjxjFHG3VpOoZoAwZ5gPYf4CQKPy',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(77,'CUS-1004','lukas.brandt','l.brandt@rheinwerk-inst.de','$2y$10$RV6Vdd8wn1t12XNmHTtCbuYnzVjxjFHG3VpOoZoAwZ5gPYf4CQKPy',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(78,'CUS-1004','CUS-1004','cus1004@rheinwerk.de','$2y$10$RV6Vdd8wn1t12XNmHTtCbuYnzVjxjFHG3VpOoZoAwZ5gPYf4CQKPy',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(79,'CUS-1005','dilshod.karim','d.karim@tashkent-precision.uz','$2y$10$RV6Vdd8wn1t12XNmHTtCbuYnzVjxjFHG3VpOoZoAwZ5gPYf4CQKPy',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(80,'CUS-1005','CUS-1005','cus1005@tashkent-precision.uz','$2y$10$RV6Vdd8wn1t12XNmHTtCbuYnzVjxjFHG3VpOoZoAwZ5gPYf4CQKPy',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(81,'CUS-1006','mara.kalnina','m.kalnina@daugava-optical.lv','$2y$10$RV6Vdd8wn1t12XNmHTtCbuYnzVjxjFHG3VpOoZoAwZ5gPYf4CQKPy',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(82,'CUS-1007','murad.safarov','m.safarov@caspian-robotics.az','$2y$10$RV6Vdd8wn1t12XNmHTtCbuYnzVjxjFHG3VpOoZoAwZ5gPYf4CQKPy',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(83,'CUS-1008','oleg.petrenko','o.petrenko@eurasia-water.ua','$2y$10$RV6Vdd8wn1t12XNmHTtCbuYnzVjxjFHG3VpOoZoAwZ5gPYf4CQKPy',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(84,'CUS-1009','ainur.sadyk','a.sadyk@altai-env.kz','$2y$10$RV6Vdd8wn1t12XNmHTtCbuYnzVjxjFHG3VpOoZoAwZ5gPYf4CQKPy',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(85,'CUS-1010','tomas.varga','t.varga@central-rail.hu','$2y$10$RV6Vdd8wn1t12XNmHTtCbuYnzVjxjFHG3VpOoZoAwZ5gPYf4CQKPy',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06');
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
  `special_price` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_pricing`
--

LOCK TABLES `customer_pricing` WRITE;
/*!40000 ALTER TABLE `customer_pricing` DISABLE KEYS */;
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
INSERT INTO `customers` VALUES ('CUS-1001','Aral Geomatics Group','Surveying & GIS','Sergei Makarov','EMP-1007','2026-09-19 10:35:40'),('CUS-1002','BaltNord Process Systems','Industrial Automation','Kristaps Ozols','EMP-1010','2026-09-19 10:35:40'),('CUS-1003','Steppe Mining Technologies','Mining','Yerlan Bektemis','EMP-1008','2026-09-19 10:35:40'),('CUS-1004','Rhein Werk Instrumentation','Industrial Measurement','Lukas Brandt','EMP-1010','2026-09-19 10:35:40'),('CUS-1005','Tashkent Precision Controls','Manufacturing','Dilshod Karim','EMP-1008','2026-09-19 10:35:40'),('CUS-1006','Daugava Optical Research','Optical Engineering','Mara Kalnina','EMP-1007','2026-09-19 10:35:40'),('CUS-1007','Caspian Industrial Robotics','Robotics','Murad Safarov','EMP-1009','2026-09-19 10:35:40'),('CUS-1008','Eurasia Water Automation','Water Infrastructure','Oleg Petrenko','EMP-1009','2026-09-19 10:35:40'),('CUS-1009','Altai Environmental Systems','Environmental Monitoring','Ainur Sadyk','EMP-1008','2026-09-19 10:35:40'),('CUS-1010','Central Rail Diagnostics','Railway Infrastructure','Tomas Varga','EMP-1006','2026-09-19 10:35:40');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department_boards`
--

DROP TABLE IF EXISTS `department_boards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department_boards` (
  `board_post_id` int(11) NOT NULL,
  `department_code` varchar(4) DEFAULT NULL,
  `topic` varchar(200) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `posted_by_emp_id` varchar(10) DEFAULT NULL,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department_boards`
--

LOCK TABLES `department_boards` WRITE;
/*!40000 ALTER TABLE `department_boards` DISABLE KEYS */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devices`
--

LOCK TABLES `devices` WRITE;
/*!40000 ALTER TABLE `devices` DISABLE KEYS */;
/*!40000 ALTER TABLE `devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_access_log`
--

DROP TABLE IF EXISTS `document_access_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_access_log` (
  `access_id` int(11) NOT NULL,
  `doc_id` varchar(15) NOT NULL,
  `accessed_by_emp_id` varchar(10) DEFAULT NULL,
  `accessed_by_cus_id` varchar(10) DEFAULT NULL,
  `system_id` varchar(4) DEFAULT NULL,
  `device_id` int(11) DEFAULT NULL,
  `source_ip` varchar(45) DEFAULT NULL,
  `access_type` enum('View','Download','Edit','Delete') DEFAULT NULL,
  `success` tinyint(1) DEFAULT 1,
  `accessed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_access_log`
--

LOCK TABLES `document_access_log` WRITE;
/*!40000 ALTER TABLE `document_access_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_access_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_approvals`
--

DROP TABLE IF EXISTS `document_approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_approvals` (
  `approval_id` int(11) NOT NULL,
  `doc_id` varchar(15) NOT NULL,
  `reviewer_emp_id` varchar(10) DEFAULT NULL,
  `decision` enum('Approved','Rejected','Pending') DEFAULT 'Pending',
  `decision_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_approvals`
--

LOCK TABLES `document_approvals` WRITE;
/*!40000 ALTER TABLE `document_approvals` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_approvals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_versions`
--

DROP TABLE IF EXISTS `document_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_versions` (
  `version_id` int(11) NOT NULL,
  `doc_id` varchar(15) NOT NULL,
  `version_number` int(11) NOT NULL,
  `uploaded_by_emp_id` varchar(10) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_versions`
--

LOCK TABLES `document_versions` WRITE;
/*!40000 ALTER TABLE `document_versions` DISABLE KEYS */;
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
  PRIMARY KEY (`doc_id`),
  KEY `fk_documents_owner_emp_id` (`owner_emp_id`),
  KEY `fk_documents_related_cus_id` (`related_cus_id`),
  KEY `fk_documents_related_prj_id` (`related_prj_id`),
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
INSERT INTO `documents` VALUES ('DOC-2026-001','Corporate_Information_Security_Policy.pdf','TopSecret','Admin & Governance','EMP-1005',NULL,NULL),('DOC-2026-002','Customer_Onboarding_Standard.pdf','Confidential','CRM','EMP-1006',NULL,NULL),('DOC-2026-003','PRJ-2026-001_Statement_of_Work.pdf','Confidential','File Center','EMP-1019','PRJ-2026-001','CUS-1001'),('DOC-2026-004','PRJ-2026-002_Integration_Specification.pdf','TopSecret','File Center','EMP-1019','PRJ-2026-002','CUS-1002'),('DOC-2026-005','INV-2026-002_Billing_Record.pdf','Confidential','Finance','EMP-1003','PRJ-2026-002','CUS-1002'),('DOC-2026-006','Employee_Onboarding_Procedure.pdf','Confidential','HR','EMP-1005',NULL,NULL),('DOC-2026-007','Employee_Access_Matrix.xlsx','TopSecret','Admin & Governance','EMP-1005',NULL,NULL),('DOC-2026-008','Supplier_Evaluation_2026.pdf','Confidential','Operations','EMP-1013',NULL,NULL),('DOC-2026-009','Optical_Sensor_Product_Catalog.pdf','Public','E-Commerce','EMP-1006',NULL,NULL),('DOC-2026-010','API_Integration_Guide.pdf','Internal','Developer Portal','EMP-1020',NULL,NULL),('DOC-2026-011','Disaster_Recovery_Plan.pdf','TopSecret','IT Helpdesk','EMP-1018',NULL,NULL),('DOC-2026-012','Annual_Corporate_Budget_2026.xlsx','TopSecret','Finance','EMP-1003',NULL,NULL),('DOC-2026-013','Customer_Service_Handbook.pdf','Internal','Intranet','EMP-1004',NULL,NULL),('DOC-2026-014','PRJ-2026-007_Test_Report.pdf','Confidential','File Center','EMP-1019','PRJ-2026-007','CUS-1007'),('DOC-2026-015','Board_Risk_Register_2026.xlsx','TopSecret','Admin & Governance','EMP-1005',NULL,NULL);
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
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=251 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_accounts`
--

LOCK TABLES `employee_accounts` WRITE;
/*!40000 ALTER TABLE `employee_accounts` DISABLE KEYS */;
INSERT INTO `employee_accounts` VALUES (1,'EMP-1001','viktor.sokolov','$2y$10$KuWJgXJV75Caakm7701XZuNCVxLt0Q8yotMAQ8tUL6OUh7reJJMcy',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(2,'EMP-1001','ADM-VP-01','$2y$10$KuWJgXJV75Caakm7701XZuNCVxLt0Q8yotMAQ8tUL6OUh7reJJMcy',0,'Active','2026-09-20 16:14:32','2026-09-20 16:05:39'),(3,'EMP-1001','EMP-1001','$2y$10$KuWJgXJV75Caakm7701XZuNCVxLt0Q8yotMAQ8tUL6OUh7reJJMcy',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(4,'EMP-1002','amina.karimova','$2y$10$KuWJgXJV75Caakm7701XZuNCVxLt0Q8yotMAQ8tUL6OUh7reJJMcy',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(5,'EMP-1002','HR-VP-201','$2y$10$KuWJgXJV75Caakm7701XZuNCVxLt0Q8yotMAQ8tUL6OUh7reJJMcy',0,'Active','2026-09-20 16:14:33','2026-09-20 16:05:39'),(6,'EMP-1002','EMP-1002','$2y$10$KuWJgXJV75Caakm7701XZuNCVxLt0Q8yotMAQ8tUL6OUh7reJJMcy',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(7,'EMP-1003','daniel.weber','$2y$10$KuWJgXJV75Caakm7701XZuNCVxLt0Q8yotMAQ8tUL6OUh7reJJMcy',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(8,'EMP-1003','FIN-VP-102','$2y$10$KuWJgXJV75Caakm7701XZuNCVxLt0Q8yotMAQ8tUL6OUh7reJJMcy',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(9,'EMP-1003','EMP-1003','$2y$10$KuWJgXJV75Caakm7701XZuNCVxLt0Q8yotMAQ8tUL6OUh7reJJMcy',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(10,'EMP-1004','elena.morozova','$2y$10$KuWJgXJV75Caakm7701XZuNCVxLt0Q8yotMAQ8tUL6OUh7reJJMcy',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(11,'EMP-1004','EMP-1004','$2y$10$KuWJgXJV75Caakm7701XZuNCVxLt0Q8yotMAQ8tUL6OUh7reJJMcy',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(12,'EMP-1005','timur.akhmetov','$2y$10$KuWJgXJV75Caakm7701XZuNCVxLt0Q8yotMAQ8tUL6OUh7reJJMcy',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(13,'EMP-1005','EMP-1005','$2y$10$KuWJgXJV75Caakm7701XZuNCVxLt0Q8yotMAQ8tUL6OUh7reJJMcy',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(14,'EMP-1006','pavel.orlov','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(15,'EMP-1006','CRM-VP-842','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:14:32','2026-09-20 16:05:39'),(16,'EMP-1006','EMP-842','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(17,'EMP-1006','EMP-1006','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(18,'EMP-1007','sara.lindholm','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(19,'EMP-1007','EMP-1007','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(20,'EMP-1008','bekzod.rakhimov','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(21,'EMP-1008','EMP-1008','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(22,'EMP-1009','nadia.petrova','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(23,'EMP-1009','EMP-1009','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(24,'EMP-1010','markus.klein','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(25,'EMP-1010','EMP-1010','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(26,'EMP-1011','arman.tulegenov','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(27,'EMP-1011','EMP-1011','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(28,'EMP-1012','rustam.bekov','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(29,'EMP-1012','EMP-1012','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(30,'EMP-1013','ilona.vetra','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(31,'EMP-1013','EMP-1013','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(32,'EMP-1014','mikhail.antonov','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(33,'EMP-1014','EMP-1014','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(34,'EMP-1015','kamila.nurzhan','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(35,'EMP-1015','EMP-1015','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(36,'EMP-1016','erik.hansen','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(37,'EMP-1016','EMP-1016','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(38,'EMP-1017','dana.yermak','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(39,'EMP-1017','EMP-1017','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(40,'EMP-1018','leonid.volkov','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(41,'EMP-1018','IT-VP-304','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:14:33','2026-09-20 16:05:39'),(42,'EMP-1018','EMP-1018','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(43,'EMP-1019','farida.iskakova','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(44,'EMP-1019','DOC-VP-501','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(45,'EMP-1019','EMP-1019','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(46,'EMP-1020','jonas.richter','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(47,'EMP-1020','DEV-VP-994','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:14:32','2026-09-20 16:05:39'),(48,'EMP-1020','EMP-1020','$2y$10$YoiW88Q8n2TO3p8rn9Vbu.unlc16qhMVTQllo6zERbQ.JpUce9pmC',0,'Active','2026-09-20 16:07:17','2026-09-20 16:05:39'),(49,'EMP-1001','viktor.sokolov','$2y$10$Ca2sI8gGWKsJfXMJc39UvuBANytOHFEu3ZQ.Fz9idk2uTRpX3/vQu',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(50,'EMP-1001','ADM-VP-01','$2y$10$Ca2sI8gGWKsJfXMJc39UvuBANytOHFEu3ZQ.Fz9idk2uTRpX3/vQu',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(51,'EMP-1001','EMP-1001','$2y$10$Ca2sI8gGWKsJfXMJc39UvuBANytOHFEu3ZQ.Fz9idk2uTRpX3/vQu',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(52,'EMP-1002','amina.karimova','$2y$10$Ca2sI8gGWKsJfXMJc39UvuBANytOHFEu3ZQ.Fz9idk2uTRpX3/vQu',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(53,'EMP-1002','HR-VP-201','$2y$10$Ca2sI8gGWKsJfXMJc39UvuBANytOHFEu3ZQ.Fz9idk2uTRpX3/vQu',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(54,'EMP-1002','EMP-1002','$2y$10$Ca2sI8gGWKsJfXMJc39UvuBANytOHFEu3ZQ.Fz9idk2uTRpX3/vQu',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(55,'EMP-1003','daniel.weber','$2y$10$Ca2sI8gGWKsJfXMJc39UvuBANytOHFEu3ZQ.Fz9idk2uTRpX3/vQu',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(56,'EMP-1003','FIN-VP-102','$2y$10$Ca2sI8gGWKsJfXMJc39UvuBANytOHFEu3ZQ.Fz9idk2uTRpX3/vQu',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(57,'EMP-1003','EMP-1003','$2y$10$Ca2sI8gGWKsJfXMJc39UvuBANytOHFEu3ZQ.Fz9idk2uTRpX3/vQu',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(58,'EMP-1004','elena.morozova','$2y$10$Ca2sI8gGWKsJfXMJc39UvuBANytOHFEu3ZQ.Fz9idk2uTRpX3/vQu',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(59,'EMP-1004','EMP-1004','$2y$10$Ca2sI8gGWKsJfXMJc39UvuBANytOHFEu3ZQ.Fz9idk2uTRpX3/vQu',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(60,'EMP-1005','timur.akhmetov','$2y$10$Ca2sI8gGWKsJfXMJc39UvuBANytOHFEu3ZQ.Fz9idk2uTRpX3/vQu',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(61,'EMP-1005','EMP-1005','$2y$10$Ca2sI8gGWKsJfXMJc39UvuBANytOHFEu3ZQ.Fz9idk2uTRpX3/vQu',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(62,'EMP-1006','pavel.orlov','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(63,'EMP-1006','CRM-VP-842','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(64,'EMP-1006','EMP-842','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(65,'EMP-1006','EMP-1006','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(66,'EMP-1007','sara.lindholm','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(67,'EMP-1007','EMP-1007','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(68,'EMP-1008','bekzod.rakhimov','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(69,'EMP-1008','EMP-1008','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(70,'EMP-1009','nadia.petrova','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(71,'EMP-1009','EMP-1009','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(72,'EMP-1010','markus.klein','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(73,'EMP-1010','EMP-1010','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(74,'EMP-1011','arman.tulegenov','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(75,'EMP-1011','EMP-1011','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(76,'EMP-1012','rustam.bekov','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(77,'EMP-1012','EMP-1012','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(78,'EMP-1013','ilona.vetra','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(79,'EMP-1013','EMP-1013','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(80,'EMP-1014','mikhail.antonov','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(81,'EMP-1014','EMP-1014','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(82,'EMP-1015','kamila.nurzhan','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(83,'EMP-1015','EMP-1015','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(84,'EMP-1016','erik.hansen','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(85,'EMP-1016','EMP-1016','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(86,'EMP-1017','dana.yermak','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(87,'EMP-1017','EMP-1017','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(88,'EMP-1018','leonid.volkov','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(89,'EMP-1018','IT-VP-304','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(90,'EMP-1018','EMP-1018','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(91,'EMP-1019','farida.iskakova','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(92,'EMP-1019','DOC-VP-501','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(93,'EMP-1019','EMP-VP-1019','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:14:33','2026-09-20 16:10:27'),(94,'EMP-1019','EMP-1019','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(95,'EMP-1020','jonas.richter','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(96,'EMP-1020','DEV-VP-994','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(97,'EMP-1020','EMP-1020','$2y$10$OCWMXFvjf/S8OQR.E1gb7uIxiZ/.kZ.TkOeqe3SpxHqvsfRcGJQaa',0,'Active','2026-09-20 16:10:27','2026-09-20 16:10:27'),(98,'EMP-1001','viktor.sokolov','$2y$10$0ozW3nDAvzNhWh8J38B8tOczfPsRcWwg/jivRPUjnEixvve.9CuQu',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(99,'EMP-1001','ADM-VP-01','$2y$10$0ozW3nDAvzNhWh8J38B8tOczfPsRcWwg/jivRPUjnEixvve.9CuQu',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(100,'EMP-1001','EMP-1001','$2y$10$0ozW3nDAvzNhWh8J38B8tOczfPsRcWwg/jivRPUjnEixvve.9CuQu',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(101,'EMP-1002','amina.karimova','$2y$10$0ozW3nDAvzNhWh8J38B8tOczfPsRcWwg/jivRPUjnEixvve.9CuQu',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(102,'EMP-1002','HR-VP-201','$2y$10$0ozW3nDAvzNhWh8J38B8tOczfPsRcWwg/jivRPUjnEixvve.9CuQu',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(103,'EMP-1002','EMP-1002','$2y$10$0ozW3nDAvzNhWh8J38B8tOczfPsRcWwg/jivRPUjnEixvve.9CuQu',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(104,'EMP-1003','daniel.weber','$2y$10$0ozW3nDAvzNhWh8J38B8tOczfPsRcWwg/jivRPUjnEixvve.9CuQu',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(105,'EMP-1003','FIN-VP-102','$2y$10$0ozW3nDAvzNhWh8J38B8tOczfPsRcWwg/jivRPUjnEixvve.9CuQu',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(106,'EMP-1003','EMP-1003','$2y$10$0ozW3nDAvzNhWh8J38B8tOczfPsRcWwg/jivRPUjnEixvve.9CuQu',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(107,'EMP-1004','elena.morozova','$2y$10$0ozW3nDAvzNhWh8J38B8tOczfPsRcWwg/jivRPUjnEixvve.9CuQu',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(108,'EMP-1004','EMP-1004','$2y$10$0ozW3nDAvzNhWh8J38B8tOczfPsRcWwg/jivRPUjnEixvve.9CuQu',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(109,'EMP-1005','timur.akhmetov','$2y$10$0ozW3nDAvzNhWh8J38B8tOczfPsRcWwg/jivRPUjnEixvve.9CuQu',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(110,'EMP-1005','EMP-1005','$2y$10$0ozW3nDAvzNhWh8J38B8tOczfPsRcWwg/jivRPUjnEixvve.9CuQu',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(111,'EMP-1006','pavel.orlov','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(112,'EMP-1006','CRM-VP-842','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(113,'EMP-1006','EMP-842','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(114,'EMP-1006','EMP-1006','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(115,'EMP-1007','sara.lindholm','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(116,'EMP-1007','EMP-1007','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(117,'EMP-1008','bekzod.rakhimov','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(118,'EMP-1008','EMP-1008','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(119,'EMP-1009','nadia.petrova','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(120,'EMP-1009','EMP-1009','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(121,'EMP-1010','markus.klein','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(122,'EMP-1010','EMP-1010','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(123,'EMP-1011','arman.tulegenov','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(124,'EMP-1011','EMP-1011','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(125,'EMP-1012','rustam.bekov','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(126,'EMP-1012','EMP-1012','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(127,'EMP-1013','ilona.vetra','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(128,'EMP-1013','EMP-1013','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(129,'EMP-1014','mikhail.antonov','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(130,'EMP-1014','EMP-1014','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(131,'EMP-1015','kamila.nurzhan','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(132,'EMP-1015','EMP-1015','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(133,'EMP-1016','erik.hansen','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(134,'EMP-1016','EMP-1016','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(135,'EMP-1017','dana.yermak','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(136,'EMP-1017','EMP-1017','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(137,'EMP-1018','leonid.volkov','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(138,'EMP-1018','IT-VP-304','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(139,'EMP-1018','EMP-1018','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(140,'EMP-1019','farida.iskakova','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(141,'EMP-1019','DOC-VP-501','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(142,'EMP-1019','CST-VP-09','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:14:33','2026-09-20 16:10:58'),(143,'EMP-1019','EMP-VP-1019','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(144,'EMP-1019','EMP-1019','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(145,'EMP-1020','jonas.richter','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(146,'EMP-1020','DEV-VP-994','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(147,'EMP-1020','EMP-1020','$2y$10$gQS96DIpYPLBhUiJ00CRXuz0RnAQxefG3VPaSIAD20PZOhcAcxw0S',0,'Active','2026-09-20 16:10:58','2026-09-20 16:10:58'),(148,'EMP-1001','viktor.sokolov','$2y$10$xrhe857trcKAzOQpRUBJWOGNhDb07UsuFjCdCdpYzAIZ7bOEwIgpa',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(149,'EMP-1001','ADM-VP-01','$2y$10$xrhe857trcKAzOQpRUBJWOGNhDb07UsuFjCdCdpYzAIZ7bOEwIgpa',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(150,'EMP-1001','EMP-1001','$2y$10$xrhe857trcKAzOQpRUBJWOGNhDb07UsuFjCdCdpYzAIZ7bOEwIgpa',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(151,'EMP-1002','amina.karimova','$2y$10$xrhe857trcKAzOQpRUBJWOGNhDb07UsuFjCdCdpYzAIZ7bOEwIgpa',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(152,'EMP-1002','HR-VP-201','$2y$10$xrhe857trcKAzOQpRUBJWOGNhDb07UsuFjCdCdpYzAIZ7bOEwIgpa',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(153,'EMP-1002','EMP-1002','$2y$10$xrhe857trcKAzOQpRUBJWOGNhDb07UsuFjCdCdpYzAIZ7bOEwIgpa',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(154,'EMP-1003','daniel.weber','$2y$10$xrhe857trcKAzOQpRUBJWOGNhDb07UsuFjCdCdpYzAIZ7bOEwIgpa',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(155,'EMP-1003','FIN-VP-102','$2y$10$xrhe857trcKAzOQpRUBJWOGNhDb07UsuFjCdCdpYzAIZ7bOEwIgpa',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(156,'EMP-1003','FIN-VP-502','$2y$10$xrhe857trcKAzOQpRUBJWOGNhDb07UsuFjCdCdpYzAIZ7bOEwIgpa',0,'Active','2026-09-20 16:14:33','2026-09-20 16:11:28'),(157,'EMP-1003','EMP-1003','$2y$10$xrhe857trcKAzOQpRUBJWOGNhDb07UsuFjCdCdpYzAIZ7bOEwIgpa',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(158,'EMP-1004','elena.morozova','$2y$10$xrhe857trcKAzOQpRUBJWOGNhDb07UsuFjCdCdpYzAIZ7bOEwIgpa',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(159,'EMP-1004','EMP-1004','$2y$10$xrhe857trcKAzOQpRUBJWOGNhDb07UsuFjCdCdpYzAIZ7bOEwIgpa',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(160,'EMP-1005','timur.akhmetov','$2y$10$xrhe857trcKAzOQpRUBJWOGNhDb07UsuFjCdCdpYzAIZ7bOEwIgpa',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(161,'EMP-1005','EMP-1005','$2y$10$xrhe857trcKAzOQpRUBJWOGNhDb07UsuFjCdCdpYzAIZ7bOEwIgpa',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(162,'EMP-1006','pavel.orlov','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(163,'EMP-1006','CRM-VP-842','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(164,'EMP-1006','EMP-842','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(165,'EMP-1006','EMP-1006','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(166,'EMP-1007','sara.lindholm','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(167,'EMP-1007','EMP-1007','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(168,'EMP-1008','bekzod.rakhimov','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(169,'EMP-1008','EMP-1008','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(170,'EMP-1009','nadia.petrova','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(171,'EMP-1009','EMP-1009','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(172,'EMP-1010','markus.klein','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(173,'EMP-1010','EMP-1010','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(174,'EMP-1011','arman.tulegenov','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(175,'EMP-1011','EMP-1011','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(176,'EMP-1012','rustam.bekov','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(177,'EMP-1012','EMP-1012','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(178,'EMP-1013','ilona.vetra','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(179,'EMP-1013','EMP-1013','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(180,'EMP-1014','mikhail.antonov','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(181,'EMP-1014','EMP-1014','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(182,'EMP-1015','kamila.nurzhan','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(183,'EMP-1015','EMP-1015','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(184,'EMP-1016','erik.hansen','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(185,'EMP-1016','EMP-1016','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(186,'EMP-1017','dana.yermak','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(187,'EMP-1017','EMP-1017','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(188,'EMP-1018','leonid.volkov','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(189,'EMP-1018','IT-VP-304','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(190,'EMP-1018','EMP-1018','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(191,'EMP-1019','farida.iskakova','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(192,'EMP-1019','DOC-VP-501','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(193,'EMP-1019','CST-VP-09','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(194,'EMP-1019','EMP-VP-1019','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(195,'EMP-1019','EMP-1019','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(196,'EMP-1020','jonas.richter','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(197,'EMP-1020','DEV-VP-994','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(198,'EMP-1020','EMP-1020','$2y$10$pFkDJnHIAoPstzdg5LchReF94tzSBrFSou.pohf76.Hdx3GZzrw7S',0,'Active','2026-09-20 16:11:28','2026-09-20 16:11:28'),(199,'EMP-1001','viktor.sokolov','$2y$10$QR7f3aHwxHJCvCKNd7jSU.lPzZ/EkfiGYhrg8HfNbFXLdJJ0JoxA2',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(200,'EMP-1001','ADM-VP-01','$2y$10$QR7f3aHwxHJCvCKNd7jSU.lPzZ/EkfiGYhrg8HfNbFXLdJJ0JoxA2',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(201,'EMP-1001','EMP-1001','$2y$10$QR7f3aHwxHJCvCKNd7jSU.lPzZ/EkfiGYhrg8HfNbFXLdJJ0JoxA2',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(202,'EMP-1002','amina.karimova','$2y$10$QR7f3aHwxHJCvCKNd7jSU.lPzZ/EkfiGYhrg8HfNbFXLdJJ0JoxA2',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(203,'EMP-1002','HR-VP-201','$2y$10$QR7f3aHwxHJCvCKNd7jSU.lPzZ/EkfiGYhrg8HfNbFXLdJJ0JoxA2',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(204,'EMP-1002','HR-VP-104','$2y$10$QR7f3aHwxHJCvCKNd7jSU.lPzZ/EkfiGYhrg8HfNbFXLdJJ0JoxA2',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(205,'EMP-1002','EMP-1002','$2y$10$QR7f3aHwxHJCvCKNd7jSU.lPzZ/EkfiGYhrg8HfNbFXLdJJ0JoxA2',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(206,'EMP-1003','daniel.weber','$2y$10$QR7f3aHwxHJCvCKNd7jSU.lPzZ/EkfiGYhrg8HfNbFXLdJJ0JoxA2',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(207,'EMP-1003','FIN-VP-102','$2y$10$QR7f3aHwxHJCvCKNd7jSU.lPzZ/EkfiGYhrg8HfNbFXLdJJ0JoxA2',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(208,'EMP-1003','FIN-VP-502','$2y$10$QR7f3aHwxHJCvCKNd7jSU.lPzZ/EkfiGYhrg8HfNbFXLdJJ0JoxA2',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(209,'EMP-1003','EMP-1003','$2y$10$QR7f3aHwxHJCvCKNd7jSU.lPzZ/EkfiGYhrg8HfNbFXLdJJ0JoxA2',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(210,'EMP-1004','elena.morozova','$2y$10$QR7f3aHwxHJCvCKNd7jSU.lPzZ/EkfiGYhrg8HfNbFXLdJJ0JoxA2',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(211,'EMP-1004','EMP-1004','$2y$10$QR7f3aHwxHJCvCKNd7jSU.lPzZ/EkfiGYhrg8HfNbFXLdJJ0JoxA2',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(212,'EMP-1005','timur.akhmetov','$2y$10$QR7f3aHwxHJCvCKNd7jSU.lPzZ/EkfiGYhrg8HfNbFXLdJJ0JoxA2',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(213,'EMP-1005','EMP-1005','$2y$10$QR7f3aHwxHJCvCKNd7jSU.lPzZ/EkfiGYhrg8HfNbFXLdJJ0JoxA2',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(214,'EMP-1006','pavel.orlov','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(215,'EMP-1006','CRM-VP-842','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(216,'EMP-1006','EMP-842','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(217,'EMP-1006','EMP-1006','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(218,'EMP-1007','sara.lindholm','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(219,'EMP-1007','EMP-1007','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(220,'EMP-1008','bekzod.rakhimov','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(221,'EMP-1008','EMP-1008','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(222,'EMP-1009','nadia.petrova','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(223,'EMP-1009','EMP-1009','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(224,'EMP-1010','markus.klein','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(225,'EMP-1010','EMP-1010','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(226,'EMP-1011','arman.tulegenov','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(227,'EMP-1011','EMP-1011','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(228,'EMP-1012','rustam.bekov','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(229,'EMP-1012','EMP-1012','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(230,'EMP-1013','ilona.vetra','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(231,'EMP-1013','EMP-1013','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(232,'EMP-1014','mikhail.antonov','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(233,'EMP-1014','EMP-1014','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(234,'EMP-1015','kamila.nurzhan','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(235,'EMP-1015','EMP-1015','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(236,'EMP-1016','erik.hansen','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(237,'EMP-1016','EMP-1016','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(238,'EMP-1017','dana.yermak','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(239,'EMP-1017','EMP-1017','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(240,'EMP-1018','leonid.volkov','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(241,'EMP-1018','IT-VP-304','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(242,'EMP-1018','EMP-1018','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(243,'EMP-1019','farida.iskakova','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(244,'EMP-1019','DOC-VP-501','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(245,'EMP-1019','CST-VP-09','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(246,'EMP-1019','EMP-VP-1019','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(247,'EMP-1019','EMP-1019','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(248,'EMP-1020','jonas.richter','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(249,'EMP-1020','DEV-VP-994','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06'),(250,'EMP-1020','EMP-1020','$2y$10$obQbSn53bF0jazjsYbsH0eawaf0YK.jPDwxgszKLJNIPubpm33oY.',0,'Active','2026-09-20 16:12:06','2026-09-20 16:12:06');
/*!40000 ALTER TABLE `employee_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_offboarding`
--

DROP TABLE IF EXISTS `employee_offboarding`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_offboarding` (
  `offboarding_id` int(11) NOT NULL,
  `emp_id` varchar(10) NOT NULL,
  `step` enum('HRInitiated','StatusChanged','ITNotified','AccessRevoked','IntranetRevoked','FileCenterReviewed','CRMRevoked','HelpdeskClosed','GovernanceVerified','AuditLogged') NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_offboarding`
--

LOCK TABLES `employee_offboarding` WRITE;
/*!40000 ALTER TABLE `employee_offboarding` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_offboarding` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_onboarding`
--

DROP TABLE IF EXISTS `employee_onboarding`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_onboarding` (
  `onboarding_id` int(11) NOT NULL,
  `emp_id` varchar(10) NOT NULL,
  `step` enum('RecordCreated','AccessRequested','IntranetGranted','SystemAccessGranted','DocumentsFiled','GovernanceReviewed') NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_onboarding`
--

LOCK TABLES `employee_onboarding` WRITE;
/*!40000 ALTER TABLE `employee_onboarding` DISABLE KEYS */;
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
  `granted_by_emp_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_roles`
--

LOCK TABLES `employee_roles` WRITE;
/*!40000 ALTER TABLE `employee_roles` DISABLE KEYS */;
INSERT INTO `employee_roles` VALUES ('EMP-1001',1,'2026-09-20 16:05:39','EMP-1001'),('EMP-1001',1,'2026-09-20 16:05:39','EMP-1001'),('EMP-1001',1,'2026-09-20 16:05:39','EMP-1001'),('EMP-1002',7,'2026-09-20 16:05:39','EMP-1001'),('EMP-1002',7,'2026-09-20 16:05:39','EMP-1001'),('EMP-1002',7,'2026-09-20 16:05:39','EMP-1001'),('EMP-1003',6,'2026-09-20 16:05:39','EMP-1001'),('EMP-1003',6,'2026-09-20 16:05:39','EMP-1001'),('EMP-1003',6,'2026-09-20 16:05:39','EMP-1001'),('EMP-1004',4,'2026-09-20 16:05:39','EMP-1001'),('EMP-1004',4,'2026-09-20 16:05:39','EMP-1001'),('EMP-1005',2,'2026-09-20 16:05:39','EMP-1001'),('EMP-1005',2,'2026-09-20 16:05:39','EMP-1001'),('EMP-1006',3,'2026-09-20 16:05:39','EMP-1001'),('EMP-1006',3,'2026-09-20 16:05:39','EMP-1001'),('EMP-1006',3,'2026-09-20 16:05:39','EMP-1001'),('EMP-1006',3,'2026-09-20 16:05:39','EMP-1001'),('EMP-1007',3,'2026-09-20 16:05:39','EMP-1001'),('EMP-1007',3,'2026-09-20 16:05:39','EMP-1001'),('EMP-1008',3,'2026-09-20 16:05:39','EMP-1001'),('EMP-1008',3,'2026-09-20 16:05:39','EMP-1001'),('EMP-1009',3,'2026-09-20 16:05:39','EMP-1001'),('EMP-1009',3,'2026-09-20 16:05:39','EMP-1001'),('EMP-1010',3,'2026-09-20 16:05:39','EMP-1001'),('EMP-1010',3,'2026-09-20 16:05:39','EMP-1001'),('EMP-1011',8,'2026-09-20 16:05:39','EMP-1001'),('EMP-1011',8,'2026-09-20 16:05:39','EMP-1001'),('EMP-1012',8,'2026-09-20 16:05:39','EMP-1001'),('EMP-1012',8,'2026-09-20 16:05:39','EMP-1001'),('EMP-1013',8,'2026-09-20 16:05:39','EMP-1001'),('EMP-1013',8,'2026-09-20 16:05:39','EMP-1001'),('EMP-1014',8,'2026-09-20 16:05:39','EMP-1001'),('EMP-1014',8,'2026-09-20 16:05:39','EMP-1001'),('EMP-1015',8,'2026-09-20 16:05:39','EMP-1001'),('EMP-1015',8,'2026-09-20 16:05:39','EMP-1001'),('EMP-1016',4,'2026-09-20 16:05:39','EMP-1001'),('EMP-1016',4,'2026-09-20 16:05:39','EMP-1001'),('EMP-1017',4,'2026-09-20 16:05:39','EMP-1001'),('EMP-1017',4,'2026-09-20 16:05:39','EMP-1001'),('EMP-1018',5,'2026-09-20 16:05:39','EMP-1001'),('EMP-1018',5,'2026-09-20 16:05:39','EMP-1001'),('EMP-1018',5,'2026-09-20 16:05:39','EMP-1001'),('EMP-1019',4,'2026-09-20 16:05:39','EMP-1001'),('EMP-1019',4,'2026-09-20 16:05:39','EMP-1001'),('EMP-1019',4,'2026-09-20 16:05:39','EMP-1001'),('EMP-1020',4,'2026-09-20 16:05:39','EMP-1001'),('EMP-1020',4,'2026-09-20 16:05:39','EMP-1001'),('EMP-1020',4,'2026-09-20 16:05:39','EMP-1001'),('EMP-1001',1,'2026-09-20 16:10:27','EMP-1001'),('EMP-1001',1,'2026-09-20 16:10:27','EMP-1001'),('EMP-1001',1,'2026-09-20 16:10:27','EMP-1001'),('EMP-1002',7,'2026-09-20 16:10:27','EMP-1001'),('EMP-1002',7,'2026-09-20 16:10:27','EMP-1001'),('EMP-1002',7,'2026-09-20 16:10:27','EMP-1001'),('EMP-1003',6,'2026-09-20 16:10:27','EMP-1001'),('EMP-1003',6,'2026-09-20 16:10:27','EMP-1001'),('EMP-1003',6,'2026-09-20 16:10:27','EMP-1001'),('EMP-1004',4,'2026-09-20 16:10:27','EMP-1001'),('EMP-1004',4,'2026-09-20 16:10:27','EMP-1001'),('EMP-1005',2,'2026-09-20 16:10:27','EMP-1001'),('EMP-1005',2,'2026-09-20 16:10:27','EMP-1001'),('EMP-1006',3,'2026-09-20 16:10:27','EMP-1001'),('EMP-1006',3,'2026-09-20 16:10:27','EMP-1001'),('EMP-1006',3,'2026-09-20 16:10:27','EMP-1001'),('EMP-1006',3,'2026-09-20 16:10:27','EMP-1001'),('EMP-1007',3,'2026-09-20 16:10:27','EMP-1001'),('EMP-1007',3,'2026-09-20 16:10:27','EMP-1001'),('EMP-1008',3,'2026-09-20 16:10:27','EMP-1001'),('EMP-1008',3,'2026-09-20 16:10:27','EMP-1001'),('EMP-1009',3,'2026-09-20 16:10:27','EMP-1001'),('EMP-1009',3,'2026-09-20 16:10:27','EMP-1001'),('EMP-1010',3,'2026-09-20 16:10:27','EMP-1001'),('EMP-1010',3,'2026-09-20 16:10:27','EMP-1001'),('EMP-1011',8,'2026-09-20 16:10:27','EMP-1001'),('EMP-1011',8,'2026-09-20 16:10:27','EMP-1001'),('EMP-1012',8,'2026-09-20 16:10:27','EMP-1001'),('EMP-1012',8,'2026-09-20 16:10:27','EMP-1001'),('EMP-1013',8,'2026-09-20 16:10:27','EMP-1001'),('EMP-1013',8,'2026-09-20 16:10:27','EMP-1001'),('EMP-1014',8,'2026-09-20 16:10:27','EMP-1001'),('EMP-1014',8,'2026-09-20 16:10:27','EMP-1001'),('EMP-1015',8,'2026-09-20 16:10:27','EMP-1001'),('EMP-1015',8,'2026-09-20 16:10:27','EMP-1001'),('EMP-1016',4,'2026-09-20 16:10:27','EMP-1001'),('EMP-1016',4,'2026-09-20 16:10:27','EMP-1001'),('EMP-1017',4,'2026-09-20 16:10:27','EMP-1001'),('EMP-1017',4,'2026-09-20 16:10:27','EMP-1001'),('EMP-1018',5,'2026-09-20 16:10:27','EMP-1001'),('EMP-1018',5,'2026-09-20 16:10:27','EMP-1001'),('EMP-1018',5,'2026-09-20 16:10:27','EMP-1001'),('EMP-1019',4,'2026-09-20 16:10:27','EMP-1001'),('EMP-1019',4,'2026-09-20 16:10:27','EMP-1001'),('EMP-1019',4,'2026-09-20 16:10:27','EMP-1001'),('EMP-1019',4,'2026-09-20 16:10:27','EMP-1001'),('EMP-1020',4,'2026-09-20 16:10:27','EMP-1001'),('EMP-1020',4,'2026-09-20 16:10:27','EMP-1001'),('EMP-1020',4,'2026-09-20 16:10:27','EMP-1001'),('EMP-1001',1,'2026-09-20 16:10:58','EMP-1001'),('EMP-1001',1,'2026-09-20 16:10:58','EMP-1001'),('EMP-1001',1,'2026-09-20 16:10:58','EMP-1001'),('EMP-1002',7,'2026-09-20 16:10:58','EMP-1001'),('EMP-1002',7,'2026-09-20 16:10:58','EMP-1001'),('EMP-1002',7,'2026-09-20 16:10:58','EMP-1001'),('EMP-1003',6,'2026-09-20 16:10:58','EMP-1001'),('EMP-1003',6,'2026-09-20 16:10:58','EMP-1001'),('EMP-1003',6,'2026-09-20 16:10:58','EMP-1001'),('EMP-1004',4,'2026-09-20 16:10:58','EMP-1001'),('EMP-1004',4,'2026-09-20 16:10:58','EMP-1001'),('EMP-1005',2,'2026-09-20 16:10:58','EMP-1001'),('EMP-1005',2,'2026-09-20 16:10:58','EMP-1001'),('EMP-1006',3,'2026-09-20 16:10:58','EMP-1001'),('EMP-1006',3,'2026-09-20 16:10:58','EMP-1001'),('EMP-1006',3,'2026-09-20 16:10:58','EMP-1001'),('EMP-1006',3,'2026-09-20 16:10:58','EMP-1001'),('EMP-1007',3,'2026-09-20 16:10:58','EMP-1001'),('EMP-1007',3,'2026-09-20 16:10:58','EMP-1001'),('EMP-1008',3,'2026-09-20 16:10:58','EMP-1001'),('EMP-1008',3,'2026-09-20 16:10:58','EMP-1001'),('EMP-1009',3,'2026-09-20 16:10:58','EMP-1001'),('EMP-1009',3,'2026-09-20 16:10:58','EMP-1001'),('EMP-1010',3,'2026-09-20 16:10:58','EMP-1001'),('EMP-1010',3,'2026-09-20 16:10:58','EMP-1001'),('EMP-1011',8,'2026-09-20 16:10:58','EMP-1001'),('EMP-1011',8,'2026-09-20 16:10:58','EMP-1001'),('EMP-1012',8,'2026-09-20 16:10:58','EMP-1001'),('EMP-1012',8,'2026-09-20 16:10:58','EMP-1001'),('EMP-1013',8,'2026-09-20 16:10:58','EMP-1001'),('EMP-1013',8,'2026-09-20 16:10:58','EMP-1001'),('EMP-1014',8,'2026-09-20 16:10:58','EMP-1001'),('EMP-1014',8,'2026-09-20 16:10:58','EMP-1001'),('EMP-1015',8,'2026-09-20 16:10:58','EMP-1001'),('EMP-1015',8,'2026-09-20 16:10:58','EMP-1001'),('EMP-1016',4,'2026-09-20 16:10:58','EMP-1001'),('EMP-1016',4,'2026-09-20 16:10:58','EMP-1001'),('EMP-1017',4,'2026-09-20 16:10:58','EMP-1001'),('EMP-1017',4,'2026-09-20 16:10:58','EMP-1001'),('EMP-1018',5,'2026-09-20 16:10:58','EMP-1001'),('EMP-1018',5,'2026-09-20 16:10:58','EMP-1001'),('EMP-1018',5,'2026-09-20 16:10:58','EMP-1001'),('EMP-1019',4,'2026-09-20 16:10:58','EMP-1001'),('EMP-1019',4,'2026-09-20 16:10:58','EMP-1001'),('EMP-1019',4,'2026-09-20 16:10:58','EMP-1001'),('EMP-1019',4,'2026-09-20 16:10:58','EMP-1001'),('EMP-1019',4,'2026-09-20 16:10:58','EMP-1001'),('EMP-1020',4,'2026-09-20 16:10:58','EMP-1001'),('EMP-1020',4,'2026-09-20 16:10:58','EMP-1001'),('EMP-1020',4,'2026-09-20 16:10:58','EMP-1001'),('EMP-1001',1,'2026-09-20 16:11:28','EMP-1001'),('EMP-1001',1,'2026-09-20 16:11:28','EMP-1001'),('EMP-1001',1,'2026-09-20 16:11:28','EMP-1001'),('EMP-1002',7,'2026-09-20 16:11:28','EMP-1001'),('EMP-1002',7,'2026-09-20 16:11:28','EMP-1001'),('EMP-1002',7,'2026-09-20 16:11:28','EMP-1001'),('EMP-1003',6,'2026-09-20 16:11:28','EMP-1001'),('EMP-1003',6,'2026-09-20 16:11:28','EMP-1001'),('EMP-1003',6,'2026-09-20 16:11:28','EMP-1001'),('EMP-1003',6,'2026-09-20 16:11:28','EMP-1001'),('EMP-1004',4,'2026-09-20 16:11:28','EMP-1001'),('EMP-1004',4,'2026-09-20 16:11:28','EMP-1001'),('EMP-1005',2,'2026-09-20 16:11:28','EMP-1001'),('EMP-1005',2,'2026-09-20 16:11:28','EMP-1001'),('EMP-1006',3,'2026-09-20 16:11:28','EMP-1001'),('EMP-1006',3,'2026-09-20 16:11:28','EMP-1001'),('EMP-1006',3,'2026-09-20 16:11:28','EMP-1001'),('EMP-1006',3,'2026-09-20 16:11:28','EMP-1001'),('EMP-1007',3,'2026-09-20 16:11:28','EMP-1001'),('EMP-1007',3,'2026-09-20 16:11:28','EMP-1001'),('EMP-1008',3,'2026-09-20 16:11:28','EMP-1001'),('EMP-1008',3,'2026-09-20 16:11:28','EMP-1001'),('EMP-1009',3,'2026-09-20 16:11:28','EMP-1001'),('EMP-1009',3,'2026-09-20 16:11:28','EMP-1001'),('EMP-1010',3,'2026-09-20 16:11:28','EMP-1001'),('EMP-1010',3,'2026-09-20 16:11:28','EMP-1001'),('EMP-1011',8,'2026-09-20 16:11:28','EMP-1001'),('EMP-1011',8,'2026-09-20 16:11:28','EMP-1001'),('EMP-1012',8,'2026-09-20 16:11:28','EMP-1001'),('EMP-1012',8,'2026-09-20 16:11:28','EMP-1001'),('EMP-1013',8,'2026-09-20 16:11:28','EMP-1001'),('EMP-1013',8,'2026-09-20 16:11:28','EMP-1001'),('EMP-1014',8,'2026-09-20 16:11:28','EMP-1001'),('EMP-1014',8,'2026-09-20 16:11:28','EMP-1001'),('EMP-1015',8,'2026-09-20 16:11:28','EMP-1001'),('EMP-1015',8,'2026-09-20 16:11:28','EMP-1001'),('EMP-1016',4,'2026-09-20 16:11:28','EMP-1001'),('EMP-1016',4,'2026-09-20 16:11:28','EMP-1001'),('EMP-1017',4,'2026-09-20 16:11:28','EMP-1001'),('EMP-1017',4,'2026-09-20 16:11:28','EMP-1001'),('EMP-1018',5,'2026-09-20 16:11:28','EMP-1001'),('EMP-1018',5,'2026-09-20 16:11:28','EMP-1001'),('EMP-1018',5,'2026-09-20 16:11:28','EMP-1001'),('EMP-1019',4,'2026-09-20 16:11:28','EMP-1001'),('EMP-1019',4,'2026-09-20 16:11:28','EMP-1001'),('EMP-1019',4,'2026-09-20 16:11:28','EMP-1001'),('EMP-1019',4,'2026-09-20 16:11:28','EMP-1001'),('EMP-1019',4,'2026-09-20 16:11:28','EMP-1001'),('EMP-1020',4,'2026-09-20 16:11:28','EMP-1001'),('EMP-1020',4,'2026-09-20 16:11:28','EMP-1001'),('EMP-1020',4,'2026-09-20 16:11:28','EMP-1001'),('EMP-1001',1,'2026-09-20 16:12:06','EMP-1001'),('EMP-1001',1,'2026-09-20 16:12:06','EMP-1001'),('EMP-1001',1,'2026-09-20 16:12:06','EMP-1001'),('EMP-1002',7,'2026-09-20 16:12:06','EMP-1001'),('EMP-1002',7,'2026-09-20 16:12:06','EMP-1001'),('EMP-1002',7,'2026-09-20 16:12:06','EMP-1001'),('EMP-1002',7,'2026-09-20 16:12:06','EMP-1001'),('EMP-1003',6,'2026-09-20 16:12:06','EMP-1001'),('EMP-1003',6,'2026-09-20 16:12:06','EMP-1001'),('EMP-1003',6,'2026-09-20 16:12:06','EMP-1001'),('EMP-1003',6,'2026-09-20 16:12:06','EMP-1001'),('EMP-1004',4,'2026-09-20 16:12:06','EMP-1001'),('EMP-1004',4,'2026-09-20 16:12:06','EMP-1001'),('EMP-1005',2,'2026-09-20 16:12:06','EMP-1001'),('EMP-1005',2,'2026-09-20 16:12:06','EMP-1001'),('EMP-1006',3,'2026-09-20 16:12:06','EMP-1001'),('EMP-1006',3,'2026-09-20 16:12:06','EMP-1001'),('EMP-1006',3,'2026-09-20 16:12:06','EMP-1001'),('EMP-1006',3,'2026-09-20 16:12:06','EMP-1001'),('EMP-1007',3,'2026-09-20 16:12:06','EMP-1001'),('EMP-1007',3,'2026-09-20 16:12:06','EMP-1001'),('EMP-1008',3,'2026-09-20 16:12:06','EMP-1001'),('EMP-1008',3,'2026-09-20 16:12:06','EMP-1001'),('EMP-1009',3,'2026-09-20 16:12:06','EMP-1001'),('EMP-1009',3,'2026-09-20 16:12:06','EMP-1001'),('EMP-1010',3,'2026-09-20 16:12:06','EMP-1001'),('EMP-1010',3,'2026-09-20 16:12:06','EMP-1001'),('EMP-1011',8,'2026-09-20 16:12:06','EMP-1001'),('EMP-1011',8,'2026-09-20 16:12:06','EMP-1001'),('EMP-1012',8,'2026-09-20 16:12:06','EMP-1001'),('EMP-1012',8,'2026-09-20 16:12:06','EMP-1001'),('EMP-1013',8,'2026-09-20 16:12:06','EMP-1001'),('EMP-1013',8,'2026-09-20 16:12:06','EMP-1001'),('EMP-1014',8,'2026-09-20 16:12:06','EMP-1001'),('EMP-1014',8,'2026-09-20 16:12:06','EMP-1001'),('EMP-1015',8,'2026-09-20 16:12:06','EMP-1001'),('EMP-1015',8,'2026-09-20 16:12:06','EMP-1001'),('EMP-1016',4,'2026-09-20 16:12:06','EMP-1001'),('EMP-1016',4,'2026-09-20 16:12:06','EMP-1001'),('EMP-1017',4,'2026-09-20 16:12:06','EMP-1001'),('EMP-1017',4,'2026-09-20 16:12:06','EMP-1001'),('EMP-1018',5,'2026-09-20 16:12:06','EMP-1001'),('EMP-1018',5,'2026-09-20 16:12:06','EMP-1001'),('EMP-1018',5,'2026-09-20 16:12:06','EMP-1001'),('EMP-1019',4,'2026-09-20 16:12:06','EMP-1001'),('EMP-1019',4,'2026-09-20 16:12:06','EMP-1001'),('EMP-1019',4,'2026-09-20 16:12:06','EMP-1001'),('EMP-1019',4,'2026-09-20 16:12:06','EMP-1001'),('EMP-1019',4,'2026-09-20 16:12:06','EMP-1001'),('EMP-1020',4,'2026-09-20 16:12:06','EMP-1001'),('EMP-1020',4,'2026-09-20 16:12:06','EMP-1001'),('EMP-1020',4,'2026-09-20 16:12:06','EMP-1001');
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
INSERT INTO `employees` VALUES ('EMP-1001','Viktor Sokolov','Chief Executive Officer (CEO)','EXE','L4','viktor.sokolov@vostokpribor.local',NULL,'Active'),('EMP-1002','Amina Karimova','Chief Operating Officer (COO)','EXE','L4','amina.karimova@vostokpribor.local','EMP-1001','Active'),('EMP-1003','Daniel Weber','Chief Financial Officer (CFO)','EXE','L4','daniel.weber@vostokpribor.local','EMP-1001','Active'),('EMP-1004','Elena Morozova','Chief Technology Officer (CTO)','EXE','L4','elena.morozova@vostokpribor.local','EMP-1001','Active'),('EMP-1005','Timur Akhmetov','Chief Governance Officer','EXE','L4','timur.akhmetov@vostokpribor.local','EMP-1001','Active'),('EMP-1006','Pavel Orlov','Sales Director','SAL','L3','pavel.orlov@vostokpribor.local','EMP-1001','Active'),('EMP-1007','Sara Lindholm','Senior Account Manager','SAL','L3','sara.lindholm@vostokpribor.local','EMP-1006','Active'),('EMP-1008','Bekzod Rakhimov','Account Manager','SAL','L3','bekzod.rakhimov@vostokpribor.local','EMP-1006','Active'),('EMP-1009','Nadia Petrova','Strategic Sales Manager','SAL','L3','nadia.petrova@vostokpribor.local','EMP-1006','Active'),('EMP-1010','Markus Klein','Regional Sales Manager','SAL','L3','markus.klein@vostokpribor.local','EMP-1006','Active'),('EMP-1011','Arman Tulegenov','Operations Manager','OPS','L3','arman.tulegenov@vostokpribor.local','EMP-1002','Active'),('EMP-1012','Rustam Bekov','Logistics Manager','OPS','L3','rustam.bekov@vostokpribor.local','EMP-1011','Active'),('EMP-1013','Ilona Vetra','Procurement Manager','OPS','L3','ilona.vetra@vostokpribor.local','EMP-1011','Active'),('EMP-1014','Mikhail Antonov','Warehouse Supervisor','OPS','L2','mikhail.antonov@vostokpribor.local','EMP-1011','Active'),('EMP-1015','Kamila Nurzhan','Supply Chain Analyst','OPS','L2','kamila.nurzhan@vostokpribor.local','EMP-1011','Active'),('EMP-1016','Erik Hansen','Senior Automation Engineer','ENG','L3','erik.hansen@vostokpribor.local','EMP-1004','Active'),('EMP-1017','Dana Yermak','Software Integration Engineer','ENG','L3','dana.yermak@vostokpribor.local','EMP-1004','Active'),('EMP-1018','Leonid Volkov','Systems Engineer','ENG','L3','leonid.volkov@vostokpribor.local','EMP-1004','Active'),('EMP-1019','Farida Iskakova','Project Manager','ENG','L3','farida.iskakova@vostokpribor.local','EMP-1004','Active'),('EMP-1020','Jonas Richter','Senior Developer','ENG','L3','jonas.richter@vostokpribor.local','EMP-1004','Active');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `integration_logs`
--

DROP TABLE IF EXISTS `integration_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_logs` (
  `log_id` bigint(20) NOT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `endpoint` varchar(200) DEFAULT NULL,
  `request_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `response_status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `integration_logs`
--

LOCK TABLES `integration_logs` WRITE;
/*!40000 ALTER TABLE `integration_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `integration_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `integration_requirements`
--

DROP TABLE IF EXISTS `integration_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_requirements` (
  `req_id` int(11) NOT NULL,
  `cus_id` varchar(10) DEFAULT NULL,
  `prj_id` varchar(15) DEFAULT NULL,
  `reviewed_by_emp_id` varchar(10) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(30) DEFAULT 'UnderReview'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `integration_requirements`
--

LOCK TABLES `integration_requirements` WRITE;
/*!40000 ALTER TABLE `integration_requirements` DISABLE KEYS */;
/*!40000 ALTER TABLE `integration_requirements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `internal_policies`
--

DROP TABLE IF EXISTS `internal_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `internal_policies` (
  `policy_id` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `doc_id` varchar(15) DEFAULT NULL,
  `effective_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `internal_policies`
--

LOCK TABLES `internal_policies` WRITE;
/*!40000 ALTER TABLE `internal_policies` DISABLE KEYS */;
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
INSERT INTO `invoices` VALUES ('INV-2026-001','CUS-1001','PRJ-2026-001',46250.00,'EUR','Paid'),('INV-2026-002','CUS-1002','PRJ-2026-002',80000.00,'EUR','Pending'),('INV-2026-003','CUS-1003','PRJ-2026-003',137500.00,'EUR','Paid'),('INV-2026-004','CUS-1004','PRJ-2026-004',55000.00,'EUR','Pending'),('INV-2026-005','CUS-1005','PRJ-2026-005',42000.00,'EUR','Paid'),('INV-2026-006','CUS-1006','PRJ-2026-006',32000.00,'EUR','Pending'),('INV-2026-007','CUS-1007','PRJ-2026-007',105000.00,'EUR','Paid'),('INV-2026-008','CUS-1008','PRJ-2026-008',68333.00,'EUR','Pending'),('INV-2026-009','CUS-1009','PRJ-2026-012',47333.00,'EUR','Paid'),('INV-2026-010','CUS-1010','PRJ-2026-010',91667.00,'EUR','Pending');
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ip_addresses`
--

DROP TABLE IF EXISTS `ip_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip_addresses` (
  `ip_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `device_id` int(11) DEFAULT NULL,
  `is_internal` tinyint(1) DEFAULT 1,
  `geo_country` varchar(100) DEFAULT NULL,
  `first_seen_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_seen_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ip_addresses`
--

LOCK TABLES `ip_addresses` WRITE;
/*!40000 ALTER TABLE `ip_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `ip_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `it_assets`
--

DROP TABLE IF EXISTS `it_assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `it_assets` (
  `asset_id` int(11) NOT NULL,
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
  `last_seen_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `it_assets`
--

LOCK TABLES `it_assets` WRITE;
/*!40000 ALTER TABLE `it_assets` DISABLE KEYS */;
/*!40000 ALTER TABLE `it_assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_postings`
--

DROP TABLE IF EXISTS `job_postings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_postings` (
  `posting_id` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `department_code` varchar(4) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 1,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_postings`
--

LOCK TABLES `job_postings` WRITE;
/*!40000 ALTER TABLE `job_postings` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_postings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `knowledge_base_articles`
--

DROP TABLE IF EXISTS `knowledge_base_articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `knowledge_base_articles` (
  `kb_id` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `created_by_emp_id` varchar(10) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `knowledge_base_articles`
--

LOCK TABLES `knowledge_base_articles` WRITE;
/*!40000 ALTER TABLE `knowledge_base_articles` DISABLE KEYS */;
/*!40000 ALTER TABLE `knowledge_base_articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads`
--

LOCK TABLES `leads` WRITE;
/*!40000 ALTER TABLE `leads` DISABLE KEYS */;
/*!40000 ALTER TABLE `leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_requests`
--

DROP TABLE IF EXISTS `leave_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leave_requests` (
  `leave_id` int(11) NOT NULL,
  `emp_id` varchar(10) NOT NULL,
  `leave_type` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `approved_by_emp_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_requests`
--

LOCK TABLES `leave_requests` WRITE;
/*!40000 ALTER TABLE `leave_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `leave_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opportunities`
--

DROP TABLE IF EXISTS `opportunities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `opportunities` (
  `opp_id` int(11) NOT NULL,
  `lead_id` int(11) DEFAULT NULL,
  `cus_id` varchar(10) DEFAULT NULL,
  `sales_emp_id` varchar(10) DEFAULT NULL,
  `stage` enum('Qualification','Proposal','Negotiation','Won','Lost') DEFAULT 'Qualification',
  `estimated_value` decimal(14,2) DEFAULT NULL,
  `expected_close_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opportunities`
--

LOCK TABLES `opportunities` WRITE;
/*!40000 ALTER TABLE `opportunities` DISABLE KEYS */;
/*!40000 ALTER TABLE `opportunities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `prod_id` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `cus_id` varchar(10) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(30) DEFAULT 'Cart',
  `total_amount` decimal(14,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `inv_id` varchar(15) NOT NULL,
  `amount` decimal(14,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `reconciled` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_name` varchar(150) DEFAULT NULL,
  `system_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portal_accounts`
--

DROP TABLE IF EXISTS `portal_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portal_accounts` (
  `portal_user_id` int(11) NOT NULL,
  `cus_id` varchar(10) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `mfa_enabled` tinyint(1) DEFAULT 0,
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portal_accounts`
--

LOCK TABLES `portal_accounts` WRITE;
/*!40000 ALTER TABLE `portal_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `portal_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portal_notifications`
--

DROP TABLE IF EXISTS `portal_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portal_notifications` (
  `notification_id` int(11) NOT NULL,
  `portal_user_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `related_entity_type` varchar(30) DEFAULT NULL,
  `related_entity_id` varchar(15) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portal_notifications`
--

LOCK TABLES `portal_notifications` WRITE;
/*!40000 ALTER TABLE `portal_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `portal_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portal_sessions`
--

DROP TABLE IF EXISTS `portal_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portal_sessions` (
  `session_id` int(11) NOT NULL,
  `portal_user_id` int(11) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `logout_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portal_sessions`
--

LOCK TABLES `portal_sessions` WRITE;
/*!40000 ALTER TABLE `portal_sessions` DISABLE KEYS */;
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
  PRIMARY KEY (`prod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_inventory`
--

LOCK TABLES `product_inventory` WRITE;
/*!40000 ALTER TABLE `product_inventory` DISABLE KEYS */;
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
INSERT INTO `projects` VALUES ('PRJ-2026-001','CUS-1001','EMP-1019',185000.00,'EUR','Execution'),('PRJ-2026-002','CUS-1002','EMP-1019',240000.00,'EUR','Integration'),('PRJ-2026-003','CUS-1003','EMP-1016',410000.00,'EUR','Procurement'),('PRJ-2026-004','CUS-1004','EMP-1019',165000.00,'EUR','Execution'),('PRJ-2026-005','CUS-1005','EMP-1017',128000.00,'EUR','Integration'),('PRJ-2026-006','CUS-1006','EMP-1016',96000.00,'EUR','Design'),('PRJ-2026-007','CUS-1007','EMP-1019',315000.00,'EUR','Integration'),('PRJ-2026-008','CUS-1008','EMP-1017',205000.00,'EUR','Execution'),('PRJ-2026-009','CUS-1001','EMP-1019',275000.00,'EUR','Design'),('PRJ-2026-010','CUS-1002','EMP-1017',74000.00,'EUR','ContractReview'),('PRJ-2026-011','CUS-1005','EMP-1016',188000.00,'EUR','Testing'),('PRJ-2026-012','CUS-1009','EMP-1016',142000.00,'EUR','Maintenance'),('PRJ-2026-013','CUS-1010','EMP-1019',112000.00,'EUR','Procurement'),('PRJ-2026-014','CUS-1007','EMP-1017',260000.00,'EUR','Procurement'),('PRJ-2026-015','CUS-1010','EMP-1016',151000.00,'EUR','Planning');
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
  PRIMARY KEY (`quote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quotes`
--

LOCK TABLES `quotes` WRITE;
/*!40000 ALTER TABLE `quotes` DISABLE KEYS */;
/*!40000 ALTER TABLE `quotes` ENABLE KEYS */;
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
  PRIMARY KEY (`risk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_register`
--

LOCK TABLES `risk_register` WRITE;
/*!40000 ALTER TABLE `risk_register` DISABLE KEYS */;
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
  PRIMARY KEY (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
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
  `access_level` varchar(30) DEFAULT 'Full',
  PRIMARY KEY (`role_id`,`system_id`)
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
  `role_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`role_id`)
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
  PRIMARY KEY (`tkt_id`),
  KEY `fk_tickets_assigned_emp_id` (`assigned_emp_id`),
  KEY `fk_tickets_requester_cus_id` (`requester_cus_id`),
  KEY `fk_tickets_requester_emp_id` (`requester_emp_id`),
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
INSERT INTO `tickets` VALUES ('TKT-2026-001','Customer','CUS-1002',NULL,'Customer Portal','High','EMP-1018','InProgress'),('TKT-2026-002','Employee',NULL,'EMP-1007','CRM','Medium','EMP-1018','Resolved'),('TKT-2026-003','Customer','CUS-1004',NULL,'E-Commerce','High','EMP-1018','Investigating'),('TKT-2026-004','Employee',NULL,'EMP-1015','Intranet','Low','EMP-1018','Resolved'),('TKT-2026-005','Customer','CUS-1007',NULL,'Customer Portal','Critical','EMP-1018','Escalated'),('TKT-2026-006','Employee',NULL,'EMP-1020','Developer Portal','Medium','EMP-1018','Resolved'),('TKT-2026-007','Customer','CUS-1005',NULL,'E-Commerce','Medium','EMP-1018','Resolved'),('TKT-2026-008','Employee',NULL,'EMP-1016','File Center','Medium','EMP-1018','InProgress'),('TKT-2026-009','Customer','CUS-1001',NULL,'Customer Portal','Low','EMP-1018','Resolved'),('TKT-2026-010','Employee',NULL,'EMP-1017','Developer Portal','High','EMP-1018','Investigating'),('TKT-2026-011','Customer','CUS-1008',NULL,'Customer Portal','High','EMP-1018','Escalated'),('TKT-2026-012','Employee',NULL,'EMP-1013','Intranet','Medium','EMP-1018','Resolved'),('TKT-2026-013','Customer','CUS-1009',NULL,'E-Commerce','Low','EMP-1018','Resolved'),('TKT-2026-014','Employee',NULL,'EMP-1019','File Center','Medium','EMP-1018','InProgress'),('TKT-2026-015','Customer','CUS-1010',NULL,'Customer Portal','High','EMP-1018','Investigating');
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_sessions`
--

DROP TABLE IF EXISTS `user_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_sessions` (
  `session_id` varchar(64) NOT NULL,
  `account_type` varchar(20) NOT NULL,
  `employee_account_id` int(11) DEFAULT NULL,
  `customer_account_id` int(11) DEFAULT NULL,
  `system_id` varchar(4) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_sessions`
--

LOCK TABLES `user_sessions` WRITE;
/*!40000 ALTER TABLE `user_sessions` DISABLE KEYS */;
INSERT INTO `user_sessions` VALUES ('0hlc95vdrabn2k5r8620clpifg','Employee',47,NULL,'DEV','127.0.0.1','curl/8.21.0','Active','2026-09-20 16:07:52','2026-09-21 00:07:52'),('17o014m3e0ephkobmp87m9r2gh','Employee',2,NULL,'ADM','127.0.0.1','curl/8.21.0','Active','2026-09-20 16:07:46','2026-09-21 00:07:46'),('50ovasektdkj4cjh10oev11uts','Employee',5,NULL,'HR','127.0.0.1','Unknown','Active','2026-09-20 16:14:33','2026-09-21 00:14:33'),('61lhog85l44nc9s73eo7qm8q8u','Customer',NULL,2,'CUS','127.0.0.1','Unknown','Active','2026-09-20 16:14:32','2026-09-21 00:14:32'),('bdks6vr1oibg7dprjdqc4k3sm1','Employee',41,NULL,'IT','127.0.0.1','Unknown','Active','2026-09-20 16:14:33','2026-09-21 00:14:33'),('c8769tfu0r3irrkrc4251lr975','Employee',93,NULL,'EMP','127.0.0.1','Unknown','Active','2026-09-20 16:14:33','2026-09-21 00:14:33'),('mdslfunsks6ev3t2377fgo0ipl','Customer',NULL,2,'CUS','127.0.0.1','curl/8.21.0','Active','2026-09-20 16:08:00','2026-09-21 00:08:00'),('muvoq1581e1mj7s64vnlbuftdt','Employee',156,NULL,'FIN','127.0.0.1','Unknown','Active','2026-09-20 16:14:33','2026-09-21 00:14:33'),('opt13ko3f2h2g9cnled0l36sm0','Employee',15,NULL,'CRM','127.0.0.1','Unknown','Active','2026-09-20 16:14:32','2026-09-21 00:14:32'),('qovsvuic0b25go4gla28vgga6d','Employee',142,NULL,'DOC','127.0.0.1','Unknown','Active','2026-09-20 16:14:33','2026-09-21 00:14:33'),('s3l5bqrniqfa3h2b3g5rhp7a3r','Customer',NULL,5,'SHP','127.0.0.1','Unknown','Active','2026-09-20 16:14:33','2026-09-21 00:14:33'),('s78ib2jb9bijmeanjpdev92b3h','Employee',2,NULL,'ADM','127.0.0.1','Unknown','Active','2026-09-20 16:14:32','2026-09-21 00:14:32'),('ul3lf1bfklpgj2vklck6quarhn','Employee',47,NULL,'DEV','127.0.0.1','Unknown','Active','2026-09-20 16:14:32','2026-09-21 00:14:32');
/*!40000 ALTER TABLE `user_sessions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-20 19:14:41
