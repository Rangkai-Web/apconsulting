<template>
  <section id="services" class="bg-[#F4F9FE] py-12 sm:py-14 lg:py-16 border-b border-[#DCEAF7]">
    <div class="mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <!-- Top Header Bar -->
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 sm:gap-6 mb-8 sm:mb-10">
        <div class="flex flex-col gap-2">
          <p class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#0869B2]">
            {{ t("home.v3.services.eyebrow") }}
          </p>
          <h2
            class="text-2xl sm:text-4xl font-semibold leading-[1.18] tracking-[-0.03em] text-[#092F50]"
          >
            <span class="font-bold">{{ t("home.v3.services.titleBold") }}</span> <span class="italic text-[#0869B2]">{{ t("home.v3.services.titleItalic") }}</span>
          </h2>
        </div>

        <NuxtLink
          to="/services"
          class="group inline-flex items-center gap-1.5 text-sm font-bold text-[#0869B2] hover:text-[#06477D] transition-colors"
        >
          <span>{{ t("home.v3.services.explore") }}</span>
          <span class="transition-transform group-hover:translate-x-1">→</span>
        </NuxtLink>
      </div>

      <!-- 3-Column Architectural Service Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6">
        <NuxtLink
          v-for="service in services"
          :key="service.index"
          :to="service.href"
          class="group flex flex-col justify-between bg-white p-6 sm:p-8 rounded-[2px] border border-[#DCE8F5] hover:border-[#0869B2] transition-colors duration-200"
        >
          <div>
            <!-- Top Row: Clean Index + Icon -->
            <div class="flex items-start justify-between mb-6 sm:mb-8">
              <span class="text-xs font-bold tracking-[0.14em] text-[#0869B2]">
                {{ service.index }}
              </span>
              <img
                :src="service.icon"
                :alt="service.title"
                class="w-9 h-9 sm:w-10 sm:h-10 object-contain"
              />
            </div>

            <!-- Title & Description -->
            <h3 class="text-lg sm:text-[22px] font-semibold text-[#092F50] group-hover:text-[#0869B2] transition-colors mb-2.5 sm:mb-3">
              {{ service.title }}
            </h3>
            <p class="text-sm sm:text-[15px] text-[#52687B] leading-[1.65]">
              {{ service.description }}
            </p>
          </div>

          <!-- Clean Bottom Action -->
          <div class="mt-6 sm:mt-8 pt-4 border-t border-[#EBF2F9] flex items-center justify-between text-xs font-bold text-[#0869B2]">
            <span>{{ t("home.v3.services.learnMore") }}</span>
            <span class="text-base transition-transform group-hover:translate-x-1">→</span>
          </div>
        </NuxtLink>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();

const services = computed(() => [
  {
    index: "01",
    title: t("home.v3.services.s1Title"),
    description: t("home.v3.services.s1Desc"),
    icon: "/images/redesign/tax-service-icon.png",
    href: "/services#tax",
  },
  {
    index: "02",
    title: t("home.v3.services.s2Title"),
    description: t("home.v3.services.s2Desc"),
    icon: "/images/redesign/payroll-icon.png",
    href: "/services#payroll",
  },
  {
    index: "03",
    title: t("home.v3.services.s3Title"),
    description: t("home.v3.services.s3Desc"),
    icon: "/images/redesign/accounting-icon.png",
    href: "/services#accounting",
  },
]);
</script>
