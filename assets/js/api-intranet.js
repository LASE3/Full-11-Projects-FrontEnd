/**
 * VOSTOKPRIBOR Employee Intranet — Isolated API Client (SYS 04)
 * Scope: Strictly restricted to Employee Intranet operations
 * (Company Announcements, Personnel Directory, Leave Management & Approvals).
 *
 * Includes Inter-Module Communication hooks for querying assigned support tickets
 * (SYS03), CRM accounts (SYS05), and sales quotes (SYS02) via VostokBus.
 *
 * Exposes:
 *   - window.VostokIntranet
 *   - Registered as 'intranet' on window.VostokBus
 */

(function (global) {
  "use strict";

  if (!global.VostokCore) {
    console.error("[VostokIntranet] Error: assets/js/api-core.js must be loaded before api-intranet.js.");
  }

  const core = global.VostokCore || {
    call: async () => { throw new Error("VostokCore not loaded"); },
    bus: { request: async () => {}, emit: () => {}, register: () => {} },
    ui: { toast: () => {} }
  };
  const { call, bus } = core;

  /* ──────────────────────────────────────────────────────────────────────────
   * Isolated Employee Intranet API Methods
   * ────────────────────────────────────────────────────────────────────────── */
  const intranetApi = {
    MODULE_NAME: "intranet",
    SYSTEM_CODE: "SYS04",

    /* ── Internal Announcements & Bulletins ── */
    announcements(params = {}) {
      return call("/intranet/announcements.php", { params });
    },

    createAnnouncement(payload) {
      return call("/intranet/announcements.php", {
        method: "POST",
        body: payload,
      }).then((res) => {
        bus.emit("intranet:announcementPosted", res.data);
        return res;
      });
    },

    /* ── Enterprise Staff Directory ── */
    directory(params = {}) {
      return call("/intranet/directory.php", { params });
    },

    employee(empId, params = {}) {
      return call("/intranet/directory.php", { params: { ...params, query: empId } });
    },

    /* ── Leave Requests & Approvals ── */
    leaves(params = {}) {
      return call("/intranet/leaves.php", { params });
    },

    submitLeave(payload) {
      return call("/intranet/leaves.php", {
        method: "POST",
        body: payload,
      }).then((res) => {
        bus.emit("intranet:leaveSubmitted", res.data);
        return res;
      });
    },

    approveLeave(leaveId, decision) {
      return call("/intranet/leaves.php", {
        method: "PATCH",
        body: { leave_id: leaveId, decision },
      }).then((res) => {
        bus.emit("intranet:leaveDecided", { leaveId, decision, result: res.data });
        return res;
      });
    },

    /* ────────────────────────────────────────────────────────────────────────
     * Inter-Module Data Sharing (Cross-System Communication)
     * ──────────────────────────────────────────────────────────────────────── */

    /**
     * Cross-system query: Request support tickets assigned to employee from Customer Portal (SYS03)
     * @param {string} empId
     */
    async requestAssignedTickets(empId) {
      console.info(`[VostokIntranet] Inter-Module RPC -> Customer Portal (SYS03): Fetching tickets assigned to ${empId}`);
      return await bus.request("customer", "tickets", { assigned_emp_id: empId });
    },

    /**
     * Cross-system query: Request customer accounts managed by this employee from CRM (SYS05)
     * @param {string} empId
     */
    async requestManagedAccounts(empId) {
      console.info(`[VostokIntranet] Inter-Module RPC -> CRM (SYS05): Fetching managed accounts for ${empId}`);
      return await bus.request("crm", "customers", { manager_emp_id: empId });
    },

    /**
     * Cross-system query: Request B2B quotes handled by this sales employee from Online Shop (SYS02)
     * @param {string} empId
     */
    async requestManagedQuotes(empId) {
      console.info(`[VostokIntranet] Inter-Module RPC -> Online Shop (SYS02): Fetching active quotes for ${empId}`);
      return await bus.request("shop", "quotes", { emp_id: empId });
    },
  };

  // Register with Inter-Module Bus
  if (global.VostokBus) {
    global.VostokBus.register("intranet", intranetApi);
  }

  // Export isolated namespace
  global.VostokIntranet = intranetApi;
  console.info("[VostokIntranet] Isolated Employee Intranet API module ready.");
})(window);
