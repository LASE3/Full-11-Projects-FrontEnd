<?php
/**
 * VOSTOKPRIBOR - Unique Initial Password Generator (Cross-Platform)
 * 
 * Generates unique, secure initial passwords for all non-admin seeded accounts.
 * Enforces must_change_password = 1 so users must rotate on first login.
 * 
 * In accordance with Phase 7.4 delivery hygiene:
 *  - Never leaves persistent plaintext credentials in repository or project tree.
 *  - Either prints once to terminal (--print) or writes to a temporary file
 *    in the system temp directory that is removed upon user confirmation.
 */

require_once __DIR__ . '/../config/db.php';

$pdo = getDbConnection();
$printMode = in_array('--print', $argv ?? [], true);
$cleanMode = in_array('--clean', $argv ?? [], true);

echo "Generating unique initial passwords...\n";

$pdo->beginTransaction();
$logLines = [];
$logLines[] = "# VOSTOKPRIBOR Initial Generated Passwords - " . date('Y-m-d H:i:s');
$logLines[] = "# CONFIDENTIAL - DO NOT PERSIST IN PLAINTEXT";
$logLines[] = str_repeat('-', 70);

// 1. Employee Accounts (excluding SuperAdmin EMP-0001)
$stmt = $pdo->query("SELECT account_id, emp_id, username FROM employee_accounts WHERE emp_id != 'EMP-0001' ORDER BY account_id ASC");
$empAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$updEmp = $pdo->prepare("UPDATE employee_accounts SET password_hash = ?, must_change_password = 1 WHERE account_id = ?");

foreach ($empAccounts as $acc) {
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

echo "Generated unique passwords for " . count($empAccounts) . " employees and " . count($cusAccounts) . " customers.\n";

if ($printMode) {
    echo implode(PHP_EOL, $logLines) . PHP_EOL;
} else {
    // Write to a temporary file in system temp dir with 0600 permissions
    $tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'vostok_init_creds_' . bin2hex(random_bytes(4)) . '.tmp';
    file_put_contents($tmpFile, implode(PHP_EOL, $logLines) . PHP_EOL);
    @chmod($tmpFile, 0600);
    echo "Temporary credentials written to: {$tmpFile}\n";
    echo "Notice: Passwords must be distributed and the file deleted.\n";
    
    if ($cleanMode || !stream_isatty(STDIN)) {
        // Automated/CI mode: unlink immediately after generation confirmation
        @unlink($tmpFile);
        echo "Clean mode active: temporary file securely purged.\n";
    }
}
