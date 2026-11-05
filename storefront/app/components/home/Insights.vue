<template>
  <section class="bg-[#F9FAFB] px-4 py-16 md:px-[32px] md:py-[80px]">
    <div class="mx-auto max-w-3xl text-center" data-reveal="up">
      <p class="text-lg font-semibold uppercase tracking-wide text-neutral-400">
        {{ t("home.insights.tag") }}
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span class="font-sans font-normal text-[#083653]"
          >{{ t("home.insights.titleNormal") }}
        </span>
        <span class="block font-sans font-semibold italic text-[#083653]">{{
          t("home.insights.titleItalic")
        }}</span>
      </h2>
      <p
        class="mt-4 max-w-xl mx-auto text-sm text-neutral-500 leading-relaxed md:text-base"
      >
        {{ t("home.insights.subtitle") }}
      </p>
    </div>

    <!-- Skeleton Loading State -->
    <div
      v-if="pending && !apiResponse"
      class="mx-auto mt-12 grid max-w-7xl gap-6 md:grid-cols-3 items-stretch"
    >
      <div
        v-for="n in 3"
        :key="n"
        class="flex flex-col justify-between bg-white p-6 border border-neutral-200 animate-pulse h-[380px]"
      >
        <div class="space-y-4">
          <div class="h-48 w-full bg-neutral-200" />
          <div class="h-4 w-24 bg-neutral-200" />
          <div class="h-6 w-3/4 bg-neutral-200" />
          <div class="h-4 w-full bg-neutral-200" />
        </div>
        <div class="h-12 w-full bg-neutral-200 pt-4" />
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-else-if="!articles || articles.length === 0"
      class="mx-auto mt-12 max-w-4xl p-12 text-center border border-dashed border-neutral-300 bg-white"
    >
      <UIcon
        name="i-heroicons-document-text"
        class="size-12 text-neutral-400 mx-auto"
      />
      <p class="mt-3 text-base font-medium text-neutral-600">
        {{
          currentLocale === "en"
            ? "No articles available."
            : "Belum ada artikel saat ini."
        }}
      </p>
    </div>

    <!-- Data State -->
    <div
      v-else
      class="mx-auto mt-12 grid max-w-7xl gap-6 md:grid-cols-3 items-stretch"
      data-reveal="up"
    >
      <article
        v-for="post in articles"
        :key="post.id"
        class="flex flex-col justify-between rounded-none bg-white p-6 border border-[#0000001F] shadow-xs group hover-lift"
      >
        <div>
          <NuxtLink
            :to="`/insight/${post.slug}`"
            class="block overflow-hidden h-48 bg-[#F8FAFC] border border-neutral-100 mb-4"
          >
            <img
              :src="resolveImageUrl(post.cover_image)"
              :alt="post.title"
              class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
            />
          </NuxtLink>
          <p class="text-xs font-bold text-[#083653] uppercase tracking-wide">
            {{ post.category_label || post.category }}
          </p>
          <h3
            class="mt-2 text-lg font-bold text-[#083653] leading-snug line-clamp-2"
          >
            <NuxtLink :to="`/insight/${post.slug}`" class="hover:underline">
              {{ post.title }}
            </NuxtLink>
          </h3>
          <p
            class="mt-2 text-sm text-neutral-500 leading-relaxed font-normal line-clamp-3"
          >
            {{ post.excerpt }}
          </p>
        </div>
        <div
          class="mt-6 flex items-center justify-between pt-4 border-t border-neutral-100"
        >
          <span class="text-xs text-neutral-400 font-medium">
            {{ formatDate(post.published_at, currentLocale) }}
          </span>
          <UButton
            :to="`/insight/${post.slug}`"
            data-testid="view-details"
            color="secondary"
            variant="solid"
            size="lg"
            class="rounded-none h-[48px] px-5 py-3 gap-[10px] bg-[#083653] hover:bg-[#083653]/90 text-white font-medium text-sm inline-flex items-center justify-center"
          >
            {{ t("home.insights.viewDetails") }}
          </UButton>
        </div>
      </article>
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
  category: string;
  category_label?: string;
  excerpt: string;
  cover_image?: string;
  published_at?: string;
}

const { t, currentLocale } = useLanguage();
const apiBase = getApiBaseUrl();

const { data: apiResponse, pending } = safeUseFetch<{ data: ArticleItem[] }>(
  () => `${apiBase}/articles?lang=${currentLocale.value}&per_page=3`,
  {
    key: "home-insights-list",
    watch: [currentLocale],
  },
);

const defaultPosts = computed<ArticleItem[]>(() => [
  {
    id: 1,
    title: t("home.insights.post1Title"),
    slug: "what-your-financial-reports-are-really-telling-you",
    category: t("home.insights.cat1"),
    category_label: t("home.insights.cat1"),
    excerpt: t("home.insights.excerpt"),
    cover_image: "/images/icon/Financial_Icon.svg",
    published_at: "2026-08-31",
  },
  {
    id: 2,
    title: t("home.insights.post2Title"),
    slug: "why-monthly-financial-reporting-matters",
    category: t("home.insights.cat2"),
    category_label: t("home.insights.cat2"),
    excerpt: t("home.insights.excerpt"),
    cover_image: "/images/icon/Analysis_Icon.svg",
    published_at: "2026-08-31",
  },
  {
    id: 3,
    title: t("home.insights.post3Title"),
    slug: "a-practical-way-to-improve-cash-flow-visibility",
    category: t("home.insights.cat3"),
    category_label: t("home.insights.cat3"),
    excerpt: t("home.insights.excerpt"),
    cover_image: "/images/icon/Compliance_Icon.svg",
    published_at: "2026-08-31",
  },
]);

const articles = computed<ArticleItem[]>(() => {
  if (apiResponse.value?.data && apiResponse.value.data.length > 0) {
    return apiResponse.value.data;
  }
  return defaultPosts.value;
});
</script>
