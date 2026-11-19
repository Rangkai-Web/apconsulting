<template>
  <section
    id="scope-of-support"
    class="bg-[#F4F9FE] py-12 sm:py-16 lg:py-20 border-b border-[#DCE8F5]"
  >
    <div class="mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="max-w-[720px] mb-10 sm:mb-12" data-reveal="up">
        <p
          class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#0869B2] mb-2"
        >
          {{ t("services.scope.tag") }}
        </p>
        <h2
          class="text-2xl sm:text-4xl font-semibold leading-[1.18] tracking-[-0.03em] text-[#092F50] mb-3.5"
        >
          <span class="font-bold italic text-[#0869B2]">{{
            t("services.scope.titleItalic")
          }}</span>
          {{ " " }}
          <span class="font-semibold text-[#092F50]">{{
            t("services.scope.titleNormal")
          }}</span>
        </h2>
        <p class="text-sm sm:text-[15.5px] leading-[1.65] text-[#52687B]">
          {{ t("services.scope.description") }}
        </p>
      </div>

      <!-- 3 Core Services Architectural Cards -->
      <div v-if="coreServices.length > 0" class="space-y-6 sm:space-y-8">
        <article
          v-for="(service, idx) in coreServices"
          :id="service.anchorId"
          :key="service.number"
          class="scroll-mt-24 hover-lift bg-white rounded-[2px] border border-[#DCE8F5] hover:border-[#0869B2] transition-colors duration-200 overflow-hidden"
          data-reveal="up"
          :data-reveal-delay="idx * 120"
        >
          <div class="grid grid-cols-1 lg:grid-cols-12">
            <!-- Left Column: Service Overview & CTA -->
            <div
              class="lg:col-span-5 p-6 sm:p-8 lg:p-10 flex flex-col justify-between bg-white border-b lg:border-b-0 lg:border-r border-[#DCE8F5]"
            >
              <div>
                <!-- Top Index & Icon -->
                <div class="flex items-center justify-between mb-6">
                  <span
                    class="inline-flex items-center justify-center px-3 py-1 rounded-[2px] bg-[#F0F7FF] border border-[#DCE8F5] text-xs font-bold tracking-[0.14em] text-[#0869B2]"
                  >
                    {{ service.number }}
                  </span>
                  <img
                    :src="service.icon"
                    :alt="service.title"
                    class="w-10 h-10 sm:w-11 sm:h-11 object-contain"
                    loading="lazy"
                  />
                </div>

                <!-- Service Title -->
                <h3
                  class="text-xl sm:text-2xl lg:text-[26px] font-bold leading-[1.25] tracking-[-0.02em] text-[#092F50] mb-3.5"
                >
                  {{ service.title }}
                </h3>

                <!-- Main Description from AP Consulting -->
                <p
                  class="text-sm sm:text-[15px] leading-[1.7] text-[#4E6B85]"
                >
                  {{ service.description }}
                </p>
              </div>

              <!-- Bottom CTA Link -->
              <div class="mt-7 pt-5 border-t border-[#EBF2F9]">
                <NuxtLink
                  :to="`/contact?service=${encodeURIComponent(service.anchorId)}`"
                  class="group inline-flex items-center gap-2 text-xs sm:text-sm font-bold uppercase tracking-[0.08em] text-[#0869B2] hover:text-[#06477D] transition-colors"
                >
                  <span>{{ service.cta }}</span>
                  <span
                    class="text-base leading-none transition-transform duration-200 group-hover:translate-x-1"
                  >
                    →
                  </span>
                </NuxtLink>
              </div>
            </div>

            <!-- Right Column: Detailed Sub-Services Breakdown -->
            <div
              class="lg:col-span-7 p-6 sm:p-8 lg:p-10 bg-[#FAFCFF] flex flex-col justify-center"
            >
              <p
                class="text-xs font-bold tracking-[0.1em] uppercase text-[#0869B2] mb-4 sm:mb-5"
              >
                {{ t("services.scope.includedLabel") }}
              </p>

              <div class="divide-y divide-[#E3EDF7]">
                <div
                  v-for="(sub, sIdx) in service.subServices"
                  :key="sIdx"
                  class="flex items-start gap-3.5 py-4 sm:py-4.5 first:pt-0 last:pb-0"
                >
                  <!-- Checkmark Badge -->
                  <span
                    class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-[2px] bg-[#E6F2FC] text-[#0869B2]"
                  >
                    <svg
                      aria-hidden="true"
                      width="12"
                      height="12"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2.6"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <path d="M20 6L9 17l-5-5" />
                    </svg>
                  </span>

                  <div>
                    <h4
                      class="text-[15px] sm:text-base font-bold text-[#092F50] mb-1"
                    >
                      {{ sub.title }}
                    </h4>
                    <p
                      v-if="sub.description"
                      class="text-xs sm:text-[14px] leading-[1.65] text-[#52687B]"
                    >
                      {{ sub.description }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </article>
      </div>

      <!-- Empty State when no services are active -->
      <div
        v-else-if="!pending"
        class="p-12 text-center border border-dashed border-[#B9D3EB] rounded-[2px] bg-white"
      >
        <UIcon
          name="i-heroicons-briefcase"
          class="size-11 text-[#728FA9] mx-auto"
        />
        <h3 class="mt-3 text-lg font-bold text-[#092F50]">
          {{ currentLocale === "en" ? "No services available at this time." : "Belum ada layanan yang aktif saat ini." }}
        </h3>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useLanguage } from "~/composables/useLanguage";
import { useServiceCategories } from "~/composables/useServiceCategories";

const { t, currentLocale } = useLanguage();
const { categories, pending } = useServiceCategories();

const defaultIcons = [
  "/images/redesign/tax-service-icon.png",
  "/images/redesign/payroll-icon.png",
  "/images/redesign/accounting-icon.png",
];
const defaultAnchors = ["tax", "payroll", "accounting"];

const defaultSubServices = [
  [
    {
      title: t("services.scope.s1Sub1Title"),
      description: t("services.scope.s1Sub1Desc"),
    },
    {
      title: t("services.scope.s1Sub2Title"),
      description: t("services.scope.s1Sub2Desc"),
    },
    {
      title: t("services.scope.s1Sub3Title"),
      description: t("services.scope.s1Sub3Desc"),
    },
  ],
  [
    {
      title: t("services.scope.s2Sub1Title"),
      description: t("services.scope.s2Sub1Desc"),
    },
    {
      title: t("services.scope.s2Sub2Title"),
      description: t("services.scope.s2Sub2Desc"),
    },
    {
      title: t("services.scope.s2Sub3Title"),
      description: t("services.scope.s2Sub3Desc"),
    },
  ],
  [
    {
      title: t("services.scope.s3Sub1Title"),
      description: t("services.scope.s3Sub1Desc"),
    },
    {
      title: t("services.scope.s3Sub2Title"),
      description: t("services.scope.s3Sub2Desc"),
    },
    {
      title: t("services.scope.s3Sub3Title"),
      description: t("services.scope.s3Sub3Desc"),
    },
    {
      title: t("services.scope.s3Sub4Title"),
      description: t("services.scope.s3Sub4Desc"),
    },
  ],
];

const coreServices = computed(() => {
  const isEn = currentLocale.value === "en";
  return categories.value.map((cat, idx) => {
    const rawFeatures = isEn
      ? (cat.features_en && cat.features_en.length > 0 ? cat.features_en : cat.features_id)
      : (cat.features_id && cat.features_id.length > 0 ? cat.features_id : cat.features_en);

    const subServices = Array.isArray(rawFeatures) && rawFeatures.length > 0
      ? rawFeatures.map((f) => ({ title: f, description: "" }))
      : (defaultSubServices[idx] || []);

    return {
      number: String(idx + 1).padStart(2, "0"),
      anchorId: defaultAnchors[idx] || `service-${cat.id}`,
      icon: defaultIcons[idx % defaultIcons.length],
      title: isEn ? (cat.name_en || cat.name_id) : (cat.name_id || cat.name_en),
      description: isEn ? (cat.description_en || cat.description_id || "") : (cat.description_id || cat.description_en || ""),
      cta: isEn
        ? (cat.cta_text_en || "DISCUSS THIS SERVICE")
        : (cat.cta_text_id || "KONSULTASIKAN LAYANAN INI"),
      subServices,
    };
  });
});
</script>
