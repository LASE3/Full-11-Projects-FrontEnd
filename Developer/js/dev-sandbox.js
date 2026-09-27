/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 10: Interactive API Sandbox & Testing Console Module
 * Full Database Integration with Live Dispatch, Presets & History
 */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    const methodSelect = document.getElementById("sandboxMethod");
    const urlInput = document.getElementById("sandboxUrl");
    const bodyEditor = document.getElementById("sandboxBody");
    const sendBtn = document.getElementById("btnSendSandbox");
    const respCode = document.getElementById("responseStatusCode");
    const respTime = document.getElementById("responseTime");
    const respSize = document.getElementById("responseSize");
    const respBody = document.getElementById("responseBodyDisplay");

    // Check if navigated from "Try in Sandbox" button
    const savedMethod = sessionStorage.getItem("vk_sandbox_method");
    const savedUrl = sessionStorage.getItem("vk_sandbox_url");
    const savedBody = sessionStorage.getItem("vk_sandbox_body");

    if (savedMethod && methodSelect) methodSelect.value = savedMethod;
    if (savedUrl && urlInput) urlInput.value = savedUrl;
    if (savedBody && bodyEditor && savedBody.trim()) bodyEditor.value = savedBody;

    sessionStorage.removeItem("vk_sandbox_method");
    sessionStorage.removeItem("vk_sandbox_url");
    sessionStorage.removeItem("vk_sandbox_body");

    // Quick Preset Buttons Click
    document.addEventListener("click", (e) => {
      const btn = e.target.closest(".btn-preset");
      if (btn) {
        const method = btn.getAttribute("data-method") || "GET";
        const url = btn.getAttribute("data-url") || "";
        const sampleBody = btn.getAttribute("data-body") || "";

        if (methodSelect) methodSelect.value = method;
        if (urlInput) urlInput.value = url;
        if (bodyEditor) bodyEditor.value = sampleBody;

        window.showToast("PRESET LOADED", `Loaded endpoint: ${url}`, "info", "bookmark");
      }
    });

    // Delete Preset
    document.addEventListener("click", async (e) => {
      const delBtn = e.target.closest(".btn-delete-preset");
      if (delBtn) {
        const id = delBtn.getAttribute("data-id");
        if (!confirm("Delete this sandbox preset from database?")) return;

        try {
          const res = await fetch("api/sandbox.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "delete_preset", id: id })
          });
          const result = await res.json();
          if (result.success) {
            delBtn.parentElement.remove();
            window.showToast("PRESET DELETED", "Preset removed from database.", "warn", "delete");
          }
        } catch (err) {
          window.showToast("ERROR", err.message, "error");
        }
      }
    });

    // Save Preset Modal Handlers
    const savePresetModal = document.getElementById("savePresetModal");
    const btnOpenSavePreset = document.getElementById("btnOpenSavePreset");
    const btnClosePresetModal = document.getElementById("btnClosePresetModal");
    const btnCancelPresetModal = document.getElementById("btnCancelPresetModal");
    const btnConfirmSavePreset = document.getElementById("btnConfirmSavePreset");

    if (btnOpenSavePreset && savePresetModal) {
      btnOpenSavePreset.addEventListener("click", () => {
        document.getElementById("presetTitleInput").value = "";
        document.getElementById("presetDescInput").value = "";
        savePresetModal.style.display = "flex";
      });
    }

    const closePresetModal = () => {
      if (savePresetModal) savePresetModal.style.display = "none";
    };
    if (btnClosePresetModal) btnClosePresetModal.addEventListener("click", closePresetModal);
    if (btnCancelPresetModal) btnCancelPresetModal.addEventListener("click", closePresetModal);

    if (btnConfirmSavePreset) {
      btnConfirmSavePreset.addEventListener("click", async () => {
        const title = document.getElementById("presetTitleInput").value.trim();
        const desc = document.getElementById("presetDescInput").value.trim();
        const method = methodSelect ? methodSelect.value : "GET";
        const url = urlInput ? urlInput.value.trim() : "";
        const body = bodyEditor ? bodyEditor.value : "";

        if (!title || !url) {
          window.showToast("VALIDATION ERROR", "Title and URL are required.", "error");
          return;
        }

        try {
          const res = await fetch("api/sandbox.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              action: "create_preset",
              title: title,
              method: method,
              url: url,
              sample_body: body,
              description: desc
            })
          });
          const result = await res.json();
          if (result.success) {
            window.showToast("PRESET SAVED", `Saved preset "${title}" to database.`, "success", "bookmark_added");
            closePresetModal();
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast("ERROR", result.error || "Failed to save preset.", "error");
          }
        } catch (err) {
          window.showToast("ERROR", err.message, "error");
        }
      });
    }

    // Live Request Dispatch Simulation with Real Database Persistence
    const executeDispatch = async () => {
      const url = urlInput ? urlInput.value.trim() : "";
      const method = methodSelect ? methodSelect.value : "GET";
      const body = bodyEditor ? bodyEditor.value : "";

      if (!url) {
        window.showToast("INPUT ERROR", "Request URL is required.", "error");
        return;
      }

      const origText = sendBtn.innerHTML;
      sendBtn.disabled = true;
      sendBtn.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span><span>Sending...</span>';

      try {
        const res = await fetch("api/sandbox.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            action: "dispatch",
            method: method,
            url: url,
            body: body
          })
        });

        const result = await res.json();
        if (result.success) {
          const d = result.data;
          if (respCode) {
            respCode.textContent = `${d.status} ${d.statusText}`;
            respCode.className = `vk-status-badge ${d.status >= 200 && d.status < 300 ? "status-active" : "status-revoked"}`;
          }
          if (respTime) respTime.textContent = d.time;
          if (respSize) respSize.textContent = d.size;
          if (respBody) {
            respBody.textContent = JSON.stringify(d.data, null, 2);
          }

          window.showToast(
            "HTTP DISPATCH COMPLETED",
            `Gateway response: ${d.status} ${d.statusText} (${d.time}) logged to database.`,
            "success",
            "cloud_done"
          );

          // Prepend row to history table
          const tbody = document.getElementById("sandboxLogsBody");
          if (tbody) {
            const tr = document.createElement("tr");
            const nowStr = new Date().toISOString().replace("T", " ").substring(0, 19);
            tr.innerHTML = `
              <td><span class="endpoint-badge-${method.toLowerCase()}">${method}</span></td>
              <td><code style="font-family: var(--font-mono); font-size: 12px;">${url}</code></td>
              <td><span class="vk-status-badge ${d.status >= 200 && d.status < 300 ? "status-active" : "status-revoked"}">${d.status}</span></td>
              <td style="font-family: var(--font-mono); font-size: 12px;">${d.time}</td>
              <td style="font-family: var(--font-mono); font-size: 11px; color: var(--vk-neutral-500);">${d.size}</td>
              <td style="font-size: 11px; color: var(--vk-neutral-500);">${nowStr}</td>
              <td style="text-align: right;">
                <button class="btn-crud-action btn-rerun-log" data-method="${method}" data-url="${url}" data-body="${encodeURIComponent(body)}">
                  <span class="material-symbols-outlined text-[13px]">replay</span>
                </button>
              </td>
            `;
            tbody.insertBefore(tr, tbody.firstChild);
          }
        } else {
          window.showToast("DISPATCH ERROR", result.error || "Gateway dispatch failed.", "error");
        }
      } catch (err) {
        window.showToast("NETWORK ERROR", err.message, "error");
      } finally {
        sendBtn.disabled = false;
        sendBtn.innerHTML = origText;
      }
    };

    if (sendBtn) {
      sendBtn.addEventListener("click", executeDispatch);
    }

    // Re-run from Log
    document.addEventListener("click", (e) => {
      const rerunBtn = e.target.closest(".btn-rerun-log");
      if (rerunBtn) {
        const method = rerunBtn.getAttribute("data-method") || "GET";
        const url = rerunBtn.getAttribute("data-url") || "";
        let body = rerunBtn.getAttribute("data-body") || "";
        try {
          body = decodeURIComponent(body);
        } catch (e) {}

        if (methodSelect) methodSelect.value = method;
        if (urlInput) urlInput.value = url;
        if (bodyEditor) bodyEditor.value = body;

        executeDispatch();
      }
    });

    // Clear Sandbox Logs
    const clearLogsBtn = document.getElementById("btnClearSandboxLogs");
    if (clearLogsBtn) {
      clearLogsBtn.addEventListener("click", async () => {
        if (!confirm("Clear all sandbox dispatch logs from database?")) return;

        try {
          const res = await fetch("api/sandbox.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "clear_logs" })
          });
          const result = await res.json();
          if (result.success) {
            const tbody = document.getElementById("sandboxLogsBody");
            if (tbody) tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; color: var(--vk-neutral-500); padding: 20px;">No requests dispatched yet.</td></tr>';
            window.showToast("HISTORY CLEARED", "Database sandbox execution ledger cleared.", "info", "delete_sweep");
          }
        } catch (err) {
          window.showToast("ERROR", err.message, "error");
        }
      });
    }
  });
})();
