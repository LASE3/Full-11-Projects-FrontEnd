/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 10: Interactive API Sandbox & Testing Console Module
 */

(function () {
  "use strict";

  const MOCK_RESPONSES = {
    "/v1/sensors/optical/telemetry": {
      status: 200,
      statusText: "OK",
      time: "24ms",
      size: "842 B",
      data: {
        device_id: "PROD-1001-KZ",
        sensor_series: "Industrial Optical Sensor Package",
        calibration_epoch: 1789128000,
        station: "ALMATY-CENTRAL",
        telemetry: {
          spectral_resolution_nm: 0.04,
          focal_plane_temp_c: 18.2,
          dispersion_coefficient: 1.0024,
          optical_throughput_percent: 99.82,
          snr_db: 68.4,
        },
        status: "NOMINAL_OPERATIONAL",
        jurisdiction_merkle_root: "0x4a8c911f...c892",
      },
    },
    "/v1/devices/geodetic/measurements": {
      status: 200,
      statusText: "OK",
      time: "31ms",
      size: "710 B",
      data: {
        unit_id: "PROD-1002-UST-04",
        apparatus: "Precision Geodetic Measurement Kit",
        laser_interferometer: "STABLE",
        azimuth_arcsec: 142.8812,
        zenith_angle_deg: 44.1029,
        distance_vector_meters: 1840.4502,
        refraction_index: 1.000277,
        calibration_valid: true,
      },
    },
    "/v1/scada/ingest/frames": {
      status: 201,
      statusText: "Created",
      time: "18ms",
      size: "412 B",
      data: {
        frame_ack: "ACK-SCADA-89102",
        facility_id: "ALMATY-CENTRAL-01",
        protocol: "MODBUS-TCP",
        buffered_lines: 1,
        ring_buffer_utilization: "14%",
        audit_escrow_timestamp: 1789128842,
      },
    },
    "/v1/b2b/orders/create": {
      status: 200,
      statusText: "OK",
      time: "42ms",
      size: "620 B",
      data: {
        order_id: "ORD-2026-9904",
        customer_id: "CUS-1002",
        customer_name: "BaltNord Process Systems",
        total_eur: 240000.0,
        items: [
          { prod_id: "PROD-1001", qty: 4, desc: "Optical Sensor Package" },
          { prod_id: "PROD-1004", qty: 2, desc: "Industrial PLC Integration" },
        ],
        invoice_ref: "INV-2026-002",
        fulfillment_status: "PROCESSING_OPS",
      },
    },
  };

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
    if (savedBody && bodyEditor && savedBody.trim())
      bodyEditor.value = savedBody;

    sessionStorage.removeItem("vk_sandbox_method");
    sessionStorage.removeItem("vk_sandbox_url");
    sessionStorage.removeItem("vk_sandbox_body");

    // Quick Preset Buttons
    document.querySelectorAll(".btn-preset").forEach((btn) => {
      btn.addEventListener("click", function () {
        const method = this.getAttribute("data-method");
        const url = this.getAttribute("data-url");
        const sampleBody = this.getAttribute("data-body") || "";

        if (methodSelect) methodSelect.value = method;
        if (urlInput) urlInput.value = url;
        if (bodyEditor) bodyEditor.value = sampleBody;

        window.showToast("PRESET LOADED", `Loaded endpoint: ${url}`, "info");
      });
    });

    // Send Request Simulation
    if (sendBtn) {
      sendBtn.addEventListener("click", () => {
        const url = urlInput ? urlInput.value.trim() : "";
        const origText = sendBtn.innerHTML;

        sendBtn.disabled = true;
        sendBtn.innerHTML =
          '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span><span>Sending...</span>';

        setTimeout(() => {
          sendBtn.disabled = false;
          sendBtn.innerHTML = origText;

          let matched = null;
          for (const key in MOCK_RESPONSES) {
            if (url.includes(key)) {
              matched = MOCK_RESPONSES[key];
              break;
            }
          }

          if (!matched) {
            matched = {
              status: 200,
              statusText: "OK",
              time: `${Math.floor(20 + Math.random() * 25)}ms`,
              size: "512 B",
              data: {
                request_uri: url,
                method: methodSelect ? methodSelect.value : "GET",
                gateway: "developer.vostokpribor.local",
                authenticated_as: "CUS-1002 (BaltNord Process Systems)",
                response: "GENERIC_MOCK_SUCCESS",
                timestamp_utc: Math.floor(Date.now() / 1000),
              },
            };
          }

          if (respCode) {
            respCode.textContent = `${matched.status} ${matched.statusText}`;
            respCode.className = `vk-status-badge ${matched.status >= 200 && matched.status < 300 ? "status-active" : "status-revoked"}`;
          }
          if (respTime) respTime.textContent = matched.time;
          if (respSize) respSize.textContent = matched.size;
          if (respBody) {
            respBody.textContent = JSON.stringify(matched.data, null, 2);
          }

          window.showToast(
            "HTTP DISPATCH COMPLETED",
            `Response received: ${matched.status} ${matched.statusText} (${matched.time})`,
            "success",
            "cloud_done",
          );
        }, 450);
      });
    }
  });
})();
