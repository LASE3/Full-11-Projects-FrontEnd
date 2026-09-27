/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 09: File Center / Document Hub
 * Archival Governance, Retention Lifecycle & Legal Holds Module (Live Database Integration)
 */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    initLegalHoldToggles();
    initArchiveExport();
  });

  function initLegalHoldToggles() {
    document.addEventListener("click", async function (e) {
      const btn = e.target.closest(".btn-toggle-hold");
      if (!btn) return;

      const docId = btn.getAttribute("data-doc-id");
      const isCurrentlyHeld = btn.classList.contains("active-hold");

      btn.disabled = true;
      btn.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin">sync</span> Updating...';

      try {
        const response = await fetch("api/retention.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            doc_id: docId,
            reason: "Preservation order: Litigation and statutory audit freeze",
          }),
        });

        const res = await response.json();

        if (res.success && res.data) {
          const isHeld = res.data.is_legal_hold === 1;

          if (isHeld) {
            btn.classList.add("active-hold");
            btn.classList.remove("vk-btn-outline");
            btn.classList.add("vk-btn-accent");
            btn.innerHTML = '<span class="material-symbols-outlined text-[14px]">lock</span> Hold Active';

            if (window.showToast) {
              window.showToast(
                "LEGAL HOLD ENFORCED",
                `Document ${docId} locked under statutory preservation order in database. Purging suspended.`,
                "warning",
                "gavel"
              );
            }
          } else {
            btn.classList.remove("active-hold");
            btn.classList.remove("vk-btn-accent");
            btn.classList.add("vk-btn-outline");
            btn.innerHTML = '<span class="material-symbols-outlined text-[14px]">lock_open</span> Apply Legal Hold';

            if (window.showToast) {
              window.showToast(
                "LEGAL HOLD RELEASED",
                `Document ${docId} released in database. Restored to standard retention lifecycle.`,
                "info",
                "lock_open"
              );
            }
          }

          // Update active holds counter
          updateHoldsCount();
        } else {
          alert("Hold toggle error: " + (res.message || "Failed to update legal hold"));
        }
      } catch (err) {
        alert("Network or database error: " + err.message);
      } finally {
        btn.disabled = false;
      }
    });
  }

  function updateHoldsCount() {
    const activeHoldBtns = document.querySelectorAll(".btn-toggle-hold.active-hold").length;
    const badge = document.getElementById("holds-count-badge");
    if (badge) {
      badge.textContent = activeHoldBtns;
    }
  }

  function initArchiveExport() {
    const btnExport = document.getElementById("btn-export-archival-manifest");
    if (!btnExport) return;

    btnExport.addEventListener("click", () => {
      btnExport.disabled = true;
      btnExport.innerHTML =
        '<span class="material-symbols-outlined text-[14px] animate-spin">sync</span> Querying Database Manifest...';

      // Trigger browser download from API
      window.location.href = "api/retention.php?action=export_manifest&download=1";

      setTimeout(() => {
        btnExport.disabled = false;
        btnExport.innerHTML =
          '<span class="material-symbols-outlined text-[14px]">download</span> Export Archival Manifest';
        if (window.showToast) {
          window.showToast(
            "ARCHIVAL MANIFEST EXPORTED",
            "Cryptographically sealed ISO 27001 audit manifest generated directly from MySQL database.",
            "success",
            "verified"
          );
        }
      }, 1200);
    });
  }
})();
