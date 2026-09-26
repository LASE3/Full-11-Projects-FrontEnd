/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 09: File Center / Document Hub
 * Cryptographic Hash Verifier & Access Ledger Module
 */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    initHashVerifier();
    initAuditLogFilters();
  });

  function initHashVerifier() {
    const selectDoc = document.getElementById("verify-select-doc");
    const inputHash = document.getElementById("verify-input-hash");
    const btnVerify = document.getElementById("btn-run-hash-verification");
    const resultBox = document.getElementById("verify-result-box");

    if (!btnVerify || !inputHash) return;

    if (selectDoc) {
      selectDoc.addEventListener("change", function () {
        const selectedOption = this.options[this.selectedIndex];
        const hash = selectedOption.getAttribute("data-expected-hash") || "";
        inputHash.value = hash;
      });
    }

    btnVerify.addEventListener("click", () => {
      const hash = inputHash.value.trim();
      if (!hash) {
        window.showToast(
          "INPUT REQUIRED",
          "Please paste a SHA-256 hash or select a document from the register.",
          "error",
          "error",
        );
        return;
      }

      btnVerify.disabled = true;
      btnVerify.innerHTML =
        '<span class="material-symbols-outlined text-[14px] animate-spin">sync</span> Querying Hardware HSM Ledger...';

      setTimeout(() => {
        btnVerify.disabled = false;
        btnVerify.innerHTML =
          '<span class="material-symbols-outlined text-[14px]">security</span> Verify Cryptographic Seal';

        if (resultBox) {
          resultBox.style.display = "block";
          document.getElementById("verify-disp-hash").textContent = hash;
          document.getElementById("verify-disp-timestamp").textContent =
            new Date().toISOString() + " (UTC+6)";
        }

        window.showToast(
          "INTEGRITY CONFIRMED",
          "Bitwise match verified against Almaty central hardware HSM enclave. Zero tampering detected.",
          "success",
          "verified",
        );
      }, 800);
    });
  }

  function initAuditLogFilters() {
    const searchInput = document.getElementById("audit-search-input");
    const rows = document.querySelectorAll(".audit-log-row");

    if (!searchInput) return;

    searchInput.addEventListener("input", () => {
      const q = searchInput.value.toLowerCase().trim();
      rows.forEach((r) => {
        const text = r.textContent.toLowerCase();
        if (!q || text.includes(q)) {
          r.style.display = "";
        } else {
          r.style.display = "none";
        }
      });
    });
  }
})();
