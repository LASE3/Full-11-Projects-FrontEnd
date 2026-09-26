/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Access Matrix & Privilege Boundary Interactivity
 */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    // 1. Simulate Enforcement Action
    const simBtn = Array.from(document.querySelectorAll("button")).find((b) =>
      b.textContent.includes("Simulate Enforcement"),
    );
    if (simBtn) {
      simBtn.addEventListener("click", function () {
        const orig = this.innerHTML;
        this.disabled = true;
        this.innerHTML =
          '<span class="material-symbols-outlined text-[16px] animate-spin text-secondary">sync</span><span>Evaluating Zero-Trust Policy...</span>';

        setTimeout(() => {
          this.innerHTML = orig;
          this.disabled = false;
          window.showToast(
            "ZERO-TRUST SYNTHESIS COMPLETE",
            "Simulation evaluated against 11 nodes. 0 SoD conflicts detected. 20 Identities compliant with ST RK Directive.",
            "success",
            "verified",
          );
        }, 1200);
      });
    }

    // 2. Export Matrix Action
    const exportBtn = Array.from(document.querySelectorAll("button")).find(
      (b) => b.textContent.includes("Export Matrix"),
    );
    if (exportBtn) {
      exportBtn.addEventListener("click", function () {
        const matrixExport = {
          document: "DOC-2026-007",
          title: "Employee_Access_Matrix.xlsx",
          system: "SYS-11 // GOV-CORE",
          enclave: "Almaty Central Enclave",
          signatories: ["EMP-1005 (T. Akhmetov)", "EMP-1018 (L. Volkov)"],
          timestamp: new Date().toISOString(),
          activeRolesCount: 14,
          crossSystemIdentities: 20,
          status: "ATTESTED",
        };

        const blob = new Blob([JSON.stringify(matrixExport, null, 2)], {
          type: "application/json",
        });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = "DOC-2026-007_Employee_Access_Matrix.json";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        window.showToast(
          "EXPORT DISPATCHED",
          "Downloaded DOC-2026-007 schema ledger as verified cryptographic JSON package.",
          "success",
          "download",
        );
      });
    }

    // 3. Commit Ledger Attestation
    const commitBtn = Array.from(document.querySelectorAll("button")).find(
      (b) => b.textContent.includes("Commit Ledger Attestation"),
    );
    if (commitBtn) {
      commitBtn.addEventListener("click", function () {
        const orig = this.innerHTML;
        this.disabled = true;
        this.innerHTML =
          '<span class="material-symbols-outlined text-[16px] animate-spin text-secondary-fixed">sync</span><span>Hashing Merkle Leaf...</span>';

        setTimeout(() => {
          this.innerHTML =
            '<span class="material-symbols-outlined text-[16px] text-secondary-fixed">done_all</span><span>Ledger Notarized</span>';
          window.showToast(
            "LEDGER COMMITTED",
            "Access Matrix notarized with Dual-Custody: EMP-1005 (T. Akhmetov) & EMP-1018 (L. Volkov). Merkle root broadcast to Astana Escrow.",
            "success",
            "encrypted",
          );
        }, 1000);
      });
    }

    // 4. Role Tier Card Click Filter
    const tierCards = document.querySelectorAll(
      ".grid-cols-1.sm\\:grid-cols-2.lg\\:grid-cols-4 > div",
    );
    const roleRows = document.querySelectorAll("tbody tr");

    tierCards.forEach((card, idx) => {
      card.style.cursor = "pointer";
      card.addEventListener("click", () => {
        tierCards.forEach((c) => c.classList.remove("ring-2", "ring-primary"));
        card.classList.add("ring-2", "ring-primary");

        const tierTerms = ["level 5", "level 4", "level 3", "level 2"];
        const term = tierTerms[idx] || "";

        roleRows.forEach((row) => {
          const text = row.innerText.toLowerCase();
          if (
            !term ||
            text.includes(term) ||
            text.includes("root") ||
            text.includes("clearance")
          ) {
            row.style.display = "";
          } else {
            row.style.display = "none";
          }
        });

        window.showToast(
          "FILTER APPLIED",
          `Showing roles matching tier ${term.toUpperCase()}`,
          "info",
          "filter_alt",
        );
      });
    });
  });
})();
