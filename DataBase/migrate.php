<?php
/**
 * VOSTOKPRIBOR Database Master Migration and Seeder
 * Merges vostokpribor_1.sql missing tables and seeds systems, roles, accounts, and permissions.
 */

$host = '127.0.0.1';
$dbname = 'vostokpribor';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host={$host};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbname}`");
    echo "[OK] Connected to database '{$dbname}'.\n";
} catch (Exception $e) {
    die("[ERROR] Could not connect to MySQL: " . $e->getMessage() . "\n");
}

// 1. Create missing tables if they don't exist
$tablesSql = [
    "CREATE TABLE IF NOT EXISTS `systems_catalog` (
        `system_id` varchar(4) NOT NULL,
        `system_name` varchar(100) DEFAULT NULL,
        `fqdn` varchar(100) DEFAULT NULL,
        `criticality` varchar(30) DEFAULT NULL,
        `trust_zone` varchar(30) DEFAULT NULL,
        PRIMARY KEY (`system_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS `roles` (
        `role_id` int(11) NOT NULL AUTO_INCREMENT,
        `role_name` varchar(100) NOT NULL,
        `description` text DEFAULT NULL,
        PRIMARY KEY (`role_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS `permissions` (
        `permission_id` int(11) NOT NULL AUTO_INCREMENT,
        `permission_name` varchar(100) NOT NULL,
        `description` text DEFAULT NULL,
        PRIMARY KEY (`permission_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS `role_permissions` (
        `role_id` int(11) NOT NULL,
        `permission_id` int(11) NOT NULL,
        PRIMARY KEY (`role_id`, `permission_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS `role_system_access` (
        `role_id` int(11) NOT NULL,
        `system_id` varchar(4) NOT NULL,
        `access_level` varchar(30) DEFAULT 'Full',
        PRIMARY KEY (`role_id`, `system_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS `employee_roles` (
        `emp_id` varchar(10) NOT NULL,
        `role_id` int(11) NOT NULL,
        `granted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `granted_by_emp_id` varchar(10) DEFAULT NULL,
        PRIMARY KEY (`emp_id`, `role_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS `employee_accounts` (
        `account_id` int(11) NOT NULL AUTO_INCREMENT,
        `emp_id` varchar(10) NOT NULL,
        `username` varchar(100) NOT NULL,
        `password_hash` varchar(255) NOT NULL,
        `mfa_enabled` tinyint(1) DEFAULT 0,
        `status` varchar(20) DEFAULT 'Active',
        `last_login` timestamp NULL DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`account_id`),
        UNIQUE KEY `idx_emp_account_username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS `customer_accounts` (
        `account_id` int(11) NOT NULL AUTO_INCREMENT,
        `cus_id` varchar(10) NOT NULL,
        `username` varchar(100) NOT NULL,
        `email` varchar(150) DEFAULT NULL,
        `password_hash` varchar(255) NOT NULL,
        `mfa_enabled` tinyint(1) DEFAULT 0,
        `status` varchar(20) DEFAULT 'Active',
        `last_login` timestamp NULL DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`account_id`),
        UNIQUE KEY `idx_cus_account_username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS `authentication_events` (
        `event_id` bigint(20) NOT NULL AUTO_INCREMENT,
        `account_type` varchar(20) NOT NULL,
        `employee_account_id` int(11) DEFAULT NULL,
        `customer_account_id` int(11) DEFAULT NULL,
        `system_id` varchar(4) DEFAULT NULL,
        `device_id` int(11) DEFAULT NULL,
        `ip_id` int(11) DEFAULT NULL,
        `event_type` varchar(30) DEFAULT 'LOGIN_ATTEMPT',
        `source_ip` varchar(45) DEFAULT '127.0.0.1',
        `occurred_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `success` tinyint(1) DEFAULT 1,
        `details` text DEFAULT NULL,
        PRIMARY KEY (`event_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS `user_sessions` (
        `session_id` varchar(64) NOT NULL,
        `account_type` varchar(20) NOT NULL,
        `employee_account_id` int(11) DEFAULT NULL,
        `customer_account_id` int(11) DEFAULT NULL,
        `system_id` varchar(4) NOT NULL,
        `ip_address` varchar(45) DEFAULT NULL,
        `user_agent` text DEFAULT NULL,
        `status` varchar(20) DEFAULT 'Active',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `expires_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`session_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS `product_inventory` (
        `prod_id` varchar(10) NOT NULL,
        `warehouse_location` varchar(100) DEFAULT NULL,
        `quantity_on_hand` int(11) DEFAULT 0,
        `reorder_level` int(11) DEFAULT 0,
        PRIMARY KEY (`prod_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS `quotes` (
        `quote_id` int(11) NOT NULL AUTO_INCREMENT,
        `cus_id` varchar(10) NOT NULL,
        `prod_id` varchar(10) NOT NULL,
        `quantity` int(11) NOT NULL,
        `unit_price` decimal(12,2) DEFAULT NULL,
        PRIMARY KEY (`quote_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS `risk_register` (
        `risk_id` int(11) NOT NULL AUTO_INCREMENT,
        `description` text DEFAULT NULL,
        `likelihood` varchar(20) DEFAULT NULL,
        `impact` varchar(20) DEFAULT NULL,
        `owner_emp_id` varchar(10) DEFAULT NULL,
        `status` varchar(30) DEFAULT 'Open',
        `review_date` date DEFAULT NULL,
        PRIMARY KEY (`risk_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
];

foreach ($tablesSql as $sql) {
    $pdo->exec($sql);
}
echo "[OK] All core authentication & system catalog tables created.\n";

// 2. Seed systems_catalog
$systems = [
    ['ADM', 'Admin & Governance Portal', 'admin.vostokpribor.local', 'MissionCritical', 'Zone-Alpha'],
    ['CRM', 'CRM System', 'crm.vostokpribor.local', 'High', 'Zone-Bravo'],
    ['CUS', 'Customer Portal', 'customer.vostokpribor.local', 'High', 'Zone-External'],
    ['DEV', 'Developer Portal', 'developer.vostokpribor.local', 'High', 'Zone-Bravo'],
    ['EMP', 'Employee Intranet', 'intranet.vostokpribor.local', 'Medium', 'Zone-Internal'],
    ['DOC', 'File Center', 'files.vostokpribor.local', 'High', 'Zone-Bravo'],
    ['FIN', 'Finance & Billing', 'finance.vostokpribor.local', 'MissionCritical', 'Zone-Alpha'],
    ['HR',  'HR System', 'hr.vostokpribor.local', 'High', 'Zone-Bravo'],
    ['IT',  'IT Helpdesk', 'helpdesk.vostokpribor.local', 'Medium', 'Zone-Internal'],
    ['SHP', 'Online Shop B2B', 'shop.vostokpribor.local', 'High', 'Zone-External'],
    ['WEB', 'Corporate Web Platform', 'vostokpribor.local', 'Public', 'Zone-DMZ']
];

$stmtSys = $pdo->prepare("INSERT INTO `systems_catalog` (`system_id`, `system_name`, `fqdn`, `criticality`, `trust_zone`) 
    VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `system_name`=VALUES(`system_name`), `criticality`=VALUES(`criticality`)");
foreach ($systems as $s) {
    $stmtSys->execute($s);
}
echo "[OK] systems_catalog seeded (11 systems).\n";

// 3. Seed roles
$roles = [
    [1, 'Executive SuperAdmin', 'Full administrative authority and governance oversight across all 11 VOSTOKPRIBOR systems'],
    [2, 'Chief Governance Officer', 'Compliance, legal audits, executive risk registries and policy oversight'],
    [3, 'Sales Director & Manager', 'CRM pipeline oversight, B2B quotes, enterprise client accounts and order approval'],
    [4, 'Senior Automation & Developer', 'Engineering codebase, API developer portal, telemetry and system integrations'],
    [5, 'Systems Engineer & IT Support', 'Infrastructure management, IT Helpdesk ticketing, device telemetry, network security'],
    [6, 'Chief Financial Officer & Controller', 'Invoices, enterprise billing cycles, audits, and payment records'],
    [7, 'HR Director & Operations', 'Personnel records, department assignments, onboarding, payroll compliance'],
    [8, 'Logistics & Supply Chain Specialist', 'Warehouse inventory, product catalog, delivery telemetry and procurement'],
    [9, 'Customer Client Account', 'Access to Customer Portal, project tracking, ticket creation, B2B purchasing']
];

$stmtRole = $pdo->prepare("INSERT INTO `roles` (`role_id`, `role_name`, `description`) 
    VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `role_name`=VALUES(`role_name`), `description`=VALUES(`description`)");
foreach ($roles as $r) {
    $stmtRole->execute($r);
}
echo "[OK] roles seeded.\n";

// 4. Seed role_system_access
$roleAccess = [
    // SuperAdmin has full access to ALL systems
    [1, 'ADM', 'Full'], [1, 'CRM', 'Full'], [1, 'CUS', 'Full'], [1, 'DEV', 'Full'], [1, 'EMP', 'Full'],
    [1, 'DOC', 'Full'], [1, 'FIN', 'Full'], [1, 'HR', 'Full'], [1, 'IT', 'Full'], [1, 'SHP', 'Full'], [1, 'WEB', 'Full'],
    
    // Chief Governance Officer
    [2, 'ADM', 'Full'], [2, 'DOC', 'Full'], [2, 'EMP', 'Full'], [2, 'FIN', 'Audit'], [2, 'HR', 'Audit'],
    
    // Sales Director & Manager
    [3, 'CRM', 'Full'], [3, 'SHP', 'Full'], [3, 'EMP', 'Read'], [3, 'DOC', 'ReadWrite'], [3, 'CUS', 'Supervise'],
    
    // Senior Developer
    [4, 'DEV', 'Full'], [4, 'IT', 'Full'], [4, 'DOC', 'ReadWrite'], [4, 'EMP', 'Read'], [4, 'WEB', 'ReadWrite'],
    
    // Systems Engineer & IT Support
    [5, 'IT', 'Full'], [5, 'DEV', 'ReadWrite'], [5, 'ADM', 'Telemetry'], [5, 'DOC', 'ReadWrite'], [5, 'EMP', 'Read'],
    
    // CFO & Financial Controller
    [6, 'FIN', 'Full'], [6, 'ADM', 'Audit'], [6, 'EMP', 'Read'], [6, 'DOC', 'ReadWrite'], [6, 'CRM', 'Read'],
    
    // HR Director
    [7, 'HR', 'Full'], [7, 'EMP', 'Full'], [7, 'DOC', 'ReadWrite'], [7, 'ADM', 'Read'],
    
    // Logistics
    [8, 'SHP', 'Full'], [8, 'EMP', 'Read'], [8, 'DOC', 'ReadWrite'], [8, 'FIN', 'Read'],
    
    // Customer Client
    [9, 'CUS', 'Full'], [9, 'SHP', 'Full'], [9, 'WEB', 'Public']
];

$stmtAccess = $pdo->prepare("INSERT INTO `role_system_access` (`role_id`, `system_id`, `access_level`) 
    VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `access_level`=VALUES(`access_level`)");
foreach ($roleAccess as $ra) {
    $stmtAccess->execute($ra);
}
echo "[OK] role_system_access seeded.\n";

// 5. Seed employee accounts
$defaultHash = password_hash('Vostok2026!', PASSWORD_BCRYPT);
$adminHash   = password_hash('AdminPass2026!', PASSWORD_BCRYPT);

// Accounts to seed (emp_id, username, password_hash, role_id)
$employeeAccounts = [
    // Executives
    ['EMP-1001', 'viktor.sokolov', $adminHash, 1],
    ['EMP-1001', 'ADM-VP-01', $adminHash, 1],
    ['EMP-1001', 'EMP-1001', $adminHash, 1],
    ['EMP-1002', 'amina.karimova', $adminHash, 7],
    ['EMP-1002', 'HR-VP-201', $adminHash, 7],
    ['EMP-1002', 'HR-VP-104', $adminHash, 7],
    ['EMP-1002', 'EMP-1002', $adminHash, 7],
    ['EMP-1003', 'daniel.weber', $adminHash, 6],
    ['EMP-1003', 'FIN-VP-102', $adminHash, 6],
    ['EMP-1003', 'FIN-VP-502', $adminHash, 6],
    ['EMP-1003', 'EMP-1003', $adminHash, 6],
    ['EMP-1004', 'elena.morozova', $adminHash, 4],
    ['EMP-1004', 'EMP-1004', $adminHash, 4],
    ['EMP-1005', 'timur.akhmetov', $adminHash, 2],
    ['EMP-1005', 'EMP-1005', $adminHash, 2],

    // Sales
    ['EMP-1006', 'pavel.orlov', $defaultHash, 3],
    ['EMP-1006', 'CRM-VP-842', $defaultHash, 3],
    ['EMP-1006', 'EMP-842', $defaultHash, 3],
    ['EMP-1006', 'EMP-1006', $defaultHash, 3],
    ['EMP-1007', 'sara.lindholm', $defaultHash, 3],
    ['EMP-1007', 'EMP-1007', $defaultHash, 3],
    ['EMP-1008', 'bekzod.rakhimov', $defaultHash, 3],
    ['EMP-1008', 'EMP-1008', $defaultHash, 3],
    ['EMP-1009', 'nadia.petrova', $defaultHash, 3],
    ['EMP-1009', 'EMP-1009', $defaultHash, 3],
    ['EMP-1010', 'markus.klein', $defaultHash, 3],
    ['EMP-1010', 'EMP-1010', $defaultHash, 3],

    // Operations & Logistics
    ['EMP-1011', 'arman.tulegenov', $defaultHash, 8],
    ['EMP-1011', 'EMP-1011', $defaultHash, 8],
    ['EMP-1012', 'rustam.bekov', $defaultHash, 8],
    ['EMP-1012', 'EMP-1012', $defaultHash, 8],
    ['EMP-1013', 'ilona.vetra', $defaultHash, 8],
    ['EMP-1013', 'EMP-1013', $defaultHash, 8],
    ['EMP-1014', 'mikhail.antonov', $defaultHash, 8],
    ['EMP-1014', 'EMP-1014', $defaultHash, 8],
    ['EMP-1015', 'kamila.nurzhan', $defaultHash, 8],
    ['EMP-1015', 'EMP-1015', $defaultHash, 8],

    // Engineering & Dev & IT
    ['EMP-1016', 'erik.hansen', $defaultHash, 4],
    ['EMP-1016', 'EMP-1016', $defaultHash, 4],
    ['EMP-1017', 'dana.yermak', $defaultHash, 4],
    ['EMP-1017', 'EMP-1017', $defaultHash, 4],
    ['EMP-1018', 'leonid.volkov', $defaultHash, 5],
    ['EMP-1018', 'IT-VP-304', $defaultHash, 5],
    ['EMP-1018', 'EMP-1018', $defaultHash, 5],
    ['EMP-1019', 'farida.iskakova', $defaultHash, 4],
    ['EMP-1019', 'DOC-VP-501', $defaultHash, 4],
    ['EMP-1019', 'CST-VP-09', $defaultHash, 4],
    ['EMP-1019', 'EMP-VP-1019', $defaultHash, 4],
    ['EMP-1019', 'EMP-1019', $defaultHash, 4],
    ['EMP-1020', 'jonas.richter', $defaultHash, 4],
    ['EMP-1020', 'DEV-VP-994', $defaultHash, 4],
    ['EMP-1020', 'EMP-1020', $defaultHash, 4],
];

$stmtEmpAcc = $pdo->prepare("INSERT INTO `employee_accounts` (`emp_id`, `username`, `password_hash`, `mfa_enabled`, `status`)
    VALUES (?, ?, ?, 0, 'Active')
    ON DUPLICATE KEY UPDATE `password_hash`=VALUES(`password_hash`), `status`='Active'");

$stmtEmpRole = $pdo->prepare("INSERT INTO `employee_roles` (`emp_id`, `role_id`, `granted_by_emp_id`) 
    VALUES (?, ?, 'EMP-1001')
    ON DUPLICATE KEY UPDATE `role_id`=VALUES(`role_id`)");

foreach ($employeeAccounts as $ea) {
    $stmtEmpAcc->execute([$ea[0], $ea[1], $ea[2]]);
    $stmtEmpRole->execute([$ea[0], $ea[3]]);
}
echo "[OK] employee_accounts seeded (" . count($employeeAccounts) . " usernames/aliases).\n";

// 6. Seed customer accounts
$clientHash = password_hash('ClientPass2026!', PASSWORD_BCRYPT);
$customerAccounts = [
    ['CUS-1001', 'sergei.makarov', 's.makarov@aral-geomatics.kz', $clientHash],
    ['CUS-1001', 'CLT-77210', 'client77210@vostokpribor.local', $clientHash],
    ['CUS-1001', 'CUS-1001', 'cus1001@aral-geomatics.kz', $clientHash],
    ['CUS-1002', 'kristaps.ozols', 'k.ozols@baltnord-systems.eu', $clientHash],
    ['CUS-1002', 'SHP-VP-11', 'b2b-buyer11@baltnord.eu', $clientHash],
    ['CUS-1002', 'CUS-1002', 'cus1002@baltnord.eu', $clientHash],
    ['CUS-1003', 'yerlan.bektemis', 'y.bektemis@steppemining.kz', $clientHash],
    ['CUS-1003', 'CUS-1003', 'cus1003@steppemining.kz', $clientHash],
    ['CUS-1004', 'lukas.brandt', 'l.brandt@rheinwerk-inst.de', $clientHash],
    ['CUS-1004', 'CUS-1004', 'cus1004@rheinwerk.de', $clientHash],
    ['CUS-1005', 'dilshod.karim', 'd.karim@tashkent-precision.uz', $clientHash],
    ['CUS-1005', 'CUS-1005', 'cus1005@tashkent-precision.uz', $clientHash],
    ['CUS-1006', 'mara.kalnina', 'm.kalnina@daugava-optical.lv', $clientHash],
    ['CUS-1007', 'murad.safarov', 'm.safarov@caspian-robotics.az', $clientHash],
    ['CUS-1008', 'oleg.petrenko', 'o.petrenko@eurasia-water.ua', $clientHash],
    ['CUS-1009', 'ainur.sadyk', 'a.sadyk@altai-env.kz', $clientHash],
    ['CUS-1010', 'tomas.varga', 't.varga@central-rail.hu', $clientHash]
];

$stmtCusAcc = $pdo->prepare("INSERT INTO `customer_accounts` (`cus_id`, `username`, `email`, `password_hash`, `mfa_enabled`, `status`)
    VALUES (?, ?, ?, ?, 0, 'Active')
    ON DUPLICATE KEY UPDATE `password_hash`=VALUES(`password_hash`), `status`='Active'");

foreach ($customerAccounts as $ca) {
    $stmtCusAcc->execute($ca);
}
echo "[OK] customer_accounts seeded (" . count($customerAccounts) . " usernames/aliases).\n";

echo "\n*** VOSTOKPRIBOR DATABASE MIGRATION & SEEDING COMPLETED SUCCESSFULLY ***\n";
