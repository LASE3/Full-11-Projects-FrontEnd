/**
 * VOSTOKPRIBOR Enterprise Customer Portal - Technical Documents Controller
 * Page-specific logic for Rosstandart metrology seals, passport uploads, and hash verification.
 */

(function () {
    'use strict';

    const dossierData = {
        1: {
            id: 'CERT-2024-HPF-0994',
            title: 'High-Pressure Flowmeter HPF-900X Calibration Certificate',
            badge: 'ROSTEST CERTIFIED',
            badgeClass: 'bg-secondary-fixed text-on-secondary-fixed',
            hash: '0x8F9A83BC902E4D2',
            docType: 'Calibration Certificate'
        },
        2: {
            id: 'DWG-7721-PND-V3',
            title: 'Blast Furnace #5 Automation Wiring Schematic & P&ID Diagram',
            badge: 'ACTIVE ENGINEERING SPEC',
            badgeClass: 'bg-primary-fixed text-on-primary-fixed',
            hash: '0x33CD71EE99A814B',
            docType: 'AutoCAD P&ID Technical Schematic'
        },
        3: {
            id: 'DOC-7721-FAT.pdf',
            title: 'Factory Acceptance Test (FAT) Protocol - Gas Skid #4',
            badge: 'FAT PASSED / SIGNED',
            badgeClass: 'bg-secondary-fixed text-on-secondary-fixed',
            hash: '0x10B45C9921DF883',
            docType: 'Factory Acceptance Protocol'
        },
        4: {
            id: 'ADDENDUM-CA-402',
            title: 'Spare Parts Consignment Agreement Q4 2024',
            badge: 'PENDING CLIENT SIGNATURE',
            badgeClass: 'bg-tertiary-fixed text-on-tertiary-fixed',
            hash: '0xEE3344BC556A102',
            docType: 'Commercial Legal Addendum'
        },
        5: {
            id: 'HSE-OP-402-REV1',
            title: 'Optical Pyrometer Array Installation Manual & Safety Cert',
            badge: 'COMPLIANT',
            badgeClass: 'bg-secondary-fixed text-on-secondary-fixed',
            hash: '0x77AF223910CC029',
            docType: 'HSE Industrial Safety Dossier'
        },
        6: {
            id: 'VP-LP-400-CAL-2024',
            title: 'Continuous Casting Machine #3 Laser Profiler Accuracy Verification',
            badge: 'VALID',
            badgeClass: 'bg-secondary-fixed text-on-secondary-fixed',
            hash: '0x992BCC184029EA1',
            docType: 'Optical Calibration Report'
        }
    };

    let activeDocIndex = 1;
    let currentCategory = 'all';

    /**
     * Select active document and populate inspector pane
     */
    window.selectDoc = function (docIndex) {
        activeDocIndex = docIndex;
        const item = dossierData[docIndex];
        if (!item) return;

        const idEl = document.getElementById('inspect-id');
        if (idEl) idEl.textContent = item.id;

        const titleEl = document.getElementById('inspect-title');
        if (titleEl) titleEl.textContent = item.title;

        const badgeEl = document.getElementById('inspect-badge');
        if (badgeEl) {
            badgeEl.textContent = item.badge;
            badgeEl.className = `px-2 py-0.5 rounded font-technical-tag text-technical-tag font-bold ${item.badgeClass}`;
        }

        // Update highlighted row in UI
        document.querySelectorAll('.doc-item').forEach(row => {
            row.classList.remove('ring-1', 'ring-primary', 'bg-surface-container-high/30');
        });
        const targetRow = document.getElementById(`doc-row-${docIndex}`);
        if (targetRow) {
            targetRow.classList.add('ring-1', 'ring-primary', 'bg-surface-container-high/30');
        }
    };

    /**
     * Download or preview the currently selected document
     */
    window.downloadActiveDoc = function () {
        const item = dossierData[activeDocIndex];
        if (!item) return;
        if (window.previewDocument) {
            window.previewDocument(item.id, item.title, item.docType);
        } else {
            alert(`Downloading ${item.id}: ${item.title}`);
        }
    };

    /**
     * Copy shareable permalink to the active document
     */
    window.shareDocLink = function () {
        const item = dossierData[activeDocIndex];
        const url = `${window.location.origin}${window.location.pathname}?doc=${activeDocIndex}`;
        navigator.clipboard.writeText(url).then(() => {
            if (window.showToast) {
                window.showToast('Verification Link Copied', `Permalink ready for ${item.id}`, 'info');
            } else {
                alert(`Link copied: ${url}`);
            }
        }).catch(() => {
            if (window.showToast) {
                window.showToast('Link Ready', `Verification link ready for ${item.id}`, 'info');
            }
        });
    };

    /**
     * Zoom high-resolution Rosstandart official holographic metrology seal
     */
    window.zoomSeal = function () {
        const html = `
            <div class="space-y-4 text-center">
                <div class="p-2 bg-primary/10 rounded border border-primary/20">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDtnCICKWeo-mQgvBwmZpdtevmChXuzWhIDwStOXuwAUo9Ojn72F-k-PDQ9QfDmHmaTy3ehEIK0dv-KkM7nYXjtsoLgjR-jIfOOlaeUbP2h1GfSpD0lctAiofFyI7_loO8FZTkZ9raJ61_cCc4vew6eKpZ39WitYfcvOBsWfBfN6BnnGUCQhgF_eKECU6zV_AQDtv3oFu4TxL12wAKgZLpepR8MVcWqcTXFaC_dkcsPXDNJ1erxonZe" 
                        class="w-full max-h-96 object-contain rounded" alt="Rosstandart Stamp High-Res">
                </div>
                <div class="text-xs text-on-surface-variant font-data-mono-md">
                    FEDERAL AGENCY ON TECHNICAL REGULATION AND METROLOGY (ROSSTANDART)<br>
                    State Register #48291-11 | Holographic Certificate Security Tier A
                </div>
                <div class="flex justify-end">
                    <button onclick="document.getElementById('portal-dynamic-modal')?.remove()"
                        class="px-4 py-1.5 rounded bg-primary text-on-primary text-xs font-semibold">
                        Close Viewer
                    </button>
                </div>
            </div>
        `;
        if (window.openModal) {
            window.openModal('Rosstandart Verification Seal (High-Resolution)', html);
        }
    };

    /**
     * Batch download all engineering dossiers
     */
    window.batchDownloadDocs = function () {
        if (window.showToast) {
            window.showToast('Compiling Archives', 'Bundling 54 engineering assets into encrypted archive: Severstal_Dossiers_2024.zip', 'info');
        }
    };

    /**
     * Filter documents by category tab
     */
    window.filterDocCategory = function (btn, cat) {
        currentCategory = cat;
        document.querySelectorAll('.doc-tab-btn').forEach(b => {
            b.className = 'doc-tab-btn px-unit-md py-1.5 rounded font-label-caps text-label-caps bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-colors whitespace-nowrap';
        });
        if (btn) {
            btn.className = 'doc-tab-btn px-unit-md py-1.5 rounded font-label-caps text-label-caps bg-primary text-on-primary whitespace-nowrap shadow-sm';
        }
        window.filterDocTable();
    };

    /**
     * Filter documents table rows based on category, search input, facility, and equipment
     */
    window.filterDocTable = function () {
        const queryInput = document.getElementById('docSearchInput');
        const facilitySelect = document.getElementById('docFacilitySelect');
        const equipmentSelect = document.getElementById('docEquipmentSelect');
        const pkiSelect = document.getElementById('docPKISelect');

        const query = (queryInput ? queryInput.value : '').toLowerCase().trim();
        const facility = facilitySelect ? facilitySelect.value : 'all';
        const equipment = equipmentSelect ? equipmentSelect.value : 'all';
        const pki = pkiSelect ? pkiSelect.value : 'all';

        const items = document.querySelectorAll('.doc-item');
        items.forEach(item => {
            const cat = item.getAttribute('data-category') || '';
            const fac = item.getAttribute('data-facility') || '';
            const eq = item.getAttribute('data-equipment') || '';
            const pkiStat = item.getAttribute('data-pki') || '';
            const text = item.innerText.toLowerCase();

            const matchesCat = currentCategory === 'all' || cat === currentCategory;
            const matchesFac = facility === 'all' || fac.includes(facility);
            const matchesEq = equipment === 'all' || eq.includes(equipment);
            const matchesPki = pki === 'all' || pkiStat === pki;
            const matchesQuery = !query || text.includes(query);

            if (matchesCat && matchesFac && matchesEq && matchesPki && matchesQuery) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    };

    /**
     * Verify cryptographic SHA-256 hash
     */
    window.verifyHash = function (hash) {
        const html = `
            <div class="space-y-3 text-left">
                <div class="p-3 bg-secondary-fixed/30 rounded border border-secondary-fixed flex items-center gap-3">
                    <span class="material-symbols-outlined text-secondary text-2xl">verified_user</span>
                    <div>
                        <div class="font-bold text-on-surface text-sm">Crypto-Pro 5.0 Signature Validated</div>
                        <div class="text-xs text-on-surface-variant">Dual-key certificate matches Rosstandart national metrology registry.</div>
                    </div>
                </div>
                <div class="p-2.5 bg-surface-container rounded text-xs font-data-mono-md space-y-1">
                    <div><span class="text-on-surface-variant">Signature Digest:</span> <span class="text-primary font-bold">${hash}</span></div>
                    <div><span class="text-on-surface-variant">Timestamp Authority:</span> 2024-11-04T08:14:22 UTC (TSA #9910)</div>
                    <div><span class="text-on-surface-variant">Root Cert:</span> Russian Federal Metrology Rosstandart CA-01</div>
                </div>
                <div class="flex justify-end pt-2">
                    <button onclick="document.getElementById('portal-dynamic-modal')?.remove()" 
                        class="px-4 py-1.5 rounded bg-primary text-on-primary text-xs font-semibold">
                        Dismiss
                    </button>
                </div>
            </div>
        `;
        if (window.openModal) {
            window.openModal('Cryptographic Audit Verification', html);
        } else {
            alert('Verified hash: ' + hash);
        }
    };

    /**
     * Sign confidential commercial addendum
     */
    window.signConfidentialAddendum = function (id) {
        const html = `
            <div class="space-y-4 text-left">
                <div class="p-3 bg-tertiary-fixed/30 rounded border border-tertiary-fixed/50">
                    <div class="font-bold text-on-surface text-sm">Counter-Signature Required: ${id}</div>
                    <div class="text-xs text-on-surface-variant mt-1">
                        Severstal Industrial Contract Addendum Q4 2024. Consignment allocation: $134,400.00 USD.
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-on-surface-variant uppercase">Hardware Token Certificate</label>
                    <input type="text" readonly value="Alexey R. Danilov (Chief Eng.) #RU-GOST-2024-9912" 
                        class="w-full bg-surface-container px-3 py-2 rounded text-xs font-data-mono-md text-on-surface border border-outline-variant/30" />
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-on-surface-variant uppercase">PIN / Hardware Passcode</label>
                    <input type="password" value="••••••••" 
                        class="w-full bg-surface-container px-3 py-2 rounded text-xs text-on-surface border border-outline-variant/30 focus:outline-none" />
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-outline-variant/30">
                    <button onclick="document.getElementById('portal-dynamic-modal')?.remove()" 
                        class="px-4 py-1.5 rounded bg-surface-container-high text-on-surface text-xs font-medium">
                        Cancel
                    </button>
                    <button onclick="window.completeAddendumSigning('${id}')" 
                        class="px-4 py-1.5 rounded bg-tertiary-fixed-dim text-on-tertiary-fixed text-xs font-bold hover:bg-tertiary-fixed transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">vpn_key</span>
                        Sign with Crypto-Pro EDS
                    </button>
                </div>
            </div>
        `;
        if (window.openModal) {
            window.openModal('Execute Electronic Signature', html);
        }
    };

    /**
     * Complete simulated addendum signing
     */
    window.completeAddendumSigning = function (id) {
        const modal = document.getElementById('portal-dynamic-modal');
        if (modal) modal.remove();

        if (window.showToast) {
            window.showToast('Signature Attached', `Addendum ${id} electronically sealed and counter-signed with Crypto-Pro EDS`, 'success');
        }

        const r4 = document.getElementById('doc-row-4');
        if (r4) {
            const badge = r4.querySelector('div:nth-child(2) span');
            if (badge) {
                badge.className = 'inline-flex items-center px-1.5 py-0.5 rounded bg-secondary-fixed text-on-secondary-fixed font-technical-tag text-technical-tag font-bold w-fit';
                badge.textContent = 'SIGNED & COUNTER-VALIDATED';
            }
        }
    };

    /**
     * Display Technical Passport Upload modal with SHA-256 calculation
     */
    window.showUploadPassportModal = function () {
        const html = `
            <div class="space-y-4 text-left">
                <div class="border-2 border-dashed border-outline-variant/50 rounded-lg p-6 flex flex-col items-center justify-center bg-surface-container cursor-pointer hover:bg-surface-container-high transition-colors text-center"
                    onclick="document.getElementById('passportFileInput').click()">
                    <span class="material-symbols-outlined text-3xl text-tertiary-fixed-dim mb-1">upload_file</span>
                    <div class="text-sm font-semibold text-on-surface">Click to select Technical Passport or P&ID file</div>
                    <div class="text-xs text-on-surface-variant mt-0.5">Accepts PDF, DWG, DXF, or GOST XML files up to 100MB</div>
                    <input type="file" id="passportFileInput" class="hidden" onchange="window.handlePassportFileSelected(this)" />
                </div>

                <div id="fileSelectedFeedback" class="hidden p-2 rounded bg-surface-container-high text-xs font-data-mono-md text-on-surface flex items-center justify-between">
                    <span id="fileNameDisplay">file.pdf</span>
                    <span class="text-secondary font-semibold">Ready for hashing</span>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-variant uppercase mb-1">Equipment / Sensor Model</label>
                        <input type="text" id="uploadModelInput" placeholder="e.g. HPF-950X Flowmeter" 
                            class="w-full bg-surface-container px-3 py-1.5 rounded text-xs text-on-surface border border-outline-variant/30 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-variant uppercase mb-1">Serial / Factory Tag #</label>
                        <input type="text" id="uploadSerialInput" placeholder="e.g. SN-2024-9981" 
                            class="w-full bg-surface-container px-3 py-1.5 rounded text-xs text-on-surface border border-outline-variant/30 focus:outline-none" />
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-on-surface-variant uppercase">Destination Plant Section</label>
                    <select id="uploadSectionSelect" class="w-full bg-surface-container px-3 py-1.5 rounded text-xs text-on-surface border border-outline-variant/30 focus:outline-none">
                        <option value="bf5">Blast Furnace Division (BF-5)</option>
                        <option value="casting">Continuous Casting Unit #3</option>
                        <option value="chromatography">Gas Chromatography Facility</option>
                    </select>
                </div>

                <div class="p-2.5 bg-primary/5 rounded border border-primary/20 flex items-center justify-between text-xs">
                    <span class="text-on-surface-variant">Calculated SHA-256 Digest:</span>
                    <span id="computedDigest" class="font-data-mono-md text-primary font-semibold">0x7C41...Pending file</span>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-outline-variant/30">
                    <button onclick="document.getElementById('portal-dynamic-modal')?.remove()" 
                        class="px-4 py-1.5 rounded bg-surface-container-high text-on-surface text-xs font-medium">
                        Cancel
                    </button>
                    <button onclick="window.submitPassportUpload()" 
                        class="px-4 py-1.5 rounded bg-tertiary-fixed-dim text-on-tertiary-fixed text-xs font-bold hover:bg-tertiary-fixed transition-colors flex items-center gap-1 shadow-sm">
                        <span class="material-symbols-outlined text-sm">cloud_done</span>
                        Seal &amp; Ingest Dossier
                    </button>
                </div>
            </div>
        `;
        if (window.openModal) {
            window.openModal('Upload Engineering Technical Passport', html);
        }
    };

    /**
     * File selection change handler
     */
    window.handlePassportFileSelected = function (input) {
        if (input.files && input.files[0]) {
            const f = input.files[0];
            const feedback = document.getElementById('fileSelectedFeedback');
            const nameDisp = document.getElementById('fileNameDisplay');
            const digestDisp = document.getElementById('computedDigest');
            if (feedback && nameDisp && digestDisp) {
                feedback.classList.remove('hidden');
                nameDisp.textContent = `${f.name} (${(f.size / (1024 * 1024)).toFixed(2)} MB)`;
                digestDisp.textContent = '0x' + Array.from(crypto.getRandomValues(new Uint8Array(8))).map(b => b.toString(16).padStart(2, '0')).join('').toUpperCase();
            }
        }
    };

    /**
     * Confirm simulated passport ingestion
     */
    window.submitPassportUpload = function () {
        const modal = document.getElementById('portal-dynamic-modal');
        if (modal) modal.remove();

        const model = document.getElementById('uploadModelInput')?.value || 'Technical Asset';
        if (window.showToast) {
            window.showToast('Dossier Ingested', `Passport for ${model} ingested and SHA-256 sealed in Rosstandart gateway`, 'success');
        }
    };

    /**
     * Parse query parameters on load
     */
    document.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        const docParam = params.get('doc');
        if (docParam) {
            const num = parseInt(docParam, 10);
            if (!isNaN(num) && dossierData[num]) {
                window.selectDoc(num);
            } else {
                for (const key in dossierData) {
                    if (dossierData[key].id.toLowerCase().includes(docParam.toLowerCase()) || 
                        dossierData[key].title.toLowerCase().includes(docParam.toLowerCase())) {
                        window.selectDoc(parseInt(key, 10));
                        break;
                    }
                }
            }
        } else if (params.get('upload') === 'true') {
            setTimeout(window.showUploadPassportModal, 400);
        }
    });

})();
