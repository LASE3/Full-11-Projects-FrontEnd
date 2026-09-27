/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 10: Integration Guides & Statutory Documentation Module
 * Connected to MySQL Database via api/guides.php
 */

(function () {
  "use strict";

  const API_URL = "api/guides.php";

  document.addEventListener("DOMContentLoaded", () => {
    const guideModal = document.getElementById("guideModal");
    const guideModalTitle = document.getElementById("guideModalTitle");
    const btnOpenCreateGuide = document.getElementById("btnOpenCreateGuide");
    const btnCloseGuideModal = document.getElementById("btnCloseGuideModal");
    const btnCancelGuideModal = document.getElementById("btnCancelGuideModal");
    const btnSaveGuide = document.getElementById("btnSaveGuide");

    const guideId = document.getElementById("guideId");
    const guideCode = document.getElementById("guideCode");
    const guideSection = document.getElementById("guideSection");
    const guideTitle = document.getElementById("guideTitle");
    const guideCategory = document.getElementById("guideCategory");
    const guideClassification = document.getElementById("guideClassification");
    const guideIcon = document.getElementById("guideIcon");
    const guideSummary = document.getElementById("guideSummary");
    const guideSnippet = document.getElementById("guideSnippet");
    const guideFooterNote = document.getElementById("guideFooterNote");

    function openModal(isEdit, data = null) {
      if (!guideModal) return;

      if (isEdit && data) {
        if (guideModalTitle) guideModalTitle.textContent = "Edit Integration Guide";
        if (guideId) guideId.value = data.id || "";
        if (guideCode) guideCode.value = data.guide_code || "";
        if (guideSection) guideSection.value = data.section_number || 1;
        if (guideTitle) guideTitle.value = data.title || "";
        if (guideCategory) guideCategory.value = data.category || "General Guide";
        if (guideClassification) guideClassification.value = data.classification || "Internal Standard";
        if (guideIcon) guideIcon.value = data.icon || "menu_book";
        if (guideSummary) guideSummary.value = data.summary || "";
        if (guideSnippet) guideSnippet.value = data.code_snippet || "";
        if (guideFooterNote) guideFooterNote.value = data.footer_note || "";
      } else {
        if (guideModalTitle) guideModalTitle.textContent = "Register New Integration Guide";
        if (guideId) guideId.value = "";
        if (guideCode) guideCode.value = "GUIDE-" + Math.floor(100 + Math.random() * 900);
        if (guideSection) guideSection.value = "4";
        if (guideTitle) guideTitle.value = "";
        if (guideCategory) guideCategory.value = "Protocol Specification";
        if (guideClassification) guideClassification.value = "Internal Standard";
        if (guideIcon) guideIcon.value = "sync_alt";
        if (guideSummary) guideSummary.value = "";
        if (guideSnippet) guideSnippet.value = "";
        if (guideFooterNote) guideFooterNote.value = "Attested under ISO 27001 & ST RK IEC 62443.";
      }

      guideModal.classList.add("active");
    }

    function closeModal() {
      if (guideModal) guideModal.classList.remove("active");
    }

    if (btnOpenCreateGuide) {
      btnOpenCreateGuide.addEventListener("click", () => openModal(false));
    }
    if (btnCloseGuideModal) {
      btnCloseGuideModal.addEventListener("click", closeModal);
    }
    if (btnCancelGuideModal) {
      btnCancelGuideModal.addEventListener("click", closeModal);
    }
    if (guideModal) {
      guideModal.addEventListener("click", (e) => {
        if (e.target === guideModal) closeModal();
      });
    }

    // Edit Button Handlers
    document.querySelectorAll(".btn-edit-guide").forEach((btn) => {
      btn.addEventListener("click", function () {
        try {
          const raw = this.getAttribute("data-guide");
          if (raw) {
            const data = JSON.parse(raw);
            openModal(true, data);
          }
        } catch (e) {
          console.error("Failed to parse guide JSON:", e);
        }
      });
    });

    // Save Guide (Create or Update)
    if (btnSaveGuide) {
      btnSaveGuide.addEventListener("click", async () => {
        const idVal = guideId ? guideId.value.trim() : "";
        const codeVal = guideCode ? guideCode.value.trim() : "";
        const titleVal = guideTitle ? guideTitle.value.trim() : "";
        const summaryVal = guideSummary ? guideSummary.value.trim() : "";

        if (!codeVal || !titleVal || !summaryVal) {
          if (window.showToast) {
            window.showToast("VALIDATION ERROR", "Guide Code, Title, and Summary are required.", "alert", "error");
          }
          return;
        }

        const isEdit = Boolean(idVal);
        const action = isEdit ? "update" : "create";

        const payload = {
          action: action,
          guide_code: codeVal,
          section_number: parseInt(guideSection ? guideSection.value : 1, 10) || 1,
          title: titleVal,
          category: guideCategory ? guideCategory.value.trim() : "General Guide",
          classification: guideClassification ? guideClassification.value : "Internal Standard",
          icon: guideIcon ? guideIcon.value.trim() : "menu_book",
          summary: summaryVal,
          code_snippet: guideSnippet ? guideSnippet.value.trim() : "",
          footer_note: guideFooterNote ? guideFooterNote.value.trim() : ""
        };
        if (isEdit) payload.id = idVal;

        btnSaveGuide.disabled = true;
        btnSaveGuide.innerHTML = '<span class="material-symbols-outlined text-[15px] animate-spin">sync</span> Saving...';

        try {
          const resp = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
          });
          const res = await resp.json();

          if (res.success) {
            if (window.showToast) {
              window.showToast(
                isEdit ? "GUIDE UPDATED" : "GUIDE REGISTERED",
                res.message || "Integration guide persisted to database.",
                "success",
                "done_all"
              );
            }
            closeModal();
            setTimeout(() => window.location.reload(), 600);
          } else {
            if (window.showToast) {
              window.showToast("DATABASE ERROR", res.error || "Failed to persist guide.", "alert", "error");
            }
          }
        } catch (err) {
          if (window.showToast) {
            window.showToast("NETWORK ERROR", err.message, "alert", "wifi_off");
          }
        } finally {
          btnSaveGuide.disabled = false;
          btnSaveGuide.innerHTML = '<span class="material-symbols-outlined text-[16px]">save</span> Save Guide';
        }
      });
    }

    // Delete Guide Handler
    document.querySelectorAll(".btn-delete-guide").forEach((btn) => {
      btn.addEventListener("click", async function () {
        const id = this.getAttribute("data-id");
        const title = this.getAttribute("data-title") || "Guide #" + id;

        if (!confirm(`Are you sure you want to delete guide "${title}" from the database?`)) {
          return;
        }

        try {
          const resp = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "delete", id: id })
          });
          const res = await resp.json();

          if (res.success) {
            const card = document.getElementById(`guide-card-${id}`);
            if (card) {
              card.style.transition = "all 0.3s ease";
              card.style.opacity = "0";
              setTimeout(() => card.remove(), 300);
            }
            if (window.showToast) {
              window.showToast("GUIDE DELETED", `Guide "${title}" removed from database.`, "info", "delete");
            }
          } else {
            if (window.showToast) {
              window.showToast("DELETE ERROR", res.error || "Failed to delete guide.", "alert", "error");
            }
          }
        } catch (e) {
          if (window.showToast) {
            window.showToast("NETWORK ERROR", e.message, "alert", "wifi_off");
          }
        }
      });
    });
  });
})();
