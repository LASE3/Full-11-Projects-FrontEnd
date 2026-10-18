<?php
require 'config/db.php';
$pdo = getDbConnection();
foreach (['EMP-1017', 'EMP-1019'] as $id) {
    $s = $pdo->prepare("SELECT emp_id, full_name, department_code, clearance_level FROM employees WHERE emp_id = ?");
    $s->execute([$id]);
    echo json_encode($s->fetch(PDO::FETCH_ASSOC)) . PHP_EOL;
}
