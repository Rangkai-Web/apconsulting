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

useSeoMeta({ title: "Detail Pesanan", robots: "noindex, nofollow" });
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
const copiedField = ref("");

// Order state
const order = ref<Order | null>(null);
const loading = ref(false);

// Rating state
const ratingModalOpen = ref(false);
const itemToRate = ref<OrderItem | null>(null);
const ratingStars = ref(5);
const loadingRating = ref(false);

// QRIS Modal state
const qrisModalOpen = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);
const filePreviewUrl = ref<string>();

onMounted(async () => {
  await loadOrderDetails();
});

async function loadOrderDetails() {
  loading.value = true;
  try {
    if (guestEmail.value) {
      order.value = await orderService.lookupOrder(
        orderNumber.value,
        guestEmail.value,
      );
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

function copyToClipboard(text: string, labelName: string) {
  if (!text) return;
  navigator.clipboard.writeText(text);
  copiedField.value = labelName;
  toast.add({
    title: "Teks Disalin",
    description: `${labelName} berhasil disalin ke clipboard!`,
    color: "primary",
  });
  setTimeout(() => {
    copiedField.value = "";
  }, 2500);
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
      description:
        error.data?.message || "Terjadi kesalahan saat memproses rating.",
      color: "error",
    });
  } finally {
    loadingRating.value = false;
  }
}

function openQrisModal() {
  qrisModalOpen.value = true;
}

function triggerFileSelect() {
  fileInput.value?.click();
}

function onFileDrop(e: DragEvent) {
  const file = e.dataTransfer?.files?.[0] ?? null;
  handleFile(file);
}

function onProofFileChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0] ?? null;
  handleFile(file);
}

function handleFile(file: File | null) {
  proofError.value = "";
  if (!file) return;

  if (file.size > 2 * 1024 * 1024) {
    proofError.value = "Ukuran file maksimal 2MB.";
    proofFile.value = null;
    filePreviewUrl.value = undefined;
    return;
  }

  proofFile.value = file;
  filePreviewUrl.value = URL.createObjectURL(file);
}

function clearSelectedFile() {
  proofFile.value = null;
  if (filePreviewUrl.value) {
    URL.revokeObjectURL(filePreviewUrl.value);
    filePreviewUrl.value = undefined;
  }
  if (fileInput.value) {
    fileInput.value.value = "";
  }
}

function formatFileSize(bytes: number): string {
  if (bytes === 0) return "0 B";
  const k = 1024;
  const sizes = ["B", "KB", "MB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
}

onBeforeUnmount(() => {
  if (filePreviewUrl.value) {
    URL.revokeObjectURL(filePreviewUrl.value);
  }
});

async function submitProof() {
  if (!proofFile.value || !order.value) return;
  uploadingProof.value = true;
  try {
    order.value = await orderService.uploadPaymentProof(
      order.value.orderNumber,
      proofFile.value,
      guestEmail.value || undefined,
    );
    clearSelectedFile();
    toast.add({
      title: "Bukti Terkirim",
      description:
        "Bukti pembayaran berhasil diunggah, menunggu verifikasi admin.",
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
      return "info";
    case "ordered":
      return "info";
    case "arrived_at_proxy":
      return "info";
    case "shipped_to_indonesia":
      return "warning";
    case "arrived_at_indonesia":
      return "warning";
    case "ready_to_ship":
      return "primary";
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
    case "ordered":
      return "Telah Dipesan";
    case "arrived_at_proxy":
      return "Tiba di Warehouse Proxy";
    case "shipped_to_indonesia":
      return "Pengiriman ke Indonesia";
    case "arrived_at_indonesia":
      return "Tiba di Warehouse Indonesia";
    case "ready_to_ship":
      return "Siap Dikirim (Menunggu Pelunasan)";
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

function getTimelineStep(status: string): number {
  switch (status.toLowerCase()) {
    case "pending":
      return 1;
    case "paid":
      return 2;
    case "processing":
    case "ordered":
    case "arrived_at_proxy":
    case "shipped_to_indonesia":
    case "arrived_at_indonesia":
      return 3;
    case "ready_to_ship":
    case "shipped":
      return 4;
    case "completed":
      return 5;
    case "cancelled":
    case "expired":
      return -1;
    default:
      return 1;
  }
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
      class="absolute top-[450px] -right-40 w-96 h-96 bg-primary-100/20 rounded-full blur-3xl pointer-events-none"
    ></div>
    <AppCuteDecor />

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 space-y-6">
      <!-- Top Navigation & Back Link -->
      <div class="flex items-center justify-between">
        <UButton
          icon="i-heroicons-arrow-left"
          color="neutral"
          variant="ghost"
          class="rounded-2xl font-extrabold text-xs px-3.5 py-2 text-gray-500 hover:text-primary-600 hover:bg-white/80 shadow-2xs border border-gray-200/60 cursor-pointer transition-all"
          to="/orders/history"
        >
          Kembali ke Pesanan Saya
        </UButton>
      </div>

      <!-- Loading Skeleton -->
      <div
        v-if="loading"
        class="bg-white/80 backdrop-blur-md border border-primary-100/60 rounded-3xl p-12 text-center shadow-xs space-y-4"
      >
        <div
          class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-500 flex items-center justify-center mx-auto animate-spin"
        >
          <UIcon name="i-heroicons-arrow-path" class="w-6 h-6" />
        </div>
        <p class="text-xs text-gray-500 font-extrabold">
          Memuat rincian transaksi pesanan Anda...
        </p>
      </div>

      <!-- Not Found State -->
      <div
        v-else-if="!loading && !order"
        class="bg-white/80 backdrop-blur-md border border-gray-200/80 rounded-3xl p-12 text-center shadow-xs space-y-4 max-w-md mx-auto"
      >
        <div
          class="w-14 h-14 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto"
        >
          <UIcon
            name="i-heroicons-document-magnifying-glass"
            class="w-7 h-7"
          />
        </div>
        <div class="space-y-1">
          <h3 class="text-base font-black text-gray-900">
            Pesanan Tidak Ditemukan
          </h3>
          <p class="text-xs text-gray-400 font-semibold">
            Nomor pesanan atau alamat email tidak cocok dengan sistem kami.
          </p>
        </div>
        <UButton
          :to="guestEmail ? '/orders/cek' : '/orders/history'"
          color="primary"
          variant="solid"
          size="sm"
          class="rounded-full font-bold px-6 py-2 cursor-pointer"
        >
          {{ guestEmail ? "Coba Lagi" : "Kembali ke Riwayat" }}
        </UButton>
      </div>

      <!-- Main Order Detail Container -->
      <div v-else-if="order" class="space-y-6">
        <!-- 1. Hero Order Header Card -->
        <div
          class="bg-white/90 backdrop-blur-md border border-primary-100/80 rounded-3xl p-6 sm:p-8 shadow-sm relative overflow-hidden space-y-6"
        >
          <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-5 pb-6 border-b border-gray-100"
          >
            <div class="space-y-2">
              <div class="flex flex-wrap items-center gap-3">
                <h1
                  class="text-xl sm:text-2xl font-black text-gray-900 font-heading tracking-tight"
                >
                  {{ order.orderNumber }}
                </h1>
                <button
                  type="button"
                  class="text-gray-400 hover:text-primary-500 transition-colors cursor-pointer"
                  title="Salin Nomor Pesanan"
                  @click="copyToClipboard(order.orderNumber, 'Nomor Pesanan')"
                >
                  <UIcon
                    :name="
                      copiedField === 'Nomor Pesanan'
                        ? 'i-heroicons-check'
                        : 'i-heroicons-clipboard-document'
                    "
                    class="w-4 h-4"
                  />
                </button>
                <UBadge
                  :color="getStatusBadgeColor(order.orderStatus)"
                  size="md"
                  class="font-extrabold px-3 py-1 rounded-full uppercase tracking-wider"
                >
                  <span class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
                    {{ getStatusLabel(order.orderStatus) }}
                  </span>
                </UBadge>
              </div>
              <p class="text-xs text-gray-400 font-semibold flex items-center gap-1.5">
                <UIcon name="i-heroicons-calendar" class="w-3.5 h-3.5 text-gray-400" />
                Tanggal Transaksi: {{ formatDate(order.createdAt) }}
              </p>
            </div>

            <!-- Quick WhatsApp Support -->
            <div class="flex items-center gap-3 shrink-0">
              <a
                v-if="settings.whatsapp_number"
                :href="whatsappConfirmUrl()"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center gap-2 text-xs font-black text-emerald-600 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/80 px-4 py-2.5 rounded-full transition-all duration-200 shadow-2xs hover:scale-102 cursor-pointer"
              >
                <UIcon name="i-simple-icons-whatsapp" class="w-4 h-4 text-emerald-600" />
                Bantuan WhatsApp
              </a>
            </div>
          </div>

          <!-- 2. Visual Order Progress Step Tracker -->
          <div v-if="getTimelineStep(order.orderStatus) !== -1" class="pt-2">
            <p class="text-[11px] font-black uppercase tracking-wider text-gray-400 mb-4">
              Status Lacak Pesanan
            </p>

            <div class="grid grid-cols-5 gap-2 relative">
              <!-- Progress connector line -->
              <div
                class="absolute top-4 left-6 right-6 h-1 bg-gray-100 -z-0 rounded-full"
              ></div>

              <!-- Step 1: Dibuat -->
              <div class="flex flex-col items-center text-center relative z-10 space-y-2">
                <div
                  class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 border-2"
                  :class="
                    getTimelineStep(order.orderStatus) >= 1
                      ? 'bg-primary-500 border-primary-500 text-white shadow-md shadow-primary-500/30'
                      : 'bg-white border-gray-200 text-gray-400'
                  "
                >
                  <UIcon name="i-heroicons-shopping-bag" class="w-4 h-4" />
                </div>
                <span
                  class="text-[10px] sm:text-xs font-extrabold tracking-tight"
                  :class="getTimelineStep(order.orderStatus) >= 1 ? 'text-primary-600' : 'text-gray-400'"
                >
                  Dibuat
                </span>
              </div>

              <!-- Step 2: Terbayar -->
              <div class="flex flex-col items-center text-center relative z-10 space-y-2">
                <div
                  class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 border-2"
                  :class="
                    getTimelineStep(order.orderStatus) >= 2
                      ? 'bg-primary-500 border-primary-500 text-white shadow-md shadow-primary-500/30'
                      : 'bg-white border-gray-200 text-gray-400'
                  "
                >
                  <UIcon name="i-heroicons-credit-card" class="w-4 h-4" />
                </div>
                <span
                  class="text-[10px] sm:text-xs font-extrabold tracking-tight"
                  :class="getTimelineStep(order.orderStatus) >= 2 ? 'text-primary-600' : 'text-gray-400'"
                >
                  Terbayar
                </span>
              </div>

              <!-- Step 3: Diproses / Impor -->
              <div class="flex flex-col items-center text-center relative z-10 space-y-2">
                <div
                  class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 border-2"
                  :class="
                    getTimelineStep(order.orderStatus) >= 3
                      ? 'bg-primary-500 border-primary-500 text-white shadow-md shadow-primary-500/30'
                      : 'bg-white border-gray-200 text-gray-400'
                  "
                >
                  <UIcon name="i-heroicons-arrow-path" class="w-4 h-4" />
                </div>
                <span
                  class="text-[10px] sm:text-xs font-extrabold tracking-tight"
                  :class="getTimelineStep(order.orderStatus) >= 3 ? 'text-primary-600' : 'text-gray-400'"
                >
                  Diproses
                </span>
              </div>

              <!-- Step 4: Dikirim -->
              <div class="flex flex-col items-center text-center relative z-10 space-y-2">
                <div
                  class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 border-2"
                  :class="
                    getTimelineStep(order.orderStatus) >= 4
                      ? 'bg-primary-500 border-primary-500 text-white shadow-md shadow-primary-500/30'
                      : 'bg-white border-gray-200 text-gray-400'
                  "
                >
                  <UIcon name="i-heroicons-truck" class="w-4 h-4" />
                </div>
                <span
                  class="text-[10px] sm:text-xs font-extrabold tracking-tight"
                  :class="getTimelineStep(order.orderStatus) >= 4 ? 'text-primary-600' : 'text-gray-400'"
                >
                  Dikirim
                </span>
              </div>

              <!-- Step 5: Selesai -->
              <div class="flex flex-col items-center text-center relative z-10 space-y-2">
                <div
                  class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 border-2"
                  :class="
                    getTimelineStep(order.orderStatus) >= 5
                      ? 'bg-emerald-500 border-emerald-500 text-white shadow-md shadow-emerald-500/30'
                      : 'bg-white border-gray-200 text-gray-400'
                  "
                >
                  <UIcon name="i-heroicons-check-circle" class="w-4 h-4" />
                </div>
                <span
                  class="text-[10px] sm:text-xs font-extrabold tracking-tight"
                  :class="getTimelineStep(order.orderStatus) >= 5 ? 'text-emerald-600' : 'text-gray-400'"
                >
                  Selesai
                </span>
              </div>
            </div>
          </div>

          <!-- Cancelled / Expired Alert Banner -->
          <div
            v-else
            class="bg-red-50/80 border border-red-200 rounded-2xl p-4 flex items-center gap-3 text-red-700"
          >
            <UIcon name="i-heroicons-exclamation-triangle" class="w-5 h-5 shrink-0 text-red-500" />
            <p class="text-xs font-bold">
              Pesanan ini berstatus <strong>{{ getStatusLabel(order.orderStatus) }}</strong>. Apabila Anda merasa terdapat kesalahan, silakan hubungi tim CS kami via WhatsApp.
            </p>
          </div>
        </div>

        <!-- 3. Two-Column Layout Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          <!-- Left Column (Items List & Payment Steps) - 7 cols -->
          <div class="lg:col-span-7 space-y-6">
            <!-- Card 1: Rincian Item Belanja -->
            <div
              class="bg-white border border-gray-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6"
            >
              <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <h2
                  class="text-xs uppercase tracking-wider font-black text-gray-800 flex items-center gap-2"
                >
                  <UIcon name="i-heroicons-shopping-bag" class="w-4 h-4 text-primary-500" />
                  Rincian Item Belanja
                </h2>
                <span
                  class="text-[11px] font-extrabold text-primary-600 bg-primary-50 border border-primary-100 rounded-full px-3 py-1"
                >
                  {{ order.items.length }} Item
                </span>
              </div>

              <div class="divide-y divide-gray-100">
                <div
                  v-for="item in order.items"
                  :key="item.id"
                  class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4 group"
                >
                  <!-- Item Info -->
                  <div class="min-w-0 flex-1 space-y-1">
                    <p class="text-xs sm:text-sm font-extrabold text-gray-900 group-hover:text-primary-600 transition-colors">
                      {{ item.productName }}
                    </p>
                    <div class="flex flex-wrap items-center gap-2 pt-0.5">
                      <span
                        class="text-[10px] font-extrabold bg-gray-100 text-gray-700 rounded-md px-2 py-0.5"
                      >
                        Varian: {{ item.variantName }}
                      </span>
                      <span class="text-[11px] font-bold text-gray-400">
                        x{{ item.qty }} pcs
                      </span>
                    </div>
                    <p class="text-xs font-bold text-gray-500 pt-1">
                      {{ format(item.price) }} / pcs
                    </p>
                  </div>

                  <!-- Item Subtotal & Rating Trigger -->
                  <div class="text-right shrink-0 flex flex-col items-end gap-2">
                    <span class="text-xs sm:text-sm font-black text-gray-900 font-heading">
                      {{ format(item.subtotal) }}
                    </span>

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
                      <span
                        v-else
                        class="text-[10px] text-primary-600 font-extrabold flex items-center gap-1 bg-primary-50 px-2 py-0.5 rounded-full"
                      >
                        <UIcon
                          name="i-heroicons-check-circle"
                          class="w-3.5 h-3.5"
                        />
                        Sudah Dirating
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card 2: Instruksi Pembayaran & Upload Bukti (Only if Unpaid / Rejected) -->
            <div
              v-if="
                order.paymentStatus === 'unpaid' ||
                order.paymentStatus === 'rejected'
              "
              class="bg-white border border-gray-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6"
            >
              <div class="border-b border-gray-100 pb-4">
                <h2
                  class="text-xs uppercase tracking-wider font-black text-gray-800 flex items-center gap-2"
                >
                  <UIcon name="i-heroicons-credit-card" class="w-4 h-4 text-primary-500" />
                  {{
                    order.remainingBalance > 0 &&
                    order.orderStatus === "ready_to_ship"
                      ? "Instruksi Pelunasan"
                      : "Instruksi Pembayaran"
                  }}
                </h2>
              </div>

              <!-- Rejected Notice -->
              <div
                v-if="order.paymentStatus === 'rejected'"
                class="text-xs text-red-700 font-semibold bg-red-50 border border-red-200 rounded-2xl p-4 flex items-start gap-2.5"
              >
                <UIcon name="i-heroicons-x-circle" class="w-4 h-4 text-red-500 shrink-0 mt-0.5" />
                <span>
                  Bukti pembayaran sebelumnya ditolak{{
                    order.paymentRejectedReason
                      ? ": " + order.paymentRejectedReason
                      : ""
                  }}. Silakan unggah bukti transfer yang valid di bawah ini.
                </span>
              </div>

              <!-- Pre-Order remaining balance info -->
              <div
                v-if="
                  order.remainingBalance > 0 &&
                  order.orderStatus === 'ready_to_ship'
                "
                class="bg-primary-50/60 rounded-2xl p-4 border border-primary-200/80 text-xs text-primary-900 space-y-1.5"
              >
                <p class="font-black text-primary-700">Penting untuk Pelunasan Pre-Order:</p>
                <p class="font-semibold leading-relaxed">
                  Pesanan Pre-Order Anda telah tiba dan siap dikirim. Silakan transfer nominal pelunasan sebesar
                  <strong class="font-black text-sm text-primary-700 block mt-1">{{
                    format(order.remainingBalance)
                  }}</strong>
                  ke salah satu rekening di bawah ini dan unggah bukti transfernya.
                </p>
              </div>

              <!-- Langkah 1: Pilih Rekening Transfer -->
              <div class="space-y-4">
                <p class="text-xs font-black text-gray-700 uppercase tracking-wider flex items-center gap-2">
                  <span class="w-5 h-5 rounded-full bg-primary-500 text-white flex items-center justify-center text-[10px]">1</span>
                  Pilih Rekening Transfer:
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div
                    v-for="method in paymentMethods"
                    :key="method.id"
                    class="text-xs border border-gray-200 rounded-2xl p-4 bg-gray-50/50 hover:bg-white hover:border-primary-300 shadow-2xs hover:shadow-sm transition-all duration-200"
                  >
                    <!-- For QRIS method -->
                    <div
                      v-if="method.label.toLowerCase().includes('qris')"
                      class="flex items-center justify-between gap-4"
                    >
                      <div class="space-y-1.5">
                        <p class="font-black text-gray-900 text-xs uppercase tracking-wide">
                          {{ method.label }}
                        </p>
                        <p v-if="method.account_holder" class="text-gray-500 font-bold text-[10px]">
                          a/n {{ method.account_holder }}
                        </p>
                        <UButton
                          color="primary"
                          variant="solid"
                          size="xs"
                          class="font-black rounded-lg cursor-pointer mt-1 px-3 py-1 text-[10px]"
                          icon="i-heroicons-qr-code"
                          @click="openQrisModal"
                        >
                          Scan / Lihat QR Code
                        </UButton>
                      </div>

                      <div
                        class="shrink-0 cursor-pointer hover:scale-105 transition-transform"
                        @click="openQrisModal"
                      >
                        <img
                          src="/qris.jpeg"
                          alt="QRIS Thumbnail"
                          class="w-16 h-16 object-cover rounded-xl border border-gray-200 p-0.5 bg-white shadow-xs"
                        />
                      </div>
                    </div>

                    <!-- Bank Transfer (BCA, ShopeePay, Seabank) -->
                    <div v-else class="flex items-center justify-between gap-3">
                      <div class="space-y-1 min-w-0 flex-1">
                        <p class="font-black text-gray-900 text-xs uppercase tracking-wide">
                          {{ method.label }}
                        </p>
                        <div class="flex items-center gap-1.5">
                          <p class="text-gray-900 font-black text-sm tracking-wide select-all">
                            {{ method.account_number }}
                          </p>
                          <button
                            v-if="method.account_number"
                            type="button"
                            class="text-gray-400 hover:text-primary-500 transition-colors p-1 cursor-pointer"
                            title="Salin Nomor Rekening"
                            @click="copyToClipboard(method.account_number, method.label)"
                          >
                            <UIcon
                              :name="copiedField === method.label ? 'i-heroicons-check' : 'i-heroicons-clipboard-document'"
                              class="w-3.5 h-3.5"
                            />
                          </button>
                        </div>
                        <p v-if="method.account_holder" class="text-gray-400 font-bold text-[10px]">
                          a/n {{ method.account_holder }}
                        </p>
                      </div>

                      <div v-if="method.qr_image" class="shrink-0">
                        <img
                          :src="method.qr_image"
                          alt="Bank Logo"
                          class="h-9 max-w-[70px] object-contain"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Langkah 2: Upload Bukti Transfer -->
              <div class="border-t border-gray-100 pt-6 space-y-4">
                <p class="text-xs font-black text-gray-700 uppercase tracking-wider flex items-center gap-2">
                  <span class="w-5 h-5 rounded-full bg-primary-500 text-white flex items-center justify-center text-[10px]">2</span>
                  Unggah Bukti Transfer Pembayaran:
                </p>

                <input
                  ref="fileInput"
                  type="file"
                  accept="image/jpeg,image/png,image/webp"
                  class="hidden"
                  @change="onProofFileChange"
                />

                <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-4 items-center">
                  <!-- Drag and Drop Zone -->
                  <div
                    class="border-2 border-dashed border-primary-200 hover:border-primary-400 bg-primary-50/10 hover:bg-primary-50/20 rounded-2xl p-6 transition-all duration-200 text-center cursor-pointer flex flex-col items-center justify-center gap-3 min-h-[140px] group"
                    @click="triggerFileSelect"
                    @dragover.prevent
                    @drop.prevent="onFileDrop"
                  >
                    <!-- Empty file state -->
                    <div v-if="!proofFile" class="space-y-2 flex flex-col items-center">
                      <div
                        class="w-10 h-10 rounded-full bg-primary-100/70 text-primary-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-200 shadow-xs"
                      >
                        <UIcon name="i-heroicons-cloud-arrow-up" class="w-5 h-5" />
                      </div>
                      <div>
                        <p class="text-xs font-black text-gray-800">
                          Klik untuk Pilih File Bukti
                        </p>
                        <p class="text-[10px] text-gray-400 font-semibold mt-0.5">
                          atau seret & lepas gambar ke sini
                        </p>
                      </div>
                      <p class="text-[9px] text-gray-400 font-medium">
                        Format: JPG, PNG, WEBP (Maks. 2MB)
                      </p>
                    </div>

                    <!-- Selected file state -->
                    <div v-else class="space-y-3 w-full flex flex-col items-center">
                      <div class="relative w-20 h-20 rounded-xl overflow-hidden border border-gray-200 shadow-sm shrink-0 bg-white group/preview">
                        <img
                          :src="filePreviewUrl"
                          class="w-full h-full object-cover"
                          alt="Preview bukti"
                        />
                        <button
                          type="button"
                          class="absolute top-1 right-1 w-5 h-5 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center shadow-md cursor-pointer transition-colors"
                          aria-label="Batalkan pilihan file"
                          @click.stop="clearSelectedFile"
                        >
                          <UIcon name="i-heroicons-x-mark" class="w-3 h-3" />
                        </button>
                      </div>
                      <div class="min-w-0 text-center">
                        <p class="text-xs font-extrabold text-gray-800 truncate max-w-[280px]">
                          {{ proofFile.name }}
                        </p>
                        <p class="text-[10px] text-gray-400 font-semibold mt-0.5">
                          {{ formatFileSize(proofFile.size) }}
                        </p>
                      </div>
                    </div>
                  </div>

                  <!-- Upload Button -->
                  <div class="flex flex-col gap-3">
                    <UButton
                      block
                      color="primary"
                      size="md"
                      class="font-extrabold rounded-full py-3.5 cursor-pointer shadow-md shadow-primary-500/20 hover:scale-[1.02] transition-transform duration-200"
                      :disabled="!proofFile"
                      :loading="uploadingProof"
                      @click="submitProof"
                    >
                      Unggah Bukti
                    </UButton>
                    <p v-if="proofError" class="text-[10px] text-red-500 font-bold text-center">
                      {{ proofError }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Awaiting Verification status -->
            <div
              v-else-if="order.paymentStatus === 'awaiting_verification'"
              class="bg-white border border-primary-100 rounded-3xl p-8 text-center space-y-4 shadow-sm relative overflow-hidden"
            >
              <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-500 border border-amber-200/80 flex items-center justify-center mx-auto shadow-xs animate-pulse">
                <UIcon name="i-heroicons-clock" class="w-8 h-8" />
              </div>

              <div class="space-y-1.5">
                <h3 class="text-base font-black text-gray-900 tracking-tight">
                  {{
                    order.remainingBalance > 0 &&
                    order.orderStatus === "ready_to_ship"
                      ? "Menunggu Verifikasi Pelunasan"
                      : "Menunggu Verifikasi Admin"
                  }}
                </h3>
                <p class="text-xs text-gray-500 font-semibold max-w-md mx-auto leading-relaxed">
                  Bukti transfer Anda telah diterima dan sedang dalam proses verifikasi oleh tim admin kami.
                </p>
              </div>

              <!-- Quick Confirmation Social Links -->
              <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                <a
                  v-if="settings.whatsapp_number"
                  :href="whatsappConfirmUrl()"
                  target="_blank"
                  rel="noopener"
                  class="w-full sm:w-auto inline-flex items-center justify-center gap-2 text-xs font-black text-emerald-600 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl px-5 py-3 transition-colors shadow-2xs cursor-pointer"
                >
                  <UIcon name="i-simple-icons-whatsapp" class="w-4 h-4 text-emerald-600" />
                  Konfirmasi via WhatsApp
                </a>
                <a
                  v-if="settings.line_url"
                  :href="settings.line_url"
                  target="_blank"
                  rel="noopener"
                  class="w-full sm:w-auto inline-flex items-center justify-center gap-2 text-xs font-black text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl px-5 py-3 transition-colors shadow-2xs cursor-pointer"
                >
                  <UIcon name="i-simple-icons-line" class="w-4 h-4" />
                  Konfirmasi via LINE
                </a>
              </div>
            </div>

            <!-- Payment Verified Card -->
            <div
              v-else-if="order.paymentStatus === 'paid'"
              class="bg-linear-to-r from-emerald-50 to-emerald-50/30 border border-emerald-200/80 rounded-3xl p-6 flex items-center gap-4 shadow-2xs"
            >
              <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-md shadow-emerald-500/20 shrink-0">
                <UIcon name="i-heroicons-check-circle" class="w-6 h-6" />
              </div>
              <div class="space-y-0.5">
                <h3 class="text-sm font-black text-emerald-900">
                  Pembayaran Lunas & Terverifikasi
                </h3>
                <p class="text-xs font-semibold text-emerald-700 leading-relaxed">
                  Terima kasih! Pembayaran Anda telah terverifikasi resmi oleh sistem BbystarGO.
                </p>
              </div>
            </div>
          </div>

          <!-- Right Column (Payment Summary & Shipping Info) - 5 cols -->
          <div class="lg:col-span-5 space-y-6">
            <!-- Card 3: Rincian Pembayaran -->
            <div
              class="bg-white border border-gray-100 rounded-3xl p-6 sm:p-7 shadow-sm space-y-4"
            >
              <h2
                class="text-xs uppercase tracking-wider font-black text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2"
              >
                <UIcon name="i-heroicons-receipt-percent" class="w-4 h-4 text-primary-500" />
                Rincian Pembayaran
              </h2>

              <div class="space-y-3 text-xs font-semibold">
                <div class="flex items-center justify-between">
                  <span class="text-gray-500 font-medium">
                    {{
                      order.remainingBalance > 0
                        ? "Subtotal (DP)"
                        : "Subtotal Produk"
                    }}
                  </span>
                  <span class="text-gray-900 font-extrabold">{{
                    format(order.subtotal)
                  }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-gray-500 font-medium">Ongkos Kirim</span>
                  <span class="text-gray-900 font-extrabold">{{
                    format(order.shippingCost)
                  }}</span>
                </div>

                <!-- Display DP total -->
                <div
                  v-if="order.remainingBalance > 0"
                  class="flex items-center justify-between border-t border-dashed border-gray-200 pt-2.5"
                >
                  <span class="text-gray-500 font-medium">Total DP + Ongkir</span>
                  <span class="text-gray-900 font-black">{{
                    format(order.total)
                  }}</span>
                </div>

                <!-- Display Remaining Balance details if PO -->
                <div
                  v-if="order.remainingBalance > 0"
                  class="space-y-2 border-t border-dashed border-gray-200 pt-2.5"
                >
                  <div class="flex items-center justify-between">
                    <span class="text-primary-600 font-extrabold">Pelunasan (Sisa Bayar)</span>
                    <span class="text-primary-700 font-black text-sm">{{
                      format(order.remainingBalance)
                    }}</span>
                  </div>
                  <div class="flex items-center justify-between text-[10px]">
                    <span class="text-gray-400 font-semibold">Status Pelunasan</span>
                    <span
                      class="px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider"
                      :class="{
                        'bg-gray-100 text-gray-500':
                          order.orderStatus !== 'ready_to_ship' &&
                          order.paymentStatus === 'paid',
                        'bg-red-50 text-red-600 border border-red-100':
                          order.orderStatus === 'ready_to_ship' &&
                          order.paymentStatus === 'rejected',
                        'bg-amber-50 text-amber-600 border border-amber-100':
                          order.paymentStatus === 'awaiting_verification',
                        'bg-emerald-50 text-emerald-700 border border-emerald-200':
                          order.paymentStatus === 'paid' &&
                          (order.orderStatus === 'ready_to_ship' ||
                            order.orderStatus === 'shipped' ||
                            order.orderStatus === 'completed'),
                        'bg-red-50 text-red-500 border border-red-100 animate-pulse':
                          order.orderStatus === 'ready_to_ship' &&
                          order.paymentStatus === 'unpaid',
                      }"
                    >
                      {{
                        order.paymentStatus === "paid" &&
                        (order.orderStatus === "ready_to_ship" ||
                          order.orderStatus === "shipped" ||
                          order.orderStatus === "completed")
                          ? "Lunas"
                          : order.paymentStatus === "awaiting_verification"
                            ? "Verifikasi"
                            : order.paymentStatus === "rejected"
                              ? "Ditolak"
                              : order.orderStatus === "ready_to_ship"
                                ? "Belum Dibayar"
                                : "Menunggu Barang Siap"
                      }}
                    </span>
                  </div>
                </div>

                <div
                  class="flex items-center justify-between border-t border-gray-100 pt-3.5"
                >
                  <span class="text-gray-900 font-black text-sm">
                    {{
                      order.remainingBalance > 0
                        ? "Total Keseluruhan"
                        : "Total Bayar"
                    }}
                  </span>
                  <span class="text-base font-black text-primary-600 font-heading">{{
                    format(order.total + order.remainingBalance)
                  }}</span>
                </div>
              </div>
            </div>

            <!-- Card 4: Informasi Pengiriman & Resi -->
            <div
              class="bg-white border border-gray-100 rounded-3xl p-6 sm:p-7 shadow-sm space-y-4"
            >
              <h2
                class="text-xs uppercase tracking-wider font-black text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2"
              >
                <UIcon name="i-heroicons-truck" class="w-4 h-4 text-primary-500" />
                Informasi Pengiriman
              </h2>

              <div class="space-y-4 text-xs">
                <!-- Receiver info -->
                <div class="space-y-1">
                  <span class="block text-gray-400 font-bold text-[11px] uppercase tracking-wider">
                    Penerima Paket
                  </span>
                  <span class="font-extrabold text-gray-900 block text-sm">
                    {{ order.shippingAddress?.recipientName }}
                  </span>
                  <span class="text-gray-500 font-semibold block">
                    {{ order.shippingAddress?.phone }}
                  </span>
                </div>

                <!-- Full Address -->
                <div class="space-y-1 border-t border-gray-100 pt-3">
                  <span class="block text-gray-400 font-bold text-[11px] uppercase tracking-wider">
                    Alamat Tujuan
                  </span>
                  <p class="text-gray-650 font-semibold leading-relaxed">
                    {{ order.shippingAddress?.fullAddress }}<br />
                    {{ order.shippingAddress?.district }},
                    {{ order.shippingAddress?.city }},
                    {{ order.shippingAddress?.province }} -
                    {{ order.shippingAddress?.postalCode }}
                  </p>
                </div>

                <!-- Courier -->
                <div class="space-y-1 border-t border-gray-100 pt-3">
                  <span class="block text-gray-400 font-bold text-[11px] uppercase tracking-wider">
                    Ekspedisi Kurir
                  </span>
                  <span class="font-extrabold text-gray-900 uppercase block text-xs">
                    {{ order.courier }} - {{ order.courierService }}
                  </span>
                </div>

                <!-- Tracking Resi (If available) -->
                <div v-if="order.trackingNumber" class="border-t border-gray-100 pt-3 space-y-1.5">
                  <span class="block text-gray-400 font-bold text-[11px] uppercase tracking-wider">
                    Nomor Resi Lacak
                  </span>
                  <div class="flex items-center justify-between bg-primary-50 border border-primary-200/80 rounded-xl p-3">
                    <span class="font-black text-primary-700 text-sm select-all tracking-wide">
                      {{ order.trackingNumber }}
                    </span>
                    <button
                      type="button"
                      class="text-xs font-black text-primary-600 hover:text-primary-700 flex items-center gap-1 cursor-pointer bg-white border border-primary-200 px-2.5 py-1 rounded-lg shadow-2xs hover:scale-102 transition-transform"
                      @click="copyToClipboard(order.trackingNumber, 'Nomor Resi')"
                    >
                      <UIcon
                        :name="copiedField === 'Nomor Resi' ? 'i-heroicons-check' : 'i-heroicons-clipboard-document'"
                        class="w-3.5 h-3.5"
                      />
                      Salin Resi
                    </button>
                  </div>
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
            Bagaimana kualitas produk<br />
            <strong class="text-gray-900"
              >{{ itemToRate?.productName }} ({{
                itemToRate?.variantName
              }})</strong
            >?
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
          <p
            class="text-[10px] text-amber-500 font-bold uppercase tracking-wider"
          >
            {{ ratingStars }} Bintang
          </p>
        </div>
      </template>

      <template #footer>
        <div class="flex items-center justify-between gap-3 w-full">
          <UButton
            color="neutral"
            variant="outline"
            class="flex-1 justify-center rounded-xl font-bold cursor-pointer"
            @click="
              () => {
                ratingModalOpen = false;
              }
            "
          >
            Batal
          </UButton>
          <UButton
            color="primary"
            class="flex-1 justify-center rounded-xl font-bold cursor-pointer"
            :loading="loadingRating"
            @click="submitRating"
          >
            Kirim Ulasan
          </UButton>
        </div>
      </template>
    </UModal>

    <!-- QRIS Modal Dialog -->
    <UModal v-model:open="qrisModalOpen" title="Scan QRIS Untuk Bayar">
      <template #body>
        <div class="text-center py-2 space-y-4">
          <p class="text-xs text-gray-500 font-semibold leading-relaxed">
            Silakan scan kode QRIS di bawah ini menggunakan aplikasi e-wallet
            atau mobile banking Anda.
          </p>
          <div class="flex justify-center">
            <img
              src="/qris.jpeg"
              class="w-64 h-auto object-contain rounded-2xl border border-gray-150 p-2.5 bg-white shadow-md"
              alt="QRIS BBYSTAR"
            />
          </div>
          <div
            class="bg-primary-50/50 rounded-xl p-3 border border-primary-100/40 w-fit mx-auto text-left space-y-0.5"
          >
            <p
              class="text-[10px] text-gray-400 font-bold uppercase leading-none"
            >
              Nama Merchant
            </p>
            <p class="text-xs font-black text-gray-800">BBYSTAR, NGLIPAR</p>
            <p class="text-[9px] text-gray-400 font-semibold mt-1">
              NMID: ID1025372942546
            </p>
          </div>
        </div>
      </template>
      <template #footer>
        <div class="flex items-center gap-3 w-full">
          <UButton
            color="neutral"
            variant="outline"
            class="flex-1 justify-center rounded-xl font-bold cursor-pointer"
            @click="
              () => {
                qrisModalOpen = false;
              }
            "
          >
            Tutup
          </UButton>
          <UButton
            color="primary"
            variant="solid"
            class="flex-1 justify-center rounded-xl font-bold cursor-pointer"
            icon="i-heroicons-arrow-down-tray"
            href="/qris.jpeg"
            download="qris-bbystar.jpeg"
            external
          >
            Unduh QR
          </UButton>
        </div>
      </template>
    </UModal>
  </div>
</template>
