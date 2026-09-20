-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 19, 2026 at 12:38 PM
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
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `cus_id` varchar(10) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `primary_contact_name` varchar(150) DEFAULT NULL,
  `account_manager_emp_id` varchar(10) DEFAULT NULL,
  `onboarded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`cus_id`, `company_name`, `sector`, `primary_contact_name`, `account_manager_emp_id`, `onboarded_at`) VALUES
('CUS-1001', 'Aral Geomatics Group', 'Surveying & GIS', 'Sergei Makarov', 'EMP-1007', '2026-09-19 10:35:40'),
('CUS-1002', 'BaltNord Process Systems', 'Industrial Automation', 'Kristaps Ozols', 'EMP-1010', '2026-09-19 10:35:40'),
('CUS-1003', 'Steppe Mining Technologies', 'Mining', 'Yerlan Bektemis', 'EMP-1008', '2026-09-19 10:35:40'),
('CUS-1004', 'Rhein Werk Instrumentation', 'Industrial Measurement', 'Lukas Brandt', 'EMP-1010', '2026-09-19 10:35:40'),
('CUS-1005', 'Tashkent Precision Controls', 'Manufacturing', 'Dilshod Karim', 'EMP-1008', '2026-09-19 10:35:40'),
('CUS-1006', 'Daugava Optical Research', 'Optical Engineering', 'Mara Kalnina', 'EMP-1007', '2026-09-19 10:35:40'),
('CUS-1007', 'Caspian Industrial Robotics', 'Robotics', 'Murad Safarov', 'EMP-1009', '2026-09-19 10:35:40'),
('CUS-1008', 'Eurasia Water Automation', 'Water Infrastructure', 'Oleg Petrenko', 'EMP-1009', '2026-09-19 10:35:40'),
('CUS-1009', 'Altai Environmental Systems', 'Environmental Monitoring', 'Ainur Sadyk', 'EMP-1008', '2026-09-19 10:35:40'),
('CUS-1010', 'Central Rail Diagnostics', 'Railway Infrastructure', 'Tomas Varga', 'EMP-1006', '2026-09-19 10:35:40');

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
('ENG', 'Engineering & Automation', 'Industrial Integration, Software, and Hardware Engineering', 16),
('EXE', 'Executive Management', 'Strategy, Governance, and Executive Approvals', 5),
('FIN', 'Finance & Billing', 'Billing, Payments, Accounting, and Reconciliation', 10),
('GOV', 'Governance, Risk & Compliance', 'Compliance, Audit, Risk Management, and Information Security', 6),
('HRA', 'Human Resources & Admin', 'Recruitment, Personnel Affairs, and Facilities', 8),
('ITD', 'Information Technology', 'Infrastructure, Support, Security, and System Management', 14),
('OPS', 'Operations & Logistics', 'Procurement, Inventory, Shipping, and Execution', 20),
('SAL', 'Sales & Commercial Affairs', 'Sales, Customer Management, Quotes, and Contracts', 16);

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
  `related_cus_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`doc_id`, `file_name`, `classification`, `owning_system`, `owner_emp_id`, `related_prj_id`, `related_cus_id`) VALUES
('DOC-2026-001', 'Corporate_Information_Security_Policy.pdf', 'TopSecret', 'Admin & Governance', 'EMP-1005', NULL, NULL),
('DOC-2026-002', 'Customer_Onboarding_Standard.pdf', 'Confidential', 'CRM', 'EMP-1006', NULL, NULL),
('DOC-2026-003', 'PRJ-2026-001_Statement_of_Work.pdf', 'Confidential', 'File Center', 'EMP-1019', 'PRJ-2026-001', 'CUS-1001'),
('DOC-2026-004', 'PRJ-2026-002_Integration_Specification.pdf', 'TopSecret', 'File Center', 'EMP-1019', 'PRJ-2026-002', 'CUS-1002'),
('DOC-2026-005', 'INV-2026-002_Billing_Record.pdf', 'Confidential', 'Finance', 'EMP-1003', 'PRJ-2026-002', 'CUS-1002'),
('DOC-2026-006', 'Employee_Onboarding_Procedure.pdf', 'Confidential', 'HR', 'EMP-1005', NULL, NULL),
('DOC-2026-007', 'Employee_Access_Matrix.xlsx', 'TopSecret', 'Admin & Governance', 'EMP-1005', NULL, NULL),
('DOC-2026-008', 'Supplier_Evaluation_2026.pdf', 'Confidential', 'Operations', 'EMP-1013', NULL, NULL),
('DOC-2026-009', 'Optical_Sensor_Product_Catalog.pdf', 'Public', 'E-Commerce', 'EMP-1006', NULL, NULL),
('DOC-2026-010', 'API_Integration_Guide.pdf', 'Internal', 'Developer Portal', 'EMP-1020', NULL, NULL),
('DOC-2026-011', 'Disaster_Recovery_Plan.pdf', 'TopSecret', 'IT Helpdesk', 'EMP-1018', NULL, NULL),
('DOC-2026-012', 'Annual_Corporate_Budget_2026.xlsx', 'TopSecret', 'Finance', 'EMP-1003', NULL, NULL),
('DOC-2026-013', 'Customer_Service_Handbook.pdf', 'Internal', 'Intranet', 'EMP-1004', NULL, NULL),
('DOC-2026-014', 'PRJ-2026-007_Test_Report.pdf', 'Confidential', 'File Center', 'EMP-1019', 'PRJ-2026-007', 'CUS-1007'),
('DOC-2026-015', 'Board_Risk_Register_2026.xlsx', 'TopSecret', 'Admin & Governance', 'EMP-1005', NULL, NULL);

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
  `employment_status` enum('Active','OnLeave','Suspended','Terminated') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`emp_id`, `full_name`, `job_title`, `department_code`, `clearance_level`, `email`, `manager_emp_id`, `employment_status`) VALUES
('EMP-1001', 'Viktor Sokolov', 'Chief Executive Officer (CEO)', 'EXE', 'L4', 'viktor.sokolov@vostokpribor.local', NULL, 'Active'),
('EMP-1002', 'Amina Karimova', 'Chief Operating Officer (COO)', 'EXE', 'L4', 'amina.karimova@vostokpribor.local', 'EMP-1001', 'Active'),
('EMP-1003', 'Daniel Weber', 'Chief Financial Officer (CFO)', 'EXE', 'L4', 'daniel.weber@vostokpribor.local', 'EMP-1001', 'Active'),
('EMP-1004', 'Elena Morozova', 'Chief Technology Officer (CTO)', 'EXE', 'L4', 'elena.morozova@vostokpribor.local', 'EMP-1001', 'Active'),
('EMP-1005', 'Timur Akhmetov', 'Chief Governance Officer', 'EXE', 'L4', 'timur.akhmetov@vostokpribor.local', 'EMP-1001', 'Active'),
('EMP-1006', 'Pavel Orlov', 'Sales Director', 'SAL', 'L3', 'pavel.orlov@vostokpribor.local', 'EMP-1001', 'Active'),
('EMP-1007', 'Sara Lindholm', 'Senior Account Manager', 'SAL', 'L3', 'sara.lindholm@vostokpribor.local', 'EMP-1006', 'Active'),
('EMP-1008', 'Bekzod Rakhimov', 'Account Manager', 'SAL', 'L3', 'bekzod.rakhimov@vostokpribor.local', 'EMP-1006', 'Active'),
('EMP-1009', 'Nadia Petrova', 'Strategic Sales Manager', 'SAL', 'L3', 'nadia.petrova@vostokpribor.local', 'EMP-1006', 'Active'),
('EMP-1010', 'Markus Klein', 'Regional Sales Manager', 'SAL', 'L3', 'markus.klein@vostokpribor.local', 'EMP-1006', 'Active'),
('EMP-1011', 'Arman Tulegenov', 'Operations Manager', 'OPS', 'L3', 'arman.tulegenov@vostokpribor.local', 'EMP-1002', 'Active'),
('EMP-1012', 'Rustam Bekov', 'Logistics Manager', 'OPS', 'L3', 'rustam.bekov@vostokpribor.local', 'EMP-1011', 'Active'),
('EMP-1013', 'Ilona Vetra', 'Procurement Manager', 'OPS', 'L3', 'ilona.vetra@vostokpribor.local', 'EMP-1011', 'Active'),
('EMP-1014', 'Mikhail Antonov', 'Warehouse Supervisor', 'OPS', 'L2', 'mikhail.antonov@vostokpribor.local', 'EMP-1011', 'Active'),
('EMP-1015', 'Kamila Nurzhan', 'Supply Chain Analyst', 'OPS', 'L2', 'kamila.nurzhan@vostokpribor.local', 'EMP-1011', 'Active'),
('EMP-1016', 'Erik Hansen', 'Senior Automation Engineer', 'ENG', 'L3', 'erik.hansen@vostokpribor.local', 'EMP-1004', 'Active'),
('EMP-1017', 'Dana Yermak', 'Software Integration Engineer', 'ENG', 'L3', 'dana.yermak@vostokpribor.local', 'EMP-1004', 'Active'),
('EMP-1018', 'Leonid Volkov', 'Systems Engineer', 'ENG', 'L3', 'leonid.volkov@vostokpribor.local', 'EMP-1004', 'Active'),
('EMP-1019', 'Farida Iskakova', 'Project Manager', 'ENG', 'L3', 'farida.iskakova@vostokpribor.local', 'EMP-1004', 'Active'),
('EMP-1020', 'Jonas Richter', 'Senior Developer', 'ENG', 'L3', 'jonas.richter@vostokpribor.local', 'EMP-1004', 'Active');

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
  `payment_status` enum('Paid','Pending','Overdue') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`inv_id`, `cus_id`, `prj_id`, `total_value`, `currency`, `payment_status`) VALUES
('INV-2026-001', 'CUS-1001', 'PRJ-2026-001', 46250.00, 'EUR', 'Paid'),
('INV-2026-002', 'CUS-1002', 'PRJ-2026-002', 80000.00, 'EUR', 'Pending'),
('INV-2026-003', 'CUS-1003', 'PRJ-2026-003', 137500.00, 'EUR', 'Paid'),
('INV-2026-004', 'CUS-1004', 'PRJ-2026-004', 55000.00, 'EUR', 'Pending'),
('INV-2026-005', 'CUS-1005', 'PRJ-2026-005', 42000.00, 'EUR', 'Paid'),
('INV-2026-006', 'CUS-1006', 'PRJ-2026-006', 32000.00, 'EUR', 'Pending'),
('INV-2026-007', 'CUS-1007', 'PRJ-2026-007', 105000.00, 'EUR', 'Paid'),
('INV-2026-008', 'CUS-1008', 'PRJ-2026-008', 68333.00, 'EUR', 'Pending'),
('INV-2026-009', 'CUS-1009', 'PRJ-2026-012', 47333.00, 'EUR', 'Paid'),
('INV-2026-010', 'CUS-1010', 'PRJ-2026-010', 91667.00, 'EUR', 'Pending');

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
('PROD-1001', 'Industrial Optical Sensor Package', 'PerUnit', NULL),
('PROD-1002', 'Precision Geodetic Measurement Kit', 'PerUnit', NULL),
('PROD-1003', 'Automated Calibration Station', 'PerProject', NULL),
('PROD-1004', 'Industrial PLC Integration', 'PerProject', NULL),
('PROD-1005', 'Remote Monitoring Gateway', 'PerUnit', NULL),
('PROD-1006', 'Optical Inspection System', 'PerProject', NULL),
('PROD-1007', 'Industrial Lifecycle Support', 'SubscriptionAnnual', NULL),
('PROD-1008', 'Automation Software Integration', 'PerProject', NULL),
('PROD-1009', 'Enterprise Logistics Management', 'SubscriptionMonthly', NULL),
('PROD-1010', 'Preventive Instrument Maintenance', 'AnnualContract', NULL);

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
  `status` enum('Planning','Procurement','Design','Integration','Testing','Execution','ContractReview','Maintenance','Closed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`prj_id`, `cus_id`, `project_manager_emp_id`, `budget`, `currency`, `status`) VALUES
('PRJ-2026-001', 'CUS-1001', 'EMP-1019', 185000.00, 'EUR', 'Execution'),
('PRJ-2026-002', 'CUS-1002', 'EMP-1019', 240000.00, 'EUR', 'Integration'),
('PRJ-2026-003', 'CUS-1003', 'EMP-1016', 410000.00, 'EUR', 'Procurement'),
('PRJ-2026-004', 'CUS-1004', 'EMP-1019', 165000.00, 'EUR', 'Execution'),
('PRJ-2026-005', 'CUS-1005', 'EMP-1017', 128000.00, 'EUR', 'Integration'),
('PRJ-2026-006', 'CUS-1006', 'EMP-1016', 96000.00, 'EUR', 'Design'),
('PRJ-2026-007', 'CUS-1007', 'EMP-1019', 315000.00, 'EUR', 'Integration'),
('PRJ-2026-008', 'CUS-1008', 'EMP-1017', 205000.00, 'EUR', 'Execution'),
('PRJ-2026-009', 'CUS-1001', 'EMP-1019', 275000.00, 'EUR', 'Design'),
('PRJ-2026-010', 'CUS-1002', 'EMP-1017', 74000.00, 'EUR', 'ContractReview'),
('PRJ-2026-011', 'CUS-1005', 'EMP-1016', 188000.00, 'EUR', 'Testing'),
('PRJ-2026-012', 'CUS-1009', 'EMP-1016', 142000.00, 'EUR', 'Maintenance'),
('PRJ-2026-013', 'CUS-1010', 'EMP-1019', 112000.00, 'EUR', 'Procurement'),
('PRJ-2026-014', 'CUS-1007', 'EMP-1017', 260000.00, 'EUR', 'Procurement'),
('PRJ-2026-015', 'CUS-1010', 'EMP-1016', 151000.00, 'EUR', 'Planning');

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
  `status` enum('Open','InProgress','Investigating','Escalated','Resolved') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`tkt_id`, `requester_type`, `requester_cus_id`, `requester_emp_id`, `source_system`, `priority`, `assigned_emp_id`, `status`) VALUES
('TKT-2026-001', 'Customer', 'CUS-1002', NULL, 'Customer Portal', 'High', 'EMP-1018', 'InProgress'),
('TKT-2026-002', 'Employee', NULL, 'EMP-1007', 'CRM', 'Medium', 'EMP-1018', 'Resolved'),
('TKT-2026-003', 'Customer', 'CUS-1004', NULL, 'E-Commerce', 'High', 'EMP-1018', 'Investigating'),
('TKT-2026-004', 'Employee', NULL, 'EMP-1015', 'Intranet', 'Low', 'EMP-1018', 'Resolved'),
('TKT-2026-005', 'Customer', 'CUS-1007', NULL, 'Customer Portal', 'Critical', 'EMP-1018', 'Escalated'),
('TKT-2026-006', 'Employee', NULL, 'EMP-1020', 'Developer Portal', 'Medium', 'EMP-1018', 'Resolved'),
('TKT-2026-007', 'Customer', 'CUS-1005', NULL, 'E-Commerce', 'Medium', 'EMP-1018', 'Resolved'),
('TKT-2026-008', 'Employee', NULL, 'EMP-1016', 'File Center', 'Medium', 'EMP-1018', 'InProgress'),
('TKT-2026-009', 'Customer', 'CUS-1001', NULL, 'Customer Portal', 'Low', 'EMP-1018', 'Resolved'),
('TKT-2026-010', 'Employee', NULL, 'EMP-1017', 'Developer Portal', 'High', 'EMP-1018', 'Investigating'),
('TKT-2026-011', 'Customer', 'CUS-1008', NULL, 'Customer Portal', 'High', 'EMP-1018', 'Escalated'),
('TKT-2026-012', 'Employee', NULL, 'EMP-1013', 'Intranet', 'Medium', 'EMP-1018', 'Resolved'),
('TKT-2026-013', 'Customer', 'CUS-1009', NULL, 'E-Commerce', 'Low', 'EMP-1018', 'Resolved'),
('TKT-2026-014', 'Employee', NULL, 'EMP-1019', 'File Center', 'Medium', 'EMP-1018', 'InProgress'),
('TKT-2026-015', 'Customer', 'CUS-1010', NULL, 'Customer Portal', 'High', 'EMP-1018', 'Investigating');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`cus_id`),
  ADD KEY `fk_customers_account_manager_emp_id` (`account_manager_emp_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`dept_code`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `fk_documents_owner_emp_id` (`owner_emp_id`),
  ADD KEY `fk_documents_related_cus_id` (`related_cus_id`),
  ADD KEY `fk_documents_related_prj_id` (`related_prj_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`emp_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_employees_department_code` (`department_code`),
  ADD KEY `fk_employees_manager_emp_id` (`manager_emp_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`inv_id`),
  ADD KEY `fk_invoices_cus_id` (`cus_id`),
  ADD KEY `fk_invoices_prj_id` (`prj_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`prod_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`prj_id`),
  ADD KEY `fk_projects_cus_id` (`cus_id`),
  ADD KEY `fk_projects_project_manager_emp_id` (`project_manager_emp_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`tkt_id`),
  ADD KEY `fk_tickets_assigned_emp_id` (`assigned_emp_id`),
  ADD KEY `fk_tickets_requester_cus_id` (`requester_cus_id`),
  ADD KEY `fk_tickets_requester_emp_id` (`requester_emp_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `fk_customers_account_manager_emp_id` FOREIGN KEY (`account_manager_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `fk_documents_owner_emp_id` FOREIGN KEY (`owner_emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_documents_related_cus_id` FOREIGN KEY (`related_cus_id`) REFERENCES `customers` (`cus_id`),
  ADD CONSTRAINT `fk_documents_related_prj_id` FOREIGN KEY (`related_prj_id`) REFERENCES `projects` (`prj_id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_employees_department_code` FOREIGN KEY (`department_code`) REFERENCES `departments` (`dept_code`),
  ADD CONSTRAINT `fk_employees_manager_emp_id` FOREIGN KEY (`manager_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_invoices_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`),
  ADD CONSTRAINT `fk_invoices_prj_id` FOREIGN KEY (`prj_id`) REFERENCES `projects` (`prj_id`);

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_projects_cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customers` (`cus_id`),
  ADD CONSTRAINT `fk_projects_project_manager_emp_id` FOREIGN KEY (`project_manager_emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `fk_tickets_assigned_emp_id` FOREIGN KEY (`assigned_emp_id`) REFERENCES `employees` (`emp_id`),
  ADD CONSTRAINT `fk_tickets_requester_cus_id` FOREIGN KEY (`requester_cus_id`) REFERENCES `customers` (`cus_id`),
  ADD CONSTRAINT `fk_tickets_requester_emp_id` FOREIGN KEY (`requester_emp_id`) REFERENCES `employees` (`emp_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
