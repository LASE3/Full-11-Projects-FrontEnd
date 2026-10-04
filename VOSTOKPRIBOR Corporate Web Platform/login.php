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
    content="VOSTOKPRIBOR Corporate Web Platform - Content management and corporate administration authentication.">
  <title>Corporate Platform Login · VOSTOKPRIBOR WEB</title>
  <link rel="stylesheet" href="../Admin & Governance Portal/css/login.css">
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
  <!-- Circuit Grid Background Overlay -->
  <div class="auth-bg-grid" aria-hidden="true"></div>

  <!-- Top System Header Bar -->
  <header class="auth-top-bar">
    <a href="index.php" class="auth-brand-link" title="Return to Website">
      <img src="assets/logo.svg" alt="VOSTOKPRIBOR Logo" class="auth-brand-logo" onerror="this.style.display='none'">
      <div>
        <div class="auth-brand-name">VOSTOKPRIBOR</div>
      </div>
      <span class="auth-system-tag">SYS-01 // WEB</span>
    </a>
    <div class="auth-status-beacon" title="Automated security telemetry active">
      <span class="status-dot-pulse"></span>
      <span>SECURE TERMINAL · GOST-R 50739</span>
    </div>
  </header>

  <!-- Main Viewport -->
  <main class="auth-main">
    <section class="auth-card" aria-labelledby="login-heading">
      <div class="auth-card-stripe" aria-hidden="true"></div>

      <div class="auth-card-body">
        <header class="auth-card-header">
          <div class="auth-class-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
            </svg>
            <span>Corporate Class</span>
          </div>
          <h1 id="login-heading" class="auth-card-title">Corporate Portal Login</h1>
          <p class="auth-card-subtitle">Staff authentication for corporate announcements, catalog, and job postings.</p>
        </header>

        <!-- Dynamic Error/Success Notification Container -->
        <div id="auth-alert" class="auth-alert <?= !empty($initError) ? 'active-error' : '' ?>" role="alert" aria-live="polite">
          <svg id="alert-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <span id="alert-message"><?= !empty($initError) ? htmlspecialchars($initError) : 'Authentication failed. Please check credentials.' ?></span>
        </div>

        <!-- Login Form -->
        <form id="login-form" class="auth-form" action="../api/auth.php" method="POST" novalidate>
          <input type="hidden" name="systemId" value="WEB">
          <input type="hidden" name="redirect" value="admin.php">
          <div class="form-group">
            <label for="userId" class="form-label">
              <span>Employee / Admin ID</span>
              <span class="req-star">*</span>
            </label>
            <div class="input-container">
              <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                <circle cx="9" cy="10" r="2"></circle>
                <line x1="15" y1="8" x2="17" y2="8"></line>
                <line x1="15" y1="12" x2="17" y2="12"></line>
                <line x1="7" y1="16" x2="17" y2="16"></line>
              </svg>
              <input type="text" id="userId" name="userId" class="form-input" placeholder="admin@gmail.com / EMP-1001" autocomplete="username" autofocus>
            </div>
          </div>

          <div class="form-group">
            <label for="password" class="form-label">
              <span>Password</span>
              <span class="req-star">*</span>
            </label>
            <div class="input-container">
              <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
              <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" autocomplete="current-password">
            </div>
          </div>

          <button type="submit" id="submit-btn" class="auth-submit-btn">
            <span class="btn-text">Authenticate</span>
          </button>
        </form>
      </div>

      <footer class="auth-card-footer">
        <a href="index.php" class="auth-back-link">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          <span>Return to Corporate Platform</span>
        </a>
      </footer>
    </section>
  </main>
</body>
</html>
