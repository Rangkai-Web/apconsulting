<template>
  <section
    id="services-hero"
    class="relative min-h-[500px] md:min-h-[600px] flex items-center bg-[#083653] px-4 py-16 sm:px-8 md:px-16 md:py-20 overflow-hidden"
  >
    <picture class="absolute inset-0 size-full pointer-events-none">
      <source
        media="(min-width: 768px)"
        srcset="/images/services/Hero_Desktop.png"
      />
      <img
        src="/images/services/Hero_Mobile.png"
        alt="PCG Services"
        class="size-full object-cover"
      />
    </picture>

    <!-- Dark gradient overlay for optimal text contrast -->
    <div
      class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/35 to-transparent pointer-events-none"
    ></div>

    <div class="relative z-10 mx-auto max-w-7xl w-full">
      <div class="max-w-2xl text-white">
        <h1
          class="pt-48 text-3xl font-bold text-white sm:text-4xl md:text-[52px] leading-[1.15] tracking-tight"
        >
          Support designed around your business needs.
        </h1>
        <p
          class="mt-4 text-sm text-white/90 leading-relaxed md:text-base font-normal max-w-xl"
        >
          From tax compliance and bookkeeping to financial analysis and business
          administration, PCG provides practical support that helps you stay
          organized and make informed decisions.
        </p>
        <div
          class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4"
        >
          <UButton
            to="#scope-of-support"
            color="primary"
            variant="solid"
            size="lg"
            class="w-fit justify-center font-medium px-7 py-4 rounded-none bg-[#FFBB00] text-[#083653] hover:bg-[#FFBB00]/90 transition-colors uppercase text-xs sm:text-sm tracking-wider"
          >
            DISCOVER OUR SERVICES
          </UButton>
          <UButton
            to="/contact"
            variant="outline"
            size="lg"
            class="w-fit justify-center font-medium px-7 py-4 rounded-none border border-white text-white hover:bg-white/10 transition-colors uppercase text-xs sm:text-sm tracking-wider"
          >
            DISCUSS YOUR NEEDS
          </UButton>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts"></script>
