<template>
  <section class="px-4 py-16 md:px-8 md:py-24">
    <div
      class="mx-auto max-w-7xl grid gap-8 md:grid-cols-2 md:items-center md:gap-12"
    >
      <div>
        <p
          class="text-sm font-semibold uppercase tracking-wide text-neutral-400"
        >
          {{ t("home.howWeHelp.tag") }}
        </p>
        <h2
          class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
        >
          <span class="font-sans font-semibold italic text-[#083653] block">{{
            t("home.howWeHelp.titleItalic")
          }}</span>
          <span class="font-sans font-normal text-[#083653] block">{{
            t("home.howWeHelp.titleNormal")
          }}</span>
        </h2>
        <p
          class="mt-4 max-w-xl text-sm text-neutral-500 leading-relaxed md:text-base"
        >
          {{ t("home.howWeHelp.description") }}
        </p>
      </div>

      <picture class="block w-full overflow-hidden">
        <source
          media="(min-width: 768px)"
          srcset="/images/homepage/HowWeHelp_Desktop.png"
        />
        <img
          src="/images/homepage/HowWeHelp_Mobile.png"
          alt="PCG team"
          class="w-full object-cover shadow-sm"
        />
      </picture>
    </div>

    <div
      class="mx-auto mt-12 grid max-w-7xl gap-6 md:grid-cols-3 items-stretch"
    >
      <div
        v-for="card in cards"
        :key="card.titleKey"
        class="flex flex-col p-6 sm:p-8 md:p-[42px] min-h-[360px] border border-[#0000001F] rounded-none shadow-xs transition-shadow"
        style="
          background: radial-gradient(
            49.65% 56.63% at 97.41% 3.65%,
            #e9edff 0%,
            #ffffff 100%
          );
        "
      >
        <div>
          <img
            :src="card.icon"
            :alt="t(card.titleKey)"
            class="h-24 w-auto object-contain"
          />
        </div>
        <div class="mt-8 flex flex-col justify-start">
          <div class="min-h-[56px] flex items-start">
            <h3
              class="text-xl md:text-[22px] font-semibold text-secondary-700 leading-snug"
            >
              {{ t(card.titleKey) }}
            </h3>
          </div>
          <p class="mt-3 text-sm text-neutral-500 leading-relaxed font-normal">
            {{ t(card.bodyKey) }}
          </p>
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
    icon: "/images/icon/Compliance_Icon.svg",
    titleKey: "home.howWeHelp.card1Title",
    bodyKey: "home.howWeHelp.card1Body",
  },
  {
    icon: "/images/icon/Financial_Icon.svg",
    titleKey: "home.howWeHelp.card2Title",
    bodyKey: "home.howWeHelp.card2Body",
  },
  {
    icon: "/images/icon/Analysis_Icon.svg",
    titleKey: "home.howWeHelp.card3Title",
    bodyKey: "home.howWeHelp.card3Body",
  },
];
</script>
