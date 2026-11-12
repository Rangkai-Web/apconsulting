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
        name: "AP Consulting",
        alternateName: "AP Tax Consulting",
        url: config.public.siteUrl,
        logo: `${config.public.siteUrl}/ap_logo.jpeg`,
        image: `${config.public.siteUrl}/ap_logo.jpeg`,
        description:
          "AP Consulting merupakan kantor konsultan pajak berbasis hukum yang berpengalaman, profesional dan berintegritas dalam membantu klien (badan usaha ataupun perorangan) dalam memenuhi kewajiban perpajakannya secara efektif.",
        email: "contact@apconsulting.id",
        telephone: "+6285754172217",
        address: [
          {
            "@type": "PostalAddress",
            name: "Head Office",
            streetAddress: "SUN Plaza, Jl. Meruya Ilir Raya No. 20",
            addressLocality: "Jakarta Barat",
            addressRegion: "DKI Jakarta",
            addressCountry: "ID",
          },
          {
            "@type": "PostalAddress",
            name: "Branch Office",
            streetAddress: "Ruko Alexandrite 3 No. 23, Jl. Boulevard Gading Serpong",
            addressLocality: "Tangerang",
            addressRegion: "Banten",
            addressCountry: "ID",
          },
        ],
        priceRange: "$$",
      }),
    },
  ],
}));
</script>
