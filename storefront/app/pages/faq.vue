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
  title: "Pertanyaan Populer (FAQ)",
  description:
    "Temukan jawaban lengkap seputar pemesanan, sistem Pre Order, pembayaran, pengiriman, keaslian barang, dan kebijakan retur di BbystarGO.",
});

// FAQ Categories
const categories = [
  { id: "all", label: "Semua Pertanyaan", icon: "i-heroicons-squares-2x2" },
  {
    id: "order",
    label: "Cara Order & Layanan",
    icon: "i-heroicons-shopping-bag",
  },
  { id: "pre-order", label: "Pre Order & Estimasi", icon: "i-heroicons-clock" },
  { id: "payment", label: "Pembayaran & DP", icon: "i-heroicons-credit-card" },
  { id: "shipping", label: "Pengiriman & Packing", icon: "i-heroicons-truck" },
  {
    id: "guarantee",
    label: "Keaslian & Garansi",
    icon: "i-heroicons-shield-check",
  },
];

const selectedCategory = ref("all");
const searchQuery = ref("");
const openItems = ref<Record<number, boolean>>({
  0: true, // First item opened by default
});

// FAQ Data List
const faqs = [
  {
    id: 1,
    category: "order",
    categoryLabel: "Cara Order & Layanan",
    question: "Bagaimana cara melakukan pemesanan di BbystarGO?",
    answer:
      "Cukup telusuri produk yang Anda inginkan di menu Katalog, pilih variasi yang tersedia, lalu klik 'Tambah ke Keranjang'. Setelah selesai memilih, buka Keranjang dan lanjutkan ke halaman Checkout untuk mengisi alamat lengkap serta menyelesaikan pembayaran sesuai metode pilihan Anda.",
    // badge: "Paling Populer",
  },
  {
    id: 14,
    category: "pre-order",
    categoryLabel: "Pre Order & Estimasi",
    question:
      "Jika mengikuti PO dimana kita bisa melihat proof pembelian barangnya?",
    answer:
      "Semua proof pembelian dan foto kedatangan barang akan di update di grup chat yang ada di LINE , untuk bergabung bisa klik link di bawah ini",
    link: {
      url: "https://line.me/ti/p/dnDnihESXV",
      label: "https://line.me/ti/p/dnDnihESXV",
    },
  },
  {
    id: 2,
    category: "order",
    categoryLabel: "Cara Order & Layanan",
    question: "Bisakah saya melakukan request produk di luar katalog?",
    answer:
      "Tentu saja bisa! Kami dengan senang hati melayani pembelian untuk berbagai produk dari Korea, China, Jepang, maupun merchant lainnya. Cukup hubungi kami via WhatsApp dengan menyertakan foto atau tautan produk yang Anda cari. Kami akan segera mengecek ketersediaan dan memberikan rincian estimasi harganya.",
  },
  {
    id: 3,
    category: "order",
    categoryLabel: "Cara Order & Layanan",
    question: "Apakah saya harus membuat akun sebelum berbelanja?",
    answer:
      "Kami menyarankan Anda untuk mendaftar akun terlebih dahulu agar dapat menyimpan riwayat alamat pengiriman, memantau nomor resi dan status pengiriman secara real-time, serta menyimpan daftar wishlist produk favorit Anda. Pendaftaran akun sangat cepat dan gratis!",
  },
  {
    id: 4,
    category: "pre-order",
    categoryLabel: "Pre Order & Estimasi",
    question: "Apa perbedaan antara produk Ready Stock dan Pre Order (PO)?",
    answer:
      "Produk Ready Stock adalah barang yang sudah berada di gudang kami di Indonesia dan siap dikirim dalam 1-2 hari kerja setelah pembayaran. Sedangkan produk Pre Order (PO) adalah barang yang baru dibelikan langsung dari official store luar negeri mengikuti periode rilis serta membutuhkan waktu pengiriman internasional ke Indonesia.",
    // badge: "Penting",
  },
  {
    id: 5,
    category: "pre-order",
    categoryLabel: "Pre Order & Estimasi",
    question: "Berapa lama estimasi pengiriman untuk barang Pre Order (PO)?",
    answer:
      "Estimasi pengiriman PO berkisar antara 2 hingga 4 minggu setelah barang dikirim oleh official store ke warehouse internasional kami (Korea, China, atau Jepang) dan tiba melalui proses bea cukai resmi di Indonesia. Tim kami akan senantiasa mengupdate perkembangan status batch PO Anda.",
  },
  {
    id: 6,
    category: "pre-order",
    categoryLabel: "Pre Order & Estimasi",
    question: "Bagaimana cara melacak perkembangan pesanan saya?",
    answer:
      "Anda dapat mengecek status terkini pesanan melalui halaman 'Pesanan Saya' di akun member Anda. Apabila paket sudah diserahkan ke jasa ekspedisi domestik, nomor resi pelacakan otomatis akan tertera dan dapat dilacak langsung di situs kurir terkait.",
  },
  {
    id: 7,
    category: "payment",
    categoryLabel: "Pembayaran & DP",
    question: "Metode pembayaran apa saja yang didukung?",
    answer:
      "Kami hanya menyediakan 4 metode pembayaran resmi: BCA, ShopeePay, SeaBank, dan QRIS. Tidak ada metode pembayaran lain selain keempat metode ini. Semua transaksi diproses secara aman dan diverifikasi langsung.",
    // badge: "Hanya 4 Metode Resmi",
  },
  {
    id: 8,
    category: "payment",
    categoryLabel: "Pembayaran & DP",
    question: "Apakah tersedia opsi pembayaran Down Payment (DP)?",
    answer:
      "Ya, untuk rilisan Pre Order tertentu kami menyediakan opsi Down Payment (DP). Anda dapat membayar uang muka terlebih dahulu melalui 4 metode pembayaran kami (BCA, ShopeePay, SeaBank, atau QRIS), kemudian melunasi sisanya ketika barang telah mendarat di gudang kami di Indonesia sebelum pengiriman lokal.",
  },
  {
    id: 9,
    category: "payment",
    categoryLabel: "Pembayaran & DP",
    question: "Apakah harga produk sudah termasuk pajak dan bea masuk impor?",
    answer:
      "Benar! Semua harga yang tertera di katalog BbystarGO sudah bersifat All-in (termasuk biaya pembelian internasional, pajak impor resmi, bea masuk, dan cargo internasional ke gudang kami). Anda hanya tinggal membayar ongkos kirim lokal dari gudang kami ke alamat tujuan Anda.",
  },
  {
    id: 10,
    category: "shipping",
    categoryLabel: "Pengiriman & Packing",
    question: "Kurir pengiriman lokal apa saja yang tersedia?",
    answer:
      "Kami bekerja sama dengan ekspedisi tepercaya seperti JNE, J&T Express, SiCepat, dan ekspedisi lainnya. Ongkos kirim akan dikalkulasi secara otomatis saat Anda melengkapi alamat penerima di halaman checkout.",
  },
  {
    id: 11,
    category: "shipping",
    categoryLabel: "Pengiriman & Packing",
    question: "Bagaimana standar packing barang dari BbystarGO?",
    answer:
      "Kami sangat mengutamakan keamanan barang pesanan Anda. Setiap paket dikemas secara rapi dan aman sesuai standar pengiriman untuk memastikan barang sampai dalam kondisi baik.",
    // badge: "Free Box & Bubble Wrap",
  },
  {
    id: 12,
    category: "guarantee",
    categoryLabel: "Keaslian & Garansi",
    question: "Apakah semua produk dijamin 100% original?",
    answer:
      "100% Dijamin Asli (Authentic Guaranteed). Kami hanya membeli produk langsung dari official store, Weverse Shop, pop-up store resmi, Sanrio, Line Friends, dan authorized retailer terpercaya di negara asalnya. Kami tidak pernah menjual barang tiruan/KW.",
    // badge: "100% Original",
  },
  {
    id: 13,
    category: "guarantee",
    categoryLabel: "Keaslian & Garansi",
    question: "Bagaimana kebijakan pengajuan komplain atau retur barang?",
    answer:
      "Pengembalian atau penukaran barang dapat diterima jika terjadi kekeliruan pengiriman dari pihak kami atau cacat mayor pabrik, dengan syarat WAJIB menyertakan Video Unboxing utuh tanpa jeda (uncut/unpause) dari sebelum paket dibuka hingga selesai diperiksa. Laporan komplain wajib dikirimkan dalam kurun waktu maksimal 1x24 jam sejak paket diterima menurut data resi kurir.",
  },
];

// Computed Filtered FAQs
const filteredFaqs = computed(() => {
  return faqs.filter((item) => {
    // Category match
    const matchesCategory =
      selectedCategory.value === "all" ||
      item.category === selectedCategory.value;

    // Search query match
    const query = searchQuery.value.trim().toLowerCase();
    if (!query) return matchesCategory;

    const matchesSearch =
      item.question.toLowerCase().includes(query) ||
      item.answer.toLowerCase().includes(query) ||
      item.categoryLabel.toLowerCase().includes(query);

    return matchesCategory && matchesSearch;
  });
});

// Category count helper
function getCategoryCount(catId: string) {
  if (catId === "all") return faqs.length;
  return faqs.filter((f) => f.category === catId).length;
}

// Accordion toggle
function toggleItem(index: number) {
  openItems.value[index] = !openItems.value[index];
}

function resetFilters() {
  searchQuery.value = "";
  selectedCategory.value = "all";
}
</script>

<template>
  <div class="relative overflow-hidden min-h-screen bg-gray-50/50 pt-16 pb-24">
    <!-- Ambient Background Decor Elements -->
    <div
      class="absolute top-0 left-1/4 w-96 h-96 bg-primary-100/35 rounded-full blur-3xl -translate-y-16 pointer-events-none"
    ></div>
    <div
      class="absolute top-1/3 right-1/4 w-80 h-80 bg-secondary-100/25 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute bottom-1/4 left-10 w-72 h-72 bg-primary-200/20 rounded-full blur-3xl pointer-events-none"
    ></div>

    <!-- Header / Hero Section -->
    <section class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
      <div class="text-center max-w-3xl mx-auto space-y-4">
        <UBadge
          color="primary"
          variant="subtle"
          size="sm"
          class="font-extrabold uppercase tracking-widest px-3.5 py-1.5 rounded-full inline-flex items-center gap-1.5 shadow-xs"
        >
          <UIcon name="i-heroicons-question-mark-circle" class="w-4 h-4" />
          Pusat Bantuan & FAQ
        </UBadge>

        <h1
          class="text-3xl sm:text-4xl md:text-5xl font-black text-gray-900 tracking-tight leading-tight"
        >
          Pertanyaan Populer
        </h1>

        <p
          class="text-xs sm:text-sm text-gray-500 font-semibold max-w-xl mx-auto leading-relaxed"
        >
          Temukan jawaban cepat seputar cara order, sistem Pre-Order,
          pembayaran, pengiriman paket, dan jaminan keaslian di
          {{ settings.store_name }}.
        </p>

        <!-- Live Search Bar -->
        <div class="max-w-xl mx-auto pt-2">
          <div class="relative group">
            <UInput
              v-model="searchQuery"
              icon="i-heroicons-magnifying-glass"
              placeholder="Cari pertanyaan... (contoh: PO, estimasi, ongkir, QRIS, BCA)"
              size="lg"
              class="w-full shadow-md shadow-primary-500/5 transition-all duration-300"
              :ui="{
                base: 'rounded-2xl bg-white text-gray-800 border-2 border-primary-100 hover:border-primary-300 focus:border-primary-500 focus-visible:ring-2 focus-visible:ring-primary-500/20 py-3.5 px-4 text-xs sm:text-sm font-semibold placeholder:text-gray-400',
              }"
            >
              <template #trailing>
                <button
                  v-if="searchQuery"
                  type="button"
                  class="p-1 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
                  aria-label="Hapus kata kunci"
                  @click="searchQuery = ''"
                >
                  <UIcon name="i-heroicons-x-mark" class="w-4 h-4" />
                </button>
              </template>
            </UInput>
          </div>
        </div>
      </div>
    </section>

    <!-- Main FAQ Content Section -->
    <section
      class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-12"
    >
      <!-- Category Tabs (Horizontal Scrollable Pills) -->
      <div
        class="flex items-center gap-2 overflow-x-auto pb-4 pt-1 scrollbar-none"
      >
        <button
          v-for="cat in categories"
          :key="cat.id"
          type="button"
          class="shrink-0 flex items-center gap-2 px-4 py-2.5 rounded-full text-xs font-bold transition-all duration-200 cursor-pointer shadow-xs"
          :class="
            selectedCategory === cat.id
              ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25 scale-102'
              : 'bg-white text-gray-600 hover:text-primary-600 hover:bg-primary-50/60 border border-primary-100/50'
          "
          @click="selectedCategory = cat.id"
        >
          <UIcon :name="cat.icon" class="w-4 h-4" />
          <span>{{ cat.label }}</span>
          <span
            class="text-[10px] px-1.5 py-0.5 rounded-full font-black"
            :class="
              selectedCategory === cat.id
                ? 'bg-white/20 text-white'
                : 'bg-gray-100 text-gray-500'
            "
          >
            {{ getCategoryCount(cat.id) }}
          </span>
        </button>
      </div>

      <!-- FAQ Items Grid & Accordion List -->
      <div class="mt-6 space-y-4">
        <!-- Results Counter when filtered -->
        <div
          v-if="searchQuery"
          class="flex items-center justify-between px-1 text-xs font-bold text-gray-500"
        >
          <span>
            Menampilkan {{ filteredFaqs.length }} hasil untuk "<span
              class="text-primary-600 font-black"
              >{{ searchQuery }}</span
            >"
          </span>
          <button
            type="button"
            class="text-primary-600 hover:underline cursor-pointer flex items-center gap-1"
            @click="resetFilters"
          >
            <UIcon name="i-heroicons-arrow-path" class="w-3.5 h-3.5" />
            Reset Filter
          </button>
        </div>

        <!-- Empty State -->
        <div
          v-if="filteredFaqs.length === 0"
          class="bg-white border border-primary-100 rounded-3xl p-12 text-center space-y-4 shadow-xs"
        >
          <div
            class="w-16 h-16 rounded-full bg-primary-50 text-primary-500 flex items-center justify-center mx-auto"
          >
            <UIcon name="i-heroicons-face-frown" class="w-8 h-8" />
          </div>
          <div class="space-y-1 max-w-sm mx-auto">
            <h3 class="text-base font-black text-gray-900">
              Pertanyaan Tidak Ditemukan
            </h3>
            <p class="text-xs text-gray-500 font-semibold leading-relaxed">
              Maaf, kami tidak menemukan jawaban yang cocok untuk pencarian
              Anda. Kami siap membantu secara langsung!
            </p>
          </div>
          <div class="pt-2 flex justify-center gap-3">
            <UButton
              color="neutral"
              variant="outline"
              size="sm"
              class="font-bold text-xs rounded-full px-4 py-2 cursor-pointer"
              @click="resetFilters"
            >
              Hapus Pencarian
            </UButton>
            <UButton
              v-if="settings.whatsapp_number"
              target="_blank"
              rel="noopener"
              :href="
                'https://wa.me/' +
                settings.whatsapp_number +
                '?text=Halo Kak, Saya ingin bertanya tentang ' +
                encodeURIComponent(searchQuery)
              "
              color="primary"
              variant="solid"
              size="sm"
              class="font-extrabold text-xs rounded-full px-4 py-2 cursor-pointer shadow-md"
            >
              <UIcon name="i-simple-icons-whatsapp" class="w-4 h-4" />
              Tanya Admin via WA
            </UButton>
          </div>
        </div>

        <!-- Accordion Cards -->
        <div v-else class="space-y-3">
          <div
            v-for="(item, idx) in filteredFaqs"
            :key="item.id"
            class="bg-white border rounded-2xl overflow-hidden transition-all duration-300 shadow-xs"
            :class="
              openItems[idx]
                ? 'border-primary-300 ring-1 ring-primary-300/40 shadow-sm'
                : 'border-primary-100/70 hover:border-primary-200'
            "
          >
            <!-- Accordion Trigger Button -->
            <button
              type="button"
              class="w-full text-left p-4 sm:p-5 flex items-center justify-between gap-4 cursor-pointer select-none transition-colors"
              :aria-expanded="openItems[idx]"
              @click="toggleItem(idx)"
            >
              <div class="flex items-center gap-3 sm:gap-4 grow min-w-0">
                <!-- Question Number Indicator / Icon -->
                <div
                  class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center shrink-0 text-xs font-black transition-colors"
                  :class="
                    openItems[idx]
                      ? 'bg-primary-500 text-white'
                      : 'bg-primary-50 text-primary-600'
                  "
                >
                  Q
                </div>

                <div class="space-y-1 min-w-0">
                  <!-- Category & Badge -->
                  <div class="flex items-center gap-2 flex-wrap">
                    <span
                      class="text-[10px] font-bold uppercase tracking-wider text-gray-400"
                    >
                      {{ item.categoryLabel }}
                    </span>
                    <!-- <span
                      v-if="item?.badge"
                      class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-primary-100 text-primary-700"
                    >
                      {{ item?.badge }}
                    </span> -->
                  </div>

                  <!-- Question Text -->
                  <h2
                    class="text-xs sm:text-sm font-black text-gray-800 leading-snug transition-colors"
                    :class="{ 'text-primary-600': openItems[idx] }"
                  >
                    {{ item.question }}
                  </h2>
                </div>
              </div>

              <!-- Chevron Indicator -->
              <div
                class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 transition-transform duration-300"
                :class="
                  openItems[idx]
                    ? 'rotate-180 bg-primary-50 text-primary-600'
                    : 'text-gray-400 bg-gray-50'
                "
              >
                <UIcon name="i-heroicons-chevron-down" class="w-4 h-4" />
              </div>
            </button>

            <!-- Accordion Content -->
            <div
              v-show="openItems[idx]"
              class="px-4 sm:px-5 pb-5 pt-1 text-xs text-gray-600 font-semibold leading-relaxed border-t border-primary-50/50 bg-linear-to-b from-primary-50/20 to-transparent"
            >
              <div class="pl-10 sm:pl-12 space-y-2">
                <p>{{ item.answer }}</p>
                <div v-if="item.link" class="pt-1">
                  <a
                    :href="item.link.url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1.5 font-bold text-primary-600 hover:text-primary-700 underline break-all"
                  >
                    <span>{{ item.link.label || item.link.url }}</span>
                    <UIcon
                      name="i-heroicons-arrow-top-right-on-square"
                      class="w-3.5 h-3.5 shrink-0"
                    />
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Bottom WhatsApp Contact Banner -->
    <section class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
      <div
        class="bg-linear-to-r from-primary-500 via-primary-600 to-primary-700 text-white rounded-3xl p-8 sm:p-10 shadow-xl shadow-primary-500/20 relative overflow-hidden text-center space-y-6"
      >
        <!-- Background Orbs -->
        <div
          class="absolute -top-12 -left-12 w-44 h-44 bg-white/10 rounded-full blur-2xl pointer-events-none"
        ></div>
        <div
          class="absolute -bottom-12 -right-12 w-44 h-44 bg-secondary-400/20 rounded-full blur-2xl pointer-events-none"
        ></div>

        <div class="space-y-2 relative z-10 max-w-xl mx-auto">
          <div
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-white text-[11px] font-extrabold uppercase tracking-widest backdrop-blur-xs mb-2"
          >
            <UIcon name="i-heroicons-chat-bubble-left-right" class="w-4 h-4" />
            Butuh Bantuan Lebih Lanjut?
          </div>
          <h2
            class="text-xl sm:text-2xl md:text-3xl font-black text-white tracking-tight leading-tight"
          >
            Masih Punya Pertanyaan Lain?
          </h2>
          <p
            class="text-xs sm:text-sm text-white/90 font-semibold leading-relaxed"
          >
            {{ settings.store_name }} siap membantu menjawab seputar pesanan,
            request produk khusus, atau kendala belanja Anda.
          </p>
        </div>

        <div
          class="relative z-10 flex flex-col sm:flex-row items-center justify-center gap-3 pt-2"
        >
          <UButton
            v-if="settings.whatsapp_number"
            target="_blank"
            rel="noopener"
            :href="
              'https://wa.me/' +
              settings.whatsapp_number +
              '?text=Halo%20' +
              encodeURIComponent(settings.store_name) +
              ',%20saya%20ingin%20bertanya%20mengenai%20layanan%20pembelian%20barang...'
            "
            color="neutral"
            variant="solid"
            size="lg"
            class="!bg-white !text-primary-600 hover:!bg-primary-50 font-black text-xs sm:text-sm px-7 py-3.5 rounded-full shadow-lg shadow-black/10 hover:scale-105 transition-all duration-300 cursor-pointer flex items-center gap-2"
          >
            <UIcon
              name="i-simple-icons-whatsapp"
              class="w-5 h-5 text-emerald-600"
            />
            Hubungi via WhatsApp
          </UButton>
          <!-- <NuxtLink to="/katalog">
            <UButton
              color="neutral"
              variant="outline"
              size="lg"
              class="!text-primary-500 !border-primary-500 hover:!bg-primary-100/15 font-bold text-xs sm:text-sm px-6 py-3.5 rounded-full transition-all duration-300 cursor-pointer"
            >
              Jelajahi Katalog Produk
            </UButton>
          </NuxtLink> -->
        </div>
      </div>
    </section>
  </div>
</template>
