/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 09: File Center / Document Hub
 * Document Approvals & Electronic Signoff Module (Live Database Integration)
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
      formSign.addEventListener("submit", async (e) => {
        e.preventDefault();

        const docId = document.getElementById("sign-doc-id") ? document.getElementById("sign-doc-id").value : "DOC-2026-004";
        const submitBtn = formSign.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML =
          '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span> Affixing HSM Signature to MySQL...';

        try {
          const response = await fetch("api/approvals.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              action: "sign",
              doc_id: docId,
              comments: "Certified technical deliverable and authorized release to L4 Governance",
            }),
          });

          const res = await response.json();

          if (res.success && res.data) {
            const data = res.data;
            signModal.style.display = "none";

            // Update Stepper
            if (step2) {
              step2.classList.remove("active");
              step2.classList.add("completed");
              const circle = step2.querySelector(".step-circle");
              if (circle) {
                circle.innerHTML = '<span class="material-symbols-outlined text-[18px]">done</span>';
              }
              const subtext = step2.querySelector(".fc-font-family-var-font-c0ef");
              if (subtext) {
                subtext.innerHTML = 'SIGNED &amp; ATTESTED';
                subtext.style.color = 'var(--vk-secondary)';
              }
            }
            if (step3) {
              step3.classList.add("active");
            }
            if (stepperFill) {
              stepperFill.style.width = "85%";
            }

            // Update Badge
            if (approvalBadge) {
              approvalBadge.textContent = data.approval_status || "PM APPROVED // IN GOVERNANCE REVIEW";
              approvalBadge.className = "vk-status-badge status-approved";
            }

            // Disable button and show signed stamp
            if (btnOpenSignModal) {
              btnOpenSignModal.disabled = true;
              btnOpenSignModal.innerHTML = `<span class="material-symbols-outlined text-[16px]">verified</span> Signed by ${data.signatory_name} (${data.signatory_id})`;
              btnOpenSignModal.classList.remove("vk-btn-primary");
              btnOpenSignModal.classList.add("vk-btn-outline");
              btnOpenSignModal.style.borderColor = "var(--vk-secondary)";
              btnOpenSignModal.style.color = "var(--vk-secondary)";
            }

            // Show success toast
            if (window.showToast) {
              window.showToast(
                "DIGITAL SIGNATURE AFFIXED",
                `${docId} approved and recorded in MySQL database. Token: ${data.token}. Routed to Timur Akhmetov for L4 Governance clearance.`,
                "success",
                "draw"
              );
            }
          } else {
            alert("Signoff error: " + (res.message || "Failed to record signature"));
          }
        } catch (err) {
          alert("Network or database error: " + err.message);
        } finally {
          submitBtn.disabled = false;
          submitBtn.innerHTML =
            '<span class="material-symbols-outlined text-[16px]">draw</span> Affix Signature &amp; Release';
        }
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
        if (window.showToast) {
          window.showToast(
            "CUSTOMER VIEW APPLIED",
            "Masked proprietary PLC memory maps and internal Almaty IP topology.",
            "info",
            "lock"
          );
        }
      } else {
        toggleBtn.innerHTML =
          '<span class="material-symbols-outlined text-[16px]">visibility_off</span> Preview Redacted (Customer Portal)';
        document.querySelectorAll(".redact-target").forEach((el) => {
          el.classList.remove("redacted-bar");
        });
        if (window.showToast) {
          window.showToast(
            "INTERNAL SPEC VIEW",
            "Displaying full engineering specification with raw memory registers.",
            "info",
            "visibility"
          );
        }
      }
    });
  }
})();
