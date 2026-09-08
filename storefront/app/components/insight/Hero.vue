<template>
  <section
    id="insight-hero"
    class="relative min-h-[500px] md:min-h-[580px] flex items-center bg-[#083653] px-4 py-16 sm:px-8 md:px-16 md:py-20 overflow-hidden"
  >
    <picture class="absolute inset-0 size-full pointer-events-none">
      <source
        media="(min-width: 768px)"
        srcset="/images/insight/Hero_Desktop.png"
      />
      <img
        src="/images/insight/Hero_Mobile.png"
        alt="PCG Featured Insight"
        class="size-full object-cover"
      />
    </picture>

    <!-- Dark gradient overlay for readable white text -->
    <div
      class="absolute inset-0 bg-gradient-to-r from-black/75 via-black/50 to-transparent pointer-events-none"
    ></div>

    <div class="relative z-10 mx-auto max-w-7xl w-full">
      <span
        class="pt-24 sm:pt-0 inline-block text-xs sm:text-sm font-semibold uppercase tracking-wider text-[#FFBB00]"
      >
        PCG FEATURED INSIGHT
      </span>
      <NuxtLink
        to="/insigth/navigating-corporate-tax-compliance"
        class="group block hover:text-white/95 transition-colors"
      >
        <h1
          class="mt-3 max-w-2xl text-3xl font-semibold text-white sm:text-4xl md:text-[48px] leading-[1.15] tracking-tight group-hover:text-white/95 transition-colors"
        >
          Navigating Corporate Tax Compliance and Minimizing Business Risks
        </h1>
      </NuxtLink>
      <p
        class="mt-4 text-sm text-white/90 leading-relaxed md:text-base font-normal max-w-2xl"
      >
        Discover how managing your tax obligations properly and structuring your
        financial administration can support your business development and drive
        sustainable decision-making
      </p>

      <div
        class="mt-6 flex flex-wrap items-center gap-4 md:gap-6 text-xs sm:text-sm text-white/80 font-normal"
      >
        <span class="font-medium text-white">By PCG Editorial Team</span>
        <span class="flex items-center gap-1.5">
          <UIcon name="i-heroicons-calendar" class="size-4" />
          September 10, 2026
        </span>
        <span class="flex items-center gap-1.5">
          <UIcon name="i-heroicons-clock" class="size-4" />
          5 min read
        </span>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts"></script>
