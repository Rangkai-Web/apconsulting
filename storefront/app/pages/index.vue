<template>
  <div class="w-full bg-white font-sans text-[#092F50] antialiased">
    <HomeHero />
    <HomeServices />
    <HomeSectors />
    <HomeAbout />
    <HomeValues />
    <HomeApproach />
    <HomeInsights />
    <HomeFaq />
    <HomeCta />
  </div>
</template>

<script setup lang="ts">
import { useSeoText } from "~/composables/useSeoText";

const config = useRuntimeConfig();
const { toSafeJsonLd } = useSeoText();
const canonicalUrl = `${config.public.siteUrl}/`;

// Comprehensive SEO Metadata
const title = "AP Consulting — Tax & Consulting Services";
const description =
  "Professional tax and accounting support, so you can focus on growing your business. Beyond numbers, it's about trust.";

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
        "@type": "AccountingService",
        name: "AP Consulting",
        alternateName: "AP Tax Consulting Services",
        url: config.public.siteUrl,
        logo: `${config.public.siteUrl}/images/redesign/logo.png`,
        description,
        email: "contact@apconsulting.id",
        telephone: "+6285754172217",
      }),
    },
  ],
});
</script>
