<template>
  <section class="px-4 py-16 md:px-8 md:py-24">
    <div class="mx-auto max-w-3xl text-center">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">
        FREQUENTLY ASKED QUESTIONS
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span class="font-sans font-normal text-[#083653]">Clear answers </span>
        <span class="font-sans font-semibold italic text-[#083653]"
          >for your next step.</span
        >
      </h2>
      <p
        class="mt-4 max-w-xl mx-auto text-sm text-neutral-500 leading-relaxed md:text-base"
      >
        Before starting a conversation, here are answers to the questions
        businesses commonly ask about PCG's services, process, and engagement.
      </p>
    </div>

    <div
      class="mx-auto mt-12 max-w-4xl rounded-none bg-[#F5FBFF] border border-[#DFE7ED] overflow-hidden shadow-xs"
    >
      <button
        type="button"
        data-testid="faq-question"
        class="flex w-full items-center justify-between p-6 md:p-7 text-left font-medium text-lg md:text-[20px] text-[#083653] hover:text-[#083653]/90 transition-colors"
        :aria-expanded="open"
        @click="open = !open"
      >
        <span>What services does PCG provide?</span>
        <UIcon
          :name="open ? 'i-heroicons-minus' : 'i-heroicons-plus'"
          class="size-6 shrink-0 text-[#083653]"
        />
      </button>
      <div
        v-if="open"
        class="px-6 pb-6 md:px-7 md:pb-7 text-sm md:text-base leading-relaxed text-neutral-500 font-normal pt-2 border-t border-[#DFE7ED]"
      >
        <p>
          PCG provides professional consulting and advisory support across
          taxation, accounting and bookkeeping, and other customized financial
          services. The available scope depends on your business needs and will
          be discussed before an engagement begins.
        </p>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from "vue";

const open = ref(false);
</script>
