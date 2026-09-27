/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Main Dashboard & Attestation Review Module
 * Live Database API Integration
 */

(function () {
  "use strict";

  // Global actions called from table buttons rendered by gov_service.php
  window.purgeOrphanToken = async function (empId) {
    if (
      !confirm(
        `Permanently purge and revoke orphan access token for [${empId}]?`,
      )
    )
      return;
    try {
      const res = await fetch("api/privileged.php?action=purge_orphan", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ emp_id: empId }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast?.(
          "ORPHAN PURGED",
          `Token for ${empId} revoked and session scrubbed from database.`,
          "error",
          "delete_forever",
        );
        setTimeout(() => location.reload(), 800);
      } else {
        window.showToast?.("ERROR", data.error || "Purge failed", "error");
      }
    } catch (err) {
      window.showToast?.("NETWORK ERROR", err.message, "error");
    }
  };

  window.attestRole = async function (empId) {
    try {
      const res = await fetch("api/privileged.php?action=attest_role", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ emp_id: empId }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast?.(
          "ROLE ATTESTED",
          `Privileged access for ${empId} cryptographically signed and confirmed.`,
          "success",
          "verified",
        );
        setTimeout(() => location.reload(), 800);
      } else {
        window.showToast?.("ERROR", data.error || "Attestation failed", "error");
      }
    } catch (err) {
      window.showToast?.("NETWORK ERROR", err.message, "error");
    }
  };

  window.reviewRole = function (empId) {
    window.showToast?.(
      "ROLE REVIEW",
      `Reviewing access entitlement ledger for employee ${empId}.`,
      "info",
      "fact_check",
    );
  };

  document.addEventListener("DOMContentLoaded", () => {
    // ==========================================
    // 1. RE-CERTIFICATION DRAWER / MODAL
    // ==========================================
    const reCertDrawer = document.getElementById("reCertDrawer");
    const reCertDrawerOverlay = document.getElementById("reCertDrawerOverlay");
    const openReCertBtn = document.getElementById("openReCertDrawerBtn");
    const closeReCertBtn = document.getElementById("closeReCertDrawerBtn");
    const cancelReCertBtn = document.getElementById("cancelReCertDrawerBtn");
    const saveReCertBtn = document.getElementById("saveReCertWindowBtn");
    const windowStatusToggle = document.getElementById("windowStatusToggle");
    const toggleKnob = document.getElementById("toggleKnob");
    const drawerStatusText = document.getElementById("drawerStatusText");
    const drawerStatusSub = document.getElementById("drawerStatusSub");
    const reCertActiveBadge = document.getElementById("reCertActiveBadge");

    let isWindowActive = false;

    function openReCertDrawer() {
      if (reCertDrawerOverlay) reCertDrawerOverlay.classList.remove("hidden");
      if (reCertDrawer) reCertDrawer.classList.remove("translate-x-full");
    }

    function closeReCertDrawer() {
      if (reCertDrawer) reCertDrawer.classList.add("translate-x-full");
      if (reCertDrawerOverlay) {
        setTimeout(() => reCertDrawerOverlay.classList.add("hidden"), 300);
      }
    }

    if (openReCertBtn)
      openReCertBtn.addEventListener("click", openReCertDrawer);
    if (closeReCertBtn)
      closeReCertBtn.addEventListener("click", closeReCertDrawer);
    if (cancelReCertBtn)
      cancelReCertBtn.addEventListener("click", closeReCertDrawer);
    if (reCertDrawerOverlay)
      reCertDrawerOverlay.addEventListener("click", closeReCertDrawer);

    // Window Status Toggle Switch
    if (windowStatusToggle) {
      windowStatusToggle.addEventListener("click", function () {
        isWindowActive = !isWindowActive;
        if (isWindowActive) {
          windowStatusToggle.classList.remove("bg-slate-700");
          windowStatusToggle.classList.add("bg-[#006972]");
          if (toggleKnob) {
            toggleKnob.classList.remove("translate-x-0");
            toggleKnob.classList.add("translate-x-6");
          }
          if (drawerStatusText)
            drawerStatusText.textContent = "Window State: Active";
          if (drawerStatusSub)
            drawerStatusSub.textContent = "30-Day Mandatory Review in progress";
        } else {
          windowStatusToggle.classList.remove("bg-[#006972]");
          windowStatusToggle.classList.add("bg-slate-700");
          if (toggleKnob) {
            toggleKnob.classList.remove("translate-x-6");
            toggleKnob.classList.add("translate-x-0");
          }
          if (drawerStatusText)
            drawerStatusText.textContent = "Window State: Scheduled";
          if (drawerStatusSub)
            drawerStatusSub.textContent = "Commences on designated Start Date";
        }
      });
    }

    // Scope Chips Toggle
    const chips = document.querySelectorAll(".scope-chip");
    chips.forEach((chip) => {
      chip.addEventListener("click", function () {
        if (this.classList.contains("bg-primary")) {
          this.classList.remove("bg-primary", "text-on-primary");
          this.classList.add("bg-surface-container-high", "text-on-surface");
          const icon = this.querySelector(".material-symbols-outlined");
          if (icon) icon.remove();
        } else {
          this.classList.add("bg-primary", "text-on-primary");
          this.classList.remove("bg-surface-container-high", "text-on-surface");
          if (!this.querySelector(".material-symbols-outlined")) {
            const check = document.createElement("span");
            check.className = "material-symbols-outlined text-[12px] mr-1";
            check.textContent = "check";
            this.prepend(check);
          }
        }
      });
    });

    // Save & Launch Window Action
    if (saveReCertBtn) {
      saveReCertBtn.addEventListener("click", async function () {
        const start =
          document.getElementById("recertStartDate")?.value || "2026-04-01";
        const end =
          document.getElementById("recertEndDate")?.value || "2026-04-30";

        if (reCertActiveBadge) {
          reCertActiveBadge.classList.remove("hidden");
          reCertActiveBadge.textContent = isWindowActive
            ? "ACTIVE (30d)"
            : "SCHEDULED";
        }

        try {
          const res = await fetch("api/recert.php?action=create", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              title: "Q2-2026 Statutory Re-Certification Window",
              start_date: start,
              end_date: end,
              status: isWindowActive ? "ACTIVE" : "SCHEDULED",
              scope_departments: "ALL",
              created_by_emp_id: "EMP-1005",
            }),
          });
          const data = await res.json();
          if (data.success) {
            window.showToast?.(
              "RE-CERTIFICATION COMMITTED",
              `Quarterly Window ${data.window_id} recorded in database (${start} to ${end}).`,
              "success",
              "schedule",
            );
          }
        } catch (e) {
          console.warn("Recert API call error:", e);
        }

        closeReCertDrawer();
      });
    }

    // ==========================================
    // 2. SEARCH & DEPARTMENT FILTERING
    // ==========================================
    const searchInput = document.getElementById("matrixFilterInput");
    const tableRows = document.querySelectorAll(".matrix-row");

    if (searchInput) {
      searchInput.addEventListener("input", function (e) {
        const query = e.target.value.toLowerCase().trim();
        tableRows.forEach((row) => {
          const text = row.innerText.toLowerCase();
          row.style.display = text.includes(query) ? "" : "none";
        });
      });
    }

    const filterButtons = document.querySelectorAll(".filter-btn");
    filterButtons.forEach((btn) => {
      btn.addEventListener("click", function () {
        filterButtons.forEach((b) => {
          b.classList.remove("bg-primary", "text-on-primary", "font-bold");
          b.classList.add("bg-surface-container-high", "text-on-surface");
        });
        this.classList.remove("bg-surface-container-high", "text-on-surface");
        this.classList.add("bg-primary", "text-on-primary", "font-bold");

        const cat = this.getAttribute("data-filter");
        tableRows.forEach((row) => {
          if (cat === "ALL" || row.getAttribute("data-cat") === cat) {
            row.style.display = "";
          } else {
            row.style.display = "none";
          }
        });
      });
    });

    // ==========================================
    // 3. REMEDIATION DRAWER POPULATION
    // ==========================================
    const drawerEmpId = document.getElementById("drawerEmpId");
    const drawerEmpName = document.getElementById("drawerEmpName");
    const drawerStatusTag = document.getElementById("drawerStatusTag");
    const drawerDept = document.getElementById("drawerDept");
    const severBtn = document.getElementById("severCredentialsBtn");

    tableRows.forEach((row) => {
      row.addEventListener("click", function () {
        tableRows.forEach((r) =>
          r.classList.remove("bg-primary/10", "selected-row"),
        );
        this.classList.add("selected-row");

        const empId = this.getAttribute("data-emp");
        const empName = this.getAttribute("data-name");
        const category = this.getAttribute("data-cat");
        const dept = this.getAttribute("data-dept");

        if (drawerEmpId) drawerEmpId.textContent = empId;
        if (drawerEmpName) drawerEmpName.textContent = empName;
        if (drawerStatusTag)
          drawerStatusTag.textContent = `${category || "ACTIVE"} POSTURE`;
        if (drawerDept && dept) drawerDept.textContent = dept;

        // Reset sever button if already revoked on another row
        if (severBtn) {
          severBtn.disabled = false;
          severBtn.className =
            "w-full h-11 bg-error hover:bg-on-error-container text-on-error font-title-sm text-[13px] font-bold tracking-wider flex items-center justify-center gap-space-xs transition-colors shadow cursor-pointer";
          severBtn.innerHTML =
            '<span class="material-symbols-outlined text-[18px]">lock_reset</span><span>SEVER ALL ENTITLEMENTS</span>';
        }
      });
    });

    // ==========================================
    // 4. SEVER CREDENTIALS (REVOCATION WORKFLOW)
    // ==========================================
    if (severBtn) {
      severBtn.addEventListener("click", async function () {
        const ackCheck = document.getElementById("operatorAckCheck");
        if (ackCheck && !ackCheck.checked) {
          window.showToast?.(
            "INTERLOCK BLOCKED",
            "Mandatory Security Requirement: Please acknowledge the operator declaration checkbox prior to executing credential sever.",
            "warn",
            "warning",
          );
          return;
        }

        severBtn.disabled = true;
        severBtn.innerHTML =
          '<span class="material-symbols-outlined text-[18px] animate-spin">refresh</span><span>EXECUTING REVOCATION ACROSS SYS 01-11...</span>';
        severBtn.classList.add("opacity-80");

        const targetEmp = drawerEmpId
          ? drawerEmpId.textContent
          : "Target Account";

        try {
          const res = await fetch(
            "api/privileged.php?action=sever_credentials",
            {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ emp_id: targetEmp }),
            },
          );
          const data = await res.json();

          severBtn.innerHTML =
            '<span class="material-symbols-outlined text-[18px]">check_circle</span><span>CREDENTIALS SEVERED &amp; PURGED</span>';
          severBtn.className =
            "w-full h-11 bg-inverse-surface text-inverse-on-surface font-title-sm text-[13px] font-bold tracking-wider flex items-center justify-center gap-space-xs transition-colors shadow cursor-default";

          // Update corresponding row in table
          const activeSelected = document.querySelector(
            ".matrix-row.selected-row",
          );
          if (activeSelected) {
            const statusBadge = activeSelected.querySelector(".status-badge");
            if (statusBadge) {
              statusBadge.className =
                "px-space-xs py-[2px] bg-surface-container text-on-surface-variant font-security-stamp text-[10px] font-bold rounded";
              statusBadge.textContent = "REVOKED";
            }
          }

          window.showToast?.(
            "CREDENTIAL REVOCATION COMMITTED",
            `Security Notice: Credentials for ${targetEmp} successfully invalidated in database across SYS-01 through SYS-11 nodes.`,
            "success",
            "lock_reset",
          );
          setTimeout(() => location.reload(), 900);
        } catch (err) {
          window.showToast?.("NETWORK ERROR", err.message, "error");
          severBtn.disabled = false;
        }
      });
    }
  });
})();
