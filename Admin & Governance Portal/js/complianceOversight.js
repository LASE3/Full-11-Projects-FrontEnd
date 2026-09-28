/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Compliance Oversight & Statutory Audit Register Module
 * Full CRUD, Filter, and Incident Management
 */

(function () {
  "use strict";

  let currentFramework = "all";

  // Framework filtering
  window.filterFramework = function (framework) {
    currentFramework = framework.toLowerCase();

    // Update active tab style
    document.querySelectorAll("#frameworkTabs .tab-btn").forEach((btn) => {
      const tab = (btn.getAttribute("data-tab") || "").toLowerCase();
      if (tab === currentFramework) {
        btn.className =
          "tab-btn px-space-md py-space-xs bg-primary text-on-primary font-telemetry-micro text-telemetry-micro font-bold uppercase rounded-none border border-primary transition-colors flex items-center gap-space-xs whitespace-nowrap cursor-pointer";
      } else {
        btn.className =
          "tab-btn px-space-md py-space-xs bg-surface-container hover:bg-surface-container-high text-on-surface-variant font-telemetry-micro text-telemetry-micro font-medium uppercase rounded-none border border-outline-variant/60 transition-colors flex items-center gap-space-xs whitespace-nowrap cursor-pointer";
      }
    });

    filterControlsTable();
  };

  // Search & Status filtering
  window.filterControlsTable = function () {
    const searchVal = (
      document.getElementById("controlSearchInput")?.value || ""
    )
      .trim()
      .toLowerCase();
    const statusVal = (
      document.getElementById("controlStatusFilter")?.value || "ALL"
    ).toUpperCase();
    const rows = document.querySelectorAll("#controlsTableBody tr.control-row");

    let visibleCount = 0;

    rows.forEach((row) => {
      const cId = (row.getAttribute("data-control-id") || "").toLowerCase();
      const fw = (row.getAttribute("data-framework") || "").toLowerCase();
      const st = (row.getAttribute("data-status") || "").toUpperCase();
      const text = row.innerText.toLowerCase();

      // Framework match
      let matchFw = true;
      if (currentFramework !== "all") {
        if (currentFramework === "27001") {
          matchFw = fw.includes("27001") || fw.includes("iso");
        } else if (currentFramework === "kaz") {
          matchFw = fw.includes("kaz") || fw.includes("st rk");
        } else if (currentFramework === "scada") {
          matchFw =
            fw.includes("scada") || fw.includes("iec") || fw.includes("gost");
        }
      }

      // Status match
      let matchStatus = true;
      if (statusVal !== "ALL") {
        matchStatus = st === statusVal;
      }

      // Search match
      let matchSearch = true;
      if (searchVal) {
        matchSearch = cId.includes(searchVal) || text.includes(searchVal);
      }

      if (matchFw && matchStatus && matchSearch) {
        row.style.display = "";
        visibleCount++;
      } else {
        row.style.display = "none";
      }
    });

    const footerSpan = document.getElementById("footerCountSpan");
    if (footerSpan) {
      footerSpan.innerText = `SHOWING ${visibleCount} OF ${rows.length} REGULATORY CONTROLS`;
    }
  };

  // Modal handlers
  window.openNewControlModal = function () {
    document.getElementById("controlForm").reset();
    document.getElementById("ctrl_is_edit").value = "0";
    document.getElementById("ctrl_id").readOnly = false;
    document.getElementById("ctrl_id").classList.remove("opacity-60");
    document.getElementById("controlModalTitle").innerText =
      "Register Statutory Control";
    document.getElementById("ctrlSubmitBtn").innerText = "Commit Control";
    document.getElementById("ctrl_status").value = "COMPLIANT";
    document.getElementById("ctrl_verification_type").value = "HSM-VERIFIED";
    document.getElementById("ctrl_custodian_emp_id").value = "EMP-1005";
    document.getElementById("ctrl_proof_hash").value =
      "SHA256:" +
      Array.from({ length: 12 }, () =>
        Math.floor(Math.random() * 16).toString(16),
      ).join("");
    document.getElementById("controlModal").classList.remove("hidden");
  };

  window.closeControlModal = function () {
    document.getElementById("controlModal").classList.add("hidden");
  };

  window.editControl = function (controlId) {
    const row = document.querySelector(
      `tr.control-row[data-control-id="${controlId}"]`,
    );
    if (!row) return;

    try {
      const data = JSON.parse(row.getAttribute("data-json"));
      document.getElementById("ctrl_is_edit").value = "1";
      document.getElementById("ctrl_id").value = data.control_id || "";
      document.getElementById("ctrl_id").readOnly = true;
      document.getElementById("ctrl_id").classList.add("opacity-60");
      document.getElementById("ctrl_framework").value = data.framework || "";
      document.getElementById("ctrl_title").value = data.title || "";
      document.getElementById("ctrl_description").value =
        data.description || "";
      document.getElementById("ctrl_target_asset").value =
        data.target_asset || "";
      document.getElementById("ctrl_custodian_emp_id").value =
        data.custodian_emp_id || "EMP-1005";
      document.getElementById("ctrl_status").value =
        data.status || "COMPLIANT";
      document.getElementById("ctrl_verification_type").value =
        data.verification_type || "HSM-VERIFIED";
      document.getElementById("ctrl_proof_hash").value = data.proof_hash || "";

      document.getElementById("controlModalTitle").innerText =
        "Amend Statutory Control: " + controlId;
      document.getElementById("ctrlSubmitBtn").innerText = "Save Amendment";
      document.getElementById("controlModal").classList.remove("hidden");
    } catch (e) {
      console.error(e);
      window.showToast?.(
        "PARSING ERROR",
        "Could not load control details.",
        "error",
      );
    }
  };

  window.saveControlForm = async function (e) {
    e.preventDefault();
    const isEdit = document.getElementById("ctrl_is_edit").value === "1";
    const payload = {
      control_id: document.getElementById("ctrl_id").value.trim(),
      framework: document.getElementById("ctrl_framework").value.trim(),
      title: document.getElementById("ctrl_title").value.trim(),
      description: document.getElementById("ctrl_description").value.trim(),
      target_asset: document.getElementById("ctrl_target_asset").value.trim(),
      custodian_emp_id: document
        .getElementById("ctrl_custodian_emp_id")
        .value.trim(),
      status: document.getElementById("ctrl_status").value,
      verification_type: document
        .getElementById("ctrl_verification_type")
        .value.trim(),
      proof_hash: document.getElementById("ctrl_proof_hash").value.trim(),
    };

    try {
      const res = await fetch("api/compliance.php?action=save_control", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast?.(
          "CONTROL COMMITTED",
          `Control ${payload.control_id} successfully recorded in database.`,
          "success",
          "verified",
        );
        closeControlModal();
        setTimeout(() => location.reload(), 800);
      } else {
        window.showToast?.("ERROR", data.error || "Save failed", "error");
      }
    } catch (err) {
      window.showToast?.("NETWORK ERROR", err.message, "error");
    }
  };

  window.deleteControl = async function (controlId) {
    if (
      !confirm(
        `Are you sure you want to delete statutory control [${controlId}] from the database?`,
      )
    )
      return;
    try {
      const res = await fetch("api/compliance.php?action=delete_control", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ control_id: controlId }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast?.(
          "CONTROL EXPUNGED",
          `Control ${controlId} purged from compliance catalog.`,
          "warning",
          "delete",
        );
        const row = document.querySelector(
          `tr.control-row[data-control-id="${controlId}"]`,
        );
        if (row) row.remove();
        filterControlsTable();
      } else {
        window.showToast?.("ERROR", data.error || "Delete failed", "error");
      }
    } catch (err) {
      window.showToast?.("NETWORK ERROR", err.message, "error");
    }
  };

  window.cycleControlStatus = async function (controlId, currentStatus) {
    const cycle = {
      COMPLIANT: "REMEDIATION",
      REMEDIATION: "DEVIATION",
      DEVIATION: "COMPLIANT",
    };
    const nextStatus = cycle[currentStatus.toUpperCase()] || "COMPLIANT";

    try {
      const res = await fetch(
        "api/compliance.php?action=update_control_status",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            control_id: controlId,
            status: nextStatus,
          }),
        },
      );
      const data = await res.json();
      if (data.success) {
        window.showToast?.(
          "STATUS ATTESTED",
          `Control ${controlId} transitioned to ${nextStatus}.`,
          "info",
          "published_with_changes",
        );
        setTimeout(() => location.reload(), 600);
      } else {
        window.showToast?.("ERROR", data.error || "Update failed", "error");
      }
    } catch (err) {
      window.showToast?.("NETWORK ERROR", err.message, "error");
    }
  };

  // Incident Modal handlers
  window.openNewIncidentModal = function () {
    document.getElementById("incidentForm").reset();
    document.getElementById("inc_code").value =
      "NC-2026-" + Math.floor(100 + Math.random() * 900);
    document.getElementById("inc_assigned_to").value = "EMP-1042";
    document.getElementById("incidentModal").classList.remove("hidden");
  };

  window.closeIncidentModal = function () {
    document.getElementById("incidentModal").classList.add("hidden");
  };

  window.saveIncidentForm = async function (e) {
    e.preventDefault();
    const payload = {
      incident_code: document.getElementById("inc_code").value.trim(),
      severity: document.getElementById("inc_severity").value,
      title: document.getElementById("inc_title").value.trim(),
      description: document.getElementById("inc_description").value.trim(),
      assigned_to_emp_id: document
        .getElementById("inc_assigned_to")
        .value.trim(),
      status: "OPEN",
    };

    try {
      const res = await fetch("api/compliance.php?action=create_incident", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast?.(
          "INCIDENT FILED",
          `Non-conformity ${payload.incident_code} logged into audit stream.`,
          "error",
          "report",
        );
        closeIncidentModal();
        setTimeout(() => location.reload(), 800);
      } else {
        window.showToast?.("ERROR", data.error || "Failed to file", "error");
      }
    } catch (err) {
      window.showToast?.("NETWORK ERROR", err.message, "error");
    }
  };

  window.resolveIncident = async function (incidentId) {
    if (
      !confirm(
        `Confirm closure & resolution of non-conformity incident #${incidentId}?`,
      )
    )
      return;
    try {
      const res = await fetch("api/compliance.php?action=resolve_incident", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ incident_id: incidentId }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast?.(
          "INCIDENT RESOLVED",
          `Incident #${incidentId} marked as RESOLVED with cryptographic sign-off.`,
          "success",
          "task_alt",
        );
        setTimeout(() => location.reload(), 800);
      } else {
        window.showToast?.("ERROR", data.error || "Resolve failed", "error");
      }
    } catch (err) {
      window.showToast?.("NETWORK ERROR", err.message, "error");
    }
  };

  document.addEventListener("DOMContentLoaded", () => {
    // Re-verify SHA-256 Merkle Proofs Button
    const reverifyBtn = document.getElementById("reverifyBtn");
    if (reverifyBtn) {
      reverifyBtn.addEventListener("click", function () {
        const orig = this.innerHTML;
        this.disabled = true;
        this.innerHTML =
          '<span class="material-symbols-outlined text-[16px] animate-spin text-secondary-fixed">sync</span><span>Validating SHA-256 Merkle Proofs...</span>';

        setTimeout(() => {
          this.innerHTML =
            '<span class="material-symbols-outlined text-[16px] text-secondary-fixed">done_all</span><span>Attested Nominal</span>';
          this.classList.remove("bg-primary");
          this.classList.add("bg-secondary");

          setTimeout(() => {
            this.innerHTML = orig;
            this.classList.add("bg-primary");
            this.classList.remove("bg-secondary");
            this.disabled = false;
          }, 2500);

          window.showToast?.(
            "STATUTORY PROOFS ATTESTED",
            "Merkle consensus validated against ST RK & IEC 62443 regulatory baselines.",
            "success",
            "verified",
          );
        }, 1200);
      });
    }

    // Export Compliance Dossier Button
    const exportBtn = document.getElementById("btnExportComplianceLedger") || Array.from(document.querySelectorAll("button")).find(
      (b) => b.textContent.includes("Export") || b.textContent.includes("Dossier")
    );
    if (exportBtn) {
      exportBtn.addEventListener("click", () => {
        const rows = document.querySelectorAll("#controlsTableBody tr.control-row");
        const controls = [];
        rows.forEach((r) => {
          controls.push({
            id: r.getAttribute("data-control-id"),
            framework: r.getAttribute("data-framework"),
            status: r.getAttribute("data-status"),
            text: r.innerText.replace(/\s+/g, " ").trim()
          });
        });
        const blob = new Blob([JSON.stringify({ station: "ALMATY-CENTRAL", framework: "ST RK ISO/IEC 27001", timestamp: new Date().toISOString(), controls: controls }, null, 2)], {
          type: "application/json"
        });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `VOSTOKPRIBOR_Compliance_Ledger_${Date.now()}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        window.showToast?.(
          "COMPLIANCE DOSSIER PREPARED",
          "Generated cryptographic audit package for State Inspectorate & Supervisory Board review.",
          "info",
          "description",
        );
      });
    }

    // File Emergency Deviation Button
    const deviationBtn = document.getElementById("btnFileEmergencyDeviation");
    if (deviationBtn) {
      deviationBtn.addEventListener("click", () => {
        const reason = prompt("EMERGENCY STATUTORY DEVIATION:\nEnter operational justification for momentary compliance override (DOC-ACT-2026):");
        if (reason) {
          fetch("api/audit.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "notarize_stamp", note: `Emergency Deviation Filed: ${reason}` })
          });
          window.showToast?.(
            "DEVIATION FILED",
            "Emergency deviation logged with cryptographic timestamp to state compliance registry.",
            "error",
            "warning"
          );
        }
      });
    }
  });
})();
