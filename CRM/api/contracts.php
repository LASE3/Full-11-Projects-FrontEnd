<?php

declare(strict_types=1);

/**
 * Class 5: CRM Platform - Contracts API
 * Location: CRM/api/contracts.php
 * Methods: GET, POST, PUT, PATCH, DELETE
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/I18n.php';
require_once __DIR__ . '/helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$lang = strtolower(substr($_GET['lang'] ?? $_COOKIE['vp_lang'] ?? 'en', 0, 2)) === 'ar' ? 'ar' : 'en';
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
                con.*,
                c.company_name,
                c.sector,
                p.project_name
            FROM `contracts` con
            LEFT JOIN `customers` c ON con.cus_id = c.cus_id
            LEFT JOIN `projects` p ON con.prj_id = p.prj_id
            ORDER BY con.contract_value DESC
        ");
        $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($contracts as &$c) {
            $c['status_display'] = I18n::translate((string)$c['status'], $lang);
        }
        unset($c);

        $totalActiveArr = (float)$pdo->query("SELECT COALESCE(SUM(contract_value), 0) FROM `contracts` WHERE status = 'Active'")->fetchColumn();

        Response::success($contracts, 'Contracts loaded from DB', 200, [
            'total_contracts'  => count($contracts),
            'total_active_arr' => $totalActiveArr
        ]);
    } catch (Throwable $e) {
        Response::error("Failed to load contracts: " . $e->getMessage(), 500);
    }
}

if ($method === 'POST') {
    $action = strtolower(trim((string)($data['action'] ?? '')));
    if ($action === 'delete' || $action === 'delete_contract') {
        goto handle_delete_contract;
    }

    $title   = trim((string)($data['title'] ?? 'Master Services Agreement'));
    $cusId   = trim((string)($data['cus_id'] ?? 'CUS-1001'));
    $val     = (float)($data['contract_value'] ?? 1000000);
    $ref     = trim((string)($data['contract_ref'] ?? ('MSA-2026-' . strtoupper(substr(md5((string)time()), 0, 6)))));
    $type    = trim((string)($data['contract_type'] ?? 'MSA'));
    $eds     = trim((string)($data['eds_status'] ?? 'Counter-Signed'));
    $start   = trim((string)($data['start_date'] ?? date('Y-m-d')));
    $end     = trim((string)($data['end_date'] ?? date('Y-m-d', strtotime('+2 years'))));

    try {
        $stmt = $pdo->prepare("
            INSERT INTO `contracts` (
                contract_ref, title, cus_id, contract_value, start_date, end_date,
                status, contract_type, eds_status, confidentiality_level
            ) VALUES (
                :ref, :title, :cid, :val, :s, :e,
                'Active', :type, :eds, 'Restricted'
            )
        ");
        $stmt->execute([
            ':ref'   => $ref,
            ':title' => $title,
            ':cid'   => $cusId,
            ':val'   => $val,
            ':s'     => $start,
            ':e'     => $end,
            ':type'  => $type,
            ':eds'   => $eds
        ]);
        $conId = (int)$pdo->lastInsertId();

        AuditLogger::logAction($empId, $cusId, 'CRM Platform', 'CRM', 'CREATE_CONTRACT', 'contracts', (string)$conId, ['ref' => $ref, 'value' => $val], 'SUCCESS');

        $pdo->prepare("INSERT INTO `crm_activities` (activity_type, title, description, cus_id, emp_id) VALUES ('Contract', 'Contract Ratified & Executed', :desc, :cid, :eid)")
            ->execute([
                ':desc' => "New contract {$ref} ({$title}) valued at $" . number_format($val, 2) . " executed in DB.",
                ':cid'  => $cusId,
                ':eid'  => $empId
            ]);

        Response::success(['contract_id' => $conId, 'contract_ref' => $ref], 'Contract saved to DB', 201);
    } catch (Throwable $e) {
        Response::error("Failed to create contract: " . $e->getMessage(), 500);
    }
}

if ($method === 'PUT' || $method === 'PATCH' || ($method === 'POST' && in_array(strtolower(trim((string)($data['action'] ?? ''))), ['edit', 'update', 'update_contract', 'edit_contract'], true))) {
    $conId = (int)($data['contract_id'] ?? ($data['id'] ?? 0));
    if ($conId <= 0) {
        Response::error("Contract ID required for update", 422);
    }

    try {
        $fields = [];
        $params = [':id' => $conId];

        if (isset($data['title']) && trim($data['title']) !== '') {
            $fields[] = "`title` = :title";
            $params[':title'] = trim($data['title']);
        }
        if (isset($data['contract_value'])) {
            $fields[] = "`contract_value` = :val";
            $params[':val'] = (float)$data['contract_value'];
        }
        if (isset($data['status'])) {
            $fields[] = "`status` = :st";
            $params[':st'] = trim($data['status']);
        }
        if (isset($data['eds_status'])) {
            $fields[] = "`eds_status` = :eds";
            $params[':eds'] = trim($data['eds_status']);
        }

        if (empty($fields)) {
            Response::error("No fields provided for update", 422);
        }

        $sql = "UPDATE `contracts` SET " . implode(', ', $fields) . " WHERE `contract_id` = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        AuditLogger::logAction($empId, null, 'CRM Platform', 'CRM', 'UPDATE_CONTRACT', 'contracts', (string)$conId, $params, 'SUCCESS');
        Response::success(['contract_id' => $conId], 'Contract updated in DB');
    } catch (Throwable $e) {
        Response::error("Failed to update contract: " . $e->getMessage(), 500);
    }
}

if ($method === 'DELETE') {
    handle_delete_contract:
    $conId = (int)($data['id'] ?? ($data['contract_id'] ?? 0));
    if ($conId <= 0) {
        Response::error("Contract ID required for deletion", 422);
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM `contracts` WHERE contract_id = ?");
        $stmt->execute([$conId]);

        AuditLogger::logAction($empId, null, 'CRM Platform', 'CRM', 'DELETE_CONTRACT', 'contracts', (string)$conId, [], 'SUCCESS');

        Response::success(['contract_id' => $conId], 'Contract deleted successfully');
    } catch (Throwable $e) {
        Response::error("Failed to delete contract: " . $e->getMessage(), 500);
    }
}

