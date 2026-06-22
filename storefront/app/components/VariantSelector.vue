<script setup lang="ts">
import type { ProductVariant } from "~/types";
import { useCurrency } from "~/composables/useCurrency";

const props = defineProps<{
  variants: ProductVariant[];
  modelValue: number | null;
}>();

const emit = defineEmits<{
  "update:modelValue": [value: number];
}>();

const { format } = useCurrency();

function select(variant: ProductVariant) {
  if (variant.stock === 0) return;
  emit("update:modelValue", variant.id);
}
</script>

<template>
  <div class="flex flex-wrap gap-2">
    <button
      v-for="variant in variants"
      :key="variant.id"
      type="button"
      :disabled="variant.stock === 0"
      class="px-4 py-2 rounded-lg border text-sm text-left transition-colors"
      :class="[
        variant.id === modelValue
          ? 'border-primary-500 bg-primary-50 text-primary-700'
          : 'border-gray-300 text-gray-700 hover:border-gray-400',
        variant.stock === 0
          ? 'opacity-50 cursor-not-allowed'
          : 'cursor-pointer',
      ]"
      @click="select(variant)"
    >
      <div class="font-medium">{{ variant.name }}</div>
      <div class="text-xs text-gray-500">
        <span v-if="variant.stock === 0" class="text-red-500">Stok Habis</span>
        <span v-else>{{ format(variant.price) }}</span>
      </div>
    </button>
  </div>
</template>
