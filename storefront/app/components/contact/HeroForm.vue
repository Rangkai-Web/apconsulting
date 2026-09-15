<template>
  <section class="px-4 py-12 md:px-8 md:py-20 bg-white">
    <div class="mx-auto max-w-7xl">
      <!-- Header Bar: Left Title & Right Description -->
      <div
        class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 md:pb-12"
      >
        <div>
          <p
            class="text-xs md:text-sm font-semibold uppercase tracking-wide text-neutral-400"
          >
            {{ t("contact.hero.tag") }}
          </p>
          <h1 class="mt-2 text-3xl sm:text-4xl md:text-[44px] leading-tight">
            <span class="font-sans font-light text-[#083653]"
              >{{ t("contact.hero.titleLight") }}
            </span>
            <span class="font-sans font-semibold italic text-[#083653]"
              >{{ t("contact.hero.titleItalic") }}</span
            >
          </h1>
        </div>

        <div class="text-left md:text-right max-w-md">
          <p class="text-xs md:text-sm font-semibold text-[#083653]">
            {{ t("contact.hero.headerSubtitle") }}
          </p>
          <p
            class="mt-1 text-xs md:text-sm text-neutral-500 font-normal leading-relaxed"
          >
            {{ t("contact.hero.headerDesc") }}
          </p>
        </div>
      </div>

      <!-- Main 2-Column Section -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
        <!-- Left Column: Image Card with Overlay Text -->
        <div
          class="lg:col-span-5 relative overflow-hidden min-h-[420px] md:min-h-[500px] border border-[#0000001F]"
        >
          <picture class="absolute inset-0 size-full pointer-events-none">
            <source
              media="(min-width: 768px)"
              srcset="/images/contact/HeroContent_Desktop.webp"
            />
            <img
              src="/images/contact/HeroContent_Mobile.webp"
              :alt="t('contact.hero.cardTitle')"
              fetchpriority="high"
              decoding="async"
              width="600"
              height="500"
              class="size-full object-cover"
            />
          </picture>

          <!-- Dark Gradient Overlay & Text at Bottom -->
          <div
            class="absolute inset-0 bg-gradient-to-t from-[#083653]/95 via-[#083653]/40 to-transparent flex flex-col justify-end p-8 md:p-10 pointer-events-none"
          >
            <h2 class="text-2xl md:text-3xl font-bold text-white leading-tight">
              {{ t("contact.hero.cardTitle") }}
            </h2>
            <p
              class="mt-3 text-xs md:text-sm text-white/90 font-normal leading-relaxed max-w-sm"
            >
              {{ t("contact.hero.cardDesc") }}
            </p>
          </div>
        </div>

        <!-- Right Column: Form Container Card -->
        <div
          class="lg:col-span-7 bg-[#F8FAFC] border border-[#0000001F] p-6 sm:p-8 md:p-10 flex flex-col justify-between"
        >
          <!-- Success Alert -->
          <div
            v-if="submitted"
            class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center justify-between"
          >
            <div class="flex items-center gap-2">
              <UIcon
                name="i-heroicons-check-circle"
                class="size-5 text-emerald-600 shrink-0"
              />
              <span>{{ t("contact.form.successMsg") }}</span>
            </div>
            <button
              type="button"
              class="text-emerald-700 hover:text-emerald-900 font-bold ml-4"
              @click="submitted = false"
            >
              ×
            </button>
          </div>

          <form class="space-y-6" @submit.prevent="handleSubmit">
            <!-- Row 1: Full Name & Email Address -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <!-- Full Name -->
              <div>
                <label
                  for="fullName"
                  class="block text-xs md:text-sm font-semibold text-[#083653]"
                >
                  {{ t("contact.form.fullName") }}
                </label>
                <input
                  id="fullName"
                  v-model="form.fullName"
                  type="text"
                  :placeholder="t('contact.form.fullNamePlaceholder')"
                  class="mt-2 w-full px-4 py-3 text-xs md:text-sm border bg-white text-neutral-800 rounded-[6px] focus:outline-none focus:ring-1 focus:ring-[#083653] transition-colors"
                  :class="
                    errors.fullName ? 'border-rose-500' : 'border-[#DFE7ED]'
                  "
                  @blur="validateField('fullName')"
                />
                <p v-if="errors.fullName" class="mt-1 text-xs text-rose-500">
                  {{ errors.fullName }}
                </p>
              </div>

              <!-- Email Address -->
              <div>
                <label
                  for="email"
                  class="block text-xs md:text-sm font-semibold text-[#083653]"
                >
                  {{ t("contact.form.email") }}
                </label>
                <div class="relative mt-2">
                  <UIcon
                    name="i-heroicons-envelope"
                    class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-neutral-400 pointer-events-none"
                  />
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    :placeholder="t('contact.form.emailPlaceholder')"
                    class="w-full pl-10 pr-4 py-3 text-xs md:text-sm border bg-white text-neutral-800 rounded-[6px] focus:outline-none focus:ring-1 focus:ring-[#083653] transition-colors"
                    :class="
                      errors.email ? 'border-rose-500' : 'border-[#DFE7ED]'
                    "
                    @blur="validateField('email')"
                  />
                </div>
                <p v-if="errors.email" class="mt-1 text-xs text-rose-500">
                  {{ errors.email }}
                </p>
              </div>
            </div>

            <!-- Row 2: Service of interest & Phone number -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <!-- Service of interest -->
              <div>
                <label
                  for="service"
                  class="block text-xs md:text-sm font-semibold text-[#083653]"
                >
                  {{ t("contact.form.service") }}
                </label>
                <select
                  id="service"
                  v-model="form.service"
                  class="mt-2 w-full px-4 py-3 text-xs md:text-sm border border-[#DFE7ED] bg-white text-neutral-700 rounded-[6px] focus:outline-none focus:ring-1 focus:ring-[#083653] transition-colors appearance-none cursor-pointer"
                >
                  <option value="" disabled selected>
                    {{ t("contact.form.selectOne") }}
                  </option>
                  <option
                    v-for="item in formattedCategories"
                    :key="item.value"
                    :value="item.value"
                  >
                    {{ item.label }}
                  </option>
                </select>
              </div>

              <!-- Phone number (optional) -->
              <div>
                <label
                  for="phone"
                  class="block text-xs md:text-sm font-semibold text-[#083653]"
                >
                  {{ t("contact.form.phone") }}
                </label>
                <div class="relative mt-2">
                  <UIcon
                    name="i-heroicons-phone"
                    class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-neutral-400 pointer-events-none"
                  />
                  <input
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    placeholder="+62"
                    class="w-full pl-10 pr-4 py-3 text-xs md:text-sm border border-[#DFE7ED] bg-white text-neutral-800 rounded-[6px] focus:outline-none focus:ring-1 focus:ring-[#083653] transition-colors"
                  />
                </div>
              </div>
            </div>

            <!-- Row 3: Message -->
            <div>
              <label
                for="message"
                class="block text-xs md:text-sm font-semibold text-[#083653]"
              >
                {{ t("contact.form.message") }}
              </label>
              <textarea
                id="message"
                v-model="form.message"
                rows="4"
                :placeholder="t('contact.form.messagePlaceholder')"
                class="mt-2 w-full px-4 py-3 text-xs md:text-sm border bg-white text-neutral-800 rounded-[6px] focus:outline-none focus:ring-1 focus:ring-[#083653] transition-colors resize-none"
                :class="errors.message ? 'border-rose-500' : 'border-[#DFE7ED]'"
                @blur="validateField('message')"
              ></textarea>
              <p v-if="errors.message" class="mt-1 text-xs text-rose-500">
                {{ errors.message }}
              </p>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
              <button
                type="submit"
                class="px-8 py-3.5 bg-[#083653] text-white hover:bg-[#083653]/90 font-semibold text-xs md:text-sm rounded-none tracking-wider transition-colors inline-flex items-center justify-center gap-2"
                :disabled="loading"
              >
                <UIcon
                  v-if="loading"
                  name="i-heroicons-arrow-path"
                  class="size-4 animate-spin"
                />
                <span>{{
                  loading
                    ? t("contact.form.sendingBtn")
                    : t("contact.form.sendBtn")
                }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, reactive } from "vue";
import { useServiceCategories } from "~/composables/useServiceCategories";

const { t } = useLanguage();
const { formattedCategories } = useServiceCategories();

const form = reactive({
  fullName: "",
  email: "",
  phone: "",
  service: "",
  message: "",
});

const errors = reactive({
  fullName: "",
  email: "",
  message: "",
});

const loading = ref(false);
const submitted = ref(false);

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

  try {
    const res: any = await $fetch(`${config.public.apiBase}/contact`, {
      method: "POST",
      body: {
        full_name: form.fullName,
        email: form.email,
        phone: form.phone || null,
        service_interest: form.service || null,
        message: form.message,
      },
    });

    submitted.value = true;

    // Reset form fields
    form.fullName = "";
    form.email = "";
    form.phone = "";
    form.service = "";
    form.message = "";

    // Open WhatsApp link if returned from API
    if (res && res.whatsapp_url) {
      window.open(res.whatsapp_url, "_blank");
    }
  } catch (err) {
    // Form error handled gracefully
  } finally {
    loading.value = false;
  }
};
</script>
