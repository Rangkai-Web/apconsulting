<template>
  <section
    id="vision"
    class="relative overflow-hidden bg-[#072B4A] text-white py-12 sm:py-16 lg:py-20"
  >
    <!-- Background Image & Blue Gradient Overlay -->
    <img
      src="/images/redesign/values-bg.png"
      alt=""
      aria-hidden="true"
      class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none"
      loading="lazy"
    />
    <div
      class="absolute inset-0 bg-gradient-to-r from-[#062440]/96 via-[#083B66]/92 to-[#0869B2]/86 pointer-events-none"
    />

    <div class="relative z-10 mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <div
        class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start"
      >
        <!-- Left Column: Vision -->
        <div
          class="lg:col-span-5 pb-8 lg:pb-0 border-b lg:border-b-0 lg:border-r border-white/15 lg:pr-10"
        >
          <p
            class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#8EC5F2] mb-3"
          >
            {{ t("about.vision.tag") }}
          </p>
          <h2
            class="text-2xl sm:text-[28px] lg:text-[32px] font-semibold leading-[1.3] tracking-[-0.02em] text-white"
          >
            {{ t("about.vision.title") }}
          </h2>
        </div>

        <!-- Right Column: Mission & Checklist -->
        <div class="lg:col-span-7">
          <p
            class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#8EC5F2] mb-3"
          >
            {{ t("about.mission.tag") }}
          </p>
          <h3
            class="text-lg sm:text-2xl font-medium leading-[1.4] text-[#E1EFFA] mb-6 sm:mb-7"
          >
            {{ t("about.mission.title") }}
          </h3>

          <ul class="space-y-3.5 text-sm sm:text-[15px] text-[#D8E5F0]">
            <li
              v-for="itemKey in missionItemKeys"
              :key="itemKey"
              class="flex items-start gap-3"
            >
              <span
                class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-[#0869B2] text-white"
              >
                <svg
                  aria-hidden="true"
                  width="12"
                  height="12"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M20 6L9 17l-5-5" />
                </svg>
              </span>
              <span class="leading-[1.6]">{{ t(itemKey) }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();

const missionItemKeys = [
  "about.mission.item1",
  "about.mission.item2",
  "about.mission.item3",
  "about.mission.item4",
  "about.mission.item5",
];
</script>
