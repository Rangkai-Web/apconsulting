<script setup lang="ts">
import { useCatalogStore } from "~/store/catalog";
import type { CatalogSort } from "~/types";

const route = useRoute();
const router = useRouter();
const catalog = useCatalogStore();
const toast = useToast();

const filterOpen = ref(false);

const minPriceInput = ref<number | null>(null);
const maxPriceInput = ref<number | null>(null);

const sortOptions = [
  { label: "Terbaru", value: "newest" },
  { label: "Harga Terendah", value: "price_asc" },
  { label: "Harga Tertinggi", value: "price_desc" },
  { label: "Rating", value: "rating" },
];

function filtersFromRoute() {
  return {
    category:
      typeof route.query.category === "string" ? route.query.category : null,
    search: typeof route.query.q === "string" ? route.query.q : "",
    sort: (typeof route.query.sort === "string"
      ? route.query.sort
      : "newest") as CatalogSort,
    minPrice: route.query.min ? Number(route.query.min) : null,
    maxPrice: route.query.max ? Number(route.query.max) : null,
  };
}

async function syncFromRoute() {
  const filters = filtersFromRoute();
  minPriceInput.value = filters.minPrice;
  maxPriceInput.value = filters.maxPrice;
  try {
    await catalog.applyFilters(filters);
  } catch {
    toast.add({
      title: "Gagal memuat produk",
      description: "Terjadi kesalahan, coba muat ulang halaman.",
      color: "error",
    });
  }
}

function updateQuery(
  patch: Record<string, string | number | null | undefined>,
) {
  router.push({ query: { ...route.query, ...patch } });
}

function selectCategory(slug: string | null) {
  updateQuery({ category: slug || undefined });
}

function applyPriceRange() {
  if (minPriceInput.value !== null && minPriceInput.value < 0) {
    minPriceInput.value = 0;
  }
  if (maxPriceInput.value !== null && maxPriceInput.value < 0) {
    maxPriceInput.value = 0;
  }
  updateQuery({
    min: minPriceInput.value || undefined,
    max: maxPriceInput.value || undefined,
  });
}

function changeSort(sort: string) {
  updateQuery({ sort });
}

const activeCategoryName = computed(() => {
  const slug = route.query.category as string | undefined;
  return slug
    ? (catalog.categories.find((c) => c.slug === slug)?.name ?? null)
    : null;
});

const rootCategories = computed(() => {
  return catalog.categories.filter((c) => !c.parentId);
});

const getChildCategories = (parentId: number) => {
  return catalog.categories.filter((c) => c.parentId === parentId);
};

const activeParentId = computed(() => {
  const slug = route.query.category as string | undefined;
  if (!slug) return null;
  const currentCat = catalog.categories.find((c) => c.slug === slug);
  if (!currentCat) return null;
  return currentCat.parentId || currentCat.id;
});

function isParentActive(parent: any) {
  const slug = route.query.category as string | undefined;
  if (!slug) return false;
  if (parent.slug === slug) return true;
  const activeCat = catalog.categories.find((c) => c.slug === slug);
  return activeCat ? activeCat.parentId === parent.id : false;
}

function resetFilters() {
  minPriceInput.value = null;
  maxPriceInput.value = null;
  router.push({
    query: {
      ...route.query,
      category: undefined,
      min: undefined,
      max: undefined,
    },
  });
}

const seoTitle = computed(() => {
  if (route.query.q) return `Hasil pencarian "${route.query.q}"`;
  if (activeCategoryName.value) return `Katalog ${activeCategoryName.value}`;
  return "Katalog Produk";
});

const seoDescription = computed(() => {
  if (route.query.q) {
    return `Hasil pencarian produk "${route.query.q}" di BbystarGO — jasa titip beli & ready stock Korea, China, Jepang.`;
  }
  if (activeCategoryName.value) {
    return `Koleksi produk ${activeCategoryName.value} di BbystarGO — ready stock & pre order, aman dan terpercaya.`;
  }
  return "Jelajahi katalog lengkap BbystarGO — merchandise dan produk titip beli ready stock & pre order dari Korea, China, Jepang.";
});

useSeoMeta({
  title: seoTitle,
  description: seoDescription,
  ogTitle: seoTitle,
  ogDescription: seoDescription,
});

async function handleLoadMore() {
  try {
    await catalog.loadMore();
  } catch {
    toast.add({
      title: "Gagal memuat produk",
      description: "Terjadi kesalahan, coba lagi.",
      color: "error",
    });
  }
}

onMounted(async () => {
  try {
    await catalog.loadCategories();
  } catch {
    toast.add({
      title: "Gagal memuat kategori",
      description: "Terjadi kesalahan, coba muat ulang halaman.",
      color: "error",
    });
  }
  await syncFromRoute();
});

watch(() => route.query, syncFromRoute);
</script>

<template>
  <div
    class="relative overflow-hidden bg-linear-to-b from-primary-50/35 via-primary-50/15 to-primary-50/5 min-h-screen py-8"
  >
    <!-- Background cute decor spanning whole page -->
    <AppCuteDecor />

    <!-- Ambient Background Glows -->
    <div
      class="absolute top-44 -left-40 w-96 h-96 bg-primary-200/10 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-[600px] -right-40 w-96 h-96 bg-primary-100/10 rounded-full blur-3xl pointer-events-none"
    ></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
      <!-- Premium Shop Header Banner -->
      <div
        class="bg-linear-to-r from-pink-400 to-primary-500 text-white rounded-[2rem] p-8 md:p-12 mb-8 relative overflow-hidden shadow-lg shadow-primary-500/10 border border-white/10"
      >
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.2),transparent)]"></div>

        <!-- Floating decorations -->
        <div class="absolute right-16 top-1/2 -translate-y-1/2 hidden md:flex items-center gap-6 pointer-events-none z-0">
          <UIcon
            name="i-heroicons-star-solid"
            class="w-10 h-10 text-white/20 animate-float"
          />
          <UIcon
            name="i-heroicons-heart-solid"
            class="w-14 h-14 text-white/15 animate-float-delayed"
          />
          <UIcon
            name="i-heroicons-sparkles"
            class="w-8 h-8 text-white/10 animate-float"
          />
        </div>

        <div class="relative z-10">
          <span
            class="text-xs uppercase tracking-widest text-white/80 font-extrabold mb-2 block"
            >BbystarGO</span
          >
          <h1 class="text-2xl md:text-4xl font-black text-white tracking-tight">
            Katalog Produk
          </h1>
          <p
            class="text-xs md:text-sm text-white/90 mt-2 max-w-md leading-relaxed font-semibold"
          >
            Temukan koleksi merchandise yang lucu, unik, dan menggemaskan pilihan terbaik kami.
          </p>
        </div>
      </div>

      <!-- Mobile filter trigger & title -->
      <div
        class="flex items-center justify-between mb-6 lg:hidden bg-white/60 backdrop-blur-sm border border-primary-100/40 rounded-2xl p-4 shadow-xs"
      >
        <div>
          <span
            class="text-[10px] text-primary-600 uppercase font-extrabold tracking-wider"
            >Katalog</span
          >
          <h2 class="text-base font-bold text-gray-900">Filter Produk</h2>
        </div>
        <UButton
          icon="i-heroicons-funnel"
          color="primary"
          variant="soft"
          class="font-extrabold rounded-full"
          @click="
            () => {
              filterOpen = true;
            }
          "
        >
          Filter
        </UButton>
      </div>

      <!-- Main Layout -->
      <div class="space-y-8">
        <!-- Horizontal Desktop Filter Bar -->
        <div
          class="hidden lg:block bg-white/95 backdrop-blur-xs border border-primary-100/50 rounded-3xl p-6 shadow-xs space-y-5"
        >
          <!-- Row 1: Parent Categories -->
          <div class="space-y-2.5">
            <span class="text-[10px] text-gray-400 font-extrabold uppercase tracking-widest block pl-1">
              Kategori
            </span>
            <div class="flex flex-wrap gap-2.5">
              <button
                type="button"
                class="px-4 py-2 rounded-full text-xs font-bold transition-all duration-200 cursor-pointer border"
                :class="
                  !route.query.category
                    ? 'bg-primary-500 border-primary-500 text-white shadow-md shadow-primary-500/20'
                    : 'bg-white border border-primary-100/50 text-gray-600 hover:bg-primary-50/50 hover:border-primary-300'
                "
                @click="selectCategory(null)"
              >
                Semua Kategori
              </button>
              <button
                v-for="parent in rootCategories"
                :key="parent.id"
                type="button"
                class="px-4 py-2 rounded-full text-xs font-extrabold transition-all duration-200 cursor-pointer border flex items-center gap-1.5"
                :class="
                  route.query.category === parent.slug || isParentActive(parent)
                    ? 'bg-primary-500 border-primary-500 text-white shadow-md shadow-primary-500/20'
                    : 'bg-white border border-primary-100/50 text-gray-700 hover:bg-primary-50/50 hover:border-primary-300'
                "
                @click="selectCategory(parent.slug)"
              >
                <UIcon
                  v-if="parent.icon"
                  :name="parent.icon"
                  class="w-4 h-4 shrink-0"
                />
                {{ parent.name }}
              </button>
            </div>
          </div>

          <!-- Row 2: Sub Categories -->
          <div
            v-if="activeParentId && getChildCategories(activeParentId).length > 0"
            class="space-y-2 border-t border-primary-100/30 pt-4"
          >
            <span class="text-[10px] text-gray-400 font-extrabold uppercase tracking-widest block pl-1">
              Sub Kategori
            </span>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="child in getChildCategories(activeParentId)"
                :key="child.id"
                type="button"
                class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all duration-200 cursor-pointer border"
                :class="
                  route.query.category === child.slug
                    ? 'bg-primary-400 border-primary-400 text-white shadow-xs'
                    : 'bg-white border border-primary-100/30 text-gray-500 hover:bg-primary-50/50 hover:border-primary-200'
                "
                @click="selectCategory(child.slug)"
              >
                {{ child.name }}
              </button>
            </div>
          </div>

          <!-- Row 3: Price and Sort Controls -->
          <div class="flex flex-wrap items-center justify-between gap-4 border-t border-primary-100/30 pt-4">
            <div class="flex items-center gap-4">
              <!-- Inline Price Filter -->
              <div class="flex items-center gap-2 bg-primary-50/10 border border-primary-100/40 rounded-full px-4 py-1.5 shadow-xs">
                <UIcon name="i-heroicons-banknotes" class="w-4.5 h-4.5 text-primary-500 shrink-0" />
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider shrink-0">Harga:</span>
                <input
                  v-model.number="minPriceInput"
                  type="number"
                  min="0"
                  placeholder="Min"
                  class="w-20 text-xs font-bold text-gray-750 bg-transparent border-none outline-none focus:ring-0 placeholder-gray-400 focus:outline-none"
                  @blur="applyPriceRange"
                />
                <span class="text-gray-350 font-bold">-</span>
                <input
                  v-model.number="maxPriceInput"
                  type="number"
                  min="0"
                  placeholder="Max"
                  class="w-20 text-xs font-bold text-gray-750 bg-transparent border-none outline-none focus:ring-0 placeholder-gray-400 focus:outline-none"
                  @blur="applyPriceRange"
                />
              </div>

              <!-- Reset Filters Button -->
              <UButton
                v-if="route.query.category || route.query.min || route.query.max"
                icon="i-heroicons-arrow-path"
                color="neutral"
                variant="ghost"
                class="text-xs font-bold text-gray-500 hover:text-primary-500 rounded-full cursor-pointer hover:bg-primary-50/30 transition-all"
                @click="resetFilters"
              >
                Reset Filter
              </UButton>
            </div>

            <!-- Sort Select & Total Count -->
            <div class="flex items-center gap-4">
              <span class="text-xs font-bold text-gray-500 uppercase tracking-wider bg-primary-50/55 border border-primary-100/30 rounded-full px-3.5 py-1.5 shadow-xs">
                {{ catalog.total }} Produk
              </span>
              <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-400 whitespace-nowrap">Urutkan:</span>
                <USelect
                  :model-value="(route.query.sort as string) || 'newest'"
                  :items="sortOptions"
                  size="md"
                  class="w-44 font-extrabold text-gray-700"
                  :ui="{
                    base: 'rounded-full bg-primary-50/20 border border-primary-100/50 focus:border-primary-400 focus:ring-1 focus:ring-primary-100 transition-all duration-200',
                  }"
                  @update:model-value="changeSort"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Filter drawer (mobile) -->
        <USlideover v-model:open="filterOpen" side="bottom">
          <template #content>
            <div class="p-6 space-y-6 bg-white rounded-t-3xl">
              <div
                class="flex items-center justify-between border-b border-primary-100 pb-3"
              >
                <h3
                  class="text-sm font-extrabold text-gray-900 uppercase tracking-wider"
                >
                  Filter Pencarian
                </h3>
                <UButton
                  icon="i-heroicons-x-mark"
                  color="neutral"
                  variant="ghost"
                  aria-label="Tutup filter"
                  class="rounded-lg hover:bg-primary-50"
                  @click="
                    () => {
                      filterOpen = false;
                    }
                  "
                />
              </div>

              <div>
                <h4
                  class="text-xs font-extrabold text-primary-800 uppercase tracking-wider mb-2.5"
                >
                  Kategori
                </h4>
                <div class="space-y-3.5">
                  <!-- All Categories Button -->
                  <button
                    type="button"
                    class="text-xs font-bold w-full text-left px-4 py-2.5 rounded-xl border transition-all duration-200 cursor-pointer"
                    :class="
                      !route.query.category
                        ? 'bg-primary-500 border-primary-500 text-white shadow-sm'
                        : 'border-primary-100 text-gray-600 bg-white hover:bg-primary-50/30'
                    "
                    @click="
                      selectCategory(null);
                      filterOpen = false;
                    "
                  >
                    Semua Kategori
                  </button>

                  <!-- Root Categories -->
                  <div v-for="parent in rootCategories" :key="parent.id" class="space-y-1">
                    <button
                      type="button"
                      class="text-xs font-black w-full text-left px-4 py-2.5 rounded-xl border transition-all duration-200 cursor-pointer"
                      :class="
                        route.query.category === parent.slug
                          ? 'bg-primary-500 border-primary-500 text-white shadow-sm'
                          : 'border-primary-100 text-gray-800 bg-white hover:bg-primary-50/30'
                      "
                      @click="
                        selectCategory(parent.slug);
                        filterOpen = false;
                      "
                    >
                      {{ parent.name }}
                    </button>
                    <!-- Nested Child Categories -->
                    <div v-if="getChildCategories(parent.id).length > 0" class="pl-4 border-l border-primary-100/50 ml-4 space-y-1.5 py-1">
                      <button
                        v-for="child in getChildCategories(parent.id)"
                        :key="child.id"
                        type="button"
                        class="text-xs font-semibold w-full text-left px-3.5 py-2 rounded-xl border transition-all duration-200 cursor-pointer"
                        :class="
                          route.query.category === child.slug
                            ? 'bg-primary-400 border-primary-400 text-white shadow-xs'
                            : 'border-primary-100/40 text-gray-500 bg-white hover:bg-primary-50/30'
                        "
                        @click="
                          selectCategory(child.slug);
                          filterOpen = false;
                        "
                      >
                        {{ child.name }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div>
                <h4
                  class="text-xs font-extrabold text-primary-800 uppercase tracking-wider mb-2.5"
                >
                  Rentang Harga
                </h4>
                <div class="flex items-center gap-3">
                  <UInput
                    v-model.number="minPriceInput"
                    type="number"
                    min="0"
                    placeholder="Min"
                    size="sm"
                    class="w-full"
                    :ui="{
                      base: 'rounded-xl bg-primary-50/20 border border-primary-100/50 focus-visible:ring-1 focus-visible:ring-primary-300 text-gray-700 placeholder-gray-400 font-semibold',
                    }"
                    @blur="applyPriceRange"
                  />
                  <span class="text-gray-400 font-bold">-</span>
                  <UInput
                    v-model.number="maxPriceInput"
                    type="number"
                    min="0"
                    placeholder="Max"
                    size="sm"
                    class="w-full"
                    :ui="{
                      base: 'rounded-xl bg-primary-50/20 border border-primary-100/50 focus-visible:ring-1 focus-visible:ring-primary-300 text-gray-700 placeholder-gray-400 font-semibold',
                    }"
                    @blur="applyPriceRange"
                  />
                </div>
              </div>

              <div class="pt-2">
                <UButton
                  block
                  color="primary"
                  size="md"
                  class="font-extrabold rounded-full py-2.5"
                  @click="
                    () => {
                      filterOpen = false;
                    }
                  "
                >
                  Terapkan Filter
                </UButton>
              </div>
            </div>
          </template>
        </USlideover>

        <!-- Product section -->
        <div class="space-y-6">
          <div
            class="flex lg:hidden flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white/95 border border-primary-100/50 rounded-2xl p-4 shadow-xs"
          >
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">
              {{ catalog.total }} Produk Ditemukan
            </p>
            <div class="flex items-center gap-2">
              <span
                class="text-xs font-bold text-gray-400 whitespace-nowrap"
                >Urutkan:</span
              >
              <USelect
                :model-value="(route.query.sort as string) || 'newest'"
                :items="sortOptions"
                size="md"
                class="w-44 font-extrabold text-gray-700"
                :ui="{
                  base: 'rounded-full bg-primary-50/20 border border-primary-100/50 focus:border-primary-400 focus:ring-1 focus:ring-primary-100 transition-all duration-200',
                }"
                @update:model-value="changeSort"
              />
            </div>
          </div>

          <!-- Product card container -->
          <div
            class="bg-primary-50/20 border border-primary-100/40 rounded-3xl p-6 shadow-xs"
          >
            <ProductGrid
              :products="catalog.products"
              :loading="catalog.loading"
              :has-more="catalog.hasMore"
              @load-more="handleLoadMore"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
