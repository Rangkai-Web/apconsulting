<template>
  <section
    id="people"
    class="px-4 py-16 md:px-[32px] md:py-[80px] bg-[#F9FAFB]"
  >
    <div class="mx-auto max-w-3xl text-center">
      <p
        class="text-xs sm:text-sm font-semibold uppercase tracking-wider text-neutral-400"
      >
        {{ t('about.people.tag') }}
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span class="font-sans font-normal text-[#083653]"
          >{{ t('about.people.titleNormal') }}
        </span>
        <span class="block font-sans font-semibold italic text-[#083653]"
          >{{ t('about.people.titleItalic') }}</span
        >
      </h2>
      <p
        class="mt-4 max-w-xl mx-auto text-sm text-neutral-500 leading-relaxed md:text-base font-normal"
      >
        {{ t('about.people.description') }}
      </p>
    </div>

    <!-- Skeleton Loading State -->
    <div
      v-if="pending"
      class="mx-auto mt-12 grid max-w-7xl gap-6 sm:grid-cols-2 lg:grid-cols-4 items-stretch"
    >
      <div
        v-for="n in 4"
        :key="n"
        class="h-[380px] sm:h-[420px] md:h-[450px] w-full animate-pulse bg-neutral-200 border border-neutral-300"
      />
    </div>

    <!-- Empty State -->
    <div
      v-else-if="!people || people.length === 0"
      class="mx-auto mt-12 max-w-4xl p-12 text-center border border-dashed border-neutral-300 bg-white"
    >
      <UIcon name="i-heroicons-user-group" class="size-12 text-neutral-400 mx-auto" />
      <p class="mt-3 text-base font-medium text-neutral-600">
        {{ currentLocale === 'en' ? 'No team members listed at the moment.' : 'Belum ada anggota tim yang ditampilkan saat ini.' }}
      </p>
    </div>

    <!-- Data State -->
    <div
      v-else
      class="mx-auto mt-12 grid max-w-7xl gap-6 sm:grid-cols-2 lg:grid-cols-4 items-stretch"
    >
      <div
        v-for="person in people"
        :key="person.id"
        class="relative flex flex-col h-[380px] sm:h-[420px] md:h-[450px] w-full overflow-hidden rounded-none group bg-neutral-100 border border-neutral-200"
      >
        <img
          :src="resolveImageUrl(person.photo, '/images/homepage/About_Desktop.webp')"
          :alt="person.name"
          loading="lazy"
          decoding="async"
          width="400"
          height="450"
          class="absolute inset-0 size-full object-cover object-top transition-transform duration-300 group-hover:scale-105"
        />
        <div
          class="absolute inset-x-3 bottom-3 z-10 bg-white border border-[#0000001F] py-3.5 px-4 text-center shadow-xs"
        >
          <h3
            class="font-bold text-[#083653] text-base sm:text-lg leading-tight"
          >
            {{ person.name }}
          </h3>
          <p class="text-xs text-neutral-500 font-normal mt-1">
            {{ person.position }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useLanguage } from "~/composables/useLanguage";
import { getApiBaseUrl, resolveImageUrl, safeUseFetch } from "~/utils/api";

interface TeamMember {
  id: number;
  name: string;
  position: string;
  photo?: string;
  bio?: string;
}

const { t, currentLocale } = useLanguage();
const apiBase = getApiBaseUrl();

const { data: apiResponse, pending } = safeUseFetch<{ data: TeamMember[] }>(
  () => `${apiBase}/team`
);

const people = computed(() => apiResponse.value?.data || []);
</script>
