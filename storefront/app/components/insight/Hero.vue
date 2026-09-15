<template>
  <section
    id="insight-hero"
    class="relative min-h-[500px] md:min-h-[580px] flex items-center bg-[#083653] px-4 py-16 sm:px-8 md:px-16 md:py-20 overflow-hidden"
  >
    <picture class="absolute inset-0 size-full pointer-events-none">
      <source
        media="(min-width: 768px)"
        :srcset="resolveImageUrl(featuredArticle?.cover_image, '/images/insight/Hero_Desktop.webp')"
      />
      <img
        :src="resolveImageUrl(featuredArticle?.cover_image, '/images/insight/Hero_Mobile.webp')"
        alt="PCG Featured Insight"
        fetchpriority="high"
        decoding="async"
        width="1280"
        height="580"
        class="size-full object-cover opacity-35"
      />
    </picture>

    <!-- Dark gradient overlay for readable white text -->
    <div
      class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/60 to-transparent pointer-events-none"
    ></div>

    <div class="relative z-10 mx-auto max-w-7xl w-full">
      <span
        class="pt-24 sm:pt-0 inline-block text-xs sm:text-sm font-semibold uppercase tracking-wider text-[#FFBB00]"
      >
        {{ t('insight.hero.tag') }}
      </span>
      <NuxtLink
        :to="featuredArticle ? `/insigth/${featuredArticle.slug}` : '/insight'"
        class="group block hover:text-white/95 transition-colors"
      >
        <h1
          class="mt-3 max-w-2xl text-3xl font-semibold text-white sm:text-4xl md:text-[48px] leading-[1.15] tracking-tight group-hover:text-white/95 transition-colors line-clamp-2"
        >
          {{ featuredArticle?.title || t('insight.hero.title') }}
        </h1>
      </NuxtLink>
      <p
        class="mt-4 text-sm text-white/90 leading-relaxed md:text-base font-normal max-w-2xl line-clamp-3"
      >
        {{ featuredArticle?.excerpt || t('insight.hero.description') }}
      </p>

      <div
        class="mt-6 flex flex-wrap items-center gap-4 md:gap-6 text-xs sm:text-sm text-white/80 font-normal"
      >
        <span class="font-medium text-white">{{ featuredArticle?.author_name || t('insight.hero.author') }}</span>
        <span class="flex items-center gap-1.5">
          <UIcon name="i-heroicons-calendar" class="size-4" />
          {{ formatDate(featuredArticle?.published_at, currentLocale) || t('insight.hero.date') }}
        </span>
        <span class="flex items-center gap-1.5">
          <UIcon name="i-heroicons-clock" class="size-4" />
          {{ featuredArticle?.read_time ? `${featuredArticle.read_time} ${currentLocale === 'en' ? 'mins' : 'Menit'}` : t('insight.hero.readTime') }}
        </span>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useLanguage } from '~/composables/useLanguage'
import { getApiBaseUrl, resolveImageUrl, formatDate, safeUseFetch } from '~/utils/api'

interface ArticleItem {
  id: number;
  title: string;
  slug: string;
  excerpt: string;
  cover_image?: string;
  author_name?: string;
  read_time?: number;
  published_at?: string;
}

const { t, currentLocale } = useLanguage()
const apiBase = getApiBaseUrl()

const { data: apiResponse } = safeUseFetch<{ data: ArticleItem[] }>(
  () => `${apiBase}/articles?lang=${currentLocale.value}&per_page=1`,
  {
    key: "insight-featured-hero",
    getCachedData: () => undefined,
    watch: [currentLocale],
  }
)

const featuredArticle = computed(() => apiResponse.value?.data?.[0] || null)
</script>

