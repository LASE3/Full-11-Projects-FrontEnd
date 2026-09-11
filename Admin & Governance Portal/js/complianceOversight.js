/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Compliance Oversight & Statutory Audit Register Module
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        // 1. Re-verify SHA-256 Merkle Proofs Button
        const reverifyBtn = document.getElementById('reverifyBtn');
        if (reverifyBtn) {
            reverifyBtn.addEventListener('click', function () {
                const orig = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin text-secondary-fixed">sync</span><span>Validating SHA-256 Merkle Proofs...</span>';

                setTimeout(() => {
                    this.innerHTML = '<span class="material-symbols-outlined text-[16px] text-secondary-fixed">done_all</span><span>29 Frameworks Attested Nominal</span>';
                    this.classList.remove('bg-primary');
                    this.classList.add('bg-secondary');

                    setTimeout(() => {
                        this.innerHTML = orig;
                        this.classList.add('bg-primary');
                        this.classList.remove('bg-secondary');
                        this.disabled = false;
                    }, 2500);

                    window.showToast(
                        'STATUTORY PROOFS ATTESTED',
                        'Merkle consensus validated against ST RK & IEC 62443 regulatory baselines. All 29 industrial security frameworks verified.',
                        'success',
                        'verified'
                    );
                }, 1200);
            });
        }

        // 2. Export Compliance Dossier Button
        const exportBtn = Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('Export') || b.textContent.includes('Dossier'));
        if (exportBtn && !exportBtn.id) {
            exportBtn.addEventListener('click', () => {
                window.showToast(
                    'COMPLIANCE DOSSIER PREPARED',
                    'Generated cryptographic audit package for State Inspectorate & Supervisory Board review.',
                    'info',
                    'description'
                );
            });
        }
    });
})();
