<template>
  <section id="scope-of-support" class="px-4 py-16 md:px-8 md:py-24 bg-white">
    <div class="mx-auto max-w-4xl text-center">
      <p class="text-lg font-semibold uppercase tracking-wide text-neutral-400">
        {{ t("services.scope.tag") }}
      </p>
      <h2
        class="mt-4 text-3xl font-normal text-[#083653] sm:text-4xl md:text-[48px] leading-[1.2]"
      >
        <span class="font-sans font-bold italic text-[#083653]"
          >{{ t("services.scope.titleItalic") }}
        </span>
        <span class="font-sans font-normal text-[#083653]">{{
          t("services.scope.titleNormal")
        }}</span>
      </h2>
      <p
        class="mt-4 max-w-2xl mx-auto text-sm text-neutral-500 leading-relaxed md:text-base"
      >
        {{ t("services.scope.description") }}
      </p>
    </div>

    <div class="mx-auto mt-12 max-w-6xl space-y-6">
      <div
        v-for="service in dynamicServices"
        :key="service.id || service.number"
        class="p-6 md:p-[42px] border border-[#0000001F] rounded-none shadow-xs grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-center"
        style="
          background: radial-gradient(
            49.65% 56.63% at 97.41% 3.65%,
            #fffae3 0%,
            #ffffff 100%
          );
        "
      >
        <!-- Left Side: Service Info -->
        <div class="lg:col-span-7 flex items-start gap-4 md:gap-6">
          <span
            class="text-3xl md:text-[44px] font-semibold italic text-[#083653] leading-none shrink-0 pt-1"
          >
            {{ service.number }}
          </span>
          <div class="flex flex-col items-start">
            <h3
              class="text-xl md:text-[24px] font-semibold text-[#083653] leading-snug"
            >
              {{ service.title }}
            </h3>
            <p
              class="mt-2 text-sm md:text-base text-neutral-500 leading-relaxed font-normal max-w-lg"
            >
              {{ service.desc }}
            </p>
            <NuxtLink
              to="/contact"
              class="mt-4 inline-flex items-center text-xs md:text-sm font-semibold uppercase tracking-wider text-[#083653] hover:text-[#083653]/80 group"
            >
              <span>{{ service.cta }}</span>
              <UIcon
                name="i-heroicons-arrow-right"
                class="ml-1.5 size-4 text-[#083653] transition-transform group-hover:translate-x-1"
              />
            </NuxtLink>
          </div>
        </div>

        <!-- Right Side: Features Checklist -->
        <div class="lg:col-span-5">
          <ul class="space-y-2.5">
            <li
              v-for="(feature, idx) in service.features"
              :key="idx"
              class="flex items-center gap-3 text-sm md:text-base font-normal text-neutral-600"
            >
              <img
                src="/images/services/akar-icons_circle-check-fill.svg"
                alt=""
                class="size-5 shrink-0"
              />
              <span>{{ feature }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useLanguage } from "~/composables/useLanguage";
import { useServiceCategories } from "~/composables/useServiceCategories";

const { t, currentLocale } = useLanguage();
const { categories } = useServiceCategories();

const FALLBACK_DATA: Record<number, {
  desc_id: string;
  desc_en: string;
  cta_id: string;
  cta_en: string;
  features_id: string[];
  features_en: string[];
}> = {
  1: {
    desc_id: "Kami mendampingi pemenuhan kewajiban pajak Anda melalui proses yang lebih terstruktur dan panduan yang mudah diterapkan.",
    desc_en: "Support your tax responsibilities with a more structured process and practical guidance.",
    cta_id: "DISKUSIKAN LAYANAN PAJAK",
    cta_en: "DISCUSS TAX SUPPORT",
    features_id: ["Konsultasi perpajakan", "Pelaporan pajak bulanan dan tahunan", "Peninjauan kepatuhan pajak", "Perencanaan pajak", "Pendampingan pemeriksaan pajak"],
    features_en: ["Tax consultation", "Monthly and annual tax reporting", "Tax compliance review", "Tax planning", "Tax audit assistance"],
  },
  2: {
    desc_id: "Bangun catatan keuangan terpercaya yang memberikan gambaran lebih jelas tentang operasional harian bisnis Anda.",
    desc_en: "Build reliable financial records that give your business a clearer view of its day-to-day operations.",
    cta_id: "DISKUSIKAN DUKUNGAN AKUNTANSI",
    cta_en: "DISCUSS ACCOUNTING SUPPORT",
    features_id: ["Pencatatan transaksi keuangan", "Laporan keuangan bulanan", "Laporan keuangan tahunan", "Rekonsiliasi bank", "Penyesuaian jurnal akuntansi"],
    features_en: ["Financial transaction recording", "Monthly financial statements", "Annual financial statements", "Bank reconciliation", "Accounting journal adjustments"],
  },
  3: {
    desc_id: "Dapatkan dukungan fleksibel untuk pekerjaan administratif yang membantu bisnis Anda berjalan lebih terorganisir.",
    desc_en: "Get flexible support for administrative work that helps your business run in a more organized way.",
    cta_id: "DISKUSIKAN DUKUNGAN BISNIS",
    cta_en: "DISCUSS BUSINESS SUPPORT",
    features_id: ["Administrasi perusahaan", "Koordinasi dokumen", "Tugas-tugas administratif", "Dukungan kustom"],
    features_en: ["Corporate administration", "Document coordination", "Administrative tasks", "Customized support"],
  },
  4: {
    desc_id: "Ubah informasi keuangan menjadi wawasan berguna untuk perencanaan, pengendalian, dan keputusan yang lebih baik.",
    desc_en: "Turn financial information into useful insight for planning, control, and better decisions.",
    cta_id: "DISKUSIKAN DUKUNGAN KEUANGAN",
    cta_en: "DISCUSS FINANCIAL SUPPORT",
    features_id: ["Analisis laporan keuangan", "Proyeksi keuangan", "Manajemen arus kas", "Penganggaran perusahaan", "Evaluasi kinerja keuangan"],
    features_en: ["Financial statement analysis", "Financial projections", "Cash-flow management", "Corporate budgeting", "Financial performance evaluation"],
  },
};

function parseFeatures(val: any): string[] {
  if (!val) return [];
  if (Array.isArray(val)) {
    return val.map((s) => String(s).trim()).filter(Boolean);
  }
  if (typeof val === "string" && val.trim() !== "") {
    try {
      const parsed = JSON.parse(val);
      if (Array.isArray(parsed)) {
        return parsed.map((s) => String(s).trim()).filter(Boolean);
      }
    } catch {
      return val.split(",").map((s) => s.trim()).filter(Boolean);
    }
  }
  return [];
}

const dynamicServices = computed(() => {
  const isEn = currentLocale.value === "en";

  return categories.value
    .filter((cat) => {
      if (!cat.is_active) return false;
      const nameEn = (cat.name_en || "").toLowerCase();
      const nameId = (cat.name_id || "").toLowerCase();
      return nameEn !== "general inquiry" && nameId !== "pertanyaan umum";
    })
    .map((cat, index) => {
      const numStr = String(index + 1).padStart(2, "0");
      const fallback = (FALLBACK_DATA[cat.id] || FALLBACK_DATA[index + 1] || FALLBACK_DATA[1])!;

      const title = isEn ? cat.name_en || cat.name_id : cat.name_id || cat.name_en;
      
      const desc = isEn
        ? cat.description_en || fallback.desc_en || cat.description_id || fallback.desc_id
        : cat.description_id || fallback.desc_id || cat.description_en || fallback.desc_en;

      const cta = isEn
        ? cat.cta_text_en || fallback.cta_en || cat.cta_text_id || fallback.cta_id
        : cat.cta_text_id || fallback.cta_id || cat.cta_text_en || fallback.cta_en;
      
      const parsedCatEn = parseFeatures(cat.features_en);
      const parsedCatId = parseFeatures(cat.features_id);

      const rawFeatures = isEn
        ? parsedCatEn.length > 0
          ? parsedCatEn
          : parsedCatId.length > 0
            ? parsedCatId
            : fallback.features_en
        : parsedCatId.length > 0
          ? parsedCatId
          : parsedCatEn.length > 0
            ? parsedCatEn
            : fallback.features_id;

      const features = parseFeatures(rawFeatures);

      return {
        id: cat.id,
        number: numStr,
        title,
        desc,
        cta,
        features,
      };
    });
});
</script>
