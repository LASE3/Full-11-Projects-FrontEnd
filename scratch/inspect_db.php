<?php
require_once __DIR__ . '/../config/db.php';
$pdo = getDbConnection();
echo "--- ALL PROJECTS IN DB ---\n";
foreach ($pdo->query("SELECT prj_id, cus_id, project_name, status, budget, progress_percent FROM projects") as $r) {
    echo "{$r['prj_id']} | {$r['cus_id']} | {$r['project_name']} | {$r['status']} | {$r['budget']} | {$r['progress_percent']}%\n";
}
