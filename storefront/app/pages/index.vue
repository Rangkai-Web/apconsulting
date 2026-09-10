<template>
  <div>
    <HomeHero />
    <HomeAboutPcg />
    <HomeStats />
    <HomeHowWeHelp />
    <HomeWhyChooseUs />
    <HomeOurApproach />
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

useSeoMeta({
  title: "PCG — Phangkawira Consulting Group | Tax & Financial Advisory",
  ogTitle: "PCG — Phangkawira Consulting Group | Tax & Financial Advisory",
  description:
    "PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals.",
  ogDescription:
    "PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals.",
  ogImage: `${config.public.siteUrl}/pcg_logo.jpeg`,
  ogUrl: canonicalUrl,
  twitterCard: "summary_large_image",
  twitterTitle: "PCG — Phangkawira Consulting Group",
  twitterDescription:
    "PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals.",
  twitterImage: `${config.public.siteUrl}/pcg_logo.jpeg`,
});

useHead({
  link: [{ rel: "canonical", href: canonicalUrl }],
  script: [
    {
      type: "application/ld+json",
      innerHTML: toSafeJsonLd({
        "@context": "https://schema.org",
        "@type": "WebSite",
        name: "Phangkawira Consulting Group",
        url: config.public.siteUrl,
        publisher: {
          "@type": "Organization",
          name: "Phangkawira Consulting Group",
          logo: `${config.public.siteUrl}/pcg_logo.jpeg`,
        },
      }),
    },
  ],
});
</script>
