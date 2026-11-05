<template>
  <section
    class="relative overflow-hidden bg-white px-4 pb-12 pt-28 md:px-8 md:pb-24 md:pt-[140px] -mt-20 md:-mt-[92px]"
  >
    <img
      src="/images/homepage/Globe_Desktop.webp"
      alt=""
      aria-hidden="true"
      loading="lazy"
      decoding="async"
      width="900"
      height="858"
      class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-60"
    />

    <div
      class="relative mx-auto grid max-w-7xl items-center gap-10 md:grid-cols-2 md:gap-12"
    >
      <div>
        <!-- Typewriter Animated Tagline Badge -->
        <div
          class="mb-4 inline-flex items-center gap-2.5 max-w-full border border-secondary-100 bg-[#F5FBFF] px-3.5 py-1.5 text-xs font-semibold text-secondary-700 sm:text-sm tracking-wide shadow-xs hover:border-primary-500/40 transition-colors"
          data-reveal="fade"
        >
          <span class="relative flex size-2 shrink-0">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-primary-500 opacity-75"></span>
            <span class="relative inline-flex size-2 rounded-full bg-primary-500"></span>
          </span>
          <span class="min-h-[1.5rem] inline-flex items-center">
            <span>{{ typewriterText }}</span>
            <span class="ml-1 inline-block h-3.5 w-[2px] bg-primary-500 animate-pulse"></span>
          </span>
        </div>

        <h1
          class="text-3xl font-bold uppercase text-secondary-700 sm:text-4xl md:text-[52px] leading-[1.2] tracking-[-0.02em]"
          data-reveal="up"
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
          data-reveal="up"
          data-reveal-delay="100"
        >
          {{ t("home.hero.description") }}
        </p>
        <div
          class="mt-6 flex flex-col gap-3 sm:flex-row md:mt-8 md:gap-4"
          data-reveal="up"
          data-reveal-delay="200"
        >
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

      <div
        class="flex items-end justify-center"
        data-reveal="scale"
        data-reveal-delay="150"
      >
        <picture class="w-full">
          <source
            media="(min-width: 768px)"
            srcset="/images/homepage/HeroContent_Desktop.webp"
          />
          <img
            src="/images/homepage/HeroContent_Mobile.webp"
            alt="PCG — clearer finances, stronger business decisions"
            fetchpriority="high"
            loading="eager"
            decoding="async"
            width="600"
            height="500"
            class="w-full object-contain"
          />
        </picture>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useLanguage } from "~/composables/useLanguage";
import { useTypewriter } from "~/composables/useTypewriter";

const { t, currentLocale } = useLanguage();

const typewriterPhrases = computed(() => {
  return currentLocale.value === "en"
    ? [
        "Tax & Financial Consulting Services.",
        "Facing Complex Tax Regulations?",
        "Your Trusted Accounting & Bookkeeping Partner.",
        "We Are Here to Accelerate Your Growth.",
      ]
    : [
        "Jasa Konsultasi Perpajakan & Finansial.",
        "Butuh Pengelolaan Pajak yang Rapi?",
        "Mitra Akuntansi & Pembukuan Terpercaya.",
        "Kami Siap Membantu Bisnis Anda Berkembang.",
      ];
});

const { currentText: typewriterText } = useTypewriter(typewriterPhrases, {
  typeSpeed: 60,
  deleteSpeed: 30,
  pauseDuration: 2200,
});
</script>
