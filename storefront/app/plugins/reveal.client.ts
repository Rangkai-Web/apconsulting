import { nextTick } from "vue";

export default defineNuxtPlugin((nuxtApp) => {
  if (typeof window === "undefined" || !("IntersectionObserver" in window)) {
    return;
  }

  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  const observer = new IntersectionObserver(
    (entries) => {
      for (const entry of entries) {
        if (entry.isIntersecting) {
          const el = entry.target as HTMLElement;
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
          el.classList.add("is-revealed");
          observer.unobserve(el);
        }
      }
    },
    {
      rootMargin: "0px 0px -40px 0px",
      threshold: 0.08,
    }
  );

  const scanAndObserve = () => {
    if (prefersReducedMotion) {
      document.querySelectorAll("[data-reveal], .reveal-on-scroll").forEach((el) => {
        el.classList.add("is-revealed");
      });
      return;
    }

    document.querySelectorAll("[data-reveal], .reveal-on-scroll").forEach((el) => {
      if (!el.classList.contains("is-revealed")) {
        observer.observe(el);
      }
    });
  };

  nuxtApp.hook("app:mounted", () => {
    scanAndObserve();
  });

  nuxtApp.hook("page:finish", () => {
    nextTick(() => {
      scanAndObserve();
    });
  });

  // Vue directive v-reveal support
  nuxtApp.vueApp.directive("reveal", {
    mounted(el: HTMLElement, binding) {
      if (prefersReducedMotion) {
        el.classList.add("is-revealed");
        return;
      }

      if (binding.value && typeof binding.value === "string") {
        el.setAttribute("data-reveal", binding.value);
      } else if (!el.hasAttribute("data-reveal")) {
        el.setAttribute("data-reveal", "up");
      }

      if (binding.value && typeof binding.value === "object") {
        if (binding.value.delay) el.setAttribute("data-reveal-delay", String(binding.value.delay));
        if (binding.value.direction) el.setAttribute("data-reveal", binding.value.direction);
      }

      observer.observe(el);
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

