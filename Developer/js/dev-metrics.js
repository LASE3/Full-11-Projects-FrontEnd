/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 10: Usage Metrics, Quotas & Webhook Telemetry Module
 * Full CRUD & Real-Time Gateway Event Dispatcher
 */

(function () {
  "use strict";

  const API_URL = "api/metrics.php";

  document.addEventListener("DOMContentLoaded", () => {
    // 1. Timeframe selector
    document.querySelectorAll(".timeframe-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        document.querySelectorAll(".timeframe-btn").forEach((b) => {
          b.classList.remove("vk-btn-primary");
          b.classList.add("vk-btn-outline");
        });
        this.classList.remove("vk-btn-outline");
        this.classList.add("vk-btn-primary");

        const range = this.getAttribute("data-range");
        if (window.showToast) {
          window.showToast(
            "TELEMETRY FILTERED",
            `Aggregated metrics recomputed for timeframe: ${range}`,
            "info",
            "calendar_today"
          );
        }
      });
    });

    // 2. Webhook Modal Elements
    const webhookModal = document.getElementById("webhookModal");
    const webhookModalTitle = document.getElementById("webhookModalTitle");
    const btnOpenCreateWebhook = document.getElementById("btnOpenCreateWebhook");
    const btnCloseWebhookModal = document.getElementById("btnCloseWebhookModal");
    const btnCancelWebhookModal = document.getElementById("btnCancelWebhookModal");
    const btnSaveWebhook = document.getElementById("btnSaveWebhook");

    const whId = document.getElementById("whId");
    const whEventType = document.getElementById("whEventType");
    const whTargetEndpoint = document.getElementById("whTargetEndpoint");
    const whStatus = document.getElementById("whStatus");
    const whLatency = document.getElementById("whLatency");
    const whPayload = document.getElementById("whPayload");

    function openWebhookModal(isEdit, data = null) {
      if (!webhookModal) return;
      if (isEdit && data) {
        if (webhookModalTitle) webhookModalTitle.textContent = "Edit Webhook Dispatch Record";
        if (whId) whId.value = data.id || "";
        if (whEventType) whEventType.value = data.event_type || "";
        if (whTargetEndpoint) whTargetEndpoint.value = data.target_endpoint || "";
        if (whStatus) whStatus.value = data.status || "Delivered";
        if (whLatency) whLatency.value = data.latency_ms || 35;
        if (whPayload) whPayload.value = typeof data.payload === "object" ? JSON.stringify(data.payload, null, 2) : (data.payload || "");
      } else {
        if (webhookModalTitle) webhookModalTitle.textContent = "Dispatch Outbound Webhook";
        if (whId) whId.value = "";
        if (whEventType) whEventType.value = "telemetry.vibration.threshold";
        if (whTargetEndpoint) whTargetEndpoint.value = "https://api.partner.kz/v1/telemetry/events";
        if (whStatus) whStatus.value = "Delivered";
        if (whLatency) whLatency.value = "42";
        if (whPayload) whPayload.value = JSON.stringify({
          event: "telemetry.vibration.threshold",
          device_id: "SENS-VIB-091",
          level: "WARNING",
          peak_rms: "4.82 mm/s",
          timestamp: new Date().toISOString()
        }, null, 2);
      }
      webhookModal.classList.add("active");
    }

    function closeWebhookModal() {
      if (webhookModal) webhookModal.classList.remove("active");
    }

    if (btnOpenCreateWebhook) {
      btnOpenCreateWebhook.addEventListener("click", () => openWebhookModal(false));
    }
    if (btnCloseWebhookModal) {
      btnCloseWebhookModal.addEventListener("click", closeWebhookModal);
    }
    if (btnCancelWebhookModal) {
      btnCancelWebhookModal.addEventListener("click", closeWebhookModal);
    }

    // Close on backdrop click
    if (webhookModal) {
      webhookModal.addEventListener("click", (e) => {
        if (e.target === webhookModal) closeWebhookModal();
      });
    }

    // 3. Save / Update Webhook
    if (btnSaveWebhook) {
      btnSaveWebhook.addEventListener("click", async () => {
        const idVal = whId ? whId.value.trim() : "";
        const eventType = whEventType ? whEventType.value.trim() : "";
        const targetEndpoint = whTargetEndpoint ? whTargetEndpoint.value.trim() : "";
        const status = whStatus ? whStatus.value : "Delivered";
        const latency = parseInt(whLatency ? whLatency.value : 35, 10) || 35;
        let payloadVal = whPayload ? whPayload.value.trim() : "";

        if (!eventType || !targetEndpoint) {
          if (window.showToast) {
            window.showToast("VALIDATION ERROR", "Event type and target endpoint are required.", "alert", "error");
          }
          return;
        }

        let parsedPayload = null;
        if (payloadVal) {
          try {
            parsedPayload = JSON.parse(payloadVal);
          } catch (e) {
            parsedPayload = { text: payloadVal };
          }
        }

        const isEdit = Boolean(idVal);
        const action = isEdit ? "update_webhook" : "create_webhook";
        const bodyData = {
          action: action,
          event_type: eventType,
          target_endpoint: targetEndpoint,
          status: status,
          latency_ms: latency,
          payload: parsedPayload
        };
        if (isEdit) bodyData.id = idVal;

        btnSaveWebhook.disabled = true;
        btnSaveWebhook.innerHTML = '<span class="material-symbols-outlined text-[15px] animate-spin">sync</span> Saving...';

        try {
          const resp = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(bodyData)
          });
          const res = await resp.json();

          if (res.success) {
            if (window.showToast) {
              window.showToast(
                isEdit ? "WEBHOOK UPDATED" : "WEBHOOK DISPATCHED",
                res.message || "Webhook record synchronized successfully.",
                "success",
                "done_all"
              );
            }
            closeWebhookModal();
            setTimeout(() => window.location.reload(), 600);
          } else {
            if (window.showToast) {
              window.showToast("GATEWAY ERROR", res.error || "Failed to persist webhook", "alert", "error");
            }
          }
        } catch (err) {
          if (window.showToast) {
            window.showToast("NETWORK ERROR", err.message, "alert", "wifi_off");
          }
        } finally {
          btnSaveWebhook.disabled = false;
          btnSaveWebhook.innerHTML = '<span class="material-symbols-outlined text-[16px]">send</span> Save &amp; Dispatch';
        }
      });
    }

    // 4. Edit Buttons Handler
    document.querySelectorAll(".btn-edit-webhook").forEach((btn) => {
      btn.addEventListener("click", function () {
        try {
          const raw = this.getAttribute("data-webhook");
          if (raw) {
            const data = JSON.parse(raw);
            openWebhookModal(true, data);
          }
        } catch (e) {
          console.error("Failed to parse webhook JSON:", e);
        }
      });
    });

    // 5. Delete Buttons Handler
    document.querySelectorAll(".btn-delete-webhook").forEach((btn) => {
      btn.addEventListener("click", async function () {
        const id = this.getAttribute("data-id");
        const deliveryId = this.getAttribute("data-delivery") || id;

        if (!confirm(`Are you sure you want to permanently delete webhook record [${deliveryId}] from the database?`)) {
          return;
        }

        try {
          const resp = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "delete_webhook", id: id })
          });
          const res = await resp.json();

          if (res.success) {
            const row = document.getElementById(`webhook-row-${id}`);
            if (row) {
              row.style.transition = "all 0.3s ease";
              row.style.opacity = "0";
              setTimeout(() => row.remove(), 300);
            }
            if (window.showToast) {
              window.showToast("RECORD PURGED", `Webhook [${deliveryId}] deleted from database.`, "info", "delete");
            }
          } else {
            if (window.showToast) {
              window.showToast("DELETE ERROR", res.error || "Could not delete webhook", "alert", "error");
            }
          }
        } catch (e) {
          if (window.showToast) {
            window.showToast("NETWORK ERROR", e.message, "alert", "wifi_off");
          }
        }
      });
    });

    // 6. Webhook Retry Action (Live API call)
    document.querySelectorAll(".btn-retry-webhook").forEach((btn) => {
      btn.addEventListener("click", async function () {
        const id = this.getAttribute("data-id");
        const deliveryId = this.getAttribute("data-delivery") || id;

        this.disabled = true;
        this.innerHTML =
          '<span class="material-symbols-outlined text-[14px] animate-spin">sync</span> Sending...';

        try {
          const resp = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "retry_webhook", id: id })
          });
          const res = await resp.json();

          if (res.success) {
            this.innerHTML =
              '<span class="material-symbols-outlined text-[14px]">done</span> Delivered';
            this.style.color = "var(--vk-secondary)";
            
            // Update row UI badge
            const row = document.getElementById(`webhook-row-${id}`);
            if (row) {
              const codeBadge = row.querySelector(".wh-status-code-badge");
              if (codeBadge) {
                codeBadge.textContent = "200 OK";
                codeBadge.className = "vk-tag dev-background-dcfce7-color-166534-4a19 wh-status-code-badge";
              }
              const latencyVal = row.querySelector(".wh-latency-val");
              if (latencyVal && res.data && res.data.latency_ms) {
                latencyVal.textContent = res.data.latency_ms;
              }
            }

            if (window.showToast) {
              window.showToast(
                "WEBHOOK DISPATCHED",
                `Delivery [${deliveryId}] successfully delivered. HTTP 200 OK.`,
                "success",
                "forward_to_inbox"
              );
            }
          } else {
            this.disabled = false;
            this.innerHTML = '<span class="material-symbols-outlined text-[14px]">refresh</span> Retry';
            if (window.showToast) {
              window.showToast("RETRY FAILED", res.error || "Retry dispatch failed", "alert", "error");
            }
          }
        } catch (e) {
          this.disabled = false;
          this.innerHTML = '<span class="material-symbols-outlined text-[14px]">refresh</span> Retry';
          if (window.showToast) {
            window.showToast("NETWORK ERROR", e.message, "alert", "wifi_off");
          }
        }
      });
    });
  });
})();
