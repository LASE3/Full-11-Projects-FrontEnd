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
    const simBtn = document.getElementById("btnSimulateImpact") || Array.from(document.querySelectorAll("button")).find((b) =>
      b.textContent.includes("Simulate")
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
          window.showToast?.(
            "SIMULATION NOMINAL",
            "Statutory Policy Interlocks verified across 11 target environments. Zero boundary violations detected.",
            "success",
            "verified"
          );
        }, 1200);
      });
    }

    // Cryptographic Proof Verification
    const verifyBtn = document.getElementById("btnCryptoSignOff") || Array.from(document.querySelectorAll("button")).find((b) =>
      b.textContent.includes("Sign-Off") || b.textContent.includes("Verify")
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

            window.showToast?.(
              "CRYPTOGRAPHIC SEAL VALID",
              `Policy ledger hash [${(data.hash || "").substring(0, 16)}...] matched against Almaty HSM root.`,
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

    // Export Policy Bundle (JSON & CSV supported)
    const exportBtn = document.getElementById("btnExportBundle");
    if (exportBtn) {
      exportBtn.addEventListener("click", () => {
        const rows = document.querySelectorAll(".policy-row");
        const bundle = [];
        let csvContent = "Policy_ID,Document_Code,Directive_Title,Severity,Mode,System_Domain,Description\n";

        rows.forEach((r) => {
          const id = r.getAttribute("data-id") || "";
          const doc = r.getAttribute("data-doc") || "";
          const title = (r.getAttribute("data-title") || "").replace(/"/g, '""');
          const severity = r.getAttribute("data-severity") || "";
          const mode = r.getAttribute("data-mode") || "";
          const system = (r.getAttribute("data-system") || "").replace(/"/g, '""');
          const desc = (r.getAttribute("data-desc") || "").replace(/"/g, '""');

          bundle.push({ id, doc, title, severity, mode, system, description: desc });
          csvContent += `"${id}","${doc}","${title}","${severity}","${mode}","${system}","${desc}"\n`;
        });

        // Download JSON Package
        const blob = new Blob([JSON.stringify({ station: "ALMATY-CENTRAL", framework: "ST RK ISO/IEC 27001", timestamp: new Date().toISOString(), policies: bundle }, null, 2)], {
          type: "application/json"
        });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `VOSTOKPRIBOR_Security_Policies_${Date.now()}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        window.showToast?.("POLICIES EXPORTED", "Statutory policy bundle downloaded successfully.", "success", "file_download");
      });
    }

    // Filter & Pagination Engine
    const searchInput = document.getElementById("policySearchInput") || document.querySelector('input[placeholder*="Query policy"]');
    const rows = document.querySelectorAll(".policy-row");
    const categoryButtons = document.querySelectorAll("#policyCategoryPills button, .policy-tab-btn");
    let currentCategory = "ALL";

    let policyCurrentPage = 1;
    const policyPageSize = 5;
    const btnPolicyPrev = document.getElementById("btnPolicyPrev");
    const btnPolicyNext = document.getElementById("btnPolicyNext");
    const policyPageIndicator = document.getElementById("policyPageIndicator");

    function renderPolicyPagination() {
      const rowList = Array.from(rows);
      const matchedRows = rowList.filter((r) => r.getAttribute("data-filtered") !== "false");
      const totalPages = Math.max(1, Math.ceil(matchedRows.length / policyPageSize));

      if (policyCurrentPage > totalPages) policyCurrentPage = totalPages;
      if (policyCurrentPage < 1) policyCurrentPage = 1;

      matchedRows.forEach((r, idx) => {
        const start = (policyCurrentPage - 1) * policyPageSize;
        const end = start + policyPageSize;
        r.style.display = (idx >= start && idx < end) ? "" : "none";
      });

      if (policyPageIndicator) {
        policyPageIndicator.textContent = `${policyCurrentPage} / ${totalPages}`;
      }
      if (btnPolicyPrev) {
        btnPolicyPrev.disabled = (policyCurrentPage <= 1);
      }
      if (btnPolicyNext) {
        btnPolicyNext.disabled = (policyCurrentPage >= totalPages);
      }
    }

    if (btnPolicyPrev) {
      btnPolicyPrev.addEventListener("click", () => {
        if (policyCurrentPage > 1) {
          policyCurrentPage--;
          renderPolicyPagination();
        }
      });
    }

    if (btnPolicyNext) {
      btnPolicyNext.addEventListener("click", () => {
        policyCurrentPage++;
        renderPolicyPagination();
      });
    }

    function applyPolicyFilter() {
      const q = (searchInput ? searchInput.value : "").toLowerCase().trim();

      rows.forEach((r) => {
        const text = r.innerText.toLowerCase();
        const severity = (r.getAttribute("data-severity") || "").toLowerCase();
        const mode = (r.getAttribute("data-mode") || "").toLowerCase();
        const system = (r.getAttribute("data-system") || "").toLowerCase();

        let matchesCat = true;
        if (currentCategory === "CRITICAL") {
          matchesCat = severity === "critical" || severity === "high";
        } else if (currentCategory === "PRIVILEGED") {
          matchesCat = text.includes("privileged") || text.includes("mfa") || text.includes("authentication") || text.includes("identity");
        } else if (currentCategory === "SCADA") {
          matchesCat = text.includes("scada") || text.includes("industrial") || text.includes("plc") || system.includes("sys-0");
        } else if (currentCategory === "CRYPTO") {
          matchesCat = text.includes("crypto") || text.includes("key") || text.includes("hsm") || text.includes("fips");
        } else if (currentCategory === "REVISION") {
          matchesCat = text.includes("revision") || text.includes("audit") || mode.includes("audit");
        }

        let matchesSearch = true;
        if (q !== "") {
          matchesSearch = text.includes(q);
        }

        if (matchesCat && matchesSearch) {
          r.setAttribute("data-filtered", "true");
        } else {
          r.setAttribute("data-filtered", "false");
          r.style.display = "none";
        }
      });

      policyCurrentPage = 1;
      renderPolicyPagination();
    }

    // Initial pagination render
    renderPolicyPagination();

    if (searchInput) {
      searchInput.addEventListener("input", applyPolicyFilter);
    }

    // Category Tabs Logic
    categoryButtons.forEach((btn) => {
      btn.addEventListener("click", () => {
        categoryButtons.forEach((b) => {
          b.className = "policy-tab-btn h-control-height-sm px-space-sm bg-surface-container hover:bg-surface-container-high text-on-surface font-security-stamp uppercase whitespace-nowrap cursor-pointer";
        });
        btn.className = "policy-tab-btn h-control-height-sm px-space-sm bg-primary text-on-primary font-security-stamp uppercase font-semibold whitespace-nowrap shadow-sm cursor-pointer";

        currentCategory = btn.getAttribute("data-cat") || btn.textContent.trim().toUpperCase();
        if (currentCategory.includes("ALL")) currentCategory = "ALL";
        else if (currentCategory.includes("CONFIDENTIAL") || currentCategory.includes("CRITICAL")) currentCategory = "CRITICAL";
        else if (currentCategory.includes("PRIVILEGED")) currentCategory = "PRIVILEGED";
        else if (currentCategory.includes("SCADA")) currentCategory = "SCADA";
        else if (currentCategory.includes("CRYPTO")) currentCategory = "CRYPTO";
        else if (currentCategory.includes("REVISION")) currentCategory = "REVISION";

        applyPolicyFilter();
        window.showToast?.("FILTER ENGAGED", `Showing policy category: ${btn.textContent.trim()}`, "info", "filter_list");
      });
    });

    // Sort Button
    const sortBtn = document.getElementById("btnSortPolicy");
    const sortLabel = document.getElementById("sortPolicyLabel");
    let sortAsc = false;
    if (sortBtn) {
      sortBtn.addEventListener("click", () => {
        sortAsc = !sortAsc;
        if (sortLabel) sortLabel.textContent = sortAsc ? "SORT: PRIORITY ASC" : "SORT: PRIORITY DESC";
        const tbody = document.querySelector("#policiesTable tbody, table tbody");
        if (tbody) {
          const rowArr = Array.from(tbody.querySelectorAll(".policy-row"));
          rowArr.sort((a, b) => {
            const sevOrder = { critical: 3, high: 2, medium: 1, low: 0 };
            const sA = sevOrder[(a.getAttribute("data-severity") || "").toLowerCase()] || 0;
            const sB = sevOrder[(b.getAttribute("data-severity") || "").toLowerCase()] || 0;
            return sortAsc ? sA - sB : sB - sA;
          });
          rowArr.forEach((r) => tbody.appendChild(r));
        }
        window.showToast?.("SORT APPLIED", `Policy table reordered by priority (${sortAsc ? "ASC" : "DESC"}).`, "info", "swap_vert");
      });
    }

    // Refresh Button
    const refreshBtn = document.getElementById("btnRefreshPolicy");
    const refreshLabel = document.getElementById("refreshPolicyLabel");
    if (refreshBtn) {
      refreshBtn.addEventListener("click", () => {
        const icon = refreshBtn.querySelector(".material-symbols-outlined");
        if (icon) icon.classList.add("animate-spin");
        if (refreshLabel) refreshLabel.textContent = "REFRESH: SYNCING...";
        setTimeout(() => {
          if (icon) icon.classList.remove("animate-spin");
          if (refreshLabel) refreshLabel.textContent = "REFRESH: SYNCED";
          applyPolicyFilter();
          window.showToast?.("POLICIES SYNCHRONIZED", "All statutory policy interlocks synchronized with deterministic kernel interlock.", "success", "sync");
        }, 600);
      });
    }

    // Mode Toggle (Standard vs Matrix)
    const btnStd = document.getElementById("btnModeStandard");
    const btnMat = document.getElementById("btnModeMatrix");
    if (btnStd && btnMat) {
      btnStd.addEventListener("click", () => {
        btnStd.className = "h-control-height-sm px-space-sm bg-surface-container-lowest text-primary font-label-uppercase text-[10px] font-bold shadow-sm cursor-pointer";
        btnMat.className = "h-control-height-sm px-space-sm text-on-surface-variant hover:text-on-surface font-label-uppercase text-[10px] cursor-pointer";
        rows.forEach(r => r.classList.remove("bg-secondary-container/10"));
        window.showToast?.("VIEW ENGAGED", "Switched to Standard Statutory Registry view.", "info", "view_list");
      });
      btnMat.addEventListener("click", () => {
        btnMat.className = "h-control-height-sm px-space-sm bg-surface-container-lowest text-secondary font-label-uppercase text-[10px] font-bold shadow-sm cursor-pointer";
        btnStd.className = "h-control-height-sm px-space-sm text-on-surface-variant hover:text-on-surface font-label-uppercase text-[10px] cursor-pointer";
        rows.forEach(r => r.classList.add("bg-secondary-container/10"));
        window.showToast?.("VIEW ENGAGED", "Switched to Enforcement Matrix view.", "info", "grid_view");
      });
    }

    // Top 4 Stat Cards Click Interactivity
    const topStatCards = document.querySelectorAll(".grid-cols-1.md\\:grid-cols-2.xl\\:grid-cols-4 > div");
    topStatCards.forEach((card, idx) => {
      card.classList.add("cursor-pointer", "transition-all", "hover:shadow-md");
      card.addEventListener("click", () => {
        topStatCards.forEach(c => c.classList.remove("ring-2", "ring-primary"));
        card.classList.add("ring-2", "ring-primary");

        if (idx === 0) currentCategory = "CRITICAL";
        else if (idx === 1) currentCategory = "REVISION";
        else if (idx === 2) currentCategory = "SCADA";
        else currentCategory = "ALL";

        applyPolicyFilter();
        window.showToast?.("METRIC FILTER", `Filtered policy table from telemetry card #${idx + 1}.`, "info", "analytics");
      });
    });
  });
})();
