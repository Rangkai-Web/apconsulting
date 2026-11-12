<template>
  <main class="min-h-screen bg-white text-[#092F50] antialiased selection:bg-[#0869B2] selection:text-white">
    <InsightHero />
    <InsightArticles />
    <HomeCta />
  </main>
</template>

<script setup lang="ts">
import { useSeoText } from "~/composables/useSeoText";

definePageMeta({
  alias: ["/insigth", "/insights"],
});

const { toSafeJsonLd } = useSeoText();
const config = useRuntimeConfig();
const canonicalUrl = `${config.public.siteUrl}/insight`;
const title = "Insights & Articles — AP Consulting";
const description =
  "Stay informed with AP Consulting's latest tax regulations, accounting best practices, and strategic financial insights.";

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
        "@type": "Blog",
        name: title,
        description,
        url: canonicalUrl,
        publisher: {
          "@type": "Organization",
          name: "AP Consulting",
          logo: `${config.public.siteUrl}/ap_logo.jpeg`,
        },
      }),
    },
  ],
});
</script>
