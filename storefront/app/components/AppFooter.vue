<template>
  <footer class="bg-neutral-50 px-4 py-16 md:px-8 border-t border-neutral-200">
    <div class="mx-auto grid max-w-7xl gap-12 md:grid-cols-[2fr_1fr_1fr_1.5fr]">
      <div>
        <NuxtLink to="/" class="flex items-center gap-2">
          <img src="/pcg_logo.jpeg" alt="PCG Logo" class="h-10 w-auto object-contain" />
        </NuxtLink>
        <p class="mt-4 max-w-sm text-sm leading-relaxed text-neutral-500">
          Professional support in tax, accounting, and financial services. We're helping
          businesses move forward with clarity and confidence.
        </p>
        <div class="mt-6 flex gap-3">
          <a
            v-for="social in socials"
            :key="social.name"
            :href="social.href"
            :aria-label="social.name"
            class="flex size-9 items-center justify-center rounded-full border border-neutral-200 text-secondary-700 hover:border-secondary-700 transition-colors"
          >
            <UIcon :name="social.icon" class="size-4" />
          </a>
        </div>
      </div>

      <div>
        <p class="font-bold text-secondary-700">Quick Links</p>
        <ul class="mt-4 space-y-3 text-sm text-neutral-500">
          <li><NuxtLink to="/" class="hover:text-secondary-700">Home</NuxtLink></li>
          <li><NuxtLink to="/about" class="hover:text-secondary-700">About</NuxtLink></li>
          <li><NuxtLink to="/services" class="hover:text-secondary-700">Services</NuxtLink></li>
          <li><NuxtLink to="/insight" class="hover:text-secondary-700">Insights</NuxtLink></li>
          <li><NuxtLink to="/contact" class="hover:text-secondary-700">Contact</NuxtLink></li>
        </ul>
      </div>

      <div>
        <p class="font-bold text-secondary-700">Services</p>
        <ul class="mt-4 space-y-3 text-sm text-neutral-500">
          <li>Taxation</li>
          <li>Accounting & Bookkeeping</li>
          <li>Financial Services</li>
          <li>Business Support</li>
        </ul>
      </div>

      <div>
        <p class="font-bold text-secondary-700">Contact Us</p>
        <p class="mt-4 flex gap-2 text-sm text-neutral-500">
          <UIcon name="i-heroicons-map-pin" class="mt-1 size-4 shrink-0 text-secondary-700" />
          <span>Lt 3 Sayur Sentul, Blk. D, Rukan Cordoba, Jl. Green Lake City Boulevard No.23, Kota Tangerang, Banten 15146</span>
        </p>
        <p class="mt-3 flex items-center gap-2 text-sm text-neutral-500">
          <UIcon name="i-heroicons-envelope" class="size-4 shrink-0 text-secondary-700" />
          <a href="mailto:pcgserver9@gmail.com" class="hover:underline">pcgserver9@gmail.com</a>
        </p>
      </div>
    </div>

    <div class="mx-auto mt-12 flex max-w-7xl flex-col gap-4 border-t border-neutral-200 pt-6 text-xs text-neutral-500 md:flex-row md:items-center md:justify-between">
      <p>Copyright © 2026 Phangkawira Consulting Group. All Rights Reserved</p>
      <div class="flex gap-6">
        <a href="#" class="hover:underline">Privacy Policy</a>
        <a href="#" class="hover:underline">Terms & Conditions</a>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
const socials = [
  { name: "Instagram", icon: "i-simple-icons-instagram", href: "#" },
  { name: "X", icon: "i-simple-icons-x", href: "#" },
  { name: "Facebook", icon: "i-simple-icons-facebook", href: "#" },
  { name: "LinkedIn", icon: "i-simple-icons-linkedin", href: "#" },
];
</script>

