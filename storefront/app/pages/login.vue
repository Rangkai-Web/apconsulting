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
  email: "",
  password: "",
});

const fieldErrors = reactive({
  email: "",
  password: "",
});

const errorMsg = ref("");
const loading = ref(false);
const showPassword = ref(false);

watch(() => state.email, () => { fieldErrors.email = ""; });
watch(() => state.password, () => { fieldErrors.password = ""; });

// Validate every field so the user sees exactly which one is wrong
function validate(): boolean {
  fieldErrors.email = "";
  fieldErrors.password = "";

  if (!state.email) fieldErrors.email = "Email wajib diisi.";
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(state.email))
    fieldErrors.email = "Format email tidak valid.";

  if (!state.password) fieldErrors.password = "Password wajib diisi.";
  else if (state.password.length < 6)
    fieldErrors.password = "Password minimal harus 6 karakter.";

  return !fieldErrors.email && !fieldErrors.password;
}

async function onSubmit() {
  if (!validate()) return;

  errorMsg.value = "";
  loading.value = true;

  try {
    await authStore.login({
      email: state.email,
      password: state.password,
    });

    toast.add({
      title: "Login Berhasil",
      description: `Selamat datang kembali, ${authStore.user?.name}!`,
      color: "primary",
    });

    const redirectPath = (route.query.redirect as string) || "/";
    router.push(redirectPath);
  } catch (err: any) {
    console.error("Login error:", err);
    errorMsg.value =
      err.data?.message || "Email atau password yang Anda masukkan salah.";
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <div
    class="relative overflow-hidden bg-linear-to-b from-primary-50/15 via-white to-white min-h-screen py-20 flex items-center justify-center"
  >
    <!-- Ambient Background Glows -->
    <div
      class="absolute top-12 -left-40 w-96 h-96 bg-primary-100/10 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-[400px] -right-40 w-96 h-96 bg-secondary-100/10 rounded-full blur-3xl pointer-events-none"
    ></div>

    <div class="max-w-md w-full px-4 relative">
      <!-- Premium Login Card -->
      <div
        class="relative bg-white/80 backdrop-blur-sm border border-primary-100/60 rounded-3xl p-8 md:p-10 shadow-xl shadow-primary-950/5 overflow-hidden"
      >
        <!-- Background Leaf Glow & Motif -->
        <div
          class="absolute -top-12 -right-12 w-32 h-32 bg-primary-100/30 rounded-full blur-2xl pointer-events-none"
        ></div>
        <UIcon
          name="i-lucide-leaf"
          class="absolute -bottom-8 -left-8 w-28 h-28 text-primary-500/5 -rotate-45 pointer-events-none"
        />

        <!-- Title Block -->
        <div class="text-center mb-8 relative">
          <div
            class="w-12 h-12 rounded-full bg-linear-to-tr from-primary-600 to-primary-400 flex items-center justify-center text-white mx-auto shadow-md shadow-primary-600/20 mb-4"
          >
            <UIcon name="i-heroicons-user" class="w-6 h-6 text-white" />
          </div>
          <span
            class="text-[10px] text-primary-600 uppercase font-extrabold tracking-wider"
            >BbystarGO</span
          >
          <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight mt-1">
            Masuk Akun Member
          </h1>
          <p class="text-xs text-gray-500 mt-1 font-medium">
            Masukkan akun Anda untuk melanjutkan pemesanan
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

        <!-- Login Form -->
        <form @submit.prevent="onSubmit" class="space-y-4 relative">
          <UFormField
            label="Alamat Email"
            :error="fieldErrors.email || undefined"
            :ui="{ label: 'block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1' }"
          >
            <UInput
              id="login-email"
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
            label="Kata Sandi"
            :error="fieldErrors.password || undefined"
            :ui="{ label: 'block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1' }"
          >
            <UInput
              id="login-password"
              v-model="state.password"
              :type="showPassword ? 'text' : 'password'"
              placeholder="••••••••"
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

          <div class="pt-2">
            <UButton
              type="submit"
              block
              color="primary"
              size="md"
              :loading="loading"
              class="font-semibold py-3.5 rounded-full cursor-pointer shadow-lg shadow-primary-600/20 hover:shadow-primary-600/30 transform hover:scale-[1.01] transition-all duration-300"
            >
              Masuk Sekarang
            </UButton>
          </div>
        </form>

        <!-- Redirect to Register -->
        <div
          class="text-center mt-6 pt-4 border-t border-gray-100 text-xs text-gray-500 font-medium"
        >
          Belum memiliki akun member?
          <NuxtLink
            :to="`/register${route.query.redirect ? '?redirect=' + route.query.redirect : ''}`"
            class="text-primary-700 font-extrabold hover:text-primary-800 transition-colors ml-1"
          >
            Daftar Di Sini
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>
