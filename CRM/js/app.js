/**
 * VOSTOKPRIBOR Enterprise CRM Platform - Core Application Logic (System 04)
 * Subdomain: crm.vostokpribor.local
 */

(function () {
  "use strict";

  // Global App Registry
  const crmApp = {
    // Current active deal dataset for Kanban
    opportunities: [
      {
        id: "OPP-2024-9101",
        client: "Aral Geomatics Group",
        title: "Blast Furnace #5 Automation & Gas Analysis",
        value: 1850000,
        stage: "negotiation", // qualification | proposal | negotiation | contract | won
        probability: 85,
        closeDate: "Nov 28, 2024",
        rep: {
          name: "Pavel Orlov",
          avatar:
            "data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2236%22%20height%3D%2236%22%20viewBox%3D%220%200%2036%2036%22%3E%3Ccircle%20cx%3D%2218%22%20cy%3D%2218%22%20r%3D%2218%22%20fill%3D%22%230d1e2e%22%20stroke%3D%22%231b3a5c%22%20stroke-width%3D%221.5%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2254%25%22%20fill%3D%22%2300E5FF%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20font-weight%3D%22700%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3EVP%3C%2Ftext%3E%3C%2Fsvg%3E",
        },
        confidential: true,
        priority: "high",
      },
      {
        id: "OPP-2024-9102",
        client: "NLMK Group Lipetsk",
        title: "Coke Oven Battery Temperature Profiling & IR Cameras",
        value: 920000,
        stage: "proposal",
        probability: 60,
        closeDate: "Dec 05, 2024",
        rep: {
          name: "Mikhail Sorokin",
          avatar:
            "data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2236%22%20height%3D%2236%22%20viewBox%3D%220%200%2036%2036%22%3E%3Ccircle%20cx%3D%2218%22%20cy%3D%2218%22%20r%3D%2218%22%20fill%3D%22%230d1e2e%22%20stroke%3D%22%231b3a5c%22%20stroke-width%3D%221.5%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2254%25%22%20fill%3D%22%2300E5FF%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20font-weight%3D%22700%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3EVP%3C%2Ftext%3E%3C%2Fsvg%3E",
        },
        confidential: false,
        priority: "medium",
      },
      {
        id: "OPP-2024-9103",
        client: "Steppe Mining Technologies",
        title: "Talnakh Concentrator Flotation Telemetry Grid",
        value: 2400000,
        stage: "negotiation",
        probability: 80,
        closeDate: "Dec 18, 2024",
        rep: {
          name: "Mikhail Sorokin",
          avatar:
            "data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2236%22%20height%3D%2236%22%20viewBox%3D%220%200%2036%2036%22%3E%3Ccircle%20cx%3D%2218%22%20cy%3D%2218%22%20r%3D%2218%22%20fill%3D%22%230d1e2e%22%20stroke%3D%22%231b3a5c%22%20stroke-width%3D%221.5%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2254%25%22%20fill%3D%22%2300E5FF%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20font-weight%3D%22700%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3EVP%3C%2Ftext%3E%3C%2Fsvg%3E",
        },
        confidential: true,
        priority: "high",
      },
      {
        id: "OPP-2024-9104",
        client: "BaltNord Process Systems",
        title: "Hydraulic Pressure Sensor Telemetry Retrofit",
        value: 640000,
        stage: "contract",
        probability: 95,
        closeDate: "Nov 20, 2024",
        rep: {
          name: "Viktor Morozov",
          avatar:
            "data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2236%22%20height%3D%2236%22%20viewBox%3D%220%200%2036%2036%22%3E%3Ccircle%20cx%3D%2218%22%20cy%3D%2218%22%20r%3D%2218%22%20fill%3D%22%230d1e2e%22%20stroke%3D%22%231b3a5c%22%20stroke-width%3D%221.5%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2254%25%22%20fill%3D%22%2300E5FF%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20font-weight%3D%22700%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3EVP%3C%2Ftext%3E%3C%2Fsvg%3E",
        },
        confidential: false,
        priority: "high",
      },
      {
        id: "OPP-2024-9105",
        client: "EVRAZ Consolidated",
        title: "Rail Mill Laser Profiler & Flaw Detection Array",
        value: 1650000,
        stage: "qualification",
        probability: 40,
        closeDate: "Jan 15, 2025",
        rep: {
          name: "Denis Sokolov",
          avatar:
            "data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2236%22%20height%3D%2236%22%20viewBox%3D%220%200%2036%2036%22%3E%3Ccircle%20cx%3D%2218%22%20cy%3D%2218%22%20r%3D%2218%22%20fill%3D%22%230d1e2e%22%20stroke%3D%22%231b3a5c%22%20stroke-width%3D%221.5%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2254%25%22%20fill%3D%22%2300E5FF%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20font-weight%3D%22700%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3EVP%3C%2Ftext%3E%3C%2Fsvg%3E",
        },
        confidential: true,
        priority: "medium",
      },
      {
        id: "OPP-2024-9106",
        client: "PhosAgro Chemical",
        title: "High-Pressure Flowmeter HPF-900X Replacement Batch",
        value: 418200,
        stage: "won",
        probability: 100,
        closeDate: "Nov 12, 2024",
        rep: {
          name: "Pavel Orlov",
          avatar:
            "data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2236%22%20height%3D%2236%22%20viewBox%3D%220%200%2036%2036%22%3E%3Ccircle%20cx%3D%2218%22%20cy%3D%2218%22%20r%3D%2218%22%20fill%3D%22%230d1e2e%22%20stroke%3D%22%231b3a5c%22%20stroke-width%3D%221.5%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2254%25%22%20fill%3D%22%2300E5FF%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20font-weight%3D%22700%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3EVP%3C%2Ftext%3E%3C%2Fsvg%3E",
        },
        confidential: false,
        priority: "low",
      },
      {
        id: "OPP-2024-9107",
        client: "Daugava Optical Research",
        title: "Refinery Catalytic Cracking Gas Analysis Skid",
        value: 3100000,
        stage: "qualification",
        probability: 35,
        closeDate: "Feb 10, 2025",
        rep: {
          name: "Mikhail Sorokin",
          avatar:
            "data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2236%22%20height%3D%2236%22%20viewBox%3D%220%200%2036%2036%22%3E%3Ccircle%20cx%3D%2218%22%20cy%3D%2218%22%20r%3D%2218%22%20fill%3D%22%230d1e2e%22%20stroke%3D%22%231b3a5c%22%20stroke-width%3D%221.5%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2254%25%22%20fill%3D%22%2300E5FF%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20font-weight%3D%22700%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3EVP%3C%2Ftext%3E%3C%2Fsvg%3E",
        },
        confidential: true,
        priority: "high",
      },
      {
        id: "OPP-2024-9108",
        client: "Aral Geomatics Group",
        title: "Continuous Casting Machine #3 Optical Thickness Gauges",
        value: 410000,
        stage: "proposal",
        probability: 65,
        closeDate: "Dec 02, 2024",
        rep: {
          name: "Pavel Orlov",
          avatar:
            "data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2236%22%20height%3D%2236%22%20viewBox%3D%220%200%2036%2036%22%3E%3Ccircle%20cx%3D%2218%22%20cy%3D%2218%22%20r%3D%2218%22%20fill%3D%22%230d1e2e%22%20stroke%3D%22%231b3a5c%22%20stroke-width%3D%221.5%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2254%25%22%20fill%3D%22%2300E5FF%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20font-weight%3D%22700%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3EVP%3C%2Ftext%3E%3C%2Fsvg%3E",
        },
        confidential: false,
        priority: "medium",
      },
      {
        id: "OPP-2024-9109",
        client: "NLMK Group Lipetsk",
        title: "Turbine Bearing Vibration Transducers (x16)",
        value: 396400,
        stage: "won",
        probability: 100,
        closeDate: "Nov 08, 2024",
        rep: {
          name: "Viktor Morozov",
          avatar:
            "data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2236%22%20height%3D%2236%22%20viewBox%3D%220%200%2036%2036%22%3E%3Ccircle%20cx%3D%2218%22%20cy%3D%2218%22%20r%3D%2218%22%20fill%3D%22%230d1e2e%22%20stroke%3D%22%231b3a5c%22%20stroke-width%3D%221.5%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2254%25%22%20fill%3D%22%2300E5FF%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20font-weight%3D%22700%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3EVP%3C%2Ftext%3E%3C%2Fsvg%3E",
        },
        confidential: false,
        priority: "medium",
      },
    ],

    // Format currency USD
    formatUSD: function (num) {
      return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }).format(num);
    },

    // Show Toast Notification
    showToast: function (title, message, type = "info") {
      const container = document.getElementById("toast-container");
      if (!container) return;

      const toast = document.createElement("div");
      let typeClass = "";
      let icon = "ℹ️";
      if (type === "amber" || type === "warning") {
        typeClass = "toast-amber";
        icon = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>`;
      } else if (type === "success") {
        typeClass = "toast-success";
        icon = "✓";
      } else if (type === "danger" || type === "confidential") {
        typeClass = "toast-danger";
        icon = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>`;
      }

      toast.className = `crm-toast ${typeClass}`;
      toast.innerHTML = `
        <div style="font-size: 16px;">${icon}</div>
        <div style="flex: 1;">
          <div style="font-weight: 700; font-size: 12.5px; color: #FFFFFF;">${title}</div>
          <div style="font-size: 11px; color: rgba(255,255,255,0.8); margin-top: 2px;">${message}</div>
        </div>
        <button style="color: rgba(255,255,255,0.5); font-size: 14px;" onclick="this.parentElement.remove()">✕</button>
      `;

      container.appendChild(toast);

      setTimeout(() => {
        toast.style.transition = "all 0.3s ease";
        toast.style.opacity = "0";
        toast.style.transform = "translateY(10px)";
        setTimeout(() => toast.remove(), 300);
      }, 4200);
    },

    // Modals
    openModal: function (modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.add("active");
        const firstInput = modal.querySelector("input, select");
        if (firstInput) firstInput.focus();
      }
    },

    closeModal: function (modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.remove("active");
      }
    },

    // Switch Customer Detail Tabs (Overview, Projects, Documents, Contracts, Support)
    switchCustomerTab: function (tabName) {
      const tabButtons = document.querySelectorAll(".customer-tab-btn");
      tabButtons.forEach((btn) => {
        if (btn.getAttribute("data-tab") === tabName) {
          btn.classList.add("active");
        } else {
          btn.classList.remove("active");
        }
      });

      const panels = document.querySelectorAll(".tab-content-panel");
      panels.forEach((p) => {
        p.classList.remove("active");
      });

      const activePanel = document.getElementById(`tab-panel-${tabName}`);
      if (activePanel) {
        activePanel.classList.add("active");
      }
    },

    // Convert Lead to Customer Interactive Action
    convertLeadToCustomer: function (leadName, leadCompany, leadValue) {
      const modal = document.getElementById("modal-convert-customer");
      if (modal) {
        document.getElementById("convert-lead-name").textContent = leadName;
        document.getElementById("convert-lead-company").textContent =
          leadCompany;
        document.getElementById("convert-lead-val").textContent = leadValue;
        this.openModal("modal-convert-customer");
      } else {
        this.showToast(
          "Lead Converted",
          `${leadCompany} (${leadName}) converted to active enterprise customer account.`,
          "success",
        );
      }
    },

    saveNewLead: function () {
      const comp = (document.getElementById("new-lead-company")?.value || "").trim() || "Novatek Arctic LNG";
      const name = (document.getElementById("new-lead-name")?.value || "").trim() || "Dmitry Orlov";
      const val = (document.getElementById("new-lead-val")?.value || "").trim() || "850000";
      const notes = (document.getElementById("new-lead-notes")?.value || "").trim() || "Cryogenic temperature sensor instrumentation package";
      const token = window.__CSRF_TOKEN__ || (window.getCsrfToken ? window.getCsrfToken() : '') || (document.cookie.match(/(^|;)\s*vostok_csrf_token\s*=\s*([^;]+)/) || [])[2] || '';

      const self = this;
      fetch("api/leads.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-Token": token
        },
        body: JSON.stringify({
          company_name: comp,
          full_name: name,
          email: name.toLowerCase().replace(/[^a-z0-9]/g, ".") + "@industrial-client.ru",
          phone: "+7 (495) 800-44-22",
          message: notes,
          source_page: "CRM Manual Lead Form",
          csrf_token: token
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          self.showToast("Lead Created", `New lead #${data.data?.lead_id || "OK"} registered in database.`, "success");
          self.closeModal("modal-new-lead");
        } else {
          self.showToast("Lead Creation Error", data.message || "Failed to create lead", "error");
        }
      })
      .catch(err => {
        console.error("Save lead error:", err);
      });
    },

    // Render Kanban Cards (for Opportunities page)
    renderKanban: function () {
      const stages = [
        "qualification",
        "proposal",
        "negotiation",
        "contract",
        "won",
      ];

      stages.forEach((stage) => {
        const wrapper = document.getElementById(`kanban-cards-${stage}`);
        const countEl = document.getElementById(`col-count-${stage}`);
        const valEl = document.getElementById(`col-val-${stage}`);

        if (!wrapper) return;

        wrapper.innerHTML = "";
        const items = this.opportunities.filter((o) => o.stage === stage);
        const stageTotalVal = items.reduce((acc, curr) => acc + curr.value, 0);

        if (countEl) countEl.textContent = items.length;
        if (valEl) valEl.textContent = this.formatUSD(stageTotalVal);

        if (items.length === 0) {
          wrapper.innerHTML = '<div class="kanban-empty-part" style="padding: 24px 10px; text-align: center; color: #8892b0; font-size: 12px; font-weight: 500; font-style: italic; border: 1px dashed rgba(255,255,255,0.15); border-radius: 6px; margin: 8px 4px;">( There\'s no Opportunities in the moment )</div>';
          return;
        }

        items.forEach((opp) => {
          const card = document.createElement("div");
          card.className = "kanban-deal-card";
          card.draggable = true;
          card.setAttribute("data-id", opp.id);

          // Card Drag Events
          card.addEventListener("dragstart", (e) => {
            e.dataTransfer.setData("text/plain", opp.id);
            card.style.opacity = "0.4";
          });

          card.addEventListener("dragend", () => {
            card.style.opacity = "1";
          });

          // Card Click to Inspect
          card.addEventListener("click", () => {
            crmApp.inspectOpportunity(opp.id);
          });

          const confidentialBadge = opp.confidential
            ? `<span class="confidential-pill" style="font-size: 9px; padding: 1px 5px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Confid.</span>`
            : "";

          card.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
              <span class="card-company-name">${opp.client}</span>
              ${confidentialBadge}
            </div>
            <div class="card-deal-title">${opp.title}</div>
            <div style="display: flex; align-items: baseline; justify-content: space-between;">
              <span class="card-value-badge">${crmApp.formatUSD(opp.value)}</span>
              <span style="font-size: 10px; font-family: var(--crm-font-mono); color: var(--crm-indigo); font-weight: 700;">${opp.probability}% Prob.</span>
            </div>
            <div class="card-footer-row">
              <span class="card-close-date">
                <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                <span>${opp.closeDate}</span>
              </span>
              <img src="${opp.rep.avatar}" alt="${opp.rep.name}" class="card-rep-avatar" title="Rep: ${opp.rep.name}" />
            </div>
          `;

          wrapper.appendChild(card);
        });
      });

      // Attach Column Drag/Drop Listeners
      stages.forEach((stage) => {
        const col = document.getElementById(`kanban-col-${stage}`);
        if (!col) return;

        col.addEventListener("dragover", (e) => {
          e.preventDefault();
          col.style.backgroundColor = "#E5EBF2";
        });

        col.addEventListener("dragleave", () => {
          col.style.backgroundColor = "";
        });

        col.addEventListener("drop", async (e) => {
          e.preventDefault();
          col.style.backgroundColor = "";
          const oppId = e.dataTransfer.getData("text/plain");
          const opp = crmApp.opportunities.find((o) => o.id === oppId);
          if (opp && opp.stage !== stage) {
            const oldStage = opp.stage;
            opp.stage = stage;
            crmApp.renderKanban();
            try {
              const fetchFn = window.vpFetch || (async (u, opt) => {
                const r = await fetch(u, opt);
                return await r.json();
              });
              const res = await fetchFn("api/opportunities.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                  action: "update_stage",
                  opp_id: oppId,
                  stage: stage
                })
              });
              if (!res || !res.success) {
                opp.stage = oldStage;
                crmApp.renderKanban();
                crmApp.showToast("Update Failed", (res && res.message) || "Stage update failed; rolled back.", "alert");
              } else {
                crmApp.showToast(
                  "Stage Gating Updated",
                  `${opp.client} moved to stage: ${stage.toUpperCase()}`,
                  "amber",
                );
              }
            } catch (err) {
              opp.stage = oldStage;
              crmApp.renderKanban();
              crmApp.showToast("Connection Error", err.message + "; rolled back.", "alert");
            }
          }
        });
      });
    },

    // Inspect Opportunity Drawer/Modal
    inspectOpportunity: function (oppId) {
      const opp = this.opportunities.find((o) => o.id === oppId);
      if (!opp) return;

      const titleEl = document.getElementById("inspect-opp-title");
      const clientEl = document.getElementById("inspect-opp-client");
      const valEl = document.getElementById("inspect-opp-value");
      const stageSelect = document.getElementById("inspect-opp-stage");
      const dateEl = document.getElementById("inspect-opp-date");
      const repEl = document.getElementById("inspect-opp-rep");

      if (titleEl) titleEl.textContent = opp.title;
      if (clientEl) clientEl.textContent = opp.client;
      if (valEl) valEl.textContent = this.formatUSD(opp.value);
      if (stageSelect) stageSelect.value = opp.stage;
      if (dateEl)
        dateEl.textContent = `${opp.closeDate} (${opp.probability}% win probability)`;
      if (repEl) repEl.textContent = opp.rep.name;

      this.openModal("modal-inspect-opportunity");
    },

    // Initialize Page
    init: function () {
      // Auto-highlight sidebar active link based on current filename
      const currentPath = window.location.pathname.toLowerCase();
      const sidebarLinks = document.querySelectorAll(".sidebar-nav-item");

      sidebarLinks.forEach((link) => {
        const href = (link.getAttribute("href") || "").toLowerCase();
        if (
          href &&
          (currentPath.endsWith(href) ||
            (currentPath.endsWith("/") && href === "dashboard.php") ||
            (currentPath.endsWith("index.php") && href === "dashboard.php"))
        ) {
          link.classList.add("active");
        } else if (href && currentPath.includes(href.replace(".php", ""))) {
          link.classList.add("active");
        } else if (!href && link.classList.contains("active")) {
          // Keep active if explicitly rendered
        }
      });

      // Global Omni Search Listener (Live API)
      const omniSearch = document.getElementById("global-omni-search");
      if (omniSearch) {
        let searchTimeout = null;
        const searchWrapper = omniSearch.closest(".top-search-bar") || omniSearch.parentElement;
        let dropdown = document.querySelector(".omni-search-dropdown");
        if (!dropdown && searchWrapper) {
          dropdown = document.createElement("div");
          dropdown.className = "omni-search-dropdown";
          dropdown.style.display = "none";
          searchWrapper.appendChild(dropdown);
        }

        const closeDropdown = () => {
          if (dropdown) dropdown.style.display = "none";
        };

        const renderResults = (items, q) => {
          if (!dropdown) return;
          if (!items || items.length === 0) {
            dropdown.innerHTML = `<div style="padding:14px;text-align:center;color:#94a3b8;font-size:12px;">No matching records found for "${q}"</div>`;
            dropdown.style.display = "flex";
            return;
          }

          dropdown.innerHTML = items.map((item, idx) => `
            <a href="${item.url || '#'}" class="omni-search-item ${idx === 0 ? 'active' : ''}">
              <span class="omni-search-badge ${item.type}">${item.type}</span>
              <div class="omni-search-details">
                <span class="omni-search-title">${item.title}</span>
                <span class="omni-search-subtitle">${item.subtitle || ''}</span>
              </div>
            </a>
          `).join('');
          dropdown.style.display = "flex";
        };

        const performSearch = async () => {
          const q = omniSearch.value.trim();
          if (q.length < 2) {
            closeDropdown();
            return;
          }
          try {
            const res = await fetch(`api/search.php?q=${encodeURIComponent(q)}`);
            const json = await res.json();
            if (json.success && Array.isArray(json.data)) {
              renderResults(json.data, q);
            }
          } catch (e) {
            console.error("Omni-search error:", e);
          }
        };

        omniSearch.addEventListener("input", () => {
          clearTimeout(searchTimeout);
          searchTimeout = setTimeout(performSearch, 220);
        });

        omniSearch.addEventListener("keydown", (e) => {
          if (e.key === "Enter") {
            const firstLink = dropdown ? dropdown.querySelector(".omni-search-item") : null;
            if (firstLink && firstLink.getAttribute("href")) {
              window.location.href = firstLink.getAttribute("href");
            } else {
              performSearch();
            }
          } else if (e.key === "Escape") {
            closeDropdown();
          }
        });

        document.addEventListener("click", (e) => {
          if (!searchWrapper || !searchWrapper.contains(e.target)) {
            closeDropdown();
          }
        });
      }

      // Shortcut: Ctrl+K / Cmd+K to focus search
      document.addEventListener("keydown", (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === "k") {
          e.preventDefault();
          if (omniSearch) {
            omniSearch.focus();
            omniSearch.select();
          }
        }
      });

      // New Opportunity Form Handler
      const newOppForm = document.getElementById("form-new-opportunity");
      if (newOppForm) {
        newOppForm.addEventListener("submit", (e) => {
          e.preventDefault();
          const title = document.getElementById("new-opp-title").value;
          const client = document.getElementById("new-opp-client").value;
          const value =
            parseFloat(document.getElementById("new-opp-value").value) ||
            250000;
          const stage = document.getElementById("new-opp-stage").value;
          const closeDate =
            document.getElementById("new-opp-date").value || "Dec 31, 2024";
          const confidential = document.getElementById("new-opp-confidential")
            ? document.getElementById("new-opp-confidential").checked
            : true;

          const newOpp = {
            id: `OPP-2024-${Math.floor(1000 + Math.random() * 9000)}`,
            client: client,
            title: title,
            value: value,
            stage: stage,
            probability: 70,
            closeDate: closeDate,
            rep: {
              name: "Mikhail Sorokin",
              avatar:
                "data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2236%22%20height%3D%2236%22%20viewBox%3D%220%200%2036%2036%22%3E%3Ccircle%20cx%3D%2218%22%20cy%3D%2218%22%20r%3D%2218%22%20fill%3D%22%230d1e2e%22%20stroke%3D%22%231b3a5c%22%20stroke-width%3D%221.5%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2254%25%22%20fill%3D%22%2300E5FF%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20font-weight%3D%22700%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3EVP%3C%2Ftext%3E%3C%2Fsvg%3E",
            },
            confidential: confidential,
            priority: "high",
          };

          crmApp.opportunities.unshift(newOpp);
          crmApp.closeModal("modal-new-opportunity");
          newOppForm.reset();

          if (document.getElementById("kanban-cards-qualification")) {
            crmApp.renderKanban();
          }

          crmApp.showToast(
            "Opportunity Created",
            `${newOpp.client} - ${crmApp.formatUSD(newOpp.value)} logged to CRM ledger.`,
            "amber",
          );
        });
      }

      // Initial Kanban Render if present
      if (document.getElementById("kanban-cards-qualification")) {
        if (window.INITIAL_DB_OPPORTUNITIES !== undefined && Array.isArray(window.INITIAL_DB_OPPORTUNITIES)) {
          if (window.INITIAL_DB_OPPORTUNITIES.length > 0) {
            const stageMap = { "qualification": "qualification", "proposal": "proposal", "negotiation": "negotiation", "contract": "contract", "won": "won" };
            this.opportunities = window.INITIAL_DB_OPPORTUNITIES.map((o) => {
              let rawStage = (o.stage || "").toLowerCase().trim();
              let matched = "qualification";
              for (const s of Object.keys(stageMap)) {
                if (rawStage.includes(s)) { matched = s; break; }
              }
              return {
                id: "OPP-2026-" + String(o.opp_id).padStart(4, "0"),
                client: o.company_name || ("Enterprise Account " + (o.cus_id || "")),
                title: o.opp_title || (o.sector ? o.sector + " Instrumentation" : "Industrial Automation System"),
                value: parseFloat(o.estimated_value || 0),
                stage: matched,
                probability: parseInt(o.probability_percent || (matched === "negotiation" ? 80 : matched === "proposal" ? 60 : 35)),
                closeDate: o.expected_close_date || "Q4 2026",
                rep: { name: o.sales_representative || "Pavel Orlov", avatar: "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='20' fill='%231B3A5C'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='central' text-anchor='middle' fill='%2300E5FF' font-family='sans-serif' font-weight='bold' font-size='14'%3EPO%3C/text%3E%3C/svg%3E" },
                confidential: !!parseInt(o.is_confidential || 0),
                priority: o.priority || "high"
              };
            });
          } else {
            this.opportunities = [];
          }
        }
        this.renderKanban();
      }

      // Initialize Customer Directory interactions
      this.initCustomerDirectory();

      // Initialize responsive multi-device layout controls
      this.initResponsiveLayout();
    },

    showToast: function (title, message, type = "info") {
      let container = document.getElementById("toast-container");
      if (!container) {
        container = document.createElement("div");
        container.id = "toast-container";
        document.body.appendChild(container);
      }
      const toast = document.createElement("div");
      toast.className = `toast toast-${type}`;
      toast.style.cssText = "background: #1e293b; color: #fff; padding: 12px 16px; border-radius: 8px; margin-top: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); border-left: 4px solid " + (type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6') + "; font-size: 13px; z-index: 9999; animation: fadeIn 0.2s ease;";
      toast.innerHTML = `<div style="font-weight:600;margin-bottom:2px;">${title}</div><div style="opacity:0.85;">${message}</div>`;
      container.appendChild(toast);
      setTimeout(() => {
        toast.style.opacity = "0";
        toast.style.transition = "opacity 0.3s ease";
        setTimeout(() => toast.remove(), 300);
      }, 3500);
    },

    deleteCustomer: function (cusId) {
      if (!confirm(`Are you sure you want to permanently delete customer account ${cusId}?\nThis action will remove the account and associated records.`)) {
        return;
      }
      fetch(`api/customers.php?id=${encodeURIComponent(cusId)}`, {
        method: "DELETE",
        headers: { "Accept": "application/json" }
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === "success" || data.success) {
          crmApp.showToast("Account Deleted", `Customer ${cusId} was removed successfully.`, "success");
          const row = document.querySelector(`tr[data-cus-id="${cusId}"]`) || document.querySelector(`tr[data-id="${cusId}"]`);
          if (row) {
            row.style.transition = "opacity 0.3s ease";
            row.style.opacity = "0";
            setTimeout(() => row.remove(), 300);
          } else {
            /* in-place deletion */ document.querySelectorAll("tr").forEach(r => { if (r.textContent.includes(cusId)) r.remove(); });
          }
        } else {
          (window.vpToast ? window.vpToast(data.message || "Failed to delete customer.", "error") : alert(data.message || "Failed to delete customer."));
        }
      })
      .catch(err => {
        (window.vpToast ? window.vpToast("Error deleting customer: " + err.message, "error") : alert("Error deleting customer: " + err.message));
      });
    },

    initCustomerDirectory: function () {
      const pillButtons = document.querySelectorAll(".filter-pills-group .filter-pill-btn");
      if (pillButtons.length) {
        pillButtons.forEach(btn => {
          btn.addEventListener("click", (e) => {
            e.preventDefault();
            pillButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");

            let sector = btn.getAttribute("data-sector") || "";
            if (!sector) {
              const text = btn.textContent.trim();
              sector = text.replace(/\s*\(\d+\)$/, "").trim();
            }
            const target = sector.toLowerCase().trim();
            const rows = document.querySelectorAll("#customers-tbody tr.account-row");
            let visibleCount = 0;
            rows.forEach(r => {
              const rowSector = (r.getAttribute("data-sector") || "").toLowerCase().trim();
              const match = target === "all" || rowSector === target || rowSector.includes(target) || target.includes(rowSector);
              r.style.display = match ? "" : "none";
              if (match) visibleCount++;
            });

            let noRowsEl = document.getElementById("no-customers-filter-row");
            if (visibleCount === 0) {
              if (!noRowsEl) {
                const tr = document.createElement("tr");
                tr.id = "no-customers-filter-row";
                tr.innerHTML = '<td colspan="7" style="text-align:center;padding:32px;color:var(--crm-text-muted);">No customer accounts found for this sector.</td>';
                const tbody = document.getElementById("customers-tbody");
                if (tbody) tbody.appendChild(tr);
              } else {
                noRowsEl.style.display = "";
              }
            } else if (noRowsEl) {
              noRowsEl.style.display = "none";
            }
          });
        });
      }

      // Sort dropdown
      const sortSelect = document.querySelector(".filter-select");
      const tbody = document.getElementById("customers-tbody");
      if (sortSelect && tbody) {
        sortSelect.addEventListener("change", () => {
          const rows = Array.from(tbody.querySelectorAll("tr.account-row"));
          const val = sortSelect.value.toLowerCase();
          if (val.includes("annual") || val.includes("arr")) {
            rows.sort((a, b) => {
              const parseArr = el => {
                const txt = el.querySelector(".crm-mono-navy-lg")?.textContent || "0";
                return parseFloat(txt.replace(/[^0-9.-]+/g, "")) || 0;
              };
              return parseArr(b) - parseArr(a);
            });
          } else if (val.includes("health")) {
            rows.sort((a, b) => {
              const parseHealth = el => parseInt(el.querySelector(".crm-mono-bold-success, .crm-mono-bold-amber, .crm-mono-bold-danger")?.textContent || "0");
              return parseHealth(b) - parseHealth(a);
            });
          } else if (val.includes("id") || val.includes("account")) {
            rows.sort((a, b) => {
              const idA = a.getAttribute("data-cus-id") || "";
              const idB = b.getAttribute("data-cus-id") || "";
              return idA.localeCompare(idB);
            });
          }
          rows.forEach(r => tbody.appendChild(r));
        });
      }

      const searchInput = document.getElementById("global-omni-search");
      if (searchInput && tbody) {
        let searchTimer = null;
        searchInput.addEventListener("input", () => {
          clearTimeout(searchTimer);
          searchTimer = setTimeout(async () => {
            const q = searchInput.value.trim();
            const fetchFn = window.vpFetch || (async (u) => {
              const r = await fetch(u);
              return await r.json();
            });
            try {
              const res = await fetchFn(`api/customers.php?q=${encodeURIComponent(q)}&per_page=100`);
              const rows = (res && res.rows) ? res.rows : ((res && res.data) ? res.data : []);
              crmApp.renderCustomerRows(rows);
            } catch (err) {
              console.error("Failed to query customers:", err);
            }
          }, 300);
        });
      }
    },

    renderCustomerRows: function (customers) {
      const tbody = document.getElementById("customers-tbody");
      if (!tbody) return;

      const esc = (s) => {
        if (s == null) return "";
        return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
      };

      if (!customers || customers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:32px;color:var(--crm-text-muted);">No accounts found matching query.</td></tr>';
        return;
      }

      tbody.innerHTML = customers.map((c) => {
        const hs = parseInt(c.health_score || 90);
        const hsClass = hs >= 92 ? "crm-mono-bold-success" : (hs >= 80 ? "crm-mono-bold-amber" : "crm-mono-bold-danger");
        const tierClass = String(c.account_tier || "").toLowerCase().includes("strategic") ? "tier-badge strategic" : "tier-badge tier-1";
        const arr = parseFloat(c.total_contract_arr || c.total_contract_value || 0);
        const arrStr = (arr / 1000000).toFixed(2);
        const contractRef = c.active_contract_ref || "None";
        const contractEnd = c.contract_end || "Ongoing";

        return `
          <tr class="account-row account-row-tagged" data-cus-id="${esc(c.cus_id)}" data-sector="${esc(c.sector)}" onclick="window.location.href='CustomerDetail.php?id=${encodeURIComponent(c.cus_id)}'">
            <td>
              <div class="account-name-cell">
                <div class="account-avatar-pill">${esc(c.company_name ? c.company_name.substring(0, 2).toUpperCase() : "CU")}</div>
                <div>
                  <div class="account-company-title">${esc(c.company_name)}</div>
                  <div class="crm-text-xs-mono">${esc(c.cus_id)} · ${esc(c.headquarters || "HQ")}</div>
                </div>
              </div>
            </td>
            <td>
              <span class="sector-pill">${esc(c.sector || "Industrial")}</span>
            </td>
            <td>
              <div class="account-director-name">${esc(c.account_manager_name || "Unassigned")}</div>
              <div class="crm-text-xs-secondary">${esc(c.primary_contact_name || "")}</div>
            </td>
            <td>
              <span class="crm-mono-navy-lg">$${arrStr}M</span>
              <div class="crm-text-xs-secondary">ARR (Active Contracts)</div>
            </td>
            <td>
              <span class="crm-mono-11-navy">${esc(contractRef)}</span>
              <div class="crm-text-xs-secondary">Exp: ${esc(contractEnd)}</div>
            </td>
            <td>
              <span class="${hsClass}">${hs}%</span>
              <span class="${tierClass}">${esc(c.account_tier || "Tier-1")}</span>
            </td>
            <td onclick="event.stopPropagation()">
              <button class="btn btn-outline btn-sm" onclick="window.location.href='CustomerDetail.php?id=${encodeURIComponent(c.cus_id)}'">
                Inspect
              </button>
            </td>
          </tr>
        `;
      }).join("");
    },

    initResponsiveLayout: function () {
      const brandSection =
        document.querySelector(".brand-section") ||
        document.querySelector(".top-nav__content");
      let toggleBtn = document.getElementById("crm-sidebar-toggle");
      if (!toggleBtn && brandSection) {
        toggleBtn = document.createElement("button");
        toggleBtn.id = "crm-sidebar-toggle";
        toggleBtn.className = "mobile-nav-toggle";
        toggleBtn.setAttribute("aria-label", "Toggle Navigation Menu");
        toggleBtn.innerHTML = "☰";
        brandSection.insertBefore(toggleBtn, brandSection.firstChild);
      }

      let backdrop = document.querySelector(".sidebar-backdrop");
      if (!backdrop) {
        backdrop = document.createElement("div");
        backdrop.className = "sidebar-backdrop";
        document.body.appendChild(backdrop);
      }

      if (toggleBtn) {
        toggleBtn.addEventListener("click", (e) => {
          e.stopPropagation();
          document.body.classList.toggle("sidebar-open");
          toggleBtn.innerHTML = document.body.classList.contains("sidebar-open")
            ? "✕"
            : "☰";
        });
      }

      backdrop.addEventListener("click", () => {
        document.body.classList.remove("sidebar-open");
        if (toggleBtn) toggleBtn.innerHTML = "☰";
      });

      document
        .querySelectorAll(".sidebar-nav-item, .sidebar a")
        .forEach((link) => {
          link.addEventListener("click", () => {
            if (window.innerWidth <= 1024) {
              document.body.classList.remove("sidebar-open");
              if (toggleBtn) toggleBtn.innerHTML = "☰";
            }
          });
        });

      document
        .querySelectorAll("table.accounts-table, table.data-table, table")
        .forEach((table) => {
          if (
            !table.parentElement.classList.contains("table-responsive") &&
            !table.parentElement.classList.contains("accounts-table-container")
          ) {
            const wrapper = document.createElement("div");
            wrapper.className = "table-responsive";
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
          }
        });

      window.addEventListener("resize", () => {
        if (
          window.innerWidth > 1024 &&
          document.body.classList.contains("sidebar-open")
        ) {
          document.body.classList.remove("sidebar-open");
          if (toggleBtn) toggleBtn.innerHTML = "☰";
        }
      });
    },
  };

  // Expose globally
  window.crmApp = crmApp;

  // Auto-run on DOM Ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => crmApp.init());
  } else {
    crmApp.init();
  }
})();
