<template>
  <section
    id="services-hero"
    class="relative min-h-[500px] md:min-h-[600px] flex items-center bg-[#083653] px-4 py-16 sm:px-8 md:px-16 md:py-20 overflow-hidden"
  >
    <picture class="absolute inset-0 size-full pointer-events-none">
      <source
        media="(min-width: 768px)"
        srcset="/images/services/Hero_Desktop.webp"
      />
      <img
        src="/images/services/Hero_Mobile.webp"
        alt="PCG Services"
        fetchpriority="high"
        decoding="async"
        width="1280"
        height="600"
        class="size-full object-cover"
      />
    </picture>

    <!-- Dark gradient overlay for optimal text contrast -->
    <div
      class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/35 to-transparent pointer-events-none"
    ></div>

    <div class="relative z-10 mx-auto max-w-7xl w-full">
      <div class="max-w-2xl text-white">
        <h1
          class="pt-24 text-3xl font-semibold text-white sm:text-4xl md:text-[52px] leading-[1.15] tracking-tight animate-hero-title"
        >
          {{ t("services.hero.title") }}
        </h1>
        <p
          class="mt-4 text-sm text-white/90 leading-relaxed md:text-base font-normal max-w-xl animate-hero-desc"
        >
          {{ t("services.hero.description") }}
        </p>
        <div
          class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4 animate-hero-cta"
        >
          <UButton
            to="#scope-of-support"
            color="primary"
            variant="solid"
            size="lg"
            class="w-fit justify-center font-medium px-7 py-4 rounded-none bg-[#FFBB00] text-[#083653] hover:bg-[#FFBB00]/90 transition-colors uppercase text-xs sm:text-sm tracking-wider"
          >
            {{ t("services.hero.discoverBtn") }}
          </UButton>
          <UButton
            to="/contact"
            variant="outline"
            size="lg"
            class="w-fit justify-center font-medium px-7 py-4 rounded-none border border-white text-white hover:bg-white/10 transition-colors uppercase text-xs sm:text-sm tracking-wider"
          >
            {{ t("services.hero.discussBtn") }}
          </UButton>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();
</script>
