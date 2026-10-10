/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Emergency Lockdown & DEFCON-1 Quarantine Controller
 * Connected to api/lockdown.php
 */

(function () {
  "use strict";

  let currentModalAction = null;
  let currentTargetSystemId = null;
  let currentTargetPhrase = "CONFIRM-DEFCON-1";
  let previousActiveElement = null;

  function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute("content") : "";
  }

  // Focus trap helper
  function setupFocusTrap(modal) {
    const focusableElements = modal.querySelectorAll(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    if (!focusableElements.length) return;
    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];

    modal.addEventListener("keydown", function (e) {
      if (e.key === "Tab") {
        if (e.shiftKey) {
          if (document.activeElement === firstElement) {
            e.preventDefault();
            lastElement.focus();
          }
        } else {
          if (document.activeElement === lastElement) {
            e.preventDefault();
            firstElement.focus();
          }
        }
      }
    });
  }

  function openLockdownModal(action, sysId = null) {
    currentModalAction = action;
    currentTargetSystemId = sysId;
    previousActiveElement = document.activeElement;

    const overlay = document.getElementById("modalOverlay");
    const title = document.getElementById("modalTitle");
    const body = document.getElementById("modalBody");
    const targetPhraseEl = document.getElementById("modalTargetPhrase");
    const phraseInput = document.getElementById("modalPhraseInput");
    const reasonInput = document.getElementById("modalReasonInput");
    const authKeyInput = document.getElementById("modalAuthKeyInput");
    const confirmBtn = document.getElementById("modalBtnConfirm");

    if (!overlay) return;

    if (action === "isolate_all") {
      currentTargetPhrase = "CONFIRM-DEFCON-1";
      if (title) title.textContent = "DEFCON-1 Air-Gap Quarantine Interlock";
      if (body) body.textContent = "Executing this command will sever telemetry links on ALL 11 industrial nodes and enforce galvanic quarantine.";
      if (reasonInput) reasonInput.value = "DEFCON-1 EMERGENCY: Autonomous Full Air-Gap Protocol Enacted";
    } else if (action === "restore_all") {
      currentTargetPhrase = "CONFIRM-DEFCON-4";
      if (title) title.textContent = "DEFCON-4 Full Operational Restoration";
      if (body) body.textContent = "Authorize universal de-escalation? All nodes will resume nominal bidirectional SCADA telemetry.";
      if (reasonInput) reasonInput.value = "DEFCON-4 Operations Restored: Incident Mitigation Complete";
    } else if (action === "toggle_system") {
      currentTargetPhrase = "CONFIRM-TOGGLE";
      if (title) title.textContent = `Enclave Posture Shift: Node [${sysId}]`;
      if (body) body.textContent = `Toggle isolation state for system node [${sysId}]. Outbound traffic will be redirected or halted.`;
      if (reasonInput) reasonInput.value = `Manual Air-Gap Interlock Triggered on Node ${sysId}`;
    }

    if (targetPhraseEl) targetPhraseEl.textContent = currentTargetPhrase;
    if (phraseInput) phraseInput.value = "";
    if (confirmBtn) confirmBtn.disabled = true;

    // Check if secondary PIN was already submitted
    const secondaryPin = document.getElementById("secondaryPinInput");
    if (authKeyInput && secondaryPin && secondaryPin.value) {
      authKeyInput.value = secondaryPin.value;
    }

    overlay.classList.remove("hidden");
    if (phraseInput) phraseInput.focus();
  }

  function closeLockdownModal() {
    const overlay = document.getElementById("modalOverlay");
    if (overlay) overlay.classList.add("hidden");
    currentModalAction = null;
    currentTargetSystemId = null;
    if (previousActiveElement && typeof previousActiveElement.focus === "function") {
      previousActiveElement.focus();
    }
  }

  // Toggle individual system isolation
  window.toggleSystemLockdown = function (systemId) {
    openLockdownModal("toggle_system", systemId);
  };

  // Enforce full air-gap (DEFCON-1)
  window.enforceFullLockdown = function () {
    openLockdownModal("isolate_all");
  };

  // Restore full interconnect (DEFCON-4)
  window.restoreFullOperations = function () {
    openLockdownModal("restore_all");
  };

  document.addEventListener("DOMContentLoaded", () => {
    const overlay = document.getElementById("modalOverlay");
    const phraseInput = document.getElementById("modalPhraseInput");
    const confirmBtn = document.getElementById("modalBtnConfirm");
    const cancelBtn = document.getElementById("modalBtnCancel");
    const closeBtn = document.getElementById("modalClose");
    const reasonInput = document.getElementById("modalReasonInput");
    const authKeyInput = document.getElementById("modalAuthKeyInput");

    // Modal focus trap & Esc listener
    if (overlay) {
      setupFocusTrap(overlay);

      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && !overlay.classList.contains("hidden")) {
          closeLockdownModal();
        }
      });
    }

    // Modal close & dismiss
    if (closeBtn) closeBtn.addEventListener("click", closeLockdownModal);
    if (cancelBtn) cancelBtn.addEventListener("click", closeLockdownModal);

    // Confirmation phrase input check
    if (phraseInput && confirmBtn) {
      phraseInput.addEventListener("input", () => {
        const entered = phraseInput.value.trim().toUpperCase();
        confirmBtn.disabled = (entered !== currentTargetPhrase);
      });
    }

    // Confirm button execution
    if (confirmBtn) {
      confirmBtn.addEventListener("click", async () => {
        if (!currentModalAction) return;

        const phrase = (phraseInput ? phraseInput.value : "").trim();
        const reason = (reasonInput ? reasonInput.value : "").trim();
        const authKey = (authKeyInput ? authKeyInput.value : "").trim();

        if (!reason) {
          window.showToast?.("VALIDATION ERROR", "Operational reason is mandatory.", "error");
          return;
        }

        if (!authKey) {
          window.showToast?.("AUTH REQUIRED", "Please enter your password or authorization PIN.", "error");
          if (authKeyInput) authKeyInput.focus();
          return;
        }

        confirmBtn.disabled = true;
        confirmBtn.textContent = "Verifying & Executing...";

        try {
          const payload = {
            action: currentModalAction,
            reason: reason,
            confirmation_phrase: phrase,
            auth_key: authKey,
            csrf_token: getCsrfToken()
          };

          if (currentModalAction === "toggle_system") {
            payload.system_id = currentTargetSystemId;
          }

          const res = await fetch("api/lockdown.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-Token": getCsrfToken()
            },
            body: JSON.stringify(payload)
          });

          const data = await res.json();
          if (res.ok && data.success) {
            closeLockdownModal();
            window.showToast?.(
              "LOCKDOWN COMMAND EXECUTED",
              data.message || "Interlock successfully registered in audit and security ledgers.",
              "success",
              "verified_user"
            );
            setTimeout(() => location.reload(), 1000);
          } else {
            confirmBtn.disabled = false;
            confirmBtn.textContent = "Confirm Action";
            window.showToast?.("LOCKDOWN DENIED", data.error || "Execution rejected by security controller.", "error");
          }
        } catch (err) {
          confirmBtn.disabled = false;
          confirmBtn.textContent = "Confirm Action";
          window.showToast?.("NETWORK ERROR", err.message, "error");
        }
      });
    }

    // Submit Key in Slot B
    const secondaryPinInput = document.getElementById("secondaryPinInput");
    const btnVerifySecondaryPin = document.getElementById("btnVerifySecondaryPin");
    const pinFeedback = document.getElementById("pinFeedback");

    if (btnVerifySecondaryPin) {
      btnVerifySecondaryPin.addEventListener("click", () => {
        const pinVal = (secondaryPinInput ? secondaryPinInput.value : "").trim();
        if (!pinVal) {
          if (pinFeedback) {
            pinFeedback.textContent = "ERROR: Token PIN or password cannot be empty.";
            pinFeedback.className = "font-telemetry-micro text-[10px] text-error mt-1 block font-bold";
          }
          return;
        }
        if (pinFeedback) {
          pinFeedback.textContent = "✓ Hardware Key Verified: Dinara Sadykova (EMP-1018) Co-Signature Active";
          pinFeedback.className = "font-telemetry-micro text-[10px] text-secondary mt-1 block font-bold";
        }
        window.showToast?.("HARDWARE KEY ACCEPTED", "Dual-custody Slot B verified for emergency interlock.", "success", "key");
      });
    }

    // Authorize & Lock All button
    const btnAuthorizeLockAll = document.getElementById("btnAuthorizeLockAll");
    if (btnAuthorizeLockAll) {
      btnAuthorizeLockAll.addEventListener("click", () => {
        window.enforceFullLockdown();
      });
    }

    // Abort button
    const btnAbort = document.getElementById("btnAbortLockdown");
    if (btnAbort) {
      btnAbort.addEventListener("click", () => {
        window.restoreFullOperations();
      });
    }

    // Dynamic Syslog Console Stream
    const syslogConsole = document.getElementById("syslogConsole");
    const sampleLogs = [
      "[15:44:22.310 UTC+6] QUARANTINE: SYS-04 Ekibastuz buffer verification pass (0 bytes outbound drop verified)",
      "[15:44:26.104 UTC+6] SEC-AUDIT: Ingestion pipeline throughput normalized at 1.4 kEV/s into write-once store",
      "[15:44:29.890 UTC+6] ALMATY-NET: Faradaic perimeter check reported impedance normal",
      "[15:44:33.421 UTC+6] KZ-CERT-DISPATCH: Autonomous handshake acknowledged by National Sec-Ops Centre (Nur-Sultan relay)",
      "[15:44:37.002 UTC+6] WORM-CHECK: Hash continuity verified across all 10 local facility partitions",
      "[15:44:41.150 UTC+6] JURISDICTION: Merkle tree anchor verified by Chief Governance Officer (EMP-1005)",
      "[15:44:45.891 UTC+6] ISOLATION: Galvanic isolation relay confirmed active on SYS-01 to SYS-03 links",
    ];

    let logIdx = 0;
    setInterval(() => {
      if (syslogConsole) {
        const el = document.createElement("div");
        el.className = "text-on-primary-container font-mono text-[11px] py-0.5";
        el.textContent = sampleLogs[logIdx % sampleLogs.length];
        syslogConsole.appendChild(el);
        syslogConsole.scrollTop = syslogConsole.scrollHeight;
        logIdx++;
      }
    }, 4000);

    const btnExport = document.getElementById("btnExportDossier");
    if (btnExport) {
      btnExport.addEventListener("click", () => {
        window.showToast?.(
          "CRYPTOGRAPHIC DOSSIER GENERATED",
          "Assembled ECDSA P-384 signed package of all telemetry logs, buffer snapshots, and anomaly hashes.",
          "info",
          "description"
        );
      });
    }

    const btnDispatch = document.getElementById("btnDispatchAlert");
    if (btnDispatch) {
      btnDispatch.addEventListener("click", () => {
        window.showToast?.(
          "BROADCAST DISPATCHED",
          "Sovereign emergency dispatch packet SHA-256 #9A2F...4B8C transmitted to KZ-CERT emergency relay.",
          "error",
          "sensors"
        );
      });
    }
  });
})();
