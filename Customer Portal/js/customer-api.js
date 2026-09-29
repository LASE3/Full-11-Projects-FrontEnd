/**
 * VOSTOKPRIBOR Customer Portal — Module-Specific API Client (SYS 03)
 * Location: Customer Portal/js/customer-api.js
 * Mirrors assets/js/api-customer.js for subsystem-isolated deployment.
 */
(function (global) {
  "use strict";

  if (!global.VostokCore) {
    console.error("[VostokCustomer] Error: assets/js/api-core.js must be loaded before customer-api.js.");
  }

  const core = global.VostokCore || {
    call: async () => { throw new Error("VostokCore not loaded"); },
    bus: { request: async () => {}, emit: () => {}, register: () => {} },
    ui: { toast: () => {} }
  };
  const { call, bus } = core;

  const customerApi = {
    MODULE_NAME: "customer",
    SYSTEM_CODE: "SYS03",

    /* ── Overview Dashboard & Telemetry ── */
    dashboard(params = {}) {
      return call("/customer/dashboard.php", { params });
    },

    /* ── Industrial Projects ── */
    projects(params = {}) {
      return call("/customer/projects.php", { params });
    },
    project(id, params = {}) {
      return call("/customer/projects.php", { params: { ...params, id } });
    },

    /* ── Billing & Invoices ── */
    invoices(params = {}) {
      return call("/customer/invoices.php", { params });
    },
    invoice(id, params = {}) {
      return call("/customer/invoices.php", { params: { ...params, id } });
    },

    /* ── Technical Support Tickets ── */
    tickets(params = {}) {
      return call("/customer/tickets.php", { params });
    },
    ticket(id, params = {}) {
      return call("/customer/tickets.php", { params: { ...params, id } });
    },
    submitTicket(payload) {
      return call("/customer/tickets.php", {
        method: "POST",
        body: payload,
      }).then((res) => {
        bus.emit("customer:ticketSubmitted", res.data);
        return res;
      });
    },

    /* ── Inter-Module Communication ── */
    async getShopOrders(params = {}) {
      console.info(`[VostokCustomer] Inter-Module RPC -> Online Shop (SYS02): Querying shop orders`);
      return await bus.request("shop", "orders", params);
    },
    async requestCatalogQuote(payload) {
      console.info(`[VostokCustomer] Inter-Module RPC -> Online Shop (SYS02): Requesting quote`);
      return await bus.request("shop", "quotes", {}, { method: "POST", body: payload });
    },
    async getSupportEngineerInfo(empId) {
      console.info(`[VostokCustomer] Inter-Module RPC -> Intranet (SYS04): Engineer info for ${empId}`);
      return await bus.request("intranet", "directory", { query: empId });
    },
    async getCRMAccountInfo(cusId) {
      console.info(`[VostokCustomer] Inter-Module RPC -> CRM (SYS05): Master account for ${cusId}`);
      return await bus.request("crm", "customer", { id: cusId });
    },
  };

  if (global.VostokBus) {
    global.VostokBus.register("customer", customerApi);
  }
  global.VostokCustomer = customerApi;
})(window);
