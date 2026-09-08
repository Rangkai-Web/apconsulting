<template>
  <section class="px-4 py-16 md:px-8 md:py-24">
    <div class="mx-auto max-w-3xl text-center">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">
        {{ tag || t('home.ourApproach.tag') }}
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span class="font-sans font-normal text-[#083653]">{{
          titlePrefix || t('home.ourApproach.titlePrefix')
        }}</span>
        <span class="font-sans font-semibold italic text-[#083653]"
          >{{ titleHighlight || t('home.ourApproach.titleHighlight') }}
        </span>
        <span class="block font-sans font-normal text-[#083653]">{{
          titleSuffix || t('home.ourApproach.titleSuffix')
        }}</span>
      </h2>
      <p
        class="mt-4 max-w-xl mx-auto text-sm text-neutral-500 leading-relaxed md:text-base"
      >
        {{ subtitle || t('home.ourApproach.subtitle') }}
      </p>
    </div>

    <div
      class="mx-auto mt-12 grid max-w-6xl gap-6 md:grid-cols-2 items-stretch"
    >
      <div
        v-for="step in stepList"
        :key="step.titleKey"
        class="flex flex-col items-center justify-center p-8 md:p-[42px] border border-[#0000001F] rounded-none shadow-xs text-center"
        style="
          background: radial-gradient(
            41% 81.3% at 50% 100%,
            #e9edff 0%,
            #ffffff 100%
          );
        "
      >
        <div class="flex justify-center items-center">
          <img
            :src="step.icon"
            :alt="t(step.titleKey)"
            class="h-20 w-auto object-contain mx-auto"
          />
        </div>
        <div class="mt-6 flex flex-col gap-2 items-center text-center">
          <h3
            class="text-xl md:text-[22px] font-bold text-[#083653] leading-snug"
          >
            {{ t(step.titleKey) }}
          </h3>
          <p
            class="text-sm text-neutral-500 leading-relaxed font-normal max-w-md mx-auto"
          >
            {{ t(step.bodyKey) }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useLanguage } from '~/composables/useLanguage'

const { t } = useLanguage()

defineProps<{
  tag?: string;
  titlePrefix?: string;
  titleHighlight?: string;
  titleSuffix?: string;
  subtitle?: string;
}>()

const stepList = [
  {
    icon: "/images/icon/Understand_Icon.svg",
    titleKey: "home.ourApproach.step1Title",
    bodyKey: "home.ourApproach.step1Body",
  },
  {
    icon: "/images/icon/Identify_Icon.svg",
    titleKey: "home.ourApproach.step2Title",
    bodyKey: "home.ourApproach.step2Body",
  },
  {
    icon: "/images/icon/Solutions_Icon.svg",
    titleKey: "home.ourApproach.step3Title",
    bodyKey: "home.ourApproach.step3Body",
  },
  {
    icon: "/images/icon/Review_Icon.svg",
    titleKey: "home.ourApproach.step4Title",
    bodyKey: "home.ourApproach.step4Body",
  },
];
</script>
