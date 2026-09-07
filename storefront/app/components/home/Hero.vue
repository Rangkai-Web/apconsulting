<template>
  <section class="relative overflow-hidden bg-white px-4 py-16 md:px-8 md:py-24">
    <img
      src="/images/homepage/Globe_Desktop.png"
      alt=""
      aria-hidden="true"
      class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-60"
    />

    <div class="relative mx-auto grid max-w-7xl items-center gap-12 md:grid-cols-2">
      <div>
        <h1 class="text-4xl font-extrabold uppercase leading-tight text-secondary-700 md:text-6xl">
          CLEARER FINANCES.
          <span class="block italic text-primary-500">STRONGER BUSINESS DECISIONS</span>
          DECISIONS.
        </h1>
        <p class="mt-6 max-w-md text-neutral-500">
          PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs
          with a structured, professional approach tailored to their goals
        </p>
        <div class="mt-8 flex flex-col gap-4 sm:flex-row">
          <UButton color="secondary" variant="outline" size="lg">EXPLORE OUR SERVICES</UButton>
          <UButton color="primary" variant="solid" size="lg">DISCUSS YOUR NEEDS</UButton>
        </div>
      </div>

      <div class="flex items-end gap-4">
        <img src="/images/homepage/HeroContent_Desktop.png" alt="PCG — clearer finances, stronger business decisions" class="w-full" />
      </div>
    </div>
  </section>
</template>

<script setup lang="ts"></script>
