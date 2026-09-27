/**
 * VOSTOKPRIBOR IT Helpdesk & Support Operations System (SYS 08)
 * Real-time API Client & CRUD Engine
 */

(function () {
  "use strict";

  const hdApp = {
    // Show Toast Notification
    showToast: function (title, message, type = "orange") {
      const container = document.getElementById("toast-container");
      if (!container) return;

      const toast = document.createElement("div");
      let icon = "⚡";
      if (type === "red" || type === "critical") icon = "🚨";
      if (type === "green" || type === "success") icon = "✓";
      if (type === "amber") icon = "⚠️";

      toast.className = "hd-toast";
      if (type === "red" || type === "critical")
        toast.style.borderLeftColor = "var(--hd-priority-critical)";
      if (type === "green" || type === "success")
        toast.style.borderLeftColor = "var(--hd-success)";
      if (type === "amber") toast.style.borderLeftColor = "var(--hd-amber)";

      toast.innerHTML = `
        <div style="font-size: 16px;">${icon}</div>
        <div style="flex: 1;">
          <div style="font-weight: 700; font-size: 12.5px; color: #FFFFFF;">${title}</div>
          <div style="font-size: 11px; color: rgba(255,255,255,0.8); margin-top: 2px;">${message}</div>
        </div>
        <button style="color: rgba(255,255,255,0.5); font-size: 14px; background: none; border: none; cursor: pointer;" onclick="this.parentElement.remove()">✕</button>
      `;

      container.appendChild(toast);

      setTimeout(() => {
        toast.style.transition = "all 0.3s ease";
        toast.style.opacity = "0";
        toast.style.transform = "translateY(10px)";
        setTimeout(() => toast.remove(), 300);
      }, 4200);
    },

    // Universal Modal Helpers
    openModal: function (modalId) {
      const el = document.getElementById(modalId);
      if (el) {
        el.classList.add("active");
        document.body.style.overflow = "hidden";
      }
    },

    closeModal: function (modalId) {
      const el = document.getElementById(modalId);
      if (el) {
        el.classList.remove("active");
        document.body.style.overflow = "";
      }
    },

    // =========================================================================
    // TICKET CRUD
    // =========================================================================
    openCreateTicketModal: function () {
      const form = document.getElementById("form-create-ticket");
      if (form) form.reset();
      this.openModal("modal-create-ticket");
    },

    submitCreateTicket: async function (e) {
      if (e) e.preventDefault();
      const form = document.getElementById("form-create-ticket");
      if (!form) return;

      const fd = new FormData(form);
      const payload = Object.fromEntries(fd.entries());
      payload.action = "create";

      try {
        const res = await fetch("api/tickets.php?action=create", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
          const code = data.data?.tkt_id || data.ticket_code || "Incident";
          this.showToast("Ticket Created", `${code} created successfully in database.`, "green");
          this.closeModal("modal-create-ticket");
          setTimeout(() => window.location.reload(), 800);
        } else {
          this.showToast("Creation Failed", data.error || "Could not register ticket.", "critical");
        }
      } catch (err) {
        this.showToast("Network Error", err.message, "critical");
      }
    },

    openEditTicketModal: function (ticket) {
      if (!ticket) return;
      const tid = document.getElementById("edit-ticket-id");
      const title = document.getElementById("edit-ticket-title");
      const prio = document.getElementById("edit-ticket-priority");
      const stat = document.getElementById("edit-ticket-status");
      const sys = document.getElementById("edit-ticket-system");
      const ass = document.getElementById("edit-ticket-assigned");
      const desc = document.getElementById("edit-ticket-desc");
      const notes = document.getElementById("edit-ticket-notes");

      if (tid) tid.value = ticket.tkt_id || ticket.ticket_id || ticket.id || "";
      if (title) title.value = ticket.title || ticket.source_system || "";
      if (prio) prio.value = ticket.priority || "Medium";
      if (stat) stat.value = ticket.status || "Open";
      if (sys) sys.value = ticket.source_system || ticket.affected_system || "";
      if (ass) ass.value = ticket.assigned_to || ticket.assigned_tech_name || ticket.assigned_emp_id || "";
      if (desc) desc.value = ticket.description || "";
      if (notes) notes.value = ticket.resolution_notes || "";

      this.openModal("modal-edit-ticket");
    },

    submitEditTicket: async function (e) {
      if (e) e.preventDefault();
      const form = document.getElementById("form-edit-ticket");
      if (!form) return;

      const fd = new FormData(form);
      const payload = Object.fromEntries(fd.entries());
      payload.action = "update";
      if (!payload.tkt_id && payload.ticket_id) payload.tkt_id = payload.ticket_id;
      if (!payload.ticket_id && payload.tkt_id) payload.ticket_id = payload.tkt_id;

      try {
        const res = await fetch("api/tickets.php?action=update", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
          this.showToast("Ticket Updated", "Incident details saved to database.", "green");
          this.closeModal("modal-edit-ticket");
          setTimeout(() => window.location.reload(), 700);
        } else {
          this.showToast("Update Error", data.error || "Could not save incident changes.", "critical");
        }
      } catch (err) {
        this.showToast("Network Error", err.message, "critical");
      }
    },

    deleteTicket: async function (ticketId, code) {
      if (!confirm(`Are you sure you want to permanently delete incident ${code || '#' + ticketId} from the database?`)) {
        return;
      }

      try {
        const res = await fetch("api/tickets.php?action=delete", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ ticket_id: ticketId })
        });
        const data = await res.json();
        if (data.success) {
          this.showToast("Ticket Deleted", `Incident ${code} removed from database.`, "green");
          setTimeout(() => window.location.reload(), 700);
        } else {
          this.showToast("Deletion Failed", data.error || "Could not delete ticket.", "critical");
        }
      } catch (err) {
        this.showToast("Network Error", err.message, "critical");
      }
    },

    resolveTicket: async function (ticketIdOrCode, resolutionNotes) {
      try {
        const res = await fetch("api/tickets.php?action=resolve", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            ticket_id: ticketIdOrCode,
            resolution_notes: resolutionNotes
          })
        });
        const data = await res.json();
        if (data.success) {
          this.showToast("Incident Resolved", "Ticket marked Resolved and SLA clock stopped.", "green");
          const pill = document.getElementById("ticket-detail-status-pill");
          if (pill) {
            pill.className = "status-pill status-resolved";
            pill.textContent = "Resolved";
          }
          setTimeout(() => window.location.reload(), 900);
        } else {
          this.showToast("Resolution Error", data.error || "Could not mark resolved.", "critical");
        }
      } catch (err) {
        this.showToast("Network Error", err.message, "critical");
      }
    },

    escalateTicket: async function (ticketIdOrCode) {
      try {
        const res = await fetch("api/tickets.php?action=escalate", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            ticket_id: ticketIdOrCode,
            escalation_tier: "Tier 3 Operations & Industrial Security Lead",
            reason: "Telemetry instability exceeding standard L2 remediation horizon"
          })
        });
        const data = await res.json();
        if (data.success) {
          this.showToast("Incident Escalated", "High-priority governance audit and tier-3 lead summoned.", "critical");
          const pill = document.getElementById("ticket-detail-status-pill");
          if (pill) {
            pill.className = "status-pill status-escalated";
            pill.textContent = "Escalated";
          }
          setTimeout(() => window.location.reload(), 900);
        } else {
          this.showToast("Escalation Error", data.error || "Could not escalate incident.", "critical");
        }
      } catch (err) {
        this.showToast("Network Error", err.message, "critical");
      }
    },

    bulkAssign: async function () {
      try {
        const res = await fetch("api/tickets.php?action=bulk_assign", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            assigned_to: "Alexey Ivanov",
            assigned_emp_id: "EMP-1018"
          })
        });
        const data = await res.json();
        if (data.success) {
          this.showToast("Bulk Triage Done", `${data.affected_rows} unassigned tickets dispatched to active engineer.`, "green");
          setTimeout(() => window.location.reload(), 900);
        } else {
          this.showToast("Triage Error", data.error || "Could not bulk assign.", "critical");
        }
      } catch (err) {
        this.showToast("Network Error", err.message, "critical");
      }
    },

    // =========================================================================
    // COMMENT THREAD IN TICKET DETAIL
    // =========================================================================
    sendMessage: async function () {
      const input = document.getElementById("chat-reply-input");
      const btn = document.getElementById("btn-send-message");
      const thread = document.getElementById("chat-conversation-thread");
      if (!input || !input.value.trim()) return;

      const text = input.value.trim();
      const ticketId = btn ? btn.getAttribute("data-tid") : "";

      try {
        const res = await fetch("api/comments.php?action=create", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            ticket_id: ticketId,
            comment_text: text,
            author_type: "tech"
          })
        });
        const data = await res.json();
        if (data.success) {
          const msgRow = document.createElement("div");
          msgRow.className = "chat-msg-row tech-msg";
          msgRow.innerHTML = `
            <div class="hd-stat-column-box" style="width: 36px; height: 36px; border-radius: 50%; background: #C97A3D; color: #fff; font-size: 13px; font-weight: bold;">
              AI
            </div>
            <div class="chat-bubble">
              <div class="chat-msg-header">
                <strong>Alexey Ivanov (Tier 3 IT Tech)</strong>
                <span>Just now</span>
              </div>
              <p>${text.replace(/\n/g, '<br>')}</p>
            </div>
          `;
          if (thread) {
            thread.appendChild(msgRow);
            thread.scrollTop = thread.scrollHeight;
          }
          input.value = "";
          this.showToast("Message Dispatched", "Remediation note committed to database and logged in audit trail.", "green");
        } else {
          this.showToast("Send Failed", data.error || "Could not transmit note.", "critical");
        }
      } catch (err) {
        this.showToast("Network Error", err.message, "critical");
      }
    },

    // =========================================================================
    // ASSET CRUD
    // =========================================================================
    openCreateAssetModal: function () {
      const form = document.getElementById("form-create-asset");
      if (form) form.reset();
      this.openModal("modal-create-asset");
    },

    submitCreateAsset: async function (e) {
      if (e) e.preventDefault();
      const form = document.getElementById("form-create-asset");
      if (!form) return;

      const fd = new FormData(form);
      const payload = Object.fromEntries(fd.entries());
      payload.action = "create";

      try {
        const res = await fetch("api/assets.php?action=create", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
          const tag = (data.data && data.data.asset_tag) || data.asset_tag || payload.asset_tag || "New Device";
          this.showToast("Asset Registered", `Device ${tag} added to database.`, "green");
          this.closeModal("modal-create-asset");
          setTimeout(() => window.location.reload(), 700);
        } else {
          this.showToast("Registration Failed", data.error || "Could not register asset.", "critical");
        }
      } catch (err) {
        this.showToast("Network Error", err.message, "critical");
      }
    },

    openInspectModal: function (asset) {
      if (!asset) return;
      this._inspectAsset = asset;
      const f = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.textContent = val || "—";
      };
      f("ins-tag", asset.asset_tag || ("VP-NODE-" + asset.asset_id));
      f("ins-model", asset.device_model || asset.asset_type || "Industrial Edge Node");
      f("ins-type", asset.asset_type || "Hardware Node");
      f("ins-loc", asset.location || "Central Datacenter Bay");
      f("ins-ip", asset.ip_address || "DHCP");
      f("ins-mac", asset.mac_address || "N/A");
      f("ins-fw", asset.firmware_version || "N/A");
      f("ins-os", ((asset.operating_system || "") + (asset.os_version ? " " + asset.os_version : "")) || "Embedded Linux");
      f("ins-serial", asset.serial_number || "N/A");
      f("ins-host", asset.hostname || "N/A");
      f("ins-crit", asset.criticality || "High");
      f("ins-env", asset.environment || "Production");
      f("ins-health", asset.health_status || "Online (Active)");
      f("ins-status", asset.status || "Active");
      f("ins-date", asset.assigned_date || "2026-01-01");
      f("ins-seen", asset.last_seen_at || "Just now");
      f("ins-notes", asset.notes || "No specialized subsystem notes recorded for this edge hardware.");
      this.openModal("modal-inspect-asset");
    },

    openEditAssetModal: function (asset) {
      if (!asset) return;
      const aid = document.getElementById("edit-asset-id");
      const tag = document.getElementById("edit-asset-tag");
      const model = document.getElementById("edit-asset-model");
      const type = document.getElementById("edit-asset-type");
      const loc = document.getElementById("edit-asset-loc");
      const ip = document.getElementById("edit-asset-ip");
      const mac = document.getElementById("edit-asset-mac");
      const fw = document.getElementById("edit-asset-fw");
      const health = document.getElementById("edit-asset-health");
      const notes = document.getElementById("edit-asset-notes");

      if (aid) aid.value = asset.asset_id || "";
      if (tag) tag.value = asset.asset_tag || "";
      if (model) model.value = asset.device_model || "";
      if (type) type.value = asset.asset_type || "";
      if (loc) loc.value = asset.location || "";
      if (ip) ip.value = asset.ip_address || "";
      if (mac) mac.value = asset.mac_address || "";
      if (fw) fw.value = asset.firmware_version || "";
      if (notes) notes.value = asset.notes || "";

      if (health) {
        const dbHealth = (asset.health_status || "Online (Active)").trim();
        let matched = false;
        for (let i = 0; i < health.options.length; i++) {
          if (health.options[i].value.toLowerCase() === dbHealth.toLowerCase()) {
            health.selectedIndex = i;
            matched = true;
            break;
          }
        }
        if (!matched) {
          const opt = document.createElement("option");
          opt.value = dbHealth;
          opt.text = dbHealth;
          opt.selected = true;
          health.appendChild(opt);
        }
      }

      this.openModal("modal-edit-asset");
    },

    submitEditAsset: async function (e) {
      if (e) e.preventDefault();
      const form = document.getElementById("form-edit-asset");
      if (!form) return;

      const fd = new FormData(form);
      const payload = Object.fromEntries(fd.entries());
      payload.action = "update";

      try {
        const res = await fetch("api/assets.php?action=update", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
          this.showToast("Asset Updated", "Device configuration saved to database.", "green");
          this.closeModal("modal-edit-asset");
          setTimeout(() => window.location.reload(), 700);
        } else {
          this.showToast("Update Failed", data.error || "Could not update asset.", "critical");
        }
      } catch (err) {
        this.showToast("Network Error", err.message, "critical");
      }
    },

    deleteAsset: async function (assetId, tag) {
      if (!confirm(`Are you sure you want to delete hardware asset ${tag || '#' + assetId}?`)) {
        return;
      }

      try {
        const res = await fetch("api/assets.php?action=delete", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ action: "delete", asset_id: assetId })
        });
        const data = await res.json();
        if (data.success) {
          this.showToast("Asset Deleted", `Device ${tag} removed from database.`, "green");
          setTimeout(() => window.location.reload(), 700);
        } else {
          this.showToast("Deletion Failed", data.error || "Could not delete device.", "critical");
        }
      } catch (err) {
        this.showToast("Network Error", err.message, "critical");
      }
    },

    // =========================================================================
    // KNOWLEDGE BASE CRUD
    // =========================================================================
    openCreateKbModal: function () {
      const form = document.getElementById("form-create-kb");
      if (form) form.reset();
      this.openModal("modal-create-kb");
    },

    submitCreateKb: async function (e) {
      if (e) e.preventDefault();
      const form = document.getElementById("form-create-kb");
      if (!form) return;

      const fd = new FormData(form);
      const payload = Object.fromEntries(fd.entries());
      payload.action = "create";

      try {
        const res = await fetch("api/knowledge.php?action=create", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
          const code = (data.data && data.data.article_code) || data.article_code || payload.article_code || "New Article";
          this.showToast("Article Published", `SOP ${code} saved to database.`, "green");
          this.closeModal("modal-create-kb");
          setTimeout(() => window.location.reload(), 700);
        } else {
          this.showToast("Publish Failed", data.error || "Could not publish article.", "critical");
        }
      } catch (err) {
        this.showToast("Network Error", err.message, "critical");
      }
    },

    openEditKbModal: function (art) {
      if (!art) return;
      const kid = document.getElementById("edit-kb-id");
      const code = document.getElementById("edit-kb-code");
      const cat = document.getElementById("edit-kb-cat");
      const title = document.getElementById("edit-kb-title");
      const summary = document.getElementById("edit-kb-summary");
      const content = document.getElementById("edit-kb-content");
      const tags = document.getElementById("edit-kb-tags");

      if (kid) kid.value = art.kb_id || art.id || "";
      if (code) code.value = art.article_code || "";
      if (cat) cat.value = art.category || "";
      if (title) title.value = art.title || "";
      if (summary) summary.value = art.summary || "";
      if (content) content.value = art.content || "";
      if (tags) tags.value = art.tags || "";

      this.openModal("modal-edit-kb");
    },

    submitEditKb: async function (e) {
      if (e) e.preventDefault();
      const form = document.getElementById("form-edit-kb");
      if (!form) return;

      const fd = new FormData(form);
      const payload = Object.fromEntries(fd.entries());
      payload.action = "update";

      try {
        const res = await fetch("api/knowledge.php?action=update", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
          this.showToast("Article Saved", "SOP updates saved to database.", "green");
          this.closeModal("modal-edit-kb");
          setTimeout(() => window.location.reload(), 700);
        } else {
          this.showToast("Update Failed", data.error || "Could not save article.", "critical");
        }
      } catch (err) {
        this.showToast("Network Error", err.message, "critical");
      }
    },

    deleteKbArticle: async function (articleId, code) {
      if (!confirm(`Are you sure you want to delete SOP runbook ${code || '#' + articleId}?`)) {
        return;
      }

      try {
        const res = await fetch("api/knowledge.php?action=delete", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ action: "delete", article_id: articleId, kb_id: articleId })
        });
        const data = await res.json();
        if (data.success) {
          this.showToast("Article Removed", `Runbook ${code} deleted from database.`, "green");
          setTimeout(() => window.location.reload(), 700);
        } else {
          this.showToast("Deletion Failed", data.error || "Could not delete article.", "critical");
        }
      } catch (err) {
        this.showToast("Network Error", err.message, "critical");
      }
    },

    viewKbArticle: function (art) {
      if (!art) return;
      const codeEl = document.getElementById("view-kb-code");
      const titleEl = document.getElementById("view-kb-title");
      const summaryEl = document.getElementById("view-kb-summary");
      const contentEl = document.getElementById("view-kb-content");

      const code = art.article_code || ('KB-' + (art.kb_id || ''));
      const cat = (art.category || 'GENERAL').toUpperCase();
      if (codeEl) codeEl.textContent = `${code} · ${cat}`;
      if (titleEl) titleEl.textContent = art.title || "Procedure Runbook";
      if (summaryEl) summaryEl.textContent = art.summary || "No executive summary provided.";
      if (contentEl) contentEl.textContent = art.content || "Procedure content pending.";

      this.openModal("modal-view-kb");
    },

    // =========================================================================
    // SLA POLICIES CRUD
    // =========================================================================
    openEditSlaModal: function (pol) {
      if (!pol) return;
      const pid = document.getElementById("edit-sla-id");
      const prio = document.getElementById("edit-sla-prio");
      const resp = document.getElementById("edit-sla-resp");
      const resol = document.getElementById("edit-sla-resol");
      const escl = document.getElementById("edit-sla-escl");
      const desc = document.getElementById("edit-sla-desc");

      const respMinutes = pol.first_response_time_minutes || (pol.response_time_hours ? pol.response_time_hours * 60 : 60);
      const resolMinutes = pol.resolution_time_minutes || (pol.resolution_time_hours ? pol.resolution_time_hours * 60 : 120);
      const esclMinutes = pol.escalation_threshold_minutes || Math.round(respMinutes * 0.5);

      if (pid) pid.value = pol.sla_id || pol.policy_id || "";
      if (prio) prio.value = pol.priority || pol.priority_level || "";
      if (resp) resp.value = respMinutes;
      if (resol) resol.value = resolMinutes;
      if (escl) escl.value = esclMinutes;
      if (desc) desc.value = pol.description || "";

      this.openModal("modal-edit-sla");
    },

    submitEditSla: async function (e) {
      if (e) e.preventDefault();
      const form = document.getElementById("form-edit-sla");
      if (!form) return;

      const fd = new FormData(form);
      const payload = Object.fromEntries(fd.entries());
      payload.action = "update_policy";

      try {
        const res = await fetch("api/sla.php?action=update_policy", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
          this.showToast("SLA Policy Updated", "New operational response targets saved to database.", "green");
          this.closeModal("modal-edit-sla");
          setTimeout(() => window.location.reload(), 700);
        } else {
          this.showToast("Update Failed", data.error || "Could not save policy.", "critical");
        }
      } catch (err) {
        this.showToast("Network Error", err.message, "critical");
      }
    },

    // =========================================================================
    // SEARCH & TABLE FILTERS
    // =========================================================================
    filterTickets: function () {
      const search = document.getElementById("ticket-search");
      const priority = document.getElementById("filter-priority");
      const system = document.getElementById("filter-system");
      const status = document.getElementById("filter-status");
      const tech = document.getElementById("filter-tech");

      const q = (search ? search.value : "").toLowerCase();
      const p = priority ? priority.value : "all";
      const s = system ? system.value : "all";
      const st = status ? status.value : "all";
      const t = tech ? tech.value : "all";

      const rows = document.querySelectorAll(".ticket-table-body tr");
      rows.forEach((row) => {
        const rowId = (row.getAttribute("data-id") || "").toLowerCase();
        const rowReq = (row.getAttribute("data-requester") || "").toLowerCase();
        const rowSys = (row.getAttribute("data-system") || "").toLowerCase();
        const rowPrio = row.getAttribute("data-priority") || "";
        const rowStat = row.getAttribute("data-status") || "";
        const rowTech = row.getAttribute("data-tech") || "";

        const matchSearch =
          !q ||
          rowId.includes(q) ||
          rowReq.includes(q) ||
          rowSys.includes(q);
        const matchPrio = p === "all" || rowPrio.toLowerCase() === p.toLowerCase();
        const matchSys = s === "all" || rowSys.includes(s.toLowerCase());
        const matchStat = st === "all" || rowStat.toLowerCase() === st.toLowerCase();
        const matchTech = t === "all" || rowTech.toLowerCase().includes(t.toLowerCase());

        if (matchSearch && matchPrio && matchSys && matchStat && matchTech) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    },

    // SLA Countdown Ticker
    startSLATimer: function () {
      const timerEl = document.getElementById("live-sla-timer");
      if (!timerEl) return;

      let totalSeconds = 1 * 3600 + 42 * 60 + 15;

      setInterval(() => {
        if (totalSeconds > 0) {
          totalSeconds--;
          const hrs = String(Math.floor(totalSeconds / 3600)).padStart(2, "0");
          const mins = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, "0");
          const secs = String(totalSeconds % 60).padStart(2, "0");
          timerEl.textContent = `${hrs}:${mins}:${secs}`;
        }
      }, 1000);
    },

    // App Initialization
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

      // Global Omni Search
      const omniSearch = document.getElementById("global-omni-search");
      if (omniSearch) {
        omniSearch.addEventListener("keydown", (e) => {
          if (e.key === "Enter") {
            const val = omniSearch.value.trim();
            if (val) {
              window.location.href = `TicketQueue.php?q=${encodeURIComponent(val)}`;
            }
          }
        });
      }

      // Close modal when clicking backdrop
      document.querySelectorAll(".hd-modal-overlay").forEach((modal) => {
        modal.addEventListener("click", (e) => {
          if (e.target === modal) {
            modal.classList.remove("active");
            document.body.style.overflow = "";
          }
        });
      });

      // TicketDetail page event bindings
      const btnSend = document.getElementById("btn-send-message");
      if (btnSend) {
        btnSend.addEventListener("click", () => this.sendMessage());
      }

      const replyInput = document.getElementById("chat-reply-input");
      if (replyInput) {
        replyInput.addEventListener("keydown", (e) => {
          if (e.key === "Enter" && (e.ctrlKey || e.metaKey)) {
            e.preventDefault();
            this.sendMessage();
          }
        });
      }

      const btnResolve = document.getElementById("btn-resolve-ticket");
      if (btnResolve) {
        btnResolve.addEventListener("click", () => {
          const tid = btnResolve.getAttribute("data-tid");
          const notesEl = document.getElementById("resolution-notes");
          const notes = notesEl ? notesEl.value : "";
          this.resolveTicket(tid, notes);
        });
      }

      const btnEscalate = document.getElementById("btn-escalate-ticket");
      if (btnEscalate) {
        btnEscalate.addEventListener("click", () => {
          const tid = btnEscalate.getAttribute("data-tid");
          this.escalateTicket(tid);
        });
      }

      // TicketDetail page event bindings
      const btnEditCurrent = document.getElementById("btn-edit-current-ticket");
      if (btnEditCurrent) {
        btnEditCurrent.addEventListener("click", () => {
          const m = document.getElementById("editTicketModal");
          if (m) {
            m.classList.add("active");
            m.classList.add("show");
            document.body.style.overflow = "hidden";
          } else {
            this.openModal("modal-edit-ticket");
          }
        });
      }

      const btnCloseEditModal = document.getElementById("btnCloseEditTicketModal");
      if (btnCloseEditModal) {
        btnCloseEditModal.addEventListener("click", () => {
          const m = document.getElementById("editTicketModal");
          if (m) {
            m.classList.remove("active");
            m.classList.remove("show");
            document.body.style.overflow = "";
          }
        });
      }

      const btnCancelEditModal = document.getElementById("btnCancelEditTicketModal");
      if (btnCancelEditModal) {
        btnCancelEditModal.addEventListener("click", () => {
          const m = document.getElementById("editTicketModal");
          if (m) {
            m.classList.remove("active");
            m.classList.remove("show");
            document.body.style.overflow = "";
          }
        });
      }

      const btnSaveEditTicket = document.getElementById("btnSaveEditTicket");
      if (btnSaveEditTicket) {
        btnSaveEditTicket.addEventListener("click", async () => {
          const tid = (document.getElementById("editTktId")?.value || "").trim();
          const title = (document.getElementById("editTktTitle")?.value || "").trim();
          const sys = (document.getElementById("editTktSystem")?.value || "").trim();
          const prio = (document.getElementById("editTktPriority")?.value || "Medium");
          const stat = (document.getElementById("editTktStatus")?.value || "Open");
          const techSelect = document.getElementById("editTktTech");
          const techEmpId = techSelect ? techSelect.value : "";
          const desc = (document.getElementById("editTktDesc")?.value || "").trim();

          if (!title || !sys) {
            this.showToast("Validation Error", "Incident title and affected system are required.", "critical");
            return;
          }

          const payload = {
            action: "update",
            ticket_id: tid,
            tkt_id: tid,
            title: title,
            source_system: sys,
            affected_system: sys,
            priority: prio,
            status: stat,
            assigned_emp_id: techEmpId,
            description: desc
          };

          try {
            const res = await fetch("api/tickets.php?action=update", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
              this.showToast("Success", `Ticket [${tid}] updated.`, "green");
              const m = document.getElementById("editTicketModal");
              if (m) {
                m.classList.remove("active");
                m.classList.remove("show");
                document.body.style.overflow = "";
              }
              setTimeout(() => window.location.reload(), 700);
            } else {
              this.showToast("Update Failed", data.error || "Could not update ticket.", "critical");
            }
          } catch (err) {
            this.showToast("Network Error", err.message, "critical");
          }
        });
      }

      const btnBulkAssign = document.getElementById("btn-bulk-assign");
      if (btnBulkAssign) {
        btnBulkAssign.addEventListener("click", () => this.bulkAssign());
      }

      // TicketQueue.php specific element handlers
      const btnOpenQueueCreate = document.getElementById("btn-open-create-ticket");
      if (btnOpenQueueCreate) {
        btnOpenQueueCreate.addEventListener("click", (e) => {
          e.preventDefault();
          const modal = document.getElementById("ticketModal");
          if (modal) {
            const act = document.getElementById("modalTktAction");
            const mid = document.getElementById("modalTktId");
            const title = document.getElementById("modalTktTitle");
            const sys = document.getElementById("modalTktSystem");
            const desc = document.getElementById("modalTktDesc");
            const prio = document.getElementById("modalTktPriority");
            const stat = document.getElementById("modalTktStatus");
            const tech = document.getElementById("modalTktTech");
            const reqName = document.getElementById("modalTktReqName");
            const reqRole = document.getElementById("modalTktReqRole");
            const mTitle = document.getElementById("ticketModalTitle");

            if (mTitle) {
              mTitle.innerHTML = `<span class="material-symbols-outlined">confirmation_number</span> Create New Support Incident`;
            }
            if (act) act.value = "create";
            if (mid) mid.value = "";
            if (title) title.value = "";
            if (sys) sys.value = "";
            if (desc) desc.value = "";
            if (prio) prio.value = "Medium";
            if (stat) stat.value = "Open";
            if (tech) tech.value = "EMP-1018";
            if (reqName) reqName.value = "Authorized Personnel";
            if (reqRole) reqRole.value = "Operations Staff";

            modal.classList.add("active");
            modal.classList.add("show");
            document.body.style.overflow = "hidden";
          } else {
            this.openCreateTicketModal();
          }
        });
      }

      const btnCloseTicketModal = document.getElementById("btnCloseTicketModal");
      if (btnCloseTicketModal) {
        btnCloseTicketModal.addEventListener("click", () => {
          const modal = document.getElementById("ticketModal");
          if (modal) {
            modal.classList.remove("active");
            modal.classList.remove("show");
            document.body.style.overflow = "";
          }
        });
      }

      const btnCancelTicketModal = document.getElementById("btnCancelTicketModal");
      if (btnCancelTicketModal) {
        btnCancelTicketModal.addEventListener("click", () => {
          const modal = document.getElementById("ticketModal");
          if (modal) {
            modal.classList.remove("active");
            modal.classList.remove("show");
            document.body.style.overflow = "";
          }
        });
      }

      // Save ticket from TicketQueue.php modal
      const btnSaveTicket = document.getElementById("btnSaveTicket");
      if (btnSaveTicket) {
        btnSaveTicket.addEventListener("click", async () => {
          const action = (document.getElementById("modalTktAction")?.value || "create");
          const ticketId = (document.getElementById("modalTktId")?.value || "").trim();
          const title = (document.getElementById("modalTktTitle")?.value || "").trim();
          const sys = (document.getElementById("modalTktSystem")?.value || "").trim();
          const prio = (document.getElementById("modalTktPriority")?.value || "Medium");
          const stat = (document.getElementById("modalTktStatus")?.value || "Open");
          const techSelect = document.getElementById("modalTktTech");
          const techEmpId = techSelect ? techSelect.value : "";
          const techName = techSelect && techSelect.selectedIndex >= 0 && techSelect.value ? techSelect.options[techSelect.selectedIndex].text.split("(")[0].trim() : "Unassigned";
          const reqName = (document.getElementById("modalTktReqName")?.value || "Authorized Personnel").trim();
          const reqRole = (document.getElementById("modalTktReqRole")?.value || "Operations Staff").trim();
          const desc = (document.getElementById("modalTktDesc")?.value || "").trim();

          if (!title || !sys) {
            this.showToast("Validation Error", "Please provide incident title and affected system.", "critical");
            return;
          }

          const payload = {
            action: action === "edit" ? "update" : "create",
            ticket_id: ticketId,
            tkt_id: ticketId,
            title: title,
            affected_system: sys,
            source_system: sys,
            system: sys,
            priority: prio,
            status: stat,
            assigned_to: techName,
            assigned_emp_id: techEmpId,
            requester_name: reqName,
            requester_role: reqRole,
            description: desc
          };

          const endpoint = action === "edit" ? "api/tickets.php?action=update" : "api/tickets.php?action=create";
          try {
            const res = await fetch(endpoint, {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
              this.showToast("Success", action === "edit" ? `Incident ${ticketId} updated successfully.` : "Incident saved to database.", "green");
              const modal = document.getElementById("ticketModal");
              if (modal) {
                modal.classList.remove("active");
                modal.classList.remove("show");
                document.body.style.overflow = "";
              }
              setTimeout(() => window.location.reload(), 700);
            } else {
              this.showToast("Save Failed", data.error || "Could not save incident.", "critical");
            }
          } catch (err) {
            this.showToast("Network Error", err.message, "critical");
          }
        });
      }

      // Edit and Delete buttons on TicketQueue.php (event delegation for 100% reliability)
      document.addEventListener("click", (e) => {
        const editBtn = e.target.closest(".btn-edit-ticket");
        if (editBtn) {
          e.preventDefault();
          e.stopPropagation();
          const tData = editBtn.getAttribute("data-ticket");
          if (!tData) return;
          try {
            const t = JSON.parse(tData);
            const modal = document.getElementById("ticketModal");
            if (modal) {
              const tid = t.tkt_id || t.ticket_id || t.id || "";
              document.getElementById("modalTktAction").value = "edit";
              document.getElementById("modalTktId").value = tid;
              document.getElementById("modalTktTitle").value = t.title || t.source_system || "";
              document.getElementById("modalTktSystem").value = t.source_system || t.affected_system || "";
              document.getElementById("modalTktPriority").value = t.priority || "Medium";
              document.getElementById("modalTktStatus").value = t.status || "Open";
              document.getElementById("modalTktReqName").value = t.requester_name || "Authorized Personnel";
              document.getElementById("modalTktReqRole").value = t.requester_role || "Operations Staff";
              document.getElementById("modalTktDesc").value = t.description || "";

              const techSelect = document.getElementById("modalTktTech");
              if (techSelect) {
                if (t.assigned_emp_id) {
                  techSelect.value = t.assigned_emp_id;
                } else {
                  techSelect.value = "";
                }
              }

              const mTitle = document.getElementById("ticketModalTitle");
              if (mTitle) {
                mTitle.innerHTML = `<span class="material-symbols-outlined">edit_document</span> Edit Support Incident: ${tid}`;
              }

              modal.classList.add("active");
              modal.classList.add("show");
              document.body.style.overflow = "hidden";
            } else {
              this.openEditTicketModal(t);
            }
          } catch (err) {
            console.error("Error opening edit ticket modal:", err);
          }
        }

        const delBtn = e.target.closest(".btn-delete-ticket");
        if (delBtn) {
          e.preventDefault();
          e.stopPropagation();
          const tid = delBtn.getAttribute("data-id");
          if (tid) this.deleteTicket(tid, tid);
        }
      });

      // Queue search and filters
      const ticketSearch = document.getElementById("ticket-search");
      if (ticketSearch) {
        ticketSearch.addEventListener("input", () => this.filterTickets());
      }
      ["filter-priority", "filter-system", "filter-status", "filter-tech"].forEach((id) => {
        const sel = document.getElementById(id);
        if (sel) sel.addEventListener("change", () => this.filterTickets());
      });

      const btnSyncQueue = document.getElementById("btn-sync-queue");
      if (btnSyncQueue) {
        btnSyncQueue.addEventListener("click", () => {
          this.showToast("Queue Synced", "Refreshing live telemetry from database...", "green");
          setTimeout(() => window.location.reload(), 600);
        });
      }

      this.startSLATimer();
      this.initResponsiveLayout();
    },

    initResponsiveLayout: function () {
      const brandSection =
        document.querySelector(".brand-section") ||
        document.querySelector(".top-nav__content");
      let toggleBtn = document.getElementById("hd-sidebar-toggle");
      if (!toggleBtn && brandSection) {
        toggleBtn = document.createElement("button");
        toggleBtn.id = "hd-sidebar-toggle";
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
          toggleBtn.innerHTML = document.body.classList.contains("sidebar-open") ? "✕" : "☰";
        });
      }

      backdrop.addEventListener("click", () => {
        document.body.classList.remove("sidebar-open");
        if (toggleBtn) toggleBtn.innerHTML = "☰";
      });

      document.querySelectorAll(".sidebar-nav-item, .sidebar a").forEach((link) => {
        link.addEventListener("click", () => {
          if (window.innerWidth <= 1024) {
            document.body.classList.remove("sidebar-open");
            if (toggleBtn) toggleBtn.innerHTML = "☰";
          }
        });
      });

      document.querySelectorAll("table.hd-table, table.data-table, table").forEach((table) => {
        if (
          !table.parentElement.classList.contains("table-responsive") &&
          !table.parentElement.classList.contains("hd-table-container")
        ) {
          const wrapper = document.createElement("div");
          wrapper.className = "table-responsive";
          table.parentNode.insertBefore(wrapper, table);
          wrapper.appendChild(table);
        }
      });

      window.addEventListener("resize", () => {
        if (window.innerWidth > 1024 && document.body.classList.contains("sidebar-open")) {
          document.body.classList.remove("sidebar-open");
          if (toggleBtn) toggleBtn.innerHTML = "☰";
        }
      });
    },
  };

  window.hdApp = hdApp;

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => hdApp.init());
  } else {
    hdApp.init();
  }
})();
