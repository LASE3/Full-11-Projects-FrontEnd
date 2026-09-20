<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description"
    content="VOSTOKPRIBOR B2B Online Shop - Create a new B2B procurement account to access the industrial catalog, submit RFQs, and track orders.">
  <title>B2B Account Sign Up · VOSTOKPRIBOR SYS-02</title>
  <link rel="stylesheet" href="css/login.css">
</head>

<body>
  <div class="auth-bg-grid" aria-hidden="true"></div>

  <header class="auth-top-bar">
    <a href="index.php" class="auth-brand-link" title="Return to B2B Shop">
      <img
        src="https://lh3.googleusercontent.com/aida/AEtjO1XMGDZ9meLzj0XkVJ6C4Xv2AoEzKtMyFqF7KQ8eMADmbywzsRZ7VF4Em6pQ7fZ8QRJExCZedCKEUUo1fN1LpEmGsQva25blyUsGhOPvX2vv2cHGkppzOp9iT33Xy2n4Nkr5e_YY_0J78vA8Q7vKUpViCouPJo13HFVvW5olf7QEFzU3EX-WCqc0SYyPXZb7E11PafOMC_KprpG6Tre6bN_DyZS-CI-J5qiCgBtikqVNYGueATNgWbi73Q"
        alt="VOSTOKPRIBOR Logo" class="auth-brand-logo">
      <div>
        <div class="auth-brand-name">VOSTOKPRIBOR</div>
      </div>
      <span class="auth-system-tag">B2B SHOP · SYS-02</span>
    </a>
    <div class="auth-status-beacon" title="B2B Account Registration Gateway Active">
      <span class="status-dot-pulse"></span>
      <span>B2B REGISTRATION GATEWAY · LIVE</span>
    </div>
  </header>

  <main class="auth-main">
    <section class="auth-card auth-card-wide" aria-labelledby="signup-heading">
      <div class="auth-card-stripe" aria-hidden="true"></div>
      <div class="auth-card-body">
        <header class="auth-card-header">
          <div class="auth-class-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <circle cx="9" cy="21" r="1"></circle>
              <circle cx="20" cy="21" r="1"></circle>
              <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <span>B2B Enterprise Access</span>
          </div>
          <h1 id="signup-heading" class="auth-card-title">Create B2B Account</h1>
          <p class="auth-card-subtitle">Register a new procurement account for your organization to access the full industrial catalog, submit RFQs, and manage orders.</p>
        </header>

        <div id="auth-alert" class="auth-alert" role="alert" aria-live="polite">
          <svg id="alert-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <span id="alert-message">Please fill in all required fields.</span>
        </div>

        <form id="signup-form" class="auth-form" method="POST" novalidate>
          <div class="form-row">
            <div class="form-group">
              <label for="fullName" class="form-label">
                <span>Full Name</span>
                <span style="color: var(--auth-accent);">*</span>
              </label>
              <div class="input-container">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <input type="text" id="fullName" name="fullName" class="form-input" placeholder="e.g. Sergei Makarov" autocomplete="name" required>
              </div>
            </div>
            <div class="form-group">
              <label for="jobTitle" class="form-label">
                <span>Job Title</span>
                <span style="color: var(--auth-accent);">*</span>
              </label>
              <div class="input-container">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                  <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
                <input type="text" id="jobTitle" name="jobTitle" class="form-input" placeholder="e.g. Procurement Manager" autocomplete="organization-title" required>
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="orgName" class="form-label">
                <span>Organization / Company</span>
                <span style="color: var(--auth-accent);">*</span>
              </label>
              <div class="input-container">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                  <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <input type="text" id="orgName" name="orgName" class="form-input" placeholder="e.g. Aral Geomatics Group" autocomplete="organization" required>
              </div>
            </div>
            <div class="form-group">
              <label for="sector" class="form-label">
                <span>Industry Sector</span>
                <span style="color: var(--auth-accent);">*</span>
              </label>
              <div class="input-container">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                  <polyline points="2 17 12 22 22 17"></polyline>
                  <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
                <select id="sector" name="sector" class="form-input" required>
                  <option value="" disabled selected>Select your sector</option>
                  <option value="manufacturing">Manufacturing</option>
                  <option value="geomatics">Geomatics &amp; GIS</option>
                  <option value="process-automation">Process Automation</option>
                  <option value="mining">Mining &amp; Extraction</option>
                  <option value="metrology">Industrial Metrology</option>
                  <option value="railway">Railway Infrastructure</option>
                  <option value="water">Water Infrastructure</option>
                  <option value="robotics">Robotics</option>
                  <option value="other">Other</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="email" class="form-label">
                <span>Business Email</span>
                <span style="color: var(--auth-accent);">*</span>
              </label>
              <div class="input-container">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                  <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                <input type="email" id="email" name="email" class="form-input" placeholder="procurement@yourcompany.com" autocomplete="email" required>
              </div>
            </div>
            <div class="form-group">
              <label for="accountId" class="form-label">
                <span>Requested Account ID</span>
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
                <input type="text" id="accountId" name="accountId" class="form-input" placeholder="CUS-XXXX" autocomplete="off" required>
              </div>
            </div>
          </div>

          <div class="form-row">
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
                <input type="password" id="password" name="password" class="form-input" placeholder="Min. 12 characters" autocomplete="new-password" required>
                <button type="button" id="toggle-password-btn" class="password-toggle-btn" aria-label="Toggle password visibility">
                  <svg id="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </button>
              </div>
            </div>
            <div class="form-group">
              <label for="confirmPassword" class="form-label">
                <span>Confirm Password</span>
                <span style="color: var(--auth-accent);">*</span>
              </label>
              <div class="input-container">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <input type="password" id="confirmPassword" name="confirmPassword" class="form-input" placeholder="Re-enter password" autocomplete="new-password" required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="checkbox-label" for="agree-terms">
              <input type="checkbox" id="agree-terms" class="checkbox-input" required>
              <span>I agree to the VOSTOKPRIBOR B2B <a href="javascript:void(0)" class="utility-link">Terms of Service</a> and <a href="javascript:void(0)" class="utility-link">Procurement Policy</a></span>
            </label>
          </div>

          <button type="submit" id="submit-btn" class="auth-submit-btn">
            <span class="btn-spinner" aria-hidden="true"></span>
            <span class="btn-text">Create B2B Account</span>
          </button>

          <p style="text-align:center;font-size:12px;color:var(--auth-text-muted);margin-top:16px;">
            Already have an account? <a href="login.php" class="utility-link">Log In</a>
          </p>
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
        <span>ID: VP-SHOP-02</span>
      </footer>
    </section>
  </main>

  <footer class="auth-page-footer">
    <p>&copy; 2026 VOSTOKPRIBOR Industrial Group · B2B Commerce Engine · shop.vostokpribor.local</p>
  </footer>

  <script src="js/dynamic-login-theme.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const signupForm = document.getElementById('signup-form');
      const submitBtn = document.getElementById('submit-btn');
      const authAlert = document.getElementById('auth-alert');
      const alertMessage = document.getElementById('alert-message');
      const passwordInput = document.getElementById('password');
      const confirmPasswordInput = document.getElementById('confirmPassword');
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
        if (isError) { authAlert.style.animation = 'none'; authAlert.offsetHeight; authAlert.style.animation = 'alertShake 0.3s ease'; }
      }

      function hideAlert() {
        authAlert.className = 'auth-alert';
        if (typeof updateLoginTheme === 'function') updateLoginTheme('Create B2B Account');
      }

      signupForm.addEventListener('submit', (e) => {
        e.preventDefault();
        hideAlert();
        const fullName = document.getElementById('fullName').value.trim();
        const orgName = document.getElementById('orgName').value.trim();
        const email = document.getElementById('email').value.trim();
        const accountId = document.getElementById('accountId').value.trim();
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const agreed = document.getElementById('agree-terms').checked;

        if (!fullName) { showAlert('Please enter your full name.'); return; }
        if (!orgName) { showAlert('Please enter your organization name.'); return; }
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showAlert('Please enter a valid business email address.'); return; }
        if (!accountId) { showAlert('Please enter your requested Account ID (e.g. CUS-1099).'); return; }
        if (password.length < 12) { showAlert('Password must be at least 12 characters.'); return; }
        if (password !== confirmPassword) { showAlert('Passwords do not match. Please re-enter.'); return; }
        if (!agreed) { showAlert('You must agree to the Terms of Service to proceed.'); return; }

        submitBtn.classList.add('is-loading');
        submitBtn.disabled = true;
        setTimeout(() => {
          showAlert('B2B Account Request Submitted for ' + orgName + '! Redirecting to login...', false);
          try { sessionStorage.setItem('vp_shop_reg', JSON.stringify({ fullName, orgName, email, accountId, status: 'pending_review', timestamp: new Date().toISOString() })); } catch (err) {}
          setTimeout(() => {
            window.location.href = 'login.php';
          }, 1600);
        }, 700);
      });
    });
  </script>
</body>
</html>