<template>
  <section class="px-4 py-12 md:px-8 md:py-20 bg-white">
    <div class="mx-auto max-w-7xl">
      <!-- Section Header -->
      <div>
        <h2
          class="text-2xl sm:text-3xl md:text-[36px] font-bold text-[#083653] leading-tight"
        >
          {{ t('insight.articles.title') }}
        </h2>
        <p class="mt-2 text-sm md:text-base text-neutral-500 font-normal">
          {{ t('insight.articles.subtitle') }}
        </p>
      </div>

      <!-- Main Layout: Articles + Sidebar -->
      <div class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left Area: Filters, Cards Grid, Pagination -->
        <div class="lg:col-span-9">
          <!-- Category Filter Tabs -->
          <div class="flex flex-wrap gap-2.5 items-center">
            <button
              v-for="cat in categories"
              :key="cat.key"
              type="button"
              class="px-4 py-2 text-xs md:text-sm font-medium transition-colors rounded-none"
              :class="
                selectedCategoryKey === cat.key
                  ? 'bg-[#083653] text-white'
                  : 'bg-white text-[#083653] border border-[#DFE7ED] hover:bg-neutral-50'
              "
              @click="selectedCategoryKey = cat.key"
            >
              {{ t(cat.key) }}
            </button>
          </div>

          <!-- Articles Grid (3 cols on desktop) -->
          <div
            class="mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6"
          >
            <article
              v-for="article in filteredArticles"
              :key="article.id"
              class="flex flex-col border border-[#DFE7ED] bg-white overflow-hidden shadow-xs hover:shadow-md transition-shadow group"
            >
              <NuxtLink
                :to="`/insigth/${article.id}`"
                class="block overflow-hidden aspect-[4/3]"
              >
                <img
                  :src="article.image"
                  :alt="t(article.titleKey)"
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
                    <span>{{ t('insight.hero.date') }}</span>
                  </div>

                  <h3
                    class="mt-2.5 text-base font-bold text-[#083653] leading-snug line-clamp-2 group-hover:text-[#083653]/80"
                  >
                    <NuxtLink :to="`/insigth/${article.id}`">
                      {{ t(article.titleKey) }}
                    </NuxtLink>
                  </h3>

                  <p
                    class="mt-2 text-xs text-neutral-500 leading-relaxed line-clamp-3 font-normal"
                  >
                    {{ t(article.excerptKey) }}
                  </p>
                </div>

                <div
                  class="mt-5 pt-4 border-t border-[#DFE7ED] flex items-center justify-between text-xs font-medium"
                >
                  <span class="text-neutral-400 flex items-center gap-1">
                    <UIcon name="i-heroicons-clock" class="size-3.5" />
                    {{ t('insight.hero.readTime') }}
                  </span>
                  <NuxtLink
                    :to="`/insigth/${article.id}`"
                    class="text-[#083653] font-semibold hover:underline flex items-center gap-1"
                  >
                    {{ t('insight.articles.readBtn') }}
                    <UIcon name="i-heroicons-arrow-right" class="size-3" />
                  </NuxtLink>
                </div>
              </div>
            </article>
          </div>

          <!-- Pagination Bar -->
          <div
            class="mt-10 flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-[#DFE7ED]"
          >
            <p class="text-xs md:text-sm text-neutral-500 font-normal">
              Showing 1–{{ filteredArticles.length }} of
              {{ totalArticlesCount }} Articles
            </p>

            <div class="flex items-center gap-1.5">
              <button
                type="button"
                class="size-8 flex items-center justify-center border border-[#DFE7ED] text-neutral-500 hover:bg-neutral-100 text-xs font-medium"
                :disabled="currentPage === 1"
                @click="currentPage > 1 && currentPage--"
              >
                &lt;
              </button>
              <button
                v-for="page in pages"
                :key="page"
                type="button"
                class="size-8 flex items-center justify-center text-xs font-bold transition-colors"
                :class="
                  currentPage === page
                    ? 'bg-[#FFBB00] text-[#083653]'
                    : 'border border-[#DFE7ED] text-neutral-600 hover:bg-neutral-100'
                "
                @click="typeof page === 'number' && (currentPage = page)"
              >
                {{ page }}
              </button>
              <button
                type="button"
                class="size-8 flex items-center justify-center border border-[#DFE7ED] text-neutral-500 hover:bg-neutral-100 text-xs font-medium"
                @click="currentPage++"
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
              {{ t('insight.articles.sidebarTitle') }}
            </h3>

            <ul class="mt-4 space-y-2">
              <li v-for="cat in categories.slice(0, 4)" :key="cat.key">
                <button
                  type="button"
                  class="flex w-full items-center justify-between p-3 text-xs md:text-sm font-medium transition-colors"
                  :class="
                    selectedCategoryKey === cat.key
                      ? 'bg-[#E9EEF2] text-[#083653] font-bold'
                      : 'text-neutral-600 hover:bg-neutral-50'
                  "
                  @click="selectedCategoryKey = cat.key"
                >
                  <span>{{ t(cat.key) }}</span>
                  <span
                    class="px-2 py-0.5 text-[11px] font-semibold"
                    :class="
                      selectedCategoryKey === cat.key
                        ? 'bg-[#083653] text-white'
                        : 'bg-neutral-100 text-neutral-500'
                    "
                  >
                    {{ cat.count }}
                  </span>
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
import { ref, computed } from "vue";
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();

const selectedCategoryKey = ref("insight.cat.all");
const currentPage = ref(1);
const totalArticlesCount = ref(50);

const pages = [1, 2, 3, "...", 245];

const categories = [
  { key: "insight.cat.all", count: 54 },
  { key: "insight.cat.tax", count: 18 },
  { key: "insight.cat.acc", count: 12 },
  { key: "insight.cat.fin", count: 9 },
  { key: "insight.cat.latest", count: 5 },
];

const allArticles = [
  {
    id: "effective-tax-planning-strategies",
    titleKey: "insight.p1.title",
    excerptKey: "insight.p1.excerpt",
    categoryKey: "insight.cat.tax",
    image: "/images/homepage/TaxAdvisory_Desktop.png",
  },
  {
    id: "building-reliable-financial-records",
    titleKey: "insight.p2.title",
    excerptKey: "insight.p2.excerpt",
    categoryKey: "insight.cat.acc",
    image: "/images/homepage/Finance&Consulting_Desktop.png",
  },
  {
    id: "financial-analysis-for-business-growth",
    titleKey: "insight.p3.title",
    excerptKey: "insight.p3.excerpt",
    categoryKey: "insight.cat.fin",
    image: "/images/homepage/HowWeHelp_Desktop.png",
  },
  {
    id: "tax-compliance-checklist-2026",
    titleKey: "insight.p4.title",
    excerptKey: "insight.p4.excerpt",
    categoryKey: "insight.cat.tax",
    image: "/images/homepage/About_Desktop.png",
  },
  {
    id: "streamlining-bookkeeping-systems",
    titleKey: "insight.p5.title",
    excerptKey: "insight.p5.excerpt",
    categoryKey: "insight.cat.acc",
    image: "/images/homepage/TaxAdvisory_Desktop.png",
  },
  {
    id: "cash-flow-management-best-practices",
    titleKey: "insight.p6.title",
    excerptKey: "insight.p6.excerpt",
    categoryKey: "insight.cat.fin",
    image: "/images/homepage/Finance&Consulting_Desktop.png",
  },
  {
    id: "transfer-pricing-advisory-guide",
    titleKey: "insight.p7.title",
    excerptKey: "insight.p7.excerpt",
    categoryKey: "insight.cat.tax",
    image: "/images/homepage/HowWeHelp_Desktop.png",
  },
  {
    id: "annual-financial-statement-prep",
    titleKey: "insight.p8.title",
    excerptKey: "insight.p8.excerpt",
    categoryKey: "insight.cat.acc",
    image: "/images/homepage/About_Desktop.png",
  },
  {
    id: "business-valuation-methodologies",
    titleKey: "insight.p9.title",
    excerptKey: "insight.p9.excerpt",
    categoryKey: "insight.cat.fin",
    image: "/images/homepage/TaxAdvisory_Desktop.png",
  },
];

const filteredArticles = computed(() => {
  if (
    selectedCategoryKey.value === "insight.cat.all" ||
    selectedCategoryKey.value === "insight.cat.latest"
  ) {
    return allArticles;
  }
  return allArticles.filter((item) => item.categoryKey === selectedCategoryKey.value);
});
</script>
