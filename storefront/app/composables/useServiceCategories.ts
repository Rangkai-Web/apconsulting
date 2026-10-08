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

const DEFAULT_CATEGORIES: ServiceCategoryItem[] = [
  {
    id: 1,
    name_id: "Layanan Perpajakan",
    name_en: "Taxation Services",
    description_id: "Kami mendampingi pemenuhan kewajiban pajak Anda melalui proses yang lebih terstruktur dan panduan yang mudah diterapkan.",
    description_en: "Support your tax responsibilities with a more structured process and practical guidance.",
    cta_text_id: "DISKUSIKAN LAYANAN PAJAK",
    cta_text_en: "DISCUSS TAX SUPPORT",
    features_id: ["Konsultasi perpajakan", "Pelaporan pajak bulanan dan tahunan", "Peninjauan kepatuhan pajak", "Perencanaan pajak", "Pendampingan pemeriksaan pajak"],
    features_en: ["Tax consultation", "Monthly and annual tax reporting", "Tax compliance review", "Tax planning", "Tax audit assistance"],
    sort_order: 1,
    is_active: true,
  },
  {
    id: 2,
    name_id: "Akuntansi & Pembukuan",
    name_en: "Accounting & Bookkeeping",
    description_id: "Bangun catatan keuangan terpercaya yang memberikan gambaran lebih jelas tentang operasional harian bisnis Anda.",
    description_en: "Build reliable financial records that give your business a clearer view of its day-to-day operations.",
    cta_text_id: "DISKUSIKAN DUKUNGAN AKUNTANSI",
    cta_text_en: "DISCUSS ACCOUNTING SUPPORT",
    features_id: ["Pencatatan transaksi keuangan", "Laporan keuangan bulanan", "Laporan keuangan tahunan", "Rekonsiliasi bank", "Penyesuaian jurnal akuntansi"],
    features_en: ["Financial transaction recording", "Monthly financial statements", "Annual financial statements", "Bank reconciliation", "Accounting journal adjustments"],
    sort_order: 2,
    is_active: true,
  },
  {
    id: 3,
    name_id: "Dukungan Administrasi Bisnis",
    name_en: "Business Administration Support",
    description_id: "Dapatkan dukungan fleksibel untuk pekerjaan administratif yang membantu bisnis Anda berjalan lebih terorganisir.",
    description_en: "Get flexible support for administrative work that helps your business run in a more organized way.",
    cta_text_id: "DISKUSIKAN DUKUNGAN BISNIS",
    cta_text_en: "DISCUSS BUSINESS SUPPORT",
    features_id: ["Administrasi perusahaan", "Koordinasi dokumen", "Tugas-tugas administratif", "Dukungan kustom"],
    features_en: ["Corporate administration", "Document coordination", "Administrative tasks", "Customized support"],
    sort_order: 3,
    is_active: true,
  },
  {
    id: 4,
    name_id: "Layanan Keuangan Lainnya",
    name_en: "Other Financial Services",
    description_id: "Ubah informasi keuangan menjadi wawasan berguna untuk perencanaan, pengendalian, dan keputusan yang lebih baik.",
    description_en: "Turn financial information into useful insight for planning, control, and better decisions.",
    cta_text_id: "DISKUSIKAN DUKUNGAN KEUANGAN",
    cta_text_en: "DISCUSS FINANCIAL SUPPORT",
    features_id: ["Analisis laporan keuangan", "Proyeksi keuangan", "Manajemen arus kas", "Penganggaran perusahaan", "Evaluasi kinerja keuangan"],
    features_en: ["Financial statement analysis", "Financial projections", "Cash-flow management", "Corporate budgeting", "Financial performance evaluation"],
    sort_order: 4,
    is_active: true,
  },
];

export function useServiceCategories() {
  const apiBase = getApiBaseUrl();
  const { currentLocale } = useLanguage();

  const { data: apiResponse, pending, error } = safeUseFetch<{ data: ServiceCategoryItem[] }>(
    `${apiBase}/service-categories`,
    {
      key: "service-categories-api-v3",
    }
  );

  const categories = computed<ServiceCategoryItem[]>(() => {
    if (apiResponse.value?.data && Array.isArray(apiResponse.value.data) && apiResponse.value.data.length > 0) {
      return apiResponse.value.data;
    }
    return DEFAULT_CATEGORIES;
  });

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
    pending,
    error,
  };
}

