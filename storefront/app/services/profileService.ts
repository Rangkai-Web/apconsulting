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

export interface ProfileData {
  id: number;
  name: string;
  email: string;
  phone: string | null;
}

export const profileService = {
  async getProfile(): Promise<ProfileData> {
    const response = await $fetch<{ data: ProfileData }>("/profile", {
      baseURL: apiBase(),
      headers: getHeaders(),
    });
    return response.data;
  },

  async updateProfile(data: {
    name: string;
    phone?: string | null;
  }): Promise<ProfileData> {
    const response = await $fetch<{ data: ProfileData }>("/profile", {
      baseURL: apiBase(),
      method: "PUT",
      headers: getHeaders(),
      body: data,
    });
    return response.data;
  },

  async updatePassword(data: {
    current_password: string;
    password: string;
    password_confirmation: string;
  }): Promise<void> {
    await $fetch("/profile/password", {
      baseURL: apiBase(),
      method: "PUT",
      headers: getHeaders(),
      body: data,
    });
  },
};
