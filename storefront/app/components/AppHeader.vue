<template>
  <header class="relative z-40 bg-transparent">
    <div
      class="mx-auto flex h-20 md:h-24 max-w-7xl items-center justify-between px-4 md:px-8"
    >
      <NuxtLink to="/" class="flex items-center gap-2">
        <img
          :src="logoUrl"
          alt="PCG Logo"
          decoding="async"
          fetchpriority="high"
          class="h-16! w-auto object-contain"
        />
      </NuxtLink>

      <nav class="hidden items-center gap-8 md:flex">
        <NuxtLink
          to="/"
          class="text-base font-normal text-neutral-500 hover:text-secondary-700 transition-colors"
          >{{ t("nav.home") }}</NuxtLink
        >
        <NuxtLink
          to="/services"
          class="text-base font-normal text-neutral-500 hover:text-secondary-700 transition-colors"
          >{{ t("nav.services") }}</NuxtLink
        >
        <NuxtLink
          to="/insight"
          class="text-base font-normal text-neutral-500 hover:text-secondary-700 transition-colors"
          >{{ t("nav.insights") }}</NuxtLink
        >
        <NuxtLink
          to="/about"
          class="text-base font-normal text-neutral-500 hover:text-secondary-700 transition-colors"
          >{{ t("nav.about") }}</NuxtLink
        >
        <NuxtLink
          to="/contact"
          class="text-base font-normal text-neutral-500 hover:text-secondary-700 transition-colors"
          >{{ t("nav.contact") }}</NuxtLink
        >
      </nav>

      <div class="hidden items-center gap-4 md:flex">
        <LanguageSwitcher />
        <UButton
          to="/contact"
          color="secondary"
          variant="solid"
          size="lg"
          class="font-medium rounded-none"
        >
          {{ t("nav.discussNeeds") }}
        </UButton>
      </div>

      <div class="flex items-center gap-3 md:hidden">
        <LanguageSwitcher />
        <button
          type="button"
          data-testid="hamburger-button"
          class="p-2"
          :aria-expanded="mobileOpen"
          aria-label="Toggle menu"
          @click="mobileOpen = !mobileOpen"
        >
          <UIcon name="i-heroicons-bars-3" class="size-6 text-secondary-700" />
        </button>
      </div>
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
        >{{ t("nav.home") }}</NuxtLink
      >
      <NuxtLink
        to="/services"
        class="text-neutral-600 font-medium"
        @click="mobileOpen = false"
        >{{ t("nav.services") }}</NuxtLink
      >
      <NuxtLink
        to="/insight"
        class="text-neutral-600 font-medium"
        @click="mobileOpen = false"
        >{{ t("nav.insights") }}</NuxtLink
      >
      <NuxtLink
        to="/about"
        class="text-neutral-600 font-medium"
        @click="mobileOpen = false"
        >{{ t("nav.about") }}</NuxtLink
      >
      <NuxtLink
        to="/contact"
        class="text-neutral-600 font-medium"
        @click="mobileOpen = false"
        >{{ t("nav.contact") }}</NuxtLink
      >
      <UButton
        to="/contact"
        color="secondary"
        variant="solid"
        size="lg"
        block
        class="font-medium rounded-none"
        >{{ t("nav.discussNeeds") }}</UButton
      >
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useLanguage } from "~/composables/useLanguage";
import { useCompanySettings } from "~/composables/useCompanySettings";

const { t } = useLanguage();
const mobileOpen = ref(false);
const { logoUrl } = useCompanySettings();
</script>
