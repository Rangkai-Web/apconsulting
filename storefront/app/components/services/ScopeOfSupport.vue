<template>
  <section id="scope-of-support" class="px-4 py-16 md:px-8 md:py-24 bg-white">
    <div class="mx-auto max-w-4xl text-center">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">
        {{ t('services.scope.tag') }}
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span class="font-sans font-bold italic text-[#083653]"
          >{{ t('services.scope.titleItalic') }}
        </span>
        <span class="font-sans font-normal text-[#083653]"
          >{{ t('services.scope.titleNormal') }}</span
        >
      </h2>
      <p
        class="mt-4 max-w-2xl mx-auto text-sm text-neutral-500 leading-relaxed md:text-base"
      >
        {{ t('services.scope.description') }}
      </p>
    </div>

    <div class="mx-auto mt-12 max-w-6xl space-y-6">
      <div
        v-for="service in services"
        :key="service.number"
        class="p-6 md:p-[42px] border border-[#0000001F] rounded-none shadow-xs grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-center"
        style="
          background: radial-gradient(
            49.65% 56.63% at 97.41% 3.65%,
            #fffae3 0%,
            #ffffff 100%
          );
        "
      >
        <!-- Left Side: Service Info -->
        <div class="lg:col-span-7 flex items-start gap-4 md:gap-6">
          <span
            class="text-3xl md:text-[44px] font-semibold italic text-[#083653] leading-none shrink-0 pt-1"
          >
            {{ service.number }}
          </span>
          <div class="flex flex-col items-start">
            <h3
              class="text-xl md:text-[24px] font-semibold text-[#083653] leading-snug"
            >
              {{ t(service.titleKey) }}
            </h3>
            <p
              class="mt-2 text-sm md:text-base text-neutral-500 leading-relaxed font-normal max-w-lg"
            >
              {{ t(service.descKey) }}
            </p>
            <NuxtLink
              to="/contact"
              class="mt-4 inline-flex items-center text-xs md:text-sm font-semibold uppercase tracking-wider text-[#083653] hover:text-[#083653]/80 group"
            >
              <span>{{ t(service.ctaKey) }}</span>
              <UIcon
                name="i-heroicons-arrow-right"
                class="ml-1.5 size-4 text-[#083653] transition-transform group-hover:translate-x-1"
              />
            </NuxtLink>
          </div>
        </div>

        <!-- Right Side: Features Checklist -->
        <div class="lg:col-span-5">
          <ul class="space-y-2.5">
            <li
              v-for="(featureKey, idx) in service.featureKeys"
              :key="idx"
              class="flex items-center gap-3 text-sm md:text-base font-normal text-neutral-600"
            >
              <img
                src="/images/services/akar-icons_circle-check-fill.svg"
                alt=""
                class="size-5 shrink-0"
              />
              <span>{{ t(featureKey) }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useLanguage } from '~/composables/useLanguage'

const { t } = useLanguage()

const services = [
  {
    number: "01",
    titleKey: "services.scope.s1Title",
    descKey: "services.scope.s1Desc",
    ctaKey: "services.scope.s1Cta",
    featureKeys: [
      "services.scope.s1F1",
      "services.scope.s1F2",
      "services.scope.s1F3",
      "services.scope.s1F4",
      "services.scope.s1F5",
    ],
  },
  {
    number: "02",
    titleKey: "services.scope.s2Title",
    descKey: "services.scope.s2Desc",
    ctaKey: "services.scope.s2Cta",
    featureKeys: [
      "services.scope.s2F1",
      "services.scope.s2F2",
      "services.scope.s2F3",
      "services.scope.s2F4",
      "services.scope.s2F5",
    ],
  },
  {
    number: "03",
    titleKey: "services.scope.s3Title",
    descKey: "services.scope.s3Desc",
    ctaKey: "services.scope.s3Cta",
    featureKeys: [
      "services.scope.s3F1",
      "services.scope.s3F2",
      "services.scope.s3F3",
      "services.scope.s3F4",
    ],
  },
  {
    number: "04",
    titleKey: "services.scope.s4Title",
    descKey: "services.scope.s4Desc",
    ctaKey: "services.scope.s4Cta",
    featureKeys: [
      "services.scope.s4F1",
      "services.scope.s4F2",
      "services.scope.s4F3",
      "services.scope.s4F4",
      "services.scope.s4F5",
    ],
  },
];
</script>
