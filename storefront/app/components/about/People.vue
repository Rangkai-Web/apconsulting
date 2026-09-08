<template>
  <section id="people" class="px-4 py-16 md:px-[32px] md:py-[80px] bg-[#F9FAFB]">
    <div class="mx-auto max-w-3xl text-center">
      <p
        class="text-xs sm:text-sm font-semibold uppercase tracking-wider text-neutral-400"
      >
        THE PEOPLE BEHIND PCG
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span class="font-sans font-normal text-[#083653]">Built by expertise. </span>
        <span class="block font-sans font-semibold italic text-[#083653]"
          >Driven by purpose.</span
        >
      </h2>
      <p
        class="mt-4 max-w-xl mx-auto text-sm text-neutral-500 leading-relaxed md:text-base font-normal"
      >
        Meet the professionals behind PCG—bringing hands-on experience, clear
        focus, and a shared commitment to your success.
      </p>
    </div>

    <div
      class="mx-auto mt-12 grid max-w-7xl gap-6 sm:grid-cols-2 lg:grid-cols-4 items-stretch"
    >
      <div
        v-for="person in people"
        :key="person.name"
        class="relative flex flex-col h-[380px] sm:h-[420px] md:h-[450px] w-full overflow-hidden rounded-none group bg-neutral-100"
      >
        <img
          :src="person.image"
          :alt="person.name"
          class="absolute inset-0 size-full object-cover object-top transition-transform duration-300 group-hover:scale-105"
        />
        <div
          class="absolute inset-x-3 bottom-3 z-10 bg-white border border-[#0000001F] py-3.5 px-4 text-center shadow-xs"
        >
          <h3 class="font-bold text-[#083653] text-base sm:text-lg leading-tight">
            {{ person.name }}
          </h3>
          <p class="text-xs text-neutral-500 font-normal mt-1">
            {{ person.role }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const people = [
  {
    name: "Darren Robertson",
    role: "Partner / Tax",
    image: "/images/about/team/darren.jpg",
  },
  {
    name: "Cody Fisher",
    role: "Partner / Advisory",
    image: "/images/about/team/cody.jpg",
  },
  {
    name: "Jacob Jones",
    role: "Partner / Audit",
    image: "/images/about/team/jacob.jpg",
  },
  {
    name: "Floyd Miles",
    role: "Partner / Finance",
    image: "/images/about/team/floyd.jpg",
  },
];
</script>
