import { computed } from "vue";
import { getApiBaseUrl, resolveImageUrl, safeUseFetch } from "~/utils/api";

export interface SettingsData {
  company?: { logo?: string };
  contact?: { email?: string; phone?: string; whatsapp?: string; address?: string; maps_embed_url?: string };
  social?: {
    instagram?: string;
    twitter?: string;
    facebook?: string;
    linkedin?: string;
    youtube?: string;
    tiktok?: string;
    threads?: string;
  };
}

export function useCompanySettings() {
  const apiBase = getApiBaseUrl();

  const { data: apiResponse, pending, error } = safeUseFetch<{ data: SettingsData }>(
    `${apiBase}/settings`,
    {
      key: "company-settings",
    }
  );

  const settings = computed(() => apiResponse.value?.data || {});

  const logoUrl = computed(() => resolveImageUrl(settings.value.company?.logo, "/pcg_logo.jpeg"));
  const email = computed(() => settings.value.contact?.email || "info@pcgmitra.co.id");
  const phone = computed(() => settings.value.contact?.phone || settings.value.contact?.whatsapp || "");
  const address = computed(
    () =>
      settings.value.contact?.address ||
      "Blok. D, Rukan Cordoba, Jl. Green Lake City Boulevard No.23, Kota Tangerang, Banten 15146"
  );
  const mapsEmbedUrl = computed(
    () =>
      settings.value.contact?.maps_embed_url ||
      "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.452601955025!2d106.6974864758671!3d-6.203875393783993!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f9780bd69701%3A0xa084e1c8fe29ad9!2sGreen%20Lake%20City%20Blvd%2C%20Petir%2C%20Cipondoh%2C%20Tangerang%20City%2C%20Banten%2015147!5e0!3m2!1sen!2sid!4v1710000000000!5m2!1sen!2sid"
  );

  const socials = computed(() => {
    const list = [
      { name: "LinkedIn", icon: "i-simple-icons-linkedin", href: settings.value.social?.linkedin },
      { name: "Instagram", icon: "i-simple-icons-instagram", href: settings.value.social?.instagram },
      { name: "TikTok", icon: "i-simple-icons-tiktok", href: settings.value.social?.tiktok },
      { name: "Threads", icon: "i-simple-icons-threads", href: settings.value.social?.threads },
      { name: "X", icon: "i-simple-icons-x", href: settings.value.social?.twitter },
      { name: "Facebook", icon: "i-simple-icons-facebook", href: settings.value.social?.facebook },
      { name: "YouTube", icon: "i-simple-icons-youtube", href: settings.value.social?.youtube },
    ];
    return list
      .filter(
        (item) => typeof item.href === "string" && item.href.replace(/[\u00a0\s]+/g, "").trim() !== "" && item.href !== "#"
      )
      .map((item) => ({
        ...item,
        href: /^https?:\/\//i.test(item.href!.trim()) ? item.href!.trim() : `https://${item.href!.trim()}`,
      }));
  });

  const whatsappNumber = computed(
    () => settings.value.contact?.whatsapp || settings.value.contact?.phone || "6289673803596"
  );

  const cleanWhatsappNumber = computed(() => {
    let digits = whatsappNumber.value.replace(/\D/g, "");
    if (digits.startsWith("0")) {
      digits = "62" + digits.substring(1);
    } else if (digits.startsWith("8")) {
      digits = "62" + digits;
    }
    return digits;
  });

  const getWhatsappUrl = (message?: string) => {
    const text = message || "Halo PCG, saya ingin berkonsultasi mengenai layanan Anda.";
    return `https://wa.me/${cleanWhatsappNumber.value}?text=${encodeURIComponent(text)}`;
  };

  const whatsappLink = computed(() => getWhatsappUrl());

  return {
    settings,
    logoUrl,
    email,
    phone,
    whatsappNumber,
    cleanWhatsappNumber,
    whatsappLink,
    getWhatsappUrl,
    address,
    mapsEmbedUrl,
    socials,
    pending,
    error,
  };
}

