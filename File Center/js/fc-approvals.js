/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 09: File Center / Document Hub
 * Document Approvals & Electronic Signoff Module
 * Baseline Scenario: DOC-2026-004 (PRJ-2026-002 BaltNord) reviewed by Farida Iskakova (EMP-1019)
 */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    initApprovalActions();
    initRedactionToggle();
  });

  function initApprovalActions() {
    const btnOpenSignModal = document.getElementById("btn-open-sign-modal");
    const signModal = document.getElementById("signoff-modal-backdrop");
    const btnCancelSign = document.getElementById("btn-cancel-sign");
    const formSign = document.getElementById("electronic-signature-form");
    const approvalBadge = document.getElementById("doc-approval-status-badge");
    const stepperFill = document.querySelector(".stepper-progress-fill");
    const step2 = document.getElementById("step-pm-approval");
    const step3 = document.getElementById("step-gov-clearance");

    if (btnOpenSignModal && signModal) {
      btnOpenSignModal.addEventListener("click", () => {
        signModal.style.display = "flex";
      });
    }

    if (btnCancelSign && signModal) {
      btnCancelSign.addEventListener("click", () => {
        signModal.style.display = "none";
      });
    }

    if (formSign) {
      formSign.addEventListener("submit", (e) => {
        e.preventDefault();

        const submitBtn = formSign.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML =
          '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span> Affixing HSM Signature...';

        setTimeout(() => {
          signModal.style.display = "none";

          // Update Stepper
          if (step2) {
            step2.classList.remove("active");
            step2.classList.add("completed");
            step2.querySelector(".step-circle").innerHTML =
              '<span class="material-symbols-outlined text-[18px]">done</span>';
          }
          if (step3) {
            step3.classList.add("active");
          }
          if (stepperFill) {
            stepperFill.style.width = "85%";
          }

          // Update Badge
          if (approvalBadge) {
            approvalBadge.textContent = "PM APPROVED // IN GOVERNANCE REVIEW";
            approvalBadge.className = "vk-status-badge status-approved";
          }

          // Disable the approve button and show signed stamp
          if (btnOpenSignModal) {
            btnOpenSignModal.disabled = true;
            btnOpenSignModal.innerHTML =
              '<span class="material-symbols-outlined text-[16px]">verified</span> Signed by Farida Iskakova (EMP-1019)';
            btnOpenSignModal.classList.remove("vk-btn-primary");
            btnOpenSignModal.classList.add("vk-btn-outline");
            btnOpenSignModal.style.borderColor = "var(--vk-secondary)";
            btnOpenSignModal.style.color = "var(--vk-secondary)";
          }

          // Show success toast
          window.showToast(
            "DIGITAL SIGNATURE AFFIXED",
            "DOC-2026-004 approved by Farida Iskakova (EMP-1019). Routed to Timur Akhmetov (EMP-1005) for final L4 governance release.",
            "success",
            "draw",
          );
        }, 900);
      });
    }
  }

  function initRedactionToggle() {
    const toggleBtn = document.getElementById("btn-toggle-redaction");
    const specContent = document.getElementById("spec-preview-content");

    if (!toggleBtn || !specContent) return;

    let isRedacted = false;

    toggleBtn.addEventListener("click", () => {
      isRedacted = !isRedacted;
      if (isRedacted) {
        toggleBtn.innerHTML =
          '<span class="material-symbols-outlined text-[16px]">visibility</span> View Unredacted (Internal Only)';
        document.querySelectorAll(".redact-target").forEach((el) => {
          el.classList.add("redacted-bar");
        });
        window.showToast(
          "CUSTOMER VIEW APPLIED",
          "Masked proprietary PLC memory maps and internal Almaty IP topology.",
          "info",
          "lock",
        );
      } else {
        toggleBtn.innerHTML =
          '<span class="material-symbols-outlined text-[16px]">visibility_off</span> Preview Redacted (Customer Portal)';
        document.querySelectorAll(".redact-target").forEach((el) => {
          el.classList.remove("redacted-bar");
        });
        window.showToast(
          "INTERNAL SPEC VIEW",
          "Displaying full engineering specification with raw memory registers.",
          "info",
          "visibility",
        );
      }
    });
  }
})();
