/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 09: File Center / Document Hub
 * Document Ingestion & Metadata Taxonomy Module
 */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    initDropzone();
    initClassificationSelector();
    initAutofillPreset();
    initUploadForm();
  });

  function initDropzone() {
    const dropzone = document.getElementById("upload-dropzone");
    const fileInput = document.getElementById("file-input-hidden");
    const selectedFileName = document.getElementById("selected-file-name");

    if (!dropzone || !fileInput) return;

    dropzone.addEventListener("click", () => fileInput.click());

    dropzone.addEventListener("dragover", (e) => {
      e.preventDefault();
      dropzone.classList.add("drag-over");
    });

    dropzone.addEventListener("dragleave", () => {
      dropzone.classList.remove("drag-over");
    });

    dropzone.addEventListener("drop", (e) => {
      e.preventDefault();
      dropzone.classList.remove("drag-over");
      if (e.dataTransfer.files.length > 0) {
        handleFileSelected(e.dataTransfer.files[0]);
      }
    });

    fileInput.addEventListener("change", () => {
      if (fileInput.files.length > 0) {
        handleFileSelected(fileInput.files[0]);
      }
    });

    function handleFileSelected(file) {
      if (selectedFileName) {
        selectedFileName.textContent = `Selected: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
        selectedFileName.style.display = "block";
      }
      const titleInput = document.getElementById("meta-doc-title");
      if (titleInput && !titleInput.value) {
        titleInput.value = file.name.replace(/\.[^/.]+$/, "");
      }
    }
  }

  function initClassificationSelector() {
    const classCards = document.querySelectorAll(".class-option-card");
    const hiddenClassInput = document.getElementById("meta-classification");

    classCards.forEach((card) => {
      card.addEventListener("click", function () {
        classCards.forEach((c) => c.classList.remove("selected"));
        this.classList.add("selected");
        const val = this.getAttribute("data-class-val");
        if (hiddenClassInput) hiddenClassInput.value = val;
      });
    });
  }

  function initAutofillPreset() {
    const btnPreset = document.getElementById("btn-preset-baltnord-report");
    if (!btnPreset) return;

    btnPreset.addEventListener("click", () => {
      document.getElementById("meta-doc-title").value =
        "PRJ-2026-002_SCADA_Validation_Report.pdf";
      document.getElementById("meta-department").value = "ENG";
      document.getElementById("meta-project-ref").value =
        "PRJ-2026-002 (BaltNord Process Systems)";
      document.getElementById("meta-customer-ref").value =
        "CUS-1002 (BaltNord)";
      document.getElementById("meta-retention").value = "7y";
      document.getElementById("meta-custodian").value =
        "Farida Iskakova (EMP-1019)";
      document.getElementById("meta-description").value =
        "Official automated integration and pressure telemetry test log for BaltNord SCADA interface.";

      // Select Confidential
      document
        .querySelectorAll(".class-option-card")
        .forEach((c) => c.classList.remove("selected"));
      const confCard = document.querySelector(".class-opt-confidential");
      if (confCard) {
        confCard.classList.add("selected");
        document.getElementById("meta-classification").value = "confidential";
      }

      const fileNameElem = document.getElementById("selected-file-name");
      if (fileNameElem) {
        fileNameElem.textContent =
          "Selected: PRJ-2026-002_SCADA_Validation_Report.pdf (2.4 MB)";
        fileNameElem.style.display = "block";
      }

      window.showToast(
        "PRESET LOADED",
        "Applied authoritative metadata for BaltNord PRJ-2026-002 test dossier.",
        "info",
        "dataset",
      );
    });
  }

  function initUploadForm() {
    const form = document.getElementById("doc-upload-form");
    const successBox = document.getElementById("upload-success-card");

    if (!form) return;

    form.addEventListener("submit", (e) => {
      e.preventDefault();

      const title = document.getElementById("meta-doc-title").value.trim();
      const dept = document.getElementById("meta-department").value;
      const classification = document.getElementById(
        "meta-classification",
      ).value;

      if (!title) {
        window.showToast(
          "VALIDATION ERROR",
          "Please provide a document title.",
          "error",
          "error",
        );
        return;
      }

      const submitBtn = form.querySelector('button[type="submit"]');
      submitBtn.disabled = true;
      submitBtn.innerHTML =
        '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span> Ingesting & Calculating Hash...';

      setTimeout(() => {
        form.style.display = "none";
        if (successBox) {
          successBox.style.display = "block";
          document.getElementById("disp-new-doc-id").textContent =
            "DOC-2026-016";
          document.getElementById("disp-new-doc-title").textContent = title;
          document.getElementById("disp-new-doc-dept").textContent = dept;
          document.getElementById("disp-new-doc-class").textContent =
            classification.toUpperCase();
          document.getElementById("disp-new-doc-hash").textContent =
            "9a3f2b4c810d7e5e6c1a89b034298fc1c149afbf4c8996fb92427ae41e4649b9";
        }

        window.showToast(
          "DOCUMENT SECURED IN VAULT",
          `Assigned DOC-2026-016. SHA-256 hash registered in Almaty HSM hardware ledger.`,
          "success",
          "security",
        );
      }, 1100);
    });
  }
})();
