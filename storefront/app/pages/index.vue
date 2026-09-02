<script setup lang="ts">
import { catalogService } from "~/services/catalogService";
import type { Product } from "~/types";
import { useApi } from "~/services/api";
import { useCatalogStore } from "~/store/catalog";

const api = useApi();
const catalogStore = useCatalogStore();
const featured = ref<Product[]>([]);
const loadingFeatured = ref(true);
const categoriesList = ref<any[]>([]);
const categoriesContainer = ref<HTMLElement | null>(null);

function scrollCategories(direction: "left" | "right") {
  if (!categoriesContainer.value) return;
  const scrollAmount = categoriesContainer.value.clientWidth * 0.75;
  categoriesContainer.value.scrollBy({
    left: direction === "left" ? -scrollAmount : scrollAmount,
    behavior: "smooth",
  });
}

const { data: bannersResponse } = useAsyncData("homepage-banners", () =>
  api.getBanners(),
);

interface BannerItem {
  title: string;
  subtitle: string;
  image: string;
  link?: string;
}

const defaultBanners: BannerItem[] = [
  {
    title: "Temukan Merchandise Favoritmu",
    subtitle:
      "Penyedia jasa pembelian barang favoritmu dari Korea, China, Jepang, dan negara lain — aman sampai ke tanganmu.",
    image: "/redesign_hero.jpg",
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
const config = useRuntimeConfig();

useSeoMeta({
  title: "Jasa Pembelian Barang & Ready Stock Korea, China, Jepang",
  description:
    "BbystarGO — toko online yang menyediakan jasa pembelian barang dari Indonesia, Korea, China, Jepang, dan negara lain sejak 2023. Ready Stock & Pre Order, aman dan terpercaya.",
  ogTitle: "BbystarGO - Jasa Pembelian Barang & Ready Stock Korea, China, Jepang",
  ogDescription:
    "Belanja Ready Stock & Pre Order dari Korea, China, Jepang, dan negara lain — aman sampai ke tanganmu.",
  ogImage: `${config.public.siteUrl}/BbystarGO_1.jpg`,
});

const faqs = [
  {
    label: "Bagaimana cara melakukan pemesanan di BbystarGO?",
    content:
      "Pilih produk yang Anda inginkan melalui menu Katalog, masukkan ke keranjang belanja, lalu selesaikan proses checkout dengan mengisi detail alamat dan melakukan pembayaran sesuai metode yang dipilih.",
  },
  {
    label: "Berapa lama estimasi pengiriman barang Pre Order?",
    content:
      "Pengiriman barang Pre Order (PO) bervariasi bergantung negara asal, umumnya memakan waktu 2-4 minggu sejak tanggal tutup PO. Informasi lengkap akan diupdate secara berkala.",
  },
  {
    label: "Apakah semua produk dijamin original?",
    content:
      "Ya, kami menjamin 100% keaslian semua barang yang kami belikan langsung dari official store maupun merchant terpercaya di Korea, China, Jepang, dan negara lainnya.",
  },
  {
    label: "Bagaimana kebijakan pengembalian barang?",
    content:
      "Pengembalian barang hanya diterima apabila terdapat kesalahan pengiriman produk atau cacat produksi mayor yang disertai dengan video unboxing lengkap tanpa terputus.",
  },
];

const testimonials = [
  {
    name: "n*****8",
    rating: 5,
    text: "Makasih ka freebiesnya, ini peletnya gimana dah ya bisa dapet yejun satu set, makasih banyak kaa dah bantu bawa pulang bujang-bujangku inii",
  },
  {
    name: "jualan.kelly",
    rating: 5,
    text: "timakaciiii udah bantu bawa pulang anak pentol ini kak yul~",
  },
  {
    name: "s*****z",
    rating: 5,
    text: "paket sampai dengan selamat~~~ packingnya rapih dan aman sekali (padahal isinya sedikit wkwkwk). terima kasih kakk",
  },
];

onMounted(async () => {
  try {
    featured.value = await catalogService.getFeaturedProducts(4);
    await catalogStore.loadCategories();

    // Filter and dynamically map parent categories with visual style fallbacks
    categoriesList.value = catalogStore.categories
      .filter((cat) => !cat.parentId)
      .map((cat) => {
        const name = cat.name.toLowerCase();
        let icon = cat.icon || "i-heroicons-squares-2x2";
        let bg = "bg-primary-50 text-primary-500 border-primary-100";

        // Dynamic styling (background colors) based on icon if set, or name keywords
        if (
          icon === "i-heroicons-key" ||
          name.includes("keychain") ||
          name.includes("gantungan")
        ) {
          icon = "i-heroicons-key";
          bg = "bg-pink-50 text-pink-500 border-pink-100";
        } else if (
          icon === "i-heroicons-sparkles" ||
          name.includes("sticker") ||
          name.includes("stiker")
        ) {
          icon = "i-heroicons-sparkles";
          bg = "bg-amber-50 text-amber-500 border-amber-100";
        } else if (
          icon === "i-heroicons-shopping-bag" ||
          name.includes("bag") ||
          name.includes("tas")
        ) {
          icon = "i-heroicons-shopping-bag";
          bg = "bg-emerald-50 text-emerald-500 border-emerald-100";
        } else if (
          icon === "i-heroicons-user" ||
          name.includes("shirt") ||
          name.includes("kaos") ||
          name.includes("baju")
        ) {
          icon = "i-heroicons-user";
          bg = "bg-indigo-50 text-indigo-500 border-indigo-100";
        } else if (
          icon === "i-heroicons-gift" ||
          name.includes("access") ||
          name.includes("aksesoris")
        ) {
          icon = "i-heroicons-gift";
          bg = "bg-purple-50 text-purple-500 border-purple-100";
        } else if (
          icon === "i-heroicons-pencil-square" ||
          name.includes("station") ||
          name.includes("alat tulis")
        ) {
          icon = "i-heroicons-pencil-square";
          bg = "bg-blue-50 text-blue-500 border-blue-100";
        } else if (
          icon === "i-heroicons-heart" ||
          name.includes("koleksi") ||
          name.includes("fandom") ||
          name.includes("penggemar")
        ) {
          icon = "i-heroicons-heart";
          bg = "bg-rose-50 text-rose-500 border-rose-100";
        } else if (icon === "i-heroicons-star") {
          bg = "bg-yellow-50 text-yellow-600 border-yellow-100";
        } else if (icon === "i-heroicons-tag") {
          bg = "bg-red-50 text-red-500 border-red-100";
        } else if (icon === "i-heroicons-bookmark") {
          bg = "bg-teal-50 text-teal-600 border-teal-100";
        }

        return {
          name: cat.name,
          icon,
          bg,
          slug: cat.slug,
        };
      });
  } catch {
    toast.add({
      title: "Gagal memuat data toko",
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
    class="relative overflow-hidden bg-linear-to-b from-primary-50/35 via-primary-50/15 to-primary-50/5 min-h-screen"
  >
    <!-- Background cute decor spanning whole page -->
    <AppCuteDecor />

    <!-- Ambient Background Glows -->
    <div
      class="absolute top-20 -left-40 w-96 h-96 bg-primary-200/10 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-150 -right-40 w-96 h-96 bg-primary-100/10 rounded-full blur-3xl pointer-events-none"
    ></div>

    <!-- Hero Section -->
    <section
      class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16 relative z-20"
    >
      <UCarousel
        v-slot="{ item }"
        :items="banners"
        :loop="true"
        autoplay
        dots
        class="w-full"
      >
        <div
          class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center w-full px-2"
        >
          <!-- Hero Left Column -->
          <div class="lg:col-span-5 space-y-6 text-left">
            <div
              class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-primary-50 border border-primary-100/60 text-[11px] font-extrabold text-primary-500 tracking-wide uppercase"
            >
              Terpercaya dan Berkualitas
            </div>
            <h1
              class="text-3xl sm:text-4xl md:text-5xl font-black text-gray-900 tracking-tight leading-[1.1] min-h-[90px] md:min-h-[150px] flex items-center"
            >
              {{ item.title }}
            </h1>
            <p
              class="text-xs sm:text-sm md:text-base text-gray-500 font-semibold leading-relaxed max-w-lg min-h-[60px] md:min-h-[80px]"
            >
              {{ item.subtitle }}
            </p>
            <div class="flex flex-wrap items-center gap-3 pt-2">
              <UButton
                :to="item.link || '/katalog'"
                color="primary"
                variant="solid"
                class="font-extrabold text-xs sm:text-sm px-6 py-3 rounded-full shadow-lg shadow-primary-500/20 hover:scale-105 transition-all duration-300 cursor-pointer"
              >
                Pesan Sekarang
              </UButton>
              <UButton
                to="/faq"
                color="primary"
                variant="outline"
                class="font-extrabold text-xs sm:text-sm px-6 py-3 rounded-full hover:bg-primary-50/50 transition-all duration-300 cursor-pointer"
              >
                Panduan Belanja
              </UButton>
            </div>
            <div
              class="flex flex-wrap items-center gap-6 pt-4 text-xs font-bold text-gray-400"
            >
              <div class="flex items-center gap-2">
                <UIcon
                  name="i-heroicons-check-badge"
                  class="w-5 h-5 text-primary-400"
                />
                Kualitas Terjamin
              </div>
              <div class="flex items-center gap-2">
                <UIcon
                  name="i-heroicons-shield-check"
                  class="w-5 h-5 text-primary-400"
                />
                Aman dan Cepat
              </div>
            </div>
          </div>

          <!-- Hero Right Column -->
          <div class="lg:col-span-7 relative flex justify-center w-full">
            <div
              class="w-full max-w-xl aspect-square sm:aspect-16/13 rounded-[2rem] overflow-hidden shadow-2xl shadow-primary-900/10 border-4 border-white relative group"
            >
              <img
                :src="item.image"
                :alt="item.title"
                class="w-full h-full object-cover group-hover:scale-103 transition-all duration-700"
              />
              <div
                class="absolute inset-0 bg-linear-to-t from-black/25 via-transparent to-transparent"
              ></div>
              <!-- Floating Badge -->
              <!-- <div
                class="absolute bottom-4 left-4 bg-white/95 backdrop-blur-xs border border-primary-100/60 rounded-2xl py-2 px-4 shadow-lg flex items-center gap-2"
              >
                <span
                  class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"
                ></span>
                <span
                  class="text-[10px] font-extrabold text-gray-800 uppercase tracking-wider"
                  >Ready Stock Tersedia</span
                >
              </div> -->
            </div>
          </div>
        </div>
      </UCarousel>
    </section>

    <!-- Brand Overview & Service Highlights Section ("BBYSTARGO") -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 relative z-20">
      <div
        class="bg-white/85 backdrop-blur-md border border-primary-100/70 rounded-[2.5rem] p-6 sm:p-10 md:p-14 shadow-xs relative overflow-hidden space-y-10"
      >
        <!-- Ambient Decor Orbs inside card -->
        <div
          class="absolute -top-20 -right-20 w-72 h-72 bg-primary-100/30 rounded-full blur-3xl pointer-events-none"
        ></div>
        <div
          class="absolute -bottom-20 -left-20 w-72 h-72 bg-secondary-100/25 rounded-full blur-3xl pointer-events-none"
        ></div>

        <!-- 1. Header: Brand Title & Description (Client Brief) -->
        <div class="text-center max-w-3xl mx-auto space-y-3 relative z-10">
          <UBadge
            color="primary"
            variant="subtle"
            size="sm"
            class="font-extrabold uppercase tracking-widest px-3.5 py-1.5 rounded-full shadow-xs inline-flex items-center gap-1.5"
          >
            <UIcon name="i-heroicons-sparkles" class="w-4 h-4 text-primary-500" />
            Toko Online Sejak 2023
          </UBadge>

          <h2
            class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 tracking-tight font-heading"
          >
            BBYSTARGO
          </h2>

          <p
            class="text-xs sm:text-sm md:text-base text-gray-600 font-semibold leading-relaxed max-w-2xl mx-auto"
          >
            BbystarGO adalah toko online yang berdiri sejak tahun 2023 yang
            menyediakan jasa pembelian barang dari Indonesia, Korea, China, Jepang,
            dan negara lain.
          </p>
        </div>

        <!-- 2. Two Pillars: Ready Stock & Pre Order Cards -->
        <div class="space-y-4 max-w-4xl mx-auto relative z-10">
          <p
            class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-primary-600 text-center"
          >
            Kami menyediakan 2 layanan utama:
          </p>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- 1. Ready Stock Card -->
            <div
              class="bg-linear-to-br from-white via-primary-50/20 to-primary-50/40 border border-primary-100/80 rounded-3xl p-6 sm:p-7 shadow-xs hover:shadow-md transition-all duration-300 group hover:-translate-y-1 relative overflow-hidden"
            >
              <div class="flex items-start justify-between gap-4 mb-4">
                <div
                  class="w-12 h-12 rounded-2xl bg-primary-500 text-white flex items-center justify-center shadow-md shadow-primary-500/20 group-hover:scale-110 transition-transform duration-300"
                >
                  <UIcon name="i-heroicons-bolt" class="w-6 h-6" />
                </div>
                <UBadge
                  color="primary"
                  variant="subtle"
                  size="xs"
                  class="font-extrabold rounded-full px-2.5 py-1"
                >
                  Layanan Utama
                </UBadge>
              </div>

              <h3
                class="text-lg sm:text-xl font-black text-gray-900 tracking-tight mb-2 flex items-center gap-2"
              >
                1. Ready Stock
              </h3>

              <p
                class="text-xs sm:text-sm text-gray-600 font-semibold leading-relaxed"
              >
                Barang tersedia di gudang kami, siap diproses tanpa menunggu impor.
              </p>
            </div>

            <!-- 2. Pre Order Card -->
            <div
              class="bg-linear-to-br from-white via-indigo-50/20 to-indigo-50/40 border border-indigo-100/80 rounded-3xl p-6 sm:p-7 shadow-xs hover:shadow-md transition-all duration-300 group hover:-translate-y-1 relative overflow-hidden"
            >
              <div class="flex items-start justify-between gap-4 mb-4">
                <div
                  class="w-12 h-12 rounded-2xl bg-indigo-500 text-white flex items-center justify-center shadow-md shadow-indigo-500/20 group-hover:scale-110 transition-transform duration-300"
                >
                  <UIcon name="i-heroicons-globe-americas" class="w-6 h-6" />
                </div>
                <UBadge
                  color="neutral"
                  variant="subtle"
                  size="xs"
                  class="font-extrabold rounded-full px-2.5 py-1 text-indigo-600 bg-indigo-50"
                >
                  Layanan Utama
                </UBadge>
              </div>

              <h3
                class="text-lg sm:text-xl font-black text-gray-900 tracking-tight mb-2 flex items-center gap-2"
              >
                2. Pre Order
              </h3>

              <p
                class="text-xs sm:text-sm text-gray-600 font-semibold leading-relaxed"
              >
                Pemesanan langsung dari negara asal dan diurus bersih sampai ke
                tangan customer.
              </p>
            </div>
          </div>
        </div>

        <!-- 3. Social Proof & Statistics Counters -->
        <div class="space-y-3 max-w-4xl mx-auto pt-2 relative z-10">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Stat 1: 100+ Customer Terlayani -->
            <div
              class="bg-white border border-primary-100/70 rounded-2xl p-5 text-center shadow-xs hover:border-primary-300 hover:shadow-md transition-all duration-300 hover:scale-102"
            >
              <div
                class="w-9 h-9 rounded-xl bg-pink-50 text-pink-500 flex items-center justify-center mx-auto mb-2.5"
              >
                <UIcon name="i-heroicons-user-group" class="w-5 h-5" />
              </div>
              <div
                class="text-3xl md:text-4xl font-black text-primary-500 font-heading tracking-tight"
              >
                100+
              </div>
              <span
                class="text-[10px] sm:text-[11px] font-black text-gray-400 uppercase tracking-widest block mt-1.5"
              >
                CUSTOMER TERLAYANI
              </span>
            </div>

            <!-- Stat 2: 5.0 * Rating Shopee -->
            <div
              class="bg-white border border-amber-100/80 rounded-2xl p-5 text-center shadow-xs hover:border-amber-300 hover:shadow-md transition-all duration-300 hover:scale-102"
            >
              <div
                class="w-9 h-9 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-2.5"
              >
                <UIcon
                  name="i-heroicons-star-solid"
                  class="w-5 h-5 text-amber-400 fill-amber-400"
                />
              </div>
              <div
                class="flex items-center justify-center gap-1.5 text-3xl md:text-4xl font-black text-amber-500 font-heading tracking-tight"
              >
                <span>5.0</span>
                <UIcon
                  name="i-heroicons-star-solid"
                  class="w-6 h-6 text-amber-400 fill-amber-400"
                />
              </div>
              <span
                class="text-[10px] sm:text-[11px] font-black text-gray-400 uppercase tracking-widest block mt-1.5"
              >
                RATING SHOPEE
              </span>
            </div>

            <!-- Stat 3: 2.500+ Penilaian -->
            <div
              class="bg-white border border-emerald-100/80 rounded-2xl p-5 text-center shadow-xs hover:border-emerald-300 hover:shadow-md transition-all duration-300 hover:scale-102"
            >
              <div
                class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto mb-2.5"
              >
                <UIcon
                  name="i-heroicons-chat-bubble-bottom-center-text"
                  class="w-5 h-5"
                />
              </div>
              <div
                class="text-3xl md:text-4xl font-black text-emerald-500 font-heading tracking-tight"
              >
                2.500+
              </div>
              <span
                class="text-[10px] sm:text-[11px] font-black text-gray-400 uppercase tracking-widest block mt-1.5"
              >
                PENILAIAN SHOPEE
              </span>
            </div>
          </div>

          <p
            class="text-[11px] text-gray-400 font-semibold text-center mt-2"
          >
            Kami sudah melayani 100+ customer dan mendapatkan rating 5.0 dari
            2.500 penilaian di Shopee.
          </p>
        </div>

        <!-- Extra CTA to Tentang Kami -->
        <div class="text-center pt-2 relative z-10">
          <NuxtLink to="/tentang-kami">
            <UButton
              color="primary"
              variant="soft"
              class="font-extrabold text-xs sm:text-sm px-6 py-2.5 rounded-full hover:scale-105 transition-all duration-300 cursor-pointer inline-flex items-center gap-1.5"
            >
              Pelajari Lebih Lanjut Tentang Kami
              <UIcon name="i-heroicons-arrow-right" class="w-4 h-4" />
            </UButton>
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- Categories Section -->
    <section
      v-if="categoriesList.length > 0"
      class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-20"
    >
      <div class="flex flex-col sm:flex-row items-center sm:items-end justify-between gap-4 mb-6">
        <div class="text-center sm:text-left max-w-xl">
          <h2 class="text-xl md:text-2xl font-black text-gray-900 tracking-tight">
            Cari Favoritmu
          </h2>
          <p class="text-xs text-gray-400 font-semibold mt-1">
            Pilih kategori merchandise yang paling Anda sukai
          </p>
        </div>

        <!-- Scroll navigation buttons -->
        <div class="flex items-center gap-2 shrink-0">
          <UButton
            icon="i-heroicons-chevron-left-20-solid"
            color="neutral"
            variant="soft"
            size="sm"
            aria-label="Scroll Kiri"
            class="rounded-full cursor-pointer hover:bg-gray-200 transition-colors"
            @click="scrollCategories('left')"
          />
          <UButton
            icon="i-heroicons-chevron-right-20-solid"
            color="neutral"
            variant="soft"
            size="sm"
            aria-label="Scroll Kanan"
            class="rounded-full cursor-pointer hover:bg-gray-200 transition-colors"
            @click="scrollCategories('right')"
          />
        </div>
      </div>

      <!-- Slidable Container -->
      <div
        ref="categoriesContainer"
        class="flex gap-4 overflow-x-auto no-scrollbar snap-x snap-mandatory py-2 px-1 scroll-smooth"
      >
        <NuxtLink
          v-for="cat in categoriesList"
          :key="cat.name"
          :to="`/katalog?category=${cat.slug}`"
          class="shrink-0 w-36 sm:w-44 md:w-48 flex flex-col items-center justify-center p-5 bg-white border border-primary-100/50 rounded-2xl shadow-xs hover:border-primary-300/80 hover:shadow-md hover:-translate-y-1 transition-all duration-300 group snap-start cursor-pointer"
        >
          <div
            :class="cat.bg"
            class="w-12 h-12 rounded-xl flex items-center justify-center border mb-3 transition-transform group-hover:scale-110"
          >
            <UIcon :name="cat.icon" class="w-6 h-6" />
          </div>
          <span
            class="text-xs font-bold text-gray-700 group-hover:text-primary-500 transition-colors text-center truncate max-w-full"
          >
            {{ cat.name }}
          </span>
        </NuxtLink>
      </div>
    </section>

    <!-- Featured Products Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-20">
      <div class="flex items-end justify-between gap-4 mb-8">
        <div>
          <h2
            class="text-xl md:text-2xl font-black text-gray-900 tracking-tight"
          >
            Paling Banyak Disukai
          </h2>
          <p class="text-xs text-gray-400 font-semibold mt-1">
            Pilihan produk Ready Stock dan Pre Order terlaris kami
          </p>
        </div>
        <NuxtLink
          to="/katalog"
          class="text-xs text-primary-500 hover:text-primary-600 font-extrabold flex items-center gap-1 group transition-colors uppercase tracking-wider"
        >
          Lihat Semua
          <UIcon
            name="i-heroicons-arrow-right"
            class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200"
          />
        </NuxtLink>
      </div>

      <!-- Product grid wrapper with tinted background -->
      <div
        class="bg-primary-50/20 border border-primary-100/40 rounded-3xl p-6 shadow-xs"
      >
        <ProductGrid
          :products="featured"
          :loading="loadingFeatured"
          :has-more="false"
        />
      </div>
    </section>

    <!-- Spotlight Promo Banner -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-20">
      <div
        class="bg-linear-to-r from-pink-400 to-primary-500 text-white rounded-[2rem] p-8 md:p-12 relative overflow-hidden shadow-xl shadow-primary-500/10 border border-white/10"
      >
        <div
          class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.25),transparent)]"
        ></div>
        <!-- Floating Decorative Icons on the right -->
        <div
          class="absolute right-16 top-1/2 -translate-y-1/2 hidden md:flex items-center gap-6 pointer-events-none z-0"
        >
          <UIcon
            name="i-heroicons-sparkles"
            class="w-12 h-12 text-white/20 animate-float"
          />
          <UIcon
            name="i-heroicons-gift"
            class="w-16 h-16 text-white/10 animate-float-delayed"
          />
          <UIcon
            name="i-heroicons-heart-solid"
            class="w-10 h-10 text-white/15 animate-float"
          />
        </div>
        <div class="relative z-10 max-w-lg space-y-4">
          <h3
            class="text-2xl md:text-3xl font-black tracking-tight leading-tight"
          >
            Ada yang Baru di BbystarGO
          </h3>
          <p
            class="text-xs md:text-sm text-white/90 leading-relaxed font-semibold"
          >
            Selalu dapatkan update produk merchandise terlucu dan terpopuler
            dari luar negeri. Jangan lewatkan penawaran khusus kami.
          </p>
          <UButton
            to="/katalog"
            color="neutral"
            variant="solid"
            class="!bg-white !text-primary-500 hover:!bg-gray-50 font-extrabold text-xs sm:text-sm px-6 py-3 rounded-full shadow-md hover:scale-105 transition-all duration-300 cursor-pointer"
          >
            Cek Sekarang
          </UButton>
        </div>
      </div>
    </section>



    <!-- Trust & USPs Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-20">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div
          class="bg-white border border-l-8 border-primary-300/80 rounded-2xl p-6 shadow-xs hover:shadow-md transition-shadow"
        >
          <div
            class="w-10 h-10 rounded-xl bg-pink-50 text-pink-500 flex items-center justify-center mb-4"
          >
            <UIcon name="i-heroicons-sparkles" class="w-5 h-5" />
          </div>
          <h4
            class="text-xs uppercase tracking-wider font-extrabold text-gray-900"
          >
            Kualitas Terbaik
          </h4>
          <p
            class="text-[11px] text-gray-400 font-semibold mt-1.5 leading-relaxed"
          >
            Semua produk kami jamin keaslian dan kondisinya sebelum dikirimkan
            ke alamat Anda.
          </p>
        </div>
        <div
          class="bg-white border border-l-8 border-primary-300/80 rounded-2xl p-6 shadow-xs hover:shadow-md transition-shadow"
        >
          <div
            class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center mb-4"
          >
            <UIcon name="i-heroicons-bolt" class="w-5 h-5" />
          </div>
          <h4
            class="text-xs uppercase tracking-wider font-extrabold text-gray-900"
          >
            Proses Cepat
          </h4>
          <p
            class="text-[11px] text-gray-400 font-semibold mt-1.5 leading-relaxed"
          >
            Pemesanan barang Ready Stock diproses langsung di hari yang sama
            demi kepuasan Anda.
          </p>
        </div>
        <div
          class="bg-white border border-l-8 border-primary-300/80 rounded-2xl p-6 shadow-xs hover:shadow-md transition-shadow"
        >
          <div
            class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center mb-4"
          >
            <UIcon name="i-heroicons-gift" class="w-5 h-5" />
          </div>
          <h4
            class="text-xs uppercase tracking-wider font-extrabold text-gray-900"
          >
            Packing Aman
          </h4>
          <p
            class="text-[11px] text-gray-400 font-semibold mt-1.5 leading-relaxed"
          >
            Proteksi ekstra kardus tebal dan gelembung balon secara gratis tanpa
            biaya tambahan.
          </p>
        </div>
        <div
          class="bg-white border border-l-8 border-primary-300/80 rounded-2xl p-6 shadow-xs hover:shadow-md transition-shadow"
        >
          <div
            class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center mb-4"
          >
            <UIcon name="i-heroicons-chat-bubble-left-right" class="w-5 h-5" />
          </div>
          <h4
            class="text-xs uppercase tracking-wider font-extrabold text-gray-900"
          >
            Customer Service
          </h4>
          <p
            class="text-[11px] text-gray-400 font-semibold mt-1.5 leading-relaxed"
          >
            Admin yang ramah siap membantu memantau pesanan Anda setiap saat
            secara responsif.
          </p>
        </div>
      </div>
    </section>

    <!-- Testimonials Section ("Kata Mereka") -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative z-20">
      <div class="text-center max-w-xl mx-auto mb-10">
        <h2 class="text-xl md:text-2xl font-black text-gray-900 tracking-tight">
          Kata Mereka
        </h2>
        <p class="text-xs text-gray-400 font-semibold mt-1">
          Ulasan jujur dari pembeli setia kami
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          v-for="testi in testimonials"
          :key="testi.name"
          class="bg-white/70 border border-primary-300/40 rounded-2xl p-6 shadow-xs space-y-4"
        >
          <div class="flex items-center gap-1">
            <UIcon
              v-for="star in testi.rating"
              :key="star"
              name="i-heroicons-star-solid"
              class="w-4 h-4 text-primary-400"
            />
          </div>
          <p class="text-xs text-gray-600 font-bold leading-relaxed mb-0">
            "{{ testi.text }}"
          </p>
          <div
            class="flex items-center gap-2.5 pt-2 border-t border-primary-50/40"
          >
            <div
              class="w-7.5 h-7.5 rounded-full bg-linear-to-tr from-primary-500 to-primary-400 border-2 border-white flex items-center justify-center text-white text-[11px] font-black uppercase shadow-xs shrink-0"
            >
              {{ testi.name.charAt(0) }}
            </div>
            <span class="text-xs font-extrabold text-gray-800">{{
              testi.name
            }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Accordion FAQ Section -->
    <section
      id="faq"
      class="max-w-4xl mx-auto px-4 py-12 md:py-16 relative z-20"
    >
      <div class="text-center mb-8">
        <h2 class="text-xl md:text-2xl font-black text-gray-900 tracking-tight">
          Pertanyaan Populer
        </h2>
        <p class="text-xs text-gray-400 font-semibold mt-1">
          Temukan jawaban cepat atas pertanyaan Anda
        </p>
      </div>

      <div
        class="bg-white/80 border border-primary-100/50 rounded-3xl p-6 shadow-xs"
      >
        <UAccordion
          :items="faqs"
          :ui="{
            root: 'w-full space-y-2',
            item: 'border border-primary-50 rounded-xl overflow-hidden shadow-xs bg-primary-50/5/20',
            trigger:
              'py-4 px-5 text-left text-xs font-bold text-gray-800 hover:text-primary-500 focus-visible:outline-none flex justify-between items-center transition-colors',
            content:
              'py-4 px-5 text-xs text-gray-500 leading-relaxed font-semibold bg-white border-t border-primary-50/30',
          }"
        />

        <div class="mt-6 text-center">
          <UButton
            to="/faq"
            color="primary"
            variant="soft"
            class="font-extrabold text-xs sm:text-sm px-6 py-2.5 rounded-full hover:scale-105 transition-all duration-300 cursor-pointer inline-flex items-center gap-1.5"
          >
            Lihat Semua Pertanyaan & Panduan (FAQ)
            <UIcon name="i-heroicons-arrow-right" class="w-4 h-4" />
          </UButton>
        </div>
      </div>
    </section>

    <!-- Bottom CTA Banner Section -->
    <section class="max-w-5xl mx-auto px-4 pb-16 relative z-20">
      <div
        class="bg-primary-50/80 border border-primary-100/80 rounded-3xl p-8 md:p-12 text-center space-y-6 shadow-xs relative overflow-hidden"
      >
        <div
          class="absolute -top-10 -left-10 w-40 h-40 bg-primary-200/10 rounded-full blur-2xl"
        ></div>
        <div
          class="absolute -bottom-10 -right-10 w-40 h-40 bg-primary-100/10 rounded-full blur-2xl"
        ></div>
        <h3 class="text-xl md:text-2xl font-black text-gray-900 tracking-tight">
          Siap Memulai Belanja?
        </h3>
        <p
          class="text-xs text-gray-500 font-semibold max-w-md mx-auto leading-relaxed"
        >
          Temukan gantungan kunci, stiker, kaos, tas, dan aksesoris imut lainnya
          langsung di katalog kami.
        </p>
        <div class="flex justify-center pt-2">
          <UButton
            to="/katalog"
            color="primary"
            variant="solid"
            class="font-extrabold text-xs sm:text-sm px-8 py-3 rounded-full shadow-lg shadow-primary-500/15 hover:scale-105 transition-all duration-300 cursor-pointer"
          >
            Belanja Sekarang
          </UButton>
        </div>
      </div>
    </section>
  </div>
</template>
