<template>
  <section
    class="px-4 py-12 md:px-8 bg-[#F9FAFB] border-t border-b border-[#DFE7ED]"
  >
    <div class="mx-auto max-w-7xl">
      <div
        class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4"
      >
        <div>
          <p
            class="text-sm font-semibold uppercase tracking-wide text-neutral-400"
          >
            LOCATION
          </p>
          <h2 class="mt-2 text-2xl md:text-3xl font-bold text-[#083653]">
            Visit Our Office
          </h2>
        </div>
        <p
          class="text-sm text-neutral-500 font-normal max-w-md text-left sm:text-right"
        >
          Green Lake City Boulevard No.23, Rukan Cordoba Blk. D Lt 3, Kota
          Tangerang, Banten
        </p>
      </div>

      <!-- Google Maps Iframe -->
      <div
        class="w-full h-[380px] md:h-[450px] overflow-hidden border border-[#DFE7ED] shadow-xs"
      >
        <iframe
          title="PCG Office Location Map"
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.452601955025!2d106.6974864758671!3d-6.203875393783993!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f9843a9b1399%3A0xb35a39cb6ff1237a!2sRukan%20Cordoba%2C%20Jl.%20Green%20Lake%20City%20Blvd%2C%20Petir%2C%20Cipondoh%2C%20Tangerang%20City%2C%20Banten%2015147!5e0!3m2!1sen!2sid!4v1710000000000!5m2!1sen!2sid"
          width="100%"
          height="100%"
          style="border: 0"
          allowfullscreen="true"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
        ></iframe>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts"></script>
