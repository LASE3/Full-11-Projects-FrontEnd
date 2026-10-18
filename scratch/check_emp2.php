<?php
require 'config/db.php';
$pdo = getDbConnection();
foreach (['EMP-1017', 'EMP-1019', 'EMP-1001', 'EMP-1002'] as $id) {
    $s = $pdo->prepare("SELECT emp_id, full_name, job_title, department_code, clearance_level, manager_emp_id FROM employees WHERE emp_id = ?");
    $s->execute([$id]);
    echo json_encode($s->fetch(PDO::FETCH_ASSOC)) . PHP_EOL;
}
