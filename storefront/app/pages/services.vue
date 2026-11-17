<template>
  <main class="min-h-screen bg-white text-[#092F50] antialiased selection:bg-[#0869B2] selection:text-white">
    <!-- 1. Services Hero Banner -->
    <ServicesHero />

    <!-- 2. Scope of Support (AP Consulting 3 Core Services: Tax, Payroll Outsourcing, Tax Accounting) -->
    <ServicesScopeOfSupport />

    <!-- 3. Start With Your Current Need -->
    <ServicesCurrentNeed />

    <!-- 4. Our Approach (How We Work With You) -->
    <HomeApproach />

    <!-- 5. Consultation CTA Banner -->
    <HomeCta />
  </main>
</template>

<script setup lang="ts">
import { useSeoText } from "~/composables/useSeoText";

const { toSafeJsonLd } = useSeoText();
const config = useRuntimeConfig();
const canonicalUrl = `${config.public.siteUrl}/services`;
const title = "Services — Tax, Payroll Outsourcing & Accounting Services | AP Consulting";
const description =
  "Explore AP Consulting's professional services: Tax Services (Compliance, Review, Audit Assistance), Payroll Outsourcing Services, and Accounting Services.";

useSeoMeta({
  title,
  ogTitle: title,
  description,
  ogDescription: description,
  ogImage: `${config.public.siteUrl}/images/redesign/logo.webp`,
  ogUrl: canonicalUrl,
  twitterCard: "summary_large_image",
  twitterTitle: title,
  twitterDescription: description,
  twitterImage: `${config.public.siteUrl}/images/redesign/logo.webp`,
});

useHead({
  link: [
    { rel: "canonical", href: canonicalUrl },
    { rel: "alternate", hreflang: "id", href: `${config.public.siteUrl}/services` },
    { rel: "alternate", hreflang: "en", href: `${config.public.siteUrl}/services` },
    { rel: "alternate", hreflang: "x-default", href: `${config.public.siteUrl}/services` },
  ],
  script: [
    {
      type: "application/ld+json",
      innerHTML: toSafeJsonLd({
        "@context": "https://schema.org",
        "@graph": [
          {
            "@type": "BreadcrumbList",
            itemListElement: [
              {
                "@type": "ListItem",
                position: 1,
                name: "Home",
                item: `${config.public.siteUrl}/`,
              },
              {
                "@type": "ListItem",
                position: 2,
                name: "Services",
                item: canonicalUrl,
              },
            ],
          },
          {
            "@type": "ItemList",
            name: title,
            description,
            url: canonicalUrl,
            itemListElement: [
              {
                "@type": "Service",
                name: "Jasa Perpajakan (Tax Services)",
                serviceType: "Tax Advisory & Compliance",
                description:
                  "Jasa Tax Compliance (SPT Masa & Tahunan), Jasa Penelaahan Pajak (Tax Review), dan Jasa Pendampingan Klien dalam Pemeriksaan Pajak (Tax Audit).",
                provider: {
                  "@type": "AccountingService",
                  name: "AP Consulting",
                  url: config.public.siteUrl,
                },
              },
              {
                "@type": "Service",
                name: "Jasa Payroll Outsourcing",
                serviceType: "Payroll & HR Administration",
                description:
                  "Pengelolaan data gaji dan PPh 21, administrasi BPJS Ketenagakerjaan & Kesehatan, serta pembuatan e-Payslip digital.",
                provider: {
                  "@type": "AccountingService",
                  name: "AP Consulting",
                  url: config.public.siteUrl,
                },
              },
              {
                "@type": "Service",
                name: "Jasa Akuntansi (Accounting Services)",
                serviceType: "Accounting & Bookkeeping",
                description:
                  "Penyusunan Laporan Keuangan PT, CV, dan UMKM, Accounting Review, Internal Audit, serta Konsultasi Software Akuntansi.",
                provider: {
                  "@type": "AccountingService",
                  name: "AP Consulting",
                  url: config.public.siteUrl,
                },
              },
            ],
          },
        ],
      }),
    },
  ],
});
</script>
