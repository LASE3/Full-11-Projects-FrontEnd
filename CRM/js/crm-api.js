/**
 * VOSTOKPRIBOR CRM Platform — Module-Specific API Client (SYS 05)
 * Location: CRM/js/crm-api.js
 * Mirrors assets/js/api-crm.js for subsystem-isolated deployment.
 */
(function (global) {
  "use strict";

  if (!global.VostokCore) {
    console.error("[VostokCRM] Error: assets/js/api-core.js must be loaded before crm-api.js.");
  }

  const core = global.VostokCore || {
    call: async () => { throw new Error("VostokCore not loaded"); },
    bus: { request: async () => {}, emit: () => {}, register: () => {} },
    ui: { toast: () => {} }
  };
  const { call, bus } = core;

  const crmApi = {
    MODULE_NAME: "crm",
    SYSTEM_CODE: "SYS05",

    /* ── Customers 360 ── */
    customers(params = {}) {
      return call("/crm/customers.php", { params });
    },
    customer(id, params = {}) {
      return call("/crm/customers.php", { params: { ...params, id } });
    },
    createCustomer(payload) {
      return call("/crm/customers.php", { method: "POST", body: payload }).then((res) => {
        bus.emit("crm:customerCreated", res.data);
        return res;
      });
    },
    updateCustomer(id, payload) {
      return call("/crm/customers.php", { method: "PATCH", params: { id }, body: payload });
    },
    deleteCustomer(id) {
      return call("/crm/customers.php", { method: "DELETE", params: { id } });
    },

    /* ── Commercial Leads ── */
    leads(params = {}) {
      return call("/crm/leads.php", { params });
    },
    lead(id, params = {}) {
      return call("/crm/leads.php", { params: { ...params, id } });
    },
    createLead(payload) {
      return call("/crm/leads.php", { method: "POST", body: payload }).then((res) => {
        bus.emit("crm:leadCreated", res.data);
        return res;
      });
    },
    convertLead(leadId, payload = {}) {
      return call("/crm/leads.php", {
        method: "POST",
        params: { action: "convert", lead_id: leadId },
        body: payload,
      }).then((res) => {
        bus.emit("crm:leadConverted", { leadId, result: res.data });
        return res;
      });
    },

    /* ── Opportunities Pipeline ── */
    opportunities(params = {}) {
      return call("/crm/opportunities.php", { params });
    },
    createOpportunity(payload) {
      return call("/crm/opportunities.php", { method: "POST", body: payload }).then((res) => {
        bus.emit("crm:opportunityCreated", res.data);
        return res;
      });
    },
    updateStage(oppId, stage) {
      return call("/crm/opportunities.php", {
        method: "PATCH",
        body: { opp_id: oppId, stage },
      }).then((res) => {
        bus.emit("crm:stageUpdated", { oppId, stage });
        return res;
      });
    },
    deleteOpportunity(oppId) {
      return call("/crm/opportunities.php", { method: "DELETE", params: { id: oppId } });
    },

    /* ── Sales Forecasts ── */
    forecasts(params = {}) {
      return call("/crm/forecasts.php", { params });
    },
    upsertForecast(payload) {
      return call("/crm/forecasts.php", { method: "POST", body: payload });
    },

    /* ── Inter-Module Communication ── */
    async requestCustomerOrders(cusId) {
      console.info(`[VostokCRM] Inter-Module RPC -> Shop (SYS02): Orders for ${cusId}`);
      return await bus.request("shop", "orders", { cus_id: cusId });
    },
    async requestCustomerTickets(cusId) {
      console.info(`[VostokCRM] Inter-Module RPC -> Customer Portal (SYS03): Tickets for ${cusId}`);
      return await bus.request("customer", "tickets", { cus_id: cusId });
    },
    async requestStaffInfo(empId) {
      console.info(`[VostokCRM] Inter-Module RPC -> Intranet (SYS04): Staff info for ${empId}`);
      return await bus.request("intranet", "directory", { query: empId });
    },
  };

  if (global.VostokBus) {
    global.VostokBus.register("crm", crmApi);
  }
  global.VostokCRM = crmApi;
})(window);
