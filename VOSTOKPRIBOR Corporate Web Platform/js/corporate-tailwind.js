/**
 * VOSTOKPRIBOR Corporate Web Platform Tailwind Configuration
 */
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        "brand-primary": "#1B3A5C",
        "brand-primary-dark": "#0F2438",
        "brand-secondary": "#0E7C86",
        "brand-accent": "#E8A33D",
        "neutral-900": "#101418",
        "neutral-800": "#1E252D",
        "neutral-700": "#323B44",
        "neutral-600": "#4A5560",
        "neutral-300": "#BDC6CF",
        "neutral-200": "#DCE1E6",
        "neutral-100": "#EDF1F4",
        "neutral-50": "#F5F7F9",
        "class-public": "#8A94A0",
        "class-internal": "#3E7CB1",
        "class-confidential": "#D9822B",
        "class-restricted": "#B23A32"
      },
      fontFamily: {
        sans: ["IBM Plex Sans", "sans-serif"],
        mono: ["JetBrains Mono", "monospace"]
      }
    }
  }
};
