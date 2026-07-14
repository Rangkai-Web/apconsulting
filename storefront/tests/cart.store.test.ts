import { beforeEach, describe, expect, it } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useCartStore } from '../app/store/cart'
import type { Product, ProductVariant } from '../app/types'

function makeProduct(overrides: Partial<Product> = {}): Product {
  return {
    id: 1,
    categoryId: 1,
    name: 'Sunscreen',
    slug: 'sunscreen',
    description: 'Test product',
    images: ['sunscreen.png'],
    isActive: true,
    ratingAvg: 0,
    ratingCount: 0,
    variants: [],
    ...overrides,
  }
}

function makeVariant(overrides: Partial<ProductVariant> = {}): ProductVariant {
  return {
    id: 10,
    productId: 1,
    name: '50ml',
    sku: 'SUN-50',
    price: 100000,
    stock: 5,
    weight: 200,
    image: null,
    ...overrides,
  }
}

describe('cart store', () => {
  beforeEach(() => {
    localStorage.clear()
    setActivePinia(createPinia())
  })

  it('adds a new variant to the cart', () => {
    const cart = useCartStore()
    cart.addItem(makeProduct(), makeVariant(), 2)

    expect(cart.items).toHaveLength(1)
    expect(cart.items[0]).toMatchObject({ variantId: 10, qty: 2, price: 100000 })
  })

  it('increments qty instead of duplicating when the same variant is added again', () => {
    const cart = useCartStore()
    cart.addItem(makeProduct(), makeVariant(), 2)
    cart.addItem(makeProduct(), makeVariant(), 1)

    expect(cart.items).toHaveLength(1)
    expect(cart.items[0]!.qty).toBe(3)
  })

  it('caps qty at available stock when adding a new item', () => {
    const cart = useCartStore()
    cart.addItem(makeProduct(), makeVariant({ stock: 3 }), 10)

    expect(cart.items[0]!.qty).toBe(3)
  })

  it('caps qty at available stock when incrementing an existing item', () => {
    const cart = useCartStore()
    cart.addItem(makeProduct(), makeVariant({ stock: 3 }), 2)
    cart.addItem(makeProduct(), makeVariant({ stock: 3 }), 5)

    expect(cart.items[0]!.qty).toBe(3)
  })

  it('updates qty for an existing item, clamped between 1 and stock', () => {
    const cart = useCartStore()
    cart.addItem(makeProduct(), makeVariant({ stock: 5 }), 1)

    cart.updateQty(10, 4)
    expect(cart.items[0]!.qty).toBe(4)

    cart.updateQty(10, 99)
    expect(cart.items[0]!.qty).toBe(5)

    cart.updateQty(10, 0)
    expect(cart.items[0]!.qty).toBe(1)
  })

  it('does nothing when updating qty for a variant not in the cart', () => {
    const cart = useCartStore()
    cart.addItem(makeProduct(), makeVariant(), 1)

    cart.updateQty(999, 5)

    expect(cart.items).toHaveLength(1)
    expect(cart.items[0]!.qty).toBe(1)
  })

  it('removes only the targeted item', () => {
    const cart = useCartStore()
    cart.addItem(makeProduct(), makeVariant({ id: 10 }), 1)
    cart.addItem(makeProduct(), makeVariant({ id: 11 }), 1)

    cart.removeItem(10)

    expect(cart.items).toHaveLength(1)
    expect(cart.items[0]!.variantId).toBe(11)
  })

  it('clears the cart', () => {
    const cart = useCartStore()
    cart.addItem(makeProduct(), makeVariant(), 1)

    cart.clear()

    expect(cart.items).toHaveLength(0)
  })

  it('computes totalQty, subtotal and totalWeight across multiple items', () => {
    const cart = useCartStore()
    cart.addItem(makeProduct(), makeVariant({ id: 10, price: 100000, weight: 200 }), 2)
    cart.addItem(makeProduct(), makeVariant({ id: 11, price: 50000, weight: 300 }), 3)

    expect(cart.totalQty).toBe(5)
    expect(cart.subtotal).toBe(2 * 100000 + 3 * 50000)
    expect(cart.totalWeight).toBe(2 * 200 + 3 * 300)
  })

  it('persists items to localStorage and restores them for a fresh store instance', () => {
    const cart = useCartStore()
    cart.addItem(makeProduct(), makeVariant(), 2)

    setActivePinia(createPinia())
    const restoredCart = useCartStore()

    expect(restoredCart.items).toHaveLength(1)
    expect(restoredCart.items[0]!.variantId).toBe(10)
  })
})
