<?php
require 'config/db.php';
$pdo = getDbConnection();
$s = $pdo->prepare("SELECT e.*, ea.username, ea.account_id, r.role_name FROM employees e LEFT JOIN employee_accounts ea ON e.emp_id = ea.emp_id LEFT JOIN employee_roles er ON e.emp_id = er.emp_id LEFT JOIN roles r ON er.role_id = r.role_id WHERE e.emp_id = 'EMP-1020'");
$s->execute();
echo json_encode($s->fetch(PDO::FETCH_ASSOC)) . PHP_EOL;
