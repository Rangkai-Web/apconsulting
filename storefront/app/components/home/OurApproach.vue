<template>
  <section class="px-4 py-16 md:px-8 md:py-24">
    <div class="mx-auto max-w-3xl text-center">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">OUR APPROACH</p>
      <h2 class="mt-4 text-3xl font-semibold text-secondary-700 md:text-4xl">
        From <span class="italic">Financial Clarity</span> to Confident Action.
      </h2>
      <p class="mt-4 text-neutral-500">
        A structured approach to help your business stay compliant, organized, and ready for
        what comes next.
      </p>
    </div>

    <div class="mx-auto mt-12 grid max-w-5xl gap-6 md:grid-cols-2">
      <div v-for="step in steps" :key="step.title" class="rounded-2xl border border-neutral-200 bg-white p-8 text-center shadow-sm">
        <img :src="step.icon" :alt="step.title" class="mx-auto size-16" />
        <h3 class="mt-4 text-lg font-bold text-secondary-700">{{ step.title }}</h3>
        <p class="mt-2 text-sm text-neutral-500 leading-relaxed">{{ step.body }}</p>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const steps = [
  {
    icon: "/images/icon/Understand_Icon.svg",
    title: "Understand Your Business",
    body: "We assess your financial position, records, and administrative processes to understand where your business stands today.",
  },
  {
    icon: "/images/icon/Identify_Icon.svg",
    title: "Identify What Matters",
    body: "We identify risks, gaps, and priorities, then focus on the areas that need attention first.",
  },
  {
    icon: "/images/icon/Solutions_Icon.svg",
    title: "Put Solutions in Place",
    body: "We implement the agreed tax, accounting, and financial solutions based on your business needs.",
  },
  {
    icon: "/images/icon/Review_Icon.svg",
    title: "Review and Move Forward",
    body: "We review progress regularly and provide ongoing support as your business evolves.",
  },
];
</script>
