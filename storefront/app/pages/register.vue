<script setup lang="ts">
definePageMeta({
  middleware: "guest-only",
});

import { useAuthStore } from "~/store/auth";

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const toast = useToast();

const state = reactive({
  name: "",
  email: "",
  phone: "",
  password: "",
  password_confirmation: "",
});

const fieldErrors = reactive({
  name: "",
  email: "",
  phone: "",
  password: "",
  password_confirmation: "",
});

const errorMsg = ref("");
const loading = ref(false);
const showPassword = ref(false);
const showPasswordConfirm = ref(false);

watch(() => state.name, () => { fieldErrors.name = ""; });
watch(() => state.email, () => { fieldErrors.email = ""; });
watch(() => state.phone, () => { fieldErrors.phone = ""; });
watch(() => state.password, () => { fieldErrors.password = ""; });
watch(() => state.password_confirmation, () => { fieldErrors.password_confirmation = ""; });

// Normalize phone to international format 628XXXXXXXXXX
function normalizePhone(phone: string): string {
  const digits = phone.replace(/[^0-9]/g, "");
  if (digits.startsWith("628")) return digits;
  if (digits.startsWith("08")) return "62" + digits.slice(1);
  if (digits.startsWith("62")) return digits;
  return digits;
}

// Validate every field so the user sees exactly which one is wrong
function validate(): boolean {
  fieldErrors.name = "";
  fieldErrors.email = "";
  fieldErrors.phone = "";
  fieldErrors.password = "";
  fieldErrors.password_confirmation = "";

  if (!state.name) fieldErrors.name = "Nama Lengkap wajib diisi.";

  if (!state.email) fieldErrors.email = "Email wajib diisi.";
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(state.email))
    fieldErrors.email = "Format email tidak valid.";

  if (!state.phone) {
    fieldErrors.phone = "No. Telepon wajib diisi.";
  } else {
    // Accept: 08xx, 628xx, +62xx  — must be a valid Indonesian mobile number
    const cleaned = state.phone.replace(/[^0-9]/g, "");
    const isValid =
      /^(08|628|\+?62)/.test(state.phone) &&
      cleaned.length >= 10 &&
      cleaned.length <= 14;
    if (!isValid)
      fieldErrors.phone =
        "No. Telepon tidak valid. Gunakan format 08xx atau 628xx (10–14 angka).";
  }

  if (!state.password) fieldErrors.password = "Password wajib diisi.";
  else if (state.password.length < 6)
    fieldErrors.password = "Password minimal harus 6 karakter.";

  if (state.password && state.password !== state.password_confirmation)
    fieldErrors.password_confirmation = "Konfirmasi password tidak cocok.";

  return Object.values(fieldErrors).every((e) => !e);
}

async function onSubmit() {
  if (!validate()) return;

  errorMsg.value = "";
  loading.value = true;

  try {
    await authStore.register({
      name: state.name,
      email: state.email,
      phone: normalizePhone(state.phone), // normalize to 628... format
      password: state.password,
      password_confirmation: state.password_confirmation,
    });

    toast.add({
      title: "Registrasi Berhasil",
      description: `Selamat datang di DR PLANT, ${authStore.user?.name}! Akun member Anda berhasil didaftarkan.`,
      color: "primary",
    });

    const redirectPath = (route.query.redirect as string) || "/";
    router.push(redirectPath);
  } catch (err: any) {
    console.error("Register error:", err);
    errorMsg.value =
      err.data?.message ||
      "Gagal melakukan pendaftaran akun. Silakan coba lagi.";
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <div
    class="relative overflow-hidden bg-linear-to-b from-primary-50/15 via-white to-white min-h-screen py-16 flex items-center justify-center"
  >
    <!-- Ambient Background Glows -->
    <div
      class="absolute top-12 -left-40 w-96 h-96 bg-primary-100/10 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-[400px] -right-40 w-96 h-96 bg-secondary-100/10 rounded-full blur-3xl pointer-events-none"
    ></div>

    <div class="max-w-md w-full px-4 relative">
      <!-- Premium Register Card -->
      <div
        class="relative bg-white/80 backdrop-blur-sm border border-primary-100/60 rounded-3xl p-8 md:p-10 shadow-xl shadow-primary-950/5 overflow-hidden"
      >
        <!-- Background Glow & Cute Motif -->
        <AppCuteDecor />

        <!-- Title Block -->
        <div class="text-center mb-6 relative">
          <div
            class="w-12 h-12 rounded-full bg-linear-to-tr from-primary-600 to-primary-400 flex items-center justify-center text-white mx-auto shadow-md shadow-primary-600/20 mb-4"
          >
            <UIcon name="i-heroicons-user-plus" class="w-6 h-6 text-white" />
          </div>
          <span
            class="text-[10px] text-primary-600 uppercase font-extrabold tracking-wider"
            >BbystarGO</span
          >
          <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight mt-1">
            Daftar Member Baru
          </h1>
          <p class="text-xs text-gray-500 mt-1 font-medium">
            Buat akun untuk nikmati diskon khusus & layanan premium
          </p>
        </div>

        <!-- Error Alert -->
        <div v-if="errorMsg" class="mb-5">
          <UAlert
            color="error"
            variant="soft"
            icon="i-heroicons-exclamation-triangle"
            :title="errorMsg"
          />
        </div>

        <!-- Registration Form -->
        <form @submit.prevent="onSubmit" class="space-y-4 relative">
          <UFormField
            label="Nama Lengkap"
            :error="fieldErrors.name || undefined"
            :ui="{ label: 'block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1' }"
          >
            <UInput
              id="register-name"
              v-model="state.name"
              type="text"
              placeholder="Masukkan nama lengkap Anda"
              icon="i-heroicons-user"
              size="md"
              class="w-full border border-gray-200 rounded-full"
              :ui="{
                base: 'rounded-full focus-visible:ring-1 focus-visible:ring-primary-200 focus-visible:outline-none',
              }"
            />
          </UFormField>

          <UFormField
            label="Alamat Email"
            :error="fieldErrors.email || undefined"
            :ui="{ label: 'block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1' }"
          >
            <UInput
              id="register-email"
              v-model="state.email"
              type="email"
              placeholder="nama@email.com"
              icon="i-heroicons-envelope"
              size="md"
              class="w-full border border-gray-200 rounded-full"
              :ui="{
                base: 'rounded-full focus-visible:ring-1 focus-visible:ring-primary-200 focus-visible:outline-none',
              }"
            />
          </UFormField>

          <UFormField
            label="No. Telepon / WhatsApp"
            :error="fieldErrors.phone || undefined"
            :ui="{ label: 'block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1' }"
          >
            <UInput
              id="register-phone"
              v-model="state.phone"
              type="text"
              placeholder="08xx atau 628xx (cth: 08121234567)"
              icon="i-heroicons-phone"
              size="md"
              class="w-full border border-gray-200 rounded-full"
              :ui="{
                base: 'rounded-full focus-visible:ring-1 focus-visible:ring-primary-200 focus-visible:outline-none',
              }"
            />
          </UFormField>

          <UFormField
            label="Kata Sandi"
            :error="fieldErrors.password || undefined"
            :ui="{ label: 'block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1' }"
          >
            <UInput
              id="register-password"
              v-model="state.password"
              :type="showPassword ? 'text' : 'password'"
              placeholder="Min. 6 karakter"
              icon="i-heroicons-lock-closed"
              size="md"
              class="w-full border border-gray-200 rounded-full"
              :ui="{
                base: 'rounded-full focus-visible:ring-1 focus-visible:ring-primary-200 focus-visible:outline-none',
                trailing: 'pe-2',
              }"
            >
              <template #trailing>
                <button
                  type="button"
                  tabindex="-1"
                  class="text-gray-400 hover:text-primary-600 transition-colors cursor-pointer p-1"
                  :aria-label="
                    showPassword
                      ? 'Sembunyikan password'
                      : 'Tampilkan password'
                  "
                  @click="showPassword = !showPassword"
                >
                  <UIcon
                    :name="
                      showPassword ? 'i-heroicons-eye-slash' : 'i-heroicons-eye'
                    "
                    class="w-4 h-4"
                  />
                </button>
              </template>
            </UInput>
          </UFormField>

          <UFormField
            label="Konfirmasi Kata Sandi"
            :error="fieldErrors.password_confirmation || undefined"
            :ui="{ label: 'block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1' }"
          >
            <UInput
              id="register-password-confirmation"
              v-model="state.password_confirmation"
              :type="showPasswordConfirm ? 'text' : 'password'"
              placeholder="Ulangi kata sandi Anda"
              icon="i-heroicons-check-circle"
              size="md"
              class="w-full border border-gray-200 rounded-full"
              :ui="{
                base: 'rounded-full focus-visible:ring-1 focus-visible:ring-primary-200 focus-visible:outline-none',
                trailing: 'pe-2',
              }"
            >
              <template #trailing>
                <button
                  type="button"
                  tabindex="-1"
                  class="text-gray-400 hover:text-primary-600 transition-colors cursor-pointer p-1"
                  :aria-label="
                    showPasswordConfirm
                      ? 'Sembunyikan password'
                      : 'Tampilkan password'
                  "
                  @click="showPasswordConfirm = !showPasswordConfirm"
                >
                  <UIcon
                    :name="
                      showPasswordConfirm
                        ? 'i-heroicons-eye-slash'
                        : 'i-heroicons-eye'
                    "
                    class="w-4 h-4"
                  />
                </button>
              </template>
            </UInput>
          </UFormField>

          <div class="pt-2">
            <UButton
              type="submit"
              block
              color="primary"
              size="md"
              :loading="loading"
              class="font-semibold py-3.5 rounded-full cursor-pointer shadow-lg shadow-primary-600/20 hover:shadow-primary-600/30 transform hover:scale-[1.01] transition-all duration-300"
            >
              Daftar Sekarang
            </UButton>
          </div>
        </form>

        <!-- Redirect to Login -->
        <div
          class="text-center mt-6 pt-4 border-t border-gray-100 text-xs text-gray-500 font-medium"
        >
          Sudah memiliki akun member?
          <NuxtLink
            :to="`/login${route.query.redirect ? '?redirect=' + route.query.redirect : ''}`"
            class="text-primary-700 font-extrabold hover:text-primary-800 transition-colors ml-1"
          >
            Masuk Di Sini
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>
