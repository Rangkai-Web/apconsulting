export function buildShareUrl(url: string): string {
  const u = new URL(url);
  u.searchParams.set("utm_source", "share");
  u.searchParams.set("utm_medium", "social");
  u.searchParams.set("utm_campaign", "insight");
  return u.toString();
}
