<script setup lang="ts">
import { addressService } from "~/services/addressService";
import { useAuthStore } from "~/store/auth";
import type { Address } from "~/types";

definePageMeta({ middleware: "auth" });

const authStore = useAuthStore();
const toast = useToast();

useSeoMeta({ title: "Alamat Saya", robots: "noindex, nofollow" });

const tabs = [
  {
    label: "Informasi Profil",
    to: "/akun/profil",
    icon: "i-heroicons-user-circle",
  },
  {
    label: "Alamat Pengiriman",
    to: "/akun/alamat",
    icon: "i-heroicons-map-pin",
  },
];

// Address list
const addresses = ref<Address[]>([]);
const loadingList = ref(true);

async function loadAddresses() {
  loadingList.value = true;
  try {
    addresses.value = await addressService.getAddresses();
  } catch {
    toast.add({ title: "Gagal memuat alamat.", color: "error" });
  } finally {
    loadingList.value = false;
  }
}

onMounted(loadAddresses);

// Modal state
const modalOpen = ref(false);
const modalMode = ref<"add" | "edit">("add");
const editingId = ref<number | null>(null);
const submitting = ref(false);

// Province / city / district data
const provinces = ref<any[]>([]);
const cities = ref<any[]>([]);
const districts = ref<any[]>([]);
const loadingProvinces = ref(false);

const isPreFilling = ref(false);

// Form state
const form = reactive({
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

function resetForm() {
  form.label = "";
  form.recipientName = "";
  form.phone = "";
  form.provinceId = null;
  form.provinceName = "";
  form.cityId = null;
  form.cityName = "";
  form.districtId = null;
  form.districtName = "";
  form.postalCode = "";
  form.fullAddress = "";
  form.isDefault = false;
  cities.value = [];
  districts.value = [];
}

async function ensureProvinces() {
  if (provinces.value.length > 0) return;
  loadingProvinces.value = true;
  provinces.value = await addressService.getProvinces();
  loadingProvinces.value = false;
}

async function openAdd() {
  modalMode.value = "add";
  editingId.value = null;
  resetForm();
  await ensureProvinces();
  modalOpen.value = true;
}

async function openEdit(address: Address) {
  modalMode.value = "edit";
  editingId.value = address.id;
  resetForm();
  await ensureProvinces();

  isPreFilling.value = true;

  form.label = address.label ?? "";
  form.recipientName = address.recipientName;
  form.phone = address.phone;
  form.postalCode = address.postalCode;
  form.fullAddress = address.fullAddress;
  form.isDefault = address.isDefault;

  const prov = provinces.value.find((p) => p.name === address.province);
  if (prov) {
    form.provinceName = prov.name;
    form.provinceId = prov.id;
    cities.value = await addressService.getCities(prov.id);
    const city = cities.value.find((c) => c.name === address.city);
    if (city) {
      form.cityName = city.name;
      form.cityId = city.id;
      districts.value = await addressService.getDistricts(city.id);
      const dist = districts.value.find((d) => d.name === address.district);
      if (dist) {
        form.districtName = dist.name;
        form.districtId = dist.id;
      }
    }
  }

  isPreFilling.value = false;
  modalOpen.value = true;
}

// Cascade watchers
watch(
  () => form.provinceId,
  async (newVal) => {
    if (isPreFilling.value) return;
    cities.value = [];
    districts.value = [];
    form.cityId = null;
    form.cityName = "";
    form.districtId = null;
    form.districtName = "";
    if (newVal) {
      const prov = provinces.value.find((p) => p.id === newVal);
      form.provinceName = prov ? prov.name : "";
      cities.value = await addressService.getCities(newVal);
    }
  },
);

watch(
  () => form.cityId,
  async (newVal) => {
    if (isPreFilling.value) return;
    districts.value = [];
    form.districtId = null;
    form.districtName = "";
    if (newVal) {
      const city = cities.value.find((c) => c.id === newVal);
      form.cityName = city ? city.name : "";
      districts.value = await addressService.getDistricts(newVal);
    }
  },
);

watch(
  () => form.districtId,
  (newVal) => {
    if (isPreFilling.value) return;
    if (newVal) {
      const dist = districts.value.find((d) => d.id === newVal);
      form.districtName = dist ? dist.name : "";
    }
  },
);

const formValid = computed(
  () =>
    form.recipientName.trim() &&
    form.phone.trim() &&
    form.provinceId !== null &&
    form.cityId !== null &&
    form.districtId !== null &&
    form.postalCode.trim() &&
    form.fullAddress.trim(),
);

// Submit add / edit
async function submitForm() {
  if (!formValid.value) return;
  submitting.value = true;
  try {
    const payload: Partial<Address> = {
      label: form.label || undefined,
      recipientName: form.recipientName,
      phone: form.phone,
      province: form.provinceName,
      city: form.cityName,
      district: form.districtName,
      postalCode: form.postalCode,
      fullAddress: form.fullAddress,
      isDefault: form.isDefault,
      rajaongkirDistrictId: form.districtId!,
    };
    if (modalMode.value === "add") {
      await addressService.createAddress(payload);
      toast.add({ title: "Alamat berhasil ditambahkan.", color: "primary" });
    } else if (editingId.value !== null) {
      await addressService.updateAddress(editingId.value, payload);
      toast.add({ title: "Alamat berhasil diperbarui.", color: "primary" });
    }
    modalOpen.value = false;
    await loadAddresses();
  } catch (err: any) {
    toast.add({
      title: "Gagal menyimpan alamat.",
      description: err?.data?.message ?? "Terjadi kesalahan.",
      color: "error",
    });
  } finally {
    submitting.value = false;
  }
}

// Set default
async function setDefault(address: Address) {
  try {
    await addressService.updateAddress(address.id, {
      label: address.label,
      recipientName: address.recipientName,
      phone: address.phone,
      province: address.province,
      city: address.city,
      district: address.district,
      postalCode: address.postalCode,
      fullAddress: address.fullAddress,
      isDefault: true,
      rajaongkirDistrictId: address.rajaongkirDistrictId ?? undefined,
    });
    await loadAddresses();
    toast.add({ title: "Alamat utama berhasil diubah.", color: "primary" });
  } catch {
    toast.add({ title: "Gagal mengubah alamat utama.", color: "error" });
  }
}

// Delete
const deleteConfirmId = ref<number | null>(null);
const deleting = ref(false);

async function confirmDelete(id: number) {
  deleting.value = true;
  try {
    await addressService.deleteAddress(id);
    deleteConfirmId.value = null;
    await loadAddresses();
    toast.add({ title: "Alamat berhasil dihapus.", color: "primary" });
  } catch {
    toast.add({ title: "Gagal menghapus alamat.", color: "error" });
  } finally {
    deleting.value = false;
  }
}

function closeModal() {
  modalOpen.value = false;
}

function selectDeleteConfirm(id: number) {
  deleteConfirmId.value = id;
}

function cancelDeleteConfirm() {
  deleteConfirmId.value = null;
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

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 space-y-8">
      <!-- Breadcrumb & User Header Banner Card -->
      <div
        class="bg-white/90 backdrop-blur-md border border-gray-200/80 rounded-3xl p-6 sm:p-8 shadow-sm relative overflow-hidden space-y-6"
      >
        <div
          class="flex flex-col sm:flex-row sm:items-center justify-between gap-4"
        >
          <div class="flex items-center gap-4">
            <div
              class="w-16 h-16 rounded-2xl bg-linear-to-tr from-primary-600 via-primary-500 to-primary-400 text-white flex items-center justify-center font-black text-xl shadow-lg shadow-primary-500/20 shrink-0"
            >
              {{ (authStore.user?.name || "M").charAt(0).toUpperCase() }}
            </div>

            <div class="space-y-1">
              <div class="flex items-center gap-2">
                <h1
                  class="text-lg sm:text-xl font-black text-gray-900 leading-tight"
                >
                  {{ authStore.user?.name }}
                </h1>
                <span
                  class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-black rounded-full border border-emerald-200/60"
                >
                  Member Terverifikasi
                </span>
              </div>
              <p
                class="text-xs text-gray-500 font-semibold flex items-center gap-1.5"
              >
                <UIcon
                  name="i-heroicons-envelope"
                  class="w-3.5 h-3.5 text-gray-400"
                />
                {{ authStore.user?.email }}
              </p>
            </div>
          </div>

          <UButton
            icon="i-heroicons-plus"
            color="primary"
            class="w-fit rounded-xl font-extrabold text-xs px-4 py-2 cursor-pointer shadow-md shadow-primary-500/20"
            @click="openAdd"
          >
            Tambah Alamat Baru
          </UButton>
        </div>

        <!-- Account Tab Navigation Bar -->
        <div
          class="flex flex-col sm:flex-row items-start sm:items-center gap-2 border-t border-gray-100 pt-4"
        >
          <NuxtLink
            v-for="tab in tabs"
            :key="tab.to"
            :to="tab.to"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all duration-300 cursor-pointer shadow-2xs border"
            :class="
              $route.path === tab.to
                ? 'bg-primary-500 text-white border-primary-600 shadow-md shadow-primary-500/20'
                : 'bg-gray-50/80 text-gray-600 border-gray-200/80 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            <UIcon :name="tab.icon" class="w-4 h-4" />
            <span>{{ tab.label }}</span>
          </NuxtLink>
        </div>
      </div>

      <!-- Address Cards Section -->
      <div class="space-y-4">
        <!-- Loading Spinner -->
        <div
          v-if="loadingList"
          class="flex flex-col items-center justify-center py-20 bg-white/90 backdrop-blur-md rounded-3xl border border-gray-200/80 gap-3"
        >
          <UIcon
            name="i-heroicons-arrow-path"
            class="w-8 h-8 animate-spin text-primary-600"
          />
          <span class="text-xs text-gray-500 font-extrabold">
            Memuat daftar alamat tersimpan...
          </span>
        </div>

        <!-- Empty State -->
        <div
          v-else-if="addresses.length === 0"
          class="max-w-lg mx-auto bg-white/90 backdrop-blur-md border border-gray-200/80 rounded-3xl p-8 sm:p-12 shadow-xl shadow-gray-900/5 text-center space-y-4"
        >
          <div
            class="w-16 h-16 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center mx-auto shadow-xs border border-primary-100"
          >
            <UIcon name="i-heroicons-map-pin" class="w-8 h-8" />
          </div>
          <div>
            <h2 class="text-base font-extrabold text-gray-900">
              Belum Ada Alamat Tersimpan
            </h2>
            <p
              class="text-xs text-gray-500 mt-1 max-w-sm mx-auto leading-relaxed"
            >
              Tambahkan alamat pengiriman utama Anda untuk mempermudah dan
              mempercepat proses checkout.
            </p>
          </div>
          <UButton
            color="primary"
            size="lg"
            class="rounded-2xl font-black px-6 py-3 cursor-pointer shadow-md shadow-primary-500/20"
            @click="openAdd"
          >
            + Tambah Alamat Baru
          </UButton>
        </div>

        <!-- Address Cards Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="address in addresses"
            :key="address.id"
            class="bg-white/90 backdrop-blur-md border rounded-3xl p-6 shadow-xs flex flex-col justify-between transition-all duration-300 space-y-4"
            :class="
              address.isDefault
                ? 'border-primary-500 bg-primary-50/20 shadow-md shadow-primary-900/5 ring-2 ring-primary-500/20'
                : 'border-gray-200/80 hover:border-gray-300 hover:bg-gray-50/50'
            "
          >
            <div class="space-y-2">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="text-sm font-black text-gray-900">{{
                    address.label || "Alamat Saya"
                  }}</span>
                  <span
                    v-if="address.isDefault"
                    class="px-2.5 py-0.5 bg-primary-100 text-primary-700 text-[10px] font-black rounded-full border border-primary-200/60"
                  >
                    Alamat Utama
                  </span>
                </div>
              </div>

              <div class="space-y-0.5">
                <p class="text-xs font-bold text-gray-800">
                  {{ address.recipientName }}
                </p>
                <p class="text-xs text-gray-500 font-semibold">
                  {{ address.phone }}
                </p>
              </div>

              <p
                class="text-xs text-gray-600 font-normal leading-relaxed pt-2 border-t border-gray-100"
              >
                {{ address.fullAddress }}
              </p>
              <p class="text-[11px] text-gray-400 font-semibold">
                {{ address.district }}, {{ address.city }},
                {{ address.province }} - {{ address.postalCode }}
              </p>
            </div>

            <!-- Address Action Buttons -->
            <div
              class="flex items-center justify-between gap-2 pt-3 border-t border-gray-100"
            >
              <div>
                <UButton
                  v-if="!address.isDefault"
                  size="xs"
                  variant="ghost"
                  color="primary"
                  class="font-extrabold cursor-pointer rounded-xl hover:bg-primary-50"
                  @click="setDefault(address)"
                >
                  Jadikan Utama
                </UButton>
              </div>

              <div class="flex items-center gap-2">
                <UButton
                  size="xs"
                  variant="outline"
                  color="neutral"
                  icon="i-heroicons-pencil-square"
                  class="rounded-xl font-bold cursor-pointer"
                  @click="openEdit(address)"
                >
                  Edit
                </UButton>

                <template v-if="deleteConfirmId !== address.id">
                  <UButton
                    size="xs"
                    variant="outline"
                    color="error"
                    icon="i-heroicons-trash"
                    class="rounded-xl font-bold cursor-pointer hover:bg-red-50"
                    @click="selectDeleteConfirm(address.id)"
                  >
                    Hapus
                  </UButton>
                </template>
                <template v-else>
                  <UButton
                    size="xs"
                    color="error"
                    class="rounded-xl font-extrabold cursor-pointer"
                    :loading="deleting"
                    @click="confirmDelete(address.id)"
                  >
                    Ya, Hapus
                  </UButton>
                  <UButton
                    size="xs"
                    variant="ghost"
                    color="neutral"
                    class="rounded-xl font-bold cursor-pointer"
                    @click="cancelDeleteConfirm"
                  >
                    Batal
                  </UButton>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Add / Edit Modal -->
      <UModal
        v-model:open="modalOpen"
        class="max-w-2xl"
        :title="
          modalMode === 'add' ? 'Tambah Alamat Baru' : 'Edit Alamat Pengiriman'
        "
      >
        <template #body>
          <div class="space-y-4 py-1">
            <UFormField label="Label Alamat (Contoh: Rumah, Kantor)">
              <UInput
                v-model="form.label"
                placeholder="Rumah Saya"
                class="w-full"
              />
            </UFormField>

            <UFormField label="Nama Penerima" required>
              <UInput
                v-model="form.recipientName"
                placeholder="Nama penerima paket"
                class="w-full"
              />
            </UFormField>

            <UFormField label="Nomor Telepon" required>
              <UInput
                v-model="form.phone"
                placeholder="0812345678"
                class="w-full"
              />
            </UFormField>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <UFormField label="Provinsi" required>
                <USelect
                  v-model="form.provinceId"
                  :items="
                    provinces.map((p) => ({ label: p.name, value: p.id }))
                  "
                  placeholder="Pilih provinsi"
                  class="w-full"
                  :loading="loadingProvinces"
                />
              </UFormField>
              <UFormField label="Kota / Kabupaten" required>
                <USelect
                  v-model="form.cityId"
                  :items="cities.map((c) => ({ label: c.name, value: c.id }))"
                  placeholder="Pilih kota"
                  class="w-full"
                  :disabled="!form.provinceId"
                />
              </UFormField>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <UFormField label="Kecamatan" required>
                <USelect
                  v-model="form.districtId"
                  :items="
                    districts.map((d) => ({ label: d.name, value: d.id }))
                  "
                  placeholder="Pilih kecamatan"
                  class="w-full"
                  :disabled="!form.cityId"
                />
              </UFormField>
              <UFormField label="Kode Pos" required>
                <UInput
                  v-model="form.postalCode"
                  placeholder="Kode pos"
                  class="w-full"
                />
              </UFormField>
            </div>

            <UFormField label="Alamat Lengkap" required>
              <UTextarea
                v-model="form.fullAddress"
                placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan"
                class="w-full"
                :rows="3"
              />
            </UFormField>

            <div class="pt-1">
              <UCheckbox
                v-model="form.isDefault"
                label="Jadikan sebagai alamat utama/default"
              />
            </div>
          </div>
        </template>
        <template #footer>
          <div class="flex items-center justify-end gap-3 w-full">
            <UButton
              variant="outline"
              color="neutral"
              class="rounded-xl font-bold cursor-pointer"
              @click="closeModal"
            >
              Batal
            </UButton>
            <UButton
              color="primary"
              class="rounded-xl font-extrabold px-5 cursor-pointer"
              :loading="submitting"
              :disabled="!formValid"
              @click="submitForm"
            >
              {{ modalMode === "add" ? "Tambah Alamat" : "Simpan Perubahan" }}
            </UButton>
          </div>
        </template>
      </UModal>
    </div>
  </div>
</template>
