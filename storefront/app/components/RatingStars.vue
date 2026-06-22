<script setup lang="ts">
const props = defineProps<{
  rating: number;
  count?: number;
  size?: "sm" | "md";
}>();

const iconSize = computed(() =>
  props.size === "md" ? "w-5 h-5" : "w-3.5 h-3.5",
);
const stars = computed(() => Math.round(props.rating));
</script>

<template>
  <div class="flex items-center gap-1">
    <div class="flex items-center">
      <UIcon
        v-for="n in 5"
        :key="n"
        :name="n <= stars ? 'i-heroicons-star-solid' : 'i-heroicons-star'"
        :class="[iconSize, n <= stars ? 'text-amber-400' : 'text-gray-300']"
      />
    </div>
    <span class="text-xs text-gray-500">
      {{ rating.toFixed(1) }}
      <template v-if="count != null"> ({{ count }})</template>
    </span>
  </div>
</template>
