// https://nuxt.com/docs/api/configuration/nuxt-config
import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
  srcDir: "app",
  compatibilityDate: "2026-06-19",
  devtools: { enabled: true },
  ssr: false,
  css: ["~/assets/css/main.css"],
  modules: [
    "@nuxt/ui",
    "@nuxt/icon",
    "@nuxt/image",
    "@nuxt/fonts",
    "@vueuse/nuxt",
    "vue3-carousel-nuxt",
    "@pinia/nuxt",
  ],
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || "http://localhost:8000/api",
      siteUrl: process.env.NUXT_PUBLIC_SITE_URL || "https://bbystargo.com",
    },
  },
  ui: {
    colorMode: false,
  },
  vite: {
    plugins: [tailwindcss()],
  },
  app: {
    head: {
      htmlAttrs: { lang: "en" },
      title: "PCG — Phangkawira Consulting Group",
      titleTemplate: "%s | PCG",
      link: [{ rel: "icon", type: "image/x-icon", href: "/favicon.ico" }],
      meta: [
        { name: "viewport", content: "width=device-width, initial-scale=1" },
        { name: "theme-color", content: "#083653" },
        {
          name: "description",
          content:
            "PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals.",
        },
        {
          name: "keywords",
          content:
            "PCG, Phangkawira Consulting Group, tax consulting, accounting, bookkeeping, financial advisory, Tax Box, Tangerang, Indonesia",
        },
        { property: "og:title", content: "PCG — Phangkawira Consulting Group" },
        {
          property: "og:description",
          content:
            "PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals.",
        },
        { property: "og:site_name", content: "PCG" },
        { property: "og:type", content: "website" },
        { property: "og:image", content: "https://bbystargo.com/pcg_logo.jpeg" },
        { name: "twitter:card", content: "summary_large_image" },
        { name: "twitter:title", content: "PCG — Phangkawira Consulting Group" },
        {
          name: "twitter:description",
          content:
            "PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals.",
        },
        { name: "twitter:image", content: "https://bbystargo.com/pcg_logo.jpeg" },
      ],
    },
  },
});

