<?php

/**
 * Class 5: CRM Platform - Opportunities Pipeline API
 * Location: CRM/api/opportunities.php
 * Methods: GET, PATCH
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/I18n.php';
require_once __DIR__ . '/helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$lang = $_GET['lang'] ?? 'en';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$empId = $_SESSION['emp_id'] ?? ($_SESSION['vostok_user']['emp_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? null));

if ($method === 'GET') {
    $stage = $_GET['stage'] ?? null;
    $empFilter = $_GET['sales_emp_id'] ?? null;

    try {
        $sql = "
            SELECT 
                o.opp_id,
                o.stage,
                o.estimated_value,
                o.expected_close_date,
                c.cus_id,
                c.company_name,
                c.sector,
                e.emp_id AS sales_emp_id,
                e.full_name AS sales_representative
            FROM opportunities o
            LEFT JOIN customers c ON o.cus_id = c.cus_id
            LEFT JOIN employees e ON o.sales_emp_id = e.emp_id
            WHERE 1=1
        ";
        $params = [];

        if ($stage) {
            $sql .= " AND o.stage = :st";
            $params[':st'] = $stage;
        }

        if ($empFilter) {
            $sql .= " AND o.sales_emp_id = :eid";
            $params[':eid'] = $empFilter;
        }

        $sql .= " ORDER BY o.estimated_value DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $opportunities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pipelineValue = 0.0;
        foreach ($opportunities as &$opp) {
            $opp['stage_display'] = I18n::translate($opp['stage'], $lang);
            $pipelineValue += (float)$opp['estimated_value'];
        }
        unset($opp);

        Response::success($opportunities, "Opportunities pipeline loaded", 200, [
            'total_deals'    => count($opportunities),
            'pipeline_value' => $pipelineValue
        ]);
    } catch (Throwable $e) {
        Response::error("Failed to load opportunities: " . $e->getMessage(), 500);
    }
}

if ($method === 'PATCH' || $method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?: $_POST;
    $action = $_GET['action'] ?? ($data['action'] ?? '');
    if (empty($empId)) {
        Response::error("Authenticated employee session required.", 401);
    }

    $isUpdate = ($method === 'PATCH') || ($action === 'update_stage') || (!empty($data['opp_id']) && !isset($data['create']));

    if ($isUpdate) {
        $oppId = (int)($data['opp_id'] ?? $_GET['id'] ?? 0);
        $newStage = ucfirst(strtolower(trim($data['stage'] ?? '')));

        $allowedStages = ['Qualification', 'Proposal', 'Negotiation', 'Contract', 'Won', 'Lost'];
        if (!in_array($newStage, $allowedStages, true)) {
            Response::error("Invalid pipeline stage. Allowed: " . implode(', ', $allowedStages), 422);
        }

        try {
            $stmt = $pdo->prepare("UPDATE opportunities SET stage = :st WHERE opp_id = :id");
            $stmt->execute([':st' => $newStage, ':id' => $oppId]);

            AuditLogger::logAction(
                $empId,
                null,
                'CRM Platform',
                'CRM',
                'UPDATE_OPPORTUNITY_STAGE',
                'opportunities',
                (string)$oppId,
                ['new_stage' => $newStage],
                'SUCCESS'
            );

            Response::success([
                'opp_id'        => $oppId,
                'stage'         => $newStage,
                'stage_display' => I18n::translate($newStage, $lang)
            ], "Opportunity stage updated");
        } catch (Throwable $e) {
            Response::error("Failed to update opportunity: " . $e->getMessage(), 500);
        }
    } else {
        // Create new opportunity
        $cusId = trim($data['cus_id'] ?? '');
        $stage = ucfirst(strtolower(trim($data['stage'] ?? '')));
        $value = (float)($data['estimated_value'] ?? $data['value'] ?? 0.0);
        $closeDate = trim($data['expected_close_date'] ?? '');

        if (empty($cusId)) {
            Response::error("Customer ID (cus_id) is required to create an opportunity.", 422);
        }
        if ($value <= 0) {
            Response::error("Estimated value must be greater than zero.", 422);
        }
        if (empty($closeDate)) {
            $closeDate = date('Y-m-d', strtotime('+45 days'));
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO opportunities (cus_id, sales_emp_id, stage, estimated_value, expected_close_date)
                VALUES (:cid, :sid, :st, :val, :dt)
            ");
            $stmt->execute([
                ':cid' => $cusId,
                ':sid' => $empId,
                ':st'  => $stage,
                ':val' => $value,
                ':dt'  => $closeDate
            ]);
            $newOppId = (int)$pdo->lastInsertId();

            Response::success([
                'opp_id' => $newOppId,
                'stage'  => $stage,
                'stage_display' => I18n::translate($stage, $lang)
            ], "Opportunity created successfully", 201);
        } catch (Throwable $e) {
            Response::error("Failed to create opportunity: " . $e->getMessage(), 500);
        }
    }
}

if ($method === 'DELETE') {
    if (empty($empId)) {
        Response::error("Authenticated employee session required.", 401);
    }
    $oppId = (int)($_GET['id'] ?? ($_GET['opp_id'] ?? 0));
    if ($oppId <= 0) {
        Response::error("Opportunity ID required for deletion.", 422);
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM opportunities WHERE opp_id = ?");
        $stmt->execute([$oppId]);

        AuditLogger::logAction(
            $empId,
            null,
            'CRM Platform',
            'CRM',
            'DELETE_OPPORTUNITY',
            'opportunities',
            (string)$oppId,
            [],
            'SUCCESS'
        );

        Response::success(['opp_id' => $oppId], "Opportunity removed");
    } catch (Throwable $e) {
        Response::error("Failed to delete opportunity: " . $e->getMessage(), 500);
    }
}
