<script setup lang="ts">
import { addressService } from "~/services/addressService";
import type { Address } from "~/types";

definePageMeta({ middleware: "auth" });

const toast = useToast();

const tabs = [
  { label: "Profil", to: "/akun/profil" },
  { label: "Alamat", to: "/akun/alamat" },
];

// ── Address list ──────────────────────────────────────────
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

// ── Modal state ───────────────────────────────────────────
const modalOpen = ref(false);
const modalMode = ref<"add" | "edit">("add");
const editingId = ref<number | null>(null);
const submitting = ref(false);

// Province / city / district data (loaded once, cached in ref)
const provinces = ref<any[]>([]);
const cities = ref<any[]>([]);
const districts = ref<any[]>([]);
const loadingProvinces = ref(false);

// Suppress cascade watchers during edit pre-fill to avoid race conditions
const isPreFilling = ref(false);

// ── Form state ────────────────────────────────────────────
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

  // Suppress cascade watchers while pre-filling — otherwise setting
  // provinceId triggers the watcher which clears cityId before we can set it.
  isPreFilling.value = true;

  form.label = address.label ?? "";
  form.recipientName = address.recipientName;
  form.phone = address.phone;
  form.postalCode = address.postalCode;
  form.fullAddress = address.fullAddress;
  form.isDefault = address.isDefault;

  // Re-hydrate cascade: find province by name → load cities → find city → load districts → find district
  // Province/city names are matched by string equality — safe because both
  // come from the same RajaOngkir cache (stored on create, re-fetched here).
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

// ── Cascade watchers ──────────────────────────────────────
// isPreFilling suppresses these watchers during openEdit pre-fill to avoid
// the watchers resetting city/district while we're explicitly setting them.
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

// ── Submit add / edit ─────────────────────────────────────
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

// ── Set default ───────────────────────────────────────────
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

// ── Delete ────────────────────────────────────────────────
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
</script>

<template>
  <div class="min-h-screen bg-gray-50 py-10">
    <div class="max-w-2xl mx-auto px-4">
      <!-- Tab nav -->
      <div class="flex gap-2 mb-8 border-b border-gray-200">
        <NuxtLink
          v-for="tab in tabs"
          :key="tab.to"
          :to="tab.to"
          class="px-4 py-2 text-sm font-semibold transition-colors"
          :class="
            $route.path === tab.to
              ? 'text-primary-600 border-b-2 border-primary-600'
              : 'text-gray-500 hover:text-gray-700'
          "
        >
          {{ tab.label }}
        </NuxtLink>
      </div>

      <!-- Header row -->
      <div class="flex items-center justify-between mb-4">
        <h2 class="font-bold text-gray-900 text-lg">Alamat Tersimpan</h2>
        <UButton
          icon="i-heroicons-plus"
          color="primary"
          class="cursor-pointer"
          @click="openAdd"
        >
          Tambah Alamat
        </UButton>
      </div>

      <!-- Loading -->
      <div v-if="loadingList" class="text-center py-16 text-gray-400">
        Memuat alamat...
      </div>

      <!-- Empty -->
      <div
        v-else-if="addresses.length === 0"
        class="text-center py-16 text-gray-400"
      >
        Belum ada alamat tersimpan.
      </div>

      <!-- List -->
      <div v-else class="space-y-3">
        <UCard v-for="address in addresses" :key="address.id">
          <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <span class="font-semibold text-gray-900 text-sm">
                  {{ address.label || "Alamat" }}
                </span>
                <UBadge v-if="address.isDefault" color="primary" size="xs">
                  Utama
                </UBadge>
              </div>
              <p class="text-sm text-gray-700">
                {{ address.recipientName }} · {{ address.phone }}
              </p>
              <p class="text-sm text-gray-500 mt-0.5">
                {{ address.district }}, {{ address.city }},
                {{ address.province }} {{ address.postalCode }}
              </p>
              <p class="text-xs text-gray-400 mt-0.5 line-clamp-2">
                {{ address.fullAddress }}
              </p>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-2 shrink-0">
              <UButton
                v-if="!address.isDefault"
                size="xs"
                variant="outline"
                color="primary"
                class="cursor-pointer"
                @click="setDefault(address)"
              >
                Jadikan Utama
              </UButton>
              <UButton
                size="xs"
                variant="outline"
                color="neutral"
                class="cursor-pointer"
                @click="openEdit(address)"
              >
                Edit
              </UButton>
              <template v-if="deleteConfirmId !== address.id">
                <UButton
                  size="xs"
                  variant="outline"
                  color="error"
                  class="cursor-pointer"
                  @click="() => { deleteConfirmId = address.id }"
                >
                  Hapus
                </UButton>
              </template>
              <template v-else>
                <UButton
                  size="xs"
                  color="error"
                  :loading="deleting"
                  class="cursor-pointer"
                  @click="confirmDelete(address.id)"
                >
                  Ya, Hapus
                </UButton>
                <UButton
                  size="xs"
                  variant="ghost"
                  color="neutral"
                  class="cursor-pointer"
                  @click="() => { deleteConfirmId = null }"
                >
                  Batal
                </UButton>
              </template>
            </div>
          </div>
        </UCard>
      </div>

      <!-- Add / Edit Modal -->
      <UModal
        v-model:open="modalOpen"
        :title="modalMode === 'add' ? 'Tambah Alamat' : 'Edit Alamat'"
      >
        <template #body>
          <div class="space-y-3">
            <UFormField label="Label (opsional)" hint="Contoh: Rumah, Kantor">
              <UInput
                v-model="form.label"
                placeholder="Rumah"
                class="w-full"
              />
            </UFormField>
            <UFormField label="Nama Penerima" required>
              <UInput
                v-model="form.recipientName"
                placeholder="Nama lengkap penerima"
                class="w-full"
              />
            </UFormField>
            <UFormField label="Telepon" required>
              <UInput
                v-model="form.phone"
                placeholder="08xxxxxxxxxx"
                class="w-full"
              />
            </UFormField>
            <UFormField label="Provinsi" required>
              <USelect
                v-model="form.provinceId"
                :items="provinces.map((p) => ({ label: p.name, value: p.id }))"
                placeholder="Pilih provinsi"
                class="w-full"
                :loading="loadingProvinces"
              />
            </UFormField>
            <UFormField label="Kota/Kabupaten" required>
              <USelect
                v-model="form.cityId"
                :items="cities.map((c) => ({ label: c.name, value: c.id }))"
                placeholder="Pilih kota"
                class="w-full"
                :disabled="!form.provinceId"
              />
            </UFormField>
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
                placeholder="12345"
                class="w-full"
              />
            </UFormField>
            <UFormField label="Alamat Lengkap" required>
              <UTextarea
                v-model="form.fullAddress"
                placeholder="Nama jalan, nomor, RT/RW, dll."
                class="w-full"
                :rows="3"
              />
            </UFormField>
            <UCheckbox v-model="form.isDefault" label="Jadikan alamat utama" />
          </div>
        </template>
        <template #footer>
          <div class="flex gap-2 justify-end">
            <UButton
              variant="outline"
              color="neutral"
              class="cursor-pointer"
              @click="() => { modalOpen = false }"
            >
              Batal
            </UButton>
            <UButton
              color="primary"
              :loading="submitting"
              :disabled="!formValid"
              class="cursor-pointer"
              @click="submitForm"
            >
              {{ modalMode === "add" ? "Tambah" : "Simpan" }}
            </UButton>
          </div>
        </template>
      </UModal>
    </div>
  </div>
</template>
