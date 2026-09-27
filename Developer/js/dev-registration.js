/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 10: Partner Registration & Enclave Access Module
 * Connected to MySQL Database via api/partners.php
 */

(function () {
  "use strict";

  const API_URL = "api/partners.php";

  document.addEventListener("DOMContentLoaded", () => {
    // Preset auto-fill for BaltNord CUS-1002
    const btnAutofill = document.getElementById("btn-autofill-baltnord");
    if (btnAutofill) {
      btnAutofill.addEventListener("click", () => {
        document.getElementById("reg-company").value = "BaltNord Process Systems";
        document.getElementById("reg-partner-id").value = "CUS-1002";
        document.getElementById("reg-contact-name").value = "Kristaps Ozols";
        document.getElementById("reg-contact-email").value = "kristaps.ozols@baltnord.lv";
        document.getElementById("reg-project-ref").value = "PRJ-2026-002 (SCADA Ingestion Bridge)";
        document.getElementById("reg-env").value = "sandbox";
        document.getElementById("reg-public-key").value =
          "ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIBmQ8eBaltNordScadaGatewayKey2026";
        document.getElementById("reg-compliance-doc").checked = true;
        document.getElementById("reg-compliance-iec").checked = true;
        document.getElementById("reg-compliance-nda").checked = true;

        // Select standard scopes
        document.querySelectorAll(".scope-card-option").forEach((card) => {
          const chk = card.querySelector('input[type="checkbox"]');
          if (
            chk.value === "telemetry:read" ||
            chk.value === "orders:read_write"
          ) {
            chk.checked = true;
            card.classList.add("selected");
          }
        });

        if (window.showToast) {
          window.showToast(
            "PRESET LOADED",
            "Loaded authoritative partner baseline for BaltNord Process Systems (CUS-1002).",
            "info",
            "dataset"
          );
        }
      });
    }

    // Scope option card click handler
    document.querySelectorAll(".scope-card-option").forEach((card) => {
      card.addEventListener("click", function (e) {
        if (e.target.tagName.toLowerCase() !== "input") {
          const checkbox = this.querySelector('input[type="checkbox"]');
          checkbox.checked = !checkbox.checked;
        }
        const chk = this.querySelector('input[type="checkbox"]');
        if (chk.checked) {
          this.classList.add("selected");
        } else {
          this.classList.remove("selected");
        }
      });
    });

    // Form Submission to Database
    const regForm = document.getElementById("partner-registration-form");
    const regSuccessBox = document.getElementById("reg-success-container");
    const btnRegisterAnother = document.getElementById("btnRegisterAnother");

    if (btnRegisterAnother) {
      btnRegisterAnother.addEventListener("click", () => {
        if (regSuccessBox) regSuccessBox.style.display = "none";
        if (regForm) {
          regForm.reset();
          regForm.style.display = "block";
        }
      });
    }

    if (regForm) {
      regForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const company = document.getElementById("reg-company").value.trim();
        const partnerId = document.getElementById("reg-partner-id").value.trim();
        const contact = document.getElementById("reg-contact-name").value.trim();
        const email = document.getElementById("reg-contact-email").value.trim();
        const projectRef = document.getElementById("reg-project-ref").value.trim();
        const env = document.getElementById("reg-env").value;
        const publicKey = document.getElementById("reg-public-key").value.trim();

        // Collect scopes
        const scopes = [];
        document.querySelectorAll('.scope-card-option input[type="checkbox"]:checked').forEach((chk) => {
          scopes.push(chk.value);
        });

        const compDoc = document.getElementById("reg-compliance-doc").checked ? 1 : 0;
        const compIec = document.getElementById("reg-compliance-iec").checked ? 1 : 0;
        const compNda = document.getElementById("reg-compliance-nda").checked ? 1 : 0;

        if (!company || !contact || !email) {
          if (window.showToast) {
            window.showToast("VALIDATION ERROR", "Please complete all required fields.", "error", "error");
          }
          return;
        }

        const submitBtn = document.getElementById("btnSubmitRegistration") || regForm.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span> Submitting to Enclave...';

        try {
          const resp = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              action: "create",
              company_name: company,
              partner_id: partnerId,
              contact_name: contact,
              contact_email: email,
              project_ref: projectRef,
              target_environment: env,
              requested_scopes: scopes.join(", "),
              public_key: publicKey,
              compliance_doc: compDoc,
              compliance_iec: compIec,
              compliance_nda: compNda
            })
          });

          const res = await resp.json();

          if (res.success) {
            regForm.style.display = "none";
            if (regSuccessBox) {
              regSuccessBox.style.display = "block";
              document.getElementById("disp-reg-ticket").innerText = res.ticket_id || ("ENCLAVE-REQ-" + Math.floor(100000 + Math.random() * 900000));
              document.getElementById("disp-reg-company").innerText = company;
              document.getElementById("disp-reg-contact").innerText = `${contact} (${email})`;
              document.getElementById("disp-reg-env").innerText = env.toUpperCase();
            }

            if (window.showToast) {
              window.showToast(
                "REGISTRATION SUBMITTED",
                res.message || "Clearance request queued in database.",
                "success",
                "verified_user"
              );
            }

            // Reload after 1.5s to display new record in ledger
            setTimeout(() => {
              window.location.reload();
            }, 1500);
          } else {
            if (window.showToast) {
              window.showToast("ENCLAVE ERROR", res.error || "Failed to submit application", "alert", "error");
            }
          }
        } catch (err) {
          if (window.showToast) {
            window.showToast("NETWORK ERROR", err.message, "alert", "wifi_off");
          }
        } finally {
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<span class="material-symbols-outlined text-[18px]">verified_user</span> Submit Application for Security Vetting';
        }
      });
    }

    // Approve Button Action
    document.querySelectorAll(".btn-approve-app").forEach((btn) => {
      btn.addEventListener("click", async function () {
        const id = this.getAttribute("data-id");
        this.disabled = true;

        try {
          const resp = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "update_status", id: id, status: "Approved" })
          });
          const res = await resp.json();

          if (res.success) {
            const badge = document.getElementById(`app-status-badge-${id}`);
            if (badge) {
              badge.textContent = "Approved";
              badge.className = "vk-tag vk-tag-secondary";
            }
            this.remove();
            if (window.showToast) {
              window.showToast("CLEARANCE APPROVED", `Application ID #${id} marked as Approved.`, "success", "check_circle");
            }
          } else {
            this.disabled = false;
            if (window.showToast) {
              window.showToast("APPROVAL ERROR", res.error || "Failed to update status", "alert", "error");
            }
          }
        } catch (e) {
          this.disabled = false;
          if (window.showToast) {
            window.showToast("NETWORK ERROR", e.message, "alert", "wifi_off");
          }
        }
      });
    });

    // Reject Button Action
    document.querySelectorAll(".btn-reject-app").forEach((btn) => {
      btn.addEventListener("click", async function () {
        const id = this.getAttribute("data-id");
        if (!confirm("Are you sure you want to mark this clearance application as Rejected?")) return;

        this.disabled = true;
        try {
          const resp = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "update_status", id: id, status: "Rejected" })
          });
          const res = await resp.json();

          if (res.success) {
            const badge = document.getElementById(`app-status-badge-${id}`);
            if (badge) {
              badge.textContent = "Rejected";
              badge.className = "vk-tag vk-tag-alert";
            }
            this.remove();
            if (window.showToast) {
              window.showToast("CLEARANCE REJECTED", `Application ID #${id} rejected.`, "info", "cancel");
            }
          } else {
            this.disabled = false;
            if (window.showToast) {
              window.showToast("REJECTION ERROR", res.error || "Failed to update status", "alert", "error");
            }
          }
        } catch (e) {
          this.disabled = false;
          if (window.showToast) {
            window.showToast("NETWORK ERROR", e.message, "alert", "wifi_off");
          }
        }
      });
    });

    // Delete Button Action
    document.querySelectorAll(".btn-delete-app").forEach((btn) => {
      btn.addEventListener("click", async function () {
        const id = this.getAttribute("data-id");
        const ticket = this.getAttribute("data-ticket") || id;

        if (!confirm(`Permanently delete clearance application [${ticket}] from database?`)) return;

        try {
          const resp = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "delete", id: id })
          });
          const res = await resp.json();

          if (res.success) {
            const row = document.getElementById(`app-row-${id}`);
            if (row) {
              row.style.transition = "all 0.3s ease";
              row.style.opacity = "0";
              setTimeout(() => row.remove(), 300);
            }
            if (window.showToast) {
              window.showToast("RECORD PURGED", `Application [${ticket}] deleted from database.`, "info", "delete");
            }
          } else {
            if (window.showToast) {
              window.showToast("DELETE ERROR", res.error || "Could not delete application", "alert", "error");
            }
          }
        } catch (e) {
          if (window.showToast) {
            window.showToast("NETWORK ERROR", e.message, "alert", "wifi_off");
          }
        }
      });
    });

    // Edit Application Modal Handling
    const editAppModal = document.getElementById("editAppModal");
    const btnCloseEditAppModal = document.getElementById("btnCloseEditAppModal");
    const btnCancelEditAppModal = document.getElementById("btnCancelEditAppModal");
    const btnSaveEditApp = document.getElementById("btnSaveEditApp");

    const editAppId = document.getElementById("editAppId");
    const editAppCompany = document.getElementById("editAppCompany");
    const editAppPartnerId = document.getElementById("editAppPartnerId");
    const editAppContactName = document.getElementById("editAppContactName");
    const editAppContactEmail = document.getElementById("editAppContactEmail");
    const editAppProjectRef = document.getElementById("editAppProjectRef");
    const editAppEnv = document.getElementById("editAppEnv");
    const editAppStatus = document.getElementById("editAppStatus");
    const editAppScopes = document.getElementById("editAppScopes");
    const editAppPublicKey = document.getElementById("editAppPublicKey");

    function openEditModal(data) {
      if (!editAppModal || !data) return;
      if (editAppId) editAppId.value = data.id || "";
      if (editAppCompany) editAppCompany.value = data.company_name || "";
      if (editAppPartnerId) editAppPartnerId.value = data.partner_id || "";
      if (editAppContactName) editAppContactName.value = data.contact_name || "";
      if (editAppContactEmail) editAppContactEmail.value = data.contact_email || "";
      if (editAppProjectRef) editAppProjectRef.value = data.project_ref || "";
      if (editAppEnv) editAppEnv.value = data.target_environment || "sandbox";
      if (editAppStatus) editAppStatus.value = data.status || "In Review";
      if (editAppScopes) editAppScopes.value = data.requested_scopes || "";
      if (editAppPublicKey) editAppPublicKey.value = data.public_key || "";

      editAppModal.classList.add("active");
    }

    function closeEditModal() {
      if (editAppModal) editAppModal.classList.remove("active");
    }

    if (btnCloseEditAppModal) btnCloseEditAppModal.addEventListener("click", closeEditModal);
    if (btnCancelEditAppModal) btnCancelEditAppModal.addEventListener("click", closeEditModal);
    if (editAppModal) {
      editAppModal.addEventListener("click", (e) => {
        if (e.target === editAppModal) closeEditModal();
      });
    }

    document.querySelectorAll(".btn-edit-app").forEach((btn) => {
      btn.addEventListener("click", function () {
        try {
          const raw = this.getAttribute("data-app");
          if (raw) {
            const data = JSON.parse(raw);
            openEditModal(data);
          }
        } catch (e) {
          console.error("Failed to parse app data:", e);
        }
      });
    });

    if (btnSaveEditApp) {
      btnSaveEditApp.addEventListener("click", async () => {
        const id = editAppId.value.trim();
        const company = editAppCompany.value.trim();
        const contactName = editAppContactName.value.trim();
        const contactEmail = editAppContactEmail.value.trim();

        if (!id || !company || !contactName || !contactEmail) {
          if (window.showToast) {
            window.showToast("VALIDATION ERROR", "Company and contact fields are required.", "alert", "error");
          }
          return;
        }

        btnSaveEditApp.disabled = true;
        btnSaveEditApp.innerHTML = '<span class="material-symbols-outlined text-[15px] animate-spin">sync</span> Updating...';

        try {
          const resp = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              action: "update",
              id: id,
              company_name: company,
              partner_id: editAppPartnerId.value.trim(),
              contact_name: contactName,
              contact_email: contactEmail,
              project_ref: editAppProjectRef.value.trim(),
              target_environment: editAppEnv.value,
              requested_scopes: editAppScopes.value.trim(),
              public_key: editAppPublicKey.value.trim(),
              status: editAppStatus.value
            })
          });

          const res = await resp.json();

          if (res.success) {
            if (window.showToast) {
              window.showToast("RECORD UPDATED", res.message || "Application updated in database.", "success", "done_all");
            }
            closeEditModal();
            setTimeout(() => window.location.reload(), 600);
          } else {
            if (window.showToast) {
              window.showToast("UPDATE ERROR", res.error || "Failed to update application", "alert", "error");
            }
          }
        } catch (err) {
          if (window.showToast) {
            window.showToast("NETWORK ERROR", err.message, "alert", "wifi_off");
          }
        } finally {
          btnSaveEditApp.disabled = false;
          btnSaveEditApp.innerHTML = '<span class="material-symbols-outlined text-[16px]">save</span> Save Changes';
        }
      });
    }
  });
})();
