<template>
  <section class="bg-neutral-50 px-4 py-16 md:px-8 md:py-24">
    <div class="mx-auto max-w-3xl text-center">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">INSIGHTS</p>
      <h2 class="mt-4 text-3xl font-semibold text-secondary-700 md:text-4xl">
        Practical insight for better
        <span class="block italic">business decisions.</span>
      </h2>
      <p class="mt-4 text-neutral-500">
        Clear perspectives on tax, accounting, and financial management, made for businesses
        that want to move forward with confidence.
      </p>
    </div>

    <div class="mx-auto mt-12 grid max-w-7xl gap-6 md:grid-cols-3">
      <article v-for="post in posts" :key="post.title" class="rounded-2xl bg-white p-6 shadow-sm border border-neutral-200/80">
        <img :src="post.image" :alt="post.title" class="h-48 w-full rounded-xl object-contain bg-secondary-50 p-6" />
        <p class="mt-4 text-xs font-bold text-primary-600 uppercase tracking-wide">{{ post.category }}</p>
        <h3 class="mt-1 font-bold text-secondary-700 leading-snug">{{ post.title }}</h3>
        <p class="mt-2 text-xs text-neutral-500 leading-relaxed">{{ post.excerpt }}</p>
        <div class="mt-6 flex items-center justify-between pt-4 border-t border-neutral-100">
          <span class="text-xs text-neutral-400 font-medium">{{ post.date }}</span>
          <UButton data-testid="view-details" color="secondary" variant="solid" size="sm" class="font-bold">
            View Details
          </UButton>
        </div>
      </article>
    </div>
  </section>
</template>

<script setup lang="ts">
const excerpt =
  "Clear perspectives on tax, accounting, and financial management, made for businesses that want to move forward with confidence.";

const posts = [
  {
    image: "/images/icon/Financial_Icon.svg",
    category: "Financial Management",
    title: "What your financial reports are really telling you",
    excerpt,
    date: "Aug 31, 2026",
  },
  {
    image: "/images/icon/Analysis_Icon.svg",
    category: "Accounting",
    title: "Why monthly financial reporting matters",
    excerpt,
    date: "Aug 31, 2026",
  },
  {
    image: "/images/icon/Compliance_Icon.svg",
    category: "Finance",
    title: "A practical way to improve cash-flow visibility",
    excerpt,
    date: "Aug 31, 2026",
  },
];
</script>
