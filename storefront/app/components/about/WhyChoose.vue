<template>
  <section
    id="why-choose"
    class="relative px-4 py-16 md:px-[32px] md:py-[80px] bg-white overflow-hidden"
  >
    <!-- Background Yellow Glow Spots -->
    <img
      src="/images/about/Gradient.svg"
      alt=""
      class="absolute -top-20 -left-20 w-[450px] h-[450px] pointer-events-none opacity-50 z-0"
    />
    <img
      src="/images/about/Gradient.svg"
      alt=""
      class="absolute -bottom-20 -right-20 w-[450px] h-[450px] pointer-events-none opacity-50 z-0"
    />

    <div
      class="relative z-10 mx-auto max-w-7xl grid gap-12 lg:grid-cols-[1fr_1.3fr] lg:items-start"
    >
      <!-- Left Column: Headline & CTA -->
      <div>
        <p
          class="text-xs sm:text-sm font-semibold uppercase tracking-wider text-neutral-400"
        >
          WHY CHOOSE PCG
        </p>
        <h2
          class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
        >
          <span class="font-sans font-normal text-[#083653] block"
            >Support that
          </span>
          <span class="font-sans font-semibold italic text-[#083653] block"
            >goes beyond the numbers.</span
          >
        </h2>
        <p
          class="mt-4 max-w-lg text-sm text-neutral-500 leading-relaxed md:text-base font-normal"
        >
          PCG helps businesses manage tax, accounting, bookkeeping, and
          financial needs with a structured, professional approach tailored to
          their goals.
        </p>
        <div class="mt-8">
          <UButton
            to="/contact"
            color="neutral"
            variant="outline"
            size="lg"
            class="rounded-none h-[56px] px-8 py-4 bg-white border border-[#083653] hover:bg-neutral-50 text-[#083653] font-medium text-sm inline-flex items-center justify-center"
          >
            START A CONVERSATION
          </UButton>
        </div>
      </div>

      <!-- Right Column: 2x2 Grid Cards -->
      <div class="grid gap-6 sm:grid-cols-2 items-stretch">
        <div
          v-for="card in cards"
          :key="card.title"
          class="flex flex-col p-6 sm:p-8 md:p-[36px] border border-[#0000001F] rounded-none shadow-xs text-left justify-between bg-white"
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
              class="size-12 object-contain"
            />
          </div>
          <div class="mt-6 flex flex-col gap-2">
            <h3
              class="text-lg md:text-[20px] font-bold text-[#083653] leading-snug"
            >
              {{ card.title }}
            </h3>
            <p
              class="text-xs sm:text-sm text-neutral-500 leading-relaxed font-normal"
            >
              {{ card.body }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const cards = [
  {
    icon: "/images/about/TataKelolaOneStopBusiness.svg",
    title: "One-Stop Business Solutions",
    body: "Practical, integrated support across tax, accounting, advisory, and administrative processes through a single consulting partner.",
  },
  {
    icon: "/images/about/TataKelolaProfesionalNetwork.svg",
    title: "Professional Network",
    body: "Access specialized advisory support through our consulting network, bringing extra depth and resource when your business needs it.",
  },
  {
    icon: "/images/about/TataKelolaCompliance&RiskManagement.svg",
    title: "Compliance & Risk Management",
    body: "Structured processes to manage compliance obligations, reduce operational risk, and keep financial reporting accurate and timely.",
  },
  {
    icon: "/images/about/TataKelolaStrategicBusinessInsight.svg",
    title: "Strategic Business Insight",
    body: "Turn financial data into practical insight so management can make better-informed decisions.",
  },
];
</script>
