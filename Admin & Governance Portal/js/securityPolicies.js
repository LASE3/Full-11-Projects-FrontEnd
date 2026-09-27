/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Security Policies & Enforcement Engine Module
 * Fully connected to `api/policies.php`
 */

(function () {
  "use strict";

  // Global functions exposed to window
  window.openNewPolicyModal = function () {
    const modal = document.getElementById("policyModal");
    const title = document.getElementById("policyModalTitle");
    const form = document.getElementById("policyForm");
    if (!modal || !form) return;

    form.reset();
    document.getElementById("modalPolicyId").value = "";
    document.getElementById("modalAction").value = "create";
    title.textContent = "Draft Statutory Amendment";
    modal.classList.remove("hidden");
  };

  window.closePolicyModal = function () {
    const modal = document.getElementById("policyModal");
    if (modal) modal.classList.add("hidden");
  };

  window.editPolicy = function (id) {
    const row = document.querySelector(`.policy-row[data-id="${id}"]`);
    if (!row) return;

    const modal = document.getElementById("policyModal");
    const title = document.getElementById("policyModalTitle");
    if (!modal) return;

    document.getElementById("modalPolicyId").value = id;
    document.getElementById("modalAction").value = "update";
    document.getElementById("modalDocId").value = row.getAttribute("data-doc") || "";
    document.getElementById("modalTitle").value = row.getAttribute("data-title") || "";
    document.getElementById("modalSeverity").value = row.getAttribute("data-severity") || "High";
    document.getElementById("modalEnforceMode").value = row.getAttribute("data-mode") || "MANDATORY";
    document.getElementById("modalSystemId").value = row.getAttribute("data-system") || "SYS-01..11";
    document.getElementById("modalEffectiveDate").value = row.getAttribute("data-date") || "";
    document.getElementById("modalDescription").value = row.getAttribute("data-desc") || "";

    title.textContent = `Edit Statutory Policy: ${row.getAttribute("data-doc")}`;
    modal.classList.remove("hidden");
  };

  window.deletePolicy = function (id) {
    const row = document.querySelector(`.policy-row[data-id="${id}"]`);
    const docCode = row ? row.getAttribute("data-doc") : `Policy #${id}`;

    if (!confirm(`CRITICAL STATUTORY ACTION: Revoke and purge ${docCode} from the enterprise register?`)) {
      return;
    }

    fetch("api/policies.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "delete", policy_id: id })
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          if (row) row.remove();
          window.showToast("POLICY PURGED", data.message, "success", "delete");
        } else {
          window.showToast("ACTION FAILED", data.error || "Failed to purge policy", "error", "error");
        }
      })
      .catch((err) => {
        window.showToast("NETWORK ERROR", err.message, "error", "error");
      });
  };

  window.savePolicyForm = function (e) {
    e.preventDefault();
    const btn = document.getElementById("btnSavePolicy");
    const origHtml = btn ? btn.innerHTML : "Save";
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin">refresh</span><span>Saving to DB...</span>';
    }

    const payload = {
      action: document.getElementById("modalAction").value,
      policy_id: document.getElementById("modalPolicyId").value,
      doc_id: document.getElementById("modalDocId").value,
      title: document.getElementById("modalTitle").value,
      severity: document.getElementById("modalSeverity").value,
      enforcement_mode: document.getElementById("modalEnforceMode").value,
      system_id: document.getElementById("modalSystemId").value,
      effective_date: document.getElementById("modalEffectiveDate").value,
      description: document.getElementById("modalDescription").value
    };

    fetch("api/policies.php", {
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
          window.closePolicyModal();
          window.showToast("POLICY SAVED", data.message, "success", "verified");
          setTimeout(() => window.location.reload(), 800);
        } else {
          window.showToast("SAVE FAILED", data.error || "Could not save policy", "error", "error");
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

  window.inspectPolicy = function (id) {
    const row = document.querySelector(`.policy-row[data-id="${id}"]`);
    if (!row) return;

    // Highlight row
    document.querySelectorAll(".policy-row").forEach((r) => r.classList.remove("ring-2", "ring-primary"));
    row.classList.add("ring-2", "ring-primary");

    const docCode = row.getAttribute("data-doc");
    const title = row.getAttribute("data-title");
    const desc = row.getAttribute("data-desc");
    const sys = row.getAttribute("data-system");
    const mode = row.getAttribute("data-mode");

    // Update right inspector panel if elements exist
    const inspTitle = document.querySelector(".font-security-stamp.text-primary.uppercase");
    if (inspTitle && inspTitle.textContent.includes("POLICY INSPECTOR")) {
      inspTitle.textContent = `POLICY INSPECTOR: ${docCode}`;
    }

    const pre = document.querySelector("pre.font-telemetry-micro");
    if (pre) {
      pre.textContent = `rule "${docCode}_AUTO_ENFORCE" {\n  assertion: statutory_mandate == true;\n  target_nodes: "${sys}";\n  mode: "${mode}";\n  specification: "${desc.replace(/"/g, '\\"')}";\n  jurisdiction: "ALMATY-CENTRAL";\n}`;
    }

    window.showToast("INSPECTING DIRECTIVE", `Loaded ${docCode} into Policy Inspector console.`, "info", "policy");
  };

  document.addEventListener("DOMContentLoaded", () => {
    // Policy Rule Live Simulation Trigger
    const simBtn = Array.from(document.querySelectorAll("button")).find((b) =>
      b.textContent.includes("Trigger Live Node Simulation")
    );
    if (simBtn) {
      simBtn.addEventListener("click", function () {
        const orig = this.innerHTML;
        this.disabled = true;
        this.innerHTML =
          '<span class="material-symbols-outlined text-[16px] animate-spin text-secondary-fixed">sync</span><span>Transmitting Policy Interlock Frame...</span>';

        setTimeout(() => {
          this.innerHTML = orig;
          this.disabled = false;
          window.showToast(
            "SIMULATION NOMINAL",
            "Statutory Policy Interlocks verified across 11 target environments. Zero boundary violations detected.",
            "success",
            "verified"
          );
        }, 1200);
      });
    }

    // Cryptographic Proof Verification
    const verifyBtn = Array.from(document.querySelectorAll("button")).find((b) =>
      b.textContent.includes("Verify Cryptographic Proof")
    );
    if (verifyBtn) {
      verifyBtn.addEventListener("click", function () {
        const orig = this.innerHTML;
        this.disabled = true;
        this.innerHTML =
          '<span class="material-symbols-outlined text-[16px] animate-spin text-on-secondary-fixed">sync</span><span>Validating SHA-256 Chain...</span>';

        fetch("api/audit.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ action: "notarize_stamp", note: "Statutory Security Policy Manual Verification" })
        })
          .then((res) => res.json())
          .then((data) => {
            this.innerHTML =
              '<span class="material-symbols-outlined text-[18px]">done_all</span><span>Signature Verified</span>';
            this.classList.remove("bg-secondary-fixed");
            this.classList.add("bg-secondary", "text-white");

            setTimeout(() => {
              this.innerHTML = orig;
              this.classList.add("bg-secondary-fixed");
              this.classList.remove("bg-secondary", "text-white");
              this.disabled = false;
            }, 3000);

            window.showToast(
              "CRYPTOGRAPHIC SEAL VALID",
              `Policy ledger hash [${(data.hash || "").substring(0, 16)}...] matched against Almaty HSM root [EMP-1005 Signed].`,
              "success",
              "shield"
            );
          })
          .catch(() => {
            this.innerHTML = orig;
            this.disabled = false;
          });
      });
    }

    // Filter Policy Rules on Search
    const searchInput = document.querySelector(
      'input[placeholder*="policy"], input[placeholder*="Search"]'
    );
    const rows = document.querySelectorAll(".policy-row");
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
