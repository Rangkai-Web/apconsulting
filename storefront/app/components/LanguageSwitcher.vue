<template>
  <div ref="containerRef" class="relative inline-block text-left">
    <!-- Switcher Button -->
    <button
      type="button"
      data-testid="language-switcher-button"
      class="flex items-center gap-2 bg-white px-3 py-1.5 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 hover:border-neutral-300 focus:outline-none transition-all cursor-pointer"
      :aria-expanded="isOpen"
      aria-haspopup="true"
      @click="isOpen = !isOpen"
    >
      <img
        :src="currentLanguage?.flag || '/images/icon/UnitedFlagIcon.svg'"
        :alt="currentLanguage?.label || 'Language'"
        class="h-3.5 w-5 object-contain"
      />
      <span class="tracking-wider uppercase text-neutral-800 font-bold">{{
        currentLanguage?.shortLabel || "EN"
      }}</span>
      <UIcon
        name="i-heroicons-chevron-down"
        class="size-3.5 text-neutral-400 transition-transform duration-200"
        :class="{ 'rotate-180': isOpen }"
      />
    </button>

    <!-- Dropdown Options -->
    <Transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <div
        v-if="isOpen"
        data-testid="language-dropdown"
        class="absolute right-0 mt-1.5 w-44 border border-neutral-200 bg-white shadow-md z-50 py-1"
      >
        <button
          v-for="lang in languages"
          :key="lang.code"
          type="button"
          :data-testid="`lang-option-${lang.code}`"
          class="flex w-full items-center justify-between px-3 py-2 text-xs font-medium text-neutral-700 hover:bg-neutral-50 transition-colors text-left cursor-pointer"
          :class="{
            'bg-neutral-50 text-secondary-700 font-semibold':
              currentLocale === lang.code,
          }"
          @click="selectLanguage(lang.code)"
        >
          <div class="flex items-center gap-2.5">
            <img
              :src="lang.flag"
              :alt="lang.label"
              class="h-3.5 w-5 object-contain"
            />
            <span>{{ lang.label }}</span>
          </div>
          <UIcon
            v-if="currentLocale === lang.code"
            name="i-heroicons-check"
            class="size-4 text-secondary-600"
          />
        </button>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";
import { useLanguage, type Locale } from "~/composables/useLanguage";

const { currentLocale, currentLanguage, setLanguage, languages } =
  useLanguage();
const isOpen = ref(false);
const containerRef = ref<HTMLElement | null>(null);

function selectLanguage(code: Locale) {
  setLanguage(code);
  isOpen.value = false;
}

function handleClickOutside(event: MouseEvent) {
  if (
    containerRef.value &&
    !containerRef.value.contains(event.target as Node)
  ) {
    isOpen.value = false;
  }
}

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>
