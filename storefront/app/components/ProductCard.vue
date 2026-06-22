<script setup lang="ts">
import type { Product } from "~/types";
import { catalogService } from "~/services/catalogService";
import { useCurrency } from "~/composables/useCurrency";
import { useWishlistStore } from "~/store/wishlist";

const props = defineProps<{
  product: Product;
}>();

const { format } = useCurrency();
const wishlist = useWishlistStore();

const outOfStock = computed(() => catalogService.isOutOfStock(props.product));
const startingPrice = computed(() => catalogService.minPrice(props.product));
const wishlisted = computed(() => wishlist.isWishlisted(props.product.id));

const totalStock = computed(() =>
  props.product.variants.reduce((sum, v) => sum + v.stock, 0),
);

function toggleWishlist(event: MouseEvent) {
  event.preventDefault();
  event.stopPropagation();
  wishlist.toggle(props.product.id);
}
</script>

<template>
  <NuxtLink
    :to="`/produk/${product.slug}`"
    class="block group"
    :class="outOfStock ? 'opacity-60' : ''"
  >
    <div class="relative aspect-square rounded-xl overflow-hidden bg-gray-100">
      <img
        :src="product.images[0]"
        :alt="product.name"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
      />
      <UBadge
        v-if="outOfStock"
        color="neutral"
        variant="solid"
        class="absolute top-2 left-2"
      >
        Stok Habis
      </UBadge>
      <button
        type="button"
        class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 flex items-center justify-center shadow-sm"
        @click="toggleWishlist"
      >
        <UIcon
          :name="wishlisted ? 'i-heroicons-heart-solid' : 'i-heroicons-heart'"
          :class="wishlisted ? 'text-red-500' : 'text-gray-500'"
          class="w-4 h-4"
        />
      </button>
    </div>
    <div class="mt-3 space-y-1">
      <h3 class="text-sm font-medium text-gray-900 line-clamp-2">
        {{ product.name }}
      </h3>
      <p class="text-base font-semibold text-primary-700">
        {{ format(startingPrice) }}
      </p>
      <div
        class="flex flex-col md:flex-row md:items-center justify-between gap-2 mt-1"
      >
        <RatingStars :rating="product.ratingAvg" :count="product.ratingCount" />
        <span
          v-if="!outOfStock"
          class="text-[10px] text-primary-500 font-semibold bg-primary-100/50 px-2 py-0.5 rounded-full border border-gray-100 w-fit"
        >
          Stok: {{ totalStock }}
        </span>
      </div>
    </div>
  </NuxtLink>
</template>
