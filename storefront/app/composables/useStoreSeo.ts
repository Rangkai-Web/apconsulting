import { useApi } from "~/services/api";

export function useStoreSeo() {
  const api = useApi();
  // Same key as AppFooter's settings fetch — Nuxt dedupes it, no extra request.
  const { data } = useAsyncData("store-settings", () => api.getSettings());
  const settings = computed(() => data.value?.data ?? null);

  return { settings };
}
