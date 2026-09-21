<?php
/**
 * VOSTOKPRIBOR HR System (SYS08) - Inter-System Integrations & Data Bus
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
requireAuth('HR');
$currUser = $_SESSION['vostok_user'] ?? ['full_name' => 'Authorized User', 'clearance_level' => 'L2'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOSTOKPRIBOR HR System · Inter-System Integrations (SYS08)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: #08121e;
            color: #E2E8F0;
            font-family: 'IBM Plex Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .vostok-top-bar {
            height: 56px;
            background: #0A1929;
            border-bottom: 1px solid rgba(0, 229, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
        }
        .vostok-brand-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #FFFFFF;
        }
        .vostok-brand-link img {
            height: 28px;
            width: auto;
        }
        .vostok-nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .vostok-btn-back {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #FFFFFF;
            padding: 0.4rem 0.85rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s;
        }
        .vostok-btn-back:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: #00E5FF;
        }
        .vostok-main-content {
            flex: 1;
            max-width: 88rem;
            width: 100%;
            margin: 0 auto;
            padding: 1.5rem;
        }
    </style>
</head>
<body>

    <!-- Header Bar -->
    <header class="vostok-top-bar">
        <a href="Dashboard.php" class="vostok-brand-link">
            <img src="../assets/logo.svg" alt="VOSTOKPRIBOR" onerror="this.src='../assets/images/logo.png'">
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 700; font-size: 0.9rem; letter-spacing: 0.05em; color: #FFFFFF;">VOSTOKPRIBOR</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.65rem; color: #00E5FF;">HR System &bull; SYS08</span>
            </div>
        </a>

        <div class="vostok-nav-actions">
            <a href="Dashboard.php" class="vostok-btn-back">
                <span class="material-symbols-outlined" style="font-size: 1rem;">arrow_back</span>
                <span>Back to Dashboard</span>
            </a>
            <a href="../api/logout.php?redirect=../HR System/login.php" class="vostok-btn-back" style="color: #F87171; border-color: rgba(239,68,68,0.3);">
                <span class="material-symbols-outlined" style="font-size: 1rem;">logout</span>
                <span>Sign Out</span>
            </a>
        </div>
    </header>

    <!-- Main Content Rendering Integration Matrix -->
    <main class="vostok-main-content">
        <?php renderSystemIntegrationView('SYS08', 'standalone'); ?>
    </main>

</body>
</html>