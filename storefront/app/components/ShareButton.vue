<template>
  <div ref="containerRef" class="relative inline-block text-left">
    <!-- Share Toggle Button -->
    <button
      type="button"
      class="flex items-center gap-1.5 text-xs sm:text-sm text-neutral-500 hover:text-[#083653] transition-colors cursor-pointer select-none"
      :aria-expanded="isOpen"
      aria-haspopup="true"
      @click="toggleShareMenu"
    >
      <UIcon name="i-heroicons-share" class="size-4" />
      <span>{{ t('insight.detail.share') }}</span>
    </button>

    <!-- Dropdown Share Menu -->
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
        class="absolute right-0 mt-2 w-48 border border-neutral-200 bg-white shadow-lg z-50 py-1.5 rounded-md text-xs sm:text-sm"
      >
        <!-- Copy Link Option -->
        <button
          type="button"
          class="flex w-full items-center gap-2.5 px-3 py-2 text-neutral-700 hover:bg-neutral-50 transition-colors text-left cursor-pointer"
          @click="handleCopyLink"
        >
          <UIcon
            :name="isCopied ? 'i-heroicons-check-circle' : 'i-heroicons-link'"
            class="size-4 shrink-0"
            :class="isCopied ? 'text-emerald-600' : 'text-neutral-500'"
          />
          <span :class="{ 'text-emerald-600 font-medium': isCopied }">
            {{ isCopied ? (currentLocale === 'en' ? 'Copied!' : 'Tersalin!') : (currentLocale === 'en' ? 'Copy Link' : 'Salin Tautan') }}
          </span>
        </button>

        <!-- WhatsApp -->
        <a
          :href="whatsappUrl"
          target="_blank"
          rel="noopener noreferrer"
          class="flex w-full items-center gap-2.5 px-3 py-2 text-neutral-700 hover:bg-neutral-50 transition-colors text-left cursor-pointer"
          @click="isOpen = false"
        >
          <svg class="size-4 text-[#25D366] shrink-0" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984 0 1.76.459 3.475 1.332 4.992L2 22l5.143-1.348A9.957 9.957 0 0 0 12.012 22c5.508 0 9.99-4.478 9.99-9.984s-4.482-9.984-9.99-9.984zm0 18.271a8.27 8.27 0 0 1-4.223-1.157l-.303-.18-3.136.822.837-3.056-.197-.314a8.261 8.261 0 0 1-1.267-4.402c0-4.562 3.712-8.271 8.289-8.271 4.572 0 8.283 3.709 8.283 8.271 0 4.563-3.711 8.272-8.283 8.272z"/>
          </svg>
          <span>WhatsApp</span>
        </a>

        <!-- LinkedIn -->
        <a
          :href="linkedinUrl"
          target="_blank"
          rel="noopener noreferrer"
          class="flex w-full items-center gap-2.5 px-3 py-2 text-neutral-700 hover:bg-neutral-50 transition-colors text-left cursor-pointer"
          @click="isOpen = false"
        >
          <svg class="size-4 text-[#0A66C2] shrink-0" fill="currentColor" viewBox="0 0 24 24">
            <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/>
          </svg>
          <span>LinkedIn</span>
        </a>

        <!-- X (Twitter) -->
        <a
          :href="twitterUrl"
          target="_blank"
          rel="noopener noreferrer"
          class="flex w-full items-center gap-2.5 px-3 py-2 text-neutral-700 hover:bg-neutral-50 transition-colors text-left cursor-pointer"
          @click="isOpen = false"
        >
          <svg class="size-4 text-neutral-900 shrink-0" fill="currentColor" viewBox="0 0 24 24">
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
          </svg>
          <span>X (Twitter)</span>
        </a>

        <!-- Native Share (if supported) -->
        <button
          v-if="hasNativeShare"
          type="button"
          class="flex w-full items-center gap-2.5 px-3 py-2 text-neutral-700 hover:bg-neutral-50 transition-colors text-left cursor-pointer border-t border-neutral-100 mt-1 pt-2"
          @click="handleNativeShare"
        >
          <UIcon name="i-heroicons-share" class="size-4 text-neutral-500 shrink-0" />
          <span>{{ currentLocale === 'en' ? 'More options...' : 'Opsi lainnya...' }}</span>
        </button>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useLanguage } from "~/composables/useLanguage";
import { buildShareUrl } from "~/utils/share";

const props = defineProps<{
  url: string;
  title: string;
}>();

const { t, currentLocale } = useLanguage();

const isOpen = ref(false);
const isCopied = ref(false);
const containerRef = ref<HTMLElement | null>(null);
const hasNativeShare = ref(false);

const shareUrl = computed(() => buildShareUrl(props.url));

const whatsappUrl = computed(() => {
  const text = `${props.title}\n${shareUrl.value}`;
  return `https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`;
});

const linkedinUrl = computed(() => {
  return `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareUrl.value)}`;
});

const twitterUrl = computed(() => {
  return `https://twitter.com/intent/tweet?text=${encodeURIComponent(props.title)}&url=${encodeURIComponent(shareUrl.value)}`;
});

onMounted(() => {
  hasNativeShare.value = typeof navigator !== "undefined" && typeof navigator.share === "function";
  document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
});

function toggleShareMenu() {
  isOpen.value = !isOpen.value;
}

function handleClickOutside(event: MouseEvent) {
  if (containerRef.value && !containerRef.value.contains(event.target as Node)) {
    isOpen.value = false;
  }
}

async function copyText(text: string): Promise<boolean> {
  if (typeof navigator !== "undefined" && navigator.clipboard && typeof navigator.clipboard.writeText === "function") {
    try {
      await navigator.clipboard.writeText(text);
      return true;
    } catch {
      // fallback
    }
  }
  try {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.left = "-999999px";
    textArea.style.top = "-999999px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    const successful = document.execCommand("copy");
    textArea.remove();
    return successful;
  } catch {
    return false;
  }
}

async function handleCopyLink() {
  const success = await copyText(shareUrl.value);
  if (success) {
    isCopied.value = true;
    try {
      const toast = useToast();
      toast.add({ title: t("insight.detail.linkCopied"), icon: "i-heroicons-check-circle" });
    } catch {
      // toast fallback silently handled by UI feedback state
    }
    setTimeout(() => {
      isCopied.value = false;
      isOpen.value = false;
    }, 1500);
  }
}

async function handleNativeShare() {
  isOpen.value = false;
  if (typeof navigator !== "undefined" && navigator.share) {
    try {
      await navigator.share({
        title: props.title,
        url: shareUrl.value,
      });
    } catch {
      // user cancelled native share
    }
  }
}
</script>
