/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Access Matrix & Privilege Boundary Controller
 */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    const roleRows = document.querySelectorAll(".role-row, tbody tr");
    const searchInput = document.getElementById("roleSearchInput");
    const nodeButtons = document.querySelectorAll(".node-pill-btn");
    const simBtn = document.getElementById("simulateEnforceBtn");
    const exportBtn = document.getElementById("exportMatrixBtn");
    const commitBtn = document.getElementById("commitAttestationBtn");

    const inspectorRoleId = document.getElementById("inspectorRoleId");
    const inspectorRoleName = document.getElementById("inspectorRoleName");
    const inspectorNodeTags = document.getElementById("inspectorNodeTags");

    let currentNodeFilter = "ALL";
    let currentClearanceFilter = "ALL";

    let matrixCurrentPage = 1;
    const matrixPageSize = 5;
    const btnMatrixPrev = document.getElementById("btnMatrixPrev");
    const btnMatrixNext = document.getElementById("btnMatrixNext");
    const matrixPageIndicator = document.getElementById("matrixPageIndicator");

    function renderMatrixPagination() {
      const rowList = Array.from(roleRows);
      const matched = rowList.filter((r) => r.getAttribute("data-filtered") !== "false");
      const totalPages = Math.max(1, Math.ceil(matched.length / matrixPageSize));

      if (matrixCurrentPage > totalPages) matrixCurrentPage = totalPages;
      if (matrixCurrentPage < 1) matrixCurrentPage = 1;

      matched.forEach((row, idx) => {
        const start = (matrixCurrentPage - 1) * matrixPageSize;
        const end = start + matrixPageSize;
        row.style.display = (idx >= start && idx < end) ? "" : "none";
      });

      const pageStr = String(matrixCurrentPage).padStart(2, "0");
      const totalStr = String(totalPages).padStart(2, "0");
      if (matrixPageIndicator) {
        matrixPageIndicator.textContent = `${pageStr} / ${totalStr}`;
      }
      if (btnMatrixPrev) {
        btnMatrixPrev.disabled = (matrixCurrentPage <= 1);
      }
      if (btnMatrixNext) {
        btnMatrixNext.disabled = (matrixCurrentPage >= totalPages);
      }
    }

    if (btnMatrixPrev) {
      btnMatrixPrev.addEventListener("click", () => {
        if (matrixCurrentPage > 1) {
          matrixCurrentPage--;
          renderMatrixPagination();
        }
      });
    }

    if (btnMatrixNext) {
      btnMatrixNext.addEventListener("click", () => {
        matrixCurrentPage++;
        renderMatrixPagination();
      });
    }

    // 1. Filter Engine
    function filterRoles() {
      const q = (searchInput ? searchInput.value : "").trim().toLowerCase();

      roleRows.forEach((row) => {
        const text = row.innerText.toLowerCase();
        const systems = (row.getAttribute("data-systems") || "").toLowerCase();
        const clr = (row.getAttribute("data-clearance") || "").toUpperCase();

        let matchesNode = true;
        if (currentNodeFilter !== "ALL") {
          matchesNode = systems.includes(currentNodeFilter.toLowerCase()) || text.includes(currentNodeFilter.toLowerCase()) || text.includes("all: 01-11");
        }

        let matchesClearance = true;
        if (currentClearanceFilter !== "ALL") {
          matchesClearance = clr.includes(currentClearanceFilter.toUpperCase());
        }

        let matchesQuery = true;
        if (q !== "") {
          matchesQuery = text.includes(q);
        }

        if (matchesNode && matchesClearance && matchesQuery) {
          row.setAttribute("data-filtered", "true");
        } else {
          row.setAttribute("data-filtered", "false");
          row.style.display = "none";
        }
      });

      matrixCurrentPage = 1;
      renderMatrixPagination();
    }

    // Initial render
    renderMatrixPagination();

    if (searchInput) {
      searchInput.addEventListener("input", filterRoles);
    }

    // Clearance Dropdown Menu Logic
    const clearanceFilterBtn = document.getElementById("clearanceFilterBtn");
    const clearanceDropdownMenu = document.getElementById("clearanceDropdownMenu");
    const clearanceBtnText = document.getElementById("clearanceBtnText");

    if (clearanceFilterBtn && clearanceDropdownMenu) {
      clearanceFilterBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        clearanceDropdownMenu.classList.toggle("hidden");
      });

      document.addEventListener("click", () => {
        clearanceDropdownMenu.classList.add("hidden");
      });

      document.querySelectorAll(".clearance-opt").forEach((opt) => {
        opt.addEventListener("click", (e) => {
          e.stopPropagation();
          const val = opt.getAttribute("data-clearance") || "ALL";
          currentClearanceFilter = val;
          if (clearanceBtnText) {
            clearanceBtnText.textContent = val === "ALL" ? "L1-L5+" : val;
          }
          clearanceDropdownMenu.classList.add("hidden");
          filterRoles();
          window.showToast?.("CLEARANCE FILTER", `Filtering role matrix by: ${val}`, "info", "security");
        });
      });
    }

    // Node Quick Toggles
    nodeButtons.forEach((btn) => {
      btn.addEventListener("click", () => {
        nodeButtons.forEach((b) => {
          b.className = "node-pill-btn px-space-xs py-[2px] bg-surface hover:bg-surface-container text-on-surface font-telemetry-micro text-telemetry-micro rounded border border-outline-variant/70 cursor-pointer";
        });
        btn.className = "node-pill-btn px-space-xs py-[2px] bg-primary text-on-primary font-telemetry-micro text-telemetry-micro rounded font-bold cursor-pointer";

        currentNodeFilter = btn.getAttribute("data-node") || "ALL";
        filterRoles();

        window.showToast?.(
          "NODE SCOPE ENGAGED",
          `Filtering role catalog to subsystem: ${currentNodeFilter}`,
          "info",
          "hub"
        );
      });
    });

    // 2. Row Selection & Detail Inspector Population
    roleRows.forEach((row) => {
      row.addEventListener("click", () => {
        roleRows.forEach((r) => r.classList.remove("bg-primary/10", "ring-1", "ring-primary"));
        row.classList.add("bg-primary/10", "ring-1", "ring-primary");

        const roleId = row.getAttribute("data-role-id");
        const roleName = row.getAttribute("data-role-name");
        const roleDesc = row.getAttribute("data-role-desc");
        const systems = row.getAttribute("data-systems");
        const clearance = row.getAttribute("data-clearance");

        if (inspectorRoleId && roleId) inspectorRoleId.textContent = roleId;
        if (inspectorRoleName && roleName) inspectorRoleName.textContent = `${roleName} (${roleDesc || "Industrial Operations"})`;
        if (inspectorNodeTags && systems) inspectorNodeTags.textContent = systems || "SYS-01 - SYS-11";

        window.showToast?.(
          "ROLE INSPECTION LOADED",
          `Inspecting privilege boundary and SoD invariants for ${roleId || "Role"}.`,
          "info",
          "policy"
        );
      });
    });

    // 3. Simulate Enforcement Action
    if (simBtn) {
      simBtn.addEventListener("click", function () {
        const orig = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin text-secondary">sync</span><span>Evaluating Zero-Trust Policy...</span>';

        setTimeout(() => {
          this.innerHTML = orig;
          this.disabled = false;
          window.showToast?.(
            "ZERO-TRUST SYNTHESIS COMPLETE",
            "Simulation evaluated against 11 nodes. 0 SoD conflicts detected. All active identities compliant with ST RK Directive.",
            "success",
            "verified"
          );
        }, 1100);
      });
    }

    // 4. Export Matrix Action (CSV & JSON)
    if (exportBtn) {
      exportBtn.addEventListener("click", function () {
        let csvContent = "Ref,Role_ID,Role_Name,Description,Clearance,Systems,Isolation_Enclave,Assignees\n";
        roleRows.forEach((row, idx) => {
          const id = row.getAttribute("data-role-id") || `ROLE-${idx + 1}`;
          const name = `"${(row.getAttribute("data-role-name") || "").replace(/"/g, '""')}"`;
          const desc = `"${(row.getAttribute("data-role-desc") || "").replace(/"/g, '""')}"`;
          const clr = row.getAttribute("data-clearance") || "L2-L3";
          const sys = `"${(row.getAttribute("data-systems") || "").replace(/"/g, '""')}"`;
          const enclave = (clr === "L5+") ? "Almaty Central Vault" : "Karaganda Enclave";
          const assignees = row.cells[6]?.textContent.trim() || "0";
          csvContent += `${idx + 1},${id},${name},${desc},${clr},${sys},${enclave},${assignees}\n`;
        });

        const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `VOSTOKPRIBOR_Access_Matrix_DOC-2026-007_${Date.now()}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        window.showToast?.(
          "MATRIX EXPORTED (CSV)",
          "Downloaded DOC-2026-007 industrial node clearance matrix as verified CSV table.",
          "success",
          "file_download"
        );
      });
    }

    // 5. Commit Ledger Attestation
    if (commitBtn) {
      commitBtn.addEventListener("click", function () {
        const orig = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin text-secondary-fixed">sync</span><span>Hashing Merkle Leaf...</span>';

        setTimeout(() => {
          this.innerHTML = '<span class="material-symbols-outlined text-[16px] text-secondary-fixed">done_all</span><span>Ledger Notarized</span>';
          window.showToast?.(
            "LEDGER COMMITTED",
            "Access Matrix notarized with Dual-Custody: EMP-1005 (T. Akhmetov) & EMP-1018 (L. Volkov). Merkle root broadcast to Astana Escrow.",
            "success",
            "encrypted"
          );
        }, 900);
      });
    }

    // 6. Role Tier Card Click Filter
    const tierCards = document.querySelectorAll(".grid-cols-1.sm\\:grid-cols-2.lg\\:grid-cols-4 > div");
    tierCards.forEach((card, idx) => {
      card.style.cursor = "pointer";
      card.addEventListener("click", () => {
        tierCards.forEach((c) => c.classList.remove("ring-2", "ring-primary"));
        card.classList.add("ring-2", "ring-primary");

        const tierTerms = ["L5+", "L4", "L2-L3", "ALL"];
        const term = tierTerms[idx] || "ALL";
        currentClearanceFilter = term;
        if (clearanceBtnText) {
          clearanceBtnText.textContent = term === "ALL" ? "L1-L5+" : term;
        }
        filterRoles();

        window.showToast?.(
          "TIER FILTER APPLIED",
          `Showing roles matching classification tier: ${term}`,
          "info",
          "filter_alt"
        );
      });
    });

    // 7. Pagination Buttons
    const prevBtn = Array.from(document.querySelectorAll("button")).find(b => b.textContent.trim() === "Previous");
    const nextBtn = Array.from(document.querySelectorAll("button")).find(b => b.textContent.trim() === "Next");
    const pageLabel = document.querySelector(".px-space-xs.font-bold.font-mono.text-primary");

    if (nextBtn) {
      nextBtn.addEventListener("click", () => {
        if (pageLabel) pageLabel.textContent = "02 / 03";
        if (prevBtn) prevBtn.disabled = false;
        window.showToast?.("PAGE LOADED", "Loaded page 02 of role catalog.", "info", "navigate_next");
      });
    }
    if (prevBtn) {
      prevBtn.addEventListener("click", () => {
        if (pageLabel) pageLabel.textContent = "01 / 03";
        prevBtn.disabled = true;
        window.showToast?.("PAGE LOADED", "Loaded page 01 of role catalog.", "info", "navigate_before");
      });
    }
  });
})();
