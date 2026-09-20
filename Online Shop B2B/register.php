<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description"
    content="VOSTOKPRIBOR B2B Online Shop - Register your company as an enterprise procurement partner.">
  <title>Enterprise Partner Registration · VOSTOKPRIBOR SYS-02</title>
  <link rel="stylesheet" href="css/login.css">
</head>

<body>
  <div class="auth-bg-grid" aria-hidden="true"></div>

  <header class="auth-top-bar">
    <a href="index.php" class="auth-brand-link" title="Return to B2B Shop">
      <img
        src="assets/logo.svg"
        alt="VOSTOKPRIBOR Logo" class="auth-brand-logo">
      <div>
        <div class="auth-brand-name">VOSTOKPRIBOR</div>
      </div>
      <span class="auth-system-tag">B2B SHOP · SYS-02</span>
    </a>
    <div class="auth-status-beacon" title="Enterprise Partner Registration Active">
      <span class="status-dot-pulse"></span>
      <span>PARTNER ONBOARDING · LIVE</span>
    
<!-- Top Bar Sign Out -->
<a href="../api/logout.php?system=Online%20Shop%20B2B&redirect=../Online%20Shop%20B2B/login.php" class="top-signout-btn" title="Sign Out of Online Shop B2B" onclick="(function(){sessionStorage.clear();localStorage.clear();})()" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:4px;background:rgba(178,58,50,0.2);border:1px solid rgba(178,58,50,0.5);color:#FF8080;font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;margin-left:8px;vertical-align:middle;transition:all 0.2s;" onmouseover="this.style.background='rgba(178,58,50,0.4)';this.style.color='#FFFFFF'" onmouseout="this.style.background='rgba(178,58,50,0.2)';this.style.color='#FF8080'"><span class="material-symbols-outlined" style="font-size:15px;line-height:1;">logout</span><span>Sign Out</span></a>
</div>
  </header>

  <main class="auth-main">
    <section class="auth-card auth-card-wide" aria-labelledby="register-heading">
      <div class="auth-card-stripe" aria-hidden="true"></div>
      <div class="auth-card-body">
        <header class="auth-card-header">
          <div class="auth-class-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
              <polyline points="2 17 12 22 22 17"></polyline>
              <polyline points="2 12 12 17 22 12"></polyline>
            </svg>
            <span>Enterprise Partnership</span>
          </div>
          <h1 id="register-heading" class="auth-card-title">Enterprise Partner Registration</h1>
          <p class="auth-card-subtitle">Register your company as an authorized VOSTOKPRIBOR B2B procurement partner. Our team will review and activate your enterprise account within 1-2 business days.</p>
        </header>

        <div id="auth-alert" class="auth-alert" role="alert" aria-live="polite">
          <svg id="alert-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <span id="alert-message">Please complete all required fields.</span>
        </div>

        <form id="register-form" class="auth-form" method="POST" novalidate>

          <div class="form-row">
            <div class="form-group">
              <label for="companyName" class="form-label">
                <span>Legal Company Name</span>
                <span style="color: var(--auth-accent);">*</span>
              </label>
              <div class="input-container">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                  <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <input type="text" id="companyName" name="companyName" class="form-input" placeholder="e.g. BaltNord Process Systems JSC" required>
              </div>
            </div>
            <div class="form-group">
              <label for="regNumber" class="form-label">
                <span>Company Registration Number</span>
                <span style="color: var(--auth-accent);">*</span>
              </label>
              <div class="input-container">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                  <line x1="8" y1="10" x2="16" y2="10"></line>
                  <line x1="8" y1="14" x2="13" y2="14"></line>
                </svg>
                <input type="text" id="regNumber" name="regNumber" class="form-input" placeholder="e.g. LV77401-2026" required>
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="country" class="form-label">
                <span>Country of Registration</span>
                <span style="color: var(--auth-accent);">*</span>
              </label>
              <div class="input-container">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="2" y1="12" x2="22" y2="12"></line>
                  <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                </svg>
                <select id="country" name="country" class="form-input" required>
                  <option value="" disabled selected>Select country</option>
                  <option value="KZ">Kazakhstan</option>
                  <option value="RU">Russia</option>
                  <option value="DE">Germany</option>
                  <option value="LV">Latvia</option>
                  <option value="PL">Poland</option>
                  <option value="UA">Ukraine</option>
                  <option value="UZ">Uzbekistan</option>
                  <option value="TR">Turkey</option>
                  <option value="other">Other</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="vatNumber" class="form-label">
                <span>VAT / Tax Number</span>
              </label>
              <div class="input-container">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="1" x2="12" y2="23"></line>
                  <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
                <input type="text" id="vatNumber" name="vatNumber" class="form-input" placeholder="e.g. LV40003XXXXX (optional)">
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="contactName" class="form-label">
                <span>Primary Contact Name</span>
                <span style="color: var(--auth-accent);">*</span>
              </label>
              <div class="input-container">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <input type="text" id="contactName" name="contactName" class="form-input" placeholder="e.g. Kristaps Ozols" required>
              </div>
            </div>
            <div class="form-group">
              <label for="contactEmail" class="form-label">
                <span>Contact Email</span>
                <span style="color: var(--auth-accent);">*</span>
              </label>
              <div class="input-container">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                  <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                <input type="email" id="contactEmail" name="contactEmail" class="form-input" placeholder="contact@company.com" required>
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="annualVolume" class="form-label">
                <span>Estimated Annual Procurement Volume</span>
              </label>
              <div class="input-container">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                </svg>
                <select id="annualVolume" name="annualVolume" class="form-input">
                  <option value="" disabled selected>Select volume range</option>
                  <option value="under-50k">Under $50,000</option>
                  <option value="50k-200k">$50,000 - $200,000</option>
                  <option value="200k-1m">$200,000 - $1M</option>
                  <option value="over-1m">Over $1M</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="partnerSector" class="form-label">
                <span>Primary Industry</span>
                <span style="color: var(--auth-accent);">*</span>
              </label>
              <div class="input-container">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                  <polyline points="2 17 12 22 22 17"></polyline>
                  <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
                <select id="partnerSector" name="partnerSector" class="form-input" required>
                  <option value="" disabled selected>Select industry</option>
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

          <div class="form-group">
            <label class="checkbox-label" for="agree-partner-terms">
              <input type="checkbox" id="agree-partner-terms" class="checkbox-input" required>
              <span>I confirm I am authorized to register this company and agree to the <a href="javascript:void(0)" class="utility-link">VOSTOKPRIBOR B2B Partner Agreement</a></span>
            </label>
          </div>

          <button type="submit" id="submit-btn" class="auth-submit-btn">
            <span class="btn-spinner" aria-hidden="true"></span>
            <span class="btn-text">Submit Enterprise Registration</span>
          </button>

          <p style="text-align:center;font-size:12px;color:var(--auth-text-muted);margin-top:16px;">
            Already have an account? <a href="login.php" class="utility-link">Log In</a> &nbsp;|&nbsp; <a href="signup.php" class="utility-link">Create individual account</a>
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
      const registerForm = document.getElementById('register-form');
      const submitBtn = document.getElementById('submit-btn');
      const authAlert = document.getElementById('auth-alert');
      const alertMessage = document.getElementById('alert-message');

      function showAlert(msg, isError = true) {
        authAlert.className = isError ? 'auth-alert active-error' : 'auth-alert active-success';
        alertMessage.textContent = msg;
        if (typeof updateLoginTheme === 'function') updateLoginTheme(msg);
        if (isError) { authAlert.style.animation = 'none'; authAlert.offsetHeight; authAlert.style.animation = 'alertShake 0.3s ease'; }
      }

      registerForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const companyName = document.getElementById('companyName').value.trim();
        const regNumber = document.getElementById('regNumber').value.trim();
        const contactName = document.getElementById('contactName').value.trim();
        const contactEmail = document.getElementById('contactEmail').value.trim();
        const agreed = document.getElementById('agree-partner-terms').checked;

        if (!companyName) { showAlert('Please enter your legal company name.'); return; }
        if (!regNumber) { showAlert('Please enter your company registration number.'); return; }
        if (!contactName) { showAlert('Please enter the primary contact name.'); return; }
        if (!contactEmail || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(contactEmail)) { showAlert('Please enter a valid contact email address.'); return; }
        if (!agreed) { showAlert('You must confirm authorization and agree to the Partner Agreement.'); return; }

        submitBtn.classList.add('is-loading');
        submitBtn.disabled = true;
        setTimeout(() => {
          showAlert('Enterprise registration for ' + companyName + ' submitted! Redirecting to login...', false);
          try { sessionStorage.setItem('vp_partner_reg', JSON.stringify({ companyName, regNumber, contactName, contactEmail, status: 'pending_review', timestamp: new Date().toISOString() })); } catch (err) {}
          setTimeout(() => {
            window.location.href = 'login.php';
          }, 1600);
        }, 700);
      });
    });
  </script>
</body>
</html>