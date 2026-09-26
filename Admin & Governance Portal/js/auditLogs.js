/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Audit Logs & Live Event Stream Controller
 */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    let streamPaused = false;
    const streamBtn = document.getElementById("streamToggleBtn");
    const streamLabel = document.getElementById("streamStateLabel");
    const streamIcon = document.getElementById("streamIcon");
    const bufferLabel = document.getElementById("bufferStatus");
    const execBtn = document.getElementById("execQueryBtn");
    const queryInput = document.getElementById("logQueryInput");
    const terminalBox = document.getElementById("terminalStreamBox");

    // 1. Stream Controller Pause / Resume
    if (streamBtn) {
      streamBtn.addEventListener("click", function () {
        streamPaused = !streamPaused;
        if (streamPaused) {
          if (streamLabel) streamLabel.textContent = "Resume Stream";
          if (streamIcon) streamIcon.textContent = "play_arrow";
          streamBtn.classList.add(
            "bg-secondary-container",
            "text-on-secondary-container",
          );
          streamBtn.classList.remove("bg-surface-container", "text-on-surface");
          if (bufferLabel)
            bufferLabel.textContent = "STREAM PAUSED (HOLDING IN RAM BUFFER)";
          window.showToast(
            "STREAM SUSPENDED",
            "Event ingestion paused. Holding live frames in FIFO ring buffer.",
            "warn",
            "pause",
          );
        } else {
          if (streamLabel) streamLabel.textContent = "Pause Stream";
          if (streamIcon) streamIcon.textContent = "pause_circle";
          streamBtn.classList.remove(
            "bg-secondary-container",
            "text-on-secondary-container",
          );
          streamBtn.classList.add("bg-surface-container", "text-on-surface");
          if (bufferLabel)
            bufferLabel.textContent = "RING-BUFFER: 8,192 LINES (0 DROPPED)";
          window.showToast(
            "STREAM ACTIVE",
            "Real-time telemetry stream resumed from 10 ingestion bridges.",
            "success",
            "play_arrow",
          );
        }
      });
    }

    // 2. Fixed Filter Query Execution (Resolving Hardcoded Bug)
    function executeLogFilter() {
      if (!queryInput || !terminalBox) return;
      const q = queryInput.value.trim().toLowerCase();
      const rows = terminalBox.children;
      let matchCount = 0;

      for (let i = 0; i < rows.length; i++) {
        const text = rows[i].textContent.toLowerCase();
        if (q === "" || text.includes(q)) {
          rows[i].style.display = "flex";
          matchCount++;
        } else {
          rows[i].style.display = "none";
        }
      }

      if (q !== "") {
        window.showToast(
          "QUERY FILTERED",
          `Found ${matchCount} matching audit events for "${q}".`,
          "info",
          "search",
        );
      }
    }

    if (execBtn) {
      execBtn.addEventListener("click", executeLogFilter);
    }

    if (queryInput) {
      queryInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
          executeLogFilter();
        }
      });
      // Auto filter on clear
      queryInput.addEventListener("input", (e) => {
        if (e.target.value === "") {
          executeLogFilter();
        }
      });
    }

    // 3. Simulated Live Log Ingestion (When stream is running)
    const sampleLiveEvents = [
      {
        sys: "SYS-06",
        lvl: "AUDIT",
        msg: "SOP-06 Offboarding check executed: former staff credential purge verified.",
        hash: "0x8f2a...c104",
      },
      {
        sys: "SYS-08",
        lvl: "WARN",
        msg: "TKT-2026-005 escalated to Governance by EMP-1018 (Leonid Volkov).",
        hash: "0x3c11...9a41",
      },
      {
        sys: "SYS-09",
        lvl: "INFO",
        msg: "DOC-2026-001 access requested by CGO enclave terminal (EMP-1005).",
        hash: "0x7e44...5f82",
      },
      {
        sys: "SYS-07",
        lvl: "AUDIT",
        msg: "INV-2026-002 reconciliation pulse: €80,000 pending attestation match.",
        hash: "0x1b28...43da",
      },
      {
        sys: "SYS-11",
        lvl: "INFO",
        msg: "Jurisdiction Merkle root verified against Astana Escrow key slot #04.",
        hash: "0x99cb...0112",
      },
    ];

    let eventIdx = 0;
    setInterval(() => {
      if (streamPaused || !terminalBox) return;

      const ev = sampleLiveEvents[eventIdx % sampleLiveEvents.length];
      eventIdx++;

      const now = new Date();
      const timeStr = `${String(now.getHours()).padStart(2, "0")}:${String(now.getMinutes()).padStart(2, "0")}:${String(now.getSeconds()).padStart(2, "0")}.${String(Math.floor(now.getMilliseconds() / 10)).padStart(2, "0")} UTC+6`;

      const row = document.createElement("div");
      row.className =
        "flex items-center justify-between py-1 px-2 border-b border-white/5 font-mono text-[12px] log-entry-row";
      row.innerHTML = `
                <div class="flex items-center gap-2">
                    <span class="text-on-primary-container text-[11px]">${timeStr}</span>
                    <span class="px-1 py-0.5 rounded text-[10px] font-bold ${ev.lvl === "WARN" ? "bg-amber-500/20 text-amber-400" : "bg-secondary-container/20 text-secondary-fixed"}">${ev.sys}</span>
                    <span class="px-1 py-0.2 rounded text-[9px] font-extrabold ${ev.lvl === "WARN" ? "bg-amber-500 text-black" : "bg-[#1b3a5c] text-[#abc9f2]"}">${ev.lvl}</span>
                    <span class="text-white">${ev.msg}</span>
                </div>
                <div class="flex items-center gap-2 text-on-primary-container text-[11px]">
                    <span class="font-mono text-secondary-fixed">${ev.hash}</span>
                    <span class="material-symbols-outlined text-[14px] text-secondary">verified</span>
                </div>
            `;

      terminalBox.insertBefore(row, terminalBox.firstChild);

      // Cap at 100 rows in DOM
      if (terminalBox.children.length > 100) {
        terminalBox.removeChild(terminalBox.lastChild);
      }
    }, 5000);

    // 4. Export Log Action
    const exportBtn = Array.from(document.querySelectorAll("button")).find(
      (b) =>
        b.textContent.includes("Export") || b.textContent.includes("Save Log"),
    );
    if (exportBtn) {
      exportBtn.addEventListener("click", () => {
        const rows = terminalBox
          ? Array.from(terminalBox.children)
              .map((r) => r.innerText)
              .join("\n")
          : "Empty Log";
        const blob = new Blob([rows], { type: "text/plain" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `VOSTOKPRIBOR_AuditLog_${new Date().toISOString().slice(0, 10)}.log`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        window.showToast(
          "AUDIT LOG EXPORTED",
          "Exported cryptographic terminal stream buffer.",
          "success",
          "download",
        );
      });
    }
  });
})();
