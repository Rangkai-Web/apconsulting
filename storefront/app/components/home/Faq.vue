<template>
  <section class="px-4 py-16 md:px-8 md:py-24">
    <div class="mx-auto max-w-3xl text-center">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">
        {{ tag }}
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span class="font-sans font-semibold italic text-[#083653]"
          >{{ titlePrefix }}{{ titleHighlight }}</span
        >

      </h2>
      <p
        class="mt-4 max-w-xl mx-auto text-sm text-neutral-500 leading-relaxed md:text-base"
      >
        {{ subtitle }}
      </p>
    </div>

    <div class="mx-auto mt-12 max-w-4xl space-y-4">
      <div
        v-for="(item, index) in faqList"
        :key="index"
        class="rounded-none bg-[#F5FBFF] border border-[#DFE7ED] overflow-hidden shadow-xs"
      >
        <button
          type="button"
          data-testid="faq-question"
          class="flex w-full items-center justify-between p-6 md:p-7 text-left font-medium text-lg md:text-[20px] text-[#083653] hover:text-[#083653]/90 transition-colors"
          :aria-expanded="openIndexes.includes(index)"
          @click="toggleItem(index)"
        >
          <span>{{ item.question }}</span>
          <UIcon
            :name="
              openIndexes.includes(index)
                ? 'i-heroicons-minus'
                : 'i-heroicons-plus'
            "
            class="size-6 shrink-0 text-[#083653]"
          />
        </button>
        <div
          v-if="openIndexes.includes(index)"
          class="px-6 pb-6 md:px-7 md:pb-7 text-sm md:text-base leading-relaxed text-neutral-500 font-normal pt-2 border-t border-[#DFE7ED]"
        >
          <p>{{ item.answer }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";

export interface FaqItem {
  question: string;
  answer: string;
}

const props = withDefaults(
  defineProps<{
    tag?: string;
    titlePrefix?: string;
    titleHighlight?: string;
    subtitle?: string;
    items?: FaqItem[];
  }>(),
  {
    tag: "FREQUENTLY ASKED QUESTIONS",
    titlePrefix: "Clear answers ",
    titleHighlight: "for your next step.",
    subtitle:
      "Before starting a conversation, here are answers to the questions businesses commonly ask about PCG's services, process, and engagement.",
  },
);

const defaultItems: FaqItem[] = [
  {
    question: "What services does PCG provide?",
    answer:
      "PCG provides professional consulting and advisory support across taxation, accounting and bookkeeping, and other customized financial services. The available scope depends on your business needs and will be discussed before an engagement begins.",
  },
  {
    question: "How does PCG work with us?",
    answer:
      "We start with an initial consultation to assess your business structure, records, and primary challenges. From there, we propose a customized scope of work tailored to your objectives and operational scale.",
  },
  {
    question: "What information should I prepare before contacting PCG?",
    answer:
      "Having basic information ready—such as your company structure, current accounting setup, recent financial reports, or tax documents—helps us understand your situation faster, though you are welcome to contact us even at an early stage.",
  },
  {
    question:
      "Does PCG support ongoing monthly services or only one-off projects?",
    answer:
      "We support both ongoing recurring retainer engagements (such as monthly bookkeeping or tax reporting) as well as project-based advisory services depending on your company's requirements.",
  },
  {
    question: "How is engagement pricing set up?",
    answer:
      "Pricing is customized based on the scope, complexity, and transaction volume of your business operations. We provide a transparent scope and pricing proposal after our initial discussion.",
  },
];

const faqList = computed(() =>
  props.items && props.items.length > 0 ? props.items : defaultItems,
);

const openIndexes = ref<number[]>([]);

const toggleItem = (index: number) => {
  if (openIndexes.value.includes(index)) {
    openIndexes.value = openIndexes.value.filter((i) => i !== index);
  } else {
    openIndexes.value.push(index);
  }
};
</script>
