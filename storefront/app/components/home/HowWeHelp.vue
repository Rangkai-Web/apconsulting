<template>
  <section class="px-4 py-16 md:px-8 md:py-24">
    <div
      class="mx-auto max-w-7xl grid gap-8 md:grid-cols-2 md:items-center md:gap-12"
    >
      <div>
        <p
          class="text-sm font-semibold uppercase tracking-wide text-neutral-400"
        >
          HOW WE HELP
        </p>
        <h2
          class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
        >
          <span class="font-sans font-semibold italic text-[#083653] block"
            >Your Business Deserves</span
          >
          <span class="font-sans font-normal text-[#083653] block"
            >More Than Routine Bookkeeping.</span
          >
        </h2>
        <p
          class="mt-4 max-w-lg text-sm text-neutral-500 leading-relaxed md:text-base"
        >
          We help businesses manage tax, accounting, bookkeeping, and financial
          reporting with clarity and structure, so you can make confident
          decisions and focus on growth.
        </p>
      </div>

      <picture class="block w-full overflow-hidden">
        <source
          media="(min-width: 768px)"
          srcset="/images/homepage/HowWeHelp_Desktop.png"
        />
        <img
          src="/images/homepage/HowWeHelp_Mobile.png"
          alt="PCG team"
          class="w-full object-cover shadow-sm"
        />
      </picture>
    </div>

    <div
      class="mx-auto mt-12 grid max-w-7xl gap-6 md:grid-cols-3 items-stretch"
    >
      <div
        v-for="card in cards"
        :key="card.title"
        class="flex flex-col p-6 sm:p-8 md:p-[42px] min-h-[360px] border border-[#0000001F] rounded-none shadow-xs transition-shadow"
        style="
          background: radial-gradient(
            49.65% 56.63% at 97.41% 3.65%,
            #e9edff 0%,
            #ffffff 100%
          );
        "
      >
        <div>
          <img
            :src="card.icon"
            :alt="card.title"
            class="h-24 w-auto object-contain"
          />
        </div>
        <div class="mt-8 flex flex-col justify-start">
          <div class="min-h-[56px] flex items-start">
            <h3
              class="text-xl md:text-[22px] font-bold text-secondary-700 leading-snug"
            >
              {{ card.title }}
            </h3>
          </div>
          <p class="mt-3 text-sm text-neutral-500 leading-relaxed font-normal">
            {{ card.body }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const cards = [
  {
    icon: "/images/icon/Compliance_Icon.svg",
    title: "Compliance with confidence.",
    body: "Tax compliance, filings, reviews, tax planning, and audit support—handled with care and clarity",
  },
  {
    icon: "/images/icon/Financial_Icon.svg",
    title: "Build a Stronger Financial Foundation",
    body: "Accurate bookkeeping, financial reporting, bank reconciliation, and journal adjustments to keep your business organized.",
  },
  {
    icon: "/images/icon/Analysis_Icon.svg",
    title: "Insight for better decisions.",
    body: "Financial analysis, cash-flow forecasts, budgeting, and performance reviews to help you plan ahead.",
  },
];
</script>
