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
    const raw = settings.value.contact?.email?.trim();
    if (!raw || raw.includes("pcg") || raw.includes("example.com")) {
      return "contact@apconsulting.id";
    }
    return raw;
  });

  const phone = computed(() => {
    const raw = (settings.value.contact?.phone || settings.value.contact?.whatsapp)?.trim();
    if (!raw || raw.includes("pcg") || raw === "+62 21 1234 5678") {
      return "+62 857 5417 2217";
    }
    return raw;
  });

  const headOfficeAddress = computed(() => {
    const raw = settings.value.contact?.address?.trim();
    if (!raw || raw.includes("Sudirman No. 123")) {
      return "SUN Plaza, Jl. Meruya Ilir Raya No. 20, Jakarta Barat";
    }
    return raw;
  });

  const branchOfficeAddress = computed(() => {
    const raw = settings.value.contact?.branch_address?.trim();
    return raw || "Ruko Alexandrite 3 No. 23, Jl. Boulevard Gading Serpong, Tangerang";
  });

  const address = computed(() => headOfficeAddress.value);

  const mapsEmbedUrl = computed(() => {
    const raw = settings.value.contact?.maps_embed_url?.trim();
    if (!raw || raw.includes("123456789") || raw.includes("Sudirman")) {
      return "https://maps.google.com/maps?q=SUN+Plaza+Jl+Meruya+Ilir+Raya+No+20+Jakarta+Barat&output=embed";
    }
    return raw;
  });

  const branchMapsEmbedUrl = computed(() => {
    const raw = settings.value.contact?.branch_maps_embed_url?.trim();
    return raw || "https://maps.google.com/maps?q=Ruko+Alexandrite+3+No+23+Jl+Boulevard+Gading+Serpong+Tangerang&output=embed";
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
    if (!raw || raw === "6281234567890" || raw.includes("89673803596")) {
      return "6285754172217";
    }
    return raw;
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
    mapsEmbedUrl,
    branchMapsEmbedUrl,
    socials,
    pending,
    error,
  };
}
