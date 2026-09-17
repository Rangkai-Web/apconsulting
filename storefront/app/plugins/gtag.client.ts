export default defineNuxtPlugin(() => {
  const router = useRouter();
  const GA_MEASUREMENT_ID = "G-SH1GS7FRBG";

  router.afterEach((to) => {
    if (typeof window !== "undefined" && typeof (window as any).gtag === "function") {
      (window as any).gtag("config", GA_MEASUREMENT_ID, {
        page_path: to.fullPath,
        page_location: window.location.href,
      });
    }
  });
});
