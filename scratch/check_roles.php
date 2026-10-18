<?php
require 'config/db.php';
$pdo = getDbConnection();
foreach ($pdo->query("SELECT * FROM roles") as $r) {
    echo json_encode($r) . PHP_EOL;
}
