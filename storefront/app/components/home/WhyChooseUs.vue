<template>
  <section class="relative px-4 py-16 md:px-8 md:py-24 overflow-hidden">
    <!-- Yellow Gradient Background SVG from Figma -->
    <img
      src="/images/homepage/Yellow_Gradient.svg"
      alt=""
      aria-hidden="true"
      class="pointer-events-none absolute -top-12 left-1/4 -z-10 h-auto w-[485px] max-w-full"
    />

    <div class="mx-auto grid max-w-7xl gap-8 md:grid-cols-2 md:gap-12 md:items-stretch">
      <!-- Left Column: Header + Tax Advisory Card -->
      <div class="flex flex-col justify-between gap-8">
        <div>
          <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">WHY BUSINESSES CHOOSE US</p>
          <h2 class="mt-4 text-3xl font-semibold leading-tight text-secondary-700 md:text-4xl lg:text-5xl">
            <span class="italic">The right support</span> can change the way your business moves forward.
          </h2>
          <p class="mt-4 text-sm text-neutral-500 leading-relaxed md:text-base">
            Managing a business requires more than keeping records up to date. It requires clear
            information, practical guidance, and a reliable partner who understands the bigger
            picture.
          </p>
        </div>

        <!-- Tax Advisory Card (Underneath description on Left) -->
        <div class="relative overflow-hidden rounded-2xl shadow-sm">
          <picture class="block w-full">
            <source media="(min-width: 768px)" srcset="/images/homepage/TaxAdvisory_Desktop.png" />
            <img
              src="/images/homepage/TaxAdvisory_Mobile.png"
              alt="Tax Advisory"
              class="w-full h-auto rounded-2xl grayscale object-cover"
            />
          </picture>

          <!-- White Overlay Box (Top-Left inside Image) -->
          <div class="absolute top-4 left-4 right-4 max-w-sm rounded-xl bg-white p-4 shadow-lg border border-neutral-100/60 sm:top-6 sm:left-6">
            <div class="flex items-center gap-2.5">
              <img src="/images/icon/FinanceConsult_Icon.svg" alt="" class="size-6 shrink-0" />
              <p class="font-bold text-secondary-700">Tax Advisory</p>
            </div>
            <p class="mt-2 text-xs text-neutral-500 leading-relaxed">
              Manage obligations, identify risks, and make more informed tax decisions.
            </p>
          </div>
        </div>
      </div>

      <!-- Right Column: Finance & Consulting Card (Tall Column) -->
      <div class="relative overflow-hidden rounded-2xl shadow-sm md:h-full">
        <picture class="block w-full h-full">
          <source media="(min-width: 768px)" srcset="/images/homepage/Finance&Consulting_Desktop.png" />
          <img
            src="/images/homepage/Finance&Consulting_Mobile.png"
            alt="Finance & Consulting"
            class="w-full h-full rounded-2xl grayscale object-cover"
          />
        </picture>

        <!-- White Overlay Box (Top-Left inside Image) -->
        <div class="absolute top-4 left-4 right-4 max-w-sm rounded-xl bg-white p-4 shadow-lg border border-neutral-100/60 sm:top-6 sm:left-6">
          <div class="flex items-center gap-2.5">
            <img src="/images/icon/TaxAdvisory_Icon.svg" alt="" class="size-6 shrink-0" />
            <p class="font-bold text-secondary-700">Finance & Consulting</p>
          </div>
          <p class="mt-2 text-xs text-neutral-500 leading-relaxed">
            Use financial visibility to plan ahead and build sustainable growth.
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts"></script>
