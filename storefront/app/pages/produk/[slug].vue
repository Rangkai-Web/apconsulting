<script setup lang="ts">
import { catalogService } from "~/services/catalogService";
import { useCartStore } from "~/store/cart";
import { useWishlistStore } from "~/store/wishlist";
import { useCurrency } from "~/composables/useCurrency";
import type { Product, ProductVariant } from "~/types";

const route = useRoute();
const cart = useCartStore();
const wishlist = useWishlistStore();
const toast = useToast();
const { format } = useCurrency();

const product = ref<Product | null>(null);
const loading = ref(true);
const loadError = ref(false);
const selectedVariantId = ref<number | null>(null);
const qty = ref(1);

const carouselRef = ref<any>(null);
const activeIndex = ref(0);

function onSelect(index: number) {
  activeIndex.value = index;
}

function selectImage(index: number) {
  activeIndex.value = index;
  carouselRef.value?.emblaApi?.scrollTo(index);
}

watch(selectedVariantId, () => {
  activeIndex.value = 0;
  nextTick(() => {
    carouselRef.value?.emblaApi?.scrollTo(0);
  });
});

const selectedVariant = computed<ProductVariant | null>(() => {
  if (!product.value) return null;
  return (
    product.value.variants.find((v) => v.id === selectedVariantId.value) ?? null
  );
});

const gallery = computed(() => {
  if (!product.value) return [];
  const variantImage = selectedVariant.value?.image;
  return variantImage
    ? [variantImage, ...product.value.images]
    : product.value.images;
});

const outOfStock = computed(() => selectedVariant.value?.stock === 0);

watch(selectedVariant, () => {
  qty.value = 1;
});

async function load() {
  loading.value = true;
  loadError.value = false;
  try {
    product.value = await catalogService.getProductBySlug(
      route.params.slug as string,
    );
    if (product.value) {
      const firstInStock = product.value.variants.find((v) => v.stock > 0);
      selectedVariantId.value =
        firstInStock?.id ?? product.value.variants[0]?.id ?? null;
    }
  } catch (error) {
    console.error("Gagal memuat produk:", error);
    loadError.value = true;
    toast.add({
      title: "Gagal Memuat Produk",
      description: "Terjadi kesalahan koneksi. Silakan coba lagi.",
      color: "error",
    });
  } finally {
    loading.value = false;
  }
}

function addToCart() {
  if (!product.value || !selectedVariant.value || outOfStock.value) return;
  cart.addItem(product.value, selectedVariant.value, qty.value);
  toast.add({
    title: "Ditambahkan ke keranjang",
    description: `${product.value.name} (${selectedVariant.value.name})`,
    color: "primary",
  });
}

onMounted(load);
watch(() => route.params.slug, load);
</script>

<template>
  <div
    class="relative overflow-hidden bg-linear-to-b from-primary-50/45 via-primary-50/15 to-white min-h-screen py-10"
  >
    <!-- Background cute decor spanning whole page -->
    <AppCuteDecor />

    <!-- Ambient Background Glows -->
    <div
      class="absolute top-12 -left-40 w-96 h-96 bg-primary-200/20 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-[500px] -right-40 w-96 h-96 bg-primary-100/20 rounded-full blur-3xl pointer-events-none"
    ></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
      <!-- Breadcrumb Link Back -->
      <div class="mb-6">
        <NuxtLink
          to="/katalog"
          class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-primary-700 uppercase tracking-wider transition-colors duration-200"
        >
          <UIcon name="i-heroicons-arrow-left" class="w-3.5 h-3.5" />
          Kembali Ke Katalog
        </NuxtLink>
      </div>

      <!-- Loading State -->
      <div
        v-if="loading"
        class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-white/40 backdrop-blur-sm border border-gray-100 rounded-3xl p-6 md:p-8 shadow-sm"
      >
        <USkeleton class="aspect-square rounded-2xl w-full" />
        <div class="space-y-4">
          <USkeleton class="h-8 w-3/4 rounded-lg" />
          <USkeleton class="h-4 w-1/3 rounded-lg" />
          <USkeleton class="h-28 w-full rounded-xl" />
          <USkeleton class="h-10 w-full rounded-xl" />
        </div>
      </div>

      <!-- Load Error State (network/500 — not a real 404) -->
      <div
        v-else-if="loadError"
        class="max-w-md mx-auto bg-white/60 backdrop-blur-sm border border-gray-100 rounded-3xl p-8 shadow-sm"
      >
        <EmptyState
          icon="i-heroicons-wifi"
          title="Gagal memuat produk"
          description="Terjadi kesalahan koneksi saat memuat halaman ini. Coba lagi."
          action-label="Coba Lagi"
          @action="load"
        />
      </div>

      <!-- Not Found State -->
      <div
        v-else-if="!product"
        class="max-w-md mx-auto bg-white/60 backdrop-blur-sm border border-gray-100 rounded-3xl p-8 shadow-sm"
      >
        <EmptyState
          icon="i-heroicons-exclamation-triangle"
          title="Produk tidak ditemukan"
          description="Produk yang Anda cari tidak tersedia atau telah dihapus."
          action-label="Kembali ke Katalog"
          action-to="/katalog"
        />
      </div>

      <!-- Product Content -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
        <!-- Left: Product Image Carousel -->
        <div
          class="bg-white/80 border border-gray-100/80 rounded-3xl p-4 md:p-6 shadow-sm shadow-primary-900/5 self-start"
        >
          <UCarousel
            ref="carouselRef"
            v-slot="{ item }"
            :items="gallery"
            arrows
            class="relative w-full"
            :ui="{
              viewport:
                'rounded-2xl overflow-hidden bg-gray-50 border border-gray-100/40',
            }"
            @select="onSelect"
          >
            <img
              :src="item"
              :alt="product.name"
              class="w-full aspect-square object-cover hover:scale-102 transition-transform duration-500"
            />
          </UCarousel>

          <!-- Thumbnail Navigation Row -->
          <div
            v-if="gallery.length > 1"
            class="flex flex-wrap gap-2 mt-4 justify-center"
          >
            <button
              v-for="(item, index) in gallery"
              :key="index"
              type="button"
              class="w-14 h-14 rounded-xl overflow-hidden bg-gray-50 border-2 transition-all duration-200 hover:scale-105 active:scale-95 focus:outline-none shadow-sm shrink-0"
              :class="
                activeIndex === index
                  ? 'border-primary-600 opacity-100 scale-102 shadow-md shadow-primary-600/10'
                  : 'border-transparent opacity-50 hover:opacity-80'
              "
              @click="selectImage(index)"
            >
              <img
                :src="item"
                :alt="`${product.name} thumbnail ${index + 1}`"
                class="w-full h-full object-cover"
              />
            </button>
          </div>
        </div>

        <!-- Right: Details -->
        <div
          class="bg-white/80 backdrop-blur-sm border border-gray-100 rounded-3xl p-6 md:p-8 shadow-sm space-y-6 relative overflow-hidden self-start"
        >

          <!-- Title & Rating & Wishlist -->
          <div
            class="flex items-start justify-between gap-4 border-b border-gray-100 pb-5"
          >
            <div>
              <span
                class="text-[10px] text-primary-600 uppercase font-extrabold tracking-widest"
                >Detail Produk</span
              >
              <h1
                class="text-xl md:text-2xl font-extrabold text-gray-900 tracking-tight leading-tight mt-0.5"
              >
                {{ product.name }}
              </h1>
              <RatingStars
                :rating="product.ratingAvg"
                :count="product.ratingCount"
                size="md"
                class="mt-2"
              />
            </div>
            <UButton
              :icon="
                wishlist.isWishlisted(product.id)
                  ? 'i-heroicons-heart-solid'
                  : 'i-heroicons-heart'
              "
              :color="wishlist.isWishlisted(product.id) ? 'error' : 'neutral'"
              variant="subtle"
              class="rounded-2xl shadow-sm p-3 group transition-transform duration-300 hover:scale-105 active:scale-95 shrink-0"
              :aria-label="
                wishlist.isWishlisted(product.id)
                  ? 'Hapus dari wishlist'
                  : 'Tambah ke wishlist'
              "
              :aria-pressed="wishlist.isWishlisted(product.id)"
              @click="wishlist.toggle(product.id)"
            />
          </div>

          <!-- Price Display & Fulfillment Info -->
          <div
            class="bg-primary-50/40 border border-primary-100/50 rounded-2xl p-4 flex flex-col gap-2.5 justify-center"
          >
            <div class="flex items-center justify-between gap-4">
              <div>
                <span
                  class="text-[10px] text-primary-700 font-extrabold uppercase tracking-wider"
                  >Harga Varian</span
                >
                <p
                  class="text-2xl md:text-3xl font-black text-primary-700 tracking-tight mt-0.5"
                >
                  {{ selectedVariant ? format(selectedVariant.price) : "-" }}
                </p>
              </div>
              <div v-if="selectedVariant" class="text-right">
                <span
                  v-if="selectedVariant.fulfillmentType === 'pre_order'"
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-orange-100 text-orange-700 border border-orange-200"
                >
                  <UIcon name="i-heroicons-clock-solid" class="w-3.5 h-3.5" />
                  PO {{ selectedVariant.poDays }} Hari
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-700 border border-emerald-200"
                >
                  <UIcon name="i-heroicons-check-circle-solid" class="w-3.5 h-3.5" />
                  Ready Stock
                </span>
              </div>
            </div>

            <!-- DP Info for PO -->
            <div
              v-if="selectedVariant && selectedVariant.fulfillmentType === 'pre_order'"
              class="border-t border-primary-100/60 pt-2 flex items-center justify-between text-xs font-extrabold text-primary-850"
            >
              <span>Uang Muka (DP):</span>
              <span class="text-sm font-black text-primary-700">{{ format(selectedVariant.dpAmount) }}</span>
            </div>
          </div>

          <!-- Description -->
          <div class="space-y-2">
            <h3
              class="text-xs uppercase tracking-wider font-extrabold text-primary-800 border-l-2 border-primary-500 pl-2"
            >
              Deskripsi
            </h3>
            <p
              class="text-xs md:text-sm text-gray-600 leading-relaxed font-medium"
              v-html="product.description"
            ></p>
          </div>

          <!-- Variant Selector -->
          <div class="space-y-3">
            <h3
              class="text-xs uppercase tracking-wider font-extrabold text-primary-800 border-l-2 border-primary-500 pl-2"
            >
              Pilih Varian
            </h3>
            <VariantSelector
              v-model="selectedVariantId"
              :variants="product.variants"
            />
          </div>

          <!-- Quantity Stepper -->
          <div
            v-if="!outOfStock"
            class="flex items-center gap-4 bg-gray-50/50 border border-gray-100 rounded-2xl p-3 w-fit"
          >
            <span class="text-xs font-bold text-gray-500 pl-1">Jumlah:</span>
            <QtyStepper v-model="qty" :max="selectedVariant?.stock ?? 1" />
            <span
              class="text-xs font-semibold text-gray-400 border-l border-gray-200 pl-3"
              >Stok:
              <strong class="text-gray-700">{{
                selectedVariant?.stock
              }}</strong></span
            >
          </div>

          <!-- Buy Button & Shopee Link Button -->
          <div class="pt-2 space-y-3">
            <UButton
              block
              color="primary"
              size="lg"
              icon="i-heroicons-shopping-cart"
              :disabled="outOfStock"
              class="font-semibold py-3.5 rounded-full cursor-pointer shadow-lg shadow-primary-600/20 hover:shadow-primary-600/30 transform hover:scale-[1.01] transition-all duration-300"
              @click="addToCart"
            >
              {{ outOfStock ? "Stok Habis" : "Tambah ke Keranjang" }}
            </UButton>

            <a
              v-if="product.shopeeUrl"
              :href="product.shopeeUrl"
              target="_blank"
              rel="noopener"
              class="flex items-center justify-center gap-2 w-full py-3.5 bg-orange-600 text-white font-bold rounded-full shadow-lg shadow-orange-600/20 hover:bg-orange-500 hover:shadow-orange-600/30 transform hover:scale-[1.01] transition-all duration-300 text-sm"
            >
              <UIcon name="i-simple-icons-shopee" class="w-5 h-5" />
              Beli di Shopee
            </a>
          </div>

          <!-- Trust Badges -->
          <div
            class="grid grid-cols-3 gap-2.5 pt-4 border-t border-gray-100 text-center"
          >
            <div
              class="flex flex-col items-center p-2 rounded-xl bg-gray-50/30 hover:bg-primary-50/30 transition-colors duration-200"
            >
              <UIcon
                name="i-heroicons-shield-check"
                class="w-5 h-5 text-primary-600 mb-1"
              />
              <span class="text-[9px] font-extrabold text-gray-800"
                >100% Asli</span
              >
            </div>
            <div
              class="flex flex-col items-center p-2 rounded-xl bg-gray-50/30 hover:bg-primary-50/30 transition-colors duration-200"
            >
              <UIcon
                name="i-heroicons-truck"
                class="w-5 h-5 text-primary-600 mb-1"
              />
              <span class="text-[9px] font-extrabold text-gray-800"
                >Kirim Cepat</span
              >
            </div>
            <div
              class="flex flex-col items-center p-2 rounded-xl bg-gray-50/30 hover:bg-primary-50/30 transition-colors duration-200"
            >
              <UIcon
                name="i-heroicons-chat-bubble-left-right"
                class="w-5 h-5 text-primary-600 mb-1"
              />
              <span class="text-[9px] font-extrabold text-gray-800"
                >Konsultasi</span
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
