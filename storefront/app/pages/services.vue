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
const title = "Services — Tax, Payroll Outsourcing & Tax Accounting | AP Consulting";
const description =
  "Explore AP Consulting's professional services: Tax Services (Compliance, Review, Audit & Dispute Resolution), Payroll Outsourcing Services, and Tax Accounting Services.";

useSeoMeta({
  title,
  ogTitle: title,
  description,
  ogDescription: description,
  ogImage: `${config.public.siteUrl}/images/redesign/logo.png`,
  ogUrl: canonicalUrl,
  twitterCard: "summary_large_image",
  twitterTitle: title,
  twitterDescription: description,
  twitterImage: `${config.public.siteUrl}/images/redesign/logo.png`,
});

useHead({
  link: [{ rel: "canonical", href: canonicalUrl }],
  script: [
    {
      type: "application/ld+json",
      innerHTML: toSafeJsonLd({
        "@context": "https://schema.org",
        "@type": "ItemList",
        name: title,
        description,
        url: canonicalUrl,
        itemListElement: [
          {
            "@type": "Service",
            name: "Tax Services",
            description:
              "Tax Compliance Service, Tax Review Service, and Tax Dispute Resolution & Tax Audit assistance.",
            provider: {
              "@type": "Organization",
              name: "AP Consulting",
            },
          },
          {
            "@type": "Service",
            name: "Payroll Outsourcing Services",
            description:
              "Monthly payroll administration, PPh 21/26 calculation & withholding, payslips, and periodic employee tax reporting.",
            provider: {
              "@type": "Organization",
              name: "AP Consulting",
            },
          },
          {
            "@type": "Service",
            name: "Tax Accounting Services",
            description:
              "Bookkeeping, monthly & annual financial statements, and fiscal reconciliation for tax compliance.",
            provider: {
              "@type": "Organization",
              name: "AP Consulting",
            },
          },
        ],
      }),
    },
  ],
});
</script>
