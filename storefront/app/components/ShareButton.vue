<template>
  <button
    type="button"
    class="flex items-center gap-1.5 text-xs sm:text-sm text-neutral-500 hover:text-[#083653] transition-colors"
    @click="share"
  >
    <UIcon name="i-heroicons-share" class="size-4" />
    {{ t('insight.detail.share') }}
  </button>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useLanguage } from "~/composables/useLanguage";
import { buildShareUrl } from "~/utils/share";

const props = defineProps<{
  url: string;
  title: string;
}>();

const { t } = useLanguage();
const toast = useToast();

const shareUrl = computed(() => buildShareUrl(props.url));

async function share() {
  if (navigator.share) {
    try {
      await navigator.share({ title: props.title, url: shareUrl.value });
    } catch {
      // user cancelled the native share sheet, do nothing
    }
    return;
  }

  await navigator.clipboard.writeText(shareUrl.value);
  toast.add({ title: t('insight.detail.linkCopied'), icon: "i-heroicons-check-circle" });
}
</script>
