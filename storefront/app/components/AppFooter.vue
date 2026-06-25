<script setup lang="ts">
import { useApi } from "~/services/api";

const api = useApi();
const { data: settingsResponse } = useAsyncData("store-settings", () =>
  api.getSettings(),
);

const settings = computed(() => {
  return (
    settingsResponse.value?.data || {
      store_name: "Doctor Plant Indonesia",
      logo: null,
      address:
        "1st Floor, Jl. Artha Gading Sel. No.1, RT.18/RW.8, Klp. Gading Bar., Kec. Klp. Gading, Jkt Utara, Jakarta 14240",
      phone: "+62 812-9504-0884",
      whatsapp_number: "6281295040884",
      instagram_url: "https://www.instagram.com/drplant_official_id/",
      operating_hours: "Senin - Minggu\n10.00 - 22.00 WIB",
    }
  );
});
</script>

<template>
  <footer
    class="bg-[#081710] text-gray-300 relative overflow-hidden mt-20 border-t border-primary-950/20"
  >
    <!-- Glowing Top Border Indicator -->
    <div
      class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-px bg-linear-to-r from-transparent via-primary-500/30 to-transparent"
    ></div>

    <div
      class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12"
    >
      <!-- Brand & Info Column (4 cols) -->
      <div class="md:col-span-4 space-y-4">
        <NuxtLink to="/" class="flex items-center gap-2.5 group w-fit">
          <div
            class="w-12 h-12 rounded-full bg-linear-to-tr from-primary-600 to-primary-400 flex items-center justify-center text-white shadow-md shadow-primary-600/20 transform transition-transform group-hover:scale-105 group-hover:rotate-3 duration-300 overflow-hidden"
          >
            <NuxtImg
              :src="settings.logo || '/logo_dr_plant.jpg'"
              alt="Logo Dr. Plant"
              width="40"
              height="40"
              class="w-full h-full object-contain rounded-full"
            />
          </div>
          <div class="flex flex-col">
            <span
              class="font-bold text-white text-base tracking-wide leading-tight group-hover:text-primary-300 transition-colors"
              >{{ settings.store_name }}</span
            >
            <span
              class="text-xs text-primary-400 uppercase tracking-widest font-extrabold -mt-0.5"
              >Indonesia</span
            >
          </div>
        </NuxtLink>
        <p class="text-xs text-gray-400 leading-relaxed max-w-sm">
          Merek skincare asal Tiongkok yang berfokus pada kecantikan kulit sehat
          alami berbahan dasar tumbuhan alpine. Kami menghadirkan rangkaian
          produk hidrasi, sheet mask, serta layanan facial terbaik.
        </p>
        <div class="flex items-center gap-3 pt-2">
          <a
            v-if="settings.instagram_url"
            :href="settings.instagram_url"
            target="_blank"
            rel="noopener"
            class="w-8.5 h-8.5 rounded-full bg-gray-900/60 border border-gray-800/80 hover:bg-primary-600 hover:border-primary-500 flex items-center justify-center text-gray-400 hover:text-white transition-all duration-300 hover:scale-110"
            aria-label="Instagram"
          >
            <UIcon name="i-simple-icons-instagram" class="w-4.5 h-4.5" />
          </a>
          <a
            v-if="settings.whatsapp_number"
            :href="
              'https://api.whatsapp.com/send/?phone=' +
              settings.whatsapp_number +
              '&text&type=phone_number&app_absent=0'
            "
            target="_blank"
            rel="noopener"
            class="w-8.5 h-8.5 rounded-full bg-gray-900/60 border border-gray-800/80 hover:bg-primary-600 hover:border-primary-500 flex items-center justify-center text-gray-400 hover:text-white transition-all duration-300 hover:scale-110"
            aria-label="WhatsApp"
          >
            <UIcon name="i-simple-icons-whatsapp" class="w-4.5 h-4.5" />
          </a>
        </div>
      </div>

      <!-- Quick Links (2 cols) -->
      <div class="md:col-span-2 space-y-4">
        <h4
          class="text-xs font-bold tracking-widest uppercase text-primary-400/90 border-l-2 border-primary-500 pl-2.5"
        >
          Tautan
        </h4>
        <nav class="flex flex-col gap-2.5">
          <NuxtLink
            v-for="link in [
              { label: 'Katalog Produk', to: '/katalog' },
              { label: 'Wishlist Saya', to: '/wishlist' },
              { label: 'Keranjang', to: '/cart' },
            ]"
            :key="link.to"
            :to="link.to"
            class="text-xs text-gray-400 hover:text-white flex items-center gap-1 group transition-all duration-200 hover:translate-x-1"
          >
            <UIcon
              name="i-heroicons-chevron-right"
              class="w-3 h-3 opacity-0 -ml-3 group-hover:opacity-100 group-hover:ml-0 transition-all duration-300 text-primary-500"
            />
            {{ link.label }}
          </NuxtLink>
        </nav>
      </div>

      <!-- Contacts (4 cols) -->
      <div class="md:col-span-4 space-y-4">
        <h4
          class="text-xs font-bold tracking-widest uppercase text-primary-400/90 border-l-2 border-primary-500 pl-2.5"
        >
          Hubungi Kami
        </h4>
        <ul class="flex flex-col gap-3 text-xs text-gray-400">
          <li
            v-if="settings.address"
            class="flex items-start gap-2.5 leading-relaxed"
          >
            <UIcon
              name="i-heroicons-map-pin"
              class="w-4.5 h-4.5 text-primary-500 shrink-0 mt-0.5"
            />
            <span>{{ settings.address }}</span>
          </li>
          <li v-if="settings.phone" class="flex items-center gap-2.5">
            <UIcon
              name="i-heroicons-phone"
              class="w-4.5 h-4.5 text-primary-500 shrink-0"
            />
            <a
              :href="'tel:' + settings.phone"
              class="hover:text-white transition-colors"
              >{{ settings.phone }}</a
            >
          </li>
          <li v-if="settings.operating_hours" class="flex items-start gap-2.5">
            <UIcon
              name="i-heroicons-clock"
              class="w-4.5 h-4.5 text-primary-500 shrink-0 mt-0.5"
            />
            <span class="whitespace-pre-line">{{
              settings.operating_hours
            }}</span>
          </li>
        </ul>
      </div>

      <!-- Payments (2 cols) -->
      <div class="md:col-span-2 space-y-5">
        <div class="space-y-3">
          <h4
            class="text-xs font-bold tracking-widest uppercase text-primary-400/90 border-l-2 border-primary-500 pl-2.5"
          >
            Metode Pembayaran
          </h4>
          <div class="flex flex-wrap gap-1">
            <span
              v-for="method in [
                'Transfer Bank',
                'GoPay',
                'OVO',
                'DANA',
                'Kartu Kredit',
              ]"
              :key="method"
              class="px-2 py-1 bg-gray-700/60 border border-gray-800/80 rounded text-xs text-gray-400 font-semibold hover:border-primary-600/40 hover:text-white transition-colors duration-200"
            >
              {{ method }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Copyright -->
    <div
      class="border-t border-gray-900/80 bg-gray-950/20 py-6 text-xs text-gray-500"
    >
      <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left"
      >
        <span
          >&copy; {{ new Date().getFullYear() }} {{ settings.store_name }}. Hak
          Cipta Dilindungi.</span
        >
      </div>
    </div>
  </footer>
</template>
