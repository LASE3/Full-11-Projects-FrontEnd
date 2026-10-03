<?php
// Generates DataBase/seed_baseline.sql and DataBase/test_fixtures.sql

$defaultPassHash = password_hash('VostokPribor2026!', PASSWORD_BCRYPT);
$clientPassHash  = password_hash('ClientAccess2026!', PASSWORD_BCRYPT);

// ----------------------------------------------------------------------------
// 1. DEPARTMENTS
// ----------------------------------------------------------------------------
$departments = [
    ['EXE', 'Executive Leadership', 'Corporate strategy, executive oversight, and governance', 5],
    ['SAL', 'Sales & Enterprise Relations', 'Global business development, CRM pipeline, and enterprise accounts', 16],
    ['OPS', 'Operations & Logistics', 'Supply chain, procurement, fulfilment, and logistics operations', 20],
    ['ENG', 'Engineering & Design', 'Product development, systems architecture, and technical integration', 16],
    ['FIN', 'Finance & Billing', 'Financial accounting, commercial billing, audit, and cashflow control', 10],
    ['HRA', 'Human Resources & Admin', 'Workforce administration, recruitment, onboarding, and compliance', 8],
    ['ITD', 'Information Technology & Security', 'IT infrastructure, helpdesk support, telemetry, and cybersecurity', 14],
    ['GOV', 'Governance, Risk & Compliance', 'Enterprise risk management, regulatory compliance, and security oversight', 6]
];

// ----------------------------------------------------------------------------
// 2. EMPLOYEES (95 total: EMP-1001 .. EMP-1095)
// ----------------------------------------------------------------------------
$employees = [
    // EXE (5)
    ['EMP-1001', 'Viktor Sokolov', 'Chief Executive Officer', 'EXE', 'L4', 'viktor.sokolov@vostokpribor.local', null, 'Active', '2026-01-01'],
    ['EMP-1002', 'Amina Karimova', 'Chief Operating Officer', 'EXE', 'L4', 'amina.karimova@vostokpribor.local', 'EMP-1001', 'Active', '2026-01-01'],
    ['EMP-1003', 'Daniel Weber', 'Chief Technology Officer', 'EXE', 'L4', 'daniel.weber@vostokpribor.local', 'EMP-1001', 'Active', '2026-01-01'],
    ['EMP-1004', 'Elena Morozova', 'Chief Information Security Officer', 'EXE', 'L4', 'elena.morozova@vostokpribor.local', 'EMP-1001', 'Active', '2026-01-01'],
    ['EMP-1005', 'Timur Akhmetov', 'Chief Financial Officer', 'EXE', 'L4', 'timur.akhmetov@vostokpribor.local', 'EMP-1001', 'Active', '2026-01-01'],

    // SAL (16: 1006..1010 + 1022..1032)
    ['EMP-1006', 'Pavel Orlov', 'VP Enterprise Sales', 'SAL', 'L3', 'pavel.orlov@vostokpribor.local', 'EMP-1001', 'Active', '2026-01-05'],
    ['EMP-1007', 'Sara Lindholm', 'Senior Key Account Manager', 'SAL', 'L3', 'sara.lindholm@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-08'],
    ['EMP-1008', 'Bekzod Rakhimov', 'Lead Technical Sales Engineer', 'SAL', 'L3', 'bekzod.rakhimov@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-10'],
    ['EMP-1009', 'Nadia Petrova', 'Senior Account Executive', 'SAL', 'L3', 'nadia.petrova@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-12'],
    ['EMP-1010', 'Markus Klein', 'Strategic Account Director', 'SAL', 'L3', 'markus.klein@vostokpribor.local', 'EMP-1006', 'Active', '2026-01-15'],

    // OPS (20: 1011..1015 + 1033..1047)
    ['EMP-1011', 'Arman Tulegenov', 'Director of Operations', 'OPS', 'L3', 'arman.tulegenov@vostokpribor.local', 'EMP-1002', 'Active', '2026-01-05'],
    ['EMP-1012', 'Rustam Bekov', 'Lead Procurement & Logistics Specialist', 'OPS', 'L3', 'rustam.bekov@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-08'],
    ['EMP-1013', 'Ilona Vetra', 'Senior Fulfilment & Supply Coordinator', 'OPS', 'L3', 'ilona.vetra@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-10'],
    ['EMP-1014', 'Mikhail Antonov', 'Warehouse Operations Supervisor', 'OPS', 'L2', 'mikhail.antonov@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-15'],
    ['EMP-1015', 'Kamila Nurzhan', 'Logistics Dispatch Specialist', 'OPS', 'L2', 'kamila.nurzhan@vostokpribor.local', 'EMP-1011', 'Active', '2026-01-20'],

    // ENG (16: 1016..1020 + 1048..1058)
    ['EMP-1016', 'Erik Hansen', 'Principal Systems Architect', 'ENG', 'L3', 'erik.hansen@vostokpribor.local', 'EMP-1003', 'Active', '2026-01-05'],
    ['EMP-1017', 'Dana Yermak', 'Senior Automation Engineer', 'ENG', 'L3', 'dana.yermak@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-08'],
    ['EMP-1018', 'Leonid Volkov', 'Lead Hardware Integration Engineer', 'ENG', 'L3', 'leonid.volkov@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-10'],
    ['EMP-1019', 'Farida Iskakova', 'Senior SCADA Implementation Lead', 'ENG', 'L3', 'farida.iskakova@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-12'],
    ['EMP-1020', 'Jonas Richter', 'Firmware & Embedded Systems Engineer', 'ENG', 'L3', 'jonas.richter@vostokpribor.local', 'EMP-1016', 'Active', '2026-01-15'],

    // ITD (14: 1021 + 1077..1089)
    ['EMP-1021', 'Ahmad AbuNijim', 'IT Systems Administrator', 'ITD', 'L2', 'ahmad.abunijim@vostokpribor.local', 'EMP-1003', 'Active', '2026-01-20']
];

// Remaining SAL (11 employees: EMP-1022 .. EMP-1032)
$salNames = [
    ['Dmitry Kozlov', 'Enterprise Account Executive'],
    ['Anna Semyonova', 'Key Account Manager'],
    ['Igor Tarasov', 'B2B Sales Specialist'],
    ['Olga Romanova', 'Client Relationship Executive'],
    ['Maxim Belyayev', 'Regional Sales Representative'],
    ['Ekaterina Novikova', 'Commercial Contract Manager'],
    ['Andrei Morozov', 'Sales Operations Analyst'],
    ['Yulia Volkova', 'Export Sales Specialist'],
    ['Konstantin Lebedev', 'Technical Account Manager'],
    ['Marina Pavlova', 'Inside Sales Representative'],
    ['Sergey Fedorov', 'Strategic Partnership Lead']
];
$idx = 1022;
foreach ($salNames as $s) {
    $mail = strtolower(substr($s[0], 0, 1) . '.' . explode(' ', $s[0])[1]) . '@vostokpribor.local';
    $employees[] = ["EMP-{$idx}", $s[0], $s[1], 'SAL', 'L2', $mail, 'EMP-1006', 'Active', '2026-01-20'];
    $idx++;
}

// Remaining OPS (15 employees: EMP-1033 .. EMP-1047)
$opsNames = [
    ['Roman Vasilyev', 'Supply Chain Analyst'],
    ['Natalia Zakharova', 'Procurement Specialist'],
    ['Alexey Kuznetsov', 'Inventory Control Supervisor'],
    ['Tatyana Grigoryeva', 'Logistics Operations Lead'],
    ['Denis Melnikov', 'Warehouse Logistics Specialist'],
    ['Svetlana Borisova', 'Fulfilment Dispatch Coordinator'],
    ['Valery Stepanov', 'Shipping & Receiving Inspector'],
    ['Lyudmila Alexandrova', 'Supply Quality Specialist'],
    ['Grigory Danilov', 'Materials Management Planner'],
    ['Polina Yakovleva', 'B2B Order Expeditor'],
    ['Vladislav Sorokin', 'Equipment Packaging Engineer'],
    ['Ksenia Vorobyeva', 'Procurement Auditor'],
    ['Stanislav Solovyov', 'Logistics Fleet Dispatcher'],
    ['Daria Guseva', 'Customs Clearance Specialist'],
    ['Kirill Titov', 'Inbound Logistics Controller']
];
$idx = 1033;
foreach ($opsNames as $o) {
    $mail = strtolower(substr($o[0], 0, 1) . '.' . explode(' ', $o[0])[1]) . '@vostokpribor.local';
    $employees[] = ["EMP-{$idx}", $o[0], $o[1], 'OPS', 'L2', $mail, 'EMP-1011', 'Active', '2026-01-20'];
    $idx++;
}

// Remaining ENG (11 employees: EMP-1048 .. EMP-1058)
$engNames = [
    ['Mikhail Zaytsev', 'Senior Optical Systems Designer'],
    ['Alina Bogdanova', 'Hardware Test Engineer'],
    ['Yaroslav Belov', 'Firmware Validation Specialist'],
    ['Evgenia Maksimova', 'SCADA Integration Specialist'],
    ['Artyom Kudryavtsev', 'Precision Calibration Engineer'],
    ['Victoria Chernova', 'Embedded Linux Developer'],
    ['Nikita Panov', 'Industrial Telemetry Engineer'],
    ['Larisa Timofeeva', 'Quality Assurance Test Lead'],
    ['Anton Denisov', 'Mechatronics Integration Engineer'],
    ['Veronika Savelyeva', 'Optical Sensor QA Analyst'],
    ['Stepan Matveev', 'Industrial Protocols Specialist']
];
$idx = 1048;
foreach ($engNames as $e) {
    $mail = strtolower(substr($e[0], 0, 1) . '.' . explode(' ', $e[0])[1]) . '@vostokpribor.local';
    $employees[] = ["EMP-{$idx}", $e[0], $e[1], 'ENG', 'L3', $mail, 'EMP-1016', 'Active', '2026-01-20'];
    $idx++;
}

// FIN (10 employees: EMP-1059 .. EMP-1068)
$finNames = [
    ['Boris Filatov', 'Finance Director & Controller'],
    ['Inga Vlasova', 'Senior Financial Accountant'],
    ['Gennady Maslov', 'B2B Billing Operations Lead'],
    ['Nadezhda Isayeva', 'Accounts Payable Specialist'],
    ['Semen Bobrov', 'Accounts Receivable Controller'],
    ['Vera Zhukova', 'Corporate Tax & Compliance Accountant'],
    ['Oleg Bykov', 'Treasury & Cashflow Analyst'],
    ['Alla Tretyakova', 'Project Cost Accounting Analyst'],
    ['Fedor Nikitin', 'ERP Financial Data Reconciler'],
    ['Tamara Kulikova', 'Senior Invoicing Auditor']
];
$idx = 1059;
foreach ($finNames as $f) {
    $clearance = ($idx === 1059) ? 'L3' : 'L2';
    $mail = strtolower(substr($f[0], 0, 1) . '.' . explode(' ', $f[0])[1]) . '@vostokpribor.local';
    $employees[] = ["EMP-{$idx}", $f[0], $f[1], 'FIN', $clearance, $mail, 'EMP-1005', 'Active', '2026-01-20'];
    $idx++;
}

// HRA (8 employees: EMP-1069 .. EMP-1076)
$hraNames = [
    ['Ksenia Lavrova', 'Director of Human Resources'],
    ['Matvey Rodionov', 'Senior HR Operations Specialist'],
    ['Zhanna Simonova', 'Talent Acquisition & Recruiting Lead'],
    ['Yury Medvedev', 'Employee Relations Coordinator'],
    ['Diana Antonova', 'Workforce Training & Compliance Officer'],
    ['Leonid Fomichev', 'HRIS Systems Administrator'],
    ['Kristina Markova', 'Personnel Dossier & Onboarding Specialist'],
    ['Ilya Gavrilov', 'Benefits & Payroll Specialist']
];
$idx = 1069;
foreach ($hraNames as $h) {
    $clearance = ($idx === 1069) ? 'L3' : 'L2';
    $mail = strtolower(substr($h[0], 0, 1) . '.' . explode(' ', $h[0])[1]) . '@vostokpribor.local';
    $employees[] = ["EMP-{$idx}", $h[0], $h[1], 'HRA', $clearance, $mail, 'EMP-1002', 'Active', '2026-01-20'];
    $idx++;
}

// ITD (13 more employees: EMP-1077 .. EMP-1089)
$itdNames = [
    ['Alexander Krylov', 'IT Infrastructure & Cloud Architect'],
    ['Regina Karimova', 'Senior Cybersecurity Analyst'],
    ['Vadim Dorofeev', 'DevOps & CI/CD Systems Engineer'],
    ['Yana Blinova', 'IT Helpdesk Team Lead'],
    ['Ruslan Kasimov', 'Network Operations Specialist'],
    ['Elizaveta Shirokova', 'Identity & Access Management Engineer'],
    ['Arthur Davydov', 'Database Administrator'],
    ['Kira Samsonova', 'Hardware Asset Provisioning Specialist'],
    ['Timofey Kazakov', 'IT Service Desk Specialist'],
    ['Snezhana Konovalova', 'Telemetry & SCADA Gateway Administrator'],
    ['Albert Zakirov', 'Systems Support Technician'],
    ['Maya Chernyakhovskaya', 'IT Security Operations Analyst'],
    ['Eduard Potapov', 'Storage Appliance & Backup Administrator']
];
$idx = 1077;
foreach ($itdNames as $it) {
    $clearance = ($idx <= 1079) ? 'L3' : 'L2';
    $mail = strtolower(substr($it[0], 0, 1) . '.' . explode(' ', $it[0])[1]) . '@vostokpribor.local';
    $employees[] = ["EMP-{$idx}", $it[0], $it[1], 'ITD', $clearance, $mail, 'EMP-1003', 'Active', '2026-01-20'];
    $idx++;
}

// GOV (6 employees: EMP-1090 .. EMP-1095)
$govNames = [
    ['Vyacheslav Gromov', 'Director of Governance & Compliance'],
    ['Roza Akhmetova', 'Senior Internal Security Auditor'],
    ['Lev Krasnov', 'Enterprise Risk Assessment Specialist'],
    ['Zoya Pakhomova', 'Regulatory Affairs Officer'],
    ['Gleb Arkhipov', 'Access Governance Analyst'],
    ['Milana Shcherbakova', 'Policy & Legal Compliance Lead']
];
$idx = 1090;
foreach ($govNames as $g) {
    $clearance = ($idx === 1090) ? 'L4' : 'L3';
    $mail = strtolower(substr($g[0], 0, 1) . '.' . explode(' ', $g[0])[1]) . '@vostokpribor.local';
    $employees[] = ["EMP-{$idx}", $g[0], $g[1], 'GOV', $clearance, $mail, 'EMP-1004', 'Active', '2026-01-20'];
    $idx++;
}

// ----------------------------------------------------------------------------
// 3. CUSTOMERS (Exact 10: CUS-1001 .. CUS-1010)
// ----------------------------------------------------------------------------
$customers = [
    ['CUS-1001', 'Aral Geomatics Group', 'geomatics', 'Sergei Makarov', 's.makarov@aral-geomatics.local', 'EMP-1007', '+7 727 344-9001', 'Almaty, Kazakhstan', 'KZ-BIN-990140001', 98, 'Enterprise'],
    ['CUS-1002', 'BaltNord Process Systems', 'industrial automation', 'Kristaps Ozols', 'k.ozols@baltnord.local', 'EMP-1010', '+371 67 890 123', 'Riga, Latvia', 'LV-VAT-400030129', 95, 'Enterprise'],
    ['CUS-1003', 'Steppe Mining Technologies', 'mining', 'Yerlan Bektemis', 'y.bektemis@steppemining.local', 'EMP-1008', '+7 721 250-8800', 'Karaganda, Kazakhstan', 'KZ-BIN-040240008', 92, 'Tier-1 Partner'],
    ['CUS-1004', 'RheinWerk Instrumentation', 'industrial measurement', 'Lukas Brandt', 'l.brandt@rheinwerk.local', 'EMP-1010', '+49 211 556-7800', 'Düsseldorf, Germany', 'DE-HRB-890123', 97, 'Enterprise'],
    ['CUS-1005', 'Tashkent Precision Controls', 'manufacturing', 'Dilshod Karim', 'd.karim@tashkent-pc.local', 'EMP-1008', '+998 71 238-9900', 'Tashkent, Uzbekistan', 'UZ-INN-201889012', 90, 'Enterprise'],
    ['CUS-1006', 'Daugava Optical Research', 'optical engineering', 'Mara Kalnina', 'm.kalnina@daugava-optical.local', 'EMP-1007', '+371 63 456-789', 'Daugavpils, Latvia', 'LV-VAT-401020304', 94, 'Research Institute'],
    ['CUS-1007', 'Caspian Industrial Robotics', 'robotics', 'Murad Safarov', 'm.safarov@caspian-robotics.local', 'EMP-1009', '+994 12 490-5500', 'Baku, Azerbaijan', 'AZ-VOEN-14008901', 96, 'Enterprise'],
    ['CUS-1008', 'Eurasia Water Automation', 'water infrastructure', 'Oleg Petrenko', 'o.petrenko@eurasia-water.local', 'EMP-1009', '+380 44 290-7700', 'Kyiv, Ukraine', 'UA-EDRPOU-3890124', 91, 'Municipal Partner'],
    ['CUS-1009', 'Altai Environmental Systems', 'environmental monitoring', 'Ainur Sadyk', 'a.sadyk@altai-env.local', 'EMP-1008', '+7 723 270-3300', 'Ust-Kamenogorsk, Kazakhstan', 'KZ-BIN-080340015', 93, 'Government Entity'],
    ['CUS-1010', 'CentralRail Diagnostics', 'rail infrastructure', 'Tomas Varga', 't.varga@centralrail.local', 'EMP-1006', '+36 1 480-2200', 'Budapest, Hungary', 'HU-ADOSZ-12345678', 99, 'Strategic Infrastructure']
];

// ----------------------------------------------------------------------------
// 4. PROJECTS (Exact 15: PRJ-2026-001 .. PRJ-2026-015)
// ----------------------------------------------------------------------------
$projects = [
    ['PRJ-2026-001', 'Aral Geomatics Laser Telemetry Array', 'CUS-1001', 'EMP-1019', 185000.00, 'EUR', 'Execution', '2026-01-10', '2026-08-30', 65, 'Balkhash Observation Station', 'Automated geodetic and atmospheric lidar monitoring network'],
    ['PRJ-2026-002', 'BaltNord SCADA Refurbishment Skid', 'CUS-1002', 'EMP-1019', 240000.00, 'EUR', 'Integration', '2026-02-01', '2026-09-15', 50, 'Riga Terminal Terminal 3', 'High-speed redundant optical bus and field telemetry modernization'],
    ['PRJ-2026-003', 'Steppe Mining Deep Shaft Telemetry', 'CUS-1003', 'EMP-1016', 410000.00, 'EUR', 'Procurement', '2026-02-15', '2026-11-30', 25, 'Karaganda Pit Mine #4', 'Explosion-proof hazardous environment gas and strain monitoring sensors'],
    ['PRJ-2026-004', 'RheinWerk Metrology Cleanroom Automation', 'CUS-1004', 'EMP-1019', 165000.00, 'EUR', 'Execution', '2026-01-20', '2026-07-31', 75, 'Düsseldorf Metrology Hall', 'Sub-micron automated optical inspection and thermal chamber alignment'],
    ['PRJ-2026-005', 'Tashkent Plant Chemical Dosing Controller', 'CUS-1005', 'EMP-1017', 128000.00, 'EUR', 'Integration', '2026-03-01', '2026-10-15', 40, 'Chirchik Industrial Zone', 'Digital closed-loop PID control and hazardous fluid flow telemetry'],
    ['PRJ-2026-006', 'Daugava Laser Interferometry Bench', 'CUS-1006', 'EMP-1016', 96000.00, 'EUR', 'Design', '2026-03-10', '2026-09-30', 20, 'Daugavpils Laser Lab', 'Vibration-isolated precision optical measurement bench with photon counter'],
    ['PRJ-2026-007', 'Caspian Welding Robot Vision Guiding', 'CUS-1007', 'EMP-1019', 315000.00, 'EUR', 'Integration', '2026-01-15', '2026-08-15', 55, 'Baku Shipyard Bay 2', 'Real-time 3D laser seam tracking and adaptive weld robotic guidance'],
    ['PRJ-2026-008', 'Eurasia Municipal Pumping Station Grid', 'CUS-1008', 'EMP-1017', 205000.00, 'EUR', 'Execution', '2026-02-10', '2026-10-31', 60, 'Dnipro Water Intake Facility', 'Telemetry gateway cluster with cellular failover and automated pressure regulation'],
    ['PRJ-2026-009', 'Altai Basin Eco-Telemetry Station Array', 'CUS-1009', 'EMP-1016', 142000.00, 'EUR', 'Testing', '2026-01-25', '2026-07-15', 80, 'Katun River Basin Station', 'Autonomous solar-powered hydrological station network with satellite uplink'],
    ['PRJ-2026-010', 'CentralRail High-Speed Track Geometry', 'CUS-1010', 'EMP-1019', 275000.00, 'EUR', 'Procurement', '2026-03-05', '2026-12-15', 15, 'Budapest Keleti Test Track', 'Dynamic optical rail profile scanner and acceleration vibration analyzer'],
    ['PRJ-2026-011', 'Aral Geomatics Base Station Maintenance', 'CUS-1001', 'EMP-1017', 74000.00, 'EUR', 'Maintenance', '2026-01-01', '2026-12-31', 45, 'Aralsk Calibration Field', 'Scheduled annual calibration and firmware maintenance contract'],
    ['PRJ-2026-012', 'BaltNord Line 2 Vision Upgrade', 'CUS-1002', 'EMP-1016', 188000.00, 'EUR', 'Design', '2026-03-15', '2026-11-15', 10, 'Jelgava Packaging Plant', 'High-speed multi-camera bottle inspection with defect rejection'],
    ['PRJ-2026-013', 'Tashkent SCADA Security Hardening', 'CUS-1005', 'EMP-1019', 112000.00, 'EUR', 'ContractReview', '2026-04-01', '2026-11-30', 5, 'Tashkent Plant B', 'OT network segmentation, unidirectional gateway and anomaly detection'],
    ['PRJ-2026-014', 'Caspian Autonomous Subsea Crawler', 'CUS-1007', 'EMP-1017', 260000.00, 'EUR', 'Procurement', '2026-03-20', '2027-01-31', 10, 'Sangachal Offshore Base', 'Subsea inspection crawler with dual acoustic telemetry and HD cameras'],
    ['PRJ-2026-015', 'CentralRail Depot Wheel Profiler', 'CUS-1010', 'EMP-1016', 151000.00, 'EUR', 'Planning', '2026-04-15', '2026-12-31', 0, 'Debrecen Maintenance Yard', 'In-track laser wheel geometry inspection skid with automated reporting']
];

// ----------------------------------------------------------------------------
// 5. INVOICES (Exact 10: INV-2026-001 .. INV-2026-010)
// ----------------------------------------------------------------------------
$invoices = [
    ['INV-2026-001', 'CUS-1001', 'PRJ-2026-001', 'EMP-1005', 46250.00, 'EUR', 'Paid', '2026-01-15', '2026-02-14', 'Net-30', 'Aral Geomatics Laser Array - Milestone 1 Sign-off', '2026-02-10'],
    ['INV-2026-002', 'CUS-1002', 'PRJ-2026-002', 'EMP-1005', 80000.00, 'EUR', 'Pending', '2026-02-15', '2026-03-17', 'Net-30', 'BaltNord SCADA Refurbishment - Hardware Advance', null],
    ['INV-2026-003', 'CUS-1003', 'PRJ-2026-003', 'EMP-1005', 137500.00, 'EUR', 'Paid', '2026-02-28', '2026-03-30', 'Net-30', 'Steppe Mining Deep Shaft - Sensor Procurement Tranche', '2026-03-25'],
    ['INV-2026-004', 'CUS-1004', 'PRJ-2026-004', 'EMP-1005', 55000.00, 'EUR', 'Pending', '2026-03-01', '2026-03-31', 'Net-30', 'RheinWerk Metrology - Cleanroom Bench Integration Phase 1', null],
    ['INV-2026-005', 'CUS-1005', 'PRJ-2026-005', 'EMP-1005', 42000.00, 'EUR', 'Paid', '2026-03-10', '2026-04-09', 'Net-30', 'Tashkent Plant Chemical Dosing - Engineering Design Final', '2026-04-05'],
    ['INV-2026-006', 'CUS-1006', 'PRJ-2026-006', 'EMP-1005', 32000.00, 'EUR', 'Pending', '2026-03-15', '2026-04-14', 'Net-30', 'Daugava Laser Interferometry - Prototype Fabrication', null],
    ['INV-2026-007', 'CUS-1007', 'PRJ-2026-007', 'EMP-1005', 105000.00, 'EUR', 'Paid', '2026-01-30', '2026-03-01', 'Net-30', 'Caspian Welding Robot - Vision Head Commissioning', '2026-02-28'],
    ['INV-2026-008', 'CUS-1008', 'PRJ-2026-008', 'EMP-1005', 68333.00, 'EUR', 'Pending', '2026-02-20', '2026-03-22', 'Net-30', 'Eurasia Water Pumping Grid - Phase 1 Telemetry Deployment', null],
    ['INV-2026-009', 'CUS-1009', 'PRJ-2026-009', 'EMP-1005', 47333.00, 'EUR', 'Paid', '2026-02-10', '2026-03-12', 'Net-30', 'Altai Basin Eco-Telemetry - Sensor Calibration Package', '2026-03-08'],
    ['INV-2026-010', 'CUS-1010', 'PRJ-2026-010', 'EMP-1005', 91667.00, 'EUR', 'Pending', '2026-03-12', '2026-04-11', 'Net-30', 'CentralRail Track Geometry - Laser Scanner Advance Payment', null]
];

// ----------------------------------------------------------------------------
// 6. TICKETS (Exact 15: TKT-2026-001 .. TKT-2026-015)
// ----------------------------------------------------------------------------
$tickets = [
    ['TKT-2026-001', 'Customer', 'CUS-1001', null, 'CUS', 'Customer Portal B2B API Latency Degraded', 'API gateway latency spiked to 1,200ms on invoice document retrieval endpoint.', 'Sergei Makarov', 'Procurement Director', 'Procurement', 'High', 'EMP-1021', 'InProgress'],
    ['TKT-2026-002', 'Employee', null, 'EMP-1006', 'CRM', 'CRM Contact Synchronization Timeout on Russian Railways Sync', 'Automated nightly sync timed out after 300s waiting for partner endpoint.', 'Pavel Orlov', 'VP Enterprise Sales', 'Sales', 'Medium', 'EMP-1018', 'Resolved'],
    ['TKT-2026-003', 'Customer', 'CUS-1002', null, 'SHP', 'B2B Shop Checkout Cart Lock during Batch Invoicing', 'Simultaneous checkout of 40 optical units triggered deadlock in orders table.', 'Kristaps Ozols', 'Lead Automation Architect', 'Engineering', 'High', 'EMP-1017', 'Investigating'],
    ['TKT-2026-004', 'Employee', null, 'EMP-1007', 'EMP', 'Corporate Intranet Knowledge Base Search Index Rebuild Required', 'Elastic index out of sync following SOP revision uploads.', 'Sara Lindholm', 'Key Account Manager', 'Sales', 'Low', 'EMP-1021', 'Resolved'],
    ['TKT-2026-005', 'Customer', 'CUS-1003', null, 'CUS', 'Customer Portal TLS Handshake Error on Gateway 02', 'Intermittent TLS 1.3 handshake resets reported from Karaganda access proxy.', 'Yerlan Bektemis', 'Chief Mining Engineer', 'Operations', 'Critical', 'EMP-1004', 'Escalated'],
    ['TKT-2026-006', 'Employee', null, 'EMP-1016', 'DEV', 'API Gateway OAuth2 Token Revocation Endpoint Intermittent 502', 'FastCGI buffer exhausted during bulk token revocation test run.', 'Erik Hansen', 'Principal Systems Architect', 'Engineering', 'Medium', 'EMP-1018', 'Resolved'],
    ['TKT-2026-007', 'Employee', null, 'EMP-1005', 'FIN', 'ERP 1C Billing Data Reconciliation Discrepancy Q1', 'Discrepancy of 1,420 EUR between ERP invoice register and bank statement.', 'Timur Akhmetov', 'Chief Financial Officer', 'Finance', 'Medium', 'EMP-1005', 'Resolved'],
    ['TKT-2026-008', 'Customer', 'CUS-1004', null, 'SHP', 'Shop Catalog Price Cache Invalidation Delay', 'Discounted contract pricing for RheinWerk failed to reflect immediately.', 'Lukas Brandt', 'Managing Director', 'Management', 'Medium', 'EMP-1017', 'InProgress'],
    ['TKT-2026-009', 'Employee', null, 'EMP-1002', 'EMP', 'Single Sign-On Session Timeout Too Short on Mobile Intranet', 'Engineers in cleanroom disconnected every 15 minutes while entering inspection logs.', 'Amina Karimova', 'Chief Operating Officer', 'Operations', 'Low', 'EMP-1021', 'Resolved'],
    ['TKT-2026-010', 'Employee', null, 'EMP-1008', 'CRM', 'Lead Routing Rule Failover for Export Contracts', 'Export leads from DACH region not automatically routing to Markus Klein queue.', 'Bekzod Rakhimov', 'Lead Technical Sales', 'Sales', 'High', 'EMP-1006', 'Investigating'],
    ['TKT-2026-011', 'Employee', null, 'EMP-1004', 'ADM', 'Admin Portal Audit Log Archive Retention Rule Execution', 'Automated 1-year archive script paused due to cold storage mount timeout.', 'Elena Morozova', 'CISO', 'Governance', 'High', 'EMP-1004', 'Escalated'],
    ['TKT-2026-012', 'Employee', null, 'EMP-1003', 'IT', 'Backup Storage Appliance LUN Snapshot Pool Warning (82% full)', 'Secondary ZFS pool reaching warning threshold after quarterly image dump.', 'Daniel Weber', 'Chief Technology Officer', 'IT', 'Medium', 'EMP-1018', 'Resolved'],
    ['TKT-2026-013', 'Employee', null, 'EMP-1002', 'HR', 'Employee Onboarding Workflow Automated Provisioning Stuck', 'LDAP group creation step failed on trailing whitespace in department code.', 'Amina Karimova', 'Chief Operating Officer', 'Operations', 'Low', 'EMP-1021', 'Resolved'],
    ['TKT-2026-014', 'Customer', 'CUS-1005', null, 'FIN', 'Invoice PDF Generator Font Rendering Exception', 'Cyrillic characters in Tashkent subsidiary address appearing as question marks in PDF.', 'Dilshod Karim', 'Head of Procurement', 'Procurement', 'Medium', 'EMP-1017', 'InProgress'],
    ['TKT-2026-015', 'Customer', 'CUS-1007', null, 'SHP', 'Customer Order Status Tracking Webhook Delivery Failure', 'Order dispatch webhook returned HTTP 403 on client ingress proxy.', 'Murad Safarov', 'Robotics Systems Director', 'Engineering', 'High', 'EMP-1018', 'Investigating']
];

// ----------------------------------------------------------------------------
// 7. DOCUMENTS (Exact 15: DOC-2026-001 .. DOC-2026-015)
// ----------------------------------------------------------------------------
$documents = [
    ['DOC-2026-001', 'Corporate_Information_Security_Policy.pdf', 'Enterprise cybersecurity policies, data classification guidelines, and incident response requirements.', 'TopSecret', 'policies', 'GOV', '1.8 MB', 'Approved', '10y', null, null, 'EMP-1004', null, null],
    ['DOC-2026-002', 'Customer_Onboarding_Standard.pdf', 'Standard operating procedure SOP-01 for lead conversion, KYC, customer account provisioning and workspace setup.', 'Confidential', 'sop', 'SAL', '2.2 MB', 'Approved', '7y', null, null, 'EMP-1006', null, null],
    ['DOC-2026-003', 'PRJ-2026-001_Statement_of_Work.pdf', 'Technical specification and statement of work for Aral Geomatics Laser Telemetry Array integration.', 'Confidential', 'projects', 'ENG', '4.1 MB', 'Approved', '7y', 'PRJ-2026-001', 'Aral Geomatics Group', 'EMP-1019', 'PRJ-2026-001', 'CUS-1001'],
    ['DOC-2026-004', 'PRJ-2026-002_Integration_Specification.pdf', 'Engineering blueprint for BaltNord Process Systems SCADA telemetry Skid refurbishment.', 'TopSecret', 'projects', 'ENG', '5.4 MB', 'Approved', '7y', 'PRJ-2026-002', 'BaltNord Process Systems', 'EMP-1019', 'PRJ-2026-002', 'CUS-1002'],
    ['DOC-2026-005', 'INV-2026-002_Billing_Record.pdf', 'Official commercial VAT billing invoice and milestone certification for BaltNord Process Systems.', 'Confidential', 'invoices', 'FIN', '320 KB', 'Approved', '7y', 'PRJ-2026-002', 'BaltNord Process Systems', 'EMP-1005', 'PRJ-2026-002', 'CUS-1002'],
    ['DOC-2026-006', 'Employee_Onboarding_Procedure.pdf', 'Human resources standard operating procedure SOP-05 for employee enrollment and IT provisioning.', 'Confidential', 'hr', 'HRA', '1.4 MB', 'Approved', '5y', null, null, 'EMP-1002', null, null],
    ['DOC-2026-007', 'Employee_Access_Matrix.xlsx', 'Official enterprise RBAC role permission mapping across all 11 systems.', 'TopSecret', 'governance', 'GOV', '890 KB', 'Approved', '5y', null, null, 'EMP-1004', null, null],
    ['DOC-2026-008', 'Supplier_Evaluation_2026.pdf', 'Annual operational audit of key raw material and optical prism manufacturers.', 'Confidential', 'operations', 'OPS', '2.8 MB', 'Approved', '5y', null, null, 'EMP-1011', null, null],
    ['DOC-2026-009', 'Optical_Sensor_Product_Catalog.pdf', 'Full technical catalog with wavelength response, power draw and dimensional schematics.', 'Public', 'marketing', 'SAL', '8.5 MB', 'Approved', '3y', null, null, 'EMP-1006', null, null],
    ['DOC-2026-010', 'API_Integration_Guide.pdf', 'OpenAPI 3.1 technical specifications, authentication flows, and developer webhook guidelines.', 'Internal', 'developer', 'ITD', '3.1 MB', 'Approved', '3y', null, null, 'EMP-1003', null, null],
    ['DOC-2026-011', 'Disaster_Recovery_Plan.pdf', 'Business continuity plans, RTO/RPO metrics, and geo-redundant database failover procedures.', 'TopSecret', 'security', 'GOV', '2.5 MB', 'Approved', '10y', null, null, 'EMP-1004', null, null],
    ['DOC-2026-012', 'Annual_Corporate_Budget_2026.xlsx', 'Departmental operational expenditures, R&D equipment allocations, and revenue projections.', 'TopSecret', 'finance', 'FIN', '1.6 MB', 'Approved', '10y', null, null, 'EMP-1005', null, null],
    ['DOC-2026-013', 'Customer_Service_Handbook.pdf', 'Support SLA matrix, escalation workflows SOP-04, and service desk response guidelines.', 'Internal', 'support', 'ITD', '2.0 MB', 'Approved', '5y', null, null, 'EMP-1003', null, null],
    ['DOC-2026-014', 'PRJ-2026-007_Test_Report.pdf', 'Quality acceptance testing protocol and laser weld seam calibration data.', 'Confidential', 'projects', 'ENG', '3.7 MB', 'Approved', '7y', 'PRJ-2026-007', 'Caspian Industrial Robotics', 'EMP-1019', 'PRJ-2026-007', 'CUS-1007'],
    ['DOC-2026-015', 'Board_Risk_Register_2026.xlsx', 'Quarterly risk mitigation ledger, sanctions compliance, and geopolitical supply chain controls.', 'TopSecret', 'board', 'GOV', '1.1 MB', 'Approved', '10y', null, null, 'EMP-1004', null, null]
];

// ----------------------------------------------------------------------------
// 8. PRODUCTS (Exact 10: PROD-1001 .. PROD-1010)
// ----------------------------------------------------------------------------
$products = [
    ['PROD-1001', 'Industrial Optical Sensor Package', 'PerUnit', 'Multi-spectral 4K CMOS industrial inspection sensor with sapphire window, Modbus RTU / 4-20mA loop.', 12500.00],
    ['PROD-1002', 'Precision Geodetic Measurement Kit', 'PerUnit', 'Sub-millimeter GNSS-RTK baseline receiver with dual constellation tracking and IP68 enclosure.', 3500.00],
    ['PROD-1003', 'Automated Calibration Station', 'PerProject', 'Turnkey multi-axis automated pressure and thermal calibration bench with ISO 17025 certificate generator.', 3500.00],
    ['PROD-1004', 'Industrial PLC Integration', 'PerProject', 'Fail-safe SIL-3 PLC telemetry integration skid with redundant Profinet and optical bypass.', 3500.00],
    ['PROD-1005', 'Remote Monitoring Gateway', 'PerUnit', 'Edge computing telemetry hub with 4G/LTE failover, MQTT broker, and local SQLite buffer.', 3500.00],
    ['PROD-1006', 'High-Temp Thermal Pyrometer', 'PerUnit', 'Non-contact infrared optical pyrometer for blast furnaces up to 1,800C with air-purge collar.', 3500.00],
    ['PROD-1007', 'Vibration Analysis Sensor Skid', 'PerUnit', 'Tri-axial piezoelectric accelerometer package for turbine bearings with FFT vibration analytics.', 3500.00],
    ['PROD-1008', 'Electromagnetic Flowmeter Array', 'PerUnit', 'High-accuracy conductive fluid flowmeter with Hastelloy electrodes and digital pulse output.', 3500.00],
    ['PROD-1009', 'Differential Pressure Transmitter', 'PerUnit', 'HART protocol differential pressure cell for orifice gas flow measurement with 0.05% accuracy.', 3500.00],
    ['PROD-1010', 'SCADA Historian & Analytics Suite', 'AnnualContract', 'Enterprise operational data historian license with OPC-UA integration and predictive telemetry.', 3500.00]
];

// ============================================================================
// BUILD seed_baseline.sql
// ============================================================================
$out = [];
$out[] = "-- ============================================================================";
$out[] = "-- VOSTOKPRIBOR ENTERPRISE ECOSYSTEM - LOCKED BASELINE SEED";
$out[] = "-- Generated according to locked enterprise specifications";
$out[] = "-- Idempotent execution (INSERT ... ON DUPLICATE KEY UPDATE)";
$out[] = "-- Baseline entities:";
$out[] = "--  - 8 Departments";
$out[] = "--  - 95 Employees (EXE:5, SAL:16, OPS:20, ENG:16, FIN:10, HRA:8, ITD:14, GOV:6)";
$out[] = "--  - 10 Customers (CUS-1001 .. CUS-1010)";
$out[] = "--  - 15 Projects (PRJ-2026-001 .. PRJ-2026-015)";
$out[] = "--  - 10 Invoices (INV-2026-001 .. INV-2026-010)";
$out[] = "--  - 15 Tickets (TKT-2026-001 .. TKT-2026-015)";
$out[] = "--  - 15 Documents (DOC-2026-001 .. DOC-2026-015)";
$out[] = "--  - 10 Products (PROD-1001 .. PROD-1010)";
$out[] = "-- ============================================================================\n";

$out[] = "SET FOREIGN_KEY_CHECKS = 0;\n";

// 1. Departments
$out[] = "-- 1. DEPARTMENTS (8 Canonical Departments)";
$out[] = "INSERT INTO departments (dept_code, dept_name, main_function, employee_count_target) VALUES";
$deptRows = [];
foreach ($departments as $d) {
    $deptRows[] = sprintf("('%s', '%s', '%s', %d)", $d[0], addslashes($d[1]), addslashes($d[2]), $d[3]);
}
$out[] = implode(",\n", $deptRows);
$out[] = "ON DUPLICATE KEY UPDATE dept_name = VALUES(dept_name), main_function = VALUES(main_function), employee_count_target = VALUES(employee_count_target);\n";

// Remove legacy departments
$out[] = "DELETE FROM departments WHERE dept_code IN ('IT', 'LOG', 'QA');\n";

// 2. Customers
$out[] = "-- 2. CUSTOMERS (10 Canonical Customers: CUS-1001 .. CUS-1010)";
$out[] = "INSERT INTO customers (cus_id, company_name, sector, primary_contact_name, primary_contact_email, account_manager_emp_id, phone, headquarters, tax_id, health_score, account_tier, status) VALUES";
$cusRows = [];
foreach ($customers as $c) {
    $cusRows[] = sprintf("('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, '%s', 'Active')",
        $c[0], addslashes($c[1]), addslashes($c[2]), addslashes($c[3]), $c[4], $c[5], $c[6], addslashes($c[7]), $c[8], $c[9], $c[10]
    );
}
$out[] = implode(",\n", $cusRows);
$out[] = "ON DUPLICATE KEY UPDATE 
    company_name = VALUES(company_name),
    sector = VALUES(sector),
    primary_contact_name = VALUES(primary_contact_name),
    primary_contact_email = VALUES(primary_contact_email),
    account_manager_emp_id = VALUES(account_manager_emp_id),
    phone = VALUES(phone),
    headquarters = VALUES(headquarters),
    tax_id = VALUES(tax_id),
    health_score = VALUES(health_score),
    account_tier = VALUES(account_tier);\n";

// Customer contacts & accounts
$out[] = "-- Customer Contacts, Accounts & Portal Accounts";
foreach ($customers as $c) {
    $out[] = sprintf("INSERT INTO contacts (cus_id, full_name, role, email, phone) VALUES ('%s', '%s', 'Executive Director', '%s', '%s') ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), email = VALUES(email);",
        $c[0], addslashes($c[3]), $c[4], $c[6]
    );
    $userSlug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $c[1]));
    $username = substr($userSlug, 0, 15) . '_' . substr($c[0], 4);
    $out[] = sprintf("INSERT INTO customer_accounts (cus_id, username, email, password_hash, status) VALUES ('%s', '%s', '%s', '%s', 'Active') ON DUPLICATE KEY UPDATE email = VALUES(email), status = 'Active';",
        $c[0], $username, $c[4], $clientPassHash
    );
    $out[] = sprintf("INSERT INTO portal_accounts (cus_id, username, email) VALUES ('%s', 'portal_%s', '%s') ON DUPLICATE KEY UPDATE email = VALUES(email);",
        $c[0], strtolower(substr($c[0], 4)), $c[4]
    );
}
$out[] = "";

// 3. Employees
$out[] = "-- 3. EMPLOYEES (95 Canonical Employees: EMP-1001 .. EMP-1095)";
$out[] = "INSERT INTO employees (emp_id, full_name, job_title, department_code, clearance_level, email, manager_emp_id, employment_status, hire_date) VALUES";
$empRows = [];
foreach ($employees as $e) {
    $mgr = $e[6] ? sprintf("'%s'", $e[6]) : "NULL";
    $empRows[] = sprintf("('%s', '%s', '%s', '%s', '%s', '%s', %s, '%s', '%s')",
        $e[0], addslashes($e[1]), addslashes($e[2]), $e[3], $e[4], $e[5], $mgr, $e[7], $e[8]
    );
}
$out[] = implode(",\n", $empRows);
$out[] = "ON DUPLICATE KEY UPDATE 
    full_name = VALUES(full_name),
    job_title = VALUES(job_title),
    department_code = VALUES(department_code),
    clearance_level = VALUES(clearance_level),
    email = VALUES(email),
    manager_emp_id = VALUES(manager_emp_id),
    employment_status = VALUES(employment_status);\n";

// Remove EMP-0001
$out[] = "-- Reassign audit logs from legacy EMP-0001 to EMP-1004, then purge EMP-0001";
$out[] = "UPDATE audit_logs SET actor_emp_id = 'EMP-1004' WHERE actor_emp_id = 'EMP-0001';";
$out[] = "DELETE FROM employee_roles WHERE emp_id = 'EMP-0001';";
$out[] = "DELETE FROM employee_accounts WHERE emp_id = 'EMP-0001';";
$out[] = "DELETE FROM employees WHERE emp_id = 'EMP-0001';\n";

// Employee Accounts & Roles
$out[] = "-- Employee Accounts & Core Roles";
foreach ($employees as $e) {
    $uname = strtolower(explode('@', $e[5])[0]);
    $out[] = sprintf("INSERT INTO employee_accounts (emp_id, username, password_hash, status) VALUES ('%s', '%s', '%s', 'Active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 'Active';",
        $e[0], $uname, $defaultPassHash
    );
}
$out[] = "";

// Grant roles
$out[] = "-- Assign Roles in employee_roles";
$out[] = "INSERT INTO employee_roles (emp_id, role_id, granted_at, granted_by_emp_id) VALUES";
$out[] = "('EMP-1001', 1, NOW(), 'EMP-1001'), -- SuperAdmin (CEO)";
$out[] = "('EMP-1004', 1, NOW(), 'EMP-1001'), -- SuperAdmin (CISO)";
$out[] = "('EMP-1002', 7, NOW(), 'EMP-1001'), -- HR Director (COO)";
$out[] = "('EMP-1003', 4, NOW(), 'EMP-1001'), -- CTO (Developer & Systems)";
$out[] = "('EMP-1005', 6, NOW(), 'EMP-1001'), -- CFO (Finance Controller)";
$out[] = "('EMP-1006', 3, NOW(), 'EMP-1001'), -- Sales Director";
$out[] = "('EMP-1007', 3, NOW(), 'EMP-1006'), -- Sales Key Account Mgr";
$out[] = "('EMP-1008', 3, NOW(), 'EMP-1006'), -- Sales Engineer";
$out[] = "('EMP-1009', 3, NOW(), 'EMP-1006'), -- Sales Account Exec";
$out[] = "('EMP-1010', 3, NOW(), 'EMP-1006'), -- Sales Account Director";
$out[] = "('EMP-1011', 8, NOW(), 'EMP-1001'), -- Operations Director";
$out[] = "('EMP-1012', 8, NOW(), 'EMP-1011'), -- Logistics Specialist";
$out[] = "('EMP-1013', 8, NOW(), 'EMP-1011'), -- Fulfilment Specialist";
$out[] = "('EMP-1014', 8, NOW(), 'EMP-1011'), -- Warehouse Supervisor";
$out[] = "('EMP-1015', 8, NOW(), 'EMP-1011'), -- Logistics Dispatcher";
$out[] = "('EMP-1016', 4, NOW(), 'EMP-1003'), -- Systems Architect";
$out[] = "('EMP-1017', 4, NOW(), 'EMP-1016'), -- Automation Engineer";
$out[] = "('EMP-1018', 5, NOW(), 'EMP-1003'), -- Systems & Security Engineer";
$out[] = "('EMP-1019', 4, NOW(), 'EMP-1016'), -- SCADA Lead";
$out[] = "('EMP-1020', 4, NOW(), 'EMP-1016'), -- Embedded Systems";
$out[] = "('EMP-1021', 5, NOW(), 'EMP-1003')  -- IT Systems Administrator";
$out[] = "ON DUPLICATE KEY UPDATE granted_at = NOW();\n";

// 4. Projects
$out[] = "-- 4. PROJECTS (15 Canonical Projects: PRJ-2026-001 .. PRJ-2026-015)";
$out[] = "INSERT INTO projects (prj_id, project_name, cus_id, project_manager_emp_id, budget, currency, status, start_date, end_date, progress_percent, facility_location, scope_summary) VALUES";
$prjRows = [];
foreach ($projects as $p) {
    $prjRows[] = sprintf("('%s', '%s', '%s', '%s', %.2f, '%s', '%s', '%s', '%s', %d, '%s', '%s')",
        $p[0], addslashes($p[1]), $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8], $p[9], addslashes($p[10]), addslashes($p[11])
    );
}
$out[] = implode(",\n", $prjRows);
$out[] = "ON DUPLICATE KEY UPDATE 
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
    scope_summary = VALUES(scope_summary);\n";

// 5. Invoices
$out[] = "-- 5. INVOICES (10 Canonical Invoices: INV-2026-001 .. INV-2026-010)";
$out[] = "INSERT INTO invoices (inv_id, cus_id, prj_id, created_by_emp_id, total_value, currency, payment_status, issued_at, due_date, payment_terms, notes, paid_at) VALUES";
$invRows = [];
foreach ($invoices as $inv) {
    $paidAt = $inv[11] ? sprintf("'%s'", $inv[11]) : "NULL";
    $invRows[] = sprintf("('%s', '%s', '%s', '%s', %.2f, '%s', '%s', '%s', '%s', '%s', '%s', %s)",
        $inv[0], $inv[1], $inv[2], $inv[3], $inv[4], $inv[5], $inv[6], $inv[7], $inv[8], $inv[9], addslashes($inv[10]), $paidAt
    );
}
$out[] = implode(",\n", $invRows);
$out[] = "ON DUPLICATE KEY UPDATE 
    cus_id = VALUES(cus_id),
    prj_id = VALUES(prj_id),
    total_value = VALUES(total_value),
    currency = VALUES(currency),
    payment_status = VALUES(payment_status),
    notes = VALUES(notes);\n";

// 6. Tickets
$out[] = "-- 6. TICKETS (15 Canonical Tickets: TKT-2026-001 .. TKT-2026-015)";
$out[] = "INSERT INTO tickets (tkt_id, requester_type, requester_cus_id, requester_emp_id, source_system, title, description, requester_name, requester_role, requester_dept, priority, assigned_emp_id, status) VALUES";
$tktRows = [];
foreach ($tickets as $t) {
    $cus = $t[2] ? sprintf("'%s'", $t[2]) : "NULL";
    $emp = $t[3] ? sprintf("'%s'", $t[3]) : "NULL";
    $tktRows[] = sprintf("('%s', '%s', %s, %s, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
        $t[0], $t[1], $cus, $emp, $t[4], addslashes($t[5]), addslashes($t[6]), addslashes($t[7]), addslashes($t[8]), addslashes($t[9]), $t[10], $t[11], $t[12]
    );
}
$out[] = implode(",\n", $tktRows);
$out[] = "ON DUPLICATE KEY UPDATE 
    title = VALUES(title),
    description = VALUES(description),
    priority = VALUES(priority),
    assigned_emp_id = VALUES(assigned_emp_id),
    status = VALUES(status);\n";

// 7. Documents
$out[] = "-- 7. DOCUMENTS (15 Canonical Documents: DOC-2026-001 .. DOC-2026-015)";
$out[] = "INSERT INTO documents (doc_id, file_name, description, classification, folder, department, file_size, status, retention_period, project_ref, customer_ref, owner_emp_id, related_prj_id, related_cus_id) VALUES";
$docRows = [];
foreach ($documents as $doc) {
    $pref = $doc[9] ? sprintf("'%s'", addslashes($doc[9])) : "NULL";
    $cref = $doc[10] ? sprintf("'%s'", addslashes($doc[10])) : "NULL";
    $rpid = $doc[12] ? sprintf("'%s'", $doc[12]) : "NULL";
    $rcid = $doc[13] ? sprintf("'%s'", $doc[13]) : "NULL";
    $docRows[] = sprintf("('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %s, %s, '%s', %s, %s)",
        $doc[0], addslashes($doc[1]), addslashes($doc[2]), $doc[3], $doc[4], $doc[5], $doc[6], $doc[7], $doc[8], $pref, $cref, $doc[11], $rpid, $rcid
    );
}
$out[] = implode(",\n", $docRows);
$out[] = "ON DUPLICATE KEY UPDATE 
    file_name = VALUES(file_name),
    description = VALUES(description),
    classification = VALUES(classification),
    status = VALUES(status);\n";

// 8. Products
$out[] = "-- 8. PRODUCTS (10 Canonical Products: PROD-1001 .. PROD-1010)";
$out[] = "INSERT INTO products (prod_id, product_name, billing_model, description, price) VALUES";
$prodRows = [];
foreach ($products as $pr) {
    $prodRows[] = sprintf("('%s', '%s', '%s', '%s', %.2f)",
        $pr[0], addslashes($pr[1]), $pr[2], addslashes($pr[3]), $pr[4]
    );
}
$out[] = implode(",\n", $prodRows);
$out[] = "ON DUPLICATE KEY UPDATE 
    product_name = VALUES(product_name),
    billing_model = VALUES(billing_model),
    description = VALUES(description),
    price = VALUES(price);\n";

// 9. Reset ID Counters
$out[] = "-- 9. ID COUNTERS";
$out[] = "INSERT INTO id_counters (name, next_val) VALUES";
$out[] = "('customers', 1011),";
$out[] = "('projects', 16),";
$out[] = "('invoices', 11),";
$out[] = "('documents', 16),";
$out[] = "('tickets', 16),";
$out[] = "('orders', 1010),";
$out[] = "('employees', 1096)";
$out[] = "ON DUPLICATE KEY UPDATE next_val = VALUES(next_val);\n";

$out[] = "SET FOREIGN_KEY_CHECKS = 1;\n";

file_put_contents(__DIR__ . '/../DataBase/seed_baseline.sql', implode("\n", $out));
echo "seed_baseline.sql generated successfully!\n";
