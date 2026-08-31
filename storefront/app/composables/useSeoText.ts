function decodeHtmlEntities(text: string): string {
  const el = document.createElement("textarea");
  el.innerHTML = text;
  return el.value;
}

// JSON.stringify output can contain "</script>", or the line/paragraph
// separator code points (0x2028/0x2029) that break JS string literals in
// some engines - unsafe to drop straight into a <script> tag's innerHTML.
const LINE_SEPARATOR = String.fromCharCode(0x2028);
const PARAGRAPH_SEPARATOR = String.fromCharCode(0x2029);

function toSafeJsonLd(value: unknown): string {
  return JSON.stringify(value)
    .split("<").join("\\u003c")
    .split(">").join("\\u003e")
    .split("&").join("\\u0026")
    .split(LINE_SEPARATOR).join("\\u2028")
    .split(PARAGRAPH_SEPARATOR).join("\\u2029");
}

export function useSeoText() {
  function toPlainText(html: string, maxLength = 155): string {
    const stripped = html.replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();
    const text = decodeHtmlEntities(stripped);

    if (text.length <= maxLength) return text;
    return `${text.slice(0, maxLength - 1).trimEnd()}…`;
  }

  return { toPlainText, toSafeJsonLd };
}
