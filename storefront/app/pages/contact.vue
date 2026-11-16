<template>
  <main class="min-h-screen bg-white text-[#092F50] antialiased selection:bg-[#0869B2] selection:text-white">
    <ContactHeroForm />
    <ContactLocationMap />
    <HomeCta />
  </main>
</template>

<script setup lang="ts">
const { t } = useLanguage();
const config = useRuntimeConfig();
const route = useRoute();
const siteUrl = config.public.siteUrl || "https://apconsulting.id";
const currentUrl = `${siteUrl}${route.path}`;

useSeoMeta({
  title: computed(() => t("seo.contact.title")),
  ogTitle: computed(() => t("seo.contact.title")),
  description: computed(() => t("seo.contact.description")),
  ogDescription: computed(() => t("seo.contact.description")),
  ogUrl: currentUrl,
  ogImage: `${siteUrl}/images/redesign/logo.png`,
  twitterCard: "summary_large_image",
  twitterTitle: computed(() => t("seo.contact.title")),
  twitterDescription: computed(() => t("seo.contact.description")),
  twitterImage: `${siteUrl}/images/redesign/logo.png`,
});

useHead({
  link: [
    { rel: "canonical", href: currentUrl },
    { rel: "alternate", hreflang: "id", href: `${siteUrl}/contact` },
    { rel: "alternate", hreflang: "en", href: `${siteUrl}/contact` },
    { rel: "alternate", hreflang: "x-default", href: `${siteUrl}/contact` },
  ],
  script: [
    {
      type: "application/ld+json",
      innerHTML: JSON.stringify({
        "@context": "https://schema.org",
        "@graph": [
          {
            "@type": "BreadcrumbList",
            itemListElement: [
              {
                "@type": "ListItem",
                position: 1,
                name: "Home",
                item: `${siteUrl}/`,
              },
              {
                "@type": "ListItem",
                position: 2,
                name: "Contact",
                item: currentUrl.value,
              },
            ],
          },
          {
            "@type": "ContactPage",
            name: "Contact AP Consulting — Tax & Consulting Services",
            description: "Hubungi kantor konsultan pajak AP Consulting di Jakarta Barat atau Gading Serpong Tangerang untuk konsultasi pajak, akuntansi, dan payroll.",
            url: currentUrl.value,
            mainEntity: {
              "@type": "AccountingService",
              name: "AP Consulting",
              url: siteUrl,
              email: "contact@apconsulting.id",
              telephone: "+6285754172217",
              address: [
                {
                  "@type": "PostalAddress",
                  name: "Head Office Jakarta Barat",
                  streetAddress: "SUN Plaza, Jl. Meruya Ilir Raya No. 20",
                  addressLocality: "Jakarta Barat",
                  addressRegion: "DKI Jakarta",
                  postalCode: "11620",
                  addressCountry: "ID",
                },
                {
                  "@type": "PostalAddress",
                  name: "Branch Office Gading Serpong",
                  streetAddress: "Ruko Alexandrite 3 No. 23, Jl. Boulevard Gading Serpong",
                  addressLocality: "Tangerang",
                  addressRegion: "Banten",
                  postalCode: "15810",
                  addressCountry: "ID",
                },
              ],
            },
          },
        ],
      }),
    },
  ],
});
</script>
