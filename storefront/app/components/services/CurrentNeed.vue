<template>
  <section class="px-4 py-16 md:px-8 md:py-24 bg-[#FBFEFF]">
    <div class="mx-auto max-w-3xl text-center">
      <p class="text-lg font-semibold uppercase tracking-wide text-neutral-400">
        {{ t("services.need.tag") }}
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span class="font-sans font-semibold italic text-[#083653]">{{
          t("services.need.titleItalic")
        }}</span>
      </h2>
      <p
        class="mt-4 max-w-xl mx-auto text-sm text-neutral-500 leading-relaxed md:text-base"
      >
        {{ t("services.need.subtitle") }}
      </p>
    </div>

    <div
      class="mx-auto mt-12 grid max-w-6xl gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 items-stretch"
    >
      <div
        v-for="need in needs"
        :key="need.number"
        class="flex flex-col p-6 md:p-8 border border-[#0000001F] rounded-none shadow-xs text-left justify-between"
        style="
          background: radial-gradient(
            41% 81.3% at 50% 100%,
            #e9edff 0%,
            #ffffff 100%
          );
        "
      >
        <div>
          <span
            class="text-2xl md:text-4xl font-semibold italic text-[#083653]"
          >
            {{ need.number }}
          </span>
          <h3
            class="mt-4 text-lg md:text-xl font-semibold text-[#083653] leading-snug"
          >
            {{ t(need.titleKey) }}
          </h3>
          <p class="mt-3 text-sm text-neutral-500 leading-relaxed font-normal">
            {{ t(need.bodyKey) }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();

const needs = [
  {
    number: "01",
    titleKey: "services.need.n1Title",
    bodyKey: "services.need.n1Body",
  },
  {
    number: "02",
    titleKey: "services.need.n2Title",
    bodyKey: "services.need.n2Body",
  },
  {
    number: "03",
    titleKey: "services.need.n3Title",
    bodyKey: "services.need.n3Body",
  },
  {
    number: "04",
    titleKey: "services.need.n4Title",
    bodyKey: "services.need.n4Body",
  },
];
</script>
