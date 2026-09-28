/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Audit Logs & Live Event Stream Controller
 * Dual-Custody HSM Telemetry & Multi-Parameter Filter Engine
 */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    let streamPaused = false;
    let currentSeverity = "ALL";
    let currentNode = "ALL";

    const streamBtn = document.getElementById("streamToggleBtn");
    const streamLabel = document.getElementById("streamStateLabel");
    const streamIcon = document.getElementById("streamIcon");
    const bufferLabel = document.getElementById("bufferStatus");
    const execBtn = document.getElementById("execQueryBtn");
    const queryInput = document.getElementById("logQueryInput");
    const terminalBox = document.getElementById("terminalStreamBox");
    const nodeSelect = document.getElementById("targetNodeFilter");
    const verifyLedgerBtn = document.getElementById("verifyLedgerBtn");
    const exportSyslogBtn = document.getElementById("exportSyslogBtn");
    const clearBufferBtn = document.getElementById("clearBufferBtn");
    const sevButtons = document.querySelectorAll(".audit-sev-btn");

    // ==========================================
    // 1. FILTERING ENGINE (SEVERITY + NODE + QUERY)
    // ==========================================
    function applyAuditFilters() {
      if (!terminalBox) return;
      const q = (queryInput ? queryInput.value : "").trim().toLowerCase();
      const rows = terminalBox.querySelectorAll(".log-entry-row, div[class*='font-telemetry-data']");
      let visibleCount = 0;

      rows.forEach((row) => {
        const text = row.textContent.toLowerCase();
        const sevAttr = (row.getAttribute("data-severity") || "").toUpperCase();
        const nodeAttr = (row.getAttribute("data-node") || "").toUpperCase();

        // 1. Severity filter
        let matchesSev = true;
        if (currentSeverity === "CRITICAL") {
          matchesSev = sevAttr === "CRITICAL" || text.includes("critical") || text.includes("failed") || text.includes("breach");
        } else if (currentSeverity === "WARN") {
          matchesSev = sevAttr === "WARN" || text.includes("warn") || text.includes("warning");
        } else if (currentSeverity === "INFO") {
          matchesSev = sevAttr === "INFO" || text.includes("info") || text.includes("success");
        } else if (currentSeverity === "AUDIT") {
          matchesSev = true; // All records in audit ledger
        }

        // 2. Node filter
        let matchesNode = true;
        if (currentNode !== "ALL") {
          matchesNode = nodeAttr.includes(currentNode) || text.includes(currentNode.toLowerCase());
        }

        // 3. Query search filter
        let matchesQuery = true;
        if (q !== "") {
          matchesQuery = text.includes(q);
        }

        if (matchesSev && matchesNode && matchesQuery) {
          row.style.display = "flex";
          visibleCount++;
        } else {
          row.style.display = "none";
        }
      });

      if (bufferLabel) {
        bufferLabel.textContent = `RING-BUFFER: ${visibleCount} DISPLAYED (FILTER: ${currentSeverity} / ${currentNode})`;
      }
    }

    // Attach click listeners to severity buttons
    sevButtons.forEach((btn) => {
      btn.addEventListener("click", () => {
        sevButtons.forEach((b) => {
          b.classList.remove("bg-primary", "text-on-primary", "font-bold");
          b.classList.add("bg-surface-container-high", "text-on-surface");
        });
        btn.classList.remove("bg-surface-container-high", "text-on-surface");
        btn.classList.add("bg-primary", "text-on-primary", "font-bold");

        currentSeverity = btn.getAttribute("data-severity") || "ALL";
        applyAuditFilters();

        window.showToast?.(
          "SEVERITY FILTER APPLIED",
          `Showing audit logs with severity level: ${currentSeverity}`,
          "info",
          "filter_alt"
        );
      });
    });

    // Node selector change
    if (nodeSelect) {
      nodeSelect.addEventListener("change", (e) => {
        currentNode = e.target.value;
        applyAuditFilters();

        window.showToast?.(
          "NODE FILTER ENGAGED",
          `Filtering telemetry to target node: ${currentNode}`,
          "info",
          "hub"
        );
      });
    }

    // Query Search execution
    if (execBtn) {
      execBtn.addEventListener("click", () => {
        applyAuditFilters();
        const q = (queryInput ? queryInput.value : "").trim();
        window.showToast?.(
          "QUERY EXECUTED",
          q ? `Executed search query "${q}" across event ledger.` : "Search query reset to all logs.",
          "info",
          "search"
        );
      });
    }

    if (queryInput) {
      queryInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
          applyAuditFilters();
        }
      });
      queryInput.addEventListener("input", (e) => {
        if (e.target.value === "") {
          applyAuditFilters();
        }
      });
    }

    // ==========================================
    // 2. STREAM CONTROLLER (PAUSE / RESUME)
    // ==========================================
    if (streamBtn) {
      streamBtn.addEventListener("click", function () {
        streamPaused = !streamPaused;
        if (streamPaused) {
          if (streamLabel) streamLabel.textContent = "Resume Stream";
          if (streamIcon) streamIcon.textContent = "play_arrow";
          streamBtn.classList.add("bg-secondary-container", "text-on-secondary-container");
          streamBtn.classList.remove("bg-surface-container", "text-on-surface");
          if (bufferLabel) bufferLabel.textContent = "STREAM PAUSED (HOLDING IN RAM BUFFER)";
          window.showToast?.(
            "STREAM SUSPENDED",
            "Event ingestion paused. Holding live frames in FIFO ring buffer.",
            "warn",
            "pause"
          );
        } else {
          if (streamLabel) streamLabel.textContent = "Pause Stream";
          if (streamIcon) streamIcon.textContent = "pause_circle";
          streamBtn.classList.remove("bg-secondary-container", "text-on-secondary-container");
          streamBtn.classList.add("bg-surface-container", "text-on-surface");
          if (bufferLabel) bufferLabel.textContent = "RING-BUFFER: 8,192 LINES (0 DROPPED)";
          window.showToast?.(
            "STREAM ACTIVE",
            "Real-time telemetry stream resumed from 10 ingestion bridges.",
            "success",
            "play_arrow"
          );
        }
      });
    }

    // ==========================================
    // 3. UTILITY ACTIONS (VERIFY, EXPORT, CLEAR)
    // ==========================================
    if (verifyLedgerBtn) {
      verifyLedgerBtn.addEventListener("click", () => {
        const orig = verifyLedgerBtn.innerHTML;
        verifyLedgerBtn.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin">refresh</span><span>Verifying Merkle Tree...</span>';
        verifyLedgerBtn.disabled = true;

        setTimeout(() => {
          verifyLedgerBtn.innerHTML = orig;
          verifyLedgerBtn.disabled = false;
          window.showToast?.(
            "CRYPTOGRAPHIC CONSISTENCY VALIDATED",
            "Audit Ledger Merkle root 0xe3b0c442... validated against Astana Escrow key slot #04. Chain depth: 8,192 blocks.",
            "success",
            "verified_user"
          );
        }, 800);
      });
    }

    if (exportSyslogBtn) {
      exportSyslogBtn.addEventListener("click", () => {
        const rows = terminalBox ? Array.from(terminalBox.children).map((r) => r.innerText).join("\n") : "Empty Log";
        const blob = new Blob([rows], { type: "text/plain" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `VOSTOKPRIBOR_AuditLog_RFC5424_${new Date().toISOString().slice(0, 10)}.log`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        window.showToast?.(
          "SYSLOG EXPORTED",
          "Exported cryptographic terminal stream buffer in RFC-5424 format.",
          "success",
          "download"
        );
      });
    }

    if (clearBufferBtn) {
      clearBufferBtn.addEventListener("click", () => {
        if (!confirm("Flush the local display ring buffer? (Database audit history remains immutable in permanent storage)")) return;
        if (terminalBox) {
          terminalBox.innerHTML = '<div class="text-slate-400 font-telemetry-micro text-center py-4">[LOCAL RING-BUFFER FLUSHED BY OPERATOR - STREAM CONTINUING]</div>';
        }
        if (bufferLabel) bufferLabel.textContent = "RING-BUFFER: 0 LINES (BUFFER CLEARED)";
        window.showToast?.("BUFFER FLUSHED", "Local terminal display buffer cleared.", "info", "mop");
      });
    }

    // ==========================================
    // 4. ELEVATED SESSION CARD ACTIONS
    // ==========================================
    document.querySelectorAll("button").forEach((btn) => {
      const text = btn.textContent.trim();
      if (text === "Shadow View") {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const card = btn.closest(".bg-surface-container-low");
          const name = card ? card.querySelector(".font-bold.text-on-surface")?.textContent : "Operator";
          window.showToast?.(
            "SHADOW VIEW ENGAGED",
            `Attached read-only keystream monitor to session for ${name}.`,
            "info",
            "visibility"
          );
        });
      } else if (text === "Kill Session") {
        btn.addEventListener("click", async (e) => {
          e.stopPropagation();
          const card = btn.closest(".bg-surface-container-low");
          const name = card ? card.querySelector(".font-bold.text-on-surface")?.textContent : "Operator";
          if (!confirm(`EMERGENCY SESSION KILL:\n\nTerminate elevated session for ${name}?`)) return;

          try {
            await fetch("api/privileged.php", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ action: "terminate_session", session_id: "all" }),
            });
          } catch (err) {
            console.warn(err);
          }

          if (card) {
            card.classList.add("opacity-60");
            const badge = card.querySelector(".text-error.font-bold");
            if (badge) {
              badge.textContent = "TERMINATED";
              badge.className = "font-telemetry-micro text-telemetry-micro text-error font-bold";
            }
          }
          btn.disabled = true;
          btn.textContent = "Killed";
          window.showToast?.(
            "SESSION TERMINATED",
            `Elevated session for ${name} severed. Hardware keyring revoked.`,
            "error",
            "cancel"
          );
        });
      } else if (text === "Audit Stream") {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const card = btn.closest(".bg-surface-container-low");
          const name = card ? card.querySelector(".font-bold.text-on-surface")?.textContent : "";
          if (queryInput && name) {
            const empMatch = name.match(/EMP-\d+/);
            queryInput.value = empMatch ? empMatch[0] : name;
            applyAuditFilters();
            window.showToast?.("FILTER APPLIED", `Filtering terminal stream for ${name}.`, "info", "search");
          }
        });
      } else if (text === "Revoke MFA") {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const card = btn.closest(".bg-surface-container-low");
          const name = card ? card.querySelector(".font-bold.text-on-surface")?.textContent : "Operator";
          if (!confirm(`Revoke active MFA token challenge for ${name}?`)) return;

          if (card) {
            const mfaBadge = card.querySelector(".font-security-stamp");
            if (mfaBadge) {
              mfaBadge.textContent = "MFA REVOKED";
              mfaBadge.className = "font-security-stamp text-[10px] px-space-xs bg-error text-on-error rounded";
            }
          }
          btn.disabled = true;
          btn.textContent = "Revoked";
          window.showToast?.(
            "MFA REVOKED",
            `MFA authorization revoked for ${name}. Step-up challenge forced.`,
            "warn",
            "key_off"
          );
        });
      }
    });

    // ==========================================
    // 5. LIVE SIMULATED INGESTION (EVERY 4 SECONDS)
    // ==========================================
    const sampleLiveEvents = [
      { sys: "SYS-06", lvl: "AUDIT", msg: "SOP-06 Offboarding check executed: former staff credential purge verified.", hash: "0x8f2a...c104" },
      { sys: "SYS-08", lvl: "WARN", msg: "TKT-2026-005 escalated to Governance by EMP-1018 (Leonid Volkov).", hash: "0x3c11...9a41" },
      { sys: "SYS-09", lvl: "INFO", msg: "DOC-2026-001 access requested by CGO enclave terminal (EMP-1005).", hash: "0x7e44...5f82" },
      { sys: "SYS-07", lvl: "AUDIT", msg: "INV-2026-002 reconciliation pulse: €80,000 pending attestation match.", hash: "0x1b28...43da" },
      { sys: "SYS-11", lvl: "INFO", msg: "Jurisdiction Merkle root verified against Astana Escrow key slot #04.", hash: "0x99cb...0112" },
      { sys: "SYS-02", lvl: "CRITICAL", msg: "Hydraulic pressure safety interlock tripped above 320 bar limit on Valve #3.", hash: "0x44fa...77b1" },
    ];

    let eventIdx = 0;
    setInterval(() => {
      if (streamPaused || !terminalBox) return;

      const ev = sampleLiveEvents[eventIdx % sampleLiveEvents.length];
      eventIdx++;

      const now = new Date();
      const timeStr = now.toISOString().replace("T", " ").slice(0, 19);

      const row = document.createElement("div");
      const isCrit = ev.lvl === "CRITICAL";
      const isWarn = ev.lvl === "WARN";

      row.className = `log-entry-row flex items-start gap-space-xs font-telemetry-data text-telemetry-data py-space-2xs px-space-xs ${isCrit ? "bg-error-container/20 rounded hover:bg-error-container/30" : "hover:bg-surface-container-highest/10"} transition-colors`;
      row.setAttribute("data-severity", ev.lvl);
      row.setAttribute("data-node", ev.sys);

      row.innerHTML = `
        <span class="text-on-primary-container select-none font-telemetry-micro w-8 text-right shrink-0">LIVE</span>
        <span class="text-secondary-fixed shrink-0 font-telemetry-micro">${timeStr}</span>
        <span class="${isCrit ? "bg-error text-on-error" : isWarn ? "bg-tertiary-container text-tertiary-fixed" : "bg-primary-container text-on-primary-container"} px-space-2xs py-0 rounded font-security-stamp text-security-stamp shrink-0">${ev.lvl}</span>
        <span class="text-secondary-fixed-dim font-bold shrink-0">[${ev.sys}]</span>
        <span class="text-tertiary-fixed-dim shrink-0">[TELEMETRY]</span>
        <span class="text-on-primary">
          <strong class="text-secondary-fixed underline">SYS-AUTO</strong> (Live Daemon Relay) ${ev.msg}
          <span class="text-on-primary-container font-mono text-[10px] ml-1">[Hash: ${ev.hash}]</span>
        </span>
      `;

      // Check current filters before displaying
      if (currentSeverity !== "ALL" && currentSeverity !== ev.lvl) {
        row.style.display = "none";
      }
      if (currentNode !== "ALL" && currentNode !== ev.sys) {
        row.style.display = "none";
      }

      terminalBox.insertBefore(row, terminalBox.firstChild);

      // Keep buffer bounded
      if (terminalBox.children.length > 150) {
        terminalBox.removeChild(terminalBox.lastChild);
      }
    }, 4000);
  });
})();
