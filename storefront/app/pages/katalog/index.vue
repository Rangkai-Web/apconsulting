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
  updateQuery({
    min: minPriceInput.value || undefined,
    max: maxPriceInput.value || undefined,
  });
}

function changeSort(sort: string) {
  updateQuery({ sort });
}

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
    class="relative overflow-hidden bg-linear-to-b from-primary-50/45 via-primary-50/15 to-white min-h-screen py-8"
  >
    <!-- Background cute decor spanning whole page -->
    <AppCuteDecor />

    <!-- Ambient Background Glows -->
    <div
      class="absolute top-44 -left-40 w-96 h-96 bg-primary-200/20 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-[600px] -right-40 w-96 h-96 bg-primary-100/20 rounded-full blur-3xl pointer-events-none"
    ></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
      <!-- Premium Shop Header Banner -->
      <div
        class="bg-linear-to-br from-primary-400 to-primary-500 text-white rounded-3xl p-8 md:p-12 mb-8 relative overflow-hidden shadow-lg shadow-primary-500/15 border border-primary-400/25"
      >
        <div
          class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.35),transparent)]"
        ></div>
        <!-- Banner background decorations -->
        <UIcon
          name="i-heroicons-sparkles-solid"
          class="absolute -top-4 -right-4 w-28 h-28 text-white/25 rotate-12 pointer-events-none"
        />
        <UIcon
          name="i-heroicons-star-solid"
          class="absolute top-1/3 right-1/4 w-16 h-16 text-white/20 -rotate-12 pointer-events-none"
        />
        <UIcon
          name="i-heroicons-heart-solid"
          class="absolute bottom-4 right-12 w-14 h-14 text-white/25 rotate-45 pointer-events-none"
        />
        <span
          class="text-xs uppercase tracking-widest text-primary-100 font-extrabold mb-2 block"
          >BbystarGO</span
        >
        <h1 class="text-2xl md:text-4xl font-extrabold tracking-tight">
          Katalog Produk
        </h1>
        <p
          class="text-xs md:text-sm text-white/95 mt-2 max-w-md leading-relaxed font-medium"
        >
          Temukan koleksi merchandise yang lucu, unik, dan menggemaskan pilihan terbaik kami.
        </p>
      </div>

      <!-- Mobile filter trigger & title -->
      <div
        class="flex items-center justify-between mb-6 lg:hidden bg-white/60 backdrop-blur-sm border border-gray-100 rounded-2xl p-4 shadow-sm"
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
          class="font-semibold"
          @click="() => { filterOpen = true }"
        >
          Filter
        </UButton>
      </div>

      <!-- Main Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-[250px_1fr] gap-8">
        <!-- Filter sidebar (desktop) -->
        <aside
          class="hidden lg:block space-y-6 self-start bg-white/80 backdrop-blur-xs border border-primary-100/35 rounded-3xl p-6 shadow-[0_4px_20px_-4px_rgba(255,116,108,0.03)]"
        >
          <div>
            <h3
              class="text-xs uppercase tracking-wider font-extrabold text-primary-800 border-b border-gray-100 pb-2 mb-3"
            >
              Kategori
            </h3>
            <div class="space-y-1">
              <button
                type="button"
                class="flex items-center justify-between text-xs font-semibold w-full text-left px-3 py-2 rounded-xl transition-all duration-200"
                :class="
                  !route.query.category
                    ? 'bg-primary-50 text-primary-700 shadow-sm shadow-primary-600/5'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                "
                @click="selectCategory(null)"
              >
                <span>Semua Kategori</span>
                <UIcon
                  name="i-heroicons-chevron-right"
                  class="w-3.5 h-3.5 opacity-60"
                />
              </button>
              <button
                v-for="category in catalog.categories"
                :key="category.id"
                type="button"
                class="flex items-center justify-between text-xs font-semibold w-full text-left px-3 py-2 rounded-xl transition-all duration-200"
                :class="
                  route.query.category === category.slug
                    ? 'bg-primary-50 text-primary-700 shadow-sm shadow-primary-600/5'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                "
                @click="selectCategory(category.slug)"
              >
                <span>{{ category.name }}</span>
                <UIcon
                  name="i-heroicons-chevron-right"
                  class="w-3.5 h-3.5 opacity-60"
                />
              </button>
            </div>
          </div>

          <div>
            <h3
              class="text-xs uppercase tracking-wider font-extrabold text-primary-800 border-b border-gray-100 pb-2 mb-3"
            >
              Rentang Harga
            </h3>
            <div class="flex items-center gap-2">
              <UInput
                v-model.number="minPriceInput"
                type="number"
                placeholder="Min"
                size="sm"
                class="w-full focus-within:ring-2 focus-within:ring-primary-500/20"
                @blur="applyPriceRange"
              />
              <span class="text-gray-400 font-bold">-</span>
              <UInput
                v-model.number="maxPriceInput"
                type="number"
                placeholder="Max"
                size="sm"
                class="w-full focus-within:ring-2 focus-within:ring-primary-500/20"
                @blur="applyPriceRange"
              />
            </div>
            <p class="text-[10px] text-gray-400 mt-2 italic font-medium">
              Tekan luar input untuk menerapkan filter harga
            </p>
          </div>
        </aside>

        <!-- Filter drawer (mobile) -->
        <USlideover v-model:open="filterOpen" side="bottom">
          <template #content>
            <div class="p-6 space-y-6 bg-white rounded-t-3xl">
              <div
                class="flex items-center justify-between border-b border-gray-100 pb-3"
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
                  class="rounded-lg"
                  @click="() => { filterOpen = false }"
                />
              </div>

              <div>
                <h4
                  class="text-xs font-extrabold text-primary-800 uppercase tracking-wider mb-2.5"
                >
                  Kategori
                </h4>
                <div class="grid grid-cols-2 gap-2">
                  <button
                    type="button"
                    class="text-xs font-semibold px-3 py-2 rounded-xl text-center border transition-all duration-200"
                    :class="
                      !route.query.category
                        ? 'bg-primary-50 border-primary-200 text-primary-700'
                        : 'border-gray-200 text-gray-600 bg-white'
                    "
                    @click="
                      selectCategory(null);
                      filterOpen = false;
                    "
                  >
                    Semua Kategori
                  </button>
                  <button
                    v-for="category in catalog.categories"
                    :key="category.id"
                    type="button"
                    class="text-xs font-semibold px-3 py-2 rounded-xl text-center border transition-all duration-200 truncate"
                    :class="
                      route.query.category === category.slug
                        ? 'bg-primary-50 border-primary-200 text-primary-700'
                        : 'border-gray-200 text-gray-600 bg-white'
                    "
                    @click="
                      selectCategory(category.slug);
                      filterOpen = false;
                    "
                  >
                    {{ category.name }}
                  </button>
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
                    placeholder="Min"
                    size="sm"
                    class="w-full"
                    @blur="applyPriceRange"
                  />
                  <span class="text-gray-400 font-bold">-</span>
                  <UInput
                    v-model.number="maxPriceInput"
                    type="number"
                    placeholder="Max"
                    size="sm"
                    class="w-full"
                    @blur="applyPriceRange"
                  />
                </div>
              </div>

              <div class="pt-2">
                <UButton
                  block
                  color="primary"
                  size="md"
                  class="font-semibold rounded-full py-2"
                  @click="() => { filterOpen = false }"
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
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white/50 backdrop-blur-sm border border-gray-100 rounded-2xl p-4 shadow-sm"
          >
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">
              {{ catalog.total }} Produk Ditemukan
            </p>
            <div class="flex items-center gap-2">
              <span
                class="text-xs font-semibold text-gray-400 whitespace-nowrap"
                >Urutkan:</span
              >
              <USelect
                :model-value="(route.query.sort as string) || 'newest'"
                :items="sortOptions"
                size="md"
                class="w-44 font-semibold"
                @update:model-value="changeSort"
              />
            </div>
          </div>

          <!-- Product card container -->
          <div
            class="bg-white/60 backdrop-blur-xs border border-primary-100/35 rounded-3xl p-6 shadow-[0_4px_25px_-6px_rgba(255,116,108,0.03)]"
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
