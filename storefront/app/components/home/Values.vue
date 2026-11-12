<template>
  <section class="relative overflow-hidden bg-[#072B4A] text-white py-12 sm:py-14 lg:py-16">
    <!-- Background Image & Rich Blue Gradient Overlay -->
    <img
      src="/images/redesign/values-bg.png"
      alt=""
      aria-hidden="true"
      class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none"
      loading="lazy"
    />
    <div
      class="absolute inset-0 bg-gradient-to-r from-[#062440]/96 via-[#083B66]/92 to-[#0869B2]/88 pointer-events-none"
    />

    <div class="relative z-10 mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <!-- Compact Header Row -->
      <div class="mb-8 sm:mb-10 pb-6 sm:pb-8 border-b border-white/15 flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <div>
          <p class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#B9DBF4] mb-2">
            {{ t("home.v3.values.eyebrow") }}
          </p>
          <h2
            class="text-2xl sm:text-4xl font-medium leading-[1.18] tracking-[-0.03em] text-white"
          >
            <span class="font-semibold">{{ t("home.v3.values.titleBold") }}</span> {{ t("home.v3.values.titleMiddle") }}
            <span class="font-bold italic text-[#D1EBFF]">{{ t("home.v3.values.titleItalic") }}</span>
          </h2>
        </div>

        <p class="text-xs sm:text-[15px] italic text-[#D8E5F0] lg:text-right">
          {{ t("home.v3.values.quote") }}
        </p>
      </div>

      <!-- 3-Column Architectural Divided Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 divide-y divide-white/15 md:divide-y-0 md:divide-x md:divide-white/20">
        <div
          v-for="(item, idx) in values"
          :key="item.title"
          :class="[
            'flex flex-col gap-2.5',
            idx === 0 ? 'pb-6 md:pb-0 md:pr-10' : '',
            idx === 1 ? 'py-6 md:py-0 md:px-10' : '',
            idx === 2 ? 'pt-6 md:pt-0 md:pl-10' : '',
          ]"
        >
          <h3 class="text-lg sm:text-2xl font-semibold text-white">
            {{ item.title }}
          </h3>
          <p class="text-sm sm:text-[15px] text-[#D8E5F0] leading-[1.65]">
            {{ item.description }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();

const values = computed(() => [
  {
    title: t("home.v3.values.v1Title"),
    description: t("home.v3.values.v1Desc"),
  },
  {
    title: t("home.v3.values.v2Title"),
    description: t("home.v3.values.v2Desc"),
  },
  {
    title: t("home.v3.values.v3Title"),
    description: t("home.v3.values.v3Desc"),
  },
]);
</script>
