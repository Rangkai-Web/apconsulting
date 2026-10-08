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

      const title = isEn ? cat.name_en || cat.name_id : cat.name_id || cat.name_en;

      const desc = isEn
        ? cat.description_en || cat.description_id || ""
        : cat.description_id || cat.description_en || "";

      const cta = isEn
        ? cat.cta_text_en || cat.cta_text_id || `DISCUSS ${title.toUpperCase()}`
        : cat.cta_text_id || cat.cta_text_en || `DISKUSIKAN ${title.toUpperCase()}`;

      const rawFeatures = isEn
        ? parseFeatures(cat.features_en).length > 0
          ? cat.features_en
          : cat.features_id
        : parseFeatures(cat.features_id).length > 0
          ? cat.features_id
          : cat.features_en;

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
