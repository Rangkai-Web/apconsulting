<template>
  <section
    id="hero"
    class="relative min-h-[500px] md:min-h-[600px] flex items-end md:items-center bg-slate-900 px-4 py-12 sm:px-8 md:px-16 md:py-20 overflow-hidden"
  >
    <picture class="absolute inset-0 size-full pointer-events-none">
      <source
        media="(min-width: 768px)"
        srcset="/images/about/Hero_Desktop.png"
      />
      <img
        src="/images/about/Hero_Mobile.png"
        alt="PCG Team"
        class="size-full object-cover"
      />
    </picture>
    <!-- Soft dark gradient overlay for text readability -->
    <div
      class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/35 to-transparent pointer-events-none"
    ></div>

    <div class="relative z-10 mx-auto max-w-7xl w-full">
      <div class="max-w-xl text-white">
        <h1
          class="pt-24 text-3xl font-semibold text-white sm:text-4xl md:text-[48px] leading-[1.15]"
        >
          <span class="block">More than accountants.</span>
          <span class="block">Partners in your progress.</span>
        </h1>
        <p
          class="mt-4 text-sm text-white/90 leading-relaxed md:text-base font-normal max-w-lg"
        >
          PCG helps businesses make confident financial decisions. From tax and
          accounting to financial planning and business support, we turn complex
          information into clear, practical next steps.
        </p>
        <div class="mt-8 flex flex-wrap items-center gap-4 sm:gap-6">
          <UButton
            to="/about#why-choose"
            color="primary"
            variant="solid"
            size="lg"
            class="justify-center font-medium px-6 py-4 rounded-none bg-[#F4C400] text-[#083653] hover:bg-[#F4C400]/90 transition-colors uppercase text-xs sm:text-sm tracking-wider"
          >
            DISCOVER OUR APPROACH
          </UButton>
          <UButton
            to="#people"
            variant="link"
            size="lg"
            class="justify-center font-medium px-2 py-4 text-white hover:text-white/80 transition-colors uppercase text-xs sm:text-sm tracking-wider no-underline hover:no-underline"
          >
            MEET THE TEAM
          </UButton>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts"></script>
