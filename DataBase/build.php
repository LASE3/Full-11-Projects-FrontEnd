<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }

/**
 * VOSTOKPRIBOR - Automated One-Path Database Builder & Migrator
 * Location: DataBase/build.php
 *
 * Rebuilds the database from scratch with zero manual steps:
 * 1. Initializes / wipes database schema
 * 2. Applies original dump (vostokpribor.sql)
 * 3. Applies all numbered migrations in sequence (001 .. 008+)
 * 4. Applies locked baseline seed (seed_baseline.sql)
 * 5. Archives and purges non-baseline fixture data
 * 6. Verifies exact baseline counts (95 staff + 1 sys account, 8/10/15/10/15/15/10)
 */

require_once __DIR__ . '/../config/db.php';

$targetDb = optionalEnv('DB_NAME', 'vostokpribor');
if (in_array('--test', $argv, true)) {
    $targetDb = 'vostokpribor_test';
}

echo "====================================================================\n";
echo "       VOSTOKPRIBOR DATABASE REPRODUCIBLE BUILD ENGINE              \n";
echo "       Target Database: {$targetDb}                                 \n";
echo "====================================================================\n\n";

$host = optionalEnv('DB_HOST', '127.0.0.1');
$port = optionalEnv('DB_PORT', '3306');
$user = requireEnv('DB_USER');
$pass = requireEnv('DB_PASS');

// 1. Connect to MySQL server
try {
    $rootPdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
    ]);
} catch (PDOException $e) {
    fwrite(STDERR, "Database connection failed: " . $e->getMessage() . "\n");
    exit(1);
}

// 2. Re-create clean target database
echo "[1/6] Recreating database '{$targetDb}'...\n";
$rootPdo->exec("DROP DATABASE IF EXISTS `{$targetDb}`");
$rootPdo->exec("CREATE DATABASE `{$targetDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$rootPdo->exec("USE `{$targetDb}`");

/**
 * Helper to execute large SQL script files reliably
 */
function executeSqlFile(PDO $pdo, string $filePath, string $label): void {
    if (!file_exists($filePath)) {
        throw new RuntimeException("SQL file not found: {$filePath}");
    }
    echo "  Applying {$label}... ";
    $sql = file_get_contents($filePath);
    if ($sql === false) {
        throw new RuntimeException("Could not read {$filePath}");
    }

    // Temporarily disable foreign keys during large migrations
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec($sql);
    // Flush any pending multi-statement result sets
    while ($pdo->query("SELECT 1")->nextRowset()) {}
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    echo "DONE\n";
}

// 3. Apply original base dump
echo "\n[2/6] Applying base schema dump (vostokpribor.sql)...\n";
executeSqlFile($rootPdo, __DIR__ . '/vostokpribor.sql', 'vostokpribor.sql');

// 4. Apply all migrations in order
echo "\n[3/6] Applying numbered migrations in order...\n";
$migrationFiles = glob(__DIR__ . '/migrations/*.sql') ?: [];
sort($migrationFiles, SORT_NATURAL);

foreach ($migrationFiles as $mig) {
    $baseName = basename($mig);
    executeSqlFile($rootPdo, $mig, $baseName);
}

// 5. Apply baseline seed
echo "\n[4/6] Applying locked baseline seed (seed_baseline.sql)...\n";
executeSqlFile($rootPdo, __DIR__ . '/seed_baseline.sql', 'seed_baseline.sql');

// 4b. Install DB triggers (DELIMITER not supported in PDO — use direct exec)
echo "\n[4b] Installing DB safety triggers...\n";
try {
    $rootPdo->exec("DROP TRIGGER IF EXISTS trg_protect_system_account");
    $rootPdo->exec("
        CREATE TRIGGER trg_protect_system_account
        BEFORE DELETE ON employees
        FOR EACH ROW
        BEGIN
            IF OLD.emp_id = 'EMP-0001' OR OLD.is_system_account = 1 THEN
                SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Cannot delete system account EMP-0001';
            END IF;
        END
    ");
    echo "  trg_protect_system_account... DONE\n";

    $rootPdo->exec("DROP TRIGGER IF EXISTS trg_protect_last_superadmin");
    $rootPdo->exec("
        CREATE TRIGGER trg_protect_last_superadmin
        BEFORE DELETE ON employee_roles
        FOR EACH ROW
        BEGIN
            DECLARE sa_role_id INT;
            DECLARE remaining INT;
            SELECT role_id INTO sa_role_id FROM roles
            WHERE role_name IN ('SuperAdmin','Executive SuperAdmin')
            LIMIT 1;
            IF OLD.role_id = sa_role_id THEN
                SELECT COUNT(*) INTO remaining
                FROM employee_roles
                WHERE role_id = sa_role_id AND emp_id != OLD.emp_id;
                IF remaining = 0 THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Cannot remove the last SuperAdmin role assignment';
                END IF;
            END IF;
        END
    ");
    echo "  trg_protect_last_superadmin... DONE\n";
} catch (Throwable $trigErr) {
    echo "  WARNING: Trigger install failed: " . $trigErr->getMessage() . "\n";
}

// 6. Ensure SuperAdmin password hash matches environment variable if provided
$testAdminPass = getenv('TEST_ADMIN_PASSWORD');
if (!empty($testAdminPass)) {
    echo "  Setting EMP-0001 password hash from TEST_ADMIN_PASSWORD...\n";
    $adminHash = password_hash($testAdminPass, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $rootPdo->prepare("UPDATE employee_accounts SET password_hash = ? WHERE emp_id = 'EMP-0001'");
    $stmt->execute([$adminHash]);
}

// 7. Archive & clean non-baseline fixture data
echo "\n[5/6] Cleaning non-baseline rows and archiving test fixtures...\n";
$rootPdo->exec("SET FOREIGN_KEY_CHECKS = 0");

// Projects
$rootPdo->exec("DELETE FROM billing_cycles WHERE prj_id NOT BETWEEN 'PRJ-2026-001' AND 'PRJ-2026-015'");
$rootPdo->exec("DELETE FROM ops_tasks WHERE prj_id NOT BETWEEN 'PRJ-2026-001' AND 'PRJ-2026-015'");
$rootPdo->exec("DELETE FROM projects WHERE prj_id NOT BETWEEN 'PRJ-2026-001' AND 'PRJ-2026-015'");

// Invoices
$rootPdo->exec("DELETE FROM invoice_items WHERE inv_id NOT BETWEEN 'INV-2026-001' AND 'INV-2026-010'");
$rootPdo->exec("DELETE FROM payments WHERE inv_id NOT BETWEEN 'INV-2026-001' AND 'INV-2026-010'");
$rootPdo->exec("DELETE FROM invoices WHERE inv_id NOT BETWEEN 'INV-2026-001' AND 'INV-2026-010'");

// Tickets
$rootPdo->exec("DELETE FROM ticket_comments WHERE tkt_id NOT BETWEEN 'TKT-2026-001' AND 'TKT-2026-015'");
$rootPdo->exec("DELETE FROM ticket_escalations WHERE tkt_id NOT BETWEEN 'TKT-2026-001' AND 'TKT-2026-015'");
$rootPdo->exec("DELETE FROM tickets WHERE tkt_id NOT BETWEEN 'TKT-2026-001' AND 'TKT-2026-015'");

// Documents
$rootPdo->exec("DELETE FROM document_approvals WHERE doc_id NOT BETWEEN 'DOC-2026-001' AND 'DOC-2026-015'");
$rootPdo->exec("DELETE FROM document_versions WHERE doc_id NOT BETWEEN 'DOC-2026-001' AND 'DOC-2026-015'");
$rootPdo->exec("DELETE FROM document_access_log WHERE doc_id NOT BETWEEN 'DOC-2026-001' AND 'DOC-2026-015'");
$rootPdo->exec("DELETE FROM documents WHERE doc_id NOT BETWEEN 'DOC-2026-001' AND 'DOC-2026-015'");

// Customers
$rootPdo->exec("DELETE FROM contacts WHERE cus_id NOT BETWEEN 'CUS-1001' AND 'CUS-1010'");
$rootPdo->exec("DELETE FROM customer_accounts WHERE cus_id NOT BETWEEN 'CUS-1001' AND 'CUS-1010'");
$rootPdo->exec("DELETE FROM portal_accounts WHERE cus_id NOT BETWEEN 'CUS-1001' AND 'CUS-1010'");
$rootPdo->exec("DELETE FROM billing_cycles WHERE cus_id NOT BETWEEN 'CUS-1001' AND 'CUS-1010'");
$rootPdo->exec("DELETE FROM opportunities WHERE cus_id NOT BETWEEN 'CUS-1001' AND 'CUS-1010'");
$rootPdo->exec("DELETE FROM customers WHERE cus_id NOT BETWEEN 'CUS-1001' AND 'CUS-1010'");

// Products
$rootPdo->exec("DELETE FROM products WHERE prod_id NOT BETWEEN 'PROD-1001' AND 'PROD-1010'");

// Clean temporary or fake integration logs
$rootPdo->exec("DELETE FROM system_integration_logs WHERE endpoint = 'BASELINE_HEARTBEAT' OR payload_summary LIKE '%heartbeat%'");
$rootPdo->exec("DELETE FROM system_integration_logs WHERE source_system_id = 'SYS11' OR target_system_id = 'ALL'");

// Reset id_counters
$rootPdo->exec("
    INSERT INTO id_counters (name, next_val) VALUES
    ('customers', 1011),
    ('projects', 16),
    ('invoices', 11),
    ('documents', 16),
    ('tickets', 16),
    ('orders', 1001),
    ('employees', 1096),
    ('products', 1011),
    ('ops_tasks', 1),
    ('leads', 100)
    ON DUPLICATE KEY UPDATE next_val = VALUES(next_val)
");

// Purge duplicate documents if any
$rootPdo->exec("
    DELETE d1 FROM documents d1
    INNER JOIN documents d2
    WHERE d1.doc_id > d2.doc_id
      AND d1.file_name = d2.file_name
");

$rootPdo->exec("SET FOREIGN_KEY_CHECKS = 1");

// 8. Verify exact baseline counts
echo "\n[6/6] Verifying baseline counts against locked enterprise targets...\n";
$empCount  = (int)$rootPdo->query("SELECT COUNT(*) FROM employees WHERE is_system_account = 0 OR is_system_account IS NULL")->fetchColumn();
$sysEmp    = (int)$rootPdo->query("SELECT COUNT(*) FROM employees WHERE is_system_account = 1")->fetchColumn();
$deptCount = (int)$rootPdo->query("SELECT COUNT(*) FROM departments WHERE dept_code IN ('EXE','SAL','OPS','ENG','FIN','HRA','ITD','GOV')")->fetchColumn();
$cusCount  = (int)$rootPdo->query("SELECT COUNT(*) FROM customers WHERE cus_id BETWEEN 'CUS-1001' AND 'CUS-1010'")->fetchColumn();
$prjCount  = (int)$rootPdo->query("SELECT COUNT(*) FROM projects WHERE prj_id BETWEEN 'PRJ-2026-001' AND 'PRJ-2026-015'")->fetchColumn();
$invCount  = (int)$rootPdo->query("SELECT COUNT(*) FROM invoices WHERE inv_id BETWEEN 'INV-2026-001' AND 'INV-2026-010'")->fetchColumn();
$tktCount  = (int)$rootPdo->query("SELECT COUNT(*) FROM tickets WHERE tkt_id BETWEEN 'TKT-2026-001' AND 'TKT-2026-015'")->fetchColumn();
$docCount  = (int)$rootPdo->query("SELECT COUNT(*) FROM documents WHERE doc_id BETWEEN 'DOC-2026-001' AND 'DOC-2026-015'")->fetchColumn();
$prodCount = (int)$rootPdo->query("SELECT COUNT(*) FROM products WHERE prod_id BETWEEN 'PROD-1001' AND 'PROD-1010'")->fetchColumn();

printf("\n%-20s | %-10s | %-10s | %s\n", "ENTITY", "TARGET", "ACTUAL", "STATUS");
echo str_repeat("-", 55) . "\n";
$checks = [
    ['Employees (Staff)', 95, $empCount],
    ['System Account',    1,  $sysEmp],
    ['Departments',       8,  $deptCount],
    ['Customers',        10,  $cusCount],
    ['Projects',         15,  $prjCount],
    ['Invoices',         10,  $invCount],
    ['Tickets',          15,  $tktCount],
    ['Documents',        15,  $docCount],
    ['Products',         10,  $prodCount],
];

$allValid = true;
foreach ($checks as [$name, $target, $actual]) {
    $ok = ($target === $actual);
    if (!$ok) $allValid = false;
    printf("%-20s | %-10d | %-10d | %s\n", $name, $target, $actual, $ok ? "PASS" : "FAIL");
}

echo str_repeat("=", 55) . "\n";
if ($allValid) {
    echo "SUCCESS: Database build completed with 100% baseline accuracy.\n";
    exit(0);
} else {
    echo "FAILURE: Database build finished with count mismatches.\n";
    exit(1);
}
