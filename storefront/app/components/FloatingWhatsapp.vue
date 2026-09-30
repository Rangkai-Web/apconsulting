<template>
  <a
    :href="whatsappLink"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="Hubungi kami via WhatsApp"
    class="fixed bottom-6 right-6 z-50 flex items-center justify-center size-14 rounded-full bg-[#25D366] text-white shadow-lg shadow-green-900/20 hover:bg-[#20ba5a] hover:scale-110 active:scale-95 transition-all duration-300 group"
  >
    <UIcon name="i-simple-icons-whatsapp" class="size-8 text-white transition-transform group-hover:rotate-12" />
  </a>
</template>

<script setup lang="ts">
const { whatsappLink } = useCompanySettings();
</script>
