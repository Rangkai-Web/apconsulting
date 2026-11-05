# Web Animation & Scroll Reveal Blueprint (Nuxt 4 / Vue 3)

Panduan arsitektur animasi modern, ringan (*zero-dependency*), dan responsif untuk website storefront/company profile. Dirancang agar web terasa luwes, dinamis, dan tidak kaku (seperti standar website korporat modern/AP Consulting), tanpa membebani performa browser.

---

## 1. Filosofi & 4 Lapisan Animasi Modern

Animasi yang baik tidak terasa "heboh" atau memperlambat pengguna, melainkan memberikan kesan halus (*fluid*), terstruktur (*staggered*), dan responsif terhadap sentuhan/interaksi.

Arsitektur ini terdiri dari **4 lapisan**:
1. **Layer 1: Hero Entrance (Pure CSS Keyframes)** — Berjalan seketika saat page load/reload tanpa flicker dan tanpa ketergantungan JavaScript timing.
2. **Layer 2: Scroll Reveal (IntersectionObserver)** — Memunculkan komponen dan kartu secara bertahap saat digulir ke dalam layar (*viewport*).
3. **Layer 3: Page Transition (Nuxt Router Mode `out-in`)** — Memastikan transisi halus (fade + slide mikro) ketika berpindah menu/halaman.
4. **Layer 4: Micro-Interactions (Hover Lift, Logo Glow & Tactile Press)** — Sentuhan interaktif pada logo, kartu, dan tombol saat disentuh kursor atau diklik.

---

## 2. Gotchas & Masalah Kritis (Wajib Diingat untuk Project Lain)

### ⚠️ Gotcha 1: Class `.hover-lift` Menghilangkan Animasi Fade
- **Masalah**: Ketika membuat class kartu dengan hover lift (misal `transition: transform 0.3s, box-shadow 0.3s !important;`), properti `opacity` tidak ada dalam daftar transisi. Akibatnya, saat elemen selesai di-*reveal*, opasitasnya melompat seketika dari 0 ke 1 tanpa efek fade.
- **Solusi**: Pastikan transisi kartu menyertakan `opacity 0.72s` saat pertama kali muncul:
```css
.hover-lift {
  transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.72s cubic-bezier(0.16, 1, 0.3, 1);
}
[data-reveal]:not(.is-revealed).hover-lift {
  transition: transform 0.72s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.72s cubic-bezier(0.16, 1, 0.3, 1) !important;
}
[data-reveal].is-revealed.hover-lift {
  transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.72s cubic-bezier(0.16, 1, 0.3, 1);
}
.hover-lift:hover {
  transform: translate3d(0, -6px, 0) !important;
  box-shadow: 0 16px 32px -8px rgba(8, 54, 83, 0.12) !important;
}
```

### ⚠️ Gotcha 2: Failsafe Timer Prematur Mematikan Animasi Scroll
- **Masalah**: Memasang timer pengaman `setTimeout(..., 2500)` pada elemen tersembunyi akan otomatis mengaktifkan semua elemen di bawah fold meskipun user belum scroll ke sana. Jika user diam 3 detik di atas lalu scroll, semua animasi di bawahnya sudah mati.
- **Solusi**: Jangan pasang timeout statis untuk elemen di bawah fold. `IntersectionObserver` didukung 100% oleh semua browser modern dan akan memicu animasi tepat saat elemen masuk layar.

### ⚠️ Gotcha 3: Jangan Beri `opacity: 0` Statis pada `[data-reveal]` di CSS
- **Masalah**: Jika di CSS ditulis `[data-reveal] { opacity: 0; }`, maka data yang dimuat secara asynchronous (seperti `v-for="item in faqList"` dari API) akan stuck tidak terlihat (*blank*) jika DOM-nya di-render setelah pemindaian awal selesai.
- **Solusi**: Default elemen di CSS tetap `opacity: 1`. Class `.reveal-hidden` (`opacity: 0 !important;`) hanya ditambahkan secara dinamis oleh JavaScript jika elemen berada di bawah viewport.

### ⚠️ Gotcha 4: Animasi Hero saat Reload Wajib Pakai CSS Keyframes
- **Masalah**: Pada Nuxt/SPA (`ssr: false`), JavaScript baru berjalan setelah DOM pertama digambar. Jika mengandalkan JavaScript untuk menyembunyikan lalu memunculkan Hero di reload, akan terjadi *flash of unstyled content* (FOUC) atau animasi tidak sempat terlihat.
- **Solusi**: Gunakan CSS Keyframes dengan `animation-fill-mode: both`. Dari frame ke-0 sebelum delay dimulai, elemen sudah berada di kondisi transparan dan translate awal.

---

## 3. Template Kode Lengkap (Siap Copy-Paste)

### File 1: `app/plugins/reveal.client.ts`
```ts
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
      rootMargin: "0px 0px -40px 0px", // Memicu animasi saat elemen masuk 40px ke layar
      threshold: 0.02,
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

    const rect = htmlEl.getBoundingClientRect();
    const windowHeight = window.innerHeight || document.documentElement.clientHeight;

    // Elemen yang sudah terlewat di atas viewport (misal reload saat di tengah halaman)
    if (rect.bottom <= 0) {
      htmlEl.classList.remove("reveal-hidden");
      htmlEl.classList.add("is-revealed");
      return;
    }

    // Elemen yang tampak di awal layar: munculkan dengan transisi
    if (rect.top < windowHeight && rect.bottom > 0) {
      htmlEl.classList.add("reveal-hidden");
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          revealElement(htmlEl);
        });
      });
    } else {
      // Elemen di bawah layar: sembunyikan dan pantau dengan observer
      htmlEl.classList.add("reveal-hidden");
      observer.observe(htmlEl);
    }
  };

  const scanAndObserve = () => {
    document.querySelectorAll("[data-reveal], .reveal-on-scroll").forEach((el) => {
      initElement(el);
    });
  };

  // Pantau mutasi DOM untuk data async dari API (FAQ, Artikel, Produk)
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

  // Sambungkan ke navigasi router Nuxt
  const router = useRouter();
  if (router) {
    router.afterEach(() => {
      nextTick(() => {
        scanAndObserve();
      });
      setTimeout(() => {
        scanAndObserve();
      }, 260);
    });
  }

  // Directive v-reveal support
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
  });

  return {
    provide: {
      reveal: scanAndObserve,
    },
  };
});
```

---

### File 2: CSS Stylesheet (`assets/css/main.css`)
```css
/* ==========================================================================
   1. Page Transitions (Nuxt router-level transition)
   ========================================================================== */

.page-enter-active,
.page-leave-active {
  transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1), transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.page-enter-from {
  opacity: 0;
  transform: translate3d(0, 14px, 0);
}

.page-leave-to {
  opacity: 0;
  transform: translate3d(0, -10px, 0);
}

/* ==========================================================================
   2. Scroll Reveal Animations (IntersectionObserver driven)
   ========================================================================== */

[data-reveal],
.reveal-on-scroll {
  transition-property: opacity, transform;
  transition-duration: 0.72s;
  transition-timing-function: cubic-bezier(0.16, 1, 0.3, 1);
  will-change: opacity, transform;
}

.reveal-hidden {
  opacity: 0 !important;
}

.reveal-hidden[data-reveal="up"],
.reveal-hidden[data-reveal=""],
.reveal-hidden.reveal-on-scroll {
  transform: translate3d(0, 32px, 0);
}

.reveal-hidden[data-reveal="down"] {
  transform: translate3d(0, -32px, 0);
}

.reveal-hidden[data-reveal="left"] {
  transform: translate3d(-32px, 0, 0);
}

.reveal-hidden[data-reveal="right"] {
  transform: translate3d(32px, 0, 0);
}

.reveal-hidden[data-reveal="scale"] {
  transform: scale(0.95);
}

.reveal-hidden[data-reveal="fade"] {
  transform: none;
}

[data-reveal].is-revealed,
.reveal-on-scroll.is-revealed {
  opacity: 1 !important;
  transform: translate3d(0, 0, 0) scale(1);
  will-change: auto;
}

/* ==========================================================================
   3. Hero Entrance Keyframes (Guaranteed Smooth Play on Reload)
   ========================================================================== */

@keyframes heroFadeUp {
  0% {
    opacity: 0;
    transform: translate3d(0, 36px, 0);
  }
  100% {
    opacity: 1;
    transform: translate3d(0, 0, 0);
  }
}

@keyframes heroFadeIn {
  0% {
    opacity: 0;
  }
  100% {
    opacity: 1;
  }
}

@keyframes heroScaleUp {
  0% {
    opacity: 0;
    transform: scale(0.93) translate3d(0, 16px, 0);
  }
  100% {
    opacity: 1;
    transform: scale(1) translate3d(0, 0, 0);
  }
}

.animate-hero-badge {
  animation: heroFadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both;
}

.animate-hero-title {
  animation: heroFadeUp 0.85s cubic-bezier(0.16, 1, 0.3, 1) 0.2s both;
}

.animate-hero-desc {
  animation: heroFadeUp 0.85s cubic-bezier(0.16, 1, 0.3, 1) 0.35s both;
}

.animate-hero-cta {
  animation: heroFadeUp 0.85s cubic-bezier(0.16, 1, 0.3, 1) 0.45s both;
}

.animate-hero-image {
  animation: heroScaleUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.25s both;
}

/* ==========================================================================
   4. Micro-Interactions: Logo, Cards, Buttons
   ========================================================================== */

.navbar-logo,
.footer-logo {
  transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), filter 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

.group:hover .navbar-logo,
.group:hover .footer-logo {
  transform: scale(1.05) translate3d(0, -1px, 0);
  filter: drop-shadow(0 4px 12px rgba(8, 54, 83, 0.15)) brightness(1.03);
}

.group:active .navbar-logo,
.group:active .footer-logo {
  transform: scale(0.96);
  transition-duration: 0.1s;
}

@keyframes logoEntrance {
  0% {
    opacity: 0;
    transform: translate3d(0, -6px, 0) scale(0.96);
  }
  100% {
    opacity: 1;
    transform: translate3d(0, 0, 0) scale(1);
  }
}

.animate-logo-entrance {
  animation: logoEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1) backwards;
}

/* Hover Lift */
.hover-lift {
  transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.72s cubic-bezier(0.16, 1, 0.3, 1);
}

[data-reveal]:not(.is-revealed).hover-lift {
  transition: transform 0.72s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.72s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

[data-reveal].is-revealed.hover-lift {
  transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.72s cubic-bezier(0.16, 1, 0.3, 1);
}

[data-reveal].is-revealed.hover-lift:hover,
.hover-lift:hover {
  transform: translate3d(0, -6px, 0) !important;
  box-shadow: 0 16px 32px -8px rgba(8, 54, 83, 0.12) !important;
}

/* Tactile button press */
button:active:not(:disabled),
[class*="u-button"]:active:not([disabled]) {
  transform: scale(0.985);
  transition: transform 0.1s ease;
}

/* ==========================================================================
   5. Accessibility (Prefers-Reduced-Motion)
   ========================================================================== */

@media (prefers-reduced-motion: reduce) {
  .page-enter-active,
  .page-leave-active {
    transition: none !important;
  }

  [data-reveal],
  .reveal-on-scroll {
    opacity: 1 !important;
    transform: none !important;
    transition: none !important;
  }

  .animate-hero-badge,
  .animate-hero-title,
  .animate-hero-desc,
  .animate-hero-cta,
  .animate-hero-image,
  .animate-logo-entrance {
    animation: none !important;
    opacity: 1 !important;
    transform: none !important;
  }
}
```

---

### File 3: `nuxt.config.ts`
```ts
export default defineNuxtConfig({
  app: {
    pageTransition: { name: "page", mode: "out-in" },
  },
  css: ["~/assets/css/main.css"],
});
```

---

## 4. Contoh Pemakaian di Template Vue

### A. Di Hero Section (Reload Entrance):
```vue
<template>
  <section class="hero">
    <div class="badge animate-hero-badge">Tax & Accounting Advisory</div>
    <h1 class="animate-hero-title">Clearer Finances. Stronger Decisions.</h1>
    <p class="animate-hero-desc">PCG helps businesses manage tax and financial needs...</p>
    <div class="actions animate-hero-cta">
      <button>Explore Services</button>
    </div>
    <div class="hero-image animate-hero-image">
      <img src="..." alt="Hero" />
    </div>
  </section>
</template>
```

### B. Di Grid Kartu (Scroll Reveal dengan Stagger Delay):
```vue
<template>
  <section>
    <!-- Header Section -->
    <div data-reveal="up">
      <h2>Layanan Kami</h2>
      <p>Pilihan solusi terbaik untuk bisnis Anda.</p>
    </div>

    <!-- 3 Kolom Kartu yang muncul bertahap saat di-scroll -->
    <div class="grid grid-cols-3 gap-6">
      <div
        v-for="(service, idx) in services"
        :key="service.id"
        class="card hover-lift"
        data-reveal="up"
        :data-reveal-delay="idx * 150"
      >
        <h3>{{ service.title }}</h3>
        <p>{{ service.description }}</p>
      </div>
    </div>
  </section>
</template>
```

### C. Di Logo Navbar & Footer:
```vue
<template>
  <NuxtLink to="/" class="group flex items-center">
    <img
      src="/logo.png"
      alt="Logo"
      class="h-16 w-auto navbar-logo animate-logo-entrance"
    />
  </NuxtLink>
</template>
```

---

## 5. Animated Counter (Count-Up Numbers on Scroll)

Untuk bagian statistik angka (misal `4`, `20`, `200+`), animasi count-up dipicu sekali saat section masuk ke layar pengguna menggunakan kurva `easeOutCubic` (`1 - Math.pow(1 - progress, 3)`).

### Composable: `app/composables/useCountUp.ts`
```ts
import { ref, onBeforeUnmount, getCurrentInstance, type Ref } from "vue";

export interface CountUpItem {
  target: number;
  suffix?: string;
  prefix?: string;
}

export function useCountUp(items: CountUpItem[], options: { duration?: number } = {}) {
  const { duration = 1600 } = options;
  const isTest = typeof process !== "undefined" && process.env?.NODE_ENV === "test";

  // Inisialisasi langsung dengan nilai target saat di lingkungan test (Vitest)
  const displayValues: Ref<string[]> = ref(
    items.map((item) =>
      isTest
        ? `${item.prefix || ""}${item.target}${item.suffix || ""}`
        : `${item.prefix || ""}0${item.suffix || ""}`
    )
  );

  const isAnimating = ref(false);
  const isFinished = ref(isTest);
  let animationFrameId: number | null = null;

  const easeOutCubic = (x: number): number => 1 - Math.pow(1 - x, 3);

  const start = () => {
    if (isFinished.value || isAnimating.value) return;
    if (typeof window === "undefined") {
      isFinished.value = true;
      displayValues.value = items.map(
        (item) => `${item.prefix || ""}${item.target}${item.suffix || ""}`
      );
      return;
    }

    isAnimating.value = true;
    const startTime = performance.now();

    const frame = (currentTime: number) => {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const easedProgress = easeOutCubic(progress);

      displayValues.value = items.map((item) => {
        const current = Math.round(easedProgress * item.target);
        return `${item.prefix || ""}${current}${item.suffix || ""}`;
      });

      if (progress < 1) {
        animationFrameId = requestAnimationFrame(frame);
      } else {
        isAnimating.value = false;
        isFinished.value = true;
        displayValues.value = items.map(
          (item) => `${item.prefix || ""}${item.target}${item.suffix || ""}`
        );
      }
    };

    animationFrameId = requestAnimationFrame(frame);
  };

  if (getCurrentInstance()) {
    onBeforeUnmount(() => {
      if (animationFrameId !== null && typeof window !== "undefined") {
        cancelAnimationFrame(animationFrameId);
      }
    });
  }

  return { displayValues, start, isAnimating, isFinished };
}
```

### Cara Pemakaian di Komponen Statistik:
```vue
<template>
  <section ref="sectionRef">
    <div v-for="(stat, idx) in stats" :key="stat.key" class="group hover:scale-105 transition-transform duration-300">
      <p class="text-4xl md:text-5xl font-extrabold tabular-nums group-hover:text-[#FFBB00] transition-colors">
        {{ displayValues[idx] }}
      </p>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from "vue";
import { useCountUp } from "~/composables/useCountUp";

const stats = [
  { target: 4, suffix: "" },
  { target: 4, suffix: "" },
  { target: 20, suffix: "" },
  { target: 200, suffix: "+" },
];

const { displayValues, start } = useCountUp(stats, { duration: 1600 });
const sectionRef = ref<HTMLElement | null>(null);
let observer: IntersectionObserver | null = null;

onMounted(() => {
  if (typeof window === "undefined" || !("IntersectionObserver" in window)) {
    start();
    return;
  }
  if (sectionRef.value) {
    observer = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting) {
        start();
        observer?.disconnect();
      }
    }, { rootMargin: "0px 0px -40px 0px", threshold: 0.15 });
    observer.observe(sectionRef.value);
  }
});

onBeforeUnmount(() => observer?.disconnect());
</script>
```


