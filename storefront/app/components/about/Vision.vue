<template>
  <section id="vision" class="relative bg-[#083653] px-4 py-16 text-white md:px-[32px] md:py-[80px] overflow-hidden">
    <div class="mx-auto max-w-7xl relative z-10 flex flex-col gap-16 md:gap-20">
      <!-- Vision Block (Top Right aligned or wide) -->
      <div class="flex flex-col md:items-end md:text-right">
        <p class="text-xs sm:text-sm font-semibold uppercase tracking-wider text-[#F4C400]">
          VISION
        </p>
        <h2 class="mt-4 max-w-3xl text-2xl font-normal text-white sm:text-3xl md:text-[36px] leading-[1.3]">
          To be a trusted partner that brings clarity, confidence, and sustainable growth to every business we serve
        </h2>
      </div>

      <!-- Mission Block (Bottom Left aligned) -->
      <div class="max-w-3xl">
        <p class="text-xs sm:text-sm font-semibold uppercase tracking-wider text-[#F4C400]">
          MISSION
        </p>
        <h3 class="mt-4 text-xl font-normal text-white sm:text-2xl md:text-[28px] leading-[1.3]">
          We help businesses manage financial and operational complexity through reliable expertise, practical insight, and tailored professional support.
        </h3>

        <ul class="mt-8 space-y-4 text-sm md:text-base text-neutral-200 font-normal">
          <li v-for="item in missionItems" :key="item" class="flex items-center gap-3">
            <img
              src="/images/about/akar-icons_circle-check-fill.svg"
              alt=""
              class="size-5 shrink-0"
            />
            <span>{{ item }}</span>
          </li>
        </ul>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const missionItems = [
  "Deliver reliable professional services.",
  "Bring clarity to complex information.",
  "Assist clients to achieve financial control.",
  "Build long-term trust relationships.",
  "Enable sustainable business growth.",
];
</script>
