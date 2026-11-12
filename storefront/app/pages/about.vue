<template>
  <main class="min-h-screen bg-white text-[#092F50] antialiased selection:bg-[#0869B2] selection:text-white">
    <AboutHero />
    <AboutPcg />
    <AboutVision />
    <AboutWhyChoose />
    <AboutPeople />
    <HomeCta />
  </main>
</template>

<script setup lang="ts">
import { useSeoText } from "~/composables/useSeoText";

const config = useRuntimeConfig();
const { toSafeJsonLd } = useSeoText();
const canonicalUrl = `${config.public.siteUrl}/about`;
const title = "About Us — AP Consulting";
const description =
  "AP Consulting merupakan kantor konsultan pajak berbasis hukum yang berpengalaman, profesional dan berintegritas dalam membantu klien (badan usaha ataupun perorangan) dalam memenuhi kewajiban perpajakannya secara efektif.";

useSeoMeta({
  title,
  ogTitle: title,
  description,
  ogDescription: description,
  ogImage: `${config.public.siteUrl}/ap_logo.jpeg`,
  ogUrl: canonicalUrl,
  twitterCard: "summary_large_image",
  twitterTitle: title,
  twitterDescription: description,
  twitterImage: `${config.public.siteUrl}/ap_logo.jpeg`,
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
          name: "AP Consulting",
          url: config.public.siteUrl,
          logo: `${config.public.siteUrl}/ap_logo.jpeg`,
        },
      }),
    },
  ],
});
</script>
