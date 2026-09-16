/**
 * ============================================================
 *  VOSTOKPRIBOR ENTERPRISE — DYNAMIC LOGIN THEME ENGINE v2.0
 *  Est. 1968 · Almaty, Kazakhstan
 *  System 06: HR & Human Capital Operations
 * ============================================================
 */

(function () {

  const SYSTEM_PRESETS = {
    'SYS-06':   { accent: '#7B2CBF', hover: '#5A189A', glow: 'rgba(123,44,191,0.45)', bg1: '#0D0914', bg2: '#07050B', label: '06 · HR OPERATIONS' },
    'HR':       { accent: '#7B2CBF', hover: '#5A189A', glow: 'rgba(123,44,191,0.45)', bg1: '#0D0914', bg2: '#07050B', label: '06 · HR OPERATIONS' },
    'SYS-01':   { accent: '#1B3A5C', hover: '#0F2438', glow: 'rgba(27,58,92,0.45)',   bg1: '#060D14', bg2: '#030810', label: '01 · CORPORATE' },
    'SYS-02':   { accent: '#0E7C86', hover: '#095d65', glow: 'rgba(14,124,134,0.45)', bg1: '#04101A', bg2: '#020B0C', label: '02 · B2B SHOP'   },
    'SYS-09':   { accent: '#10B981', hover: '#059669', glow: 'rgba(16,185,129,0.45)', bg1: '#0A1118', bg2: '#050B10', label: '03 · CUSTOMER'   },
    'SYS-03':   { accent: '#5C7290', hover: '#47596f', glow: 'rgba(92,114,144,0.45)', bg1: '#0B0F13', bg2: '#06090C', label: '04 · INTRANET'   },
    'SYS-05':   { accent: '#E8A33D', hover: '#D4902B', glow: 'rgba(232,163,61,0.45)', bg1: '#0C1017', bg2: '#070A0F', label: '05 · CRM'        },
    'SYS-04':   { accent: '#2E6E4E', hover: '#22533B', glow: 'rgba(46,110,78,0.45)', bg1: '#060F09', bg2: '#030805', label: '07 · FINANCE'    },
    'SYS-08':   { accent: '#C97A3D', hover: '#A65E2A', glow: 'rgba(201,122,61,0.45)', bg1: '#160C05', bg2: '#0D0703', label: '08 · HELPDESK'   },
    'SYS-07':   { accent: '#5A6470', hover: '#434D57', glow: 'rgba(90,100,112,0.45)', bg1: '#0A0C0E', bg2: '#050607', label: '09 · FILE CENTER' },
    'SYS-10':   { accent: '#1E8FA6', hover: '#156B7D', glow: 'rgba(30,143,166,0.45)', bg1: '#041014', bg2: '#02080D', label: '10 · DEVELOPER'  },
  };

  const SENTIMENT = {
    error:   { accent: '#B23A32', hover: '#8F2C25', glow: 'rgba(178,58,50,0.5)',  bg1: '#1C0808', bg2: '#0F0404' },
    success: { accent: '#1E7E4E', hover: '#16623D', glow: 'rgba(30,126,78,0.45)', bg1: '#051209', bg2: '#030905' },
    warning: { accent: '#E8A33D', hover: '#D9822B', glow: 'rgba(232,163,61,0.45)',bg1: '#1A1104', bg2: '#0D0A02' },
  };

  let _systemPreset = SYSTEM_PRESETS['SYS-06'];
  let _originalHeading = 'HR Operations Login';

  function applyTheme(preset) {
    const r = document.documentElement;
    r.style.setProperty('--bg-color', preset.bg2);
    r.style.setProperty('--button-color', preset.accent);
    r.style.setProperty('--text-color', '#F8FAFC');

    r.style.setProperty('--auth-accent', preset.accent);
    r.style.setProperty('--auth-accent-hover', preset.hover);
    r.style.setProperty('--auth-accent-glow', preset.glow);
    r.style.setProperty('--auth-accent-subtle', preset.glow.replace(/[\d.]+\)$/, '0.14)'));
    r.style.setProperty('--auth-accent-border', preset.glow.replace(/[\d.]+\)$/, '0.35)'));

    document.body.style.background = [
      'radial-gradient(ellipse 60% 40% at 50% 0%, ' + preset.glow.replace(/[\d.]+\)$/, '0.18)') + ' 0%, transparent 70%)',
      'radial-gradient(circle at 85% 85%, ' + preset.glow.replace(/[\d.]+\)$/, '0.06)') + ' 0%, transparent 40%)',
      'linear-gradient(180deg, ' + preset.bg1 + ' 0%, ' + preset.bg2 + ' 100%)'
    ].join(', ');
  }

  function updateLoginTheme(text) {
    if (!text) return;
    const t = text.toLowerCase();

    if (/error|failed|invalid|denied|blocked|unauthorized|lockout/.test(t)) {
      applyTheme(SENTIMENT.error);
    } else if (/success|granted|approved|verified|authorized/.test(t)) {
      applyTheme(SENTIMENT.success);
    } else if (/warning|security|advisory|mfa|2fa|challenge|clearance/.test(t)) {
      applyTheme(SENTIMENT.warning);
    } else if (/welcome|hello|sign in|portal|login|ready|authenticated/.test(t)) {
      if (_systemPreset) applyTheme(_systemPreset);
    }
  }

  function setLoginThemePreset(preset) {
    const heading = document.getElementById('login-heading');
    const alertEl = document.getElementById('alert-message');
    const authAlert = document.getElementById('auth-alert');
    const simStatus = document.getElementById('sim-status-text');

    switch (preset) {
      case 'system':
        if (_systemPreset) {
          applyTheme(_systemPreset);
          if (heading) heading.textContent = _originalHeading;
          if (alertEl) alertEl.textContent = 'System default theme restored.';
          if (authAlert) authAlert.className = 'auth-alert';
          if (simStatus) simStatus.textContent = _systemPreset.label;
        }
        break;
      case 'blue':
        applyTheme({ accent: '#7B2CBF', hover: '#5A189A', glow: 'rgba(123,44,191,0.45)', bg1: '#0D0914', bg2: '#07050B' });
        if (heading) heading.textContent = 'HR Operations Login';
        if (alertEl) alertEl.textContent = 'Ready. Please authenticate with Level 4 clearance credentials.';
        if (authAlert) authAlert.className = 'auth-alert';
        if (simStatus) simStatus.textContent = 'Plum (HR Default)';
        break;
      case 'red':
        applyTheme(SENTIMENT.error);
        if (heading) heading.textContent = 'Authentication Error';
        if (alertEl) alertEl.textContent = 'Error: Security clearance credentials rejected.';
        if (authAlert) authAlert.className = 'auth-alert active-error';
        if (simStatus) simStatus.textContent = 'Red (Error/Failed)';
        break;
      case 'green':
        applyTheme(SENTIMENT.success);
        if (heading) heading.textContent = 'Access Granted';
        if (alertEl) alertEl.textContent = 'Success: Level 4 Personnel Clearance ratified.';
        if (authAlert) authAlert.className = 'auth-alert active-success';
        if (simStatus) simStatus.textContent = 'Green (Success)';
        break;
      case 'yellow':
        applyTheme(SENTIMENT.warning);
        if (heading) heading.textContent = 'Security Clearance Audit';
        if (alertEl) alertEl.textContent = 'Warning: GOST 1G verification challenge active.';
        if (authAlert) authAlert.className = 'auth-alert active-error';
        if (simStatus) simStatus.textContent = 'Yellow (Security)';
        break;
    }
  }

  function detectSystem() {
    const tagEl = document.querySelector('.auth-system-tag');
    if (!tagEl) return SYSTEM_PRESETS['SYS-06'];
    const tagText = tagEl.textContent.toUpperCase();

    for (const key of Object.keys(SYSTEM_PRESETS)) {
      if (tagText.includes(key)) {
        return SYSTEM_PRESETS[key];
      }
    }
    return SYSTEM_PRESETS['SYS-06'];
  }

  function injectSimBar() {
    if (document.querySelector('.vp-sim-bar')) return;
    const mainEl = document.querySelector('.auth-main') || document.body;

    const bar = document.createElement('div');
    bar.className = 'vp-sim-bar';
    bar.innerHTML = [
      '<span class="vp-sim-label">Theme Preview:</span>',
      '<span class="vp-sim-status" id="sim-status-text">HR Default</span>',
      '<button type="button" class="vp-sim-btn vp-sys"   onclick="setLoginThemePreset(\'system\')">⟳ System</button>',
      '<button type="button" class="vp-sim-btn vp-blue"  onclick="setLoginThemePreset(\'blue\')">● Normal</button>',
      '<button type="button" class="vp-sim-btn vp-red"   onclick="setLoginThemePreset(\'red\')">● Error</button>',
      '<button type="button" class="vp-sim-btn vp-green" onclick="setLoginThemePreset(\'green\')">● Success</button>',
      '<button type="button" class="vp-sim-btn vp-amber" onclick="setLoginThemePreset(\'yellow\')">● Security</button>',
    ].join('');

    if (!document.getElementById('vp-sim-styles')) {
      const style = document.createElement('style');
      style.id = 'vp-sim-styles';
      style.textContent = `
        .vp-sim-bar {
          display: flex;
          align-items: center;
          flex-wrap: wrap;
          gap: 6px;
          margin: 0.5rem auto 0;
          padding: 8px 12px;
          background: rgba(15,36,56,0.75);
          border: 1px solid rgba(255,255,255,0.12);
          border-radius: 8px;
          max-width: 440px;
          width: 100%;
          font-family: var(--font-mono, monospace);
          font-size: 0.68rem;
          color: #94A3B8;
          backdrop-filter: blur(10px);
          -webkit-backdrop-filter: blur(10px);
          box-sizing: border-box;
          z-index: 10;
        }
        .auth-card-wide ~ .vp-sim-bar {
          max-width: 680px;
        }
        .vp-sim-label {
          font-weight: 600;
          color: #DCE1E6;
          white-space: nowrap;
          margin-right: 2px;
        }
        .vp-sim-status {
          font-weight: 700;
          color: var(--auth-accent, #7B2CBF);
          transition: color 0.3s ease;
          flex: 1;
          min-width: 80px;
        }
        .vp-sim-btn {
          background: rgba(255,255,255,0.07);
          border: 1px solid rgba(255,255,255,0.14);
          border-radius: 4px;
          color: #E2E8F0;
          padding: 3px 8px;
          font-size: 0.65rem;
          font-family: var(--font-mono, monospace);
          cursor: pointer;
          transition: all 0.2s ease;
          white-space: nowrap;
        }
        .vp-sim-btn:hover { background: rgba(255,255,255,0.18); }
        .vp-sys:hover   { border-color: #64748B; color: #CBD5E1; }
        .vp-blue:hover  { border-color: #7B2CBF; color: #D8B4FE; }
        .vp-red:hover   { border-color: #B23A32; color: #FCA5A5; }
        .vp-green:hover { border-color: #1E7E4E; color: #6EE7B7; }
        .vp-amber:hover { border-color: #E8A33D; color: #FCD34D; }
      `;
      document.head.appendChild(style);
    }

    const card = mainEl.querySelector('.auth-card') || mainEl.querySelector('section');
    if (card && card.parentNode) {
      card.parentNode.insertBefore(bar, card.nextSibling);
    } else {
      mainEl.appendChild(bar);
    }
  }

  function enforceCardCentering() {
    const style = document.createElement('style');
    style.id = 'vp-centering';
    style.textContent = `
      html, body {
        height: 100%;
        min-height: 100vh;
        margin: 0;
        padding: 0;
      }
      body {
        display: flex !important;
        flex-direction: column !important;
      }
      .auth-main {
        flex: 1 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 1.5rem !important;
        flex-direction: column !important;
        gap: 1rem !important;
      }
      .auth-card {
        width: 100% !important;
        max-width: 440px;
        transition: all 0.3s ease !important;
      }
      .auth-card.auth-card-wide {
        max-width: 680px !important;
      }
    `;
    document.head.appendChild(style);
  }

  window.updateLoginTheme = updateLoginTheme;
  window.setLoginThemePreset = setLoginThemePreset;

  document.addEventListener('DOMContentLoaded', () => {
    enforceCardCentering();
    const detected = detectSystem();
    if (detected) {
      _systemPreset = detected;
      applyTheme(detected);
    }
    const h = document.getElementById('login-heading');
    if (h) _originalHeading = h.textContent;

    injectSimBar();
  });

})();
