/**
 * VOSTOKPRIBOR Enterprise Platform — Core Transport & Inter-Module Bus
 * Provides foundational HTTP transport, UI utilities, i18n, and
 * the Inter-Module Communication Bus (VostokBus) for cross-system data sharing.
 *
 * Exposes:
 *   - window.VostokCore
 *   - window.VostokBus
 *   - window.VostokAPI (base namespace)
 */

(function (global) {
  "use strict";

  /* ──────────────────────────────────────────────────────────────────────────
   * 1. Configuration & Endpoint Base Resolution
   * ────────────────────────────────────────────────────────────────────────── */
  const BASE = (function () {
    if (typeof document !== "undefined" && document.currentScript && document.currentScript.src) {
      try {
        const u = new URL("../../api/v1", document.currentScript.src);
        return u.pathname.endsWith("/") ? u.pathname.slice(0, -1) : u.pathname;
      } catch (e) {}
    }
    const p = window.location.pathname;
    const knownSystems = [
      "Admin & Governance Portal", "CRM", "Customer Portal", "Developer",
      "Employee Intranet", "File Center", "Finance & Billing", "HR System",
      "IT Helpdesk", "Online Shop B2B", "VOSTOKPRIBOR Corporate Web Platform"
    ];
    for (const sys of knownSystems) {
      const idx = p.indexOf("/" + sys);
      if (idx !== -1) {
        return p.substring(0, idx) + "/api/v1";
      }
    }
    return "../api/v1";
  })();

  const DEFAULT_LANG = localStorage.getItem("vp_lang") || "en";

  /* ──────────────────────────────────────────────────────────────────────────
   * 2. CSRF Token Resolution & Interceptors
   * ────────────────────────────────────────────────────────────────────────── */
  function getCsrfToken() {
    if (global.__CSRF_TOKEN__) return global.__CSRF_TOKEN__;
    if (typeof document !== "undefined") {
      const meta = document.querySelector('meta[name="csrf-token"]');
      if (meta && meta.content) {
        global.__CSRF_TOKEN__ = meta.content;
        return meta.content;
      }
    }
    try {
      const stored = sessionStorage.getItem("vostok_csrf_token") || localStorage.getItem("vostok_csrf_token");
      if (stored) {
        global.__CSRF_TOKEN__ = stored;
        return stored;
      }
    } catch (e) {}
    return "";
  }

  async function ensureCsrfToken() {
    let token = getCsrfToken();
    if (token) return token;
    try {
      const authEndpoint = BASE.replace(/\/api\/v1$/, "") + "/api/check_auth.php";
      const checkRes = await originalFetch(authEndpoint, {
        credentials: "include"
      });
      if (checkRes.ok) {
        const data = await checkRes.json();
        if (data.csrf_token) {
          global.__CSRF_TOKEN__ = data.csrf_token;
          try { sessionStorage.setItem("vostok_csrf_token", data.csrf_token); } catch(e) {}
          return data.csrf_token;
        }
      }
    } catch (e) {
      console.warn("[VostokCore] Could not auto-resolve CSRF token:", e);
    }
    return "";
  }

  // Intercept global window.fetch to automatically add X-CSRF-Token on non-GET
  const originalFetch = global.fetch;
  if (typeof originalFetch === "function") {
    global.fetch = async function (input, init = {}) {
      let method = "GET";
      if (init && init.method) {
        method = String(init.method).toUpperCase();
      } else if (typeof Request !== "undefined" && input instanceof Request && input.method) {
        method = input.method.toUpperCase();
      }

      if (method !== "GET" && method !== "HEAD" && method !== "OPTIONS") {
        let token = getCsrfToken();
        if (!token) {
          token = await ensureCsrfToken();
        }
        if (token) {
          if (typeof Request !== "undefined" && input instanceof Request) {
            try { input.headers.set("X-CSRF-Token", token); } catch (e) {}
          } else {
            init = init || {};
            if (!init.headers) {
              init.headers = { "X-CSRF-Token": token };
            } else if (typeof Headers !== "undefined" && init.headers instanceof Headers) {
              if (!init.headers.has("X-CSRF-Token")) init.headers.set("X-CSRF-Token", token);
            } else if (Array.isArray(init.headers)) {
              init.headers.push(["X-CSRF-Token", token]);
            } else if (typeof init.headers === "object") {
              if (!init.headers["X-CSRF-Token"]) init.headers["X-CSRF-Token"] = token;
            }
          }
        }
      }
      return originalFetch.call(this, input, init);
    };
  }

  // Intercept XMLHttpRequest
  if (global.XMLHttpRequest) {
    const origOpen = global.XMLHttpRequest.prototype.open;
    const origSend = global.XMLHttpRequest.prototype.send;
    global.XMLHttpRequest.prototype.open = function (method, url, async, user, password) {
      this._vpMethod = (method || "GET").toUpperCase();
      return origOpen.apply(this, arguments);
    };
    global.XMLHttpRequest.prototype.send = function (body) {
      if (this._vpMethod && this._vpMethod !== "GET" && this._vpMethod !== "HEAD" && this._vpMethod !== "OPTIONS") {
        const token = getCsrfToken();
        if (token) {
          try { this.setRequestHeader("X-CSRF-Token", token); } catch (e) {}
        }
      }
      return origSend.apply(this, arguments);
    };
  }

  // Intercept standard HTML form POST submissions
  if (typeof document !== "undefined") {
    document.addEventListener("submit", function (e) {
      const form = e.target;
      if (!form || !form.method || form.method.toUpperCase() !== "POST") return;
      const token = getCsrfToken();
      if (!token) return;
      let input = form.querySelector('input[name="csrf_token"]');
      if (!input) {
        input = document.createElement("input");
        input.type = "hidden";
        input.name = "csrf_token";
        input.value = token;
        form.appendChild(input);
      } else if (!input.value) {
        input.value = token;
      }
    }, true);
  }

  /* ──────────────────────────────────────────────────────────────────────────
   * 3. Core Fetch Transport Wrapper
   * ────────────────────────────────────────────────────────────────────────── */
  /**
   * Universal fetch transport
   * @param {string} endpoint  - relative to BASE e.g. '/crm/customers.php'
   * @param {object} [options] - fetch options
   * @param {object} [options.params] - URL query params
   * @param {string} [options.method] - HTTP method (GET, POST, PUT, PATCH, DELETE)
   * @param {object} [options.body]   - Payload for POST/PUT/PATCH
   * @returns {Promise<{success: boolean, data: any, meta?: object, message?: string}>}
   */
  async function call(endpoint, options = {}) {
    const { params = {}, method = "GET", body = null } = options;

    params.lang = params.lang || DEFAULT_LANG;

    const url = new URL(BASE + endpoint, window.location.href);
    Object.entries(params).forEach(([k, v]) => {
      if (v !== null && v !== undefined) url.searchParams.set(k, v);
    });

    const fetchOpts = {
      method,
      credentials: "include", // Send session cookies for auth
      headers: { Accept: "application/json" },
    };

    const upperMethod = (method || "GET").toUpperCase();
    if (upperMethod !== "GET" && upperMethod !== "HEAD" && upperMethod !== "OPTIONS") {
      const csrf = getCsrfToken() || await ensureCsrfToken();
      if (csrf) {
        fetchOpts.headers["X-CSRF-Token"] = csrf;
      }
    }

    if (body && upperMethod !== "GET") {
      fetchOpts.headers["Content-Type"] = "application/json";
      fetchOpts.body = JSON.stringify(body);
    }

    const res = await fetch(url.toString(), fetchOpts);
    let json;
    try {
      json = await res.json();
    } catch (parseErr) {
      throw Object.assign(new Error(`Server returned non-JSON response (HTTP ${res.status})`), {
        code: res.status,
        raw: res
      });
    }

    if (!res.ok || !json.success) {
      const msg = json.message || json.error || `HTTP ${res.status}`;
      throw Object.assign(new Error(msg), { code: res.status, response: json });
    }

    return json;
  }

  /* ──────────────────────────────────────────────────────────────────────────
   * 4. UI Helpers & Utilities
   * ────────────────────────────────────────────────────────────────────────── */
  function escHtml(str) {
    return String(str ?? "").replace(
      /[&<>"']/g,
      (c) =>
        ({
          "&": "&amp;",
          "<": "&lt;",
          ">": "&gt;",
          '"': "&quot;",
          "'": "&#39;",
        })[c]
    );
  }

  const ui = {
    toast(title, message, type = "info", duration = 4000) {
      let container = document.getElementById("toast-container");
      if (!container) {
        container = document.createElement("div");
        container.id = "toast-container";
        document.body.appendChild(container);
      }
      const toast = document.createElement("div");
      toast.className = "vp-toast vp-toast--" + type;
      toast.innerHTML = `
        <div class="vp-toast__icon">${{ success: "✔", error: "✖", warning: "⚠", info: "ℹ" }[type] || "ℹ"}</div>
        <div class="vp-toast__body"><strong>${escHtml(title)}</strong><span>${escHtml(message)}</span></div>
        <button class="vp-toast__close" onclick="this.parentElement.remove()">×</button>
      `;
      container.appendChild(toast);
      setTimeout(() => toast.classList.add("vp-toast--out"), Math.max(duration - 300, 100));
      setTimeout(() => toast.remove(), duration);
    },

    async withLoading(el, promise) {
      if (!el) return await promise;
      const original = el.innerHTML;
      el.innerHTML = '<span class="vp-spinner"></span>';
      el.style.pointerEvents = "none";
      try {
        return await promise;
      } finally {
        el.innerHTML = original;
        el.style.pointerEvents = "";
      }
    },

    currency(amount, currency = "USD") {
      const num = parseFloat(amount) || 0;
      return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency,
      }).format(num);
    },

    date(iso) {
      if (!iso) return "—";
      return new Date(iso).toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "short",
        year: "numeric",
      });
    },
  };

  /* ──────────────────────────────────────────────────────────────────────────
   * 4. Internationalization (i18n)
   * ────────────────────────────────────────────────────────────────────────── */
  const i18n = {
    get current() {
      return localStorage.getItem("vp_lang") || "en";
    },
    set(lang) {
      localStorage.setItem("vp_lang", lang);
      document.documentElement.lang = lang;
      document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
      document.dispatchEvent(new CustomEvent("vp:langchange", { detail: lang }));
    },
    toggle() {
      this.set(this.current === "en" ? "ar" : "en");
    },
  };

  /* ──────────────────────────────────────────────────────────────────────────
   * 5. Error Handler
   * ────────────────────────────────────────────────────────────────────────── */
  function handleApiError(err, context = "API") {
    console.error(`[VostokCore] ${context} error:`, err);
    const msg =
      err.code === 401
        ? "Session validation notice: " + (err.message || "access restricted")
        : err.message || "An unexpected error occurred.";
    ui.toast(context, msg, err.code === 401 ? "warning" : "error");
    if (err.code === 401 && (context === "Auth" || context === "Session" || context === "Login")) {
      setTimeout(() => {
        window.location.href = "login.php";
      }, 2000);
    }
  }

  /* ──────────────────────────────────────────────────────────────────────────
   * 6. Inter-Module Event & Data Bus (VostokBus)
   * ──────────────────────────────────────────────────────────────────────────
   * Enables isolated subsystems (CRM, B2B Shop, Customer Portal, Intranet)
   * to securely publish/subscribe events and query cross-system resources.
   */
  const _modules = new Map();
  const _listeners = new Map();
  const _exchangeLog = [];

  // Default fallback endpoint routes for remote cross-module RPC calls
  const _routeMap = {
    crm: {
      customers: "/crm/customers.php",
      customer: "/crm/customers.php",
      leads: "/crm/leads.php",
      opportunities: "/crm/opportunities.php",
      forecasts: "/crm/forecasts.php",
    },
    shop: {
      products: "/shop/products.php",
      orders: "/shop/orders.php",
      quotes: "/shop/quotes.php",
    },
    customer: {
      dashboard: "/customer/dashboard.php",
      projects: "/customer/projects.php",
      invoices: "/customer/invoices.php",
      tickets: "/customer/tickets.php",
    },
    intranet: {
      announcements: "/intranet/announcements.php",
      directory: "/intranet/directory.php",
      leaves: "/intranet/leaves.php",
    },
  };

  const VostokBus = {
    /**
     * Register an isolated API module
     * @param {string} moduleName - 'crm', 'shop', 'customer', 'intranet'
     * @param {object} apiInstance - the module's client instance
     */
    register(moduleName, apiInstance) {
      const name = moduleName.toLowerCase();
      _modules.set(name, apiInstance);
      console.info(`[VostokBus] Module registered: '${name}'`);

      // Ensure window.VostokAPI exposes the registered module
      if (!global.VostokAPI) global.VostokAPI = {};
      global.VostokAPI[name] = apiInstance;

      // Broadcast registration event
      this.emit(`module:registered`, { module: name });
      return this;
    },

    /**
     * Check if a module is registered locally
     */
    hasModule(moduleName) {
      return _modules.has(moduleName.toLowerCase());
    },

    /**
     * Retrieve registered module client
     */
    getModule(moduleName) {
      return _modules.get(moduleName.toLowerCase()) || null;
    },

    /**
     * Cross-Module Remote Request / RPC
     * Dispatches action to local module instance if present,
     * or invokes the underlying endpoint through the transport gateway.
     *
     * @param {string} targetModule - 'crm' | 'shop' | 'customer' | 'intranet'
     * @param {string} action       - method name e.g. 'orders', 'customers', 'directory'
     * @param {object} [params]     - query parameters or arguments
     * @param {object} [options]    - extra transport options (method, body, etc.)
     */
    async request(targetModule, action, params = {}, options = {}) {
      const modName = targetModule.toLowerCase();
      const startTime = performance.now();
      const logEntry = {
        from: (document.title || "Module").split("·")[0].trim(),
        to: modName,
        action,
        timestamp: new Date().toISOString(),
        status: "PENDING"
      };

      try {
        let result;
        const localMod = this.getModule(modName);

        if (localMod && typeof localMod[action] === "function") {
          // Invoke locally registered module
          result = await localMod[action](params, options);
        } else {
          // Fallback to route mapping via core transport
          const ep = _routeMap[modName]?.[action];
          if (!ep) {
            throw new Error(`Unknown cross-module action '${action}' for target '${targetModule}'`);
          }
          result = await call(ep, {
            params,
            method: options.method || "GET",
            body: options.body || null,
          });
        }

        logEntry.status = "SUCCESS";
        logEntry.durationMs = Math.round(performance.now() - startTime);
        _exchangeLog.unshift(logEntry);
        if (_exchangeLog.length > 50) _exchangeLog.pop();

        this.emit("intermodule:exchange", logEntry);
        return result;
      } catch (err) {
        logEntry.status = "FAILED";
        logEntry.error = err.message;
        logEntry.durationMs = Math.round(performance.now() - startTime);
        _exchangeLog.unshift(logEntry);

        this.emit("intermodule:error", logEntry);
        throw err;
      }
    },

    /**
     * Subscribe to an inter-module event
     */
    on(event, handler) {
      if (!_listeners.has(event)) _listeners.set(event, new Set());
      _listeners.get(event).add(handler);
      return () => this.off(event, handler);
    },

    /**
     * Subscribe once
     */
    once(event, handler) {
      const wrapper = (data) => {
        this.off(event, wrapper);
        handler(data);
      };
      return this.on(event, wrapper);
    },

    /**
     * Unsubscribe
     */
    off(event, handler) {
      if (_listeners.has(event)) {
        _listeners.get(event).delete(handler);
      }
      return this;
    },

    /**
     * Publish an event across all modules
     */
    emit(event, data) {
      if (_listeners.has(event)) {
        _listeners.get(event).forEach((fn) => {
          try {
            fn(data);
          } catch (err) {
            console.error(`[VostokBus] Error in listener for '${event}':`, err);
          }
        });
      }
      return this;
    },

    /**
     * Get inter-module communication history
     */
    getHistory() {
      return [..._exchangeLog];
    }
  };

  /* ──────────────────────────────────────────────────────────────────────────
   * 7. Namespace Export
   * ────────────────────────────────────────────────────────────────────────── */
  const VostokCore = {
    BASE,
    call,
    getCsrfToken,
    ensureCsrfToken,
    escHtml,
    ui,
    i18n,
    handleApiError,
    bus: VostokBus,
  };

  global.VostokCore = VostokCore;
  global.VostokBus = VostokBus;

  // Mount on legacy/unified VostokAPI namespace
  global.VostokAPI = global.VostokAPI || {};
  Object.assign(global.VostokAPI, {
    ui,
    i18n,
    call,
    getCsrfToken,
    ensureCsrfToken,
    escHtml,
    handleApiError,
    BASE,
    bus: VostokBus,
  });

  console.info("[VostokCore] Core Transport & Inter-Module Bus Initialized. Endpoint:", BASE);
})(window);
