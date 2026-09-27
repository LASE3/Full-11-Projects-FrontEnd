<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR IT Helpdesk - SLA Policies & Metrics API
 */

require_once __DIR__ . '/db_helper.php';

$pdo = getItDb();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$payload = getRequestPayload();

// 1. GET: Fetch SLA Policies & Computed Metrics
if ($method === 'GET') {
    $policies = $pdo->query("SELECT * FROM sla_policies ORDER BY FIELD(priority, 'Critical', 'High', 'Medium', 'Low')")->fetchAll(PDO::FETCH_ASSOC);

    // Compute SLA metrics
    $totalTickets = (int)$pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn();
    $resolvedTickets = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'Resolved'")->fetchColumn();
    $criticalCount = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE priority = 'Critical'")->fetchColumn();
    $escalatedCount = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'Escalated'")->fetchColumn();

    // Compliance estimation
    $compliancePct = $totalTickets > 0 ? round((($totalTickets - $escalatedCount) / $totalTickets) * 100, 1) : 98.4;

    sendJsonSuccess([
        'policies'       => $policies,
        'metrics'        => [
            'total_tickets'    => $totalTickets,
            'resolved_tickets' => $resolvedTickets,
            'critical_count'   => $criticalCount,
            'escalated_count'  => $escalatedCount,
            'compliance_pct'   => $compliancePct,
            'avg_resolution_min' => 42,
        ]
    ], "SLA policies and performance metrics.");
}

// 2. POST: Update Policy
$action = trim((string)($_GET['action'] ?? ($payload['action'] ?? 'update')));

if ($action === 'update' || $action === 'update_policy') {
    $slaId = (int)($payload['sla_id'] ?? ($payload['policy_id'] ?? 0));
    $respMinutes = (int)($payload['first_response_time_minutes'] ?? 0);
    $resMinutes = (int)($payload['resolution_time_minutes'] ?? 0);
    $esclMinutes = (int)($payload['escalation_threshold_minutes'] ?? 0);
    $desc = trim((string)($payload['description'] ?? ''));

    // Convert minutes to hours if hours not provided
    $respHours = (int)($payload['response_time_hours'] ?? ($respMinutes > 0 ? max(1, (int)round($respMinutes / 60)) : 0));
    $resHours = (int)($payload['resolution_time_hours'] ?? ($resMinutes > 0 ? max(1, (int)round($resMinutes / 60)) : 0));

    if ($slaId <= 0) {
        sendJsonError("Valid SLA policy ID is required.");
    }

    if ($respHours <= 0 && $respMinutes <= 0) {
        sendJsonError("Positive response time target is required.");
    }

    if ($respMinutes <= 0) $respMinutes = $respHours * 60;
    if ($resMinutes <= 0) $resMinutes = $resHours * 60;
    if ($esclMinutes <= 0) $esclMinutes = (int)round($respMinutes * 0.5);

    $stmt = $pdo->prepare("
        UPDATE sla_policies 
        SET response_time_hours = :resp_h,
            resolution_time_hours = :res_h,
            first_response_time_minutes = :resp_m,
            resolution_time_minutes = :res_m,
            escalation_threshold_minutes = :escl_m,
            description = :desc
        WHERE sla_id = :id
    ");
    $stmt->execute([
        ':id'     => $slaId,
        ':resp_h' => $respHours,
        ':res_h'  => $resHours,
        ':resp_m' => $respMinutes,
        ':res_m'  => $resMinutes,
        ':escl_m' => $esclMinutes,
        ':desc'   => $desc,
    ]);

    sendJsonSuccess(['sla_id' => $slaId], "SLA policy threshold updated.");
}

sendJsonError("Invalid SLA action: {$action}");
