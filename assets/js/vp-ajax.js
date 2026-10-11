/**
 * VOSTOKPRIBOR Universal AJAX & Telemetry Helper (vp-ajax.js)
 * Enterprise-grade fetch wrapper, CSRF injection, DOM escaping, debouncing, polling, and ARIA toasts.
 * Specification: Phase 5.1
 */

(function (window, document) {
  'use strict';

  // 1. HTML Escaping Utility (XSS Prevention)
  function vpEscape(str) {
    if (str === null || str === undefined) return '';
    const s = String(str);
    const map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;',
      '`': '&#96;'
    };
    return s.replace(/[&<>"'`]/g, (char) => map[char]);
  }

  // 2. ARIA Live Region Toast Notification System
  let toastContainer = null;

  function ensureToastContainer() {
    if (!toastContainer || !document.body.contains(toastContainer)) {
      toastContainer = document.getElementById('vp-toast-live-region');
      if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'vp-toast-live-region';
        toastContainer.setAttribute('aria-live', 'polite');
        toastContainer.setAttribute('aria-atomic', 'true');
        toastContainer.className = 'vp-toast-container';
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

  function vpToast(message, type = 'info', duration = 4000) {
    const container = ensureToastContainer();
    const toast = document.createElement('div');
    toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
    toast.className = `vp-toast vp-toast-${type}`;

    const colors = {
      success: { bg: '#064e3b', border: '#059669', text: '#ecfdf5', icon: 'check_circle' },
      error: { bg: '#7f1d1d', border: '#dc2626', text: '#fef2f2', icon: 'error' },
      warning: { bg: '#78350f', border: '#d97706', text: '#fffbeb', icon: 'warning' },
      info: { bg: '#0f2438', border: '#1B3A5C', text: '#e2e8f0', icon: 'info' }
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
      animation: vpToastIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
      opacity: 0;
      transform: translateY(10px);
    `;

    toast.innerHTML = `
      <span class="material-symbols-outlined" style="font-size: 20px; flex-shrink: 0;">${scheme.icon}</span>
      <span style="flex: 1; word-break: break-word;">${vpEscape(message)}</span>
      <button type="button" style="background: none; border: none; color: inherit; cursor: pointer; padding: 2px; opacity: 0.7;" aria-label="Dismiss">
        <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
      </button>
    `;

    const closeBtn = toast.querySelector('button');
    const dismiss = () => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(10px)';
      toast.style.transition = 'all 0.2s ease-in';
      setTimeout(() => {
        if (toast.parentNode) toast.parentNode.removeChild(toast);
      }, 200);
    };

    closeBtn.addEventListener('click', dismiss);
    container.appendChild(toast);

    // Fade in
    requestAnimationFrame(() => {
      toast.style.opacity = '1';
      toast.style.transform = 'translateY(0)';
      toast.style.transition = 'all 0.25s cubic-bezier(0.16, 1, 0.3, 1)';
    });

    if (duration > 0) {
      setTimeout(dismiss, duration);
    }
  }

  // 3. Debounce Helper
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

  // 4. Polling Helper with Visibility Awareness & Exponential Backoff
  function vpPoll(fn, intervalMs = 15000, options = {}) {
    const pauseWhenHidden = options.pauseWhenHidden !== false;
    const backoff = options.backoff !== false;

    let timer = null;
    let stopped = false;
    let currentInterval = intervalMs;
    let consecutiveFailures = 0;

    async function tick() {
      if (stopped) return;
      if (pauseWhenHidden && document.hidden) {
        // Wait until document visible
        return;
      }

      try {
        await fn();
        consecutiveFailures = 0;
        currentInterval = intervalMs;
      } catch (err) {
        consecutiveFailures++;
        if (backoff) {
          // Exponential backoff up to 60s
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
      document.addEventListener('visibilitychange', onVisibilityChange);
    }

    // Start immediately
    tick();

    return {
      stop() {
        stopped = true;
        if (timer) clearTimeout(timer);
        if (pauseWhenHidden) {
          document.removeEventListener('visibilitychange', onVisibilityChange);
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

  // 5. CSRF Token Resolver
  function getCsrfToken() {
    // 1. Meta tag
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta && meta.getAttribute('content')) {
      return meta.getAttribute('content');
    }
    // 2. Cookie
    const match = document.cookie.match(/(^|;)\s*vostok_csrf_token\s*=\s*([^;]+)/);
    if (match) {
      return decodeURIComponent(match[2]);
    }
    // 3. Window variable
    if (window.VOSTOK_CSRF_TOKEN) {
      return window.VOSTOK_CSRF_TOKEN;
    }
    return '';
  }

  // 6. Unified Enterprise Fetch Wrapper
  async function vpFetch(url, options = {}) {
    const method = (options.method || 'GET').toUpperCase();
    const isMutating = ['POST', 'PUT', 'PATCH', 'DELETE'].includes(method);
    const timeoutMs = options.timeout || 15000;
    const button = options.button || null;

    // Double-submit protection on button
    let originalBtnHtml = '';
    let btnDisabledPreviously = false;
    if (button && button instanceof HTMLElement) {
      btnDisabledPreviously = button.disabled;
      button.disabled = true;
      button.setAttribute('aria-busy', 'true');
      originalBtnHtml = button.innerHTML;
      button.innerHTML = `<span class="material-symbols-outlined" style="animation: spin 1s linear infinite; font-size: 16px; vertical-align: middle;">progress_activity</span> Processing...`;
    }

    function restoreButton() {
      if (button && button instanceof HTMLElement) {
        button.disabled = btnDisabledPreviously;
        button.removeAttribute('aria-busy');
        button.innerHTML = originalBtnHtml;
      }
    }

    // Set up headers
    const headers = new Headers(options.headers || {});
    if (!headers.has('X-Requested-With')) {
      headers.set('X-Requested-With', 'XMLHttpRequest');
    }

    // Auto-inject CSRF for mutating requests
    if (isMutating && !headers.has('X-CSRF-Token')) {
      const csrf = getCsrfToken();
      if (csrf) {
        headers.set('X-CSRF-Token', csrf);
      }
    }

    // Prepare body
    let body = options.body;
    if (options.json !== undefined) {
      if (!headers.has('Content-Type')) {
        headers.set('Content-Type', 'application/json');
      }
      body = JSON.stringify(options.json);
    }

    // Timeout via AbortController
    const controller = new AbortController();
    const timeoutTimer = setTimeout(() => {
      controller.abort();
    }, timeoutMs);

    // Merge external signal if provided
    if (options.signal) {
      options.signal.addEventListener('abort', () => controller.abort());
    }

    let response;
    let retries = options.retries !== undefined ? options.retries : (method === 'GET' ? 2 : 0);

    while (true) {
      try {
        response = await fetch(url, {
          ...options,
          method,
          headers,
          body,
          signal: controller.signal
        });
        clearTimeout(timeoutTimer);
        break;
      } catch (err) {
        if (err.name === 'AbortError') {
          restoreButton();
          vpToast('Request timed out. Please check your connection.', 'error');
          throw new Error('Request timed out after ' + (timeoutMs / 1000) + 's');
        }

        if (retries > 0) {
          retries--;
          await new Promise((r) => setTimeout(r, 600));
          continue;
        }

        clearTimeout(timeoutTimer);
        restoreButton();
        vpToast('Network connection failed. Please retry.', 'error');
        throw err;
      }
    }

    restoreButton();

    // Parse response
    let data;
    const contentType = response.headers.get('content-type') || '';
    if (contentType.includes('application/json')) {
      try {
        data = await response.json();
      } catch (e) {
        data = { error: 'Invalid JSON response from server' };
      }
    } else {
      const text = await response.text();
      data = { text };
    }

    // Unified Status Handling
    if (response.status === 401) {
      vpToast('Session expired. Redirecting to login...', 'warning', 2500);
      setTimeout(() => {
        const loginUrl = options.loginRedirect || 'login.php';
        window.location.href = loginUrl;
      }, 1500);
      const err = new Error('Unauthorized');
      err.status = 401;
      err.data = data;
      throw err;
    }

    if (response.status === 403) {
      const msg = data.error || data.message || 'Access denied by system security policy.';
      vpToast(msg, 'error');
      const err = new Error(msg);
      err.status = 403;
      err.data = data;
      throw err;
    }

    if (response.status === 404) {
      const msg = data.error || data.message || 'The requested resource was not found.';
      vpToast(msg, 'warning');
      const err = new Error(msg);
      err.status = 404;
      err.data = data;
      throw err;
    }

    if (response.status === 409 || response.status === 422) {
      const msg = data.error || data.message || 'Request conflict or validation failed.';
      vpToast(msg, 'warning');
      const err = new Error(msg);
      err.status = response.status;
      err.data = data;
      throw err;
    }

    if (response.status === 429) {
      const msg = data.error || 'Rate limit reached. Please slow down and try again.';
      vpToast(msg, 'warning');
      const err = new Error(msg);
      err.status = 429;
      err.data = data;
      throw err;
    }

    if (response.status >= 500) {
      const msg = 'A server error occurred. Please contact IT support if this persists.';
      vpToast(msg, 'error');
      const err = new Error(msg);
      err.status = response.status;
      err.data = data;
      throw err;
    }

    return data;
  }

  // Export to global scope
  window.vpEscape = vpEscape;
  window.vpToast = vpToast;
  window.vpDebounce = vpDebounce;
  window.vpPoll = vpPoll;
  window.vpFetch = vpFetch;
  window.vpAjax = {
    vpEscape,
    vpToast,
    vpDebounce,
    vpPoll,
    vpFetch
  };
})(window, document);
