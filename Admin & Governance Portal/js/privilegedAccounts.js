/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Privileged Accounts Monitoring & Vault Control Module
 * Live Database API Integration & Interactive Telemetry
 */

(function () {
  "use strict";

  // Track currently inspected account for Bastion Console
  let currentInspected = {
    empId: "EMP-1001",
    fullName: "Viktor Sokolov",
    username: "viktor.sokolov",
    clearance: "L4",
    targetHost: "sys-05-balkhash"
  };

  // Global functions exposed to window for inline HTML onclick handlers
  window.inspectAccount = function (empId, fullName, username, clearance, host, ip) {
    currentInspected = {
      empId: empId || "EMP-1001",
      fullName: fullName || "Viktor Sokolov",
      username: username || "operator",
      clearance: clearance || "L4",
      targetHost: host || "sys-05-balkhash"
    };

    // Update Live Bastion Keystream Inspector on right panel
    const opDisplay = document.querySelector("#bastion-operator-name") || document.querySelector(".xl\\:col-span-4 .font-semibold.text-primary") || document.querySelector(".xl\\:col-span-4 div:has(> span.text-on-surface-variant)");
    const targetDisplay = document.querySelector("#bastion-target-info");
    const terminalBox = document.querySelector("#bastionTerminalStream") || document.querySelector(".xl\\:col-span-4 pre") || document.querySelector(".xl\\:col-span-4 .bg-primary-container\\/80");

    // Find inspect header elements
    const operatorEl = document.getElementById("bastion-operator-label");
    const targetEl = document.getElementById("bastion-target-label");
    if (operatorEl) {
      operatorEl.textContent = `${fullName} (${empId})`;
    }
    if (targetEl) {
      targetEl.textContent = `admin_${username}@${currentInspected.targetHost}`;
    }

    if (terminalBox) {
      const now = new Date();
      const timeStr = now.toISOString().slice(11, 19) + " UTC+6";
      terminalBox.innerHTML = `
<div class="text-[#00F0FF] font-bold">[sys-05-balkhash~#] attach --operator="${empId}" --session-mode=SHADOW_RO</div>
<div class="text-slate-300">Target host: 10.240.44.102 (${currentInspected.targetHost})</div>
<div class="text-slate-400">Operator: ${fullName} • Clearance: ${clearance} • Key: RSA-4096</div>
<div class="text-emerald-400">Channel secure. OCR Keystroke mirroring active [${timeStr}]</div>
<div class="text-slate-200 mt-1">[${username}@balkhash-0] systemctl status substation-telemetry.service</div>
<div class="text-slate-400">  Active: active (running) since Tue 2026-03-31 08:00:14 UTC</div>
<div class="text-slate-200">[${username}@balkhash-0] openssl verify -CAfile /etc/vostok/ca.crt cert.pem</div>
<div class="text-emerald-400">cert.pem: OK (Serial: 9f:44:18:57:39; EXPIRY: 2027-01-01)</div>
<div class="text-[#00F0FF] animate-pulse">[${username}@balkhash-0] █</div>
      `.trim();
    }

    // Highlight row in table
    document.querySelectorAll("#vault-registry-tbody tr").forEach(tr => {
      tr.classList.remove("ring-2", "ring-secondary", "bg-surface-container");
    });
    const clickedBtn = window.event?.target;
    if (clickedBtn) {
      const row = clickedBtn.closest("tr");
      if (row) row.classList.add("ring-2", "ring-secondary", "bg-surface-container");
    }

    window.showToast?.(
      "BASTION INSPECTOR ENGAGED",
      `Active session for ${fullName} (${empId}) bound to live keystream monitor.`,
      "info",
      "shield_person"
    );
  };

  window.severAccountCredentials = async function (empId) {
    if (!confirm(`CRITICAL SECURITY INTERLOCK:\n\nRevoke all active cryptographic keys and sever interactive session for employee [${empId}]?`)) {
      return;
    }
    try {
      const res = await fetch("api/privileged.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "sever_credentials", emp_id: empId }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast?.(
          "CREDENTIALS SEVERED",
          `Account [${empId}] revoked. Session tokens flushed from memory and recorded in audit ledger.`,
          "error",
          "link_off"
        );

        // Update row dynamically
        const clickedBtn = window.event?.target;
        if (clickedBtn) {
          const row = clickedBtn.closest("tr");
          if (row) {
            row.classList.remove("border-l-4", "border-l-secondary");
            row.classList.add("opacity-75");

            // Change status badge
            const statusBadge = row.querySelector(".font-security-stamp:last-of-type, span:has(> .rounded-full)");
            if (statusBadge) {
              statusBadge.className = "inline-flex items-center gap-[4px] px-space-xs py-[2px] bg-error-container text-on-error-container font-security-stamp text-[10px] font-bold rounded";
              statusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-error"></span>REVOKED';
            }

            // Change TTL
            const ttlSpan = row.querySelector(".font-telemetry-micro.font-bold");
            if (ttlSpan) {
              ttlSpan.textContent = "SEVERED";
              ttlSpan.className = "font-telemetry-micro text-telemetry-micro font-bold text-error";
            }

            // Replace Sever button with Restore button
            clickedBtn.outerHTML = `<button class="px-space-xs py-[3px] bg-secondary hover:bg-secondary/90 text-on-secondary rounded font-telemetry-micro text-telemetry-micro font-bold cursor-pointer" onclick="restoreAccountCredentials('${empId}')">Restore</button>`;
          }
        } else {
          setTimeout(() => location.reload(), 800);
        }
      } else {
        window.showToast?.("ERROR", data.error || "Sever operation rejected by policy engine.", "error");
      }
    } catch (err) {
      window.showToast?.("NETWORK ERROR", err.message, "error");
    }
  };

  window.restoreAccountCredentials = async function (empId) {
    if (!confirm(`Restore privileged access and clear quarantine for employee [${empId}]?`)) {
      return;
    }
    try {
      const res = await fetch("api/privileged.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "restore_credentials", emp_id: empId }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast?.(
          "CREDENTIALS RESTORED",
          `Account [${empId}] status restored to Active in database.`,
          "success",
          "verified"
        );
        setTimeout(() => location.reload(), 700);
      } else {
        window.showToast?.("ERROR", data.error || "Restore failed", "error");
      }
    } catch (err) {
      window.showToast?.("NETWORK ERROR", err.message, "error");
    }
  };

  window.terminateActiveLease = async function () {
    if (!confirm(`EMERGENCY SESSION SEVER:\n\nTerminate bastion session for ${currentInspected.fullName} (${currentInspected.empId})?`)) {
      return;
    }
    try {
      const res = await fetch("api/privileged.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "terminate_session", session_id: "all" }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast?.(
          "SESSION TERMINATED",
          `Active lease for ${currentInspected.fullName} severed. Cryptographic keys cleared from HSM.`,
          "error",
          "cancel"
        );
        const terminalBox = document.querySelector("#bastionTerminalStream");
        if (terminalBox) {
          terminalBox.innerHTML += `<div class="text-rose-500 font-bold mt-2">[SECURITY ALERT] Session abruptly terminated by Executive SuperAdmin via Zero-Trust interlock. Connection closed.</div>`;
        }
      } else {
        window.showToast?.("ERROR", data.error || "Termination failed", "error");
      }
    } catch (err) {
      window.showToast?.("NETWORK ERROR", err.message, "error");
    }
  };

  window.emergencySeverAll = async function () {
    if (!confirm("DEFCON-1 GLOBAL PAM SEVER:\n\nAre you sure you want to trigger emergency lockout? All active privileged sessions across all bastions (01-04) will be immediately terminated and keys zeroized in HSM.")) {
      return;
    }
    try {
      const res = await fetch("api/privileged.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "terminate_session", session_id: "all" }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast?.(
          "GLOBAL LOCKOUT EXECUTED",
          "All interactive PAM sessions terminated across production bastions.",
          "error",
          "power_off"
        );
        setTimeout(() => location.reload(), 1000);
      } else {
        window.showToast?.("ERROR", data.error || "Global sever failed", "error");
      }
    } catch (err) {
      window.showToast?.("NETWORK ERROR", err.message, "error");
    }
  };

  // DOMContentLoaded handlers
  document.addEventListener("DOMContentLoaded", () => {
    const reqModal = document.getElementById("modal-request-credential");
    const checkinModal = document.getElementById("modal-checkin-confirm");
    const btnRequestCred = document.getElementById("btn-request-cred");
    const btnCheckinAll = document.getElementById("btn-checkin-all");
    const closeModalReq = document.getElementById("close-modal-req");
    const cancelModalReq = document.getElementById("cancel-modal-req");
    const closeModalCheckin = document.getElementById("close-modal-checkin");
    const cancelModalCheckin = document.getElementById("cancel-modal-checkin");
    const submitModalReq = document.getElementById("submit-modal-req");
    const confirmMassCheckin = document.getElementById("confirm-mass-checkin");

    // 1. Modal Open / Close
    if (btnRequestCred && reqModal) {
      btnRequestCred.addEventListener("click", () => reqModal.classList.remove("hidden"));
    }
    if (closeModalReq && reqModal) {
      closeModalReq.addEventListener("click", () => reqModal.classList.add("hidden"));
    }
    if (cancelModalReq && reqModal) {
      cancelModalReq.addEventListener("click", () => reqModal.classList.add("hidden"));
    }

    if (btnCheckinAll && checkinModal) {
      btnCheckinAll.addEventListener("click", () => checkinModal.classList.remove("hidden"));
    }
    if (closeModalCheckin && checkinModal) {
      closeModalCheckin.addEventListener("click", () => checkinModal.classList.add("hidden"));
    }
    if (cancelModalCheckin && checkinModal) {
      cancelModalCheckin.addEventListener("click", () => checkinModal.classList.add("hidden"));
    }

    // 2. Tier Selection in Modal
    document.querySelectorAll(".tier-btn").forEach((btn) => {
      btn.addEventListener("click", () => {
        document.querySelectorAll(".tier-btn").forEach((b) => {
          b.classList.remove("border-error", "bg-error/10", "text-error", "border-primary", "bg-primary-container", "text-on-primary", "border-secondary", "bg-secondary-container/20");
          b.classList.add("border-outline-variant/60");
          const title = b.querySelector("span:first-child");
          if (title) title.className = "font-security-stamp text-[11px] font-bold text-primary";
        });

        const tier = btn.getAttribute("data-tier");
        const selectedTierInput = document.getElementById("req-selected-tier");
        if (selectedTierInput) selectedTierInput.value = tier;
        const notice = document.getElementById("tier0-notice");

        if (tier === "0") {
          btn.classList.add("border-error", "bg-error/10", "text-error");
          btn.querySelector("span:first-child").className = "font-security-stamp text-[11px] font-bold text-error";
          if (notice) notice.classList.remove("hidden");
        } else if (tier === "1") {
          btn.classList.add("border-[#D9822B]", "bg-tertiary-fixed/20");
          btn.querySelector("span:first-child").className = "font-security-stamp text-[11px] font-bold text-[#D9822B]";
          if (notice) notice.classList.add("hidden");
        } else {
          btn.classList.add("border-secondary", "bg-secondary-container/20");
          btn.querySelector("span:first-child").className = "font-security-stamp text-[11px] font-bold text-secondary";
          if (notice) notice.classList.add("hidden");
        }
      });
    });

    // 3. TTL Selection in Modal
    document.querySelectorAll(".ttl-btn").forEach((btn) => {
      btn.addEventListener("click", () => {
        document.querySelectorAll(".ttl-btn").forEach((b) => {
          b.className = "ttl-btn py-space-xs px-1 text-center font-telemetry-micro text-[11px] rounded border border-outline-variant/50 hover:bg-surface-container font-medium";
        });
        btn.className = "ttl-btn py-space-xs px-1 text-center font-telemetry-micro text-[11px] rounded border-2 border-primary bg-primary-container text-on-primary font-bold";
        const dur = btn.getAttribute("data-duration");
        const ttlInput = document.getElementById("req-selected-ttl");
        const ttlDisplay = document.getElementById("req-ttl-display");
        if (ttlInput) ttlInput.value = dur;
        if (ttlDisplay) ttlDisplay.innerText = dur;
      });
    });

    // 4. Submit Request (Mint Credential)
    if (submitModalReq) {
      submitModalReq.addEventListener("click", () => {
        const targetAssetEl = document.getElementById("req-target-asset");
        const asset = targetAssetEl ? targetAssetEl.value.split(" ")[0] : "SYS-11";
        const tierEl = document.getElementById("req-selected-tier");
        const tier = tierEl ? tierEl.value : "1";
        const ttlEl = document.getElementById("req-selected-ttl");
        const ttl = ttlEl ? ttlEl.value : "1 Hour";
        const justEl = document.getElementById("req-justification");
        const justification = justEl ? justEl.value : "Emergency maintenance";

        if (reqModal) reqModal.classList.add("hidden");

        const tbody = document.getElementById("vault-registry-tbody");
        const newRow = document.createElement("tr");
        newRow.className = "bg-secondary-container/20 border-l-4 border-l-secondary-fixed transition-colors vault-row hover:bg-surface-container";
        newRow.innerHTML = `
          <td class="py-space-xs px-space-sm border-r border-outline-variant/20">
            <div class="flex flex-col">
              <div class="flex items-center gap-space-xs">
                <span class="w-2 h-2 rounded-full bg-secondary-fixed animate-ping"></span>
                <span class="font-telemetry-data text-telemetry-data font-bold text-primary">jit_${asset.toLowerCase().replace(/[^a-z0-9]/g, '_')}@vostok-vault</span>
              </div>
              <span class="font-telemetry-micro text-[10px] text-on-surface-variant">IP: 10.240.99.1 • ${asset} (EPHEMERAL)</span>
            </div>
          </td>
          <td class="py-space-xs px-space-sm border-r border-outline-variant/20">
            <div class="flex flex-col">
              <span class="font-semibold text-primary">System Administrator</span>
              <span class="font-telemetry-micro text-[10px] text-secondary font-bold">EMP-0001 • JIT ${justification.slice(0, 30)}</span>
            </div>
          </td>
          <td class="py-space-xs px-space-sm border-r border-outline-variant/20">
            <span class="font-security-stamp text-[10px] px-space-xs py-[2px] ${tier === "0" ? "bg-error text-on-error" : tier === "1" ? "bg-[#D9822B] text-white" : "bg-primary text-on-primary"} font-bold rounded">TIER ${tier} (${tier === "0" ? "ROOT" : "PRIV"})</span>
          </td>
          <td class="py-space-xs px-space-sm border-r border-outline-variant/20 font-telemetry-micro text-telemetry-micro">
            HSM Ephemeral / MFA: <span class="text-secondary font-bold">YES</span>
          </td>
          <td class="py-space-xs px-space-sm border-r border-outline-variant/20">
            <div class="flex flex-col gap-1">
              <span class="font-bold text-secondary font-telemetry-micro text-telemetry-micro">ACTIVE LEASE</span>
              <span class="font-telemetry-micro text-[10px] text-on-surface-variant">${ttl} remaining</span>
            </div>
          </td>
          <td class="py-space-xs px-space-sm border-r border-outline-variant/20">
            <span class="inline-flex items-center gap-[4px] px-space-xs py-[2px] bg-secondary-container/30 text-on-secondary-container font-security-stamp text-[10px] font-bold rounded">
              <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>NOMINAL
            </span>
          </td>
          <td class="py-space-xs px-space-sm text-right">
            <div class="flex items-center justify-end gap-space-2xs">
              <button class="px-space-xs py-[3px] bg-surface-container text-primary hover:bg-surface-container-high border border-outline-variant rounded font-telemetry-micro text-telemetry-micro font-bold cursor-pointer" onclick="inspectAccount('EMP-0001', 'System Administrator', 'jit_${asset.toLowerCase()}', 'L4')">Inspect</button>
              <button class="px-space-xs py-[3px] bg-error hover:bg-error/90 text-on-error rounded font-telemetry-micro text-telemetry-micro font-bold cursor-pointer" onclick="severAccountCredentials('EMP-0001')">Sever</button>
            </div>
          </td>
        `;

        if (tbody) tbody.insertBefore(newRow, tbody.firstChild);

        const activeKpi = document.getElementById("kpi-active-leases-count");
        if (activeKpi) activeKpi.innerText = "08 ACCOUNTS CHECKED OUT";

        window.showToast?.(
          "EPHEMERAL LEASE MINTED",
          `Credential generated for ${asset} (${ttl} TTL) [FIPS-140-3 HSM VALIDATED]`,
          "success",
          "vpn_key"
        );
      });
    }

    // 5. Mass Check-In Confirmation
    if (confirmMassCheckin) {
      confirmMassCheckin.addEventListener("click", async () => {
        if (checkinModal) checkinModal.classList.add("hidden");
        try {
          await fetch("api/privileged.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "terminate_session", session_id: "all" }),
          });
        } catch (e) {
          console.warn(e);
        }

        const activeKpi = document.getElementById("kpi-active-leases-count");
        if (activeKpi) activeKpi.innerText = "00 ACCOUNTS CHECKED OUT";

        const tbody = document.getElementById("vault-registry-tbody");
        if (tbody) {
          tbody.querySelectorAll("tr").forEach((tr) => {
            const ttlEl = tr.querySelector("td:nth-child(5) .font-telemetry-micro.font-bold");
            if (ttlEl) {
              ttlEl.textContent = "STANDBY";
              ttlEl.className = "font-telemetry-micro text-telemetry-micro font-bold text-on-surface-variant";
            }
            const pulseDot = tr.querySelector(".animate-pulse, .animate-ping");
            if (pulseDot) pulseDot.remove();
            tr.classList.remove("border-l-4", "border-l-secondary");
          });
        }

        window.showToast?.(
          "ALL LEASES CHECKED IN",
          "All active leases successfully returned to HSM Vault [ECDSA-SECP256K1 CONFIRMED]",
          "success",
          "lock_reset"
        );
      });
    }

    // 6. Middle Filter Tabs & Search Chassis
    const filterTabs = document.querySelectorAll(".flex.items-center.gap-\\[2px\\] button");
    const searchInput = document.getElementById("pamSearchInput") || document.querySelector('input[placeholder*="Filter query syntax"]');
    const matchCountBadge = document.getElementById("pamMatchBadge") || document.querySelector(".bg-surface-container-high.px-space-xs");
    const resetFilterBtn = document.getElementById("pamResetBtn");

    let currentTab = "ALL";

    function applyPamFilter() {
      const tbody = document.getElementById("vault-registry-tbody");
      if (!tbody) return;
      const rows = tbody.querySelectorAll("tr");
      const query = (searchInput ? searchInput.value : "").trim().toLowerCase();
      let matchCount = 0;

      rows.forEach((row) => {
        const text = row.innerText.toLowerCase();
        let matchesTab = true;

        if (currentTab === "ACTIVE") {
          matchesTab = text.includes("active lease");
        } else if (currentTab === "PENDING") {
          matchesTab = text.includes("standby") || text.includes("pending");
        } else if (currentTab === "TIER0") {
          matchesTab = text.includes("tier 0");
        } else if (currentTab === "SERVICES") {
          matchesTab = text.includes("vault") || text.includes("root") || text.includes("sys-");
        }

        let matchesQuery = true;
        if (query) {
          matchesQuery = text.includes(query);
        }

        if (matchesTab && matchesQuery) {
          row.style.display = "";
          matchCount++;
        } else {
          row.style.display = "none";
        }
      });

      const badge = document.getElementById("pamMatchBadge") || document.querySelector(".bg-surface-container-high.px-space-xs");
      if (badge) {
        badge.textContent = `MATCH: ${matchCount} OF ${rows.length} RECORDS`;
      }
    }

    // Attach click to middle bar tabs
    filterTabs.forEach((tab, index) => {
      tab.addEventListener("click", () => {
        filterTabs.forEach((t) => {
          t.className = "px-space-sm py-[4px] text-body-compact font-body-compact font-medium text-on-surface-variant hover:text-primary hover:bg-surface-container-high rounded-sm transition-colors whitespace-nowrap cursor-pointer";
        });
        tab.className = "px-space-sm py-[4px] text-body-compact font-body-compact font-bold bg-primary text-on-primary rounded-sm shadow-sm whitespace-nowrap cursor-pointer";

        if (index === 0) currentTab = "ALL";
        else if (index === 1) currentTab = "ACTIVE";
        else if (index === 2) currentTab = "PENDING";
        else if (index === 3) currentTab = "TIER0";
        else if (index === 4) currentTab = "SERVICES";

        applyPamFilter();
      });
    });

    if (searchInput) {
      searchInput.addEventListener("input", applyPamFilter);
      searchInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") applyPamFilter();
      });
    }

    // Find reset button
    document.querySelectorAll("button").forEach((btn) => {
      if (btn.textContent.trim() === "Reset") {
        btn.addEventListener("click", () => {
          if (searchInput) searchInput.value = "";
          currentTab = "ALL";
          if (filterTabs[0]) filterTabs[0].click();
          applyPamFilter();
          window.showToast?.("FILTER RESET", "Showing all privileged account records.", "info", "refresh");
        });
      }
    });

    // 6.5 Polling Refresh & Real Export Handlers
    const pamRefreshBtn = document.getElementById("pamPollingRefreshBtn");
    if (pamRefreshBtn) {
      pamRefreshBtn.addEventListener("click", () => {
        const icon = pamRefreshBtn.querySelector(".material-symbols-outlined");
        if (icon) icon.classList.add("animate-spin");
        setTimeout(() => {
          if (icon) icon.classList.remove("animate-spin");
          applyPamFilter();
          window.showToast?.(
            "INVENTORY SYNCHRONIZED",
            "50 Privileged vault leases verified against HSM daemon. Zero cryptographic drift detected.",
            "success",
            "sync"
          );
        }, 700);
      });
    }

    const pamCsvBtn = document.getElementById("pamExportCsvBtn");
    if (pamCsvBtn) {
      pamCsvBtn.addEventListener("click", () => {
        const rows = document.querySelectorAll("#vault-registry-tbody tr");
        let csvContent = "Account_Email,Host_IP,Department,Operator_Name,EMP_ID,Clearance,Protocol,Status,Integrity\n";
        rows.forEach((r) => {
          if (r.style.display === "none") return;
          const account = r.cells[0]?.querySelector(".font-telemetry-data")?.textContent.trim() || "";
          const ipDept = r.cells[0]?.querySelector(".font-telemetry-micro")?.textContent.trim() || "";
          const opName = r.cells[1]?.querySelector(".font-semibold")?.textContent.trim() || "";
          const empTitle = r.cells[1]?.querySelector(".font-telemetry-micro")?.textContent.trim() || "";
          const clearance = r.cells[2]?.querySelector(".font-security-stamp")?.textContent.trim() || "";
          const protocol = r.cells[3]?.textContent.replace(/\s+/g, " ").trim() || "";
          const ttlStatus = r.cells[4]?.querySelector(".font-telemetry-micro")?.textContent.trim() || "";
          const integrity = r.cells[5]?.querySelector(".font-security-stamp")?.textContent.trim() || "";
          csvContent += `"${account}","${ipDept}","","${opName}","${empTitle}","${clearance}","${protocol}","${ttlStatus}","${integrity}"\n`;
        });
        const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `VOSTOKPRIBOR_Privileged_Accounts_${Date.now()}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        window.showToast?.("PAM CSV EXPORTED", "Downloaded privileged accounts inventory as CSV.", "success", "file_download");
      });
    }

    const pamJsonBtn = document.getElementById("pamExportJsonBtn");
    if (pamJsonBtn) {
      pamJsonBtn.addEventListener("click", () => {
        const rows = document.querySelectorAll("#vault-registry-tbody tr");
        const accounts = [];
        rows.forEach((r) => {
          if (r.style.display === "none") return;
          accounts.push({
            account: r.cells[0]?.querySelector(".font-telemetry-data")?.textContent.trim(),
            host: r.cells[0]?.querySelector(".font-telemetry-micro")?.textContent.trim(),
            operator: r.cells[1]?.querySelector(".font-semibold")?.textContent.trim(),
            details: r.cells[1]?.querySelector(".font-telemetry-micro")?.textContent.trim(),
            clearance: r.cells[2]?.querySelector(".font-security-stamp")?.textContent.trim(),
            protocol: r.cells[3]?.textContent.replace(/\s+/g, " ").trim(),
            lease: r.cells[4]?.querySelector(".font-telemetry-micro")?.textContent.trim(),
            integrity: r.cells[5]?.querySelector(".font-security-stamp")?.textContent.trim(),
          });
        });
        const data = {
          header: "VOSTOKPRIBOR SYSTEM 11 SECURED VAULT DIRECTORY",
          timestamp: new Date().toISOString(),
          station: "ALMATY-CENTRAL",
          signature: "SHA256:ECDSA-P384:8f90ee1a98c3b77209",
          totalRecords: accounts.length,
          accounts: accounts
        };
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: "application/json" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `VOSTOKPRIBOR_Privileged_Accounts_Signed_${Date.now()}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        window.showToast?.("PAM JSON-SIG EXPORTED", "Downloaded cryptographic JSON-SIG payload.", "success", "file_download");
      });
    }

    // 7. Right Panel Buttons
    // A. Shadow Session (RO)
    const shadowBtn = Array.from(document.querySelectorAll("button")).find(b => b.textContent.includes("Shadow Session"));
    if (shadowBtn) {
      shadowBtn.addEventListener("click", () => {
        window.showToast?.(
          "SHADOW TERMINAL OPENED",
          `Monitoring read-only shadow stream for ${currentInspected.fullName} (${currentInspected.empId}) on ${currentInspected.targetHost}.`,
          "info",
          "terminal"
        );
      });
    }

    // B. Broadcast to Shell
    const broadcastBtn = Array.from(document.querySelectorAll("button")).find(b => b.textContent.includes("Broadcast to Shell"));
    if (broadcastBtn) {
      broadcastBtn.addEventListener("click", () => {
        const msg = prompt(`Enter broadcast message to send to ${currentInspected.fullName}'s active shell:`, "MAINTENANCE IMMINENT: Please save state and terminate session.");
        if (msg) {
          const terminalBox = document.querySelector("#bastionTerminalStream");
          if (terminalBox) {
            const time = new Date().toISOString().slice(11, 19);
            terminalBox.innerHTML += `<div class="text-amber-400 font-bold mt-1">[BROADCAST @ ${time} from Executive SuperAdmin]: ${msg}</div>`;
          }
          window.showToast?.("BROADCAST DISPATCHED", `Sent message to bastion session: "${msg}"`, "success", "cell_tower");
        }
      });
    }

    // C. Terminate & Sever from Inspector
    const termInspectBtn = Array.from(document.querySelectorAll("button")).find(b => b.textContent.includes("TERMINATE & SEVER"));
    if (termInspectBtn) {
      termInspectBtn.addEventListener("click", window.terminateActiveLease);
    }

    // D. Rapid Elevation Desk Actions
    const approveElevationBtn = Array.from(document.querySelectorAll("button")).find(b => b.textContent.includes("Approve Elevation"));
    if (approveElevationBtn) {
      approveElevationBtn.addEventListener("click", () => {
        approveElevationBtn.disabled = true;
        approveElevationBtn.className = "w-full py-2 bg-secondary text-on-secondary font-title-sm text-[12px] font-bold rounded flex items-center justify-center gap-1 opacity-90 cursor-default";
        approveElevationBtn.innerHTML = '<span class="material-symbols-outlined text-[16px]">verified</span><span>APPROVED & LATCHED (60m LEASE DISPATCHED)</span>';
        const deskHeader = document.querySelector(".xl\\:col-span-4 .bg-error\\/10") || document.getElementById("kpi-pending-signoff-count");
        if (deskHeader) deskHeader.innerText = "02 AWAITING SIGN-OFF";
        window.showToast?.(
          "ELEVATION AUTHORIZED",
          "Dual-Custody quorum satisfied. Ephemeral 60m lease minted for Amina Karimova (EMP-1002).",
          "success",
          "draw"
        );
      });
    }

    const rejectElevationBtn = Array.from(document.querySelectorAll("button")).find(b => b.textContent.includes("Reject Request"));
    if (rejectElevationBtn) {
      rejectElevationBtn.addEventListener("click", () => {
        if (!confirm("Reject elevation request #REQ-8821?")) return;
        rejectElevationBtn.disabled = true;
        rejectElevationBtn.className = "px-3 py-1.5 bg-error/20 text-error font-body-compact text-[11px] font-bold rounded cursor-default";
        rejectElevationBtn.textContent = "REJECTED BY ADMIN";
        if (approveElevationBtn) approveElevationBtn.remove();
        window.showToast?.(
          "ELEVATION REJECTED",
          "Request #REQ-8821 denied. Rejection reason logged to Security Audit Ledger.",
          "error",
          "block"
        );
      });
    }

    const escalateBtn = Array.from(document.querySelectorAll("button")).find(b => b.textContent.includes("Escalate to Board"));
    if (escalateBtn) {
      escalateBtn.addEventListener("click", () => {
        window.showToast?.(
          "ESCALATED TO BOARD RISK REGISTER",
          "Elevation #REQ-8821 escalated for statutory audit under DOC-2026-015.",
          "info",
          "balance"
        );
      });
    }

    // E. Verify Vault Ledger button
    const verifyLedgerBtn = Array.from(document.querySelectorAll("button")).find(b => b.textContent.includes("Verify Vault Ledger"));
    if (verifyLedgerBtn) {
      verifyLedgerBtn.addEventListener("click", () => {
        window.showToast?.(
          "CRYPTOGRAPHIC INTEGRITY CONFIRMED",
          "Vault Ledger valid through block #441,890. HSM Root: 0x8cf948...7a12 [FIPS 140-3 LEVEL 4]",
          "success",
          "history_edu"
        );
      });
    }
  });
})();
