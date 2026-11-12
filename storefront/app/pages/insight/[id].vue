<template>
  <main class="min-h-screen bg-white text-[#092F50] antialiased selection:bg-[#0869B2] selection:text-white">
    <!-- Top Editorial Breadcrumb & Header Band -->
    <section class="bg-[#F4F9FE] border-b border-[#DCE8F5] py-10 sm:py-12 lg:py-14">
      <div class="mx-auto max-w-[880px] px-5 sm:px-6 lg:px-8">
        <!-- Back Navigation -->
        <NuxtLink
          to="/insight"
          class="group inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-[#0869B2] hover:text-[#06477D] transition-colors"
        >
          <span class="transition-transform duration-200 group-hover:-translate-x-1">←</span>
          <span>{{ t("insight.detail.backLink") }}</span>
        </NuxtLink>

        <!-- Skeleton Header -->
        <div v-if="pending" class="mt-6 space-y-4 animate-pulse">
          <div class="h-6 w-28 bg-[#DCE8F5] rounded-[2px]" />
          <div class="h-10 w-5/6 bg-[#DCE8F5] rounded-[2px]" />
          <div class="h-4 w-1/2 bg-[#DCE8F5] rounded-[2px]" />
        </div>

        <!-- Article Header -->
        <div v-else-if="article" class="mt-6">
          <span
            class="inline-block px-3 py-1 rounded-[2px] text-[11px] font-bold uppercase tracking-[0.12em] bg-[#E6F2FC] text-[#0869B2] border border-[#CFE4F7]"
          >
            {{ article.category_label || article.category }}
          </span>

          <h1
            class="mt-4 text-2xl sm:text-4xl lg:text-[42px] font-bold text-[#092F50] leading-[1.18] tracking-[-0.025em]"
          >
            {{ article.title }}
          </h1>

          <div
            class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2.5 text-xs sm:text-sm text-[#52687B]"
          >
            <span class="font-bold text-[#092F50]">
              {{ article.author_name || t("insight.hero.author") }}
            </span>
            <span class="flex items-center gap-1.5">
              <UIcon name="i-heroicons-calendar" class="size-4 text-[#0869B2]" />
              {{ formatDate(article.published_at, currentLocale) }}
            </span>
            <span class="flex items-center gap-1.5">
              <UIcon name="i-heroicons-clock" class="size-4 text-[#0869B2]" />
              {{
                article.read_time
                  ? `${article.read_time} ${currentLocale === "en" ? "mins" : "Menit"}`
                  : t("insight.hero.readTime")
              }}
            </span>
            <ShareButton :url="canonicalUrl" :title="article.title" />
          </div>
        </div>
      </div>
    </section>

    <!-- Article Body Section -->
    <section class="py-10 sm:py-12 lg:py-16">
      <div class="mx-auto max-w-[880px] px-5 sm:px-6 lg:px-8">
        <!-- Skeleton Body -->
        <div v-if="pending" class="space-y-6 animate-pulse">
          <div class="h-72 sm:h-96 w-full bg-[#EBF3FA] rounded-[2px]" />
          <div class="space-y-3 pt-4">
            <div class="h-4 w-full bg-[#EBF3FA]" />
            <div class="h-4 w-full bg-[#EBF3FA]" />
            <div class="h-4 w-4/5 bg-[#EBF3FA]" />
          </div>
        </div>

        <!-- 404 / Empty State -->
        <div
          v-else-if="!article"
          class="p-10 sm:p-12 text-center border border-dashed border-[#B9D3EB] rounded-[2px] bg-[#F4F9FE]"
        >
          <UIcon
            name="i-heroicons-document-text"
            class="size-12 text-[#0869B2] mx-auto"
          />
          <h2 class="mt-4 text-xl sm:text-2xl font-bold text-[#092F50]">
            {{
              currentLocale === "en"
                ? "Article Not Found"
                : "Artikel Tidak Ditemukan"
            }}
          </h2>
          <p class="mt-2 text-sm text-[#52687B] max-w-md mx-auto">
            {{
              currentLocale === "en"
                ? "The article you are looking for might have been moved or removed."
                : "Artikel yang Anda cari tidak dapat ditemukan atau telah dipindahkan."
            }}
          </p>
          <NuxtLink
            to="/insight"
            class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-[#0869B2] hover:bg-[#075997] text-white text-xs font-bold uppercase tracking-wider rounded-[2px] transition-colors"
          >
            <span>←</span>
            <span>{{ t("insight.detail.backLink") }}</span>
          </NuxtLink>
        </div>

        <!-- Article Content State -->
        <article v-else>
          <!-- Featured Image -->
          <div
            class="overflow-hidden aspect-[16/9] rounded-[2px] border border-[#DCE8F5] bg-[#EBF3FA]"
          >
            <img
              :src="resolveImageUrl(article.cover_image, '/images/redesign/article-featured.png')"
              :alt="article.title"
              class="size-full object-cover"
            />
          </div>

          <!-- Article Excerpt / Highlight -->
          <div
            v-if="article.excerpt"
            class="mt-8 p-5 sm:p-6 bg-[#F4F9FE] border-l-4 border-[#0869B2] rounded-r-[2px]"
          >
            <p
              class="text-sm sm:text-base text-[#092F50] font-medium leading-[1.7] italic"
            >
              {{ article.excerpt }}
            </p>
          </div>

          <!-- Article Body Content -->
          <div
            class="mt-8 article-content text-[#2E465C] text-[15.5px] sm:text-[17px] leading-[1.8] font-normal space-y-5 border-b border-[#DCE8F5] pb-12"
            v-html="formattedContent"
          />

          <!-- Bottom Navigation -->
          <div
            class="mt-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
          >
            <NuxtLink
              to="/insight"
              class="group inline-flex items-center gap-2 text-sm font-bold text-[#0869B2] hover:text-[#06477D] transition-colors"
            >
              <span class="transition-transform duration-200 group-hover:-translate-x-1">←</span>
              <span>{{ t("insight.detail.exploreMore") }}</span>
            </NuxtLink>

            <NuxtLink
              to="/contact"
              class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-[2px] bg-[#0869B2] hover:bg-[#075997] text-white text-sm font-bold transition-colors shadow-xs"
            >
              <span>{{ t("insight.detail.discussBtn") }}</span>
              <span>→</span>
            </NuxtLink>
          </div>
        </article>
      </div>
    </section>

    <!-- Consultation CTA Banner -->
    <HomeCta />
  </main>
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

const formattedContent = computed(() => {
  const rawContent = article.value?.content;
  if (!rawContent) return "";
  return rawContent.replace(
    /(<div[^>]*class="[^"]*table-wrapper[^"]*"[^>]*>[\s\S]*?<\/div>)|(<table[\s\S]*?<\/table>)/gi,
    (match, alreadyWrapped, tableOnly) => {
      if (alreadyWrapped) return alreadyWrapped;
      return `<div class="table-wrapper" tabindex="0" role="region" aria-label="Tabel Data">${tableOnly}</div>`;
    }
  );
});

const pageTitle = computed(() => (article.value?.title ? article.value.title : "Insight Detail"));
const pageDescription = computed(() =>
  article.value?.excerpt ? toPlainText(article.value.excerpt, 160) : "AP Consulting Article Insight"
);
const pageImage = computed(() =>
  article.value?.cover_image
    ? resolveImageUrl(article.value.cover_image)
    : `${config.public.siteUrl}/ap_logo.jpeg`
);
const canonicalUrl = computed(() => {
  if (typeof window !== "undefined" && window.location?.origin) {
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
              name: article.value.author_name || "AP Consulting Team",
            },
            publisher: {
              "@type": "Organization",
              name: "AP Consulting",
              logo: {
                "@type": "ImageObject",
                url: `${config.public.siteUrl}/ap_logo.jpeg`,
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
  color: #092F50;
  margin-top: 2rem;
  margin-bottom: 1rem;
}
.article-content h3 {
  font-size: 1.25rem;
  font-weight: 700;
  color: #092F50;
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
  background-color: #F4F9FE;
  border-left: 4px solid #0869B2;
  font-style: italic;
  margin-bottom: 1.25rem;
}
.article-content a {
  color: #0869B2;
  text-decoration: underline;
}

/* Table Styles */
.article-content .table-wrapper {
  width: 100%;
  overflow-x: auto;
  margin-top: 1.5rem;
  margin-bottom: 1.5rem;
  -webkit-overflow-scrolling: touch;
}

.article-content table {
  width: 100%;
  border-collapse: collapse;
  border: 1px solid #DCE8F5;
  margin-top: 1.5rem;
  margin-bottom: 1.5rem;
  font-size: 0.875rem;
  line-height: 1.5;
  text-align: left;
}

@media (min-width: 768px) {
  .article-content table {
    font-size: 0.9375rem;
  }
}

.article-content .table-wrapper table {
  margin-top: 0;
  margin-bottom: 0;
}

.article-content thead {
  background-color: #F4F9FE;
}

.article-content th {
  border: 1px solid #DCE8F5;
  padding: 0.75rem 1rem;
  font-weight: 600;
  color: #092F50;
  background-color: #F4F9FE;
  text-align: left;
  vertical-align: top;
}

.article-content td {
  border: 1px solid #DCE8F5;
  padding: 0.75rem 1rem;
  color: #2E465C;
  vertical-align: top;
}

.article-content tr:nth-child(even) td {
  background-color: #FAFCFF;
}

.article-content tr:hover td {
  background-color: #F0F7FF;
}

.article-content th p,
.article-content td p {
  margin-bottom: 0.35rem;
  line-height: 1.5;
}

.article-content th p:last-child,
.article-content td p:last-child {
  margin-bottom: 0;
}

.article-content th strong,
.article-content td strong {
  color: #092F50;
}
</style>
