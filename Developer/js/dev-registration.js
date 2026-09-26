/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 10: Partner Registration & Enclave Access Module
 */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    // Preset auto-fill for BaltNord CUS-1002
    const btnAutofill = document.getElementById("btn-autofill-baltnord");
    if (btnAutofill) {
      btnAutofill.addEventListener("click", () => {
        document.getElementById("reg-company").value =
          "BaltNord Process Systems";
        document.getElementById("reg-partner-id").value = "CUS-1002";
        document.getElementById("reg-contact-name").value = "Kristaps Ozols";
        document.getElementById("reg-contact-email").value =
          "kristaps.ozols@baltnord.lv";
        document.getElementById("reg-project-ref").value =
          "PRJ-2026-002 (SCADA Ingestion Bridge)";
        document.getElementById("reg-env").value = "sandbox";
        document.getElementById("reg-public-key").value =
          "ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIBmQ8eBaltNordScadaGatewayKey2026";
        document.getElementById("reg-compliance-doc").checked = true;
        document.getElementById("reg-compliance-iec").checked = true;
        document.getElementById("reg-compliance-nda").checked = true;

        // Select standard scopes
        document.querySelectorAll(".scope-card-option").forEach((card) => {
          const chk = card.querySelector('input[type="checkbox"]');
          if (
            chk.value === "telemetry:read" ||
            chk.value === "orders:read_write"
          ) {
            chk.checked = true;
            card.classList.add("selected");
          }
        });

        window.showToast(
          "PRESET LOADED",
          "Loaded authoritative partner baseline for BaltNord Process Systems (CUS-1002).",
          "info",
          "dataset",
        );
      });
    }

    // Scope option card click handler
    document.querySelectorAll(".scope-card-option").forEach((card) => {
      card.addEventListener("click", function (e) {
        if (e.target.tagName.toLowerCase() !== "input") {
          const checkbox = this.querySelector('input[type="checkbox"]');
          checkbox.checked = !checkbox.checked;
        }
        const chk = this.querySelector('input[type="checkbox"]');
        if (chk.checked) {
          this.classList.add("selected");
        } else {
          this.classList.remove("selected");
        }
      });
    });

    // Form Submission
    const regForm = document.getElementById("partner-registration-form");
    const regSuccessBox = document.getElementById("reg-success-container");

    if (regForm) {
      regForm.addEventListener("submit", (e) => {
        e.preventDefault();

        const company = document.getElementById("reg-company").value.trim();
        const contact = document
          .getElementById("reg-contact-name")
          .value.trim();
        const email = document.getElementById("reg-contact-email").value.trim();
        const env = document.getElementById("reg-env").value;

        if (!company || !contact || !email) {
          window.showToast(
            "VALIDATION ERROR",
            "Please complete all required fields.",
            "error",
            "error",
          );
          return;
        }

        const submitBtn = regForm.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML =
          '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span> Validating Credentials...';

        setTimeout(() => {
          regForm.style.display = "none";
          if (regSuccessBox) {
            regSuccessBox.style.display = "block";
            document.getElementById("disp-reg-ticket").innerText =
              "ENCLAVE-REQ-" + Math.floor(100000 + Math.random() * 900000);
            document.getElementById("disp-reg-company").innerText = company;
            document.getElementById("disp-reg-contact").innerText =
              contact + " (" + email + ")";
            document.getElementById("disp-reg-env").innerText =
              env.toUpperCase();
          }

          window.showToast(
            "REGISTRATION SUBMITTED",
            "Clearance request routed to Jonas Richter (EMP-1020) & Dana Yermak (EMP-1017).",
            "success",
            "verified_user",
          );
        }, 1000);
      });
    }
  });
})();
