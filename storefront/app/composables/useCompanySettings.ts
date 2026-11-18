import { computed } from "vue";
import { getApiBaseUrl, resolveImageUrl, safeUseFetch } from "~/utils/api";

export interface SettingsData {
  company?: { logo?: string };
  contact?: {
    email?: string;
    phone?: string;
    whatsapp?: string;
    address?: string;
    branch_address?: string;
    maps_embed_url?: string;
    branch_maps_embed_url?: string;
  };
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

  const logoUrl = computed(() =>
    resolveImageUrl(settings.value.company?.logo, "/images/redesign/logo.webp")
  );

  // Return real settings with sensible AP Consulting defaults
  const email = computed(() => {
    return settings.value.contact?.email?.trim() || "contact@apconsulting.id";
  });

  const phone = computed(() => {
    const raw = (settings.value.contact?.phone || settings.value.contact?.whatsapp)?.trim();
    return raw || "+62 857 5417 2217";
  });

  const headOfficeAddress = computed(() => {
    const raw = settings.value.contact?.address?.trim();
    return raw || "SUN Plaza, Jl. Meruya Ilir Raya No. 20, Jakarta Barat";
  });

  const branchOfficeAddress = computed(() => {
    return settings.value.contact?.branch_address?.trim() || "";
  });

  const hasBranchOffice = computed(() => {
    return Boolean(branchOfficeAddress.value);
  });

  const address = computed(() => headOfficeAddress.value);

  const mapsEmbedUrl = computed(() => {
    const raw = settings.value.contact?.maps_embed_url?.trim();
    if (raw && (raw.startsWith("https://") || raw.startsWith("http://"))) {
      return raw;
    }
    const query = encodeURIComponent(headOfficeAddress.value || "SUN Plaza, Jl. Meruya Ilir Raya No. 20, Jakarta Barat");
    return `https://maps.google.com/maps?q=${query}&output=embed`;
  });

  const branchMapsEmbedUrl = computed(() => {
    const raw = settings.value.contact?.branch_maps_embed_url?.trim();
    if (raw && (raw.startsWith("https://") || raw.startsWith("http://"))) {
      return raw;
    }
    if (branchOfficeAddress.value) {
      return `https://maps.google.com/maps?q=${encodeURIComponent(branchOfficeAddress.value)}&output=embed`;
    }
    return "";
  });

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
        (item) =>
          typeof item.href === "string" &&
          item.href.replace(/[\u00a0\s]+/g, "").trim() !== "" &&
          item.href !== "#"
      )
      .map((item) => ({
        ...item,
        href: /^https?:\/\//i.test(item.href!.trim())
          ? item.href!.trim()
          : `https://${item.href!.trim()}`,
      }));
  });

  const whatsappNumber = computed(() => {
    const raw = (settings.value.contact?.whatsapp || settings.value.contact?.phone)?.trim();
    return raw || "6285754172217";
  });

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
    const text =
      message || "Halo AP Consulting, saya ingin berkonsultasi mengenai layanan Anda.";
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
    headOfficeAddress,
    branchOfficeAddress,
    hasBranchOffice,
    mapsEmbedUrl,
    branchMapsEmbedUrl,
    socials,
    pending,
    error,
  };
}
