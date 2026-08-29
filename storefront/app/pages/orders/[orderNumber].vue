<script setup lang="ts">
import { orderService } from "~/services/orderService";
import { useCurrency } from "~/composables/useCurrency";
import { useApi } from "~/services/api";
import type { Order, OrderItem } from "~/types";

const route = useRoute();
const { format } = useCurrency();
const toast = useToast();
const router = useRouter();

const orderNumber = computed(() => route.params.orderNumber as string);
const guestEmail = computed(() => (route.query.email as string) || "");

const api = useApi();
const { data: paymentMethodsResponse } = useAsyncData("payment-methods", () =>
  api.getPaymentMethods(),
);
const paymentMethods = computed(() => paymentMethodsResponse.value?.data ?? []);

const { data: settingsResponse } = useAsyncData("store-settings-order", () =>
  api.getSettings(),
);
const settings = computed(() => settingsResponse.value?.data ?? {});

const proofFile = ref<File | null>(null);
const uploadingProof = ref(false);
const proofError = ref("");

// Order state
const order = ref<Order | null>(null);
const loading = ref(false);

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

function onProofFileChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0] ?? null;
  proofError.value = "";
  if (file && file.size > 2 * 1024 * 1024) {
    proofError.value = "Ukuran file maksimal 2MB.";
    proofFile.value = null;
    return;
  }
  proofFile.value = file;
}

async function submitProof() {
  if (!proofFile.value || !order.value) return;
  uploadingProof.value = true;
  try {
    order.value = await orderService.uploadPaymentProof(
      order.value.orderNumber,
      proofFile.value,
      guestEmail.value || undefined,
    );
    proofFile.value = null;
    toast.add({
      title: "Bukti Terkirim",
      description: "Bukti pembayaran berhasil diunggah, menunggu verifikasi admin.",
      color: "primary",
    });
  } catch (error: any) {
    toast.add({
      title: "Gagal Mengunggah",
      description: error.data?.message || "Terjadi kesalahan, coba lagi.",
      color: "error",
    });
  } finally {
    uploadingProof.value = false;
  }
}

function whatsappConfirmUrl(): string {
  const phone = settings.value.whatsapp_number;
  if (!phone || !order.value) return "";
  const text = encodeURIComponent(
    `Konfirmasi pembayaran pesanan ${order.value.orderNumber}`,
  );
  return `https://api.whatsapp.com/send/?phone=${phone}&text=${text}&type=phone_number&app_absent=0`;
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
        <div class="bg-white/80 backdrop-blur-xs border border-primary-100/35 rounded-3xl p-6 md:p-8 shadow-[0_4px_25px_-6px_rgba(255,116,108,0.03)] flex flex-col md:flex-row md:items-center justify-between gap-6">
          <div class="space-y-2">
            <div class="flex items-center gap-3">
              <h1 class="text-lg font-black text-gray-900 tracking-tight">{{ order.orderNumber }}</h1>
              <UBadge :color="getStatusBadgeColor(order.orderStatus)" size="sm" class="font-extrabold">
                {{ getStatusLabel(order.orderStatus) }}
              </UBadge>
            </div>
            <p class="text-xs text-gray-400 font-medium">Tanggal Transaksi: {{ formatDate(order.createdAt) }}</p>
          </div>
        </div>

        <!-- Detail Grid -->
        <div class="grid grid-cols-1 md:grid-cols-[1fr_300px] gap-8">
          
          <!-- Left side: Items Table -->
          <div class="bg-white/80 backdrop-blur-xs border border-primary-100/35 rounded-3xl p-6 md:p-8 shadow-[0_4px_20px_-4px_rgba(255,116,108,0.02)] space-y-6">
            <h2 class="text-xs uppercase tracking-wider font-extrabold text-primary-500 border-b border-primary-100/30 pb-3">
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
                  <div v-if="order.orderStatus.toLowerCase() === 'completed'">
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
            <div class="bg-white/80 backdrop-blur-xs border border-primary-100/35 rounded-3xl p-6 shadow-[0_4px_20px_-4px_rgba(255,116,108,0.02)] space-y-4">
              <h2 class="text-xs uppercase tracking-wider font-extrabold text-primary-500 border-b border-primary-100/30 pb-2">
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
            <div class="bg-linear-to-b from-primary-50/50 via-white/80 to-white/95 backdrop-blur-xs border border-primary-100/40 rounded-3xl p-6 shadow-[0_4px_25px_-6px_rgba(255,116,108,0.03)] space-y-4">
              <h2 class="text-xs uppercase tracking-wider font-extrabold text-primary-500 border-b border-primary-100/30 pb-2">
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

            <!-- Payment Instructions & Proof Upload -->
            <div
              v-if="order.paymentStatus === 'unpaid' || order.paymentStatus === 'rejected'"
              class="bg-white/80 backdrop-blur-xs border border-primary-100/35 rounded-3xl p-6 shadow-[0_4px_20px_-4px_rgba(255,116,108,0.02)] space-y-4"
            >
              <h2 class="text-xs uppercase tracking-wider font-extrabold text-primary-500 border-b border-primary-100/30 pb-2">
                Instruksi Pembayaran
              </h2>

              <p v-if="order.paymentStatus === 'rejected'" class="text-xs text-red-600 font-semibold bg-red-50 rounded-xl p-3">
                Bukti pembayaran sebelumnya ditolak{{ order.paymentRejectedReason ? ': ' + order.paymentRejectedReason : '' }}. Silakan unggah ulang.
              </p>

              <div v-for="method in paymentMethods" :key="method.id" class="text-xs border border-gray-100 rounded-2xl p-3.5 space-y-1">
                <p class="font-extrabold text-gray-900">{{ method.label }}</p>
                <p v-if="method.account_number" class="text-gray-600 font-semibold select-all">{{ method.account_number }}</p>
                <p v-if="method.account_holder" class="text-gray-400">a/n {{ method.account_holder }}</p>
                <img v-if="method.qr_image" :src="method.qr_image" alt="QR" class="w-28 h-28 object-contain mt-2" />
              </div>

              <div class="space-y-2 pt-2">
                <input type="file" accept="image/jpeg,image/png,image/webp" class="text-xs" @change="onProofFileChange" />
                <p v-if="proofError" class="text-[10px] text-red-500 font-semibold">{{ proofError }}</p>
                <UButton
                  block
                  color="primary"
                  size="sm"
                  class="font-bold rounded-xl cursor-pointer"
                  :disabled="!proofFile"
                  :loading="uploadingProof"
                  @click="submitProof"
                >
                  Kirim Bukti Transfer
                </UButton>
              </div>
            </div>

            <div
              v-else-if="order.paymentStatus === 'awaiting_verification'"
              class="bg-amber-50 border border-amber-100 rounded-3xl p-6 shadow-xs text-center"
            >
              <p class="text-xs font-bold text-amber-700">Menunggu verifikasi admin.</p>
              <p class="text-[10px] text-amber-600 mt-1">Bukti pembayaran sudah diterima, proses verifikasi biasanya memakan waktu beberapa jam.</p>
            </div>

            <!-- WhatsApp/LINE Confirmation -->
            <div v-if="order.paymentStatus !== 'unpaid'" class="grid grid-cols-1 gap-2.5">
              <a
                v-if="settings.whatsapp_number"
                :href="whatsappConfirmUrl()"
                target="_blank"
                rel="noopener"
                class="flex items-center justify-center gap-2 text-xs font-bold rounded-xl px-4 py-3 border border-primary-200 text-primary-700 bg-primary-50/40 hover:bg-primary-50 transition-colors"
              >
                <UIcon name="i-simple-icons-whatsapp" class="w-4 h-4" />
                Konfirmasi via WhatsApp
              </a>
              <a
                v-if="settings.line_url"
                :href="settings.line_url"
                target="_blank"
                rel="noopener"
                class="flex items-center justify-center gap-2 text-xs font-bold rounded-xl px-4 py-3 border border-gray-200 text-gray-700 bg-gray-50 hover:bg-gray-100 transition-colors"
              >
                Konfirmasi via LINE
              </a>
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
          <UButton color="neutral" variant="outline" class="flex-1 rounded-xl cursor-pointer" @click="() => { ratingModalOpen = false }">Batal</UButton>
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
