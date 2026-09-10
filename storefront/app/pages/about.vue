<template>
  <div>
    <AboutHero />
    <AboutPcg />
    <AboutVision />
    <AboutWhyChoose />
    <AboutPeople />
    <HomeCta />
  </div>
</template>

<script setup lang="ts">
import { useSeoText } from "~/composables/useSeoText";

const config = useRuntimeConfig();
const { toSafeJsonLd } = useSeoText();
const canonicalUrl = `${config.public.siteUrl}/about`;
const title = "About Us — Phangkawira Consulting Group";
const description =
  "Learn about Phangkawira Consulting Group. More than accountants, partners in your progress providing tax, accounting, and financial consulting.";

useSeoMeta({
  title,
  ogTitle: title,
  description,
  ogDescription: description,
  ogImage: `${config.public.siteUrl}/pcg_logo.jpeg`,
  ogUrl: canonicalUrl,
  twitterCard: "summary_large_image",
  twitterTitle: title,
  twitterDescription: description,
  twitterImage: `${config.public.siteUrl}/pcg_logo.jpeg`,
});

useHead({
  link: [{ rel: "canonical", href: canonicalUrl }],
  script: [
    {
      type: "application/ld+json",
      innerHTML: toSafeJsonLd({
        "@context": "https://schema.org",
        "@type": "AboutPage",
        name: title,
        description,
        url: canonicalUrl,
        mainEntity: {
          "@type": "Organization",
          name: "Phangkawira Consulting Group",
          url: config.public.siteUrl,
          logo: `${config.public.siteUrl}/pcg_logo.jpeg`,
        },
      }),
    },
  ],
});
</script>
