<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR DEVELOPER PORTAL MASTER API ROUTER
 * System 10 RESTful Gateway
 * 
 * Supports both dedicated endpoints (e.g. /api/endpoints.php)
 * and unified routing via /api/index.php?service=<name>&action=<action>
 */

require_once __DIR__ . '/db_helper.php';

$payload = getRequestPayload();
$service = trim($payload['service'] ?? '');

if ($service === '') {
    // If no service specified, output API directory manifest and health check
    $pdo = getDbConnection();
    ensureDeveloperTables($pdo);

    sendJsonSuccess([
        'system' => 'VOSTOKPRIBOR SYS-10 // DEVELOPER & API GATEWAY',
        'status' => 'ONLINE',
        'database' => VP_DB_NAME,
        'gateway_version' => 'v4.12.0',
        'services' => [
            'endpoints' => 'Developer/api/endpoints.php (CRUD for API reference & endpoints)',
            'credentials' => 'Developer/api/credentials.php (CRUD for API keys & vault)',
            'sandbox' => 'Developer/api/sandbox.php (CRUD for presets & live request runner)',
            'metrics' => 'Developer/api/metrics.php (Live KPIs, throughput & webhooks CRUD)',
            'partners' => 'Developer/api/partners.php (Partner applications & onboarding clearance CRUD)',
            'guides' => 'Developer/api/guides.php (Integration standards & DOC-010 specs CRUD)'
        ],
        'timestamp' => date('c')
    ], 'VOSTOKPRIBOR System 10 API Gateway Online');
}

// Forward to service handler
$allowedServices = [
    'endpoints' => __DIR__ . '/endpoints.php',
    'credentials' => __DIR__ . '/credentials.php',
    'keys' => __DIR__ . '/credentials.php',
    'sandbox' => __DIR__ . '/sandbox.php',
    'metrics' => __DIR__ . '/metrics.php',
    'webhooks' => __DIR__ . '/metrics.php',
    'partners' => __DIR__ . '/partners.php',
    'guides' => __DIR__ . '/guides.php'
];

if (isset($allowedServices[$service]) && file_exists($allowedServices[$service])) {
    require $allowedServices[$service];
    exit;
}

sendJsonError("Unknown service '{$service}'. Valid services: " . implode(', ', array_keys($allowedServices)), 404);
