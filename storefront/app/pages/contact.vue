<template>
  <div>
    <ContactHeroForm />
    <ContactLocationMap />
    <HomeCta
      :tag="t('contact.cta.tag')"
      title-normal=""
      :title-italic="t('contact.cta.titleItalic')"
      :description="t('contact.cta.description')"
      :button-text="t('contact.cta.buttonText')"
      button-link="#fullName"
    />
  </div>
</template>

<script setup lang="ts">
import { useLanguage } from "~/composables/useLanguage";
import { useSeoText } from "~/composables/useSeoText";

const { t } = useLanguage();
const { toSafeJsonLd } = useSeoText();
const config = useRuntimeConfig();
const canonicalUrl = `${config.public.siteUrl}/contact`;
const title = "Contact Us — Phangkawira Consulting Group";
const description =
  "Get in touch with Phangkawira Consulting Group for professional tax, accounting, bookkeeping, and business financial advisory services.";

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
        "@type": "ContactPage",
        name: title,
        description,
        url: canonicalUrl,
        mainEntity: {
          "@type": "Organization",
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
        },
      }),
    },
  ],
});
</script>
