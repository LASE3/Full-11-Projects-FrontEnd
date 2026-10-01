/**
 * VOSTOKPRIBOR Enterprise Customer Portal - Industrial Projects Controller
 * Page-specific logic for project filtering, milestone roadmaps, and RFQ scope changes.
 */

(function () {
  "use strict";

  /**
   * Filter projects table by search input and execution stage
   */
  window.filterProjects = function () {
    const searchInput = document.getElementById("projectFilterInput");
    const stageSelect = document.getElementById("projectStageSelect");

    const searchVal = (searchInput ? searchInput.value : "").toLowerCase();
    const stageVal = stageSelect ? stageSelect.value : "ALL";
    const rows = document.querySelectorAll(
      'table tbody tr:not([class*="bg-surface-container-low/60"])',
    );

    rows.forEach((row) => {
      const text = row.innerText.toLowerCase();
      const matchesSearch = !searchVal || text.includes(searchVal);
      const matchesStage =
        stageVal === "ALL" || text.includes(stageVal.toLowerCase());

      if (matchesSearch && matchesStage) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  };

  /**
   * Display interactive Request For Quotation (RFQ) / Scope Change dialog
   */
  window.showRFQModal = function () {
    if (!window.openModal) return;

    window.openModal(`
            <div class="flex flex-col gap-4 text-left">
                <div class="flex items-center gap-3 pb-3 border-b border-outline/20">
                    <div class="p-2.5 rounded bg-tertiary-fixed text-primary">
                        <span class="material-symbols-outlined text-xl">post_add</span>
                    </div>
                    <div>
                        <h3 class="font-headline-sm text-base font-bold text-primary">Request Project Scope Change / New RFQ</h3>
                        <p class="text-xs text-on-surface-variant">Severstal Metallurgy Plant #4 • Engineering Expansion</p>
                    </div>
                </div>
                <div class="flex flex-col gap-3 text-xs">
                    <div>
                        <label class="font-medium text-on-surface block mb-1">Associated Project ID</label>
                        <select id="rfqProjectSelect" class="w-full p-2 rounded bg-surface-container border border-outline/30 text-primary font-body-sm">
                            <option>PRJ-VP-7721: Blast Furnace #5 Automation Suite</option>
                            <option>PRJ-VP-7804: Hot Strip Mill Hydraulic Pressure Telemetry</option>
                            <option>PRJ-VP-7910: Coke Oven Battery IR Cameras</option>
                            <option>NEW RFQ (Standalone Turnkey Project)</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-medium text-on-surface block mb-1">Scope Modification Description</label>
                        <textarea id="rfqDescInput" class="w-full p-2.5 rounded bg-surface-container border border-outline/30 text-primary font-body-sm h-20" placeholder="Specify addition of optical sensors, telemetry channels, or milestone dates..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-medium text-on-surface block mb-1">Estimated Budget Scope</label>
                            <input type="text" class="w-full p-2 rounded bg-surface-container border border-outline/30 text-primary font-body-sm" value="$150,000 — $300,000 USD">
                        </div>
                        <div>
                            <label class="font-medium text-on-surface block mb-1">Required In-Service Date</label>
                            <input type="text" class="w-full p-2 rounded bg-surface-container border border-outline/30 text-primary font-body-sm" value="Q1 2025">
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-outline/20">
                    <button onclick="document.getElementById('portal-dynamic-modal')?.remove()" class="px-4 py-2 rounded bg-surface-container hover:bg-surface-container-high text-xs font-semibold">Cancel</button>
                    <button onclick="window.showToast('RFQ Transmitted', 'Project change order filed with Vostokpribor Chief Engineer. Ref: RFQ-2024-884.', 'success'); document.getElementById('portal-dynamic-modal')?.remove();" class="px-4 py-2 rounded bg-primary text-on-primary text-xs font-semibold">Submit Scope Request</button>
                </div>
            </div>
        `);
  };

  /**
   * Toggle project inspection drawer
   */
  window.toggleProjectDrawer = function (triggerEl) {
    let row = triggerEl.closest("tr");
    if (!row) return;

    // Check if next row is already a drawer
    let nextRow = row.nextElementSibling;
    let isDrawer = nextRow && nextRow.classList.contains("bg-surface-container-low/60");

    if (isDrawer) {
      const isVisible = nextRow.style.display !== "none";
      nextRow.style.display = isVisible ? "none" : "";
      
      const btn = row.querySelector("button:has(.material-symbols-outlined)");
      if (btn) {
        const textSpan = btn.querySelector("span:not(.material-symbols-outlined)");
        const iconSpan = btn.querySelector(".material-symbols-outlined");
        if (textSpan) textSpan.textContent = isVisible ? "VIEW" : "COLLAPSE";
        if (iconSpan) iconSpan.textContent = isVisible ? "keyboard_arrow_down" : "keyboard_arrow_up";
        if (isVisible) {
          btn.className = "inline-flex items-center gap-0.5 font-technical-tag text-technical-tag px-2 py-1 rounded bg-surface-container-low text-primary hover:bg-surface-container transition-colors";
        } else {
          btn.className = "inline-flex items-center gap-0.5 font-technical-tag text-technical-tag px-2 py-1 rounded bg-primary text-on-primary font-medium";
        }
      }
    } else {
      // Find project code
      const prjCode = row.querySelector("td")?.innerText?.trim().split("\n")[0] || "PRJ-VP";
      const prjTitle = row.querySelector("td:nth-child(2) .font-headline-sm")?.innerText?.trim() || "Industrial Automation Suite";

      // Dynamically create an inspection drawer row
      const drawer = document.createElement("tr");
      drawer.className = "bg-surface-container-low/60";
      drawer.innerHTML = `
        <td class="p-unit-lg" colspan="7">
          <div class="bg-surface-container-lowest rounded-xl p-unit-lg shadow-md flex flex-col gap-unit-lg">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between pb-unit-md gap-unit-base bg-surface-container-low/40 p-unit-md rounded-lg">
              <div class="flex items-start gap-unit-md">
                <div class="p-2.5 rounded bg-primary text-tertiary-fixed shrink-0">
                  <span class="material-symbols-outlined text-2xl">precision_manufacturing</span>
                </div>
                <div>
                  <div class="flex items-center gap-unit-sm">
                    <span class="font-label-caps text-label-caps uppercase text-outline">Detailed Execution Blueprint</span>
                    <span class="font-data-mono-md text-data-mono-md font-semibold text-primary bg-surface-container px-2 py-0.5 rounded">${prjCode}</span>
                    <span class="px-2 py-0.5 rounded bg-tertiary-fixed/30 text-on-tertiary-container font-technical-tag text-technical-tag font-semibold">STAGE: ACTIVE</span>
                  </div>
                  <h2 class="font-headline-md text-headline-md text-primary mt-0.5">${prjTitle}</h2>
                </div>
              </div>
              <div class="flex items-center gap-unit-xs">
                <button class="px-unit-sm py-1.5 rounded bg-surface-container text-primary font-technical-tag text-technical-tag font-medium hover:bg-surface-container-high transition-colors"
                  onclick="window.showToast('Telemetry Link Active', 'SCADA feed polling telemetry sensors at 250ms interval.', 'info')" type="button">
                  Telemetry Log
                </button>
                <button class="px-unit-sm py-1.5 rounded bg-primary text-on-primary font-technical-tag text-technical-tag font-medium hover:bg-primary-container transition-colors"
                  onclick="window.location.href='SupportTicketView.php?ticket=TCK-9482'" type="button">
                  Field Ops Dispatch
                </button>
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-unit-md text-sm">
              <div class="p-unit-md rounded bg-surface-container-low/30 border border-outline/10">
                <span class="text-xs text-on-surface-variant font-label-caps uppercase">System Architecture</span>
                <p class="font-medium text-primary mt-1">Multi-drop Modbus TCP/IP gateway connected to SCADA host node VP-704.</p>
              </div>
              <div class="p-unit-md rounded bg-surface-container-low/30 border border-outline/10">
                <span class="text-xs text-on-surface-variant font-label-caps uppercase">Safety Standard</span>
                <p class="font-medium text-primary mt-1">GOST 12.2.007.0-75 compliant with dual redundant optical failover.</p>
              </div>
              <div class="p-unit-md rounded bg-surface-container-low/30 border border-outline/10">
                <span class="text-xs text-on-surface-variant font-label-caps uppercase">Inspection Dossier</span>
                <p class="font-medium text-primary mt-1">Cryptographic certificate signed by Lead Instrumentation Engineer.</p>
              </div>
            </div>
          </div>
        </td>
      `;
      row.after(drawer);

      const btn = row.querySelector("button:has(.material-symbols-outlined)");
      if (btn) {
        const textSpan = btn.querySelector("span:not(.material-symbols-outlined)");
        const iconSpan = btn.querySelector(".material-symbols-outlined");
        if (textSpan) textSpan.textContent = "COLLAPSE";
        if (iconSpan) iconSpan.textContent = "keyboard_arrow_up";
        btn.className = "inline-flex items-center gap-0.5 font-technical-tag text-technical-tag px-2 py-1 rounded bg-primary text-on-primary font-medium";
      }
    }
  };

  /**
   * Check query parameters for automatic RFQ or Project deep-link focus
   */
  document.addEventListener("DOMContentLoaded", () => {
    // Bind click events on all project action buttons
    document.querySelectorAll("table tbody tr button:has(.material-symbols-outlined)").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.stopPropagation();
        window.toggleProjectDrawer(btn);
      });
    });

    const params = new URLSearchParams(window.location.search);
    if (params.get("rfq") === "true") {
      setTimeout(window.showRFQModal, 400);
    }

    const prjParam = params.get("project") || params.get("project_id") || params.get("id");
    if (prjParam) {
      setTimeout(() => {
        const cleanPrj = prjParam.toLowerCase().trim();
        const rows = document.querySelectorAll("table tbody tr:not([class*='bg-surface-container-low/60'])");
        let matchedRow = null;

        rows.forEach((r) => {
          const txt = r.innerText.toLowerCase();
          if (txt.includes(cleanPrj)) {
            matchedRow = r;
          }
        });

        if (matchedRow) {
          matchedRow.scrollIntoView({ behavior: "smooth", block: "center" });
          matchedRow.style.transition = "outline 0.3s, background-color 0.3s";
          matchedRow.style.outline = "2px solid #0052cc";
          matchedRow.style.backgroundColor = "rgba(0, 82, 204, 0.08)";

          const toggleBtn = matchedRow.querySelector("button:has(.material-symbols-outlined)");
          const textSpan = toggleBtn?.querySelector("span:not(.material-symbols-outlined)");
          if (textSpan && textSpan.textContent.trim().toUpperCase() === "VIEW") {
            window.toggleProjectDrawer(toggleBtn);
          }
        }
      }, 350);
    }
  });

  window.exportProjectsCSV = function () {
    const rows = document.querySelectorAll("#projects-tbody tr:not([class*='bg-surface-container-low/60'])");
    let csv = "Project ID,Description,Stage,Budget,Progress,Project Manager\n";
    rows.forEach((row) => {
      const cols = Array.from(row.querySelectorAll("td")).map((td) => {
        return '"' + td.innerText.replace(/"/g, '""').replace(/\n+/g, " ").trim() + '"';
      });
      if (cols.length >= 6) {
        csv += cols.slice(0, 6).join(",") + "\n";
      }
    });
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "vostokpribor_projects_" + new Date().toISOString().slice(0, 10) + ".csv";
    link.click();
    if (window.showToast) {
      window.showToast("Ledger Exported", "Project capital allocations exported (.CSV).", "success");
    }
  };
})();

