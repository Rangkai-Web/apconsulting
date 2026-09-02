<script setup lang="ts">
import { useCartStore } from "~/store/cart";
import { useAuthStore } from "~/store/auth";
import { addressService } from "~/services/addressService";
import { orderService } from "~/services/orderService";
import { useCurrency } from "~/composables/useCurrency";
import type { Address } from "~/types";

definePageMeta({ middleware: "auth" });

const cart = useCartStore();
const authStore = useAuthStore();
const { format } = useCurrency();
const toast = useToast();
const router = useRouter();

useSeoMeta({ title: "Checkout", robots: "noindex, nofollow" });

// Page states
const loading = ref(false);
const addresses = ref<Address[]>([]);
const selectedAddressId = ref<number | null>(null);
const addressSlideoverOpen = ref(false);

// Address form dropdowns state
const provinces = ref<any[]>([]);
const cities = ref<any[]>([]);
const districts = ref<any[]>([]);

// Active address form state
const addressForm = reactive({
  label: "",
  recipientName: "",
  phone: "",
  provinceId: null as number | null,
  provinceName: "",
  cityId: null as number | null,
  cityName: "",
  districtId: null as number | null,
  districtName: "",
  postalCode: "",
  fullAddress: "",
  isDefault: false,
});

// Shipping courier options
const couriersList = [
  { code: "jne", name: "JNE Express" },
  { code: "jnt", name: "J&T Express" },
  { code: "sicepat", name: "SiCepat" },
  { code: "pos", name: "POS Indonesia" },
];
const selectedCourier = ref<string>("");
const shippingServices = ref<any[]>([]);
const loadingShipping = ref(false);
const selectedService = ref<any | null>(null);

// Getters
const selectedAddress = computed(() => {
  return (
    addresses.value.find((a) => a.id === selectedAddressId.value) || null
  );
});

const isSubmitDisabled = computed(() => {
  if (cart.items.length === 0) return true;
  if (loading.value) return true;
  if (!selectedAddressId.value) return true;
  if (!selectedCourier.value || !selectedService.value) return true;

  return false;
});

// Watchers for cascading dropdowns - Member Address Form
watch(
  () => addressForm.provinceId,
  async (newVal) => {
    cities.value = [];
    districts.value = [];
    addressForm.cityId = null;
    addressForm.cityName = "";
    addressForm.districtId = null;
    addressForm.districtName = "";
    if (newVal) {
      const prov = provinces.value.find((p) => p.id === newVal);
      addressForm.provinceName = prov ? prov.name : "";
      try {
        cities.value = await addressService.getCities(newVal);
      } catch {
        toast.add({
          title: "Gagal memuat daftar kota",
          description: "Terjadi kesalahan, coba pilih ulang provinsi.",
          color: "error",
        });
      }
    }
  },
);

watch(
  () => addressForm.cityId,
  async (newVal) => {
    districts.value = [];
    addressForm.districtId = null;
    addressForm.districtName = "";
    if (newVal) {
      const city = cities.value.find((c) => c.id === newVal);
      addressForm.cityName = city ? city.name : "";
      try {
        districts.value = await addressService.getDistricts(newVal);
      } catch {
        toast.add({
          title: "Gagal memuat daftar kecamatan",
          description: "Terjadi kesalahan, coba pilih ulang kota.",
          color: "error",
        });
      }
    }
  },
);

watch(
  () => addressForm.districtId,
  (newVal) => {
    if (newVal) {
      const dist = districts.value.find((d) => d.id === newVal);
      addressForm.districtName = dist ? dist.name : "";
    }
  },
);

// Initial load
const loadingInitial = ref(false);

onMounted(async () => {
  if (cart.items.length === 0) return;

  loadingInitial.value = true;
  try {
    // Load provinces for forms
    provinces.value = await addressService.getProvinces();
    await loadAddresses();
  } catch {
    toast.add({
      title: "Gagal memuat data checkout",
      description: "Terjadi kesalahan, coba muat ulang halaman.",
      color: "error",
    });
  } finally {
    loadingInitial.value = false;
  }
});

async function loadAddresses() {
  addresses.value = await addressService.getAddresses();
  const def = addresses.value.find((a) => a.isDefault);
  if (def) {
    selectedAddressId.value = def.id;
    if (selectedCourier.value) fetchShippingCost();
  } else if (addresses.value.length > 0) {
    selectedAddressId.value = addresses.value[0]?.id || null;
    if (selectedCourier.value) fetchShippingCost();
  }
}

// Watch selected address to reload shipping options
watch(selectedAddressId, () => {
  selectedService.value = null;
  shippingServices.value = [];
  if (selectedCourier.value) fetchShippingCost();
});

// Fetch shipping costs from RajaOngkir API proxy
async function fetchShippingCost() {
  const destinationId = selectedAddress.value?.rajaongkirDistrictId;

  if (!destinationId) return;

  loadingShipping.value = true;
  selectedService.value = null;
  shippingServices.value = [];

  try {
    const services = await orderService.getShippingCost(
      destinationId,
      cart.totalWeight,
    );
    // Filter services based on selectedCourier
    shippingServices.value = services.filter(
      (s) => s.courier.toLowerCase() === selectedCourier.value.toLowerCase(),
    );

    if (shippingServices.value.length === 0) {
      toast.add({
        title: "Kurir Tidak Tersedia",
        description:
          "Layanan kurir ini tidak menjangkau wilayah pengiriman Anda.",
        color: "warning",
      });
    }
  } catch (error: any) {
    console.error("Gagal cek ongkir:", error);
    toast.add({
      title: "Gagal Cek Ongkir",
      description:
        error.data?.message ||
        "Terjadi kesalahan koneksi saat menghitung ongkos kirim.",
      color: "error",
    });
  } finally {
    loadingShipping.value = false;
  }
}

watch(selectedCourier, () => {
  if (selectedCourier.value) {
    fetchShippingCost();
  } else {
    shippingServices.value = [];
    selectedService.value = null;
  }
});

function selectService(service: any) {
  selectedService.value = service;
}

// Add New Member Address Form Submission
async function saveAddress() {
  if (
    !addressForm.recipientName ||
    !addressForm.phone ||
    !addressForm.districtId ||
    !addressForm.fullAddress
  ) {
    toast.add({
      title: "Validasi Gagal",
      description: "Mohon isi semua field alamat wajib.",
      color: "error",
    });
    return;
  }

  try {
    const payload = {
      label: addressForm.label || "Alamat Saya",
      recipientName: addressForm.recipientName,
      phone: addressForm.phone,
      province: addressForm.provinceName,
      city: addressForm.cityName,
      district: addressForm.districtName,
      postalCode: addressForm.postalCode,
      fullAddress: addressForm.fullAddress,
      isDefault: addressForm.isDefault,
      rajaongkirDistrictId: addressForm.districtId,
    };

    const newAddr = await addressService.createAddress(payload);

    toast.add({
      title: "Alamat Disimpan",
      description: "Alamat pengiriman baru berhasil ditambahkan.",
      color: "primary",
    });

    addressSlideoverOpen.value = false;

    // Reset form
    Object.assign(addressForm, {
      label: "",
      recipientName: "",
      phone: "",
      provinceId: null,
      provinceName: "",
      cityId: null,
      cityName: "",
      districtId: null,
      districtName: "",
      postalCode: "",
      fullAddress: "",
      isDefault: false,
    });

    await loadAddresses();
    selectedAddressId.value = newAddr.id;
  } catch (error: any) {
    toast.add({
      title: "Gagal Menyimpan Alamat",
      description:
        error.data?.message || "Terjadi kesalahan saat menyimpan alamat.",
      color: "error",
    });
  }
}

function orderUrl(
  orderNumber: string,
  extra: Record<string, string> = {},
): string {
  const params = new URLSearchParams(extra);
  const qs = params.toString();
  return `/orders/${orderNumber}${qs ? `?${qs}` : ""}`;
}

// Submit Checkout Order & Initiate Payment Flow
async function handleCheckout() {
  if (isSubmitDisabled.value) return;

  loading.value = true;

  try {
    const orderPayload = {
      items: cart.items.map((i) => ({ variantId: i.variantId, qty: i.qty })),
      addressId: selectedAddressId.value,
      courier: selectedCourier.value,
      courierService: selectedService.value.service,
      shippingCost: selectedService.value.cost,
    };

    // Submit Order
    const { orderNumber } = await orderService.createOrder(orderPayload);

    cart.clear();
    router.push(orderUrl(orderNumber));
  } catch (error: any) {
    console.error("Gagal melakukan checkout:", error);
    toast.add({
      title: "Checkout Gagal",
      description:
        error.data?.message ||
        "Gagal memproses transaksi Anda. Silakan coba lagi.",
      color: "error",
    });
  } finally {
    loading.value = false;
  }
}

function openAddressSlideover() {
  addressSlideoverOpen.value = true;
}

function closeAddressSlideover() {
  addressSlideoverOpen.value = false;
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

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 space-y-8">
      <!-- Breadcrumb & Step Navigation Header -->
      <div
        class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white/80 backdrop-blur-md border border-gray-200/80 rounded-2xl p-4 shadow-xs"
      >
        <div class="flex items-center gap-3">
          <div
            class="w-10 h-10 rounded-xl bg-linear-to-tr from-primary-600 to-primary-400 flex items-center justify-center text-white shadow-md shadow-primary-500/20 shrink-0"
          >
            <UIcon name="i-heroicons-credit-card" class="w-5 h-5" />
          </div>
          <div>
            <div
              class="flex items-center gap-2 text-xs font-semibold text-gray-500"
            >
              <NuxtLink to="/" class="hover:text-primary-600">Beranda</NuxtLink>
              <span>/</span>
              <NuxtLink to="/cart" class="hover:text-primary-600"
                >Keranjang</NuxtLink
              >
              <span>/</span>
              <span class="text-primary-600 font-bold">Checkout</span>
            </div>
            <h1
              class="text-lg md:text-xl font-black text-gray-900 tracking-tight"
            >
              Penyelesaian Pesanan
            </h1>
          </div>
        </div>

        <!-- Checkout Steps Progress -->
        <div
          class="flex items-center gap-1.5 sm:gap-3 text-[11px] sm:text-xs font-bold overflow-x-auto no-scrollbar py-0.5 shrink-0"
        >
          <NuxtLink
            to="/cart"
            class="flex items-center gap-1.5 text-emerald-600 bg-emerald-50 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-full border border-emerald-200/60 shadow-2xs hover:bg-emerald-100 transition-colors shrink-0"
          >
            <span
              class="w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[9px] sm:text-[10px] font-black"
              >✓</span
            >
            <span>Keranjang</span>
          </NuxtLink>
          <UIcon
            name="i-heroicons-chevron-right"
            class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-300 shrink-0"
          />
          <div
            class="flex items-center gap-1.5 text-primary-600 bg-primary-50 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-full border border-primary-200/60 shadow-2xs shrink-0"
          >
            <span
              class="w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-primary-600 text-white flex items-center justify-center text-[9px] sm:text-[10px] font-black"
              >2</span
            >
            <span>Checkout</span>
          </div>
          <UIcon
            name="i-heroicons-chevron-right"
            class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-300 shrink-0"
          />
          <div class="flex items-center gap-1.5 text-gray-400 shrink-0">
            <span
              class="w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-[9px] sm:text-[10px] font-black"
              >3</span
            >
            <span>Pembayaran</span>
          </div>
        </div>
      </div>

      <!-- Empty State Fallback -->
      <div
        v-if="cart.items.length === 0"
        class="max-w-lg mx-auto bg-white/90 backdrop-blur-md border border-gray-200/80 rounded-3xl p-8 sm:p-12 shadow-xl shadow-gray-900/5 text-center"
      >
        <EmptyState
          icon="i-heroicons-shopping-cart"
          title="Keranjang Belanja Masih Kosong"
          description="Tambahkan beberapa produk pilihan BbystarGO ke keranjang belanja Anda terlebih dahulu untuk diproses."
          action-label="Mulai Belanja Sekarang"
          action-to="/katalog"
        />
      </div>

      <!-- Main Layout Grid -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left Column: Steps (Address & Shipping Courier) -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-6">
          <!-- STEP 1: Alamat Pengiriman -->
          <div
            class="bg-white/90 backdrop-blur-md border border-gray-200/80 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6"
          >
            <div
              class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-gray-100 pb-4"
            >
              <div class="flex items-center gap-3 mt-4 sm:mt-0">
                <span
                  class="w-8 h-8 rounded-full bg-linear-to-tr from-primary-600 to-primary-400 text-white flex items-center justify-center text-xs font-black shadow-md shadow-primary-500/20"
                >
                  1
                </span>
                <div>
                  <h2
                    class="text-base font-extrabold text-gray-900 leading-tight"
                  >
                    Alamat Pengiriman
                  </h2>
                  <p class="text-xs text-gray-500">
                    Pilih atau isi lokasi pengiriman paket Anda
                  </p>
                </div>
              </div>

              <UButton
                icon="i-heroicons-plus"
                color="primary"
                variant="solid"
                class="rounded-xl font-bold cursor-pointer shadow-sm text-xs mt-4 sm:mt-0"
                @click="openAddressSlideover"
              >
                Tambah Alamat
              </UButton>
            </div>

            <!-- Member View: Address Cards Grid -->
            <div class="space-y-4">
              <div
                v-if="loadingInitial"
                class="grid grid-cols-1 md:grid-cols-2 gap-4"
              >
                <USkeleton v-for="n in 2" :key="n" class="h-32 rounded-2xl" />
              </div>

              <div
                v-else-if="addresses.length === 0"
                class="text-center py-10 bg-gray-50/80 rounded-2xl border border-dashed border-gray-200 space-y-3"
              >
                <div
                  class="w-12 h-12 rounded-full bg-primary-50 text-primary-500 flex items-center justify-center mx-auto"
                >
                  <UIcon name="i-heroicons-map-pin" class="w-6 h-6" />
                </div>
                <div>
                  <p class="text-xs font-bold text-gray-800">
                    Belum Ada Alamat Tersimpan
                  </p>
                  <p class="text-xs text-gray-400 mt-0.5">
                    Tambahkan alamat utama untuk memudahkan pengiriman.
                  </p>
                </div>
                <UButton
                  color="primary"
                  variant="solid"
                  class="rounded-xl font-bold cursor-pointer text-xs"
                  @click="openAddressSlideover"
                >
                  + Tambah Alamat Baru
                </UButton>
              </div>

              <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                  v-for="addr in addresses"
                  :key="addr.id"
                  class="relative p-5 rounded-2xl border cursor-pointer transition-all duration-300 space-y-2"
                  :class="
                    selectedAddressId === addr.id
                      ? 'border-primary-500 bg-primary-50/20 shadow-md shadow-primary-900/5 ring-2 ring-primary-500/20'
                      : 'border-gray-200/80 hover:border-gray-300 hover:bg-gray-50/50'
                  "
                  @click="selectedAddressId = addr.id"
                >
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <span class="text-xs font-black text-gray-900">{{
                        addr.label
                      }}</span>
                      <span
                        v-if="addr.isDefault"
                        class="px-2 py-0.5 bg-primary-100 text-[9px] text-primary-700 font-extrabold rounded-full border border-primary-200/60"
                      >
                        Utama
                      </span>
                    </div>

                    <div
                      class="w-5 h-5 rounded-full border flex items-center justify-center transition-colors"
                      :class="
                        selectedAddressId === addr.id
                          ? 'border-primary-600 bg-primary-600 text-white'
                          : 'border-gray-300 bg-white'
                      "
                    >
                      <UIcon
                        v-if="selectedAddressId === addr.id"
                        name="i-heroicons-check"
                        class="w-3 h-3 stroke-[3]"
                      />
                    </div>
                  </div>

                  <div class="space-y-0.5">
                    <p class="text-xs font-bold text-gray-800">
                      {{ addr.recipientName }}
                    </p>
                    <p class="text-xs text-gray-500 font-medium">
                      {{ addr.phone }}
                    </p>
                  </div>

                  <p
                    class="text-xs text-gray-600 font-normal leading-relaxed line-clamp-2 pt-1 border-t border-gray-100"
                  >
                    {{ addr.fullAddress }}
                  </p>
                  <p class="text-[11px] text-gray-400 font-semibold">
                    {{ addr.district }}, {{ addr.city }}, {{ addr.province }} -
                    {{ addr.postalCode }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- STEP 2: Kurir & Layanan Pengiriman -->
          <div
            class="bg-white/90 backdrop-blur-md border border-gray-200/80 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6"
          >
            <div
              class="flex flex-col sm:flex-row items-start sm:items-center gap-3 border-b border-gray-100 pb-4"
            >
              <span
                class="w-8 h-8 rounded-full bg-linear-to-tr from-primary-600 to-primary-400 text-white flex items-center justify-center text-xs font-black shadow-md shadow-primary-500/20"
              >
                2
              </span>
              <div>
                <h2
                  class="text-base font-extrabold text-gray-900 leading-tight"
                >
                  Kurir &amp; Paket Pengiriman
                </h2>
                <p class="text-xs text-gray-500">
                  Pilih ekspedisi dan tarif ongkos kirim sesuai lokasi Anda
                </p>
              </div>
            </div>

            <!-- Lock Message if Address not selected -->
            <div
              v-if="!selectedAddressId"
              class="text-center py-8 bg-gray-50/60 rounded-2xl border border-dashed border-gray-200 text-gray-400 text-xs font-semibold space-y-2"
            >
              <UIcon
                name="i-heroicons-truck"
                class="w-8 h-8 mx-auto text-gray-300"
              />
              <p>
                Silakan tentukan alamat pengiriman di atas terlebih dahulu untuk
                menghitung opsi ongkos kirim.
              </p>
            </div>

            <div v-else class="space-y-6">
              <!-- Select Courier Brand Buttons -->
              <div>
                <span
                  class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-3"
                >
                  1. Pilih Ekspedisi Pengiriman
                </span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                  <button
                    v-for="c in couriersList"
                    :key="c.code"
                    type="button"
                    class="p-3.5 rounded-2xl border font-black text-xs transition-all duration-300 cursor-pointer flex flex-col items-center justify-center gap-1.5"
                    :class="
                      selectedCourier === c.code
                        ? 'border-primary-600 bg-primary-50/30 text-primary-700 shadow-md shadow-primary-900/5 ring-2 ring-primary-500/20'
                        : 'border-gray-200/80 hover:bg-gray-50 hover:border-gray-300 text-gray-700'
                    "
                    @click="selectedCourier = c.code"
                  >
                    <UIcon
                      name="i-heroicons-truck"
                      class="w-5 h-5 text-primary-500"
                    />
                    <span>{{ c.name }}</span>
                  </button>
                </div>
              </div>

              <!-- Shipping Services List -->
              <div>
                <span
                  class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-3"
                >
                  2. Pilih Layanan Tarif
                </span>

                <div
                  v-if="loadingShipping"
                  class="flex items-center justify-center gap-2 py-8 bg-gray-50/50 rounded-2xl border border-gray-100 text-xs text-gray-500 font-bold"
                >
                  <UIcon
                    name="i-heroicons-arrow-path"
                    class="w-5 h-5 animate-spin text-primary-600"
                  />
                  <span>Menghitung tarif ongkos kirim RajaOngkir...</span>
                </div>

                <div
                  v-else-if="selectedCourier && shippingServices.length > 0"
                  class="grid grid-cols-1 sm:grid-cols-2 gap-3.5"
                >
                  <div
                    v-for="service in shippingServices"
                    :key="service.service"
                    class="p-4 rounded-2xl border cursor-pointer flex items-center justify-between transition-all duration-300"
                    :class="
                      selectedService?.service === service.service
                        ? 'border-primary-500 bg-primary-50/20 shadow-md shadow-primary-900/5 ring-2 ring-primary-500/20'
                        : 'border-gray-200/80 hover:border-gray-300 hover:bg-gray-50/50'
                    "
                    @click="selectService(service)"
                  >
                    <div class="space-y-1">
                      <p class="text-xs font-black text-gray-900 uppercase">
                        {{ service.courier }} - {{ service.service }}
                      </p>
                      <p
                        class="text-[11px] text-gray-500 font-semibold flex items-center gap-1"
                      >
                        <UIcon
                          name="i-heroicons-clock"
                          class="w-3.5 h-3.5 text-gray-400"
                        />
                        Estimasi tiba:
                        {{
                          service.etd
                            ? service.etd.replace(/\bday\b/gi, "hari")
                            : "-"
                        }}
                      </p>
                    </div>
                    <span
                      class="text-sm font-black text-primary-600 bg-white px-2.5 py-1 rounded-xl border border-gray-200/80 shadow-2xs"
                    >
                      {{ format(service.cost) }}
                    </span>
                  </div>
                </div>

                <div
                  v-else-if="selectedCourier"
                  class="text-center py-6 bg-primary-100/50 rounded-2xl border border-primary-200/60 text-xs text-primary-600 font-semibold"
                >
                  Layanan ekspedisi {{ selectedCourier.toUpperCase() }} tidak
                  tersedia untuk rute tujuan ini. Silakan coba pilih kurir
                  lainnya.
                </div>

                <div
                  v-else
                  class="text-center py-6 bg-gray-100/50 rounded-2xl border border-gray-200 text-xs text-gray-400 font-semibold"
                >
                  Pilih salah satu jenis ekspedisi di atas untuk menampilkan
                  tarif pengiriman.
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Order Summary Panel -->
        <div class="lg:col-span-5 xl:col-span-4 sticky top-24">
          <div
            class="bg-white/90 backdrop-blur-md border border-gray-200/80 rounded-3xl p-6 shadow-xl shadow-gray-900/5 space-y-6 relative overflow-hidden"
          >
            <!-- Panel Header -->
            <div
              class="flex items-center justify-between border-b border-gray-100 pb-4"
            >
              <h2
                class="text-sm font-extrabold text-gray-900 uppercase tracking-wide"
              >
                Ringkasan Transaksi
              </h2>
              <span
                class="text-xs font-bold text-primary-600 bg-primary-50 px-2.5 py-0.5 rounded-full border border-primary-200/60"
              >
                {{ cart.totalQty }} produk
              </span>
            </div>

            <!-- Product Thumbnails List -->
            <div
              class="max-h-56 overflow-y-auto space-y-3 pr-1 divide-y divide-gray-100"
            >
              <div
                v-for="item in cart.items"
                :key="item.variantId"
                class="pt-3 first:pt-0 flex gap-3 items-center"
              >
                <img
                  :src="item.image ?? 'https://picsum.photos/seed/cart/100/100'"
                  :alt="item.productName"
                  class="w-12 h-12 object-cover rounded-xl border border-gray-200/80 shrink-0 bg-gray-50"
                />
                <div class="min-w-0 flex-1 space-y-0.5">
                  <p class="text-xs font-bold text-gray-900 truncate">
                    {{ item.productName }}
                  </p>
                  <div
                    class="flex items-center gap-1.5 text-[10px] text-gray-500 font-semibold"
                  >
                    <span>{{ item.variantName }} (x{{ item.qty }})</span>
                    <span
                      v-if="item.fulfillmentType === 'pre_order'"
                      class="px-1.5 py-0.2 bg-amber-100 text-amber-800 text-[8px] font-black rounded"
                    >
                      DP
                    </span>
                  </div>
                  <p class="text-xs font-black text-primary-600">
                    {{ format(item.price * item.qty) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Cost Recap -->
            <div
              class="space-y-3 border-t border-gray-100 pt-4 text-xs font-semibold"
            >
              <div class="flex items-center justify-between text-gray-600">
                <span>Subtotal Produk</span>
                <span class="text-gray-900 font-extrabold">{{
                  format(cart.subtotal)
                }}</span>
              </div>
              <div class="flex items-center justify-between text-gray-600">
                <span>Ongkos Kirim</span>
                <span
                  v-if="selectedService"
                  class="text-gray-900 font-extrabold"
                >
                  {{ format(selectedService.cost) }}
                </span>
                <span
                  v-else
                  class="text-amber-600 italic text-[11px] font-bold"
                >
                  Belum dipilih
                </span>
              </div>

              <div
                class="border-t border-gray-100 pt-3 flex items-center justify-between"
              >
                <div>
                  <span class="text-sm font-black text-gray-900 block"
                    >Total Tagihan</span
                  >
                  <span class="text-[10px] text-gray-400 font-normal"
                    >Sudah termasuk produk &amp; ongkir</span
                  >
                </div>
                <span class="text-lg font-black text-primary-600">
                  {{
                    format(
                      cart.subtotal +
                        (selectedService ? selectedService.cost : 0),
                    )
                  }}
                </span>
              </div>
            </div>

            <!-- Submit Order Button -->
            <div class="pt-2">
              <UButton
                block
                color="primary"
                size="xl"
                class="font-black py-4 rounded-2xl cursor-pointer shadow-lg shadow-primary-500/25 hover:shadow-primary-500/35 hover:scale-[1.01] transition-all duration-300 flex items-center justify-center gap-2 text-sm"
                :disabled="isSubmitDisabled"
                :loading="loading"
                @click="handleCheckout"
              >
                <UIcon name="i-heroicons-lock-closed" class="w-4 h-4" />
                <span>Bayar Sekarang</span>
              </UButton>
            </div>

            <!-- Security Assurance -->
            <div
              class="flex items-center justify-center gap-2 text-[10px] font-bold text-gray-400 text-center"
            >
              <UIcon
                name="i-heroicons-shield-check"
                class="w-4 h-4 text-emerald-500"
              />
              <span>Transaksi Aman &amp; Terverifikasi BbystarGO</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Member Address Creation Slideover -->
    <USlideover
      v-model:open="addressSlideoverOpen"
      title="Tambah Alamat Pengiriman Baru"
    >
      <template #content>
        <div
          class="p-6 space-y-6 h-full flex flex-col justify-between bg-white overflow-y-auto"
        >
          <div class="space-y-4">
            <div
              class="flex items-center justify-between pb-3 border-b border-gray-100"
            >
              <div>
                <h2 class="text-sm font-black text-gray-900">
                  Alamat Pengiriman Baru
                </h2>
                <p class="text-xs text-gray-500">
                  Isi rincian lokasi tujuan pengiriman paket Anda
                </p>
              </div>
              <UButton
                icon="i-heroicons-x-mark"
                color="neutral"
                variant="ghost"
                aria-label="Tutup formulir"
                class="hover:bg-gray-100 rounded-lg"
                @click="closeAddressSlideover"
              />
            </div>

            <UFormField label="Label Alamat (Contoh: Rumah, Kantor)">
              <UInput
                v-model="addressForm.label"
                placeholder="Rumah Saya"
                class="w-full"
              />
            </UFormField>

            <UFormField label="Nama Penerima" required>
              <UInput
                v-model="addressForm.recipientName"
                placeholder="Nama penerima paket"
                class="w-full"
              />
            </UFormField>

            <UFormField label="Nomor Telepon" required>
              <UInput
                v-model="addressForm.phone"
                placeholder="0812345678"
                class="w-full"
              />
            </UFormField>

            <!-- Dropdown cascading wilayah untuk member -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <UFormField label="Provinsi" required>
                <USelect
                  v-model="addressForm.provinceId"
                  :items="provinces"
                  placeholder="Pilih Provinsi"
                  class="w-full"
                  label-key="name"
                  value-key="id"
                />
              </UFormField>
              <UFormField label="Kota / Kabupaten" required>
                <USelect
                  v-model="addressForm.cityId"
                  :items="cities"
                  placeholder="Pilih Kota"
                  class="w-full"
                  :disabled="!addressForm.provinceId"
                  label-key="name"
                  value-key="id"
                />
              </UFormField>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <UFormField label="Kecamatan" required>
                <USelect
                  v-model="addressForm.districtId"
                  :items="districts"
                  placeholder="Pilih Kecamatan"
                  class="w-full"
                  :disabled="!addressForm.cityId"
                  label-key="name"
                  value-key="id"
                />
              </UFormField>
              <UFormField label="Kode Pos" required>
                <UInput
                  v-model="addressForm.postalCode"
                  placeholder="Kode pos"
                  class="w-full"
                />
              </UFormField>
            </div>

            <UFormField label="Alamat Lengkap" required>
              <UTextarea
                v-model="addressForm.fullAddress"
                placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan"
                class="w-full"
              />
            </UFormField>

            <div class="pt-2">
              <UCheckbox
                v-model="addressForm.isDefault"
                label="Jadikan sebagai alamat utama/default"
              />
            </div>
          </div>

          <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
            <UButton
              color="neutral"
              variant="outline"
              class="flex-1 justify-center rounded-xl cursor-pointer font-bold"
              @click="closeAddressSlideover"
            >
              Batal
            </UButton>
            <UButton
              color="primary"
              class="flex-1 justify-center rounded-xl font-extrabold cursor-pointer"
              @click="saveAddress"
            >
              Simpan Alamat
            </UButton>
          </div>
        </div>
      </template>
    </USlideover>
  </div>
</template>
