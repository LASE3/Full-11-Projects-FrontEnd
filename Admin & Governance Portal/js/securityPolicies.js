/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Security Policies & Enforcement Engine Module
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        // 1. Policy Rule Live Simulation Trigger
        const simBtn = Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('Trigger Live Node Simulation'));
        if (simBtn) {
            simBtn.addEventListener('click', function () {
                const orig = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin text-secondary-fixed">sync</span><span>Transmitting Policy Interlock Frame...</span>';

                setTimeout(() => {
                    this.innerHTML = orig;
                    this.disabled = false;
                    window.showToast(
                        'SIMULATION NOMINAL',
                        'DOC-2026-001 Policy Interlocks verified across 11 target environments. Zero boundary violations detected.',
                        'success',
                        'verified'
                    );
                }, 1200);
            });
        }

        // 2. Cryptographic Proof Verification
        const verifyBtn = Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('Verify Cryptographic Proof'));
        if (verifyBtn) {
            verifyBtn.addEventListener('click', function () {
                const orig = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin text-on-secondary-fixed">sync</span><span>Validating SHA-256 Chain...</span>';

                setTimeout(() => {
                    this.innerHTML = '<span class="material-symbols-outlined text-[18px]">done_all</span><span>Signature Verified</span>';
                    this.classList.remove('bg-secondary-fixed');
                    this.classList.add('bg-secondary', 'text-white');

                    setTimeout(() => {
                        this.innerHTML = orig;
                        this.classList.add('bg-secondary-fixed');
                        this.classList.remove('bg-secondary', 'text-white');
                        this.disabled = false;
                    }, 3000);

                    window.showToast(
                        'CRYPTOGRAPHIC SEAL VALID',
                        'Policy ledger hash matched against Almaty Secure Enclave HSM root [Timur Akhmetov EMP-1005 Signed].',
                        'success',
                        'shield'
                    );
                }, 900);
            });
        }

        // 3. Filter Policy Rules on Search
        const searchInput = document.querySelector('input[placeholder*="policy"], input[placeholder*="Search"]');
        const rows = document.querySelectorAll('tbody tr');
        if (searchInput && rows.length > 0) {
            searchInput.addEventListener('input', (e) => {
                const q = e.target.value.toLowerCase().trim();
                rows.forEach(r => {
                    const text = r.innerText.toLowerCase();
                    r.style.display = q === '' || text.includes(q) ? '' : 'none';
                });
            });
        }
    });
})();
