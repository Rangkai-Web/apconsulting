<template>
  <UApp>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </UApp>
</template>

<script setup lang="ts">
import { useSeoText } from "~/composables/useSeoText";
import { useLanguage } from "~/composables/useLanguage";

const { toSafeJsonLd } = useSeoText();
const { currentLocale } = useLanguage();
const config = useRuntimeConfig();

useHead(() => ({
  htmlAttrs: {
    lang: currentLocale.value,
  },
  script: [
    {
      type: "application/ld+json",
      innerHTML: toSafeJsonLd({
        "@context": "https://schema.org",
        "@type": "ProfessionalService",
        name: "Phangkawira Consulting Group",
        alternateName: "PCG Consulting",
        url: config.public.siteUrl,
        logo: `${config.public.siteUrl}/pcg_logo.jpeg`,
        image: `${config.public.siteUrl}/pcg_logo.jpeg`,
        description:
          "PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals.",
        email: "pcgserver9@gmail.com",
        telephone: "+628123456789",
        address: {
          "@type": "PostalAddress",
          streetAddress:
            "Lt 3 Sayur Sentul, Blk. D, Rukan Cordoba, Jl. Green Lake City Boulevard No.23",
          addressLocality: "Kota Tangerang",
          addressRegion: "Banten",
          postalCode: "15146",
          addressCountry: "ID",
        },
        priceRange: "$$",
      }),
    },
  ],
}));
</script>

