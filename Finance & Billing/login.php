<?php
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
    content="VOSTOKPRIBOR Finance & Billing - Financial control login, invoicing, transactions, and corporate fiscal oversight.">
  <title>Financial Control Login · VOSTOKPRIBOR SYS-04</title>
  <link rel="stylesheet" href="css/login.css">
</head>

<body>
  <div class="auth-bg-grid" aria-hidden="true"></div>

  <header class="auth-top-bar">
    <a href="index.php" class="auth-brand-link" title="Return to Finance Portal">
      <img
        src="assets/logo.svg"
        alt="VOSTOKPRIBOR Logo" class="auth-brand-logo">
      <div>
        <div class="auth-brand-name">VOSTOKPRIBOR</div>
      </div>
      <span class="auth-system-tag">SYS-04 // FINANCE</span>
    </a>
    <div class="auth-status-beacon" title="Treasury & Ledger Gateway Active">
      <span class="status-dot-pulse"></span>
      <span>FINANCIAL LEDGER SECURED · 256-BIT</span>
    </div>
  </header>

  <main class="auth-main">
    <section class="auth-card" aria-labelledby="login-heading">
      <div class="auth-card-stripe" aria-hidden="true"></div>

      <div class="auth-card-body">
        <header class="auth-card-header">
          <div class="auth-class-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="12" y1="1" x2="12" y2="23"></line>
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
            <span>Finance &amp; Billing Class</span>
          </div>
          <h1 id="login-heading" class="auth-card-title">Financial Control Login</h1>
          <p class="auth-card-subtitle">Invoicing, transactions, project budgets, and corporate financial oversight.</p>
        </header>

        <div id="auth-alert" class="auth-alert <?= !empty($initError) ? 'active-error' : '' ?>" role="alert" aria-live="polite">
          <svg id="alert-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <span id="alert-message"><?= !empty($initError) ? htmlspecialchars($initError) : 'Authentication failed. Please verify credentials.' ?></span>
        </div>

        <form id="login-form" data-recovery-msg="Contact VP Corporate Treasury &amp; Controller for credential recovery." class="auth-form" action="../api/auth.php" method="POST" novalidate>
          <input type="hidden" name="systemId" value="FIN">
          <input type="hidden" name="redirect" value="Dashboard.php">
          <div class="form-group">
            <label for="userId" class="form-label">
              <span>Finance Officer ID</span>
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
              <input type="text" id="userId" name="userId" class="form-input" placeholder="FIN-VP-502"
                autocomplete="username" autofocus>
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
              <input type="password" id="password" name="password" class="form-input" placeholder="••••••••"
                autocomplete="current-password">
              <button type="button" id="toggle-password-btn" class="password-toggle-btn"
                aria-label="Toggle password visibility" title="Show/Hide Password">
                <svg id="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
            </div>
          </div>

          <div class="form-utility-row">
            <label class="checkbox-label" for="remember-session">
              <input type="checkbox" id="remember-session" class="checkbox-input">
              <span>Remember workstation</span>
            </label>
            <a href="javascript:void(0)" class="utility-link" onclick="handleForgotCredentials()">Forgot password?</a>
          </div>

          <button type="submit" id="submit-btn" class="auth-submit-btn">
            <span class="btn-spinner" aria-hidden="true"></span>
            <span class="btn-text">Log In to Finance</span>
          </button>


        </form>
      </div>

      <footer class="auth-card-footer">
        <span class="security-badge">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
          </svg>
          TLS 1.3 256-Bit Encrypted
        </span>
        <span>ID: VP-FIN-04</span>
      </footer>
    </section>
  </main>

  <footer class="auth-page-footer">
    <p>&copy; 2026 VOSTOKPRIBOR Industrial Group · Financial Control &amp; Treasury · finance.vostokpribor.local</p>
  </footer>

  <script src="js/dynamic-login-theme.js"></script>
  <script src="../assets/js/auth-form.js"></script>
</body>

</html>