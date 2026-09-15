import { describe, it, expect } from "vitest";
import { useServiceCategories } from "../app/composables/useServiceCategories";

describe("useServiceCategories", () => {
  it("returns default categories when initialization occurs", () => {
    const { categories, formattedCategories } = useServiceCategories();
    expect(categories.value.length).toBeGreaterThan(0);
    expect(formattedCategories.value.length).toBeGreaterThan(0);
    expect(formattedCategories.value[0]).toHaveProperty("value");
    expect(formattedCategories.value[0]).toHaveProperty("label");
  });
});
