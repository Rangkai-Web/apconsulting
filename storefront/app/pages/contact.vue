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
  link: [{ rel: "canonical", href: currentUrl }],
  script: [
    {
      type: "application/ld+json",
      innerHTML: JSON.stringify({
        "@context": "https://schema.org",
        "@type": "ContactPage",
        name: "Contact AP Consulting",
        url: currentUrl,
        mainEntity: {
          "@type": "Organization",
          name: "AP Consulting",
          email: "contact@apconsulting.id",
          telephone: "+62 857 5417 2217",
          address: {
            "@type": "PostalAddress",
            streetAddress: "SUN Plaza, Jl. Meruya Ilir Raya No. 20",
            addressLocality: "Jakarta Barat",
            addressRegion: "DKI Jakarta",
            addressCountry: "ID",
          },
        },
      }),
    },
  ],
});
</script>
