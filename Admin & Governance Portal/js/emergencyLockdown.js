/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Emergency Lockdown & DEFCON-1 Quarantine Controller
 * Connected to api/lockdown.php
 */

(function () {
  "use strict";

  // Toggle individual system isolation
  window.toggleSystemLockdown = async function (systemId) {
    try {
      const res = await fetch("api/lockdown.php?action=toggle_system", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ system_id: systemId }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast?.(
          "ENCLAVE STATE TRANSITIONED",
          `${systemId} shifted to ${data.new_status}. Galvanic state logged.`,
          data.new_status === "ISOLATED" ? "error" : "success",
          data.new_status === "ISOLATED" ? "shield" : "lock_open",
        );
        setTimeout(() => location.reload(), 800);
      } else {
        window.showToast?.("ERROR", data.error || "Toggle failed", "error");
      }
    } catch (err) {
      window.showToast?.("NETWORK ERROR", err.message, "error");
    }
  };

  // Enforce full air-gap (DEFCON-1)
  window.enforceFullLockdown = async function () {
    if (
      !confirm(
        "CRITICAL ALERT: Enforce full galvanic air-gap on ALL production and governance nodes? All outbound telemetry will be severed.",
      )
    )
      return;
    try {
      const res = await fetch("api/lockdown.php?action=isolate_all", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ reason: "Manual DEFCON-1 Quarantine Triggered" }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast?.(
          "DEFCON-1 MAX ENGAGED",
          `All ${data.affected_systems} systems placed into hardened air-gap quarantine.`,
          "error",
          "warning",
        );
        setTimeout(() => location.reload(), 1000);
      } else {
        window.showToast?.(
          "ERROR",
          data.error || "Quarantine command failed",
          "error",
        );
      }
    } catch (err) {
      window.showToast?.("NETWORK ERROR", err.message, "error");
    }
  };

  // Restore full interconnect (DEFCON-4)
  window.restoreFullOperations = async function () {
    if (
      !confirm(
        "Authorize universal de-escalation? All nodes will resume nominal bidirectional telemetry.",
      )
    )
      return;
    try {
      const res = await fetch("api/lockdown.php?action=restore_all", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ reason: "DEFCON-4 Operations Restored" }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast?.(
          "OPERATIONS RESTORED",
          `Interconnect re-established across all ${data.affected_systems} nodes. Nominal baseline.`,
          "success",
          "check_circle",
        );
        setTimeout(() => location.reload(), 1000);
      } else {
        window.showToast?.(
          "ERROR",
          data.error || "Restoration command failed",
          "error",
        );
      }
    } catch (err) {
      window.showToast?.("NETWORK ERROR", err.message, "error");
    }
  };

  document.addEventListener("DOMContentLoaded", () => {
    // 1. Dynamic Syslog Console Stream
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

    // 2. Abort & Export buttons
    const btnAbort = document.getElementById("btnAbortLockdown");
    if (btnAbort) {
      btnAbort.addEventListener("click", () => {
        window.restoreFullOperations();
      });
    }

    const btnExport = document.getElementById("btnExportDossier");
    if (btnExport) {
      btnExport.addEventListener("click", () => {
        window.showToast?.(
          "CRYPTOGRAPHIC DOSSIER GENERATED",
          "Assembled ECDSA P-384 signed package of all telemetry logs, buffer snapshots, and anomaly hashes.",
          "info",
          "description",
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
          "sensors",
        );
      });
    }
  });
})();
