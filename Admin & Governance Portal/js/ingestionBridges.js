/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Telemetry Ingestion Bridges Module (SYS 01-10 Pipeline)
 * High-Trust Industrial Bus & Buffer Controller
 */

(function () {
  "use strict";

  // Global bridge selector function
  window.selectBridge = function (
    id,
    name,
    location,
    protocol,
    eps,
    latency,
    buffer,
    status,
    classification
  ) {
    const idElem = document.getElementById("inspector-id") || document.getElementById("inspector-bridge-id");
    const nameElem = document.getElementById("inspector-name") || document.getElementById("inspector-bridge-title");
    const locElem = document.getElementById("inspector-location");
    const buffText = document.getElementById("inspector-buffer-text");
    const barElem = document.getElementById("inspector-buffer-bar");
    const badgeElem = document.getElementById("inspector-status-badge");
    const stripElem = document.getElementById("inspector-strip");

    if (idElem) idElem.textContent = id;
    if (nameElem) nameElem.textContent = name;
    if (locElem) locElem.textContent = location || "Industrial Enclave Relay";

    const bufNum = parseInt(buffer, 10) || 45;
    if (buffText) {
      const lines = Math.floor(bufNum * 81.92);
      buffText.textContent = `${lines.toLocaleString()} / 8,192 LINES (${bufNum}%)`;
    }
    if (barElem) {
      barElem.style.width = `${bufNum}%`;
      if (bufNum > 70) {
        barElem.className = "bg-error h-full transition-all duration-500 animate-pulse";
      } else if (bufNum > 40) {
        barElem.className = "bg-[#D9822B] h-full transition-all duration-500";
      } else {
        barElem.className = "bg-secondary h-full transition-all duration-500";
      }
    }

    if (badgeElem && stripElem) {
      if (bufNum > 70) {
        badgeElem.textContent = "HIGH BUFFER OCCUPANCY";
        badgeElem.className = "font-label-uppercase text-label-uppercase text-error font-bold";
        stripElem.className = "absolute left-0 top-0 bottom-0 w-1 bg-error";
      } else if (bufNum > 40) {
        badgeElem.textContent = "MODERATE LOAD // NOMINAL";
        badgeElem.className = "font-label-uppercase text-label-uppercase text-[#D9822B] font-bold";
        stripElem.className = "absolute left-0 top-0 bottom-0 w-1 bg-[#D9822B]";
      } else {
        badgeElem.textContent = "BUFFER OPTIMAL (SLA MET)";
        badgeElem.className = "font-label-uppercase text-label-uppercase text-secondary font-bold";
        stripElem.className = "absolute left-0 top-0 bottom-0 w-1 bg-secondary";
      }
    }

    // Append to live log
    const logBox = document.getElementById("live-event-stream");
    if (logBox) {
      const entry = document.createElement("div");
      entry.className = "flex items-start gap-space-xs py-0.5 border-b border-white/5 font-mono text-[11px]";
      const timeStr = new Date().toTimeString().split(" ")[0];
      entry.innerHTML = `<span class="text-on-surface-variant">${timeStr}</span> <span class="text-primary font-bold">[${id}]</span> <span class="text-on-surface">Operator selected ${name} (${location})</span>`;
      logBox.insertBefore(entry, logBox.firstChild);
    }
  };

  document.addEventListener("DOMContentLoaded", () => {
    // 1. Table row clicks
    const rows = document.querySelectorAll(".bridge-table-row");
    rows.forEach((row) => {
      row.addEventListener("click", function (e) {
        // If clicking inside the INTEGRATION button, let button handle it
        if (e.target.closest("button")) return;

        rows.forEach((r) => r.classList.remove("ring-1", "ring-primary", "bg-surface-container-low"));
        this.classList.add("ring-1", "ring-primary", "bg-surface-container-low");

        const link = this.getAttribute("data-link") || "LINK";
        const src = this.getAttribute("data-source") || "SYS";
        const tgt = this.getAttribute("data-target") || "SYS";
        const proto = this.getAttribute("data-protocol") || "REST";
        const auth = this.getAttribute("data-auth") || "HMAC";
        const data = this.getAttribute("data-data") || "Telemetry Stream";
        const tx = this.getAttribute("data-tx") || "4200";

        const pseudoBuffer = Math.min(84, Math.max(18, (parseInt(tx, 10) % 70) + 18));
        window.selectBridge(
          src,
          `${src} → ${tgt} Ingestion Relay`,
          `${data} // ${proto} (${auth})`,
          proto,
          tx,
          "0.8ms",
          pseudoBuffer,
          "ONLINE",
          "INTERNAL"
        );
      });
    });

    // 2. Buffer expander button
    const btnIncreaseBuffer = document.getElementById("btn-increase-buffer");
    if (btnIncreaseBuffer) {
      btnIncreaseBuffer.addEventListener("click", function () {
        const bar = document.getElementById("inspector-buffer-bar");
        const buffText = document.getElementById("inspector-buffer-text");
        const badge = document.getElementById("inspector-status-badge");
        const strip = document.getElementById("inspector-strip");

        if (bar) {
          bar.style.width = "48%";
          bar.className = "bg-secondary h-full transition-all duration-500";
        }
        if (buffText) buffText.textContent = "6,881 / 14,336 LINES (48%)";
        if (badge) {
          badge.textContent = "BUFFER EXPANDED (+4MB COMMITTED)";
          badge.className = "font-label-uppercase text-label-uppercase text-secondary font-bold";
        }
        if (strip) strip.className = "absolute left-0 top-0 bottom-0 w-1 bg-secondary";

        this.innerHTML = '<span class="material-symbols-outlined text-[16px]">check_circle</span><span>BUFFER EXPANDED (+4 MB ALLOCATED)</span>';
        this.disabled = true;
        this.classList.add("opacity-70");

        window.showToast?.(
          "BUFFER EXPANDED",
          "Committed +4 MB RAM pool from reserved kernel buffer. Overflow hazard mitigated.",
          "success",
          "memory"
        );
      });
    }

    // 3. Force flush button
    const btnForceFlush = document.getElementById("btn-force-flush");
    if (btnForceFlush) {
      btnForceFlush.addEventListener("click", function () {
        const logBox = document.getElementById("live-event-stream");
        if (logBox) {
          const entry = document.createElement("div");
          entry.className = "flex items-start gap-space-xs text-secondary font-bold py-0.5 border-b border-white/5 font-mono text-[11px]";
          entry.innerHTML = `<span class="text-on-surface-variant">NOW</span><span>[PIPELINE]</span><span>Manual Kafka ring-buffer checkpoint flushed (6,881 lines written to persistent disk)</span>`;
          logBox.insertBefore(entry, logBox.firstChild);
        }
        const bar = document.getElementById("inspector-buffer-bar");
        const buffText = document.getElementById("inspector-buffer-text");
        const badge = document.getElementById("inspector-status-badge");
        if (bar) {
          bar.style.width = "12%";
          bar.className = "bg-secondary h-full transition-all duration-500";
        }
        if (buffText) buffText.textContent = "983 / 8,192 LINES (12%)";
        if (badge) {
          badge.textContent = "BUFFER OPTIMAL (POST-FLUSH)";
          badge.className = "font-label-uppercase text-label-uppercase text-secondary font-bold";
        }

        window.showToast?.(
          "BUFFER FLUSHED",
          "Manual ring-buffer purge committed to persistent write-once store.",
          "info",
          "cleaning_services"
        );
      });
    }

    // 4. Reroute Relay button
    const btnReroute = document.getElementById("btn-reroute-relay");
    if (btnReroute) {
      btnReroute.addEventListener("click", function () {
        const logBox = document.getElementById("live-event-stream");
        if (logBox) {
          const entry = document.createElement("div");
          entry.className = "flex items-start gap-space-xs text-primary font-bold py-0.5 border-b border-white/5 font-mono text-[11px]";
          entry.innerHTML = `<span class="text-on-surface-variant">NOW</span><span>[FAIL-OVER]</span><span>Telemetry route switched to secondary optical relay node 03-B. Zero frame drop.</span>`;
          logBox.insertBefore(entry, logBox.firstChild);
        }

        this.innerHTML = '<span class="material-symbols-outlined text-[16px]">alt_route</span><span>ACTIVE ROUTE: RELAY 03-B</span>';
        this.classList.add("bg-secondary-container", "text-on-secondary-container");

        window.showToast?.(
          "RELAY RE-ROUTED",
          "Active telemetry stream diverted to secondary cold-runner bypass relay 03-B.",
          "success",
          "alt_route"
        );
      });
    }

    // 5. Test Heartbeats button
    const btnHeartbeats = document.getElementById("btn-test-heartbeats");
    if (btnHeartbeats) {
      btnHeartbeats.addEventListener("click", function () {
        const orig = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin">sensors</span><span>TESTING...</span>';

        setTimeout(() => {
          this.innerHTML = '<span class="material-symbols-outlined text-[14px]">done_all</span><span>10/10 NOMINAL</span>';
          this.classList.remove("bg-primary");
          this.classList.add("bg-secondary");

          setTimeout(() => {
            this.innerHTML = orig;
            this.classList.add("bg-primary");
            this.classList.remove("bg-secondary");
            this.disabled = false;
          }, 3000);

          const logBox = document.getElementById("live-event-stream");
          if (logBox) {
            const entry = document.createElement("div");
            entry.className = "flex items-start gap-space-xs text-secondary font-bold py-0.5 border-b border-white/5 font-mono text-[11px]";
            entry.innerHTML = `<span class="text-on-surface-variant">NOW</span><span>[ICMP/gRPC]</span><span>Broadcast echo received from all 10 nodes (min: 0.4ms, max: 2.1ms, avg: 0.9ms).</span>`;
            logBox.insertBefore(entry, logBox.firstChild);
          }

          window.showToast?.(
            "HEARTBEAT BROADCAST COMPLETE",
            "10/10 Ingestion bridges confirmed online with synchronous dual-HMAC cryptographic heartbeat.",
            "success",
            "sensors"
          );
        }, 800);
      });
    }

    // 6. Flush All Buffers button
    const btnFlushAll = document.getElementById("btn-flush-buffers");
    if (btnFlushAll) {
      btnFlushAll.addEventListener("click", function () {
        const bar = document.getElementById("inspector-buffer-bar");
        const buffText = document.getElementById("inspector-buffer-text");
        if (bar) bar.style.width = "10%";
        if (buffText) buffText.textContent = "819 / 8,192 LINES (10%)";

        window.showToast?.(
          "GLOBAL FLUSH EXECUTED",
          "All fleet ring-buffers written to persistent storage partitions.",
          "info",
          "cleaning_services"
        );
      });
    }

    // 7. Export Topology button
    const btnExportTopology = document.getElementById("btn-export-topology");
    if (btnExportTopology) {
      btnExportTopology.addEventListener("click", function () {
        const rows = document.querySelectorAll(".bridge-table-row");
        const topology = [];
        rows.forEach((r) => {
          topology.push({
            link: r.getAttribute("data-link"),
            source: r.getAttribute("data-source"),
            target: r.getAttribute("data-target"),
            protocol: r.getAttribute("data-protocol"),
            auth: r.getAttribute("data-auth"),
            status: r.getAttribute("data-status"),
            txCount: r.getAttribute("data-tx")
          });
        });

        const blob = new Blob([JSON.stringify({ station: "ALMATY-CENTRAL", timestamp: new Date().toISOString(), nodes: topology }, null, 2)], {
          type: "application/json"
        });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `VOSTOKPRIBOR_Topology_SYS01_10_${Date.now()}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        window.showToast?.(
          "TOPOLOGY EXPORTED",
          "Saved 10-node ingestion architecture schema to JSON file.",
          "success",
          "account_tree"
        );
      });
    }

    // 8. Bridge Quarantine button
    const btnQuarantine = document.getElementById("btn-bridge-quarantine");
    if (btnQuarantine) {
      btnQuarantine.addEventListener("click", function () {
        if (confirm("DEFENSE INTERLOCK: Quarantine selected ingestion bridge and divert inbound packets to cold isolation sandbox?")) {
          window.location.href = "EmergencyLockdown.php";
        }
      });
    }

    // 9. Poll / Refresh Grid button
    const btnRefreshGrid = document.getElementById("btn-refresh-grid");
    if (btnRefreshGrid) {
      btnRefreshGrid.addEventListener("click", function () {
        const orig = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin">refresh</span><span>POLLING...</span>';
        setTimeout(() => {
          this.innerHTML = orig;
          this.disabled = false;
          window.showToast?.("TELEMETRY SYNCHRONIZED", "Queried live EPS counters from 10 nodes.", "info", "refresh");
        }, 600);
      });
    }

    // 10. Node filter search
    const filterInput = document.getElementById("node-filter-input");
    if (filterInput) {
      filterInput.addEventListener("input", function (e) {
        const q = e.target.value.toUpperCase();
        const rows = document.querySelectorAll("#bridge-table-body tr");
        rows.forEach((r) => {
          const text = r.innerText.toUpperCase();
          r.style.display = text.includes(q) ? "" : "none";
        });
      });
    }
  });
})();
