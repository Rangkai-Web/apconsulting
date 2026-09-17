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

      <!-- Skeleton Loading State -->
      <div v-if="pending" class="mt-8 space-y-6 animate-pulse">
        <div class="h-6 w-32 bg-neutral-200" />
        <div class="h-10 w-3/4 bg-neutral-200" />
        <div class="h-4 w-1/2 bg-neutral-200" />
        <div class="h-72 w-full bg-neutral-200" />
        <div class="space-y-3 pt-6">
          <div class="h-4 w-full bg-neutral-200" />
          <div class="h-4 w-full bg-neutral-200" />
          <div class="h-4 w-4/5 bg-neutral-200" />
        </div>
      </div>

      <!-- 404 / Empty State -->
      <div
        v-else-if="!article"
        class="mt-12 p-12 text-center border border-dashed border-neutral-300 bg-[#F5FBFF]"
      >
        <UIcon name="i-heroicons-exclamation-triangle" class="size-12 text-amber-500 mx-auto" />
        <h2 class="mt-4 text-2xl font-bold text-[#083653]">
          {{ currentLocale === 'en' ? 'Article Not Found' : 'Artikel Tidak Ditemukan' }}
        </h2>
        <p class="mt-2 text-sm text-neutral-500 max-w-md mx-auto">
          {{ currentLocale === 'en' ? 'The article you are looking for might have been moved or removed.' : 'Artikel yang Anda cari tidak dapat ditemukan atau telah dipindahkan.' }}
        </p>
        <NuxtLink
          to="/insight"
          class="mt-6 inline-block px-5 py-2.5 bg-[#083653] text-white text-xs font-semibold uppercase tracking-wider hover:bg-[#083653]/90"
        >
          {{ t('insight.detail.backLink') }}
        </NuxtLink>
      </div>

      <!-- Article Content State -->
      <div v-else>
        <!-- Article Header -->
        <div class="mt-6">
          <span
            class="inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider bg-[#FFBB00] text-[#083653]"
          >
            {{ article.category_label || article.category }}
          </span>

          <h1
            class="mt-4 text-3xl font-bold text-[#083653] sm:text-4xl md:text-[44px] leading-tight"
          >
            {{ article.title }}
          </h1>

          <div
            class="mt-4 flex flex-wrap items-center gap-4 text-xs sm:text-sm text-neutral-500 font-normal"
          >
            <span class="font-semibold text-[#083653]"
              >{{ article.author_name || t('insight.hero.author') }}</span
            >
            <span class="flex items-center gap-1.5">
              <UIcon name="i-heroicons-calendar" class="size-4" />
              {{ formatDate(article.published_at, currentLocale) }}
            </span>
            <span class="flex items-center gap-1.5">
              <UIcon name="i-heroicons-clock" class="size-4" />
              {{ article.read_time ? `${article.read_time} ${currentLocale === 'en' ? 'mins' : 'Menit'}` : t('insight.hero.readTime') }}
            </span>
            <ShareButton :url="canonicalUrl" :title="article.title" />
          </div>
        </div>

        <!-- Featured Image -->
        <div class="mt-8 overflow-hidden aspect-[16/9] border border-[#DFE7ED] bg-neutral-100">
          <img
            :src="resolveImageUrl(article.cover_image)"
            :alt="article.title"
            class="size-full object-cover"
          />
        </div>

        <!-- Article Excerpt / Highlight -->
        <div v-if="article.excerpt" class="mt-8 p-6 bg-[#F5FBFF] border-l-4 border-[#083653]">
          <p class="text-sm md:text-base text-[#083653] font-medium leading-relaxed italic">
            {{ article.excerpt }}
          </p>
        </div>

        <!-- Article Body Content -->
        <div
          class="mt-8 article-content text-neutral-700 text-base md:text-lg leading-relaxed font-normal space-y-6 border-b border-[#DFE7ED] pb-12"
          v-html="article.content"
        />

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
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useRoute } from "vue-router";
import { useLanguage } from "~/composables/useLanguage";
import { useSeoText } from "~/composables/useSeoText";
import { getApiBaseUrl, resolveImageUrl, formatDate, safeUseFetch } from "~/utils/api";

interface ArticleDetail {
  id: number;
  title: string;
  slug: string;
  category: string;
  category_label?: string;
  excerpt: string;
  content: string;
  cover_image?: string;
  author_name?: string;
  read_time?: number;
  published_at?: string;
}

definePageMeta({
  alias: ["/insigth/:id", "/insights/:id"],
});

const { t, currentLocale } = useLanguage();
const { toSafeJsonLd, toPlainText } = useSeoText();
const route = useRoute();
const config = useRuntimeConfig();
const apiBase = getApiBaseUrl();

const articleId = computed(() => {
  return Array.isArray(route.params.id) ? route.params.id[0] : route.params.id;
});

const { data: apiResponse, pending } = safeUseFetch<{ data: ArticleDetail | ArticleDetail[] }>(
  () => `${apiBase}/articles/${articleId.value}?lang=${currentLocale.value}`,
  {
    key: `article-detail-${articleId.value}`,
    getCachedData: () => undefined,
    watch: [articleId, currentLocale],
  }
);

const article = computed<ArticleDetail | null>(() => {
  const data = apiResponse.value?.data;
  if (!data) return null;
  if (Array.isArray(data)) {
    return data[0] ?? null;
  }
  return data;
});

const pageTitle = computed(() => (article.value?.title ? article.value.title : "Insight Detail"));
const pageDescription = computed(() =>
  article.value?.excerpt ? toPlainText(article.value.excerpt, 160) : "PCG Article Insight"
);
const pageImage = computed(() =>
  article.value?.cover_image
    ? resolveImageUrl(article.value.cover_image)
    : `${config.public.siteUrl}/pcg_logo.jpeg`
);
const canonicalUrl = computed(() => {
  if (import.meta.client && typeof window !== "undefined" && window.location?.origin) {
    return `${window.location.origin}/insight/${articleId.value}`;
  }
  return `${config.public.siteUrl}/insight/${articleId.value}`;
});

useSeoMeta({
  title: pageTitle,
  ogTitle: pageTitle,
  description: pageDescription,
  ogDescription: pageDescription,
  ogImage: pageImage,
  ogUrl: canonicalUrl,
  ogType: "article",
  twitterCard: "summary_large_image",
  twitterTitle: pageTitle,
  twitterDescription: pageDescription,
  twitterImage: pageImage,
});

useHead(() => ({
  link: [{ rel: "canonical", href: canonicalUrl.value }],
  script: article.value
    ? [
        {
          type: "application/ld+json",
          innerHTML: toSafeJsonLd({
            "@context": "https://schema.org",
            "@type": "BlogPosting",
            headline: article.value.title,
            description: pageDescription.value,
            image: [pageImage.value],
            datePublished: article.value.published_at,
            author: {
              "@type": "Person",
              name: article.value.author_name || "PCG Team",
            },
            publisher: {
              "@type": "Organization",
              name: "Phangkawira Consulting Group",
              logo: {
                "@type": "ImageObject",
                url: `${config.public.siteUrl}/pcg_logo.jpeg`,
              },
            },
            mainEntityOfPage: {
              "@type": "WebPage",
              "@id": canonicalUrl.value,
            },
          }),
        },
      ]
    : [],
}));
</script>

<style>
.article-content p {
  margin-bottom: 1.25rem;
}
.article-content h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #083653;
  margin-top: 2rem;
  margin-bottom: 1rem;
}
.article-content h3 {
  font-size: 1.25rem;
  font-weight: 700;
  color: #083653;
  margin-top: 1.5rem;
  margin-bottom: 0.75rem;
}
.article-content ul {
  list-style-type: disc;
  padding-left: 1.5rem;
  margin-bottom: 1.25rem;
}
.article-content ol {
  list-style-type: decimal;
  padding-left: 1.5rem;
  margin-bottom: 1.25rem;
}
.article-content blockquote {
  padding: 1rem 1.5rem;
  background-color: #F5FBFF;
  border-left: 4px solid #083653;
  font-style: italic;
  margin-bottom: 1.25rem;
}
.article-content a {
  color: #083653;
  text-decoration: underline;
}
</style>
