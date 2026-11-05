<template>
  <section class="px-4 py-12 md:px-8 md:py-20 bg-white">
    <div class="mx-auto max-w-7xl">
      <!-- Section Header -->
      <div data-reveal="up">
        <h2
          class="text-2xl sm:text-3xl md:text-[36px] font-bold text-[#083653] leading-tight"
        >
          {{ t("insight.articles.title") }}
        </h2>
        <p class="mt-2 text-sm md:text-base text-neutral-500 font-normal">
          {{ t("insight.articles.subtitle") }}
        </p>
      </div>

      <!-- Main Layout: Articles + Sidebar -->
      <div class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left Area: Filters, Cards Grid, Pagination -->
        <div class="lg:col-span-9">
          <!-- Search & Filter Header -->
          <div
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6"
          >
            <!-- Category Filter Tabs -->
            <div class="flex flex-wrap gap-2 items-center">
              <button
                v-for="cat in categoryList"
                :key="cat.apiKey"
                type="button"
                class="px-3.5 py-1.5 text-xs md:text-sm font-medium transition-colors rounded-none"
                :class="
                  selectedCategoryKey === cat.apiKey
                    ? 'bg-[#083653] text-white'
                    : 'bg-white text-[#083653] border border-[#DFE7ED] hover:bg-neutral-50'
                "
                @click="selectCategory(cat.apiKey)"
              >
                {{ cat.label }}
              </button>
            </div>

            <!-- Search Input with Debounce -->
            <div class="relative w-full sm:w-72 shrink-0">
              <UIcon
                name="i-heroicons-magnifying-glass"
                class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-neutral-400 pointer-events-none"
              />
              <input
                v-model="searchInput"
                type="text"
                :placeholder="
                  currentLocale === 'en'
                    ? 'Search articles...'
                    : 'Cari artikel...'
                "
                class="w-full pl-9 pr-8 py-2 text-xs md:text-sm bg-white border border-[#DFE7ED] focus:border-[#083653] focus:outline-none transition-colors"
              />
              <button
                v-if="searchInput"
                type="button"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600"
                @click="searchInput = ''"
              >
                <UIcon name="i-heroicons-x-mark" class="size-4" />
              </button>
            </div>
          </div>

          <!-- Skeleton Loading State -->
          <div
            v-if="pending"
            class="mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6"
          >
            <div
              v-for="n in 6"
              :key="n"
              class="flex flex-col border border-[#DFE7ED] bg-white p-5 animate-pulse h-[360px]"
            >
              <div class="h-40 w-full bg-neutral-200" />
              <div class="mt-4 h-4 w-20 bg-neutral-200" />
              <div class="mt-2 h-6 w-3/4 bg-neutral-200" />
              <div class="mt-2 h-4 w-full bg-neutral-200" />
              <div
                class="mt-auto pt-4 border-t border-[#DFE7ED] h-10 w-full bg-neutral-100"
              />
            </div>
          </div>

          <!-- Empty State -->
          <div
            v-else-if="!articles || articles.length === 0"
            class="mt-8 p-12 text-center border border-dashed border-neutral-300 bg-[#F5FBFF]"
          >
            <UIcon
              name="i-heroicons-magnifying-glass"
              class="size-12 text-neutral-400 mx-auto"
            />
            <h3 class="mt-3 text-lg font-bold text-[#083653]">
              {{
                currentLocale === "en"
                  ? "No articles found"
                  : "Tidak ada artikel ditemukan"
              }}
            </h3>
            <p class="mt-1 text-sm text-neutral-500 max-w-md mx-auto">
              {{
                currentLocale === "en"
                  ? "Try adjusting your search query or reset the filters."
                  : "Coba gunakan kata kunci lain atau reset filter kategori Anda."
              }}
            </p>
            <button
              type="button"
              class="mt-4 px-4 py-2 bg-[#083653] text-white text-xs font-semibold uppercase tracking-wider hover:bg-[#083653]/90"
              @click="resetFilters"
            >
              {{ currentLocale === "en" ? "Reset Filters" : "Reset Filter" }}
            </button>
          </div>

          <!-- Articles Grid (3 cols on desktop) -->
          <div
            v-else
            class="mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6"
          >
            <article
              v-for="article in articles"
              :key="article.id"
              class="flex flex-col border border-[#DFE7ED] bg-white overflow-hidden shadow-xs hover:shadow-md transition-shadow group"
            >
              <NuxtLink
                :to="`/insight/${article.slug}`"
                class="block overflow-hidden aspect-[4/3] bg-neutral-100"
              >
                <img
                  :src="resolveImageUrl(article.cover_image)"
                  :alt="article.title"
                  class="size-full object-cover transition-transform duration-300 group-hover:scale-105"
                />
              </NuxtLink>

              <div class="p-5 flex flex-col flex-grow justify-between">
                <div>
                  <div
                    class="flex items-center gap-1.5 text-xs text-neutral-400 font-normal"
                  >
                    <UIcon
                      name="i-heroicons-calendar"
                      class="size-3.5 shrink-0"
                    />
                    <span>{{
                      formatDate(article.published_at, currentLocale)
                    }}</span>
                  </div>

                  <h3
                    class="mt-2.5 text-base font-bold text-[#083653] leading-snug line-clamp-2 group-hover:text-[#083653]/80"
                  >
                    <NuxtLink :to="`/insight/${article.slug}`">
                      {{ article.title }}
                    </NuxtLink>
                  </h3>

                  <p
                    class="mt-2 text-xs text-neutral-500 leading-relaxed line-clamp-3 font-normal"
                  >
                    {{ article.excerpt }}
                  </p>
                </div>

                <div
                  class="mt-5 pt-4 border-t border-[#DFE7ED] flex items-center justify-between text-xs font-medium"
                >
                  <span class="text-neutral-400 flex items-center gap-1">
                    <UIcon name="i-heroicons-clock" class="size-3.5" />
                    {{
                      article.read_time
                        ? `${article.read_time} ${currentLocale === "en" ? "mins" : "Menit"}`
                        : t("insight.hero.readTime")
                    }}
                  </span>
                  <NuxtLink
                    :to="`/insight/${article.slug}`"
                    class="text-[#083653] font-semibold hover:underline flex items-center gap-1"
                  >
                    {{ t("insight.articles.readBtn") }}
                    <UIcon name="i-heroicons-arrow-right" class="size-3" />
                  </NuxtLink>
                </div>
              </div>
            </article>
          </div>

          <!-- Pagination Bar -->
          <div
            v-if="articles && articles.length > 0"
            class="mt-10 flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-[#DFE7ED]"
          >
            <p class="text-xs md:text-sm text-neutral-500 font-normal">
              Showing page {{ currentPage }} of {{ lastPage }} ({{
                totalArticles
              }}
              Articles)
            </p>

            <div class="flex items-center gap-1.5">
              <button
                type="button"
                class="size-8 flex items-center justify-center border border-[#DFE7ED] text-neutral-500 hover:bg-neutral-100 text-xs font-medium disabled:opacity-40"
                :disabled="currentPage <= 1"
                @click="currentPage > 1 && currentPage--"
              >
                &lt;
              </button>
              <button
                v-for="page in lastPage"
                :key="page"
                type="button"
                class="size-8 flex items-center justify-center text-xs font-bold transition-colors"
                :class="
                  currentPage === page
                    ? 'bg-[#FFBB00] text-[#083653]'
                    : 'border border-[#DFE7ED] text-neutral-600 hover:bg-neutral-100'
                "
                @click="currentPage = page"
              >
                {{ page }}
              </button>
              <button
                type="button"
                class="size-8 flex items-center justify-center border border-[#DFE7ED] text-neutral-500 hover:bg-neutral-100 text-xs font-medium disabled:opacity-40"
                :disabled="currentPage >= lastPage"
                @click="currentPage < lastPage && currentPage++"
              >
                &gt;
              </button>
            </div>
          </div>
        </div>

        <!-- Right Area: Sidebar Categories -->
        <div class="lg:col-span-3">
          <div class="border border-[#DFE7ED] bg-white p-6 shadow-xs">
            <h3
              class="text-base font-bold text-[#083653] border-b border-[#DFE7ED] pb-4"
            >
              {{ t("insight.articles.sidebarTitle") }}
            </h3>

            <ul class="mt-4 space-y-2">
              <li v-for="cat in categoryList" :key="cat.apiKey">
                <button
                  type="button"
                  class="flex w-full items-center justify-between p-3 text-xs md:text-sm font-medium transition-colors text-left"
                  :class="
                    selectedCategoryKey === cat.apiKey
                      ? 'bg-[#E9EEF2] text-[#083653] font-bold'
                      : 'text-neutral-600 hover:bg-neutral-50'
                  "
                  @click="selectCategory(cat.apiKey)"
                >
                  <span>{{ cat.label }}</span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { refDebounced } from "@vueuse/core";
import { useLanguage } from "~/composables/useLanguage";
import { useServiceCategories } from "~/composables/useServiceCategories";
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
  read_time?: number;
  published_at?: string;
}

interface ArticleMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

const { t, currentLocale } = useLanguage();
const apiBase = getApiBaseUrl();
const { categories: serviceCategories } = useServiceCategories();

const selectedCategoryKey = ref("");
const searchInput = ref("");
const debouncedSearch = refDebounced(searchInput, 400);
const currentPage = ref(1);

const categoryList = computed(() => {
  const allObj = {
    apiKey: "",
    label: currentLocale.value === "en" ? "All Categories" : "Semua Kategori",
  };
  const list = serviceCategories.value.map((cat) => ({
    apiKey: currentLocale.value === "en" ? cat.name_en : cat.name_id,
    label: currentLocale.value === "en" ? cat.name_en : cat.name_id,
  }));
  return [allObj, ...list];
});

const selectCategory = (apiKey: string) => {
  selectedCategoryKey.value = apiKey;
  currentPage.value = 1;
};

const resetFilters = () => {
  selectedCategoryKey.value = "";
  searchInput.value = "";
  currentPage.value = 1;
};

watch([selectedCategoryKey, debouncedSearch], () => {
  currentPage.value = 1;
});

const { data: apiResponse, pending } = safeUseFetch<{
  data: ArticleItem[];
  meta: ArticleMeta;
}>(
  () => {
    const categoryParam = selectedCategoryKey.value
      ? `&category=${encodeURIComponent(selectedCategoryKey.value)}`
      : "";
    const searchParam = debouncedSearch.value
      ? `&search=${encodeURIComponent(debouncedSearch.value)}`
      : "";
    return `${apiBase}/articles?lang=${currentLocale.value}&page=${currentPage.value}&per_page=6${categoryParam}${searchParam}`;
  },
  {
    key: "articles-list",
    watch: [currentLocale, currentPage, selectedCategoryKey, debouncedSearch],
  },
);

const articles = computed(() => apiResponse.value?.data || []);
const lastPage = computed(() => apiResponse.value?.meta?.last_page || 1);
const totalArticles = computed(() => apiResponse.value?.meta?.total || 0);
</script>
