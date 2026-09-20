-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2026 at 08:29 PM
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
) ;

-- --------------------------------------------------------

--
-- Table structure for table `billing_cycles`
--

CREATE TABLE `billing_cycles` (
  `cycle_id` int(11) NOT NULL,
  `prj_id` varchar(15) NOT NULL,
  `milestone_description` varchar(200) DEFAULT NULL,
  `scheduled_date` date DEFAULT NULL,
  `invoiced` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `doc_id` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `onboarded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_pricing`
--

CREATE TABLE `customer_pricing` (
  `cus_id` varchar(10) NOT NULL,
  `prod_id` varchar(10) NOT NULL,
  `special_price` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `doc_id` varchar(15) NOT NULL,
  `file_name` varchar(200) NOT NULL,
  `classification` enum('Public','Internal','Confidential','TopSecret') NOT NULL,
  `owning_system` varchar(50) DEFAULT NULL,
  `owner_emp_id` varchar(10) DEFAULT NULL,
  `related_prj_id` varchar(15) DEFAULT NULL,
  `related_cus_id` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `access_type` enum('View','Download','Edit','Delete') DEFAULT NULL,
  `success` tinyint(1) DEFAULT 1,
  `accessed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_approvals`
--

CREATE TABLE `document_approvals` (
  `approval_id` int(11) NOT NULL,
  `doc_id` varchar(15) NOT NULL,
  `reviewer_emp_id` varchar(10) DEFAULT NULL,
  `decision` enum('Approved','Rejected','Pending') DEFAULT 'Pending',
  `decision_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `paid_at` date DEFAULT NULL
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

-- --------------------------------------------------------

--
-- Table structure for table `it_assets`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `job_postings`
--

CREATE TABLE `job_postings` (
  `posting_id` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `department_code` varchar(4) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 1,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_base_articles`
--

CREATE TABLE `knowledge_base_articles` (
  `kb_id` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `created_by_emp_id` varchar(10) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `opportunities`
--

CREATE TABLE `opportunities` (
  `opp_id` int(11) NOT NULL,
  `lead_id` int(11) DEFAULT NULL,
  `cus_id` varchar(10) DEFAULT NULL,
  `sales_emp_id` varchar(10) DEFAULT NULL,
  `stage` enum('Qualification','Proposal','Negotiation','Won','Lost') DEFAULT 'Qualification',
  `estimated_value` decimal(14,2) DEFAULT NULL,
  `expected_close_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `inv_id` varchar(15) NOT NULL,
  `amount` decimal(14,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `reconciled` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_name` varchar(150) DEFAULT NULL,
  `system_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `prj_id` varchar(15) NOT NULL,
  `cus_id` varchar(10) NOT NULL,
  `project_manager_emp_id` varchar(10) DEFAULT NULL,
  `budget` decimal(14,2) DEFAULT NULL,
  `currency` char(3) DEFAULT 'EUR',
  `status` enum('Planning','Procurement','Design','Integration','Testing','Execution','ContractReview','Maintenance','Closed') DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `review_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_system_access`
--

CREATE TABLE `role_system_access` (
  `role_id` int(11) NOT NULL,
  `system_id` varchar(4) NOT NULL,
  `access_level` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_forecasts`
--

CREATE TABLE `sales_forecasts` (
  `forecast_id` int(11) NOT NULL,
  `sales_emp_id` varchar(10) DEFAULT NULL,
  `period` varchar(20) DEFAULT NULL,
  `forecast_amount` decimal(14,2) DEFAULT NULL,
  `actual_amount` decimal(14,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `closed_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `security_policies`
--

CREATE TABLE `security_policies` (
  `policy_id` int(11) NOT NULL,
  `doc_id` varchar(15) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `effective_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sla_policies`
--

CREATE TABLE `sla_policies` (
  `sla_id` int(11) NOT NULL,
  `priority` enum('Low','Medium','High','Critical') DEFAULT NULL,
  `response_time_hours` int(11) DEFAULT NULL,
  `resolution_time_hours` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `systems_catalog`
--

CREATE TABLE `systems_catalog` (
  `system_id` varchar(4) NOT NULL,
  `system_name` varchar(100) DEFAULT NULL,
  `fqdn` varchar(100) DEFAULT NULL,
  `criticality` varchar(30) DEFAULT NULL,
  `trust_zone` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `priority` enum('Low','Medium','High','Critical') NOT NULL,
  `assigned_emp_id` varchar(10) DEFAULT NULL,
  `status` enum('Open','InProgress','Investigating','Escalated','Resolved') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_comments`
--

CREATE TABLE `ticket_comments` (
  `comment_id` int(11) NOT NULL,
  `tkt_id` varchar(15) NOT NULL,
  `author_emp_id` varchar(10) DEFAULT NULL,
  `comment_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_escalations`
--

CREATE TABLE `ticket_escalations` (
  `escalation_id` int(11) NOT NULL,
  `tkt_id` varchar(15) NOT NULL,
  `escalated_to_emp_id` varchar(10) DEFAULT NULL,
  `escalated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_customer_accounts_cus_id` (`cus_id`);

--
-- Indexes for table `customer_pricing`
--
ALTER TABLE `customer_pricing`
  ADD PRIMARY KEY (`cus_id`,`prod_id`),
  ADD KEY `fk_customer_pricing_prod_id` (`prod_id`);

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
  ADD UNIQUE KEY `emp_id` (`emp_id`),
  ADD UNIQUE KEY `username` (`username`);

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
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `api_access_logs`
--
ALTER TABLE `api_access_logs`
  MODIFY `api_log_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `api_credentials`
--
ALTER TABLE `api_credentials`
  MODIFY `credential_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `api_partners`
--
ALTER TABLE `api_partners`
  MODIFY `partner_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `audit_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `authentication_events`
--
ALTER TABLE `authentication_events`
  MODIFY `event_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `billing_cycles`
--
ALTER TABLE `billing_cycles`
  MODIFY `cycle_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `budget_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `contract_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_accounts`
--
ALTER TABLE `customer_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department_boards`
--
ALTER TABLE `department_boards`
  MODIFY `board_post_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `device_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_access_log`
--
ALTER TABLE `document_access_log`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_approvals`
--
ALTER TABLE `document_approvals`
  MODIFY `approval_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_versions`
--
ALTER TABLE `document_versions`
  MODIFY `version_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_offboarding`
--
ALTER TABLE `employee_offboarding`
  MODIFY `offboarding_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_onboarding`
--
ALTER TABLE `employee_onboarding`
  MODIFY `onboarding_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `integration_logs`
--
ALTER TABLE `integration_logs`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `integration_requirements`
--
ALTER TABLE `integration_requirements`
  MODIFY `req_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `internal_policies`
--
ALTER TABLE `internal_policies`
  MODIFY `policy_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ip_addresses`
--
ALTER TABLE `ip_addresses`
  MODIFY `ip_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `it_assets`
--
ALTER TABLE `it_assets`
  MODIFY `asset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_postings`
--
ALTER TABLE `job_postings`
  MODIFY `posting_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_base_articles`
--
ALTER TABLE `knowledge_base_articles`
  MODIFY `kb_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `lead_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `leave_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opportunities`
--
ALTER TABLE `opportunities`
  MODIFY `opp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `portal_accounts`
--
ALTER TABLE `portal_accounts`
  MODIFY `portal_user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `portal_notifications`
--
ALTER TABLE `portal_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `portal_sessions`
--
ALTER TABLE `portal_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotes`
--
ALTER TABLE `quotes`
  MODIFY `quote_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recruitment_candidates`
--
ALTER TABLE `recruitment_candidates`
  MODIFY `candidate_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `risk_register`
--
ALTER TABLE `risk_register`
  MODIFY `risk_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_forecasts`
--
ALTER TABLE `sales_forecasts`
  MODIFY `forecast_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `security_events`
--
ALTER TABLE `security_events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `security_incidents`
--
ALTER TABLE `security_incidents`
  MODIFY `incident_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `security_policies`
--
ALTER TABLE `security_policies`
  MODIFY `policy_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sla_policies`
--
ALTER TABLE `sla_policies`
  MODIFY `sla_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_escalations`
--
ALTER TABLE `ticket_escalations`
  MODIFY `escalation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `training_records`
--
ALTER TABLE `training_records`
  MODIFY `training_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `session_id` bigint(20) NOT NULL AUTO_INCREMENT;

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
