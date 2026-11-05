// https://nuxt.com/docs/api/configuration/nuxt-config
import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
  srcDir: "app",
  compatibilityDate: "2026-06-19",
  devtools: { enabled: false },
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
  fonts: {
    provider: "google",
    defaults: {
      weights: [400, 600, 700],
      styles: ["normal", "italic"],
      subsets: ["latin"],
    },
  },
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || "https://admin.pcgmitra.co.id/api",
      siteUrl: process.env.NUXT_PUBLIC_SITE_URL || "https://pcgmitra.co.id",
    },
  },
  ui: {
    colorMode: false,
  },
  vite: {
    plugins: [tailwindcss()],
    build: {
      cssCodeSplit: true,
    },
  },
  image: {
    format: ["webp", "jpg", "png"],
    quality: 80,
  },
  app: {
    pageTransition: { name: "page", mode: "out-in" },
    head: {
      htmlAttrs: { lang: "id" },
      title: "PCG — Phangkawira Consulting Group",
      titleTemplate: "%s | PCG",
      link: [
        { rel: "icon", type: "image/x-icon", href: "/favicon.ico" },
        {
          rel: "preload",
          as: "image",
          href: "/images/homepage/HeroContent_Desktop.webp",
          media: "(min-width: 768px)",
          type: "image/webp",
        },
        {
          rel: "preload",
          as: "image",
          href: "/images/homepage/HeroContent_Mobile.webp",
          media: "(max-width: 767px)",
          type: "image/webp",
        },
      ],
      script: [
        {
          src: "https://www.googletagmanager.com/gtag/js?id=G-SH1GS7FRBG",
          async: true,
        },
        {
          innerHTML: `window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-SH1GS7FRBG');`,
        },
      ],
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
        { property: "og:image", content: "https://pcgmitra.co.id/pcg_logo.jpeg" },
        { name: "twitter:card", content: "summary_large_image" },
        { name: "twitter:title", content: "PCG — Phangkawira Consulting Group" },
        {
          name: "twitter:description",
          content:
            "PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals.",
        },
        { name: "twitter:image", content: "https://pcgmitra.co.id/pcg_logo.jpeg" },
      ],
    },
  },
});

