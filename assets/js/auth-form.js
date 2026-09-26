/**
 * VOSTOKPRIBOR Centralized Authentication Client
 * Provides shared event binding, password reveal toggling, AJAX credential verification,
 * and session state propagation across all 11 enterprise login pages.
 */
document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("login-form");
  if (!loginForm) return;

  const userIdInput = document.getElementById("userId");
  const passwordInput = document.getElementById("password");
  const submitBtn = document.getElementById("submit-btn");
  const authAlert = document.getElementById("auth-alert");
  const alertMessage = document.getElementById("alert-message");
  const togglePasswordBtn = document.getElementById("toggle-password-btn");
  const eyeIcon = document.getElementById("eye-icon");

  // Toggle password visibility
  if (togglePasswordBtn && passwordInput && eyeIcon) {
    togglePasswordBtn.addEventListener("click", () => {
      const isPassword = passwordInput.getAttribute("type") === "password";
      passwordInput.setAttribute("type", isPassword ? "text" : "password");
      eyeIcon.innerHTML = isPassword
        ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>'
        : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
    });
  }

  function showAlert(msg, isError = true) {
    if (!authAlert || !alertMessage) return;
    authAlert.className = isError ? "auth-alert active-error" : "auth-alert active-success";
    alertMessage.textContent = msg;
    if (typeof updateLoginTheme === "function") updateLoginTheme(msg);
    if (isError) {
      authAlert.style.animation = "none";
      authAlert.offsetHeight;
      authAlert.style.animation = "alertShake 0.3s ease";
    }
  }

  function hideAlert() {
    if (!authAlert) return;
    authAlert.className = "auth-alert";
    if (typeof updateLoginTheme === "function") {
      updateLoginTheme(document.getElementById("login-heading")?.textContent || "Welcome");
    }
  }

  loginForm.addEventListener("submit", (e) => {
    e.preventDefault();
    hideAlert();

    const userId = userIdInput ? userIdInput.value.trim() : "";
    const password = passwordInput ? passwordInput.value : "";
    if (!userId) {
      showAlert("Please enter your User ID or email.", true);
      return;
    }
    if (!password) {
      showAlert("Please enter your password.", true);
      return;
    }

    if (submitBtn) {
      submitBtn.classList.add("is-loading");
      submitBtn.disabled = true;
    }

    const formData = new FormData(loginForm);
    formData.append("ajax", "1");

    fetch("../api/auth.php", {
      method: "POST",
      headers: {
        Accept: "application/json"
      },
      body: formData
    })
      .then(async (res) => {
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success) {
          showAlert(data.message || `Access Granted for ${userId}!`, false);
          try {
            localStorage.setItem("vostok_authenticated", "true");
            sessionStorage.setItem("vostok_authenticated", "true");
            const userData = JSON.stringify(data.user || { userId: userId });
            sessionStorage.setItem("vp_auth_user", userData);
            const sysKey = loginForm.getAttribute("data-system-key");
            if (sysKey) {
              sessionStorage.setItem(sysKey, userData);
            }
          } catch (err) {}

          const fallbackRedirect = loginForm.getAttribute("data-default-redirect") || "Dashboard.php";
          setTimeout(() => {
            window.location.href = data.redirect || fallbackRedirect;
          }, 600);
        } else {
          if (submitBtn) {
            submitBtn.classList.remove("is-loading");
            submitBtn.disabled = false;
          }
          showAlert(data.message || "Authentication failed: Invalid credentials or insufficient clearance.", true);
        }
      })
      .catch((err) => {
        if (submitBtn) {
          submitBtn.classList.remove("is-loading");
          submitBtn.disabled = false;
        }
        showAlert("Database connection error: " + err.message, true);
      });
  });

  window.handleForgotCredentials = function () {
    const customMsg = loginForm.getAttribute("data-recovery-msg");
    showAlert(customMsg || "Security Advisory: Contact VP-SecOps Root Authority (ext. 1001) for clearance recovery.", true);
  };
});
