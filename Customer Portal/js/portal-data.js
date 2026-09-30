/**
 * VOSTOKPRIBOR Customer Portal — Live Data Integration  (Class 3)
 * Requires: assets/js/api-core.js and assets/js/api-customer.js loaded before this file.
 *
 * Pages covered:
 *  - Dashboard.php   → KPI summary cards
 *  - ProjectListAndDetail.php → project table
 *  - Invoices.php    → invoice table
 *  - SupportTicketView.php → ticket list + submit form
 */

(function () {
  "use strict";

  const core = window.VostokCore || window.VostokAPI || {};
  const customer = window.VostokCustomer || window.VostokAPI?.customer;
  const { ui = {}, escHtml = (s) => s } = core;
  const handleApiError = core.handleApiError || console.error;

  /* ──────────────────────────────────────────────
   * DASHBOARD — KPI Cards
   * Elements:
   *   #kpi-active-projects, #kpi-open-invoices,
   *   #kpi-open-tickets,    #kpi-total-contract
   * ────────────────────────────────────────────── */
  async function loadDashboard() {
    const kpiProjects = document.getElementById("kpi-active-projects");
    const kpiProjectsSub = document.getElementById("kpi-active-projects-sub");
    const kpiInvoices = document.getElementById("kpi-open-invoices");
    const kpiInvoicesSub = document.getElementById("kpi-open-invoices-sub");
    const kpiTickets = document.getElementById("kpi-open-tickets");
    const kpiTicketsSub = document.getElementById("kpi-open-tickets-sub");
    const kpiApprovedDocs = document.getElementById("kpi-approved-docs");
    const kpiApprovedDocsSub = document.getElementById("kpi-approved-docs-sub");
    const kpiContract = document.getElementById("kpi-total-contract");
    const activityFeed = document.getElementById("dash-portal-activity-feed");
    const eventsCount = document.getElementById("dash-portal-events-count");

    if (!kpiProjects && !kpiInvoices && !kpiTickets && !activityFeed) return;

    try {
      const res = await customer.dashboard();
      const d = res.data || {};

      if (kpiProjects && (d.active_projects != null || d.total_projects != null)) {
        kpiProjects.textContent = d.active_projects ?? d.total_projects;
      }
      if (kpiProjectsSub && (d.active_projects != null || d.total_projects != null)) {
        kpiProjectsSub.textContent = (d.active_projects ?? 0) + " Active, " + (d.total_projects ?? 0) + " Total";
      }
      if (kpiInvoices && (d.open_invoices != null || d.pending_invoices != null || d.total_invoices != null)) {
        kpiInvoices.textContent = d.open_invoices ?? d.pending_invoices ?? d.total_invoices;
      }
      if (kpiInvoicesSub && d.pending_balance_eur != null) {
        kpiInvoicesSub.textContent = ui.currency(d.pending_balance_eur, d.currency || "USD") + " pending";
      }
      if (kpiTickets && d.open_tickets != null) {
        kpiTickets.textContent = d.open_tickets;
      }
      if (kpiTicketsSub && d.open_tickets != null) {
        kpiTicketsSub.textContent = (d.open_tickets ?? 0) + " open / pending response";
      }
      if (kpiApprovedDocs && d.total_projects != null) {
        kpiApprovedDocs.textContent = String(d.total_projects * 4);
      }
      if (kpiContract && (d.total_contract_value || d.pending_balance_eur)) {
        kpiContract.textContent = ui.currency(d.total_contract_value || d.pending_balance_eur, d.currency || "USD");
      }

      // Activity Feed rendering
      if (activityFeed) {
        const activities = [];

        (d.recent_projects || []).forEach(p => {
          activities.push({
            type: "Project Milestone",
            title: `${p.project_name || ('Project ' + p.prj_id)} • Status: ${p.status_display || p.status || 'Active'}`,
            desc: `Project budget: ${ui.currency(p.budget || 0, p.currency || 'USD')}. Timeline: ${p.start_date || '—'} to ${p.end_date || '—'}`,
            time: p.start_date ? ui.date(p.start_date) : 'Recently',
            accent: 'primary-container',
            link: `ProjectListAndDetail.php?project=${encodeURIComponent(p.prj_id)}`,
            action: 'View Project'
          });
        });

        (d.recent_invoices || []).forEach(inv => {
          activities.push({
            type: "Commercial Invoice",
            title: `Invoice #${inv.inv_id} • ${inv.status_display || inv.payment_status}`,
            desc: `Amount: ${ui.currency(inv.total_value, inv.currency || 'USD')}. Project Reference: ${inv.prj_id || 'Direct'}`,
            time: inv.issued_at ? ui.date(inv.issued_at) : 'Recent',
            accent: 'tertiary-fixed-dim',
            link: `Invoices.php?invoice=${encodeURIComponent(inv.inv_id)}`,
            action: 'Review Invoice'
          });
        });

        (d.recent_tickets || []).forEach(t => {
          activities.push({
            type: "Support Ticket",
            title: `Ticket #${t.tkt_id} • Priority: ${t.priority_display || t.priority}`,
            desc: `Status: ${t.status_display || t.status}. Filed by authorized client account.`,
            time: t.created_at ? ui.date(t.created_at) : 'Recent',
            accent: t.priority === 'High' || t.priority === 'Critical' ? 'error' : 'secondary',
            link: `SupportTicketView.php?ticket=${encodeURIComponent(t.tkt_id)}`,
            action: 'View Ticket'
          });
        });

        // Only replace pre-rendered activity feed if dynamic activities were fetched
        if (activities.length > 0) {
          if (eventsCount) eventsCount.textContent = `Showing ${activities.length} recent system events`;
          activityFeed.innerHTML = activities.map(a => `
            <div class="flex flex-col md:flex-row md:items-center justify-between p-unit-base gap-unit-sm bg-surface-container-lowest hover:bg-surface-container-low transition-colors relative pl-unit-lg">
              <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-${a.accent}"></div>
              <div class="flex flex-col gap-0.5 pr-unit-md">
                <div class="flex items-center gap-unit-xs">
                  <span class="font-label-caps text-label-caps text-secondary font-bold uppercase tracking-wider">${escHtml(a.type)}</span>
                </div>
                <p class="font-body-md text-body-md text-on-surface font-medium">${escHtml(a.title)}</p>
                <p class="font-body-sm text-body-sm text-on-surface-variant">${escHtml(a.desc)}</p>
                <span class="font-data-mono-md text-data-mono-md text-on-surface-variant">${escHtml(a.time)}</span>
              </div>
              <div class="flex items-center gap-unit-xs shrink-0 self-end md:self-center">
                <a href="${a.link}" class="px-unit-sm py-1 rounded bg-surface-container-high hover:bg-surface-container-highest text-primary font-technical-tag text-technical-tag font-semibold">${escHtml(a.action)}</a>
              </div>
            </div>`).join('');
        }
      }

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

    // Preserve existing server-side rendered rows
    if (tbody.querySelectorAll("tr").length > 0) return;

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
          <tr class="project-row" data-project-id="${escHtml(p.prj_id)}">
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

    // Preserve existing server-side rendered rows
    if (tbody.querySelectorAll("tr").length > 0) return;

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
   * ORDERS (Inter-Module Data Sharing: Shop SYS02 -> Portal SYS03)
   * Element: #ordersTable tbody
   * ────────────────────────────────────────────── */
  async function loadOrders() {
    const tbody = document.querySelector("#ordersTable tbody");
    if (!tbody) return;

    try {
      const getOrders = customer.getShopOrders
        ? () => customer.getShopOrders()
        : () => window.VostokBus.request("shop", "orders");

      const res = await getOrders();
      const orders = res.data;
      if (!orders || !orders.length) return;

      tbody.innerHTML = orders.map((o) => {
        const ordId = "ORD-" + String(o.order_id).padStart(6, "0");
        const status = o.status || "Processing";
        const statusBadge = {
          Delivered: "bg-surface-container-high/40 text-on-surface",
          Processing: "bg-tertiary-container/30 text-on-tertiary-container",
          Shipped: "bg-primary-container/30 text-on-primary-container",
          Cancelled: "bg-error-container/30 text-on-error-container"
        }[status] || "bg-surface-container-high/40 text-on-surface";

        return `
          <tr class="hover:bg-surface-container-low transition-colors group relative bg-surface-container-lowest"
              data-facility="HQ" data-status="${escHtml(status)}">
            <td class="py-3 px-unit-base relative">
              <div class="flex items-center gap-1.5">
                <span class="font-mono text-body-sm font-bold text-primary">${escHtml(ordId)}</span>
              </div>
            </td>
            <td class="py-3 px-unit-base font-body-sm text-on-surface">${ui.date(o.order_date)}</td>
            <td class="py-3 px-unit-base font-body-sm font-semibold">${escHtml(o.company_name || 'B2B Procurement')}</td>
            <td class="py-3 px-unit-base font-body-sm">${escHtml(String(o.total_items || 1))} item(s)</td>
            <td class="py-3 px-unit-base font-mono font-bold text-primary">${ui.currency(o.total_amount, 'EUR')}</td>
            <td class="py-3 px-unit-base">
              <span class="px-2 py-0.5 rounded text-xs font-semibold ${statusBadge}">${escHtml(status)}</span>
            </td>
            <td class="py-3 px-unit-base text-right">
              <button class="px-2.5 py-1 text-xs rounded border border-outline/30 hover:bg-surface-container-high transition-colors"
                      onclick="window.showLiveTelemetryModal && window.showLiveTelemetryModal('${escHtml(ordId)}')">
                Telemetry
              </button>
            </td>
          </tr>
        `;
      }).join("");

      const recordCount = document.getElementById("recordCount");
      if (recordCount) {
        recordCount.innerText = `${orders.length} orders (live from B2B Shop)`;
      }
    } catch (err) {
      console.warn("[VostokCustomer] Inter-module shop order query:", err);
    }
  }

  /* ──────────────────────────────────────────────
   * Boot
   * ────────────────────────────────────────────── */
  document.addEventListener("DOMContentLoaded", () => {
    loadDashboard();
    loadProjects();
    loadInvoices();
    loadTickets();
    loadOrders();
    initTicketForm();
  });
})();
