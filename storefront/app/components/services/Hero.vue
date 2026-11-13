<template>
  <section
    id="services-hero"
    class="relative overflow-hidden bg-[#072642] text-white"
  >
    <!-- Background Image & Crisp Blue-Navy Gradient Overlay -->
    <img
      src="https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=1600&q=80"
      alt=""
      aria-hidden="true"
      class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none"
      loading="eager"
    />
    <div
      class="absolute inset-0 bg-gradient-to-r from-[#06233D]/96 via-[#083A63]/90 to-[#0869B2]/78 pointer-events-none"
    />

    <div
      class="relative z-10 mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-20"
    >
      <div
        class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-10 lg:gap-12 items-center"
      >
        <!-- Left Column: Primary Message & CTAs -->
        <div class="lg:col-span-7 flex flex-col items-start gap-4 sm:gap-5">
          <p
            class="animate-hero-badge text-[11px] font-bold tracking-[0.14em] uppercase text-[#B9DBF4]"
          >
            {{ t("services.hero.eyebrow") }}
          </p>

          <h1
            class="animate-hero-title text-[32px] sm:text-5xl lg:text-[56px] font-semibold leading-[1.12] tracking-[-0.03em] text-white"
          >
            <span class="font-bold">{{ t("services.hero.titleBold") }}</span><br />
            <span class="font-bold italic text-[#B9E2FF]">{{
              t("services.hero.titleItalic")
            }}</span>
          </h1>

          <p
            class="animate-hero-desc text-[15px] sm:text-lg text-[#E1EFFA] leading-[1.65] max-w-[540px]"
          >
            {{ t("services.hero.description") }}
          </p>

          <div
            class="animate-hero-cta pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-3.5 w-full sm:w-auto"
          >
            <a
              href="#scope-of-support"
              class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white text-[#083A63] text-sm font-bold rounded-[2px] shadow-sm hover:bg-[#F0F7FF] transition-colors"
            >
              <span>{{ t("services.hero.discoverBtn") }}</span>
              <span class="text-base leading-none text-[#0869B2]">↓</span>
            </a>

            <NuxtLink
              to="/contact"
              class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white/10 hover:bg-white/15 border border-white/35 text-white text-sm font-semibold rounded-[2px] transition-colors"
            >
              <span>{{ t("services.hero.discussBtn") }}</span>
              <span class="text-base leading-none">→</span>
            </NuxtLink>
          </div>
        </div>

        <!-- Right Column: Quick Service Directory Card (Inspired by AP Consulting Service Navigation) -->
        <div class="animate-hero-image lg:col-span-5">
          <div
            class="rounded-[2px] bg-[#061F36]/80 backdrop-blur-xs border border-white/15 overflow-hidden shadow-2xl"
          >
            <div
              class="px-5 sm:px-6 py-3.5 bg-gradient-to-r from-[#0869B2] to-[#06477D] border-b border-white/15 flex items-center justify-between"
            >
              <span
                class="text-[10px] sm:text-[11px] font-bold tracking-[0.12em] uppercase text-white"
              >
                {{ t("services.hero.subtitleBanner") }}
              </span>
            </div>

            <div class="divide-y divide-white/10">
              <a
                v-for="item in quickLinks"
                :key="item.index"
                :href="item.href"
                class="group flex items-center justify-between gap-4 px-5 sm:px-6 py-4 sm:py-4.5 hover:bg-white/8 transition-colors"
              >
                <div class="flex items-center gap-3.5">
                  <span
                    class="text-xs font-bold tracking-[0.14em] text-[#8EC5F2]"
                  >
                    {{ item.index }}
                  </span>
                  <span
                    class="text-sm sm:text-[15px] font-semibold text-white group-hover:text-[#B9E2FF] transition-colors"
                  >
                    {{ item.title }}
                  </span>
                </div>
                <span
                  class="text-sm text-[#8EC5F2] transition-transform duration-200 group-hover:translate-x-1"
                >
                  →
                </span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();

const quickLinks = computed(() => [
  {
    index: "01",
    title: t("home.v3.services.s1Title"),
    href: "#tax",
  },
  {
    index: "02",
    title: t("home.v3.services.s2Title"),
    href: "#payroll",
  },
  {
    index: "03",
    title: t("home.v3.services.s3Title"),
    href: "#accounting",
  },
]);
</script>
