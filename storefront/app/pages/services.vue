<template>
  <div>
    <ServicesHero />
    <ServicesScopeOfSupport />
    <ServicesCurrentNeed />
    <HomeOurApproach
      :tag="t('services.ourApproach.tag')"
      :title-prefix="t('services.ourApproach.titlePrefix')"
      :title-highlight="t('services.ourApproach.titleHighlight')"
      :title-suffix="t('services.ourApproach.titleSuffix')"
      :subtitle="t('services.ourApproach.subtitle')"
    />
    <HomeFaq />
    <HomeCta
      :tag="t('services.cta.tag')"
      :title-normal="t('services.cta.titleNormal')"
      title-italic=""
      :description="t('services.cta.description')"
      :button-text="t('services.cta.buttonText')"
      button-link="/contact"
    />
  </div>
</template>

<script setup lang="ts">
import { useLanguage } from '~/composables/useLanguage';
import { useSeoText } from '~/composables/useSeoText';

const { t } = useLanguage();
const { toSafeJsonLd } = useSeoText();
const config = useRuntimeConfig();
const canonicalUrl = `${config.public.siteUrl}/services`;
const title = "Services — Tax, Accounting & Advisory";
const description =
  "Explore PCG's comprehensive services: Tax Box, Accounting & Bookkeeping, Business Administration Support, and custom financial advisory.";

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
        "@type": "ItemList",
        name: title,
        description,
        url: canonicalUrl,
        itemListElement: [
          {
            "@type": "Service",
            name: "Tax Box",
            description: "Complete tax compliance and advisory services.",
            provider: {
              "@type": "Organization",
              name: "Phangkawira Consulting Group",
            },
          },
          {
            "@type": "Service",
            name: "Accounting & Bookkeeping",
            description: "Structured financial reporting and bookkeeping support.",
            provider: {
              "@type": "Organization",
              name: "Phangkawira Consulting Group",
            },
          },
          {
            "@type": "Service",
            name: "Business Administration Support",
            description: "Operational and corporate administrative assistance.",
            provider: {
              "@type": "Organization",
              name: "Phangkawira Consulting Group",
            },
          },
        ],
      }),
    },
  ],
});
</script>
