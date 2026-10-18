<?php
require 'config/db.php';
$pdo = getDbConnection();
$s = $pdo->query("SELECT doc_id, file_name, classification, owner_emp_id, department, status, is_legal_hold FROM documents WHERE owner_emp_id = 'EMP-1019'");
foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo json_encode($row) . PHP_EOL;
}
