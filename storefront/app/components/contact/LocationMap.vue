<template>
  <section class="border-b border-[#DCE8F5] bg-[#F4F9FE] py-12 sm:py-16 lg:py-20">
    <div class="mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8 flex flex-col justify-between gap-4 sm:mb-10 md:flex-row md:items-end">
        <div>
          <p class="mb-2 text-[11px] font-bold uppercase tracking-[0.14em] text-[#0869B2]">
            {{ t("contact.location.tag") }}
          </p>
          <h2
            class="text-2xl font-semibold leading-[1.18] tracking-[-0.03em] text-[#092F50] sm:text-4xl"
          >
            <span class="font-bold">{{ t("contact.location.titleNormal") }}</span>
            {{ " " }}
            <span class="font-bold italic text-[#0869B2]">{{ t("contact.location.titleItalic") }}</span>
          </h2>
        </div>

        <a
          :href="currentOffice.externalUrl"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex w-fit items-center gap-2 rounded-[2px] border border-[#0869B2] bg-white px-4 py-2.5 text-[13px] font-bold text-[#0869B2] transition-colors hover:bg-[#0869B2] hover:text-white"
        >
          <span>{{ t("contact.location.openMaps") }}</span>
          <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </a>
      </div>

      <!-- Office Selector Cards -->
      <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
        <button
          v-for="office in offices"
          :key="office.id"
          type="button"
          :class="[
            'group flex items-start gap-3.5 rounded-[2px] border p-5 text-left transition-colors',
            activeOffice === office.id
              ? 'border-[#0869B2] bg-white'
              : 'border-[#DCE8F5] bg-white/75 hover:border-[#9EC5E8] hover:bg-white',
          ]"
          @click="activeOffice = office.id"
        >
          <div
            :class="[
              'mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-[2px] transition-colors',
              activeOffice === office.id
                ? 'bg-[#0869B2] text-white'
                : 'bg-[#E9F3FC] text-[#0869B2]',
            ]"
          >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
              />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <div>
            <div class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#0869B2]">
              {{ t(office.labelKey) }}
            </div>
            <div class="mt-1 text-[17px] font-bold text-[#092F50]">
              {{ office.city }}
            </div>
            <p class="mt-1 text-[13.5px] leading-[1.6] text-[#52687B]">
              {{ office.address }}
            </p>
          </div>
        </button>
      </div>

      <!-- Map Container -->
      <div
        ref="mapContainer"
        class="relative h-[380px] w-full overflow-hidden rounded-[2px] border border-[#DCE8F5] bg-[#E9F3FC] sm:h-[440px]"
      >
        <iframe
          v-if="isMapVisible"
          :key="currentOffice.id"
          :title="`${t(currentOffice.labelKey)} — ${currentOffice.address}`"
          class="h-full w-full border-0"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          :src="currentOffice.embedUrl"
        ></iframe>
        <div
          v-else
          class="flex h-full w-full items-center justify-center text-[13px] text-[#52687B]"
        >
          {{ t("contact.location.loadMap") }}
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from "vue";

const { t } = useLanguage();

interface OfficeItem {
  id: "head" | "branch";
  labelKey: string;
  city: string;
  address: string;
  embedUrl: string;
  externalUrl: string;
}

const headOffice: OfficeItem = {
  id: "head",
  labelKey: "footer.headOffice",
  city: "Jakarta Barat",
  address: "SUN Plaza, Jl. Meruya Ilir Raya No. 20, Jakarta Barat",
  embedUrl:
    "https://maps.google.com/maps?q=SUN+Plaza+Jl+Meruya+Ilir+Raya+No+20+Jakarta+Barat&output=embed",
  externalUrl:
    "https://maps.google.com/?q=SUN+Plaza+Jl+Meruya+Ilir+Raya+No+20+Jakarta+Barat",
};

const branchOffice: OfficeItem = {
  id: "branch",
  labelKey: "footer.branchOffice",
  city: "Gading Serpong, Tangerang",
  address: "Ruko Alexandrite 3 No. 23, Jl. Boulevard Gading Serpong, Tangerang",
  embedUrl:
    "https://maps.google.com/maps?q=Ruko+Alexandrite+3+No+23+Jl+Boulevard+Gading+Serpong+Tangerang&output=embed",
  externalUrl:
    "https://maps.google.com/?q=Ruko+Alexandrite+3+No+23+Jl+Boulevard+Gading+Serpong+Tangerang",
};

const offices: OfficeItem[] = [headOffice, branchOffice];

const activeOffice = ref<"head" | "branch">("head");

const currentOffice = computed<OfficeItem>(() =>
  activeOffice.value === "branch" ? branchOffice : headOffice
);

const mapContainer = ref<HTMLElement | null>(null);
const isMapVisible = ref(false);
let observer: IntersectionObserver | null = null;

onMounted(() => {
  if (!mapContainer.value) return;

  if ("IntersectionObserver" in window) {
    observer = new IntersectionObserver(
      (entries) => {
        if (entries[0]?.isIntersecting) {
          isMapVisible.value = true;
          observer?.disconnect();
          observer = null;
        }
      },
      { rootMargin: "200px" }
    );
    observer.observe(mapContainer.value);
  } else {
    isMapVisible.value = true;
  }
});

onUnmounted(() => {
  observer?.disconnect();
});
</script>
