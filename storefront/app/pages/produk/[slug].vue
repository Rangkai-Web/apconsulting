<script setup lang="ts">
import { catalogService } from "~/services/catalogService";
import { useCartStore } from "~/store/cart";
import { useWishlistStore } from "~/store/wishlist";
import { useCurrency } from "~/composables/useCurrency";
import { useSeoText } from "~/composables/useSeoText";
import type { Product, ProductVariant } from "~/types";

const route = useRoute();
const cart = useCartStore();
const wishlist = useWishlistStore();
const toast = useToast();
const { format } = useCurrency();
const { toPlainText, toSafeJsonLd } = useSeoText();
const config = useRuntimeConfig();

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

const plainDescription = computed(() =>
  product.value ? toPlainText(product.value.description) : "",
);

const accordionItems = computed(() => {
  return [
    {
      label: "Deskripsi Produk",
      icon: "i-heroicons-document-text",
      slot: "description",
      defaultOpen: true,
    },
    {
      label: "Jaminan & Informasi Pengiriman",
      icon: "i-heroicons-shield-check",
      slot: "shipping",
    },
    {
      label: "Ketentuan Layanan (Ready & PO)",
      icon: "i-heroicons-bookmark-square",
      slot: "policies",
    },
  ];
});

const productUrl = computed(
  () => `${config.public.siteUrl}/produk/${route.params.slug}`,
);

useSeoMeta({
  title: () => product.value?.name,
  description: () => plainDescription.value,
  ogTitle: () => product.value?.name,
  ogDescription: () => plainDescription.value,
  ogImage: () => product.value?.images[0],
  ogUrl: () => productUrl.value,
});

useHead(() => ({
  link: [{ rel: "canonical", href: productUrl.value }],
  // og:type "product" isn't in unhead's typed ogType union, set it directly.
  meta: [{ property: "og:type", content: "product" }],
  script: product.value
    ? [
        {
          type: "application/ld+json",
          innerHTML: toSafeJsonLd({
            "@context": "https://schema.org",
            "@type": "Product",
            name: product.value.name,
            image: product.value.images,
            description: plainDescription.value,
            sku: product.value.variants[0]?.sku,
            offers: product.value.variants.map((v) => ({
              "@type": "Offer",
              price: v.price,
              priceCurrency: "IDR",
              availability:
                v.stock > 0
                  ? "https://schema.org/InStock"
                  : "https://schema.org/OutOfStock",
              url: productUrl.value,
            })),
            ...(product.value.ratingCount > 0
              ? {
                  aggregateRating: {
                    "@type": "AggregateRating",
                    ratingValue: product.value.ratingAvg,
                    reviewCount: product.value.ratingCount,
                  },
                }
              : {}),
          }),
        },
      ]
    : [],
}));
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
        class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-white backdrop-blur-sm border border-primary-100 rounded-3xl p-6 md:p-8 shadow-sm"
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
        class="max-w-md mx-auto bg-white backdrop-blur-sm border border-primary-100 rounded-3xl p-8 shadow-sm"
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
        class="max-w-md mx-auto bg-white backdrop-blur-sm border border-primary-100 rounded-3xl p-8 shadow-sm"
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
      <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
        <!-- Left: Sticky Product Image Gallery -->
        <div
          class="lg:col-span-6 lg:sticky self-start flex flex-col-reverse md:flex-row gap-4"
        >
          <!-- Thumbnail navigation (desktop vertical, mobile horizontal) -->
          <div
            v-if="gallery.length > 1"
            class="flex md:flex-col gap-2.5 overflow-x-auto md:overflow-x-visible md:overflow-y-auto shrink-0 pb-2 md:pb-0 justify-start max-h-[450px] scrollbar-none"
          >
            <button
              v-for="(item, index) in gallery"
              :key="index"
              type="button"
              class="w-14 h-14 rounded-xl overflow-hidden bg-gray-50 border-2 transition-all duration-200 hover:scale-105 active:scale-95 focus:outline-none shadow-xs shrink-0"
              :class="
                activeIndex === index
                  ? 'border-primary-500 opacity-100 scale-102 shadow-md shadow-primary-500/10'
                  : 'border-transparent opacity-60 hover:opacity-90'
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

          <!-- Main Large Image Carousel -->
          <div
            class="grow bg-white border border-primary-100/50 rounded-3xl p-4 shadow-xs"
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
          </div>
        </div>

        <!-- Right: Details & Checkout Actions -->
        <div class="lg:col-span-6 space-y-6">
          <!-- Main Purchase card -->
          <div
            class="bg-white border border-primary-100/50 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6 relative overflow-hidden"
          >
            <!-- Title & Rating & Wishlist -->
            <div
              class="flex items-start justify-between gap-4 border-b border-primary-50 pb-5"
            >
              <div>
                <span
                  class="text-[10px] text-primary-600 uppercase font-extrabold tracking-widest"
                >
                  Detail Produk
                </span>
                <h1
                  class="text-xl md:text-2xl font-black text-gray-900 tracking-tight leading-tight mt-0.5"
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
                class="rounded-2xl shadow-xs p-3 group transition-transform duration-300 hover:scale-105 active:scale-95 shrink-0 cursor-pointer"
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
              class="bg-primary-50/20 border border-primary-100/40 rounded-2xl p-4 flex flex-col gap-2.5 justify-center"
            >
              <div class="flex items-center justify-between gap-4">
                <div>
                  <span
                    class="text-[10px] text-primary-700 font-extrabold uppercase tracking-wider"
                  >
                    Harga Varian
                  </span>
                  <p
                    class="text-2xl md:text-3xl font-black text-primary-600 tracking-tight mt-0.5"
                  >
                    {{ selectedVariant ? format(selectedVariant.price) : "-" }}
                  </p>
                </div>
                <div v-if="selectedVariant" class="text-right">
                  <span
                    v-if="selectedVariant.fulfillmentType === 'pre_order'"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black bg-orange-50 text-orange-600 border border-orange-100"
                  >
                    <UIcon name="i-heroicons-clock-solid" class="w-3.5 h-3.5" />
                    PO {{ selectedVariant.poDays }} Hari
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black bg-emerald-50 text-emerald-600 border border-emerald-100"
                  >
                    <UIcon
                      name="i-heroicons-check-circle-solid"
                      class="w-3.5 h-3.5"
                    />
                    Ready Stock
                  </span>
                </div>
              </div>

              <!-- DP Info for PO -->
              <div
                v-if="
                  selectedVariant &&
                  selectedVariant.fulfillmentType === 'pre_order'
                "
                class="border-t border-primary-100/40 pt-2.5 flex items-center justify-between text-xs font-extrabold text-primary-800"
              >
                <span>Uang Muka (DP):</span>
                <span class="text-sm font-black text-primary-600">
                  {{ format(selectedVariant.dpAmount) }}
                </span>
              </div>
            </div>

            <!-- Variant Selector -->
            <div class="space-y-3">
              <h3
                class="text-xs uppercase tracking-wider font-extrabold text-primary-800 border-l-2 border-primary-500 pl-2"
              >
                Pilih Varian
              </h3>
              <VariantSelector
                v-slot:default="_"
                v-model="selectedVariantId"
                :variants="product.variants"
              />
            </div>

            <!-- Quantity Stepper -->
            <div
              v-if="!outOfStock"
              class="flex items-center gap-4 bg-gray-50/50 border border-gray-100/50 rounded-2xl p-3 w-fit"
            >
              <span class="text-xs font-bold text-gray-500 pl-1">Jumlah:</span>
              <QtyStepper v-model="qty" :max="selectedVariant?.stock ?? 1" />
              <span
                class="text-xs font-semibold text-gray-400 border-l border-gray-200 pl-3"
              >
                Stok:
                <strong class="text-gray-700">{{
                  selectedVariant?.stock
                }}</strong>
              </span>
            </div>

            <!-- Buy Button & Shopee Link Button -->
            <div class="pt-2 space-y-3">
              <UButton
                block
                color="primary"
                size="lg"
                icon="i-heroicons-shopping-cart"
                :disabled="outOfStock"
                class="font-extrabold py-3.5 rounded-full cursor-pointer shadow-lg shadow-primary-600/20 hover:shadow-primary-600/30 transform hover:scale-[1.01] transition-all duration-300"
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
          </div>
        </div>

        <!-- Product Details Accordion (Description, Shipping Info, Jastip Policies) -->
        <div
          class="lg:col-span-12 bg-white border border-primary-100/40 rounded-3xl p-5 shadow-xs mt-2 lg:mt-6"
        >
            <UAccordion
              :items="accordionItems"
              :ui="{
                root: 'w-full space-y-2',
                item: 'border border-primary-50 rounded-2xl overflow-hidden shadow-xs bg-primary-50/5',
                trigger:
                  'py-3.5 px-5 text-left text-xs font-bold text-gray-800 hover:text-primary-500 focus-visible:outline-none flex justify-between items-center transition-colors',
                content:
                  'px-5 text-xs text-gray-655 leading-relaxed font-medium bg-white border-t border-primary-50/30 py-0',
              }"
            >
              <template #description>
                <div class="py-4 space-y-2" v-html="product.description"></div>
              </template>
              <template #shipping>
                <div class="py-4">
                  <ul class="space-y-2.5">
                    <li class="flex items-start gap-2">
                      <UIcon
                        name="i-heroicons-shield-check"
                        class="w-4 h-4 text-primary-500 shrink-0 mt-0.5"
                      />
                      <span
                        ><strong>100% Produk Resmi &amp; Asli</strong>: Kami jamin
                        semua barang diimpor langsung dari official store resmi
                        asal negara masing-masing.</span
                      >
                    </li>
                    <li class="flex items-start gap-2">
                      <UIcon
                        name="i-heroicons-gift"
                        class="w-4 h-4 text-primary-500 shrink-0 mt-0.5"
                      />
                      <span
                        ><strong>Free Safety Packing</strong>: Proteksi tebal
                        menggunakan bubble wrap berkualitas dan kardus ekstra kuat
                        secara gratis.</span
                      >
                    </li>
                    <li class="flex items-start gap-2">
                      <UIcon
                        name="i-heroicons-truck"
                        class="w-4 h-4 text-primary-500 shrink-0 mt-0.5"
                      />
                      <span
                        ><strong>Pengiriman Aman</strong>: Pengiriman diproses
                        sesegera mungkin dengan nomor resi pelacakan yang
                        valid.</span
                      >
                    </li>
                  </ul>
                </div>
              </template>
              <template #policies>
                <div class="py-4">
                  <ul class="space-y-2.5">
                    <li class="flex items-start gap-2">
                      <UIcon
                        name="i-heroicons-check-circle"
                        class="w-4 h-4 text-primary-500 shrink-0 mt-0.5"
                      />
                      <span
                        ><strong>Ready Stock</strong>: Barang langsung dikirim
                        dari gudang kami di hari yang sama untuk transaksi sebelum
                        jam operasional berakhir.</span
                      >
                    </li>
                    <li class="flex items-start gap-2">
                      <UIcon
                        name="i-heroicons-clock"
                        class="w-4 h-4 text-primary-500 shrink-0 mt-0.5"
                      />
                      <span
                        ><strong>Pre Order (PO)</strong>: Barang akan dipesan
                        terlebih dahulu dengan estimasi waktu pengiriman berkisar
                        antara jumlah hari yang tertera.</span
                      >
                    </li>
                    <li class="flex items-start gap-2">
                      <UIcon
                        name="i-heroicons-banknotes"
                        class="w-4 h-4 text-primary-500 shrink-0 mt-0.5"
                      />
                      <span
                        ><strong>Sistem Uang Muka (DP)</strong>: Pembayaran awal
                        sesuai nominal DP. Setelah barang tiba di gudang
                        Indonesia, Anda dapat melunasinya untuk pengiriman
                        lokal.</span
                      >
                    </li>
                  </ul>
                </div>
              </template>
            </UAccordion>
          </div>
      </div>
    </div>
  </div>
</template>
