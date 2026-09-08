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
const { t } = useLanguage();

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
