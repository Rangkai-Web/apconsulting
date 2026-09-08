<template>
  <div>
    <ContactHeroForm />
    <ContactLocationMap />
    <HomeCta
      tag="START A CONVERSATION"
      title-normal=""
      title-italic="Ready to take the next step?"
      description="Discuss your business needs with PCG."
      button-text="Discuss Your Needs"
      button-link="#fullName"
    />

  </div>
</template>

<script setup lang="ts">
useHead({
  title: "Contact Us",
  meta: [
    {
      name: "description",
      content:
        "Get in touch with Phangkawira Consulting Group for professional tax, accounting, bookkeeping, and business financial advisory services.",
    },
  ],
});
</script>
