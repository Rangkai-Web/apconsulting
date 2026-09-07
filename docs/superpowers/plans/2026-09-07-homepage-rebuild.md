# Homepage Rebuild (PCG Company Profile) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the storefront's ecommerce homepage (`/`) with the PCG (Phangkawira Consulting Group) company-profile homepage, matching the Figma design system and the live "Homepage" frame content.

**Architecture:** Nuxt 4 SPA (`ssr: false`), one presentational Vue component per homepage section under `app/components/home/`, composed in `app/pages/index.vue`. Navbar/Footer are rewritten in place (`AppHeader.vue`, `AppFooter.vue`). Design tokens (colors, font) live in `app/assets/css/main.css` as Tailwind v4 `@theme` custom properties, consumed by Nuxt UI v4 via `app.config.ts`. No backend calls — all copy is hardcoded per the spec.

**Tech Stack:** Nuxt 4.4, Vue 3, Tailwind CSS v4 (`@theme`), Nuxt UI v4 (`UButton`, `UAccordion`/manual accordion), Vitest + `@vue/test-utils` + happy-dom.

**Spec:** `docs/superpowers/specs/2026-09-07-homepage-rebuild-design.md`

## Global Constraints

- Font: **Poppins only** (all weights), replacing the current Quicksand/Fredoka pair.
- Colors: yellow scale (primary), navy/blue scale (secondary), neutral scale — exact hexes in Task 2. Replace the old coral/green ecommerce palette entirely.
- No backend/API calls from any homepage section — all copy hardcoded in components (per spec decision).
- Follow the existing `<img src="/images/...">` convention (plain `<img>`, not `<NuxtImg>`) — matches `AppHeader.vue`/`AppFooter.vue`/`ProductCard.vue` today.
- `~` resolves to `storefront/app` (see `vitest.config.ts` and Nuxt default). Tests run under plain Vitest + happy-dom (`vitest.config.ts`), not the full Nuxt runtime — components must not rely on Nuxt auto-imports that don't exist under plain Vue (`ref`, `computed` must be explicitly imported from `'vue'` in any component that has a test).
- Button copy goes in the default slot (e.g. `<UButton>Discuss Your Needs</UButton>`), never only in a `label` prop — keeps text assertable via `wrapper.text()` in tests regardless of whether Nuxt UI components are registered in the test environment.
- Figma source of truth: file `2oCbkmAnxCENuBJ1mmMCyz`, frame `10:28973` (desktop) / `74:8038` (mobile). Node IDs per section are in the spec's section tables — re-pull `get_design_context` on a node if a task's styling needs finer pixel detail than what's described here.

---

## Task 1: Delete dead ecommerce code

The frontend is mid-pivot: `app/store/cart.ts`, `app/store/auth.ts`, `app/store/catalog.ts`, `app/services/catalogService.ts`, and `app/services/orderService.ts` **no longer exist on disk** but are still imported by several files, so `npm run typecheck` and part of the test suite are currently broken. This task removes every file that imports the missing modules (all of them are ecommerce-only and out of scope for the company-profile homepage) so the repo returns to a working baseline before any new code is added.

**Files:**
- Delete: `app/pages/tentang-kami.vue`
- Delete: `app/components/ProductCard.vue`
- Delete: `app/components/ProductGrid.vue`
- Delete: `app/components/VariantSelector.vue`
- Delete: `app/components/QtyStepper.vue`
- Delete: `app/components/RatingStars.vue`
- Delete: `app/components/EmptyState.vue`
- Delete: `app/components/AppCuteDecor.vue`
- Delete: `app/store/wishlist.ts`
- Delete: `app/services/api.ts`
- Delete: `app/services/profileService.ts`
- Delete: `app/composables/useStoreSeo.ts`
- Delete: `app/composables/useCurrency.ts`
- Delete: `app/plugins/1.store.ts` (dead stub — body is already fully commented out)
- Delete: `tests/cart.store.test.ts` (tests the nonexistent `store/cart`)
- Delete: `tests/orderService.test.ts` (tests the nonexistent `services/orderService`)
- Modify: `app/pages/index.vue` (emptied here, rebuilt in Task 15)
- Modify: `app/components/AppHeader.vue` (emptied here, rebuilt in Task 4)
- Modify: `app/components/AppFooter.vue` (emptied here, rebuilt in Task 5)
- Modify: `app/app.vue` (strip the `useStoreSeo`/`useCurrency` references here; real PCG JSON-LD content added in Task 3)

**Interfaces:**
- Produces: a repo where `npm run typecheck` and `npm run test` both run cleanly (may still show `AppHeader`/`AppFooter`/`index.vue` as trivially empty — that's expected until Tasks 4/5/15).

- [x] **Step 1: Delete the files listed above**

```bash
cd storefront
git rm app/pages/tentang-kami.vue \
  app/components/ProductCard.vue \
  app/components/ProductGrid.vue \
  app/components/VariantSelector.vue \
  app/components/QtyStepper.vue \
  app/components/RatingStars.vue \
  app/components/EmptyState.vue \
  app/components/AppCuteDecor.vue \
  app/store/wishlist.ts \
  app/services/api.ts \
  app/services/profileService.ts \
  app/composables/useStoreSeo.ts \
  app/composables/useCurrency.ts \
  app/plugins/1.store.ts \
  tests/cart.store.test.ts \
  tests/orderService.test.ts
```

- [x] **Step 2: Strip `app.vue` down to a placeholder (real content in Task 3)**

```vue
<template>
  <UApp>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </UApp>
</template>

<script setup lang="ts"></script>
```

- [x] **Step 3: Strip `index.vue`, `AppHeader.vue`, `AppFooter.vue` to empty placeholders**

`app/pages/index.vue`:
```vue
<template>
  <div />
</template>

<script setup lang="ts"></script>
```

`app/components/AppHeader.vue` and `app/components/AppFooter.vue`: same empty-`<div />` placeholder as above.

- [x] **Step 4: Verify the repo builds clean**

Run: `cd storefront && npm run typecheck && npm run test`
Expected: both PASS (no references to deleted modules remain; remaining tests are `health.test.ts`, `generate-sitemap.test.ts`, `useSeoText.test.ts`).

- [x] **Step 5: Commit**

```bash
git add -A
git commit -m "chore: remove dead ecommerce code ahead of homepage rebuild"
```

---

## Task 2: Design tokens — colors and font

Replace the ecommerce coral/green palette and Quicksand/Fredoka fonts with the PCG palette (Poppins only). Values below are the exact hexes captured from `design-system.manifest.json` (see spec's "Design tokens" section) — only the shades that exist in the Figma export are defined; there is no fabricated in-between shade.

**Files:**
- Modify: `app/assets/css/main.css`
- Modify: `app/app.config.ts`

**Interfaces:**
- Produces: Tailwind utilities `bg-primary-{50,100,500,600,700,900}`, `text-primary-*`, `bg-secondary-{50,100,500,600,700}`, `text-secondary-*`, `bg-neutral-{50,200,400,500,600,800,900}`, `text-neutral-*`, and `font-sans`/`font-heading` both resolving to Poppins. All later section tasks consume these.

- [ ] **Step 1: Replace the color/font `@theme` block in `main.css`**

```css
@import "tailwindcss";
@import "@nuxt/ui";

html {
  scroll-behavior: smooth;
}

h1, h2, h3, h4, h5, h6 {
  font-family: var(--font-heading);
}

@theme static {
  --font-sans: "Poppins", sans-serif;
  --font-heading: "Poppins", sans-serif;

  /* Yellow — Figma "Yellow/*" styles */
  --color-primary-50: #FEF9E8;
  --color-primary-100: #FBEEB7;
  --color-primary-500: #FFBB00;
  --color-primary-600: #DBB514;
  --color-primary-700: #AB8D10;
  --color-primary-900: #655409;

  /* Navy blue — Figma "Blue/*" styles */
  --color-secondary-50: #E6EBEE;
  --color-secondary-100: #B2C1CA;
  --color-secondary-500: #083653;
  --color-secondary-600: #07314C;
  --color-secondary-700: #06263B;

  /* Neutral — Figma "Netral/01..07" styles, renamed to a Tailwind-shaped scale */
  --color-neutral-50: #FFFFFF;
  --color-neutral-200: #D4D4D4;
  --color-neutral-400: #999999;
  --color-neutral-500: #666666;
  --color-neutral-600: #444444;
  --color-neutral-800: #222222;
  --color-neutral-900: #111111;
}

.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
```

Note: the `float`/`float-reverse`/`slow-spin` keyframes and `.animate-float*`/`.animate-slow-spin` classes are removed — they existed only for `AppCuteDecor.vue`, deleted in Task 1.

- [ ] **Step 2: Point Nuxt UI's neutral slot at the new scale**

In `app/app.config.ts`, change `neutral: "slate"` to `neutral: "neutral"`:

```ts
export default defineAppConfig({
  ui: {
    colors: {
      primary: "primary",
      secondary: "secondary",
      neutral: "neutral",
    },
  },
});
```

- [ ] **Step 3: Verify Poppins loads and old colors are gone**

Run: `cd storefront && npm run dev` (or `npm run build`), then:
```bash
grep -rn "Quicksand\|Fredoka\|#ff746c\|#3fff33" app/assets/css/main.css
```
Expected: no matches. `@nuxt/fonts` (already installed, see `nuxt.config.ts` modules) auto-provisions Poppins from the `--font-sans`/`--font-heading` CSS values — confirm in the dev server's network tab that a Poppins font file is requested, or check `.nuxt/nuxt-fonts-global.css` after `npm run dev` mentions Poppins.

- [ ] **Step 4: Commit**

```bash
git add app/assets/css/main.css app/app.config.ts
git commit -m "feat: swap design tokens to PCG palette and Poppins"
```

---

## Task 3: Global SEO meta and JSON-LD for PCG

`nuxt.config.ts` and `app.vue` still describe "BbystarGO — Jasa Pembelian Barang & Ready Stock" (an `OnlineStore` schema). Replace with PCG's real identity, using the address/email confirmed from the Figma footer (spec section 10).

**Files:**
- Modify: `storefront/nuxt.config.ts`
- Modify: `storefront/app/app.vue`

**Interfaces:**
- Consumes: `useSeoText().toSafeJsonLd` (kept from Task 1, `app/composables/useSeoText.ts`, signature `toSafeJsonLd(value: unknown): string`).
- Produces: `<head>` title/meta and a static `ProfessionalService` JSON-LD block present on every page.

- [ ] **Step 1: Update `app.head` in `nuxt.config.ts`**

```ts
app: {
  head: {
    htmlAttrs: { lang: "en" },
    title: "PCG — Phangkawira Consulting Group",
    titleTemplate: "%s | PCG",
    link: [{ rel: "icon", type: "image/x-icon", href: "/favicon.ico" }],
    meta: [
      {
        name: "description",
        content:
          "PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals.",
      },
      { property: "og:title", content: "PCG — Phangkawira Consulting Group" },
      {
        property: "og:description",
        content:
          "PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals.",
      },
      { property: "og:site_name", content: "PCG" },
      { property: "og:type", content: "website" },
      { property: "og:locale", content: "en_US" },
      { name: "twitter:card", content: "summary_large_image" },
      { name: "twitter:title", content: "PCG — Phangkawira Consulting Group" },
      {
        name: "twitter:description",
        content:
          "PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals.",
      },
    ],
  },
},
```

Leave `runtimeConfig.public.siteUrl`'s default as-is for now (still `bbystargo.com`) — updating the production domain is a deployment concern, not part of this homepage rebuild; flag it to the user separately.

- [ ] **Step 2: Replace the JSON-LD in `app.vue` with hardcoded PCG data**

```vue
<template>
  <UApp>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </UApp>
</template>

<script setup lang="ts">
import { useSeoText } from "~/composables/useSeoText";

const { toSafeJsonLd } = useSeoText();
const config = useRuntimeConfig();

useHead(() => ({
  script: [
    {
      type: "application/ld+json",
      innerHTML: toSafeJsonLd({
        "@context": "https://schema.org",
        "@type": "ProfessionalService",
        name: "Phangkawira Consulting Group",
        url: config.public.siteUrl,
        email: "pcgserver9@gmail.com",
        address: {
          "@type": "PostalAddress",
          streetAddress:
            "Lt 3 Sayur Sentul, Blk. D, Rukan Cordoba, Jl. Green Lake City Boulevard No.23",
          addressLocality: "Kota Tangerang",
          addressRegion: "Banten",
          postalCode: "15146",
          addressCountry: "ID",
        },
      }),
    },
  ],
}));
</script>
```

- [ ] **Step 3: Verify**

Run: `cd storefront && npm run typecheck`
Expected: PASS.

- [ ] **Step 4: Commit**

```bash
git add nuxt.config.ts app/app.vue
git commit -m "feat: replace BbystarGO SEO meta and JSON-LD with PCG identity"
```

---

## Task 4: Navbar (`AppHeader.vue`)

Rebuild as a static nav bar (no cart/auth/wishlist state — those stores no longer exist and the company site has no login). Confirmed content: logo, `Home / Services / Insights / About / Contact` links, "Discuss Your Needs" CTA button, collapsing to a hamburger toggle below `md`.

**Files:**
- Modify: `app/components/AppHeader.vue`
- Test: `tests/AppHeader.test.ts`

**Interfaces:**
- Produces: no props, no emits — self-contained. Rendered by `app/layouts/default.vue` (unchanged, already renders `<AppHeader />`).
- Note: the PCG logo file doesn't exist in `public/images/` yet — Figma MCP export quota was exhausted mid-session. Until the real logo is exported, render the text "PCG" as a stand-in wrapped in the same `<NuxtLink to="/">` so swapping in `<img src="/images/pcg-logo.svg">` later is a one-line change. Flag this to the user in the task's commit message.

- [ ] **Step 1: Write the failing test**

```ts
// tests/AppHeader.test.ts
import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import AppHeader from '../app/components/AppHeader.vue'

describe('AppHeader', () => {
  it('renders all primary nav links and the CTA', () => {
    const wrapper = mount(AppHeader)
    const text = wrapper.text()
    expect(text).toContain('Home')
    expect(text).toContain('Services')
    expect(text).toContain('Insights')
    expect(text).toContain('About')
    expect(text).toContain('Contact')
    expect(text).toContain('Discuss Your Needs')
  })

  it('toggles the mobile menu open state when the hamburger is clicked', async () => {
    const wrapper = mount(AppHeader)
    expect(wrapper.find('[data-testid="mobile-menu"]').exists()).toBe(false)
    await wrapper.find('[data-testid="hamburger-button"]').trigger('click')
    expect(wrapper.find('[data-testid="mobile-menu"]').exists()).toBe(true)
  })
})
```

- [ ] **Step 2: Run test to verify it fails**

Run: `cd storefront && npx vitest run tests/AppHeader.test.ts`
Expected: FAIL (`AppHeader.vue` is still the empty placeholder from Task 1).

- [ ] **Step 3: Implement `AppHeader.vue`**

```vue
<template>
  <header class="sticky top-0 z-40 border-b border-neutral-200 bg-white">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 md:px-8">
      <NuxtLink to="/" class="text-2xl font-bold text-secondary-700">PCG</NuxtLink>

      <nav class="hidden items-center gap-8 md:flex">
        <NuxtLink to="/" class="text-neutral-500 hover:text-secondary-700">Home</NuxtLink>
        <NuxtLink to="/services" class="text-neutral-500 hover:text-secondary-700">Services</NuxtLink>
        <NuxtLink to="/insight" class="text-neutral-500 hover:text-secondary-700">Insights</NuxtLink>
        <NuxtLink to="/about" class="text-neutral-500 hover:text-secondary-700">About</NuxtLink>
        <NuxtLink to="/contact" class="text-neutral-500 hover:text-secondary-700">Contact</NuxtLink>
      </nav>

      <UButton
        to="/contact"
        color="primary"
        variant="solid"
        size="lg"
        class="hidden md:inline-flex"
      >
        Discuss Your Needs
      </UButton>

      <button
        type="button"
        data-testid="hamburger-button"
        class="p-2 md:hidden"
        :aria-expanded="mobileOpen"
        aria-label="Toggle menu"
        @click="mobileOpen = !mobileOpen"
      >
        <Icon name="lucide:menu" class="size-6 text-secondary-700" />
      </button>
    </div>

    <div v-if="mobileOpen" data-testid="mobile-menu" class="flex flex-col gap-4 border-t border-neutral-200 px-4 py-4 md:hidden">
      <NuxtLink to="/" class="text-neutral-500" @click="mobileOpen = false">Home</NuxtLink>
      <NuxtLink to="/services" class="text-neutral-500" @click="mobileOpen = false">Services</NuxtLink>
      <NuxtLink to="/insight" class="text-neutral-500" @click="mobileOpen = false">Insights</NuxtLink>
      <NuxtLink to="/about" class="text-neutral-500" @click="mobileOpen = false">About</NuxtLink>
      <NuxtLink to="/contact" class="text-neutral-500" @click="mobileOpen = false">Contact</NuxtLink>
      <UButton to="/contact" color="primary" variant="solid" block>Discuss Your Needs</UButton>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref } from "vue";

const mobileOpen = ref(false);
</script>
```

`icon/Hamburger_Icon.svg` exists in `public/images/icon/` but is a bare custom SVG shape, not from the icon library already wired into the project (`@nuxt/icon`, confirmed installed in `nuxt.config.ts` modules) — using `lucide:menu` via `<Icon>` is less setup than importing a raw SVG file as a component. Swap to the exported asset instead if the user wants pixel-exact match with Figma.

- [ ] **Step 4: Run test to verify it passes**

Run: `cd storefront && npx vitest run tests/AppHeader.test.ts`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/components/AppHeader.vue tests/AppHeader.test.ts
git commit -m "feat: rebuild AppHeader as PCG navbar (logo is a text stand-in pending Figma export)"
```

---

## Task 5: Footer (`AppFooter.vue`)

Rebuild with the real content confirmed from Figma node `3:14329` (spec section 10).

**Files:**
- Modify: `app/components/AppFooter.vue`
- Test: `tests/AppFooter.test.ts`

**Interfaces:**
- Produces: no props, no emits. Rendered by `app/layouts/default.vue` (unchanged).

- [ ] **Step 1: Write the failing test**

```ts
// tests/AppFooter.test.ts
import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import AppFooter from '../app/components/AppFooter.vue'

describe('AppFooter', () => {
  it('renders contact details and the copyright line', () => {
    const wrapper = mount(AppFooter)
    const text = wrapper.text()
    expect(text).toContain('pcgserver9@gmail.com')
    expect(text).toContain('Kota Tangerang, Banten 15146')
    expect(text).toContain('Copyright © 2026 Phangkawira Consulting Group. All Rights Reserved')
  })

  it('lists all four services', () => {
    const wrapper = mount(AppFooter)
    const text = wrapper.text()
    expect(text).toContain('Taxation')
    expect(text).toContain('Accounting & Bookkeeping')
    expect(text).toContain('Financial Services')
    expect(text).toContain('Business Support')
  })
})
```

- [ ] **Step 2: Run test to verify it fails**

Run: `cd storefront && npx vitest run tests/AppFooter.test.ts`
Expected: FAIL.

- [ ] **Step 3: Implement `AppFooter.vue`**

```vue
<template>
  <footer class="bg-neutral-50 px-4 py-16 md:px-8">
    <div class="mx-auto grid max-w-7xl gap-12 md:grid-cols-[2fr_1fr_1fr_1.5fr]">
      <div>
        <p class="text-2xl font-bold text-secondary-700">PCG</p>
        <p class="mt-4 max-w-sm text-neutral-500">
          Professional support in tax, accounting, and financial services. We're helping
          businesses move forward with clarity and confidence.
        </p>
        <div class="mt-6 flex gap-3">
          <a
            v-for="social in socials"
            :key="social.name"
            :href="social.href"
            :aria-label="social.name"
            class="flex size-9 items-center justify-center rounded-full border border-neutral-200 text-secondary-700"
          >
            <Icon :name="social.icon" class="size-4" />
          </a>
        </div>
      </div>

      <div>
        <p class="font-semibold text-secondary-700">Quick Links</p>
        <ul class="mt-4 space-y-3 text-neutral-500">
          <li><NuxtLink to="/">Home</NuxtLink></li>
          <li><NuxtLink to="/about">About</NuxtLink></li>
          <li><NuxtLink to="/services">Services</NuxtLink></li>
          <li><NuxtLink to="/insight">Insights</NuxtLink></li>
          <li><NuxtLink to="/contact">Contact</NuxtLink></li>
        </ul>
      </div>

      <div>
        <p class="font-semibold text-secondary-700">Services</p>
        <ul class="mt-4 space-y-3 text-neutral-500">
          <li>Taxation</li>
          <li>Accounting & Bookkeeping</li>
          <li>Financial Services</li>
          <li>Business Support</li>
        </ul>
      </div>

      <div>
        <p class="font-semibold text-secondary-700">Contact Us</p>
        <p class="mt-4 flex gap-2 text-neutral-500">
          <Icon name="lucide:map-pin" class="mt-1 size-4 shrink-0" />
          <span>Lt 3 Sayur Sentul, Blk. D, Rukan Cordoba, Jl. Green Lake City Boulevard No.23, Kota Tangerang, Banten 15146</span>
        </p>
        <p class="mt-3 flex items-center gap-2 text-neutral-500">
          <Icon name="lucide:mail" class="size-4 shrink-0" />
          <a href="mailto:pcgserver9@gmail.com">pcgserver9@gmail.com</a>
        </p>
      </div>
    </div>

    <div class="mx-auto mt-12 flex max-w-7xl flex-col gap-4 border-t border-neutral-200 pt-6 text-sm text-neutral-500 md:flex-row md:items-center md:justify-between">
      <p>Copyright © 2026 Phangkawira Consulting Group. All Rights Reserved</p>
      <div class="flex gap-6">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms & Conditions</a>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
const socials = [
  { name: "Instagram", icon: "simple-icons:instagram", href: "#" },
  { name: "X", icon: "simple-icons:x", href: "#" },
  { name: "Facebook", icon: "simple-icons:facebook", href: "#" },
  { name: "LinkedIn", icon: "simple-icons:linkedin", href: "#" },
];
</script>
```

`Privacy Policy` / `Terms & Conditions` link to `#` — no such pages exist yet (not in scope, spec's "Known follow-ups").

- [ ] **Step 4: Run test to verify it passes**

Run: `cd storefront && npx vitest run tests/AppFooter.test.ts`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/components/AppFooter.vue tests/AppFooter.test.ts
git commit -m "feat: rebuild AppFooter with real PCG content"
```

---

## Task 6: Hero section

**Files:**
- Create: `app/components/home/Hero.vue`
- Test: `tests/home/Hero.test.ts`

**Interfaces:**
- Produces: no props. Consumed by `index.vue` in Task 15 as `<HomeHero />` (Nuxt auto-registers components under `app/components/home/` with a `Home` prefix).

- [ ] **Step 1: Write the failing test**

```ts
// tests/home/Hero.test.ts
import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Hero from '../../app/components/home/Hero.vue'

describe('Home Hero', () => {
  it('renders the headline, body copy, and both CTAs', () => {
    const wrapper = mount(Hero)
    const text = wrapper.text()
    expect(text).toContain('CLEARER FINANCES.')
    expect(text).toContain('STRONGER BUSINESS DECISIONS')
    expect(text).toContain('DECISIONS.')
    expect(text).toContain('PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals')
    expect(text).toContain('EXPLORE OUR SERVICES')
    expect(text).toContain('DISCUSS YOUR NEEDS')
  })
})
```

- [ ] **Step 2: Run test to verify it fails**

Run: `cd storefront && npx vitest run tests/home/Hero.test.ts`
Expected: FAIL (file doesn't exist).

- [ ] **Step 3: Implement `Hero.vue`**

```vue
<template>
  <section class="relative overflow-hidden bg-white px-4 py-16 md:px-8 md:py-24">
    <img
      src="/images/homepage/Globe_Desktop.png"
      alt=""
      aria-hidden="true"
      class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-60"
    />

    <div class="relative mx-auto grid max-w-7xl items-center gap-12 md:grid-cols-2">
      <div>
        <h1 class="text-4xl font-extrabold uppercase leading-tight text-secondary-700 md:text-6xl">
          CLEARER FINANCES.
          <span class="block italic text-primary-500">STRONGER BUSINESS DECISIONS</span>
          DECISIONS.
        </h1>
        <p class="mt-6 max-w-md text-neutral-500">
          PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs
          with a structured, professional approach tailored to their goals
        </p>
        <div class="mt-8 flex flex-col gap-4 sm:flex-row">
          <UButton color="secondary" variant="outline" size="lg">EXPLORE OUR SERVICES</UButton>
          <UButton color="primary" variant="solid" size="lg">DISCUSS YOUR NEEDS</UButton>
        </div>
      </div>

      <div class="flex items-end gap-4">
        <img src="/images/homepage/HeroContent_Desktop.png" alt="PCG — clearer finances, stronger business decisions" class="w-full" />
      </div>
    </div>
  </section>
</template>

<script setup lang="ts"></script>
```

Note: the headline in Figma is three visually distinct lines ("CLEARER FINANCES." / "STRONGER BUSINESS DECISIONS" italic yellow / "DECISIONS.") — reproduced literally here per the spec; confirm with the user this isn't a typo in the original design before shipping (the third line repeats "DECISIONS." from the second line's word). `HeroContent_Mobile.png` / `Hero_Bg_Mobile.png` should replace the desktop images below the `md` breakpoint — add `<picture>` or `hidden md:block` / `md:hidden` pairs when those mobile files are confirmed to exist (only `_Desktop` variants were verified during the spec walk for this section).

- [ ] **Step 4: Run test to verify it passes**

Run: `cd storefront && npx vitest run tests/home/Hero.test.ts`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/components/home/Hero.vue tests/home/Hero.test.ts
git commit -m "feat: add homepage Hero section"
```

---

## Task 7: About PCG section

**Files:**
- Create: `app/components/home/AboutPcg.vue`
- Test: `tests/home/AboutPcg.test.ts`

**Interfaces:**
- Produces: no props. Consumed as `<HomeAboutPcg />`.

- [ ] **Step 1: Write the failing test**

```ts
// tests/home/AboutPcg.test.ts
import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import AboutPcg from '../../app/components/home/AboutPcg.vue'

describe('Home AboutPcg', () => {
  it('renders the headline and CTA', () => {
    const wrapper = mount(AboutPcg)
    const text = wrapper.text()
    expect(text).toContain('ABOUT PCG')
    expect(text).toContain('More than accountants.')
    expect(text).toContain('Partners in your progress.')
    expect(text).toContain('DISCOVER HOW WE HELP')
  })
})
```

- [ ] **Step 2: Run test to verify it fails**

Run: `cd storefront && npx vitest run tests/home/AboutPcg.test.ts`
Expected: FAIL.

- [ ] **Step 3: Implement `AboutPcg.vue`**

```vue
<template>
  <section class="px-4 py-16 md:px-8 md:py-24">
    <div class="mx-auto grid max-w-7xl gap-8 md:grid-cols-2 md:items-start">
      <div>
        <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">About PCG</p>
        <h2 class="mt-4 text-3xl font-semibold text-secondary-700 md:text-4xl">
          More than accountants.
          <span class="block italic">Partners in your progress.</span>
        </h2>
      </div>
      <div class="md:pt-10">
        <p class="text-neutral-500">
          We helps businesses, families, and ambitious individuals make confident financial
          decisions. We turn complex information into clear, practical next steps, helping you
          stay compliant, improve performance, and plan for what comes next.
        </p>
        <UButton color="primary" variant="solid" size="lg" class="mt-6">
          DISCOVER HOW WE HELP
        </UButton>
      </div>
    </div>

    <img
      src="/images/homepage/About_Desktop.png"
      alt="PCG team reviewing financial documents"
      class="mx-auto mt-12 max-w-7xl rounded-lg"
    />
  </section>
</template>

<script setup lang="ts"></script>
```

- [ ] **Step 4: Run test to verify it passes**

Run: `cd storefront && npx vitest run tests/home/AboutPcg.test.ts`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/components/home/AboutPcg.vue tests/home/AboutPcg.test.ts
git commit -m "feat: add homepage About PCG section"
```

---

## Task 8: Stats section ("PCG At a Glance")

**Files:**
- Create: `app/components/home/Stats.vue`
- Test: `tests/home/Stats.test.ts`

**Interfaces:**
- Produces: no props. Consumed as `<HomeStats />`.

- [ ] **Step 1: Write the failing test**

```ts
// tests/home/Stats.test.ts
import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Stats from '../../app/components/home/Stats.vue'

describe('Home Stats', () => {
  it('renders all four stats with their labels', () => {
    const wrapper = mount(Stats)
    const text = wrapper.text()
    expect(text).toContain('4')
    expect(text).toContain('Core service areas')
    expect(text).toContain('Steps in our approach')
    expect(text).toContain('20')
    expect(text).toContain('Integrated support partner')
    expect(text).toContain('200+')
    expect(text).toContain('Customers')
  })
})
```

- [ ] **Step 2: Run test to verify it fails**

Run: `cd storefront && npx vitest run tests/home/Stats.test.ts`
Expected: FAIL.

- [ ] **Step 3: Implement `Stats.vue`**

```vue
<template>
  <section class="bg-secondary-700 px-4 py-16 text-white md:px-8 md:py-24">
    <div class="mx-auto max-w-7xl md:flex md:items-end md:justify-between">
      <div class="max-w-xl">
        <p class="text-sm font-semibold uppercase tracking-wide text-primary-500">PCG at a Glance</p>
        <h2 class="mt-4 text-3xl font-bold italic md:text-4xl">
          A structured foundation for better business decisions.
        </h2>
      </div>
      <p class="mt-6 max-w-sm text-neutral-200 md:mt-0 md:text-right">
        Key facts about our service model and approach—presented without unsupported client or
        performance claims.
      </p>
    </div>

    <div class="mx-auto mt-12 grid max-w-7xl grid-cols-2 divide-y divide-neutral-600 md:grid-cols-4 md:divide-x md:divide-y-0">
      <div v-for="stat in stats" :key="stat.label" class="px-4 py-6 text-center">
        <p class="text-4xl font-bold">{{ stat.value }}</p>
        <p class="mt-2 text-neutral-200">{{ stat.label }}</p>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const stats = [
  { value: "4", label: "Core service areas" },
  { value: "4", label: "Steps in our approach" },
  { value: "20", label: "Integrated support partner" },
  { value: "200+", label: "Customers" },
];
</script>
```

- [ ] **Step 4: Run test to verify it passes**

Run: `cd storefront && npx vitest run tests/home/Stats.test.ts`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/components/home/Stats.vue tests/home/Stats.test.ts
git commit -m "feat: add homepage Stats section"
```

---

## Task 9: How We Help section (3 cards)

**Files:**
- Create: `app/components/home/HowWeHelp.vue`
- Test: `tests/home/HowWeHelp.test.ts`

**Interfaces:**
- Produces: no props. Consumed as `<HomeHowWeHelp />`.

- [ ] **Step 1: Write the failing test**

```ts
// tests/home/HowWeHelp.test.ts
import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import HowWeHelp from '../../app/components/home/HowWeHelp.vue'

describe('Home HowWeHelp', () => {
  it('renders the headline and all three cards', () => {
    const wrapper = mount(HowWeHelp)
    const text = wrapper.text()
    expect(text).toContain('HOW WE HELP')
    expect(text).toContain('Your Business Deserves')
    expect(text).toContain('More Than Routine Bookkeeping.')
    expect(text).toContain('Compliance with confidence.')
    expect(text).toContain('Build a Stronger Financial Foundation')
    expect(text).toContain('Insight for better decisions.')
  })
})
```

- [ ] **Step 2: Run test to verify it fails**

Run: `cd storefront && npx vitest run tests/home/HowWeHelp.test.ts`
Expected: FAIL.

- [ ] **Step 3: Implement `HowWeHelp.vue`**

```vue
<template>
  <section class="px-4 py-16 md:px-8 md:py-24">
    <div class="mx-auto max-w-7xl md:grid md:grid-cols-2 md:items-start md:gap-12">
      <div>
        <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">How We Help</p>
        <h2 class="mt-4 text-3xl font-semibold text-secondary-700 md:text-4xl">
          <span class="italic">Your Business Deserves</span>
          <span class="block">More Than Routine Bookkeeping.</span>
        </h2>
      </div>
      <p class="mt-6 text-neutral-500 md:mt-0 md:pt-10">
        We help businesses manage tax, accounting, bookkeeping, and financial reporting with
        clarity and structure, so you can make confident decisions and focus on growth.
      </p>
    </div>

    <img
      src="/images/homepage/HowWeHelp_Desktop.png"
      alt="PCG team"
      class="mx-auto mt-10 max-w-7xl rounded-lg grayscale"
    />

    <div class="mx-auto mt-12 grid max-w-7xl gap-6 md:grid-cols-3">
      <div v-for="card in cards" :key="card.title" class="rounded-lg border border-neutral-200 bg-gradient-to-b from-secondary-50 to-white p-6">
        <img :src="card.icon" :alt="card.title" class="size-14" />
        <h3 class="mt-4 font-semibold text-secondary-700">{{ card.title }}</h3>
        <p class="mt-2 text-neutral-500">{{ card.body }}</p>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const cards = [
  {
    icon: "/images/icon/Compliance_Icon.svg",
    title: "Compliance with confidence.",
    body: "Tax compliance, filings, reviews, tax planning, and audit support—handled with care and clarity",
  },
  {
    icon: "/images/icon/Financial_Icon.svg",
    title: "Build a Stronger Financial Foundation",
    body: "Accurate bookkeeping, financial reporting, bank reconciliation, and journal adjustments to keep your business organized.",
  },
  {
    icon: "/images/icon/Analysis_Icon.svg",
    title: "Insight for better decisions.",
    body: "Financial analysis, cash-flow forecasts, budgeting, and performance reviews to help you plan ahead.",
  },
];
</script>
```

The Figma frame renders `HowWeHelp_Desktop.png` in duotone/grayscale (spec section 4 note) — `grayscale` class approximates that; swap for a closer CSS filter (e.g. `sepia` + `hue-rotate` tuned to `secondary-700`) if the plain grayscale doesn't match closely enough once compared against the Figma frame.

- [ ] **Step 4: Run test to verify it passes**

Run: `cd storefront && npx vitest run tests/home/HowWeHelp.test.ts`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/components/home/HowWeHelp.vue tests/home/HowWeHelp.test.ts
git commit -m "feat: add homepage How We Help section"
```

---

## Task 10: Why Businesses Choose Us section

**Files:**
- Create: `app/components/home/WhyChooseUs.vue`
- Test: `tests/home/WhyChooseUs.test.ts`

**Interfaces:**
- Produces: no props. Consumed as `<HomeWhyChooseUs />`.

- [ ] **Step 1: Write the failing test**

```ts
// tests/home/WhyChooseUs.test.ts
import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import WhyChooseUs from '../../app/components/home/WhyChooseUs.vue'

describe('Home WhyChooseUs', () => {
  it('renders the headline and both service overlay cards', () => {
    const wrapper = mount(WhyChooseUs)
    const text = wrapper.text()
    expect(text).toContain('WHY BUSINESSES CHOOSE US')
    expect(text).toContain('The right support')
    expect(text).toContain('Tax Advisory')
    expect(text).toContain('Manage obligations, identify risks, and make more informed tax decisions.')
    expect(text).toContain('Finance & Consulting')
    expect(text).toContain('Use financial visibility to plan ahead and build sustainable growth.')
  })
})
```

- [ ] **Step 2: Run test to verify it fails**

Run: `cd storefront && npx vitest run tests/home/WhyChooseUs.test.ts`
Expected: FAIL.

- [ ] **Step 3: Implement `WhyChooseUs.vue`**

```vue
<template>
  <section class="px-4 py-16 md:px-8 md:py-24">
    <div class="mx-auto max-w-7xl">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">Why Businesses Choose Us</p>
      <h2 class="mt-4 max-w-2xl text-3xl font-semibold text-secondary-700 md:text-4xl">
        <span class="italic">The right support</span> can change the way your business moves
        forward.
      </h2>
      <p class="mt-4 max-w-2xl text-neutral-500">
        Managing a business requires more than keeping records up to date. It requires clear
        information, practical guidance, and a reliable partner who understands the bigger
        picture.
      </p>
    </div>

    <div class="mx-auto mt-10 grid max-w-7xl gap-8 md:grid-cols-2">
      <div v-for="card in cards" :key="card.title" class="relative">
        <img :src="card.image" :alt="card.title" class="w-full rounded-lg grayscale" />
        <div class="mx-6 -mt-10 max-w-xs rounded-lg bg-white p-5 shadow-lg md:mx-8">
          <div class="flex items-center gap-2">
            <img :src="card.icon" alt="" class="size-6" />
            <p class="font-semibold text-secondary-700">{{ card.title }}</p>
          </div>
          <p class="mt-2 text-sm text-neutral-500">{{ card.body }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const cards = [
  {
    image: "/images/homepage/TaxAdvisory_Desktop.png",
    icon: "/images/icon/TaxAdvisory_Icon.svg",
    title: "Tax Advisory",
    body: "Manage obligations, identify risks, and make more informed tax decisions.",
  },
  {
    image: "/images/homepage/Finance&Consulting_Desktop.png",
    icon: "/images/icon/FinanceConsult_Icon.svg",
    title: "Finance & Consulting",
    body: "Use financial visibility to plan ahead and build sustainable growth.",
  },
];
</script>
```

- [ ] **Step 4: Run test to verify it passes**

Run: `cd storefront && npx vitest run tests/home/WhyChooseUs.test.ts`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/components/home/WhyChooseUs.vue tests/home/WhyChooseUs.test.ts
git commit -m "feat: add homepage Why Businesses Choose Us section"
```

---

## Task 11: Our Approach section (4 steps)

**Files:**
- Create: `app/components/home/OurApproach.vue`
- Test: `tests/home/OurApproach.test.ts`

**Interfaces:**
- Produces: no props. Consumed as `<HomeOurApproach />`.

- [ ] **Step 1: Write the failing test**

```ts
// tests/home/OurApproach.test.ts
import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import OurApproach from '../../app/components/home/OurApproach.vue'

describe('Home OurApproach', () => {
  it('renders the headline and all four steps in order', () => {
    const wrapper = mount(OurApproach)
    const text = wrapper.text()
    expect(text).toContain('OUR APPROACH')
    expect(text).toContain('From')
    expect(text).toContain('Financial Clarity')
    expect(text).toContain('to')
    expect(text).toContain('Confident Action.')

    const stepTitles = ['Understand Your Business', 'Identify What Matters', 'Put Solutions in Place', 'Review and Move Forward']
    const positions = stepTitles.map((title) => text.indexOf(title))
    expect(positions.every((pos) => pos !== -1)).toBe(true)
    expect(positions).toEqual([...positions].sort((a, b) => a - b))
  })
})
```

- [ ] **Step 2: Run test to verify it fails**

Run: `cd storefront && npx vitest run tests/home/OurApproach.test.ts`
Expected: FAIL.

- [ ] **Step 3: Implement `OurApproach.vue`**

```vue
<template>
  <section class="px-4 py-16 md:px-8 md:py-24">
    <div class="mx-auto max-w-3xl text-center">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">Our Approach</p>
      <h2 class="mt-4 text-3xl font-semibold text-secondary-700 md:text-4xl">
        From <span class="italic">Financial Clarity</span> to Confident Action.
      </h2>
      <p class="mt-4 text-neutral-500">
        A structured approach to help your business stay compliant, organized, and ready for
        what comes next.
      </p>
    </div>

    <div class="mx-auto mt-12 grid max-w-5xl gap-6 md:grid-cols-2">
      <div v-for="step in steps" :key="step.title" class="rounded-lg border border-neutral-200 p-8 text-center">
        <img :src="step.icon" :alt="step.title" class="mx-auto size-16" />
        <h3 class="mt-4 font-semibold text-secondary-700">{{ step.title }}</h3>
        <p class="mt-2 text-neutral-500">{{ step.body }}</p>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const steps = [
  {
    icon: "/images/icon/Understand_Icon.svg",
    title: "Understand Your Business",
    body: "We assess your financial position, records, and administrative processes to understand where your business stands today.",
  },
  {
    icon: "/images/icon/Identify_Icon.svg",
    title: "Identify What Matters",
    body: "We identify risks, gaps, and priorities, then focus on the areas that need attention first.",
  },
  {
    icon: "/images/icon/Solutions_Icon.svg",
    title: "Put Solutions in Place",
    body: "We implement the agreed tax, accounting, and financial solutions based on your business needs.",
  },
  {
    icon: "/images/icon/Review_Icon.svg",
    title: "Review and Move Forward",
    body: "We review progress regularly and provide ongoing support as your business evolves.",
  },
];
</script>
```

- [ ] **Step 4: Run test to verify it passes**

Run: `cd storefront && npx vitest run tests/home/OurApproach.test.ts`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/components/home/OurApproach.vue tests/home/OurApproach.test.ts
git commit -m "feat: add homepage Our Approach section"
```

---

## Task 12: Insights section (3 article cards)

The three cards' excerpt text is identical lorem-style copy in the Figma source itself (confirmed via `get_metadata` on node `3:14290` — all three cards share the exact string "Clear perspectives on tax, accounting, and financial management, made for businesses that want to move forward with confidence."), and no thumbnail photos are exported to `public/images/`. Titles, category tags, and dates are real per-card. Ship with a neutral placeholder image per card; flag both gaps to the user rather than fabricating real article photos or unique excerpts.

**Files:**
- Create: `app/components/home/Insights.vue`
- Test: `tests/home/Insights.test.ts`

**Interfaces:**
- Produces: no props. Consumed as `<HomeInsights />`.

- [ ] **Step 1: Write the failing test**

```ts
// tests/home/Insights.test.ts
import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Insights from '../../app/components/home/Insights.vue'

describe('Home Insights', () => {
  it('renders the headline and all three article cards', () => {
    const wrapper = mount(Insights)
    const text = wrapper.text()
    expect(text).toContain('INSIGHTS')
    expect(text).toContain('Practical insight for better')
    expect(text).toContain('business decisions.')
    expect(text).toContain('What your financial reports are really telling you')
    expect(text).toContain('Why monthly financial reporting matters')
    expect(text).toContain('A practical way to improve cash-flow visibility')
    expect(text).toContain('Financial Management')
    expect(text).toContain('Accounting')
    expect(text).toContain('Finance')
    const viewDetailsCount = wrapper.findAll('[data-testid="view-details"]').length
    expect(viewDetailsCount).toBe(3)
  })
})
```

- [ ] **Step 2: Run test to verify it fails**

Run: `cd storefront && npx vitest run tests/home/Insights.test.ts`
Expected: FAIL.

- [ ] **Step 3: Implement `Insights.vue`**

```vue
<template>
  <section class="bg-neutral-50 px-4 py-16 md:px-8 md:py-24">
    <div class="mx-auto max-w-3xl text-center">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">Insights</p>
      <h2 class="mt-4 text-3xl font-semibold text-secondary-700 md:text-4xl">
        Practical insight for better
        <span class="block italic">business decisions.</span>
      </h2>
      <p class="mt-4 text-neutral-500">
        Clear perspectives on tax, accounting, and financial management, made for businesses
        that want to move forward with confidence.
      </p>
    </div>

    <div class="mx-auto mt-12 grid max-w-7xl gap-6 md:grid-cols-3">
      <article v-for="post in posts" :key="post.title" class="rounded-lg bg-white p-6 shadow-sm">
        <img :src="post.image" :alt="post.title" class="h-48 w-full rounded-lg object-cover" />
        <p class="mt-4 text-sm font-medium text-secondary-700">{{ post.category }}</p>
        <h3 class="mt-1 font-semibold text-secondary-700">{{ post.title }}</h3>
        <p class="mt-2 text-sm text-neutral-500">{{ post.excerpt }}</p>
        <div class="mt-4 flex items-center justify-between">
          <span class="text-sm text-neutral-400">{{ post.date }}</span>
          <UButton data-testid="view-details" color="secondary" variant="solid" size="sm">
            View Details
          </UButton>
        </div>
      </article>
    </div>
  </section>
</template>

<script setup lang="ts">
const excerpt =
  "Clear perspectives on tax, accounting, and financial management, made for businesses that want to move forward with confidence.";

const posts = [
  {
    image: "/images/icon/Financial_Icon.svg",
    category: "Financial Management",
    title: "What your financial reports are really telling you",
    excerpt,
    date: "Aug 31,2026",
  },
  {
    image: "/images/icon/Analysis_Icon.svg",
    category: "Accounting",
    title: "Why monthly financial reporting matters",
    excerpt,
    date: "Aug 31,2026",
  },
  {
    image: "/images/icon/Compliance_Icon.svg",
    category: "Finance",
    title: "A practical way to improve cash-flow visibility",
    excerpt,
    date: "Aug 31,2026",
  },
];
</script>
```

Placeholder images reuse existing icon files (`Financial_Icon.svg`, `Analysis_Icon.svg`, `Compliance_Icon.svg`) purely as visible stand-ins so the card isn't blank — replace with real article photography once it exists (spec's "Known follow-ups").

- [ ] **Step 4: Run test to verify it passes**

Run: `cd storefront && npx vitest run tests/home/Insights.test.ts`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/components/home/Insights.vue tests/home/Insights.test.ts
git commit -m "feat: add homepage Insights section (placeholder thumbnails, real titles/copy)"
```

---

## Task 13: FAQ section (Q1 only)

Per the brainstorming decision: ship only the one FAQ item that has both a real question and a real answer in Figma (node `32:7136`, item `58:958`/`57:946`). The other four items are either missing an answer entirely or contain mismatched content from an unrelated template — not included this phase (spec + plan both flag this; do not invent answers for them).

**Files:**
- Create: `app/components/home/Faq.vue`
- Test: `tests/home/Faq.test.ts`

**Interfaces:**
- Produces: no props. Consumed as `<HomeFaq />`.

- [ ] **Step 1: Write the failing test**

```ts
// tests/home/Faq.test.ts
import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Faq from '../../app/components/home/Faq.vue'

describe('Home Faq', () => {
  it('renders the headline and the one confirmed question', () => {
    const wrapper = mount(Faq)
    const text = wrapper.text()
    expect(text).toContain('FREQUENTLY ASKED QUESTIONS')
    expect(text).toContain('Clear answers for your next step.')
    expect(text).toContain('What services does PCG provide?')
  })

  it('expands the answer when the question is clicked', async () => {
    const wrapper = mount(Faq)
    expect(wrapper.text()).not.toContain('PCG provides professional consulting')
    await wrapper.find('[data-testid="faq-question"]').trigger('click')
    expect(wrapper.text()).toContain('PCG provides professional consulting and advisory support across taxation, accounting and bookkeeping, and other customized financial services.')
  })
})
```

- [ ] **Step 2: Run test to verify it fails**

Run: `cd storefront && npx vitest run tests/home/Faq.test.ts`
Expected: FAIL.

- [ ] **Step 3: Implement `Faq.vue`**

```vue
<template>
  <section class="px-4 py-16 md:px-8 md:py-24">
    <div class="mx-auto max-w-3xl text-center">
      <p class="text-sm font-semibold uppercase tracking-wide text-neutral-400">Frequently Asked Questions</p>
      <h2 class="mt-4 text-3xl font-semibold italic text-secondary-700 md:text-4xl">
        Clear answers for your next step.
      </h2>
      <p class="mt-4 text-neutral-500">
        Before starting a conversation, here are answers to the questions businesses commonly
        ask about PCG's services, process, and engagement.
      </p>
    </div>

    <div class="mx-auto mt-10 max-w-4xl rounded-lg bg-secondary-50">
      <button
        type="button"
        data-testid="faq-question"
        class="flex w-full items-center justify-between p-6 text-left font-semibold text-secondary-700"
        :aria-expanded="open"
        @click="open = !open"
      >
        What services does PCG provide?
        <Icon :name="open ? 'lucide:minus' : 'lucide:plus'" class="size-5 shrink-0" />
      </button>
      <p v-if="open" class="px-6 pb-6 text-neutral-500">
        PCG provides professional consulting and advisory support across taxation, accounting
        and bookkeeping, and other customized financial services. The available scope depends
        on your business needs and will be discussed before an engagement begins.
      </p>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from "vue";

const open = ref(false);
</script>
```

- [ ] **Step 4: Run test to verify it passes**

Run: `cd storefront && npx vitest run tests/home/Faq.test.ts`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/components/home/Faq.vue tests/home/Faq.test.ts
git commit -m "feat: add homepage FAQ section (single confirmed item, per spec)"
```

---

## Task 14: CTA section

**Files:**
- Create: `app/components/home/Cta.vue`
- Test: `tests/home/Cta.test.ts`

**Interfaces:**
- Produces: no props. Consumed as `<HomeCta />`.

- [ ] **Step 1: Write the failing test**

```ts
// tests/home/Cta.test.ts
import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Cta from '../../app/components/home/Cta.vue'

describe('Home Cta', () => {
  it('renders the headline, body, and button', () => {
    const wrapper = mount(Cta)
    const text = wrapper.text()
    expect(text).toContain('START A CONVERSATION')
    expect(text).toContain('Ready to bring more clarity to your business?')
    expect(text).toContain("Let's take the next step together.")
    expect(text).toContain('Tell us about your tax, accounting, or financial needs. PCG will help you identify a practical starting point for your business.')
    expect(text).toContain('Discuss Your Needs')
  })
})
```

- [ ] **Step 2: Run test to verify it fails**

Run: `cd storefront && npx vitest run tests/home/Cta.test.ts`
Expected: FAIL.

- [ ] **Step 3: Implement `Cta.vue`**

```vue
<template>
  <section
    class="bg-secondary-700 bg-cover bg-center px-4 py-20 text-center text-white md:px-8"
    style="background-image: url('/images/homepage/CTA_Desktop.png')"
  >
    <div class="mx-auto max-w-3xl">
      <p class="text-sm font-semibold uppercase tracking-wide text-primary-500">Start a Conversation</p>
      <h2 class="mt-4 text-3xl font-semibold md:text-5xl">
        Ready to bring more clarity to your business?
        <span class="block italic">Let's take the next step together.</span>
      </h2>
      <p class="mt-6 text-neutral-200">
        Tell us about your tax, accounting, or financial needs. PCG will help you identify a
        practical starting point for your business.
      </p>
      <UButton color="neutral" variant="solid" size="lg" class="mt-8 bg-white text-secondary-700">
        Discuss Your Needs
      </UButton>
    </div>
  </section>
</template>

<script setup lang="ts"></script>
```

- [ ] **Step 4: Run test to verify it passes**

Run: `cd storefront && npx vitest run tests/home/Cta.test.ts`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/components/home/Cta.vue tests/home/Cta.test.ts
git commit -m "feat: add homepage CTA section"
```

---

## Task 15: Assemble the homepage and add a route smoke test

**Files:**
- Modify: `app/pages/index.vue`
- Test: `tests/home/index.test.ts`

**Interfaces:**
- Consumes: `HomeHero`, `HomeAboutPcg`, `HomeStats`, `HomeHowWeHelp`, `HomeWhyChooseUs`, `HomeOurApproach`, `HomeInsights`, `HomeFaq`, `HomeCta` (all zero-prop components from Tasks 6–14, auto-registered by Nuxt from `app/components/home/`).

- [ ] **Step 1: Write the failing test**

```ts
// tests/home/index.test.ts
import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Hero from '../../app/components/home/Hero.vue'
import AboutPcg from '../../app/components/home/AboutPcg.vue'
import Stats from '../../app/components/home/Stats.vue'
import HowWeHelp from '../../app/components/home/HowWeHelp.vue'
import WhyChooseUs from '../../app/components/home/WhyChooseUs.vue'
import OurApproach from '../../app/components/home/OurApproach.vue'
import Insights from '../../app/components/home/Insights.vue'
import Faq from '../../app/components/home/Faq.vue'
import Cta from '../../app/components/home/Cta.vue'
import IndexPage from '../../app/pages/index.vue'
import { readFileSync } from 'node:fs'
import { resolve } from 'node:path'

describe('Homepage composition', () => {
  it('references every section component, in spec order, in app/pages/index.vue', () => {
    const source = readFileSync(resolve(__dirname, '../../app/pages/index.vue'), 'utf-8')
    const expectedOrder = [
      'HomeHero',
      'HomeAboutPcg',
      'HomeStats',
      'HomeHowWeHelp',
      'HomeWhyChooseUs',
      'HomeOurApproach',
      'HomeInsights',
      'HomeFaq',
      'HomeCta',
    ]
    const positions = expectedOrder.map((tag) => source.indexOf(`<${tag}`))
    expect(positions.every((pos) => pos !== -1)).toBe(true)
    expect(positions).toEqual([...positions].sort((a, b) => a - b))
  })

  it('every referenced section component mounts without throwing', () => {
    for (const Component of [Hero, AboutPcg, Stats, HowWeHelp, WhyChooseUs, OurApproach, Insights, Faq, Cta]) {
      expect(() => mount(Component)).not.toThrow()
    }
  })
})
```

- [ ] **Step 2: Run test to verify it fails**

Run: `cd storefront && npx vitest run tests/home/index.test.ts`
Expected: FAIL (`index.vue` is still the empty placeholder from Task 1).

- [ ] **Step 3: Implement `index.vue`**

```vue
<template>
  <div>
    <HomeHero />
    <HomeAboutPcg />
    <HomeStats />
    <HomeHowWeHelp />
    <HomeWhyChooseUs />
    <HomeOurApproach />
    <HomeInsights />
    <HomeFaq />
    <HomeCta />
  </div>
</template>

<script setup lang="ts"></script>
```

- [ ] **Step 4: Run test to verify it passes**

Run: `cd storefront && npx vitest run tests/home/index.test.ts`
Expected: PASS.

- [ ] **Step 5: Run the full suite and typecheck**

Run: `cd storefront && npm run test && npm run typecheck`
Expected: both PASS.

- [ ] **Step 6: Manual visual pass**

Run: `cd storefront && npm run dev`, open `http://localhost:3000/`, compare section-by-section against Figma frame `10:28973` (desktop) and `74:8038` (mobile, resize browser to ~375px width) per the spec's "Testing" section. Note any visual drift for a follow-up pass — this task's automated tests only check text content and structure, not pixel fidelity.

- [ ] **Step 7: Commit**

```bash
git add app/pages/index.vue tests/home/index.test.ts
git commit -m "feat: assemble PCG homepage from all sections"
```

---

## Open items for the user (not blocking, carried from spec + this plan)

- PCG logo file: not exported (Figma MCP quota hit mid-session). `AppHeader.vue`/`AppFooter.vue` render "PCG" as text until the real logo is exported and dropped at e.g. `public/images/pcg-logo.svg`.
- Insight article thumbnails: using icon files as placeholders (Task 12) until real photography exists.
- FAQ items 2–5: not built — Figma content for them is missing or mismatched (spec + Task 13).
- Hero headline's literal third line "DECISIONS." repeating the second line's last word — worth confirming with whoever owns the Figma file that this isn't a copy-paste error before treating it as final copy.
- `runtimeConfig.public.siteUrl` still defaults to `bbystargo.com` — a deployment/env config change, out of scope here.
