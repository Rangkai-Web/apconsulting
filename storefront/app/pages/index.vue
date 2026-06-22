<script setup lang="ts">
import { catalogService } from "~/services/catalogService";
import type { Product } from "~/types";

const featured = ref<Product[]>([]);
const loadingFeatured = ref(true);

const banners = [
  {
    title: "Perawatan Kulit Alami Berbasis Alpine Plants",
    subtitle:
      "Rangkaian produk hidrasi premium dari ekstrak tanaman pegunungan tinggi untuk kulit sehat berseri.",
    image:
      "https://images.unsplash.com/photo-1612817288484-6f916006741a?auto=format&fit=crop&w=1600&h=600&q=80",
  },
  {
    title: "Sheet Mask Mewah untuk Hidrasi Instan",
    subtitle:
      "Mengembalikan kelembaban optimal dan menyegarkan kulit lelah dalam 15 menit.",
    image:
      "https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=1600&h=600&q=80",
  },
  {
    title: "Layanan Facial Treatment Eksklusif",
    subtitle:
      "Perawatan kulit mendalam dengan teknologi ultrasound dan terapis berpengalaman di klinik kami.",
    image:
      "https://images.unsplash.com/photo-1519699047748-de8e457a634e?auto=format&fit=crop&w=1600&h=600&q=80",
  },
];

onMounted(async () => {
  featured.value = await catalogService.getFeaturedProducts(4);
  loadingFeatured.value = false;
});
</script>

<template>
  <div
    class="relative overflow-hidden bg-linear-to-b from-primary-50/20 via-white to-white min-h-screen"
  >
    <!-- Ambient Background Glows -->
    <div
      class="absolute top-20 -left-40 w-96 h-96 bg-primary-100/20 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-[600px] -right-40 w-96 h-96 bg-secondary-100/20 rounded-full blur-3xl pointer-events-none"
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
          <div class="relative aspect-3/2 sm:aspect-16/5 w-full">
            <img
              :src="item.image"
              :alt="item.title"
              class="w-full h-full object-cover"
            />
            <!-- Overlay with linear -->
            <div
              class="absolute inset-0 bg-linear-to-r from-black/75 via-black/45 to-transparent flex flex-col items-start justify-center px-6 sm:px-16 text-white"
            >
              <span
                class="text-[9px] sm:text-xs uppercase tracking-widest text-primary-400 font-extrabold mb-1"
                >Doctor Plant Indonesia</span
              >
              <h2
                class="text-lg sm:text-2xl md:text-4xl font-extrabold max-w-xs sm:max-w-lg tracking-tight leading-snug sm:leading-tight"
              >
                {{ item.title }}
              </h2>
              <p
                class="text-[10px] sm:text-xs md:text-base mt-1.5 max-w-xs sm:max-w-md text-gray-200 leading-relaxed font-medium"
              >
                {{ item.subtitle }}
              </p>
              <UButton
                to="/katalog"
                color="primary"
                class="mt-3.5 sm:mt-5 font-semibold text-xs sm:text-sm px-4 sm:px-6 py-1.5 sm:py-2.5 rounded-full shadow-lg shadow-primary-600/20 hover:scale-105 transition-transform duration-300 cursor-pointer"
                >Belanja Sekarang</UButton
              >
            </div>
          </div>
        </UCarousel>
      </div>
    </section>

    <!-- Featured Products Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative">
      <!-- Background leaf icon motif -->
      <UIcon
        name="i-lucide-leaf"
        class="absolute -top-6 -right-6 w-36 h-36 text-primary-500/5 transform rotate-45 pointer-events-none"
      />

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
            Rekomendasi produk skincare alpine terbaik untuk kecantikan alami
            kulit Anda
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
        class="bg-white/40 backdrop-blur-sm border border-gray-100/60 rounded-3xl p-6 shadow-sm"
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
