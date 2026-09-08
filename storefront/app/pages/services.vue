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
import { useLanguage } from '~/composables/useLanguage'

const { t } = useLanguage()

useHead({
  title: "Services",
  meta: [
    {
      name: "description",
      content:
        "Explore PCG's comprehensive services: Tax Box, Accounting & Bookkeeping, Business Administration Support, and custom financial advisory.",
    },
  ],
});
</script>
