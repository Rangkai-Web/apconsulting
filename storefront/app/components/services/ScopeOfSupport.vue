<template>
  <section id="scope-of-support" class="px-4 py-16 md:px-8 md:py-24 bg-white">
    <div class="mx-auto max-w-4xl text-center">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">
        SCOPE OF SUPPORT
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span class="font-sans font-bold italic text-[#083653]"
          >Practical support
        </span>
        <span class="font-sans font-normal text-[#083653]"
          >from compliance to business insight.</span
        >
      </h2>
      <p
        class="mt-4 max-w-2xl mx-auto text-sm text-neutral-500 leading-relaxed md:text-base"
      >
        Whether you need recurring compliance support, cleaner financial
        records, or custom advice to scale, PCG provides flexible services built
        around your needs.
      </p>
    </div>

    <div class="mx-auto mt-12 max-w-6xl space-y-6">
      <div
        v-for="service in services"
        :key="service.number"
        class="p-6 md:p-[42px] border border-[#0000001F] rounded-none shadow-xs grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-center"
        style="
          background: radial-gradient(
            49.65% 56.63% at 97.41% 3.65%,
            #fffae3 0%,
            #ffffff 100%
          );
        "
      >
        <!-- Left Side: Service Info -->
        <div class="lg:col-span-7 flex items-start gap-4 md:gap-6">
          <span
            class="text-3xl md:text-[44px] font-semibold italic text-[#083653] leading-none shrink-0 pt-1"
          >
            {{ service.number }}
          </span>
          <div class="flex flex-col items-start">
            <h3
              class="text-xl md:text-[24px] font-semibold text-[#083653] leading-snug"
            >
              {{ service.title }}
            </h3>
            <p
              class="mt-2 text-sm md:text-base text-neutral-500 leading-relaxed font-normal max-w-lg"
            >
              {{ service.description }}
            </p>
            <NuxtLink
              to="/contact"
              class="mt-4 inline-flex items-center text-xs md:text-sm font-semibold uppercase tracking-wider text-[#083653] hover:text-[#083653]/80 group"
            >
              <span>{{ service.ctaText }}</span>
              <UIcon
                name="i-heroicons-arrow-right"
                class="ml-1.5 size-4 text-[#083653] transition-transform group-hover:translate-x-1"
              />
            </NuxtLink>
          </div>
        </div>

        <!-- Right Side: Features Checklist -->
        <div class="lg:col-span-5">
          <ul class="space-y-2.5">
            <li
              v-for="(feature, idx) in service.features"
              :key="idx"
              class="flex items-center gap-3 text-sm md:text-base font-normal text-neutral-600"
            >
              <img
                src="/images/services/akar-icons_circle-check-fill.svg"
                alt=""
                class="size-5 shrink-0"
              />
              <span>{{ feature }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const services = [
  {
    number: "01",
    title: "Taxation",
    description:
      "Support your tax responsibilities with a more structured process and practical guidance.",
    ctaText: "DISCUSS TAX SUPPORT",
    features: [
      "Tax consultation",
      "Monthly and annual tax reporting",
      "Tax compliance review",
      "Tax planning",
      "Tax audit assistance",
    ],
  },
  {
    number: "02",
    title: "Accounting & Bookkeeping",
    description:
      "Build reliable financial records that give your business a clearer view of its day-to-day operations.",
    ctaText: "DISCUSS ACCOUNTING SUPPORT",
    features: [
      "Financial transaction recording",
      "Monthly financial statements",
      "Annual financial statements",
      "Bank reconciliation",
      "Accounting journal adjustments",
    ],
  },
  {
    number: "03",
    title: "Business Administration Support",
    description:
      "Get flexible support for administrative work that helps your business run in a more organized way.",
    ctaText: "DISCUSS BUSINESS SUPPORT",
    features: [
      "Corporate administration",
      "Document coordination",
      "Administrative tasks",
      "Customized support",
    ],
  },
  {
    number: "04",
    title: "Other Financial Services",
    description:
      "Turn financial information into useful insight for planning, control, and better decisions.",
    ctaText: "DISCUSS FINANCIAL SUPPORT",
    features: [
      "Financial statement analysis",
      "Financial projections",
      "Cash-flow management",
      "Corporate budgeting",
      "Financial performance evaluation",
    ],
  },
];
</script>
