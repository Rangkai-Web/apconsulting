<script setup lang="ts">
import { useCartStore } from "~/store/cart";
import { useCurrency } from "~/composables/useCurrency";
import type { CartItem } from "~/types";

const cart = useCartStore();
const { format } = useCurrency();

useSeoMeta({ title: "Keranjang Belanja", robots: "noindex, nofollow" });

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

function closeConfirmModal() {
  confirmOpen.value = false;
}
</script>

<template>
  <div
    class="relative overflow-hidden bg-linear-to-b from-primary-50/40 via-white to-gray-50/50 min-h-screen py-8 md:py-12"
  >
    <!-- Background Decor Elements -->
    <div
      class="absolute top-12 -left-40 w-96 h-96 bg-primary-200/20 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute top-[400px] -right-40 w-96 h-96 bg-primary-100/20 rounded-full blur-3xl pointer-events-none"
    ></div>
    <AppCuteDecor />

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 space-y-8">
      <!-- Breadcrumb & Step Navigation Header -->
      <div
        class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white/80 backdrop-blur-md border border-gray-200/80 rounded-2xl p-4 shadow-xs"
      >
        <div class="flex items-center gap-3">
          <div
            class="w-10 h-10 rounded-xl bg-linear-to-tr from-primary-600 to-primary-400 flex items-center justify-center text-white shadow-md shadow-primary-500/20 shrink-0"
          >
            <UIcon name="i-heroicons-shopping-bag" class="w-5 h-5" />
          </div>
          <div>
            <div
              class="flex items-center gap-2 text-xs font-semibold text-gray-500"
            >
              <NuxtLink to="/" class="hover:text-primary-600">Beranda</NuxtLink>
              <span>/</span>
              <span class="text-primary-600 font-bold">Keranjang</span>
            </div>
            <h1
              class="text-lg md:text-xl font-black text-gray-900 tracking-tight"
            >
              Keranjang Belanja
            </h1>
          </div>
        </div>

        <!-- Checkout Steps Progress -->
        <div
          class="flex items-center gap-1.5 sm:gap-3 text-[11px] sm:text-xs font-bold overflow-x-auto no-scrollbar py-0.5 shrink-0"
        >
          <div
            class="flex items-center gap-1.5 text-primary-600 bg-primary-50 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-full border border-primary-200/60 shadow-2xs shrink-0"
          >
            <span
              class="w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-primary-600 text-white flex items-center justify-center text-[9px] sm:text-[10px] font-black"
              >1</span
            >
            <span>Keranjang</span>
          </div>
          <UIcon
            name="i-heroicons-chevron-right"
            class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-300 shrink-0"
          />
          <div class="flex items-center gap-1.5 text-gray-400 shrink-0">
            <span
              class="w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-[9px] sm:text-[10px] font-black"
              >2</span
            >
            <span>Checkout</span>
          </div>
          <UIcon
            name="i-heroicons-chevron-right"
            class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-300 shrink-0"
          />
          <div class="flex items-center gap-1.5 text-gray-400 shrink-0">
            <span
              class="w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-[9px] sm:text-[10px] font-black"
              >3</span
            >
            <span>Pembayaran</span>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-if="cart.items.length === 0"
        class="max-w-lg mx-auto bg-white/90 backdrop-blur-md border border-gray-200/80 rounded-3xl p-8 sm:p-12 shadow-xl shadow-gray-900/5 text-center"
      >
        <EmptyState
          icon="i-heroicons-shopping-cart"
          title="Keranjang Anda Masih Kosong"
          description="Yuk telusuri katalog lengkap BbystarGO dan temukan produk favoritmu dari Korea, China, Jepang &amp; Indonesia!"
          action-label="Jelajahi Katalog Produk"
          action-to="/katalog"
        />
      </div>

      <!-- Cart Layout Grid -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left Side: Items List (7 cols or 8 cols) -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-4">
          <!-- List Header Card -->
          <div
            class="bg-white/90 backdrop-blur-md border border-gray-200/80 rounded-3xl p-6 shadow-xs space-y-4"
          >
            <div
              class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-gray-100 pb-4"
            >
              <div class="flex items-center gap-2.5">
                <span class="text-sm font-extrabold text-gray-900"
                  >Daftar Produk Belanja</span
                >
                <span
                  class="px-2.5 py-0.5 bg-primary-50 text-primary-700 text-xs font-black rounded-full border border-primary-200/60"
                >
                  {{ cart.totalQty }} item
                </span>
              </div>
              <button
                type="button"
                class="text-xs font-semibold text-red-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer transition-colors"
                @click="cart.clear()"
              >
                <UIcon name="i-heroicons-trash" class="w-3.5 h-3.5" />
                Kosongkan Keranjang
              </button>
            </div>

            <!-- Items Loop -->
            <div class="divide-y divide-gray-100">
              <div
                v-for="item in cart.items"
                :key="item.variantId"
                class="py-4 sm:py-5 first:pt-0 last:pb-0 group transition-all"
              >
                <div
                  class="flex flex-col sm:flex-row items-start sm:items-center gap-4"
                >
                  <!-- Product Image -->
                  <NuxtLink
                    :to="`/produk/${item.productSlug}`"
                    class="shrink-0 relative overflow-hidden rounded-2xl border border-gray-200/80 shadow-2xs group-hover:border-primary-300 transition-colors w-20 h-20 sm:w-24 sm:h-24 bg-gray-50"
                  >
                    <img
                      :src="
                        item.image ??
                        'https://picsum.photos/seed/cart-fallback/200/200'
                      "
                      :alt="item.productName"
                      class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
                    />
                  </NuxtLink>

                  <!-- Product Info -->
                  <div class="flex-1 min-w-0 space-y-1.5 w-full">
                    <div class="flex items-start justify-between gap-2">
                      <NuxtLink
                        :to="`/produk/${item.productSlug}`"
                        class="text-sm font-bold text-gray-900 hover:text-primary-600 transition-colors line-clamp-2 leading-snug"
                      >
                        {{ item.productName }}
                      </NuxtLink>
                    </div>

                    <!-- Badges -->
                    <div class="flex flex-wrap items-center gap-2">
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 bg-gray-100 text-[11px] text-gray-600 rounded-lg font-bold border border-gray-200/60"
                      >
                        Varian: {{ item.variantName }}
                      </span>
                      <span
                        v-if="item.fulfillmentType === 'pre_order'"
                        class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-black rounded-lg border border-amber-200/60"
                      >
                        <UIcon name="i-heroicons-clock" class="w-3 h-3" />
                        Pre-Order (DP)
                      </span>
                      <span
                        v-else
                        class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-black rounded-lg border border-emerald-200/60"
                      >
                        <UIcon
                          name="i-heroicons-check-circle"
                          class="w-3 h-3"
                        />
                        Ready Stock
                      </span>
                    </div>

                    <!-- Single Item Price -->
                    <div class="flex items-baseline gap-2 pt-0.5">
                      <span class="text-xs font-semibold text-gray-500"
                        >Harga:</span
                      >
                      <span class="text-sm font-black text-primary-600">
                        {{ format(item.price) }}
                      </span>
                      <span
                        v-if="
                          item.fulfillmentType === 'pre_order' &&
                          item.originalPrice
                        "
                        class="text-[10px] text-gray-400 font-semibold line-through"
                      >
                        {{ format(item.originalPrice) }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Quantity Controls & Subtotal Row -->
                <div
                  class="flex items-center justify-between gap-4 mt-4 pt-3 border-t border-dashed border-gray-100"
                >
                  <div class="flex items-center gap-3">
                    <span
                      class="text-xs font-semibold text-gray-500 hidden sm:inline"
                      >Jumlah:</span
                    >
                    <QtyStepper
                      :model-value="item.qty"
                      :max="item.stock"
                      @update:model-value="
                        (qty) => cart.updateQty(item.variantId, qty)
                      "
                    />
                  </div>

                  <div class="flex items-center gap-4">
                    <div class="text-right">
                      <span
                        class="text-[10px] text-gray-400 font-semibold block"
                        >Total Item</span
                      >
                      <span
                        class="text-sm sm:text-base font-black text-gray-900"
                      >
                        {{ format(item.price * item.qty) }}
                      </span>
                    </div>

                    <UButton
                      icon="i-heroicons-trash"
                      color="error"
                      variant="ghost"
                      class="rounded-xl hover:bg-red-50 text-red-400 hover:text-red-600 transition-all shrink-0 p-2 cursor-pointer"
                      aria-label="Hapus Item"
                      @click="askRemove(item)"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Side: Order Summary Sidebar (5 cols or 4 cols) -->
        <div class="lg:col-span-5 xl:col-span-4 sticky top-24">
          <div
            class="bg-white/90 backdrop-blur-md border border-gray-200/80 rounded-3xl p-6 shadow-xl shadow-gray-900/5 space-y-6 relative overflow-hidden"
          >
            <!-- Header Ringkasan -->
            <div
              class="flex items-center justify-between border-b border-gray-100 pb-4"
            >
              <h2
                class="text-sm font-extrabold text-gray-900 uppercase tracking-wide"
              >
                Ringkasan Pesanan
              </h2>
              <UIcon
                name="i-heroicons-receipt-percent"
                class="w-5 h-5 text-primary-500"
              />
            </div>

            <!-- Price Breakdown -->
            <div class="space-y-3.5 text-xs font-semibold">
              <div class="flex items-center justify-between text-gray-600">
                <span>Total Jumlah Barang</span>
                <span class="text-gray-900 font-extrabold"
                  >{{ cart.totalQty }} produk</span
                >
              </div>

              <div class="flex items-center justify-between text-gray-600">
                <span>Total Bobot Kirim</span>
                <span class="text-gray-900 font-extrabold"
                  >{{ (cart.totalWeight / 1000).toFixed(2) }} kg</span
                >
              </div>

              <div class="flex items-center justify-between text-gray-600">
                <span>Subtotal Harga</span>
                <span class="text-gray-900 font-extrabold">{{
                  format(cart.subtotal)
                }}</span>
              </div>

              <div class="flex items-center justify-between text-gray-600">
                <span>Estimasi Ongkos Kirim</span>
                <span
                  class="text-primary-600 text-[10px] font-bold bg-primary-50 px-2 py-0.5 rounded-md border border-primary-100"
                  >Dihitung di Checkout</span
                >
              </div>

              <div
                class="border-t border-gray-100 pt-3.5 flex items-center justify-between"
              >
                <div>
                  <span class="text-sm font-black text-gray-900 block"
                    >Total Pembayaran</span
                  >
                  <span class="text-[10px] text-gray-400 font-normal"
                    >Belum termasuk ongkos kirim</span
                  >
                </div>
                <span class="text-lg font-black text-primary-600">
                  {{ format(cart.subtotal) }}
                </span>
              </div>
            </div>

            <!-- Note Box -->
            <div
              class="bg-gray-50 border border-gray-200/60 rounded-2xl p-3.5 text-[11px] text-gray-600 leading-relaxed space-y-1.5"
            >
              <div class="flex items-center gap-1.5 font-bold text-gray-800">
                <UIcon
                  name="i-heroicons-information-circle"
                  class="w-4 h-4 text-primary-500 shrink-0"
                />
                <span>Informasi Pengiriman</span>
              </div>
              <p class="text-gray-500">
                Opsi kurir (J&amp;T, POS) serta ongkos kirim
                otomatis dihitung sesuai alamat tujuan Anda pada tahap
                berikutnya.
              </p>
            </div>

            <!-- Checkout Button -->
            <div class="pt-2">
              <NuxtLink to="/checkout" class="block">
                <UButton
                  block
                  color="primary"
                  size="xl"
                  class="font-black py-4 rounded-2xl cursor-pointer shadow-lg shadow-primary-500/25 hover:shadow-primary-500/35 hover:scale-[1.01] transition-all duration-300 flex items-center justify-center gap-2 text-sm"
                >
                  <span>Lanjut ke Checkout</span>
                  <UIcon name="i-heroicons-arrow-right" class="w-4 h-4" />
                </UButton>
              </NuxtLink>
            </div>

            <!-- Trust Features Badges -->
            <div
              class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100 text-[10px] font-bold text-gray-500"
            >
              <div class="flex items-center gap-1.5">
                <UIcon
                  name="i-heroicons-shield-check"
                  class="w-4 h-4 text-emerald-500 shrink-0"
                />
                <span>100% Produk Original</span>
              </div>
              <div class="flex items-center gap-1.5">
                <UIcon
                  name="i-heroicons-truck"
                  class="w-4 h-4 text-blue-500 shrink-0"
                />
                <span>Ekspedisi Terpercaya</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <UModal v-model:open="confirmOpen" title="Hapus produk dari keranjang?">
        <template #body>
          <div class="space-y-3 py-1">
            <p class="text-xs text-gray-600 leading-relaxed">
              Apakah Anda yakin ingin menghapus produk
              <strong class="text-gray-900 font-bold"
                >"{{ itemToRemove?.productName }}" ({{
                  itemToRemove?.variantName
                }})</strong
              >
              dari keranjang belanja?
            </p>
          </div>
        </template>
        <template #footer>
          <div class="flex items-center justify-end gap-3 w-full">
            <UButton
              color="neutral"
              variant="outline"
              class="rounded-xl font-bold cursor-pointer"
              @click="closeConfirmModal"
            >
              Batal
            </UButton>
            <UButton
              color="error"
              class="rounded-xl font-extrabold px-5 cursor-pointer"
              @click="confirmRemove"
            >
              Ya, Hapus Produk
            </UButton>
          </div>
        </template>
      </UModal>
    </div>
  </div>
</template>
