<template>
  <section
    class="relative overflow-hidden bg-white px-4 pb-12 pt-28 md:px-8 md:pb-24 md:pt-[140px] -mt-20 md:-mt-[92px]"
  >
    <img
      src="/images/homepage/Globe_Desktop.png"
      alt=""
      aria-hidden="true"
      class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-60"
    />

    <div
      class="relative mx-auto grid max-w-7xl items-center gap-10 md:grid-cols-2 md:gap-12"
    >
      <div>
        <h1
          class="text-3xl font-bold uppercase text-secondary-700 sm:text-4xl md:text-[52px] leading-[1.2] tracking-[-0.02em]"
        >
          {{ t("home.hero.title1") }}
          <span
            class="font-serif font-semibold italic text-primary-500 text-3xl sm:text-4xl md:text-[52px] leading-[1.2] tracking-[-0.02em]"
            >{{ t("home.hero.titleHighlight") }}</span
          >
          {{ t("home.hero.title2") }}
        </h1>
        <p
          class="mt-4 max-w-md text-sm text-neutral-500 leading-relaxed md:mt-6 md:text-base"
        >
          {{ t("home.hero.description") }}
        </p>
        <div class="mt-6 flex flex-col gap-3 sm:flex-row md:mt-8 md:gap-4">
          <UButton
            to="/services"
            color="secondary"
            variant="outline"
            size="lg"
            class="w-fit sm:w-auto justify-center font-medium px-8 py-4 rounded-none border border-secondary-700 text-secondary-700 hover:bg-secondary-50 transition-colors"
          >
            {{ t("home.hero.exploreBtn") }}
          </UButton>
          <UButton
            to="/contact"
            color="primary"
            variant="solid"
            size="lg"
            class="w-fit sm:w-auto justify-center font-medium px-8 py-4 rounded-none bg-primary-500 text-secondary-700 hover:bg-primary-600 transition-colors"
          >
            {{ t("home.hero.discussBtn") }}
          </UButton>
        </div>
      </div>

      <div class="flex items-end justify-center">
        <picture class="w-full">
          <source
            media="(min-width: 768px)"
            srcset="/images/homepage/HeroContent_Desktop.png"
          />
          <img
            src="/images/homepage/HeroContent_Mobile.png"
            alt="PCG — clearer finances, stronger business decisions"
            class="w-full object-contain"
          />
        </picture>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();
</script>
