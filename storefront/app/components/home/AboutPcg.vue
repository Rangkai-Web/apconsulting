<template>
  <section class="px-4 py-16 md:px-8 md:py-24">
    <div class="mx-auto grid max-w-7xl gap-8 md:grid-cols-2 md:items-start">
      <div>
        <p
          class="text-sm font-semibold uppercase tracking-wide text-neutral-400"
        >
          ABOUT PCG
        </p>
        <h2
          class="mt-4 text-3xl font-normal text-secondary-700 md:text-[48px] leading-[1.2] tracking-normal"
        >
          More than accountants.
          <span class="block font-semibold italic text-secondary-700"
            >Partners in your progress.</span
          >
        </h2>
      </div>
      <div class="flex flex-col items-start md:items-end md:pt-10">
        <p
          class="text-base font-normal text-neutral-500 leading-[1.5] tracking-[-0.02em] md:text-right"
        >
          We helps businesses, families, and ambitious individuals make
          confident financial decisions. We turn complex information into clear,
          practical next steps, helping you stay compliant, improve performance,
          and plan for what comes next.
        </p>
        <UButton
          color="primary"
          variant="solid"
          size="lg"
          class="mt-6 font-medium px-6 py-4 h-14 rounded-none bg-primary-500 text-secondary-700 hover:bg-primary-600 transition-colors"
        >
          DISCOVER HOW WE HELP
        </UButton>
      </div>
    </div>

    <picture class="mx-auto mt-12 block max-w-7xl w-full">
      <source
        media="(min-width: 768px)"
        srcset="/images/homepage/About_Desktop.png"
      />
      <img
        src="/images/homepage/About_Mobile.png"
        alt="PCG team reviewing financial documents"
        class="w-full object-cover shadow-sm"
      />
    </picture>
  </section>
</template>

<script setup lang="ts"></script>
