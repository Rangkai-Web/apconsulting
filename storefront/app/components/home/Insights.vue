<template>
  <section class="bg-[#F9FAFB] px-4 py-16 md:px-[32px] md:py-[80px]">
    <div class="mx-auto max-w-3xl text-center">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">
        {{ t('home.insights.tag') }}
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span class="font-sans font-normal text-[#083653]"
          >{{ t('home.insights.titleNormal') }}
        </span>
        <span class="block font-sans font-semibold italic text-[#083653]"
          >{{ t('home.insights.titleItalic') }}</span
        >
      </h2>
      <p
        class="mt-4 max-w-xl mx-auto text-sm text-neutral-500 leading-relaxed md:text-base"
      >
        {{ t('home.insights.subtitle') }}
      </p>
    </div>

    <div
      class="mx-auto mt-12 grid max-w-7xl gap-6 md:grid-cols-3 items-stretch"
    >
      <article
        v-for="post in posts"
        :key="post.titleKey"
        class="flex flex-col justify-between rounded-none bg-white p-6 border border-[#0000001F] shadow-xs"
      >
        <div>
          <img
            :src="post.image"
            :alt="t(post.titleKey)"
            class="h-48 w-full rounded-none object-contain bg-[#F8FAFC] p-6 border border-neutral-100"
          />
          <p
            class="mt-4 text-xs font-bold text-[#083653] uppercase tracking-wide"
          >
            {{ t(post.categoryKey) }}
          </p>
          <h3 class="mt-2 text-lg font-bold text-[#083653] leading-snug">
            {{ t(post.titleKey) }}
          </h3>
          <p class="mt-2 text-sm text-neutral-500 leading-relaxed font-normal">
            {{ t('home.insights.excerpt') }}
          </p>
        </div>
        <div
          class="mt-6 flex items-center justify-between pt-4 border-t border-neutral-100"
        >
          <span class="text-xs text-neutral-400 font-medium">{{
            post.date
          }}</span>
          <UButton
            to="/insights"
            data-testid="view-details"
            color="secondary"
            variant="solid"
            size="lg"
            class="rounded-none h-[56px] px-6 py-4 gap-[10px] bg-[#083653] hover:bg-[#083653]/90 text-white font-medium text-sm inline-flex items-center justify-center"
          >
            {{ t('home.insights.viewDetails') }}
          </UButton>
        </div>
      </article>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useLanguage } from '~/composables/useLanguage'

const { t } = useLanguage()

const posts = [
  {
    image: "/images/icon/Financial_Icon.svg",
    categoryKey: "home.insights.cat1",
    titleKey: "home.insights.post1Title",
    date: "Aug 31, 2026",
  },
  {
    image: "/images/icon/Analysis_Icon.svg",
    categoryKey: "home.insights.cat2",
    titleKey: "home.insights.post2Title",
    date: "Aug 31, 2026",
  },
  {
    image: "/images/icon/Compliance_Icon.svg",
    categoryKey: "home.insights.cat3",
    titleKey: "home.insights.post3Title",
    date: "Aug 31, 2026",
  },
];
</script>
