export default defineNuxtPlugin(() => {
  const router = useRouter();
  const config = useRuntimeConfig();
  const GA_MEASUREMENT_ID = (config.public.gaMeasurementId as string) || "G-58DMCKKCP2";

  router.afterEach((to) => {
    if (typeof window !== "undefined") {
      // 1. Send pageview to Google Analytics 4 (gtag)
      if (typeof (window as any).gtag === "function") {
        (window as any).gtag("config", GA_MEASUREMENT_ID, {
          page_path: to.fullPath,
          page_location: window.location.href,
        });
      }

      // 2. Push pageview to Google Tag Manager (dataLayer)
      (window as any).dataLayer = (window as any).dataLayer || [];
      (window as any).dataLayer.push({
        event: "page_view",
        page_path: to.fullPath,
        page_location: window.location.href,
      });
    }
  });
});
