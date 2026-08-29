<script setup lang="ts">
import { catalogService } from "~/services/catalogService";
import type { Product } from "~/types";
import { useApi } from "~/services/api";

const api = useApi();
const featured = ref<Product[]>([]);
const loadingFeatured = ref(true);

const { data: bannersResponse } = useAsyncData("homepage-banners", () =>
  api.getBanners(),
);

interface BannerItem {
  title: string;
  subtitle: string;
  image: string;
  image_mobile?: string | null;
  link?: string;
}

const defaultBanners: BannerItem[] = [
  {
    title: "Belanja Tanpa Batas Negara",
    subtitle:
      "Titip beli produk favoritmu dari Korea, China, Jepang, dan negara lain — aman sampai ke tanganmu.",
    image:
      "https://images.unsplash.com/photo-1607082349566-187342175e2f?auto=format&fit=crop&w=1600&h=600&q=80",
    link: "/katalog",
  },
  {
    title: "Ready Stock, Kirim Langsung",
    subtitle:
      "Barang sudah tersedia di gudang kami — pesan hari ini, diproses tanpa perlu menunggu impor.",
    image:
      "https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?auto=format&fit=crop&w=1600&h=600&q=80",
    link: "/katalog",
  },
  {
    title: "Pre Order Terpercaya",
    subtitle:
      "Kami urus pemesanan langsung dari negara asal sampai bersih ke tanganmu.",
    image:
      "https://images.unsplash.com/photo-1591370874773-6702e8f12fd8?auto=format&fit=crop&w=1600&h=600&q=80",
    link: "/katalog",
  },
];

const banners = computed<BannerItem[]>(() => {
  const apiBanners = (bannersResponse.value?.data || []) as BannerItem[];
  return apiBanners.length > 0 ? apiBanners : defaultBanners;
});

const toast = useToast();

onMounted(async () => {
  try {
    featured.value = await catalogService.getFeaturedProducts(4);
  } catch {
    toast.add({
      title: "Gagal memuat produk unggulan",
      description: "Terjadi kesalahan, coba muat ulang halaman.",
      color: "error",
    });
  } finally {
    loadingFeatured.value = false;
  }
});
</script>

<template>
  <div
    class="relative overflow-hidden bg-linear-to-b from-primary-50/45 via-primary-50/15 to-white min-h-screen"
  >
    <!-- Background cute decor spanning whole page -->
    <AppCuteDecor />
    <!-- Ambient Background Glows -->
    <div
      class="absolute top-20 -left-40 w-96 h-96 bg-primary-200/20 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-150 -right-40 w-96 h-96 bg-primary-100/20 rounded-full blur-3xl pointer-events-none"
    ></div>

    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 relative">
      <div
        class="rounded-3xl overflow-hidden shadow-xl shadow-primary-900/5 border border-primary-100/30"
      >
        <UCarousel
          v-slot="{ item }"
          :items="banners"
          loop
          autoplay
          class="w-full"
        >
          <div class="relative aspect-4/3 sm:aspect-16/5 w-full">
            <img
              :src="item.image_mobile || item.image"
              :alt="item.title"
              class="w-full h-full object-cover sm:hidden"
            />
            <img
              :src="item.image"
              :alt="item.title"
              class="w-full h-full object-cover hidden sm:block"
            />
            <!-- Overlay with linear -->
            <div
              class="absolute inset-0 bg-linear-to-r from-black/75 via-black/45 to-transparent flex flex-col items-start justify-center px-6 sm:px-16 text-white"
            >
              <span
                class="text-[9px] sm:text-xs uppercase tracking-widest text-primary-400 font-extrabold mb-1"
                >BbystarGO</span
                >
              <h2
                class="text-lg sm:text-2xl md:text-4xl font-extrabold max-w-xs sm:max-w-lg tracking-tight leading-snug sm:leading-tight line-clamp-2"
              >
                {{ item.title }}
              </h2>
              <p
                class="text-[10px] sm:text-xs md:text-base mt-1.5 max-w-xs sm:max-w-md text-gray-200 leading-relaxed font-medium line-clamp-2"
              >
                {{ item.subtitle }}
              </p>
              <UButton
                :to="item.link || '/katalog'"
                color="primary"
                class="mt-3.5 sm:mt-5 font-semibold text-xs sm:text-sm px-4 sm:px-6 py-1.5 sm:py-2.5 rounded-full shadow-lg shadow-primary-600/20 hover:scale-105 transition-transform duration-300 cursor-pointer"
                >Belanja Sekarang</UButton
              >
            </div>
          </div>
        </UCarousel>
      </div>
    </section>

    <!-- Trust & Services Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative">
      <div class="text-center max-w-2xl mx-auto mb-10">
        <span
          class="text-[10px] text-primary-500 uppercase font-extrabold tracking-wider"
          >BbystarGO</span
        >
        <p class="text-sm md:text-base text-gray-600 font-medium mt-2 leading-relaxed">
          Toko online sejak 2023 yang menyediakan jasa pembelian barang dari
          Indonesia, Korea, China, Jepang, dan negara lain.
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div
          class="bg-white/80 backdrop-blur-xs border border-primary-100/35 rounded-3xl p-6 shadow-[0_4px_20px_-4px_rgba(255,116,108,0.02)] flex items-start gap-4 hover:shadow-[0_8px_30px_-6px_rgba(255,116,108,0.08)] transition-all duration-300"
        >
          <div
            class="w-11 h-11 shrink-0 rounded-2xl bg-primary-50/80 text-primary-500 border border-primary-100/50 flex items-center justify-center"
          >
            <UIcon name="i-heroicons-archive-box" class="w-5 h-5" />
          </div>
          <div>
            <h3 class="text-sm font-extrabold text-gray-900">Ready Stock</h3>
            <p class="text-xs text-gray-500 font-medium mt-1 leading-relaxed">
              Barang tersedia di gudang kami, siap diproses tanpa menunggu
              impor.
            </p>
          </div>
        </div>
        <div
          class="bg-white/80 backdrop-blur-xs border border-primary-100/35 rounded-3xl p-6 shadow-[0_4px_20px_-4px_rgba(255,116,108,0.02)] flex items-start gap-4 hover:shadow-[0_8px_30px_-6px_rgba(255,116,108,0.08)] transition-all duration-300"
        >
          <div
            class="w-11 h-11 shrink-0 rounded-2xl bg-primary-50/80 text-primary-500 border border-primary-100/50 flex items-center justify-center"
          >
            <UIcon name="i-heroicons-globe-asia-australia" class="w-5 h-5" />
          </div>
          <div>
            <h3 class="text-sm font-extrabold text-gray-900">Pre Order</h3>
            <p class="text-xs text-gray-500 font-medium mt-1 leading-relaxed">
              Pemesanan langsung dari negara asal, kami urus bersih sampai ke
              tanganmu.
            </p>
          </div>
        </div>
      </div>

      <div
        class="bg-white/70 backdrop-blur-xs border border-primary-100/35 rounded-3xl p-6 shadow-[0_4px_20px_-4px_rgba(255,116,108,0.02)] flex flex-wrap items-center justify-center gap-x-12 gap-y-4 text-center"
      >
        <div>
          <p class="text-lg font-black text-primary-500">100+</p>
          <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">
            Customer Terlayani
          </p>
        </div>
        <div class="h-8 w-px bg-primary-100/60 hidden sm:block"></div>
        <div>
          <p class="text-lg font-black text-primary-500">5.0 ★</p>
          <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">
            Rating Shopee
          </p>
        </div>
        <div class="h-8 w-px bg-primary-100/60 hidden sm:block"></div>
        <div>
          <p class="text-lg font-black text-primary-500">2.500+</p>
          <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">
            Penilaian
          </p>
        </div>
      </div>
    </section>

    <!-- Featured Products Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative">

      <div
        class="flex items-end justify-between mb-8 border-b border-gray-100 pb-4"
      >
        <div>
          <h2
            class="text-xl md:text-2xl font-extrabold text-gray-900 tracking-tight"
          >
            Produk Unggulan
          </h2>
          <p class="text-xs md:text-sm text-gray-500 mt-1 font-medium">
            Pilihan produk Ready Stock &amp; Pre Order terlaris dari kami
          </p>
        </div>
        <NuxtLink
          to="/katalog"
          class="text-sm text-primary-700 hover:text-primary-800 font-semibold flex items-center gap-1 group transition-colors"
        >
          Lihat semua
          <UIcon
            name="i-heroicons-arrow-right"
            class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200"
          />
        </NuxtLink>
      </div>

      <!-- Main product grid inside a styled card container for depth -->
      <div
        class="bg-white/70 backdrop-blur-xs border border-primary-100/35 rounded-3xl p-6 shadow-[0_4px_25px_-6px_rgba(255,116,108,0.03)]"
      >
        <ProductGrid
          :products="featured"
          :loading="loadingFeatured"
          :has-more="false"
        />
      </div>
    </section>
  </div>
</template>
