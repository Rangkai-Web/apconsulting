<template>
  <section
    id="vision"
    class="relative bg-[#083653] px-4 py-16 text-white md:px-[32px] md:py-[80px] overflow-hidden"
  >
    <div class="mx-auto max-w-7xl relative z-10 flex flex-col gap-16 md:gap-20">
      <!-- Vision Block (Top Right aligned or wide) -->
      <div class="flex flex-col md:items-end md:text-right" data-reveal="up">
        <p
          class="text-lg font-semibold uppercase tracking-wider text-[#F4C400]"
        >
          {{ t("about.vision.tag") }}
        </p>
        <h2
          class="mt-4 max-w-3xl text-2xl font-normal text-white sm:text-3xl md:text-[36px] leading-[1.3]"
        >
          {{ t("about.vision.title") }}
        </h2>
      </div>

      <!-- Mission Block (Bottom Left aligned) -->
      <div class="max-w-3xl" data-reveal="up" data-reveal-delay="150">
        <p
          class="text-lg font-semibold uppercase tracking-wider text-[#F4C400]"
        >
          {{ t("about.mission.tag") }}
        </p>
        <h3
          class="mt-4 text-xl font-normal text-white sm:text-2xl md:text-[28px] leading-[1.3]"
        >
          {{ t("about.mission.title") }}
        </h3>

        <ul
          class="mt-8 space-y-4 text-sm md:text-base text-neutral-200 font-normal"
        >
          <li
            v-for="itemKey in missionItemKeys"
            :key="itemKey"
            class="flex items-center gap-3"
          >
            <img
              src="/images/about/akar-icons_circle-check-fill.svg"
              alt=""
              class="size-5 shrink-0"
            />
            <span>{{ t(itemKey) }}</span>
          </li>
        </ul>
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
