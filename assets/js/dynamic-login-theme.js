/**
 * VOSTOKPRIBOR Dynamic Login Alert Color Engine
 * Dynamically shifts theme accents when authentication alerts are displayed.
 * Location: assets/js/dynamic-login-theme.js
 */

(function () {
  const SENTIMENT = {
    error: {
      accent: "#ef4444",
      hover: "#dc2626",
      glow: "rgba(239, 68, 68, 0.35)",
    },
    success: {
      accent: "#10b981",
      hover: "#059669",
      glow: "rgba(16, 185, 129, 0.35)",
    },
    warning: {
      accent: "#e8a33d",
      hover: "#d4902b",
      glow: "rgba(232, 163, 61, 0.35)",
    },
  };

  let defaultAccent = null;
  let defaultHover = null;
  let defaultGlow = null;

  function initDefaults() {
    const r = document.documentElement;
    const style = getComputedStyle(r);
    defaultAccent = style.getPropertyValue("--auth-accent").trim() || "#38bdf8";
    defaultHover = style.getPropertyValue("--auth-accent-hover").trim() || "#0284c7";
    defaultGlow = style.getPropertyValue("--auth-accent-glow").trim() || "rgba(56, 189, 248, 0.35)";
  }

  function applyPalette(palette) {
    const r = document.documentElement;
    r.style.setProperty("--button-color", palette.accent);
    r.style.setProperty("--auth-accent", palette.accent);
    r.style.setProperty("--auth-accent-hover", palette.hover);
    r.style.setProperty("--auth-accent-glow", palette.glow);
    r.style.setProperty("--auth-accent-subtle", palette.glow.replace(/[\d.]+\)$/, "0.12)"));
    r.style.setProperty("--auth-accent-border", palette.glow.replace(/[\d.]+\)$/, "0.35)"));
  }

  window.updateLoginTheme = function (text) {
    if (!defaultAccent) initDefaults();
    if (!text) return;
    const t = text.toLowerCase();

    if (/error|failed|invalid|denied|blocked|unauthorized|lockout/.test(t)) {
      applyPalette(SENTIMENT.error);
    } else if (/success|granted|approved|verified|authorized/.test(t)) {
      applyPalette(SENTIMENT.success);
    } else if (/warning|security|advisory|mfa|2fa|challenge|otp|token/.test(t)) {
      applyPalette(SENTIMENT.warning);
    } else {
      applyPalette({ accent: defaultAccent, hover: defaultHover, glow: defaultGlow });
    }
  };

  document.addEventListener("DOMContentLoaded", initDefaults);
})();
