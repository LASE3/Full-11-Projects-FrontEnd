<?php

/**
 * Class 5: CRM Platform - Dashboard Metrics API
 * Location: CRM/api/dashboard.php
 * Methods: GET
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('CRM', []);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/I18n.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$lang = $_GET['lang'] ?? 'en';

try {
    // 1. KPI Counts
    $leadCount = (int)$pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
    $qualifiedThisWeek = (int)$pdo->query("
        SELECT COUNT(*) FROM leads 
        WHERE status = 'Qualified' 
        AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ")->fetchColumn();

    $oppCount = (int)$pdo->query("
        SELECT COUNT(*) FROM opportunities 
        WHERE stage NOT IN ('Closed Won', 'Closed Lost')
    ")->fetchColumn();

    $lateNegotiation = (int)$pdo->query("
        SELECT COUNT(*) FROM opportunities 
        WHERE stage = 'Negotiation'
    ")->fetchColumn();

    $pipelineVal = (float)$pdo->query("
        SELECT COALESCE(SUM(estimated_value), 0) FROM opportunities 
        WHERE stage NOT IN ('Closed Won', 'Closed Lost')
    ")->fetchColumn();

    // Weighted pipeline based on stage probabilities
    $stageWeights = [
        'Prospecting' => 0.10,
        'Qualification' => 0.25,
        'Technical Review' => 0.50,
        'Proposal Sent' => 0.70,
        'Negotiation' => 0.85
    ];
    $weightedVal = 0.0;
    $oppRows = $pdo->query("
        SELECT stage, estimated_value FROM opportunities 
        WHERE stage NOT IN ('Closed Won', 'Closed Lost')
    ")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($oppRows as $row) {
        $st = $row['stage'];
        $w = $stageWeights[$st] ?? 0.5;
        $weightedVal += (float)$row['estimated_value'] * $w;
    }

    $wonCount = (int)$pdo->query("SELECT COUNT(*) FROM opportunities WHERE stage = 'Closed Won'")->fetchColumn();
    $closedTotal = (int)$pdo->query("SELECT COUNT(*) FROM opportunities WHERE stage IN ('Closed Won', 'Closed Lost')")->fetchColumn();
    $winRate = $closedTotal > 0 ? round(($wonCount / $closedTotal) * 100, 1) : 0;

    $custCount = (int)$pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();

    // 2. Funnel Stages
    $allStages = ['Prospecting', 'Qualification', 'Technical Review', 'Proposal Sent', 'Negotiation'];
    $funnelStmt = $pdo->query("
        SELECT stage, COUNT(*) AS cnt, COALESCE(SUM(estimated_value), 0) AS val 
        FROM opportunities 
        WHERE stage NOT IN ('Closed Won', 'Closed Lost')
        GROUP BY stage
    ");
    $stageStats = [];
    while ($r = $funnelStmt->fetch(PDO::FETCH_ASSOC)) {
        $stageStats[$r['stage']] = ['count' => (int)$r['cnt'], 'total_val' => (float)$r['val']];
    }

    $funnelStages = [];
    $prevCount = null;
    foreach ($allStages as $idx => $st) {
        $cnt = $stageStats[$st]['count'] ?? 0;
        $val = $stageStats[$st]['total_val'] ?? 0.0;
        $conv = '—';
        if ($idx === 0) {
            $conv = '100%';
        } elseif ($prevCount !== null && $prevCount > 0) {
            $conv = round(($cnt / $prevCount) * 100) . '%';
        }
        if ($cnt > 0) {
            $prevCount = $cnt;
        }
        $funnelStages[] = [
            'stage' => $st,
            'count' => $cnt,
            'total_val' => $val,
            'conversion_rate' => $conv
        ];
    }

    // 3. Top Accounts
    $topAccStmt = $pdo->query("
        SELECT 
            c.cus_id,
            c.company_name,
            c.sector AS industry,
            'Strategic Tier-1' AS account_tier,
            COALESCE((SELECT SUM(contract_value) FROM contracts WHERE cus_id = c.cus_id), 0) AS total_contract_value,
            (SELECT COUNT(*) FROM opportunities WHERE cus_id = c.cus_id AND stage NOT IN ('Closed Won', 'Closed Lost')) AS open_opps
        FROM customers c
        ORDER BY total_contract_value DESC, c.company_name ASC
        LIMIT 5
    ");
    $topAccounts = $topAccStmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Recent Activities (from audit_logs or fallback)
    $acts = [];
    try {
        $auditStmt = $pdo->query("
            SELECT action AS activity_type, record_id AS title, details AS notes, created_at AS activity_date, 'System' AS company_name
            FROM audit_logs
            ORDER BY created_at DESC
            LIMIT 5
        ");
        $acts = $auditStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $ae) {
        $acts = [];
    }

    $data = [
        'lead_count' => $leadCount,
        'qualified_this_week' => $qualifiedThisWeek,
        'opp_count' => $oppCount,
        'late_negotiation' => $lateNegotiation,
        'pipeline_value' => $pipelineVal,
        'weighted_pipeline' => round($weightedVal, 2),
        'customer_count' => $custCount,
        'win_rate' => $winRate,
        'funnel_stages' => $funnelStages,
        'top_accounts' => $topAccounts,
        'recent_activities' => $acts
    ];

    Response::success($data, "CRM dashboard metrics loaded");
} catch (Throwable $e) {
    Response::error("Failed to load CRM dashboard: " . $e->getMessage(), 500);
}
