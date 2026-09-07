<template>
  <section class="bg-secondary-700 px-4 py-16 text-white md:px-8 md:py-24">
    <div class="mx-auto max-w-7xl md:flex md:items-end md:justify-between">
      <div class="max-w-xl">
        <p class="text-xs sm:text-sm font-semibold uppercase tracking-wider text-primary-500">
          PCG AT A GLANCE
        </p>
        <h2 class="mt-4 text-3xl font-normal leading-[1.2] text-white sm:text-4xl md:text-[44px]">
          <span class="font-serif italic font-semibold">A structured foundation</span>
          <span class="block">for better business decisions.</span>
        </h2>
      </div>
      <p class="mt-6 max-w-xs text-sm text-neutral-300 md:mt-0 md:max-w-sm md:text-right leading-relaxed">
        Key facts about our service model and approach—presented without unsupported client or
        performance claims.
      </p>
    </div>

    <div class="mx-auto mt-14 grid max-w-7xl grid-cols-2 gap-y-8 divide-y divide-white/20 md:grid-cols-4 md:gap-y-0 md:divide-y-0 md:divide-x md:divide-white/20">
      <div v-for="stat in stats" :key="stat.label" class="px-4 py-6 text-center">
        <p class="text-4xl font-extrabold text-white md:text-5xl">{{ stat.value }}</p>
        <p class="mt-3 text-sm text-neutral-300 leading-normal">{{ stat.label }}</p>
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
