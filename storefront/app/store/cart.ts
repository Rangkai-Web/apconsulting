import { defineStore } from "pinia";
import type { CartItem, Product, ProductVariant } from "~/types";

const STORAGE_KEY = "dpi-cart";

// ssr: false (SPA mode) — safe to touch localStorage directly here, this
// module never runs on a server.
function readFromStorage(): CartItem[] {
  if (typeof localStorage === "undefined") return [];
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    return raw ? (JSON.parse(raw) as CartItem[]) : [];
  } catch {
    return [];
  }
}

function writeToStorage(items: CartItem[]) {
  if (typeof localStorage === "undefined") return;
  localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
}

export const useCartStore = defineStore("cart", {
  state: () => ({
    items: readFromStorage(),
  }),

  getters: {
    totalQty: (state) => state.items.reduce((sum, item) => sum + item.qty, 0),
    subtotal: (state) =>
      state.items.reduce((sum, item) => sum + item.price * item.qty, 0),
    totalWeight: (state) =>
      state.items.reduce((sum, item) => sum + item.weight * item.qty, 0),
  },

  actions: {
    persist() {
      writeToStorage(this.items);
    },

    addItem(product: Product, variant: ProductVariant, qty: number) {
      const existing = this.items.find((item) => item.variantId === variant.id);
      const priceToCharge = variant.fulfillmentType === "pre_order" && variant.dpAmount > 0
        ? variant.dpAmount
        : variant.price;

      if (existing) {
        existing.qty = Math.min(existing.qty + qty, variant.stock);
      } else {
        this.items.push({
          variantId: variant.id,
          productId: product.id,
          productName: product.name,
          productSlug: product.slug,
          variantName: variant.name,
          price: priceToCharge,
          image: variant.image ?? product.images[0] ?? null,
          qty: Math.min(qty, variant.stock),
          stock: variant.stock,
          weight: variant.weight,
          fulfillmentType: variant.fulfillmentType,
          originalPrice: variant.price,
        });
      }

      this.persist();
    },

    updateQty(variantId: number, qty: number) {
      const item = this.items.find((i) => i.variantId === variantId);
      if (!item) return;
      item.qty = Math.max(1, Math.min(qty, item.stock));
      this.persist();
    },

    removeItem(variantId: number) {
      this.items = this.items.filter((i) => i.variantId !== variantId);
      this.persist();
    },

    clear() {
      this.items = [];
      this.persist();
    },
  },
});
