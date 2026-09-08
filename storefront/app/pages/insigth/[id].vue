<template>
  <div class="bg-white py-12 md:py-20">
    <div class="mx-auto max-w-4xl px-4 md:px-8">
      <!-- Back Navigation -->
      <NuxtLink
        to="/insight"
        class="inline-flex items-center gap-2 text-xs md:text-sm font-semibold uppercase tracking-wider text-[#083653] hover:underline"
      >
        <UIcon name="i-heroicons-arrow-left" class="size-4" />
        {{ t('insight.detail.backLink') }}
      </NuxtLink>

      <!-- Article Header -->
      <div class="mt-6">
        <span
          class="inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider bg-[#FFBB00] text-[#083653]"
        >
          {{ t(article.categoryKey) }}
        </span>

        <h1
          class="mt-4 text-3xl font-bold text-[#083653] sm:text-4xl md:text-[44px] leading-tight"
        >
          {{ t(article.titleKey) }}
        </h1>

        <div
          class="mt-4 flex flex-wrap items-center gap-4 text-xs sm:text-sm text-neutral-500 font-normal"
        >
          <span class="font-semibold text-[#083653]"
            >{{ t('insight.hero.author') }}</span
          >
          <span class="flex items-center gap-1.5">
            <UIcon name="i-heroicons-calendar" class="size-4" />
            {{ t('insight.hero.date') }}
          </span>
          <span class="flex items-center gap-1.5">
            <UIcon name="i-heroicons-clock" class="size-4" />
            {{ t('insight.hero.readTime') }}
          </span>
        </div>
      </div>

      <!-- Featured Image -->
      <div class="mt-8 overflow-hidden aspect-[16/9] border border-[#DFE7ED]">
        <img
          :src="article.image"
          :alt="t(article.titleKey)"
          class="size-full object-cover"
        />
      </div>

      <!-- Article Body -->
      <div
        class="mt-8 text-neutral-700 text-base md:text-lg leading-relaxed font-normal space-y-6 border-b border-[#DFE7ED] pb-12"
      >
        <p>
          {{ t('insight.detail.p1') }}
        </p>

        <h2 class="text-2xl font-bold text-[#083653] pt-4">
          {{ t('insight.detail.h1') }}
        </h2>

        <p>
          {{ t('insight.detail.p2') }}
        </p>

        <blockquote
          class="p-6 bg-[#F5FBFF] border-l-4 border-[#083653] text-[#083653] italic font-medium"
        >
          {{ t('insight.detail.quote') }}
        </blockquote>

        <h2 class="text-2xl font-bold text-[#083653] pt-4">
          {{ t('insight.detail.h2') }}
        </h2>

        <ul class="list-disc pl-6 space-y-2 text-neutral-700 font-normal">
          <li>
            {{ t('insight.detail.li1') }}
          </li>
          <li>
            {{ t('insight.detail.li2') }}
          </li>
          <li>
            {{ t('insight.detail.li3') }}
          </li>
        </ul>
      </div>

      <!-- Bottom Navigation -->
      <div class="mt-8 flex items-center justify-between">
        <NuxtLink
          to="/insight"
          class="inline-flex items-center gap-2 text-sm font-semibold text-[#083653] hover:underline"
        >
          <UIcon name="i-heroicons-arrow-left" class="size-4" />
          {{ t('insight.detail.exploreMore') }}
        </NuxtLink>

        <UButton
          to="/contact"
          color="primary"
          variant="solid"
          size="md"
          class="rounded-none bg-[#FFBB00] text-[#083653] hover:bg-[#FFBB00]/90 font-medium"
        >
          {{ t('insight.detail.discussBtn') }}
        </UButton>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useRoute } from "vue-router";
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();

definePageMeta({
  alias: ["/insight/:id", "/insights/:id"],
});

const route = useRoute();

const articlesList = [
  {
    id: "effective-tax-planning-strategies",
    titleKey: "insight.p1.title",
    categoryKey: "insight.cat.tax",
    image: "/images/homepage/TaxAdvisory_Desktop.png",
  },
  {
    id: "building-reliable-financial-records",
    titleKey: "insight.p2.title",
    categoryKey: "insight.cat.acc",
    image: "/images/homepage/Finance&Consulting_Desktop.png",
  },
  {
    id: "financial-analysis-for-business-growth",
    titleKey: "insight.p3.title",
    categoryKey: "insight.cat.fin",
    image: "/images/homepage/HowWeHelp_Desktop.png",
  },
  {
    id: "tax-compliance-checklist-2026",
    titleKey: "insight.p4.title",
    categoryKey: "insight.cat.tax",
    image: "/images/homepage/About_Desktop.png",
  },
  {
    id: "streamlining-bookkeeping-systems",
    titleKey: "insight.p5.title",
    categoryKey: "insight.cat.acc",
    image: "/images/homepage/TaxAdvisory_Desktop.png",
  },
  {
    id: "cash-flow-management-best-practices",
    titleKey: "insight.p6.title",
    categoryKey: "insight.cat.fin",
    image: "/images/homepage/Finance&Consulting_Desktop.png",
  },
  {
    id: "transfer-pricing-advisory-guide",
    titleKey: "insight.p7.title",
    categoryKey: "insight.cat.tax",
    image: "/images/homepage/HowWeHelp_Desktop.png",
  },
  {
    id: "annual-financial-statement-prep",
    titleKey: "insight.p8.title",
    categoryKey: "insight.cat.acc",
    image: "/images/homepage/About_Desktop.png",
  },
  {
    id: "business-valuation-methodologies",
    titleKey: "insight.p9.title",
    categoryKey: "insight.cat.fin",
    image: "/images/homepage/TaxAdvisory_Desktop.png",
  },
];

const article = computed(() => {
  const paramId = Array.isArray(route.params.id) ? route.params.id[0] : route.params.id;
  const found = articlesList.find((a) => a.id === paramId);
  if (found) {
    return found;
  }
  return {
    id: paramId || "navigating-corporate-tax-compliance",
    titleKey: "insight.hero.title",
    categoryKey: "insight.cat.tax",
    image: "/images/insight/Hero_Desktop.png",
  };
});

useHead({
  title: computed(() => t(article.value.titleKey)),
  meta: [
    {
      name: "description",
      content: computed(() => t('insight.hero.description')),
    },
  ],
});
</script>
