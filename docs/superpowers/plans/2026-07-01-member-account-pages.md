# Member Account Pages (Profil + Alamat) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build two member-only account pages (`/akun/profil` and `/akun/alamat`) for managing profile info, password, and saved addresses, wired to the existing Laravel API.

**Architecture:** Two separate Nuxt 4 SPA page files under `storefront/app/pages/akun/`, both protected by the existing `auth` middleware. A new `profileService.ts` handles profile/password API calls following the same `$fetch` + `getHeaders()` pattern as `addressService.ts`. The auth store gets one new action (`updateProfile`) to sync in-memory user state after a profile update. The `AppHeader` dropdown and mobile nav gain two new links. `addressService.ts` is used as-is — it already has full CRUD and cascade methods.

**Tech Stack:** Nuxt 4.4.x SPA (`ssr: false`), Nuxt UI v4 (`UCard`, `UButton`, `UInput`, `UTextarea`, `USelect`, `UCheckbox`, `UBadge`, `UFormField`, `UModal`), Pinia, TypeScript. No new dependencies.

## Global Constraints

- Storefront root: `storefront/app/`. All paths below are relative to that root.
- `definePageMeta({ middleware: 'auth' })` on every page under `/akun/` — redirects unauthenticated users to `/login?redirect=...`.
- API base URL from `useRuntimeConfig().public.apiBase`. Auth token from `useAuthStore().token` as `Authorization: Bearer <token>` header — same pattern as `services/addressService.ts`.
- No new npm packages. No Vitest tests for Vue pages — existing codebase has none; `tests/health.test.ts` is a stub only.
- Verification method for pages: start dev server (`npm run dev` from `storefront/`), navigate to page, verify behavior manually.
- Use `useToast()` for success/error feedback — same as checkout and order pages.
- No tests for Vue UI pages — follow existing project convention.

---

### Task 1: `profileService.ts` + auth store `updateProfile()` + `/akun/profil.vue`

**Files:**
- Create: `storefront/app/services/profileService.ts`
- Modify: `storefront/app/store/auth.ts` — add `updateProfile()` action
- Create: `storefront/app/pages/akun/profil.vue`

**Interfaces:**
- Consumes: `useAuthStore()` (existing: `token`, `user`, `persist()`), `useRuntimeConfig().public.apiBase` (existing)
- Produces: `profileService.getProfile()`, `profileService.updateProfile()`, `profileService.updatePassword()`, `authStore.updateProfile({ name, phone })`

- [ ] **Step 1: Create `profileService.ts`**

Create `storefront/app/services/profileService.ts`:

```typescript
import { useAuthStore } from "~/store/auth";

function apiBase(): string {
  return useRuntimeConfig().public.apiBase;
}

function getHeaders(): Record<string, string> {
  const authStore = useAuthStore();
  const headers: Record<string, string> = {
    Accept: "application/json",
    "Content-Type": "application/json",
  };
  if (authStore.token) {
    headers.Authorization = `Bearer ${authStore.token}`;
  }
  return headers;
}

export interface ProfileData {
  id: number;
  name: string;
  email: string;
  phone: string | null;
}

export const profileService = {
  async getProfile(): Promise<ProfileData> {
    const response = await $fetch<{ data: ProfileData }>("/profile", {
      baseURL: apiBase(),
      headers: getHeaders(),
    });
    return response.data;
  },

  async updateProfile(data: {
    name: string;
    phone?: string | null;
  }): Promise<ProfileData> {
    const response = await $fetch<{ data: ProfileData }>("/profile", {
      baseURL: apiBase(),
      method: "PUT",
      headers: getHeaders(),
      body: data,
    });
    return response.data;
  },

  async updatePassword(data: {
    current_password: string;
    password: string;
    password_confirmation: string;
  }): Promise<void> {
    await $fetch("/profile/password", {
      baseURL: apiBase(),
      method: "PUT",
      headers: getHeaders(),
      body: data,
    });
  },
};
```

- [ ] **Step 2: Add `updateProfile()` action to auth store**

Read `storefront/app/store/auth.ts`. In the `actions` object, add after the `logout` action:

```typescript
    updateProfile(data: { name: string; phone?: string | null }) {
      if (this.user) {
        this.user.name = data.name;
        this.user.phone = data.phone ?? null;
        this.persist();
      }
    },
```

The full `actions` block after modification:

```typescript
  actions: {
    async login(credentials: LoginCredentials) {
      try {
        const response = await authService.login(credentials);
        this.user = response.user;
        this.token = response.token;
        this.persist();
        return response.user;
      } catch (error) {
        this.clear();
        throw error;
      }
    },

    async register(data: RegisterData) {
      try {
        const response = await authService.register(data);
        this.user = response.user;
        this.token = response.token;
        this.persist();
        return response.user;
      } catch (error) {
        this.clear();
        throw error;
      }
    },

    async logout() {
      if (this.token) {
        await authService.logout(this.token);
      }
      this.clear();
    },

    updateProfile(data: { name: string; phone?: string | null }) {
      if (this.user) {
        this.user.name = data.name;
        this.user.phone = data.phone ?? null;
        this.persist();
      }
    },

    persist() {
      if (typeof window === "undefined") return;
      if (this.user && this.token) {
        localStorage.setItem(USER_KEY, JSON.stringify(this.user));
        localStorage.setItem(TOKEN_KEY, this.token);
      } else {
        this.clear();
      }
    },

    clear() {
      this.user = null;
      this.token = null;
      if (typeof window === "undefined") return;
      localStorage.removeItem(USER_KEY);
      localStorage.removeItem(TOKEN_KEY);
    },
  },
```

- [ ] **Step 3: Create `/akun/profil.vue`**

Create `storefront/app/pages/akun/profil.vue`:

```vue
<script setup lang="ts">
import { profileService } from "~/services/profileService";
import { useAuthStore } from "~/store/auth";

definePageMeta({ middleware: "auth" });

const authStore = useAuthStore();
const toast = useToast();

const tabs = [
  { label: "Profil", to: "/akun/profil" },
  { label: "Alamat", to: "/akun/alamat" },
];

// ── Profile form ─────────────────────────────────────────
const profileForm = reactive({
  name: authStore.user?.name ?? "",
  phone: authStore.user?.phone ?? "",
});
const loadingProfile = ref(false);

async function saveProfile() {
  if (!profileForm.name.trim()) return;
  loadingProfile.value = true;
  try {
    const updated = await profileService.updateProfile({
      name: profileForm.name,
      phone: profileForm.phone || null,
    });
    authStore.updateProfile({ name: updated.name, phone: updated.phone });
    toast.add({ title: "Profil berhasil diperbarui.", color: "primary" });
  } catch (err: any) {
    toast.add({
      title: "Gagal memperbarui profil.",
      description: err?.data?.message ?? "Terjadi kesalahan.",
      color: "error",
    });
  } finally {
    loadingProfile.value = false;
  }
}

// ── Password form ─────────────────────────────────────────
const passwordForm = reactive({
  current_password: "",
  password: "",
  password_confirmation: "",
});
const passwordError = ref<string | null>(null);
const loadingPassword = ref(false);

async function savePassword() {
  passwordError.value = null;
  if (passwordForm.password !== passwordForm.password_confirmation) {
    passwordError.value = "Konfirmasi password tidak cocok.";
    return;
  }
  loadingPassword.value = true;
  try {
    await profileService.updatePassword({
      current_password: passwordForm.current_password,
      password: passwordForm.password,
      password_confirmation: passwordForm.password_confirmation,
    });
    passwordForm.current_password = "";
    passwordForm.password = "";
    passwordForm.password_confirmation = "";
    toast.add({ title: "Password berhasil diperbarui.", color: "primary" });
  } catch (err: any) {
    const apiError =
      err?.data?.errors?.current_password?.[0] ??
      err?.data?.message ??
      "Terjadi kesalahan.";
    passwordError.value = apiError;
  } finally {
    loadingPassword.value = false;
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

      <!-- Info Profil -->
      <UCard class="mb-6">
        <template #header>
          <h2 class="font-bold text-gray-900">Info Profil</h2>
        </template>
        <div class="space-y-4">
          <UFormField label="Email" hint="Tidak dapat diubah">
            <UInput :model-value="authStore.user?.email ?? ''" disabled class="w-full" />
          </UFormField>
          <UFormField label="Nama" required>
            <UInput v-model="profileForm.name" placeholder="Nama lengkap" class="w-full" />
          </UFormField>
          <UFormField label="Telepon">
            <UInput v-model="profileForm.phone" placeholder="08xxxxxxxxxx" class="w-full" />
          </UFormField>
          <UButton
            color="primary"
            :loading="loadingProfile"
            :disabled="!profileForm.name.trim()"
            class="cursor-pointer"
            @click="saveProfile"
          >
            Simpan Profil
          </UButton>
        </div>
      </UCard>

      <!-- Ganti Password -->
      <UCard>
        <template #header>
          <h2 class="font-bold text-gray-900">Ganti Password</h2>
        </template>
        <div class="space-y-4">
          <UFormField
            label="Password Saat Ini"
            :error="passwordError ?? undefined"
          >
            <UInput
              v-model="passwordForm.current_password"
              type="password"
              placeholder="Password saat ini"
              class="w-full"
            />
          </UFormField>
          <UFormField label="Password Baru">
            <UInput
              v-model="passwordForm.password"
              type="password"
              placeholder="Min. 8 karakter"
              class="w-full"
            />
          </UFormField>
          <UFormField label="Konfirmasi Password Baru">
            <UInput
              v-model="passwordForm.password_confirmation"
              type="password"
              placeholder="Ulangi password baru"
              class="w-full"
            />
          </UFormField>
          <UButton
            color="primary"
            :loading="loadingPassword"
            :disabled="
              !passwordForm.current_password ||
              !passwordForm.password ||
              !passwordForm.password_confirmation
            "
            class="cursor-pointer"
            @click="savePassword"
          >
            Ubah Password
          </UButton>
        </div>
      </UCard>
    </div>
  </div>
</template>
```

- [ ] **Step 4: Start dev server and verify `/akun/profil`**

Run from `storefront/`:
```bash
npm run dev
```

Manual checks:
1. Navigate to `http://localhost:3000/akun/profil` without being logged in → should redirect to `/login?redirect=/akun/profil`.
2. Log in as a member → navigate back to `/akun/profil` → page loads with user's name and email pre-filled.
3. Update name → click "Simpan Profil" → toast "Profil berhasil diperbarui." → header shows updated name.
4. Enter wrong current password → click "Ubah Password" → inline error "Password saat ini tidak cocok." shows under the field.
5. Enter correct current password + matching new passwords → click "Ubah Password" → toast "Password berhasil diperbarui." → form clears.

- [ ] **Step 5: Commit**

```bash
git add storefront/app/services/profileService.ts storefront/app/store/auth.ts storefront/app/pages/akun/profil.vue
git commit -m "feat(storefront): add profile management page and profileService"
```

---

### Task 2: `/akun/alamat.vue` + AppHeader dropdown + mobile nav links

**Files:**
- Create: `storefront/app/pages/akun/alamat.vue`
- Modify: `storefront/app/components/AppHeader.vue` — desktop dropdown (lines 43–74) + mobile nav (lines 350–362)

**Interfaces:**
- Consumes: `addressService.getAddresses()`, `addressService.createAddress()`, `addressService.updateAddress()`, `addressService.deleteAddress()`, `addressService.getProvinces()`, `addressService.getCities(provinceId)`, `addressService.getDistricts(cityId)` (all existing in `services/addressService.ts`)
- Consumes: `Address` type from `~/types` (existing: `id`, `label`, `recipientName`, `phone`, `province`, `city`, `district`, `postalCode`, `fullAddress`, `isDefault`, `rajaongkirDistrictId`)
- Produces: `/akun/alamat` route with full CRUD; `AppHeader` dropdown with "Profil Saya" and "Alamat Saya" links

- [ ] **Step 1: Create `/akun/alamat.vue`**

Create `storefront/app/pages/akun/alamat.vue`:

```vue
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
  addresses.value = await addressService.getAddresses();
  loadingList.value = false;
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
                  @click="deleteConfirmId = address.id"
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
                  @click="deleteConfirmId = null"
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
              @click="modalOpen = false"
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
```

- [ ] **Step 2: Update AppHeader desktop dropdown**

Read `storefront/app/components/AppHeader.vue`. Replace the `userDropdownItems` computed at lines 43–74 with:

```typescript
const userDropdownItems = computed(() => [
  [
    {
      label: authStore.user?.email || "Member",
      icon: "i-heroicons-envelope",
      disabled: true,
    },
    {
      label: "Profil Saya",
      icon: "i-heroicons-user-circle",
      to: "/akun/profil",
      class: "cursor-pointer",
    },
    {
      label: "Alamat Saya",
      icon: "i-heroicons-map-pin",
      to: "/akun/alamat",
      class: "cursor-pointer",
    },
    {
      label: "Pesanan Saya",
      icon: "i-heroicons-shopping-bag",
      to: "/orders/history",
      class: "cursor-pointer",
    },
  ],
  [
    {
      label: "Keluar (Logout)",
      icon: "i-heroicons-arrow-right-on-rectangle",
      color: "error" as const,
      class: "cursor-pointer",
      onSelect: async () => {
        await authStore.logout();
        toast.add({
          title: "Logout Berhasil",
          description: "Anda telah keluar dari akun member.",
          color: "primary",
        });
        router.push("/");
      },
    },
  ],
]);
```

- [ ] **Step 3: Update AppHeader mobile nav**

In `storefront/app/components/AppHeader.vue`, replace the mobile "Pesanan Saya" `NuxtLink` block at lines 350–362 with (add Profil and Alamat links before it):

```html
                <NuxtLink
                  to="/akun/profil"
                  class="flex items-center justify-between text-gray-700 hover:bg-gray-50 hover:text-gray-950 font-semibold w-full text-left px-4 py-3 rounded-xl transition-all duration-300 cursor-pointer"
                  @click="mobileNavOpen = false"
                >
                  <span class="flex items-center gap-2">
                    <UIcon
                      name="i-heroicons-user-circle"
                      class="w-4 h-4 text-primary-600"
                    />
                    Profil Saya
                  </span>
                </NuxtLink>
                <NuxtLink
                  to="/akun/alamat"
                  class="flex items-center justify-between text-gray-700 hover:bg-gray-50 hover:text-gray-950 font-semibold w-full text-left px-4 py-3 rounded-xl transition-all duration-300 cursor-pointer"
                  @click="mobileNavOpen = false"
                >
                  <span class="flex items-center gap-2">
                    <UIcon
                      name="i-heroicons-map-pin"
                      class="w-4 h-4 text-primary-600"
                    />
                    Alamat Saya
                  </span>
                </NuxtLink>
                <NuxtLink
                  to="/orders/history"
                  class="flex items-center justify-between text-gray-700 hover:bg-gray-50 hover:text-gray-950 font-semibold w-full text-left px-4 py-3 rounded-xl transition-all duration-300 cursor-pointer"
                  @click="mobileNavOpen = false"
                >
                  <span class="flex items-center gap-2">
                    <UIcon
                      name="i-heroicons-shopping-bag"
                      class="w-4 h-4 text-primary-600"
                    />
                    Pesanan Saya
                  </span>
                </NuxtLink>
```

- [ ] **Step 4: Start dev server and verify `/akun/alamat` and header**

Run from `storefront/`:
```bash
npm run dev
```

Manual checks:
1. Navigate to `http://localhost:3000/akun/alamat` without login → redirected to `/login`.
2. Log in → navigate to `/akun/alamat` → "Belum ada alamat tersimpan." shown if empty.
3. Click "+ Tambah Alamat" → modal opens → select Provinsi → Kota/Kabupaten dropdown populates → select Kota → Kecamatan dropdown populates → fill all fields → click "Tambah" → address card appears in list.
4. Click "Edit" on a card → modal opens with form pre-filled (province, city, district re-hydrated) → update a field → "Simpan" → list refreshes with changes.
5. Click "Hapus" on a card → "Ya, Hapus" / "Batal" inline buttons appear → confirm → address removed.
6. Click "Jadikan Utama" on a non-default address → "Utama" badge moves to that card.
7. Desktop: click user avatar in header → dropdown shows "Profil Saya", "Alamat Saya", "Pesanan Saya".
8. Mobile: open hamburger menu → "Profil Saya" and "Alamat Saya" links appear above "Pesanan Saya".
9. Both tab links (Profil | Alamat) work correctly between the two pages.

- [ ] **Step 5: Commit**

```bash
git add storefront/app/pages/akun/alamat.vue storefront/app/components/AppHeader.vue
git commit -m "feat(storefront): add address management page and header account links"
```

---

## Verification Plan

Manual verification only (no Vitest component tests — project convention).

Run `npm run dev` from `storefront/` and verify:

**Auth guard:**
- Unauthenticated → `/akun/profil` and `/akun/alamat` both redirect to `/login?redirect=...`
- After login, redirect returns user to the intended page

**Profile page:**
- Name and email pre-filled from auth store on load
- Save profile → toast + header name updates
- Wrong current password → inline error under field
- Correct password change → form clears, toast

**Address page:**
- Empty state shown when no addresses
- Add: modal cascade (province → city → district), submit → card appears
- Edit: pre-filled cascade, update → list refreshes
- Delete inline confirm, confirm → address gone
- Set default → badge moves
- "Tambah" button disabled until all required fields filled

**Header:**
- Desktop dropdown: Profil Saya, Alamat Saya, Pesanan Saya present
- Mobile nav: same three links present above Logout
