<script setup lang="ts">
import { orderService } from "~/services/orderService";
import { useAuthStore } from "~/store/auth";
import { useCurrency } from "~/composables/useCurrency";
import type { Order } from "~/types";

definePageMeta({ middleware: "auth" });

const authStore = useAuthStore();
const { format } = useCurrency();
const toast = useToast();
const router = useRouter();

useSeoMeta({ title: "Riwayat Pesanan", robots: "noindex, nofollow" });

const activeTab = ref(authStore.isLoggedIn ? "member" : "guest");
const statusFilter = ref("all");

// Member orders state
const ordersList = ref<Order[]>([]);
const loadingOrders = ref(false);

// Guest lookup state
const lookupState = reactive({
  orderNumber: "",
  email: "",
});
const loadingLookup = ref(false);

const tabs = computed(() => {
  const list = [];
  if (authStore.isLoggedIn) {
    list.push({ label: "Pesanan Saya", value: "member" });
  } else {
    list.push({ label: "Lacak Pesanan Guest", value: "guest" });
  }
  return list;
});

const filteredOrders = computed(() => {
  if (statusFilter.value === "all") return ordersList.value;
  return ordersList.value.filter((o) => {
    const st = o.orderStatus.toLowerCase();
    if (statusFilter.value === "pending") return st === "pending";
    if (statusFilter.value === "processing")
      return [
        "paid",
        "processing",
        "ordered",
        "arrived_at_proxy",
        "shipped_to_indonesia",
        "arrived_at_indonesia",
        "ready_to_ship",
      ].includes(st);
    if (statusFilter.value === "shipped") return st === "shipped";
    if (statusFilter.value === "completed") return st === "completed";
    if (statusFilter.value === "cancelled")
      return st === "cancelled" || st === "expired";
    return true;
  });
});

onMounted(async () => {
  if (authStore.isLoggedIn && activeTab.value === "member") {
    await loadMemberOrders();
  }
});

async function loadMemberOrders() {
  loadingOrders.value = true;
  try {
    const res = await orderService.getOrders();
    ordersList.value = res.data;
  } catch (error: any) {
    console.error("Gagal memuat pesanan:", error);
    toast.add({
      title: "Gagal Memuat Pesanan",
      description:
        "Terjadi kesalahan koneksi saat memuat riwayat pesanan Anda.",
      color: "error",
    });
  } finally {
    loadingOrders.value = false;
  }
}

async function handleGuestLookup() {
  if (!lookupState.orderNumber || !lookupState.email) {
    toast.add({
      title: "Validasi Gagal",
      description: "Mohon isi Nomor Pesanan dan Email Anda.",
      color: "error",
    });
    return;
  }

  loadingLookup.value = true;

  try {
    const order = await orderService.lookupOrder(
      lookupState.orderNumber,
      lookupState.email,
    );
    toast.add({
      title: "Pesanan Ditemukan",
      description: "Mengarahkan ke detail pesanan Anda...",
      color: "primary",
    });
    router.push(`/orders/${order.orderNumber}?email=${lookupState.email}`);
  } catch (error: any) {
    console.error("Gagal melacak pesanan:", error);
    toast.add({
      title: "Pesanan Tidak Ditemukan",
      description: error.data?.message || "Nomor pesanan atau email salah.",
      color: "error",
    });
  } finally {
    loadingLookup.value = false;
  }
}

function getStatusBadgeColor(
  status: string,
):
  | "error"
  | "primary"
  | "secondary"
  | "success"
  | "info"
  | "warning"
  | "neutral" {
  switch (status.toLowerCase()) {
    case "paid":
      return "primary";
    case "processing":
    case "ordered":
    case "arrived_at_proxy":
      return "info";
    case "shipped_to_indonesia":
    case "arrived_at_indonesia":
      return "warning";
    case "ready_to_ship":
      return "primary";
    case "shipped":
    case "completed":
      return "success";
    case "pending":
      return "warning";
    case "cancelled":
    case "expired":
      return "error";
    default:
      return "neutral";
  }
}

function getStatusLabel(status: string): string {
  switch (status.toLowerCase()) {
    case "paid":
      return "Terbayar";
    case "processing":
      return "Diproses";
    case "ordered":
      return "Dipesan dari Asal";
    case "arrived_at_proxy":
      return "Tiba di Gudang Transit";
    case "shipped_to_indonesia":
      return "Pengiriman ke Indonesia";
    case "arrived_at_indonesia":
      return "Tiba di Gudang Indonesia";
    case "ready_to_ship":
      return "Siap Dikirim (Perlu Pelunasan)";
    case "shipped":
      return "Dalam Pengiriman";
    case "completed":
      return "Selesai";
    case "pending":
      return "Menunggu Pembayaran";
    case "cancelled":
      return "Dibatalkan";
    case "expired":
      return "Kedaluwarsa";
    default:
      return status;
  }
}

function formatDate(dateStr: string): string {
  if (!dateStr) return "";
  const d = new Date(dateStr);
  return d.toLocaleDateString("id-ID", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

function copyOrderNumber(orderNum: string) {
  navigator.clipboard.writeText(orderNum);
  toast.add({
    title: "Nomor Pesanan Disalin",
    description: `${orderNum} telah disalin ke clipboard.`,
    color: "primary",
  });
}
</script>

<template>
  <div
    class="relative overflow-hidden bg-linear-to-b from-primary-50/40 via-white to-gray-50/50 min-h-screen py-8 md:py-12"
  >
    <!-- Background Decor Elements -->
    <div
      class="absolute top-12 -left-40 w-96 h-96 bg-primary-200/20 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-[400px] -right-40 w-96 h-96 bg-primary-100/20 rounded-full blur-3xl pointer-events-none"
    ></div>
    <AppCuteDecor />

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 space-y-8">
      <!-- Breadcrumb & Header Banner -->
      <div
        class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white/80 backdrop-blur-md border border-gray-200/80 rounded-2xl p-4 shadow-xs"
      >
        <div class="flex items-center gap-3">
          <div
            class="w-10 h-10 rounded-xl bg-linear-to-tr from-primary-600 to-primary-400 flex items-center justify-center text-white shadow-md shadow-primary-500/20 shrink-0"
          >
            <UIcon name="i-heroicons-shopping-bag" class="w-5 h-5" />
          </div>
          <div>
            <div
              class="flex items-center gap-2 text-xs font-semibold text-gray-500"
            >
              <NuxtLink to="/" class="hover:text-primary-600">Beranda</NuxtLink>
              <span>/</span>
              <span class="text-primary-600 font-bold">Riwayat Pesanan</span>
            </div>
            <h1
              class="text-lg md:text-xl font-black text-gray-900 tracking-tight"
            >
              Daftar Transaksi Saya
            </h1>
          </div>
        </div>

        <!-- Total Orders Pill Badge -->
        <div
          v-if="authStore.isLoggedIn"
          class="flex items-center gap-2 bg-primary-50 border border-primary-200/60 px-3.5 py-1.5 rounded-full text-xs font-extrabold text-primary-700 w-fit"
        >
          <UIcon name="i-heroicons-receipt-percent" class="w-4 h-4" />
          <span>{{ ordersList.length }} Transaksi Terdaftar</span>
        </div>
      </div>

      <!-- Status Filter Tabs (For Member) -->
      <div v-if="authStore.isLoggedIn" class="space-y-4">
        <div
          class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none"
        >
          <button
            v-for="st in [
              { label: 'Semua', value: 'all' },
              { label: 'Belum Bayar', value: 'pending' },
              { label: 'Diproses / Dikirim', value: 'processing' },
              { label: 'Selesai', value: 'completed' },
              { label: 'Dibatalkan', value: 'cancelled' },
            ]"
            :key="st.value"
            type="button"
            class="px-4 py-2 rounded-xl text-xs font-extrabold whitespace-nowrap transition-all duration-300 cursor-pointer shadow-2xs border"
            :class="
              statusFilter === st.value
                ? 'bg-primary-500 text-white border-primary-500 shadow-md shadow-primary-600/20'
                : 'bg-white/80 text-gray-600 border-gray-200/80 hover:bg-gray-50 hover:text-gray-900'
            "
            @click="statusFilter = st.value"
          >
            {{ st.label }}
          </button>
        </div>
      </div>

      <!-- Member Orders List -->
      <div v-if="authStore.isLoggedIn" class="space-y-4">
        <!-- Loading Spinner -->
        <div
          v-if="loadingOrders"
          class="flex flex-col items-center justify-center py-20 bg-white/80 backdrop-blur-md rounded-3xl border border-gray-200/80 gap-3"
        >
          <UIcon
            name="i-heroicons-arrow-path"
            class="w-8 h-8 animate-spin text-primary-600"
          />
          <span class="text-xs text-gray-500 font-extrabold">
            Memuat riwayat transaksi Anda...
          </span>
        </div>

        <!-- Empty State -->
        <div
          v-else-if="filteredOrders.length === 0"
          class="max-w-lg mx-auto bg-white/90 backdrop-blur-md border border-gray-200/80 rounded-3xl p-8 sm:p-12 shadow-xl shadow-gray-900/5 text-center"
        >
          <EmptyState
            icon="i-heroicons-shopping-bag"
            title="Belum Ada Transaksi"
            description="Yuk telusuri katalog produk BbystarGO dan pesan barang favoritmu dari Korea, China, Jepang &amp; Indonesia!"
            action-label="Mulai Belanja Sekarang"
            action-to="/katalog"
          />
        </div>

        <!-- Orders Cards Grid -->
        <div v-else class="space-y-4">
          <div
            v-for="order in filteredOrders"
            :key="order.id"
            class="bg-white/90 backdrop-blur-md border border-gray-200/80 rounded-3xl p-5 sm:p-6 shadow-sm hover:shadow-xl hover:shadow-gray-900/5 hover:border-primary-300 transition-all duration-300 space-y-4 group cursor-pointer"
            @click="router.push(`/orders/${order.orderNumber}`)"
          >
            <!-- Card Header: Order Number, Date, Status -->
            <div
              class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-3.5"
            >
              <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                  <span class="text-xs font-black text-gray-900 tracking-tight">
                    {{ order.orderNumber }}
                  </span>
                  <button
                    type="button"
                    class="p-1 text-gray-400 hover:text-primary-600 rounded-md hover:bg-primary-50 transition-colors"
                    title="Salin Nomor Pesanan"
                    @click.stop="copyOrderNumber(order.orderNumber)"
                  >
                    <UIcon
                      name="i-heroicons-document-duplicate"
                      class="w-3.5 h-3.5"
                    />
                  </button>
                </div>
                <span class="text-gray-300 text-xs font-bold">•</span>
                <span class="text-xs text-gray-500 font-semibold">
                  {{ formatDate(order.createdAt) }}
                </span>
              </div>

              <UBadge
                :color="getStatusBadgeColor(order.orderStatus)"
                size="md"
                class="font-black w-fit rounded-full px-3 py-1 text-[11px]"
              >
                {{ getStatusLabel(order.orderStatus) }}
              </UBadge>
            </div>

            <!-- Card Middle: Product Item Preview -->
            <div class="flex items-center justify-between gap-4">
              <div class="flex items-center gap-4 min-w-0 flex-1">
                <div
                  class="relative shrink-0 w-12 h-12 sm:w-14 sm:h-14 rounded-2xl overflow-hidden border border-primary-200/60 bg-linear-to-tr from-primary-100 via-primary-50 to-primary-100 flex items-center justify-center text-primary-600 shadow-2xs group-hover:scale-105 transition-transform duration-300"
                >
                  <img
                    v-if="order.items[0]?.image"
                    :src="order.items[0].image"
                    :alt="order.items[0].productName"
                    class="w-full h-full object-cover"
                  />
                  <UIcon v-else name="i-heroicons-shopping-bag" class="w-6 h-6" />
                </div>

                <div class="min-w-0 space-y-1">
                  <p
                    class="text-sm font-bold text-gray-900 group-hover:text-primary-600 transition-colors truncate"
                  >
                    {{ order.items[0]?.productName }}
                  </p>
                  <div
                    class="flex items-center gap-2 text-xs text-gray-500 font-semibold"
                  >
                    <span
                      v-if="order.items[0]?.variantName"
                      class="px-2 py-0.5 bg-gray-100 rounded-md text-[11px]"
                    >
                      Varian: {{ order.items[0]?.variantName }}
                    </span>
                    <span>x{{ order.items[0]?.qty }}</span>
                    <span
                      v-if="order.items.length > 1"
                      class="text-primary-600 font-bold text-[11px] bg-primary-50 px-2 py-0.5 rounded-md"
                    >
                      +{{ order.items.length - 1 }} produk lainnya
                    </span>
                  </div>
                  <p class="text-[11px] text-gray-400 font-semibold uppercase">
                    Kurir: {{ order.courier }} ({{ order.courierService }})
                  </p>
                </div>
              </div>

              <!-- Card Total Invoice & Action Button -->
              <div class="flex flex-col items-end gap-2 shrink-0">
                <div class="text-right">
                  <span
                    class="text-[10px] text-gray-400 font-semibold uppercase block"
                    >Total Tagihan</span
                  >
                  <span
                    class="text-base sm:text-lg font-black text-primary-600"
                  >
                    {{ format(order.total) }}
                  </span>
                </div>

                <UButton
                  color="neutral"
                  variant="outline"
                  size="xs"
                  class="rounded-xl font-bold text-xs px-3 py-1.5 cursor-pointer hover:bg-primary-600 group-hover:bg-primary-500 group-hover:text-white group-hover:border-primary-600 transition-all duration-300"
                >
                  <span>Detail Pesanan</span>
                  <UIcon name="i-heroicons-arrow-right" class="w-3.5 h-3.5" />
                </UButton>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Guest Lookup Section (If logged out) -->
      <div
        v-else
        class="max-w-lg mx-auto bg-white/90 backdrop-blur-md border border-gray-200/80 rounded-3xl p-8 sm:p-10 shadow-xl shadow-gray-900/5 space-y-6"
      >
        <div class="text-center space-y-2">
          <div
            class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center mx-auto shadow-xs border border-primary-100"
          >
            <UIcon name="i-heroicons-magnifying-glass" class="w-6 h-6" />
          </div>
          <h2 class="text-lg font-extrabold text-gray-900">
            Lacak Pesanan Pembeli Guest
          </h2>
          <p class="text-xs text-gray-500 max-w-sm mx-auto leading-relaxed">
            Masukkan nomor pesanan transaksi Anda beserta email aktif pemesan
            untuk mengecek status terkini.
          </p>
        </div>

        <form @submit.prevent="handleGuestLookup" class="space-y-4">
          <UFormField label="Nomor Pesanan" required>
            <UInput
              v-model="lookupState.orderNumber"
              placeholder="Contoh: ORD-20260625-XXXXXX"
              class="w-full"
            />
          </UFormField>

          <UFormField label="Email Pemesan" required>
            <UInput
              v-model="lookupState.email"
              type="email"
              placeholder="email@domain.com"
              class="w-full"
            />
          </UFormField>

          <div class="pt-3">
            <UButton
              type="submit"
              block
              color="primary"
              size="lg"
              class="font-black py-4 rounded-2xl cursor-pointer shadow-lg shadow-primary-500/25 hover:shadow-primary-500/35 hover:scale-[1.01] transition-all duration-300"
              :loading="loadingLookup"
            >
              Lacak Status Pesanan
            </UButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
