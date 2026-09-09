import { ref, computed, watch } from "vue";
import { getApiBaseUrl, resolveImageUrl, safeUseFetch } from "~/utils/api";

export interface SettingsData {
  company?: { logo?: string };
  contact?: { email?: string; phone?: string; whatsapp?: string; address?: string; maps_embed_url?: string };
  social?: { instagram?: string; twitter?: string; facebook?: string; linkedin?: string; youtube?: string };
}

// Module-level singleton state: shared across all component invocations
let isFetching = false;
const settingsState = ref<SettingsData | null>(null);
const pendingState = ref(false);
const errorState = ref<any>(null);

export function useCompanySettings() {
  const apiBase = getApiBaseUrl();

  // Fetch only ONCE per application lifecycle
  if (!settingsState.value && !isFetching) {
    isFetching = true;
    pendingState.value = true;

    const { data: apiResponse, pending, error } = safeUseFetch<{ data: SettingsData }>(
      `${apiBase}/settings`,
      {
        key: "company-settings",
      }
    );

    if (apiResponse) {
      watch(
        apiResponse,
        (newVal) => {
          if (newVal?.data) {
            settingsState.value = newVal.data;
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

  const settings = computed(() => settingsState.value || {});

  const logoUrl = computed(() => resolveImageUrl(settings.value.company?.logo, "/pcg_logo.jpeg"));
  const email = computed(() => settings.value.contact?.email || "info@pcgmitra.co.id");
  const phone = computed(() => settings.value.contact?.phone || settings.value.contact?.whatsapp || "");
  const address = computed(() => settings.value.contact?.address || "Blk. D, Rukan Cordoba, Jl. Green Lake City Boulevard No.23, Kota Tangerang, Banten 15146");
  const mapsEmbedUrl = computed(
    () =>
      settings.value.contact?.maps_embed_url ||
      "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.452601955025!2d106.6974864758671!3d-6.203875393783993!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f9780bd69701%3A0xa084e1c8fe29ad9!2sGreen%20Lake%20City%20Blvd%2C%20Petir%2C%20Cipondoh%2C%20Tangerang%20City%2C%20Banten%2015147!5e0!3m2!1sen!2sid!4v1710000000000!5m2!1sen!2sid"
  );

  const socials = computed(() => {
    const list = [
      { name: "LinkedIn", icon: "i-simple-icons-linkedin", href: settings.value.social?.linkedin },
      { name: "Instagram", icon: "i-simple-icons-instagram", href: settings.value.social?.instagram },
      { name: "X", icon: "i-simple-icons-x", href: settings.value.social?.twitter },
      { name: "Facebook", icon: "i-simple-icons-facebook", href: settings.value.social?.facebook },
      { name: "YouTube", icon: "i-simple-icons-youtube", href: settings.value.social?.youtube },
    ];
    return list.filter(
      (item) => typeof item.href === "string" && item.href.trim() !== "" && item.href !== "#"
    );
  });

  return {
    settings,
    logoUrl,
    email,
    phone,
    address,
    mapsEmbedUrl,
    socials,
    pending: pendingState,
    error: errorState,
  };
}
