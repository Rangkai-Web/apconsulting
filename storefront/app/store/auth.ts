import { defineStore } from "pinia";
import { authService } from "~/services/authService";
import type { User, LoginCredentials, RegisterData } from "~/types";

const USER_KEY = "dpi-auth-user";
const TOKEN_KEY = "dpi-auth-token";

function getStoredUser(): User | null {
  if (typeof window === "undefined") return null;
  try {
    const raw = localStorage.getItem(USER_KEY);
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
}

function getStoredToken(): string | null {
  if (typeof window === "undefined") return null;
  try {
    return localStorage.getItem(TOKEN_KEY);
  } catch {
    return null;
  }
}

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: getStoredUser() as User | null,
    token: getStoredToken() as string | null,
  }),

  getters: {
    isLoggedIn: (state) => !!state.token && !!state.user,
  },

  actions: {
    async login(credentials: LoginCredentials) {
      try {
        const response = await authService.login(credentials);
        this.user = response.user;
        this.token = response.token;
        this.persist();
        return response.user;
      } catch (error) {
        this.clear();
        throw error;
      }
    },

    async register(data: RegisterData) {
      try {
        const response = await authService.register(data);
        this.user = response.user;
        this.token = response.token;
        this.persist();
        return response.user;
      } catch (error) {
        this.clear();
        throw error;
      }
    },

    async logout() {
      try {
        if (this.token) {
          await authService.logout(this.token);
        }
      } finally {
        this.clear();
      }
    },

    updateProfile(data: { name: string; phone?: string | null }) {
      if (this.user) {
        this.user.name = data.name;
        this.user.phone = data.phone ?? null;
        this.persist();
      }
    },

    persist() {
      if (typeof window === "undefined") return;
      if (this.user && this.token) {
        localStorage.setItem(USER_KEY, JSON.stringify(this.user));
        localStorage.setItem(TOKEN_KEY, this.token);
      } else {
        this.clear();
      }
    },

    clear() {
      this.user = null;
      this.token = null;
      if (typeof window === "undefined") return;
      localStorage.removeItem(USER_KEY);
      localStorage.removeItem(TOKEN_KEY);
    },
  },
});
