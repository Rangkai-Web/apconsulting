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
            class="text-lg font-semibold uppercase tracking-wide text-neutral-400"
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

      <!-- Google Maps Container -->
      <div
        ref="mapContainer"
        class="relative w-full h-[380px] md:h-[450px] overflow-hidden border border-[#DFE7ED] shadow-xs bg-neutral-100"
      >
        <iframe
          v-if="isMapVisible"
          title="PCG Office Location Map"
          :src="mapsEmbedUrl"
          width="100%"
          height="100%"
          style="border: 0"
          allowfullscreen="true"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
        ></iframe>
        <div
          v-else
          class="flex flex-col items-center justify-center h-full gap-3 p-6 text-center"
        >
          <UIcon name="i-heroicons-map-pin" class="size-10 text-[#083653]" />
          <p class="text-sm font-medium text-neutral-600">
            {{ address || t("contact.location.address") }}
          </p>
          <UButton
            color="secondary"
            variant="solid"
            size="sm"
            class="rounded-none font-medium mt-2"
            @click="loadMap"
          >
            Buka Peta Interaktif
          </UButton>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";

const { t } = useLanguage();
const { address, mapsEmbedUrl } = useCompanySettings();

const mapContainer = ref<HTMLElement | null>(null);
const isMapVisible = ref(false);

const loadMap = () => {
  isMapVisible.value = true;
};

onMounted(() => {
  if (
    typeof window !== "undefined" &&
    "IntersectionObserver" in window &&
    mapContainer.value
  ) {
    const observer = new IntersectionObserver(
      (entries) => {
        const firstEntry = entries[0];
        if (firstEntry && firstEntry.isIntersecting) {
          isMapVisible.value = true;
          observer.disconnect();
        }
      },
      { rootMargin: "100px" },
    );
    observer.observe(mapContainer.value);
  } else {
    isMapVisible.value = true;
  }
});
</script>
