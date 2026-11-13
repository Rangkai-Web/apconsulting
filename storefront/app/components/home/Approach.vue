<script setup lang="ts">
import { computed } from "vue";
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();

const steps = computed(() => [
  {
    number: "01",
    title: t("home.v3.approach.s1Title"),
    description: t("home.v3.approach.s1Desc"),
  },
  {
    number: "02",
    title: t("home.v3.approach.s2Title"),
    description: t("home.v3.approach.s2Desc"),
  },
  {
    number: "03",
    title: t("home.v3.approach.s3Title"),
    description: t("home.v3.approach.s3Desc"),
  },
  {
    number: "04",
    title: t("home.v3.approach.s4Title"),
    description: t("home.v3.approach.s4Desc"),
  },
]);
</script>

<template>
  <section
    class="w-full bg-white py-12 sm:py-14 lg:py-16 border-b border-[#DCE8F5]"
  >
    <div class="mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <!-- Clean Left-Aligned Header -->
      <div class="max-w-[640px] mb-8 sm:mb-10" data-reveal="up">
        <p
          class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#0869B2] mb-2"
        >
          {{ t("home.v3.approach.eyebrow") }}
        </p>
        <h2
          class="text-2xl sm:text-4xl font-semibold leading-[1.18] tracking-[-0.03em] text-[#092F50] mb-3"
        >
          <span class="font-bold">{{ t("home.v3.approach.titleBold") }}</span>
          <span class="italic text-[#0869B2]">{{
            t("home.v3.approach.titleItalic")
          }}</span>
        </h2>
        <p class="text-sm sm:text-[15px] leading-[1.65] text-[#52687B]">
          {{ t("home.v3.approach.subtitle") }}
        </p>
      </div>

      <!-- Unified 4-Column Architectural Grid (1-col mobile, 2x2 tablet, 4-col desktop with clean 1px borders) -->
      <div
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-px bg-[#DCE8F5] border border-[#DCE8F5] rounded-[2px] overflow-hidden"
        data-reveal="up"
        data-reveal-delay="100"
      >
        <div
          v-for="(step, sIdx) in steps"
          :key="step.number"
          class="p-6 sm:p-7 lg:p-8 flex flex-col justify-between bg-[#F8FBFF] hover:bg-white transition-colors duration-200"
          :style="{ transitionDelay: `${sIdx * 100}ms` }"
        >
          <div>
            <span
              class="block text-2xl font-bold tracking-[-0.02em] text-[#0869B2] mb-4 sm:mb-5"
            >
              {{ step.number }}
            </span>

            <h3
              class="text-[17px] sm:text-[18px] font-bold leading-[1.35] text-[#092F50] mb-2.5 sm:mb-3"
            >
              {{ step.title }}
            </h3>

            <p class="text-[14px] leading-[1.65] text-[#52687B]">
              {{ step.description }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
