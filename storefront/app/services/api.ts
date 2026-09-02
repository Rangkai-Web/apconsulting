export const useApi = () => {
  const config = useRuntimeConfig();
  const apiBase = config.public.apiBase;
  const toast = useToast();

  const fetchApi = async <T>(path: string, options: any = {}): Promise<T> => {
    try {
      return await $fetch<T>(`${apiBase}${path}`, {
        ...options,
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          ...(options.headers || {}),
        },
      });
    } catch (error: any) {
      if (error?.status === 429 || error?.statusCode === 429) {
        toast.add({
          title: "Terlalu Banyak Permintaan",
          description: "Sistem menerima terlalu banyak permintaan dari perangkat Anda. Mohon tunggu beberapa detik.",
          color: "warning",
        });
      }
      throw error;
    }
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
