/**
 * VOSTOKPRIBOR Finance & Billing Platform (SYS-04 / SYS-08)
 * Real Database-driven Client Controller & API Integration
 */

(function () {
  "use strict";

  const finApp = {
    apiBase: "api/finance_api.php",

    formatCurrency: function (val, curr = "EUR") {
      const sym = curr === "USD" ? "$" : curr === "RUB" ? "₽" : "€";
      return (
        sym +
        Number(val || 0).toLocaleString("en-US", {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        })
      );
    },

    showToast: function (title, message, type = "green") {
      let container = document.getElementById("toast-container");
      if (!container) {
        container = document.createElement("div");
        container.id = "toast-container";
        document.body.appendChild(container);
      }

      const toast = document.createElement("div");
      let icon = "💳";
      if (type === "amber") icon = "⚡";
      if (type === "red" || type === "alert" || type === "error") icon = "⚠️";
      if (type === "success" || type === "green") icon = "✓";

      toast.className = "fin-toast";
      if (type === "amber")
        toast.style.borderLeftColor = "var(--fin-amber, #E8A33D)";
      if (type === "red" || type === "alert" || type === "error")
        toast.style.borderLeftColor = "var(--fin-confidential, #B23A32)";
      if (type === "success" || type === "green")
        toast.style.borderLeftColor = "var(--fin-green, #2E6E4E)";

      toast.innerHTML = `
        <div style="font-size: 16px;">${icon}</div>
        <div style="flex: 1;">
          <div style="font-weight: 700; font-size: 12.5px; color: #FFFFFF;">${title}</div>
          <div style="font-size: 11px; color: rgba(255,255,255,0.85); margin-top: 2px;">${message}</div>
        </div>
        <button style="color: rgba(255,255,255,0.6); font-size: 14px; background: none; border: none; cursor: pointer;" onclick="this.parentElement.remove()">✕</button>
      `;

      container.appendChild(toast);

      setTimeout(() => {
        toast.style.transition = "all 0.3s ease";
        toast.style.opacity = "0";
        toast.style.transform = "translateY(10px)";
        setTimeout(() => toast.remove(), 300);
      }, 4500);
    },

    openModal: function (modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.add("active");
        const input = modal.querySelector(
          'input:not([type="hidden"]), select, textarea',
        );
        if (input) input.focus();
      }
    },

    closeModal: function (modalId) {
      const modal = document.getElementById(modalId);
      if (modal) modal.classList.remove("active");
    },

    // Inspect Invoice Details - Loads LIVE from Database via API
    inspectInvoice: function (invId) {
      if (!invId) return;

      const idEl = document.getElementById("modal-inv-id");
      const custEl = document.getElementById("modal-inv-cust");
      const projEl = document.getElementById("modal-inv-proj");
      const totalEl = document.getElementById("modal-inv-total");
      const statusEl = document.getElementById("modal-inv-status");
      const issueEl = document.getElementById("modal-inv-issue");
      const dueEl = document.getElementById("modal-inv-due");
      const itemsBody = document.getElementById("modal-inv-items-body");
      const timelineBody = document.getElementById("modal-inv-timeline-body");
      const btnMarkPaid = document.getElementById("btn-modal-mark-paid");
      const btnSendReminder = document.getElementById(
        "btn-modal-send-reminder",
      );

      if (idEl) idEl.textContent = invId;
      if (custEl) custEl.textContent = "Loading from database...";
      if (itemsBody)
        itemsBody.innerHTML =
          '<tr><td colspan="4" style="text-align: center; padding: 1.5rem; color: var(--fin-text-muted);">Fetching ledger items from database...</td></tr>';
      if (timelineBody)
        timelineBody.innerHTML =
          '<div style="color: var(--fin-text-muted); font-size: 12px;">Loading payment telemetry...</div>';

      this.openModal("modal-invoice-detail");

      fetch(
        `${this.apiBase}?action=get_invoice_detail&inv_id=${encodeURIComponent(invId)}`,
      )
        .then((res) => res.json())
        .then((data) => {
          if (!data.success || !data.invoice) {
            finApp.showToast(
              "Ledger Error",
              data.error || "Failed to retrieve invoice from DB.",
              "error",
            );
            finApp.closeModal("modal-invoice-detail");
            return;
          }

          const inv = data.invoice;
          const curr = inv.currency || "EUR";

          if (idEl) idEl.textContent = inv.inv_id;
          if (custEl)
            custEl.textContent =
              inv.company_name || "Client Account " + inv.cus_id;
          if (projEl)
            projEl.textContent = inv.project_name
              ? `${inv.project_name} (${inv.prj_id})`
              : `Project #${inv.prj_id || "GENERAL"}`;
          if (totalEl)
            totalEl.textContent = finApp.formatCurrency(inv.total_value, curr);
          if (issueEl) issueEl.textContent = inv.issued_at || "N/A";
          if (dueEl) dueEl.textContent = inv.due_date || "Net-30";

          if (statusEl) {
            if (inv.payment_status === "Paid") {
              statusEl.className = "status-badge-paid";
              statusEl.innerHTML = "✓ Paid";
            } else if (inv.payment_status === "Pending") {
              statusEl.className = "status-badge-pending";
              statusEl.innerHTML = "⚡ Pending";
            } else {
              statusEl.className = "status-badge-overdue";
              statusEl.innerHTML = "⚠️ Overdue";
            }
          }

          // Populate Line Items from DB
          if (itemsBody) {
            itemsBody.innerHTML = "";
            if (data.items && data.items.length > 0) {
              data.items.forEach((item) => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                  <td>
                    <div style="font-weight: 600; color: var(--fin-navy);">${escapeHtml(item.description)}</div>
                    <div style="font-family: var(--fin-font-mono); font-size: 10.5px; color: var(--fin-text-muted);">${escapeHtml(item.part_number || "VP-PART-STD")}</div>
                  </td>
                  <td style="text-align: center; font-family: var(--fin-font-mono); font-weight: 600;">${item.qty}</td>
                  <td style="text-align: right; font-family: var(--fin-font-mono);">${finApp.formatCurrency(item.unit_price, curr)}</td>
                  <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-navy);">${finApp.formatCurrency(item.total_price, curr)}</td>
                `;
                itemsBody.appendChild(tr);
              });
            } else {
              itemsBody.innerHTML = `
                <tr>
                  <td><div style="font-weight: 600; color: var(--fin-navy);">Standard Instrumentation Milestone Contract</div><div style="font-family: var(--fin-font-mono); font-size: 10.5px; color: var(--fin-text-muted);">VP-CONTRACT-LINE</div></td>
                  <td style="text-align: center; font-family: var(--fin-font-mono);">1</td>
                  <td style="text-align: right; font-family: var(--fin-font-mono);">${finApp.formatCurrency(inv.total_value, curr)}</td>
                  <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 700;">${finApp.formatCurrency(inv.total_value, curr)}</td>
                </tr>
              `;
            }
          }

          // Populate Payments & Reconciliation History from DB
          if (timelineBody) {
            timelineBody.innerHTML = "";
            if (data.payments && data.payments.length > 0) {
              data.payments.forEach((p) => {
                const step = document.createElement("div");
                step.style.cssText =
                  "display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.85rem; background: var(--fin-surface-dim); border-radius: var(--fin-radius-sm); border-left: 3px solid var(--fin-green); margin-bottom: 0.4rem;";
                step.innerHTML = `
                  <div>
                    <div style="font-weight: 600; color: var(--fin-navy); font-size: 12px;">${escapeHtml(p.method || "Direct Wire")} · <span style="font-family: var(--fin-font-mono);">${escapeHtml(p.tx_reference || "REF-" + p.payment_id)}</span></div>
                    <div style="font-size: 11px; color: var(--fin-text-muted);">${escapeHtml(p.payment_date || "")} ${p.remittance_memo ? "· " + escapeHtml(p.remittance_memo) : ""}</div>
                  </div>
                  <div style="text-align: right;">
                    <div style="font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-navy);">${finApp.formatCurrency(p.amount, curr)}</div>
                    <div style="font-size: 10.5px; font-weight: 600; color: var(--fin-green);">${p.reconciled ? "Reconciled ✓" : "Pending Match ⚡"}</div>
                  </div>
                `;
                timelineBody.appendChild(step);
              });
            } else {
              timelineBody.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.85rem; background: var(--fin-surface-dim); border-radius: var(--fin-radius-sm); border-left: 3px solid ${inv.payment_status === "Paid" ? "var(--fin-green)" : "var(--fin-amber)"};">
                  <div>
                    <div style="font-weight: 600; color: var(--fin-navy); font-size: 12px;">${inv.payment_status === "Paid" ? "Milestone Settlement Completed" : "Awaiting Settlement Clearance"}</div>
                    <div style="font-size: 11px; color: var(--fin-text-muted);">Due: ${inv.due_date || "Net-30"} · Terms: ${escapeHtml(inv.payment_terms || "Net-30")}</div>
                  </div>
                  <div style="text-align: right;">
                    <div style="font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-navy);">${finApp.formatCurrency(inv.total_value, curr)}</div>
                    <div style="font-size: 10.5px; font-weight: 600; color: ${inv.payment_status === "Paid" ? "var(--fin-green)" : "var(--fin-amber)"};">${inv.payment_status}</div>
                  </div>
                </div>
              `;
            }
          }

          // Attach actions to buttons
          if (btnMarkPaid) {
            btnMarkPaid.style.display =
              inv.payment_status === "Paid" ? "none" : "inline-flex";
            btnMarkPaid.onclick = () => finApp.markInvoicePaid(inv.inv_id);
          }
          if (btnSendReminder) {
            btnSendReminder.style.display =
              inv.payment_status === "Paid" ? "none" : "inline-flex";
            btnSendReminder.onclick = () =>
              finApp.sendPaymentReminder(inv.inv_id);
          }
        })
        .catch((err) => {
          finApp.showToast(
            "Network / DB Error",
            err.message || "Error communicating with database.",
            "error",
          );
        });
    },

    // Mark Invoice as Paid in DB
    markInvoicePaid: function (invId) {
      if (
        !confirm(`Confirm mark invoice ${invId} as PAID in the General Ledger?`)
      )
        return;

      const fd = new FormData();
      fd.append("action", "mark_paid");
      fd.append("inv_id", invId);

      fetch(this.apiBase, { method: "POST", body: fd })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            finApp.showToast(
              "Ledger Updated",
              data.message || `Invoice ${invId} marked as PAID.`,
              "green",
            );
            finApp.closeModal("modal-invoice-detail");
            setTimeout(() => window.location.reload(), 800);
          } else {
            finApp.showToast(
              "Error",
              data.error || "Failed to update invoice.",
              "error",
            );
          }
        })
        .catch((err) => finApp.showToast("Error", err.message, "error"));
    },

    // Send Payment Reminder via DB Notification
    sendPaymentReminder: function (invId) {
      const fd = new FormData();
      fd.append("action", "send_reminder");
      fd.append("inv_id", invId);

      fetch(this.apiBase, { method: "POST", body: fd })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            finApp.showToast("Notice Dispatched", data.message, "amber");
            finApp.closeModal("modal-invoice-detail");
          } else {
            finApp.showToast(
              "Error",
              data.error || "Failed to dispatch reminder.",
              "error",
            );
          }
        })
        .catch((err) => finApp.showToast("Error", err.message, "error"));
    },

    // Reconcile Individual Payment to DB
    reconcilePayment: function (paymentId, invId = null) {
      const fd = new FormData();
      fd.append("action", "reconcile_payment");
      fd.append("payment_id", paymentId);
      if (invId) fd.append("inv_id", invId);

      fetch(this.apiBase, { method: "POST", body: fd })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            finApp.showToast(
              "Reconciliation Executed",
              data.message || `Payment #${paymentId} matched to ledger.`,
              "green",
            );
            const row =
              document.getElementById(`tx-row-${paymentId}`) ||
              document.getElementById(`tx-row-TX-SPFS-${paymentId}`) ||
              document.querySelector(`[data-payment-id="${paymentId}"]`);
            if (row) {
              row.style.transition = "all 0.3s ease";
              row.style.opacity = "0";
              setTimeout(() => {
                row.remove();
                // If table is empty, reload
                const tbody = document.querySelector(
                  "#unmatched-desk-body, .fin-table tbody",
                );
                if (tbody && tbody.children.length === 0) {
                  window.location.reload();
                }
              }, 300);
            } else {
              setTimeout(() => window.location.reload(), 800);
            }
          } else {
            finApp.showToast(
              "Reconciliation Error",
              data.error || "Failed to reconcile payment.",
              "error",
            );
          }
        })
        .catch((err) => finApp.showToast("Error", err.message, "error"));
    },

    // Run Auto-Match Engine across DB
    runAutoMatch: function () {
      finApp.showToast(
        "Auto-Match Running",
        "Scanning incoming bank telemetry wires against pending customer receivables...",
        "amber",
      );

      const fd = new FormData();
      fd.append("action", "auto_reconcile");

      fetch(this.apiBase, { method: "POST", body: fd })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            finApp.showToast(
              "Auto-Reconciliation Complete",
              data.message,
              "green",
            );
            setTimeout(() => window.location.reload(), 1000);
          } else {
            finApp.showToast(
              "Auto-Match Failed",
              data.error || "Failed auto-reconciliation.",
              "error",
            );
          }
        })
        .catch((err) => finApp.showToast("Error", err.message, "error"));
    },

    // Sync Bank Feeds (Pull incoming wire into DB)
    syncBankFeeds: function () {
      finApp.showToast(
        "Gateway Connection",
        "Connecting to Sberbank & SPFS electronic settlement nodes...",
        "amber",
      );

      const fd = new FormData();
      fd.append("action", "sync_bank_feeds");

      fetch(this.apiBase, { method: "POST", body: fd })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            finApp.showToast("Telemetry Ingested", data.message, "green");
            setTimeout(() => window.location.reload(), 1000);
          } else {
            finApp.showToast(
              "Sync Error",
              data.error || "Failed to refresh feeds.",
              "error",
            );
          }
        })
        .catch((err) => finApp.showToast("Error", err.message, "error"));
    },

    // Bill Milestone (Generate Invoice from Cycle in DB)
    billMilestone: function (cycleId) {
      if (!cycleId) {
        finApp.showToast(
          "Select Milestone",
          "Please select an unbilled project milestone to bill.",
          "amber",
        );
        return;
      }

      if (
        !confirm(
          "Ratify milestone completion and generate commercial invoice in database?",
        )
      )
        return;

      const fd = new FormData();
      fd.append("action", "bill_milestone");
      fd.append("cycle_id", cycleId);

      fetch(this.apiBase, { method: "POST", body: fd })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            finApp.showToast(
              "Milestone Billed",
              `${data.message} Saved to database.`,
              "green",
            );
            setTimeout(() => {
              window.location.href = "Invoices.php";
            }, 1000);
          } else {
            finApp.showToast(
              "Billing Failed",
              data.error || "Could not bill milestone.",
              "error",
            );
          }
        })
        .catch((err) => finApp.showToast("Error", err.message, "error"));
    },

    // Handle Create Invoice Form Submission to DB
    handleCreateInvoiceSubmit: function (form) {
      const fd = new FormData(form);
      fd.append("action", "create_invoice");

      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = "Recording in DB...";
      }

      fetch(this.apiBase, { method: "POST", body: fd })
        .then((res) => res.json())
        .then((data) => {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = "Ratify & Issue Invoice";
          }

          if (data.success) {
            finApp.showToast(
              "Invoice Issued",
              `Invoice ${data.inv_id} created and committed to database!`,
              "green",
            );
            finApp.closeModal("modal-create-invoice");
            setTimeout(() => window.location.reload(), 800);
          } else {
            finApp.showToast(
              "Error Creating Invoice",
              data.error || "Failed to create invoice.",
              "error",
            );
          }
        })
        .catch((err) => {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = "Ratify & Issue Invoice";
          }
          finApp.showToast("Database Error", err.message, "error");
        });
    },

    // Handle Update Budget Form Submission to DB
    handleUpdateBudgetSubmit: function (form) {
      const fd = new FormData(form);
      fd.append("action", "update_budget");

      fetch(this.apiBase, { method: "POST", body: fd })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            finApp.showToast("Budget Updated", data.message, "green");
            finApp.closeModal("modal-update-budget");
            setTimeout(() => window.location.reload(), 800);
          } else {
            finApp.showToast(
              "Budget Error",
              data.error || "Failed to update budget.",
              "error",
            );
          }
        })
        .catch((err) =>
          finApp.showToast("Database Error", err.message, "error"),
        );
    },

    // Filter Invoices Table dynamically
    filterInvoices: function () {
      const searchInput = document.getElementById("invoice-search");
      const statusFilter = document.getElementById("invoice-status-filter");

      const query = (searchInput ? searchInput.value : "").toLowerCase().trim();
      const selectedStatus = statusFilter ? statusFilter.value : "all";

      const rows = document.querySelectorAll(
        ".invoice-table-body tr.fin-table-row",
      );
      let visibleCount = 0;

      rows.forEach((row) => {
        const id = (row.getAttribute("data-id") || "").toLowerCase();
        const customer = (
          row.getAttribute("data-customer") || ""
        ).toLowerCase();
        const project = (row.getAttribute("data-project") || "").toLowerCase();
        const status = row.getAttribute("data-status") || "";

        const matchQuery =
          !query ||
          id.includes(query) ||
          customer.includes(query) ||
          project.includes(query);
        const matchStatus =
          selectedStatus === "all" ||
          status.toLowerCase() === selectedStatus.toLowerCase();

        if (matchQuery && matchStatus) {
          row.style.display = "";
          visibleCount++;
        } else {
          row.style.display = "none";
        }
      });

      const countEl = document.getElementById("invoice-visible-count");
      if (countEl) {
        countEl.textContent = visibleCount;
      }
    },

    // Initialize App & Keyboard Shortcuts
    init: function () {
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
        }
      });

      const omniSearch = document.getElementById("global-omni-search");
      if (omniSearch) {
        omniSearch.addEventListener("keydown", (e) => {
          if (e.key === "Enter") {
            const val = omniSearch.value.trim();
            if (val) {
              window.location.href = `Invoices.php?search=${encodeURIComponent(val)}`;
            }
          }
        });
      }

      document.addEventListener("keydown", (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === "k") {
          e.preventDefault();
          if (omniSearch) {
            omniSearch.focus();
            omniSearch.select();
          }
        }
        if (e.key === "Escape") {
          document
            .querySelectorAll(".modal-backdrop.active")
            .forEach((m) => m.classList.remove("active"));
        }
      });

      // Check URL search parameter if on Invoices page
      const urlParams = new URLSearchParams(window.location.search);
      const searchParam = urlParams.get("search");
      if (searchParam) {
        const searchInput = document.getElementById("invoice-search");
        if (searchInput) {
          searchInput.value = searchParam;
          this.filterInvoices();
        }
      }

      // Initialize responsive multi-device layout controls
      this.initResponsiveLayout();
    },

    initResponsiveLayout: function () {
      const brandSection =
        document.querySelector(".brand-section") ||
        document.querySelector(".top-nav__content");
      let toggleBtn = document.getElementById("fin-sidebar-toggle");
      if (!toggleBtn && brandSection) {
        toggleBtn = document.createElement("button");
        toggleBtn.id = "fin-sidebar-toggle";
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

      document.querySelectorAll("table.fin-table").forEach((table) => {
        if (
          !table.parentElement.classList.contains("table-responsive") &&
          !table.parentElement.classList.contains("fin-table-container")
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

  function escapeHtml(str) {
    if (!str) return "";
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  window.finApp = finApp;

  window.openBudgetEdit = function (id, code, allocated, spent) {
    const editId = document.getElementById("edit-budget-id");
    const editCode = document.getElementById("edit-dept-code");
    const editAllocated = document.getElementById("edit-allocated");
    const editSpent = document.getElementById("edit-spent");
    if (editId) editId.value = id;
    if (editCode) editCode.value = code;
    if (editAllocated) editAllocated.value = allocated;
    if (editSpent) editSpent.value = spent;
    finApp.openModal("modal-update-budget");
  };

  window.openReportModal = function (type, title) {
    const titleEl = document.getElementById("report-modal-title");
    if (titleEl) titleEl.textContent = title;

    document.querySelectorAll(".fin-report-tab").forEach((tab) => {
      tab.classList.add("hidden");
    });

    if (type === "Statement of Operations") {
      const tab = document.getElementById("report-tab-operations");
      if (tab) tab.classList.remove("hidden");
    } else if (type === "Balance Sheet") {
      const tab = document.getElementById("report-tab-balance");
      if (tab) tab.classList.remove("hidden");
    } else {
      const tab = document.getElementById("report-tab-tax");
      if (tab) tab.classList.remove("hidden");
    }

    finApp.openModal("modal-report-view");
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => finApp.init());
  } else {
    finApp.init();
  }
})();
