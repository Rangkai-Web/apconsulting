<script setup lang="ts">
withDefaults(
  defineProps<{
    onDark?: boolean;
  }>(),
  { onDark: false },
);
</script>

<template>
  <div class="absolute inset-0 z-10 pointer-events-none" aria-hidden="true">
    <UIcon
      name="i-lucide-sparkles"
      class="absolute top-3 right-3 w-10 h-10 rotate-12"
      :class="onDark ? 'text-white/10' : 'text-primary-500/15'"
    />
    <UIcon
      name="i-lucide-heart"
      class="absolute top-1/3 left-2 w-7 h-7 -rotate-12"
      :class="onDark ? 'text-white/10' : 'text-primary-400/15'"
    />
    <UIcon
      name="i-lucide-flower-2"
      class="absolute top-2/3 right-4 w-8 h-8 rotate-45"
      :class="onDark ? 'text-white/10' : 'text-primary-300/15'"
    />
    <UIcon
      name="i-lucide-star"
      class="absolute bottom-2 left-1/4 w-9 h-9 rotate-6"
      :class="onDark ? 'text-white/10' : 'text-primary-500/15'"
    />
    <UIcon
      name="i-lucide-sparkles"
      class="absolute bottom-1/4 right-1/4 w-6 h-6 -rotate-6"
      :class="onDark ? 'text-white/10' : 'text-primary-400/15'"
    />
    <UIcon
      name="i-lucide-heart"
      class="absolute top-2 left-1/3 w-5 h-5 rotate-6"
      :class="onDark ? 'text-white/10' : 'text-primary-300/15'"
    />
  </div>
</template>