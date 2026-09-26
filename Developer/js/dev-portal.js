/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 10: Developer & API Documentation Explorer Module
 */

(function () {
  "use strict";

  // Code Snippets Database for Industrial Endpoints
  const CODE_SNIPPETS = {
    "endpoint-optical": {
      curl: `curl -X GET "https://developer.vostokpribor.local/v1/sensors/optical/telemetry?device_id=PROD-1001-KZ" \\
  -H "Authorization: Bearer vk_live_9a41c2e8f10b" \\
  -H "Accept: application/json"`,
      python: `import requests

url = "https://developer.vostokpribor.local/v1/sensors/optical/telemetry"
headers = {
    "Authorization": "Bearer vk_live_9a41c2e8f10b",
    "Accept": "application/json"
}
params = {"device_id": "PROD-1001-KZ"}

response = requests.get(url, headers=headers, params=params)
data = response.json()
print("Wavelength Peak (nm):", data["spectral_resolution_nm"])`,
      node: `const fetch = require('node-fetch');

async function getOpticalTelemetry() {
  const url = new URL('https://developer.vostokpribor.local/v1/sensors/optical/telemetry');
  url.searchParams.set('device_id', 'PROD-1001-KZ');

  const res = await fetch(url, {
    headers: {
      'Authorization': 'Bearer vk_live_9a41c2e8f10b',
      'Accept': 'application/json'
    }
  });
  const data = await res.json();
  console.log(data);
}
getOpticalTelemetry();`,
      go: `package main

import (
    "fmt"
    "net/http"
    "io"
)

func main() {
    url := "https://developer.vostokpribor.local/v1/sensors/optical/telemetry?device_id=PROD-1001-KZ"
    req, _ := http.NewRequest("GET", url, nil)
    req.Header.Set("Authorization", "Bearer vk_live_9a41c2e8f10b")

    client := &http.Client{}
    resp, err := client.Do(req)
    if err != nil { panic(err) }
    defer resp.Body.Close()

    body, _ := io.ReadAll(resp.Body)
    fmt.Println(string(body))
}`,
    },
    "endpoint-geodetic": {
      curl: `curl -X GET "https://developer.vostokpribor.local/v1/devices/geodetic/measurements?unit=PROD-1002" \\
  -H "Authorization: Bearer vk_live_9a41c2e8f10b"`,
      python: `import requests

url = "https://developer.vostokpribor.local/v1/devices/geodetic/measurements"
headers = {"Authorization": "Bearer vk_live_9a41c2e8f10b"}
params = {"unit": "PROD-1002"}

resp = requests.get(url, headers=headers, params=params)
print("Calibration Status:", resp.json()["calibration_valid"])`,
      node: `const res = await fetch('https://developer.vostokpribor.local/v1/devices/geodetic/measurements?unit=PROD-1002', {
  headers: { 'Authorization': 'Bearer vk_live_9a41c2e8f10b' }
});
console.log(await res.json());`,
      go: `// Go implementation for Geodetic Calibration Kit Telemetry
req, _ := http.NewRequest("GET", "https://developer.vostokpribor.local/v1/devices/geodetic/measurements?unit=PROD-1002", nil)
req.Header.Set("Authorization", "Bearer vk_live_9a41c2e8f10b")`,
    },
    "endpoint-scada": {
      curl: `curl -X POST "https://developer.vostokpribor.local/v1/scada/ingest/frames" \\
  -H "Authorization: Bearer vk_live_9a41c2e8f10b" \\
  -H "Content-Type: application/json" \\
  -d '{
    "facility_id": "ALMATY-CENTRAL-01",
    "protocol": "MODBUS-TCP",
    "plc_register": "40001",
    "payload_hex": "0A2B4C",
    "timestamp_utc": 1789128800
  }'`,
      python: `import requests

payload = {
    "facility_id": "ALMATY-CENTRAL-01",
    "protocol": "MODBUS-TCP",
    "plc_register": "40001",
    "payload_hex": "0A2B4C"
}
headers = {"Authorization": "Bearer vk_live_9a41c2e8f10b"}
resp = requests.post("https://developer.vostokpribor.local/v1/scada/ingest/frames", json=payload, headers=headers)
print("Ingestion Receipt:", resp.json()["frame_ack"])`,
      node: `const payload = {
  facility_id: "ALMATY-CENTRAL-01",
  protocol: "MODBUS-TCP",
  plc_register: "40001",
  payload_hex: "0A2B4C"
};
const res = await fetch("https://developer.vostokpribor.local/v1/scada/ingest/frames", {
  method: "POST",
  headers: { "Authorization": "Bearer vk_live_9a41c2e8f10b", "Content-Type": "application/json" },
  body: JSON.stringify(payload)
});`,
      go: `// SCADA high-speed frame dispatch in Go
resp, err := http.Post("https://developer.vostokpribor.local/v1/scada/ingest/frames", "application/json", body)`,
    },
  };

  document.addEventListener("DOMContentLoaded", () => {
    // Tab Switchers
    document.querySelectorAll(".code-tab-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        const parent = this.closest(".endpoint-code-col");
        if (!parent) return;

        parent
          .querySelectorAll(".code-tab-btn")
          .forEach((b) => b.classList.remove("active"));
        this.classList.add("active");

        const lang = this.getAttribute("data-lang");
        const endpointId = this.getAttribute("data-endpoint");
        const codeBlock = parent.querySelector(".code-block-box code");

        if (
          CODE_SNIPPETS[endpointId] &&
          CODE_SNIPPETS[endpointId][lang] &&
          codeBlock
        ) {
          codeBlock.textContent = CODE_SNIPPETS[endpointId][lang];
        }
      });
    });

    // Copy Button Click
    document.querySelectorAll(".copy-code-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        const parent = this.closest(".endpoint-code-col");
        const code = parent
          ? parent.querySelector(".code-block-box code")?.textContent
          : "";
        if (code) {
          window.copyText(code, "Code snippet copied to clipboard");
        }
      });
    });

    // Try in Sandbox Button
    document.querySelectorAll(".btn-try-sandbox").forEach((btn) => {
      btn.addEventListener("click", function () {
        const method = this.getAttribute("data-method") || "GET";
        const url =
          this.getAttribute("data-url") || "/v1/sensors/optical/telemetry";
        const body = this.getAttribute("data-body") || "";

        sessionStorage.setItem("vk_sandbox_method", method);
        sessionStorage.setItem("vk_sandbox_url", url);
        sessionStorage.setItem("vk_sandbox_body", body);

        window.location.href = "sandbox.php";
      });
    });
  });
})();
