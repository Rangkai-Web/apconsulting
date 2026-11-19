<template>
  <section id="consultation-form" class="bg-white pt-10 pb-14 sm:pt-14 sm:pb-20 border-b border-[#DCE8F5]">
    <div class="mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <!-- Page Header -->
      <div class="mb-10 border-b border-[#DCE8F5] pb-8 sm:mb-12 sm:pb-10">
        <div class="grid grid-cols-1 items-end gap-6 lg:grid-cols-12 lg:gap-10">
          <div class="lg:col-span-7">
            <p class="animate-hero-badge mb-2 text-[11px] font-bold uppercase tracking-[0.14em] text-[#0869B2]">
              {{ t("contact.hero.tag") }}
            </p>
            <h1
              class="animate-hero-title text-[30px] font-semibold leading-[1.16] tracking-[-0.03em] text-[#092F50] sm:text-4xl lg:text-[46px]"
            >
              <span class="font-bold">{{ t("contact.hero.titleLight") }}</span>
              {{ " " }}
              <span class="font-bold italic text-[#0869B2]">{{ t("contact.hero.titleItalic") }}</span>
            </h1>
          </div>

          <div class="animate-hero-desc lg:col-span-5 lg:pb-1">
            <p class="text-[15px] font-bold text-[#092F50] sm:text-[16px]">
              {{ t("contact.hero.headerSubtitle") }}
            </p>
            <p class="mt-2 text-[14px] leading-[1.65] text-[#52687B] sm:text-[15px]">
              {{ t("contact.hero.headerDesc") }}
            </p>
          </div>
        </div>
      </div>

      <!-- 2-Column Contact Layout -->
      <div class="grid grid-cols-1 items-stretch gap-8 lg:grid-cols-12 lg:gap-10">
        <!-- Left Column: Direct Contact & Office Directory -->
        <div class="lg:col-span-5" data-reveal="up" data-reveal-delay="100">
          <div
            class="flex h-full flex-col justify-between overflow-hidden rounded-[2px] border border-[#0D3D66] bg-[#072642] text-white"
          >
            <!-- Clean Office Photo Header -->
            <div class="relative h-44 w-full overflow-hidden border-b border-white/10 sm:h-48">
              <img
                src="/images/redesign/office.webp"
                alt="AP Consulting Office"
                class="h-full w-full object-cover"
                loading="eager"
              />
              <div
                class="absolute inset-0 bg-gradient-to-t from-[#072642] via-[#072642]/30 to-transparent"
                aria-hidden="true"
              ></div>
            </div>

            <div class="flex flex-1 flex-col justify-between p-6 sm:p-8">
              <div class="space-y-6">
                <!-- Reach Us Headline & WhatsApp CTA -->
                <div>
                  <h2 class="text-[22px] font-bold leading-[1.25] text-white sm:text-[25px]">
                    {{ t("contact.hero.cardTitle") }}
                  </h2>
                  <p class="mt-2 text-[14px] leading-[1.65] text-[#D7E6F5]">
                    {{ t("contact.hero.cardDesc") }}
                  </p>

                  <a
                    :href="whatsappHref"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-5 inline-flex w-full items-center justify-center gap-2.5 rounded-[2px] bg-[#0869B2] px-5 py-3.5 text-[14px] font-bold text-white transition-colors hover:bg-[#0B7ACC]"
                  >
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                      <path
                        d="M12.031 2c-5.516 0-9.969 4.453-9.969 9.969 0 1.766.469 3.484 1.344 5l-1.406 5.156 5.281-1.375c1.469.797 3.109 1.219 4.75 1.219 5.516 0 9.969-4.453 9.969-9.969S17.547 2 12.031 2zm5.813 14.125c-.25.703-1.438 1.344-2 1.422-.516.078-1.172.109-1.906-.125-.453-.141-1.031-.328-1.781-.656-3.125-1.344-5.156-4.516-5.313-4.719-.156-.219-1.266-1.688-1.266-3.219s.797-2.281 1.078-2.594c.281-.313.625-.391.828-.391.203 0 .422.016.609.016.188 0 .453-.078.703.531.266.625.891 2.172.969 2.328.078.156.125.344.031.547-.109.203-.156.328-.313.516-.156.188-.328.406-.469.547-.156.156-.328.328-.141.656.188.328.844 1.391 1.813 2.25 1.25 1.109 2.297 1.453 2.625 1.609.328.156.516.125.703-.094.188-.219.813-.953 1.031-1.281.219-.328.438-.266.734-.156.297.109 1.875.891 2.203 1.047.328.156.547.234.625.375.078.141.078.813-.172 1.516z"
                      />
                    </svg>
                    <span>{{ t("contact.hero.whatsappCta") }}</span>
                  </a>
                </div>

                <!-- Direct Channels -->
                <div class="grid grid-cols-1 gap-4 border-t border-white/15 pt-6 sm:grid-cols-2 lg:grid-cols-1">
                  <div class="flex items-start gap-3">
                    <div
                      class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-[2px] border border-white/15 bg-white/10 text-[#93C5FD]"
                    >
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                        />
                      </svg>
                    </div>
                    <div class="min-w-0">
                      <div class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#93C5FD]">
                        {{ t("contact.hero.emailLabel") }}
                      </div>
                      <a
                        :href="`mailto:${contactEmail}`"
                        class="mt-0.5 block truncate text-[14px] font-medium text-white hover:underline"
                      >
                        {{ contactEmail }}
                      </a>
                    </div>
                  </div>

                  <div class="flex items-start gap-3">
                    <div
                      class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-[2px] border border-white/15 bg-white/10 text-[#93C5FD]"
                    >
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                        />
                      </svg>
                    </div>
                    <div class="min-w-0">
                      <div class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#93C5FD]">
                        {{ t("contact.hero.phoneLabel") }}
                      </div>
                      <a
                        :href="whatsappHref"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-0.5 block text-[14px] font-medium text-white hover:underline"
                      >
                        {{ contactPhone }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Office Addresses -->
              <div class="mt-6 space-y-4 border-t border-white/15 pt-6">
                <div>
                  <div class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#93C5FD]">
                    {{ t("footer.headOffice") }}
                  </div>
                  <p class="mt-1 text-[13px] leading-[1.6] text-[#E2EDF7]">
                    {{ headOfficeAddress }}
                  </p>
                </div>
                <div v-if="branchOfficeAddress">
                  <div class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#93C5FD]">
                    {{ t("footer.branchOffice") }}
                  </div>
                  <p class="mt-1 text-[13px] leading-[1.6] text-[#E2EDF7]">
                    {{ branchOfficeAddress }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Consultation Inquiry Form -->
        <div class="lg:col-span-7" data-reveal="up" data-reveal-delay="150">
          <div class="h-full rounded-[2px] border border-[#DCE8F5] bg-[#F4F9FE] p-6 sm:p-9">
            <div class="mb-7 border-b border-[#DCE8F5] pb-5">
              <h2 class="text-[22px] font-bold text-[#092F50] sm:text-[26px]">
                {{ t("contact.form.title") }}
              </h2>
              <p class="mt-1.5 text-[14px] text-[#52687B]">
                {{ t("contact.form.subtitle") }}
              </p>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-5" novalidate>
              <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <!-- Full Name -->
                <div>
                  <label
                    for="fullName"
                    class="mb-1.5 block text-[13px] font-semibold text-[#092F50]"
                  >
                    {{ t("contact.form.fullName") }} <span class="text-[#0869B2]">*</span>
                  </label>
                  <input
                    id="fullName"
                    v-model="form.fullName"
                    type="text"
                    required
                    :placeholder="t('contact.form.fullNamePlaceholder')"
                    :class="[
                      'w-full rounded-[2px] border bg-white px-4 py-3 text-[14px] text-[#092F50] placeholder-[#8AA0B4] transition-colors focus:outline-none',
                      errors.fullName
                        ? 'border-red-500 focus:border-red-500'
                        : 'border-[#DCE8F5] focus:border-[#0869B2]',
                    ]"
                    @blur="validateField('fullName')"
                    @input="errors.fullName = ''"
                  />
                  <p v-if="errors.fullName" class="mt-1.5 text-[12px] text-red-600">
                    {{ errors.fullName }}
                  </p>
                </div>

                <!-- Email -->
                <div>
                  <label
                    for="email"
                    class="mb-1.5 block text-[13px] font-semibold text-[#092F50]"
                  >
                    {{ t("contact.form.email") }} <span class="text-[#0869B2]">*</span>
                  </label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    :placeholder="t('contact.form.emailPlaceholder')"
                    :class="[
                      'w-full rounded-[2px] border bg-white px-4 py-3 text-[14px] text-[#092F50] placeholder-[#8AA0B4] transition-colors focus:outline-none',
                      errors.email
                        ? 'border-red-500 focus:border-red-500'
                        : 'border-[#DCE8F5] focus:border-[#0869B2]',
                    ]"
                    @blur="validateField('email')"
                    @input="errors.email = ''"
                  />
                  <p v-if="errors.email" class="mt-1.5 text-[12px] text-red-600">
                    {{ errors.email }}
                  </p>
                </div>
              </div>

              <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <!-- Phone -->
                <div>
                  <label
                    for="phone"
                    class="mb-1.5 block text-[13px] font-semibold text-[#092F50]"
                  >
                    {{ t("contact.form.phone") }}
                  </label>
                  <input
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    placeholder="+62 8xx-xxxx-xxxx"
                    class="w-full rounded-[2px] border border-[#DCE8F5] bg-white px-4 py-3 text-[14px] text-[#092F50] placeholder-[#8AA0B4] transition-colors focus:border-[#0869B2] focus:outline-none"
                  />
                </div>

                <!-- Company -->
                <div>
                  <label
                    for="company"
                    class="mb-1.5 block text-[13px] font-semibold text-[#092F50]"
                  >
                    {{ t("contact.form.company") }}
                  </label>
                  <input
                    id="company"
                    v-model="form.company"
                    type="text"
                    :placeholder="t('contact.form.companyPlaceholder')"
                    class="w-full rounded-[2px] border border-[#DCE8F5] bg-white px-4 py-3 text-[14px] text-[#092F50] placeholder-[#8AA0B4] transition-colors focus:border-[#0869B2] focus:outline-none"
                  />
                </div>
              </div>

              <!-- Service Interest -->
              <div>
                <label
                  for="service"
                  class="mb-1.5 block text-[13px] font-semibold text-[#092F50]"
                >
                  {{ t("contact.form.service") }}
                </label>
                <div class="relative">
                  <select
                    id="service"
                    v-model="form.service"
                    :class="[
                      'w-full appearance-none rounded-[2px] border border-[#DCE8F5] bg-white px-4 py-3 pr-10 text-[14px] transition-colors focus:border-[#0869B2] focus:outline-none',
                      form.service ? 'text-[#092F50]' : 'text-[#8AA0B4]',
                    ]"
                  >
                    <option value="">{{ t("contact.form.selectOne") }}</option>
                    <option
                      v-for="cat in formattedCategories"
                      :key="cat.value"
                      :value="cat.value"
                      class="text-[#092F50]"
                    >
                      {{ cat.label }}
                    </option>
                  </select>
                  <svg
                    class="pointer-events-none absolute top-1/2 right-3.5 h-4 w-4 -translate-y-1/2 text-[#52687B]"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>

              <!-- Message -->
              <div>
                <label
                  for="message"
                  class="mb-1.5 block text-[13px] font-semibold text-[#092F50]"
                >
                  {{ t("contact.form.message") }} <span class="text-[#0869B2]">*</span>
                </label>
                <textarea
                  id="message"
                  v-model="form.message"
                  rows="5"
                  required
                  :placeholder="t('contact.form.messagePlaceholder')"
                  :class="[
                    'w-full resize-none rounded-[2px] border bg-white px-4 py-3 text-[14px] text-[#092F50] placeholder-[#8AA0B4] transition-colors focus:outline-none',
                    errors.message
                      ? 'border-red-500 focus:border-red-500'
                      : 'border-[#DCE8F5] focus:border-[#0869B2]',
                  ]"
                  @blur="validateField('message')"
                  @input="errors.message = ''"
                ></textarea>
                <p v-if="errors.message" class="mt-1.5 text-[12px] text-red-600">
                  {{ errors.message }}
                </p>
              </div>

              <!-- Submit Button -->
              <div class="pt-1">
                <button
                  type="submit"
                  :disabled="loading"
                  class="inline-flex w-full items-center justify-center gap-2 rounded-[2px] bg-[#0869B2] px-6 py-3.5 text-[14px] font-bold text-white transition-colors hover:bg-[#07528C] disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto sm:min-w-[210px]"
                >
                  <span>{{ loading ? t("contact.form.sendingBtn") : t("contact.form.sendBtn") }}</span>
                  <svg
                    v-if="!loading"
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                  </svg>
                </button>
              </div>

              <!-- Success Message -->
              <div
                v-if="submitted"
                class="rounded-[2px] border border-[#BFD9F2] bg-white px-4 py-3.5 text-[13px] font-medium text-[#092F50]"
              >
                {{ t("contact.form.successMsg") }}
              </div>

              <!-- Error Message -->
              <div
                v-if="submitError"
                class="rounded-[2px] border border-red-200 bg-red-50 px-4 py-3.5 text-[13px] font-medium text-red-700"
              >
                {{ submitError }}
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import { useServiceCategories } from "~/composables/useServiceCategories";
import { getApiBaseUrl } from "~/utils/api";

const route = useRoute();
const { t, currentLocale } = useLanguage();
const { formattedCategories } = useServiceCategories();
const { email, phone, getWhatsappUrl, headOfficeAddress, branchOfficeAddress } = useCompanySettings();

const contactEmail = computed(() => email.value || "contact@apconsulting.id");
const contactPhone = computed(() => phone.value || "+62 857 5417 2217");
const whatsappHref = computed(() => getWhatsappUrl() || "https://wa.me/6285754172217");

const form = reactive({
  fullName: "",
  email: "",
  phone: "",
  company: "",
  service: "",
  message: "",
});

onMounted(() => {
  const queryService = typeof route.query.service === "string" ? route.query.service.toLowerCase().trim() : "";
  if (queryService) {
    const matched = formattedCategories.value.find((c) =>
      c.value.toLowerCase().includes(queryService) || c.label.toLowerCase().includes(queryService)
    );
    if (matched) {
      form.service = matched.value;
    }
  }
});

const errors = reactive({
  fullName: "",
  email: "",
  message: "",
});

const loading = ref(false);
const submitted = ref(false);
const submitError = ref("");

const validateField = (field: "fullName" | "email" | "message") => {
  if (field === "fullName") {
    errors.fullName = form.fullName.trim() ? "" : t("contact.form.errName");
  }
  if (field === "email") {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!form.email.trim()) {
      errors.email = t("contact.form.errEmailEmpty");
    } else if (!emailRegex.test(form.email.trim())) {
      errors.email = t("contact.form.errEmailInvalid");
    } else {
      errors.email = "";
    }
  }
  if (field === "message") {
    if (!form.message.trim()) {
      errors.message = t("contact.form.errMsgEmpty");
    } else if (form.message.trim().length < 10) {
      errors.message = t("contact.form.errMsgShort");
    } else {
      errors.message = "";
    }
  }
};

const validateAll = () => {
  validateField("fullName");
  validateField("email");
  validateField("message");
  return !errors.fullName && !errors.email && !errors.message;
};

const config = useRuntimeConfig();

const handleSubmit = async () => {
  if (!validateAll()) return;

  loading.value = true;
  submitError.value = "";

  try {
    const apiBase = getApiBaseUrl();
    const res: any = await $fetch(`${apiBase}/contact`, {
      method: "POST",
      body: {
        full_name: form.fullName,
        email: form.email,
        phone: form.phone || null,
        company: form.company || null,
        service_interest: form.service || null,
        message: form.message,
      },
    });

    submitted.value = true;

    // Reset form fields
    form.fullName = "";
    form.email = "";
    form.phone = "";
    form.company = "";
    form.service = "";
    form.message = "";

    // Open WhatsApp link if returned from API
    if (res && res.whatsapp_url) {
      window.open(res.whatsapp_url, "_blank");
    }
  } catch (err: any) {
    submitError.value =
      err?.data?.message ||
      (currentLocale.value === "en"
        ? "Failed to send message. Please try again or reach us directly via WhatsApp."
        : "Gagal mengirim pesan. Silakan coba lagi atau hubungi kami langsung via WhatsApp.");
  } finally {
    loading.value = false;
  }
};
</script>
