import { useAuthStore } from "~/store/auth";

export default defineNuxtRouteMiddleware((to, from) => {
  const auth = useAuthStore();

  if (!auth.isLoggedIn) {
    return navigateTo({
      path: "/login",
      query: { redirect: to.fullPath },
    });
  }
});
