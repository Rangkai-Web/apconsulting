<template>
  <section
    ref="sectionRef"
    class="bg-[#083653] px-4 py-16 text-white md:px-[32px] md:py-[80px]"
  >
    <div class="mx-auto max-w-7xl md:flex md:items-end md:justify-between">
      <div class="max-w-2xl" data-reveal="up">
        <p
          class="text-lg font-semibold uppercase tracking-wider text-[#F4C400]"
        >
          {{ t("home.stats.tag") }}
        </p>
        <h2
          class="mt-4 text-3xl font-normal text-white sm:text-4xl md:text-[48px] leading-[1.2]"
        >
          <span class="font-sans font-semibold italic text-white"
            >{{ t("home.stats.titleItalic") }}
          </span>
          <span class="font-sans font-normal text-white">{{
            t("home.stats.titleNormal")
          }}</span>
        </h2>
      </div>
      <p
        class="mt-4 max-w-sm text-sm text-neutral-300 leading-relaxed md:mt-0 md:text-right font-normal"
        data-reveal="up"
        data-reveal-delay="100"
      >
        {{ t("home.stats.description") }}
      </p>
    </div>

    <div
      class="mx-auto mt-12 grid max-w-7xl grid-cols-1 gap-8 md:grid-cols-4 md:gap-y-0 md:divide-x md:divide-white/20"
    >
      <div
        v-for="(stat, idx) in stats"
        :key="stat.key"
        class="group flex flex-col items-center justify-center p-4 text-center transition-transform duration-300 hover:scale-105"
        data-reveal="up"
        :data-reveal-delay="idx * 120"
      >
        <p class="text-4xl font-extrabold text-white md:text-5xl tabular-nums tracking-tight transition-colors duration-300 group-hover:text-[#FFBB00]">
          {{ displayValues[idx] }}
        </p>
        <p class="mt-2 text-sm text-neutral-300 leading-normal font-normal">
          {{ t(stat.key) }}
        </p>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from "vue";
import { useLanguage } from "~/composables/useLanguage";
import { useCountUp } from "~/composables/useCountUp";

const { t } = useLanguage();

const stats = [
  { target: 4, suffix: "", key: "home.stats.stat1" },
  { target: 4, suffix: "", key: "home.stats.stat2" },
  { target: 20, suffix: "", key: "home.stats.stat3" },
  { target: 200, suffix: "+", key: "home.stats.stat4" },
];

const { displayValues, start } = useCountUp(stats, { duration: 1600 });
const sectionRef = ref<HTMLElement | null>(null);

let observer: IntersectionObserver | null = null;

onMounted(() => {
  if (typeof window === "undefined" || !("IntersectionObserver" in window)) {
    start();
    return;
  }

  if (sectionRef.value) {
    observer = new IntersectionObserver(
      (entries) => {
        for (const entry of entries) {
          if (entry.isIntersecting) {
            start();
            observer?.disconnect();
            break;
          }
        }
      },
      {
        rootMargin: "0px 0px -40px 0px",
        threshold: 0.15,
      }
    );
    observer.observe(sectionRef.value);
  } else {
    start();
  }
});

onBeforeUnmount(() => {
  observer?.disconnect();
});
</script>
