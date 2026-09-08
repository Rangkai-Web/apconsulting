<template>
  <header class="relative z-40 bg-transparent">
    <div
      class="mx-auto flex h-20 md:h-24 max-w-7xl items-center justify-between px-4 md:px-8"
    >
      <NuxtLink
        to="/"
        class="flex items-center gap-2 text-2xl font-bold text-secondary-700"
      >
        <img
          src="/pcg_logo.jpeg"
          alt="PCG Logo"
          class="h-20 md:h-24 w-auto object-contain"
        />
      </NuxtLink>

      <nav class="hidden items-center gap-8 md:flex">
        <NuxtLink
          to="/"
          class="text-base font-normal text-neutral-500 hover:text-secondary-700 transition-colors"
          >Home</NuxtLink
        >
        <NuxtLink
          to="/services"
          class="text-base font-normal text-neutral-500 hover:text-secondary-700 transition-colors"
          >Services</NuxtLink
        >
        <NuxtLink
          to="/insight"
          class="text-base font-normal text-neutral-500 hover:text-secondary-700 transition-colors"
          >Insights</NuxtLink
        >
        <NuxtLink
          to="/about"
          class="text-base font-normal text-neutral-500 hover:text-secondary-700 transition-colors"
          >About</NuxtLink
        >
        <NuxtLink
          to="/contact"
          class="text-base font-normal text-neutral-500 hover:text-secondary-700 transition-colors"
          >Contact</NuxtLink
        >
      </nav>

      <UButton
        to="/contact"
        color="secondary"
        variant="solid"
        size="lg"
        class="hidden font-medium md:inline-flex rounded-none"
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

    <div
      v-if="mobileOpen"
      data-testid="mobile-menu"
      class="absolute top-full left-0 right-0 z-50 flex flex-col gap-4 border-t border-neutral-200 bg-white/95 px-4 py-4 md:hidden shadow-lg"
    >
      <NuxtLink
        to="/"
        class="text-neutral-600 font-medium"
        @click="mobileOpen = false"
        >Home</NuxtLink
      >
      <NuxtLink
        to="/services"
        class="text-neutral-600 font-medium"
        @click="mobileOpen = false"
        >Services</NuxtLink
      >
      <NuxtLink
        to="/insight"
        class="text-neutral-600 font-medium"
        @click="mobileOpen = false"
        >Insights</NuxtLink
      >
      <NuxtLink
        to="/about"
        class="text-neutral-600 font-medium"
        @click="mobileOpen = false"
        >About</NuxtLink
      >
      <NuxtLink
        to="/contact"
        class="text-neutral-600 font-medium"
        @click="mobileOpen = false"
        >Contact</NuxtLink
      >
      <UButton
        to="/contact"
        color="secondary"
        variant="solid"
        size="lg"
        block
        class="font-medium rounded-none"
        >Discuss Your Needs</UButton
      >
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref } from "vue";

const mobileOpen = ref(false);
</script>
