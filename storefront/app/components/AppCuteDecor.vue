<script setup lang="ts">
withDefaults(
  defineProps<{
    onDark?: boolean;
  }>(),
  { onDark: false },
);
</script>

<template>
  <div class="absolute inset-0 z-10 pointer-events-none overflow-hidden" aria-hidden="true">
    <UIcon
      name="i-heroicons-sparkles-solid"
      class="absolute top-4 right-4 w-12 h-12 rotate-12 animate-slow-spin"
      :class="onDark ? 'text-white/25' : 'text-amber-400/60'"
    />
    <UIcon
      name="i-heroicons-heart-solid"
      class="absolute top-1/4 left-3 w-10 h-10 -rotate-12 animate-float"
      :class="onDark ? 'text-primary-200/30' : 'text-primary-400/65'"
    />
    <UIcon
      name="i-lucide-flower-2"
      class="absolute top-2/3 right-6 w-11 h-11 rotate-45 animate-float-delayed"
      :class="onDark ? 'text-white/25' : 'text-primary-400/60'"
    />
    <UIcon
      name="i-heroicons-star-solid"
      class="absolute bottom-6 left-1/5 w-12 h-12 rotate-6 animate-float"
      :class="onDark ? 'text-amber-200/30' : 'text-amber-300/65'"
    />
    <UIcon
      name="i-heroicons-sparkles-solid"
      class="absolute bottom-1/3 right-1/4 w-9 h-9 -rotate-6 animate-slow-spin"
      :class="onDark ? 'text-white/20' : 'text-amber-400/50'"
    />
    <UIcon
      name="i-heroicons-heart-solid"
      class="absolute top-6 left-1/3 w-8 h-8 rotate-6 animate-float-delayed"
      :class="onDark ? 'text-primary-200/25' : 'text-primary-400/55'"
    />
  </div>
</template>