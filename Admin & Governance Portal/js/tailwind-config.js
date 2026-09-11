/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Unified Tailwind CSS Configuration
 */
tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                "surface-bright": "#f7f9ff",
                "on-error-container": "#93000a",
                "on-tertiary": "#ffffff",
                "secondary-fixed-dim": "#7cd4df",
                "on-tertiary-container": "#d79530",
                "on-primary": "#ffffff",
                "inverse-surface": "#2d3135",
                "error-container": "#ffdad6",
                "on-tertiary-fixed-variant": "#643f00",
                "primary-fixed": "#d2e4ff",
                "on-secondary-fixed-variant": "#004f56",
                "background": "#f7f9ff",
                "tertiary": "#341f00",
                "on-error": "#ffffff",
                "surface": "#f7f9ff",
                "surface-dim": "#d7dae0",
                "inverse-primary": "#abc9f2",
                "on-secondary-fixed": "#001f23",
                "primary": "#002444",
                "on-surface": "#181c20",
                "on-secondary": "#ffffff",
                "secondary-container": "#96eef9",
                "on-background": "#181c20",
                "surface-variant": "#e0e3e8",
                "primary-fixed-dim": "#abc9f2",
                "tertiary-fixed": "#ffddb5",
                "on-secondary-container": "#006d77",
                "tertiary-fixed-dim": "#ffb956",
                "surface-container-low": "#f1f4f9",
                "outline-variant": "#c3c6cf",
                "surface-container-lowest": "#ffffff",
                "secondary-fixed": "#98f0fb",
                "surface-tint": "#436084",
                "surface-container-high": "#e5e8ee",
                "surface-container-highest": "#e0e3e8",
                "primary-container": "#1b3a5c",
                "inverse-on-surface": "#eef1f7",
                "on-primary-container": "#87a4cc",
                "secondary": "#006972",
                "on-primary-fixed-variant": "#2b486b",
                "on-primary-fixed": "#001c38",
                "on-surface-variant": "#43474e",
                "outline": "#73777f",
                "tertiary-container": "#513200",
                "error": "#ba1a1a",
                "surface-container": "#ebeef4",
                "on-tertiary-fixed": "#2a1800"
            },
            borderRadius: {
                "DEFAULT": "0.25rem",
                "lg": "0.5rem",
                "xl": "0.75rem",
                "full": "9999px"
            },
            spacing: {
                "space-xs": "4px",
                "control-height-lg": "40px",
                "space-base": "16px",
                "space-xl": "32px",
                "gutter-dense": "8px",
                "space-lg": "24px",
                "space-sm": "8px",
                "grid-unit": "4px",
                "control-height-sm": "28px",
                "control-height-md": "32px",
                "gutter-desktop": "12px",
                "space-2xs": "2px",
                "space-md": "12px"
            },
            fontFamily: {
                "display-lg": ["IBM Plex Sans", "sans-serif"],
                "telemetry-data": ["JetBrains Mono", "monospace"],
                "telemetry-micro": ["JetBrains Mono", "monospace"],
                "headline-lg": ["IBM Plex Sans", "sans-serif"],
                "body-default": ["IBM Plex Sans", "sans-serif"],
                "security-stamp": ["JetBrains Mono", "monospace"],
                "label-uppercase": ["JetBrains Mono", "monospace"],
                "body-compact": ["IBM Plex Sans", "sans-serif"],
                "title-sm": ["IBM Plex Sans", "sans-serif"],
                "headline-md": ["IBM Plex Sans", "sans-serif"]
            },
            fontSize: {
                "display-lg": ["32px", { lineHeight: "38px", letterSpacing: "-0.02em", fontWeight: "700" }],
                "telemetry-data": ["13px", { lineHeight: "18px", letterSpacing: "-0.01em", fontWeight: "500" }],
                "telemetry-micro": ["11px", { lineHeight: "14px", letterSpacing: "0.02em", fontWeight: "400" }],
                "headline-lg": ["24px", { lineHeight: "30px", letterSpacing: "-0.015em", fontWeight: "600" }],
                "body-default": ["13px", { lineHeight: "18px", letterSpacing: "0em", fontWeight: "400" }],
                "security-stamp": ["11px", { lineHeight: "12px", letterSpacing: "0.12em", fontWeight: "800" }],
                "label-uppercase": ["10px", { lineHeight: "12px", letterSpacing: "0.08em", fontWeight: "700" }],
                "body-compact": ["12px", { lineHeight: "16px", letterSpacing: "0em", fontWeight: "400" }],
                "title-sm": ["15px", { lineHeight: "20px", letterSpacing: "0em", fontWeight: "600" }],
                "headline-md": ["18px", { lineHeight: "24px", letterSpacing: "-0.01em", fontWeight: "600" }]
            }
        }
    }
};
