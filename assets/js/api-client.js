/**
 * VOSTOKPRIBOR Enterprise Platform — Shared API Client
 * Centralises all fetch() calls to /api/v1/* endpoints.
 * Works for Class 2 (Shop), Class 3 (Customer Portal),
 * Class 4 (Intranet), and Class 5 (CRM).
 *
 * Usage (from any page JS):
 *   const client = window.VostokAPI;
 *   const data   = await client.crm.customers();
 */

(function (global) {
  'use strict';

  /* ─────────────────────────────────────────────
   * 1. Configuration
   * ───────────────────────────────────────────── */
  const BASE = (function () {
    // Works from any subdirectory depth
    const parts = window.location.pathname.split('/').filter(Boolean);
    // Remove the system folder (e.g. "CRM", "Online Shop B2B") + filename
    // so we always resolve back to the project root
    const depth = parts.length >= 2 ? parts.length - 1 : parts.length;
    const up    = '../'.repeat(depth);
    return (up || './') + 'api/v1';
  })();

  const DEFAULT_LANG = localStorage.getItem('vp_lang') || 'en';

  /* ─────────────────────────────────────────────
   * 2. Core fetch wrapper
   * ───────────────────────────────────────────── */
  /**
   * @param {string} endpoint  - relative to BASE e.g. '/crm/customers.php'
   * @param {object} [options] - fetch options + extra helpers
   * @param {object} [options.params]  - URL query params
   * @param {string} [options.method]  - HTTP method (default GET)
   * @param {object} [options.body]    - JSON body for POST/PUT
   * @returns {Promise<{success, data, meta, message}>}
   */
  async function call(endpoint, options = {}) {
    const { params = {}, method = 'GET', body = null } = options;

    // Always include lang
    params.lang = params.lang || DEFAULT_LANG;

    const url = new URL(BASE + endpoint, window.location.href);
    Object.entries(params).forEach(([k, v]) => {
      if (v !== null && v !== undefined) url.searchParams.set(k, v);
    });

    const fetchOpts = {
      method,
      credentials: 'include',           // send session cookies
      headers: { 'Accept': 'application/json' }
    };

    if (body && method !== 'GET') {
      fetchOpts.headers['Content-Type'] = 'application/json';
      fetchOpts.body = JSON.stringify(body);
    }

    const res  = await fetch(url.toString(), fetchOpts);
    const json = await res.json();

    if (!res.ok || !json.success) {
      const msg = json.message || `HTTP ${res.status}`;
      throw Object.assign(new Error(msg), { code: res.status, response: json });
    }

    return json;
  }

  /* ─────────────────────────────────────────────
   * 3. Namespace: CRM  (Class 5)
   * ───────────────────────────────────────────── */
  const crm = {
    customers(params = {}) { return call('/crm/customers.php', { params }); },
    customer(id, params = {}) { return call('/crm/customers.php', { params: { ...params, id } }); },
    leads(params = {}) { return call('/crm/leads.php', { params }); },
    convertLead(leadId, payload = {}) {
      return call('/crm/leads.php', { method: 'POST', params: { action: 'convert', lead_id: leadId }, body: payload });
    },
    opportunities(params = {}) { return call('/crm/opportunities.php', { params }); },
    createOpportunity(payload) { return call('/crm/opportunities.php', { method: 'POST', body: payload }); },
    forecasts(params = {}) { return call('/crm/forecasts.php', { params }); },
    upsertForecast(payload) { return call('/crm/forecasts.php', { method: 'POST', body: payload }); }
  };

  /* ─────────────────────────────────────────────
   * 4. Namespace: Shop  (Class 2)
   * ───────────────────────────────────────────── */
  const shop = {
    products(params = {}) { return call('/shop/products.php', { params }); },
    placeOrder(payload) { return call('/shop/orders.php', { method: 'POST', body: payload }); },
    orders(params = {}) { return call('/shop/orders.php', { params }); },
    quotes(params = {}) { return call('/shop/quotes.php', { params }); },
    requestQuote(payload) { return call('/shop/quotes.php', { method: 'POST', body: payload }); }
  };

  /* ─────────────────────────────────────────────
   * 5. Namespace: Customer Portal  (Class 3)
   * ───────────────────────────────────────────── */
  const customer = {
    dashboard(params = {}) { return call('/customer/dashboard.php', { params }); },
    projects(params = {}) { return call('/customer/projects.php', { params }); },
    invoices(params = {}) { return call('/customer/invoices.php', { params }); },
    tickets(params = {}) { return call('/customer/tickets.php', { params }); },
    submitTicket(payload) { return call('/customer/tickets.php', { method: 'POST', body: payload }); }
  };

  /* ─────────────────────────────────────────────
   * 6. Namespace: Intranet  (Class 4)
   * ───────────────────────────────────────────── */
  const intranet = {
    announcements(params = {}) { return call('/intranet/announcements.php', { params }); },
    directory(params = {}) { return call('/intranet/directory.php', { params }); },
    leaves(params = {}) { return call('/intranet/leaves.php', { params }); },
    submitLeave(payload) { return call('/intranet/leaves.php', { method: 'POST', body: payload }); }
  };

  /* ─────────────────────────────────────────────
   * 7. UI Helpers
   * ───────────────────────────────────────────── */
  function escHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, c => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    })[c]);
  }

  const ui = {
    toast(title, message, type = 'info', duration = 4000) {
      let container = document.getElementById('toast-container');
      if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
      }
      const toast = document.createElement('div');
      toast.className = 'vp-toast vp-toast--' + type;
      toast.innerHTML = `
        <div class="vp-toast__icon">${{ success: '✔', error: '✖', warning: '⚠', info: 'ℹ' }[type] || 'ℹ'}</div>
        <div class="vp-toast__body"><strong>${escHtml(title)}</strong><span>${escHtml(message)}</span></div>
        <button class="vp-toast__close" onclick="this.parentElement.remove()">×</button>
      `;
      container.appendChild(toast);
      setTimeout(() => toast.classList.add('vp-toast--out'), duration - 300);
      setTimeout(() => toast.remove(), duration);
    },

    async withLoading(el, promise) {
      const original = el.innerHTML;
      el.innerHTML = '<span class="vp-spinner"></span>';
      el.style.pointerEvents = 'none';
      try { return await promise; }
      finally { el.innerHTML = original; el.style.pointerEvents = ''; }
    },

    currency(amount, currency = 'USD') {
      return new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(amount);
    },

    date(iso) {
      if (!iso) return '—';
      return new Date(iso).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }
  };

  /* ─────────────────────────────────────────────
   * 8. i18n
   * ───────────────────────────────────────────── */
  const i18n = {
    get current() { return localStorage.getItem('vp_lang') || 'en'; },
    set(lang) {
      localStorage.setItem('vp_lang', lang);
      document.documentElement.lang = lang;
      document.documentElement.dir  = lang === 'ar' ? 'rtl' : 'ltr';
      document.dispatchEvent(new CustomEvent('vp:langchange', { detail: lang }));
    },
    toggle() { this.set(this.current === 'en' ? 'ar' : 'en'); }
  };
  i18n.set(i18n.current);

  /* ─────────────────────────────────────────────
   * 9. Error handler
   * ───────────────────────────────────────────── */
  function handleApiError(err, context = 'API') {
    console.error('[VostokAPI] ' + context + ':', err);
    const msg = err.code === 401 ? 'Session expired — please sign in again.' : (err.message || 'Unexpected error.');
    ui.toast(context + ' Error', msg, 'error');
    if (err.code === 401) setTimeout(() => { window.location.href = 'login.php'; }, 2000);
  }

  /* ─────────────────────────────────────────────
   * 10. Public surface
   * ───────────────────────────────────────────── */
  global.VostokAPI = { crm, shop, customer, intranet, ui, i18n, call, escHtml, handleApiError, BASE };
  console.info('[VostokAPI] Client ready. Base:', BASE);

})(window);
