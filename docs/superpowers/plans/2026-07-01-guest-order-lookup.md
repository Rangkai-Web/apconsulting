# Guest Order Lookup Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Allow guests to look up their order status by entering order number + email, and fix the checkout redirect bug that prevents guests from seeing their order detail after payment.

**Architecture:** Three changes — (1) new `/orders/cek` form page that navigates to the existing order detail page with `?email=` query param, (2) an `orderUrl()` helper in checkout that appends `?email=` for guest redirects, (3) "Cek Pesanan" link in AppHeader visible to non-authenticated users. The existing `[orderNumber].vue` + `orderService.lookupOrder()` + `GET /api/orders/lookup` backend already handle the actual lookup — this plan wires up the entry points.

**Tech Stack:** Nuxt 4.4.x SPA (`ssr: false`), Nuxt UI v4, TypeScript, `useAuthStore()` (Pinia).

## Global Constraints

- No `middleware: 'auth'` on `/orders/cek` — this page is exclusively for guests.
- No new API endpoints — all backend is already in place.
- No Vitest tests for page components — project convention (only `health.test.ts` stub exists).
- UI components: `UButton`, `UInput`, `UFormField` from Nuxt UI v4.
- Follow existing page patterns: `useToast()`, `useRouter()`, same gradient background as other pages.
- `AppHeader.vue` uses `authStore.isLoggedIn` (not `authStore.user`) for conditional rendering.
- Mobile nav guest section is at `AppHeader.vue:415` — `<div v-else class="grid grid-cols-2 gap-2 px-2">`.
- Desktop login button is at `AppHeader.vue:246` — `<NuxtLink v-else to="/login">`.
- All 5 `router.push` calls in checkout that redirect to order detail are at lines 414, 433, 442, 450, 459.

---

### Task 1: Guest Lookup Form Page + Checkout Redirect Fix

**Files:**
- Create: `storefront/app/pages/orders/cek.vue`
- Modify: `storefront/app/pages/checkout/index.vue` (add `orderUrl()` helper, replace 5 `router.push` calls)

**Interfaces:**
- Consumes: `useRouter()` (Nuxt built-in), `useToast()` (Nuxt UI), no service calls
- Produces: Navigation to `/orders/${orderNumber}?email=${email}` which the existing `[orderNumber].vue` handles

- [x] **Step 1: Create `/orders/cek.vue`**

Create `storefront/app/pages/orders/cek.vue` with this complete content:

```vue
<script setup lang="ts">
const router = useRouter();

const form = reactive({
  orderNumber: "",
  email: "",
});

const loading = ref(false);

const formValid = computed(
  () => form.orderNumber.trim().length > 0 && form.email.trim().length > 0,
);

async function handleLookup() {
  if (!formValid.value) return;
  loading.value = true;
  router.push(
    `/orders/${form.orderNumber.trim()}?email=${encodeURIComponent(form.email.trim())}`,
  );
}
</script>

<template>
  <div
    class="relative overflow-hidden bg-linear-to-b from-primary-50/15 via-white to-white min-h-screen py-16"
  >
    <div
      class="absolute top-12 -left-40 w-96 h-96 bg-primary-100/10 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-[400px] -right-40 w-96 h-96 bg-secondary-100/10 rounded-full blur-3xl pointer-events-none"
    ></div>

    <div class="max-w-md mx-auto px-4 relative">
      <div class="text-center mb-10">
        <div
          class="w-14 h-14 rounded-2xl bg-primary-50 flex items-center justify-center mx-auto mb-4 shadow-sm"
        >
          <UIcon
            name="i-heroicons-magnifying-glass"
            class="w-7 h-7 text-primary-600"
          />
        </div>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight">
          Cek Status Pesanan
        </h1>
        <p class="text-sm text-gray-500 font-medium mt-2">
          Masukkan nomor pesanan dan email yang Anda gunakan saat checkout.
        </p>
      </div>

      <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-xs">
        <form class="space-y-5" @submit.prevent="handleLookup">
          <UFormField label="Nomor Pesanan" required>
            <UInput
              v-model="form.orderNumber"
              placeholder="Contoh: ORD-20260701-XXXX"
              size="lg"
              class="w-full"
              :ui="{ base: 'rounded-xl' }"
            />
          </UFormField>

          <UFormField label="Email" required>
            <UInput
              v-model="form.email"
              type="email"
              placeholder="email@contoh.com"
              size="lg"
              class="w-full"
              :ui="{ base: 'rounded-xl' }"
            />
          </UFormField>

          <UButton
            type="submit"
            color="primary"
            size="lg"
            class="w-full font-extrabold rounded-full cursor-pointer shadow-md shadow-primary-600/15 mt-2"
            :disabled="!formValid"
            :loading="loading"
          >
            Cek Pesanan
          </UButton>
        </form>
      </div>

      <p class="text-center text-xs text-gray-400 font-medium mt-6">
        Punya akun?
        <NuxtLink to="/login" class="text-primary-600 font-bold hover:underline"
          >Masuk</NuxtLink
        >
        untuk melihat semua riwayat pesanan.
      </p>
    </div>
  </div>
</template>
```

- [x] **Step 2: Add `orderUrl()` helper to checkout and fix 5 redirect calls**

Open `storefront/app/pages/checkout/index.vue`. Add the helper function after the existing `loadMidtransSnapScript` function (around line 70, before `handleRetryPayment`):

```typescript
function orderUrl(orderNumber: string, extra: Record<string, string> = {}): string {
  const params = new URLSearchParams(extra);
  if (!isLoggedIn.value && guestForm.email) {
    params.set("email", guestForm.email);
  }
  const qs = params.toString();
  return `/orders/${orderNumber}${qs ? `?${qs}` : ""}`;
}
```

Then replace all 5 `router.push` calls that redirect to the order detail page:

**Line ~414** (mock mode):
```typescript
// Before:
router.push(`/orders/${orderNumber}?mock=true`);
// After:
router.push(orderUrl(orderNumber, { mock: "true" }));
```

**Line ~433** (onSuccess):
```typescript
// Before:
router.push(`/orders/${orderNumber}?status=success`);
// After:
router.push(orderUrl(orderNumber, { status: "success" }));
```

**Line ~442** (onPending):
```typescript
// Before:
router.push(`/orders/${orderNumber}?status=pending`);
// After:
router.push(orderUrl(orderNumber, { status: "pending" }));
```

**Line ~450** (onError):
```typescript
// Before:
router.push(`/orders/${orderNumber}?status=error`);
// After:
router.push(orderUrl(orderNumber, { status: "error" }));
```

**Line ~459** (onClose):
```typescript
// Before:
router.push(`/orders/${orderNumber}`);
// After:
router.push(orderUrl(orderNumber));
```

- [x] **Step 3: Commit Task 1**

```bash
git add storefront/app/pages/orders/cek.vue storefront/app/pages/checkout/index.vue
git commit -m "feat(storefront): add guest order lookup page and fix checkout redirect for guests"
```

---

### Task 2: AppHeader — "Cek Pesanan" Link

**Files:**
- Modify: `storefront/app/components/AppHeader.vue`

**Interfaces:**
- Consumes: `authStore.isLoggedIn` (already imported in AppHeader), `mobileNavOpen` ref (already exists)
- Produces: nothing consumed by other tasks

- [x] **Step 1: Add desktop "Cek Pesanan" link**

In `storefront/app/components/AppHeader.vue`, find the desktop auth section (around line 226). The current structure is:

```html
<!-- User Profile Dropdown or Login Button -->
<div class="ml-1 shrink-0 flex items-center">
  <UDropdownMenu v-if="authStore.isLoggedIn" ...>
    ...
  </UDropdownMenu>
  <NuxtLink v-else to="/login">
    <UButton ...>Masuk</UButton>
  </NuxtLink>
</div>
```

Add a "Cek Pesanan" link BEFORE that `<div class="ml-1 shrink-0 flex items-center">`:

```html
<!-- Cek Pesanan link — visible to guests only, desktop -->
<NuxtLink
  v-if="!authStore.isLoggedIn"
  to="/orders/cek"
  class="hidden lg:flex items-center text-xs font-bold text-gray-600 hover:text-primary-600 transition-colors mr-1"
>
  Cek Pesanan
</NuxtLink>

<!-- User Profile Dropdown or Login Button -->
<div class="ml-1 shrink-0 flex items-center">
```

- [x] **Step 2: Add mobile nav "Cek Pesanan" link**

In the same file, find the mobile guest section (around line 415). Current structure:

```html
<div v-else class="grid grid-cols-2 gap-2 px-2">
  <NuxtLink to="/login" class="flex items-center justify-center font-bold text-xs rounded-full px-4 py-3 border border-primary-600 text-primary-700 bg-white hover:bg-primary-50/50 text-center transition-all duration-200 cursor-pointer" @click="mobileNavOpen = false">
    Masuk
  </NuxtLink>
  <NuxtLink to="/register" class="flex items-center justify-center font-bold text-xs rounded-full px-4 py-3 bg-primary-600 text-white hover:bg-primary-700 text-center transition-all duration-200 cursor-pointer" @click="mobileNavOpen = false">
    Daftar
  </NuxtLink>
</div>
```

Replace with:

```html
<div v-else class="space-y-2 px-2">
  <NuxtLink
    to="/orders/cek"
    class="flex items-center justify-center gap-1.5 font-bold text-xs rounded-full px-4 py-3 border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 text-center transition-all duration-200 cursor-pointer w-full"
    @click="mobileNavOpen = false"
  >
    <UIcon name="i-heroicons-magnifying-glass" class="w-3.5 h-3.5" />
    Cek Pesanan
  </NuxtLink>
  <div class="grid grid-cols-2 gap-2">
    <NuxtLink
      to="/login"
      class="flex items-center justify-center font-bold text-xs rounded-full px-4 py-3 border border-primary-600 text-primary-700 bg-white hover:bg-primary-50/50 text-center transition-all duration-200 cursor-pointer"
      @click="mobileNavOpen = false"
    >
      Masuk
    </NuxtLink>
    <NuxtLink
      to="/register"
      class="flex items-center justify-center font-bold text-xs rounded-full px-4 py-3 bg-primary-600 text-white hover:bg-primary-700 text-center transition-all duration-200 cursor-pointer"
      @click="mobileNavOpen = false"
    >
      Daftar
    </NuxtLink>
  </div>
</div>
```

- [x] **Step 3: Commit Task 2**

```bash
git add storefront/app/components/AppHeader.vue
git commit -m "feat(storefront): add Cek Pesanan link in header for guest users"
```

---

## Verification Plan

Manual only (no Vitest for pages):

1. **Guest checkout redirect fix**: Buka checkout sebagai guest → isi form → bayar (gunakan VA BCA sandbox) → setelah Snap popup close, URL harus `/orders/ORD-XXX?email=guest@email.com`, bukan `/orders/ORD-XXX` saja → order detail terbuka tanpa error.

2. **Cek Pesanan page**: Buka `/orders/cek` tanpa login → form muncul → isi order number + email yang valid → klik "Cek Pesanan" → redirect ke `/orders/${orderNumber}?email=${email}` → detail pesanan muncul.

3. **Header link — desktop**: Logout → link "Cek Pesanan" muncul di kanan header sebelum tombol "Masuk" → login → link hilang.

4. **Header link — mobile**: Logout → buka mobile nav (burger) → "Cek Pesanan" muncul di atas grid Masuk/Daftar → login → hilang.

5. **Member tidak terpengaruh**: Login sebagai member → lakukan checkout → redirect ke `/orders/${orderNumber}` tanpa `?email=` parameter (helper hanya append email untuk guest).
