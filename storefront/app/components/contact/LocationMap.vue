<template>
  <section
    class="px-4 py-12 md:px-8 bg-[#F9FAFB] border-t border-b border-[#DFE7ED]"
  >
    <div class="mx-auto max-w-7xl">
      <div
        class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4"
      >
        <div>
          <p
            class="text-sm font-semibold uppercase tracking-wide text-neutral-400"
          >
            {{ t("contact.location.tag") }}
          </p>
          <h2 class="mt-2 text-2xl md:text-3xl font-bold text-[#083653]">
            {{ t("contact.location.title") }}
          </h2>
        </div>
        <p
          class="text-sm text-neutral-500 font-normal max-w-md text-left sm:text-right"
        >
          {{ address || t("contact.location.address") }}
        </p>
      </div>

      <!-- Google Maps Iframe -->
      <div
        class="w-full h-[380px] md:h-[450px] overflow-hidden border border-[#DFE7ED] shadow-xs"
      >
        <iframe
          title="PCG Office Location Map"
          :src="mapsEmbedUrl"
          width="100%"
          height="100%"
          style="border: 0"
          allowfullscreen="true"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
        ></iframe>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useLanguage } from "~/composables/useLanguage";
import { useCompanySettings } from "~/composables/useCompanySettings";

const { t } = useLanguage();
const { address, mapsEmbedUrl } = useCompanySettings();
</script>
