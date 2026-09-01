<script setup lang="ts">
import { profileService } from "~/services/profileService";
import { useAuthStore } from "~/store/auth";

definePageMeta({ middleware: "auth" });

const authStore = useAuthStore();
const toast = useToast();

useSeoMeta({ title: "Profil Saya", robots: "noindex, nofollow" });

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

// Profile form
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

// Password form
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

          <NuxtLink to="/orders/history">
            <UButton
              color="primary"
              variant="outline"
              icon="i-heroicons-shopping-bag"
              class="rounded-xl font-extrabold text-xs px-4 py-2 cursor-pointer"
            >
              Riwayat Pesanan Saya
            </UButton>
          </NuxtLink>
        </div>

        <!-- Account Tab Navigation Bar -->
        <div class="flex items-center gap-2 border-t border-gray-100 pt-4">
          <NuxtLink
            v-for="tab in tabs"
            :key="tab.to"
            :to="tab.to"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all duration-300 cursor-pointer shadow-2xs border"
            :class="
              $route.path === tab.to
                ? 'bg-primary-500 text-white border-primary-500 shadow-md shadow-primary-500/20'
                : 'bg-gray-50/80 text-gray-600 border-gray-200/80 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            <UIcon :name="tab.icon" class="w-4 h-4" />
            <span>{{ tab.label }}</span>
          </NuxtLink>
        </div>
      </div>

      <!-- Main Form Grid (Profile Info & Password Security) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
        <!-- Card 1: Informasi Profil -->
        <div
          class="bg-white/90 backdrop-blur-md border border-gray-200/80 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6"
        >
          <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
            <div
              class="w-8 h-8 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0"
            >
              <UIcon name="i-heroicons-user" class="w-4 h-4" />
            </div>
            <div>
              <h2 class="text-base font-extrabold text-gray-900 leading-tight">
                Informasi Profil Akun
              </h2>
              <p class="text-xs text-gray-500">
                Perbarui data diri dan nomor kontak Anda
              </p>
            </div>
          </div>

          <div class="space-y-4">
            <UFormField
              label="Alamat Email (Akun)"
              hint="Permanen & tidak dapat diubah"
            >
              <UInput
                :model-value="authStore.user?.email ?? ''"
                disabled
                icon="i-heroicons-envelope"
                class="w-full bg-gray-50/80"
              />
            </UFormField>

            <UFormField label="Nama Lengkap" required>
              <UInput
                v-model="profileForm.name"
                placeholder="Masukkan nama lengkap Anda"
                icon="i-heroicons-user"
                class="w-full"
              />
            </UFormField>

            <UFormField label="Nomor Telepon / WhatsApp">
              <UInput
                v-model="profileForm.phone"
                placeholder="Contoh: 08123456789"
                icon="i-heroicons-phone"
                class="w-full"
              />
            </UFormField>

            <div class="pt-2">
              <UButton
                color="primary"
                size="lg"
                class="font-black px-6 py-3 rounded-2xl cursor-pointer shadow-md shadow-primary-500/20 hover:scale-[1.01] transition-transform duration-300 w-full sm:w-auto"
                :loading="loadingProfile"
                :disabled="!profileForm.name.trim()"
                @click="saveProfile"
              >
                Simpan Perubahan Profil
              </UButton>
            </div>
          </div>
        </div>

        <!-- Card 2: Keamanan & Password -->
        <div
          class="bg-white/90 backdrop-blur-md border border-gray-200/80 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6"
        >
          <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
            <div
              class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0"
            >
              <UIcon name="i-heroicons-key" class="w-4 h-4" />
            </div>
            <div>
              <h2 class="text-base font-extrabold text-gray-900 leading-tight">
                Ganti Kata Sandi
              </h2>
              <p class="text-xs text-gray-500">
                Amankan akun Anda dengan kata sandi baru
              </p>
            </div>
          </div>

          <div class="space-y-4">
            <div
              v-if="passwordError"
              class="bg-red-50 border border-red-200/80 rounded-2xl p-3.5 text-xs text-red-600 font-semibold flex items-start gap-2"
            >
              <UIcon
                name="i-heroicons-exclamation-triangle"
                class="w-4 h-4 shrink-0 mt-0.5"
              />
              <span>{{ passwordError }}</span>
            </div>

            <UFormField label="Kata Sandi Saat Ini" required>
              <UInput
                v-model="passwordForm.current_password"
                type="password"
                placeholder="Masukkan kata sandi saat ini"
                icon="i-heroicons-lock-closed"
                class="w-full"
              />
            </UFormField>

            <UFormField label="Kata Sandi Baru" required>
              <UInput
                v-model="passwordForm.password"
                type="password"
                placeholder="Min. 8 karakter"
                icon="i-heroicons-lock-closed"
                class="w-full"
              />
            </UFormField>

            <UFormField label="Konfirmasi Kata Sandi Baru" required>
              <UInput
                v-model="passwordForm.password_confirmation"
                type="password"
                placeholder="Ulangi kata sandi baru"
                icon="i-heroicons-lock-closed"
                class="w-full"
              />
            </UFormField>

            <div class="pt-2">
              <UButton
                color="primary"
                size="lg"
                class="font-black px-6 py-3 rounded-2xl cursor-pointer shadow-md shadow-primary-500/20 hover:scale-[1.01] transition-transform duration-300 w-full sm:w-auto"
                :loading="loadingPassword"
                :disabled="
                  !passwordForm.current_password ||
                  !passwordForm.password ||
                  !passwordForm.password_confirmation
                "
                @click="savePassword"
              >
                Ubah Kata Sandi
              </UButton>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
