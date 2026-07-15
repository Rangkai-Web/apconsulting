import type { Address } from "~/types";
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

function mapAddress(raw: any): Address {
  return {
    id: raw.id,
    label: raw.label ?? "",
    recipientName: raw.recipient_name,
    phone: raw.phone,
    province: raw.province,
    city: raw.city,
    district: raw.district,
    postalCode: raw.postal_code,
    fullAddress: raw.full_address,
    isDefault: !!raw.is_default,
    rajaongkirDistrictId: raw.rajaongkir_district_id ?? null,
  };
}

function unmapAddress(address: Partial<Address>): any {
  return {
    label: address.label,
    recipient_name: address.recipientName,
    phone: address.phone,
    province: address.province,
    city: address.city,
    district: address.district,
    postal_code: address.postalCode,
    full_address: address.fullAddress,
    is_default: address.isDefault,
    rajaongkir_district_id: address.rajaongkirDistrictId,
  };
}

export const addressService = {
  async getAddresses(): Promise<Address[]> {
    try {
      const response = await $fetch<{ data: any[] }>("/addresses", {
        baseURL: apiBase(),
        headers: getHeaders(),
      });
      return (response.data ?? []).map(mapAddress);
    } catch (error) {
      console.error("Gagal memuat daftar alamat:", error);
      return [];
    }
  },

  async createAddress(address: Partial<Address>): Promise<Address> {
    const response = await $fetch<{ data: any }>("/addresses", {
      baseURL: apiBase(),
      method: "POST",
      headers: getHeaders(),
      body: unmapAddress(address),
    });
    return mapAddress(response.data);
  },

  async updateAddress(id: number, address: Partial<Address>): Promise<Address> {
    const response = await $fetch<{ data: any }>(`/addresses/${id}`, {
      baseURL: apiBase(),
      method: "PUT",
      headers: getHeaders(),
      body: unmapAddress(address),
    });
    return mapAddress(response.data);
  },

  async deleteAddress(id: number): Promise<void> {
    await $fetch(`/addresses/${id}`, {
      baseURL: apiBase(),
      method: "DELETE",
      headers: getHeaders(),
    });
  },

  async getProvinces(): Promise<any[]> {
    const response = await $fetch<{ data: any[] }>("/shipping/provinces", {
      baseURL: apiBase(),
    });
    return response.data ?? [];
  },

  async getCities(provinceId: number): Promise<any[]> {
    const response = await $fetch<{ data: any[] }>("/shipping/cities", {
      baseURL: apiBase(),
      query: { province_id: provinceId },
    });
    return response.data ?? [];
  },

  async getDistricts(cityId: number): Promise<any[]> {
    const response = await $fetch<{ data: any[] }>("/shipping/districts", {
      baseURL: apiBase(),
      query: { city_id: cityId },
    });
    return response.data ?? [];
  },
};
