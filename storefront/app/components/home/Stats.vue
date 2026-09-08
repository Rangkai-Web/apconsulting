<template>
  <section class="bg-[#083653] px-4 py-16 text-white md:px-[32px] md:py-[80px]">
    <div class="mx-auto max-w-7xl md:flex md:items-end md:justify-between">
      <div class="max-w-2xl">
        <p
          class="text-xs sm:text-sm font-semibold uppercase tracking-wider text-[#F4C400]"
        >
          PCG AT A GLANCE
        </p>
        <h2
          class="mt-4 text-3xl font-normal text-white sm:text-4xl md:text-[48px] leading-[1.2]"
        >
          <span class="font-sans font-semibold italic text-white"
            >A structured foundation
          </span>
          <span class="block font-sans font-normal text-white"
            >for better business decisions.</span
          >
        </h2>
      </div>
      <p
        class="mt-4 max-w-sm text-sm text-neutral-300 leading-relaxed md:mt-0 md:text-right font-normal"
      >
        Key facts about our service model and approach—presented without
        unsupported client or performance claims.
      </p>
    </div>

    <div
      class="mx-auto mt-12 grid max-w-7xl grid-cols-1 gap-8 md:grid-cols-4 md:gap-y-0 md:divide-x md:divide-white/20"
    >
      <div
        v-for="stat in stats"
        :key="stat.label"
        class="flex flex-col items-center justify-center p-4 text-center"
      >
        <p class="text-4xl font-extrabold text-white md:text-5xl">
          {{ stat.value }}
        </p>
        <p class="mt-2 text-sm text-neutral-300 leading-normal font-normal">
          {{ stat.label }}
        </p>
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
