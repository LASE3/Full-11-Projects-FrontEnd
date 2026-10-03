<?php

declare(strict_types=1);

/**
 * Class 5: CRM Platform - Projects API
 * Location: CRM/api/projects.php
 * Methods: GET, POST
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('CRM', []);

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
                p.*,
                c.company_name,
                c.sector,
                e.full_name AS project_manager_name
            FROM `projects` p
            LEFT JOIN `customers` c ON p.cus_id = c.cus_id
            LEFT JOIN `employees` e ON p.project_manager_emp_id = e.emp_id
            ORDER BY p.budget DESC
        ");
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($projects as &$pj) {
            $pj['status_display'] = I18n::translate((string)$pj['status'], $lang);
        }
        unset($pj);

        $totalBudget = (float)array_sum(array_column($projects, 'budget'));

        Response::success($projects, 'Engineering projects loaded from DB', 200, [
            'total_projects' => count($projects),
            'total_budget'   => $totalBudget
        ]);
    } catch (Throwable $e) {
        Response::error("Failed to load projects: " . $e->getMessage(), 500);
    }
}

if ($method === 'POST') {
    $name   = trim((string)($data['project_name'] ?? ''));
    $cusId  = trim((string)($data['cus_id'] ?? 'CUS-1001'));
    $budget = (float)($data['budget'] ?? 1000000);
    $loc    = trim((string)($data['facility_location'] ?? 'Industrial Site'));
    $status = trim((string)($data['status'] ?? 'Planning'));
    $start  = trim((string)($data['start_date'] ?? date('Y-m-d')));
    $end    = trim((string)($data['end_date'] ?? date('Y-m-d', strtotime('+1 year'))));
    $pmId   = trim((string)($data['project_manager_emp_id'] ?? $empId));

    if (empty($name)) {
        Response::error("Project name is required.", 422);
    }

    try {
        $maxPrj = (int)$pdo->query("SELECT MAX(CAST(SUBSTRING(prj_id, 5) AS UNSIGNED)) FROM `projects` WHERE prj_id LIKE 'PRJ-%'")->fetchColumn();
        $newPrjId = 'PRJ-' . str_pad((string)(($maxPrj > 0 ? $maxPrj : 1000) + 1), 4, '0', STR_PAD_LEFT);

        $stmt = $pdo->prepare("
            INSERT INTO `projects` (
                prj_id, project_name, cus_id, budget, currency,
                facility_location, status, start_date, end_date, project_manager_emp_id
            ) VALUES (
                :id, :name, :cid, :b, 'USD',
                :loc, :st, :s, :e, :pm
            )
        ");
        $stmt->execute([
            ':id'   => $newPrjId,
            ':name' => $name,
            ':cid'  => $cusId,
            ':b'    => $budget,
            ':loc'  => $loc,
            ':st'   => $status,
            ':s'    => $start,
            ':e'    => $end,
            ':pm'   => $pmId
        ]);

        AuditLogger::logAction($empId, $cusId, 'CRM Platform', 'CRM', 'CREATE_PROJECT', 'projects', $newPrjId, ['name' => $name, 'budget' => $budget], 'SUCCESS');

        Response::success(['prj_id' => $newPrjId], 'Project created in DB', 201);
    } catch (Throwable $e) {
        Response::error("Failed to create project: " . $e->getMessage(), 500);
    }
}

if ($method === 'PUT' || $method === 'PATCH' || ($method === 'POST' && in_array(strtolower(trim((string)($data['action'] ?? ''))), ['edit', 'update', 'update_project', 'edit_project'], true))) {
    $prjId  = trim((string)($data['prj_id'] ?? ($data['id'] ?? '')));
    if (empty($prjId)) {
        Response::error("Project ID is required for update.", 422);
    }

    try {
        $fields = [];
        $params = [':id' => $prjId];

        if (isset($data['project_name']) && trim($data['project_name']) !== '') {
            $fields[] = "`project_name` = :pname";
            $params[':pname'] = trim($data['project_name']);
        }
        if (isset($data['budget'])) {
            $fields[] = "`budget` = :budget";
            $params[':budget'] = (float)$data['budget'];
        }
        if (isset($data['status'])) {
            $fields[] = "`status` = :status";
            $params[':status'] = trim($data['status']);
        }
        if (isset($data['facility_location'])) {
            $fields[] = "`facility_location` = :loc";
            $params[':loc'] = trim($data['facility_location']);
        }

        if (empty($fields)) {
            Response::error("No fields provided for update.", 422);
        }

        $sql = "UPDATE `projects` SET " . implode(', ', $fields) . " WHERE `prj_id` = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        AuditLogger::logAction($empId, null, 'CRM Platform', 'CRM', 'UPDATE_PROJECT', 'projects', $prjId, $params, 'SUCCESS');
        Response::success(['prj_id' => $prjId], 'Project updated in DB');
    } catch (Throwable $e) {
        Response::error("Failed to update project: " . $e->getMessage(), 500);
    }
}

if ($method === 'DELETE' || ($method === 'POST' && in_array(strtolower(trim((string)($data['action'] ?? ''))), ['delete', 'delete_project'], true))) {
    $prjId = trim((string)($data['prj_id'] ?? ($data['id'] ?? '')));
    if (empty($prjId)) {
        Response::error("Project ID is required for deletion.", 422);
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM `projects` WHERE `prj_id` = ?");
        $stmt->execute([$prjId]);

        AuditLogger::logAction($empId, null, 'CRM Platform', 'CRM', 'DELETE_PROJECT', 'projects', $prjId, [], 'SUCCESS');
        Response::success(['prj_id' => $prjId], 'Project deleted from DB');
    } catch (Throwable $e) {
        Response::error("Failed to delete project: " . $e->getMessage(), 500);
    }
}

