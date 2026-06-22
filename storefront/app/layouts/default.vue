<template>
  <div class="min-h-screen flex flex-col bg-white font-sans antialiased text-gray-900">
    <AppHeader />
    <main class="grow">
      <slot />
    </main>
    <AppFooter />
  </div>
</template>

<script setup lang="ts">
// Components in Nuxt are auto-imported
</script>
