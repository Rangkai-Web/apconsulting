<template>
  <footer
    class="relative bg-[#061F35] text-white px-5 sm:px-6 py-12 sm:py-14 lg:px-8 border-t border-[#123B5E]"
  >
    <div
      class="relative z-10 mx-auto grid max-w-[1240px] gap-10 md:gap-12 grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1.8fr_1fr_1fr_1.6fr]"
    >
      <!-- Column 1: Logo, Tagline, Quote & Socials -->
      <div data-reveal="fade">
        <NuxtLink
          to="/"
          class="group inline-flex items-center gap-3.5 focus:outline-none"
          aria-label="AP Consulting Homepage"
        >
          <div class="bg-white px-3 py-1.5 rounded-[2px] shadow-xs">
            <img
              :src="logoUrl"
              alt="AP Consulting Logo"
              loading="lazy"
              decoding="async"
              class="h-9 w-auto object-contain footer-logo"
            />
          </div>
          <span
            class="text-xs sm:text-sm font-semibold text-white leading-tight"
          >
            Tax &amp; Consulting<br />Services
          </span>
        </NuxtLink>

        <p
          class="mt-4 max-w-sm text-sm leading-relaxed text-[#B8CFE4] font-normal"
        >
          {{ t("footer.tagline") }}
        </p>

        <p class="mt-3 text-xs italic text-[#8FB2D1]">
          {{ t("footer.quote") }}
        </p>

        <div v-if="socials && socials.length > 0" class="mt-6 flex gap-3">
          <a
            v-for="social in socials"
            :key="social.name"
            :href="social.href"
            :aria-label="social.name"
            target="_blank"
            rel="noopener noreferrer"
            class="flex size-9 items-center justify-center rounded-[2px] border border-white/15 bg-white/5 text-white hover:bg-[#0869B2] hover:border-[#0869B2] transition-colors duration-200"
          >
            <UIcon :name="social.icon" class="size-4" />
          </a>
        </div>
      </div>

      <!-- Column 2: Quick Links -->
      <div>
        <p class="font-semibold text-white text-sm tracking-wide uppercase">
          {{ t("footer.quickLinks") }}
        </p>
        <ul class="mt-4 space-y-2.5 text-sm text-[#B8CFE4] font-normal">
          <li>
            <NuxtLink to="/" class="hover:text-white transition-colors">{{
              t("nav.home")
            }}</NuxtLink>
          </li>
          <li>
            <NuxtLink to="/about" class="hover:text-white transition-colors">{{
              t("nav.about")
            }}</NuxtLink>
          </li>
          <li>
            <NuxtLink
              to="/services"
              class="hover:text-white transition-colors"
              >{{ t("nav.services") }}</NuxtLink
            >
          </li>
          <li>
            <NuxtLink
              to="/insight"
              class="hover:text-white transition-colors"
              >{{ t("nav.insights") }}</NuxtLink
            >
          </li>
          <li>
            <NuxtLink
              to="/contact"
              class="hover:text-white transition-colors"
              >{{ t("nav.contact") }}</NuxtLink
            >
          </li>
        </ul>
      </div>

      <!-- Column 3: Services -->
      <div>
        <p class="font-semibold text-white text-sm tracking-wide uppercase">
          {{ t("footer.services") }}
        </p>
        <ul class="mt-4 space-y-2.5 text-sm text-[#B8CFE4] font-normal">
          <li v-for="cat in footerServices" :key="cat.id">
            <NuxtLink :to="cat.href" class="hover:text-white transition-colors">
              {{ cat.label }}
            </NuxtLink>
          </li>
        </ul>
      </div>

      <!-- Column 4: Reach Us (Head Office, Branch Office, Phone, Email) -->
      <div>
        <p class="font-semibold text-white text-sm tracking-wide uppercase">
          {{ t("footer.contactUs") }}
        </p>

        <!-- Head Office -->
        <div class="mt-4 flex gap-2.5 text-sm text-[#B8CFE4] font-normal">
          <UIcon
            name="i-heroicons-map-pin"
            class="mt-0.5 size-5 shrink-0 text-[#66B8FF]"
          />
          <div>
            <p
              class="text-[11px] font-bold tracking-[0.08em] uppercase text-white mb-0.5"
            >
              {{ t("footer.headOffice") }}
            </p>
            <a
              :href="`https://maps.google.com/?q=${encodeURIComponent(headOfficeAddress)}`"
              target="_blank"
              rel="noopener noreferrer"
              class="hover:underline hover:text-white transition-colors leading-relaxed"
            >
              {{ headOfficeAddress }}
            </a>
          </div>
        </div>

        <!-- Branch Office -->
        <div class="mt-3.5 flex gap-2.5 text-sm text-[#B8CFE4] font-normal">
          <UIcon
            name="i-heroicons-building-office-2"
            class="mt-0.5 size-5 shrink-0 text-[#66B8FF]"
          />
          <div>
            <p
              class="text-[11px] font-bold tracking-[0.08em] uppercase text-white mb-0.5"
            >
              {{ t("footer.branchOffice") }}
            </p>
            <a
              :href="`https://maps.google.com/?q=${encodeURIComponent(branchOfficeAddress)}`"
              target="_blank"
              rel="noopener noreferrer"
              class="hover:underline hover:text-white transition-colors leading-relaxed"
            >
              {{ branchOfficeAddress }}
            </a>
          </div>
        </div>

        <!-- Email -->
        <p
          class="mt-3.5 flex items-center gap-2.5 text-sm text-[#B8CFE4] font-normal"
        >
          <UIcon
            name="i-heroicons-envelope"
            class="size-5 shrink-0 text-[#66B8FF]"
          />
          <a
            :href="`mailto:${email}`"
            class="hover:underline hover:text-white transition-colors"
            >{{ email }}</a
          >
        </p>

        <!-- Phone / WhatsApp -->
        <p
          v-if="phone"
          class="mt-3 flex items-center gap-2.5 text-sm text-[#B8CFE4] font-normal"
        >
          <UIcon
            name="i-heroicons-phone"
            class="size-5 shrink-0 text-[#66B8FF]"
          />
          <a
            :href="getWhatsappUrl()"
            target="_blank"
            rel="noopener noreferrer"
            class="hover:underline hover:text-white transition-colors"
          >
            {{ phone }}
          </a>
        </p>
      </div>
    </div>

    <!-- Licensed Tax Consultants Credential Bar (from Client v3 + Official SIKOP Kemenkeu Data) -->
    <div
      class="relative z-20 mx-auto mt-12 flex max-w-[1240px] flex-wrap items-center justify-between gap-4 border-t border-white/15 py-5 text-xs sm:text-[13px] text-[#B8CFE4]"
    >
      <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5">
        <span class="inline-flex items-center gap-2 font-semibold text-white">
          <svg
            aria-hidden="true"
            width="19"
            height="19"
            viewBox="0 0 24 24"
            fill="none"
            stroke="#8EC5F2"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="shrink-0"
          >
            <path d="M12 3l7 3v5c0 4.6-3 8.4-7 10-4-1.6-7-5.4-7-10V6z" />
            <path d="M9 12l2 2 4-4" />
          </svg>
          {{ t("footer.licensedConsultants") }}
        </span>
        <span aria-hidden="true" class="hidden sm:inline text-white/30">·</span>
        <span>{{ t("footer.ikpiMember") }}</span>
        <span aria-hidden="true" class="hidden sm:inline text-white/30">·</span>
        <span>
          {{ t("footer.practiceLicensePrefix") }}
          <strong class="font-semibold text-white"
            >KIP-6592/IP.B/PJ/2020</strong
          >
          (Andry Phangkawira)
        </span>
      </div>

      <a
        href="https://sikop.kemenkeu.go.id/front/carikonsultan"
        target="_blank"
        rel="noopener noreferrer"
        title="Verify Andry Phangkawira (KIP-6592/IP.B/PJ/2020) on SIKOP Kemenkeu"
        class="inline-flex items-center gap-1.5 font-semibold text-[#8EC5F2] hover:text-white transition-colors"
        @click="copyConsultantName"
      >
        <span>{{
          copiedLicense ? t("footer.copiedLicense") : t("footer.viewLicenses")
        }}</span>
        <span aria-hidden="true">→</span>
      </a>
    </div>

    <!-- Bottom Copyright & Language Switcher Bar -->
    <div
      class="relative z-20 mx-auto flex max-w-[1240px] flex-col gap-4 border-t border-white/15 pt-6 text-xs text-[#8FB2D1] font-normal sm:flex-row sm:items-center sm:justify-between"
    >
      <p>
        Copyright © {{ new Date().getFullYear() }} AP Consulting.
        {{ t("footer.rights") }}
      </p>

      <div class="flex items-center gap-3">
        <span class="text-xs text-[#8FB2D1]">{{ t("footer.language") }}</span>
        <LanguageSwitcher direction="up" variant="dark" />
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { useLanguage } from "~/composables/useLanguage";
import { useCompanySettings } from "~/composables/useCompanySettings";

const { t, currentLocale } = useLanguage();
const {
  logoUrl,
  email,
  phone,
  headOfficeAddress,
  branchOfficeAddress,
  socials,
  getWhatsappUrl,
} = useCompanySettings();

const copiedLicense = ref(false);
function copyConsultantName() {
  if (typeof navigator !== "undefined" && navigator.clipboard) {
    navigator.clipboard.writeText("Andry Phangkawira").catch(() => {});
    copiedLicense.value = true;
    setTimeout(() => {
      copiedLicense.value = false;
    }, 3000);
  }
}

const footerServices = computed(() => {
  const isEn = currentLocale.value === "en";
  return [
    {
      id: "tax-services",
      label: isEn ? "Tax Services" : "Layanan Perpajakan",
      href: "/services#tax",
    },
    {
      id: "payroll-outsourcing",
      label: isEn
        ? "Payroll Outsourcing Services"
        : "Layanan Payroll Outsourcing",
      href: "/services#payroll",
    },
    {
      id: "tax-accounting",
      label: isEn ? "Tax Accounting Services" : "Layanan Akuntansi Pajak",
      href: "/services#accounting",
    },
  ];
});
</script>
