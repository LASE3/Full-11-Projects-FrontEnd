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
    content="VOSTOKPRIBOR Employee Intranet - Internal communications, personnel directory, policies, and staff tools.">
  <title>Internal Portal Login · VOSTOKPRIBOR SYS-03</title>
  <link rel="stylesheet" href="css/login.css">
</head>

<body>
  <div class="auth-bg-grid" aria-hidden="true"></div>

  <header class="auth-top-bar">
    <a href="index.php" class="auth-brand-link" title="Return to Employee Intranet">
      <img
        src="assets/logo.svg"
        alt="VOSTOKPRIBOR Logo" class="auth-brand-logo">
      <div>
        <div class="auth-brand-name">VOSTOKPRIBOR</div>
      </div>
      <span class="auth-system-tag">SYS-03 // INTRANET</span>
    </a>
    <div class="auth-status-beacon" title="Staff Communications Hub">
      <span class="status-dot-pulse"></span>
      <span>INTERNAL NETWORK SECURE · 10.240.0.0/16</span>
    </div>
  </header>

  <main class="auth-main">
    <section class="auth-card" aria-labelledby="login-heading">
      <div class="auth-card-stripe" aria-hidden="true"></div>

      <div class="auth-card-body">
        <header class="auth-card-header">
          <div class="auth-class-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            <span>Employee Internal Class</span>
          </div>
          <h1 id="login-heading" class="auth-card-title">Internal Portal Login</h1>
          <p class="auth-card-subtitle">Internal communications, team directory, corporate schedules, and staff tools.
          </p>
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

        <form id="login-form" class="auth-form" action="../api/auth.php" method="POST" novalidate>
          <input type="hidden" name="systemId" value="EMP">
          <input type="hidden" name="redirect" value="Dashboard.php">
          <div class="form-group">
            <label for="userId" class="form-label">
              <span>Employee ID</span>
              <span style="color: var(--auth-accent);">*</span>
            </label>
            <div class="input-container">
              <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                <circle cx="9" cy="10" r="2"></circle>
                <line x1="15" y1="8" x2="17" y2="8"></line>
                <line x1="15" y1="12" x2="17" y2="12"></line>
                <line x1="7" y1="16" x2="17" y2="16"></line>
              </svg>
              <input type="text" id="userId" name="userId" class="form-input" placeholder="EMP-VP-1019"
                autocomplete="username"  autofocus>
            </div>
          </div>

          <div class="form-group">
            <label for="password" class="form-label">
              <span>Password</span>
              <span style="color: var(--auth-accent);">*</span>
            </label>
            <div class="input-container">
              <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
              <input type="password" id="password" name="password" class="form-input" placeholder="••••••••"
                autocomplete="current-password" >
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
            <span class="btn-text">Log In as Employee</span>
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
        <span>ID: VP-INTRANET-03</span>
      </footer>
    </section>
  </main>

  <footer class="auth-page-footer">
    <p>&copy; 2026 VOSTOKPRIBOR Industrial Group · Internal Staff Portal · intranet.vostokpribor.local</p>
  </footer>

  <script src="js/dynamic-login-theme.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const loginForm = document.getElementById('login-form');
      const userIdInput = document.getElementById('userId');
      const passwordInput = document.getElementById('password');
      const submitBtn = document.getElementById('submit-btn');
      const authAlert = document.getElementById('auth-alert');
      const alertMessage = document.getElementById('alert-message');
      const togglePasswordBtn = document.getElementById('toggle-password-btn');
      const eyeIcon = document.getElementById('eye-icon');

      togglePasswordBtn.addEventListener('click', () => {
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        eyeIcon.innerHTML = isPassword
          ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>'
          : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
      });

      function showAlert(msg, isError = true) {
        authAlert.className = isError ? 'auth-alert active-error' : 'auth-alert active-success';
        alertMessage.textContent = msg;
        if (typeof updateLoginTheme === 'function') updateLoginTheme(msg);
        if (isError) {
          authAlert.style.animation = 'none';
          authAlert.offsetHeight;
          authAlert.style.animation = 'alertShake 0.3s ease';
        }
      }

      function hideAlert() {
        authAlert.className = 'auth-alert';
        if (typeof updateLoginTheme === 'function') updateLoginTheme(document.getElementById('login-heading')?.textContent || 'Welcome');
      }

      loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        hideAlert();

        const userId = userIdInput.value.trim();
        const password = passwordInput.value;
        if (!userId) {
          showAlert('Please enter your User ID or email.', true);
          return;
        }
        if (!password) {
          showAlert('Please enter your password.', true);
          return;
        }

        submitBtn.classList.add('is-loading');
        submitBtn.disabled = true;

        const formData = new FormData(loginForm);
        formData.append('ajax', '1');

        fetch('../api/auth.php', {
          method: 'POST',
          headers: { 'Accept': 'application/json' },
          body: formData
        })
        .then(async (res) => {
          const data = await res.json().catch(() => ({}));
          if (res.ok && data.success) {
            showAlert(data.message || `Access Granted for ${userId}!`, false);
            try {
              localStorage.setItem('vostok_authenticated', 'true');
              sessionStorage.setItem('vostok_authenticated', 'true');
              sessionStorage.setItem('vp_intranet_user', JSON.stringify(data.user || {
                userId: userId,
                role: 'employee',
                system: 'SYS-03'
              }));
            } catch (err) { }

            setTimeout(() => {
              window.location.href = data.redirect || 'Dashboard.php';
            }, 600);
          } else {
            submitBtn.classList.remove('is-loading');
            submitBtn.disabled = false;
            showAlert(data.message || 'Authentication failed: Invalid credentials or insufficient clearance.', true);
          }
        })
        .catch(err => {
          submitBtn.classList.remove('is-loading');
          submitBtn.disabled = false;
          showAlert('Database connection error: ' + err.message, true);
        });
      });

      window.handleForgotCredentials = function () {
        showAlert('Contact HR or Internal IT Helpdesk (ext. 2244) for password reset.', true);
      };
    });
  </script>
</body>

</html>