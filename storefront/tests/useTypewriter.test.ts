import { describe, expect, it, vi, beforeEach, afterEach } from "vitest";
import { ref, nextTick } from "vue";
import { useTypewriter } from "../app/composables/useTypewriter";

describe("useTypewriter", () => {
  beforeEach(() => {
    vi.useFakeTimers();
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it("types through phrases letter by letter", () => {
    const phrases = ["Tax", "Audit"];
    const { currentText } = useTypewriter(phrases, {
      typeSpeed: 50,
      deleteSpeed: 25,
      pauseDuration: 1000,
    });

    // Advance 50ms -> 1st letter
    vi.advanceTimersByTime(50);
    expect(currentText.value).toBe("T");

    // Advance 50ms -> 2nd letter
    vi.advanceTimersByTime(50);
    expect(currentText.value).toBe("Ta");

    // Advance 50ms -> 3rd letter
    vi.advanceTimersByTime(50);
    expect(currentText.value).toBe("Tax");

    // Advance pause duration (1000ms) -> pause completes and first character deleted
    vi.advanceTimersByTime(1000);
    expect(currentText.value).toBe("Ta");

    // Next delete step (25ms)
    vi.advanceTimersByTime(25);
    expect(currentText.value).toBe("T");

    // Next delete step (25ms)
    vi.advanceTimersByTime(25);
    expect(currentText.value).toBe("");

    // Small delay between phrases (300ms) -> triggers first character of next phrase
    vi.advanceTimersByTime(300);
    expect(currentText.value).toBe("A");

    // Next letter (50ms)
    vi.advanceTimersByTime(50);
    expect(currentText.value).toBe("Au");
  });

  it("resets when reactive phrases change", async () => {
    const phrases = ref(["Initial"]);
    const { currentText } = useTypewriter(phrases, { typeSpeed: 50 });

    vi.advanceTimersByTime(150);
    expect(currentText.value).toBe("Ini");

    // Change phrase list
    phrases.value = ["Updated"];
    await nextTick();

    vi.advanceTimersByTime(100);
    expect(currentText.value).toBe("Up");
  });
});

