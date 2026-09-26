/**
 * VOSTOKPRIBOR Universal Integration Panel Client
 * Manages test dispatches and real-time log refreshes via api/integration_router.php
 */
(function (global) {
  "use strict";

  global.triggerVostokIntegration = function (linkCode, btnElement) {
    if (!confirm("Execute live integration exchange for link " + linkCode + "?")) {
      return;
    }

    if (btnElement) {
      btnElement.disabled = true;
      btnElement.innerHTML = "<span>&#8987; Dispatching...</span>";
    }

    fetch("../api/integration_router.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        link_code: linkCode,
        action: "UI_INTERACTIVE_DISPATCH",
        payload: {
          client_time: new Date().toISOString(),
          trigger: "WEB_CONSOLE"
        }
      })
    })
      .then((res) => res.json())
      .then((data) => {
        if (btnElement) {
          btnElement.disabled = false;
          btnElement.innerHTML = "<span>&#9889; Dispatch Test Exchange</span>";
        }

        if (data.success) {
          alert(
            "SUCCESS: Integration " +
              linkCode +
              " executed!\nLog ID #" +
              data.log_id +
              "\nStatus: " +
              data.status_code +
              " OK\nProtocol: " +
              data.protocol
          );
          const rootEl = document.getElementById("vostok-integration-root");
          const sysCode = rootEl ? rootEl.getAttribute("data-system-code") : "";
          if (sysCode) {
            global.refreshVostokLogs(sysCode);
          }
        } else {
          alert("ERROR: " + (data.error || "Failed to dispatch integration."));
        }
      })
      .catch((err) => {
        if (btnElement) {
          btnElement.disabled = false;
          btnElement.innerHTML = "<span>&#9889; Dispatch Test Exchange</span>";
        }
        alert("Network Error: " + err.message);
      });
  };

  global.refreshVostokLogs = function (sysCode) {
    fetch("../api/integration_router.php?system=" + encodeURIComponent(sysCode) + "&include_logs=1")
      .then((res) => res.json())
      .then((data) => {
        if (data.success && data.logs) {
          const tbody = document.getElementById("vostok-logs-tbody");
          if (!tbody) return;

          if (data.logs.length === 0) {
            tbody.innerHTML =
              '<tr><td colspan="8" class="text-center p-8 text-slate-500">No integration events recorded yet for this system.</td></tr>';
            return;
          }

          tbody.innerHTML = data.logs
            .map((log) => {
              const isSuccess = log.status_code >= 200 && log.status_code < 300;
              const statusClass = isSuccess ? "success" : "error";
              const statusText = isSuccess ? log.status_code + " OK" : log.status_code + " ERR";
              const payloadEsc = String(log.payload_summary || "")
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;");
              return `
              <tr>
                <td class="font-mono font-semibold text-cyan-400">#${log.log_id}</td>
                <td class="font-mono text-xs text-slate-200">${log.link_code}</td>
                <td class="font-mono text-xs">${log.source_system_id} &rarr; ${log.target_system_id}</td>
                <td class="text-xs text-slate-400">${log.api_protocol}</td>
                <td><span class="vostok-status-pill ${statusClass}">${statusText}</span></td>
                <td class="font-mono text-xs text-slate-300">${log.actor_id}</td>
                <td class="text-xs text-slate-400 truncate max-w-xs" title="${payloadEsc}">${payloadEsc}</td>
                <td class="font-mono text-xs text-slate-500">${log.executed_at}</td>
              </tr>
            `;
            })
            .join("");
        }
      })
      .catch((err) => console.error("Failed to refresh integration logs:", err));
  };
})(window);
