<?php
/**
 * Class 5: CRM Platform - Opportunities Pipeline API
 * Location: api/v1/crm/opportunities.php
 * Methods: GET, PATCH
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/I18n.php';
require_once __DIR__ . '/../../helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$lang = $_GET['lang'] ?? 'en';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

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

        Response::success($opportunities, "Opportunities pipeline loaded", 200, [
            'total_deals'    => count($opportunities),
            'pipeline_value' => $pipelineValue
        ]);
    } catch (Exception $e) {
        Response::error("Failed to load opportunities: " . $e->getMessage(), 500);
    }
}

if ($method === 'PATCH') {
    $data = json_decode(file_get_contents('php://input'), true);
    $oppId = (int)($data['opp_id'] ?? 0);
    $newStage = trim($data['stage'] ?? '');
    $empId = $_SESSION['emp_id'] ?? 'EMP-1006';

    $allowedStages = ['Qualification', 'Proposal', 'Negotiation', 'Won', 'Lost'];
    if (!in_array($newStage, $allowedStages, true)) {
        Response::error("Invalid pipeline stage. Allowed: " . implode(', ', $allowedStages), 422);
    }

    try {
        $stmt = $pdo->prepare("UPDATE opportunities SET stage = :st WHERE opp_id = :id");
        $stmt->execute([':st' => $newStage, ':id' => $oppId]);

        AuditLogger::logAction(
            $empId, null, 'CRM Platform', 'CRM',
            'UPDATE_OPPORTUNITY_STAGE', 'opportunities', (string)$oppId,
            ['new_stage' => $newStage], 'SUCCESS'
        );

        Response::success([
            'opp_id'        => $oppId,
            'stage'         => $newStage,
            'stage_display' => I18n::translate($newStage, $lang)
        ], "Opportunity stage updated");
    } catch (Exception $e) {
        Response::error("Failed to update opportunity: " . $e->getMessage(), 500);
    }
}
