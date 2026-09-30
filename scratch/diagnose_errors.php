<?php
require_once __DIR__ . '/../config/db.php';
$pdo = getDbConnection();

echo "=== TABLES IN DATABASE ===\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) {
    echo "- $t\n";
}

echo "\n=== CHECK SPECIFIC TABLES AND COLUMNS ===\n";

function describeTable($pdo, $table) {
    echo "\n--- Columns of $table ---\n";
    try {
        $cols = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $c) {
            echo "  {$c['Field']} ({$c['Type']}) Null={$c['Null']} Key={$c['Key']} Default={$c['Default']}\n";
        }
    } catch (Exception $e) {
        echo "  ERROR: " . $e->getMessage() . "\n";
    }
}

describeTable($pdo, 'sales_forecasts');
describeTable($pdo, 'employees');
describeTable($pdo, 'invoices');
describeTable($pdo, 'documents');
describeTable($pdo, 'security_events');
describeTable($pdo, 'customers');
describeTable($pdo, 'customer_accounts');
describeTable($pdo, 'crm_activities');
describeTable($pdo, 'quotes');
describeTable($pdo, 'contracts');
describeTable($pdo, 'projects');
