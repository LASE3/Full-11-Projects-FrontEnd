/**
 * VOSTOKPRIBOR Employee Intranet — Module-Specific API Client (SYS 04)
 * Location: Employee Intranet/js/intranet-api.js
 * Mirrors assets/js/api-intranet.js for subsystem-isolated deployment.
 */
(function (global) {
  "use strict";

  if (!global.VostokCore) {
    console.error("[VostokIntranet] Error: assets/js/api-core.js must be loaded before intranet-api.js.");
  }

  const core = global.VostokCore || {
    call: async () => { throw new Error("VostokCore not loaded"); },
    bus: { request: async () => {}, emit: () => {}, register: () => {} },
    ui: { toast: () => {} }
  };
  const { call, bus } = core;

  const intranetApi = {
    MODULE_NAME: "intranet",
    SYSTEM_CODE: "SYS04",

    /* ── Announcements ── */
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

    /* ── Directory ── */
    directory(params = {}) {
      return call("/intranet/directory.php", { params });
    },
    employee(empId, params = {}) {
      return call("/intranet/directory.php", { params: { ...params, query: empId } });
    },

    /* ── Leaves ── */
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

    /* ── Inter-Module Communication ── */
    async requestAssignedTickets(empId) {
      console.info(`[VostokIntranet] Inter-Module RPC -> Customer Portal (SYS03): Tickets for ${empId}`);
      return await bus.request("customer", "tickets", { assigned_emp_id: empId });
    },
    async requestManagedAccounts(empId) {
      console.info(`[VostokIntranet] Inter-Module RPC -> CRM (SYS05): Managed accounts for ${empId}`);
      return await bus.request("crm", "customers", { manager_emp_id: empId });
    },
    async requestManagedQuotes(empId) {
      console.info(`[VostokIntranet] Inter-Module RPC -> Online Shop (SYS02): Quotes for ${empId}`);
      return await bus.request("shop", "quotes", { emp_id: empId });
    },
  };

  if (global.VostokBus) {
    global.VostokBus.register("intranet", intranetApi);
  }
  global.VostokIntranet = intranetApi;
})(window);
