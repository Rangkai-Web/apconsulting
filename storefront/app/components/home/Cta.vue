<template>
  <section class="w-full bg-[#F4F9FE] py-10 sm:py-12 lg:py-16 border-t border-[#DCE8F5]">
    <div class="mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <div class="relative rounded-[2px] overflow-hidden bg-[#092F50] border border-[#144673]" data-reveal="up">
        <img
          src="/images/redesign/footer-bg.webp"
          alt=""
          aria-hidden="true"
          class="absolute inset-0 w-full h-full object-cover object-center opacity-20 mix-blend-luminosity pointer-events-none"
          loading="lazy"
        />
        <div
          class="absolute inset-0 pointer-events-none"
          style="background: linear-gradient(90deg, rgba(9, 47, 80, 0.96) 0%, rgba(8, 105, 178, 0.84) 100%);"
        />

        <!-- Banner Content -->
        <div class="relative z-10 px-7 py-10 sm:px-12 sm:py-12 lg:px-14 lg:py-14 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
          <div class="max-w-[600px]">
            <p class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#8CC8F8] mb-2.5">
              {{ t("home.v3.cta.eyebrow") }}
            </p>

            <h2 class="text-2xl sm:text-3xl lg:text-[36px] font-bold leading-[1.18] tracking-[-0.02em] text-white mb-3">
              {{ t("home.v3.cta.titlePrefix") }} <span class="italic text-[#CFE6FA]">{{ t("home.v3.cta.titleItalic") }}</span> {{ t("home.v3.cta.titleSuffix") }}
            </h2>

            <p class="text-[15px] leading-[1.65] text-white/85">
              {{ t("home.v3.cta.description") }}
            </p>
          </div>

          <!-- Action Buttons & Direct Contact -->
          <div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row items-stretch sm:items-center gap-4 shrink-0">
            <NuxtLink
              to="/contact"
              class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-[2px] bg-white hover:bg-[#F4F9FE] text-[#092F50] text-[14px] font-bold transition-colors shadow-sm"
            >
              <span>{{ t("home.v3.cta.primaryBtn") }}</span>
              <span class="text-base leading-none text-[#0869B2]">→</span>
            </NuxtLink>

            <a
              :href="whatsappLink"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-[2px] bg-white/10 hover:bg-white/15 border border-white/35 text-white text-[14px] font-semibold transition-colors"
            >
              <span>{{ t("home.v3.cta.whatsappBtn") }}</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useLanguage } from "~/composables/useLanguage";
import { useCompanySettings } from "~/composables/useCompanySettings";

const { t } = useLanguage();
const { whatsappLink } = useCompanySettings();
</script>
