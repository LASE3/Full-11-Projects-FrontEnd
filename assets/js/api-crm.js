/**
 * VOSTOKPRIBOR CRM Platform — Isolated API Client (SYS 05)
 * Scope: Strictly restricted to CRM operations (Leads, Opportunities,
 * Customers 360, Quotes/Contracts, Sales Forecasts).
 *
 * Includes Inter-Module Communication hooks for querying B2B orders,
 * customer tickets, and staff directory records via VostokBus.
 *
 * Exposes:
 *   - window.VostokCRM
 *   - Registered as 'crm' on window.VostokBus
 */

(function (global) {
  "use strict";

  if (!global.VostokCore) {
    console.error("[VostokCRM] Error: assets/js/api-core.js must be loaded before api-crm.js.");
  }

  const core = global.VostokCore || {
    call: async () => { throw new Error("VostokCore not loaded"); },
    bus: { request: async () => {}, emit: () => {}, register: () => {} },
    ui: { toast: () => {} }
  };
  const { call, bus } = core;

  /* ──────────────────────────────────────────────────────────────────────────
   * Isolated CRM API Methods
   * ────────────────────────────────────────────────────────────────────────── */
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
      return call("/crm/customers.php", {
        method: "POST",
        body: payload,
      }).then((res) => {
        bus.emit("crm:customerCreated", res.data);
        return res;
      });
    },

    updateCustomer(id, payload) {
      return call("/crm/customers.php", {
        method: "PATCH",
        params: { id },
        body: payload,
      });
    },

    deleteCustomer(id) {
      return call("/crm/customers.php", {
        method: "DELETE",
        params: { id },
      });
    },

    /* ── Commercial Leads ── */
    leads(params = {}) {
      return call("/crm/leads.php", { params });
    },

    lead(id, params = {}) {
      return call("/crm/leads.php", { params: { ...params, id } });
    },

    createLead(payload) {
      return call("/crm/leads.php", {
        method: "POST",
        body: payload,
      }).then((res) => {
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
      return call("/crm/opportunities.php", {
        method: "POST",
        body: payload,
      }).then((res) => {
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
      return call("/crm/opportunities.php", {
        method: "DELETE",
        params: { id: oppId },
      });
    },

    /* ── Dashboard Metrics ── */
    dashboard(params = {}) {
      return call("/crm/dashboard.php", { params });
    },

    /* ── Sales Forecasts ── */
    forecasts(params = {}) {
      return call("/crm/forecasts.php", { params });
    },

    upsertForecast(payload) {
      return call("/crm/forecasts.php", {
        method: "POST",
        body: payload,
      });
    },

    /* ────────────────────────────────────────────────────────────────────────
     * Inter-Module Data Sharing (Cross-System Communication)
     * ──────────────────────────────────────────────────────────────────────── */

    /**
     * Cross-system query: Fetch orders from B2B Shop for a given customer
     * @param {string} cusId
     */
    async requestCustomerOrders(cusId) {
      console.info(`[VostokCRM] Inter-Module RPC -> Shop (SYS02): Fetching orders for ${cusId}`);
      return await bus.request("shop", "orders", { cus_id: cusId });
    },

    /**
     * Cross-system query: Fetch open support tickets from Customer Portal
     * @param {string} cusId
     */
    async requestCustomerTickets(cusId) {
      console.info(`[VostokCRM] Inter-Module RPC -> Customer Portal (SYS03): Fetching tickets for ${cusId}`);
      return await bus.request("customer", "tickets", { cus_id: cusId });
    },

    /**
     * Cross-system query: Fetch employee details from Intranet Directory
     * @param {string} empId
     */
    async requestStaffInfo(empId) {
      console.info(`[VostokCRM] Inter-Module RPC -> Intranet (SYS04): Fetching staff info for ${empId}`);
      return await bus.request("intranet", "directory", { query: empId });
    },
  };

  // Register with Inter-Module Bus
  if (global.VostokBus) {
    global.VostokBus.register("crm", crmApi);
  }

  // Export isolated namespace
  global.VostokCRM = crmApi;
  console.info("[VostokCRM] Isolated CRM API module ready.");
})(window);
