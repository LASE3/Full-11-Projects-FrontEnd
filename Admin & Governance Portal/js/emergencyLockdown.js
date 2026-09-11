/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Emergency Lockdown & DEFCON-1 Quarantine Controller
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        // 1. Dynamic Syslog Console Stream
        const syslogConsole = document.getElementById('syslogConsole');
        const sampleLogs = [
            "[15:44:22.310 UTC+6] QUARANTINE: SYS-04 Ekibastuz buffer verification pass (0 bytes outbound drop verified)",
            "[15:44:26.104 UTC+6] SEC-AUDIT: Ingestion pipeline throughput normalized at 1.4 kEV/s into write-once store",
            "[15:44:29.890 UTC+6] ALMATY-NET: Faradaic perimeter check reported impedance normal",
            "[15:44:33.421 UTC+6] KZ-CERT-DISPATCH: Autonomous handshake acknowledged by National Sec-Ops Centre (Nur-Sultan relay)",
            "[15:44:37.002 UTC+6] WORM-CHECK: Hash continuity verified across all 10 local facility partitions",
            "[15:44:41.150 UTC+6] JURISDICTION: Merkle tree anchor verified by Chief Governance Officer (EMP-1005)",
            "[15:44:45.891 UTC+6] ISOLATION: Galvanic isolation relay confirmed active on SYS-01 to SYS-03 links"
        ];

        let logIdx = 0;
        setInterval(() => {
            if (syslogConsole) {
                const el = document.createElement('div');
                el.className = 'text-on-primary-container font-mono text-[11px] py-0.5';
                el.textContent = sampleLogs[logIdx % sampleLogs.length];
                syslogConsole.appendChild(el);
                syslogConsole.scrollTop = syslogConsole.scrollHeight;
                logIdx++;
            }
        }, 4000);

        // 2. Modal Handlers
        const modal = document.getElementById('modalOverlay');
        const modalClose = document.getElementById('modalClose');
        const modalBtnCancel = document.getElementById('modalBtnCancel');
        const modalBtnConfirm = document.getElementById('modalBtnConfirm');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');

        const showModal = (title, body) => {
            if (modalTitle) modalTitle.textContent = title;
            if (modalBody) modalBody.textContent = body;
            if (modal) modal.classList.remove('hidden');
        };

        const hideModal = () => {
            if (modal) modal.classList.add('hidden');
        };

        if (modalClose) modalClose.addEventListener('click', hideModal);
        if (modalBtnCancel) modalBtnCancel.addEventListener('click', hideModal);
        if (modalBtnConfirm) {
            modalBtnConfirm.addEventListener('click', () => {
                hideModal();
                window.showToast('COMMAND COMMITTED', 'Cryptographic execution dispatched across secure chassis bus.', 'success', 'check_circle');
            });
        }

        // 3. Button Action Bindings
        const btnAbort = document.getElementById('btnAbortLockdown');
        if (btnAbort) {
            btnAbort.addEventListener('click', () => {
                showModal(
                    'Abort DEFCON-1 Lockdown?',
                    'Aborting requires secondary officer physical hardware key presence (EMP-1018 Leonid Volkov). Initiating safe challenge countdown...'
                );
            });
        }

        const btnExport = document.getElementById('btnExportDossier');
        if (btnExport) {
            btnExport.addEventListener('click', () => {
                showModal(
                    'Generate Cryptographic Dossier',
                    'Assembling ECDSA P-384 signed package of all telemetry logs, buffer snapshots, and anomaly hashes for DOC-2026-LOCK-01.'
                );
            });
        }

        const btnDispatch = document.getElementById('btnDispatchAlert');
        if (btnDispatch) {
            btnDispatch.addEventListener('click', () => {
                showModal(
                    'Broadcast Sec-Ops Alert',
                    'Sovereign emergency dispatch packet SHA-256 #9A2F...4B8C transmitted to KZ-CERT emergency relay.'
                );
            });
        }

        const btnIsolateAll = document.getElementById('btnIsolateAll');
        if (btnIsolateAll) {
            btnIsolateAll.addEventListener('click', () => {
                showModal(
                    'Air-Gap All Nodes',
                    'All 10 industrial facility bridges will be subjected to physical galvano-magnetic actuator severing.'
                );
            });
        }

        // 4. Secondary PIN Verification
        const btnPin = document.getElementById('btnVerifySecondaryPin');
        const pinInput = document.getElementById('secondaryPinInput');
        const pinFeedback = document.getElementById('pinFeedback');

        if (btnPin && pinInput && pinFeedback) {
            btnPin.addEventListener('click', () => {
                if (pinInput.value.length >= 4) {
                    pinFeedback.textContent = "KEY VERIFIED: EMP-1018 Leonid Volkov authenticated via FIPS-140-3 token.";
                    pinFeedback.className = "font-telemetry-micro text-[10px] text-secondary font-bold mt-1 block";
                    pinInput.disabled = true;
                    btnPin.disabled = true;
                    btnPin.textContent = "Accepted";
                    window.showToast('AUTHENTICATION ACCEPTED', 'Leonid Volkov (EMP-1018) hardware challenge validated.', 'success', 'key');
                } else {
                    pinFeedback.textContent = "ERROR: Minimum 4 digits required for hardware cryptographic token challenge.";
                    pinFeedback.className = "font-telemetry-micro text-[10px] text-error font-bold mt-1 block";
                    window.showToast('CHALLENGE FAILED', 'Invalid FIPS challenge length.', 'error', 'key_off');
                }
            });
        }
    });
})();
