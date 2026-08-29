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

const activeTab = ref(authStore.isLoggedIn ? "member" : "guest");

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
      description: "Terjadi kesalahan koneksi saat memuat riwayat pesanan Anda.",
      color: "error",
    });
  } finally {
    loadingOrders.value = false;
  }
}

async function handleGuestLookup() {
  if (!lookupState.orderNumber || !lookupState.email) {
    toast.add({ title: "Validasi Gagal", description: "Mohon isi Nomor Pesanan dan Email Anda.", color: "error" });
    return;
  }

  loadingLookup.value = true;

  try {
    // Attempt lookup to verify it exists before redirecting
    const order = await orderService.lookupOrder(lookupState.orderNumber, lookupState.email);
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

function getStatusBadgeColor(status: string): "error" | "primary" | "secondary" | "success" | "info" | "warning" | "neutral" {
  switch (status.toLowerCase()) {
    case "paid":
      return "primary";
    case "processing":
      return "info";
    case "shipped":
      return "success";
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
      return "Dibayar";
    case "processing":
      return "Diproses";
    case "shipped":
      return "Dikirim";
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
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}
</script>

<template>
  <div class="relative overflow-hidden bg-linear-to-b from-primary-50/45 via-primary-50/15 to-white min-h-screen py-10">
    <!-- Ambient Background Glows -->
    <div class="absolute top-12 -left-40 w-96 h-96 bg-primary-200/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-[400px] -right-40 w-96 h-96 bg-primary-100/20 rounded-full blur-3xl pointer-events-none"></div>
    <AppCuteDecor />

    <div class="max-w-4xl mx-auto px-4 sm:px-6 relative z-20">
      <!-- Title Block -->
      <div class="mb-8 flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-primary-50/80 flex items-center justify-center text-primary-500 shadow-sm border border-primary-100/50">
          <UIcon name="i-heroicons-shopping-bag" class="w-5 h-5" />
        </div>
        <div>
          <span class="text-[10px] text-primary-500 uppercase font-extrabold tracking-wider">Histori Pembelian</span>
          <h1 class="text-xl md:text-2xl font-black text-gray-900 tracking-tight leading-none mt-0.5">Daftar Transaksi</h1>
        </div>
      </div>

      <!-- Tabs Navigation -->
      <div class="mb-8 border-b border-primary-100/40 flex items-center gap-2">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          type="button"
          class="pb-3.5 px-4 text-sm font-bold border-b-2 transition-all duration-200 cursor-pointer"
          :class="activeTab === tab.value
            ? 'border-primary-500 text-primary-500'
            : 'border-transparent text-gray-400 hover:text-primary-500'"
          @click="activeTab = tab.value"
        >
          {{ tab.label }}
        </button>
      </div>

      <!-- Tab Content: Member Orders List -->
      <div v-if="activeTab === 'member' && authStore.isLoggedIn" class="space-y-4">
        <div v-if="loadingOrders" class="flex flex-col items-center justify-center py-16 gap-3">
          <UIcon name="i-heroicons-arrow-path" class="w-8 h-8 animate-spin text-primary-600" />
          <span class="text-xs text-gray-400 font-bold">Memuat riwayat transaksi Anda...</span>
        </div>

        <div v-else-if="ordersList.length === 0" class="bg-white/80 backdrop-blur-xs border border-primary-100/35 rounded-3xl p-10 text-center shadow-[0_4px_25px_-6px_rgba(255,116,108,0.03)]">
          <UIcon name="i-heroicons-shopping-bag" class="w-10 h-10 text-gray-300 mx-auto mb-3" />
          <p class="text-sm font-extrabold text-gray-900">Belum ada transaksi</p>
          <p class="text-xs text-gray-400 font-semibold mt-1 mb-5">Temukan berbagai produk Ready Stock &amp; Pre Order pilihan di katalog kami.</p>
          <UButton color="primary" variant="solid" to="/katalog" class="rounded-full font-bold px-6 py-2 cursor-pointer shadow-md shadow-primary-600/10">Mulai Belanja</UButton>
        </div>

        <div v-else class="space-y-5">
          <div
            v-for="order in ordersList"
            :key="order.id"
            class="bg-white/80 backdrop-blur-xs border border-primary-100/35 rounded-3xl p-6 shadow-[0_4px_20px_-4px_rgba(255,116,108,0.02)] flex flex-col md:flex-row md:items-center justify-between gap-6 hover:shadow-md hover:shadow-primary-500/8 hover:border-primary-100/60 transition-all duration-250 cursor-pointer"
            @click="router.push(`/orders/${order.orderNumber}`)"
          >
            <div class="space-y-3 flex-1 min-w-0">
              <div class="flex flex-wrap items-center gap-3">
                <span class="text-xs font-black text-gray-900 leading-none">{{ order.orderNumber }}</span>
                <span class="text-[10px] text-gray-400 font-bold">•</span>
                <span class="text-xs text-gray-400 font-semibold">{{ formatDate(order.createdAt) }}</span>
              </div>

              <!-- Product Preview -->
              <div class="flex items-center gap-3.5 pt-1.5">
                <div class="flex -space-x-4 overflow-hidden shrink-0">
                  <div
                    v-for="(item, idx) in order.items.slice(0, 3)"
                    :key="item.id"
                    class="w-10 h-10 rounded-lg border-2 border-white overflow-hidden bg-gray-50 shrink-0"
                  >
                    <!-- Visual preview item -->
                    <div class="w-full h-full bg-primary-50 text-[10px] text-primary-700 font-extrabold flex items-center justify-center">
                      {{ item.productName.slice(0,2).toUpperCase() }}
                    </div>
                  </div>
                </div>
                <div class="min-w-0">
                  <p class="text-xs font-extrabold text-gray-900 truncate">
                    {{ order.items[0]?.productName }}
                    <span v-if="order.items.length > 1" class="text-gray-400 font-medium text-[11px]">
                      dan {{ order.items.length - 1 }} produk lainnya
                    </span>
                  </p>
                  <p class="text-[10px] text-gray-400 font-medium uppercase mt-0.5">Kurir: {{ order.courier }} ({{ order.courierService }})</p>
                </div>
              </div>
            </div>

            <div class="flex md:flex-col items-between md:items-end justify-between gap-2 border-t md:border-t-0 border-gray-50 pt-4 md:pt-0">
              <div class="flex flex-col md:items-end">
                <span class="text-[10px] text-gray-400 font-semibold leading-none">Total Tagihan</span>
                <span class="text-sm font-black text-primary-700 mt-1 leading-none">{{ format(order.total) }}</span>
              </div>
              <UBadge :color="getStatusBadgeColor(order.status)" size="sm" class="font-extrabold mt-1">
                {{ getStatusLabel(order.status) }}
              </UBadge>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab Content: Guest Lookup Form -->
      <div v-else class="max-w-md mx-auto bg-white/80 backdrop-blur-xs border border-primary-100/35 rounded-3xl p-8 md:p-10 shadow-[0_4px_25px_-6px_rgba(255,116,108,0.03)]">
        <div class="text-center mb-6">
          <UIcon name="i-heroicons-magnifying-glass" class="w-8 h-8 text-primary-600 mx-auto mb-2" />
          <h2 class="text-base font-extrabold text-gray-900">Lacak Pesanan Tanpa Akun</h2>
          <p class="text-xs text-gray-400 font-semibold mt-1">Masukkan nomor pesanan dan email pemesan untuk melacak pengiriman Anda.</p>
        </div>

        <form @submit.prevent="handleGuestLookup" class="space-y-4">
          <UFormField label="Nomor Pesanan" required>
            <UInput v-model="lookupState.orderNumber" placeholder="Contoh: ORD-20260625-XXXXXX" class="w-full" />
          </UFormField>

          <UFormField label="Email Pemesan" required>
            <UInput v-model="lookupState.email" type="email" placeholder="Contoh: guest@example.com" class="w-full" />
          </UFormField>

          <div class="pt-3">
            <UButton
              type="submit"
              block
              color="primary"
              size="lg"
              class="font-extrabold py-3.5 rounded-full cursor-pointer shadow-md shadow-primary-600/10"
              :loading="loadingLookup"
            >
              Lacak Pesanan
            </UButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
