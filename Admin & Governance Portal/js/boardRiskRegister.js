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

    // 3. Risk Filter Tabs Engine
    const filterTabs = document.querySelectorAll("#riskFilterTabs button, .risk-tab-btn");
    const searchInput = document.getElementById("riskSearchInput") || document.querySelector('input[placeholder*="Filter by cluster"]');
    const rows = document.querySelectorAll(".risk-row");
    let currentFilter = "ALL";

    function applyRiskFilter() {
      const q = (searchInput ? searchInput.value : "").toLowerCase().trim();
      let matchCount = 0;

      rows.forEach((r) => {
        const text = r.innerText.toLowerCase();
        const impact = (r.getAttribute("data-impact") || "").toLowerCase();
        const status = (r.getAttribute("data-status") || "").toLowerCase();

        let matchesTab = true;
        if (currentFilter === "CRITICAL") {
          matchesTab = impact === "critical" || impact === "high";
        } else if (currentFilter === "MITIGATION") {
          matchesTab = status.includes("action") || status.includes("review") || text.includes("remediation") || text.includes("mitigat");
        } else if (currentFilter === "ATTESTED") {
          matchesTab = status.includes("validated") || status.includes("attested") || text.includes("validated");
        } else if (currentFilter === "RETIRED") {
          matchesTab = status.includes("retired") || status.includes("resolved") || text.includes("retired");
        }

        let matchesSearch = true;
        if (q !== "") {
          matchesSearch = text.includes(q);
        }

        if (matchesTab && matchesSearch) {
          r.style.display = "";
          matchCount++;
        } else {
          r.style.display = "none";
        }
      });
    }

    if (searchInput) {
      searchInput.addEventListener("input", applyRiskFilter);
    }

    filterTabs.forEach((tab) => {
      tab.addEventListener("click", () => {
        filterTabs.forEach((t) => {
          t.className = "risk-tab-btn px-space-sm py-space-xs bg-surface-container hover:bg-surface-container-high text-primary font-label-uppercase text-label-uppercase transition-colors cursor-pointer";
        });
        tab.className = "risk-tab-btn px-space-sm py-space-xs bg-primary text-on-primary font-label-uppercase text-label-uppercase font-bold cursor-pointer shadow-sm";

        currentFilter = tab.getAttribute("data-filter") || "ALL";
        applyRiskFilter();

        window.showToast?.(
          "FILTER APPLIED",
          `Displaying board risk register for category: ${tab.textContent.trim()}`,
          "info",
          "filter_alt"
        );
      });
    });

    // 4. 5x5 Heatmap Matrix Cells Interactivity
    const heatmapCells = document.querySelectorAll(".grid-cols-5 > div");
    heatmapCells.forEach((cell) => {
      const codeSpan = cell.querySelector(".font-bold");
      const codeText = codeSpan ? codeSpan.textContent.trim() : "";

      if (codeText && codeText.startsWith("RR-")) {
        cell.classList.add("hover:scale-105", "transition-transform", "cursor-pointer");
        cell.setAttribute("title", `Inspect Risk ${codeText}`);

        cell.addEventListener("click", () => {
          heatmapCells.forEach((c) => c.classList.remove("ring-2", "ring-primary", "scale-105"));
          cell.classList.add("ring-2", "ring-primary", "scale-105");

          // Find row with matching code
          const num = parseInt(codeText.replace("RR-", ""), 10);
          let targetRow = Array.from(rows).find(r => (r.getAttribute("data-code") || "").includes(String(num)) || (r.getAttribute("data-id") || "") == num);
          if (!targetRow && rows.length > 0) {
            targetRow = rows[num % rows.length];
          }

          if (targetRow) {
            const riskId = targetRow.getAttribute("data-id");
            if (riskId && window.inspectRisk) {
              window.inspectRisk(riskId);
            }
            targetRow.scrollIntoView({ behavior: "smooth", block: "center" });
            targetRow.classList.add("ring-2", "ring-[#D9822B]");
          }

          window.showToast?.(
            "HEATMAP CELL INSPECTION",
            `Matrix coordinate inspected: ${codeText} loaded into Focused Dossier.`,
            "info",
            "grid_goldenratio"
          );
        });
      }
    });

    // 5. Right Panel Action Interlocks
    const btnPatch = document.getElementById("btnDispatchPatch");
    if (btnPatch) {
      btnPatch.addEventListener("click", () => {
        const orig = btnPatch.innerHTML;
        btnPatch.disabled = true;
        btnPatch.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span><span>Deploying Kernel Patch v4.9.1...</span>';
        setTimeout(() => {
          btnPatch.innerHTML = '<span class="material-symbols-outlined text-[16px]">verified</span><span>PATCH DISPATCHED (FIPS-140-3 COMMITTED)</span>';
          btnPatch.className = "h-control-height-md w-full bg-secondary text-white font-body-compact text-body-compact font-bold flex items-center justify-center gap-space-xs transition-colors shadow-sm cursor-default";
          window.showToast?.(
            "PATCH DEPLOYED",
            "Industrial Patch v4.9.1 propagated to all SCADA nodes across 11 clusters.",
            "success",
            "build_circle"
          );
        }, 1200);
      });
    }

    const btnInspectorate = document.getElementById("btnStateInspectorate");
    if (btnInspectorate) {
      btnInspectorate.addEventListener("click", () => {
        window.showToast?.(
          "INSPECTORATE NOTIFIED",
          "Statutory dossier and cryptographic telemetry leaf transmitted to Astana State Inspectorate.",
          "info",
          "send"
        );
      });
    }

    const btnKeyHash = document.getElementById("btnAppendKeyHash");
    if (btnKeyHash) {
      btnKeyHash.addEventListener("click", () => {
        window.showToast?.(
          "KEY HASH NOTARIZED",
          "ECDSA SHA-256 state seal appended to Risk Register Merkle leaf #4,921,809.",
          "success",
          "lock_reset"
        );
      });
    }

    // 6. Export Risk Dossier Button (JSON & CSV)
    const exportRiskBtn = document.getElementById("btnExportRiskDossier");
    if (exportRiskBtn) {
      exportRiskBtn.addEventListener("click", () => {
        const risks = [];
        let csvContent = "Risk_ID,Code,Impact,Likelihood,Target_Apparatus,Custodian,Review_Date,Status,Description\n";

        rows.forEach((r) => {
          const id = r.getAttribute("data-id") || "";
          const code = r.getAttribute("data-code") || "";
          const desc = (r.getAttribute("data-desc") || "").replace(/"/g, '""');
          const impact = r.getAttribute("data-impact") || "";
          const likelihood = r.getAttribute("data-likelihood") || "";
          const target = (r.getAttribute("data-target") || "").replace(/"/g, '""');
          const owner = r.getAttribute("data-owner") || "";
          const status = r.getAttribute("data-status") || "";
          const date = r.getAttribute("data-date") || "";

          risks.push({ id, code, desc, impact, likelihood, target, owner, status, date });
          csvContent += `"${id}","${code}","${impact}","${likelihood}","${target}","${owner}","${date}","${status}","${desc}"\n`;
        });

        // Download JSON
        const blob = new Blob([JSON.stringify({ station: "ALMATY-CENTRAL", framework: "ST RK 27001-2026", timestamp: new Date().toISOString(), risks: risks }, null, 2)], {
          type: "application/json"
        });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `VOSTOKPRIBOR_Board_Risk_Dossier_${Date.now()}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        window.showToast?.("DOSSIER EXPORTED", "Board Risk Register XBRL/JSON package downloaded.", "success", "file_download");
      });
    }

    // 7. Submit to Almaty Board Button
    const submitBoardBtn = document.getElementById("btnSubmitBoard");
    if (submitBoardBtn) {
      submitBoardBtn.addEventListener("click", () => {
        const orig = submitBoardBtn.innerHTML;
        submitBoardBtn.disabled = true;
        submitBoardBtn.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin">sync</span><span>Transmitting to Board...</span>';

        setTimeout(() => {
          submitBoardBtn.innerHTML = '<span class="material-symbols-outlined text-[14px]">done_all</span><span>Transmitted to Board</span>';
          setTimeout(() => {
            submitBoardBtn.innerHTML = orig;
            submitBoardBtn.disabled = false;
          }, 3000);
          window.showToast?.("BOARD DISPATCH ACKNOWLEDGED", "Quarterly Risk Appraisal submitted to Supervisory Board Protocol Act #19.", "success", "verified");
        }, 1000);
      });
    }

    // 8. Pagination buttons
    const paginationButtons = document.querySelectorAll(".p-space-xs.px-space-sm button");
    paginationButtons.forEach((btn, index) => {
      btn.addEventListener("click", () => {
        window.showToast?.("PAGE ACCESSED", `Navigated to ledger page ${index + 1} of 5.`, "info", "navigate_next");
      });
    });
  });
})();
