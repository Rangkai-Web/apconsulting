import { ref } from "vue";

export function getApiBaseUrl(): string {
  try {
    if (typeof useRuntimeConfig === "function") {
      const config = useRuntimeConfig();
      const configuredBase = config?.public?.apiBase as string | undefined;

      // Auto-detect local development in browser
      if (
        typeof window !== "undefined" &&
        (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1")
      ) {
        if (configuredBase && (configuredBase.includes("localhost") || configuredBase.includes("127.0.0.1"))) {
          return configuredBase;
        }
        return "http://localhost:8000/api";
      }

      if (configuredBase) {
        return configuredBase;
      }
    }
  } catch {
    // Fallback when executed outside Nuxt context (e.g. unit tests)
  }

  // Fallback for browser on localhost outside runtime config
  if (
    typeof window !== "undefined" &&
    (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1")
  ) {
    return "http://localhost:8000/api";
  }

  return "https://admin.apconsulting.id/api";
}

export function safeUseFetch<T>(url: string | (() => string), opts?: any) {
  try {
    if (typeof useFetch === "function") {
      return useFetch<T>(url, {
        dedupe: "defer",
        ...opts,
      });
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
  if (!imagePath || !imagePath.trim() || imagePath === "null" || imagePath === "undefined") {
    return fallback;
  }
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
