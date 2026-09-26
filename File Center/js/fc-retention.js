/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 09: File Center / Document Hub
 * Archival Governance, Retention Lifecycle & Legal Holds Module
 */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    initLegalHoldToggles();
    initArchiveExport();
  });

  function initLegalHoldToggles() {
    document.querySelectorAll(".btn-toggle-hold").forEach((btn) => {
      btn.addEventListener("click", function () {
        const docId = this.getAttribute("data-doc-id");
        const isHeld = this.classList.contains("active-hold");

        if (isHeld) {
          this.classList.remove("active-hold");
          this.innerHTML =
            '<span class="material-symbols-outlined text-[14px]">lock_open</span> Apply Legal Hold';
          this.classList.remove("vk-btn-accent");
          this.classList.add("vk-btn-outline");
          window.showToast(
            "LEGAL HOLD RELEASED",
            `Document ${docId} returned to standard statutory retention schedule.`,
            "info",
            "lock_open",
          );
        } else {
          this.classList.add("active-hold");
          this.innerHTML =
            '<span class="material-symbols-outlined text-[14px]">lock</span> Hold Active';
          this.classList.remove("vk-btn-outline");
          this.classList.add("vk-btn-accent");
          window.showToast(
            "LEGAL HOLD ENFORCED",
            `Document ${docId} marked under legal preservation order. Automated purging suspended.`,
            "warning",
            "gavel",
          );
        }
      });
    });
  }

  function initArchiveExport() {
    const btnExport = document.getElementById("btn-export-archival-manifest");
    if (!btnExport) return;

    btnExport.addEventListener("click", () => {
      btnExport.disabled = true;
      btnExport.innerHTML =
        '<span class="material-symbols-outlined text-[14px] animate-spin">sync</span> Generating ISO 27001 Manifest...';

      setTimeout(() => {
        btnExport.disabled = false;
        btnExport.innerHTML =
          '<span class="material-symbols-outlined text-[14px]">download</span> Export Archival Manifest';
        window.showToast(
          "ARCHIVAL MANIFEST READY",
          "Cryptographically signed audit manifest generated (SHA-256 root anchor).",
          "success",
          "verified",
        );
      }, 1000);
    });
  }
})();
