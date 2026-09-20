<?php
/**
 * Admin & Governance Portal - Entry Point
 * Directs unauthenticated visitors to login.php first.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['vostok_authenticated']) || empty($_SESSION['vostok_system_ADM'])) {
    header("Location: login.php");
    exit;
}

header("Location: mainDashboard.php");
exit;
