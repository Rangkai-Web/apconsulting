import { describe, expect, it } from "vitest";
import { useCountUp } from "../app/composables/useCountUp";

describe("useCountUp", () => {
  it("initializes with target values in test environment", () => {
    const stats = [
      { target: 4, suffix: "" },
      { target: 20, suffix: "" },
      { target: 200, suffix: "+" },
    ];
    const { displayValues, isFinished } = useCountUp(stats);

    expect(displayValues.value[0]).toBe("4");
    expect(displayValues.value[1]).toBe("20");
    expect(displayValues.value[2]).toBe("200+");
    expect(isFinished.value).toBe(true);
  });
});

