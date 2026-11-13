<template>
  <section class="bg-white py-12 sm:py-16 lg:py-20 border-b border-[#DCE8F5]">
    <div class="mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <!-- Clean Header -->
      <div class="max-w-[680px] mb-8 sm:mb-10" data-reveal="up">
        <p
          class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#0869B2] mb-2"
        >
          {{ t("services.need.tag") }}
        </p>
        <h2
          class="text-2xl sm:text-4xl font-semibold leading-[1.18] tracking-[-0.03em] text-[#092F50] mb-3"
        >
          <span class="font-bold">{{ t("services.need.titleNormal") }}</span>
          {{ " " }}
          <span class="font-bold italic text-[#0869B2]">{{
            t("services.need.titleItalic")
          }}</span>
        </h2>
        <p class="text-sm sm:text-[15px] leading-[1.65] text-[#52687B]">
          {{ t("services.need.subtitle") }}
        </p>
      </div>

      <!-- 4-Column Architectural Priority Grid -->
      <div
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6"
      >
        <a
          v-for="(need, idx) in needs"
          :key="need.number"
          :href="need.href"
          class="group hover-lift flex flex-col justify-between p-6 sm:p-7 rounded-[2px] bg-[#F8FBFF] border border-[#DCE8F5] hover:border-[#0869B2] hover:bg-white transition-all duration-200"
          data-reveal="up"
          :data-reveal-delay="idx * 100"
        >
          <div>
            <div class="flex items-center justify-between mb-4 sm:mb-5">
              <span
                class="text-2xl font-bold tracking-[-0.02em] text-[#0869B2]"
              >
                {{ need.number }}
              </span>
              <span
                class="text-sm text-[#0869B2] opacity-70 group-hover:opacity-100 transition-all duration-200 group-hover:translate-x-0.5"
              >
                →
              </span>
            </div>

            <h3
              class="text-[17px] sm:text-[18px] font-bold leading-[1.35] text-[#092F50] group-hover:text-[#0869B2] transition-colors mb-2.5"
            >
              {{ t(need.titleKey) }}
            </h3>

            <p class="text-[14px] leading-[1.65] text-[#52687B]">
              {{ t(need.bodyKey) }}
            </p>
          </div>
        </a>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();

const needs = [
  {
    number: "01",
    titleKey: "services.need.n1Title",
    bodyKey: "services.need.n1Body",
    href: "#tax",
  },
  {
    number: "02",
    titleKey: "services.need.n2Title",
    bodyKey: "services.need.n2Body",
    href: "#payroll",
  },
  {
    number: "03",
    titleKey: "services.need.n3Title",
    bodyKey: "services.need.n3Body",
    href: "#accounting",
  },
  {
    number: "04",
    titleKey: "services.need.n4Title",
    bodyKey: "services.need.n4Body",
    href: "#tax",
  },
];
</script>
