import {
  ref,
  watch,
  onMounted,
  onBeforeUnmount,
  getCurrentInstance,
  type Ref,
  type ComputedRef,
} from "vue";

export interface TypewriterOptions {
  typeSpeed?: number;
  deleteSpeed?: number;
  pauseDuration?: number;
  loop?: boolean;
}

export function useTypewriter(
  phrases: Ref<string[]> | ComputedRef<string[]> | string[],
  options: TypewriterOptions = {}
) {
  const {
    typeSpeed = 65,
    deleteSpeed = 35,
    pauseDuration = 2000,
    loop = true,
  } = options;

  const currentText = ref("");
  const phraseIndex = ref(0);
  const charIndex = ref(0);
  const isDeleting = ref(false);

  let timer: ReturnType<typeof setTimeout> | null = null;

  const getPhrases = (): string[] => {
    if (Array.isArray(phrases)) return phrases;
    return phrases.value || [];
  };

  const clearTimer = () => {
    if (timer) {
      clearTimeout(timer);
      timer = null;
    }
  };

  const tick = () => {
    const list = getPhrases();
    if (!list.length) {
      currentText.value = "";
      return;
    }

    const currentPhrase = list[phraseIndex.value % list.length] || "";

    if (!isDeleting.value) {
      // Typing forward
      charIndex.value++;
      currentText.value = currentPhrase.slice(0, charIndex.value);

      if (charIndex.value >= currentPhrase.length) {
        if (list.length > 1 || loop) {
          isDeleting.value = true;
          timer = setTimeout(tick, pauseDuration);
          return;
        }
        return;
      }
      timer = setTimeout(tick, typeSpeed);
    } else {
      // Deleting backwards
      charIndex.value--;
      currentText.value = currentPhrase.slice(0, Math.max(0, charIndex.value));

      if (charIndex.value <= 0) {
        isDeleting.value = false;
        phraseIndex.value = (phraseIndex.value + 1) % list.length;
        timer = setTimeout(tick, 300);
        return;
      }
      timer = setTimeout(tick, deleteSpeed);
    }
  };

  const start = () => {
    clearTimer();
    timer = setTimeout(tick, typeSpeed);
  };

  const hasInstance = getCurrentInstance();
  if (hasInstance) {
    onMounted(() => {
      start();
    });
    onBeforeUnmount(() => {
      clearTimer();
    });
  } else {
    start();
  }

  // Watch for dynamic phrase updates (e.g. language change)
  if (typeof phrases === "object" && "value" in phrases) {
    watch(
      () => phrases.value,
      (newList) => {
        clearTimer();
        phraseIndex.value = 0;
        charIndex.value = 0;
        isDeleting.value = false;
        currentText.value = "";
        if (newList && newList.length > 0) {
          start();
        }
      },
      { deep: true }
    );
  }

  return {
    currentText,
    phraseIndex,
    isDeleting,
    stop: clearTimer,
  };
}
