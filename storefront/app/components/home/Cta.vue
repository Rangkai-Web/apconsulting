<template>
  <section
    class="relative bg-[#083653] px-4 py-16 text-center text-white md:px-[32px] md:py-[80px] overflow-hidden"
  >
    <picture class="absolute inset-0 size-full pointer-events-none">
      <source
        media="(min-width: 768px)"
        srcset="/images/homepage/CTA_Desktop.webp"
      />
      <img
        src="/images/homepage/CTA_Mobile.webp"
        alt=""
        aria-hidden="true"
        loading="lazy"
        decoding="async"
        width="1280"
        height="400"
        class="size-full object-cover opacity-60"
      />
    </picture>
    <div class="relative z-10 mx-auto max-w-3xl">
      <p class="text-lg font-semibold uppercase tracking-wide text-[#F4C400]">
        {{ tag || t("cta.tag") }}
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-white sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span class="font-sans font-normal text-white"
          >{{ titleNormal || t("cta.titleNormal") }}
        </span>

        <span class="block font-sans font-semibold italic text-white">{{
          titleItalic || t("cta.titleItalic")
        }}</span>
      </h2>
      <p
        class="mt-4 max-w-xl mx-auto text-sm text-neutral-200 leading-relaxed md:text-base font-normal"
      >
        {{ description || t("cta.description") }}
      </p>
      <UButton
        :to="buttonLink || '/contact'"
        size="lg"
        class="mt-4 rounded-none h-[56px] px-8 py-4 bg-white text-[#083653] hover:bg-neutral-100 font-medium text-sm inline-flex items-center justify-center"
      >
        {{ buttonText || t("cta.buttonText") }}
      </UButton>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useLanguage } from "~/composables/useLanguage";

const { t } = useLanguage();

defineProps<{
  tag?: string;
  titleNormal?: string;
  titleItalic?: string;
  description?: string;
  buttonText?: string;
  buttonLink?: string;
}>();
</script>
