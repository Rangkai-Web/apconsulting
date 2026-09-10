<template>
  <div>
    <InsightHero />
    <InsightArticles />
  </div>
</template>

<script setup lang="ts">
import { useSeoText } from "~/composables/useSeoText";

definePageMeta({
  alias: ["/insight", "/insights"],
});

const { toSafeJsonLd } = useSeoText();
const config = useRuntimeConfig();
const canonicalUrl = `${config.public.siteUrl}/insight`;
const title = "Insights & Articles — Phangkawira Consulting Group";
const description =
  "Stay informed with PCG's latest tax regulations, accounting best practices, and strategic financial insights.";

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
        "@type": "Blog",
        name: title,
        description,
        url: canonicalUrl,
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
