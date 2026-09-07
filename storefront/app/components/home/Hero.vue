<template>
  <section class="relative overflow-hidden bg-white px-4 py-12 md:px-8 md:py-24">
    <img
      src="/images/homepage/Globe_Desktop.png"
      alt=""
      aria-hidden="true"
      class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-60"
    />

    <div class="relative mx-auto grid max-w-7xl items-center gap-10 md:grid-cols-2 md:gap-12">
      <div>
        <h1 class="text-3xl font-extrabold uppercase leading-tight text-secondary-700 sm:text-4xl md:text-6xl">
          CLEARER FINANCES.
          <span class="block italic text-primary-500">STRONGER BUSINESS DECISIONS</span>
          DECISIONS.
        </h1>
        <p class="mt-4 max-w-md text-sm text-neutral-500 leading-relaxed md:mt-6 md:text-base">
          PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs
          with a structured, professional approach tailored to their goals
        </p>
        <div class="mt-6 flex flex-col gap-3 sm:flex-row md:mt-8 md:gap-4">
          <UButton color="secondary" variant="outline" size="lg" class="w-full justify-center font-bold sm:w-auto">EXPLORE OUR SERVICES</UButton>
          <UButton color="primary" variant="solid" size="lg" class="w-full justify-center font-bold sm:w-auto">DISCUSS YOUR NEEDS</UButton>
        </div>
      </div>

      <div class="flex items-end justify-center">
        <picture class="w-full">
          <source media="(min-width: 768px)" srcset="/images/homepage/HeroContent_Desktop.png" />
          <img
            src="/images/homepage/HeroContent_Mobile.png"
            alt="PCG — clearer finances, stronger business decisions"
            class="w-full object-contain"
          />
        </picture>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts"></script>
