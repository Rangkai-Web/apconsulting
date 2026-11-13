<template>
  <section
    id="insight-hero"
    class="relative overflow-hidden bg-[#072642] text-white"
  >
    <!-- Background Image & Crisp Blue-Navy Gradient Overlay -->
    <img
      src="https://images.unsplash.com/photo-1556740758-90de374c12ad?auto=format&fit=crop&w=1600&q=80"
      alt=""
      aria-hidden="true"
      class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none"
      loading="eager"
    />
    <div
      class="absolute inset-0 bg-gradient-to-r from-[#06233D]/96 via-[#083A63]/92 to-[#0869B2]/80 pointer-events-none"
    />

    <div
      class="relative z-10 mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-20"
    >
      <div
        class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-10 lg:gap-12 items-center"
      >
        <!-- Left Column: Featured Article Editorial -->
        <div class="lg:col-span-7 flex flex-col items-start gap-4 sm:gap-5">
          <div class="animate-hero-badge flex flex-wrap items-center gap-3">
            <span
              class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#B9DBF4]"
            >
              {{ t("insight.hero.eyebrow") }}
            </span>
            <span
              class="px-2.5 py-0.5 rounded-[2px] bg-[#0869B2] text-white text-[10px] font-bold tracking-[0.12em] uppercase"
            >
              {{ t("insight.hero.tag") }}
            </span>
          </div>

          <NuxtLink
            :to="featuredHref"
            class="animate-hero-title group block focus:outline-none"
          >
            <h1
              class="text-[28px] sm:text-4xl lg:text-[46px] font-bold leading-[1.15] tracking-[-0.025em] text-white group-hover:text-[#B9E2FF] transition-colors line-clamp-3"
            >
              {{ featuredArticle?.title || t("insight.hero.title") }}
            </h1>
          </NuxtLink>

          <p
            class="animate-hero-desc text-[15px] sm:text-[16.5px] text-[#E1EFFA] leading-[1.65] max-w-[560px] line-clamp-3"
          >
            {{ featuredArticle?.excerpt || t("insight.hero.description") }}
          </p>

          <!-- Metadata Row -->
          <div
            class="animate-hero-desc flex flex-wrap items-center gap-x-5 gap-y-2 text-xs sm:text-sm text-[#B9DBF4]"
          >
            <span class="font-semibold text-white">
              {{ featuredArticle?.author_name || t("insight.hero.author") }}
            </span>
            <span class="flex items-center gap-1.5">
              <UIcon name="i-heroicons-calendar" class="size-4 text-[#8EC5F2]" />
              {{
                formatDate(featuredArticle?.published_at, currentLocale) ||
                t("insight.hero.date")
              }}
            </span>
            <span class="flex items-center gap-1.5">
              <UIcon name="i-heroicons-clock" class="size-4 text-[#8EC5F2]" />
              {{
                featuredArticle?.read_time
                  ? `${featuredArticle.read_time} ${currentLocale === "en" ? "mins" : "Menit"}`
                  : t("insight.hero.readTime")
              }}
            </span>
          </div>

          <div class="animate-hero-cta pt-1 w-full sm:w-auto">
            <NuxtLink
              :to="featuredHref"
              class="inline-flex w-full sm:w-auto items-center justify-center gap-2 px-6 py-3 bg-white text-[#083A63] text-sm font-bold rounded-[2px] shadow-sm hover:bg-[#F0F7FF] transition-colors"
            >
              <span>{{ t("insight.hero.readFeatured") }}</span>
              <span class="text-base leading-none text-[#0869B2]">→</span>
            </NuxtLink>
          </div>
        </div>

        <!-- Right Column: Featured Visual Frame -->
        <div class="animate-hero-image lg:col-span-5">
          <NuxtLink
            :to="featuredHref"
            class="group block relative overflow-hidden rounded-tr-[40px] sm:rounded-tr-[52px] rounded-tl-[4px] rounded-b-[4px] border border-white/15 shadow-2xl bg-[#061F35]"
          >
            <img
              :src="
                resolveImageUrl(
                  featuredArticle?.cover_image,
                  '/images/redesign/article-featured.png'
                )
              "
              :alt="featuredArticle?.title || t('insight.hero.title')"
              fetchpriority="high"
              decoding="async"
              class="w-full h-[220px] sm:h-[300px] lg:h-[340px] object-cover transition-transform duration-500 group-hover:scale-105"
            />
          </NuxtLink>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useLanguage } from "~/composables/useLanguage";
import {
  getApiBaseUrl,
  resolveImageUrl,
  formatDate,
  safeUseFetch,
} from "~/utils/api";

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

const { t, currentLocale } = useLanguage();
const apiBase = getApiBaseUrl();

const { data: apiResponse } = safeUseFetch<{ data: ArticleItem[] }>(
  () => `${apiBase}/articles?lang=${currentLocale.value}&per_page=1`,
  {
    key: "insight-featured-hero",
    watch: [currentLocale],
  }
);

const featuredArticle = computed(() => apiResponse.value?.data?.[0] || null);
const featuredHref = computed(() =>
  featuredArticle.value
    ? `/insight/${featuredArticle.value.slug}`
    : "/insight/new-business-do-you-need-to-pay-tax-right-away"
);
</script>

