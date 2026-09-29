/**
 * VOSTOKPRIBOR Online Shop B2B — Isolated API Client (SYS 02)
 * Scope: Strictly restricted to B2B Industrial E-Commerce operations
 * (Product Catalog, Customer Orders, RFQ Quotes, Inventory).
 *
 * Includes Inter-Module Communication hooks for querying Customer Portal
 * dossiers and CRM master accounts via VostokBus.
 *
 * Exposes:
 *   - window.VostokShop
 *   - Registered as 'shop' on window.VostokBus
 */

(function (global) {
  "use strict";

  if (!global.VostokCore) {
    console.error("[VostokShop] Error: assets/js/api-core.js must be loaded before api-shop.js.");
  }

  const core = global.VostokCore || {
    call: async () => { throw new Error("VostokCore not loaded"); },
    bus: { request: async () => {}, emit: () => {}, register: () => {} },
    ui: { toast: () => {} }
  };
  const { call, bus } = core;

  /* ──────────────────────────────────────────────────────────────────────────
   * Isolated B2B Shop API Methods
   * ────────────────────────────────────────────────────────────────────────── */
  const shopApi = {
    MODULE_NAME: "shop",
    SYSTEM_CODE: "SYS02",

    /* ── Products & Catalog ── */
    products(params = {}) {
      return call("/shop/products.php", { params });
    },

    /* ── Orders & Placement ── */
    orders(params = {}) {
      return call("/shop/orders.php", { params });
    },

    order(id, params = {}) {
      return call("/shop/orders.php", { params: { ...params, order_id: id } });
    },

    placeOrder(payload) {
      return call("/shop/orders.php", {
        method: "POST",
        body: payload,
      }).then((res) => {
        bus.emit("shop:orderPlaced", res.data);
        return res;
      });
    },

    /* ── Commercial Quotes (RFQ) ── */
    quotes(params = {}) {
      return call("/shop/quotes.php", { params });
    },

    requestQuote(payload) {
      return call("/shop/quotes.php", {
        method: "POST",
        body: payload,
      }).then((res) => {
        bus.emit("shop:quoteRequested", res.data);
        return res;
      });
    },

    /* ────────────────────────────────────────────────────────────────────────
     * Inter-Module Data Sharing (Cross-System Communication)
     * ──────────────────────────────────────────────────────────────────────── */

    /**
     * Cross-system query: Request customer dossier from Customer Portal (SYS03)
     * @param {string} [cusId]
     */
    async requestCustomerDossier(cusId) {
      console.info(`[VostokShop] Inter-Module RPC -> Customer Portal (SYS03): Fetching dossier for ${cusId || "current user"}`);
      return await bus.request("customer", "dashboard", cusId ? { cus_id: cusId } : {});
    },

    /**
     * Cross-system query: Request CRM enterprise account contracts (SYS05)
     * @param {string} cusId
     */
    async requestCRMContracts(cusId) {
      console.info(`[VostokShop] Inter-Module RPC -> CRM (SYS05): Fetching master contract terms for ${cusId}`);
      return await bus.request("crm", "customer", { id: cusId });
    },

    /**
     * Cross-system query: Request assigned Account Manager / Sales Rep info from Intranet (SYS04)
     * @param {string} empId
     */
    async requestSalesRep(empId) {
      console.info(`[VostokShop] Inter-Module RPC -> Intranet (SYS04): Fetching sales engineer profile for ${empId}`);
      return await bus.request("intranet", "directory", { query: empId });
    },
  };

  // Register with Inter-Module Bus
  if (global.VostokBus) {
    global.VostokBus.register("shop", shopApi);
  }

  // Export isolated namespace
  global.VostokShop = shopApi;
  console.info("[VostokShop] Isolated B2B Shop API module ready.");
})(window);
