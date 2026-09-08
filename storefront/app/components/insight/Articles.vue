<template>
  <section class="px-4 py-12 md:px-8 md:py-20 bg-white">
    <div class="mx-auto max-w-7xl">
      <!-- Section Header -->
      <div>
        <h2
          class="text-2xl sm:text-3xl md:text-[36px] font-bold text-[#083653] leading-tight"
        >
          Latest Articles & Insights
        </h2>
        <p class="mt-2 text-sm md:text-base text-neutral-500 font-normal">
          Stay informed with the latest tax regulations, accounting best
          practices, and strategic financial insights.
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
              :key="cat.name"
              type="button"
              class="px-4 py-2 text-xs md:text-sm font-medium transition-colors rounded-none"
              :class="
                selectedCategory === cat.name
                  ? 'bg-[#083653] text-white'
                  : 'bg-white text-[#083653] border border-[#DFE7ED] hover:bg-neutral-50'
              "
              @click="selectedCategory = cat.name"
            >
              {{ cat.name }}
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
                    <span>{{ article.date }}</span>
                  </div>

                  <h3
                    class="mt-2.5 text-base font-bold text-[#083653] leading-snug line-clamp-2 group-hover:text-[#083653]/80"
                  >
                    <NuxtLink :to="`/insigth/${article.id}`">
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
                    {{ article.readTime }}
                  </span>
                  <NuxtLink
                    :to="`/insigth/${article.id}`"
                    class="text-[#083653] font-semibold hover:underline flex items-center gap-1"
                  >
                    Read
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
              Articles Categories
            </h3>

            <ul class="mt-4 space-y-2">
              <li v-for="cat in categories.slice(0, 4)" :key="cat.name">
                <button
                  type="button"
                  class="flex w-full items-center justify-between p-3 text-xs md:text-sm font-medium transition-colors"
                  :class="
                    selectedCategory === cat.name
                      ? 'bg-[#E9EEF2] text-[#083653] font-bold'
                      : 'text-neutral-600 hover:bg-neutral-50'
                  "
                  @click="selectedCategory = cat.name"
                >
                  <span>{{ cat.name }}</span>
                  <span
                    class="px-2 py-0.5 text-[11px] font-semibold"
                    :class="
                      selectedCategory === cat.name
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

const selectedCategory = ref("All Categories");
const currentPage = ref(1);
const totalArticlesCount = ref(50);

const pages = [1, 2, 3, "...", 245];

const categories = [
  { name: "All Categories", count: 54 },
  { name: "Taxation Services", count: 18 },
  { name: "Accounting & Bookkeeping", count: 12 },
  { name: "Financial Analysis", count: 9 },
  { name: "Latest", count: 5 },
];

const allArticles = [
  {
    id: "effective-tax-planning-strategies",
    title: "Effective Tax Planning Strategies for Growing Enterprises",
    excerpt:
      "Discover how managing your tax obligations properly and structuring your financial administration can support your business development.",
    category: "Taxation Services",
    date: "September 10, 2026",
    readTime: "5 min read",
    image: "/images/homepage/TaxAdvisory_Desktop.png",
  },
  {
    id: "building-reliable-financial-records",
    title: "Building Reliable Financial Records for Day-to-Day Operations",
    excerpt:
      "Learn practical steps to keep your financial statements accurate, audit-ready, and valuable for strategic decision-making.",
    category: "Accounting & Bookkeeping",
    date: "September 10, 2026",
    readTime: "5 min read",
    image: "/images/homepage/Finance&Consulting_Desktop.png",
  },
  {
    id: "financial-analysis-for-business-growth",
    title: "Financial Analysis & Valuation Techniques for Business Growth",
    excerpt:
      "Turn raw financial data into actionable corporate forecasting, budgeting, and performance evaluation metrics.",
    category: "Financial Analysis",
    date: "September 10, 2026",
    readTime: "5 min read",
    image: "/images/homepage/HowWeHelp_Desktop.png",
  },
  {
    id: "tax-compliance-checklist-2026",
    title: "Corporate Tax Compliance Checklist for the Upcoming Fiscal Year",
    excerpt:
      "Ensure your enterprise meets statutory tax filing requirements and avoids common compliance pitfalls.",
    category: "Taxation Services",
    date: "September 10, 2026",
    readTime: "5 min read",
    image: "/images/homepage/About_Desktop.png",
  },
  {
    id: "streamlining-bookkeeping-systems",
    title: "Streamlining Accounting Systems and Document Coordination",
    excerpt:
      "How modern cloud bookkeeping and automated document administration improve operational transparency.",
    category: "Accounting & Bookkeeping",
    date: "September 10, 2026",
    readTime: "5 min read",
    image: "/images/homepage/TaxAdvisory_Desktop.png",
  },
  {
    id: "cash-flow-management-best-practices",
    title: "Cash-Flow Management and Strategic Corporate Budgeting",
    excerpt:
      "Key strategies for managing liquidity, optimizing operational budgets, and preparing for capital investments.",
    category: "Financial Analysis",
    date: "September 10, 2026",
    readTime: "5 min read",
    image: "/images/homepage/Finance&Consulting_Desktop.png",
  },
  {
    id: "transfer-pricing-advisory-guide",
    title: "Understanding Transfer Pricing Regulations for Mid-Market Firms",
    excerpt:
      "A comprehensive overview of compliance standards, documentation rules, and risk mitigation in transfer pricing.",
    category: "Taxation Services",
    date: "September 10, 2026",
    readTime: "5 min read",
    image: "/images/homepage/HowWeHelp_Desktop.png",
  },
  {
    id: "annual-financial-statement-prep",
    title: "Preparing Annual Financial Statements with Confidence",
    excerpt:
      "Best practices for closing fiscal periods, completing bank reconciliations, and filing statutory returns.",
    category: "Accounting & Bookkeeping",
    date: "September 10, 2026",
    readTime: "5 min read",
    image: "/images/homepage/About_Desktop.png",
  },
  {
    id: "business-valuation-methodologies",
    title: "Evaluating Business Valuation Methodologies for Strategic M&A",
    excerpt:
      "An essential guide to discounted cash flows, earnings multiples, and asset valuation in corporate transactions.",
    category: "Financial Analysis",
    date: "September 10, 2026",
    readTime: "5 min read",
    image: "/images/homepage/TaxAdvisory_Desktop.png",
  },
];

const filteredArticles = computed(() => {
  if (
    selectedCategory.value === "All Categories" ||
    selectedCategory.value === "Latest"
  ) {
    return allArticles;
  }
  return allArticles.filter((item) => item.category === selectedCategory.value);
});
</script>
