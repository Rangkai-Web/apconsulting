<template>
  <section class="bg-secondary-700 px-4 py-16 text-center text-white md:px-8 md:py-20 relative overflow-hidden">
    <picture class="absolute inset-0 size-full pointer-events-none">
      <source media="(min-width: 768px)" srcset="/images/homepage/CTA_Desktop.png" />
      <img src="/images/homepage/CTA_Mobile.png" alt="" class="size-full object-cover opacity-80" />
    </picture>
    <div class="relative z-10 mx-auto max-w-3xl">
      <p class="text-sm font-semibold uppercase tracking-wide text-primary-500">START A CONVERSATION</p>
      <h2 class="mt-4 text-3xl font-bold leading-tight md:text-5xl">
        Ready to bring more clarity to your business?
        <span class="block italic">Let's take the next step together.</span>
      </h2>
      <p class="mt-6 text-neutral-200 leading-relaxed max-w-2xl mx-auto">
        Tell us about your tax, accounting, or financial needs. PCG will help you identify a
        practical starting point for your business.
      </p>
      <UButton to="/contact" color="neutral" variant="solid" size="lg" class="mt-8 bg-white text-secondary-700 font-extrabold px-8 py-3.5 rounded-full hover:bg-neutral-100">
        Discuss Your Needs
      </UButton>
    </div>
  </section>
</template>

<script setup lang="ts"></script>
