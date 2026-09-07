<template>
  <section class="px-4 py-16 md:px-8 md:py-24">
    <div class="mx-auto max-w-3xl text-center">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">FREQUENTLY ASKED QUESTIONS</p>
      <h2 class="mt-4 text-3xl font-semibold italic text-secondary-700 md:text-4xl">
        Clear answers for your next step.
      </h2>
      <p class="mt-4 text-neutral-500">
        Before starting a conversation, here are answers to the questions businesses commonly
        ask about PCG's services, process, and engagement.
      </p>
    </div>

    <div class="mx-auto mt-10 max-w-4xl rounded-2xl bg-secondary-50 border border-secondary-100 overflow-hidden shadow-xs">
      <button
        type="button"
        data-testid="faq-question"
        class="flex w-full items-center justify-between p-6 text-left font-bold text-secondary-700 hover:text-secondary-900 transition-colors"
        :aria-expanded="open"
        @click="open = !open"
      >
        <span>What services does PCG provide?</span>
        <UIcon :name="open ? 'i-heroicons-minus' : 'i-heroicons-plus'" class="size-5 shrink-0 text-secondary-700" />
      </button>
      <p v-if="open" class="px-6 pb-6 text-sm leading-relaxed text-neutral-600">
        PCG provides professional consulting and advisory support across taxation, accounting
        and bookkeeping, and other customized financial services. The available scope depends
        on your business needs and will be discussed before an engagement begins.
      </p>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from "vue";

const open = ref(false);
</script>
