<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$initError = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description"
    content="VOSTOKPRIBOR Corporate Web Platform - Executive and Employee Access Portal.">
  <title>Corporate Gateway Login · VOSTOKPRIBOR SYS-01</title>
  <link rel="stylesheet" href="../CRM/css/login.css?v=1790609011">
  <style>
  .vp-sim-bar, .theme-simulation-bar, [class*="vp-sim"], [class*="theme-sim"] {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    height: 0 !important;
    pointer-events: none !important;
  }
</style>
</head>

<body>
  <div class="auth-bg-grid" aria-hidden="true"></div>

  <!-- Top Navigation Bar -->
  <header class="auth-top-bar">
    <a href="index.php" class="auth-brand-link" title="Return to Corporate Showcase">
      <img
        src="assets/logo.svg"
        alt="VOSTOKPRIBOR Logo" class="auth-brand-logo" onerror="this.src='../assets/logo.svg'">
      <div>
        <div class="auth-brand-name">VOSTOKPRIBOR</div>
      </div>
      <span class="auth-system-tag">CORP · SYS-01</span>
    </a>
    <div class="auth-status-beacon" title="Corporate web gateway online">
      <span class="status-dot-pulse"></span>
      <span>GATEWAY: ACTIVE 99.98%</span>
    </div>
  </header>

  <!-- Main Viewport -->
  <main class="auth-main">
    <section class="auth-card" aria-labelledby="login-heading">
      <div class="auth-card-stripe" aria-hidden="true" style="background:#1B3A5C;"></div>

      <div class="auth-card-body">
        <header class="auth-card-header">
          <div class="auth-class-badge" style="color:#38bdf8;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"></path>
              <path d="M12 6v6l4 2"></path>
            </svg>
            <span>Corporate Web Gateway</span>
          </div>
          <h1 id="login-heading" class="auth-card-title">Corporate Portal Login</h1>
          <p class="auth-card-subtitle">Access executive presentations, public catalog administration, and corporate communications.</p>
        </header>

        <!-- Dynamic Alert Container -->
        <div id="auth-alert" class="auth-alert <?= !empty($initError) ? 'active-error' : '' ?>" role="alert" aria-live="polite">
          <svg id="alert-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <span id="alert-message"><?= !empty($initError) ? htmlspecialchars($initError) : 'Authentication failed. Please verify your credentials.' ?></span>
        </div>

        <!-- Login Form -->
        <form id="login-form" class="auth-form" action="../CRM/api/auth.php" method="POST">
          <input type="hidden" name="systemId" value="CRM">
          <input type="hidden" name="redirect" value="../../VOSTOKPRIBOR Corporate Web Platform/index.php">
          <div class="form-group">
            <label for="userId" class="form-label">
              <span>Executive / Staff ID</span>
              <span class="req-star">*</span>
            </label>
            <div class="input-container">
              <input type="text" id="userId" name="userId" class="form-input" placeholder="admin" value="admin"
                autocomplete="username" autofocus required>
            </div>
          </div>

          <div class="form-group">
            <label for="password" class="form-label">
              <span>Password</span>
              <span class="req-star">*</span>
            </label>
            <div class="input-container">
              <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" value="admin1234"
                autocomplete="current-password" required>
            </div>
          </div>

          <div class="form-actions" style="margin-top: 20px;">
            <button type="submit" id="submit-btn" class="submit-btn" style="background:#1B3A5C; color:#fff; width:100%; padding:10px; border-radius:6px; font-weight:600; cursor:pointer;">
              <span>Authenticate & Enter Corporate Platform</span>
            </button>
          </div>
          <div style="margin-top: 15px; text-align: center;">
            <a href="index.php" style="color: #64748b; font-size: 12px; text-decoration: none;">&larr; Or view public showcase directly</a>
          </div>
        </form>
      </div>
    </section>
  </main>
</body>
</html>
