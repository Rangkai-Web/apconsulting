<template>
  <div>
    <InsightHero />
    <InsightArticles />
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  alias: ["/insight", "/insights"],
});

useHead({
  title: "Insights & Articles",
  meta: [
    {
      name: "description",
      content:
        "Stay informed with PCG's latest tax regulations, accounting best practices, and strategic financial insights.",
    },
  ],
});
</script>
