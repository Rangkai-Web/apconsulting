<template>
  <UApp>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </UApp>
</template>

<script setup lang="ts">
import { useSeoText } from "~/composables/useSeoText";

const { toSafeJsonLd } = useSeoText();
const config = useRuntimeConfig();

useHead(() => ({
  script: [
    {
      type: "application/ld+json",
      innerHTML: toSafeJsonLd({
        "@context": "https://schema.org",
        "@type": "ProfessionalService",
        name: "Phangkawira Consulting Group",
        url: config.public.siteUrl,
        email: "pcgserver9@gmail.com",
        address: {
          "@type": "PostalAddress",
          streetAddress:
            "Lt 3 Sayur Sentul, Blk. D, Rukan Cordoba, Jl. Green Lake City Boulevard No.23",
          addressLocality: "Kota Tangerang",
          addressRegion: "Banten",
          postalCode: "15146",
          addressCountry: "ID",
        },
      }),
    },
  ],
}));
</script>

