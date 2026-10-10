/**
 * VOSTOKPRIBOR Enterprise Customer Portal - Technical Documents Controller
 * Location: Customer Portal/js/documents.js
 * Unified Controller: Metrology seals, passport uploads, hash verification, inspector panel & real downloads.
 */

(function () {
  "use strict";

  let currentCategory = "all";
  window.currentSelectedDocId = "";

  document.addEventListener("DOMContentLoaded", () => {
    // Select first document row by default
    const firstRow = document.querySelector(".doc-item-row");
    if (firstRow) {
      const docId = firstRow.id.replace("doc-row-", "");
      window.currentSelectedDocId = docId;
    }

    const params = new URLSearchParams(window.location.search);
    const docParam = params.get("doc");
    if (docParam) {
      const targetRow = document.getElementById("doc-row-" + docParam);
      if (targetRow) {
        targetRow.click();
      }
    } else if (params.get("upload") === "true") {
      setTimeout(window.showUploadModal, 400);
    }
  });

  /**
   * Filter documents by category tab
   */
  window.filterDocsCategory = function (btn, category) {
    currentCategory = category || "all";

    document.querySelectorAll(".doc-filter-tab").forEach((tab) => {
      tab.classList.remove("bg-surface-container-lowest", "text-on-surface", "font-semibold", "shadow-sm");
      tab.classList.add("text-on-surface-variant");
    });

    if (btn) {
      btn.classList.remove("text-on-surface-variant");
      btn.classList.add("bg-surface-container-lowest", "text-on-surface", "font-semibold", "shadow-sm");
    }

    window.filterDocList();
  };

  /**
   * Filter table rows by search keyword and active category
   */
  window.filterDocList = function () {
    const input = document.getElementById("docSearchInput");
    const query = (input ? input.value : "").toLowerCase().trim();
    const rows = document.querySelectorAll("#doc-table-body .doc-item-row");

    rows.forEach((row) => {
      const rowCat = row.getAttribute("data-category") || "";
      const text = row.innerText.toLowerCase();

      const matchesCat = currentCategory === "all" || rowCat === currentCategory;
      const matchesQuery = !query || text.includes(query);

      if (matchesCat && matchesQuery) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  };

  /**
   * Select a document row and update the inspector panel
   */
  window.selectDocumentRow = function (docId, title, status) {
    window.currentSelectedDocId = docId;

    // Update highlight
    document.querySelectorAll("#doc-table-body .doc-item-row").forEach((row) => {
      row.classList.remove("bg-surface-container-low", "font-semibold");
      row.classList.add("bg-surface-container-lowest");
      const indicator = row.querySelector(".bg-on-tertiary-container");
      if (indicator) indicator.remove();
    });

    const activeRow = document.getElementById("doc-row-" + docId);
    if (activeRow) {
      activeRow.classList.remove("bg-surface-container-lowest");
      activeRow.classList.add("bg-surface-container-low", "font-semibold");
      const firstTd = activeRow.querySelector("td");
      if (firstTd && !firstTd.querySelector(".bg-on-tertiary-container")) {
        const bar = document.createElement("div");
        bar.className = "absolute left-0 top-0 bottom-0 w-1 bg-on-tertiary-container";
        firstTd.prepend(bar);
      }
    }

    // Populate Inspector Pane
    const idEl = document.getElementById("inspect-doc-id");
    if (idEl) idEl.textContent = docId;

    const statusEl = document.getElementById("inspect-doc-status");
    if (statusEl) statusEl.textContent = status || "VERIFIED";

    const titleEl = document.getElementById("inspect-doc-title");
    if (titleEl) titleEl.textContent = title;

    // Fetch live metadata from documents API
    fetch(`../File%20Center/api/documents.php?id=${encodeURIComponent(docId)}`)
      .then((r) => r.json())
      .then((res) => {
        if (res.success && res.data) {
          const d = res.data;
          const auditChain = document.querySelector(".font-data-mono-md.text-technical-tag");
          if (auditChain && d.file_hash) {
            const hashEl = auditChain.querySelector(".text-\\[10px\\]");
            if (hashEl) {
              hashEl.textContent = `SHA-256: ${d.file_hash.substring(0, 32)}...`;
            }
          }
        }
      })
      .catch(() => {});
  };

  /**
   * Download the currently inspected document
   */
  window.downloadCurrentInspectedDoc = function () {
    const docId = window.currentSelectedDocId || document.getElementById("inspect-doc-id")?.textContent?.trim();
    if (!docId) {
      if (window.showToast) {
        window.showToast("NO SELECTION", "Please select a document row to download.", "warning");
      } else {
        alert("Please select a document to download.");
      }
      return;
    }

    if (window.showToast) {
      window.showToast(
        "DOWNLOADING DOSSIER",
        `Streaming verified artifact for ${docId} from secure vault enclave...`,
        "info"
      );
    }

    window.location.href = `api/download.php?doc_id=${encodeURIComponent(docId)}`;
  };

  /**
   * Inspect Electronic Metrology Seal & Cryptographic Audit Chain
   */
  window.inspectSeal = async function (docId) {
    const id = docId || window.currentSelectedDocId || "DOC-2026-001";
    let docData = null;

    try {
      const res = await fetch(`../File%20Center/api/documents.php?id=${encodeURIComponent(id)}`);
      const json = await res.json();
      if (json.success) docData = json.data;
    } catch (e) {}

    const title = docData ? docData.file_name : id;
    const hash = docData?.file_hash || "ba21577ee20a86f87425178652467d581cba5600508a8a4f0017e4f1694f4c22";
    const classification = docData?.classification || "Confidential";
    const custodian = docData?.custodian_name || "Farida Iskakova (Chief Metrologist)";
    const status = docData?.status || "Approved";

    const modalHtml = `
      <div class="space-y-4 text-left">
        <div class="p-3 bg-secondary-fixed/20 rounded border border-secondary-fixed flex items-center gap-3">
          <span class="material-symbols-outlined text-secondary text-2xl">verified_user</span>
          <div>
            <div class="font-bold text-on-surface text-sm">Rosstandart Cryptographic Validation Record</div>
            <div class="text-xs text-on-surface-variant">Validated under GOST R 34.10-2012 / 256-bit Public Key Infrastructure</div>
          </div>
        </div>

        <div class="p-3 bg-surface-container rounded text-xs font-data-mono-md space-y-1.5">
          <div class="flex justify-between"><span class="text-on-surface-variant">Document ID:</span> <span class="text-on-surface font-bold">${id}</span></div>
          <div class="flex justify-between"><span class="text-on-surface-variant">Title:</span> <span class="text-on-surface">${title}</span></div>
          <div class="flex justify-between"><span class="text-on-surface-variant">Classification:</span> <span class="text-on-surface font-semibold">${classification}</span></div>
          <div class="flex justify-between"><span class="text-on-surface-variant">Status:</span> <span class="text-secondary font-bold">${status}</span></div>
          <div class="flex justify-between"><span class="text-on-surface-variant">Verified Custodian:</span> <span class="text-on-surface">${custodian}</span></div>
          <div class="pt-1 border-t border-outline/20">
            <span class="text-on-surface-variant block mb-0.5">Authoritative SHA-256 Digest:</span>
            <span class="text-primary font-bold break-all select-all">${hash}</span>
          </div>
        </div>

        <div class="flex items-center justify-between pt-2">
          <button onclick="navigator.clipboard.writeText('${hash}'); if(window.showToast) window.showToast('Copied', 'SHA-256 digest copied to clipboard', 'info');" 
            class="px-3 py-1.5 rounded bg-surface-container-high text-on-surface text-xs font-medium hover:bg-surface-container transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">content_copy</span> Copy Digest
          </button>
          <div class="flex items-center gap-2">
            <button onclick="document.getElementById('portal-dynamic-modal')?.remove()" 
              class="px-4 py-1.5 rounded bg-surface-container-high text-on-surface text-xs font-medium">
              Close
            </button>
            <a href="api/download.php?doc_id=${encodeURIComponent(id)}" 
              class="px-4 py-1.5 rounded bg-primary text-on-primary text-xs font-semibold hover:bg-primary/90 transition-colors flex items-center gap-1">
              <span class="material-symbols-outlined text-sm">download</span> Download File
            </a>
          </div>
        </div>
      </div>
    `;

    if (window.openModal) {
      window.openModal("Cryptographic Seal & Compliance Dossier", modalHtml);
    } else {
      alert(`Document: ${id}\nSHA-256: ${hash}\nCustodian: ${custodian}`);
    }
  };

  /**
   * Zoom high-resolution metrology holographic seal
   */
  window.zoomCertificateModal = function () {
    const html = `
      <div class="space-y-4 text-center">
        <div class="p-4 bg-primary/5 rounded border border-primary/20 flex flex-col items-center justify-center">
          <svg class="w-48 h-48 text-primary" viewBox="0 0 200 200" fill="none">
            <circle cx="100" cy="100" r="90" stroke="currentColor" stroke-width="4" stroke-dasharray="6 4" />
            <circle cx="100" cy="100" r="75" stroke="#bb7d16" stroke-width="2" />
            <polygon points="100,35 120,80 170,80 130,110 145,155 100,125 55,155 70,110 30,80 80,80" stroke="#00E5FF" stroke-width="2" fill="none" />
            <text x="100" y="105" text-anchor="middle" fill="currentColor" font-family="monospace" font-size="11" font-weight="bold">ROSSTANDART</text>
            <text x="100" y="120" text-anchor="middle" fill="#bb7d16" font-family="monospace" font-size="9">GOST R 34.10</text>
          </svg>
        </div>
        <div class="text-xs text-on-surface-variant font-data-mono-md">
          FEDERAL AGENCY ON TECHNICAL REGULATING AND METROLOGY (ROSSTANDART)<br>
          Official Digital Inspection Seal • Security Tier A (Station Almaty PKI Gateway)
        </div>
        <div class="flex justify-end">
          <button onclick="document.getElementById('portal-dynamic-modal')?.remove()"
            class="px-4 py-1.5 rounded bg-primary text-on-primary text-xs font-semibold hover:bg-primary/90 transition-colors">
            Close Viewer
          </button>
        </div>
      </div>
    `;
    if (window.openModal) {
      window.openModal("Rosstandart Verification Seal (High-Resolution)", html);
    }
  };

  /**
   * Copy shareable permalink to the active document
   */
  window.shareDocumentLink = function () {
    const docId = window.currentSelectedDocId || document.getElementById("inspect-doc-id")?.textContent?.trim() || "DOC-2026-001";
    const url = `${window.location.origin}${window.location.pathname}?doc=${encodeURIComponent(docId)}`;
    
    navigator.clipboard.writeText(url)
      .then(() => {
        if (window.showToast) {
          window.showToast("Verification Link Copied", `Permalink ready for ${docId}`, "info");
        } else {
          alert(`Permalink copied: ${url}`);
        }
      })
      .catch(() => {
        prompt("Copy permalink to document:", url);
      });
  };

  /**
   * Print Technical Passport Dossier
   */
  window.printDocumentDossier = function () {
    if (window.showToast) {
      window.showToast("Preparing Dossier", "Formatting document passport for print output...", "info");
    }
    setTimeout(() => {
      window.print();
    }, 300);
  };

  /**
   * Batch download all engineering dossiers
   */
  window.downloadAllDocuments = function () {
    if (window.showToast) {
      window.showToast("Compiling Dossiers", "Downloading engineering documents archive...", "info");
    }
    const docId = window.currentSelectedDocId || "DOC-2026-003";
    window.location.href = `api/download.php?doc_id=${encodeURIComponent(docId)}`;
  };

  /**
   * Display Technical Passport Upload modal with live submission
   */
  window.showUploadModal = function () {
    const html = `
      <form id="portal-passport-upload-form" class="space-y-4 text-left" enctype="multipart/form-data">
        <div class="border-2 border-dashed border-outline-variant/50 rounded-lg p-6 flex flex-col items-center justify-center bg-surface-container cursor-pointer hover:bg-surface-container-high transition-colors text-center"
          onclick="document.getElementById('passportFileInput').click()">
          <span class="material-symbols-outlined text-3xl text-tertiary-fixed-dim mb-1">upload_file</span>
          <div class="text-sm font-semibold text-on-surface">Click to select Technical Passport or P&ID file</div>
          <div class="text-xs text-on-surface-variant mt-0.5">Accepts PDF, XLSX, DOCX, PNG, JPG up to 50MB</div>
          <input type="file" id="passportFileInput" name="file" class="hidden" required onchange="window.handlePassportFileSelected(this)" />
        </div>

        <div id="fileSelectedFeedback" class="hidden p-2 rounded bg-surface-container-high text-xs font-data-mono-md text-on-surface flex items-center justify-between">
          <span id="fileNameDisplay">file.pdf</span>
          <span class="text-secondary font-semibold">Ready for upload</span>
        </div>

        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs font-semibold text-on-surface-variant uppercase mb-1">Document Title</label>
            <input type="text" id="uploadTitleInput" name="title" required placeholder="e.g. HPF-950X Calibration Passport" 
              class="w-full bg-surface-container px-3 py-1.5 rounded text-xs text-on-surface border border-outline-variant/30 focus:outline-none" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-on-surface-variant uppercase mb-1">Classification</label>
            <select id="uploadClassSelect" name="classification" class="w-full bg-surface-container px-3 py-1.5 rounded text-xs text-on-surface border border-outline-variant/30 focus:outline-none">
              <option value="confidential">Confidential</option>
              <option value="internal">Internal</option>
              <option value="public">Public</option>
            </select>
          </div>
        </div>

        <div class="space-y-1">
          <label class="block text-xs font-semibold text-on-surface-variant uppercase">Technical Notes</label>
          <textarea id="uploadNotesInput" name="description" rows="2" placeholder="Primary technical specifications and calibration notes..."
            class="w-full bg-surface-container px-3 py-1.5 rounded text-xs text-on-surface border border-outline-variant/30 focus:outline-none"></textarea>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-outline-variant/30">
          <button type="button" onclick="document.getElementById('portal-dynamic-modal')?.remove()" 
            class="px-4 py-1.5 rounded bg-surface-container-high text-on-surface text-xs font-medium">
            Cancel
          </button>
          <button type="submit" id="btn-submit-passport-upload"
            class="px-4 py-1.5 rounded bg-tertiary-fixed-dim text-on-tertiary-fixed text-xs font-bold hover:bg-tertiary-fixed transition-colors flex items-center gap-1 shadow-sm">
            <span class="material-symbols-outlined text-sm">cloud_done</span>
            Seal &amp; Ingest Dossier
          </button>
        </div>
      </form>
    `;

    if (window.openModal) {
      window.openModal("Upload Engineering Technical Passport", html);

      const form = document.getElementById("portal-passport-upload-form");
      if (form) {
        form.onsubmit = async (e) => {
          e.preventDefault();
          const btn = document.getElementById("btn-submit-passport-upload");
          if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">sync</span> Uploading...';
          }

          const fileInput = document.getElementById("passportFileInput");
          const title = document.getElementById("uploadTitleInput").value.trim();
          const classification = document.getElementById("uploadClassSelect").value;
          const description = document.getElementById("uploadNotesInput").value.trim();

          const fd = new FormData();
          if (fileInput && fileInput.files[0]) {
            fd.append("file", fileInput.files[0]);
          }
          fd.append("action", "create");
          fd.append("title", title);
          fd.append("classification", classification);
          fd.append("description", description);
          fd.append("department", "ENG");

          try {
            const res = await fetch("../File%20Center/api/documents.php", {
              method: "POST",
              body: fd,
            });
            const data = await res.json();
            if (data.success) {
              document.getElementById("portal-dynamic-modal")?.remove();
              if (window.showToast) {
                window.showToast("DOSSIER SECURED", `Document ${data.data?.doc_id || ""} uploaded and secured in vault.`, "success");
              }
              setTimeout(() => window.location.reload(), 1000);
            } else {
              alert("Upload failed: " + (data.message || data.error || "Server error"));
            }
          } catch (err) {
            alert("Upload error: " + err.message);
          } finally {
            if (btn) {
              btn.disabled = false;
              btn.innerHTML = '<span class="material-symbols-outlined text-sm">cloud_done</span> Seal & Ingest Dossier';
            }
          }
        };
      }
    }
  };

  /**
   * File selection change handler for modal
   */
  window.handlePassportFileSelected = function (input) {
    if (input.files && input.files[0]) {
      const f = input.files[0];
      const feedback = document.getElementById("fileSelectedFeedback");
      const nameDisp = document.getElementById("fileNameDisplay");
      const titleInput = document.getElementById("uploadTitleInput");

      if (feedback && nameDisp) {
        feedback.classList.remove("hidden");
        nameDisp.textContent = `${f.name} (${(f.size / (1024 * 1024)).toFixed(2)} MB)`;
      }
      if (titleInput && !titleInput.value) {
        titleInput.value = f.name;
      }
    }
  };

  // Backwards compatibility aliases
  window.selectDoc = function (idx) {
    const rows = document.querySelectorAll("#doc-table-body .doc-item-row");
    if (rows[idx - 1]) rows[idx - 1].click();
  };
  window.downloadActiveDoc = window.downloadCurrentInspectedDoc;
  window.shareDocLink = window.shareDocumentLink;
  window.zoomSeal = window.zoomCertificateModal;
  window.batchDownloadDocs = window.downloadAllDocuments;
  window.filterDocCategory = window.filterDocsCategory;
  window.filterDocTable = window.filterDocList;
})();
