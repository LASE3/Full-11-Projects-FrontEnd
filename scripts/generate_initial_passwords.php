<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR — Initial Password Generator
 * Generates unique random initial passwords per employee and customer account.
 * Sets must_change_password = 1.
 * Exempts SuperAdmin (EMP-0001).
 * Saves initial credentials once to an uncommitted file outside the web root.
 */

require_once __DIR__ . '/../config/db.php';

$pdo = getDbConnection();

// Output file outside web root
$outputDir = 'c:/xampp';
if (!is_dir($outputDir)) {
    $outputDir = dirname(__DIR__, 2);
}
$outputFile = $outputDir . '/vostok_initial_passwords_' . date('Ymd_His') . '.txt';

echo "Generating unique initial passwords...\n";

$pdo->beginTransaction();
$logLines = [];
$logLines[] = "# VOSTOKPRIBOR Initial Generated Passwords - " . date('Y-m-d H:i:s');
$logLines[] = "# DO NOT COMMIT THIS FILE. KEEP CONFIDENTIAL.";
$logLines[] = str_repeat('-', 70);

// 1. Employee Accounts (excluding SuperAdmin EMP-0001)
$stmt = $pdo->query("SELECT account_id, emp_id, username FROM employee_accounts WHERE emp_id != 'EMP-0001' ORDER BY account_id ASC");
$empAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$updEmp = $pdo->prepare("UPDATE employee_accounts SET password_hash = ?, must_change_password = 1 WHERE account_id = ?");

foreach ($empAccounts as $acc) {
    // Generate unique random 16-char password
    $initialPass = 'Vp#' . bin2hex(random_bytes(5)) . '!' . rand(10, 99);
    $hash = password_hash($initialPass, PASSWORD_BCRYPT, ['cost' => 10]);
    $updEmp->execute([$hash, $acc['account_id']]);
    $logLines[] = sprintf("EMPLOYEE | %-10s | %-20s | %s", $acc['emp_id'], $acc['username'], $initialPass);
}

// 2. Customer Accounts
$stmtCus = $pdo->query("SELECT account_id, cus_id, username FROM customer_accounts ORDER BY account_id ASC");
$cusAccounts = $stmtCus->fetchAll(PDO::FETCH_ASSOC);

$updCus = $pdo->prepare("UPDATE customer_accounts SET password_hash = ?, must_change_password = 1 WHERE account_id = ?");

foreach ($cusAccounts as $acc) {
    $initialPass = 'Cus#' . bin2hex(random_bytes(5)) . '!' . rand(10, 99);
    $hash = password_hash($initialPass, PASSWORD_BCRYPT, ['cost' => 10]);
    $updCus->execute([$hash, $acc['account_id']]);
    $logLines[] = sprintf("CUSTOMER | %-10s | %-20s | %s", $acc['cus_id'], $acc['username'], $initialPass);
}

// Ensure SuperAdmin remains exempt
$pdo->exec("UPDATE employee_accounts SET must_change_password = 0 WHERE emp_id = 'EMP-0001'");

$pdo->commit();

file_put_contents($outputFile, implode(PHP_EOL, $logLines) . PHP_EOL);
echo "Generated unique passwords for " . count($empAccounts) . " employees and " . count($cusAccounts) . " customers.\n";
echo "Credentials written to: {$outputFile}\n";
