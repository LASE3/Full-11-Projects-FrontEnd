/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 09: File Center / Document Hub
 * Document Ingestion & Metadata Taxonomy Module (Live Database Integration)
 */

(function () {
  "use strict";

  let currentSelectedFile = null;

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
      currentSelectedFile = file;
      const sizeMB = (file.size / 1024 / 1024).toFixed(1);
      if (selectedFileName) {
        selectedFileName.textContent = `Selected: ${file.name} (${sizeMB} MB)`;
        selectedFileName.style.display = "block";
      }
      const titleInput = document.getElementById("meta-doc-title");
      if (titleInput && !titleInput.value) {
        titleInput.value = file.name;
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

      if (window.showToast) {
        window.showToast(
          "PRESET LOADED",
          "Applied authoritative metadata for BaltNord PRJ-2026-002 test dossier.",
          "info",
          "dataset"
        );
      }
    });
  }

  function initUploadForm() {
    const form = document.getElementById("doc-upload-form");
    const successBox = document.getElementById("upload-success-card");

    if (!form) return;

    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const title = document.getElementById("meta-doc-title").value.trim();
      const dept = document.getElementById("meta-department").value;
      const projectRef = document.getElementById("meta-project-ref").value.trim();
      const customerRef = document.getElementById("meta-customer-ref").value.trim();
      const classification = document.getElementById("meta-classification").value;
      const retention = document.getElementById("meta-retention").value;
      const custodian = document.getElementById("meta-custodian").value.trim();
      const description = document.getElementById("meta-description").value.trim();

      if (!title) {
        if (window.showToast) {
          window.showToast("VALIDATION ERROR", "Please provide a document title.", "error", "error");
        }
        return;
      }

      // Determine file size
      let fileSize = "2.4 MB";
      if (currentSelectedFile) {
        fileSize = `${(currentSelectedFile.size / 1024 / 1024).toFixed(1)} MB`;
      }

      const submitBtn = form.querySelector('button[type="submit"]');
      submitBtn.disabled = true;
      submitBtn.innerHTML =
        '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span> Ingesting into MySQL Database...';

      try {
        const response = await fetch("api/documents.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            action: "create",
            title: title,
            department: dept,
            project_ref: projectRef,
            customer_ref: customerRef,
            classification: classification,
            retention_period: retention,
            custodian: custodian,
            description: description,
            file_size: fileSize,
            status: classification === "highly-confidential" ? "In Review" : "Approved",
          }),
        });

        const res = await response.json();

        if (res.success && res.data) {
          const doc = res.data;
          form.style.display = "none";
          if (successBox) {
            successBox.style.display = "block";
            document.getElementById("disp-new-doc-id").textContent = doc.doc_id;
            document.getElementById("disp-new-doc-title").textContent = doc.file_name;
            document.getElementById("disp-new-doc-dept").textContent = doc.department;
            document.getElementById("disp-new-doc-class").textContent = doc.classification.toUpperCase();
            document.getElementById("disp-new-doc-hash").textContent = doc.file_hash;
          }

          if (window.showToast) {
            window.showToast(
              "DOCUMENT SECURED IN VAULT",
              `Assigned ${doc.doc_id}. Registered in MySQL database and Almaty HSM hardware ledger.`,
              "success",
              "security"
            );
          }

          // Update header with the subsequent next ID
          try {
            const nextRes = await fetch("api/documents.php?action=next_id");
            const nextData = await nextRes.json();
            if (nextData.success && nextData.data) {
              const headerId = document.getElementById("header-next-doc-id");
              if (headerId) headerId.textContent = `NEXT ID: ${nextData.data.next_doc_id}`;
            }
          } catch (ne) {}
        } else {
          alert("Ingestion error: " + (res.message || "Failed to ingest document"));
        }
      } catch (err) {
        alert("Network or database error: " + err.message);
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML =
          '<span class="material-symbols-outlined text-[16px]">security</span> Ingest &amp; Register in Hardware Vault';
      }
    });
  }
})();
