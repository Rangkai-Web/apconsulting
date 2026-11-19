import { computed } from "vue";
import { getApiBaseUrl, safeUseFetch } from "~/utils/api";
import { useLanguage } from "~/composables/useLanguage";

export interface ServiceCategoryItem {
  id: number;
  name_id: string;
  name_en: string;
  description_id?: string;
  description_en?: string;
  cta_text_id?: string;
  cta_text_en?: string;
  features_id?: string[];
  features_en?: string[];
  sort_order: number;
  is_active: boolean;
}

export function useServiceCategories() {
  const apiBase = getApiBaseUrl();
  const { currentLocale } = useLanguage();

  const { data: apiResponse, pending, error } = safeUseFetch<{ data: ServiceCategoryItem[] }>(
    `${apiBase}/service-categories`,
    {
      key: "service-categories-api-v4",
    }
  );

  const categories = computed<ServiceCategoryItem[]>(() => {
    return apiResponse.value?.data || [];
  });

  const formattedCategories = computed(() => {
    const locale = currentLocale?.value || "id";
    return categories.value.map((cat) => ({
      value: cat.name_en,
      label: locale === "en" ? cat.name_en : cat.name_id,
    }));
  });

  return {
    categories,
    formattedCategories,
    pending,
    error,
  };
}
