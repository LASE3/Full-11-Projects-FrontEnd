<?php
require 'config/db.php';
$pdo = getDbConnection();
$stmt = $pdo->query("SELECT emp_id, full_name, department_code, clearance_level FROM employees WHERE clearance_level = 'L1' LIMIT 5");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo json_encode($r) . PHP_EOL;
}
