<script setup lang="ts">
import { catalogService } from "~/services/catalogService";
import { useWishlistStore } from "~/store/wishlist";
import type { Product } from "~/types";

const wishlist = useWishlistStore();
const products = ref<Product[]>([]);
const loading = ref(true);
const toast = useToast();

useSeoMeta({ title: "Wishlist Saya", robots: "noindex, nofollow" });

async function load() {
  loading.value = true;
  try {
    products.value = wishlist.productIds.length
      ? await catalogService.getProductsByIds(wishlist.productIds)
      : [];
  } catch {
    toast.add({
      title: "Gagal memuat wishlist",
      description: "Terjadi kesalahan, coba muat ulang halaman.",
      color: "error",
    });
  } finally {
    loading.value = false;
  }
}

function clearWishlist() {
  wishlist.clear();
  products.value = [];
  toast.add({
    title: "Wishlist Dikosongkan",
    description: "Semua produk telah dihapus dari koleksi favorit Anda.",
    color: "primary",
  });
}

onMounted(load);
watch(() => wishlist.productIds.length, load);
</script>

<template>
  <div
    class="relative overflow-hidden bg-linear-to-b from-primary-50/40 via-white to-gray-50/50 min-h-screen py-8 md:py-12"
  >
    <!-- Ambient Background Glows & Cute Decor -->
    <div
      class="absolute top-12 -left-40 w-96 h-96 bg-primary-200/20 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-[400px] -right-40 w-96 h-96 bg-primary-100/20 rounded-full blur-3xl pointer-events-none"
    ></div>
    <AppCuteDecor />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 space-y-8">
      <!-- Breadcrumb & Header Title Card -->
      <div
        class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white/80 backdrop-blur-md border border-gray-200/80 rounded-2xl p-4 shadow-xs"
      >
        <div class="flex items-center gap-3">
          <div
            class="w-10 h-10 rounded-xl bg-linear-to-tr from-primary-600 to-primary-400 flex items-center justify-center text-white shadow-md shadow-primary-500/20 shrink-0"
          >
            <UIcon name="i-heroicons-heart" class="w-5 h-5" />
          </div>
          <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500">
              <NuxtLink to="/" class="hover:text-primary-600">Beranda</NuxtLink>
              <span>/</span>
              <span class="text-primary-600 font-bold">Wishlist Saya</span>
            </div>
            <h1 class="text-lg md:text-xl font-black text-gray-900 tracking-tight">
              Koleksi Favorit Saya
            </h1>
          </div>
        </div>

        <!-- Wishlist Actions / Badges -->
        <div class="flex items-center gap-3">
          <div
            class="flex items-center gap-2 bg-primary-50 border border-primary-200/60 px-3.5 py-1.5 rounded-full text-xs font-extrabold text-primary-700"
          >
            <UIcon name="i-heroicons-bookmark" class="w-4 h-4" />
            <span>{{ wishlist.productIds.length }} Produk Disimpan</span>
          </div>

          <button
            v-if="products.length > 0"
            type="button"
            class="text-xs font-bold text-red-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer transition-colors px-2 py-1"
            @click="clearWishlist"
          >
            <UIcon name="i-heroicons-trash" class="w-3.5 h-3.5" />
            <span>Kosongkan Wishlist</span>
          </button>
        </div>
      </div>

      <!-- Premium Wishlist Banner -->
      <div
        class="bg-linear-to-br from-primary-400 via-primary-500 to-primary-600 text-white rounded-3xl p-8 md:p-10 relative overflow-hidden shadow-xl shadow-primary-500/15 border border-primary-400/30"
      >
        <div
          class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.3),transparent)]"
        ></div>

        <!-- Background Floating Icon Decorations -->
        <UIcon
          name="i-heroicons-sparkles-solid"
          class="absolute -top-4 -right-4 w-32 h-32 text-white/20 rotate-12 pointer-events-none"
        />
        <UIcon
          name="i-heroicons-heart-solid"
          class="absolute bottom-4 right-12 w-20 h-20 text-white/20 rotate-45 pointer-events-none"
        />

        <div class="relative z-10 space-y-2 max-w-xl">
          <span
            class="text-xs uppercase tracking-widest text-primary-100 font-black inline-block px-3 py-1 bg-white/15 rounded-full backdrop-blur-xs border border-white/20"
          >
            Koleksi Produk Pilihan
          </span>
          <h2 class="text-2xl md:text-3xl font-black tracking-tight leading-tight">
            Produk Impian Anda Dalam Satu Tempat
          </h2>
          <p class="text-xs md:text-sm text-white/95 leading-relaxed font-medium pt-1">
            Simpan produk Ready Stock &amp; Pre Order favoritmu dari Korea, China, Jepang, dan Indonesia untuk dibeli kapan saja.
          </p>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-if="!loading && products.length === 0"
        class="max-w-lg mx-auto bg-white/90 backdrop-blur-md border border-gray-200/80 rounded-3xl p-8 sm:p-12 shadow-xl shadow-gray-900/5 text-center"
      >
        <EmptyState
          icon="i-heroicons-heart"
          title="Wishlist Anda Masih Kosong"
          description="Tandai produk favorit Anda dengan menekan ikon hati pada katalog produk kami."
          action-label="Jelajahi Katalog Sekarang"
          action-to="/katalog"
        />
      </div>

      <!-- Product Grid Container -->
      <div
        v-else
        class="bg-white/80 backdrop-blur-md border border-gray-200/80 rounded-3xl p-6 sm:p-8 shadow-xs"
      >
        <ProductGrid
          :products="products"
          :loading="loading"
          :has-more="false"
        />
      </div>
    </div>
  </div>
</template>
