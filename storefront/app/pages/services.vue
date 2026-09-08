<template>
  <div>
    <ServicesHero />
    <ServicesScopeOfSupport />
    <ServicesCurrentNeed />
    <HomeOurApproach
      tag="HOW WE WORK"
      title-prefix="From "
      title-highlight="understanding "
      title-suffix="to confident action."
      subtitle="Managing and growing your business with strategic financial advice starts with four clear steps."
    />
    <HomeFaq />
    <HomeCta
      tag="START A CONVERSATION"
      title-normal="Not sure which service is right for you?"
      title-italic=""
      description="Tell us about your current situation. We'll help you define the right scope of support for your business."
      button-text="Discuss Your Needs"
      button-link="/contact"
    />
  </div>
</template>

<script setup lang="ts">
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
