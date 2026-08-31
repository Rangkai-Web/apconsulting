<template>
  <UApp>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </UApp>
</template>

<script setup lang="ts">
import { onMounted } from "vue";
import { useStoreSeo } from "~/composables/useStoreSeo";
import { useSeoText } from "~/composables/useSeoText";

onMounted(() => {});

const { settings } = useStoreSeo();
const { toSafeJsonLd } = useSeoText();
const config = useRuntimeConfig();

useHead(() => ({
  script: settings.value
    ? [
        {
          type: "application/ld+json",
          innerHTML: toSafeJsonLd({
            "@context": "https://schema.org",
            "@type": "OnlineStore",
            name: settings.value.store_name || "BbystarGO",
            url: config.public.siteUrl,
            logo: settings.value.logo || `${config.public.siteUrl}/BbystarGO_1.jpg`,
            ...(settings.value.address
              ? { address: { "@type": "PostalAddress", streetAddress: settings.value.address } }
              : {}),
            ...(settings.value.whatsapp_number
              ? {
                  contactPoint: [
                    {
                      "@type": "ContactPoint",
                      telephone: settings.value.whatsapp_number,
                      contactType: "customer service",
                    },
                  ],
                }
              : {}),
          }),
        },
      ]
    : [],
}));
</script>
