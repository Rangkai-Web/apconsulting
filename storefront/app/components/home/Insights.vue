<template>
  <section class="bg-[#F9FAFB] px-4 py-16 md:px-[32px] md:py-[80px]">
    <div class="mx-auto max-w-3xl text-center">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">
        INSIGHTS
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span class="font-sans font-normal text-[#083653]"
          >Practical insight for better
        </span>
        <span class="block font-sans font-semibold italic text-[#083653]"
          >business decisions.</span
        >
      </h2>
      <p
        class="mt-4 max-w-xl mx-auto text-sm text-neutral-500 leading-relaxed md:text-base"
      >
        Clear perspectives on tax, accounting, and financial management, made
        for businesses that want to move forward with confidence.
      </p>
    </div>

    <div
      class="mx-auto mt-12 grid max-w-7xl gap-6 md:grid-cols-3 items-stretch"
    >
      <article
        v-for="post in posts"
        :key="post.title"
        class="flex flex-col justify-between rounded-none bg-white p-6 border border-[#0000001F] shadow-xs"
      >
        <div>
          <img
            :src="post.image"
            :alt="post.title"
            class="h-48 w-full rounded-none object-contain bg-[#F8FAFC] p-6 border border-neutral-100"
          />
          <p
            class="mt-4 text-xs font-bold text-[#083653] uppercase tracking-wide"
          >
            {{ post.category }}
          </p>
          <h3 class="mt-2 text-lg font-bold text-[#083653] leading-snug">
            {{ post.title }}
          </h3>
          <p class="mt-2 text-sm text-neutral-500 leading-relaxed font-normal">
            {{ post.excerpt }}
          </p>
        </div>
        <div
          class="mt-6 flex items-center justify-between pt-4 border-t border-neutral-100"
        >
          <span class="text-xs text-neutral-400 font-medium">{{
            post.date
          }}</span>
          <UButton
            data-testid="view-details"
            color="secondary"
            variant="solid"
            size="lg"
            class="rounded-none h-[56px] px-6 py-4 gap-[10px] bg-[#083653] hover:bg-[#083653]/90 text-white font-medium text-sm inline-flex items-center justify-center"
          >
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
