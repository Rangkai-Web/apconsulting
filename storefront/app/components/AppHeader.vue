<template>
  <header class="sticky top-0 z-40 border-b border-neutral-200 bg-white">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 md:px-8">
      <NuxtLink to="/" class="flex items-center gap-2 text-2xl font-bold text-secondary-700">
        <img src="/pcg_logo.jpeg" alt="PCG Logo" class="h-10 w-auto object-contain" />
      </NuxtLink>

      <nav class="hidden items-center gap-8 md:flex">
        <NuxtLink to="/" class="text-sm font-semibold text-neutral-600 hover:text-secondary-700">Home</NuxtLink>
        <NuxtLink to="/services" class="text-sm font-semibold text-neutral-600 hover:text-secondary-700">Services</NuxtLink>
        <NuxtLink to="/insight" class="text-sm font-semibold text-neutral-600 hover:text-secondary-700">Insights</NuxtLink>
        <NuxtLink to="/about" class="text-sm font-semibold text-neutral-600 hover:text-secondary-700">About</NuxtLink>
        <NuxtLink to="/contact" class="text-sm font-semibold text-neutral-600 hover:text-secondary-700">Contact</NuxtLink>
      </nav>

      <UButton
        to="/contact"
        color="primary"
        variant="solid"
        size="lg"
        class="hidden font-bold md:inline-flex"
      >
        Discuss Your Needs
      </UButton>

      <button
        type="button"
        data-testid="hamburger-button"
        class="p-2 md:hidden"
        :aria-expanded="mobileOpen"
        aria-label="Toggle menu"
        @click="mobileOpen = !mobileOpen"
      >
        <UIcon name="i-heroicons-bars-3" class="size-6 text-secondary-700" />
      </button>
    </div>

    <div v-if="mobileOpen" data-testid="mobile-menu" class="flex flex-col gap-4 border-t border-neutral-200 px-4 py-4 md:hidden">
      <NuxtLink to="/" class="text-neutral-600 font-semibold" @click="mobileOpen = false">Home</NuxtLink>
      <NuxtLink to="/services" class="text-neutral-600 font-semibold" @click="mobileOpen = false">Services</NuxtLink>
      <NuxtLink to="/insight" class="text-neutral-600 font-semibold" @click="mobileOpen = false">Insights</NuxtLink>
      <NuxtLink to="/about" class="text-neutral-600 font-semibold" @click="mobileOpen = false">About</NuxtLink>
      <NuxtLink to="/contact" class="text-neutral-600 font-semibold" @click="mobileOpen = false">Contact</NuxtLink>
      <UButton to="/contact" color="primary" variant="solid" block class="font-bold">Discuss Your Needs</UButton>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref } from "vue";

const mobileOpen = ref(false);
</script>

