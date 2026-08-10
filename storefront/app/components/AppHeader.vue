<script setup lang="ts">
import { useCartStore } from "~/store/cart";
import { useWishlistStore } from "~/store/wishlist";
import { useAuthStore } from "~/store/auth";
import { useApi } from "~/services/api";

const api = useApi();
const { data: settingsResponse } = useAsyncData("store-settings", () =>
  api.getSettings(),
);

const settings = computed(() => {
  return (
    settingsResponse.value?.data || {
      store_name: "Doctor Plant Indonesia",
      logo: null,
    }
  );
});

const router = useRouter();
const route = useRoute();
const cart = useCartStore();
const wishlist = useWishlistStore();
const authStore = useAuthStore();
const toast = useToast();

const searchQuery = ref(typeof route.query.q === "string" ? route.query.q : "");
const mobileNavOpen = ref(false);

const navLinks = [
  { label: "Home", to: "/" },
  { label: "Katalog", to: "/katalog" },
  { label: "Tentang Kami", to: "/about" },
  { label: "Keranjang", to: "/cart" },
];

const runSearch = useDebounceFn((value: string) => {
  router.push({ path: "/katalog", query: { q: value || undefined } });
}, 400);

watch(searchQuery, (value) => runSearch(value));

const userDropdownItems = computed(() => [
  [
    {
      label: authStore.user?.email || "Member",
      icon: "i-heroicons-envelope",
      disabled: true,
    },
    {
      label: "Profil Saya",
      icon: "i-heroicons-user-circle",
      to: "/akun/profil",
      class: "cursor-pointer",
    },
    {
      label: "Alamat Saya",
      icon: "i-heroicons-map-pin",
      to: "/akun/alamat",
      class: "cursor-pointer",
    },
    {
      label: "Pesanan Saya",
      icon: "i-heroicons-shopping-bag",
      to: "/orders/history",
      class: "cursor-pointer",
    },
  ],
  [
    {
      label: "Keluar (Logout)",
      icon: "i-heroicons-arrow-right-on-rectangle",
      color: "error" as const,
      class: "cursor-pointer",
      onSelect: async () => {
        await authStore.logout();
        toast.add({
          title: "Logout Berhasil",
          description: "Anda telah keluar dari akun member.",
          color: "primary",
        });
        router.push("/");
      },
    },
  ],
]);

async function handleMobileLogout() {
  await authStore.logout();
  toast.add({
    title: "Logout Berhasil",
    description: "Anda telah keluar dari akun member.",
    color: "primary",
  });
  mobileNavOpen.value = false;
  router.push("/");
}
</script>

<template>
  <header
    class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-100/80 shadow-[0_2px_12px_-4px_rgba(0,0,0,0.04)] transition-all duration-300"
  >
    <!-- Main Top Row -->
    <div
      class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4"
    >
      <!-- Left: Hamburger (Mobile Only) & Brand Logo -->
      <div class="flex items-center gap-2">
        <UButton
          icon="i-heroicons-bars-3"
          color="neutral"
          variant="ghost"
          class="lg:hidden hover:bg-gray-100/80 rounded-xl p-2 shrink-0"
          aria-label="Buka Menu"
          @click="() => { mobileNavOpen = true }"
        />
        <NuxtLink to="/" class="flex items-center gap-2.5 group">
          <div
            class="w-auto max-w-30 h-10 shrink-0 flex items-center justify-center"
          >
            <NuxtImg
              :src="settings.logo || '/logo_dr_plant.jpg'"
              alt="Logo Dr. Plant"
              width="40"
              height="40"
              class="w-full h-full object-contain"
            />
          </div>
          <!-- <div class="hidden md:block">
            <div class="flex flex-col shrink-0">
              <span
                class="font-extrabold text-gray-900 tracking-tight leading-tight text-base sm:text-lg group-hover:text-primary-700 transition-colors"
                >Doctor Plant</span
              >
              <span
                class="text-[9px] text-primary-400 uppercase tracking-widest font-extrabold -mt-0.5"
                >Indonesia</span
              >
            </div>
          </div> -->
        </NuxtLink>
      </div>

      <!-- Center: Wide Search Input (Desktop Only - lg:flex) -->
      <div
        class="hidden lg:flex items-center justify-center grow max-w-md mx-6"
      >
        <div class="relative w-full">
          <UInput
            v-model="searchQuery"
            icon="i-heroicons-magnifying-glass"
            placeholder="Cari produk yang Anda butuhkan..."
            size="md"
            class="w-full transition-all duration-300 border border-gray-200 rounded-full"
            :ui="{
              base: 'rounded-full focus-visible:ring-1 focus-visible:ring-primary-200 focus-visible:outline-none',
            }"
          />
        </div>
      </div>

      <!-- Right: Desktop Navigation Menu & Action Icons -->
      <div class="flex items-center gap-6">
        <!-- Navigation links (Desktop Only - lg:flex) -->
        <nav class="hidden lg:flex items-center gap-6">
          <NuxtLink
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            class="relative py-2 text-sm font-semibold transition-colors duration-300 group"
            :class="
              route.path === link.to
                ? 'text-primary-700 font-bold'
                : 'text-gray-600 hover:text-primary-600'
            "
          >
            {{ link.label }}
            <span
              class="absolute bottom-0 left-0 h-[2px] bg-primary-600 transition-all duration-300 rounded-full"
              :class="
                route.path === link.to ? 'w-full' : 'w-0 group-hover:w-full'
              "
            />
          </NuxtLink>
        </nav>

        <!-- Action Icons (Always visible on all screens) -->
        <div class="flex items-center gap-2 shrink-0">
          <NuxtLink to="/wishlist" class="relative group">
            <UButton
              icon="i-heroicons-heart"
              color="neutral"
              variant="ghost"
              class="text-gray-600 cursor-pointer hover:text-red-500 hover:bg-red-50/60 transition-all duration-300 rounded-xl p-2.5"
              aria-label="Wishlist"
            />
            <UBadge
              v-if="wishlist.count > 0"
              color="error"
              size="sm"
              class="absolute -top-1 -right-1 min-w-[18px] h-[18px] flex items-center justify-center px-1 rounded-full text-[10px] font-extrabold ring-2 ring-white animate-pulse"
            >
              {{ wishlist.count }}
            </UBadge>
          </NuxtLink>

          <NuxtLink to="/cart" class="relative group">
            <UButton
              icon="i-heroicons-shopping-cart"
              color="neutral"
              variant="ghost"
              class="text-gray-600 hover:text-primary-600 cursor-pointer hover:bg-primary-50/60 transition-all duration-300 rounded-xl p-2.5"
              aria-label="Keranjang Belanja"
            />
            <UBadge
              v-if="cart.totalQty > 0"
              color="primary"
              size="sm"
              class="absolute -top-1 -right-1 min-w-[18px] h-[18px] flex items-center justify-center px-1 rounded-full text-[10px] font-extrabold ring-2 ring-white animate-bounce"
            >
              {{ cart.totalQty }}
            </UBadge>
          </NuxtLink>

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
            <UDropdownMenu
              v-if="authStore.isLoggedIn"
              :items="userDropdownItems"
              :ui="{ content: 'w-48' }"
            >
              <UButton
                icon="i-heroicons-user"
                color="neutral"
                variant="ghost"
                class="text-gray-600 hover:text-primary-600 hover:bg-primary-50/60 transition-all duration-300 rounded-xl p-2.5 flex items-center gap-1 cursor-pointer"
              >
                <span
                  class="hidden md:inline text-xs font-semibold text-gray-700 truncate max-w-[100px]"
                >
                  {{ authStore.user?.name.split(" ")[0] }}
                </span>
              </UButton>
            </UDropdownMenu>
            <NuxtLink v-else to="/login">
              <UButton
                color="primary"
                variant="solid"
                icon="i-heroicons-user"
                class="font-bold text-xs rounded-full px-4 py-2 cursor-pointer shadow-md shadow-primary-600/10 hover:scale-102 transition-all duration-300"
              >
                Masuk
              </UButton>
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Search Row (Only visible on mobile - lg:hidden) -->
    <div
      class="lg:hidden px-4 pb-3 pt-1.5 border-t border-gray-100/50 bg-white/95"
    >
      <UInput
        v-model="searchQuery"
        icon="i-heroicons-magnifying-glass"
        placeholder="Cari produk yang Anda butuhkan..."
        size="lg"
        class="w-full transition-all duration-300 border border-gray-200 rounded-full"
        :ui="{
          base: 'rounded-full focus-visible:ring-1 focus-visible:ring-primary-200 focus-visible:outline-none',
        }"
      />
    </div>

    <!-- Mobile Slideover Navigation (Burger drawer) -->
    <USlideover v-model:open="mobileNavOpen" side="left">
      <template #content>
        <div
          class="p-6 space-y-6 h-full flex flex-col justify-between bg-white"
        >
          <div class="space-y-6">
            <div
              class="flex items-center justify-between pb-4 border-b border-gray-100"
            >
              <NuxtLink
                to="/"
                class="flex items-center gap-2.5"
                @click="mobileNavOpen = false"
              >
                <div
                  class="w-auto max-w-30 h-10 shrink-0 flex items-center justify-center"
                >
                  <NuxtImg
                    :src="settings.logo || '/logo_dr_plant.jpg'"
                    alt="Logo Dr. Plant"
                    width="40"
                    height="40"
                    class="w-full h-full object-contain"
                  />
                </div>
                <!-- <div class="flex flex-col">
                  <span class="font-bold text-gray-900 leading-tight"
                    >Doctor Plant</span
                  >
                  <span
                    class="text-[8px] text-primary-600 uppercase tracking-widest font-extrabold -mt-0.5"
                    >Indonesia</span
                  >
                </div> -->
              </NuxtLink>
              <UButton
                icon="i-heroicons-x-mark"
                color="neutral"
                variant="ghost"
                aria-label="Tutup menu"
                class="hover:bg-gray-100 rounded-lg"
                @click="() => { mobileNavOpen = false }"
              />
            </div>

            <nav class="space-y-2">
              <NuxtLink
                v-for="link in navLinks"
                :key="link.to"
                :to="link.to"
                class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-300"
                :class="
                  route.path === link.to
                    ? 'bg-primary-50 text-primary-700'
                    : 'text-gray-700 hover:bg-gray-50 hover:text-gray-950'
                "
                @click="mobileNavOpen = false"
              >
                <span>{{ link.label }}</span>
                <UIcon
                  v-if="route.path === link.to"
                  name="i-heroicons-chevron-right"
                  class="w-4 h-4 text-primary-600"
                />
              </NuxtLink>
            </nav>

            <!-- Mobile User Section -->
            <div class="pt-4 border-t border-gray-100">
              <div v-if="authStore.isLoggedIn" class="space-y-3">
                <div class="flex items-center gap-3 px-4 py-2">
                  <div
                    class="w-8 h-8 rounded-full bg-linear-to-tr from-primary-600 to-primary-400 flex items-center justify-center text-white shadow-md shadow-primary-900/5"
                  >
                    <UIcon name="i-heroicons-user" class="w-4 h-4" />
                  </div>
                  <div class="flex flex-col">
                    <span class="text-sm font-bold text-gray-900 leading-tight">
                      {{ authStore.user?.name }}
                    </span>
                    <span class="text-xs text-gray-400">
                      {{ authStore.user?.email }}
                    </span>
                  </div>
                </div>
                <NuxtLink
                  to="/akun/profil"
                  class="flex items-center justify-between text-gray-700 hover:bg-gray-50 hover:text-gray-950 font-semibold w-full text-left px-4 py-3 rounded-xl transition-all duration-300 cursor-pointer"
                  @click="mobileNavOpen = false"
                >
                  <span class="flex items-center gap-2">
                    <UIcon
                      name="i-heroicons-user-circle"
                      class="w-4 h-4 text-primary-600"
                    />
                    Profil Saya
                  </span>
                </NuxtLink>
                <NuxtLink
                  to="/akun/alamat"
                  class="flex items-center justify-between text-gray-700 hover:bg-gray-50 hover:text-gray-950 font-semibold w-full text-left px-4 py-3 rounded-xl transition-all duration-300 cursor-pointer"
                  @click="mobileNavOpen = false"
                >
                  <span class="flex items-center gap-2">
                    <UIcon
                      name="i-heroicons-map-pin"
                      class="w-4 h-4 text-primary-600"
                    />
                    Alamat Saya
                  </span>
                </NuxtLink>
                <NuxtLink
                  to="/orders/history"
                  class="flex items-center justify-between text-gray-700 hover:bg-gray-50 hover:text-gray-950 font-semibold w-full text-left px-4 py-3 rounded-xl transition-all duration-300 cursor-pointer"
                  @click="mobileNavOpen = false"
                >
                  <span class="flex items-center gap-2">
                    <UIcon
                      name="i-heroicons-shopping-bag"
                      class="w-4 h-4 text-primary-600"
                    />
                    Pesanan Saya
                  </span>
                </NuxtLink>
                <button
                  type="button"
                  class="flex items-center justify-between text-red-600 hover:bg-red-50 hover:text-red-700 font-semibold w-full text-left px-4 py-3 rounded-xl transition-all duration-300 cursor-pointer"
                  @click="handleMobileLogout"
                >
                  <span class="flex items-center gap-2">
                    <UIcon
                      name="i-heroicons-arrow-right-on-rectangle"
                      class="w-4 h-4"
                    />
                    Keluar (Logout)
                  </span>
                </button>
              </div>
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
            </div>
          </div>

          <div class="pt-6 border-t border-gray-100">
            <p class="text-xs text-gray-400 text-center font-medium">
              &copy; {{ new Date().getFullYear() }} PT Doctor Plant Indonesia
            </p>
          </div>
        </div>
      </template>
    </USlideover>
  </header>
</template>
