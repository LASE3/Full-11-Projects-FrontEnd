/**
 * VOSTOKPRIBOR Customer Portal (SYS03)
 * Unified Tailwind CSS Configuration
 */
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        primary: "#000e1d",
        secondary: "#436084",
        "primary-container": "#0f2438",
        "surface-container-highest": "#e0e3e5",
        "inverse-primary": "#b4c8e3",
        "secondary-fixed": "#d2e4ff",
        "on-secondary-container": "#3f5b7f",
        "surface-bright": "#f8f9fb",
        "primary-fixed-dim": "#b4c8e3",
        "on-secondary-fixed-variant": "#2b486b",
        "on-tertiary-fixed-variant": "#643f00",
        "surface-container-low": "#f2f4f6",
        "on-error": "#ffffff",
        "tertiary-fixed": "#ffddb5",
        "tertiary-container": "#331e00",
        "inverse-on-surface": "#eff1f3",
        "on-tertiary-fixed": "#2a1800",
        "on-secondary-fixed": "#001c38",
        "on-background": "#191c1e",
        "secondary-container": "#b6d4fe",
        "inverse-surface": "#2d3133",
        tertiary: "#150a00",
        "error-container": "#ffdad6",
        "on-primary-fixed": "#071d30",
        "secondary-fixed-dim": "#abc9f2",
        error: "#ba1a1a",
        "primary-fixed": "#d0e4ff",
        "surface-container": "#eceef0",
        "on-primary-fixed-variant": "#35485e",
        "on-error-container": "#93000a",
        "surface-variant": "#e0e3e5",
        "on-primary": "#ffffff",
        "on-tertiary-container": "#bb7d16",
        "on-secondary": "#ffffff",
        background: "#f8f9fb",
        "surface-container-lowest": "#ffffff",
        "on-surface-variant": "#43474c",
        surface: "#f8f9fb",
        "surface-tint": "#4d6077",
        "tertiary-fixed-dim": "#ffb956",
        outline: "#74777d",
        "on-primary-container": "#788ca4",
        "surface-dim": "#d8dadc",
        "outline-variant": "#c4c6cd",
        "surface-container-high": "#e6e8ea",
        "on-tertiary": "#ffffff",
        "on-surface": "#191c1e"
      },
      borderRadius: {
        DEFAULT: "0.125rem",
        lg: "0.25rem",
        xl: "0.5rem",
        full: "0.75rem"
      },
      spacing: {
        "unit-md": "0.75rem",
        "grid-gutter": "1rem",
        "unit-lg": "1.5rem",
        "unit-xl": "2rem",
        "unit-sm": "0.5rem",
        "unit-2xs": "0.125rem",
        "grid-margin": "1.5rem",
        "unit-2xl": "3rem",
        "unit-base": "1rem",
        "unit-xs": "0.25rem"
      },
      fontFamily: {
        "body-md": ["IBM Plex Sans"],
        "technical-tag": ["JetBrains Mono"],
        "display-lg": ["IBM Plex Sans"],
        "headline-lg": ["IBM Plex Sans"],
        "body-sm": ["IBM Plex Sans"],
        "body-lg": ["IBM Plex Sans"],
        "headline-md": ["IBM Plex Sans"],
        "headline-sm": ["IBM Plex Sans"],
        "data-mono-md": ["JetBrains Mono"],
        "data-mono-lg": ["JetBrains Mono"],
        "label-caps": ["IBM Plex Sans"],
        "display-lg-mobile": ["IBM Plex Sans"]
      },
      fontSize: {
        "body-md": ["13px", { lineHeight: "18px", letterSpacing: "0em", fontWeight: "400" }],
        "technical-tag": ["10px", { lineHeight: "12px", letterSpacing: "0.02em", fontWeight: "500" }],
        "display-lg": ["32px", { lineHeight: "40px", letterSpacing: "-0.02em", fontWeight: "700" }],
        "headline-lg": ["24px", { lineHeight: "32px", letterSpacing: "-0.015em", fontWeight: "600" }],
        "body-sm": ["12px", { lineHeight: "16px", letterSpacing: "0.005em", fontWeight: "400" }],
        "body-lg": ["15px", { lineHeight: "22px", letterSpacing: "0em", fontWeight: "400" }],
        "headline-md": ["20px", { lineHeight: "28px", letterSpacing: "-0.01em", fontWeight: "600" }],
        "headline-sm": ["16px", { lineHeight: "24px", letterSpacing: "-0.005em", fontWeight: "600" }],
        "data-mono-md": ["12px", { lineHeight: "16px", letterSpacing: "-0.01em", fontWeight: "500" }],
        "data-mono-lg": ["14px", { lineHeight: "20px", letterSpacing: "-0.02em", fontWeight: "600" }],
        "label-caps": ["11px", { lineHeight: "14px", letterSpacing: "0.06em", fontWeight: "700" }],
        "display-lg-mobile": ["24px", { lineHeight: "32px", letterSpacing: "-0.01em", fontWeight: "700" }]
      }
    }
  }
};
