import type { Category, CatalogFilters, PaginatedResult, Product } from '~/types'
import { dummyCategories, dummyProducts } from './dummyData'

// Dummy implementation — same function signatures the real API-backed
// service will use later, so swapping the body for $fetch() calls is the
// only change needed once api/ ships these endpoints.

const PAGE_SIZE = 8
const SIMULATED_LATENCY_MS = 300

function delay<T>(value: T): Promise<T> {
  return new Promise((resolve) => setTimeout(() => resolve(value), SIMULATED_LATENCY_MS))
}

function minPrice(product: Product): number {
  return Math.min(...product.variants.map((v) => v.price))
}

function isOutOfStock(product: Product): boolean {
  return product.variants.every((v) => v.stock === 0)
}

export const catalogService = {
  async getCategories(): Promise<Category[]> {
    return delay(dummyCategories)
  },

  async getProducts(filters: Partial<CatalogFilters> = {}): Promise<PaginatedResult<Product>> {
    const page = filters.page ?? 1
    const search = (filters.search ?? '').trim().toLowerCase()

    let results = dummyProducts.filter((p) => p.isActive)

    if (filters.category) {
      const category = dummyCategories.find((c) => c.slug === filters.category)
      if (category) results = results.filter((p) => p.categoryId === category.id)
    }

    if (filters.minPrice != null) {
      results = results.filter((p) => minPrice(p) >= filters.minPrice!)
    }

    if (filters.maxPrice != null) {
      results = results.filter((p) => minPrice(p) <= filters.maxPrice!)
    }

    if (search) {
      results = results.filter((p) => p.name.toLowerCase().includes(search))
    }

    switch (filters.sort) {
      case 'price_asc':
        results = [...results].sort((a, b) => minPrice(a) - minPrice(b))
        break
      case 'price_desc':
        results = [...results].sort((a, b) => minPrice(b) - minPrice(a))
        break
      case 'rating':
        results = [...results].sort((a, b) => b.ratingAvg - a.ratingAvg)
        break
      default:
        results = [...results].sort((a, b) => b.id - a.id)
    }

    const total = results.length
    const start = (page - 1) * PAGE_SIZE
    const pageItems = results.slice(start, start + PAGE_SIZE)
    const hasMore = start + PAGE_SIZE < total

    return delay({
      data: pageItems,
      hasMore,
      nextPage: hasMore ? page + 1 : null,
      total,
    })
  },

  async getFeaturedProducts(limit = 4): Promise<Product[]> {
    const featured = [...dummyProducts]
      .filter((p) => p.isActive)
      .sort((a, b) => b.ratingAvg - a.ratingAvg)
      .slice(0, limit)
    return delay(featured)
  },

  async getProductBySlug(slug: string): Promise<Product | null> {
    const product = dummyProducts.find((p) => p.slug === slug) ?? null
    return delay(product)
  },

  async getProductsByIds(ids: number[]): Promise<Product[]> {
    const results = dummyProducts.filter((p) => ids.includes(p.id))
    return delay(results)
  },

  isOutOfStock,
  minPrice,
}
