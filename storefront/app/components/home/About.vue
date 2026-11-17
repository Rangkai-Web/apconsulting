<template>
  <section id="about" class="bg-[#F4F9FE] py-12 sm:py-14 lg:py-16 border-b border-[#DCE8F5]">
    <div class="mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-10 lg:gap-14 items-center">
        <!-- Left Column: Clean Office Photograph -->
        <div class="lg:col-span-5">
          <div class="overflow-hidden rounded-tr-[40px] sm:rounded-tr-[48px] rounded-tl-[4px] rounded-b-[4px]">
            <img
              src="/images/redesign/office.webp"
              alt="AP Consulting office lounge in SUN Plaza, Jakarta"
              class="w-full h-[240px] sm:h-[320px] lg:h-[360px] object-cover"
              loading="lazy"
            />
          </div>
        </div>

        <!-- Right Column: Editorial Narrative -->
        <div class="lg:col-span-7 flex flex-col gap-3.5 sm:gap-4">
          <p class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#0869B2]">
            {{ t("home.v3.about.eyebrow") }}
          </p>

          <h2
            class="text-2xl sm:text-4xl font-semibold leading-[1.18] tracking-[-0.03em] text-[#092F50]"
          >
            <span class="font-bold">{{ t("home.v3.about.titleBold") }}</span> {{ t("home.v3.about.titleMiddle") }}
            <span class="font-bold italic text-[#0869B2]">{{ t("home.v3.about.titleItalic") }}</span>
          </h2>

          <p class="text-base sm:text-[17px] text-[#092F50] leading-[1.65]">
            {{ t("home.v3.about.p1") }}
          </p>

          <p class="text-sm sm:text-[15px] text-[#52687B] leading-[1.65]">
            {{ t("home.v3.about.p2") }}
          </p>

          <hr class="border-[#DCE6EF] my-1.5" />

          <div>
            <NuxtLink
              to="/about"
              class="group inline-flex items-center gap-1.5 text-sm font-bold text-[#0869B2] hover:text-[#06477D] transition-colors"
            >
              <span>{{ t("home.v3.about.cta") }}</span>
              <span class="transition-transform group-hover:translate-x-1">→</span>
            </NuxtLink>
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
