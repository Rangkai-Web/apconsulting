<script setup lang="ts">
import { useApi } from "~/services/api";

const api = useApi();
const { data: settingsResponse } = useAsyncData("store-settings", () =>
  api.getSettings(),
);
const { data: paymentMethodsResponse } = useAsyncData(
  "footer-payment-methods",
  () => api.getPaymentMethods(),
);

const settings = computed(() => {
  return (
    settingsResponse.value?.data || {
      store_name: "BbystarGO",
      logo: null,
      address: "",
      phone: "",
      whatsapp_number: "",
      operating_hours: "",
      socials: [],
    }
  );
});

const getSocialIcon = (platform: string) => {
  const icons: Record<string, string> = {
    instagram: "i-simple-icons-instagram",
    tiktok: "i-simple-icons-tiktok",
    facebook: "i-simple-icons-facebook",
    twitter: "i-simple-icons-x",
    whatsapp: "i-simple-icons-whatsapp",
    line: "i-simple-icons-line",
    youtube: "i-simple-icons-youtube",
  };
  return icons[platform.toLowerCase()] || "i-heroicons-globe-alt";
};

const paymentMethods = computed(() => paymentMethodsResponse.value?.data ?? []);
</script>

<template>
  <footer
    id="hubungi-kami"
    class="bg-primary-500 relative overflow-hidden mt-20 border-t border-primary-400"
  >
    <!-- Glowing Top Border Indicator -->
    <div
      class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-px bg-linear-to-r from-transparent via-primary-400/40 to-transparent"
    ></div>

    <div
      class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12"
    >
      <!-- Brand & Info Column (4 cols) -->
      <div class="md:col-span-4 space-y-4">
        <NuxtLink to="/" class="flex items-center gap-2.5 group w-fit">
          <div
            class="w-auto max-w-30 h-10 shrink-0 flex items-center justify-center"
          >
            <NuxtImg
              :src="settings.logo || '/BbystarGO_1.jpg'"
              alt="Logo BbystarGO"
              width="40"
              height="40"
              class="w-full h-full object-contain"
            />
          </div>
        </NuxtLink>
        <p class="text-xs text-white/90 font-semibold leading-relaxed max-w-sm">
          {{ settings.store_name }} - jasa pembelian barang dari Indonesia,
          Korea, China, Jepang, dan negara lain sejak 2023. Ready Stock &amp;
          Pre Order.
        </p>
        <div class="flex items-center gap-3 pt-2">
          <template v-if="settings.socials && settings.socials.length > 0">
            <a
              v-for="(social, index) in settings.socials"
              :key="index"
              :href="social.url"
              target="_blank"
              rel="noopener"
              class="w-8.5 h-8.5 rounded-full bg-white/10 border border-white/20 hover:bg-white/25 hover:border-white/40 flex items-center justify-center text-white transition-all duration-300 hover:scale-110 shadow-sm capitalize"
              :aria-label="social.platform"
            >
              <UIcon
                :name="getSocialIcon(social.platform)"
                class="w-4.5 h-4.5"
              />
            </a>
          </template>
          <template v-else>
            <a
              v-if="settings.whatsapp_number"
              :href="
                'https://api.whatsapp.com/send/?phone=' +
                settings.whatsapp_number +
                '&text&type=phone_number&app_absent=0'
              "
              target="_blank"
              rel="noopener"
              class="w-8.5 h-8.5 rounded-full bg-white/10 border border-white/20 hover:bg-white/25 hover:border-white/40 flex items-center justify-center text-white transition-all duration-300 hover:scale-110 shadow-sm"
              aria-label="WhatsApp"
            >
              <UIcon name="i-simple-icons-whatsapp" class="w-4.5 h-4.5" />
            </a>
          </template>
        </div>
      </div>

      <!-- Quick Links (2 cols) -->
      <div class="md:col-span-2 space-y-4">
        <h4
          class="text-xs font-extrabold tracking-widest uppercase text-white border-l-2 border-white/80 pl-2.5"
        >
          Tautan
        </h4>
        <nav class="flex flex-col gap-2.5">
          <NuxtLink
            v-for="link in [
              { label: 'Katalog Produk', to: '/katalog' },
              { label: 'Tentang Kami', to: '/tentang-kami' },
              { label: 'Pertanyaan Populer (FAQ)', to: '/faq' },
              { label: 'Wishlist Saya', to: '/wishlist' },
              { label: 'Keranjang', to: '/cart' },
            ]"
            :key="link.to"
            :to="link.to"
            class="text-xs text-white/90 hover:text-white flex items-center gap-1 group transition-all duration-200 hover:translate-x-1 font-semibold"
          >
            <UIcon
              name="i-heroicons-chevron-right"
              class="w-3 h-3 opacity-0 -ml-3 group-hover:opacity-100 group-hover:ml-0 transition-all duration-300 text-white"
            />
            {{ link.label }}
          </NuxtLink>
        </nav>
      </div>

      <!-- Contacts (4 cols) -->
      <div class="md:col-span-4 space-y-4">
        <h4
          class="text-xs font-extrabold tracking-widest uppercase text-white border-l-2 border-white/80 pl-2.5"
        >
          Hubungi Kami
        </h4>
        <ul class="flex flex-col gap-3 text-xs text-white/90 font-semibold">
          <li
            v-if="settings.address"
            class="flex items-start gap-2.5 leading-relaxed"
          >
            <UIcon
              name="i-heroicons-map-pin"
              class="w-4.5 h-4.5 text-white shrink-0 mt-0.5"
            />
            <span>{{ settings.address }}</span>
          </li>
          <li v-if="settings.phone" class="flex items-center gap-2.5">
            <UIcon
              name="i-heroicons-phone"
              class="w-4.5 h-4.5 text-white shrink-0"
            />
            <a
              target="_blank"
              rel="noopener"
              :href="
                'https://wa.me/' +
                settings.whatsapp_number +
                '?text=Halo Kak, Saya ingin bertanya tentang produk...'
              "
              class="hover:text-white transition-colors"
              >{{ settings.phone }}</a
            >
          </li>
          <li v-if="settings.operating_hours" class="flex items-center gap-2.5">
            <UIcon
              name="i-heroicons-clock"
              class="w-4.5 h-4.5 text-white shrink-0 mt-0.5"
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
            class="text-xs font-extrabold tracking-widest uppercase text-white border-l-2 border-white/80 pl-2.5"
          >
            Metode Pembayaran
          </h4>
          <div class="flex flex-wrap gap-2">
            <div
              v-for="method in paymentMethods"
              :key="method.id"
              class="flex items-center gap-2 px-2 py-1 bg-white/60 border border-white/20 rounded-lg hover:border-white/30 transition-colors duration-200 shadow-xs"
            >
              <img
                v-if="method.qr_image"
                :src="method.qr_image"
                :alt="method.label"
                class="w-12 h-12 object-contain rounded"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Copyright -->
    <div
      class="border-t border-white/10 bg-black/10 py-6 text-xs text-white/80 backdrop-blur-sm"
    >
      <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-center gap-3 text-center sm:text-left font-semibold"
      >
        <span
          >&copy; {{ new Date().getFullYear() }} {{ settings.store_name }}. Hak
          Cipta Dilindungi.</span
        >
      </div>
    </div>
  </footer>
</template>
