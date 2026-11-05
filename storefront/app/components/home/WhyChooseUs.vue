<template>
  <section class="relative px-4 py-16 md:px-8 md:py-24 overflow-hidden">
    <!-- Yellow Gradient Glow matching exact Figma specs (285x285, #F4C400, blur, opacity) -->
    <div
      aria-hidden="true"
      class="pointer-events-none absolute top-10 left-1/2 -translate-x-1/2 md:top-[57px] md:left-[513px] md:translate-x-0 z-0 h-[285px] w-[285px] hidden sm:block rounded-full bg-[#F4C400] opacity-30 blur-[75px]"
    />

    <div class="relative z-10 mx-auto max-w-7xl">
      <!-- Section Header -->
      <div class="max-w-3xl" data-reveal="up">
        <p
          class="text-lg font-semibold uppercase tracking-wide text-neutral-400"
        >
          {{ t("home.whyChooseUs.tag") }}
        </p>
        <h2
          class="mt-4 text-3xl font-normal leading-[1.2] text-[#083653] sm:text-4xl md:text-[48px] tracking-normal"
        >
          <span class="font-sans font-semibold italic text-[#083653]">{{
            t("home.whyChooseUs.titleItalic")
          }}</span>
          <span class="block font-sans font-normal text-[#083653]">{{
            t("home.whyChooseUs.titleNormal")
          }}</span>
        </h2>
        <p
          class="mt-4 text-sm text-neutral-500 leading-relaxed md:text-base max-w-xl"
        >
          {{ t("home.whyChooseUs.description") }}
        </p>
      </div>

      <!-- 3 Items Grid -->
      <div
        class="mt-10 md:mt-12 grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 items-stretch"
      >
        <div
          v-for="(card, idx) in cards"
          :key="card.titleKey"
          class="group relative overflow-hidden shadow-sm h-[380px] sm:h-[420px] md:h-[480px] w-full hover-lift"
          data-reveal="up"
          :data-reveal-delay="idx * 150"
        >
          <picture class="block w-full h-full">
            <source media="(min-width: 768px)" :srcset="card.desktopImg" />
            <img
              :src="card.mobileImg"
              :alt="t(card.titleKey)"
              loading="lazy"
              decoding="async"
              width="600"
              height="480"
              class="w-full h-full grayscale object-cover transition-transform duration-500 group-hover:scale-105"
            />
          </picture>

          <!-- White Overlay Box (Top-Left inside Image) -->
          <div
            class="absolute top-4 left-4 right-4 bg-white p-5 shadow-md shadow-black/5 border border-neutral-100/80 sm:top-6 sm:left-6 sm:right-6"
          >
            <div class="flex items-center gap-2.5">
              <img
                :src="card.icon"
                alt=""
                loading="lazy"
                decoding="async"
                width="24"
                height="24"
                class="size-6 shrink-0"
              />
              <p class="font-bold text-secondary-700 text-base">
                {{ t(card.titleKey) }}
              </p>
            </div>
            <p
              class="mt-2 text-xs text-neutral-500 leading-relaxed font-normal"
            >
              {{ t(card.bodyKey) }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();

const cards = [
  {
    icon: "/images/icon/TaxAdvisory_Icon.svg",
    desktopImg: "/images/homepage/TaxAdvisory_Desktop.webp",
    mobileImg: "/images/homepage/TaxAdvisory_Mobile.webp",
    titleKey: "home.whyChooseUs.card1Title",
    bodyKey: "home.whyChooseUs.card1Body",
  },
  {
    icon: "/images/icon/FinanceConsult_Icon.svg",
    desktopImg: "/images/homepage/Finance&Consulting_Desktop.webp",
    mobileImg: "/images/homepage/Finance&Consulting_Mobile.webp",
    titleKey: "home.whyChooseUs.card2Title",
    bodyKey: "home.whyChooseUs.card2Body",
  },
  {
    icon: "/images/icon/Check_Icon.svg",
    desktopImg: "/images/homepage/HowWeHelp_Desktop.webp",
    mobileImg: "/images/homepage/HowWeHelp_Mobile.webp",
    titleKey: "home.whyChooseUs.card3Title",
    bodyKey: "home.whyChooseUs.card3Body",
  },
];
</script>
