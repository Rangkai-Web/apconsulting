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
      midtransClientKey: process.env.NUXT_PUBLIC_MIDTRANS_CLIENT_KEY || "",
      midtransIsProduction:
        process.env.NUXT_PUBLIC_MIDTRANS_IS_PRODUCTION === "true" || false,
    },
  },
  ui: {
    colorMode: false,
  },
  vite: {
    plugins: [tailwindcss()],
  },
});
