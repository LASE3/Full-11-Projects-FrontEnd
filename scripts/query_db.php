<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

$action = $argv[1] ?? 'query';
$sql = $argv[2] ?? '';

if ($sql === '') {
    echo json_encode(['error' => 'No SQL provided']);
    exit(1);
}

try {
    $pdo = getDbConnection();
    if ($action === 'exec') {
        $count = $pdo->exec($sql);
        echo json_encode(['affected' => $count]);
    } else {
        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($rows);
    }
} catch (Throwable $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit(1);
}
