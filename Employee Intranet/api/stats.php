<?php

declare(strict_types=1);

/**
 * Class 4: Employee Intranet - Live System Statistics & Badges API
 * Location: Employee Intranet/api/stats.php
 */

require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('EMP', []);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';

$pdo = getDbConnection();
$currentEmpId = $_SESSION['emp_id'] ?? ($_SESSION['vostok_user']['emp_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? 'EMP-1004'));

try {
    $empCount = (int)$pdo->query("SELECT COUNT(*) FROM employees WHERE employment_status = 'Active'")->fetchColumn();
    $policiesCount = (int)$pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn();
    $announcementsCount = (int)$pdo->query("SELECT COUNT(*) FROM announcements")->fetchColumn();
    $pendingLeavesCount = (int)$pdo->query("SELECT COUNT(*) FROM leave_requests WHERE status = 'Pending'")->fetchColumn();
    
    $myPendingStmt = $pdo->prepare("SELECT COUNT(*) FROM leave_requests WHERE emp_id = ? AND status = 'Pending'");
    $myPendingStmt->execute([$currentEmpId]);
    $myPendingLeaves = (int)$myPendingStmt->fetchColumn();

    $stats = [
        'employees'          => $empCount,
        'policies'           => $policiesCount,
        'announcements'      => $announcementsCount,
        'pending_leaves'     => $pendingLeavesCount,
        'my_pending_leaves'  => $myPendingLeaves
    ];

    Response::success($stats, "Live intranet badges loaded");
} catch (Exception $e) {
    Response::error("Failed to load statistics: " . $e->getMessage(), 500);
}
