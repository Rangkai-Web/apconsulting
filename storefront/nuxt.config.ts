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
        {
          name: "description",
          content:
            "PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals.",
        },
        { property: "og:title", content: "PCG — Phangkawira Consulting Group" },
        {
          property: "og:description",
          content:
            "PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals.",
        },
        { property: "og:site_name", content: "PCG" },
        { property: "og:type", content: "website" },
        {
          name: "twitter:description",
          content:
            "PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals.",
        },
      ],
    },
  },
});

