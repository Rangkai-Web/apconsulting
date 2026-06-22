import type { Category, CatalogFilters, PaginatedResult, Product, ProductVariant } from '~/types'

function mapVariant(raw: any): ProductVariant {
  return {
    id: raw.id,
    productId: raw.product_id ?? 0,
    name: raw.name,
    sku: raw.sku,
    price: raw.price,
    stock: raw.stock,
    weight: raw.weight,
    image: raw.image,
  }
}

function mapProduct(raw: any): Product {
  return {
    id: raw.id,
    categoryId: raw.category_id,
    name: raw.name,
    slug: raw.slug,
    description: raw.description,
    images: raw.images ?? [],
    isActive: raw.is_active,
    ratingAvg: raw.rating_avg,
    ratingCount: raw.rating_count,
    variants: (raw.variants ?? []).map(mapVariant),
  }
}

function mapCategory(raw: any): Category {
  return {
    id: raw.id,
    name: raw.name,
    slug: raw.slug,
    parentId: raw.parent_id,
  }
}

function apiBase(): string {
  return useRuntimeConfig().public.apiBase
}

function minPrice(product: Product): number {
  return Math.min(...product.variants.map((v) => v.price))
}

function isOutOfStock(product: Product): boolean {
  return product.variants.every((v) => v.stock === 0)
}

export const catalogService = {
  async getCategories(): Promise<Category[]> {
    const response = await $fetch<{ data: any[] }>('/categories', { baseURL: apiBase() })
    return response.data.map(mapCategory)
  },

  async getProducts(filters: Partial<CatalogFilters> = {}): Promise<PaginatedResult<Product>> {
    const response = await $fetch<{ data: any[]; meta: { has_more: boolean; next_page: number | null; total: number } }>(
      '/products',
      {
        baseURL: apiBase(),
        query: {
          category: filters.category ?? undefined,
          min: filters.minPrice ?? undefined,
          max: filters.maxPrice ?? undefined,
          search: filters.search || undefined,
          sort: filters.sort ?? undefined,
          page: filters.page ?? 1,
        },
      },
    )

    return {
      data: response.data.map(mapProduct),
      hasMore: response.meta.has_more,
      nextPage: response.meta.next_page,
      total: response.meta.total,
    }
  },

  async getFeaturedProducts(limit = 4): Promise<Product[]> {
    const result = await catalogService.getProducts({ sort: 'rating', page: 1 })
    return result.data.slice(0, limit)
  },

  async getProductBySlug(slug: string): Promise<Product | null> {
    try {
      const response = await $fetch<{ data: any }>(`/products/${slug}`, { baseURL: apiBase() })
      return mapProduct(response.data)
    } catch {
      return null
    }
  },

  async getProductsByIds(ids: number[]): Promise<Product[]> {
    if (ids.length === 0) return []
    const response = await $fetch<{ data: any[] }>('/products', {
      baseURL: apiBase(),
      query: { ids: ids.join(',') },
    })
    return response.data.map(mapProduct)
  },

  isOutOfStock,
  minPrice,
}
