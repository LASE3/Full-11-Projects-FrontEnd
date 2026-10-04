/**
 * VOSTOKPRIBOR Online Shop B2B — Live Data Integration  (Class 2)
 * Requires: assets/js/api-core.js and assets/js/api-shop.js loaded before this file.
 *
 * Pages covered:
 *  - Dashboard.php  → product catalogue + order history
 *  - (quotes handled via requestQuote modal)
 */

(function () {
  "use strict";

  const core = window.VostokCore || window.VostokAPI || {};
  const shop = window.VostokShop || window.VostokAPI?.shop;
  const { ui = {}, escHtml = (s) => s } = core;
  const handleApiError = core.handleApiError || console.error;

  /* ──────────────────────────────────────────────
   * PRODUCT CATALOGUE
   * Element: #catalog-products-grid or #products-grid or #products-tbody
   * ────────────────────────────────────────────── */
  async function loadProducts() {
    const grid =
      document.getElementById("catalog-products-grid") ||
      document.getElementById("products-grid");
    const tbody = document.getElementById("products-tbody");
    if (!grid && !tbody) return;

    try {
      const res = await shop.products();
      const products = res.data;

      if (!products.length) return;

      const stockStatus = (stock) => {
        if (stock === null || stock === undefined)
          return { label: "—", color: "#aaa" };
        if (stock <= 0) return { label: "Out of Stock", color: "#e74c3c" };
        if (stock <= 10) return { label: "Low Stock", color: "#f39c12" };
        return { label: "In Stock", color: "#2ecc71" };
      };

      // Grid view
      if (grid) {
        grid.innerHTML = products
          .map((p) => {
            const st = stockStatus(p.in_stock ?? p.stock_quantity);
            const pid = p.prod_id || p.product_id;
            const price = parseFloat(p.effective_price || p.unit_price || 2500);

            return `
            <div class="product-card" data-product-id="${escHtml(pid)}" style="border:1px solid #1b3a5c;background:#0d1e2e;border-radius:8px;padding:18px;display:flex;flex-direction:column;justify-content:space-between;box-shadow:0 4px 16px rgba(0,0,0,0.35);transition:all 0.25s ease;" onmouseover="this.style.borderColor='rgba(0,229,255,0.5)';this.style.transform='translateY(-3px)'" onmouseout="this.style.borderColor='#1b3a5c';this.style.transform='none'">
              <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                  <span class="product-code" style="font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:600;color:#00E5FF;background:rgba(0,229,255,0.12);border:1px solid rgba(0,229,255,0.25);padding:3px 8px;border-radius:4px;">${escHtml(pid)}</span>
                  <span style="font-size:11px;font-weight:700;padding:3px 8px;border-radius:12px;background:${st.color}20;color:${st.color};border:1px solid ${st.color}40;">${st.label}</span>
                </div>
                <div class="product-name" style="font-weight:700;font-size:15px;margin:8px 0 6px;color:#ffffff;line-height:1.35;">${escHtml(p.product_name || p.name)}</div>
                <div class="product-desc" style="font-size:12px;color:#94a3b8;line-height:1.45;margin-bottom:14px;">
                  ${escHtml(p.billing_model_display || p.billing_model || "Industrial Precision Equipment")} · Location: ${escHtml(p.warehouse_location || "Warehouse")}
                </div>
              </div>
              <div>
                <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:12px;">
                  <div style="font-size:18px;font-weight:800;font-family:'JetBrains Mono',monospace;color:#F59E0B;">
                    ${ui.currency(price, "EUR")}
                  </div>
                  <div style="font-size:11px;color:#94a3b8;font-family:'JetBrains Mono',monospace;">Available: ${p.in_stock ?? 45} units</div>
                </div>
                <button class="btn btn-primary-amber btn-sm" style="width:100%;padding:9px 14px;background:linear-gradient(135deg,#F59E0B,#D97706);color:#07131e;font-weight:800;font-size:12px;border:none;border-radius:4px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;box-shadow:0 2px 8px rgba(245,158,11,0.3);transition:all 0.2s;"
                  onclick="window.VostokAPI && window.VostokAPI.ui && window.VostokAPI.ui.toast ? window.VostokAPI.ui.toast('Order Placed', 'Initiated commercial RFQ for ${escHtml(pid)}', 'success') : (window.shopApp && window.shopApp.showToast ? window.shopApp.showToast('Initiated commercial RFQ for ${escHtml(pid)}', 'green') : alert('RFQ initiated for ${escHtml(pid)}'))"
                  ${st.label === "Out of Stock" ? 'disabled style="opacity:.4;cursor:not-allowed;"' : ""}>
                  ${st.label === "Out of Stock" ? "Out of Stock" : "+ Request Quote / Order"}
                </button>
              </div>
            </div>`;
          })
          .join("");
      }
    } catch (err) {
      handleApiError(err, "Shop Products");
    }
  }

  /* ──────────────────────────────────────────────
   * ORDER HISTORY
   * Element: #tracking-orders-table-body or #orders-tbody
   * ────────────────────────────────────────────── */
  async function loadOrders() {
    const tbody =
      document.getElementById("tracking-orders-table-body") ||
      document.getElementById("orders-tbody");
    if (!tbody) return;

    try {
      const cusId =
        sessionStorage.getItem("vp_cus_id") ||
        localStorage.getItem("vp_cus_id") ||
        "CUS-1001";
      const res = await shop.orders({ cus_id: cusId });
      const orders = res.data;

      if (!orders || !orders.length) return;

      const statusStyle = {
        Pending: "color:#f39c12;background:rgba(243,156,18,.12)",
        Processing: "color:#00E5FF;background:rgba(0,229,255,.12)",
        Shipped: "color:#9b59b6;background:rgba(155,89,182,.12)",
        Delivered: "color:#2ecc71;background:rgba(46,204,113,.12)",
        Cancelled: "color:#e74c3c;background:rgba(231,76,60,.12)",
      };

      tbody.innerHTML = orders
        .map((o) => {
          const style =
            statusStyle[o.status || o.order_status] ||
            "color:#aaa;background:rgba(170,170,170,.1)";
          const ordId = "ORD-" + String(o.order_id).padStart(6, "0");

          return `
          <tr class="confidential-row" style="cursor:pointer;" onclick="window.VostokAPI.ui.toast('Order Details', 'Order ${ordId} - Status: ${escHtml(o.status || 'Processing')}', 'info')">
            <td style="font-family:monospace;font-size:12px;">
              <span style="color:#00E5FF;font-weight:700;">${ordId}</span>
              <div style="font-size:10px;opacity:0.6;">ID: ${escHtml(String(o.order_id))}</div>
            </td>
            <td style="font-family:monospace;font-size:11px;">
              <span style="background:rgba(255,255,255,0.06);padding:2px 6px;border-radius:4px;">
                ${escHtml(o.cus_id || '—')}
              </span>
            </td>
            <td>
              <strong style="color:#fff;">${escHtml(o.company_name || o.cus_id || '—')}</strong>
              <div style="font-size:10.5px;opacity:0.6;font-family:monospace;">${escHtml(o.primary_contact_name || '')}</div>
            </td>
            <td>
              <div style="font-weight:600;color:#fff;">${escHtml(o.sector || 'B2B Order')}</div>
              <div style="font-size:11px;opacity:0.6;">${o.total_items ?? o.line_item_count ?? 0} line item(s) · ${o.total_units ?? 0} units</div>
            </td>
            <td style="font-family:monospace;">
              <strong style="color:#D9822B;font-size:13px;">${ui.currency(o.total_amount, "EUR")}</strong>
            </td>
            <td>
              <span style="padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;${style}">
                ${escHtml(o.status || o.order_status || "Processing")}
              </span>
            </td>
            <td style="font-family:monospace;font-size:11px;">
              ${ui.date(o.order_date || o.created_at)}
            </td>
            <td style="text-align:right;">
              <button class="btn-table-action" style="padding:4px 10px;background:rgba(0,229,255,0.1);border:1px solid rgba(0,229,255,0.4);color:#00E5FF;border-radius:4px;cursor:pointer;"
                onclick="event.stopPropagation(); window.VostokAPI.ui.toast('Live Tracking', 'Tracking active for ${ordId}', 'info')">
                Inspect
              </button>
            </td>
          </tr>`;
        })
        .join("");
    } catch (err) {
      handleApiError(err, "Order History");
    }
  }

  /* ──────────────────────────────────────────────
   * DASHBOARD KPIs + CUSTOMER CONTEXT HEADER
   * Elements: #shop-kpi-*, #header-customer-*, #customer-dropdown-items
   * ────────────────────────────────────────────── */
  async function loadDashboard() {
    try {
      // ── Customer context (header dropdown) ──────────
      const cusRes = await fetch("../api/v1/customers.php?limit=20");
      const cusJson = await cusRes.json();
      const customers = cusJson.data || [];

      const urlParams = new URLSearchParams(window.location.search);
      const urlCusId = urlParams.get("cus_id");
      const activeCusId =
        urlCusId ||
        sessionStorage.getItem("vp_cus_id") ||
        localStorage.getItem("vp_cus_id") ||
        (customers[0] && (customers[0].cus_id || customers[0].id)) ||
        "";
      if (urlCusId) {
        try { sessionStorage.setItem("vp_cus_id", urlCusId); } catch (e) {}
      }

      // Populate header chip with the active customer
      const activeCus = customers.find((c) => (c.cus_id || c.id) === activeCusId) || customers[0];
      if (activeCus) {
        const code = activeCus.cus_id || activeCus.id || "—";
        const name = activeCus.company_name || activeCus.name || "Enterprise Account";
        const avatar = name.charAt(0).toUpperCase();
        const elAvatar = document.getElementById("header-customer-avatar");
        const elCode   = document.getElementById("header-customer-code");
        const elName   = document.getElementById("header-customer-name");
        if (elAvatar) elAvatar.textContent = avatar;
        if (elCode)   elCode.textContent   = code;
        if (elName)   elName.textContent   = name;
      }

      // Populate dropdown switcher items if empty
      const dropdownItems = document.getElementById("customer-dropdown-items");
      if (dropdownItems && (!dropdownItems.children.length || dropdownItems.children[0].innerText.includes("Loading"))) {
        if (customers.length) {
          dropdownItems.innerHTML = customers.map((c) => {
            const code  = c.cus_id || c.id || "—";
            const cname = c.company_name || c.name || code;
            const sector = c.industry_sector || c.sector || "Industrial";
            const isSelected = code === activeCusId;
            const initial = (cname || "C").charAt(0).toUpperCase();
            return `<div class="customer-option-item ${isSelected ? 'selected' : ''}" data-code="${escHtml(code)}" data-name="${escHtml(cname.toLowerCase())}" onclick="window.selectEnterpriseAccount ? window.selectEnterpriseAccount('${escHtml(code)}', event) : (window.location.href='Dashboard.php?cus_id=${encodeURIComponent(code)}')" style="padding:10px 14px;cursor:pointer;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(255,255,255,0.06);transition:background 0.2s;background:${isSelected ? 'rgba(0,229,255,0.12)' : 'transparent'};">
              <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;">
                <div style="width:28px;height:28px;border-radius:50%;background:${isSelected ? 'rgba(0,229,255,0.2)' : 'rgba(255,255,255,0.06)'};border:1px solid ${isSelected ? '#00E5FF' : 'rgba(255,255,255,0.12)'};display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:${isSelected ? '#00E5FF' : '#94a3b8'};flex-shrink:0;">
                  ${escHtml(initial)}
                </div>
                <div style="flex:1;min-width:0;">
                  <div style="display:flex;align-items:center;gap:6px;">
                    <span style="font-family:'JetBrains Mono',monospace;color:#00E5FF;font-size:11px;font-weight:600;">${escHtml(code)}</span>
                    <span style="color:#ffffff;font-size:12.5px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escHtml(cname)}</span>
                  </div>
                  <div style="font-size:11px;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escHtml(sector)}</div>
                </div>
              </div>
              ${isSelected ? '<span style="color:#00E5FF;font-size:10px;font-weight:700;background:rgba(0,229,255,0.15);padding:2px 6px;border-radius:4px;margin-left:8px;flex-shrink:0;">ACTIVE</span>' : ''}
            </div>`;
          }).join("");
        } else {
          dropdownItems.innerHTML = "<div style='padding:12px 14px;font-size:12px;color:#94a3b8;'>No accounts found</div>";
        }
      }
    } catch (err) {
      /* customer header is optional — fail silently */
    }

    // ── KPI metrics from orders ──────────────────────
    try {
      const cusId =
        sessionStorage.getItem("vp_cus_id") ||
        localStorage.getItem("vp_cus_id") ||
        "";
      const ordUrl = cusId
        ? `../api/v1/orders.php?limit=200&cus_id=${encodeURIComponent(cusId)}`
        : `../api/v1/orders.php?limit=200`;
      const ordRes  = await fetch(ordUrl);
      const ordJson = await ordRes.json();
      const orders  = ordJson.data || [];

      const totalOrders = orders.length;
      const totalValue  = orders.reduce((s, o) => s + parseFloat(o.total_amount || 0), 0);
      const shipped     = orders.filter((o) => (o.status || o.order_status || "").toLowerCase() === "shipped").length;
      const pending     = orders.filter((o) => (o.status || o.order_status || "").toLowerCase() === "pending").length;

      const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };

      set("shop-kpi-orders",    totalOrders || "—");
      set("shop-kpi-orders-sub", totalOrders ? `${totalOrders} live order${totalOrders !== 1 ? "s" : ""}` : "No orders yet");

      set("shop-kpi-value",    totalValue ? ui.currency(totalValue, "EUR") : "—");
      set("shop-kpi-value-sub", totalOrders ? `${totalOrders} platform order${totalOrders !== 1 ? "s" : ""}` : "No data");

      set("shop-kpi-shipments",    shipped || "—");
      set("shop-kpi-shipments-sub", `${shipped} in-transit shipment${shipped !== 1 ? "s" : ""}`);

      set("shop-kpi-rfqs",    pending || "—");
      set("shop-kpi-rfqs-sub", `${pending} commercial RFQ${pending !== 1 ? "s" : ""}`);

      // Nav badge (Quotes tab)
      const navQuotes = document.getElementById("nav-quotes-count");
      if (navQuotes) navQuotes.textContent = pending;
    } catch (err) {
      handleApiError(err, "Dashboard KPIs");
    }
  }

  /* ──────────────────────────────────────────────
   * Simple Cart (in-memory, no server state)
   * ────────────────────────────────────────────── */
  const cart = [];

  window.shopAddToCart = function (productId, name, price, currency) {
    const existing = cart.find((i) => i.productId === productId);
    if (existing) {
      existing.qty++;
    } else {
      cart.push({ productId, name, price, currency, qty: 1 });
    }

    const count = cart.reduce((s, i) => s + i.qty, 0);
    const badge = document.getElementById("header-cart-badge") || document.getElementById("cart-badge");
    if (badge) badge.textContent = count;

    ui.toast(
      "Added to Order",
      `${name} × ${existing ? existing.qty : 1}`,
      "success",
      2500,
    );
  };

  window.shopCheckout = async function () {
    if (!cart.length) {
      ui.toast(
        "Empty Order",
        "Add products before placing an order.",
        "warning",
      );
      return;
    }

    const btn = document.getElementById("checkout-btn");
    try {
      const cusId = sessionStorage.getItem("vp_cus_id") || localStorage.getItem("vp_cus_id") || "CUS-1001";
      const payload = {
        cus_id: cusId,
        items: cart.map((i) => ({
          prod_id: i.productId,
          product_id: i.productId,
          quantity: i.qty,
          unit_price: i.price,
        })),
      };
      const res = await ui.withLoading(btn, shop.placeOrder(payload));
      ui.toast(
        "Order Placed",
        `Order ${res.data?.order_id || ""} confirmed!`,
        "success",
      );
      cart.length = 0;
      const badge = document.getElementById("header-cart-badge") || document.getElementById("cart-badge");
      if (badge) badge.textContent = "0";
      loadOrders();
    } catch (err) {
      handleApiError(err, "Place Order");
    }
  };

  /* ──────────────────────────────────────────────
   * Product search (client-side)
   * ────────────────────────────────────────────── */
  function initProductSearch() {
    const input = document.getElementById("product-search");
    if (!input) return;
    input.addEventListener("input", () => {
      const q = input.value.toLowerCase();
      document.querySelectorAll(".product-card,.product-row").forEach((el) => {
        el.style.display = el.textContent.toLowerCase().includes(q)
          ? ""
          : "none";
      });
    });
  }

  /* ──────────────────────────────────────────────
   * Boot
   * ────────────────────────────────────────────── */
  document.addEventListener("DOMContentLoaded", () => {
    loadDashboard();
    loadProducts();
    loadOrders();
    initProductSearch();
  });
})();
