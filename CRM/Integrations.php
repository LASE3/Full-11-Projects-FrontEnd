<?php

/**
 * VOSTOKPRIBOR CRM System (SYS02) - Inter-System Integrations & Data Bus
 * Features live data from MySQL vostokpribor:
 *   - API / Protocol
 *   - Authentication
 *   - Data Exchanged
 *   - Direction
 *   - Permissions
 *   - Live Audit Logs & Dispatch Action
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/integration_panel.php';

// Enforce authentication & clearance
requireAuth('CRM');
$currUser = $_SESSION['vostok_user'] ?? ['full_name' => 'Authorized User', 'clearance_level' => 'L2'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOSTOKPRIBOR CRM System · Inter-System Integrations (SYS02)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="../assets/css/integration-panel.css">
</head>

<body class="vostok-integrations-page">

    <!-- Header Bar -->
    <header class="vostok-top-bar">
        <a href="Dashboard.php" class="vostok-brand-link">
            <img src="../assets/logo.svg" alt="VOSTOKPRIBOR" onerror="this.src='../assets/images/logo.png'">
            <div class="vostok-brand-title">
                <span class="vostok-brand-name">VOSTOKPRIBOR</span>
                <span class="vostok-brand-system">CRM System &bull; SYS02</span>
            </div>
        </a>

        <div class="vostok-nav-actions">
            <a href="Dashboard.php" class="vostok-btn-back">
                <span class="material-symbols-outlined vostok-icon-sm">arrow_back</span>
                <span>Back to Dashboard</span>
            </a>
            <a href="../api/logout.php?redirect=../CRM/login.php" class="vostok-btn-back vostok-btn-signout">
                <span class="material-symbols-outlined vostok-icon-sm">logout</span>
                <span>Sign Out</span>
            </a>
        </div>
    </header>

    <!-- Main Content Rendering Integration Matrix -->
    <main class="vostok-main-content">
        <?php renderSystemIntegrationView('SYS02', 'standalone'); ?>
    </main>

</body>

</html>