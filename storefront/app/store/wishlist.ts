import { defineStore } from "pinia";

const STORAGE_KEY = "dpi-wishlist";

// ssr: false (SPA mode) — safe to touch localStorage directly, mirrors store/cart.ts.
function readFromStorage(): number[] {
  if (typeof localStorage === "undefined") return [];
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    return raw ? (JSON.parse(raw) as number[]) : [];
  } catch {
    return [];
  }
}

function writeToStorage(ids: number[]) {
  if (typeof localStorage === "undefined") return;
  localStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
}

export const useWishlistStore = defineStore("wishlist", {
  state: () => ({
    productIds: readFromStorage(),
  }),

  getters: {
    count: (state) => state.productIds.length,
  },

  actions: {
    persist() {
      writeToStorage(this.productIds);
    },

    isWishlisted(productId: number) {
      return this.productIds.includes(productId);
    },

    toggle(productId: number) {
      this.productIds = this.productIds.includes(productId)
        ? this.productIds.filter((id) => id !== productId)
        : [...this.productIds, productId];
      this.persist();
    },

    clear() {
      this.productIds = [];
      this.persist();
    },
  },
});
