import { ref, getCurrentInstance, onMounted } from "vue";

const lastRefreshedMap = new Map<string, number>();

export function getApiBaseUrl(): string {
  try {
    if (typeof useRuntimeConfig === "function") {
      const config = useRuntimeConfig();
      if (config?.public?.apiBase) {
        return config.public.apiBase as string;
      }
    }
  } catch {
    // Fallback when executed outside Nuxt context (e.g. unit tests)
  }
  return "https://admin.pcgmitra.co.id/api";
}

export function safeUseFetch<T>(url: string | (() => string), opts?: any) {
  try {
    if (typeof useFetch === "function") {
      const fetchOpts = {
        getCachedData: (key: string, nuxtApp: any) =>
          nuxtApp?.isHydrating ? nuxtApp.payload?.data?.[key] : undefined,
        ...opts,
      };

      const res = useFetch<T>(url, fetchOpts);

      // On client side, revalidate in background after hydration (SWR pattern)
      if (typeof window !== "undefined" && opts?.clientRefresh !== false) {
        const triggerRefresh = () => {
          let rawKey = opts?.key;
          if (!rawKey) {
            try {
              rawKey = typeof url === "function" ? url() : String(url);
            } catch {
              rawKey = "api-fetch";
            }
          }

          const now = Date.now();
          const last = lastRefreshedMap.get(rawKey) || 0;
          if (now - last > 5000) {
            lastRefreshedMap.set(rawKey, now);
            try {
              const promise = res.refresh();
              if (promise && typeof promise.catch === "function") {
                promise.catch(() => {});
              }
            } catch {
              // Ignore background refresh failure to prevent UI crash
            }
          }
        };

        if (getCurrentInstance()) {
          onMounted(() => {
            triggerRefresh();
          });
        } else {
          setTimeout(triggerRefresh, 0);
        }
      }

      return res;
    }
  } catch {
    // Unit tests fallback
  }
  return {
    data: ref<T | null>(null),
    pending: ref(false),
    error: ref(null),
    refresh: () => Promise.resolve(),
  };
}

export function resolveImageUrl(imagePath?: string | null, fallback = "/images/homepage/About_Desktop.webp"): string {
  if (!imagePath) return fallback;
  if (imagePath.startsWith("http://") || imagePath.startsWith("https://")) {
    return imagePath;
  }
  if (imagePath.startsWith("/")) {
    return imagePath;
  }
  const apiBase = getApiBaseUrl();
  const origin = apiBase.replace(/\/api\/?$/, "");

  if (imagePath.startsWith("uploads/")) {
    return `${origin}/${imagePath}`;
  }
  if (imagePath.startsWith("storage/")) {
    return `${origin}/${imagePath}`;
  }

  return `${origin}/uploads/${imagePath}`;
}

export function formatDate(dateStr?: string | null, locale = "id"): string {
  if (!dateStr) return "";
  try {
    const date = new Date(dateStr);
    return date.toLocaleDateString(locale === "en" ? "en-US" : "id-ID", {
      day: "numeric",
      month: "short",
      year: "numeric",
    });
  } catch {
    return dateStr;
  }
}
