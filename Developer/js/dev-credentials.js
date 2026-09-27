/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 10: API Credentials & Partner Key Vault Module
 * Full Database Integration with Create, Read, Update, Delete & Revoke
 */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    // Generate Key Modal Elements
    const genModal = document.getElementById("genKeyModal");
    const openGenBtn = document.getElementById("btnOpenGenKey");
    const closeGenBtn = document.getElementById("btnCloseGenKey");
    const cancelGenBtn = document.getElementById("btnCancelGenKey");
    const submitGenBtn = document.getElementById("btnSubmitGenKey");

    // Edit Key Modal Elements
    const editModal = document.getElementById("editKeyModal");
    const closeEditBtn = document.getElementById("btnCloseEditKey");
    const cancelEditBtn = document.getElementById("btnCancelEditKey");
    const submitEditBtn = document.getElementById("btnSubmitEditKey");

    // Open Generate Key Modal
    if (openGenBtn && genModal) {
      openGenBtn.addEventListener("click", () => {
        document.getElementById("keyLabelInput").value = "";
        genModal.style.display = "flex";
      });
    }

    const closeModals = () => {
      if (genModal) genModal.style.display = "none";
      if (editModal) editModal.style.display = "none";
    };

    if (closeGenBtn) closeGenBtn.addEventListener("click", closeModals);
    if (cancelGenBtn) cancelGenBtn.addEventListener("click", closeModals);
    if (closeEditBtn) closeEditBtn.addEventListener("click", closeModals);
    if (cancelEditBtn) cancelEditBtn.addEventListener("click", closeModals);

    window.addEventListener("click", (e) => {
      if (e.target === genModal || e.target === editModal) closeModals();
    });

    // 1. CREATE: Generate & Mint Key into MySQL
    if (submitGenBtn) {
      submitGenBtn.addEventListener("click", async () => {
        const labelInput = document.getElementById("keyLabelInput");
        const label = labelInput ? labelInput.value.trim() : "";
        if (!label) {
          window.showToast("VALIDATION ERROR", "Key label / service name is required.", "error");
          return;
        }

        const env = document.querySelector('input[name="keyEnv"]:checked')?.value || "Production";
        const rate = document.getElementById("keyRateSelect")?.value || "10,000";

        // Scopes
        const scopes = [];
        if (document.getElementById("scopeTelemetry")?.checked) scopes.push("telemetry:read");
        if (document.getElementById("scopeScada")?.checked) scopes.push("scada:ingest");
        if (document.getElementById("scopeOrders")?.checked) scopes.push("orders:write");

        submitGenBtn.disabled = true;
        const origText = submitGenBtn.innerHTML;
        submitGenBtn.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span> Minting Key...';

        try {
          const res = await fetch("api/credentials.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              action: "create",
              label: label,
              environment: env,
              rate_limit: rate,
              scopes: scopes.join(",")
            })
          });

          const result = await res.json();
          if (result.success) {
            window.showToast(
              "API KEY MINTED IN DATABASE",
              `Generated ${result.data.key_identifier} for ${label} [${env}]. Quota: ${rate} req/min.`,
              "success",
              "vpn_key"
            );
            closeModals();
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast("ERROR", result.error || "Failed to mint key.", "error");
          }
        } catch (err) {
          window.showToast("NETWORK ERROR", err.message, "error");
        } finally {
          submitGenBtn.disabled = false;
          submitGenBtn.innerHTML = origText;
        }
      });
    }

    // 2. OPEN EDIT MODAL
    document.addEventListener("click", (e) => {
      const editBtn = e.target.closest(".btn-edit-key");
      if (editBtn) {
        const raw = editBtn.getAttribute("data-key");
        if (!raw) return;
        try {
          const key = JSON.parse(raw);
          document.getElementById("editKeyId").value = key.id;
          document.getElementById("editKeyLabel").value = key.label || "";
          document.getElementById("editKeyEnv").value = key.environment || "Production";
          document.getElementById("editKeyStatus").value = key.status || "Active";
          document.getElementById("editKeyScopes").value = key.scopes || "";

          // Match rate limit select
          const rateSelect = document.getElementById("editKeyRateSelect");
          if (rateSelect) {
            const rawLimit = String(key.rate_limit || "");
            if (rawLimit.includes("50,000")) rateSelect.value = "50,000";
            else if (rawLimit.includes("2,500")) rateSelect.value = "2,500";
            else rateSelect.value = "10,000";
          }

          document.getElementById("editKeyModalTitle").textContent = `Edit Key: ${key.key_identifier}`;
          if (editModal) editModal.style.display = "flex";
        } catch (err) {
          console.error("Failed to parse key json", err);
        }
      }
    });

    // 3. UPDATE KEY IN DATABASE
    if (submitEditBtn) {
      submitEditBtn.addEventListener("click", async () => {
        const id = document.getElementById("editKeyId").value;
        const label = document.getElementById("editKeyLabel").value.trim();
        const env = document.getElementById("editKeyEnv").value;
        const status = document.getElementById("editKeyStatus").value;
        const rate = document.getElementById("editKeyRateSelect").value;
        const scopes = document.getElementById("editKeyScopes").value.trim();

        if (!label) {
          window.showToast("VALIDATION ERROR", "Key label is required.", "error");
          return;
        }

        submitEditBtn.disabled = true;
        const origText = submitEditBtn.innerHTML;
        submitEditBtn.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span> Updating...';

        try {
          const res = await fetch("api/credentials.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              action: "update",
              id: id,
              label: label,
              environment: env,
              status: status,
              rate_limit: rate,
              scopes: scopes
            })
          });

          const result = await res.json();
          if (result.success) {
            window.showToast("KEY UPDATED", "API key parameters saved to database.", "success");
            closeModals();
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast("UPDATE ERROR", result.error || "Failed to update key.", "error");
          }
        } catch (err) {
          window.showToast("NETWORK ERROR", err.message, "error");
        } finally {
          submitEditBtn.disabled = false;
          submitEditBtn.innerHTML = origText;
        }
      });
    }

    // 4. REVOKE KEY IN DATABASE
    document.addEventListener("click", async (e) => {
      const btn = e.target.closest(".btn-revoke-key");
      if (btn && !btn.disabled) {
        const tr = btn.closest("tr");
        const keyId = btn.getAttribute("data-id") || tr?.getAttribute("data-key-id");
        if (!keyId) return;

        if (!confirm("Are you sure you want to revoke this cryptographic key? Regional access will be terminated immediately.")) {
          return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin">sync</span>';

        try {
          const res = await fetch("api/credentials.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "revoke", id: keyId })
          });
          const result = await res.json();

          if (result.success) {
            btn.innerHTML = '<span class="material-symbols-outlined text-[14px]">done</span> Revoked';
            btn.style.color = "#94A3B8";
            const statusBadge = tr?.querySelector(".key-status-badge");
            if (statusBadge) {
              statusBadge.className = "vk-status-badge status-revoked key-status-badge";
              statusBadge.textContent = "REVOKED";
            }
            window.showToast(
              "KEY REVOKED IN DATABASE",
              "Cryptographic access token invalidated across all regional gateways.",
              "warn",
              "link_off"
            );
          } else {
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-[14px]">block</span> Revoke';
            window.showToast("ERROR", result.error || "Failed to revoke key.", "error");
          }
        } catch (err) {
          btn.disabled = false;
          btn.innerHTML = '<span class="material-symbols-outlined text-[14px]">block</span> Revoke';
          window.showToast("NETWORK ERROR", err.message, "error");
        }
      }
    });

    // 5. DELETE KEY FROM DATABASE
    document.addEventListener("click", async (e) => {
      const delBtn = e.target.closest(".btn-delete-key");
      if (delBtn) {
        const id = delBtn.getAttribute("data-id");
        const label = delBtn.getAttribute("data-label") || "this key";

        if (!confirm(`Permanently delete "${label}" from database? This action cannot be undone.`)) {
          return;
        }

        try {
          const res = await fetch("api/credentials.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "delete", id: id })
          });

          const result = await res.json();
          if (result.success) {
            const tr = delBtn.closest("tr");
            if (tr) tr.remove();
            window.showToast("KEY DELETED", `API key "${label}" removed from database.`, "warn", "delete");
          } else {
            window.showToast("DELETE ERROR", result.error || "Failed to delete key.", "error");
          }
        } catch (err) {
          window.showToast("NETWORK ERROR", err.message, "error");
        }
      }
    });
  });
})();
