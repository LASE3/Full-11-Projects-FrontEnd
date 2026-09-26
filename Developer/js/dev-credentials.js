/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 10: API Credentials & Partner Key Vault Module
 */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("genKeyModal");
    const openBtn = document.getElementById("btnOpenGenKey");
    const closeBtn = document.getElementById("btnCloseGenKey");
    const cancelBtn = document.getElementById("btnCancelGenKey");
    const submitBtn = document.getElementById("btnSubmitGenKey");
    const keysTbody = document.getElementById("keysTableBody");

    if (openBtn && modal) {
      openBtn.addEventListener("click", () => (modal.style.display = "flex"));
    }
    if (closeBtn && modal) {
      closeBtn.addEventListener("click", () => (modal.style.display = "none"));
    }
    if (cancelBtn && modal) {
      cancelBtn.addEventListener("click", () => (modal.style.display = "none"));
    }

    if (modal) {
      modal.addEventListener("click", (e) => {
        if (e.target === modal) modal.style.display = "none";
      });
    }

    if (submitBtn && keysTbody) {
      submitBtn.addEventListener("click", () => {
        const labelInput = document.getElementById("keyLabelInput");
        const label = labelInput
          ? labelInput.value.trim() || "Custom Enterprise Service"
          : "Custom Enterprise Service";
        const env =
          document.querySelector('input[name="keyEnv"]:checked')?.value ||
          "Production";
        const rate =
          document.getElementById("keyRateSelect")?.value || "10,000";

        const randomHash = Array.from({ length: 24 }, () =>
          Math.floor(Math.random() * 16).toString(16),
        ).join("");
        const newKeyToken = `vk_${env === "Sandbox" ? "test" : "live"}_${randomHash}`;

        const tr = document.createElement("tr");
        tr.className = "tag-confidential";
        tr.innerHTML = `
                    <td>
                        <div style="font-weight: 600; color: var(--vk-primary);">${label}</div>
                        <div style="font-family: var(--font-mono); font-size: 11px; color: var(--vk-neutral-600);">ID: KEY-${Math.floor(1000 + Math.random() * 9000)} • CUS-1002</div>
                    </td>
                    <td>
                        <div class="key-token-display">
                            <span>${newKeyToken.substring(0, 16)}••••••••</span>
                            <button class="vk-btn-outline" style="padding: 2px 6px; font-size: 10px;" onclick="window.copyText('${newKeyToken}', 'Full API token copied to clipboard')">
                                <span class="material-symbols-outlined text-[14px]">content_copy</span>
                            </button>
                        </div>
                    </td>
                    <td>
                        <span class="vk-status-badge ${env === "Sandbox" ? "status-sandbox" : "status-active"}">${env.toUpperCase()}</span>
                    </td>
                    <td>
                        <div style="font-family: var(--font-mono); font-size: 11px; font-weight: 600;">${rate} req/min</div>
                        <div class="rate-limit-bar-bg"><div class="rate-limit-bar-fill" style="width: 4%;"></div></div>
                    </td>
                    <td>
                        <span class="badge-classification badge-confidential">Confidential</span>
                    </td>
                    <td style="text-align: right;">
                        <button class="vk-btn vk-btn-outline btn-revoke-key" style="padding: 4px 8px; font-size: 11px; color: var(--vk-class-high-confidential);">
                            <span class="material-symbols-outlined text-[14px]">block</span> Revoke
                        </button>
                    </td>
                `;

        keysTbody.insertBefore(tr, keysTbody.firstChild);
        modal.style.display = "none";

        window.showToast(
          "API KEY MINTED",
          `Generated token for ${label} [${env}]. Quota set to ${rate} req/min.`,
          "success",
          "vpn_key",
        );
      });
    }

    // Delegated Revoke Handler
    document.addEventListener("click", (e) => {
      const btn = e.target.closest(".btn-revoke-key");
      if (btn) {
        const tr = btn.closest("tr");
        if (tr) {
          btn.disabled = true;
          btn.innerHTML =
            '<span class="material-symbols-outlined text-[14px]">done</span> Revoked';
          btn.style.color = "#94A3B8";
          const statusBadge = tr.querySelector(".vk-status-badge");
          if (statusBadge) {
            statusBadge.className = "vk-status-badge status-revoked";
            statusBadge.textContent = "REVOKED";
          }
          window.showToast(
            "KEY REVOKED",
            "Cryptographic access token invalidated across all regional gateways.",
            "warn",
            "link_off",
          );
        }
      }
    });
  });
})();
