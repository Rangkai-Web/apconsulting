<template>
  <section
    id="why-choose"
    class="scroll-mt-20 bg-[#F4F9FE] py-12 sm:py-16 lg:py-20 border-b border-[#DCE8F5]"
  >
    <div class="mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <div
        class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start"
      >
        <!-- Left Column: Headline & CTA -->
        <div class="lg:col-span-5" data-reveal="up">
          <p
            class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#0869B2] mb-2"
          >
            {{ t("about.whyChoose.tag") }}
          </p>
          <h2
            class="text-2xl sm:text-4xl font-semibold leading-[1.18] tracking-[-0.03em] text-[#092F50] mb-4"
          >
            <span class="font-bold">{{ t("about.whyChoose.titleNormal") }}</span>
            {{ " " }}
            <span class="font-bold italic text-[#0869B2]">{{
              t("about.whyChoose.titleItalic")
            }}</span>
          </h2>
          <p
            class="text-sm sm:text-[15.5px] leading-[1.65] text-[#52687B] mb-7 max-w-md"
          >
            {{ t("about.whyChoose.description") }}
          </p>

          <NuxtLink
            to="/contact"
            class="group inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-[2px] bg-[#0869B2] hover:bg-[#075997] text-white text-sm font-bold transition-colors shadow-xs"
          >
            <span>{{ t("about.whyChoose.ctaButton") }}</span>
            <span
              class="text-base leading-none transition-transform duration-200 group-hover:translate-x-1"
            >
              →
            </span>
          </NuxtLink>
        </div>

        <!-- Right Column: 2x2 Architectural Advantage Cards -->
        <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
          <div
            v-for="(card, cIdx) in cards"
            :key="card.titleKey"
            class="hover-lift flex flex-col justify-between p-6 sm:p-7 rounded-[2px] bg-white border border-[#DCE8F5] hover:border-[#0869B2] transition-colors duration-200"
            data-reveal="up"
            :data-reveal-delay="cIdx * 100"
          >
            <div>
              <div
                class="mb-5 flex size-11 items-center justify-center rounded-[2px] bg-[#F0F7FF] border border-[#DCE8F5] text-[#0869B2]"
              >
                <UIcon :name="card.icon" class="size-5" />
              </div>

              <h3
                class="text-[17px] sm:text-[18px] font-bold text-[#092F50] leading-snug mb-2"
              >
                {{ t(card.titleKey) }}
              </h3>

              <p class="text-[13.5px] sm:text-[14px] text-[#52687B] leading-[1.65]">
                {{ t(card.bodyKey) }}
              </p>
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

const cards = [
  {
    icon: "i-heroicons-shield-check",
    titleKey: "about.whyChoose.card1Title",
    bodyKey: "about.whyChoose.card1Body",
  },
  {
    icon: "i-heroicons-academic-cap",
    titleKey: "about.whyChoose.card2Title",
    bodyKey: "about.whyChoose.card2Body",
  },
  {
    icon: "i-heroicons-lock-closed",
    titleKey: "about.whyChoose.card3Title",
    bodyKey: "about.whyChoose.card3Body",
  },
  {
    icon: "i-heroicons-chart-bar-square",
    titleKey: "about.whyChoose.card4Title",
    bodyKey: "about.whyChoose.card4Body",
  },
];
</script>
