// https://nuxt.com/docs/api/configuration/nuxt-config
import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
  compatibilityDate: "2026-06-19",
  devtools: { enabled: true },
  ssr: false,
  css: ["./app/assets/css/main.css"],
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
      htmlAttrs: { lang: "id" },
      title: "BbystarGO — Jasa Pembelian Barang & Ready Stock",
      titleTemplate: "BbystarGO",
      link: [{ rel: "icon", type: "image/x-icon", href: "/favicon.ico" }],
      meta: [
        {
          name: "description",
          content:
            "BbystarGO — jasa pembelian barang dari Indonesia, Korea, China, Jepang, dan negara lain sejak 2023. Ready Stock & Pre Order, aman dan terpercaya.",
        },
        {
          property: "og:title",
          content: "BbystarGO — Jasa Pembelian Barang & Ready Stock",
        },
        {
          property: "og:description",
          content:
            "BbystarGO — jasa pembelian barang dari Indonesia, Korea, China, Jepang, dan negara lain sejak 2023. Ready Stock & Pre Order, aman dan terpercaya.",
        },
        {
          property: "og:image",
          content: "https://bbystargo.com/BbystarGO_1.jpg",
        },
        { property: "og:site_name", content: "BbystarGO" },
        { property: "og:type", content: "website" },
        { property: "og:locale", content: "id_ID" },
        { name: "twitter:card", content: "summary_large_image" },
        {
          name: "twitter:title",
          content: "BbystarGO — Jasa Pembelian Barang & Ready Stock",
        },
        {
          name: "twitter:description",
          content:
            "BbystarGO — jasa pembelian barang dari Indonesia, Korea, China, Jepang, dan negara lain sejak 2023. Ready Stock & Pre Order, aman dan terpercaya.",
        },
        {
          name: "twitter:image",
          content: "https://bbystargo.com/BbystarGO_1.jpg",
        },
      ],
    },
  },
});
