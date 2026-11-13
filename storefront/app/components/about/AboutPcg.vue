<template>
  <section
    id="about-ap"
    class="scroll-mt-20 bg-white py-12 sm:py-16 lg:py-20 border-b border-[#DCE8F5]"
  >
    <div class="mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <!-- Top Header -->
      <div class="max-w-[760px] mb-8 sm:mb-10" data-reveal="up">
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
        data-reveal="up"
        data-reveal-delay="100"
      >
        <p class="text-[#092F50] font-medium">
          {{ t("about.pcg.paragraph1") }}
        </p>
        <p>
          {{ t("about.pcg.paragraph2") }}
        </p>
      </div>

      <!-- Key Credentials / Numbers Strip (Count-Up Numbers) -->
      <dl
        ref="statsContainerRef"
        class="mt-10 sm:mt-12 pt-8 sm:pt-10 border-t border-[#DCE8F5] grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8"
        data-reveal="up"
        data-reveal-delay="150"
      >
        <div
          v-for="(stat, idx) in statConfigs"
          :key="stat.labelKey"
          class="flex flex-col gap-1.5"
        >
          <dd
            class="text-3xl sm:text-4xl font-bold leading-none tracking-[-0.02em] text-[#0869B2] tabular-nums"
          >
            {{ displayValues[idx] }}
          </dd>
          <dt class="text-xs sm:text-sm leading-[1.5] text-[#52687B]">
            {{ t(stat.labelKey) }}
          </dt>
        </div>
      </dl>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from "vue";
import { useLanguage } from "~/composables/useLanguage";
import { useCountUp } from "~/composables/useCountUp";

const { t } = useLanguage();

const statConfigs = [
  { target: 15, suffix: "+", labelKey: "home.v3.hero.stat1Label" },
  { target: 50, suffix: "+", labelKey: "home.v3.hero.stat2Label" },
  { target: 300, suffix: "+", labelKey: "home.v3.hero.stat3Label" },
  { target: 8, suffix: "", labelKey: "home.v3.hero.stat4Label" },
];

const { displayValues, start } = useCountUp(statConfigs, { duration: 1600 });
const statsContainerRef = ref<HTMLElement | null>(null);
let observer: IntersectionObserver | null = null;

onMounted(() => {
  if (typeof window === "undefined" || !("IntersectionObserver" in window)) {
    start();
    return;
  }
  if (statsContainerRef.value) {
    observer = new IntersectionObserver(
      (entries) => {
        if (entries[0]?.isIntersecting) {
          start();
          observer?.disconnect();
          observer = null;
        }
      },
      { rootMargin: "0px 0px -20px 0px", threshold: 0.1 }
    );
    observer.observe(statsContainerRef.value);
  } else {
    start();
  }
});

onBeforeUnmount(() => {
  observer?.disconnect();
});
</script>
