import { writeFile } from "node:fs/promises";
import { resolve, dirname } from "node:path";
import { fileURLToPath } from "node:url";

const __dirname = dirname(fileURLToPath(import.meta.url));

function escapeXml(value) {
  return value
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&apos;");
}

export function buildSitemapXml(urls) {
  const entries = urls
    .map((url) => `  <url><loc>${escapeXml(url)}</loc></url>`)
    .join("\n");

  return `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n${entries}\n</urlset>\n`;
}

async function fetchAllProductSlugs(apiBase) {
  const slugs = [];
  let page = 1;
  let hasMore = true;

  while (hasMore) {
    const response = await fetch(`${apiBase}/products?page=${page}`);
    if (!response.ok) {
      throw new Error(`Failed to fetch products page ${page}: ${response.status}`);
    }
    const body = await response.json();
    slugs.push(...body.data.map((product) => product.slug));
    hasMore = Boolean(body.meta?.has_more);
    page += 1;
  }

  return slugs;
}

async function main() {
  const apiBase = process.env.NUXT_PUBLIC_API_BASE || "http://localhost:8000/api";
  const siteUrl = process.env.NUXT_PUBLIC_SITE_URL || "https://bbystargo.com";
  const outputDir = process.env.SITEMAP_OUTPUT_DIR || resolve(__dirname, "../.output/public");

  const staticUrls = [`${siteUrl}/`, `${siteUrl}/katalog`];

  let productSlugs = [];
  try {
    productSlugs = await fetchAllProductSlugs(apiBase);
  } catch (error) {
    console.warn(`[generate-sitemap] Could not fetch products, sitemap will only contain static routes: ${error.message}`);
  }

  const productUrls = productSlugs.map((slug) => `${siteUrl}/produk/${slug}`);
  const xml = buildSitemapXml([...staticUrls, ...productUrls]);

  await writeFile(resolve(outputDir, "sitemap.xml"), xml, "utf-8");
  console.log(`[generate-sitemap] Wrote sitemap.xml with ${staticUrls.length + productUrls.length} URLs.`);
}

const isMain = process.argv[1] === fileURLToPath(import.meta.url);
if (isMain) {
  main().catch((error) => {
    console.error("[generate-sitemap] Failed:", error);
    process.exit(1);
  });
}
