<script setup lang="ts">
import { useCartStore } from "~/store/cart";
import { useCurrency } from "~/composables/useCurrency";
import type { CartItem } from "~/types";

const cart = useCartStore();
const { format } = useCurrency();

const itemToRemove = ref<CartItem | null>(null);
const confirmOpen = ref(false);

function askRemove(item: CartItem) {
  itemToRemove.value = item;
  confirmOpen.value = true;
}

function confirmRemove() {
  if (itemToRemove.value) cart.removeItem(itemToRemove.value.variantId);
  confirmOpen.value = false;
  itemToRemove.value = null;
}
</script>

<template>
  <div
    class="relative overflow-hidden bg-linear-to-b from-primary-50/15 via-white to-white min-h-screen py-10"
  >
    <!-- Ambient Background Glows -->
    <div
      class="absolute top-12 -left-40 w-96 h-96 bg-primary-100/10 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-[400px] -right-40 w-96 h-96 bg-secondary-100/10 rounded-full blur-3xl pointer-events-none"
    ></div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative">
      <!-- Page Title -->
      <div class="mb-8 flex items-center gap-3">
        <div
          class="w-10 h-10 rounded-2xl bg-primary-50 flex items-center justify-center text-primary-600 shadow-sm border border-primary-100"
        >
          <UIcon name="i-heroicons-shopping-cart" class="w-5 h-5" />
        </div>
        <div>
          <span
            class="text-[10px] text-primary-600 uppercase font-extrabold tracking-wider"
            >Belanjaan Anda</span
          >
          <h1
            class="text-xl md:text-2xl font-black text-gray-900 tracking-tight leading-none mt-0.5"
          >
            Keranjang Belanja
          </h1>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-if="cart.items.length === 0"
        class="max-w-md mx-auto bg-white/70 backdrop-blur-sm border border-gray-100 rounded-3xl p-8 shadow-sm"
      >
        <EmptyState
          icon="i-heroicons-shopping-cart"
          title="Keranjang Anda Kosong"
          description="Yuk mulai telusuri katalog dan temukan produk skincare dan perawatan terbaik untuk kulit Anda."
          action-label="Lihat Katalog"
          action-to="/katalog"
        />
      </div>

      <!-- Cart Grid -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-8">
        <!-- Left Side: Items List -->
        <div
          class="bg-white/80 backdrop-blur-sm border border-gray-100 rounded-3xl p-6 shadow-sm space-y-4"
        >
          <h2
            class="text-xs uppercase tracking-wider font-extrabold text-primary-800 border-b border-gray-100 pb-3 mb-4"
          >
            Daftar Item ({{ cart.totalQty }} produk)
          </h2>

          <div class="divide-y divide-gray-100">
            <div
              v-for="item in cart.items"
              :key="item.variantId"
              class="flex items-center gap-4 py-5 first:pt-0 last:pb-0 group"
            >
              <!-- Item Image -->
              <NuxtLink
                :to="`/produk/${item.productSlug}`"
                class="shrink-0 relative overflow-hidden rounded-xl border border-gray-100 shadow-sm"
              >
                <img
                  :src="
                    item.image ??
                    'https://picsum.photos/seed/cart-fallback/200/200'
                  "
                  :alt="item.productName"
                  class="w-20 h-20 object-cover bg-gray-50 transform group-hover:scale-105 transition-transform duration-300"
                />
              </NuxtLink>

              <!-- Item Details -->
              <div class="flex-1 min-w-0">
                <NuxtLink
                  :to="`/produk/${item.productSlug}`"
                  class="text-sm font-bold text-gray-900 hover:text-primary-700 transition-colors line-clamp-1"
                >
                  {{ item.productName }}
                </NuxtLink>
                <span
                  class="inline-block px-2 py-0.5 bg-gray-100 text-[10px] text-gray-500 rounded font-semibold mt-1"
                >
                  Varian: {{ item.variantName }}
                </span>
                <p class="text-sm font-extrabold text-primary-700 mt-2">
                  {{ format(item.price) }}
                </p>
              </div>

              <!-- Item Quantity Stepper -->
              <div class="flex items-center gap-3">
                <QtyStepper
                  :model-value="item.qty"
                  :max="item.stock"
                  @update:model-value="
                    (qty) => cart.updateQty(item.variantId, qty)
                  "
                />
              </div>

              <!-- Item Total Price (Desktop Only) -->
              <p
                class="text-sm font-black text-gray-800 w-24 text-right hidden sm:block"
              >
                {{ format(item.price * item.qty) }}
              </p>

              <!-- Delete Button -->
              <UButton
                icon="i-heroicons-trash"
                color="error"
                variant="ghost"
                class="rounded-xl hover:bg-red-50 text-red-500/80 hover:text-red-600 transition-all shrink-0 p-2"
                aria-label="Hapus Item"
                @click="askRemove(item)"
              />
            </div>
          </div>
        </div>

        <!-- Right Side: Summary Card -->
        <div
          class="bg-linear-to-b from-primary-50/40 to-white/90 border border-primary-100/60 rounded-3xl p-6 shadow-md shadow-primary-900/5 h-fit space-y-5 relative overflow-hidden"
        >
          <!-- Background decoration icon -->
          <UIcon
            name="i-lucide-leaf"
            class="absolute -bottom-8 -right-8 w-28 h-28 text-primary-500/5 -rotate-12 pointer-events-none"
          />

          <h2
            class="text-xs uppercase tracking-wider font-extrabold text-primary-800 border-b border-primary-100/50 pb-2.5"
          >
            Ringkasan Belanja
          </h2>

          <div class="space-y-3.5">
            <div
              class="flex items-center justify-between text-xs font-semibold"
            >
              <span class="text-gray-500"
                >Subtotal ({{ cart.totalQty }} item)</span
              >
              <span class="text-gray-900 font-extrabold">{{
                format(cart.subtotal)
              }}</span>
            </div>

            <div
              class="flex items-center justify-between text-xs font-semibold"
            >
              <span class="text-gray-500">Estimasi Ongkir</span>
              <span class="text-primary-700 italic">Dihitung nanti</span>
            </div>
          </div>

          <div
            class="bg-white/60 border border-primary-100/30 rounded-xl p-3 text-[10px] text-gray-500 leading-relaxed font-medium"
          >
            <UIcon
              name="i-heroicons-information-circle"
              class="w-4 h-4 text-primary-600 inline shrink-0"
            />
            Ongkos kirim & kurir pengiriman dapat diatur pada langkah checkout
            selanjutnya.
          </div>

          <div class="pt-2">
            <UButton
              block
              color="primary"
              size="lg"
              to="/checkout"
              class="font-semibold py-3 rounded-full cursor-pointer shadow-lg shadow-primary-600/20 hover:scale-[1.01] transition-transform duration-300"
            >
              Lanjut ke Checkout
            </UButton>
          </div>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <UModal v-model:open="confirmOpen" title="Hapus item?">
        <template #body>
          <p class="text-sm text-gray-600">
            Hapus
            <strong
              >{{ itemToRemove?.productName }} ({{
                itemToRemove?.variantName
              }})</strong
            >
            dari keranjang belanja Anda?
          </p>
        </template>
        <template #footer>
          <div class="flex items-center justify-between gap-2.5 w-full">
            <UButton
              color="neutral"
              variant="outline"
              class="rounded-xl cursor-pointer"
              @click="() => { confirmOpen = false }"
              >Batal</UButton
            >
            <UButton
              color="error"
              class="rounded-xl font-bold px-4 cursor-pointer"
              @click="confirmRemove"
              >Ya, Hapus</UButton
            >
          </div>
        </template>
      </UModal>
    </div>
  </div>
</template>
