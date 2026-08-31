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
      titleTemplate: "%s | BbystarGO",
      link: [{ rel: "icon", type: "image/x-icon", href: "/favicon.ico" }],
      meta: [
        {
          name: "description",
          content:
            "BbystarGO — jasa pembelian barang dari Indonesia, Korea, China, Jepang, dan negara lain sejak 2023. Ready Stock & Pre Order, aman dan terpercaya.",
        },
        { property: "og:site_name", content: "BbystarGO" },
        { property: "og:type", content: "website" },
        { property: "og:locale", content: "id_ID" },
        { name: "twitter:card", content: "summary_large_image" },
      ],
    },
  },
});
