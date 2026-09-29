/**
 * VOSTOKPRIBOR Customer Portal — Isolated API Client (SYS 03)
 * Scope: Strictly restricted to Customer Portal operations
 * (Account Profile, Industrial Projects, Invoices/Billing, Support Tickets).
 *
 * Includes Inter-Module Communication hooks for querying B2B orders
 * and catalog quote requests via VostokBus.
 *
 * Exposes:
 *   - window.VostokCustomer
 *   - Registered as 'customer' on window.VostokBus
 */

(function (global) {
  "use strict";

  if (!global.VostokCore) {
    console.error("[VostokCustomer] Error: assets/js/api-core.js must be loaded before api-customer.js.");
  }

  const core = global.VostokCore || {
    call: async () => { throw new Error("VostokCore not loaded"); },
    bus: { request: async () => {}, emit: () => {}, register: () => {} },
    ui: { toast: () => {} }
  };
  const { call, bus } = core;

  /* ──────────────────────────────────────────────────────────────────────────
   * Isolated Customer Portal API Methods
   * ────────────────────────────────────────────────────────────────────────── */
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

    /* ────────────────────────────────────────────────────────────────────────
     * Inter-Module Data Sharing (Cross-System Communication)
     * ──────────────────────────────────────────────────────────────────────── */

    /**
     * Cross-system query: Request customer orders from Online Shop B2B (SYS02)
     * Used by Customer Portal Orders.php to populate orders placed via B2B e-commerce
     * @param {object} [params]
     */
    async getShopOrders(params = {}) {
      console.info(`[VostokCustomer] Inter-Module RPC -> Online Shop (SYS02): Querying shop orders`);
      return await bus.request("shop", "orders", params);
    },

    /**
     * Cross-system query: Request quote from B2B Shop catalog (SYS02)
     * @param {object} payload
     */
    async requestCatalogQuote(payload) {
      console.info(`[VostokCustomer] Inter-Module RPC -> Online Shop (SYS02): Requesting quote`);
      return await bus.request("shop", "quotes", {}, { method: "POST", body: payload });
    },

    /**
     * Cross-system query: Request assigned support engineer or manager profile from Intranet (SYS04)
     * @param {string} empId
     */
    async getSupportEngineerInfo(empId) {
      console.info(`[VostokCustomer] Inter-Module RPC -> Intranet (SYS04): Fetching engineer info for ${empId}`);
      return await bus.request("intranet", "directory", { query: empId });
    },

    /**
     * Cross-system query: Request CRM enterprise customer status (SYS05)
     * @param {string} cusId
     */
    async getCRMAccountInfo(cusId) {
      console.info(`[VostokCustomer] Inter-Module RPC -> CRM (SYS05): Fetching master account for ${cusId}`);
      return await bus.request("crm", "customer", { id: cusId });
    },
  };

  // Register with Inter-Module Bus
  if (global.VostokBus) {
    global.VostokBus.register("customer", customerApi);
  }

  // Export isolated namespace
  global.VostokCustomer = customerApi;
  console.info("[VostokCustomer] Isolated Customer Portal API module ready.");
})(window);
