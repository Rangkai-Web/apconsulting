<script setup lang="ts">
import { orderService } from "~/services/orderService";
import { useCurrency } from "~/composables/useCurrency";
import type { Order, OrderItem } from "~/types";

const route = useRoute();
const { format } = useCurrency();
const toast = useToast();
const router = useRouter();

const orderNumber = computed(() => route.params.orderNumber as string);
const guestEmail = computed(() => (route.query.email as string) || "");

// Order state
const order = ref<Order | null>(null);
const loading = ref(false);

// Payment retry state
const loadingPayment = ref(false);

// Rating state
const ratingModalOpen = ref(false);
const itemToRate = ref<OrderItem | null>(null);
const ratingStars = ref(5);
const loadingRating = ref(false);

onMounted(async () => {
  await loadOrderDetails();
});

async function loadOrderDetails() {
  loading.value = true;
  try {
    if (guestEmail.value) {
      order.value = await orderService.lookupOrder(orderNumber.value, guestEmail.value);
    } else {
      order.value = await orderService.getOrderDetails(orderNumber.value);
    }
  } catch (error: any) {
    console.error("Gagal memuat detail pesanan:", error);
    toast.add({
      title: "Pesanan Tidak Ditemukan",
      description: "Gagal memuat informasi detail pesanan Anda.",
      color: "error",
    });
  } finally {
    loading.value = false;
  }
}

// Midtrans Snap script loader (same helper)
function loadMidtransSnapScript(): Promise<boolean> {
  return new Promise((resolve) => {
    if ((window as any).snap) return resolve(true);

    const config = useRuntimeConfig();
    const snapUrl = config.public.midtransIsProduction
      ? "https://app.midtrans.com/snap/snap.js"
      : "https://app.sandbox.midtrans.com/snap/snap.js";

    const script = document.createElement("script");
    script.src = snapUrl;
    script.setAttribute("data-client-key", config.public.midtransClientKey);
    script.onload = () => resolve(true);
    script.onerror = () => resolve(false);
    document.head.appendChild(script);
  });
}

// Retry payment for pending orders
async function handleRetryPayment() {
  if (!order.value) return;

  loadingPayment.value = true;

  try {
    const snapToken = await orderService.requestSnapToken(order.value.orderNumber);

    if (snapToken.startsWith("mock-snap-token-")) {
      toast.add({
        title: "Pembayaran Sukses (Mock Mode)",
        description: "Simulasi pembayaran sukses untuk pengujian lokal.",
        color: "primary",
      });
      // In mock mode, let's call webhook on backend directly or change order status locally for mock validation
      await loadOrderDetails();
      return;
    }

    const scriptLoaded = await loadMidtransSnapScript();
    if (!scriptLoaded) {
      throw new Error("Gagal memuat modul pembayaran Midtrans.");
    }

    (window as any).snap.pay(snapToken, {
      onSuccess: function () {
        toast.add({ title: "Pembayaran Sukses", description: "Terima kasih atas pesanan Anda!", color: "primary" });
        loadOrderDetails();
      },
      onPending: function () {
        toast.add({ title: "Pembayaran Tertunda", description: "Selesaikan pembayaran Anda segera.", color: "warning" });
        loadOrderDetails();
      },
      onError: function () {
        toast.add({ title: "Pembayaran Gagal", description: "Transaksi Anda gagal diproses.", color: "error" });
      },
      onClose: function () {
        toast.add({ title: "Pembayaran Ditutup", description: "Selesaikan pembayaran sebelum batas waktu berakhir.", color: "neutral" });
      },
    });

  } catch (error: any) {
    console.error("Gagal membuka modul pembayaran:", error);
    toast.add({
      title: "Koneksi Gagal",
      description: error.data?.message || "Gagal memproses pembayaran. Coba lagi.",
      color: "error",
    });
  } finally {
    loadingPayment.value = false;
  }
}

// Rating submission
function openRatingModal(item: OrderItem) {
  itemToRate.value = item;
  ratingStars.value = 5;
  ratingModalOpen.value = true;
}

async function submitRating() {
  if (!itemToRate.value) return;

  loadingRating.value = true;

  try {
    await orderService.rateOrderItem(itemToRate.value.id, ratingStars.value);
    toast.add({
      title: "Rating Dikirim",
      description: "Terima kasih atas ulasan produk Anda!",
      color: "primary",
    });
    ratingModalOpen.value = false;
    itemToRate.value = null;
    await loadOrderDetails();
  } catch (error: any) {
    console.error("Gagal mengirim rating:", error);
    toast.add({
      title: "Gagal Mengirim Rating",
      description: error.data?.message || "Terjadi kesalahan saat memproses rating.",
      color: "error",
    });
  } finally {
    loadingRating.value = false;
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
  <div class="relative overflow-hidden bg-linear-to-b from-primary-50/15 via-white to-white min-h-screen py-10">
    <!-- Ambient Background Glows -->
    <div class="absolute top-12 -left-40 w-96 h-96 bg-primary-100/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-[400px] -right-40 w-96 h-96 bg-secondary-100/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 relative">
      <!-- Back button -->
      <div class="mb-6">
        <UButton
          icon="i-heroicons-arrow-left"
          color="neutral"
          variant="ghost"
          class="rounded-xl font-bold px-3 py-2 cursor-pointer"
          :to="guestEmail ? '/orders/cek' : '/orders/history'"
        >
          {{ guestEmail ? 'Kembali ke Cek Pesanan' : 'Kembali ke Riwayat Pesanan' }}
        </UButton>
      </div>

      <!-- Loading skeleton -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-20 gap-3">
        <UIcon name="i-heroicons-arrow-path" class="w-8 h-8 animate-spin text-primary-600" />
        <span class="text-xs text-gray-400 font-bold">Memuat detail transaksi...</span>
      </div>

      <!-- Not found -->
      <div v-else-if="!loading && !order" class="flex flex-col items-center justify-center py-24 gap-4">
        <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center shadow-sm">
          <UIcon name="i-heroicons-document-magnifying-glass" class="w-7 h-7 text-gray-400" />
        </div>
        <div class="text-center">
          <p class="text-sm font-extrabold text-gray-900">Pesanan Tidak Ditemukan</p>
          <p class="text-xs text-gray-400 font-medium mt-1">Nomor pesanan atau email tidak cocok.</p>
        </div>
        <UButton
          :to="guestEmail ? '/orders/cek' : '/orders/history'"
          color="primary"
          variant="outline"
          size="sm"
          class="rounded-full font-bold mt-2 cursor-pointer"
        >
          {{ guestEmail ? 'Coba Lagi' : 'Riwayat Pesanan' }}
        </UButton>
      </div>

      <!-- Main Content -->
      <div v-else-if="order" class="space-y-8">
        
        <!-- Header Info Card -->
        <div class="bg-white border border-gray-100 rounded-3xl p-6 md:p-8 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
          <div class="space-y-2">
            <div class="flex items-center gap-3">
              <h1 class="text-lg font-black text-gray-900 tracking-tight">{{ order.orderNumber }}</h1>
              <UBadge :color="getStatusBadgeColor(order.status)" size="sm" class="font-extrabold">
                {{ getStatusLabel(order.status) }}
              </UBadge>
            </div>
            <p class="text-xs text-gray-400 font-medium">Tanggal Transaksi: {{ formatDate(order.createdAt) }}</p>
          </div>

          <div v-if="order.status === 'pending'" class="shrink-0 flex items-center">
            <UButton
              color="primary"
              size="lg"
              class="font-extrabold px-6 rounded-full cursor-pointer shadow-md shadow-primary-600/15"
              :loading="loadingPayment"
              @click="handleRetryPayment"
            >
              Bayar Sekarang
            </UButton>
          </div>
        </div>

        <!-- Detail Grid -->
        <div class="grid grid-cols-1 md:grid-cols-[1fr_300px] gap-8">
          
          <!-- Left side: Items Table -->
          <div class="bg-white border border-gray-100 rounded-3xl p-6 md:p-8 shadow-xs space-y-6">
            <h2 class="text-xs uppercase tracking-wider font-extrabold text-primary-850 border-b border-gray-100 pb-3">
              Rincian Item Belanja
            </h2>

            <div class="divide-y divide-gray-100">
              <div v-for="item in order.items" :key="item.id" class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                <div class="min-w-0 flex-1">
                  <p class="text-xs font-extrabold text-gray-900 truncate">{{ item.productName }}</p>
                  <p class="text-[10px] text-gray-400 font-semibold mt-0.5">Varian: {{ item.variantName }} (x{{ item.qty }})</p>
                  <p class="text-xs font-black text-gray-700 mt-2">{{ format(item.price) }} / pcs</p>
                </div>
                
                <div class="text-right shrink-0 flex flex-col items-end gap-2.5">
                  <span class="text-xs font-black text-gray-800">{{ format(item.subtotal) }}</span>
                  
                  <!-- Star rating trigger -->
                  <div v-if="order.status.toLowerCase() === 'completed'">
                    <UButton
                      v-if="!item.rated"
                      icon="i-heroicons-star"
                      color="warning"
                      variant="subtle"
                      size="xs"
                      class="font-bold rounded-lg cursor-pointer"
                      @click="openRatingModal(item)"
                    >
                      Beri Rating
                    </UButton>
                    <span v-else class="text-[10px] text-primary-600 font-extrabold flex items-center gap-1">
                      <UIcon name="i-heroicons-check-circle" class="w-3.5 h-3.5" />
                      Sudah Dirating
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right side: Shipping & Payment Cost -->
          <div class="space-y-6">
            
            <!-- Shipping Details -->
            <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-xs space-y-4">
              <h2 class="text-xs uppercase tracking-wider font-extrabold text-primary-850 border-b border-gray-100 pb-2">
                Informasi Pengiriman
              </h2>

              <div class="space-y-3.5 text-xs">
                <div>
                  <span class="block text-gray-400 font-semibold">Penerima</span>
                  <span class="font-bold text-gray-800 block mt-0.5">{{ order.shippingAddress?.recipientName }}</span>
                  <span class="text-gray-500 mt-0.5 block">{{ order.shippingAddress?.phone }}</span>
                </div>

                <div>
                  <span class="block text-gray-400 font-semibold">Alamat Lengkap</span>
                  <span class="text-gray-600 font-medium block mt-0.5 leading-relaxed">
                    {{ order.shippingAddress?.fullAddress }}<br>
                    {{ order.shippingAddress?.district }}, {{ order.shippingAddress?.city }}, {{ order.shippingAddress?.province }} - {{ order.shippingAddress?.postalCode }}
                  </span>
                </div>

                <div>
                  <span class="block text-gray-400 font-semibold">Kurir Pengirim</span>
                  <span class="font-extrabold text-gray-800 block mt-0.5 uppercase">{{ order.courier }} - {{ order.courierService }}</span>
                </div>

                <div v-if="order.trackingNumber">
                  <span class="block text-gray-400 font-semibold">Nomor Resi Lacak</span>
                  <span class="font-black text-primary-700 block mt-0.5 select-all">{{ order.trackingNumber }}</span>
                </div>
              </div>
            </div>

            <!-- Receipt Card -->
            <div class="bg-primary-50/5 border border-primary-100/50 rounded-3xl p-6 shadow-xs space-y-4">
              <h2 class="text-xs uppercase tracking-wider font-extrabold text-primary-850 border-b border-primary-100/50 pb-2">
                Rincian Pembayaran
              </h2>

              <div class="space-y-3 text-xs font-semibold">
                <div class="flex items-center justify-between">
                  <span class="text-gray-500 font-medium">Subtotal Produk</span>
                  <span class="text-gray-800 font-extrabold">{{ format(order.subtotal) }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-gray-500 font-medium">Ongkos Kirim</span>
                  <span class="text-gray-800 font-extrabold">{{ format(order.shippingCost) }}</span>
                </div>
                <div class="flex items-center justify-between border-t border-primary-100 pt-3">
                  <span class="text-gray-900 font-extrabold">Total Bayar</span>
                  <span class="text-sm font-black text-primary-800">{{ format(order.total) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Rating Modal Dialog -->
    <UModal v-model:open="ratingModalOpen" title="Beri Rating Produk">
      <template #body>
        <div class="text-center py-4 space-y-4">
          <p class="text-xs text-gray-500 font-semibold leading-relaxed">
            Bagaimana kualitas produk<br>
            <strong class="text-gray-900">{{ itemToRate?.productName }} ({{ itemToRate?.variantName }})</strong>?
          </p>

          <!-- Star picker -->
          <div class="flex items-center justify-center gap-2 pt-2">
            <button
              v-for="star in 5"
              :key="star"
              type="button"
              class="text-3xl focus:outline-none transition-transform hover:scale-110 cursor-pointer"
              :class="star <= ratingStars ? 'text-amber-400' : 'text-gray-200'"
              @click="ratingStars = star"
            >
              ★
            </button>
          </div>
          <p class="text-[10px] text-amber-500 font-bold uppercase tracking-wider">{{ ratingStars }} Bintang</p>
        </div>
      </template>

      <template #footer>
        <div class="flex items-center justify-between gap-3 w-full">
          <UButton color="neutral" variant="outline" class="flex-1 rounded-xl cursor-pointer" @click="ratingModalOpen = false">Batal</UButton>
          <UButton
            color="primary"
            class="flex-1 rounded-xl font-bold cursor-pointer"
            :loading="loadingRating"
            @click="submitRating"
          >
            Kirim Ulasan
          </UButton>
        </div>
      </template>
    </UModal>
  </div>
</template>
