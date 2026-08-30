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

const qrisModalOpen = ref(false);

function openQrisModal() {
  qrisModalOpen.value = true;
}

const fileInput = ref<HTMLInputElement | null>(null);
const filePreviewUrl = ref<string>();

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
</script>

<template>
  <div
    class="relative overflow-hidden bg-linear-to-b from-primary-50/45 via-primary-50/15 to-white min-h-screen py-10"
  >
    <!-- Ambient Background Glows -->
    <div
      class="absolute top-12 -left-40 w-96 h-96 bg-primary-200/20 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-[400px] -right-40 w-96 h-96 bg-primary-100/20 rounded-full blur-3xl pointer-events-none"
    ></div>
    <AppCuteDecor />

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
      <!-- Back button -->
      <div class="mb-6">
        <UButton
          icon="i-heroicons-arrow-left"
          color="neutral"
          variant="ghost"
          class="rounded-xl font-bold px-3 py-2 text-gray-400 cursor-pointer hover:bg-transparent hover:text-primary-500"
          :to="guestEmail ? '/orders/cek' : '/orders/history'"
        >
          {{
            guestEmail ? "Kembali ke Cek Pesanan" : "Kembali ke Riwayat Pesanan"
          }}
        </UButton>
      </div>

      <!-- Loading skeleton -->
      <div
        v-if="loading"
        class="flex flex-col items-center justify-center py-20 gap-3"
      >
        <UIcon
          name="i-heroicons-arrow-path"
          class="w-8 h-8 animate-spin text-primary-600"
        />
        <span class="text-xs text-gray-400 font-bold"
          >Memuat detail transaksi...</span
        >
      </div>

      <!-- Not found -->
      <div
        v-else-if="!loading && !order"
        class="flex flex-col items-center justify-center py-24 gap-4"
      >
        <div
          class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center shadow-sm"
        >
          <UIcon
            name="i-heroicons-document-magnifying-glass"
            class="w-7 h-7 text-gray-400"
          />
        </div>
        <div class="text-center">
          <p class="text-sm font-extrabold text-gray-900">
            Pesanan Tidak Ditemukan
          </p>
          <p class="text-xs text-gray-400 font-medium mt-1">
            Nomor pesanan atau email tidak cocok.
          </p>
        </div>
        <UButton
          :to="guestEmail ? '/orders/cek' : '/orders/history'"
          color="primary"
          variant="outline"
          size="sm"
          class="rounded-full font-bold mt-2 cursor-pointer"
        >
          {{ guestEmail ? "Coba Lagi" : "Riwayat Pesanan" }}
        </UButton>
      </div>

      <!-- Main Content -->
      <div v-else-if="order" class="space-y-8">
        <!-- Header Info Card -->
        <div
          class="bg-linear-to-b from-primary-50/50 via-white/80 to-white/95 backdrop-blur-xs border-2 border-primary-300/40 rounded-3xl p-6 md:p-8 shadow-[0_4px_25px_-6px_rgba(255,116,108,0.03)] flex flex-col md:flex-row md:items-center justify-between gap-6"
        >
          <div class="space-y-2">
            <div class="flex items-center gap-3">
              <h1 class="text-lg font-black text-gray-900 tracking-tight">
                {{ order.orderNumber }}
              </h1>
              <UBadge
                :color="getStatusBadgeColor(order.orderStatus)"
                size="sm"
                class="font-extrabold"
              >
                {{ getStatusLabel(order.orderStatus) }}
              </UBadge>
            </div>
            <p class="text-xs text-gray-400 font-medium">
              Tanggal Transaksi: {{ formatDate(order.createdAt) }}
            </p>
          </div>
        </div>

        <!-- Detail Grid -->
        <div
          class="grid grid-cols-1 md:grid-cols-[1fr_320px] gap-8 items-start"
        >
          <!-- Left side: Items Table & Payment Instructions -->
          <div class="space-y-8">
            <!-- Rincian Item Belanja -->
            <div
              class="bg-linear-to-b from-primary-50/50 via-white/80 to-white/95 backdrop-blur-xsh border-2 border-primary-300/40 rounded-3xl p-6 md:p-8 shadow-[0_4px_20px_-4px_rgba(255,116,108,0.02)] space-y-6"
            >
              <h2
                class="text-xs uppercase tracking-wider font-extrabold text-primary-500 border-b border-primary-100/30 pb-3"
              >
                Rincian Item Belanja
              </h2>

              <div class="divide-y divide-gray-100">
                <div
                  v-for="item in order.items"
                  :key="item.id"
                  class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4"
                >
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-extrabold text-gray-900 truncate">
                      {{ item.productName }}
                    </p>
                    <p class="text-[10px] text-gray-400 font-semibold mt-0.5">
                      Varian: {{ item.variantName }} (x{{ item.qty }})
                    </p>
                    <p class="text-xs font-black text-gray-700 mt-2">
                      {{ format(item.price) }} / pcs
                    </p>
                  </div>

                  <div
                    class="text-right shrink-0 flex flex-col items-end gap-2.5"
                  >
                    <span class="text-xs font-black text-gray-800">{{
                      format(item.subtotal)
                    }}</span>

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
                        class="text-[10px] text-primary-600 font-extrabold flex items-center gap-1"
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

            <!-- Payment Instructions & Proof Upload -->
            <div
              v-if="
                order.paymentStatus === 'unpaid' ||
                order.paymentStatus === 'rejected'
              "
              class="bg-linear-to-b from-primary-50/50 via-white/80 to-white/95 backdrop-blur-xsh border-2 border-primary-300/40 rounded-3xl p-6 md:p-8 shadow-[0_4px_20px_-4px_rgba(255,116,108,0.02)] space-y-6"
            >
              <h2
                class="text-xs uppercase tracking-wider font-extrabold text-primary-500 border-b border-primary-100 pb-3"
              >
                {{
                  order.remainingBalance > 0 &&
                  order.orderStatus === "ready_to_ship"
                    ? "Instruksi Pelunasan"
                    : "Instruksi Pembayaran"
                }}
              </h2>

              <p
                v-if="order.paymentStatus === 'rejected'"
                class="text-xs text-red-600 font-semibold bg-red-50 rounded-2xl p-4"
              >
                Bukti pembayaran sebelumnya ditolak{{
                  order.paymentRejectedReason
                    ? ": " + order.paymentRejectedReason
                    : ""
                }}. Silakan unggah ulang.
              </p>

              <!-- Pre-Order remaining balance info -->
              <div
                v-if="
                  order.remainingBalance > 0 &&
                  order.orderStatus === 'ready_to_ship'
                "
                class="bg-primary-50/50 rounded-2xl p-4 border border-primary-100 text-xs text-primary-800 space-y-1"
              >
                <p class="font-black">Penting untuk Pelunasan:</p>
                <p class="font-medium leading-relaxed">
                  Pesanan Pre-Order Anda telah tiba dan siap dikirim. Silakan
                  transfer nominal pelunasan sebesar
                  <strong class="font-black text-sm text-primary-700">{{
                    format(order.remainingBalance)
                  }}</strong>
                  ke salah satu rekening di bawah ini dan unggah bukti
                  transfernya.
                </p>
              </div>

              <!-- Section 1: Choose Payment Method -->
              <div class="space-y-4">
                <p class="text-xs text-gray-400 font-bold">
                  Pilih metode transfer di bawah ini:
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div
                    v-for="method in paymentMethods"
                    :key="method.id"
                    class="text-xs border border-primary-100 rounded-2xl p-4.5 bg-white/50 shadow-2xs hover:shadow-xs transition-shadow duration-200"
                  >
                    <!-- For QRIS method -->
                    <div
                      v-if="method.label.toLowerCase().includes('qris')"
                      class="flex items-center justify-between gap-4"
                    >
                      <div class="space-y-1">
                        <p
                          class="font-extrabold text-gray-955 text-xs tracking-wide uppercase"
                        >
                          {{ method.label }}
                        </p>
                        <p
                          v-if="method.account_holder"
                          class="text-gray-400 font-bold text-[10px]"
                        >
                          a/n {{ method.account_holder }}
                        </p>
                        <UButton
                          color="primary"
                          variant="subtle"
                          size="xs"
                          class="font-black rounded-lg cursor-pointer mt-1.5 px-2.5 py-1 text-[10px]"
                          icon="i-heroicons-qr-code"
                          @click="openQrisModal"
                        >
                          Lihat QR Code
                        </UButton>
                      </div>

                      <!-- Small QR Code Thumbnail on the right -->
                      <div
                        class="shrink-0 cursor-pointer hover:opacity-85 transition-opacity"
                        @click="openQrisModal"
                      >
                        <img
                          src="/qris.jpeg"
                          alt="QRIS Thumbnail"
                          class="w-16 h-16 object-cover rounded-xl border border-gray-100 p-0.5 bg-white shadow-xs"
                        />
                      </div>
                    </div>

                    <!-- For Bank Transfer methods (BCA, ShopeePay, Seabank, etc.) -->
                    <div v-else class="flex items-center justify-between gap-4">
                      <div class="space-y-1">
                        <p
                          class="font-extrabold text-gray-955 text-xs tracking-wide uppercase"
                        >
                          {{ method.label }}
                        </p>
                        <p
                          v-if="method.account_number"
                          class="text-gray-800 font-black text-sm select-all tracking-wide mt-1"
                        >
                          {{ method.account_number }}
                        </p>
                        <p
                          v-if="method.account_holder"
                          class="text-gray-400 font-bold text-[10px]"
                        >
                          a/n {{ method.account_holder }}
                        </p>
                      </div>

                      <!-- Bank Logo on the right -->
                      <div v-if="method.qr_image" class="shrink-0">
                        <img
                          :src="method.qr_image"
                          alt="Bank Logo"
                          class="h-10 max-w-[80px] object-contain"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 2: Upload Proof of Payment (Directly Below) -->
              <div class="border-t border-gray-100/70 pt-6 space-y-4">
                <p class="text-xs text-gray-400 font-bold">
                  Unggah bukti transfer pembayaran:
                </p>

                <input
                  ref="fileInput"
                  type="file"
                  accept="image/jpeg,image/png,image/webp"
                  class="hidden"
                  @change="onProofFileChange"
                />

                <div
                  class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-4 items-center"
                >
                  <div
                    class="border-2 border-dashed border-primary-250 hover:border-primary-400 bg-primary-50/5 hover:bg-primary-50/10 rounded-2xl p-6 transition-all duration-200 text-center cursor-pointer flex flex-col items-center justify-center gap-3 min-h-[140px] group"
                    @click="triggerFileSelect"
                    @dragover.prevent
                    @drop.prevent="onFileDrop"
                  >
                    <!-- When no file is selected -->
                    <div
                      v-if="!proofFile"
                      class="space-y-2 flex flex-col items-center"
                    >
                      <div
                        class="w-10 h-10 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center group-hover:scale-105 transition-transform duration-200 ring-4 ring-primary-50/50"
                      >
                        <UIcon
                          name="i-heroicons-cloud-arrow-up"
                          class="w-5 h-5"
                        />
                      </div>
                      <div>
                        <p class="text-xs font-black text-gray-800">
                          Klik untuk Pilih File Bukti
                        </p>
                        <p
                          class="text-[10px] text-gray-400 font-semibold mt-0.5"
                        >
                          atau seret & lepas gambar ke sini
                        </p>
                      </div>
                      <p class="text-[9px] text-gray-400 font-medium">
                        Format: JPG, PNG, WEBP (Maks. 2MB)
                      </p>
                    </div>

                    <!-- When file is selected -->
                    <div
                      v-else
                      class="space-y-3 w-full flex flex-col items-center"
                    >
                      <!-- Image preview container -->
                      <div
                        class="relative w-20 h-20 rounded-xl overflow-hidden border border-gray-150 shadow-sm shrink-0 bg-white group/preview"
                      >
                        <img
                          :src="filePreviewUrl"
                          class="w-full h-full object-cover"
                          alt="Preview bukti"
                        />
                        <button
                          type="button"
                          class="absolute top-1 right-1 w-5 h-5 rounded-full bg-red-500 hover:bg-red-650 text-white flex items-center justify-center shadow-md cursor-pointer transition-colors"
                          @click.stop="clearSelectedFile"
                          aria-label="Batalkan pilihan file"
                        >
                          <UIcon name="i-heroicons-x-mark" class="w-3 h-3" />
                        </button>
                      </div>
                      <div class="min-w-0 text-center">
                        <p
                          class="text-xs font-bold text-gray-850 truncate max-w-[300px]"
                        >
                          {{ proofFile.name }}
                        </p>
                        <p
                          class="text-[10px] text-gray-400 font-semibold mt-0.5"
                        >
                          {{ formatFileSize(proofFile.size) }}
                        </p>
                      </div>
                    </div>
                  </div>

                  <div class="flex flex-col gap-3">
                    <UButton
                      block
                      color="primary"
                      size="md"
                      class="font-extrabold rounded-full py-3.5 cursor-pointer shadow-md shadow-primary-600/10 hover:scale-[1.01] transition-transform duration-200"
                      :disabled="!proofFile"
                      :loading="uploadingProof"
                      @click="submitProof"
                    >
                      Unggah Bukti
                    </UButton>
                    <p
                      v-if="proofError"
                      class="text-[10px] text-red-500 font-bold text-center"
                    >
                      {{ proofError }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Awaiting Verification status -->
            <div
              v-else-if="order.paymentStatus === 'awaiting_verification'"
              class="relative overflow-hidden bg-linear-to-br from-primary-50/50 via-white/80 to-primary-50/30 border-2 border-primary-300/40 rounded-3xl p-8 md:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.02)] text-center space-y-4"
            >
              <!-- Ambient soft light behind icon -->
              <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-48 h-48 bg-primary-100/10 rounded-full blur-3xl pointer-events-none"
              ></div>

              <div class="relative z-10 flex flex-col items-center">
                <!-- Cute Icon Container with Pulse effect -->
                <div
                  class="w-16 h-16 rounded-2xl bg-primary-100/70 text-primary-600 flex items-center justify-center mb-3 shadow-xs border border-primary-200/40 animate-pulse"
                >
                  <UIcon name="i-heroicons-clock" class="w-8 h-8" />
                </div>

                <h3 class="text-sm font-black text-primary-900 tracking-tight">
                  {{
                    order.remainingBalance > 0 &&
                    order.orderStatus === "ready_to_ship"
                      ? "Menunggu Verifikasi Pelunasan"
                      : "Menunggu Verifikasi Admin"
                  }}
                </h3>

                <!-- Quick support text or info -->
                <div
                  class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-100/45 border border-primary-200/30 rounded-full text-[10px] text-primary-700 font-bold"
                >
                  <span class="flex h-1.5 w-1.5 relative">
                    <span
                      class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"
                    ></span>
                    <span
                      class="relative inline-flex rounded-full h-1.5 w-1.5 bg-primary-500"
                    ></span>
                  </span>
                  Sedang Diproses
                </div>

                <p
                  class="text-xs text-gray-500 max-w-md mx-auto font-semibold mt-2 leading-relaxed"
                >
                  {{
                    order.remainingBalance > 0 &&
                    order.orderStatus === "ready_to_ship"
                      ? "Bukti transfer pelunasan Anda telah kami terima dan sedang diverifikasi oleh admin. Barang Anda akan segera dikirim setelah pelunasan diverifikasi."
                      : "Bukti pembayaran Anda telah kami terima dan sedang dalam proses verifikasi oleh tim admin kami. Proses ini biasanya memakan waktu hingga beberapa jam."
                  }}
                  Untuk proses verifikasi yang lebih cepat, Anda juga dapat
                  melakukan konfirmasi pembayaran via WhatsApp atau LINE di
                  bawah ini.
                </p>

                <!-- WhatsApp/LINE Buttons inside Card -->
                <div
                  class="flex flex-col sm:flex-row gap-3 mt-2 w-full max-w-md justify-center relative z-20"
                >
                  <a
                    v-if="settings.whatsapp_number"
                    :href="whatsappConfirmUrl()"
                    target="_blank"
                    rel="noopener"
                    class="flex-1 flex items-center justify-center gap-2 text-xs font-bold rounded-xl px-4 py-3 border border-primary-200 text-primary-700 bg-primary-50/40 hover:bg-primary-50 transition-colors shadow-xs cursor-pointer"
                  >
                    <UIcon name="i-simple-icons-whatsapp" class="w-4 h-4" />
                    Konfirmasi via WhatsApp
                  </a>
                  <a
                    v-if="settings.line_url"
                    :href="settings.line_url"
                    target="_blank"
                    rel="noopener"
                    class="flex-1 flex items-center justify-center gap-2 text-xs font-bold rounded-xl px-4 py-3 border border-gray-200 text-gray-750 bg-gray-50 hover:bg-gray-100 transition-colors shadow-xs cursor-pointer"
                  >
                    <UIcon name="i-simple-icons-line" class="w-4 h-4" />
                    Konfirmasi via LINE
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Right side: Shipping & Payment Cost -->
          <div class="space-y-6">
            <!-- Receipt Card -->
            <div
              class="bg-linear-to-b from-primary-50/50 via-white/80 to-white/95 backdrop-blur-xs border-2 border-primary-300/40 rounded-3xl p-6 shadow-[0_4px_25px_-6px_rgba(255,116,108,0.03)] space-y-4"
            >
              <h2
                class="text-xs uppercase tracking-wider font-extrabold text-primary-500 border-b border-primary-100 pb-2"
              >
                Rincian Pembayaran
              </h2>

              <div class="space-y-3.5 text-xs font-semibold">
                <div class="flex items-center justify-between">
                  <span class="text-gray-500 font-medium">
                    {{
                      order.remainingBalance > 0
                        ? "Subtotal (DP)"
                        : "Subtotal Produk"
                    }}
                  </span>
                  <span class="text-gray-800 font-extrabold">{{
                    format(order.subtotal)
                  }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-gray-500 font-medium">Ongkos Kirim</span>
                  <span class="text-gray-800 font-extrabold">{{
                    format(order.shippingCost)
                  }}</span>
                </div>

                <!-- Display DP total -->
                <div
                  class="flex items-center justify-between border-t border-dashed border-gray-150 pt-2"
                  v-if="order.remainingBalance > 0"
                >
                  <span class="text-gray-500 font-medium"
                    >Total DP + Ongkir</span
                  >
                  <span class="text-gray-800 font-black">{{
                    format(order.total)
                  }}</span>
                </div>

                <!-- Display Remaining Balance details if PO -->
                <div
                  v-if="order.remainingBalance > 0"
                  class="space-y-2 border-t border-dashed border-gray-150 pt-2.5"
                >
                  <div class="flex items-center justify-between">
                    <span class="text-primary-600 font-extrabold"
                      >Pelunasan (Sisa Bayar)</span
                    >
                    <span class="text-primary-700 font-black text-sm">{{
                      format(order.remainingBalance)
                    }}</span>
                  </div>
                  <div class="flex items-center justify-between text-[10px]">
                    <span class="text-gray-400 font-semibold"
                      >Status Pelunasan</span
                    >
                    <span
                      class="px-2 py-0.5 rounded-full font-bold uppercase tracking-wider"
                      :class="{
                        'bg-gray-100 text-gray-505':
                          order.orderStatus !== 'ready_to_ship' &&
                          order.paymentStatus === 'paid',
                        'bg-red-50 text-red-600 border border-red-100':
                          order.orderStatus === 'ready_to_ship' &&
                          order.paymentStatus === 'rejected',
                        'bg-amber-50 text-amber-600 border border-amber-100':
                          order.paymentStatus === 'awaiting_verification',
                        'bg-primary-50 text-primary-600 border border-primary-100':
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
                  class="flex items-center justify-between border-t border-primary-100 pt-3.5"
                >
                  <span class="text-gray-900 font-black text-sm">
                    {{
                      order.remainingBalance > 0
                        ? "Total Keseluruhan"
                        : "Total Bayar"
                    }}
                  </span>
                  <span class="text-base font-black text-primary-700">{{
                    format(order.total + order.remainingBalance)
                  }}</span>
                </div>
              </div>
            </div>

            <!-- Shipping Details -->
            <div
              class="bg-linear-to-b from-primary-50/50 via-white/80 to-white/95 backdrop-blur-xsh border-2 border-primary-300/40 rounded-3xl p-6 shadow-[0_4px_20px_-4px_rgba(255,116,108,0.02)] space-y-4"
            >
              <h2
                class="text-xs uppercase tracking-wider font-extrabold text-primary-500 border-b border-primary-100 pb-2"
              >
                Informasi Pengiriman
              </h2>

              <div class="space-y-3.5 text-xs">
                <div>
                  <span class="block text-gray-400 font-semibold"
                    >Penerima</span
                  >
                  <span class="font-bold text-gray-800 block mt-0.5">{{
                    order.shippingAddress?.recipientName
                  }}</span>
                  <span class="text-gray-500 mt-0.5 block font-medium">{{
                    order.shippingAddress?.phone
                  }}</span>
                </div>

                <div>
                  <span class="block text-gray-400 font-semibold"
                    >Alamat Lengkap</span
                  >
                  <span
                    class="text-gray-650 font-medium block mt-0.5 leading-relaxed"
                  >
                    {{ order.shippingAddress?.fullAddress }}<br />
                    {{ order.shippingAddress?.district }},
                    {{ order.shippingAddress?.city }},
                    {{ order.shippingAddress?.province }} -
                    {{ order.shippingAddress?.postalCode }}
                  </span>
                </div>

                <div>
                  <span class="block text-gray-400 font-semibold"
                    >Kurir Pengirim</span
                  >
                  <span
                    class="font-extrabold text-gray-800 block mt-0.5 uppercase"
                    >{{ order.courier }} - {{ order.courierService }}</span
                  >
                </div>

                <div v-if="order.trackingNumber">
                  <span class="block text-gray-400 font-semibold"
                    >Nomor Resi Lacak</span
                  >
                  <span
                    class="font-black text-primary-700 block mt-0.5 select-all"
                    >{{ order.trackingNumber }}</span
                  >
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
