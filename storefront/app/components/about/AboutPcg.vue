<template>
  <section
    id="about-ap"
    class="scroll-mt-20 bg-white py-12 sm:py-16 lg:py-20 border-b border-[#DCE8F5]"
  >
    <div class="mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <!-- Top Header -->
      <div class="max-w-[760px] mb-8 sm:mb-10">
        <p
          class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#0869B2] mb-2"
        >
          {{ t("about.pcg.tag") }}
        </p>
        <h2
          class="text-2xl sm:text-4xl font-semibold leading-[1.18] tracking-[-0.03em] text-[#092F50]"
        >
          <span class="font-bold">{{ t("about.pcg.titleNormal") }}</span>
          {{ " " }}
          <span class="font-bold italic text-[#0869B2]">{{
            t("about.pcg.titleItalic")
          }}</span>
        </h2>
      </div>

      <!-- Two-Column Editorial Narrative -->
      <div
        class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-10 lg:gap-14 text-sm sm:text-[15.5px] text-[#4E6B85] leading-[1.75]"
      >
        <p class="text-[#092F50] font-medium">
          {{ t("about.pcg.paragraph1") }}
        </p>
        <p>
          {{ t("about.pcg.paragraph2") }}
        </p>
      </div>

      <!-- Key Credentials / Numbers Strip -->
      <dl
        class="mt-10 sm:mt-12 pt-8 sm:pt-10 border-t border-[#DCE8F5] grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8"
      >
        <div
          v-for="stat in stats"
          :key="stat.label"
          class="flex flex-col gap-1.5"
        >
          <dd
            class="text-3xl sm:text-4xl font-bold leading-none tracking-[-0.02em] text-[#0869B2]"
          >
            {{ stat.value }}
          </dd>
          <dt class="text-xs sm:text-sm leading-[1.5] text-[#52687B]">
            {{ stat.label }}
          </dt>
        </div>
      </dl>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();

const stats = computed(() => [
  {
    value: "15+",
    label: t("home.v3.hero.stat1Label"),
  },
  {
    value: "50+",
    label: t("home.v3.hero.stat2Label"),
  },
  {
    value: "300+",
    label: t("home.v3.hero.stat3Label"),
  },
  {
    value: "8",
    label: t("home.v3.hero.stat4Label"),
  },
]);
</script>
