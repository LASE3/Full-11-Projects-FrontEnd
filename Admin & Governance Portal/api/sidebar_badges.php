<?php
/**
 * VOSTOKPRIBOR System 11 // GOV-CORE
 * Dynamic Live Sidebar Badges API
 * Returns live operational and statutory metrics for sidebar badges.
 */
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
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
