<template>
  <section class="bg-[#F4F9FE] py-12 sm:py-16 lg:py-20 border-b border-[#DCE8F5]">
    <div class="mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="max-w-[680px] mb-8 sm:mb-10">
        <p
          class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#0869B2] mb-2"
        >
          {{ t("insight.articles.eyebrow") }}
        </p>
        <h2
          class="text-2xl sm:text-4xl font-semibold leading-[1.18] tracking-[-0.03em] text-[#092F50] mb-3"
        >
          <span class="font-bold">{{ t("insight.articles.titleBold") }}</span>
          {{ " " }}
          <span class="italic text-[#0869B2]">{{
            t("insight.articles.titleItalic")
          }}</span>
        </h2>
        <p class="text-sm sm:text-[15.5px] leading-[1.65] text-[#52687B]">
          {{ t("insight.articles.subtitle") }}
        </p>
      </div>

      <!-- Filter & Search Bar (Full-Width Clean Editorial Bar, No Sidebar) -->
      <div
        class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 mb-8 border-b border-[#DCE8F5]"
      >
        <!-- Category Filter Pills -->
        <div class="flex flex-wrap gap-2 items-center">
          <button
            v-for="cat in categoryList"
            :key="cat.apiKey"
            type="button"
            :class="[
              'px-3.5 py-2 text-xs sm:text-[13px] font-semibold rounded-[2px] transition-colors cursor-pointer',
              selectedCategoryKey === cat.apiKey
                ? 'bg-[#0869B2] text-white shadow-xs'
                : 'bg-white text-[#092F50] border border-[#DCE8F5] hover:border-[#0869B2] hover:text-[#0869B2]',
            ]"
            @click="selectCategory(cat.apiKey)"
          >
            {{ cat.label }}
          </button>
        </div>

        <!-- Search Input with Debounce -->
        <div class="relative w-full md:w-72 shrink-0">
          <UIcon
            name="i-heroicons-magnifying-glass"
            class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-[#728FA9] pointer-events-none"
          />
          <input
            v-model="searchInput"
            type="text"
            :placeholder="
              currentLocale === 'en' ? 'Search articles...' : 'Cari artikel...'
            "
            class="w-full pl-10 pr-9 py-2 text-xs sm:text-sm bg-white text-[#092F50] placeholder-[#728FA9] border border-[#DCE8F5] rounded-[2px] focus:border-[#0869B2] focus:outline-none transition-colors"
          />
          <button
            v-if="searchInput"
            type="button"
            aria-label="Clear search"
            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[#728FA9] hover:text-[#092F50]"
            @click="searchInput = ''"
          >
            <UIcon name="i-heroicons-x-mark" class="size-4" />
          </button>
        </div>
      </div>

      <!-- Skeleton Loading State -->
      <div
        v-if="pending"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
      >
        <div
          v-for="n in 6"
          :key="n"
          class="flex flex-col border border-[#DCE8F5] rounded-[2px] bg-white p-6 animate-pulse h-[390px]"
        >
          <div class="h-44 w-full bg-[#EBF3FA]" />
          <div class="mt-5 h-3.5 w-24 bg-[#EBF3FA]" />
          <div class="mt-3 h-6 w-4/5 bg-[#EBF3FA]" />
          <div class="mt-3 h-4 w-full bg-[#EBF3FA]" />
          <div class="mt-2 h-4 w-2/3 bg-[#EBF3FA]" />
          <div class="mt-auto pt-4 border-t border-[#F0F6FC] h-8 w-full bg-[#F4F9FE]" />
        </div>
      </div>

      <!-- Empty State (when search or category filter returns no results) -->
      <div
        v-else-if="displayedArticles.length === 0"
        class="p-12 text-center border border-dashed border-[#B9D3EB] rounded-[2px] bg-white"
      >
        <UIcon
          name="i-heroicons-magnifying-glass"
          class="size-11 text-[#728FA9] mx-auto"
        />
        <h3 class="mt-3 text-lg font-bold text-[#092F50]">
          {{
            currentLocale === "en"
              ? "No articles found"
              : "Tidak ada artikel ditemukan"
          }}
        </h3>
        <p class="mt-1 text-sm text-[#52687B] max-w-md mx-auto">
          {{
            currentLocale === "en"
              ? "Try adjusting your search query or reset the filters."
              : "Coba gunakan kata kunci lain atau reset filter kategori Anda."
          }}
        </p>
        <button
          type="button"
          class="mt-5 px-5 py-2.5 bg-[#0869B2] hover:bg-[#075997] text-white text-xs font-bold uppercase tracking-wider rounded-[2px] transition-colors cursor-pointer"
          @click="resetFilters"
        >
          {{ currentLocale === "en" ? "Reset Filters" : "Reset Filter" }}
        </button>
      </div>

      <!-- 3-Column Architectural Article Grid -->
      <div
        v-else
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
      >
        <article
          v-for="article in displayedArticles"
          :key="article.id"
          class="group bg-white rounded-[2px] border border-[#DCE8F5] hover:border-[#0869B2] overflow-hidden flex flex-col justify-between transition-colors duration-200"
        >
          <div>
            <!-- Thumbnail + AP Consulting Signature Date Badge -->
            <NuxtLink
              :to="`/insight/${article.slug}`"
              class="relative block w-full h-[205px] overflow-hidden bg-[#EBF3FA] border-b border-[#DCE8F5]"
            >
              <img
                :src="article.resolvedImage"
                :alt="article.title"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                loading="lazy"
              />
              <!-- Crisp Blue Date Badge (inspired by AP Consulting Articles) -->
              <div
                class="absolute top-3.5 left-3.5 bg-[#0869B2] text-white px-2.5 py-1.5 rounded-[2px] text-center leading-tight shadow-sm"
              >
                <span class="block text-sm font-bold">{{
                  article.badgeDay
                }}</span>
                <span
                  class="block text-[9px] font-semibold tracking-[0.08em] uppercase opacity-90"
                >
                  {{ article.badgeMonthYear }}
                </span>
              </div>
            </NuxtLink>

            <!-- Content -->
            <div class="p-6">
              <div
                class="flex items-center gap-2 text-[11px] font-bold tracking-[0.1em] uppercase text-[#0869B2] mb-2.5"
              >
                <span>{{ article.categoryLabel }}</span>
              </div>

              <h3
                class="text-[18px] font-bold leading-[1.35] tracking-[-0.01em] text-[#092F50] group-hover:text-[#0869B2] transition-colors mb-2.5 line-clamp-2"
              >
                <NuxtLink :to="`/insight/${article.slug}`">
                  {{ article.title }}
                </NuxtLink>
              </h3>

              <p class="text-[13.5px] leading-[1.6] text-[#4E6B85] line-clamp-3">
                {{ article.excerpt }}
              </p>
            </div>
          </div>

          <!-- Card Footer -->
          <div
            class="px-6 py-4 border-t border-[#F0F6FC] flex items-center justify-between text-xs"
          >
            <span class="text-[#728FA9] flex items-center gap-1.5 font-medium">
              <UIcon name="i-heroicons-clock" class="size-3.5" />
              {{
                article.read_time
                  ? `${article.read_time} ${currentLocale === "en" ? "mins" : "Menit"}`
                  : t("insight.hero.readTime")
              }}
            </span>
            <NuxtLink
              :to="`/insight/${article.slug}`"
              class="inline-flex items-center gap-1 font-bold text-[#0869B2] hover:text-[#06477D] transition-colors"
            >
              <span>{{ t("insight.articles.readBtn") }}</span>
              <span class="transition-transform group-hover:translate-x-1">→</span>
            </NuxtLink>
          </div>
        </article>
      </div>

      <!-- Pagination Bar -->
      <div
        v-if="displayedArticles.length > 0 && lastPage > 1"
        class="mt-10 flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-[#DCE8F5]"
      >
        <p class="text-xs sm:text-sm text-[#52687B]">
          {{ t("insight.articles.showing") }} {{ currentPage }}
          {{ t("insight.articles.of") }} {{ lastPage }} ({{ totalArticles }}
          {{ t("insight.articles.articlesLabel") }})
        </p>

        <div class="flex items-center gap-1.5">
          <button
            type="button"
            class="size-8 flex items-center justify-center rounded-[2px] border border-[#DCE8F5] bg-white text-[#52687B] hover:border-[#0869B2] hover:text-[#0869B2] text-xs font-semibold disabled:opacity-40 cursor-pointer"
            :disabled="currentPage <= 1"
            @click="currentPage > 1 && currentPage--"
          >
            &lt;
          </button>
          <button
            v-for="page in lastPage"
            :key="page"
            type="button"
            :class="[
              'size-8 flex items-center justify-center rounded-[2px] text-xs font-bold transition-colors cursor-pointer',
              currentPage === page
                ? 'bg-[#0869B2] text-white'
                : 'bg-white border border-[#DCE8F5] text-[#092F50] hover:border-[#0869B2] hover:text-[#0869B2]',
            ]"
            @click="currentPage = page"
          >
            {{ page }}
          </button>
          <button
            type="button"
            class="size-8 flex items-center justify-center rounded-[2px] border border-[#DCE8F5] bg-white text-[#52687B] hover:border-[#0869B2] hover:text-[#0869B2] text-xs font-semibold disabled:opacity-40 cursor-pointer"
            :disabled="currentPage >= lastPage"
            @click="currentPage < lastPage && currentPage++"
          >
            &gt;
          </button>
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
  safeUseFetch,
} from "~/utils/api";

interface ArticleItem {
  id: number | string;
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
  const list = serviceCategories.value
    .filter((cat) => {
      const nameEn = (cat.name_en || "").toLowerCase();
      return nameEn !== "general inquiry";
    })
    .map((cat) => ({
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
  }
);

const fallbackRawArticles = computed<ArticleItem[]>(() => {
  const isEn = currentLocale.value === "en";
  return [
    {
      id: "fb-1",
      title: isEn
        ? "New Business: Do You Need to Pay Tax Right Away?"
        : "Usaha Baru Buka, Harus Langsung Bayar Pajak?",
      slug: "new-business-do-you-need-to-pay-tax-right-away",
      category: isEn ? "Tax Services" : "Layanan Perpajakan",
      excerpt: isEn
        ? "Taxation is an essential part of national economic sustainability. Understanding tax obligations from day one helps newly established businesses stay compliant."
        : "Pajak merupakan bagian penting dari keberlanjutan ekonomi negara, dan pemahaman mengenai kewajiban pajak membantu bisnis baru tetap patuh aturan.",
      cover_image: "/images/redesign/article-featured.png",
      read_time: 5,
      published_at: "2023-12-18",
    },
    {
      id: "fb-2",
      title: isEn
        ? "Link Your NIK and NPWP Before the End of 2023!"
        : "Padankan NIK dan NPWP Sebelum Akhir Tahun 2023!",
      slug: "padankan-nik-npwp",
      category: isEn ? "Tax Services" : "Layanan Perpajakan",
      excerpt: isEn
        ? "Since July 14, 2022, Indonesia's taxation system has undergone significant digital integration through NIK-NPWP validation."
        : "Mulai tanggal 14 Juli 2022, sistem perpajakan di Indonesia telah mengalami perubahan signifikan melalui implementasi pemadanan NIK dan NPWP.",
      cover_image: "/images/redesign/article-thumb-1.png",
      read_time: 4,
      published_at: "2023-12-15",
    },
    {
      id: "fb-3",
      title: isEn
        ? "Annual Tax Return (SPT) Consultants in Tangerang"
        : "Konsultan Pajak SPT Tahunan Tangerang Jadi Lebih Mudah",
      slug: "konsultan-pajak-spt-tahunan",
      category: isEn ? "Tax Services" : "Layanan Perpajakan",
      excerpt: isEn
        ? "Practical guidance for individuals and corporate taxpayers in Tangerang on preparing accurate annual SPT filings with licensed consultants."
        : "Konsultan Pajak SPT Tahunan Tangerang — Masyarakat ataupun Perusahaan harus memiliki kesadaran wajib pajak untuk pelaporan tahunan tepat waktu.",
      cover_image: "/images/redesign/article-thumb-2.png",
      read_time: 5,
      published_at: "2023-10-18",
    },
  ];
});

function formatBadgeDate(dateStr?: string) {
  const d = dateStr ? new Date(dateStr) : new Date("2023-12-18");
  const valid = !Number.isNaN(d.getTime()) ? d : new Date("2023-12-18");
  const day = String(valid.getDate()).padStart(2, "0");
  const month = valid.toLocaleDateString(
    currentLocale.value === "id" ? "id-ID" : "en-US",
    { month: "short" }
  );
  const year = valid.getFullYear();
  return {
    day,
    monthYear: `${month} ${year}`,
  };
}

const rawArticles = computed(() => {
  const apiItems = apiResponse.value?.data || [];
  if (apiItems.length > 0) return apiItems;
  if (!debouncedSearch.value && !selectedCategoryKey.value && currentPage.value === 1) {
    return fallbackRawArticles.value;
  }
  return [];
});

const displayedArticles = computed(() =>
  rawArticles.value.map((article, idx) => {
    const badge = formatBadgeDate(article.published_at);
    const fallbackImages = [
      "/images/redesign/article-featured.png",
      "/images/redesign/article-thumb-1.png",
      "/images/redesign/article-thumb-2.png",
    ];
    return {
      ...article,
      resolvedImage: article.cover_image?.startsWith("/images/redesign/")
        ? article.cover_image
        : resolveImageUrl(article.cover_image, fallbackImages[idx % 3]),
      badgeDay: badge.day,
      badgeMonthYear: badge.monthYear,
      categoryLabel:
        article.category_label ||
        article.category ||
        t("home.v3.insights.defaultCat"),
    };
  })
);

const lastPage = computed(() => apiResponse.value?.meta?.last_page || 1);
const totalArticles = computed(
  () => apiResponse.value?.meta?.total || displayedArticles.value.length
);
</script>
