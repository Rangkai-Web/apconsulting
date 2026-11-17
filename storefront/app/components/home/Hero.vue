<template>
  <section class="relative overflow-hidden bg-[#072642] text-white">
    <!-- Background Image & Crisp Blue-Navy Gradient overlay -->
    <img
      src="/images/redesign/hero-bg.webp"
      alt=""
      aria-hidden="true"
      class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none"
      loading="eager"
    />
    <div
      class="absolute inset-0 bg-gradient-to-r from-[#06233D]/96 via-[#083A63]/90 to-[#0869B2]/75 pointer-events-none"
    />

    <div class="relative z-10 mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-20">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-10 lg:gap-12 items-center">
        <!-- Left Column: Copy & Primary Message -->
        <div class="lg:col-span-7 flex flex-col items-start gap-5 sm:gap-6">
          <!-- Eyebrow -->
          <p class="animate-hero-badge text-[11px] font-bold tracking-[0.14em] uppercase text-[#B9DBF4]">
            {{ t("home.v3.hero.eyebrow") }}
          </p>

          <!-- H1 Headline -->
          <h1
            class="animate-hero-title text-[34px] sm:text-5xl lg:text-[62px] font-semibold leading-[1.1] tracking-[-0.03em] text-white"
          >
            <span class="font-bold">{{ t("home.v3.hero.titleBold") }}</span><br />
            {{ t("home.v3.hero.titleMiddle") }}<br />
            <span class="font-bold italic text-[#B9E2FF]">{{ t("home.v3.hero.titleItalic") }}</span>
          </h1>

          <!-- Subtitle -->
          <p class="animate-hero-desc text-[15px] sm:text-lg text-[#E1EFFA] leading-[1.6] max-w-[500px]">
            {{ t("home.v3.hero.subtitle") }}
          </p>

          <!-- CTA Button -->
          <div class="animate-hero-cta pt-1 w-full sm:w-auto">
            <NuxtLink
              to="/contact"
              class="inline-flex w-full sm:w-auto items-center justify-center gap-2 px-6 py-3.5 bg-white text-[#083A63] text-sm font-bold rounded-[2px] shadow-sm hover:bg-[#F0F7FF] transition-colors"
            >
              <span>{{ t("home.v3.hero.cta") }}</span>
              <span class="text-base leading-none text-[#0869B2]">→</span>
            </NuxtLink>
          </div>
        </div>

        <!-- Right Column: Editorial Photo Composition -->
        <div class="animate-hero-image lg:col-span-5 flex flex-col">
          <div class="relative overflow-hidden rounded-tr-[40px] sm:rounded-tr-[56px] rounded-tl-[4px] rounded-b-[4px] shadow-2xl">
            <img
              src="/images/redesign/collaboration.webp"
              alt="AP Consulting team working together at laptops"
              class="w-full h-[240px] sm:h-[340px] lg:h-[380px] object-cover"
              loading="eager"
            />
            <!-- Photo Caption Banner -->
            <div
              class="flex items-center justify-between px-5 sm:px-6 py-3.5 sm:py-4 bg-gradient-to-r from-[#0869B2] to-[#06477D] text-white"
            >
              <span class="text-[10px] sm:text-[11px] font-bold tracking-[0.14em] uppercase">
                {{ t("home.v3.hero.banner") }}
              </span>
              <span class="text-lg sm:text-xl font-light leading-none">↗</span>
            </div>
          </div>
        </div>
      </div>

      <!-- AP Consulting in Numbers (Stats Bar with Count-Up) -->
      <dl
        ref="statsContainerRef"
        aria-label="AP Consulting in numbers"
        class="mt-10 sm:mt-12 lg:mt-16 pt-8 lg:pt-10 border-t border-white/20 grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 lg:gap-10"
        data-reveal="up"
      >
        <div
          v-for="(stat, idx) in statConfigs"
          :key="stat.labelKey"
          class="flex flex-col gap-1.5 sm:gap-2"
        >
          <dd class="text-3xl sm:text-4xl lg:text-[44px] font-bold leading-none tracking-[-0.02em] text-white tabular-nums">
            {{ displayValues[idx] }}
          </dd>
          <dt class="text-xs sm:text-sm leading-[1.45] text-[#D3E0EC]">
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
