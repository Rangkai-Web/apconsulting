<template>
  <section class="px-4 py-16 md:px-8 md:py-24">
    <div class="mx-auto max-w-3xl text-center">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">
        {{ tag }}
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span class="font-sans font-normal text-[#083653]">{{
          titlePrefix
        }}</span>
        <span class="font-sans font-semibold italic text-[#083653]"
          >{{ titleHighlight }}
        </span>
        <span class="block font-sans font-normal text-[#083653]">{{
          titleSuffix
        }}</span>
      </h2>
      <p
        class="mt-4 max-w-xl mx-auto text-sm text-neutral-500 leading-relaxed md:text-base"
      >
        {{ subtitle }}
      </p>
    </div>

    <div
      class="mx-auto mt-12 grid max-w-6xl gap-6 md:grid-cols-2 items-stretch"
    >
      <div
        v-for="step in steps"
        :key="step.title"
        class="flex flex-col items-center justify-center p-8 md:p-[42px] border border-[#0000001F] rounded-none shadow-xs text-center"
        style="
          background: radial-gradient(
            41% 81.3% at 50% 100%,
            #e9edff 0%,
            #ffffff 100%
          );
        "
      >
        <div class="flex justify-center items-center">
          <img
            :src="step.icon"
            :alt="step.title"
            class="h-20 w-auto object-contain mx-auto"
          />
        </div>
        <div class="mt-6 flex flex-col gap-2 items-center text-center">
          <h3
            class="text-xl md:text-[22px] font-bold text-[#083653] leading-snug"
          >
            {{ step.title }}
          </h3>
          <p
            class="text-sm text-neutral-500 leading-relaxed font-normal max-w-md mx-auto"
          >
            {{ step.body }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
withDefaults(
  defineProps<{
    tag?: string;
    titlePrefix?: string;
    titleHighlight?: string;
    titleSuffix?: string;
    subtitle?: string;
  }>(),
  {
    tag: "OUR APPROACH",
    titlePrefix: "From ",
    titleHighlight: "Financial Clarity",
    titleSuffix: "to Confident Action.",
    subtitle:
      "A structured approach to help your business stay compliant, organized, and ready for what comes next.",
  },
);

const steps = [
  {
    icon: "/images/icon/Understand_Icon.svg",
    title: "Understand Your Business",
    body: "We assess your financial position, records, and administrative processes to understand where your business stands today.",
  },
  {
    icon: "/images/icon/Identify_Icon.svg",
    title: "Identify What Matters",
    body: "We identify risks, gaps, and priorities, then focus on the areas that need attention first.",
  },
  {
    icon: "/images/icon/Solutions_Icon.svg",
    title: "Put Solutions in Place",
    body: "We implement the agreed tax, accounting, and financial solutions based on your business needs.",
  },
  {
    icon: "/images/icon/Review_Icon.svg",
    title: "Review and Move Forward",
    body: "We review progress regularly and provide ongoing support as your business evolves.",
  },
];
</script>
