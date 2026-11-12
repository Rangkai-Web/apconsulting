<template>
  <header class="relative z-40 bg-white border-b border-[#E3EAF1]">
    <div
      class="mx-auto flex h-18 sm:h-20 md:h-22 max-w-[1240px] items-center justify-between px-5 sm:px-6 lg:px-8"
    >
      <NuxtLink
        to="/"
        class="group flex items-center gap-3 focus:outline-none"
        aria-label="AP Consulting Homepage"
      >
        <img
          src="/images/redesign/logo.png"
          alt="AP Consulting Logo"
          decoding="async"
          fetchpriority="high"
          class="h-9 sm:h-10 md:h-11 w-auto object-contain navbar-logo animate-logo-entrance"
        />
        <span
          class="text-xs sm:text-[13px] font-semibold text-[#102E47] leading-[1.3]"
        >
          Tax &amp; Consulting<br />Services
        </span>
      </NuxtLink>

      <nav class="hidden items-center gap-7 lg:gap-8 md:flex">
        <NuxtLink
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          :class="[
            'relative py-2 text-sm transition-colors duration-200 after:content-[\'\'] after:absolute after:bottom-0 after:left-0 after:h-[2px] after:w-full after:bg-[#0869B2] after:origin-left after:transition-transform after:duration-300 after:ease-out',
            isActive(item.to)
              ? 'font-bold text-[#0869B2] after:scale-x-100'
              : 'font-medium text-[#102E47] hover:text-[#0869B2] after:scale-x-0 hover:after:scale-x-100',
          ]"
        >
          {{ t(item.labelKey) }}
        </NuxtLink>
      </nav>

      <div class="hidden items-center gap-4 md:flex">
        <NuxtLink
          to="/contact"
          class="group inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-[#0869B2] hover:bg-[#075997] text-white text-sm font-bold rounded-[2px] transition-all duration-200 shadow-sm"
        >
          <span>{{ t("nav.consultation") }}</span>
          <span class="text-sm leading-none transition-transform duration-200 group-hover:translate-x-0.5">→</span>
        </NuxtLink>
      </div>

      <div class="flex items-center gap-3 md:hidden">
        <button
          type="button"
          data-testid="hamburger-button"
          class="flex h-10 w-10 items-center justify-center rounded-[2px] border border-[#DCE8F5] text-[#092F50] hover:bg-[#F4F9FE] transition-colors"
          :aria-expanded="mobileOpen"
          aria-label="Toggle menu"
          @click="mobileOpen = !mobileOpen"
        >
          <UIcon
            :name="mobileOpen ? 'i-heroicons-x-mark' : 'i-heroicons-bars-3'"
            class="size-6 text-[#092F50]"
          />
        </button>
      </div>
    </div>

    <div
      v-if="mobileOpen"
      data-testid="mobile-menu"
      class="absolute top-full left-0 right-0 z-50 flex flex-col gap-1 border-t border-[#DCE8F5] bg-white px-5 py-4 md:hidden shadow-lg"
    >
      <NuxtLink
        v-for="(item, idx) in navItems"
        :key="item.to"
        :to="item.to"
        :class="[
          'py-2.5 text-sm transition-colors',
          idx < navItems.length - 1 ? 'border-b border-[#F0F6FC]' : '',
          isActive(item.to)
            ? 'font-bold text-[#0869B2] border-l-2 border-l-[#0869B2] pl-2.5'
            : 'font-semibold text-[#092F50] hover:text-[#0869B2]',
        ]"
        @click="mobileOpen = false"
      >
        {{ t(item.labelKey) }}
      </NuxtLink>
      <NuxtLink
        to="/contact"
        class="mt-3 inline-flex w-full items-center justify-center gap-2 py-3 bg-[#0869B2] hover:bg-[#075997] text-white text-sm font-bold rounded-[2px] transition-colors"
        @click="mobileOpen = false"
      >
        <span>{{ t("nav.consultation") }}</span>
        <span>→</span>
      </NuxtLink>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRoute } from "vue-router";
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();
const route = useRoute();
const mobileOpen = ref(false);

const navItems = [
  { to: "/", labelKey: "nav.home" },
  { to: "/insight", labelKey: "nav.insights" },
  { to: "/services", labelKey: "nav.services" },
  { to: "/about", labelKey: "nav.about" },
  { to: "/contact", labelKey: "nav.contact" },
];

function isActive(path: string): boolean {
  if (path === "/") {
    return route.path === "/";
  }
  return route.path === path || route.path.startsWith(`${path}/`);
}
</script>
