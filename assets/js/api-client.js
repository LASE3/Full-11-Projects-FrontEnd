/**
 * VOSTOKPRIBOR Enterprise Platform — Unified API Facade & Module Aggregator
 * Orchestrates isolated subsystem clients (CRM, Shop, Customer Portal, Intranet)
 * over the central VostokBus inter-module communication backbone.
 *
 * For isolated subsystem deployment, pages may include:
 *   - assets/js/api-core.js + assets/js/api-<module>.js
 *
 * For universal cross-system access, pages may include this file:
 *   - assets/js/api-client.js (loads core + all 4 isolated modules)
 */

(function (global) {
  "use strict";

  // 1. Ensure VostokCore is present or initialized
  if (!global.VostokCore) {
    // If api-core.js was not loaded via separate script tag,
    // load it synchronously or initialize inline core
    console.warn("[VostokAPI] Warning: api-core.js was not loaded before api-client.js.");
  }

  const core = global.VostokCore;
  const bus  = global.VostokBus;

  // 2. Define or mount isolated modules if not already registered
  const crm      = global.VostokCRM      || bus?.getModule("crm");
  const shop     = global.VostokShop     || bus?.getModule("shop");
  const customer = global.VostokCustomer || bus?.getModule("customer");
  const intranet = global.VostokIntranet || bus?.getModule("intranet");

  // 3. Construct unified VostokAPI surface backed by VostokBus
  const VostokAPI = {
    crm: crm || (core ? {
      customers: (p) => core.call("/crm/customers.php", { params: p }),
      customer: (id, p) => core.call("/crm/customers.php", { params: { ...p, id } }),
      leads: (p) => core.call("/crm/leads.php", { params: p }),
      convertLead: (id, b) => core.call("/crm/leads.php", { method: "POST", params: { action: "convert", lead_id: id }, body: b }),
      opportunities: (p) => core.call("/crm/opportunities.php", { params: p }),
      createOpportunity: (b) => core.call("/crm/opportunities.php", { method: "POST", body: b }),
      forecasts: (p) => core.call("/crm/forecasts.php", { params: p }),
      upsertForecast: (b) => core.call("/crm/forecasts.php", { method: "POST", body: b }),
    } : {}),

    shop: shop || (core ? {
      products: (p) => core.call("/shop/products.php", { params: p }),
      orders: (p) => core.call("/shop/orders.php", { params: p }),
      placeOrder: (b) => core.call("/shop/orders.php", { method: "POST", body: b }),
      quotes: (p) => core.call("/shop/quotes.php", { params: p }),
      requestQuote: (b) => core.call("/shop/quotes.php", { method: "POST", body: b }),
    } : {}),

    customer: customer || (core ? {
      dashboard: (p) => core.call("/customer/dashboard.php", { params: p }),
      projects: (p) => core.call("/customer/projects.php", { params: p }),
      invoices: (p) => core.call("/customer/invoices.php", { params: p }),
      tickets: (p) => core.call("/customer/tickets.php", { params: p }),
      submitTicket: (b) => core.call("/customer/tickets.php", { method: "POST", body: b }),
    } : {}),

    intranet: intranet || (core ? {
      announcements: (p) => core.call("/intranet/announcements.php", { params: p }),
      directory: (p) => core.call("/intranet/directory.php", { params: p }),
      leaves: (p) => core.call("/intranet/leaves.php", { params: p }),
      submitLeave: (b) => core.call("/intranet/leaves.php", { method: "POST", body: b }),
    } : {}),

    // Core & Inter-Module Bus references
    bus: bus || null,
    core: core || null,
    ui: core?.ui || {},
    i18n: core?.i18n || {},
    call: core?.call || null,
    escHtml: core?.escHtml || ((s) => s),
    handleApiError: core?.handleApiError || console.error,
    BASE: core?.BASE || "../api/v1",
  };

  // Register un-registered modules onto VostokBus if present
  if (bus) {
    if (!bus.hasModule("crm") && VostokAPI.crm) bus.register("crm", VostokAPI.crm);
    if (!bus.hasModule("shop") && VostokAPI.shop) bus.register("shop", VostokAPI.shop);
    if (!bus.hasModule("customer") && VostokAPI.customer) bus.register("customer", VostokAPI.customer);
    if (!bus.hasModule("intranet") && VostokAPI.intranet) bus.register("intranet", VostokAPI.intranet);
  }

  // Export to global
  global.VostokAPI = VostokAPI;
  console.info("[VostokAPI] Unified Facade ready with Inter-Module Bus.");
})(window);
