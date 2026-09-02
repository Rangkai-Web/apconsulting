<script setup lang="ts">
import { useCartStore } from "~/store/cart";
import { useWishlistStore } from "~/store/wishlist";
import { useAuthStore } from "~/store/auth";
import { useApi } from "~/services/api";

const api = useApi();
const { data: settingsResponse, status: settingsStatus } = useAsyncData<any>(
  "store-settings",
  () => api.getSettings(),
);

const logoSrc = ref<string>("");

watchEffect(() => {
  if (settingsResponse.value?.data?.logo) {
    logoSrc.value = settingsResponse.value.data.logo;
  } else {
    logoSrc.value = "/BbystarGO__1.png";
  }
});

function handleLogoError(e: Event) {
  const target = e.target as HTMLImageElement;
  if (target) {
    target.src = "/BbystarGO__1.png";
  }
}

const settings = computed(() => {
  return (
    settingsResponse.value?.data || {
      store_name: "BbystarGO",
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

const mobileNavOpen = ref(false);

const navLinks = [
  { label: "Home", to: "/" },
  { label: "Katalog", to: "/katalog" },
  { label: "Tentang Kami", to: "/tentang-kami" },
  { label: "FAQ", to: "/faq" },
];

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
    class="sticky top-0 z-45 bg-primary-500/90 backdrop-blur-md border-b border-primary-400/30 shadow-[0_4px_20px_-6px_rgba(255,116,108,0.12)] transition-all duration-300"
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
          class="lg:hidden hover:!bg-white/10 rounded-xl p-2 shrink-0 !text-white"
          aria-label="Buka Menu"
          @click="
            () => {
              mobileNavOpen = true;
            }
          "
        />
        <NuxtLink to="/" class="flex items-center gap-2.5 group">
          <div
            class="w-auto max-w-30 h-10 shrink-0 flex items-center justify-center"
          >
            <USkeleton v-if="settingsStatus === 'pending'" class="w-24 h-8 rounded-lg bg-white/20" />
            <img
              v-else
              :src="logoSrc"
              alt="Logo BbystarGO"
              width="120"
              height="40"
              class="w-full h-full object-contain"
              @error="handleLogoError"
            />
          </div>
        </NuxtLink>
      </div>

      <!-- Center: Navigation links (Desktop Only - lg:flex) -->
      <nav
        class="hidden lg:flex items-center gap-1.5"
        aria-label="Navigasi Utama"
      >
        <NuxtLink
          v-for="link in navLinks"
          :key="link.to"
          :to="link.to"
          class="px-3.5 py-1.5 rounded-full text-xs transition-all duration-200"
          :class="
            route.path === link.to ||
            (link.to.startsWith('/#') && route.hash === link.to.substring(1))
              ? 'bg-white text-primary-600 font-black shadow-xs'
              : 'text-white/90 hover:text-white hover:bg-white/15 font-bold'
          "
        >
          {{ link.label }}
        </NuxtLink>
      </nav>

      <!-- Right: Action Icons (Always visible on all screens) -->
      <div class="flex items-center sm:gap-2 shrink-0">
        <NuxtLink to="/wishlist" class="relative group">
          <UButton
            icon="i-heroicons-heart"
            color="neutral"
            variant="ghost"
            class="!text-white hover:!bg-white/10 transition-all duration-300 rounded-xl p-2 cursor-pointer"
            aria-label="Wishlist"
          />
          <UBadge
            v-if="wishlist.count > 0"
            size="sm"
            class="absolute -top-1 -right-1 min-w-4.5 h-4.5 flex items-center justify-center px-1 rounded-full text-[9px] font-extrabold ring-2 ring-primary-500 bg-white text-primary-500 animate-pulse"
          >
            {{ wishlist.count }}
          </UBadge>
        </NuxtLink>

        <NuxtLink to="/cart" class="relative group">
          <UButton
            icon="i-heroicons-shopping-cart"
            color="neutral"
            variant="ghost"
            class="!text-white hover:!bg-white/10 transition-all duration-300 rounded-xl p-2 cursor-pointer"
            aria-label="Keranjang Belanja"
          />
          <UBadge
            v-if="cart.totalQty > 0"
            size="sm"
            class="absolute -top-1 -right-1 min-w-4.5 h-4.5 flex items-center justify-center px-1 rounded-full text-[9px] font-extrabold ring-2 ring-primary-500 bg-white text-primary-500 animate-pulse"
          >
            {{ cart.totalQty }}
          </UBadge>
        </NuxtLink>

        <!-- User Profile Dropdown or Login Button -->
        <div class="shrink-0 flex items-center">
          <UDropdownMenu
            v-if="authStore.isLoggedIn"
            :items="userDropdownItems"
            :ui="{ content: 'w-48' }"
          >
            <UButton
              icon="i-heroicons-user"
              color="neutral"
              variant="ghost"
              aria-label="Menu Akun"
              class="!text-white hover:!bg-white/10 transition-all duration-300 rounded-xl p-2 flex items-center gap-1 cursor-pointer"
            >
              <span
                class="hidden md:inline text-xs font-bold text-white truncate max-w-25"
              >
                {{ authStore.user?.name.split(" ")[0] }}
              </span>
            </UButton>
          </UDropdownMenu>
          <NuxtLink v-else to="/login">
            <UButton
              color="neutral"
              variant="solid"
              icon="i-heroicons-user"
              class="!bg-white !text-primary-500 hover:!bg-gray-50 font-extrabold text-[11px] rounded-full px-4 py-2 cursor-pointer shadow-md shadow-black/10 hover:scale-105 transition-all duration-300"
            >
              Masuk
            </UButton>
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Mobile Slideover Navigation (Burger drawer) -->
    <USlideover v-model:open="mobileNavOpen" side="left">
      <template #content>
        <div
          class="p-6 space-y-6 h-full flex flex-col justify-between bg-white"
        >
          <div class="space-y-6">
            <div
              class="flex items-center justify-between pb-4 border-b border-primary-100"
            >
              <NuxtLink
                to="/"
                class="flex items-center gap-2.5"
                @click="mobileNavOpen = false"
              >
                <div
                  class="w-auto max-w-30 h-10 shrink-0 flex items-center justify-center"
                >
                  <USkeleton v-if="settingsStatus === 'pending'" class="w-24 h-8 rounded-lg" />
                  <img
                    v-else
                    :src="logoSrc"
                    alt="Logo BbystarGO"
                    width="120"
                    height="40"
                    class="w-full h-full object-contain"
                    @error="handleLogoError"
                  />
                </div>
              </NuxtLink>
              <UButton
                icon="i-heroicons-x-mark"
                color="neutral"
                variant="ghost"
                aria-label="Tutup menu"
                class="hover:bg-gray-100 rounded-lg"
                @click="
                  () => {
                    mobileNavOpen = false;
                  }
                "
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
            <div class="pt-4 border-t border-primary-100">
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
              &copy; {{ new Date().getFullYear() }} BbystarGO
            </p>
          </div>
        </div>
      </template>
    </USlideover>
  </header>
</template>
