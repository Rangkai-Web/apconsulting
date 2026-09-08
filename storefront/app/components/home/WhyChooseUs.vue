<template>
  <section class="relative px-4 py-16 md:px-8 md:py-24 overflow-hidden">
    <!-- Yellow Gradient Glow matching exact Figma specs (285x285, #F4C400, blur, opacity) -->
    <div
      aria-hidden="true"
      class="pointer-events-none absolute top-10 left-1/2 -translate-x-1/2 md:top-[57px] md:left-[513px] md:translate-x-0 z-0 h-[285px] w-[285px] hidden sm:block rounded-full bg-[#F4C400] opacity-30 blur-[75px]"
    />

    <div
      class="relative z-10 mx-auto grid max-w-7xl gap-8 md:grid-cols-2 md:gap-12 md:items-stretch"
    >
      <!-- Left Column: Header + Tax Advisory Card -->
      <div class="flex flex-col gap-6 md:gap-8">
        <div>
          <p
            class="text-sm font-semibold uppercase tracking-wide text-neutral-400"
          >
            {{ t('home.whyChooseUs.tag') }}
          </p>
          <h2
            class="mt-4 text-3xl font-normal leading-[1.2] text-[#083653] sm:text-4xl md:text-[48px] tracking-normal"
          >
            <span class="font-sans font-semibold italic text-[#083653]"
              >{{ t('home.whyChooseUs.titleItalic') }}</span
            >
            <span class="block font-sans font-normal text-[#083653]"
              >{{ t('home.whyChooseUs.titleNormal') }}</span
            >
          </h2>
          <p
            class="mt-4 text-sm text-neutral-500 leading-relaxed md:text-base max-w-lg"
          >
            {{ t('home.whyChooseUs.description') }}
          </p>
        </div>

        <!-- Tax Advisory Card (Underneath description on Left) -->
        <div
          class="relative overflow-hidden shadow-sm h-[320px] sm:h-[360px] md:h-[380px] w-full"
        >
          <picture class="block w-full h-full">
            <source
              media="(min-width: 768px)"
              srcset="/images/homepage/TaxAdvisory_Desktop.png"
            />
            <img
              src="/images/homepage/TaxAdvisory_Desktop.png"
              alt="Tax Advisory"
              class="w-full h-full grayscale object-cover"
            />
          </picture>

          <!-- White Overlay Box (Top-Left inside Image) -->
          <div
            class="absolute top-4 left-4 right-4 sm:right-auto sm:max-w-xs md:max-w-sm bg-white p-5 shadow-md shadow-black/5 border border-neutral-100/80 sm:top-6 sm:left-6"
          >
            <div class="flex items-center gap-2.5">
              <img
                src="/images/icon/TaxAdvisory_Icon.svg"
                alt=""
                class="size-6 shrink-0"
              />
              <p class="font-bold text-secondary-700 text-base">{{ t('home.whyChooseUs.card1Title') }}</p>
            </div>
            <p
              class="mt-2 text-xs text-neutral-500 leading-relaxed font-normal"
            >
              {{ t('home.whyChooseUs.card1Body') }}
            </p>
          </div>
        </div>
      </div>

      <!-- Right Column: Finance & Consulting Card (Tall Column) -->
      <div
        class="relative overflow-hidden shadow-sm h-[420px] sm:h-[480px] md:h-[715px] w-full"
      >
        <picture class="block w-full h-full">
          <source
            media="(min-width: 768px)"
            srcset="/images/homepage/Finance&Consulting_Desktop.png"
          />
          <img
            src="/images/homepage/Finance&Consulting_Desktop.png"
            alt="Finance & Consulting"
            class="w-full h-full grayscale object-cover"
          />
        </picture>

        <!-- White Overlay Box (Top-Left inside Image) -->
        <div
          class="absolute top-4 left-4 right-4 sm:right-auto sm:max-w-xs md:max-w-sm bg-white p-5 shadow-md shadow-black/5 border border-neutral-100/80 sm:top-6 sm:left-6"
        >
          <div class="flex items-center gap-2.5">
            <img
              src="/images/icon/FinanceConsult_Icon.svg"
              alt=""
              class="size-6 shrink-0"
            />
            <p class="font-bold text-secondary-700 text-base">
              {{ t('home.whyChooseUs.card2Title') }}
            </p>
          </div>
          <p class="mt-2 text-xs text-neutral-500 leading-relaxed font-normal">
            {{ t('home.whyChooseUs.card2Body') }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useLanguage } from '~/composables/useLanguage'

const { t } = useLanguage()
</script>
