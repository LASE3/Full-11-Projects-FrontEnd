<?php

/**
 * Class 5: CRM Platform - Sales Forecasts API
 * Location: api/v1/crm/forecasts.php
 * Methods: GET, POST
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../helpers/Response.php';

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
    $empId    = trim($data['sales_emp_id'] ?? 'EMP-1006');
    $period   = trim($data['period'] ?? '2026-Q4');
    $forecast = (float)($data['forecast_amount'] ?? 0.0);
    $actual   = isset($data['actual_amount']) ? (float)$data['actual_amount'] : null;

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
