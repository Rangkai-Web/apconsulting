<template>
  <section class="px-4 py-16 md:px-8 md:py-24">
    <div class="mx-auto max-w-7xl">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">WHY BUSINESSES CHOOSE US</p>
      <h2 class="mt-4 max-w-2xl text-3xl font-semibold text-secondary-700 md:text-4xl">
        <span class="italic">The right support</span> can change the way your business moves
        forward.
      </h2>
      <p class="mt-4 max-w-2xl text-neutral-500">
        Managing a business requires more than keeping records up to date. It requires clear
        information, practical guidance, and a reliable partner who understands the bigger
        picture.
      </p>
    </div>

    <div class="mx-auto mt-10 grid max-w-7xl gap-8 md:grid-cols-2">
      <div v-for="card in cards" :key="card.title" class="relative">
        <picture class="block w-full">
          <source media="(min-width: 768px)" :srcset="card.imageDesktop" />
          <img :src="card.imageMobile" :alt="card.title" class="w-full rounded-2xl grayscale object-cover" />
        </picture>
        <div class="mx-6 -mt-12 relative z-10 max-w-xs rounded-xl bg-white p-5 shadow-lg border border-neutral-100 md:mx-8">
          <div class="flex items-center gap-2">
            <img :src="card.icon" alt="" class="size-6" />
            <p class="font-bold text-secondary-700">{{ card.title }}</p>
          </div>
          <p class="mt-2 text-xs text-neutral-500 leading-relaxed">{{ card.body }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const cards = [
  {
    imageDesktop: "/images/homepage/TaxAdvisory_Desktop.png",
    imageMobile: "/images/homepage/TaxAdvisory_Mobile.png",
    icon: "/images/icon/TaxAdvisory_Icon.svg",
    title: "Tax Advisory",
    body: "Manage obligations, identify risks, and make more informed tax decisions.",
  },
  {
    imageDesktop: "/images/homepage/Finance&Consulting_Desktop.png",
    imageMobile: "/images/homepage/Finance&Consulting_Mobile.png",
    icon: "/images/icon/FinanceConsult_Icon.svg",
    title: "Finance & Consulting",
    body: "Use financial visibility to plan ahead and build sustainable growth.",
  },
];
</script>
