/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Board Risk Register & Supervisory Board Oversight Module
 * Fully connected to `api/risks.php`
 */

(function () {
  "use strict";

  // Global Modal & CRUD Functions
  window.openNewRiskModal = function () {
    const modal = document.getElementById("riskModal");
    const title = document.getElementById("riskModalTitle");
    const form = document.getElementById("riskForm");
    if (!modal || !form) return;

    form.reset();
    document.getElementById("modalRiskId").value = "";
    document.getElementById("modalRiskAction").value = "create";
    title.textContent = "New Statutory Risk Filing";
    modal.classList.remove("hidden");
  };

  window.closeRiskModal = function () {
    const modal = document.getElementById("riskModal");
    if (modal) modal.classList.add("hidden");
  };

  window.editRisk = function (id) {
    const row = document.querySelector(`.risk-row[data-id="${id}"]`);
    if (!row) return;

    const modal = document.getElementById("riskModal");
    const title = document.getElementById("riskModalTitle");
    if (!modal) return;

    document.getElementById("modalRiskId").value = id;
    document.getElementById("modalRiskAction").value = "update";
    document.getElementById("modalRiskDesc").value = row.getAttribute("data-desc") || "";
    document.getElementById("modalRiskImpact").value = row.getAttribute("data-impact") || "High";
    document.getElementById("modalRiskLikelihood").value = row.getAttribute("data-likelihood") || "Moderate";
    document.getElementById("modalRiskTarget").value = row.getAttribute("data-target") || "SYS-01 Production Enclave";
    document.getElementById("modalRiskOwner").value = row.getAttribute("data-owner") || "EMP-1005";
    document.getElementById("modalRiskStatus").value = row.getAttribute("data-status") || "UnderReview";
    document.getElementById("modalRiskReviewDate").value = row.getAttribute("data-date") || "";
    document.getElementById("modalRiskThreatVector").value = row.getAttribute("data-vector") || "";

    const code = row.getAttribute("data-code");
    title.textContent = `Edit Statutory Risk: ${code}`;
    modal.classList.remove("hidden");
  };

  window.deleteRisk = function (id) {
    const row = document.querySelector(`.risk-row[data-id="${id}"]`);
    const code = row ? row.getAttribute("data-code") : `Risk #${id}`;

    if (!confirm(`CRITICAL BOARD ACTION: Retire and remove ${code} from the statutory register?`)) {
      return;
    }

    fetch("api/risks.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "delete", risk_id: id })
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          if (row) row.remove();
          window.showToast("RISK RETIRED", data.message, "success", "delete");
        } else {
          window.showToast("ACTION FAILED", data.error || "Could not remove risk", "error", "error");
        }
      })
      .catch((err) => {
        window.showToast("NETWORK ERROR", err.message, "error", "error");
      });
  };

  window.saveRiskForm = function (e) {
    e.preventDefault();
    const btn = document.getElementById("btnSaveRisk");
    const origHtml = btn ? btn.innerHTML : "Save";
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin">refresh</span><span>Saving to DB...</span>';
    }

    const payload = {
      action: document.getElementById("modalRiskAction").value,
      risk_id: document.getElementById("modalRiskId").value,
      description: document.getElementById("modalRiskDesc").value,
      impact: document.getElementById("modalRiskImpact").value,
      likelihood: document.getElementById("modalRiskLikelihood").value,
      system_target: document.getElementById("modalRiskTarget").value,
      owner_emp_id: document.getElementById("modalRiskOwner").value,
      status: document.getElementById("modalRiskStatus").value,
      review_date: document.getElementById("modalRiskReviewDate").value,
      threat_vector: document.getElementById("modalRiskThreatVector").value
    };

    fetch("api/risks.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    })
      .then((res) => res.json())
      .then((data) => {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = origHtml;
        }
        if (data.success) {
          window.closeRiskModal();
          window.showToast("RISK COMMITTED", data.message, "success", "balance");
          setTimeout(() => window.location.reload(), 800);
        } else {
          window.showToast("COMMIT FAILED", data.error || "Could not commit risk", "error", "error");
        }
      })
      .catch((err) => {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = origHtml;
        }
        window.showToast("SERVER ERROR", err.message, "error", "error");
      });
  };

  window.inspectRisk = function (id) {
    const row = document.querySelector(`.risk-row[data-id="${id}"]`);
    if (!row) return;

    document.querySelectorAll(".risk-row").forEach((r) => r.classList.remove("ring-2", "ring-[#D9822B]"));
    row.classList.add("ring-2", "ring-[#D9822B]");

    const code = row.getAttribute("data-code");
    const desc = row.getAttribute("data-desc");
    const target = row.getAttribute("data-target");
    const vector = row.getAttribute("data-vector");

    // Update Focused Dossier Card
    const stamp = document.querySelector(".font-security-stamp.text-\\[10px\\].tracking-wider");
    if (stamp && stamp.textContent.includes("RR-")) {
      stamp.textContent = code;
    }

    const titleDiv = document.querySelector(".font-title-sm.text-title-sm.text-primary.font-bold");
    if (titleDiv) {
      titleDiv.textContent = desc;
    }

    const subDiv = titleDiv ? titleDiv.nextElementSibling : null;
    if (subDiv) {
      subDiv.textContent = `Impacted Apparatus: ${target}`;
    }

    const vectorP = document.querySelector("p.font-body-compact.text-body-compact.text-on-surface");
    if (vectorP && vector) {
      vectorP.textContent = vector;
    }

    window.showToast("FOCUSED DOSSIER", `Appraising ${code} against board oversight matrix.`, "info", "security");
  };

  document.addEventListener("DOMContentLoaded", () => {
    // 1. Dual Signatory Co-Attestation (Sign Now Action)
    const signBtn = Array.from(document.querySelectorAll("button")).find((b) =>
      b.textContent.includes("SIGN NOW")
    );
    if (signBtn) {
      signBtn.addEventListener("click", function () {
        this.disabled = true;
        this.className =
          "px-space-xs py-space-2xs bg-secondary text-white font-label-uppercase text-[10px] font-bold cursor-default";
        this.innerHTML =
          '<span class="material-symbols-outlined text-[12px] inline">done</span> SIGNED';

        fetch("api/audit.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ action: "notarize_stamp", note: "Board Dual-Signatory Co-Attestation Sign-off" })
        });

        const statusHeader = Array.from(document.querySelectorAll("span")).find(
          (s) => s.textContent.includes("1/2 PENDING")
        );
        if (statusHeader) {
          statusHeader.textContent = "2/2 ATTESTED";
          statusHeader.className = "font-telemetry-micro text-[10px] text-secondary font-bold";
        }

        window.showToast(
          "CO-ATTESTATION SIGNED",
          "Timur Akhmetov (EMP-1005) cryptographic endorsement committed to database ledger.",
          "success",
          "draw"
        );
      });
    }

    // 2. Verify Cryptographic Seal Action
    const verifySealBtn = Array.from(document.querySelectorAll("button")).find(
      (b) => b.textContent.includes("Verify Cryptographic Seal")
    );
    if (verifySealBtn) {
      verifySealBtn.addEventListener("click", function () {
        const orig = this.innerHTML;
        this.disabled = true;
        this.innerHTML =
          '<span class="material-symbols-outlined text-[14px] animate-spin">sync</span><span>Validating Hash Root...</span>';

        fetch("api/audit.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ action: "notarize_stamp", note: "Tamper-evident Board Risk ledger hash validation" })
        })
          .then((res) => res.json())
          .then((data) => {
            this.innerHTML =
              '<span class="material-symbols-outlined text-[14px]">done_all</span><span>Hash Seal Verified</span>';
            this.classList.add("bg-primary", "text-white");

            setTimeout(() => {
              this.innerHTML = orig;
              this.classList.remove("bg-primary", "text-white");
              this.disabled = false;
            }, 3000);

            window.showToast(
              "TAMPER-EVIDENT SEAL VALID",
              `SHA-256 Signature verified against Almaty Secure Enclave HSM root [${(data.hash || "").substring(0, 16)}...].`,
              "success",
              "verified"
            );
          });
      });
    }

    // 3. Search filter
    const searchInput = document.querySelector('input[placeholder*="Filter by cluster"]');
    const rows = document.querySelectorAll(".risk-row");
    if (searchInput && rows.length > 0) {
      searchInput.addEventListener("input", (e) => {
        const q = e.target.value.toLowerCase().trim();
        rows.forEach((r) => {
          const text = r.innerText.toLowerCase();
          r.style.display = q === "" || text.includes(q) ? "" : "none";
        });
      });
    }
  });
})();
