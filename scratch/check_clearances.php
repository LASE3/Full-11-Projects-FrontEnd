<?php
require 'config/db.php';
$pdo = getDbConnection();
$stmt = $pdo->query("SELECT clearance_level, department_code, count(*) as cnt FROM employees GROUP BY clearance_level, department_code");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo json_encode($r) . PHP_EOL;
}
