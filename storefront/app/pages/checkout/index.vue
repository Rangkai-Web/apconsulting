<script setup lang="ts">
import { useCartStore } from "~/store/cart";
import { useAuthStore } from "~/store/auth";
import { addressService } from "~/services/addressService";
import { orderService } from "~/services/orderService";
import { useCurrency } from "~/composables/useCurrency";
import type { Address } from "~/types";

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

// Shipping courier options
const couriersList = [
  { code: "jne", name: "JNE" },
  { code: "jnt", name: "J&T" },
  { code: "sicepat", name: "SiCepat" },
];
const selectedCourier = ref<string>("");
const shippingServices = ref<any[]>([]);
const loadingShipping = ref(false);
const selectedService = ref<any | null>(null);

// Getters
const isLoggedIn = computed(() => authStore.isLoggedIn);
const selectedAddress = computed(() => {
  if (isLoggedIn.value) {
    return addresses.value.find((a) => a.id === selectedAddressId.value) || null;
  }
  return null;
});

const isSubmitDisabled = computed(() => {
  if (cart.items.length === 0) return true;
  if (loading.value) return true;

  if (isLoggedIn.value) {
    if (!selectedAddressId.value) return true;
  } else {
    if (!guestForm.name || !guestForm.email || !guestForm.phone) return true;
    if (!guestForm.recipientName || !guestForm.recipientPhone || !guestForm.districtId) return true;
    if (!guestForm.fullAddress || !guestForm.postalCode) return true;
  }

  if (!selectedCourier.value || !selectedService.value) return true;

  return false;
});

// Watchers for cascading dropdowns - Member Address Form
watch(() => addressForm.provinceId, async (newVal) => {
  cities.value = [];
  districts.value = [];
  addressForm.cityId = null;
  addressForm.cityName = "";
  addressForm.districtId = null;
  addressForm.districtName = "";
  if (newVal) {
    const prov = provinces.value.find((p) => p.id === newVal);
    addressForm.provinceName = prov ? prov.name : "";
    cities.value = await addressService.getCities(newVal);
  }
});

watch(() => addressForm.cityId, async (newVal) => {
  districts.value = [];
  addressForm.districtId = null;
  addressForm.districtName = "";
  if (newVal) {
    const city = cities.value.find((c) => c.id === newVal);
    addressForm.cityName = city ? city.name : "";
    districts.value = await addressService.getDistricts(newVal);
  }
});

watch(() => addressForm.districtId, (newVal) => {
  if (newVal) {
    const dist = districts.value.find((d) => d.id === newVal);
    addressForm.districtName = dist ? dist.name : "";
  }
});

// Watchers for cascading dropdowns - Guest Address Form
const guestCities = ref<any[]>([]);
const guestDistricts = ref<any[]>([]);

watch(() => guestForm.provinceId, async (newVal) => {
  guestCities.value = [];
  guestDistricts.value = [];
  guestForm.cityId = null;
  guestForm.cityName = "";
  guestForm.districtId = null;
  guestForm.districtName = "";
  if (newVal) {
    const prov = provinces.value.find((p) => p.id === newVal);
    guestForm.provinceName = prov ? prov.name : "";
    guestCities.value = await addressService.getCities(newVal);
  }
});

watch(() => guestForm.cityId, async (newVal) => {
  guestDistricts.value = [];
  guestForm.districtId = null;
  guestForm.districtName = "";
  if (newVal) {
    const city = guestCities.value.find((c) => c.id === newVal);
    guestForm.cityName = city ? city.name : "";
    guestDistricts.value = await addressService.getDistricts(newVal);
  }
});

watch(() => guestForm.districtId, (newVal) => {
  if (newVal) {
    const dist = guestDistricts.value.find((d) => d.id === newVal);
    guestForm.districtName = dist ? dist.name : "";
    // Reset selected courier service as destination district changed
    selectedService.value = null;
    shippingServices.value = [];
    if (selectedCourier.value) fetchShippingCost();
  }
});

// Initial load
onMounted(async () => {
  if (cart.items.length === 0) return;

  // Load provinces for forms
  provinces.value = await addressService.getProvinces();

  if (isLoggedIn.value) {
    await loadAddresses();
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
    selectedAddressId.value = addresses.value[0].id;
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
    const services = await orderService.getShippingCost(destinationId, cart.totalWeight);
    // Filter services based on selectedCourier
    shippingServices.value = services.filter((s) => s.courier.toLowerCase() === selectedCourier.value.toLowerCase());
    
    if (shippingServices.value.length === 0) {
      toast.add({
        title: "Kurir Tidak Tersedia",
        description: "Layanan kurir ini tidak menjangkau wilayah pengiriman Anda.",
        color: "warning",
      });
    }
  } catch (error: any) {
    console.error("Gagal cek ongkir:", error);
    toast.add({
      title: "Gagal Cek Ongkir",
      description: error.data?.message || "Terjadi kesalahan koneksi saat menghitung ongkos kirim.",
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
  if (!addressForm.recipientName || !addressForm.phone || !addressForm.districtId || !addressForm.fullAddress) {
    toast.add({ title: "Validasi Gagal", description: "Mohon isi semua field alamat wajib.", color: "error" });
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
      description: error.data?.message || "Terjadi kesalahan saat menyimpan alamat.",
      color: "error",
    });
  }
}

// Dynamic Script Loader for Midtrans Snap.js
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

// Submit Checkout Order & Initiate Payment Flow
async function handleCheckout() {
  if (isSubmitDisabled.value) return;

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
    const { orderNumber, snapToken } = await orderService.createOrder(orderPayload);

    // If snapToken is a local mock token (no midtrans server key configured)
    if (snapToken.startsWith("mock-snap-token-")) {
      toast.add({
        title: "Pemesanan Berhasil (Mock Mode)",
        description: "Snap Token Midtrans kosong. Simulasi checkout berhasil.",
        color: "primary",
      });
      cart.clear();
      router.push(`/orders/${orderNumber}?mock=true`);
      return;
    }

    // Load Snap.js
    const scriptLoaded = await loadMidtransSnapScript();
    if (!scriptLoaded) {
      throw new Error("Gagal memuat modul pembayaran Midtrans.");
    }

    // Open Snap payment popup
    (window as any).snap.pay(snapToken, {
      onSuccess: function (result: any) {
        toast.add({ title: "Pembayaran Sukses", description: "Terima kasih atas pesanan Anda!", color: "primary" });
        cart.clear();
        router.push(`/orders/${orderNumber}?status=success`);
      },
      onPending: function (result: any) {
        toast.add({ title: "Pembayaran Tertunda", description: "Selesaikan pembayaran Anda segera.", color: "warning" });
        cart.clear();
        router.push(`/orders/${orderNumber}?status=pending`);
      },
      onError: function (result: any) {
        toast.add({ title: "Pembayaran Gagal", description: "Transaksi Anda gagal diproses.", color: "error" });
        router.push(`/orders/${orderNumber}?status=error`);
      },
      onClose: function () {
        toast.add({ title: "Pembayaran Ditutup", description: "Anda menutup jendela pembayaran. Selesaikan nanti.", color: "neutral" });
        cart.clear();
        router.push(`/orders/${orderNumber}`);
      },
    });

  } catch (error: any) {
    console.error("Gagal melakukan checkout:", error);
    toast.add({
      title: "Checkout Gagal",
      description: error.data?.message || "Gagal memproses transaksi Anda. Silakan coba lagi.",
      color: "error",
    });
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <div class="relative overflow-hidden bg-linear-to-b from-primary-50/15 via-white to-white min-h-screen py-10">
    <!-- Ambient Background Glows -->
    <div class="absolute top-12 -left-40 w-96 h-96 bg-primary-100/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-[400px] -right-40 w-96 h-96 bg-secondary-100/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative">
      <!-- Title Block -->
      <div class="mb-8 flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-primary-50 flex items-center justify-center text-primary-600 shadow-sm border border-primary-100">
          <UIcon name="i-heroicons-credit-card" class="w-5 h-5" />
        </div>
        <div>
          <span class="text-[10px] text-primary-600 uppercase font-extrabold tracking-wider">Penyelesaian Pesanan</span>
          <h1 class="text-xl md:text-2xl font-black text-gray-900 tracking-tight leading-none mt-0.5">Checkout</h1>
        </div>
      </div>

      <!-- Empty State Fallback -->
      <div v-if="cart.items.length === 0" class="max-w-md mx-auto bg-white/70 backdrop-blur-sm border border-gray-100 rounded-3xl p-8 shadow-sm">
        <EmptyState
          icon="i-heroicons-shopping-cart"
          title="Keranjang Belanja Kosong"
          description="Tambahkan beberapa produk skincare terbaik kami ke keranjang belanja Anda terlebih dahulu."
          action-label="Mulai Belanja"
          action-to="/katalog"
        />
      </div>

      <!-- Main Grid -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-8">
        
        <!-- Left Column: Address, Courier Options -->
        <div class="space-y-8">
          
          <!-- Step 1: Address Section -->
          <div class="bg-white border border-gray-100 rounded-3xl p-6 md:p-8 shadow-xs">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
              <div class="flex items-center gap-3">
                <span class="w-7 h-7 rounded-full bg-primary-50 text-primary-700 flex items-center justify-center text-xs font-bold ring-4 ring-primary-50/50">1</span>
                <h2 class="text-base font-extrabold text-gray-900">Alamat Pengiriman</h2>
              </div>
              <UButton
                v-if="isLoggedIn"
                icon="i-heroicons-plus"
                color="primary"
                variant="ghost"
                class="rounded-xl font-bold cursor-pointer"
                @click="addressSlideoverOpen = true"
              >
                Tambah Alamat
              </UButton>
            </div>

            <!-- Member View: Address Selection -->
            <div v-if="isLoggedIn" class="space-y-4">
              <div v-if="addresses.length === 0" class="text-center py-8 bg-gray-55/30 rounded-2xl border border-dashed border-gray-200">
                <UIcon name="i-heroicons-map-pin" class="w-8 h-8 text-gray-300 mx-auto mb-2" />
                <p class="text-xs text-gray-400 font-semibold mb-3">Anda belum memiliki alamat tersimpan.</p>
                <UButton color="primary" variant="solid" class="rounded-xl font-semibold cursor-pointer" @click="addressSlideoverOpen = true">
                  Tambah Alamat Baru
                </UButton>
              </div>

              <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                  v-for="addr in addresses"
                  :key="addr.id"
                  class="relative p-5 rounded-2xl border cursor-pointer transition-all duration-200"
                  :class="selectedAddressId === addr.id
                    ? 'border-primary-500 bg-primary-50/10 shadow-md shadow-primary-950/5'
                    : 'border-gray-100 hover:border-gray-250 hover:bg-gray-50/20'"
                  @click="selectedAddressId = addr.id"
                >
                  <!-- Badge default -->
                  <div class="flex items-center justify-between mb-3.5">
                    <span class="text-xs font-black text-gray-900 leading-none">{{ addr.label }}</span>
                    <span v-if="addr.isDefault" class="px-2 py-0.5 bg-primary-100 text-[9px] text-primary-700 font-bold rounded-full">Utama</span>
                  </div>

                  <p class="text-xs font-bold text-gray-800">{{ addr.recipientName }}</p>
                  <p class="text-xs text-gray-400 font-medium mt-0.5">{{ addr.phone }}</p>
                  <p class="text-xs text-gray-500 font-medium mt-2.5 line-clamp-2">{{ addr.fullAddress }}</p>
                  <p class="text-xs text-gray-400 font-semibold mt-1">
                    {{ addr.district }}, {{ addr.city }}, {{ addr.province }} - {{ addr.postalCode }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Guest View: Details & Shipping Address Form -->
            <div v-else class="space-y-6">
              <!-- Guest Info -->
              <div class="bg-primary-50/10 border border-primary-100/50 rounded-2xl p-5 space-y-4">
                <h3 class="text-xs uppercase font-extrabold tracking-wider text-primary-800">Informasi Pemesan (Guest)</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <UFormField label="Nama Lengkap" required>
                    <UInput v-model="guestForm.name" placeholder="Masukkan nama lengkap Anda" class="w-full" />
                  </UFormField>
                  <UFormField label="Email" required>
                    <UInput v-model="guestForm.email" type="email" placeholder="Masukkan email aktif" class="w-full" />
                  </UFormField>
                  <UFormField label="Nomor Telepon" required>
                    <UInput v-model="guestForm.phone" placeholder="Contoh: 0812345678" class="w-full" />
                  </UFormField>
                </div>
              </div>

              <!-- Shipping Address Info -->
              <div class="space-y-4 pt-2">
                <h3 class="text-xs uppercase font-extrabold tracking-wider text-primary-800">Alamat Kiriman</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <UFormField label="Nama Penerima" required>
                    <UInput v-model="guestForm.recipientName" placeholder="Nama penerima paket" class="w-full" />
                  </UFormField>
                  <UFormField label="No. Telp Penerima" required>
                    <UInput v-model="guestForm.recipientPhone" placeholder="No. telp penerima paket" class="w-full" />
                  </UFormField>
                </div>

                <!-- Dropdowns wilayah cascading -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <UFormField label="Provinsi" required>
                    <USelect
                      v-model="guestForm.provinceId"
                      :items="provinces"
                      placeholder="Pilih Provinsi"
                      class="w-full"
                      item-attribute="name"
                      value-attribute="id"
                    />
                  </UFormField>
                  <UFormField label="Kota / Kabupaten" required>
                    <USelect
                      v-model="guestForm.cityId"
                      :items="guestCities"
                      placeholder="Pilih Kota"
                      class="w-full"
                      :disabled="!guestForm.provinceId"
                      item-attribute="name"
                      value-attribute="id"
                    />
                  </UFormField>
                  <UFormField label="Kecamatan" required>
                    <USelect
                      v-model="guestForm.districtId"
                      :items="guestDistricts"
                      placeholder="Pilih Kecamatan"
                      class="w-full"
                      :disabled="!guestForm.cityId"
                      item-attribute="name"
                      value-attribute="id"
                    />
                  </UFormField>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-[1fr_160px] gap-4">
                  <UFormField label="Alamat Lengkap" required>
                    <UTextarea v-model="guestForm.fullAddress" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan" class="w-full" />
                  </UFormField>
                  <UFormField label="Kode Pos" required>
                    <UInput v-model="guestForm.postalCode" placeholder="Kode pos" class="w-full" />
                  </UFormField>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 2: Shipping Options Section -->
          <div class="bg-white border border-gray-100 rounded-3xl p-6 md:p-8 shadow-xs">
            <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
              <span class="w-7 h-7 rounded-full bg-primary-50 text-primary-700 flex items-center justify-center text-xs font-bold ring-4 ring-primary-50/50">2</span>
              <h2 class="text-base font-extrabold text-gray-900">Kurir & Paket Pengiriman</h2>
            </div>

            <!-- Wait for destination area to be chosen -->
            <div v-if="isLoggedIn ? !selectedAddressId : !guestForm.districtId" class="text-center py-6 text-gray-400 text-xs font-semibold">
              Silakan pilih/isi alamat pengiriman terlebih dahulu untuk menghitung ongkos kirim.
            </div>

            <div v-else class="space-y-6">
              <!-- Select courier brand -->
              <div>
                <span class="block text-xs font-black text-gray-700 mb-3">Pilih Kurir Eksplisit</span>
                <div class="flex items-center gap-3">
                  <button
                    v-for="c in couriersList"
                    :key="c.code"
                    type="button"
                    class="px-5 py-3 rounded-xl border font-bold text-xs transition-all duration-200 cursor-pointer"
                    :class="selectedCourier === c.code
                      ? 'border-primary-600 bg-primary-50/20 text-primary-700 shadow-sm'
                      : 'border-gray-150 hover:bg-gray-50/50 text-gray-600'"
                    @click="selectedCourier = c.code"
                  >
                    {{ c.name }}
                  </button>
                </div>
              </div>

              <!-- Services list -->
              <div v-if="loadingShipping" class="flex items-center gap-2 py-4 justify-center text-xs text-gray-400 font-bold">
                <UIcon name="i-heroicons-arrow-path" class="w-4 h-4 animate-spin text-primary-600" />
                <span>Menghitung estimasi ongkir RajaOngkir...</span>
              </div>

              <div v-else-if="selectedCourier && shippingServices.length > 0" class="space-y-3">
                <span class="block text-xs font-black text-gray-700 mb-2">Layanan Pengiriman Tersedia</span>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                  <div
                    v-for="service in shippingServices"
                    :key="service.service"
                    class="p-4 rounded-xl border cursor-pointer flex items-center justify-between transition-all duration-200"
                    :class="selectedService?.service === service.service
                      ? 'border-primary-500 bg-primary-50/10'
                      : 'border-gray-100 hover:border-gray-200 hover:bg-gray-50/30'"
                    @click="selectService(service)"
                  >
                    <div>
                      <p class="text-xs font-extrabold text-gray-900 uppercase">
                        {{ service.courier }} - {{ service.service }}
                      </p>
                      <p class="text-[10px] text-gray-400 font-semibold mt-0.5">Estimasi tiba: {{ service.etd }} hari</p>
                    </div>
                    <span class="text-xs font-black text-primary-700">{{ format(service.cost) }}</span>
                  </div>
                </div>
              </div>

              <div v-else-if="selectedCourier" class="text-center py-4 text-xs text-gray-400 font-semibold">
                Layanan pengantar kurir terpilih tidak ditemukan untuk rute ini.
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Order Summary, Checkout Button -->
        <div class="space-y-6">
          <div class="bg-linear-to-b from-primary-50/40 to-white/90 border border-primary-100/60 rounded-3xl p-6 shadow-md shadow-primary-900/5 h-fit space-y-5 relative overflow-hidden">
            <UIcon name="i-lucide-leaf" class="absolute -bottom-8 -right-8 w-28 h-28 text-primary-500/5 -rotate-12 pointer-events-none" />

            <h2 class="text-xs uppercase tracking-wider font-extrabold text-primary-800 border-b border-primary-100/50 pb-2.5">
              Ringkasan Transaksi
            </h2>

            <!-- Items list -->
            <div class="max-h-56 overflow-y-auto space-y-3 pr-1">
              <div v-for="item in cart.items" :key="item.variantId" class="flex gap-3">
                <img :src="item.image ?? 'https://picsum.photos/seed/cart/100/100'" :alt="item.productName" class="w-12 h-12 object-cover rounded-lg border border-gray-100 shrink-0 bg-white" />
                <div class="min-w-0 flex-1">
                  <p class="text-xs font-extrabold text-gray-900 truncate">{{ item.productName }}</p>
                  <p class="text-[10px] text-gray-400 font-semibold mt-0.5">Varian: {{ item.variantName }} (x{{ item.qty }})</p>
                  <p class="text-xs font-black text-gray-700 mt-1">{{ format(item.price * item.qty) }}</p>
                </div>
              </div>
            </div>

            <!-- Cost recap -->
            <div class="space-y-3.5 border-t border-primary-100/50 pt-4 text-xs font-semibold">
              <div class="flex items-center justify-between">
                <span class="text-gray-500 font-medium">Subtotal Produk</span>
                <span class="text-gray-900 font-extrabold">{{ format(cart.subtotal) }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-gray-500 font-medium">Ongkos Kirim</span>
                <span class="text-gray-900 font-extrabold" v-if="selectedService">{{ format(selectedService.cost) }}</span>
                <span class="text-primary-700 italic text-[11px]" v-else>Pilih kurir</span>
              </div>
              <div class="flex items-center justify-between border-t border-primary-150 pt-3">
                <span class="text-gray-900 font-extrabold">Total Bayar</span>
                <span class="text-base font-black text-primary-800">
                  {{ format(cart.subtotal + (selectedService ? selectedService.cost : 0)) }}
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
        <div class="p-6 space-y-5 h-full flex flex-col justify-between bg-white overflow-y-auto">
          <div class="space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-3">
              <h2 class="text-sm font-black text-gray-900">Alamat Pengiriman Baru</h2>
              <UButton icon="i-heroicons-x-mark" color="neutral" variant="ghost" class="hover:bg-gray-100 rounded-lg" @click="addressSlideoverOpen = false" />
            </div>

            <UFormField label="Label Alamat (Contoh: Rumah, Kantor)">
              <UInput v-model="addressForm.label" placeholder="Masukkan label alamat" class="w-full" />
            </UFormField>

            <UFormField label="Nama Penerima" required>
              <UInput v-model="addressForm.recipientName" placeholder="Masukkan nama penerima paket" class="w-full" />
            </UFormField>

            <UFormField label="Nomor Telepon" required>
              <UInput v-model="addressForm.phone" placeholder="Contoh: 0812345678" class="w-full" />
            </UFormField>

            <!-- Dropdown cascading wilayah untuk member -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <UFormField label="Provinsi" required>
                <USelect
                  v-model="addressForm.provinceId"
                  :items="provinces"
                  placeholder="Pilih Provinsi"
                  class="w-full"
                  item-attribute="name"
                  value-attribute="id"
                />
              </UFormField>
              <UFormField label="Kota / Kabupaten" required>
                <USelect
                  v-model="addressForm.cityId"
                  :items="cities"
                  placeholder="Pilih Kota"
                  class="w-full"
                  :disabled="!addressForm.provinceId"
                  item-attribute="name"
                  value-attribute="id"
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
                  item-attribute="name"
                  value-attribute="id"
                />
              </UFormField>
              <UFormField label="Kode Pos" required>
                <UInput v-model="addressForm.postalCode" placeholder="Kode pos" class="w-full" />
              </UFormField>
            </div>

            <UFormField label="Alamat Lengkap" required>
              <UTextarea v-model="addressForm.fullAddress" placeholder="Masukkan alamat lengkap (jalan, nomor, RT/RW)" class="w-full" />
            </UFormField>

            <div class="pt-2">
              <UCheckbox v-model="addressForm.isDefault" label="Atur sebagai alamat utama/default" />
            </div>
          </div>

          <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
            <UButton color="neutral" variant="outline" class="flex-1 rounded-xl cursor-pointer" @click="addressSlideoverOpen = false">Batal</UButton>
            <UButton color="primary" class="flex-1 rounded-xl font-bold cursor-pointer" @click="saveAddress">Simpan Alamat</UButton>
          </div>
        </div>
      </template>
    </USlideover>
  </div>
</template>
