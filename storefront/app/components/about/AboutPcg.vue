<template>
  <section id="about-pcg" class="px-4 py-16 md:px-[32px] md:py-[80px] bg-white">
    <div class="mx-auto max-w-7xl">
      <p
        class="text-xs sm:text-sm font-semibold uppercase tracking-wider text-neutral-400"
      >
        {{ t('about.pcg.tag') }}
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span class="font-sans font-normal text-[#083653]"
          >{{ t('about.pcg.titleNormal') }}
        </span>
        <br />
        <span class="font-sans font-semibold italic text-[#083653]"
          >{{ t('about.pcg.titleItalic') }}</span
        >
      </h2>

      <div
        class="mt-8 grid gap-8 md:grid-cols-2 md:gap-12 text-sm text-neutral-500 leading-relaxed font-normal md:text-base"
      >
        <p>
          {{ t('about.pcg.paragraph1') }}
        </p>
        <p>
          {{ t('about.pcg.paragraph2') }}
        </p>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useLanguage } from '~/composables/useLanguage'

const { t } = useLanguage()
</script>
