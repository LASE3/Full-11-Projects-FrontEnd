<?php

declare(strict_types=1);

/**
 * Universal Enterprise Notification API
 * Serves real-time telemetry alerts and notifications from MySQL database across all 11 systems.
 * Location: api/notifications.php
 */

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');

require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();

// Check if table portal_notifications exists; if not, create it
$pdo->exec("
    CREATE TABLE IF NOT EXISTS portal_notifications (
        notification_id INT AUTO_INCREMENT PRIMARY KEY,
        portal_user_id INT NULL,
        system_code VARCHAR(20) DEFAULT 'ALL',
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        related_entity_type VARCHAR(50) DEFAULT 'System',
        related_entity_id VARCHAR(50) NULL,
        is_read TINYINT(1) DEFAULT 0,
        severity VARCHAR(20) DEFAULT 'info',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Check if system_code / title / severity columns exist (in case table was from an older migration)
try {
    $cols = $pdo->query("SHOW COLUMNS FROM portal_notifications")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('title', $cols, true)) {
        $pdo->exec("ALTER TABLE portal_notifications ADD COLUMN title VARCHAR(255) NOT NULL AFTER portal_user_id");
    }
    if (!in_array('system_code', $cols, true)) {
        $pdo->exec("ALTER TABLE portal_notifications ADD COLUMN system_code VARCHAR(20) DEFAULT 'ALL' AFTER portal_user_id");
    }
    if (!in_array('severity', $cols, true)) {
        $pdo->exec("ALTER TABLE portal_notifications ADD COLUMN severity VARCHAR(20) DEFAULT 'info' AFTER is_read");
    }
} catch (Exception $e) {
    // Ignore schema update errors
}

// Seed realistic enterprise notifications if table has fewer than 5 rows
$count = (int)$pdo->query("SELECT COUNT(*) FROM portal_notifications")->fetchColumn();
if ($count < 5) {
    $seedNotifications = [
        [
            'system_code' => 'CRM',
            'title' => 'Opportunity Won: Severstal PJSC',
            'message' => 'Deal #OPP-2026-089 valued at $2,450,000 transitioned to Closed / Won in Sales Pipeline.',
            'related_entity_type' => 'Opportunity',
            'related_entity_id' => 'OPP-2026-089',
            'severity' => 'success',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-15 minutes'))
        ],
        [
            'system_code' => 'IT',
            'title' => 'Critical Incident: Gateway VP-GW-09',
            'message' => 'High telemetry jitter detected on Almaty Station Ingestion Bridge. Auto-failover engaged.',
            'related_entity_type' => 'Incident',
            'related_entity_id' => 'INC-2026-104',
            'severity' => 'warning',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-42 minutes'))
        ],
        [
            'system_code' => 'CUS',
            'title' => 'Dispatched: Freight Manifest #ORD-2026-104',
            'message' => 'Bulk optical pyrometer shipment cleared customs at Karaganda Hub and is in transit.',
            'related_entity_type' => 'Order',
            'related_entity_id' => 'ORD-2026-104',
            'severity' => 'info',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
        ],
        [
            'system_code' => 'HR',
            'title' => 'L4 Security Clearance Approved',
            'message' => 'Dossier vetting completed for Dr. Jonas Richter (ENG-1020). Clearance active for DEFCON-4 matrices.',
            'related_entity_type' => 'Employee',
            'related_entity_id' => 'EMP-1020',
            'severity' => 'success',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours'))
        ],
        [
            'system_code' => 'DOC',
            'title' => 'File Center Signoff Required',
            'message' => 'Optical Calibration Standard v4.2 requires cryptographic approval signature from Lead Custodian.',
            'related_entity_type' => 'Document',
            'related_entity_id' => 'DOC-2026-001',
            'severity' => 'warning',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 hours'))
        ],
        [
            'system_code' => 'EMP',
            'title' => 'Directive Published: Metrotec-900',
            'message' => 'Elena Morozova (CTO) released new industrial metrology SOP guidelines in Document Vault.',
            'related_entity_type' => 'Announcement',
            'related_entity_id' => 'ANN-03',
            'severity' => 'info',
            'is_read' => 1,
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ]
    ];

    $insertStmt = $pdo->prepare("
        INSERT INTO portal_notifications 
        (portal_user_id, system_code, title, message, related_entity_type, related_entity_id, is_read, severity, created_at)
        VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($seedNotifications as $sn) {
        $insertStmt->execute([
            $sn['system_code'],
            $sn['title'],
            $sn['message'],
            $sn['related_entity_type'],
            $sn['related_entity_id'],
            $sn['is_read'],
            $sn['severity'],
            $sn['created_at']
        ]);
    }
}

// Handle Mark All Read action
$action = $_GET['action'] ?? ($_POST['action'] ?? '');
if ($action === 'mark_all_read') {
    $pdo->query("UPDATE portal_notifications SET is_read = 1");
    echo json_encode([
        'success' => true,
        'message' => 'All notifications marked as read',
        'unread_count' => 0
    ]);
    exit;
}

// Fetch unread count
$unreadCount = (int)$pdo->query("SELECT COUNT(*) FROM portal_notifications WHERE is_read = 0")->fetchColumn();

// Fetch latest notifications (max 15)
$stmt = $pdo->query("
    SELECT notification_id, system_code, title, message, related_entity_type, related_entity_id, is_read, severity, created_at
    FROM portal_notifications
    ORDER BY created_at DESC
    LIMIT 15
");
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Map human readable time elapsed
foreach ($notifications as &$n) {
    $ts = strtotime($n['created_at'] ?? 'now');
    $diff = time() - $ts;
    if ($diff < 60) {
        $n['time_ago'] = 'Just now';
    } elseif ($diff < 3600) {
        $n['time_ago'] = floor($diff / 60) . 'm ago';
    } elseif ($diff < 86400) {
        $n['time_ago'] = floor($diff / 3600) . 'h ago';
    } else {
        $n['time_ago'] = floor($diff / 86400) . 'd ago';
    }
    
    // Fallback title if empty
    if (empty($n['title'])) {
        $n['title'] = ($n['related_entity_type'] ?? 'System') . ' Alert';
    }
}
unset($n);

echo json_encode([
    'success' => true,
    'unread_count' => $unreadCount,
    'notifications' => $notifications
]);

