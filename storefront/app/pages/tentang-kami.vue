<script setup lang="ts">
import { useApi } from "~/services/api";

const api = useApi();
const { data: settingsResponse } = useAsyncData("store-settings", () =>
  api.getSettings(),
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

useSeoMeta({
  title: "Tentang Kami",
  description:
    "Kenali lebih dekat BbystarGO, toko online yang menyediakan jasa pembelian barang dari Indonesia, Korea, China, Jepang, dan negara lain sejak 2023.",
});
</script>

<template>
  <div class="relative overflow-hidden min-h-screen bg-gray-50/50 pt-16 pb-20">
    <!-- Background Decor Elements -->
    <div
      class="absolute top-0 left-1/4 w-96 h-96 bg-primary-100/30 rounded-full blur-3xl -translate-y-12"
    ></div>
    <div
      class="absolute top-1/3 right-1/4 w-80 h-80 bg-secondary-100/20 rounded-full blur-3xl"
    ></div>

    <!-- Header / Hero Section -->
    <section class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-6">
      <div class="text-center max-w-3xl mx-auto space-y-3">
        <UBadge
          color="primary"
          variant="subtle"
          size="sm"
          class="font-extrabold uppercase tracking-widest px-3.5 py-1.5 rounded-full"
        >
          Tentang Kami
        </UBadge>
        <h1
          class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight leading-none"
        >
          Mengenal {{ settings.store_name }}
        </h1>
        <p
          class="text-xs sm:text-sm text-gray-400 font-semibold max-w-xl mx-auto leading-relaxed"
        >
          Toko online terpercaya untuk memenuhi kebutuhan koleksi, hobi, dan
          merchandise resmi luar negeri Anda.
        </p>
      </div>
    </section>

    <!-- Content Section -->
    <section class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left: Story & Values (8 cols) -->
        <div class="lg:col-span-7 space-y-8">
          <div
            class="bg-white border border-primary-100/40 rounded-3xl p-6 sm:p-8 shadow-xs space-y-5"
          >
            <h2
              class="text-lg sm:text-xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2"
            >
              <UIcon
                name="i-heroicons-sparkles"
                class="w-5 h-5 text-primary-500"
              />
              Siapa Kami?
            </h2>
            <div
              class="text-xs sm:text-sm text-gray-600 font-semibold leading-relaxed space-y-4"
            >
              <p>
                Didirikan sejak tahun <strong>2023</strong>,
                <strong>{{ settings.store_name }}</strong> hadir sebagai solusi
                terpercaya bagi para kolektor, hobiis, dan pecinta merchandise
                untuk mendapatkan produk impian mereka secara aman langsung dari
                Indonesia, Korea, China, dan Jepang.
              </p>
              <p>
                Kami melayani pembelian berbagai macam kategori produk mulai
                dari album K-Pop, photocard, boneka, gantungan kunci, pakaian,
                tas, alat tulis, dan aksesoris imut lainnya. Layanan kami
                mencakup penyediaan produk <strong>Ready Stock</strong> untuk
                pengiriman instan serta sistem <strong>Pre Order</strong> yang
                transparan dan amanah.
              </p>
              <p>
                Dengan komitmen tinggi pada keaslian produk and kepuasan
                pelanggan, kami siap mendampingi proses belanja Anda dari
                awal pemesanan hingga barang mendarat dengan selamat di pelukan
                Anda.
              </p>
            </div>
          </div>

          <!-- Core Values -->
          <div class="space-y-4">
            <h3
              class="text-sm font-black text-gray-400 uppercase tracking-widest pl-1"
            >
              Nilai Utama Kami
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <!-- Card 1 -->
              <div
                class="bg-white border border-primary-100 rounded-2xl p-5 shadow-xs space-y-3"
              >
                <div
                  class="w-9 h-9 rounded-xl bg-pink-50 text-pink-500 flex items-center justify-center"
                >
                  <UIcon name="i-heroicons-shield-check" class="w-5 h-5" />
                </div>
                <h4 class="text-xs font-extrabold text-gray-900">
                  Keaslian Terjamin
                </h4>
                <p
                  class="text-[11px] text-gray-500 font-semibold leading-normal"
                >
                  Semua barang dijamin 100% asli dari official store asal
                  masing-masing negara.
                </p>
              </div>

              <!-- Card 2 -->
              <div
                class="bg-white border border-primary-100 rounded-2xl p-5 shadow-xs space-y-3"
              >
                <div
                  class="w-9 h-9 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center"
                >
                  <UIcon name="i-heroicons-gift" class="w-5 h-5" />
                </div>
                <h4 class="text-xs font-extrabold text-gray-900">
                  Packing Ekstra Aman
                </h4>
                <p
                  class="text-[11px] text-gray-500 font-semibold leading-normal"
                >
                  Proteksi gratis kardus kuat dan bubble wrap berlapis tanpa
                  biaya tambahan.
                </p>
              </div>

              <!-- Card 3 -->
              <div
                class="bg-white border border-primary-100 rounded-2xl p-5 shadow-xs space-y-3"
              >
                <div
                  class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center"
                >
                  <UIcon
                    name="i-heroicons-chat-bubble-left-right"
                    class="w-5 h-5"
                  />
                </div>
                <h4 class="text-xs font-extrabold text-gray-900">
                  Layanan Prima
                </h4>
                <p
                  class="text-[11px] text-gray-500 font-semibold leading-normal"
                >
                  Customer service ramah dan siap membantu menjawab kendala Anda
                  dengan responsif.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Information & Contacts (4 cols) -->
        <div class="lg:col-span-5 space-y-6">
          <!-- Information Card -->
          <div
            class="bg-white border border-primary-100/40 rounded-3xl p-6 shadow-xs space-y-5"
          >
            <h2 class="text-lg font-extrabold text-gray-900 tracking-tight">
              Informasi Toko
            </h2>
            <ul class="space-y-4 text-xs font-semibold text-gray-600">
              <!-- Address -->
              <li v-if="settings.address" class="flex gap-3">
                <div
                  class="w-8 h-8 rounded-lg bg-primary-50 text-primary-500 flex items-center justify-center shrink-0"
                >
                  <UIcon name="i-heroicons-map-pin" class="w-4.5 h-4.5" />
                </div>
                <div class="space-y-0.5">
                  <span
                    class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block"
                    >Alamat</span
                  >
                  <span class="leading-relaxed">{{ settings.address }}</span>
                </div>
              </li>

              <!-- Phone -->
              <li v-if="settings.phone" class="flex gap-3">
                <div
                  class="w-8 h-8 rounded-lg bg-primary-50 text-primary-500 flex items-center justify-center shrink-0"
                >
                  <UIcon name="i-heroicons-phone" class="w-4.5 h-4.5" />
                </div>
                <div class="space-y-0.5">
                  <span
                    class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block"
                    >Nomor HP</span
                  >
                  <span>{{ settings.phone }}</span>
                </div>
              </li>

              <!-- Operating Hours -->
              <li v-if="settings.operating_hours" class="flex gap-3">
                <div
                  class="w-8 h-8 rounded-lg bg-primary-50 text-primary-500 flex items-center justify-center shrink-0"
                >
                  <UIcon name="i-heroicons-clock" class="w-4.5 h-4.5" />
                </div>
                <div class="space-y-0.5">
                  <span
                    class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block"
                    >Jam Pelayanan</span
                  >
                  <span class="whitespace-pre-line leading-relaxed">{{
                    settings.operating_hours
                  }}</span>
                </div>
              </li>
            </ul>
          </div>

          <!-- WhatsApp Chat Box CTA -->
          <div
            v-if="settings.whatsapp_number"
            class="bg-linear-to-r from-primary-400 to-primary-600 text-white rounded-3xl p-6 shadow-lg shadow-primary-600/20 space-y-4 border border-primary-300/20 relative overflow-hidden"
          >
            <div
              class="absolute -top-12 -right-12 w-32 h-32 bg-white/5 rounded-full blur-xl"
            ></div>
            <div class="space-y-1 relative z-10">
              <h3
                class="text-sm font-bold uppercase tracking-wider text-secondary-100"
              >
                Punya Pertanyaan Langsung?
              </h3>
              <p class="text-xs text-white/90 leading-relaxed font-semibold">
                Hubungi kami via WhatsApp untuk konsultasi produk, pertanyaan
                stok, atau bantuan pemesanan.
              </p>
            </div>
            <div class="pt-2 relative z-10">
              <UButton
                target="_blank"
                rel="noopener"
                :href="
                  'https://wa.me/' +
                  settings.whatsapp_number +
                  '?text=Halo Kak, Saya ingin bertanya tentang produk...'
                "
                color="neutral"
                variant="solid"
                class="!bg-white !text-primary-500 hover:!bg-secondary-50 w-full justify-center font-extrabold text-xs py-3 rounded-full shadow-md hover:scale-103 transition-all duration-300 cursor-pointer flex items-center gap-2"
              >
                <UIcon name="i-simple-icons-whatsapp" class="w-4.5 h-4.5" />
                Hubungi via WhatsApp
              </UButton>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
