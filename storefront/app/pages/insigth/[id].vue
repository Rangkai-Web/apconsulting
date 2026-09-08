<template>
  <div class="bg-white py-12 md:py-20">
    <div class="mx-auto max-w-4xl px-4 md:px-8">
      <!-- Back Navigation -->
      <NuxtLink
        to="/insight"
        class="inline-flex items-center gap-2 text-xs md:text-sm font-semibold uppercase tracking-wider text-[#083653] hover:underline"
      >
        <UIcon name="i-heroicons-arrow-left" class="size-4" />
        Back to Insights
      </NuxtLink>

      <!-- Article Header -->
      <div class="mt-6">
        <span class="inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider bg-[#FFBB00] text-[#083653]">
          {{ article.category }}
        </span>

        <h1 class="mt-4 text-3xl font-bold text-[#083653] sm:text-4xl md:text-[44px] leading-tight">
          {{ article.title }}
        </h1>

        <div class="mt-4 flex flex-wrap items-center gap-4 text-xs sm:text-sm text-neutral-500 font-normal">
          <span class="font-semibold text-[#083653]">By {{ article.author }}</span>
          <span class="flex items-center gap-1.5">
            <UIcon name="i-heroicons-calendar" class="size-4" />
            {{ article.date }}
          </span>
          <span class="flex items-center gap-1.5">
            <UIcon name="i-heroicons-clock" class="size-4" />
            {{ article.readTime }}
          </span>
        </div>
      </div>

      <!-- Featured Image -->
      <div class="mt-8 overflow-hidden aspect-[16/9] border border-[#DFE7ED]">
        <img
          :src="article.image"
          :alt="article.title"
          class="size-full object-cover"
        />
      </div>

      <!-- Article Body -->
      <div class="mt-8 text-neutral-700 text-base md:text-lg leading-relaxed font-normal space-y-6 border-b border-[#DFE7ED] pb-12">
        <p>
          Managing corporate tax compliance and financial records effectively is essential for organizational stability and sustainable growth. Companies operating in competitive markets face evolving regulatory frameworks that require structured oversight and continuous monitoring.
        </p>

        <h2 class="text-2xl font-bold text-[#083653] pt-4">
          Strategic Tax Planning & Risk Oversight
        </h2>

        <p>
          Proactive tax advisory allows businesses to structure financial transactions efficiently while mitigating legal and regulatory risks. From periodic tax reporting to statutory audits, transparent financial record-keeping builds credibility with tax authorities and financial partners.
        </p>

        <blockquote class="p-6 bg-[#F5FBFF] border-l-4 border-[#083653] text-[#083653] italic font-medium">
          "Proper financial administration turns compliance from a routine obligation into a strategic asset for business decision-making."
        </blockquote>

        <h2 class="text-2xl font-bold text-[#083653] pt-4">
          Key Takeaways for Business Leaders
        </h2>

        <ul class="list-disc pl-6 space-y-2 text-neutral-700 font-normal">
          <li>Establish standardized monthly financial reconciliation processes.</li>
          <li>Conduct periodic tax risk assessments prior to statutory filing deadlines.</li>
          <li>Consult with specialized advisory partners to optimize tax planning strategies.</li>
        </ul>
      </div>

      <!-- Bottom Navigation -->
      <div class="mt-8 flex items-center justify-between">
        <NuxtLink
          to="/insight"
          class="inline-flex items-center gap-2 text-sm font-semibold text-[#083653] hover:underline"
        >
          <UIcon name="i-heroicons-arrow-left" class="size-4" />
          Explore More Insights
        </NuxtLink>

        <UButton
          to="/contact"
          color="primary"
          variant="solid"
          size="md"
          class="rounded-none bg-[#FFBB00] text-[#083653] hover:bg-[#FFBB00]/90 font-medium"
        >
          Discuss Your Needs
        </UButton>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useRoute } from "vue-router";

definePageMeta({
  alias: ["/insight/:id", "/insights/:id"],
});

const route = useRoute();

const article = computed(() => {
  return {
    id: route.params.id || "navigating-corporate-tax-compliance",
    title: "Navigating Corporate Tax Compliance and Minimizing Business Risks",
    category: "Taxation Services",
    author: "PCG Editorial Team",
    date: "September 10, 2026",
    readTime: "5 min read",
    image: "/images/insight/Hero_Desktop.png",
  };
});

useHead({
  title: article.value.title,
  meta: [
    {
      name: "description",
      content:
        "Discover how managing your tax obligations properly and structuring your financial administration can support your business development.",
    },
  ],
});
</script>
