<template>
  <section class="px-4 py-16 md:px-8 md:py-24">
    <div class="mx-auto max-w-3xl text-center">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">
        {{ tag || t('home.faq.tag') }}
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span class="font-sans font-semibold italic text-[#083653]"
          >{{ titlePrefix || t('home.faq.titlePrefix') }}{{ titleHighlight || t('home.faq.titleHighlight') }}</span
        >
      </h2>
      <p
        class="mt-4 max-w-xl mx-auto text-sm text-neutral-500 leading-relaxed md:text-base"
      >
        {{ subtitle || t('home.faq.subtitle') }}
      </p>
    </div>

    <!-- Skeleton Loading State -->
    <div v-if="pending && !apiResponse" class="mx-auto mt-12 max-w-4xl space-y-4">
      <div
        v-for="n in 4"
        :key="n"
        class="h-[72px] w-full animate-pulse bg-neutral-100 border border-[#DFE7ED]"
      />
    </div>

    <!-- Empty State -->
    <div
      v-else-if="!faqList || faqList.length === 0"
      class="mx-auto mt-12 max-w-4xl p-12 text-center border border-dashed border-neutral-300 bg-[#F5FBFF]"
    >
      <UIcon name="i-heroicons-question-mark-circle" class="size-12 text-neutral-400 mx-auto" />
      <p class="mt-3 text-base font-medium text-neutral-600">
        {{ currentLocale === 'en' ? 'No FAQs available at the moment.' : 'Belum ada pertanyaan saat ini.' }}
      </p>
    </div>

    <!-- Data State -->
    <div v-else class="mx-auto mt-12 max-w-4xl space-y-4">
      <div
        v-for="(item, index) in faqList"
        :key="item.id || index"
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
import { useLanguage } from "~/composables/useLanguage";
import { getApiBaseUrl, safeUseFetch } from "~/utils/api";

const { t, currentLocale } = useLanguage();

export interface FaqItem {
  id?: number;
  question: string;
  answer: string;
}

defineProps<{
  tag?: string;
  titlePrefix?: string;
  titleHighlight?: string;
  subtitle?: string;
}>();

const apiBase = getApiBaseUrl();

const { data: apiResponse, pending } = safeUseFetch<{ data: FaqItem[] }>(
  () => `${apiBase}/faqs?lang=${currentLocale.value}`,
  {
    key: "faqs-list",
    getCachedData: () => undefined,
    watch: [currentLocale],
  }
);

const defaultItems = computed<FaqItem[]>(() => [
  { question: t("home.faq.q1"), answer: t("home.faq.a1") },
  { question: t("home.faq.q2"), answer: t("home.faq.a2") },
  { question: t("home.faq.q3"), answer: t("home.faq.a3") },
  { question: t("home.faq.q4"), answer: t("home.faq.a4") },
  { question: t("home.faq.q5"), answer: t("home.faq.a5") },
]);

const faqList = computed<FaqItem[]>(() => {
  if (apiResponse.value?.data && apiResponse.value.data.length > 0) {
    return apiResponse.value.data;
  }
  return defaultItems.value;
});

const openIndexes = ref<number[]>([]);

const toggleItem = (index: number) => {
  if (openIndexes.value.includes(index)) {
    openIndexes.value = openIndexes.value.filter((i) => i !== index);
  } else {
    openIndexes.value.push(index);
  }
};
</script>


