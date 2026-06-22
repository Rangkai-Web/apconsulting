<script setup lang="ts">
defineProps<{
  icon?: string;
  title: string;
  description?: string;
  actionLabel?: string;
  actionTo?: string;
}>();
</script>

<template>
  <div class="flex flex-col items-center justify-center text-center py-16 px-4">
    <div
      class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4"
    >
      <UIcon
        :name="icon ?? 'i-heroicons-inbox'"
        class="w-8 h-8 text-gray-400"
      />
    </div>
    <h3 class="text-base font-semibold text-gray-900">{{ title }}</h3>
    <p v-if="description" class="text-sm text-gray-500 mt-1 max-w-xs">
      {{ description }}
    </p>
    <UButton
      v-if="actionLabel && actionTo"
      :to="actionTo"
      color="primary"
      class="mt-5 rounded-full cursor-pointer py-2 px-4"
    >
      {{ actionLabel }}
    </UButton>
  </div>
</template>
