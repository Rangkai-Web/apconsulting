<script setup lang="ts">
import { ref, computed } from "vue"
import { useLanguage } from "~/composables/useLanguage"
import { getApiBaseUrl, safeUseFetch } from "~/utils/api"

interface FaqItem {
  id?: number
  question: string
  answer: string
  category?: string
}

const { t, currentLocale } = useLanguage()
const apiBase = getApiBaseUrl()
const openFaqIndex = ref<number | null>(0)

const toggleFaq = (idx: number) => {
  openFaqIndex.value = openFaqIndex.value === idx ? null : idx
}

const { data: apiResponse } = safeUseFetch<{ data: FaqItem[] }>(
  () => `${apiBase}/faqs?lang=${currentLocale.value}`,
  {
    watch: [currentLocale],
  }
)

const faqs = computed<FaqItem[]>(() => {
  if (
    apiResponse.value?.data &&
    Array.isArray(apiResponse.value.data) &&
    apiResponse.value.data.length > 0
  ) {
    return apiResponse.value.data
  }
  return [
    {
      question: t("home.v3.faq.q1"),
      answer: t("home.v3.faq.a1"),
    },
    {
      question: t("home.v3.faq.q2"),
      answer: t("home.v3.faq.a2"),
    },
    {
      question: t("home.v3.faq.q3"),
      answer: t("home.v3.faq.a3"),
    },
    {
      question: t("home.v3.faq.q4"),
      answer: t("home.v3.faq.a4"),
    },
  ]
})
</script>

<template>
  <section class="w-full bg-white py-12 sm:py-14 lg:py-16">
    <div class="mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        <!-- Left Column (5 cols) -->
        <div class="lg:col-span-5" data-reveal="up">
          <p class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#0869B2] mb-2">
            {{ t("home.v3.faq.eyebrow") }}
          </p>
          <h2 class="text-2xl sm:text-4xl font-semibold leading-[1.18] tracking-[-0.03em] text-[#092F50] mb-3 sm:mb-4">
            {{ t("home.v3.faq.titleNormal") }} <span class="font-bold italic text-[#0869B2]">{{ t("home.v3.faq.titleItalic") }}</span>
          </h2>
          <p class="text-[15px] leading-[1.65] text-[#4E6B85] mb-6">
            {{ t("home.v3.faq.subtitle") }}
          </p>

          <!-- Clean Direct Consultation Box -->
          <div class="bg-[#F4F9FE] border border-[#DCE8F5] rounded-[2px] p-6">
            <h3 class="text-[16px] font-bold text-[#092F50] mb-1.5">
              {{ t("home.v3.faq.boxTitle") }}
            </h3>
            <p class="text-[13.5px] leading-[1.6] text-[#4E6B85] mb-4">
              {{ t("home.v3.faq.boxDesc") }}
            </p>
            <NuxtLink
              to="/contact"
              class="group inline-flex items-center gap-1.5 text-sm font-bold text-[#0869B2] hover:text-[#06477D] transition-colors"
            >
              <span>{{ t("home.v3.faq.boxCta") }}</span>
              <span class="transition-transform group-hover:translate-x-1">↗</span>
            </NuxtLink>
          </div>
        </div>

        <!-- Right Column (7 cols): Clean Editorial Accordion -->
        <div class="lg:col-span-7 border-t border-[#DCE8F5]" data-reveal="up" data-reveal-delay="100">
          <div
            v-for="(faq, index) in faqs"
            :key="index"
            class="border-b border-[#DCE8F5]"
          >
            <button
              type="button"
              class="w-full py-5 flex items-start justify-between gap-4 text-left cursor-pointer group"
              @click="toggleFaq(index)"
            >
              <span
                :class="[
                  'text-[16px] sm:text-[17px] font-semibold leading-[1.45] transition-colors',
                  openFaqIndex === index ? 'text-[#0869B2]' : 'text-[#092F50] group-hover:text-[#0869B2]',
                ]"
              >
                {{ faq.question }}
              </span>
              <span
                :class="[
                  'w-6 h-6 shrink-0 flex items-center justify-center text-lg font-medium leading-none select-none transition-colors mt-0.5',
                  openFaqIndex === index ? 'text-[#0869B2]' : 'text-[#728FA9] group-hover:text-[#0869B2]',
                ]"
              >
                {{ openFaqIndex === index ? "−" : "+" }}
              </span>
            </button>

            <div
              v-show="openFaqIndex === index"
              class="pb-5 pr-8"
            >
              <p class="text-[14.5px] leading-[1.7] text-[#4E6B85] whitespace-pre-line">
                {{ faq.answer }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
