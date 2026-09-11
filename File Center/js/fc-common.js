/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 09: File Center / Document Hub (files.vostokpribor.local)
 * Common Utilities, Navigation, Command Palette & Station Clock
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        initStationClock();
        initCommandPalette();
        initToastSystem();
        initNavigationHighlight();
    });

    /**
     * Almaty Central Station Live Clock (UTC+6)
     */
    function initStationClock() {
        const clockElem = document.querySelector('.station-live-clock');
        if (!clockElem) return;

        function updateTime() {
            const now = new Date();
            // Convert to UTC+6 Almaty time
            const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
            const almatyTime = new Date(utc + (3600000 * 6));

            const hours = String(almatyTime.getHours()).padStart(2, '0');
            const minutes = String(almatyTime.getMinutes()).padStart(2, '0');
            const seconds = String(almatyTime.getSeconds()).padStart(2, '0');
            clockElem.textContent = `${hours}:${minutes}:${seconds} UTC+6`;
        }

        updateTime();
        setInterval(updateTime, 1000);
    }

    /**
     * Universal Toast Notification System
     */
    function initToastSystem() {
        let toastContainer = document.querySelector('.vk-toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'vk-toast-container';
            document.body.appendChild(toastContainer);
        }

        window.showToast = function (title, message, type = 'info', iconName = 'info') {
            const toast = document.createElement('div');
            toast.className = 'vk-toast';

            let borderCol = 'var(--vk-sys-accent)';
            let iconCol = 'var(--vk-sys-accent)';
            if (type === 'success') {
                borderCol = 'var(--vk-secondary)';
                iconCol = 'var(--vk-secondary)';
                if (iconName === 'info') iconName = 'check_circle';
            } else if (type === 'warning') {
                borderCol = 'var(--vk-accent-cta)';
                iconCol = 'var(--vk-accent-cta)';
                if (iconName === 'info') iconName = 'warning';
            } else if (type === 'error') {
                borderCol = 'var(--vk-alert)';
                iconCol = 'var(--vk-alert)';
                if (iconName === 'info') iconName = 'error';
            }

            toast.style.borderLeftColor = borderCol;
            toast.innerHTML = `
                <span class="material-symbols-outlined text-[20px]" style="color: ${iconCol}; flex-shrink: 0;">${iconName}</span>
                <div style="flex: 1;">
                    <div style="font-size: 12px; font-weight: 700; color: var(--vk-neutral-900); text-transform: uppercase; letter-spacing: 0.03em;">${title}</div>
                    <div style="font-size: 12px; color: var(--vk-neutral-600); margin-top: 2px;">${message}</div>
                </div>
                <button type="button" style="background: none; border: none; cursor: pointer; color: var(--vk-neutral-600); padding: 0 4px;" aria-label="Close">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            `;

            toast.querySelector('button').addEventListener('click', () => {
                toast.remove();
            });

            toastContainer.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(20px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 4500);
        };

        window.copyToClipboard = function (text, label = 'Content') {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    window.showToast('COPIED TO CLIPBOARD', `${label} copied successfully.`, 'info', 'content_copy');
                }).catch(() => {
                    fallbackCopy(text, label);
                });
            } else {
                fallbackCopy(text, label);
            }
        };

        function fallbackCopy(text, label) {
            const temp = document.createElement('textarea');
            temp.value = text;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);
            window.showToast('COPIED TO CLIPBOARD', `${label} copied successfully.`, 'info', 'content_copy');
        }
    }

    /**
     * Universal Command Palette (Ctrl + K)
     */
    function initCommandPalette() {
        const modal = document.getElementById('cmd-palette-modal');
        const input = document.getElementById('cmd-palette-input');
        const results = document.getElementById('cmd-palette-results');
        const triggerBtns = document.querySelectorAll('.search-trigger-btn');

        if (!modal || !input) return;

        function openPalette() {
            modal.style.display = 'flex';
            input.value = '';
            input.focus();
        }

        function closePalette() {
            modal.style.display = 'none';
        }

        triggerBtns.forEach(btn => btn.addEventListener('click', openPalette));

        window.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                if (modal.style.display === 'flex') {
                    closePalette();
                } else {
                    openPalette();
                }
            } else if (e.key === 'Escape' && modal.style.display === 'flex') {
                closePalette();
            }
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) closePalette();
        });

        if (input && results) {
            input.addEventListener('input', () => {
                const query = input.value.toLowerCase().trim();
                const items = results.querySelectorAll('.cmd-palette-item');
                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(query)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
    }

    /**
     * Sidebar Navigation Active Link Highlight
     */
    function initNavigationHighlight() {
        const currentPath = window.location.pathname.split('/').pop() || 'index.html';
        document.querySelectorAll('.vk-nav-item').forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPath || (currentPath === '' && href === 'index.html')) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }
})();
