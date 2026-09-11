/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Board Risk Register & Supervisory Board Oversight Module
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        // 1. Dual Signatory Co-Attestation (Sign Now Action)
        const signBtn = Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('SIGN NOW'));
        if (signBtn) {
            signBtn.addEventListener('click', function () {
                this.disabled = true;
                this.className = 'px-space-xs py-space-2xs bg-secondary text-white font-label-uppercase text-[10px] font-bold cursor-default';
                this.innerHTML = '<span class="material-symbols-outlined text-[12px] inline">done</span> SIGNED';

                const statusHeader = Array.from(document.querySelectorAll('span')).find(s => s.textContent.includes('1/2 PENDING'));
                if (statusHeader) {
                    statusHeader.textContent = '2/2 ATTESTED';
                    statusHeader.className = 'font-telemetry-micro text-[10px] text-secondary font-bold';
                }

                const sign2Card = this.closest('.bg-surface-container-lowest');
                if (sign2Card) {
                    const icon = sign2Card.querySelector('.material-symbols-outlined');
                    if (icon) {
                        icon.textContent = 'check_circle';
                        icon.className = 'material-symbols-outlined text-secondary text-[16px]';
                    }
                    const text = sign2Card.querySelector('.text-error');
                    if (text) {
                        text.textContent = 'Board Trustee • Signed 09:44 UTC+6';
                        text.className = 'text-[10px] text-on-surface-variant';
                    }
                }

                window.showToast(
                    'CO-ATTESTATION SIGNED',
                    'Timur Akhmetov (EMP-1005) cryptographic endorsement appended to DOC-2026-015 ledger.',
                    'success',
                    'draw'
                );
            });
        }

        // 2. Verify Cryptographic Seal Action
        const verifySealBtn = Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('Verify Cryptographic Seal'));
        if (verifySealBtn) {
            verifySealBtn.addEventListener('click', function () {
                const orig = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin">sync</span><span>Validating Hash Root...</span>';

                setTimeout(() => {
                    this.innerHTML = '<span class="material-symbols-outlined text-[14px]">done_all</span><span>Hash Seal Verified</span>';
                    this.classList.add('bg-primary', 'text-white');

                    setTimeout(() => {
                        this.innerHTML = orig;
                        this.disabled = false;
                    }, 3000);

                    window.showToast(
                        'TAMPER-EVIDENT SEAL VALID',
                        'SHA-256 Signature verified against Almaty Secure Enclave HSM root. DOC-2026-015 immutable continuity intact.',
                        'success',
                        'verified'
                    );
                }, 1000);
            });
        }

        // 3. Dispatch Patch Button
        const patchBtn = Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('Dispatch Industrial Patch'));
        if (patchBtn) {
            patchBtn.addEventListener('click', function () {
                const orig = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span><span>Deploying SCADA Hotfix...</span>';

                setTimeout(() => {
                    this.innerHTML = '<span class="material-symbols-outlined text-[16px]">check_circle</span><span>Patch v4.9.1 Deployed</span>';
                    this.className = 'h-control-height-md w-full bg-secondary text-white font-body-compact text-body-compact font-semibold flex items-center justify-center gap-space-xs cursor-default';

                    window.showToast(
                        'HOTFIX DISPATCHED',
                        'Automated fail-safe margin normalized across Ust-Kamenogorsk fiber trunk. Latency restored to <35ms.',
                        'success',
                        'precision_manufacturing'
                    );
                }, 1400);
            });
        }
    });
})();
