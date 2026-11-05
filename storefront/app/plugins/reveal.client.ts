import { nextTick } from "vue";

export default defineNuxtPlugin((nuxtApp) => {
  if (typeof window === "undefined" || !("IntersectionObserver" in window)) {
    return;
  }

  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  const revealElement = (el: HTMLElement) => {
    const delay = el.getAttribute("data-reveal-delay");
    if (delay) {
      el.style.transitionDelay = `${delay}ms`;
      el.addEventListener(
        "transitionend",
        () => {
          el.style.transitionDelay = "";
        },
        { once: true }
      );
    }
    el.classList.remove("reveal-hidden");
    el.classList.add("is-revealed");
  };

  const observer = new IntersectionObserver(
    (entries) => {
      for (const entry of entries) {
        if (entry.isIntersecting) {
          const el = entry.target as HTMLElement;
          revealElement(el);
          observer.unobserve(el);
        }
      }
    },
    {
      rootMargin: "0px 0px -50px 0px",
      threshold: 0.05,
    }
  );

  const initElement = (el: Element) => {
    const htmlEl = el as HTMLElement;
    if (htmlEl.classList.contains("is-revealed") || htmlEl.dataset.revealInit === "true") return;
    htmlEl.dataset.revealInit = "true";

    if (prefersReducedMotion) {
      htmlEl.classList.remove("reveal-hidden");
      htmlEl.classList.add("is-revealed");
      return;
    }

    // Check element position relative to viewport
    const rect = htmlEl.getBoundingClientRect();
    const windowHeight = window.innerHeight || document.documentElement.clientHeight;

    // Elements above the viewport (e.g. user refreshed mid-page)
    if (rect.bottom <= 0) {
      htmlEl.classList.remove("reveal-hidden");
      htmlEl.classList.add("is-revealed");
      return;
    }

    // Elements currently visible in viewport: play smooth entrance reveal
    if (rect.top < windowHeight && rect.bottom > 0) {
      htmlEl.classList.add("reveal-hidden");
      // Double rAF ensures the browser paints the hidden frame before transitioning
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          revealElement(htmlEl);
        });
      });
    } else {
      // Elements below the viewport; hide until scrolled into view
      htmlEl.classList.add("reveal-hidden");
      observer.observe(htmlEl);

      // Failsafe timer: ensure no element is ever stuck hidden under any browser condition
      setTimeout(() => {
        if (htmlEl.classList.contains("reveal-hidden") && !htmlEl.classList.contains("is-revealed")) {
          revealElement(htmlEl);
          observer.unobserve(htmlEl);
        }
      }, 2500);
    }
  };

  const scanAndObserve = () => {
    document.querySelectorAll("[data-reveal], .reveal-on-scroll").forEach((el) => {
      initElement(el);
    });
  };

  // Watch DOM mutations for dynamic/async data (like FAQs or articles fetched from API)
  let mutationObserver: MutationObserver | null = null;
  if ("MutationObserver" in window) {
    let debounceTimer: ReturnType<typeof setTimeout> | null = null;
    mutationObserver = new MutationObserver(() => {
      if (debounceTimer) clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        scanAndObserve();
      }, 40);
    });
  }

  nuxtApp.hook("app:mounted", () => {
    scanAndObserve();
    if (mutationObserver && document.body) {
      mutationObserver.observe(document.body, {
        childList: true,
        subtree: true,
      });
    }
  });

  nuxtApp.hook("page:finish", () => {
    nextTick(() => {
      scanAndObserve();
    });
  });

  // Also hook into router navigation directly
  const router = useRouter();
  if (router) {
    router.afterEach(() => {
      nextTick(() => {
        scanAndObserve();
      });
      // Safety passes as page transition completes
      setTimeout(() => {
        scanAndObserve();
      }, 260);
      setTimeout(() => {
        scanAndObserve();
      }, 500);
    });
  }

  // Vue directive v-reveal support
  nuxtApp.vueApp.directive("reveal", {
    mounted(el: HTMLElement, binding) {
      if (binding.value && typeof binding.value === "string") {
        el.setAttribute("data-reveal", binding.value);
      } else if (!el.hasAttribute("data-reveal")) {
        el.setAttribute("data-reveal", "up");
      }

      if (binding.value && typeof binding.value === "object") {
        if (binding.value.delay) el.setAttribute("data-reveal-delay", String(binding.value.delay));
        if (binding.value.direction) el.setAttribute("data-reveal", binding.value.direction);
      }

      initElement(el);
    },
    unmounted(el: HTMLElement) {
      observer.unobserve(el);
    },
    getSSRProps() {
      return {};
    },
  });

  return {
    provide: {
      reveal: scanAndObserve,
    },
  };
});
