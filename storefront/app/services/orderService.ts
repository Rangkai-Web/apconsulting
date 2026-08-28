import type { Order, OrderItem } from "~/types";
import { useAuthStore } from "~/store/auth";

function apiBase(): string {
  return useRuntimeConfig().public.apiBase;
}

function getHeaders(): Record<string, string> {
  const authStore = useAuthStore();
  const headers: Record<string, string> = {
    Accept: "application/json",
    "Content-Type": "application/json",
  };
  if (authStore.token) {
    headers.Authorization = `Bearer ${authStore.token}`;
  }
  return headers;
}

function mapOrderItem(raw: any): OrderItem {
  return {
    id: raw.id,
    productVariantId: raw.product_variant_id,
    productName: raw.product_name,
    variantName: raw.variant_name,
    price: raw.price,
    qty: raw.qty,
    subtotal: raw.subtotal,
    rated: !!raw.rating || !!raw.rated,
  };
}

function mapOrder(raw: any): Order {
  const shippingAddress = raw.shipping_address
    ? {
        recipientName: raw.shipping_address.recipient_name,
        phone: raw.shipping_address.phone,
        province: raw.shipping_address.province,
        city: raw.shipping_address.city,
        district: raw.shipping_address.district,
        postalCode: raw.shipping_address.postal_code,
        fullAddress: raw.shipping_address.full_address,
        rajaongkirDistrictId:
          raw.shipping_address.rajaongkir_district_id ?? null,
      }
    : null;

  return {
    id: raw.id,
    orderNumber: raw.order_number,
    orderStatus: raw.order_status,
    paymentStatus: raw.payment_status,
    paymentProof: raw.payment_proof ?? null,
    paymentRejectedReason: raw.payment_rejected_reason ?? null,
    courier: raw.courier,
    courierService: raw.courier_service,
    shippingCost: raw.shipping_cost,
    subtotal: raw.subtotal,
    total: raw.total,
    trackingNumber: raw.tracking_number,
    createdAt: raw.created_at,
    items: (raw.items ?? []).map(mapOrderItem),
    shippingAddress,
  };
}

// Exported only for the unit test in tests/orderService.test.ts — not part
// of the public service surface used by pages.
export function mapOrderForTest(raw: any): Order {
  return mapOrder(raw);
}

export const orderService = {
  async getOrders(): Promise<{ data: Order[]; total: number }> {
    const response = await $fetch<{ data: any[]; meta?: { total: number } }>(
      "/orders",
      {
        baseURL: apiBase(),
        headers: getHeaders(),
      },
    );
    return {
      data: (response.data ?? []).map(mapOrder),
      total: response.meta?.total ?? (response.data ?? []).length,
    };
  },

  async getOrderDetails(orderNumber: string): Promise<Order> {
    const response = await $fetch<{ data: any }>(`/orders/${orderNumber}`, {
      baseURL: apiBase(),
      headers: getHeaders(),
    });
    return mapOrder(response.data);
  },

  async lookupOrder(orderNumber: string, email: string): Promise<Order> {
    const response = await $fetch<{ data: any }>("/orders/lookup", {
      baseURL: apiBase(),
      query: { order_number: orderNumber, email },
    });
    return mapOrder(response.data);
  },

  async createOrder(data: {
    items: { variantId: number; qty: number }[];
    addressId?: number | null;
    guestName?: string | null;
    guestEmail?: string | null;
    guestPhone?: string | null;
    shippingAddress?: any;
    courier: string;
    courierService: string;
    shippingCost: number;
  }): Promise<{ orderNumber: string }> {
    const body: any = {
      items: data.items.map((i) => ({ variant_id: i.variantId, qty: i.qty })),
      courier: data.courier,
      courier_service: data.courierService,
      shipping_cost: data.shippingCost,
    };

    if (data.addressId) {
      body.address_id = data.addressId;
    } else {
      body.guest_name = data.guestName;
      body.guest_email = data.guestEmail;
      body.guest_phone = data.guestPhone;
      body.shipping_address = data.shippingAddress
        ? {
            recipient_name: data.shippingAddress.recipientName,
            phone: data.shippingAddress.phone,
            province: data.shippingAddress.province,
            city: data.shippingAddress.city,
            district: data.shippingAddress.district,
            postal_code: data.shippingAddress.postalCode,
            full_address: data.shippingAddress.fullAddress,
            rajaongkir_district_id: data.shippingAddress.rajaongkirDistrictId,
          }
        : null;
    }

    const response = await $fetch<{ order_number: string }>("/orders", {
      baseURL: apiBase(),
      method: "POST",
      headers: getHeaders(),
      body,
    });

    return { orderNumber: response.order_number };
  },

  async getShippingCost(
    districtId: number,
    totalWeight: number,
  ): Promise<any[]> {
    const response = await $fetch<{ couriers: any[] }>("/shipping/cost", {
      baseURL: apiBase(),
      method: "POST",
      headers: getHeaders(),
      body: {
        district_id: districtId,
        total_weight: totalWeight,
      },
    });
    return response.couriers ?? [];
  },

  async uploadPaymentProof(
    orderNumber: string,
    file: File,
    guestEmail?: string,
  ): Promise<Order> {
    const formData = new FormData();
    formData.append("proof", file);
    if (guestEmail) formData.append("guest_email", guestEmail);

    const authStore = useAuthStore();
    const headers: Record<string, string> = {};
    if (authStore.token) headers.Authorization = `Bearer ${authStore.token}`;

    const response = await $fetch<{ data: any }>(
      `/orders/${orderNumber}/payment-proof`,
      {
        baseURL: apiBase(),
        method: "POST",
        headers,
        body: formData,
      },
    );
    return mapOrder(response.data);
  },

  async rateOrderItem(orderItemId: number, stars: number): Promise<any> {
    return await $fetch(`/order-items/${orderItemId}/rating`, {
      baseURL: apiBase(),
      method: "POST",
      headers: getHeaders(),
      body: { stars },
    });
  },
};
