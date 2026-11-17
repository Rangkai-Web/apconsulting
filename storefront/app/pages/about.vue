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
  ogImage: `${config.public.siteUrl}/images/redesign/logo.webp`,
  ogUrl: canonicalUrl,
  twitterCard: "summary_large_image",
  twitterTitle: title,
  twitterDescription: description,
  twitterImage: `${config.public.siteUrl}/images/redesign/logo.webp`,
});

useHead({
  link: [
    { rel: "canonical", href: canonicalUrl },
    { rel: "alternate", hreflang: "id", href: `${config.public.siteUrl}/about` },
    { rel: "alternate", hreflang: "en", href: `${config.public.siteUrl}/about` },
    { rel: "alternate", hreflang: "x-default", href: `${config.public.siteUrl}/about` },
  ],
  script: [
    {
      type: "application/ld+json",
      innerHTML: toSafeJsonLd({
        "@context": "https://schema.org",
        "@graph": [
          {
            "@type": "BreadcrumbList",
            itemListElement: [
              {
                "@type": "ListItem",
                position: 1,
                name: "Home",
                item: `${config.public.siteUrl}/`,
              },
              {
                "@type": "ListItem",
                position: 2,
                name: "About Us",
                item: canonicalUrl,
              },
            ],
          },
          {
            "@type": "AboutPage",
            name: title,
            description,
            url: canonicalUrl,
            mainEntity: {
              "@type": "AccountingService",
              name: "AP Consulting",
              url: config.public.siteUrl,
              logo: `${config.public.siteUrl}/images/redesign/logo.webp`,
              image: `${config.public.siteUrl}/images/redesign/office.webp`,
              description,
              telephone: "+6285754172217",
              email: "contact@apconsulting.id",
              founder: {
                "@type": "Person",
                name: "Andry Phangkawira, S.E., Ak., BKP.",
                jobTitle: "Partner & Licensed Tax Consultant",
                identifier: "KIP-6592/IP.B/PJ/2020",
                sameAs: "https://www.linkedin.com/in/andry-phangkawira/",
                image: "https://www.apconsulting.id/images/team/andry.jpeg",
                alumniOf: "Universitas",
                memberOf: {
                  "@type": "Organization",
                  name: "Ikatan Konsultan Pajak Indonesia (IKPI)",
                },
              },
            },
          },
        ],
      }),
    },
  ],
});
</script>
