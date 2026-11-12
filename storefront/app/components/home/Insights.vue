<script setup lang="ts">
import { computed } from "vue"
import { useLanguage } from "~/composables/useLanguage"

const { t, currentLocale } = useLanguage()

const { data: insightsData } = await useFetch<{
  data?: Array<{
    id: string | number
    title: string
    slug: string
    category?: { name: string }
    published_at?: string
    thumbnail?: string
    excerpt?: string
  }>
}>("/api/insights", {
  query: { limit: 3 },
  default: () => ({ data: [] }),
})

const fallbackInsights = computed(() => [
  {
    id: "fallback-1",
    category: t("home.v3.insights.defaultCat"),
    date: t("home.v3.insights.f1Date"),
    title: t("home.v3.insights.f1Title"),
    excerpt: t("home.v3.insights.f1Excerpt"),
    slug: "new-business-do-you-need-to-pay-tax-right-away",
    image: "/images/redesign/article-featured.png",
  },
  {
    id: "fallback-2",
    category: t("home.v3.insights.defaultCat"),
    date: t("home.v3.insights.f2Date"),
    title: t("home.v3.insights.f2Title"),
    excerpt: t("home.v3.insights.f2Excerpt"),
    slug: "padankan-nik-npwp",
    image: "/images/redesign/article-thumb-1.png",
  },
  {
    id: "fallback-3",
    category: t("home.v3.insights.defaultCat"),
    date: t("home.v3.insights.f3Date"),
    title: t("home.v3.insights.f3Title"),
    excerpt: t("home.v3.insights.f3Excerpt"),
    slug: "konsultan-pajak-spt-tahunan",
    image: "/images/redesign/article-thumb-2.png",
  },
])

const insightsList = computed(() => {
  const items = insightsData.value?.data
  if (items && items.length >= 3) {
    const localeStr = currentLocale.value === "id" ? "id-ID" : "en-GB"
    return items.slice(0, 3).map((item, idx) => ({
      id: item.id,
      category: (item.category?.name || t("home.v3.insights.defaultCat")).toUpperCase(),
      date: item.published_at
        ? new Date(item.published_at)
            .toLocaleDateString(localeStr, {
              day: "2-digit",
              month: "long",
              year: "numeric",
            })
            .toUpperCase()
        : t("home.v3.insights.f1Date"),
      title: item.title,
      excerpt:
        item.excerpt ||
        t("home.v3.insights.f1Excerpt"),
      slug: item.slug,
      image: item.thumbnail || fallbackInsights.value[idx]?.image || "/images/redesign/article-featured.png",
    }))
  }
  return fallbackInsights.value
})
</script>

<template>
  <section class="w-full bg-[#F4F9FE] py-12 sm:py-14 lg:py-16 border-b border-[#DCE8F5]">
    <div class="mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <!-- Top Header -->
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 sm:mb-10">
        <div>
          <p class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#0869B2] mb-2">
            {{ t("home.v3.insights.eyebrow") }}
          </p>
          <h2 class="text-2xl sm:text-4xl font-semibold leading-[1.18] tracking-[-0.03em] text-[#092F50]">
            {{ t("home.v3.insights.titleNormal") }} <span class="italic font-bold text-[#0869B2]">{{ t("home.v3.insights.titleItalic") }}</span>
          </h2>
        </div>

        <NuxtLink
          to="/insight"
          class="group inline-flex items-center gap-1.5 text-sm font-bold text-[#0869B2] hover:text-[#06477D] transition-colors"
        >
          <span>{{ t("home.v3.insights.viewAll") }}</span>
          <span class="transition-transform group-hover:translate-x-1">→</span>
        </NuxtLink>
      </div>

      <!-- 3-Column Editorial Article Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <NuxtLink
          v-for="article in insightsList"
          :key="article.id"
          :to="`/insight/${article.slug}`"
          class="group bg-white rounded-[2px] border border-[#DCE8F5] overflow-hidden flex flex-col justify-between transition-all duration-200 hover:border-[#0869B2]"
        >
          <div>
            <!-- Article Thumbnail -->
            <div class="w-full h-[200px] overflow-hidden bg-[#EBF3FA] border-b border-[#DCE8F5]">
              <img
                :src="article.image"
                :alt="article.title"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                loading="lazy"
              />
            </div>

            <!-- Article Content -->
            <div class="p-6">
              <div class="flex items-center gap-2 text-[11px] font-semibold tracking-[0.1em] uppercase mb-3">
                <span class="text-[#0869B2] font-bold">{{ article.category }}</span>
                <span class="text-[#DCE8F5]">/</span>
                <span class="text-[#728FA9]">{{ article.date }}</span>
              </div>

              <h3 class="text-[18px] font-bold leading-[1.35] tracking-[-0.01em] text-[#092F50] group-hover:text-[#0869B2] transition-colors mb-2.5 line-clamp-2">
                {{ article.title }}
              </h3>

              <p class="text-[13.5px] leading-[1.6] text-[#4E6B85] line-clamp-3">
                {{ article.excerpt }}
              </p>
            </div>
          </div>

          <div class="px-6 pb-5 pt-3 border-t border-[#F0F6FC] flex items-center justify-between text-xs font-bold text-[#0869B2]">
            <span class="group-hover:text-[#092F50] transition-colors">
              {{ t("home.v3.insights.readInsight") }}
            </span>
            <span class="text-base transition-transform group-hover:translate-x-1">↗</span>
          </div>
        </NuxtLink>
      </div>
    </div>
  </section>
</template>
