<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR Employee Intranet (SYS05) - Inter-System Integrations & Data Bus
 *
 * Provides real-time visibility into inter-system data interchange channels,
 * protocol specifications, authorization clearance rules, and live telemetry audit logs.
 *
 * @package    VOSTOKPRIBOR Enterprise Suite
 * @subpackage Employee Intranet
 * @version    2.4.0
 */

// Centralized authentication guard & session engine
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/integration_panel.php';

// Strictly enforce intranet clearance & session authorization (SYS05 / EMP)
requireAuth('EMP');

// Active user identity context
$currUser = $_SESSION['vostok_user'] ?? [
    'full_name'       => 'Authorized Staff',
    'clearance_level' => 'L2',
    'role_name'       => 'Specialist'
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="VOSTOKPRIBOR Employee Intranet (SYS05) — Inter-System Integrations &amp; Real-time Data Bus">
    <meta name="robots" content="noindex, nofollow">
    <title>VOSTOKPRIBOR Employee Intranet · Inter-System Integrations (SYS05)</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="../assets/logo.svg">

    <!-- Fonts: IBM Plex Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <!-- Stylesheets -->
    <link rel="stylesheet" href="../assets/css/integration-panel.css">
</head>

<body class="vostok-integrations-page">

    <!-- Header Bar -->
    <header class="vostok-top-bar" role="banner">
        <a href="Dashboard.php" class="vostok-brand-link" title="Return to Intranet Dashboard">
            <img src="../assets/logo.svg" alt="VOSTOKPRIBOR Logo" width="28" height="28">
            <div class="vostok-brand-title">
                <span class="vostok-brand-name">VOSTOKPRIBOR</span>
                <span class="vostok-brand-system">Employee Intranet &bull; SYS05</span>
            </div>
        </a>

        <div class="vostok-nav-actions">
            <!-- User Status Context -->
            <div class="vostok-user-badge-header" title="Signed in as <?= htmlspecialchars($currUser['full_name'] ?? 'Authorized Staff', ENT_QUOTES, 'UTF-8') ?>">
                <span class="material-symbols-outlined vostok-icon-sm">person</span>
                <span class="vostok-user-name"><?= htmlspecialchars($currUser['full_name'] ?? 'Authorized Staff', ENT_QUOTES, 'UTF-8') ?></span>
                <span class="vostok-clearance-pill"><?= htmlspecialchars($currUser['clearance_level'] ?? 'L2', ENT_QUOTES, 'UTF-8') ?></span>
            </div>

            <!-- Navigation Actions -->
            <a href="Dashboard.php" class="vostok-btn-back" title="Back to Dashboard">
                <span class="material-symbols-outlined vostok-icon-sm">arrow_back</span>
                <span>Back to Dashboard</span>
            </a>
            <a href="./api/logout.php?redirect=../login.php" class="vostok-btn-back vostok-btn-signout" title="Sign Out of Employee Intranet">
                <span class="material-symbols-outlined vostok-icon-sm">logout</span>
                <span>Sign Out</span>
            </a>
        </div>
    </header>

    <!-- Main Content Rendering Integration Matrix -->
    <main class="vostok-main-content" role="main">
        <?php renderSystemIntegrationView('SYS05', 'standalone'); ?>
    </main>

</body>

</html>