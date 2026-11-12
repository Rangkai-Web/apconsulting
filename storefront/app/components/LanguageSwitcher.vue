<template>
  <div ref="containerRef" class="relative inline-block text-left">
    <!-- Switcher Button -->
    <button
      type="button"
      data-testid="language-switcher-button"
      :class="[
        'flex items-center gap-2 rounded-[2px] px-3 py-1.5 text-xs font-semibold focus:outline-none transition-all cursor-pointer',
        variant === 'dark'
          ? 'bg-white/10 text-white border border-white/15 hover:bg-white/15'
          : 'bg-white text-neutral-700 border border-neutral-200 hover:bg-neutral-50',
      ]"
      :aria-expanded="isOpen"
      aria-haspopup="true"
      @click="isOpen = !isOpen"
    >
      <img
        :src="currentLanguage?.flag || '/images/icon/UnitedFlagIcon.svg'"
        :alt="currentLanguage?.label || 'Language'"
        class="h-3.5 w-5 object-contain"
      />
      <span
        :class="[
          'tracking-wider uppercase font-bold',
          variant === 'dark' ? 'text-white' : 'text-neutral-800',
        ]"
      >{{ currentLanguage?.shortLabel || "EN" }}</span>
      <UIcon
        name="i-heroicons-chevron-down"
        :class="[
          'size-3.5 transition-transform duration-200',
          variant === 'dark' ? 'text-white/70' : 'text-neutral-400',
          { 'rotate-180': isOpen },
        ]"
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
        :class="[
          'absolute left-0 sm:left-auto sm:right-0 w-44 rounded-[2px] border border-neutral-200 bg-white shadow-lg z-50 py-1',
          direction === 'up' ? 'bottom-full mb-1.5' : 'mt-1.5',
        ]"
      >
        <button
          v-for="lang in languages"
          :key="lang.code"
          type="button"
          :data-testid="`lang-option-${lang.code}`"
          :class="[
            'flex w-full items-center justify-between px-3 py-2 text-xs font-medium text-neutral-700 hover:bg-neutral-50 transition-colors text-left cursor-pointer',
            {
              'bg-neutral-50 text-[#0869B2] font-semibold':
                currentLocale === lang.code,
            },
          ]"
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
            class="size-4 text-[#0869B2]"
          />
        </button>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";
import { useLanguage, type Locale } from "~/composables/useLanguage";

withDefaults(
  defineProps<{
    direction?: "up" | "down";
    variant?: "light" | "dark";
  }>(),
  {
    direction: "down",
    variant: "light",
  }
);

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
