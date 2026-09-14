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

async function fetchAllArticleIdentifiers(apiBase) {
  const identifiers = [];
  let page = 1;
  let hasMore = true;

  while (hasMore) {
    const response = await fetch(`${apiBase}/articles?page=${page}`);
    if (!response.ok) {
      throw new Error(`Failed to fetch articles page ${page}: ${response.status}`);
    }
    const body = await response.json();
    if (Array.isArray(body.data)) {
      identifiers.push(...body.data.map((article) => article.slug || article.id));
    }
    hasMore = Boolean(body.meta?.has_more || (body.meta?.last_page && page < body.meta.last_page));
    page += 1;
  }

  return identifiers;
}

async function main() {
  const apiBase = process.env.NUXT_PUBLIC_API_BASE || "http://localhost:8000/api";
  const siteUrl = process.env.NUXT_PUBLIC_SITE_URL || "https://pcgmitra.co.id";
  const outputDir = process.env.SITEMAP_OUTPUT_DIR || resolve(__dirname, "../.output/public");

  const staticUrls = [
    `${siteUrl}/`,
    `${siteUrl}/about`,
    `${siteUrl}/services`,
    `${siteUrl}/contact`,
    `${siteUrl}/insight`,
  ];

  let articleIdentifiers = [];
  try {
    articleIdentifiers = await fetchAllArticleIdentifiers(apiBase);
  } catch (error) {
    console.warn(`[generate-sitemap] Could not fetch articles, sitemap will only contain static routes: ${error.message}`);
  }

  const articleUrls = articleIdentifiers.map((id) => `${siteUrl}/insight/${id}`);
  const xml = buildSitemapXml([...staticUrls, ...articleUrls]);

  await writeFile(resolve(outputDir, "sitemap.xml"), xml, "utf-8");
  console.log(`[generate-sitemap] Wrote sitemap.xml with ${staticUrls.length + articleUrls.length} URLs.`);
}

const isMain = process.argv[1] === fileURLToPath(import.meta.url);
if (isMain) {
  main().catch((error) => {
    console.error("[generate-sitemap] Failed:", error);
    process.exit(1);
  });
}
