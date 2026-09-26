/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Break-Glass Emergency Console & Dual-Custody HSM Module
 */

(function () {
  "use strict";

  let isCustodian02Latched = false;
  let countdownSeconds = 252; // 04:12

  // 1. Live Countdown Timer
  function startTimer() {
    const timerDisplay = document.getElementById("sessionTimer");
    setInterval(() => {
      if (countdownSeconds > 0) {
        countdownSeconds--;
        const mins = String(Math.floor(countdownSeconds / 60)).padStart(2, "0");
        const secs = String(countdownSeconds % 60).padStart(2, "0");
        if (timerDisplay) timerDisplay.textContent = `${mins}:${secs}`;
      } else {
        if (timerDisplay) {
          timerDisplay.textContent = "EXPIRED";
          timerDisplay.classList.add("animate-pulse");
        }
      }
    }, 1000);
  }

  // 2. Latch Custodian 02 (Leonid Volkov, EMP-1018)
  window.latchCustodian02 = function () {
    const input = document.getElementById("fipsKeyInput");
    if (!input || !input.value.trim()) {
      window.showToast(
        "SECURITY ERROR",
        "Please enter FIPS hardware authentication challenge key.",
        "error",
        "key_off",
      );
      return;
    }

    isCustodian02Latched = true;

    const stripe = document.getElementById("custodianStripe02");
    const icon = document.getElementById("custodianIcon02");
    const badge = document.getElementById("custodianBadge02");
    const statusIcon = document.getElementById("hsmStatusIcon");
    const statusText = document.getElementById("hsmStatusText");
    const latchBtn = document.getElementById("latchSlotBtn");
    const dynamicLog = document.getElementById("dynamicLog");
    const hsmConsole = document.getElementById("hsmConsole");

    if (stripe)
      stripe.className = "absolute left-0 top-0 bottom-0 w-1 bg-secondary";
    if (icon) {
      icon.className =
        "w-10 h-10 bg-primary text-on-primary flex items-center justify-center font-bold";
      icon.innerHTML =
        '<span class="material-symbols-outlined text-[22px]">verified_user</span>';
    }
    if (badge) {
      badge.className =
        "bg-secondary-container text-on-secondary-container font-label-uppercase text-label-uppercase px-space-xs py-space-2xs font-bold";
      badge.textContent = "SLOT #02 ENGAGED (2/2 QUORUM)";
      badge.classList.remove("animate-pulse");
    }
    if (statusIcon) {
      statusIcon.className =
        "material-symbols-outlined text-[14px] text-secondary";
      statusIcon.textContent = "check_circle";
    }
    if (statusText) {
      statusText.textContent =
        "Chassis Slot #02: LATCHED (YubiKey FIPS Leonid Volkov ECC-P384 attested)";
    }
    if (latchBtn) {
      latchBtn.textContent = "VERIFIED & LOCKED";
      latchBtn.className =
        "h-control-height-sm px-space-md bg-primary text-on-primary font-label-uppercase text-label-uppercase cursor-default";
      latchBtn.disabled = true;
    }
    if (input) input.disabled = true;

    if (dynamicLog) {
      dynamicLog.className = "text-secondary font-bold";
      dynamicLog.textContent =
        "[09:42:30] HSM-SLOT-02: Cryptographic handshake valid. Quorum 2/2 achieved.";
    }

    if (hsmConsole) {
      const newLog = document.createElement("div");
      newLog.className = "text-on-surface";
      newLog.textContent =
        "[09:42:31] DUAL-CUSTODY: Sovereign air-gap enclave unlocked for armed sub-systems.";
      hsmConsole.appendChild(newLog);
      hsmConsole.scrollTop = hsmConsole.scrollHeight;
    }

    window.showToast(
      "DUAL-CUSTODY ACHIEVED",
      "Custodian Leonid Volkov (EMP-1018) verified. Sovereign Enclave armed for emergency execution.",
      "success",
      "verified",
    );
  };

  // 3. Subsystem Scope Selection
  window.toggleScope = function () {
    const c1 = document.getElementById("sys01Check")?.checked;
    const c3 = document.getElementById("sys03Check")?.checked;
    const c5 = document.getElementById("sys05Check")?.checked;
    const c7 = document.getElementById("sys07Check")?.checked;
    const c11 = document.getElementById("sys11Check")?.checked;

    const totalChecked = [c1, c3, c5, c7, c11].filter(Boolean).length;
    const countDisplay = document.getElementById("activeNodesCount");
    if (countDisplay) {
      countDisplay.textContent = `${totalChecked} SUB-SYSTEMS`;
    }
  };

  // 4. Execute Break-Glass Invocations
  window.executeBreakGlass = function () {
    const affirmation = document.getElementById("statutoryAffirmation");
    if (affirmation && !affirmation.checked) {
      window.showToast(
        "LEGAL REQUIREMENT",
        "You must check the Statutory Affirmation box before proceeding.",
        "error",
        "gavel",
      );
      return;
    }

    if (!isCustodian02Latched) {
      window.showToast(
        "QUORUM INCOMPLETE",
        "Cannot execute: Dual-Custody Quorum requires Custodian #02 key latching.",
        "error",
        "lock",
      );
      return;
    }

    const execBtn = document.getElementById("executeBreakGlassBtn");
    if (execBtn) {
      execBtn.disabled = true;
      execBtn.className =
        "w-full h-12 bg-surface-container-highest text-on-surface-variant font-title-sm text-title-sm font-bold tracking-wider flex items-center justify-center gap-space-sm cursor-not-allowed";
      execBtn.innerHTML =
        '<span class="material-symbols-outlined text-[24px] animate-spin">sync</span><span>TRANSMITTING MERKLE OVERRIDE INVOCATION...</span>';

      setTimeout(() => {
        execBtn.className =
          "w-full h-12 bg-secondary text-on-secondary font-title-sm text-title-sm font-bold tracking-wider flex items-center justify-center gap-space-sm";
        execBtn.innerHTML =
          '<span class="material-symbols-outlined text-[24px]">task_alt</span><span>OVERRIDE ACTIVE // SUPERUSER SHELL OPENED</span>';

        const hsmConsole = document.getElementById("hsmConsole");
        if (hsmConsole) {
          const log1 = document.createElement("div");
          log1.className = "text-error font-bold";
          log1.textContent =
            "[09:43:00] MERKLE-BROADCAST: Autonomous override daemon dispatched across selected nodes!";
          hsmConsole.appendChild(log1);
          hsmConsole.scrollTop = hsmConsole.scrollHeight;
        }

        window.showToast(
          "BREAK-GLASS ENGAGED",
          "Lease active (30m). Superuser telemetry session logged to Astana Audit Escrow.",
          "success",
          "lock_open",
        );
      }, 1500);
    }
  };

  document.addEventListener("DOMContentLoaded", () => {
    startTimer();

    // Bind buttons if not using inline onclick
    document
      .getElementById("latchSlotBtn")
      ?.addEventListener("click", window.latchCustodian02);
    document
      .getElementById("executeBreakGlassBtn")
      ?.addEventListener("click", window.executeBreakGlass);

    [
      "sys01Check",
      "sys03Check",
      "sys05Check",
      "sys07Check",
      "sys11Check",
    ].forEach((id) => {
      document
        .getElementById(id)
        ?.addEventListener("change", window.toggleScope);
    });
  });
})();
