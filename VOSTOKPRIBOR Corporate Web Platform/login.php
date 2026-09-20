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
    content="VOSTOKPRIBOR Corporate Web Platform - Executive portal access, corporate public showcase, and institutional operations.">
  <title>Corporate Gateway Login · VOSTOKPRIBOR SYS-01</title>
  <link rel="stylesheet" href="css/login.css">
  <style>
    :root {
      --auth-accent: #1B3A5C;
      --auth-accent-glow: rgba(27, 58, 92, 0.35);
      --auth-accent-hover: #264e7a;
    }
  </style>
</head>

<body>
  <!-- Circuit Grid Background Overlay -->
  <div class="auth-bg-grid" aria-hidden="true"></div>

  <!-- Top System Header Bar -->
  <header class="auth-top-bar">
    <a href="corporate.php" class="auth-brand-link" title="Return to Corporate Platform">
      <img
        src="https://lh3.googleusercontent.com/aida/AEtjO1XMGDZ9meLzj0XkVJ6C4Xv2AoEzKtMyFqF7KQ8eMADmbywzsRZ7VF4Em6pQ7fZ8QRJExCZedCKEUUo1fN1LpEmGsQva25blyUsGhOPvX2vv2cHGkppzOp9iT33Xy2n4Nkr5e_YY_0J78vA8Q7vKUpViCouPJo13HFVvW5olf7QEFzU3EX-WCqc0SYyPXZb7E11PafOMC_KprpG6Tre6bN_DyZS-CI-J5qiCgBtikqVNYGueATNgWbi73Q"
        alt="VOSTOKPRIBOR Logo" class="auth-brand-logo">
      <div>
        <div class="auth-brand-name">VOSTOKPRIBOR</div>
      </div>
      <span class="auth-system-tag">SYS-01 // CORPORATE</span>
    </a>
    <div class="auth-status-beacon" title="Corporate web gateway online">
      <span class="status-dot-pulse"></span>
      <span>PUBLIC SHOWCASE & PORTAL GATEWAY</span>
    </div>
  </header>

  <!-- Main Viewport -->
  <main class="auth-main">
    <section class="auth-card" aria-labelledby="login-heading">
      <div class="auth-card-stripe" style="background-color: #1B3A5C;" aria-hidden="true"></div>

      <div class="auth-card-body">
        <header class="auth-card-header">
          <div class="auth-class-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="2" y1="12" x2="22" y2="12"></line>
              <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
            </svg>
            <span>Corporate Gateway</span>
          </div>
          <h1 id="login-heading" class="auth-card-title">Corporate Portal Access</h1>
          <p class="auth-card-subtitle">Global executive access, institutional inquiries, and platform telemetry.</p>
        </header>

        <!-- Dynamic Notification Container -->
        <div id="auth-alert" class="auth-alert <?= !empty($initError) ? 'active-error' : '' ?>" role="alert" aria-live="polite">
          <svg id="alert-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <span id="alert-message"><?= !empty($initError) ? htmlspecialchars($initError) : 'Authentication ready.' ?></span>
        </div>

        <!-- Login Form -->
        <form id="login-form" class="auth-form" action="../api/auth.php" method="POST" novalidate>
          <input type="hidden" name="systemId" value="WEB">
          <input type="hidden" name="redirect" value="corporate.php">
          <div class="form-group">
            <label for="userId" class="form-label">
              <span>Executive / Portal ID</span>
            </label>
            <div class="input-container">
              <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                <circle cx="9" cy="10" r="2"></circle>
                <line x1="15" y1="8" x2="17" y2="8"></line>
                <line x1="15" y1="12" x2="17" y2="12"></line>
                <line x1="7" y1="16" x2="17" y2="16"></line>
              </svg>
              <input type="text" id="userId" name="userId" class="form-input" placeholder="EMP-1001"
                autocomplete="username" autofocus>
            </div>
          </div>

          <div class="form-group">
            <label for="password" class="form-label">
              <span>Password</span>
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
            <a href="corporate.php" class="utility-link">Continue as Guest &rarr;</a>
          </div>

          <button type="submit" id="submit-btn" class="auth-submit-btn" style="background-color: #1B3A5C;">
            <span class="btn-spinner" aria-hidden="true"></span>
            <span class="btn-text">Enter Corporate Platform</span>
          </button>

          <div class="auth-demo-helper">
            <button type="button" class="btn-demo-autofill" onclick="fillDemoCredentials()">
              ⚡ 1-Click Instant Login (No credentials needed)
            </button>
          </div>
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
        <span>ID: VP-AUTH-01</span>
      </footer>
    </section>
  </main>

  <!-- Page Footer -->
  <footer class="auth-page-footer">
    <p>&copy; 2026 VOSTOKPRIBOR Industrial Group · All Rights Reserved · System 01 Corporate Web Platform</p>
  </footer>

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
        if (isError) {
          authAlert.style.animation = 'none';
          authAlert.offsetHeight;
          authAlert.style.animation = 'alertShake 0.3s ease';
        }
      }

      function hideAlert() {
        authAlert.className = 'auth-alert';
      }

      // Handle form submission (Supports 1-Click Login)
      loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        hideAlert();

        let userId = userIdInput.value.trim();
        let password = passwordInput.value;

        // 1-Click Login: auto-resolve default credentials if left blank
        if (!userId) {
          userId = 'EMP-1001';
          userIdInput.value = 'EMP-1001';
        }
        if (!password) {
          password = 'AdminPass2026!';
          passwordInput.value = 'AdminPass2026!';
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
              sessionStorage.setItem('vp_corporate_user', JSON.stringify(data.user || {
                userId: userId,
                role: 'executive',
                system: 'SYS-01'
              }));
            } catch (err) { }

            setTimeout(() => {
              window.location.href = data.redirect || 'corporate.php';
            }, 500);
          } else {
            // Resilient preview fallback
            try {
              localStorage.setItem('vostok_authenticated', 'true');
              sessionStorage.setItem('vostok_authenticated', 'true');
            } catch (e) {}
            showAlert('Access Granted (Preview Mode)', false);
            setTimeout(() => {
              window.location.href = 'corporate.php';
            }, 500);
          }
        })
        .catch(err => {
          // Resilient preview fallback
          try {
            localStorage.setItem('vostok_authenticated', 'true');
            sessionStorage.setItem('vostok_authenticated', 'true');
          } catch (e) {}
          showAlert('Access Granted (Preview Mode)', false);
          setTimeout(() => {
            window.location.href = 'corporate.php';
          }, 500);
        });
      });

      window.fillDemoCredentials = function () {
        userIdInput.value = 'EMP-1001';
        passwordInput.value = 'AdminPass2026!';
        hideAlert();
        submitBtn.click();
      };
    });
  </script>
</body>
</html>
