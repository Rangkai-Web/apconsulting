<template>
  <section aria-labelledby="sectors-title" class="bg-white py-12 sm:py-14 lg:py-16 border-b border-[#DCE8F5]">
    <div class="mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-10 lg:gap-14 items-start">
        <!-- Left Column: Heading & Confidentiality Commitment -->
        <div class="lg:col-span-5 flex flex-col" data-reveal="up">
          <p class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#0869B2] mb-2">
            {{ t("home.v3.sectors.eyebrow") }}
          </p>
          <h2
            id="sectors-title"
            class="text-2xl sm:text-4xl font-semibold leading-[1.18] tracking-[-0.03em] text-[#092F50] mb-3 sm:mb-4"
          >
            {{ t("home.v3.sectors.titleNormal") }} <span class="font-bold italic text-[#0869B2]">{{ t("home.v3.sectors.titleItalic") }}</span>
          </h2>
          <p class="text-sm sm:text-[15px] leading-[1.65] text-[#52687B] mb-5 sm:mb-6">
            {{ t("home.v3.sectors.description") }}
          </p>

          <!-- Confidentiality Note -->
          <div class="flex items-start gap-3.5 pt-5 border-t border-[#DCE8F5]">
            <svg
              aria-hidden="true"
              width="22"
              height="22"
              viewBox="0 0 24 24"
              fill="none"
              stroke="#0869B2"
              stroke-width="1.7"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="shrink-0 mt-0.5"
            >
              <rect x="5" y="10.5" width="14" height="10" rx="2" />
              <path d="M8 10.5V7.5a4 4 0 018 0v3" />
              <path d="M12 14.5v2.5" />
            </svg>
            <p class="text-[13.5px] sm:text-[14px] leading-[1.6] text-[#092F50]">
              <strong class="font-bold">{{ t("home.v3.sectors.confidentialBold") }}</strong> {{ t("home.v3.sectors.confidentialBody") }}
            </p>
          </div>
        </div>

        <!-- Right Column: 6 Sectors Grid -->
        <div class="lg:col-span-7" data-reveal="up" data-reveal-delay="100">
          <ul class="grid grid-cols-1 sm:grid-cols-2 gap-x-8">
            <!-- 1. Manufacturing -->
            <li class="flex items-center gap-4 py-4.5 sm:py-5 border-t border-b border-[#DCE8F5] text-[15px] sm:text-[17px] font-semibold text-[#092F50]">
              <svg
                aria-hidden="true"
                width="26"
                height="26"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#0869B2"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="shrink-0"
              >
                <path d="M3 21V10l5 3V10l5 3V6l8 4v11z" />
                <path d="M7 17h2M12 17h2M17 17h1" />
              </svg>
              <span>{{ t("home.v3.sectors.item1") }}</span>
            </li>

            <!-- 2. Trading & Distribution -->
            <li class="flex items-center gap-4 py-4.5 sm:py-5 sm:border-t border-b border-[#DCE8F5] text-[15px] sm:text-[17px] font-semibold text-[#092F50]">
              <svg
                aria-hidden="true"
                width="26"
                height="26"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#0869B2"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="shrink-0"
              >
                <path d="M3 7h11v9H3z" />
                <path d="M14 10h4l3 3v3h-7" />
                <circle cx="7" cy="18" r="1.8" />
                <circle cx="17" cy="18" r="1.8" />
              </svg>
              <span>{{ t("home.v3.sectors.item2") }}</span>
            </li>

            <!-- 3. Property & Construction -->
            <li class="flex items-center gap-4 py-4.5 sm:py-5 border-b border-[#DCE8F5] text-[15px] sm:text-[17px] font-semibold text-[#092F50]">
              <svg
                aria-hidden="true"
                width="26"
                height="26"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#0869B2"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="shrink-0"
              >
                <path d="M4 21V9l8-5 8 5v12" />
                <path d="M9 21v-6h6v6" />
                <path d="M2.5 21h19" />
              </svg>
              <span>{{ t("home.v3.sectors.item3") }}</span>
            </li>

            <!-- 4. Food & Beverage -->
            <li class="flex items-center gap-4 py-4.5 sm:py-5 border-b border-[#DCE8F5] text-[15px] sm:text-[17px] font-semibold text-[#092F50]">
              <svg
                aria-hidden="true"
                width="26"
                height="26"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#0869B2"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="shrink-0"
              >
                <path d="M6 3v7a2 2 0 002 2h0a2 2 0 002-2V3" />
                <path d="M8 12v9" />
                <path d="M17 21V3c-2 1.5-3 4-3 7h3" />
              </svg>
              <span>{{ t("home.v3.sectors.item4") }}</span>
            </li>

            <!-- 5. Technology & Startups -->
            <li class="flex items-center gap-4 py-4.5 sm:py-5 border-b border-[#DCE8F5] text-[15px] sm:text-[17px] font-semibold text-[#092F50]">
              <svg
                aria-hidden="true"
                width="26"
                height="26"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#0869B2"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="shrink-0"
              >
                <rect x="3" y="4" width="18" height="12" rx="1.5" />
                <path d="M8 20h8M12 16v4" />
                <path d="M9 9l-2 1.5L9 12M15 9l2 1.5-2 1.5" />
              </svg>
              <span>{{ t("home.v3.sectors.item5") }}</span>
            </li>

            <!-- 6. Foreign-invested (PT PMA) -->
            <li class="flex items-center gap-4 py-4.5 sm:py-5 border-b border-[#DCE8F5] text-[15px] sm:text-[17px] font-semibold text-[#092F50]">
              <svg
                aria-hidden="true"
                width="26"
                height="26"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#0869B2"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="shrink-0"
              >
                <circle cx="12" cy="12" r="9" />
                <path d="M3 12h18" />
                <path d="M12 3c2.5 2.7 3.5 5.7 3.5 9s-1 6.3-3.5 9c-2.5-2.7-3.5-5.7-3.5-9s1-6.3 3.5-9z" />
              </svg>
              <span>{{ t("home.v3.sectors.item6") }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();
</script>
