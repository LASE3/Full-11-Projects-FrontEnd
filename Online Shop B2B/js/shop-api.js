/**
 * VOSTOKPRIBOR Online Shop B2B — Module-Specific API Client (SYS 02)
 * Location: Online Shop B2B/js/shop-api.js
 * Mirrors assets/js/api-shop.js for subsystem-isolated deployment.
 */
(function (global) {
  "use strict";

  if (!global.VostokCore) {
    console.error("[VostokShop] Error: assets/js/api-core.js must be loaded before shop-api.js.");
  }

  const core = global.VostokCore || {
    call: async () => { throw new Error("VostokCore not loaded"); },
    bus: { request: async () => {}, emit: () => {}, register: () => {} },
    ui: { toast: () => {} }
  };
  const { call, bus } = core;

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

    /* ── Inter-Module Communication ── */
    async requestCustomerDossier(cusId) {
      console.info(`[VostokShop] Inter-Module RPC -> Customer Portal (SYS03): Dossier for ${cusId || "current user"}`);
      return await bus.request("customer", "dashboard", cusId ? { cus_id: cusId } : {});
    },
    async requestCRMContracts(cusId) {
      console.info(`[VostokShop] Inter-Module RPC -> CRM (SYS05): Master contracts for ${cusId}`);
      return await bus.request("crm", "customer", { id: cusId });
    },
    async requestSalesRep(empId) {
      console.info(`[VostokShop] Inter-Module RPC -> Intranet (SYS04): Profile for ${empId}`);
      return await bus.request("intranet", "directory", { query: empId });
    },
  };

  if (global.VostokBus) {
    global.VostokBus.register("shop", shopApi);
  }
  global.VostokShop = shopApi;
})(window);
