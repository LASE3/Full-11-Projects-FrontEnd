/**
 * VOSTOKPRIBOR Enterprise Customer Portal - Account & System Settings Controller
 * Page-specific logic for 5-tab segment switching, SCADA API key rotation, webhooks, and clearance elevation.
 */

(function () {
  "use strict";

  let activeMasterKey = "vstk_live_e992b4fa817c992019488e001928374a88f199a2";

  /**
   * Switch settings tab view
   */
  window.switchTab = function (targetId) {
    document.querySelectorAll(".tab-btn").forEach((b) => {
      b.classList.remove(
        "bg-surface-container-lowest",
        "text-primary",
        "shadow-sm",
      );
      b.classList.add("text-on-surface-variant");
      if (b.getAttribute("data-target") === targetId) {
        b.classList.add(
          "bg-surface-container-lowest",
          "text-primary",
          "shadow-sm",
        );
        b.classList.remove("text-on-surface-variant");
      }
    });

    const targetElement = document.getElementById(targetId);
    if (targetElement) {
      targetElement.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  };

  /**
   * Save configuration changes with cryptographic signoff animation
   */
  window.saveAllSettings = function () {
    const saveBtn = document.getElementById("save-cfg-btn");
    if (saveBtn) {
      const originalText = saveBtn.innerHTML;
      saveBtn.innerHTML =
        '<span class="material-symbols-outlined text-lg animate-spin">refresh</span><span>Saving...</span>';
      setTimeout(() => {
        saveBtn.innerHTML =
          '<span class="material-symbols-outlined text-lg">check</span><span>Configuration Committed</span>';
        saveBtn.classList.add("bg-primary", "text-tertiary-fixed");
        if (window.showToast) {
          window.showToast(
            "Configuration Committed",
            "Changes cryptographically signed (Crypto-Pro GOST R 34.10)",
            "success",
          );
        }
        setTimeout(() => {
          saveBtn.innerHTML = originalText;
          saveBtn.classList.remove("bg-primary", "text-tertiary-fixed");
        }, 2500);
      }, 600);
    }
  };

  /**
   * Copy SCADA API Key to clipboard
   */
  window.copyApiKey = function () {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(activeMasterKey).then(() => {
        if (window.showToast)
          window.showToast(
            "API Key Copied",
            "SCADA Master Secret copied to clipboard",
            "success",
          );
      });
    } else {
      if (window.showToast)
        window.showToast("API Key", activeMasterKey, "info");
    }
  };

  /**
   * Display SCADA API Key Rotation Warning Modal
   */
  window.showRotateKeyModal = function () {
    const modalHtml = `
            <div class="space-y-4 text-left">
                <div class="p-3 rounded bg-surface-container-low border border-outline-variant/40 text-body-sm text-primary">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-error">warning</span>
                        <span class="font-headline-sm font-semibold">Security Warning: Active SCADA Polling</span>
                    </div>
                    <div>Rotating this industrial API key will immediately invalidate the active telemetry session on Gateway <strong>VP-GW-09</strong> until updated in the PLC configuration.</div>
                </div>
                <div class="space-y-2">
                    <label class="block font-label-caps text-label-caps text-secondary uppercase">Authorization Factor</label>
                    <div class="flex items-center gap-2 p-2 rounded bg-surface-container border border-outline-variant/50 font-technical-tag text-xs">
                        <span class="material-symbols-outlined text-sm text-primary">verified_user</span>
                        <span>YubiKey 5 FIPS hardware token detected (Alexey R. Danilov)</span>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-outline-variant/30">
                    <button onclick="document.getElementById('portal-dynamic-modal')?.remove()" class="px-3 py-1.5 rounded bg-surface-container text-secondary text-xs font-semibold hover:text-primary">Cancel</button>
                    <button onclick="window.confirmRotateKey()" class="px-4 py-1.5 rounded bg-primary text-tertiary-fixed text-xs font-bold hover:bg-primary-container shadow">
                        Confirm Key Rotation
                    </button>
                </div>
            </div>
        `;
    if (window.openModal) {
      window.openModal("Rotate SCADA Industrial API Key", modalHtml);
    }
  };

  /**
   * Confirm SCADA API key rotation and generate fresh 256-bit token
   */
  window.confirmRotateKey = function () {
    const modal = document.getElementById("portal-dynamic-modal");
    if (modal) modal.remove();

    const randomHex = Array.from({ length: 32 }, () =>
      Math.floor(Math.random() * 16).toString(16),
    ).join("");
    activeMasterKey = "vstk_live_" + randomHex;

    const display = document.getElementById("scadaApiKeyDisplay");
    if (display) {
      display.textContent =
        "vstk_live_********************************" + randomHex.slice(-4);
    }
    if (window.showToast) {
      window.showToast(
        "Key Rotated",
        "New SCADA Master Key generated & synced to Gateway VP-GW-09",
        "success",
      );
    }
  };

  /**
   * Dispatch simulated telemetry webhook test
   */
  window.testWebhookEndpoint = function () {
    if (window.showToast) {
      window.showToast(
        "Webhook Tested",
        "Telemetry test payload dispatched: HTTP 200 OK (14ms latency)",
        "success",
      );
    }
  };

  /**
   * Toggle alert notification channel switch
   */
  window.toggleNotificationSwitch = function (btn) {
    const isChecked = btn.getAttribute("aria-checked") === "true";
    const newChecked = !isChecked;
    btn.setAttribute("aria-checked", newChecked ? "true" : "false");

    if (newChecked) {
      btn.className =
        "w-10 h-5 bg-on-tertiary-container rounded-full flex items-center justify-end px-1 cursor-pointer transition-colors";
      if (window.showToast)
        window.showToast(
          "Channel Activated",
          "Notification channel enabled",
          "info",
        );
    } else {
      btn.className =
        "w-10 h-5 bg-surface-container-high rounded-full flex items-center justify-start px-1 cursor-pointer transition-colors";
      if (window.showToast)
        window.showToast(
          "Channel Muted",
          "Notification channel disabled",
          "info",
        );
    }
  };

  /**
   * Display session termination modal
   */
  window.terminateOtherSessions = function () {
    const modalHtml = `
            <div class="space-y-4 text-left">
                <div class="p-3 rounded bg-surface-container-low border border-outline-variant/40 text-body-sm text-primary">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-error">power_settings_new</span>
                        <span class="font-headline-sm font-semibold">Terminate 2 Remote Sessions?</span>
                    </div>
                    <div>This will revoke authentication tokens for Field Tablet (10.142.88.104) and SSH Terminal (194.226.13.88). Your current workstation session will remain active.</div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-outline-variant/30">
                    <button onclick="document.getElementById('portal-dynamic-modal')?.remove()" class="px-3 py-1.5 rounded bg-surface-container text-secondary text-xs font-semibold hover:text-primary">Cancel</button>
                    <button onclick="window.confirmTerminateSessions()" class="px-4 py-1.5 rounded bg-error text-on-error text-xs font-bold hover:bg-on-error-container shadow">
                        Terminate Remote Sessions
                    </button>
                </div>
            </div>
        `;
    if (window.openModal) {
      window.openModal("Revoke Remote Sessions", modalHtml);
    }
  };

  /**
   * Confirm remote sessions revocation
   */
  window.confirmTerminateSessions = function () {
    const modal = document.getElementById("portal-dynamic-modal");
    if (modal) modal.remove();

    document.querySelectorAll(".remote-session-row").forEach((row) => {
      row.remove();
    });
    if (window.showToast) {
      window.showToast(
        "Sessions Terminated",
        "Remote sessions revoked successfully.",
        "success",
      );
    }
  };

  /**
   * Display Clearance Elevation request form
   */
  window.requestClearanceElevation = function () {
    const modalHtml = `
            <form onsubmit="event.preventDefault(); window.submitClearanceElevation();" class="space-y-4 text-left">
                <div>
                    <label class="block font-label-caps text-label-caps text-secondary uppercase mb-1">Requested Clearance Tier</label>
                    <select id="clearanceTier" class="w-full h-9 px-2 bg-surface-container-low border border-outline-variant/60 rounded font-body-sm text-body-sm text-primary">
                        <option value="tier2">Tier-2 Root Industrial Plant Administrator (Full SCADA Override)</option>
                        <option value="auditor">Safety &amp; Metrological Regulatory Auditor</option>
                    </select>
                </div>
                <div>
                    <label class="block font-label-caps text-label-caps text-secondary uppercase mb-1">Operational Justification / Work Order</label>
                    <textarea id="clearanceReason" rows="3" class="w-full p-2.5 bg-surface-container-low border border-outline-variant/60 rounded font-body-sm text-body-sm text-primary" placeholder="Enter WO number, planned modernization schedule, or emergency override rationale..." required></textarea>
                </div>
                <div class="p-2.5 rounded bg-surface-container-low border border-outline-variant/40 font-technical-tag text-xs text-secondary flex items-center justify-between">
                    <span>Approving Authority: Viktor Morozov (Sr. Systems Eng.)</span>
                    <span class="text-primary font-semibold">SLA Window: &lt; 2h</span>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-outline-variant/30">
                    <button type="button" onclick="document.getElementById('portal-dynamic-modal')?.remove()" class="px-3 py-1.5 rounded bg-surface-container text-secondary text-xs font-semibold hover:text-primary">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 rounded bg-tertiary-fixed text-primary text-xs font-bold shadow hover:bg-tertiary-fixed-dim">
                        Submit Request to Manager
                    </button>
                </div>
            </form>
        `;
    if (window.openModal) {
      window.openModal("Request Clearance Elevation", modalHtml);
    }
  };

  /**
   * Submit clearance elevation request
   */
  window.submitClearanceElevation = function () {
    const modal = document.getElementById("portal-dynamic-modal");
    if (modal) modal.remove();
    if (window.showToast) {
      window.showToast(
        "Elevation Submitted",
        "Elevation request #REQ-9014 submitted to Viktor Morozov for signoff",
        "success",
      );
    }
  };

  /**
   * Attach tab click event listeners and parse URL query parameters
   */
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".tab-btn").forEach((btn) => {
      btn.addEventListener("click", () => {
        const targetId = btn.getAttribute("data-target");
        window.switchTab(targetId);
      });
    });

    const params = new URLSearchParams(window.location.search);
    const tabParam = params.get("tab");
    if (tabParam) {
      const targetId = "panel-" + tabParam;
      if (document.getElementById(targetId)) {
        setTimeout(() => window.switchTab(targetId), 200);
      }
    }
  });
})();
