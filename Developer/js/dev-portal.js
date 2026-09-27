/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 10: Developer & API Documentation Explorer Module
 * Full Database Integration with Create, Read, Update, Delete (CRUD)
 */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("endpointModal");
    const openBtn = document.getElementById("btnOpenCreateEndpoint");
    const closeBtn = document.getElementById("btnCloseEndpointModal");
    const cancelBtn = document.getElementById("btnCancelEndpointModal");
    const saveBtn = document.getElementById("btnSaveEndpoint");
    const form = document.getElementById("endpointForm");
    const modalTitle = document.getElementById("endpointModalTitle");

    // Modal open for New Endpoint
    if (openBtn && modal) {
      openBtn.addEventListener("click", () => {
        if (form) form.reset();
        document.getElementById("epId").value = "";
        document.getElementById("epSlug").value = "";
        if (modalTitle) modalTitle.textContent = "Register New API Endpoint";
        modal.style.display = "flex";
      });
    }

    // Modal close handlers
    const closeModal = () => {
      if (modal) modal.style.display = "none";
    };
    if (closeBtn) closeBtn.addEventListener("click", closeModal);
    if (cancelBtn) cancelBtn.addEventListener("click", closeModal);
    if (modal) {
      modal.addEventListener("click", (e) => {
        if (e.target === modal) closeModal();
      });
    }

    // Save Endpoint (Create or Update)
    if (saveBtn) {
      saveBtn.addEventListener("click", async () => {
        const id = document.getElementById("epId")?.value.trim() || "";
        const title = document.getElementById("epTitle")?.value.trim() || "";
        const method = document.getElementById("epMethod")?.value.trim() || "GET";
        const path = document.getElementById("epPath")?.value.trim() || "";
        const classification = document.getElementById("epClassification")?.value.trim() || "Internal";
        const rateLimit = document.getElementById("epRateLimit")?.value.trim() || "10k/min";
        const targetHardware = document.getElementById("epTargetHardware")?.value.trim() || "PROD-1001";
        const description = document.getElementById("epDescription")?.value.trim() || "";
        const paramsJson = document.getElementById("epParamsJson")?.value.trim() || "";
        const curlSnippet = document.getElementById("epCurl")?.value.trim() || "";

        if (!title || !path || !description) {
          window.showToast("VALIDATION ERROR", "Title, URI path, and description are required.", "error");
          return;
        }

        saveBtn.disabled = true;
        const origText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span> Saving...';

        try {
          const isUpdate = id !== "";
          const payload = {
            action: isUpdate ? "update" : "create",
            id: id,
            title: title,
            method: method,
            path: path,
            classification: classification,
            rate_limit: rateLimit,
            target_hardware: targetHardware,
            description: description,
            parameters_json: paramsJson,
            curl_snippet: curlSnippet
          };

          const res = await fetch("api/endpoints.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
          });

          const result = await res.json();
          if (result.success) {
            window.showToast("ENDPOINT SAVED", isUpdate ? "API endpoint specification updated in database." : "New industrial endpoint registered in database.", "success");
            closeModal();
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast("ERROR", result.error || "Failed to save endpoint.", "error");
          }
        } catch (err) {
          window.showToast("NETWORK ERROR", err.message || "Failed to connect to database API.", "error");
        } finally {
          saveBtn.disabled = false;
          saveBtn.innerHTML = origText;
        }
      });
    }

    // Edit Endpoint Delegated Handler
    document.addEventListener("click", (e) => {
      const editBtn = e.target.closest(".btn-edit-endpoint");
      if (editBtn) {
        const raw = editBtn.getAttribute("data-endpoint");
        if (!raw) return;
        try {
          const ep = JSON.parse(raw);
          document.getElementById("epId").value = ep.id || "";
          document.getElementById("epSlug").value = ep.endpoint_slug || "";
          document.getElementById("epTitle").value = ep.title || "";
          document.getElementById("epMethod").value = ep.method || "GET";
          document.getElementById("epPath").value = ep.path || "";
          document.getElementById("epClassification").value = ep.classification || "Internal";
          document.getElementById("epRateLimit").value = ep.rate_limit || "10k/min";
          document.getElementById("epTargetHardware").value = ep.target_hardware || "PROD-1001";
          document.getElementById("epDescription").value = ep.description || "";
          document.getElementById("epParamsJson").value = ep.parameters_json || "";
          document.getElementById("epCurl").value = ep.curl_snippet || "";

          if (modalTitle) modalTitle.textContent = "Edit Endpoint: " + (ep.title || "");
          if (modal) modal.style.display = "flex";
        } catch (err) {
          console.error("Failed to parse endpoint data", err);
        }
      }
    });

    // Delete Endpoint Delegated Handler
    document.addEventListener("click", async (e) => {
      const delBtn = e.target.closest(".btn-delete-endpoint");
      if (delBtn) {
        const id = delBtn.getAttribute("data-id");
        const title = delBtn.getAttribute("data-title") || "this endpoint";
        if (!confirm(`Are you sure you want to remove endpoint "${title}" from the database?`)) {
          return;
        }

        try {
          const res = await fetch("api/endpoints.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "delete", id: id })
          });
          const result = await res.json();
          if (result.success) {
            const card = delBtn.closest(".endpoint-card");
            if (card) card.remove();
            window.showToast("ENDPOINT DELETED", `Endpoint "${title}" removed from database.`, "warn", "delete");
          } else {
            window.showToast("DELETE ERROR", result.error || "Could not delete endpoint.", "error");
          }
        } catch (err) {
          window.showToast("NETWORK ERROR", err.message, "error");
        }
      }
    });

    // Code Tab Switchers
    document.querySelectorAll(".code-tab-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        const parent = this.closest(".endpoint-code-col");
        if (!parent) return;

        parent.querySelectorAll(".code-tab-btn").forEach((b) => b.classList.remove("active"));
        this.classList.add("active");

        const lang = this.getAttribute("data-lang");
        const card = this.closest(".endpoint-card");
        const editBtn = card ? card.querySelector(".btn-edit-endpoint") : null;
        let ep = null;
        if (editBtn && editBtn.getAttribute("data-endpoint")) {
          try {
            ep = JSON.parse(editBtn.getAttribute("data-endpoint"));
          } catch (e) {}
        }

        const codeBlock = parent.querySelector(".code-block-box code");
        if (codeBlock && ep) {
          if (lang === "curl") {
            codeBlock.textContent = ep.curl_snippet || `curl -X ${ep.method} "https://developer.vostokpribor.local${ep.path}" \\\n  -H "Authorization: Bearer vk_live_9a41c2e8f10b"`;
          } else if (lang === "python") {
            codeBlock.textContent = ep.python_snippet || `import requests\n\nresp = requests.${ep.method.toLowerCase()}("https://developer.vostokpribor.local${ep.path}", headers={"Authorization": "Bearer vk_live_9a41c2e8f10b"})\nprint(resp.json())`;
          } else if (lang === "node") {
            codeBlock.textContent = ep.node_snippet || `const fetch = require('node-fetch');\n\nfetch("https://developer.vostokpribor.local${ep.path}", { headers: { 'Authorization': 'Bearer vk_live_9a41c2e8f10b' } })\n  .then(res => res.json()).then(console.log);`;
          } else if (lang === "go") {
            codeBlock.textContent = ep.go_snippet || `// Go telemetry client for ${ep.path}\nreq, _ := http.NewRequest("${ep.method}", "https://developer.vostokpribor.local${ep.path}", nil)\nreq.Header.Set("Authorization", "Bearer vk_live_9a41c2e8f10b")`;
          }
        }
      });
    });

    // Copy Button Click
    document.querySelectorAll(".copy-code-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        const parent = this.closest(".endpoint-code-col");
        const code = parent ? parent.querySelector(".code-block-box code")?.textContent : "";
        if (code) {
          window.copyText(code, "Code snippet copied to clipboard");
        }
      });
    });

    // Try in Sandbox Button
    document.querySelectorAll(".btn-try-sandbox").forEach((btn) => {
      btn.addEventListener("click", function () {
        const method = this.getAttribute("data-method") || "GET";
        const url = this.getAttribute("data-url") || "/v1/sensors/optical/telemetry";
        const body = this.getAttribute("data-body") || "";

        sessionStorage.setItem("vk_sandbox_method", method);
        sessionStorage.setItem("vk_sandbox_url", url);
        sessionStorage.setItem("vk_sandbox_body", body);

        window.location.href = "sandbox.php";
      });
    });
  });
})();
