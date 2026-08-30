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

// Guest checkout form state
const guestForm = reactive({
  name: "",
  email: "",
  phone: "",
  recipientName: "",
  recipientPhone: "",
  provinceId: null as number | null,
  provinceName: "",
  cityId: null as number | null,
  cityName: "",
  districtId: null as number | null,
  districtName: "",
  postalCode: "",
  fullAddress: "",
});

const guestFieldErrors = reactive({
  name: "",
  email: "",
  phone: "",
  recipientName: "",
  recipientPhone: "",
  provinceId: "",
  cityId: "",
  districtId: "",
  postalCode: "",
  fullAddress: "",
});

// Clear a field's error as soon as the user fills it in
watch(
  guestForm,
  () => {
    for (const key of Object.keys(guestFieldErrors) as (keyof typeof guestFieldErrors)[]) {
      if (guestForm[key]) guestFieldErrors[key] = "";
    }
  },
  { deep: true },
);

// Validate every guest field so the user sees exactly which one is wrong,
// instead of just a disabled submit button with no explanation.
function validateGuestForm(): boolean {
  (Object.keys(guestFieldErrors) as (keyof typeof guestFieldErrors)[]).forEach(
    (key) => { guestFieldErrors[key] = ""; },
  );

  if (!guestForm.name) guestFieldErrors.name = "Nama wajib diisi.";
  if (!guestForm.email) guestFieldErrors.email = "Email wajib diisi.";
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(guestForm.email))
    guestFieldErrors.email = "Format email tidak valid.";
  if (!guestForm.phone) guestFieldErrors.phone = "No. telepon wajib diisi.";
  if (!guestForm.recipientName)
    guestFieldErrors.recipientName = "Nama penerima wajib diisi.";
  if (!guestForm.recipientPhone)
    guestFieldErrors.recipientPhone = "No. telp penerima wajib diisi.";
  if (!guestForm.provinceId)
    guestFieldErrors.provinceId = "Provinsi wajib dipilih.";
  if (!guestForm.cityId) guestFieldErrors.cityId = "Kota wajib dipilih.";
  if (!guestForm.districtId)
    guestFieldErrors.districtId = "Kecamatan wajib dipilih.";
  if (!guestForm.postalCode)
    guestFieldErrors.postalCode = "Kode pos wajib diisi.";
  if (!guestForm.fullAddress)
    guestFieldErrors.fullAddress = "Alamat lengkap wajib diisi.";

  return Object.values(guestFieldErrors).every((e) => !e);
}

// Shipping courier options
const couriersList = [
  { code: "jne", name: "JNE" },
  { code: "jnt", name: "J&T" },
  { code: "sicepat", name: "SiCepat" },
  { code: "pos", name: "POS Indonesia" },
];
const selectedCourier = ref<string>("");
const shippingServices = ref<any[]>([]);
const loadingShipping = ref(false);
const selectedService = ref<any | null>(null);

// Getters
const isLoggedIn = computed(() => authStore.isLoggedIn);
const selectedAddress = computed(() => {
  if (isLoggedIn.value) {
    return (
      addresses.value.find((a) => a.id === selectedAddressId.value) || null
    );
  }
  return null;
});

const isSubmitDisabled = computed(() => {
  if (cart.items.length === 0) return true;
  if (loading.value) return true;

  if (isLoggedIn.value) {
    if (!selectedAddressId.value) return true;
  }
  // Guest field completeness is validated on submit (validateGuestForm),
  // with per-field error messages — a disabled button gave no feedback on
  // which field was missing.

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

// Watchers for cascading dropdowns - Guest Address Form
const guestCities = ref<any[]>([]);
const guestDistricts = ref<any[]>([]);

watch(
  () => guestForm.provinceId,
  async (newVal) => {
    guestCities.value = [];
    guestDistricts.value = [];
    guestForm.cityId = null;
    guestForm.cityName = "";
    guestForm.districtId = null;
    guestForm.districtName = "";
    if (newVal) {
      const prov = provinces.value.find((p) => p.id === newVal);
      guestForm.provinceName = prov ? prov.name : "";
      try {
        guestCities.value = await addressService.getCities(newVal);
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
  () => guestForm.cityId,
  async (newVal) => {
    guestDistricts.value = [];
    guestForm.districtId = null;
    guestForm.districtName = "";
    if (newVal) {
      const city = guestCities.value.find((c) => c.id === newVal);
      guestForm.cityName = city ? city.name : "";
      try {
        guestDistricts.value = await addressService.getDistricts(newVal);
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
  () => guestForm.districtId,
  (newVal) => {
    if (newVal) {
      const dist = guestDistricts.value.find((d) => d.id === newVal);
      guestForm.districtName = dist ? dist.name : "";
      // Reset selected courier service as destination district changed
      selectedService.value = null;
      shippingServices.value = [];
      if (selectedCourier.value) fetchShippingCost();
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

    if (isLoggedIn.value) {
      await loadAddresses();
    }
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
    // Auto load shipping costs if default address exists
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
  const destinationId = isLoggedIn.value
    ? selectedAddress.value?.rajaongkirDistrictId
    : guestForm.districtId;

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

// Build order detail URL with guest email if not logged in
function orderUrl(orderNumber: string, extra: Record<string, string> = {}): string {
  const params = new URLSearchParams(extra);
  if (!isLoggedIn.value && guestForm.email) {
    params.set("email", guestForm.email);
  }
  const qs = params.toString();
  return `/orders/${orderNumber}${qs ? `?${qs}` : ""}`;
}

// Submit Checkout Order & Initiate Payment Flow
async function handleCheckout() {
  if (isSubmitDisabled.value) return;

  if (!isLoggedIn.value && !validateGuestForm()) {
    toast.add({
      title: "Lengkapi Data Pengiriman",
      description: "Mohon periksa kembali field yang ditandai merah.",
      color: "error",
    });
    return;
  }

  loading.value = true;

  try {
    let orderPayload: any = {
      items: cart.items.map((i) => ({ variantId: i.variantId, qty: i.qty })),
      courier: selectedCourier.value,
      courierService: selectedService.value.service,
      shippingCost: selectedService.value.cost,
    };

    if (isLoggedIn.value) {
      orderPayload.addressId = selectedAddressId.value;
    } else {
      orderPayload.guestName = guestForm.name;
      orderPayload.guestEmail = guestForm.email;
      orderPayload.guestPhone = guestForm.phone;
      orderPayload.shippingAddress = {
        recipientName: guestForm.recipientName,
        phone: guestForm.recipientPhone,
        province: guestForm.provinceName,
        city: guestForm.cityName,
        district: guestForm.districtName,
        postalCode: guestForm.postalCode,
        fullAddress: guestForm.fullAddress,
        rajaongkirDistrictId: guestForm.districtId,
      };
    }

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
</script>

<template>
  <div
    class="relative overflow-hidden bg-linear-to-b from-primary-50/45 via-primary-50/15 to-white min-h-screen py-10"
  >
    <!-- Background cute decor spanning whole page -->
    <AppCuteDecor />

    <!-- Ambient Background Glows -->
    <div
      class="absolute top-12 -left-40 w-96 h-96 bg-primary-200/20 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-[400px] -right-40 w-96 h-96 bg-primary-100/20 rounded-full blur-3xl pointer-events-none"
    ></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
      <!-- Title Block -->
      <div class="mb-8 flex items-center gap-3">
        <div
          class="w-10 h-10 rounded-2xl bg-primary-50/80 flex items-center justify-center text-primary-500 shadow-sm border border-primary-100/50"
        >
          <UIcon name="i-heroicons-credit-card" class="w-5 h-5" />
        </div>
        <div>
          <span
            class="text-[10px] text-primary-500 uppercase font-extrabold tracking-wider"
            >Penyelesaian Pesanan</span
          >
          <h1
            class="text-xl md:text-2xl font-black text-gray-900 tracking-tight leading-none mt-0.5"
          >
            Checkout
          </h1>
        </div>
      </div>

      <!-- Empty State Fallback -->
      <div
        v-if="cart.items.length === 0"
        class="max-w-md mx-auto bg-white/70 backdrop-blur-sm border border-gray-100 rounded-3xl p-8 shadow-sm"
      >
        <EmptyState
          icon="i-heroicons-shopping-cart"
          title="Keranjang Belanja Kosong"
          description="Tambahkan beberapa produk pilihan kami ke keranjang belanja Anda terlebih dahulu."
          action-label="Mulai Belanja"
          action-to="/katalog"
        />
      </div>

      <!-- Main Grid -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-8">
        <!-- Left Column: Address, Courier Options -->
        <div class="space-y-8">
          <!-- Step 1: Address Section -->
          <div
            class="bg-white border border-gray-100 rounded-3xl p-6 md:p-8 shadow-xs"
          >
            <div
              class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6"
            >
              <div class="flex items-center gap-3">
                <span
                  class="w-7 h-7 rounded-full bg-primary-50 text-primary-700 flex items-center justify-center text-xs font-bold ring-4 ring-primary-50/50"
                  >1</span
                >
                <h2 class="text-base font-extrabold text-gray-900">
                  Alamat Pengiriman
                </h2>
              </div>
              <UButton
                v-if="isLoggedIn"
                icon="i-heroicons-plus"
                color="primary"
                variant="ghost"
                class="rounded-xl font-bold cursor-pointer"
                @click="() => { addressSlideoverOpen = true }"
              >
                Tambah Alamat
              </UButton>
            </div>

            <!-- Member View: Address Selection -->
            <div v-if="isLoggedIn" class="space-y-4">
              <div v-if="loadingInitial" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <USkeleton v-for="n in 2" :key="n" class="h-28 rounded-2xl" />
              </div>

              <div
                v-else-if="addresses.length === 0"
                class="text-center py-8 bg-gray-55/30 rounded-2xl border border-dashed border-gray-200"
              >
                <UIcon
                  name="i-heroicons-map-pin"
                  class="w-8 h-8 text-gray-300 mx-auto mb-2"
                />
                <p class="text-xs text-gray-400 font-semibold mb-3">
                  Anda belum memiliki alamat tersimpan.
                </p>
                <UButton
                  color="primary"
                  variant="solid"
                  class="rounded-xl font-semibold cursor-pointer"
                  @click="() => { addressSlideoverOpen = true }"
                >
                  Tambah Alamat Baru
                </UButton>
              </div>

              <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                  v-for="addr in addresses"
                  :key="addr.id"
                  class="relative p-5 rounded-2xl border cursor-pointer transition-all duration-200"
                  :class="
                    selectedAddressId === addr.id
                      ? 'border-primary-500 bg-primary-50/10 shadow-md shadow-primary-950/5'
                      : 'border-gray-100 hover:border-gray-250 hover:bg-gray-50/20'
                  "
                  @click="selectedAddressId = addr.id"
                >
                  <!-- Badge default -->
                  <div class="flex items-center justify-between mb-3.5">
                    <span
                      class="text-xs font-black text-gray-900 leading-none"
                      >{{ addr.label }}</span
                    >
                    <span
                      v-if="addr.isDefault"
                      class="px-2 py-0.5 bg-primary-100 text-[9px] text-primary-700 font-bold rounded-full"
                      >Utama</span
                    >
                  </div>

                  <p class="text-xs font-bold text-gray-800">
                    {{ addr.recipientName }}
                  </p>
                  <p class="text-xs text-gray-400 font-medium mt-0.5">
                    {{ addr.phone }}
                  </p>
                  <p
                    class="text-xs text-gray-500 font-medium mt-2.5 line-clamp-2"
                  >
                    {{ addr.fullAddress }}
                  </p>
                  <p class="text-xs text-gray-400 font-semibold mt-1">
                    {{ addr.district }}, {{ addr.city }}, {{ addr.province }} -
                    {{ addr.postalCode }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Guest View: Details & Shipping Address Form -->
            <div v-else class="space-y-6">
              <!-- Guest Info -->
              <div
                class="bg-primary-50/10 border border-primary-100/50 rounded-2xl p-5 space-y-4"
              >
                <h3
                  class="text-xs uppercase font-extrabold tracking-wider text-primary-800"
                >
                  Informasi Pemesan (Guest)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <UFormField label="Nama Lengkap" required :error="guestFieldErrors.name || undefined">
                    <UInput
                      v-model="guestForm.name"
                      placeholder="Masukkan nama lengkap Anda"
                      class="w-full"
                    />
                  </UFormField>
                  <UFormField label="Email" required :error="guestFieldErrors.email || undefined">
                    <UInput
                      v-model="guestForm.email"
                      type="email"
                      placeholder="Masukkan email aktif"
                      class="w-full"
                    />
                  </UFormField>
                  <UFormField label="Nomor Telepon" required :error="guestFieldErrors.phone || undefined">
                    <UInput
                      v-model="guestForm.phone"
                      placeholder="Contoh: 0812345678"
                      class="w-full"
                    />
                  </UFormField>
                </div>
              </div>

              <!-- Shipping Address Info -->
              <div class="space-y-4 pt-2">
                <h3
                  class="text-xs uppercase font-extrabold tracking-wider text-primary-800"
                >
                  Alamat Kiriman
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <UFormField label="Nama Penerima" required :error="guestFieldErrors.recipientName || undefined">
                    <UInput
                      v-model="guestForm.recipientName"
                      placeholder="Nama penerima paket"
                      class="w-full"
                    />
                  </UFormField>
                  <UFormField label="No. Telp Penerima" required :error="guestFieldErrors.recipientPhone || undefined">
                    <UInput
                      v-model="guestForm.recipientPhone"
                      placeholder="No. telp penerima paket"
                      class="w-full"
                    />
                  </UFormField>
                </div>

                <!-- Dropdowns wilayah cascading -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <UFormField label="Provinsi" required :error="guestFieldErrors.provinceId || undefined">
                    <USelect
                      v-model="guestForm.provinceId"
                      :items="provinces"
                      placeholder="Pilih Provinsi"
                      class="w-full"
                      label-key="name"
                      value-key="id"
                    />
                  </UFormField>
                  <UFormField label="Kota / Kabupaten" required :error="guestFieldErrors.cityId || undefined">
                    <USelect
                      v-model="guestForm.cityId"
                      :items="guestCities"
                      placeholder="Pilih Kota"
                      class="w-full"
                      :disabled="!guestForm.provinceId"
                      label-key="name"
                      value-key="id"
                    />
                  </UFormField>
                  <UFormField label="Kecamatan" required :error="guestFieldErrors.districtId || undefined">
                    <USelect
                      v-model="guestForm.districtId"
                      :items="guestDistricts"
                      placeholder="Pilih Kecamatan"
                      class="w-full"
                      :disabled="!guestForm.cityId"
                      label-key="name"
                      value-key="id"
                    />
                  </UFormField>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-[1fr_160px] gap-4">
                  <UFormField label="Alamat Lengkap" required :error="guestFieldErrors.fullAddress || undefined">
                    <UTextarea
                      v-model="guestForm.fullAddress"
                      placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan"
                      class="w-full"
                    />
                  </UFormField>
                  <UFormField label="Kode Pos" required :error="guestFieldErrors.postalCode || undefined">
                    <UInput
                      v-model="guestForm.postalCode"
                      placeholder="Kode pos"
                      class="w-full"
                    />
                  </UFormField>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 2: Shipping Options Section -->
          <div
            class="bg-white border border-gray-100 rounded-3xl p-6 md:p-8 shadow-xs"
          >
            <div
              class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6"
            >
              <span
                class="w-7 h-7 rounded-full bg-primary-50 text-primary-700 flex items-center justify-center text-xs font-bold ring-4 ring-primary-50/50"
                >2</span
              >
              <h2 class="text-base font-extrabold text-gray-900">
                Kurir & Paket Pengiriman
              </h2>
            </div>

            <!-- Wait for destination area to be chosen -->
            <div
              v-if="isLoggedIn ? !selectedAddressId : !guestForm.districtId"
              class="text-center py-6 text-gray-400 text-xs font-semibold"
            >
              Silakan pilih/isi alamat pengiriman terlebih dahulu untuk
              menghitung ongkos kirim.
            </div>

            <div v-else class="space-y-6">
              <!-- Select courier brand -->
              <div>
                <span class="block text-xs font-black text-gray-700 mb-3"
                  >Pilih Kurir Eksplisit</span
                >
                <div class="flex flex-wrap items-center gap-3">
                  <button
                    v-for="c in couriersList"
                    :key="c.code"
                    type="button"
                    class="px-5 py-3 rounded-xl border font-bold text-xs transition-all duration-200 cursor-pointer"
                    :class="
                      selectedCourier === c.code
                        ? 'border-primary-600 bg-primary-50/20 text-primary-700 shadow-sm'
                        : 'border-gray-150 hover:bg-gray-50/50 text-gray-600'
                    "
                    @click="selectedCourier = c.code"
                  >
                    {{ c.name }}
                  </button>
                </div>
              </div>

              <!-- Services list -->
              <div
                v-if="loadingShipping"
                class="flex items-center gap-2 py-4 justify-center text-xs text-gray-400 font-bold"
              >
                <UIcon
                  name="i-heroicons-arrow-path"
                  class="w-4 h-4 animate-spin text-primary-600"
                />
                <span>Menghitung estimasi ongkir RajaOngkir...</span>
              </div>

              <div
                v-else-if="selectedCourier && shippingServices.length > 0"
                class="space-y-3"
              >
                <span class="block text-xs font-black text-gray-700 mb-2"
                  >Layanan Pengiriman Tersedia</span
                >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                  <div
                    v-for="service in shippingServices"
                    :key="service.service"
                    class="p-4 rounded-xl border cursor-pointer flex items-center justify-between transition-all duration-200"
                    :class="
                      selectedService?.service === service.service
                        ? 'border-primary-500 bg-primary-50/10'
                        : 'border-gray-100 hover:border-gray-200 hover:bg-gray-50/30'
                    "
                    @click="selectService(service)"
                  >
                    <div>
                      <p class="text-xs font-extrabold text-gray-900 uppercase">
                        {{ service.courier }} - {{ service.service }}
                      </p>
                      <p class="text-[10px] text-gray-400 font-semibold mt-0.5">
                        Estimasi tiba:
                        {{
                          service.etd
                            ? service.etd.replace(/\bday\b/gi, 'hari')
                            : '-'
                        }}
                      </p>
                    </div>
                    <span class="text-xs font-black text-primary-700">{{
                      format(service.cost)
                    }}</span>
                  </div>
                </div>
              </div>

              <div
                v-else-if="selectedCourier"
                class="text-center py-4 text-xs text-gray-400 font-semibold"
              >
                Layanan pengantar kurir terpilih tidak ditemukan untuk rute ini.
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Order Summary, Checkout Button -->
        <div class="space-y-6">
          <div
            class="bg-linear-to-b from-primary-50/50 via-white/80 to-white/95 backdrop-blur-xs border border-primary-100/40 rounded-3xl p-6 shadow-[0_4px_25px_-6px_rgba(255,116,108,0.03)] h-fit space-y-5 relative overflow-hidden"
          >

            <h2
              class="text-xs uppercase tracking-wider font-extrabold text-primary-800 border-b border-primary-100/50 pb-2.5"
            >
              Ringkasan Transaksi
            </h2>

            <!-- Items list -->
            <div class="max-h-56 overflow-y-auto space-y-3 pr-1">
              <div
                v-for="item in cart.items"
                :key="item.variantId"
                class="flex gap-3"
              >
                <img
                  :src="item.image ?? 'https://picsum.photos/seed/cart/100/100'"
                  :alt="item.productName"
                  class="w-12 h-12 object-cover rounded-lg border border-gray-100 shrink-0 bg-white"
                />
                <div class="min-w-0 flex-1">
                  <p class="text-xs font-extrabold text-gray-900 truncate">
                    {{ item.productName }}
                  </p>
                  <p class="text-[10px] text-gray-400 font-semibold mt-0.5 flex flex-wrap items-center gap-1.5">
                    <span>Varian: {{ item.variantName }} (x{{ item.qty }})</span>
                    <span
                      v-if="item.fulfillmentType === 'pre_order'"
                      class="px-1 py-0.5 bg-orange-100 text-orange-700 text-[8px] font-black rounded"
                    >
                      DP
                    </span>
                  </p>
                  <div class="flex items-baseline gap-1.5 mt-1">
                    <p class="text-xs font-black text-gray-700">
                      {{ format(item.price * item.qty) }}
                    </p>
                    <span
                      v-if="item.fulfillmentType === 'pre_order' && item.originalPrice"
                      class="text-[9px] text-gray-400 font-semibold line-through"
                    >
                      {{ format(item.originalPrice * item.qty) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Cost recap -->
            <div
              class="space-y-3.5 border-t border-primary-100/50 pt-4 text-xs font-semibold"
            >
              <div class="flex items-center justify-between">
                <span class="text-gray-500 font-medium">Subtotal Produk</span>
                <span class="text-gray-900 font-extrabold">{{
                  format(cart.subtotal)
                }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-gray-500 font-medium">Ongkos Kirim</span>
                <span
                  class="text-gray-900 font-extrabold"
                  v-if="selectedService"
                  >{{ format(selectedService.cost) }}</span
                >
                <span class="text-primary-700 italic text-[11px]" v-else
                  >Pilih kurir</span
                >
              </div>
              <div
                class="flex items-center justify-between border-t border-primary-150 pt-3"
              >
                <span class="text-gray-900 font-extrabold">Total Bayar</span>
                <span class="text-base font-black text-primary-800">
                  {{
                    format(
                      cart.subtotal +
                        (selectedService ? selectedService.cost : 0),
                    )
                  }}
                </span>
              </div>
            </div>

            <div class="pt-2">
              <UButton
                block
                color="primary"
                size="lg"
                class="font-extrabold py-3.5 rounded-full cursor-pointer shadow-lg shadow-primary-600/20 hover:scale-[1.01] transition-transform duration-300"
                :disabled="isSubmitDisabled"
                :loading="loading"
                @click="handleCheckout"
              >
                Bayar Sekarang
              </UButton>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Member Address Creation Slideover -->
    <USlideover v-model:open="addressSlideoverOpen" title="Tambah Alamat Baru">
      <template #content>
        <div
          class="p-6 space-y-5 h-full flex flex-col justify-between bg-white overflow-y-auto"
        >
          <div class="space-y-4">
            <div
              class="flex items-center justify-between pb-3 border-b border-gray-100 mb-3"
            >
              <h2 class="text-sm font-black text-gray-900">
                Alamat Pengiriman Baru
              </h2>
              <UButton
                icon="i-heroicons-x-mark"
                color="neutral"
                variant="ghost"
                aria-label="Tutup formulir alamat"
                class="hover:bg-gray-100 rounded-lg"
                @click="() => { addressSlideoverOpen = false }"
              />
            </div>

            <UFormField label="Label Alamat (Contoh: Rumah, Kantor)">
              <UInput
                v-model="addressForm.label"
                placeholder="Masukkan label alamat"
                class="w-full"
              />
            </UFormField>

            <UFormField label="Nama Penerima" required>
              <UInput
                v-model="addressForm.recipientName"
                placeholder="Masukkan nama penerima paket"
                class="w-full"
              />
            </UFormField>

            <UFormField label="Nomor Telepon" required>
              <UInput
                v-model="addressForm.phone"
                placeholder="Contoh: 0812345678"
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
                placeholder="Masukkan alamat lengkap (jalan, nomor, RT/RW)"
                class="w-full"
              />
            </UFormField>

            <div class="pt-2">
              <UCheckbox
                v-model="addressForm.isDefault"
                label="Atur sebagai alamat utama/default"
              />
            </div>
          </div>

          <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
            <UButton
              color="neutral"
              variant="outline"
              class="flex-1 justify-center rounded-xl cursor-pointer"
              @click="() => { addressSlideoverOpen = false }"
              >Batal</UButton
            >
            <UButton
              color="primary"
              class="flex-1 justify-center rounded-xl font-bold cursor-pointer"
              @click="saveAddress"
              >Simpan Alamat</UButton
            >
          </div>
        </div>
      </template>
    </USlideover>
  </div>
</template>
