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
