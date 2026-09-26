/**
 * VOSTOKPRIBOR Customer Portal — Live Data Integration  (Class 3)
 * Requires: ../../assets/js/api-client.js
 *
 * Pages covered:
 *  - Dashboard.php   → KPI summary cards
 *  - ProjectListAndDetail.php → project table
 *  - Invoices.php    → invoice table
 *  - SupportTicketView.php → ticket list + submit form
 */

(function () {
  "use strict";

  const { customer, ui, escHtml, handleApiError } = window.VostokAPI;

  /* ──────────────────────────────────────────────
   * DASHBOARD — KPI Cards
   * Elements:
   *   #kpi-active-projects, #kpi-open-invoices,
   *   #kpi-open-tickets,    #kpi-total-contract
   * ────────────────────────────────────────────── */
  async function loadDashboard() {
    const kpiProjects = document.getElementById("kpi-active-projects");
    const kpiInvoices = document.getElementById("kpi-open-invoices");
    const kpiTickets = document.getElementById("kpi-open-tickets");
    const kpiContract = document.getElementById("kpi-total-contract");

    if (!kpiProjects && !kpiInvoices && !kpiTickets && !kpiContract) return;

    try {
      const res = await customer.dashboard();
      const d = res.data;

      if (kpiProjects) kpiProjects.textContent = d.active_projects ?? d.total_projects ?? "—";
      if (kpiInvoices) kpiInvoices.textContent = d.open_invoices ?? d.pending_invoices ?? d.total_invoices ?? "—";
      if (kpiTickets) kpiTickets.textContent = d.open_tickets ?? "—";
      if (kpiContract)
        kpiContract.textContent = (d.total_contract_value || d.pending_balance_eur)
          ? ui.currency(d.total_contract_value || d.pending_balance_eur, d.currency || "USD")
          : "—";

      // Also update last-sync timestamp if present
      const syncEl = document.querySelector("[data-sync-time]");
      if (syncEl && res.meta?.timestamp) {
        syncEl.textContent = "Last synced: " + ui.date(res.meta.timestamp);
      }
    } catch (err) {
      handleApiError(err, "Portal Dashboard");
    }
  }

  /* ──────────────────────────────────────────────
   * PROJECTS TABLE
   * Element: #projects-tbody
   * ────────────────────────────────────────────── */
  async function loadProjects() {
    const tbody = document.getElementById("projects-tbody");
    if (!tbody) return;

    tbody.innerHTML =
      '<tr><td colspan="7" style="text-align:center;padding:28px;"><span class="vp-spinner"></span> Loading projects…</td></tr>';

    try {
      const res = await customer.projects();
      const rows = res.data;

      if (!rows.length) {
        tbody.innerHTML =
          '<tr><td colspan="7" style="text-align:center;padding:28px;opacity:.5;">No projects found for your account.</td></tr>';
        return;
      }

      const statusColor = {
        Active: "#2ecc71",
        "In Progress": "#3498db",
        Completed: "#9b59b6",
        "On Hold": "#f39c12",
        Delayed: "#e74c3c",
      };

      tbody.innerHTML = rows
        .map((p) => {
          const color = statusColor[p.status] || "#aaa";
          return `
          <tr class="project-row">
            <td>
              <strong>${escHtml(p.prj_id)}</strong>
            </td>
            <td>
              <div style="font-weight:600;">${escHtml(p.project_name || p.prj_title)}</div>
              <div style="font-size:11px;opacity:.6;">${escHtml(p.description || "").substring(0, 60)}…</div>
            </td>
            <td>
              <span style="display:inline-block;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;
                background:${color}20;color:${color};border:1px solid ${color}40;">
                ${escHtml(p.status)}
              </span>
            </td>
            <td>${ui.date(p.start_date)}</td>
            <td>${ui.date(p.expected_completion)}</td>
            <td>
              ${
                p.completion_pct != null
                  ? `
                <div style="display:flex;align-items:center;gap:8px;">
                  <div style="flex:1;height:6px;border-radius:3px;background:rgba(255,255,255,.08);">
                    <div style="height:100%;width:${p.completion_pct}%;border-radius:3px;background:${color};transition:width .5s;"></div>
                  </div>
                  <span style="font-size:11px;font-weight:700;">${p.completion_pct}%</span>
                </div>`
                  : "—"
              }
            </td>
            <td>${escHtml(p.project_manager || "—")}</td>
          </tr>`;
        })
        .join("");
    } catch (err) {
      handleApiError(err, "Projects");
      tbody.innerHTML =
        '<tr><td colspan="7" style="color:#e74c3c;padding:28px;">Failed to load projects.</td></tr>';
    }
  }

  /* ──────────────────────────────────────────────
   * INVOICES TABLE
   * Element: #invoices-tbody
   * ────────────────────────────────────────────── */
  async function loadInvoices() {
    const tbody = document.getElementById("invoices-tbody");
    if (!tbody) return;

    tbody.innerHTML =
      '<tr><td colspan="6" style="text-align:center;padding:28px;"><span class="vp-spinner"></span></td></tr>';

    try {
      const res = await customer.invoices();
      const rows = res.data;

      if (!rows.length) {
        tbody.innerHTML =
          '<tr><td colspan="6" style="text-align:center;padding:28px;opacity:.5;">No invoices on record.</td></tr>';
        return;
      }

      const badgeMap = {
        Paid: "color:#2ecc71;background:rgba(46,204,113,.12)",
        Unpaid: "color:#e74c3c;background:rgba(231,76,60,.12)",
        Overdue: "color:#f39c12;background:rgba(243,156,18,.12)",
        Pending: "color:#3498db;background:rgba(52,152,219,.12)",
      };

      tbody.innerHTML = rows
        .map((inv) => {
          const badge =
            badgeMap[inv.payment_status] ||
            "color:#aaa;background:rgba(170,170,170,.1)";
          return `
          <tr class="invoice-row">
            <td style="font-family:monospace;font-weight:700;">${escHtml(inv.inv_id)}</td>
            <td>${escHtml(inv.prj_id || "—")}</td>
            <td>
              <strong style="font-size:15px;">${ui.currency(inv.total_value || 0, inv.currency || "EUR")}</strong>
            </td>
            <td>
              <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;${badge};">
                ${escHtml(inv.payment_status)}
              </span>
            </td>
            <td>${ui.date(inv.issued_at)}</td>
            <td>${inv.paid_at ? ui.date(inv.paid_at) : '<em style="opacity:.4">—</em>'}</td>
          </tr>`;
        })
        .join("");
    } catch (err) {
      handleApiError(err, "Invoices");
      tbody.innerHTML =
        '<tr><td colspan="6" style="color:#e74c3c;padding:28px;">Failed to load invoices.</td></tr>';
    }
  }

  /* ──────────────────────────────────────────────
   * SUPPORT TICKETS
   * Element: #tickets-list
   * ────────────────────────────────────────────── */
  async function loadTickets() {
    const list = document.getElementById("tickets-list");
    if (!list) return;

    list.innerHTML =
      '<div style="padding:24px;text-align:center;"><span class="vp-spinner"></span> Loading tickets…</div>';

    try {
      const res = await customer.tickets();
      const tickets = res.data;

      if (!tickets.length) {
        list.innerHTML =
          '<div style="padding:24px;text-align:center;opacity:.5;">No support tickets found.</div>';
        return;
      }

      const priorityColor = {
        Critical: "#e74c3c",
        High: "#f39c12",
        Medium: "#3498db",
        Low: "#2ecc71",
      };
      const statusColor = {
        Open: "#2ecc71",
        "In Progress": "#3498db",
        Closed: "#9b59b6",
        Pending: "#f39c12",
      };

      list.innerHTML = tickets
        .map((t) => {
          const pc = priorityColor[t.priority] || "#aaa";
          const sc = statusColor[t.status] || "#aaa";
          const tId = t.tkt_id || t.ticket_id || "TKT-2026-000";
          const subject = t.subject || t.title || t.source_system || "Support Request";
          const desc = t.description || t.message || "";
          return `
          <div class="ticket-card" style="border-left:3px solid ${pc};">
            <div class="ticket-header">
              <span style="font-family:monospace;font-size:12px;font-weight:700;">${escHtml(tId)}</span>
              <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;background:${sc}20;color:${sc};">${escHtml(t.status)}</span>
            </div>
            <div class="ticket-subject" style="font-weight:700;margin:6px 0 4px;">${escHtml(subject)}</div>
            <div class="ticket-desc" style="font-size:12px;opacity:.65;line-height:1.4;">${escHtml(desc.substring(0, 120))}${desc.length > 120 ? "…" : ""}</div>
            <div class="ticket-meta" style="margin-top:10px;display:flex;gap:16px;font-size:11px;opacity:.55;">
              <span>Priority: <strong style="color:${pc};">${escHtml(t.priority)}</strong></span>
              <span>Category: ${escHtml(t.category || t.source_system || "Technical")}</span>
              <span>Submitted: ${ui.date(t.created_at)}</span>
              ${t.sla_deadline ? `<span>SLA: ${ui.date(t.sla_deadline)}</span>` : ""}
            </div>
          </div>`;
        })
        .join("");
    } catch (err) {
      handleApiError(err, "Support Tickets");
      list.innerHTML =
        '<div style="padding:24px;color:#e74c3c;">Failed to load tickets.</div>';
    }
  }

  /* ──────────────────────────────────────────────
   * TICKET SUBMIT FORM
   * Element: #ticket-submit-form
   * ────────────────────────────────────────────── */
  function initTicketForm() {
    const form = document.getElementById("ticket-submit-form");
    if (!form) return;

    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      const btn = form.querySelector('button[type="submit"]');

      const payload = {
        subject: form.querySelector('[name="subject"]')?.value,
        description: form.querySelector('[name="description"]')?.value,
        priority: form.querySelector('[name="priority"]')?.value,
        category: form.querySelector('[name="category"]')?.value,
      };

      if (!payload.subject || !payload.description) {
        ui.toast(
          "Validation",
          "Subject and description are required.",
          "warning",
        );
        return;
      }

      try {
        await ui.withLoading(btn, customer.submitTicket(payload));
        ui.toast(
          "Ticket Submitted",
          "Your support request has been created.",
          "success",
        );
        form.reset();
        loadTickets(); // refresh list
      } catch (err) {
        handleApiError(err, "Submit Ticket");
      }
    });
  }

  /* ──────────────────────────────────────────────
   * Boot
   * ────────────────────────────────────────────── */
  document.addEventListener("DOMContentLoaded", () => {
    loadDashboard();
    loadProjects();
    loadInvoices();
    loadTickets();
    initTicketForm();
  });
})();
