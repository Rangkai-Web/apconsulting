<script setup lang="ts">
import { catalogService } from "~/services/catalogService";
import { useWishlistStore } from "~/store/wishlist";
import type { Product } from "~/types";

const wishlist = useWishlistStore();
const products = ref<Product[]>([]);
const loading = ref(true);
const toast = useToast();

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

onMounted(load);
watch(() => wishlist.productIds.length, load);
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
      class="absolute top-[400px] -right-40 w-96 h-96 bg-primary-100/20 rounded-full blur-3xl pointer-events-none"
    ></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
      <!-- Premium Wishlist Header Banner -->
      <div
        class="bg-linear-to-br from-primary-400 to-primary-500 text-white rounded-3xl p-8 md:p-10 mb-8 relative overflow-hidden shadow-lg shadow-primary-500/15 border border-primary-400/25"
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
          class="text-xs uppercase tracking-widest text-primary-100 font-extrabold mb-1.5 block"
          >Koleksi Favorit</span
        >
        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">
          Wishlist Saya
        </h1>
        <p
          class="text-xs md:text-sm text-white/95 mt-1 max-w-md leading-relaxed font-medium"
        >
          Simpan produk Ready Stock &amp; Pre Order favoritmu di sini untuk
          dibeli nanti.
        </p>
      </div>

      <!-- Empty State -->
      <div
        v-if="!loading && products.length === 0"
        class="max-w-md mx-auto bg-white backdrop-blur-xs border-2 border-primary-300/40 rounded-3xl p-8 shadow-[0_4px_25px_-6px_rgba(255,116,108,0.03)]"
      >
        <EmptyState
          icon="i-heroicons-heart"
          title="Wishlist Anda Masih Kosong"
          description="Tandai produk favorit Anda dengan menekan tombol hati di katalog."
          action-label="Lihat Katalog"
          action-to="/katalog"
        />
      </div>

      <!-- Product Grid Container -->
      <div
        v-else
        class="bg-white/60 backdrop-blur-xs border border-primary-100/35 rounded-3xl p-6 shadow-[0_4px_25px_-6px_rgba(255,116,108,0.03)]"
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
