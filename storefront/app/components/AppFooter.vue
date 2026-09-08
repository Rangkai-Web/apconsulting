<template>
  <footer
    class="relative bg-[#F9FAFB] px-4 py-16 md:px-8 border-t border-neutral-200/80 overflow-hidden"
  >
    <!-- Background SVG Decorations -->
    <img
      src="/images/footer/footer1.svg"
      alt=""
      class="absolute top-0 left-0 pointer-events-none z-0"
    />
    <img
      src="/images/footer/footer2.svg"
      alt=""
      class="absolute bottom-0 right-0 pointer-events-none z-0"
    />

    <div
      class="relative z-10 mx-auto grid max-w-7xl gap-8 md:gap-12 md:grid-cols-[2fr_1fr_1fr_1.5fr]"
    >
      <!-- Column 1: Logo, Description & Socials -->
      <div>
        <NuxtLink to="/" class="flex items-center gap-2">
          <img
            src="/pcg_logo.jpeg"
            alt="PCG Logo"
            class="h-24 w-auto object-contain rounded-full"
          />
        </NuxtLink>
        <p
          class="mt-4 max-w-sm text-sm leading-relaxed text-neutral-500 font-normal"
        >
          {{ t('footer.tagline') }}
        </p>
        <div class="mt-6 flex gap-3">
          <a
            v-for="social in socials"
            :key="social.name"
            :href="social.href"
            :aria-label="social.name"
            class="flex size-9 items-center justify-center rounded-full border border-neutral-300 bg-white/60 text-[#083653] hover:border-[#083653] transition-colors"
          >
            <UIcon :name="social.icon" class="size-4" />
          </a>
        </div>
      </div>

      <!-- Column 2: Quick Links -->
      <div>
        <p class="font-semibold text-[#083653] text-base md:text-lg">
          {{ t('footer.quickLinks') }}
        </p>
        <ul class="mt-4 space-y-3 text-sm text-neutral-500 font-normal">
          <li>
            <NuxtLink to="/" class="hover:text-[#083653] transition-colors"
              >{{ t('nav.home') }}</NuxtLink
            >
          </li>
          <li>
            <NuxtLink to="/about" class="hover:text-[#083653] transition-colors"
              >{{ t('nav.about') }}</NuxtLink
            >
          </li>
          <li>
            <NuxtLink
              to="/services"
              class="hover:text-[#083653] transition-colors"
              >{{ t('nav.services') }}</NuxtLink
            >
          </li>
          <li>
            <NuxtLink
              to="/insight"
              class="hover:text-[#083653] transition-colors"
              >{{ t('nav.insights') }}</NuxtLink
            >
          </li>
          <li>
            <NuxtLink
              to="/contact"
              class="hover:text-[#083653] transition-colors"
              >{{ t('nav.contact') }}</NuxtLink
            >
          </li>
        </ul>
      </div>

      <!-- Column 3: Services -->
      <div>
        <p class="font-semibold text-[#083653] text-base md:text-lg">
          {{ t('footer.services') }}
        </p>
        <ul class="mt-4 space-y-3 text-sm text-neutral-500 font-normal">
          <li>Taxation</li>
          <li>Accounting & Bookkeeping</li>
          <li>Financial Services</li>
          <li>Business Support</li>
        </ul>
      </div>

      <!-- Column 4: Contact Us -->
      <div>
        <p class="font-semibold text-[#083653] text-base md:text-lg">
          {{ t('footer.contactUs') }}
        </p>
        <p class="mt-4 flex gap-2.5 text-sm text-neutral-500 font-normal">
          <UIcon
            name="i-heroicons-map-pin"
            class="mt-0.5 size-5 shrink-0 text-[#083653]"
          />
          <span
            >Lt 3 Sayur Sentul, Blk. D, Rukan Cordoba, Jl. Green Lake City
            Boulevard No.23, Kota Tangerang, Banten 15146</span
          >
        </p>
        <p
          class="mt-3 flex items-center gap-2.5 text-sm text-neutral-500 font-normal"
        >
          <UIcon
            name="i-heroicons-envelope"
            class="size-5 shrink-0 text-[#083653]"
          />
          <a href="mailto:pcgserver9@gmail.com" class="hover:underline"
            >pcgserver9@gmail.com</a
          >
        </p>
      </div>
    </div>

    <!-- Bottom Copyright & Policies Bar -->
    <div
      class="relative z-10 mx-auto mt-12 flex max-w-7xl flex-col gap-4 border-t border-neutral-200/80 pt-6 text-xs text-neutral-500 font-normal md:flex-row md:items-center md:justify-between"
    >
      <p class="mx-auto text-center">
        Copyright © {{ new Date().getFullYear() }} Phangkawira Consulting Group.
        {{ t('footer.rights') }}
      </p>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();

const socials = [
  { name: "Instagram", icon: "i-simple-icons-instagram", href: "#" },
  { name: "X", icon: "i-simple-icons-x", href: "#" },
  { name: "Facebook", icon: "i-simple-icons-facebook", href: "#" },
  { name: "LinkedIn", icon: "i-simple-icons-linkedin", href: "#" },
];
</script>
