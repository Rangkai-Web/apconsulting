export function buildShareUrl(url: string): string {
  if (!url) return "";
  try {
    const base = typeof window !== "undefined" ? window.location.origin : "https://apconsulting.id";
    const u = new URL(url, base);
    u.searchParams.set("utm_source", "share");
    u.searchParams.set("utm_medium", "social");
    u.searchParams.set("utm_campaign", "insight");
    return u.toString();
  } catch {
    return url;
  }
}

