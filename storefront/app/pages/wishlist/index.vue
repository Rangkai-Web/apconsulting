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
    class="relative overflow-hidden bg-linear-to-b from-primary-50/15 via-white to-white min-h-screen py-10"
  >
    <!-- Ambient Background Glows -->
    <div
      class="absolute top-12 -left-40 w-96 h-96 bg-primary-100/10 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-[400px] -right-40 w-96 h-96 bg-secondary-100/10 rounded-full blur-3xl pointer-events-none"
    ></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
      <!-- Premium Wishlist Header Banner -->
      <div
        class="bg-linear-to-br from-primary-900 to-primary-950 text-white rounded-3xl p-8 md:p-10 mb-8 relative overflow-hidden shadow-lg shadow-primary-950/10 border border-primary-900/10"
      >
        <div
          class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(4,92,52,0.4),transparent)]"
        ></div>
        <UIcon
          name="i-heroicons-heart"
          class="absolute -bottom-8 -right-8 w-36 h-36 text-white/5 transform rotate-12 pointer-events-none"
        />
        <span
          class="text-xs uppercase tracking-widest text-primary-400 font-extrabold mb-1.5 block"
          >Koleksi Favorit</span
        >
        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">
          Wishlist Saya
        </h1>
        <p
          class="text-xs md:text-sm text-primary-200 mt-1 max-w-md leading-relaxed font-medium"
        >
          Simpan produk skincare alpine dan layanan facial favorit Anda di sini.
          Dapatkan kulit sehat impian Anda kapan saja.
        </p>
      </div>

      <!-- Empty State -->
      <div
        v-if="!loading && products.length === 0"
        class="max-w-md mx-auto bg-white/70 backdrop-blur-sm border border-gray-100 rounded-3xl p-8 shadow-sm"
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
        class="bg-white/40 backdrop-blur-sm border border-gray-100/60 rounded-3xl p-6 shadow-sm"
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
