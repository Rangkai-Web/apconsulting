<template>
  <section
    class="relative bg-[#083653] px-4 py-16 text-center text-white md:px-[32px] md:py-[80px] overflow-hidden"
  >
    <picture class="absolute inset-0 size-full pointer-events-none">
      <source
        media="(min-width: 768px)"
        srcset="/images/homepage/CTA_Desktop.png"
      />
      <img
        src="/images/homepage/CTA_Mobile.png"
        alt=""
        class="size-full object-cover opacity-60"
      />
    </picture>
    <div class="relative z-10 mx-auto max-w-3xl">
      <p class="text-sm font-semibold uppercase tracking-wide text-[#F4C400]">
        {{ tag }}
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-white sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span v-if="titleNormal" class="font-sans font-normal text-white"
          >{{ titleNormal }}
        </span>

        <span
          v-if="titleItalic"
          class="block font-sans font-semibold italic text-white"
          >{{ titleItalic }}</span
        >
      </h2>
      <p
        class="mt-4 max-w-xl mx-auto text-sm text-neutral-200 leading-relaxed md:text-base font-normal"
      >
        {{ description }}
      </p>
      <UButton
        :to="buttonLink"
        size="lg"
        class="mt-4 rounded-none h-[56px] px-8 py-4 bg-white text-[#083653] hover:bg-neutral-100 font-medium text-sm inline-flex items-center justify-center"
      >
        {{ buttonText }}
      </UButton>
    </div>
  </section>
</template>

<script setup lang="ts">
withDefaults(
  defineProps<{
    tag?: string;
    titleNormal?: string;
    titleItalic?: string;
    description?: string;
    buttonText?: string;
    buttonLink?: string;
  }>(),
  {
    tag: "START A CONVERSATION",
    titleNormal: "Ready to bring more clarity to your business?",
    titleItalic: "Let's take the next step together.",
    description:
      "Tell us about your tax, accounting, or financial needs. PCG will help you identify a practical starting point for your business.",
    buttonText: "Discuss Your Needs",
    buttonLink: "/contact",
  },
);
</script>
