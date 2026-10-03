-- ============================================================================
-- VOSTOKPRIBOR ENTERPRISE ECOSYSTEM - LOCKED BASELINE SEED
-- Generated according to locked enterprise specifications
-- Idempotent execution (INSERT ... ON DUPLICATE KEY UPDATE)
-- Baseline entities:
--  - 8 Departments
--  - 95 Employees (EXE:5, SAL:16, OPS:20, ENG:16, FIN:10, HRA:8, ITD:14, GOV:6)
--  - 10 Customers (CUS-1001 .. CUS-1010)
--  - 15 Projects (PRJ-2026-001 .. PRJ-2026-015)
--  - 10 Invoices (INV-2026-001 .. INV-2026-010)
--  - 15 Tickets (TKT-2026-001 .. TKT-2026-015)
--  - 15 Documents (DOC-2026-001 .. DOC-2026-015)
--  - 10 Products (PROD-1001 .. PROD-1010)
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. DEPARTMENTS (8 Canonical Departments)
INSERT INTO departments (dept_code, dept_name, main_function, employee_count_target) VALUES
('EXE', 'Executive Leadership', 'Corporate strategy, executive oversight, and governance', 5),
('SAL', 'Sales & Enterprise Relations', 'Global business development, CRM pipeline, and enterprise accounts', 16),
('OPS', 'Operations & Logistics', 'Supply chain, procurement, fulfilment, and logistics operations', 20),
('ENG', 'Engineering & Design', 'Product development, systems architecture, and technical integration', 16),
('FIN', 'Finance & Billing', 'Financial accounting, commercial billing, audit, and cashflow control', 10),
('HRA', 'Human Resources & Admin', 'Workforce administration, recruitment, onboarding, and compliance', 8),
('ITD', 'Information Technology & Security', 'IT infrastructure, helpdesk support, telemetry, and cybersecurity', 14),
('GOV', 'Governance, Risk & Compliance', 'Enterprise risk management, regulatory compliance, and security oversight', 6)
ON DUPLICATE KEY UPDATE dept_name = VALUES(dept_name), main_function = VALUES(main_function), employee_count_target = VALUES(employee_count_target);

DELETE FROM departments WHERE dept_code IN ('IT', 'LOG', 'QA');

-- 2. CUSTOMERS (10 Canonical Customers: CUS-1001 .. CUS-1010)
INSERT INTO customers (cus_id, company_name, sector, primary_contact_name, primary_contact_email, account_manager_emp_id, phone, headquarters, tax_id, health_score, account_tier, status) VALUES
('CUS-1001', 'Aral Geomatics Group', 'geomatics', 'Sergei Makarov', 's.makarov@aral-geomatics.local', 'EMP-1007', '+7 727 344-9001', 'Almaty, Kazakhstan', 'KZ-BIN-990140001', 98, 'Enterprise', 'Active'),
('CUS-1002', 'BaltNord Process Systems', 'industrial automation', 'Kristaps Ozols', 'k.ozols@baltnord.local', 'EMP-1010', '+371 67 890 123', 'Riga, Latvia', 'LV-VAT-400030129', 95, 'Enterprise', 'Active'),
('CUS-1003', 'Steppe Mining Technologies', 'mining', 'Yerlan Bektemis', 'y.bektemis@steppemining.local', 'EMP-1008', '+7 721 250-8800', 'Karaganda, Kazakhstan', 'KZ-BIN-040240008', 92, 'Tier-1 Partner', 'Active'),
('CUS-1004', 'RheinWerk Instrumentation', 'industrial measurement', 'Lukas Brandt', 'l.brandt@rheinwerk.local', 'EMP-1010', '+49 211 556-7800', 'Düsseldorf, Germany', 'DE-HRB-890123', 97, 'Enterprise', 'Active'),
('CUS-1005', 'Tashkent Precision Controls', 'manufacturing', 'Dilshod Karim', 'd.karim@tashkent-pc.local', 'EMP-1008', '+998 71 238-9900', 'Tashkent, Uzbekistan', 'UZ-INN-201889012', 90, 'Enterprise', 'Active'),
('CUS-1006', 'Daugava Optical Research', 'optical engineering', 'Mara Kalnina', 'm.kalnina@daugava-optical.local', 'EMP-1007', '+371 63 456-789', 'Daugavpils, Latvia', 'LV-VAT-401020304', 94, 'Research Institute', 'Active'),
('CUS-1007', 'Caspian Industrial Robotics', 'robotics', 'Murad Safarov', 'm.safarov@caspian-robotics.local', 'EMP-1009', '+994 12 490-5500', 'Baku, Azerbaijan', 'AZ-VOEN-14008901', 96, 'Enterprise', 'Active'),
('CUS-1008', 'Eurasia Water Automation', 'water infrastructure', 'Oleg Petrenko', 'o.petrenko@eurasia-water.local', 'EMP-1009', '+380 44 290-7700', 'Kyiv, Ukraine', 'UA-EDRPOU-3890124', 91, 'Municipal Partner', 'Active'),
('CUS-1009', 'Altai Environmental Systems', 'environmental monitoring', 'Ainur Sadyk', 'a.sadyk@altai-env.local', 'EMP-1008', '+7 723 270-3300', 'Ust-Kamenogorsk, Kazakhstan', 'KZ-BIN-080340015', 93, 'Government Entity', 'Active'),
('CUS-1010', 'CentralRail Diagnostics', 'rail infrastructure', 'Tomas Varga', 't.varga@centralrail.local', 'EMP-1006', '+36 1 480-2200', 'Budapest, Hungary', 'HU-ADOSZ-12345678', 99, 'Strategic Infrastructure', 'Active')
ON DUPLICATE KEY UPDATE 
    company_name = VALUES(company_name),
    sector = VALUES(sector),
    primary_contact_name = VALUES(primary_contact_name),
    primary_contact_email = VALUES(primary_contact_email),
    account_manager_emp_id = VALUES(account_manager_emp_id),
    phone = VALUES(phone),
    headquarters = VALUES(headquarters),
    tax_id = VALUES(tax_id),
    health_score = VALUES(health_score),
    account_tier = VALUES(account_tier);

-- Customer Contacts, Accounts & Portal Accounts
INSERT INTO contacts (cus_id, full_name, role, email, phone) VALUES ('CUS-1001', 'Sergei Makarov', 'Executive Director', 's.makarov@aral-geomatics.local', '+7 727 344-9001') ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), email = VALUES(email);
INSERT INTO customer_accounts (cus_id, username, email, password_hash, status) VALUES ('CUS-1001', 'aralgeomaticsgr_1001', 's.makarov@aral-geomatics.local', '$2y$12$tuQRcuHUI56KHfnXFCH6LetBjwTYQTTg80PxboI7QnZT1JNFNELvm', 'Active') ON DUPLICATE KEY UPDATE email = VALUES(email), status = 'Active';
INSERT INTO portal_accounts (cus_id, username, email) VALUES ('CUS-1001', 'portal_1001', 's.makarov@aral-geomatics.local') ON DUPLICATE KEY UPDATE email = VALUES(email);
INSERT INTO contacts (cus_id, full_name, role, email, phone) VALUES ('CUS-1002', 'Kristaps Ozols', 'Executive Director', 'k.ozols@baltnord.local', '+371 67 890 123') ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), email = VALUES(email);
INSERT INTO customer_accounts (cus_id, username, email, password_hash, status) VALUES ('CUS-1002', 'baltnordprocess_1002', 'k.ozols@baltnord.local', '$2y$12$tuQRcuHUI56KHfnXFCH6LetBjwTYQTTg80PxboI7QnZT1JNFNELvm', 'Active') ON DUPLICATE KEY UPDATE email = VALUES(email), status = 'Active';
INSERT INTO portal_accounts (cus_id, username, email) VALUES ('CUS-1002', 'portal_1002', 'k.ozols@baltnord.local') ON DUPLICATE KEY UPDATE email = VALUES(email);
INSERT INTO contacts (cus_id, full_name, role, email, phone) VALUES ('CUS-1003', 'Yerlan Bektemis', 'Executive Director', 'y.bektemis@steppemining.local', '+7 721 250-8800') ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), email = VALUES(email);
INSERT INTO customer_accounts (cus_id, username, email, password_hash, status) VALUES ('CUS-1003', 'steppeminingtec_1003', 'y.bektemis@steppemining.local', '$2y$12$tuQRcuHUI56KHfnXFCH6LetBjwTYQTTg80PxboI7QnZT1JNFNELvm', 'Active') ON DUPLICATE KEY UPDATE email = VALUES(email), status = 'Active';
INSERT INTO portal_accounts (cus_id, username, email) VALUES ('CUS-1003', 'portal_1003', 'y.bektemis@steppemining.local') ON DUPLICATE KEY UPDATE email = VALUES(email);
INSERT INTO contacts (cus_id, full_name, role, email, phone) VALUES ('CUS-1004', 'Lukas Brandt', 'Executive Director', 'l.brandt@rheinwerk.local', '+49 211 556-7800') ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), email = VALUES(email);
INSERT INTO customer_accounts (cus_id, username, email, password_hash, status) VALUES ('CUS-1004', 'rheinwerkinstru_1004', 'l.brandt@rheinwerk.local', '$2y$12$tuQRcuHUI56KHfnXFCH6LetBjwTYQTTg80PxboI7QnZT1JNFNELvm', 'Active') ON DUPLICATE KEY UPDATE email = VALUES(email), status = 'Active';
INSERT INTO portal_accounts (cus_id, username, email) VALUES ('CUS-1004', 'portal_1004', 'l.brandt@rheinwerk.local') ON DUPLICATE KEY UPDATE email = VALUES(email);
INSERT INTO contacts (cus_id, full_name, role, email, phone) VALUES ('CUS-1005', 'Dilshod Karim', 'Executive Director', 'd.karim@tashkent-pc.local', '+998 71 238-9900') ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), email = VALUES(email);
INSERT INTO customer_accounts (cus_id, username, email, password_hash, status) VALUES ('CUS-1005', 'tashkentprecisi_1005', 'd.karim@tashkent-pc.local', '$2y$12$tuQRcuHUI56KHfnXFCH6LetBjwTYQTTg80PxboI7QnZT1JNFNELvm', 'Active') ON DUPLICATE KEY UPDATE email = VALUES(email), status = 'Active';
INSERT INTO portal_accounts (cus_id, username, email) VALUES ('CUS-1005', 'portal_1005', 'd.karim@tashkent-pc.local') ON DUPLICATE KEY UPDATE email = VALUES(email);
INSERT INTO contacts (cus_id, full_name, role, email, phone) VALUES ('CUS-1006', 'Mara Kalnina', 'Executive Director', 'm.kalnina@daugava-optical.local', '+371 63 456-789') ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), email = VALUES(email);
INSERT INTO customer_accounts (cus_id, username, email, password_hash, status) VALUES ('CUS-1006', 'daugavaopticalr_1006', 'm.kalnina@daugava-optical.local', '$2y$12$tuQRcuHUI56KHfnXFCH6LetBjwTYQTTg80PxboI7QnZT1JNFNELvm', 'Active') ON DUPLICATE KEY UPDATE email = VALUES(email), status = 'Active';
INSERT INTO portal_accounts (cus_id, username, email) VALUES ('CUS-1006', 'portal_1006', 'm.kalnina@daugava-optical.local') ON DUPLICATE KEY UPDATE email = VALUES(email);
INSERT INTO contacts (cus_id, full_name, role, email, phone) VALUES ('CUS-1007', 'Murad Safarov', 'Executive Director', 'm.safarov@caspian-robotics.local', '+994 12 490-5500') ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), email = VALUES(email);
INSERT INTO customer_accounts (cus_id, username, email, password_hash, status) VALUES ('CUS-1007', 'caspianindustri_1007', 'm.safarov@caspian-robotics.local', '$2y$12$tuQRcuHUI56KHfnXFCH6LetBjwTYQTTg80PxboI7QnZT1JNFNELvm', 'Active') ON DUPLICATE KEY UPDATE email = VALUES(email), status = 'Active';
INSERT INTO portal_accounts (cus_id, username, email) VALUES ('CUS-1007', 'portal_1007', 'm.safarov@caspian-robotics.local') ON DUPLICATE KEY UPDATE email = VALUES(email);
INSERT INTO contacts (cus_id, full_name, role, email, phone) VALUES ('CUS-1008', 'Oleg Petrenko', 'Executive Director', 'o.petrenko@eurasia-water.local', '+380 44 290-7700') ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), email = VALUES(email);
INSERT INTO customer_accounts (cus_id, username, email, password_hash, status) VALUES ('CUS-1008', 'eurasiawateraut_1008', 'o.petrenko@eurasia-water.local', '$2y$12$tuQRcuHUI56KHfnXFCH6LetBjwTYQTTg80PxboI7QnZT1JNFNELvm', 'Active') ON DUPLICATE KEY UPDATE email = VALUES(email), status = 'Active';
INSERT INTO portal_accounts (cus_id, username, email) VALUES ('CUS-1008', 'portal_1008', 'o.petrenko@eurasia-water.local') ON DUPLICATE KEY UPDATE email = VALUES(email);
INSERT INTO contacts (cus_id, full_name, role, email, phone) VALUES ('CUS-1009', 'Ainur Sadyk', 'Executive Director', 'a.sadyk@altai-env.local', '+7 723 270-3300') ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), email = VALUES(email);
INSERT INTO customer_accounts (cus_id, username, email, password_hash, status) VALUES ('CUS-1009', 'altaienvironmen_1009', 'a.sadyk@altai-env.local', '$2y$12$tuQRcuHUI56KHfnXFCH6LetBjwTYQTTg80PxboI7QnZT1JNFNELvm', 'Active') ON DUPLICATE KEY UPDATE email = VALUES(email), status = 'Active';
INSERT INTO portal_accounts (cus_id, username, email) VALUES ('CUS-1009', 'portal_1009', 'a.sadyk@altai-env.local') ON DUPLICATE KEY UPDATE email = VALUES(email);
INSERT INTO contacts (cus_id, full_name, role, email, phone) VALUES ('CUS-1010', 'Tomas Varga', 'Executive Director', 't.varga@centralrail.local', '+36 1 480-2200') ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), email = VALUES(email);
INSERT INTO customer_accounts (cus_id, username, email, password_hash, status) VALUES ('CUS-1010', 'centralraildiag_1010', 't.varga@centralrail.local', '$2y$12$tuQRcuHUI56KHfnXFCH6LetBjwTYQTTg80PxboI7QnZT1JNFNELvm', 'Active') ON DUPLICATE KEY UPDATE email = VALUES(email), status = 'Active';
INSERT INTO portal_accounts (cus_id, username, email) VALUES ('CUS-1010', 'portal_1010', 't.varga@centralrail.local') ON DUPLICATE KEY UPDATE email = VALUES(email);

-- 3. EMPLOYEES (95 Canonical Employees: EMP-1001 .. EMP-1095)
INSERT INTO employees (emp_id, full_name, job_title, department_code, clearance_level, email, manager_emp_id, employment_status, hire_date) VALUES
('EMP-1001', 'Viktor Sokolov', 'Chief Executive Officer', 'EXE', 'L4', 'viktor.sokolov@vostokpribor.local', NULL, 'Active', '2026-01-01'),
('EMP-1002', 'Amina Karimova', 'Chief Operating Officer', 'EXE', 'L4', 'amina.karimova@vostokpribor.local', 'EMP-1001', 'Active', '2026-01-01'),
('EMP-1003', 'Daniel Weber', 'Chief Technology Officer', 'EXE', 'L4', 'daniel.weber@vostokpribor.local', 'EMP-1001', 'Active', '2026-01-01'),
('EMP-1004', 'Elena Morozova', 'Chief Information Security Officer', 'EXE', 'L4', 'elena.morozova@vostokpribor.local', 'EMP-1001', 'Active', '2026-01-01'),
('EMP-1005', 'Timur Akhmetov', 'Chief Financial Officer', 'EXE', 'L4', 'timur.akhmetov@vostokpribor.local', 'EMP-1001', 'Active', '2026-01-01'),
('EMP-1006', 'Pavel Orlov', 'VP Enterprise Sales', 'SAL', 'L3', 'pavel.orlov@vostokpribor.local', 'EMP-1001', 'Active', '2026-01-05'),
('EMP-1007', 'Sara Lindholm', 'Senior Key Account Manager', 'SAL', 'L3', 'sara.lindholm@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-08'),
('EMP-1008', 'Bekzod Rakhimov', 'Lead Technical Sales Engineer', 'SAL', 'L3', 'bekzod.rakhimov@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-10'),
('EMP-1009', 'Nadia Petrova', 'Senior Account Executive', 'SAL', 'L3', 'nadia.petrova@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-12'),
('EMP-1010', 'Markus Klein', 'Strategic Account Director', 'SAL', 'L3', 'markus.klein@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-15'),
('EMP-1011', 'Arman Tulegenov', 'Director of Operations', 'OPS', 'L3', 'arman.tulegenov@vostokpribor.local', 'EMP-1002', 'Active', '2026-01-05'),
('EMP-1012', 'Rustam Bekov', 'Lead Procurement & Logistics Specialist', 'OPS', 'L3', 'rustam.bekov@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-08'),
('EMP-1013', 'Ilona Vetra', 'Senior Fulfilment & Supply Coordinator', 'OPS', 'L3', 'ilona.vetra@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-10'),
('EMP-1014', 'Mikhail Antonov', 'Warehouse Operations Supervisor', 'OPS', 'L2', 'mikhail.antonov@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-15'),
('EMP-1015', 'Kamila Nurzhan', 'Logistics Dispatch Specialist', 'OPS', 'L2', 'kamila.nurzhan@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-20'),
('EMP-1016', 'Erik Hansen', 'Principal Systems Architect', 'ENG', 'L3', 'erik.hansen@vostokpribor.local', 'EMP-1003', 'Active', '2026-01-05'),
('EMP-1017', 'Dana Yermak', 'Senior Automation Engineer', 'ENG', 'L3', 'dana.yermak@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-08'),
('EMP-1018', 'Leonid Volkov', 'Lead Hardware Integration Engineer', 'ENG', 'L3', 'leonid.volkov@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-10'),
('EMP-1019', 'Farida Iskakova', 'Senior SCADA Implementation Lead', 'ENG', 'L3', 'farida.iskakova@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-12'),
('EMP-1020', 'Jonas Richter', 'Firmware & Embedded Systems Engineer', 'ENG', 'L3', 'jonas.richter@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-15'),
('EMP-1021', 'Ahmad AbuNijim', 'IT Systems Administrator', 'ITD', 'L2', 'ahmad.abunijim@vostokpribor.local', 'EMP-1003', 'Active', '2026-01-20'),
('EMP-1022', 'Dmitry Kozlov', 'Enterprise Account Executive', 'SAL', 'L2', 'd.kozlov@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-20'),
('EMP-1023', 'Anna Semyonova', 'Key Account Manager', 'SAL', 'L2', 'a.semyonova@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-20'),
('EMP-1024', 'Igor Tarasov', 'B2B Sales Specialist', 'SAL', 'L2', 'i.tarasov@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-20'),
('EMP-1025', 'Olga Romanova', 'Client Relationship Executive', 'SAL', 'L2', 'o.romanova@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-20'),
('EMP-1026', 'Maxim Belyayev', 'Regional Sales Representative', 'SAL', 'L2', 'm.belyayev@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-20'),
('EMP-1027', 'Ekaterina Novikova', 'Commercial Contract Manager', 'SAL', 'L2', 'e.novikova@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-20'),
('EMP-1028', 'Andrei Morozov', 'Sales Operations Analyst', 'SAL', 'L2', 'a.morozov@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-20'),
('EMP-1029', 'Yulia Volkova', 'Export Sales Specialist', 'SAL', 'L2', 'y.volkova@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-20'),
('EMP-1030', 'Konstantin Lebedev', 'Technical Account Manager', 'SAL', 'L2', 'k.lebedev@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-20'),
('EMP-1031', 'Marina Pavlova', 'Inside Sales Representative', 'SAL', 'L2', 'm.pavlova@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-20'),
('EMP-1032', 'Sergey Fedorov', 'Strategic Partnership Lead', 'SAL', 'L2', 's.fedorov@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-20'),
('EMP-1033', 'Roman Vasilyev', 'Supply Chain Analyst', 'OPS', 'L2', 'r.vasilyev@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-20'),
('EMP-1034', 'Natalia Zakharova', 'Procurement Specialist', 'OPS', 'L2', 'n.zakharova@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-20'),
('EMP-1035', 'Alexey Kuznetsov', 'Inventory Control Supervisor', 'OPS', 'L2', 'a.kuznetsov@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-20'),
('EMP-1036', 'Tatyana Grigoryeva', 'Logistics Operations Lead', 'OPS', 'L2', 't.grigoryeva@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-20'),
('EMP-1037', 'Denis Melnikov', 'Warehouse Logistics Specialist', 'OPS', 'L2', 'd.melnikov@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-20'),
('EMP-1038', 'Svetlana Borisova', 'Fulfilment Dispatch Coordinator', 'OPS', 'L2', 's.borisova@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-20'),
('EMP-1039', 'Valery Stepanov', 'Shipping & Receiving Inspector', 'OPS', 'L2', 'v.stepanov@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-20'),
('EMP-1040', 'Lyudmila Alexandrova', 'Supply Quality Specialist', 'OPS', 'L2', 'l.alexandrova@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-20'),
('EMP-1041', 'Grigory Danilov', 'Materials Management Planner', 'OPS', 'L2', 'g.danilov@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-20'),
('EMP-1042', 'Polina Yakovleva', 'B2B Order Expeditor', 'OPS', 'L2', 'p.yakovleva@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-20'),
('EMP-1043', 'Vladislav Sorokin', 'Equipment Packaging Engineer', 'OPS', 'L2', 'v.sorokin@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-20'),
('EMP-1044', 'Ksenia Vorobyeva', 'Procurement Auditor', 'OPS', 'L2', 'k.vorobyeva@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-20'),
('EMP-1045', 'Stanislav Solovyov', 'Logistics Fleet Dispatcher', 'OPS', 'L2', 's.solovyov@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-20'),
('EMP-1046', 'Daria Guseva', 'Customs Clearance Specialist', 'OPS', 'L2', 'd.guseva@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-20'),
('EMP-1047', 'Kirill Titov', 'Inbound Logistics Controller', 'OPS', 'L2', 'k.titov@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-20'),
('EMP-1048', 'Mikhail Zaytsev', 'Senior Optical Systems Designer', 'ENG', 'L3', 'm.zaytsev@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-20'),
('EMP-1049', 'Alina Bogdanova', 'Hardware Test Engineer', 'ENG', 'L3', 'a.bogdanova@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-20'),
('EMP-1050', 'Yaroslav Belov', 'Firmware Validation Specialist', 'ENG', 'L3', 'y.belov@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-20'),
('EMP-1051', 'Evgenia Maksimova', 'SCADA Integration Specialist', 'ENG', 'L3', 'e.maksimova@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-20'),
('EMP-1052', 'Artyom Kudryavtsev', 'Precision Calibration Engineer', 'ENG', 'L3', 'a.kudryavtsev@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-20'),
('EMP-1053', 'Victoria Chernova', 'Embedded Linux Developer', 'ENG', 'L3', 'v.chernova@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-20'),
('EMP-1054', 'Nikita Panov', 'Industrial Telemetry Engineer', 'ENG', 'L3', 'n.panov@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-20'),
('EMP-1055', 'Larisa Timofeeva', 'Quality Assurance Test Lead', 'ENG', 'L3', 'l.timofeeva@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-20'),
('EMP-1056', 'Anton Denisov', 'Mechatronics Integration Engineer', 'ENG', 'L3', 'a.denisov@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-20'),
('EMP-1057', 'Veronika Savelyeva', 'Optical Sensor QA Analyst', 'ENG', 'L3', 'v.savelyeva@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-20'),
('EMP-1058', 'Stepan Matveev', 'Industrial Protocols Specialist', 'ENG', 'L3', 's.matveev@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-20'),
('EMP-1059', 'Boris Filatov', 'Finance Director & Controller', 'FIN', 'L3', 'b.filatov@vostokpribor.local', 'EMP-1005', 'Active', '2026-01-20'),
('EMP-1060', 'Inga Vlasova', 'Senior Financial Accountant', 'FIN', 'L2', 'i.vlasova@vostokpribor.local', 'EMP-1005', 'Active', '2026-01-20'),
('EMP-1061', 'Gennady Maslov', 'B2B Billing Operations Lead', 'FIN', 'L2', 'g.maslov@vostokpribor.local', 'EMP-1005', 'Active', '2026-01-20'),
('EMP-1062', 'Nadezhda Isayeva', 'Accounts Payable Specialist', 'FIN', 'L2', 'n.isayeva@vostokpribor.local', 'EMP-1005', 'Active', '2026-01-20'),
('EMP-1063', 'Semen Bobrov', 'Accounts Receivable Controller', 'FIN', 'L2', 's.bobrov@vostokpribor.local', 'EMP-1005', 'Active', '2026-01-20'),
('EMP-1064', 'Vera Zhukova', 'Corporate Tax & Compliance Accountant', 'FIN', 'L2', 'v.zhukova@vostokpribor.local', 'EMP-1005', 'Active', '2026-01-20'),
('EMP-1065', 'Oleg Bykov', 'Treasury & Cashflow Analyst', 'FIN', 'L2', 'o.bykov@vostokpribor.local', 'EMP-1005', 'Active', '2026-01-20'),
('EMP-1066', 'Alla Tretyakova', 'Project Cost Accounting Analyst', 'FIN', 'L2', 'a.tretyakova@vostokpribor.local', 'EMP-1005', 'Active', '2026-01-20'),
('EMP-1067', 'Fedor Nikitin', 'ERP Financial Data Reconciler', 'FIN', 'L2', 'f.nikitin@vostokpribor.local', 'EMP-1005', 'Active', '2026-01-20'),
('EMP-1068', 'Tamara Kulikova', 'Senior Invoicing Auditor', 'FIN', 'L2', 't.kulikova@vostokpribor.local', 'EMP-1005', 'Active', '2026-01-20'),
('EMP-1069', 'Ksenia Lavrova', 'Director of Human Resources', 'HRA', 'L3', 'k.lavrova@vostokpribor.local', 'EMP-1002', 'Active', '2026-01-20'),
('EMP-1070', 'Matvey Rodionov', 'Senior HR Operations Specialist', 'HRA', 'L2', 'm.rodionov@vostokpribor.local', 'EMP-1002', 'Active', '2026-01-20'),
('EMP-1071', 'Zhanna Simonova', 'Talent Acquisition & Recruiting Lead', 'HRA', 'L2', 'z.simonova@vostokpribor.local', 'EMP-1002', 'Active', '2026-01-20'),
('EMP-1072', 'Yury Medvedev', 'Employee Relations Coordinator', 'HRA', 'L2', 'y.medvedev@vostokpribor.local', 'EMP-1002', 'Active', '2026-01-20'),
('EMP-1073', 'Diana Antonova', 'Workforce Training & Compliance Officer', 'HRA', 'L2', 'd.antonova@vostokpribor.local', 'EMP-1002', 'Active', '2026-01-20'),
('EMP-1074', 'Leonid Fomichev', 'HRIS Systems Administrator', 'HRA', 'L2', 'l.fomichev@vostokpribor.local', 'EMP-1002', 'Active', '2026-01-20'),
('EMP-1075', 'Kristina Markova', 'Personnel Dossier & Onboarding Specialist', 'HRA', 'L2', 'k.markova@vostokpribor.local', 'EMP-1002', 'Active', '2026-01-20'),
('EMP-1076', 'Ilya Gavrilov', 'Benefits & Payroll Specialist', 'HRA', 'L2', 'i.gavrilov@vostokpribor.local', 'EMP-1002', 'Active', '2026-01-20'),
('EMP-1077', 'Alexander Krylov', 'IT Infrastructure & Cloud Architect', 'ITD', 'L3', 'a.krylov@vostokpribor.local', 'EMP-1003', 'Active', '2026-01-20'),
('EMP-1078', 'Regina Karimova', 'Senior Cybersecurity Analyst', 'ITD', 'L3', 'r.karimova@vostokpribor.local', 'EMP-1003', 'Active', '2026-01-20'),
('EMP-1079', 'Vadim Dorofeev', 'DevOps & CI/CD Systems Engineer', 'ITD', 'L3', 'v.dorofeev@vostokpribor.local', 'EMP-1003', 'Active', '2026-01-20'),
('EMP-1080', 'Yana Blinova', 'IT Helpdesk Team Lead', 'ITD', 'L2', 'y.blinova@vostokpribor.local', 'EMP-1003', 'Active', '2026-01-20'),
('EMP-1081', 'Ruslan Kasimov', 'Network Operations Specialist', 'ITD', 'L2', 'r.kasimov@vostokpribor.local', 'EMP-1003', 'Active', '2026-01-20'),
('EMP-1082', 'Elizaveta Shirokova', 'Identity & Access Management Engineer', 'ITD', 'L2', 'e.shirokova@vostokpribor.local', 'EMP-1003', 'Active', '2026-01-20'),
('EMP-1083', 'Arthur Davydov', 'Database Administrator', 'ITD', 'L2', 'a.davydov@vostokpribor.local', 'EMP-1003', 'Active', '2026-01-20'),
('EMP-1084', 'Kira Samsonova', 'Hardware Asset Provisioning Specialist', 'ITD', 'L2', 'k.samsonova@vostokpribor.local', 'EMP-1003', 'Active', '2026-01-20'),
('EMP-1085', 'Timofey Kazakov', 'IT Service Desk Specialist', 'ITD', 'L2', 't.kazakov@vostokpribor.local', 'EMP-1003', 'Active', '2026-01-20'),
('EMP-1086', 'Snezhana Konovalova', 'Telemetry & SCADA Gateway Administrator', 'ITD', 'L2', 's.konovalova@vostokpribor.local', 'EMP-1003', 'Active', '2026-01-20'),
('EMP-1087', 'Albert Zakirov', 'Systems Support Technician', 'ITD', 'L2', 'a.zakirov@vostokpribor.local', 'EMP-1003', 'Active', '2026-01-20'),
('EMP-1088', 'Maya Chernyakhovskaya', 'IT Security Operations Analyst', 'ITD', 'L2', 'm.chernyakhovskaya@vostokpribor.local', 'EMP-1003', 'Active', '2026-01-20'),
('EMP-1089', 'Eduard Potapov', 'Storage Appliance & Backup Administrator', 'ITD', 'L2', 'e.potapov@vostokpribor.local', 'EMP-1003', 'Active', '2026-01-20'),
('EMP-1090', 'Vyacheslav Gromov', 'Director of Governance & Compliance', 'GOV', 'L4', 'v.gromov@vostokpribor.local', 'EMP-1004', 'Active', '2026-01-20'),
('EMP-1091', 'Roza Akhmetova', 'Senior Internal Security Auditor', 'GOV', 'L3', 'r.akhmetova@vostokpribor.local', 'EMP-1004', 'Active', '2026-01-20'),
('EMP-1092', 'Lev Krasnov', 'Enterprise Risk Assessment Specialist', 'GOV', 'L3', 'l.krasnov@vostokpribor.local', 'EMP-1004', 'Active', '2026-01-20'),
('EMP-1093', 'Zoya Pakhomova', 'Regulatory Affairs Officer', 'GOV', 'L3', 'z.pakhomova@vostokpribor.local', 'EMP-1004', 'Active', '2026-01-20'),
('EMP-1094', 'Gleb Arkhipov', 'Access Governance Analyst', 'GOV', 'L3', 'g.arkhipov@vostokpribor.local', 'EMP-1004', 'Active', '2026-01-20'),
('EMP-1095', 'Milana Shcherbakova', 'Policy & Legal Compliance Lead', 'GOV', 'L3', 'm.shcherbakova@vostokpribor.local', 'EMP-1004', 'Active', '2026-01-20')
ON DUPLICATE KEY UPDATE 
    full_name = VALUES(full_name),
    job_title = VALUES(job_title),
    department_code = VALUES(department_code),
    clearance_level = VALUES(clearance_level),
    email = VALUES(email),
    manager_emp_id = VALUES(manager_emp_id),
    employment_status = VALUES(employment_status);

-- System Account EMP-0001 is preserved and managed via Migration 007 (is_system_account = 1)

-- Employee Accounts & Core Roles
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1001', 'viktor.sokolov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1002', 'amina.karimova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1003', 'daniel.weber', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1004', 'elena.morozova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1005', 'timur.akhmetov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1006', 'pavel.orlov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1007', 'sara.lindholm', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1008', 'bekzod.rakhimov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1009', 'nadia.petrova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1010', 'markus.klein', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1011', 'arman.tulegenov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1012', 'rustam.bekov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1013', 'ilona.vetra', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1014', 'mikhail.antonov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1015', 'kamila.nurzhan', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1016', 'erik.hansen', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1017', 'dana.yermak', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1018', 'leonid.volkov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1019', 'farida.iskakova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1020', 'jonas.richter', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1021', 'ahmad.abunijim', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1022', 'd.kozlov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1023', 'a.semyonova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1024', 'i.tarasov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1025', 'o.romanova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1026', 'm.belyayev', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1027', 'e.novikova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1028', 'a.morozov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1029', 'y.volkova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1030', 'k.lebedev', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1031', 'm.pavlova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1032', 's.fedorov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1033', 'r.vasilyev', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1034', 'n.zakharova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1035', 'a.kuznetsov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1036', 't.grigoryeva', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1037', 'd.melnikov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1038', 's.borisova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1039', 'v.stepanov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1040', 'l.alexandrova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1041', 'g.danilov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1042', 'p.yakovleva', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1043', 'v.sorokin', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1044', 'k.vorobyeva', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1045', 's.solovyov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1046', 'd.guseva', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1047', 'k.titov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1048', 'm.zaytsev', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1049', 'a.bogdanova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1050', 'y.belov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1051', 'e.maksimova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1052', 'a.kudryavtsev', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1053', 'v.chernova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1054', 'n.panov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1055', 'l.timofeeva', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1056', 'a.denisov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1057', 'v.savelyeva', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1058', 's.matveev', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1059', 'b.filatov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1060', 'i.vlasova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1061', 'g.maslov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1062', 'n.isayeva', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1063', 's.bobrov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1064', 'v.zhukova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1065', 'o.bykov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1066', 'a.tretyakova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1067', 'f.nikitin', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1068', 't.kulikova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1069', 'k.lavrova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1070', 'm.rodionov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1071', 'z.simonova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1072', 'y.medvedev', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1073', 'd.antonova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1074', 'l.fomichev', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1075', 'k.markova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1076', 'i.gavrilov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1077', 'a.krylov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1078', 'r.karimova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1079', 'v.dorofeev', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1080', 'y.blinova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1081', 'r.kasimov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1082', 'e.shirokova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1083', 'a.davydov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1084', 'k.samsonova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1085', 't.kazakov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1086', 's.konovalova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1087', 'a.zakirov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1088', 'm.chernyakhovskaya', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1089', 'e.potapov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1090', 'v.gromov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1091', 'r.akhmetova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1092', 'l.krasnov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1093', 'z.pakhomova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1094', 'g.arkhipov', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';
INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('EMP-1095', 'm.shcherbakova', '$2y$12$p3oSP14.iTjL486AkBdZu.3G/8tRxQO.up5w13JnW9YxNvBTthG3e', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';

-- Assign Roles in employee_roles
INSERT INTO employee_roles (emp_id, role_id, granted_at, granted_by_emp_id) VALUES
('EMP-1001', 1, NOW(), 'EMP-1001'), -- SuperAdmin (CEO)
('EMP-1004', 1, NOW(), 'EMP-1001'), -- SuperAdmin (CISO)
('EMP-1002', 7, NOW(), 'EMP-1001'), -- HR Director (COO)
('EMP-1003', 4, NOW(), 'EMP-1001'), -- CTO (Developer & Systems)
('EMP-1005', 6, NOW(), 'EMP-1001'), -- CFO (Finance Controller)
('EMP-1006', 3, NOW(), 'EMP-1001'), -- Sales Director
('EMP-1007', 3, NOW(), 'EMP-1006'), -- Sales Key Account Mgr
('EMP-1008', 3, NOW(), 'EMP-1006'), -- Sales Engineer
('EMP-1009', 3, NOW(), 'EMP-1006'), -- Sales Account Exec
('EMP-1010', 3, NOW(), 'EMP-1006'), -- Sales Account Director
('EMP-1011', 8, NOW(), 'EMP-1001'), -- Operations Director
('EMP-1012', 8, NOW(), 'EMP-1011'), -- Logistics Specialist
('EMP-1013', 8, NOW(), 'EMP-1011'), -- Fulfilment Specialist
('EMP-1014', 8, NOW(), 'EMP-1011'), -- Warehouse Supervisor
('EMP-1015', 8, NOW(), 'EMP-1011'), -- Logistics Dispatcher
('EMP-1016', 4, NOW(), 'EMP-1003'), -- Systems Architect
('EMP-1017', 4, NOW(), 'EMP-1016'), -- Automation Engineer
('EMP-1018', 5, NOW(), 'EMP-1003'), -- Systems & Security Engineer
('EMP-1019', 4, NOW(), 'EMP-1016'), -- SCADA Lead
('EMP-1020', 4, NOW(), 'EMP-1016'), -- Embedded Systems
('EMP-1021', 5, NOW(), 'EMP-1003')  -- IT Systems Administrator
ON DUPLICATE KEY UPDATE granted_at = NOW();

-- 4. PROJECTS (15 Canonical Projects: PRJ-2026-001 .. PRJ-2026-015)
INSERT INTO projects (prj_id, project_name, cus_id, project_manager_emp_id, budget, currency, status, start_date, end_date, progress_percent, facility_location, scope_summary) VALUES
('PRJ-2026-001', 'Aral Geomatics Laser Telemetry Array', 'CUS-1001', 'EMP-1019', 185000.00, 'EUR', 'Execution', '2026-01-10', '2026-08-30', 65, 'Balkhash Observation Station', 'Automated geodetic and atmospheric lidar monitoring network'),
('PRJ-2026-002', 'BaltNord SCADA Refurbishment Skid', 'CUS-1002', 'EMP-1019', 240000.00, 'EUR', 'Integration', '2026-02-01', '2026-09-15', 50, 'Riga Terminal Terminal 3', 'High-speed redundant optical bus and field telemetry modernization'),
('PRJ-2026-003', 'Steppe Mining Deep Shaft Telemetry', 'CUS-1003', 'EMP-1016', 410000.00, 'EUR', 'Procurement', '2026-02-15', '2026-11-30', 25, 'Karaganda Pit Mine #4', 'Explosion-proof hazardous environment gas and strain monitoring sensors'),
('PRJ-2026-004', 'RheinWerk Metrology Cleanroom Automation', 'CUS-1004', 'EMP-1019', 165000.00, 'EUR', 'Execution', '2026-01-20', '2026-07-31', 75, 'Düsseldorf Metrology Hall', 'Sub-micron automated optical inspection and thermal chamber alignment'),
('PRJ-2026-005', 'Tashkent Plant Chemical Dosing Controller', 'CUS-1005', 'EMP-1017', 128000.00, 'EUR', 'Integration', '2026-03-01', '2026-10-15', 40, 'Chirchik Industrial Zone', 'Digital closed-loop PID control and hazardous fluid flow telemetry'),
('PRJ-2026-006', 'Daugava Laser Interferometry Bench', 'CUS-1006', 'EMP-1016', 96000.00, 'EUR', 'Design', '2026-03-10', '2026-09-30', 20, 'Daugavpils Laser Lab', 'Vibration-isolated precision optical measurement bench with photon counter'),
('PRJ-2026-007', 'Caspian Welding Robot Vision Guiding', 'CUS-1007', 'EMP-1019', 315000.00, 'EUR', 'Integration', '2026-01-15', '2026-08-15', 55, 'Baku Shipyard Bay 2', 'Real-time 3D laser seam tracking and adaptive weld robotic guidance'),
('PRJ-2026-008', 'Eurasia Municipal Pumping Station Grid', 'CUS-1008', 'EMP-1017', 205000.00, 'EUR', 'Execution', '2026-02-10', '2026-10-31', 60, 'Dnipro Water Intake Facility', 'Telemetry gateway cluster with cellular failover and automated pressure regulation'),
('PRJ-2026-009', 'Altai Basin Eco-Telemetry Station Array', 'CUS-1009', 'EMP-1016', 142000.00, 'EUR', 'Testing', '2026-01-25', '2026-07-15', 80, 'Katun River Basin Station', 'Autonomous solar-powered hydrological station network with satellite uplink'),
('PRJ-2026-010', 'CentralRail High-Speed Track Geometry', 'CUS-1010', 'EMP-1019', 275000.00, 'EUR', 'Procurement', '2026-03-05', '2026-12-15', 15, 'Budapest Keleti Test Track', 'Dynamic optical rail profile scanner and acceleration vibration analyzer'),
('PRJ-2026-011', 'Aral Geomatics Base Station Maintenance', 'CUS-1001', 'EMP-1017', 74000.00, 'EUR', 'Maintenance', '2026-01-01', '2026-12-31', 45, 'Aralsk Calibration Field', 'Scheduled annual calibration and firmware maintenance contract'),
('PRJ-2026-012', 'BaltNord Line 2 Vision Upgrade', 'CUS-1002', 'EMP-1016', 188000.00, 'EUR', 'Design', '2026-03-15', '2026-11-15', 10, 'Jelgava Packaging Plant', 'High-speed multi-camera bottle inspection with defect rejection'),
('PRJ-2026-013', 'Tashkent SCADA Security Hardening', 'CUS-1005', 'EMP-1019', 112000.00, 'EUR', 'ContractReview', '2026-04-01', '2026-11-30', 5, 'Tashkent Plant B', 'OT network segmentation, unidirectional gateway and anomaly detection'),
('PRJ-2026-014', 'Caspian Autonomous Subsea Crawler', 'CUS-1007', 'EMP-1017', 260000.00, 'EUR', 'Procurement', '2026-03-20', '2027-01-31', 10, 'Sangachal Offshore Base', 'Subsea inspection crawler with dual acoustic telemetry and HD cameras'),
('PRJ-2026-015', 'CentralRail Depot Wheel Profiler', 'CUS-1010', 'EMP-1016', 151000.00, 'EUR', 'Planning', '2026-04-15', '2026-12-31', 0, 'Debrecen Maintenance Yard', 'In-track laser wheel geometry inspection skid with automated reporting')
ON DUPLICATE KEY UPDATE 
    project_name = VALUES(project_name),
    cus_id = VALUES(cus_id),
    project_manager_emp_id = VALUES(project_manager_emp_id),
    budget = VALUES(budget),
    currency = VALUES(currency),
    status = VALUES(status),
    start_date = VALUES(start_date),
    end_date = VALUES(end_date),
    progress_percent = VALUES(progress_percent),
    facility_location = VALUES(facility_location),
    scope_summary = VALUES(scope_summary);

-- 5. INVOICES (10 Canonical Invoices: INV-2026-001 .. INV-2026-010)
INSERT INTO invoices (inv_id, cus_id, prj_id, created_by_emp_id, total_value, currency, payment_status, issued_at, due_date, payment_terms, notes, paid_at) VALUES
('INV-2026-001', 'CUS-1001', 'PRJ-2026-001', 'EMP-1005', 46250.00, 'EUR', 'Paid', '2026-01-15', '2026-02-14', 'Net-30', 'Aral Geomatics Laser Array - Milestone 1 Sign-off', '2026-02-10'),
('INV-2026-002', 'CUS-1002', 'PRJ-2026-002', 'EMP-1005', 80000.00, 'EUR', 'Pending', '2026-02-15', '2026-03-17', 'Net-30', 'BaltNord SCADA Refurbishment - Hardware Advance', NULL),
('INV-2026-003', 'CUS-1003', 'PRJ-2026-003', 'EMP-1005', 137500.00, 'EUR', 'Paid', '2026-02-28', '2026-03-30', 'Net-30', 'Steppe Mining Deep Shaft - Sensor Procurement Tranche', '2026-03-25'),
('INV-2026-004', 'CUS-1004', 'PRJ-2026-004', 'EMP-1005', 55000.00, 'EUR', 'Pending', '2026-03-01', '2026-03-31', 'Net-30', 'RheinWerk Metrology - Cleanroom Bench Integration Phase 1', NULL),
('INV-2026-005', 'CUS-1005', 'PRJ-2026-005', 'EMP-1005', 42000.00, 'EUR', 'Paid', '2026-03-10', '2026-04-09', 'Net-30', 'Tashkent Plant Chemical Dosing - Engineering Design Final', '2026-04-05'),
('INV-2026-006', 'CUS-1006', 'PRJ-2026-006', 'EMP-1005', 32000.00, 'EUR', 'Pending', '2026-03-15', '2026-04-14', 'Net-30', 'Daugava Laser Interferometry - Prototype Fabrication', NULL),
('INV-2026-007', 'CUS-1007', 'PRJ-2026-007', 'EMP-1005', 105000.00, 'EUR', 'Paid', '2026-01-30', '2026-03-01', 'Net-30', 'Caspian Welding Robot - Vision Head Commissioning', '2026-02-28'),
('INV-2026-008', 'CUS-1008', 'PRJ-2026-008', 'EMP-1005', 68333.00, 'EUR', 'Pending', '2026-02-20', '2026-03-22', 'Net-30', 'Eurasia Water Pumping Grid - Phase 1 Telemetry Deployment', NULL),
('INV-2026-009', 'CUS-1009', 'PRJ-2026-009', 'EMP-1005', 47333.00, 'EUR', 'Paid', '2026-02-10', '2026-03-12', 'Net-30', 'Altai Basin Eco-Telemetry - Sensor Calibration Package', '2026-03-08'),
('INV-2026-010', 'CUS-1010', 'PRJ-2026-010', 'EMP-1005', 91667.00, 'EUR', 'Pending', '2026-03-12', '2026-04-11', 'Net-30', 'CentralRail Track Geometry - Laser Scanner Advance Payment', NULL)
ON DUPLICATE KEY UPDATE 
    cus_id = VALUES(cus_id),
    prj_id = VALUES(prj_id),
    total_value = VALUES(total_value),
    currency = VALUES(currency),
    payment_status = VALUES(payment_status),
    notes = VALUES(notes);

-- 6. TICKETS (15 Canonical Tickets: TKT-2026-001 .. TKT-2026-015)
INSERT INTO tickets (tkt_id, requester_type, requester_cus_id, requester_emp_id, source_system, title, description, requester_name, requester_role, requester_dept, priority, assigned_emp_id, status) VALUES
('TKT-2026-001', 'Customer', 'CUS-1001', NULL, 'CUS', 'Customer Portal B2B API Latency Degraded', 'API gateway latency spiked to 1,200ms on invoice document retrieval endpoint.', 'Sergei Makarov', 'Procurement Director', 'Procurement', 'High', 'EMP-1021', 'InProgress'),
('TKT-2026-002', 'Employee', NULL, 'EMP-1006', 'CRM', 'CRM Contact Synchronization Timeout on Russian Railways Sync', 'Automated nightly sync timed out after 300s waiting for partner endpoint.', 'Pavel Orlov', 'VP Enterprise Sales', 'Sales', 'Medium', 'EMP-1018', 'Resolved'),
('TKT-2026-003', 'Customer', 'CUS-1002', NULL, 'SHP', 'B2B Shop Checkout Cart Lock during Batch Invoicing', 'Simultaneous checkout of 40 optical units triggered deadlock in orders table.', 'Kristaps Ozols', 'Lead Automation Architect', 'Engineering', 'High', 'EMP-1017', 'Investigating'),
('TKT-2026-004', 'Employee', NULL, 'EMP-1007', 'EMP', 'Corporate Intranet Knowledge Base Search Index Rebuild Required', 'Elastic index out of sync following SOP revision uploads.', 'Sara Lindholm', 'Key Account Manager', 'Sales', 'Low', 'EMP-1021', 'Resolved'),
('TKT-2026-005', 'Customer', 'CUS-1003', NULL, 'CUS', 'Customer Portal TLS Handshake Error on Gateway 02', 'Intermittent TLS 1.3 handshake resets reported from Karaganda access proxy.', 'Yerlan Bektemis', 'Chief Mining Engineer', 'Operations', 'Critical', 'EMP-1004', 'Escalated'),
('TKT-2026-006', 'Employee', NULL, 'EMP-1016', 'DEV', 'API Gateway OAuth2 Token Revocation Endpoint Intermittent 502', 'FastCGI buffer exhausted during bulk token revocation test run.', 'Erik Hansen', 'Principal Systems Architect', 'Engineering', 'Medium', 'EMP-1018', 'Resolved'),
('TKT-2026-007', 'Employee', NULL, 'EMP-1005', 'FIN', 'ERP 1C Billing Data Reconciliation Discrepancy Q1', 'Discrepancy of 1,420 EUR between ERP invoice register and bank statement.', 'Timur Akhmetov', 'Chief Financial Officer', 'Finance', 'Medium', 'EMP-1005', 'Resolved'),
('TKT-2026-008', 'Customer', 'CUS-1004', NULL, 'SHP', 'Shop Catalog Price Cache Invalidation Delay', 'Discounted contract pricing for RheinWerk failed to reflect immediately.', 'Lukas Brandt', 'Managing Director', 'Management', 'Medium', 'EMP-1017', 'InProgress'),
('TKT-2026-009', 'Employee', NULL, 'EMP-1002', 'EMP', 'Single Sign-On Session Timeout Too Short on Mobile Intranet', 'Engineers in cleanroom disconnected every 15 minutes while entering inspection logs.', 'Amina Karimova', 'Chief Operating Officer', 'Operations', 'Low', 'EMP-1021', 'Resolved'),
('TKT-2026-010', 'Employee', NULL, 'EMP-1008', 'CRM', 'Lead Routing Rule Failover for Export Contracts', 'Export leads from DACH region not automatically routing to Markus Klein queue.', 'Bekzod Rakhimov', 'Lead Technical Sales', 'Sales', 'High', 'EMP-1006', 'Investigating'),
('TKT-2026-011', 'Employee', NULL, 'EMP-1004', 'ADM', 'Admin Portal Audit Log Archive Retention Rule Execution', 'Automated 1-year archive script paused due to cold storage mount timeout.', 'Elena Morozova', 'CISO', 'Governance', 'High', 'EMP-1004', 'Escalated'),
('TKT-2026-012', 'Employee', NULL, 'EMP-1003', 'IT', 'Backup Storage Appliance LUN Snapshot Pool Warning (82% full)', 'Secondary ZFS pool reaching warning threshold after quarterly image dump.', 'Daniel Weber', 'Chief Technology Officer', 'IT', 'Medium', 'EMP-1018', 'Resolved'),
('TKT-2026-013', 'Employee', NULL, 'EMP-1002', 'HR', 'Employee Onboarding Workflow Automated Provisioning Stuck', 'LDAP group creation step failed on trailing whitespace in department code.', 'Amina Karimova', 'Chief Operating Officer', 'Operations', 'Low', 'EMP-1021', 'Resolved'),
('TKT-2026-014', 'Customer', 'CUS-1005', NULL, 'FIN', 'Invoice PDF Generator Font Rendering Exception', 'Cyrillic characters in Tashkent subsidiary address appearing as question marks in PDF.', 'Dilshod Karim', 'Head of Procurement', 'Procurement', 'Medium', 'EMP-1017', 'InProgress'),
('TKT-2026-015', 'Customer', 'CUS-1007', NULL, 'SHP', 'Customer Order Status Tracking Webhook Delivery Failure', 'Order dispatch webhook returned HTTP 403 on client ingress proxy.', 'Murad Safarov', 'Robotics Systems Director', 'Engineering', 'High', 'EMP-1018', 'Investigating')
ON DUPLICATE KEY UPDATE 
    title = VALUES(title),
    description = VALUES(description),
    priority = VALUES(priority),
    assigned_emp_id = VALUES(assigned_emp_id),
    status = VALUES(status);

-- 7. DOCUMENTS (15 Canonical Documents: DOC-2026-001 .. DOC-2026-015)
INSERT INTO documents (doc_id, file_name, description, classification, folder, department, file_size, status, retention_period, project_ref, customer_ref, owner_emp_id, related_prj_id, related_cus_id) VALUES
('DOC-2026-001', 'Corporate_Information_Security_Policy.pdf', 'Enterprise cybersecurity policies, data classification guidelines, and incident response requirements.', 'TopSecret', 'policies', 'GOV', '1.8 MB', 'Approved', '10y', NULL, NULL, 'EMP-1004', NULL, NULL),
('DOC-2026-002', 'Customer_Onboarding_Standard.pdf', 'Standard operating procedure SOP-01 for lead conversion, KYC, customer account provisioning and workspace setup.', 'Confidential', 'sop', 'SAL', '2.2 MB', 'Approved', '7y', NULL, NULL, 'EMP-1006', NULL, NULL),
('DOC-2026-003', 'PRJ-2026-001_Statement_of_Work.pdf', 'Technical specification and statement of work for Aral Geomatics Laser Telemetry Array integration.', 'Confidential', 'projects', 'ENG', '4.1 MB', 'Approved', '7y', 'PRJ-2026-001', 'Aral Geomatics Group', 'EMP-1019', 'PRJ-2026-001', 'CUS-1001'),
('DOC-2026-004', 'PRJ-2026-002_Integration_Specification.pdf', 'Engineering blueprint for BaltNord Process Systems SCADA telemetry Skid refurbishment.', 'TopSecret', 'projects', 'ENG', '5.4 MB', 'Approved', '7y', 'PRJ-2026-002', 'BaltNord Process Systems', 'EMP-1019', 'PRJ-2026-002', 'CUS-1002'),
('DOC-2026-005', 'INV-2026-002_Billing_Record.pdf', 'Official commercial VAT billing invoice and milestone certification for BaltNord Process Systems.', 'Confidential', 'invoices', 'FIN', '320 KB', 'Approved', '7y', 'PRJ-2026-002', 'BaltNord Process Systems', 'EMP-1005', 'PRJ-2026-002', 'CUS-1002'),
('DOC-2026-006', 'Employee_Onboarding_Procedure.pdf', 'Human resources standard operating procedure SOP-05 for employee enrollment and IT provisioning.', 'Confidential', 'hr', 'HRA', '1.4 MB', 'Approved', '5y', NULL, NULL, 'EMP-1002', NULL, NULL),
('DOC-2026-007', 'Employee_Access_Matrix.xlsx', 'Official enterprise RBAC role permission mapping across all 11 systems.', 'TopSecret', 'governance', 'GOV', '890 KB', 'Approved', '5y', NULL, NULL, 'EMP-1004', NULL, NULL),
('DOC-2026-008', 'Supplier_Evaluation_2026.pdf', 'Annual operational audit of key raw material and optical prism manufacturers.', 'Confidential', 'operations', 'OPS', '2.8 MB', 'Approved', '5y', NULL, NULL, 'EMP-1011', NULL, NULL),
('DOC-2026-009', 'Optical_Sensor_Product_Catalog.pdf', 'Full technical catalog with wavelength response, power draw and dimensional schematics.', 'Public', 'marketing', 'SAL', '8.5 MB', 'Approved', '3y', NULL, NULL, 'EMP-1006', NULL, NULL),
('DOC-2026-010', 'API_Integration_Guide.pdf', 'OpenAPI 3.1 technical specifications, authentication flows, and developer webhook guidelines.', 'Internal', 'developer', 'ITD', '3.1 MB', 'Approved', '3y', NULL, NULL, 'EMP-1003', NULL, NULL),
('DOC-2026-011', 'Disaster_Recovery_Plan.pdf', 'Business continuity plans, RTO/RPO metrics, and geo-redundant database failover procedures.', 'TopSecret', 'security', 'GOV', '2.5 MB', 'Approved', '10y', NULL, NULL, 'EMP-1004', NULL, NULL),
('DOC-2026-012', 'Annual_Corporate_Budget_2026.xlsx', 'Departmental operational expenditures, R&D equipment allocations, and revenue projections.', 'TopSecret', 'finance', 'FIN', '1.6 MB', 'Approved', '10y', NULL, NULL, 'EMP-1005', NULL, NULL),
('DOC-2026-013', 'Customer_Service_Handbook.pdf', 'Support SLA matrix, escalation workflows SOP-04, and service desk response guidelines.', 'Internal', 'support', 'ITD', '2.0 MB', 'Approved', '5y', NULL, NULL, 'EMP-1003', NULL, NULL),
('DOC-2026-014', 'PRJ-2026-007_Test_Report.pdf', 'Quality acceptance testing protocol and laser weld seam calibration data.', 'Confidential', 'projects', 'ENG', '3.7 MB', 'Approved', '7y', 'PRJ-2026-007', 'Caspian Industrial Robotics', 'EMP-1019', 'PRJ-2026-007', 'CUS-1007'),
('DOC-2026-015', 'Board_Risk_Register_2026.xlsx', 'Quarterly risk mitigation ledger, sanctions compliance, and geopolitical supply chain controls.', 'TopSecret', 'board', 'GOV', '1.1 MB', 'Approved', '10y', NULL, NULL, 'EMP-1004', NULL, NULL)
ON DUPLICATE KEY UPDATE 
    file_name = VALUES(file_name),
    description = VALUES(description),
    classification = VALUES(classification),
    status = VALUES(status);

-- 8. PRODUCTS (10 Canonical Products: PROD-1001 .. PROD-1010)
INSERT INTO products (prod_id, product_name, billing_model, description, price) VALUES
('PROD-1001', 'Industrial Optical Sensor Package', 'PerUnit', 'Multi-spectral 4K CMOS industrial inspection sensor with sapphire window, Modbus RTU / 4-20mA loop.', 12500.00),
('PROD-1002', 'Precision Geodetic Measurement Kit', 'PerUnit', 'Sub-millimeter GNSS-RTK baseline receiver with dual constellation tracking and IP68 enclosure.', 3500.00),
('PROD-1003', 'Automated Calibration Station', 'PerProject', 'Turnkey multi-axis automated pressure and thermal calibration bench with ISO 17025 certificate generator.', 3500.00),
('PROD-1004', 'Industrial PLC Integration', 'PerProject', 'Fail-safe SIL-3 PLC telemetry integration skid with redundant Profinet and optical bypass.', 3500.00),
('PROD-1005', 'Remote Monitoring Gateway', 'PerUnit', 'Edge computing telemetry hub with 4G/LTE failover, MQTT broker, and local SQLite buffer.', 3500.00),
('PROD-1006', 'High-Temp Thermal Pyrometer', 'PerUnit', 'Non-contact infrared optical pyrometer for blast furnaces up to 1,800C with air-purge collar.', 3500.00),
('PROD-1007', 'Vibration Analysis Sensor Skid', 'PerUnit', 'Tri-axial piezoelectric accelerometer package for turbine bearings with FFT vibration analytics.', 3500.00),
('PROD-1008', 'Electromagnetic Flowmeter Array', 'PerUnit', 'High-accuracy conductive fluid flowmeter with Hastelloy electrodes and digital pulse output.', 3500.00),
('PROD-1009', 'Differential Pressure Transmitter', 'PerUnit', 'HART protocol differential pressure cell for orifice gas flow measurement with 0.05% accuracy.', 3500.00),
('PROD-1010', 'SCADA Historian & Analytics Suite', 'AnnualContract', 'Enterprise operational data historian license with OPC-UA integration and predictive telemetry.', 3500.00)
ON DUPLICATE KEY UPDATE 
    product_name = VALUES(product_name),
    billing_model = VALUES(billing_model),
    description = VALUES(description),
    price = VALUES(price);

-- 9. ID COUNTERS
INSERT INTO id_counters (name, next_val) VALUES
('customers', 1011),
('projects', 16),
('invoices', 11),
('documents', 16),
('tickets', 16),
('orders', 1010),
('employees', 1096)
ON DUPLICATE KEY UPDATE next_val = VALUES(next_val);

SET FOREIGN_KEY_CHECKS = 1;
