import { ref, computed, watch } from "vue";
import { getApiBaseUrl, safeUseFetch } from "~/utils/api";
import { useLanguage } from "~/composables/useLanguage";

export interface ServiceCategoryItem {
  id: number;
  name_id: string;
  name_en: string;
  sort_order: number;
  is_active: boolean;
}

const DEFAULT_CATEGORIES: ServiceCategoryItem[] = [
  { id: 1, name_id: "Layanan Perpajakan", name_en: "Taxation Services", sort_order: 1, is_active: true },
  { id: 2, name_id: "Akuntansi & Pembukuan", name_en: "Accounting & Bookkeeping", sort_order: 2, is_active: true },
  { id: 3, name_id: "Dukungan Administrasi Bisnis", name_en: "Business Administration Support", sort_order: 3, is_active: true },
  { id: 4, name_id: "Layanan Keuangan Lainnya", name_en: "Other Financial Services", sort_order: 4, is_active: true },
  { id: 5, name_id: "Pertanyaan Umum", name_en: "General Inquiry", sort_order: 5, is_active: true },
];

let isFetching = false;
const categoriesState = ref<ServiceCategoryItem[]>(DEFAULT_CATEGORIES);
const pendingState = ref(false);
const errorState = ref<any>(null);

export function useServiceCategories() {
  const apiBase = getApiBaseUrl();
  const { currentLocale } = useLanguage();

  if (!isFetching) {
    isFetching = true;
    pendingState.value = true;

    const { data: apiResponse, pending, error } = safeUseFetch<{ data: ServiceCategoryItem[] }>(
      `${apiBase}/service-categories`,
      {
        key: "service-categories",
        lazy: true,
        getCachedData: () => undefined,
      }
    );

    if (apiResponse) {
      watch(
        apiResponse,
        (newVal) => {
          if (newVal?.data && Array.isArray(newVal.data) && newVal.data.length > 0) {
            categoriesState.value = newVal.data;
          }
        },
        { immediate: true }
      );
    }

    if (pending) {
      watch(
        pending,
        (val) => {
          pendingState.value = val;
        },
        { immediate: true }
      );
    }

    if (error) {
      watch(
        error,
        (val) => {
          errorState.value = val;
        },
        { immediate: true }
      );
    }
  }

  const categories = computed(() => categoriesState.value);

  const formattedCategories = computed(() => {
    const locale = currentLocale?.value || "id";
    return categories.value.map((cat) => ({
      value: cat.name_en, // Store name_en in backend inquiry
      label: locale === "en" ? cat.name_en : cat.name_id,
    }));
  });

  return {
    categories,
    formattedCategories,
    pending: pendingState,
    error: errorState,
  };
}
