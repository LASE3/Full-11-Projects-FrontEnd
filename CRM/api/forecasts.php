<?php

/**
 * Class 5: CRM Platform - Sales Forecasts API
 * Location: CRM/api/forecasts.php
 * Methods: GET, POST
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('CRM', []);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    try {
        $stmt = $pdo->query("
            SELECT 
                sf.forecast_id,
                sf.period,
                sf.forecast_amount,
                sf.actual_amount,
                e.emp_id AS sales_emp_id,
                e.full_name AS sales_representative
            FROM sales_forecasts sf
            LEFT JOIN employees e ON sf.sales_emp_id = e.emp_id
            ORDER BY sf.period ASC, sf.forecast_id ASC
        ");
        $forecasts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        Response::success($forecasts, "Sales forecasts loaded");
    } catch (Exception $e) {
        Response::error("Failed to load forecasts: " . $e->getMessage(), 500);
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $empId    = trim($_SESSION['emp_id'] ?? ($_SESSION['vostok_user']['emp_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? ($data['sales_emp_id'] ?? ''))));
    $period   = trim($data['period'] ?? '');
    $forecast = (float)($data['forecast_amount'] ?? 0.0);
    $actual   = isset($data['actual_amount']) ? (float)$data['actual_amount'] : null;

    if (empty($empId)) {
        Response::error("Authenticated employee session required (sales_emp_id missing).", 401);
    }

    if (empty($period) || $forecast <= 0) {
        Response::error("Period and forecast amount required.", 422);
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO sales_forecasts (sales_emp_id, period, forecast_amount, actual_amount)
            VALUES (:eid, :prd, :fc, :act)
        ");
        $stmt->execute([
            ':eid' => $empId,
            ':prd' => $period,
            ':fc'  => $forecast,
            ':act' => $actual
        ]);

        Response::success(['forecast_id' => (int)$pdo->lastInsertId()], "Sales forecast created", 201);
    } catch (Exception $e) {
        Response::error("Failed to save forecast: " . $e->getMessage(), 500);
    }
}
