import type { User, LoginCredentials, RegisterData, AuthResponse } from "~/types";

const MOCK_USERS_KEY = "dpi-mock-users";
const DEFAULT_MEMBER = {
  id: 999,
  name: "Member Doctor Plant",
  email: "member@drplant.id",
  phone: "081295040884",
  password: "password123", // used in mock comparison
};

// Types check/helper
function getMockUsers(): any[] {
  if (typeof window === "undefined") return [];
  try {
    const raw = localStorage.getItem(MOCK_USERS_KEY);
    if (!raw) {
      // Seed default user
      localStorage.setItem(MOCK_USERS_KEY, JSON.stringify([DEFAULT_MEMBER]));
      return [DEFAULT_MEMBER];
    }
    return JSON.parse(raw);
  } catch {
    return [DEFAULT_MEMBER];
  }
}

function saveMockUser(user: any) {
  if (typeof window === "undefined") return;
  const users = getMockUsers();
  users.push(user);
  localStorage.setItem(MOCK_USERS_KEY, JSON.stringify(users));
}

export const authService = {
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    const config = useRuntimeConfig();
    const apiBase = config.public.apiBase;

    try {
      // Attempt API login
      const response = await $fetch<AuthResponse>(`${apiBase}/login`, {
        method: "POST",
        body: credentials,
      });
      return response;
    } catch (error: any) {
      // If server is unreachable or offline, perform mock fallback
      const isConnectionError = !error.response || error.status === 503 || error.status === 504 || error.status === 500;
      if (isConnectionError) {
        console.warn("API Server offline. Falling back to mock authentication.");
        const users = getMockUsers();
        const found = users.find(
          (u) => u.email.toLowerCase() === credentials.email.toLowerCase()
        );

        if (found && found.password === credentials.password) {
          // Success mock response
          const { password, ...userProfile } = found;
          return {
            user: userProfile as User,
            token: "mock-jwt-token-xyz-12345",
          };
        }
        
        // Mock validation error
        throw {
          status: 422,
          data: {
            message: "Email atau password salah. (Mock Auth)",
          },
        };
      }
      throw error;
    }
  },

  async register(data: RegisterData): Promise<AuthResponse> {
    const config = useRuntimeConfig();
    const apiBase = config.public.apiBase;

    try {
      // Attempt API register
      const response = await $fetch<AuthResponse>(`${apiBase}/register`, {
        method: "POST",
        body: data,
      });
      return response;
    } catch (error: any) {
      const isConnectionError = !error.response || error.status === 503 || error.status === 504 || error.status === 500;
      if (isConnectionError) {
        console.warn("API Server offline. Falling back to mock registration.");
        const users = getMockUsers();
        const emailExists = users.some(
          (u) => u.email.toLowerCase() === data.email.toLowerCase()
        );

        if (emailExists) {
          throw {
            status: 422,
            data: {
              message: "Email sudah terdaftar. (Mock Auth)",
            },
          };
        }

        const newUser = {
          id: Math.floor(Math.random() * 1000) + 10,
          name: data.name,
          email: data.email,
          phone: data.phone || "",
          password: data.password,
        };

        saveMockUser(newUser);

        const { password, ...userProfile } = newUser;
        return {
          user: userProfile as User,
          token: "mock-jwt-token-xyz-12345",
        };
      }
      throw error;
    }
  },

  async logout(token: string): Promise<void> {
    const config = useRuntimeConfig();
    const apiBase = config.public.apiBase;

    if (token.startsWith("mock-")) {
      console.warn("Mock logout completed locally.");
      return;
    }

    try {
      await $fetch(`${apiBase}/logout`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });
    } catch (error) {
      console.error("Logout request failed:", error);
    }
  },
};
