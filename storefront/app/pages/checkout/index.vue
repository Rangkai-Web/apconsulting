<script setup lang="ts">
definePageMeta({
  middleware: "auth",
});
</script>

<template>
  <div
    class="relative overflow-hidden bg-linear-to-b from-primary-50/15 via-white to-white min-h-screen py-20"
  >
    <!-- Ambient Background Glows -->
    <div
      class="absolute top-12 -left-40 w-96 h-96 bg-primary-100/10 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-[400px] -right-40 w-96 h-96 bg-secondary-100/10 rounded-full blur-3xl pointer-events-none"
    ></div>

    <div class="max-w-2xl mx-auto px-4 relative">
      <!-- Styled Checkout Card -->
      <div
        class="relative bg-white/80 backdrop-blur-sm border border-primary-100/60 rounded-3xl p-8 md:p-12 shadow-xl shadow-primary-950/5 overflow-hidden"
      >
        <!-- Background Leaf Glow & Motif -->
        <div
          class="absolute -top-12 -right-12 w-32 h-32 bg-primary-100/30 rounded-full blur-2xl pointer-events-none"
        ></div>
        <UIcon
          name="i-lucide-leaf"
          class="absolute -bottom-8 -left-8 w-28 h-28 text-primary-500/5 -rotate-45 pointer-events-none"
        />

        <EmptyState
          icon="i-heroicons-wrench-screwdriver"
          title="Checkout Segera Hadir"
          description="Halaman pembayaran, kurir pengiriman, dan alamat pengiriman saat ini sedang dalam tahap pengembangan akhir."
          action-label="Kembali ke Keranjang"
          action-to="/cart"
        />
      </div>
    </div>
  </div>
</template>
