export const useApi = () => {
  const config = useRuntimeConfig();
  const apiBase = config.public.apiBase;

  const fetchApi = async <T>(path: string, options: any = {}): Promise<T> => {
    return await $fetch<T>(`${apiBase}${path}`, {
      ...options,
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        ...(options.headers || {}),
      },
    });
  };

  return {
    async getSettings() {
      return await fetchApi<any>("/settings");
    },
    async getBanners() {
      return await fetchApi<any>("/banners");
    },
    async getPaymentMethods() {
      return await fetchApi<any>("/payment-methods");
    },
  };
};
