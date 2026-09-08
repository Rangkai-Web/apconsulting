<template>
  <section class="px-4 py-16 md:px-8 md:py-24 bg-[#FBFEFF]">
    <div class="mx-auto max-w-3xl text-center">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">
        START WITH YOUR CURRENT NEED
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span class="font-sans font-semibold italic text-[#083653]"
          >Where does your business need more clarity?</span
        >
      </h2>
      <p
        class="mt-4 max-w-xl mx-auto text-sm text-neutral-500 leading-relaxed md:text-base"
      >
        Choose the statement below that matches your reality. We can build our
        service scope around where you are right now.
      </p>
    </div>

    <div
      class="mx-auto mt-12 grid max-w-6xl gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 items-stretch"
    >
      <div
        v-for="need in needs"
        :key="need.number"
        class="flex flex-col p-6 md:p-8 border border-[#0000001F] rounded-none shadow-xs text-left justify-between"
        style="
          background: radial-gradient(
            41% 81.3% at 50% 100%,
            #e9edff 0%,
            #ffffff 100%
          );
        "
      >
        <div>
          <span
            class="text-2xl md:text-4xl font-semibold italic text-[#083653]"
          >
            {{ need.number }}
          </span>
          <h3
            class="mt-4 text-lg md:text-xl font-semibold text-[#083653] leading-snug"
          >
            {{ need.title }}
          </h3>
          <p class="mt-3 text-sm text-neutral-500 leading-relaxed font-normal">
            {{ need.body }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const needs = [
  {
    number: "01",
    title: "Stay compliant",
    body: "Organize tax reportings and meet statutory requirements on time.",
  },
  {
    number: "02",
    title: "Organize your books",
    body: "Create structured records for cleaner accounting and financial reporting.",
  },
  {
    number: "03",
    title: "Understand performance",
    body: "Use financial information to understand what is happening in your business.",
  },
  {
    number: "04",
    title: "Get strategic support",
    body: "Evaluate financial decisions and prepare your business for growth.",
  },
];
</script>
