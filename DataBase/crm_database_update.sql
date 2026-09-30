-- =============================================================================
-- VOSTOKPRIBOR ENTERPRISE CRM · COMPLETE DATABASE MIGRATION & SEED SCRIPT
-- Target Database: vostokpribor
-- Safe for deployment on other devices/servers with the same base schema
-- Generated: 2026-09-29 16:45:22
-- =============================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

-- -----------------------------------------------------------------------------
-- 1. DDL: Create crm_activities table if it does not exist
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `crm_activities` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `cus_id` varchar(10) DEFAULT NULL,
  `opp_id` int(11) DEFAULT NULL,
  `emp_id` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`activity_id`),
  KEY `idx_crm_act_cus` (`cus_id`),
  KEY `idx_crm_act_opp` (`opp_id`),
  KEY `idx_crm_act_emp` (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. DDL: Ensure all required CRM columns and stage types exist
-- -----------------------------------------------------------------------------
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

ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `progress_percent` int(11) DEFAULT 0;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `facility_location` varchar(255) DEFAULT NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `scope_summary` text DEFAULT NULL;

-- -----------------------------------------------------------------------------
-- Data for table `customers` (10 records)
-- -----------------------------------------------------------------------------
INSERT INTO `customers` (`cus_id`, `company_name`, `sector`, `primary_contact_name`, `primary_contact_email`, `account_manager_emp_id`, `onboarded_at`, `phone`, `headquarters`, `tax_id`, `health_score`, `account_tier`, `status`) VALUES
  ('CUS-1001', 'Severstal Metallurgy PJSC', 'Ferrous Metallurgy', 'P. V. Cherepanov', 'procurement@severstal.com', 'EMP-1006', '2026-09-19 10:35:40', '+7 (8202) 53-0900', 'Cherepovets, Vologda Oblast', 'INN 3528000597', 98, 'Strategic Tier-1', 'Active'),
  ('CUS-1002', 'NLMK Group Lipetsk', 'Ferrous Metallurgy', 'Alexei Voronov', 'automation@nlmk.com', 'EMP-1010', '2026-09-19 10:35:40', '+7 (4742) 44-4009', 'Lipetsk, Lipetsk Oblast', 'INN 4823006703', 92, 'Tier-1 Enterprise', 'Active'),
  ('CUS-1003', 'Norilsk Nickel Mining', 'Non-Ferrous Mining', 'Dmitry Kiselev', 'telemetry@nornickel.ru', 'EMP-1007', '2026-09-19 10:35:40', '+7 (3919) 25-1100', 'Talnakh / Norilsk, Krasnoyarsk Krai', 'INN 8401005730', 95, 'Strategic Tier-1', 'Active'),
  ('CUS-1004', 'EVRAZ Consolidated', 'Ferrous Metallurgy', 'Maxim Gusev', 'm.gusev@evraz.com', 'EMP-1008', '2026-09-19 10:35:40', '+7 (3435) 49-7000', 'Nizhny Tagil, Sverdlovsk Oblast', 'INN 7715047418', 88, 'Tier-2 Enterprise', 'Active'),
  ('CUS-1005', 'PhosAgro Chemical', 'Chemical & Agro', 'Sergey Belyakov', 's.belyakov@phosagro.ru', 'EMP-1006', '2026-09-19 10:35:40', '+7 (8153) 15-5000', 'Apatity, Murmansk Oblast', 'INN 7725542282', 96, 'Tier-2 Enterprise', 'Active'),
  ('CUS-1006', 'Gazprom Neft Omsk', 'Oil & Gas', 'Roman Danilov', 'danilov.ra@omsk.gazprom-neft.ru', 'EMP-1007', '2026-09-19 10:35:40', '+7 (3812) 69-0000', 'Omsk, Omsk Oblast', 'INN 5504036333', 90, 'Strategic Tier-1', 'Active'),
  ('CUS-1007', 'Chelyabinsk Pipe Plant', 'Ferrous Metallurgy', 'Dr. Andrei Vasiliev', 'a.vasiliev@chelpipe.ru', 'EMP-1010', '2026-09-19 10:35:40', '+7 (351) 259-8801', 'Chelyabinsk, Chelyabinsk Oblast', 'INN 7451010323', 94, 'Tier-1 Enterprise', 'Active'),
  ('CUS-1008', 'Magnitogorsk Iron & Steel Works', 'Ferrous Metallurgy', 'Valery Semenov', 'v.semenov@mmk.ru', 'EMP-1008', '2026-09-19 10:35:40', '+7 (3519) 24-0012', 'Magnitogorsk, Chelyabinsk Oblast', 'INN 7414003638', 91, 'Strategic Tier-1', 'Active'),
  ('CUS-1009', 'Uralchem Mineral Fertilizers', 'Chemical & Agro', 'Irina Kondratieva', 'i.kondratieva@uralchem.com', 'EMP-1006', '2026-09-19 10:35:40', '+7 (342) 219-4400', 'Perm, Perm Krai', 'INN 7703647595', 89, 'Tier-2 Enterprise', 'Active'),
  ('CUS-1010', 'Central Rail Diagnostics', 'Railway Infrastructure', 'Konstantin Titov', 'titov@rail-diagnostics.ru', 'EMP-1007', '2026-09-19 10:35:40', '+7 (499) 262-9901', 'Moscow, Central District', 'INN 7708503727', 85, 'Tier-2 Enterprise', 'Active')
ON DUPLICATE KEY UPDATE
  `cus_id` = VALUES(`cus_id`),
  `company_name` = VALUES(`company_name`),
  `sector` = VALUES(`sector`),
  `primary_contact_name` = VALUES(`primary_contact_name`),
  `primary_contact_email` = VALUES(`primary_contact_email`),
  `account_manager_emp_id` = VALUES(`account_manager_emp_id`),
  `onboarded_at` = VALUES(`onboarded_at`),
  `phone` = VALUES(`phone`),
  `headquarters` = VALUES(`headquarters`),
  `tax_id` = VALUES(`tax_id`),
  `health_score` = VALUES(`health_score`),
  `account_tier` = VALUES(`account_tier`),
  `status` = VALUES(`status`);

-- -----------------------------------------------------------------------------
-- Data for table `opportunities` (10 records)
-- -----------------------------------------------------------------------------
INSERT INTO `opportunities` (`opp_id`, `lead_id`, `cus_id`, `sales_emp_id`, `stage`, `estimated_value`, `expected_close_date`, `opp_title`, `probability_percent`, `is_confidential`, `priority`) VALUES
  (1, NULL, 'CUS-1004', 'EMP-1008', 'Qualification', 1650000.00, '2026-11-15', 'Rail Mill Laser Profiler & Flaw Detection Array', 40, 1, 'high'),
  (2, NULL, 'CUS-1006', 'EMP-1007', 'Qualification', 3100000.00, '2026-12-10', 'Refinery Catalytic Cracking Gas Analysis Skid', 35, 1, 'high'),
  (3, NULL, 'CUS-1002', 'EMP-1010', 'Proposal', 920000.00, '2026-11-20', 'Coke Oven Battery Temperature Profiling & IR Cameras', 60, '0', 'medium'),
  (4, NULL, 'CUS-1001', 'EMP-1006', 'Proposal', 410000.00, '2026-12-05', 'Continuous Casting Machine #3 Optical Thickness Gauges', 65, '0', 'medium'),
  (5, NULL, 'CUS-1001', 'EMP-1006', 'Negotiation', 1850000.00, '2026-11-28', 'Blast Furnace #5 Automation & Gas Analysis', 85, 1, 'high'),
  (6, NULL, 'CUS-1003', 'EMP-1007', 'Negotiation', 2400000.00, '2026-12-18', 'Talnakh Concentrator Flotation Telemetry Grid', 80, 1, 'high'),
  (7, NULL, 'CUS-1001', 'EMP-1006', 'Contract', 640000.00, '2026-11-25', 'Hydraulic Pressure Sensor Telemetry Retrofit', 95, '0', 'high'),
  (8, NULL, 'CUS-1007', 'EMP-1010', 'Contract', 1450000.00, '2026-12-15', 'Seamless Casing Ultrasonic Flaw Detection Skid', 90, 1, 'high'),
  (9, NULL, 'CUS-1005', 'EMP-1006', 'Won', 418200.00, '2026-10-12', 'High-Pressure Flowmeter HPF-900X Replacement Batch', 100, '0', 'low'),
  (10, NULL, 'CUS-1002', 'EMP-1010', 'Won', 396400.00, '2026-10-08', 'Turbine Bearing Vibration Transducers (x16)', 100, '0', 'medium')
ON DUPLICATE KEY UPDATE
  `opp_id` = VALUES(`opp_id`),
  `lead_id` = VALUES(`lead_id`),
  `cus_id` = VALUES(`cus_id`),
  `sales_emp_id` = VALUES(`sales_emp_id`),
  `stage` = VALUES(`stage`),
  `estimated_value` = VALUES(`estimated_value`),
  `expected_close_date` = VALUES(`expected_close_date`),
  `opp_title` = VALUES(`opp_title`),
  `probability_percent` = VALUES(`probability_percent`),
  `is_confidential` = VALUES(`is_confidential`),
  `priority` = VALUES(`priority`);

-- -----------------------------------------------------------------------------
-- Data for table `leads` (6 records)
-- -----------------------------------------------------------------------------
INSERT INTO `leads` (`lead_id`, `full_name`, `email`, `phone`, `company_name`, `message`, `source_page`, `status`, `assigned_sales_emp_id`, `converted_cus_id`, `created_at`, `estimated_value`, `lead_score`, `equipment_scope`, `priority`) VALUES
  (1, 'Dr. Andrei Vasiliev', 'a.vasiliev@chelpipe.ru', '+7 (351) 259-8801', 'Chelyabinsk Pipe Plant', 'Seeking seamless casing ultrasonic flaw detection skid for pipe rolling mill #8.', 'Web RFQ', 'Qualified', 'EMP-1010', NULL, '2026-09-29 19:25:06', 1450000.00, 94, 'Seamless Casing Ultrasonic Flaw Detection Skid', 'High'),
  (2, 'Valery Semenov', 'v.semenov@mmk.ru', '+7 (3519) 24-0012', 'Magnitogorsk Iron & Steel Works', 'Multi-channel gas optical spectrometry matrix required for converter shop #2.', 'Direct Inbound', 'Qualified', 'EMP-1008', NULL, '2026-09-29 19:25:06', 2280000.00, 91, 'Multi-Channel Gas Optical Spectrometry Matrix', 'High'),
  (3, 'Irina Kondratieva', 'i.kondratieva@uralchem.com', '+7 (342) 219-4400', 'Uralchem Mineral Fertilizers', 'Engineering inquiry for ammonia synthesis high-temp pressure sensor telemetry.', 'Direct Referral', 'New', 'EMP-1006', NULL, '2026-09-29 19:25:06', 890000.00, 86, 'Ammonia Synthesis High-Temp Pressure Sensors', 'Normal'),
  (4, 'Nursultan Kadyrov', 'n.kadyrov@kaztransgas.kz', '+7 (717) 255-8801', 'KazTransGas Distribution', 'Requesting commercial quote for 40 units of Metrotec-500 Flow Analyzers.', 'Corporate Web', 'Qualified', 'EMP-1007', NULL, '2026-09-29 19:25:06', 450000.00, 80, '40x Metrotec-500 Flow Analyzers', 'Normal'),
  (5, 'Olga Demidova', 'o.demidova@severstal.ru', '+7 (820) 256-4422', 'Severstal Metallurgy PJSC', 'Follow-up regarding proposal for furnace pressure differential gauges.', 'Direct Referral', 'Converted', 'EMP-1006', NULL, '2026-09-29 19:25:06', 640000.00, 95, 'Hydraulic Pressure Differential Gauges', 'High'),
  (6, 'Yerbol Sadykov', 'yerbol@bogatyr.kz', '+7 (718) 722-1144', 'Bogatyr Coal Mining', 'Seeking vibration analysis telemetry sensors for open-pit excavators.', 'B2B Shop', 'New', 'EMP-1008', NULL, '2026-09-29 19:25:06', 320000.00, 74, 'Open-Pit Excavator Vibration Sensors', 'Normal')
ON DUPLICATE KEY UPDATE
  `lead_id` = VALUES(`lead_id`),
  `full_name` = VALUES(`full_name`),
  `email` = VALUES(`email`),
  `phone` = VALUES(`phone`),
  `company_name` = VALUES(`company_name`),
  `message` = VALUES(`message`),
  `source_page` = VALUES(`source_page`),
  `status` = VALUES(`status`),
  `assigned_sales_emp_id` = VALUES(`assigned_sales_emp_id`),
  `converted_cus_id` = VALUES(`converted_cus_id`),
  `created_at` = VALUES(`created_at`),
  `estimated_value` = VALUES(`estimated_value`),
  `lead_score` = VALUES(`lead_score`),
  `equipment_scope` = VALUES(`equipment_scope`),
  `priority` = VALUES(`priority`);

-- -----------------------------------------------------------------------------
-- Data for table `contracts` (7 records)
-- -----------------------------------------------------------------------------
INSERT INTO `contracts` (`contract_id`, `cus_id`, `prj_id`, `opp_id`, `contract_value`, `start_date`, `end_date`, `status`, `doc_id`, `contract_ref`, `title`, `contract_type`, `eds_status`, `confidentiality_level`) VALUES
  (1, 'CUS-1001', 'PRJ-VP-7721', NULL, 14250000.00, '2024-01-01', '2026-12-31', 'Active', NULL, 'MSA-2024-SVST-088', 'Master Automation Equipment & SCADA Services Agreement', 'MSA', 'Counter-Signed', 'Restricted'),
  (2, 'CUS-1003', 'PRJ-VP-6610', NULL, 8400000.00, '2023-11-15', '2025-11-14', 'Active', NULL, 'MSA-2023-NN-014', 'Talnakh Concentrator Multi-Year Telemetry & Field Sensor MSA', 'MSA', 'Counter-Signed', 'Restricted'),
  (3, 'CUS-1002', 'PRJ-VP-5520', NULL, 4200000.00, '2024-02-01', '2026-10-31', 'Active', NULL, 'MSA-2024-NLMK-090', 'Blast Furnace & Strip Mill Telemetry Integration Agreement', 'MSA', 'Counter-Signed', 'Restricted'),
  (4, 'CUS-1001', 'PRJ-VP-7721', NULL, 850000.00, '2024-01-01', '2025-12-31', 'Active', NULL, 'SLA-2024-TIER1', '24/7 Field Engineering Specialist & Incident SLA Support Coverage', 'SLA', 'Active SLA', 'Standard'),
  (5, 'CUS-1001', NULL, NULL, '0.00', '2023-12-01', '2028-12-01', 'Active', NULL, 'NDA-VP-SVR-90214', 'Bilateral Non-Disclosure Agreement for Industrial Proprietary Telemetry', 'NDA', 'Electronic Seal', 'Strict'),
  (6, 'CUS-1004', 'PRJ-VP-4411', NULL, 2100000.00, '2024-03-01', '2026-03-01', 'Active', NULL, 'MSA-2024-EVRAZ-033', 'Rail Mill Flaw Detection & Laser Profiler Deployment MSA', 'MSA', 'Counter-Signed', 'Restricted'),
  (7, 'CUS-1005', NULL, NULL, 1950000.00, '2024-04-15', '2026-04-15', 'Active', NULL, 'MSA-2024-PHOS-019', 'Fertilizer Complex Flowmeter & Telemetry Master Contract', 'MSA', 'Counter-Signed', 'Standard')
ON DUPLICATE KEY UPDATE
  `contract_id` = VALUES(`contract_id`),
  `cus_id` = VALUES(`cus_id`),
  `prj_id` = VALUES(`prj_id`),
  `opp_id` = VALUES(`opp_id`),
  `contract_value` = VALUES(`contract_value`),
  `start_date` = VALUES(`start_date`),
  `end_date` = VALUES(`end_date`),
  `status` = VALUES(`status`),
  `doc_id` = VALUES(`doc_id`),
  `contract_ref` = VALUES(`contract_ref`),
  `title` = VALUES(`title`),
  `contract_type` = VALUES(`contract_type`),
  `eds_status` = VALUES(`eds_status`),
  `confidentiality_level` = VALUES(`confidentiality_level`);

-- -----------------------------------------------------------------------------
-- Data for table `quotes` (4 records)
-- -----------------------------------------------------------------------------
INSERT INTO `quotes` (`quote_id`, `cus_id`, `prod_id`, `quantity`, `unit_price`, `created_by_emp_id`, `created_at`, `quote_ref`, `equipment_scope`, `valid_until`, `total_amount`, `status`) VALUES
  (1, 'CUS-1002', 'PROD-1001', 16, 26137.50, 'EMP-1010', '2026-09-29 19:25:06', 'QUO-9912', '16x High-Pressure Flowmeters HPF-900X with Industrial Hart Protocol', '2026-11-30', 418200.00, 'Delivered'),
  (2, 'CUS-1001', 'PROD-1002', 2, 340000.00, 'EMP-1006', '2026-09-29 19:25:06', 'QUO-9918', 'Blast Furnace #5 Optical Spectrometry Skid & Calibration Module', '2026-12-15', 680000.00, 'Approved'),
  (3, 'CUS-1003', 'PROD-1003', 40, 31000.00, 'EMP-1007', '2026-09-29 19:25:06', 'QUO-9924', 'Talnakh Flotation Telemetry Grid Transducers & Sub-Zero Junction Skids', '2026-12-31', 1240000.00, 'Under Review'),
  (4, 'CUS-1007', 'PROD-1001', 1, 1450000.00, 'EMP-1010', '2026-09-29 19:25:06', 'QUO-9931', 'Seamless Casing Ultrasonic Flaw Detection Station with Automated Feeder', '2026-11-25', 1450000.00, 'Pending Review')
ON DUPLICATE KEY UPDATE
  `quote_id` = VALUES(`quote_id`),
  `cus_id` = VALUES(`cus_id`),
  `prod_id` = VALUES(`prod_id`),
  `quantity` = VALUES(`quantity`),
  `unit_price` = VALUES(`unit_price`),
  `created_by_emp_id` = VALUES(`created_by_emp_id`),
  `created_at` = VALUES(`created_at`),
  `quote_ref` = VALUES(`quote_ref`),
  `equipment_scope` = VALUES(`equipment_scope`),
  `valid_until` = VALUES(`valid_until`),
  `total_amount` = VALUES(`total_amount`),
  `status` = VALUES(`status`);

-- -----------------------------------------------------------------------------
-- Data for table `projects` (6 records)
-- -----------------------------------------------------------------------------
INSERT INTO `projects` (`prj_id`, `project_name`, `cus_id`, `project_manager_emp_id`, `budget`, `currency`, `status`, `start_date`, `end_date`, `progress_percent`, `facility_location`, `scope_summary`) VALUES
  ('PRJ-VP-4411', 'Rail Mill Laser Profiler & Flaw Detection Array', 'CUS-1004', 'EMP-1008', 1650000.00, 'USD', 'Procurement', '2024-04-01', '2027-01-15', 35, 'Rail & Structural Mill · Nizhny Tagil', 'High-speed eddy-current flaw detector array for continuous railway rail profiles.'),
  ('PRJ-VP-5520', 'Coke Oven Battery Temperature Profiling & IR Cameras', 'CUS-1002', 'EMP-1010', 920000.00, 'USD', 'Integration', '2024-02-15', '2026-11-20', 60, 'Coke Plant Shop #3 · Lipetsk', 'Continuous thermal imaging array with automatic hot-spot flame detection alerting.'),
  ('PRJ-VP-6610', 'Talnakh Concentrator Flotation Telemetry Grid', 'CUS-1003', 'EMP-1007', 2400000.00, 'USD', 'Execution', '2024-02-10', '2026-12-18', 80, 'Talnakh Concentrator Division · Norilsk', 'Sub-zero froth height acoustic telemetry and slurry density radiometric densitometer network.'),
  ('PRJ-VP-7721', 'Blast Furnace #5 Automation & Gas Analysis', 'CUS-1001', 'EMP-1006', 1850000.00, 'USD', 'Execution', '2024-01-15', '2026-11-28', 72, 'Cherepovets Ironmaking Plant #4', 'Blast furnace off-gas telemetry, CO/CO2 infrared optical analyzers, and high-temp probe arrays.'),
  ('PRJ-VP-7804', 'Hot Strip Mill #2 Hydraulic Pressure Sensor Telemetry Retrofit', 'CUS-1001', 'EMP-1010', 640000.00, 'USD', 'Integration', '2024-03-01', '2026-12-20', 45, 'Rolling Mill Bay 2 · Cherepovets', 'Retrofit 48 high-pressure hydraulic transducers with real-time SCADA Modbus TCP gateway.'),
  ('PRJ-VP-8902', 'Continuous Casting Machine #3 Optical Thickness Gauges', 'CUS-1001', 'EMP-1006', 410000.00, 'USD', 'Design', '2024-05-01', '2027-02-15', 18, 'Steelmaking Shop #1 · Cherepovets', 'Triangulation laser profilometer pair for slab hot-geometry dimensional verification.')
ON DUPLICATE KEY UPDATE
  `prj_id` = VALUES(`prj_id`),
  `project_name` = VALUES(`project_name`),
  `cus_id` = VALUES(`cus_id`),
  `project_manager_emp_id` = VALUES(`project_manager_emp_id`),
  `budget` = VALUES(`budget`),
  `currency` = VALUES(`currency`),
  `status` = VALUES(`status`),
  `start_date` = VALUES(`start_date`),
  `end_date` = VALUES(`end_date`),
  `progress_percent` = VALUES(`progress_percent`),
  `facility_location` = VALUES(`facility_location`),
  `scope_summary` = VALUES(`scope_summary`);

-- -----------------------------------------------------------------------------
-- Data for table `sales_forecasts` (5 records)
-- -----------------------------------------------------------------------------
INSERT INTO `sales_forecasts` (`forecast_id`, `sales_emp_id`, `period`, `forecast_amount`, `actual_amount`, `target_quota`, `weighted_amount`, `notes`) VALUES
  (1, 'EMP-1006', '2026-Q1', 16200000.00, 16200000.00, 15000000.00, 16200000.00, '108% of Plan (Settled)'),
  (2, 'EMP-1007', '2026-Q2', 18100000.00, 18100000.00, 17500000.00, 18100000.00, '102% of Plan (Settled)'),
  (3, 'EMP-1006', '2026-Q3', 19750000.00, 19750000.00, 19000000.00, 19750000.00, '104% of Plan (Settled)'),
  (4, 'EMP-1007', '2026-Q4', 22500000.00, 18450000.00, 22500000.00, 12800000.00, 'Active Q4 Pipeline (82% Gated)'),
  (5, 'EMP-1006', '2027-Q1', 25100000.00, '0.00', 24000000.00, 14500000.00, 'Forward Qualified Backlog')
ON DUPLICATE KEY UPDATE
  `forecast_id` = VALUES(`forecast_id`),
  `sales_emp_id` = VALUES(`sales_emp_id`),
  `period` = VALUES(`period`),
  `forecast_amount` = VALUES(`forecast_amount`),
  `actual_amount` = VALUES(`actual_amount`),
  `target_quota` = VALUES(`target_quota`),
  `weighted_amount` = VALUES(`weighted_amount`),
  `notes` = VALUES(`notes`);

-- -----------------------------------------------------------------------------
-- Data for table `crm_activities` (4 records)
-- -----------------------------------------------------------------------------
INSERT INTO `crm_activities` (`activity_id`, `activity_type`, `title`, `description`, `cus_id`, `opp_id`, `emp_id`, `created_at`) VALUES
  (1, 'Contract', 'Master Contract MSA-2024-SVST Counter-Signed', 'P. V. Cherepanov (Severstal VP Proc.) ratified the 3-Year Automation SLA.', 'CUS-1001', 5, 'EMP-1006', '2026-09-29 19:25:06'),
  (2, 'Opportunity', 'Opportunity Advanced to Negotiation', 'Talnakh Concentrator Flotation Telemetry ($2.40M) passed Phase 2 FAT review.', 'CUS-1003', 6, 'EMP-1007', '2026-09-29 19:25:06'),
  (3, 'Quote', 'Engineering Quotation Delivered (#QUO-9912)', 'Spec sheet for 16x High-Pressure Flowmeters HPF-900X dispatched to NLMK Lipetsk.', 'CUS-1002', 3, 'EMP-1010', '2026-09-29 19:25:06'),
  (4, 'Meeting', 'On-Site Technical Audit Completed', 'Dr. Elena Rostova completed sensor calibration walkthrough at Cherepovets Blast Furnace #5.', 'CUS-1001', 5, 'EMP-1006', '2026-09-29 19:25:06')
ON DUPLICATE KEY UPDATE
  `activity_id` = VALUES(`activity_id`),
  `activity_type` = VALUES(`activity_type`),
  `title` = VALUES(`title`),
  `description` = VALUES(`description`),
  `cus_id` = VALUES(`cus_id`),
  `opp_id` = VALUES(`opp_id`),
  `emp_id` = VALUES(`emp_id`),
  `created_at` = VALUES(`created_at`);

SET FOREIGN_KEY_CHECKS = 1;
-- =============================================================================
-- END OF VOSTOKPRIBOR CRM MIGRATION
-- =============================================================================
