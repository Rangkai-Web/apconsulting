<template>
  <section class="bg-secondary-700 px-4 py-16 text-white md:px-8 md:py-24">
    <div class="mx-auto max-w-7xl md:flex md:items-end md:justify-between">
      <div class="max-w-xl">
        <p class="text-sm font-semibold uppercase tracking-wide text-primary-500">PCG at a Glance</p>
        <h2 class="mt-4 text-3xl font-bold italic md:text-4xl">
          A structured foundation for better business decisions.
        </h2>
      </div>
      <p class="mt-6 max-w-sm text-neutral-200 md:mt-0 md:text-right">
        Key facts about our service model and approach—presented without unsupported client or
        performance claims.
      </p>
    </div>

    <div class="mx-auto mt-12 grid max-w-7xl grid-cols-2 divide-y divide-neutral-600 md:grid-cols-4 md:divide-x md:divide-y-0">
      <div v-for="stat in stats" :key="stat.label" class="px-4 py-6 text-center">
        <p class="text-4xl font-bold text-primary-500">{{ stat.value }}</p>
        <p class="mt-2 text-neutral-200">{{ stat.label }}</p>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const stats = [
  { value: "4", label: "Core service areas" },
  { value: "4", label: "Steps in our approach" },
  { value: "20", label: "Integrated support partner" },
  { value: "200+", label: "Customers" },
];
</script>
