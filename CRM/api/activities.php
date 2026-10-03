<?php

declare(strict_types=1);

/**
 * Class 5: CRM Platform - Commercial Activities API
 * Location: CRM/api/activities.php
 * Methods: GET, POST
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('CRM', []);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$empId = $_SESSION['emp_id'] ?? ($_SESSION['vostok_user']['emp_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? 'EMP-1006'));

$rawInput = file_get_contents('php://input');
$jsonBody = [];
if ($rawInput !== false && trim($rawInput) !== '') {
    $decoded = json_decode($rawInput, true);
    if (is_array($decoded)) {
        $jsonBody = $decoded;
    }
}
$data = array_merge(is_array($_GET) ? $_GET : [], is_array($_POST) ? $_POST : [], $jsonBody);

if ($method === 'GET') {
    try {
        $stmt = $pdo->query("
            SELECT 
                a.*,
                c.company_name,
                e.full_name AS author_name
            FROM `crm_activities` a
            LEFT JOIN `customers` c ON a.cus_id = c.cus_id
            LEFT JOIN `employees` e ON a.emp_id = e.emp_id
            ORDER BY a.activity_id DESC
            LIMIT 30
        ");
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        Response::success($activities, 'Activities stream loaded from DB');
    } catch (Throwable $e) {
        Response::error("Failed to load activities: " . $e->getMessage(), 500);
    }
}

if ($method === 'POST') {
    $title = trim((string)($data['title'] ?? 'Commercial Meeting / Audit'));
    $type  = trim((string)($data['activity_type'] ?? 'Meeting'));
    $desc  = trim((string)($data['description'] ?? ($data['notes'] ?? '')));
    $cusId = trim((string)($data['cus_id'] ?? ''));

    if ($cusId === '' && !empty($data['client'])) {
        $m = $pdo->prepare("SELECT cus_id FROM `customers` WHERE company_name LIKE :cn LIMIT 1");
        $m->execute([':cn' => "%{$data['client']}%"]);
        $cusId = (string)$m->fetchColumn();
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO `crm_activities` (activity_type, title, description, cus_id, emp_id)
            VALUES (:type, :title, :desc, :cid, :eid)
        ");
        $stmt->execute([
            ':type'  => $type,
            ':title' => $title,
            ':desc'  => $desc,
            ':cid'   => $cusId ?: null,
            ':eid'   => $empId
        ]);
        $actId = (int)$pdo->lastInsertId();

        AuditLogger::logAction($empId, $cusId, 'CRM Platform', 'CRM', 'LOG_COMMERCIAL_ACTIVITY', 'crm_activities', (string)$actId, ['title' => $title, 'type' => $type], 'SUCCESS');

        Response::success(['activity_id' => $actId], 'Commercial event logged to DB', 201);
    } catch (Throwable $e) {
        Response::error("Failed to log activity: " . $e->getMessage(), 500);
    }
}
