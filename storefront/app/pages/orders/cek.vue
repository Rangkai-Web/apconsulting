<script setup lang="ts">
const router = useRouter();

const form = reactive({
  orderNumber: "",
  email: "",
});

const loading = ref(false);

const formValid = computed(
  () => form.orderNumber.trim().length > 0 && form.email.trim().length > 0,
);

async function handleLookup() {
  if (!formValid.value) return;
  loading.value = true;
  try {
    await router.push(
      `/orders/${form.orderNumber.trim()}?email=${encodeURIComponent(form.email.trim())}`,
    );
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <div
    class="relative overflow-hidden bg-linear-to-b from-primary-50/15 via-white to-white min-h-screen py-16"
  >
    <div
      class="absolute top-12 -left-40 w-96 h-96 bg-primary-100/10 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-[400px] -right-40 w-96 h-96 bg-secondary-100/10 rounded-full blur-3xl pointer-events-none"
    ></div>

    <div class="max-w-md mx-auto px-4 relative">
      <div class="text-center mb-10">
        <div
          class="w-14 h-14 rounded-2xl bg-primary-50 flex items-center justify-center mx-auto mb-4 shadow-sm"
        >
          <UIcon
            name="i-heroicons-magnifying-glass"
            class="w-7 h-7 text-primary-600"
          />
        </div>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight">
          Cek Status Pesanan
        </h1>
        <p class="text-sm text-gray-500 font-medium mt-2">
          Masukkan nomor pesanan dan email yang Anda gunakan saat checkout.
        </p>
      </div>

      <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-xs">
        <form class="space-y-5" @submit.prevent="handleLookup">
          <UFormField label="Nomor Pesanan" required>
            <UInput
              v-model="form.orderNumber"
              placeholder="Contoh: ORD-20260701-XXXX"
              size="lg"
              class="w-full"
              :ui="{ base: 'rounded-xl' }"
            />
          </UFormField>

          <UFormField label="Email" required>
            <UInput
              v-model="form.email"
              type="email"
              placeholder="email@contoh.com"
              size="lg"
              class="w-full"
              :ui="{ base: 'rounded-xl' }"
            />
          </UFormField>

          <UButton
            type="submit"
            color="primary"
            size="lg"
            class="w-full font-extrabold rounded-full cursor-pointer shadow-md shadow-primary-600/15 mt-2"
            :disabled="!formValid"
            :loading="loading"
          >
            Cek Pesanan
          </UButton>
        </form>
      </div>

      <p class="text-center text-xs text-gray-400 font-medium mt-6">
        Punya akun?
        <NuxtLink to="/login" class="text-primary-600 font-bold hover:underline"
          >Masuk</NuxtLink
        >
        untuk melihat semua riwayat pesanan.
      </p>
    </div>
  </div>
</template>
