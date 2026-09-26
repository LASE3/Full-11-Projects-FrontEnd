/**
 * VOSTOKPRIBOR B2B Partner Registration Form Handler
 */
document.addEventListener("DOMContentLoaded", () => {
  const registerForm = document.getElementById("register-form");
  if (!registerForm) return;

  const submitBtn = document.getElementById("submit-btn");
  const authAlert = document.getElementById("auth-alert");
  const alertMessage = document.getElementById("alert-message");

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

  registerForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const companyName = document.getElementById("companyName")?.value.trim() || "";
    const regNumber = document.getElementById("regNumber")?.value.trim() || "";
    const contactName = document.getElementById("contactName")?.value.trim() || "";
    const contactEmail = document.getElementById("contactEmail")?.value.trim() || "";
    const agreed = document.getElementById("agree-partner-terms")?.checked;

    if (!companyName) {
      showAlert("Please enter your legal company name.");
      return;
    }
    if (!regNumber) {
      showAlert("Please enter your company registration number.");
      return;
    }
    if (!contactName) {
      showAlert("Please enter the primary contact name.");
      return;
    }
    if (!contactEmail || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(contactEmail)) {
      showAlert("Please enter a valid contact email address.");
      return;
    }
    if (!agreed) {
      showAlert("You must confirm authorization and agree to the Partner Agreement.");
      return;
    }

    if (submitBtn) {
      submitBtn.classList.add("is-loading");
      submitBtn.disabled = true;
    }

    setTimeout(() => {
      showAlert("Enterprise registration for " + companyName + " submitted! Redirecting to login...", false);
      try {
        sessionStorage.setItem(
          "vp_partner_reg",
          JSON.stringify({
            companyName,
            regNumber,
            contactName,
            contactEmail,
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
