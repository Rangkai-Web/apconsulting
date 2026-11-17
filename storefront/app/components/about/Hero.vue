<template>
  <section
    id="hero"
    class="relative overflow-hidden bg-[#072642] text-white"
  >
    <!-- Background Image & Crisp Blue-Navy Gradient Overlay -->
    <img
      src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=1600&q=80"
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
        <!-- Left Column: Primary Copy & Actions -->
        <div class="lg:col-span-7 flex flex-col items-start gap-4 sm:gap-5">
          <p
            class="animate-hero-badge text-[11px] font-bold tracking-[0.14em] uppercase text-[#B9DBF4]"
          >
            {{ t("about.hero.eyebrow") }}
          </p>

          <h1
            class="animate-hero-title text-[32px] sm:text-5xl lg:text-[54px] font-semibold leading-[1.12] tracking-[-0.03em] text-white"
          >
            <span class="font-bold">{{ t("about.hero.title1") }}</span><br />
            <span class="font-bold italic text-[#B9E2FF]">{{
              t("about.hero.title2")
            }}</span>
          </h1>

          <p
            class="animate-hero-desc text-[15px] sm:text-lg text-[#E1EFFA] leading-[1.65] max-w-[540px]"
          >
            {{ t("about.hero.description") }}
          </p>

          <div
            class="animate-hero-cta pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-3.5 w-full sm:w-auto"
          >
            <a
              href="#about-ap"
              class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white text-[#083A63] text-sm font-bold rounded-[2px] shadow-sm hover:bg-[#F0F7FF] transition-colors"
            >
              <span>{{ t("about.hero.discoverBtn") }}</span>
              <span class="text-base leading-none text-[#0869B2]">↓</span>
            </a>

            <a
              href="#people"
              class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white/10 hover:bg-white/15 border border-white/35 text-white text-sm font-semibold rounded-[2px] transition-colors"
            >
              <span>{{ t("about.hero.teamBtn") }}</span>
              <span class="text-base leading-none">→</span>
            </a>
          </div>
        </div>

        <!-- Right Column: Editorial Office Photo + Banner -->
        <div class="animate-hero-image lg:col-span-5">
          <div
            class="relative overflow-hidden rounded-tr-[40px] sm:rounded-tr-[56px] rounded-tl-[4px] rounded-b-[4px] shadow-2xl border border-white/15"
          >
            <img
              src="/images/redesign/office.webp"
              alt="AP Consulting office in SUN Plaza, Jakarta"
              class="w-full h-[230px] sm:h-[320px] lg:h-[350px] object-cover"
              loading="eager"
            />
            <div
              class="flex items-center justify-between px-5 sm:px-6 py-3.5 sm:py-4 bg-gradient-to-r from-[#0869B2] to-[#06477D] text-white"
            >
              <span
                class="text-[10px] sm:text-[11px] font-bold tracking-[0.14em] uppercase"
              >
                {{ t("about.hero.banner") }}
              </span>
              <span class="text-lg sm:text-xl font-light leading-none">↗</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();
</script>
