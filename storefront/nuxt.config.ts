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
      weights: [400, 500, 600, 700, 800],
      styles: ["normal", "italic"],
      subsets: ["latin"],
    },
  },
  runtimeConfig: {
    public: {
      apiBase:
        process.env.NUXT_PUBLIC_API_BASE ||
        (process.env.NODE_ENV === "production"
          ? "https://admin.apconsulting.id/api"
          : "http://localhost:8000/api"),
      siteUrl:
        process.env.NUXT_PUBLIC_SITE_URL ||
        (process.env.NODE_ENV === "production"
          ? "https://apconsulting.id"
          : "http://localhost:3000"),
      gaMeasurementId: process.env.NUXT_PUBLIC_GA_MEASUREMENT_ID || "G-SH1GS7FRBG",
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
      title: "AP Consulting — Kantor Konsultan Pajak Berbasis Hukum",
      titleTemplate: "%s | AP Consulting",
      link: [
        { rel: "icon", type: "image/x-icon", href: "/favicon.ico" },
        { rel: "icon", type: "image/png", sizes: "32x32", href: "/favicon/favicon-32x32.png" },
        { rel: "icon", type: "image/png", sizes: "16x16", href: "/favicon/favicon-16x16.png" },
        { rel: "apple-touch-icon", sizes: "180x180", href: "/favicon/apple-touch-icon.png" },
        {
          rel: "preload",
          as: "image",
          href: "/images/redesign/hero-bg.webp",
          type: "image/webp",
        },
        {
          rel: "preload",
          as: "image",
          href: "/images/redesign/logo.webp",
          type: "image/webp",
        },
      ],
      script: [
        {
          src: `https://www.googletagmanager.com/gtag/js?id=${process.env.NUXT_PUBLIC_GA_MEASUREMENT_ID || "G-SH1GS7FRBG"}`,
          async: true,
        },
        {
          innerHTML: `window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '${process.env.NUXT_PUBLIC_GA_MEASUREMENT_ID || "G-SH1GS7FRBG"}');`,
        },
      ],
      meta: [
        { name: "viewport", content: "width=device-width, initial-scale=1" },
        { name: "theme-color", content: "#083653" },
        {
          name: "description",
          content:
            "AP Consulting merupakan kantor konsultan pajak berbasis hukum yang berpengalaman, profesional dan berintegritas dalam membantu klien (badan usaha ataupun perorangan) dalam memenuhi kewajiban perpajakannya secara efektif.",
        },
        {
          name: "keywords",
          content:
            "AP Consulting, konsultan pajak, konsultan pajak berbasis hukum, akuntansi, perpajakan, tax consulting, payroll outsourcing, Andry Phangkawira, IKPI, Indonesia, Jakarta Barat, Tangerang",
        },
        { property: "og:title", content: "AP Consulting — Kantor Konsultan Pajak Berbasis Hukum" },
        {
          property: "og:description",
          content:
            "AP Consulting merupakan kantor konsultan pajak berbasis hukum yang berpengalaman, profesional dan berintegritas dalam membantu klien (badan usaha ataupun perorangan) dalam memenuhi kewajiban perpajakannya secara efektif.",
        },
        { property: "og:site_name", content: "AP Consulting" },
        { property: "og:type", content: "website" },
        { property: "og:image", content: "https://apconsulting.id/images/redesign/logo.png" },
        { name: "twitter:card", content: "summary_large_image" },
        { name: "twitter:title", content: "AP Consulting — Kantor Konsultan Pajak Berbasis Hukum" },
        {
          name: "twitter:description",
          content:
            "AP Consulting merupakan kantor konsultan pajak berbasis hukum yang berpengalaman, profesional dan berintegritas dalam membantu klien (badan usaha ataupun perorangan) dalam memenuhi kewajiban perpajakannya secara efektif.",
        },
        { name: "twitter:image", content: "https://apconsulting.id/images/redesign/logo.png" },
      ],
    },
  },
});

