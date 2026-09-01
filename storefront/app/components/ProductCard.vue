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
    class="block group bg-white backdrop-blur-xs border border-primary-300/40 rounded-2xl p-2.5 shadow-[0_4px_20px_-4px_rgba(255,116,108,0.03)] hover:shadow-[0_8px_30px_-6px_rgba(255,116,108,0.12)] hover:-translate-y-1 transition-all duration-300"
    :class="outOfStock ? 'opacity-60' : ''"
  >
    <div
      class="relative aspect-square rounded-xl overflow-hidden bg-primary-50/20 border border-primary-50/40"
    >
      <img
        :src="product.images[0]"
        :alt="product.name"
        class="w-full h-full object-cover group-hover:scale-106 transition-transform duration-500"
      />
      <UBadge
        v-if="outOfStock"
        color="neutral"
        variant="solid"
        class="absolute top-2 left-2 font-bold text-[10px]"
      >
        Stok Habis
      </UBadge>
      <button
        type="button"
        class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/95 border border-primary-300 flex items-center justify-center shadow-md hover:scale-105 active:scale-95 transition-transform cursor-pointer"
        :aria-label="wishlisted ? 'Hapus dari wishlist' : 'Tambah ke wishlist'"
        :aria-pressed="wishlisted"
        @click="toggleWishlist"
      >
        <UIcon
          :name="wishlisted ? 'i-heroicons-heart-solid' : 'i-heroicons-heart'"
          :class="
            wishlisted
              ? 'text-primary-500 scale-105'
              : 'text-gray-400 group-hover:text-primary-400'
          "
          class="w-4 h-4 transition-colors duration-250"
        />
      </button>
    </div>
    <div class="mt-2.5 space-y-1">
      <h3
        class="text-xs font-bold text-gray-800 line-clamp-2 leading-snug group-hover:text-primary-600 transition-colors min-h-10"
      >
        {{ product.name }}
      </h3>
      <p class="text-sm font-black text-primary-500">
        {{ format(startingPrice) }}
      </p>
      <div class="flex flex-wrap items-center justify-between gap-1.5 pt-0.5">
        <RatingStars :rating="product.ratingAvg" :count="product.ratingCount" />
        <span
          v-if="!outOfStock"
          class="text-[9px] text-primary-500 font-extrabold bg-primary-50/80 px-2 py-0.5 rounded-full border border-primary-100/50 w-fit"
        >
          Stok: {{ totalStock }}
        </span>
      </div>
    </div>
  </NuxtLink>
</template>
