<?php
/**
 * VOSTOKPRIBOR System 11 // GOV-CORE
 * Dynamic Live Sidebar Badges API
 * Returns live operational and statutory metrics for sidebar badges.
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('ADM', ['min_clearance' => 'L3']);

require_once __DIR__ . '/db_helper.php';
require_once __DIR__ . '/../gov_service.php';

try {
    $badges = gov_getSidebarBadges();
    jsonResponse([
        'success' => true,
        'badges' => $badges,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    error_log("API error: " . $e->getMessage());
        jsonResponse(['success' => false, 'error' => 'An internal server error occurred.'], 500);
}
