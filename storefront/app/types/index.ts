export interface Category {
  id: number
  name: string
  slug: string
  parentId: number | null
}

export interface ProductVariant {
  id: number
  productId: number
  name: string
  sku: string
  price: number
  stock: number
  weight: number
  image: string | null
}

export interface Product {
  id: number
  categoryId: number
  category?: Category
  name: string
  slug: string
  description: string
  images: string[]
  isActive: boolean
  ratingAvg: number
  ratingCount: number
  variants: ProductVariant[]
}

export interface CartItem {
  variantId: number
  productId: number
  productName: string
  productSlug: string
  variantName: string
  price: number
  image: string | null
  qty: number
  stock: number
  weight: number
}

export interface Address {
  id: number
  label: string
  recipientName: string
  phone: string
  province: string
  city: string
  district: string
  postalCode: string
  fullAddress: string
  isDefault: boolean
  rajaongkirDistrictId?: number | null
}

export type OrderStatus =
  | 'pending'
  | 'paid'
  | 'processing'
  | 'shipped'
  | 'completed'
  | 'cancelled'
  | 'expired'

export interface OrderItem {
  id: number
  productVariantId: number
  productName: string
  variantName: string
  price: number
  qty: number
  subtotal: number
  rated: boolean
}

export interface Order {
  id: number
  orderNumber: string
  status: OrderStatus
  courier: string
  courierService: string
  shippingCost: number
  subtotal: number
  total: number
  trackingNumber: string | null
  createdAt: string
  items: OrderItem[]
  snapToken?: string | null
  shippingAddress?: {
    recipientName: string
    phone: string
    province: string
    city: string
    district: string
    postalCode: string
    fullAddress: string
    rajaongkirDistrictId?: number | null
  } | null
}

export interface PaginatedResult<T> {
  data: T[]
  hasMore: boolean
  nextPage: number | null
  total: number
}

export type CatalogSort = 'newest' | 'price_asc' | 'price_desc' | 'rating'

export interface CatalogFilters {
  category: string | null
  minPrice: number | null
  maxPrice: number | null
  sort: CatalogSort
  search: string
  page: number
}

export interface User {
  id: number
  name: string
  email: string
  phone: string | null
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterData {
  name: string
  email: string
  phone?: string
  password: string
  password_confirmation: string
}

export interface AuthResponse {
  user: User
  token: string
}

