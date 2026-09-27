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

    btnVerify.addEventListener("click", async () => {
      const hash = inputHash.value.trim();
      const docId = selectDoc ? selectDoc.value.trim() : "";

      if (!hash) {
        window.showToast(
          "INPUT REQUIRED",
          "Please paste a SHA-256 hash or select a document from the register.",
          "error",
          "error"
        );
        return;
      }

      btnVerify.disabled = true;
      btnVerify.innerHTML =
        '<span class="material-symbols-outlined text-[14px] animate-spin">sync</span> Querying Hardware HSM Ledger...';

      try {
        const res = await fetch("api/audit.php?action=verify_hash", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Accept": "application/json"
          },
          body: JSON.stringify({
            doc_id: docId,
            hash: hash
          })
        });

        const json = await res.json();

        if (!res.ok || !json.success) {
          throw new Error(json.message || "Cryptographic integrity mismatch or lookup error.");
        }

        const data = json.data;
        if (resultBox) {
          resultBox.style.display = "block";
          const dispHash = document.getElementById("verify-disp-hash");
          const dispTime = document.getElementById("verify-disp-timestamp");
          if (dispHash) dispHash.textContent = data.digest || hash;
          if (dispTime) dispTime.textContent = (data.timestamp || new Date().toISOString()) + (data.file_name ? " // " + data.file_name : "");
        }

        window.showToast(
          "INTEGRITY CONFIRMED",
          json.message || "Bitwise match verified against Almaty central hardware HSM enclave. Zero tampering detected.",
          "success",
          "verified"
        );
      } catch (err) {
        if (resultBox) {
          resultBox.style.display = "none";
        }
        window.showToast(
          "VERIFICATION FAILED",
          err.message || "Supplied hash does not match vault root hash.",
          "error",
          "gpp_bad"
        );
      } finally {
        btnVerify.disabled = false;
        btnVerify.innerHTML =
          '<span class="material-symbols-outlined text-[14px]">security</span> Verify Cryptographic Seal';
      }
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
