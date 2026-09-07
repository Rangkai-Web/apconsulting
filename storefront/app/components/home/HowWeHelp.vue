<template>
  <section class="px-4 py-16 md:px-8 md:py-24">
    <div class="mx-auto max-w-7xl md:grid md:grid-cols-2 md:items-start md:gap-12">
      <div>
        <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">HOW WE HELP</p>
        <h2 class="mt-4 text-3xl font-semibold text-secondary-700 md:text-4xl">
          <span class="italic">Your Business Deserves</span>
          <span class="block">More Than Routine Bookkeeping.</span>
        </h2>
      </div>
      <p class="mt-6 text-neutral-500 md:mt-0 md:pt-10">
        We help businesses manage tax, accounting, bookkeeping, and financial reporting with
        clarity and structure, so you can make confident decisions and focus on growth.
      </p>
    </div>

    <picture class="mx-auto mt-10 block max-w-7xl w-full">
      <source media="(min-width: 768px)" srcset="/images/homepage/HowWeHelp_Desktop.png" />
      <img
        src="/images/homepage/HowWeHelp_Mobile.png"
        alt="PCG team"
        class="w-full rounded-2xl grayscale object-cover"
      />
    </picture>

    <div class="mx-auto mt-12 grid max-w-7xl gap-6 md:grid-cols-3">
      <div v-for="card in cards" :key="card.title" class="rounded-xl border border-neutral-200 bg-gradient-to-b from-secondary-50 to-white p-6 shadow-sm">
        <img :src="card.icon" :alt="card.title" class="size-14" />
        <h3 class="mt-4 text-lg font-bold text-secondary-700">{{ card.title }}</h3>
        <p class="mt-2 text-sm text-neutral-500 leading-relaxed">{{ card.body }}</p>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const cards = [
  {
    icon: "/images/icon/Compliance_Icon.svg",
    title: "Compliance with confidence.",
    body: "Tax compliance, filings, reviews, tax planning, and audit support—handled with care and clarity",
  },
  {
    icon: "/images/icon/Financial_Icon.svg",
    title: "Build a Stronger Financial Foundation",
    body: "Accurate bookkeeping, financial reporting, bank reconciliation, and journal adjustments to keep your business organized.",
  },
  {
    icon: "/images/icon/Analysis_Icon.svg",
    title: "Insight for better decisions.",
    body: "Financial analysis, cash-flow forecasts, budgeting, and performance reviews to help you plan ahead.",
  },
];
</script>
