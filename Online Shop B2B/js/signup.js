/**
 * VOSTOKPRIBOR B2B Account Signup Form Handler
 */
document.addEventListener("DOMContentLoaded", () => {
  const signupForm = document.getElementById("signup-form");
  if (!signupForm) return;

  const submitBtn = document.getElementById("submit-btn");
  const authAlert = document.getElementById("auth-alert");
  const alertMessage = document.getElementById("alert-message");
  const passwordInput = document.getElementById("password");
  const confirmPasswordInput = document.getElementById("confirmPassword");
  const togglePasswordBtn = document.getElementById("toggle-password-btn");
  const eyeIcon = document.getElementById("eye-icon");

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
      authAlert.offsetHeight; // trigger reflow
      authAlert.style.animation = "alertShake 0.3s ease";
    }
  }

  function hideAlert() {
    if (!authAlert) return;
    authAlert.className = "auth-alert";
    if (typeof updateLoginTheme === "function") updateLoginTheme("Create B2B Account");
  }

  signupForm.addEventListener("submit", (e) => {
    e.preventDefault();
    hideAlert();
    const fullName = document.getElementById("fullName")?.value.trim() || "";
    const orgName = document.getElementById("orgName")?.value.trim() || "";
    const email = document.getElementById("email")?.value.trim() || "";
    const accountId = document.getElementById("accountId")?.value.trim() || "";
    const password = passwordInput ? passwordInput.value : "";
    const confirmPassword = confirmPasswordInput ? confirmPasswordInput.value : "";
    const agreed = document.getElementById("agree-terms")?.checked;

    if (!fullName) {
      showAlert("Please enter your full name.");
      return;
    }
    if (!orgName) {
      showAlert("Please enter your organization name.");
      return;
    }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      showAlert("Please enter a valid business email address.");
      return;
    }
    if (!accountId) {
      showAlert("Please enter your requested Account ID (e.g. CUS-1099).");
      return;
    }
    if (password.length < 12) {
      showAlert("Password must be at least 12 characters.");
      return;
    }
    if (password !== confirmPassword) {
      showAlert("Passwords do not match. Please re-enter.");
      return;
    }
    if (!agreed) {
      showAlert("You must agree to the Terms of Service to proceed.");
      return;
    }

    if (submitBtn) {
      submitBtn.classList.add("is-loading");
      submitBtn.disabled = true;
    }

    setTimeout(() => {
      showAlert("B2B Account Request Submitted for " + orgName + "! Redirecting to login...", false);
      try {
        sessionStorage.setItem(
          "vp_shop_reg",
          JSON.stringify({
            fullName,
            orgName,
            email,
            accountId,
            status: "pending_review",
            timestamp: new Date().toISOString()
          })
        );
      } catch (err) {}
      setTimeout(() => {
        window.location.href = "login.php";
      }, 1600);
    }, 700);
  });
});
