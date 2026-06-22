<script setup lang="ts">
import type { Product } from "~/types";

defineProps<{
  products: Product[];
  loading: boolean;
  hasMore: boolean;
}>();

defineEmits<{
  "load-more": [];
}>();
</script>

<template>
  <div>
    <div
      v-if="loading && products.length === 0"
      class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6"
    >
      <div v-for="n in 8" :key="n" class="space-y-3">
        <USkeleton class="aspect-square rounded-xl" />
        <USkeleton class="h-4 w-3/4" />
        <USkeleton class="h-4 w-1/2" />
      </div>
    </div>

    <EmptyState
      v-else-if="!loading && products.length === 0"
      icon="i-heroicons-magnifying-glass"
      title="Produk tidak ditemukan"
      description="Coba ubah filter atau kata kunci pencarian."
    />

    <template v-else>
      <div
        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6"
      >
        <ProductCard
          v-for="product in products"
          :key="product.id"
          :product="product"
        />
      </div>

      <div v-if="hasMore" class="flex justify-center mt-8">
        <UButton
          color="primary"
          variant="solid"
          :loading="loading"
          class="rounded-full cursor-pointer py-2 px-4"
          @click="$emit('load-more')"
        >
          Muat Lebih Banyak
        </UButton>
      </div>
    </template>
  </div>
</template>
