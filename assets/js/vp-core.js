/**
 * VOSTOKPRIBOR Enterprise Platform — Unified Core Client Runtime (vp-core.js)
 * Merges and supercedes api-core.js and vp-ajax.js.
 *
 * Core Capabilities:
 *  1. Universal CSRF Auto-Injection for same-origin mutating fetch & XHR.
 *  2. Transparent 403 CSRF token refresh (GET api/csrf.php) with single retry.
 *  3. Safe FormData & Multipart upload handling (preserves boundary headers).
 *  4. Universal HTML Escaping (vpEscape), ARIA Live Toasts (vpToast), and Debounce (vpDebounce).
 *  5. Enterprise fetch wrapper (vpFetch) with timeout aborts and double-submit locks.
 *  6. Cross-Module Inter-System Bus (VostokBus) and legacy VostokCore / VostokAPI compatibility.
 */

(function (global) {
  "use strict";

  const document = global.document;

  /* ──────────────────────────────────────────────────────────────────────────
   * 1. Path & Endpoint Resolution
   * ────────────────────────────────────────────────────────────────────────── */
  const KNOWN_SYSTEMS = [
    "Admin & Governance Portal", "CRM", "Customer Portal", "Developer",
    "Employee Intranet", "File Center", "Finance & Billing", "HR System",
    "IT Helpdesk", "Online Shop B2B", "VOSTOKPRIBOR Corporate Web Platform", "account"
  ];

  function getAppRoot() {
    const rawPath = (global.location && global.location.pathname) ? global.location.pathname : "/";
    let p = rawPath;
    try {
      p = decodeURIComponent(rawPath);
    } catch (e) {}

    for (const sys of KNOWN_SYSTEMS) {
      const idx = p.indexOf("/" + sys);
      if (idx !== -1) {
        return p.substring(0, idx);
      }
    }
    const lastSlash = p.lastIndexOf("/");
    if (lastSlash > 0) {
      return p.substring(0, lastSlash);
    }
    return "";
  }

  function getCsrfEndpoint() {
    const root = getAppRoot();
    return (root ? root : "") + "/api/csrf.php";
  }

  const API_V1_BASE = getAppRoot() + "/api/v1";

  /* ──────────────────────────────────────────────────────────────────────────
   * 2. CSRF Token Management & Transparent Refresh
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

    // Match cookie fallback if available
    if (typeof document !== "undefined" && document.cookie) {
      const match = document.cookie.match(/(^|;)\s*vostok_csrf_token\s*=\s*([^;]+)/);
      if (match) {
        const val = decodeURIComponent(match[2]);
        global.__CSRF_TOKEN__ = val;
        return val;
      }
    }

    return "";
  }

  function setCsrfToken(token) {
    if (!token) return;
    global.__CSRF_TOKEN__ = token;
    if (typeof document !== "undefined") {
      let meta = document.querySelector('meta[name="csrf-token"]');
      if (!meta) {
        meta = document.createElement("meta");
        meta.name = "csrf-token";
        if (document.head) document.head.appendChild(meta);
      }
      if (meta) meta.setAttribute("content", token);
    }
    try {
      sessionStorage.setItem("vostok_csrf_token", token);
    } catch (e) {}
  }

  let refreshPromise = null;
  async function refreshCsrfToken() {
    if (refreshPromise) return refreshPromise;
    refreshPromise = (async () => {
      try {
        const res = await originalFetch.call(global, getCsrfEndpoint() + "?rotate=1", {
          method: "GET",
          credentials: "include",
          headers: { "Accept": "application/json" }
        });
        if (res.ok) {
          const data = await res.json();
          if (data && data.csrf_token) {
            setCsrfToken(data.csrf_token);
            return data.csrf_token;
          }
        }
      } catch (err) {
        console.warn("[vp-core] CSRF token refresh network failure:", err);
      } finally {
        refreshPromise = null;
      }
      return getCsrfToken();
    })();
    return refreshPromise;
  }

  async function ensureCsrfToken() {
    let token = getCsrfToken();
    if (token) return token;
    return await refreshCsrfToken();
  }

  function isSameOrigin(url) {
    try {
      const u = new URL(url, global.location.href);
      return u.origin === global.location.origin;
    } catch (e) {
      return true; // Relative URLs belong to current origin
    }
  }

  /* ──────────────────────────────────────────────────────────────────────────
   * 3. Fetch & XHR Interceptors
   * ────────────────────────────────────────────────────────────────────────── */
  const originalFetch = global.fetch;

  if (typeof originalFetch === "function") {
    global.fetch = async function (input, init = {}) {
      let method = "GET";
      let url = "";

      if (typeof input === "string") {
        url = input;
      } else if (input instanceof URL) {
        url = input.toString();
      } else if (typeof Request !== "undefined" && input instanceof Request) {
        url = input.url;
        method = input.method.toUpperCase();
      }

      if (init && init.method) {
        method = String(init.method).toUpperCase();
      }

      const isMutating = ["POST", "PUT", "PATCH", "DELETE"].includes(method);
      const sameOrigin = isSameOrigin(url);

      if (sameOrigin && isMutating) {
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
              init.headers["X-CSRF-Token"] = token;
            }
          }
        }
      }

      let res = await originalFetch.call(this, input, init);

      // Handle 403 CSRF rejection with single refresh and retry
      if (res.status === 403 && sameOrigin && isMutating && (!init || !init._csrfRetried)) {
        try {
          const clone = res.clone();
          const text = await clone.text();
          if (/csrf/i.test(text)) {
            console.warn("[vp-core] CSRF token rejected (403). Refreshing token and retrying request...");
            const newToken = await refreshCsrfToken();
            if (newToken) {
              const retryInit = Object.assign({}, init, { _csrfRetried: true });
              if (retryInit.headers instanceof Headers) {
                retryInit.headers.set("X-CSRF-Token", newToken);
              } else if (Array.isArray(retryInit.headers)) {
                const idx = retryInit.headers.findIndex(([k]) => k.toLowerCase() === "x-csrf-token");
                if (idx !== -1) retryInit.headers[idx][1] = newToken;
                else retryInit.headers.push(["X-CSRF-Token", newToken]);
              } else if (typeof retryInit.headers === "object") {
                retryInit.headers["X-CSRF-Token"] = newToken;
              }
              return await originalFetch.call(this, input, retryInit);
            }
          }
        } catch (e) {
          console.warn("[vp-core] CSRF retry check failed:", e);
        }
      }

      return res;
    };
  }

  // Intercept XMLHttpRequest
  if (global.XMLHttpRequest) {
    const origOpen = global.XMLHttpRequest.prototype.open;
    const origSend = global.XMLHttpRequest.prototype.send;
    const origSetHeader = global.XMLHttpRequest.prototype.setRequestHeader;

    global.XMLHttpRequest.prototype.open = function (method, url) {
      this._vpMethod = (method || "GET").toUpperCase();
      this._vpUrl = url;
      this._vpHeaders = {};
      return origOpen.apply(this, arguments);
    };

    global.XMLHttpRequest.prototype.setRequestHeader = function (header, value) {
      if (!this._vpHeaders) this._vpHeaders = {};
      this._vpHeaders[header.toLowerCase()] = value;
      return origSetHeader.apply(this, arguments);
    };

    global.XMLHttpRequest.prototype.send = function (body) {
      const isMutating = ["POST", "PUT", "PATCH", "DELETE"].includes(this._vpMethod);
      if (isMutating && isSameOrigin(this._vpUrl)) {
        if (!this._vpHeaders || !this._vpHeaders["x-csrf-token"]) {
          const token = getCsrfToken();
          if (token) {
            try {
              origSetHeader.call(this, "X-CSRF-Token", token);
            } catch (e) {}
          }
        }
      }
      return origSend.apply(this, arguments);
    };
  }

  // Intercept traditional standard HTML form POST submissions
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
   * 4. HTML Escaping Utility (XSS Prevention)
   * ────────────────────────────────────────────────────────────────────────── */
  function vpEscape(str) {
    if (str === null || str === undefined) return "";
    const s = String(str);
    const map = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;",
      "`": "&#96;"
    };
    return s.replace(/[&<>"'`]/g, (char) => map[char]);
  }

  /* ──────────────────────────────────────────────────────────────────────────
   * 5. ARIA Live Region Toast Notification System
   * ────────────────────────────────────────────────────────────────────────── */
  let toastContainer = null;

  function ensureToastContainer() {
    if (!toastContainer || !document.body.contains(toastContainer)) {
      toastContainer = document.getElementById("vp-toast-live-region");
      if (!toastContainer) {
        toastContainer = document.createElement("div");
        toastContainer.id = "vp-toast-live-region";
        toastContainer.setAttribute("aria-live", "polite");
        toastContainer.setAttribute("aria-atomic", "true");
        toastContainer.className = "vp-toast-container";
        toastContainer.style.cssText = `
          position: fixed;
          bottom: 24px;
          right: 24px;
          z-index: 999999;
          display: flex;
          flex-direction: column;
          gap: 10px;
          max-width: 400px;
          width: calc(100vw - 48px);
          pointer-events: none;
        `;
        document.body.appendChild(toastContainer);
      }
    }
    return toastContainer;
  }

  function vpToast(message, type = "info", duration = 4000) {
    if (!document || !document.body) return;
    const container = ensureToastContainer();
    const toast = document.createElement("div");
    toast.setAttribute("role", type === "error" ? "alert" : "status");
    toast.className = `vp-toast vp-toast-${type}`;

    const colors = {
      success: { bg: "#064e3b", border: "#059669", text: "#ecfdf5", icon: "check_circle" },
      error:   { bg: "#7f1d1d", border: "#dc2626", text: "#fef2f2", icon: "error" },
      warning: { bg: "#78350f", border: "#d97706", text: "#fffbeb", icon: "warning" },
      info:    { bg: "#0f2438", border: "#1B3A5C", text: "#e2e8f0", icon: "info" }
    };

    const scheme = colors[type] || colors.info;

    toast.style.cssText = `
      pointer-events: auto;
      background: ${scheme.bg};
      border: 1px solid ${scheme.border};
      color: ${scheme.text};
      padding: 12px 16px;
      border-radius: 8px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.5);
      display: flex;
      align-items: center;
      gap: 12px;
      font-family: 'IBM Plex Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      font-size: 13px;
      line-height: 1.4;
      opacity: 0;
      transform: translateY(10px);
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    `;

    toast.innerHTML = `
      <span class="material-symbols-outlined" style="font-size: 20px; flex-shrink: 0;">${scheme.icon}</span>
      <span style="flex: 1; word-break: break-word;">${vpEscape(message)}</span>
      <button type="button" style="background: none; border: none; color: inherit; cursor: pointer; padding: 2px; opacity: 0.7;" aria-label="Dismiss">
        <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
      </button>
    `;

    const closeBtn = toast.querySelector("button");
    const dismiss = () => {
      toast.style.opacity = "0";
      toast.style.transform = "translateY(10px)";
      setTimeout(() => {
        if (toast.parentNode) toast.parentNode.removeChild(toast);
      }, 200);
    };

    closeBtn.addEventListener("click", dismiss);
    container.appendChild(toast);

    requestAnimationFrame(() => {
      toast.style.opacity = "1";
      toast.style.transform = "translateY(0)";
    });

    if (duration > 0) {
      setTimeout(dismiss, duration);
    }
  }

  /* ──────────────────────────────────────────────────────────────────────────
   * 6. Debounce & Polling Utilities
   * ────────────────────────────────────────────────────────────────────────── */
  function vpDebounce(fn, wait = 300) {
    let timeoutId = null;
    return function (...args) {
      const context = this;
      if (timeoutId) clearTimeout(timeoutId);
      timeoutId = setTimeout(() => {
        fn.apply(context, args);
      }, wait);
    };
  }

  function vpPoll(fn, intervalMs = 15000, options = {}) {
    const pauseWhenHidden = options.pauseWhenHidden !== false;
    const backoff = options.backoff !== false;

    let timer = null;
    let stopped = false;
    let currentInterval = intervalMs;
    let consecutiveFailures = 0;

    async function tick() {
      if (stopped) return;
      if (pauseWhenHidden && document.hidden) return;

      try {
        await fn();
        consecutiveFailures = 0;
        currentInterval = intervalMs;
      } catch (err) {
        consecutiveFailures++;
        if (backoff) {
          currentInterval = Math.min(60000, intervalMs * Math.pow(1.5, consecutiveFailures));
        }
      } finally {
        if (!stopped) {
          timer = setTimeout(tick, currentInterval);
        }
      }
    }

    function onVisibilityChange() {
      if (!document.hidden && !stopped) {
        if (timer) clearTimeout(timer);
        tick();
      }
    }

    if (pauseWhenHidden) {
      document.addEventListener("visibilitychange", onVisibilityChange);
    }

    tick();

    return {
      stop() {
        stopped = true;
        if (timer) clearTimeout(timer);
        if (pauseWhenHidden) {
          document.removeEventListener("visibilitychange", onVisibilityChange);
        }
      },
      restart() {
        stopped = false;
        currentInterval = intervalMs;
        consecutiveFailures = 0;
        tick();
      }
    };
  }

  /* ──────────────────────────────────────────────────────────────────────────
   * 7. Unified Enterprise Fetch Wrapper (vpFetch)
   * ────────────────────────────────────────────────────────────────────────── */
  async function vpFetch(url, options = {}) {
    const method = (options.method || "GET").toUpperCase();
    const timeoutMs = options.timeout || 15000;
    const button = options.button || null;

    let originalBtnHtml = "";
    let btnDisabledPreviously = false;
    if (button && button instanceof HTMLElement) {
      btnDisabledPreviously = button.disabled;
      button.disabled = true;
      button.setAttribute("aria-busy", "true");
      originalBtnHtml = button.innerHTML;
      button.innerHTML = `<span class="material-symbols-outlined" style="animation: spin 1s linear infinite; font-size: 16px; vertical-align: middle;">progress_activity</span> Processing...`;
    }

    function restoreButton() {
      if (button && button instanceof HTMLElement) {
        button.disabled = btnDisabledPreviously;
        button.removeAttribute("aria-busy");
        button.innerHTML = originalBtnHtml;
      }
    }

    const headers = new Headers(options.headers || {});
    if (!headers.has("X-Requested-With")) {
      headers.set("X-Requested-With", "XMLHttpRequest");
    }

    let body = options.body;
    if (options.json !== undefined) {
      if (!headers.has("Content-Type")) {
        headers.set("Content-Type", "application/json");
      }
      body = JSON.stringify(options.json);
    }

    const controller = new AbortController();
    const timeoutTimer = setTimeout(() => controller.abort(), timeoutMs);
    if (options.signal) {
      options.signal.addEventListener("abort", () => controller.abort());
    }

    let response;
    try {
      response = await fetch(url, {
        ...options,
        method,
        headers,
        body,
        signal: controller.signal
      });
      clearTimeout(timeoutTimer);
    } catch (err) {
      clearTimeout(timeoutTimer);
      restoreButton();
      if (err.name === "AbortError") {
        vpToast("Request timed out. Please check your connection.", "error");
        throw new Error("Request timed out after " + (timeoutMs / 1000) + "s");
      }
      vpToast("Network connection failed. Please retry.", "error");
      throw err;
    }

    restoreButton();

    let data;
    const contentType = response.headers.get("content-type") || "";
    if (contentType.includes("application/json")) {
      try {
        data = await response.json();
      } catch (e) {
        data = { error: "Invalid JSON response from server" };
      }
    } else {
      const text = await response.text();
      data = { text };
    }

    if (response.status === 401) {
      vpToast("Session expired. Redirecting to login...", "warning", 2500);
      setTimeout(() => {
        window.location.href = options.loginRedirect || "login.php";
      }, 1500);
      const err = new Error("Unauthorized");
      err.status = 401;
      err.data = data;
      throw err;
    }

    if (response.status === 403) {
      const msg = data.error || data.message || "Access denied by security policy.";
      vpToast(msg, "error");
      const err = new Error(msg);
      err.status = 403;
      err.data = data;
      throw err;
    }

    if (!response.ok) {
      const msg = data.error || data.message || `Request failed with status ${response.status}`;
      if (!options.silent) vpToast(msg, "error");
      const err = new Error(msg);
      err.status = response.status;
      err.data = data;
      throw err;
    }

    return data;
  }

  /* ──────────────────────────────────────────────────────────────────────────
   * 8. Inter-Module Event Bus (VostokBus) & Legacy VostokCore
   * ────────────────────────────────────────────────────────────────────────── */
  const _modules = new Map();
  const _listeners = new Map();
  const _exchangeLog = [];

  const VostokBus = {
    register(moduleName, apiInstance) {
      const name = moduleName.toLowerCase();
      _modules.set(name, apiInstance);
      if (!global.VostokAPI) global.VostokAPI = {};
      global.VostokAPI[name] = apiInstance;
      this.emit("module:registered", { module: name });
      return this;
    },
    hasModule(moduleName) {
      return _modules.has(moduleName.toLowerCase());
    },
    getModule(moduleName) {
      return _modules.get(moduleName.toLowerCase()) || null;
    },
    on(event, handler) {
      if (!_listeners.has(event)) _listeners.set(event, new Set());
      _listeners.get(event).add(handler);
      return () => this.off(event, handler);
    },
    once(event, handler) {
      const wrapper = (data) => {
        this.off(event, wrapper);
        handler(data);
      };
      return this.on(event, wrapper);
    },
    off(event, handler) {
      if (_listeners.has(event)) {
        _listeners.get(event).delete(handler);
      }
      return this;
    },
    emit(event, data) {
      if (_listeners.has(event)) {
        _listeners.get(event).forEach((fn) => {
          try { fn(data); } catch (err) { console.error(`[VostokBus] Error in '${event}':`, err); }
        });
      }
      return this;
    },
    getHistory() {
      return [..._exchangeLog];
    }
  };

  const VostokCore = {
    BASE: API_V1_BASE,
    call: async function (endpoint, options = {}) {
      const url = new URL(API_V1_BASE + endpoint, global.location.href);
      return vpFetch(url.toString(), options);
    },
    getCsrfToken,
    ensureCsrfToken,
    refreshCsrfToken,
    escHtml: vpEscape,
    ui: {
      toast: (title, message, type = "info") => vpToast(title ? `${title}: ${message}` : message, type),
      currency: (amt, cur = "USD") => new Intl.NumberFormat("en-US", { style: "currency", currency: cur }).format(parseFloat(amt) || 0),
      date: (iso) => iso ? new Date(iso).toLocaleDateString("en-GB", { day: "2-digit", month: "short", year: "numeric" }) : "—"
    },
    bus: VostokBus
  };

  /* ──────────────────────────────────────────────────────────────────────────
   * 9. Global Exports & Backward Compatibility
   * ────────────────────────────────────────────────────────────────────────── */
  global.vpEscape = vpEscape;
  global.vpToast = vpToast;
  global.vpDebounce = vpDebounce;
  global.vpPoll = vpPoll;
  global.vpFetch = vpFetch;
  global.getCsrfToken = getCsrfToken;
  global.refreshCsrfToken = refreshCsrfToken;

  global.VostokCore = VostokCore;
  global.VostokBus = VostokBus;
  global.VostokAPI = global.VostokAPI || {};
  Object.assign(global.VostokAPI, {
    ui: VostokCore.ui,
    call: VostokCore.call,
    getCsrfToken,
    ensureCsrfToken,
    refreshCsrfToken,
    escHtml: vpEscape,
    BASE: API_V1_BASE,
    bus: VostokBus
  });

})(typeof window !== "undefined" ? window : global);
