<script setup lang="ts">
const props = defineProps<{
  modelValue: number;
  max: number;
}>();

const emit = defineEmits<{
  "update:modelValue": [value: number];
}>();

function decrement() {
  if (props.modelValue > 1) emit("update:modelValue", props.modelValue - 1);
}

function increment() {
  if (props.modelValue < props.max)
    emit("update:modelValue", props.modelValue + 1);
}

function onInput(event: Event) {
  const value = Number((event.target as HTMLInputElement).value);
  if (Number.isNaN(value)) return;
  emit("update:modelValue", Math.max(1, Math.min(value, props.max)));
}
</script>

<template>
  <div class="inline-flex items-center border border-gray-300 rounded-lg">
    <UButton
      icon="i-heroicons-minus"
      color="neutral"
      variant="ghost"
      aria-label="Kurangi jumlah"
      :disabled="modelValue <= 1"
      @click="decrement"
    />
    <input
      :value="modelValue"
      type="number"
      min="1"
      :max="max"
      aria-label="Jumlah"
      class="w-12 text-center border-0 focus:ring-0 text-sm"
      @input="onInput"
    />
    <UButton
      icon="i-heroicons-plus"
      color="neutral"
      variant="ghost"
      aria-label="Tambah jumlah"
      :disabled="modelValue >= max"
      @click="increment"
    />
  </div>
</template>
