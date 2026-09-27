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
$action = trim((string)($payload['action'] ?? 'update'));

if ($action === 'update') {
    $slaId = (int)($payload['sla_id'] ?? 0);
    $respHours = (int)($payload['response_time_hours'] ?? 0);
    $resHours = (int)($payload['resolution_time_hours'] ?? 0);

    if ($slaId <= 0 || $respHours <= 0 || $resHours <= 0) {
        sendJsonError("Valid SLA ID and positive hours are required.");
    }

    $stmt = $pdo->prepare("UPDATE sla_policies SET response_time_hours = :resp, resolution_time_hours = :res WHERE sla_id = :id");
    $stmt->execute([
        ':id'   => $slaId,
        ':resp' => $respHours,
        ':res'  => $resHours,
    ]);

    sendJsonSuccess(['sla_id' => $slaId], "SLA policy threshold updated.");
}

sendJsonError("Invalid SLA action: {$action}");
