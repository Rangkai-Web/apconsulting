import { defineStore } from "pinia";
import { catalogService } from "~/services/catalogService";
import type { CatalogFilters, Category, Product } from "~/types";

const defaultFilters = (): CatalogFilters => ({
  category: null,
  minPrice: null,
  maxPrice: null,
  sort: "newest",
  search: "",
  page: 1,
});

export const useCatalogStore = defineStore("catalog", {
  state: () => ({
    categories: [] as Category[],
    products: [] as Product[],
    filters: defaultFilters(),
    loading: false,
    hasMore: false,
    total: 0,
  }),

  actions: {
    async loadCategories() {
      this.categories = await catalogService.getCategories();
    },

    async applyFilters(filters: Partial<CatalogFilters>) {
      this.filters = { ...defaultFilters(), ...filters, page: 1 };
      this.loading = true;
      try {
        const result = await catalogService.getProducts(this.filters);
        this.products = result.data;
        this.hasMore = result.hasMore;
        this.total = result.total;
      } finally {
        this.loading = false;
      }
    },

    async loadMore() {
      if (!this.hasMore || this.loading) return;
      this.loading = true;
      try {
        const nextFilters = { ...this.filters, page: this.filters.page + 1 };
        const result = await catalogService.getProducts(nextFilters);
        this.filters = nextFilters;
        this.products = [...this.products, ...result.data];
        this.hasMore = result.hasMore;
      } finally {
        this.loading = false;
      }
    },
  },
});
